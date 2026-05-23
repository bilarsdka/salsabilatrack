<?php

$base = 'http://127.0.0.1:8015';
$cookieFile = 'test_cookies.txt';

// Get page to obtain CSRF token
$ch = curl_init($base.'/admin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$page = curl_exec($ch);
if (curl_errno($ch)) {
    exit('Error: '.curl_error($ch));
}
    preg_match('/name="csrf-token" content="([^"]+)"/', $page, $m);
    $token = $m[1] ?? '';
    echo 'Token: '.substr($token, 0, 20)."...\n";
    curl_close($ch);

    // Create order
$ch2 = curl_init($base.'/admin/orders');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode([
    'customer_name' => 'CurlTest',
    'order_items' => 'Mie Ayam, Es Teh',
]));
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: '.$token,
]);
curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookieFile);
$response2 = curl_exec($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
echo "POST Status: $code2\n";
echo 'Response: '.substr($response2, 0, 300)."\n";

// Get orders
$ch3 = curl_init($base.'/admin/orders/json');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_COOKIEFILE, $cookieFile);
$response3 = curl_exec($ch3);
$code3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
$data = json_decode($response3, true);
echo "GET Status: $code3\n";
echo 'Orders count: '.count($data['orders']['data'])."\n";
if (isset($data['orders']['data'])) {
    foreach ($data['orders']['data'] as $order) {
        echo " - {$order['order_number']} | {$order['customer_name']} | Est: {$order['estimated_duration']}min\n";
    }
}
unlink($cookieFile);
