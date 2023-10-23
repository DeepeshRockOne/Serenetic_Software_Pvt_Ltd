<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET['id'];
$search = $_GET['search'];


if(isset($_REQUEST['id'])){
  $agent_sql = "SELECT fname,lname,rep_id,id FROM customer WHERE type ='Agent' AND md5(id)=:id";
  $where_id = array(':id' => $id);
  $agent_res = $pdo->selectOne($agent_sql, $where_id);

  $res = array();
   
  if (!$agent_res) {
    $res['status'] = 'fail';
    $res['msg'] = 'Agent can not be deleted.';
    if($search != 'Y'){
      setNotifyError('Agent can not be deleted.');
    }
  } else {
    $downlineSql = "SELECT GROUP_CONCAT(rep_id) as downline FROM customer where upline_sponsors like '%,".$agent_res['id'].",%' AND is_deleted='N'";
    $downlineRes = $pdo->selectOne($downlineSql);

    if(!empty($downlineRes) && !empty($downlineRes['downline'])){
      $res['status'] = 'fail';
      $res['msg'] = 'Agent can not be deleted, Downline Found';
    }else{
      
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

      $agent_id = $agent_res['id'];
      
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Deleted Agent ',
        'ac_red_2'=>array(
            'title'=>$agent_res['rep_id'],
        ),
      ); 

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $agent_res['id'], 'Agent','Deleted Agent', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

      $res['status'] = 'success';
      $res['msg'] = 'Agent deleted';
      if($search != 'Y'){
        setNotifySuccess("Agent deleted successfully");
      }

      $mail_data = array();
      $mail_data = "Hello, Agent account deleted <br/><br/>Agent Name : ".$agent_res['fname'].' '.$agent_res['lname']."<br/> Agent ID : ".$agent_res['rep_id']."<br/><br/> This Agent deleted by : ". $_SESSION['admin']['name'] ."<br/> <br/> Thank You!";

      $toemail = array("karan@cyberxllc.com","dharmesh@cyberxllc.com");
      $subject="Agent Deleted";
      trigger_mail_to_email($mail_data, $toemail, $subject, $other_params = array(), $company_id = 3);
    }
    
  }
}

header('Content-Type:application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>