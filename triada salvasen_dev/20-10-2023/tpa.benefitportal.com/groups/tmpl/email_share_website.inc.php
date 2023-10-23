<div class="panel panel-default ">
 <div class="panel-heading">
   <div class="panel-title">
     <h4 class="mn">Share Website - <span class="fw300"><?= $website_name ?></span></h4>
   </div>
 </div>
 <form action="" name="frm_share_website" id="frm_share_website">
    <input type="hidden" name="sent_via" id="sent_via" value="Email">
    <input type="hidden" name="trigger_id" id="trigger_id" value="<?= $trigger_id ?>">
    <input type="hidden" name="website_url" id="website_url" value="<?= $website_url ?>">
   <div class="panel-body">
     <h4 class="m-b-20 fs18">Email</h4>
     <div class="theme-form">
       <div class="row">
         <div class="col-sm-6">
           <div class="form-group">
             <input type="text" name="email_from" id="email_from" class="form-control no_space" value="<?= $from_email ?>" readonly>
             <label>From</label>
             <p class="error" id="error_email_from"></p>
           </div>
         </div>
         <div class="col-sm-6">
           <div class="form-group">
              <input type="hidden" name="to_email" id="to_email" value="">
             <input type="text" name="autocomplete_enrolee" id="autocomplete_enrolee" class="form-control no_space">
             <label>To</label>
             <p class="error" id="error_to_email"></p>
           </div>
         </div>
         <div class="col-sm-12">
           <div class="form-group">
             <input type="text" name="email_subject" id="email_subject" class="form-control" value="<?= $email_subject ?>">
             <label>Subject</label>
             <p class="error" id="error_email_subject"></p>
           </div>
         </div>
       </div>
       <div class="form-group height_auto">
         <textarea class="summernote" name="email_content" id="email_content"><?= $email_content ?></textarea>
         <p class="error" id="error_email_content"></p>
       </div>
       
     </div>
   </div>
   <div class="panel-footer text-center">
     <a href="javascript:void(0);" class="btn btn-action" id="share_website">Send</a>
     <a href="javascript:void(0);" class="btn red-link back_website" data-id="<?= $website_id ?>">Back</a>
   </div>
  </form>
</div>
<script type="text/javascript">
  $(document).ready(function() {
      checkEmail();
      $('.summernote').summernote({
        toolbar: $SUMMERNOTE_TOOLBAR,
        disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
        focus: true, // set focus to editable area after initializing summernote
        height:250,
        callbacks: {
          onImageUpload: function(image) {
            editor = $(this);
            uploadImageContent(image[0], editor);
          },
          onMediaDelete : function(target) {
              deleteImage(target[0].src);
              target.remove();
          }
        }
      });

      var auto_complete = {
      autoFocus: true,
      source: function(request, response) {
          $.ajax({
              url: "ajax_autocomplete_enrolee_data.php",
              type: "POST",
              dataType: "json",
              data: {
                  searchVia :'Email',
                  query: request.term
              },
              success: function(data) {
                  response(data);
              }
          });
      },
      select: function (event, ui) {
          $('#to_email').val(ui.item.email);
      },
      minLength: 2, 
    };

    $("#autocomplete_enrolee").autocomplete(auto_complete);
  });

  $(document).off('click',".back_website");
  $(document).on('click',".back_website",function(){
    $id = $(this).attr('data-id');
    window.parent.$.colorbox({
        href:'share_website_link.php?id='+$id,
        iframe:true,
        height:'500px',
        width:'600px'
    });
  });

  $(document).off('click',"#share_website");
  $(document).on('click',"#share_website",function(){

    $("#ajax_loader").show();
    $(".error").html('');
    $.ajax({
      url:'<?= $GROUP_HOST ?>/ajax_share_website_link.php',
      dataType:'JSON',
      data:$("#frm_share_website").serialize(),
      type:'POST',
      success:function(res){
        $("#ajax_loader").hide();
        if(res.status == "success"){
          parent.setNotifySuccess("Link Shared!");
          window.parent.$.colorbox.close();
        }else if(res.status=="fail"){
          parent.$.colorbox.close();
          parent.setNotifyError(res.msg);
        }else{
          var is_error = true;
          $.each(res.errors, function (index, value) {
              $('#error_' + index).html(value).show();
              if(is_error){
                  var offset = $('#error_' + index).offset();
                  var offsetTop = offset.top;
                  var totalScroll = offsetTop - 50;
                  $('body,html').animate({scrollTop: totalScroll}, 1200);
                  is_error = false;
              }
          });
        }
      }
    })
  });
</script>