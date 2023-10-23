<?php 
$timezone = isset($_POST['timezone']) ? $_POST['timezone'] : '' ;
$order_date = isset($_POST['order_date']) ? $_POST['order_date'] : '' ;
if(!empty($timezone) && !empty($order_date)){
	include 'UserTimezone.php';	
	$tz = new UserTimeZone('m/d/Y g:i A T', $timezone);

	$date = $tz->getDate($order_date,'m/d/Y @ g:i A T');
	$response = array("date"=>$date);
	header("Content-type:application/json");
	echo json_encode($response);
	exit;
}
include_once __DIR__ . '/includes/connect.php'; 
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';

$function_list = new functionsList();
$enrollDate = new enrollmentDate();
$MemberEnrollment = new MemberEnrollment();
$md5_customer_id = checkIsset($_GET['id']);
$same_as_personal = false;
$lead_quote_details_res = $pdo->selectOne("SELECT id,is_opened,md5(customer_ids) as customer_id,order_ids,lead_id,billing_info_param,plan_ids,expire_time,created_at,admin_id,primary_annual_salary,primary_monthly_salary_percentage FROM lead_quote_details WHERE status!='Disabled' AND token = :token", array(":token" => $md5_customer_id));

if (empty($lead_quote_details_res)) {
	setNotifyError("quote_not_found");
	redirect($HOST . "/lead_quote_enrollment_response.php");
	exit();
}else{
	if(strtotime(date('m/d/Y')) >= strtotime($lead_quote_details_res['expire_time'])){
		setNotifyError("quote_expired");
		redirect($HOST . "/lead_quote_enrollment_response.php");
		exit();
	}
}
$lead_id = $lead_quote_details_res['lead_id'];
$lead_display_id = getname('leads',$lead_quote_details_res['lead_id'],'lead_id','id');

$primary_annual_salary = json_decode($lead_quote_details_res['primary_annual_salary'],true);
$primary_monthly_salary_percentage = json_decode($lead_quote_details_res['primary_monthly_salary_percentage'],true);

$customer_res = $pdo->selectOne("SELECT c.id,c.status,fname,lname,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,email,address,address_2,city,state,zip,birth_date,gender,sponsor_id,cell_phone,cs.salary,cs.employmentStatus,height_feet,height_inch,weight,tobacco_use,smoke_use,benefit_level,hours_per_week ,pay_frequency ,us_citizen ,no_of_children ,has_spouse ,hire_date,is_address_verified as is_valid_address FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE md5(c.id) = :id", array(":id" => $lead_quote_details_res['customer_id']));
$customer_id = $customer_res['id'];
$md5sponsor_id = md5($customer_res['sponsor_id']);

$sponsor_detail = $pdo->selectOne("SELECT c.id,if(c.public_name is not null,c.public_name,CONCAT(c.fname,' ',c.lname)) as name,if(c.public_phone is not null,c.public_phone,c.cell_phone) as cell_phone,if(c.public_email is not null ,c.public_email,c.email) as email,c.rep_id,cs.brand_icon,cs.is_branding,cs.display_in_member,c.type,c.sponsor_id FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id = c.id) WHERE is_deleted='N' AND type!='Customer' AND c.status in('Active','Contracted') AND c.id = :id", array(":id" =>$customer_res['sponsor_id']));

/*--- Check Member Already Exist ---*/
if(in_array($customer_res["status"],array('Customer Abandon','Pending Quote','Pending Validation'))) {
	$is_add_product = 0;
} else {
	$is_add_product = 1;
}
$email_error = $MemberEnrollment->validate_existing_email($customer_res['email'],$customer_res['sponsor_id'],$customer_id,$lead_id,array('is_add_product' => $is_add_product,'unqualified_leads' => true));
if($email_error['status'] == "fail" && in_array($email_error['existing_status'],array("bob_member","none_bob_member"))) {
	redirect($HOST . "/lead_quote_enrollment_response.php");
	exit();
}
/*---/Check Member Already Exist ---*/

