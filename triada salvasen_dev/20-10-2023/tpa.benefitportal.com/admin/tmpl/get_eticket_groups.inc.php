<div class="panel-body">
    <div class="clearfix tbl_filter">
    <div class="pull-left">
        <h4 class="m-t-7">Eticket Groups</h4>
    </div>
    <div class="pull-right">
        <div class="m-b-15">
        <div class="note_search_wrap auto_size" id="name_search_div" style="<?=!empty($rep_id) || !empty($name) ? 'display:inline-block;'  : 'display: none;'  ?> max-width: 100%;">
            <div class="phone-control-wrap theme-form">
            <div class="phone-addon">
                <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="name_search_close_btn">X</a>
                </div>
            </div>
            <div class="phone-addon w-200">
                <div class="form-group height_auto mn">
                <input type="text" name="admindisplayId" id="admindisplayId" value="<?=checkIsset($rep_id)?>" class="form-control <?=!empty($rep_id) ? 'has-value':''?>">
                <label>Admin ID</label>
                </div>
            </div>
                <div class="phone-addon w-200">
                <div class="form-group height_auto mn">
                <input type="text" name="groupName" id="groupName" value="<?=checkIsset($name)?>" class="form-control <?=!empty($name) ? 'has-value':''?>">
                <label>Group Name</label>
                </div>
            </div>
            <div class="phone-addon w-80">
                <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="btn btn-info search_button" onclick="get_eticket_groups()">Search</a>
                </div>
            </div>
            </div>
        </div>
        <a href="javascript:void(0);" class="search_btn" id="name_search_btn" style="<?=!empty($rep_id) || !empty($name) ? 'display: none;'  : 'display:inline-block;' ?>"><i class="fa fa-search fa-lg text-blue"></i></a>
        <a href="add_etickets_groups.php" class="btn btn-action m-l-5 add_etickets_groups" style="display:inline-block;" >+ Group</a>
        </div>
    </div>
    </div>
    <div class="table-responsive">
        <table class="<?=$table_class?>">
        <thead>
            <tr>
            <th width="300px">Group Name</th>
            <th class="text-center">Admins</th>
            <th width="100px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($fetch_rows) && $total_rows > 0) {
                foreach($fetch_rows as $rows) {?>
            <tr>
                <td><?=$rows['title']?></td>
                <td class="text-center">
                    <!-- <a href="etickets_groups_admins.php" class="text-action fw500 etickets_groups_admins">18</a> -->
                    <a href="etickets_groups_admins.php?id=<?=$rows['id']?>&name=<?=$rows['title']?>&total_admin=<?=$rows['totalassignedAdmin']?>" class="text-action fw500 etickets_groups_admins"><?=$rows['totalassignedAdmin']?></a>
                </td>
                <td class="icons">
                    <!-- <a href="edit_etickets_groups.php" class="edit_etickets_groups" data-toggle="tooltip" data-title="Edit"><i class="fa fa-edit"></i></a> -->
                    <a href="add_etickets_groups.php?id=<?=$rows['id']?>" class="edit_etickets_groups" data-toggle="tooltip" data-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" data-toggle="tooltip" data-title="Delete" onclick="delete_e_ticket_group('<?=$rows['id']?>')"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            <?php } } ?>
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
    $(".etickets_groups_admins").colorbox({iframe: true, width: '768px', height: '600px',overlayClose: false,escKey:false});
    $(".add_etickets_groups").colorbox({iframe: true, width: '768px', height: '370px',overlayClose: false,escKey:false});
    $(".edit_etickets_groups").colorbox({iframe: true, width: '768px', height: '370px',overlayClose: false,escKey:false});
    $(document).on("click", "#name_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#name_search_div").css('display', 'inline-block');
    });
    $(document).on("click", "#name_search_close_btn", function(e) {
        e.preventDefault();
        $("#group_div #groupName").val('');
        $("#group_div #admindisplayId").val('');
        $("#name_search_div").hide();
        $("#name_search_btn").show();
        get_eticket_groups();
    });
$(document).off('click', '#group_div ul.pagination li a');
$(document).on('click', '#group_div ul.pagination li a', function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#is_ajaxed").val(1);
    $('#group_div').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        data:{is_ajaxed:1},
        success: function(res) {
            $('#ajax_loader').hide();
            $('#group_div').html(res).show();
            common_select();
        }
    });
});
delete_e_ticket_group = function(categoryId){
    var $href = 'ajax_add_etickets_groups.php?is_deleted=Y&categoryId='+categoryId;
    $.colorbox({
        href:$href,
        iframe: true,
        width: '768px',
        height: '600px',
        overlayClose: false,
        escKey:false
    });
}
</script>