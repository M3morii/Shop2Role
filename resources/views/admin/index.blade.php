<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        @include('admin.partials.navbar')
        
        <div class="container mt-4">
            <div id="mainContent"></div>
            @include('admin.modals.item-modals')
            @include('admin.modals.order-modals')
            @include('admin.modals.category-modals')
            @include('admin.modals.user-modals')
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
<script>
    $(document).ready(function() {
        console.log('Document ready');

        var token = sessionStorage.getItem('access_token');
        if (!token) {
            console.log('No token found, redirecting to login');
            window.location.href = '/login';
            return;
        }

        let currentSort = '';
        let currentOrder = 'asc';

        $(document).on('click', '#addbarang', function() {
            try {
                console.log('Tombol Tambah Barang Baru diklik');
                loadCategoriesToDropdown();
                $('#addItemModal').modal('show');
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menampilkan modal'
                });
            }
        });

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
                url: '/api/admin/items',
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
                                    filesList += `
                                        <a href="/storage/${file.file_path}" data-lightbox="item-${item.id}">
                                            <img src="/storage/${file.file_path}" alt="Item Image" 
                                                 class="file-thumbnail" style="width: 50px; height: auto; margin-right: 5px;">
                                        </a>`;
                                });
                                filesList += '</div>';
                            } else {
                                filesList = 'Tidak ada file';
                            }
                            let categoryName = item.category ? item.category.name : 'Tanpa Kategori';
                            rows += `
                                <tr>
                                    <td>${(page - 1) * 10 + index + 1}</td>
                                    <td>${item.name}</td>
                                    <td>${item.description}</td>
                                    <td>${item.stock}</td>
                                    <td>Rp ${Number(item.sellprice).toLocaleString('id-ID')}</td>
                                    <td>${categoryName}</td>
                                    <td>${filesList}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-item" data-id="${item.id}">Edit Item</button>
                                        <button class="btn btn-sm btn-info edit-stock" data-id="${item.id}">Edit Stock</button>
                                        <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">Hapus</button>
                                    </td>
                                </tr>`;
                        });
                        $('#itemTable').html(rows);

                        if (response.last_page > 1) {
                            let pagination = '';
                            for (let i = 1; i <= response.last_page; i++) {
                                pagination += `
                                    <li class="page-item ${response.current_page === i ? 'active' : ''}">
                                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                                    </li>`;
                            }
                            $('#pagination').html(`
                                <ul class="pagination justify-content-center">
                                    ${pagination}
                                </ul>`);

                            $('.page-link').click(function(e) {
                                e.preventDefault();
                                let page = $(this).data('page');
                                loadItems(page, $('#searchInput').val(), currentSort, currentOrder);
                            });
                        }
                    } else {
                        $('#itemTable').html('<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
                        $('#pagination').html('');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    alert('Gagal mengambil data items');
                }
            });
        }

        // Event listener untuk pencarian
        $('#searchInput').on('keyup', function() {
            let searchTerm = $(this).val();
            loadItems(1, searchTerm, currentSort, currentOrder);
        });

        // Event listener untuk tombol edit item
        $(document).on('click', '.edit-item', function() {
            let itemId = $(this).data('id');
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

        // Event listener untuk tombol edit stok
        $(document).on('click', '.edit-stock', function() {
            let itemId = $(this).data('id');
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mengambil data item'
                    });
                }
            });
        });

        // Event listener untuk menyimpan perubahan stok
        $('#saveStockChanges').click(function() {
            var itemId = $('#editStockItemId').val();
            var quantity = parseInt($('#stockQuantity').val());
            var type = $('input[name="stockType"]:checked').val();

            if (!quantity || quantity <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Jumlah harus lebih besar dari 0'
                });
                return;
            }

            Swal.fire({
                title: 'Memperbarui Stok...',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Stok berhasil diperbarui'
                    });
                    $('#editStockModal').modal('hide');
                    $('#stockQuantity').val('');
                    $('input[name="stockType"][value="in"]').prop('checked', true);
                    loadItems();
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal memperbarui stok';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errorMessage
                    });
                }
            });
        });

        // Event listener untuk tombol hapus item
        $(document).on('click', '.delete-item', function() {
            let itemId = $(this).data('id');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Item yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/admin/items/' + itemId,
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Item berhasil dihapus.',
                                'success'
                            );
                            loadItems();
                        },
                        error: function() {
                            Swal.fire(
                                'Gagal!',
                                'Gagal menghapus item.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Fungsi untuk menampilkan pagination
        function renderPagination(response) {
            let paginationHtml = '';
            if (response.last_page > 1) {
                paginationHtml = '<nav><ul class="pagination">';
                for (let i = 1; i <= response.last_page; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === response.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                }
                paginationHtml += '</ul></nav>';
            }
            $('#pagination').html(paginationHtml);
        }

        // Event listener untuk tombol tambah barang
        $('#addbarang').click(function() {
            try {
                console.log('Tombol Tambah Barang Baru diklik');
                loadCategoriesToDropdown();
                $('#addItemModal').modal('show');
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menampilkan modal'
                });
            }
        });

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

        $('#saveNewItem').click(function() {
            var formData = new FormData($('#addItemForm')[0]);
            
            Swal.fire({
                title: 'Menyimpan...',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Item baru berhasil ditambahkan'
                    });
                    $('#addItemModal').modal('hide');
                    $('#addItemForm')[0].reset();
                    loadItems();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal menambahkan item baru: ' + xhr.responseJSON.message
                    });
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
            Swal.fire({
                title: 'Apakah Anda yakin ingin keluar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    sessionStorage.removeItem('access_token');
                    window.location.href = '/login';
                }
            });
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
                        <button class="btn btn-success mr-2" id="addbarang">Tambah Barang Baru</button>
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
                                <th data-sort="category">Category</th>
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
                            </tr>`;
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
                            </tr>`;
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

        // Fungsi untuk load kategori tanpa forEach
        function loadCategoriesToDropdown() {
            $.ajax({
                url: '/api/admin/categories',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    let options = '<option value="">Pilih Kategori</option>';
                    for(let i = 0; i < response.length; i++) {
                        options += `<option value="${response[i].id}">${response[i].name}</option>`;
                    }
                    $('#addItemCategory').html(options);
                },
                error: function(xhr) {
                    console.error('Gagal mengambil data kategori:', xhr);
                    alert('Gagal mengambil data kategori');
                }
            });
        }

        // Inisialisasi halaman
        $('#manageItems').click();
    });
    </script>
</html>
