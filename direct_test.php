<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

$app = require 'bootstrap/app.php';
$app->handleRequest(Request::create('/admin', 'GET'));
// Actually, let's use the kernel
$kernel = $app->make(Kernel::class);
$request = Request::create('/admin', 'GET');
$response = $kernel->handle($request);
echo 'Status: '.$response->getStatusCode()."\n";
echo "Content (first 200 chars):\n";
echo substr($response->getContent(), 0, 200);
