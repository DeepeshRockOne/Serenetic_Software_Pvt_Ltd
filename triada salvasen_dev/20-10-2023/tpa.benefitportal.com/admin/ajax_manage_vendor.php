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
    $zipcode = $_POST['zipcode'];
    $old_address = $_POST['old_address'];
    $old_zip = $_POST['old_zipcode'];

    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));
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
              $response['errors'] = array("zipcode"=>$zipAddress['error_message']);
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

$is_clone = checkIsset($_POST['is_clone']);
$upload_type = checkIsset($_POST['upload_type']); 
$vendor_id = checkIsset($_POST['vendor_id']); 
$name = checkIsset($_POST['name']); 
$display_id = checkIsset($_POST['display_id']);
$contact_fname = checkIsset($_POST['contact_fname']);
$phone = phoneReplaceMain(checkIsset($_POST['phone']));
$email = checkIsset($_POST['email']); 
$address = checkIsset($_POST['address']);
$address2 = checkIsset($_POST['address2']);
$city = checkIsset($_POST['city']); 
$state = checkIsset($_POST['state']);
$zipcode = checkIsset($_POST['zipcode']);
$taxid = checkIsset($_POST['taxid']);
$vendor_fee_id = !empty($_POST['vendor_fee_id']) ? explode(",", $_POST['vendor_fee_id']) : array();
$vendor_attachment_id = !empty($_POST['vendor_attachment_id']) ? explode(",", $_POST['vendor_attachment_id']) : array();
$attachments = checkIsset($_FILES['vendor_attachements']);

