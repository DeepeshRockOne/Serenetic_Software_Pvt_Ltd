<?php
include_once __DIR__ . '/includes/connect.php'; 

$product_id =  checkIsset($_GET['product_id']);

$description = "";

if(!empty($product_id)){

	$description = $pdo->selectOne("SELECT p.id,p.name,pd.enrollment_desc as description,pd.limitations_exclusions from prd_descriptions pd LEFT JOIN prd_main p on(p.id = pd.product_id) where md5(product_id)=:product_id",array(":product_id"=>$product_id));
	// pre_print($description);
}

$template = 'prd_description_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>