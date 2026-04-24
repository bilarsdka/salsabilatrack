<?php

$base = 'http://127.0.0.1:8030';
$cookieFile = __DIR__.'/flow_cookies.txt';

function getToken($base, $cookieFile)
{
    $ch = curl_init("$base/admin");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    $page = curl_exec($ch);
    curl_close($ch);
    preg_match('/name="csrf-token" content="([^"]+)"/', $page, $m);

    return $m[1] ?? '';
}

function jsonReq($url, $method = 'GET', $token = null, $cookieFile = null, $body = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
    }
    if ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POST, true);
    }
    if ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    if ($body) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    if ($token) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-CSRF-TOKEN: '.$token]);
    }
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$code, json_decode($res, true)];
}

echo "=== SALSABILA TRACK - FULL FLOW TEST ===\n\n";

$token = getToken($base, $cookieFile);
echo 'Got CSRF token: '.substr($token, 0, 10)."...\n";

// 1. Create order
[$code, $data] = jsonReq("$base/admin/orders", 'POST', $token, $cookieFile, json_encode([
    'customer_name' => 'Flow Test',
    'order_items' => 'Nasi Goreng, Es Teh',
]));
echo "Create order: HTTP $code, success: ".($data['success'] ?? 'no')."\n";
$orderNum = $data['order']['order_number'] ?? null;
if (! $orderNum) {
    echo "Failed to create order\n";
    exit;
}
echo "Order number: $orderNum\n";
echo "Estimated duration: {$data['order']['estimated_duration']} min\n\n";

// 2. Get orders (check initial)
[$code, $data] = jsonReq("$base/admin/orders/json", 'GET', null, $cookieFile);
$orders = $data['orders']['data'];
$myOrder = null;
foreach ($orders as $o) {
    if ($o['order_number'] == $orderNum) {
        $myOrder = $o;
        break;
    }
}
echo "After create: status={$myOrder['status']}, duration={$myOrder['duration_minutes']}, est={$myOrder['estimated_duration']}\n";

// 3. Set to processing
[$code, $data] = jsonReq("$base/admin/orders/$orderNum/status/processing", 'POST', $token, $cookieFile);
echo "Set processing: HTTP $code\n";
[$code, $data] = jsonReq("$base/admin/orders/json", 'GET', null, $cookieFile);
$myOrder = collect($data['orders']['data'])->firstWhere('order_number', $orderNum);
echo "After processing: status={$myOrder['status']}, started_at=".($myOrder['started_at'] ?? 'null')."\n";

// 4. Set to shipped
sleep(1); // ensure time passes
[$code, $data] = jsonReq("$base/admin/orders/$orderNum/status/shipped", 'POST', $token, $cookieFile);
echo "Set shipped: HTTP $code\n";
[$code, $data] = jsonReq("$base/admin/orders/json", 'GET', null, $cookieFile);
$myOrder = collect($data['orders']['data'])->firstWhere('order_number', $orderNum);
echo "After shipped: status={$myOrder['status']}\n";

// 5. Set to completed
sleep(1);
[$code, $data] = jsonReq("$base/admin/orders/$orderNum/status/completed", 'POST', $token, $cookieFile);
echo "Set completed: HTTP $code\n";
$respOrder = $data['order'] ?? null;
if ($respOrder) {
    echo "Response order: duration={$respOrder['duration_minutes']}, is_late={$respOrder['is_late']}\n";
}
[$code, $data] = jsonReq("$base/admin/orders/json", 'GET', null, $cookieFile);
$myOrder = collect($data['orders']['data'])->firstWhere('order_number', $orderNum);
if ($myOrder) {
    echo "Final table: status={$myOrder['status']}, duration={$myOrder['duration_minutes']}, is_late=".($myOrder['is_late'] ? 'true' : 'false')."\n";
} else {
    echo "Order not found in table\n";
}

unlink($cookieFile);
echo "\nDone.\n";
