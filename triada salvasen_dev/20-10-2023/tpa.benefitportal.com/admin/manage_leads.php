<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Groups';
$breadcrumbes[1]['link'] = 'javascript:void(0);';
$breadcrumbes[2]['title'] = 'Leads';
$breadcrumbes[2]['link'] = 'lead_listing.php';
$breadcrumbes[3]['title'] = 'Manage Leads';
$breadcrumbes[3]['link'] = 'manage_leads.php';
$summernote = "Y";

$tz = new UserTimeZone('m/d/Y @ g:i A T', $_SESSION['admin']['timezone']);

$current_time = $tz->getDate('', 'Y-m-d H:i:s');
$next_import_time = date("n/j/Y @ g:i a ",ceil(strtotime($current_time) / 15 / 60) * 15 * 60);
$next_import_time .= $tz->getDate('', 'T');
$is_ajaxed = isset($_GET['is_ajaxed'])?$_GET['is_ajaxed']:false;
$per_page = 10;
$SortBy = "id";
$SortDirection = "DESC";
if ($is_ajaxed) {
    $sch_params = array();
    $has_querystring = false;
    
    if (count($sch_params) > 0) {
        $has_querystring = true;
    }
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
        $has_querystring = true;
        $per_page = $_GET['pages'];
    }

    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;

    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

    $options = array(
        'results_per_page' => $per_page,
        'url' => 'manage_leads.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params
    );    
    $options = array_merge($pageinate_html, $options);
    try {
        $sel_sql = "SELECT * FROM csv_agent_leads WHERE 1 ORDER BY id DESC";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
        include_once 'tmpl/leads_import_summary.inc.php';
        exit();
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}

$lead_agreement = get_app_settings('lead_agreement');
/*$select = "SELECT * FROM `app_settings` WHERE setting_key='lead_agreement'";
$row = $pdo->selectOne($select);
if(!empty($row)) {
	$lead_agreement = $row['setting_value'];
}*/

$exJs = array('thirdparty/ckeditor/ckeditor.js');

$template = 'manage_leads.inc.php';
include_once 'layout/end.inc.php';
?>