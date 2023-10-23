<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once __DIR__ . '/../includes/function.class.php';
  $functionsList = new functionsList();
  
  $validate = new Validation();
  $response = array();
  

  $reasonId = !empty($_REQUEST['id']) ? $_REQUEST['id'] : '';
  $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';

  $validate->string(array('required' => true, 'field' => 'type', 'value' => $type), array('required' => 'type is required'));
  
  if ($validate->isValid()) {
    $insertParams = array(
      "name" => $type,
    );

    if(!empty($reasonId)) {
      $sqlReason = "SELECT id,name FROM termination_reason WHERE is_deleted='N' AND md5(id)=:id";
      $resReason = $pdo->selectOne($sqlReason,array(":id"=>$reasonId));
    }

    if(!empty($resReason['id'])){
      $reasonId = $resReason['id'];
      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $resReason['id'],
        ),
      );
      $pdo->update('termination_reason', $insertParams, $updWhere);

      //************* Activity Code Start *************
        $oldValue = $resReason;
        $newValue = $insertParams;
        unset($oldValue['id']);        
        
        $activity = array_diff_assoc($oldValue, $newValue);
      
        if(!empty($activity)){
          $tmp = array();
          $tmp2 = array();

          if(array_key_exists('name',$activity)){
            $tmp['name'] = $oldValue['name'];
            $tmp2['name'] = $newValue['name'];
          }

          $link = $ADMIN_HOST.'/payment_setting.php';
                  
          $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,'',$resReason['id'],'termination_reason','Admin Updated Reversal Reasons','updated Reversal Reasons');
        }
      //************* Activity Code End *************
    }else{
      $reasonId = $pdo->insert('termination_reason', $insertParams);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/payment_setting.php',
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' created Reversal Reasons '.$insertParams['name'],
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $reasonId, 'termination_reason','Created Reversal Reasons', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************
    }

    $response['status'] = "success";
    $response['msg'] = "Reversal Reasons Saved Successfully.";
  } else {
    $errors = $validate->getErrors();
    $response['errors'] = $errors;
    $response['status'] = "fail";
  }

  header('Content-type: application/json');
  echo json_encode($response); 
  dbConnectionClose();
  exit;
?>