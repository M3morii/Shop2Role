<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <h1 class="navbar-brand mb-0">
            <i class="bi bi-shop"></i> Dashboard Customer
        </h1>
        <div class="d-flex">
            <button id="viewCart" class="btn btn-outline-primary me-2">
                <i class="bi bi-cart"></i> Keranjang
            </button>
            <button id="viewInvoice" class="btn btn-outline-success me-2">
                <i class="bi bi-receipt"></i> Invoice
            </button>
            <button id="viewOrders" class="btn btn-outline-warning me-2">
                <i class="bi bi-bag"></i> Pesanan
            </button>
            <button id="logoutBtn" class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </div>
    </div>
</nav>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari item..." autocomplete="off">
        </div>
    </div>
    <div class="col-md-4">
        <select class="form-control" id="categoryFilter">
            <option value="">Semua Kategori</option>
        </select>
    </div>
</div>
<div id="messageContainer" class="mt-3"></div>