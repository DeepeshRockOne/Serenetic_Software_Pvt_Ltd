<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';

  $validate = new Validation();
  $response = array();
  
  $save_type = !empty($_POST['save_type']) ? $_POST['save_type'] : '';
  $group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : '';
  $is_clone = !empty($_POST['is_clone']) ? $_POST['is_clone'] : '';
  $tmp_coverage_id = !empty($_POST['tmp_coverage_id']) ? $_POST['tmp_coverage_id'] : '';
  $coverage_id = !empty($_POST['coverage_id']) ? $_POST['coverage_id'] : '';
  
  $coverage_period_name = !empty($_POST['coverage_period_name']) ? $_POST['coverage_period_name'] : '';
  $display_id = !empty($_POST['display_id']) ? $_POST['display_id'] : '';
  $status = !empty($_POST['status']) ? $_POST['status'] : '';
  $coverage_period_start = !empty($_POST['coverage_period_start']) ? $_POST['coverage_period_start'] : '';
  $coverage_period_end = !empty($_POST['coverage_period_end']) ? $_POST['coverage_period_end'] : '';


  $validate->string(array('required' => true, 'field' => 'coverage_period_name', 'value' => $coverage_period_name), array('required' => 'Plan Period Name is required'));
  $validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Coverage Period ID is required'));
  $validate->string(array('required' => true, 'field' => 'status', 'value' => $status), array('required' => 'Status is required'));
  $validate->string(array('required' => true, 'field' => 'coverage_period_start', 'value' => $coverage_period_start), array('required' => 'Plan Period Start Date is required'));
  $validate->string(array('required' => true, 'field' => 'coverage_period_end', 'value' => $coverage_period_end), array('required' => 'Plan Period End Date is required'));

  if(!empty($coverage_period_start)){
    $check_coverage_period_start=validateDate($coverage_period_start,"m/d/Y");
    if(!$check_coverage_period_start){
      $validate->setError("coverage_period_start","Enter Valid Date");
    }
  }
  if(!empty($coverage_period_end)){
    $check_coverage_period_end=validateDate($coverage_period_end,"m/d/Y");
    if(!$check_coverage_period_end){
      $validate->setError("coverage_period_end","Enter Valid Date");
    }
  }
  if(strtotime($coverage_period_start) >= strtotime($coverage_period_end)){
    $validate->setError("coverage_period_start","Enter Valid Date");
  }
  if (!$validate->getError('coverage_period_start') && !$validate->getError('coverage_period_end')) {
    $coverage_period_start = date("Y-m-d",strtotime($coverage_period_start));
    $coverage_period_end = date("Y-m-d",strtotime($coverage_period_end));
    $incr="";

    $whrCoverage=array(
      ":group_id"=>$group_id,
      ":start_date"=>$coverage_period_start,
      ":end_date"=>$coverage_period_end
    );

    if(!empty($coverage_id)){
      $incr.=" AND md5(id) != :id";
      $whrCoverage[':id']=$coverage_id;
    }
    $sqlCoverage= "SELECT id FROM group_coverage_period gc WHERE gc.is_deleted='N' AND group_id = :group_id AND ((gc.coverage_period_start BETWEEN :start_date AND :end_date) OR (gc.coverage_period_end BETWEEN :start_date AND :end_date)) $incr";
    $resCoverage = $pdo->select($sqlCoverage,$whrCoverage);

    if(!empty($resCoverage)){
       $validate->setError("coverage_period_start","Plan Already added");
    }
  }

  if (!$validate->getError('display_id')) {
    $schParams=array(":display_id"=>$display_id);
    $incr='';

    if(!empty($coverage_id)){
      $incr.=" AND md5(id) != :id";
      $schParams[':id']=$coverage_id;
    }
    $sqlCoverage="SELECT id FROM group_coverage_period where display_id=:display_id AND is_deleted='N' $incr";
    $resCoverage=$pdo->selectOne($sqlCoverage,$schParams);

    if(!empty($resCoverage)){
      $validate->setError('display_id',"Plan ID Already Exists");
    }
  }

  if ($validate->isValid()) {

    $params=array(
      'group_id'=>$group_id,
      'coverage_period_name'=>$coverage_period_name,
      'display_id'=>$display_id,
      'status'=>$status,
      'coverage_period_start'=>$coverage_period_start,
      'coverage_period_end'=>$coverage_period_end,
    );
    $tmp_id = '';
    $activity_feed_coverage = array();
    if(!empty($coverage_id)){
      $upd_where = array(
        'clause' => 'md5(id) = :id',
        'params' => array(
          ':id' => $coverage_id,
        ),
      );
      $activity_feed_coverage['coverage'] = $pdo->update('group_coverage_period',$params,$upd_where,true);     
    }else{
      $tmp_id = $pdo->insert('group_coverage_period',$params);

      $coverage_id = md5($tmp_id);

      if($is_clone =='Y' && !empty($tmp_coverage_id)){
         $sqlOffering = "SELECT * FROM group_coverage_period_offering WHERE md5(group_coverage_period_id)=:tmp_coverage_id AND is_deleted='N'";
         $resOffering = $pdo->select($sqlOffering,array(":tmp_coverage_id"=>$tmp_coverage_id));

         if(!empty($resOffering)){
            foreach ($resOffering as $key => $value) {
              $params = array(
                'group_id'=>$group_id,
                'group_coverage_period_id'=>$tmp_id,
                'class_id'=>$value['class_id'],
                'open_enrollment_start'=>$value['open_enrollment_start'],
                'open_enrollment_end'=>$value['open_enrollment_end'],
                'first_coverage_date'=>$value['first_coverage_date'],
                'waiting_restriction_on_open_enrollment'=>$value['waiting_restriction_on_open_enrollment'],
                'allow_future_effective_date'=>$value['allow_future_effective_date'],
                'allowed_range'=>$value['allowed_range'],
                'products'=>$value['products'],
                'is_contribution'=>$value['is_contribution'],
                'display_contribution_on_enrollment'=>$value['display_contribution_on_enrollment'],
                'status'=>"Inactive",
              );
              $offering_id = $pdo->insert("group_coverage_period_offering",$params);

              $sqlCheck = "SELECT * FROM group_coverage_period_contributions WHERE group_coverage_period_offering_id=:id AND is_deleted='N'";
              $whrCheck = array(
                ":id"=>$value['id'],
              );
              $resCheck = $pdo->select($sqlCheck,$whrCheck);

              if(!empty($resCheck)){
                foreach ($resCheck as $tmpKey => $tmpValue) {
                    $params = array(
                      'group_id'=>$group_id,
                      'group_coverage_period_id'=>$tmp_id,
                      'group_coverage_period_offering_id'=>$offering_id,
                      'class_id'=>$value['class_id'],
                      'product_id'=>$tmpValue['product_id'],
                      'plan_id'=>$tmpValue['plan_id'],
                      'type'=>($value['is_contribution'] == 'Y' ? $tmpValue['type'] : 'Percentage'),
                      'con_value'=>($value['is_contribution'] == 'Y' ? $tmpValue['con_value'] : 0),
                    );
                    $contribution_id = $pdo->insert("group_coverage_period_contributions",$params);
                }
              }
            }
         }
      }
      setNotifySuccess("Plan Period Added Successfully.");
    }

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
        'title'=>$_SESSION['groups']['rep_id'],
      )
    ); 

    $entity_action ='';
    $inesrtActivity = false;
    $class_id = '';
    if($tmp_id!=''){
      $inesrtActivity = true;
      $class_id = $tmp_id;
      $description['ac_message']['ac_message_1']=' created Plan Period ';
      $description['ac_message']['ac_red_2']['title']=$display_id;
      $entity_action = 'Group Created Plan Period';
      
    }else if($activity_feed_coverage['coverage']){
      $inesrtActivity = true;
      $class_id = getname('group_coverage_period',$coverage_id,'id','md5(id)');
      $description['ac_message']['ac_message_1']=' updated Coverage Period ';
      $description['ac_message']['ac_red_2']['title']=$display_id;
      $entity_action = 'Group Updated Plan Period';
      
      foreach($activity_feed_coverage as $key => $value){
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
      setNotifySuccess("Plan Period updated Successfully.");
    }

    if($inesrtActivity){
      activity_feed(3, $_SESSION['groups']['id'], 'Group', $class_id, 'group_coverage_period',$entity_action, $_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($description));
    }

    if($save_type=="add_offering"){
      $response['href']="offering_coverage_periods.php?coverage=".$coverage_id;
    }
    $response['status'] = "success";
    $response['coverage_id'] = $coverage_id;
    
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