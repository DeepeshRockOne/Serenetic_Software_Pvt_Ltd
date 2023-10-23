<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Admins";
$breadcrumbes[1]['link'] = "admins.php";
$breadcrumbes[2]['title'] = "Manage Admins";
$breadcrumbes[2]['class'] = "Active";

$page_title = "Add New Admin";

$security_ajax = isset($_POST['security_ajax']) ? $_POST['security_ajax'] : '';
if($security_ajax){
	$is_2fa = !empty($_POST['is_2fa']) ? $_POST['is_2fa'] : 'N';
	$is_ip_restriction = !empty($_POST['is_ip_restriction']) ? $_POST['is_ip_restriction'] : 'N';
	$allowed_ip_res = !empty($_POST['allowed_ip_res']) ? $_POST['allowed_ip_res'] : array();
	if($is_ip_restriction == "N") {
		$allowed_ip_res = array();
	}
	$validate = new Validation();

	if($is_ip_restriction == "Y") {
        foreach ($allowed_ip_res as $key => $allowed_ip) {
            $validate->string(array('required' => true, 'field' => 'ip_address_'.$key, 'value' => $allowed_ip), array('required' => 'IP Address is required'));
            if (!empty($allowed_ip) && !filter_var($allowed_ip, FILTER_VALIDATE_IP)) {
                $validate->setError('ip_address_'.$key, 'IP Address not valid');
            }
        }
	}
	$res = array();

	if($validate->isValid()){
		
		$new_update_details = array(
			'is_2fa' => isset($_POST['is_2fa']) ? 'Selected' : 'Unselected',
			'is_ip_restriction' => isset($_POST['is_ip_restriction']) ? 'Selected' : 'Unselected',
			'allowed_ip' => implode(',',array_values($allowed_ip_res)),
		);

		$params = array(
			'user_type' => 'admin',
			'is_2fa' => $is_2fa,
			'is_ip_restriction' => $is_ip_restriction,
			'allowed_ip' => !empty($allowed_ip_res) ? implode(',',array_values($allowed_ip_res)) : '',
		);
		$extra_parma = array(
			'ac_message_1' => 'updated Global Admin Login Security <br>',
			'new_update_details' => $new_update_details,
			'user_id' => $_SESSION['admin']['id'],
			'link'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'display_id' =>$_SESSION['admin']['display_id'],
			'entity_action' => 'Global Admin Login Security',
			'user_fname'=>$_SESSION['admin']['fname'],
			'user_fname'=>$_SESSION['admin']['lname'],
		);
		$status = save2FAuserSetting('Admin',$params,$extra_parma);
		$res['status'] = $status;
	}
	if(count($validate->getErrors()) > 0){
		$res['status'] = "fail";
    	$res['errors'] = $validate->getErrors();
	}
	header('Content-Type: application/json');
	echo json_encode($res);
	exit;
}

if(isset($_GET['get_access_level_data'])) {

	$sql_acl = "SELECT md5(id) as id,name,dashboard,feature_access,created_at FROM access_level ORDER BY name";
	$res_acl = $pdo->select($sql_acl);

	$sel_assign = "SELECT count('type') as total,type from admin where is_deleted='N' group by type";
	$res_assign = $pdo->select($sel_assign);

	$total_ass=array();
	$access_lvl = array();
	foreach($res_acl as $acl){
	    $access_lvl[$acl['name']] = $acl['name'];
	}

	foreach($access_lvl as $lvl){
	    foreach($res_assign as $ass){
	        if($ass['type']==$access_lvl[$lvl]){
	            $total_ass[$lvl] = $ass['total'];
	            break;
	        }else{
	            $total_ass[$lvl] = 0;
	        }
	    }
	}
	include_once 'tmpl/add_access_level.inc.php';
	exit();
}

$globalSett = get_global_user_setting('Admin');

$res_t =$pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Admin',":status"=>'Active')); 
$summernote = "Y";

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/summernote-master/dist/summernote.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js', 'thirdparty/ckeditor/ckeditor.js', 'thirdparty/summernote-master/dist/popper.js', 'thirdparty/summernote-master/dist/summernote.js');

$page_title = "Add New Admin";
$template = "add_access_level.inc.php";
include_once 'layout/end.inc.php';
