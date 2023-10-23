<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(2);
$customer_id = checkIsset($_GET['id']);

$SortBy = "";
$SortDirection = "ASC";
$currSortDirection = "ASC";
$sch_params = array();
$incr = '';

$is_transaction_ajaxed = isset($_GET['is_transaction_ajaxed']) ? $_GET['is_transaction_ajaxed'] : '';
if ($is_transaction_ajaxed) {

    $customer_id = $_GET['customer_id'];
    if(!empty($customer_id)){
        $sch_params[':customer_id'] = $customer_id;
        $incr .= ' AND md5(t.customer_id) = :customer_id';
    }
    if (count($sch_params) > 0) {
        $has_querystring = true;
    }
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
        $has_querystring = true;
        $per_page = $_GET['pages'];
    }
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    $query_string = $has_querystring ? (!empty($page) ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

    $options = array(
        'results_per_page' => $per_page,
        'url' => 'get_transactions_history.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );

    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);

    try {
        $sel_sql = "SELECT md5(t.id) as t_id,md5(o.id) as o_id,o.display_id,t.created_at,o.is_renewal,o.customer_id,t.transaction_id,t.transaction_status,t.credit,t.debit,s.id as agent_id,s.fname,s.lname,s.rep_id,
        c.rep_id as c_rep_id ,
        p.name as merchant_name,IF(t.order_type='Credit', t.credit,t.debit) as transTotal,s.type, t.billing_info
        FROM transactions t
        JOIN orders o ON(o.id=t.order_id)
        LEFT JOIN payment_master p ON(p.id=t.payment_master_id)
        JOIN customer c ON(c.id=o.customer_id and c.is_deleted='N')
        JOIN customer s ON(s.id=c.sponsor_id and s.is_deleted='N')
        where 1 $incr GROUP BY t.id ORDER BY created_at desc";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/get_transactions_history.inc.php';
	exit;
}

include_once 'tmpl/get_transactions_history.inc.php';
?>