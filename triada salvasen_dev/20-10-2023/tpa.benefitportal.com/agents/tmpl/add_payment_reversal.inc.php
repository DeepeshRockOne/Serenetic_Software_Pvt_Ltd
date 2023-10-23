<div class="container m-t-30">
<div class="panel panel-default panel-block advance_info_div">
  <div class="panel-body">
    <div class="phone-control-wrap ">
      <div class="phone-addon w-90">
        <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
      </div>
      <div class="phone-addon text-left">
        <p class="fs14">There are three options when it comes to reversing a successful payment: chargeback, refund, and void.  A chargeback will reverse the entire order, commissions, and stop all future billings.  A refund allows user to select order items they wish to reverse and the form of reimbursement desired. A void is allowed if the order has not settled and will reverse the entire order, commissions, and stop all future billings.  To begin, enter the Order ID you wish to reverse:</p>
        <div class="info_box_max_width theme-form roboto_font">
          <input type="hidden" name="location" id="location" value="<?=$location?>">
          <input type="hidden" name="tmp_order_id" id="tmp_order_id" value="<?=$tmp_order_id?>">
          <div class="phone-control-wrap ">
            <div class="phone-addon text-left">
              <div class="form-group height_auto">
                <select class="form-control" data-live-search="true" name="order_id"  id="order_id">
                  <option></option>
                  <?php if($orders){ ?>
                    <?php foreach ($orders as $key => $value) { ?>
                      <?php
                          if($value['status'] == 'Pending Settlement'){
                            if(!is_ach_voidable($value["id"])){
                              continue;
                            }
                          }
                       ?>
                      <option value="<?=$value['id']?>" <?=md5($value['id']) == $tmp_order_id ? "selected='selected'" : ""?>><?=$value['display_id']?></option>
                    <?php } 
                  } ?>
                </select>
                <label>Search Order ID</label>
                <p class="error"><span id="err_order_id"></span></p>
              </div>
            </div>
            <div class="phone-addon w-70 v-align-top">
              <button class="btn btn-action" id="submit">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row order_info" style="display: none">
  
</div>
<div class="failed_popup_wrapper" style="display: none;">
  <div class="panel panel-default failed_popup">
    <div class="panel-body login-alert-modal">
      <div class="media br-n pn mn">
        <div class="media-left"> <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left" height="130px"> </div>
        <div class="media-body">
          <h3 class="text-action m-t-n fw600" >Uh Oh!</h3>
          <p id="api_response"></p>
        </div>
        <div class="text-center">
          <a href="javascript:void(0);" class="red-link " onclick='parent.$.colorbox.close(); return false;'>Close</a>
        </div>
      </div>
    </div>
  </div>
