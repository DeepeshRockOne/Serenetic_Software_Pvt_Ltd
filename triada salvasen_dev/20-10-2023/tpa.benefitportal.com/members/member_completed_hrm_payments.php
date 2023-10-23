<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$customer_id = $_SESSION['customer']['id'];
$sch_params = array();
$SortBy = "hrmp.pay_period";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$has_querystring = false;
$incr = "";

if ($_GET["hrm_sort_by"] != "") {
    $has_querystring = true;
    $SortBy = $_GET["hrm_sort_by"];
}

if ($_GET["hrm_sort_direction"] != "") {
    $has_querystring = true;
    $currSortDirection = $_GET["hrm_sort_direction"];
}
$is_ajaxed = $_GET['is_ajaxed_hrm'];

if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['hrm_pages']) && $_GET['hrm_pages'] > 0) {
    $has_querystring = true;
    $doc_per_page = $_GET['hrm_pages'];
}

$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$ws_params = array(":customer_id" => $customer_id);
$options = array(
    'results_per_page' =>$doc_per_page,
    'url' => 'member_completed_hrm_payments.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $ws_params,
);


$page = !empty($_GET['page']) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
if ($is_ajaxed) {
    try {
        $ws_sql = "SELECT s.rep_id AS grpRepId,
        IF(csts.company_name != '',csts.company_name,CONCAT(s.fname,' ',s.lname)) AS groupName,
        hrmp.pay_period,hrmp.pay_date,hrmp.amount AS totalAmount,gc.pay_period as hrm_payment_duration,lbd.start_coverage_date,lbd.end_coverage_date
        FROM hrm_payment hrmp
        JOIN customer AS s ON(s.id = hrmp.group_id AND s.type='Group')
        JOIN customer_settings csts ON (s.id = csts.customer_id)
        JOIN customer c ON (c.id = hrmp.payer_id AND c.type='Customer')
        JOIN customer_settings cs ON (hrmp.payer_id = cs.customer_id)
        JOIN list_bill_details lbd ON(lbd.customer_id=c.id AND lbd.id=hrmp.list_bill_detail_id)
        JOIN group_classes gc ON(gc.id=cs.class_id)
        WHERE hrmp.payer_id = :customer_id AND hrmp.is_deleted='N'
        AND hrmp.status IN('Completed') ORDER BY $SortBy $currSortDirection";
        
        $paginate = new pagination($page, $ws_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
    include_once 'tmpl/member_completed_hrm_payments.inc.php';
    exit;
}
?>
