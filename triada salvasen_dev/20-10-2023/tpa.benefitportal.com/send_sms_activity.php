<?php
include_once dirname(__FILE__) . '/includes/connect.php';

$sch_params = array();
$incr='';

$has_querystring = false;

$popup_is_ajaxed = checkIsset($_GET['popup_is_ajaxed']);  
$log_id = checkIsset($_GET['log_id']); 
$phone = checkIsset($_GET['phone']); 


if(!empty($log_id)){
	$sch_params[":id"] = makeSafe($log_id);
	$incr .= " AND md5(sl.log_id) = :id";	
} 

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
  
$options = array(
	'results_per_page' => $per_page,
	'url' => 'send_sms_activity.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
 
if ($popup_is_ajaxed) {
	try {
		$sel_sql = "SELECT 
						md5(sl.id) as id,
						sl.created_at,
						sl.status,sc.code,sc.error_message
					FROM sms_log_details sl
					LEFT JOIN sms_error_codes sc ON(sc.service=sl.service AND sl.error_code=sc.code)
					WHERE 1   
					". $incr . " 
					ORDER BY sl.id ASC"; 
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}

	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/send_sms_activity.inc.php';
	exit;
}

$template = 'send_sms_activity.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>