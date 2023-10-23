<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Edit Information</h4>
	</div>
	<div class="panel-body">
		<p class="fs32 fw300 mb30">Primary Details</p>
		<div class="row enrollment_auto_row theme-form">
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="fname" id="primary_fname" value="<?=$customer_res['fname']?>" class="form-control">
					<label>First Name*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="lname" id="primary_lname" value="<?=$customer_res['lname']?>" class="form-control">
					<label>Last Name*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="ssn" id="primary_ssn" value="<?=$customer_res['ssn']?>" class="form-control ssn">
					<label>SSN*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="email" id="primary_email" readonly value="<?=$customer_res['email']?>" class="form-control">
					<label>Email*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="address" id="primary_address" readonly value="<?=$customer_res['address']?>" class="form-control">
					<label>Address</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="city" id="primary_city" readonly value="<?=$customer_res['city']?>" class="form-control">
					<label>City*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="state" id="primary_state" readonly value="<?=$customer_res['state']?>" class="form-control">
					<label>State*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="zip" id="primary_zipcode" readonly value="<?=$customer_res['zip']?>" class="form-control">
					<label>Zip Code*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input  type="text" name="dob" id="primary_dob" readonly value="<?=getCustomDate($customer_res['birth_date'])?>" class="form-control" >
							<label>DOB* (MM/DD/YYYY)</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<div class="btn-group colors" data-toggle="buttons">
						<label class="btn btn-info <?=$customer_res['gender'] == 'Male' ? 'active' : ''?>">
							<input type="radio" name="gender" readonly id="gender_male" autocomplete="off" class="js-switch"> Male
						</label>
						<label class="btn btn-info <?=$customer_res['gender'] == 'Female' ? 'active' : ''?>">
							<input type="radio" name="gender" readonly id="gender_female" autocomplete="off" class="js-switch"> Female
						</label>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<?php if(!empty($resCustomerDep)) { ?>
			<p class="fs32 fw300 mb30">Dependent Details</p>
			<?php $repeat = array() ; ?>
			<?php foreach($resCustomerDep as $dependent) : ?>
				
				<?php if(!in_array($dependent['relation'],$repeat)) { ?>
				<h4 class="mt0 mb30"><?=getRevRelation($dependent['relation'])?></h4>
				<?php array_push($repeat,$dependent['relation']); } ?>
				<div class="row enrollment_auto_row theme-form">
					<div class="col-lg-3 col-md-6" >
						<div class="form-group">
							<input type="text" name="dep_fname[<?=$dependent['id']?>]" id="dep_fname_[<?=$dependent['id']?>]" value="<?=$dependent['fname']?>" class="form-control">
							<label>First Name*</label>
						</div>
					</div>
					<div class="col-lg-3 col-md-6" >
						<div class="form-group">
							<input type="text" name="dep_lname[<?=$dependent['id']?>]" id="dep_lname_[<?=$dependent['id']?>]" value="<?=$dependent['lname']?>" class="form-control">
							<label>Last Name*</label>
						</div>
					</div>
					<div class="col-lg-3 col-md-6" >
						<div class="form-group">
							<input type="text" name="dep_ssn[<?=$dependent['id']?>]" id="dep_ssn_[<?=$dependent['id']?>]" value="<?=$dependent['ssn']?>" class="form-control ssn">
							<label>SSN*</label>
						</div>
					</div>
					<div class="col-lg-3 col-md-6" >
						<div class="form-group">
							<input type="text" name="dep_email[<?=$dependent['id']?>]" id="dep_email_[<?=$dependent['id']?>]" readonly value="<?=$dependent['email']?>" class="form-control">
							<label>Email*</label>
						</div>
					</div>
					<div class="col-lg-3 col-md-6" >
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<div class="pr">
									<input  type="text" name="dep_dob[<?=$dependent['id']?>]" class="form-control" readonly id="dep_dob_[<?=$dependent['id']?>]" value="<?=getCustomDate($dependent['birth_date'])?>" >
									<label>DOB* (MM/DD/YYYY)</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6" >
						<div class="form-group">
							<div class="btn-group colors" data-toggle="buttons">
								<label class="btn btn-info <?=$dependent['gender'] == 'Male' ? 'active' : ''?>">
									<input type="radio" readonly autocomplete="off" class="js-switch"> Male
								</label>
								<label class="btn btn-info <?=$dependent['gender'] == 'Female' ? 'active' : ''?>">
									<input type="radio" readonly autocomplete="off" class="js-switch"> Female
								</label>
							</div>
						</div>
					</div>
				</div>
				<hr>
			<?php endforeach; ?>
		<?php } ?>
		<!-- <h4 class="mt0 mb30">Spouse</h4>
		<div class="row enrollment_auto_row theme-form">
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="dep_fname" id="dep_fname" value="<?=$resCustomerDep['fname']?>" class="form-control">
					<label>First Name*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="dep_lname" id="dep_lname" value="<?=$resCustomerDep['lname']?>" class="form-control">
					<label>Last Name*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="dep_ssn" id="dep_ssn" value="<?=$resCustomerDep['ssn']?>" class="form-control">
					<label>SSN*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="dep_email" id="dep_email" value="<?=$resCustomerDep['email']?>" class="form-control">
					<label>Email*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input  type="text" name="dep_dob" class="form-control" id="dep_dob" value="<?=$resCustomerDep['birth_date']?>" >
							<label>DOB* (MM/DD/YYYY)</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<div class="btn-group colors" data-toggle="buttons">
						<label class="btn btn-info">
							<input type="radio" autocomplete="off" class="js-switch"> Male
						</label>
						<label class="btn btn-info">
							<input type="radio" autocomplete="off" class="js-switch"> Female
						</label>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<h4 class="mt0 mb30">Child</h4>
		<div class="row enrollment_auto_row theme-form">
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="" class="form-control">
					<label>First Name*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="" class="form-control">
					<label>Last Name*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="" class="form-control">
					<label>SSN*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<input type="text" name="" class="form-control">
					<label>Email*</label>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input  type="text" class="form-control" >
							<label>DOB* (MM/DD/YYYY)</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6" >
				<div class="form-group">
					<div class="btn-group colors" data-toggle="buttons">
						<label class="btn btn-info">
							<input type="radio" autocomplete="off" class="js-switch"> Male
						</label>
						<label class="btn btn-info">
							<input type="radio" autocomplete="off" class="js-switch"> Female
						</label>
					</div>
				</div>
			</div>
		</div>
		<hr> -->
		<p class="fs32 fw300 mb30">Billing Information</p>
		<div class="payment_section mn pn">
			<div class="row">
				<div class="col-sm-4 col-lg-3">
					<h4 class="fs16 m-b-20">Payment Method</h4>
					<div class="blue_arrow_tab">
						<ul class="nav nav-tabs nav-noscroll">
							<li class="<?=$billing_data['payment_mode'] == 'CC' ? 'active' : '' ?>"><a href="#credit_card" class="tabs_collapse" data-toggle="tab">Credit Card</a></li>
							<li class="<?=$billing_data['payment_mode'] == 'ACH' ? 'active' : '' ?>"> <a href="#bank_draft" class="tabs_collapse" data-toggle="tab">ACH Bank Draft</a> </li>
						</ul>
					</div>
					<div class="tab-content left_form_tab">
						<div class="tab-pane fade in active" id="credit_card">
							<div class="row  theme-form">
								<div class="col-sm-12">
									<div class="form-group">
										<input type="text" class="form-control" name="name_on_card" placeholder="Name On Card" value="<?=checkIsset($billing_data['fname']).' '.checkIsset($billing_data['lname'])?>">
									</div>
								</div>
								<div class="col-sm-12">
								<div>
								<label class="text-white">Card Number <span id="cc_billing_detail"><?= !empty($billing_data['card_no']) ? "(".$billing_data['card_type']." *" . $billing_data['card_no'] . ")" : '' ?></span><span class="req-indicator">*</span> </label>
								</div>
									<div class="form-group">
										<input type="text" class=" form-control" name="card_number" placeholder="Card Number" maxlength="16" value="" oninput="isValidNumber(this)">
										<input type="hidden" name="full_card_number" value="<?=$billing_data['card_no_full']?>">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<select name="card_type" class=" form-control">
										<option value="Visa" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Visa' ? 'selected="selected"' : '' ?>> Visa</option>
										<option value="MasterCard" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'MasterCard' ? 'selected="selected"' : '' ?>> MasterCard </option>
										<option value="Discover" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Discover' ? 'selected="selected"' : '' ?>> Discover </option>
										<option value="Amex" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Amex' ? 'selected="selected"' : '' ?>> American Express </option>
										</select>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group height_auto m-b-15">
												<?php
												$date = '';
												if(!empty($billing_data['expiry_month']) && !empty($billing_data['expiry_year'])){
													$date = date('m/y',strtotime($billing_data['expiry_month']."/01/".$billing_data['expiry_year']));
												}
												
												?>
												<input type="text" name="expiration" id="expiration" value="<?=$date?>" class="form-control" placeholder="Expiration MM/YY*">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group height_auto m-b-15">
												<input type="text" name="cvv" id="cvv" class="form-control" placeholder="CVV*" oninput="isValidNumber(this)">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="bank_draft">
							<div class="theme-form">
								<div class="form-group">
									<input type="text" class="form-control" name="ach_name" value="<?=checkIsset($billing_data['bankname'])?>" placeholder="Name*">
								</div>
								<div class="form-group">
									<select class="form-control" name="account_type" title="Account Type">
										<option value="checking" <?= (!empty($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'checking') ? 'selected="selected"' : '' ?>>Checking</option>
										<option value="savings" <?= (!empty($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'savings') ? 'selected="selected"' : '' ?>>Saving</option>
									</select>
								</div>
								<div style="<?=!empty($billing_data['ach_routing_number']) ? '' : 'display:none'?>">
									<label class="text-white">Routing Number
									<?= !empty($billing_data['ach_routing_number']) ? "(***" . substr($billing_data['ach_routing_number'],-4) . ")" : '' ?>
									<span class="req-indicator">*</span></label>
									</div>
								<div class="form-group">
									<input type="text" class="form-control" value="" name="routing_number" placeholder="Routing Number*" maxlength='9' oninput="isValidNumber(this)">
									<input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?=checkIsset($billing_data['ach_routing_number'])?>" maxlength='50' class="required form-control tblur">
								</div>
								<div style="<?=!empty($billing_data['ach_account_number']) ? '' : 'display:none'?>">
								<label class="text-white">Account Number <span id="ach_billing_detail"><?= !empty($billing_data['ach_account_number']) ? "(ACH *" . substr($billing_data['ach_account_number'],-4) . ")" : '' ?></span><span class="req-indicator">*</span></label>
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="account_number" value="" placeholder="Account Number*" oninput="isValidNumber(this)" maxlength='17'>
									<input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=checkIsset($billing_data['ach_account_number'])?>" maxlength='50' class="form-control">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="confirm_account_number" placeholder="Confirm Account Number*" oninput="isValidNumber(this)" maxlength='17'>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-8 col-lg-8 col-lg-offset-1">
					<h4 class="fs16 m-b-20 ">Billing Address</h4>
					<div class="mb30">
						<label class="mn"><input type="checkbox" value="" id="same_as_personal" <?php echo $same_as_personal ? 'checked' : ''; ?>>Same as primary plan holder?</label>
					</div>
					<div class="row theme-form bill_info">
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="bill_fname" id="bill_fname" value="<?=checkIsset($billing_data['fname'])?>" class="form-control">
								<label>First Name*</label>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="bill_lname" id="bill_lname" value="<?=checkIsset($billing_data['lname'])?>" class="form-control">
								<label>Last Name*</label>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<input type="text" name="bill_address" id="bill_address" value="<?=checkIsset($billing_data['address'])?>" class="form-control">
								<label>Address</label>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="bill_city" id="bill_city" value="<?=checkIsset($billing_data['city'])?>" class="form-control">
								<label>City*</label>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<!-- <input type="text" name="" id="bill_state" class="form-control"> -->
								<?php ?>
									<select class="form-control" id="bill_state" name="bill_state">	
										<option value="" selected disable hidden></option>
										<?php if(!empty($allStateRes)){ 
											foreach($allStateRes as $state){
											?>
												<option value="<?=$state['name']?>" <?=checkIsset($billing_data['state']) == $state['name'] ? '' : '';?>><?=$state['name']?></option>
										<?php } } ?>
										
									</select>
								<?php ?>
								<label>State*</label>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="" id="bill_zip" class="form-control">
								<label>Zip Code*</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel-footer text-center">
		<a href="javascript:void(0);" class="btn btn-action">Save</a>
		<a href="javascript:void(0);" class="btn red-link">Cancel</a>
	</div>
</div>
<script type="text/javascript">
$(".ssn").mask("999-99-9999");
$("#bill_zip").mask("999999");
$('#expiration').datepicker({
	format: 'mm/yy',
	startView : 1,
	minViewMode: 1,
	autoclose: true,	
	startDate:new Date(),
	endDate : '+15y'
});

$("#cvv").mask("999");
$(document).off('click', '#same_as_personal');
$(document).on('click', '#same_as_personal', function () {
	$("#bill_zip").mask("999999");
	if ($(this).is(":checked")) {
		$("#bill_fname").val($("#primary_fname").val());
		$("#bill_lname").val($("#primary_lname").val());

		$("#bill_address").val($("#primary_address").val());
		$("#bill_city").val($("#primary_city").val());
		$("#bill_state").val($("#primary_state").val()).change();
		$("#bill_zip").val($("#primary_zipcode").val());
		$(".bill_info input").addClass('has-value');
	//$("#bill_state option[value="+$("#state").val()+"]").prop('selected', 'selected')
	} else {
		$(".bill_info input").removeClass('has-value');
		$("#bill_fname").val('');
		$("#bill_lname").val('');
		$("#bill_address").val('');
		$("#bill_city").val('');
		$("#bill_state").val('').change();
		$("#bill_zip").val('');
	}
});

</script>