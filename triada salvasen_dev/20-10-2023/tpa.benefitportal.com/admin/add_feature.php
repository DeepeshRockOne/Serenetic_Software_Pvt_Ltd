<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$sel = "SELECT id,feature_access from customer where feature_access != '' AND type = 'Agent'";

$res = $pdo->select($sel);

foreach ($res as $key => $value) {
	$access = json_decode($value['feature_access']);
	if(!array_search('30', $access)){
		array_push($access, '30');
	}
	if(!array_search('33', $access)){
		array_push($access, '33');
	}

	$update_params = array(
		'feature_access' => json_encode($access),
	);
	$update_where = array(
		'clause' => 'id = :id',
		'params' => array(
			':id' => $value['id'],
		),
	);
	echo $pdo->update("customer", $update_params, $update_where);

	// pre_print(json_encode($access),false);
}