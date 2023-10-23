<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead> 
            <tr>
              <th>Type</th>
              <th width="90px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= $rows['name']; ?></td>
                        <td class="icons">
                            <a href="javascript:void(0)" class="editReason" data-toggle="tooltip" data-original-title="Edit" data-id="<?= $rows['id'] ?>"><i class="fa fa-pencil-square-o"></i></a>
                            <a href="javascript:void(0)" class="deleteReason" data-toggle="tooltip" data-original-title="Delete" data-id="<?= $rows['id'] ?>"><i class="fa fa-trash"></i></a>
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
    $(document).ready(function(){
        $(document).off('click', '#reversalDiv ul.pagination li a');
        $(document).on('click', '#reversalDiv ul.pagination li a', function (e) {
            e.preventDefault();
            $('#ajax_loader').show();
            $('#reversalDiv').hide();
            $.ajax({
                url: $(this).attr('href'),
                type: 'GET',
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#reversalDiv').html(res).show();
                }
            });
        });

        $(document).off("click",".editReason");
        $(document).on("click",".editReason",function(){
            $id=$(this).attr('data-id');
            $link='manage_reversal_reasons.php?id='+$id;
            $.colorbox({
                href: $link,
                iframe: true, 
                width: '600px', 
                height: '250px',
                onClosed:function(){
                    
                }
            });
        });

        $(document).off("click",".deleteReason");
        $(document).on("click",".deleteReason",function(){
            $id=$(this).attr('data-id');

            swal({
                text: 'Delete  Reversal Reasons: Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
            }).then(function () {
                $("#ajax_loader").show();
                $.ajax({
                    url:'ajax_delete_reversal_reasons.php',
                    dataType:'JSON',
                    type:'POST',
                    data:{id:$id},
                    success:function(res){
                      if(res.status='success'){
                        window.parent.setNotifySuccess("Reversal Reasons Deleted Successfully");
                        window.parent.load_reversal_reason_settings();
                      }
                      $("#ajax_loader").hide();
                    }
                });
            }, function (dismiss) {
            });
        });
    });
</script>