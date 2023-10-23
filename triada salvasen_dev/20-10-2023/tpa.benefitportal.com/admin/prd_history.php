<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$type=!empty($_GET['type']) ? $_GET['type'] : '';
$product_id = !empty($_GET['product']) ? $_GET['product'] : 0;

$template = 'prd_history.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>