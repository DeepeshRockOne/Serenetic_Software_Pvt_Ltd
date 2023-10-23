<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(2);


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'All Users';

$gsearch = isset($_GET['gsearch']) ? trim($_GET['gsearch']) : '';
$gsearch_area_type = isset($_GET['gsearch_area_type']) ? $_GET['gsearch_area_type'] : '';

$total_reps = 0;
$total_admins = 0;
$total_customers = 0;

$gsearch = cleanSearchKeyword($gsearch); 
  
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Admins')) {
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

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Agents' || $gsearch_area_type == 'Agent')) {

	// $sch_params = array(
	// 	':fname' => '%' . makeSafe($gsearch) . '%',
	// 	':lname' => '%' . makeSafe($gsearch) . '%',
	// 	':name' => '%' . makeSafe($gsearch) . '%',
	// 	':display_id' => '%' . makeSafe($gsearch) . '%',
	// 	);
	$incr = '';

	// // $gsearch = str_replace("+1", "", $gsearch);
	// if(strpos($gsearch,'@') != false){
	// 	$sch_params[':email'] = "%" . makeSafe($gsearch) . "%";
	// 	$incr .= " OR c.email LIKE :email";
	// }


	// $p = "/\(\d{3}\)\s\d{3}-\d{4}/";
	// if(preg_match($p,$gsearch,$m)){
	//     $gsearch = str_replace("(", "", $gsearch);
	//     $gsearch = str_replace(")", "", $gsearch);
	//     $gsearch = str_replace("-", "", $gsearch);
	//     $gsearch = str_replace(" ", "", $gsearch);

	//     $sch_params[':cell_phone'] = "%" . makeSafe($gsearch) . "%";
	// 	$incr .= " OR c.cell_phone LIKE :cell_phone";

	// }else if(preg_match("/^[1-9][0-9]*$/",$gsearch,$m)){
	// 	$sch_params[':cell_phone'] = "%" . makeSafe($gsearch) . "%";
	// 	$incr .= " OR c.cell_phone LIKE :cell_phone";
	// }

	// //$p = "/\([a-zA-Z]{1}\)\d)/";
	// $p = "/^[a-z0-9.\-]+$/i";	
	
	// //preg_match('/^[a-z0-9.\-]+$/i', $firstname)

	// if(preg_match($p,$gsearch)){
	//     $sch_params[':rep_id'] = "%" . makeSafe($gsearch) . "%";
	// 	$incr .= " OR c.rep_id LIKE :rep_id";
	// }

	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':rep_id'] = makeSafe($gsearch);
	$sch_params[':company'] = makeSafe($gsearch);
	$sch_params[':user_id'] = makeSafe($gsearch);
	$sch_params[':cell_phone'] = '%' . makeSafe($gsearch) . '%';
	$incr .= " AND (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR  cs.company=:company OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone)";
	
	$actholder_sql ="SELECT COUNT(DISTINCT c.id) AS agents
                  FROM customer c
                  JOIN customer_settings cs ON (c.id = cs.customer_id)
                  WHERE c.type='Agent' AND c.is_deleted = 'N' AND c.id>0 $incr ";

	$accountholder_rs = $pdo->selectOne($actholder_sql, $sch_params);
	$total_agents = $accountholder_rs['agents'];
}

