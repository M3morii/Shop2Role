<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <h1 class="navbar-brand mb-0">Dashboard Customer</h1>
        <div class="d-flex">
            <button id="viewCart" class="btn btn-primary me-2">Cek Keranjang</button>
            <button id="viewInvoice" class="btn btn-info text-white me-2">Cek Invoice</button>
            <button id="viewOrders" class="btn btn-success me-2">Cek Pesanan</button>
            <button id="logoutBtn" class="btn btn-danger">Logout</button>
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