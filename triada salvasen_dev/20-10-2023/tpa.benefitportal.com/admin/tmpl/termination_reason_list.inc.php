<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr class="data-head">
            <th>Name</th>
            <th>Qualifies For COBRA</th>
            <th width="130px">Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= $rows['reason'] ?></td>
                        <td><?= ($rows['is_qualifies_for_cobra'] == 'Y') ? 'Yes' : 'No' ?></td>
                        <td class="icons">
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit" class="edit_termination_reason" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);"  data-toggle="tooltip" data-placement="top" data-container="body" title="Delete" class="delete_termination_reason" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="3" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
        <?php 
        if ($total_rows > 0) {?>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php }?>
    </table>
</div>

<script type="text/javascript">
    $(document).off('click', '#group_termination_reason_div ul.pagination li a');
    $(document).on('click', '#group_termination_reason_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#group_termination_reason_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#group_termination_reason_div').html(res).show();
                common_select();
            }
        });
    });



    $(document).off("click",".delete_termination_reason");
    $(document).on("click",".delete_termination_reason",function(){
        $id=$(this).attr('data-rule-id');
        swal({
            text: "Delete Termination Reason: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
        }).then(function() {
           $("#ajax_loader").show();
            $.ajax({
                url:'ajax_delete_termination_reason.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                    if(res.status='success'){
                        window.parent.setNotifySuccess("Termination Reason Deleted Successfully");
                    }
                    $("#ajax_loader").hide();
                    window.parent.group_termination_reason();
                }
            });
        }, function (dismiss) {
        }); 
    });

    $(document).off("click",".edit_termination_reason");
    $(document).on("click",".edit_termination_reason",function(){
        $id=$(this).attr('data-rule-id');
        
        $.colorbox({
            href:'termination_reasons_group.php?id='+$id,
            iframe:true,
            width:"515px;",
            height:"325px;",
            onClosed:function(){
                window.parent.group_termination_reason();
            }
        });
    });
</script>