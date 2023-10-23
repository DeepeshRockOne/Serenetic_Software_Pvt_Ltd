<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';

  $response = array();
  $api_user = 'apikey';
  $api_key = $SENDGRID_API_KEY;
  $token = $SENDGRID_API_KEY;


  $idArr = !empty($_REQUEST['remove_email']) ? $_REQUEST['remove_email'] : array();
  $email_ids = '';
  $email_ids = '"'.implode('","', $idArr).'"';

  $resEmail = $pdo->select("SELECT id,email FROM unsubscribes WHERE md5(id) IN($email_ids) AND type='email' AND is_deleted='N'");

  if(!empty($resEmail)){
    foreach ($resEmail as $row) {

      // remove this email from sendgrid global_unsubscribes list

      $data = "api_user=".$api_user."&api_key=".$api_key."&email=".$row['email'];                  
      $url = 'https://api.sendgrid.com/api/unsubscribes.delete.json';
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl,CURLOPT_HEADER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('authorization: Bearer '.$token
          ));
      $resp = curl_exec($curl);
      curl_close($curl);

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
          'ac_message_1' =>' removed '.$row['email'].' from unsubscribe list',
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