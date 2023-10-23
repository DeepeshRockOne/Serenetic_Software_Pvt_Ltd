<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/group_member_enrollment.class.php';
include_once __DIR__ . '/includes/Api.class.php';
include_once __DIR__ . '/includes/apiUrlKey.php';
$groupMemberEnrollment = new groupMemberEnrollment();
$ajaxApiCall = new Api();
$body_class ="group-enroll";
$step = !empty($_GET['step']) ? $_GET['step'] : "";
$member_id = !empty($_GET['member']) ? $_GET['member'] : "";
$group_id = !empty($_GET['group']) ? $_GET['group'] : "";
$quote_id = !empty($_GET['quote']) ? $_GET['quote'] : "";
$lead_id  = 0;
if(empty($group_id)){
	setNotifyError("Restricted Link: Contact Representative");
	redirect($GROUP_HOST . "/dashboard.php");
}
// else if(empty($member_id)){
// 	setNotifyError("Enrollee Id is empty");
// 	redirect($HOST.'/'.$group_id);
// }

if($member_id == 'quote'){
	if(empty($quote_id)){
		setNotifyError("quote_not_found");
		redirect($HOST . "/lead_quote_enrollment_response.php");
		exit();
	}
}

$getGroupData = $groupMemberEnrollment->getGroupDetails($group_id);
if(!empty($getGroupData['status']) && $getGroupData['status'] == 'Error') {
	$template = '404.inc.php';
	$layout = 'iframe.layout.php';
	include_once 'layout/end.inc.php';
	exit();
}
$getGroupDetails = checkIsset($getGroupData['getGroupDetails']);
$pb_row = checkIsset($getGroupData['pb_row']);
$groupStatus = checkIsset($getGroupDetails["status"]);

if(!in_array($groupStatus ,array('Active','Contracted'))){
	setNotifyError("Restricted Link: Contact Representative");
	redirect($GROUP_HOST . "/dashboard.php");
}

$group_billing_method = !empty($getGroupDetails["billing_type"]) ? $getGroupDetails["billing_type"] : 'individual';
$pageBuilderLink = $GROUP_ENROLLMENT_WEBSITE_HOST.'/'.$group_id;
//if enroll from page builder than replace with group username
$pb_id = checkIsset($pb_row['id']);
$pageUserName = $pb_id > 0 ? $group_id : '';
$group_id = $getGroupDetails['user_name'];
/*--- EO Expiration Code Start checkEOExpiredOrNot() ---*/
$eo_expiration_date = checkIsset($getGroupDetails["e_o_expiration"]);

if(!empty($eo_expiration_date) && (strtotime($eo_expiration_date) < strtotime(date('Y-m-d')))){
	setNotifyError("E&O is Expired.");
	redirect($GROUP_HOST . "/dashboard.php");
}


