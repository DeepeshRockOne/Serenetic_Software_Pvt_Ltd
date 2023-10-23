<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/cyberx_payment_class.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/trigger.class.php';
include_once __DIR__ . '/includes/member_setting.class.php';
require __DIR__ . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

$memberSetting = new memberSetting();
$MemberEnrollment = new MemberEnrollment();
$enrollDate = new enrollmentDate();
$function_list = new functionsList();
$TriggerMailSms = new TriggerMailSms();
$customer_rep_id = '';
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);


$response = array();
$validate = new Validation();

$today_date=date('Y-m-d');
$customer_id = 0;
$lead_id = 0;
$lead_display_id = '';
$order_id = 0;
$is_assisted_enrollment ='N';
$enrollment_type = isset($_POST['enrollment_type'])?$_POST['enrollment_type']:"";
$sponsor_billing_method = isset($_POST['group_billing_method'])?$_POST['group_billing_method']:"";
$order_status_res="";
$decline_log_id = "";
$payment_master_id = 0;
$REAL_IP_ADDRESS = get_real_ipaddress();
//********** Quote code start **********************
	if ($enrollment_type == "quote" || !empty($_POST['lead_quote_detail_id'])) {
		$lead_quote_detail_id = isset($_POST['lead_quote_detail_id'])?$_POST['lead_quote_detail_id']:0;

		$lead_quote_detail_sql = "SELECT * FROM lead_quote_details WHERE id =:lead_detail_id";
		$lead_quote_detail_res = $pdo->selectOne($lead_quote_detail_sql, array(":lead_detail_id" => $lead_quote_detail_id));

		$customer_id = $lead_quote_detail_res['customer_ids'];
		$quote_id = $lead_quote_detail_res['id'];
		$is_assisted_enrollment = $lead_quote_detail_res['is_assisted_enrollment'];
		$lead_id = $lead_quote_detail_res['lead_id'];

		/*---------- Customer Detail -----------*/
		$customer_sql = "SELECT sponsor_id,email,birth_date,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn FROM customer WHERE id =:id";
		$customer_res = $pdo->selectOne($customer_sql, array(":id" => $customer_id));
		/*---------- Customer Detail -----------*/
		$sponsor_id = $customer_res['sponsor_id'];

		$primary_email = $customer_res['email'];
	}
//********** Quote code end   **********************

$customer_id = isset($_POST["customer_id"]) ? $_POST["customer_id"] : 0;
$lead_id = isset($_POST["lead_id"]) ? $_POST["lead_id"] : 0;
$existing_customer_id = 0;
if(!empty($customer_id)){
	$existing_customer_id = $customer_id;
}
$sponsor_id = isset($_POST['sponsor_id'])?$_POST['sponsor_id']:"";
$submit_type = isset($_POST['submit_type'])?$_POST['submit_type']:"";
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";
$pb_id = isset($_POST['pb_id'])?$_POST['pb_id']:0; //Page Builder ID
$site_user_name = isset($_POST['site_user_name'])?$_POST['site_user_name']:''; //Page Builder ID
$action = isset($_POST['action'])?$_POST['action']:"";
$step = isset($_POST['dataStep'])?$_POST['dataStep']:"";

$sponsor_sql = "SELECT id,type,upline_sponsors,level,payment_master_id,ach_master_id,fname,lname,user_name,rep_id,sponsor_id FROM customer WHERE type!='Customer' AND id = :id ";
$sponsor_row = $pdo->selectOne($sponsor_sql, array(':id' => $sponsor_id));

$sponsor_agents = $function_list->getDirectLoaAgents($sponsor_id);
$sponsor_agents[] = $sponsor_id;

$is_group_member = 'N';
if($sponsor_row['type'] == "Group") {
	$is_group_member = 'Y';
	if(empty($sponsor_billing_method)) {
		$sponsor_billing_method = "individual";
		
		$sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
		$resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$sponsor_id));
		if(!empty($resBillingType)){
			$sponsor_billing_method = $resBillingType['billing_type'];
		}
	}
} else {
	$sponsor_billing_method = "individual";
	
}
/*--- enrollment_url ----*/

$enrollment_url = $AGENT_HOST.'/member_enrollment.php';
if($enrollmentLocation == "aae_site") {
	$enrollment_url = $AAE_WEBSITE_HOST . '/' . $site_user_name;

} else if($enrollmentLocation == "self_enrollment_site") {
	$enrollment_url = $ENROLLMENT_WEBSITE_HOST . '/' . $site_user_name;
} else if($enrollmentLocation == "groupSide") {
	$enrollment_url = $GROUP_HOST.'/member_enrollment.php';

} else if($enrollmentLocation == "adminSide"){
	$enrollment_url = $ADMIN_HOST.'/member_enrollment.php';
}
/*---/enrollment_url ----*/

$response['step']=$step;
$response['submit_type']=$submit_type;
$response['action']=$action;
$response['enrollmentLocation']=$enrollmentLocation;
$response['address_response_status']='';

$is_add_product = isset($_POST['is_add_product'])?$_POST['is_add_product']:0;
$already_puchase_product = isset($_POST['already_puchase_product'])?$_POST['already_puchase_product']:'';
$added_product = isset($_POST['added_product'])?$_POST['added_product']:0;
$product_price = isset($_POST['product_price'])?$_POST['product_price']:array();
$product_matrix = isset($_POST['product_matrix'])?$_POST['product_matrix']:array();
$product_plan = isset($_POST['product_plan'])?$_POST['product_plan']:array();
$product_category = isset($_POST['product_category'])?$_POST['product_category']:array();
$all_category = isset($_POST['all_category'])?$_POST['all_category']:array();
$healthy_step_fee = isset($_POST['healthy_step_fee'])?$_POST['healthy_step_fee']:0;
$primary_zip = isset($_POST['primary_zip']) ? trim($_POST['primary_zip']) : '';

$product_list = array();
$tmpWaive_product_list = array();
$group_product_list = array();

if(!empty($product_matrix)){
	foreach ($product_matrix as $product_id => $matrix_id) {
		if(!empty($matrix_id)){
			array_push($product_list, $product_id);
		}
	}
}
$product_list_without_waive = $product_list;
$response['product_list']=!empty($product_list) ? implode(",", $product_list) : '';

//********* Product send email code start ********************
$mi_patria_product_res = $pdo->selectOne("SELECT setting_value FROM app_settings WHERE setting_key='mi_patria_products'");
$mi_patria_product_id = !empty($mi_patria_product_res['setting_value']) ? explode(',',$mi_patria_product_res['setting_value']) : '';

$send_email_productId = array_diff($product_list,$mi_patria_product_id);
//********* Product send email code end ********************
//********** Waive varible intialization code end   **********************
	$only_waive_products = false;
	if($enrollmentLocation=='groupSide'){
		$waive_checkbox = !empty($_POST['waive_checkbox']) ? $_POST['waive_checkbox'] : array();
		$waive_coverage_reason = !empty($_POST['waive_coverage_reason']) ? $_POST['waive_coverage_reason'] : array();
		$waive_coverage_other_reason = !empty($_POST['waive_coverage_other_reason']) ? $_POST['waive_coverage_other_reason'] : array();

		if(!empty($waive_checkbox)){

			$group_waive_product=isset($_POST['waive_products'])?$_POST['waive_products']:array();

			if(!empty($group_waive_product)){
				$group_product_list= $MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
				if(!empty($group_waive_product)  && count($product_list) == 0){
		  			$only_waive_products = true;
		  		}
				$product_list = array_merge($product_list,$group_product_list); 
				$tmpWaive_product_list = array_merge($tmpWaive_product_list,$group_product_list); 
			}
			
		}
	}
	$response['only_waive_products']=$only_waive_products ? 'Y' : 'N';
//********** Waive varible intialization code end   **********************

//*********** Check If Joinder Agreement Require Code Start
	if(!empty($product_list)){
		$selAgreementPrd = "SELECT id FROM prd_main WHERE id IN(".implode(",", $product_list).") AND joinder_agreement_require='Y'";
		$resAgreementPrd = $pdo->selectOne($selAgreementPrd);
		$response['joinder_agreement_require']= !empty($resAgreementPrd["id"]) ? 'Y' : 'N';
	}

//*********** Check If Joinder Agreement Require Code Ends


$combination_products = $MemberEnrollment->getCombinationProducts($product_matrix,$sponsor_id);

$core_products  = $MemberEnrollment->getCoreProducts($product_matrix,$added_product,$combination_products);

//$riderProduct = $MemberEnrollment->getRiderProducts($product_matrix,$sponsor_id);
$riderProduct = array();

if(isset($_GET['quote_prds'])) {
	if(!empty($added_product)) {
		$added_product = explode(',',$added_product);
		foreach ($added_product as $key => $prd_id) {
			if(!empty($prd_id) && !empty($combination_products[$prd_id])){
				$response['combination_products'][$prd_id] = $combination_products[$prd_id];
			}
		}

		foreach ($added_product as $key => $prd_id) {
			if(!empty($prd_id) && !empty($core_products[$prd_id])){
				$response['combination_products'][$prd_id]['Excludes'] = $core_products[$prd_id]['Excludes'];
			}
		}
	}
} else {

	if(!empty($added_product) && !empty($combination_products[$added_product])){
		$response['combination_products'] = $combination_products[$added_product];
	}

	if(!empty($added_product) && !empty($core_products[$added_product])){
		$response['combination_products']['Excludes'] = $core_products[$added_product]['Excludes'];
	}
}

if(!empty($added_product) && !empty($riderProduct) && !empty($riderProduct[$added_product])){
	$response['riderProduct'] = $riderProduct[$added_product];
}

$display_contribution='N';
$groupCoverageContributionArr=array();

