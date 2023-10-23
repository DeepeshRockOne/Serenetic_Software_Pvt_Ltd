<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']);
if($is_address_ajaxed){
    $response = array("status"=>'success');
    $address = $_POST['address'];
    $address_2 = checkIsset($_POST['address2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zipcode = $_POST['zip'];
    $old_address = $_POST['old_address'];
    $old_zip = $_POST['old_zip'];

    $validate->digit(array('required' => true, 'field' => 'zip', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));
    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
      include_once '../includes/function.class.php';
      $function_list = new functionsList();
      $zipAddress = $function_list->uspsCityVerification($zipcode);

      if($old_address != $address || $zipcode!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
          if($zipAddress['status'] =='success'){
              $response['city'] = $zipAddress['city'];
              $response['state'] = $getStateNameByShortName[$zipAddress['state']];
              $response['zip_response_status']='success';

              $tmpAdd1=$address;
              $tmpAdd2=!empty($address_2) ? $address_2 : '#';
              $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zipcode);

              if(!empty($address_response)){
                  if($address_response['status']=='success'){
                      $response['address'] = $address_response['address'];
                      $response['address2'] = $address_response['address2'];
                      $response['city'] = $address_response['city'];
                      $response['state'] = $getStateNameByShortName[$address_response['state']];
                      $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zipcode;
                      $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
                      $response['zip_response_status']='';
                      $response['address_response_status']='success';
                  }
              }
          }else if($zipAddress['status'] =='fail'){
              $response['status'] = 'fail';
              $response['errors'] = array("zip"=>$zipAddress['error_message']);
          }
      }
    }else{
      $errors = $validate->getErrors();
      $response['status'] = 'fail';
      $response['errors'] = $errors;
    }
    header('Content-type: application/json');
    echo json_encode($response);
    exit();
}

$name = isset($_POST['name']) ? $_POST['name'] : ""; 
$display_id = isset($_POST['display_id']) ? $_POST['display_id'] : "";
$fname = isset($_POST['fname']) ? $_POST['fname'] : "";
$lname = isset($_POST['lname']) ? $_POST['lname'] : "";
$phone = isset($_POST['phone']) ? phoneReplaceMain($_POST['phone']) : "";
$email = isset($_POST['email']) ? $_POST['email'] : "";  
$address = isset($_POST['address']) ? $_POST['address'] : "";
$address2 = isset($_POST['address2']) ? $_POST['address2'] : "";
$city = isset($_POST['city']) ? $_POST['city'] : ""; 
$zip = isset($_POST['zip']) ? $_POST['zip'] : "";
$state = isset($_POST['state']) ? $_POST['state'] : "";
$is_clone = isset($_POST['is_clone']) ? $_POST['is_clone'] : "";
$fee_id = isset($_POST['fee_id']) ? $_POST['fee_id'] : "";
$benefits = !empty($_POST['content']) ? $_POST['content'] : array();
$temp_fee_products = isset($_SESSION['temp_fee_products']) ? $_SESSION['temp_fee_products'] : array();
$temp_fees = isset($_SESSION['temp_fee_products']) ? $_SESSION['temp_fee_products'] : array();
$assign_fee_ids = isset($_POST['ids']) ? $_POST['ids'] : "";


$validate->string(array('required' => true, 'field' => 'name', 'value' => $name), array('required' => 'Name is required'));
$validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Vendor ID is required'));
$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
$validate->phoneDigit(array('required' => true, 'field' => 'phone', 'value' => $phone), array('required' => 'Phone is required', 'invalid' => 'Invalid Phone Number'));
$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Invalid Email Address'));
$validate->string(array('required' => true, 'field' => 'address', 'max' => 255, 'value' => $address), array('required' => 'Address is required'));
if(!empty($address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address2)) {
    $validate->setError('address2','Special character not allowed');
}
$validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
$validate->string(array('required' => true, 'field' => 'zip', 'value' => $zip), array('required' => 'Zipcode is required'));

