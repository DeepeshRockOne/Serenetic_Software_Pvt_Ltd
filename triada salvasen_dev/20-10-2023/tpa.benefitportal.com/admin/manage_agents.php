<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Agents";
$breadcrumbes[1]['link'] = "agent_listing.php";
$breadcrumbes[2]['title'] = "Manage Agents";
$breadcrumbes[2]['class'] = "Active";


$res_t =$pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Agent',":status"=>'Active')); 
$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['admin']['timezone']);
$acl = $pdo->select("SELECT *,md5(id) as id1 FROM agent_coded_level  WHERE is_active=:is_active order by id desc",array("is_active"=>'Y'));

$sel_agent_level = $pdo->select("SELECT COUNT(cs.agent_coded_id) as total,agent_coded_id as type FROM customer_settings cs JOIN customer c ON(c.id=cs.customer_id) WHERE c.is_deleted=:is_deleted GROUP BY agent_coded_id",array(':is_deleted'=>'N'));

$total_ass=array();
$access_lvl = array();
foreach($acl as $ac){
    $access_lvl[$ac['id']] = $ac['id'];
}
foreach($access_lvl as $lvl){
    foreach($sel_agent_level as $ass){
        if($ass['type']==$lvl){
            $total_ass[$lvl] = $ass['total'];
            break;
        }else{
            $total_ass[$lvl] = 0;
        }
    }
}

$summernote = "Y";

$exJs = array(
    'thirdparty/ckeditor/ckeditor.js'
);

$page_title = "Manage Agents";
$page_title = "Manage Agents";
$template = "manage_agents.inc.php";
include_once 'layout/end.inc.php';
