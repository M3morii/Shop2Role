<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand text-white" href="#">
                    <i class="bi bi-shop"></i> Dashboard Customer
                </a>
                <div class="d-flex">
                    <button id="viewCart" class="btn btn-outline-primary me-2">
                        <i class="bi bi-cart"></i> Cek Keranjang
                    </button>
                    <button id="viewInvoice" class="btn btn-outline-info me-2">
                        <i class="bi bi-receipt"></i> Cek Invoice
                    </button>
                    <button id="viewOrders" class="btn btn-outline-success me-2">
                        <i class="bi bi-bag"></i> Cek Pesanan
                    </button>
                    <button id="logoutBtn" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </div>
            </div>
        </nav>
        
        <div class="container mt-4">
            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari item..." autocomplete="off">
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="categoryFilter">
                        <option value="">Semua Kategori</option>
                    </select>
                </div>
            </div>

            <!-- Item List -->
            <div id="itemList" class="row"></div>
        </div>

        <!-- Modals -->
        @include('customer.modals.cart-modal')
        @include('customer.modals.invoice-modal')
        @include('customer.modals.order-modal')
    </div>

    <!-- Core Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('js/customer/item-management.js') }}"></script>
    <script src="{{ asset('js/customer/cart-management.js') }}"></script>
    <script src="{{ asset('js/customer/order-management.js') }}"></script>
    <script src="{{ asset('js/customer/invoice-management.js') }}"></script>
</body>
</html>