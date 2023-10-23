<div class="panel-body">
    <div class="clearfix tbl_filter">
    <div class="pull-left">
        <h4 class="m-t-7">Quick Reply</h4>
    </div>
    <div class="pull-right">
        <div class="m-b-15">
        <div class="note_search_wrap auto_size" id="label_search_div" style="<?=!empty($name) ? 'display:inline-block;'  : 'display: none;'  ?>  max-width: 100%;">
            <div class="phone-control-wrap theme-form">
            <div class="phone-addon">
                <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="label_search_close_btn">X</a>
                </div>
            </div>
            <div class="phone-addon w-300">
                <div class="form-group height_auto mn">
                <input type="text" name="replyName" id="replyName" value="<?=checkIsset($name)?>" class="form-control <?=checkIsset($name)!='' ? 'has-value' : ''?>">
                <label>Quick reply name</label>
                </div>
            </div>
            <div class="phone-addon w-80">
                <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="btn btn-info search_button" onclick="get_eticket_quick_reply()">Search</a>
                </div>
            </div>
            </div>
        </div>
        <a href="javascript:void(0);" class="search_btn" id="label_search_btn" style="<?= !empty($name) ? 'display: none;'  : 'display:inline-block;' ?>"><i class="fa fa-search fa-lg text-blue"></i></a>
        <a href="add_etickets_quick_reply.php" class="btn btn-action m-l-5 add_etickets_quick_reply" style="display:inline-block;" >+ Quick Reply</a>
        </div>
    </div>
    </div>
    <div class="clearfix"></div>
    <div class="table-responsive">
        <table class="<?=$table_class?>">
        <thead>
            <tr>
            <th width="300px">Quick Reply Label</th>
            <th class="text-center" >Reply</th>
            <th width="100px">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($fetch_rows) && $total_rows > 0) {
                foreach($fetch_rows as $rows) {?>
                <tr>
                    <td><?=$rows['title']?></td>
                    <td class="icons text-center">
                        <!-- <a href="etickets_quick_reply_email.php" class="etickets_quick_reply_email" data-toggle="tooltip" data-title="View"><i class="fa fa-eye"></i></a> -->
                        <a href="add_etickets_quick_reply.php?id=<?=$rows['id']?>&type=view" class="etickets_quick_reply_email" data-toggle="tooltip" data-title="View"><i class="fa fa-eye"></i></a>
                    </td>
                    <td class="icons ">
                        <!-- <a href="edit_etickets_quick_reply.php" class="edit_etickets_quick_reply" data-toggle="tooltip" data-title="Edit"><i class="fa fa-edit"></i></a> -->
                        <a href="add_etickets_quick_reply.php?id=<?=$rows['id']?>" class="edit_etickets_quick_reply" data-toggle="tooltip" data-title="Edit"><i class="fa fa-edit"></i></a>
                        <a href="javascript:void(0);" data-toggle="tooltip" data-title="Delete" onclick="delete_e_ticket_quickReply('<?=$rows['id']?>')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
        <?php }} ?>
        </tbody>
        <tfoot>
            <tr>
            <?php if($total_rows > 0 && !empty($fetch_rows)) { ?>
                <td colspan="3">
                <?php echo $paginate->links_html; ?>
                </td>
            <?php }else echo "<td colspan='3'>No record found!</td>"; ?>
            </tr>
        </tfoot>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(".etickets_quick_reply_email").colorbox({iframe: true, width: '800px', height: '550px',overlayClose: false,escKey:false});
    $(".add_etickets_quick_reply").colorbox({iframe: true, width: '1024px', height: '550px',overlayClose: false,escKey:false});
    $(".edit_etickets_quick_reply").colorbox({iframe: true, width: '1024px', height: '550px',overlayClose: false,escKey:false});
    $(document).on("click", "#label_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#label_search_div").css('display', 'inline-block');
    });
    $(document).on("click", "#label_search_close_btn", function(e) {
        e.preventDefault();
        $("#quick_reply_div #replyName").val('');
        $("#label_search_div").hide();
        $("#label_search_btn").show();
        get_eticket_quick_reply();
    });
$(document).off('click', '#quick_reply_div ul.pagination li a');
$(document).on('click', '#quick_reply_div ul.pagination li a', function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#is_ajaxed").val(1);
    $('#quick_reply_div').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        data:{is_ajaxed:1},
        success: function(res) {
            $('#ajax_loader').hide();
            $('#quick_reply_div').html(res).show();
            common_select()
        }
    });
});
delete_e_ticket_quickReply = function(categoryId){
    swal({
      text: "Delete Quick Reply: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
        $.ajax({
            url: 'ajax_add_etickets_quick_reply.php',
            type: 'POST',
            data:{categoryId:categoryId,is_deleted:'Y'},
            dataType:'json',
            beforeSend :function(){
                $('#ajax_loader').show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                if(res.status =='success'){
                    parent.get_eticket_quick_reply();
                    parent.setNotifySuccess("Group deleted successfully.");
                }else{
                    parent.get_eticket_quick_reply();
                    parent.setNotifyError("Something went wrong.");
                }
                
            }
        });
    });
}
</script>