if($is_group_member == 'Y') {
	$group_coverage_period_id = !empty($_POST['coverage_period']) ? $_POST['coverage_period'] : '';
	$enrolle_class = !empty($_POST['hdn_enrolle_class']) ? $_POST['hdn_enrolle_class'] : '';
	$relationship_to_group = !empty($_POST['hdn_relationship_to_group']) ? $_POST['hdn_relationship_to_group'] : '';
	$relationship_date = !empty($_POST['relationship_date']) ? $_POST['relationship_date'] : '';

	$sqlCoveragePeriod="SELECT gcc.*,gc.pay_period, gco.display_contribution_on_enrollment 
		FROM group_coverage_period_offering gco 
		JOIN group_classes gc ON (gc.id=gco.class_id and gc.is_deleted='N')
		LEFT JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N') 
		where gco.is_deleted='N' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id";
	$sqlCoveragePeriodWhere=array(':group_id'=>$sponsor_id,':class_id'=>$enrolle_class,':group_coverage_period_id'=>$group_coverage_period_id);
	$resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);

	
	if($resCovergaePeriod){
		foreach ($resCovergaePeriod as $key => $value) {
			$display_contribution=$value['display_contribution_on_enrollment'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['type']=$value['type'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['contribution']=$value['con_value'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['pay_period']=$value['pay_period'];
			$groupCoverageContributionArr['pay_period']['pay_period'] = $value['pay_period'];
		}
	}
}

$premium_products = $MemberEnrollment->getProductDetails($product_price,$product_matrix,$product_plan,$groupCoverageContributionArr);
$premium_products_display_total = $premium_products['display_total'];
$premium_products_total = $premium_products['total'];
$premium_products_group_price_total = $premium_products['group_price_total'];
$premium_products_display_group_price_total = $premium_products['display_group_price_total'];
unset($premium_products['total']);
unset($premium_products['display_total']);
unset($premium_products['group_price_total']);
unset($premium_products['display_group_price_total']);
$is_list_bill = 'N';
if(in_array($sponsor_billing_method,array('list_bill'))){
	$is_list_bill = 'Y';
}

$linked_Fee = $MemberEnrollment->getLinkedFee($product_matrix,$sponsor_id,'Y','N',0,'',$is_list_bill);
$linked_Fee_total = $linked_Fee['total'];
unset($linked_Fee['total']);
unset($linked_Fee['total_single']);
unset($linked_Fee['total_annually']);

$membership_Fee = $MemberEnrollment->getMembershipFee($product_matrix,$customer_id,$primary_zip);
$membership_Fee_total = $membership_Fee['total'];
unset($membership_Fee['total']);

if($enrollmentLocation == "self_enrollment_site") {
	$healthyStepFee = array();
}else if($is_add_product == 1){
	$extra = array("customer_id" => $customer_id,"is_add_product" => $is_add_product,"enrollmentLocation" => $enrollmentLocation);
	$healthyStepFee = $MemberEnrollment->getHealthyStepFee($product_matrix,$sponsor_id,$primary_zip,$extra);
} else {
	$healthyStepFee = $MemberEnrollment->getHealthyStepFee($product_matrix,$sponsor_id,$primary_zip);	
}

$healthy_step_fee_total =  0;
$healthy_step_fee_detail = '';
$addedHealthyStep = array();

if($is_group_member == 'Y'){
	$healthyStepFee=array();
}

if(!empty($healthyStepFee)){
	foreach ($healthyStepFee as $key => $value) {
		if($value['product_id'] == $healthy_step_fee){
			$healthy_step_fee_total = $value['price'];
			$healthy_step_fee_detail = $value['product_name'];
			$addedHealthyStep[$value['product_id']]=$value;
		}
	}
}


$sub_total = $premium_products_total + $linked_Fee_total + $membership_Fee_total;
$display_sub_total = $premium_products_display_total + $linked_Fee_total + $membership_Fee_total;
$sub_products_count = count($premium_products) + count($linked_Fee) + count($membership_Fee);

$serviceFee = $MemberEnrollment->getServiceFee($product_matrix,$sponsor_id,$sub_total);
$service_fee_total = $serviceFee['total'];
unset($serviceFee['total']);
if($is_group_member == 'Y'){
	$service_fee_total = 0;
	$serviceFee=array();
}


$order_total = $sub_total + $service_fee_total + $healthy_step_fee_total;
$display_order_total = $display_sub_total + $service_fee_total + $healthy_step_fee_total;

$acaProducts = $MemberEnrollment->getAcaProducts($premium_products);

//**** ACA Product Code Start *****
	$billing_display = true;
	$response['billing_display'] = 'Y';
	if(empty($order_total) && !empty($acaProducts)){
		$response['billing_display'] = 'N';
		$billing_display = false;
	}
//**** ACA Product Code End *****

if($display_contribution=='N'){
	$premium_products_display_group_price_total = 0;
}
$order_total = array(
	"premium_products_total" => $premium_products_total,
	"linked_Fee_total" => $linked_Fee_total,
	"membership_Fee_total" => $membership_Fee_total,
	"sub_total" => $sub_total,
	"display_sub_total" => $display_sub_total,
	"group_price_sub_total" => $premium_products_group_price_total,
	"display_group_price_sub_total" => $premium_products_display_group_price_total,
	"service_fee" => $service_fee_total,
	"healthy_step_fee" => $healthy_step_fee_total,
	"grand_total" => $order_total,
	"display_grand_total" => $display_order_total,
);

$response['display_contribution'] = $display_contribution;

if($submit_type=='CalculatePrice'){
	$response['premium_products_total'] = $premium_products_total;
	$response['premium_products'] = $premium_products;
	$response['linked_Fee'] = $linked_Fee;
	$response['membership_Fee'] = $membership_Fee;

	$response['premium_products_count'] = $sub_products_count;

	$response['service_fee_total'] = $order_total['service_fee'];
	$response['healthy_step_fees_name'] = $healthy_step_fee_detail;
	$response['healthy_step_fees_total'] = $healthy_step_fee_total;

	$response['order_total'] = $order_total['grand_total'];
	$response['display_order_total'] = $order_total['display_grand_total'];
	$response['group_price_sub_total'] = $order_total['group_price_sub_total'];
	$response['display_group_price_sub_total'] = $order_total['display_group_price_sub_total'];
	$response['sub_total'] = $order_total['sub_total'];
	$response['display_sub_total'] = $order_total['display_sub_total'];
	
	$response['status'] = 'success';

	if(!empty($healthyStepFee)){
		$response['healthyStepFeeList']=$healthyStepFee;
	}

	header('Content-type: application/json');
	echo json_encode($response);
	exit;
}

$purchase_products_array = array();

if(!empty($premium_products)){
	foreach ($premium_products as $product_id => $row) {

		if(!isset($purchase_products_array[$row['product_id']])){
			$purchase_products_array[$row['product_id']] = $row;
			$purchase_products_array[$row['product_id']]['qty'] = 1;
		}else{
			$purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
		}
	}
}
if(!empty($linked_Fee)){
	foreach ($linked_Fee as $key => $row) {
		$fee_product = $row['fee_product_id'];
		if(!isset($purchase_products_array[$row['product_id']])){
			$purchase_products_array[$row['product_id']] = $row;
			$purchase_products_array[$row['product_id']]['qty'] = 1;
			if($row['pricing_model'] == 'VariableEnrollee' && $row['is_benefit_tier'] == 'Y'){
			    $purchase_products_array[$row['product_id']]['plan_id'] = $product_plan[$fee_product];
			}
		}else{
			if($row['pricing_model'] == 'VariableEnrollee'){
				$purchase_products_array[$row['product_id']]['price'] = $purchase_products_array[$row['product_id']]['price'] + $row['price'];
				$purchase_products_array[$row['product_id']]['retail_price'] = $purchase_products_array[$row['product_id']]['retail_price'] + $row['retail_price'];
				$purchase_products_array[$row['product_id']]['commission_amount'] = $purchase_products_array[$row['product_id']]['commission_amount'] + $row['commission_amount'];
				$purchase_products_array[$row['product_id']]['non_commission_amount'] = $purchase_products_array[$row['product_id']]['non_commission_amount'] + $row['non_commission_amount'];
				$purchase_products_array[$row['product_id']]['matrix_id'] .= ','.$row['matrix_id'];
			}
			$purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
		}
	}
}
if(!empty($membership_Fee)){
	foreach ($membership_Fee as $key => $row) {
		if(!isset($purchase_products_array[$row['product_id']])){
			$purchase_products_array[$row['product_id']] = $row;
			$purchase_products_array[$row['product_id']]['qty'] = 1;
		}else{
			$purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
		}
	}
}

if(!empty($addedHealthyStep)){
	foreach ($addedHealthyStep as $key => $row) {
		if($key == $healthy_step_fee){
			if(!isset($purchase_products_array[$row['product_id']])){
				$purchase_products_array[$row['product_id']] = $row;
				$purchase_products_array[$row['product_id']]['qty'] = 1;
			}else{
				$purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
			}
		}

	}
}
if(!empty($serviceFee)){
	foreach ($serviceFee as $key => $row) {
		if(!isset($purchase_products_array[$row['product_id']])){
			$purchase_products_array[$row['product_id']] = $row;
			$purchase_products_array[$row['product_id']]['qty'] = 1;
		}else{
			$purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
		}
	}
}

//********** step3 varible intialization code start **********************
	$is_address_verified = isset($_POST['is_address_verified'])?$_POST['is_address_verified']:"N";
	$is_valid_address = isset($_POST['is_valid_address'])?$_POST['is_valid_address']:"N";
	$primary_fname = isset($_POST['primary_fname']) ? trim($_POST['primary_fname']) : '';
	$primary_lname = isset($_POST['primary_lname']) ? trim($_POST['primary_lname']) : '';
	$primary_SSN = isset($_POST['primary_SSN']) ? $_POST['primary_SSN'] : '';
	$primary_phone = isset($_POST['primary_phone']) ? $_POST['primary_phone'] : '';
	$primary_address1 = isset($_POST['primary_address1']) ? trim($_POST['primary_address1']) : '';
	$primary_address2 = isset($_POST['primary_address2']) ? trim($_POST['primary_address2']) : '';
	$primary_city = isset($_POST['primary_city']) ? trim($_POST['primary_city']) : '';
	$primary_state = isset($_POST['primary_state']) ? trim($_POST['primary_state']) : '';
	if(!empty($primary_state) && strlen($primary_state) == 2) {
		$primary_state = strtoupper($primary_state);
    	$primary_state = (isset($getStateNameByShortName[$primary_state])?$getStateNameByShortName[$primary_state]:$primary_state);
    	$_POST['primary_state'] = $primary_state;
  	}
	$primary_zip = isset($_POST['primary_zip']) ? trim($_POST['primary_zip']) : '';
	$primary_email = isset($_POST['primary_email']) ? trim($_POST['primary_email']) : '';
	$primary_birthdate = isset($_POST['primary_birthdate']) ? $_POST['primary_birthdate'] : '';
	$primary_gender = isset($_POST['primary_gender']) ? trim($_POST['primary_gender']) : '';
	$primary_benefit_amount_arr = isset($_POST['primary_benefit_amount']) ? $_POST['primary_benefit_amount'] : array();
	$std_monthly_benefit_amount_arr = isset($_POST['std_monthly_benefit']) ? $_POST['std_monthly_benefit'] : array();
	$primary_in_patient_benefit_arr = isset($_POST['primary_in_patient_benefit']) ? $_POST['primary_in_patient_benefit'] : array();
	$primary_out_patient_benefit_arr = isset($_POST['primary_out_patient_benefit']) ? $_POST['primary_out_patient_benefit'] : array();
	$primary_monthly_income_arr = isset($_POST['primary_monthly_income']) ? $_POST['primary_monthly_income'] : array();
	$primary_benefit_percentage_arr = isset($_POST['primary_benefit_percentage']) ? $_POST['primary_benefit_percentage'] : array();
	$spouse_benefit_amount_arr = isset($_POST['spouse_benefit_amount']) ? $_POST['spouse_benefit_amount'] : array();
	$spouse_in_patient_benefit_arr = isset($_POST['spouse_in_patient_benefit']) ? $_POST['spouse_in_patient_benefit'] : array();
	$spouse_out_patient_benefit_arr = isset($_POST['spouse_out_patient_benefit']) ? $_POST['spouse_out_patient_benefit'] : array();
	$spouse_monthly_income_arr = isset($_POST['spouse_monthly_income']) ? $_POST['spouse_monthly_income'] : array();
	$spouse_benefit_percentage_arr = isset($_POST['spouse_benefit_percentage']) ? $_POST['spouse_benefit_percentage'] : array();
	$child_benefit_amount_arr = isset($_POST['child_benefit_amount']) ? $_POST['child_benefit_amount'] : array();
	$child_in_patient_benefit_arr = isset($_POST['child_in_patient_benefit']) ? $_POST['child_in_patient_benefit'] : array();
	$child_out_patient_benefit_arr = isset($_POST['child_out_patient_benefit']) ? $_POST['child_out_patient_benefit'] : array();
	$child_monthly_income_arr = isset($_POST['child_monthly_income']) ? $_POST['child_monthly_income'] : array();
	$child_benefit_percentage_arr = isset($_POST['child_benefit_percentage']) ? $_POST['child_benefit_percentage'] : array();
	$group_company_id = isset($_POST['group_company_id']) ? $_POST['group_company_id'] : '';
	$primary_annual_salary = array();
	$primary_monthly_salary_percentage = array();
	$primary_monthly_benefit = array();
	$primary = isset($_POST['primary']) ? $_POST['primary'] : array();
	$out_of_pocket_maximum = array();

	foreach ($product_plan as $p_id => $v) {

		if(isset($primary[$p_id]['annual_salary'])){
			$primary_annual_salary[$p_id] = $primary[$p_id]['annual_salary'];
		}
		if(isset($primary[$p_id]['monthly_benefit_percentage'])){
			$primary_monthly_salary_percentage[$p_id] = $primary[$p_id]['monthly_benefit_percentage'];
		}
		if(isset($_POST['monthly_benefit_amount_'.$p_id])){
			$primary_monthly_benefit[$p_id] = str_replace(',', '',$_POST['monthly_benefit_amount_'.$p_id]);
		}
		if(isset($_POST['out_of_pocket_maximum_primary_'.$p_id])){
			$out_of_pocket_maximum[$p_id] = str_replace(',', '',$_POST['out_of_pocket_maximum_primary_'.$p_id]);
		}
	}
	$primary_member_field =$MemberEnrollment->get_primary_member_field($product_list_without_waive);
	if(!empty($primary_member_field)){
		foreach($primary_member_field as $key => $row) {
			${'primary_'.$row['label']} = isset($_POST['primary_'.$row['label']])?$_POST['primary_'.$row['label']]:"";
		}
	}

	$spouse_fname = !empty($_POST['spouse_fname']) ? $_POST['spouse_fname'] : array();
	$child_fname = !empty($_POST['child_fname']) ? $_POST['child_fname'] : array();
	$spouse_assign_products = !empty($_POST['spouse_assign_products']) ? $_POST['spouse_assign_products'] : array();
	$combined_spouse_assign_products = array();


	$child_assign_products = !empty($_POST['child_assign_products']) ? $_POST['child_assign_products'] : array();
	$combined_child_assign_products = array();


	$spouse_products_list = !empty($_POST['spouse_products_list'])? explode(",", $_POST['spouse_products_list']):array();
	$spouse_field = array();
	if(!empty($spouse_products_list)){
		$spouse_field =$MemberEnrollment->get_spouse_field($spouse_products_list);
	}

	$child_products_list = !empty($_POST['child_products_list'])? explode(",", $_POST['child_products_list']):array();
	$child_field = array();
	if(!empty($child_products_list)){
		$child_field =$MemberEnrollment->get_child_field($child_products_list);
	}

	$is_principal_beneficiary = isset($_POST['is_principal_beneficiary'])?  $_POST['is_principal_beneficiary']:'';
	$is_contingent_beneficiary = isset($_POST['is_contingent_beneficiary'])? $_POST['is_contingent_beneficiary']:'';

	$productWiseDependentCount = array();
//********** step3 varible intialization code end   **********************

//********** step4 varible intialization code start *********************
	$temp_password = '';
	$billing_profile = isset($_POST['billing_profile'])?$_POST['billing_profile']:"new_billing";
	$last_billing_profile_id = (isset($_POST['last_billing_profile_id'])?$_POST['last_billing_profile_id']:0);
	$payment_mode = isset($_POST['payment_mode'])?$_POST['payment_mode']:"";

	$bill_address = isset($_POST['bill_address'])?$_POST['bill_address']:"";
	$bill_address2 = isset($_POST['bill_address2'])?$_POST['bill_address2']:"";
	$bill_city = isset($_POST['bill_city'])?$_POST['bill_city']:"";
	$bill_country = 231;
	$bill_state = isset($_POST['bill_state'])?$_POST['bill_state']:"";
	$bill_zip = isset($_POST['bill_zip'])?$_POST['bill_zip']:"";

	if($payment_mode=='CC'){
		$name_on_card = isset($_POST['name_on_card'])?$_POST['name_on_card']:"";
		$card_number = isset($_POST['card_number'])?$_POST['card_number']:"";
		$card_type = isset($_POST['card_type'])?$_POST['card_type']:"";
		$expiration = isset($_POST['expiration'])?$_POST['expiration']:"";
		$expiry_month = '';
		$expiry_year = '';
		if(!empty($expiration)){
			$expirtation_details = explode("/", $expiration);
			$expiry_month = $expirtation_details[0];
			$expiry_year = $expirtation_details[1];
		}
		$cvv_no = checkIsset($_POST['cvv_no']);
		$full_card_number = isset($_POST['full_card_number'])?$_POST['full_card_number']:"";

		if(empty($card_number) && !empty($full_card_number)) {
			$card_number = $full_card_number;
		}


	}else{
		$ach_bill_fname = isset($_POST['ach_bill_fname'])?$_POST['ach_bill_fname']:"";
		$ach_bill_lname = isset($_POST['ach_bill_lname'])?$_POST['ach_bill_lname']:"";
		$bankname = isset($_POST['bankname'])?$_POST['bankname']:"";
		$ach_account_type = isset($_POST['ach_account_type'])?$_POST['ach_account_type']:"";
		$routing_number = isset($_POST['routing_number'])?$_POST['routing_number']:"";
		$account_number = isset($_POST['account_number'])?$_POST['account_number']:"";
		$confirm_account_number = isset($_POST['confirm_account_number'])?$_POST['confirm_account_number']:"";

		$entered_routing_number = isset($_POST['entered_routing_number'])?$_POST['entered_routing_number']:"";
		$entered_account_number = isset($_POST['entered_account_number'])?$_POST['entered_account_number']:"";

		if(empty($account_number) && !empty($entered_account_number)) {
			$account_number = $entered_account_number;
			$confirm_account_number = $entered_account_number;
		}

		if(empty($routing_number) && !empty($entered_routing_number)) {
			$routing_number = $entered_routing_number;
		}
	}

	$coverage_dates = isset($_POST['coverage_date'])?$_POST['coverage_date']:array();

	$lowest_coverage_date ='';
	if(!empty($coverage_dates)){
		$lowest_coverage_date=$enrollDate->getLowestCoverageDate($coverage_dates);
	}
	$enroll_with_post_date = isset($_POST['enroll_with_post_date'])?$_POST['enroll_with_post_date']:"";
	if ($enroll_with_post_date == 'yes') {
		$post_date = isset($_POST['post_date'])?$_POST['post_date']:"";
	}
	$application_type = isset($_POST['application_type'])?trim($_POST['application_type']):"";
	$enrollment_application_type = '';
	$newStatus = 'Pending';
	if(in_array($application_type,array('member'))){
		$enrollment_application_type = 'email_sms_verification';
		$newStatus = 'Pending Validation';

		$sent_via = isset($_POST['sent_via'])?$_POST['sent_via']:"";
		$email_content = isset($_POST['email_content'])?$_POST['email_content']:"";
		$sms_content = isset($_POST['sms_content'])?$_POST['sms_content']:"";
		$email_subject = isset($_POST['email_subject'])?$_POST['email_subject']:"";
	}else if(in_array($application_type,array('member_signature'))){
		$enrollment_application_type = 'eSign';
		$password = isset($_POST['password'])?$_POST['password']:"";
		$c_password = isset($_POST['c_password'])?$_POST['c_password']:"";
		$Signature_data = isset($_POST['signature_data'])?$_POST['signature_data']:"";

		$product_check = isset($_POST['product_check'])?$_POST['product_check']:"";
		$product_term_check = isset($_POST['product_term_check'])?$_POST['product_term_check']:"";

		$joinder_agreement = isset($_POST['joinder_agreement'])?$_POST['joinder_agreement']:"";
		$joinder_agreement_check = isset($_POST['joinder_agreement_check'])?$_POST['joinder_agreement_check']:"";


	}else if(in_array($application_type,array('voice_verification'))){
		$enrollment_application_type = 'voice_verification';
		$voice_application_type = isset($_POST['voice_application_type'])?$_POST['voice_application_type']:"";
		$voice_verification_system_code = isset($_POST['voice_verification_system_code'])?$_POST['voice_verification_system_code']:"";

		$voice_physical_upload = isset($_FILES['voice_physical_upload'])?$_FILES['voice_physical_upload']:array();
		$voice_physical_name = isset($voice_physical_upload['name'])?$voice_physical_upload['name']:"";
		$voice_physical_type = isset($voice_physical_upload['type'])?$voice_physical_upload['type']:"";
		$voice_physical_tmp_name = isset($voice_physical_upload['tmp_name'])?$voice_physical_upload['tmp_name']:"";
		$voice_physical_size = isset($voice_physical_upload['size'])?$voice_physical_upload['size']:"";
		$voice_uploaded_fileName = array();

	}else if(in_array($application_type,array('admin'))){
		$enrollment_application_type = 'upload_document';
		$physical_file_name = isset($_FILES['physical_upload']['name'])?$_FILES['physical_upload']['name']:"";
		$physical_file_tmp_name = isset($_FILES['physical_upload']['tmp_name'])?$_FILES['physical_upload']['tmp_name']:"";
		$physical_fileSize = isset($_FILES['physical_upload']['size'])?$_FILES['physical_upload']['size']:"";
		$physical_fileType = isset($_FILES['physical_upload']['type'])?$_FILES['physical_upload']['type']:"";
	}
	if (isset($bill_country) && $bill_country != '') {
		$country_sql = "SELECT * FROM `country` WHERE country_id in($bill_country) ORDER BY country_id DESC";
		$country_res = $pdo->select($country_sql);
	}
	if (isset($country_res) && count($country_res)>0) {
		foreach ($country_res AS $key => $value) {
			$countries[$value['country_id']] = $value;
			$country_name[$value['country_id']] = $value['short_name'];
		}
	}
//********** step4 varible intialization code end   **********************

//********* step2 validation code start ********************
	if ($step >= 2) {

		$already_puchase_product = $MemberEnrollment->getAlreadyPurchasedProducts($customer_id);

		if (empty($product_list)) {
			if($is_group_member == 'Y'){
				if(empty($waive_checkbox)){
					$validate->setError("product_cart", "Please Select Any Product");
				}
			}else{
				$validate->setError("product_cart", "Please Select Any Product");
			}
		}else if(!empty($already_puchase_product)){
			foreach ($product_list as $tmp_plan_id => $tmp_product_id) {
				if(in_array($tmp_product_id, $already_puchase_product)){
					$validate->setError("product_plan_" . $tmp_product_id, "Product already purchased");
				}
			}
		}else{
			$required_products_error=array();
			$packagedProductArr=array();
			foreach ($product_list as $key => $product_id) {
				if (empty($product_plan[$product_id])) {
					if($is_group_member == 'Y'){
						if(!in_array($product_id,$group_product_list)){
							$validate->setError("product_plan_" . $product_id, "Please select plan");
						}
					}else{
						$validate->setError("product_plan_" . $product_id, "Please select plan");
					}
				}


				if(!empty($combination_products[$product_id])){
					$tmpCombinationProduct = $combination_products[$product_id];
					if(!empty($tmpCombinationProduct['Required']['product_id'])){
						$required_product = explode(",",$tmpCombinationProduct['Required']['product_id']);
						if(!empty($required_product)){
							foreach ($required_product as $key => $required) {

								if(!in_array($required, $product_list)){
									$requiredProductName=getname('prd_main',$required,'name','id');
									$productName=getname('prd_main',$product_id,'name','id');

									$required_products_error[$required]['productName']=$productName;
									$required_products_error[$required]['product_id']=$product_id;
									$required_products_error[$required]['requiredProductName']=$requiredProductName;
									$required_products_error[$required]['required_product_id']=$required;

									$requiredProductArr[$product_id][]=$requiredProductName;

									$validate->setError("product_plan_".$product_id, implode(", ",$requiredProductArr[$product_id]) ." is Required for this product");
								}
							}
						}
					}
					if(!empty($tmpCombinationProduct['Packaged']['product_id'])){
						$packaged_product = explode(",",$tmpCombinationProduct['Packaged']['product_id']);
						if(!empty($packaged_product)){
							$is_package_prd_found_counter=0;
							foreach ($packaged_product as $key => $packaged) {
								if(in_array($packaged, $product_list)){
									$is_package_prd_found_counter++;
								}else{
									$packaged_products_error[$product_id][]=getname('prd_main',$packaged,'name','id');
								}
							}
							if($is_package_prd_found_counter==0){
								$validate->setError("product_plan_".$product_id, "This product required at least one packaged product from these products: ".implode(", ",$packaged_products_error[$product_id]));
							}
						}
					}
				}
				/*OP29-844 Task updates User must waive coverage if not selected (required even if all products are excluded) ,
				(Waive Coverage: on enrollment, if a product is not selected from that category than user must select waive coverage option to proceed. Please update validation) */
				if($is_group_member == 'Y' && $enrollmentLocation=='groupSide'){
					$tmp_waive_checkbox = array_merge($waive_checkbox,$product_category);
					if(!empty($tmp_waive_checkbox)){
						foreach($all_category as $key => $category_id){
							if(!in_array($category_id,$tmp_waive_checkbox)){
								$validate->setError("product_cart", "Select Waive plan if you don't purchase or excluded.");
							}
						}
					}else{
						if(empty($waive_checkbox)){
							$validate->setError("product_cart", "Please Select Any Product");
						}
					}
				}
			}
		}

		if(!empty($healthyStepFee) && !array_key_exists($healthy_step_fee,$addedHealthyStep)) {
			$validate->setError("product_cart", "Please select healthy step");
		}

		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "products_detail";
		}
	}
//********* step2 validation code end   ********************

//********* step3 validation code start ********************
	if ($step >= 3) {

		if(!empty($primary_member_field)){
			$primary_benefit_arr = array('primary_benefit_amount','primary_in_patient_benefit','primary_out_patient_benefit','primary_monthly_income','primary_benefit_percentage');
			foreach($primary_member_field as $key => $row) {
				$prd_question_id= $row['id'];
				$is_required = $row['required'];
				$control_name = 'primary_'.$row['label'];
				$control_types = $row['control_type'];
				$label = $row['display_label'];
				$type = $row['questionType'];
				$control_class = $row['control_class'];
				$questionType = $row['questionType'];
				$product_ids = $row['product_ids'];

				if(in_array($control_name,$primary_benefit_arr)){
					continue;
				}

				$control_value = isset($_POST[$control_name])?$_POST[$control_name]:"";


				if($questionType=='Custom'){
					$custom_control_name = str_replace($prd_question_id,"", $control_name);
					$custom_control_value = isset($_POST[$custom_control_name][$prd_question_id])?$_POST[$custom_control_name][$prd_question_id]:"";
					$tmpControlName = $custom_control_name;
					$tmpControlValue = $custom_control_value;
					${$tmpControlName} = $custom_control_value;
				}else{
					$tmpControlName = $control_name;
					$tmpControlValue = $control_value;
					${$tmpControlName} = $control_value;
				}
				if($is_required=='Y') {
					if(is_array(${$tmpControlName})){
						if(empty($tmpControlValue)){
							$validate->setError($control_name,$label.' is required');
						}
					}else{
						if($control_name == 'primary_SSN'){
							$validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue), array('required' => $label.' is required'));
							if(!$validate->getError('primary_SSN')){
								$array = array_unique(str_split(str_replace('-', "", $tmpControlValue)));
								$result = $array;

								if(count($result) === 1 ) {
								    $validate->setError('primary_SSN',"Please enter valid SSN");
								}

								if(doesStringContainChain(str_replace('-', "", $tmpControlValue),9) == true){
									$validate->setError('primary_SSN',"Please enter valid SSN");	
								}
							}
						}else if($control_name == 'primary_phone'){
							if(!$validate->getError('primary_phone')){
								$validate->digit(array('required' => true, 'field' => $control_name, 'value' => phoneReplaceMain($tmpControlValue), 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
							}
						}else if($control_name == 'primary_salary'){
							if(!$validate->getError('primary_salary') && ($tmpControlValue == 0 || $tmpControlValue == 0.00)){
								$validate->setError($control_name,'Valid Annual Salary is required');
							}
						}else{
							if($control_types == "textarea"){
								$validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue,'max' => 300), array('required' => $label.' is required'));	
							}else{
							$validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue), array('required' => $label.' is required'));
							}
						}
					}
				}

				if($control_name == "primary_address1" && !empty($control_value) && $is_valid_address !='Y'){
					$tmpAdd1=$primary_address1;
					$tmpAdd2=!empty($primary_address2) ? $primary_address2 : '#';
					$address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$primary_city,$primary_state,$primary_zip);
				    if(!empty($address_response)){
				      if($address_response['status']=='success'){
				        $response['address'] = $address_response['address'];
				        $response['address2'] = $address_response['address2'];
				        $response['enteredAddress']= $primary_address1 .' '.$primary_address2 .'</br>'.$primary_city.', '.$allStateShortName[$primary_state] . ' '.$primary_zip;
				        $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
				        $response['address_response_status']='success';
				      }/*else{
				        $adress_reponse_error = isset($address_response['error_message']) ? $address_response['error_message'] : 'Address Not Found.';
						$validate->setError("primary_address1",$adress_reponse_error);
				      }*/
				    }
				}

				if(!empty($primary_address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$primary_address2)) {
				    $validate->setError('primary_address2','Special character not allowed');
				}

				if($control_class == "dob" && !empty($control_value)){
					if (!$validate->getError($control_name)) {
						list($mm, $dd, $yyyy) = explode('/', $control_value);

						if (!checkdate($mm, $dd, $yyyy)) {
							$validate->setError($control_name, 'Valid Date is required');
						}
					}
				}

				if($questionType=='Custom'){
					$productNames = "";
					if(!empty($product_ids)){
						$sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
						$resProduct = $pdo->selectOne($sqlProduct);

						if(!empty($resProduct) && !empty($resProduct['productNames'])){
							$productNames = $resProduct['productNames'];
						}
					}
					$custom_control_name = str_replace($prd_question_id,"", $control_name);
					$custom_control_value = isset($_POST[$custom_control_name][$prd_question_id])?$_POST[$custom_control_name][$prd_question_id]:"";
					if(!empty($custom_control_value)){
						if(is_array($custom_control_value)){
							$tmpIncr = " AND answer in ('".implode("','", $custom_control_value)."')";
						}else{
							$tmpIncr = " AND answer = '".$custom_control_value."'";
						}

						$sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
		          		$resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));

		          		if(!empty($resAnswer)){
		          			$validate->setError($control_name,"Answer is not eligible For <b>".$productNames."</b>");
		          		}
					}

				}
			}
			if(!$validate->getError('primary_fname') && !$validate->getError('primary_lname') && !$validate->getError('primary_birthdate') && !$validate->getError('primary_gender') && !$validate->getError('primary_address1')){
				$where_policy_holder = array(
					":fname" => $primary_fname,
					":lname" => $primary_lname,
					":birth_date" => date('Y-m-d',strtotime($primary_birthdate)),
					":gender" => $primary_gender,
					":address_1" => $primary_address1,
				);

				$temp_incr = "";
				if(!empty($customer_id)){
					$temp_incr .= " AND id != :id";
					$where_policy_holder[':id'] = $customer_id;
				}

				$exist_member = $pdo->selectOne("SELECT id from customer where fname=:fname AND lname=:lname AND birth_date=:birth_date AND gender=:gender AND address=:address_1 AND is_deleted='N' AND type='Customer' AND status NOT IN('Customer Abandon','Pending Quote','Pending Validation') $temp_incr",$where_policy_holder);
				if(!empty($exist_member)){
					$validate->setError('primary_poliy_holder',"Planholder Already Exists");
				}
			}
			
			if (!filter_var($primary_email, FILTER_VALIDATE_EMAIL)) {
				$validate->setError("primary_email2", "Valid Email is required");
			}

			if(!$validate->getError('primary_email')){
				$email_error = $MemberEnrollment->validate_existing_email($primary_email,$sponsor_id,$customer_id,$lead_id,array('is_add_product' => $is_add_product));
				if($email_error['status'] == "fail" && in_array($email_error['existing_status'],array("bob_member","none_bob_member"))) {
					$validate->setError("primary_email","This email is already associated with another member account.");
					$validate->setError("primary_email2","This email is already associated with another member account.");
				}
			}
		}

		if(!empty($spouse_fname) && is_array($spouse_fname)){
			foreach ($spouse_fname as $countKey => $spouseArr) {
				if(empty($spouse_assign_products[$countKey])){
					$validate->setError("spouse_assign_products","Please Select Product");
				}else{
					foreach ($spouse_assign_products[$countKey] as $key => $product_id) {
						array_push($combined_spouse_assign_products, $product_id);
						$productWiseDependentCount[$product_id]['Spouse'] = isset($productWiseDependentCount[$product_id]['Spouse'])?$productWiseDependentCount[$product_id]['Spouse']+ 1:1;
					}
				}
			}
		}

		if($is_group_member == 'Y'){
			$validate->string(array('required' => true, 'field' => 'group_company_id', 'value' => $group_company_id), array('required' => 'Group Company is required'));
		}

		if(!empty($child_fname) && is_array($child_fname)){
			foreach ($child_fname as $countKey => $childArr) {
				if(empty($child_assign_products[$countKey])){
					$validate->setError("child_assign_products_".$countKey,"Please Select Product");
				}else{
					foreach ($child_assign_products[$countKey] as $key => $product_id) {
						array_push($combined_child_assign_products, $product_id);
						$productWiseDependentCount[$product_id]['Child'] = isset($productWiseDependentCount[$product_id]['Child'])?$productWiseDependentCount[$product_id]['Child']+ 1:1;
					}
				}
			}
		}

		$dependent_final_array = array();
		//********* Dependent Validation  code start ********************
			if(!empty($product_list)){
				foreach ($product_list as $key => $productID) {
					$tmpDependent = array();
					$product_plan_id = $product_plan[$productID];
					$product_matrix_id = $product_matrix[$productID];

					$sqlProducts="SELECT name,family_plan_rule,is_children_age_restrictions,children_age_restrictions_from,children_age_restrictions_to,is_spouse_age_restrictions,spouse_age_restrictions_from,spouse_age_restrictions_to FROM prd_main where id=:id";
					$resProducts=$pdo->selectOne($sqlProducts,array(":id"=>$productID));



					$product_name=$resProducts['name'];
					$family_plan_rule = $resProducts['family_plan_rule'];

					$spouse_dependent = !empty($productWiseDependentCount[$productID]['Spouse']) ? $productWiseDependentCount[$productID]['Spouse'] : 0;
					$child_dependent = !empty($productWiseDependentCount[$productID]['Child']) ? $productWiseDependentCount[$productID]['Child'] : 0;
					$totalDependent = $spouse_dependent + $child_dependent;


					if($product_plan_id == 2){
						if (!in_array($productID, $combined_child_assign_products)) {
							$validate->setError("dependent_general", "Add Child For Product <b>".$product_name."</b>");
						}
					}else if($product_plan_id == 3){

						if (!in_array($productID, $combined_spouse_assign_products)) {
							$validate->setError("dependent_general", "Add Spouse For Product <b>".$product_name."</b>");
						}
					}else if($product_plan_id == 4){
						if($family_plan_rule=="Spouse And Child"){
							if($spouse_dependent == 0){
								$validate->setError('dependent_general', "Add Spouse For Product <b>".$product_name."</b>");
							}
							if($child_dependent == 0){
								$validate->setError('dependent_general', "Add Child For Product <b>".$product_name."</b>");
							}

						}else if($family_plan_rule=="Minimum One Dependent"){
							if($spouse_dependent == 0 && $child_dependent == 0){
								$validate->setError('dependent_general', "Any One Dependent is required For <b>".$product_name."</b>");
							}

						}else if($family_plan_rule=="Minimum Two Dependent"){
							if($totalDependent < 2){
								$validate->setError('dependent_general', "Minimum Two Dependent is required For <b>".$product_name."</b>");
							}
						}
					}else if($product_plan_id == 5){
						if($spouse_dependent == 0 && $child_dependent == 0){
							$validate->setError('dependent_general', "Any One Dependent is required For <b>".$product_name."</b>");
						}
						if($totalDependent > 1){
							$validate->setError('dependent_general', "Only One Dependent is required For <b>".$product_name."</b>");
						}
					}

					if(!empty($spouse_dependent)){
						if(!empty($spouse_assign_products)){
							foreach ($spouse_assign_products as $spouseKey => $spouseArr) {
								if(is_array($spouseArr) && !in_array($productID,$spouseArr)) {
									continue;
								}

								$tmpDependent[$spouseKey]['dependent_product_list']=$spouse_products_list;
								$tmpDependent[$spouseKey]['dependent_relation_input']='spouse';
								$tmpDependent[$spouseKey]['relation']='Spouse';
								$tmpDependent[$spouseKey]['dependent_id']=$spouseKey;
								$tmpDependent[$spouseKey]['cd_profile_id']=isset($_POST['spouse_cd_profile_id'][$spouseKey])?$_POST['spouse_cd_profile_id'][$spouseKey]:0;

								if(!empty($spouse_field)){
									$spouse_benefit_arr = array('spouse_benefit_amount','spouse_in_patient_benefit','spouse_out_patient_benefit','spouse_monthly_income','spouse_benefit_percentage');
									foreach($spouse_field as $field_key => $row) {
										$prd_question_id = $row['id'];
										$is_required = $row['required'];
										$control_name = 'spouse_'.$row['label'];
										$control_types = $row['control_type'];
										$label = $row['display_label'];
										$control_value = isset($_POST[$control_name][$spouseKey])?$_POST[$control_name][$spouseKey]:"";

										${$control_name} = $control_value;
										$control_class = $row['control_class'];
										$questionType = $row['questionType'];

										if(in_array($control_name,$spouse_benefit_arr)){
											continue;
										}


										if($questionType=='Custom'){
											$custom_control_name = str_replace($prd_question_id,"", $control_name);
											$custom_control_value = isset($_POST[$custom_control_name][$spouseKey][$prd_question_id])?$_POST[$custom_control_name][$spouseKey][$prd_question_id]:"";
											$tmpControlName = $custom_control_name;
											$tmpControlValue = $custom_control_value;
											${$tmpControlName} = $custom_control_value;
										}else{
											$tmpControlName = $control_name;
											$tmpControlValue = $control_value;
											${$tmpControlName} = $control_value;
										}

										if($is_required=='Y') {
											if(is_array(${$tmpControlName})){
												if(empty($tmpControlValue)){
													$validate->setError($control_name,$label.' is required');
												}
											}else{
												if($control_types == "textarea"){
													$validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue,'max' => 300), array('required' => $label.' is required'));	
												}else{
												$validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue), array('required' => $label.' is required'));
												}
											}
										}

										if($control_class == "dob" && !empty($control_value)){
											if (!$validate->getError($control_name)) {
												list($mm, $dd, $yyyy) = explode('/', $control_value);

												if (!checkdate($mm, $dd, $yyyy)) {
													$validate->setError($control_name, 'Valid Date is required');
												}
											}
										}

										if($control_name == "spouse_gender" && !empty($control_value)){
											$tmpDependent[$spouseKey]['dependent_relation']=getRelation('spouse', $control_value);
										}
										if($control_name == 'spouse_birthdate' && !empty($control_value)){
											if(strtotime($control_value) >= strtotime($today_date)){
												$validate->setError($control_name,"Please Enter Valid Birthdate");
											}
										}
										if($control_name == 'spouse_email' && !empty($control_value)){
											if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
												$validate->setError($control_name, "Valid Email is required");
											}
										}

										$tmpDependent[$spouseKey][$control_name]=$control_value;

										if($questionType=='Custom'){
											$productNames = "";
											if(!empty($product_ids)){
												$sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
												$resProduct = $pdo->selectOne($sqlProduct);

												if(!empty($resProduct) && !empty($resProduct['productNames'])){
													$productNames = $resProduct['productNames'];
												}
											}
											$custom_control_name = str_replace($prd_question_id,"", $control_name);
											$custom_control_value = isset($_POST[$custom_control_name][$spouseKey][$prd_question_id])?$_POST[$custom_control_name][$spouseKey][$prd_question_id]:"";
											if(!empty($custom_control_value)){
												if(is_array($custom_control_value)){
													$tmpIncr = " AND answer in ('".implode("','", $custom_control_value)."')";
												}else{
													$tmpIncr = " AND answer = '".$custom_control_value."'";
												}

												$sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
								          		$resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));

								          		if(!empty($resAnswer)){
								          			$validate->setError($control_name,"Answer is not eligible For <b>".$productNames."</b>");
								          		}
											}
										}

										if($control_name == "spouse_birthdate" && !empty($control_value) && $resProducts['is_spouse_age_restrictions']=='Y'){
											$ageFrom=$resProducts['spouse_age_restrictions_from'];
											$ageTo=$resProducts['spouse_age_restrictions_to'];


											$dependentAge=calculateAge(date('Y-m-d',strtotime($control_value)));

											if ($dependentAge < $ageFrom) {
												$validate->setError('dependent_general', 'Spouse must be '.$ageFrom.' years of age for product <b>'.$product_name.'</b>');
											} else if ($dependentAge > $ageTo) {
												$validate->setError('dependent_general', 'Spouse must be younger then '.$ageTo.' years of age for product <b>'.$product_name.'</b>');
											}

										}
									}
								}
							}
						}
					}

					if(!empty($child_dependent)){

						if(!empty($child_assign_products)){
							foreach ($child_assign_products as $childKey => $childArr) {
								if(is_array($childArr) &&!in_array($productID,$childArr)) {
									continue;
								}

								$tmpDependent[$childKey]['dependent_product_list']=$child_products_list;
								$tmpDependent[$childKey]['dependent_relation_input']='child';
								$tmpDependent[$childKey]['relation']='Child';
								$tmpDependent[$childKey]['dependent_id']=$childKey;
								$tmpDependent[$childKey]['cd_profile_id']=isset($_POST['child_cd_profile_id'][$childKey])?$_POST['child_cd_profile_id'][$childKey]:0;

								if(!empty($child_field)){
									$child_benefit_arr = array('child_benefit_amount','child_in_patient_benefit','child_out_patient_benefit','child_monthly_income','child_benefit_percentage');
									foreach($child_field as $field_key => $row) {
										$prd_question_id = $row['id'];
										$is_required = $row['required'];
										$control_name = 'child_'.$row['label'];
										$control_types = $row['control_type'];
										$label = $row['display_label'];
										$control_value = isset($_POST[$control_name][$childKey])?$_POST[$control_name][$childKey]:"";
										${$control_name} = $control_value;
										$control_class = $row['control_class'];
										$questionType = $row['questionType'];

										if(in_array($control_name,$child_benefit_arr)){
											continue;
										}


										if($questionType=='Custom'){
											$custom_control_name = str_replace($prd_question_id,"", $control_name);
											$custom_control_value = isset($_POST[$custom_control_name][$childKey][$prd_question_id])?$_POST[$custom_control_name][$childKey][$prd_question_id]:"";
											$tmpControlName = $custom_control_name;
											$tmpControlValue = $custom_control_value;
											${$tmpControlName} = $custom_control_value;
										}else{
											$tmpControlName = $control_name;
											$tmpControlValue = $control_value;
											${$tmpControlName} = $control_value;
										}

										if($is_required=='Y') {
											if(is_array(${$tmpControlName})){
												if(empty($custom_control_value)){
													$validate->setError($control_name."_".$childKey,$label.' is required');
												}
											}else{
												if($control_types == "textarea"){
													$validate->string(array('required' => true, 'field' => $control_name."_".$childKey, 'value' => $tmpControlValue,'max' => 300), array('required' => $label.' is required'));	
												}else{
												$validate->string(array('required' => true, 'field' => $control_name."_".$childKey, 'value' => $tmpControlValue), array('required' => $label.' is required'));
												}
											}
										}

										if($control_class == "dob" && !empty($control_value)){
											if (!$validate->getError($control_name."_".$childKey)) {
												list($mm, $dd, $yyyy) = explode('/', $control_value);

												if (!checkdate($mm, $dd, $yyyy)) {
													$validate->setError($control_name."_".$childKey, 'Valid Date is required');
												}
											}
										}

										if($control_name == "child_gender" && !empty($control_value)){
											$tmpDependent[$childKey]['dependent_relation']=getRelation('child', $control_value);
										}
										if($control_name == 'child_email' && !empty($control_value)){
											if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
												$validate->setError($control_name.'_'.$childKey, "Valid Email is required");
											}
										}
										if($control_name == 'child_birthdate' && !empty($control_value)){
											if(strtotime($control_value) >= strtotime($today_date)){
												$validate->setError($control_name.'_'.$childKey,"Please Enter Valid Birthdate");
											}
										}

										$tmpDependent[$childKey][$control_name]=$control_value;

										if($questionType=='Custom'){
											$productNames = "";
											if(!empty($product_ids)){
												$sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
												$resProduct = $pdo->selectOne($sqlProduct);

												if(!empty($resProduct) && !empty($resProduct['productNames'])){
													$productNames = $resProduct['productNames'];
												}
											}

											$custom_control_name = str_replace($prd_question_id,"", $control_name);
											$custom_control_value = isset($_POST[$custom_control_name][$childKey][$prd_question_id])?$_POST[$custom_control_name][$childKey][$prd_question_id]:"";
											if(!empty($custom_control_value)){
												if(is_array($custom_control_value)){
													$tmpIncr = " AND answer in ('".implode("','", $custom_control_value)."')";
												}else{
													$tmpIncr = " AND answer = '".$custom_control_value."'";
												}

												$sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
								          		$resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));

								          		if(!empty($resAnswer)){
								          			$validate->setError($control_name."_".$childKey,"Answer is not eligible For For <b>".$productNames."</b>");
								          		}
											}

										}

										if($control_name == "child_birthdate" && !empty($control_value) && $resProducts['is_children_age_restrictions']=='Y'){
											$ageFrom=$resProducts['children_age_restrictions_from'];
											$ageTo=$resProducts['children_age_restrictions_to'];


											$dependentAge=calculateAge(date('Y-m-d',strtotime($control_value)));
											if ($dependentAge < $ageFrom) {
												$validate->setError('dependent_general', 'Child must be '.$ageFrom.' years of age for product <b>'.$product_name.'</b>');
											} else if ($dependentAge > $ageTo) {
												$validate->setError('dependent_general', 'Child must be younger then '.$ageTo.' years of age for product <b>'.$product_name.'</b>');
											}

										}
									}
								}
							}
						}
					}

					if(!empty($tmpDependent)){
						$dependent_final_array[$productID] = array(
							"product_id" => $productID,
							"plan_id" => $product_plan_id,
							"matrix_id" => $product_matrix_id,
							"child_dependent" => $child_dependent,
							"spouse_dependent" => $spouse_dependent,
							"dependent" => $tmpDependent,
						);
					}
					
					if($is_add_product == 1){
						foreach ($dependent_final_array as $p_id => $arr_val) {
							$tmp_names = array();
							foreach ($arr_val['dependent'] as $v) {
								if($v['relation'] != 'Spouse'){
									if(!in_array($v['child_fname'] . $v['child_lname'] . $v['child_gender'] . strtotime($v['child_birthdate']), $tmp_names)){
										array_push($tmp_names, $v['child_fname'] . $v['child_lname'] . $v['child_gender'] . strtotime($v['child_birthdate']));
									}else{
										$validate->setError('child_fname_'.$v['dependent_id'],"Duplicate Name");
									}

								}
							}
						}
					}

				}
			}
		//********* Dependent Validation  code end   ********************

		//********* Beneficiery Validation  code Start   ********************
			$contingent_beneficiary_percentage = 0;
			if($is_contingent_beneficiary == 'displayed'){
				$contingent_beneficiary_field =$MemberEnrollment->get_contingent_beneficiary_field($product_list_without_waive);
				$tmpContingent = !empty($_POST['contingent_queBeneficiaryFullName']) ? $_POST['contingent_queBeneficiaryFullName'] : array();

				if(!empty($tmpContingent)){
					foreach ($tmpContingent as $contingentKey => $childArr) {
						if(!empty($contingent_beneficiary_field)){
							foreach($contingent_beneficiary_field as $field_key => $row) {

								$is_required = $row['required'];
								$control_name = 'contingent_'.$row['label'];
								$label = $row['display_label'];
								$control_value = isset($_POST[$control_name][$contingentKey])?$_POST[$control_name][$contingentKey]:"";
								${$control_name} = $control_value;
								$control_class = $row['control_class'];

								if($control_name == "contingent_queBeneficiaryAllow3"){
						          	continue;
					          	}

								if($is_required=="Y"){
									if(is_array(${$control_name})){
										if(empty($control_value)){
											$validate->setError($control_name."_".$contingentKey,$label.' is required');
										}
									}else{
										$validate->string(array('required' => true, 'field' => $control_name."_".$contingentKey, 'value' => $control_value), array('required' => $label.' is required'));
									}
								}


								if($control_class == "dob" && !empty($control_value)){
									if (!$validate->getError($control_name."_".$contingentKey)) {
										list($mm, $dd, $yyyy) = explode('/', $control_value);

										if (!checkdate($mm, $dd, $yyyy)) {
											$validate->setError($control_name."_".$contingentKey, 'Valid Date is required');
										}
									}
								}
								if($control_name == "contingent_queBeneficiaryEmail" && !empty($control_value)){
									if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
										$validate->setError($control_name."_".$contingentKey, 'Valid Email is required');
									}
								}
								if($control_name == "contingent_queBeneficiaryPhone" && !empty($control_value)){
									$validate->digit(array('required' => true, 'field' => $control_name."_".$contingentKey, 'value' => phoneReplaceMain($control_value), 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
								}
								if($control_name == "contingent_queBeneficiaryPercentage" && $control_value != ''){

									$contingent_beneficiary_percentage = $contingent_beneficiary_percentage + $control_value;


								}
							}
						}
					}
				}
				if($contingent_beneficiary_percentage!=100){
					$validate->setError("contingent_beneficiary_general", 'Sum of all Contingent Beneficiary percentages must equal 100%');
				}
			}else if($is_contingent_beneficiary == "not_displayed"){
				//$validate->setError("contingent_beneficiary_general","Add Contingent Beneficiery");
			}

			$principal_beneficiary_percentage = 0;
			if($is_principal_beneficiary == 'displayed'){
				$principal_beneficiary_field =$MemberEnrollment->get_principal_beneficiary_field($product_list_without_waive);
				$tmpPrincipal = !empty($_POST['principal_queBeneficiaryFullName']) ? $_POST['principal_queBeneficiaryFullName'] : array();

				if(!empty($tmpPrincipal)){
					foreach ($tmpPrincipal as $principalKey => $childArr) {
						if(!empty($principal_beneficiary_field)){
							foreach($principal_beneficiary_field as $field_key => $row) {

								$is_required = $row['required'];
								$control_name = 'principal_'.$row['label'];
								$label = $row['display_label'];
								$control_value = isset($_POST[$control_name][$principalKey])?$_POST[$control_name][$principalKey]:"";
								${$control_name} = $control_value;
								$control_class = $row['control_class'];

								if($control_name == "principal_queBeneficiaryAllow3"){
						          	continue;
					          	}
								if($is_required=='Y'){
									if(is_array(${$control_name})){
										if(empty($control_value)){
											$validate->setError($control_name."_".$principalKey,$label.' is required');
										}
									}else{
										$validate->string(array('required' => true, 'field' => $control_name."_".$principalKey, 'value' => $control_value), array('required' => $label.' is required'));
									}
								}


								if($control_class == "dob" && !empty($control_value)){
									if (!$validate->getError($control_name."_".$principalKey)) {
										list($mm, $dd, $yyyy) = explode('/', $control_value);

										if (!checkdate($mm, $dd, $yyyy)) {
											$validate->setError($control_name."_".$principalKey, 'Valid Date is required');
										}
									}
								}
								if($control_name == "principal_queBeneficiaryEmail" && !empty($control_value)){
									if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
										$validate->setError($control_name."_".$principalKey, 'Valid Email is required');
									}
								}
								if($control_name == "principal_queBeneficiaryPhone" && !empty($control_value)){
									$validate->digit(array('required' => true, 'field' => $control_name."_".$principalKey, 'value' => phoneReplaceMain($control_value), 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
								}
								if($control_name == "principal_queBeneficiaryPercentage" && $control_value != ''){
									$principal_beneficiary_percentage = $principal_beneficiary_percentage + $control_value;


								}


							}
						}
					}
				}
				if($principal_beneficiary_percentage!=100){
					$validate->setError('principal_beneficiary_general', 'Sum of all Principal Beneficiary percentages must equal 100%');
				}
			}else if($is_principal_beneficiary == "not_displayed"){

				$validate->setError("principal_beneficiary_general","Add Principal Beneficiery");
			}
		//********* Beneficiery Validation  code end   ********************

		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "basic_detail";
		}
	}
