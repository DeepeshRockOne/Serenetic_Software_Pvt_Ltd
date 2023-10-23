<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';

$ws_id = isset($_POST['ws_id']) ? $_POST['ws_id'] : "";
$reason = isset($_POST['reason']) ? $_POST['reason'] : "";
$response = array();
$ws_row = $pdo->selectOne("SELECT id from website_subscriptions where id = :id",array(':id' => $ws_id));

if($ws_row && $reason){

	$upd_term_date_data = array('termination_reason' => $reason);
    $upd_term_date_where = array(
        "clause" => "id=:id",
        "params" => array(":id" => $ws_row['id'])
    );

    $pdo->update("website_subscriptions", $upd_term_date_data, $upd_term_date_where);
    // setNotifySuccess("Termination date removed successfully");
    $response['status'] = "success";
    $response['message'] = "Reason updated successfully";
    
}else{
    $response['status'] = "fail";
    $response['message'] = "Something went wrong";
}
echo json_encode($response);
dbConnectionClose();
exit();
?>