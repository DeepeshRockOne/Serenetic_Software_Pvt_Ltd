<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/enrollment_dates.class.php';
$enrollDate = new enrollmentDate();

$customer_id = checkIsset($_GET['id']);

$SortBy = "";
$SortDirection = "ASC";
$currSortDirection = "ASC";
$sch_params = array();
$incr = '';

$is_order_ajaxed = isset($_GET['is_order_ajaxed']) ? $_GET['is_order_ajaxed'] : '';
if ($is_order_ajaxed) {

    $customer_id = $_GET['customer_id'];
    if(!empty($customer_id)){
        $sch_params[':customer_id'] = $customer_id;
        $incr .= ' AND md5(o.customer_id) = :customer_id ';
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
        'url' => 'get_orders_history.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );

    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);

	try {
		$sel_sql = "SELECT md5(o.id) as order_id,o.id as id,o.display_id,o.created_at,o.updated_at,o.is_renewal,o.customer_id,o.transaction_id,o.status,o.grand_total,s.id as agent_id,s.fname,s.lname,s.rep_id,obi.payment_mode as order_payment_mode,obi.last_cc_ach_no as card_no,
        obi.card_type as order_card_type,
        AES_DECRYPT(obi.ach_account_number,'".$CREDIT_CARD_ENC_KEY."') as order_ach_acc_no, c.rep_id as c_rep_id,s.type  from orders o 
        JOIN customer c ON(c.id=o.customer_id and c.is_deleted='N')
        JOIN customer s ON(s.id=c.sponsor_id and s.is_deleted='N')
        JOIN (SELECT payment_mode,last_cc_ach_no,card_type,ach_account_number,order_id FROM order_billing_info WHERE id IN(SELECT MAX(id) FROM order_billing_info GROUP BY order_id)) as obi ON (obi.order_id = o.id)
        where 1 $incr GROUP BY o.id ORDER BY created_at DESC";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/get_orders_history.inc.php';
	exit;
}

include_once 'tmpl/get_orders_history.inc.php';
?>