</div>
<div style="display:none" id="clonePwdPopup">
   <div id="password_popup_~name~" class="system_setup_access m-b-10">
       <div class="input-group">
          <input type="password" class="form-control radius-zero" name="det" id="password_~name~">
          <span class="input-group-addon" data-name="~name~" id="unlockBtn">Uncheck</span>
        </div>
   </div>
   <div class="clearfix"></div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  $(".payment_refund_popup").colorbox({iframe: true, width: '400px', height: '215px', closeButton : false});

  $(document).on('click','#chk_reverse_comm',function(){
      if(!$(this).is(":checked")) {
        $('#chk_reverse_comm').prop('checked',true).uniform('refresh');
        
        $popupHtml = $("#clonePwdPopup").html();
        $("#pwd_reverse_comm").html($popupHtml.replace(/~name~/g,'reverse_comm')).show();
      }
  });

  $(document).on('click','#unlockBtn',function(e){
      e.preventDefault();
      $divName = $(this).attr("data-name");
      if($("#password_"+$divName).val() === '5401'){
         $('#chk_reverse_comm').prop('checked',false).uniform('refresh');
      }
      $("#pwd_"+$divName).html('');
  });

  $(document).on('change','#reversal_type',function(){
    var value = $(this).val();
    $('.action_button').show('slow');
    if(value == 'Void'){
      $('.reversal_void').slideDown();
      $('.reversal_chargeback').slideUp();
      $('.payment_return').slideUp();
      $('.refund_div').slideUp();
      $('#refund_submit').text('Void');
      $('#chk_set_term_date').prop('checked',false);
      $('#chk_reverse_comm').prop('checked',false);
      $.uniform.update();
    }else if(value == 'Refund'){
      $('.reversal_void').slideUp();
      $('.reversal_chargeback').slideUp();
      $('.payment_return').slideUp();
      $('.refund_div').slideDown();
      $('#refund_submit').text('Refund');
      $('#chk_set_term_date').prop('checked',true);
      $('#chk_reverse_comm').prop('checked',true);
      $.uniform.update();
    }else if(value == 'Chargeback'){
      $('.reversal_void').slideUp();
      $('.refund_div').slideUp();
      $('.payment_return').slideUp();
      $('.reversal_chargeback').slideDown();
      $('#refund_submit').text('Chargeback');
      $('#chk_set_term_date').prop('checked',false);
      $('#chk_reverse_comm').prop('checked',false);
      $.uniform.update();
    }else if(value == 'Payment Return'){
      $('.reversal_void').slideUp();
      $('.refund_div').slideUp();
      $('.reversal_chargeback').slideUp();
      $('.payment_return').slideDown();
      $('#refund_submit').text('Payment Return');
      $('#chk_set_term_date').prop('checked',false);
      $('#chk_reverse_comm').prop('checked',false);
      $.uniform.update();
    }else{
      $('.action_button').hide('slow');
      $('.reversal_void').slideUp();
      $('.refund_div').slideUp();
      $('#chk_set_term_date').prop('checked',false);
      $('#chk_reverse_comm').prop('checked',false);
      $.uniform.update();
    }

  });

  $(document).on('change','.term_checkbox',function(){
    var plan_id = $(this).data('plan_id');
    if(this.checked) {
      var amount = $(this).data('unitprice');
      var total_amount = parseFloat($('#refund_amount').val()) + parseFloat(amount);
      $('#refund_amount').val(total_amount.toFixed(2));
      if($('#chk_set_term_date').is(":checked")){
        $('#termination_date_' + plan_id).show('slow');
      }
      $('#product_wrapper_' + plan_id).addClass('active');

    }else{
      var amount = $(this).data('unitprice');
      var total_amount = parseFloat($('#refund_amount').val()) - parseFloat(amount);
      $('#refund_amount').val(total_amount.toFixed(2));
      $('#termination_date_' + plan_id).hide('slow');
      $('#product_wrapper_' + plan_id).removeClass('active');
    }
  });

  $(document).on('change','.void_term_checkbox',function(){
    var plan_id = $(this).data('plan_id');
    if(this.checked) {
      $('#void_product_wrapper_' + plan_id).addClass('active');
      $('#void_termination_date_' + plan_id).show('slow');
    }else{
      $('#void_product_wrapper_' + plan_id).removeClass('active');
      $('#void_termination_date_' + plan_id).hide('slow');
    }
  });

  $(document).on('change','#chk_set_term_date',function(){
      
      if(this.checked) {  
        $('.term_checkbox').each(function () {
          var plan_id = $(this).data('plan_id');
          
          if(this.checked) {
            $('#termination_date_' + plan_id).show('slow');
          }else{
            $('#termination_date_' + plan_id).hide('slow');
          }
        });
      }else{
        $('.term_checkbox').each(function () {
          var plan_id = $(this).data('plan_id');          
            $('#termination_date_' + plan_id).hide('slow');
        });
      }
  });

  $(document).on('change','#inactive_member_void',function(){
      
      if(this.checked) {  
        $('.void_term_checkbox').each(function () {
          var plan_id = $(this).data('plan_id');
          
          if(this.checked) {
            $('#void_termination_date_' + plan_id).show('slow');
          }else{
            $('#void_termination_date_' + plan_id).hide('slow');
          }
        });
      }else{
        $('.void_term_checkbox').each(function () {
          var plan_id = $(this).data('plan_id');          
            $('#coid_termination_date_' + plan_id).hide('slow');
        });
      }
  });

  $(document).on('change','#chk_set_term_date_void',function(){
        
        if(this.checked) {
          $('.void_products').show('slow');
        }else{
          $('.void_products').hide('slow');
        }
  });

  $(document).on('change','#chk_refund_by_check',function(){

        if(this.checked) {
          $('.input_check_id').show();
        }else{
          $('.input_check_id').hide();
        }
  });

  if($('#tmp_order_id').val() != ""){
    setTimeout(function(){
      $('#submit').trigger("click");
    },1);
  }


  $(document).on('click','#submit',function(){

    var order_id = $('#order_id').val();
    var location = $('#location').val();
    if(!order_id){
      $('#err_order_id').text("Please select order");
      return false;
    }else{

      disableButton($(this));
      $('#ajax_loader').show();
      // $('#ajax_data').hide();
      // $('#is_ajaxed').val('1');
      var params = {order_id:order_id,location:location};
      $.ajax({
        url: '<?=$HOST?>/ajax_get_return_order.php',
        type: 'GET',
        data: params,
        success: function(res) {
          $('#ajax_loader').hide();
          $('.order_info').html(res).show();
          // $('[data-toggle="tooltip"]').tooltip();
          common_select();
          $('.chk_refund_options, .chk_set_term_date_void').uniform();
        }
      });
      enableButton($("#submit"));
      return false;

    }


  });

  $(document).on('click','#refund_submit',function(){
    // var order_id = $('#order_id').val();
    // if(!order_id){
    //   $('#err_order_id').text("Please select order");
    //   return false;
    // }else{

      disableButton($(this));
      $('#ajax_loader').show();
      // $('#ajax_data').hide();
      $('.error span').html('');
      var params = $('#refund_form').serialize();
      $.ajax({
        url: '<?=$HOST?>/ajax_return_order.php',
        type: 'POST',
        data: params,
        dataType: 'JSON', 
        success: function(res) {
          if(res.status == 'success'){
            $('#is_ajax').val('');
            if($("#location").val() == 'agent') {
                window.location.href = 'all_orders.php';  
            } else {
                window.location.href = 'payment_reversal.php';  
            }           
            
            $('#ajax_loader').hide();

          }else if(res.status == 'fail_attempt'){
            $('#ajax_loader').hide();
            $('#api_response').text(res.failed_message);
            $.colorbox({
              href: $('.failed_popup'),
              inline:true,
              width: '470px',
              height: '300px',
              html: 'test',
              fastIframe: false,
              escKey: false,
              overlayClose: false,
            });
          }else if(res.status=='processor_inactive'){
            $('#ajax_loader').hide();
            setNotifyError("Merchant Account Closed: Refund must be done by check");
          }else{
            $('#ajax_loader').hide();
            var is_error = true;
            $.each(res.errors, function(index, error) {
              console.log('#err_' + index);
              $('#err_' + index).html(error);
              if (is_error) {
                var offset = $('#err_' + index).offset();
                if (typeof(offset) === "undefined") {
                  console.log("Not found : " + index);
                } else {
                  var offsetTop = offset.top;
                  var totalScroll = offsetTop - 195;
                  $('body,html').animate({
                    scrollTop: totalScroll
                  }, 1200);
                  is_error = false;
                }
              }
            });
          }

          enableButton($("#refund_submit"));
          common_select();
        }
      });
      return false;

    // }


  });
})
</script>