<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr class="data-head">
            <th>Added Date</th>
            <th>ID/Group Name</th>
            <th>Product(s)</th>
            <th width="130px">Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <?php $product_arr = !empty($rows['products']) ? explode(",", $rows['products']) : array(); ?>
                    <tr>
                        <td><?= date('m/d/Y',strtotime($rows['added_date'])); ?></td>
                        <td><a href="groups_details.php?id=<?= md5($rows['group_id']) ?>" class="fw500 text-action"><?= $rows['rep_id'] ?></a><br><?= $rows['business_name'] ?></td>
                        <td><?= count($product_arr) ?></td>
                        <td class="icons">
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="Clone" class="clone_group_contribution" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-clone" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit" class="edit_group_contribution" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);"  data-toggle="tooltip" data-placement="top" data-container="body" title="Delete" class="delete_group_contribution" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="4" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
        <?php 
        if ($total_rows > 0) {?>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php }?>
    </table>
</div>

<script type="text/javascript">
    $(document).off('click', '#group_contribution_variations_div ul.pagination li a');
    $(document).on('click', '#group_contribution_variations_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#group_contribution_variations_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#group_contribution_variations_div').html(res).show();
                common_select();
            }
        });
    });



    $(document).off("click",".delete_group_contribution");
    $(document).on("click",".delete_group_contribution",function(){
        $id=$(this).attr('data-rule-id');
        swal({
            text: "Delete Group Contribution: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
        }).then(function() {
           $("#ajax_loader").show();
            $.ajax({
                url:'ajax_delete_group_contribution.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                    if(res.status='success'){
                        window.parent.setNotifySuccess("Group Contribution Deleted Successfully");
                    }
                    $("#ajax_loader").hide();
                    window.parent.group_contribution_variations();
                }
            });
        }, function (dismiss) {
        }); 
    });

    $(document).off("click",".edit_group_contribution");
    $(document).on("click",".edit_group_contribution",function(){
        $id=$(this).attr('data-rule-id');
        
        $.colorbox({
            href:'add_group_contribution_variation.php?id='+$id,
            iframe:true,
            width:"1085px;",
            height:"670px;",
            onClosed:function(){
                window.parent.group_contribution_variations();
            }
        });
    });

    $(document).off("click",".clone_group_contribution");
    $(document).on("click",".clone_group_contribution",function(){
        $id=$(this).attr('data-rule-id');
        
        $.colorbox({
            href:'add_group_contribution_variation.php?id='+$id+'&clone=Y',
            iframe:true,
            width:"1085px;",
            height:"670px;",
            onClosed:function(){
                window.parent.group_contribution_variations();
            }
        });
    });
</script>