<div class="panel panel-default">
   <form action="ajax_change_agent_status.php" method="post" name="user_form" id="user_form" class="theme-form">
  <div class="panel-heading ">
    <div class="panel-title">
      <h4 class="mn">
  Change Status
  </h4>
</div>
</div>
 
    <div class="panel-body">
        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
        <input type="hidden" name="status" value="<?php echo $_GET['status']; ?>">
          <div class="form-group height_auto">
            <textarea id="reason" name="reason" class="form-control" value="" rows="3" placeholder="Enter Reason for change" /></textarea>
            <span class="error" id="error_reason"></span>
          </div>
       
          <div class="form-group">
            <button class="btn btn-info" type="button" name="save" id="save">Save</button>
            <button class="btn btn-default" type="button" onclick='parent.$.colorbox.close()'>Cancel</button>
        </div>
      </div>
  </form>
</div>
<script type="text/javascript">
  $(document).on("click","#save",function(){
    $formId=$("#user_form");
    $action=$formId.attr("action");
      $.ajax({
      url: "ajax_change_agent_status.php",
      type: 'POST',
      dataType: 'json',
      data: $formId.serialize(),
      beforeSend:function(){
        $("#ajax_loader").show();
      },
      success:function(data){
        $("#ajax_loader").hide();
        // $("ul.parsley-error-list").find("li").html("");
        $(".error").hide();
        if(data.status=='success'){
            parent.setNotifySuccess(data.msg);
             setTimeout(function(){
              window.parent.location.href=window.parent.location.href;
             }, 1000);
        }else if(data.status=='error'){
            $("#ajax_loader").show();
            setTimeout(function(){
              window.parent.location.href=window.parent.location.href;
             }, 1000);
        }else{
          $.each(data.error,function($div_id,$div_error_msg){
            $("#error_"+$div_id).html($div_error_msg).show();
          });
        }
      }
    });
  });
</script>