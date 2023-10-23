<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
$validate = new Validation();

$agentId = checkIsset($_POST['agent_id']);
$newAgentId = checkIsset($_POST['new_agent_id']);
$customer_id = checkIsset($_POST['customer_id']);
 
$validate->string(array('required' => true, 'field' => 'new_agent_id', 'value' => $newAgentId), array('required' => 'Please select parent agent'));

if($validate->isValid()){

    $customerInfo = $pdo->selectOne("SELECT id,fname,lname,rep_id from customer where md5(id)=:customer_id",array(":customer_id"=>$customer_id));
    // New Agent Code Start
    $selAgent = "SELECT c.id as agentId,c.rep_id as agentDispId
                FROM customer c
                WHERE c.type IN('Agent','Group') AND is_deleted='N' AND c.id=:agentId";
    $resAgent = $pdo->selectOne($selAgent,array(":agentId" => $agentId));

    // New Agent Code Start
    $selNewAgent = "SELECT id,rep_id,level,upline_sponsors FROM customer WHERE type IN('Agent','Group') AND id=:agentId";
    $resNewAgent = $pdo->selectOne($selNewAgent,array(":agentId" => $newAgentId));
    if(!empty($resNewAgent['id'])){

      $newAgentId = $resNewAgent['id'];
      $newAgentLevel = $resNewAgent['level'] + 1;
      $newAgentUplineSponsor = $resNewAgent['upline_sponsors'] . $resNewAgent['id'] . ',';

      $updParams = array(
        "sponsor_id" => $newAgentId,
        "level" => $newAgentLevel,
        "upline_sponsors" => $newAgentUplineSponsor,
      );
      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(':id' => $customerInfo['id']),
    	);
      $pdo->update('customer', $updParams, $updWhere);

      $tmp = array();
      $tmp2 = array();
      $tmp['Parent Agent']= $resAgent['agentDispId'];
      $tmp2['Parent Agent']= $resNewAgent['rep_id'];

        $link = $ADMIN_HOST.'/members_details.php?id='.$customer_id;
        $msg = ' Updated Member ';
        $user_type = 'customer';
        $display_id = $customerInfo['rep_id'];

      $actFeed=$functionsList->generalActivityFeedAgent($tmp,$tmp2,$link,$display_id,$resAgent['agentId'],$user_type,'Agent Updated Parent Agent',$msg);
      $response['status'] = "success";
    }else{
      $response['status'] = "fail";
    }
}else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>