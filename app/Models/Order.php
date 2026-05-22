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
     * Get dynamic estimated remaining time based on current status
     * Used for tracking page to show updated estimates
     */
    public function getDynamicEstimateAttribute()
    {
        // If order is completed, return 'Selesai'
        if ($this->status === 'completed') {
            return 'Selesai';
        }

        // If no estimated_duration set, use default
        if (! $this->estimated_duration) {
            return 45;
        }

        // Calculate elapsed time since order was created
        $orderTime = $this->created_at ?? now();
        $elapsedMinutes = now()->diffInMinutes($orderTime);

        // Estimate remaining time based on status
        switch ($this->status) {
            case 'waiting':
                // Still waiting, show full estimate minus elapsed time
                return max(5, $this->estimated_duration - $elapsedMinutes);

            case 'processing':
                // Get average processing time from completed orders
                $avgProcessingTime = $this->getAverageTimeForStatus('processing');
                $elapsedSinceStarted = $this->started_at ? now()->diffInMinutes($this->started_at) : 0;
                $remaining = max(5, $avgProcessingTime - $elapsedSinceStarted);

                return $remaining;

            case 'shipped':
                // Get average shipping time from completed orders
                $avgShippingTime = $this->getAverageTimeForStatus('shipped');
                $shippedAt = $this->started_at ? $this->started_at->addMinutes(
                    $this->getAverageTimeForStatus('processing')
                ) : now();
                $elapsedSinceShipped = now()->diffInMinutes($shippedAt);
                $remaining = max(5, $avgShippingTime - $elapsedSinceShipped);

                return $remaining;

            default:
                return $this->estimated_duration;
        }
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
