<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'order_items',
        'status',
        'order_time',
        'started_at',
        'completed_at',
        'duration_minutes',
        'estimated_duration',
        'is_late',
        'prediction',
        'jam_kategori',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_duration' => 'integer',
        'is_late' => 'boolean',
    ];

    protected $appends = ['tracking_url', 'status_label', 'status_color', 'progress_step', 'dynamic_estimate'];

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'waiting' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'waiting' => 'bg-yellow-500',
            'processing' => 'bg-blue-500',
            'shipped' => 'bg-orange-500',
            'completed' => 'bg-green-500',
            default => 'bg-gray-500'
        };
    }

    public function getProgressStepAttribute()
    {
        return match ($this->status) {
            'waiting' => 1,
            'processing' => 2,
            'shipped' => 3,
            'completed' => 4,
            default => 1
        };
    }

    
    public function scopeRecent($query)
    {
    return $query->orderBy('id', 'desc');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['waiting', 'processing', 'shipped']);
    }

    /**
     * AI-based duration estimation using historical data
     * Learns from completed order durations to predict delivery time
     */
    public static function estimateDuration($orderItems = null)
    {
        try {
            // Get completed orders with actual duration (last 30 days)
            $completedOrders = self::where('status', 'completed')
                ->whereNotNull('duration_minutes')
                ->where('created_at', '>=', now()->subDays(30))
                ->limit(50)
                ->get();

            if ($completedOrders->isEmpty()) {
                // Default fallback: 45 minutes
                return 45;
            }

            // Primary learning: Use average duration from completed orders
            $avgDuration = round($completedOrders->avg('duration_minutes') ?: 45);

            // Secondary learning: Adjust based on time of day pattern
            $timeBasedAvg = self::calculateTimeBasedAverage($completedOrders);

            // Blend: 80% historical average (main learning) + 20% time pattern
            $estimate = round(($avgDuration * 0.8) + ($timeBasedAvg * 0.2));

            // Add small buffer (10% of estimate) for safety
            $buffer = round($estimate * 0.1);
            $finalEstimate = $estimate + $buffer;

            // Clamp between 20-120 minutes
            return max(20, min(120, $finalEstimate));
        } catch (\Exception $e) {
            return 45;
        }
    }

    /**
     * Get dynamic estimated remaining time based on current status and elapsed time.
     * Uses metadata only — no heavy DB queries — and is safe for all statuses.
     */
    public function getDynamicEstimateAttribute()
    {
        // Completed orders show 'Selesai'
        if ($this->status === 'completed') {
            return 'Selesai';
        }

        $estimated = (int) ($this->estimated_duration ?: 45);

        // Reference point: when the order was placed
        $reference = $this->created_at ?? now();
        $elapsed   = now()->diffInMinutes($reference);   // minutes already passed

        // Remaining estimate (never below 0)
        $remaining = max(0, $estimated - $elapsed);

        // Don't floor at 5 — show 0 when time is up so customer knows it's done
        return $remaining;
    }

    /**
     * Get average time spent in a specific status from completed orders
     */
    private function getAverageTimeForStatus($targetStatus)
    {
        // Get completed orders to analyze status durations
        $completedOrders = self::where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->where('created_at', '>=', now()->subDays(30))
            ->limit(30)
            ->get();

        if ($completedOrders->isEmpty()) {
            // Default estimates if no historical data
            return match ($targetStatus) {
                'processing' => 25,
                'shipped' => 15,
                default => 20
            };
        }

        // For simplicity, divide total duration by 2 (processing + shipping)
        // In a real implementation, you'd track timestamps for each status change
        $totalDuration = $completedOrders->avg('duration_minutes') ?: 40;

        return match ($targetStatus) {
            'processing' => round($totalDuration * 0.6), // 60% of time in processing
            'shipped' => round($totalDuration * 0.4),    // 40% of time in shipping
            default => round($totalDuration * 0.5)
        };
    }

    /**
     * Calculate average duration based on time of day patterns
     */
    private static function calculateTimeBasedAverage($completedOrders)
    {
        // Group by hour of day
        $hourlyDurations = [];
        foreach ($completedOrders as $order) {
            if ($order->started_at) {
                $hour = $order->started_at->hour;
                $hourlyDurations[$hour][] = $order->duration_minutes;
            }
        }

        // Get current hour's average or fallback to overall average
        $currentHour = now()->hour;
        if (isset($hourlyDurations[$currentHour]) && count($hourlyDurations[$currentHour]) >= 3) {
            return collect($hourlyDurations[$currentHour])->avg();
        }

        return $completedOrders->avg('duration_minutes');
    }

    /**
     * Get tracking URL for this order
     */
    public function getTrackingUrlAttribute()
    {
        return route('tracking.show', $this->order_number);
    }
}
