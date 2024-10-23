<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
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
    </style>
    @yield('additional_css')
</head>
<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-10">
                <h2 class="text-center">@yield('page_title', 'Admin Dashboard')</h2>
            </div>
            <div class="col-md-2">
                <button id="logoutButton" class="btn btn-danger">Logout</button>
            </div>
        </div>

        @yield('content')
    </div>

    @yield('modals')

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#logoutButton').click(function() {
                sessionStorage.removeItem('access_token');
                window.location.href = '/login';
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
