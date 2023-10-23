<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
$page_title = "Weekly Commission Statement";

$commObj = new Commission();

$sch_params = array();
$SortBy = "cs.pay_period";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$has_querystring = false;
if (!empty($_GET["sort_by"])) {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}
if (!empty($_GET["sort_direction"])) {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}


$incr = "";
$pay_period = checkIsset($_GET['pay_period']);
$is_ajaxed = $_GET['is_ajaxed'];

$agent_id = $_SESSION['agents']['id'];
$company_id = $_SESSION['agents']['company_id'];

if(!empty($agent_id)){
  $sch_params[':agent_id'] = $agent_id;
  $incr.=" AND cs.customer_id = :agent_id";
}

if (!empty($pay_period)) {
  $incr .= " AND DATE(cs.pay_period) = :pay_period";
  $sch_params[':pay_period'] = $pay_period;
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}

$per_page = 10;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'weekly_commission_statement.php?is_ajaxed=1&' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = !empty($_GET['page']) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
      $sel_sql = "SELECT cs.id ,cs.pay_period,cs.sub_type,cs.status,cs.customer_id,
      SUM(IF((cs.sub_type='Reverse' AND cs.is_fee_comm='N') OR (cs.type='Adjustment' AND cs.balance_type='revCredit') OR (cs.is_fee_comm='Y' AND cs.sub_type='Fee'),cs.amount,0)) AS debit_amount, 
      SUM(IF((cs.sub_type!='Reverse' AND cs.is_fee_comm='N') OR (cs.type='Adjustment' AND cs.balance_type='addCredit') OR (cs.is_fee_comm='Y' AND cs.sub_type='Reverse'),cs.amount,0)) AS credit_amount
         FROM commission cs
         WHERE cs.commission_duration='weekly' AND cs.is_deleted = 'N' " . $incr . " GROUP BY cs.pay_period
         ORDER BY  $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/weekly_commission_statement.inc.php';
  exit;
}

?>