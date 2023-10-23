<?php
include_once __DIR__ . '/includes/connect.php'; 

$product_id =  checkIsset($_GET['product_id']);

$terms_condition = "";
$temp_prd_id = 0;
if(!empty($product_id)){

	$terms_condition = $pdo->selectOne("SELECT p.id,p.name,terms_condition FROM prd_terms_condition pt LEFT JOIN prd_main p ON(p.id=pt.product_id) where md5(product_id) = :product_id and pt.is_deleted='N' and p.is_deleted='N'",array(":product_id" => $product_id));
	if($terms_condition){
		$temp_prd_id = $terms_condition['id'];
		if(!empty($terms_condition['terms_condition'])){

			$smart_tags = get_user_smart_tags($terms_condition['id'],'product');
            if($smart_tags){
            	foreach ($smart_tags as $key => $value) {
            		$terms_condition['terms_condition'] = str_replace("[[" . $key . "]]", $value, $terms_condition['terms_condition']);
            	}
            }
			
		}
	}
}

$template = 'prd_term_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>