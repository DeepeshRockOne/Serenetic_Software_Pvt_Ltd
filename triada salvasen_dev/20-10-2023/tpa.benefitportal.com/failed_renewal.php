<?php 
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
include_once 'UserTimezone.php';
$validate = new Validation();
$function_list = new functionsList();
$is_dob_verified = false;

$order_id = isset($_REQUEST['token']) ? $_REQUEST['token'] : "";
if(empty($order_id)){
	$template = '404.inc.php';
	$layout = 'iframe.layout.php';
	include_once 'layout/end.inc.php';
	exit();

}
$timezone = date_default_timezone_get();
if(isset($_REQUEST['timezone'])){
	$timezone = $_REQUEST['timezone'];
}
$tz = new UserTimeZone('M d, Y h:i:s A T', $timezone);

$order_row = $pdo->selectOne("SELECT id,display_id,customer_id,is_renewal,post_date,status FROM orders o where md5(id) = :order_id and o.status = 'Payment Declined'",array(":order_id" => $order_id));


if(empty($order_row)){
	$template = '404.inc.php';
	$layout = 'iframe.layout.php';
	include_once 'layout/end.inc.php';
	exit();
}

$currentOrderDet = $pdo->select("SELECT o.id,o.customer_id,od.plan_id,od.start_coverage_period FROM orders o JOIN order_details od ON(od.order_id = o.id) WHERE md5(o.id) = :order_id AND od.is_deleted = 'N' GROUP BY od.id",array(":order_id" => $order_id));

$is_payment_approved = false;
if($currentOrderDet){
	foreach ($currentOrderDet as $order_det) {
		$selectExisting = $pdo->selectOne("SELECT o.id FROM orders o JOIN order_details od on(od.order_id = o.id) WHERE o.customer_id = :customer_id AND od.plan_id = :plan_id AND od.is_deleted='N' AND o.status = 'Payment Approved' AND od.start_coverage_period = :start_coverage_period",array(":customer_id" => $order_det['customer_id'],":plan_id" => $order_det['plan_id'],":start_coverage_period" => $order_det['start_coverage_period']));
		if($selectExisting){
			$is_payment_approved = true;
		}
	}
}

if($is_payment_approved){
	setNotifyError("Charge already taken for this coverage period");
	$template = '404.inc.php';
	$layout = 'iframe.layout.php';
	include_once 'layout/end.inc.php';
	exit();
}

$cust_row = $pdo->selectOne("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as member_name,c.birth_date,c.sponsor_id,c.fname,c.lname,c.address,c.address_2,c.city,c.state,c.zip,c.status FROM customer c WHERE c.id = :id", array(":id" => $order_row['customer_id']));

$sponsor_row = $pdo->selectOne("SELECT cs.display_in_member,c.public_name,c.public_email,c.public_phone,cs.brand_icon,c.id,c.rep_id,c.type,c.sponsor_id FROM customer c JOIN customer_settings cs on(cs.customer_id = c.id) WHERE c.id = :id",array(":id" => $cust_row['sponsor_id']));

$app_settings_row = $pdo->select("SELECT * FROM app_settings");

$user_name = "Member Services";
$display_phone_number = "";
$display_email = "";

if($sponsor_row && $sponsor_row['display_in_member'] == 'N'){
	$user_name = $sponsor_row['public_name'];
	$display_phone_number = $sponsor_row['public_phone'];
	$display_email = $sponsor_row['public_email'];
}else{
	if($app_settings_row){
		foreach ($app_settings_row as $value) {
			if($value['setting_key'] == "member_services_cell_phone"){
				$display_phone_number = $value['setting_value'];
			}
			if($value['setting_key'] == "member_services_email"){
				$display_email = $value['setting_value'];
			}
		}
	}
}
$image_url = $POWERED_BY_LOGO;

if(!empty($sponsor_row['brand_icon']) && file_exists($AGENTS_BRAND_ICON . $sponsor_row['brand_icon'])){
	$image_url = $AGENTS_BRAND_ICON_WEB . $sponsor_row['brand_icon'];
}

$order_details_info = $pdo->select("SELECT plan_id FROM order_details WHERE order_id = :order_id AND is_deleted='N'",array(':order_id' => $order_row['id']));
	
$PlanIdArr= array();

if($order_details_info){
	foreach ($order_details_info as $order_detail) {
		array_push($PlanIdArr, $order_detail['plan_id']);
	}
}

$sale_type_params = array();
$sale_type_params['is_renewal'] = $order_row['is_renewal'];
$sale_type_params['customer_id'] = $order_row['customer_id'];
$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $cust_row['sponsor_id'], "CC",$sale_type_params);

$ach_payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $cust_row['sponsor_id'], "ACH",$sale_type_params);

$cvv_required = "N";
$is_cc_accept = true;
$is_ach_accept = true;

