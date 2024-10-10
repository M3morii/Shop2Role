<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-dark.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Daftar Barang</h2>
        <div id="productList" class="row"></div>
        <div id="cartMessage" class="mt-3 text-center"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mengambil daftar barang dari API
            $.ajax({
                url: '/api/barang', // Endpoint API untuk mengambil barang
                method: 'GET',
                success: function(response) {
                    const products = response.data;
                    products.forEach(product => {
                        const productCard = `
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <img src="${product.file_url}" class="card-img-top" alt="${product.name}">
                                    <div class="card-body">
                                        <h5 class="card-title">${product.name}</h5>
                                        <p class="card-text">Deskripsi: ${product.description}</p>
                                        <p class="card-text">Harga: RP${product.price}</p>
                                        <button class="btn btn-primary add-to-cart" data-id="${product.id}">Tambah ke Keranjang</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#productList').append(productCard);
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseJSON.message);
                    $('#cartMessage').text('Gagal mengambil daftar barang.');
                }
            });

            // Menangani aksi klik pada tombol "Tambah ke Keranjang"
            $('#productList').on('click', '.add-to-cart', function() {
                const productId = $(this).data('id');
                $.ajax({
                    url: '/api/cart', // Endpoint API untuk menambah barang ke keranjang
                    method: 'POST',
                    data: {
                        product_id: productId,
                    },
                    success: function(response) {
                        $('#cartMessage').text(response.message);
                    },
                    error: function(xhr) {
                        $('#cartMessage').text(xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>
</html>
