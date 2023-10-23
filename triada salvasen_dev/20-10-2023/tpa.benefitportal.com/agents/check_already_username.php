<?php
include_once dirname(__FILE__) . '/includes/connect.php';

if(isset($_POST["username"])){
	$username=$_POST["username"];
	$agent_id=$_POST["agent_id"];
	$page_builder_id=isset($_POST['page_builder_id'])?$_POST['page_builder_id']:0;
	if (isValidUserName($username,$agent_id,$page_builder_id)) {
		echo 'true';
	}else{
		echo 'false';
	}
}else{
	echo "false";
}
