<form action="" role="form" method="post" class="uform" name="address_form"  id="address_form" enctype="multipart/form-data">
<input type="hidden" name='member_id' value="<?=$member_id?>">
<div class="panel panel-default">
  <div class="panel-heading">
      <h4 class="mn">Request Access - <span class="fw300"><?=$member_res['rep_id']?></span></h4>
  </div>
  <div class="panel-body">      
    <div class="secound-part"> 
      <p>Please enter the validation code below to make changes to a member account.</p>
        <div class="col-xs-8 col-xs-offset-2" >
          <div class="member_access_request text-center">
            <hr>
            <input type="text" name="otp" id="otp" class="form-control num_only" placeholder="6 - Digit Code" maxlength="6">
            <p class="error"><?=isset($errors['otp'])?$errors['otp']:''?></p>
            <hr>
          </div>          
        </div>
        <div class="clearfix"></div>
        <div class="text-center">
          <p>Didn’t receive the code? <a href="members_request_access.php?member_id=<?=$member_id?>&resend=<?=$_SESSION['member_otp']['send_via']?>" class="red-link">Resend</a></p>
          <button type="submit" class="btn btn-action btn-continue" onclick="show_loader();" name="save" id="save">Continue</button>
          <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
        </div>
    </div>
  </div>
  </div>
</form>
<script type="text/javascript">
    function show_loader(){
        $("#ajax_loader").show();
    }
    $(document).ready(function(){
        $(document).keypress(function (e) {
          if (e.which == 13) {
            e.preventDefault();
            $('#save').click();
          }
        });
        $(document).on('input blur paste',"#otp",function(){
            $(this).val($(this).val().replace(/\D/g, ''))
        });
    });
</script>