//********* step3 validation code end   ********************

$product_wise_dependents = array();

if(isset($dependent_final_array) && !empty($dependent_final_array)){
	foreach ($dependent_final_array as $dp) {
		$product_wise_dependents[$dp["product_id"]] = $dp["dependent"];
	}
}
$PlanIdArr = array();

if(!empty($purchase_products_array)){
	foreach ($purchase_products_array as $key => $product) {
		$PlanIdArr[] = $product['matrix_id'];
	}
}
$sale_type_params = array();
$sale_type_params['is_renewal'] = 'N';
if($is_group_member == 'Y'){
	if(!$only_waive_products && $billing_display){
		$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['sponsor_id'], "CC",$sale_type_params);
	}
}else{
	if($billing_display){
		$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_id, "CC",$sale_type_params);
	}
}
$cc_html = "";
if($payment_master_id){
	$cc_results = $pdo->selectOne("SELECT acceptable_cc FROM payment_master WHERE id = :id",array(":id" => $payment_master_id));
	if(!empty($cc_results['acceptable_cc'])){
		$acceptable_cc = explode(',', $cc_results['acceptable_cc']);
		if($acceptable_cc){
			$selected_card_type = $class = '';
			if(!empty($payment_mode) && $payment_mode == 'CC'){
				$selected_card_type = (!empty($card_type)?$card_type:'');
				$class = !empty($selected_card_type) ? "has-value" : "" ;
			}
			$cc_html .= '<select name="card_type" id="card_type" class="tblur form-control '.$class.'" data-error="Card Type is required">
           <option value=""> </option>';
           if(in_array("Visa", $acceptable_cc)){
            $cc_html .= '<option value="Visa" '.($selected_card_type == "Visa"?'selected="selected"':'').'> Visa </option>';
           }
           if(in_array('MasterCard', $acceptable_cc)){
            $cc_html .= '<option value="MasterCard" '.($selected_card_type == "MasterCard"?'selected="selected"':'').'> MasterCard </option>';
           }
           if(in_array('Discover', $acceptable_cc)){
            $cc_html .= '<option value="Discover" '.($selected_card_type == "Discover"?'selected="selected"':'').'> Discover </option>';
           }
           if(in_array('Amex', $acceptable_cc)){
            $cc_html .= '<option value="Amex" '.($selected_card_type == "Amex"?'selected="selected"':'').'> American Express </option>';
           }
        	$cc_html .= '</select>
                      <label>Card Type*</label>
                      <p class="error" id="error_card_type"></p>';
		}
	}
}
$response['cc_html'] = $cc_html;
//********* step4 validation code start ********************
	if ($step >= 4) {
		if($sponsor_billing_method == 'individual'){
			if(!$only_waive_products && $billing_display){

				$validate->string(array('required' => true, 'field' => 'billing_profile', 'value' => $billing_profile), 	array('required' => 'Payment Method is required'));
				if (!$validate->getError("billing_profile")) {
					if($billing_profile != "new_billing" && $billing_profile > 0) {
						$def_bill_sql = "SELECT *,
							AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                            AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                            AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as card_no_full
							FROM customer_billing_profile WHERE id=:id";
						$def_bill_row = $pdo->selectOne($def_bill_sql,array(':id'=>$billing_profile));
						if(!empty($def_bill_row)) {
							$payment_mode = $def_bill_row['payment_mode'];
							$bill_address = $def_bill_row['address'];
							$bill_address2 = $def_bill_row['address2'];
							$bill_city = $def_bill_row['city'];
							$bill_country = 231;
							$bill_state = $def_bill_row['state'];
							$bill_zip = $def_bill_row['zip'];

							if($payment_mode=='CC'){
								$name_on_card = $def_bill_row['fname'];
								$card_number = $def_bill_row['card_no_full'];
								$card_type = $def_bill_row['card_type'];
								$expiry_month = $def_bill_row['expiry_month'];
								$expiry_year = $def_bill_row['expiry_year'];
								$cvv_no = $def_bill_row['cvv_no'];
								$full_card_number = $def_bill_row['card_no_full'];
							} else {
								$ach_bill_fname = $def_bill_row['fname'];
								$ach_bill_lname = $def_bill_row['lname'];
								$bankname = $def_bill_row['bankname'];
								$ach_account_type = $def_bill_row['ach_account_type'];
								$routing_number = $def_bill_row['ach_routing_number'];
								$account_number = $def_bill_row['ach_account_number'];
								$confirm_account_number = $def_bill_row['ach_account_number'];
							}
						} else {
							$validate->setError("billing_profile","Payment Method is not found");
						}
					}
				}

				if($billing_profile == "new_billing") {
					if(!empty($last_billing_profile_id)) {
						$billing_profile = $last_billing_profile_id;
					}
					$validate->string(array('required' => true, 'field' => 'payment_mode', 'value' => $payment_mode), 	array('required' => 'Payment Mode is required'));
					$sale_type_params = array();
			  		$sale_type_params['is_renewal'] = 'N';
			  		if($is_group_member == 'Y'){
			  			$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['sponsor_id'], $payment_mode,$sale_type_params);
			  		}else{
						$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_id, $payment_mode,$sale_type_params);
			  		}

					if ($payment_mode == "CC") {
						$validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Name is required'));

						$validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Select Card Type'));
						$validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number,"max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER), array('required' => 'Card is required', 'invalid' => "Enter valid Card Number"));

						$validate->string(array('required' => true, 'field' => 'expiration', 'value' => $expiration), array('required' => 'Please select expiration month and year'));
						$cvv_required = "N";
						if(!empty($payment_master_id)){
							$sqlProcessor = "SELECT require_cvv FROM payment_master where id=:id";
							$resProcessor = $pdo->selectOne($sqlProcessor,array(":id"=>$payment_master_id));

							if(!empty($resProcessor)){
								$cvv_required = $resProcessor['require_cvv'];
							}
						}
						if($cvv_required == 'Y' || $cvv_no!=''){
							$validate->digit(array('required' => true, 'field' => 'cvv_no', 'value' => $cvv_no), array('required' => 'CVV is required', 'invalid' => "Enter valid CVV"));

							if(!$validate->getError("cvv_no") && !cvv_type_pair($cvv_no,$card_type)){
								$validate->setError("cvv_no","Invalid CVV Number");
							}
						}

						if (!$validate->getError("name_on_card") && !ctype_alnum(str_replace(" ","",$name_on_card))) {
							$validate->setError("name_on_card","Enter Valid Name");
						}

						if(!$validate->getError("card_number") && !is_valid_luhn($card_number,$card_type)){
							$validate->setError("card_number","Enter valid Credit Card Number");
						}

						if (!$validate->getError("expiration")) {
							$expirty_date = $expiry_year.'-'.$expiry_month.'-01';


							if($expiry_year < date('y') && $expiry_month < date('m')){
								$validate->setError("expiration","Valid Expiry Date is required");
							}
						}
					}

					if ($payment_mode == "ACH") {
						$validate->string(array('required' => true, 'field' => 'ach_bill_fname', 'value' => $ach_bill_fname), array('required' => 'First Name is required'));
						$validate->string(array('required' => true, 'field' => 'ach_bill_lname', 'value' => $ach_bill_lname), array('required' => 'Last Name is required'));

						$validate->digit(array('required' => true, 'field' => 'account_number', 'value' => $account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
						$validate->digit(array('required' => true, 'field' => 'confirm_account_number', 'value' => $confirm_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Confirm Account number is required', 'invalid' => "Enter valid Account number"));
						$validate->digit(array('required' => true, 'field' => 'routing_number', 'value' => $routing_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));

						if (!$validate->getError("routing_number")) {
							if (checkRoutingNumber($routing_number) == false) {
								$validate->setError("routing_number", "Enter valid routing number");
							}
						}
						if($account_number != $confirm_account_number){
							$validate->setError("confirm_account_number", "account number not matched");
						}
						$validate->string(array('required' => true, 'field' => 'ach_account_type', 'value' => $ach_account_type), array('required' => 'Account Type is required'));
						$validate->string(array('required' => true, 'field' => 'bankname', 'value' => $bankname), array('required' => 'Bank Name is required'));

						if (!$validate->getError("ach_bill_fname") && !ctype_alnum(str_replace(" ","",$ach_bill_fname))) {
							$validate->setError("ach_bill_fname","Enter Valid Firstname");
						}

						if (!$validate->getError("ach_bill_lname") && !ctype_alnum(str_replace(" ","",$ach_bill_lname))) {
							$validate->setError("ach_bill_lname","Enter Valid Lastname");
						}
					}

					$validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_address), array('required' => 'Address is required'));
					if(!empty($bill_address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$bill_address2)) {
					    $validate->setError('billing_address','Special character not allowed in Address 2');
					}
					$validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_city), array('required' => 'City is required'));
					$validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_state), array('required' => 'State is required'));
					$validate->string(array('required' => true, 'field' => 'billing_address', 'value' => $bill_zip), array('required' => 'Zip is required'));
				}
			}
		}
		//********* Coverage Date Validation  code start ********************
			$extra = array();
			if($is_group_member == 'Y'){
				$extra['is_group_member']=$is_group_member;
				$extra['enrollmentLocation']=$enrollmentLocation;
				$extra['enrolle_class']=$enrolle_class;
				$extra['coverage_period']=$group_coverage_period_id;
				$extra['relationship_to_group']=$relationship_to_group;
				$extra['relationship_date']=$relationship_date;
			}

			$tmpProduct_list=array_diff($product_list, $tmpWaive_product_list);

			$coverage_period =$MemberEnrollment->get_coverage_period($tmpProduct_list,$sponsor_id,$extra);
			if(!empty($coverage_period) && !$only_waive_products && $billing_display){
				foreach ($coverage_period as $key => $coverage) {
					if((empty($coverage_dates[$coverage['product_id']]) || strtotime($coverage_dates[$coverage['product_id']]) < 0)) {
						$validate->setError('coverage_date_'.$coverage['product_id'],'Please select plan date');
					}else{
						$effective_detail = $coverage['coverage_date'];
						if(strtotime($coverage_dates[$coverage['product_id']]) < strtotime($effective_detail) ) {
							$validate->setError('coverage_date_'.$coverage['product_id'],date('m/d/Y', strtotime($coverage_dates[$coverage['product_id']])). ' Plan date must be greater than or equal to ' . date('m/d/Y', strtotime($effective_detail)));
						}
					}
				}
			}
		//********* Coverage Date Validation  code end   ********************

		//********* Post Date Validation  code start ********************
			if ($enroll_with_post_date == 'yes') {
				$validate->string(array('required' => true, 'field' => 'post_date', 'value' => $post_date), array('required' => 'Select Post date'));

				if (empty($validate->getError('post_date'))) {
					if (strtotime($post_date) >= strtotime($lowest_coverage_date)) {
						$validate->setError('post_date', 'Post date must be less than ' . date('m/d/Y', strtotime($lowest_coverage_date)));
					}
					if (strtotime($post_date) <= strtotime(date("Y-m-d"))) {
						$validate->setError('post_date', 'Post date must future date');
					}
				}
			}
		//********* Post Date Validation  code end   ********************

		//********* Member Verification Validation  code start ********************
			if (!empty($application_type)) {
				if (in_array($application_type,array('member'))) {
					$response["sent_via"] = $sent_via;
					if ($sent_via == 'text' || $sent_via == 'Both') {
						$validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'SMS Content is required'));
						if (!$validate->getError("sms_content")) {
							if (strlen($sms_content) > 160) {
								$validate->setError('sms_content', 'SMS Content must be less then 160 character');
							}
							$is_link = strpos($sms_content, "[[link]]");
							if ($is_link <= 0) {
								$validate->setError('sms_content', 'SMS content must have [[link]] tag');
							}
						}
					}if ($sent_via == 'email' || $sent_via == 'Both') {
						$validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email Content is required'));
						$validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Email Subject is required'));

						if (!$validate->getError("email_content")) {
							$is_link = strpos($email_content, "[[link]]");
							if ($is_link <= 0) {
								$validate->setError('email_content', 'Email content must have [[link]] tag');
							}
						}
					}
				} else if (in_array($application_type,array('admin'))) {
					{
						if (empty($physical_file_name)) {
							$validate->setError('physical_upload', "Physical File is required");
						}

						if (!$validate->getError('physical_upload')) {
							$allowed_mime_types = array(
								'text/plain', 'image/png', 'image/jpeg', 'application/pdf', 'application/msword', 'application/vnd.ms-excel', 'text/csv',
							);
							$allowed_extensions = '*.pdf, *.jpg, *.jpeg, *png, *doc, *xls, *csv';
							$allowed_file_size = '52428800';
							$size_in_mb = "50";
							$mime_type = $physical_fileType;
							if (!in_array($mime_type, $allowed_mime_types)) {
								$validate->setError('physical_upload', "Only " . $allowed_extensions . " file format allowed");
							} else if ($physical_fileSize > $allowed_file_size) {
								$validate->setError('physical_upload', "Maximum " . $size_in_mb . " MB file size allowed");
							}
						}
					}
				} else if (in_array($application_type,array('voice_verification'))) {

					$validate->string(array('required' => true, 'field' => 'voice_application_type', 'value' => $voice_application_type), array('required' => 'Please select option'));

					if(empty($validate->getError('voice_application_type'))) {

						if($voice_application_type == "by_system_code") {
							$validate->digit(array('required' => true, 'field' => 'voice_verification_system_code', 'value' => $voice_verification_system_code), array('required' => 'System Code is required', 'invalid' => 'Valid System Code is required'));
						} else {
							if(count($voice_physical_upload) > 0){
								foreach($voice_physical_upload as $value){
								  foreach($value as $key => $voice_file){
										if (empty($voice_physical_name[$key])) {
											$validate->setError('voice_physical_upload_'.$key, "Voice Recording is required");
										}
										if (!$validate->getError('voice_physical_upload_'.$key)) {
											$allowed_mime_types = array(
												'audio/mpeg', 'audio/x-wav', 'audio/mp3', 'audio/wav',
											);
											$allowed_extensions = '*.mp3; *.wav';
											$allowed_file_size = '52428800';
											$size_in_mb = "50";
											$mime_type = $voice_physical_type[$key];
											if (!in_array($mime_type, $allowed_mime_types)) {
												$validate->setError('voice_physical_upload_'.$key, "Only " . $allowed_extensions . " file format allowed");
											} else if ($voice_physical_size[$key] > $allowed_file_size) {
												$validate->setError('voice_physical_upload_'.$key, "Maximum " . $size_in_mb . " MB file size allowed");
											}
										}
									}
								}
							}else{
								$validate->setError('voice_physical_upload_0', "Voice Recording is required");
							}
						}
					}
				} else {
					/*$validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
					$validate->string(array('required' => true, 'field' => 'c_password', 'value' => $c_password), array('required' => 'Confirm Password is required'));
					if (!$validate->getError('password')) {
						if (strlen($password) < 8 || strlen($password) > 20) {
							$validate->setError('password', 'Password must be 8-20 characters');
						} else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
							 || !preg_match('`[0-9]`', $password)) {
							// at least one digit
							$validate->setError('password', 'Valid Password is required');
						} else if (!ctype_alnum($password)) {
							$validate->setError('password', 'Special character not allowed');
						} else if (preg_match('`[?/$\*+]`', $password)) {
							$validate->setError('password', 'Password not valid');
						} else if (preg_match('`[,"]`', $password)) {
							$validate->setError('password', 'Password not valid');
						} else if (preg_match("[']", $password)) {
							$validate->setError('password', 'Password not valid');
						}
					}
					if (!$validate->getError('c_password') && !$validate->getError('password')) {
						if ($password != $c_password) {
							$validate->setError('c_password', 'Both Password must be same');
						}
					}*/

					if(!empty($product_list)){
						foreach ($product_list as $key => $product) {

							/*$sqlProduct="SELECT p.id,p.name,p.is_eSignTermsCondition FROM prd_main p where p.id=:id";
							$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$product));*/

							if(empty($product_check[$product]) && !in_array($product,$group_product_list)){
								$validate->setError('product_check_'.$product, 'Please select product agreement');
							}

							/*if(empty($product_term_check[$resProduct['id']]) && $resProduct['is_eSignTermsCondition']=='Y' ){
								$validate->setError('product_term_check_'.$resProduct['id'], 'Please agree to terms and conditions');
							}*/

						}
					}
					if(empty($product_term_check[0])){
						$validate->setError('product_term_check_0', 'Please Select Agreement');
					}
					if(checkIsset($joinder_agreement) == "Y" && empty($joinder_agreement_check)){
						$validate->setError('joinder_agreement_check', 'Please Select Joinder Agreement');	
					}
					$validate->string(array('required' => true, 'field' => 'signature_data', 'value' => $Signature_data), array('required' => 'Please draw your signature'));
					if (!$validate->getError("signature_data")) {
						$signature_data_tmp = preg_replace('#^data:image/\w+;base64,#i', '', $Signature_data);
						if(check_base64_image($signature_data_tmp) == false) {
							$validate->setError('signature_data', 'Please draw your signature');
						}
					}
				}
			}else{
				$validate->setError('application_type', 'Select Any Option');

			}

		//********* Member Verification Validation  code end   ********************
		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "payment_detail";
		}
	}
//********* step4 validation code end   ********************


