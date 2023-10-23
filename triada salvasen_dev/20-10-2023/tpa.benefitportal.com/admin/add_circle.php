<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(66);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[1]['link'] = 'communication_circle.php';
$breadcrumbes[2]['title'] = 'Circles';
$breadcrumbes[2]['link'] = 'add_circle.php';

$circlId = checkIsset($_GET['id']);
$activeAdmin = $pdo->select("SELECT id,fname,lname,display_id from admin where status='Active' and is_deleted='N'");

$circleName = $circleStatus = '';
$circlArr = array();
if(!empty($circlId)){
    $adminCircle = $pdo->selectOne("SELECT ac.id,ac.name,ac.status,GROUP_CONCAT(aac.admin_id) AS adminIds from admin_circle ac JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N' AND aac.admin_id!=0) where ac.is_deleted='N' AND md5(ac.id)=:id",array(":id"=>$circlId));
    if(!empty($adminCircle['id'])){
        $circleName = $adminCircle['name'];
        $circleStatus = $adminCircle['status'];
        $circlArr = explode(',',$adminCircle['adminIds']);
    }
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$page_title = "Add Circle";
$template = 'add_circle.inc.php';
include_once 'layout/end.inc.php';
?>
