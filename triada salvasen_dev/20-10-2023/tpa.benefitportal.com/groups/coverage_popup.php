<?php
include_once __DIR__ . '/includes/connect.php';

$sch_params = array();
$incr ='';

$SortBy = "p.name";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$group_id = isset($_GET['group_id']) ? $_GET["group_id"] : '';
$id = isset($_GET['id']) ? $_GET["id"] : '';



if (!empty($id)) {
	$incr .= " AND md5(gcpo.id)=:id";
	$sch_params[':id']=$id;
}

if (!empty($group_id)) {
	$incr .= " AND gcpo.group_id=:group_id";
	$sch_params[':group_id']=$group_id;
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'coverage_popup.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
if ($is_ajaxed) {
	try {

		$sel_sql = "SELECT p.name,p.status FROM prd_main p 
			JOIN group_coverage_period_offering gcpo ON FIND_IN_SET (p.id,gcpo.products)
			WHERE gcpo.is_deleted='N' $incr ORDER BY  $SortBy $currSortDirection";
		/*pre_print($incr,false);
		pre_print($sch_params,false);
		pre_print($sel_sql);*/
		$paginate = new pagination($page, $sel_sql, $options);

		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/coverage_popup.inc.php';
	exit;
}

$sqlClass="SELECT gc.class_name FROM group_coverage_period_offering gcpo
			JOIN group_classes gc on (gc.id=gcpo.class_id)
			WHERE md5(gcpo.id)=:offfering_id";
$resClass=$pdo->selectOne($sqlClass,array(":offfering_id"=>$id));
$class_name ="";

if(!empty($resClass)){
	$class_name = $resClass['class_name'];
}
$productSql="SELECT count(p.id) as product_total FROM prd_main p 
			JOIN group_coverage_period_offering gcpo ON FIND_IN_SET (p.id,gcpo.products)
			WHERE gcpo.is_deleted='N' $incr";
$productRes=$pdo->selectOne($productSql,$sch_params);
$product_total = "0";

if(!empty($productRes) && !empty($productRes['product_total'])){
	$product_total = $productRes['product_total'];
}
$template = 'coverage_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
