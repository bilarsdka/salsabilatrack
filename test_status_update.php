<?php

$base = 'http://127.0.0.1:8025';
$cookieFile = 'D:\salsabilatrack\test_cookies.txt';

// 1. GET admin page for CSRF token
$ch = curl_init("$base/admin");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$page = curl_exec($ch);
preg_match('/name="csrf-token" content="([^"]+)"/', $page, $m);
$token = $m[1] ?? '';
echo 'Token: '.substr($token, 0, 20)."...\n";

// 2. Check orders before
$ch2 = curl_init("$base/admin/orders/json");
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookieFile);
$before = curl_exec($ch2);
$beforeData = json_decode($before, true);
$countBefore = count($beforeData['orders']['data']);
echo "Orders before: $countBefore\n";
if ($countBefore > 0) {
    $testOrder = $beforeData['orders']['data'][0];
    echo "Test order: {$testOrder['order_number']} status: {$testOrder['status']} duration: {$testOrder['duration_minutes']}\n";
}

// 3. Try to update status to completed (assuming first order is waiting or processing)
if ($countBefore > 0) {
    $orderNum = $testOrder['order_number'];
    $ch3 = curl_init("$base/admin/orders/$orderNum/status/completed");
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch3, CURLOPT_POST, true);
    curl_setopt($ch3, CURLOPT_HTTPHEADER, ['X-CSRF-TOKEN: '.$token]);
    curl_setopt($ch3, CURLOPT_COOKIEFILE, $cookieFile);
    $upd = curl_exec($ch3);
    $code3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
    echo "\nUpdate status to completed: HTTP $code3\n";
    $updData = json_decode($upd, true);
    if ($updData) {
        echo 'Success: '.($updData['success'] ?? 'no')."\n";
        if (isset($updData['order']['duration_minutes'])) {
            echo "Duration in response: {$updData['order']['duration_minutes']} min\n";
        } else {
            echo "Duration NOT in response\n";
        }
    }

    // 4. Check orders after
    $ch4 = curl_init("$base/admin/orders/json");
    curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch4, CURLOPT_COOKIEFILE, $cookieFile);
    $after = curl_exec($ch4);
    $afterData = json_decode($after, true);
    $afterOrder = $afterData['orders']['data'][0] ?? null;
    if ($afterOrder) {
        echo "\nAfter update:\n";
        echo "Status: {$afterOrder['status']}\n";
        echo "Duration: {$afterOrder['duration_minutes']}\n";
        echo "Is Late: {$afterOrder['is_late']}\n";
    }
}

unlink($cookieFile);
