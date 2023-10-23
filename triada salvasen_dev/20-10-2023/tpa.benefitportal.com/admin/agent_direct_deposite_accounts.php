<?php
include_once __DIR__ . '/includes/connect.php';

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$per_page = '';
$incr = "";
$agent_id = checkIsset($_GET['agent_id']);

if($is_ajaxed){
$sch_params = array();
$SortBy = "created_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";
$has_querystring = false;

$sch_params[':id'] = $agent_id;

if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$options = array(
    'results_per_page' =>6,
    'url' => 'agent_direct_deposite_accounts.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
    try {
    $selRecords ="SELECT md5(id) as id,effective_date,termination_date,status,account_type,bank_name,routing_number,account_number FROM `direct_deposit_account` where customer_id=:id ORDER BY  $SortBy $currSortDirection";

    $paginate_records = new pagination($page, $selRecords, $options);
    if ($paginate_records->success == true) {
        $fetchRecords = $paginate_records->resultset->fetchAll();
        $totalRecords = count($fetchRecords);
    }
    include_once 'tmpl/agent_direct_deposite_accounts.inc.php';
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}
?>