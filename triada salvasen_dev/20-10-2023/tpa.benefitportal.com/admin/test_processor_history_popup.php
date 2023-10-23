<?php
include_once __DIR__ . '/layout/start.inc.php';
$payment_master_id = checkIsset($_GET['pay_id']);
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : 0;
$sch_params = array();
$incr = '';
$has_querystring = false;
if($is_ajaxed){
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);

if(!empty($payment_master_id)){
    $incr.=' AND md5(payment_master_id)=:processor_id ';
    $sch_params[":processor_id"] = $payment_master_id;
}

if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => 10,
    'url' => 'test_processor_history_popup.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
    try {

        $sel_sql = "SELECT pm.created_at,pm.payment_mode,pm.payment_status as status,pm.transaction_id,pm.decline_text,a.display_id,CONCAT(a.fname,' ',a.lname) as admin_name,pm.amount
        FROM payment_master_test_connection pm 
        LEFT JOIN admin a ON(a.id=pm.admin_id)
        WHERE 1 $incr ORDER BY pm.created_at DESC";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }

    include_once 'tmpl/test_processor_history_popup.inc.php';
    exit;
}
$pro_name = $_GET['pro_name'];

$template = 'test_processor_history_popup.inc.php';
include_once 'layout/iframe.layout.php';
include_once 'tmpl/' . $template;
?>