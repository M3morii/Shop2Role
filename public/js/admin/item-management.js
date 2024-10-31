function getToken() {
    const token = sessionStorage.getItem('access_token');
    if (!token) {
        window.location.href = '/login';
        return null;
    }
    return token;
}

function loadItems(page = 1, search = '', categoryId = '') {
    const token = getToken();
    if (!token) return;

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
                            <td>${index + 1}</td>
                            <td>${item.name}</td>
                            <td>${item.description}</td>
                            <td>
                                ${item.stock}
                                <button class="btn btn-sm btn-info edit-stock ml-2" data-id="${item.id}" data-stock="${item.stock}">
                                    <i class="fas fa-boxes"></i>
                                </button>
                            </td>
                            <td>${formatCurrency(item.sellprice)}</td>
                            <td>${item.category ? item.category.name : '-'}</td>
                            <td>${filesHtml}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary edit-item" data-id="${item.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
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
            console.error('Error:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat data'
            });
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
    loadCategoriesToSelect('#addItemCategory');
    $('#addItemModal').modal('show');
    $('#addItemModal').find('.modal-content').addClass('animate__animated animate__fadeIn');
});

function loadCategoriesToSelect(selectElement, selectedId = null) {
    const token = getToken();
    if (!token) return;

    return $.ajax({
        url: '/api/admin/categories',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            let options = '<option value="">Pilih Kategori</option>';
            response.forEach(category => {
                const selected = selectedId && selectedId == category.id ? 'selected' : '';
                options += `<option value="${category.id}" ${selected}>${category.name}</option>`;
            });
            $(selectElement).html(options);
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat kategori'
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

$(document).on('click', '.delete-item', function() {
    const itemId = $(this).data('id');
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Item yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/items/${itemId}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    Swal.fire(
                        'Terhapus!',
                        'Item berhasil dihapus.',
                        'success'
                    );
                    loadItems();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'Gagal menghapus item.',
                        'error'
                    );
                }
            });
        }
    });
});

$(document).on('click', '.edit-stock', function() {
    const itemId = $(this).data('id');
    const currentStock = $(this).data('stock');
    
    Swal.fire({
        title: 'Edit Stock',
        html: `
            <div class="form-group">
                <label>Stock Saat Ini: ${currentStock}</label>
                <div class="mt-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" id="stockIn" value="in" checked>
                        <label class="form-check-label" for="stockIn">Tambah (+)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" id="stockOut" value="out">
                        <label class="form-check-label" for="stockOut">Kurang (-)</label>
                    </div>
                </div>
                <input type="number" id="stockChange" class="form-control mt-2" min="1" placeholder="Masukkan jumlah">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const stockChange = document.getElementById('stockChange').value;
            const type = document.querySelector('input[name="type"]:checked').value;
            
            if (!stockChange || stockChange <= 0) {
                Swal.showValidationMessage('Masukkan jumlah stock yang valid');
                return false;
            }
            
            return { stockChange, type };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const { stockChange, type } = result.value;
            
            $.ajax({
                url: '/api/admin/stocks',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: {
                    item_id: itemId,
                    quantity: parseInt(stockChange),
                    type: type
                },
                success: function(response) {
                    let message = '';
                    if (type === 'in') {
                        message = `Stock berhasil ditambah sebanyak ${stockChange}`;
                    } else {
                        message = `Stock berhasil dikurangi sebanyak ${stockChange}`;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: message
                    }).then(() => {
                        loadItems();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal memperbarui stock.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
    });
});

function loadCategoriesToSelect(selectElement, selectedId = null) {
    const token = getToken();
    if (!token) return;

    return $.ajax({
        url: '/api/admin/categories',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            let options = '<option value="">Pilih Kategori</option>';
            response.forEach(category => {
                const selected = selectedId && selectedId == category.id ? 'selected' : '';
                options += `<option value="${category.id}" ${selected}>${category.name}</option>`;
            });
            $(selectElement).html(options);
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat kategori'
            });
        }
    });
}

function loadCategoriesToFilter() {
    const token = getToken();
    if (!token) return;

    $.ajax({
        url: '/api/admin/categories',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            let options = '<option value="">Semua Kategori</option>';
            response.forEach(category => {
                options += `<option value="${category.id}">${category.name}</option>`;
            });
            $('#categoryFilter').html(options);
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat kategori'
            });
        }
    });
}

function editItem(itemId) {
    const token = getToken();
    if (!token) return;

    Swal.fire({
        title: 'Edit Barang',
        text: 'Apakah Anda ingin mengedit barang ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, edit',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            loadCategoriesToSelect('#editItemCategory').then(() => {
                $.ajax({
                    url: `/api/admin/items/${itemId}`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        Swal.close();
                        $('#editItemId').val(response.id);
                        $('#editItemName').val(response.name);
                        $('#editItemDescription').val(response.description);
                        $('#editItemSellPrice').val(response.sellprice);
                        $('#editItemCategory').val(response.category_id);
                        
                        if (response.files && response.files.length > 0) {
                            let imagesHtml = response.files.map(file => `
                                <div class="mb-2">
                                    <img src="${file.url}" class="img-thumbnail" style="height: 100px">
                                </div>`
                            ).join('');
                            $('#currentImages').html(imagesHtml);
                        } else {
                            $('#currentImages').html('<p class="text-muted">Tidak ada gambar</p>');
                        }
                        
                        Swal.close();
                        $('#editItemModal').modal('show');
                        $('#editItemModal').find('.modal-content').addClass('animate__animated animate__fadeIn');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memuat data item'
                        });
                    }
                });
            });
        }
    });
}

// Initialize
$(document).ready(function() {
    const token = getToken();
    if (!token) return;
    
    loadItems();
    loadCategoriesToFilter();
});
