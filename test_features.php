<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/admin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($response && $httpCode == 200) {
    echo "SUCCESS! Admin page loaded.\n";
    echo "Page contains 'Dashboard Admin': ".(strpos($response, 'Dashboard Admin') !== false ? 'YES' : 'NO')."\n";
    echo "Page contains 'Tambah Pesanan Baru': ".(strpos($response, 'Tambah Pesanan Baru') !== false ? 'YES' : 'NO')."\n";
    echo "Page contains 'Daftar Pesanan': ".(strpos($response, 'Daftar Pesanan') !== false ? 'YES' : 'NO')."\n";
    echo "Page contains 'Estimasi': ".(strpos($response, 'Estimasi') !== false ? 'YES' : 'NO')."\n";
    echo "Page contains 'Tracking': ".(strpos($response, 'Tracking') !== false ? 'YES' : 'NO')."\n";
    echo "Page contains 'Edit': ".(strpos($response, 'Edit') !== false ? 'YES' : 'NO')."\n";
} else {
    echo "FAILED or no response\n";
    if ($response) {
        echo "First 200 chars:\n";
        echo substr($response, 0, 200);
    }
}
