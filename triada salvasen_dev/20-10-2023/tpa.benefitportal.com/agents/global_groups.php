<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$agent_id = $_SESSION['agents']['id'];
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();
if(in_array('Groups', $displayDirectEnroll)){
    $sponsor_agents = get_direct_loa_agents($agent_id,false);
} else {
    $sponsor_agents = get_downline_agents($agent_id,false);
}
$SortBy = "c.joined_date";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$gsearch = isset($_GET['gsearch']) ? $_GET['gsearch'] : "";
$is_ajaxed_groups = isset($_GET['is_ajaxed_groups']) ? $_GET['is_ajaxed_groups'] : "";
$has_querystring = false;
$sch_params = array();
$incr = "";

if ($gsearch != "") {
	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':rep_id'] = makeSafe($gsearch);
	$sch_params[':cell_phone'] = '%' . makeSafe($gsearch) . '%';
	$incr .= " AND (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.cell_phone LIKE :cell_phone)";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] > 0 ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'global_groups.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed_groups) {
	$sel_sql = "SELECT c.rep_id, c.joined_date,c.invite_at,md5(c.id) as id, c.type, c.fname, c.lname, c.email, c.status,c.cell_phone,c.user_name,c.business_name,s.fname as s_fname,c.access_type,s.lname as s_lname,c.sponsor_id, c.upline_sponsors,s.rep_id as sponsor_rep_id,HOUR(TIMEDIFF(NOW(), c.invite_at)) as invite_time_diff,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,count(DISTINCT p.id) as total_products,count(DISTINCT(m.id)) as total_members
                  FROM customer c
                  JOIN customer as s on(s.id= c.sponsor_id)
				  JOIN agent_product_rule apr on(apr.agent_id = c.id AND apr.is_deleted = 'N')
				  JOIN prd_main p on(apr.product_id = p.id AND p.is_deleted = 'N' AND p.product_type ='Group Enrollment')
				  LEFT JOIN customer m ON (m.sponsor_id = c.id AND m.type='Customer')
                  WHERE c.type='Group' AND c.is_deleted = 'N' AND c.sponsor_id IN(".implode(',',$sponsor_agents).") 
                  " . $incr . " 
                  GROUP BY c.id 
				  ORDER BY  $SortBy $currSortDirection";
	$paginate = new pagination($page, $sel_sql, $options);
	if ($paginate->success == true) {
		$fetch_rows = $paginate->resultset->fetchAll();
		$total_rows = count($fetch_rows);
	}
}
include_once 'tmpl/global_groups.inc.php';
?>
