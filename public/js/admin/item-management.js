function loadCategoriesToFilter() {
    console.log('Loading categories for filter...');
    
    $.ajax({
        url: '/api/admin/categories',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            console.log('Categories response:', response);
            
            const categories = Array.isArray(response) ? response : 
                             (response.data ? response.data : []);
            
            let options = '<option value="">Semua Kategori</option>';
            if (categories.length > 0) {
                categories.forEach(category => {
                    options += `<option value="${category.id}">${category.name}</option>`;
                });
            }
            
            $('#categoryFilter').html(options);
        },
        error: function(xhr) {
            console.error('Error loading categories:', xhr);
            $('#categoryFilter').html('<option value="">Error loading categories</option>');
        }
    });
}

function loadItems(page = 1, search = '', categoryId = '') {
    console.log('Loading items with:', { page, search, categoryId });
    
    $.ajax({
        url: '/api/admin/items',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        data: {
            page: page,
            search: search,
            category_id: categoryId
        },
        success: function(response) {
            let items = '';
            if (response.data && response.data.length > 0) {
                response.data.forEach((item, index) => {
                    const startIndex = (response.current_page - 1) * response.per_page;
                    
                    let filesHtml = '';
                    if (item.files && item.files.length > 0) {
                        filesHtml = item.files.map(file => {
                            const fileExt = file.file_path.split('.').pop().toLowerCase();
                            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExt);
                            
                            if (isImage) {
                                return `
                                    <a href="/storage/${file.file_path}" 
                                       data-lightbox="item-${item.id}" 
                                       data-title="${item.name}"
                                       class="mr-2">
                                        <img src="/storage/${file.file_path}" 
                                             alt="${item.name}" 
                                             class="img-thumbnail" 
                                             style="height: 50px; width: 50px; object-fit: cover;">
                                    </a>`;
                            } else {
                                return `
                                    <a href="${file.file_path}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-info mr-1">
                                        <i class="fas fa-file"></i>
                                    </a>`;
                            }
                        }).join('');
                    } else {
                        filesHtml = '-';
                    }

                    items += `
                        <tr>
                            <td>${startIndex + index + 1}</td>
                            <td>${item.name}</td>
                            <td>${item.description || '-'}</td>
                            <td>${item.stock}</td>
                            <td>${formatCurrency(item.sellprice)}</td>
                            <td>${item.category ? item.category.name : '-'}</td>
                            <td>${filesHtml}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-item" data-id="${item.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                items = `
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada item ditemukan</td>
                    </tr>
                `;
            }
            
            $('#itemTable').html(items);
            
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': 'Gambar %1 dari %2'
            });
            
            if (response.last_page > 1) {
                updatePagination(response);
            } else {
                $('#pagination').html('');
            }
        },
        error: function(xhr) {
            console.error('Error loading items:', xhr);
            $('#itemTable').html(`
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        Gagal memuat data item
                    </td>
                </tr>
            `);
        }
    });
}

function updatePagination(response) {
    if (response.last_page > 1) {
        let pagination = '';
        for (let i = 1; i <= response.last_page; i++) {
            pagination += `
                <li class="page-item ${response.current_page === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
        }
        $('#pagination').html(`
            <ul class="pagination justify-content-center">
                ${pagination}
            </ul>`);
    } else {
        $('#pagination').html('');
    }
}

function showNoDataMessage(search) {
    $('#itemTable').html(`
        <tr>
            <td colspan="8" class="text-center">
                ${search ? 'Tidak ada hasil untuk pencarian "' + search + '"' : 'Tidak ada data'}
            </td>
        </tr>`);
    $('#pagination').html('');
}

function showErrorMessage(xhr) {
    console.error('Error:', xhr);
    $('#itemTable').html(`
        <tr>
            <td colspan="8" class="text-center text-danger">
                Gagal memuat data: ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
            </td>
        </tr>`);
}

$(document).on('click', '.edit-item', function() {
    let itemId = $(this).data('id');
    $.ajax({
        url: '/api/admin/items/' + itemId,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            $('#editItemId').val(response.id);
            $('#editItemName').val(response.name);
            $('#editItemDescription').val(response.description);
            $('#editItemSellPrice').val(response.sellprice);
            $('#editItemCategory').val(response.category_id);
            
            let existingImages = '';
            if (response.files && response.files.length > 0) {
                response.files.forEach(file => {
                    existingImages += `
                        <div class="existing-image">
                            <img src="/storage/${file.file_path}" class="img-thumbnail" style="width: 100px;">
                            <button type="button" class="btn btn-sm btn-danger delete-image" 
                                    data-id="${file.id}" data-item-id="${response.id}">Hapus</button>
                        </div>`;
                });
            }
            $('#existingImages').html(existingImages);
            
            $('#editItemModal').modal('show');
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal mengambil data item'
            });
        }
    });
});

