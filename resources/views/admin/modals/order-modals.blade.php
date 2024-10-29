<!-- Modal Pesanan Customer dan Riwayat Pembelian -->
<div class="modal fade" id="customerOrdersModal" tabindex="-1" role="dialog" aria-labelledby="customerOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerOrdersModalLabel">Pesanan Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="orderStatusFilterContainer" class="form-group">
                        <label for="orderStatusFilter">Filter by Status:</label>
                        <select id="orderStatusFilter" class="form-control">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                    <div id="customerOrdersContent">
                        <!-- Orders will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>