<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
    
    <!-- Core Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('js/admin/item-management.js') }}"></script>
    <script src="{{ asset('js/admin/order-management.js') }}"></script>
    <script src="{{ asset('js/admin/category-management.js') }}"></script>
    <script src="{{ asset('js/admin/user-management.js') }}"></script>
    <script src="{{ asset('js/admin/search-pagination.js') }}"></script>
    <script src="{{ asset('js/admin/purchase-history.js') }}"></script>

    <!-- Main Script -->
    <script>
    $(document).ready(function() {
        console.log('Document ready');

        // Inisialisasi token
        window.token = sessionStorage.getItem('access_token');
        if (!token) {
            console.log('No token found, redirecting to login');
            window.location.href = '/login';
            return;
        }

        // Event listeners untuk menu navbar
        $('#manageItems').click(function() {
            $('#mainContent').html(`
                <h2 class="text-center mb-4">Kelola Item</h2>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <button class="btn btn-success w-100" id="addbarang">
                            <i class="fas fa-plus"></i> Tambah Barang
                        </button>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari item..." autocomplete="off">
                            <div class="input-group-append">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="categoryFilter">
                            <option value="">Loading categories...</option>
                        </select>
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
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="pagination" class="mt-3"></div>
            `);
            
            // Load kategori untuk filter
            loadCategoriesToFilter();
            // Load items
            loadItems();
        });

        $('#manageOrders').click(function() {
            // Update mainContent dengan struktur tabel orders
            $('#mainContent').html(`
                <div class="container">
                    <h2 class="text-center mb-4">Kelola Pesanan Customer</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <select class="form-control" id="orderStatusFilter">
                                        <option value="all">Semua Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Disetujui</option>
                                        <option value="declined">Ditolak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Customer</th>
                                            <th>Item</th>
                                            <th>Jumlah</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orderTableBody">
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Load orders
            loadCustomerOrders('all');
        });

        $('#manageCategories').click(function() {
            $('#mainContent').html(`
                <h2 class="text-center mb-4">Kelola Kategori</h2>
                <div class="row mb-3">
                    <div class="col-md-8">
                        <button class="btn btn-success" data-toggle="modal" data-target="#categoryModal">
                            Tambah Kategori Baru
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Kategori</th>
                                <th>Jumlah Item</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="categoryList">
                        </tbody>
                    </table>
                </div>
            `);
            loadCategories();
        });

        $('#purchaseHistory').click(function(e) {
            e.preventDefault();
            console.log('Purchase History clicked'); // Debugging

            $('#mainContent').html(`
                <div class="container">
                    <h2 class="text-center mb-4">Riwayat Pembelian</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Item</th>
                                            <th>Jumlah</th>
                                            <th>Tipe</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseHistoryList">
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Panggil fungsi untuk memuat data
            loadPurchaseHistory();
        });

        $('#manageUsers').click(function() {
            $('#mainContent').html(`
                <h2 class="text-center mb-4">Kelola Pengguna</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userList">
                        </tbody>
                    </table>
                </div>
            `);
            loadUsers();
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

        // Inisialisasi halaman
        $('#manageItems').click();
    });
    </script>
</body>
</html>

