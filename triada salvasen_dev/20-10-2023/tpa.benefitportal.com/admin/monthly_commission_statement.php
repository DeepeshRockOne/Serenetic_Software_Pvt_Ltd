<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

  $incr = "";
  $sch_params = array();
  $has_querystring = false;
  
  $is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

  $agent_id = isset($_GET['agent_id']) ? $_GET['agent_id'] : '';

  if(!empty($agent_id)){
    $sch_params[':agent_id'] = $agent_id;
    $incr .= " AND md5(cs.customer_id) = :agent_id";
  }

  if (count($sch_params) > 0) {
    $has_querystring = true;
  }

  $per_page=10;
  if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
  }
  $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

  $options = array(
      'results_per_page' => $per_page,
      'url' => 'monthly_commission_statement.php?is_ajaxed=1&' . $query_string,
      'db_handle' => $pdo->dbh,
      'named_params' => $sch_params
  );

  $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
  $options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT count(DISTINCT cs.customer_id) as totalPayee,
                      cs.pay_period as payPeriod,
                       SUM(cs.amount) as totalComm,
                       SUM(if(cs.sub_type='New' OR cs.sub_type='Renewals',cs.amount,0)) as earnedComm,
                       SUM(if(cs.sub_type='Advance',cs.amount,0)) as advanceComm,
                       SUM(if(cs.sub_type='PMPM',cs.amount,0)) as pmpmComm,
                       SUM(if(cs.sub_type='Reverse',cs.amount,0)) as reverseComm,
                       SUM(if(cs.sub_type='Fee',cs.amount,0)) as feeComm,
                       SUM(if(cs.type='Adjustment',cs.amount,0)) as adjustComm
             FROM commission cs
             JOIN customer c ON(c.id=cs.customer_id)
             WHERE cs.id>0 AND c.type !='Customer' AND cs.status IN('Pending','Approved','Cancelled')
             AND cs.commission_duration='monthly' AND cs.is_deleted='N' " . $incr . "
             GROUP BY cs.pay_period ORDER BY cs.pay_period DESC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/monthly_commission_statement.inc.php';
  exit;
}
?>