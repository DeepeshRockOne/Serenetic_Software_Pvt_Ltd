<div id="enrollment_verification_age">
<div class="bg_white">
	<div class="section_wrap">
		<div class="container">
			<div class="text-center">
				<p class="fs32 fw300 mb20"><strong>Hello</strong> <?=$customer_res['fname'].' '.$customer_res['lname']?>,</p>
				<p class="fs16 m-b-30">Verify your DOB below to complete application.</p>
			</div>
				<div class="row theme-form">
					<div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2 text-center">
					<div class="form-group">
						<div class="phone-control-wrap">
						<div class="phone-addon">
							<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<div class="pr">
							<input type="text" class="form-control" id="dob">
							<label class="label-wrap">DOB (MM/DD/YYYY)</label>
							</div>
							</div>
							<p class="error text-left" id="error_dob"></p>
						</div>
						<div class="phone-addon w-70 v-align-top">
							<!-- <a href="enrollment_verification.php" class="btn btn-action btn-block">Submit</a> -->
							<button type="button" id="submit_date" class="btn btn-action btn-block">Submit</button>
						</div>
						</div>
					</div>
					</div>
				</div>
		</div>
	</div>
	<div class="verification_banner">
		
	</div>
	<div class="smarte_footer mn">
		<div class="container m-b-15" >
			<div class="row footer_help">
				<div class="col-xs-7">
					<div style="<?=$sponsor_detail['display_in_member'] == 'N' ? '' : 'display:none'?>">
					<h4 class="text-action m-t-0">NEED HELP?</h4>
					<p class="mn need_help"><span><?=$sponsor_detail['name']?></span> <span>  <?=format_telephone($sponsor_detail['cell_phone'])?> </span> <span> <?=$sponsor_detail['email']?> </span></p>
					</div>
				</div>
				<div class="col-xs-5 mn text-right">
					<div class="powered_by_logo">
					<?php if($sponsor_detail['is_branding'] == 'Y' && file_exists($AGENTS_BRAND_ICON . $sponsor_detail['brand_icon'])) { ?>
					<img src="<?=$AGENTS_BRAND_ICON_WEB . $sponsor_detail['brand_icon']?>" height="43px" />
					<?php } else { ?>
					<img src="<?php echo $POWERED_BY_LOGO; ?>" height="43px" />
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom_footer ">
			<div class="container">
				<ul>
					<li><?= $DEFAULT_SITE_NAME ?>  &copy; <?php echo date('Y')?> </li>
				</ul>
			</div>
		</div>
	</div>
	</div>
</div>
<div class="bg_white">
	<div id="enrollment_verification_dashbpard" style="display:none">
		<div class="verification_header">
		</div>
		<div class="verification_sub_header">
			<div class="container">
				<div class="row">
					<div class="col-xs-7">
						<h4 class="mn text-action fs20"><?=$customer_res['fname'].' '.$customer_res['lname']?></h4>
					</div>
					<div class="col-xs-5 text-right">
						<a href="#assistance_popup" class="text-action fs16 fw500 assistance_popup"><i class="material-icons"> info </i> &nbsp;&nbsp;Assistance?</a>
					</div>
				</div>
			</div>
		</div>
		<div class="section_wrap">
			<div class="container text-center">
				<p class="fs32 fw300 mb20">Application Details</p>
				<p class="fs16 mn">Please review the information below and use the edit link provided to make any necessary changes or corrections. For further assistance, please contact your healthcare agent.</p>
			</div>
		</div>
		<div class="section_wrap enrollment_receipt">
			<div class="container">
				<div class="transaction_receipt">
					<div class="row bg_white">
						<div class="col-sm-3 receipt_left">
							<div class="bg_dark_primary">
								<div class="panel-body">
									<h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Primary Details</h4>
									<p class="text-white mn"><span class="fw700  fs18" id="primary_member_details"><?=$customer_res['fname'].' '.$customer_res['lname']?></span><br><?=$lead_display_id?><br><?=format_telephone($customer_res['cell_phone'])?><br> <?=$customer_res['email']?></p>
								</div>
							</div>
								<div class="panel-body">
									<?php if(!empty($resCustomerDep)){ ?>
									<h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">DEPENDENTS</h4>
									<p class="text-white mn" id="dependent_details">
									<?php $count_child = 0; foreach($resCustomerDep as $dep){ ?>
										<?php /*if(in_array(getRevRelation($dep['relation']),array('Child'))) {
											$count_child++;
											} else { */ ?>
											<?=getRevRelation($dep['relation'],$dep['gender']).' - '.ucfirst($dep['fname']).' '.ucfirst($dep['lname'])?><br />
										<?php } ?>
									<!-- <p class="text-white mn">Spouse - John Murray<br>Child - David Murray<br>Child - David Murray</p> -->
										<?php /*}	if($count_child !=0) echo "Child (".$count_child.")";*/ } ?>
									</p>
								</div>
							<?php if(!in_array($group_billing_method,array('list_bill','TPA'))){ ?>
									<div class="panel-body">
										<h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Billing</h4>
										<!-- <p class="text-white mn">Brent Murray<br>VISA *5555</p> -->
										<?php if(isset($billing_data['payment_mode']) && $billing_data['payment_mode'] == 'CC'){ ?>
										<p class="text-white mn billing_details"><?=$billing_data['fname'].' '.$billing_data['lname'] ?><br><?=$billing_data['card_type']?> *<?=$billing_data['card_no']?></p>
										<?php } ?>
										<?php if(isset($billing_data['payment_mode']) && $billing_data['payment_mode'] == 'ACH'){ ?>
										<p class="text-white mn billing_details"><?=$billing_data['fname'].' '.$billing_data['lname'] ?><br>ACH *<?=checkIsset($billing_data['last_cc_ach_no']) !='' ? $billing_data['last_cc_ach_no'] : $billing_data['last_cc_ach_no']?></p>
										<?php } ?>
									</div>
								<div id="post_payment_date" style="<?=strtotime(date("m/d/Y",strtotime('+1 day'))) < strtotime($coverge_effective_date) && !empty($post_date) ? '' : 'display:none'?>">
									<div class="panel-body">
										<h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Post Payment Date </h4>
										<p class="text-white mn" id="post_payment_details"><?=$post_date?></p>
									</div>
								</div>
								<div class="billing_info_edit">
									<div class="panel-body">
										<a href="javascript:void(0)" class="enrollment_verification_edit btn btn-white-o">Edit</a>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="col-sm-9 receipt_right">
							<div class="p-10">
								<div class="clearfix m-b-15 p-t-25 fw600">
									<div class="pull-left fs18 ">
										Summary
									</div>
									<div class="pull-right fs14" id="order_date_text">
										<?php echo getCustomDate($created_at) ?>
									</div>
									<input type="hidden" name="order_date" id="order_date" value="<?=$created_at?>">
								</div>
								<div class="table-responsive">
									<table class="<?=$table_class?>">
										<thead>
											<tr>
												<th>Product</th>
												<th>Plan Period</th>
												<th>Plan</th>
												<th class="text-right">Total</th>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($products)) :  ?>
												<?php foreach($products as $product) :  ?>

												<?php 

													$member_payment_type = '';
													$member_payment_type = checkIsset($product['payment_type_subscription']);
													if(strtotime(date('m/d/Y')) >= strtotime($product['start_coverage_period'])){
														$start_coverage_date = checkIsset($coverage_dates[$product['product_id']]);
														if($product['type']=='Fees'){ 
															$start_coverage_date = checkIsset($coverage_dates[$product['fee_applied_for_product']]);
														}
														$product_dates=$enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);
														$startCoveragePeriod = date('m/d/Y',strtotime($product_dates['startCoveragePeriod']));
													}else{
														$start_coverage_date = $product['start_coverage_period'];
														$product_dates=$enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);
														$startCoveragePeriod = date('m/d/Y',strtotime($product_dates['startCoveragePeriod']));
													}
													$product_name = $product['name'];
													

													

													$startCoveragePeriods[$product['product_id']] = $startCoveragePeriod;
													$endCoveragePeriod = date('m/d/Y',strtotime($product_dates['endCoveragePeriod']));
													$endCoveragePeriodArr[] = date('m/d/Y',strtotime($product_dates['endCoveragePeriod']));
												?>
													<tr>
														<td><?=$product_name?></td>
														<td><?=$startCoveragePeriod?> - <?=$endCoveragePeriod?></td>
														<td><?=isset($prdPlanTypeArray[$product['prd_plan_type_id']]['title'])?$prdPlanTypeArray[$product['prd_plan_type_id']]['title']:'-'?></td>
														<td class="text-right"><?=displayAmount($product['unit_price'])?></td>
													</tr>
												<?php endforeach; 
												$next_billing_date = min(array_map(function($item) { return $item; }, array_values($endCoveragePeriodArr)));
												?>
											<?php endif; ?>
										</tbody>
									</table>
									</div>
									<div class="pull-right receipt_total">
									<table class="table table-borderless receipt_table" >
										<tbody>
											<tr>
												<td>SubTotal</td>
												<td class="text-right"><?=displayAmount($sub_total)?></td>
											</tr>
											<?php if(!empty($healhty_step['id'])){?>
											<tr>
												<td><?=$healhty_step['name']?></td>
												<td class="text-right"><?=displayAmount($healhty_step['unit_price'])?></td>
											</tr>
											<?php } ?>
											<tr>
												<td>Service Fee</td>
												<td class="text-right"><?=displayAmount($service_fee)?></td>
											</tr>
											<tr>
												<td>Admin Fee(s)</td>
												<td class="text-right"><?=displayAmount($admin_fee)?></td>
											</tr>
											<tr>
												<td class="fw500">Total</td>
												<td class="text-right fw500"><?=displayAmount($grand_total)?></td>
											</tr>
										</tbody>
									</table>
									<div class="clearfix"></div>
									<div class="panel panel-default panel-black ">
										<div class="panel-heading">
											<table  class="table table-borderless text-white fs12 fw300" width="100%">
												<tbody>
													<tr>
														<td class="fw500">Monthly Payment</td>
														<td class="text-right"><?=displayAmount($monthly_payment)?></td>
													</tr>
													<?php if($enrollmentLocation!='groupSide'){ ?>
														<tr>
															<td class="fw500">Next Billing Date</td>
															<td class="text-right"><?php echo date('m/d/Y',strtotime($next_billing_date.' -1 day')) ?></td>
														</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="m-t-30 m-b-30">
			<div class="container" id="varification_page">
				<form action="" id="enrollment_verification_edit_form">
					<input type="hidden" name="group_billing_method" value="<?=$group_billing_method?>">
					<input type="hidden" name="enrollmentLocation" value="<?=$enrollmentLocation?>">
					<input type="hidden" name="customer_id" value="<?=$customer_res['id']?>">
					<input type="hidden" name="order_id" value="<?=$order_id?>">
					<input type="hidden" name="lead_id" value="<?=$lead_id?>">
					<input type="hidden" name="lead_quote_id" value="<?=checkIsset($lead_quote_details_res["id"])?>">
					<?php if(!empty($contingent_beneficiary_field)) { ?>
					<input type="hidden" name="is_contingent_beneficiary" id="is_contingent_beneficiary" value="">
					<?php } ?>
					<?php if(!empty($principal_beneficiary_field)) { ?>
					<input type="hidden" name="is_principal_beneficiary" id="is_principal_beneficiary" value="">
					<?php } if(count($product_list) > 0) { ?>
						<?php foreach ($product_list as $key => $product) { ?>
							<input type="hidden" name="quote_products[]" value='<?= $product ?>'>
							<input type="hidden" name="sel_plans[<?= $product ?>]" value="<?= $plan_list[$product] ?>">
						<?php } ?>
					<?php } ?>
					<?php if(isset($primary_annual_salary)){
				      if(!empty($primary_annual_salary)){
				        foreach ($primary_annual_salary as $key => $value) { ?>
				          <input type="hidden" name="primary_annual_salary[<?=$key?>]" value="<?=$value?>">
				      <?php }
				      }
				    } ?>
				    <?php if(isset($primary_monthly_salary_percentage)){
				      if(!empty($primary_monthly_salary_percentage)){
				        foreach ($primary_monthly_salary_percentage as $key => $value) { ?>
				          <input type="hidden" name="primary_monthly_salary_percentage[<?=$key?>]" value="<?=$value?>">
				      <?php }
				      }
				    } ?>
					<input type="hidden" name="coverage_date" id="coverage_date" value="<?=$coverge_effective_date?>" class="required form-control tblur coverage_date_input" required>
					<input type="hidden" name="child_product_list" value="<?=$child_products ?>">
					<input type="hidden" name="spouse_product_list" value="<?=$spouse_products ?>">
					<input type="hidden" name="admin_id" value="<?=$admin_id?>">
					<?php foreach ($eligibility_dates as $key => $eligibility_date) { ?>
						<input type="hidden" name="coverage_dates[<?=$key?>]" value="<?=checkIsset($startCoveragePeriods[$key])!='' ? $startCoveragePeriods[$key] : getCustomDate($eligibility_date) ?>" class="required form-control tblur" required>
					<?php } ?>
					<div id="edit_information_popup" style="display:none">
						<div class="panel panel-default panel-block panel-shadowless mn" >
							<div class="panel-heading">
								<h4 class="mn">Edit Information</h4>
							</div>
							<div class="panel-body">
							<p class="fs32 fw300 m-b-30">Primary Details</p>
							<div class="row enrollment_auto_row theme-form">
							<?php 
							$benefit_amount_arr = array('benefit_amount','in_patient_benefit','out_patient_benefit','monthly_income','benefit_percentage');
							if(!empty($primary_product_question)) { ?>
								<?php foreach($primary_product_question as $question){	
									$control_name = $question['label'];
									$control_type = $question['control_type'];
									$label = $question['display_label'];
									$class = $question['control_class'];
									$maxlength = $question['control_maxlength'];
									$control_attribute = $question['control_attribute'];
									$is_member = $question['is_member'];

									if($question['questionType']!="Default" && !in_array($question['id'],$customQuestionArray)){
										continue;
									}

									if(!in_array($control_name,$benefit_amount_arr))
										$control_value = ${'primary_'.$control_name.'_value'};

									if($question['questionType']=="Default"){
											if(checkIsset($question['asked']) == 'Y'){ 
												if($control_type == 'text' || $control_name == 'benefit_percentage') { ?>
														<?php if(in_array($control_name,$benefit_amount_arr)){
															if(!empty($customer_benefit_amount)){
																foreach($customer_benefit_amount as $bamount){ 
																	if(empty($bamount[$control_name]) || $bamount[$control_name] == 0 || $bamount[$control_name] == '0.00'){
																		continue;
																	}
																?>
															<div class="col-lg-3 col-md-6" >
																<div class="form-group">
																	<input 	type="<?=$control_type?>" 
																	name="primary_<?=$control_name?>[<?=$bamount['product_id']?>]" 
																	value="<?=$bamount[$control_name]?>"
																	class="<?=$class?> form-control" 
																		id="primary_<?=$control_name?>_<?=$bamount['product_id']?>"
																	<?=in_array($control_name,array("fname","lname","SSN")) ? "" : "readonly" ?>
																	/>
																	<label><?=$bamount['name'].' '.$label?> *</label>
																</div>
															</div>
														<?php }
															} }else{ ?>
														<div class="col-lg-3 col-md-6" >
															<div class="form-group">
																<input 	type="<?=$control_type?>" 
																name="primary_<?=$control_name?>" 
																value="<?=$control_value?>"
																class="<?=$class?> form-control" 
																id="primary_<?=$control_name?>"
																<?=in_array($control_name,array("fname","lname","SSN")) ? "" : "readonly" ?>
																/>
																<label><?=$label?> *</label>
															</div>
														</div>
														<?php } if(in_array($control_name,array("fname","lname","SSN"))){ ?>
															<input type="hidden" name="required_<?=$control_name?>" value="<?=$question['required']?>">
														<?php }?>
														
												<?php }else if($control_type == 'date_mask') { ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<div class="input-group">
															<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
															<div class="pr">
																<input  
																	type="text" 
																	name="primary_<?=$control_name?>" 
																	value="<?=getCustomDate($control_value);?>"
																	class="<?=$class?> form-control" 
																	id="primary_<?=$control_name?>"
																	readonly
																/>
																<label><?=$label?> *</label>
															</div>
															</div>
														</div>
													</div>
												<?php }else if(($control_type == 'select') && $control_name != 'benefit_percentage') {
														if($control_value == 'Y')
															$control_value = 'Yes';
														else if($control_value == 'N') 
															$control_value = 'No';
													?>
												<div class="col-lg-3 col-md-6" >
													<div class="form-group">
														<select 
														class="form-control <?=$class?> <?=$control_value !='' ? 'has-value' : '' ?>" 
														id="primary_<?=$control_name?>" 
														name="primary_<?=$control_name?>"
														title="&nbsp;" disabled>	
															<!-- <option value="" selected disable hidden></option> -->
															<?php /*if($control_name == 'queState') { */?>
															<option value="<?=$control_value?>" selected><?=ucfirst($control_value)?></option>
														</select>
														<label><?=$label?> *</label>
														<input type="hidden" name="primary_<?=$control_name?>" value="<?=$control_value?>">
														<?php $control_value=''; ?>
													</div>
												</div>
												<?php }else if($control_type == 'radio'){ ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<?php if($control_name == 'gender') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Male' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="primary_gender" readonly id="primary_gender_male" autocomplete="off" class="js-switch" value="Male"> Male
																</label>
																<label class="btn btn-info <?=$control_value == 'Female' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="primary_gender" readonly id="primary_gender_female" autocomplete="off" class="js-switch" value="Female"> Female
																</label>
																</div>
															<?php }else if($control_type == 'radio' && $control_name == 'has_spouse'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="primary_<?=$control_name?>" value="Y" readonly id="primary_has_spouse_yes" autocomplete="off" class="js-switch">Spouse
																</label>
																<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="<?=$control_name?>" value="N" readonly id="primary_has_spouse_no" autocomplete="off" class="js-switch"> No Spouse
																</label>
																</div>
															<?php }else if($control_name == 'tobacco_status') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_tobbaco_y" autocomplete="off" class="js-switch"> Tobacco
																</label>
																<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_tobbaco_n" autocomplete="off" class="js-switch"> No Tobacco
																</label>
																</div>
																<?php } else if($control_name == 'smoking_status') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_smoke_y" autocomplete="off" class="js-switch"> Smoke
																</label>
																<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_smoke_n" autocomplete="off" class="js-switch"> No Smoke
																</label>
																</div>
																<?php }else if($control_name == 'employment_status'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_employment_y" autocomplete="off" class="js-switch"> Employed
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_employment_n" autocomplete="off" class="js-switch"> Unemployed
																	</label>
																</div>
																<?php }else if($control_name == 'us_citizen'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_us_citizen_y" autocomplete="off" class="js-switch"> U.S. Citizen
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="primary_<?=$control_name?>" readonly id="primary_us_citizen_n" autocomplete="off" class="js-switch"> Not  U.S. Citizen
																	</label>
																</div>
																<?php } ?>
														</div>
													</div>
												<?php } ?>
											<?php } //Asked
									}else{
											$prd_question_id = $question['id'];
											$custom_name = str_replace($prd_question_id,"", $control_name);
											$sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N'";
											$resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));
											?>
											<div class="clearfix"></div>
											<?php if($control_type=='select'){ ?>
												<div class="col-sm-12 form-inline m-b-25">
												<div class="form-group  m-r-15">
													<label><?= $label ?></label>
												</div>
												<div class="form-group  w-300 custom_question">
													<select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $prd_question_id ?>]" class="form-control primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" disabled data-live-search="true">
													<option value=""></option>
													<?php if(!empty($resAnswer)){
														foreach ($resAnswer as $ansKey => $ansValue) { ?>
														<option value="<?= $ansValue['answer'] ?>" <?= ($control_value==$ansValue['answer'] ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
														<?php } ?>
													<?php } ?>
													</select>
													<label><?= $label ?></label>
												</div>
												</div>
											<?php }else if($control_type=='radio'){ ?>
												<div class="col-sm-12 form-inline m-b-25">
												<div class="form-group  m-r-15">
												<label><?= $label ?></label>
												</div>
												<div class="form-group ">
													<div class="btn-group colors  custom-question-btn" data-toggle="buttons">
													<?php if(!empty($resAnswer)){
														foreach ($resAnswer as $ansKey => $ansValue) { ?>
														<label class="btn btn-info <?= (!empty($control_value) && $control_value== $ansValue['answer'] ? 'active' : '') ?>">
																<input type="radio" readonly name="<?= $custom_name ?>[<?= $prd_question_id ?>]" value="<?= $ansValue['answer'] ?>" data-ans-eligible="<?= $ansValue['answer_eligible'] ?>" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value==$ansValue['answer'] ? 'checked' : '') ?> > <?= $ansValue['answer'] ?>
															</label>
														<?php } ?>
													<?php } ?>
													</div>
													<p class="error" id="error_<?= $control_name ?>"></p>
												</div>
												</div>
											<?php }else if($control_type=='select_multiple'){ ?>
												<div class="col-sm-12 form-inline m-b-25">
												<div class="form-group  m-r-15">
													<?= $label ?>
												</div>
												<div class="form-group  w-300 custom_question">
													<select id="<?= $control_name ?>" readonly name="<?= $custom_name ?>[<?= $prd_question_id ?>][]" class="se_multiple_select form-control primary_multiple_select primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" disabled multiple="multiple">
													<?php if(!empty($resAnswer)){
														$tmp_control_value = explode(',',$control_value);
														foreach ($resAnswer as $ansKey => $ansValue) { ?>
														<option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'],$tmp_control_value) ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
														<?php } ?>
													<?php } ?>
													</select>
													<label><?= $label ?></label>
												</div>
												</div>
											<?php } ?>
									<?php }//Question Type
								}//End Forloop 
							} ?>
							</div>
							<hr>
							<?php if(!empty($resCustomerDep)) { ?>
								<p class="fs32 fw300 m-b-30">Dependent Details</p>
								<?php $repeat = array() ; $count = 1; ?>
								<?php foreach($resCustomerDep as $dependent) :
									$is_dependent = getRevRelation($dependent['relation']) == 'Spouse' ? 'is_spouse' : 'is_child';
									$relation = getRevRelation($dependent['relation']);				
									?>
										<?php //if(!in_array($relation,$repeat)) {  ?>
											<h4 class="m-t-0 m-b-30"><?=getRevRelation($dependent['relation'])?><?php echo getRevRelation($dependent['relation']) == 'Child' ? "(".$count++.")": " "?></h4>
										<?php //array_push($repeat,$relation); } ?>
										<input type="hidden" name="dep_ids[<?=$dependent['id']?>]" value="<?=$dependent['id']?>">
										<div class="row enrollment_auto_row theme-form">
										<input type="hidden" name="dependent_relation_input[<?=$dependent['id']?>]" value="<?=strtolower($dependent['relation'])?>">
								<!-- spouse information start -->
									<?php foreach($spouse_product_question as $question){ 	
										$asked_question = false;
										$required_question = false;
											if($is_dependent=='is_spouse' && $question['asked'] == 'Y'){
												$asked_question = true;
												if($question['required'] == 'Y'){
													$required_question = true;
												}
											}
										$control_name = $question['label'];
										$control_type = $question['control_type'];
										$label = $question['display_label'];
										$class = $question['control_class'];
										$maxlength = $question['control_maxlength'];
										$control_attribute = $question['control_attribute'];
										if(!in_array($control_name,$benefit_amount_arr))
											$control_value = ${'dependent_'.$control_name.'_value'}[$dependent['id']];
										if($question['questionType']=="Default"){
											if($question['asked'] == 'Y' && $asked_question){ 	
													if($control_type == 'text' || $control_name == 'benefit_percentage') { ?>
													<?php 
													if($control_value == 'Y')
															$control_value = 'Yes';
													else if($control_value == 'N') 
															$control_value = 'No';
													?>
												<?php if(in_array($control_name,$benefit_amount_arr)){
													if(!empty($dep_benefit_amount)){
														foreach($dep_benefit_amount as $bamount){ ?>
													<?php if(getRevRelation($bamount['relation']) != 'Spouse' || $dependent['id'] != $bamount['cd_profile_id']){ continue ;}
														if(isset($bamount[$control_name]) && $bamount[$control_name] > 0){
													?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<input 	type="<?=$control_type?>" 
															name="dep_<?=$control_name?>[<?=$dependent['id']?>][<?=$bamount['product_id']?>]" 
															value="<?=$bamount[$control_name]?>"
															class="<?=$class?> form-control" 
															id="dep_<?=$control_name?>_<?=$dependent['id']?>_<?=$bamount['product_id']?>"
															<?=in_array($control_name,array("fname","lname","SSN")) ? "" : "readonly" ?>
															/>
															<label><?=$bamount['name'].' '.$label?> *</label>
														</div>
													</div>
												<?php } }
													} }else{ ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<input 	type="<?=$control_type?>" 
															name="dep_<?=$control_name?>[<?=$dependent['id']?>]" 
															value="<?=$control_value?>"
															class="<?=$class?> form-control" 
															id="dep_<?=$control_name?>_<?=$dependent['id']?>"
															<?=in_array($control_name,array("fname","lname","SSN")) ? "" : "readonly" ?>
															/>
															<label><?=$label?> *</label>
														</div>
													</div>
													<?php if(in_array($control_name,array("fname","lname","SSN"))){ ?>
														<input type="hidden" name="dep_required_<?=$control_name?>[<?=$dependent['id']?>]" value="<?=$required_question ? "Y" : "N"?>">
													<?php }?>
												<?php } } else if($control_type == 'date_mask') { ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<div class="input-group">
															<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
															<div class="pr">
																<input  
																	type="text" 
																	name="dep_<?=$control_name?>[<?=$dependent['id']?>]" 
																	value="<?=getCustomDate($control_value);?>"
																	class="<?=$class?> form-control" 
																	id="dep_<?=$control_name?>_<?=$dependent['id']?>"
																	readonly
																/>
																<label><?=$label?> *</label>
															</div>
															</div>
														</div>
													</div>
												<?php } else if($control_type == 'select' && $control_name != 'benefit_percentage') { ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<select 
															class="form-control <?=$class?> <?=$control_value !='' ? 'has-value' : '' ?>" 
															id="dep_<?=$control_name?>_<?=$dependent['id']?>" 
															name="<?=$control_name?>"
															title="&nbsp;" disabled>	
																<!-- <option value="" selected disable hidden></option> -->
																<?php /*if($control_name == 'queState') { */?>
																<option value="<?=$control_value?>" selected><?=ucfirst($control_value)?></option>
																
															</select>
															<label><?=$label?> *</label>
															<input type="hidden" name="dep_<?=$control_name?>_<?=$dependent['id']?>" value="<?=$control_value?>">
														</div>
													</div>
													
													
													<input type="hidden" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="<?=$control_value?>">
													
													<!-- <input type="hidden" name="dependent_product_input[<?=$dependent['id']?>][<?=$dependent['product_id']?>]" value="<?=getRevRelation($control_value)?>"> -->
													<input type="hidden" name="dep_<?=$control_name?><?=$dependent['id']?>" value="<?=$control_value?>"> 
													<?php $control_value=''; ?>
												<?php } else if($control_type == 'radio' && !empty($control_value)){ ?>
														<div class="col-lg-3 col-md-6" >
															<div class="form-group">
															<input type="hidden" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="<?=$control_value?>">
															<?php if($control_name == 'gender') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Male' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_gender_male_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="Male"> Male
																</label>
																<label class="btn btn-info <?=$control_value == 'Female' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_gender_female_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="Female"> Female
																</label>
																</div>
															<?php }else if($control_type == 'radio' && $control_name == 'has_spouse'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="Y" readonly id="dep_has_spouse_yes_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>">Spouse
																</label>
																<label class="btn btn-info <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="N" readonly id="dep_has_spouse_no_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> No Spouse
																</label>
																</div>
															<?php } else if($control_name == 'tobacco_status') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_tobbaco_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> Tobacco
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_tobbaco_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> No Tobacco
																	</label>
																</div>
																<?php } else if($control_name == 'smoking_status') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_smoke_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> Smoke
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_smoke_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> No Smoke
																	</label>
																</div>
																<?php }else if($control_name == 'employment_status'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_employment_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> Employed
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_employment_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> Unemployed
																	</label>
																</div>
																<?php }else if($control_name == 'us_citizen'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_us_citizen_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> U.S. Citizen
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_us_citizen_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> Not  U.S. Citizen
																	</label>
																</div>
																<?php } ?>
															</div>
														</div>
												<?php } 
											} 
										}else if($question['questionType']!="Default" && in_array($question['id'],$customQuestionArraySpouse) && $is_dependent=='is_spouse'){
											$prd_question_id = $question['id'];
											$custom_name = str_replace($prd_question_id,"", $control_name);
											$sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N'";
												$resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));
												?>
											<div class="clearfix"></div>
											<?php if($control_type=='select'){ ?>
												<div class="col-sm-12 form-inline m-b-25">
												<div class="form-group  m-r-15">
													<label><?= $label ?></label>
												</div>
												<div class="form-group  w-300 custom_question">
													<select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $prd_question_id ?>]" class="form-control primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" disabled data-live-search="true">
													<option value=""></option>
													<?php if(!empty($resAnswer)){
														foreach ($resAnswer as $ansKey => $ansValue) { ?>
														<option value="<?= $ansValue['answer'] ?>" <?= ($control_value==$ansValue['answer'] ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
														<?php } ?>
													<?php } ?>
													</select>
													<label><?= $label ?></label>
												</div>
												</div>
											<?php }else if($control_type=='radio'){ ?>
												<div class="col-sm-12 form-inline m-b-25">
												<div class="form-group  m-r-15">
												<label><?= $label ?></label>
												</div>
												<div class="form-group">
													<div class="btn-group colors  custom-question-btn" data-toggle="buttons">
													<?php if(!empty($resAnswer)){
														foreach ($resAnswer as $ansKey => $ansValue) { ?>
														<label class="btn btn-info <?= (!empty($control_value) && $control_value== $ansValue['answer'] ? 'active' : '') ?>">
																<input type="radio" readonly name="<?= $custom_name ?>[<?= $prd_question_id ?>]" value="<?= $ansValue['answer'] ?>" data-ans-eligible="<?= $ansValue['answer_eligible'] ?>" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value==$ansValue['answer'] ? 'checked' : '') ?> > <?= $ansValue['answer'] ?>
															</label>
														<?php } ?>
													<?php } ?>
													</div>
													<p class="error" id="error_<?= $control_name ?>"></p>
												</div>
												</div>
											<?php }else if($control_type=='select_multiple'){ ?>
												<div class="col-sm-12 form-inline m-b-25">
												<div class="form-group  m-r-15">
													<?= $label ?>
												</div>
												<div class="form-group  w-300 custom_question">
													<select id="<?= $control_name ?>" readonly name="<?= $custom_name ?>[<?= $prd_question_id ?>][]" class="se_multiple_select form-control primary_multiple_select primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" disabled multiple="multiple">
													<?php if(!empty($resAnswer)){
														$tmp_control_value = explode(',',$control_value);
														foreach ($resAnswer as $ansKey => $ansValue) { ?>
														<option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'],$tmp_control_value) ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
														<?php } ?>
													<?php } ?>
													</select>
													<label><?= $label ?></label>
												</div>
												</div>
											<?php }
										}
									} //end foreach spouse_product_question 
									?>
								<!-- spouse information end -->
								<!-- child information start -->
									<?php foreach($child_product_question as $question){ 	
											$asked_question = false;
											$required_question = false;
												if($is_dependent=='is_child' && $question['asked'] == 'Y'){
													$asked_question = true;
													if($question['required'] == 'Y'){
														$required_question = true;
													}
												}
											$control_name = $question['label'];
											$control_type = $question['control_type'];
											$label = $question['display_label'];
											$class = $question['control_class'];
											$maxlength = $question['control_maxlength'];
											$control_attribute = $question['control_attribute'];
											if(!in_array($control_name,$benefit_amount_arr))
												$control_value = ${'dependent_'.$control_name.'_value'}[$dependent['id']];
									 if($question['questionType']=="Default"){
										if($question['asked'] == 'Y' && $asked_question ){ 	
											if($control_type == 'text' || $control_name == 'benefit_percentage') { ?>

													<?php 
													if($control_value == 'Y')
															$control_value = 'Yes';
													else if($control_value == 'N') 
															$control_value = 'No';
													?>
												<?php if(in_array($control_name,$benefit_amount_arr)){
													if(!empty($dep_benefit_amount)){
														foreach($dep_benefit_amount as $bamount){ ?>
													<?php if(getRevRelation($bamount['relation']) != 'Child' || $dependent['id'] != $bamount['cd_profile_id']){ continue ;}
														if(isset($bamount[$control_name]) && $bamount[$control_name] > 0){
													?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<input 	type="<?=$control_type?>" 
															name="dep_<?=$control_name?>[<?=$dependent['id']?>][<?=$bamount['product_id']?>]" 
															value="<?=$bamount[$control_name]?>"
															class="<?=$class?> form-control" 
															id="primary_<?=$control_name?>_<?=$bamount['product_id']?>"
															<?=in_array($control_name,array("fname","lname","SSN")) ? "" : "readonly" ?>
															/>
															<label><?=$bamount['name'].' '.$label?> *</label>
														</div>
													</div>
													<?php }}} 
												}else{ ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<input 	type="<?=$control_type?>" 
															name="dep_<?=$control_name?>[<?=$dependent['id']?>]" 
															value="<?=$control_value?>"
															class="<?=$class?> form-control" 
															id="dep_<?=$control_name?>_<?=$dependent['id']?>"
															<?=in_array($control_name,array("fname","lname","SSN")) ? "" : "readonly" ?>
															/>
															<label><?=$label?> *</label>
														</div>
													</div>
													<?php if(in_array($control_name,array("fname","lname","SSN"))){ ?>
														<input type="hidden" name="dep_required_<?=$control_name?>[<?=$dependent['id']?>]" value="<?=$required_question ? "Y" : "N"?>">
													<?php }?>
													
												<?php } 
											}else if($control_type == 'date_mask') { ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<div class="input-group">
															<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
															<div class="pr">
																<input  
																	type="text" 
																	name="dep_<?=$control_name?>[<?=$dependent['id']?>]" 
																	value="<?=getCustomDate($control_value);?>"
																	class="<?=$class?> form-control" 
																	id="dep_<?=$control_name?>_<?=$dependent['id']?>"
																	readonly
																/>
																<label><?=$label?> *</label>
															</div>
															</div>
														</div>
													</div>
											<?php }else if($control_type == 'select' && $control_name != 'benefit_percentage') { ?>
													<div class="col-lg-3 col-md-6" >
														<div class="form-group">
															<select 
															class="form-control <?=$class?> <?=$control_value !='' ? 'has-value' : '' ?>" 
															id="dep_<?=$control_name?>_<?=$dependent['id']?>" 
															name="<?=$control_name?>"
															title="&nbsp;" disabled>	
																<!-- <option value="" selected disable hidden></option> -->
																<?php /*if($control_name == 'queState') { */?>
																<option value="<?=$control_value?>" selected><?=ucfirst($control_value)?></option>
																
															</select>
															<label><?=$label?> *</label>
															<input type="hidden" name="dep_<?=$control_name?>_<?=$dependent['id']?>" value="<?=$control_value?>">
														</div>
													</div>
													
													
													<input type="hidden" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="<?=$control_value?>">
													
													<!-- <input type="hidden" name="dependent_product_input[<?=$dependent['id']?>][<?=$dependent['product_id']?>]" value="<?=getRevRelation($control_value)?>"> -->
													<input type="hidden" name="dep_<?=$control_name?><?=$dependent['id']?>" value="<?=$control_value?>"> 
													<?php $control_value=''; ?>
											<?php }else if($control_type == 'radio' && !empty($control_value)){ ?>
														<div class="col-lg-3 col-md-6" >
															<div class="form-group">
															<input type="hidden" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="<?=$control_value?>">
															<?php if($control_name == 'gender') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Male' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_gender_male_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="Male"> Male
																</label>
																<label class="btn btn-info <?=$control_value == 'Female' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_gender_female_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="Female"> Female
																</label>
																</div>
															<?php }else if($control_type == 'radio' && $control_name == 'has_spouse'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="Y" readonly id="dep_has_spouse_yes_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>">Spouse
																</label>
																<label class="btn btn-info <?= !empty($control_value) ? 'disabled' : '' ?>">
																	<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" value="N" readonly id="dep_has_spouse_no_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> No Spouse
																</label>
																</div>
															<?php } else if($control_name == 'tobacco_status') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_tobbaco_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> Tobacco
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_tobbaco_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> No Tobacco
																	</label>
																</div>
																<?php } else if($control_name == 'smoking_status') { ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_smoke_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> Smoke
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_smoke_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch" value="<?=$control_value?>"> No Smoke
																	</label>
																</div>
																<?php }else if($control_name == 'employment_status'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_employment_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> Employed
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_employment_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> Unemployed
																	</label>
																</div>
																<?php }else if($control_name == 'us_citizen'){ ?>
																<div class="btn-group colors <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
																	<label class="btn btn-info <?=$control_value == 'Y' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_us_citizen_y_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> U.S. Citizen
																	</label>
																	<label class="btn btn-info <?=$control_value == 'N' ? 'active' : ''?> <?= !empty($control_value) ? 'disabled' : '' ?>">
																		<input type="radio" name="dep_<?=$control_name?>[<?=$dependent['id']?>]" readonly id="dep_us_citizen_n_<?=$dependent['id']?>" autocomplete="off" class="js-switch"> Not  U.S. Citizen
																	</label>
																</div>
																<?php } ?>
															</div>
														</div>
											<?php } 
										} 
									 }else if($question['questionType']!="Default" && in_array($question['id'],$customQuestionArrayChild)  && $is_dependent=='is_child'){
										$prd_question_id = $question['id'];
										$custom_name = str_replace($prd_question_id,"", $control_name);
										$sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N'";
											$resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));
											?>
										<div class="clearfix"></div>
										<?php if($control_type=='select'){ ?>
											<div class="col-sm-12 form-inline m-b-25">
											<div class="form-group  m-r-15">
												<label><?= $label ?></label>
											</div>
											<div class="form-group  w-300 custom_question">
												<select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $prd_question_id ?>]" class="form-control primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" disabled data-live-search="true">
												<option value=""></option>
												<?php if(!empty($resAnswer)){
													foreach ($resAnswer as $ansKey => $ansValue) { ?>
													<option value="<?= $ansValue['answer'] ?>" <?= ($control_value==$ansValue['answer'] ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
													<?php } ?>
												<?php } ?>
												</select>
												<label><?= $label ?></label>
											</div>
											</div>
										<?php }else if($control_type=='radio'){ ?>
											<div class="col-sm-12 form-inline m-b-25">
											<div class="form-group  m-r-15">
											<label><?= $label ?></label>
											</div>
											<div class="form-group ">
												<div class="btn-group colors  custom-question-btn" data-toggle="buttons">
												<?php if(!empty($resAnswer)){
													foreach ($resAnswer as $ansKey => $ansValue) { ?>
													<label class="btn btn-info <?= (!empty($control_value) && $control_value== $ansValue['answer'] ? 'active' : '') ?>">
															<input type="radio" readonly name="<?= $custom_name ?>[<?= $prd_question_id ?>]" value="<?= $ansValue['answer'] ?>" data-ans-eligible="<?= $ansValue['answer_eligible'] ?>" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value==$ansValue['answer'] ? 'checked' : '') ?> > <?= $ansValue['answer'] ?>
														</label>
													<?php } ?>
												<?php } ?>
												</div>
												<p class="error" id="error_<?= $control_name ?>"></p>
											</div>
											</div>
										<?php }else if($control_type=='select_multiple'){ ?>
											<div class="col-sm-12 form-inline m-b-25">
											<div class="form-group  m-r-15">
												<?= $label ?>
											</div>
											<div class="form-group  w-300 custom_question">
												<select id="<?= $control_name ?>" readonly name="<?= $custom_name ?>[<?= $prd_question_id ?>][]" class="se_multiple_select form-control primary_multiple_select primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" disabled multiple="multiple">
												<?php if(!empty($resAnswer)){
													$tmp_control_value = explode(',',$control_value);
													foreach ($resAnswer as $ansKey => $ansValue) { ?>
													<option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'],$tmp_control_value) ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
													<?php } ?>
												<?php } ?>
												</select>
												<label><?= $label ?></label>
											</div>
											</div>
										<?php }
									 }
									} ?>
								<!-- child information end -->
								</div>
								<hr>
								<?php  endforeach; ?>
							<?php }  ?>
							<?php if(!empty($customer_beneficiary) && (!empty($principal_beneficiary_field) || !empty($contingent_beneficiary_field) )) { 
								$exists_type = array();
								$pri_cnt = 1;
								$con_cnt = 1;
								?>
							<p class="fs32 fw300 m-b-30">Beneficiary Details</p>
							<?php if(!empty($principal_beneficiary)){
								foreach($principal_beneficiary as $principal_beneficiary_row){ ?>
							<?php if(!empty($principal_beneficiary_field)){ ?>
								<h4 class="m-b-15">Principal Beneficiary <?=$pri_cnt?></h4>
								<div class="row theme-form">
									<input type="hidden" name="principal_beneficiary_id[<?= $pri_cnt ?>]" id="principal_beneficiary_id_<?= $pri_cnt ?>" value="<?=$principal_beneficiary_row['id']?>">
									<?php foreach ($principal_beneficiary_field as $key => $row) { ?>
										<?php
										$prd_question_id = $row['id'];
										$is_required= $row['required'];
										$control_name = 'principal_'.$row['label'];
										$label = $row['display_label'];
										$control_type = $row['control_type'];
										$class = $row['control_class'];
										$maxlength = $row['control_maxlength'];
										$control_attribute = $row['control_attribute'];
										$questionType = $row['questionType'];
										if($control_name == "principal_queBeneficiaryAllow3"){
											continue;
										}
										?>
										<?php if($control_type=='text'){?>
											<div class="col-lg-3 col-md-6">
												<?php if($control_name =="principal_queBeneficiaryPercentage"){?>
													<div class="form-group">
														<div class="input-group">
															<div class="pr">
																<input type="text" id="<?= $control_name ?>_<?= $pri_cnt ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $pri_cnt ?>]" value="" class="form-control <?= $class ?>"  required data-id="<?= $pri_cnt ?>">
																<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
															</div>
															<div class="input-group-addon"> % </div>
														</div>
													</div>
												<?php }else{ ?>
													<div class="form-group">
														<input type="text" id="<?= $control_name ?>_<?= $pri_cnt ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $pri_cnt ?>]" value="" class="form-control <?= ($control_name == "principal_queBeneficiaryEmail") ? "no_space" : ""; ?> <?= $class ?>"  required data-id="<?= $pri_cnt ?>">
														<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
													</div>
												<?php } ?>
												
											</div>
										<?php }else if($control_type=='select'){ ?>
											<div class="col-lg-3 col-md-6">
												<div class="form-group">
													<select id="<?= $control_name ?>_<?= $pri_cnt ?>"  name="<?= $control_name ?>[<?= $pri_cnt ?>]" class="principal_beneficiary_select_<?= $pri_cnt ?> <?= $class ?>" required data-live-search="true" data-id="<?= $pri_cnt ?>">
														<option value="" hidden></option>
														<?php if($control_name=='principal_queBeneficiaryRelationship'){ ?>
															<option value="Child">Child</option>
															<option value="Spouse">Spouse</option>
															<option value="Parent">Parent</option>
															<option value="Grandparent">Grandparent</option>
															<option value="Friend">Friend</option>
															<option value="Other">Other</option>
														<?php } ?>
													</select>
													<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
											</div>
											</div>
										<?php } ?>
									<?php } ?>
								</div>
								<hr>
							<?php } $pri_cnt++; } } ?>
							<p class="error text-left" id="error_principal_beneficiary_general"></p>
							<?php if(!empty($contingent_beneficiary)){
								foreach($contingent_beneficiary as $contingent_beneficiary_row){  ?>
							<?php if(!empty($contingent_beneficiary_field)){ ?>
								<h4 class="m-b-15">Contingent Beneficiary <?= $con_cnt ?></h4>
								<div class="row theme-form">
									<input type="hidden" name="contingent_beneficiary_id[<?= $con_cnt ?>]" id="principal_beneficiary_id_<?= $con_cnt ?>" value="<?=$contingent_beneficiary_row['id']?>">
									<?php foreach ($contingent_beneficiary_field as $key => $row) { ?>
										<?php
										$prd_question_id = $row['id'];
										$is_required= $row['required'];
										$control_name = 'contingent_'.$row['label'];
										$label = $row['display_label'];
										$control_type = $row['control_type'];
										$class = $row['control_class'];
										$maxlength = $row['control_maxlength'];
										$control_attribute = $row['control_attribute'];
										$questionType = $row['questionType'];
										if($control_name == "contingent_queBeneficiaryAllow3"){
											continue;
										}
										?>
										<?php if($control_type=='text'){?>
											<div class="col-lg-3 col-md-6">
												<?php if($control_name =="contingent_queBeneficiaryPercentage"){?>
													<div class="form-group">
														<div class="input-group">
															<div class="pr">
																<input type="text" id="<?= $control_name ?>_<?= $con_cnt ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $con_cnt ?>]" value="" class="form-control <?= $class ?>"  required data-id="<?= $con_cnt ?>">
																<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
															</div>
															<div class="input-group-addon"> % </div>
														</div>
													</div>
												<?php }else{ ?>
													<div class="form-group">
														<input type="text" id="<?= $control_name ?>_<?= $con_cnt ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $con_cnt ?>]" value="" class="form-control <?= ($control_name == "contingent_queBeneficiaryEmail") ? "no_space" : ""; ?> <?= $class ?>"  required data-id="<?= $con_cnt ?>">
														<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
													</div>
												<?php } ?>
												
											</div>
										<?php }else if($control_type=='select'){ ?>
											<div class="col-lg-3 col-md-6">
												<div class="form-group">
													<select id="<?= $control_name ?>_<?= $con_cnt ?>"  name="<?= $control_name ?>[<?= $con_cnt ?>]" class="contingent_beneficiary_select_<?= $con_cnt ?> <?= $class ?>" required data-live-search="true" data-id="<?= $con_cnt ?>">
														<option value="" hidden></option>
														<?php if($control_name=='contingent_queBeneficiaryRelationship'){ ?>
															<option value="Child">Child</option>
															<option value="Spouse">Spouse</option>
															<option value="Parent">Parent</option>
															<option value="Grandparent">Grandparent</option>
															<option value="Friend">Friend</option>
															<option value="Other">Other</option>
														<?php } ?>
													</select>
													<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
											</div>
											</div>
										<?php } ?>
									<?php } ?>
								</div>
								<hr>
							<?php } $con_cnt++;} } ?>
							<p class="error text-left" id="error_contingent_beneficiary_general"></p>
							<?php } ?>
							<!-- <hr> -->
							<p class="fs32 fw300 m-b-30">Billing Information</p>
							<div class="payment_section mn pn">
								<div class="row">
								<div class="col-md-4">
									<h4 class="fs16 m-b-20">Select Payment Method</h4>
									<p class="error" id="error_payment_mode"></p>
									<div class="blue_arrow_tab">
									<ul class="nav nav-tabs nav-noscroll">
										<?php if($is_cc_accepted == true || $billing_data['payment_mode'] == "CC") { ?>
											<li class="<?=$billing_data['payment_mode'] == 'CC' ? 'active' : '' ?>"><a href="#credit_card" class="tabs_collapse" data-mode="CC" data-toggle="tab">Credit Card</a></li>
										<?php } ?>

										<?php if($is_ach_accepted == true || $billing_data['payment_mode'] == "ACH") { ?>
											<li class="<?=$billing_data['payment_mode'] == 'ACH' ? 'active' : '' ?>"> <a href="#bank_draft" class="tabs_collapse" data-mode="ACH" data-toggle="tab">ACH Bank Draft</a> </li>
										<?php } ?>
									</ul>
									</div>
									<div class="tab-content left_form_tab">
									<input type="hidden" name="payment_mode" id="payment_mode" value="<?=checkIsset($billing_data['payment_mode'])?>">
									<input type="hidden" name="billing_profile_id" id="billing_profile_id" value="<?=checkIsset($billing_data['billing_profile_id'])?>">

									<?php if($is_cc_accepted == true || $billing_data['payment_mode'] == "CC") { ?>
										<div class="tab-pane fade <?=$billing_data['payment_mode'] == 'CC' ? 'in active' : '' ?>" id="credit_card">
											<div class="row  theme-form">
											<div class="col-sm-12">
												<div class="form-group">
												<input type="text" class="form-control" name="name_on_card" id="name_on_card" value="<?=checkIsset($billing_data['fname']).' '.checkIsset($billing_data['lname'])?>">
												<label>Name On Card*</label>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
												<input type="text" class=" form-control" name="card_number" id="card_number" maxlength="16" value="" oninput="isValidNumber(this)">
												<label>Card Number <span id="cc_billing_detail"><?= !empty($billing_data['card_no']) ? "(".$billing_data['card_type']." *" . $billing_data['card_no'] . ")" : '' ?></span></label>
												<input type="hidden" name="full_card_number" value="<?=$billing_data['card_no_full']?>">
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
												<select name="card_type" class="form-control" id="card_type">
													<?php if(!empty($payment_res['acceptable_cc'])) {
														$payment_master_res = explode(',',$payment_res['acceptable_cc']);
														?>
														<?php foreach($payment_master_res as $ctype) : ?>
															<option value="<?=$ctype?>" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == $ctype ? 'selected="selected"' : '' ?> ><?= ucwords(str_replace('_',' ',$ctype))?></option>
														<?php endforeach;?>
													<?php }else{ ?>
														<option value="Visa" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Visa' ? 'selected="selected"' : '' ?>> Visa</option>
														<option value="MasterCard" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'MasterCard' ? 'selected="selected"' : '' ?>> MasterCard </option>
														<option value="Discover" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Discover' ? 'selected="selected"' : '' ?>> Discover </option>
														<option value="Amex" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Amex' ? 'selected="selected"' : '' ?>> American Express </option>
													<?php } ?>													
												</select>
												<label>Card Type*</label>
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
													<input type="text" name="expiration" id="expiration" value="<?=$date?>" class="form-control">
													<label>Expiration Date*</label>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group height_auto m-b-15">
													<input type="text" name="cvv" id="cvv" oninput="isValidNumber(this)" minlength="3" maxlength="4" value="<?=checkIsset($billing_data['cvv_no'])?>" class="form-control" >
													<label>CVV*</label>
													</div>
													<input type="hidden" name="require_cvv" value="<?=$payment_res['require_cvv'] == 'Y' ? 'yes' : 'no'?>">
												</div>
												</div>
											</div>
											</div>
										</div>
									<?php } ?>

									<?php if($is_ach_accepted == true || $billing_data['payment_mode'] == "ACH") { ?>
										<div class="tab-pane fade <?=$billing_data['payment_mode'] == 'ACH' ? 'in active' : '' ?>" id="bank_draft">
											<div class="theme-form">
											<div class="form-group">
												<input type="text" class="form-control" name="ach_name" id="ach_name" value="<?=checkIsset($billing_data['bankname'])?>" >
												<label>Bank Name*</label>
											</div>
											<div class="form-group">
												<select class="form-control <?=!empty($billing_data['ach_account_type']) ? 'has-value' : ''; ?>" name="account_type" id="account_type">
												<option value="checking" <?= (!empty($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'checking') ? 'selected="selected"' : '' ?>>Checking</option>
												<option value="savings" <?= (!empty($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'savings') ? 'selected="selected"' : '' ?>>Saving</option>
												</select>
												<label>Account Type*</label>
											</div>
											<div style="<?=!empty($billing_data['ach_routing_number']) ? '' : 'display:none'?>">
												<label class="text-white" id="routing_number_detail">Routing Number
												<?= !empty($billing_data['ach_routing_number']) ? "(***" . substr($billing_data['ach_routing_number'],-4) . ")" : '' ?>
												<span class="req-indicator">*</span></label>
											</div>
											<div class="form-group">
												<input type="text" class="form-control" value="" id="routing_number" name="routing_number" maxlength='9' oninput="isValidNumber(this)">
												<label>Routing Number*</label>
												<input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?=checkIsset($billing_data['ach_routing_number'])?>" maxlength='50' class="required form-control tblur">
											</div>
											<div style="<?=!empty($billing_data['ach_account_number']) ? '' : 'display:none'?>">
											<label class="text-white">Account Number <span id="ach_billing_detail"><?= !empty($billing_data['ach_account_number']) ? "(ACH *" . substr($billing_data['ach_account_number'],-4) . ")" : '' ?></span><span class="req-indicator">*</span></label>
											</div>
											<div class="form-group">
												<input type="text" class="form-control" id="account_number" name="account_number" value="" oninput="isValidNumber(this)" maxlength='17'>
												<label>Account Number*</label>
												<input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=checkIsset($billing_data['ach_account_number'])?>" maxlength='50' class="form-control">
											</div>
											<div class="form-group">
												<input type="text" class="form-control" name="confirm_account_number" id="confirm_account_number" oninput="isValidNumber(this)" maxlength='17'>
												<label>Confirm Account Number*</label>
											</div>
											</div>
										</div>
									<?php } ?>
										<div class=" billing_info hidden">
										<h5 class="m-t-0 text-white mn">Billing Address</h5>
										<p class="text-white mn">
										<span><?=checkIsset($billing_data['fname'])?> <?=checkIsset($billing_data['lname'])?></span><br>
										<span><?=checkIsset($billing_data['address'])?></span><span><?=checkIsset($billing_data['address2'])?></span><br>
										<span><?=checkIsset($billing_data['city'])?></span>,
										<span><?=checkIsset($billing_data['state'])?></span> 
										<span><?=checkIsset($billing_data['zip'])?></span><br>
										</p>
										</div>
									</div>
								</div>
								<div class="col-md-8">
									<h4 class="fs16 m-b-20 ">Billing Address</h4>
									<div class="m-b-30">
									<label class="mn"><input type="checkbox" value="" id="same_as_personal" <?php echo $same_as_personal ? 'checked' : ''; ?>>Same as primary plan holder?</label>
									</div>
									<div class="row theme-form bill_info">
									<div class="col-sm-12">
										<div class="form-group">
										<input type="text" name="bill_fname" id="bill_fname" value="<?=checkIsset($billing_data['fname']).' '.checkIsset($billing_data['lname'])?>" class="form-control">
										<label>Full Name*</label>
										</div>
									</div>
									<!-- <div class="col-sm-6">
										<div class="form-group">
										<input type="text" name="bill_lname" id="bill_lname" value="<?=checkIsset($billing_data['lname'])?>" class="form-control">
										<label>Last Name*</label>
										</div>
									</div> -->
									<div class="col-sm-6">
										<div class="form-group">
										<input type="text" name="bill_address" id="bill_address" value="<?=checkIsset($billing_data['address'])?>" class="form-control">
										<label>Address</label>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
										<input type="text" name="bill_address2" id="bill_address2" value="<?=checkIsset($billing_data['address2'])?>" class="form-control" onkeypress="return block_special_char(event)">
										<label>Address 2 (suite, apt)</label>
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
											<select class="form-control" id="bill_state" name="bill_state" title="&nbsp;" >	
												<option value="" selected disable hidden></option>
												<?php if(!empty($allStateRes)){ 
													foreach($allStateRes as $state){
													?>
													<option value="<?=$state['name']?>" <?=checkIsset($billing_data['state']) == $state['name'] ? 'selected' : '';?>><?=$state['name']?></option>
												<?php } } ?>
											</select>
										<?php ?>
										<label>State*</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
										<input type="text" name="bill_zip" id="bill_zip" class="form-control" value="<?=checkIsset($billing_data['zip'])?>">
										<label>Zip Code*</label>
										</div>
									</div>
									</div>
								</div>
								<?php if(strtotime(date("m/d/Y",strtotime('+1 day'))) < strtotime($coverge_effective_date)) { ?>
									<div class="col-md-4 theme-form">
										<div class="m-b-25">
										<label class="mn"><input type="checkbox" name="enroll_with_post_date" value="yes" id="enroll_with_post_date" <?=$future_payment=='Y'?'checked':''?>>Click to set payment for future date: <i class="fa fa-info text-info i_post_date_div" id="post_info_popover" style="display: none;"></i> </label>
										</div>
										<div class="post_date_div"  style="<?php echo checkIsset($post_date)!='' ? '' : 'display: none' ; ?>">
											<div class="form-group">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													<div class="pr">
														<input type="text" name="post_date" id="post_date" value="<?=checkIsset($post_date)?>" class="form-control"/>
														<label>Post Payment Date(MM/DD/YYYY)</label>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
								</div>
							</div>
							</div>
							<div class="panel-footer text-center">
							<button type="button" class="btn btn-action" id="edit_verification">Save</button>
							<a href="javascript:void(0)" onclick="$.colorbox.close(); return false;" class="btn red-link">Cancel</a>
							</div>
						</div>
					</div>
					<div id="verification_information">
						<div>
							<h4 class="m-t-0 m-b-30">Electronic Signature</h4>
							<p class="m-b-30">I agree that I have a full and complete understanding of the products for which I am electing and I am the applicant listed above. I accept the following:</p>
								<?php if(!empty($products)) { ?>
									<div class="table-responsive">
										<table class="<?=$table_class?>">
										<thead>
											<tr>
												<th width="30px">
											
												<div class="checkbox checkbox-custom mn">
												<input 	type="checkbox" 
														name="product_check_all" 
														class="js-switch product_check_all" 
														id="product_check_all" >
														<label for="product_check_all"></label>
													</div>
												</th>
												<th class="text-center">Details</th>
												<th>Category</th>
												<th>Name</th>
												<th>Effective Date</th>
												<th class="text-center">Terms</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($products as $prd) : ?>
											<input type="hidden" name="product_list[<?=$prd['product_id']?>]" value="<?=$prd['product_id']?>">
											<tr>
												<td>
													<div class="checkbox checkbox-custom mn">
													<input 	type="checkbox" 
															name="product_check[<?=$prd['product_id']?>]" 
															id="product_check_<?=$prd['product_id']?>" 
															class="js-switch select_checkbox"
															value="<?=$prd['product_id']?>">
															<label for="product_check[<?=$prd['product_id']?>]"></label>
													<!-- <span class="error" id="error_product_check_<?=$prd['product_id']?>"></span> -->
												</div>
												</td>
												<td class="text-center icons">
												<?php if(!in_array($prd['product_type'], array('Vendor','Membership'))){ ?>
													<a href="javascript:void(0);"  data-desc="product_id=<?=md5($prd['product_id'])?>" data-url="prd_description_popup" class="prd_terms_popup"><i class="material-icons"> info </i></a>
												<?php }elseif($prd['product_type'] == 'Membership'){?>
													<a href="javascript:void(0);" data-desc="product_id=<?=md5($prd['product_id'])?>" data-url="prd_description_popup" class="prd_terms_popup"><i class="material-icons"> info </i></a>
												<?php }else echo '-'; ?>
												</td>
												<td>
													<?php if(in_array($prd['product_type'], array('Membership','Vendor'))){ ?>
														<?=$prd['product_type']?> &nbsp;&nbsp;&nbsp;&nbsp;
														<?php }else{ ?>
														<?=$prd['title']?> &nbsp;&nbsp;&nbsp;&nbsp;
													<?php } ?>
												</td> 
												<td>
													<?=$prd['name']?>	
												</td>
												<td>
													<?=$startCoveragePeriods[$prd['product_id']]?>	
												</td>
												<td class="text-center"><a href="javascript:void(0);" data-desc="product_id=<?=md5($prd['product_id'])?>" data-url="prd_terms_popup" class="prd_terms_popup"><i class="material-icons fa-lg fa fa-file-text-o"></i></a></td>
											</tr>
										<?php endforeach; ?>
										<?php if(!empty($healhty_step['id'])) : ?>
											<tr>
												<td>
													<div class="checkbox checkbox-custom mn">
													<input type="hidden" name="product_list[<?=$healhty_step['id']?>]" value="<?=$healhty_step['id']?>">
													<input 	type="checkbox" 
															name="product_check[<?=$healhty_step['id']?>]" 
															class="js-switch select_checkbox"
															value="<?=$prd['product_id']?>">
															<label for="product_check[<?=$healhty_step['id']?>]"></label>
													<!-- <span class="error" id="error_product_check_<?=$healhty_step['id']?>"></span> -->
												</div>
												</td>
												<td class="text-center icons">
													<a href="javascript:void(0)" data-desc="product_id=<?=md5($healhty_step['id'])?>&sponsor_id=<?=$md5sponsor_id?>" data-id="<?=$prd['product_id']?>" class="terms_popup"><i class="material-icons"> info </i></a>
												</td>
												<td>
													Healthy Step
												</td> 
												<td>
													<?=$healhty_step['name']?>	
												</td>
												<td>
													-	
												</td>
											</tr>
										<?php endif; ?>
										</tbody>
										</table>
									</div>
									
								<!-- <div class="mb15">
									<label class="mn">
										<input type="checkbox" 
										name="product_check[<?=$prd['product_id']?>]" 
										value="<?=$prd['product_id']?>">
										<?php if(in_array($prd['product_type'], array('Membership','Vendor'))){ ?>
										Product Category: <?=$prd['product_type']?> &nbsp;&nbsp;&nbsp;&nbsp;
										<?php }else{ ?>
										Product Category: <?=$prd['title']?> &nbsp;&nbsp;&nbsp;&nbsp;
										<?php } ?>
										Product Name: <?=$prd['name']?> &nbsp;&nbsp;&nbsp;&nbsp; 
										Effective Date: <?=$startCoveragePeriods[$prd['product_id']]?> &nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:void(0)"  data-desc="product_id=<?=md5($prd['product_id'])?>&sponsor_id=<?=$md5sponsor_id?>" data-id="<?=$prd['product_id']?>" class="terms_popup text-red"><i class="material-icons"> info </i></a></label>
									<br>
									<span class="error" id="error_product_check_<?=$prd['product_id']?>"></span>
								</div> -->
								
								<?php } ?>
								<!-- <?php if(!empty($healhty_step['id'])) : ?>
								<div class="mb15">
									<label class="mn"><input type="checkbox" name="product_check[<?=$healhty_step['id']?>]" value="<?=$healhty_step['id']?>"><?=$healhty_step['name']?></label>
									<input type="hidden" name="product_list[<?=$healhty_step['id']?>]" value="<?=$healhty_step['id']?>"><br>
									<span class="error" id="error_product_check_<?=$healhty_step['id']?>"></span>
								</div>
								<p class="error" id="error_products"></p>
								<?php endif; ?> -->
								<span class="error" id="error_product_check_all"></span>
							<hr>
							<?php //if(!empty($products)) { ?>
								<div class="m-t-30 m-b-10">
									<label class="mn label-input">
									<input type="hidden" name="product_term[<?=$order_id?>]" value="<?=$order_id?>">
										<input type="checkbox" value="<?=$order_id?>" name="product_term_check[<?=$order_id?>]">&nbsp;I acknowledge that I have read and agree to the <a href="javascript:void(0)" class="terms_popup fw500 red-link" data-desc="id=<?=md5($order_id)?>&sponsor_id=<?=$md5sponsor_id?>" >terms and conditions</a> in this agreement.
										<p class="error" id="error_product_term_check_<?=$order_id?>"></p>
										</label>
								<?php //endforeach; ?>
								</div>
							<?php //} ?>
							<?php 
								if(!empty($joinderAgreementProducts)){ 
							?>
								<div class="m-t-30 m-b-10">
								<label class="mn label-input">
								<input type="hidden" name="joinder_agreement" value="<?=$joinder_agreement?>">
								<input type="checkbox" name="joinder_agreement_check" value="Y">&nbsp;I acknowledge that I have read and agree to the <a href="javascript:void(0);" data-desc="order_id=<?=md5($order_id)?>" class="prd_agreement_popup fw500 red-link">Joinder Agreement</a>.
								<p class="error" id="error_joinder_agreement_check"></p>
								</label>
								</div>
							<?php } ?>

							
							<!--Signature Pad Start  -->
							<div id="error_signature-pad" class="pr">
								<input type="text" style="opacity: 0;width: 0;height: 0" name="signature_data" value="" id="signature_data">
							</div>
							<div id="signature-pad" class="m-signature-pad" style="height:300px">
								<div class="m-signature-pad--body">
									<canvas></canvas>
								</div>
								<div class="m-signature-pad--footer">
									<div class="description pull-left">
									</div>
									<!-- <div class="description pull-left">Draw your signature above</div> -->
									<div class="pull-right">
										<button type="button" class="btn red-link m-t-5" data-action="clear">Clear Signature</button>
									</div>
								</div>
							</div>
							<p class="error"  id="error_signature_data"></p>
							<!--Signature Pad End  -->
						</div>
						<div class="m-t-30 text-center">
							<button type="button" name="btn_submit_application" id="btn_submit_application" class="btn btn-action btn_submit_application">Apply</button>
							<!-- <a href="#enroll_popup" class="enroll_popup btn btn-action">Enroll</a> -->
							<?php /*<a href="<?=$_SERVER['REQUEST_URI']?>"  class="btn red-link">Cancel Policy</a> */ ?>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="smarte_footer mn">
			<div class="container m-b-15">
					<div class="row footer_help">
					<div class="col-xs-7">
						<div style="<?=$sponsor_detail['display_in_member'] == 'N' ? '' : 'display:none'?>">
						<h4 class="text-action m-t-0">NEED HELP?</h4>
						<p class="mn need_help"><span><?=$sponsor_detail['name']?></span><span><?=format_telephone($sponsor_detail['cell_phone'])?></span><span><?=$sponsor_detail['email']?></span></p>
						</div>
					</div>
					<div class="col-xs-5 m-t-0 text-right">
						<div class="powered_by_logo">
						<?php if($sponsor_detail['is_branding'] == 'Y' && file_exists($AGENTS_BRAND_ICON . $sponsor_detail['brand_icon'])) { ?>
							<img src="<?=$AGENTS_BRAND_ICON_WEB . $sponsor_detail['brand_icon']?>" height="43px" />
						<?php } else { ?>
							<img src="<?php echo $POWERED_BY_LOGO; ?>" height="43px" />
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="bottom_footer ">
				<div class="container">
					<ul>
						<li><?= $DEFAULT_SITE_NAME ?> &copy; <?php echo date('Y')?> </li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div style='display:none'>
	<div id='enroll_popup' class="panel panel-default mn panel-shadowless">
		<div class="panel-body login-alert-modal">
			<div class="media br-n pn mn">
				<div class="media-left"> <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
				<div class="media-body theme-form">
					<h3 class="text-action m-t-n fw600">Uh Oh!</h3>
					<p id="error_text" style="display:none">Incomplete payment Information, please update your payment details to process payment.</p>
					<p id="error_response"></p>
				</div>
				<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-action" id="update_billing" style="display:none">Update Billing</a>
						<a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="btn red-link">Close</a>
				</div>
			</div>
		</div>
	</div>
	<div id='assistance_popup' class="panel panel-default panel-block panel-shadowless mn popup-height">
		<div class="panel-body">
			<h4 class="text-action">Need Help?</h4>
			<p class=" need_help"><span><?=$sponsor_detail['name']?></span><span><?=format_telephone($sponsor_detail['cell_phone'])?></span><span><?=$sponsor_detail['email']?></span></p>
			<div class="text-center">
				<a href="javascript:void(0);" class="btn red-link pn" onclick='parent.$.colorbox.close(); return false;'>Close</a>
			</div>
		</div>
	</div>
	<div class="panel-body login-alert-modal" id="successWindow" >
		<div class="media br-n pn mn">
			<div class="media-left"> <img src="<?= $AGENT_HOST ?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
			<div class="media-body theme-form">
				<h3 class="blue-link m-t-n fw600 fs24 m-b-10" >Success!</h3>
				<p class="m-b-20">You have sucessfully enrolled and are now a member!<br>Click the login button below to visit your portal.</p>
			</div>
			<div class="text-center">
				<a href="<?= $CUSTOMER_HOST ?>" class="btn btn-info confirm">Login</a>
				<a href="<?= $HOST ?>" class="btn red-link pn">Exit</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
	// signaturePadInit();
	$("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
	var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
	$.ajax({
		url:"<?=$HOST?>/enrollment_verification.php",
		data : {timezone:timezone,order_date:$("#order_date").val()},
		type:"POST",
		dataType : 'json',
		success : function(res){
			$("#order_date_text").text(res.date);
		}
	});
	$("#order_date").text();
	if($("#bill_state").val() !== '' && $("#bill_state").val() !== undefined){
		$("#bill_state").addClass('has-value');
	}
	$('.assistance_popup').colorbox({
	  inline:true,
      width:"465px;",
      height:"155px;",
      closeButton:false,
      onComplete : function(e){
		if ($(window).width() <= 767) {
			parent.$.colorbox.resize({
	            width: "465px",
	            height: "195px"
	        });
		}
		}
    });
    $(document).off("click",".terms_popup");
    $(document).on("click",".terms_popup",function(e){
        e.preventDefault();
        $search_query = $(this).attr('data-desc');
        $link = "<?=$HOST?>/verification_terms.php?"+$search_query;
        $.colorbox({
            href : $link,
            iframe:true,
            width:"800px;",
            height:"600px;"
        });   
    });

    $(document).off("click",".prd_terms_popup");
    $(document).on("click",".prd_terms_popup",function(e){
        e.preventDefault();
        $urlType = $(this).attr('data-url');
        $search_query = $(this).attr('data-desc');
        if($urlType == 'prd_terms_popup'){
        	$link = "<?=$HOST?>/prd_term_popup.php?"+$search_query;
        }else{
        	$link = "<?=$HOST?>/prd_description_popup.php?"+$search_query;
        }
        $.colorbox({
            href : $link,
            iframe:true,
            width:"800px;",
            height:"600px;"
        });   
    });

    $(document).off("click",".prd_agreement_popup");
    $(document).on("click",".prd_agreement_popup",function(e){
        e.preventDefault();
        $search_query = $(this).attr('data-desc');
        $link = "<?=$HOST?>/prd_agreement_popup.php?"+$search_query;
        $.colorbox({
            href :$link,
            iframe:true,
            width:"800px;",
            height:"600px;"
        });   
    });
    $('.enroll_popup').colorbox({
      inline:true,
      width:"530px;",
      height:"225px;",
    });
	setPostDate();
	var quote_contingent_beneficiary = <?php echo !empty($contingent_beneficiary)?json_encode($contingent_beneficiary):'[]'; ?>;
	var quote_principal_beneficiary = <?php echo !empty($principal_beneficiary)?json_encode($principal_beneficiary):'[]'; ?>;

	if(quote_principal_beneficiary.length > 0) {
		$.each(quote_principal_beneficiary,function(index,beneficiary_row){
			$display_number = index + 1;
			principal_beneficiary_field($display_number,beneficiary_row);
		});
	}
	
	if(quote_contingent_beneficiary.length > 0) {
		$.each(quote_contingent_beneficiary,function(index,beneficiary_row){
			$display_number = index + 1;
			contingent_beneficiary_field($display_number,beneficiary_row);
		});
	}
  });
$("#dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
$(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
$(document).off("click","#submit_date");
$(document).on("click","#submit_date",function(e){
$(".error").html("");
var dob = $("#dob").val();
var customer_id = <?=$customer_id?>;
var lead_dis_id =  '<?=$lead_display_id?>';
var lead_id =  <?=$lead_quote_details_res['lead_id']?>;
$.ajax({
	url : "<?=$HOST?>/ajax_check_birth_date.php",
	data : {dob:dob,id:customer_id,lead_dis_id:lead_dis_id,lead_id:lead_id},
	datType : 'json',
	type : 'POST',
	beforeSend:function(){
	$("#ajax_loader").show();
	},
	success : function(res){
	$("#ajax_loader").hide();
	if(res.status === 'success'){
		$("#enrollment_verification_age").hide();
		$("#enrollment_verification_dashbpard").show();
		resizeCanvas();
	}else{
		$("#error_dob").html(res.message);
	}
	}
})
});

// $(document).off('click','.enrollmentPage');
// $(document).on('click','.enrollmentPage',function(e){
// 	var $id=$(this).attr('data-id');
// 	var $href = "#enrollmentPage_"+$id;
// 	$.colorbox({
// 	  href:$href,
//       inline:true,
//       width:"70%",
//       height:"80%",
//     });
// });
$(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
$("#bill_zip").inputmask({"mask": "99999",'showMaskOnHover': false});
$('#expiration').datepicker({
	format: 'mm/yy',
	startView : 1,
	minViewMode: 1,
	autoclose: true,	
	startDate:new Date(),
	endDate : '+15y'
});

$(document).off('click','#product_check_all');
$(document).on('click','#product_check_all',function(e){
    if($(this).is(":checked")){
        $(".select_checkbox").prop('checked',true);
    }else{
        $(".select_checkbox").prop('checked',false);
    }
});

$(document).off('change',".select_checkbox");
$(document).on('change',".select_checkbox",function(){
    if($('.select_checkbox:checked').length == $('.select_checkbox').length){
        $('#product_check_all').prop('checked',true);        
    }else{
        $('#product_check_all').prop('checked',false);
    }
});


	  
// $("#cvv").inputmask("9999");
$(document).off('click', '#same_as_personal');
$(document).on('click', '#same_as_personal', function () {
	// $("#bill_zip").mask("999999");
	if ($(this).is(":checked")) {
		$("#bill_fname").val($("#primary_fname").val()+' '+$("#primary_lname").val());
		// $("#bill_lname").val($("#primary_lname").val());

		$("#bill_address").val($("#primary_address1").val());
		$("#bill_city").val($("#primary_city").val());
		$("#bill_state").val($("#primary_state").val()).change();
		$("#bill_zip").val($("#primary_zip").val());
		$(".bill_info input").addClass('has-value');
	} else {
		$(".bill_info input").removeClass('has-value');
		$("#bill_fname").val('');
		// $("#bill_lname").val('');
		$("#bill_address").val('');
		$("#bill_city").val('');
		$("#bill_state").val('').change();
		$("#bill_zip").val('');
	}
});

$(document).off('click','.enrollment_verification_edit');
$(document).on('click','.enrollment_verification_edit',function(e){
	// alert();
	openColorbox();
});

$(document).off('click','#edit_verification');
$(document).on('click','#edit_verification',function(){
	$(".error").html("");
	var $data = $("#enrollment_verification_edit_form").serializeArray();
	submitForm($data);
});
$(document).off('click','#btn_submit_application');
$(document).on('click','#btn_submit_application',function(){
	$(".error").html("");	
	if (!(signaturePad.isEmpty())) {
        $("#signature_data").val(signaturePad.toDataURL());
      }

	var $data = $("#enrollment_verification_edit_form").serializeArray();
	$data.push({'name':'btn_submit_application','value':"btn_submit_application"});
	submitForm($data);
});


$(document).off('click','.tabs_collapse');
$(document).on('click','.tabs_collapse',function(){
	var $mode = $(this).attr('data-mode');
	$("#payment_mode").val($mode);
});

 var wrapper = document.getElementById("signature-pad"),
    clearButton = wrapper.querySelector("[data-action=clear]"),
    savePNGButton = wrapper.querySelector("[data-action=save-png]"),
    saveSVGButton = wrapper.querySelector("[data-action=save-svg]"),
    canvas = wrapper.querySelector("canvas"),
    signaturePad;

// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
        signaturePad.clear();
    }
    $("#signature_data").val("");
}

window.onresize = resizeCanvas;
resizeCanvas();

signaturePad = new SignaturePad(canvas);

clearButton.addEventListener("click", function (event) {
	signaturePad.clear();
	$("#signature_data").val("");
});

function scrollToElement(e) {
    var offset = $(e).offset();
    var offsetTop = offset.top;
    var totalScroll = offsetTop - 200;
    $('body,html').animate({
        scrollTop: totalScroll
    }, 1200);
}
function submitForm($data){
	// console.log($data);
	// signaturePadInit();
	$.ajax({
		url:"<?=$HOST?>/ajax_edit_enrollment_verification.php",
		type:"post",
		data:$data,
		dataType:"json",
		beforeSend :function(){
			$("#ajax_loader").show();
		},
		success:function(res){
			$("#ajax_loader").hide();
			if(typeof(res.existing_email) !== "undefined") {
				window.location.reload();
			}
			
			// if(res.status =='success'){
			// 	console.log(res);
			// }else
			if(res.status =='quote_not_found'){
				window.location="<?=$HOST?>/lead_quote_enrollment_response.php";
			}else if(res.status =='application_already_submitted'){
				window.location="<?=$HOST?>/lead_quote_enrollment_response.php";
			}else if(res.status =='account_approved'){
				var $incr = "?orderId="+res.order_id;
				if(res.payment_type === 'list_bill' || res.payment_type === 'tpa' ){
					var $incr = "?memberId="+res.customer_id;
				}
				window.location="<?=$HOST?>/enrollment_successfull.php"+$incr;
				// $.colorbox({
				// 	href:"#successWindow",
				// 	inline:true,
				// 	width:"470px;",
				// 	height:"200px",
				// 	closeButton :false,
				// 	fixed:true,
				// 	overlayClose: false,
				// 	onComplete : function(e){
				// 	if ($(window).width() <= 767) {
				// 		parent.$.colorbox.resize({
				//             width: "470px",
				//             height: "245px"
				//         });
				// 	}
				// 	}
				// });
			}else{
				if(res.status_popup == 'edit_popup'){
					$("#error_text").show();
					$("#update_billing").hide();
					$("#error_response").html("");
					$.colorbox({
						href:"#enroll_popup",
						inline:true,
						width:"530px;",
						height:"267px;",
						onClosed : function(e){
							$("#edit_information_popup").hide();
							$("#verification_information").show();
						}
					});
				}else if(res.status == 'save_popup'){
					$.colorbox.close();
				}else if(res.status == 'payment_fail' || res.status == 'next_day'){
					if(res.status == 'payment_fail'){
						$("#update_billing").show();
					}else{
						$("#update_billing").hide();
					}
					$("#error_response").html("");
					$("#error_response").html(res.payment_error);
					$("#error_text").hide();
					$.colorbox({
						href:"#enroll_popup",
						inline:true,
						width:"530px;",
						height:"267px;",
						onClosed : function(e){
							$("#edit_information_popup").hide();
							$("#verification_information").show();
						}
					});
				}
				$.each(res.errors,function(key,error){
					$("#"+key).parents('.form-group').append("<span class='error'>"+error+"</span>");
					// if(key === 'payment_mode'){
					$("#error_"+key).html(error).show();
					// }
					// scrollToElement($("#enrollment_verification_edit_form"));

					if(key == "signature_data") {
						resizeCanvas();
					}
				});

				if(res.billing_details !=='' || res.billing_details !== undefined){
					$(".billing_details").html(res.billing_details);
					// $("#name_on_card").val($("#bill_fname").val());
				}

				if(res.dependent_details !=='' || res.dependent_details !== undefined){
					$("#dependent_details").html(res.dependent_details);
				}

				if(res.primary_member_details !=='' || res.primary_member_details !== undefined){
					$("#primary_member_details").html(res.primary_member_details);
				}

				if(res.post_payment_details !=='' || res.post_payment_details !== undefined){
					if(res.post_payment_details !=='not_set'){
						$("#post_payment_date").show();
						$("#post_payment_details").html(res.post_payment_details);
					}else{
						$("#post_payment_date").hide();
					}	
				}

				if(res.routing_number_detail !=='' || res.routing_number_detail !== undefined){
					$("#routing_number_detail").html(res.routing_number_detail);
				}

				if(res.ach_billing_detail !=='' || res.ach_billing_detail !== undefined){
					$("#ach_billing_detail").html(res.ach_billing_detail);
				}

				if(res.cc_billing_detail !=='' || res.cc_billing_detail !== undefined){
					$("#cc_billing_detail").html(res.cc_billing_detail);
				}
			}
		}
	});
}
$(document).off('change', 'input#enroll_with_post_date');
$(document).on('change', 'input#enroll_with_post_date', function () {
      if ($(this).is(':checked')) {
        $("div.post_date_div").show();
      } else {
        $("div.post_date_div").hide();
      }
    });

    updateIframe = function () {
      <?php if (isset($_GET["iframe"])) {
        echo "$(function(){resizeIframe()})";
      } ?>
    };

$(document).off('click','#update_billing');
$(document).on('click','#update_billing',function(e){
	$(this).hide();
	$("#edit_information_popup").show();
	$("#verification_information").hide();
	
	$.colorbox({
		href:"#enrollment_verification_edit_form",
		inline:true,
		width:"90%;",
		height:"600px;",
		onClosed : function(e){
			$("#edit_information_popup").hide();
			$("#verification_information").show();
		}
	});
});

$(document).on('click','.btn-select',function(){
	$('.bootstrap-select.bs-container').css('z-index',999999);
});

function openColorbox(){
	$.colorbox({
		href:"#enrollment_verification_edit_form",
		inline:true,
		width:"90%;",
		height:"600px;",
		onOpen : function(e){
			// alert();
			$("#edit_information_popup").show();
			$("#verification_information").hide();
			
		},
		onClosed : function(e){
			$("#edit_information_popup").hide();
			$("#verification_information").show();
		}
	});
}
setPostDate = function(){
	var effective_dates = [];
	var cnt = 0;
	$("#enrollment_verification_edit_form .coverage_date_input").each(function(index,element){
		effective_dates[cnt] = $(this).val();
		cnt++;
	});
	if(effective_dates.length > 0) {

		var lowest_effective_date = effective_dates.reduce(function (a, b) { return a < b ? a : b; }); 
		
		var next_billing_date = moment(lowest_effective_date).add(1, 'M').add(-4,'d').format("MM/DD/YYYY");
		var old_post_date = $("#post_date").val();
		if((new Date(lowest_effective_date) < new Date(old_post_date)) || lowest_effective_date == old_post_date) {
			$("#post_date").val(moment(lowest_effective_date).add(-1,'d').format("MM/DD/YYYY"));
		}
		try{ $('#post_date').data('datepicker').remove(); }catch(e){}
		$("#post_date").datepicker({
			startDate: "<?=date("m/d/Y",strtotime("+1 days"))?>",
			endDate: moment(lowest_effective_date).add(-1,'d').format("MM/DD/YYYY"),
			orientation: "bottom",
			enableOnReadonly: true
		});
	}
}
 $(function() {
   $('.receipt_left').matchHeight({
         target: $('.receipt_right'),
         property: 'min-height'
     });
   });

principal_beneficiary_field = function($number,beneficiary_row){
	$("#is_principal_beneficiary").val('displayed');
	$("#principal_beneficiary_count").text($number);
	if(typeof(beneficiary_row) !== "undefined") {
		$("#principal_beneficiary_id_"+$number).val(beneficiary_row.id);
		var selected_option = $("#principal_existing_dependent_"+$number+" option[data-full-name='"+beneficiary_row.name+"'][data-type='"+beneficiary_row.relationship+"']");
		if(typeof(selected_option) !== "undefined") {
			var principal_existing_dependent = selected_option.attr('value');
			$("#principal_existing_dependent_"+$number).val(principal_existing_dependent);
			$(".principal_beneficiary_select_"+$number).addClass('has-value');

			selected_option.attr('data-full-name',beneficiary_row.name);
			selected_option.attr('data-type',beneficiary_row.relationship);
			selected_option.attr('data-phone',beneficiary_row.cell_phone);
			selected_option.attr('data-email',beneficiary_row.email);
			selected_option.attr('data-ssn',beneficiary_row.ssn);
			selected_option.attr('data-address',beneficiary_row.address);
		}

		$("#principal_existing_dependent_"+$number).val(principal_existing_dependent).addClass('has-value');
		$("#principal_queBeneficiaryFullName_"+$number).val(beneficiary_row.name).addClass('has-value');
		$("#principal_queBeneficiaryAddress_"+$number).val(beneficiary_row.address).addClass('has-value');
		$("#principal_queBeneficiaryPhone_"+$number).val(beneficiary_row.cell_phone).addClass('has-value');
		$("#principal_queBeneficiaryEmail_"+$number).val(beneficiary_row.email).addClass('has-value');
		$("#principal_queBeneficiarySSN_"+$number).val(beneficiary_row.ssn).addClass('has-value');
		$("#principal_queBeneficiaryRelationship_"+$number).val(beneficiary_row.relationship).addClass('has-value');
		$("#principal_queBeneficiaryPercentage_"+$number).val(beneficiary_row.percentage).addClass('has-value');
		tmpPrdArr = beneficiary_row.product_ids.split(',');
		// $("#principal_product_"+$number).multipleSelect('setSelects',tmpPrdArr);
	}

	$(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
	$(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
	$(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
	$(".principal_beneficiary_select_"+$number).addClass('form-control');
	$(".principal_beneficiary_select_"+$number).selectpicker({
		container: 'body', 
		style:'btn-select',
		noneSelectedText: '',
		dropupAuto:false,
	});
}

contingent_beneficiary_field = function($number,beneficiary_row){
		$("#is_contingent_beneficiary").val('displayed');
		$("#contingent_beneficiary_count").text($number);
		if(typeof(beneficiary_row) !== "undefined") {
			$("#contingent_beneficiary_id_"+$number).val(beneficiary_row.id);
			var selected_option = $("#contingent_existing_dependent_"+$number+" option[data-full-name='"+beneficiary_row.name+"'][data-type='"+beneficiary_row.relationship+"']");
			if(typeof(selected_option) !== "undefined") {
				var contingent_existing_dependent = selected_option.attr('value');
				$("#contingent_existing_dependent_"+$number).val(contingent_existing_dependent);
				$(".contingent_beneficiary_select_"+$number).addClass('has-value');

				selected_option.attr('data-full-name',beneficiary_row.name);
				selected_option.attr('data-type',beneficiary_row.relationship);
				selected_option.attr('data-phone',beneficiary_row.cell_phone);
				selected_option.attr('data-email',beneficiary_row.email);
				selected_option.attr('data-ssn',beneficiary_row.ssn);
				selected_option.attr('data-address',beneficiary_row.address);
			}                                
			$("#contingent_queBeneficiaryFullName_"+$number).val(beneficiary_row.name).addClass('has-value');
			$("#contingent_queBeneficiaryAddress_"+$number).val(beneficiary_row.address).addClass('has-value');
			$("#contingent_queBeneficiaryPhone_"+$number).val(beneficiary_row.cell_phone).addClass('has-value');
			$("#contingent_queBeneficiaryEmail_"+$number).val(beneficiary_row.email).addClass('has-value');
			$("#contingent_queBeneficiarySSN_"+$number).val(beneficiary_row.ssn).addClass('has-value');
			$("#contingent_queBeneficiaryRelationship_"+$number).val(beneficiary_row.relationship).addClass('has-value');
			$("#contingent_queBeneficiaryPercentage_"+$number).val(beneficiary_row.percentage).addClass('has-value');
			tmpPrdArr = beneficiary_row.product_ids.split(',');
			// $("#contingent_product_"+$number).multipleSelect('setSelects',tmpPrdArr);
		}

		$(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
		$(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
		$(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
		$(".contingent_beneficiary_select_"+$number).addClass('form-control');
		$(".contingent_beneficiary_select_"+$number).selectpicker({
			container: 'body', 
			style:'btn-select',
			noneSelectedText: '',
			dropupAuto:false,
		});
	}
</script>
