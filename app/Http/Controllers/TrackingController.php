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
}
