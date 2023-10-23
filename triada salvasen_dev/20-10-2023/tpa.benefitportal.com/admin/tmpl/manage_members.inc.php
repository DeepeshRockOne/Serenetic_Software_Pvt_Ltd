<div class="panel panel-default panel-block">
    <div class="panel-body">
        <div class="clearfix m-b-15">
            <div class="pull-left">
                <h4 class="mn">Termination Reasons</h4>
                <p class="mn">Set termination reason(s) below for when an admin terms a product.</p>
            </div>
            <div class="pull-right">
                <a href="termination_add.php?action=Add" class="termination_add btn btn-action">+ Termination Reason</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="<?=$table_class?>">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th width="90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($reasions)){
                        foreach($reasions as $res){ ?> 
                        <tr>
                            <td><?=$res['name']?></td>
                            <td class="icons">
                                <a href="termination_add.php?id=<?=$res['id']?>&action=Edit" class="termination_add" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                <a href="javascript:void(0);" data-toggle="tooltip" class="termination_delete" onclick="delete_reason('<?=$res['id']?>')" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } }else echo "<tr><td colspan='2'>No record found!</td></tr>" ?>
                    <!-- <tr>
                        <td>Reason 001</td>
                        <td class="icons">
                        <a href="termination_add.php" class="termination_add" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr> -->
                </tbody>
            </table>
        </div>

<div class="clearfix m-b-15 m-t-15">
        <div class="pull-left">
            <h4 class="mn">Member Agreement</h4>
            <p class="mn">The terms a member will agree to upon enrolling.</p>
        </div>
        <div class="pull-right"> <a href="javascript:void(0);"  id="edit_terms" data-id="<?=$res_t['id']?>" data-type="Member"></i></a> </div>
    </div>

    <textarea rows="13" class="summernote" id="member_terms" name="member_terms">
      <?=!empty($res_t['terms']) ? $res_t['terms'] : '' ?>
    </textarea>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {
  initCKEditor("member_terms",true);
$("#edit_terms").addClass('fa fa-edit fs18 edit_term');
}); 
$(document).off('click', '.termination_add');
    $(document).on('click', '.termination_add', function (e) {
        e.preventDefault();
        $.colorbox({
            href: $(this).attr('href'),
            iframe: true, 
            width: '515px', 
            height: '220px',
            onClosed: function() {
                // window.location.reload();
            }
        });
    });

$(document).off('click', '#edit_terms');
$(document).on('click', '#edit_terms', function(e) {
if ($(this).hasClass('edit_term')) {
    CKEDITOR.instances['member_terms'].setReadOnly(false);
    $("#edit_terms").removeClass('edit_term fa fa-edit fs18');
    $("#edit_terms").addClass('btn btn-info save_term').text('Save');
} else { 
    $("#edit_terms").removeClass('btn btn-info save_term').text('');;
    $("#edit_terms").addClass('fa fa-edit fs18 edit_term');
    $('#ajax_loader').show();
    var id = $(this).data('id');
    var type = $(this).data('type');
    var terms = CKEDITOR.instances.member_terms.getData();
    $.ajax({
    url: 'ajax_update_terms.php',
    data: {
        id: id,
        type: type,
        terms: terms
    },
    type: 'POST',
    success: function(res) {
        $('#ajax_loader').hide();
        if(res.status=='success'){
        setNotifySuccess(res.msg);
        CKEDITOR.instances['member_terms'].setReadOnly(true);
        }else{
        setNotifyError(res.msg);
        }
    }
    });
}
});

function delete_reason(reason_id) {
    swal({
        text: 'Delete Reason: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function() {
        $("#ajax_loader").show();
        $.ajax({
            url: "termination_add.php",
            type: 'GET',
            data: {
                id: reason_id,
                action:'delete',
                is_ajaxed: 1
            },
            dataType: 'JSON',
            success: function(res) {
                $("#ajax_loader").hide();
                if (res.status == 'success') {
                    setNotifySuccess(res.msg);
                } else {
                    setNotifyError(res.msg);
                }
                location.reload();
            }
        });
    }, function(dismiss) {})
}
</script>


