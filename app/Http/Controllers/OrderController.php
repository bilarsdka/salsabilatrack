<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Auto-detect a Python executable that has numpy installed.
     * Checks common install paths and PATH, returns full path or null.
     */
    private static function findPythonWithNumpy(): ?string
    {
        $candidates = [
            'python', 'python3', 'py',
            'C:\Program Files\Python312\python.exe',
            'C:\Program Files\Python311\python.exe',
            'C:\Program Files (x86)\Python312\python.exe',
            'C:\Users\ASUS\AppData\Local\Programs\Python\Python312\python.exe',
            'C:\Users\ASUS\AppData\Local\Programs\Python\Python311\python.exe',
        ];

        foreach ($candidates as $candidate) {
            $escaped = escapeshellarg($candidate);
            $out = trim(shell_exec($candidate . ' -c "import numpy; print(numpy.__version__)" 2>&1'));
            if (is_numeric(substr($out, 0, 1))) {
                \Log::info("Using Python: $candidate (numpy $out)");
                return $candidate;
            }
        }

        \Log::warning('No Python with numpy found — will use PHP fallback prediction');
        return null;
    }

    /**
     * PHP-based prediction fallback (used when ML is unavailable).
     * Implements the same logic as the ML model using only order metadata.
     *
     * @return string 'Telat' or 'Tepat'
     */
    private static function phpPredict(string $orderTime, int $queueCount, int $estimatedDuration): string
    {
        // Parse hour from HH:mm
        $parts = explode(':', $orderTime);
        $hour  = (int)($parts[0] ?? 12);

        // *** Peak-hour rule (same heuristic as the ML training script) ***
        $isPeak  = ($hour >= 11 && $hour <= 13) || ($hour >= 17 && $hour <= 19);
        $isBigQueue = $queueCount >= 8;
        $isLongEst  = $estimatedDuration >= 45;

        $score = ($isPeak  ? 2 : 0)
               + ($isBigQueue ? 2 : 0)
               + ($isLongEst  ? 1 : 0);

        // Slight noise (0-2) to match the ML variability
        $noise = random_int(0, 2);

        return ($score + $noise) >= 3 ? 'Telat' : 'Tepat';
    }

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
                $itemsArg = escapeshellarg($request->order_items);

                // Try ML prediction with auto-detected Python path
                $pythonPath = self::findPythonWithNumpy();
                $scriptPath = base_path('ml/predict.py');

                if ($pythonPath) {
                    $command = "\"$pythonPath\" \"$scriptPath\" $orderTime $jumlahAntrian $estimatedDuration $itemsArg full 2>&1";
                    \Log::info('ML Command: ' . $command);
                    $rawOutput = trim(shell_exec($command));
                    \Log::info('ML Raw output: ' . $rawOutput);

                    $lines = explode("\n", $rawOutput);
                    $lastLine = trim(end($lines));
                    \Log::info('ML Final output: ' . $lastLine);

                    $mlResult = json_decode($lastLine, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($mlResult['prediksi'])) {
                        $order->prediction     = $mlResult['prediksi'];
                        $order->estimated_duration = !empty($mlResult['estimasi_menit']) ? (int) $mlResult['estimasi_menit'] : $estimatedDuration;
                        $order->jam_kategori   = !empty($mlResult['kategori_jam'])  ? $mlResult['kategori_jam'] : null;
                        $order->save();
                    }
                }

                // Fallback: PHP-based prediction if ML unavailable or failed
                if (empty($order->prediction)) {
                    $order->prediction = self::phpPredict($orderTime, $jumlahAntrian, $estimatedDuration);
                    $order->save();
                }
            } catch (\Exception $e) {
                \Log::error('Prediction error: ' . $e->getMessage());
                // PHP fallback on any error
                try {
                    $order->prediction = self::phpPredict($orderTime, $jumlahAntrian, $estimatedDuration);
                    $order->save();
                } catch (\Exception $e2) {
                    \Log::error('PHP fallback also failed: ' . $e2->getMessage());
                }
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

            if (! $order->started_at) {
                $order->started_at = $order->created_at ?? now();
            }

            $startTime = $order->started_at;
            $now = now();

            $duration = $startTime->diffInMinutes($now);

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
