<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

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
                        <h3>Register</h3>
                    </div>
                    <div class="card-body">
                        <form id="registerForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <div id="errorMessage" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#registerForm').submit(function(event) {
                event.preventDefault();

                var password = $('#password').val();
                var password_confirmation = $('#password_confirmation').val();

                if (password !== password_confirmation) {
                    $('#errorMessage').text('Password dan konfirmasi password tidak cocok').removeClass('d-none');
                    return;
                }

                $.ajax({
                    url: '/api/register',
                    type: 'POST',
                    data: {
                        name: $('#name').val(),
                        username: $('#username').val(),
                        email: $('#email').val(),
                        password: password,
                        password_confirmation: password_confirmation,
                    },
                    success: function(response) {
                        // Tambahkan logika untuk menangani respons sukses
                        console.log('Registrasi berhasil:', response);
                        // Redirect ke halaman login atau tampilkan pesan sukses
                    },
                    error: function(xhr) {
                        $('#errorMessage').text(xhr.responseJSON.message).removeClass('d-none');
                    }
                });
            });
        });
    </script>
</body>
</html>