if ($validate->isValid()) {
	
	if ($step >= 2) {
	  	$response['status']="success";
	  	$response['order_total']=$order_total;
	  	$response['purchase_products_array']=$purchase_products_array;
	  	
  	}
  	if ($step >= 3) {
	  	$response['order_total']=$order_total;
	  	$response['purchase_products_array']=$purchase_products_array;
	  	$response['dependent_array']=json_encode($dependent_final_array);

  	}
  	if ($step == 4) {
  		if ($enrollment_type == "quote" || !empty($_POST['lead_quote_detail_id'])) {
  			$order_id = $lead_quote_detail_res['order_ids'];
			$quote_order_id = $lead_quote_detail_res['order_ids'];
			$order_sql = "SELECT * FROM orders WHERE id=:order_id and status IN('Pending Validation','Payment Declined')";
			$order_res = $pdo->selectOne($order_sql, array(":order_id" => $order_id));

			if(!empty($order_res)){
				$order_status_res = $order_res['status'];
			}

			if(in_array($sponsor_billing_method,array('TPA','list_bill'))){
				if(!empty($order_res)){
					$pdo->delete("DELETE FROM orders WHERE id=:order_id",array("order_id"=>$order_id));
				}
				$order_sql = "SELECT * FROM group_orders WHERE id=:order_id and status IN('Pending Validation','Payment Declined')";
				$order_res = $pdo->selectOne($order_sql, array(":order_id" => $order_id));

				if(!empty($order_res)){
					$order_status_res = $order_res['status'];
				}
				
			}else if(in_array($sponsor_billing_method,array('individual'))){
				$group_order_sql = "SELECT * FROM group_orders WHERE id=:order_id and status IN('Pending Validation','Payment Declined')";
				$group_order_res = $pdo->selectOne($group_order_sql, array(":order_id" => $order_id));

				if(!empty($group_order_res)){
					$order_status_res = $group_order_res['status'];
					$pdo->delete("DELETE FROM group_orders WHERE id=:order_id",array("order_id"=>$order_id));
				}
			}

			if(!$order_res){
				$order_id=0;
				$quote_id=0;
				$lead_quote_detail_id=0;
			}
  		}

  		if (!empty($customer_id)) {
			$checkCustomerExistSql = "SELECT id,rep_id FROM customer WHERE id = :id AND is_deleted='N'";
			$checkCustomerExist = $pdo->selectOne($checkCustomerExistSql, array(':id' => makeSafe($customer_id)));
		} else {
			if ($lead_id > 0) {
				$lead_sql_tmp = "SELECT id,customer_id 
							FROM leads 
							WHERE id=:id";
				$lead_row_tmp = $pdo->selectOne($lead_sql_tmp,array(":id" => $lead_id));	
			} else {
				$lead_sql_tmp = "SELECT id,customer_id 
							FROM leads 
							WHERE 
							lead_type='Member' AND 
							email=:email AND 
							sponsor_id IN(".implode(',',$sponsor_agents).") AND 
							is_deleted='N'";
				$lead_row_tmp = $pdo->selectOne($lead_sql_tmp,array(":email" => $primary_email));	
			}
			if (!empty($lead_row_tmp['customer_id'])) {
				$checkCustomerExistSql = "SELECT id,rep_id FROM customer WHERE id=:id";
				$checkCustomerExist = $pdo->selectOne($checkCustomerExistSql,array(':id'=> $lead_row_tmp['customer_id']));
			}
		}

		if (!empty($checkCustomerExist['id'])) {
			$customer_id = $checkCustomerExist["id"];
			$customer_rep_id = $checkCustomerExist["rep_id"];
		}

		/*---------- Check Application Already Submitted ----------*/
		$lead_tracking_id = 0;
		if(!empty($customer_id) && !empty($lead_id) && !empty($order_id)) {
			$sql = 'SELECT id
					FROM lead_tracking 
					WHERE 
					status="submit_application_start" AND 
					(is_request_completed="N" OR order_status IN("Payment Approved","Pending Settlement")) AND 
					customer_id=:customer_id AND 
					lead_id=:lead_id AND 
					order_id=:order_id
					ORDER BY id DESC';
			$where = array(
				':customer_id'=>$customer_id,
				':lead_id'=>$lead_id,
				':order_id'=>$order_id
			);
			$already_submitted = $pdo->selectOne($sql,$where);
			if(!empty($already_submitted)) {
				$lead_track = array(
					'status' => 'Enrollment',
					'description' => 'Application Already Submitted - ajax_member_enrollment',
				);
				lead_tracking($lead_id,$customer_id,$lead_track);

				setNotifyError("Application Already Submitted");
				$response['status'] = 'application_already_submitted';
				header('Content-type: application/json');
				echo json_encode($response);
				exit;
			}
			$ld_desc = array(
				'page' => 'ajax_member_enrollment',
				'application_type' => $application_type,
			);
			$tracking_data = array(
				'status' => 'submit_application_start',
				'is_request_completed' => 'N',
				'order_status' => '',
				'customer_id' => $customer_id,
				'lead_id' => $lead_id,
				'order_id' => $order_id,
				'description' => json_encode($ld_desc),
			);
			$lead_tracking_id = $pdo->insert('lead_tracking',$tracking_data);
		}
		/*----------/Check Application Already Submitted ----------*/

		if ($enrollment_type == "quote" && !empty($lead_quote_detail_id)) {
				$str_plan_ids = implode(",", array_unique($PlanIdArr));
				$lead_quote_detail_where = array(
					"clause" => "id=:id",
					"params" => array(
						":id" => $lead_quote_detail_id,
					),
				);
				$pdo->update("lead_quote_details", array('updated_at' => 'msqlfunc_NOW()', 'plan_ids' => $str_plan_ids), $lead_quote_detail_where);
		}

		//********* File Upload code start ********************
			if (in_array($application_type,array('admin')) && $physical_file_name != '') {
				$physical_file_name = time() . $physical_file_name;
				move_uploaded_file($physical_file_tmp_name, $PHYSICAL_DOCUMENT_DIR . $physical_file_name);
			}

			if(in_array($application_type,array('voice_verification')) && !empty($voice_physical_name)){
				foreach($voice_physical_name as $key => $name){
					$file_name = $voice_physical_name[$key];
					if ($file_name != '') {
						$voice_physical_file_name = time() . $file_name;
						move_uploaded_file($voice_physical_tmp_name[$key], $PHYSICAL_DOCUMENT_DIR . $voice_physical_file_name);
						$voice_uploaded_fileName[] = $voice_physical_file_name;
					}
		    	}
		    }
	    //********* File Upload code end   ********************

		$primary_phone = phoneReplaceMain($primary_phone);

		//********* Customer Table code start ********************
			$customerInfo = array(
				'fname' => $primary_fname,
				'lname' => $primary_lname,
				'email' => $primary_email,
				'type' => 'Customer',
				'country_id' => 231,
				'country_name' => "United States",
				'cell_phone' => $primary_phone,
				'birth_date' => date('Y-m-d', strtotime($primary_birthdate)),
				'gender' => $primary_gender,
				'address' => $primary_address1,
				'address_2' => $primary_address2,
				'city' => $primary_city,
				'state' => $primary_state,
				'zip' => $primary_zip,

				'updated_at' => 'msqlfunc_NOW()',
				'sponsor_id' => $sponsor_row['id'],
				'level' => ($sponsor_row['level'] + 1),
				'upline_sponsors' => ($sponsor_row['upline_sponsors'] . $sponsor_row['id'] . ","),
			);
			if (!empty($primary_SSN)) {
				$customerInfo['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $primary_SSN) . "','" . $CREDIT_CARD_ENC_KEY . "')";
				$customerInfo['last_four_ssn'] = substr(str_replace("-", "", $primary_SSN), -4);
			}
			if(isset($group_company_id)){
				 $customerInfo['group_company_id'] = $group_company_id;
			}
			if (!empty($Signature_data)) {
				$signature_file_name = $primary_fname . time() . '.png';
				$signature_file_name = str_replace(' ', "", $signature_file_name);
				
				$s3Client = new S3Client([
			        'version' => 'latest',
			        'region'  => $S3_REGION,
			        'credentials'=>array(
			            'key'=> $S3_KEY,
			            'secret'=> $S3_SECRET
			        )
			    ]);

			    $result = $s3Client->putObject([
			        'Bucket' => $S3_BUCKET_NAME,
			        'Key'    => $SIGNATURE_FILE_PATH.$signature_file_name,
			        'SourceFile' => $Signature_data,
			        'ACL' => 'public-read'
			    ]);
			}


			$lead_quote_details_param = array();

			if ($customer_id > 0) {
				$upd_where = array(
					'clause' => 'id = :id',
					'params' => array(
						':id' => $customer_id,
					),
				);
				$pdo->update('customer', $customerInfo, $upd_where);

				if($is_add_product == 1) {
					$MemberEnrollment->unqualified_leads_with_duplicate_email($primary_email,$customer_id);
				}
			} else {
				$customer_rep_id = $MemberEnrollment->get_customer_id();
				$customerInfo = array_merge($customerInfo, array(
					'rep_id' => $customer_rep_id,
					'display_id' => get_display_id('customer'),
					'status' => "Customer Abandon",
					"created_at" => "msqlfunc_NOW()",
					"joined_date" => "msqlfunc_NOW()",
					"invite_at" => "msqlfunc_NOW()"
				));
				$customer_id = $pdo->insert('customer', $customerInfo);
			}

			$cust_old_status = "";
			if (!empty($customer_id)) {
				$cust_old_status = getname('customer',$customer_id,'status');
			}

			$customerSettingParams=array(
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'is_address_verified'=>$is_address_verified,
			);
			if (!empty($signature_file_name)) {
				$customerSettingParams['signature_file'] = $signature_file_name;
				$customerSettingParams['signature_date'] = 'msqlfunc_NOW()';
			}


			if (!empty($primary_height)) {
				$primary_height_array = explode(".", $primary_height);
				$customerSettingParams['height_feet']=$primary_height_array[0];
				$customerSettingParams['height_inch']=$primary_height_array[1];
			}

			if (!empty($primary_weight)) {
				$customerSettingParams['weight']=$primary_weight;
			}
			if (!empty($primary_smoking_status)) {
				$customerSettingParams['smoke_use']=$primary_smoking_status;
			}
			if (!empty($primary_tobacco_status)) {
				$customerSettingParams['tobacco_use']=$primary_tobacco_status;
			}
			if (!empty($primary_benefit_level)) {
				$customerSettingParams['benefit_level']=$primary_benefit_level;
			}
			if (!empty($primary_employment_status)) {
				$customerSettingParams['employmentStatus']=$primary_employment_status;
			}
			if (!empty($primary_salary)) {
				$customerSettingParams['salary']=$primary_salary;
			}
			if (!empty($primary_date_of_hire)) {
				$customerSettingParams['hire_date']=date('Y-m-d',strtotime($primary_date_of_hire));
			}
			if (!empty($primary_hours_per_week)) {
				$customerSettingParams['hours_per_week']=$primary_hours_per_week;
			}
			if (!empty($primary_pay_frequency)) {
				$customerSettingParams['pay_frequency']=$primary_pay_frequency;
			}
			if (!empty($primary_us_citizen)) {
				$customerSettingParams['us_citizen']=$primary_us_citizen;
			}
			if (!empty($primary_no_of_children)) {
				$customerSettingParams['no_of_children']=$primary_no_of_children;
			}
			if (!empty($primary_has_spouse)) {
				$customerSettingParams['has_spouse']=$primary_has_spouse;
			}
			if (!empty($group_coverage_period_id)) {
				$customerSettingParams['group_coverage_period_id']=$group_coverage_period_id;
			}
			if (!empty($enrolle_class)) {
				$customerSettingParams['class_id']=$enrolle_class;
			}
			if (!empty($relationship_to_group)) {
				$customerSettingParams['relationship_to_group']=$relationship_to_group;
			}
			if (!empty($relationship_date)) {
				$customerSettingParams['relationship_date']=date('Y-m-d',strtotime($relationship_date));
			}
			

			$sqlCustomerSetting="SELECT * FROM customer_settings where customer_id=:customer_id";
			$resCustomerSetting=$pdo->selectOne($sqlCustomerSetting,array(":customer_id"=>$customer_id));
			if($resCustomerSetting){
				$upd_where = array(
					'clause' => 'id = :id',
					'params' => array(
						':id' => $resCustomerSetting['id'],
					),
				);
				$pdo->update('customer_settings', $customerSettingParams, $upd_where);
			}else{
				$customerSettingParams['customer_id']=$customer_id;
				$pdo->insert('customer_settings', $customerSettingParams);
			}

			$primary_queCustom = !empty($_POST['primary_queCustom']) ? $_POST['primary_queCustom'] : array();

			if(!empty($primary_queCustom)){
				foreach ($primary_queCustom as $key => $value) {
					$sqlQue= "SELECT id FROM customer_custom_questions WHERE is_deleted='N' AND question_id=:question_id AND customer_id =:customer_id AND enrollee_type='primary'";
					$resQue=$pdo->selectOne($sqlQue,array(":customer_id"=>$customer_id,":question_id"=>$key));

					if(is_array($value)){
						$answer = implode(",", $value);
					}else{
						$answer = $value;
					}
					$queInsParams = array(
						"enrollee_type"=>'primary',
						"customer_id"=>$customer_id,
						"question_id"=>$key,
						"answer"=>$answer,
					);
					if(!empty($resQue)){
						$queInswhere = array(
							"clause" => "id=:id",
							"params" => array(
								":id" => $resQue['id'],
							),
						);
						$pdo->update("customer_custom_questions", $queInsParams, $queInswhere);
					}else{
						$pdo->insert("customer_custom_questions", $queInsParams);
					}
				}
			}
		//********* Customer Table code end   ********************

		//********* Lead Table code start ********************
			$leadInfo = array(
				'customer_id' => $customer_id,
				'fname' => $primary_fname,
				'lname' => $primary_lname,
				'email' => $primary_email,
				'birth_date' => date('Y-m-d', strtotime($primary_birthdate)),
				'cell_phone' => $primary_phone,
				'address' => $primary_address1,
				'address2' => $primary_address2,
				'city' => $primary_city,
				'state' => $primary_state,
				'zip' => $primary_zip,
				'gender' => $primary_gender,
				'updated_at' => 'msqlfunc_NOW()',
			);
			if(isset($group_company_id)){
				 $leadInfo['group_company_id'] = $group_company_id;
			}
			if (!empty($group_coverage_period_id)) {
				$leadInfo['group_coverage_id']=$group_coverage_period_id;
			}
			if (!empty($enrolle_class)) {
				$leadInfo['group_classes_id']=$enrolle_class;
			}
			if (!empty($relationship_to_group)) {
				$leadInfo['employee_type']=$relationship_to_group;
			}
			if (!empty($relationship_date)) {
				$leadInfo['hire_date']=date('Y-m-d',strtotime($relationship_date));
			}
			
			if (!empty($primary_SSN)) {
				$leadInfo['ssn_itin_num'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $primary_SSN) . "','" . $CREDIT_CARD_ENC_KEY . "')";
				$leadInfo['last_four_ssn'] = substr(str_replace("-", "", $primary_SSN), -4);
			}
			if ($lead_id > 0) {
				$where = array(
					"clause" => "id=:id",
					"params" => array(
						":id" => $lead_id,
					),
				);
				$pdo->update("leads", $leadInfo, $where);

				$lead_track = array(
					'status' => 'Edit Enrollment',
					'description' => 'Basic Info added',
				);

				lead_tracking($lead_id,$customer_id,$lead_track);

			} else {
				$lead_sql_tmp = "SELECT id,status 
								FROM leads 
								WHERE 
								lead_type='Member' AND 
								email=:email AND 
								sponsor_id IN(".implode(',',$sponsor_agents).") AND 
								is_deleted='N'";
				$lead_row_tmp = $pdo->selectOne($lead_sql_tmp,array(":email" => $primary_email));
				if ($lead_row_tmp) {
					$lead_id = $lead_row_tmp['id'];
					
					if($lead_row_tmp['status'] != "Converted") {
						$leadInfo['status']	= "Working";
					}
					$where = array(
						"clause" => "id=:id",
						"params" => array(
							":id" => $lead_id,
						),
					);
					$pdo->update("leads", $leadInfo, $where);

					$lead_track = array(
						'status' => 'Exitsting',
						'description' => 'Existing lead found and basic info updated',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				} else {
					$tempDesc = array('agent_id' => $sponsor_id,'ip_address' => $_SERVER['SERVER_ADDR'],'note' => 'Lead must complete process');

					if(isset($_FILES['physical_upload']['name']) &&  $_FILES['physical_upload']['name']!= ''){
						$tempDesc['Document'] = $physical_file_name;
					}

					if(isset($voice_physical_name) && !empty($voice_physical_name)){
						$tempDesc['Document'] = json_encode($voice_uploaded_fileName);
					}

					if(isset($voice_verification_system_code) && !empty($voice_verification_system_code)){
						$tempDesc['system_code'] = $voice_verification_system_code;
					}

					$leadInfo = array_merge($leadInfo, array(
						"customer_id" => $customer_id,
						"lead_id" => get_lead_id(),
						"employee_id" => get_lead_id(),
						'sponsor_id' => $sponsor_id,
						'status' => "Working",
						'lead_type'=>'Member',
						'generate_type' => "Manual",
						'opt_in_type' => (!empty($sponsor_row['type']) && $sponsor_row['type'] =='Group' ? 'Enrollment' : "Agent Assisted Enrollment"),
						'ip_address' => $_SERVER['SERVER_ADDR'],
						'created_at' => "msqlfunc_NOW()",
					));
					$lead_id = $pdo->insert("leads", $leadInfo);

					$lead_track = array(
						'status' => 'Created',
						'description' => 'New lead created',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

					$desc = array();
	                $desc['ac_message'] = array(
	                    'ac_red_1' => array(
	                        'href' => 'lead_details.php?id=' . md5($lead_id),
	                        'title' => $leadInfo['lead_id'],
	                    ),
	                    'ac_message_1' => ' added by Agent ',
	                    'ac_red_2' => array(
	                        'href' => 'agent_detail_v1.php?id=' . md5($sponsor_id),
	                        'title' => $sponsor_row['rep_id'],
	                    ),
	                    'ac_message_2' => ' via Agent Assisted Application',
	                );
					activity_feed(3, $sponsor_id, 'Agent', $lead_id, 'Lead', 'Lead added by Agent', $primary_fname, $primary_lname, json_encode($desc), $REQ_URL);
				}
			}
            $lead_sql = "SELECT * FROM leads WHERE id=:id";
            $lead_row = $pdo->selectOne($lead_sql,array(":id"=>$lead_id));

            $response['lead_display_id'] = $lead_row['lead_id'];
			$response['lead_name'] = $lead_row['fname'] . ' ' . $lead_row['lname'];
		//********* Lead Table code end   ********************

        //********* Waive Coverage Table code Start   ********************
            if(!empty($waive_checkbox)){
				$sponsor_type = $sponsor_row["type"];
				$waive_coverage_id=$MemberEnrollment->waive_coverage_insert($sponsor_id,$sponsor_type,$waive_checkbox,$waive_coverage_reason,$waive_coverage_other_reason,$customer_id,$primary_fname,$primary_lname);
			}
		//********* Waive Coverage Table code end   ********************            
        

		$order_display_id = 0;
		if ($order_id > 0) {
			$order_display_id = $order_res['display_id'];
		} else {
			$order_display_id = $function_list->get_order_id();
		}

		//********* Payment code start ********************
			$paymentApproved = false;
			$payment_processor = "";
			if($sponsor_billing_method == 'individual'){
				if(!empty($payment_master_id)){
					$payment_processor= getname('payment_master',$payment_master_id,'processor_id');
				}
				if (in_array($application_type,array('member_signature','voice_verification','admin')))
				{

					if ($enroll_with_post_date == "yes") {
						$paymentApproved = true;
						$txn_id = 0;
					}else if(($is_group_member == 'Y' && $only_waive_products) || (!$billing_display)){
						$paymentApproved = true;
						$txn_id = 0;
					} else {

						$api = new CyberxPaymentAPI();

						$cc_params = array();
						$cc_params['customer_id'] = $customer_rep_id;
						$cc_params['order_id'] = $order_display_id;
						$cc_params['amount'] = $order_total['grand_total'];
						$cc_params['description'] = "Product Purchase";
						$cc_params['firstname'] = ($payment_mode == 'CC' ? $name_on_card : $ach_bill_fname);
						$cc_params['lastname'] = $payment_mode == 'CC' ? '' : $ach_bill_lname;
						$cc_params['address1'] = $bill_address;
						$cc_params['address2'] = $bill_address2;
						$cc_params['city'] = $bill_city;
						$cc_params['state'] = $bill_state;
						$cc_params['zip'] = $bill_zip;
						$cc_params['country'] = $bill_country;
						$cc_params['phone'] = $primary_phone;
						$cc_params['email'] = $primary_email;
						$cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
						$cc_params['processor'] = $payment_processor;

						if ($payment_mode == "ACH") {
							$cc_params['firstname'] = $primary_fname;
							$cc_params['lastname'] = $primary_lname;
							$cc_params['address1'] = $primary_address1;
							$cc_params['address2'] = $primary_address2;
							$cc_params['city'] = $primary_city;
							$cc_params['state'] = $primary_state;
							$cc_params['zip'] = $primary_zip;
							$cc_params['country'] = 'USA';
							$cc_params['ach_account_type'] = $ach_account_type;
							$cc_params['ach_routing_number'] = !empty($routing_number) ? $routing_number : $entered_routing_number;
							$cc_params['ach_account_number'] = !empty($account_number) ? $account_number : $entered_account_number;
							$cc_params['name_on_account'] = $ach_bill_fname . ' ' . $ach_bill_lname;
							$cc_params['bankname'] = $bankname;

							$lead_track = array(
								'status' => 'Calling Processor',
								'description' => 'Attempt to take charge with ACH Payment Method',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

							$payment_res = $api->processPaymentACH($cc_params, $payment_master_id);


							if ($payment_res['status'] == 'Success') {
								$paymentApproved = true;
								$txn_id = $payment_res['transaction_id'];
							} else {
								$paymentApproved = false;
								$txn_id = $payment_res['transaction_id'];
								$payment_error = $payment_res['message'];
								$cc_params['order_type'] = 'Quote';
								$cc_params['browser'] = $BROWSER;
								$cc_params['os'] = $OS;
								$cc_params['req_url'] = $REQ_URL;
								$cc_params['err_text'] = $payment_error;
								$decline_log_id = $function_list->credit_card_decline_log($customer_id, $cc_params, $payment_res);
							}

							$lead_track = array(
								'status' => 'Processor Call End',
								'description' => 'Payment status - ' . ($payment_res['status'] == 'Success' ? 'Success' : 'Fail'),
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

						} elseif ($payment_mode == "CC") {
							$cc_params['ccnumber'] = !empty($card_number) ? $card_number : $full_card_number;
							$cc_params['card_type'] = $card_type;
							$cc_params['ccexp'] = str_pad($expiry_month, 2, "0", STR_PAD_LEFT) . substr($expiry_year, -2);

							if ($cc_params['ccnumber'] == '4111111111111114') {
								$paymentApproved = true;
								$txn_id = 0;
								$payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
								$lead_track = array(
									'status' => 'Manually Approved',
									'description' => 'Payment Approved using fake card',
								);
							
								lead_tracking($lead_id,$customer_id,$lead_track);
							} else {
								if($SITE_ENV != 'Live' && $cc_params['ccnumber'] == "4111111111111113") {
									$payment_res = '{"status":"Fail","transaction_id":"40049416880","message":"This transaction has been declined.","API_Type":"Auhtorize Global","API_Mode":"sandbox","API_response":{"status":"Fail","error_code":"2","error_message":"This transaction has been declined.","txn_id":"40049416880"}}';
	    							$payment_res = json_decode($payment_res,true);

	    							$lead_track = array(
										'status' => 'Manually Declined',
										'description' => 'Payment Declined using fake card',
									);
								
									lead_tracking($lead_id,$customer_id,$lead_track);
								} else {

									$lead_track = array(
										'status' => 'Calling Processor',
										'description' => 'Attempt to take charge with CC Payment Method',
									);
								
									lead_tracking($lead_id,$customer_id,$lead_track);

									$payment_res = $api->processPayment($cc_params, $payment_master_id);
								}
								
								if ($payment_res['status'] == 'Success') {
									$paymentApproved = true;
									$txn_id = $payment_res['transaction_id'];
								} else {
									$paymentApproved = false;
									$txn_id = $payment_res['transaction_id'];
									$payment_error = $payment_res['message'];
									$cc_params['order_type'] = 'Quote';
									$cc_params['browser'] = $BROWSER;
									$cc_params['os'] = $OS;
									$cc_params['req_url'] = $REQ_URL;
									$cc_params['err_text'] = $payment_error;
									$decline_log_id = $function_list->credit_card_decline_log($customer_id, $cc_params, $payment_res);
								}

								$lead_track = array(
									'status' => 'Processor Call End',
									'description' => 'Payment status - ' . ($payment_res['status'] == 'Success' ? 'Success' : 'Fail'),
								);
							
								lead_tracking($lead_id,$customer_id,$lead_track);
							}
						}
					}
				}
			}else if(in_array($sponsor_billing_method,array('TPA','list_bill'))){
				if (in_array($application_type,array('member_signature','voice_verification','admin')))
				{
					$paymentApproved = true;
					$txn_id = 0;

					$lead_track = array(
						'status' => 'Payment Approved',
						'description' => 'Payment Approved using application type .' . $application_type,
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}
			}
		//********* Payment code end   ********************

		//********* Order Table code start ********************
			$orderParams = array(
				'payment_type'=>$payment_mode,
				'payment_master_id' => $payment_master_id,
				'payment_processor' => $payment_processor,
				'type' => ",Customer Enrollment,",
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'browser' => $BROWSER,
				'os' => $OS,
				'req_url' => $REQ_URL,
				'updated_at' => 'msqlfunc_NOW()',
				'created_at' => 'msqlfunc_NOW()',
				'product_total' => $order_total['sub_total'],
				'sub_total' =>$order_total['sub_total'] ,
				'grand_total' => $order_total['grand_total'],
				'status' => $newStatus,
				'order_count'=>1,
			);
			if ($enroll_with_post_date == "yes") {
				$orderParams['post_date'] = date("Y-m-d", strtotime($post_date));
				$orderParams['future_payment'] = 'Y';
			} else {
				//if post date is not setup then add coverage date in post_date field
				$orderParams['post_date'] = date("Y-m-d", strtotime($lowest_coverage_date));
				$orderParams['future_payment'] = 'N';
			}

			if (in_array($application_type,array('member_signature','voice_verification','admin'))) {
				$orderParams['transaction_id'] = $txn_id;
				$orderParams['payment_processor_res'] = isset($payment_res)?json_encode($payment_res):"";

				$orderParams['status'] = ($payment_mode == "ACH") ? 'Pending Settlement' : 'Payment Approved';
				if (!$paymentApproved) {
					$orderParams['status'] = 'Payment Declined';
				}
				if ($enroll_with_post_date == "yes") {
					$orderParams['status'] = 'Post Payment';
				}
			}

			if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
                $orderParams['review_require'] = 'Y';
            }

			if ($order_id > 0) {
				$order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
				if($sponsor_billing_method == 'individual' && !$only_waive_products && $billing_display){
					$pdo->update("orders", $orderParams, $order_where);

					$lead_track = array(
						'status' => 'Existing Order Updated',
						'description' => 'Existing order found',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);


				}else if($is_group_member == 'Y' && !$only_waive_products && $billing_display && in_array($application_type,array('member'))){
					$pdo->update("group_orders", $orderParams, $order_where);

					$lead_track = array(
						'status' => 'Existing Order Updated',
						'description' => 'Existing order found',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

				}
			} else {
				$orderParams = array_merge($orderParams, array(
						'display_id' => $order_display_id,
						'customer_id' => $customer_id,
						'created_at' => 'msqlfunc_NOW()',
						'original_order_date' => 'msqlfunc_NOW()',
					)
				);
				if($sponsor_billing_method == 'individual' && !$only_waive_products && $billing_display){
					$order_id = $pdo->insert("orders", $orderParams);
					$lead_track = array(
						'status' => 'Order Created',
						'description' => 'New order Created',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}else if($is_group_member == 'Y' && !$only_waive_products && $billing_display && in_array($application_type,array('member'))){
					$order_id = $pdo->insert("group_orders", $orderParams);

					$lead_track = array(
						'status' => 'Order Created',
						'description' => 'New order Created',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}
			}	
		//********* Order Table code end   ********************

		//********* Billing Profile Table code start ********************
			$orderBillingId = 0;
			if($sponsor_billing_method == 'individual' && !$only_waive_products && $billing_display){
				$isDefaultCheck = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' AND is_default = 'Y'", array(':customer_id' => $customer_id));
				if ($payment_mode == "CC") {
					$billParams = array(
						'order_id' => $order_id,
						'customer_id' => $customer_id,
						'fname' => makeSafe($name_on_card),
						'lname' => '',
						'email' => makeSafe($primary_email),
						'country_id' => 231,
						'country' => 'United States',
						'state' => makeSafe($bill_state),
						'city' => makeSafe($bill_city),
						'zip' => makeSafe($bill_zip),
						'address' => makeSafe($bill_address),
						'address2' => makeSafe($bill_address2),
						'cvv_no' => makeSafe($cvv_no),
						'card_no' => makeSafe(substr($card_number, -4)),
						'last_cc_ach_no' => makeSafe(substr($card_number, -4)),
						'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
						'card_type' => makeSafe($card_type),
						'expiry_month' => makeSafe($expiry_month),
						'expiry_year' => makeSafe($expiry_year),
						'created_at' => 'msqlfunc_NOW()',
						'payment_mode' => 'CC',
					);

					if (!empty($order_res['billing_id'])) {
						unset($billParams['created_at']);
						$orderBillingId = $order_res['billing_id'];
						$pdo->update("order_billing_info", $billParams, array("clause" => "id=:id", "params" => array(":id" => $order_res['billing_id'])));

						$lead_track = array(
							'status' => 'Billing Info Updated',
							'description' => 'Billing Info Updated',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					} else {
						if(in_array($application_type,array('member'))){
							$billParams['card_no_full'] = $card_number;
							$lead_quote_details_param['billing_info_param'] = json_encode($billParams);
							$billParams['card_no_full'] = "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
						} else {
							$orderBillingId = $pdo->insert("order_billing_info", $billParams);

							$lead_track = array(
								'status' => 'Billing Info Updated',
								'description' => 'Billing Info Updated',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					}
					
					$billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
					$billParams['updated_at'] = 'msqlfunc_NOW()';

					/*--- We are get billing from this table when verify email/sms ---*/
					unset($billParams['order_id']);
					if($billing_profile != 'new_billing'){
						$isCustomerBillingExists = $pdo->selectOne("SELECT * FROM customer_billing_profile WHERE id = :id and is_deleted='N'", array(':id' => $billing_profile));
						if (empty($isCustomerBillingExists)) {
							$billing_profile_id = $pdo->insert("customer_billing_profile", $billParams);

							$lead_track = array(
								'status' => 'Billing Profile Created',
								'description' => 'Billing Profile Created',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						} else {
							unset($billParams['created_at']);
							$billing_profile_id = $isCustomerBillingExists['id'];
							$pdo->update("customer_billing_profile", $billParams, array("clause" => "id=:id", "params" => array(":id" => $isCustomerBillingExists['id'])));

							$lead_track = array(
								'status' => 'Billing Profile Updated',
								'description' => 'Billing Profile Updated',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					} else {
						$billing_profile_id = $pdo->insert("customer_billing_profile", $billParams);

						$lead_track = array(
							'status' => 'Billing Profile Created',
							'description' => 'Billing Profile Created',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

					}

					if(empty($isDefaultCheck)){
						$function_list->setDefaultBillingProfile($customer_id,$billing_profile_id);
					} else {
						if ($paymentApproved && in_array($application_type,array('member_signature','voice_verification','admin'))) {
			                $function_list->setDefaultBillingProfile($customer_id,$billing_profile_id);
				    	}
					}

					if(!empty($lead_quote_details_param['billing_info_param'])){
						$leadQuoteBillingParams = json_decode($lead_quote_details_param['billing_info_param'],true);
						$leadQuoteBillingParams['billing_profile_id'] = $billing_profile_id;
						$lead_quote_details_param['billing_info_param'] = json_encode($leadQuoteBillingParams);
					}
				} else {
					$billParams = array(
						'order_id' => $order_id,
						'customer_id' => $customer_id,
						'fname' => makeSafe($ach_bill_fname),
						'lname' => makeSafe($ach_bill_lname),
						'email' => makeSafe($primary_email),
						'country_id' => '231',
						'country' => 'United States',
						'state' => makeSafe($primary_state),
						'city' => makeSafe($primary_city),
						'zip' => makeSafe($primary_zip),
						'address' => makeSafe($primary_address1),
						'address2' => makeSafe($primary_address2),
						'created_at' => 'msqlfunc_NOW()',
						'payment_mode' => 'ACH',
						'ach_account_type' => $ach_account_type,
						'bankname' => $bankname,
						'last_cc_ach_no' => makeSafe(substr($account_number, -4)),
						'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
						'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
					);
					if (!empty($order_res['billing_id'])) {
						unset($billParams['created_at']);
						$orderBillingId = $order_res['billing_id'];
						$pdo->update("order_billing_info", $billParams, array("clause" => "id=:id", "params" => array(":id" => $order_res['billing_id'])));

						$lead_track = array(
							'status' => 'Billing Info updated',
							'description' => 'Billing Info updated',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

					} else {
						if(in_array($application_type,array('member'))){
							$billParams['ach_account_number'] = $account_number;
							$billParams['ach_routing_number'] = $routing_number;
							$lead_quote_details_param['billing_info_param'] = json_encode($billParams);
							$billParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
							$billParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
						} else {
							$orderBillingId = $pdo->insert("order_billing_info", $billParams);

							$lead_track = array(
								'status' => 'Billing Info Created',
								'description' => 'Billing Info Created',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					}
					
					$billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
					$billParams['updated_at'] = 'msqlfunc_NOW()';

					/*--- We are get billing from this table when verify email/sms ---*/
					unset($billParams['order_id']);
					if($billing_profile != 'new_billing'){
						$isCustomerBillingExists = $pdo->selectOne("SELECT * FROM customer_billing_profile WHERE id = :id and is_deleted='N'", array(":id" => $billing_profile));
						if (empty($isCustomerBillingExists)) {
							$billing_profile_id = $pdo->insert("customer_billing_profile", $billParams);

							$lead_track = array(
								'status' => 'Billing Profile Created',
								'description' => 'Billing Profile Created',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

						} else {
							unset($billParams['created_at']);
							$billing_profile_id = $isCustomerBillingExists['id'];
							$pdo->update("customer_billing_profile", $billParams, array("clause" => "id=:id", "params" => array(":id" => $isCustomerBillingExists['id'])));

							$lead_track = array(
								'status' => 'Billing Profile Updated',
								'description' => 'Billing Profile Updated',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					} else {
						$billing_profile_id = $pdo->insert("customer_billing_profile", $billParams);

						$lead_track = array(
							'status' => 'Billing Profile Created',
							'description' => 'Billing Profile Created',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}

					if(empty($isDefaultCheck)){
						$function_list->setDefaultBillingProfile($customer_id,$billing_profile_id);
					} else {
						if ($paymentApproved && in_array($application_type,array('member_signature','voice_verification','admin'))) {
			                $function_list->setDefaultBillingProfile($customer_id,$billing_profile_id);
				    	}
					}

					if(!empty($lead_quote_details_param['billing_info_param'])){
						$leadQuoteBillingParams = json_decode($lead_quote_details_param['billing_info_param'],true);
						$leadQuoteBillingParams['billing_profile_id'] = $billing_profile_id;
						$lead_quote_details_param['billing_info_param'] = json_encode($leadQuoteBillingParams);
					}
				}
			}
		//********* Billing Profile Table code end   ********************

		//********* Physical File code start ********************
			if (isset($physical_file_name) && $physical_file_name != "" && in_array($application_type,array('admin'))) {
				$enroll_application_res = $pdo->selectOne("SELECT * FROM enroll_application WHERE customer_id = :customer_id AND order_id = :order_id", array(":customer_id" => $customer_id, ":order_id" => $order_id));

				if ($enroll_application_res) {
					$enroll_application_data = array(
						'file_name' => $physical_file_name,
						'updated_at' => 'msqlfunc_NOW()',
					);
					$enroll_application_data_where = array("clause" => "id=:id", "params" => array(":id" => $enroll_application_res['id']));
					$pdo->update("enroll_application", $enroll_application_data, $enroll_application_data_where);

					$lead_track = array(
						'status' => 'Enrollment Application Updated',
						'description' => 'Application Updated',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

					$activity_feed_data = array(
						'url' => $enrollment_url,
						'username' => $sponsor_row['user_name'],
						'agent_id' => $sponsor_id,
						'order_id' => $order_id,
						'file_name' => $physical_file_name,
					);
					activity_feed(3, $customer_id, 'customer', $customer_id, 'customer', 'Application is Updated', $primary_fname, $primary_lname, json_encode($activity_feed_data));
				} else {
					$enroll_application_data = array(
						'is_approved' => 'Y',
						'customer_id' => $customer_id,
						'order_id' => $order_id,
						'file_name' => $physical_file_name,
						'updated_at' => 'msqlfunc_NOW()',
						'created_at' => 'msqlfunc_NOW()',
					);
					$enroll_app_id = $pdo->insert("enroll_application", $enroll_application_data);

					$lead_track = array(
						'status' => 'Enrollment Application Created',
						'description' => 'Application Created',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

					$activity_feed_data = array(
						'url' => $enrollment_url,
						'username' => $sponsor_row['user_name'],
						'agent_id' => $sponsor_id,
						'order_id' => $order_id,
						'file_name' => $physical_file_name,
					);
					activity_feed(3, $customer_id, 'customer', $customer_id, 'customer', 'Application is Added', $primary_fname, $primary_lname, json_encode($activity_feed_data));
				}
				addAdminNotification(0, 7, "{HOST}/member_detail.php?id=" . $customer_id, 0, 'N', $sponsor_id, $customer_id);
			}

			if (((isset($voice_physical_name) && !empty($voice_physical_name)) || !empty($voice_verification_system_code)) && in_array($application_type,array('voice_verification'))) {

				$enroll_application_res = $pdo->selectOne("SELECT * FROM enroll_application WHERE customer_id = :customer_id AND order_id = :order_id", array(":customer_id" => $customer_id, ":order_id" => $order_id));

				if ($enroll_application_res) {
					if($voice_application_type == "by_system_code") {
						$enroll_application_data = array(
							'is_approved' => 'Y',
							'voice_application_type' => $voice_application_type,
							'system_code' => $voice_verification_system_code,
							'file_name' => '',
							'updated_at' => 'msqlfunc_NOW()',
						);

						$lead_track = array(
							'status' => 'Used Voice Verification',
							'description' => 'Used voice verification and used system code',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

					} else {
						$enroll_application_data = array(
							'is_approved' => 'Y',
							'is_voice_msg' => 'Y',
							'voice_application_type' => $voice_application_type,
							'system_code' => '',
							'file_name' => json_encode($voice_uploaded_fileName),
							'updated_at' => 'msqlfunc_NOW()',
						);

						$lead_track = array(
							'status' => 'Used Voice Verification',
							'description' => 'Used voice verification and used voice file',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}

					$enroll_application_data_where = array("clause" => "id=:id", "params" => array(":id" => $enroll_application_res['id']));
					$pdo->update("enroll_application", $enroll_application_data, $enroll_application_data_where);

					$lead_track = array(
						'status' => 'Application Updated',
						'description' => 'updated voice verification type',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				} else {
					if($voice_application_type == "by_system_code") {
						$enroll_application_data = array(
							'is_approved' => 'Y',
							'customer_id' => $customer_id,
							'order_id' => $order_id,
							'voice_application_type' => $voice_application_type,
							'system_code' => $voice_verification_system_code,
							'file_name' => '',
							'is_voice_msg' => 'Y',
							'updated_at' => 'msqlfunc_NOW()',
							'created_at' => 'msqlfunc_NOW()',
						);

						$lead_track = array(
							'status' => 'Used Voice Verification',
							'description' => 'Used voice verification and used system code',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					} else {
						$enroll_application_data = array(
							'is_approved' => 'Y',
							'customer_id' => $customer_id,
							'order_id' => $order_id,
							'voice_application_type' => $voice_application_type,
							'system_code' => '',
							'file_name' => json_encode($voice_uploaded_fileName),
							'is_voice_msg' => 'Y',
							'updated_at' => 'msqlfunc_NOW()',
							'created_at' => 'msqlfunc_NOW()',
						);

						$lead_track = array(
							'status' => 'Used Voice Verification',
							'description' => 'Used voice verification and used voice file',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}
					$enroll_app_id = $pdo->insert("enroll_application", $enroll_application_data);

					$lead_track = array(
						'status' => 'Application Created',
						'description' => 'inserted voice verification type',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}
			}
		//********* Physical File code end   ********************

		//********* Order Detail Table code start ********************
			if (in_array($application_type,array('member_signature','voice_verification','admin'))) {
				$websiteSubscriptionArr = array();
				$subscription_ids = array();

				if(isset($quote_order_id) && !empty($quote_order_id) && ($order_status_res == 'Pending Validation')){

					$pdo->delete("DELETE  FROM customer_enrollment 
		                            WHERE website_id IN (SELECT ws.id FROM website_subscriptions ws WHERE ws.customer_id=:cust_id AND ws.status IN ('Post Payment','Pending') AND ws.last_order_id=:order_id) ", array(":cust_id" => $customer_id, ":order_id" =>$quote_order_id));

					$pdo->delete("DELETE FROM website_subscriptions WHERE customer_id=:cust_id and status IN ('Post Payment','Pending') AND last_order_id=:order_id", array(":cust_id" => $customer_id, ":order_id" =>$quote_order_id));

					
					// $pdo->delete("DELETE FROM order_details WHERE order_id=:order_id",array("order_id"=>$quote_order_id));

					// $pdo->delete("DELETE FROM group_order_details WHERE order_id=:order_id",array("order_id"=>$quote_order_id));

					$od_where = array(
						"clause" => "order_id=:o_id",
						"params" => array(
							":o_id" => $quote_order_id,
						),
					);
					$pdo->update("group_order_details", array('is_deleted' => 'Y'), $od_where);
					$pdo->update("order_details", array('is_deleted' => 'Y'), $od_where);
					

					$pdo->delete("DELETE FROM customer_dependent WHERE order_id=:order_id AND status != 'Active'",array(":order_id"=>$quote_order_id));
				} else {

					$pdo->delete("DELETE  FROM customer_enrollment 
		                            WHERE website_id IN (SELECT ws.id FROM website_subscriptions ws WHERE ws.customer_id=:cust_id AND ws.status='Pending' AND ws.last_order_id=:order_id) ", array(":cust_id" => $customer_id, ":order_id" => $order_id));

					$pdo->delete("DELETE FROM website_subscriptions WHERE customer_id=:cust_id and status='Pending' AND last_order_id=:order_id", array(":cust_id" => $customer_id, ":order_id" => $order_id));
					
					// $pdo->delete("DELETE FROM order_details WHERE order_id=:order_id",array(":order_id"=>$order_id));
				
					// $pdo->delete("DELETE FROM group_order_details WHERE order_id=:order_id",array(":order_id"=>$order_id));
					$pdo->delete("DELETE FROM customer_dependent WHERE order_id=:order_id AND status != 'Active'",array(":order_id"=>$order_id));

					$od_where = array(
						"clause" => "order_id=:o_id",
						"params" => array(
							":o_id" => $order_id,
						),
					);
					$pdo->update("group_order_details", array('is_deleted' => 'Y'), $od_where);
					$pdo->update("order_details", array('is_deleted' => 'Y'), $od_where);
				}
			} else {
				if(isset($quote_order_id) && !empty($quote_order_id) && ($order_status_res == 'Pending Validation')){
					// $pdo->delete("DELETE FROM order_details WHERE order_id=:order_id",array("order_id"=>$quote_order_id));
					// $pdo->delete("DELETE FROM group_order_details WHERE order_id=:order_id",array("order_id"=>$quote_order_id));

					$pdo->delete("DELETE FROM customer_dependent WHERE order_id=:order_id AND status != 'Active'",array(":order_id"=>$quote_order_id));

					$pdo->delete("DELETE  FROM customer_enrollment 
		                            WHERE website_id IN (SELECT ws.id FROM website_subscriptions ws WHERE ws.customer_id=:cust_id AND ws.status='Pending' AND ws.last_order_id=:order_id) ", array(":cust_id" => $customer_id, ":order_id" => $quote_order_id));

					$pdo->delete("DELETE FROM website_subscriptions WHERE customer_id=:cust_id and status='Pending' AND last_order_id=:order_id", array(":cust_id" => $customer_id, ":order_id" => $quote_order_id));

					$od_where = array(
						"clause" => "order_id=:o_id",
						"params" => array(
							":o_id" => $quote_order_id,
						),
					);
					$pdo->update("group_order_details", array('is_deleted' => 'Y'), $od_where);
					$pdo->update("order_details", array('is_deleted' => 'Y'), $od_where);


				} else {
					// $pdo->delete("DELETE FROM order_details WHERE order_id=:order_id",array(":order_id"=>$order_id));
					// $pdo->delete("DELETE FROM group_order_details WHERE order_id=:order_id",array(":order_id"=>$order_id));

					$pdo->delete("DELETE  FROM customer_enrollment 
		                            WHERE website_id IN (SELECT ws.id FROM website_subscriptions ws WHERE ws.customer_id=:cust_id AND ws.status='Pending' AND ws.last_order_id=:order_id) ", array(":cust_id" => $customer_id, ":order_id" => $order_id));

					$pdo->delete("DELETE FROM website_subscriptions WHERE customer_id=:cust_id and status='Pending' AND last_order_id=:order_id", array(":cust_id" => $customer_id, ":order_id" => $order_id));
					
					$pdo->delete("DELETE FROM customer_dependent WHERE order_id=:order_id AND status != 'Active'",array(":order_id"=>$order_id));

					$od_where = array(
						"clause" => "order_id=:o_id",
						"params" => array(
							":o_id" => $order_id,
						),
					);
					$pdo->update("group_order_details", array('is_deleted' => 'Y'), $od_where);
					$pdo->update("order_details", array('is_deleted' => 'Y'), $od_where);
				}
			}
			//***** get minimum end coverage ********
				$endCoverageDateArr= array();
				foreach ($purchase_products_array as $key => $product) {
					if($product['type']=='Fees') {
							$member_payment_type=$product['payment_type_subscription'];
							$start_coverage_date =$coverage_dates[$product['fee_product_id']];
					} else {
						$member_payment_type=$product['payment_type_subscription'];
						$start_coverage_date =$coverage_dates[$product['product_id']];
					}
					$product_dates=$enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);

					$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
					array_push($endCoverageDateArr, $endCoveragePeriod);
				}
			//***** get minimum end coverage ********


			foreach ($purchase_products_array as $key => $product) {
				$website_id = 0;

				if($product['type']=='Fees') {
						$member_payment_type=$product['payment_type_subscription'];
						$start_coverage_date =$coverage_dates[$product['fee_product_id']];
				} else {
					$member_payment_type=$product['payment_type_subscription'];
					$start_coverage_date =$coverage_dates[$product['product_id']];
				}
				$product_dates=$enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);

				$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
				$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
				$eligibility_date = date('Y-m-d',strtotime($product_dates['eligibility_date']));

				$shortTermProductDetails = $MemberEnrollment->shortTermDisabilityProductDetails($product['product_id']);
				$is_short_term_disability_product = 'N';
				if($shortTermProductDetails){
					$is_short_term_disability_product = $shortTermProductDetails['is_short_term_disablity_product'];
				}

				//********* Website Subcription,Customer enrollment Table code start *********
					if (in_array($application_type,array('member_signature','voice_verification','admin'))) {

						$web_payment_type = $payment_mode == 'ACH' ? 'ACH' : 'CC';
						if($sponsor_billing_method != 'individual'){
							$web_payment_type = $sponsor_billing_method;
						}
						$web_subscription_data = array(
							'product_id' => $product['product_id'],
							'fee_applied_for_product'=>!empty($product['fee_product_id']) ? $product['fee_product_id'] : 0,
							'plan_id' => $product['matrix_id'],
							'prd_plan_type_id' => $product['plan_id'],
							'product_code' => $product['product_code'],
							'product_type' => makeSafe($product['type']),
							'last_purchase_date' => 'msqlfunc_NOW()',
							'last_order_id' => $order_id,
							'total_attempts' => 0,
							'price' => $product['price'],
							'member_price' => 0,
							'group_price' => 0,
							'qty' => $product['qty'],
							'payment_type' => $web_payment_type,
							'updated_at' => 'msqlfunc_NOW()',
							'termination_date'=>NULL,
							'term_date_set' => NULL,
							'admin_id' => ($enrollmentLocation == "adminSide" ? $_SESSION['admin']['id'] : 0),
							'application_type'=>$enrollment_application_type,
						);
						if(!empty($primary_benefit_amount_arr) && isset($primary_benefit_amount_arr[$product['product_id']])){
							$web_subscription_data['benefit_amount'] = $primary_benefit_amount_arr[$product['product_id']];
						}
						if($is_short_term_disability_product == 'Y' && !empty($primary_monthly_benefit) && isset($primary_monthly_benefit[$product['product_id']])){
							$web_subscription_data['benefit_amount'] = $primary_monthly_benefit[$product['product_id']];
						}

						
						if(isset($primary_annual_salary[$product['product_id']])){
							$web_subscription_data['annual_salary'] = $primary_annual_salary[$product['product_id']];
						}
						if(isset($primary_monthly_salary_percentage[$product['product_id']])){
							$web_subscription_data['monthly_benefit_percentage'] = $primary_monthly_salary_percentage[$product['product_id']];
						}
						if(!empty($primary_in_patient_benefit_arr) && isset($primary_in_patient_benefit_arr[$product['product_id']])){
							$web_subscription_data['in_patient_benefit'] = $primary_in_patient_benefit_arr[$product['product_id']];
						}
						if(!empty($primary_out_patient_benefit_arr) && isset($primary_out_patient_benefit_arr[$product['product_id']])){
							$web_subscription_data['out_patient_benefit'] = $primary_out_patient_benefit_arr[$product['product_id']];
						}
						if(!empty($primary_monthly_income_arr) && isset($primary_monthly_income_arr[$product['product_id']])){
							$web_subscription_data['monthly_income'] = $primary_monthly_income_arr[$product['product_id']];
						}
						if(!empty($primary_benefit_percentage_arr) && isset($primary_benefit_percentage_arr[$product['product_id']])){
							$web_subscription_data['benefit_percentage'] = $primary_benefit_percentage_arr[$product['product_id']];
						}


						$member_setting = $memberSetting->get_status_by_payment($paymentApproved,"",($enroll_with_post_date == "yes" ? true : false),$cust_old_status,array("is_from_enrollment" => true));

						$web_subscription_data["status"] = $member_setting['policy_status'];
						// $web_subscription_data["status"] = 'Active';

						// if (!$paymentApproved) {
						// 	$web_subscription_data['status'] = 'Payment Declined';
						// }

						// if ($enroll_with_post_date == "yes") {
						// 	$web_subscription_data['status'] = "Post Payment";
						// }

						if ($product['payment_type'] == 'Recurring') {
							$next_purchase_date=$enrollDate->getNextBillingDateFromCoverageList($endCoverageDateArr,$startCoveragePeriod,$customer_id,$product['payment_type_subscription']);
							$web_subscription_data['next_purchase_date'] = date('Y-m-d',strtotime($next_purchase_date));
						}else{
							$web_subscription_data['is_onetime'] = 'Y';
							$web_subscription_data['next_purchase_date'] = date('Y-m-d');
						}

						if(isset($product['is_aca_product']) && $product['is_aca_product'] == 'Y'){
							$web_subscription_data['is_aca_product'] = 'Y';
						}
						$web_subscription_data['eligibility_date'] = $eligibility_date;

						$web_subscription_data['start_coverage_period'] = $startCoveragePeriod;
						$web_subscription_data['end_coverage_period'] = $endCoveragePeriod;

						if(!empty($primary_state)){
							$web_subscription_data['issued_state']=$primary_state;
						}

						if($is_group_member == 'Y'){
							
							$web_subscription_data['member_price'] = isset($product['member_price']) ? $product['member_price'] : 0;
							$web_subscription_data['group_price'] = isset($product['group_price']) ? $product['group_price'] : 0;
							$web_subscription_data['contribution_type'] = isset($product['contribution_type']) ? $product['contribution_type'] : '';
							$web_subscription_data['contribution_value'] = isset($product['contribution_value']) ? $product['contribution_value'] : '';

						}

						/*------ Set Termination Date for Healthy Step ------*/
						if($product['product_type'] == "Healthy Step") {
							if($product['is_member_benefits'] == "Y" && $product['is_fee_on_renewal'] == "Y" && $product['fee_renewal_type'] == "Renewals" && $product['fee_renewal_count'] > 0) {
								$tmp_fee_renewal_count = $product['fee_renewal_count'];
								$tmp_start_coverage_date = $startCoveragePeriod;
								$tmp_termination_date = $endCoveragePeriod;
								while ($tmp_fee_renewal_count > 0) {
									$product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
									$tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
									$tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
									$tmp_fee_renewal_count--;
								}
								$web_subscription_data['termination_date'] = $tmp_termination_date;
								$web_subscription_data['term_date_set'] = date('Y-m-d');
								$web_subscription_data['termination_reason'] = 'Policy Change';
							}
						}
						/*------/Set Termination Date for Healthy Step ------*/

						$web_subscription_data = array_merge($web_subscription_data, array(
							'website_id' => $function_list->get_website_id(),
							'customer_id' => $customer_id,
							'created_at' => 'msqlfunc_NOW()',
							'purchase_date' => 'msqlfunc_NOW()',
						));
						$website_id = $pdo->insert("website_subscriptions", $web_subscription_data);


						$lead_track = array(
							'status' => 'Created Policy',
							'description' => 'Created Plan : ' . $web_subscription_data['website_id'],
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

						if($is_group_member == 'Y' && $web_payment_type=='TPA'){
							$subscription_param = array();
							$subscription_param['start_coverage_period'] = $startCoveragePeriod;
							$subscription_param['end_coverage_period'] = $endCoveragePeriod;
							$subscription_param['renew_count'] = 1;
							$subscription_param['created_at'] = 'msqlfunc_NOW()';
							$subscription_param['updated_at'] = 'msqlfunc_NOW()';
							$subscription_param['subscription_id'] = $website_id;
							$member_coverage = $pdo->insert("tpa_member_coverage", $subscription_param);

							$lead_track = array(
								'status' => 'TPA Member Coverage Added',
								'description' => 'TPA Member Plan Added',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}


						$subscription_ids[] = $website_id;

						$website_subscriptions_history_msg = 'Initial Setup Successful' . ($enroll_with_post_date == "yes" ? " With Post Date " . date("m/d/Y", strtotime($post_date)) : "");

						if (in_array($application_type,array('member_signature','voice_verification','admin'))) {
							$website_subscriptions_history_msg .= (!$paymentApproved ? "(Declined)" : "");
						}

						$web_history_data = array(
							'customer_id' => $customer_id,
							'website_id' => $website_id,
							'product_id' => $product['product_id'],
							'fee_applied_for_product'=>!empty($product['fee_product_id']) ? $product['fee_product_id'] : 0,
							'plan_id' => $product['matrix_id'],
							'prd_plan_type_id' => $product['plan_id'],
							'order_id' => $order_id,
							'status' => 'Setup',
							'message' => $website_subscriptions_history_msg,
							'authorize_id' => makeSafe($txn_id),
							'processed_at' => 'msqlfunc_NOW()',
							'created_at' => 'msqlfunc_NOW()',
							'admin_id' => ($enrollmentLocation == "adminSide" ? $_SESSION['admin']['id'] : 0),
						);
						$pdo->insert("website_subscriptions_history", $web_history_data);

						$lead_track = array(
							'status' => 'History added',
							'description' => 'Inserted in website subscription history',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

						$sub_products = $function_list->get_sub_product($product['product_id']);
						$enrollParams = array(
							'website_id' => $website_id,
							'company_id' => $product['company_id'],
							'sub_product' =>$sub_products,
							'sponsor_id' => $sponsor_row['id'],
							'upline_sponsors' => $sponsor_row['upline_sponsors'] . $sponsor_row['id'] . ",",
							'level' => $sponsor_row['level']+1,
						);
						$customer_enrollment_id = $pdo->insert("customer_enrollment", $enrollParams);

						$lead_track = array(
							'status' => 'Customer Enrollment Table',
							'description' => 'Inserted in customer application table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

						$websiteSubscriptionArr[] = array(
							'eligibility_date' => $eligibility_date,
							'website_id' => $website_id,
							'customer_id' => $customer_id,
							'product_id' => $product['product_id'],
							'plan_id' => $product['matrix_id'],
							'prd_plan_type_id' => $product['plan_id'],
						);

					}
				//********* Website Subcription,Customer enrollment Table code end   *********

				
				$insOrderDetailSql = array(
					'website_id'=>$website_id,
					'order_id' => $order_id,
					'product_id' => $product['product_id'],
					'fee_applied_for_product'=>!empty($product['fee_product_id']) ? $product['fee_product_id'] : 0,
					'plan_id' => $product['matrix_id'],
					'prd_plan_type_id' => $product['plan_id'],
					'product_type' => $product['type'],
					'product_name' => $product['product_name'],
					'product_code' => $product['product_code'],
					'start_coverage_period' => $startCoveragePeriod,
					'end_coverage_period' => $endCoveragePeriod,
					'qty' => $product['qty'],
					'renew_count'=>'1',
				);

				if (isset($product_wise_dependents[$product['product_id']]) && !empty($product_wise_dependents[$product['product_id']])) {
					$insOrderDetailSql['family_member'] = count($product_wise_dependents[$product['product_id']]);
				}
				$insOrderDetailSql['unit_price'] = $product['price'];
				$insOrderDetailSql['member_price'] = 0;
				$insOrderDetailSql['group_price'] = 0;
				if($is_group_member == 'Y') {
					$insOrderDetailSql['member_price'] = isset($product['member_price']) ? $product['member_price'] : 0;
					$insOrderDetailSql['group_price'] = isset($product['group_price']) ? $product['group_price'] : 0;
					$insOrderDetailSql['contribution_type'] = isset($product['contribution_type']) ? $product['contribution_type'] : '';
					$insOrderDetailSql['contribution_value'] = isset($product['contribution_value']) ? $product['contribution_value'] : '';
				}

				if($sponsor_billing_method == 'individual' && !$only_waive_products && $billing_display){	
					$detail_insert_id = $pdo->insert("order_details", $insOrderDetailSql);

					$lead_track = array(
						'status' => 'Inserted',
						'description' => 'Inserted in order details table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

				}else if($is_group_member == 'Y' && !$only_waive_products && $billing_display && in_array($application_type,array('member'))){
					$detail_insert_id = $pdo->insert("group_order_details", $insOrderDetailSql);
					
					$lead_track = array(
						'status' => 'Inserted',
						'description' => 'Inserted in group order details table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}

				$insert_customer_benefit_amount = false;
				$benefitAmountParams = array(
					'customer_id' => $customer_id,
					'product_id' =>$product['product_id'],
					'type'=>'Primary',
				);
				if(!empty($primary_benefit_amount_arr) && isset($primary_benefit_amount_arr[$product['product_id']])){
					$benefitAmountParams['amount'] = $primary_benefit_amount_arr[$product['product_id']];
					$insert_customer_benefit_amount = true;
				}
				if($is_short_term_disability_product == 'Y' && isset($primary_monthly_benefit[$product['product_id']])){
					$benefitAmountParams['amount'] = $primary_monthly_benefit[$product['product_id']];
					$insert_customer_benefit_amount = true;
				}
				if(!empty($primary_in_patient_benefit_arr) && isset($primary_in_patient_benefit_arr[$product['product_id']])){
					$benefitAmountParams['in_patient_benefit'] = $primary_in_patient_benefit_arr[$product['product_id']];
					$insert_customer_benefit_amount = true;
				}
				if(!empty($primary_out_patient_benefit_arr) && isset($primary_out_patient_benefit_arr[$product['product_id']])){
					$benefitAmountParams['out_patient_benefit'] = $primary_out_patient_benefit_arr[$product['product_id']];
					$insert_customer_benefit_amount = true;
				}
				if(!empty($primary_monthly_income_arr) && isset($primary_monthly_income_arr[$product['product_id']])){
					$benefitAmountParams['monthly_income'] = $primary_monthly_income_arr[$product['product_id']];
					$insert_customer_benefit_amount = true;
				}
				if(!empty($primary_benefit_percentage_arr) && isset($primary_benefit_percentage_arr[$product['product_id']])){
					$benefitAmountParams['benefit_percentage'] = $primary_benefit_percentage_arr[$product['product_id']];
					$insert_customer_benefit_amount = true;
				}
				if(!empty($primary_monthly_benefit[$product['product_id']])){
					$benefitAmountParams['monthly_benefit'] = $primary_monthly_benefit[$product['product_id']];
    				$insert_customer_benefit_amount = true;
				}
				if(!empty($out_of_pocket_maximum[$product['product_id']])){
					$benefitAmountParams['out_of_pocket_maximum'] = $out_of_pocket_maximum[$product['product_id']];
    				$insert_customer_benefit_amount = true;
				}

				if($insert_customer_benefit_amount){

					$sqlAmount="SELECT id FROM customer_benefit_amount where is_deleted='N' AND customer_id=:customer_id AND product_id=:product_id AND type='Primary'";
					$resAmount = $pdo->selectOne($sqlAmount,array(":customer_id"=>$customer_id,":product_id"=>$product['product_id']));

					if(!empty($resAmount)){
						$benefitAmountWhere = array("clause" => "id=:id", "params" => array(":id" => $resAmount['id']));
						$pdo->update("customer_benefit_amount", $benefitAmountParams,$benefitAmountWhere);
						$lead_track = array(
							'status' => 'Updated',
							'description' => 'Updated customer benefit amount table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}else{
						$benefit_amount_id = $pdo->insert("customer_benefit_amount", $benefitAmountParams);

						$lead_track = array(
							'status' => 'Inserted',
							'description' => 'Inserted in customer benefit amount table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

					}
				}

			}
		//********* Order Detail Table code end   ********************

		//********** Transaction Table code start **************
		if($sponsor_billing_method == 'individual' && !$only_waive_products && $billing_display){	
			if ($enroll_with_post_date != "yes" && in_array($application_type,array('member_signature','voice_verification','admin'))) {
					$other_params=array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res,'cc_decline_log_id'=>checkIsset($decline_log_id));
	                if ($paymentApproved){
	                	if($payment_mode != "ACH"){
	                    	//************* insert transaction code start ***********************
	                        	$transactionInsId=$function_list->transaction_insert($order_id,'Credit','New Order','Transaction Approved',0,$other_params);

	                        	$lead_track = array(
									'status' => 'Inserted',
									'description' => 'Inserted in transaction table',
								);
							
								lead_tracking($lead_id,$customer_id,$lead_track);
	                    	//**************** insert transaction code end ***********************
	                    }else{
	                    	$transactionInsId=$function_list->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params);

	                    	$lead_track = array(
								'status' => 'Inserted',
								'description' => 'Inserted in transaction table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
	                    }

	                }else{
	                    //************************ insert transaction code start ***********************
	                   		$other_params["reason"] = checkIsset($payment_error);
	                        $transactionInsId=$function_list->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined',0,$other_params);

	                        $lead_track = array(
								'status' => 'Inserted',
								'description' => 'Inserted in transaction table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
	                    //************************ insert transaction code end ***********************
	                }
	    	}else if ($enroll_with_post_date == "yes" && in_array($application_type,array('member_signature','voice_verification','admin'))) {
	    			$other_params=array();
					$transactionInsId=$function_list->transaction_insert($order_id,'Credit','Post Payment','Post Transaction',0,$other_params);

					$lead_track = array(
						'status' => 'Inserted',
						'description' => 'Inserted in transaction table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
			}
		}
		//********** Transaction Table code end **************
		
		//********* Order Table update subscription id code start ********************
			if($sponsor_billing_method == 'individual' && !$only_waive_products && $billing_display){
				if (!empty($subscription_ids)) {
					$pdo->update("orders", array('subscription_ids' => implode(',', $subscription_ids)), array("clause" => "id=:id", "params" => array(":id" => $order_id)));
				}
			}
		//********* Order Table update subscription id code end   ********************

		//********* dependent table code start ********************

			if(count($dependent_final_array) > 0){

				foreach ($dependent_final_array as $dp) {

					$prd_id = $dp["product_id"];
					$plan_id = $dp["plan_id"];
					$prd_mat_id = $dp["matrix_id"];
					$is_gap_product = 'N';
					if($prd_id){
						$gap_res = getname('prd_main',$prd_id,'is_gap_plus_product','id');
						if($gap_res == 'Y'){
					        $is_gap_product = 'Y';
					    }
					}
					$sqlWeb = "SELECT id FROM website_subscriptions where customer_id=:customer_id AND product_id=:product_id  AND prd_plan_type_id=:prd_plan_type_id AND (termination_date is NULL OR termination_date = '')";
					$resWeb= $pdo->selectOne($sqlWeb,array(":customer_id"=>$customer_id,":product_id"=>$prd_id,":prd_plan_type_id"=>$plan_id));
					$dep_website_id=0;
					if(!empty($resWeb)){
						$dep_website_id=$resWeb['id'];
					}

					$product_wise_dependents[$dp["product_id"]] = $dp["dependent"];
					foreach ($dp["dependent"] as $d) {
						if($dep_website_id > 0) {
							$cust_dep_sql = "SELECT id FROM customer_dependent WHERE website_id=:website_id AND cd_profile_id=:cd_profile_id";
							$cust_dep_where = array(":website_id" =>$dep_website_id,":cd_profile_id" =>$d['cd_profile_id']);
							$cust_dep_res = $pdo->selectOne($cust_dep_sql,$cust_dep_where);
						} else {

							$cust_dep_sql = "SELECT id FROM customer_dependent WHERE product_plan_id=:product_plan_id AND cd_profile_id=:cd_profile_id AND customer_id=:customer_id";
							$cust_dep_where = array(":product_plan_id" =>$prd_mat_id,":cd_profile_id" =>$d['cd_profile_id'],":customer_id" =>$customer_id);
							$cust_dep_res = $pdo->selectOne($cust_dep_sql,$cust_dep_where);
						}

						$relation=$d['dependent_relation_input'];
						$cd_profile_id = !empty($d['cd_profile_id']) ? $d['cd_profile_id'] :0;
						
						$dependent_params = array(
							'website_id' => $dep_website_id,
							'customer_id' => $customer_id,
							'order_id' => (isset($order_id) && $order_id != 0) ? $order_id : 0,
							'product_id' => (isset($prd_id)) ? $prd_id : 0,
							'product_plan_id' => (isset($prd_mat_id)) ? $prd_mat_id : 0,
							'prd_plan_type_id' => (isset($plan_id)) ? $plan_id : 0,
							'relation' => $d["dependent_relation"],
							'fname' => $d[$relation."_fname"],
							'lname' => $d[$relation."_lname"],
							'birth_date' => date('Y-m-d', strtotime($d[$relation."_birthdate"])),
							'gender' => $d[$relation."_gender"],
							'status' => ($paymentApproved ? 'Active' : 'Pending Payment'),
							'is_deleted' => 'N',
							'updated_at' => 'msqlfunc_NOW()',
						);
						if($is_gap_product == 'Y'){
							$spouse_benefit_amount_arr[$d['dependent_id']][$prd_id] = $primary_benefit_amount_arr[$prd_id];
						}

						if($d['relation']=="Spouse"){
							$dependent_params['benefit_amount'] = isset($spouse_benefit_amount_arr[$d['dependent_id']][$prd_id]) ? $spouse_benefit_amount_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['in_patient_benefit'] = isset($spouse_in_patient_benefit_arr[$d['dependent_id']][$prd_id]) ? $spouse_in_patient_benefit_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['out_patient_benefit'] = isset($spouse_out_patient_benefit_arr[$d['dependent_id']][$prd_id]) ? $spouse_out_patient_benefit_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['monthly_income'] = isset($spouse_monthly_income_arr[$d['dependent_id']][$prd_id]) ? $spouse_monthly_income_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['benefit_percentage'] = isset($spouse_benefit_percentage_arr[$d['dependent_id']][$prd_id]) ? $spouse_benefit_percentage_arr[$d['dependent_id']][$prd_id] : 0;
						}else if($d['relation']=="Child"){
							if($is_gap_product == 'Y'){
								$child_benefit_amount_arr[$d['dependent_id']][$prd_id] = $primary_benefit_amount_arr[$prd_id];
							}
							$dependent_params['benefit_amount'] = isset($child_benefit_amount_arr[$d['dependent_id']][$prd_id]) ? $child_benefit_amount_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['in_patient_benefit'] = isset($child_in_patient_benefit_arr[$d['dependent_id']][$prd_id]) ? $child_in_patient_benefit_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['out_patient_benefit'] = isset($child_out_patient_benefit_arr[$d['dependent_id']][$prd_id]) ? $child_out_patient_benefit_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['monthly_income'] = isset($child_monthly_income_arr[$d['dependent_id']][$prd_id]) ? $child_monthly_income_arr[$d['dependent_id']][$prd_id] : 0;
							$dependent_params['benefit_percentage'] = isset($child_benefit_percentage_arr[$d['dependent_id']][$prd_id]) ? $child_benefit_percentage_arr[$d['dependent_id']][$prd_id] : 0;
						}
						if (isset($d[$relation."_mname"]) && $d[$relation."_mname"]!="") {
							$dependent_params['mname'] = $d[$relation."_mname"];
						}else{
							$dependent_params['mname'] = "";
						}
						if (isset($d[$relation."_email"]) && $d[$relation."_email"]!="") {
							$dependent_params['email'] = $d[$relation."_email"];
						}else{
							$dependent_params['email'] = "";
						}
						if (isset($d[$relation."_phone"]) && $d[$relation."_phone"]!="") {
							$dependent_params['phone'] = phoneReplaceMain($d[$relation."_phone"]);
						}else{
							$dependent_params['phone'] ="";
						}
						if (isset($d[$relation."_address1"]) && $d[$relation."_address1"]!="") {
							$dependent_params['address'] = $d[$relation."_address1"];
						}else{
							$dependent_params['address'] = $primary_address1;
						}
						if (isset($d[$relation."_address2"]) && $d[$relation."_address2"]!="") {
							$dependent_params['address2'] = $d[$relation."_address2"];
						}else{
							$dependent_params['address2'] = $primary_address2;
						}
						if (isset($d[$relation."_city"]) && $d[$relation."_city"]!="") {
							$dependent_params['city'] = $d[$relation."_city"];
						}else{
							$dependent_params['city'] = $primary_city;
						}
						if (isset($d[$relation."_state"]) && $d[$relation."_state"]!="") {
							$dependent_params['state'] = $d[$relation."_state"];
						}else{
							$dependent_params['state'] = $primary_state;
						}
						if (isset($d[$relation."_zip"]) && $d[$relation."_zip"]!="") {
							$dependent_params['zip_code'] = $d[$relation."_zip"];
						}else{
							$dependent_params['zip_code'] = $primary_zip;
						}
						if (isset($d[$relation."_salary"]) && $d[$relation."_salary"]!="") {
							$dependent_params['salary']=$d[$relation."_salary"];
						}else{
							$dependent_params['salary']="";
						}
						if (isset($d[$relation."_employment_status"]) && $d[$relation."_employment_status"]!="") {
							$dependent_params['employmentStatus']=$d[$relation."_employment_status"];
						}else{
							$dependent_params['employmentStatus']="";
						}
						if (isset($d[$relation."_tobacco_status"]) && $d[$relation."_tobacco_status"]!="") {
							$dependent_params['tobacco_use']=$d[$relation."_tobacco_status"];
						}else{
							$dependent_params['tobacco_use']="";
						}
						if (isset($d[$relation."_smoking_status"]) && $d[$relation."_smoking_status"]!="") {
							$dependent_params['smoke_use']=$d[$relation."_smoking_status"];
						}else{
							$dependent_params['smoke_use']="";
						}
						if (isset($d[$relation."_height"]) && $d[$relation."_height"]!="") {
							$dependent_height_array = explode(".", $d[$relation."_height"]);
							$dependent_params['height_feet']=$dependent_height_array[0];
							$dependent_params['height_inches']=$dependent_height_array[1];
						}else{
							$dependent_params['height_feet']='';
							$dependent_params['height_inches']='';
						}
						if (isset($d[$relation."_weight"]) && $d[$relation."_weight"]!="") {
							$dependent_params['weight']=$d[$relation."_weight"];
						}else{
							$dependent_params['weight']="";
						}
						if (isset($d[$relation."_SSN"]) &&  $d[$relation."_SSN"]!= '') {
							$dependent_params['ssn'] = str_replace("-", "", $d[$relation."_SSN"]);
							$dependent_params['last_four_ssn'] = substr(str_replace("-", "", $d[$relation."_SSN"]), -4);
						}else{
							$dependent_params['ssn'] = "";
							$dependent_params['last_four_ssn'] = "";
						}

						if (isset($d[$relation."_benefit_level"]) && $d[$relation."_benefit_level"]!="") {
							$dependent_params['benefit_level']=$d[$relation."_benefit_level"];
						}else{
							$dependent_params['benefit_level']="";
						}
						if (isset($d[$relation."_hours_per_week"]) && $d[$relation."_hours_per_week"]!="") {
							$dependent_params['hours_per_week']=$d[$relation."_hours_per_week"];
						}else{
							$dependent_params['hours_per_week']=NULL;
						}

						if (isset($d[$relation."_pay_frequency"]) && $d[$relation."_pay_frequency"]!="") {
							$dependent_params['pay_frequency']=$d[$relation."_pay_frequency"];
						}else{
							$dependent_params['pay_frequency']="";
						}

						if (isset($d[$relation."_us_citizen"]) && $d[$relation."_us_citizen"]!="") {
							$dependent_params['us_citizen']=$d[$relation."_us_citizen"];
						}else{
							$dependent_params['us_citizen']="";
						}

						if (isset($d[$relation."_date_of_hire"]) && $d[$relation."_date_of_hire"]!="") {
							$dependent_params['hire_date']=date('Y-m-d', strtotime($d[$relation."_date_of_hire"]));
						}else{
							$dependent_params['hire_date']=NULL;
						}
						if (in_array($application_type,array('member_signature','voice_verification','admin'))) {
							if (!$paymentApproved) {
								$dependent_params['status'] = 'Payment Declined';
							}
						}
						if ($enroll_with_post_date == "yes") {
							$dependent_params['status'] = "Post Payment";
						}

						if(count($cust_dep_res) > 0 && $is_add_product != 1){
							$cdp_param = array(
								'customer_id' => $customer_id,
								'relation' => $dependent_params['relation'],
								'fname' => $dependent_params['fname'],
								'lname' => $dependent_params['lname'],
								'birth_date' => $dependent_params['birth_date'],
								'gender' => $dependent_params['gender'],
								'email' => $dependent_params['email'],
								'phone' => $dependent_params['phone'],
								'mname' => $dependent_params['mname'],
								'address' => $dependent_params['address'],
								'city' => $dependent_params['city'],
								'state' => $dependent_params['state'],
								'zip_code' => $dependent_params['zip_code'],
								'salary' => $dependent_params['salary'],
								'employmentStatus' => $dependent_params['employmentStatus'],
								'tobacco_use' => $dependent_params['tobacco_use'],
								'smoke_use' => $dependent_params['smoke_use'],
								'height_feet' => $dependent_params['height_feet'],
								'height_inches' => $dependent_params['height_inches'],
								'weight' => $dependent_params['weight'],
								'ssn' => $dependent_params['ssn'],
								'last_four_ssn' => $dependent_params['last_four_ssn'],
								'benefit_level' => $dependent_params['benefit_level'],
								'hours_per_week' => $dependent_params['hours_per_week'],
								'pay_frequency' => $dependent_params['pay_frequency'],
								'us_citizen' => $dependent_params['us_citizen'],
								'hire_date' => $dependent_params['hire_date'],
							);							
							$dependent_profile_where = array(
						        "clause" => "id = :id",
						        "params" => array(
						            ":id" => $d['cd_profile_id']
						        ),
							);
							$dependent_where = array(
						        "clause" => "id=:id",
						        "params" => array(
									":id" => $cust_dep_res['id'],
						        ),
						    );
							$pdo->update("customer_dependent_profile", $cdp_param, $dependent_profile_where);

							$lead_track = array(
								'status' => 'Updated',
								'description' => 'Updated in customer dependent profile table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

							$pdo->update("customer_dependent", $dependent_params, $dependent_where);

							$lead_track = array(
								'status' => 'Updated',
								'description' => 'Updated in customer dependent table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

							$dep_id=$cust_dep_res['id'];

							//Store Dependent Profile Benefit Amount
							if(!empty($dependent_params['benefit_amount']) || !empty($dependent_params['in_patient_benefit']) || !empty($dependent_params['out_patient_benefit']) || !empty($dependent_params['monthly_income']) || !empty($dependent_params['benefit_percentage'])) {
								$dep_benefit_param = array(
									"benefit_amount" => $dependent_params['benefit_amount'],
									"in_patient_benefit" => $dependent_params['in_patient_benefit'],
									"out_patient_benefit" => $dependent_params['out_patient_benefit'],
									"monthly_income" => $dependent_params['monthly_income'],
									"benefit_percentage" => $dependent_params['benefit_percentage'],
								);
								save_customer_dependent_profile_benefit_amount($d['cd_profile_id'],$dependent_params['product_id'],$dep_benefit_param);
							}							
						} else {
							$dep_id=$function_list->insert_dependent($dependent_params, $prd_mat_id,$is_add_product,$cd_profile_id);

							$lead_track = array(
								'status' => 'Inserted',
								'description' => 'Inserted in customer dependent table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}

						$queCustom = !empty($_POST[$relation.'_queCustom'][$d['dependent_id']]) ? $_POST[$relation.'_queCustom'][$d['dependent_id']] : array();

						if(!empty($queCustom)){
							foreach ($queCustom as $key => $value) {
								$sqlQue= "SELECT id FROM customer_custom_questions WHERE is_deleted='N' AND question_id=:question_id AND customer_id =:customer_id AND enrollee_type=:enrollee_type AND dependent_id=:dependent_id";
								$resQue=$pdo->selectOne($sqlQue,array(":customer_id"=>$customer_id,":question_id"=>$key,":enrollee_type"=>$relation,":dependent_id"=>$dep_id));

								if(is_array($value)){
									$answer = implode(",", $value);
								}else{
									$answer = $value;
								}
								$queInsParams = array(
									"enrollee_type"=>$relation,
									"customer_id"=>$customer_id,
									"question_id"=>$key,
									"dependent_id"=>$dep_id,
									"answer"=>$answer,
								);
								if(!empty($resQue)){
									$queInswhere = array(
										"clause" => "id=:id",
										"params" => array(
											":id" => $resQue['id'],
										),
									);
									$pdo->update("customer_custom_questions", $queInsParams, $queInswhere);

									$lead_track = array(
										'status' => 'Updated',
										'description' => 'Updated in customer custom question table',
									);
								
									lead_tracking($lead_id,$customer_id,$lead_track);
								}else{
									$pdo->insert("customer_custom_questions", $queInsParams);

									$lead_track = array(
										'status' => 'Inserted',
										'description' => 'Inserted in customer custom question table',
									);
								
									lead_tracking($lead_id,$customer_id,$lead_track);
								}
							}
						}
					}
				}

				//********* Update cust_enrollment_id To customer_dependent code start ********************
				if (!empty($websiteSubscriptionArr)) {
					$incr = "";
					// if($is_add_product == 1){
						$incr = " AND terminationDate IS NULL";
					// }
					foreach ($websiteSubscriptionArr as $ws_row) {
						$dependent_where = array(
							"clause" => "customer_id=:customer_id AND product_id=:product_id AND product_plan_id=:plan_id AND prd_plan_type_id=:prd_plan_type_id $incr",
							"params" => array(
								":customer_id" => $ws_row['customer_id'],
								":product_id" => $ws_row['product_id'],
								":plan_id" => $ws_row['plan_id'],
								":prd_plan_type_id" => $ws_row['prd_plan_type_id'],
							),
						);
						 $pdo->update("customer_dependent", array('website_id' => $ws_row['website_id'],'eligibility_date'=>$ws_row['eligibility_date']), $dependent_where);
					}

					$lead_track = array(
						'status' => 'Updated',
						'description' => 'Updated customer dependent table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}
				//********* Update cust_enrollment_id To customer_dependent code end   ********************
			}
		//********* dependent table code end   ********************



		//********* insert terms and agreement of member code start ********************
			if(($sponsor_billing_method == 'individual' || $application_type == 'member_signature') && !$only_waive_products && $billing_display){
				if(in_array($application_type,array('voice_verification','admin','member_signature'))){
					$extraMemberTerms = array(
						'websiteSubscriptionArr' => array_column($websiteSubscriptionArr,'website_id'),
						'action' => 'member_signature'
					);
			 		$function_list->insert_member_terms($customer_id,$order_id,$extraMemberTerms);
			 		$lead_track = array(
						'status' => 'Inserted',
						'description' => 'Inserted in member term agreement table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}
			}
			if($billing_display){
				$function_list->insert_dpg_agreements($customer_id,$order_id);
				$function_list->insert_joinder_agreements($customer_id,$order_id,$enrollment_application_type);

				$lead_track = array(
					'status' => 'Inserted',
					'description' => 'Inserted dpg agreement',
				);
			
				lead_tracking($lead_id,$customer_id,$lead_track);
			}
		//********* insert terms and agreement of member code end   ********************

		//********* Update Lead and Customer Status code start ********************
			$member_setting = $memberSetting->get_status_by_payment($paymentApproved,"",($enroll_with_post_date == "yes" ? true : false),$cust_old_status,array("is_from_enrollment" => true));

			if ($paymentApproved && in_array($application_type,array('member_signature','voice_verification','admin'))) {
				if(isset($quote_id) && !empty($quote_id)){
					$c_quote_param = array(
						'status' => 'Completed',
						'updated_at' => 'msqlfunc_NOW()'
					);
					$quote_where = array("clause" => "id=:id", "params" => array(":id" => $quote_id));
					$pdo->update("lead_quote_details", $c_quote_param, $quote_where);

					$lead_track = array(
						'status' => 'Updated',
						'description' => 'Updated lead quote details',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				} else {
					$str_plan_ids = implode(",", array_unique($PlanIdArr));
					$lead_quote_details_response = $pdo->selectOne("SELECT * FROM lead_quote_details WHERE agent_id = :agent_id AND customer_ids = :customer_ids AND lead_id = :lead_id AND plan_ids LIKE :plan_id", array(":agent_id" => $sponsor_id, ":customer_ids" => $customer_id, ":lead_id" => $lead_id, ":plan_id" => $str_plan_ids));
					$currentQuoteId = isset($lead_quote_details_response['id'])?$lead_quote_details_response['id']:0;

					if($lead_quote_details_response){
						$c_quote_param = array(
							'status' => 'Completed',
							'updated_at' => 'msqlfunc_NOW()'
						);
						$quote_where = array("clause" => "id=:id", "params" => array(":id" => $lead_quote_details_response['id']));
						$pdo->update("lead_quote_details", $c_quote_param, $quote_where);

						$lead_track = array(
							'status' => 'Updated',
							'description' => 'Updated lead quote details',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				}

				if ($enroll_with_post_date != "yes") {
					$lead_where = array(
						"clause" => "id=:id",
						"params" => array(
							":id" => $lead_id,
						),
					);
					$pdo->update("leads", array('status' => 'Converted', 'updated_at' => 'msqlfunc_NOW()'), $lead_where);

					$lead_track = array(
						'status' => 'Updated',
						'description' => 'Updated leads table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

					$update_lead_param = array(
						'customer_id' => $customer_id,
						'email' => $primary_email,
						'cell_phone' => $primary_phone,
					);
					$function_list->update_leads_and_details($update_lead_param);

					if($billing_display){
						$customerPassword = getname("customer",$customer_id,"password",'id');
						if(empty($customerPassword)){
							$temporaryPassword = generate_chat_password(10);
							$updateCustomerPasswordParams = ['password' => "msqlfunc_AES_ENCRYPT('" . $temporaryPassword . "','" . $CREDIT_CARD_ENC_KEY . "')"];
							$updateCustomerPasswordWhere = ['clause' => 'id=:id','params' => [':id' => $customer_id]];
							$pdo->update('customer',$updateCustomerPasswordParams,$updateCustomerPasswordWhere);
						}
						$TriggerMailSms->trigger_action_mail('member_enrollment',$customer_id,'member','addedEffectiveDate',$coverage_dates);

						$lead_track = array(
							'status' => 'Sent',
							'description' => 'Email sent - addedEffectiveDate',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				}
				
				$MemberEnrollment->unqualified_leads_with_duplicate_email($primary_email,$customer_id);

				$customer_where = array(
					'clause' => 'id = :id',
					'params' => array(
						':id' => $customer_id,
					),
				);

				if (!empty($customer_id)) {
					$updateCustomer = array(
						// 'status' => 'Active',
						'status' => $member_setting['member_status'],
						'updated_at' => 'msqlfunc_NOW()',
						"invite_at" => "msqlfunc_NOW()"
					);
					// if ($enroll_with_post_date == "yes") {
					// 	$updateCustomer['status'] = 'Post Payment';
					// }
					$pdo->update('customer', $updateCustomer, $customer_where);

					$lead_track = array(
						'status' => 'Updated',
						'description' => 'Updated customer table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}
				/*--------------------- Start Final Script ---------------------------------*/

				/*--------- Send Welcome Mail ---------*/
				$agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['id']);
				$trigger_id = 39;
				if($is_group_member == 'Y'){
					$trigger_id = 109;
				}
				
				$mail_data = array();
				$mail_data['fname'] = $primary_fname;
				$mail_data['lname'] = $primary_lname;
				$mail_data['Email'] = $primary_email;
				$mail_data['Phone'] = $primary_phone;
				$mail_data['link'] = $CUSTOMER_HOST;
				$mail_data['Agent'] = !empty($agent_detail['agent_name']) ? $agent_detail['agent_name'] : '';
				$mail_data['order_id'] = "#" . $order_display_id;
				$mail_data['order_date'] = date("m/d/Y");
				$mail_data['MemberID'] = $customer_rep_id;

				if(!empty($sponsor_row['sponsor_id'])){
					$parent_agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['sponsor_id']);
					$mail_data['ParentAgent'] = $parent_agent_detail['agent_name'];
				}

				//********* Confirm summary code start ********************
					$summary = "";
					$summary .= '<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center" style="margin-bottom:15px; text-align:left; font-size:14px; margin-top:10px" >
		            <thead>
		                <tr style="background-color:#f1f1f1; text-align:left;">
		                    <th width="5%">No.</th>
		                    <th width="50%">Description</th>
		                    <th width="10%">Qty</th>
		                    <th width="">Unit Price</th>
		                    <th width="12%" style="text-align:right">Total</th>
		                </tr>
		            </thead>
		            <tbody>';
					$i = 1;

					foreach ($purchase_products_array as $key => $product) {
						if(in_array($product['product_type'], array('Healthy Step','ServiceFee'))){
							continue;
						}
						$summary_price = 0;
						$summary_price = $product['price'];

						$plan_name = isset($product['plan_name'])?$product['plan_name']:"";
						$product_name = $product['product_name'];
						if($product['type']=='Fees'){
							$plan_name = $product['product_type'].' Fee';

						}
						$count = $i;

						$summary .= '<tr>
			                <td>' . $count . '</td>
			                <td>' . $product_name . ' (' . $plan_name . ')' . '</td>
			                <td>' . $product['qty'] . '</td>
			                <td>' . displayAmount($summary_price, 2, 'USA') . '</td>
			                <td style="text-align:right">' . displayAmount($summary_price * $product['qty'], 2, 'USA') .'</td>
		            	</tr>';

						$i++;

					}


					$summary .= '</tbody> </table>
		            <table cellspacing="0" cellpadding="5" border="0" style="float:right; width:290px; font-size:14px;">
		            <tr>
		                <td>Sub Total : </td>
		                <td style="text-align:right">' . displayAmount($order_total['sub_total'], 2, "USA") . '</td>
		            </tr>';
					if ($order_total['service_fee'] > 0) {
						$summary .= '<tr>
		                    <td>Service Fee</td>
		                    <td align="right">' . displayAmount($order_total['service_fee'], 2, 'USA') . ' </td>
		                </tr>';
					}
					if ($order_total['healthy_step_fee'] > 0) {
						$summary .= '<tr>
		                    <td>Healthy Step </td>
		                    <td align="right">' . displayAmount($order_total['healthy_step_fee'], 2, 'USA') . ' </td>
		                </tr>';
					}
					$summary .= '<tr style="background-color:#f1f1f1; font-size: 16px;">
		                <td><strong>Grand Total</strong></td>
		                <td style="text-align:right"><strong>' . displayAmount($order_total['grand_total'], 2, "USA") . '</strong></td>
		            </tr>
		            </table>
		            <div style="clear:both"></div>';
				//********* Confirm summary code end ********************

				$mail_data['order_summary'] = $summary;

				if ($payment_mode == "CC") {
					$cd_number = !empty($card_number) ? $card_number : $full_card_number;
					$mail_data['billing_detail'] = "Billed to: $card_type *" . substr($cd_number, -4);
				} else {
					$r_number = !empty($routing_number) ? $routing_number : $entered_routing_number;
					$mail_data['billing_detail'] = "Billed to: ACH *" . substr($r_number, -4);
				}

				if ($SITE_ENV == 'Local') {
					$primary_email = "karan@cyberxllc.com";
				}

				if (!empty($customer_id)) {
					if (!empty($agent_detail)) {
						$mail_data['agent_name'] = !empty($agent_detail['agent_name']) ? $agent_detail['agent_name'] : '';
						$mail_data['agent_email'] = !empty($agent_detail['agent_email']) ? $agent_detail['agent_email'] : '';
						$mail_data['agent_phone'] = !empty($agent_detail['agent_phone']) ? format_telephone($agent_detail['agent_phone']) : '';
						$mail_data['agent_id'] = !empty($agent_detail['agent_id']) ? $agent_detail['agent_id'] : '';
						$mail_data['is_public_info'] = !empty($agent_detail['is_public_info']) ? $agent_detail['is_public_info'] : '';
					} else {
						$mail_data['is_public_info'] = 'display:none';
					}

					$smart_tags = get_user_smart_tags($customer_id,'member');
                
			        if($smart_tags){
			            $mail_data = array_merge($mail_data,$smart_tags);
			        }
			        if ($enroll_with_post_date != "yes" && !empty($send_email_productId)) {
			        	if($billing_display){
							trigger_mail($trigger_id, $mail_data, $primary_email, array(), 3);

							$lead_track = array(
								'status' => 'Sent',
								'description' => 'Email sent - Member Application Summary',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					}
					/*---------/Send Welcome Mail ---------*/

					//********* Activity Feed code start ********************
					if($enrollmentLocation=='adminSide'){
						$activity_feed_data = array();
						if(in_array($application_type,array('voice_verification'))) {
							$activity_feed_data['ac_message'] = array(
								'ac_red_1'=>array(
									'href' => $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            						'title' => $_SESSION['admin']['display_id'],
								),
								'ac_message_1' =>' added product on ',
								'ac_red_2'=>array(
									'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
									'title'=>$customer_rep_id,
								),
								'ac_message_2' =>' used Voice Recording Uploading to complete application'
							);
						} else {
							$activity_feed_data['ac_message'] = array(
								'ac_red_1'=>array(
									'href' => $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            						'title' => $_SESSION['admin']['display_id'],
								),
								'ac_message_1' =>' added product on ',
								'ac_red_2'=>array(
									'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
									'title'=>$customer_rep_id,
								),
								'ac_message_2' =>' used eSignature to complete application'
							);
						}
						activity_feed(3, $_SESSION['admin']['id'], 'Admin', $customer_id, 'customer', 'Admin Added Product', $primary_fname, $primary_lname, json_encode($activity_feed_data));

						$lead_track = array(
							'status' => 'Activity Feed',
							'description' => 'Activity added - Admin Added Product',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					} else {
						$message_3 = $payment_mode =='CC' ? ' Approved on Order ' : 'PENDING SETTLEMENT on Order';
						$leadRes = $pdo->selectOne("SELECT lead_id FROM leads WHERE id = :lead_id", array(":lead_id" => $lead_id));
						if($is_group_member == 'Y' && $sponsor_billing_method != 'individual'){
							$activity_feed_data_member['ac_group_description'] = $sponsor_row['rep_id'].' Enrolled A New Member <br>';
							$activity_feed_data_member['ac_message'] = array(
								'ac_red_1'=>array(
									'href'=>$GROUP_HOST.'/lead_details.php?id='.md5($lead_id),
									'title'=>$leadRes['lead_id'],
								),
								'ac_message_2' =>' became Member ',
								'ac_red_2'=>array(
									'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
									'title'=>$customer_rep_id,
								),
								'ac_message_3' =>'  Member : '.$primary_fname.' '.$primary_lname.' <br>',
							);
						}else{
							$activity_feed_data_member['ac_message'] = array(
								'ac_red_1'=>array(
									'href'=>$AGENT_HOST.'/lead_details.php?id='.md5($lead_id),
									'title'=>$leadRes['lead_id'],
								),
								'ac_message_2' =>'  transaction  ',
								'ac_red_2'=>array(
									// 'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
									'title'=>$txn_id,
								),
								'ac_message_3' =>$message_3,
								'ac_red_3'=>array(
									'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
									'title'=>$order_display_id,
								),
								'ac_message_4' =>' and became Member ',
								'ac_red_4'=>array(
									'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
									'title'=>$customer_rep_id,
								),
								'ac_message_5' =>'  Member : '.$primary_fname.' '.$primary_lname.' <br>',
							);
						}
						$activity_feed_data_member['key_value'] = array(
							'desc_arr'=>array(
								'url'=> $enrollment_url,
								'email'=>$primary_email,
								'phone' => $primary_phone,
							)
						);

						if(in_array($application_type,array('voice_verification'))) {
							$activity_feed_data = array(
								'file_name' => json_encode($voice_uploaded_fileName),
								'voice_application_type' => $voice_application_type,
								'system_code' => $voice_verification_system_code,
								'is_voice_msg' => 'Y',
							);
						} else {
							$activity_feed_data = array();
						}

						activity_feed(3, $sponsor_row['id'], $sponsor_row['type'],$sponsor_row['id'],$sponsor_row['type'], 'Enrolled A New Member', $primary_fname, $primary_lname, json_encode($activity_feed_data_member));

						$lead_track = array(
							'status' => 'Activity Feed',
							'description' => 'Activity added - Enrolled A New Member',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

						activity_feed(3, $customer_id, "Customer", $order_id, 'orders', 'Joined', $primary_fname, $primary_lname, json_encode($activity_feed_data_member));

						$lead_track = array(
							'status' => 'Activity Feed',
							'description' => 'Activity added - Joined',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

						$trigger_id = 38;
						if($is_group_member == 'Y'){
							$trigger_id = 110;
						}
						$mail_content = $pdo->selectOne("SELECT id,email_content,display_id from triggers where id=:id",array(":id"=>$trigger_id));
						$email_activity = array();
						if(!empty($mail_data) && !empty($mail_content['id'])){
							$email_cn = $mail_content['email_content'];
							foreach ($mail_data as $placeholder => $value) {
								$email_cn = str_replace("[[" . $placeholder . "]]", $value, $email_cn);
							}

							$email_activity["ac_description_link"] = array(
								'Trigger :' => array(
									'href'=>'#javascript:void(0)',
									'class'=>'descriptionPopup',
									'title'=>$mail_content['display_id'],
									'data-desc'=>htmlspecialchars($email_cn),
									'data-encode'=>'no'
								),
								'<br>Email :' => array(
									'href' => '#javascript:void(0)',
									'title' => $primary_email,
								)
							);
						}
						if ($enroll_with_post_date != "yes" && !empty($send_email_productId)) {
							if($billing_display){

								activity_feed(3, $customer_id, "Customer", $trigger_id, 'triggers', 'Welcome email delivered', $primary_fname,$primary_lname,json_encode($email_activity));

								$MemberEnrollment->send_temporary_password_mail($customer_id);

								$lead_track = array(
									'status' => 'Sent',
									'description' => 'Email Sent - Temporary Password',
								);
							
								lead_tracking($lead_id,$customer_id,$lead_track);
							}
						}
						//********* Activity Feed code end ********************

						if(in_array($application_type,array('voice_verification','admin'))){
							$mail_data['fname']=$primary_fname;
							$mail_data['Email']=$primary_email;

						}else{
							$mail_data['fname']=$primary_fname;
							$mail_data['Email']=$primary_email;
						}

						if (!empty($agent_detail)) {
							$mail_data['agent_name'] = !empty($agent_detail['agent_name']) ? $agent_detail['agent_name'] : "";
							$mail_data['agent_email'] = !empty($agent_detail['agent_email']) ? $agent_detail['agent_email'] : "";
							$mail_data['agent_phone'] = !empty($agent_detail['agent_phone']) ? format_telephone($agent_detail['agent_phone']) : "";
							$mail_data['agent_id'] = !empty($agent_detail['agent_id']) ? $agent_detail['agent_id'] : "";
							$mail_data['is_public_info'] = $agent_detail['is_public_info'];
						} else {
							$mail_data['is_public_info'] = 'display:none';
						}

						$smart_tags = get_user_smart_tags($customer_id,'member');
                
				        if($smart_tags){
				            $mail_data = array_merge($mail_data,$smart_tags);
				        }
				        if ($enroll_with_post_date != "yes" && !empty($send_email_productId)) {
				        	if($billing_display){
										trigger_mail($trigger_id, $mail_data, $primary_email, array(), 3);

										$lead_track = array(
											'status' => 'Sent',
											'description' => 'Email Sent - Trigger id - '. $trigger_id,
										);
									
										lead_tracking($lead_id,$customer_id,$lead_track);
									}
							
						}

						$activity_feed_data = array();
						$activity_feed_data_lead = array();
						if(in_array($application_type,array('voice_verification'))) {
							$activity_feed_data['ac_message'] = array(
								'ac_red_1'=>array(
									'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
									'title'=>$customer_rep_id,
								),
								'ac_message_1' =>' used Voice Recording Uploading to complete application'
							);

							$activity_feed_data_lead['ac_message'] = array(
								'ac_red_1'=>array(
									'href' => $ADMIN_HOST.'/lead_details.php?id=' . md5($lead_id),
                            		'title' => $lead_row['lead_id'],
								),
								'ac_message_1' =>' used Voice Recording Uploading to complete application'
							);
						} else {
							$activity_feed_data['ac_message'] = array(
								'ac_red_1'=>array(
									'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
									'title'=>$customer_rep_id,
								),
								'ac_message_1' =>' used eSignature to complete application'
							);
							$activity_feed_data_lead['ac_message'] = array(
								'ac_red_1'=>array(
									'href' => $ADMIN_HOST.'/lead_details.php?id=' . md5($lead_id),
                            		'title' => $lead_row['lead_id'],
								),
								'ac_message_1' =>' used eSignature to complete application'
							);
						}
						activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'Application is Approved by Member', $primary_fname, $primary_lname, json_encode($activity_feed_data));

						$lead_track = array(
							'status' => 'Activity Added',
							'description' => 'Activity feed added - Application is Approved by Member',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

						activity_feed(3, $lead_id, 'Lead', $lead_id, 'leads', 'Application is Approved by Lead', $primary_fname, $primary_lname, json_encode($activity_feed_data_lead));

						$lead_track = array(
							'status' => 'Activity Added',
							'description' => 'Activity feed added - Application is Approved by Lead',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				}

				if ($enrollment_type == "quote") {
					$lead_quote_detail_where = array(
						"clause" => "id=:id",
						"params" => array(
							":id" => $lead_quote_detail_id,
						),
					);
					$pdo->update("lead_quote_details", array('status' => 'Completed', 'updated_at' => 'msqlfunc_NOW()'), $lead_quote_detail_where);

					$lead_track = array(
						'status' => 'Updated',
						'description' => 'Updated lead quote details with status - completed',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

				}
				$response['md5_customer_id'] = md5($customer_id);
				$response['md5_order_id'] = md5($order_id);
				$response['payment_type'] = strtolower($web_payment_type);
				$response['status'] = 'account_approved';
				$response['is_add_product'] = $is_add_product;
				if($enrollmentLocation == "adminSide"){
					setNotifySuccess("Congratulations.. Product added Successfully");
				}else{
					setNotifySuccess("Congratulations.. Member Enrolled Successfully");
				}
			} else if (!$paymentApproved && !in_array($application_type,array('member_signature','voice_verification','admin'))) { 

				$customer_where = array(
					'clause' => 'id = :id',
					'params' => array(
						':id' => $customer_id,
					),
				);

				$updateCustomer = array(
					'status' => $newStatus,
					'updated_at' => 'msqlfunc_NOW()',
					"invite_at" => "msqlfunc_NOW()",
				);

				if($enrollmentLocation == "adminSide" || (!empty($existing_customer_id))){
					$updateCustomer = array('updated_at' => 'msqlfunc_NOW()');
				}

				if(in_array($application_type,array('voice_verification','admin'))){
					$customer_pwd = getname("customer",$customer_id,"password",'id');
					if(empty($customer_pwd)){
						$temp_password = generate_chat_password(10);
					}
				}
				$pdo->update('customer', $updateCustomer, $customer_where);

				$lead_track = array(
					'status' => 'Updated',
					'description' => 'Updated customer table when payment failed',
				);
			
				lead_tracking($lead_id,$customer_id,$lead_track);

				if (isset($_SESSION["exist_email"])) {
					unset($_SESSION["exist_email"]);
				}
				if (isset($_SESSION["shop"])) {
					unset($_SESSION["shop"]);
				}

                if(empty($existing_customer_id)){
                    $desc = array();
                    $desc['ac_message'] = array(
                        'ac_red_1' => array(
                            'href' => 'agent_detail_v1.php?id=' . md5($sponsor_id),
                            'title' => $sponsor_row['rep_id'],
                        ),
                        'ac_message_1' => ' created new AAE for '.($lead_row['fname'].' '.$lead_row['lname']),
                        'ac_red_2' => array(
                            'href' => 'lead_details.php?id=' . md5($lead_id),
                            'title' => $lead_row['lead_id'],
                        ),
                        'ac_message_2' => '',
                    );
                    activity_feed(3, $sponsor_id, 'Agent', $lead_row['id'], 'Lead', 'Created New AAE', $primary_fname, $primary_lname, json_encode($desc), $REQ_URL);
                } else {

                	if($enrollmentLocation == "adminSide"){
                		$desc = array();
	                    $desc['ac_message'] = array(
	                        'ac_red_1' => array(
	                            'href' => 'admin_profile.php?id' . md5($_SESSION['admin']['id']),
	                            'title' => $_SESSION['admin']['display_id'],
	                        ),
	                        'ac_message_1' => ' updated AAE on '.($lead_row['fname'].' '.$lead_row['lname']),
	                        'ac_red_2' => array(
	                            'href' => 'lead_details.php?id=' . md5($lead_id),
	                            'title' => $lead_row['lead_id'],
	                        ),
	                        'ac_message_2' => '',
	                    );
	                    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $lead_row['id'], 'Lead', 'Updated AAE', $primary_fname, $primary_lname, json_encode($desc), $REQ_URL);
                	}else{
	                    $desc = array();
	                    $desc['ac_message'] = array(
	                        'ac_red_1' => array(
	                            'href' => 'agent_detail_v1.php?id=' . md5($sponsor_id),
	                            'title' => $sponsor_row['rep_id'],
	                        ),
	                        'ac_message_1' => ' updated AAE on '.($lead_row['fname'].' '.$lead_row['lname']),
	                        'ac_red_2' => array(
	                            'href' => 'lead_details.php?id=' . md5($lead_id),
	                            'title' => $lead_row['lead_id'],
	                        ),
	                        'ac_message_2' => '',
	                    );
	                    activity_feed(3, $sponsor_id, 'Agent', $lead_row['id'], 'Lead', 'Updated AAE', $primary_fname, $primary_lname, json_encode($desc), $REQ_URL);
	                }
                }

				if (!empty($sent_via) && in_array($application_type,array('member'))) {
					$mail_sent_status = '';
					$sms_sent_status = '';
					$token_val = md5('TOKEN'.$order_id);
					$url_link = $HOST . '/quote/enroll_varification/'. $token_val;
					$url_params = array(
						'dest_url' => $url_link,
						'agent_id' => $sponsor_id,
						'customer_id' => $customer_id,
					);
					$link = get_short_url($url_params);

					if ($sent_via == 'text' || $sent_via == 'Both') {
						$sms_data = array();
						$sms_data['fname'] = $primary_fname;
						$sms_data['lname'] = $primary_lname;
						$sms_data['link'] = $link;
						$tophone = "+1". $primary_phone;

						if ($SITE_ENV=='Local') {
							$tophone = '+919429548647';
						}

						$smart_tags = get_user_smart_tags($customer_id,'member');
                
				        if($smart_tags){
				            $sms_data = array_merge($sms_data,$smart_tags);
				        }

						$sms_sent_status = trigger_sms(84, $tophone, $sms_data, true, $sms_content);

						$lead_track = array(
							'status' => 'Sent',
							'description' => 'SMS sent - Verification sent using ' . $sent_via.' Sent Status: '. $sms_sent_status,
						);					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}
					if ($sent_via == 'email' || $sent_via == 'Both') {
						$mail_data = array();
						$mail_data['fname'] = $primary_fname;
						$mail_data['lname'] = $primary_lname;
						$mail_data['Email'] = $primary_email;
						$mail_data['Phone'] = $primary_phone;
						$mail_data['MemberID'] = $customer_rep_id;
						$agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['id']);
						$mail_data['Agent'] = !empty($agent_detail['agent_name']) ? $agent_detail['agent_name'] : '';
						if(!empty($sponsor_row['sponsor_id'])){
							$parent_agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['sponsor_id']);
							$mail_data['ParentAgent'] = $parent_agent_detail['agent_name'];
						}

						if (!empty($agent_detail)) {
							$mail_data['agent_name'] = !empty($agent_detail['agent_name']) ? $agent_detail['agent_name'] : '';
							$mail_data['agent_email'] = !empty($agent_detail['agent_email']) ? $agent_detail['agent_email'] : '';
							$mail_data['agent_phone'] = !empty($agent_detail['agent_phone']) ? format_telephone($agent_detail['agent_phone']) : '';
							$mail_data['agent_id'] = !empty($agent_detail['agent_id']) ? $agent_detail['agent_id'] : '';
							$mail_data['is_public_info'] = !empty($agent_detail['is_public_info']) ? $agent_detail['is_public_info'] : '';
						} else {
							$mail_data['is_public_info'] = 'display:none';
						}

						$mail_data['fname'] = $primary_fname;
						$mail_data['lname'] = $primary_lname;
						$mail_data['link'] = $link;
						$mail_data['USER_IDENTITY'] = array('rep_id' => $customer_rep_id, 'cust_type' => 'Agent', 'location' => $REQ_URL);
						if ($SITE_ENV == 'Local') {
							$primary_email = 'karan.shukla@serenetic.in';
						}

						$smart_tags = get_user_smart_tags($customer_id,'member');
                
				        if($smart_tags){
				            $mail_data = array_merge($mail_data,$smart_tags);
				        }

						$email_content = preg_replace('/[[:^print:]]/', '', $email_content);
						$mail_sent_status = trigger_mail(84, $mail_data, $primary_email, array(), 3, $email_content, $email_subject);

						$lead_track = array(
							'status' => 'Sent',
							'description' => 'Email sent - Verification sent using ' . $sent_via.' Sent Status: '. $mail_sent_status,
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					}
					// Inert into lead_quote_details with flag is_assisted_enrollment = 'Y'
					$quote_param = array_merge($lead_quote_details_param, array(
						'is_assisted_enrollment' => 'Y',
						'token' => $token_val,
						'updated_at' => 'msqlfunc_NOW()',
						'created_at' => 'msqlfunc_NOW()',
						'expire_time' => "msqlfunc_NOW() + INTERVAL 3 DAY"
					));

					if(!empty($primary_monthly_salary_percentage)){
						$quote_param['primary_monthly_salary_percentage'] = json_encode($primary_monthly_salary_percentage);
					}
					if(!empty($primary_annual_salary)){
						$quote_param['primary_annual_salary'] = json_encode($primary_annual_salary);
					}
					if(!empty($primary_monthly_benefit)){
						$quote_param['monthly_benefit_amount'] = json_encode($primary_monthly_benefit);
					}

					$quote_inserted = 'N';
					if(isset($quote_id) && !empty($quote_id)) {
						if($is_assisted_enrollment == 'Y'){
							$currentQuoteId = $quote_id;
							$quote_where = array("clause" => "id=:id", "params" => array(":id" => $quote_id));
							$pdo->update("lead_quote_details", $quote_param, $quote_where);

							$lead_track = array(
								'status' => 'Updated',
								'description' => 'Updated expiry time in lead quote details table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						} else {
							$c_quote_param = array(
								'status' => 'Completed',
								'updated_at' => 'msqlfunc_NOW()'
							);
							$quote_where = array("clause" => "id=:id", "params" => array(":id" => $quote_id));
							$pdo->update("lead_quote_details", $c_quote_param, $quote_where);

							$lead_track = array(
								'status' => 'Updated',
								'description' => 'Updated status as completed in lead quote details table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

							$str_plan_ids = implode(",", array_unique($PlanIdArr));
							$quote_param['agent_id'] = $sponsor_id;
							$quote_param['admin_id'] = ($enrollmentLocation == "adminSide" ? $_SESSION['admin']['id'] : 0);
							$quote_param['lead_id'] = $lead_id;
							$quote_param['customer_ids'] = $customer_id;
							$quote_param['order_ids'] = $order_id;
							$quote_param['status'] = 'Pending';
							$quote_param['plan_ids'] = $str_plan_ids;
							$quote_param['created_at'] = 'msqlfunc_NOW()';
							$quote_param['enroll_agent_id'] = 0;

							$quote_inserted = 'Y';
							$currentQuoteId = $pdo->insert("lead_quote_details", $quote_param);

							$lead_track = array(
								'status' => 'Inserted',
								'description' => 'Inserted in lead quote details table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					} else {
						$str_plan_ids = implode(",", array_unique($PlanIdArr));
						$quote_param['agent_id'] = $sponsor_id;
						$quote_param['admin_id'] = ($enrollmentLocation == "adminSide" ? $_SESSION['admin']['id'] : 0);
						$quote_param['lead_id'] = $lead_id;
						$quote_param['customer_ids'] = $customer_id;
						$quote_param['order_ids'] = $order_id;
						$quote_param['status'] = 'Pending';
						$quote_param['plan_ids'] = $str_plan_ids;
						$quote_param['created_at'] = 'msqlfunc_NOW()';

						$lead_quote_details_response = $pdo->selectOne("SELECT * FROM lead_quote_details WHERE agent_id = :agent_id AND customer_ids = :customer_ids AND lead_id = :lead_id AND plan_ids LIKE :plan_id", array(":agent_id" => $sponsor_id, ":customer_ids" => $customer_id, ":lead_id" => $lead_id, ":plan_id" => $str_plan_ids));

						$currentQuoteId = 0;
						if($lead_quote_details_response){
							$currentQuoteId = $lead_quote_details_response['id'];
							if($lead_quote_details_response['is_assisted_enrollment'] == 'Y'){
								$quote_where = array("clause" => "id=:id", "params" => array(":id" => $lead_quote_details_response['id']));
								$pdo->update("lead_quote_details", $quote_param, $quote_where);

								$lead_track = array(
									'status' => 'Updated',
									'description' => 'Updated status as pending in lead quote details table',
								);
							
								lead_tracking($lead_id,$customer_id,$lead_track);
							} else {
								$c_quote_param = array(
									'status' => 'Completed',
									'updated_at' => 'msqlfunc_NOW()'
								);
								$quote_where = array("clause" => "id=:id", "params" => array(":id" => $lead_quote_details_response['id']));
								$pdo->update("lead_quote_details", $c_quote_param, $quote_where);

								$lead_track = array(
									'status' => 'Updated',
									'description' => 'Updated status as Completed in lead quote details table',
								);
							
								lead_tracking($lead_id,$customer_id,$lead_track);
								$quote_inserted = 'Y';
								$currentQuoteId = $pdo->insert("lead_quote_details", $quote_param);

								$lead_track = array(
									'status' => 'Inserted',
									'description' => 'Inserted in lead quote details table',
								);
							
								lead_tracking($lead_id,$customer_id,$lead_track);
							}
						} else {
							$quote_inserted = 'Y';
							$currentQuoteId = $pdo->insert("lead_quote_details", $quote_param);

							$lead_track = array(
								'status' => 'Inserted',
								'description' => 'Inserted in lead quote details table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					}

					if($is_assisted_enrollment == 'N' || $quote_inserted == 'Y'){
						$activity_feed_data = array(
							'email_address' => $primary_email,
							'sms_phone' => $primary_phone,
							'customer_id' => $customer_id,
							'quote_id' => $currentQuoteId,
							'agent_id' => $sponsor_id,
							'lead_id' => $lead_id,
							'url' => $enrollment_url,
						);
						activity_feed(3, $sponsor_id, 'Agent', $lead_id, 'leads', 'Application Application Sent via SMS/Email', $primary_fname, $primary_lname, json_encode($activity_feed_data));
					}

					$policy_count = 0;
					if(count($PlanIdArr) > 0){
						foreach ($PlanIdArr as $key => $value) {
							if($value == 267 || $value == 271)
								continue;
							$policy_count++;
						}
					}

					$function_list->addQuoteNotificationMain($sponsor_id, 2, $lead_id, $currentQuoteId, count($PlanIdArr), $order_total['grand_total'], $url = "#");

					
					if(strtolower($mail_sent_status) == "success" || strtolower($sms_sent_status) == "success") {
						$message_delivered_status = 'success';
					} else {
						$message_delivered_status = 'fail';
					}
					if ($SITE_ENV == 'Local') {
						$message_delivered_status = 'success';
					}

					if(!empty($currentQuoteId)) {
						$quote_param = array(
							"is_opened" => "N",
							"link_opened_at" => NULL,
						);
						$quote_where = array("clause" => "id=:id", "params" => array(":id" => $currentQuoteId));
						$pdo->update("lead_quote_details", $quote_param, $quote_where);
					}

					$lead_track = array(
						'status' => 'message status',
						'description' => 'Email/SMS delivery status '.$message_delivered_status,
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
					
					if(in_array($enrollmentLocation,array('aae_site','self_enrollment_site')) && $message_delivered_status == 'success') {
						setNotifySuccess("Verification sent successfully");
					}
					$response['sent_to_member'] = $sent_via;
					$response['lead_quote_detail_id'] = $currentQuoteId;
					$response['message_delivered_status'] = $message_delivered_status;
					$response['customer_id'] = $customer_id;
					$response['lead_id'] = $lead_id;
					$response['order_id'] = $order_id;
				} else if (in_array($application_type,array('voice_verification','admin'))) {


					$activity_feed_data = array(
						'email_address' => $primary_email,
						'sms_phone' => $primary_phone,
						'customer_id' => $customer_id,
						'agent_id' => $sponsor_id,
						'quote_id' => $quote_id,
						'lead_id' => $lead_id,
						'url' => $enrollment_url,
						'file_name' => (in_array($application_type,array('voice_verification'))?json_encode($voice_uploaded_fileName):$physical_file_name),
						'voice_application_type' => $voice_application_type,
						'system_code' => $voice_verification_system_code,
						'is_voice_msg' => (in_array($application_type,array('voice_verification'))?'Y':'N'),
					);

					activity_feed(3, $sponsor_id, 'Agent', $lead_id, 'leads', 'Application (Admin Approval)', $primary_fname, $primary_lname, json_encode($activity_feed_data));


					if(isset($quote_id) && !empty($quote_id)){
						$c_quote_param = array(
							'status' => 'Completed',
							'updated_at' => 'msqlfunc_NOW()'
						);
						$quote_where = array("clause" => "id=:id", "params" => array(":id" => $quote_id));
						$pdo->update("lead_quote_details", $c_quote_param, $quote_where);

						$lead_track = array(
							'status' => 'Updated',
							'description' => 'Updated status as Completed in lead quote details table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
					} else {
						$str_plan_ids = implode(",", array_unique($PlanIdArr));
						$lead_quote_details_response = $pdo->selectOne("SELECT * FROM lead_quote_details WHERE agent_id = :agent_id AND customer_ids = :customer_ids AND lead_id = :lead_id AND plan_ids LIKE :plan_id", array(":agent_id" => $sponsor_id, ":customer_ids" => $customer_id, ":lead_id" => $lead_id, ":plan_id" => $str_plan_ids));
						$currentQuoteId = $lead_quote_details_response['id'];
						if($lead_quote_details_response){
							$c_quote_param = array(
								'status' => 'Completed',
								'updated_at' => 'msqlfunc_NOW()'
							);
							$quote_where = array("clause" => "id=:id", "params" => array(":id" => $lead_quote_details_response['id']));
							$pdo->update("lead_quote_details", $c_quote_param, $quote_where);

							$lead_track = array(
								'status' => 'Updated',
								'description' => 'Updated status as Completed in lead quote details table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					}

					if (!empty($temp_password)) {

						$mail_data = array();
						$mail_data['fname'] = $primary_fname;
						$mail_data['lname'] = $primary_lname;
						$mail_data['Email'] = $primary_email;
						$mail_data['Phone'] = $primary_phone;
						$mail_data['MemberID'] = $customer_rep_id;
						$agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['id']);
						$mail_data['Agent'] = $agent_detail['agent_name'];
						if(!empty($sponsor_row['sponsor_id'])){
							$parent_agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['sponsor_id']);
							$mail_data['ParentAgent'] = $parent_agent_detail['agent_name'];
						}
						if (!empty($agent_detail)) {
							$mail_data['agent_name'] = $agent_detail['agent_name'];
							$mail_data['agent_email'] = $agent_detail['agent_email'];
							$mail_data['agent_phone'] = format_telephone($agent_detail['agent_phone']);
							$mail_data['agent_id'] = $agent_detail['agent_id'];
							$mail_data['is_public_info'] = $agent_detail['is_public_info'];
						} else {
							$mail_data['is_public_info'] = 'display:none';
						}

						/*---------/Send Welcome Mail ---------*/

						/*--------- Activity Feed ---------*/
						if($enrollmentLocation == "adminSide" || (!empty($existing_customer_id))){

						}else{
							activity_feed(3, $sponsor_row['id'], $sponsor_row['type'], $sponsor_row['id'], $sponsor_row['type'], 'Enrolled A New Member', $primary_fname, $primary_lname);
							activity_feed(3, $customer_id, 'customer', $customer_id, 'customer', 'Enrolled A New Member', $primary_fname, $primary_lname);
						}

						if ($SITE_ENV=='Local') {
							$primary_email = "karan@cyberxllc.com";
						}
						$trigger_id = 38;
						if($is_group_member == 'Y'){
							$trigger_id = 110;
						}

						$smart_tags = get_user_smart_tags($customer_id,'member');
                
				        if($smart_tags){
				            $mail_data = array_merge($mail_data,$smart_tags);
				        }
				        if ($enroll_with_post_date != "yes" && !empty($send_email_productId)) {
				        	if($billing_display){
										trigger_mail($trigger_id, $mail_data, $primary_email, array(), 3);

										$lead_track = array(
											'status' => 'Sent',
											'description' => 'Email Sent - Welcome email',
										);
									
										lead_tracking($lead_id,$customer_id,$lead_track);
									}
						}

						$mail_content = $pdo->selectOne("SELECT id,email_content,display_id from triggers where id=:id",array(":id"=>$trigger_id));
						$email_activity = array();
						if(!empty($mail_data) && !empty($mail_content['id'])){
							$email_cn = $mail_content['email_content'];
							foreach ($mail_data as $placeholder => $value) {
								$email_cn = str_replace("[[" . $placeholder . "]]", $value, $email_cn);
							}

							$email_activity["ac_description_link"] = array(
								'Trigger :' => array(
									'href'=>'#javascript:void(0)',
									'class'=>'descriptionPopup',
									'title'=>$mail_content['display_id'],
									'data-desc'=>htmlspecialchars($email_cn),
									'data-encode'=>'no'
								),
								'<br>Email :' => array(
									'href' => '#javascript:void(0)',
									'title' => $primary_email,
								)
							);
						}
						if ($enroll_with_post_date != "yes" && !empty($send_email_productId)) {
							if($billing_display){
								activity_feed(3, $customer_id, "Customer", $trigger_id, 'triggers', 'Welcome email delivered', $primary_fname,$primary_lname,json_encode($email_activity));

								$MemberEnrollment->send_temporary_password_mail($customer_id);
							}
						}
					}
				}				
				if (!empty($sent_via) && in_array($application_type,array('member'))) {
					//setNotifySuccess("AAE verification successfully sent to enrollee");
					$response['status'] = 'success';
					$response['sub_status'] = "verification";
				} else {
					if($enrollmentLocation == "adminSide"){
						setNotifySuccess("Congratulations.. Product added Successfully");
					}else{
	    				setNotifySuccess("Congratulations.. Member Enrolled Successfully");
	    			}
	    			$response['status'] = 'account_approved';
				}
				$response['is_add_product'] = $is_add_product;

			} else if (!$paymentApproved && in_array($application_type,array('member_signature','voice_verification','admin'))) {

				$token_val = md5('TOKEN'.$order_id);
				// Inert into lead_quote_details with flag is_assisted_enrollment = 'Y'
				$quote_param = array_merge($lead_quote_details_param, array(
					'is_assisted_enrollment' => 'Y',
					'token' => $token_val,
					'updated_at' => 'msqlfunc_NOW()',
					'created_at' => 'msqlfunc_NOW()',
					'expire_time' => "msqlfunc_NOW() + INTERVAL 3 DAY"
				));

				$quote_inserted = 'N';
				if(isset($quote_id) && !empty($quote_id)) {
					$str_plan_ids = implode(",", array_unique($PlanIdArr));
					$quote_param['agent_id'] = $sponsor_id;
					$quote_param['lead_id'] = $lead_id;
					$quote_param['customer_ids'] = $customer_id;
					$quote_param['order_ids'] = $order_id;
					$quote_param['status'] = 'Pending';
					$quote_param['plan_ids'] = $str_plan_ids;
					$quote_param['created_at'] = 'msqlfunc_NOW()';
					$quote_param['enroll_agent_id'] = 0;

					if($is_assisted_enrollment == 'Y'){
						$currentQuoteId = $quote_id;
						$quote_where = array("clause" => "id=:id", "params" => array(":id" => $quote_id));
						$pdo->update("lead_quote_details", $quote_param, $quote_where);
						$lead_track = array(
							'status' => 'Updated',
							'description' => 'Updated with status as Pending in lead quote details table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

					} else {
						$c_quote_param = array(
							'status' => 'Completed',
							'updated_at' => 'msqlfunc_NOW()'
						);
						$quote_where = array("clause" => "id=:id", "params" => array(":id" => $quote_id));
						$pdo->update("lead_quote_details", $c_quote_param, $quote_where);

						$lead_track = array(
							'status' => 'Updated',
							'description' => 'Updated status as completed in lead quote details table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

						$quote_inserted = 'Y';
						$currentQuoteId = $pdo->insert("lead_quote_details", $quote_param);
					};
				} else {
					$str_plan_ids = implode(",", array_unique($PlanIdArr));
					$quote_param['agent_id'] = $sponsor_id;
					$quote_param['lead_id'] = $lead_id;
					$quote_param['customer_ids'] = $customer_id;
					$quote_param['order_ids'] = $order_id;
					$quote_param['status'] = 'Pending';
					$quote_param['plan_ids'] = $str_plan_ids;
					$quote_param['created_at'] = 'msqlfunc_NOW()';

					$lead_quote_details_response = $pdo->selectOne("SELECT * FROM lead_quote_details WHERE agent_id = :agent_id AND customer_ids = :customer_ids AND lead_id = :lead_id AND plan_ids LIKE :plan_id", array(":agent_id" => $sponsor_id, ":customer_ids" => $customer_id, ":lead_id" => $lead_id, ":plan_id" => $str_plan_ids));
					if(!empty($lead_quote_details_response)){
						$currentQuoteId = $lead_quote_details_response['id'];
						if($lead_quote_details_response['is_assisted_enrollment'] == 'Y'){
							$quote_where = array("clause" => "id=:id", "params" => array(":id" => $lead_quote_details_response['id']));
							$pdo->update("lead_quote_details", $quote_param, $quote_where);

							$lead_track = array(
								'status' => 'Updated',
								'description' => 'Updated with status as Pending in lead quote details table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);

						} else {
							$c_quote_param = array(
								'status' => 'Completed',
								'updated_at' => 'msqlfunc_NOW()'
							);
							$quote_where = array("clause" => "id=:id", "params" => array(":id" => $lead_quote_details_response['id']));
							$pdo->update("lead_quote_details", $c_quote_param, $quote_where);
							$currentQuoteId = $pdo->insert("lead_quote_details", $quote_param);

							$lead_track = array(
								'status' => 'Updated',
								'description' => 'Updated with status as Completed in lead quote details table',
							);
						
							lead_tracking($lead_id,$customer_id,$lead_track);
						}
					} else {
						$currentQuoteId = $pdo->insert("lead_quote_details", $quote_param);

						$lead_track = array(
							'status' => 'Inserted',
							'description' => 'Inserted in lead quote details table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

					}
				}

				$customer_where = array(
					'clause' => 'id = :id',
					'params' => array(
						':id' => $customer_id,
					),
				);

				if (empty($existing_customer_id)) {
					if ($enrollment_type != 'quote') {
						$updateCustomer = array(
							'status' => 'Pending Quote',
							'updated_at' => 'msqlfunc_NOW()',
							"invite_at" => "msqlfunc_NOW()"

						);
						$pdo->update('customer', $updateCustomer, $customer_where);
						activity_feed(3,$sponsor_row['id'], $sponsor_row['type'],$sponsor_row['id'], $sponsor_row['type'],'Enrolled A New Member', $primary_fname, $primary_lname);
						activity_feed(3,$customer_id, 'customer',$customer_id, 'customer','Enrolled', $primary_fname, $primary_lname);
					}
				}

				if(in_array($application_type,array('member_signature'))){
					if (isset($_SESSION["exist_email"])) {
						unset($_SESSION["exist_email"]);
					}
					if (isset($_SESSION["shop"])) {
						unset($_SESSION["shop"]);
					}
				}

				$response['status'] = 'payment_fail';
				$response['lead_quote_detail_id'] = $currentQuoteId;
				$response['customer_id'] = $customer_id;
				$response['billing_profile_id'] = $billing_profile_id;
				$response['payment_error'] = ($payment_error ? $payment_error : 'Error in processing Enrollment');
			}
			
			if(!empty($lead_tracking_id)) {
				$tracking_where = array(
					'clause' => 'id=:id',
					'params' => array(
						':id' => $lead_tracking_id,
					),
				);
				$tracking_data = array(
					'is_request_completed' => 'Y',
					'order_status' => (isset($orderParams['status'])?$orderParams['status']:''),
				);
				$pdo->update('lead_tracking',$tracking_data,$tracking_where);
				$ld_desc = array(
					'page' => 'ajax_member_enrollment',
					'paymentApproved' => ($paymentApproved?'true':'false'),
					'application_type' => $application_type,
				);
				$tracking_data = array(
					'status' => 'submit_application_end',
					'order_status' => (isset($orderParams['status'])?$orderParams['status']:''),
					'is_request_completed' => 'Y',
					'customer_id' => $customer_id,
					'lead_id' => $lead_id,
					'order_id' => $order_id,
					'description' => json_encode($ld_desc),
				);
				$pdo->insert('lead_tracking',$tracking_data);
			}

		//********* Update Lead and Customer Status code end   ********************

		//********* Payable Insert Code Start ********************
			if($sponsor_billing_method == 'individual' && !$only_waive_products && $billing_display){
				if ($paymentApproved == true && $enroll_with_post_date != "yes"){
			    	if($payment_mode != "ACH"){

						$payable_params=array(
							'payable_type'=>'Vendor',
							'type'=>'Vendor',
							'transaction_tbl_id' => $transactionInsId['id'],
						);
						$payable=$function_list->payable_insert($order_id,0,0,0,$payable_params);

						$lead_track = array(
							'status' => 'Inserted',
							'description' => 'Inserted in Payable table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
			    	}
			    }
			}
		//********* Payable Insert Code End   ********************

		//********* Beneficiery Insert Code Start ********************
		    $tmpPrincipal = !empty($_POST['principal_queBeneficiaryFullName']) ? $_POST['principal_queBeneficiaryFullName'] : array();
		    if(!empty($tmpPrincipal)){
		    	$saved_principal_ids = array();
		    	foreach ($tmpPrincipal as $key => $value) {
		    		$principal_beneficiary_id = !empty($_POST['principal_beneficiary_id'][$key]) ? $_POST['principal_beneficiary_id'][$key] : 0;
		    		if(!empty($principal_beneficiary_id)) {
		    			$sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
		    			$resBeneficiery = $pdo->selectOne($sqlBeneficiery,array(":id"=>$principal_beneficiary_id));
		    		} else {
		    			$resBeneficiery = array();
		    		}

		    		$benficiaryProduct = !empty($_POST['principal_product'][$key]) ? implode(",", $_POST['principal_product'][$key]) : '';
		    		$name = !empty($_POST['principal_queBeneficiaryFullName'][$key]) ? $_POST['principal_queBeneficiaryFullName'][$key] : '';
		    		$address = !empty($_POST['principal_queBeneficiaryAddress'][$key]) ? $_POST['principal_queBeneficiaryAddress'][$key] : '';
		    		$cell_phone = !empty(phoneReplaceMain($_POST['principal_queBeneficiaryPhone'][$key])) ? phoneReplaceMain($_POST['principal_queBeneficiaryPhone'][$key]) : '';
		    		$email = !empty($_POST['principal_queBeneficiaryEmail'][$key]) ? $_POST['principal_queBeneficiaryEmail'][$key] : '';
		    		$ssn = !empty($_POST['principal_queBeneficiarySSN'][$key]) ? $_POST['principal_queBeneficiarySSN'][$key] : '';
		    		$relationship = !empty($_POST['principal_queBeneficiaryRelationship'][$key]) ? $_POST['principal_queBeneficiaryRelationship'][$key] : '';
		    		$percentage = !empty($_POST['principal_queBeneficiaryPercentage'][$key]) ? $_POST['principal_queBeneficiaryPercentage'][$key] : '';
		    		$insParams=array(
		    			'beneficiary_type'=>'Principal',
		    			'product_ids'=> $benficiaryProduct,
		    			'customer_id'=>$customer_id,
		    			'name'=>$name,
		    			'address'=>$address,
		    			'cell_phone'=>$cell_phone,
		    			'email'=>$email,
		    			'relationship'=>$relationship,
		    			'percentage'=>$percentage,
		    		);
		    		if(!empty($ssn)){
		    			$insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
						$insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn), -4);
		    		}

		    		if(!empty($resBeneficiery)){
		    			$updWhr = array(
							'clause' => 'id = :id',
							'params' => array(
								':id' => $resBeneficiery['id'],
							),
						);
						$pdo->update("customer_beneficiary",$insParams,$updWhr);
						$saved_principal_ids[] = $resBeneficiery['id'];

						$lead_track = array(
							'status' => 'Updated',
							'description' => 'Updated customer beneficiary table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);

		    		}else{
		    			$saved_principal_ids[] = $pdo->insert("customer_beneficiary",$insParams);

		    			$lead_track = array(
							'status' => 'Inserted',
							'description' => 'Inserted in customer beneficiary table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
		    		}
		    	}
		    	if(count($saved_principal_ids) > 0) {
		    		$updWhr = array(
						'clause' => 'customer_id=:customer_id AND id NOT IN('.implode(',',$saved_principal_ids).') AND beneficiary_type="Principal"',
						'params' => array(
							':customer_id' => $customer_id,
						),
					);
			    	$pdo->update('customer_beneficiary',array('is_deleted' => 'Y'),$updWhr);
		    	}
		    } else {
				if($is_principal_beneficiary == 'not_displayed'){
					$updWhr = array(
						'clause' => 'customer_id=:customer_id AND beneficiary_type="Principal"',
						'params' => array(
							':customer_id' => $customer_id,
						),
					);
					$pdo->update('customer_beneficiary',array('is_deleted' => 'Y'),$updWhr);
				}
		    }

		    $tmpContingent = !empty($_POST['contingent_queBeneficiaryFullName']) ? $_POST['contingent_queBeneficiaryFullName'] : array();
		    if(!empty($tmpContingent)){
		    	$saved_principal_ids = array();
		    	foreach ($tmpContingent as $key => $value) {
		    		$contingent_beneficiary_id = !empty($_POST['contingent_beneficiary_id'][$key]) ? $_POST['contingent_beneficiary_id'][$key] : 0;
		    		if(!empty($contingent_beneficiary_id)) {
		    			$sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
		    			$resBeneficiery = $pdo->selectOne($sqlBeneficiery,array(":id"=>$contingent_beneficiary_id));
		    		} else {
		    			$resBeneficiery = array();
		    		}

		    		$benficiaryProduct = !empty($_POST['contingent_product'][$key]) ? implode(",", $_POST['contingent_product'][$key]) : '';
		    		$name = !empty($_POST['contingent_queBeneficiaryFullName'][$key]) ? $_POST['contingent_queBeneficiaryFullName'][$key] : '';
		    		$address = !empty($_POST['contingent_queBeneficiaryAddress'][$key]) ? $_POST['contingent_queBeneficiaryAddress'][$key] : '';
		    		$cell_phone = !empty(phoneReplaceMain($_POST['contingent_queBeneficiaryPhone'][$key])) ? phoneReplaceMain($_POST['contingent_queBeneficiaryPhone'][$key]) : '';
		    		$email = !empty($_POST['contingent_queBeneficiaryEmail'][$key]) ? $_POST['contingent_queBeneficiaryEmail'][$key] : '';
		    		$ssn = !empty($_POST['contingent_queBeneficiarySSN'][$key]) ? $_POST['contingent_queBeneficiarySSN'][$key] : '';
		    		$relationship = !empty($_POST['contingent_queBeneficiaryRelationship'][$key]) ? $_POST['contingent_queBeneficiaryRelationship'][$key] : '';
		    		$percentage = !empty($_POST['contingent_queBeneficiaryPercentage'][$key]) ? $_POST['contingent_queBeneficiaryPercentage'][$key] : '';
		    		$insParams=array(
		    			'beneficiary_type'=>'Contingent',
		    			'product_ids'=> $benficiaryProduct,
		    			'customer_id'=>$customer_id,
		    			'name'=>$name,
		    			'address'=>$address,
		    			'cell_phone'=>$cell_phone,
		    			'email'=>$email,
		    			'relationship'=>$relationship,
		    			'percentage'=>$percentage,
		    		);
		    		if(!empty($ssn)){
		    			$insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
						$insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn), -4);
		    		}
		    		if(!empty($resBeneficiery)){
		    			$updWhr = array(
							'clause' => 'id = :id',
							'params' => array(
								':id' => $resBeneficiery['id'],
							),
						);
						$pdo->update("customer_beneficiary",$insParams,$updWhr);
						$saved_principal_ids[] = $resBeneficiery['id'];

						$lead_track = array(
							'status' => 'Updated',
							'description' => 'Updated customer beneficiary table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
		    		}else{
		    			$saved_principal_ids[] = $pdo->insert("customer_beneficiary",$insParams);

		    			$lead_track = array(
							'status' => 'Inserted',
							'description' => 'Inserted customer beneficiary table',
						);
					
						lead_tracking($lead_id,$customer_id,$lead_track);
		    		}
		    	}
		    	if(count($saved_principal_ids)) {
		    		$updWhr = array(
						'clause' => 'customer_id=:customer_id AND id NOT IN('.implode(',',$saved_principal_ids).') AND beneficiary_type="Contingent"',
						'params' => array(
							':customer_id' => $customer_id,
						),
					);
			    	$pdo->update('customer_beneficiary',array('is_deleted' => 'Y'),$updWhr);
		    	}
		    } else {
				if($is_contingent_beneficiary == 'not_displayed'){
					$updWhr = array(
						'clause' => 'customer_id=:customer_id AND beneficiary_type="Contingent"',
						'params' => array(
							':customer_id' => $customer_id,
						),
					);
					$pdo->update('customer_beneficiary',array('is_deleted' => 'Y'),$updWhr);
				}
		    }
		//********* Beneficiery Insert Code End   ********************
  	}
}else {
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
	$response['div_step_error'] = $div_step_error;
}

header('Content-type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
