<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']);
$is_address_verified = checkIsset($_POST['is_address_verified']);

if($is_address_ajaxed){

    $response = array("status"=>'success');
    $address = $_POST['business_address'];
    $address_2 = checkIsset($_POST['business_address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zipcode = $_POST['zipcode'];
    $old_address = $_POST['old_business_address'];
    $old_zip = $_POST['old_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $address), array('required' => 'Address is required'));
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

$validate = new Validation();
$response = array();

$group_id = $_POST['group_id'];


$group_update_activity = array();

$group_name = !empty($_POST['group_name']) ? $_POST['group_name'] : '';
$business_address = !empty($_POST['business_address']) ? $_POST['business_address'] : '';
$business_address_2 = !empty($_POST['business_address_2']) ? $_POST['business_address_2'] : '';
$is_valid_address = !empty($_POST['is_valid_address']) ? $_POST['is_valid_address'] : '';
$city = !empty($_POST['city']) ? $_POST['city'] : '';
$state = !empty($_POST['state']) ? $_POST['state'] : '';
$zipcode = !empty($_POST['zipcode']) ? $_POST['zipcode'] : '';
$business_phone = !empty($_POST['business_phone']) ? $_POST['business_phone'] : '';
$business_phone = phoneReplaceMain($business_phone);
$business_email = checkIsset($_POST['business_email']);
$no_of_employee = !empty($_POST['no_of_employee']) ? $_POST['no_of_employee'] : '';
$years_in_business = !empty($_POST['years_in_business']) ? $_POST['years_in_business'] : '';
$ein = !empty($_POST['ein']) ? $_POST['ein'] : '';
$ein = phoneReplaceMain($ein);
$nature_of_business = !empty($_POST['nature_of_business']) ? $_POST['nature_of_business'] : '';
$sic_code = !empty($_POST['sic_code']) ? $_POST['sic_code'] : '';
$fname = !empty($_POST['fname']) ? $_POST['fname'] : '';
$lname = !empty($_POST['lname']) ? $_POST['lname'] : '';
$phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
$phone = phoneReplaceMain($phone);
$email = checkIsset($_POST['email']);
$password = !empty($_POST['password']) ? $_POST['password'] : '';
$c_password = !empty($_POST['c_password']) ? $_POST['c_password'] : '';

$company_count = !empty($_POST['company_count']) ? $_POST['company_count'] : '';
$group_company = !empty($_POST['group_company']) ? $_POST['group_company'] : '';
$billing_broken = !empty($_POST['billing_broken']) ? $_POST['billing_broken'] : '';


    if(!empty($password)){
        $validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
        $validate->string(array('required' => true, 'field' => 'c_password', 'value' => $c_password), array('required' => 'Confirm Password is required'));
        //for strong password
        if (!$validate->getError('password')) {
            if (strlen($password) < 8 || strlen($password) > 20) {
                $validate->setError('password', 'Password must be 8-20 characters');
            } else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
                 || !preg_match('`[0-9]`', $password)) {
                // at least one digit
                $validate->setError('password', 'Valid Password is required');
            } else if (!ctype_alnum($password)) {
                $validate->setError('password', 'Special character not allowed');
            } else if (preg_match('`[?/$\*+]`', $password)) {
                $validate->setError('password', 'Password not valid');
            } else if (preg_match('`[,"]`', $password)) {
                $validate->setError('password', 'Password not valid');
            } else if (preg_match("[']", $password)) {
                $validate->setError('password', 'Password not valid');
            }
        }
        if (!$validate->getError('c_password') && !$validate->getError('password')) {
            if ($password != $c_password) {
                $validate->setError('c_password', 'Both Password must be same');
            }
        }
    }

    $validate->string(array('required' => true, 'field' => 'group_name', 'value' => $group_name), array('required' => 'Group Name is required.'));
    $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $business_address), array('required' => 'Business address is required.'));
    if(!empty($business_address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$business_address_2)) {
        $validate->setError('business_address_2','Special character not allowed');
    }
    $validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'city is required.'));
    $validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'state is required.'));
    $validate->string(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode), array('required' => 'Zip Code is required'));

    $validate->digit(array('required' => true, 'field' => 'business_phone', 'value' => $business_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
    $validate->email(array('required' => true, 'field' => 'business_email', 'value' => $business_email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

    $validate->digit(array('required' => true, 'field' => 'no_of_employee', 'value' => $no_of_employee), array('required' => 'Employee is required', 'invalid' => 'Valid Number is required'));

    $validate->digit(array('required' => true, 'field' => 'years_in_business', 'value' => $years_in_business), array('required' => 'Year in business is required', 'invalid' => 'Valid Number is required'));

    $validate->digit(array('required' => true, 'field' => 'ein', 'value' => $ein), array('required' => 'EIN/FEIN is required', 'invalid' => 'Valid EIN/FEIN is required'));

    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));

    $validate->digit(array('required' => true, 'field' => 'phone', 'value' => $phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

    $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

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
    if(!$validate->getError('email')){
            $where_select_email = array(':email' => $email);
            $incr = "";
            if(!empty($group_id)){
                $incr .= " AND md5(id)!=:id";
                $where_select_email[":id"] = $group_id;
            }
            $selectEmail = "SELECT id,email FROM customer WHERE email=:email $incr AND type='Group' AND is_deleted='N' ";
            $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
            if (!empty($resultEmail)) {
                $validate->setError("email", "This email is already associated with another group account");
            }
    }
        
    $validate->string(array('required' => true, 'field' => 'group_company', 'value' => $group_company), array('required' => 'Select any option'));

    if($group_company == 'Y'){
            $validate->string(array('required' => true, 'field' => 'billing_broken', 'value' => $billing_broken), array('required' => 'Select any option'));
            if(empty($company_count)){
                $validate->setError("company_count","Please Add Location/Company");
            }
    }



$new_update_details =array(
    'business_name'=>$group_name,
    'address'=>$business_address,
    'address_2'=>$business_address_2,
    'city'=>$city,
    'state'=>$state,
    'zip'=>$zipcode,
    'business_phone'=>$business_phone,
    'business_email'=>$business_email,
    'fname'=>$fname,
    'lname'=>$lname,
    'cell_phone'=>$phone,
    'email'=>$email,
    'group_size'=>$no_of_employee,
    'group_in_year'=>$years_in_business,
    'ein'=>$ein,
    'business_nature'=>$nature_of_business,
    'sic_code'=>$sic_code,
    'employer_company_common_owner'=>$group_company,
    'invoice_broken_locations'=>$billing_broken,
);

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
        'business_name' => $group_name,
        'address' => makesafe($business_address),
        'address_2' => makesafe($business_address_2),
        'city' => makesafe($city),
        'country_id' => '231',
        'country_name' => "United States",
        'state' => makesafe($state),
        'zip' => makesafe($zipcode),
        'business_phone' => $business_phone,
        'business_email' => $business_email,
        'fname' => makesafe($fname),
        'lname' => makesafe($lname),
        'cell_phone' => makesafe($phone),
        'email' => $email,
    );

    if ($password != "") {
        $upd_params['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
    }

    $c_upd_where = array(
        'clause' => 'md5(id)=:id',
        'params' => array(
            ':id' => $group_id,
        ),
    );

    $upd_cs_param = array(
        'is_valid_address'=>$is_valid_address,
        'is_address_verified'=>$is_address_verified,
    );

    $groupSettingParams = array(
        'group_size' => $no_of_employee,
        'group_in_year' => $years_in_business,
        'ein' => $ein,
        'business_nature' => $nature_of_business,
        'sic_code' => $sic_code,
        'employer_company_common_owner' => $group_company,
        'invoice_broken_locations'=>'N',
    );
    if($group_company=='Y'){
            $groupSettingParams['invoice_broken_locations']=$billing_broken;
    }

    $cs_upd_where = array(
        'clause' => 'md5(customer_id) = :id',
        'params' => array(
            ':id' => $group_id,
        ),
    );

    $group_update_activity['customer'] = $pdo->update('customer',$upd_params,$c_upd_where,true);
    $group_update_activity['customer_settings'] = $pdo->update('customer_settings',$upd_cs_param,$cs_upd_where,true);
    $group_update_activity['customer_group_settings'] = $pdo->update('customer_group_settings',$groupSettingParams,$cs_upd_where,true);
    
    $response['status'] = "success";   
}
$description = array();
if(!empty($group_update_activity)){

    group_profile_activity($group_update_activity);
}
function group_profile_activity($group_update_activity){
    global $pdo,$group_id,$ADMIN_HOST,$new_update_details,$password;
    $flg = "true";
    $group_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where md5(id)=:id",array(":id"=>$group_id));
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
            'title' => $_SESSION['agents']['rep_id'],
        ),
        'ac_message_1' =>'  Updated Profile In Group '.$group_name['name'].' (',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($group_name['id']),
            'title'=> $group_name['rep_id'],
        ),
        'ac_message_2' =>')<br>',
        );
    foreach($group_update_activity as $key => $value){
        if(!empty($value) && is_array($value)){
            foreach($value as $key2 => $val){
                if(array_key_exists($key2,$new_update_details)){
                        if(in_array($val,array('Y','N'))){
                            $val = $val == 'Y' ? "selected" : "unselected";
                            $new_update_details[$key2] = $new_update_details[$key2] == 'Y' ? "selected" : "unselected";
                        }
                        
                        if($key2 == "cell_phone" || $key2 == "business_phone") {
                            $val = format_telephone($val);
                            $new_update_details[$key2] = format_telephone($new_update_details[$key2]);
                        }

                        $key2_tmp = ucwords(str_replace('_',' ',$key2));
                        $description['key_value']['desc_arr'][$key2_tmp] = ' Updated From '.$val." To ".$new_update_details[$key2].".<br>";
                        $flg = "false";
                }else{
                    $description['description2'][] = ucwords(str_replace('_',' ',$val));
                    $flg = "false";
                }
            }    
        }else{
            if(is_array($value) && !empty($value)){
                $description['description'.$key][] = implode('',$value);
                $flg = "false";
            }else if(!empty($value)){
                $description['description'.$key][] = $value;
                $flg = "false";
            }
        }
        
    }
    if($password !=''){
        $description['description_password'] = 'Password updated.';
        $flg = "false";
    }
    if($flg == "true"){
        $description['description_novalue'] = 'No updates in group profile page.';
    }
    
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['agents']['id'],'Agent',$group_name['id'],'Group','Group Profile Updated',"","",$desc);
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