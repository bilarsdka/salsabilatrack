@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in">
    <!-- Order Info Card -->
    <div class="glass-card rounded-2xl p-8 shadow-2xl mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-6 border-b border-gray-200">
            <div>
                <p class="text-gray-500 text-sm font-medium mb-1">ID Order</p>
                <h2 class="font-serif text-3xl font-bold text-primary-600">{{ $order->order_number }}</h2>
            </div>
            <div class="mt-4 md:mt-0">
                <p class="text-gray-500 text-sm text-right mb-1">
                    @if($order->status === 'completed')
                    Status Pesanan
                    @else
                    Estimasi Waktu Tersisa
                    @endif
                </p>
                @if($order->status === 'completed')
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl shadow-lg">
                    <span class="text-2xl font-bold">Selesai</span>
                </div>
                @elseif(is_numeric($order->dynamic_estimate) && (int)$order->dynamic_estimate >= 0)
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-xl shadow-lg js-estimate-box"  id="dynamic-estimate" data-estimate-url="{{ route('tracking.dynamic', $order->order_number) }}">
                    <span class="text-2xl font-bold js-estimate-text">{{ $order->dynamic_estimate }}</span>
                    <span class="text-sm ml-1 js-estimate-unit">Menit</span>
                </div>
                @else
                <div class="bg-gradient-to-r from-gray-400 to-gray-500 text-white px-6 py-3 rounded-xl shadow-lg">
                    <span class="text-2xl font-bold">--</span>
                    <span class="text-sm ml-1">Menit</span>
                </div>
                @endif
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <h3 class="font-serif text-2xl font-bold text-gray-800 mb-2">{{ $order->customer_name }}</h3>
                <p class="text-gray-600 text-lg leading-relaxed">{{ $order->order_items }}</p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-gray-500 text-sm mb-2">Status Pesanan</p>
                <div id="current-status" class="inline-block px-6 py-3 rounded-xl font-bold text-lg">
                    {{ $order->status_label }}
                </div>
                <p class="text-gray-500 text-sm mt-4">Waktu Order: <span class="font-mono font-semibold">{{ $order->order_time }}</span></p>
                @if($order->duration_minutes)
                <p class="text-gray-500 text-sm">Durasi: <span class="font-mono font-semibold">{{ $order->duration_minutes }} menit</span></p>
                @endif
            </div>
        </div>
    </div>

    <!-- Progress Tracking -->
    <div class="glass-card rounded-2xl p-8 shadow-xl">
        <h3 class="font-serif text-2xl font-semibold text-gray-800 mb-8 text-center">Status Progress Pesanan</h3>

        <div class="relative">
            <!-- Progress Line Background -->
            <div class="absolute top-5 left-0 w-full h-1 bg-gray-200 rounded"></div>

            <!-- Progress Line Filled -->
            @php
                $progressWidth = match($order->progress_step) {
                    1 => '0%',
                    2 => '33%',
                    3 => '66%',
                    4 => '100%',
                    default => '0%'
                };
                $statusColor = match($order->status) {
                    'waiting' => 'bg-yellow-500',
                    'processing' => 'bg-blue-500',
                    'shipped' => 'bg-orange-500',
                    'completed' => 'bg-green-500',
                    default => 'bg-gray-500'
                };
            @endphp

            <div class="relative z-10 flex justify-between">
                <!-- Step 1: Menunggu -->
                <div class="flex flex-col items-center" style="width: 25%;">
                    <div class="w-10 h-10 rounded-full border-4 {{ $order->progress_step >= 1 ? 'bg-yellow-500 border-yellow-500 text-white' : 'bg-white border-gray-300 text-gray-400' }} flex items-center justify-center font-bold transition-all duration-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold {{ $order->progress_step >= 1 ? 'text-yellow-600' : 'text-gray-400' }}">Menunggu</p>
                </div>

                <!-- Step 2: Diproses -->
                <div class="flex flex-col items-center" style="width: 25%;">
                    <div class="w-10 h-10 rounded-full border-4 {{ $order->progress_step >= 2 ? 'bg-blue-500 border-blue-500 text-white' : 'bg-white border-gray-300 text-gray-400' }} flex items-center justify-center font-bold transition-all duration-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold {{ $order->progress_step >= 2 ? 'text-blue-600' : 'text-gray-400' }}">Diproses</p>
                </div>

                <!-- Step 3: Dikirim -->
                <div class="flex flex-col items-center" style="width: 25%;">
                    <div class="w-10 h-10 rounded-full border-4 {{ $order->progress_step >= 3 ? 'bg-orange-500 border-orange-500 text-white' : 'bg-white border-gray-300 text-gray-400' }} flex items-center justify-center font-bold transition-all duration-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414l1.414-1.414a1 1 0 011.242-.242l-1.414 1.414-1.414-1.414 1.414-1.414M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold {{ $order->progress_step >= 3 ? 'text-orange-600' : 'text-gray-400' }}">Dikirim</p>
                </div>

                <!-- Step 4: Selesai -->
                <div class="flex flex-col items-center" style="width: 25%;">
                    <div class="w-10 h-10 rounded-full border-4 {{ $order->progress_step >= 4 ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-300 text-gray-400' }} flex items-center justify-center font-bold transition-all duration-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold {{ $order->progress_step >= 4 ? 'text-green-600' : 'text-gray-400' }}">Selesai</p>
                </div>
            </div>

            <!-- Progress line fill -->
            @php
                $width = match($order->progress_step) {
                    1 => '0%',
                    2 => '33%',
                    3 => '66%',
                    4 => '100%',
                    default => '0%'
                };
            @endphp
            <div class="absolute top-5 left-0 h-1 bg-green-500 rounded transition-all duration-500"
                 style="width: {{ $width }}; z-index: 5;">
            </div>
        </div>

        @if($order->is_late)
        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <span class="font-semibold">⚠️ Terlambat:</span> Pesanan melebihi estimasi waktu ({{ $order->duration_minutes }} menit)
        </div>
        @endif
    </div>

    <!-- Additional Info -->
    <div class="mt-8 text-center text-gray-500 text-sm">
        <p>Link tracking: <span class="font-mono text-primary-600">{{ $order->tracking_url }}</span></p>
        <p class="mt-2">Simpan link ini untuk mengecek status pesanan Anda</p>
    </div>
