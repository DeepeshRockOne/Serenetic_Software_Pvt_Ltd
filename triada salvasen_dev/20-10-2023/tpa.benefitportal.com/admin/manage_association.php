<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(15);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Association';
$breadcrumbes[1]['link'] = 'associations.php';
$breadcrumbes[2]['title'] = 'Manage Association';

$page_title = "Manage Association";
$fee_id=isset($_GET['id']) ? $_GET['id'] : 0;
$prdIncr = '';
if(isset($_GET['fee_id'])){
	$fee_id = $_GET['fee_id'];
}
if(isset($_GET['product_id'])){
	$product_id = $_GET['product_id'];
	$prdIncr .=' OR id='.$product_id;
}
if($fee_id > 0){
	$sqlFeeProduct="SELECT group_concat(id) as feeProduct FROM prd_main where find_in_set(:fee_id,association_ids) AND is_deleted='N'";
	$resFeeProduct=$pdo->selectOne($sqlFeeProduct,array(":fee_id"=>$fee_id));

	if(!empty($resFeeProduct['feeProduct'])){
		$prdIncr .=' OR id IN ('.$resFeeProduct['feeProduct'].')';
	}
	
}

$iframe = isset($_GET['iframe']) ? $_GET['iframe'] : 0;

$sqlProduct="SELECT * FROM prd_main where id=:id and product_type = 'Association'";
$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$fee_id));

$resPriceMatrix = array();
$association_states = array();
$product_name = $product_code = $is_fee_to_association = $is_association_fee_included = $is_fee_on_renewal = $is_fee_on_commissionable = $is_assign_by_state  = "";

$price =$non_commissionable_price =$commissionable_price = 0 ;
$associationStateRes = array();

if($fee_id>0){
	if($resProduct){
		$product_name = $resProduct['name'];
		$product_code = $resProduct['product_code'];
		
		$is_fee_to_association = $resProduct['is_fee_to_association'];
		$is_association_fee_included = $resProduct['is_association_fee_included'];

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


		$association_ids_products = array();

		$checkAssociation="SELECT group_concat(id) as productList FROM prd_main where is_deleted='N' AND FIND_IN_SET(:fee_id,association_ids)";
		$resAssociation=$pdo->selectOne($checkAssociation,array(":fee_id"=>$fee_id));

		$association_ids_products = explode(",", $resAssociation['productList']);

		

		$is_assign_by_state = $resProduct['is_assign_by_state'];
		$associationStateSql="SELECT group_concat(product_id) as product_id,states FROM association_assign_by_state where is_deleted='N' AND association_fee_id = :fee_id group by states";
		$associationStateRes=$pdo->select($associationStateSql,array(":fee_id"=>$fee_id));

		
	}else{
		setNotifyError("Association Not Found");
		redirect("associations.php");
	}
}




$productListSql="SELECT id,name,product_code,record_type 
				FROM prd_main 
				WHERE id!=:id AND (status='Active' $prdIncr) AND is_deleted='N' and type NOT IN('Kit','Fees','Association') 
				ORDER BY name ASC";
$productListRes=$pdo->select($productListSql,array(":id"=>$fee_id));

 

$stateSql="SELECT * FROM states_c where country_id=:country_id and is_deleted='N' ORDER BY name ASC";
$stateRes=$pdo->select($stateSql,array(":country_id"=>231));

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css',
);
$exJs = array(
	'thirdparty/multiple-select-master/multiple-select-name.js',
	'thirdparty/price_format/jquery.price_format.2.0.js',
	"thirdparty/ajax_form/jquery.form.js",
);
if($iframe==1){
	$template = "manage_association.inc.php";
	include_once 'layout/iframe.layout.php';
}else{
	$layout = "main.layout.php";
	$template = "manage_association.inc.php";
	include_once 'layout/end.inc.php';
}
?>