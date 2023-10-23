<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';
//start set variable for the resouce tab tag only
$html_res_tag = isset($_GET['editortype']) ? $_GET['editortype'] : '';
$editortype = false;
//end set variable for the resouce tab tag only
$displayAll = true;
$displayUser = false;
$showSmartTagClass = 'allSmartTag';
if($user_type != ''){
	$displayUser = true;
	$displayAll = false;
	if($user_type == 'member'){
		$showSmartTagClass = 'memberSmartTag';
		// start set the condition on resourec tab
		if($html_res_tag == 'html_res_tag'){
		 	$editortype = true;
		}
		// end set the condition on resourec tab
	}else if($user_type == 'agent'){
		$showSmartTagClass = 'agentSmartTag';
	}else if($user_type == 'admin'){
		$showSmartTagClass = 'adminSmartTag';
	}else if($user_type == 'group'){
		$showSmartTagClass = 'groupSmartTag';
	}else if($user_type == 'lead'){
		$showSmartTagClass = 'leadSmartTag';
	}else{
		$displayAll = true;
		$displayUser = false;
	}
}
$template = "smart_tag_popup.inc.php";
include_once 'layout/iframe.layout.php';
?>