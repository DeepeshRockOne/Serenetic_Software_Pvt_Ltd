<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$productFees = !empty($_POST['productFees']) ? json_decode($_POST['productFees'],true) : array();
$groupEnrollmentPrd = checkIsset($_POST['groupEnrollmentPrd']);


$fetch_rows = $productFees;

$total_rows = count($fetch_rows);

include_once 'tmpl/prd_product_fee_list.inc.php';
exit;

?>