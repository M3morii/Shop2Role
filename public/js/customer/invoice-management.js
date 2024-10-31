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
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Item</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                if (!response || response.length === 0) {
                    invoiceHtml += '<tr><td colspan="5" class="text-center">Tidak ada riwayat pembelian</td></tr>';
                } else {
                    response.forEach(invoice => {
                        // Format tanggal
                        const date = new Date(invoice.purchase_date);
                        const formattedDate = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
                        
                        // Format items dan quantity
                        const items = invoice.orders.map(order => 
                            `${order.item_name} (${order.quantity} pcs)`
                        ).join('<br>');
                        
                        // Format harga
                        const total = invoice.total_price || 0;
                        
                        invoiceHtml += `
                            <tr>
                                <td>#INV-${invoice.id}</td>
                                <td>${formattedDate}</td>
                                <td>${items}</td>
                                <td>Rp ${total.toLocaleString('id-ID')}</td>
                                <td><span class="badge bg-success">Disetujui</span></td>
                            </tr>
                        `;
                    });
                }

                invoiceHtml += `
                            </tbody>
                        </table>
                    </div>
                `;

                $('#invoiceContent').html(invoiceHtml);
                $('#invoiceModal').modal('show');
            },
            error: handleAjaxError
        });
    };
});