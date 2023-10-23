<?php
error_reporting(((E_ALL | E_STRICT) ^ E_NOTICE ^ E_DEPRECATED));
// session_start();
define("ABSOLUTE_PATH", dirname(__DIR__) . "/");
include ABSOLUTE_PATH . "includes/config.inc.php";
include ABSOLUTE_PATH . "includes/Validation.php";
include ABSOLUTE_PATH . "includes/pdo_operation.php";
include ABSOLUTE_PATH . "includes/pdo_operation_read.php";
include ABSOLUTE_PATH . "includes/functions.php";
include ABSOLUTE_PATH . "includes/reporting_function.php";
include ABSOLUTE_PATH . "includes/trigger_mail.php";
include ABSOLUTE_PATH . "includes/generateCache.php";
// include ABSOLUTE_PATH . "includes/upload_paths.php";

$pdo = new PdoOpt();
$rpdo = new PdoOptRead();
$setsql = "SELECT * FROM emailer_setting";
$setrs = $pdo->select($setsql);
$emailer_settings = array();
foreach ($setrs as $key => $setrow) {
  $emailer_settings[$setrow['company_id']][$setrow['field_name']] = stripslashes($setrow['field_value']);
}
?>