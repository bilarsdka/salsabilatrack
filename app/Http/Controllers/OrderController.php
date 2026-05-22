<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::recent()->get();

        return view('admin.dashboard', compact('orders'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'order_items' => 'required|string|max:500',
            ]);

            // Generate order number: ORD-001, ORD-002, etc.
            $lastOrder = Order::orderByRaw('CAST(SUBSTRING(order_number, 5) AS UNSIGNED) DESC')->first();
            $nextNumber = $lastOrder ? intval(substr($lastOrder->order_number, 4)) + 1 : 1;
            $orderNumber = 'ORD-'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Format current time
            $now = now();
            $orderTime = $now->format('H:i');

            // AI-based duration estimation (with fallback)
            try {
                $estimatedDuration = Order::estimateDuration();
            } catch (\Exception $e) {
                \Log::error('Estimation error: '.$e->getMessage());
                $estimatedDuration = 45;
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'order_items' => $request->order_items,
                'status' => 'waiting',
                'order_time' => $orderTime,
                'estimated_duration' => $estimatedDuration,
            ]);

            try {
                $jumlahAntrian = Order::where('status', '!=', 'completed')->count();
                $pythonPath = 'C:\\Program Files\\Python312\\python.exe';
                $scriptPath = base_path('ml/predict.py');
                
                $itemsArg = escapeshellarg($request->order_items);
                $command = "\"$pythonPath\" \"$scriptPath\" $orderTime $jumlahAntrian $estimatedDuration $itemsArg full 2>&1";
                $rawOutput = trim(shell_exec($command));

                \Log::info('Command: ' . $command);
                \Log::info('Raw output: ' . $rawOutput);

                $lines = explode("\n", $rawOutput);
                $lastLine = trim(end($lines));

                \Log::info('Final output: ' . $lastLine);

                $mlResult = json_decode($lastLine, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($mlResult['prediksi'])) {
                    $order->prediction = $mlResult['prediksi'];
                    if (!empty($mlResult['estimasi_menit'])) {
                        $order->estimated_duration = $mlResult['estimasi_menit'];
                    }
                    if (!empty($mlResult['kategori_jam'])) {
                        $order->jam_kategori = $mlResult['kategori_jam'];
                    }
                    $order->save();
                }
            } catch (\Exception $e) {
                \Log::error('Prediction error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'order' => $order,
                'tracking_url' => route('tracking.show', $order->order_number),
                'message' => 'Order created successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Order create error: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus($id, $status)
    {
        $order = Order::where('order_number', $id)->firstOrFail();

        $validStatuses = ['waiting', 'processing', 'shipped', 'completed'];
        if (! in_array($status, $validStatuses)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $order->status = $status;

        // Set timestamps based on status changes
        if ($status === 'processing' && ! $order->started_at) {
            $order->started_at = now();
        } elseif ($status === 'completed') {
            $order->completed_at = now();

            // Ensure we have a start time
            if (! $order->started_at) {
                $order->started_at = $order->created_at;
            }

            // Calculate duration in minutes
            $startTime = $order->created_at;
            $duration = now()->diffInMinutes($startTime);

            // Ensure minimum 1 minute if same minute
            if ($duration < 1) {
                $duration = 1;
            }

            $order->duration_minutes = $duration;

            // Determine if late (more than 60 minutes)
            $order->is_late = $duration > 60;
        }

        $order->save();

        // Refresh to get latest attributes
        $order->refresh();

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    /**
     * Show order details (for edit modal)
     */
    public function show($id)
    {
        $order = Order::where('order_number', $id)->firstOrFail();

        return response()->json(['order' => $order]);
    }

    /**
     * Update order details
     */
    public function update(Request $request, $id)
    {
        $order = Order::where('order_number', $id)->firstOrFail();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_items' => 'required|string|max:500',
        ]);

        $order->update([
            'customer_name' => $request->customer_name,
            'order_items' => $request->order_items,
        ]);

        return response()->json([
            'success' => true,
            'order' => $order,
            'message' => 'Order updated successfully',
        ]);
    }

    /**
     * Delete an order
     */
    public function destroy($id)
    {
        $order = Order::where('order_number', $id)->firstOrFail();
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }

    public function getOrders()
    {
        $orders = Order::recent()->paginate(50);

        return response()->json(['orders' => $orders]);
    }
}
