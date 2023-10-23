<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$incr = "";
$sch_params = array();
$SortBy = "hrmp.pay_period";
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
$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$customer_id = checkIsset($_GET["customer_id"]);
$real_id = getname('customer',$customer_id,'id','MD5(id)');

if (!empty($customer_id)) {
    $sch_params[':customer_id'] = makeSafe($real_id);
    $incr .= " AND hrmp.payer_id = :customer_id";
}

if (count($sch_params) > 0) {
    $has_querystring = true;
}

$per_page = 10;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'member_non_compliant_hrm_payments.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
    try {
        $sel_sql = "SELECT s.rep_id AS grpRepId,
                    IF(csts.company_name != '',csts.company_name,CONCAT(s.fname,' ',s.lname)) AS groupName,
                    hrmp.pay_period,hrmp.pay_date,hrmp.amount AS totalAmount,gc.pay_period as hrm_payment_duration,lbd.start_coverage_date,lbd.end_coverage_date
                    FROM hrm_payment hrmp
                    JOIN customer AS s ON(s.id = hrmp.group_id AND s.type='Group')
                    JOIN customer_settings csts ON (s.id = csts.customer_id)
                    JOIN customer c ON (c.id = hrmp.payer_id AND c.type='Customer')
                    JOIN customer_settings cs ON (c.id = cs.customer_id)
                    JOIN list_bill_details lbd ON(lbd.customer_id=c.id AND lbd.id=hrmp.list_bill_detail_id)
                    JOIN group_classes gc ON(gc.id=cs.class_id)
                    WHERE hrmp.is_deleted='N'
                    AND hrmp.status IN('NonCompliant') $incr ORDER BY $SortBy $currSortDirection";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }

    include_once 'tmpl/member_non_compliant_hrm_payments.inc.php';
    exit;
}

include_once 'tmpl/member_non_compliant_hrm_payments.inc.php';
?>
