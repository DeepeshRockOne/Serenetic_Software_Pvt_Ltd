<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$productId = $_GET["productId"];

// fetch product details
$selPrd = "SELECT id,name as prdName,product_code as prdCode FROM prd_main WHERE md5(id)=:prdId";
$resPrd = $pdo->selectOne($selPrd,array(":prdId" =>$productId));

// fetch all commission rules for product
$selCommRule = "SELECT cr.id,cr.parent_rule_id,cr.rule_code,p.name as prdName,p.product_code as prdCode FROM commission_rule cr JOIN prd_main p ON(cr.product_id=p.id) WHERE (md5(cr.product_id)=:productId || md5(cr.parent_product_id)=:productId) AND cr.is_deleted='N'";
$whereRule = array(":productId" => $productId);
$ruleRows = $pdo->select($selCommRule, $whereRule);

// fetch agents do not have assigned product
$selAgent = "SELECT c.id,CONCAT(fname,' ',lname) as agentName,c.rep_id as agentDispId 
  			FROM customer c 
            WHERE c.status='Active' AND c.is_deleted='N' AND c.type='Agent' 
            AND c.id NOT IN(SELECT agent_id FROM agent_product_rule WHERE is_deleted='N' AND md5(product_id)=:prdId)";
$agentRows = $pdo->select($selAgent,array(":prdId" => $productId));

$selAgent = "SELECT c.id,CONCAT(fname,' ',lname) AS agentName,c.rep_id AS agentDispId,c.status,c.type
			FROM customer c
			LEFT JOIN agent_product_rule apr ON (apr.agent_id = c.id AND md5(apr.product_id)=:prdId)
			WHERE c.is_deleted='N' AND c.type='Agent' AND apr.agent_id IS NULL GROUP BY c.id";
$agentRows = $pdo->select($selAgent,array(":prdId" => $productId));



$selAssignedAgent = "SELECT c.id,CONCAT(fname,' ',lname) as agentName,c.rep_id as agentDispId,a.status as productStatus 
  			FROM customer c 
  			JOIN agent_product_rule a ON(c.id=a.agent_id  AND a.is_deleted='N')
  			JOIN commission_rule cr ON(cr.product_id=a.product_id AND cr.is_deleted='N')
            WHERE c.is_deleted='N' AND c.type='Agent' AND md5(a.product_id)=:prdId GROUP BY c.id";
$assignedAgentRows = $pdo->select($selAssignedAgent,array(":prdId" => $productId));



/* ------- Update trigger status code start --------- */
  if(isset($_GET['action']) && ($_GET['action'] == 'updStatus')){
    $agentUpdStatus = checkIsset($_GET['agentUpdStatus']);
    $updAgents = checkIsset($_GET['updAgents'],'arr');
    $prdId = checkIsset($_GET['productId']);
    $res = array();
    if (!empty($agentUpdStatus) && !empty($updAgents)){
      foreach ($updAgents as $key => $agentId) {
        $checkPrdSql = "SELECT apr.id,apr.status as agentPrdStatus,a.rep_id as agentDispId,p.product_code as prdDispId 
                      FROM agent_product_rule apr
                      JOIN customer a ON(apr.agent_id=a.id)
                      JOIN prd_main p ON(apr.product_id=p.id)
                      WHERE apr.agent_id=:agentId AND md5(apr.product_id)=:prdId AND apr.is_deleted='N'";
        $checkPrdRow = $pdo->selectOne($checkPrdSql, array(":agentId" => $agentId,":prdId" => $prdId));
        
        if (!empty($checkPrdRow['id'])) {
          $updateSql = array("status" => $agentUpdStatus,'updated_at' => 'msqlfunc_NOW()');
          $updateWhere = array("clause" => "agent_id=:agentId AND md5(product_id)=:prdId", "params" => array(":agentId" => $agentId,":prdId" => $prdId));
          $pdo->update("agent_product_rule", $updateSql, $updateWhere);

          $tmp = array();
          $tmp2 = array();
          $tmp['Product Status']=$checkPrdRow['agentPrdStatus'];
          $tmp2['Product Status']=$agentUpdStatus;

          $link = $ADMIN_HOST.'/add_assign_agents.php?productId='.$prdId;

          $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,$checkPrdRow['prdDispId'],$checkPrdRow['id'],'agent_product_rule','Admin Updated Agent Product Rule','Updated Agent('.$checkPrdRow['agentDispId'].') Product');
        }

        $res['status'] = "success";
        $res['msg'] = "Agent Product status updated successfully";
      }
    }else{
        $res['status'] = 'fail';
        $res['msg'] = 'something went wrong';
    }
    echo json_encode($res);
    exit();
  }
/* ------- Update trigger status code ends ---------- */

 

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache, 'thirdparty/bootstrap-tables/css/bootstrap-table.min.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 'thirdparty/bootstrap-tables/js/bootstrap-table.min.js');

$template = 'add_assign_agents.inc.php';
include_once 'layout/iframe.layout.php';
?>