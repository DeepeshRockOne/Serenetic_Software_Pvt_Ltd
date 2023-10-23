<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once __DIR__ . '/../includes/function.class.php';
  $functionsList = new functionsList();

  $validate = new Validation();
  $response = array();

  $attempt = checkIsset($_POST['attempt'],'arr');
  $attemptFrequency = checkIsset($_POST['attempt_frequency'],'arr');
  $failTriggerId = checkIsset($_POST['fail_trigger_id'],'arr');
  $adminTicket = checkIsset($_POST['admin_ticket'],'arr');

  $total_days = $dayErrorKey = 0;

  if(!empty($attempt)){
    foreach ($attempt as $key => $val) {
      $validate->string(array('required' => true, 'field' => 'attempt_frequency'.$val, 'value' => $attemptFrequency[$val]), array('required' => 'Day is required'));

      $validate->string(array('required' => true, 'field' => 'fail_trigger_id'.$val, 'value' => $failTriggerId[$val]), array('required' => 'Trigger is required'));
      if(!$validate->getError('attempt_frequency'.$val)){
        $total_days += $attemptFrequency[$val];
      }
    }
  }
  
  if(!empty($attemptFrequency) && $total_days > 26){
    end($attemptFrequency);
    $dayErrorKey = key($attemptFrequency);
    $validate->setError('attempt_frequency'.$dayErrorKey,'Failed Renewal Setting days should not be more than 26 days');
  }
  
  if ($validate->isValid()) {
    $attemptIdsArr = array();

    if(!empty($attempt)){
      $failAttempt = 1;
      foreach ($attempt as $key => $val) {
        $insParams = array(
                        "attempt" => $failAttempt,
                        "attempt_frequency" => checkIsset($attemptFrequency[$val]),
                        "fail_trigger_id" => checkIsset($failTriggerId[$val]),
                        "admin_ticket" => (!empty($adminTicket[$val]) ? "Y" : ''),
                      );

        if($val > 0){
          $sqlAttempt = "SELECT id,attempt,attempt_frequency,fail_trigger_id,admin_ticket FROM prd_subscription_attempt WHERE is_deleted='N' AND id=:id";
          $resAttempt = $pdo->selectOne($sqlAttempt,array(":id"=>$val));
        
          if(!empty($resAttempt)){
            $attemptId = $resAttempt['id'];
            array_push($attemptIdsArr,$attemptId);

            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(
                ':id' => $resAttempt['id'],
              ),
            );
            $activity = $pdo->update('prd_subscription_attempt', $insParams, $updWhere,true);
            //************* Activity Code Start *************
              if(!empty($activity)){
                $tmp = array();
                $tmp2 = array();

                if(array_key_exists('attempt_frequency',$activity)){
                  $tmp['Days'] = $resAttempt['attempt_frequency'];
                  $tmp2['Days'] = $insParams['attempt_frequency'];
                }

                if(array_key_exists('fail_trigger_id',$activity)){
                  $tmp['Trigger ID'] = $resAttempt['fail_trigger_id'];
                  $tmp2['Trigger ID'] = $insParams['fail_trigger_id'];
                }

                if(array_key_exists('admin_ticket',$activity)){
                  $tmp['Admin Ticket'] = $resAttempt['admin_ticket'];
                  $tmp2['Admin Ticket'] = $insParams['admin_ticket'];
                }

                $link = $ADMIN_HOST.'/payment_setting.php';

                if(!empty($tmp)){
                  $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,'',$attemptId,'prd_subscription_attempt','Admin Updated Failed Renewal Settings','updated on Failed Attempt '.$failAttempt);
                }
              }
            //************* Activity Code End *************
          }
        }else{
          $attemptId = $pdo->insert('prd_subscription_attempt', $insParams);
          array_push($attemptIdsArr,$attemptId);
         
          //************* Activity Code Start *************
            $description['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/payment_setting.php',
                'title'=>$_SESSION['admin']['display_id'],
              ),
              'ac_message_1' =>' created failed renewal settings : Attempt '.$insParams["attempt"].' to reattempt after '.$insParams["attempt_frequency"].' days and use trigger '.$insParams["fail_trigger_id"].' for notification.',
            ); 
            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $attemptId, 'prd_subscription_attempt','Admin Created Failed Renewal Settings', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
          //************* Activity Code End *************
        }
       
        $failAttempt++;
      }

      if(!empty($attemptIdsArr)){
        delFailedAttempt($attemptIdsArr);
      }

      $response['status'] = "success";
      $response['msg'] = "Failed Renewal Settings Saved Successfully.";
    }else{
      delFailedAttempt();
      $response['status'] = "fail";
      $response['msg'] = "Failed Renewal Settings not found";
    }
  } else {
    $errors = $validate->getErrors();
    $response['errors'] = $errors;
    $response['status'] = "fail";
  }


  function delFailedAttempt($attemptIdsArr = array()){
    global $pdo,$ADMIN_HOST;
    $incr = "";

    if(!empty($attemptIdsArr)){
      $incr .= " AND id NOT IN(".implode(',',$attemptIdsArr).")";
    }
    
    $sqlAttempt = "SELECT id,attempt FROM prd_subscription_attempt WHERE is_deleted='N' $incr";
    $removeAttempt = $pdo->select($sqlAttempt);

    if(!empty($removeAttempt)){
      foreach ($removeAttempt as $row) {
        $params = array('is_deleted' => 'Y');
        $where = array(
          'clause' => 'id = :id ', 
          'params' => array(':id' => $row['id'])
        );
        $pdo->update("prd_subscription_attempt", $params, $where);

        //************* Activity Code Start *************
          $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' deleted Failed Attempt '.$row['attempt'],
          ); 

          activity_feed(3, $_SESSION['admin']['id'], 'Admin', $row['id'], 'prd_subscription_attempt','Admin Deleted Failed Renewal Settings', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        //************* Activity Code End ***************
      }
    }
  }

  header('Content-type: application/json');
  echo json_encode($response);
  dbConnectionClose(); 
  exit;
?>