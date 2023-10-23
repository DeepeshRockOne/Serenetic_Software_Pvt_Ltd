<div class="panel panel-defualt">
 <div class="panel-heading">
   <div class="panel-title">
     <h4 class="mn">Share Website - <span class="fw300"><?= $website_name ?></span></h4>
   </div>
 </div>
  <form action="" name="frm_share_website" id="frm_share_website">
    <input type="hidden" name="sent_via" id="sent_via" value="SMS">
    <input type="hidden" name="trigger_id" id="trigger_id" value="<?= $trigger_id ?>">
    <input type="hidden" name="website_url" id="website_url" value="<?= $website_url ?>">
     <div class="panel-body">
       <h4 class="m-b-20 fs18">Text Message (SMS)</h4>
       <div class="theme-form">
         <div class="row">
           <div class="col-sm-12">
             <div class="form-group">
               <input type="text" class="form-control" value="<?= format_telephone($from_number) ?>" readonly="readonly">
               <label>From Phone</label>
             </div>
           </div>
           <div class="col-sm-12">
             <div class="form-group">
                <input type="hidden" name="to_phone" id="to_phone" value="">
               <input type="text" name="autocomplete_enrolee" id="autocomplete_enrolee" class="form-control">
               <label>To Phone</label>
               <p class="error" id="error_to_phone"></p>
             </div>
           </div>
         </div>
         <div class="form-group height_auto">
           <textarea class="form-control" name="sms_content" id="sms_content" rows="7"><?= $sms_content ?></textarea>
           <p class="error" id="error_sms_content"></p>
           <p>Characters Remaining: <label id="message1"></label><br>Messages over 160 characters will send in multiple SMS messages.</p>
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
    //$("#to_phone").inputmask("(999) 999-9999");
    var chars = $("#sms_content").val().length;
    $("#message1").text(160 - chars);

    $("#sms_content").keyup(function (e) {
        var chars = $(this).val().length;
        $("#message1").text(160 - chars);

        if (chars > 160 || chars <= 0) {
            $("#message1").addClass("minus");
            $(this).css("text-decoration", "line-through");
        } else {
            $("#message1").removeClass("minus");
            $(this).css("text-decoration", "");
            e.preventDefault();
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
                  searchVia :'SMS',
                  query: request.term
              },
              success: function(data) {
                  response(data);
              }
          });
      },
      select: function (event, ui) {
          $('#to_phone').val(ui.item.phone);
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