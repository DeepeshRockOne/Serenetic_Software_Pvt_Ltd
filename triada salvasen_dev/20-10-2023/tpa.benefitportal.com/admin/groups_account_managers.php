<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$group_id = $_GET['group_id'];
if(isset($_SESSION['admin']['timezone'])){
	$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
}
$res_acc = array();
if(!empty($group_id)){
    $res_acc = $pdo->select("SELECT md5(sa.id) as sa_id,sa.created_at,CONCAT(sa.fname,' ', sa.lname) as sa_name,a.fname as  afname,a.lname as alname,md5(a.id) as aid,a.display_id,sa.status,sa.feature_access 
    	from sub_group sa 
    	LEFT JOIN admin a ON(a.id=sa.admin_id and a.is_deleted='N') 
    	where  md5(sa.group_id)=:id  AND sa.is_deleted='N'",array(':id'=>$group_id));
}

$template = 'groups_account_managers.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
