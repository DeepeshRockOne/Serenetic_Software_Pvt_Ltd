  <?php
include_once 'layout/start.inc.php';
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();

$reason = !empty($_POST['reason']) ? $_POST['reason'] : '';
$is_qualifies_for_cobra = !empty($_POST['is_qualifies_for_cobra']) ? $_POST['is_qualifies_for_cobra'] : '';
$rule_id = !empty($_POST['rule_id']) ? $_POST['rule_id'] : 0;



$validate->string(array('required' => true, 'field' => 'reason', 'value' => $reason), array('required' => 'Please Add Reason'));
$validate->string(array('required' => true, 'field' => 'is_qualifies_for_cobra', 'value' => $is_qualifies_for_cobra), array('required' => 'Select Any Option'));


if ($validate->isValid()) {
   
  $sqlTerminationReason = "SELECT id,name,is_qualifies_for_cobra FROM termination_reason where is_deleted='N' and id=:id";
  $resTerminationReason = $pdo->selectOne($sqlTerminationReason,array(":id"=>$rule_id));

  $update_param = array(
      "name" => $reason,
      "is_qualifies_for_cobra" => $is_qualifies_for_cobra,
  );
 
  if(!empty($resTerminationReason)){
    $upd_where = array(
      'clause' => 'id = :id',
      'params' => array(
          ':id' => $resTerminationReason['id'],
      ),
    );
    $pdo->update('termination_reason', $update_param, $upd_where);
    $response['msg'] = 'Termination Reason Updated successfully!';

    $activityFeedDesc['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' => " Updated Termination Reason ",
    ); 
    if($resTerminationReason['name'] != $reason){
        $activity_text = ' From '.$resTerminationReason['name'].' To '.$reason; 
        $activityFeedDesc['key_value']['desc_arr']["Termination Reason"] = $activity_text;
    }
    if($resTerminationReason['is_qualifies_for_cobra'] != $is_qualifies_for_cobra){
        $tmpOld = ($resTerminationReason['is_qualifies_for_cobra']=='N') ? 'No' : 'Yes';
        $tmpNew = ($is_qualifies_for_cobra=='N') ? 'No' : 'Yes';
        
        $activity_text1 = ' From '.$tmpOld.' To '.$tmpNew; 
        $activityFeedDesc['key_value']['desc_arr']["Qualifies For COBRA"] = $activity_text1;
    }
  }else{      
    $termination_reason_id = $pdo->insert("termination_reason", $update_param);
    $response['msg'] = 'Termination Reason Added successfully!';
    $activity = 'insert';
    $message = ' ';
    $tmpNew = ($is_qualifies_for_cobra=='N') ? 'No' : 'Yes';
    $activityFeedDesc['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' => " Added Termination Reason ",
    ); 
    $activityFeedDesc['key_value']['desc_arr']["Termination Reason"] = $reason;
    $activityFeedDesc['key_value']['desc_arr']["Qualifies For COBRA"] = $tmpNew;
  } 

  
    
  activity_feed(3,$_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'Admin', 'Termination Reason',$_SESSION['admin']['name'],"",json_encode($activityFeedDesc));
  $response['status'] = 'success';
}
else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}



header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>