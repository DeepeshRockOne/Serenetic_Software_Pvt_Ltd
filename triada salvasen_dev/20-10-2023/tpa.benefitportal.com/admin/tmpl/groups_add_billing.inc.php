<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">+ Billing File - <span class="fw300"><?= $resGroup['business_name'].' ('.$resGroup['rep_id'] .')' ?></span></h4>
		</div>
	</div>
	<div class="panel-body">
		<form action="" name="group_add_billing_form" id="group_add_billing_form" method="POST">
			<input type="hidden" name="group_id" id="group_id" value="<?=$resGroup['id'] ?>">
    		<input type="hidden" name="billing_id" id="billing_id" value="<?=$billing_id?>">
    		<input type="hidden" name="is_display_payment" id="is_display_payment" value="N">
    		<input type="hidden" name="is_valid_address" id="is_valid_address" value="<?= !empty($is_valid_address) ? $is_valid_address : 'N' ?>">
        <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
			<div class="theme-form">
				<div class="form-group">
					<select class="form-control" name="company_id" id="company_id">
						<option data-hidden="true"></option>
						<option value="0" <?= isset($company_id) && $company_id == 0 ? 'selected' : '' ?>><?= $resGroup['business_name'] ?></option>
						<?php if(!empty($resCompany)) { ?>
							<?php foreach ($resCompany as $key => $companyRow) { ?>
									<option value="<?= $companyRow['id'] ?>" <?= !empty($company_id) && $company_id == $companyRow['id'] ? 'selected' : '' ?>><?= $companyRow['name'] ?></option>		
							<?php } ?>
						<?php } ?>
					</select>
					<label>Company</label>
					<p class="error" id="error_company_id"></p>
				</div>
				<p class="fw500">Select method this company/group will use for list bill payments:</p>
				<div class="m-b-25">
					<input type="hidden" name="available_payment" id="available_payment" value="<?=!empty($available_payment) ? implode(',',$available_payment) : '' ?>">
					<div class="m-b-10" style="<?= in_array('CC',$available_payment) || in_array('ACH',$available_payment)  ? '' : 'display: none' ?>">
						<label class="mn"><input type="radio" name="payment_mode" value="ACH_CC" <?= !empty($payment_mode) && ($payment_mode=="ACH" || $payment_mode=="CC") ? 'checked' : '' ?>> ACH/CC </label>
					</div>
					<div class="mn" style="<?= in_array('Check',$available_payment) ? '' : 'display: none' ?>">
						<label class="mn"><input type="radio" name="payment_mode" value="Check" <?= !empty($payment_mode) && $payment_mode=="Check" ? 'checked' : '' ?>> Check</label>
					</div>
					<p class="error" id="error_payment_mode"></p>
				</div>
				<div id="ACH_CC_div" style="<?= !empty($payment_mode) && ($payment_mode=="ACH" || $payment_mode=="CC") ? '' : 'display: none' ?>">
					<div class="row m-t-10">
						<div class="col-sm-4">
							<div class="form-group height_auto">
								<select class="form-control" id="billing_profile_id" name="billing_profile_id">
									<option value=""></option>
									<?php if(!empty($billingRes)) { ?>
										<?php foreach ($billingRes as $key => $billingRow) { ?>
											<option value="<?= $billingRow['id'] ?>" <?= !empty($billing_id) && $billing_id == $billingRow['id'] ? 'selected' : '' ?>><?=$billingRow['card_type'].' (*'.$billingRow['last_cc_ach_no'].')'?></option>
										<?php } ?>
									<?php } ?>
								</select>
								<label>Existing Profiles</label>
								<p class="error" id="error_billing_profile_id"></p>
								<div class="text-right m-t-5">
									<a href="javascript:void(0);" class="red-link fs12 fw500" id="add_payment_mathod">+ Payment Method</a>
								</div>
							</div>
						</div>
					</div>
					<div  id="billing_profile_div" style="display: none">
				     
					</div>
					
				</div>
				<div class="m-b-25">
					<?php if($display_cc_charge) { ?>
						<div class="p-15 bg_light_gray text-center text-light-gray" id="cc_charge_div" style="<?= !empty($payment_mode) && ($payment_mode=="CC") ? '' : 'display: none' ?>">
							<?= $cc_charge_amount ?> service charge will be applied if you pay by credit card
						</div>
					<?php } ?>
					<?php if($display_check_charge) { ?>
						<div class="p-15 bg_light_gray text-center text-light-gray" id="check_charge_div" style="<?= !empty($payment_mode) && ($payment_mode=="Check") ? '' : 'display: none' ?>">
							<?= $check_charge_amount ?> service charge will be applied if you pay by check
						</div>
					<?php } ?>
				</div>
				
			</div>
			<div class="clearfix text-center">
				<a href="javascript:void(0);" class="btn btn-action" id="group_add_billing">Save</a>
				<a href="javascript:void(0);" class="btn red-link" id="group_cancel_billing" onClick="$.colorbox.close();">Close</a>
			</div>
		</form>
		<div id="add_billing_info_div" style="display: none"></div>
	</div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>
