<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_id = isset($_GET['id']) ? $_GET['id'] : "";
$product_type = "";
$file_name = "";
$products = array();
if($file_id){
	$file_data = $pdo->selectOne("SELECT * FROM eligibility_files where id = :id",array(':id' => $file_id));

	if($file_data){
		$file_name = $file_data['file_name'];
		$product_type = $file_data['product_type'];
		$products = ($file_data['products'] ? explode(',', $file_data['products']) : array());
	}
}

$productRes = get_active_global_products_for_filter(0,false,true);

$subProductSql="SELECT p.id,p.product_name as name,p.product_code,'sub_product' as type,'Sub Product' as title 
            FROM sub_products p 
            where p.is_deleted='N' AND p.status='Active'  GROUP BY p.id ORDER BY product_name ASC";
$subProductRes = $pdo->selectGroup($subProductSql,array(),'title');


$participantsProductSql="SELECT p.product_code
            FROM participants_products p
            where p.is_deleted='N' GROUP BY p.product_code ORDER BY product_code ASC";
$participantsProductRes = $pdo->select($participantsProductSql);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "add_eligibility.inc.php";
include_once 'layout/iframe.layout.php';
?>