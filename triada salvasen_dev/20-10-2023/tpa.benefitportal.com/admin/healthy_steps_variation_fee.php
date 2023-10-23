<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$response_fee = array();
$response = array();
$fee_ids = isset($_POST['fee_id']) ? $_POST['fee_id'] : '' ;
$healthy_steps = isset($_POST['healthy_steps']) ? $_POST['healthy_steps'] : '' ;
$agent_id = $_POST['agent_id'];

if($fee_ids){
    $response_fee = explode(',', $fee_ids);
}

$validate = new Validation();

if(empty($healthy_steps)){
    $validate->setError('healthy_steps',"Please select any healthy Step.");
}

if(empty($agent_id)){
    $validate->setError('agent_id',"Please select any Agent.");
}

if($validate->isValid()){
    foreach($healthy_steps as $step){
        array_push($response_fee,$step);
    }
    $response['status'] = "success";
    $response['fee_ids'] = implode(',',$response_fee);
}

if(count($validate->getErrors()) > 0){
    $errors = $validate->getErrors();
    $response['status'] = "fail";
    $response['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>