$group_billing_method = '';
$enrollmentLocation = '';
if($sponsor_detail['type']=='Group'){
	$enrollmentLocation ="groupSide";
	$sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
	$resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$customer_res['sponsor_id']));

	if(!empty($resBillingType)){
		$group_billing_method = $resBillingType['billing_type'];
	}
}

$order_res = $pdo->selectOne("SELECT id FROM orders WHERE status in('Pending Validation','Payment Declined','Pending Application') AND customer_id = :customer_id AND id = :order_id", array(":customer_id" => $customer_id, ":order_id" => $lead_quote_details_res['order_ids']));

if(empty($order_res)){
	$order_res = $pdo->selectOne("SELECT id FROM group_orders WHERE status in('Pending Validation','Payment Declined','Pending Application') AND customer_id = :customer_id AND id = :order_id", array(":customer_id" => $customer_id, ":order_id" => $lead_quote_details_res['order_ids']));
}

if (empty($order_res)) {
	setNotifyError("quote_not_found");
	redirect($HOST . "/lead_quote_enrollment_response.php");
	exit();
}

$billing_data = $pdo->selectOne("SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full, AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number,id as billing_profile_id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' ORDER BY id DESC", array(":customer_id" => $customer_id));
if(empty($billing_data) || !empty($lead_quote_details_res['billing_info_param'])){
	$billing_data = json_decode($lead_quote_details_res['billing_info_param'], true);
}
$payment_mode = '';
if (!empty($billing_data)) {
	if (($customer_res['fname'] == $billing_data['fname']) && ($customer_res['lname'] == $billing_data['lname']) && ($customer_res['address'] == $billing_data['address']) && ($customer_res['city'] == $billing_data['city']) && ($customer_res['state'] == $billing_data['state']) && ($customer_res['zip'] == $billing_data['zip'])) {
		$same_as_personal = true;
	}
	$payment_mode = $billing_data['payment_mode'];
}


$order_id = $order_res['id'];
$plan_ids = $lead_quote_details_res['plan_ids'];
$eligibility_dates = array();
$product_list = array();
$products = array();
$plan_list = array();


$orderSql="SELECT od.id,od.order_id,website_id,pc.title,p.id as product_id,od.fee_applied_for_product,p.payment_type_subscription,od.prd_plan_type_id,p.type,od.plan_id,p.name,od.unit_price,od.start_coverage_period,od.end_coverage_period,p.product_type,p.fee_type,o.created_at,p.joinder_agreement_require FROM orders o 
			JOIN order_details od on (o.id=od.order_id AND od.is_deleted='N')
			LEFT JOIN prd_main p ON(p.id=od.product_id and p.is_deleted='N')
			LEFT JOIN prd_category pc ON(pc.id=p.category_id and pc.is_deleted='N')
			WHERE o.id=:order_id GROUP BY p.id";
$orderRes=$pdo->select($orderSql,array(":order_id"=>$order_id));

if(empty($orderRes)){
	$orderSql="SELECT od.id,od.order_id,website_id,pc.title,p.id as product_id,od.fee_applied_for_product,p.payment_type_subscription,od.prd_plan_type_id,p.type,od.plan_id,p.name,od.unit_price,od.start_coverage_period,od.end_coverage_period,p.product_type,p.fee_type,o.created_at,p.joinder_agreement_require FROM group_orders o 
			JOIN group_order_details od on (o.id=od.order_id AND od.is_deleted='N')
			LEFT JOIN prd_main p ON(p.id=od.product_id and p.is_deleted='N')
			LEFT JOIN prd_category pc ON(pc.id=p.category_id and pc.is_deleted='N')
			WHERE o.id=:order_id GROUP BY p.id";
	$orderRes=$pdo->select($orderSql,array(":order_id"=>$order_id));
}

