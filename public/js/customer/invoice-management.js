function getToken() {
    return sessionStorage.getItem('access_token');
}

$(document).ready(function() {
    window.viewInvoice = function() {
        $.ajax({
            url: '/api/invoice',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(response) {
                let invoiceHtml = `
                    <div class="modal fade" id="invoiceModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Daftar Invoice</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                `;

                if (response.length === 0) {
                    invoiceHtml += '<p class="text-center">Tidak ada invoice</p>';
                } else {
                    invoiceHtml += `
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No. Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    response.forEach(invoice => {
                        const invoiceDate = new Date(invoice.created_at).toLocaleDateString('id-ID');
                        
                        invoiceHtml += `
                            <tr>
                                <td>${invoice.id}</td>
                                <td>${invoiceDate}</td>
                                <td>Rp${Number(invoice.total_amount).toLocaleString('id-ID')}</td>
                                <td>${invoice.status}</td>
                                <td>
                                    <button class="btn btn-info btn-sm view-invoice-detail" data-id="${invoice.id}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    invoiceHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }

                invoiceHtml += `
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                $('#invoiceModal').remove();
                // Add new modal to body
                $('body').append(invoiceHtml);
                // Show the modal
                $('#invoiceModal').modal('show');
            },
            error: handleAjaxError
        });
    };

    // Event handler untuk melihat detail invoice
    $(document).on('click', '.view-invoice-detail', function() {
        const invoiceId = $(this).data('id');
        viewInvoiceDetail(invoiceId);
    });

    function viewInvoiceDetail(invoiceId) {
        $.ajax({
            url: `/api/invoice/${invoiceId}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(response) {
                // Implementasi tampilan detail invoice
                console.log(response);
            },
            error: handleAjaxError
        });
    }
});