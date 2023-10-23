<?php
include_once 'layout/start.inc.php';
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();
$activityMsg='Update';
$adminId=$_SESSION['admin']['id'];
$assignGroup = !empty($_POST['assign_group']) ? $_POST['assign_group'] : '';
$billingSetting = !empty($_POST['billing_setting']) ? $_POST['billing_setting'] : '';

$variationPriorDay = !empty($_POST['variation_prior_day']) ? $_POST['variation_prior_day'] : '';
$isAutoPaymentSet = !empty($_POST['set_auto_payment']) ? $_POST['set_auto_payment'] : ($_POST['set_auto_payment_inside_system'] == 'Y' ? 'N' : '');
$isAutoPaymentSetInsideSystem = !empty($_POST['set_auto_payment_inside_system']) ? $_POST['set_auto_payment_inside_system'] : ($_POST['set_auto_payment'] == 'Y' ? 'N' : '');
$autoPaymentDays = !empty($_POST['auto_payment_days']) ? $_POST['auto_payment_days'] : '';

$rule_id = !empty($_POST['rule_id']) ? $_POST['rule_id'] : 0;

if(empty($assignGroup)){
  $validate->setError("assign_group","Please Select Group");
}

if(empty($variationPriorDay)){
  $validate->setError("variation_prior_day","Please Select Prior Day");
}
if(empty($isAutoPaymentSet)){
  $validate->setError("set_auto_payment","Please Select Option");
}
if(empty($isAutoPaymentSetInsideSystem)){
  $validate->setError("set_auto_payment_inside_system","Please Select Option");
}
if(empty($autoPaymentDays) && ($isAutoPaymentSet=='Y' || $isAutoPaymentSetInsideSystem=='Y')){
  $validate->setError("auto_payment_days","Please Select Auto Payment Day");
}else if($variationPriorDay <= $autoPaymentDays){
  $validate->setError("auto_payment_days","Auto Payment prior days must be less than List Bill generation prior days");
}


if( !empty($assignGroup) && !empty($billingSetting) && $billingSetting=="days_prior_pay_period"){
      $grpClassesSql = "SELECT count(id) as ClassCount FROM group_classes where is_deleted='N' and group_id = :group_id";
      $grpClassesCount = $pdo->selectOne($grpClassesSql,array(":group_id"=>$assignGroup));

      $grpClassesHavingPaydatesSql = "SELECT count(distinct class_id) as ClassHavePayDateCount FROM group_classes_paydates where is_deleted='N' and group_id = :group_id";
      $grpClassesHavingPaydatesCount = $pdo->selectOne($grpClassesHavingPaydatesSql,array(":group_id"=>$assignGroup));

      if( !empty($grpClassesCount) && !empty($grpClassesHavingPaydatesCount) ){
          if( $grpClassesCount['ClassCount']!=$grpClassesHavingPaydatesCount['ClassHavePayDateCount'] ){
            $validate->setError("assign_group","Pay Dates Not Set for All Classes");
          }
      }
}

