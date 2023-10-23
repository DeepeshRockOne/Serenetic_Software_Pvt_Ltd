<div class="panel panel-default panel-block">
    <div class="panel-body">
        <form id="lead_frm_search" action="manage_leads.php" method="GET" class="sform">
            <input type="hidden" name="is_ajaxed" id="lead_is_ajaxed" value="1"/>
            <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
            <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
            <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>"/>
        </form>                
        <div id="lead_ajax_data"></div>
    </div>
</div>
<div  class="white-box"> 
   <div class="clearfix m-b-15">
        <div class="pull-left">
            <h4 class="mn">Lead Agreement</h4>
            <p class="mn">The terms a user will agree to when adding leads to the account.</p>
        </div>
        <div class="pull-right">
            <a href="javascript:void(0);"  id="edit_terms"></a>
        </div>
    </div>
    <textarea rows="13" class="summernote" id="lead_agreement" name="lead_agreement">
    <?=$lead_agreement?>
    </textarea>
</div>
<script type="text/javascript">
$(document).ready(function() {    
    initCKEditor("lead_agreement",true);
    lead_ajax_submit();

    setInterval(function () {
        lead_ajax_submit();
    },20000);

    $(document).on('click','.btn_cancel_import',function(){
        var file_id = $(this).attr('data-id');
        swal({
            text: "Cancel Import: Are you sure?",
            showCancelButton: true,
            confirmButtonText: 'Confirm'
        }).then(function () {
            $('#ajax_loader').show();
            $.ajax({
                url: "cancel_csv_agent_lead_import.php?id=" + file_id,
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if(res.status == "success") {
                        setNotifySuccess(res.msg);
                    } else {
                        setNotifyError(res.msg);
                    }
                    lead_ajax_submit();
                }
            });
        }, function (dismiss) {

        });
    });

    $(document).on('click','.btn_delete_import',function(){
        var file_id = $(this).attr('data-id');
        swal({
            text: "Delete Import Request: Are you sure?",
            showCancelButton: true,
            confirmButtonText: 'Confirm'
        }).then(function () {
            $('#ajax_loader').show();
            $.ajax({
                url: "cancel_csv_agent_lead_import.php?id=" + file_id +"&action=delete",
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if(res.status == "success") {
                        setNotifySuccess(res.msg);
                    } else {
                        setNotifyError(res.msg);
                    }
                    lead_ajax_submit();
                }
            });
        }, function (dismiss) {

        });
    });

    $(document).off('click', '#lead_ajax_data ul.pagination li a');
    $(document).on('click', '#lead_ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#lead_ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#lead_ajax_data').html(res).show();
            }
        });
    });

    $("#edit_terms").addClass('fa fa-edit fs18 edit_term');

    $(document).off('click', '#edit_terms');
    $(document).on('click', '#edit_terms', function(e) {
        if ($(this).hasClass('edit_term')) {
            CKEDITOR.instances['lead_agreement'].setReadOnly(false);
            $("#edit_terms").removeClass('edit_term fa fa-edit fs18 ');
            $("#edit_terms").addClass('btn btn-info save_term').text('Save');
        } else {
            CKEDITOR.instances['lead_agreement'].setReadOnly(true);
            $("#edit_terms").removeClass('pull-right btn btn-info save_term').text('');;
            $("#edit_terms").addClass('fa fa-edit fs18 edit_term');
            $('#ajax_loader').show();

            var leads_agreement = CKEDITOR.instances.lead_agreement.getData();
            $.ajax({
                url: 'ajax_update_lead_agreement.php',
                data: {
                    lead_agreement: leads_agreement
                },
                type: 'POST',
                success: function(res) {
                    $('#ajax_loader').hide();
                    if(res.status='success'){
                        setNotifySuccess(res.msg);
                        CKEDITOR.instances['lead_agreement'].setReadOnly(true);
                    }else{
                        setNotifyError(res.msg);
                    }
                }
            })
        }
    });
}); 
function lead_ajax_submit() {
    $('#ajax_loader').show();
    $('#lead_ajax_data').hide();
    $('#lead_is_ajaxed').val('1');
    $("#lead_agreement").val(CKEDITOR.instances.lead_agreement.getData());
    var params = $('#lead_frm_search').serialize();
    $.ajax({
        url: $('#lead_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#lead_ajax_data').html(res).show();

            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    return false;
}
</script>


