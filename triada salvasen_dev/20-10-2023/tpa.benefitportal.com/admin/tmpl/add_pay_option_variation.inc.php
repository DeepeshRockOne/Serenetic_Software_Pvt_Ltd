<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">+ Pay Option - <span class="fw300">Variation</span></h4>
	</div>
	<form  method="POST" id="manage_group_form" enctype="multipart/form-data"  autocomplete="off">
		<input type="hidden" name="rule_id" id="rule_id" value="<?= $rule_id ?>">
	<div class="panel-body theme-form">
		<p class="m-b-20">Assign this Variation to a group below.</p>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<select class="se_multiple_select" id="assign_group" name="assign_group[]" multiple="multiple">
						<?php if(!empty($resGroup)) { ?>
							<?php foreach ($resGroup as $key => $value) { ?>
								<option value="<?= $value['id'] ?>" <?= !empty($group_id) && $group_id == $value['id'] ? 'selected' : '' ?>> <?= $value['rep_id'] ." - ". $value['business_name'] ?> </option>
							<?php } ?>
						<?php } ?>
					</select>
					<label>Assign Group(s)</label>
					<p class="error" id="error_assign_group"></p>
				</div>
			</div>
		</div>
		<hr class="m-t-0">
		<p class="m-b-20">Select all pay options that apply to groups.</p>
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
					<select class="se_multiple_select" id="pay_options" name="pay_options[]" multiple="multiple">
						<option value="ACH" <?= !empty($is_ach) && $is_ach=='Y' ? 'selected' : '' ?>>ACH/Bank Draft</option>
						<option value="CC" <?= !empty($is_cc) && $is_cc=='Y' ? 'selected' : '' ?>>Credit Card</option>
						<option value="Check" <?= !empty($is_check) && $is_check=='Y' ? 'selected' : '' ?>>Check</option>
					</select>
					<label>Select Pay Options</label>
					<p class="error" id="error_pay_options"></p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4" id="CC_div" style="<?= !empty($is_cc) && $is_cc=='Y' ? '' : 'display: none' ?>">
				<div class="pay_option_box">
					<h5 class="m-t-0">Credit Card</h5>
					<p class="m-b-20">If paid by credit card, should there be an additional charge for this payment method?</p>
					<div class="form-group ">
						<div class="m-b-10">
							<label class="mn"><input type="radio" name="cc_additional_charge" value="Y" <?= !empty($cc_additional_charge) && $cc_additional_charge =='Y' ? 'checked' : '' ?>> Yes</label>
						</div>
						<div class="m-b-10">
							<label class="mn"><input type="radio" name="cc_additional_charge" value="N" <?= !empty($cc_additional_charge) && $cc_additional_charge =='N' ? 'checked' : '' ?>> No</label>
						</div>
						<p class="error" id="error_cc_additional_charge"></p>
					</div>
					<div id="cc_additional_charge_div" style="<?= !empty($cc_additional_charge) && $cc_additional_charge =='Y' ? '' : 'display: none' ?>">
						<p class="m-b-15">Select Charge Type:</p>
						<div class="form-group ">
							<div class="m-b-10">
								<label class="mn"><input type="radio" name="cc_charge_type" value="Fixed" <?= !empty($cc_charge_type) && $cc_charge_type =='Fixed' ? 'checked' : '' ?>> Fixed Amount</label>
							</div>
							<div class="m-b-10">
								<label class="mn"><input type="radio" name="cc_charge_type" value="Percentage" <?= !empty($cc_charge_type) && $cc_charge_type =='Percentage' ? 'checked' : '' ?>> Percentage</label>
							</div>
							<p class="error" id="error_cc_charge_type"></p>
						</div>
						<div class="input-group">
							<span class="input-group-addon" id="Fixed_div" style="<?= !empty($cc_charge_type) && $cc_charge_type =='Fixed' ? '' : 'display: none' ?>"><i class="fa fa-usd"></i></span>
							<div class="pr">
								<input type="text" id="cc_charge" name="cc_charge" class="form-control" value="<?= isset($cc_charge) ? $cc_charge : '' ?>" onkeypress="return isNumberOnly(event)">
								<label class="label-wrap">Credit Card Charge By Invoice</label>
								
							</div>
							<span class="input-group-addon" id="Percentage_div" style="<?= !empty($cc_charge_type) && $cc_charge_type =='Percentage' ? '' : 'display: none' ?>"><i class="fa fa-percent"></i></span>
						</div>
						<p class="error" id="error_cc_charge"></p>
					</div>
				</div>
			</div>
			<div id="Check_div" style="<?= !empty($is_check) && $is_check=='Y' ? '' : 'display: none' ?>">
				<div class="col-sm-4">
					<div class="pay_option_box">
						<h5 class="m-t-0">Check</h5>
						<p class="m-b-20">If paid by check should there be an additional charge for this payment method?</p>
						<div class="form-group ">
							<div class="m-b-10">
								<label class="mn"><input type="radio" name="check_additional_charge" value="Y" <?= !empty($check_additional_charge) && $check_additional_charge == 'Y' ? 'checked' : '' ?>> Yes</label>
							</div>
							<div class="m-b-10">
								<label class="mn"><input type="radio" name="check_additional_charge" value="N" <?= !empty($check_additional_charge) && $check_additional_charge == 'N' ? 'checked' : '' ?>> No</label>
							</div>
							<p class="error" id="error_check_additional_charge"></p>
						</div>
						<div id="check_additional_charge_div" style="<?= !empty($check_additional_charge) && $check_additional_charge =='Y' ? '' : 'display: none' ?>">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-usd"></i></span>
								<div class="pr">
									<input type="text" class="form-control" id="check_charge" name="check_charge" value="<?= isset($check_charge) ? $check_charge : '' ?>" onkeypress="return isNumberOnly(event)">
									<label>Check Charge 0.00</label>
									
								</div>
							</div>
							<p class="error" id="error_check_charge"></p>
						</div>
					</div>
				</div>
				<div class="col-sm-4" >
					<div class="pay_option_box">
						<h5 class="m-t-0">Check Remit to Address</h5>
						<div class="form-group">
							<textarea class="form-control" id="remit_to_address" name="remit_to_address" placeholder="Remit To Address" rows="4"><?= !empty($remit_to_address) ? $remit_to_address : '' ?></textarea>
							<p class="error" id="error_remit_to_address"></p>
						</div>
					</div>
				</div>
			</div>
			<div id="ACH_div" style="<?= !empty($is_ach) && $is_ach=='Y' ? '' : 'display: none' ?>">
				
			</div>
		</div>
		<div class="text-center">
			<a href="javascript:void(0);" class="btn btn-action" id="save">Save</a>
			<a href="javascript:void(0);" class="btn red-link" id="cancel">Cancel</a>
		</div>
	</div>
	</form>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#pay_options").multipleSelect({
	       selectAll: false,
	       filter:false,
	       onClick:function(e){
				$pay_options = e.value;
				if(e.selected){
					$("#"+$pay_options+"_div").show();
				}else{
					$("#"+$pay_options+"_div").hide();
				}
			},
			onTagRemove:function(e){
				$pay_options = e.value;
				$("#"+$pay_options+"_div").hide();
			}
  	});
	$("#assign_group").multipleSelect({
		
	});
});

