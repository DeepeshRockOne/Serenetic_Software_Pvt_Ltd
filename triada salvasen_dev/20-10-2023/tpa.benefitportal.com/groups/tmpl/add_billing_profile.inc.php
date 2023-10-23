<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">+ Billing Profile - <span class="fw300">New</span></h4>
	</div>
	<div class="panel-body">
		<form action="" id="billing_form" name="billing_form">
			<input type="hidden" name="customer_id" value="<?=$customer_id?>" id="customer_id">
			<input type="hidden" name="bill_id" value="<?=$bill_id?>" id="bill_id">
			<input type="hidden" name="is_billing_ajaxed" value="1" id="is_billing_ajaxed">
			<input type="hidden" name="action" value="<?=$action?>" id="action">
			<input type="hidden" name="is_valid_address" id="is_valid_address" value="">
			<div class="row theme-form">
				<div class="col-sm-6">
					<div class="form-group">
						<select class="form-control" name="payment_mode" id="payment_mode">
							<option data-hidden="true"></option>
							<option value="CC" <?= $payment_mode =='CC' ? 'selected="selected"' : ''?>>Credit Card</option>
							<option value="ACH" <?= $payment_mode =='ACH' ? 'selected="selected"' : ''?>>Bank Draft</option>
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
							<input type="text" name="name_on_card" value="<?=checkIsset($row['fname']).''.checkIsset($row['lname'])?>" class="form-control">
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
							<input type="text" name="card_number" maxlength="16" class="form-control" oninput="isValidNumber(this)">
							<label>Card Number<i class="text-red">*</i><?=checkIsset($row['last_cc_ach_no'])!='' && !empty($row['payment_mode']) && $row['payment_mode']=='CC' ? ' (*'.$row['last_cc_ach_no'].')' : ''?></label>
							<input type="hidden" name="full_card_number" id="full_card_number" value="<?=checkIsset($row['card_no_full'])?>">
							<p class="error error_card_number"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<select class="form-control" name="card_type" id="card_type">
								<option data-hidden="true"></option>
								<option value="Visa" <?= !empty($row['card_type']) && $row['card_type'] == 'Visa' ? 'selected="selected"' : '' ?>> Visa</option>
								<option value="MasterCard" <?= !empty($row['card_type']) && $row['card_type'] == 'MasterCard' ? 'selected="selected"' : '' ?>> MasterCard </option>
								<option value="Discover" <?= !empty($row['card_type']) && $row['card_type'] == 'Discover' ? 'selected="selected"' : '' ?>> Discover </option>
								<option value="Amex" <?= !empty($row['card_type']) && $row['card_type'] == 'Amex' ? 'selected="selected"' : '' ?>> American Express </option>
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
						</div>
					</div>
					<!-- <div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="" class="form-control" value="<?=checkIsset($row['address2'])?>">
							<label>Address 2 (Apt, Suite)</label>
						</div>
					</div> -->
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
							<input type="text" name="routing_number" id="routing_number" class="form-control" maxlength='9' oninput="isValidNumber(this)">
							<label>Routing Number<i class="text-red">*</i><?=checkIsset($row['ach_routing_number'])!='' && !empty($row['payment_mode']) && $row['payment_mode']=='ACH' ? ' (*'.substr($row['ach_routing_number'],-4).')' : ''?></label>
							<input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?=$row['ach_routing_number']?>">
							<p class="error error_routing_number"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="account_number" id="account_number" class="form-control" oninput="isValidNumber(this)">
							<label>Account Number<i class="text-red">*</i><?=checkIsset($row['last_cc_ach_no'])!='' && !empty($row['payment_mode']) && $row['payment_mode']=='ACH' ? ' (*'.$row['last_cc_ach_no'].')' : ''?></label>
							<input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=checkIsset($row['ach_account_number'])?>">
							<p class="error error_account_number"></p>
						</div>
					</div>
				</div>
			</div>
			<!-- <h4>Is Default</h4> -->
			<div class="phone-control-wrap m-b-25">
				<input type="checkbox" id="is_default" name="is_default"  <?=checkIsset($row['is_default']) !='' && $row['is_default'] == 'Y' ? 'checked="checked"' : '' ?>> Set as default billing method
			</div>
			<div class="text-center">
				<a href="javascript:void(0)" class="btn btn-info" id="save_billing_info">Save</a>
				<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">

//google map api for address start

$(document).on('focus','#bill_address',function(){
   $("#is_valid_address").val('N');
});

$(document).on('blur','#bill_address',function(){
   if($("#is_valid_address").val() == 'N'){
      $("#bill_address").val("");
      $("#bill_address").attr('placeholder', '');
   }
});

function initAutocompleteBill() {              
	var input = document.getElementById('bill_address');
	var options = {
		types: ['geocode'],
		componentRestrictions: {country: 'us'}
	};
	autocomplete = new google.maps.places.Autocomplete(input, options);
	autocomplete.setFields(['address_component']);
	autocomplete.addListener('place_changed', fillInAddressBill);
}

function fillInAddressBill() {
   $("#is_valid_address").val('N');
   var place = autocomplete.getPlace();
   var address = "";
   var zip = "";
   var city = "";
   var state = "";
//    var defaultZip = $("#primary_policy_form #primary_zip").val();
   $(".error").html('');
   for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if(addressType == "street_number"){
      var val = place.address_components[i]["short_name"];
         address = address + " "+ val;
      }else if(addressType=="route"){
      var val = place.address_components[i]["long_name"];
      address = address + " "+ val;
      }else if(addressType=="postal_code"){
      zip = place.address_components[i]["short_name"];
      }else if(addressType=="locality"){
      city = place.address_components[i]["short_name"];
      }else if(addressType == "administrative_area_level_1"){
      state = place.address_components[i]["long_name"];
      }
   }
   // if(zip != defaultZip){
   //    $("#primary_policy_form #address").val('');
   //    $("#primary_policy_form #error_address").html("Address Not Match with zipcode");
   // }else{
      $("#billing_form #bill_zip").val(zip).addClass('has-value');;
      $("#billing_form #bill_address").val(address);
      $("#billing_form #bill_address").addClass('has-value');
      $("#billing_form #bill_city").val(city).addClass('has-value');;
      $("#billing_form #bill_state").val(state).change();
      $("#is_valid_address").val('Y');
   // }

}
//google map api for address end

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

	<?php if($SITE_ENV == 'Live') { ?>
		initAutocompleteBill();     
   <?php } ?>
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
   e.preventDefault();
   $.ajax({
      url : "add_billing_profile.php",
      type : 'POST',
      data:$("#billing_form").serialize(),
      dataType:'json',
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success : function(res){
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
});
</script>