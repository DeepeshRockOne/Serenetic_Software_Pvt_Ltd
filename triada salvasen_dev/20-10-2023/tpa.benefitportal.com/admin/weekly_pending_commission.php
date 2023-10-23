<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
if(empty($_GET["agent_id"])){
$module_access_type = has_access(60);
}else{
$module_access_type = has_access(90);		
}
$incr = "";
$sch_params = array();
$SortBy = "agentName";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}
$commission_duration = "weekly";
$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$agent_id = checkIsset($_GET["agent_id"]);
$pay_period = checkIsset($_REQUEST["pay_period"]);
$agentId = checkIsset($_GET["agentId"]);
$agentName = checkIsset($_GET["agentName"]);

if(empty($pay_period)){
	redirect("payment_commissions.php",true);
}else{
	$sch_params[':pay_period'] = date("Y-m-d",strtotime($pay_period));
	$incr .= " AND cs.pay_period = :pay_period";

	if(!empty($agent_id)){
	    $sch_params[':agent_id'] = $agent_id;
	    $incr .= " AND  md5(cs.customer_id) = :agent_id";
	}
}

$startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($pay_period)));;
$endPayPeriod=date('m/d/Y', strtotime($pay_period));

if (!empty($agentId)) {
	$sch_params[':agentId'] = "%" . makeSafe($agentId) . "%";
	$incr .= " AND s.rep_id LIKE :agentId";
}

if (!empty($agentName)) {
	$sch_params[':agentName'] = "%" . makeSafe($agentName) . "%";
	$incr .= " AND (s.fname LIKE :agentName or s.lname LIKE :agentName or CONCAT(s.fname,s.lname) LIKE :agentName)";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'weekly_pending_commission.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		 $sel_sql = "SELECT cs.customer_id,cs.id as commId,cs.type as commType,cs.status,
		 			   s.rep_id as agentRepId,CONCAT(s.fname,' ',s.lname) as age_name,
    	 			   IF(cst.company_name != '',cst.company_name,CONCAT(s.fname,' ',s.lname)) as agentName,
		 			   SUM(cs.amount) as totalComm,
                       SUM(if(cs.sub_type='New' OR cs.sub_type='Renewals',cs.amount,0)) as earnedComm,
                       SUM(if(cs.sub_type='Advance',cs.amount,0)) as advanceComm,
                       SUM(if(cs.sub_type='PMPM',cs.amount,0)) as pmpmComm,
                       SUM(if(cs.sub_type='Reverse',cs.amount,0)) as reverseComm,
                       SUM(if(cs.sub_type='Fee',cs.amount,0)) as feeComm,
                       SUM(if(cs.type='Adjustment',cs.amount,0)) as adjustComm
				    FROM commission cs
				    JOIN customer as s on(s.id = cs.customer_id)
				    JOIN customer_settings cst ON (s.id = cst.customer_id)
				    WHERE cs.commission_duration='weekly' AND s.type !='Customer' AND cs.is_deleted='N' AND cs.status IN('Pending') $incr 
				    GROUP BY cs.customer_id ORDER BY $SortBy $currSortDirection";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/weekly_pending_commission.inc.php';
	exit;
}

$template = 'weekly_pending_commission.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php'; 
?>