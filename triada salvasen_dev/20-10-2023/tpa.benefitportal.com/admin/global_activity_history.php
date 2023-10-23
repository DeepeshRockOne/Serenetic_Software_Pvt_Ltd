<?php
// error_reporting(E_ALL);
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Global Activity History';
$breadcrumbes[1]['class'] = 'Active';
$user_groups = "active";

$gsearch_area_type = isset($_GET['gsearch_area_type']) ? $_GET['gsearch_area_type'] : '';
$gsearch = checkIsset($gsearch);
$total_admins = 0;
$total_customers = 0;
$total_agents=0;

/*if (($gsearch_area_type == '' || $gsearch_area_type == 'Admins')) {
// admins
    $admins_sql="SELECT COUNT(*) as admins FROM activity_feed af LEFT JOIN admin a ON(a.id=af.user_id)  WHERE af.entity_action!='New Order' AND af.is_deleted ='N' AND af.user_type='Admin'";
    $admins_rs = $pdo->selectOne($admins_sql);
    $total_admins = $admins_rs['admins'];

}

if (($gsearch_area_type == '' || $gsearch_area_type == 'Agents' || $gsearch_area_type == 'Agent')) {	
    
    $actholder_sql ="SELECT COUNT(*) as agents FROM activity_feed af LEFT JOIN customer c ON((af.entity_id=c.id OR af.user_id=c.id) AND c.type='Agent') WHERE  af.user_type='Agent' AND af.entity_type!='leads' AND af.entity_action!='New Order' AND af.is_deleted ='N'";
	$accountholder_rs = $pdo->selectOne($actholder_sql);
	$total_agents = $accountholder_rs['agents'];
}

if (($gsearch_area_type == '' || $gsearch_area_type == 'Agents' || $gsearch_area_type == 'Agent')) {	
    
    $actholder_sql ="SELECT COUNT(*) as groups FROM activity_feed af LEFT JOIN customer c ON((af.entity_id=c.id OR af.user_id=c.id) AND c.type='Group') WHERE  af.user_type='Group' AND af.entity_type!='leads' AND af.entity_action!='New Order' AND af.is_deleted ='N'";
	$accountholder_rs = $pdo->selectOne($actholder_sql);
	$total_groups = $accountholder_rs['groups'];
}

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Leads')) {
	$leads_sql = "SELECT COUNT(*) as leads 
	FROM activity_feed af 
	LEFT JOIN leads l ON (l.id=af.user_id)
	LEFT JOIN customer c ON(l.customer_id=c.id AND c.status IN('Affiliate Abandon','Affiliates Abandon','Agent Abandon','Invited','Customer Abandon','Group Abandon'))
	WHERE ((af.entity_type IN('Lead','leads')) OR (af.user_type IN('Lead','Leads'))) AND af.is_deleted = 'N'";	

	$leads_rs = $pdo->selectOne($leads_sql);
	$total_leads = $leads_rs['leads'];

}
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Members')) {

	$group_sql = "SELECT COUNT(*) as members FROM activity_feed af LEFT JOIN customer c on (c.id=af.user_id) WHERE  af.entity_action!='New Order' AND af.is_deleted ='N' AND (af.user_type='Customer' or af.entity_type='Customer') and af.user_type!='Agent'";
	
	$group_rs = $pdo->selectOne($group_sql);
	$total_customers = $group_rs['members'];

}*/
$selectize = true;
$exStylesheets = array('thirdparty/bootstrap-datepicker-master/css/datepicker.css', 'thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/masked_inputs/jquery.maskedinput.min.js','thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js',
'thirdparty/multiple-select-master/jquery.multiple.select.js');
$page_title = "Global Activity History";
$template = 'global_activity_history.inc.php';
include_once 'layout/end.inc.php';
?>