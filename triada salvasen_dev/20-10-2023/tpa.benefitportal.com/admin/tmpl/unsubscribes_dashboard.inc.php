<div class="panel panel-default panel-block advance_info_div">
  <div class="panel-body">
      <div class="phone-control-wrap ">
    <div class="phone-addon w-130">
      <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="120px">
    </div>
    <div class="phone-addon text-left"> <p class="fs14 m-b-20">The email and phone numbers below have unsubscribed from receiving communications from <?= $DEFAULT_SITE_NAME ?> system.</p>
        <div class="info_box info_box_max_width">
          <p class="fs14 mn">To reinstate, simply select the checkbox of the email/phone number you wish to reinstate and click the remove button to remove them off of the unsubscribe list.</p>
    </div>
  </div>
  </div>
  </div>
</div>
  <div id="email_unsubscribes_div"></div>
  <div id="sms_unsubscribes_div"></div>

<script type="text/javascript">
  $(document).ready(function(){
    dropdown_pagination('email_unsubscribes_div','sms_unsubscribes_div')

    email_unsubscribes();
    sms_unsubscribes();
  });

    email_unsubscribes = function() {
      $('#email_unsubscribes_div').hide();
      $.ajax({
        url: 'email_unsubscribes.php',
        type: 'GET',
        data: {
          is_ajaxed: 1,
        },
        beforeSend:function(){
          $("#ajax_loader").show();
        },
        success: function(res) {
          $('#ajax_loader').hide();
          $('#email_unsubscribes_div').html(res).show();
          common_select();
        }
      });
    }

    sms_unsubscribes = function() {
      $('#sms_unsubscribes_div').hide();
      $.ajax({
        url: 'sms_unsubscribes.php',
        type: 'GET',
        data: {
          sms_ajaxed: 1,
        },
        beforeSend:function(){
          $("#ajax_loader").show();
        },
        success: function(res) {
          $('#ajax_loader').hide();
          $('#sms_unsubscribes_div').html(res).show();
          common_select();
        }
      });
    }

</script>

