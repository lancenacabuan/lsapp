<div class="modal fade in" id="importStock">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">ADD STOCK VIA IMPORT FILE</h6>
                <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form id="formUpload" action="/stocks/import" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row no-margin">
                        <div class="col-md-12 form-group">
                            <input type="file" id="xlsx" name="xlsx" class="form-control" onchange="validate_xlsx(this);" required/>
                        </div>
                        <span style="color: Red; font-size: 14px;">Please upload an EXCEL (.xlx/.xlsx) file with less than 10MB.</span>
                    </div>
                    <br>
                    <button type="button" id="btnDetach" class="btn btn-primary bp">RESET</button>
                    <button type="button" id="btnUpload" class="btn btn-primary bp float-right">UPLOAD</button>
                    <input type="submit" id="btnSubmit" class="btn btn-primary bp float-right d-none"/>
                </form>
            </div>
        </div>
    </div>
</div>