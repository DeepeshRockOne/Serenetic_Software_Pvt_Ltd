<?php 
	session_start();
	include dirname(__DIR__) . "/includes/Validation.php";
	include dirname(__DIR__) . "/includes/config.inc.php";
	include dirname(__DIR__) . '/includes/pdo_operation.php';
	$pdo = new PdoOpt();
	$validation = new Validation();

	$db_host = "localhost";
	$db_name = "deepesh_initial_tasks";
	$db_username = "root";
	$db_password = "";

	//Set DSN (Data Source Name)
	$dsn = 'mysql:host='.$db_host.';dbname='.$db_name.';charset=utf8';
	$db_options = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	);

	try {
		$pdo_conn = new PDO($dsn, $db_username, $db_password, $db_options);
	} catch (Exception $e) {
		echo $e->getMessage();
	}
