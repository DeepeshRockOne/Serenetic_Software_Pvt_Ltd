<?php
include_once __DIR__ . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[1]['link'] = 'merchant_processor.php';
$breadcrumbes[2]['title'] = "Merchant Processor";


$is_show_key = checkIsset($_POST['is_show_key']);
if($is_show_key){
	$processor_id = checkIsset($_POST['processor_id']);
	$keyType = checkIsset($_POST['keyType']);
	$processorType = checkIsset($_POST['processorType']);
	$response = array("status"=>"fail");

	$resPro = $pdo->selectOne("SELECT id,name from payment_master where md5(id)=:id and type=:type and is_deleted='N'",array(":id"=>$processor_id,":type"=>$processorType));
	if(!empty($resPro) && !empty($resPro['id'])){
		$description['ac_message'] =array(
			'ac_red_1'=>array(
			  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			  'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>'  read Merchant Processor '.$keyType.' (',
			'ac_red_2'=>array(
			  'href'=> $ADMIN_HOST.'/add_merchant_processor.php?type='.$processorType.'&id='.$processor_id,
			  'title'=> $resPro['name'],
			),
			'ac_message_2' =>')',
		  );
		$desc=json_encode($description);
		activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $resPro['id'], 'MerchantProcessor', 'Admin Read Merchant Processor '.$keyType,$_SESSION['admin']['name'],"",$desc);
		$response = array("status"=>"success","key_type"=>$keyType);
	}
	
	header('Content-type: application/json');
	echo json_encode($response);
	exit;
}

$page_title = 'Add Processor';
$user_groups = "active";

$payment_master_id = !empty($_GET['id']) ? $_GET['id'] : '';
$type = !empty($_GET['type']) ? $_GET['type'] : 'Global';
if(!empty($payment_master_id)){
	$breadcrumbes[2]['link'] = 'add_merchant_processor.php?type='.$type.'&id='.$payment_master_id;
	$breadcrumbes[2]['title'] = "Edit Merchant";
	$page_title = 'Edit Processor';
}
$assigned_products = array();

if($type == 'Variation'){
	$incr_p ='';
	$sch_p_param = array();
	if(!empty($payment_master_id)){
		$sch_p_param[':id'] = $payment_master_id;
		$incr_p = ' AND md5(payment_master_id)!=:id ';
	}
	$assigned_products = $pdo->selectOne("SELECT GROUP_CONCAT(product_id) as assigned_product from payment_master_assigned_product pp JOIN payment_master p ON(p.id=pp.payment_master_id) where pp.is_deleted='N' and p.is_deleted='N' $incr_p ",$sch_p_param);
	if(!empty($assigned_products['assigned_product'])){
		$assigned_products = array_unique(explode(',',$assigned_products['assigned_product']));
	}
}
$product_ids = $agent_downline_id_arr = $agents_loa_id_arr = $acceptable_cc_arr = $variation_product_id_arr = $agent_ids = array();
$gateway_id = 0;
$accept_ach_value = $accept_cc_value= $merchant_id = '';
$payment_master_res = array('is_assigned_to_all_agent'=>'Y','is_assigned_to_all_product'=>'Y');
// get live_details,selected_prodcuts,selected_agents for edit processor variation
if(!empty($payment_master_id) && !empty($type)) {
	$payment_master_res = $pdo->selectOne("SELECT * FROM payment_master WHERE md5(id) = :id and type=:type", array(":id" => $payment_master_id,':type'=>$type));
	if(!empty($payment_master_res)){
		$processor_name = $payment_master_res['name'];
		$merchant_id = $payment_master_res['merchant_id'];
		$gateway_id = $payment_master_res['gateway_id'];
		$gateway_name = $payment_master_res['gateway_name'];

		$monthly_threshold_sale = $payment_master_res['monthly_threshold_sale'];
		$description = $payment_master_res['description'];

		$accept_ach_value = $payment_master_res['is_ach_accepted'];
		$is_accept_ach_default = $payment_master_res['is_default_for_ach'];

		$accept_cc_value = $payment_master_res['is_cc_accepted'];
		$acceptable_cc_arr = explode(',',$payment_master_res['acceptable_cc']);
		$is_accept_ach_default = $payment_master_res['is_default_for_ach'];
		
		$live_details = json_decode($payment_master_res['live_details'], true);
		$live_url = $live_details['url'];
		// Authorize
		if($gateway_id == 1){
			$transaction_key = $live_details["transaction_key"];
			$login_id = $live_details["login_id"];

		}else if($gateway_id == 2 || $gateway_id == 3){
			// NMI or C&H
			$user_name = $live_details["user_name"];
			$password = $live_details["password"];
			$api_key = $live_details["api_key"];

		}elseif ($gateway_id == 4) {
			// USAEPay
			$user_name = $live_details["user_name"];
			$api_pin = $live_details["api_pin"];
			$api_key = $live_details["api_key"];
			
		}elseif ($gateway_id == 5) {
			// PayByCliq
			$user_name = $live_details["user_name"];
			$password = $live_details["password"];
			$service_key = $live_details["service_key"];
			
		}elseif ($gateway_id == 6) {
			// CyberSource
			$api_key = $live_details["api_key"];
			$secret_key = $live_details["secret_key"];
			
		}

		if($payment_master_res['is_assigned_to_all_product'] == 'N') {
			$products_res = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(product_id)) as assigned_product from payment_master_assigned_product where payment_master_id=:id and is_deleted='N'",array(":id"=>$payment_master_res['id']));
			$product_ids = explode(",", $products_res['assigned_product']);
		}
		if($payment_master_res['is_assigned_to_all_agent'] == 'N') {

			$agents_res = $pdo->select("SELECT * from payment_master_assigned_agent where payment_master_id=:id AND status!='Deleted' and is_deleted='N'",array(":id"=>$payment_master_res['id']));
			$agent_ids =$agent_downline_id_arr=$agents_loa_id_arr = array();//explode(",", $agents_res['assigned_agent']);
			if(!empty($agents_res)){
				foreach($agents_res as $agent){
					array_push($agent_ids,$agent['agent_id']);
					if($agent['include_downline'] == 'Y'){
						array_push($agent_downline_id_arr,$agent['agent_id']);
					}
					if($agent['loa_only'] == 'Y'){
						array_push($agents_loa_id_arr,$agent['agent_id']);
					}
				}
			}
		}
		
	}else{
		setNotifyError('No record found!');
		redirect("merchant_processor.php");
	}
}
$agent_res = $pdo->select("SELECT id, fname, lname, rep_id FROM customer WHERE is_deleted = 'N' AND type ='Agent'");

