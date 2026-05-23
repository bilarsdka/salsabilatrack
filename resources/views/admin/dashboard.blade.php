@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fade-in">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="font-serif text-4xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="text-gray-600 mt-2">Kelola pesanan dan tracking delivery restoran</p>
    </div>

    <!-- Add Order Form -->
    <div class="glass-card rounded-2xl p-6 mb-8 shadow-xl">
        <h2 class="font-serif text-2xl font-semibold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Tambah Pesanan Baru
        </h2>
        
        <form id="add-order-form" class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Customer</label>
                <input type="text" id="customer-name" name="customer_name" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="Masukkan nama customer..." required>
            </div>

            <!-- Menu Selection Section -->
            <div class="md:col-span-2 mb-2">
                <div class="flex items-center gap-3 mb-3">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="font-semibold text-gray-800 text-lg">Pilih Menu</h3>
                </div>

                <!-- Tabs -->
                <div class="flex gap-2 mb-3 flex-wrap">
                    <button type="button" class="menu-tab active px-4 py-2 rounded-lg text-sm font-semibold bg-primary-500 text-white transition" data-tab="makanan">🍽️ Makanan</button>
                    <button type="button" class="menu-tab px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition" data-tab="minuman">🥤 Minuman</button>
                    <button type="button" class="menu-tab px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition" data-tab="tambahan">➕ Tambahan</button>
                </div>

                <!-- Menu Search -->
                <div class="mb-3">
                    <input type="text" id="menu-search" placeholder="🔍 Cari menu..." class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 text-sm">
                </div>

                <!-- Menu Panels -->
                <div id="menu-panel-makanan" class="menu-panel card-grid hidden"></div>
                <div id="menu-panel-minuman" class="menu-panel card-grid hidden"></div>
                <div id="menu-panel-tambahan" class="menu-panel card-grid hidden"></div>

                <!-- Selected items preview -->
                <div id="selected-preview" class="mt-3 hidden">
                    <p class="text-xs font-semibold text-gray-500 mb-1">Item terpilih:</p>
                    <div id="selected-items" class="flex flex-wrap gap-1"></div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Detail Pesanan</label>
                <input type="text" id="order-items" name="order_items"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="Contoh: 2x Nasi Goreng, 1x Es Teh, 1x Mie Ayam" required>
            </div>
            
            <div class="md:col-span-2">
                <button type="submit" 
                    class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Order
                </button>
            </div>
        </form>

        <!-- Order Success - Link Display -->
        <div id="order-link-container" class="hidden mt-6 p-5 bg-green-50 border border-green-200 rounded-xl animate-fade-in">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <span class="text-green-800 font-semibold">Order berhasil dibuat!</span>
                    <p class="text-sm text-green-700 mt-1">Link tracking untuk customer:</p>
                    <div class="flex items-center mt-2">
                        <input type="text" id="tracking-url" readonly
                            class="flex-1 px-4 py-2 bg-white border border-green-300 rounded-lg text-sm text-gray-700"
                            onclick="this.select()">
                        <button onclick="copyTrackingLink(document.getElementById('tracking-url').value)"
                            class="ml-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="glass-card rounded-2xl p-6 shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-serif text-2xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Daftar Pesanan
            </h2>
            <span id="total-orders" class="px-4 py-2 bg-primary-100 text-primary-800 rounded-full text-sm font-semibold">
                0 orders
            </span>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-primary-500 to-primary-600 text-white">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider w-24">ID Order</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider w-32">Customer</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider">Pesanan</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-24">Estimasi</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-24">Status</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-20">Waktu</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-20">Durasi</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-24">Prediksi</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-24">Hasil</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-20">Link</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="orders-table-body" class="divide-y divide-gray-200 bg-white">
                    <!-- Orders populated via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl p-8 max-w-lg w-full shadow-2xl animate-fade-in">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-serif text-2xl font-bold text-gray-800">Edit Order</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="edit-order-form">
            <input type="hidden" id="edit-order-number">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">ID Order</label>
                <input type="text" id="edit-order-id" readonly
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Customer</label>
                <input type="text" id="edit-customer-name" name="customer_name" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    required>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Detail Pesanan</label>
                <input type="text" id="edit-order-items" name="order_items"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    required>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-primary-500 to-primary-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// API URLs
