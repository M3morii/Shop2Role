function loadCategories() {
    $.ajax({
        url: '/api/admin/categories',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            let categoriesHtml = '';
            const categories = Array.isArray(response) ? response : [];
            
            categories.map(category => {
                categoriesHtml += `
                    <tr>
                        <td>${category.name}</td>
                        <td>${category.items_count || 0} item</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-category" data-id="${category.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-category" data-id="${category.id}">Hapus</button>
                        </td>
                    </tr>`;
            });

            if (categories.length === 0) {
                categoriesHtml = `
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada kategori</td>
                    </tr>`;
            }
            
            $('#categoryList').html(categoriesHtml);
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal memuat kategori'
            });
            console.error('Error loading categories:', xhr);
        }
    });
}

function loadCategoriesToDropdown() {
    $.ajax({
        url: '/api/admin/categories',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            let options = '<option value="">Pilih Kategori</option>';
            for(let i = 0; i < response.length; i++) {
                options += `<option value="${response[i].id}">${response[i].name}</option>`;
            }
            $('#addItemCategory').html(options);
            $('#editItemCategory').html(options);
        },
        error: function(xhr) {
            console.error('Gagal mengambil data kategori:', xhr);
            alert('Gagal mengambil data kategori');
        }
    });
}

// Save Category Changes
$('#saveCategoryChanges').click(function() {
    let categoryId = $('#categoryId').val();
    let categoryName = $('#categoryName').val();
    let url = categoryId ? `/api/admin/categories/${categoryId}` : '/api/admin/categories';
    let method = categoryId ? 'PUT' : 'POST';

    Swal.fire({
        title: 'Menyimpan Kategori...',
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: url,
        method: method,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        data: { name: categoryName },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Kategori berhasil disimpan'
            });
            loadCategories();
            $('#categoryModal').modal('hide');
            $('#categoryId').val('');
            $('#categoryName').val('');
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal menyimpan kategori'
            });
        }
    });
});

// Edit Category
$(document).on('click', '.edit-category', function() {
    let categoryId = $(this).data('id');
    $.ajax({
        url: `/api/admin/categories/${categoryId}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            $('#categoryId').val(response.category.id);
            $('#categoryName').val(response.category.name);
        },
        error: function(xhr) {
            alert('Gagal mengambil data kategori');
        }
    });
});

// Delete Category
$(document).on('click', '.delete-category', function() {
    let categoryId = $(this).data('id');
    
    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin menghapus kategori ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/categories/${categoryId}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kategori berhasil dihapus'
                    });
                    loadCategories();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal menghapus kategori'
                    });
                }
            });
        }
    });
}); 