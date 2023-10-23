<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
agent_has_access(13);
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'My Production';
$breadcrumbes[2]['title'] = 'Commissions';
$breadcrumbes[2]['link'] = 'commissions.php';

$commObj = new Commission();
$body_class="com_page";

if($_SESSION['agents']['agent_coded_level']=='LOA'){
  setNotifyError("You are not authorised to access this page");
  redirect('dashboard.php');
}
$agent_id = $_SESSION['agents']['id'];
$agents = array($agent_id);

$payPeriod='';
if(isset($_GET['pay_period']) && isset($_GET['noti_id']) && $_GET['pay_period']!='' && $_GET['noti_id'] !=''){
  $payPeriod=$_GET['pay_period'];
}

/*--------------------- Paid Commission Code START ------------*/
  $sqlPaidCommission="SELECT 
  					SUM(cs.amount) as totalPaidComm,
  					SUM(if(cs.commission_duration='weekly',cs.amount,0)) as weeklyPaidComm,
  					SUM(if(cs.commission_duration='monthly',cs.amount,0)) as monthlyPaidComm
                    FROM commission cs 
                    WHERE cs.customer_id=:customer_id AND cs.status IN ('Approved') 
                    AND cs.amount != 0  AND cs.is_deleted = 'N'";
  $resPaidCommission=$pdo->selectOne($sqlPaidCommission,array(":customer_id"=>$_SESSION['agents']['id']));

  $totalPaidCommission = !empty($resPaidCommission['totalPaidComm']) ? $resPaidCommission['totalPaidComm'] : 0;
  $totalWeeklyCommission = !empty($resPaidCommission['weeklyPaidComm']) ? $resPaidCommission['weeklyPaidComm'] : 0;
  $totalMonthlyCommission = !empty($resPaidCommission['monthlyPaidComm']) ? $resPaidCommission['monthlyPaidComm'] : 0;
/*--------------------- Paid Commission Code ENDS -------------*/

$agentRow = $pdo->selectOne("SELECT id, fname,lname,CONCAT(fname,' ',lname) as name,rep_id from customer where id = :id",array(":id"=>$agent_id));

$today = date('Y-m-d');
$weeklyPayPeriod = $commObj->getWeeklyPayPeriod($today);
$monthlyPayPeriod = $commObj->getMonthlyPayPeriod($today);

