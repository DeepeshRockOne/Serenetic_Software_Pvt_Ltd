<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="mn"><?=$lead_info['fname'] . " " . $lead_info['lname']?> - <span class="fw300">Pending Validation AAE</span></h4>
  </div>
  <div class="panel-body">
    <p class="m-b-20">This lead's Agent Assisted Application is pending validation.  Click below to resend the application link to encourage completion.</p>
    <div class="text-center">
      <a href="javascript:void(0);" class="btn btn-action" id="resend_btn">Send</a>
      <a href="javascript:void(0);" class="btn red-link" id="cancel_brn">Cancel</a>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).on('click','#cancel_brn',function(){
    parent.$.colorbox.close();    
  });
  $(document).on('click','#resend_btn',function(){
    $('#ajax_loader').show();
    $.ajax({
        url: 'aae_resend_enrollment_edit_popup.php',
        type: 'POST',
        data:{is_resend : 'Yes',lead_quote_id : '<?=$_GET['lead_quote_id']?>'},
        dataType : 'JSON',
        success: function (res) {
          $('#ajax_loader').hide();
          if(res.status == 'success'){
            if(res.mail_status == 'success'){
              parent.setNotifySuccess("Application sent successfully");
            }else{
              parent.setNotifyError("Application Not Sent");
            }
          }else{
            parent.setNotifyError("Application Details Not Found");
          }
          parent.$.colorbox.close();             
        }
    });
  });
</script>


