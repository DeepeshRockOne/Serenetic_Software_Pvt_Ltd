<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$response = array();
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "";
$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where md5(id)=:id",array(':id' => $ws_id));

if(!empty($ws_row)){
    $extra_params = array();
    $extra_params['location'] = "member_detail";
    $extra_params['portal'] = $location;
    $policySetting->removeTerminationDate($ws_row['id'],$extra_params);

    $response['future_next_billing_date'] = 'N';
    if(strtotime('now') < strtotime($ws_row['next_purchase_date'])){
    	$response['future_next_billing_date'] = 'Y';
    }

    $response['next_billing_date'] = date('m/d/Y',strtotime($ws_row['next_purchase_date']));
    $response['ws_id'] = $ws_id;

    $response['status'] = "success";
    echo json_encode($response);
    dbConnectionClose();
    exit();
}
?>