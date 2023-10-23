<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT carrier.name as carrierName,p.product_code,sp.product_code AS sub_product_code,sp.product_name,sp.status as subStatuts,psp.* FROM prd_sub_products psp
JOIN sub_products sp ON (sp.id = psp.sub_product_id)
JOIN prd_main p ON (p.id = psp.product_id)
JOIN prd_fees carrier ON(carrier.id=sp.carrier_id)
WHERE psp.is_deleted='N' AND p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		//Insert Sub Poroduct's Carrier start
			$selectCarrier = "SELECT id,name FROM prd_fees WHERE setting_type='Carrier' AND name=:name AND is_deleted='N' ";
			$resultCarrier = $pdo->selectOne($selectCarrier, array(":name"=>$value['carrierName']));
		
			if (empty($resultCarrier['id'])) {
				$display_id = get_carrier_id();
				$insert_params = array(
				'name' => $value['carrierName'],
				'display_id' => $display_id,
				'setting_type' => 'Carrier',
				'contact_fname' => '',
				'contact_lname' => '',
				'phone' => '',
				'email' => '',
				'status' => 'Active',
				'use_appointments' => 'N',
				);
				$carrier_id = $pdo->insert("prd_fees", $insert_params);
			}else{
				$carrier_id = $resultCarrier['id'];
			}
		//Insert Sub Poroduct's Carrier end
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		$sqlSubPrd = "SELECT id FROM sub_products where product_code=:product_code AND product_name=:product_name";
		$resSubPrd = $pdo->selectOne($sqlSubPrd,array(":product_name"=>$value['product_name'],":product_code"=>$value['sub_product_code']));

		if(empty($resSubPrd['id'])){
			$insSubParams=array(
				'carrier_id' => makeSafe($carrier_id),
				'product_code' => makeSafe($value['sub_product_code']),
				'product_name' => makeSafe($value['product_name']),
				'status' => makeSafe($value['subStatuts']),
			);
			$sub_product_id = $pdo->insert('sub_products',$insSubParams);	
		}else{
			$sub_product_id = $resSubPrd['id'];
		}
		if(!empty($resPrd) && $sub_product_id!=''){
			$product_id = $resPrd['id'];
			$sub_product_id = $sub_product_id;
			
			$insSubParams=array(
				'product_id'=>$product_id,
				'sub_product_id'=>$sub_product_id,
			);
			$pdo->insert('prd_sub_products',$insSubParams);	
		}
		
	}
}
echo "import_product_sub_products -> Completed";
dbConnectionClose();
exit;
?>