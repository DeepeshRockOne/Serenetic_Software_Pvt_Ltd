<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$is_clone = $_POST['is_clone']; 
$carrier_fee_id = !empty($_POST['carrier_fee_id']) ? explode(",", $_POST['carrier_fee_id']) : array();
$carrier_id = checkIsset($_POST['carrier_id']); 
$name = checkIsset($_POST['name']); 
$display_id = checkIsset($_POST['display_id']);
$contact_fname = checkIsset($_POST['contact_fname']);
$contact_lname = checkIsset($_POST['contact_lname']);
$phone = phoneReplaceMain(checkIsset($_POST['phone']));
$email = checkIsset($_POST['email']);  
$status = checkIsset($_POST['status']);
$is_clone = checkIsset($_POST['is_clone']);
$appointments = checkIsset($_POST['appointments']);
 
$validate->string(array('required' => true, 'field' => 'name', 'value' => $name), array('required' => 'Name is required')); 

// if (!$validate->getError('name')) {
//   $incr="";
//   $sch_params=array();
//   $sch_params[':name']=$name;
//   if (!empty($carrier_id)) {
//     $incr.=" AND md5(id)!=:id";
//     $sch_params[':id']=$carrier_id;
//   } 
//   $selectCarrier = "SELECT id FROM prd_fees WHERE setting_type='Carrier' AND name=:name $incr AND is_deleted='N' ";
//   $resultCarrier = $pdo->selectOne($selectCarrier, $sch_params);
//   if ($resultCarrier) {
//     $validate->setError("name", "This Carrier Name is already associated with another carrier account");
//   }
// }

// if(empty($carrier_fee_id)){
//   $validate->setError("carrier_fee_id", "Please Add Fee");
// }

$validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Carrier ID is required'));
  
if (!$validate->getError('display_id')) {
  $incr="";
  $sch_params=array();
  $sch_params[':display_id']=$display_id;
  if (!empty($carrier_id)) {
    $incr.=" AND md5(id)!=:id";
    $sch_params[':id']=$carrier_id;
  } 
  $selectCarrier = "SELECT id FROM prd_fees WHERE setting_type='Carrier' AND display_id=:display_id $incr AND is_deleted='N' ";
  $resultCarrier = $pdo->selectOne($selectCarrier, $sch_params);
  if ($resultCarrier) {
    $validate->setError("display_id", "This Carrier ID is already associated with another carrier account");
  }
}

$validate->string(array('required' => true, 'field' => 'status', 'max' => 255, 'value' => $status), array('required' => 'Status is required'));
$validate->string(array('required' => true, 'field' => 'contact_fname', 'max' => 255, 'value' => $contact_fname), array('required' => 'Contact Name is required'));
$validate->string(array('required' => true, 'field' => 'appointments', 'max' => 255, 'value' => $appointments), array('required' => 'Please choose any option'));

if(!empty($contact_fname)){
  $full_name = splitName($contact_fname);
  $contact_fname = $full_name['first_name'];
  $contact_lname = $full_name['last_name'];
  if($contact_fname==''){
    $validate->setError('contact_fname', 'First & Last Name is required');
  }
  if($contact_lname==''){
    $validate->setError('contact_fname', 'First & Last Name is required');
  }
}

$validate->phoneDigit(array('required' => true, 'field' => 'phone', 'value' => $phone), array('required' => 'Phone is required', 'invalid' => 'Invalid Phone Number'));
$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Invalid Email Address'));

if (!$validate->getError('email')) {
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // $incr="";
    // $sch_params=array();
    // $sch_params[':email']=$email;
    // if (!empty($carrier_id)) {
    //   $incr.=" AND md5(id)!=:id";
    //   $sch_params[':id']=$carrier_id;
    // } 
    // $selectEmail = "SELECT id FROM prd_fees WHERE setting_type='Carrier' AND email=:email $incr AND is_deleted='N' ";
    // $resultEmail = $pdo->selectOne($selectEmail, $sch_params);
    // if ($resultEmail) {
    //   $validate->setError("email", "This email is already associated with another Carrier account");
    // }
  } else {
      $validate->setError("email", "Valid Email is required");
  }
}

if ($validate->isValid()) {

  $insert_params = array(
    'name' => $name,
    'display_id' => $display_id,
    'setting_type' => 'Carrier',
    'contact_fname' => $contact_fname,
    'contact_lname' => $contact_lname,
    'phone' => $phone,
    'email' => $email,
    'status' => $status,
    'use_appointments' => $appointments,
  );

  $insert_params_key =  implode(",", array_keys($insert_params));
  $feesSql = "SELECT id,$insert_params_key FROM prd_fees WHERE md5(id)=:c_id AND is_deleted='N'";
  $feesRow = $pdo->selectOne($feesSql, array(":c_id" => $carrier_id));

  if(!empty($feesRow) && $is_clone=='N'){
    $carrier_id = $feesRow['id'];
    $update_where = array(
      'clause' => 'id = :id',
      'params' => array(':id' => $carrier_id)
    );
    $update_status = $pdo->update('prd_fees', $insert_params, $update_where);

    //************* Activity Code Start *************
      $oldVaArray = $feesRow;
      $NewVaArray = $insert_params;
      unset($oldVaArray['id']);

      $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
       
      if(!empty($checkDiff)){
        $activityFeedDesc['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id']),
          'ac_message_1' =>' Updated Carrier ',
          'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/manage_carrier.php?carrier_id='.md5($carrier_id),
            'title'=>$display_id,
          ),
        ); 
        
        if(!empty($checkDiff)){
          foreach ($checkDiff as $key1 => $value1) {
            $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
          } 
        }
        
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $carrier_id, 'carrier','Admin Updated Carrier', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
      }
    //************* Activity Code End   *************

    $response['message']="Carrier Updated successfully";
  }else{ 
    $carrier_id = $pdo->insert("prd_fees", $insert_params);

    //************* Activity Code Start *************
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Created Carrier ',
        'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/manage_carrier.php?carrier_id='.md5($carrier_id),
          'title'=>$display_id,
        ),
      ); 
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $carrier_id, 'carrier','Admin Created Carrier', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************

    $response['message']="Carrier created successfully";
  }

  if(!empty($carrier_fee_id)){
    foreach ($carrier_fee_id as $key => $value) {

      $updParam = array('prd_fee_id'=>$carrier_id);
      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $value
        )
      ); 
      $pdo->update('prd_main', $updParam, $updWhere); 

      $assignSql = "SELECT id FROM prd_assign_fees where fee_id=:fee_id ";
      $assignRow = $pdo->select($assignSql, array(":fee_id" => $value));
       
      if(!empty($assignRow)){
        foreach ($assignRow as $key => $val) {

          $updParam = array('prd_fee_id'=>$carrier_id);
          $updWhere = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $val['id']
            )
          ); 
          $pdo->update('prd_assign_fees', $updParam, $updWhere); 

        }
      }
    }
  } 
   
  $response['redirect_url']=$ADMIN_HOST.'/carrier.php';    
  $response['status']="success";
  
} else{
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();  
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>