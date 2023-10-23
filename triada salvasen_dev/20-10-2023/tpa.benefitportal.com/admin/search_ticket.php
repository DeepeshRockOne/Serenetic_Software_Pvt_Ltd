<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(25);
$agent_menu = has_menu_access(5);
$affiliates_menu = has_menu_access(4);
$member_menu = has_menu_access(8);
$group_menu = has_menu_access(6);

$sch_params = array();
$has_querystring = false;

$SortBy = "tc.last_replied";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$has_querystring = false;
if ($_GET["sort"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort"];
}

if ($_GET["direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["direction"];
	if ($_GET["direction"] == "ASC") {
		$SortDirection = "DESC";
	} else {
		$SortDirection = "ASC";
	}
}

$is_ajaxed = $_GET['is_ajaxed'];
$ticket_search = $_GET['ticket_search'];

if (!empty($ticket_search)) {
	$sch_params[':search_prm'] = "%" . trim($ticket_search) . "%";
	$incr .= " AND (tc.tracking_id LIKE :search_prm OR tc.email LIKE :search_prm OR tc.name LIKE  :search_prm OR tc.phone LIKE :search_prm OR tc.subject LIKE :search_prm OR tc.status LIKE :search_prm OR cat.title LIKE :search_prm ) ";
} 

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages'])) {
	$per_page = $_GET['pages'];
}

$query_string = $has_querystring ? ($_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'search_ticket.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
if ($is_ajaxed) {
	try {
		$sel_sql = "SELECT tc.id, tc.tracking_id, tc.subject, tc.status, tc.created_at, tc.priority, tc.email,tc.name,tc.user_id,tc.opened_by,
	                     tc.last_replied, cat.title as cat_name, tkt_msg.user_type, tkt_msg.ticket_id,
	                     c.fname AS cust_fname, c.lname AS cust_lname, c.email AS cust_email,tc.assigned_admin_id as tc_admin_id,c.type as cust_type,c.rep_id,tc.phone,c.cell_phone 
	              FROM s_ticket tc
	              LEFT JOIN s_ticket_group as cat  ON(cat.id=tc.group_id)
	              LEFT JOIN customer c ON (c.id = tc.user_id)
	              LEFT JOIN (SELECT ticket_id,user_id, user_type
	                         FROM (SELECT ticket_id, user_id, user_type FROM s_ticket_message ORDER BY created_at DESC) as tkt_msg
	                         GROUP BY tkt_msg.ticket_id) as tkt_msg ON (tc.id = tkt_msg.ticket_id)
	                  WHERE tc.id > 0 $incr order by $SortBy $currSortDirection";

		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/search_ticket.inc.php';
	exit;

}
$exStylesheets = array("thirdparty/bootstrap-switch/css/bootstrap3/bootstrap-switch.css");
$exJs = array("thirdparty/bootstrap-switch/js/bootstrap-switch.js", 'thirdparty/clipboard/clipboard.min.js');
$template = 'search_ticket.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>