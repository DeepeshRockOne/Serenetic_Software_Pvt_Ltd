<?php 
	session_start();
	include dirname(__DIR__) . "/includes/Validation.php";
	include dirname(__DIR__) . "/includes/config.inc.php";
	include dirname(__DIR__) . '/includes/pdo_operation.php';
	$pdo = new PdoOpt();
	$validation = new Validation();
