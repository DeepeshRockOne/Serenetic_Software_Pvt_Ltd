<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//has_access(37);

$SortBy = "created_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$gsearch = isset($_GET['gsearch']) ? $_GET['gsearch'] : '';
$is_ajaxed_admins = isset($_GET['is_ajaxed_admins']) ? $_GET['is_ajaxed_admins'] : '';

$sql_acl = "SELECT name FROM access_level ORDER BY name";
$res_acls = $pdo->select($sql_acl);

$has_querystring = false;
$sch_params = array();
$incr = isset($incr) ? $incr : '';
if ($gsearch != "") {
	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':phone'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':display_id'] = '%' . makeSafe($gsearch) . '%';
	$incr .= " AND (fname LIKE :name OR lname LIKE :name OR CONCAT(trim(fname),' ',trim(lname)) LIKE :name OR CONCAT(trim(lname),' ',trim(fname)) LIKE :name OR email LIKE :email OR phone LIKE :phone OR display_id LIKE :display_id)";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'global_admins.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed_admins){

	try {
		$sel_sql = "SELECT * , TIMESTAMPDIFF(HOUR,invite_at,now()) as invited_difference,md5(id) as id FROM admin WHERE id>0 AND is_deleted='N'" . $incr . " ORDER BY $SortBy $currSortDirection";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
			//pre_print($sel_sql);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
}
	

$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/colorbox/jquery.colorbox.js', 'thirdparty/clipboard/clipboard.min.js');
include_once 'tmpl/global_admins.inc.php';
?>