$productSql="SELECT p.id,p.name,p.parent_product_id,p.product_code,pc.title as category_name 
			FROM prd_main p
			LEFT JOIN prd_category pc on(pc.id=p.category_id and pc.is_deleted='N')
			WHERE p.type NOT IN('Fees','Kit') AND p.name != '' AND p.is_deleted='N' AND p.record_type='Primary'  
			ORDER BY p.name ASC";
$productRes=$pdo->select($productSql);

$company_arr = array('No category' => array());
if(!empty($productRes)){
	foreach ($productRes as $key => $value) {
		if($value['category_name']!=''){
			$company_arr[$value['category_name']][]=$value;
		}else{
			$company_arr['No category'][]=$value;
		}
	}
	if(empty($company_arr['No category'])){
		unset($company_arr['No category']);
	}
}
// select URL code start
	$payment_res = $pdo->select("SELECT id,live_details,gateway_id FROM payment_master WHERE is_deleted = 'N'/* AND status = 'Active'*/");
	$url_arr = array();
	$uniq_url_arr = array();
	if(!empty($payment_res) && count($payment_res) > 0){
		$i = 0;
		foreach ($payment_res as $key => $value) {
			$live_details = json_decode($value['live_details'],true);
			if(!empty($live_details) && (count($live_details) > 0) && !empty($live_details['url']) && !in_array($live_details['url'], $url_arr)){
				$uniq_url_arr[$i]['gateway_id'] = $value['gateway_id'];
				$uniq_url_arr[$i]['url'] = $live_details['url'];
				$i++;
				array_push($url_arr, $live_details['url']);
			}
		}
	}
	$urlOptionaArr = array(
		"1" => "https://api2.authorize.net",
		"2" => "https://secure.nmi.com/api/transact.php",
		"3" => "https://secure.chfsgateway.com/api/transact.php",
		"4" => "https://usaepay.com/api/v2/transactions",
		"5" => "https://wswest.cfinc.com/ach/ACHOnlineServices.svc",
		"6" => "https://api.cybersource.com",
	);
// select URL code ends
$gateway_details_res = $pdo->select("SELECT id,gateway_name,gateway_url FROM payment_gateway_details WHERE is_deleted = 'N'");

$exStylesheets = array('thirdparty/bootstrap-switch/css/bootstrap3/bootstrap-switch.css','thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/bootstrap-switch/js/bootstrap-switch.js','thirdparty/ajax_form/jquery.form.js','thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js','thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,);

$template = 'add_merchant_processor.inc.php';
include_once 'layout/end.inc.php';
?>