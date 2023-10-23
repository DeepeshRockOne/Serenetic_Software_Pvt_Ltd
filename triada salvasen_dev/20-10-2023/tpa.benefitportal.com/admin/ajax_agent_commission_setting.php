<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$response = array();

$type = $_REQUEST['type'];
$agent_id = $_REQUEST['agent_id'];
$is_on = $_REQUEST['is_on'];

$REAL_IP_ADDRESS = get_real_ipaddress();
$agentRes = array();
if(!empty($agent_id)){
    $selAgent =  "SELECT a.id as agentId,a.rep_id as agentDispId,cs.id as settingId,cs.advance_on,cs.graded_on
                    FROM customer a 
                    JOIN customer_settings cs ON(a.id=cs.customer_id)
                    WHERE a.type='Agent' AND a.is_deleted='N' AND a.id=:agentId";
    $agentRes = $pdo->selectOne($selAgent,array(":agentId" =>$agent_id));
}

if(!empty($agentRes)){
    if($type == 'advance'){
        $updParams = array("advance_on" => $is_on);
        $updWhere = array(
            'clause' => 'id = :id',
            'params' => array(':id' => $agentRes['settingId'])
        );
        $pdo->update('customer_settings', $updParams, $updWhere);

        $historyData = array(
                    'agent_id' => $agent_id,
                    'is_on' => $is_on,
                    'admin_id' => checkIsset($_SESSION['admin']['id']),
                    'entity_action' => ($is_on == "Y"?"Advanced Commissions ON":"Advanced Commissions OFF"),
                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                    'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                );
        $pdo->insert("advance_comm_rule_history", $historyData);

        if($is_on == 'Y' && $agentRes["graded_on"] == 'Y'){
            $gradedSetting = 'N';

            $updParams = array("graded_on" => $gradedSetting);
            $updWhere = array(
                'clause' => 'id = :id',
                'params' => array(':id' => $agentRes['settingId'])
            );
            $pdo->update('customer_settings', $updParams, $updWhere);

            $historyData = array(
                        'agent_id' => $agent_id,
                        'is_on' => $gradedSetting,
                        'admin_id' => checkIsset($_SESSION['admin']['id']),
                        'entity_action' => ($gradedSetting == "Y"?"Graded Commissions ON":"Graded Commissions OFF"),
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                        'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    );
            $pdo->insert("graded_comm_rule_history", $historyData);
        }
    }else if($type == 'graded'){
        $updParams = array("graded_on" => $is_on);
        $updWhere = array(
            'clause' => 'id = :id',
            'params' => array(':id' => $agentRes['settingId'])
        );
        $pdo->update('customer_settings', $updParams, $updWhere);

        $historyData = array(
                    'agent_id' => $agent_id,
                    'is_on' => $is_on,
                    'admin_id' => checkIsset($_SESSION['admin']['id']),
                    'entity_action' => ($is_on == "Y"?"Graded Commissions ON":"Graded Commissions OFF"),
                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                    'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                );
        $pdo->insert("graded_comm_rule_history", $historyData);

        if($is_on == 'Y' && $agentRes["advance_on"] == 'Y'){
            $advanceSetting = 'N';

            $updParams = array("advance_on" => $advanceSetting);
            $updWhere = array(
                'clause' => 'id = :id',
                'params' => array(':id' => $agentRes['settingId'])
            );
            $pdo->update('customer_settings', $updParams, $updWhere);

            $historyData = array(
                        'agent_id' => $agent_id,
                        'is_on' => $advanceSetting,
                        'admin_id' => checkIsset($_SESSION['admin']['id']),
                        'entity_action' => ($advanceSetting == "Y"?"Advance Commissions ON":"Advance Commissions OFF"),
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                        'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    );
            $pdo->insert("advance_comm_rule_history", $historyData);
        }
    }
    $response['status'] = 'success';
}else{
    $response['status'] = 'fail';
}


if($response['status'] == 'success'){
	setNotifySuccess(ucfirst($type)." commissions settings changed successfully");
}else{
	setNotifyError("Something went wrong");
}
echo json_encode($response);
dbConnectionClose();
exit;
?>