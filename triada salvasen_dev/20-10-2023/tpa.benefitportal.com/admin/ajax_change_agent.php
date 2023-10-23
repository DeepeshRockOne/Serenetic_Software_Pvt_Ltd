<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
$validate = new Validation();

$agentId = checkIsset($_POST['agent_id']);
$newAgentId = checkIsset($_POST['new_agent_id']);
 
$validate->string(array('required' => true, 'field' => 'new_agent_id', 'value' => $newAgentId), array('required' => 'Please select parent agent'));

if($validate->isValid()){

    // New Agent Code Start
    $selAgent = "SELECT c.id as agentId,c.rep_id as agentDispId,s.rep_id as sponsorDispId,c.type  
                FROM customer c
                JOIN customer s ON(c.sponsor_id=s.id AND s.type in ('Agent','Group'))
                WHERE c.type IN('Agent','Group') AND c.id=:agentId";
    $resAgent = $pdo->selectOne($selAgent,array(":agentId" => $agentId));

    // New Agent Code Start
    $selNewAgent = "SELECT id,rep_id,level,upline_sponsors FROM customer WHERE type='Agent' AND id=:agentId AND id!=:oldAgentID";
    $resNewAgent = $pdo->selectOne($selNewAgent,array(":agentId" => $newAgentId,":oldAgentID"=>$agentId));
    if(!empty($resNewAgent['id'])){

      $newAgentId = $resNewAgent['id'];
      $newAgentLevel = $resNewAgent['level'] + 1;
      $newAgentUplineSponsor = $resNewAgent['upline_sponsors'] . $newAgentId . ',';

      $updParams = array(
	      "sponsor_id" => $newAgentId,
        "level" => $newAgentLevel,
        "upline_sponsors" => $newAgentUplineSponsor,
  		);
 
      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(':id' => $agentId),
    	);
      $pdo->update('customer', $updParams, $updWhere);

      // Link Agent to Their Agency
      $agencyId = $functionsList->getAgencyId($agentId);
      $customer_settings = array();
      $customer_settings["agency_id"] = $agencyId;
      $agentSettings=$functionsList->addCustomerSettings($customer_settings,$agentId);


      $tmp = array();
      $tmp2 = array();
      $tmp['Parent Agent']= $resAgent['sponsorDispId'];
      $tmp2['Parent Agent']= $resNewAgent['rep_id'];

      $link = $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($resAgent['agentId']);

      $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,$resAgent['agentDispId'],$resAgent['agentId'],'customer','Admin Updated Parent Agent','Updated Agent');

      if($resAgent["type"] == "Group"){
        $functionsList->updateGroupMembers($agentId);
      }else{
        $functionsList->updateDownlineGroup($agentId);
        $functionsList->updateDownlineMember($agentId);
      }

      $functionsList->updateAgentDownline($agentId);
      $response['status'] = "success";
    }else{
      $response['status'] = "fail";
      $response['message'] = "Something went wrong";
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