if($upload_type=="file"){
  if(!empty($attachments)){
    $length = count($attachments['name']);
        
    $attachments_array = array();
    for($i=0; $i<$length; $i++){
      $attachments_array[$i]['name'] = $attachments['name'][$i];
      $attachments_array[$i]['type'] = $attachments['type'][$i];
      $attachments_array[$i]['tmp_name'] = $attachments['tmp_name'][$i];
      $attachments_array[$i]['error'] = $attachments['error'][$i];
      $attachments_array[$i]['size'] = $attachments['size'][$i];
    }
      
    if(!empty($attachments_array)){
      $files_info=array();
      $i=0;
      foreach($attachments_array as $file){
        $ticket_file = $file['name'];
        if (!empty($ticket_file)) {
          $file_type = explode("/",$file['type']);

          $file_name = $vendor_id.'_'.rand(1000, 9999).'_'.$file['name'];
          $file_path = $FEES_ATTACHMENS_DIR . $file_name;
          if (!file_exists($FEES_ATTACHMENS_DIR)) {
            mkdir($FEES_ATTACHMENS_DIR, 0777, true);
          }
          move_uploaded_file($file['tmp_name'], $file_path);
          chmod($file_path, 0777);

          $attachment_params = array(
            'prd_fee_id' => ($vendor_id)?$vendor_id:'',
            'type' => 'Vendor',
            'file_name' => $file_name,
            'file_path' => $file_path,
            'file_type' => $file['type'],
            'is_deleted' => 'N', 
          );
          $attachment_id = $pdo->insert("fees_attachments", $attachment_params);
          array_push($vendor_attachment_id, $attachment_id);
            
          $imageExt=array_reverse(explode(".", $file_name));
          if(strtolower($imageExt[0])=="jpg" || strtolower($imageExt[0])=="jpeg" || strtolower($imageExt[0])=="png" || strtolower($imageExt[0])=="gif" || strtolower($imageExt[0])=="tif"){
            $file_display_name = $file_name;
          }else{
            $file_display_name="img_placeholder.jpg";
          }

          $file_id = $attachment_id;
          $files_info[$i]['file_display_name']=$file_display_name;
          $files_info[$i]['file_name']=$file_name;
          $files_info[$i]['file_id']=$file_id;
          $i++;
          $response['attachment']=implode(",", $vendor_attachment_id);

        }
      }
      $response['status']="success_file";
      $response['files_info']=$files_info;
      $response['message']="Attachment Added successfully";
    }
  }
}else{
  $validate->string(array('required' => true, 'field' => 'name', 'value' => $name), array('required' => 'Name is required'));

  if (!$validate->getError('name')) {
    $incr="";
    $sch_params=array();
    $sch_params[':name']=$name;
    if (!empty($vendor_id)) {
      $incr.=" AND md5(id)!=:id";
      $sch_params[':id']=$vendor_id;
    } 
    $selectVendor = "SELECT id FROM prd_fees WHERE setting_type='Vendor' AND name=:name $incr AND is_deleted='N' ";
    $resultVendor = $pdo->selectOne($selectVendor, $sch_params);
    if ($resultVendor) {
      $validate->setError("name", "This Vendor Name is already associated with another Vendor account");
    }
  }

  $validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Vendor ID is required'));

  if (!$validate->getError('display_id')) {
    $incr="";
    $sch_params=array();
    $sch_params[':display_id']=$display_id;
    if (!empty($vendor_id)) {
      $incr.=" AND md5(id)!=:id";
      $sch_params[':id']=$vendor_id;
    } 

    $selectVendor = "SELECT id FROM prd_fees WHERE setting_type='Vendor' AND display_id=:display_id $incr AND is_deleted='N' ";
    $resultVendor = $pdo->selectOne($selectVendor, $sch_params);

    if ($resultVendor) {
      $validate->setError("display_id", "This Vendor ID is already associated with another Vendor account");
    }
  }

  $validate->string(array('required' => true, 'field' => 'contact_fname', 'max' => 255, 'value' => $contact_fname), array('required' => 'Contact Name is required'));

  if(!empty($contact_fname)){
    $full_name = splitName(trim($contact_fname));
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

  if (!empty($email)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $incr="";
        $sch_params=array();
        $sch_params[':email']=$email;
        if (!empty($vendor_id)) {
          $incr.=" AND md5(id)!=:id";
          $sch_params[':id']=$vendor_id;
        } 
        $selectEmail = "SELECT id FROM prd_fees WHERE setting_type='Vendor' AND email=:email $incr AND is_deleted='N' ";
        $resultEmail = $pdo->selectOne($selectEmail, $sch_params);
        if ($resultEmail) {
          $validate->setError("email", "This email is already associated with another vendor account");
        }
    } else {
        $validate->setError("email", "Valid Email is required");
    }
  }

  $validate->string(array('required' => true, 'field' => 'address', 'max' => 255, 'value' => $address), array('required' => 'Address is required'));
  if(!empty($address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address2)) {
      $validate->setError('address2','Special character not allowed');
  }
  $validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
  $validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
  $validate->string(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode), array('required' => 'Zipcode is required'));
 
  if(!$validate->getError('zipcode')){
    $getDetailOnPinCode=$pdo->selectOne("SELECT * FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$zipcode));
    if(!$getDetailOnPinCode){
      $validate->setError('zipcode', 'Validate zip code is required');
    } else {
      if(!$validate->getError('state')){
        $state_res = $pdo->selectOne("SELECT * FROM states_c WHERE name=:name", array(':name' => $state));
        if($getDetailOnPinCode['state_code'] != $state_res['short_name']){
          $validate->setError('zipcode', 'Validate zip code is required');
        }
      }
    }
  }

  if (!$validate->getError('zipcode')){
      include_once '../includes/function.class.php';
      $function_list = new functionsList();
      $zipAddress = $function_list->uspsCityVerification($zipcode);
      if($zipAddress['status'] !='success'){
          $validate->setError("zipcode",$zipAddress['error_message']);
      }
  }

  // if(empty($vendor_fee_id)){
  //   $validate->setError("vendor_fee_id", "Please Add Fee");
  // }
  
  if ($validate->isValid()) {

    $insert_params = array(
      'name' => $name,
      'display_id' => $display_id,
      'contact_fname' => $contact_fname,
      'contact_lname' => $contact_lname,
      'setting_type' => 'Vendor',
      'phone' => $phone,
      'email' => $email,
      'address' => $address,
      'address2' => $address2,
      'city' => $city,
      'state' => $state,
      'zipcode' => $zipcode,
      'tax_id' => $taxid,
    );

    $insert_params_key =  implode(",", array_keys($insert_params));
    $feesSql = "SELECT id,$insert_params_key FROM prd_fees WHERE md5(id)=:v_id AND is_deleted='N'";
    $feesRow = $pdo->selectOne($feesSql, array(":v_id" => $vendor_id));

    if(!empty($feesRow) && $is_clone=='N'){ 
      $vendor_id = $feesRow['id'];
      $update_where = array(
        'clause' => 'id = :id',
        'params' => array(':id' => $feesRow['id'])
      );
      $pdo->update('prd_fees', $insert_params, $update_where);

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
            'ac_message_1' =>' Updated Vendor ',
            'ac_red_2'=>array(
              'href'=> $ADMIN_HOST.'/manage_vendor.php?vendor_id='.md5($vendor_id),
              'title'=>$display_id,
            ),
          ); 
          
          if(!empty($checkDiff)){
            foreach ($checkDiff as $key1 => $value1) {
              $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
            } 
          }
          
          activity_feed(3, $_SESSION['admin']['id'], 'Admin', $vendor_id, 'vendor','Admin Updated Vendor', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
        }
      //************* Activity Code End   *************

      $response['message']="Vendor Updated successfully";
    }else{
      if($is_clone=='N'){
        if(empty($vendor_fee_id)){
          $insert_params['status']="Inactive";
        }else{
          $insert_params['status']="Active";
        } 
      }
      $vendor_id = $pdo->insert("prd_fees", $insert_params);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Created Vendor ',
          'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/manage_vendor.php?vendor_id='.md5($vendor_id),
            'title'=>$display_id,
          ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $vendor_id, 'vendor','Admin Created Vendor', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************

      $response['message']="Vendor created successfully";
    }
      
    if(!empty($vendor_attachment_id)){
      foreach ($vendor_attachment_id as $key => $value) {
        $updParam = array('prd_fee_id'=>$vendor_id);
        $updWhere = array(
          'clause' => 'id = :id',
          'params' => array(':id' => $value)
        );
        $pdo->update('fees_attachments', $updParam, $updWhere);
      }
    }

    if(!empty($vendor_fee_id)){
      foreach ($vendor_fee_id as $key => $value) {

        $updParam = array('prd_fee_id'=>$vendor_id);
        $updWhere = array(
          'clause' => 'id = :id',
          'params' => array(':id' => $value)
        ); 
        $pdo->update('prd_main', $updParam, $updWhere); 

        $assignSql = "SELECT id FROM prd_assign_fees where fee_id=:fee_id ";
        $assignRow = $pdo->select($assignSql, array(":fee_id" => $value));
         
        if(!empty($assignRow)){
          foreach ($assignRow as $key => $val) {
            $updParam = array('prd_fee_id'=>$vendor_id);
            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(':id' => $val['id'])
            ); 
            $pdo->update('prd_assign_fees', $updParam, $updWhere); 
          }
        }
      }
    } 
     
    $response['redirect_url']=$ADMIN_HOST.'/vendors.php';    
    $response['status']="success";  
  } else{
    $response['status'] = "fail";
    $response['errors'] = $validate->getErrors();  
  }
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>