<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$customer_id = $_POST['id'];
$is_list_bill = false;
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$ws_sql = "SELECT ws.*,p.name as product_name 
			FROM website_subscriptions ws
			JOIN order_details od ON (od.website_id = ws.id AND od.is_deleted='N')
			JOIN prd_main p ON(p.id = ws.product_id)
			JOIN payable py ON(py.product_id = ws.product_id AND py.customer_id = ws.customer_id)
			WHERE 
			ws.product_type = 'Normal' AND 
			md5(ws.customer_id)=:customer_id
			GROUP BY ws.id
			ORDER BY p.name";
$ws_res = $pdo->select($ws_sql,array(":customer_id"=>$customer_id));

$check_sponser = $pdo->selectOne("SELECT cs.billing_type FROM customer s
	    JOIN `customer_group_settings` cs ON (s.id = cs.customer_id)
	    JOIN customer c ON (c.sponsor_id = s.id) WHERE s.type='Group' AND md5(c.id) = :cid",array(":cid"=>$customer_id));		
if(!empty($check_sponser) && checkIsset($check_sponser['billing_type']) == 'list_bill'){
	$is_list_bill = true;
}	
include_once 'tmpl/member_payable_tab.inc.php';
?>