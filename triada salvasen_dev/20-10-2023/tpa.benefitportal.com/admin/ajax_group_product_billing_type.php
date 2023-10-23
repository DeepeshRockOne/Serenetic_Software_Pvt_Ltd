<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$response = array();
$id = $_REQUEST['id'];
$billing_type = $_REQUEST['billing_type'];
$agentRes = array();

if(!empty($id)){
    $selAgent =  "SELECT a.id as agentId,a.rep_id as agentDispId,apr.id as ruleId 
                    FROM customer a 
                    JOIN agent_product_rule apr ON(a.id=apr.agent_id)
                    WHERE a.type='Group' AND a.is_deleted='N' AND apr.id=:id";
    $agentRes = $pdo->selectOne($selAgent,array(":id" =>$id));
}


if(!empty($agentRes['ruleId'])){
    $updParams = array("product_billing_type" => $billing_type);
    $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(':id' => $agentRes['ruleId'])
    );
    $pdo->update('agent_product_rule', $updParams, $updWhere);

    $response['status'] = 'success';
}else{
    $response['status'] = 'fail';
}

if($response['status'] == 'success'){
    $response['msg']= "Product Billing Type changed successfully";
}else{
    $response['msg']= "Oops!!! Product Billing Type can not change";
}
echo json_encode($response);
dbConnectionClose();
exit;
?>