$(document).on("change","input[name=cc_additional_charge]",function(){
	$val=$(this).val();
	$("#cc_additional_charge_div").hide();
	if($val=="Y"){
		$("#cc_additional_charge_div").show();
	}
});

$(document).on("change","input[name=cc_charge_type]",function(){
	$val=$(this).val();
	$("#Fixed_div").hide();
	$("#Percentage_div").hide();
	if($val=="Fixed"){
		$("#Fixed_div").show();
	}else if($val=="Percentage"){
		$("#Percentage_div").show();
	}
});

$(document).on("change","input[name=check_additional_charge]",function(){
	$val=$(this).val();
	$("#check_additional_charge_div").hide();
	if($val=="Y"){
		$("#check_additional_charge_div").show();
	}
});

//******************** Button Code Start **********************
	$(document).on("click","#save",function(){
		$("#ajax_loader").show();
		$(".error").html("");
		$.ajax({
			url:'ajax_add_pay_option_variation.php',
			dataType:'JSON',
			data:$("#manage_group_form").serialize(),
			type:"POST",
			success:function(res){
				$("#ajax_loader").hide();
				if(res.status=="success"){
					window.parent.setNotifySuccess("Pay Options Variation Added Successfully");
					window.parent.$.colorbox.close();
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
		});
	});
	$(document).on("click","#cancel",function(){
		window.parent.$.colorbox.close();
	});
//******************** Button Code End   **********************

isNumberOnly = function(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode != 8 && charCode != 46 && charCode != 47 && charCode != 0 && (charCode < 48 || charCode > 57)) {
          return false;
      }
      return true;
}

</script>