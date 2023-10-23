<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$sending_data = http_build_query($_POST);

$sending_data .= "&admin_id=".$_SESSION['admin']['id'];
$sending_data .= "&admin_display_id=".$_SESSION['admin']['display_id'];
$sending_data .= "&admin_name=".$_SESSION['admin']['name'];

if(empty(checkIsset($_POST['cm_product']))){
    $validate->string(array("required"=>true,'field'=>'cm_product','value'=>$sending_data['cm_product']),array("required"=>"Please select any commission rule for product."));
}  

$script_running_check = $pdo->selectOne("SELECT is_running FROM system_scripts WHERE `script_code` = 'agent_product_rule'");
$script_running = $script_running_check['is_running'];
if($script_running == 'Y'){
    $validate->setError('script_running',"Agent's LOA/Downline update process is running background.");
}

if($validate->isValid()){
    $url = $HOST.'/cron_scripts/agent_prd_rule_edit.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $sending_data);
    curl_exec($ch);
    curl_close($ch);
    $response['status'] = 'success';
    setNotifySuccess("Agent's LOA/Downline update process is running background.");
}else{
    $errors = $validate->getErrors();
    $response['status'] = 'fail';
    $response['errors'] = $errors;
}

header("Content-type: apllication/json");
echo json_encode($response);
dbConnectionClose();
exit;
?>