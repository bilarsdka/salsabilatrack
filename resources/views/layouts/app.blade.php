<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Salsabila Track - Delivery Makanan Profesional') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN for simplicity) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef3c7',
                            100: '#fde68a',
                            200: '#fcd34d',
                            300: '#fbbf24',
                            400: '#f59e0b',
                            500: '#d97706',
                            600: '#b45309',
                            700: '#92400e',
                            800: '#78350f',
                            900: '#451a03',
                        },
                        secondary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        :root {
            --color-primary: #d97706;
            --color-primary-strong: #b45309;
            --color-secondary: #f97316;
            --color-dark: #111827;
            --color-muted: #6b7280;
            --color-surface: rgba(255, 255, 255, 0.92);
            --shadow-soft: 0 18px 50px rgba(15, 23, 42, 0.08);
            --shadow-md: 0 12px 24px rgba(15, 23, 42, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(249, 115, 22, 0.12), transparent 30%),
                        radial-gradient(circle at bottom right, rgba(217, 119, 6, 0.08), transparent 25%),
                        linear-gradient(180deg, #fffaf0 0%, #fff7ed 100%);
            color: var(--color-dark);
            line-height: 1.5;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.93);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 16px 44px rgba(15, 23, 42, 0.1);
        }

        .glass-card:hover {
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
        }

        .status-waiting { background-color: #f59e0b; color: white; }
        .status-processing { background-color: #3b82f6; color: white; }
        .status-shipped { background-color: #f97316; color: white; }
        .status-completed { background-color: #10b981; color: white; }

        .progress-circle {
            transition: all 0.3s ease;
        }

        .progress-circle.active {
            border-color: #10b981;
            background-color: #10b981;
            color: white;
        }

        .progress-line {
            transition: width 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            max-width: 340px;
            padding: 16px 20px;
            background: white;
            border-radius: 0.875rem;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.15);
            border-left: 4px solid var(--color-primary);
            z-index: 9999;
            display: none;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .toast.show {
            display: flex;
            animation: slideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideIn {
            from { transform: translateX(24px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .bg-pattern {
            background-attachment: fixed;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.875rem;
        }
        @media (min-width: 640px) { .card-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (min-width: 768px) { .card-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

        .menu-card {
            display: flex;
            flex-direction: column;
            background: white;
            border: 1px solid rgba(229, 231, 235, 0.95);
            border-radius: 0.875rem;
            overflow: hidden;
            transition: all 200ms ease;
            box-shadow: var(--shadow-md);
        }
        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 56px rgba(15, 23, 42, 0.14);
            border-color: rgba(217, 119, 6, 0.2);
        }
        .card-accent {
            height: 4px;
            background: linear-gradient(90deg, var(--color-secondary) 0%, var(--color-primary) 100%);
        }
        .card-body {
            padding: 0.75rem 0.875rem 0;
            min-height: 3.25rem;
            display: flex;
            align-items: center;
        }
        .card-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
        }
        .card-footer {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.875rem 0.65rem;
        }
        .btn-qty {
            width: 1.75rem;
            height: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: #f8fafc;
            color: #374151;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 120ms ease;
            flex-shrink: 0;
            padding: 0;
        }
        .btn-qty:hover { background: #f0f4f8; border-color: #d1d5db; }
        .btn-number {
            width: 1.85rem;
            height: 1.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: #ffffff;
            color: #374151;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            padding: 0;
            font-family: 'Inter', sans-serif;
            appearance: textfield;
            -moz-appearance: textfield;
        }
        .btn-number::-webkit-inner-spin-button,
        .btn-number::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .btn-add {
            margin-left: auto;
            width: 1.75rem;
            height: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 0.5rem;
            background: #10b981;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 120ms ease;
            flex-shrink: 0;
            padding: 0;
        }
        .btn-add:hover {
            background: #059669;
            box-shadow: 0 8px 18px rgba(16, 185, 129, 0.25);
            transform: scale(1.05);
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        textarea {
            border-color: rgba(229, 231, 235, 0.95);
            transition: all 200ms ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
        }

        .table-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
            color: white;
        }

        .table-row:hover {
            background-color: rgba(217, 119, 6, 0.04);
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 1.875rem;
            }
        }
    </style>
</head>
<body class="bg-pattern">
    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toast-message">Berhasil!</span>
    </div>

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 backdrop-blur-xl border-b border-white/10">
        <div class="bg-gradient-to-r from-white/80 to-white/70 backdrop-blur-2xl border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Brand -->
                    <div class="flex items-center space-x-3 group">
                        <div class="w-11 h-11 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-105">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1.5a6.75 6.75 0 100 13.5A6.75 6.75 0 0012 1.5zM9 15h6v1.5H9zm4.5 3a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM7.5 18a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-serif text-xl font-bold text-gray-900">Salsabila Track</h1>
                            <p class="text-xs text-gray-500 font-medium">Delivery Makanan Cepat</p>
                        </div>
                    </div>

                    <!-- Tagline -->
                    <div class="hidden sm:flex flex-col items-center">
                        <p class="text-sm font-semibold text-gray-700">Kelola Pesanan dengan Mudah</p>
                        <p class="text-xs text-gray-500">Tracking Real-time & Prediksi AI</p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-gray-200 bg-white/40 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="font-serif text-lg font-bold text-gray-900 mb-3">Salsabila Track</h3>
                    <p class="text-sm text-gray-600">Sistem manajemen delivery makanan modern dengan tracking real-time dan prediksi waktu berbasis AI.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Fitur</h4>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✓ Kelola Pesanan</li>
                        <li>✓ Tracking Real-time</li>
                        <li>✓ Prediksi AI Cerdas</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Hubungi Kami</h4>
                    <p class="text-sm text-gray-600">Email: info@salsabila.local</p>
                </div>
            </div>
            <div class="border-t border-gray-200 pt-6 text-center">
                <p class="text-gray-500 text-sm font-medium">
                    &copy; {{ date('Y') }} <span class="text-primary-600 font-semibold">Salsabila Track</span> - Sistem Delivery Makanan Profesional
                </p>
            </div>
        </div>
    </footer>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toast-message');
            toastMsg.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Link berhasil disalin!');
            }).catch(() => {
                showToast('Gagal menyalin link', 'error');
            });
        }

        function copyTrackingLink(url) {
            copyToClipboard(url);
        }
    </script>

    @stack('scripts')
</body>
</html>
