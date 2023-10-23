<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$status = $_POST['status'];
$id=$_POST['id'];

$reason = checkIsset($_POST['reason']);
$terminationDate = checkIsset($_POST['termination_date']);
$res = array();

if(in_array($status,array('Terminated','Suspended'))){
  $validate->string(array('required' => true, 'field' => 'reason', 'value' => $reason), array('required' => 'Please Enter reason to change'));
}
if(empty($terminationDate) && $status =='Terminated'){
  $validate->setError("termination_date","Please select termination date.");
}

if ($validate->isValid()){ 
   $query = "SELECT id, status, type,concat(fname,' ',lname) as name,rep_id FROM customer WHERE md5(id) =:id and is_deleted='N'";
   $srow = $pdo->selectOne($query,array(':id'=>$id));

    if (!empty($srow)){

      //OP29-843 Update For Terminated Status Start
        if(!empty($terminationDate) && strtolower($status) =='terminated'){
          $generateListBill = false;
          $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
          $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$srow['id']));
          $sponsor_billing_method = '';
          if(!empty($resBillingType)){
              $sponsor_billing_method = $resBillingType['billing_type'];
              if($sponsor_billing_method == 'list_bill'){
                $generateListBill = true;
              }
          }
          updateGroupMemberPolicy($srow['id'],$terminationDate,'Policy Change',array(),$generateListBill,'Agent',$sponsor_billing_method);
        }
      //OP29-843 Update For Terminated Status End
      
      $update_params = array(
        'status' => makeSafe($status)
      );
      $update_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => makeSafe($srow['id'])
        )
      );
      $pdo->update("customer", $update_params, $update_where);
      
      $user_data = get_user_data($_SESSION['agents']);

      audit_log($user_data,$srow['id'], $srow['type'], 'Status Change from ' . $srow['status'] . ' to ' . $status);

      $old_status = $srow['status'] == 'Active' ? 'Contracted' : $srow['status'];
      $new_status = $status=='Active' ? 'Contracted' : $status;

      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
          'title' => $_SESSION['agents']['rep_id'],
        ),
        'ac_message_1' =>' updated Group '.$srow['name'].'(',
        'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/groups_details.php?id='.$id,
          'title'=> $srow['rep_id'],
        ),
        'ac_message_2'=>') status from '.$old_status.' to '.$new_status,
      );  
      if(in_array(strtolower($status),array('terminated','suspended'))){
        $description['desc'] ='Reason: '.$reason;
      }
      if(!empty($terminationDate) && strtolower($status) =='terminated'){
        $description['desc_termDate'] ='Term Date: '.ucfirst(str_replace('_',' ',$terminationDate));
      }

      $desc=json_encode($description);
      activity_feed(3,$_SESSION['agents']['id'],'Agent',$srow['id'],'Group','Status Updated',"","",$desc,"","");
      
      $check_query = "SELECT customer_id FROM customer_settings WHERE customer_id =:c_id ";
      $check_query_row = $pdo->selectOne($check_query,array(':c_id'=>$srow['id']));

      if(in_array($status,array('Terminated','Suspended'))){
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

