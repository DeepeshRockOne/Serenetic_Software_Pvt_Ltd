<?php
include_once __DIR__ . '/includes/connect.php';
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
if ($is_ajaxed) {
	$sch_params = array();
	$has_querystring = true;
	$incr = isset($incr) ? $incr : '';
	if (strtotime($_REQUEST["getfromdate"]) > 0) {
		$sch_params[':fcreated_at'] = date('Y-m-d', strtotime($_REQUEST["getfromdate"]));
		$incr .= " AND DATE(t.created_at) >= :fcreated_at";
	}
	if (strtotime($_REQUEST["gettodate"]) > 0) {
		$sch_params[':tcreated_at'] = date('Y-m-d', strtotime($_REQUEST["gettodate"]));
		$incr .= " AND DATE(t.created_at) <= :tcreated_at";
	}
	if (isset($_GET['pages']) && $_GET['pages'] > 0) {
		$per_page = $_GET['pages'];
	}
	$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
	$options = array(
		'results_per_page' => 10,//$per_page,
		'url' => 'top_performing_agents_v2.php?' . $query_string,
		'db_handle' => $pdo->dbh,
		'named_params' => $sch_params,
	);

	$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
	$options = array_merge($pageinate_html, $options);
	$fee_products = get_enrollment_with_associate_fee_prd_ids("string");
	$enroll_products = get_enrollment_fee_prd_ids('string');
	$where_incr = " AND 1 ";
	if($enroll_products!='')
	{
		$where_incr.= "AND od.product_id NOT IN($enroll_products)";
	}
	if($fee_products!='')
	{
		$where_incr.= "AND od.product_id NOT IN($fee_products)";   
	}
	try {
		$sql = "SELECT IF(a.business_name = '' OR a.business_name IS NULL,CONCAT(a.fname,' ',a.lname),a.business_name)as agent_name,a.rep_id as agent_display_id,ord.total_premiums,ord.total_policy_holders,ord.total_policies,a.id as agent_id,ord.NewBusinessMember
		FROM customer a
		LEFT JOIN
		(SELECT tran.sponsor_id as s_id,sum(t.credit) as total_premiums,count(DISTINCT tran.cust_id) as total_policy_holders,SUM(tran.total_product) AS total_policies, COUNT(distinct (CASE WHEN t.transaction_type = 'New Order' THEN tran.cust_id END)) as NewBusinessMember
				FROM transactions as t
				LEFT JOIN (
					SELECT o.id, c.sponsor_id, c.id as cust_id, count(od.product_id) as total_product
					FROM orders as o
					JOIN order_details od ON (od.order_id = o.id AND od.is_deleted='N')
		            JOIN prd_main p ON(p.id=od.product_id)
		            JOIN customer c ON (c.id=o.customer_id)	
		            WHERE p.is_deleted='N' AND o.payment_type IN('CC','ACH') $where_incr GROUP BY o.id
				) as tran ON (tran.id = t.order_id)
            WHERE t.transaction_type IN ('New Order','Renewal Order') $incr GROUP BY tran.sponsor_id
		) as ord ON ord.s_id = a.id
		WHERE a.is_deleted='N' GROUP BY a.id having total_premiums > 0 ORDER BY  total_premiums DESC";	
		$paginate = new pagination($page, $sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
			if (!empty($fetch_rows[0]['agent_name'])) {
				$gran_total = 0;
                foreach ($fetch_rows as $key => $rows) {
                	$gran_total = $gran_total + $rows['total_premiums'] ;
                	if($rows['total_premiums'] > 0) {
                		$fetch_rows[$key]['avg_premium_per_holder'] = $rows['total_premiums']/$rows['total_policy_holders'];
                	} else {
                		$fetch_rows[$key]['avg_premium_per_holder'] = 0;
                	}

                	if($rows['total_policies'] > 0) {
                		$fetch_rows[$key]['avg_policies_per_holder'] = number_format((float)$rows['total_policies']/$rows['total_policy_holders'], 2, '.', '');
                	} else {
                		$fetch_rows[$key]['avg_policies_per_holder'] = 0.00;
                	}
                }
            }
		}
		include_once 'tmpl/top_performing_agents_v2.inc.php';
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
} else {
	include_once 'tmpl/top_performing_agents_v2.inc.php';
}
?>
