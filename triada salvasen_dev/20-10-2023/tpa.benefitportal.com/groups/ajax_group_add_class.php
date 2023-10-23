<?php include_once dirname(__FILE__) . '/layout/start.inc.php';

  $validate = new Validation();
  $response = array();
  $sch_params = array();
  
  $group_classes_id = !empty($_POST['group_classes_id']) ? $_POST['group_classes_id'] : '';
  
  $class_name = !empty(trim($_POST['class_name'])) ? trim($_POST['class_name']) : '';
  
  $existing_member_eligible_coverage = !empty($_POST['existing_member_eligible_coverage']) ? $_POST['existing_member_eligible_coverage'] : '';
  $existing_member_eligible_coverage_day = !empty($_POST['existing_member_eligible_coverage_day']) ? $_POST['existing_member_eligible_coverage_day'] : '';


  $new_member_eligible_coverage = !empty($_POST['new_member_eligible_coverage']) ? $_POST['new_member_eligible_coverage'] : '';
  $new_member_eligible_coverage_day = !empty($_POST['new_member_eligible_coverage_day']) ? $_POST['new_member_eligible_coverage_day'] : '';

  $renewed_member_eligible_coverage = !empty($_POST['renewed_member_eligible_coverage']) ? $_POST['renewed_member_eligible_coverage'] : '';
  $renewed_member_eligible_coverage_day = !empty($_POST['renewed_member_eligible_coverage_day']) ? $_POST['renewed_member_eligible_coverage_day'] : '';
  
  $pay_period_select = !empty($_POST['pay_period_select']) ? $_POST['pay_period_select'] : '';

  $pay_period = !empty($_POST['pay_period']) ? $_POST['pay_period'] : '';
  $Monthly = !empty($_POST['monthly']) ?  $_POST['monthly'] : '' ;
  $semiMonthly = !empty($_POST['semiMonthlyDate']) ?  $_POST['semiMonthlyDate'] : '';
  $biWeekly  = !empty($_POST['BiweeklyDate']) ?  $_POST['BiweeklyDate'] : '' ;
  $weekly = !empty($_POST['weekly']) ? $_POST['weekly'] : '';
  
  $validate->string(array('required' => true, 'field' => 'class_name', 'value' => $class_name), array('required' => 'Class Name is required.'));
  $group_id = $_SESSION['groups']['id'];

  if(!empty($group_id)){
    $group_class = [];
    $sch_params = array(
      ':group_id' => $group_id
    );
    $incr = '';
    if(!empty($group_classes_id)){
      $incr .= " AND MD5(id)!=:id ";
      $sch_params[':id']=$group_classes_id;
    }
    $group_class_name = $pdo->selectOne("SELECT GROUP_CONCAT(class_name) AS className FROM group_classes where group_id=:group_id and is_deleted='N' $incr ",$sch_params);
    if(!empty($group_class_name)){
      $group_class = explode(",", $group_class_name['className']);
      if(in_array(trim($class_name),$group_class)){
        $validate->setError("class_name","Class Name is already exist.");
      }
    }
  }
  $validate->string(array('required' => true, 'field' => 'existing_member_eligible_coverage', 'value' => $existing_member_eligible_coverage), array('required' => 'Select Any Option'));
  $validate->string(array('required' => true, 'field' => 'new_member_eligible_coverage', 'value' => $new_member_eligible_coverage), array('required' => 'Select Any Option'));
  $validate->string(array('required' => true, 'field' => 'renewed_member_eligible_coverage', 'value' => $renewed_member_eligible_coverage), array('required' => 'Select Any Option'));
 
  $validate->string(array('required' => true, 'field' => 'pay_period', 'value' => $pay_period_select), array('required' => 'Pay Period is required.'));

  if($pay_period_select=="pay_period"){
    $validate->string(array('required' => true, 'field' => 'pay_period', 'value' => $pay_period), array('required' => 'Pay Period is required.'));
      if($pay_period == "Semi-Monthly"){
          $validate->string(array('required' => true, 'field' => 'pay_period_semimonthly_paydate', 'value' => $semiMonthly), array('required' => 'Select Date Of Month'));
          if(!empty($semiMonthly)){
            $semiMonthlyarr = explode(",",$semiMonthly);
            $semiMonthlycount = count($semiMonthlyarr);
            if($semiMonthlycount == 1){
              $validate->setError('pay_period_semimonthly_paydate',"please enter valid date");
            }
          }
      }
      if($pay_period == "Weekly"){
          $validate->string(array('required' => true, 'field' => 'pay_period_weekly_paydate', 'value' => $weekly), array('required' => 'Select Day Of Month'));
          if(!empty($weekly)){
            $weeklyarr = explode(",",$weekly);
            $weeklydatecount = count($weeklyarr);
            if($weeklydatecount == 1){
              $validate->setError('pay_period_weekly_paydate',"please enter valid date");
            }
          }
      }
      if($pay_period == "Bi-Weekly"){
          $validate->string(array('required' => true, 'field' => 'pay_period_biweekly_paydate', 'value' =>  $biWeekly), array('required' => 'Select Day Of Month'));
          if(!empty($biWeekly)){
            $biWeeklyarr = explode(",",$biWeekly);
            $biWeeklycount = count($biWeeklyarr);
            if($biWeeklycount == 1){
              $validate->setError('pay_period_biweekly_paydate',"please enter valid date");
            }
          }
      }
  }

  if($pay_period_select=="Monthly"){
    $pay_period = "Monthly";
    $validate->string(array('required' => true, 'field' => 'pay_period_monthly_paydate', 'value' => $Monthly), array('required' => 'Select Day Of Month'));
    if(!empty($Monthly)){
      $Monthlyarr = explode(",",$Monthly);
      $Monthlycount = count($Monthlyarr);
      if($Monthlycount == 1){
        $validate->setError('pay_period_monthly_paydate',"please enter valid date");
      }
    }
  }


  if ($validate->isValid()) {
    $params=array(
      'group_id'=>$_SESSION['groups']['id'],
      'class_name'=>$class_name,
      'existing_member_eligible_coverage'=>($existing_member_eligible_coverage=='Immediately') ? $existing_member_eligible_coverage : $existing_member_eligible_coverage_day ,
      'new_member_eligible_coverage'=>($new_member_eligible_coverage=='Immediately') ? $new_member_eligible_coverage : $new_member_eligible_coverage_day ,
      'renewed_member_eligible_coverage'=>($renewed_member_eligible_coverage=='Immediately') ? $renewed_member_eligible_coverage : $renewed_member_eligible_coverage_day ,
      'pay_period'=>$pay_period,
    );
    if($pay_period_select == "Monthly"){
            $payperiod_dates = explode(",",$Monthly);
            usort($payperiod_dates, "compareDate");
        }
        if($pay_period == "Weekly"){
            $payperiod_dates = explode(",",$weekly);
            usort($payperiod_dates, "compareDate");
        }
        if($pay_period == "Semi-Monthly"){
            $payperiod_dates = explode(",",$semiMonthly);
            usort($payperiod_dates, "compareDate");
        }
        if($pay_period == "Bi-Weekly"){
          $payperiod_dates = explode(",",$biWeekly);
          $minDatePay = min(array_map('strtotime', $payperiod_dates));
          $firstDateBiweekly = date('Y-m-d', $minDatePay);
          $firstDay = date('d', strtotime($firstDateBiweekly));
          if(!empty($firstDateBiweekly) && (14 < $firstDay)){
            $firstDate = date('m/d/Y', strtotime("-14 Days", strtotime($firstDateBiweekly)));
            if(!empty($firstDate)){
              $payperiodDatesArr = explode(",",$biWeekly);
              $payperiod_dates = array_merge($payperiodDatesArr, array($firstDate));
            }
          }

            usort($payperiod_dates, "compareDate");
        }

    if(!empty($group_classes_id)){
      $whr = array(
        'clause' => 'md5(id) = :id',
        'params' => array(
            ':id' => $group_classes_id,
        ),
      );

      $paydates = !empty($_POST['allpaydate']) ? $_POST['allpaydate'] : '';
      if(!empty($paydates)){
        $paydates = explode(",",$paydates);
      }
      $payperiod = !empty($_POST['datepayperiod']) ? $_POST['datepayperiod'] : '';
     if(!empty($payperiod)){
                $groupParams = '';
                $sqlData = "SELECT * FROM group_classes where md5(id)=:id and is_deleted = 'N'";
                $resData = $pdo->selectOne($sqlData ,array(":id"=>$group_classes_id));
                          if($payperiod == 'Monthly'){
                                if($payperiod_dates != $paydates){
                                  $groupParams = array(
                                    'is_deleted' => 'Y'
                                  );
                                }
                          }
                          if($payperiod == 'Weekly'){
                                if($weekly != $paydates){
                                  $groupParams = array(
                                    'is_deleted' => 'Y'
                                  );
                                }
                          }
                          if($payperiod == 'Bi-Weekly'){
                                if($biWeekly != array_reverse($paydates)){
                                  $groupParams = array(
                                    'is_deleted' => 'Y'
                                  );
                                }
                          }
                          if($payperiod == 'Semi-Monthly'){
                                if($semiMonthly != array_reverse($paydates)){
                                  $groupParams = array(
                                    'is_deleted' => 'Y'
                                  );
                                }
                          }
                          $wherdate = array(
                            'clause' =>  'md5(class_id) = :id',
                            'params' => array(
                                ':id' => $group_classes_id,
                            ),
                          );
                          if(!empty($groupParams)){
                            $groupClassesPaydates = $pdo->update("group_classes_paydates",$groupParams,$wherdate,true);
                                  $sqlData = "SELECT * FROM group_classes where md5(id)=:id";
                                  $resData = $pdo->selectOne($sqlData ,array(":id"=>$group_classes_id));
                                  foreach($payperiod_dates as $value){
                                      $class_paydate_params = array(
                                                'class_id' => $resData['id'],
                                                'group_id'=> $_SESSION['groups']['id'],
                                                'pay_period' => $pay_period,
                                      );
                                      $paydate = date("Y-m-d", strtotime($value));
                                        $class_paydate_params['paydate'] = $paydate;
                                      $group_classes_paydate = $pdo->insert("group_classes_paydates",$class_paydate_params);
                                  }
                      }
                  }
      $activity_feed_update_group_class['group_class'] = $pdo->update("group_classes",$params,$whr,true);

      if(!empty($activity_feed_update_group_class)){
          $flg = "true";
          $class_id = getname('group_classes',$group_classes_id,'id','md5(id)');          
          $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
              'title'=>$_SESSION['groups']['rep_id'],
            ),
            'ac_message_1' =>' updated Class ',
            'ac_red_2'=>array(
                'title'=>$class_name,
            ),
          ); 
          foreach($activity_feed_update_group_class as $key => $value){
              if(!empty($value) && is_array($value)){
                  foreach($value as $key2 => $val){
                      if(array_key_exists($key2,$params)){
                              $description['key_value']['desc_arr'][$key2] = ' Updated From '.$val." To ".$params[$key2].".<br>";
                              $flg = "false";
                      } else {
                          $description['description2'][] = ucwords(str_replace('_',' ',$val));
                          $flg = "false";
                      }
                  }
              } else {
                  if(is_array($value) && !empty($value)){
                      $description['description'.$key][] = implode('',$value);
                      $flg = "false";
                  } else if(!empty($value)) {
                      $description['description'.$key][] = $value;
                      $flg = "false";
                  }
              }
          }
          if($flg == "true"){
              $description['description_novalue'] = 'No updates in Group Class.';
          }    
          $desc=json_encode($description);
          activity_feed(3,$_SESSION['groups']['id'], 'Group' , $class_id, 'group_classes', 'Group Updated Class',($_SESSION['groups']['fname'].' '.$_SESSION['groups']['lname']),"",$desc);
      }
      setNotifySuccess("Class Updated Successfully");
    }else{
      $group_classes_id = $pdo->insert("group_classes",$params);
        foreach($payperiod_dates as $value){
          $class_paydate_params = array(
                    'class_id' => $group_classes_id,
                    'group_id'=> $_SESSION['groups']['id'],
                    'pay_period' => $pay_period,
                  );
            $paydate = date("Y-m-d", strtotime($value));
            $class_paydate_params['paydate'] = $paydate;
          $group_classes_paydate = $pdo->insert("group_classes_paydates",$class_paydate_params);
        }
      setNotifySuccess("Class Added Successfully");
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
          'title'=>$_SESSION['groups']['rep_id'],
        ),
        'ac_message_1' =>' created Class ',
        'ac_red_2'=>array(
            'title'=>$class_name,
        ),
      ); 
      activity_feed(3, $_SESSION['groups']['id'], 'Group', $group_classes_id, 'group_classes','Group Created Class', $_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($description));
    }
    $response['status'] = "success";
    $response['message'] = "form_submited";
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