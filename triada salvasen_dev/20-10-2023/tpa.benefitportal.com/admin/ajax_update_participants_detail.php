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

$ptRow = array(); 
$ptUpdateActivity = array(); 
$ptId = 0;
$participants_id = checkIsset($_REQUEST['participants_id']);
$participants_type = checkIsset($_REQUEST['participants_type']);
$reseller_number = checkIsset($_REQUEST['reseller_number']);
$client_code = checkIsset($_REQUEST['client_code']);
$ssn = !empty($_POST['ssn']) ? phoneReplaceMain($_POST['ssn']) : '';
$is_ssn_edit = checkIsset($_REQUEST['is_ssn_edit']);

$fname = checkIsset($_REQUEST['fname']);
$lname = checkIsset($_REQUEST['lname']);
$mname = checkIsset($_REQUEST['mname']);
$birth_date = checkIsset($_REQUEST['birth_date']);
$gender = checkIsset($_REQUEST['gender']);

$employee_number = checkIsset($_REQUEST['employee_number']);
$employee_id = checkIsset($_REQUEST['employee_id']);
$person_code = checkIsset($_REQUEST['person_code']);

$address = checkIsset($_REQUEST['address']);
$address2 = checkIsset($_REQUEST['address2']);
$city = checkIsset($_REQUEST['city']);
$state = checkIsset($_REQUEST['state']);
$zip = checkIsset($_REQUEST['zip']);

$home_phone = !empty($_POST['home_phone']) ? phoneReplaceMain($_POST['home_phone']) : '';
$work_phone = !empty($_POST['work_phone']) ? phoneReplaceMain($_POST['work_phone']) : '';
$cell_phone = !empty($_POST['cell_phone']) ? phoneReplaceMain($_POST['cell_phone']) : '';

$email = checkIsset($_REQUEST['email']);

$hire_date = !empty($_REQUEST['hire_date']) ? date("Y-m-d",strtotime($_REQUEST['hire_date'])) : NULL;
$employee_term_date = !empty($_REQUEST['employee_term_date']) ? date("Y-m-d",strtotime($_REQUEST['employee_term_date'])) : NULL;
$employee_term_reason_code = checkIsset($_REQUEST['employee_term_reason_code']);

$employment_status = checkIsset($_REQUEST['employment_status']);
$marital_status = checkIsset($_REQUEST['marital_status']);
$marriage_date = !empty($_REQUEST['marriage_date']) ? date("Y-m-d",strtotime($_REQUEST['marriage_date'])) : NULL;
$is_disabled = checkIsset($_REQUEST['is_disabled']);
$disability_effective_date = !empty($_REQUEST['disability_effective_date']) ? date("Y-m-d",strtotime($_REQUEST['disability_effective_date'])) : NULL;

$is_deceased = checkIsset($_REQUEST['is_deceased']);
$death_date = !empty($_REQUEST['death_date']) ? date("Y-m-d",strtotime($_REQUEST['death_date'])) : NULL;
$requires_cob = checkIsset($_REQUEST['requires_cob']);
$pay_frequency = checkIsset($_REQUEST['pay_frequency']);

$annual_salary = checkIsset($_REQUEST['annual_salary']);
$annual_compensation = checkIsset($_REQUEST['annual_compensation']);

$salary_effective_date = !empty($_REQUEST['salary_effective_date']) ? date("Y-m-d",strtotime($_REQUEST['salary_effective_date']))  : NULL;
$hours_per_week = checkIsset($_REQUEST['hours_per_week']);
$occupation = checkIsset($_REQUEST['occupation']);

$payroll_class = checkIsset($_REQUEST['payroll_class']);
$ftpt_status = checkIsset($_REQUEST['ftpt_status']);
$department = checkIsset($_REQUEST['department']);
$location = checkIsset($_REQUEST['location']);
$area = checkIsset($_REQUEST['area']);

$reporting_field1 = checkIsset($_REQUEST['reporting_field1']);
$reporting_field2 = checkIsset($_REQUEST['reporting_field2']);
$reporting_field3 = checkIsset($_REQUEST['reporting_field3']);
$reporting_field4 = checkIsset($_REQUEST['reporting_field4']);
$wellness_credit = checkIsset($_REQUEST['wellness_credit']);
$tobacco_user = checkIsset($_REQUEST['tobacco_user']);

