function getToken() {
    return sessionStorage.getItem('access_token');
}

$(document).ready(function() {
    window.createOrder = function() {
        if (!window.cartTotal || window.cartTotal <= 0) {
            showSweetAlert('Keranjang belanja kosong atau total tidak valid', 'error');
            return;
        }

        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/api/cart',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(cartItems) {
                const cartIds = cartItems.map(item => item.id);

                const orderData = {
                    cart_id: cartIds
                };

                $.ajax({
                    url: '/api/order',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + getToken(),
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        Swal.close();
                        $('#cartModal').modal('hide');
                        showSweetAlert('Pesanan berhasil dibuat!', 'success');
                        setTimeout(() => {
                            viewOrders();
                        }, 2000);
                    },
                    error: function(xhr) {
                        Swal.close();
                        let message = 'Terjadi kesalahan saat membuat pesanan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        console.error('Order Error:', xhr.responseJSON);
                        showSweetAlert(message, 'error');
                    }
                });
            },
            error: function(xhr) {
                Swal.close();
                showSweetAlert('Gagal mengambil data keranjang', 'error');
            }
        });
    };

    window.viewOrders = function() {
        $.ajax({
            url: '/api/order',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(response) {
                let ordersHtml = `
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                // Filter hanya order dengan status pending
                const pendingOrders = response.orders.filter(order => order.status === 'pending');

                if (!pendingOrders || pendingOrders.length === 0) {
                    ordersHtml += '<tr><td colspan="4" class="text-center">Tidak ada pesanan yang menunggu persetujuan</td></tr>';
                } else {
                    pendingOrders.forEach(order => {
                        // Format tanggal
                        const date = new Date(order.created_at);
                        const formattedDate = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
                        
                        ordersHtml += `
                            <tr>
                                <td>#ORD-${order.id}</td>
                                <td>${formattedDate}</td>
                                <td>Rp ${Number(order.price).toLocaleString('id-ID')}</td>
                                <td><span class="badge bg-warning">Menunggu</span></td>
                            </tr>
                        `;
                    });
                }

                ordersHtml += `
                            </tbody>
                        </table>
                    </div>
                `;

                // Menggunakan modal yang sudah ada
                $('#ordersContent').html(ordersHtml);
                $('#ordersModal').modal('show');
            },
            error: handleAjaxError
        });
    };

    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning">Menunggu</span>',
            'approved': '<span class="badge bg-success">Disetujui</span>',
            'declined': '<span class="badge bg-danger">Ditolak</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }
});