<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Groups';
$breadcrumbes[2]['title'] = 'Members';
$breadcrumbes[2]['link'] = 'member_listing.php';
$breadcrumbes[3]['title'] = 'Manage Members';

$res_t =$pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Member',":status"=>'Active')); 

$reasions = $pdo->select("SELECT md5(id) as id, name from termination_reason where is_deleted='N' ORDER BY name ASC");
$summernote = "Y";

$exJs = array('thirdparty/ckeditor/ckeditor.js');

$template = 'manage_members.inc.php';
include_once 'layout/end.inc.php';
?>