// validation code start
    $validate->string(array('required' => true, 'field' => 'participants_id', 'value' => $participants_id), array('required' => 'Participants Id is required'));
    if (!$validate->getError('participants_id')){
        $select = "SELECT p.id
                FROM participants p
                WHERE md5(p.id)=:id";
        $where = array(':id' => makeSafe($participants_id));
        $ptRow = $pdo->selectOne($select, $where);
        $ptId = !empty($ptRow["id"]) ? $ptRow["id"] : 0;

        $validate->string(array('required' => true, 'field' => 'participants_id', 'value' => $ptId), array('required' => 'Participants Id is required'));
    }

    $validate->string(array('required' => true, 'field' => 'participants_type', 'value' => $participants_type), array('required' => 'Participants Type is required'));
    $validate->string(array('required' => true, 'field' => 'reseller_number', 'value' => $reseller_number), array('required' => 'Reseller is required'));
    if ($is_ssn_edit == "Y") {
        $validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
    }
    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'Firstname is required'));
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Lastname is required'));
    $validate->string(array('required' => true, 'field' => 'birth_date', 'value' => $birth_date), array('required' => 'DOB is required'));
    if (!$validate->getError('birth_date') && !empty($birth_date)) {
        list($mm, $dd, $yyyy) = explode('/', $birth_date);
        if (!checkdate($mm, $dd, $yyyy)) {
            $validate->setError('birth_date', 'Valid DOB is required');
        }
    }

    $validate->string(array('required' => true, 'field' => 'gender', 'value' => $gender), array('required' => 'Gender is required'));
    $validate->string(array('required' => true, 'field' => 'employee_id', 'value' => $employee_id), array('required' => 'PrimaryID is required'));
    $validate->digit(array('required' => true, 'field' => 'person_code', 'value' => $person_code), array('required' => 'Person Code is required', 'invalid' => 'Person Code Should be Numberic'));
    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));

    if(!empty($employee_id) && !empty($person_code)) {
        $tmp_persion_code = $person_code;
        $person_code = sprintf("%02d",$person_code);
        $selEmployee = "SELECT id FROM participants WHERE id!=:id AND employee_id=:employee_id AND (person_code=:person_code or person_code=:tmp_persion_code) AND is_deleted='N'";
        $resEmployee = $pdo->selectOne($selEmployee,array(":id"=>$ptId,":employee_id" => $employee_id,":person_code" => $person_code,':tmp_persion_code'=>$tmp_persion_code));
        if(!empty($resEmployee)) {
            $validate->setError('person_code', 'Participant already exists');
        }
    }

    if(!empty($address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address2)) {
        $validate->setError('address2','Special character not allowed');
    }
    $validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
    $validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
    $validate->string(array('required' => true, 'field' => 'zip', 'value' => $zip ), array('required' => 'Zip Code is required'));

    if (!$validate->getError('zip')){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zip);
        if($zipAddress['status'] !='success'){
            $validate->setError("zip",$zipAddress['error_message']);
        }
    }

// validation code ends

if($validate->isValid()){  

    $ptData = array(
        "participants_type" => $participants_type,        
        "reseller_number" => $reseller_number,
        "client_code" => $client_code,
        "employee_id" => $employee_id,
        "fname" => $fname,
        "lname" => $lname,
        "birth_date" => date("Y-m-d",strtotime($birth_date)),
        "gender" => $gender,
        "email" => $email,
        "person_code" => $person_code,
        "address" => $address,
        "city" => $city,
        "state" => $state,
        "zip" => $zip,

        "mname" => $mname,
        "employee_number" => $employee_number,
        "address2" => $address2,
        "home_phone" => $home_phone,
        "work_phone" => $work_phone,
        "cell_phone" => $cell_phone,

        "hire_date" => $hire_date,
        "employee_term_date" => $employee_term_date,
        "employee_term_reason_code" => $employee_term_reason_code,
        "employment_status" => $employment_status,
        "marital_status" => $marital_status,
        "marriage_date" => $marriage_date,
        "is_disabled" => $is_disabled,
        "disability_effective_date" => $disability_effective_date,
        "is_deceased" => $is_deceased,
        "death_date" => $death_date,
        "requires_cob" => $requires_cob,
        "pay_frequency" => $pay_frequency,
        "annual_salary" => $annual_salary,
        "annual_compensation" => $annual_compensation,
        "salary_effective_date" => $salary_effective_date,
        "hours_per_week" => $hours_per_week,
        "occupation" => $occupation,
        "payroll_class" => $payroll_class,
        "ftpt_status" => $ftpt_status,
        "department" => $department,
        "location" => $location,
        "area" => $area,
        "reporting_field1" => $reporting_field1,
        "reporting_field2" => $reporting_field2,
        "reporting_field3" => $reporting_field3,
        "reporting_field4" => $reporting_field4,
        "wellness_credit" => $wellness_credit,
        "tobacco_user" => $tobacco_user,
    ); 

    if (!empty($ssn)) {
        $ssn_last_four_digit = substr($ssn,-4,4);
        $ptData['ssn'] = "msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
        $ptData['last_four_ssn'] = $ssn_last_four_digit;
    }

    $ptWhere = array(
        'clause' => 'id=:id',
        'params' => array(
            ':id' => $ptId,
        ),
    );
    $ptUpdateActivity['participants'] = $pdo->update('participants',$ptData,$ptWhere,true);  
    $response['status'] = "success";   
}

if(!empty($ptUpdateActivity['participants'])){

    $select = "SELECT p.*
            FROM participants p
            WHERE id=:id";
    $where = array(':id' => $ptId);
    $ptRow = $pdo->selectOne($select, $where);

    $flg = "true";
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'  Updated Details In Participants '.$ptRow['fname'].' '.$ptRow['lname'].' (',
        'ac_red_2'=>array(
            'href'=> 'participants_details.php?id='.md5($ptRow['id']),
            'title'=> $ptRow['participants_id'],
        ),
        'ac_message_2' =>')<br>',
    );
    foreach($ptUpdateActivity as $key => $value){
        if(!empty($value) && is_array($value)){
            foreach($value as $key2 => $val){
                if(array_key_exists($key2,$ptRow)){
                        if(in_array($val,array('Y','N'))){
                            $val = $val == 'Y' ? "selected" : "unselected";
                        }
                        if($key2 == "cell_phone" || $key2 == "work_phone" || $key2 == "home_phone") {
                            $ptRow[$key2] = format_telephone($ptRow[$key2]);
                            $val = format_telephone($val);
                        }
                        $description['key_value']['desc_arr'][$key2] = ' Updated From '.$val." To ".$ptRow[$key2].".<br>";
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
        $description['description_novalue'] = 'No updates in participants details page.';
    }    
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $ptRow['id'], 'Participants', 'Participants Details Updated',($_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']),"",$desc);
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