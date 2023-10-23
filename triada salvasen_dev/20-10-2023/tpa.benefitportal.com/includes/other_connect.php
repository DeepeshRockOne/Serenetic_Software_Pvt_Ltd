<?php session_start();

include dirname(__DIR__) . "/includes/config.inc.php";
include dirname(__DIR__) . "/includes/import_config.php";

if($SITE_ENV == 'Local'){
	error_reporting(E_ALL);
}else{
	error_reporting(0);
}
include dirname(__DIR__) . "/includes/Validation.php";
include dirname(__DIR__) . '/includes/pdo_operation.php';
include dirname(__DIR__) . '/includes/other_pdo_operation.php';
include dirname(__DIR__) . '/includes/pagination.php';
include dirname(__DIR__) . "/includes/functions.php";
include dirname(__DIR__) . "/includes/notification.php";
include dirname(__DIR__) . "/includes/trigger_mail.php";
$pdo = new PdoOpt();
$OtherPdo = new OtherPdoOpt();

include dirname(__DIR__) . "/includes/generateCache.php";


if(!isset($site_access_page)){
	if (!isset($_SESSION['site_access']) || $_SESSION['site_access'] != "YES") {
		//redirect($HOST.'/site_access.php');
	}
}

include dirname(__DIR__) . "/includes/upload_paths.php";


?>