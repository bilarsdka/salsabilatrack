<?php

// Simulate browser: get CSRF token, then create order
$base = 'http://127.0.0.1:8012';

// 1. GET home page to get CSRF token and session cookie
$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, "$base/");
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_COOKIEJAR, 'cookie.txt');
$response1 = curl_exec($ch1);
preg_match('/name="csrf-token" content="([^"]+)"/', $response1, $m);
$token = $m[1] ?? '';
echo 'CSRF Token: '.substr($token, 0, 20)."...\n";

// 2. POST create order with token and cookies
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, "$base/admin/orders");
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode([
    'customer_name' => 'Browser Test',
    'order_items' => 'Mie Ayam, Es Teh',
]));
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: '.$token,
]);
curl_setopt($ch2, CURLOPT_COOKIEFILE, 'cookie.txt');
$response2 = curl_exec($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
echo "POST Status: $code2\n";
echo 'Response: '.substr($response2, 0, 200)."\n";

// 3. GET orders JSON
$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, "$base/admin/orders/json");
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_COOKIEFILE, 'cookie.txt');
$response3 = curl_exec($ch3);
$code3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
$data = json_decode($response3, true);
echo "GET JSON Status: $code3\n";
echo 'Orders count: '.count($data['orders']['data'])."\n";
if (count($data['orders']['data']) > 0) {
    $last = end($data['orders']['data']);
    echo "Last order: {$last['order_number']} - {$last['customer_name']} - Est: {$last['estimated_duration']}min\n";
}

// Cleanup
unlink('cookie.txt');
