<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Communications";
$breadcrumbes[2]['title'] = "Interactions";
$breadcrumbes[2]['class'] = "interaction_dashboard.php";

$interactionArr=array(
	0 => array('title'=>'AGENT INTERACTION' , 'user_type'=>'agent' , 'id'=>'agent_interaction'),
	1 => array('title'=>'EMPLOYER GROUP INTERACTION' , 'user_type'=>'group' , 'id'=>'group_interaction'),
	2 => array('title'=>'MEMBER INTERACTION' , 'user_type'=>'member' , 'id'=>'member_interaction')
);


$template = 'interaction_dashboard.inc.php';
include_once 'layout/end.inc.php';
?>
