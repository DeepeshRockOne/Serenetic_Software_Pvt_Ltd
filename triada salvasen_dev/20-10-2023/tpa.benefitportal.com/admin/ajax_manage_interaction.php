<?php include_once dirname(__FILE__) . '/layout/start.inc.php';
  
  $validate = new Validation();
  $response = array();
  

  $interaction_id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : '';
  $user_type = !empty($_REQUEST['user_type']) ? $_REQUEST['user_type'] : '';
  $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';

    $validate->string(array('required' => true, 'field' => 'type', 'value' => $user_type), array('required' => 'Interaction User Type is required'));
    $validate->string(array('required' => true, 'field' => 'type', 'value' => $type), array('required' => 'Interaction Type is required'));
  
  if ($validate->isValid()) {
    
    $insertParams = array(
      "type" => $type,
      "user_type" => $user_type,
      "created_at" => "msqlfunc_NOW()"
    );

    $interactionIncrId = '';
    if(!empty($interaction_id)) {
      $sql="SELECT id,type,user_type FROM interaction WHERE is_deleted='N' AND md5(id)=:id";
      $updInteraction=$pdo->selectOne($sql,array(":id"=>$interaction_id));
    }

    if(!empty($updInteraction['type'])){
      unset($insertParams['created_at']);

      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $updInteraction['id'],
        ),
      );
      $pdo->update('interaction', $insertParams, $upd_where);

      //************* Activity Code Start *************
        $interactionIncrId = $updInteraction['id'];
        unset($updInteraction['id']);
        $oldValue = $updInteraction;
        $newValue = $insertParams;
        
        $checkDiff = array_diff_assoc($newValue, $oldValue);
      
        if(!empty($checkDiff)){

          $activityFeedDesc['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id']),
            'ac_message_1' =>' updated '.$updInteraction['type'].' on '.ucfirst($user_type).' interaction',
          ); 
          
          foreach ($checkDiff as $key1 => $value1) {
            $activityFeedDesc['key_value']['desc_arr'][$key1]= 'From '.$oldValue[$key1].' To '.$newValue[$key1];
          }
          
          activity_feed(3, $_SESSION['admin']['id'], 'Admin', $interactionIncrId, 'interaction','Updated Interaction Type', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
        }
      //************* Activity Code End *************
    } else {
      $interaction_id = $pdo->insert('interaction', $insertParams);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' created interaction '.$type.' on '.ucfirst($user_type).' interaction ',
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $interaction_id, 'interaction','Created Interaction', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************
    }

    $response['status'] = "success";
    $response['msg'] = "Interaction Saved Successfully.";
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