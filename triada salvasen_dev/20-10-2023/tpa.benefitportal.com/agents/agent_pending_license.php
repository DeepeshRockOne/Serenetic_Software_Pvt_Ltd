<?php
include_once __DIR__ . '/includes/connect.php';
$id = isset($_REQUEST['id'])?$_REQUEST['id']:$agent_id;
$is_ajaxed = isset($_GET['is_ajaxed_license']) ? $_GET['is_ajaxed_license'] : '';

$per_page = '';
if($is_ajaxed){
$sch_params[":agent_id"] = $id;
if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$query_string.="&#license_table";
$options = array(
    'results_per_page' => 10,
    'url' => 'agent_pending_license.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
    try {
    $selADoc = "SELECT * FROM agent_license WHERE (new_request='Y' or is_rejected='Y') AND md5(agent_id)=:agent_id AND is_deleted='N'";
    $paginate_license = new pagination($page, $selADoc, $options);
    if ($paginate_license->success == true) {
        $fetchDocs = $paginate_license->resultset->fetchAll();
        $totalDocs = count($fetchDocs);
    }
    include_once 'tmpl/agent_pending_license.inc.php';
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
} else {
    include_once 'tmpl/agent_pending_license.inc.php';
}
?>