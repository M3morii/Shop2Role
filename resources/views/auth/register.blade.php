<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-dark.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Register</h2>
        <form id="registerForm" autocomplete="off">
            <div class="form-group mb-3">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" required>
            </div>
            <div class="form-group mb-3">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" required>
            </div>
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" required>
            </div>
            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" required>
            </div>
            <div class="form-group mb-3">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <button type="button" class="btn btn-secondary w-100 mt-2" id="toLoginBtn">Login</button>
        </form>
        <div id="registerMessage" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'api/register', // Endpoint API register
                method: 'POST',
                data: {
                    name: $('#name').val(),
                    username: $('#username').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    password_confirmation: $('#password_confirmation').val(),
                },
                success: function(response) {
                    $('#registerMessage').text('Register berhasil! Silakan login.');
                },
                error: function(xhr) {
                    $('#registerMessage').text(xhr.responseJSON.message);
                }
            });
        });

        $('#toLoginBtn').click(function() {
            window.location.href = '/login'; // Redirect ke halaman login
        });
    </script>
</body>
</html>
