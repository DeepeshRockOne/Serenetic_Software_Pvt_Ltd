<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(2);
$customer_id = $_POST['id'];

$next_purchase_info = displayNextBillingDate($customer_id,0,'Y');

include_once 'tmpl/member_orders_tab.inc.php';
?>