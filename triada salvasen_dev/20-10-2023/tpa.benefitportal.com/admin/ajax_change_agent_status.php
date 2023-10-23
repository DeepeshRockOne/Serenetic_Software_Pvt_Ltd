<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$status = $_POST['status'];
$id=$_POST['id'];
$admin_id = $_SESSION['admin']['id'];
$downline = checkIsset($_POST['downline']);
$loa = checkIsset($_POST['loa']);
$reason = checkIsset($_POST['reason']);
$res = array();


if (in_array($status, array('Terminated','Suspended'))){
  $validate->string(array('required' => true, 'field' => 'reason', 'value' => $reason), array('required' => 'Please Enter reason to change'));
}

if ($validate->isValid()){ 
   $query = "SELECT id, status, type,concat(fname,' ',lname) as name,rep_id FROM customer WHERE md5(id) =:id and is_deleted='N'";
   $srow = $pdo->selectOne($query,array(':id'=>$id));

    if (!empty($srow)){

      $resAgents = array();
      $id = $srow['id'];
      

      if(!empty($downline)){
        $selAgents = "SELECT group_concat(c.id) as ids,GROUP_CONCAT(CONCAT(c.rep_id,' - ',c.fname,' ',c.lname) SEPARATOR '<br>') as agentsInfo 
                      FROM customer c  
                      WHERE c.type='Agent' AND c.upline_sponsors like '%,$id,%' AND c.is_deleted='N' 
                      AND c.status not in('Invited','Pending Approval','Pending Documentation','Pending Contract')";
        $resAgents = $pdo->selectOne($selAgents);
      }else if(!empty($loa)){
          $selAgents = "SELECT group_concat(c.id) as ids,GROUP_CONCAT(CONCAT(c.rep_id,' - ',c.fname,' ',c.lname) SEPARATOR '<br>') as agentsInfo 
                      FROM customer c
                      LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
                      WHERE c.type='Agent' AND cs.agent_coded_level='LOA' AND  c.sponsor_id=:sponsor_id AND c.is_deleted='N' 
                      AND c.status not in('Invited','Pending Approval','Pending Documentation','Pending Contract')";
         $resAgents = $pdo->selectOne($selAgents,array(":sponsor_id" => $id));
      }

      $update_params = array(
        'status' => makeSafe($status)
      );
      
      if(!empty($resAgents['ids']) && (!empty($downline)|| !empty($loa))){
        $update_where = array(
            'clause' => " id IN (". makeSafe($resAgents['ids']) .",$id) ",
            'params' => array(),
        );
      }else{
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
              ':id' => makeSafe($srow['id'])
            )
          );
      }
      
      $pdo->update("customer", $update_params, $update_where);
      
      $user_data = get_user_data($_SESSION['admin']);

      audit_log($user_data,$srow['id'], $srow['type'], 'Status Change from ' . $srow['status'] . ' to ' . $status);

      $old_status = $srow['status'] == 'Active' ? 'Contracted' : $srow['status'];
      $new_status = $status=='Active' ? 'Contracted' : $status;

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
        'ac_message_2'=>')',
      );
          
      if(!empty($resAgents['ids']) && !empty($downline) ){
        $description['description_agents'] = 'And their Downline Agents : <br>'.$resAgents['agentsInfo'];
      }else if(!empty($resAgents['ids']) && !empty($loa)){
        $description['description_agents'] = 'And their LOA Agents : <br>'.$resAgents['agentsInfo'];
      }
      $description['description_message'] = 'Status updated from '.$old_status.' to '.$new_status;
      
      if(!empty($reason)){
        $description['desc'] ='Reason : '.$reason;
      }

      $desc=json_encode($description);
      activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $srow['id'], 'Agent', 'Status Updated',$_SESSION['admin']['name'],"",$desc, "", "");

      if(!empty($reason)){
        $check_query = "SELECT customer_id FROM customer_settings WHERE customer_id =:c_id ";
        $check_query_row = $pdo->selectOne($check_query,array(':c_id'=>$srow['id']));
        
        if (!empty($check_query_row)){
          $update_params = array(
            'term_reason' => makeSafe($reason)
          );
          $update_where = array(
            'clause' => 'customer_id = :id',
            'params' => array(
                ':id' => makeSafe($srow['id'])
            )
          );
          $pdo->update("customer_settings", $update_params, $update_where);
        }else{
          $insert_params = array(
          'customer_id' => makeSafe($srow['id']),
          'term_reason' => makeSafe($reason),
          );
          $activity_id = $pdo->insert("customer_settings", $insert_params);
        }
      }


      $res['status'] = 'success';
      $res['msg'] = 'Status Changed Successfully';
    }else{
      $res['status'] = 'error';
      $res['msg'] = 'Something went wrong';
      setNotifyError($res['msg'],true);
    }
} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}  

$errors = $validate->getErrors();
if (count($errors)) {
  $res["status"] = "fail";
  $res["error"] = $errors;
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

