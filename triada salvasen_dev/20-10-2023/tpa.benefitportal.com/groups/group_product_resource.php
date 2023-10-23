<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(10);
$product_id = !empty($_GET['product_id']) ? $_GET['product_id'] : '';
$search_val = !empty($_GET['search_val']) ? $_GET['search_val'] : '' ;

$incr = "";
$sch_params = array();

$sch_params[':product_id']=$product_id;

$search_val = cleanSearchKeyword($search_val); 
 
if(!empty($search_val)){
  $is_search = true;
  $incr = " AND  (r.name like '%$search_val%' OR sr.coll_type like '%$search_val%' )";
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
if ($is_ajaxed) { 
	$sqlResourse = "SELECT r.id AS resourse_id,r.name AS resourse_name,sr.coll_type,sr.description,sr.coll_doc_url,md5(sr.id) as sub_resource_id
	FROM resources r 
	JOIN res_products rp ON (r.id=rp.res_id)
	JOIN sub_resources sr ON (r.id = sr.res_id AND sr.is_deleted='N')
	JOIN prd_main pm ON(pm.id=rp.product_id OR pm.parent_product_id=rp.product_id)
	WHERE r.user_group='Group'  AND r.status='Active' AND r.is_deleted='N' AND md5(pm.id)=:product_id $incr
	ORDER BY r.id DESC";
	$resResourse = $pdo->select($sqlResourse,$sch_params);

	include_once 'tmpl/group_product_resource.inc.php';
	exit;

}
?>
