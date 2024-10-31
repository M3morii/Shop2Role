function getToken() {
    return sessionStorage.getItem('access_token');
}

$(document).ready(function() {
    // Utility Functions
    function getToken() {
        return sessionStorage.getItem('access_token');
    }

    function showMessage(message, type = 'success') {
        $('#messageContainer').html(`<div class="alert alert-${type}">${message}</div>`);
        setTimeout(() => {
            $('#messageContainer').html('');
        }, 5000);
    }

    function handleAjaxError(xhr) {
        if (xhr.status === 401) {
            sessionStorage.removeItem('access_token');
            window.location.href = '/login';
        } else {
            showMessage('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
    }

    // Item Management Functions
    function loadCategories() {
        $.ajax({
            url: '/api/categories',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken(),
                'Accept': 'application/json'
            },
            success: function(response) {
                let categorySelect = $('#categoryFilter');
                categorySelect.empty();
                categorySelect.append('<option value="">Semua Kategori</option>');
                
                if (Array.isArray(response)) {
                    response.forEach(category => {
                        categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }
            },
            error: handleAjaxError
        });
    }

    function loadItems(search = '', categoryId = '') {
        $.ajax({
            url: '/api/items',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(response) {
                let itemsHtml = '';
                let items = Array.isArray(response) ? response : (response.data || []);
                
                if (categoryId) {
                    items = items.filter(item => item.category_id == categoryId);
                }

                if (search) {
                    const searchLower = search.toLowerCase();
                    items = items.filter(item => 
                        item.name.toLowerCase().includes(searchLower)
                    );
                }

                if (items.length === 0) {
                    itemsHtml = `
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                ${categoryId ? 'Tidak ada item dalam kategori ini' : 'Tidak ada item yang ditemukan'}
                            </div>
                        </div>`;
                } else {
                    items.forEach(item => {
                        let imageUrl = '/img/no-image.jpg';
                        if (item.files && item.files.length > 0) {
                            imageUrl = `/storage/${item.files[0].file_path}`;
                        }

                        itemsHtml += `
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="position-relative">
                                        <img src="${imageUrl}" class="card-img-top" alt="${item.name}">
                                        <span class="badge-stock">Stock: ${item.stock} pcs</span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${item.name}</h5>
                                        <p class="card-text">${item.description || 'Berkualitas'}</p>
                                        <p class="price">Rp${Number(item.sellprice).toLocaleString('id-ID')}</p>
                                        <div class="input-group">
                                            <input type="number" 
                                                class="form-control item-quantity" 
                                                data-id="${item.id}" 
                                                value="0" 
                                                min="0"
                                                max="${item.stock}"
                                                ${item.stock <= 0 ? 'disabled' : ''}>
                                            <button class="btn btn-primary add-to-cart" 
                                                data-id="${item.id}"
                                                ${item.stock <= 0 ? 'disabled' : ''}>
                                                <i class="bi bi-cart-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }

                $('#itemList').html(itemsHtml);
            },
            error: handleAjaxError
        });
    }

    // Event Handlers
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val();
        const categoryId = $('#categoryFilter').val();
        loadItems(searchTerm, categoryId);
    });

    $('#categoryFilter').on('change', function() {
        const categoryId = $(this).val();
        const searchTerm = $('#searchInput').val();
        loadItems(searchTerm, categoryId);
    });

    // Initialize
    if (!getToken()) {
        window.location.href = '/login';
    } else {
        loadCategories();
        loadItems();
    }

    // Quantity handlers
    $(document).on('click', '.increase-quantity, .decrease-quantity', function() {
        const input = $(this).siblings('input.item-quantity');
        const currentValue = parseInt(input.val());
        const maxStock = parseInt(input.attr('max'));
        if ($(this).hasClass('increase-quantity') && currentValue < maxStock) {
            input.val(currentValue + 1);
        } else if ($(this).hasClass('decrease-quantity') && currentValue > 0) {
            input.val(currentValue - 1);
        }
        updateTotalPrice(input);
    });

    function updateTotalPrice(input) {
        const quantity = parseInt(input.val());
        const price = parseFloat(input.data('price'));
        const totalPrice = quantity * price;
        $(`.total-price[data-id="${input.data('id')}"]`).text(
            `Total: Rp${totalPrice.toLocaleString('id-ID')}`
        );
    }

    // Tambahkan di bagian Event Handlers
    $('#logoutBtn').click(function() {
        Swal.fire({
            title: 'Logout',
            text: 'Apakah Anda yakin ingin keluar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                sessionStorage.removeItem('access_token');
                Swal.fire({
                    title: 'Berhasil Logout',
                    text: 'Anda akan dialihkan ke halaman login',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/login';
                });
            }
        });
    });

    // Event handler untuk tombol navbar
    $('#viewCart').click(function() {
        loadCart(); // Fungsi dari cart-management.js
    });

    $('#viewInvoice').click(function() {
        viewInvoice(); // Fungsi dari invoice-management.js
    });

    $('#viewOrders').click(function() {
        viewOrders(); // Fungsi dari order-management.js
    });

    // Event handler untuk tombol Order di modal keranjang
    $('#orderBtn').click(function() {
        createOrder(); // Fungsi dari order-management.js
    });

    // Tambahkan SweetAlert untuk setiap aksi
    window.showSweetAlert = function(message, type = 'success') {
        Swal.fire({
            title: message,
            icon: type,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };

    // Fungsi untuk handle error dengan SweetAlert
    window.handleAjaxError = function(xhr) {
        if (xhr.status === 401) {
            sessionStorage.removeItem('access_token');
            Swal.fire({
                title: 'Sesi Berakhir',
                text: 'Silakan login kembali',
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '/login';
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Terjadi kesalahan. Silakan coba lagi.',
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        }
    };

    // Tambahkan event handler untuk input quantity
    $(document).on('input', '.item-quantity', function() {
        const maxStock = parseInt($(this).attr('max'));
        let currentValue = parseInt($(this).val()) || 0;

        // Validasi input tidak boleh melebihi stock
        if (currentValue > maxStock) {
            $(this).val(maxStock);
            showSweetAlert(`Maksimal pembelian ${maxStock} unit`, 'warning');
        }
        
        // Validasi input tidak boleh negatif
        if (currentValue < 0) {
            $(this).val(0);
        }
    });

    // Update event handler add-to-cart
    $(document).on('click', '.add-to-cart', function() {
        const itemId = $(this).data('id');
        const quantityInput = $(this).closest('.card-body').find('.item-quantity');
        const quantity = parseInt(quantityInput.val());
        const maxStock = parseInt(quantityInput.attr('max'));

        if (quantity <= 0) {
            showSweetAlert('Jumlah item harus lebih dari 0', 'error');
            return;
        }

        if (quantity > maxStock) {
            showSweetAlert(`Maksimal pembelian ${maxStock} unit`, 'error');
            quantityInput.val(maxStock);
            return;
        }

        $.ajax({
            url: '/api/cart',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            data: {
                item_id: itemId,
                quantity: quantity
            },
            success: function(response) {
                showSweetAlert('Item berhasil ditambahkan ke keranjang', 'success');
                // Reset input quantity
                quantityInput.val(0);
            },
            error: handleAjaxError
        });
    });
});