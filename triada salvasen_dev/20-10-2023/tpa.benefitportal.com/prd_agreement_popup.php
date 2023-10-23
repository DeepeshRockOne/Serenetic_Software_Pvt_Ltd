<?php
include_once __DIR__ . '/includes/connect.php'; 

$order_id =  checkIsset($_REQUEST['order_id']);
$product_list =  checkIsset($_REQUEST['product_list']);
$agreementContent = '';

if(!empty($order_id)){

	$sqlOrder="SELECT o.customer_id,p.id as productId,p.product_code,pa.joinder_agreement 
					FROM orders o
					JOIN order_details od on (o.id=od.order_id AND od.is_deleted='N')
					JOIN prd_main p ON (p.id=od.product_id AND p.joinder_agreement_require='Y')
					JOIN prd_agreements pa ON(p.id=pa.product_id AND pa.is_deleted='N')
					where md5(o.id)=:order_id";
	$resOrder=$pdo->select($sqlOrder,array(":order_id"=>$order_id));
	// pre_print($resOrder,false);

	if(!empty($resOrder)){
		foreach ($resOrder as $key => $odrRow) {
			$joinderAgreement = $odrRow['joinder_agreement'];
			
			if(!empty($joinderAgreement)){
				$smart_tags = get_user_smart_tags($odrRow["customer_id"],'member',$odrRow['productId']);
	
	            if(!empty($smart_tags)){
	            	foreach ($smart_tags as $key => $value) {
	            		$joinderAgreement = str_replace("[[" . $key . "]]", $value, $joinderAgreement);
	            	}
	            }
	            $agreementContent .= $joinderAgreement;
			}
			
		}
	}
}else if(!empty($product_list)){

	$sqlPrd="SELECT p.id as productId,p.product_code,pa.joinder_agreement 
					FROm prd_main p
					JOIN prd_agreements pa ON(p.id=pa.product_id AND pa.is_deleted='N')
					where p.joinder_agreement_require='Y' AND p.id IN(".$product_list.") ";
	$resPrd=$pdo->select($sqlPrd);

	if(!empty($resPrd)){
		foreach ($resPrd as $key => $prdRow) {
			$joinderAgreement = $prdRow['joinder_agreement'];
			
			if(!empty($joinderAgreement)){
				$smart_tags = get_user_smart_tags($prdRow['productId'],'product');
	
	            if(!empty($smart_tags)){
	            	foreach ($smart_tags as $key => $value) {
	            		$joinderAgreement = str_replace("[[" . $key . "]]", $value, $joinderAgreement);
	            	}
	            }
	            $agreementContent .= $joinderAgreement;
			}
			
		}
	}

}

$template = 'prd_agreement_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>