// Tab-1 Coverage Details Code Start
	$groupAndMemberInformation = $groupMemberEnrollment->getGroupAndMemberInformation($group_id,$member_id);
	$groupCompany =!empty( $groupAndMemberInformation['groupCompany']) ?  $groupAndMemberInformation['groupCompany'] : array();
	$enrollee_class_row =!empty( $groupAndMemberInformation['enrolleeClass']) ?  $groupAndMemberInformation['enrolleeClass'] : array();
	$coverage_period_row =!empty( $groupAndMemberInformation['coveragePeriods']) ?  $groupAndMemberInformation['coveragePeriods'] : array();
	$member_data =!empty( $groupAndMemberInformation['groupMemberDetail']) ?  $groupAndMemberInformation['groupMemberDetail'] : array();
	$additional =!empty($groupAndMemberInformation['additionalInfo']) ?  $groupAndMemberInformation['additionalInfo'] : array();

	$member_details =  !empty($member_data['data']) ? $member_data['data'] : array();

	if(!empty($member_details['id'])){
		$lead_id = !empty($member_details['lead_id']) ? $member_details['lead_id'] : 0;
	}

	$def_bill_row =!empty($groupAndMemberInformation['getDefaultBilling']) ?  $groupAndMemberInformation['getDefaultBilling'] : array();
	$additionalInfo = !empty($additional['additionalInfo']) ? $additional['additionalInfo'] : array();
	$disabledQuestion = !empty($additional['disabledQuestion']) ? $additional['disabledQuestion'] : array();

	$additionalDisabledQuestion = [];
	if(!empty($disabledQuestion)){
		foreach($disabledQuestion as $disabledQuestion){
			$additionalDisabledQuestion[$disabledQuestion['id']] = $disabledQuestion;
		}
	}
	$memberType = !empty($member_data['type']) ? $member_data['type'] : 'lead';
	if($member_id == 'quote'){
		$quoteData = [
			'groupId' => $group_id,
			'api_key'=>'getLeadQuoteDetail',
			'quote_id'=>$quote_id,
		];
		$lead_quote_data = $ajaxApiCall->ajaxApiCall($quoteData,true);
		if (empty($lead_quote_data)) {
			$template = '404.inc.php';
			$layout = 'iframe.layout.php';
			include_once 'layout/end.inc.php';
			exit();
		}
		
		$member_details = $lead_quote_data['member_data'];
		$lead_quote_row = $lead_quote_data['leadQuoteData'];
		$memberType = 'member';
		$lead_id = $lead_quote_row['lead_id'];
		$lead_quote_detail_id = $lead_quote_row['id'];
	}

	if(isset($lead_quote_row) && !empty($lead_quote_row)){
		if (strtotime(date('Y-m-d H:i:s')) > strtotime($lead_quote_row['expire_time'])) {
			setNotifyError("quote_expired");
			redirect($HOST . "/lead_quote_expired.php");
			exit();
		}
		
		$customer_id = $lead_quote_row['customer_ids'];
		$enrollment_type = "quote";
		$order_id = $lead_quote_row['order_ids'];

		$ordData = [
			'group_billing_method' => $group_billing_method,
			'api_key'=>'getOrderData',
			'order_id'=>$order_id,
		];
		$order_res = $ajaxApiCall->ajaxApiCall($ordData,true);
		if ($order_res['status'] != 'Pending Quote' && $order_res['status'] != 'Pending Validation') {
			$customerData = [
				'group_billing_method' => $group_billing_method,
				'api_key'=>'checkMemberStatus',
				'customer_id'=>$order_res['customer_id'],
			];
			$customer_res = $ajaxApiCall->ajaxApiCall($customerData,true);
			if (empty($customer_res)) {
				setNotifyError("already_enrolled");
				redirect($HOST . "/lead_quote_enrollment_response.php");
				exit();
			}
		}
	}

	if(!empty($memberType) && $memberType =='lead' && !empty($member_details)){
		if(in_array($member_details['status'],array("Converted"))) {
			setNotifyError("Enrollee ID already enrolled.");
			if($pb_id > 0){
				redirect($pageBuilderLink);
			}else{
			    redirect($GROUP_HOST . "/member_enrollment.php");
			}
			exit();
		}
		$lead_id = $member_details['id'];
		$customer_id = $member_details['customer_id'];
	}

	if(!empty($member_id) && (empty($member_details['sponsor_id']) || $member_details['sponsor_id'] != $getGroupDetails['id'])){
		setNotifyError("Invalid group or sponsor.");
		$template = '404.inc.php';
		$layout = 'iframe.layout.php';
		include_once 'layout/end.inc.php';
		exit();
	}
	if($memberType == 'member'){
		$is_add_product = 1;
		$member_id = checkIsset($member_details['rep_id']) !='' ? $member_details['rep_id'] : $member_id ;
		$customer_id = checkIsset($member_details['id']);
		$coverage_period = !empty($member_details['group_coverage_period_id']) ? $member_details['group_coverage_period_id'] : "";
		$group_company = !empty($member_details['group_company_id']) ? $member_details['group_company_id'] : 0;
		$group_company_name = !empty($member_details['business_name']) ? $member_details['business_name'] : "";
		$enrollee_class = !empty($member_details['class_id']) ? $member_details['class_id'] : "";
		$relationship_date = !empty($member_details['relationship_date']) ? date('m/d/Y',strtotime($member_details['relationship_date'])) : "";
		$relationship_of_group = !empty($member_details['relationship_to_group']) ? $member_details['relationship_to_group'] : "";
	}else{
		$member_id = '';
		$is_add_product = 0;
		$customer_id = checkIsset($member_details['customer_id']);
		$coverage_period = !empty($member_details['group_coverage_id']) ? $member_details['group_coverage_id'] : "";
		$group_company = !empty($member_details['group_company_id']) ? $member_details['group_company_id'] : 0;
		$group_company_name = !empty($member_details['business_name']) ? $member_details['business_name'] : "";
		$enrollee_class = !empty($member_details['group_classes_id']) ? $member_details['group_classes_id'] : "";
		$relationship_date = !empty($member_details['hire_date']) ? date('m/d/Y',strtotime($member_details['hire_date'])) : "";
		$relationship_of_group = !empty($member_details['employee_type']) ? $member_details['employee_type'] : "";
	}
	

	//get group coverage for members if its exists
	$coverage_period_table = array_filter($coverage_period_row,function($arr) use ($coverage_period) {
		if($arr['id'] == $coverage_period){
			return $arr;
		}
	});
	$coverage_period_table = !empty($coverage_period_table) ? $coverage_period_table : $coverage_period_row;

	//get group class for members if its exists
	$enrollee_class_table = array_filter($enrollee_class_row,function($arr) use ($enrollee_class) {
		if($arr['id'] == $enrollee_class){
			return $arr;
		}
	});

	$gap_pay_frequency_form = '';
	if(!empty($enrollee_class_table)){
		foreach($enrollee_class_table as $arr){
			$gap_pay_frequency_form = $arr['pay_period'];
		}
	}

	$primary_address1 = checkIsset($member_details['address']);
	$primary_address2 = checkIsset($member_details['address_2']);

	$primary_fname = checkIsset($member_details['fname']);
	$primary_lname = checkIsset($member_details['lname']);
	$primary_name = !empty($primary_fname) ? $primary_fname.' '.$primary_lname : '';
	$primary_email = checkIsset($member_details['email']);
	$primary_birthdate = !empty($member_details['birth_date']) ? date('m/d/Y',strtotime($member_details['birth_date'])) : "";
	$primary_zipcode = checkIsset($member_details['zip']);
	$primary_gender = checkIsset($member_details['gender']);

	$height_feet = checkIsset($member_details['height_feet']);
	$height_inch = checkIsset($member_details['height_inch']);

	$primary_height = !empty($height_feet) && !empty($height_inch)? $height_feet." Ft. ".$height_inch." In." : "";

	$primary_weight = checkIsset($member_details['weight']);
	$primary_annual_salary = checkIsset($member_details['primary_annual_salary']) !='' ? $member_details['primary_annual_salary'] : (checkIsset($member_details['salary']) !='' ? checkIsset($member_details['salary']) : checkIsset($member_details['income'])) ;
	//Tax Deduction Field
	$pre_tax_deductions = checkIsset($member_details['pre_tax_deductions_field']);
	$post_tax_deductions = checkIsset($member_details['post_tax_deductions_field']);
	$gap_marital_status = checkIsset($member_details['w4_filing_status_field']) !='' ? $member_details['w4_filing_status_field'] : 'single';
	$w4_no_of_allowances_field = checkNumberset($member_details['w4_no_of_allowances_field']);
	$w4_two_jobs_field = checkIsset($member_details['w4_two_jobs_field']);
	$w4_dependents_amount_field = checkIsset($member_details['w4_dependents_amount_field']);
	$w4_4a_other_income_field = checkIsset($member_details['w4_4a_other_income_field']);
	$w4_4b_deductions_field = checkIsset($member_details['w4_4b_deductions_field']);
	$w4_additional_withholding_field = checkIsset($member_details['w4_additional_withholding_field']);
	$state_filing_status_field = checkIsset($member_details['state_filing_status_field']);
	$state_dependents_field = checkIsset($member_details['state_dependents_field']);
	$state_additional_withholdings_field = checkIsset($member_details['state_additional_withholdings_field']);
	//Tax Deduction Field

	$primary_monthly_income = checkIsset($member_details['primary_monthly_income']);
	$primary_no_of_children = checkIsset($member_details['no_of_children']);
	$primary_pay_frequency = checkIsset($member_details['pay_frequency']);
	$primary_tobacco_status = checkIsset($member_details['tobacco_use']);
	$primary_smoking_status = checkIsset($member_details['smoke_use']);
	$primary_has_spouse = checkIsset($member_details['has_spouse']);
	$primary_employment_status = checkIsset($member_details['employmentStatus']);
	$primary_us_citizen = checkIsset($member_details['us_citizen']);
	$primary_hours_per_week = checkIsset($member_details['hours_per_week']);
	$primary_salary = checkIsset($member_details['salary']);
	$primary_date_of_hire = checkIsset($member_details['hire_date']);
	$member_rep_id = checkIsset($member_details['rep_id']);
	$is_address_verified = checkIsset($member_details['is_address_verified']);
	$is_valid_address = checkIsset($member_details['is_address_verified']);
