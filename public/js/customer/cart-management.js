function getToken() {
    return sessionStorage.getItem('access_token');
}

$(document).ready(function() {
    // Fungsi untuk memuat keranjang
    window.loadCart = function() {
        $.ajax({
            url: '/api/cart',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(response) {
                let cartHtml = '';
                let totalAmount = 0;
                
                if (response.length === 0) {
                    cartHtml = '<p class="text-center">Keranjang belanja kosong</p>';
                    $('#orderBtn').hide();
                } else {
                    cartHtml = `
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    response.forEach(cart => {
                        const subtotal = cart.quantity * cart.price;
                        totalAmount += subtotal;

                        cartHtml += `
                            <tr>
                                <td>${cart.item_name}</td>
                                <td>Rp${Number(cart.price).toLocaleString('id-ID')}</td>
                                <td>
                                    <input type="number" class="form-control cart-quantity" 
                                        data-id="${cart.id}" 
                                        value="${cart.quantity}" 
                                        min="1" 
                                        style="width: 80px">
                                </td>
                                <td>Rp${Number(subtotal).toLocaleString('id-ID')}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-cart-item" 
                                        data-id="${cart.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    cartHtml += `
                            </tbody>
                        </table>
                        <div class="text-end mt-3">
                            <h5>Total: Rp${Number(totalAmount).toLocaleString('id-ID')}</h5>
                        </div>
                    </div>
                `;
                $('#orderBtn').show();
            }

            $('#cartContent').html(cartHtml);
            window.cartTotal = totalAmount;
            $('#cartModal').modal('show');
        },
        error: handleAjaxError
    });
    };

    // Event handler untuk tombol viewCart di navbar
    $('#viewCart').click(function() {
        loadCart();
    });

    // Fungsi untuk update total harga
    window.updateTotalPrice = function() {
        let total = 0;
        $('.cart-quantity').each(function() {
            const quantity = $(this).val();
            const price = $(this).closest('tr').find('td:eq(1)').text()
                .replace('Rp', '').replace(/\./g, '');
            const subtotal = quantity * parseInt(price);
            $(this).closest('tr').find('td:eq(3)').text(
                'Rp' + Number(subtotal).toLocaleString('id-ID')
            );
            total += subtotal;
        });

        $('.text-end.mt-3 h5').text(`Total: Rp${Number(total).toLocaleString('id-ID')}`);
        window.cartTotal = total;
    };

    // Event handler untuk perubahan quantity di keranjang
    $(document).on('change', '.cart-quantity', function() {
        const cartId = $(this).data('id');
        const quantity = $(this).val();

        $.ajax({
            url: `/api/cart/${cartId}`,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            data: { quantity: quantity },
            success: function(response) {
                updateTotalPrice();
                showSweetAlert('Jumlah item berhasil diupdate', 'success');
            },
            error: handleAjaxError
        });
    });

    // Event handler untuk hapus item dari keranjang
    $(document).on('click', '.delete-cart-item', function() {
        const cartId = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Item',
            text: 'Apakah Anda yakin ingin menghapus item ini dari keranjang?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCartItem(cartId);
            }
        });
    });
});

// Fungsi untuk menghapus item dari keranjang
function deleteCartItem(cartId) {
    $.ajax({
        url: `/api/cart/${cartId}`,
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer ' + getToken()
        },
        success: function(response) {
            loadCart();
            showSweetAlert('Item berhasil dihapus dari keranjang', 'success');
        },
        error: handleAjaxError
    });
}

// Fungsi untuk menampilkan SweetAlert
function showSweetAlert(message, type) {
    Swal.fire({
        title: message,
        icon: type,
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}