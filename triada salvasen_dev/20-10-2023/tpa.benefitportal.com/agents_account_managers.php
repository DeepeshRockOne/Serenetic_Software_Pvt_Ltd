<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$agent_id = $_GET['agent_id'];

$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['agents']['timezone']);
$res_acc = array();
if(!empty($agent_id)){
    $res_acc = $pdo->select("SELECT md5(sa.id) as sa_id,sa.created_at,CONCAT(sa.fname,' ', sa.lname) as sa_name,a.fname as  afname,a.lname as alname,md5(a.id) as aid,a.display_id,sa.status,sa.feature_access from sub_agent sa LEFT JOIN admin a ON(a.id=sa.admin_id and a.is_deleted='N') where  md5(sa.agent_id)=:id  AND sa.is_deleted='N'",array(':id'=>$agent_id));
}

$template = 'agents_account_managers.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
