<?php 
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();
/*****/
//Note : Please do same change on get_product_price_detail() method in includes\functions.php  if Applicable
/*****/
$response = array();
$error_display = '';
$amount_limit_error = false;
$amount_limit_error_text = "";
$product = !empty($_GET['product']) ? $_GET['product'] : '';
$plan = !empty($_POST['product_plan'][$product]) ? $_POST['product_plan'][$product] : '';
$pricing_model = !empty($_GET['pricing_model']) ? $_GET['pricing_model'] : '';
$addType = !empty($_GET['addType']) ? $_GET['addType'] : '';
$submitType = !empty($_GET['submitType']) ? $_GET['submitType'] : '';
$submitSubType = checkIsset($_GET['submitSubType']);
$enrollmentLocation = !empty($_POST['enrollmentLocation']) ? $_POST['enrollmentLocation'] : '';
$is_group_member = !empty($_POST['is_group_member']) ? $_POST['is_group_member'] : 'N';
$sponsor_id = $_POST['sponsor_id'];
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : 0;
$lead_quote_detail_id = $_POST['lead_quote_detail_id'];

$orig_pricing_model = getname('prd_main',$product,'pricing_model','id');
$prd_row = $pdo->selectOne("SELECT pricing_model,is_short_term_disablity_product,monthly_benefit_allowed,percentage_of_salary,is_gap_plus_product,is_require_out_of_pocket_maximum,is_benefit_amount_limit,minimum_benefit_amount,maximum_benefit_amount,is_set_default_out_of_pocket_maximum,default_out_of_pocket_maximum,gap_custom_recommendation_text,annual_hrm_payment,gap_home_savings_recommend_text FROM prd_main WHERE id = :id",array(":id" => $product));
$prd_matrix_id = checkIsset($_GET['matrix_id']);
$accepted = isset($_GET['accepted']) ? $_GET['accepted'] : 'N';
$adjusted_percentage = 0;
$adjusted_member_price = 0;

if($enrollmentLocation=='groupSide' || $is_group_member == "Y"){
	$coverage_period = !empty($_POST['coverage_period']) ? $_POST['coverage_period'] : '';
	$enrolle_class = !empty($_POST['hdn_enrolle_class']) ? $_POST['hdn_enrolle_class'] : '';
	$relationship_to_group = !empty($_POST['hdn_relationship_to_group']) ? $_POST['hdn_relationship_to_group'] : '';
	$relationship_date = !empty($_POST['relationship_date']) ? $_POST['relationship_date'] : '';

	$sqlCoveragePeriod="SELECT gcc.*,gc.pay_period 
		FROM group_coverage_period_offering gco 
		JOIN group_classes gc ON (gc.id=gco.class_id and gc.is_deleted='N')
		LEFT JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N')
		where gco.is_deleted='N' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id";
	$sqlCoveragePeriodWhere=array(':group_id'=>$sponsor_id,':class_id'=>$enrolle_class,':group_coverage_period_id'=>$coverage_period);
	$resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);
	if($resCovergaePeriod){
		foreach ($resCovergaePeriod as $key => $value) {
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['type']=$value['type'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['contribution']=$value['con_value'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['pay_period']=$value['pay_period'];
			$groupCoverageContributionArr['pay_period']['pay_period'] = $value['pay_period'];
		}
	}
}
$tmpPlan = 0;
if($pricing_model=="VariablePrice"){
	$tmpPlan = $plan;
}

$assignedQuestionValue=$MemberEnrollment->assignedQuestionValue($product,$tmpPlan);

$benefitAmountSetting=$MemberEnrollment->benefitAmountSetting($product);


$primary_gender = !empty($_POST['primary_gender']) ? $_POST['primary_gender'] : '';
$primary_birthdate = !empty($_POST['primary_birthdate']) ? $_POST['primary_birthdate'] : '';
$primary_zip = !empty(trim($_POST['primary_zip'])) ? trim($_POST['primary_zip']) : '';

$annual_salary = 0.0;
$salary_percentage = 0.0;
$db_monthly_benefit = "";


$variableEnrolleeOptions = array();

$shortTermProductDetails = $MemberEnrollment->shortTermDisabilityProductDetails($product);

$is_short_term_disability_product = 'N';
$monthly_benefit_allowed_db = "";
$percentage_of_salary_db = "";
$tmp_annual_salary = "";
$tmp_monthly_benefit_percentage = "";

if($shortTermProductDetails){
	$is_short_term_disability_product = $shortTermProductDetails['is_short_term_disablity_product'];
	$monthly_benefit_allowed_db = $shortTermProductDetails['monthly_benefit_allowed'];
	$percentage_of_salary_db = $shortTermProductDetails['percentage_of_salary'];

	$lead_quote_detail_sql = "SELECT primary_monthly_salary_percentage,primary_annual_salary,monthly_benefit_amount FROM lead_quote_details WHERE id = :id AND status = 'Pending' AND is_assisted_enrollment = 'Y'";
	$lead_quote_row = $pdo->selectOne($lead_quote_detail_sql, array(":id" => $lead_quote_detail_id));

	$primary_monthly_salary_percentage = !empty($lead_quote_row['primary_monthly_salary_percentage']) ? json_decode($lead_quote_row['primary_monthly_salary_percentage'], true) : '';
	$primary_annual_salary = !empty($lead_quote_row['primary_annual_salary']) ? json_decode($lead_quote_row['primary_annual_salary'], true) : '';
	$primary_monthly_benefit_amount = !empty($lead_quote_row['monthly_benefit_amount']) ? json_decode($lead_quote_row['monthly_benefit_amount'], true) : '';

	$annual_salary = $tmp_annual_salary = !empty($primary_annual_salary[$product]) ? $primary_annual_salary[$product] : 0.0;
	$salary_percentage = $tmp_monthly_benefit_percentage = !empty($primary_monthly_salary_percentage[$product]) ? $primary_monthly_salary_percentage[$product] : 0.0;
	$db_monthly_benefit = !empty($primary_monthly_benefit_amount[$product]) ? $primary_monthly_benefit_amount[$product] : '';
}

$is_gap_plus_product = 'N';
$gap_plus_details = array();
$gapCalculationRes = array();
$benefitAmountArr = array();
if($prd_row['is_gap_plus_product'] == "Y") {
	$is_gap_plus_product = 'Y';
	if($prd_row['is_benefit_amount_limit'] == "N") {
		$prd_row['minimum_benefit_amount'] = 500;
		$prd_row['maximum_benefit_amount'] = 10000;
	}
	$state_default_allowance = 1;
	if(!empty($primary_zip)) {
		$getStateCode=$pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$primary_zip));
		if(!empty($getStateCode['state_code']) && isset($STATE_TAX_RATES[$getStateCode['state_code']])) {
			$state_default_allowance = $STATE_TAX_RATES[$getStateCode['state_code']];
		}
	}
	$gap_plus_details = array(
		"is_require_out_of_pocket_maximum" => $prd_row['is_require_out_of_pocket_maximum'],
		"is_benefit_amount_limit" => $prd_row['is_benefit_amount_limit'],
		"minimum_benefit_amount" => $prd_row['minimum_benefit_amount'],
		"maximum_benefit_amount" => $prd_row['maximum_benefit_amount'],
		"minimum_benefit_amount_label" => displayAmount($prd_row['minimum_benefit_amount'],2),
		"maximum_benefit_amount_label" => displayAmount($prd_row['maximum_benefit_amount'],2),
		"is_set_default_out_of_pocket_maximum" => $prd_row['is_set_default_out_of_pocket_maximum'],
		"default_out_of_pocket_maximum" => $prd_row['default_out_of_pocket_maximum'],
		"gap_custom_recommendation_text" => $prd_row['gap_custom_recommendation_text'],
		"state_default_allowance" => sprintf('%0.3f',$state_default_allowance),
		"out_of_pocket_maximum" => 0,
	);

	$benefit_amount_res = $pdo->select("SELECT pmc.benefit_amount FROM prd_matrix_criteria pmc JOIN prd_matrix pm on(pm.id = pmc.prd_matrix_id) WHERE pm.product_id = :product_id AND pm.plan_type = :plan_type AND pmc.is_deleted = 'N'",array(":product_id" => $product,":plan_type" => $plan));
	if($benefit_amount_res){

		foreach ($benefit_amount_res as $v) {
			if($prd_row['is_benefit_amount_limit'] == 'Y'){
				if($v['benefit_amount'] >= $prd_row['minimum_benefit_amount'] && $v['benefit_amount'] <= $prd_row['maximum_benefit_amount']){
					array_push($benefitAmountArr, $v['benefit_amount']);
				}
			}else{
				array_push($benefitAmountArr, $v['benefit_amount']);
			}
			
		}
		if($benefitAmountArr){
			$benefitAmountArr = array_unique($benefitAmountArr);
			sort($benefitAmountArr);
			// $gap_plus_details['minimum_benefit_amount'] = $benefitAmountArr[0];
			// $gap_plus_details['minimum_benefit_amount_label'] = displayAmount($benefitAmountArr[0]);
			// $gap_plus_details['maximum_benefit_amount'] = end($benefitAmountArr);
			// $gap_plus_details['maximum_benefit_amount_label'] = displayAmount(end($benefitAmountArr));
		}
	}
}

