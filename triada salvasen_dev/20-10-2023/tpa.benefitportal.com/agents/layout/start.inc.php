<?php
include_once dirname(__DIR__) . '/includes/connect.php';
if (!isset($_SESSION['agents']['id']) || !is_numeric($_SESSION['agents']['id'])) {
	$requstURL = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	if(!empty($requstURL)){
		redirect($AGENT_HOST."/index.php?previous_page=".urlencode($_SERVER['REQUEST_URI']));
	}else{
		redirect($AGENT_HOST."/index.php");
	}
  	exit;
}

	if ($SITE_ENV=='Local') {
	  
	} elseif ($SITE_ENV=='Stag') {

	} else {
	  	/*if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") {
		    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		    header('HTTP/1.1 301 Moved Permanently');
		    header('Location: ' . $redirect);
		    exit();
		  }*/
	}

$SELF_MEMBER_ENROL_LINK = $HOST."/quote/".$_SESSION['agents']['user_name'];
?>