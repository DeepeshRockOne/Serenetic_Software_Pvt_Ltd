<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(27);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Agent Tree';
$breadcrumbes[1]['class'] = 'Active';

//list of types for agentra
$node_boder_class = array();
$node_boder_class['Affiliates'] = 'node-border-primary';
$node_boder_class['Ambassadors'] = 'node-border-child';
$node_boder_class['Group'] = 'node-border-current';
$node_boder_class['Customer'] = 'node-border-danger';
$node_boder_class['Residents'] = 'node-border-success';
$node_boder_class['Agent'] = 'node-border-current';

$agentId =$_GET['agent_id'];
$is_group =isset($_GET['is_group']) ? $_GET['is_group'] : 'N';


  $selAgent ="SELECT c.id, CONCAT(c.fname,' ', c.lname) as agentName,c.business_name as cbusiness_name, c.rep_id, c.email, c.type,cs.agent_coded_level,cs.agent_coded_id, c.status, c.sponsor_id, CONCAT(s.fname,' ',s.lname) as sponsor_name,s.business_name as sbusiness_name,s.rep_id as s_rep_id,s.status as sstatus,scs.agent_coded_level as sponsor_coded_level,scs.agent_coded_id as sponsor_coded_id
        FROM customer c  
        JOIN customer_settings cs ON (cs.customer_id=c.id)
        LEFT JOIN customer s ON (s.id = c.sponsor_id) 
        LEFT JOIN customer_settings scs ON (scs.customer_id=s.id)
        WHERE md5(c.id) =:agentId";
  $paramsAgent =array(":agentId"=>$agentId);
  $resAgent = $pdo->selectOne($selAgent,$paramsAgent);
 
  if(!empty($resAgent)){
    
    $node_id=$resAgent['id'];

    $parentAgentId = checkIsset($resAgent['sponsor_id']);
    $currentAgentId = checkIsset($resAgent['id']);

    // get new parent agents list
    $newAgent = array();
    $agentCodedId = checkIsset($resAgent['agent_coded_id']);
    $agentSql = "SELECT c.id as agentId,c.rep_id as agentDispId,c.email as agentEmail,concat(c.fname,' ', c.lname) as agentName 
                FROM customer c
                JOIN customer_settings cs on (cs.customer_id=c.id)
                WHERE c.type='Agent' AND c.status='Active' AND c.is_deleted='N' AND cs.agent_coded_level!='LOA' AND cs.agent_coded_id >= :codedId AND c.id!=:sponsorId AND c.id!=:agentId AND c.sponsor_id!=:agentId";
    $newAgent = $pdo->select($agentSql,array(":codedId" => $agentCodedId,":sponsorId" => $parentAgentId,":agentId"=>$currentAgentId));

    $cbusiness_name = checkIsset($resAgent["cbusiness_name"]);
    $sbusiness_name = checkIsset($resAgent["sbusiness_name"]);
    $coded_level = isset($agentCodedRes[$resAgent['agent_coded_id']]['level_heading']) ? $agentCodedRes[$resAgent['agent_coded_id']]['level_heading'] : '';
    $sponsor_coded_level = isset($agentCodedRes[$resAgent['sponsor_coded_id']]['level_heading'])?$agentCodedRes[$resAgent['sponsor_coded_id']]['level_heading']  : '';
    
    $business_name = empty($cbusiness_name) ? $resAgent["agentName"] : $cbusiness_name;
    $sponsor_name = empty($sbusiness_name) ? $resAgent["sponsor_name"] : $sbusiness_name;

    if($parentAgentId==1)
    {
      $sponsor_coded_level='Root';
      $sponsor_name = 'House';    
    }
    if($parentAgentId==0){
      $business_name = 'House';
      $coded_level='Root';
    }

    $encAgentId = md5($resAgent["id"]);
    $encSponsId = md5($resAgent["sponsor_id"]);

  $sponsorData = "";
  if($parentAgentId!=0){
    $sponsorData="{id:  \"".$parentAgentId."\",name: \"<div class='node_label node-border-parent'><a href='agent_detail_v1.php?id={$encSponsId}' target='_blank'><h4><i class='fa fa-external-link'></i>" . stripslashes($sponsor_name) . "</h4></a><p> ".$resAgent['s_rep_id']." </p><h5 " . (strtolower($resAgent['sstatus']) != "active" ? "class='text-inactive'" : "") . ">" . strtoupper($sponsor_coded_level) . "</h5><p>".checkIsset($icons)."</p></div>\",data: {user_id: \"".$resAgent["sponsor_id"]."_". $resAgent["id"] . "\", Name: \"" . stripslashes($sponsor_name) . "\",ID: \"" . $resAgent["s_rep_id"] . "\",Email: \"" . $resAgent["s_rep_id"] . "\",Type: \"" . $sponsor_coded_level . "\",Status: \"" . $resAgent["sstatus"] . "\"},children: [] },";
  }
  $link = $resAgent['type'] == 'Group' ? 'groups_details.php?id='.$encAgentId : 'agent_detail_v1.php?id='.$encAgentId;
  $jsonData = $sponsorData."{id:  \"".$resAgent['id']."\",name: \"<div class='node_label " . $node_boder_class[$resAgent['type']] . "'><a href='{$link}' target='_blank'><h4><i class='fa fa-external-link'></i>" . stripslashes($business_name) . "</h4></a><p> ".$resAgent['rep_id']." </p><h5 " . (strtolower($resAgent['status']) != "active" ? "class='text-inactive'" : "") . ">" . strtoupper($coded_level) . "</h5><p>".checkIsset($icons)."</p></div>\",data: {user_id: \"" . $resAgent["id"] . "\", Name: \"" . stripslashes($business_name) . "\",ID: \"" . $resAgent["rep_id"] . "\",Email: \"" . $resAgent["rep_id"] . "\",Type: \"" . $coded_level . "\",Status: \"" . $resAgent["status"] . "\",Sponsor: \"" . $resAgent["sponsor_name"] . "\"},children: [] }";

  }else{
    if($is_group == 'Y'){
      redirect("groups_listing.php",true);
    }else{
      redirect("agent_listing.php",true);
    }
  }


$exStylesheets = array('thirdparty/spacetree/base.css','thirdparty/spacetree/Spacetree.css', 'thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css'.$cache);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array('thirdparty/spacetree/jit-yc.js');

$template = 'agent_tree_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>

