<?php 
  include_once dirname(__FILE__) . '/layout/start.inc.php';

  $validate = new Validation();
  $response = array();

  $is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']);
  if($is_address_ajaxed){

      $response = array("status"=>'success');
      $address = $_POST['address'];
      $address_2 = checkIsset($_POST['address_2']);
      $city = $_POST['city'];
      $old_city = $_POST['old_city'];
      $state = checkIsset($_POST['state']);
      $zipcode = $_POST['zipcode'];
      $old_address = $_POST['old_address'];
      $old_zip = $_POST['old_zipcode'];

      $validate = new Validation();

      $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

      $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
      if($validate->isValid()){
          include_once '../includes/function.class.php';
          $function_list = new functionsList();
          $zipAddress = $function_list->uspsCityVerification($zipcode);

          if($old_address != $address || $zipcode!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state || $old_city != $city){
              
              if($zipAddress['status'] =='success'){
                  $response['city'] = $zipAddress['city'];
                  $response['state'] = $allStateResByName[$getStateNameByShortName[$zipAddress['state']]]['id'];
                  $response['zip_response_status']='success';

                  $tmpAdd1=$address;
                  $tmpAdd2=!empty($address_2) ? $address_2 : '#';
                  $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zipcode);
                  
                  if(!empty($address_response)){
                      if($address_response['status']=='success'){
                          $response['address'] = $address_response['address'];
                          $response['address2'] = $address_response['address2'];
                          $response['city'] = $address_response['city'];
                          $response['state'] = $allStateResByName[$getStateNameByShortName[$address_response['state']]]['id'];
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
  
  $group_id = $_SESSION['groups']['id'];
  $is_valid_address = !empty($_POST['is_valid_address']) ? $_POST['is_valid_address'] : '';
  
  $tag_from = !empty($_POST['tag_from']) ? $_POST['tag_from'] : '';
  $existing_tag = !empty($_POST['existing_tag']) ? $_POST['existing_tag'] : '';
  $new_tag = !empty($_POST['new_tag']) ? $_POST['new_tag'] : '';
  
  $enrollee_id = !empty($_POST['enrollee_id']) ? $_POST['enrollee_id'] : '';
  $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '';
  $employee_type = !empty($_POST['employee_type']) ? $_POST['employee_type'] : '';
  $hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : '';
  $termination_date = !empty($_POST['termination_date']) ? $_POST['termination_date'] : '';  
  $fname = !empty($_POST['fname']) ? $_POST['fname'] : '';
  $lname = !empty($_POST['lname']) ? $_POST['lname'] : '';
  $address = !empty($_POST['address']) ? $_POST['address'] : '';
  $address_2 = !empty($_POST['address_2']) ? $_POST['address_2'] : '';
  $city = !empty($_POST['city']) ? $_POST['city'] : '';
  $state = !empty($_POST['state']) ? $_POST['state'] : '';
  $found_state_id = 0;
  $zipcode = !empty($_POST['zipcode']) ? $_POST['zipcode'] : '';
  $gender = !empty($_POST['gender']) ? $_POST['gender'] : '';
  $dob = !empty($_POST['dob']) ? $_POST['dob'] : '';
  $ssn = !empty($_POST['ssn']) ? $_POST['ssn'] : '';
  $ssn = phoneReplaceMain($ssn);
  $ssn_last_four_digit = '';
  if(!empty($ssn)){
    $ssn_last_four_digit=substr($ssn,-4,4);
  }
  $email = checkIsset($_POST['email']);
  $phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
  $phone = phoneReplaceMain($phone);
  $class_id = !empty($_POST['class_id']) ? $_POST['class_id'] : '';
  $coverage_id = !empty($_POST['coverage_id']) ? $_POST['coverage_id'] : '';
  $allowedCoverage = !empty($_POST['allowedCoverage']) ? $_POST['allowedCoverage'] : array();

  $lead_tag = ($tag_from == 'existing') ? $existing_tag : $new_tag;

  $validate->string(array('required' => true, 'field' => 'tag_from', 'value' => $tag_from), array('required' => 'Select any option'));
  if ($tag_from == 'existing') {
    $validate->string(array('required' => true, 'field' => 'existing_tag', 'value' => $existing_tag), array('required' => 'Select Lead Tag'));
  } else {
    $validate->string(array('required' => true, 'field' => 'new_tag', 'value' => $new_tag), array('required' => 'Lead Tag is required'));

    if (!$validate->getError('new_tag')) {
        if ($new_tag == 'Converted') {
            $validate->setError("new_tag", "This Tag is Invalid");
        } else {
            $check_exist_tag_sql = "SELECT * FROM lead_tag_master where lead_tag=:tag and is_deleted='N'";
            $check_exist_tag_res = $pdo->selectOne($check_exist_tag_sql, array(":tag" => $new_tag));
            if ($check_exist_tag_res) {
                $validate->setError("new_tag", "This Tag is already exists");
            }
        }

    }
  }

  
  $validate->string(array('required' => true, 'field' => 'enrollee_id', 'value' => $enrollee_id), array('required' => 'Enrollee ID  is required'));
  if($enrollee_id!=""){
      $checkEmpId_sql = "SELECT employee_id FROM leads WHERE employee_id = :employee_id AND is_deleted='N' AND sponsor_id=:sponsor";
      $whereEmpId = array(':employee_id' => makeSafe($enrollee_id),":sponsor"=>$group_id);    

      $resultEmpId_res = $pdo->selectOne($checkEmpId_sql, $whereEmpId);
        if (count($resultEmpId_res)>0) {
          $validate->setError("enrollee_id", "Enrollee ID already exists");
        }
  }
  /*$validate->string(array('required' => true, 'field' => 'annual_earnings', 'value' => $annual_earnings), array('required' => 'Annual Earnings is required'));*/

  $validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Company is required'));

  $validate->string(array('required' => true, 'field' => 'employee_type', 'value' => $employee_type), array('required' => 'Employee Type is required'));
  $validate->string(array('required' => true, 'field' => 'hire_date', 'value' => $hire_date), array('required' => 'Hire Date is required'));
  if(!empty($hire_date)){
    $check_hire_date=validateDate($hire_date,"m/d/Y");
    if(!$check_hire_date){
      $validate->setError("hire_date","Enter Valid Date");
    }
  }
  /*if(!empty($employee_type) && $employee_type == 'Renew'){
    $validate->string(array('required' => true, 'field' => 'termination_date', 'value' => $termination_date), array('required' => 'Termination Date is required'));
    if(!empty($termination_date)){
      $check_termination_date=validateDate($termination_date,"m/d/Y");
      if(!$check_termination_date){
        $validate->setError("termination_date","Enter Valid Date");
      }
    }
  }*/

  $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));

  $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));

  $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
  if(!empty($address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address_2)) {
      $validate->setError('address_2','Special character not allowed');
  }
  
  $validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
  
  $validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));

  $validate->string(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode), array('required' => 'Zip Code is required'));

  if(!$validate->getError('zipcode')){
    $zipRes=$pdo->selectOne("SELECT id,state_code FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$zipcode));

    if(empty($zipRes)){
      $validate->setError('zipcode', 'Zip code is not valid');
    }else{
      $stateRes=$pdo->selectOne("SELECT id FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$zipRes['state_code']));

      if(empty($stateRes)){
        $validate->setError('zipcode', 'Zip code is not valid');
      }else{
        $found_state_id = $stateRes['id'];
      }
    }
  }

  if(!$validate->getError('state')){
    if($found_state_id != $state){
      $validate->setError('state', 'Zip code is not valid for this state');
    }
  }

  $validate->string(array('required' => true, 'field' => 'gender', 'value' => $gender), array('required' => 'Please select gender'));
  $validate->string(array('required' => true, 'field' => 'dob', 'value' => $dob), array('required' => 'Date of Birth is required'));
  if(!empty($dob)){
    $check_dob=validateDate($dob,"m/d/Y");
    if(!$check_dob){
      $validate->setError("dob","Enter Valid Date");
    }

    $today_date = date('m/d/Y');
    if(strtotime($dob) >= strtotime($today_date)){
      $validate->setError("dob","Enter Valid Date");
    }
  }

  /*$validate->string(array('required' => true, 'field' => 'ssn', 'value' => $ssn), array('required' => 'SSN  is required'));*/

  $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required','invalid' => 'Valid Email is required'));
  if ($email != "") {
    if(!$validate->getError('email')){
      $selectEmail = "SELECT email FROM customer WHERE email = :email and status='Active' and type in('Agent','Customer') AND is_deleted = 'N'";
      $where_select_email = array(':email' => makeSafe($email));
      $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);

      if ($resultEmail) {
        $validate->setError("email", "This email is already associated with another user account");
      }
      $selectlead = "SELECT email FROM leads WHERE email = :email AND is_deleted = 'N'";
      $where_select_lead = array(":email" => makeSafe($email));
      $resultleads = $pdo->selectOne($selectlead,$where_select_lead);
      
      if($resultleads){
        $validate->setError("email", "This email is already associated with another user account");
      }
    }
  }

  $validate->digit(array('required' => true, 'field' => 'phone', 'value' => $phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

  
  $validate->string(array('required' => true, 'field' => 'class_id', 'value' => $class_id), array('required' => 'Class is required'));
  $validate->string(array('required' => true, 'field' => 'coverage_id', 'value' => $coverage_id), array('required' => 'Coverage is required'));

  $taxFieldArr = [
    'income',
    'pre_tax_deductions_field',
    'post_tax_deductions_field',
    'w4_dependents_amount_field',
    'w4_4a_other_income_field',
    'w4_4b_deductions_field',
    'w4_additional_withholding_field',
    'state_dependents_field',
    'state_additional_withholdings_field',
  ];
  foreach($taxFieldArr as $field){
      if(!empty($_POST[$field]) && !is_numeric($_POST[$field])){
          $validate->setError($field,'Valid '.ucwords(str_replace(array('_','w4','field','4a'),array(' ','','',''),$field)).' is required');
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

  if ($validate->isValid()) {
    if(!empty($ssn)){
      $ssn="msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
    }
    $display_id= get_lead_id();
    $lead_data = array(
      'lead_type'=>'Member',
      'sponsor_id' => $group_id,
      'lead_id' =>$display_id ,
      "opt_in_type" => $lead_tag,
      'employee_id'=>!empty($enrollee_id)? $enrollee_id:'',
      'group_company_id' => $company_id,
      'employee_type' => $employee_type,
      'hire_date' => date("Y-m-d", strtotime($hire_date)),
      'fname' => $fname,
      'lname' => $lname,
      'address'=>$address,
      'address2'=>$address_2,
      'city'=>$city,
      'state'=>$getStateNameByShortName[$zipAddress['state']],
      'zip'=>$zipcode,
      'gender'=>$gender,
      'birth_date'=>date("Y-m-d", strtotime($dob)),
      'ssn_itin_num'=>$ssn,
      'email' => $email,
      'cell_phone' => $phone,
      'group_classes_id'=>$class_id,
      'group_coverage_id' =>$coverage_id,
      'name'=>$fname.' '.$lname,
      'is_ssn_itin'=>'Y',
      'last_four_ssn'=>$ssn_last_four_digit,
      'status' => 'New',
      'generate_type' => 'Manual',
      'ip_address' => $_SERVER['SERVER_ADDR'],
      'created_at' => 'msqlfunc_NOW()',
      'updated_at' => 'msqlfunc_NOW()',
    );
    /*if(!empty($employee_type) && $employee_type == 'Renew'){
      $lead_data['termination_date'] = date("Y-m-d", strtotime($termination_date));
    }*/

    $tax_details = array(
      "income" => checkIsset($_POST['income']),
      "pre_tax_deductions_field" => checkIsset($_POST['pre_tax_deductions_field']),
      "post_tax_deductions_field" => checkIsset($_POST['post_tax_deductions_field']),
      "w4_filing_status_field" => checkIsset($_POST['w4_filing_status_field']),
      "w4_no_of_allowances_field" => checkIsset($_POST['w4_no_of_allowances_field']),
      "w4_two_jobs_field" => checkIsset($_POST['w4_two_jobs_field']),
      "w4_dependents_amount_field" => checkIsset($_POST['w4_dependents_amount_field']),
      "w4_4a_other_income_field" => checkIsset($_POST['w4_4a_other_income_field']),
      "w4_4b_deductions_field" => checkIsset($_POST['w4_4b_deductions_field']),
      "w4_additional_withholding_field" => checkIsset($_POST['w4_additional_withholding_field']),
      "state_filing_status_field" => checkIsset($_POST['state_filing_status_field']),
      "state_dependents_field" => checkIsset($_POST['state_dependents_field']),
      "state_additional_withholdings_field" => checkIsset($_POST['state_additional_withholdings_field']),
    );
    $lead_data = array_merge($lead_data,$tax_details);

    $lead_id = $pdo->insert("leads", $lead_data);

    if ($tag_from == 'new') {
      $tag_data = array(
          'lead_tag' =>$lead_tag,
          'agent_tag_id' => 0,
          'updated_at' => 'msqlfunc_NOW()',
          'created_at' => 'msqlfunc_NOW()'
      );
      $pdo->insert("lead_tag_master", $tag_data);
    }

    if(!empty($allowedCoverage)){
        foreach ($allowedCoverage as $key => $value) {
            $insCoverageParams = array(
                "lead_id" => $lead_id,
                "group_coverage_period_id" => $value,
            );
            $leads_assign_coverage = $pdo->insert('leads_assign_coverage',$insCoverageParams);
        }
    }
    
    $response['status'] = "success";
    setNotifySuccess("Enrollee Added Successfully");

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
        'title'=>$_SESSION['groups']['rep_id'],
      ),
      'ac_message_1' =>' created Enrollee ',
      'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/lead_details.php?id='.md5($lead_id),
          'title'=>$display_id,
      ),
    ); 
    activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'customer','Group Created Enrollee', $_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($description));
    
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