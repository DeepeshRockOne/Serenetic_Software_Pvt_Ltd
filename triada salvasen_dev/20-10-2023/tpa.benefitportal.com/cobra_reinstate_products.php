<?php
include_once __DIR__ . '/includes/connect.php';
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : "";
$is_reinstate = isset($_GET['reinstate']) ? $_GET['reinstate'] : "";
$terminated_subscriptions = get_terminated_subscriptions_for_cobra($customer_id,$is_reinstate);
// pre_print($terminated_subscriptions);
/*--- Remove Prd If Restricted Prd Active ---*/
if($terminated_subscriptions){
	foreach ($terminated_subscriptions as $key => $value) {
		$res_restricted_products = $pdo->selectOne("SELECT restricted_products FROM prd_main where id = :id",array(':id' => $value['product_id']));
		if(!empty($res_restricted_products) && !empty($res_restricted_products['restricted_products'])) {
			$restricted_products = $res_restricted_products['restricted_products'];
			$check_active_products = $pdo->selectOne("SELECT id FROM website_subscriptions where status in('Active','Pending Payment') AND product_id in($restricted_products) and customer_id = :id",array(':id' =>$customer_id));
			if(!empty($check_active_products)){
				unset($terminated_subscriptions[$key]);
			}
		}
	}
}
/*---/Remove Prd If Restricted Prd Active ---*/

$rep_id = getname('customer', $customer_id, 'rep_id', 'id');

$billing_sql = "SELECT * FROM customer_billing_profile WHERE is_default = 'Y' AND customer_id=:cust_id";
$billing_where = array(":cust_id" => $customer_id);
$billing_row = $pdo->selectOne($billing_sql,$billing_where);

$state_res = $pdo->select("SELECT * FROM states_c WHERE country_id = 231");

if(isset($_POST['new_plan_id']) && $_POST['new_plan_id'] != ""){
	$new_plan_type = $_POST['new_plan_id'];
	$customer_id = $_POST['customer_id'];
	$product_id = $_POST['product_id'];
	$dep_res = array();
	if ($new_plan_type == "4" || $new_plan_type == "5") {
	    $sel = "SELECT *,IF(LOWER(relation)IN('husband','wife'),'Spouse','Child') as crelation FROM customer_dependent_profile WHERE customer_id=:customer_id AND is_deleted='N' ORDER BY FIELD (crelation,'Spouse','Child') ASC, created_at DESC";
	    $arr1 = array(":customer_id" => $customer_id);
	    $dep_res = $pdo->select($sel, $arr1);
	}
	if ($new_plan_type == "2") {
	    $sel = "SELECT * FROM customer_dependent_profile WHERE customer_id=:customer_id AND is_deleted='N' AND LOWER(relation) IN ('son','daughter') ORDER BY created_at DESC";
	    $arr1 = array(":customer_id" => $customer_id);
	    $dep_res = $pdo->select($sel, $arr1);
	}
	if ($new_plan_type == "3") {
	    $sel = "SELECT * FROM customer_dependent_profile WHERE customer_id=:customer_id AND is_deleted='N' AND LOWER(relation) IN ('wife','husband') ORDER BY created_at DESC";
	    $arr1 = array(":customer_id" => $customer_id);
	    $dep_res = $pdo->select($sel, $arr1);
	}
	$content = "";
	if($dep_res){
		foreach ($dep_res as $dep) { 
			$relation = "";
            $selected = "";
			if(in_array(strtolower($dep['relation']), array('son','daughter'))){
				$relation = 'Child';
			} else {
				$relation = "Spouse";
			}
            if(!empty($selected_dep_ids) && in_array($dep['id'],$selected_dep_ids)) {
                $selected = "selected";
            }
			
			$content .= '<option value="'. $dep['id'] . '" '.$selected .'>' . $dep['display_id'] . ' - '.$dep['fname'] .' '. $dep['lname'] . ' ( '.$relation . ')</option>'; 
		}
	}

	$get_price = $pdo->selectOne("SELECT price FROM prd_matrix WHERE product_id = :product_id AND plan_type = :plan_type AND is_deleted = 'N'",array(':product_id' => $product_id,':plan_type' => $new_plan_type));

	$price = 0;
	$cobra_service_fee = 0;
	$total_amount = 0;
	if($get_price){
		$price = $get_price['price'];
		$get_cobra_service_fee = $pdo->selectOne("SELECT additional_surcharge FROM group_cobra_benefits WHERE group_use_cobra_benefit = 'Y' AND is_additional_surcharge = 'Y'");
		if($get_cobra_service_fee){
			$cobra_service_fee = $get_cobra_service_fee['additional_surcharge'];

			$total_amount = $price + ($price * $cobra_service_fee / 100);
		}
	}

	echo json_encode(array('content' => $content,'price' => displayAmount($price,2),'total_amount' => displayAmount($total_amount,2),'cobra_service_fee' => $cobra_service_fee));
	exit();
}
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css' . $cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache);

$template = 'cobra_reinstate_products.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>