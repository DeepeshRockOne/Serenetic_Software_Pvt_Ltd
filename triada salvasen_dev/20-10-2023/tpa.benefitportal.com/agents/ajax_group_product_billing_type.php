<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$response = array();
$id = $_REQUEST['id'];
$billing_type = $_REQUEST['billing_type'];
$agentRes = array();

if(!empty($id)){
    $selAgent =  "SELECT apr.agent_id,a.rep_id,CONCAT(a.fname,' ',a.lname) as agent_name,apr.id as ruleId,apr.product_billing_type,pm.product_code,pm.name as product_name 
                    FROM customer a 
                    JOIN agent_product_rule apr ON(a.id=apr.agent_id)
                    JOIN prd_main pm ON(pm.id=apr.product_id)
                    WHERE a.type='Group' AND a.is_deleted='N' AND apr.id=:id";
    $agentRes = $pdo->selectOne($selAgent,array(":id" =>$id));
}

if(!empty($agentRes['ruleId'])){
    $updParams = array("product_billing_type" => $billing_type);
    $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(':id' => $agentRes['ruleId'])
    );
    $pdo->update('agent_product_rule',$updParams,$updWhere);

    $description =array();
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
            'title' => $_SESSION['agents']['rep_id'],
        ),
        'ac_message_1' =>'  Updated product billing type for Group : '.$agentRes['agent_name'].' (',
        'ac_red_2'=>array(
            'href'=> 'groups_details.php?id='.md5($agentRes['agent_id']),
            'title'=> $agentRes['rep_id'],
        ),
        'ac_message_2' =>')',
    );

    $description['key_value'] = array(
        "desc_arr" => array(
            "billing Type" => ' updated from '.ucwords(str_replace('_',' ',$agentRes['product_billing_type'])).' to '.ucwords(str_replace('_',' ',$billing_type)),
        ),
    );
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['agents']['id'],'Agent',$agentRes['agent_id'],'Group','Product Billing Type Updated.',"","",$desc);

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