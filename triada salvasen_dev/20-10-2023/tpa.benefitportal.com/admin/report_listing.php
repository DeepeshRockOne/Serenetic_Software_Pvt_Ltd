<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Reporting";
$breadcrumbes[1]['link'] = 'set_reports.php';

$sch_params = array();
$incr = '';
$SortBy = "r.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$report_name = isset($_GET['report_name']) ? $_GET["report_name"] : '';
$category_id = isset($_GET['category_id']) ? $_GET["category_id"] : array();
$portal = isset($_GET['portal']) ? $_GET["portal"] : array();

if ($report_name) {
    $sch_params[':report_name'] = "%" . makeSafe($report_name) . "%";
    $incr .= " AND r.report_name LIKE :report_name";
}

if (!empty($category_id)) {
    $category_id = "'" . implode("','", makeSafe($category_id)) . "'";
    $incr .= " AND r.category_id IN ($category_id)";
}

if (!empty($portal)) {
    $portal = "'" . implode("','", makeSafe($portal)) . "'";
    $incr .= " AND c.portal IN ($portal)";
}


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
    'url' => 'report_listing.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';

if ($is_ajaxed) {
	try {
        $sel_sql = "SELECT r.*,c.title as category_name,c.portal 
                  FROM $REPORT_DB.rps_reports r
                  JOIN $REPORT_DB.rps_category c ON (c.id = r.category_id)
                  WHERE r.is_deleted = 'N' AND c.is_deleted='N' " . $incr . " 
				  ORDER BY $SortBy $currSortDirection";
        $paginate = new pagination($page, $sel_sql, $options);

        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }    
    include_once 'tmpl/report_listing.inc.php';
    exit;
}

$category_res = $pdo->select("SELECT * FROM $REPORT_DB.rps_category WHERE is_deleted='N' ORDER BY title");

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "report_listing.inc.php";
include_once 'layout/main.layout.php';
?>