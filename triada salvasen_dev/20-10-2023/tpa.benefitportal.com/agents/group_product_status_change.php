<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$location = isset($_GET['location'])?$_GET['location']:'agent';

$group_id = checkIsset($_GET['group_id']);
$product_ids = checkIsset($_GET['product_ids']);
$product_status = checkIsset($_GET['product_status']);
$text = '';
if($product_status == 'Contracted'){
    $text = 'Active status allows for new sales and renewals based on the rules of the product.';
}else if($product_status == 'Suspended'){
    $text = 'Suspended status allows renewals to continue and ability to receive commissions, but stops new applications.';
}else if($product_status == 'Extinct'){
    $text = 'Extinct status allows renewals to continue but the group does not receive renewal commissions and stops new applications.<p class="fs14 m-b-10">Additionally setting a termination status will also  terminate all policies of account as of selection below :</p>';
}

$template = 'group_product_status_change.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
