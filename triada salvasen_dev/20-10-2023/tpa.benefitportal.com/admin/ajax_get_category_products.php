<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$res = array();

$productJSON = $_POST['category_detail'];
$html = urldecode($productJSON);
$res['html'] = $html;

header('Content-Type:appliaction/json');
echo json_encode($res);
exit;
?>