$admin_fee = 0;
$enrollment_fee_amount=0;
$premium_products = array();
$PlanIdArr = $products = $all_products = array();
$healhty_step = array('unit_price'=>0,'id'=>'','name'=>'');
$monthly_payment = $sub_total = $service_fee = $grand_total = 0;
$next_billing_date = '';
$created_at = $lead_quote_details_res['created_at'];
$admin_id = $lead_quote_details_res['admin_id'];
$joinderAgreementProducts = array();
$joinder_agreement = "N";
if(count($orderRes)){
	foreach ($orderRes as $key => $row) {
			$plan_list[$row['product_id']]=$row['plan_id'];
			if(!empty($row['plan_id']) && !in_array($row['plan_id'], $PlanIdArr)){
				array_push($PlanIdArr, $row['plan_id']);
			}
			if(!in_array($row['product_type'],array('Healthy Step','ServiceFee'))){
				$products[$row['product_id']] = $row;
				$sub_total +=$row['unit_price'];
			}else if($row['product_type'] == 'Healthy Step'){
				$healhty_step['name'] = $row['name'];
				$healhty_step['id'] = $row['product_id'];
				$healhty_step['unit_price'] = $row['unit_price'];

			}else if($row['product_type'] == 'ServiceFee'){
				$service_fee+=$row['unit_price'];
			}else if($row['type']=='Fees' && in_array($row['product_type'],array('Carrier','Product','Vendor')) && $row['fee_type'] =='Charged'){
				$admin_fee += $row['unit_price'];
			}
			if($row["joinder_agreement_require"] == "Y"){
				$joinder_agreement = "Y";
				array_push($joinderAgreementProducts, $row['product_id']);
			}
			$eligibility_dates[$row['product_id']] = $row["start_coverage_period"];
			array_push($product_list, $row['product_id']);
	}
	$monthly_payment = $sub_total+$service_fee+$admin_fee;
	$grand_total=$monthly_payment+$healhty_step['unit_price'];	
}
$coverage_dates_option = $MemberEnrollment->get_coverage_period($product_list);
$coverage_dates = array();
foreach ($coverage_dates_option as $key => $coverage) {
	$coverage_dates[$coverage['product_id']]=$coverage['coverage_date'];	
}
$eligibility_date = min(array_map(function($item) { return $item; }, array_values($eligibility_dates)));
if (!empty($eligibility_date)) {
	$coverge_var = true;
	$coverge_effective_date = date('m/d/Y', strtotime($eligibility_date));
}
// $product_question = array();
// $product_question = $pdo->select("SELECT pa.*,pe.id as peid,pe.label FROM prd_enrollment_questions_assigned pa LEFT JOIN prd_enrollment_questions pe ON(pe.id=pa.prd_question_id) where product_id in(".implode(',',$product_list).") group by prd_question_id  order by pe.order_by");
$spouse_product_list = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(product_id)) as products from customer_dependent where order_id=:order_id and customer_id=:customer_id AND relation IN('husband', 'wife')",array(":order_id"=>$order_id,":customer_id"=>$customer_id));

$child_product_list = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(product_id)) as products from customer_dependent where order_id=:order_id and customer_id=:customer_id AND relation IN('son', 'daughter')",array(":order_id"=>$order_id,":customer_id"=>$customer_id));
$primary_product_question = $MemberEnrollment->get_primary_member_field($product_list);
$spouse_product_question = $child_product_question = array();
$spouse_products = $child_products = '';
if(!empty($spouse_product_list['products'])){
	$spouse_products = $spouse_product_list['products'];
	$spouse_product_list = explode(',',$spouse_product_list['products']);
	$spouse_product_question =  $MemberEnrollment->get_spouse_field($spouse_product_list);
}

