<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(1);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Agent Tree';
$breadcrumbes[1]['class'] = 'Active';

//list of types for agentra
$node_boder_class = array();
$node_boder_class['Affiliates'] = 'node-border-primary';
$node_boder_class['Ambassadors'] = 'node-border-child';
$node_boder_class['Group'] = 'node-border-current';
$node_boder_class['Customer'] = 'node-border-black';
$node_boder_class['Residents'] = 'node-border-success';
$node_boder_class['Agent'] = 'node-border-current';

// $agentId =$_GET['agent_id'];
$member_id = $_GET['member_id'];

    $selMember ="SELECT c.id, CONCAT(c.fname,' ', c.lname) as memberName, c.rep_id,if(c.type='customer','Member','')  as type,c.status,CONCAT(s.upline_sponsors,s.id,',') as upline_sponsors,c.sponsor_id,IF(s.type='Group',s.business_name,CONCAT(s.fname,' ',s.lname)) as sponsor_name,s.rep_id as s_rep_id,s.id as parent_id,s.type as sponsor_type
    FROM customer c  
    LEFT JOIN customer s ON(s.id=c.sponsor_id)
    WHERE md5(c.id) =:agentId and c.is_deleted='N'";
    $paramsMember =array(":agentId"=>$member_id);
    $resMember = $pdo->selectOne($selMember,$paramsMember);
    $sponsors = substr($resMember['upline_sponsors'],1,-1);    
    if(empty($resMember['id'])){
        redirect("member_listing.php",true);
    }

    // get new parent agents list
    $newAgent = array();
    $agentCodedId = checkIsset($resMember['agent_coded_id']);
    $parentAgentId =  checkIsset($resMember['parent_id']);

    $agentSql = "SELECT c.id as agentId,c.rep_id as agentDispId,c.email as agentEmail,IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as agentName ,agent_coded_id
                FROM customer c
                JOIN customer_settings cs on (cs.customer_id=c.id)
                WHERE c.type='".$resMember['sponsor_type']."' AND c.status IN('Active','Suspended') AND c.is_deleted='N' AND c.id!=:sponsorId";
    $newAgent = $pdo->select($agentSql,array(":sponsorId" => $parentAgentId));
  
    if(!empty($sponsors) && $sponsors!=''){
      $selAgent ="SELECT c.id, CONCAT(c.fname,' ', c.lname) as agentName,c.business_name as cbusiness_name, c.rep_id, c.email, c.type,cs.agent_coded_level,cs.agent_coded_id, c.status, c.sponsor_id
      FROM customer c  
      LEFT JOIN customer_settings cs ON (cs.customer_id=c.id)
      WHERE c.is_deleted='N' AND c.id IN($sponsors , ".$resMember['id']." ) ORDER BY FIELD(c.id,$sponsors , ".$resMember['id'].")";
      $resAgent = $pdo->select($selAgent);
        if(!empty($resAgent)){
          
          $sponsorparent  = $sponsorData = array();
          $existAgent = '';
          foreach($resAgent as $key => $agent){

            $cbusiness_name = checkIsset($agent["cbusiness_name"]);
            $sbusiness_name = checkIsset($agent["sbusiness_name"]);
            $coded_level = checkIsset($agentCodedRes[$agent['agent_coded_id']]['level_heading']);
            
            $business_name = empty($cbusiness_name) ? $agent["agentName"] : $cbusiness_name;
        
            if($agent['id']==1)
            {
              $coded_level ='Root';
              $business_name = 'House Agent';
            }
            $pr_type = $agent['type'] == 'Customer' ? 'Member' : '';
            $link_url = '';
            if($agent['type'] == 'Customer') 
              $link_url =  'members_details.php?id='.md5($agent['id']);
            elseif($agent['type'] == 'Agent') 
              $link_url = 'agent_detail_v1.php?id='.md5($agent['id']);
            elseif($agent['type'] == 'Group')
              $link_url = 'groups_details.php?id='.md5($agent['id']);

            if($agent['id'] != 1){
              $sponsorArr['child_node'.$agent['id']] = array(
                "parent"=>!empty($existAgent) ? $existAgent  : "parent_node",
                "HTMLclass"=> "node_label ".$node_boder_class[$agent['type']],
                "innerHTML" => "
                    <a href='".$link_url."' target='_blank'>
                      <h4><i class='fa fa-external-link'></i>" . stripslashes($business_name) . "&nbsp;&nbsp;&nbsp;&nbsp;".$pr_type."</h4>
                    </a>
                    <p> ".$agent['rep_id']." </p>
                    <h5 " . (strtolower($agent['status']) != "active" ? "class='text-inactive'" : "") . ">" . strtoupper($coded_level) . "</h5>
                    <p>".checkIsset($icons)."</p>",
              );
              $existAgent = 'child_node'.$agent['id'] ;
            }else{
              $sponsorparent = array(
                  "HTMLclass"=> "node_label node-border-parent",
                  "innerHTML" => "
                  <a href='agent_detail_v1.php?id=".md5($agent['id'])."' target='_blank'>
                    <h4><i class='fa fa-external-link'></i>" . stripslashes($business_name) . "</h4>
                  </a>
                  <p> ".$agent['rep_id']." </p>
                  <h5 " . (strtolower($agent['status']) != "active" ? "class='text-inactive'" : "") . ">" . strtoupper($coded_level) . "</h5>
                  <p>".checkIsset($icons)."</p>",
                );
            }
            
            }
        }else{
          redirect("member_listing.php",true);
        }
        
    }
$exStylesheets = array('thirdparty/spacetree/base.css','thirdparty/treant-js-master/Treant.css');
$exJs = array('thirdparty/treant-js-master/vendor/raphael.js','thirdparty/treant-js-master/Treant.js');

$template = 'member_tree_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>