if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Groups')) {
	$group_sql = "SELECT COUNT(DISTINCT c.id) AS `groups`
                  FROM customer c
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id  OR c.cell_phone LIKE :cell_phone OR c.business_name LIKE :business_name ) AND c.type='Group' AND c.is_deleted ='N' AND c.id>0";

	$group_params = array(
		':fname' => '%' . makeSafe($gsearch) . '%',
		':lname' => '%' . makeSafe($gsearch) . '%',
		':name' => '%' . makeSafe($gsearch) . '%',
		':email' => '%' . makeSafe($gsearch) . '%',
		':cell_phone' => '%' . makeSafe($gsearch) . '%',
		':rep_id' => makeSafe($gsearch),
		':business_name' => makeSafe($gsearch),
	);
	
	$group_rs = $pdo->selectOne($group_sql, $group_params);
	$total_groups = $group_rs['groups'];

}
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Leads')) {
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
if ($gsearch != "" || ($gsearch_area_type == '' || $gsearch_area_type == 'Members')) {

	$group_sql = "SELECT COUNT(DISTINCT c.id) AS customers
                  FROM customer c
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN website_subscriptions ws on(ws.customer_id=c.id)
                  LEFT JOIN prd_main p ON(p.id=ws.product_id and p.is_deleted='N' AND p.type!='Fees')
                  WHERE (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone) AND c.id>0 AND c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Quotes','Pending Validation','Post Payment','Pending')";

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

if (!empty($_GET['member_status']) && !empty($_GET['admin_id'])) {
	$change_type = $_GET['member_status'];
	$old_status = $_GET['old_status'];
	$admin_id = $_GET['admin_id'];
	if (!empty($admin_id) &&  $change_type != "") {
		$updateSql = array('status' => makeSafe($change_type));
		$where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($admin_id)));

		$pdo->update("admin", $updateSql, $where);

		// $extra['old_type'] = $old_status;
		// $extra['new_type'] = $change_type;

		$extra['status_update'] = $old_status." to ".$change_type;
		$extra['from'] = ' from ';
		$extra['display_id'] = $_SESSION['admin']['display_id'];
		$res_enity =$pdo->selectOne("SELECT fname,lname,display_id,id from admin where md5(id)=:id",array(":id"=>$admin_id));
		$extra['res_fname'] = $res_enity['fname'];
		$extra['res_lname'] = $res_enity['lname'];
		$extra['res_display_id'] = $res_enity['display_id'];
		$description['description'] = $_SESSION['admin']['display_id']." updated ".$res_enity['fname'].' '.$res_enity['lname'].' '.$res_enity['display_id'].' admin status from '.$old_status." to ".$change_type;
		activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $res_enity['id'], 'update_old_to_new','Admin Status Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));

		setNotifySuccess("Admin status changed successfully");
		redirect("all_users.php");
	}
}

if (isset($_GET['type']) && isset($_GET['id'])) {
	$change_type = $_GET['type'];
	$admin_id = $_GET['id'];
	$old_type = $_GET['old_type'];

	if (!empty($admin_id) && $change_type != "") {
		$updateSql = array('type' => makeSafe($change_type));
		
		$where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($admin_id)));

		$update_params_new = $updateSql;
		$extra_column ='';
		foreach ($update_params_new as $key_audit => $up_params) {
			$extra_column .= "," . $key_audit;
		}
		if ($extra_column != '') {
			$extra_column = trim($extra_column, ',');

			$select_admin_data = "SELECT " . $extra_column . " FROM admin WHERE md5(id)=:id";
			$select_admin_where = array(':id' => $admin_id);

			$result_audit_admin_data = $pdo->selectOne($select_admin_data, $select_admin_where);
		}

		$pdo->update("admin", $updateSql, $where);

		/* Code for audit log*/
		$user_data = get_user_data($_SESSION['admin']);
		audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Admin Type Updated Id is " . $admin_id, $result_audit_admin_data, $update_params_new, 'admin type updated by admin');

		$extra['from'] = ' from ';
		$extra['status_update'] = $old_type." to ".$change_type;
		$extra['user_display_id'] = $_SESSION['admin']['display_id'];
		$res_enity =$pdo->selectOne("SELECT fname,lname,display_id,id from admin where md5(id)=:id",array(":id"=>$admin_id));
		$extra['en_fname'] = $res_enity['fname'];
		$extra['en_lname'] = $res_enity['lname'];
		$extra['en_display_id'] = $res_enity['display_id'];
		$description['description'] = $_SESSION['admin']['display_id']." updated ".$res_enity['fname'].' '.$res_enity['lname'].' '.$res_enity['display_id'].' admin level from '.$old_type." to ".$change_type;
		activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $res_enity['id'], 'update_old_to_new','Admin Access Level Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
		setNotifySuccess("Access levels updated successfully");
		redirect("all_users.php",true);
	}
}

$exStylesheets = array('thirdparty/colorbox/colorbox.css','thirdparty/sweetalert/sweetalert.css');
$exJs = array('thirdparty/clipboard/clipboard.min.js', 'thirdparty/sweetalert/jquery.sweet-alert.custom.js', 'thirdparty/colorbox/jquery.colorbox.js');
$page_title = "All Users";
$template = 'all_users.inc.php';
include_once 'layout/end.inc.php';
?>