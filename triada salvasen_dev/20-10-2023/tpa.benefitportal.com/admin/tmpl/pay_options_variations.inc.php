<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr class="data-head">
            <th>Added Date</th>
            <th>ID/Group Name</th>
            <th>ACH</th>
            <th>CC</th>
            <th>Check</th>
            <th width="130px">Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= date('m/d/Y',strtotime($rows['added_date'])); ?></td>
                        <td><a href="groups_details.php?id=<?= md5($rows['group_id']) ?>" class="fw500 text-action"><?= $rows['rep_id'] ?></a><br><?= $rows['business_name'] ?></td>
                        <td><?= ($rows['is_ach'] == 'Y') ? 'Yes' : 'No' ?></td>
                        <td><?= ($rows['is_cc'] == 'Y') ? 'Yes' : 'No' ?></td>
                        <td><?= ($rows['is_check'] == 'Y') ? 'Yes' : 'No' ?></td>
                        <td class="icons">
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="Clone" class="clone_pay_options" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-clone" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit" class="edit_pay_options" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="javascript:void(0);"  data-toggle="tooltip" data-placement="top" data-container="body" title="Delete" class="delete_pay_options" data-rule-id="<?= $rows['id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
    $(document).off('click', '#pay_options_variations_div ul.pagination li a');
    $(document).on('click', '#pay_options_variations_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#pay_options_variations_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#pay_options_variations_div').html(res).show();
                common_select();
            }
        });
    });



    $(document).off("click",".delete_pay_options");
    $(document).on("click",".delete_pay_options",function(){
        $id=$(this).attr('data-rule-id');
        swal({
            text: "Delete Pay Option: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
        }).then(function() {
           $("#ajax_loader").show();
            $.ajax({
                url:'ajax_delete_pay_option.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                    if(res.status='success'){
                        window.parent.setNotifySuccess("Pay Option Deleted Successfully");
                    }
                    $("#ajax_loader").hide();
                    window.parent.pay_options_variations();
                }
            });
        }, function (dismiss) {
        }); 
    });

    $(document).off("click",".edit_pay_options");
    $(document).on("click",".edit_pay_options",function(){
        $id=$(this).attr('data-rule-id');
        
        $.colorbox({
            href:'add_pay_option_variation.php?id='+$id,
            iframe:true,
            width:"1085px;",
            height:"670px;",
            onClosed:function(){
                window.parent.pay_options_variations();
            }
        });
    });

    $(document).off("click",".clone_pay_options");
    $(document).on("click",".clone_pay_options",function(){
        $id=$(this).attr('data-rule-id');
        
        $.colorbox({
            href:'add_pay_option_variation.php?id='+$id+'&clone=Y',
            iframe:true,
            width:"1085px;",
            height:"670px;",
            onClosed:function(){
                window.parent.pay_options_variations();
            }
        });
    });
</script>