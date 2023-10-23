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
            <form name="product_categories_form" id="product_categories_form">
                <table class="<?=$table_class?> text-nowrap">
                    <thead>
                    <tr class="data-head">
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Offering Company</th>
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
                                            <?php if(!empty($category_res)) { ?>
                                                <?php foreach ($category_res as $category) {?>
                                                  <option value="<?php echo $category["id"]; ?>" <?php echo $category['id'] == $rows['category_id'] ? 'selected=selected' : ''; ?>><?php echo $category["title"]; ?>
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
            url:'ajax_products_categories_change.php',
            dataType:'JSON',
            type:'POST',
            data:$("#product_categories_form").serialize(),
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
                url:'ajax_product_categories_delete.php',
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