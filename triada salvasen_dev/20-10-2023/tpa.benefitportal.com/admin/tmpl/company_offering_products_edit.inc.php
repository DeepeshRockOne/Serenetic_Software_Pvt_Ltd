<div class="panel panel-default panel-block ">
    <div class="panel-heading">
        <div class="panel-title">
            <h4 class="mn">
        <?php if(!empty($_GET['id'])){ ?>
            Edit Company Name    
        <?php }else{ ?>
            + Company
        <?php } ?>
    </h4>
    </div>
    </div>
    <div class="panel-body">
        <form method="post" name="edit_company_name_form" id="edit_company_name_form" class="theme-form">
            <input type="hidden" name="company_id" id="company_id" value="<?= $id ?>">
            <div class="form-group">
                <input type="text" name="company_name" id="company_name" class="form-control" value="<?php echo (!empty($name))?$name:'' ?>">
                <label>Company </label>
                <p class="error" id="error_company_name"></p>
            </div>
            <div class="text-center">
                <button class="btn btn-action" type="button" name="save" id="save">Save</button>
                <button class="btn red-link" type="button" onclick='parent.$.colorbox.close()' >Close</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).on('keypress',function(e) {
        if(e.which == 13) {
          $("#save").trigger('click');      
        }
    });
    $(document).on("click","#save",function(){
        $formId=$("#edit_company_name_form");
        $action='ajax_company_offering_products_edit.php';
        $('.error').html('');
        $.ajax({
            url: $action,
            type: 'POST',
            dataType: 'json',
            data: $formId.serialize(),
            beforeSend:function(){
              $("#ajax_loader").show();
            },
            success:function(res){
                $("#ajax_loader").hide();
                if(res.status=='success'){
                    parent.$.colorbox.close();
                    window.parent.setNotifySuccess(res.msg);
                }else{
                    $.each(res.errors, function (index, error) {
                        $('#error_' + index).html(error);
                        var offset = $('#error_' + index).offset();
                        if(typeof(offset) === "undefined"){
                            console.log("Not found : "+index);
                        }else{
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 195;
                            $('body,html').animate({
                                scrollTop: totalScroll
                            }, 1200);
                        }                        
                    });
                }
            }
        });
    });
</script>