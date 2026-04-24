<?php

$base = 'http://127.0.0.1:8000';

// Test 1: GET admin page
echo "=== TEST 1: Admin Page ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$base/admin");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Status: $code\n";
echo "Has 'Dashboard Admin': ".(strpos($response, 'Dashboard Admin') !== false ? 'YES' : 'NO')."\n";
echo "Has 'Estimasi' column: ".(strpos($response, 'Estimasi') !== false ? 'YES' : 'NO')."\n";
echo "Has 'Tracking' column: ".(strpos($response, 'Tracking') !== false ? 'YES' : 'NO')."\n";
echo "Has 'Edit' button: ".(strpos($response, 'Edit') !== false ? 'YES' : 'NO')."\n\n";

// Test 2: POST new order
echo "=== TEST 2: Create Order ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$base/admin/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'customer_name' => 'Budi Santoso',
    'order_items' => '3x Nasi Goreng, 2x Es Teh Manis',
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Status: $code\n";
$data = json_decode($response, true);
if ($data && isset($data['success'])) {
    echo 'Order created: '.$data['order']['order_number']."\n";
    echo 'Estimated duration: '.($data['order']['estimated_duration'] ?? 'N/A')." minutes\n";
    echo 'Tracking URL: '.($data['tracking_url'] ?? 'N/A')."\n\n";
} else {
    echo "Failed: $response\n\n";
}

// Test 3: GET orders JSON
echo "=== TEST 3: Get Orders ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$base/admin/orders/json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Status: $code\n";
$data = json_decode($response, true);
if ($data && isset($data['orders']['data'])) {
    $count = count($data['orders']['data']);
    echo "Total orders: $count\n";
    if ($count > 0) {
        $first = $data['orders']['data'][0];
        echo "First order ID: {$first['order_number']}\n";
        echo "First order customer: {$first['customer_name']}\n";
        echo 'First order estimated: '.($first['estimated_duration'] ?? 'N/A')." minutes\n";
    }
} else {
    echo "No orders or error\n";
}
echo "\n";

// Test 4: GET tracking page
echo "=== TEST 4: Tracking Page ===\n";
if (isset($data['orders']['data'][0])) {
    $orderNum = $data['orders']['data'][0]['order_number'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$base/tracking/$orderNum");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "Status: $code\n";
    echo "Page contains 'Status Progress': ".(strpos($response, 'Status Progress') !== false ? 'YES' : 'NO')."\n";
    echo 'Page contains order number: '.(strpos($response, $orderNum) !== false ? 'YES' : 'NO')."\n";
    echo "Tracking URL: $base/tracking/$orderNum\n";
}

echo "\n=== ALL TESTS COMPLETE ===\n";
