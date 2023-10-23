<?php
include_once __DIR__ . '/includes/connect.php';
$is_ajaxed = $_GET['is_ajaxed'];
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
		'url' => 'top_performing_products_v2.php?' . $query_string,
		'db_handle' => $pdo->dbh,
		'named_params' => $sch_params,
	);

	$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
	$options = array_merge($pageinate_html, $options);
	$enr_fee_prd_ids = get_enrollment_fee_prd_ids('string');
	$where_incr = " AND 1 ";

    if($enr_fee_prd_ids!='')
    {
        $where_incr.= "AND od.product_id NOT IN($enr_fee_prd_ids)";
    }
	try {
		$sql = "SELECT 
				p.name AS product_name,
				p.product_code,
				ord.total_premiums,
				ord.renewal_premiums,
				ord.new_business_premiums,
				ord.total_policies,
				ord.renewal_policies,
				ord.new_business_policies,
				p.id AS product_id  
				FROM prd_main p
				LEFT JOIN (
 					SELECT 
 						SUM(od.unit_price*od.qty) AS total_premiums,
			            SUM(IF(t.transaction_type = 'Renewal Order',(od.unit_price*od.qty),0)) AS renewal_premiums,
			            SUM(IF(t.transaction_type = 'New Order',(od.unit_price*od.qty),0)) AS new_business_premiums,
			            COUNT(o.id) AS total_policies,
			            COUNT((CASE WHEN t.transaction_type='Renewal Order' THEN o.id END)) AS renewal_policies,
			            COUNT((CASE WHEN t.transaction_type='New Order' THEN o.id END)) AS new_business_policies,
 						IF(p.parent_product_id = 0,p.id,p.parent_product_id) AS parent_product_id, od.product_id
 		            FROM prd_main p 
 					JOIN order_details od ON(p.id = od.product_id AND od.is_deleted='N')
 					JOIN orders as o ON (od.order_id = o.id)
					JOIN transactions as t ON (t.order_id = o.id)
 		            WHERE t.transaction_type IN ('New Order','Renewal Order') $incr $where_incr GROUP BY od.product_id 
 				) as ord ON (ord.product_id = p.id)
				WHERE p.is_deleted='N' AND p.category_id NOT IN(3,33)
				GROUP BY p.id
				HAVING total_premiums > 0
				ORDER BY total_premiums DESC";
		$paginate = new pagination($page, $sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
		include_once 'tmpl/top_performing_products_v2.inc.php';
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
} else {
	include_once 'tmpl/top_performing_products_v2.inc.php';
}
?>