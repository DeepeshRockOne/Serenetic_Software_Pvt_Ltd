<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr class="data-head">
            <th>Added Date/Group ID</th>
            <th>Group Name</th>
            <th>Admin Name</th>
            <th width="130px">Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><a href="groups_details.php?id=<?= md5($rows['group_id']) ?>" class="fw500 text-action"><?= $rows['rep_id'] ?></a><br><?= date('m/d/Y',strtotime($rows['added_date'])); ?></td>
                        <td><?= $rows['business_name'] ?></td>
                        <td><?= $rows['admin_name'] ?></td>
                        <td class="icons">
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="Clone" class="clone_listbill_options" data-rule-id="<?=$rows['id']?>"><i class="fa fa-clone" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit" class="edit_listbill_options" data-rule-id="<?=$rows['id']?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);"  data-toggle="tooltip" data-placement="top" data-container="body" title="Delete" class="delete_listbill_options" data-rule-id="<?=$rows['id']?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="6" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
        <?php 
        if ($total_rows > 0) {?>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php }?>
    </table>
</div>

<script type="text/javascript">
    
    $(document).off('click', '#listbill_variations_div ul.pagination li a');
    $(document).on('click', '#listbill_variations_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#listbill_variations_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#listbill_variations_div').html(res).show();
                common_select();
            }
        });
    });

    $(document).off("click",".edit_listbill_options");
    $(document).on("click",".edit_listbill_options",function(){
        $id=$(this).attr('data-rule-id');
        
        $.colorbox({
            href:'add_listbill_variations.php?id='+$id,
            iframe:true,
            width:"1085px;",
            height:"600px;",
            onClosed:function(){
                window.parent.listbill_variations();
            }
        });
    });

    $(document).off("click",".clone_listbill_options");
    $(document).on("click",".clone_listbill_options",function(){
        $id=$(this).attr('data-rule-id');
        
        $.colorbox({
            href:'add_listbill_variations.php?id='+$id+'&clone=Y',
            iframe:true,
            width:"1085px;",
            height:"600px;",
            onClosed:function(){
                window.parent.listbill_variations();
            }
        });
    });

    $(document).off("click",".delete_listbill_options");
    $(document).on("click",".delete_listbill_options",function(){
        $id=$(this).attr('data-rule-id');
        swal({
            text: "Delete List Bill Option: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
        }).then(function() {
           $("#ajax_loader").show();
            $.ajax({
                url:'ajax_delete_listbill_option.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                    if(res.status='success'){
                        window.parent.setNotifySuccess("List Bill Option Deleted Successfully");
                    }
                    $("#ajax_loader").hide();
                    window.parent.listbill_variations();
                }
            });
        }, function (dismiss) {
        }); 
    });

</script>