if(!empty($payment_master_id)){
	$sqlProcessor = "SELECT * FROM payment_master where id=:id";
	$resProcessor = $pdo->selectOne($sqlProcessor,array(":id"=>$payment_master_id));

	if(!empty($resProcessor)){
		$cvv_required = $resProcessor['require_cvv'];
		$cards = $resProcessor['acceptable_cc'] ? explode(',', $resProcessor['acceptable_cc']) : array();
	}
}else{
	$is_cc_accept = false;
}

if(!empty($ach_payment_master_id)){
	$sqlProcessor = "SELECT * FROM payment_master where id=:id";
	$ach_resProcessor = $pdo->selectOne($sqlProcessor,array(":id"=>$ach_payment_master_id));

	if(!empty($resProcessor)){
		$cvv_required = $resProcessor['require_cvv'];
	}
}else{
	$is_ach_accept = false;
}

// pre_print($order_row);

$resOrder = $pdo->selectOne("SELECT o.id as odrId,o.display_id as odrDispId,CONCAT(c.fname,' ',c.lname) as mbrName, 
  			c.id as mbrId,c.rep_id as mbrDispId,c.cell_phone as mbrPhone,c.email as mbrEmail,c.sponsor_id,
                c.user_name as mbrUserName,o.status as odrStatus,o.sub_total as subTotal,o.grand_total as grandTotal,o.created_at as odrDate,o.post_date as odrPostDate,
                ob.address as billAdd,ob.address2 as billAdd2,ob.city as billCity,ob.state as billState,
                ob.zip as billZip,ob.payment_mode as billPayType,ob.card_type as billCardType,ob.last_cc_ach_no as lastPayNo,o.transaction_id as transactionId,DATE_FORMAT(o.created_at,'%m/%d/%Y') as transactionDate,o.payment_processor_res as processorResponse,o.order_comments as odrReason
          FROM orders o
          LEFT JOIN customer c ON (c.id = o.customer_id)
          LEFT JOIN customer s ON (c.sponsor_id = s.id)
          LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
          WHERE md5(o.id) = :id",array(':id' => $order_id));

if($resOrder){
	$orderId = checkIsset($resOrder["odrId"]);
	$customer_id = checkIsset($resOrder["mbrId"]);
	$orderDispId = checkIsset($resOrder['odrDispId']);
	$orderStatus = checkIsset($resOrder["odrStatus"]);
	$odrPostDate = !empty($resOrder["odrPostDate"]) ? date("m/d/Y",strtotime($resOrder["odrPostDate"])) : "";
	$transactionDate =  $orderStatus != "Post Payment" ? $resOrder['transactionDate'] : "";
	$transactionId =  $resOrder["transactionId"] > 0 ? $resOrder["transactionId"] : "";
	$subTotal = !empty($resOrder["subTotal"]) ? $resOrder["subTotal"] : 0;
	$grandTotal = !empty($resOrder["grandTotal"]) ? $resOrder["grandTotal"] : 0;
	$stepFeePrice = 0;
	$stepFeeRefund = 'N';
	$serviceFeePrice = 0;
	$serviceFeeRefund = 'N';

	$detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,od.unit_price as price,od.is_refund
        FROM order_details od
        JOIN prd_main pm ON(pm.id=od.product_id)
        LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
        WHERE od.order_id = :odrId AND od.is_deleted='N'
        ORDER BY od.product_name ASC";
	$detRes = $pdo->select($detSql, array(':odrId' => makeSafe($orderId)));
}

$name_on_card = "";
$card_number = "";
$card_type = "";
$expiry_month = "";
$expiry_year = "";
$cvv = "";
$address = "";
$address2 = "";
$city = "";
$state = "";
$zip = "";

$cc_number = "";
$ach_number = "";

$first_name = "";
$last_name = "";
$bank_name = "";
$account_type = "";
$routing_number = "";

$last_cc_ach_no = "";
$payment_mode = "";
$expiry_date = "";

$order_billing_profile = $pdo->selectOne("SELECT fname,lname,city,state,zip,phone,address,address2,payment_mode,last_cc_ach_no,cvv_no,card_type,expiry_month,expiry_year,AES_DECRYPT(ach_routing_number,'Gserw54sf533sS') as routing_number,AES_DECRYPT(card_no_full,'Gserw54sf533sS') as card_number,AES_DECRYPT(ach_account_number,'Gserw54sf533sS') as ach_number,ach_account_type,customer_billing_id,bankname FROM order_billing_info WHERE order_id = :order_id",array(":order_id" => $order_row['id']));

if(empty($order_billing_profile)){
	$order_billing_profile = $pdo->selectOne("SELECT fname,lname,city,state,zip,phone,address,address2,payment_mode,last_cc_ach_no,cvv_no,card_type,expiry_month,expiry_year,AES_DECRYPT(ach_routing_number,'Gserw54sf533sS') as routing_number,AES_DECRYPT(card_no_full,'Gserw54sf533sS') as card_number,AES_DECRYPT(ach_account_number,'Gserw54sf533sS') as ach_number,ach_account_type,bankname FROM customer_billing_profile WHERE customer_id = :customer_id and is_default = 'Y'",array(":customer_id" => $order_row['customer_id']));
}
	$payment_mode = $order_billing_profile['payment_mode'];
	if($payment_mode == 'CC'){
		$name_on_card = $order_billing_profile['fname'] . ' '. $order_billing_profile['lname'];
		$card_type = $order_billing_profile['card_type'];
		$expiry_month = $order_billing_profile['expiry_month'];
		$expiry_year = $order_billing_profile['expiry_year'];
		$expiry_date = $expiry_month . "/" . $expiry_year;
		$cvv = $order_billing_profile['cvv_no'];
		$address = $order_billing_profile['address'];
		$address2 = $order_billing_profile['address2'];
		$city = $order_billing_profile['city'];
		$state = $order_billing_profile['state'];
		$zip = $order_billing_profile['zip'];
		$cc_number = $order_billing_profile['card_number'];
	}else{

		$first_name = $order_billing_profile['fname'];
		$last_name = $order_billing_profile['lname'];
		$bank_name = $order_billing_profile['bankname'];
		$account_type = $order_billing_profile['ach_account_type'];
		$routing_number = $order_billing_profile['routing_number'];
		$ach_number = $order_billing_profile['ach_number'];
	}

	$last_cc_ach_no = $order_billing_profile['last_cc_ach_no'];



if(isset($_POST['dob'])){
	if($_POST['dob'] == ""){
		$validate->setError('dob','Please enter birth date');
	}

	if(!$validate->getError('dob')){
		if(strtotime($cust_row['birth_date']) != strtotime(date('Y-m-d',strtotime($_POST['dob'])))){
			$validate->setError('dob','Birth date is incorrect');
		}
	}

	if($validate->isValid()){
	    $response['status'] = 'success';

	    if(in_array($cust_row['status'],array('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending'))) {
	    	$lead_row = $pdo->selectOne("SELECT l.id,l.lead_id FROM leads l WHERE customer_id=:customer_id", array(":customer_id" => $cust_row['id']));
	    	$description['ac_message'] =array(
			    'ac_red_1'=>array(
			      'title'=> $lead_row['lead_id'],
			    ),
			    'ac_message_1' =>' accessed failed billing for '. $order_row['display_id'],
		  	);
		  	activity_feed(3,$lead_row['id'],'leads',$lead_row['id'],'Lead','Accessed failed billing', $cust_row['fname'],$cust_row['lname'],json_encode($description));
	    } else {
	    	$description['ac_message'] =array(
			    'ac_red_1'=>array(
			      'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
			      'title'=>$cust_row['rep_id'],
			    ),
			    'ac_message_1' =>' accessed failed billing for ',
			    'ac_red_2'=>array(
		                'href'=> 'order_receipt.php?orderId='.md5($order_row['id']),
		                'title'=>$order_row['display_id'],
		            )
		  	);
		  	activity_feed(3, $cust_row['id'], 'Customer',0, 'orders','Accessed failed billing', $cust_row['fname'],$cust_row['lname'],json_encode($description));
	    }
	}else{
		$errors = $validate->getErrors();
	    $response['status'] = 'fail';
	    $response['errors'] = $errors;
	}

	header('Content-Type: application/json');
	echo json_encode($response);
	exit;
}


/*--------/Billing Data -------*/
$billing_data = $pdo->selectOne("SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full, AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number FROM customer_billing_profile WHERE is_default='Y' AND customer_id=:customer_id AND is_deleted='N' ORDER BY id DESC", array(":customer_id" => $customer_id));

$same_as_personal = false;
$payment_mode = '';
if (!empty($billing_data)) {
    if (($cust_row['fname'] == $billing_data['fname']) && ($cust_row['lname'] == $billing_data['lname']) && ($cust_row['address'] == $billing_data['address']) && ($cust_row['city'] == $billing_data['city']) && ($cust_row['state'] == $billing_data['state']) && ($cust_row['zip'] == $billing_data['zip'])) {
        $same_as_personal = true;
    }
    $payment_mode = $billing_data['payment_mode'];
}

$sale_type_params = array();
$sale_type_params['is_renewal'] = $order_row['is_renewal'];
$sale_type_params['customer_id'] = $order_row['customer_id'];
if($sponsor_row['type']=='Group'){
    $payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['sponsor_id'], $payment_mode,$sale_type_params);
}else{
    $payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['id'], $payment_mode,$sale_type_params);
}
$payment_res = $pdo->selectOne("SELECT * from payment_master where id=:id and is_deleted='N'",array(":id"=>$payment_master_id));
$pyament_methods = get_pyament_methods($cust_row['sponsor_id']);
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];
/*-------- Billing Data -------*/

$exStylesheets = array('thirdparty/bootstrap-datepicker-master/css/datepicker.css','thirdparty/sweetalert2/sweetalert2.css');
$exJs = array('thirdparty/masked_inputs/jquery.maskedinput.min.js','thirdparty/jquery-match-height/js/jquery.matchHeight.js','thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js','thirdparty/sweetalert2/sweetalert2.min.js');

$template = 'failed_renewal.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>