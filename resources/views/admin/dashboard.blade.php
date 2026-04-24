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
            
            <div>
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
                        <button onclick="copyTrackingLink()" 
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
            <table class="w-full text-sm table-fixed">
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
                        <td colspan="10" class="px-3 py-12 text-center text-gray-500">
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
                        class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium hover:bg-blue-200 transition"
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
    if (order.status === 'completed') {
        predictionBadge = order.is_late
            ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1"></span>Telat</span>'
            : '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span>Tepat</span>';
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
</script>
@endsection
