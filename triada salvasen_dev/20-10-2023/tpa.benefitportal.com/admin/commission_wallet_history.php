<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$incr = "";
$sch_params = array();
$SortBy = "created_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";


$agentId = checkIsset($_GET['agentId']);
$is_ajaxed = isset($_GET['is_ajaxed'])?$_GET['is_ajaxed']:'';
$has_querystring = false;

if ($agentId != "") {
  $sch_params[':agentId'] = makeSafe($agentId);
  $incr.=" AND md5(cs.agent_id) = :agentId";
}

$agentRes = $pdo->selectOne("SELECT CONCAT(fname,' ',lname) as agentName,rep_id as agentDispId,id as agentId FROM customer WHERE md5(id)=:id", array(":id"=>$agentId));




$has_querystring = false;

if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'commission_wallet_history.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {

    //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>'  read commissions wallet for',
           'ac_red_2'=>array(
              'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agentRes['agentId']),
              'title'=> $agentRes['agentDispId'],
            ),
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$agentRes['agentId'], 'Agent',"Read Commissions", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************

  try {
    $sel_sql = "SELECT cs.id,cs.created_at as transDate,cs.pay_period,cs.message,
    					cs.commission_duration,cs.pay_period,
    					if(cs.type='Credit',cs.amount,'-') as creditAmt,
    					if(cs.type='Debit',cs.amount,'-') as debitAmt,
    					cs.current_balance, cs.type,cs.deposit_detail,cs.wallet_id, ccb.paid_to_agent, ccb.pmpm_to_agent
               FROM commission_wallet_history cs
               LEFT JOIN commission_credit_balance ccb ON ccb.wallet_history_id=cs.id
               WHERE cs.id > 0 $incr ORDER BY cs.id DESC";       
         
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
    
  } catch (paginationException $e) {
      echo $e;
      exit();
  }
  include_once 'tmpl/commission_wallet_history.inc.php';
  exit;
}

$exStylesheets = array('thirdparty/bootstrap-datepicker-master/css/datepicker.css');
$exJs = array('thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js');

$template = "commission_wallet_history.inc.php";
include_once 'layout/iframe.layout.php';
?>