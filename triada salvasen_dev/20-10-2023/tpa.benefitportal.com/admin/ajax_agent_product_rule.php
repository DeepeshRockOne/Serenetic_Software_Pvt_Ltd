<?php
include_once 'layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
$res = array();
$validate = new Validation();
// pre_print($_REQUEST);


$productId = checkIsset($_POST['productId']);
$commissionRuleId = checkIsset($_POST['commission_rule_id']);
$productStatus = checkIsset($_POST['product_status']);
$assignTo = checkIsset($_POST['assign_to']);
$agents = checkIsset($_POST['agents'],'arr');
$fullDownline = checkIsset($_POST['full_downline'],'arr');
$loaOnly = checkIsset($_POST['loa_only'],'arr');



$validate->string(array('required' => true, 'field' => 'commission_rule_id', 'value' => $productId), array('required' => 'Product is required'));

$validate->string(array('required' => true, 'field' => 'commission_rule_id', 'value' => $commissionRuleId), array('required' => 'Please select commission rule'));

$validate->string(array('required' =>true, 'field' => 'product_status', 'value' => $productStatus), array('required' => 'Please select product status'));

$validate->string(array('required' => true, 'field' => 'assign_to', 'value' => $assignTo), array('required' => 'Please select option'));

if($assignTo == "specific_agents"){
	if (empty($agents)) {
		$validate->setError("agents","Please select Agent");
	}
}

if ($validate->isValid()) {

	$prdId = getname('prd_main',$productId,'id','md5(id)');

	$agentIds = array();

	if($assignTo == 'all_agents'){
		$resAgents = $pdo->selectOne("SELECT group_concat(id) as ids FROM customer WHERE type='Agent' AND status='Active' AND is_deleted='N'");
		
		$agentArr = !empty($resAgents['ids']) ? explode(",", $resAgents['ids']) : array();
   		if(!empty($agentArr)){
   			foreach ($agentArr as $id) {
   				$agentIds[] = $id;
   			}
    	}
	}else if($assignTo == 'specific_agents'){
		if(!empty($agents)){
		    foreach($agents as $key => $agentId) {
		    	$agentIds[] = $agentId;
		        if(isset($fullDownline[$agentId]) && $fullDownline[$agentId]=='Y'){
		       		$resDownlineAgents = $pdo->selectOne("SELECT group_concat(id) as ids FROM customer WHERE upline_sponsors LIKE CONCAT('%,',".$agentId.",',%') AND type='Agent' AND is_deleted='N'");
		       		
		       		$downlineArr = !empty($resDownlineAgents['ids']) ? explode(",", $resDownlineAgents['ids']) : array();
		       		if(!empty($downlineArr)){
		       			foreach ($downlineArr as $id) {
		       				$agentIds[] = $id;
		       			}
		        	}
		        }else if(isset($loaOnly[$agentId]) && $loaOnly[$agentId]=='Y'){
		        	$resLoaAgents = $pdo->selectOne("SELECT group_concat(c.id) as ids FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE agent_coded_level=:type AND sponsor_id=:sponsor_id AND type='Agent' AND is_deleted='N' ",array(":type"=>'LOA',":sponsor_id"=>$agentId));
		        	$loaArr = !empty($resLoaAgents['ids']) ? explode(",", $resLoaAgents['ids']) : array();
		       		if(!empty($loaArr)){
		       			foreach ($loaArr as $id) {
		       				$agentIds[] = $id;
		       			}
		        	}
		        }
		    }
		}
	}
	
	$agentIds = array_unique($agentIds);
	
			
	if(!empty($agentIds)){
		foreach ($agentIds as $key => $agentId) {
			//checking for existing product assignments and rules
			$checkPrdSql = "SELECT id FROM agent_product_rule WHERE agent_id=:agentId AND product_id=:prdId AND is_deleted='N'";
			$checkPrdRow = $pdo->selectOne($checkPrdSql, array(":agentId" => $agentId,":prdId" => $prdId));
			if ($checkPrdRow) {
				//deleting all previous assigned rules
				$updateSql = array("is_deleted" => 'Y','updated_at' => 'msqlfunc_NOW()');
				$updateWhere = array("clause" => "agent_id=:agentId AND product_id=:prdId", "params" => array(":agentId" => $agentId,":prdId" => $prdId));
				$pdo->update("agent_product_rule", $updateSql, $updateWhere);
			}

			$insertProductRule = array(
				'agent_id' => $agentId,
				'product_id' => $prdId,
				'commission_rule_id'=>$commissionRuleId,
				'admin_id'=> isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0,
				'status' => $productStatus,
			);
			$insId = $pdo->insert("agent_product_rule",$insertProductRule);
			$assignCommissionRule = $functionsList->assignCommissionRuleToAgent($agentId,$prdId,$commissionRuleId);

			/* ------ Activity Code Start -------- */
			$agentDispId = getname('customer',$agentId,'rep_id','id');
			$prdDispId = getname('prd_main',$prdId,'product_code','id');
			$commRule = getname('commission_rule',$commissionRuleId,'rule_code','id');
              $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='. $_SESSION['admin']['id'],
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' added Product ('. $prdDispId .') on Agent',
                'ac_red_2'=>array(
                  'title'=>$agentDispId,
                ),
              ); 
              activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insId, 'agent_product_rule','Admin Added Agent Product Rule', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
            /* ------ Activity Code Ends -------- */
		}
	}
	$res["status"] = "success";
	$res["msg"] = "Commission rule assigned successfully";
}else{
	$errors = $validate->getErrors();
	$res["status"] = "fail";
	$res["errors"] = $errors;
}

echo json_encode($res);
dbConnectionClose();
exit;
?>