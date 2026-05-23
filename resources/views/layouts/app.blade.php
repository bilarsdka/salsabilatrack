<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Salsabila Track - Restaurant Delivery') }}</title>

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
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                        secondary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
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
            --color-primary: #f97316;
            --color-secondary: #ef4444;
            --color-dark: #1a1a1a;
            --color-light: #fef2f2;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fff7ed 0%, #fee2e2 100%);
            min-height: 100vh;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
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

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            display: none;
            align-items: center;
            gap: 12px;
        }

        .toast.show {
            display: flex;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Subtle pattern overlay */
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f97316' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* ── Menu Card Grid ──────────────────────────────────────────── */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.625rem;
        }
        @media (min-width: 640px) { .card-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 768px) { .card-grid { grid-template-columns: repeat(4, 1fr); } }

        .menu-card {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.625rem;
            overflow: hidden;
            transition: box-shadow 180ms ease, transform 180ms ease;
        }
        .menu-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.10);
            transform: translateY(-1px);
        }
        .card-accent {
            height: 3px;
            background: linear-gradient(90deg, #f97316 0%, #fb923c 100%);
        }
        .card-body {
            padding: 0.5rem 0.625rem 0;
            min-height: 3rem;
            display: flex;
            align-items: center;
        }
        .card-name {
            font-size: 0.8125rem;
            font-weight: 600;
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
            gap: 0.25rem;
            padding: 0.375rem 0.625rem 0.5rem;
        }
        .btn-qty {
            width: 1.625rem;
            height: 1.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background: #f3f4f6;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 120ms;
            flex-shrink: 0;
            padding: 0;
        }
        .btn-qty:hover { background: #e5e7eb; }
        .btn-number {
            width: 1.75rem;
            height: 1.625rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background: #fff;
            color: #374151;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            padding: 0;
            -moz-appearance: textfield;
            appearance: textfield;
        }
        .btn-number::-webkit-inner-spin-button { -webkit-appearance: none; }
        .btn-add {
            margin-left: auto;
            width: 1.625rem;
            height: 1.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 0.375rem;
            background: #10b981;
            color: #fff;
            font-size: 0.9375rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 120ms, box-shadow 120ms;
            flex-shrink: 0;
            padding: 0;
        }
        .btn-add:hover { background: #059669; box-shadow: 0 2px 6px rgba(16,185,129,0.35); }
    </style>
</head>
<body class="bg-pattern">
    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toast-message">Success!</span>
    </div>

    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="font-serif text-2xl font-bold text-gray-800">Salsabila Track</span>
                </div>
                <div class="text-sm text-gray-500 font-medium">
                    Restaurant Delivery System
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white/60 backdrop-blur border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Salsabila Track - Restaurant Delivery Management System
            </div>
        </div>
    </footer>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toast-message');
            toastMsg.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Link berhasil disalin!');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
