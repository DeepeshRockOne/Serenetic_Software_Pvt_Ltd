<?php
include_once __DIR__ . '/includes/connect.php';

$group_id = !empty($_GET['group_id']) ? $_GET['group_id'] : 0;
$company_id = !empty($_GET['id']) ? $_GET['id'] : 0;

if(!empty($company_id)){
	$selSql="SELECT * from group_company where id=:id";
	$selRes=$pdo->selectOne($selSql,array(":id"=>$company_id));

	if(!empty($selRes)){
		$group_id = $selRes['group_id'];
		$name = $selRes['name'];
		$address = $selRes['address'];
		$address_2 = $selRes['address_2'];
		$city = $selRes['city'];
		$state = $selRes['state'];
		$zip = $selRes['zip'];
		$ein = $selRes['ein'];
		$location = $selRes['location'];
		$contact = $selRes['contact'];
		$phone = $selRes['phone'];
		$email = $selRes['email'];
		$title = $selRes['title'];
	}
}

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
);

$template = 'groups_add_company.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
