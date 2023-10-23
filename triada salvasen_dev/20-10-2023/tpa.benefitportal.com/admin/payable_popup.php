<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$order_id = $_GET['order_id'];
if($order_id){
	$schParams = array();
	$SortBy = "cs.id";
	$SortDirection = "DESC";
	$currSortDirection = "ASC";
	$sch_params = array();
	$has_querystring = false;

	$sch_params[":order_id"] = $order_id;

	if (count($sch_params) > 0) {
		$has_querystring = true;
	}
	if (isset($_GET['pages']) && $_GET['pages'] > 0) {
		$has_querystring = true;
		$per_page = $_GET['pages'];
	}

	$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

	$options = array(
		'results_per_page' => $per_page,
		'url' => 'payable_popup.php?' . $query_string,
		'db_handle' => $pdo->dbh,
		'named_params' => $sch_params,
	);

	$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
	$options = array_merge($pageinate_html, $options);
	try {
		$sel_query = "SELECT c.rep_id as customer_id,s.rep_id,cs.pay_period,cs.commission_duration,p.name,p.product_code,
					   SUM(cs.amount) as totalComm,
                       SUM(if(cs.sub_type='New' OR cs.sub_type='Renewals' OR (cs.sub_type='Reverse' AND cs.is_advance='N' AND cs.is_pmpm_comm='N' AND cs.initial_period_reverse='Y' AND cs.is_fee_comm='N'),cs.amount,0)) as earnedComm,
                       SUM(if(cs.sub_type='Advance' OR (cs.sub_type='Reverse' AND cs.is_advance='Y' AND cs.initial_period_reverse='Y'),cs.amount,0)) as advanceComm,
                       SUM(if(cs.sub_type='PMPM' OR (cs.sub_type='Reverse' AND cs.is_pmpm_comm='Y' AND cs.initial_period_reverse='Y'),cs.amount,0)) as pmpmComm,
                       SUM(if(cs.sub_type='Reverse' AND cs.initial_period_reverse='N',cs.amount,0)) as reverseComm,
                       SUM(if(cs.sub_type='Fee' OR (cs.sub_type='Reverse' AND cs.is_fee_comm='Y' AND cs.initial_period_reverse='Y'),cs.amount,0)) as feeComm,
                       SUM(if(cs.type='Adjustment',cs.amount,0)) as adjustComm
								FROM commission cs
								JOIN customer c on(c.id = cs.payer_id)
								JOIN customer s on(s.id = cs.customer_id)
								JOIN prd_main p on(cs.product_id = p.id)
								WHERE cs.order_id = :order_id and cs.is_deleted = 'N' GROUP BY cs.id
								 ";

	$paginate = new pagination($page, $sel_query, $options);

	if ($paginate->success == true) {
        $commissions = $paginate->resultset->fetchAll();
        // pre_print($commissions);
        $totalCommissions = count($commissions);
    }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}


$template = 'payable_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>