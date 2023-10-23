<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';

  $response = array();

  $idArr = !empty($_REQUEST['remove_phone']) ? $_REQUEST['remove_phone'] : array();
  $phone_id = '';
  $phone_id = '"'.implode('","', $idArr).'"';

  $resSms = $pdo->select("SELECT id,phone FROM unsubscribes WHERE md5(id) IN($phone_id) AND type='sms' AND is_deleted='N'");

  if(!empty($resSms)){
    foreach ($resSms as $row) {
      $params = array('is_deleted' => 'Y',"removed_date"=>'msqlfunc_NOW()');
      $where = array(
        'clause' => 'id = :id ', 
        'params' => array(':id' => $row['id'])
      );
      $pdo->update("unsubscribes", $params, $where);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' removed '.$row['phone'].' from unsubscribe list',
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $row['id'], 'unsubscribes','Removed from Unsubscribes', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    }
     $response['status'] = "success";
  }else{
    $response['status'] = "fail";
  }
 
  header('Content-type: application/json');
  echo json_encode($response);
  dbConnectionClose(); 
  exit;
?>