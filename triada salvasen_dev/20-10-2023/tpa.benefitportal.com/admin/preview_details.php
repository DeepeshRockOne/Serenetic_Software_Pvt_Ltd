<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$prdArr = array(
    '1893' => array(
        'plan_type' => 4,
        'annual_hrm_payment' => array(
            '1' => 5000.00,
            '2' => 8000.00,
            '3' => 7500.00,
            '4' => 10000.00,
            '5' => 7500.00
        ),
        'price' => 120.00,
        'name' => 'GAP pls product'
    ),
);
// $product_list = checkIsset($_GET['product_list']);
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array(
    'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
    'thirdparty/price_format/jquery.price_format.2.0.js'.$cache,
);


$template = 'preview_details.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
