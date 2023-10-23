<div class="panel panel-default share_website_model ">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">Share Website - <span class="fw300"><?= $website_name ?>!</span></h4>
      </div>
   </div>
   <div class="panel-body">
      <div class="text-center">
         <h5 class="m-b-20 fs16">Share Your Self Application Website</h5>
         <p class="m-b-20">Send link via:</p>
         <div class="clearfix m-b-20">
            <a href="javascript:void(0);" data-href="email_share_website.php?id=<?= $website_id ?>" class="btn btn-info send_enrollment_link">Email</a>
            <a href="javascript:void(0);" data-href="sms_share_website.php?id=<?= $website_id ?>"  class="btn btn-action send_enrollment_link">SMS Text</a>
         </div>
         <div class="or_line">Or</div>
         <div class="m-b-20 m-t-20 generic_url_box">
            <div class="input-group">
               <input type="text" id="copytext" class="form-control radius-zero"  value="<?= $website_url ?>" readonly="readonly">
               <span class="input-group-addon clone_link" id="copyingg" data-clipboard-target="#copytext">
                  COPY LINK
               </span>
            </div>
         </div>
         <div class="clearfix">
            <a href="javascript:void(0);" class="fw500 text-action" onclick="window.parent.$.colorbox.close()">Close</a>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
      var clipboard = new Clipboard('#copyingg');
      clipboard.on('success', function (e) {
        $('#ajax_loader').show();
        parent.setNotifySuccess("Link Copied!");
        window.parent.$.colorbox.close();
      });
   });

  
  $(document).off('click',".send_enrollment_link");
    $(document).on('click',".send_enrollment_link",function(){
        $href = $(this).attr('data-href');
        window.parent.$.colorbox({
            href:$href,
            iframe:true,
            height:'600px',
            width:'800px'
        });
    });
</script>