</div>

@if($order->status !== 'completed')
<script>
(function () {
    const box     = document.querySelector('.js-estimate-box');
    const textEl  = document.querySelector('.js-estimate-text');
    const unitEl  = document.querySelector('.js-estimate-unit');
    let   minutes = '{{ $order->dynamic_estimate }}';
    const isNum   = !isNaN(parseInt(minutes));
    const apiUrl  = box ? box.dataset.estimateUrl : null;

    // ---- Countdown: decrement every second (only integer minutes) ----
    function tick() {
        if (!isNum) return;
        let m = parseInt(minutes, 10);
        if (m > 0) {
            minutes = String(m - 1);
            if (textEl) textEl.textContent = minutes;
        } else {
            // Time's up — reload page to get updated status
            setTimeout(() => window.location.reload(), 2000);
        }
    }

    // Sync metres with the wall-clock: reset tick at each full minute boundary
    let lastSecond = new Date().getSeconds();
    const syncTimer = setInterval(function () {
        const s = new Date().getSeconds();
        if (s !== lastSecond) {
            lastSecond = s;
            if (s === 0) tick();   // tick exactly at minute boundary
        }
    }, 250);

    // ---- AJAX poll: refresh estimate every 30 s ----
    if (apiUrl) {
        setInterval(function () {
            fetch(apiUrl, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (typeof data.remaining === 'number' && !isNaN(data.remaining)) {
                        minutes = String(data.remaining);
                        if (textEl) textEl.textContent = minutes;
                    }
                    if (data.status === 'completed') {
                        window.location.reload();
                    }
                })
                .catch(function () { /* silently ignore poll errors */ });
        }, 30000);
    }
})();
</script>
@endif
@endsection
