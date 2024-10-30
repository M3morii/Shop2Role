function loadCustomerOrders(status = 'all') {
    console.log('Loading orders with status:', status);
    
    $.ajax({
        url: '/api/admin/orders',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        data: { status: status },
        success: function(response) {
            console.log('Orders response:', response);
            
            let ordersHtml = '';
            const orders = Array.isArray(response) ? response : 
                          (response.orders ? response.orders : []);

            if (orders.length > 0) {
                orders.forEach((order, index) => {
                    const statusBadgeClass = {
                        'pending': 'badge-warning',
                        'approved': 'badge-success',
                        'declined': 'badge-danger'
                    }[order.status] || 'badge-secondary';

                    ordersHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${new Date(order.created_at).toLocaleDateString('id-ID')}</td>
                            <td>${order.user ? order.user.name : '-'}</td>
                            <td>${order.item ? order.item.name : '-'}</td>
                            <td>${order.quantity}</td>
                            <td>${formatCurrency(order.price)}</td>
                            <td><span class="badge ${statusBadgeClass}">${order.status}</span></td>
                            <td>
                                ${order.status === 'pending' ? `
                                    <button class="btn btn-sm btn-success approve-order" data-id="${order.id}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger decline-order" data-id="${order.id}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : '-'}
                            </td>
                        </tr>
                    `;
                });
            } else {
                ordersHtml = `
                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada pesanan ${status !== 'all' ? `dengan status ${status}` : ''}
                        </td>
                    </tr>
                `;
            }
            
            $('#orderTableBody').html(ordersHtml);
        },
        error: function(xhr) {
            console.error('Error loading orders:', xhr);
            $('#orderTableBody').html(`
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        Gagal memuat data pesanan: ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </td>
                </tr>
            `);
        }
    });
}

// Helper function format currency
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

// Event handlers
$(document).on('change', '#orderStatusFilter', function() {
    loadCustomerOrders($(this).val());
});

$(document).on('click', '.approve-order', function() {
    const orderId = $(this).data('id');
    approveOrder(orderId);
});

$(document).on('click', '.decline-order', function() {
    const orderId = $(this).data('id');
    declineOrder(orderId);
});

function approveOrder(orderId) {
    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin menyetujui pesanan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/orders/${orderId}/approve`,
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pesanan berhasil disetujui'
                    });
                    loadCustomerOrders($('#orderStatusFilter').val());
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal menyetujui pesanan'
                    });
                }
            });
        }
    });
}

function declineOrder(orderId) {
    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin menolak pesanan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/orders/${orderId}/decline`,
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pesanan berhasil ditolak'
                    });
                    loadCustomerOrders($('#orderStatusFilter').val());
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal menolak pesanan'
                    });
                }
            });
        }
    });
} 