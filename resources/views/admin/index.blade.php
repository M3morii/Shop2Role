<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Item List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #ffffff;
            color: #000000;
        }
        .table {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            color: #000000;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Item List</h2>
        <button class="btn btn-success mb-3" id="addNewItem">Tambah Barang Baru</button>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Stock</th>
                    <th>Sell Price</th>
                    <th>Files</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="itemTable">
                <!-- Item rows will be added here -->
            </tbody>
        </table>
    </div>

    <!-- Modal Edit Stock -->
    <div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStockModalLabel">Edit Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editStockForm">
                        <input type="hidden" id="editStockItemId">
                        <div class="form-group">
                            <label>Current Stock: <span id="currentStock"></span></label>
                        </div>
                        <div class="form-group">
                            <label>Stock Type:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="stockType" id="stockTypeIn" value="in" checked>
                                <label class="form-check-label" for="stockTypeIn">
                                    In (Tambah Stok)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="stockType" id="stockTypeOut" value="out">
                                <label class="form-check-label" for="stockTypeOut">
                                    Out (Kurangi Stok)
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="stockQuantity">Quantity:</label>
                            <input type="number" class="form-control" id="stockQuantity" required min="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStockChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Item -->
    <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm">
                        <input type="hidden" id="editItemId">
                        <div class="form-group">
                            <label for="editItemName">Name:</label>
                            <input type="text" class="form-control" id="editItemName" required>
                        </div>
                        <div class="form-group">
                            <label for="editItemDescription">Description:</label>
                            <textarea class="form-control" id="editItemDescription" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editItemSellPrice">Sell Price:</label>
                            <input type="number" class="form-control" id="editItemSellPrice" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveItemChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Item Baru -->
    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Tambah Barang Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="addItemName">Nama:</label>
                            <input type="text" class="form-control" id="addItemName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="addItemDescription">Deskripsi:</label>
                            <textarea class="form-control" id="addItemDescription" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="addItemSellPrice">Harga Jual:</label>
                            <input type="number" class="form-control" id="addItemSellPrice" name="sellprice" required>
                        </div>
                        <div class="form-group">
                            <label for="addItemStock">Stok Awal:</label>
                            <input type="number" class="form-control" id="addItemStock" name="stock" required>
                        </div>
                        <div class="form-group">
                            <label for="addItemFiles">File (Gambar/Video):</label>
                            <input type="file" class="form-control-file" id="addItemFiles" name="files[]" multiple>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveNewItem">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        var token = sessionStorage.getItem('access_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        function loadItems() {
            $.ajax({
                url: '/api/items',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    if (response && response.length > 0) {
                        let rows = '';
                        $.each(response, function(index, item) {
                            let filesList = '';
                            if (item.files && item.files.length > 0) {
                                filesList = '<div class="file-list">';
                                item.files.forEach(file => {
                                    const fileName = file.file_path.split('/').pop();
                                    filesList += `<img src="/storage/${file.file_path}" alt="${fileName}" class="file-thumbnail" style="width: 50px; height: auto; margin-right: 5px;">`;
                                });
                                filesList += '</div>';
                            } else {
                                filesList = 'Tidak ada file';
                            }
                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.name}</td>
                                    <td>${item.description}</td>
                                    <td>${item.stock}</td>
                                    <td>Rp ${Number(item.sellprice).toLocaleString('id-ID')}</td>
                                    <td>${filesList}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-item" data-id="${item.id}">Edit Item</button>
                                        <button class="btn btn-sm btn-info edit-stock" data-id="${item.id}">Edit Stok</button>
                                        <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">Hapus</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#itemTable').html(rows);

                        // Event listeners untuk tombol-tombol
                        $('.edit-item').click(function() {
                            var itemId = $(this).data('id');
                            // Ambil data item dan isi form
                            $.ajax({
                                url: '/api/admin/items/' + itemId,
                                method: 'GET',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                success: function(item) {
                                    $('#editItemId').val(item.id);
                                    $('#editItemName').val(item.name);
                                    $('#editItemDescription').val(item.description);
                                    $('#editItemSellPrice').val(Number(item.sellprice).toLocaleString('id-ID'));
                                    $('#editItemModal').modal('show');
                                },
                                error: function() {
                                    alert('Gagal mengambil data item');
                                }
                            });
                        });

                        $('.edit-stock').click(function() {
                            var itemId = $(this).data('id');
                            $('#editStockItemId').val(itemId);
                            
                            // Ambil data stok saat ini
                            $.ajax({
                                url: '/api/admin/stocks/' + itemId,
                                method: 'GET',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                success: function(response) {
                                    $('#currentStock').text(response.quantity);
                                    $('#editStockModal').modal('show');
                                },
                                error: function() {
                                    alert('Gagal mengambil data stok');
                                }
                            });
                        });

                        $('.delete-item').click(function() {
                            var itemId = $(this).data('id');
                            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                                $.ajax({
                                    url: '/api/admin/items/' + itemId,
                                    method: 'DELETE',
                                    headers: {
                                        'Authorization': 'Bearer ' + token
                                    },
                                    success: function(response) {
                                        alert('Item berhasil dihapus');
                                        loadItems();
                                    },
                                    error: function(xhr) {
                                        alert('Gagal menghapus item');
                                    }
                                });
                            }
                        });
                    } else {
                        $('#itemTable').html('<tr><td colspan="7" class="text-center">Tidak ada item ditemukan</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 401) {
                        alert('Sesi Anda telah berakhir. Silakan login kembali.');
                        sessionStorage.removeItem('access_token');
                        window.location.href = '/login';
                    } else {
                        $('#itemTable').html('<tr><td colspan="7" class="text-center">Error mengambil data</td></tr>');
                    }
                }
            });
        }

        loadItems();

        // Handle edit stock
        $('#saveStockChanges').click(function() {
            var itemId = $('#editStockItemId').val();
            var quantity = $('#stockQuantity').val();
            var type = $('input[name="stockType"]:checked').val();

            $.ajax({
                url: '/api/admin/stocks',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: {
                    item_id: itemId,
                    quantity: quantity,
                    type: type
                },
                success: function(response) {
                    alert('Stok berhasil diperbarui');
                    $('#editStockModal').modal('hide');
                    loadItems();
                },
                error: function(xhr) {
                    alert('Gagal memperbarui stok: ' + xhr.responseJSON.message);
                }
            });
        });

        // Handle edit item
        $('#saveItemChanges').click(function() {
            var itemId = $('#editItemId').val();
            var itemData = {
                name: $('#editItemName').val(),
                description: $('#editItemDescription').val(),
                sellprice: $('#editItemSellPrice').val().replace(/\D/g, '') // Hapus semua karakter non-digit
            };
            $.ajax({
                url: '/api/admin/items/' + itemId,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: itemData,
                success: function(response) {
                    alert('Item berhasil diperbarui');
                    $('#editItemModal').modal('hide');
                    loadItems();
                },
                error: function(xhr) {
                    alert('Gagal memperbarui item');
                }
            });
        });

        // Tambahkan ini di dalam script yang sudah ada
        $('#addNewItem').click(function() {
            $('#addItemModal').modal('show');
        });

        $('#saveNewItem').click(function() {
            var formData = new FormData($('#addItemForm')[0]);
            
            $.ajax({
                url: '/api/admin/items',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert('Item baru berhasil ditambahkan');
                    $('#addItemModal').modal('hide');
                    $('#addItemForm')[0].reset();
                    loadItems();
                },
                error: function(xhr) {
                    alert('Gagal menambahkan item baru: ' + xhr.responseJSON.message);
                }
            });
        });
    });
    </script>
</body>
</html>
