<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$module_access_type = has_access(92);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Active Members";
$breadcrumbes[1]['link'] = '#';

$page_title = "Members";
$user_groups = "active";

$sch_params = array();
$incr = '';
$SortBy = "month_date";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
//Report export Start
$export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';
if(!empty($export_val)){
    include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
    $job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Active Member End Of The Month","active_member_of_month",'', array(),array(),'active_member_export');
    echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
    exit;
}
//Report export End
$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
    $has_querystring = true;
    $SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
    $has_querystring = true;
    $currSortDirection = $_GET["sort_direction"];
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'active_member_of_month.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
if($is_ajaxed){
    try {

        $sel_sql = "SELECT month_date,active_members FROM active_members WHERE is_deleted='N' ORDER BY  $SortBy $currSortDirection";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
    include_once 'tmpl/active_member_of_month.inc.php';
    exit;
}

$template = 'active_member_of_month.inc.php';
include_once 'layout/end.inc.php';
