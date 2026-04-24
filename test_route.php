<?php

// test_route.php
$ch = curl_init('http://127.0.0.1:8001/admin');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

echo "HTTP Code: $code\n";
echo "Headers:\n$headers\n";
echo "Body (first 200 chars):\n".substr($body, 0, 200)."\n";
