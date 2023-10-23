<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

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

        if($old_address != $address || $zipcode!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state  ||  $old_city != $city){
            
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

$validate = new Validation();
$response = array();
$group_id = $_SESSION['groups']['id'];
$lead_update_activity = array(); 
$lead_id = $_REQUEST['id'];
$lead_type = $_POST["lead_type"];
$is_valid_address = !empty($_POST['is_valid_address']) ? $_POST['is_valid_address'] : '';

$enrollee_id = !empty($_POST['enrollee_id']) ? $_POST['enrollee_id'] : '';
// $annual_earnings = !empty($_POST['annual_earnings']) ? $_POST['annual_earnings'] : '';
$company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '';
$employee_type = !empty($_POST['employee_type']) ? $_POST['employee_type'] : '';
$hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : '';
$fname = !empty($_POST['fname']) ? $_POST['fname'] : '';
$lname = !empty($_POST['lname']) ? $_POST['lname'] : '';
$address = !empty($_POST['address']) ? $_POST['address'] : '';
$address2 = !empty($_POST['address_2']) ? $_POST['address_2'] : '';
$city = !empty($_POST['city']) ? $_POST['city'] : '';
$state = !empty($_POST['state']) ? $_POST['state'] : '';
$found_state_id = 0;
$zipcode = !empty($_POST['zipcode']) ? $_POST['zipcode'] : '';
$gender = !empty($_POST['gender']) ? $_POST['gender'] : '';
$dob = !empty($_POST['dob']) ? $_POST['dob'] : '';
$ssn = !empty($_POST['ssn']) ? $_POST['ssn'] : '';
$entered_ssn = !empty($_POST['entered_ssn']) ? $_POST['entered_ssn'] : '';
$ssn = phoneReplaceMain($ssn);
$ssn_last_four_digit=substr($ssn,-4,4);
$email = !empty($_POST['email']) ? $_POST['email'] : '';
$phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
$phone = phoneReplaceMain($phone);
$class_id = !empty($_POST['class_id']) ? $_POST['class_id'] : '';
$coverage_id = !empty($_POST['coverage_id']) ? $_POST['coverage_id'] : '';

$allowedCoverage = !empty($_POST['allowedCoverage']) ? $_POST['allowedCoverage'] : array();

$lead_sql = "SELECT l.id, CONCAT(l.fname,' ',l.lname) as name ,l.lead_id as rep_id,l.sponsor_id,l.customer_id,c.status as customer_status 
            FROM leads l
            LEFT JOIN customer c ON(c.id = l.customer_id)
            WHERE md5(l.id)=:id";
$lead_row = $pdo->selectOne($lead_sql,array(":id"=>$lead_id));
$customer_id = 0;
if(!empty($lead_row['customer_id'])) {
    $customer_id = $lead_row['customer_id'];
}


$validate->string(array('required' => true, 'field' => 'enrollee_id', 'value' => $enrollee_id), array('required' => 'Enrollee ID  is required'));
if($enrollee_id!=""){
  $checkEmpId_sql = "SELECT employee_id FROM leads WHERE id!=:id AND employee_id = :employee_id AND is_deleted='N' AND sponsor_id=:sponsor";
  $whereEmpId = array(':employee_id' => makeSafe($enrollee_id),":sponsor"=>$group_id,":id"=>$lead_row['id']);    

  $resultEmpId_res = $pdo->selectOne($checkEmpId_sql, $whereEmpId);
    if (count($resultEmpId_res)>0) {
      $validate->setError("enrollee_id", "Enrollee ID already exists");
    }
}
// $validate->string(array('required' => true, 'field' => 'annual_earnings', 'value' => $annual_earnings), array('required' => 'Annual Earnings is required'));

$validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Company is required'));

$validate->string(array('required' => true, 'field' => 'employee_type', 'value' => $employee_type), array('required' => 'Employee Type is required'));
$validate->string(array('required' => true, 'field' => 'hire_date', 'value' => $hire_date), array('required' => 'Hire Date is required'));
if(!empty($hire_date)){
    $check_hire_date=validateDate($hire_date,"m/d/Y");
    if(!$check_hire_date){
      $validate->setError("hire_date","Enter Valid Date");
    }
}


$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));

$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));

$validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
// if(!empty($address) && $is_valid_address !='Y'){
//     $validate->setError("address","Valid Address is required");
// }
$validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));

$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));

$validate->string(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode), array('required' => 'Zip Code is required'));

