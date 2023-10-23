<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
has_access(3);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Admins";
$breadcrumbes[1]['class'] = "Active";
$user_groups = "active";
 
$sql_acl = "SELECT name FROM access_level ORDER BY name";
$res_acls = $pdo->select($sql_acl);

$features_arr = get_admin_feature_access_options();

if (!empty($_GET['member_status_c']) && !empty($_GET['admin_id'])) {
	$change_type = $_GET['member_status_c'];
	$old_status = $_GET['old_status'];
	$admin_id = $_GET['admin_id'];
	if (!empty($admin_id) && $change_type != "") {
		$updateSql = array('status' => makeSafe($change_type));
		$where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($admin_id)));

		$pdo->update("admin", $updateSql, $where);

		$extra['from'] = ' from ';
		$extra['status_update'] = $old_status." to ".$change_type;
		$extra['user_display_id'] = $_SESSION['admin']['display_id'];
		$res_enity =$pdo->selectOne("SELECT id,fname,lname,display_id from admin where md5(id)=:id",array(":id"=>$admin_id));
		$extra['en_fname'] = $res_enity['fname'];
		$extra['en_lname'] = $res_enity['lname'];
		$extra['en_display_id'] = $res_enity['display_id'];
		$description['description'] = $_SESSION['admin']['display_id']." updated ".$res_enity['fname'].' '.$res_enity['lname'].' '.$res_enity['display_id'].' admin status from '.$old_status." to ".$change_type;
		activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $res_enity['id'], 'update_old_to_new','Admin Status Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));

		/* End Code for audit log*/
		setNotifySuccess("Admin status changed successfully");
		redirect("admins.php");
	}
}

if (isset($_GET['type']) && isset($_GET['id'])) {
	$change_type = $_GET['type'];
	$old_type = $_GET['old_type'];
	$admin_id = $_GET['id'];
	if (!empty($admin_id) && $change_type != "") {
		setNotifySuccess("Access levels updated successfully");
		$updateSql = array('type' => makeSafe($change_type));
		$where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($admin_id)));

		/* Code for audit log */

		$extra_column = '';
		$update_params_new = $updateSql;
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
		/* End Code for audit log*/

		$extra['from'] = ' from ';
		$extra['status_update'] = $old_type." to ".$change_type;
		$extra['user_display_id'] = $_SESSION['admin']['display_id'];
		$res_enity =$pdo->selectOne("SELECT fname,lname,display_id,id from admin where md5(id)=:id",array(":id"=>$admin_id));
		$extra['en_fname'] = $res_enity['fname'];
		$extra['en_lname'] = $res_enity['lname'];
		$extra['en_display_id'] = $res_enity['display_id'];
		$description['description'] = $_SESSION['admin']['display_id']." updated ".$res_enity['fname'].' '.$res_enity['lname'].' '.$res_enity['display_id'].' admin level from '.$old_type." to ".$change_type;
		activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $res_enity['id'], 'update_old_to_new','Admin Access Level Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
		redirect("admins.php", true);
	}
}

$sch_params = array();
$incr='';
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
$display_id = !empty($_GET['display_id']) ? explode(",", $_GET['display_id']) : "";
$status = isset($_GET["status"]) ? $_GET["status"] : '';
$rep_id = isset($_GET['rep_id']) ? $_GET['rep_id'] : '';
$fname = isset($_GET['fname']) ? $_GET["fname"] : '';
$lname = isset($_GET['lname']) ? $_GET["lname"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$phone = isset($_GET['phone']) ? $_GET["phone"] : '';
$_access = isset($_GET['_access']) ? $_GET["_access"] : '';
$refine_filter = isset($_GET['refine_filter']) ? $_GET["refine_filter"] : '';
$s_member_status = isset($_GET['member_status']) ? $_GET['member_status'] : array();
if(in_array('Pending', $s_member_status)){
	$s_member_status[] = 'Suspended';
	$s_member_status[] = 'Terminated';
}
$a_level = isset($_GET['leveltype']) ? $_GET["leveltype"] : array();
$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";

$fname = cleanSearchKeyword($fname);
$lname = cleanSearchKeyword($lname);
$email = cleanSearchKeyword($email);
$phone = cleanSearchKeyword($phone); 
 
if ($fname != "") {
	$sch_params[':fname'] = "%" . makeSafe($fname) . "%";
	$incr .= " AND fname LIKE :fname";
}
if ($lname != "") {
	$sch_params[':lname'] = "%" . makeSafe($lname) . "%";
	$incr .= " AND lname LIKE :lname";
}
if ($phone != "") {
	$sch_params[':phone'] = "%" . makeSafe($phone) . "%";
	$incr .= " AND phone LIKE :phone";
}
if ($email != "") {
	$sch_params[':email'] = "%" . makeSafe($email) . "%";
	$incr .= " AND email LIKE :email";
}

if (count($s_member_status) > 0) {
	$imploded_status = "'" . implode("','", makeSafe($s_member_status)) . "'";
	$incr .= " AND status IN ($imploded_status)";
}

if (count($a_level) > 0 && !empty($a_level)) {
	$imploded_level = "'" . implode("','", makeSafe($a_level)) . "'";
	$incr .= " AND type IN ($imploded_level)";
}

if ($_access != "") {
	$imploded_access =  implode(",", makeSafe($_access)) . "";
	if($refine_filter != "" && $refine_filter=='blank'){
		foreach($_access as $ac){
			//$incr .= " AND feature_access NOT LIKE '%$ac,%' ";
			$incr .= " AND NOT FIND_IN_SET($ac,feature_access)";
		}
	}else{
		foreach($_access as $ac){
			$incr .= " AND FIND_IN_SET($ac,feature_access)";
		}
	}
}

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(created_at) >= :fromdate AND DATE(created_at) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(created_at) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(created_at) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(created_at) > :added_date";
  }
}

if (!empty($display_id)) {
	$display_id = array_map('trim',$display_id);
	$incr .= " AND display_id IN ('".implode("','",$display_id)."')";
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
	'url' => 'admins.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
 
if ($is_ajaxed) {

	if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_admin') {
		if($fromdate !='' && $todate != '') {
	        $no_days=0;
	        if($fromdate != '' && $todate != '') {
	            $date1 = date_create($fromdate);
	            $date2 = date_create($todate);
	            $diff = date_diff($date1,$date2);
	            $no_days = $diff->format("%a");
	        }
	        
	        if($no_days > 62) {
	            echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
	            exit();
	        }
	  	}

	  	$job_id = add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Admin Export","admin_export",$incr,$sch_params,'','admin_export');
	    $reportDownloadURL = $AWS_REPORTING_URL['admin_export']."&job_id=".$job_id;
	    echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
	    exit;
	}

	try {
		$sel_sql = "SELECT * , TIMESTAMPDIFF(HOUR,invite_at,now()) as invited_difference,md5(id) as id  
					FROM admin 
					WHERE 1 and is_deleted='N' " . $incr . " 
					ORDER BY $SortBy $currSortDirection";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/admins.inc.php';
	exit;
}

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$page_title = "Admins";
$template = 'admins.inc.php';
include_once 'layout/end.inc.php';
?>