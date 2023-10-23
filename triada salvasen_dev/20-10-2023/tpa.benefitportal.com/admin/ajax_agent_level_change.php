<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$id=$_GET['agent_id'];
$admin_id = $_SESSION['admin']['id'];
$agent_coded_id = $_GET['level_id'];
$agent_coded_level = $_GET['level'];
$res = array();

$query = "SELECT c.type,c.id,cs.agent_coded_level,cs.agent_coded_id,CONCAT(c.fname,' ',c.lname) as name,c.rep_id  from customer c JOIN customer_settings cs ON(cs.customer_id=c.id) where md5(c.id)=:id and c.is_deleted='N'";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if (!empty($srow)) {
  $update_params = array(
    'agent_coded_id' => makeSafe($agent_coded_id),
    'agent_coded_level' => makeSafe($agent_coded_level),

  );
  $update_where = array(
    'clause' => 'customer_id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );
  
  $pdo->update("customer_settings", $update_params, $update_where);
  
  
  $old_level = $agentCodedRes[$srow['agent_coded_id']]['level_heading'];
  $new_level = $agentCodedRes[$agent_coded_id]['level_heading'];

  $user_data = get_user_data($_SESSION['admin']);
  audit_log($user_data, $srow['id'], $srow['type'], 'Level Change from ' . $old_level . ' to ' . $new_level);


  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' updated Agent '.$srow['name'].'(',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.$id,
      'title'=> $srow['rep_id'],
    ),
    'ac_message_2'=>') Level from '.$old_level.' to '.$new_level,
  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $srow['id'], 'Agent', 'Agent Level Updated',$_SESSION['admin']['name'],"",$desc, "", "");
    setNotifySuccess("Level Changed Successfully!");
} else {
    setNotifyError("Something went wrong!");
}
redirect($_SERVER['HTTP_REFERER'],true);
dbConnectionClose();
?>

