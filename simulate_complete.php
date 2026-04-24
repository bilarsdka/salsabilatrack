<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Request::create('/test', 'GET')
);

use App\Models\Order;
use Illuminate\Http\Request;

// Find an order that is not completed
$order = Order::where('order_number', 'ORD-0-1')->first();
if (! $order) {
    echo "Order not found\n";
    exit;
}

echo "Before: Status={$order->status}, started_at={$order->started_at}, duration={$order->duration_minutes}\n";

// Simulate updateStatus to 'completed'
$status = 'completed';
$order->status = $status;

if ($status === 'processing' && ! $order->started_at) {
    $order->started_at = now();
} elseif ($status === 'completed') {
    $order->completed_at = now();

    // Ensure we have a start time
    if (! $order->started_at) {
        $order->started_at = $order->created_at;
    }

    $startTime = $order->started_at;
    $duration = now()->diffInMinutes($startTime);
    if ($duration < 1) {
        $duration = 1;
    }

    $order->duration_minutes = $duration;
    $order->is_late = $duration > 60;
}

$order->save();
$order->refresh();

echo "After: Status={$order->status}, started_at={$order->started_at}, duration={$order->duration_minutes}, is_late=".($order->is_late ? 'true' : 'false')."\n";
