<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
Facade::setFacadeApplication($app);

// Simulate request
$request = Request::create('/admin/orders', 'POST', [], [], [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode(['customer_name' => 'Test CSRF', 'order_items' => 'Test items'])
);
$request->headers->set('X-CSRF-TOKEN', config('app.key')); // bypass CSRF for test

$response = $kernel->handle($request);
echo 'Status: '.$response->getStatusCode()."\n";
echo 'Content: '.substr($response->getContent(), 0, 300)."\n";
$kernel->terminate($request, $response);
