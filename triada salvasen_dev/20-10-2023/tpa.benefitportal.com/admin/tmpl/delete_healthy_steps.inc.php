<form id="delete_healthy_step"  name="delete_healthy_step" method="POST">
    <input type="hidden" name="product_ids[<?=$product_id?>]" value="<?=$product_id?>">
    <input type="hidden" name="product_id" value="<?=$product_id?>">
    <input type="hidden" name="health_ids[<?=$product_id?>]" value="<?=$health_id?>">
        <div class="panel panel-default panel-block ">
        <div class="panel-heading">
            <h4 class="mn">
            <!-- <img src="images/icons/add_level_icon.svg" height="20px" />&nbsp;&nbsp; -->
            Delete Healthy Step - <?=$healthy_row['name'].' ('.$healthy_row['product_code'].')'?></h4>
        </div>
        <div class="panel-body">
            <div class="theme-form">
            <p class="m-b-15">If You want to delete this Global Healthy step It will affect following variations : </p>
            <div class="table-responsive br-n">
                <table class="<?=$table_class?> ">
                    <thead>
                    <tr class="data-head">
                        <th><a href="javascript:void(0);">ID</a></th>
                        <th><a href="javascript:void(0);">ID/Agent Name</a></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if ( count($variation_porducts) > 0) {
                            foreach ($variation_porducts as $rows) { ?>
                                <tr>
                                <input type="hidden" name="product_ids[<?=$rows['id']?>]" value="<?=$rows['id']?>">
                                <input type="hidden" name="health_ids[<?=$rows['id']?>]" value="<?=$rows['pfid']?>">
                                <input type="hidden" name="agent_ids[<?=$rows['id']?>]" value="<?=$rows['agent_id']?>">
                                    <td class="text-red"><?= $rows['display_id']; ?></td>
                                    <td><?= $rows['rep_id'].' - '.$rows['fname'].' '.$rows['lname'] ?></td>
                                </tr>
                            <?php }?>
                        <?php } else {?>
                            <tr>
                                <td colspan="3" class="text-center">No affected record(s) found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            </div>
            <div class="text-center m-t-20">
            <button class="btn btn-action" type="button" name="delete_btn" id="delete_btn" tabindex="6" >Delete</button>
            <a href="javascript:void(0);" onclick='parent.$.colorbox.close();' class="btn red-link">Cancel</a>
            </div>
        </div>
    
    </div>
</form>
<script type="text/javascript">
    $(document).off('click', '#delete_btn');
    $(document).on("click", "#delete_btn", function() {
        $("#ajax_loader").show();
        $(".error").html('');
        var $id = $("#id").val();
        $.ajax({
        url: 'ajax_delete_healthy_steps.php',
        dataType: 'JSON',
        data: $("#delete_healthy_step").serialize(),
        type: 'POST',
        success: function(res) {
            $("#ajax_loader").hide();
            if (res.status == "success") {
                parent.window.$.colorbox.close();
                parent.window.location = "healthy_steps.php";
            }
        }
        });
    });
</script>