// Tab-1 Coverage Details Code Ends
	
// Tab-2 Question Code Start
	if(!empty($group_id)){
		$question = $groupMemberEnrollment->bundleQuestionsAnswers($group_id);
	}
// Tab-2 Question Code Ends


$quote_products = array();
$quote_healthy_step_fee = 0;
if(!empty($lead_quote_row)) {
	$lead_id = $lead_quote_row['lead_id'];
	$from_group_side = true;
	$ordData = [
		'group_billing_method' => $group_billing_method,
		'api_key'=>'getOrderDetailData',
		'order_id'=>$lead_quote_row['order_ids'],
	];
	$od_res = $ajaxApiCall->ajaxApiCall($ordData,true);

    if(!empty($od_res)) {
    	foreach ($od_res as $od_row) {
    		if($od_row['product_type'] == "Healthy Step") {
    			$quote_healthy_step_fee = $od_row['product_id'];
    		} elseif($od_row['type'] == "Normal") {

				$ordData = [
					'api_key'=>'getDynamicRaw',
					'table'=>'prd_main',
					'columns' => array('id','category_id','is_short_term_disablity_product'),
					'where' => array('id'=>$od_row['product_id']),
					'singleRow' => true
				];
				$prdRow = $ajaxApiCall->ajaxApiCall($ordData,true);

    			$quote_products[] = array(
    				'product_id' => $od_row['product_id'],
					'category_id' => $prdRow['category_id'],
					'is_short_term_disablity_product' => $prdRow['is_short_term_disablity_product'],
    				'price' => $od_row['price'],
					'display_price' => $od_row['display_price'],
    				'matrix_id' => $od_row['plan_id'],
    				'prd_plan_type_id' => $od_row['prd_plan_type_id'],
    				'pricing_model' => $od_row['pricing_model'],
    				'start_coverage_period' => $od_row['start_coverage_period'],
    			);
    			$coverage_date_selection_prd_array[] = $od_row['product_id'];
    			$coverage_date_selection_date_array[$od_row['product_id']] = date('m/d/Y',strtotime($od_row['start_coverage_period']));
    		}
    	}
    }

	$waive_coverage = array();
	$WaiveData = [
		'api_key'=>'getDynamicRaw',
		'table'=>'customer_waive_coverage',
		'where' => array('customer_id'=>$customer_id,'is_deleted'=>'N'),
		'columns' => array('id','category_id','reason','other_reason')
	];
	$resWaive = $ajaxApiCall->ajaxApiCall($WaiveData,true);

	if(!empty($resWaive)){
		foreach($resWaive as $wa){
			if(!in_array($wa['category_id'],array_column($quote_products,'category_id'))){
				$waive_coverage[] = $wa;
			}
		}
	}

    $resCustomerDep = array();

	$depRaw = [
		'api_key'=>'getDependentData',
		'order_id'=>$lead_quote_row['order_ids'],
	];
	$resCustomerDep = $ajaxApiCall->ajaxApiCall($depRaw,true);

	//dependent member information start
	$child_dep = array();
	$spouse_dep = array();
	foreach($resCustomerDep as $dep){
		if(!empty($dep['benefit_amount'])) {
			$dep['benefit_amount'] = explode(',',$dep['benefit_amount']);
			if(!empty($dep['benefit_amount'])) {
				$benefit_amount = array();
				foreach ($dep['benefit_amount'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$benefit_amount[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['benefit_amount'] = $benefit_amount;
			}
		}
		if(!empty($dep['in_patient_benefit'])) {
			$dep['in_patient_benefit'] = explode(',',$dep['in_patient_benefit']);
			if(!empty($dep['in_patient_benefit'])) {
				$in_patient_benefit = array();
				foreach ($dep['in_patient_benefit'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$in_patient_benefit[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['in_patient_benefit'] = $in_patient_benefit;
			}
		}
		if(!empty($dep['out_patient_benefit'])) {
			$dep['out_patient_benefit'] = explode(',',$dep['out_patient_benefit']);
			if(!empty($dep['out_patient_benefit'])) {
				$out_patient_benefit = array();
				foreach ($dep['out_patient_benefit'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$out_patient_benefit[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['out_patient_benefit'] = $out_patient_benefit;
			}
		}
		if(!empty($dep['monthly_income'])) {
			$dep['monthly_income'] = explode(',',$dep['monthly_income']);
			if(!empty($dep['monthly_income'])) {
				$monthly_income = array();
				foreach ($dep['monthly_income'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$monthly_income[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['monthly_income'] = $monthly_income;
			}
		}
		if(!empty($dep['benefit_percentage'])) {
			$dep['benefit_percentage'] = explode(',',$dep['benefit_percentage']);
			if(!empty($dep['benefit_percentage'])) {
				$benefit_percentage = array();
				foreach ($dep['benefit_percentage'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$benefit_percentage[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['benefit_percentage'] = $benefit_percentage;
			}
		}

		$dep = array(
			'dep_id' => $dep['id'],
			'cd_profile_id' => $dep['cd_profile_id'],
			'order_id' => $dep['order_id'],
			'product_id' => $dep['product_id'],
			'product_plan_id' => $dep['product_plan_id'],
			'fname' => $dep['fname'],
			'birthdate' => date('m/d/Y',strtotime($dep['birth_date'])),
			'hire_date' => (strtotime($dep['hire_date']) > 0?date('m/d/Y',strtotime($dep['hire_date'])):''),
			'relation' => $dep['relation'],
			'gender' => $dep['gender'],
			'email' => $dep['email'],
			'phone' => $dep['phone'],
			'ssn' => $dep['ssn'],
			'city' => $dep['city'],
			'state' => $dep['state'],
			'zip_code' => $dep['zip_code'],
			'height_feet' => $dep['height_feet'],
			'height_inches' => $dep['height_inches'],
			'weight' => $dep['weight'],
			'smoke_use' => $dep['smoke_use'],
			'tobacco_use' => $dep['tobacco_use'],
			'benefit_level' => $dep['benefit_level'],
			'employmentStatus' => $dep['employmentStatus'],
			'salary' => $dep['salary'],
			'hours_per_week' => $dep['hours_per_week'],
			'pay_frequency' => $dep['pay_frequency'],
			'us_citizen' => $dep['us_citizen'],
			'benefit_amount' => $dep['benefit_amount'],
			'in_patient_benefit' => $dep['in_patient_benefit'],
			'out_patient_benefit' => $dep['out_patient_benefit'],
			'monthly_income' => $dep['monthly_income'],
			'benefit_percentage' => $dep['benefit_percentage'],
		);

		if(in_array($dep['relation'],array("Son","Daughter"))) {
			$child_dep[] = $dep;
		} else {
			$spouse_dep[] = $dep;
		}
	}
	//dependent member information end

	//primary additional question code start
	$additionalInfo = array();

	$csRaw = [
		'api_key'=>'getDynamicRaw',
		'table'=>'customer_settings',
		'where' => array('customer_id'=>$customer_id),
		'singleRow' => true
	];
	$customer_setting = $ajaxApiCall->ajaxApiCall($csRaw,true);
	
	if(!empty($customer_setting)) {
		$benefit_amount_arr = array();

		$customer_benefit = [
			'api_key'=>'getDynamicRaw',
			'table'=>'customer_benefit_amount',
			'where' => array('type'=>'Primary','customer_id'=>$customer_id,'is_deleted'=>'N'),
		];
		$customer_benefit_amount = $ajaxApiCall->ajaxApiCall($customer_benefit,true);
		if(!empty($customer_benefit_amount)){
			foreach ($customer_benefit_amount as $key => $value) {
				$benefit_amount_arr['benefit_amount'][$value['product_id']] = $value['amount'];
				$benefit_amount_arr['in_patient_benefit'][$value['product_id']] = $value['in_patient_benefit'];
				$benefit_amount_arr['out_patient_benefit'][$value['product_id']] = $value['out_patient_benefit'];
				$benefit_amount_arr['monthly_income'][$value['product_id']] = $value['monthly_income'];
				$benefit_amount_arr['benefit_percentage'][$value['product_id']] = $value['benefit_percentage'];
			}
		}

		$primaryMember = $ajaxApiCall->ajaxApiCall(['api_key'=>'getAdditionalInfo','memberId'=>$member_id,'quote_products'=>array_column($quote_products,'product_id')],true);
		$additionalInfo = !empty($primaryMember['additionalInfo']) ? $primaryMember['additionalInfo'] : array();
	}
	//primary additional question code end

	//beneficiary code start
		$csRaw = [
			'api_key'=>'getCustomerBeneficiaryData',
			'customer_id' => $customer_id,
		];
		$customer_beneficiary = $ajaxApiCall->ajaxApiCall($csRaw,true);

		$contingent_beneficiary = array();
		$principal_beneficiary = array();
		if(!empty($customer_beneficiary)){
			foreach ($customer_beneficiary as $key => $value) {
				if($value['beneficiary_type'] == "Contingent") {
					$contingent_beneficiary[] = $value;
				}
				if($value['beneficiary_type'] == "Principal") {
					$principal_beneficiary[] = $value;	
				}
			}
		}
	//beneficiary code End

	$depRaw = [
		'api_key'=>'getCustomerBillingProfile',
		'customer_id'=>$customer_id,
	];
	$billing_data = $ajaxApiCall->ajaxApiCall($depRaw,true);
	if(!empty($lead_quote_row['billing_info_param'])){
		$tmp_billing_data = json_decode($lead_quote_row['billing_info_param'], true);
		if(!empty($tmp_billing_data['payment_mode'])) {
			$billing_data = $tmp_billing_data;
		}
	}

	if($order_res['future_payment'] == "Y") {
		$post_date = date('m/d/Y',strtotime($order_res['post_date']));
		$future_payment = "Y";
	}
}

$enrollmentLocation = 'groupSide';
$is_group_member = 'Y';
$display_default_billing = "N";
$from_group_side = false;

$is_direct_deposit_account = false;
$is_gap_or_hip_plus_product = false;
if(!empty($def_bill_row)){
	$display_default_billing  = 'Y';

	foreach ($def_bill_row as $k => $billing) {
		if(!$is_direct_deposit_account && $billing['is_direct_deposit_account'] == 'Y'){
		   $is_direct_deposit_account = true;
		   break;
		}
	}
}

$agent_sql = "SELECT c.id,c.type,c.sponsor_id,c.fname,c.lname,c.feature_access,c.public_name,c.public_email,c.public_phone,cs.not_show_eo_expired,cs.display_in_member,cs.is_branding,cs.brand_icon,ad.e_o_expiration,c.status
		FROM customer c
		LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) 
		LEFT JOIN agent_document ad ON (c.id=ad.agent_id)
		WHERE c.user_name=:user_id AND c.is_deleted='N' AND c.type !='Customer' AND c.status IN('Active','Contracted')";
$agent_row = $pdo->selectOne($agent_sql,array(":user_id" => $group_id));
$sponsor_id = $agent_row['id'];

if($agent_row['type']=='Group'){
	if(isset($is_add_product) && $is_add_product == 1) {
		$pyament_methods = get_pyament_methods($agent_row['sponsor_id']);
	} else {
		$pyament_methods = get_pyament_methods($agent_row['sponsor_id'],false);	
	}
}else{
	if(isset($is_add_product) && $is_add_product == 1) {
		$pyament_methods = get_pyament_methods($sponsor_id);
	} else {
		$pyament_methods = get_pyament_methods($sponsor_id,false);	
	}
}
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];

$sms_content = $email_subject = $email_content = "";
$trigger_res = $pdo->selectOne("SELECT * FROM triggers WHERE id = 84");
if ($trigger_res > 0) {
	$sms_content = $trigger_res['sms_content'];
	$email_content = html_entity_decode($trigger_res['email_content']);
	$email_subject = $trigger_res['email_subject'];
}

$title = $member_rep_id;

$exStylesheets = array(
	'thirdparty/signature_pad-master/example/css/signature-pad.css',
	'thirdparty/multiple-select-master/multiple-select.css',
	'thirdparty/summernote-master/dist/summernote.css',
	'thirdparty/jquery-asRange-master/dist/css/asRange.css'.$cache,
	'thirdparty/ionrangeSlider/css/ion.rangeSlider.css'.$cache
);
$exJs = array(
	'thirdparty/ajax_form/jquery.form.js',
	'thirdparty/signature_pad-master/example/js/signature_pad.js',
	'thirdparty/price_format/jquery.price_format.2.0.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,
	'thirdparty/summernote-master/dist/summernote.js',
	'thirdparty/summernote-master/dist/popper.js',
	'thirdparty/bower_components/moment/moment.js',
	'thirdparty/jquery-asRange-master/dist/jquery-asRange.min.js'.$cache,
	'thirdparty/ionrangeSlider/js/ion.rangeSlider.js'.$cache,
	'thirdparty/jquery-match-height/js/jquery.matchHeight.js'
);

$template = 'group_member_enrollment.inc.php';
$layout = 'group.enroll.layout.php';
include_once 'layout/end.inc.php';
?>
