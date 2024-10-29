// Fungsi untuk mengelola item
const itemManager = {
    loadItems: function() {
        $.ajax({
            url: '/api/items',
            method: 'GET',
            success: function(data) {
                $('#mainContent').html(data);
            },
            error: function(error) {
                console.error('Error fetching items:', error);
            }
        });
    },
    
    addItem: function(formData) {
        $.ajax({
            url: '/api/items',
            method: 'POST',
            data: formData,
            success: function(response) {
                // Handle success
            },
            error: function(error) {
                // Handle error
            }
        });
    }
};

// Fungsi untuk mengelola pesanan
const orderManager = {
    loadOrders: function() {
        $.ajax({
            url: '/api/orders',
            method: 'GET',
            success: function(data) {
                $('#mainContent').html(data);
            },
            error: function(error) {
                console.error('Error fetching orders:', error);
            }
        });
    }
};

// Event handlers
$(document).ready(function() {
    // Handle menu clicks
    $('#manageItems').click(function() {
        itemManager.loadItems();
    });

    $('#manageOrders').click(function() {
        orderManager.loadOrders();
    });

    // Handle form submissions
    $('#addItemForm').submit(function(e) {
        e.preventDefault();
        itemManager.addItem(new FormData(this));
    });
}); 