<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";

$commObj = new Commission();

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$per_page = '';

$incr = "";
$agent_id = checkIsset($_GET['agent_id']);
if($is_ajaxed){
$sch_params = array();
$SortBy = "cs.pay_period";
$SortDirection = "ASC";
$currSortDirection = "DESC";
$has_querystring = false;

if ($agent_id != "") {
    $sch_params[':agent_id'] = $agent_id;
    $incr.=" AND cs.customer_id = :agent_id";
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
    'results_per_page' => 6,
    'url' => 'agent_weekly_commission.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);
$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
    try {
    $selRecords ="SELECT cs.id ,cs.pay_period,cs.sub_type,cs.status,cs.customer_id,
    SUM(IF((cs.sub_type='Reverse' AND cs.is_fee_comm='N') OR (cs.type='Adjustment' AND cs.balance_type='revCredit') OR (cs.is_fee_comm='Y' AND cs.sub_type='Fee'),cs.amount,0)) AS debit_amount, 
    SUM(IF((cs.sub_type!='Reverse' AND cs.is_fee_comm='N') OR (cs.type='Adjustment' AND cs.balance_type='addCredit') OR (cs.is_fee_comm='Y' AND cs.sub_type='Reverse'),cs.amount,0)) AS credit_amount
    FROM commission cs
    WHERE cs.commission_duration='weekly' AND  cs.is_deleted = 'N' " . $incr . " GROUP BY cs.pay_period
    ORDER BY  $SortBy $currSortDirection";

    $paginate_records = new pagination($page, $selRecords, $options);
    if ($paginate_records->success == true) {
        $fetchRecords = $paginate_records->resultset->fetchAll();
        $totalRecords = count($fetchRecords);
    }
    include_once 'tmpl/agent_weekly_commission.inc.php';
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}
?>