<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\Order;
use Illuminate\Support\Facades\DB;

$order = Order::where('status', 'completed')->first();
if ($order) {
    echo "Order: {$order->order_number}\n";
    echo "Started: {$order->started_at}\n";
    echo "Completed: {$order->completed_at}\n";
    echo "Duration: {$order->duration_minutes}\n";
    echo 'Is Late: '.($order->is_late ? 'true' : 'false')."\n";
} else {
    echo "No completed orders\n";
}

// Also count all orders by status
echo "\nAll orders by status:\n";
$counts = Order::select('status', DB::raw('count(*) as cnt'))->groupBy('status')->get();
foreach ($counts as $c) {
    echo "  {$c->status}: {$c->cnt}\n";
}
