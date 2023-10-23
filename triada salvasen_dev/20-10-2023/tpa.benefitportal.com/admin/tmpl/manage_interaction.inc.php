<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="fs18 mn"><?=!empty($id) ? "Edit" : "+"?> Interaction - <span class="fw300">(<?=!empty($popupTitle) ? $popupTitle : ''?>)</span></h4>
    </div>
  </div>
  <div class="panel-body">
    <form class="theme-form" id="interaction_form">
       <input type="hidden" class="form-control" name="user_type" value="<?=!empty($user_type) ? $user_type : ''?>">
       <input type="hidden" class="form-control" name="id" value="<?=!empty($id) ? $id : ''?>">
      <div class="row">
        <div class="form-group">
          <input type="text" class="form-control" name="type" value="<?=!empty($type) ? $type : ''?>">
          <label>Interaction Type</label>
           <p class="error" id="error_type"></p>
        </div>
      </div>
      <div class="clearfix text-center">
        <button class="btn btn-action" type="button" id="save_interaction">Save</button>
        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
      </div>
    </form>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {  
    $("#save_interaction").focus();
  });
  $(document).on('keypress',function(e) {
    if(e.which == 13 || e.keyCode == 13) {
      e.preventDefault();
      $("#save_interaction").trigger('click');      
    }
  });

  $(document).off("click","#save_interaction");
  $(document).on("click","#save_interaction",function(e){
        e.preventDefault();
        $formId=$("#interaction_form");
        $action='ajax_manage_interaction.php';
        $('.error').html('');
        
        disableButton($(this));
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
                enableButton($("#save_interaction"));
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
