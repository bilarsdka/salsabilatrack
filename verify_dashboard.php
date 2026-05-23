<?php
// verify_dashboard.php — run with: php verify_dashboard.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(\Illuminate\Http\Request::create('/', 'GET'));

use App\Models\Order;
use Illuminate\Support\Facades\Schema;

echo "=== KOLOM TABEL ORDERS ===\n";
foreach (Schema::getColumnListing('orders') as $col) {
    echo "  $col\n";
}

echo "\n=== SEMUA ORDER ===\n";
foreach (Order::orderBy('id')->get() as $o) {
    printf(
        "%-10s  status=%-12s  prediction=%-6s  est=%4dmin  is_late=%d\n",
        $o->order_number,
        $o->status,
        $o->prediction ?? 'NULL',
        $o->estimated_duration,
        $o->is_late ? 1 : 0
    );
}

echo "\n=== PREDIKSI DAN ESTIMASI ===\n";
foreach (Order::whereNotNull('prediction')->get() as $o) {
    printf(
        "%-10s  prediction=%-6s  dur=%4dmin  is_late=%d  est=%4dmin\n",
        $o->order_number,
        $o->prediction,
        $o->duration_minutes ?? 0,
        $o->is_late ? 1 : 0,
        $o->estimated_duration
    );
}

echo "DONE\n";
