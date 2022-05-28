<div class="modal fade in" id="reportModal">
    <div class="modal-dialog modal-dialog-centered modal-m">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REPORT A PROBLEM</h6>            
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <textarea style="margin-bottom: 8px; font-size: 14px; resize: none;" class="form-control" rows="5" name="report" id="report" maxlength="300" autocomplete="off" placeholder="Please describe the error or bug that you have encountered."></textarea>
            <span style="color: Red; font-size: 12px;">*Required Field</span><br>
            <span id='textlimit' style="font-size: 12px;"></span>
            <button type="button" id="btnSupport" class="btn btn-primary float-right bp">SUBMIT</button>
            <span class="float-right" style="margin-right: 5px;">&nbsp;</span>
            <button type="button" id="btnResetReport" class="btn btn-primary float-right bp">RESET</button><br><br>
            <span style="font-size: 14px;">You may also contact us at our Viber numbers below for chat support.</span>
            <div class="my-2" style="height: 50px; line-height: 50px;">
                <img src="{{asset('viber.png')}}" style="width: auto; height: 50px;">
                <span class="ml-2" style="font-size: 14px;">0999-220-6507 / 0998-848-8624 / 0946-5656-535</span>
            </div>
        </div>
    </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        var max = 300;
        $('#textlimit').html(max + ' characters remaining');

        $('#report').keyup(function(){
            var text_length = $('#report').val().length;
            var text_remaining = max - text_length;

            $('#textlimit').html(text_remaining + ' characters remaining');
        });
    });

    $('#btnReport').on('click', function(){
        $('#reportModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#reportModal').modal('show');
        $('#report').val('');
        $('#report').focus();
        max = 300;
        $('#textlimit').html(max + ' characters remaining');
    });

    $('#btnResetReport').on('click', function(){
        $('#report').val('');
        $('#report').focus();
        max = 300;
        $('#textlimit').html(max + ' characters remaining');
    });
</script>