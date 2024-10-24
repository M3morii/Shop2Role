<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#" id="manageItems">Kelola Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="manageOrders">Kelola Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="manageCategories">Kelola Kategori</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="manageUsers">Kelola Pengguna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="viewPurchaseHistory">Riwayat Pembelian</a>
                </li>
            </ul>
            <button id="logoutButton" class="btn btn-outline-light">Logout</button>
        </div>
    </nav>

        <div class="container mt-4">
            <div id="content">
                <!-- Content will be loaded here dynamically -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Your JavaScript code to handle API calls and update the content
    </script>
</body>
</html>

    <script>
        // Your JavaScript code to handle API calls and update the content
    </script>
</body>
</html>

    <style>
        body {
            background-color: #f8f9fa;
            color: #000000;
        }
        .dashboard-card {
            height: 200px;
            overflow-y: auto;
        }
        .dashboard-card .card-body {
            padding: 1rem;
        }
        .dashboard-card h5 {
            margin-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.5rem;
        }
        .dashboard-card .list-group-item {
            padding: 0.5rem 1rem;
        }
        .table {
            background-color: #ffffff;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead th {
            background-color: #6c757d;
            color: #ffffff;
        }
        .btn-custom {
            background-color: #007bff;
            color: #ffffff;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container mt-5">
        <div id="mainContent">
            <!-- Konten utama akan dimuat di sini -->
        </div>
    </div>

    <!-- Modal Edit Stock -->
    <div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStockModalLabel">Edit Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editStockForm">
                        <input type="hidden" id="editStockItemId">
                        <div class="form-group">
                            <label>Current Stock: <span id="currentStock"></span></label>
                        </div>
                        <div class="form-group">
                            <label>Stock Type:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="stockType" id="stockTypeIn" value="in" checked>
                                <label class="form-check-label" for="stockTypeIn">
                                    In (Tambah stock)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="stockType" id="stockTypeOut" value="out">
                                <label class="form-check-label" for="stockTypeOut">
                                    Out (Kurangi stock)
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="stockQuantity">Quantity:</label>
                            <input type="number" class="form-control" id="stockQuantity" required min="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStockChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Item -->
    <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm">
                        <input type="hidden" id="editItemId">
                        <div class="form-group">
                            <label for="editItemName">Name:</label>
                            <input type="text" class="form-control" id="editItemName" required>
                        </div>
                        <div class="form-group">
                            <label for="editItemDescription">Description:</label>
                            <textarea class="form-control" id="editItemDescription" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editItemSellPrice">Sell Price:</label>
                            <input type="number" class="form-control" id="editItemSellPrice" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveItemChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Item Baru -->
    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Tambah Barang Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="addItemName">Nama:</label>
                            <input type="text" class="form-control" id="addItemName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="addItemDescription">Deskripsi:</label>
                            <textarea class="form-control" id="addItemDescription" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="addItemSellPrice">Harga Jual:</label>
                            <input type="number" class="form-control" id="addItemSellPrice" name="sellprice" required>
                        </div>
                        <div class="form-group">
                            <label for="addItemStock">stock Awal:</label>
                            <input type="number" class="form-control" id="addItemStock" name="stock" required>
                        </div>
                        <div class="form-group">
                            <label for="addItemFiles">File (Gambar/Video):</label>
                            <input type="file" class="form-control-file" id="addItemFiles" name="files[]" multiple>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveNewItem">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pesanan Customer dan Riwayat Pembelian -->
    <div class="modal fade" id="customerOrdersModal" tabindex="-1" role="dialog" aria-labelledby="customerOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerOrdersModalLabel">Pesanan Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="orderStatusFilterContainer" class="form-group">
                        <label for="orderStatusFilter">Filter by Status:</label>
                        <select id="orderStatusFilter" class="form-control">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                    <div id="customerOrdersContent">
                        <!-- Orders will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kategori -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Kelola Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="categoryId">
                        <div class="form-group">
                            <label for="categoryName">Nama Kategori:</label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                    </form>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="categoryList">
                            <!-- Daftar kategori akan dimuat di sini -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveCategoryChanges">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pengguna -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Kelola Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Peran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userList">
                            <!-- Daftar pengguna akan dimuat di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal edit user -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editUserId">
                        <div class="form-group">
                            <label for="editUserName">Nama:</label>
                            <input type="text" class="form-control" id="editUserName" required>
                        </div>
                        <div class="form-group">
                            <label for="editUserEmail">Email:</label>
                            <input type="email" class="form-control" id="editUserEmail" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveUserChanges">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        var token = sessionStorage.getItem('access_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        let currentSort = '';
        let currentOrder = 'asc';

        function loadItems(page = 1, search = '', sort = '', order = '') {
            let data = { 
                page: page,
                search: search,
                per_page: 10
            };

            if (sort && order) {
                data.sort = sort;
                data.order = order.toLowerCase();
            }

            $.ajax({
                url: '/api/items',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: data,
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        let rows = '';
                        $.each(response.data, function(index, item) {
                            let filesList = '';
                            if (item.files && item.files.length > 0) {
                                filesList = '<div class="file-list">';
                                item.files.forEach(file => {
                                    const fileName = file.file_path.split('/').pop();
                                    filesList += `<img src="/storage/${file.file_path}" alt="${fileName}" class="file-thumbnail" style="width: 50px; height: auto; margin-right: 5px;">`;
                                });
                                filesList += '</div>';
                            } else {
                                filesList = 'Tidak ada file';
                            }
                            rows += `
                                <tr>
                                    <td>${(page - 1) * 10 + index + 1}</td>
                                    <td>${item.name}</td>
                                    <td>${item.description}</td>
                                    <td>${item.stock}</td>
                                    <td>Rp ${Number(item.sellprice).toLocaleString('id-ID')}</td>
                                    <td>${filesList}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-item" data-id="${item.id}">Edit Item</button>
                                        <button class="btn btn-sm btn-info edit-stock" data-id="${item.id}">Edit Stock</button>
                                        <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">Hapus</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#itemTable').html(rows);

                        // Event listeners untuk tombol-tombol
                        $('.edit-item').click(function() {
                            var itemId = $(this).data('id');
                            $.ajax({
                                url: '/api/admin/items/' + itemId,
                                method: 'GET',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                success: function(item) {
                                    $('#editItemId').val(item.id);
                                    $('#editItemName').val(item.name);
                                    $('#editItemDescription').val(item.description);
                                    $('#editItemSellPrice').val(Number(item.sellprice).toLocaleString('id-ID'));
                                    $('#editItemModal').modal('show');
                                },
                                error: function() {
                                    alert('Gagal mengambil data item');
                                }
                            });
                        });

                        $('.edit-stock').click(function() {
                            var itemId = $(this).data('id');
                            $('#editStockItemId').val(itemId);
                            
                            $.ajax({
                                url: '/api/admin/items/' + itemId,
                                method: 'GET',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                success: function(item) {
                                    $('#currentStock').text(item.stock);
                                    $('#editStockModal').modal('show');
                                },
                                error: function() {
                                    alert('Gagal mengambil data item');
                                }
                            });
                        });

                        $('.delete-item').click(function() {
                            var itemId = $(this).data('id');
                            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                                $.ajax({
                                    url: '/api/admin/items/' + itemId,
                                    method: 'DELETE',
                                    headers: {
                                        'Authorization': 'Bearer ' + token
                                    },
                                    success: function(response) {
                                        alert('Item berhasil dihapus');
                                        loadItems();
                                    },
                                    error: function(xhr) {
                                        alert('Gagal menghapus item');
                                    }
                                });
                            }
                        });
                    } else {
                        $('#itemTable').html('<tr><td colspan="7" class="text-center">Tidak ada item ditemukan</td></tr>');
                    }

                    // Tambahkan tombol pagination
                    let paginationHtml = '';
                    if (response.last_page > 1) {
                        paginationHtml += `<nav><ul class="pagination">`;
                        for (let i = 1; i <= response.last_page; i++) {
                            paginationHtml += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>`;
                        }
                        paginationHtml += `</ul></nav>`;
                    }
                    $('#pagination').html(paginationHtml);

                    console.log('Pagination info:', {
                        currentPage: response.current_page,
                        lastPage: response.last_page,
                        total: response.total,
                        perPage: response.per_page
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    $('#itemTable').html('<tr><td colspan="7" class="text-center">Error mengambil data: ' + xhr.responseText + '</td></tr>');
                }
            });

            console.log('Items loaded successfully');
            console.log('Attaching click event to #addNewItem');
            $('#addNewItem').click(function() {
                try {
                    console.log('Tombol Tambah Barang Baru diklik');
                    $('#addItemModal').modal('show');
                } catch (error) {
                    console.error('Error showing modal:', error);
                }
            });
        }

        function loadCustomerOrders(status = 'all') {
            $.ajax({
                url: '/api/admin/orders',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    let ordersHtml = '';
                    if (response.orders && response.orders.length > 0) {
                        let groupedOrders = {};
                        response.orders.forEach(function(order) {
                            if (!groupedOrders[order.invoice_id]) {
                                groupedOrders[order.invoice_id] = {
                                    id: order.invoice_id,
                                    status: order.status,
                                    items: [],
                                    total_price: 0
                                };
                            }
                            let itemPrice = parseFloat(order.price);
                            if (isNaN(itemPrice)) {
                                itemPrice = 0;
                            }
                            groupedOrders[order.invoice_id].items.push({
                                id: order.id,
                                name: order.item.name,
                                quantity: order.quantity,
                                price: itemPrice
                            });
                            groupedOrders[order.invoice_id].total_price += itemPrice * order.quantity;
                        });

                        Object.values(groupedOrders).forEach(function(invoice) {
                            if (status === 'all' || invoice.status === status) {
                                let approveButton = `<button class="btn btn-success approve-order" data-id="${invoice.id}">Approve</button>`;
                                let declineButton = `<button class="btn btn-danger decline-order" data-id="${invoice.id}">Decline</button>`;
                                
                                if (invoice.status === 'approved') {
                                    approveButton = `<button class="btn btn-success" disabled>Approved</button>`;
                                    declineButton = '';
                                } else if (invoice.status === 'declined') {
                                    approveButton = '';
                                    declineButton = `<button class="btn btn-danger" disabled>Declined</button>`;
                                }

                                ordersHtml += `
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            Invoice #${invoice.id} - Status: ${invoice.status}
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Items:</h5>
                                            <ul class="list-group">
                                `;
                                invoice.items.forEach(function(item) {
                                    ordersHtml += `
                                        <li class="list-group-item">
                                            ${item.name} - Jumlah: ${item.quantity} - Harga: Rp${item.price.toLocaleString('id-ID')}
                                        </li>
                                    `;
                                });
                                ordersHtml += `
                                            </ul>
                                            <p class="mt-3">Total Harga: Rp${invoice.total_price.toLocaleString('id-ID')}</p>
                                            ${approveButton}
                                            ${declineButton}
                                        </div>
                                    </div>
                                `;
                            }
                        });
                    }
                    
                    if (ordersHtml === '') {
                        ordersHtml = '<p>Tidak ada pesanan yang sesuai dengan filter saat ini.</p>';
                    }
                    
                    $('#customerOrdersContent').html(ordersHtml);
                },
                error: function(xhr) {
                    alert('Gagal memuat pesanan customer');
                }
            });
        }

        // Event listeners
        $('th[data-sort]').click(function() {
            const sort = $(this).data('sort');
            if (sort === currentSort) {
                currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = sort;
                currentOrder = 'asc';
            }
            loadItems(1, $('#searchInput').val(), currentSort, currentOrder);
        });

        $('#saveStockChanges').click(function() {
            var itemId = $('#editStockItemId').val();
            var quantity = $('#stockQuantity').val();
            var type = $('input[name="stockType"]:checked').val();

            $.ajax({
                url: '/api/admin/stocks',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: {
                    item_id: itemId,
                    quantity: quantity,
                    type: type
                },
                success: function(response) {
                    alert('stock berhasil diperbarui');
                    $('#editStockModal').modal('hide');
                    loadItems();
                },
                error: function(xhr) {
                    alert('Gagal memperbarui stock: ' + xhr.responseJSON.message);
                }
            });
        });

        $('#saveItemChanges').click(function() {
            var itemId = $('#editItemId').val();
            var itemData = {
                name: $('#editItemName').val(),
                description: $('#editItemDescription').val(),
                sellprice: $('#editItemSellPrice').val().replace(/\D/g, '')
            };
            $.ajax({
                url: '/api/admin/items/' + itemId,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: itemData,
                success: function(response) {
                    alert('Item berhasil diperbarui');
                    $('#editItemModal').modal('hide');
                    loadItems();
                },
                error: function(xhr) {
                    alert('Gagal memperbarui item');
                }
            });
        });

        $('#addNewItem').click(function() {
        console.log('Tombol Tambah Barang Baru diklik');
            $('#addItemModal').modal('show');
        });

        $('#saveNewItem').click(function() {
            var formData = new FormData($('#addItemForm')[0]);
            
            $.ajax({
                url: '/api/admin/items',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert('Item baru berhasil ditambahkan');
                    $('#addItemModal').modal('hide');
                    $('#addItemForm')[0].reset();
                    loadItems();
                },
                error: function(xhr) {
                    alert('Gagal menambahkan item baru: ' + xhr.responseJSON.message);
                }
            });
        });

        $('#viewCustomerOrders').click(function() {
            $('#orderStatusFilterContainer').show(); // Tampilkan filter status
            $('#customerOrdersModalLabel').text('Pesanan Customer'); // Ubah judul modal
            $('#orderStatusFilter').val('all');
            loadCustomerOrders('all');
            $('#customerOrdersModal').modal('show');
        });

        $(document).on('click', '.approve-order', function() {
            var invoiceId = $(this).data('id');
            $.ajax({
                url: '/api/admin/orders/' + invoiceId + '/approve',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    alert(response.message);
                    loadCustomerOrders();
                },
                error: function(xhr) {
                    alert('Gagal menyetujui pesanan: ' + xhr.responseJSON.message);
                }
            });
        });

        $(document).on('click', '.decline-order', function() {
            var invoiceId = $(this).data('id');
            $.ajax({
                url: '/api/admin/orders/' + invoiceId + '/decline',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    alert(response.message);
                    loadCustomerOrders();
                },
                error: function(xhr) {
                    alert('Gagal menolak pesanan: ' + xhr.responseJSON.message);
                }
            });
        });

        $('#searchInput').on('keyup', function() {
            const searchTerm = $(this).val();
            loadItems(1, searchTerm);
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            const search = $('#searchInput').val();
            loadItems(page, search, currentSort, currentOrder);
        });

        $('#logoutButton').click(function() {
            sessionStorage.removeItem('access_token');
            window.location.href = '/login';
        });

        function loadPurchaseHistory() {
            $.ajax({
                url: '/api/admin/purchase-history',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    let historyHtml = '<h2 class="text-center mb-4">Riwayat Pembelian</h2>';
                    historyHtml += '<table class="table">';
                    historyHtml += '<thead><tr><th>Item</th><th>Jumlah</th><th>Tipe</th><th>Tanggal</th></tr></thead><tbody>';
                    
                    response.purchase_history.forEach(function(item) {
                        historyHtml += `<tr>
                            <td>${item.item_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.type === 'in' ? 'Masuk' : 'Keluar'}</td>
                            <td>${item.date}</td>
                        </tr>`;
                    });
                    
                    historyHtml += '</tbody></table>';
                    $('#mainContent').html(historyHtml);
                },
                error: function(xhr) {
                    alert('Gagal memuat riwayat pembelian');
                }
            });
        }

        // Event listeners untuk menu navbar
        $('#manageItems').click(function() {
            loadItems();
            $('#mainContent').html(`
                <h2 class="text-center mb-4">Kelola Item</h2>
                <div class="row mb-3">
                    <div class="col-md-8">
                        <button class="btn btn-success mr-2" id="addNewItem">Tambah Barang Baru</button>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari item...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th data-sort="name">Name</th>
                                <th data-sort="description">Description</th>
                                <th data-sort="stock">Stock</th>
                                <th data-sort="sellprice">Sell Price</th>
                                <th>Files</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="itemTable">
                            <!-- Item rows will be added here -->
                        </tbody>
                    </table>
                </div>
                <div id="pagination" class="mt-3"></div>
            `);
        });

        $('#manageOrders').click(function() {
            $('#orderStatusFilterContainer').show();
            $('#customerOrdersModalLabel').text('Kelola Pesanan');
            $('#orderStatusFilter').val('all');
            loadCustomerOrders('all');
            $('#customerOrdersModal').modal('show');
        });

        $('#manageCategories').click(function() {
            loadCategories();
            $('#categoryModal').modal('show');
        });

        $('#manageUsers').click(function() {
            loadUsers();
            $('#userModal').modal('show');
        });

        $('#viewPurchaseHistory').click(function() {
            loadPurchaseHistory();
        });

        $('#orderStatusFilter').change(function() {
            const selectedStatus = $(this).val();
            loadCustomerOrders(selectedStatus);
        });

        function loadCategories() {
            $.ajax({
                url: '/api/admin/categories',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    let categoriesHtml = '';
                    response.categories.forEach(function(category) {
                        categoriesHtml += `
                            <tr>
                                <td>${category.name}</td>
                                <td>${category.item_count} item</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-category" data-id="${category.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-category" data-id="${category.id}">Hapus</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#categoryList').html(categoriesHtml);
                },
                error: function(xhr) {
                    alert('Gagal memuat kategori');
                }
            });
        }

        function loadUsers() {
            $.ajax({
                url: '/api/admin/users',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    let usersHtml = '';
                    response.users.forEach(function(user) {
                        let roleText = user.role_id === 1 ? 'Admin' : 'Customer';
                        let changeRoleText = user.role_id === 1 ? 'Jadikan Customer' : 'Jadikan Admin';
                        usersHtml += `
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${roleText}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-user" data-id="${user.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-user" data-id="${user.id}">Hapus</button>
                                    <button class="btn btn-sm btn-info change-role" data-id="${user.id}" data-role-id="${user.role_id}">
                                        ${changeRoleText}
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#userList').html(usersHtml);
                },
                error: function(xhr) {
                    alert('Gagal memuat pengguna');
                }
            });
        }

        $(document).on('click', '.edit-user', function() {
            let userId = $(this).data('id');
            $.ajax({
                url: `/api/admin/users/${userId}`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    $('#editUserId').val(response.user.id);
                    $('#editUserName').val(response.user.name);
                    $('#editUserEmail').val(response.user.email);
                    $('#editUserModal').modal('show');
                },
                error: function(xhr) {
                    alert('Gagal mengambil data pengguna');
                }
            });
        });

        $('#saveUserChanges').click(function() {
            let userId = $('#editUserId').val();
            let userData = {
                name: $('#editUserName').val(),
                email: $('#editUserEmail').val(),
                _method: 'PUT' // Tambahkan ini
            };
            $.ajax({
                url: `/api/admin/users/${userId}`,
                method: 'POST', // Ubah ini menjadi POST
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Tambahkan ini jika belum ada
                },
                data: userData,
                success: function(response) {
                    alert('Pengguna berhasil diperbarui');
                    $('#editUserModal').modal('hide');
                    loadUsers();
                },
                error: function(xhr) {
                    alert('Gagal memperbarui pengguna: ' + xhr.responseJSON.message);
                }
            });
        });

        $(document).on('click', '.delete-user', function() {
            if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                let userId = $(this).data('id');
                $.ajax({
                    url: `/api/admin/users/${userId}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        alert('Pengguna berhasil dihapus');
                        loadUsers();
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus pengguna');
                    }
                });
            }
        });

        $(document).on('click', '.change-role', function() {
            let userId = $(this).data('id');
            let currentRoleId = $(this).data('role-id');
            let newRoleId = currentRoleId === 1 ? 2 : 1;
            
            $.ajax({
                url: `/api/admin/users/${userId}/change-role`,
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: { role_id: newRoleId },
                success: function(response) {
                    alert('Peran pengguna berhasil diubah');
                    loadUsers();
                },
                error: function(xhr) {
                    alert('Gagal mengubah peran pengguna');
                }
            });
        });

        $('#saveCategoryChanges').click(function() {
            let categoryId = $('#categoryId').val();
            let categoryName = $('#categoryName').val();
            let url = categoryId ? `/api/admin/categories/${categoryId}` : '/api/admin/categories';
            let method = categoryId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: { name: categoryName },
                success: function(response) {
                    alert('Kategori berhasil disimpan');
                    loadCategories();
                    $('#categoryModal').modal('hide');
                    $('#categoryId').val('');
                    $('#categoryName').val('');
                },
                error: function(xhr) {
                    alert('Gagal menyimpan kategori');
                }
            });
        });

        $(document).on('click', '.edit-category', function() {
            let categoryId = $(this).data('id');
            $.ajax({
                url: `/api/admin/categories/${categoryId}`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    $('#categoryId').val(response.category.id);
                    $('#categoryName').val(response.category.name);
                },
                error: function(xhr) {
                    alert('Gagal mengambil data kategori');
                }
            });
        });

        $(document).on('click', '.delete-category', function() {
            if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                let categoryId = $(this).data('id');
                $.ajax({
                    url: `/api/admin/categories/${categoryId}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        alert('Kategori berhasil dihapus');
                        loadCategories();
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus kategori');
                    }
                });
            }
        });

        // Inisialisasi halaman
        $('#manageItems').click();
    });
    </script>
</body>
</html>