const API_ORDERS_JSON = '{{ route("admin.orders.json") }}';
const API_ORDER_STORE = '{{ route("admin.orders.store") }}';
const API_ORDER_UPDATE_STATUS = (id, status) => `/admin/orders/${id}/status/${status}`;
const API_ORDER_SHOW = (id) => `/admin/orders/${id}`;
const API_ORDER_UPDATE = (id) => `/admin/orders/${id}`;

// Load orders on page load
document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
    
    // Add order form submission
    document.getElementById('add-order-form').addEventListener('submit', function(e) {
        e.preventDefault();
        addOrder();
    });

    // Edit order form submission
    document.getElementById('edit-order-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateOrder();
    });
});

// Load orders via AJAX
async function loadOrders() {
    try {
        const response = await fetch(API_ORDERS_JSON);
        if (!response.ok) {
            console.error('Failed to load orders:', response.status);
            return;
        }
        const data = await response.json();
        
        const tbody = document.getElementById('orders-table-body');
        tbody.innerHTML = '';
        
        if (data.orders && data.orders.data) {
            if (data.orders.data.length === 0) {
                // Empty state
                tbody.innerHTML = `
                    <tr>
                        <td colspan="11" class="px-3 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm font-medium">Belum ada pesanan</p>
                                <p class="text-xs text-gray-400 mt-1">Masukkan order baru di atas untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                data.orders.data.forEach(order => {
                    try {
                        const row = createOrderRow(order);
                        tbody.appendChild(row);
                    } catch (rowError) {
                        console.error('Error creating row:', rowError, order);
                    }
                });
            }
            
            // Update order count
            document.getElementById('total-orders').textContent = 
                `${data.orders.data.length} order${data.orders.data.length !== 1 ? 's' : ''}`;
        } else {
            console.error('Unexpected data structure:', data);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

// Create table row for an order
function createOrderRow(order) {
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50 transition-colors';
    
    // badges and buttons
    let statusBadge = '';
    let actionButtons = '';
    
    // Estimasi badge (purple)
    let estimatedBadge = order.estimated_duration 
        ? `<span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700">${order.estimated_duration}m</span>`
        : '<span class="text-gray-400 text-xs">-</span>';
    
    // Tracking button
    let trackingButton = '';
    if (order.status === 'completed') {
        trackingButton = `<a href="${order.tracking_url}" target="_blank" 
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition"
            title="Lihat tracking">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
        </a>`;
    } else {
        trackingButton = `<button onclick="copyTrackingLink('${order.tracking_url}')" 
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition"
            title="Copy link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
        </button>`;
    }
    
    // Status badge
    switch(order.status) {
        case 'waiting':
            statusBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>Menunggu</span>';
            actionButtons = `
                <div class="flex items-center justify-center gap-1">
                    <button onclick="updateStatus('${order.order_number}', 'processing')" 
                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold hover:bg-blue-200 transition"
                        title="Proses">
                        Proses
                    </button>
                    <button onclick="editOrder('${order.order_number}')" 
                        class="p-1 text-yellow-600 hover:bg-yellow-50 rounded transition"
                        title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteOrder('${order.order_number}', '${order.customer_name}')" 
                        class="p-1 text-red-600 hover:bg-red-50 rounded transition"
                        title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;
            break;
        case 'processing':
            statusBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>Diproses</span>';
            actionButtons = `
                <div class="flex items-center justify-center gap-1">
                    <button onclick="updateStatus('${order.order_number}', 'shipped')" 
                        class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs font-medium hover:bg-orange-200 transition"
                        title="Kirim">
                        Kirim
                    </button>
                    <button onclick="editOrder('${order.order_number}')" 
                        class="p-1 text-yellow-600 hover:bg-yellow-50 rounded transition"
                        title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteOrder('${order.order_number}', '${order.customer_name}')" 
                        class="p-1 text-red-600 hover:bg-red-50 rounded transition"
                        title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;
            break;
        case 'shipped':
            statusBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800"><span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5"></span>Dikirim</span>';
            actionButtons = `
                <div class="flex items-center justify-center gap-1">
                    <button onclick="updateStatus('${order.order_number}', 'completed')" 
                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium hover:bg-green-200 transition"
                        title="Selesai">
                        Selesai
                    </button>
                    <button onclick="editOrder('${order.order_number}')" 
                        class="p-1 text-yellow-600 hover:bg-yellow-50 rounded transition"
                        title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteOrder('${order.order_number}', '${order.customer_name}')" 
                        class="p-1 text-red-600 hover:bg-red-50 rounded transition"
                        title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;
            break;
        case 'completed':
            statusBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>Selesai</span>';
            actionButtons = `
                <div class="flex items-center justify-center gap-1">
                    <button onclick="editOrder('${order.order_number}')" 
                        class="p-1 text-yellow-600 hover:bg-yellow-50 rounded transition"
                        title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteOrder('${order.order_number}', '${order.customer_name}')" 
                        class="p-1 text-red-600 hover:bg-red-50 rounded transition"
                        title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;
            break;
    }
    
    // Duration text
    const durationText = order.duration_minutes !== null && order.duration_minutes !== undefined 
        ? order.duration_minutes + 'm' 
        : '-';
    
   
    // Prediction badge
    let predictionBadge = '-';
    if (order.prediction === 'Telat') {
        predictionBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1"></span>Telat</span>';
    } else if (order.prediction === 'Tepat') {
        predictionBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span>Tepat</span>';
    }

    // result aktual
    let hasilBadge = '-';
    if (order.status === 'completed' && order.duration_minutes !== null) {
        if (order.duration_minutes <= order.estimated_duration) {
            hasilBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span>Tepat</span>';
        } else {
            hasilBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1"></span>Telat</span>';
        }
    }


    tr.innerHTML = `
        <td class="px-3 py-3">
            <span class="font-mono font-semibold text-gray-800 text-sm">${order.order_number}</span>
        </td>
        <td class="px-3 py-3">
            <div class="text-sm text-gray-700 truncate max-w-[150px]" title="${order.customer_name}">${order.customer_name}</div>
        </td>
        <td class="px-3 py-3">
            <div class="text-sm text-gray-600 truncate max-w-[200px]" title="${order.order_items}">${order.order_items}</div>
        </td>
        <td class="px-3 py-3 text-center">
            ${estimatedBadge}
        </td>
        <td class="px-3 py-3 text-center">
            ${statusBadge}
        </td>
        <td class="px-3 py-3 text-center">
            <span class="text-sm text-gray-600 font-mono">${order.order_time}</span>
        </td>
        <td class="px-3 py-3 text-center">
            <span class="text-sm font-mono text-gray-700">${durationText}</span>
        </td>
        <td class="px-3 py-3 text-center">
            ${predictionBadge}
        </td>
        <td class="px-3 py-3 text-center">
            ${hasilBadge}
        </td>
        <td class="px-3 py-3 text-center">
            ${trackingButton}
        </td>
        <td class="px-3 py-3 text-center">
            ${actionButtons}
        </td>
    `;
    
    return tr;
}

// Add new order
async function addOrder() {
    const customerName = document.getElementById('customer-name').value.trim();
    const orderItems = document.getElementById('order-items').value.trim();
    
    if (!customerName || !orderItems) {
        showToast('Harap isi semua field!', 'error');
        return;
    }
    
    try {
        const response = await fetch(API_ORDER_STORE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ customer_name: customerName, order_items: orderItems })
        });
        
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            console.error('Invalid JSON response:', text.substring(0,200));
            showToast('Gagal menambahkan order: respons tidak valid', 'error');
            return;
        }
        
        if (data.success) {
            // Show tracking link
            document.getElementById('tracking-url').value = data.tracking_url;
            document.getElementById('order-link-container').classList.remove('hidden');
            
            // Clear form
            document.getElementById('customer-name').value = '';
            document.getElementById('order-items').value = '';
            
            showToast('Order berhasil ditambahkan!');
            console.log('Order created:', data.order);
            loadOrders();
        } else {
            showToast('Gagal menambahkan order: ' + (data.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error adding order:', error);
        showToast('Gagal menambahkan order: ' + error.message, 'error');
    }
}

// Update order status
async function updateStatus(orderNumber, status) {
    try {
        const response = await fetch(API_ORDER_UPDATE_STATUS(orderNumber, status), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(`Status berhasil diubah ke ${status}!`);
            loadOrders();
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showToast('Gagal mengubah status', 'error');
    }
}

// Edit order - show modal with order data
async function editOrder(orderNumber) {
    try {
        const response = await fetch(API_ORDER_SHOW(orderNumber));
        const data = await response.json();
        
        if (data.order) {
            document.getElementById('edit-order-id').value = data.order.order_number;
            document.getElementById('edit-customer-name').value = data.order.customer_name;
            document.getElementById('edit-order-items').value = data.order.order_items;
            document.getElementById('edit-order-number').value = data.order.order_number;
            
            document.getElementById('edit-modal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading order:', error);
        showToast('Gagal memuat data order', 'error');
    }
}

// Update order
async function updateOrder() {
    const orderNumber = document.getElementById('edit-order-number').value;
    const customerName = document.getElementById('edit-customer-name').value.trim();
    const orderItems = document.getElementById('edit-order-items').value.trim();
    
    if (!customerName || !orderItems) {
        showToast('Harap isi semua field!', 'error');
        return;
    }
    
    try {
        const response = await fetch(API_ORDER_UPDATE(orderNumber), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ customer_name: customerName, order_items: orderItems })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeEditModal();
            showToast('Order berhasil diperbarui!');
            loadOrders();
        }
    } catch (error) {
        console.error('Error updating order:', error);
        showToast('Gagal memperbarui order', 'error');
    }
}

// Delete order
async function deleteOrder(orderNumber, customerName) {
    if (!confirm(`Yakin ingin menghapus order ${orderNumber} untuk ${customerName}?`)) {
        return;
    }
    
    try {
        const response = await fetch(API_ORDER_UPDATE(orderNumber), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Order berhasil dihapus!');
            loadOrders();
        } else {
            showToast('Gagal menghapus order: ' + (data.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error deleting order:', error);
        showToast('Gagal menghapus order', 'error');
    }
}

// Close edit modal
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

// Copy tracking link
function copyTrackingLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        showToast('Link tracking berhasil disalin!');
    });
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toast-message');
    toastMsg.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}
// ─── MENU DATA & SELECTION ─────────────────────────────────
const MENU_DATA = {
    makanan: [
        'Nasgor Kebuli Sapi', 'Nasgor Kebuli Katsu', 'Nasgor Kebuli Ayam',
        'Nasi Mie Goreng Ayam Geprek', 'Nasi Mie Goreng Ayam Bakar', 'Nasi Mie Goreng Ayam Goreng',
        'Kentang Katsu Salad', 'Nasi Katsu Salad', 'Kentang Katsu', 'Nasi Katsu',
        'Kentang Cheese', 'Nasi Cheese', 'Kentang Cheese Salad', 'Nasi Cheese Salad',
        'Nasi Ayam Geprek Gobyos + Tempe', 'Nasi Ayam Penyet Bakar + Tempe',
        'Nasi Ayam Penyet Goreng + Tempe', 'Nasi Lele Penyet + Tempe', 'Nasi SFC + Tempe',
        'Kentang Goreng Saus', 'Nasi Soto Babat', 'Nasi Soto Ayam', 'Kenplingju', 'Nasi Soto Sapi',
    ],
    minuman: [
        'Chocolatos Drink', 'Jeruk Susu Es', 'Jeruk Susu Hangat', 'Jeruk Es', 'Jeruk Hangat',
        'Teh Susu Es', 'Teh Susu Hangat', 'Kuku Bima Susu Es', 'Extra Jos Susu Es',
        'Es Laguna Salsabilla', 'Hilo Es', 'Hilo Hangat', 'Susu Putih Es', 'Susu Putih Hangat',
        'Susu Coklat Es', 'Susu Coklat Hangat', 'Cappucino Coffe Es', 'Cappucino Coffe Hangat',
        'Lemon Tea Es', 'Lemon Tea Hangat', 'Orange Squash Es', 'Orange Squash Hangat',
        'Teh Tarik Es', 'Teh Tarik Hangat', 'Teh Leci Es', 'Teh Leci Hangat',
        'Teh Melon Es', 'Teh Melon Hangat', 'Teh Mangga Es', 'Teh Mangga Hangat',
        'Kopi Hitam', 'Teh Manis Es', 'Teh Manis Hangat', 'Teh Tawar Es', 'Teh Tawar Hangat',
        'Es Batu', 'Air Mineral 1,5 L', 'Air Mineral 300 ml', 'Aneka Nutrisari Es', 'Aneka Nutrisari Hangat',
    ],
    tambahan: [
        'Telor Dadar', 'Telor Ceplok', 'Kerupuk Udang', 'Peyek', 'Tahu Goreng', 'Tempe Goreng',
        'Mendoan', 'Bala-bala', 'Bacem Tahu', 'Bacem Tempe', 'Kol Goreng', 'Terong Goreng',
        'Nasi', 'Sambal',
    ],
};

// Current state of selected items (label → qty)
const selectedItems = {};

// Parse the display name back to the canonical menu name
function getCanonicalName(display) {
    return display.replace(/^(\d+x)\s*/, '');
}

// Add item → order-items field
function addMenuToOrder(itemText) {
    const field = document.getElementById('order-items');
    const current = field.value.trim();
    field.value = current ? current + ', ' + itemText : itemText;
    field.focus();
    showToast('Ditambahkan: ' + itemText);
}

// Update selected preview
function updatePreview() {
    const preview = document.getElementById('selected-preview');
    const container = document.getElementById('selected-items');
    const entries = Object.entries(selectedItems).filter(([, v]) => v > 0);
    if (entries.length === 0) {
        preview.classList.add('hidden');
        return;
    }
    preview.classList.remove('hidden');
    container.innerHTML = entries.map(([k, v]) =>
        '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-700">' +
            v + 'x ' + k +
        '</span>'
    ).join('');
}

// ─── Render a single menu card into a panel ─────────────────────────────────
function renderMenuItem(item, panel) {
    // Support both a plain string ("Nasi Goreng") and the old
    // { name: 'Nasi Goreng' } object shape so both MENU_DATA formats work.
    const name = typeof item === 'string' ? item : item.name;

    // Card
    const card = document.createElement('div');
    card.className = 'menu-card';

    const accent = document.createElement('div');
    accent.className = 'card-accent';
    card.appendChild(accent);

    // Body — item name label
    const body = document.createElement('div');
    body.className = 'card-body';

    const nameSpan = document.createElement('span');
    nameSpan.className = 'card-name';
    nameSpan.textContent = name;
    body.appendChild(nameSpan);
    card.appendChild(body);

    // Footer — qty controls + add button
    const footer = document.createElement('div');
    footer.className = 'card-footer';

    const decBtn = document.createElement('button');
    decBtn.type = 'button';
    decBtn.textContent = '−';
    decBtn.className = 'btn-qty';

    const qtyInput = document.createElement('input');
    qtyInput.type = 'number';
    qtyInput.min = 1; qtyInput.max = 99; qtyInput.value = 1;
    qtyInput.className = 'btn-number';

    const incBtn = document.createElement('button');
    incBtn.type = 'button';
    incBtn.textContent = '+';
    incBtn.className = 'btn-qty';

    const addBtn = document.createElement('button');
    addBtn.type = 'button';
    addBtn.textContent = '+';
    addBtn.className = 'btn-add';

    decBtn.addEventListener('click', () => {
        const v = Math.max(1, parseInt(qtyInput.value || '1'));
        qtyInput.value = v - 1;
    });

    incBtn.addEventListener('click', () => {
        const v = Math.min(99, parseInt(qtyInput.value || '1'));
        qtyInput.value = v + 1;
    });

    addBtn.addEventListener('click', () => {
        const qty = Math.max(1, parseInt(qtyInput.value || '1'));
        const itemText = qty + 'x ' + name;
        selectedItems[name] = (selectedItems[name] || 0) + qty;
        updatePreview();
        addMenuToOrder(itemText);
    });

    footer.appendChild(decBtn);
    footer.appendChild(qtyInput);
    footer.appendChild(incBtn);
    footer.appendChild(addBtn);
    card.appendChild(footer);

    panel.appendChild(card);
}

// ─── Populate all panels ────────────────────────────────────────────────────
function buildPanels() {
    ['makanan', 'minuman', 'tambahan'].forEach(cat => {
        const panel = document.getElementById('menu-panel-' + cat);
        if (!panel) return;
        panel.innerHTML = '';
        MENU_DATA[cat].forEach(item => renderMenuItem(item, panel));
    });
}

// ─── Tab switching ──────────────────────────────────────────────────────────
document.querySelectorAll('.menu-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        const tab = this.dataset.tab;
        document.querySelectorAll('.menu-tab').forEach(b => {
            b.classList.remove('bg-primary-500', 'text-white');
            b.classList.add('bg-gray-100', 'text-gray-600');
        });
        this.classList.remove('bg-gray-100', 'text-gray-600');
        this.classList.add('bg-primary-500', 'text-white');
        document.querySelectorAll('.menu-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('menu-panel-' + tab).classList.remove('hidden');
    });
});

// ─── Search filter ──────────────────────────────────────────────────────────
document.getElementById('menu-search').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    ['makanan', 'minuman', 'tambahan'].forEach(cat => {
        const panel = document.getElementById('menu-panel-' + cat);
        if (!panel) return;
        panel.querySelectorAll(':scope > div').forEach(el => {
            const text = (el.textContent || '').toLowerCase();
            el.style.display = text.includes(q) ? '' : 'none';
        });
    });
});

// ─── Init ───────────────────────────────────────────────────────────────────
buildPanels();
</script>
@endsection