<div id="stockModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center" id="head">Stock Details</h6>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table-hover table stockDetails" id="stockDetails" style="width:100%">
                    <thead class="thead-dark">
                        <th>Date</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Serial</th>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <input type="button" style="display:" class="btn btn-primary use_Btn mr-auto" id="use_Btn" class="button" value="Use" disabled>
                <input type="button" style="display:" class="btn btn-primary pull_Btn mr-auto" id="pull_Btn" class="button" value="Pullout" disabled>
                <input type="button" class="btn btn-primary def_Btn" id="def_Btn" class="button" value="Defective" disabled>
            </div>
        </div>
    </div>
</div>