/*--------------------- current weekly commission pay period summary code START ------------*/
  $weeklyCommSql = "SELECT cs.pay_period,
                      SUM(cs.amount) as commTotal,
                      SUM(if(cs.sub_type='New' OR cs.sub_type='Renewals',cs.amount,0)) as earnedComm,
                      SUM(IF(cs.sub_type='Reverse' AND cs.is_pmpm_comm='N' AND cs.is_advance='N' AND cs.initial_period_reverse='Y' AND cs.is_fee_comm='N',cs.amount,0)) AS earnedCommRev,

                       SUM(if(cs.sub_type='Advance',cs.amount,0)) as advanceComm,
                      SUM(IF(cs.sub_type='Reverse' AND cs.is_advance='Y' AND cs.initial_period_reverse='Y',cs.amount,0)) AS advanceCommRev,

                       SUM(if(cs.sub_type='PMPM',cs.amount,0)) as pmpmComm,
                       SUM(IF(cs.sub_type='Reverse' AND cs.is_pmpm_comm='Y' AND cs.initial_period_reverse='Y',cs.amount,0)) AS pmpmCommRev,

                       SUM(if(cs.sub_type='Fee' OR (cs.sub_type='Reverse' AND cs.is_fee_comm='Y'),cs.amount,0)) as feeComm,
                       SUM(if(cs.type='Adjustment',cs.amount,0)) as adjustComm,
                       SUM(if(cs.sub_type='Reverse' AND cs.initial_period_reverse='N',cs.amount,0)) as pastReverseComm

             FROM commission cs
             WHERE cs.is_deleted='N' AND cs.commission_duration='weekly' AND cs.customer_id =:id AND cs.pay_period=:pay_period  GROUP BY cs.pay_period ORDER BY cs.pay_period DESC";
  $weeklyCommParams = array(":pay_period" => date("Y-m-d",strtotime($weeklyPayPeriod)),":id"=>$agentRow['id']);
  $resWeeklyCommission = $pdo->selectOne($weeklyCommSql,$weeklyCommParams);

  $totalCommWeekly = !empty($resWeeklyCommission['commTotal']) ? $resWeeklyCommission['commTotal'] : 0 ;
  
  $earnedCommWeekly = !empty($resWeeklyCommission['earnedComm']) ? $resWeeklyCommission['earnedComm'] : 0 ;
  $earnedCommRevWeekly = !empty($resWeeklyCommission['earnedCommRev']) ? $resWeeklyCommission['earnedCommRev']  : 0;
  $earnedNetCommWeekly = $earnedCommWeekly + $earnedCommRevWeekly;

  $advanceCommWeekly = !empty($resWeeklyCommission['advanceComm']) ? $resWeeklyCommission['advanceComm'] : 0;
  $advanceCommRevWeekly = !empty($resWeeklyCommission['advanceCommRev']) ? $resWeeklyCommission['advanceCommRev'] : 0 ;
  $advanceNetCommWeekly = $advanceCommWeekly + $advanceCommRevWeekly;

  $pmpmCommWeekly = !empty($resWeeklyCommission['pmpmComm']) ? $resWeeklyCommission['pmpmComm'] : 0;
  $pmpmCommRevWeekly = !empty($resWeeklyCommission['pmpmCommRev']) ? $resWeeklyCommission['pmpmCommRev'] : 0;
  $pmpmNetCommWeekly = $pmpmCommWeekly + $pmpmCommRevWeekly;

  $totalEarnedCommWeekly = $earnedCommWeekly + $advanceCommWeekly + $pmpmCommWeekly;
  $totalRevCommWeekly = $earnedCommRevWeekly + $advanceCommRevWeekly + $pmpmCommRevWeekly;
  $totalNetCommWeekly =  $earnedNetCommWeekly +  $advanceNetCommWeekly + $pmpmNetCommWeekly;


  $pastCommRevWeekly = !empty($resWeeklyCommission['pastReverseComm']) ? $resWeeklyCommission['pastReverseComm'] : 0;
  $feeCommWeekly =  !empty($resWeeklyCommission['feeComm']) ? $resWeeklyCommission['feeComm'] : 0;
  $adjustmentCommWeekly =  !empty($resWeeklyCommission['adjustComm']) ? $resWeeklyCommission['adjustComm'] : 0;
  $otherTotalCommWeekly = $pastCommRevWeekly + $feeCommWeekly + $adjustmentCommWeekly;
/*--------------------- current weekly commission pay period summary code START ------------*/

