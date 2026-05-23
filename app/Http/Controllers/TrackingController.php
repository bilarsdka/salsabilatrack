<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return view('tracking.not-found', compact('orderNumber'));
        }

        return view('tracking.index', compact('order'));
    }

    /**
     * AJAX endpoint: return current remaining estimate + status for the tracking page.
     * Called by JS every 30 s so the countdown stays accurate.
     */
    public function dynamic($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (! $order) {
            return response()->json([
                'remaining' => 0,
                'status'    => 'not_found',
            ]);
        }

        $remaining = $order->status === 'completed'
            ? 'Selesai'
            : (int) $order->dynamic_estimate;

        return response()->json([
            'remaining' => $remaining,
            'status'    => $order->status,
        ]);
    }
}
