<?php
include_once __DIR__ . '/includes/connect.php';
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : "";
$ws_id = isset($_GET['ws_id']) ? $_GET['ws_id'] : "";
$terminated_subscriptions = get_terminated_subscriptions($customer_id);

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
			/*---/Check same product purchased and its Active/Pending START----*/
			$check_same_active_products = $pdo->selectOne("SELECT id FROM website_subscriptions WHERE status IN('Active','Pending Payment') AND product_id = :product_id AND id != :website_id and customer_id = :id", array(':id' => $customer_id,':product_id' => $value['product_id'],':website_id' => $value['ws_id']));
		    if (isset($terminated_subscriptions[$key]) && $check_same_active_products) {
		        unset($terminated_subscriptions[$key]);
		    }
			/*---/Check same product purchased and its Active/Pending END----*/
		}
	}
}
/*---/Remove Prd If Restricted Prd Active ---*/

$rep_id = getname('customer', $customer_id, 'rep_id', 'id');

$billing_sql = "SELECT * FROM customer_billing_profile WHERE is_default = 'Y' AND customer_id=:cust_id";
$billing_where = array(":cust_id" => $customer_id);
$billing_row = $pdo->selectOne($billing_sql,$billing_where);

$state_res = $pdo->select("SELECT * FROM states_c WHERE country_id = 231");

$template = 'reinstate_products.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>