if(!empty($child_product_list['products'])){
	$child_products = $child_product_list['products'];
	$child_product_list = explode(',',$child_product_list['products']);
	$child_product_question = $MemberEnrollment->get_child_field($child_product_list);
}
$prdQuestions = array();
if(!empty($prdQuestionRes)){
	foreach ($prdQuestionRes as $key => $value) {
		$prdQuestions[$value['id']]=$value;
	}
}
$primary_custom_question = $spouse_custom_question= $child_custom_question= array();
$customQuestionArray = array();
if(!empty($customer_res)){
	$primary_custom_question = $pdo->select("SELECT peq.*,ccq.answer from  customer_custom_questions ccq LEFT JOIN prd_enrollment_questions peq ON(peq.id=ccq.question_id) where peq.is_deleted='N' AND peq.is_deleted='N' and ccq.customer_id=:customer_id and enrollee_type='primary' ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC",array(":customer_id"=>$customer_id));

	$spouse_custom_question = $pdo->select("SELECT peq.*,ccq.answer from  customer_custom_questions ccq LEFT JOIN prd_enrollment_questions peq ON(peq.id=ccq.question_id) where peq.is_deleted='N' AND peq.is_deleted='N' and ccq.customer_id=:customer_id and enrollee_type='spouse' ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC",array(":customer_id"=>$customer_id));

	$child_custom_question = $pdo->select("SELECT peq.*,ccq.answer from  customer_custom_questions ccq LEFT JOIN prd_enrollment_questions peq ON(peq.id=ccq.question_id) where peq.is_deleted='N' AND peq.is_deleted='N' and ccq.customer_id=:customer_id and enrollee_type='child' ORDER BY  FIELD(questionType,'Default','Custom') ASC, order_by ASC",array(":customer_id"=>$customer_id));
	if(!empty($primary_custom_question)){
		foreach($primary_custom_question as $question){
			$customQuestionArray[] = $question['id'];
			${'primary_' . $question['label'].'_value'}  = $question['answer'];
		}
	}
}

$prdQuestion = array();
if(!empty($prdQuestions)){
	foreach ($prdQuestions as $key => $value) {
		$prdQuestion[$value['id']]=$value;
	}
}

//primary member information start
	$primary_fname_value = $customer_res['fname'];
	$primary_lname_value = $customer_res['lname'];
	$primary_birthdate_value = $customer_res['birth_date'];
	$primary_date_of_hire_value = $customer_res['hire_date'];
	$primary_gender_value = $customer_res['gender'];
	$primary_email_value = $customer_res['email'];
	$primary_phone_value = $customer_res['cell_phone'];
	$primary_SSN_value = $customer_res['ssn'];
	$primary_address1_value = $customer_res['address'];
	$primary_address2_value = $customer_res['address_2'];
	$is_valid_address = $customer_res['is_valid_address'];
	$primary_city_value = $customer_res['city'];
	$primary_state_value = $customer_res['state'];
	$primary_zip_value = $customer_res['zip'];
	$primary_height_feet_value = $customer_res['height_feet'];
	$primary_height_inch_value = $customer_res['height_inch'];
	$primary_height_value = $primary_height_feet_value.' Ft. '.$primary_height_inch_value.' In.';
	$primary_weight_value = $customer_res['weight'];
	$primary_smoking_status_value = $customer_res['smoke_use'];
	$primary_tobacco_status_value = $customer_res['tobacco_use'];
	$primary_benefit_level_value = $customer_res['benefit_level'];
	$primary_employment_status_value = $customer_res['employmentStatus'];
	$primary_salary_value = $customer_res['salary'];
	$primary_hours_per_week_value = $customer_res['hours_per_week'];
	$primary_pay_frequency_value = $customer_res['pay_frequency'];
	$primary_us_citizen_value = $customer_res['us_citizen'];
	$primary_no_of_children_value = $customer_res['no_of_children'];
	$primary_has_spouse_value = $customer_res['has_spouse'];
	// $primary_benefit_amount_value = $customer_res['benefit_amount'];	


	
	if(in_array($customer_res['status'],$MEMBER_ABONDON_STATUS)) {
	    $lead_sql = "SELECT * FROM leads WHERE id=:id";
		$lead_res = $pdo->selectOne($lead_sql, array(":id" => $lead_id));

		$primary_fname_value = $lead_res['fname'];
		$primary_lname_value = $lead_res['lname'];
		$primary_email_value = $lead_res['email'];
		$primary_phone_value = $lead_res['cell_phone'];
    }
