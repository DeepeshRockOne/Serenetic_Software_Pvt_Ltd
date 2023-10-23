<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$incr = '';
$payment_master_id = $_GET['payment_id'];
$payment_master_res = $pdo->selectOne("SELECT name FROM payment_master WHERE md5(id) = :id", array(":id" => $payment_master_id));

$agnts_res = $pdo->select("SELECT c.id as agentId,c.rep_id as agentDispId,CONCAT(c.fname,' ',c.lname) as agentName
	FROM customer c
	JOIN payment_master_assigned_agent pmaa ON(pmaa.agent_id=c.id AND pmaa.is_deleted='N' AND pmaa.status!='Deleted')
	JOIN payment_master p ON(p.id=pmaa.payment_master_id AND p.is_deleted='N')
	WHERE c.type='Agent'  AND 
	c.is_deleted = 'N' AND 
	c.id>0  AND md5(p.id)=:payment_id",array(":payment_id"=>$payment_master_id));
$total_agents = count($agnts_res);

$sch_params = array();
$SortBy = "c.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

if($payment_master_id!=''){
	$incr.=' AND md5(p.id) = :payment_id ';
	$sch_params[":payment_id"] = $payment_master_id;
}

$has_querystring = false;
if (!empty($_GET["sort_by"])) {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (!empty($_GET["sort_direction"])) {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = !empty($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$rep_id = !empty($_GET['agent_ids']) ? $_GET['agent_ids'] : '';

$export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';

	if(!empty($export_val)){

		include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

		if(!empty($payment_master_id)){
			$rincr = " AND md5(p.id) IN('".$payment_master_id."') ";

			if (!empty($rep_id)) {
				$rincr .= " AND c.id in(".implode(',',$rep_id).")";
			}

			$extra_export_arr['processor_incr'] = $rincr;

			$job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Agent Merchant Assignment(s)","agentMerchantExport",'',array(),$extra_export_arr,'agent_merchant_assignment');
			$reportDownloadURL = $AWS_REPORTING_URL['agent_merchant_assignment']."&job_id=".$job_id;
			echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
			exit;
		}
	}

if (!empty($rep_id)) {
	$incr .= " AND c.id in(".implode(',',$rep_id).")";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'view_assigned_agents.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		$sel_sql = "SELECT c.rep_id, c.fname, c.lname
        FROM customer c
		JOIN payment_master_assigned_agent pmaa ON(pmaa.agent_id=c.id AND pmaa.is_deleted='N' AND pmaa.status!='Deleted')
		JOIN payment_master p ON(p.id=pmaa.payment_master_id AND p.is_deleted='N')
        WHERE c.type='Agent'  AND 
        c.is_deleted = 'N' AND 
        c.id>0  " . $incr . " ORDER BY  $SortBy $currSortDirection";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/view_assigned_agents.inc.php';
	exit;
}
$exStylesheets = array('thirdparty/bootstrap-tables/css/bootstrap-table.min.css', 'thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/bootstrap-tables/js/bootstrap-table.min.js', 'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'view_assigned_agents.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>