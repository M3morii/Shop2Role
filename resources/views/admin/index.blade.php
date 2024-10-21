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
        <button class="btn btn-info mb-3" id="viewCustomerOrders">Lihat Pesanan Customer</button>
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

    <!-- Modal Pesanan Customer -->
    <div class="modal fade" id="customerOrdersModal" tabindex="-1" role="dialog" aria-labelledby="customerOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerOrdersModalLabel">Pesanan Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="customerOrdersContent">
                    <!-- Pesanan akan ditampilkan di sini -->
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
                            
                            // Ambil data item termasuk stok
                            $.ajax({
                                url: '/api/admin/items/' + itemId,
                                method: 'GET',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                success: function(item) {
                                    $('#currentStock').text(item.stock);
                                    $('#editStockModal').modal('show');
                                },
                                error: function() {
                                    alert('Gagal mengambil data item');
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

        // Ganti fungsi loadCustomerOrders() dengan yang berikut:
        function loadCustomerOrders() {
            $.ajax({
                url: '/api/admin/orders',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    let ordersHtml = '';
                    if (response.orders && response.orders.length > 0) {
                        let groupedOrders = {};
                        response.orders.forEach(function(order) {
                            if (!groupedOrders[order.invoice_id]) {
                                groupedOrders[order.invoice_id] = {
                                    id: order.invoice_id,
                                    status: order.status,
                                    items: [],
                                    total_price: 0
                                };
                            }
                            let itemPrice = parseFloat(order.price);
                            if (isNaN(itemPrice)) {
                                itemPrice = 0;
                            }
                            groupedOrders[order.invoice_id].items.push({
                                id: order.id,
                                name: order.item.name,
                                quantity: order.quantity,
                                price: itemPrice
                            });
                            groupedOrders[order.invoice_id].total_price += itemPrice;
                        });

                        Object.values(groupedOrders).forEach(function(invoice) {
                            let approveButton = `<button class="btn btn-success approve-order" data-id="${invoice.id}">Approve</button>`;
                            let declineButton = `<button class="btn btn-danger decline-order" data-id="${invoice.id}">Decline</button>`;
                            
                            if (invoice.status === 'approved') {
                                approveButton = `<button class="btn btn-success" disabled>Approved</button>`;
                                declineButton = '';
                            } else if (invoice.status === 'declined') {
                                approveButton = '';
                                declineButton = `<button class="btn btn-danger" disabled>Declined</button>`;
                            }

                            ordersHtml += `
                                <div class="card mb-3">
                                    <div class="card-header">
                                        Invoice #${invoice.id} - Status: ${invoice.status}
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Items:</h5>
                                        <ul class="list-group">
                    `;
                            invoice.items.forEach(function(item) {
                                ordersHtml += `
                                    <li class="list-group-item">
                                        ${item.name} - Jumlah: ${item.quantity} - Harga: Rp${item.price.toLocaleString('id-ID')}
                                    </li>
                                `;
                            });
                            ordersHtml += `
                                        </ul>
                                        <p class="mt-3">Total Harga: Rp${invoice.total_price.toLocaleString('id-ID')}</p>
                                        ${approveButton}
                                        ${declineButton}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        ordersHtml = '<p>Tidak ada pesanan saat ini.</p>';
                    }
                    $('#customerOrdersContent').html(ordersHtml);
                },
                error: function(xhr) {
                    alert('Gagal memuat pesanan customer');
                }
            });
        }

        // Event listener untuk tombol Lihat Pesanan Customer
        $('#viewCustomerOrders').click(function() {
            loadCustomerOrders();
            $('#customerOrdersModal').modal('show');
        });

        // Ganti event listener untuk tombol Approve
        $(document).on('click', '.approve-order', function() {
            var invoiceId = $(this).data('id');
            $.ajax({
                url: '/api/admin/orders/' + invoiceId + '/approve',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    alert(response.message);
                    loadCustomerOrders();
                },
                error: function(xhr) {
                    alert('Gagal menyetujui pesanan: ' + xhr.responseJSON.message);
                }
            });
        });

        // Ganti event listener untuk tombol Decline
        $(document).on('click', '.decline-order', function() {
            var invoiceId = $(this).data('id');
            $.ajax({
                url: '/api/admin/orders/' + invoiceId + '/decline',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    alert(response.message);
                    loadCustomerOrders();
                },
                error: function(xhr) {
                    alert('Gagal menolak pesanan: ' + xhr.responseJSON.message);
                }
            });
        });
    });
    </script>
</body>
</html>