//primary member information end
$resCustomerDep = array();
if(!empty($product_list) && !empty($customer_id)){
	$sqlCustomerDep="SELECT c.cd_profile_id as id,c.product_id,c.product_plan_id,c.relation,c.fname,c.lname,c.gender,c.email,c.ssn,c.birth_date,phone,ssn,city,state,zip_code,height_feet,height_inches,weight,smoke_use,tobacco_use,employmentStatus,salary,hire_date ,benefit_level ,hours_per_week ,pay_frequency ,us_citizen  
	FROM customer_dependent c WHERE c.customer_id=:customer_id AND c.product_id in (".implode(',',$product_list).") AND c.terminationDate is NULL GROUP BY c.cd_profile_id ORDER BY FIELD(c.relation,'husband','wife','son','daughter')";
	$resCustomerDep=$pdo->select($sqlCustomerDep,array(":customer_id"=>$customer_id));	
//dependent member information start
	foreach($resCustomerDep as $dep){
	$dep_id = $dep['id'];
	$dependent_fname_value[$dep_id] = $dep['fname'];
	$dependent_lname_value[$dep_id] = $dep['lname'];
	$dependent_birthdate_value[$dep_id] = $dep['birth_date'];
	$dependent_date_of_hire_value[$dep_id] = $dep['hire_date'];
	$dependent_relation_value[$dep_id] = $dep['relation'];
	$dependent_gender_value[$dep_id] = $dep['gender'];
	$dependent_email_value[$dep_id] = $dep['email'];
	$dependent_phone_value[$dep_id] = $dep['phone'];
	$dependent_SSN_value[$dep_id] = $dep['ssn'];
	$dependent_city_value[$dep_id] = $dep['city'];
	$dependent_state_value[$dep_id] = $dep['state'];
	$dependent_zip_value[$dep_id] = $dep['zip_code'];
	$dependent_height_feet_value[$dep_id] = $dep['height_feet'];
	$dependent_height_inch_value[$dep_id] = $dep['height_inches'];
	$dependent_height_value[$dep_id] = $dependent_height_feet_value[$dep_id].' Ft. '.$dependent_height_inch_value[$dep_id].' In.';
	$dependent_weight_value[$dep_id] = $dep['weight'];
	$dependent_smoking_status_value[$dep_id] = $dep['smoke_use'];
	$dependent_tobacco_status_value[$dep_id] = $dep['tobacco_use'];
	$dependent_benefit_level_value[$dep_id] = $dep['benefit_level'];
	$dependent_employment_status_value[$dep_id] = $dep['employmentStatus'];
	$dependent_salary_value[$dep_id] = $dep['salary'];
	$dependent_hours_per_week_value[$dep_id] = $dep['hours_per_week'];
	$dependent_pay_frequency_value[$dep_id] = $dep['pay_frequency'];
	$dependent_us_citizen_value[$dep_id] = $dep['us_citizen'];
	// $dependent_benefit_amount_value[$dep_id] = $dep['benefit_amount'];
	$customQuestionArraySpouse = $customQuestionArrayChild = array();
	if(!empty($spouse_custom_question)){
		foreach($spouse_custom_question as $question){
			$customQuestionArraySpouse[$question['id']] = $question['id'];
			${'dependent_' . $question['label'].'_value'}[$dep_id]  = $question['answer'];
		}
	}
	if(!empty($child_custom_question)){
		foreach($child_custom_question as $question){
			$customQuestionArrayChild[$question['id']] = $question['id'];
			${'dependent_' . $question['label'].'_value'}[$dep_id]  = $question['answer'];
		}
	}
//dependent member information end
	}
}
$customer_beneficiary = $customer_benefit_amount = $dep_benefit_amount = array();
if(!empty($customer_id)){
	//beneficiary code start
	$customer_beneficiary = $pdo->select("SELECT *,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn from customer_beneficiary where customer_id=:customer_id AND is_deleted='N'",array(":customer_id"=>$customer_id));
	$contingent_beneficiary = array();
	$principal_beneficiary = array();
	foreach ($customer_beneficiary as $key => $value) {
		if($value['beneficiary_type'] == "Contingent") {
			$contingent_beneficiary[] = $value;
		}
		if($value['beneficiary_type'] == "Principal") {
			$principal_beneficiary[] = $value;	
		}
	}
	$principal_beneficiary_field = $MemberEnrollment->get_principal_beneficiary_field($product_list);
	$contingent_beneficiary_field = $MemberEnrollment->get_contingent_beneficiary_field($product_list);
	//beneficiary code End

	//benefit Amount start
	$customer_benefit_amount = $pdo->select("SELECT cb.*,cb.amount as benefit_amount,p.name,p.product_code from customer_benefit_amount cb JOIN prd_main p ON(p.id=cb.product_id and p.is_deleted='N' AND p.id IN(".implode(',',$product_list).")) where cb.type='Primary' AND cb.customer_id=:customer_id AND cb.is_deleted='N'",array(":customer_id"=>$customer_id));

	$dep_benefit_amount = $pdo->select("SELECT cd_profile_id ,customer_id,benefit_amount,in_patient_benefit,out_patient_benefit,monthly_income,benefit_percentage, product_id ,relation,p.name FROM customer_dependent cd JOIN prd_main p ON(p.id=cd.product_id AND p.is_deleted='N' AND p.id IN(".implode(',',$product_list).")) WHERE customer_id=:customer_id ",array(":customer_id"=>$customer_id));
	//benefit Amount end
}

