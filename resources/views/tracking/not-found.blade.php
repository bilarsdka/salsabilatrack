@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 animate-fade-in">
    <div class="glass-card rounded-2xl p-12 shadow-2xl text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h2 class="font-serif text-3xl font-bold text-gray-900 mb-3">Pesanan Tidak Ditemukan</h2>
        <p class="text-gray-600 mb-8 text-lg">Maaf, kami tidak dapat menemukan pesanan dengan ID <span class="font-mono font-semibold text-primary-600">{{ $orderNumber }}</span>. Silakan periksa kembali link Anda.</p>
        
        <a href="{{ route('admin.dashboard') }}" 
           class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-primary-600 to-secondary-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
