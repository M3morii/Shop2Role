<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- CDN Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CDN jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div id="errorMessage" class="alert alert-danger mt-3 d-none"></div>
                        <div class="text-center mt-3">
                            <button class="btn btn-secondary w-100" id="registerButton">Register</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#registerButton').click(function(event) {
            event.preventDefault();
            window.location.href = '/register';
        });

        $('#loginForm').submit(function(event) {
            event.preventDefault();
            $.ajax({
                url: '/api/login',
                type: 'POST',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val(),
                },
                success: function(response) {
                    if (response && response.access_token) {
                        var token = response.access_token;
                        sessionStorage.setItem('access_token', token);
                        
                        // Redirect berdasarkan role_id
                        switch(response.user.role_id) {
                            case 1:
                                window.location.href = '/Admin';
                                break;
                            case 2:
                                window.location.href = '/Customer';
                                break;
                            default:
                                showError('Peran pengguna tidak dikenal!');
                        }
                    } else {
                        showError('Token tidak ditemukan dalam respons.');
                    }
                },
                error: function(xhr) {
                    showError(xhr.responseJSON.message || 'Login gagal.');
                }
            });
        });

        function showError(message) {
            $('#errorMessage').removeClass('d-none').text(message);
        }
    });
    </script>
</body>
</html>
