<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/Api.class.php';
include_once __DIR__ . '/includes/apiUrlKey.php';

$ajaxApiCall = new Api();

$response = array();
$childData = isset($_POST['child']) ? $_POST['child'] : array();
$product_list = isset($_POST['spouse_products_list']) ? explode(",", $_POST['spouse_products_list']) : array();
$spouse_doc = !empty($_POST['coverage_spouse_verification_doc']) ? count(explode('\\',$_POST['coverage_spouse_verification_doc'])) : 0;
$spouse_doc_name_org = $spouse_doc > 0 ? explode('\\',$_POST['coverage_spouse_verification_doc'])[$spouse_doc-1] : '';
$product_plan = array();
if (!empty($product_list)) {
	foreach ($product_list as $key => $value) {
		$product_plan[$value] = 4;
	}
}

$enrolleeElementsVal = !empty($_POST['enrolleeElementsVal']) ? json_decode($_POST['enrolleeElementsVal'], true) : array();

$number = 0;
$enrollmentLocation = "groupSide";

$is_group_member = 'Y';
$is_add_product = 0;

$cd_profile_id = isset($_POST['cd_profile_id']) ? $_POST['cd_profile_id'] : 0;
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;

$customer_rep_id = isset($_POST['groupId']) ? $_POST['groupId'] : "";
$member_rep_id = isset($_POST['memberId']) ? $_POST['memberId'] : "";
$primary_lname = isset($_POST['primary_lname']) ? $_POST['primary_lname'] : "";
$spouse_zip_value = isset($_POST['primaryZipcode']) ? $_POST['primaryZipcode'] : "";

$child_assign_products = array();
$spouse_assign_products = array();
$postArray = array(
	'productList' => $product_list,
	'productPlan' => $product_plan,
	'number' => $number,
	'enrollmentLocation' => $enrollmentLocation,
	'isGroupMember' => $is_group_member,
	'cdProfileId' => $cd_profile_id,
	'orderId' => $order_id,
	'groupId' => $customer_rep_id,
	'memberId' => $member_rep_id,
	'spouseZipValue' => $spouse_zip_value,
	'isAddProduct' => $is_add_product,
	'spouseAssignProducts' => array($product_list),
	'childAssignProducts' => array($product_list),
	'api_key' => 'getSpouseField'
);

$response = $ajaxApiCall->ajaxApiCall($postArray, true);

$productRes = $response['productRes'];
$memberPlusOneProduct = $response['memberPlusOneProduct'];
$spouse_field = $response['spouseField'];
$stateRes = $response['stateRes'];
$spouse_dep_row = $response['spouseDepRow'];

if (!empty($enrolleeElementsVal['spouse_fname_1'])) {
	$spouse_fname_value = $enrolleeElementsVal['spouse_fname_1'];
} else {
	$spouse_fname_value = isset($spouse_fname_value) ? $spouse_fname_value : '';
}

if (!empty($enrolleeElementsVal['spouse_birthdate_1'])) {
	$spouse_birthdate_value = $enrolleeElementsVal['spouse_birthdate_1'];
} else {
	$spouse_birthdate_value = isset($spouse_birthdate_value) ? $spouse_birthdate_value : '';
}

if (!empty($enrolleeElementsVal['spouse_gender_1'])) {
	$spouse_gender_value = $enrolleeElementsVal['spouse_gender_1'];
} else {
	$spouse_gender_value = isset($spouse_gender_value) ? $spouse_gender_value : '';
}

