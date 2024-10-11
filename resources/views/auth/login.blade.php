<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-dark.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Login</h2>
        <form id="loginForm" autocomplete="off">
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" required>
            </div>
            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <button type="button" class="btn btn-secondary w-100 mt-2" id="toRegisterBtn">Register</button>
        </form>
        <div id="loginMessage" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
    $('#loginForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'api/login', // Endpoint API login
        method: 'POST',
        data: {
            email: $('#email').val(),
            password: $('#password').val(),
        },
        success: function(response) {
            localStorage.setItem('token', response.token);

            if (response.role === 'admin') {
                window.location.href = '/items';
            } else if (response.role === 'customer') {
                window.location.href = '/customer';
            } else {
                $('#loginMessage').text('Role tidak dikenali.');
            }
        },
        error: function(xhr) {
            $('#loginMessage').text(xhr.responseJSON.message);
        }
    });
});
</script>
</body>
</html>
