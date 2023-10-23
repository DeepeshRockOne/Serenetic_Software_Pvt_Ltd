<?php
if (file_exists($CACHE_PATH_DIR.$PRODUCT_CACHE_FILE_NAME)) {
	$productCacheMainArray = json_decode((file_get_contents($CACHE_PATH_DIR.$PRODUCT_CACHE_FILE_NAME)),true);
}else{
	ini_set('memory_limit', '-1');
	ini_set('max_execution_time', '-1');

	$productCacheMainArray = array();
	$product_description_res = $pdo->select("SELECT pd.product_id,pd.agent_portal,p.name FROM prd_descriptions pd JOIN prd_main p ON(p.id = pd.product_id) WHERE p.is_deleted = 'N'");
	if($product_description_res){
		foreach ($product_description_res as $key => $value) {

			$product_detail_info = displayAgentPortalDescriptionInfo($value['product_id'],'N');
			$productCacheMainArray[$value['product_id']]['description'] = $product_detail_info."</br>".$value['agent_portal'];
			$productCacheMainArray[$value['product_id']]['name'] = $value['name'];
		}
	}

	$product_cached = fopen($CACHE_PATH_DIR.$PRODUCT_CACHE_FILE_NAME, 'w');
	fwrite($product_cached, json_encode($productCacheMainArray));
	fclose($product_cached);
}
?>