$spouse_zip_value = isset($_POST['primary_zip']) ? $_POST['primary_zip'] : "";
$spouse_state_value = isset($_POST['primary_state']) ? $_POST['primary_state'] : "";
$htmlData = '';
ob_start();
?>
<div id='inner_spouse_field_<?= $number ?>' class='inner_spouse_field m-t-20'>
	<hr>
	<p><span class='font-bold'>Spouse</span> <a href='javascript:void(0);' class='btn red-link removeSpouseField' data-id='<?= $number ?>' id='removeSpouseField<?= $number ?>' data-toggle='tooltip' data-container='body' data-trigger='hover' title='Remove' data-placement='bottom'>Remove</a></p>
	<div class='row enrollment_auto_row'>
		<?php if (!empty($spouse_dep_row)) { ?>
			<div class='col-sm-4'>
				<div class='form-group'>
					<?php $cp_id = !empty($_GET['cd_profile_id']) ? $_GET['cd_profile_id'] : '0'; ?>
					<input type='hidden' name='spouse_cd_profile_id[<?= $number ?>]' id='spouse_cd_profile_id_<?= $number ?>' value='<?= $cp_id ?>'>
					<select class='form-control existing_spouse_dependent  spouse_select_<?= $number ?>' name='existing_spouse_dependent' id='existing_spouse_dependent_<?= $number ?>' data-id='<?= $number ?>'>
						<option data-hiddent='true' value=''>Existing Spouse</option>
						<?php foreach ($spouse_dep_row as $s) { ?>
							<option value='<?= $s['id'] ?>' data-fname='<?= $s['fname'] ?>' data-lname='<?= $s['lname'] ?>' data-email='<?= $s['email'] ?>' data-gender='<?= $s['gender'] ?>' data-birth_date='<?= date("m/d/Y", strtotime($s["birth_date"])) ?>' data-ssn='<?= $s['ssn'] ?>'> <?= $s['fname'] . " " . $s['lname'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<div class='col-sm-4'>
			<div class='form-group'>
				<select id='spouse_assign_products_<?= $number ?>' name='spouse_assign_products[<?= $number ?>][]' class='se_multiple_select spouse_dependent_multiple_select' multiple='multiple' data-id=<?= $number ?>>
					<?php if (!empty($productRes)) {
						foreach ($productRes as $key => $productRow) {
							$select = (!isset($dep_row['product_ids']) && !empty($product_plan) && $product_plan[$productRow['id']] != 5) || (!empty($dep_row['product_ids']) && in_array($productRow['id'], $dep_row['product_ids'])) ? 'selected' : '';
							$disable = !empty($memberPlusOneProduct) && in_array($productRow['id'], $memberPlusOneProduct) ? 'disabled' : '';
					?>
							<option value='<?= $productRow['id'] ?>' data-product-plan='<?= $product_plan[$productRow['id']] ?>' <?= $select ?> <?= $disable ?>>
								<?= $productRow['name'] ?> (<?= $productRow['product_code'] ?>)
							</option>
					<?php } } ?>
				</select>
				<label>Assign Product(s)*</label>
				<p class='error' id='error_spouse_assign_products'></p>
			</div>
		</div>
		<?php if (array_key_exists('fname', $spouse_field)) {
			$required = $spouse_field['fname']['required'] == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
			<div class='col-sm-4'>
				<div class='form-group'>
					<input type='input' class='form-control spouse_fname_<?= $number ?>' name='spouse_fname[<?= $number ?>]' id='spouse_fname_<?= $number ?>' data-id='<?= $number ?>' value='<?= $spouse_fname_value ?>'>
					<label>Spouse First Name
						<?= $required ?>
					</label>
					<p class='error' id='error_spouse_fname_<?= $number ?>'></p>
				</div>
			</div>
		<?php unset($spouse_field['fname']);
		}
		if (array_key_exists('lname', $spouse_field)) {
			$required = $spouse_field['lname']['required'] == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
			<div class='col-sm-4'>
				<div class='form-group'>
					<input type='input' class='form-control spouse_last_name spouse_lname_<?= $number ?>' name='spouse_lname[<?= $number ?>]' value='<?= $primary_lname ?>' id='spouse_lname_<?= $number ?>' data-id='<?= $number ?>'>
					<label>Spouse Last Name
						<?= $required ?>
					</label>
					<p class='error' id='error_spouse_lname_<?= $number ?>'></p>
				</div>
			</div>
		<?php unset($spouse_field['lname']);
		}
		if (array_key_exists('email', $spouse_field)) {
			$required = $spouse_field['email']['required'] == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
			<div class='col-sm-4'>
				<div class='form-group'>
					<input type='input' class='form-control no_space spouse_email_<?= $number ?>' name='spouse_email[<?= $number ?>]' id='spouse_email_<?= $number ?>' data-id='<?= $number ?>'>
					<label>Email
						<?= $required ?>
					</label>
					<p class='error' id='error_spouse_email_<?= $number ?>'></p>
				</div>
			</div>
		<?php unset($spouse_field['email']);
		}
		if (array_key_exists('SSN', $spouse_field)) { ?>
			<div class='col-sm-4'>
				<div class='form-group'>
					<input type='text' class='form-control SSN_mask' name='spouse_SSN[<?= $number ?>]' id='spouse_SSN_<?= $number ?>' data-element='ssn'>
					<label>SSN*</label>
					<p class='error' id='error_spouse_SSN_<?= $number ?>'></p>
				</div>
				<?php unset($spouse_field['SSN']); ?>
			</div>
		<?php }
		if (array_key_exists('birthdate', $spouse_field)) {
			$is_readonly = !empty($spouse_birthdate_value) ? "readonly" : ""; ?>
			<div class='col-sm-4'>
				<div class='form-group'>
					<div class='input-group'>
						<div class='input-group-addon'><i class='fa fa-calendar'></i></div>
						<div class='pr'>
							<input type='text' class='form-control dateClass date_picker spouse_birthdate_<?= $number ?>' data-id='<?= $number ?>' id='spouse_birthdate_<?= $number ?>' name='spouse_birthdate[<?= $number ?>]' data-element='birthdate' value='<?= $spouse_birthdate_value ?>' <?= $is_readonly ?>>
							<label>DOB*</label>
						</div>
					</div>
					<p class='error' id='error_spouse_birthdate_<?= $number ?>'></p>
				</div>
			</div>
		<?php unset($spouse_field['birthdate']);
		}
		if (array_key_exists('gender', $spouse_field)) {
			$spouse_gender_readonly = !empty($spouse_gender_value) ? 'readonly' : '';
			$spouse_gender_disabled = !empty($spouse_gender_value) ? 'disabled' : ''; ?>
			<div class='col-sm-4'>
				<div class='form-group'>
					<div class='btn-group btn-custom-group btn-group-justified <?= (!empty($spouse_gender_value) ? "btn-group-disabled" : "") ?>'>
						<?php
						$spouse_gender_class = !empty($spouse_gender_value) && $spouse_gender_value == "Male" ? 'active' : '';
						$spouse_gender_checked = !empty($spouse_gender_value) && $spouse_gender_value == "Male" ? 'checked' : '';
						?>

						<div class='toggle-item'>
							<input class='js-switch spouse_gender_class' name='spouse_gender[<?= $number ?>]' type='radio' value='Male' id='spouse_gender_<?= $number ?>_Male' data-id='<?= $number ?>' <?= $spouse_gender_checked ?> <?= $spouse_gender_readonly ?> />
							<label for='spouse_gender_<?= $number ?>_Male' class='btn btn-info <?= $spouse_gender_class ?>' <?= $spouse_gender_disabled ?>>Male</label>
						</div>
						<?php
						$spouse_gender_class = !empty($spouse_gender_value) && $spouse_gender_value == "Female" ? 'active' : '';
						$spouse_gender_checked = !empty($spouse_gender_value) && $spouse_gender_value == "Female" ? 'checked' : '';
						?>
						<div class='toggle-item'>
							<input class='js-switch spouse_gender_class' name='spouse_gender[<?= $number ?>]' type='radio' value='Female' id='spouse_gender_<?= $number ?>_Female' data-id='<?= $number ?>' <?= $spouse_gender_checked ?> <?= $spouse_gender_readonly ?> />
							<label for='spouse_gender_<?= $number ?>_Female' class='btn btn-info <?= $spouse_gender_class ?>' <?= $spouse_gender_disabled ?>>Female</label>
						</div>
						<input type="hidden" name="spouse_gender[<?= $number ?>]" id="hidden_spouse_gender_<?= $number ?>" value="<?=$spouse_gender_value?>">
					</div>
					<?php unset($spouse_field['gender']); ?>
					<p class='error' id='error_spouse_gender_<?= $number ?>'></p>
				</div>
			</div>
		<?php } ?>
	</div>

	<?php if (!empty($spouse_field)) {
		$spouse_benefit_arr = array('spouse_benefit_amount', 'spouse_in_patient_benefit', 'spouse_out_patient_benefit', 'spouse_monthly_income', 'spouse_benefit_percentage'); ?>
		<h5 class='m-t-15'>Additional Spouse Information</h5>
		<div class='row enrollment_auto_row'>
			<?php foreach ($spouse_field as $key => $row) {
				$prd_question_id = $row['id'];
				$is_required = $row['required'];
				$control_name = "spouse_" . $row['label'];
				$label = $row['display_label'];
				$control_type = $row['control_type'];
				$class = $row['control_class'];
				$maxlength = $row['control_maxlength'];
				$control_attribute = $row['control_attribute'];
				$questionType = $row['questionType'];

				if (in_array($row['label'], array('fname', 'lname', 'SSN', 'email', 'birthdate', 'gender'))) {
					continue;
				}

				$control_value = isset($enrolleeElementsVal[$control_name . "_1"]) ? $enrolleeElementsVal[$control_name . "_1"] : "";

				if (empty($control_value) && !empty(${$control_name . '_value'})) {
					$control_value = ${$control_name . '_value'};
				}

				if ($questionType == "Default") {
					if ($control_type == 'text' && !in_array($control_name, $spouse_benefit_arr)) {
						$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
						$is_readonly = !empty($control_value) ? "readonly" : ""; ?>
						<div class='col-sm-4'>
							<div class='form-group'>
								<input type='text' maxlength='<?= $maxlength ?>' class='form-control <?= $class ?>' required name='<?= $control_name ?>[<?= $number ?>]' id='<?= $control_name ?>_<?= $number ?>' value='<?= $control_value ?>' <?= $is_readonly ?>>
								<label>
									<?= $label . $required ?>
								</label>
								<p class='error' id='error_<?= $control_name ?>_<?= $number ?>'></p>
							</div>
						</div>
					<?php } elseif ($control_type == 'date_mask' && !in_array($control_name, $spouse_benefit_arr)) {
						$dateValue = '';
						if ($dateValue != '') {
							$dateValue = date('m/d/Y', strtotime($control_value));
						}
						$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
						<div class='col-sm-4'>
							<div class='form-group'>
								<div class='input-group'>
									<div class='input-group-addon'><i class='fa fa-calendar'></i></div>
									<div class='pr'>
										<input type='text' class='form-control date_picker dateClass dob <?= $class ?>' name='<?= $control_name ?>[<?= $number ?>]' id='<?= $control_name ?>_<?= $number ?>' value='<?= $dateValue ?>'>
										<label>
											<?= $label . $required ?>
										</label>
									</div>
								</div>
								<p class='error' id='error_<?= $control_name ?>_<?= $number ?>'></p>
							</div>
						</div>
					<?php } elseif ($control_type == 'select' && !in_array($control_name, $spouse_benefit_arr)) {
						$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
						<div class='col-sm-4'>
							<div class='form-group'>
								<?php if (!empty($control_value)) {
									$is_readonly = !empty($control_value) ? "readonly" : ""; ?>
									<input type='text' id='<?= $control_name ?>_<?= $number ?>' name='<?= $control_name ?>[<?= $number ?>]' class='form-control <?= $class ?>' value='<?= $control_value ?>' <?= $is_readonly ?>>
								<?php } else { ?>
									<select class='form-control spouse_member_field spouse_select_<?= $number ?> <?= $class ?>' name='<?= $control_name ?>[<?= $number ?>]' id='<?= $control_name ?>_<?= $number ?>' required data-live-search='true'>
										<?php if ($control_name == 'spouse_state') { ?>
											<option value=''></option>
											<?php foreach ($stateRes as $key => $value) {
												$spouse_state_selected = $value['name'] == $control_value ? "selected" : ""; ?>
												<option data-state_id='<?= $value['id'] ?>' value='<?= $value['name'] ?>' <?= $spouse_state_selected ?>>
													<?= $value['name'] ?>
												</option>
											<?php }
										} elseif (in_array($control_name, array('spouse_height'))) { ?>
											<option value=''></option>
											<?php for ($i = 1; $i <= 8; $i++) {
												for ($j = 0; $j <= 11; $j++) {
													$is_selected = $control_value == $i . '.' . $j ? "selected" : ""; ?>
													<option value='<?= $i .'.'. $j ?>' <?= $is_selected ?>>
														<?= $i ?> Ft.
														<?= $j ?> In.
													</option>
											<?php }
											}
										} elseif (in_array($control_name, array('spouse_weight'))) { ?>
											<option value=''></option>
											<?php for ($i = 1; $i <= 1000; $i++) {
												$is_selected = $control_value == $i ? "selected" : ""; ?>
												<option value='<?= $i ?>' <?= $is_selected ?>>
													<?= $i ?>
												</option>
											<?php }
										} elseif (in_array($control_name, array('spouse_no_of_children'))) { ?>
											<option value=''></option>
											<?php for ($i = 1; $i <= 15; $i++) {
												$is_selected = $control_value == $i ? "selected" : ""; ?>
												<option value='<?= $i ?>' <?= $is_selected ?>>
													<?= $i ?>
												</option>
											<?php }
										} elseif (in_array($control_name, array('spouse_pay_frequency'))) { ?>
											<option value=''></option>
											<?= $is_selected = $control_value == "Annual" ? "selected" : ""; ?>
											<option value='Annual' <?= $is_selected ?>>Annual</option>
											<?= $is_selected = $control_value == "Monthly" ? "selected" : ""; ?>
											<option value='Monthly' <?= $is_selected ?>>Monthly</option>
											<?= $is_selected = $control_value == "Semi-Monthly" ? "selected" : ""; ?>
											<option value='Semi-Monthly' <?= $is_selected ?>>Semi-Monthly</option>
											<?= $is_selected = $control_value == "Semi-Weekly" ? "selected" : ""; ?>
											<option value='Semi-Weekly' <?= $is_selected ?>>Semi-Weekly</option>
											<?= $is_selected = $control_value == "Weekly" ? "selected" : ""; ?>
											<option value='Weekly' <?= $is_selected ?>>Weekly</option>
											<?= $is_selected = $control_value == "Hourly" ? "selected" : ""; ?>
											<option value='Hourly' <?= $is_selected ?>>Hourly</option>
										<?php } elseif (in_array($control_name, array('spouse_benefit_percentage'))) { ?>
											<option value=''></option>
											<?php for ($i = 1; $i <= 100; $i++) { ?>
												<option value='<?= $i ?>'></option>
										<?php }
										} ?>
									</select>
								<?php } ?>
								<label>
									<?= $label . $required ?>
								</label>
								<p class='error' id='error_<?= $control_name ?>_<?= $number ?>'></p>
							</div>
						</div>
					<?php } elseif ($control_type == 'radio' && !in_array($control_name, $spouse_benefit_arr)) { ?>
						<?php if(in_array($control_name,array('spouse_smoking_status','spouse_tobacco_status','spouse_has_spouse','spouse_employment_status','spouse_us_citizen'))){ ?>
							<input type="hidden" name="<?=$control_name?>[<?= $number ?>]" id="hidden_<?=$control_name?>_<?= $number ?>" value="">
						<?php } ?>
						<div class='col-sm-4'>
							<div class='form-group'>
								<div class='btn-group btn-custom-group btn-group-justified'>
									<?php $is_readonly = !empty($control_value) ? "readonly" : "";
									$is_disabled = !empty($control_value) ? "disabled" : ""; ?>
									<?php if ($control_name == 'spouse_smoking_status') { ?>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_y" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='Y' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_y' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>>Smokes</label>
										</div>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_n" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='N' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_n' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>>Non Smokes</label>
										</div>
									<?php } else if ($control_name == 'spouse_tobacco_status') { ?>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_y" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='Y' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_y' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>>Tobacco</label>
										</div>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_n" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='N' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_n' class='btn btn-info <?= $is_active ?>_n' <?= $is_disabled ?>>Non Tobacco</label>
										</div>
									<?php } else if ($control_name == 'spouse_has_spouse') { ?>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_y" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='Y' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_y' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>>Spouse</label>
										</div>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_n" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='N' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_n' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>>Non Spouse</label>
										</div>
									<?php } else if ($control_name == 'spouse_employment_status') { ?>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_y" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='Y' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_y' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>>Employed</label>
										</div>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_n" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='N' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_n' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>>Unemployed</label>
										</div>
									<?php } else if ($control_name == 'spouse_us_citizen') { ?>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_y" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='Y' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_y' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>> U.S. Citizen</label>
										</div>
										<div class='toggle-item'>
											<?php $is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
											$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : ""; ?>
											<input class='js-switch spouse_member_field' id="<?= $control_name ?>_n" type='radio' name='<?= $control_name ?>[<?= $number ?>]' value='N' <?= $is_checked ?> <?= $is_readonly ?> />
											<label for='<?= $control_name ?>_n' class='btn btn-info <?= $is_active ?>' <?= $is_disabled ?>> Not U.S. Citizen</label>
										</div>
									<?php } ?>
								</div>
								<p class='error' id='error_<?= $control_name ?>_<?= $number ?>'></p>
							</div>
						</div>
					<?php }
					if (in_array($control_name, $spouse_benefit_arr)) {
					}
				} else {
					$custom_name = str_replace($prd_question_id, "", $control_name);
					$resAnswer = $ajaxApiCall->ajaxApiCall(['api_key' => 'customeQuestionAnswer', 'questionId' => $prd_question_id], true); ?>
					<div class='clearfix'></div>
					<?php if ($control_type == 'select') {
						$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
						<div class='col-sm-12'>
							<p>
								<label>
									<?= $label ?>
								</label>
							</p>
							<div class='form-group height_auto w-300 custom_question'>
								<select class='form-control spouse_select spouse_select_<?= $number ?>' name='<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]' id='<?= $control_name ?>_<?= $number ?>' required data-live-search='true' data-id='<?= $number ?>'>
									<option value=''></option>
									<?php if (!empty($resAnswer)) {
										foreach ($resAnswer as $ansKey => $ansValue) { ?>
											<option value='<?= $ansValue['answer'] ?>' data-ans-eligible='<?= $ansValue['answer_eligible'] ?>'>
												<?= $ansValue['answer'] ?>
											</option>
									<?php }
									} ?>
								</select>
								<label>
									<?= $label . $required ?>
								</label>
								<p class='error' id='error_<?= $custom_name ?>_<?= $number ?>_<?= $prd_question_id ?>'></p>
							</div>
						</div>
					<?php } elseif ($control_type == 'radio') {
						$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
						<div class='col-sm-12 m-b-25'>
							<p>
								<label>
									<?= $label . $required ?>
								</label>
							</p>
							<div class='radio-button'>
								<div class='btn-group colors  custom-question-btn' data-toggle='buttons'>
									<?php if (!empty($resAnswer)) {
										foreach ($resAnswer as $ansKey => $ansValue) { ?>
											<label class='btn btn-info'>
												<input type='radio' name='<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]' value='<?= $ansValue['answer'] ?>' data-ans-eligible='
												<?= $ansValue['answer_eligible'] ?>' class='js-switch spouse_member_field' autocomplete='false'><?= $ansValue['answer'] ?>
											</label>
									<?php }
									} ?>
								</div>
							</div>
							<p class='error' id='error_<?= $custom_name ?>_<?= $number ?>_<?= $prd_question_id ?>'></p>
						</div>
					<?php } elseif ($control_type == 'select_multiple') {
						$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
						<div class='col-sm-12'>
							<p>
								<label>
									<?= $label ?>
								</label>
							</p>
							<div class='form-group height_auto w-300 custom_question'>
								<select id='<?= $control_name ?>_<?= $number ?>' name='<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]' class='se_multiple_select spouse_multiple_select spouse_member_field spouse_multiple_select_<?= $number ?>' required multiple='multiple' data-id='<?= $number ?>'>
									<?php if (!empty($resAnswer)) {
										foreach ($resAnswer as $ansKey => $ansValue) { ?>
											<option value='<?= $ansValue['answer'] ?>' data-ans-eligible='<?= $ansValue['answer_eligible'] ?>'>
												<?= $ansValue['answer'] ?>
											</option>
									<?php }
									} ?>
								</select>
								<label>
									<?= $label . $required ?>
								</label>
								<p class='error' id='error_<?= $custom_name ?>_<?= $number ?>_<?= $prd_question_id ?>'></p>
							</div>
						</div>
					<?php } elseif ($control_type == 'textarea') {
						$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : ""; ?>
						<div class='col-sm-12 form-inline m-b-25'>
							<p>
								<label>
									<?= $label . $required ?>
								</label>
							</p>
							<textarea id='<?= $control_name ?>_<?= $number ?>' class='form-control' name='<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]' rows='3' cols='50' maxlength='300' data-id=<?= $number ?>></textarea>
							<p class='error' id='error_<?= $custom_name ?>_<?= $number ?>_<?= $prd_question_id ?>'></p>
						</div>
			<?php }
				}
			} ?>
		</div>
	<?php } ?>
	<?php /*
	<p class='m-b-20'> Verification of Dependent 
		<i class='fa fa-info-circle' data-container='body' data-toggle='popover' title='Ways to Verify Dependents' data-trigger='hover' data-placement='top' data-html='true'>
		</i>
	</p>
	<div class='row'>
		<div class='col-sm-4'>
			<div class='form-group'>
				<div class='custom_drag_control'>
					<span class='btn btn-info'>Upload</span>
					<input type='file' class='gui-file' id='spouse_verification_doc_<?= $number ?>' name='spouse_verification_doc[<?= $number ?>]' <?=!empty($spouse_doc_name_org) ? 'disabled' : ''?>>
					<input type='text' class='gui-input' placeholder='Choose File(s)' value="<?=$spouse_doc_name_org?>" size='' <?=!empty($spouse_doc_name_org) ? 'disabled' : ''?>>
					<p class='error text-left' id='error_spouse_verification_doc_<?= $number ?>'></p>
				</div>
			</div>
		</div>
	</div>
	*/ ?>
</div>
<?php
$htmlData = ob_get_clean();
$res['number'] = $number;
$res['html'] = $htmlData;
$res['status'] = 'success';
echo json_encode($res);
exit;
?>