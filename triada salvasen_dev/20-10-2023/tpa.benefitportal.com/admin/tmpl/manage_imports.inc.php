<div class="panel panel-default panel-block">
    <div class="panel-body">
        <form id="lead_frm_search" action="manage_imports.php" method="GET" class="sform">
            <input type="hidden" name="is_ajaxed" id="lead_is_ajaxed" value="1"/>
            <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
            <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
            <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>"/>
        </form>                
        <div id="imports_ajax_data"></div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {   
    dropdown_pagination('imports_ajax_data');
    imports_ajax_submit();
    setInterval(function () {
        imports_ajax_submit();
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
                url: "cancel_csv_import.php?id=" + file_id,
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if(res.status == "success") {
                        setNotifySuccess(res.msg);
                    } else {
                        setNotifyError(res.msg);
                    }
                    imports_ajax_submit();
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
           /* $.ajax({
                url: "cancel_csv_agent_lead_import.php?id=" + file_id +"&action=delete",
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if(res.status == "success") {
                        setNotifySuccess(res.msg);
                    } else {
                        setNotifyError(res.msg);
                    }
                    imports_ajax_submit();
                }
            });*/
        }, function (dismiss) {

        });
    });

    $(document).off('click', '#imports_ajax_data ul.pagination li a');
    $(document).on('click', '#imports_ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#imports_ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#imports_ajax_data').html(res).show();
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

}); 
function imports_ajax_submit() {
    $('#ajax_loader').show();
    $('#imports_ajax_data').hide();
    $('#lead_is_ajaxed').val('1');
    var params = $('#lead_frm_search').serialize();
    $.ajax({
        url: $('#lead_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#imports_ajax_data').html(res).show();
            $('[data-toggle="tooltip"]').tooltip();
            common_select();
        }
    });
    return false;
}
</script>


