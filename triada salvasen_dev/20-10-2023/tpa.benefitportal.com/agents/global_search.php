<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$function_list = new functionsList();
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Global Search';
$agent_id = $_SESSION['agents']['id'];
$gsearch = isset($_GET['gsearch']) ? trim($_GET['gsearch']) : '';
$gsearch_area_type = isset($_GET['gsearch_area_type']) ? $_GET['gsearch_area_type'] : '';
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();
$total_reps = 0;
$total_customers = 0;

$gsearch = cleanSearchKeyword($gsearch); 
 	
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Agents' || $gsearch_area_type == 'Agent')) {
	if(in_array('Agents', $displayDirectEnroll)){
        $sponsor_agents = get_direct_loa_agents($agent_id,false);
    } else {
        $sponsor_agents = get_downline_agents($agent_id,false);
    }

	$incr = '';
	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':rep_id'] = makeSafe($gsearch);
	$sch_params[':user_id'] = makeSafe($gsearch);
	$sch_params[':cell_phone'] = '%' . makeSafe($gsearch) . '%';
	$incr .= " AND (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.sponsor_id IN(".implode(',',$sponsor_agents).")";
	$actholder_sql ="SELECT COUNT(DISTINCT c.id) AS agents
                  FROM customer c
                  WHERE c.type='Agent' AND c.is_deleted = 'N' AND c.id>0 $incr ";

	$accountholder_rs = $pdo->selectOne($actholder_sql, $sch_params);
	$total_agents = $accountholder_rs['agents'];
}

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Groups')) {
	if(in_array('Groups', $displayDirectEnroll)){
        $sponsor_agents = get_direct_loa_agents($agent_id,false);
    } else {
        $sponsor_agents = get_downline_agents($agent_id,false);
    }

	$group_sql = "SELECT COUNT(DISTINCT c.id) AS `groups`
                  FROM customer c
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id  OR c.cell_phone LIKE :cell_phone) AND c.type='Group' AND c.is_deleted ='N' AND c.id>0 AND c.sponsor_id IN(".implode(',',$sponsor_agents).")";
	$group_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':rep_id' => makeSafe($gsearch),
	);
	
	$group_rs = $pdo->selectOne($group_sql, $group_params);
	$total_groups = $group_rs['groups'];

}

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Leads')) {
    if(in_array('Leads', $displayDirectEnroll)){
        $sponsor_agents = get_direct_loa_agents($agent_id,false);
    } else {
        $sponsor_agents = get_downline_agents($agent_id,false);
    }

	$leads_sql = "SELECT COUNT(DISTINCT l.id) AS `lead`
                FROM leads l
                WHERE (l.fname LIKE :name OR l.lname LIKE :name OR CONCAT(trim(l.fname),' ',trim(l.lname)) LIKE :name OR CONCAT(trim(l.lname),' ',trim(l.fname)) LIKE :name OR l.email LIKE :email OR l.lead_id=:lead_id OR l.cell_phone LIKE :cell_phone) AND l.id>0 AND l.is_deleted = 'N' AND l.sponsor_id IN(".implode(',',$sponsor_agents).")";
	$leads_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':lead_id' => makeSafe($gsearch),

	);

	$leads_rs = $pdo->selectOne($leads_sql, $leads_params);
	$total_leads = $leads_rs['lead'];

}

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Members')) {
	if(in_array('Members', $displayDirectEnroll)){
        $sponsor_agents = get_direct_loa_agents($agent_id,false);
    } else {
        $sponsor_agents = get_downline_agents($agent_id,false);
    }

	$group_sql = "SELECT COUNT(DISTINCT c.id) AS customers
                  FROM customer c
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN website_subscriptions ws on(ws.customer_id=c.id)
                  LEFT JOIN prd_main p ON(p.id=ws.product_id and p.is_deleted='N' AND p.type!='Fees')
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.id>0 AND c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Quotes','Pending Validation','Post Payment','Pending') AND c.sponsor_id IN(".implode(',',$sponsor_agents).")";
	$group_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':rep_id' => makeSafe($gsearch),
		':user_id' => makeSafe($gsearch),
	);	
	$group_rs = $pdo->selectOne($group_sql, $group_params);
	$total_customers = $group_rs['customers'];
}

$exStylesheets = array(
	'thirdparty/colorbox/colorbox.css',
	'thirdparty/sweetalert/sweetalert.css'
);
$exJs = array(
	'thirdparty/clipboard/clipboard.min.js',
	'thirdparty/sweetalert/jquery.sweet-alert.custom.js',
	'thirdparty/colorbox/jquery.colorbox.js'
);
$page_title = "Global Search";
$template = 'global_search.inc.php';
include_once 'layout/end.inc.php';
?>