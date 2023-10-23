<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Support Dashboard';
$breadcrumbes[1]['link'] = 'support_dashboard.php';
$breadcrumbes[1]['class'] = 'Active';

if(!empty(get_admin_dashboard($_SESSION['admin']['id'])) && get_admin_dashboard($_SESSION['admin']['id']) != 'Support Dashboard'){
  redirect('dashboard.php');
}

$display_class = "";
$count = 0;
if(has_menu_access(11)){
	$count++;
}
if(has_menu_access(80)){
	$count++;
}
// if(has_menu_access(81)){
// 	$count++;
// }

if($count == 1){
	$display_class = "col-sm-12";
}else if($count == 2){
	$display_class = "col-sm-6";
}else if($count == 3){
	$display_class = "col-sm-4";
}

$gsearch = isset($_GET['gsearch']) ? trim($_GET['gsearch']) : '';
$gsearch_area_type = isset($_GET['gsearch_area_type']) ? $_GET['gsearch_area_type'] : '';

$total_reps = 0;
$total_admins = 0;
$total_customers = 0;
$total_leads = 0;
$total_agents = 0;
$total_groups = 0;
$total_users = 0;
$total_participants = 0;

$gsearch = cleanSearchKeyword($gsearch); 
 
if ($gsearch != "" && ($gsearch_area_type == '' || $gsearch_area_type == 'Admins')) {
	$admins_sql = "SELECT COUNT(id) AS admins
                FROM admin
                where is_active = 'Y' AND (fname LIKE :fname OR lname LIKE :lname OR CONCAT(trim(fname),' ',trim(lname)) LIKE :name OR CONCAT(trim(lname),' ',trim(fname)) LIKE :name OR email LIKE :email OR phone LIKE :phone OR display_id LIKE :display_id)";
	$admins_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':phone' => '%' . makeSafe($gsearch) . '%',
		':display_id'=> '%' . makeSafe($gsearch) . '%',
	);
	$admins_rs = $pdo->selectOne($admins_sql, $admins_params);
	$total_admins = $admins_rs['admins'];
}

if ($gsearch != "" && ($gsearch_area_type == '' || $gsearch_area_type == 'Agents' || $gsearch_area_type == 'Agent')) {

	// $sch_params = array(
	// 	':fname' => '%' . makeSafe($gsearch) . '%',
	// 	':lname' => '%' . makeSafe($gsearch) . '%',
	// 	':name' => '%' . makeSafe($gsearch) . '%',
	// 	':display_id' => '%' . makeSafe($gsearch) . '%',
	// 	);
	$incr = '';

	// $gsearch = str_replace("+1", "", $gsearch);
	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':business_name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':rep_id'] = makeSafe($gsearch);
	$sch_params[':user_id'] = makeSafe($gsearch);
	$sch_params[':cell_phone'] = '%' . makeSafe($gsearch) . '%';
	$incr .= " AND (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.business_name LIKE :business_name OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone)";
	
	$actholder_sql ="SELECT COUNT(DISTINCT c.id) AS agents
                  FROM customer c
                  WHERE c.type='Agent' AND c.is_deleted = 'N' AND c.id>0 $incr ";

	$accountholder_rs = $pdo->selectOne($actholder_sql, $sch_params);
	$total_agents = $accountholder_rs['agents'];
}

if ($gsearch != "" && ($gsearch_area_type == '' || $gsearch_area_type == 'Groups')) {
	$group_sql = "SELECT COUNT(DISTINCT c.id) AS `groups`
                  FROM customer c
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id  OR c.cell_phone LIKE :cell_phone) AND c.type='Group' AND c.is_deleted ='N' AND c.id>0";

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
if ($gsearch != "" && ($gsearch_area_type == '' || $gsearch_area_type == 'Leads')) {
	$leads_sql = "SELECT COUNT(DISTINCT l.id) AS `lead`
                FROM leads l
                WHERE (l.fname LIKE :name OR l.lname LIKE :name OR CONCAT(trim(l.fname),' ',trim(l.lname)) LIKE :name OR CONCAT(trim(l.lname),' ',trim(l.fname)) LIKE :name OR l.email LIKE :email OR l.lead_id=:lead_id OR l.cell_phone LIKE :cell_phone) AND l.id>0 AND l.is_deleted = 'N' ";
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
if ($gsearch != "" && ($gsearch_area_type == '' || $gsearch_area_type == 'Members')) {

	$group_sql = "SELECT COUNT(DISTINCT c.id) AS customers
                  FROM customer c
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN website_subscriptions ws on(ws.customer_id=c.id)
                  LEFT JOIN prd_main p ON(p.id=ws.product_id and p.is_deleted='N' AND p.type!='Fees')
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.id>0 AND c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Quotes','Pending Validation')";

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

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Participants')) {

	$p_sql = "SELECT COUNT(DISTINCT p.id) AS participants
                  FROM participants p
                  WHERE (p.fname LIKE :name OR p.lname LIKE :name OR CONCAT(trim(p.fname),' ',trim(p.lname)) LIKE :name OR CONCAT(trim(p.lname),' ',trim(p.fname)) LIKE :name OR p.participants_id LIKE :participants_id OR p.employee_id LIKE :employee_id) AND p.is_deleted = 'N'";

	$p_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':participants_id' => '%' . makeSafe($gsearch) . '%',
		':employee_id' => '%' . makeSafe($gsearch) . '%',
	);
	
	$p_rs = $pdo->selectOne($p_sql, $p_params);
	$total_participants = $p_rs['participants'];

}


//to get new ticket counter
$exStylesheets = array("thirdparty/bootstrap-switch/css/bootstrap3/bootstrap-switch.css");
$exJs = array("thirdparty/bootstrap-switch/js/bootstrap-switch.js", 'thirdparty/clipboard/clipboard.min.js');

$template = 'support_dashboard.inc.php';
include_once 'layout/end.inc.php';
?>