<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(63);
 
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Fees Setting';
$breadcrumbes[1]['link'] = 'service_fees.php';
$breadcrumbes[2]['title'] = 'Manage Service Fees';
$prdIncr = '';
$page_title = "Manage Service Fees";
$fee_id=isset($_GET['id']) ? $_GET['id'] : 0;
if(isset($_GET['fee_id'])){
	$fee_id = $_GET['fee_id'];
}
if(isset($_GET['product_id'])){
	$product_id = $_GET['product_id'];
	$prdIncr .=' OR id='.$product_id;
}
if($fee_id > 0){
	$sqlFeeProduct="SELECT group_concat(id) as feeProduct 
					FROM prd_main 
					WHERE find_in_set(:fee_id,service_fee_ids) AND is_deleted='N'";
	$resFeeProduct=$pdo->selectOne($sqlFeeProduct,array(":fee_id"=>$fee_id));

	if(!empty($resFeeProduct['feeProduct'])){
		$prdIncr .=' OR id IN ('.$resFeeProduct['feeProduct'].')';
	}
	
}
$iframe = isset($_GET['iframe']) ? $_GET['iframe'] : 0;

$sqlProduct="SELECT * FROM prd_main where id=:id and product_type='Service Fee'";
$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$fee_id));

$resPriceMatrix = array();
if($fee_id > 0){
	if($resProduct){
		$product_name = $resProduct['name'];
		$product_code = $resProduct['product_code'];
		
		$is_fee_on_renewal = $resProduct['is_fee_on_renewal'];
		$fee_renewal_type = $resProduct['fee_renewal_type'];
		$fee_renewal_count = $resProduct['fee_renewal_count'];
		
		$is_fee_on_commissionable = $resProduct['is_fee_on_commissionable'];

		$sqlPriceMatrix="SELECT price,non_commission_amount,commission_amount 
						FROM prd_matrix 
						WHERE product_id=:fee_id and is_deleted='N'";
		$resPriceMatrix=$pdo->selectOne($sqlPriceMatrix,array(":fee_id"=>$fee_id));

		if($resPriceMatrix){
			$price = $resPriceMatrix['price'];
			$non_commissionable_price = $resPriceMatrix['non_commission_amount'];
			$commissionable_price = $resPriceMatrix['commission_amount'];
		}

		$service_fee_ids_products = array();

		$checkServiceFee="SELECT group_concat(id) as productList FROM prd_main where is_deleted='N' AND FIND_IN_SET(:fee_id,service_fee_ids)";
		$resServiceFee=$pdo->selectOne($checkServiceFee,array(":fee_id"=>$fee_id));

		$service_fee_ids_products = explode(",", $resServiceFee['productList']);
	}else{
		setNotifyError("Service Fee Not Found");
		redirect("service_fees.php");
	}
}


$productListSql="SELECT id,name,product_code,record_type 
				FROM prd_main 
				WHERE id!=:id AND (status='Active' $prdIncr) AND is_deleted='N' AND type NOT IN('Kit','Fees','Association') 
				ORDER BY name ASC";
$productListRes=$pdo->select($productListSql,array(":id"=>$fee_id));

 
$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css',
);
$exJs = array(
	'thirdparty/multiple-select-master/multiple-select-name.js',
	'thirdparty/price_format/jquery.price_format.2.0.js',
	"thirdparty/ajax_form/jquery.form.js",
);
if($iframe==1){
	$template = "manage_service_fees.inc.php";
	include_once 'layout/iframe.layout.php';
}else{
	$layout = "main.layout.php";
	$template = "manage_service_fees.inc.php";
	include_once 'layout/end.inc.php';
}
?>