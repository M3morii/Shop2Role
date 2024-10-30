<!-- Modal Tambah Item Baru -->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Tambah Barang Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addItemForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="addItemName">Nama:</label>
                        <input type="text" class="form-control" id="addItemName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="addItemDescription">Deskripsi:</label>
                        <textarea class="form-control" id="addItemDescription" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="addItemCategory">Kategori:</label>
                        <select class="form-control" id="addItemCategory" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addItemSellPrice">Harga Jual:</label>
                        <input type="number" class="form-control" id="addItemSellPrice" name="sellprice" required>
                    </div>
                    <div class="form-group">
                        <label for="addItemStock">Stock Awal:</label>
                        <input type="number" class="form-control" id="addItemStock" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="addItemFiles">File (Gambar/Video):</label>
                        <input type="file" class="form-control-file" id="addItemFiles" name="files[]" multiple>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveNewItem">Simpan</button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal Edit Stock -->
<div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockModalLabel">Edit Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editStockForm">
                    <input type="hidden" id="editStockItemId">
                    <div class="form-group">
                        <label>Stok Saat Ini: <span id="currentStock"></span></label>
                    </div>
                    <div class="form-group">
                        <label>Tipe Stok:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="stockType" id="stockTypeIn" value="in" checked>
                            <label class="form-check-label" for="stockTypeIn">
                                Masuk (Tambah stok)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="stockType" id="stockTypeOut" value="out">
                            <label class="form-check-label" for="stockTypeOut">
                                Keluar (Kurangi stok)
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stockQuantity">Jumlah:</label>
                        <input type="number" class="form-control" id="stockQuantity" required min="1">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveStockChanges">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Item -->
<div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">Edit Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editItemForm">
                    <input type="hidden" id="editItemId" name="id">
                    <div class="form-group">
                        <label for="editItemName">Nama Item</label>
                        <input type="text" class="form-control" id="editItemName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editItemDescription">Deskripsi</label>
                        <textarea class="form-control" id="editItemDescription" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editItemSellPrice">Harga Jual</label>
                        <input type="number" class="form-control" id="editItemSellPrice" name="sellprice" required>
                    </div>
                    <div class="form-group">
                        <label for="editItemCategory">Kategori</label>
                        <select class="form-control" id="editItemCategory" name="category_id" required>
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editItemFiles">Gambar</label>
                        <div id="currentImages" class="mb-2">
                            <!-- Existing images will be loaded here -->
                        </div>
                        <input type="file" class="form-control" id="editItemFiles" name="files[]" multiple accept="image/*">
                        <small class="form-text text-muted">Upload gambar baru akan menggantikan gambar yang ada</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveItemChanges">Simpan</button>
            </div>
        </div>
    </div>
</div>