if(!$validate->getError('zipcode')){
    $zipRes=$pdo->selectOne("SELECT id,state_code FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$zipcode));

    if(empty($zipRes)){
      $validate->setError('zipcode', 'Zip code is not valid');
    }else{
      $stateRes=$pdo->selectOne("SELECT id,name FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$zipRes['state_code']));

      if(empty($stateRes)){
        $validate->setError('zipcode', 'Zip code is not valid');
      }else{
        $found_state_id = $stateRes['name'];
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


// if(empty($entered_ssn) || !empty($ssn)){
//     $validate->string(array('required' => true, 'field' => 'ssn', 'value' => $ssn), array('required' => 'SSN  is required'));
// }


$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

if (!$validate->getError('email')) {
    if($lead_type == "Member") {
        if(!empty($customer_id)) {
            $selectEmail = "SELECT id,email FROM customer WHERE id!=:id AND email=:email AND type='Customer' AND is_deleted='N'";
            $where_select_email = array(':email' => $email,':id' => $customer_id);
        } else {
            $selectEmail = "SELECT id,email FROM customer WHERE email=:email AND type='Customer' AND is_deleted='N'";
            $where_select_email = array(':email' => $email);
        }
        
        $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
        if ($resultEmail) {
            $validate->setError("email", "This email is already associated with another Member account.");
        } else {
            $where_select_email = array(':id'=>$lead_row['id'],':sponsor_id'=>$lead_row['sponsor_id'],':email'=>$email);
            $selectEmail = "SELECT id,email FROM leads WHERE lead_type='Member' AND is_deleted='N' AND email=:email AND id!=:id AND sponsor_id=:sponsor_id";
            $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
            if (!empty($resultEmail)) {
                $validate->setError("email","This email is already associated with another lead");
            }
        }
    } else {
        if(!empty($customer_id)) {
            $selectEmail = "SELECT id,email FROM customer WHERE id!=:id AND email=:email AND type IN('Agent','Group') AND is_deleted='N' ";
            $where_select_email = array(':email' => $email,':id' => $customer_id);
        } else {
            $selectEmail = "SELECT id,email FROM customer WHERE email=:email AND type IN('Agent','Group') AND is_deleted='N' ";
            $where_select_email = array(':email' => $email);
        }
        $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
        if ($resultEmail) {
            $validate->setError("email", "This email is already associated with another Agent/Group account.");
        } else {
            $where_select_email = array(':id'=>$lead_row['id'],':sponsor_id'=>$lead_row['sponsor_id'],':email'=>$email);
            $selectEmail = "SELECT id,email FROM leads WHERE lead_type='Agent/Group' AND is_deleted='N' AND email=:email AND id!=:id AND sponsor_id=:sponsor_id";
            $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
            if (!empty($resultEmail)) {
                $validate->setError("email","This email is already associated with another lead");
            }
        }
    }
}

$validate->digit(array('required' => true, 'field' => 'phone', 'value' => $phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

$validate->string(array('required' => true, 'field' => 'class_id', 'value' => $class_id), array('required' => 'Class is required'));
$validate->string(array('required' => true, 'field' => 'coverage_id', 'value' => $coverage_id), array('required' => 'Plan is required'));

//Check numeric validation
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
    if($field == 'income' && !empty($_POST[$field])){
        $tempIncome = $_POST['salary_encrypted'] == 'Y' ? base64_decode($_POST['income']) : $_POST['income'];
        if(!is_numeric($tempIncome)){
            $validate->setError($field,'Valid '.ucwords(str_replace(array('_','w4','field','4a'),array(' ','','',''),$field)).' is required');
        }
    }else{
        if(!empty($_POST[$field]) && !is_numeric($_POST[$field])){
            $validate->setError($field,'Valid '.ucwords(str_replace(array('_','w4','field','4a'),array(' ','','',''),$field)).' is required');
        }
    }
}

$new_update_details =array(
    'lead_type' => checkIsset($lead_type),
    'employee_id'=>!empty($enrollee_id)? $enrollee_id:'',
    'group_company_id' => $company_id,
    'employee_type' => $employee_type,
    'hire_date' => date("Y-m-d", strtotime($hire_date)),
    'fname' => $fname,
    'lname' => $lname,
    'address'=>$address,
    'address2'=>makeSafe($address2),
    'city'=>$city,
    'state'=>$state,
    'zip'=>$zipcode,
    'gender'=>$gender,
    'birth_date'=>date("Y-m-d", strtotime($dob)),
    'email' => $email,
    'cell_phone' => $phone,
    'group_classes_id'=>$class_id,
    'group_coverage_id'=>$coverage_id,
    'last_four_ssn' => $ssn_last_four_digit,
    'name'=>$fname.' '.$lname,
);

$tax_details = array(
    "income" => !empty($_POST['income']) && $_POST['salary_encrypted'] == 'Y' ? base64_decode($_POST['income']) : checkIsset($_POST['income']),
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
$new_update_details = array_merge($new_update_details,$tax_details);

if(!$validate->getError('zipcode')){
    include_once '../includes/function.class.php';
    $function_list = new functionsList();
    $zipAddress = $function_list->uspsCityVerification($zipcode);
    if($zipAddress['status'] !='success'){
        $validate->setError("zipcode",$zipAddress['error_message']);
    }
}

if($validate->isValid()){    
    $upd_params = array(
        'lead_type' => $lead_type,
        'employee_id'=>!empty($enrollee_id)? $enrollee_id:'',
        'group_company_id' => $company_id,
        'employee_type' => $employee_type,
        'hire_date' => date("Y-m-d", strtotime($hire_date)),
        'fname' => $fname,
        'lname' => $lname,
        'address'=>$address,
        'address2'=>makeSafe($address2),
        'city'=>$city,
        'state'=>$state,
        'zip'=>$zipcode,
        'gender'=>$gender,
        'birth_date'=>date("Y-m-d", strtotime($dob)),
        'email' => $email,
        'cell_phone' => $phone,
        'group_classes_id'=>$class_id,
        'group_coverage_id'=>$coverage_id,
        'name'=>$fname.' '.$lname,
        'updated_at' => 'msqlfunc_NOW()'
    );
    if(!empty($ssn)){
        $ssn="msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
        $upd_params['ssn_itin_num']=$ssn;
        $upd_params['is_ssn_itin']='Y';
        $upd_params['last_four_ssn']=$ssn_last_four_digit;
    }
    $upd_where = array(
        'clause' => 'md5(id)=:id',
        'params' => array(
            ':id' => $lead_id,
        ),
    );
    $upd_params = array_merge($upd_params,$tax_details);
    $lead_update_activity['leads'] = $pdo->update('leads',$upd_params,$upd_where,true);

    if($lead_type == "Member" && !empty($customer_id) && in_array($lead_row['customer_status'],$MEMBER_ABONDON_STATUS)) {
        $upd_params = array(
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'cell_phone' => $cell_phone,
        );
        $upd_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $customer_id,
            ),
        );
        $pdo->update('customer',$upd_params,$upd_where);          
    }
    
    $response['status'] = "success";   

    $sqlAssignCoverage = "SELECT * FROM leads_assign_coverage where is_deleted='N' AND md5(lead_id)=:lead_id";
    $resAssignCoverage = $pdo->select($sqlAssignCoverage,array(":lead_id"=>$lead_id));

    $assignCoverageArr = array();
    if(!empty($resAssignCoverage)){
        foreach ($resAssignCoverage as $key => $value) {
            array_push($assignCoverageArr, $value['group_coverage_period_id']);
        }
    }

    $coverageResult=array_diff($assignCoverageArr,$allowedCoverage);
    if(!empty($coverageResult)){
        foreach ($coverageResult as $key => $value) {
            $updCoverageParams=array(
                'is_deleted'=>'Y'
            );
            $updCoverageWhere=array(
                'clause'=>'is_deleted="N" AND lead_id = :lead_id AND group_coverage_period_id=:group_coverage_period_id',
                'params'=>array(
                    ":lead_id"=>$lead_row['id'],
                    ":group_coverage_period_id"=>$value
                )
            );
            $pdo->update("leads_assign_coverage",$updCoverageParams,$updCoverageWhere);
        }
    }

    $coverageResult=array_diff($allowedCoverage,$assignCoverageArr);
    if(!empty($coverageResult)){
        foreach ($coverageResult as $key => $value) {
            $insCoverageParams = array(
                "lead_id" => $lead_row['id'],
                "group_coverage_period_id" => $value,
            );
            $leads_assign_coverage = $pdo->insert('leads_assign_coverage',$insCoverageParams);
        }
    }
}

if(!empty($lead_update_activity)){
    $flg = "true";
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
            'title'=>$_SESSION['groups']['rep_id'],
        ),
        'ac_message_1' =>'  Updated Profile In Lead '.$lead_row['name'].' (',
        'ac_red_2'=>array(
            'href'=> 'lead_details.php?id='.md5($lead_row['id']),
            'title'=> $lead_row['rep_id'],
        ),
        'ac_message_2' =>')<br>',
    );
    foreach($lead_update_activity as $key => $value){
        if(!empty($value) && is_array($value)){
            foreach($value as $key2 => $val){
                if(isset($new_update_details[$key2]) && $val == $new_update_details[$key2]){
                    continue;
                }
                if(array_key_exists($key2,$new_update_details)){
                        if(in_array($val,array('Y','N'))){
                            $val = $val == 'Y' ? "selected" : "unselected";
                        }
                        if($key2 == "cell_phone") {
                            $new_update_details[$key2] = format_telephone($new_update_details[$key2]);
                            $val = format_telephone($val);
                        }
                        $description['key_value']['desc_arr'][$key2] = ' Updated From '.addDashtoBlankField($val)." To ".$new_update_details[$key2].".<br>";
                        if($key2 == 'income' && $_SESSION['groups']['rep_id'] == 'G56118'){
                            $description['key_value']['desc_arr'][$key2] = 'Salary updated';
                        }
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
        $description['description_novalue'] = 'No updates in lead profile page.';
    }    
    $desc=json_encode($description);
    if($customer_id > 0){
        activity_feed(3,$_SESSION['groups']['id'], 'Group' , $lead_row['customer_id'], 'customer', 'Group Update Member Detail',($_SESSION['groups']['fname'].' '.$_SESSION['groups']['lname']),"",$desc);    
    }
    activity_feed(3,$_SESSION['groups']['id'], 'Group' , $lead_row['id'], 'Lead', 'Lead Profile Updated',($_SESSION['groups']['fname'].' '.$_SESSION['groups']['lname']),"",$desc);
}

if(count($validate->getErrors()) > 0){
    $response['status'] = "errors";   
    $response['errors'] = $validate->getErrors();   
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();

?>