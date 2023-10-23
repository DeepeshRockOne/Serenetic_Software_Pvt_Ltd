<div class="panel panel-default panel-block panel-title-block reinvite_user">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">
         <i class="fa fa-link"></i>&nbsp; <?php echo $fname ." ". $lname; ?> - <span class="fw300">Personalized Application Link</span></h1>
      </div>
   </div>
   <?php if($data['invite_time_diff'] < 168){ ?>  
   <form action="" role="form" method="post" class="theme-form " name="user_form"  id="user_form" enctype="multipart/form-data">
      <div class="panel-body" >
         <div class="form-group height_auto pn">
            <div class="row">
               <div class="col-xs-9 col-sm-9">
                  <input class="form-control" type="text" value="<?=$link?>" readonly="readonly" id="copytext" style="<?php if($data['invite_time_diff'] > 168){ echo 'color:red' ;}  ?>"/>
                  <label>Click on link below to copy and share<em>*</em></label>
               </div>
               <div class="col-xs-3 col-sm-3"><a class="btn btn-action btn-block" id="<?php echo ($data['invite_time_diff'] > 168) ? 'generate_link' : 'copyingg'?>" data-clipboard-target="#copytext"><?php echo ($data['invite_time_diff'] > 168) ?'GENERATE LINK' : 'COPY LINK'?></a></div>
            </div>
         </div>
         <div class="form-group height_auto pn">
            <div class="row">
               <div class="col-xs-9 col-sm-9">
                  <input type="text" id="email" name="email" class="form-control no_space <?php echo isset($errors['email']) ? 'parsley-error' : '' ?>" value="<?php echo ($email); ?>" tabindex="4"/>
                  <label>Enter email address to resend invitation<em>*</em></label>
                  <p class="error" id="error_email"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></p>
               </div>
               <div class="col-xs-3 col-sm-3">
                  <button class="btn btn-info btn-block" type="submit" name="save" onclick="$('#ajax_loader').show()">SEND INVITE</button>
               </div>
            </div>
         </div>
      </div>
   </form>
   <?php }else{ ?>
   <div class="panel-body" >
      <p>The Personalized Application link for <?php echo $fname ." ". $lname; ?> has expired. Please click "Generate Link" button below  for a new link that you can copy and share or send directly to this agent here.</p>
      <p>This link is needed to accept invite and create an account.</p>
      <div class="text-center">
         <a class="btn btn-success" id="generate_link" data-clipboard-target="#copytext">GENERATE LINK</a>
      </div>
   </div>
   <?php } ?>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    checkEmail();
    var check_disable = $('#copyingg').attr('disabled');
    if(check_disable != 'disabled'){
      var clipboard = new Clipboard('#copyingg');
      clipboard.on('success', function (e) {
        $('#ajax_loader').show();
        parent.setNotifySuccess("Link Copied!");
        window.parent.$.colorbox.close();
      });
    }

    $("#generate_link").click(function(){
      var customer_id = "<?=$id?>";
      $('#ajax_loader').show();
      $.ajax({
        url: 'ajax_reinvite_agent_generate_link.php',
        data: 'id=' + customer_id,
        type: 'GET',
        dataType: 'json',
        success: function (result) {
          if(result.status == 'success'){
            window.parent.setNotifySuccess("Generate link successfully");
            parent.get_status(customer_id);
            window.location.reload();
          } else {
            $("#ajax_loader").hide();
            window.parent.setNotifyError("Can not Generate link.");
          }
        }
      });
    });
  });
</script>