$order_res = $pdo->selectOne("SELECT post_date,future_payment FROM orders WHERE status in('Pending Validation','Payment Declined','Pending Application') AND customer_id = :customer_id AND id = :order_id", array(":customer_id" => $customer_id, ":order_id" => $lead_quote_details_res['order_ids']));
if(empty($order_res)){
	$order_res = $pdo->selectOne("SELECT post_date,future_payment FROM group_orders WHERE status in('Pending Validation','Payment Declined','Pending Application') AND customer_id = :customer_id AND id = :order_id", array(":customer_id" => $customer_id, ":order_id" => $lead_quote_details_res['order_ids']));
}
//  Post Date
$future_payment = $order_res['future_payment'];
$post_date = "";
$is_post_date_updated = false;
if ($future_payment == "Y") {
	$post_date = date('m/d/Y', strtotime($order_res['post_date']));
	if (strtotime($post_date) <= strtotime(date("Y-m-d"))) {
		$is_post_date_updated = true;
		$o_post_date = $post_date;
		$post_date = date("m/d/Y", strtotime("+1 days"));
	}
}

$sale_type_params = array();
$sale_type_params['is_renewal'] = "N";
$sale_type_params['customer_id'] = $customer_id;
if(in_array($group_billing_method,array('list_bill','TPA'))){
}else{
	if($sponsor_detail['type']=='Group'){
		$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_detail['sponsor_id'], $payment_mode,$sale_type_params);
	}else{
		$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_detail['id'], $payment_mode,$sale_type_params);
	}
	$payment_res = $pdo->selectOne("SELECT * from payment_master where id=:id and is_deleted='N'",array(":id"=>$payment_master_id));
}


$pyament_methods = get_pyament_methods($customer_res['sponsor_id'],false);
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];

$exStylesheets = array('thirdparty/signature_pad-master/example/css/signature-pad.css','thirdparty/bootstrap-datepicker-master/css/datepicker.css');
$exJs = array('thirdparty/signature_pad-master/example/js/signature_pad.js','js/password_validation.js','thirdparty/masked_inputs/jquery.maskedinput.min.js','thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js','thirdparty/Birthdate/moment.min.js', 'thirdparty/jquery-match-height/js/jquery.matchHeight.js');

/*--- Update Link Open Status ---*/
if($lead_quote_details_res['is_opened'] == "N") {
	$pdo->update("lead_quote_details",array('is_opened'=>'Y','link_opened_at' => 'msqlfunc_NOW()'),array("clause" => "id=:id", "params" => array(":id" => $lead_quote_details_res['id'])));
}
/*---/Update Link Open Status ---*/

$template = 'enrollment_verification.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>