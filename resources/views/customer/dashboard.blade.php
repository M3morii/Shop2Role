<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Tambahkan link untuk Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Dashboard Customer</h1>
        
        <div class="row mb-4">
            <div class="col">
                <button id="viewCart" class="btn btn-primary">Cek Keranjang</button>
                <button id="viewInvoice" class="btn btn-info text-white">Cek Invoice</button>
                <button id="viewOrders" class="btn btn-success">Cek Pesanan</button>
            </div>
        </div>

        <div id="itemList" class="row"></div>
        
        <div id="cartModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
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

        <div id="messageContainer" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tambahkan script untuk Lightbox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk mendapatkan token dari sessionStorage
            function getToken() {
                return sessionStorage.getItem('access_token');
            }

            // Fungsi untuk menampilkan pesan
            function showMessage(message, type = 'success') {
                $('#messageContainer').html(`<div class="alert alert-${type}">${message}</div>`);
            }

            // Mengambil dan menampilkan daftar item
            function loadItems() {
                $.ajax({
                    url: '/api/items',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        const items = response;
                        let itemsHtml = '';
                        items.forEach(item => {
                            let filesList = '';
                            if (item.files && item.files.length > 0) {
                                filesList = '<div class="file-list">';
                                item.files.forEach(file => {
                                    const fileName = file.file_path.split('/').pop();
                                    filesList += `<a href="/storage/${file.file_path}" data-lightbox="item-${item.id}" data-title="${item.name}">
                                        <img src="/storage/${file.file_path}" alt="${fileName}" class="img-thumbnail" style="width: 50px; height: auto; margin-right: 5px;">
                                    </a>`;
                                });
                                filesList += '</div>';
                            } else {
                                filesList = 'Tidak ada gambar';
                            }
                            
                            itemsHtml += `
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">${item.name}</h5>
                                            <p class="card-text">Description: ${item.description}</p>
                                            <p class="card-text">Harga: Rp${Number(item.sellprice).toLocaleString('id-ID')}</p>
                                            <p class="card-text">Sisa Stok: ${item.stock}</p>
                                            ${filesList}
                                            <div class="input-group mt-2">
                                                <button class="btn btn-outline-secondary decrease-quantity" type="button" data-id="${item.id}">-</button>
                                                <input type="number" class="form-control item-quantity" value="0" min="0" max="${item.remaining_stock}" data-id="${item.id}">
                                                <button class="btn btn-outline-secondary increase-quantity" type="button" data-id="${item.id}">+</button>
                                            </div>
                                            <button class="btn btn-primary add-to-cart mt-2" data-id="${item.id}">Tambah ke Keranjang</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#itemList').html(itemsHtml);
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            window.location.href = '/login';
                        } else {
                            showMessage('Gagal memuat daftar item.', 'danger');
                        }
                    }
                });
            }

            // Tambahkan event listener untuk tombol + dan -
            $(document).on('click', '.increase-quantity', function() {
                const input = $(this).siblings('input.item-quantity');
                input.val(parseInt(input.val()) + 1);
            });

            $(document).on('click', '.decrease-quantity', function() {
                const input = $(this).siblings('input.item-quantity');
                const currentValue = parseInt(input.val());
                if (currentValue > 0) {
                    input.val(currentValue - 1);
                }
            });

            // Modifikasi fungsi untuk menambahkan item ke keranjang
            $(document).on('click', '.add-to-cart', function() {
                const itemId = $(this).data('id');
                const quantity = $(this).closest('.card-body').find('.item-quantity').val();
                
                if (quantity > 0) {
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
                            showMessage(response.message);
                            // Reset quantity input to 0 after adding to cart
                            $(`.item-quantity[data-id="${itemId}"]`).val(0);
                        },
                        error: function(xhr) {
                            showMessage(xhr.responseJSON.message, 'danger');
                        }
                    });
                } else {
                    showMessage('Silakan pilih jumlah item yang akan ditambahkan ke keranjang.', 'warning');
                }
            });

            // Menampilkan keranjang
            $('#viewCart').click(function() {
                $.ajax({
                    url: '/api/cart',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        let cartHtml = '<ul class="list-group">';
                        response.cart.forEach(item => {
                            cartHtml += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${item.item.name} - Rp${item.item.sellprice} x ${item.quantity}
                                    <button class="btn btn-danger btn-sm remove-from-cart" data-id="${item.id}">Hapus</button>
                                </li>
                            `;
                        });
                        cartHtml += '</ul>';
                        $('#cartContent').html(cartHtml);
                        $('#orderBtn').show(); // Tampilkan tombol Order untuk keranjang
                        $('#cartModal').modal('show');
                    },
                    error: function(xhr) {
                        showMessage('Gagal memuat keranjang.', 'danger');
                    }
                });
            });

            // Menghapus item dari keranjang
            $(document).on('click', '.remove-from-cart', function() {
                const cartId = $(this).data('id');
                $.ajax({
                    url: `/api/cart/${cartId}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    data: { quantity: 1 },
                    success: function(response) {
                        showMessage(response.message);
                        $('#viewCart').click();
                    },
                    error: function(xhr) {
                        showMessage(xhr.responseJSON.message, 'danger');
                    }
                });
            });

            // Order (sebelumnya Checkout)
            $('#orderBtn').click(function() {
                const cartIds = $('.remove-from-cart').map(function() {
                    return $(this).data('id');
                }).get();

                $.ajax({
                    url: '/api/order',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    data: { cart_id: cartIds },
                    success: function(response) {
                        showMessage(response.message);
                        $('#cartModal').modal('hide');
                        // Refresh keranjang setelah order berhasil
                        $('#viewCart').click();
                    },
                    error: function(xhr) {
                        showMessage(xhr.responseJSON.message || 'Terjadi kesalahan saat membuat order.', 'danger');
                    }
                });
            });

            // Menampilkan invoice
            $('#viewInvoice').click(function() {
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
                            let statusClass = status === 'approved' ? 'text-success' : 'text-warning';
                            invoicesHtml += `
                                <div class="card mb-3">
                                    <div class="card-header">
                                        Invoice #${invoice.id} - Total: Rp${invoice.total_price} - Tanggal: ${invoice.purchase_date}
                                        <span class="float-end ${statusClass}">Status: ${status}</span>
                                    </div>
                                    <ul class="list-group list-group-flush">
                            `;
                            invoice.orders.forEach(order => {
                                invoicesHtml += `
                                    <li class="list-group-item">
                                        ${order.item_name} - Jumlah: ${order.quantity} - Harga: Rp${order.price}
                                    </li>
                                `;
                            });
                            invoicesHtml += `
                                    </ul>
                                </div>
                            `;
                        });
                        $('#cartContent').html(invoicesHtml);
                        $('#cartModal .modal-title').text('Daftar Invoice');
                        $('#orderBtn').hide(); // Sembunyikan tombol Order
                        $('#cartModal').modal('show');
                    },
                    error: function(xhr) {
                        showMessage('Gagal memuat daftar invoice.', 'danger');
                    }
                });
            });

            // Menampilkan pesanan
            $('#viewOrders').click(function() {
                $.ajax({
                    url: '/api/order',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        let ordersHtml = '';
                        let groupedOrders = {};
                        let orderNumber = 1; // Inisialisasi nomor urut order

                        // Mengelompokkan pesanan berdasarkan invoice_id
                        response.orders.forEach(order => {
                            let status = order.status || 'pending';
                            if (status !== 'approved') {
                                if (!groupedOrders[order.invoice_id]) {
                                    groupedOrders[order.invoice_id] = [];
                                }
                                groupedOrders[order.invoice_id].push(order);
                            }
                        });

                        // Membuat HTML untuk setiap invoice
                        Object.entries(groupedOrders).forEach(([invoiceId, orders]) => {
                            if (orders.length > 0) {
                                ordersHtml += `<h5>Order #${orderNumber}</h5><ul class="list-group mb-3">`; // Menggunakan orderNumber
                                orders.forEach(order => {
                                    let status = order.status || 'pending';
                                    ordersHtml += `
                                        <li class="list-group-item">
                                            Pesanan #${order.id} - ${order.item.name} - Jumlah: ${order.quantity} - Harga: Rp${order.price} - Status: ${status}
                                        </li>
                                    `;
                                });
                                ordersHtml += '</ul>';
                                orderNumber++; // Menambah nomor urut order
                            }
                        });

                        if (ordersHtml === '') {
                            ordersHtml = '<p>Tidak ada pesanan yang menunggu persetujuan.</p>';
                        }
                        $('#cartContent').html(ordersHtml);
                        $('#cartModal .modal-title').text('Daftar Pesanan');
                        $('#orderBtn').hide(); // Sembunyikan tombol Order
                        $('#cartModal').modal('show');
                    },
                    error: function(xhr) {
                        showMessage('Gagal memuat daftar pesanan.', 'danger');
                    }
                });
            });

            // Memuat daftar item saat halaman dimuat
            loadItems();
        });
    </script>
</body>
</html>
