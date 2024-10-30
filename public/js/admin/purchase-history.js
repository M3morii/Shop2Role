function loadPurchaseHistory() {
    console.log('Loading purchase history...');
    
    $.ajax({
        url: '/api/admin/purchase-history',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            console.log('Purchase history response:', response);
            
            let historyHtml = '';
            const histories = Array.isArray(response) ? response : 
                            (response.data ? response.data : []);
            
            if (histories.length > 0) {
                historyHtml = histories.map((history, index) => {
                    const date = new Date(history.created_at).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    
                    return `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${date}</td>
                            <td>${history.item ? history.item.name : 'Item tidak ditemukan'}</td>
                            <td>${history.quantity}</td>
                            <td>
                                <span class="badge badge-${history.type === 'in' ? 'success' : 'danger'}">
                                    ${history.type === 'in' ? 'Masuk' : 'Keluar'}
                                </span>
                            </td>
                            <td>${history.description || '-'}</td>
                        </tr>`;
                }).join('');
            } else {
                historyHtml = `
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="alert alert-info mb-0">
                                Tidak ada riwayat pembelian
                            </div>
                        </td>
                    </tr>`;
            }
            
            $('#purchaseHistoryList').html(historyHtml);
        },
        error: function(xhr) {
            console.error('Error loading purchase history:', xhr);
            $('#purchaseHistoryList').html(`
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="alert alert-danger mb-0">
                            Gagal memuat riwayat pembelian: ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                        </div>
                    </td>
                </tr>
            `);
        }
    });
} 