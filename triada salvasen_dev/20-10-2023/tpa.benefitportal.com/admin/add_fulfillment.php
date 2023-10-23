<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_id = isset($_GET['id']) ? $_GET['id'] : "";
$file_name = "";
$products = array();
if($file_id){
	$file_data = $pdo->selectOne("SELECT * FROM fulfillment_files where id = :id",array(':id' => $file_id));

	if($file_data){
		$file_name = $file_data['file_name'];
		$products = ($file_data['products'] ? explode(',', $file_data['products']) : array());
	}
}

$productRes = get_active_global_products_for_filter(0,false,true);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "add_fulfillment.inc.php";
include_once 'layout/iframe.layout.php';
?>