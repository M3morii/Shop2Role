@extends('layouts.customer')

@section('title', 'Customer Item')

@section('page_title', 'Dashboard Customer')

@section('content')
    <div id="itemList" class="row"></div>
@endsection

@section('modals')
    <div id="cartModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Keranjang Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="orderBtn">Order</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Tambahkan link ke library Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            function getToken() {
                return sessionStorage.getItem('access_token');
            }

            function showMessage(message, type = 'success') {
                $('#messageContainer').html(`<div class="alert alert-${type}">${message}</div>`);
                setTimeout(() => {
                    $('#messageContainer').html('');
                }, 5000);
            }

            function handleAjaxError(xhr) {
                if (xhr.status === 401) {
                    sessionStorage.removeItem('access_token');
                    window.location.href = '/login';
                } else {
                    showMessage('Terjadi kesalahan. Silakan coba lagi.', 'danger');
                }
            }

            function loadItems(search = '') {
                let url = '/api/items';
                if (search) {
                    url += '/search?search=' + encodeURIComponent(search);
                }
                $.ajax({
                    url: url,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        const items = Array.isArray(response) ? response : (response.data || []);
                        let itemsHtml = '';
                        items.forEach(item => {
                            let imageSrc = item.files && item.files.length > 0 
                                ? `/storage/${item.files[0].file_path}` 
                                : 'https://via.placeholder.com/300x200.png?text=Tidak+ada+gambar';
                            
                            itemsHtml += `
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <img src="${imageSrc}" class="card-img-top" alt="${item.name}">
                                        <div class="card-body">
                                            <div class="card-content">
                                                <h5 class="card-title">${item.name}</h5>
                                                <p class="card-text text-muted">${item.description}</p>
                                                <div class="price-stock-container">
                                                    <p class="price">Rp${Number(item.sellprice).toLocaleString('id-ID')}</p>
                                                    <span class="badge bg-info badge-stock">Stok: ${item.stock}</span>
                                                </div>
                                            </div>
                                            <div class="card-actions">
                                                <div class="input-group mt-3">
                                                    <button class="btn btn-outline-secondary decrease-quantity" type="button" data-id="${item.id}">-</button>
                                                    <input type="number" class="form-control item-quantity" value="0" min="0" max="${item.stock}" data-id="${item.id}" data-price="${item.sellprice}">
                                                    <button class="btn btn-outline-secondary increase-quantity" type="button" data-id="${item.id}">+</button>
                                                </div>
                                                <p class="mt-2 total-price" data-id="${item.id}">Total: Rp0</p>
                                                <button class="btn btn-primary w-100 mt-3 add-to-cart" data-id="${item.id}">
                                                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#itemList').html(itemsHtml);
                    },
                    error: handleAjaxError
                });
            }

            function loadCart() {
                $.ajax({
                    url: '/api/cart',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        let cartHtml = '<div class="cart-container">';
                        let totalCart = 0;

                        if (Array.isArray(response) && response.length > 0) {
                            cartHtml += '<div class="cart-items">';
                            response.forEach(item => {
                                const itemTotal = item.quantity * item.price;
                                totalCart += itemTotal;
                                cartHtml += `
                                    <div class="cart-item">
                                        <div class="item-details">
                                            <h5 class="item-name">${item.item_name}</h5>
                                            <p class="item-price">Rp${Number(item.price).toLocaleString('id-ID')} x ${item.quantity}</p>
                                        </div>
                                        <div class="item-total">
                                            <p>Rp${Number(itemTotal).toLocaleString('id-ID')}</p>
                                            <button class="btn btn-danger btn-sm delete-cart-item" data-id="${item.id}">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                `;
                            });
                            cartHtml += '</div>';
                            cartHtml += `
                                <div class="cart-summary">
                                    <div class="summary-row">
                                        <span>Total Keranjang:</span>
                                        <strong>Rp${Number(totalCart).toLocaleString('id-ID')}</strong>
                                    </div>
                                </div>
                            `;
                        } else {
                            cartHtml += '<p class="empty-cart">Keranjang belanja Anda kosong.</p>';
                        }
                        
                        cartHtml += '</div>';
                        $('#cartContent').html(cartHtml);
                        $('#cartModal .modal-title').text('Keranjang Belanja');
                        $('#orderBtn').toggle(totalCart > 0);
                        $('#cartModal').modal('show');
                    },
                    error: handleAjaxError
                });
            }

            function viewInvoice() {
                $.ajax({
                    url: '/api/invoice',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        let invoicesHtml = '';
                        response.forEach(invoice => {
                            let status = invoice.status || 'pending';
                            let statusClass = status === 'approved' ? 'status-approved' : 'status-pending';
                            invoicesHtml += `
                                <div class="invoice-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span>Invoice #${invoice.id}</span>
                                        <span class="${statusClass}">Status: ${status}</span>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1">Tanggal: ${invoice.purchase_date}</p>
                                        <ul class="list-group list-group-flush">
                    `;
                            invoice.orders.forEach(order => {
                                invoicesHtml += `
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>${order.item_name} - Jumlah: ${order.quantity}</span>
                                        <span>Rp${Number(order.price).toLocaleString('id-ID')}</span>
                                    </li>
                                `;
                            });
                            invoicesHtml += `
                                        </ul>
                                    </div>
                                    <div class="card-footer text-end">
                                        <strong>Total: Rp${Number(invoice.total_price).toLocaleString('id-ID')}</strong>
                                    </div>
                                </div>
                            `;
                        });
                        $('#cartContent').html(invoicesHtml);
                        $('#cartModal .modal-title').text('Daftar Invoice');
                        $('#orderBtn').hide();
                        $('#cartModal').modal('show');
                    },
                    error: handleAjaxError
                });
            }

            function viewOrders() {
                $.ajax({
                    url: '/api/order',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        let ordersHtml = '';
                        let groupedOrders = {};
                        let orderNumber = 1;

                        response.orders.forEach(order => {
                            let status = order.status || 'pending';
                            if (status !== 'approved') {
                                if (!groupedOrders[order.invoice_id]) {
                                    groupedOrders[order.invoice_id] = [];
                                }
                                groupedOrders[order.invoice_id].push(order);
                            }
                        });

                        Object.entries(groupedOrders).forEach(([invoiceId, orders]) => {
                            if (orders.length > 0) {
                                ordersHtml += `<h5>Order #${orderNumber}</h5><ul class="list-group mb-3">`;
                                orders.forEach(order => {
                                    let status = order.status || 'pending';
                                    ordersHtml += `
                                        <li class="list-group-item">
                                            Pesanan #${order.id} - ${order.item.name} - Jumlah: ${order.quantity} - Harga: Rp${order.price} - Status: ${status}
                                        </li>
                                    `;
                                });
                                ordersHtml += '</ul>';
                                orderNumber++;
                            }
                        });

                        if (ordersHtml === '') {
                            ordersHtml = '<p>Tidak ada pesanan yang menunggu persetujuan.</p>';
                        }
                        $('#cartContent').html(ordersHtml);
                        $('#cartModal .modal-title').text('Daftar Pesanan');
                        $('#orderBtn').hide();
                        $('#cartModal').modal('show');
                    },
                    error: handleAjaxError
                });
            }

            function updateTotalPrice(input) {
                const quantity = parseInt(input.val());
                const price = parseFloat(input.data('price'));
                const totalPrice = quantity * price;
                const formattedPrice = totalPrice.toLocaleString('id-ID');
                input.closest('.card-body').find('.total-price').text(`Total: Rp${formattedPrice}`);
            }

            function showSweetAlert(message, type = 'success') {
                Swal.fire({
                    title: type.charAt(0).toUpperCase() + type.slice(1),
                    text: message,
                    icon: type,
                    confirmButtonText: 'OK'
                });
            }

            function addToCart(itemId, quantity) {
                $.ajax({
                    url: '/api/cart',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    data: {
                        item_id: itemId,
                        quantity: quantity
                    },
                    success: function(response) {
                        showSweetAlert(response.message, 'success');
                        $(`.item-quantity[data-id="${itemId}"]`).val(0);
                        updateTotalPrice($(`.item-quantity[data-id="${itemId}"]`));
                    },
                    error: handleAjaxError
                });
            }

            function deleteCartItem(cartId) {
                $.ajax({
                    url: `/api/cart/${cartId}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        showSweetAlert(response.message, 'success');
                        loadCart();
                    },
                    error: handleAjaxError
                });
            }

            function createOrder() {
                Swal.fire({
                    title: 'Konfirmasi Pesanan',
                    text: "Apakah Anda yakin ingin membuat pesanan?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Buat Pesanan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Ambil semua ID keranjang yang ada
                        let cartIds = [];
                        $('.delete-cart-item').each(function() {
                            cartIds.push($(this).data('id'));
                        });

                        if (cartIds.length === 0) {
                            Swal.fire('Peringatan', 'Keranjang belanja Anda kosong.', 'warning');
                            return;
                        }

                        $.ajax({
                            url: '/api/order',
                            method: 'POST',
                            headers: {
                                'Authorization': 'Bearer ' + getToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            data: JSON.stringify({ cart_id: cartIds }),
                            success: function(response) {
                                Swal.fire(
                                    'Berhasil!',
                                    'Pesanan berhasil dibuat!',
                                    'success'
                                ).then(() => {
                                    $('#cartModal').modal('hide');
                                    loadItems();
                                    loadCart();
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire(
                                    'Gagal!',
                                    errorMessage,
                                    'error'
                                );
                                handleAjaxError(xhr);
                            }
                        });
                    }
                });
            }

            function deleteSelectedItems() {
                const selectedItems = $('.cart-item-checkbox:checked').map(function() {
                    return $(this).data('id');
                }).get();

                if (selectedItems.length === 0) {
                    showSweetAlert('Pilih item yang ingin dihapus terlebih dahulu.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Anda yakin ingin menghapus ${selectedItems.length} item terpilih dari keranjang?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/cart/delete-multiple',
                            method: 'POST',
                            headers: {
                                'Authorization': 'Bearer ' + getToken(),
                                'Content-Type': 'application/json'
                            },
                            data: JSON.stringify({ cart_ids: selectedItems }),
                            success: function(response) {
                                showSweetAlert(response.message, 'success');
                                loadCart();
                            },
                            error: handleAjaxError
                        });
                    }
                });
            }

            function removeFromCart(cartId, quantity) {
                const maxQuantity = parseInt($(`#remove-quantity-${cartId}`).attr('max'));
                
                if (quantity > maxQuantity) {
                    showSweetAlert(`Jumlah yang dihapus tidak boleh lebih dari ${maxQuantity}`, 'error');
                    return;
                }

                $.ajax({
                    url: `/api/cart/${cartId}`,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + getToken(),
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ quantity: quantity }),
                    success: function(response) {
                        showSweetAlert(response.message, 'success');
                        loadCart();
                    },
                    error: handleAjaxError
                });
            }

            // Event handlers
            $('#viewCart').click(loadCart);
            $('#viewInvoice').click(viewInvoice);
            $('#viewOrders').click(viewOrders);
            $('#logoutBtn').click(function() {
                sessionStorage.removeItem('access_token');
                window.location.href = '/login';
            });
            $('#searchInput').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    loadItems($(this).val());
                }
            });
            $('#orderBtn').click(function() {
                createOrder();
            });

            $(document).on('click', '.increase-quantity, .decrease-quantity', function() {
                const input = $(this).siblings('input.item-quantity');
                const currentValue = parseInt(input.val());
                const maxStock = parseInt(input.attr('max'));
                if ($(this).hasClass('increase-quantity') && currentValue < maxStock) {
                    input.val(currentValue + 1);
                } else if ($(this).hasClass('decrease-quantity') && currentValue > 0) {
                    input.val(currentValue - 1);
                }
                updateTotalPrice(input);
            });

            $(document).on('input', '.item-quantity', function() {
                const input = $(this);
                const currentValue = parseInt(input.val());
                const maxStock = parseInt(input.attr('max'));
                if (currentValue > maxStock) {
                    input.val(maxStock);
                } else if (currentValue < 0 || isNaN(currentValue)) {
                    input.val(0);
                }
                updateTotalPrice(input);
            });

            $(document).on('click', '.add-to-cart', function() {
                const itemId = $(this).data('id');
                const quantity = $(this).closest('.card-body').find('.item-quantity').val();
                if (quantity > 0) {
                    addToCart(itemId, quantity);
                } else {
                    showSweetAlert('Silakan pilih jumlah item yang akan ditambahkan ke keranjang.', 'warning');
                }
            });

            $(document).on('click', '.delete-cart-item', function() {
                const cartId = $(this).data('id');
                deleteCartItem(cartId);
            });

            // Inisialisasi
            if (!getToken()) {
                window.location.href = '/login';
            } else {
                loadItems();
                setInterval(function() {
                    loadItems($('#searchInput').val());
                }, 30000);
            }
        });
    </script>
@endsection

