<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
            <tr class="data-head">
                <th>Product ID</th>
                <th>Sub Product Name</th>
                <th>Carrier</th>
                <th width="180px">Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $row) { ?>
                    <tr>
                        <td><?php echo $row['product_code']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['carrier_name'].' ('.$row['display_id'].')'; ?></td>
                        <td>
                            <div class="theme-form pr">
                                <select id="is_active_<?= $row['id']; ?>" class="status_s form-control has-value" name="is_active" onchange="confirm_active('<?= $row['id']; ?>', this.value,'<?= $row['status']; ?>')">
                                    <option value="Active" <?php echo ($row['status'] == "Active")?'selected':''?>>Active</option>
                                    <option value="Inactive" <?php echo ($row['status'] == "Inactive")?'selected':''?>>Inactive</option>
                                </select>
                                <label>Status</label>
                            </div>
                        </td>
                        <td class="icons no-wr text-right">
                            <a data-toggle="tooltip" title="Edit Sub Product" class="editSubProduct" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><i class="fa fa-lg fa-edit"></i></a>
                            <a data-toggle="tooltip" title="Delete Sub Product" class="deleteSubProduct" data-id="<?=$row['id']?>" href="javascript:void(0);"><i class="fa fa-lg fa-trash"></i></a>
                        </td>
                    </tr>
                <?php }?>
            <?php } else {?>
            <tr>
                <td colspan="5" class="text-center">No record(s) found</td>
            </tr>
            <?php } ?>
        </tbody>
        <?php
        if ($total_rows > 0) {?>
        <tfoot>
        <tr>
            <td colspan="5">
                <?php echo $paginate->links_html; ?>
            </td>
        </tr>
        </tfoot>
        <?php }?>
    </table>
</div>

<script type="text/javascript">
    $(document).off('click', '#sub_products_div ul.pagination li a');
    $(document).on('click', '#sub_products_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#sub_products_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#sub_products_div').html(res).show();
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

    $(document).off("click",".deleteSubProduct");
    $(document).on("click",".deleteSubProduct",function(){
        $id=$(this).attr('data-id');
          
        swal({
            text: 'Delete  Sub Product: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                url:'ajax_sub_product_delete.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                    $("#ajax_loader").hide();
                    if(res.status='success'){
                        window.parent.setNotifySuccess("Sub Products Deleted Successfully");
                    }
                    window.parent.sub_products();
                }
            });
        }, function (dismiss) {
        });
            
        
    });

    function confirm_active(id, status, old_status) {
        swal({
          text: "Change Status: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
          showCloseButton: true
        }).then(function() {
          $.ajax({
            url: 'ajax_change_sub_product_status.php',
            type: 'POST',
            data: {id: id,status: status},
            dataType: "json",
            success: function(res) {
              if (res.status == "success") {
                window.parent.setNotifySuccess(res.msg)
              }else{
                window.parent.setNotifyError(res.msg);
              }
            }
          });
        }, function (dismiss) {
            $('#is_active_'+id).val(old_status);
            $('select.form-control').selectpicker('refresh');
             
        });  
    }

    $(document).off("click",".editSubProduct");
    $(document).on("click",".editSubProduct",function(){
        $id=$(this).attr('data-id');
        $link='sub_product_edit.php?id='+$id;
        $.colorbox({
            href: $link,
            iframe: true, 
            width: '500px', 
            height: '430px',
            onClosed:function(){
                window.parent.sub_products();
            }
        });
    });     
</script>