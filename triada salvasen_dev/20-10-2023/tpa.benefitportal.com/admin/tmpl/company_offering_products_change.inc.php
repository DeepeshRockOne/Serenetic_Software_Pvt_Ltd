<div class="panel panel-default panel-block ">
    <div class="panel-heading">Delete Company</span></div>
    <div class="panel-body">
            <p class="text-center p-t-20">
                <strong>This company is connected to <span class="text-blue"><?= $total_rows ?> Products.</span></strong>
                <br>
                <span>You can not delete a company untill it is no longer connected to any products.You can change the <br> products on their product listing page, or by using the form below.</span>
            </p>
        
        <div class="clearfix"></div>
        <div class="table-responsive p-t-20">
            <form name="comapny_offering_product_form" id="comapny_offering_product_form">
            <table class="<?=$table_class?> text-nowrap">
                <thead>
                <tr class="data-head">
                    <th><a href="javascript:void(0);">Product ID</a></th>
                    <th><a href="javascript:void(0);">Product Name</a></th>
                    <th><a href="javascript:void(0);">Offering Company</a></th>
                </tr>
                </thead>
                <tbody>
                    <?php if ($total_rows > 0) {
                        foreach ($fetch_rows as $rows) { ?>
                            <tr>
                                <td><?= $rows['product_code']; ?></td>
                                <td><?= $rows['name']; ?></td>
                                <td>
                                    <select name="offering_company[<?= $rows['id'] ?>]" class="offering_company form-control select2">
                                        <?php if(!empty($company_res)) { ?>
                                            <?php foreach ($company_res as $company) {?>
                                              <option value="<?php echo $company["id"]; ?>" <?php echo $company['id'] == $rows['company_id'] ? 'selected=selected' : ''; ?>><?php echo $company["company_name"]; ?>
                                              </option>
                                            <?php }?>
                                          <?php }?>
                                    </select>                                    
                                </td>
                            </tr>
                        <?php }?>
                    <?php } else {?>
                        <tr>
                            <td colspan="9" class="text-center">No record(s) found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php if ($total_rows > 0) { ?>
                <div class="form-group text-center p-t-20">
                    <button type="button" id="save" name="save" class="btn btn-info">Save Changes</button>
                </div>
            <?php }else{  ?>
                <div class="form-group text-center p-t-20">
                    <button type="button" id="delete" name="delete" class="btn btn-info">Delete Company</button>
                </div>
            <?php }  ?>
            </form>
        </div>
    </div>
  </div>
  <script type="text/javascript">
    $(document).on("click","#save",function(){
        $("#ajax_loader").show();
        $.ajax({
            url:'ajax_company_offering_products_change.php',
            dataType:'JSON',
            type:'POST',
            data:$("#comapny_offering_product_form").serialize(),
            success:function(res){
                $("#ajax_loader").hide();
                if(res.status=="success"){
                    window.location.reload();
                }else{
                    window.parent.$.colorbox.close();
                }
            }
        });
    });
    $(document).on("click","#delete",function(){
        $id='<?= $id ?>';
        swal({
            text: 'Delete Company: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                url:'ajax_company_offering_products_delete.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                    $("#ajax_loader").hide();
                    if(res.status='success'){
                        window.parent.setNotifySuccess("Company Deleted Successfully");
                        window.parent.$.colorbox.close();
                    }                    
                }
            });
        }, function (dismiss) {
        });
    });
  </script>