if($submitType=="displayQuestion"){
	$primary_fname = !empty($_POST['primary_fname']) ? $_POST['primary_fname'] : '';
	
	$primary_email = !empty($_POST['primary_email']) ? $_POST['primary_email'] : '';

	$spouse_fname = !empty($_POST['spouse_fname']) ? $_POST['spouse_fname'] : '';
	$spouse_gender = !empty($_POST['spouse_gender']) ? $_POST['spouse_gender'] : '';
	$spouse_birthdate = !empty($_POST['spouse_birthdate']) ? $_POST['spouse_birthdate'] : '';

	$child_fname = !empty($_POST['tmp_child_fname']) ? $_POST['tmp_child_fname'] : array();
	$child_gender = !empty($_POST['tmp_child_gender']) ? $_POST['tmp_child_gender'] : array();
	$child_birthdate = !empty($_POST['tmp_child_birthdate']) ? $_POST['tmp_child_birthdate'] : array();

	$assignedQuestion=$MemberEnrollment->getPriceAssignedQuestion($product);
	

	if($pricing_model=="VariableEnrollee"){
		if($plan==1){
			if(isset($assignedQuestion['Spouse'])){
				unset($assignedQuestion['Spouse']);
			}
			if(isset($assignedQuestion['Child'])){
				unset($assignedQuestion['Child']);
			}
		}
		if($plan==2){
			if(isset($assignedQuestion['Spouse'])){
				unset($assignedQuestion['Spouse']);
			}
		}else if($plan == 3){
			if(isset($assignedQuestion['Child'])){
				unset($assignedQuestion['Child']);
			}
		}
	}

	$data = array();
	if(empty($addType) && isset($assignedQuestion['Primary'])){
		$data['primary'][1]['fname']=$primary_fname;

		if(array_key_exists(1, $assignedQuestion['Primary'])){
			$data['primary'][1]['birthdate']=$primary_birthdate;
		}
		if(array_key_exists(2, $assignedQuestion['Primary']) || array_key_exists(3, $assignedQuestion['Primary'])){
			$data['primary'][1]['zip']=$primary_zip;
		}
		if(array_key_exists(4, $assignedQuestion['Primary'])){
			$data['primary'][1]['gender']=$primary_gender;
		}

		if(array_key_exists(5, $assignedQuestion['Primary'])){
			$data['primary'][1]['smoking_status']='';
		}
		if(array_key_exists(6, $assignedQuestion['Primary'])){
			$data['primary'][1]['tobacco_status']='';
		}
		if(array_key_exists(7, $assignedQuestion['Primary'])){
			$data['primary'][1]['height']='';
		}
		if(array_key_exists(8, $assignedQuestion['Primary'])){
			$data['primary'][1]['weight']='';
		}
		if(array_key_exists(9, $assignedQuestion['Primary'])){
			$data['primary'][1]['no_of_children']='';
		}
		if(array_key_exists(10, $assignedQuestion['Primary'])){
			$data['primary'][1]['has_spouse']='';
		}
		if(array_key_exists(17, $assignedQuestion['Primary'])){
			if(!empty($assignedQuestionValue['Primary']['benefit_amount'])){
				foreach ($assignedQuestionValue['Primary']['benefit_amount'] as $key => $value) {
					if(empty($data['primary'][1]['benefit_amount']) || !in_array($value,$data['primary'][1]['benefit_amount'])){
						$data['primary'][1]['benefit_amount'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(18, $assignedQuestion['Primary'])){
			if(!empty($assignedQuestionValue['Primary']['in_patient_benefit'])){
				foreach ($assignedQuestionValue['Primary']['in_patient_benefit'] as $key => $value) {
					if(empty($data['primary'][1]['in_patient_benefit']) || !in_array($value,$data['primary'][1]['in_patient_benefit'])){
						$data['primary'][1]['in_patient_benefit'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(19, $assignedQuestion['Primary'])){
			if(!empty($assignedQuestionValue['Primary']['out_patient_benefit'])){
				foreach ($assignedQuestionValue['Primary']['out_patient_benefit'] as $key => $value) {
					if(empty($data['primary'][1]['out_patient_benefit']) || !in_array($value,$data['primary'][1]['out_patient_benefit'])){
						$data['primary'][1]['out_patient_benefit'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(20, $assignedQuestion['Primary'])){
			if(!empty($assignedQuestionValue['Primary']['monthly_income'])){
				foreach ($assignedQuestionValue['Primary']['monthly_income'] as $key => $value) {
					if(empty($data['primary'][1]['monthly_income']) || !in_array($value,$data['primary'][1]['monthly_income'])){
						$data['primary'][1]['monthly_income'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(21, $assignedQuestion['Primary'])){
			if(!empty($assignedQuestionValue['Primary']['benefit_percentage'])){
				foreach ($assignedQuestionValue['Primary']['benefit_percentage'] as $key => $value) {
					if(empty($data['primary'][1]['benefit_percentage']) || !in_array($value,$data['primary'][1]['benefit_percentage'])){
						$data['primary'][1]['benefit_percentage'][]=$value;
					}
				}
			}
		}
	}
	if(empty($addType) && isset($assignedQuestion['Spouse'])){
		$data['spouse'][1]['fname']=$spouse_fname;

		if(array_key_exists(1, $assignedQuestion['Spouse'])){
			$data['spouse'][1]['birthdate']=$spouse_birthdate;
		}
		if(array_key_exists(2, $assignedQuestion['Spouse']) || array_key_exists(3, $assignedQuestion['Spouse'])){
			$data['spouse'][1]['zip']='';
		}
		if(array_key_exists(4, $assignedQuestion['Spouse'])){
			$data['spouse'][1]['gender']=$spouse_gender;
		}
		if(array_key_exists(5,$assignedQuestion['Spouse'])){
			$data['spouse'][1]['smoking_status']='';
		}
		if(array_key_exists(6,$assignedQuestion['Spouse'])){
			$data['spouse'][1]['tobacco_status']='';
		}
		if(array_key_exists(7,$assignedQuestion['Spouse'])){
			$data['spouse'][1]['height']='';
		}
		if(array_key_exists(8,$assignedQuestion['Spouse'])){
			$data['spouse'][1]['weight']='';
		}
		if(array_key_exists(9,$assignedQuestion['Spouse'])){
			$data['spouse'][1]['no_of_children']='';
		}
		if(array_key_exists(10,$assignedQuestion['Spouse'])){
			$data['spouse'][1]['has_spouse']='';
		}
		if(array_key_exists(17,$assignedQuestion['Spouse'])){
			if(!empty($assignedQuestionValue['Spouse']['benefit_amount'])){
				foreach ($assignedQuestionValue['Spouse']['benefit_amount'] as $key => $value) {
					if(empty($data['spouse'][1]['benefit_amount']) || !in_array($value, $data['spouse'][1]['benefit_amount'])){
						$data['spouse'][1]['benefit_amount'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(18, $assignedQuestion['Spouse'])){
			if(!empty($assignedQuestionValue['Spouse']['in_patient_benefit'])){
				foreach ($assignedQuestionValue['Spouse']['in_patient_benefit'] as $key => $value) {
					if(empty($data['spouse'][1]['in_patient_benefit']) || !in_array($value,$data['spouse'][1]['in_patient_benefit'])){
						$data['spouse'][1]['in_patient_benefit'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(19, $assignedQuestion['Spouse'])){
			if(!empty($assignedQuestionValue['Spouse']['out_patient_benefit'])){
				foreach ($assignedQuestionValue['Spouse']['out_patient_benefit'] as $key => $value) {
					if(empty($data['spouse'][1]['out_patient_benefit']) || !in_array($value,$data['spouse'][1]['out_patient_benefit'])){
						$data['spouse'][1]['out_patient_benefit'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(20, $assignedQuestion['Spouse'])){
			if(!empty($assignedQuestionValue['Spouse']['monthly_income'])){
				foreach ($assignedQuestionValue['Spouse']['monthly_income'] as $key => $value) {
					if(empty($data['spouse'][1]['monthly_income']) || !in_array($value,$data['spouse'][1]['monthly_income'])){
						$data['spouse'][1]['monthly_income'][]=$value;
					}
				}
			}
		}
		if(array_key_exists(21, $assignedQuestion['Spouse'])){
			if(!empty($assignedQuestionValue['Spouse']['benefit_percentage'])){
				foreach ($assignedQuestionValue['Spouse']['benefit_percentage'] as $key => $value) {
					if(empty($data['spouse'][1]['benefit_percentage']) || !in_array($value,$data['spouse'][1]['benefit_percentage'])){
						$data['spouse'][1]['benefit_percentage'][]=$value;
					}
				}
			}
		}
	}
	if(isset($assignedQuestion['Child'])){
		$addTypeKeyArr = array();
		if(!empty($addType)){
			$addTypeDiff=explode("_", $addType);
			$addTypeKey=$addTypeDiff[1];
			array_push($addTypeKeyArr,1);
		}else{
			if(!empty($child_fname)){
				foreach ($child_fname as $key => $value) {
					array_push($addTypeKeyArr, $key);
				}
			}else{
				array_push($addTypeKeyArr,1);
			}
			
		}

		if(!empty($addTypeKeyArr)){
			foreach ($addTypeKeyArr as $key => $addTypeKey) {
				$data['child'][$addTypeKey]['fname']=!empty($child_fname[$addTypeKey]) ? $child_fname[$addTypeKey] : '';
				
				if(array_key_exists(1, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['birthdate']=!empty($child_birthdate[$addTypeKey]) ? $child_birthdate[$addTypeKey] : '';
				}
				if(array_key_exists(2, $assignedQuestion['Child']) || array_key_exists(3, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['zip']='';
				}
				if(array_key_exists(4, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['gender']=!empty($child_gender[$addTypeKey]) ? $child_gender[$addTypeKey] : '';
				}
				if(array_key_exists(5, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['smoking_status']='';
				}
				if(array_key_exists(6, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['tobacco_status']='';
				}
				if(array_key_exists(7, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['height']='';
				}
				if(array_key_exists(8, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['weight']='';
				}
				if(array_key_exists(9, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['no_of_children']='';
				}
				if(array_key_exists(10, $assignedQuestion['Child'])){
					$data['child'][$addTypeKey]['has_spouse']='';
				}
				if(array_key_exists(17, $assignedQuestion['Child'])){
					if(!empty($assignedQuestionValue['Child']['benefit_amount'])){
						foreach ($assignedQuestionValue['Child']['benefit_amount'] as $key => $value) {
							if(empty($data['child'][$addTypeKey]['benefit_amount']) || !in_array($value, $data['child'][$addTypeKey]['benefit_amount'])){
								$data['child'][$addTypeKey]['benefit_amount'][]=$value;
							}
						}
					}
				}
				if(array_key_exists(18, $assignedQuestion['Child'])){
					if(!empty($assignedQuestionValue['Child']['in_patient_benefit'])){
						foreach ($assignedQuestionValue['Child']['in_patient_benefit'] as $key => $value) {
							if(empty($data['child'][$addTypeKey]['in_patient_benefit']) || !in_array($value,$data['child'][$addTypeKey]['in_patient_benefit'])){
								$data['child'][$addTypeKey]['in_patient_benefit'][]=$value;
							}
						}
					}
				}
				if(array_key_exists(19, $assignedQuestion['Child'])){
					if(!empty($assignedQuestionValue['Child']['out_patient_benefit'])){
						foreach ($assignedQuestionValue['Child']['out_patient_benefit'] as $key => $value) {
							if(empty($data['child'][$addTypeKey]['out_patient_benefit']) || !in_array($value,$data['child'][$addTypeKey]['out_patient_benefit'])){
								$data['child'][$addTypeKey]['out_patient_benefit'][]=$value;
							}
						}
					}
				}
				if(array_key_exists(20, $assignedQuestion['Child'])){
					if(!empty($assignedQuestionValue['Child']['monthly_income'])){
						foreach ($assignedQuestionValue['Child']['monthly_income'] as $key => $value) {
							if(empty($data['child'][$addTypeKey]['monthly_income']) || !in_array($value,$data['child'][$addTypeKey]['monthly_income'])){
								$data['child'][$addTypeKey]['monthly_income'][]=$value;
							}
						}
					}
				}
				if(array_key_exists(21, $assignedQuestion['Child'])){
					if(!empty($assignedQuestionValue['Child']['benefit_percentage'])){
						foreach ($assignedQuestionValue['Child']['benefit_percentage'] as $key => $value) {
							if(empty($data['child'][$addTypeKey]['benefit_percentage']) || !in_array($value,$data['child'][$addTypeKey]['benefit_percentage'])){
								$data['child'][$addTypeKey]['benefit_percentage'][]=$value;
							}
						}
					}
				}
			}
		}
	}

	if($is_short_term_disability_product == 'Y'){
		$data['primary'][1]['annual_salary'] = $tmp_annual_salary;
		$data['primary'][1]['monthly_benefit_percentage'] = $tmp_monthly_benefit_percentage;
	}

	if($is_gap_plus_product == 'Y'){
		$data['primary'][1]['gap_plus_inputs']='';
		$gap_available_benefit_amount = array();
		if(!empty($data['primary'][1]['benefit_amount'])) {
			foreach($data['primary'][1]['benefit_amount'] as $amount) {
				if($amount >= $gap_plus_details['minimum_benefit_amount'] && $amount <= $gap_plus_details['maximum_benefit_amount']) {
					$gap_available_benefit_amount[] = $amount;
				}
			}
		}
		$gap_plus_details['available_benefit_amount'] = $gap_available_benefit_amount;
		if(isset($data['primary'][1]['benefit_amount'])) {
			unset($data['primary'][1]['benefit_amount']);
		}
		if(!empty($customer_id)){
			$out_of_pocket_max_res = $pdo->selectOne("SELECT out_of_pocket_maximum FROM customer_benefit_amount WHERE customer_id = :customer_id AND is_deleted = 'N'",array(":customer_id" => $customer_id));
			if($out_of_pocket_max_res){
				$gap_plus_details['out_of_pocket_maximum']= $out_of_pocket_max_res['out_of_pocket_maximum'];
				if($prd_row['is_require_out_of_pocket_maximum'] == 'Y'){
					$gap_plus_details['default_out_of_pocket_maximum']= $out_of_pocket_max_res['out_of_pocket_maximum'];
					$gap_plus_details['maximum_benefit_amount']= $out_of_pocket_max_res['out_of_pocket_maximum'];
					$gap_plus_details['maximum_benefit_amount_label']= displayAmount($out_of_pocket_max_res['out_of_pocket_maximum'],2);
					if($benefitAmountArr){
						foreach ($benefitAmountArr as $k => $v) {
							if($v > $out_of_pocket_max_res['out_of_pocket_maximum']){
								unset($benefitAmountArr[$k]);
							}
						}
					}
				}
			}
		}

	}

	$response['gap_plus_details'] = $gap_plus_details;
	$response['is_gap_plus_product'] = $is_gap_plus_product;
	$response['is_short_term_disability_product'] = $is_short_term_disability_product;
	$response['data'] = $data;
}else{
	if($pricing_model == "VariablePrice"){
		$assignedQuestionValue=$MemberEnrollment->assignedQuestionValue($product,$plan);
	}
	if($pricing_model=="VariableEnrollee"){
		$variableEnrolleeOptions=$MemberEnrollment->variableEnrolleeOptions($product);
	}
	$assignedQuestion = $MemberEnrollment->getPriceAssignedQuestion($product);
	$enrollee = array();
	if(!empty($_POST['hidden_primary'][$product])){
		$enrollee['Primary'] =  $_POST['hidden_primary'][$product];
	}
	if(!empty($_POST['hidden_spouse'][$product])){
		$enrollee['Spouse'] =  $_POST['hidden_spouse'][$product];
	}
	if(!empty($_POST['hidden_child'][$product])){
		$enrollee['Child'] =  $_POST['hidden_child'][$product];
	}
	$productDetails  = array();
	$largestChild = array();
	$error_check = false;

	if($prd_row['is_gap_plus_product'] == "Y") {
		$gap_benefit_amount = checkIsset($enrollee['Primary']['1']['benefit_amount']);
		$gap_benefit_amount = str_replace(array('$',''), array('',''), $gap_benefit_amount);
		if(empty($gap_benefit_amount)){
			$error_display .= "<br> Please select Benefit amount";
			$is_rule_valid = false;
		}

		if($prd_row['is_require_out_of_pocket_maximum'] == "Y") {
			$out_of_pocket_maximum = checkIsset($_POST['out_of_pocket_maximum_primary_'.$product]);
			$out_of_pocket_maximum = str_replace(array('$',''), array('',''), $out_of_pocket_maximum);
			if(!($out_of_pocket_maximum > 0)){
				$error_display .= "<br> Please enter Out of Pocket Maximum";
				$is_rule_valid = false;
			} else {
				if(!empty($out_of_pocket_maximum) && !empty($gap_benefit_amount) && $gap_benefit_amount > $out_of_pocket_maximum) {
					$error_display .= "<br> Benefit amount cannot be greater than Out of Pocket Maximum";
					$is_rule_valid = false;	
				}
			}
		}

		if($submitSubType == 'calculateGapRate'){
			$payroll_type = checkIsset($_POST['gap_payroll_type_primary_'.$product]);
			if(empty($payroll_type)){
				$error_display .= "<br> Please select Payroll type";
				$is_rule_valid = false;
			}
			$payroll_type_hourly_wage = checkIsset($_POST['gap_payroll_type_hourly_wage_primary_'.$product],'arr');
			$payroll_type_hours = checkIsset($_POST['gap_payroll_type_hours_primary_'.$product],'arr');
			if($payroll_type == 'Hourly') {
				if(!empty($payroll_type_hourly_wage)) {
					foreach($payroll_type_hourly_wage as $hourly_key => $hourly_wage) {
						if(empty(str_replace(array('$',''), array('',''), $payroll_type_hourly_wage[$hourly_key]))){
							$error_display .= "<br> Please enter Hourly Wage and Hours for all rates";
							$is_rule_valid = false;
							break;
						}						
						if(empty($payroll_type_hours[$hourly_key])){
							$error_display .= "<br> Please enter Hourly Wage and Hours for all rates";
							$is_rule_valid = false;
							break;
						}
					}
				} else {
					if(empty(str_replace(array('$',''), array('',''), $payroll_type_hourly_wage))){
						$error_display .= "<br> Please enter Hourly Wage";
						$is_rule_valid = false;
					}					
					if(empty($payroll_type_hours)){
						$error_display .= "<br> Please enter Hours";
						$is_rule_valid = false;
					}
				}
			}

			$payroll_type_salary = checkIsset($_POST['gap_payroll_type_salary_primary_'.$product]);
			if($payroll_type == 'Salary' && empty(str_replace(array('$',''), array('',''), $payroll_type_salary))){
				$error_display .= "<br> Please enter Salary";
				$is_rule_valid = false;
			}

			$marital_status = checkIsset($_POST['gap_marital_status_primary_'.$product]);
			if(empty($marital_status)){
				$error_display .= "<br> Please select Marital status";
				$is_rule_valid = false;
			}
			$pay_frequency = checkIsset($_POST['gap_pay_frequency_primary_'.$product]);
			if(empty($pay_frequency)){
				$error_display .= "<br> Please select Pay frequency";
				$is_rule_valid = false;
			}
		}
	}

	if(!empty($enrollee)){
		foreach ($enrollee as $enrolleeType => $enrolleeArr) {
			$valid_rule_id = array();

			if($is_short_term_disability_product == 'Y'){
				if($enrolleeType == 'Primary'){
					if($enrolleeArr['annual_salary'] == 0 || empty($enrolleeArr['annual_salary'])){
						$error_display .= "<br> Please enter annual salary";
						$is_rule_valid = false;
					}else{
						$annual_salary = $enrolleeArr['annual_salary'];
					}
					if(empty($enrolleeArr['monthly_benefit_percentage'])){
						$error_display .= " <br> Please select annual salary percentage";
						$is_rule_valid = false;
					}else{
						$salary_percentage = $enrolleeArr['monthly_benefit_percentage'];
					}
				}
			}
			if(isset($enrolleeArr['annual_salary'])){
				unset($enrolleeArr['annual_salary']);
			}
			if(isset($enrolleeArr['monthly_benefit_percentage'])){
				unset($enrolleeArr['monthly_benefit_percentage']);
			}


			if(isset($assignedQuestionValue[$enrolleeType]['id'])){
				foreach ($assignedQuestionValue[$enrolleeType]['id'] as $key => $value) {
					
					if(!empty($enrolleeArr)){
						foreach ($enrolleeArr as $fieldKey => $fieldName) {
							$is_rule_valid=true;
							
							if(isset($fieldName["gender"])){
								$criteriaGender = $assignedQuestionValue[$enrolleeType]['gender'][$key];
								if($criteriaGender!='' && $fieldName["gender"] !='' && $fieldName["gender"] != $criteriaGender){
									$is_rule_valid = false;
									if($error_check){
										echo "1 : ".$enrolleeType." : ".$key."</br>";
									}
								}
							}
							if(isset($fieldName["birthdate"])){
								$age_from_birthdate=calculateAge($fieldName["birthdate"]);
								$criteriaAgeFrom = $assignedQuestionValue[$enrolleeType]['age_from'][$key];
								$criteriaAgeTo = $assignedQuestionValue[$enrolleeType]['age_to'][$key];
								if($criteriaAgeFrom>=0 &&  $criteriaAgeTo>0 && ($criteriaAgeFrom > $age_from_birthdate || $criteriaAgeTo < $age_from_birthdate)){
									$is_rule_valid = false;
									if($error_check){
										echo "2 : ".$enrolleeType." : ".$key."</br>";
									}
								}
								if($enrolleeType == 'Child'){
									if(empty($largestChild)){
										$largestChild['age'] = $age_from_birthdate;
										$largestChild['id'] = $fieldKey;
									}else{
										if($age_from_birthdate > $largestChild['age']){
											$largestChild['age'] = $age_from_birthdate;
											$largestChild['id'] = $fieldKey;
										}
									}
								}
							}else{
								if($enrolleeType == 'Child' && empty($largestChild)){
									$largestChild['age'] = 0;
									$largestChild['id'] = $fieldKey;
								}
								
							}
							if(isset($fieldName["zip"])){
								$criteriaZip = $assignedQuestionValue[$enrolleeType]['zipcode'][$key];
								if($criteriaZip != '' && $fieldName["zip"] != $criteriaZip){
									$is_rule_valid = false;
									if($error_check){
										echo "3 : ".$enrolleeType." : ".$key."</br>";
									}
								}
								$criteriaStateName = $assignedQuestionValue[$enrolleeType]['state'][$key];
								if(!empty($criteriaStateName)){
									$getStateCode=$pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$fieldName["zip"]));
									$pricing_control_State = '';
									if($getStateCode){
										$pricing_control_State = getname("states_c",$getStateCode['state_code'],"name","short_name");
										if($criteriaStateName != $pricing_control_State){
											$is_rule_valid = false;
											if($error_check){
												echo "4 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}

									
									$restricted_state_date = date('Y-m-d');

									$restrictedStateSql="SELECT GROUP_CONCAT(distinct product_id) as restrictedStateProduct FROM prd_no_sale_states WHERE state_name=:state AND is_deleted='N' AND effective_date <= :restricted_state_date AND (termination_date >= :restricted_state_date OR termination_date IS NULL) AND product_id = :product_id";
									$restrictedStateRes=$pdo->selectOne($restrictedStateSql,array(":state"=>$pricing_control_State,":restricted_state_date"=>$restricted_state_date,':product_id' => $product));
									
									if(!empty($restrictedStateRes['restrictedStateProduct'])){
										$restrictedStateArray = explode(",", $restrictedStateRes['restrictedStateProduct']);

										if(in_array($product,$restrictedStateArray)){
											$is_rule_valid = false;
											if($error_check){
												echo "5 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}
								}
							}
							if(isset($fieldName["smoking_status"])){
								$criteriaSmoking = $assignedQuestionValue[$enrolleeType]['smoking_status'][$key];
								if($criteriaSmoking != '' && $fieldName["smoking_status"] != $criteriaSmoking){
									$is_rule_valid = false;
									if($error_check){
										echo "6 : ".$enrolleeType." : ".$key."</br>";
									}
								}
							}
							if(isset($fieldName["tobacco_status"])){
								$criteriaTobacco = $assignedQuestionValue[$enrolleeType]['tobacco_status'][$key];
								if($criteriaTobacco !='' && $fieldName["tobacco_status"] != $criteriaTobacco){
									$is_rule_valid = false;
									if($error_check){
										echo "7 : ".$enrolleeType." : ".$key."</br>";
									}
								}
							}
							if(isset($fieldName["height"])){
								$height=$fieldName["height"];
								
								$heightBy=$assignedQuestionValue[$enrolleeType]['height_by'][$key];
								$criteriaHeight = $assignedQuestionValue[$enrolleeType]['height_feet'][$key].".".$assignedQuestionValue[$enrolleeType]['height_inch'][$key];
								$criteriaHeightTo = $assignedQuestionValue[$enrolleeType]['height_feet_to'][$key].".".$assignedQuestionValue[$enrolleeType]['height_inch_to'][$key];
								if($criteriaHeight !=0 || $criteriaHeightTo!=0 ){
									if($heightBy=="Exactly"){
										if($criteriaHeight!='' && $height != $criteriaHeight){
											$is_rule_valid = false;
											if($error_check){
												echo "8 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($heightBy=="Less Than"){
										if($criteriaHeight!='' && $height >= $criteriaHeight){
											$is_rule_valid = false;
											if($error_check){
												echo "9 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($heightBy=="Greater Than"){
										if($criteriaHeight!='' && $height <= $criteriaHeight){
											$is_rule_valid = false;
											if($error_check){
												echo "10 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($heightBy=="Range"){
										if($criteriaHeight!='' && $criteriaHeightTo!='' && ($criteriaHeight > $height || $criteriaHeightTo < $height)){
											$is_rule_valid = false;
											if($error_check){
												echo "11 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}
								}
							}
							if(isset($fieldName["weight"])){
								$weight=$fieldName["weight"];
								
								$weightBy=$assignedQuestionValue[$enrolleeType]['weight_by'][$key];
								$criteriaWeight = $assignedQuestionValue[$enrolleeType]['weight'][$key];
								$criteriaWeightTo = $assignedQuestionValue[$enrolleeType]['weight_to'][$key];

								if($criteriaWeight !=0 || $criteriaWeightTo!=0 ){
									if($weightBy=="Exactly"){
										if($criteriaWeight!='' && $weight != $criteriaWeight){
											$is_rule_valid = false;
											if($error_check){
												echo "12 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($weightBy=="Less Than"){
										if($criteriaWeight!='' && $weight >= $criteriaWeight){
											$is_rule_valid = false;
											if($error_check){
												echo "13 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($weightBy=="Greater Than"){
										if($criteriaWeight!='' && $weight <= $criteriaWeight){
											$is_rule_valid = false;
											if($error_check){
												echo "14 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($weightBy=="Range"){
										if($criteriaWeight!='' && $criteriaWeightTo!='' && ($criteriaWeight > $weight || $criteriaWeightTo < $weight)){
											$is_rule_valid = false;
											if($error_check){
												echo "15 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}
								}
							}
							if(isset($fieldName["no_of_children"])){
								$no_of_children=$fieldName["no_of_children"];
								
								$noOfChildrenBy=$assignedQuestionValue[$enrolleeType]['no_of_children_by'][$key];
								$criteriaNoOfChildren = $assignedQuestionValue[$enrolleeType]['no_of_children'][$key];
								$criteriaNoOfChildrenTo = $assignedQuestionValue[$enrolleeType]['no_of_children_to'][$key];
								if($criteriaNoOfChildren !=0 || $criteriaNoOfChildrenTo!=0 ){
									if($noOfChildrenBy=="Exactly"){
										if($criteriaNoOfChildren!='' && $no_of_children != $criteriaNoOfChildren){
											$is_rule_valid = false;
											if($error_check){
												echo "16 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($noOfChildrenBy=="Less Than"){
										if($criteriaNoOfChildren!='' && $no_of_children >= $criteriaNoOfChildren){
											$is_rule_valid = false;
											if($error_check){
												echo "17 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($noOfChildrenBy=="Greater Than"){
										if($criteriaNoOfChildren!='' && $no_of_children <= $criteriaNoOfChildren){
											$is_rule_valid = false;
											if($error_check){
												echo "18 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}else if($noOfChildrenBy=="Range"){
										if($criteriaNoOfChildren!='' && $criteriaNoOfChildrenTo!='' && ($criteriaNoOfChildren > $no_of_children || $criteriaNoOfChildrenTo < $no_of_children)){
											$is_rule_valid = false;
											if($error_check){
												echo "19 : ".$enrolleeType." : ".$key."</br>";
											}
										}
									}
								}
							}
							if(isset($fieldName["has_spouse"])){
								$criteriaHasSpouse = $assignedQuestionValue[$enrolleeType]['has_spouse'][$key];
								if($criteriaHasSpouse!='' && $fieldName["has_spouse"] != $criteriaHasSpouse){
									$is_rule_valid = false;
									if($error_check){
										echo "20 : ".$enrolleeType." : ".$key."</br>";
									}
								}
							}
							if(isset($fieldName["benefit_amount"]) && array_key_exists(17, $assignedQuestion[$enrolleeType])){
								$criteriaBenefit = $assignedQuestionValue[$enrolleeType]['benefit_amount'][$key];
								if($criteriaBenefit!='0.00' && $fieldName["benefit_amount"] != $criteriaBenefit){
									$is_rule_valid = false;
									if($error_check){
										echo "21 : ".$enrolleeType." : ".$key."</br>";
									}
								}

								if(!empty($benefitAmountSetting) ){
									if(isset($enrollee['Primary']) && isset($enrollee['Spouse'])){
										if($benefitAmountSetting['is_spouse_issue_amount_larger']=='N' && $enrollee['Spouse']['1']['benefit_amount'] > $enrollee['Primary']['1']['benefit_amount']){
											$is_rule_valid = false;
											if($error_check){
												echo "22 : ".$enrolleeType." : ".$key."</br>";
											}
											$error_display = 'Spouse issue amount can not be larger than primary';
										}
									}
									
									
									if($enrolleeType == 'Primary' && !empty($benefitAmountSetting['primary_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['primary_issue_amount']){

										$is_rule_valid = false;
										if($error_check){
												echo "23 : ".$enrolleeType." : ".$key."</br>";
										}
										$error_display = 'Guarantee Issue amount for Primary is $'.$benefitAmountSetting['primary_issue_amount'].', please select this benefit level';
										
									}
									
									if($enrolleeType == 'Spouse' && !empty($benefitAmountSetting['spouse_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['spouse_issue_amount']){
										$is_rule_valid = false;
										if($error_check){
												echo "24 : ".$enrolleeType." : ".$key."</br>";
										}
										$error_display = 'Guarantee Issue amount for Spouse is $'.$benefitAmountSetting['spouse_issue_amount'].', please select this benefit level';
										
									}
									if($enrolleeType == 'Child' && !empty($benefitAmountSetting['child_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['child_issue_amount']){
										$is_rule_valid = false;
										if($error_check){
												echo "25 : ".$enrolleeType." : ".$key."</br>";
										}
										$error_display = 'Guarantee Issue amount for Child(ren) is $'.$benefitAmountSetting['child_issue_amount'].', please select this benefit level';
										
									}
								}
							}
							if(isset($fieldName["in_patient_benefit"]) && array_key_exists(18, $assignedQuestion[$enrolleeType])){
								$criteriaInPatientBenefit = $assignedQuestionValue[$enrolleeType]['in_patient_benefit'][$key];
								if($criteriaInPatientBenefit !='0.00' && $fieldName["in_patient_benefit"] != $criteriaInPatientBenefit){
									$is_rule_valid = false;
									if($error_check){
										echo "26 : ".$enrolleeType." : ".$key."</br>";
									}
								}
							}
							if(isset($fieldName["out_patient_benefit"]) && array_key_exists(19, $assignedQuestion[$enrolleeType])){
								$criteriaOutPatientBenefit = $assignedQuestionValue[$enrolleeType]['out_patient_benefit'][$key];
								if($criteriaOutPatientBenefit !='0.00' && $fieldName["out_patient_benefit"] != $criteriaOutPatientBenefit){
									$is_rule_valid = false;
									if($error_check){
										echo "27 : ".$enrolleeType." : ".$key."</br>";
									}
								}
							}
							if(isset($fieldName["monthly_income"]) && array_key_exists(20, $assignedQuestion[$enrolleeType])){
								$criteriaMonthlyIncome = $assignedQuestionValue[$enrolleeType]['monthly_income'][$key];
								if($criteriaMonthlyIncome !='0.00' && $fieldName["monthly_income"] != $criteriaMonthlyIncome){
									$is_rule_valid = false;
									if($error_check){
										echo "28 : ".$enrolleeType." : ".$key."</br>";
									}
								}
							}
							// if(isset($fieldName["benefit_percentage"])){
							// 	$criteriaBenefitPecentage = $assignedQuestionValue[$enrolleeType]['benefit_percentage'][$key];
							// 	if($criteriaBenefitPecentage !='0.00' && $fieldName["benefit_percentage"] != $criteriaBenefitPecentage){
							// 		$is_rule_valid = false;
							// 		if($error_check){
							// 			echo "29 : ".$enrolleeType." : ".$key."</br>";
							// 		}
							// 	}
							// }
							
							if($is_rule_valid){
								if(!empty($valid_rule_id[$fieldKey])){
									$prevID = $valid_rule_id[$fieldKey];
									$newID  = $key;

									if($assignedQuestionValue[$enrolleeType]['price'][$newID] > $assignedQuestionValue[$enrolleeType]['price'][$prevID]){
										$valid_rule_id[$fieldKey]=$key;
									}

								}else{
									$valid_rule_id[$fieldKey]=$key;
								}
							}
						}
					}
				}
			}
			
			if(!empty($valid_rule_id)){
				foreach ($valid_rule_id as $fieldKey => $value) {

					if($enrolleeType=='Child' && !empty($variableEnrolleeOptions) && $variableEnrolleeOptions['child_dependent_rate_calculation']=='Single Rate based on Eldest Child'){
						if(!empty($largestChild) && $fieldKey == $largestChild['id']){
							$productDetails[$enrolleeType][$fieldKey]['matrix_id']=$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value];
					
							if(isset($groupCoverageContributionArr) && $groupCoverageContributionArr){
								$tmp_contribution_value = isset($groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]]) ? $groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]] : null;
								if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){

 									$tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];

									$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($assignedQuestionValue[$enrolleeType]['price'][$value],$tmp_group_coverage_contribution,false);
									$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
									$productDetails[$enrolleeType][$fieldKey]['member_price']=$calculatedPrice['member_price'];
									$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$calculatedPrice['display_member_price'];
									$productDetails[$enrolleeType][$fieldKey]['group_price']=$calculatedPrice['group_price'];
									$productDetails[$enrolleeType][$fieldKey]['display_group_price']=$calculatedPrice['display_group_price'];
								}else{
									$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
									$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
									$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
									$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
									$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
									
								}
							}else{
								$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
								$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
								$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
								$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
								$productDetails[$enrolleeType][$fieldKey]['member_price']=0;

							}
						}
					}else{
						$productDetails[$enrolleeType][$fieldKey]['matrix_id']=$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value];
					
						if(isset($groupCoverageContributionArr) && $groupCoverageContributionArr){
							$tmp_contribution_value = isset($groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]]) ? $groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]] : null;
							if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
								$tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'] ;
								$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($assignedQuestionValue[$enrolleeType]['price'][$value],$tmp_group_coverage_contribution,false);
								$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
								
								$productDetails[$enrolleeType][$fieldKey]['member_price']=$calculatedPrice['member_price'];
								$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$calculatedPrice['display_member_price'];
								$productDetails[$enrolleeType][$fieldKey]['group_price']=$calculatedPrice['group_price'];
								$productDetails[$enrolleeType][$fieldKey]['display_group_price']=$calculatedPrice['display_group_price'];
							}else{
								$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
								$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
								$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
								$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
								$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
								
							}
						}else{
							$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
							$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
							$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
							$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
							$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
							if($is_short_term_disability_product == 'Y'){
								$monthly_benefit_amount = 0;
								if(isset($_POST['monthly_benefit_amount_'. $product])){
									if($_POST['monthly_benefit_amount_'. $product] > 0){
										$monthly_benefit_amount = $_POST['monthly_benefit_amount_'. $product];
									}
								}
								$rate_details = $MemberEnrollment->calculateSTDRate($assignedQuestionValue[$enrolleeType]['price'][$value],$annual_salary,$salary_percentage,$accepted,0,$monthly_benefit_amount);

								$adjusted_percentage = $MemberEnrollment->calculateSTDPercentage($annual_salary,$monthly_benefit_allowed_db);

								$adjusted_rate_details = $MemberEnrollment->calculateSTDRate($assignedQuestionValue[$enrolleeType]['price'][$value],$annual_salary,$adjusted_percentage,$accepted);
								$adjusted_member_price = $adjusted_rate_details['rate'];


								$rate = $rate_details['rate'];
								$monthly_benefit = $rate_details['monthly_benefit'];

								$productDetails[$enrolleeType][$fieldKey]['display_member_price'] = $rate;
								$productDetails[$enrolleeType][$fieldKey]['price'] = $rate;
								$productDetails[$enrolleeType][$fieldKey]['monthly_benefit'] = $monthly_benefit;

								if(($monthly_benefit_allowed_db < $monthly_benefit) && $accepted == 'N'){
									$error_display .= " <br> Maximum monthly benefit amount limit is exceed";
									$amount_limit_error = true;
									$amount_limit_error_text = 'The maximum monthly benefit is $' . $monthly_benefit_allowed_db .'. To accept maximum amount click button below.' ;
									$adjusted_percentage = $MemberEnrollment->calculateSTDPercentage($annual_salary,$monthly_benefit_allowed_db);
								}

							}
						}
					}
					
				}
				
			}else{
				if($orig_pricing_model == 'FixedPrice' && $is_short_term_disability_product == 'Y'){
					if($prd_matrix_id){
						$price = getname('prd_matrix',$prd_matrix_id,'price','id');
						if($price){

							$monthly_benefit_amount = 0;
							if(isset($_POST['monthly_benefit_amount_'. $product])){
								if($_POST['monthly_benefit_amount_'. $product] > 0){
									$monthly_benefit_amount = $_POST['monthly_benefit_amount_'. $product];
								}
							}

							$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$salary_percentage,$accepted,0,$monthly_benefit_amount);
							$adjusted_percentage = $MemberEnrollment->calculateSTDPercentage($annual_salary,$monthly_benefit_allowed_db);
							$adjusted_rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted);
							$adjusted_member_price = $adjusted_rate_details['rate'];

							$rate = $rate_details['rate'];
							$monthly_benefit = $rate_details['monthly_benefit'];

							$productDetails['Primary'][1]['display_member_price'] = $rate;
							$productDetails['Primary'][1]['price'] = $rate;
							$productDetails['Primary'][1]['matrix_id'] = $prd_matrix_id;
							$productDetails['Primary'][1]['group_price']=0;
							$productDetails['Primary'][1]['display_group_price']=0;
							$productDetails['Primary'][1]['member_price']=0;
							$productDetails['Primary'][1]['monthly_benefit']=$monthly_benefit;
							if(($monthly_benefit_allowed_db < $monthly_benefit) && $accepted == 'N'){
								$error_display .= " <br> Maximum monthly benefit amount limit is exceed";
								$amount_limit_error = true;
								$amount_limit_error_text = 'The maximum monthly benefit is $' . $monthly_benefit_allowed_db .'. To accept maximum amount click button below.' ;
							}
						}
					}
				} elseif($orig_pricing_model == 'FixedPrice' && $is_gap_plus_product == 'Y'){
					if($prd_matrix_id){
						$product_price = getname('prd_matrix',$prd_matrix_id,'price','id');
						if($product_price) {
							$productDetails['Primary'][1]['matrix_id'] = $prd_matrix_id;
							$tmp_contribution_value = isset($groupCoverageContributionArr[$product][$prd_matrix_id]) ? $groupCoverageContributionArr[$product][$prd_matrix_id] : null;
							if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
								$tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'] ;
								$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($product_price,$tmp_group_coverage_contribution,false);
								$productDetails['Primary'][1]['price'] = $product_price;
								$productDetails['Primary'][1]['member_price']=$calculatedPrice['member_price'];
								$productDetails['Primary'][1]['display_member_price'] = $calculatedPrice['display_member_price'];
								$productDetails['Primary'][1]['group_price']=$calculatedPrice['group_price'];
								$productDetails['Primary'][1]['display_group_price']=$calculatedPrice['display_group_price'];
							}else{
								$productDetails['Primary'][1]['display_member_price'] = $product_price;
								$productDetails['Primary'][1]['price'] = $product_price;
								$productDetails['Primary'][1]['group_price']=0;
								$productDetails['Primary'][1]['display_group_price']=0;
								$productDetails['Primary'][1]['member_price']=0;
							}
						}
					}
				}
			}
		}
	}
	if(!empty($enrollee)){
		foreach ($enrollee as $enrolleeType => $enrolleeArr) {
			if(!isset($productDetails[$enrolleeType])) {
				if($orig_pricing_model=='FixedPrice' && $is_short_term_disability_product == 'Y') {

				} elseif($orig_pricing_model=='FixedPrice' && $is_gap_plus_product == 'Y') {

				} else {
					$productDetails = array();
				}
			}
		}
	}

	if($prd_row['is_gap_plus_product'] == "Y" && $submitSubType == "calculateGapRate" && empty($error_display)){

		$tmp_price = 0;
		if(!empty($productDetails)){
			if(isset($productDetails['Primary'])){
				foreach ($productDetails['Primary'] as $key => $value) {
					$tmp_price = $value['display_member_price'];
					break;
				}
			}
		}

		// $member_pay_frequency = "";
		// if(isset($_POST['hdn_enrolle_class'])){
		// 	$selSql="SELECT pay_period
		// 			FROM group_classes
		// 		  	WHERE id=:class_id and is_deleted='N'";
		// 	$selRes=$pdo->selectOne($selSql,array(":class_id"=>$_POST['hdn_enrolle_class']));
		// 	if($selRes){
		// 		$member_pay_frequency = $selRes['pay_period'];
		// 	}
		// }

		$extra_params = array();
		$extra_params['plan_type'] = $plan;
		$extra_params['member_pay_frequency'] = $_POST['gap_pay_frequency_primary_' . $product];
		$extra_params['annual_hrm_payment'] = json_decode($prd_row['annual_hrm_payment'],true);
		$extra_params['with_gap_premium'] = $tmp_price;
		$gapCalculationRes = $MemberEnrollment->calculateTakeHomePay($_POST,$product,$extra_params);
		$response['savings_recommend_text'] = $prd_row['gap_home_savings_recommend_text'];
		if($prd_row['gap_home_savings_recommend_text'] == "custom_recommendation") {
			$response['custom_savings_recommend_text'] = $prd_row['gap_custom_recommendation_text'];
		} else {
			$product_res = $MemberEnrollment->getMinMaxCostlyProduct($prd_row['gap_home_savings_recommend_text'],$_POST['product_matrix'],$prd_row['gap_custom_recommendation_text']);
			if($product_res){
				$response['saving_details'] = $product_res;
			}
		}
	}

	if(!empty($productDetails) && empty($error_display)){
		$response['enrollee']=$productDetails;
		$response['monthly_benefit_allowed_percentage'] = $percentage_of_salary_db;
	}
}

$response['adjusted_percentage'] = $adjusted_percentage;
$response['monthly_benefit_allowed'] = $monthly_benefit_allowed_db;
$response['annual_salary'] = $annual_salary;
$response['salary_percentage'] = $salary_percentage;
$response['db_monthly_benefit'] = $db_monthly_benefit;
$response['adjusted_member_price'] = $adjusted_member_price;
$response['gapCalculationRes'] = $gapCalculationRes;
$response['groupCoverageContributionArr'] = (isset($groupCoverageContributionArr)?$groupCoverageContributionArr:'');
$response['submitSubType'] = $submitSubType;
$response['is_gap_plus_product'] = $is_gap_plus_product;
$response['benefitAmountArr'] = json_encode($benefitAmountArr);

if(!empty($error_display)){
	$response['error_display'] = $error_display;
	$response['amount_limit_error'] = $amount_limit_error;
	$response['amount_limit_error_text'] = $amount_limit_error_text;
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
