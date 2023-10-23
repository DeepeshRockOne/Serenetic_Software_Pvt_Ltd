<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr class="data-head">
            <th width="15%">Company Name</th>
            <th class="text-center" >Products #</th>
            <th width="90px">Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= $rows['company_name']; ?></td>
                        <td class="text-center">
                            <a class="listCompanyProduct text-action fw500" href="javascript:void(0);" data-id="<?=$rows['id']?>" data-name="<?=$rows['company_name']?>"><?= $rows['total_products']; ?></a>
                        </td>
                        <td class="icons no-wr">
                            <div class="text-left">
                                <a data-toggle="tooltip" title="Edit Company" class="editCompany " href="javascript:void(0);" data-id="<?=$rows['id']?>"><i class="fa fa-lg fa-edit"></i></a>
                                <a data-toggle="tooltip" title="Delete Company" class="deleteCompany" data-id="<?=$rows['id']?>" data-count="<?= $rows['total_products']; ?>" href="javascript:void(0);"><i class="fa fa-lg fa-trash"></i></a>
                            </div>
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
    $(document).off('click', '#company_offering_products_div ul.pagination li a');
    $(document).on('click', '#company_offering_products_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#company_offering_products_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#company_offering_products_div').html(res).show();
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

    $(document).off("click",".deleteCompany");
    $(document).on("click",".deleteCompany",function(){
        $id=$(this).attr('data-id');
        $count=$(this).attr('data-count');

        if($count>0){
            $link = 'company_offering_products_change.php?id='+$id;
            $.colorbox({
                href: $link,
                iframe: true, 
                width: '900px', 
                height: '550px',
                onClosed:function(){
                    window.parent.company_offering_products();
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
                    url:'ajax_company_offering_products_delete.php',
                    dataType:'JSON',
                    type:'POST',
                    data:{id:$id},
                    success:function(res){
                        if(res.status='success'){
                            window.parent.setNotifySuccess("Company Deleted Successfully");
                        }
                        $("#ajax_loader").hide();
                        window.parent.company_offering_products();
                    }
                });
            }, function (dismiss) {
            });
            
        }
    });

    $(document).off("click",".editCompany");
    $(document).on("click",".editCompany",function(){
        $id=$(this).attr('data-id');
        $link='company_offering_products_edit.php?id='+$id;
        $.colorbox({
            href: $link,
            iframe: true, 
            width: '600px', 
            height: '250px',
            onClosed:function(){
                window.parent.company_offering_products();
            }
        });
    });

    $(document).off("click",".listCompanyProduct");
    $(document).on("click",".listCompanyProduct",function(){
        $id=$(this).attr('data-id');
        $name=$(this).attr('data-name');
        $link='company_offering_products_list.php?id='+$id+'&name='+$name;
        $.colorbox({
            href: $link,
            iframe: true, 
            width: '800px', 
            height: '550px',
        });
    });
</script>