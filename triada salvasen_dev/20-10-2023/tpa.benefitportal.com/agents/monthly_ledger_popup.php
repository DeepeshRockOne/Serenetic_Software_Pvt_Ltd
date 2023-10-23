<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
  $commObj = new Commission();

  $incr = '';
  $sch_params = array();
  $agentId = $_SESSION['agents']['id'];

  $weeklyPayPeriod = $commObj->getWeeklyPayPeriod(date('Y-m-d'));
  $monthlyPayPeriod = $commObj->getMonthlyPayPeriod(date('Y-m-d'));

if(isset($_GET['action']) && $_GET['action'] == 'agent_debit_ledger') {
    include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
    include_once dirname(__DIR__) . '/includes/export_report.class.php';
    $config_data = array(
      'user_id' => $_SESSION['agents']['id'],
      'user_type' => 'Agent',
      'user_rep_id' => $_SESSION['agents']['rep_id'],
      'user_profile_page' => $AGENT_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
      'timezone' => $_SESSION['agents']['timezone'],
      'file_type' => 'EXCEL',
      'report_location' => 'monthly_ledger_popup',
      'report_key' => 'agent_debit_ledger',
    );
    $_POST['join_range'] = 'before';
    $_POST['added_date'] = date('m/d/Y',strtotime('+1 day'));
    $exportreport = new ExportReport(0,$config_data);
    $response = $exportreport->run();
    echo json_encode($response);
    exit();
}

  if(!empty($agentId)){
      $incr .= " AND h.agent_id = :agent_id";
      $sch_params[':agent_id'] = $agentId;
  }

  $has_querystring = false;
  if(isset($_GET["sort_by"]) && $_GET["sort_by"] != ""){
      $has_querystring = true;
      $SortBy = $_GET["sort_by"];
  }

if(isset($_GET["sort_direction"]) && $_GET["sort_direction"] != ""){
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

if(count($sch_params) > 0) {
  $has_querystring = true;
}

$per_page = 500;

if(isset($_GET['pages']) && $_GET['pages'] > 0){
  $has_querystring = true;
  $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
  'results_per_page' => $per_page,
  'url' => 'monthly_ledger_popup.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed){
  try{
    $sel_sql = "SELECT h.id,h.pay_period,h.agent_id,
                SUM(IF(h.transaction_type IN('Advance_Generated','Advance_Reversed','Deny_Commission','Reversed_Deny_Commission','Credit_Reversed_Commission_Payment','Debit_Wallet_Transfer'),h.amount,0)) AS debitBalance,
                SUM(IF(h.transaction_type IN('Commission_Payment','Reversed_Commission_Payment','Credit_Wallet_Transfer'),h.amount,0)) AS appliedCredit,
                h.commission_duration
                FROM commission_debit_balance_history h
                WHERE h.is_deleted='N'"
                 . $incr . "
                GROUP BY h.pay_period,h.commission_duration ORDER BY h.pay_period ASC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  }catch(paginationException $e){
    echo $e;
    exit();
  }

  include_once 'tmpl/monthly_ledger_popup.inc.php';
  exit;
}
    
$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js');
$template = "monthly_ledger_popup.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>