<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .card {
            transition: transform 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .badge-stock {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
        }
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        .card {
            max-width: 250px;
            margin-bottom: 20px;
        }
        .card-img-top {
            height: 150px;
            object-fit: cover;
        }
        .card-body {
            padding: 0.75rem;
        }
        .card-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        .price {
            font-size: 1rem;
            font-weight: bold;
            color: #28a745;
        }
        .badge-stock {
            font-size: 0.75rem;
        }
        .input-group {
            margin-top: 0.5rem;
        }
        .input-group .btn {
            padding: 0.25rem 0.5rem;
        }
        .item-quantity {
            width: 50px;
            text-align: center;
        }
        .total-price {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        .add-to-cart {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Dashboard Customer</h1>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <button id="viewCart" class="btn btn-primary">Cek Keranjang</button>
                <button id="viewInvoice" class="btn btn-info text-white">Cek Invoice</button>
                <button id="viewOrders" class="btn btn-success">Cek Pesanan</button>
                <button id="logoutBtn" class="btn btn-danger">Logout</button>
            </div>
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari item...">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk mendapatkan token dari sessionStorage
            function getToken() {
                return sessionStorage.getItem('access_token');
            }

            function showMessage(message, type = 'success') {
                $('#messageContainer').html(`<div class="alert alert-${type}">${message}</div>`);
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
                                            <h5 class="card-title">${item.name}</h5>
                                            <p class="card-text text-muted">${item.description}</p>
                                            <p class="price">Rp${Number(item.sellprice).toLocaleString('id-ID')}</p>
                                            <span class="badge bg-info badge-stock">Stok: ${item.stock}</span>
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

            $(document).on('click', '.increase-quantity', function() {
                const input = $(this).siblings('input.item-quantity');
                const currentValue = parseInt(input.val());
                const maxStock = parseInt(input.attr('max'));
                if (currentValue < maxStock) {
                    input.val(currentValue + 1);
                }
            });

            $(document).on('click', '.decrease-quantity', function() {
                const input = $(this).siblings('input.item-quantity');
                const currentValue = parseInt(input.val());
                if (currentValue > 0) {
                    input.val(currentValue - 1);
                }
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
            });

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
                            $(`.item-quantity[data-id="${itemId}"]`).val(0);
                            updateTotalPrice($(`.item-quantity[data-id="${itemId}"]`));
                        },
                        error: function(xhr) {
                            showMessage(xhr.responseJSON.message || 'Terjadi kesalahan saat menambahkan item ke keranjang.', 'danger');
                        }
                    });
                } else {
                    showMessage('Silakan pilih jumlah item yang akan ditambahkan ke keranjang.', 'warning');
                }
            });

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

            $('#logoutBtn').click(function() {
                sessionStorage.removeItem('access_token');
                window.location.href = '/login';
            });

            function performSearch() {
                const searchTerm = $('#searchInput').val();
                loadItems(searchTerm);
            }

            $('#searchInput').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            // Fungsi untuk refresh otomatis
            function autoRefresh() {
                loadItems($('#searchInput').val());
            }

            // Set interval untuk refresh otomatis setiap 30 detik
            setInterval(autoRefresh, 30000);

            // Panggil loadItems saat halaman dimuat
            loadItems();

            // Fungsi untuk mengupdate total harga
            function updateTotalPrice(input) {
                const quantity = parseInt(input.val());
                const price = parseFloat(input.data('price'));
                const totalPrice = quantity * price;
                const formattedPrice = totalPrice.toLocaleString('id-ID');
                input.closest('.card-body').find('.total-price').text(`Total: Rp${formattedPrice}`);
            }

            // Event handler untuk perubahan jumlah item
            $(document).on('input', '.item-quantity', function() {
                updateTotalPrice($(this));
            });

            $(document).on('click', '.increase-quantity, .decrease-quantity', function() {
                const input = $(this).siblings('input.item-quantity');
                updateTotalPrice(input);
            });

            // Tambahkan fungsi baru untuk menampilkan keranjang
            $('#viewCart').click(function() {
                $.ajax({
                    url: '/api/cart',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        console.log('Response dari server:', response); // Untuk debugging

                        let cartHtml = '<table class="table"><thead><tr><th>Item</th><th>Jumlah</th><th>Harga</th><th>Total</th></tr></thead><tbody>';
                        let totalCart = 0;

                        if (Array.isArray(response) && response.length > 0) {
                            response.forEach(item => {
                                const itemTotal = item.quantity * item.price;
                                totalCart += itemTotal;
                                cartHtml += `
                                    <tr>
                                        <td>${item.item_name}</td>
                                        <td>${item.quantity}</td>
                                        <td>Rp${Number(item.price).toLocaleString('id-ID')}</td>
                                        <td>Rp${Number(itemTotal).toLocaleString('id-ID')}</td>
                                    </tr>
                                `;
                            });
                            cartHtml += `</tbody><tfoot><tr><td colspan="3" class="text-end"><strong>Total Keranjang:</strong></td><td><strong>Rp${Number(totalCart).toLocaleString('id-ID')}</strong></td></tr></tfoot></table>`;
                        } else {
                            cartHtml = '<p>Keranjang belanja Anda kosong.</p>';
                        }
                        
                        $('#cartContent').html(cartHtml);
                        $('#cartModal .modal-title').text('Keranjang Belanja');
                        $('#orderBtn').toggle(totalCart > 0);
                        $('#cartModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showMessage('Gagal memuat keranjang belanja.', 'danger');
                    }
                });
            });

            // Tambahkan fungsi untuk tombol Order
            $('#orderBtn').click(function() {
                $.ajax({
                    url: '/api/order',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        showMessage('Pesanan berhasil dibuat!', 'success');
                        $('#cartModal').modal('hide');
                        loadItems(); // Refresh daftar item
                    },
                    error: function(xhr) {
                        showMessage('Gagal membuat pesanan.', 'danger');
                    }
                });
            });

            function loadCart() {
                $.ajax({
                    url: '/api/cart',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        console.log('Response dari server:', response);

                        let cartHtml = '<table class="table"><thead><tr><th>Item</th><th>Jumlah</th><th>Harga</th><th>Total</th><th>Aksi</th></tr></thead><tbody>';
                        let totalCart = 0;

                        if (Array.isArray(response) && response.length > 0) {
                            response.forEach(item => {
                                const itemTotal = item.quantity * item.price;
                                totalCart += itemTotal;
                                cartHtml += `
                                    <tr>
                                        <td>${item.item_name}</td>
                                        <td>${item.quantity}</td>
                                        <td>Rp${Number(item.price).toLocaleString('id-ID')}</td>
                                        <td>Rp${Number(itemTotal).toLocaleString('id-ID')}</td>
                                        <td>
                                            <button class="btn btn-danger btn-sm delete-cart-item" data-id="${item.id}">Hapus</button>
                                        </td>
                                    </tr>
                                `;
                            });
                            cartHtml += `</tbody><tfoot><tr><td colspan="3" class="text-end"><strong>Total Keranjang:</strong></td><td colspan="2"><strong>Rp${Number(totalCart).toLocaleString('id-ID')}</strong></td></tr></tfoot></table>`;
                        } else {
                            cartHtml = '<p>Keranjang belanja Anda kosong.</p>';
                        }
                        
                        $('#cartContent').html(cartHtml);
                        $('#cartModal .modal-title').text('Keranjang Belanja');
                        $('#orderBtn').toggle(totalCart > 0);
                        $('#cartModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showMessage('Gagal memuat keranjang belanja.', 'danger');
                    }
                });
            }

            $(document).on('click', '.delete-cart-item', function() {
                const cartId = $(this).data('id');
                
                $.ajax({
                    url: `/api/cart/${cartId}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        showMessage(response.message);
                        loadCart();
                    },
                    error: function(xhr) {
                        showMessage('Gagal menghapus item dari keranjang.', 'danger');
                    }
                });
            });

            $('#viewCart').click(loadCart);
        });
    </script>
</body>
</html>
