<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//group_has_access(10);
$product_id = !empty($_GET['product_id']) ? $_GET['product_id'] : '';

$sqlProduct= "SELECT p.id,p.product_code,p.name,pd.agent_portal,pd.limitations_exclusions,pd.agent_info 
	FROM prd_main p 
	LEFT JOIN prd_descriptions pd ON (pd.product_id = p.id) 
	WHERE md5(p.id)=:product_id";
$resProduct = $pdo->selectOne($sqlProduct,array(":product_id"=>$product_id));

$product_name = "";
$product_description = "";
$limitations_exclusions = "";
if(!empty($resProduct)){
	$product_name = $resProduct['name'];
	$product_description = $resProduct['agent_portal'];
	$limitations_exclusions = $resProduct['limitations_exclusions'];
}

$smart_tags = get_user_smart_tags($resProduct['id'],'product');
foreach($smart_tags as $placeholder => $value){
	$product_description = str_replace("[[" . $placeholder . "]]", $value,$product_description);
	$limitations_exclusions = str_replace("[[" . $placeholder . "]]", $value,$limitations_exclusions);
}
$exStylesheets = array('thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css');
$exJs = array('thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.min.js');

$template = 'agent_product_detail.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
