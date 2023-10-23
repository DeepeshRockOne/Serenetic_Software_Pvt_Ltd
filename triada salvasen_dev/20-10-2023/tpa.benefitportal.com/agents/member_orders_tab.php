<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$customer_id = $_POST['id'];
$has_full_access = agent_has_member_access($customer_id);
$next_purchase_info = displayNextBillingDate($customer_id,0,'Y');

include_once 'tmpl/member_orders_tab.inc.php';
?>