<?php
include_once __DIR__ . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
$commObj = new Commission();

$tz = new UserTimeZone('m/d/Y @ g:i A T', $_SESSION['agents']['timezone']);
$current_time = $tz->getDate('', 'Y-m-d H:i:s');
$last_comm_script_run_at = date("n/j/Y @ g:i a ",floor(strtotime($current_time) / 15 / 60) * 15 * 60);
$last_comm_script_run_at .= $tz->getDate('', 'T');

$incr = "";
$sel_params = array();

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$tmp_today = date('Y-m-d');
$pay_period = $commObj->getMonthlyPayPeriod($tmp_today);

$has_querystring = false;

$sel_params[":agentId"] = $_SESSION["agents"]["id"];

if ($pay_period != "") {
	$incr .= ' AND DATE(cs.pay_period) =:pay_period';
	$sel_params[":pay_period"] = date('Y-m-d',strtotime($pay_period));
}
if (count($sel_params) > 1) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$per_page = $_GET['pages'];
	$has_querystring = true;
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$options = array(
	'results_per_page' => $per_page,
	'url' => 'monthly_commission.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sel_params,
);
$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
if ($is_ajaxed) {
	try {
		$sel_sql = "SELECT 
				cs.created_at as orderDate,
				CONCAT(m.fname,' ',m.lname) as memberName,
				CONCAT(e.fname,' ',e.lname) as enrollerName,
				o.display_id as odrDispId,
				od.product_name as prdName,
				cs.commissionable_unit_price as unitPrice,
				
				IFNULL(SUM(DISTINCT CASE WHEN cs.is_pmpm_comm='N' THEN cs.percentage END),0) AS overidePercentage,
				IFNULL(SUM(DISTINCT CASE WHEN cs.is_pmpm_comm='N' THEN cs.original_amount END),0) AS overideAmount,
               
               SUM(cs.amount) as commTotal,
               IFNULL(SUM(IF(cs.sub_type='New' OR cs.sub_type='Renewals',cs.amount,0)),0) as earnedTotal,
               IFNULL(SUM(IF(cs.sub_type='Advance',cs.amount,0)),0) as advanceTotal,
               IFNULL(SUM(IF(cs.sub_type='PMPM',cs.amount,0)),0) as pmpmTotal,
               IFNULL(SUM(IF(cs.sub_type='Reverse',cs.amount,0)),0) as reverseTotal,
               IFNULL(SUM(IF(cs.sub_type='Fee',cs.amount,0)),0) as feeTotal
      
             FROM commission cs
             JOIN orders as o ON (o.id = cs.order_id)
             JOIN order_details od ON (od.id = cs.order_detail_id AND od.is_deleted='N') 
             LEFT JOIN customer m ON(m.id=cs.payer_id)
             LEFT JOIN customer e ON(e.id=m.sponsor_id)
             WHERE cs.commission_duration='monthly' AND cs.customer_id = :agentId 
             AND cs.status IN ('Approved','Pending') AND cs.amount != 0 
             AND cs.is_deleted = 'N' " . $incr . "
             GROUP BY cs.order_id,cs.order_detail_id
             ORDER BY cs.created_at DESC";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/monthly_commission.inc.php';
	exit;
}
$header_commission_res = get_pay_period_commission_totals($_SESSION['agents']['id']);
$template = 'monthly_commission.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>