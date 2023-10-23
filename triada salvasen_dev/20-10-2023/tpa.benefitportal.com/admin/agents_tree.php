<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$agent_id = checkIsset($_REQUEST['agent_id']);
$is_up_root_check = checkIsset($_REQUEST['is_up_root_check']);

$sponsor_id = $agent_id;

$exStylesheets = array('thirdparty/spacetree/base.css', 'thirdparty/spacetree/Spacetree.css');
$exJs = array('thirdparty/spacetree/jit-yc.js');



$template = 'agents_tree.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>