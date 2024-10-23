$(document).ready(function() {
    var token = sessionStorage.getItem('access_token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    let currentSort = '';
    let currentOrder = 'asc';

    function loadItems(page = 1, search = '', sort = '', order = '') {
        // ... (kode loadItems yang sudah ada)
    }

    function loadCustomerOrders(status = 'all') {
        // ... (kode loadCustomerOrders yang sudah ada)
    }

    // Event listeners
    $('th[data-sort]').click(function() {
        // ... (kode event listener yang sudah ada)
    });

    $('#saveStockChanges').click(function() {
        // ... (kode saveStockChanges yang sudah ada)
    });

    $('#saveItemChanges').click(function() {
        // ... (kode saveItemChanges yang sudah ada)
    });

    $('#addNewItem').click(function() {
        $('#addItemModal').modal('show');
    });

    $('#saveNewItem').click(function() {
        // ... (kode saveNewItem yang sudah ada)
    });

    $('#viewCustomerOrders').click(function() {
        // ... (kode viewCustomerOrders yang sudah ada)
    });

    $(document).on('click', '.approve-order', function() {
        // ... (kode approve-order yang sudah ada)
    });

    $(document).on('click', '.decline-order', function() {
        // ... (kode decline-order yang sudah ada)
    });

    $('#searchInput').on('keyup', function() {
        // ... (kode searchInput yang sudah ada)
    });

    $(document).on('click', '.pagination a', function(e) {
        // ... (kode pagination yang sudah ada)
    });

    $('#viewPurchaseHistory').click(function() {
        // ... (kode viewPurchaseHistory yang sudah ada)
    });

    // Initial load
    loadItems();
});
