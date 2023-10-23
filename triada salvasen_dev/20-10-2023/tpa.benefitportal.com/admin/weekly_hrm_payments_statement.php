<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$has_querystring = false;

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

$per_page = 10;
if (isset($_GET['weekly_pages']) && $_GET['weekly_pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['weekly_pages'];
}
$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
  'results_per_page' => $per_page,
  'url' => 'weekly_hrm_payments_statement.php?is_ajaxed=1&' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params
);

$page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
    try {
        $sel_sql = "SELECT COUNT(DISTINCT hrmp.group_id) AS groupId,COUNT(DISTINCT hrmp.payer_id) AS memberId,
    hrmp.pay_period AS payPeriod,SUM(hrmp.amount) AS totalAmount,hrmp.pay_date AS payDate
    FROM hrm_payment hrmp
                 JOIN customer c ON(c.id = hrmp.group_id AND c.type = 'Group')
                 JOIN customer s ON (s.id = hrmp.payer_id AND s.type = 'Customer')
                 WHERE hrmp.status IN('Pending','Completed','Cancelled','NonCompliant')
                 AND hrmp.hrm_payment_duration='weekly' AND hrmp.is_deleted='N'
                 GROUP BY hrmp.pay_period ORDER BY hrmp.pay_period DESC";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
    include_once 'tmpl/weekly_hrm_payments_statement.inc.php';
    exit;
}
