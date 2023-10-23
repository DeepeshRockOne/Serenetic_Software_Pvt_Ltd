<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Global Search';

$gsearch = $_GET['gsearch'];
$gsearch_area_type = $_GET['gsearch_area_type'];

$total_reps = 0;
$total_admins = 0;
$total_customers = 0;

$gsearch = cleanSearchKeyword($gsearch); 
 
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Admins')) {
// admins
	$admins_sql = "SELECT COUNT(id) AS admins
                FROM admin
                where fname LIKE :fname OR lname LIKE :lname OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR phone LIKE :phone";
	$admins_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':phone' => '%' . makeSafe($gsearch) . '%',
	);
	$admins_rs = $pdo->selectOne($admins_sql, $admins_params);
	$total_admins = $admins_rs['admins'];
}

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Affiliates')) {

	//$actholder_sql = "SELECT COUNT(DISTINCT id) AS affiliates
                    // FROM customer
                    // WHERE (fname LIKE :name OR lname LIKE :name OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR rep_id=:rep_id OR display_id=:user_id OR cell_phone LIKE :cell_phone) AND type IN ('Affiliates') AND customer.status  IN('Active') AND is_deleted='N'";
	$actholder_sql ="SELECT COUNT(DISTINCT c.id) AS affiliates
                  FROM customer c
                  JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN agent_product_rule apr ON(apr.agent_id = c.id)
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(c.fname,' ',c.lname) LIKE :name OR CONCAT(c.lname,' ',c.fname) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.id>0 AND c.type='Affiliates' AND c.is_deleted ='N'";
	$achoutholder_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':rep_id' => makeSafe($gsearch),
		':user_id' => makeSafe($gsearch),
	);

	$accountholder_rs = $pdo->selectOne($actholder_sql, $achoutholder_params);
	$total_ambassadors = $accountholder_rs['affiliates'];

}
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Agents')) {

	//$actholder_sql = "SELECT COUNT(DISTINCT id) AS agents
                    //FROM customer
                    //WHERE (fname LIKE :name OR lname LIKE :name OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR rep_id=:rep_id OR display_id=:user_id OR cell_phone LIKE :cell_phone) AND type IN ('Agent') AND customer.status  IN('Active') AND is_deleted='N'";
	$actholder_sql ="SELECT COUNT(DISTINCT c.id) AS agents
                  FROM customer c
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN agent_twilio_number at on(at.customer_id=c.id)
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(c.fname,' ',c.lname) LIKE :name OR CONCAT(c.lname,' ',c.fname) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.type='Agent' AND c.is_deleted = 'N' AND c.id>0 ";

	$achoutholder_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':rep_id' => makeSafe($gsearch),
		':user_id' => makeSafe($gsearch),
	);

	$accountholder_rs = $pdo->selectOne($actholder_sql, $achoutholder_params);
	$total_agents = $accountholder_rs['agents'];

}
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Call Center')) {

	//$actholder_sql = "SELECT COUNT(DISTINCT id) AS call_center
                    //FROM customer
                    //WHERE (fname LIKE :name OR lname LIKE :name OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR rep_id=:rep_id OR display_id=:user_id OR cell_phone LIKE :cell_phone) AND type IN ('Call Center') AND customer.status  IN('Active') AND is_deleted='N'";
	$actholder_sql="SELECT COUNT(DISTINCT c.id) AS call_center
                  FROM customer c
                  LEFT JOIN customer as s on(c.id= s.sponsor_id)
                  LEFT JOIN customer as sp on(c.sponsor_id= sp.id)
                  LEFT JOIN agent_twilio_number at on(at.customer_id=c.id)
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(c.fname,' ',c.lname) LIKE :name OR CONCAT(c.lname,' ',c.fname) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.type='Call Center' AND c.is_deleted = 'N' AND c.id>0";
	$achoutholder_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':rep_id' => makeSafe($gsearch),
		':user_id' => makeSafe($gsearch),
	);

	$accountholder_rs = $pdo->selectOne($actholder_sql, $achoutholder_params);
	$total_call_center = $accountholder_rs['call_center'];

}
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Groups')) {

	// $group_sql = "SELECT COUNT(DISTINCT id) AS groups
 //                    FROM customer
 //                    WHERE (fname LIKE :name OR lname LIKE :name OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR rep_id=:rep_id OR display_id=:user_id OR cell_phone LIKE :cell_phone) AND type IN ('Group') AND customer.status IN('Active') AND is_deleted='N'";
	$group_sql = "SELECT COUNT(DISTINCT c.id) AS groups
                  FROM customer c 
                  JOIN customer_group_settings cg ON(c.id=cg.customer_id)
                  LEFT JOIN customer s ON (c.sponsor_id=s.id)
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(c.fname,' ',c.lname) LIKE :name OR CONCAT(c.lname,' ',c.fname) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id  OR c.cell_phone LIKE :cell_phone) AND c.type='Group' AND c.is_deleted ='N' AND c.id>0";

	$group_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':rep_id' => makeSafe($gsearch),
		// ':user_id' => makeSafe($gsearch),
	);
	
	$group_rs = $pdo->selectOne($group_sql, $group_params);
	$total_groups = $group_rs['groups'];

}
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Leads')) {

	//$leads_sql = "SELECT COUNT(DISTINCT id) AS lead
                    //FROM leads
                    //WHERE (fname LIKE :name OR lname LIKE :name OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR lead_id=:lead_id OR cell_phone LIKE :cell_phone) AND status NOT IN('Converted') AND is_deleted = 'N'";
	$leads_sql = "SELECT COUNT(DISTINCT l.id) AS lead
                FROM leads l
                LEFT JOIN customer c on(l.customer_id = c.id AND c.is_deleted ='N')
                LEFT JOIN customer s ON (l.sponsor_id = s.id)
                WHERE (l.fname LIKE :name OR l.lname LIKE :name OR CONCAT(l.fname,' ',l.lname) LIKE :name OR CONCAT(l.lname,' ',l.fname) LIKE :name OR l.email LIKE :email OR l.lead_id=:lead_id OR l.cell_phone LIKE :cell_phone) AND l.id>0 AND l.is_deleted = 'N' AND l.status != 'Converted'";
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

	//$group_sql = "SELECT COUNT(DISTINCT id) AS customers
                    //FROM customer
                    // WHERE (fname LIKE :name OR lname LIKE :name OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR rep_id=:rep_id OR display_id=:user_id OR cell_phone LIKE :cell_phone) AND type IN ('Customer') AND customer.status IN('Active') AND is_deleted='N'";
	$group_sql = "SELECT COUNT(DISTINCT c.id) AS customers
                  FROM customer c
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN customer_dependent d ON(c.id=d.customer_id and d.is_deleted = 'N')
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(c.fname,' ',c.lname) LIKE :name OR CONCAT(c.lname,' ',c.fname) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.id>0 AND c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Quotes','Pending Validation','Pending')";
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
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Providers')) {

	$group_sql = "SELECT COUNT(DISTINCT p.id) AS providers
                    FROM providers p
                    LEFT JOIN sub_providers sp ON (p.id=sp.providers_id)
                    WHERE (p.provider_name LIKE :name  OR p.email LIKE :email OR p.display_id=:user_id OR sp.cell_phone LIKE :cell_phone) AND p.status IN('Active') AND p.is_deleted='N'";

	$group_params = array(
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':user_id' => makeSafe($gsearch),
	);
	
	$group_rs = $pdo->selectOne($group_sql, $group_params);
	$total_providers = $group_rs['providers'];

}
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Organizations')) {

	$group_sql = "SELECT COUNT(DISTINCT id) AS organizations
                    FROM customer
                    WHERE (fname LIKE :name OR lname LIKE :name OR CONCAT(fname,' ',lname) LIKE :name OR CONCAT(lname,' ',fname) LIKE :name OR email LIKE :email OR rep_id=:rep_id OR display_id=:user_id OR cell_phone LIKE :cell_phone) AND type IN ('Organization') AND is_deleted='N'";

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
	$total_organizations = $group_rs['organizations'];

}


$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/clipboard/clipboard.min.js', 'thirdparty/colorbox/jquery.colorbox.js');
$page_title = "Global Search";
$template = 'global_search.inc.php';
// $layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>