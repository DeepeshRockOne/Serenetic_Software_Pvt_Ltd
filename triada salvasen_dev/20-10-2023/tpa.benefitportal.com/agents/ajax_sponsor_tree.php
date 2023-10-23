<?php
include_once (__DIR__) . '/layout/start.inc.php';

$node_boder_class = array();
//list of types for agentra
$node_boder_class['Affiliates'] = 'node-border-primary';
$node_boder_class['Ambassadors'] = 'node-border-child';
$node_boder_class['Group'] = 'node-border-success';
$node_boder_class['Customer'] = 'node-border-danger';
$node_boder_class['Residents'] = 'node-border-success';
$node_boder_class['Agent'] = 'node-border-child';

if (isset($_GET['userId'])) {
  $nodeData = explode("_",$_GET['userId']);
  $incr="";
  if(isset($nodeData[1])){
    $childNode=$nodeData[1];
    $incr=" AND c.id=".$childNode;
  }
  $node_id = $nodeData[0];

  $sql = "SELECT c.id, CONCAT(c.fname,' ', c.lname) as member_name,c.business_name, c.rep_id, c.email, c.type,cs.agent_coded_level,cs.agent_coded_id, c.status,CONCAT(s.fname,' ',s.lname) as sponsor_name 
    FROM customer c
    JOIN customer_settings cs ON (cs.customer_id=c.id)
    LEFT JOIN customer s ON s.id = c.sponsor_id
    WHERE c.type IN ('Agent') AND c.is_deleted='N' AND c.sponsor_id =:id $incr";
  $params=array(":id"=>$node_id);  
  $rows = $pdo->select($sql,$params);
  
  $json_sub_tree = "";
  foreach ($rows as $key => $sub_row) {
    $business_name=$sub_row["business_name"];
    $coded_level=isset($agentCodedRes[$sub_row['agent_coded_id']]['level_heading']) ? $agentCodedRes[$sub_row['agent_coded_id']]['level_heading'] : '';
    if($sub_row["business_name"]=='' || empty($sub_row["business_name"]))
      $business_name=$sub_row["member_name"];
    
    if($sub_row['id']==1)
      $coded_level='Root';
    $icons="";
    
     $sql_sub = "SELECT c.id, CONCAT(c.fname,' ', c.lname) as member_name,c.business_name, c.rep_id, c.email, c.type,cs.agent_coded_level, c.status,CONCAT(s.fname,' ',s.lname) as sponsor_name 
    FROM customer c
    JOIN customer_settings cs ON (cs.customer_id=c.id)
    JOIN customer s ON s.id = c.sponsor_id
    WHERE c.type IN ('Agent') AND c.is_deleted='N' AND c.sponsor_id =:id ";
    $params_sub=array(":id"=>$sub_row['id']);  
    $rows_sub = $pdo->select($sql_sub,$params_sub);
    
    if(count($rows_sub)>0){
        $class='node-highlight';
    }else{
        $class=$node_boder_class[$sub_row['type']];
    }
    //pre_print($class);
    $eid=md5($sub_row["id"]);
    $json_sub_tree .= "{id: \"" . ($sub_row['id']) . "\",name: \"<div class='node_label " . $class . "'><a href='javascript:void(0);'><h4><i class='fa fa-external-link'></i>" . stripslashes($business_name) . "</h4></a><p> ".$sub_row['rep_id']." </p><h5 " . (strtolower($sub_row['status']) != "active" ? "class='text-inactive'" : "") . ">" . strtoupper($coded_level) . "</h5><p>".$icons."</p></div>\",data: {user_id: \"" . $sub_row["id"] . "\", Name: \"" . stripslashes($business_name) . "\",ID: \"" . $sub_row["rep_id"] . "\",Email: \"" . $sub_row["email"] . "\",Type: \"" . $coded_level . "\",Status: \"" . $sub_row["status"] . "\",Sponsor: \"" . $sub_row["sponsor_name"] . "\"},children: [] },";
  }
  $json_sub_tree = rtrim($json_sub_tree, ",");
  $json_sub_tree = "{ 'childData' : [" . $json_sub_tree . "]}";
  
  echo $json_sub_tree;
}
?>