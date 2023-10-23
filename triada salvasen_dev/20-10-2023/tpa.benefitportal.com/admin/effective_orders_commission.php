<?php
include_once __DIR__ . '/layout/start.inc.php';

$incr = "";
$sch_params = array();

$id = checkIsset($_GET["id"]);
$is_ajaxed = isset($_GET['is_ajaxed'])?$_GET['is_ajaxed']:'';
$agent_id = isset($_GET['agent_id'])?$_GET['agent_id']:'';
$member_id = isset($_GET['member_id'])?$_GET['member_id']:'';
$order_id = isset($_GET['order_id'])?$_GET['order_id']:'';
$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";

$has_querystring = false;

$queryJoin = "";
$resRegenComm = array();
if (!empty($id)) {
	$resRegenComm = $pdo->selectOne("SELECT id,order_ids,commission_ids FROM regenerated_commission WHERE md5(id)=:id",array(":id" => $id));

	if(!empty($resRegenComm["order_ids"])){
		$incr .= " AND o.id IN (".$resRegenComm['order_ids'].")";
	}

	if(!empty($resRegenComm["order_ids"])){
		$incr .= " AND o.id IN (".$resRegenComm['order_ids'].")";
	}

	if(!empty($resRegenComm["commission_ids"])){
		$queryJoin = "  FROM orders as o
						LEFT JOIN commission cs ON (o.id = cs.order_id AND cs.id IN (".$resRegenComm['commission_ids']."))
					 	LEFT JOIN order_details od ON (od.order_id = o.id AND od.product_id=cs.product_id AND od.is_deleted='N') 
             			LEFT JOIN prd_main p ON (p.id=od.product_id)
				    	LEFT JOIN customer c ON(c.id=o.customer_id)
	          			LEFT JOIN customer pa ON(pa.id=cs.customer_id AND pa.type !='Customer')";
	}else{
		$queryJoin = "FROM regenerated_commission rc
					  JOIN regenerated_order_commissions ro ON(rc.id=ro.request_id)
					  JOIN commission cs ON(ro.commission_id=cs.id AND cs.is_deleted='N')
					  JOIN orders o ON (o.id = ro.order_id)
             		  JOIN prd_main p ON (p.id=cs.product_id)
				      JOIN customer c ON(c.id=o.customer_id)
	          		  JOIN customer pa ON(pa.id=cs.customer_id AND pa.type !='Customer')";
	    if(!empty($resRegenComm["id"])){
			$incr .= " AND rc.id IN (".$resRegenComm['id'].")";
		}
	}
}

if (!empty($agent_id)) {
    $agent_id = explode(',', trim($agent_id));
    $agent_id = array_map('trim', $agent_id);
    $agent_id = "'" . implode("','", makeSafe($agent_id)) . "'";
    $incr .= " AND pa.rep_id IN ($agent_id)";
}

if (!empty($member_id)) {
    $member_id = explode(',', trim($member_id));
    $member_id = array_map('trim', $member_id);
    $member_id = "'" . implode("','", makeSafe($member_id)) . "'";
    $incr .= " AND c.rep_id IN ($member_id)";
}

if (!empty($order_id)) {
    $order_id = explode(',', trim($order_id));
    $order_id = array_map('trim', $order_id);
    $order_id = "'" . implode("','", makeSafe($order_id)) . "'";
    $incr .= " AND o.display_id IN ($order_id)";
}

if($join_range != ""){
	if($join_range == "Range" && $fromdate!='' && $todate!=''){
	  $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
	  $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
	  $incr.=" AND DATE(o.created_at) >= :fromdate AND DATE(o.created_at) <= :todate";
	}else if($join_range == "Exactly" && $added_date!=''){
	  $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $incr.=" AND DATE(o.created_at) = :added_date";
	}else if($join_range == "Before" && $added_date!=''){
	  $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $incr.=" AND DATE(o.created_at) < :added_date";
	}else if($join_range == "After" && $added_date!=''){
	  $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $incr.=" AND DATE(o.created_at) > :added_date";
	}
}

if (count($sch_params) > 1) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$per_page = $_GET['pages'];
	$has_querystring = true;
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$options = array(
	'results_per_page' => $per_page,
	'url' => 'effective_orders_commission.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);
$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		 $sel_sql = "SELECT 
		 			o.display_id as orderDispId,
		 			DATE(o.created_at) as orderDate,
		 			
		 			c.rep_id as memberDispId,
		 			CONCAT(c.fname,' ',c.lname) as memberName,
		 			

		 			pa.rep_id as payeeAgentId,
					CONCAT(pa.fname,' ',pa.lname) as payeeAgentName,

					p.name as productName,
					cs.commissionable_unit_price as commUnitPrice,
					IF(cs.percentage != '','percentage','amount') AS commOverideType,
					IF(cs.percentage != '',cs.percentage,cs.original_amount) AS commOveride,
					IF(cs.sub_type='Reverse','Reversal','Payment') as paymentType,

	 			   SUM(cs.amount) as totalComm,
                   SUM(if(cs.sub_type='New' OR cs.sub_type='Renewals' OR (cs.sub_type='Reverse' AND cs.is_advance='N' AND cs.is_pmpm_comm='N' AND cs.initial_period_reverse='Y'),cs.amount,0)) as earnedComm,
                   SUM(if(cs.sub_type='Advance' OR (cs.sub_type='Reverse' AND cs.is_advance='Y' AND cs.initial_period_reverse='Y'),cs.amount,0)) as advanceComm,
                   SUM(if(cs.sub_type='PMPM' OR (cs.sub_type='Reverse' AND cs.is_pmpm_comm='Y' AND cs.initial_period_reverse='Y'),cs.amount,0)) as pmpmComm,
                   SUM(if(cs.sub_type='Reverse' AND cs.initial_period_reverse='N',cs.amount,0)) as reverseComm,
					cs.customer_id,cs.id as commId,cs.type as commType,cs.status
				    
					$queryJoin
				    WHERE o.id >0 ".$incr ."
				    GROUP BY paymentType,cs.customer_id,o.id,cs.product_id
             		ORDER BY cs.created_at DESC";
        // pre_print($sel_sql);
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/effective_orders_commission.inc.php';
	exit;
}

$template = 'effective_orders_commission.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>