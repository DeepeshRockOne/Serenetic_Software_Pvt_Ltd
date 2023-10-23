<?php
@session_start();

include dirname(__DIR__) . "/../includes/config.inc.php";
include __DIR__ . "/config.inc.php";
if($SITE_ENV == 'Local'){
	error_reporting(E_ALL);
}else{
	error_reporting(0);
}
include dirname(__DIR__) . "/../includes/Validation.php";
include dirname(__DIR__) . '/../includes/pdo_operation.php';
include dirname(__DIR__) . '/../includes/pdo_operation_read.php';
include dirname(__DIR__) . "/../includes/functions.php";
include dirname(__DIR__) . "/../includes/notification.php";
include dirname(__DIR__) . "/../includes/notification_function.php";
include dirname(__DIR__) . "/../includes/trigger_mail.php";
include dirname(__DIR__) . '/includes/pagination.php';
include dirname(__DIR__) . "/includes/functions.php";
include dirname(__DIR__) . "/../UserTimezone.php";
/*
if(!isset($site_access_page)){
	if (!isset($_SESSION['site_access']) || $_SESSION['site_access'] != "YES") {
		redirect($HOST.'/site_access.php');
	}
}
*/
$pdo = new PdoOpt();
$rpdo = new PdoOptRead();
include dirname(__DIR__) . "/../includes/generateCache.php";
include dirname(__DIR__) . "/../includes/upload_paths.php";
if($SITE_ENV == 'Local'){
	include dirname(__DIR__) . "/../includes/generateProductCache.php";
}

$setsql = "SELECT * FROM emailer_setting";
$setrs = $pdo->select($setsql);
$emailer_settings = array();
foreach ($setrs as $key => $setrow) {
	$emailer_settings[$setrow['company_id']][$setrow['field_name']] = stripslashes($setrow['field_value']);
}

/**
 * Cache Code Start
 */
$cache=$pdo->selectOne("SELECT * from cache_management where id=1");
if($cache){
	$cache="?_v=".$cache["version"];
}else{
	$cache="?_v=0";
}
/**
 * Cache code end
 */
?>