$('#saveItemChanges').click(function() {
    let itemId = $('#editItemId').val();
    let formData = new FormData();
    
    formData.append('name', $('#editItemName').val());
    formData.append('description', $('#editItemDescription').val());
    formData.append('sellprice', $('#editItemSellPrice').val());
    formData.append('category_id', $('#editItemCategory').val());
    
    let files = $('#editItemFiles')[0].files;
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        formData.append('replace_images', '1');
    } else {
        formData.append('replace_images', '0');
    }

    if (!validateItemForm(formData)) return;

    showSavingDialog();
    
    $.ajax({
        url: `/api/admin/items/${itemId}/update`,
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        data: formData,
        processData: false,
        contentType: false,
        success: handleSaveSuccess,
        error: handleSaveError
    });
});

function validateItemForm(formData) {
    if (!formData.get('name') || !formData.get('description') || 
        !formData.get('sellprice') || !formData.get('category_id')) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Semua field harus diisi!'
        });
        return false;
    }
    return true;
}

function showSavingDialog() {
    Swal.fire({
        title: 'Menyimpan Perubahan...',
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function handleSaveSuccess(response) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Item berhasil diperbarui'
    }).then((result) => {
        $('#editItemModal').modal('hide');
        $('#editItemFiles').val('');
        loadItems();
    });
}

function handleSaveError(xhr) {
    let errorMessage = 'Gagal memperbarui item';
    if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = typeof xhr.responseJSON.message === 'object' 
            ? Object.values(xhr.responseJSON.message).join('\n')
            : xhr.responseJSON.message;
    }
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: errorMessage
    });
}

$(document).on('click', '#addbarang', function() {
    $('#addItemForm')[0].reset();
    
    loadCategoriesToDropdown('#addItemCategory');
    
    $('#addItemModal').modal('show');
});

function loadCategoriesToDropdown(selectElement) {
    $.ajax({
        url: '/api/admin/categories',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            let options = '<option value="">Pilih Kategori</option>';
            if (Array.isArray(response)) {
                response.map(category => {
                    options += `<option value="${category.id}">${category.name}</option>`;
                });
            }
            $(selectElement).html(options);
        },
        error: function(xhr) {
            console.error('Gagal memuat kategori:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal memuat daftar kategori'
            });
        }
    });
}

$('#saveNewItem').click(function() {
    let formData = new FormData($('#addItemForm')[0]);
    
    if (!validateNewItemForm(formData)) return;

    Swal.fire({
        title: 'Menyimpan...',
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
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
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Item baru berhasil ditambahkan'
            }).then((result) => {
                $('#addItemModal').modal('hide');
                loadItems();
            });
        },
        error: function(xhr) {
            let errorMessage = 'Gagal menambahkan item';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = typeof xhr.responseJSON.message === 'object' 
                    ? Object.values(xhr.responseJSON.message).join('\n')
                    : xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMessage
            });
        }
    });
});

function validateNewItemForm(formData) {
    if (!formData.get('name') || 
        !formData.get('description') || 
        !formData.get('sellprice') || 
        !formData.get('stock') || 
        !formData.get('category_id')) {
        
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Semua field harus diisi!'
        });
        return false;
    }
    return true;
}

$(document).on('change', '#categoryFilter', function() {
    const categoryId = $(this).val();
    const searchTerm = $('#searchInput').val();
    loadItems(1, searchTerm, categoryId);
});

$(document).on('keyup', '#searchInput', function() {
    const searchTerm = $(this).val();
    const categoryId = $('#categoryFilter').val();
    loadItems(1, searchTerm, categoryId);
});

function formatCurrency(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num)
    .replace(/\s/g, '')  // Hapus spasi
    .replace(/,00/g, ''); // Hapus ,00
}