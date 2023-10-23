<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once __DIR__ . '/../includes/function.class.php';
  $functionsList = new functionsList();
  $response = array();

  $day = checkIsset($_REQUEST['day']);
  $payPeriod = checkIsset($_REQUEST['payPeriod']);
  $type = checkIsset($_REQUEST['type']);

  if(!empty($day)){
    $selCommSettings = $pdo->selectOne("SELECT id,commission_period FROM commission_periods_settings WHERE commission_type=:type",array(":type" => $type));

    if(!empty($selCommSettings)){
      $updParams = array(
        "commission_day" => $day,
        "commission_period" => $payPeriod,
      );

      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $selCommSettings['id'],
        ),
      );
      $pdo->update('commission_periods_settings', $updParams, $updWhere);
        //************* Activity Code Start *************
        $oldValue = $selCommSettings;
        $newValue = $updParams;
        unset($oldValue['id']);        
        unset($newValue['commission_day']);        
        
        $activity = array_diff_assoc($oldValue, $newValue);
      
        if(!empty($activity)){
          $tmp = array();
          $tmp2 = array();

          if(array_key_exists('commission_period',$activity)){
            $tmp['Pay Period'] = $oldValue['commission_period'];
            $tmp2['Pay Period'] = $newValue['commission_period'];
          }

          $link = $ADMIN_HOST.'/payment_setting.php';
                  
          $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,'',$selCommSettings['id'],'commission_periods_settings','Admin Updated Commission Periods Settings','updated Weekly Pay Period');
        }
      //************* Activity Code End *************
    }else{
      $insParams = array(
        "commission_day" => $day,
        "commission_period" => $payPeriod,
        "commission_type" => $type,
      );
      $periodId = $pdo->insert('commission_periods_settings', $insParams);

        //************* Activity Code Start *************
          $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/payment_setting.php',
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' updated Commission Periods Settings : Weekly Period ('.$payPeriod.')',
          ); 
          activity_feed(3, $_SESSION['admin']['id'], 'Admin',$periodId, 'commission_periods_settings','Admin Updated Commission Periods Settings', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        //************* Activity Code End *************

    }
 
    $response['status'] = "success";
    $response['msg'] = "Commission Periods Settings Saved Successfully.";
  } else {
    $response['status'] = "fail";
    $response['msg'] = "Select Commission Pay period";
  }

  header('Content-type: application/json');
  echo json_encode($response); 
  dbConnectionClose();
  exit;
?>