<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Customer Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/customer-dashboard.css') }}">
    @yield('additional_css')
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">@yield('page_title', 'Customer Dashboard')</h1>
        
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

        @yield('content')
        
        <div id="messageContainer" class="mt-3"></div>
    </div>

    @yield('modals')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#logoutBtn').click(function() {
                sessionStorage.removeItem('access_token');
                window.location.href = '/login';
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
