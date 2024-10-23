<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shop App')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .container {
            max-width: 1140px;
        }
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,.15);
            transform: translateY(-2px);
        }
        .card-body {
            padding: 0.75rem;
        }
        .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        .card-text {
            font-size: 0.8rem;
        }
        .btn-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        .navbar-nav .nav-item .nav-link {
            position: relative;
            color: rgba(255,255,255,.8);
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-item .nav-link:hover,
        .navbar-nav .nav-item .nav-link.active {
            color: #ffffff;
        }
        .navbar-nav .nav-item .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #ffffff;
            transition: all 0.3s ease;
        }
        .navbar-nav .nav-item .nav-link:hover::after,
        .navbar-nav .nav-item .nav-link.active::after {
            width: 100%;
            left: 0;
        }
        .card .btn-block {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
        }
        .card .input-group {
            margin-bottom: 0.5rem;
        }
        .card .total-price {
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Shop App</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    @yield('navbar-items')
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="logoutButton">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#logoutButton').click(function() {
                sessionStorage.removeItem('access_token');
                window.location.href = '/login';
            });

            // Menambahkan kelas active pada item navbar yang sesuai dengan halaman saat ini
            $('.navbar-nav .nav-link').each(function() {
                if ($(this).attr('href') === window.location.pathname) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
