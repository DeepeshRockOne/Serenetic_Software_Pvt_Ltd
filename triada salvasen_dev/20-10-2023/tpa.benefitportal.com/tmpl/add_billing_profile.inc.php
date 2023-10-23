<div class="panel panel-default">
	<div class="panel-heading">
		<?php if($action == 'Edit'){ ?>
			<h4 class="mn">Billing Profile - <span class="fw300">Edit</span></h4>
		<?php } else { ?>
			<h4 class="mn">+ Billing Profile - <span class="fw300">New</span></h4>
		<?php } ?>		
	</div>
	<div class="panel-body">
		<form action="" id="billing_form" name="billing_form">
			<input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
			<input type="hidden" name="customer_id" value="<?=$customer_id?>" id="customer_id">
			<input type="hidden" name="location" value="<?=$location?>" id="location">
			<input type="hidden" name="bill_id" value="<?=$bill_id?>" id="bill_id">
			<input type="hidden" name="is_billing_ajaxed" value="1" id="is_billing_ajaxed">
			<input type="hidden" name="action" value="<?=$action?>" id="action">
			<input type="hidden" name="is_valid_address" id="is_valid_address" value="">
			<div class="row theme-form">
				<div class="col-sm-6">
					<div class="form-group">
						<select class="form-control" name="payment_mode" id="payment_mode">
							<option data-hidden="true"></option>
							 <?php 
                                if(!empty($pyament_methods)){
                                    if($pyament_methods['is_cc_accepted']){ ?>
                                        <option value="CC" <?=!empty($payment_mode) && $payment_mode == "CC" ? "selected='selected'" : ""?>>Credit Card</option>
                                <?php 
                            		}
                                    if($pyament_methods['is_ach_accepted']){ ?>
                                        <option value="ACH" <?=!empty($payment_mode) && $payment_mode == "ACH" ? "selected='selected'" : ""?>>ACH Bank Draft</option>
                                <?php }
                                }
                            ?>
						</select>
						<label>Payment Method</label>
						<p class="error error_payment_mode"></p>
					</div>
				</div>
			</div>
			<div id="card_div" class="billing_info">
				<h4>Card Details</h4>
				<div class="row theme-form">
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" id="name_on_card" name="name_on_card" value="<?=checkIsset($row['fname']).''.checkIsset($row['lname'])?>" class="form-control">
							<label>Full Name<i class="text-red">*</i></label>
							<p class="error error_name_on_card"></p>
						</div>
					</div>
					<!-- <div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="cc_lname" value="<?=checkIsset($row['lname'])?>" class="form-control">
							<label>Last Name<i class="text-red">*</i></label>
						</div>
					</div> -->
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="card_number" maxlength="16" oninput="isValidNumber(this)" class="form-control">
							<label>Card Number<i class="text-red">*</i><?=checkIsset($row['last_cc_ach_no'])!='' && !empty($row['payment_mode']) && $row['payment_mode']=='CC' ? ' (*'.$row['last_cc_ach_no'].')' : ''?></label>
							<input type="hidden" name="full_card_number" id="full_card_number" value="<?=checkIsset($row['card_no_full'])?>">
							<p class="error error_card_number"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<select class="form-control" name="card_type" id="card_type">
								<option data-hidden="true"></option>

								<?php if(in_array('Visa', $acceptable_cc)){ ?>
	                            <option value="Visa" <?= !empty($row['card_type']) && $row['card_type'] == 'Visa' ? 'selected="selected"' : '' ?>> Visa </option>
	                           <?php } ?>
	                           <?php if(in_array('MasterCard', $acceptable_cc)){ ?>
	                            <option value="MasterCard" <?= !empty($row['card_type']) && $row['card_type'] == 'MasterCard' ? 'selected="selected"' : '' ?>> MasterCard </option>
	                            <?php } ?>
	                           <?php if(in_array('Discover', $acceptable_cc)){ ?>
	                            <option value="Discover" <?= !empty($row['card_type']) && $row['card_type'] == 'Discover' ? 'selected="selected"' : '' ?>> Discover </option>
	                            <?php } ?>
	                           <?php if(in_array('Amex', $acceptable_cc)){ ?>
	                            <option value="Amex" <?= !empty($row['card_type']) && $row['card_type'] == 'Amex' ? 'selected="selected"' : '' ?>> American Express </option>
	                            <?php } ?>
							</select>
							<label>Card Type<i class="text-red">*</i></label>
							<p class="error error_card_type"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<!-- <select class="form-control" value="<?=checkIsset($row['fname'])?>">
								<option data-hidden="true"></option>
								<option>1</option>
							</select>
							<label>Expired Month<i class="text-red">*</i></label> -->
							<?php
								$date = '';
								if(!empty($row['expiry_month']) && !empty($row['expiry_year'])){
									$date = date('m/y',strtotime($row['expiry_month']."/01/".$row['expiry_year']));
								}
							?>
							<input type="text" name="expiration" id="expiration" value="<?=$date?>" class="form-control">
							<label>Expiration Date<i class="text-red">*</i></label>
							<p class="error error_expiration"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group height_auto m-b-15">
							<input type="text" name="cvv" id="cvv" oninput="isValidNumber(this)" minlength="3" maxlength="4" value="<?=checkIsset($row['cvv_no'])?>" class="form-control" >
							<label>CVV*</label>
							<p class="error error_cvv"></p>
						</div>
						<!-- <input type="hidden" name="require_cvv" value="<?=$payment_res['require_cvv'] == 'Y' ? 'yes' : 'no'?>"> -->
					</div>
					<!-- <div class="col-sm-6">
						<div class="form-group">
							<select class="form-control" value="<?=checkIsset($row['fname'])?>">
								<option data-hidden="true"></option>
								<option>2020</option>
							</select>
							<label>Expired Year<i class="text-red">*</i></label>
						</div>
					</div> -->
				</div>
				<h4>Billing Address</h4>
				<div class="row theme-form">
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="bill_address" id="bill_address" class="form-control" value="<?=checkIsset($row['address'])?>">
							<label>Address<i class="text-red">*</i></label>
							<p class="error error_bill_address"></p>
							<input type="hidden" name="old_bill_address" value="<?=checkIsset($row['address'])?>">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="bill_address_2" id="bill_address_2" class="form-control" value="<?=checkIsset($row['address2'])?>" onkeypress="return block_special_char(event)">
							<label>Address 2 (Apt, Suite)</label>
							<p class="error error_bill_address_2"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="bill_city" class="form-control" id="bill_city" value="<?=checkIsset($row['city'])?>">
							<label>City<i class="text-red">*</i></label>
							<p class="error error_bill_city"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<select class="form-control" name="bill_state" id="bill_state">
								<option data-hidden="true"></option>
								<!-- <option>Alabama</option> -->
								<?php if(!empty($allStateRes)){
									foreach($allStateRes as $states){ ?>
									<option value="<?=$states['name']?>" <?=!empty($row['state']) &&  $row['state'] == $states['name'] ? 'selected="selected"': ''?>><?=$states['name']?></option>
								<?php } } ?>
							</select>
							<label>State<i class="text-red">*</i></label>
							<p class="error error_bill_state"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="bill_zip" maxlength="5" id="bill_zip" class="form-control" oninput="isValidNumber(this)" value="<?=checkIsset($row['zip'])?>">
							<label>Zip/Postal Code<i class="text-red">*</i></label>
							<p class="error error_bill_zip"></p>
							<input type="hidden" name="old_bill_zip" value="<?=checkIsset($row['zip'])?>">
						</div>
					</div>
				</div>
			</div>
			<div id="bank_draft_div" class="billing_info">
				<h4>Bank Draft Details</h4>
				<div class="row theme-form">
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="ach_name" value="<?=checkIsset($row['fname']).''.checkIsset($row['lname'])?>" class="form-control">
							<label>Full Name<i class="text-red">*</i></label>
							<p class="error error_ach_name"></p>
						</div>
					</div>
					<!-- <div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="" value="<?=checkIsset($row['lname'])?>" class="form-control">
							<label>Last Name<i class="text-red">*</i></label>
						</div>
					</div> -->
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="bank_name" value="<?=checkIsset($row['bankname'])?>" class="form-control">
							<label>Bank Name<i class="text-red">*</i></label>
							<p class="error error_bank_name"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<select class="form-control" name="account_type" id="account_type">
								<option data-hidden="true"></option>
								<option value="checking" <?=!empty($row['ach_account_type']) &&  $row['ach_account_type'] =='checking' ? 'selected="selected"' : ''?>>Checking</option>
								<option value="savings" <?=!empty($row['ach_account_type']) &&  $row['ach_account_type'] =='savings' ? 'selected="selected"' : ''?>>Saving</option>
							</select>
							<label>Account Type<i class="text-red">*</i></label>
							<p class="error error_account_type"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="routing_number" id="routing_number" class="form-control" oninput="isValidNumber(this)" maxlength='9'>
							<label>Routing Number<i class="text-red">*</i><?=checkIsset($row['ach_routing_number'])!='' && !empty($row['payment_mode']) && $row['payment_mode']=='ACH' ? ' (*'.substr($row['ach_routing_number'],-4).')' : ''?></label>
							<input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?=$row['ach_routing_number']?>">
							<p class="error error_routing_number"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="account_number" id="account_number" class="form-control" maxlength="17" oninput="isValidNumber(this)">
							<label>Account Number<i class="text-red">*</i><?=checkIsset($row['last_cc_ach_no'])!='' && !empty($row['payment_mode']) && $row['payment_mode']=='ACH' ? ' (*'.$row['last_cc_ach_no'].')' : ''?></label>
							<input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=checkIsset($row['ach_account_number'])?>">
							<p class="error error_account_number"></p>
						</div>
					</div>
				</div>
			</div>
			<!-- <h4>Is Default</h4> -->
			<div class="phone-control-wrap m-b-25 <?=checkIsset($row['is_default']) !='' && $row['is_default'] == 'Y' ? 'hidden' : '' ?>">
				<input type="checkbox" id="is_default" name="is_default"  <?=checkIsset($row['is_default']) !='' && $row['is_default'] == 'Y' ? 'checked="checked"' : '' ?>> Set as default billing method
			</div>
			<div class="text-center">
				<button type="button" class="btn btn-info" id="save_billing_info">Save</button>
				<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
			</div>
		</form>
	</div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once __DIR__.'/suggested_address.inc.php'; ?>
  </div>
