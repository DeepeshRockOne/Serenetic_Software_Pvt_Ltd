<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead> 
            <tr class="data-head">
                <th width="15%">Category Name</th>
                <th class="text-center">Products #</th>
                <th width="90px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= $rows['title']; ?></td>
                        <td class="text-center">
                            <a class="listCategoryProduct text-action fw600" href="javascript:void(0)" data-id="<?= $rows['id'] ?>" data-name="<?= $rows['title']; ?>"><?= $rows['total_products']; ?></a>
                        </td>
                        <td class="icons">
                                <a data-toggle="tooltip" title="Edit Company" class="editCategory" href="javascript:void(0)" data-id="<?= $rows['id'] ?>"><i class="fa fa-lg fa-edit"></i></a>
                                <a data-toggle="tooltip" title="Delete Company" class="deleteCategory" data-id="<?=$rows['id']?>" data-count="<?= $rows['total_products']; ?>" href="javascript:void(0);"><i class="fa fa-lg fa-trash"></i></a>
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
    $(document).off('click', '#product_categories_div ul.pagination li a');
    $(document).on('click', '#product_categories_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#product_categories_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#product_categories_div').html(res).show();
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

    $(document).off("click",".deleteCategory");
    $(document).on("click",".deleteCategory",function(){
        $id=$(this).attr('data-id');
        $count=$(this).attr('data-count');

        if($count>0){
            $link = 'product_categories_change.php?id='+$id;
            $.colorbox({
                href: $link,
                iframe: true, 
                width: '900px', 
                height: '550px',
                onClosed:function(){
                    window.parent.product_categories();
                }
            });
        }else{
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
                        if(res.status='success'){
                            window.parent.setNotifySuccess("Company Deleted Successfully");
                        }
                        window.parent.product_categories();
                        $("#ajax_loader").hide();
                    }
                });
            }, function (dismiss) {
            });
            
        }
    });

    $(document).off("click",".editCategory");
    $(document).on("click",".editCategory",function(){
        $id=$(this).attr('data-id');
        $link='product_categories_edit.php?id='+$id;
        $.colorbox({
            href: $link,
            iframe: true, 
            width: '600px', 
            height: '700px',
            onClosed:function(){
                window.parent.product_categories();
            }
        });
    });

    $(document).off("click",".listCategoryProduct");
    $(document).on("click",".listCategoryProduct",function(){
        $id=$(this).attr('data-id');
        $name=$(this).attr('data-name');
        $link='product_categories_list.php?id='+$id+'&name='+$name;
        $.colorbox({
            href: $link,
            iframe: true, 
            width: '800px', 
            height: '550px',
        });
    });
</script>