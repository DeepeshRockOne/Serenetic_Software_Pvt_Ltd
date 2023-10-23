<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once __DIR__ . '/../includes/function.class.php';
  $functionsList = new functionsList();
  $response = array();

  $endCoverage = checkIsset($_REQUEST['endCoverage']);
  $isOpenEnrolllment = checkIsset($_REQUEST['isOpenEnrolllment']);

  $validate = new Validation();

  if($isOpenEnrolllment=='Y'){
      $validate->string(array('required' => true, 'field' => 'endCoverage', 'value' => $endCoverage), array('required' => 'Enter End Coverage Period'));
      
      if (!$validate->getError("endCoverage") && (strtotime(date('Y-m-d',strtotime($endCoverage))) <= strtotime(date('Y-m-d')))) {
        $validate->setError("endCoverage","End Plan Date should be in future");
      }
  }else{
      if (!empty($endCoverage)) {
        $validate->setError("endCoverage","Turn on Open Application");
      }
  }

  if($validate->isValid()){
    $endCoverageRes = $pdo->selectOne("SELECT id,is_open_enrollment,end_coverage_date FROM end_coverage_periods_settings WHERE is_deleted='N'");

    if(!empty($endCoverageRes)){

      $updParams = array(
        "is_open_enrollment" => $isOpenEnrolllment,
        "end_coverage_date" => NULL,
      );
      if($isOpenEnrolllment=='Y'){
        $updParams['end_coverage_date']=date('Y-m-d',strtotime($endCoverage));
      }

      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $endCoverageRes['id'],
        ),
      );
      $pdo->update('end_coverage_periods_settings', $updParams, $updWhere);
      //************* Activity Code Start *************
        $oldValue = $endCoverageRes;
        $newValue = $updParams;
        unset($oldValue['id']);        
        
        $activity = array_diff_assoc($oldValue, $newValue);
      
        if(!empty($activity)){
          $tmp = array();
          $tmp2 = array();

          if(array_key_exists('end_coverage_date',$activity)){
            $tmp['End Coverage Period'] = $oldValue['end_coverage_date'];
            $tmp2['End Coverage Period'] = $newValue['end_coverage_date'];
          }
          if(array_key_exists('is_open_enrollment',$activity)){
            $tmp['is Open Enrollment'] = $oldValue['is_open_enrollment'];
            $tmp2['is Open Enrollment'] = $newValue['is_open_enrollment'];
          }

          $link = $ADMIN_HOST.'/payment_setting.php';
                  
          $actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,$link,'',$endCoverageRes['id'],'end_coverage_periods_settings','Admin Updated End Plan Periods Settings','updated End Plan Period');
        }
      //************* Activity Code End *************
    }else{
      $insParams = array(
        "is_open_enrollment" => $isOpenEnrolllment,
        "end_coverage_date" => NULL,
      );
      if($isOpenEnrolllment=='Y'){
        $insParams['end_coverage_date']=date('Y-m-d',strtotime($endCoverage));
      }
      $periodId = $pdo->insert('end_coverage_periods_settings', $insParams);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/payment_setting.php',
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' updated End Plan Periods Settings : '.$endCoverage,
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$periodId, 'end_coverage_periods_settings','Admin Updated End Plan Periods Settings', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************
    }
 
    $response['status'] = "success";
    $response['msg'] = "End Plan Periods Settings Saved Successfully.";
  } else {
    $response['status'] = "fail";
    $response['errors'] = $validate->getErrors();
    $response['msg'] = "Enter End Plan Period";
  }

  header('Content-type: application/json');
  echo json_encode($response);
  dbConnectionClose(); 
  exit;
?>