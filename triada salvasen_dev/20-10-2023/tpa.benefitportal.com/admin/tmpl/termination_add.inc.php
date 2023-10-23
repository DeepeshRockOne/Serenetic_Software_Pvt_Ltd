<div class="panel panel-default panel-block">
    <div class="panel-heading">
        <h4 class="mn">+ Termination Reasons</h4>
    </div>
    <div class="panel-body">
        <form action="" id="reason_form" name="reason_form">
            <input type="hidden" name="reason_id" value="<?=checkIsset($rows['id'])?>">
            <input type="hidden" name="action" value="<?=$action?>">
            <input type="hidden" name="is_ajaxed" value="1">
            <div class="theme-form">
                <div class="form-group">
                    <input type="text" name="reason_name" id="reason_name" class="form-control" value="<?=checkIsset($rows['name'])?>">
                    <label>Name<em>*</em></label>
                    <p class="error error_reason_name"></p>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-action" id="save_reason">Save</a>
                    <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).off('click','#save_reason');
    $(document).on('click','#save_reason',function(e){
       save_reason(); 
    });
    $(document).on("keypress",'#reason_name',function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
         if (code == 13) {
            e.preventDefault();
            e.stopPropagation();
            save_reason();     
        }
    });
    function save_reason() {
        $.ajax({
            url: 'termination_add.php',
            data: $("#reason_form").serialize(),
            type: 'POST',
            beforeSend : function(e){
                $('#ajax_loader').show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $(".error").html("");
                if(res.status=='success'){
                    parent.$.colorbox.close();
                    parent.setNotifySuccess(res.msg);
                    parent.location.reload();
                }else{
                    $.each(res.errors,function(index,error){
                        $(".error_"+index).html(error).show();
                    });
                }
            }
        });
    }
</script>