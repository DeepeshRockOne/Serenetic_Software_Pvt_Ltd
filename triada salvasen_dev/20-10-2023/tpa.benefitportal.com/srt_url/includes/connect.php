<?php
session_start();
include dirname(__DIR__) . "/../includes/config.inc.php";
if($SITE_ENV == 'Local'){
	error_reporting(E_ALL);
}else{
	error_reporting(0);
}
include dirname(__DIR__) . '/../includes/pdo_operation.php';
include dirname(__DIR__) . "/includes/functions.php";
global $pdo;
$pdo = new PdoOpt();
?>