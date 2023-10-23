<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$response = array();

$member_id = $_REQUEST['member_id'];
$is_on = $_REQUEST['is_on'];

if(!empty($member_id)){
    $updParams = array("is_compliant" => $is_on);
    $updWhere = array(
        'clause' => 'md5(id) = :id',
        'params' => array(':id' => $member_id)
    );
    $pdo->update('customer', $updParams, $updWhere);
    $response['status'] = 'success';
}
echo json_encode($response);
dbConnectionClose();
exit;
?>