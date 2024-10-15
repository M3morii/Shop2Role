<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-dark.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
        }
        .table {
            background-color: #ffffff;
            color: #000000;
        }
        .table th {
            background-color: #f8f9fa;
            color: #000000;
        }
        .table td {
            color: #000000;
        }
        .modal-content {
            background-color: #ffffff;
        }
        h2 {
            color: #ffffff;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .btn-warning, .btn-danger {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Item List</h2>
        <button class="btn btn-primary mb-3" id="addItemBtn" data-bs-toggle="modal" data-bs-target="#addItemModal">Add Item</button>
        
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Files</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="itemList"></tbody>
        </table>
    </div>

<!-- Modal Add Item -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #343a40;">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="addItemModalLabel">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="addItemName" class="form-label text-white">Name</label>
                        <input type="text" class="form-control" id="addItemName" required>
                    </div>
                    <div class="mb-3">
                        <label for="addItemDescription" class="form-label text-white">Description</label>
                        <textarea class="form-control" id="addItemDescription" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="addItemPrice" class="form-label text-white">Price</label>
                        <input type="text" class="form-control" id="addItemPrice" required>
                    </div>
                    <div class="mb-3">
                        <label for="addItemStock" class="form-label text-white">Stock</label>
                        <input type="number" class="form-control" id="addItemStock" required>
                    </div>
                    <div class="mb-3">
                        <label for="addItemFile" class="form-label text-white">Image</label>
                        <input type="file" class="form-control" id="addItemFile" required accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Item -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #343a40;">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="editItemModalLabel">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editItemForm" enctype="multipart/form-data">
                    <input type="hidden" id="editItemId">
                    <div class="mb-3">
                        <label for="editItemName" class="form-label text-white">Name</label>
                        <input type="text" class="form-control" id="editItemName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editItemDescription" class="form-label text-white">Description</label>
                        <textarea class="form-control" id="editItemDescription" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editItemPrice" class="form-label text-white">Price</label>
                        <input type="text" class="form-control" id="editItemPrice" required>
                    </div>
                    <div class="mb-3">
                        <label for="editItemStock" class="form-label text-white">Stock</label>
                        <input type="number" class="form-control" id="editItemStock" required>
                    </div>
                    <div class="mb-3">
                        <label for="editItemFile" class="form-label text-white">Image</label>
                        <input type="file" class="form-control" id="editItemFile" accept="image/*">
                        <small class="text-white">Leave blank to keep the current image.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function fetchItems(){
        const token = localStorage.getItem('token');
        $.ajax({
    url: '/api/items',
    method: 'GET',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    dataType: 'json',  // Menentukan tipe data yang diharapkan
    success: function(items) {
        $('#itemList').empty();
        items.forEach((item, index) => {
            $('#itemList').append(`
                <tr>
                    <td style="color: #000000;">${index + 1}</td>
                    <td style="color: #000000;">${item.name}</td>
                    <td style="color: #000000;">${item.description}</td>
                    <td style="color: #000000;">${item.price}</td>
                    <td style="color: #000000;">${item.stock}</td>
                    <td><img src="${item.file}" style="width: 40px; height: 40px;"></td>
                    <td>
                        <button class="btn btn-warning editItemBtn" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#editItemModal">Edit</button>
                        <button class="btn btn-danger deleteItemBtn" data-id="${item.id}">Delete</button>
                    </td>
                </tr>
            `);
        });
    },
    error: function(error) {
        console.error(error);
    }
    });
}


    function clearForm() {
        $('#editItemId').val('');
        $('#editItemForm')[0].reset();
        $('#editItemModalLabel').text('Edit Item');
    }

    $(document).ready(function() {
        fetchItems();

        // Handle Add Item form submission
        $('#addItemForm').submit(function(event) {
            event.preventDefault();
            const token = localStorage.getItem('token');
            const formData = new FormData(this); // Use FormData to handle file uploads

            $.ajax({
                url: '/api/items',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    fetchItems();
                    $('#addItemModal').modal('hide');
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.message);
                }
            });
        });

        // Handle Edit button click
        $(document).on('click', '.editItemBtn', function() {
            const id = $(this).data('id');
            $.ajax({
                url: `/api/items/${id}`,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                success: function(item) {
                    $('#editItemId').val(item.id);
                    $('#editItemName').val(item.name);
                    $('#editItemDescription').val(item.description);
                    $('#editItemPrice').val(item.price);
                    $('#editItemStock').val(item.stock);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.message);
                }
            });
        });

        // Handle Edit Item form submission
        $('#editItemForm').submit(function(event) {
            event.preventDefault();
            const token = localStorage.getItem('token');
            const id = $('#editItemId').val();
            const formData = new FormData(this); 

            $.ajax({
                url: `/api/items/${id}`,
                method: 'POST', // Use POST with '_method' for PUT
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    fetchItems();
                    $('#editItemModal').modal('hide');
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.message);
                }
            });
        });
    });
</script>
</body>
</html>
