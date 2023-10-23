<form  method="POST" id="manage_group_form" enctype="multipart/form-data"  autocomplete="off">
	<input type="hidden" name="save_type" id="save_type" value="">
	<div class="panel panel-default panel-block">
		<div class="panel-body theme-form">
			<div class="clearfix">
				<div class="pull-left">
					<h4 class="m-t-0 m-b-20">Pay Options</h4>
				</div>
				<!-- <div class="pull-right">
					<a href="javascript:void(0)" class="btn btn-action" id="add_pay_option_variation">+ Variation</a>
				</div> -->
			</div>
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
						<div class="m-b-20">
							<div class="m-b-10">
								<label class="mn"><input type="radio" name="cc_additional_charge" value="Y" <?= !empty($cc_additional_charge) && $cc_additional_charge =='Y' ? 'checked' : '' ?>> Yes</label>
							</div>
							<div class="m-b-0">
								<label class="mn"><input type="radio" name="cc_additional_charge" value="N" <?= !empty($cc_additional_charge) && $cc_additional_charge =='N' ? 'checked' : '' ?>> No</label>
							</div>
							<p class="error" id="error_cc_additional_charge"></p>
						</div>
						<div id="cc_additional_charge_div" style="<?= !empty($cc_additional_charge) && $cc_additional_charge =='Y' ? '' : 'display: none' ?>">
							<p class="m-b-15">Select Charge Type:</p>
							<div class="m-b-20">
								<div class="m-b-10">
									<label class="mn"><input type="radio" name="cc_charge_type" value="Fixed" <?= !empty($cc_charge_type) && $cc_charge_type =='Fixed' ? 'checked' : '' ?>> Fixed Amount</label>
								</div>
								<div class="m-b-0">
									<label class="mn"><input type="radio" name="cc_charge_type" value="Percentage" <?= !empty($cc_charge_type) && $cc_charge_type =='Percentage' ? 'checked' : '' ?>> Percentage</label>
								</div>
								<p class="error" id="error_cc_charge_type"></p>
							</div>
							<div class="input-group">
								<span class="input-group-addon" id="Fixed_div" style="<?= !empty($cc_charge_type) && $cc_charge_type =='Fixed' ? '' : 'display: none' ?>"><i class="fa fa-usd"></i></span>
								<div class="pr">
									<input type="text" id="cc_charge" name="cc_charge" class="form-control" value="<?= isset($cc_charge) ? $cc_charge : '' ?>" onkeypress="return isNumberOnly(event)" >
									<label>Credit Card Charge By Invoice</label>
								</div>
								<span class="input-group-addon" id="Percentage_div" style="<?= !empty($cc_charge_type) && $cc_charge_type =='Percentage' ? '' : 'display: none' ?>"><i class="fa fa-percent"></i></span>
							</div>
							<p class="error" id="error_cc_charge"></p>
						</div>
					</div>
				</div>
				<div id="Check_div" style="<?= !empty($is_check) && $is_check=='Y' ? '' : 'display: none' ?>">
					<div  class="col-sm-4" >
						<div class="pay_option_box">
							<h5 class="m-t-0">Check</h5>
							<p class="m-b-20">If paid by check should there be an additional charge for this payment method?</p>
							<div class="m-b-20">
								<div class="m-b-10">
									<label class="mn"><input type="radio" name="check_additional_charge" value="Y" <?= !empty($check_additional_charge) && $check_additional_charge == 'Y' ? 'checked' : '' ?>> Yes</label>
								</div>
								<div class="m-b-0">
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
					<div  class="col-sm-4" >
						<div class="pay_option_box">
							<h5 class="m-t-0">Check Remit to Address</h5>
							<div class="form-group height_auto">
								<textarea class="form-control" id="remit_to_address" name="remit_to_address" placeholder="Remit To Address" rows="4"><?= !empty($remit_to_address) ? $remit_to_address : '' ?></textarea>
								<p class="error" id="error_remit_to_address"></p>
							</div>
						</div>
					</div>
				</div>
				<div id="ACH_div" style="<?= !empty($is_ach) && $is_ach=='Y' ? '' : 'display: none' ?>">
					
				</div>
			</div>
			<hr>
			<div class="clearfix tbl_filter">
				<div class="pull-left"><h4 class="m-t-7">Pay Option Variations</h4></div>
				<div class="pull-right">
					<div class="m-b-15">
						<div class="note_search_wrap auto_size" id="pay_options_variations_search_div" style="display: none; max-width: 100%;">
							<div class="phone-control-wrap theme-form">
								<div class="phone-addon">
									<div class="form-group">
										<a href="javascript:void(0);" class="search_close_btn text-light-gray" data-close="pay_options_variations">X</a>
									</div>
								</div>
								<div class="phone-addon w-300">
									<div class="form-group">
										<input type="text"  class="form-control" id="input_pay_options_variations" value="" >
										<label>Keywords</label>
									</div>
								</div>
								<div class="phone-addon w-80">
									<div class="form-group">
										<a href="javascript:void(0);" class="btn btn-info search_button" data-search="input_pay_options_variations">Search</a>
									</div>
								</div>
							</div>
						</div>
						<a href="javascript:void(0);" class="search_btn" id="search_pay_options_variations" data-tab="pay_options_variations"><i class="fa fa-search fa-lg text-blue"></i></a>
						<a href="javascript:void(0)" class="btn btn-action" id="add_pay_option_variation">+ Variation</a>
					</div>
				</div>
			</div>
			<div id="pay_options_variations_div">
			</div>
			<div class="text-center m-t-10">
				<button type="button" class="btn btn-action save" data-type="pay_option">Save</button>
				<a href="javascript:void(0);" class="btn red-link cancel">Cancel</a>
			</div>
		</div>
	</div>

	<div class="panel panel-default panel-block">
		<div class="panel-body theme-form">
			<h4 class="m-t-0">COBRA Benefits</h4>
			<p class="m-b-15">Will groups use COBRA benefits?</p>
			<div class="m-b-20">
				<div class="m-b-10">
					<label class="mn"><input type="radio" name="group_use_cobra_benefit" value="Y" <?= !empty($group_use_cobra_benefit) && $group_use_cobra_benefit =='Y' ? 'checked' : '' ?>> Yes</label>
				</div>
				<div class="m-b-0">
					<label class="mn"><input type="radio" name="group_use_cobra_benefit" value="N" <?= !empty($group_use_cobra_benefit) && $group_use_cobra_benefit =='N' ? 'checked' : '' ?>> No</label>
				</div>
				<p class="error" id="error_group_use_cobra_benefit"></p>
			</div>
			<div id="group_use_cobra_benefit_div" style="<?= !empty($group_use_cobra_benefit) && $group_use_cobra_benefit =='Y' ? '' : 'display:none' ?>">
				<p class="m-b-15">Assign additional surcharge percentage of Full Premium?</p>
				<div class="m-b-20">
					<div class="m-b-10">
						<label class="mn"><input type="radio" name="is_additional_surcharge" value="Y" <?= !empty($is_additional_surcharge) && $is_additional_surcharge =='Y' ? 'checked' : '' ?>> Yes</label>
					</div>
					<div class="m-b-0">
						<label class="mn"><input type="radio" name="is_additional_surcharge" value="N" <?= !empty($is_additional_surcharge) && $is_additional_surcharge =='N' ? 'checked' : '' ?>> No</label>
					</div>
					<p class="error" id="error_is_additional_surcharge"></p>
				</div>
				<div class="row" id="is_additional_surcharge_div" style="<?= !empty($is_additional_surcharge) && $is_additional_surcharge =='Y' ? '' : 'display:none' ?>">
					<div class="col-sm-3">
						<div class="input-group">
							<div class="pr">
								<input type="text" class="form-control" name="additional_surcharge" id="additional_surcharge" value="<?= isset($additional_surcharge) ? $additional_surcharge : '' ?>" onkeypress="return isNumberOnly(event)">
								<label>Set Percentage</label>
								
							</div>
							<span class="input-group-addon"><i class="fa fa-percent"></i></span>
						</div>
						<p class="error" id="error_additional_surcharge"></p>
					</div>
				</div>
			</div>
			<div class="text-center">
				<button type="button" class="btn btn-action save" data-type="cobra_benefits">Save</button>
				<a href="javascript:void(0);" class="btn red-link cancel">Cancel</a>
			</div>
		</div>
	</div>
	<div class="panel panel-default panel-block">
		<div class="panel-body theme-form">
			<div class="clearfix">
				<div class="pull-left">
					<h4 class="m-t-0 m-b-20">Minimum Group Contribution</h4>
				</div>
				<!-- <div class="pull-right">
					<a href="javascript:void(0);" class="btn btn-action" id="add_group_contribution_variation">+ Variation</a>
				</div> -->
			</div>
			<p class="m-b-15">Is there a minimum group contribution required for any product(s)?</p>
			<div class="m-b-20">
				<div class="m-b-10">
					<label class="mn"><input type="radio" name="minimum_group_contribution" value="Y" <?= !empty($minimum_group_contribution) && $minimum_group_contribution =='Y' ? 'checked' : '' ?>> Yes</label>
				</div>
				<div class="m-b-0">
					<label class="mn"><input type="radio" name="minimum_group_contribution" value="N" <?= !empty($minimum_group_contribution) && $minimum_group_contribution =='N' ? 'checked' : '' ?>> No</label>
				</div>
				<p class="error" id="error_minimum_group_contribution"></p>
				<p class="error" id="error_general_minimum_group_contribution"></p>
			</div>

			<div id="minimum_group_contribution_div" style="<?= !empty($minimum_group_contribution) && $minimum_group_contribution =='Y' ? '' : 'display: none' ?>">
				<div id="minimum_group_contribution_main_div">
					<?php if(!empty($resGroupContributionSetting)){ ?>
						<?php foreach ($resGroupContributionSetting as $key => $value) { ?>
							<div id="minimum_group_contribution_inner_div_<?= $value['id'] ?>" class="minimum_group_contribution_inner_div" data-id="<?= $value['id'] ?>">
								<?php $contribution_products = !empty($value['products']) ? explode(",", $value['products']) : '' ?>
								<div class="row">
									<div class="col-lg-3 col-sm-6">
										<div class="form-group">
												<select class="se_multiple_select products added_products" id="products_<?= $value['id'] ?>" name="products[<?= $value['id'] ?>][]" multiple="multiple" data-id="<?= $value['id'] ?>">
													<?php foreach ($company_arr as $key => $company){
												        if($company){ ?>
												            <optgroup label="<?= $key ?>">
												                <?php foreach ($company as $pkey => $row) {
												                	$option_display = $row['name'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : ''); ?>
												                	<option value="<?= $row["id"] ?>" data-id="<?= $value['id'] ?>" <?= !empty($contribution_products) && in_array($row['id'], $contribution_products) ? 'selected' : '' ?> <?= !empty($contribution_products) && !in_array($row['id'], $contribution_products) && !empty($all_products) && in_array($row['id'], $all_products) ? 'disabled' : '' ?>> <?= $option_display ?></option>
												                <?php } ?>
												            </optgroup>
												        <?php }
												    } ?>
												</select>
												<label> Select Product(s)</label>
											<p class="error" id="error_products_<?= $value['id'] ?>"></p>
										</div>
									</div>
									<div class="col-lg-3 col-sm-6">
										<div class="form-group  text-center">
											<div class="m-t-5 hidden-xs"></div>
											<label class="radio-inline"><input type="radio" name="contribution_type[<?= $value['id'] ?>]" class="contribution_type" data-id="<?= $value['id'] ?>" value="Fixed" <?= !empty($value['contribution_type']) && $value['contribution_type'] == "Fixed" ? "checked" : "" ?>> Fixed Amount</label>
											<label class="radio-inline"><input type="radio" name="contribution_type[<?= $value['id'] ?>]" class="contribution_type" data-id="<?= $value['id'] ?>" value="Percentage" <?= !empty($value['contribution_type']) && $value['contribution_type'] == "Percentage" ? "checked" : "" ?>> Percentage</label>
											<p class="error" id="error_contribution_type_<?= $value['id'] ?>" ></p>
										</div>
									</div>
									<div class="visible-md visible-sm clearfix"></div>
									<div class="col-lg-3 col-sm-6">
										<div class="phone-control-wrap">
											<div class="phone-addon">
												<div class="form-group ">
													<div class="input-group w-100">
														<span class="input-group-addon" id="Fixed_div_<?= $value['id'] ?>" style="<?= !empty($value['contribution_type']) && $value['contribution_type'] == "Fixed" ? "" : "display: none" ?>"><i class="fa fa-usd"></i></span>
														<div class="pr">
															<input type="text" class="form-control" name="contribution[<?= $value['id'] ?>]" value="<?=  isset($value['contribution']) ? $value['contribution'] : '' ?>" onkeypress="return isNumberOnly(event)">
															<label>Set Contribution</label>
														</div>
														<span class="input-group-addon" id="Percentage_div_<?= $value['id'] ?>" style="<?= !empty($value['contribution_type']) && $value['contribution_type'] == "Percentage" ? "" : "display: none" ?>"><i class="fa fa-percent"></i></span>
													</div>
													<p class="error" id="error_contribution_<?= $value['id'] ?>"></p>
												</div>
											</div>
											<div class="phone-addon w-30 ">
												<div class="form-group">
													<span class="text-light-gray fw500"><a href="javascript:void(0);" id="remove_minimum_group_contribution_inner_div_<?= $value['id'] ?>" class="remove_minimum_group_contribution_inner_div" data-id="<?= $value['id'] ?>"> X </a></span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-3 col-sm-6" id="percentage_calculate_by_div_<?= $value['id'] ?>" style="<?= !empty($value['contribution_type']) && $value['contribution_type'] == "Percentage" ? "" : "display: none" ?>">
										<p>What will this percentage be calculated by?</p>
										<div class="m-b-20">
											<div class="m-b-10">
												<label class="mn label-input"><input type="radio" name="percentage_calculate_by[<?= $value['id'] ?>]" value="member_only_tier_apply_to_all" class="percentage_calculate_by" <?= !empty($value['percentage_calculate_by']) && $value['percentage_calculate_by'] == "member_only_tier_apply_to_all" ? "checked" : "" ?>> Member Only tier applied to all plan tiers</label>
											</div>
											<div class="m-b-0">
												<label class="mn"><input type="radio" name="percentage_calculate_by[<?= $value['id'] ?>]" value="each_benefit_tier" class="percentage_calculate_by" <?= !empty($value['percentage_calculate_by']) && $value['percentage_calculate_by'] == "each_benefit_tier" ? "checked" : "" ?>> Each plan tier</label>
											</div>
											<p class="error" id="error_percentage_calculate_by_<?= $value['id'] ?>"></p>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="clearfix p-b-20">
					<a href="javascript:void(0);" class="red-link" id="add_minimum_group_contribution">+ Min Group Contribution</a>
				</div>
			</div>
			<hr>
			<div class="clearfix tbl_filter">
				<div class="pull-left"><h4 class="m-t-7">Min. Contribution Variations</h4></div>
				<div class="pull-right">
					<div class="m-b-15">
						<div class="note_search_wrap auto_size" id="group_contribution_variations_search_div" style="display: none; max-width: 100%;">
							<div class="phone-control-wrap theme-form">
								<div class="phone-addon">
									<div class="form-group">
										<a href="javascript:void(0);" class="search_close_btn text-light-gray" data-close="group_contribution_variations">X</a>
									</div>
								</div>
								<div class="phone-addon w-300">
									<div class="form-group">
										<input type="text"  class="form-control" id="input_group_contribution_variations" value="" >
										<label>Keywords</label>
									</div>
								</div>
								<div class="phone-addon w-80">
									<div class="form-group">
										<a href="javascript:void(0);" class="btn btn-info search_button" data-search="input_group_contribution_variations">Search</a>
									</div>
								</div>
							</div>
						</div>
						<a href="javascript:void(0);" class="search_btn" id="search_group_contribution_variations" data-tab="group_contribution_variations"><i class="fa fa-search fa-lg text-blue"></i></a>
						<a href="javascript:void(0);" class="btn btn-action" id="add_group_contribution_variation">+ Variation</a>
					</div>
				</div>
			</div>
			<div id="group_contribution_variations_div">
			</div>
			<div class="text-center m-t-15">
				<button type="button" class="btn btn-action save" data-type="group_contribution">Save</button>
				<a href="javascript:void(0);" class="btn red-link cancel">Cancel</a>
			</div>
		</div>
	</div>

	<div class="panel panel-default panel-block">
		<div class="panel-body">
			<div class="clearfix">
				<div class="pull-left">
					<h4 class="">Termination Reasons</h4>
				</div>
				<div class="pull-right">
					<div class="m-b-15">
						<a href="javascript:void(0);" class="btn btn-action" id="termination_reasons_group">+ Reason</a>
					</div>
				</div>
			</div>
			<div id="group_termination_reason_div">
			</div>

			<div class="text-center">
				<button type="button" class="btn btn-action save" id="save" data-type="all_options">Save All</button>
				<a href="javascript:void(0);" class="btn red-link" id="cancel">Cancel</a>
			</div>
		</div>
	</div>
	
</form>
<div class="add_level_panelwrap">
	<div class="panel panel-default panel-block ">
  		<div class="panel-body">
			<div class="clearfix m-b-15">
		      	<div class="pull-left">
		        	<h4 class="fw600 m-t-0">Group Agreement</h4>
		        	<p class="mn">The terms an group will agree to upon setting up an account.</p>
		      	</div>
		      	<div class="pull-right"> 
		    		<a href="javascript:void(0);"  id="edit_terms" data-id="<?= !empty($group_terms_id) ? $group_terms_id : 0 ?>" data-type="Group" class="fa fa-edit fs18  edit_term"></a> 
	    		</div>
		    </div>
		    
		  	<textarea rows="13" class="summernote" id="group_terms" name="group_terms">
		        <?= !empty($group_terms) ? $group_terms : '' ?>
		  	</textarea>
		</div>
	</div>
</div>

<?php include ('group_enrollment_settings.inc.php'); ?>

<div id="minimum_group_contribution_clone_div" style="display: none;">
	<div id="minimum_group_contribution_inner_div_~number~" class="minimum_group_contribution_inner_div" data-id="~number~">
		<div class="row">
			<div class="col-lg-3 col-sm-6">
				<div class="form-group">
						<select class="se_multiple_select products products_dynamic" id="products_~number~" name="products[~number~][]" multiple="multiple" data-id="~number~">
							<?php foreach ($company_arr as $key => $company){
						        if($company){ ?>
						            <optgroup label="<?= $key ?>">
						                <?php foreach ($company as $pkey => $row) {
						                	$option_display = $row['name'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : ''); ?>
						                	<option value="<?= $row["id"] ?>" data-id="~number~" <?= !empty($all_products) && in_array($row['id'], $all_products) ? 'disabled' : '' ?>> <?= $option_display ?></option>
						                <?php } ?>
						            </optgroup>
						        <?php }
						    } ?>
						</select>
						<label> Select Product(s)</label>
					<p class="error" id="error_products_~number~"></p>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6">
				<div class="form-group text-center">
					<div class="m-t-5 hidden-xs"></div>
					<label class="radio-inline"><input type="radio" name="contribution_type[~number~]" class="contribution_type" data-id="~number~" value="Fixed"> Fixed Amount</label>
					<label class="radio-inline"><input type="radio" name="contribution_type[~number~]" class="contribution_type" data-id="~number~" value="Percentage"> Percentage</label>
					<p class="error" id="error_contribution_type_~number~"></p>
				</div>
			</div>
			<div class="visible-md visible-sm clearfix"></div>
			<div class="col-lg-3 col-sm-6">
				<div class="phone-control-wrap">
					<div class="phone-addon">
						<div class="form-group">
							<div class="input-group w-100">
								<span class="input-group-addon" id="Fixed_div_~number~" style="display: none"><i class="fa fa-usd"></i></span>
								<div class="pr">
									<input type="text" class="form-control" name="contribution[~number~]" onkeypress="return isNumberOnly(event)"> 
									<label>Set Contribution</label>
								</div>
								<span class="input-group-addon" id="Percentage_div_~number~" style="display: none"><i class="fa fa-percent"></i></span>
							</div>
							<p class="error" id="error_contribution_~number~"></p>
						</div>
					</div>
					<div class="phone-addon w-30 ">
						<div class="form-group ">
							<span class="text-light-gray fw500"><a href="javascript:void(0);" id="remove_minimum_group_contribution_inner_div_~number~" class="remove_minimum_group_contribution_inner_div" data-id="~number~"> X </a></span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6" id="percentage_calculate_by_div_~number~" style="display: none">
				<p>What will this percentage be calculated by?</p>
				<div class="m-b-20">
					<div class="m-b-10">
						<label class="mn label-input"><input type="radio" name="percentage_calculate_by[~number~]" value="member_only_tier_apply_to_all" class="percentage_calculate_by"> Member Only tier applied to all plan tiers</label>
					</div>
					<div class="m-b-0">
						<label class="mn"><input type="radio" name="percentage_calculate_by[~number~]" value="each_benefit_tier" class="percentage_calculate_by"> Each plan tier</label>
					</div>
					<p class="error" id="error_percentage_calculate_by_~number~"></p>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var $contribution_count = 0;
	$(document).ready(function(){
		dropdown_pagination('pay_options_variations_div','group_contribution_variations_div','group_termination_reason_div')

		initCKEditor("group_terms",true);
		//******************** Pay Options Code Start **********************
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
		  pay_options_variations();
		//******************** Pay Options Code End   **********************
	  	
	  	//******************** Contribution Code Start **********************
	  	
			$is_added_products = '<?= $is_added_products ?>';
			
			if($is_added_products=="true"){
				$(".added_products").multipleSelect({
					selectAll: false,
					onClick:function(e){
						
						$id = e.data.id;
						$text = e.text;
						$productName = e.value;
						if(e.selected){
						$(".products_dynamic [value='"+$productName+"']").prop('disabled',true);
						}else{
							$(".products_dynamic [value='"+$productName+"']").prop('disabled',false);
						}
						$("select.products").each(function(){
						    $subID = $(this).attr('data-id');
						    if($id != $subID){
						        if(e.selected){
						            $("#products_"+$subID+" [value='"+$productName+"']").prop('disabled',true);
						        }else{
						            $("#products_"+$subID+" [value='"+$productName+"']").prop('disabled',false);
						        }  
						        $("#products_"+$subID).multipleSelect('refresh');
						    }
						});
					},
					onOptgroupClick:function(e){
						$childRecords=e.children;
						$id = e.data.id;
						$.each($childRecords,function($k,$v){
							$productName = $v.value;
							if(!$v.disabled){
								if(e.selected){
									$(".products [value='"+$productName+"']").prop('disabled',true);
									$("#products_"+$id+" [value='"+$productName+"']").prop('disabled',false);
								}else{
									$(".products [value='"+$productName+"']").prop('disabled',false);
								}
							}
						});
						$("#manage_group_form .products").multipleSelect('refresh');
						
					},
					onTagRemove:function(e){
						$productName = e.value;
						$(".products [value='"+$productName+"']").prop('disabled',false);
						
						$("#manage_group_form .products").multipleSelect('refresh');
						
					}
			  	});
			  	$("#manage_group_form .contribution_type").not('.js-switch').uniform();
			  	$("#manage_group_form .percentage_calculate_by").not('.js-switch').uniform();
			}

			group_contribution_variations();
		//******************** Contribution Code End   **********************
		  

		//******************** Termination Reason Code Start **********************
			group_termination_reason();
  		//******************** Termination Reason Code End   **********************

  		//******************** Termination Reason Code Start **********************
  			// CKEDITOR.instances['group_terms'].setReadOnly(false);
  		//******************** Termination Reason Code End   **********************
  		
  		//************* Variation Cart Setting Code Start *************//
  			getVariationCartSettings();
  		//************* Variation Cart Setting Code End****************//
	});
	//******************** General Search Code Start  **********************
		$(document).off("click", ".search_btn");
		$(document).on("click", ".search_btn", function(e) {
			e.preventDefault();
			var tabs = $(this).attr('data-tab');
			$(this).hide();
			$("#" + tabs + "_search_div").css('display', 'inline-block');
			$("#" + tabs + "_search_div").show();
		});

		$(document).off("click", ".search_close_btn");
		$(document).on("click", ".search_close_btn", function(e) {
			e.preventDefault();
			var tabs = $(this).attr('data-close');
			$("#" + tabs + "_search_div").hide();
			$("#search_" + tabs).show();
			$('#' + tabs).val('');

			if (tabs == 'pay_options_variations') {
			  pay_options_variations(search_val = '');
			}else if (tabs == 'group_contribution_variations') {
				group_contribution_variations(search_val = '');
			}
		});

		$(document).off("click", ".search_button");
		$(document).on("click", ".search_button", function(e) {
		    e.preventDefault();
		    var search = $(this).attr('data-search');
		    var search_val = $('#' + search).val();

		    if (search == 'input_pay_options_variations') {
		      pay_options_variations(search_val);
		    } else if(search == 'input_group_contribution_variations'){
				group_contribution_variations(search_val);
			}
		});
	//******************** General Search Code End    **********************
	
	//******************** Pay Options Code Start **********************
		$(document).on("click", "#add_pay_option_variation", function(e) {
		    e.preventDefault();
		    $.colorbox({
		    	href:'add_pay_option_variation.php',
			  	iframe:true,
			  	width:"1085px;",
			  	height:"670px;",
			  	onClosed : function(){
			  		pay_options_variations();
			  	}
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

		pay_options_variations = function(search_val) {
		    $('#ajax_loader').show();
		    $('#pay_options_variations_div').hide();
		    $.ajax({
		      url: 'pay_options_variations.php',
		      type: 'GET',
		      data: {
		        is_ajaxed: 1,
		        search_val: search_val
		      },
		      success: function(res) {
		        $('#ajax_loader').hide();
		        $('#pay_options_variations_div').html(res).show();
				common_select();
				$('[data-toggle="tooltip"]').tooltip();
		      }
		    });
	  	}
  	//******************** Pay Options Code end   **********************

  	//******************** Cobra Benefit Code start **********************
		$(document).on("change","input[name=group_use_cobra_benefit]",function(){
			$val=$(this).val();
			$("#group_use_cobra_benefit_div").hide();
			if($val=="Y"){
				$("#group_use_cobra_benefit_div").show();
			}
		});

		$(document).on("change","input[name=is_additional_surcharge]",function(){
			$val=$(this).val();
			$("#is_additional_surcharge_div").hide();
			if($val=="Y"){
				$("#is_additional_surcharge_div").show();
			}
		});
	//******************** Cobra Benefit Code end   **********************
  	
  	//******************** Contribution Code Start **********************
  		
  		$(document).on("click", "#add_group_contribution_variation", function(e) {
		    e.preventDefault();
		    $.colorbox({
			  	href:'add_group_contribution_variation.php',
			  	iframe:true,
			  	width:"1085px;",
			  	height:"500px;",
			  	onClosed : function(){
			  		group_contribution_variations();
			  	}
		  	});
		});

		$(document).on("change","input[name=minimum_group_contribution]",function(){
			$val=$(this).val();
			$("#minimum_group_contribution_div").hide();
			if($val=="Y"){
				$("#minimum_group_contribution_div").show();
				add_minimum_group_contribution();
			}else{
				$("#minimum_group_contribution_main_div").html('');
			}
		});

		$(document).on("click", "#add_minimum_group_contribution", function() {
			add_minimum_group_contribution();
		});

		$(document).on("click", ".remove_minimum_group_contribution_inner_div", function() {
			$id = $(this).attr('data-id');
			if($id <= 0){
				$("#minimum_group_contribution_inner_div_"+$id).remove();
			}else{
				swal({
		            text: "Delete Contribution: Are you sure?",
		            showCancelButton: true,
		            confirmButtonText: "Confirm",
		        }).then(function() {
		           $("#ajax_loader").show();
		            $.ajax({
		                url:'ajax_delete_contribution.php',
		                dataType:'JSON',
		                type:'POST',
		                data:{id:$id},
		                success:function(res){
		                    if(res.status='success'){
		                        setNotifySuccess("Contribution Deleted Successfully");
		                        $("#minimum_group_contribution_inner_div_"+$id).remove();
		                    }
		                    $("#ajax_loader").hide();
		                }
		            });
		        }, function (dismiss) {
		        }); 
			}
		});

		$(document).on("click",".contribution_type",function(){
			$val=$(this).val();
			$id = $(this).attr('data-id');
			$("#Fixed_div_"+$id).hide();
			$("#Percentage_div_"+$id).hide();
			$("#percentage_calculate_by_div_"+$id).hide();
			if($val=="Fixed"){
				$("#Fixed_div_"+$id).show();
			}else if($val=="Percentage"){
				$("#Percentage_div_"+$id).show();
				$("#percentage_calculate_by_div_"+$id).show();
			}
		});

		add_minimum_group_contribution = function(){
			$contribution_count=$contribution_count+1;
			$number = "-"+$contribution_count;
			html = $('#minimum_group_contribution_clone_div').html();
			html = html.replace(/~number~/g,$number)
            $('#minimum_group_contribution_main_div').append(html);
            $("#products_"+$number).removeClass('products_dynamic');

			$("#products_"+$number).multipleSelect({
				selectAll: false,
				onClick:function(e){
					
					$id = e.data.id;
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$(".products_dynamic [value='"+$productName+"']").prop('disabled',true);
					}else{
						$(".products_dynamic [value='"+$productName+"']").prop('disabled',false);
					}
					$("select.products").each(function(){
					    $subID = $(this).attr('data-id');
					    if($id != $subID){
					        if(e.selected){
					            $("#products_"+$subID+" [value='"+$productName+"']").prop('disabled',true);
					        }else{
					            $("#products_"+$subID+" [value='"+$productName+"']").prop('disabled',false);
					        }  
					        $("#products_"+$subID).multipleSelect('refresh');
					    }
					});
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;
					$id = e.data.id;
					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$(".products [value='"+$productName+"']").prop('disabled',true);
								$("#products_"+$id+" [value='"+$productName+"']").prop('disabled',false);
							}else{
								$(".products [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#manage_group_form .products").multipleSelect('refresh');
					
				},
				onTagRemove:function(e){
					$productName = e.value;
					$(".products [value='"+$productName+"']").prop('disabled',false);
					
					$("#manage_group_form .products").multipleSelect('refresh');
					
				}
		  	});
		  	$("#manage_group_form .contribution_type").not('.js-switch').uniform();
		  	$("#manage_group_form .percentage_calculate_by").not('.js-switch').uniform();
		}

		group_contribution_variations = function(search_val) {
		    $('#ajax_loader').show();
		    $('#group_contribution_variations_div').hide();
		    $.ajax({
		      url: 'group_contribution_variations.php',
		      type: 'GET',
		      data: {
		        is_ajaxed: 1,
		        search_val: search_val
		      },
		      success: function(res) {
		        $('#ajax_loader').hide();
		        $('#group_contribution_variations_div').html(res).show();
				common_select();
				$('[data-toggle="tooltip"]').tooltip();
		      }
		    });
	  	}
  	//******************** Contribution Code End   **********************

  	//******************** Termination Reason Code Start **********************
  		$(document).on("click", "#termination_reasons_group", function(e) {
		    e.preventDefault();
		    $.colorbox({
			  	href:'termination_reasons_group.php',
			  	iframe:true,
			  	width:"515px;",
			  	height:"325px;",
			  	onClosed : function(){
					$.ajax({
						url: 'ajax_update_cache.php',
						type: 'POST',
						dataType: 'JSON',
	                });   
			  		group_termination_reason();
			  	}
		  	});
		});
  		group_termination_reason = function(search_val) {
		    $('#ajax_loader').show();
		    $('#group_termination_reason_div').hide();
		    $.ajax({
		      url: 'termination_reason_list.php',
		      type: 'GET',
		      data: {
		        is_ajaxed: 1,
		        search_val: search_val
		      },
		      success: function(res) {
		        $('#ajax_loader').hide();
		        $('#group_termination_reason_div').html(res).show();
				common_select();
				$('[data-toggle="tooltip"]').tooltip();
		      }
		    });
	  	}
  	//******************** Termination Reason Code End   **********************
  	

  	//******************** Agreement Code Start **********************
  		$(document).off('click', '#edit_terms');
	 	$(document).on('click', '#edit_terms', function(e) {
		    if ($(this).hasClass('edit_term')) {
		      CKEDITOR.instances['group_terms'].setReadOnly(false);
		      $("#edit_terms").removeClass('edit_term fa fa-edit fs18');
		      $("#edit_terms").addClass('btn btn-info save_term').text('Save');
		    } else { 
		      $("#edit_terms").removeClass('btn btn-info save_term').text('');;
		      $("#edit_terms").addClass('fa fa-edit fs18 edit_term');
		      $('#ajax_loader').show();
		      var id = $(this).data('id');
		      var type = $(this).data('type');
		      var terms = CKEDITOR.instances.group_terms.getData();
		      $.ajax({
		        url: 'ajax_update_terms.php',
		        data: {
		          id: id,
		          type: type,
		          terms: terms
		        },
		        type: 'POST',
		        success: function(res) {
		          $('#ajax_loader').hide();
		          if(res.status='success'){
		            setNotifySuccess(res.msg);
		            CKEDITOR.instances['group_terms'].setReadOnly(true);
		          }else{
		            setNotifyError(res.msg);
		          }
		        }
		      });
		    }
	  	});
  	//******************** Agreement Code End   **********************
  	
  	
  	
  	//******************** Button Code Start **********************
	  	$(document).off("click",".save");
  		$(document).on("click",".save",function(e){
			e.preventDefault();
			disableButton($(this));
			var $saveType = $(this).attr('data-type');
			$("#save_type").val($saveType);
  			$("#ajax_loader").show();
  			$(".error").html("");
  			$("#group_terms").val(CKEDITOR.instances.group_terms.getData());
  			$.ajax({
  				url:'ajax_manage_groups.php',
  				dataType:'JSON',
  				data:$("#manage_group_form").serialize(),
  				type:"POST",
  				success:function(res){
					enableButton($(".save"));
					$("#save_type").val('');
  					$("#ajax_loader").hide();
  					if(res.status=="success"){
						if(res.save_type == 'all_options' || res.save_type == 'group_contribution'){
							window.location.reload();
						}else{
							setNotifySuccess(res.msg);
						}
  					}else{
  						var is_error = true;
	                  
	                  	$.each(res.errors, function (index, value) {
	                      $('#error_' + index).html(value).show();
	                      if(is_error){
	                          var offset = $('#error_' + index).offset();
	                          var offsetTop = offset.top;
	                          var totalScroll = offsetTop - 150;
	                          $('body,html').animate({scrollTop: totalScroll}, 1200);
	                          is_error = false;
	                      }
	                  	});
  					}
  				}
  			});
  		});
		$(document).off("click",".cancel");
		$(document).on("click",".cancel",function(e){
			e.preventDefault();
			window.location.reload();
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

    function getVariationCartSettings(cart_option = "",join_range = "",added_date = "",fromdate = "",todate="") {
    	$('#ajax_loader').show();
    	var is_ajaxed = $('#is_ajaxed').val();
    	var perPages = $("#perPages").val();
      	$.ajax({
         	url: 'manage_groups.php',
         	type: 'POST',
         	data: { 
         		api_key : 'variationCartSettings',
         		cartType : cart_option,
         		joinRange : join_range,
         		addedDate : added_date,
         		fromDate : fromdate,
         		toDate : todate,
         		is_ajaxed : is_ajaxed,
         		perPages : perPages
         		 },
         	success: function(res) {
         		$('#ajax_loader').hide();
         		$('#ajax_data_variation').html(res).show();
         		common_select();
         	}
      	});
   	}
   
	$(document).off('click','#deleteCart');
  	$(document).on('click','#deleteCart',function(e){
  		e.stopPropagation();
  		var id = $(this).data('id');
  		swal({
	       text: 'Delete Record: Are you sure?',
	       showCancelButton: true,
	       confirmButtonText: 'Confirm',
	    }).then(function() {
	    	$("#ajax_loader").show();
	  		$.ajax({
	         	url :'<?=$HOST?>/ajax_api_call.php' ,
	         	type : 'POST',
	         	data : {
	         		api_key : 'variationDetete',
	         		id : id
	         	},
	         	dataType : 'json',
	         	success: function(res){
	         		$("#ajax_loader").hide();
	         		if(res.status=='Success'){
	         			setNotifySuccess(res.message);
	         			getVariationCartSettings();
	         		}else{
	         			setNotifyError(res.message);
	         			getVariationCartSettings();
	         		}
	         	}
	  		});
	  	}, function(dismiss){

	  	});		
  	});

</script>