if (!empty($display_id)) {
    $incr="";
    $sch_params=array();
    $sch_params[':display_id']=$display_id;
    if (!empty($fee_id)) {
      $incr.=" AND id!=:id";
      $sch_params[':id']=$fee_id;
    } 
    $selectVendor = "SELECT id FROM prd_fees WHERE display_id=:display_id $incr";
    $resultVendor = $pdo->selectOne($selectVendor, $sch_params);
    if ($resultVendor) {
      $validate->setError("display_id", "This Membership ID is already associated with another Membership");
    }
}
if (!empty($email)) {
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      
      // $incr="";
      // $sch_params=array();
      // $sch_params[':email']=$email;
      // if (!empty($fee_id) && $is_clone == 'N') {
      //   $incr.=" AND id!=:id";
      //   $sch_params[':id']=$fee_id;
      // } 
      // $selectEmail = "SELECT id FROM prd_fees WHERE email=:email $incr";
      // $resultEmail = $pdo->selectOne($selectEmail, $sch_params);
      // if ($resultEmail) {
      //   $validate->setError("email", "This email is already associated with another Membership");
      // }
  } else {
      $validate->setError("email", "Valid Email is required");
  }
}
if(!$validate->getError('zip')){
    $getDetailOnPinCode=$pdo->selectOne("SELECT * FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$zip));
      if(!$getDetailOnPinCode){
        $validate->setError('zip', 'Validate zip code is required');
      } else {
        if(!$validate->getError('state')){
          $state_res = $pdo->selectOne("SELECT * FROM `states_c` WHERE name = :name", array(':name' => $state));
          if($getDetailOnPinCode['state_code'] != $state_res['short_name']){
            $validate->setError('zip', 'Validate zip code is required');
          }
        }
      }
}

if (!$validate->getError('zip')){
    include_once '../includes/function.class.php';
    $function_list = new functionsList();
    $zipAddress = $function_list->uspsCityVerification($zip);
    if($zipAddress['status'] !='success'){
        $validate->setError("zip",$zipAddress['error_message']);
    }
}

if ($validate->isValid()) {

  $insert_params = array(
      'setting_type' => 'membership',
      'name' => $name,
      'display_id' => $display_id,
      'contact_fname' => $fname,
      'contact_lname' => $lname,
      'phone' => $phone,
      'email' => $email,
      'address' => $address,
      'address2' => $address2,
      'city' => $city,
      'state' => $state,
      'zipcode' => $zip,
      'benefits' => trim($benefits),
  );
  
  if(!empty($fee_id) && $is_clone=='N'){
    $insert_params['updated_at']="msqlfunc_NOW()";
    $update_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $fee_id
        )
    );
    $oldValue = $pdo->selectOne("SELECT * FROM prd_fees WHERE id = :id",array(':id' => $fee_id));
    $newValue = $insert_params;
    $update_status = $pdo->update('prd_fees', $insert_params, $update_where);
    $response['message']="Membership Updated successfully";

    unset($newValue['updated_at']);
    $checkDiff=array_diff_assoc($newValue, $oldValue);
    // pre_print($checkDiff);
    if(!empty($checkDiff)){

      $activityFeedDesc['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id']),
        'ac_message_1' =>' Updated Membership',
      ); 
      
      // $str = "";
      foreach ($checkDiff as $key1 => $value1) {
        $activityFeedDesc['key_value']['desc_arr'][$key1]= 'From '.$oldValue[$key1].' To '.$newValue[$key1];
        // $str .= $key1 . 'from ' . $oldValue[$key1] . ' to ' . $newValue[$key1] . ' ';
      }

      $activityFeedDesc['ac_message']['ac_red_2']=array(
        'href'=>$ADMIN_HOST.'/memberships_mange.php?id='.md5($fee_id),
        'title'=>$display_id
      ); 

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_id, 'prd_fees','Admin Updated Membership', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
    }



  }else{
    $insert_params['status']="Active";
    $insert_params['created_at']="msqlfunc_NOW()";
    $insert_params['updated_at']="msqlfunc_NOW()";
    $fee_id = $pdo->insert("prd_fees", $insert_params);
    unset($_SESSION['temp_fee_products']);

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' created membership ',
      'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/memberships_mange.php?id='.md5($fee_id),
          'title'=>$display_id,
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_id, 'prd_fees','created membership', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    $response['message']="Membership created successfully";
  }

  if(!empty($temp_fees) && $is_clone == 'N'){
    foreach ($temp_fees as $key => $value) {

      $updParam = array('prd_fee_id'=>$fee_id);
      $updWhere = array(
        'clause' => 'fee_id = :id',
        'params' => array(
            ':id' => $value
        )
      );
      $update_status = $pdo->update('prd_assign_fees', $updParam, $updWhere);

      $updParam = array('prd_fee_id'=>$fee_id);
      $updWhere = array(
        'clause' => 'association_fee_id = :id',
        'params' => array(
            ':id' => $value
        )
      );
      $update_status = $pdo->update('association_assign_by_state', $updParam, $updWhere);

      $products = $pdo->selectOne("SELECT product_id FROM prd_assign_fees WHERE fee_id = :fee_id",array(':fee_id' => $value));

      if($products){
        $temp_product_id = $products['product_id'];
        $product_res = $pdo->selectOne("SELECT id,prd_fee_id FROM prd_main WHERE id = :id",array(':id' => $temp_product_id));
        if($product_res){
            $prd_fee_ids = array($fee_id);
            if($product_res['prd_fee_id']){
              $prd_fee_ids = explode(',', $product_res['prd_fee_id']);
              if(!in_array($fee_id, $prd_fee_ids)){
                array_push($prd_fee_ids, $fee_id);
              }
            }
            $updParam = array('prd_fee_id'=>implode(',', $prd_fee_ids));
            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(
                  ':id' => $product_res['id']
              )
            );
            $update_status = $pdo->update('prd_main', $updParam, $updWhere);
        }
      }
    }
  }

  if($is_clone == 'Y'){
    if($assign_fee_ids || $temp_fees){
      $ids = "";
      if($assign_fee_ids){
        $ids = $assign_fee_ids;
      }
      if($temp_fees){
        if($ids){
          $ids .= ',' . implode(',', $temp_fees);
        }else{
          $ids .= implode(',', $temp_fees);
        }
      }

      if($ids){
        $prd_res = $pdo->select("SELECT * FROM prd_main where id in($ids)");
        if($prd_res){
          foreach ($prd_res as $k => $v) {

            if(!in_array($v['id'], $temp_fees)){

              $Insert_params = $v;
              unset($Insert_params['id']);
              $Insert_params['create_date'] = 'mysql_func_now()';
              $Insert_params['product_code'] = get_membership_fee_id();

              $new_product_id = $pdo->insert('prd_main',$Insert_params);

              $prd_matrix = $pdo->selectOne("SELECT * from prd_matrix where product_id = :id",array(':id' => $v['id']));

              if($prd_matrix){
                $Insert_params = $prd_matrix;
                unset($Insert_params['id']);
                $Insert_params['create_date'] = 'mysql_func_now()';
                $Insert_params['product_id'] = $new_product_id;
                $new_product_matrix_id = $pdo->insert('prd_matrix',$Insert_params);
              }

              $prd_assign_fees = $pdo->select("SELECT * from prd_assign_fees where fee_id = :id",array(':id' => $v['id']));
              if($prd_assign_fees){
                foreach ($prd_assign_fees as $key => $value) {
                  $inser_fee_details = array(
                    "product_id" => $value['product_id'],
                    "fee_id" => $new_product_id,
                    "prd_fee_id" => $fee_id,
                    "created_at" => 'msqlfunc_NOW()'
                  );
                  
                  $prd_assign_fees = $pdo->insert('prd_assign_fees',$inser_fee_details);

                  $associon_states = $pdo->select("SELECT * from association_assign_by_state where association_fee_id = :id",array(':id' => $v['id']));

                  if($associon_states){
                    foreach ($associon_states as $key => $value) {
                      $Insert_params = array();
                      $Insert_params['product_id'] = $value['product_id'];
                      $Insert_params['states'] = $value['states'];
                      $Insert_params['is_deleted'] = $value['is_deleted'];
                      $Insert_params['association_fee_id'] = $prd_assign_fees;
                      $Insert_params['prd_fee_id'] = $fee_id;
                      $Insert_params['created_at'] = 'mysql_func_now()';
                      $new_product_matrix_id = $pdo->insert('association_assign_by_state',$Insert_params);
                    }
                  }
                }
              }
            }else{
              $updParam = array('prd_fee_id'=>$fee_id);
              $updWhere = array(
                'clause' => 'fee_id = :id',
                'params' => array(
                    ':id' => $v['id']
                )
              );
              $update_status = $pdo->update('prd_assign_fees', $updParam, $updWhere);

              $updParam = array('prd_fee_id'=>$fee_id);
              $updWhere = array(
                'clause' => 'association_fee_id = :id',
                'params' => array(
                    ':id' => $v['id']
                )
              );
              $update_status = $pdo->update('association_assign_by_state', $updParam, $updWhere);
            }
          }
        }
      }
    }
  }


  setNotifySuccess($response['message']);
  $response['redirect_url']=$ADMIN_HOST.'/memberships.php';    
  $response['status']="success";
  $response['fee_id']=$fee_id;
  
} else{
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();  
}


header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>