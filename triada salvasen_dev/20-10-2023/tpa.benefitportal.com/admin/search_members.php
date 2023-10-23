<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(1);
$affiliates_menu = has_menu_access(4);
$agent_menu = has_menu_access(5);
$member_menu = has_menu_access(8);
$group_menu = has_menu_access(6);

$sch_params = array();
$SortBy = "created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$recent = isset($_GET['recent']) ? $_GET['recent'] : '';
$member_search = isset($_GET['member_search']) ? $_GET['member_search'] : '';
$member_multiple_search = isset($_GET['member_multiple_search']) ? $_GET['member_multiple_search'] : '';

$incr = "";
if($recent == 'Y'){
	$sch_params[':admin_id'] = makeSafe($_SESSION['admin']['id']);
	$incr .= " AND rus.admin_id = :admin_id";
	$SortBy = 'rus.updated_at';
	$_GET['pages'] = 10;
}

if(!empty($member_search)){
	$sch_params[':search_prm'] = '%'.trim($member_search).'%';
	$incr .= " AND (c.rep_id LIKE :search_prm OR c.fname LIKE :search_prm OR c.lname LIKE  :search_prm OR c.email LIKE :search_prm OR c.cell_phone LIKE :search_prm OR c.state LIKE :search_prm OR c.city LIKE :search_prm OR c.zip LIKE :search_prm OR c.status LIKE :search_prm OR s.rep_id LIKE :search_prm OR s.fname LIKE :search_prm OR s.lname LIKE  :search_prm OR s.email LIKE :search_prm OR s.cell_phone LIKE :search_prm OR d.fname LIKE :search_prm OR d.lname LIKE :search_prm OR d.display_id LIKE :search_prm) ";
}

if(!empty($member_multiple_search)){
	$search_arr  = explode(',', $member_multiple_search);
	if(count($search_arr) > 0){
		$incr .= " AND (";
		foreach ($search_arr as $key => $value) {
			$sch_params[':search_prm_' . $key] = '%'.trim($value).'%';
			$incr .= " c.rep_id LIKE :search_prm_".$key ." OR c.fname LIKE :search_prm_".$key ." OR c.lname LIKE  :search_prm_".$key ." OR c.email LIKE :search_prm_".$key ." OR c.cell_phone LIKE :search_prm_".$key ." OR c.state LIKE :search_prm_".$key ." OR c.city LIKE :search_prm_".$key ." OR c.zip LIKE :search_prm_".$key ." OR c.status LIKE :search_prm_".$key ." OR s.rep_id LIKE :search_prm_".$key ." OR s.fname LIKE :search_prm_".$key ." OR s.lname LIKE :search_prm_".$key ." OR s.email LIKE :search_prm_".$key ." OR s.cell_phone LIKE :search_prm_".$key ." OR d.fname LIKE :search_prm_".$key ." OR d.lname LIKE :search_prm_".$key ." OR d.display_id LIKE :search_prm_".$key ."";
			if(count($search_arr) != ($key+1))
				$incr .= " OR ";
		}
		$incr .= " ) ";
	}
}

if (isset($_SESSION['company_id']) && $_SESSION['company_id'] != "") {
	$sch_params[':company_id'] = makeSafe($_SESSION['company_id']);
	$incr .= " AND c.company_id = :company_id";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'search_members.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		if($recent != 'Y'){
			$sel_sql = "SELECT c.rep_id, c.created_at, c.id, c.type, c.fname, c.lname, c.email, c.status, c.cell_phone,c.user_name,s.business_name as s_business_name,s.type as s_type,
                  s.fname as s_fname,s.lname as s_lname,c.sponsor_id, s.rep_id as sponsor_rep_id,c.import_type,count(DISTINCT(d.id)) as dep_count,d.display_id as dependent_display_id
	        FROM customer c
	        JOIN customer as s on(s.id= c.sponsor_id)
	        LEFT JOIN customer_dependent_profile d ON(c.id=d.customer_id and d.is_deleted = 'N')
	        WHERE c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Quotes','Pending Validation') " . $incr . "
	         GROUP BY c.id ORDER BY  $SortBy $currSortDirection";
		} else {
			$sel_sql = "SELECT c.rep_id, c.created_at, c.id, c.type, c.fname, c.lname, c.email, c.status, c.cell_phone,c.user_name,s.business_name as s_business_name,s.type as s_type,
	                  s.fname as s_fname,s.lname as s_lname,c.sponsor_id, s.rep_id as sponsor_rep_id,c.import_type,count(DISTINCT(d.id)) as dep_count,d.display_id as dependent_display_id
	        FROM customer c
	        JOIN customer as s on(s.id= c.sponsor_id)
	        LEFT JOIN customer_dependent_profile d ON(c.id=d.customer_id and d.is_deleted = 'N')
	        JOIN recent_user_search as rus ON (rus.customer_id = c.id)
	        WHERE c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Quotes','Pending Validation') " . $incr . "
	         GROUP BY c.id ORDER BY  $SortBy $currSortDirection";
		}
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
			$link_array = (isset($paginate->links_array['links']) && $paginate->links_array['links'] ? $paginate->links_array['links'] : array());
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	if (count($link_array) > 0) {
		foreach ($link_array as $value) {
			foreach ($value as $key => $val) {
				if ($val['is_current_page'] != 0) {
					$curr_ajax_url['link_url'] = $val['link_url'];
				}
			}
		}
	}

	include_once 'tmpl/search_members.inc.php';
	exit;
}
$exStylesheets = array("thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/sweetalert/sweetalert.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js', 'thirdparty/clipboard/clipboard.min.js');

$template = 'search_members.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php'; ?>