if ( $validate->isValid() ) {
  $updatedGroup = array();
  $updatedArr = array();
    if(!empty($assignGroup)){
          $sqlListBillOptions = "SELECT id FROM list_bill_options where rule_type='Variation' and is_deleted='N' and id=:id and group_id = :group_id";
          $resListBillOptions = $pdo->selectOne($sqlListBillOptions,array(":id"=>$rule_id,":group_id"=>$assignGroup));
          
          $update_param = array(
              "group_id" => $assignGroup,
              "rule_type" => 'Variation',
              "billing_setting"=>$billingSetting,
              "days_prior_pay_period"=>NULL,
              "auto_set_payment_received"=>'N',
              "auto_set_payment_received_inside_sys"=>'N',
              "auto_payment_days"=>NUll,
          );
          
          $update_param['days_prior_pay_period']= (!empty($variationPriorDay)) ? $variationPriorDay : $update_param['days_prior_pay_period'];
          $update_param['auto_set_payment_received']= (!empty($isAutoPaymentSet)) ? $isAutoPaymentSet : $update_param['auto_set_payment_received'];
          $update_param['auto_set_payment_received_inside_sys']= (!empty($isAutoPaymentSetInsideSystem)) ? $isAutoPaymentSetInsideSystem : $update_param['auto_set_payment_received_inside_sys'];
          $update_param['auto_payment_days']= (!empty($autoPaymentDays)) ? $autoPaymentDays : $update_param['auto_payment_days'];

          if( !empty($resListBillOptions) ){
            $updatedArr = $update_param;
            $upd_where = array(
              'clause' => 'id = :id',
              'params' => array(
                  ':id' => $resListBillOptions['id'],
              ),
            );
            $updated_param['list_bill_options'] = $pdo->update('list_bill_options', $update_param, $upd_where,true);
          }else{      
            $update_param['admin_id']=$adminId;
            $inserted_param['list_bill_options'] = $update_param;
            $pdo->insert("list_bill_options", $update_param);
          } 
    }
    $flg = false;

    if( !empty($inserted_param) ){
      $GroupDetailSql="SELECT CONCAT(business_name,' (',rep_id,')') as groups from customer where id=:group_id";
      $GroupParam=array(
        ':group_id'=>$assignGroup,
      );
      $insertedGroup = $pdo->selectOne($GroupDetailSql,$GroupParam);

      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Inserted List Bill Option For',
        'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($assignGroup),
          'title'=>$insertedGroup['groups'],
        ),
      ); 
      
      foreach($inserted_param as $optionName => $groupOption){
       
        if(!empty($groupOption)){
          unset($groupOption['group_id']);
          unset($groupOption['admin_id']);
          foreach($groupOption as $key2 => $val){
            $description['&nbsp;&nbsp;&nbsp;'.$key2] = ucwords(str_replace('_',' ',$key2)) .": ".ucwords(str_replace('_',' ',$val));
            $flg = "false";
          }
        }
      }
      $activityMsg='Insert';
    }

    if(!empty($updated_param)){
      $GroupDetailSql="SELECT CONCAT(business_name,' (',rep_id,')') as groups from customer where id=:group_id";
      $GroupParam=array(
        ':group_id'=>$assignGroup,
      );
      $updatedGroup = $pdo->selectOne($GroupDetailSql,$GroupParam);
      
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Updated List Bill Option For',
        'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($assignGroup),
          'title'=>$updatedGroup['groups'],
        ),
      ); 

      foreach($updated_param as $optionName => $groupOption){
        if(!empty($groupOption)){
          $groupOption=array_filter($groupOption);
          unset($groupOption['group_id']);
          unset($groupOption['rule_type']);
          unset($groupOption['admin_id']);
          if(isset($groupOption['billing_setting'])){
            $description['&nbsp;&nbsp;&nbsp;billing_setting'] ="Billing Setting" .": From ".ucwords(str_replace('_',' ',$groupOption['billing_setting'])).' to '.ucwords(str_replace('_',' ',$updatedArr['billing_setting']));
            $flg = true;
          }else{
            foreach($groupOption as $key2 => $val){
              if(!empty($val) && !empty($updatedArr[$key2])){
                $description['&nbsp;&nbsp;&nbsp;'.$key2] = ucwords(str_replace('_',' ',$key2)) .": From ".$val.' to '.ucwords(str_replace('_',' ',$updatedArr[$key2]));
                 $flg = true;
              }
            }
          }

        }
      }
    }

    if($flg){
      $desc=json_encode($description);
      activity_feed(3,$_SESSION['admin']['id'],'Admin','0','Variation List Bill Options','Admin '.$activityMsg.' Variation List Bill Options',"","",$desc);
    }

    $response['Activity']=$activityMsg;
    $response['status'] = 'success';
}
else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>