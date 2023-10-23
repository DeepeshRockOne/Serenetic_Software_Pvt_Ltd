<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : 0;
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
$plan_id = isset($_GET['plan_id']) ? $_GET['plan_id'] : 0;
$agent_portal = $product_description = '';
$cust_sql = "SELECT c.* 
			FROM customer c
			WHERE c.id=:id";
$cust_row = $pdo->selectOne($cust_sql,array(":id"=>$customer_id));

$cust_state_id = $allStateResByName[$cust_row['state']]['id'];

$sqlDesc = "SELECT enrollment_desc,agent_portal,agent_info,limitations_exclusions FROM prd_descriptions where product_id = :product_id";
$resDesc = $pdo->selectOne($sqlDesc,array(":product_id"=>$product_id));

if(!empty($resDesc)){
	// $enrollmentPage = $resDesc['enrollment_desc'];
	$agent_portal = $resDesc['agent_portal'];

}

$smart_tags = get_user_smart_tags($product_id,'product');
foreach($smart_tags as $placeholder => $value){
	$agent_portal = str_replace("[[" . $placeholder . "]]", $value,$agent_portal);
}
$ws_sql = "SELECT ws.*,p.product_code,p.name as product_name
                FROM website_subscriptions as ws
                JOIN prd_main p on(p.id=ws.product_id)
				WHERE ws.product_type = 'Normal' AND ws.customer_id=:customer_id AND ws.product_id = :product_id AND FIND_IN_SET(:plan_id,ws.plan_id) 
				ORDER BY product_name ASC";
$ws_where = array(":customer_id" => $customer_id,":product_id" => $product_id,":plan_id" => $plan_id);
$ws_res = $pdo->select($ws_sql, $ws_where);

// pre_print($ws_res);
//echo $customer_id;
$products_res = array();
if(!empty($ws_res)) {
	foreach ($ws_res as $key => $ws_row) {
		$product_row = array(
			'ws_id' => $ws_row['id'],
			'product_code' => $ws_row['product_code'],
			'product_name' => $ws_row['product_name'],
			'customer_rep_id' => $cust_row['rep_id'],
			'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
			'prd_plan_type' => (isset($prdPlanTypeArray[$ws_row['prd_plan_type_id']]['title'])?$prdPlanTypeArray[$ws_row['prd_plan_type_id']]['title']:''),
			'eligibility_date' => strtotime($ws_row['eligibility_date']) ? date('m/d/Y',strtotime($ws_row['eligibility_date'])):'',
			'purchase_date' => strtotime($ws_row['purchase_date']) ? date('m/d/Y',strtotime($ws_row['purchase_date'])):'',
			'status' => $ws_row['status'],
			'price' => displayAmount($ws_row['price']),
			'tab_res' => array(),
			'prd_resources' => array(),
			'id_card_available' => 'N',
		);
		$tab_sql = "SELECT * FROM prd_member_portal_information WHERE is_deleted = 'N' AND product_id=:product_id";
		$tab_where = array(":product_id" => $ws_row['product_id']);
		$tab_res = $pdo->select($tab_sql,$tab_where);
		if(!empty($tab_res)) {
			$product_row['tab_res'] = $tab_res;
			// $product_description = $tab_res[0]['description'];
		}
		$smart_tags = get_user_smart_tags($ws_row['product_id'],'product');
		foreach($smart_tags as $placeholder => $value){
			$product_description = str_replace("[[" . $placeholder . "]]", $value,$product_description);
		}
		
		/*--- Prd Resources ---*/
		$resource_sql = "SELECT r.id,sr.id,r.display_id,r.name,r.type,r.user_group,sr.coll_doc_url,sr.coll_type,sr.video_type ,GROUP_CONCAT(DISTINCT(rs.state_id)) AS state_id,sr.group_id,sr.state_url,sr.description,r.status, r.created_at
				FROM sub_resources sr 
				JOIN resources r ON(sr.res_id = r.id)
				JOIN res_products rp ON(rp.res_id=r.id) 
				JOIN prd_main pm ON(pm.id=rp.product_id OR pm.parent_product_id=rp.product_id)
				LEFT JOIN res_states rs ON(rs.sub_res_id = sr.id)
				WHERE 
				sr.is_deleted='N' AND
				r.is_deleted='N' AND 
				r.status='Active' AND 
				r.user_group='Member' AND
				pm.id=:product_id AND 
				r.effective_date <= :today_date AND 
				(r.termination_date = '0000-00-00' OR r.termination_date IS NULL OR r.termination_date >= :today_date) AND 
				(rs.state_id IS NULL OR rs.state_id=:state_id)
				GROUP BY sr.id
				ORDER BY name,sr.group_id";
		$resource_where = array(
							":product_id" => $ws_row['product_id'],
							":state_id" => $cust_state_id,
							":today_date" => date('Y-m-d')
						);
		$resource_res = $pdo->select($resource_sql,$resource_where);
		if(!empty($resource_res)) {
			$tmp_resource_res = array();
			foreach ($resource_res as $key => $resource_row) {
				if(in_array($resource_row['type'],array('Certificate','Collateral'))) {
					$tmp_resource_res[] = $resource_row;
				}
				if(in_array($resource_row['type'],array('id_card'))) {
					$product_row['id_card_available'] = 'Y';
				}
			}
			if(!empty($tmp_resource_res)) {
				$product_row['prd_resources'] = $tmp_resource_res;	
			}			
		}
		/*---/Prd Resources ---*/

		$products_res[] = $product_row;
	}
}

$exStylesheets = array('thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css');
$exJs = array('thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.min.js');

$template = 'member_product_detail.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
