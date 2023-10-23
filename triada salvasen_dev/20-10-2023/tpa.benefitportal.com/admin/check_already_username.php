<?php
include_once dirname(__FILE__) . '/includes/connect.php';

if(isset($_POST["username"])){
	$username=$_POST["username"];
	$group_id=$_POST["group_id"];
	$page_builder_id=isset($_POST['page_builder_id'])?$_POST['page_builder_id']:0;
	if (isValidUserName($username,$group_id,$page_builder_id)) {
		echo 'true';
	}else{
		echo 'false';
	}
}else{
	echo "false";
}
