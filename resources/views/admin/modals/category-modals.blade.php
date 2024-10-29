<!-- Modal Kategori -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Kelola Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="categoryId">
                        <div class="form-group">
                            <label for="categoryName">Nama Kategori:</label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                    </form>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="categoryList">
                            <!-- Daftar kategori akan dimuat di sini -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveCategoryChanges">Simpan</button>
                </div>
            </div>
        </div>
    </div>