<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$categoryId = checkIsset($_GET['id']);
$categoryRec = array();
if(!empty($categoryId)){
    $categoryRec = $pdo->selectOne("SELECT sc.title,sc.id,group_concat(sa.admin_id) as adminIds from s_ticket_group sc LEFT JOIN s_ticket_assign_admin sa ON(sa.s_ticket_group_id=sc.id and sa.is_deleted='N') where sc.is_deleted='N' and md5(sc.id)=:id",array(":id"=>$categoryId));
}
$access_level_sql = "SELECT id,name FROM access_level ORDER BY name";
$access_level_res = $pdo->select($access_level_sql);

$adminRes = $pdo->select("SELECT id,fname,lname,display_id,type from admin where is_deleted='N'");
if(!empty($access_level_res) && !empty($adminRes)) {
	foreach ($access_level_res as $key => $access_level_row) {
		foreach ($adminRes as $key2 => $adminRow) {
			if($adminRow['type'] == $access_level_row['name']) {
				if(!isset($access_level_res[$key]['admin_res'])) {
					$access_level_res[$key]['admin_res'] = array();
				}
				$access_level_res[$key]['admin_res'][] = $adminRow;
			}
		}
	}
}
foreach ($access_level_res as $key => $access_level_row) {
	if(empty($access_level_row['admin_res'])) {
		unset($access_level_res[$key]);
	}
}
//pre_print($access_level_res);
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = "add_etickets_groups.inc.php";
include_once 'layout/iframe.layout.php';
?>