</div>
<script type="text/javascript">
//google map api for address start

$(document).on('focus','#bill_address,#bill_zip',function(){
   $("#is_address_ajaxed").val(1);
});

$(document).on('blur','#bill_address',function(){
   /*if($("#is_valid_address").val() == 'N'){
      $("#bill_address").val("");
      $("#bill_address").attr('placeholder', '');
   }*/
});

$(document).ready(function(e){
	$(".billing_info").hide();
	<?php if($action == 'Edit' && !empty($row['payment_mode']) && $row['payment_mode']=='ACH'){ ?>
		$('#bank_draft_div').show();
	<?php }else{ ?>
		$('#card_div').show();
	<?php } ?>

	$('#expiration').datepicker({
		format: 'mm/yy',
		startView : 1,
		minViewMode: 1,
		autoclose: true,	
		startDate:new Date(),
		endDate : '+15y'
	});

});
$(document).off('change','#payment_mode');
$(document).on('change','#payment_mode',function(e){
	$(".billing_info").hide();
	if($(this).val() == 'ACH'){
		$('#bank_draft_div').show();
	}else{
		$('#card_div').show();
	}
});

$(document).off('click','#save_billing_info');
$(document).on('click','#save_billing_info',function(e){
   	$is_address_ajaxed = $("#is_address_ajaxed").val();
	if($is_address_ajaxed == 1){
   		updateAddress();
	}else{
		ajaxSaveAccountDetails();
	}
});

