// Search Event Handlers
$(document).on('keydown', '#searchInput', function(e) {
    if (e.keyCode === 13) { // Enter key
        e.preventDefault();
        handleSearch($(this).val().trim());
    }
});

$(document).on('input', '#searchInput', function() {
    if ($(this).val().trim() === '') {
        handleEmptySearch();
    }
});

// Pagination Event Handler
$(document).on('click', '.page-link', function(e) {
    e.preventDefault();
    handlePagination($(this));
});

// Sorting Event Handler
$(document).on('click', 'th[data-sort]', function() {
    handleSorting($(this));
});

// Handler Functions
function handleSearch(searchTerm) {
    console.log('Searching for:', searchTerm);
    
    if (searchTerm === '') {
        handleEmptySearch();
        return;
    }

    showLoadingMessage();
    loadItems(1, searchTerm, currentSort, currentOrder);
}

function handleEmptySearch() {
    console.log('Search bar kosong, menampilkan semua item');
    loadItems(1, '', currentSort, currentOrder);
}

function handlePagination($element) {
    const page = $element.data('page');
    const searchTerm = $('#searchInput').val().trim();
    loadItems(page, searchTerm, currentSort, currentOrder);
}

function handleSorting($element) {
    const sort = $element.data('sort');
    
    // Update sort order
    if (sort === currentSort) {
        currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort = sort;
        currentOrder = 'asc';
    }

    // Update UI to show sort direction
    updateSortIndicators($element);
    
    // Reload items with new sort
    loadItems(1, $('#searchInput').val().trim(), currentSort, currentOrder);
}

// UI Helper Functions
function showLoadingMessage() {
    $('#itemTable').html(`
        <tr>
            <td colspan="8" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="mt-2">Mencari...</div>
            </td>
        </tr>`);
}

function updateSortIndicators($element) {
    // Remove all sort indicators first
    $('th[data-sort]').find('.sort-indicator').remove();
    
    // Add indicator to current sort column
    const indicator = currentOrder === 'asc' ? '↑' : '↓';
    $element.append(`<span class="sort-indicator ml-1">${indicator}</span>`);
}

// Initialize search functionality
function initializeSearch() {
    // Clear search input
    $('#searchInput').val('');
    
    // Reset sort
    currentSort = '';
    currentOrder = 'asc';
    
    // Remove all sort indicators
    $('th[data-sort]').find('.sort-indicator').remove();
}

// Export variables and functions that need to be globally accessible
window.currentSort = '';
window.currentOrder = 'asc';
window.initializeSearch = initializeSearch;