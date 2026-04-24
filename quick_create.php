<?php

// Quick test to create order
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Request::create('/test-create', 'GET')
);
echo "Test complete\n";

// Or simpler: use tinker alternative
use App\Models\Order;
use Illuminate\Http\Request;

$countBefore = Order::count();
echo "Orders before: $countBefore\n";

$order = Order::create([
    'order_number' => 'TEST-002',
    'customer_name' => 'Test Customer 2',
    'order_items' => 'Test items 2',
    'status' => 'waiting',
    'order_time' => now()->format('H:i'),
    'estimated_duration' => 35,
]);

echo 'Created: '.$order->order_number."\n";
echo 'Orders after: '.Order::count()."\n";
