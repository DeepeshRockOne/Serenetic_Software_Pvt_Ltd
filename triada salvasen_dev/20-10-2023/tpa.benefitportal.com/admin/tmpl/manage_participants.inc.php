<div class="panel panel-default panel-block">
    <div class="panel-body">
        <form id="frmSearch" action="manage_participants.php" method="GET" class="sform">
            <input type="hidden" name="is_ajaxed" id="participants_is_ajaxed" value="1"/>
            <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
            <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
            <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>"/>
        </form>                
        <div id="participants_ajax_data"></div>
    </div>
</div>
<div  class="white-box"> 
   <div class="clearfix m-b-20">
        <div class="pull-left">
            <h4 class="mn">Participants Agreement</h4>
            <p class="mn">The terms a user will agree to when adding participants to the account.</p>
        </div>
        <a href="javascript:void(0);"  id="edit_terms" class="pull-right"></a>
    </div>
    <textarea rows="13" class="summernote" id="participants_agreement" name="participants_agreement">
    <?=$participants_agreement?>
    </textarea>
</div>
<script type="text/javascript">
$(document).ready(function() {    
    initCKEditor("participants_agreement",true);
    participants_ajax_submit();

    setInterval(function () {
        participants_ajax_submit();
    },60000);

    $(document).on('click','.btn_cancel_import',function(){
        var file_id = $(this).attr('data-id');
        swal({
            text: "Cancel Import Request: Are you sure?",
            showCancelButton: true,
            confirmButtonColor: "#bd4360",
            confirmButtonText: "Confirm",
        }).then(function () {
            $('#ajax_loader').show();
            $.ajax({
                url: "ajax_cancel_participation_import.php",
                data: {
                    file_id: file_id,
                    action:"cancelRequest"
                },
                method: 'POST',
                dataType: 'json',
                success: function (res) {
                    $('#ajax_loader').hide();
                    if(res.status == "success") {
                        setNotifySuccess(res.message);
                    } else {
                        setNotifyError(res.message);
                    }
                    participants_ajax_submit();
                }
            });
        });
    });

    $(document).on('click','.btn_delete_import',function(){
        var file_id = $(this).attr('data-id');
        swal({
            text: "Delete Import Request: Are you sure?",
            showCancelButton: true,
            confirmButtonColor: "#bd4360",
            confirmButtonText: "Confirm",
        }).then(function () {
            $('#ajax_loader').show();
            $.ajax({
                url: "ajax_cancel_participation_import.php",
                data: {
                    file_id: file_id,
                    action:"deleteRequest"
                },
                method: 'POST',
                dataType: 'json',
                success: function (res) {
                    $('#ajax_loader').hide();
                    if(res.status == "success") {
                        setNotifySuccess(res.message);
                    } else {
                        setNotifyError(res.message);
                    }
                    participants_ajax_submit();
                }
            });
        });
    });

    $(document).off('click', '#participants_ajax_data ul.pagination li a');
    $(document).on('click', '#participants_ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#participants_ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#participants_ajax_data').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

    $("#edit_terms").addClass('pull-right fa fa-edit fs18 m-t-15 edit_term');

    $(document).off('click', '#edit_terms');
    $(document).on('click', '#edit_terms', function(e) {
        if ($(this).hasClass('edit_term')) {
            CKEDITOR.instances['participants_agreement'].setReadOnly(false);
            $("#edit_terms").removeClass('edit_term pull-right fa fa-edit fs18 m-t-15');
            $("#edit_terms").addClass('pull-right btn btn-info save_term m-t-7').text('Save');
        } else {
            CKEDITOR.instances['participants_agreement'].setReadOnly(true);
            $("#edit_terms").removeClass('pull-right btn btn-info save_term').text('');;
            $("#edit_terms").addClass('pull-right fa fa-edit fs18 m-t-15 edit_term');
            $('#ajax_loader').show();

            var participantss_agreement = CKEDITOR.instances.participants_agreement.getData();
            $.ajax({
                url: 'ajax_update_participants_agreement.php',
                data: {
                    participants_agreement: participantss_agreement
                },
                type: 'POST',
                success: function(res) {
                    $('#ajax_loader').hide();
                    if(res.status='success'){
                        setNotifySuccess(res.msg);
                        CKEDITOR.instances['participants_agreement'].setReadOnly(true);
                    }else{
                        setNotifyError(res.msg);
                    }
                }
            })
        }
    });
}); 
function participants_ajax_submit() {
    $('#ajax_loader').show();
    $('#participants_ajax_data').hide();
    $('#participants_is_ajaxed').val('1');
    $("#participants_agreement").val(CKEDITOR.instances.participants_agreement.getData());
    var params = $('#frmSearch').serialize();
    $.ajax({
        url: $('#frmSearch').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#participants_ajax_data').html(res).show();

            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    return false;
}
</script>


