<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET['id'];
$search = $_GET['search'];
if(isset($_REQUEST['id'])){
  $group_sql = "SELECT fname,lname,rep_id,id FROM customer WHERE type ='Group' AND md5(id)=:id";
  $where_id = array(':id' => $id);
  $group_res = $pdo->selectOne($group_sql, $where_id);
  $res = array();
   
  if (!$group_res) {
    $res['status'] = 'fail';
    $res['msg'] = 'Group can not be deleted.';
    if($search != 'Y'){
      setNotifyError('Group can not be deleted.');
    }
  } else {
    $up_params = array(
      'is_deleted' => 'Y',
    );
    $up_where = array(
        'clause' => 'md5(id)=:id',
        'params' => array(
            ':id' => $id
        )
    );
    $pdo->update('customer', $up_params, $up_where);

    $group_id = $group_res['id'];
    $group_data = array();

    $group_data['customer'] = $pdo->select("SELECT * FROM customer WHERE id =:id",array(":id"=>$group_id));
    $group_data['activity_feed'] = $pdo->select("SELECT * FROM activity_feed WHERE user_id =:id AND user_type=:user_type",array(":id"=>$group_id,":user_type"=>'Group'));
    $group_data['activity_feed_v2'] = $pdo->select("SELECT * FROM activity_feed WHERE entity_id =:id AND entity_type=:entity_type",array(":id"=>$group_id,":entity_type"=>'customer'));
    $group_data['leads'] = $pdo->select("SELECT * FROM leads WHERE customer_id =:id",array(":id"=>$group_id));

    $action_by_data = array(
        "user_id" => $_SESSION['admin']['id'],
        "full_name" => $_SESSION['admin']['name'],
        "user_type" => $_SESSION['admin']['type'],
    );
    $group_data = json_encode($group_data);

    audit_log($action_by_data,$group_id,'Group','Group Deleted','old_data',$group_data);
    
    $pdo->delete("DELETE FROM customer WHERE id =:id ",array(":id"=>$group_id));
    $pdo->delete("DELETE FROM activity_feed WHERE user_id = :id AND user_type=:user_type ",array(":id"=>$group_id,":user_type"=>'Group'));
    $pdo->delete("DELETE FROM activity_feed WHERE entity_id = :id AND entity_type=:entity_type",array(":id"=>$group_id,":entity_type"=>'customer'));
    $pdo->delete("DELETE FROM leads WHERE customer_id = :id ",array(":id"=>$group_id));

    $res['status'] = 'success';
    $res['msg'] = 'Group deleted';
    if($search != 'Y'){
      setNotifySuccess("Group deleted successfully");
    }

    $mail_data = array();
    $mail_data = "Hello, Group account deleted <br/><br/>Group Name : ".$group_res['fname'].' '.$group_res['lname']."<br/> Group ID : ".$group_res['rep_id']."<br/><br/> This Group deleted by : ". $_SESSION['admin']['name'] ."<br/> <br/> Thank You!";

      $toemail = array("karan@cyberxllc.com","dharmesh@cyberxllc.com");
      $subject="Group Deleted";
      trigger_mail_to_email($mail_data, $toemail, $subject, $other_params = array(), $company_id = 3);
  }
}
if(isset($_REQUEST['multiple_id'])){
    $multiple_id = $_REQUEST['multiple_id'];
    if(!empty($multiple_id)){
      foreach ($multiple_id as $value) {
          $up_params = array(
            'is_deleted' => 'Y',
          );
          $up_where = array(
              'clause' => 'id=:id',
              'params' => array(
                  ':id' => $value
              )
          );
          $pdo->update('customer', $up_params, $up_where);
          $res['status'] = 'success';
          $res['msg'] = 'Group deleted successfully'; 
      }
    }else{
      $res['status'] = 'fail';
    }
}

header('Content-Type:application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>