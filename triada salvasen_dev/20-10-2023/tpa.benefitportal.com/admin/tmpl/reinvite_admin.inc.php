<?php include_once 'notify.inc.php'; ?>
<div class="panel panel-default panel-block panel-title-block reinvite_user">
   <div class="panel-heading">
      <div class="panel-title">
         <h4><i class="fa fa-link" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $fname ." ". $lname; ?> - <span class="fw300">Personalized Application Link</span></h4>
      </div>
   </div>
   <form action="" role="form" method="post" class="theme-form" name="user_form"  id="user_form" enctype="multipart/form-data">
      <div class="panel-body" >
         <div class="form-group height_auto pn">
            <div class="row">
               <div class="col-xs-9 col-sm-9">
                  <input class="form-control" type="text" value="<?=$link?>" readonly="readonly" id="copytext" style="<?php if($data['invite_time_diff'] > 168){ echo 'color:red' ;}  ?>"/>
                  <label>Click on link below to copy and share<em>*</em></label>
               </div>
               <div class="col-xs-3 col-sm-3"><a class="btn btn-info btn-block" id="<?php echo ($data['invite_time_diff'] > 168) ? 'generate_link' : 'copyingg'?>" data-clipboard-target="#copytext"><?php echo ($data['invite_time_diff'] > 168) ?'GENERATE LINK' : 'COPY LINK'?></a></div>
            </div>
         </div>
         <div class="form-group height_auto pn">
            <div class="row">
               <div class="col-xs-9 col-sm-9">
                  <input type="text" id="email" name="email" class="form-control no_space <?php echo isset($errors['email']) ? 'parsley-error' : '' ?>" value="<?php echo ($email); ?>" tabindex="4"/>
                  <label>Enter email address to resend invitation<em>*</em></label>
                  <?php if (isset($errors['email'])): ?>
                  <ul class="parsley-error-list">
                     <li class="required"><?php echo $errors['email'] ?></li>
                  </ul>
                  <?php endif; ?>
               </div>
               <div class="col-xs-3 col-sm-3">
                  <button class="btn btn-action btn-block" type="submit" name="save" onclick="$('#ajax_loader').show()">SEND INVITE</button>
               </div>
            </div>
         </div>
      </div>
   </form>
</div>
<script type="text/javascript">
  $(document).ready(function(){

    checkEmail();

    var check_disable = $('#copyingg').attr('disabled');

    if(check_disable != 'disabled'){
      var clipboard = new Clipboard('#copyingg');
      // Copy invitation link in clipboard
      clipboard.on('success', function (e) {
        $('#ajax_loader').show();
        parent.setNotifySuccess("Link Copied!");
        // window.setTimeout(function() {
          window.parent.$.colorbox.close();
        // }, 1000);
      });
    }

    $("#generate_link").click(function(){
      var customer_id = "<?=$id?>";
      $('#ajax_loader').show();
      $.ajax({
        url: 'ajax_reinvite_admin_generate_link.php',
        data: 'id=' + customer_id,
        type: 'GET',
        dataType: 'json',
        success: function (result) {
          if(result.status == 'success'){
            window.parent.setNotifySuccess("Generate link successfully");
            // window.setTimeout(function() {
              location.reload();
            // }, 1000);
          } else {
            $("#ajax_loader").hide();
            window.parent.setNotifyError("Can not Generate link.");
          }
        }
      });
    });
  })
</script>