function ajaxSaveAccountDetails(){
    parent.disableButton($("#save_billing_info"));
    $.ajax({
      url : "add_billing_profile.php",
      type : 'POST',
      data:$("#billing_form").serialize(),
      dataType:'json',
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success : function(res){
		parent.enableButton($("#save_billing_info"));
        $("#ajax_loader").hide();
        $(".error").html("");
        if(res.status =='success'){
			parent.$.colorbox.close();
            parent.ajax_get_member_data('member_billing_tab.php','billing_tab','<?=$customer_id?>');
            parent.setNotifySuccess(res.msg);
        }else{
           $.each(res.errors,function(index,error){
               $(".error_"+index).html(error).show();
           });
        }
      }
    });
}

function updateAddress(){
   $.ajax({
      url : "add_billing_profile.php",
      type : 'POST',
      data:$("#billing_form").serialize(),
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
            // $("#is_address_verified").val('N');
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
                        // $("#is_address_verified").val('Y');
                     }else{
                        // $("#is_address_verified").val('N');
                     }
                     ajaxSaveAccountDetails();
                  },
            });
         }else if(res.status == 'success'){
            // $("#is_address_verified").val('N');
            ajaxSaveAccountDetails();
         }else{
            $.each(res.errors,function(index,error){
               $(".error_"+index).html(error).show();
           });
         }
         $('#bill_state').selectpicker('refresh');
      }
   });
}
</script>
<?php if($SITE_ENV == 'Live') { ?>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places&callback=initAutocompleteBill"></script>
<?php } ?>