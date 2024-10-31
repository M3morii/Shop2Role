function getToken() {
    return sessionStorage.getItem('access_token');
}

$(document).ready(function() {
    window.createOrder = function() {
        Swal.fire({
            title: 'Buat Pesanan',
            text: 'Apakah Anda yakin ingin membuat pesanan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Buat Pesanan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/order',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + getToken()
                    },
                    success: function(response) {
                        $('#cartModal').modal('hide');
                        showSweetAlert('Pesanan berhasil dibuat!', 'success');
                        setTimeout(() => {
                            viewOrders();
                        }, 2000);
                    },
                    error: handleAjaxError
                });
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
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                if (response.length === 0) {
                    ordersHtml += '<tr><td colspan="5" class="text-center">Tidak ada pesanan</td></tr>';
                } else {
                    for (let i = 0; i < response.length; i++) {
                        const order = response[i];
                        const orderDate = new Date(order.created_at).toLocaleDateString('id-ID');
                        ordersHtml += `
                            <tr>
                                <td>${order.id}</td>
                                <td>${orderDate}</td>
                                <td>Rp${Number(order.total_amount).toLocaleString('id-ID')}</td>
                                <td>${getStatusBadge(order.status)}</td>
                                <td>
                                    <button class="btn btn-info btn-sm view-order-detail" data-id="${order.id}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        `;
                    }
                }

                ordersHtml += `
                            </tbody>
                        </table>
                    </div>
                `;

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

    // Event handler untuk melihat detail pesanan
    $(document).on('click', '.view-order-detail', function() {
        const orderId = $(this).data('id');
        viewOrderDetail(orderId);
    });

    function viewOrderDetail(orderId) {
        $.ajax({
            url: `/api/order/${orderId}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(response) {
                // Implementasi tampilan detail pesanan
                let detailHtml = `
                    <div class="modal fade" id="orderDetailModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Pesanan #${orderId}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <h6>Status: ${getStatusBadge(response.status)}</h6>
                                    <h6>Tanggal: ${new Date(response.created_at).toLocaleDateString('id-ID')}</h6>
                                    <h6>Total: Rp${Number(response.total_amount).toLocaleString('id-ID')}</h6>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                $('#orderDetailModal').remove();
                // Add new modal to body
                $('body').append(detailHtml);
                // Show the modal
                $('#orderDetailModal').modal('show');
            },
            error: handleAjaxError
        });
    }
});