/*--------------------- current monthly commission pay period summary code START ------------*/
  $monthlyCommSql = "SELECT cs.pay_period,
                      SUM(cs.amount) as commTotal,
                      SUM(if(cs.sub_type='New' OR cs.sub_type='Renewals',cs.amount,0)) as earnedComm,
                      SUM(IF(cs.sub_type='Reverse' AND cs.is_pmpm_comm='N' AND cs.is_advance='N' AND cs.initial_period_reverse='Y' AND cs.is_fee_comm='N',cs.amount,0)) AS earnedCommRev,

                       SUM(if(cs.sub_type='Advance',cs.amount,0)) as advanceComm,
                      SUM(IF(cs.sub_type='Reverse' AND cs.is_advance='Y' AND cs.initial_period_reverse='Y',cs.amount,0)) AS advanceCommRev,

                       SUM(if(cs.sub_type='PMPM',cs.amount,0)) as pmpmComm,
                       SUM(IF(cs.sub_type='Reverse' AND cs.is_pmpm_comm='Y' AND cs.initial_period_reverse='Y',cs.amount,0)) AS pmpmCommRev,

                       SUM(if(cs.sub_type='Fee' OR (cs.sub_type='Reverse' AND cs.is_fee_comm='Y'),cs.amount,0)) as feeComm,
                       SUM(if(cs.type='Adjustment',cs.amount,0)) as adjustComm,
                       SUM(if(cs.sub_type='Reverse' AND cs.initial_period_reverse='N',cs.amount,0)) as pastReverseComm

             FROM commission cs
             WHERE cs.is_deleted='N' AND commission_duration='monthly' AND cs.customer_id =:id AND cs.pay_period=:pay_period  GROUP BY cs.pay_period ORDER BY cs.pay_period DESC";
  $monthlyCommParams = array(":pay_period" => date("Y-m-d",strtotime($monthlyPayPeriod)),":id"=>$agentRow['id']);
  $resMonthlyCommission = $pdo->selectOne($monthlyCommSql,$monthlyCommParams);

  $totalCommMonthly = !empty($resMonthlyCommission['commTotal']) ? $resMonthlyCommission['commTotal'] : 0 ;

	$earnedCommMonthly = !empty($resMonthlyCommission['earnedComm']) ? $resMonthlyCommission['earnedComm'] : 0;
	$earnedCommRevMonthly = !empty($resMonthlyCommission['earnedCommRev']) ? $resMonthlyCommission['earnedCommRev'] : 0;
	$earnedNetCommMonthly = $earnedCommMonthly + $earnedCommRevMonthly;

	$advanceCommMonthly = !empty($resMonthlyCommission['advanceComm']) ? $resMonthlyCommission['advanceComm'] : 0;
	$advanceCommRevMonthly = !empty($resMonthlyCommission['advanceCommRev']) ? $resMonthlyCommission['advanceCommRev'] : 0;
	$advanceNetCommMonthly = $advanceCommMonthly + $advanceCommRevMonthly;

	$pmpmCommMonthly = !empty($resMonthlyCommission['pmpmComm']) ? $resMonthlyCommission['pmpmComm'] : 0;
	$pmpmCommRevMonthly = !empty($resMonthlyCommission['pmpmCommRev']) ? $resMonthlyCommission['pmpmCommRev'] : 0;
	$pmpmNetCommMonthly = $pmpmCommMonthly + $pmpmCommRevMonthly;

	$totalEarnedCommMonthly = $earnedCommMonthly + $advanceCommMonthly + $pmpmCommMonthly;
	$totalRevCommMonthly = $earnedCommRevMonthly + $advanceCommRevMonthly + $pmpmCommRevMonthly;
	$totalNetCommMonthly =  $earnedNetCommMonthly + $advanceNetCommMonthly + $pmpmNetCommMonthly;

	$pastCommRevMonthly = !empty($resMonthlyCommission['pastReverseComm']) ? $resMonthlyCommission['pastReverseComm'] : 0;
	$feeCommMonthly =  !empty($resMonthlyCommission['feeComm']) ? $resMonthlyCommission['feeComm'] : 0;
	$adjustmentCommMonthly =  !empty($resMonthlyCommission['adjustComm']) ? $resMonthlyCommission['adjustComm'] : 0;
	$otherTotalCommMonthly = $pastCommRevMonthly + $feeCommMonthly + $adjustmentCommMonthly;
/*--------------------- current monthly commission pay period summary code ENDS ------------*/

// get debit balance 
$debitBalance = $commObj->getAgentDebitBalance($agent_id);



if (isset($_REQUEST["noti_id"])) {
  $sqlCheckNotification="SELECT * FROM `users_notifications` WHERE id=:id";
  $resCheckNotification=$pdo->selectOne($sqlCheckNotification,array(":id"=>$_REQUEST['noti_id']));

  if($resCheckNotification){
    openNotification($_REQUEST["noti_id"], $_SESSION["agents"]["id"]);
  }else{
    setNotifyError("Oops!! No notification found");
    redirect("dashboard.php");
  }
}

$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' => ' read Commissions'
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'Agent', 'Agent Read Commissions.', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

$exJs = array('thirdparty/moment/moment.js');

$template = 'commissions.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>