<script type="text/javascript">
	var $site_location = '<?= $SITE_ENV ?>';
    var placeSearch, autocomplete;

	$(document).ready(function(){
		$billing_id = '<?= $billing_id ?>';

		if($billing_id > 0){
			load_payment_method($billing_id);
		}

		
        
	});
	$(document).on("change","input[name=payment_mode]",function(){
        $val=$(this).val();
        $("#ACH_CC_div").hide();
        $("#is_display_payment").val('N');
        $("#billing_profile_div").hide();
        $("#cc_charge_div").hide();
		$("#check_charge_div").hide();
         if($val=='ACH_CC'){
            $("#ACH_CC_div").show();
         }else if($val == "Check"){
         	$("#check_charge_div").show();
         }
  	});
  	$(document).on("click","#add_payment_mathod",function(){
  		$("#billing_profile_id").val('');
  		$("#billing_profile_id").selectpicker('refresh');
  		load_payment_method(0);
  	});
  	$(document).on("change","#billing_profile_id",function(e){
  		e.preventDefault();
  		$billing_profile_id = $(this).val();

  		if($billing_profile_id > 0){
  			load_payment_method($billing_profile_id);
  		}else{
  			$("#is_display_payment").val('N');
  			$("#billing_profile_div").hide();
  		}
  	});

  	load_payment_method = function($billing_profile_id){
  		$("#ajax_loader").show();
		var $available_payment = $("#available_payment").val();
  		$.ajax({
  			url:'ajax_load_group_billing_profile.php',
  			dataType:'JSON',
  			data:{billing_profile:$billing_profile_id,available_payment:$available_payment},
  			type:"POST",
  			success:function(res){
  				$("#ajax_loader").hide();
  				$("#is_display_payment").val('Y');
  				$("#billing_profile_div").html(res.html).show();
  				$("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
  				$('#expiration').datepicker({
				          format: 'mm/yy',
				          startView : 1,
				          minViewMode: 1,
				          autoclose: true,  
				          startDate:new Date(),
				          endDate : '+15y'
		        });
		        fRefresh();
		        common_select();
		        
		        
		        
  			}
  		})
  	}
  	$(document).on("change","input[name=payment_type]",function(){
         $val=$(this).val();
         $("#achDiv").hide();
         $("#CCDiv").hide();
         $("#cc_charge_div").hide();
         $("#check_charge_div").hide();
         if($val=='ACH'){
            $("#achDiv").show();
         }else if($val == "CC"){
             $("#CCDiv").show();
             $("#cc_charge_div").show();
         }
  	});


  	$(document).off('click','#group_cancel_billing');
    $(document).on('click','#group_cancel_billing',function(e){
    	window.parent.$.colorbox.close();
    });
    $(document).off('click','#group_add_billing');
    $(document).on('click','#group_add_billing',function(e){
        $is_address_ajaxed = $("#is_address_ajaxed").val();
        if($is_address_ajaxed == 1){
          updateAddress();
        }else{
          ajaxSaveAccountDetails();
        }
    });

    function ajaxSaveAccountDetails(){
      $("#ajax_loader").show();
      $(".error").html('');
      $.ajax({
          url:'ajax_group_add_billing.php',
          dataType:'JSON',
          data:$("#group_add_billing_form").serialize(),
          type:'POST',
          success:function(data){
              $("#ajax_loader").hide();
              if (data.status == 'success') {
                  parent.$.colorbox.close();
              parent.ajax_get_group_data('group_billing.php','gp_billing');
              parent.setNotifySuccess(data.msg);
              } else if (data.status == "fail") {
                  setNotifyError("Oops... Something went wrong please try again later");
              } else {
                  $.each(data.errors, function(key, value) {
                      $('#error_' + key).parent("p.error").show();
                      $('#error_' + key).html(value).show();
                      $('.error_' + key).parent("p.error").show();
                      $('.error_' + key).html(value).show();
                     
                  });
              }
          }
      });
    }

    function updateAddress(){
      $.ajax({
        url : "ajax_group_add_billing.php",
        type : 'POST',
        data:$("#group_add_billing_form").serialize(),
        dataType:'json',
        beforeSend :function(e){
           $("#ajax_loader").show();
           $(".error").html('');
        },success(res){
           $("#is_address_ajaxed").val("");
           $("#ajax_loader").hide();
           $(".suggested_address_box").uniform();
           if(res.zip_response_status =="success"){
              $("#bill_state").val(res.state).addClass('has-value');
              $("#bill_city").val(res.city).addClass('has-value');
              ajaxSaveAccountDetails();
           }else if(res.address_response_status =="success"){
              $(".suggestedAddressEnteredName").html($("#name_on_card").val());
              $("#bill_state").val(res.state).addClass('has-value');
              $("#bill_city").val(res.city).addClass('has-value');
              $(".suggestedAddressEntered").html(res.enteredAddress);
              $(".suggestedAddressAPI").html(res.suggestedAddress);
              $("#is_valid_address").val('Y');
              $.colorbox({
                    inline:true,
                    href:'#suggestedAddressPopup',
                    height:'500px',
                    width:'650px',
                    escKey:false, 
                    overlayClose:false,
                    closeButton:false,
                    onClosed:function(){
                       $suggestedAddressRadio = $("input[name='suggestedAddressRadio']:checked"). val();
                       
                       if($suggestedAddressRadio=="Suggested"){
                          $("#bill_address").val(res.address).addClass('has-value');
                          $("#bill_address_2").val(res.address2).addClass('has-value');
                       }
                       ajaxSaveAccountDetails();
                    },
              });
           }else if(res.status == 'success'){
              ajaxSaveAccountDetails();
           }else{
              $.each(res.errors,function(index,error){
                 $("#error_"+index).html(error).show();
             });
           }
           $("#bill_state").selectpicker('refresh');
        }
      });
    }

	$(document).on("focus","#bill_address,#bill_zip",function(e){
		$('#is_address_ajaxed').val(1);
	});

</script>