<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']) ;
$is_agency_address_ajaxed = checkIsset($_POST['is_agency_address_ajaxed']) ;

if($is_address_ajaxed){

    $response = array("status"=>'success');
    $address = $_POST['address'];
    $address_2 = checkIsset($_POST['address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zipcode = $_POST['zipcode'];
    $old_address = $_POST['old_address'];
    $old_zip = $_POST['old_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        $response['agencyApi'] = "";
        if(!empty($is_agency_address_ajaxed)){
            $response['agencyApi'] = 'success';
        }

        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zipcode);
        // pre_print($zipAddress);
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
if($is_agency_address_ajaxed){

    $response = array("status"=>'success');
    $address = $_POST['business_address'];
    $address_2 = checkIsset($_POST['business_address2']);
    $city = $_POST['business_city'];
    $state = checkIsset($_POST['business_state']);
    $zipcode = $_POST['business_zipcode'];
    $old_address = $_POST['old_business_address'];
    $old_zip = $_POST['old_business_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'business_zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        $response['agencyApi'] = 'done';
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
                $response['errors'] = array("business_zipcode"=>$zipAddress['error_message']);
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
function phoneReplace($phone) {
    return str_replace(array("_", "-", " ", "(", ")"), array("", "", "", "", ""), $phone);
}

$ajax_delete = !empty($_POST['ajax_delete']) ? $_POST['ajax_delete'] : '' ;
if ($ajax_delete) {
    $result = array();
    $lid = !empty($_POST['lid']) ? $_POST['lid'] : '' ;
    if($lid != ''){
        $deleted = delete_license($lid);
        if($deleted){
            $result['status'] = "success";
        }else{
            $result['status'] = "fail";
        }
        
    }
    header('Content-type: application/json');
    echo json_encode($result); 
    exit;
}

function delete_license($lid){
    global $ADMIN_HOST;
    $selADoc = "SELECT id,selling_licensed_state FROM agent_license WHERE md5(agent_id)=:agent_id AND id=:id AND is_deleted='N'";
    $whrADoc = array(":agent_id" => $_POST['agent_id'],":id"=>$lid);
    global $pdo;
    $resADoc = $pdo->selectOne($selADoc, $whrADoc);
    if (!empty($resADoc)) {
        //remove license which is not exists when save
            $upd_where = array(
                'clause' => 'id = :id',
                'params' => array(
                    ':id' => $resADoc['id'],
                ),
            );
            $pdo->update('agent_license', array("is_deleted" => 'Y', 'updated_at' => 'msqlfunc_NOW()', 'license_removal_date'=>'msqlfunc_NOW()','license_status'=>'Inactive'), $upd_where);
        // }
        $agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where md5(id)=:id",array(":id"=>$_POST['agent_id']));
        $description =array();
        $description['ac_message'] = array(
            'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' License State Deleted '.$resADoc['selling_licensed_state'].' on '.$agent_name['name'].' (',
            'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
                'title'=> $agent_name['rep_id'],
            ),
            'ac_message_2' =>')',
            );


        $desc=json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $agent_name['id'], 'Agent', 'Agent License Deleted',$_SESSION['admin']['name'],"",$desc);
        return true;
    }
        return false;
}

$agent_id = $_POST['agent_id'];
$agent_dec_id = getname('customer',$agent_id,'id','md5(id)');

$is_ajax_license = !empty($_POST['is_ajax_license']) ? $_POST['is_ajax_license'] : '' ;
if ($is_ajax_license) {
    $result = array();
    $license_expiry = $_POST["license_expiry"];
    $license_not_exp = !empty($_POST['license_not_expire']) ? $_POST['license_not_expire'] : 'N';
    $license_number = $_POST['license_number'];
    $license_active = $_POST["license_active_date"];
    $license_state = !empty($_POST['license_state']) ? $_POST['license_state'] : '';
    $license_type = !empty($_POST["license_type"]) ? $_POST["license_type"] : array();
    $license_auth = !empty($_POST["licsense_authority"]) ? $_POST["licsense_authority"] : array() ;
    $lid = !empty($_POST["lid"]) ? $_POST["lid"] : array() ;
    $hdn_license = $_POST["hdn_license"];
    $hdn_license = array_flip($hdn_license);
    $edit = !empty($_POST['edit']) ? $_POST['edit'] : '';

    foreach($hdn_license as $key => $value){
        $license_staten[$key] = $license_state;
        $license_numbern[$key] = $license_number;
        $license_activen[$key] = $license_active;
        $license_typen[$key] = $license_type;
        $license_authn[$key] = $license_auth;
        $license_expiryn[$key] = $license_expiry;
        $license_not_expn[$key] = $license_not_exp;
        $hdn_license[$key] = $lid;
        $editn[$key] = $edit;
    }
    $ajax = 1;
    check_agent_license_validation($agent_dec_id,$validate,$hdn_license,$license_staten,$license_numbern,$license_activen,$license_typen,$license_authn,$license_expiryn,$license_not_expn,$editn,$ajax);
    if($validate->isValid()){
        $doc_id = add_update_license($hdn_license,$license_staten,$license_numbern,$license_activen,$license_typen,$license_authn,$license_expiryn,$license_not_expn,$ajax);
        $result['status'] = "success";
        $result['doc_id'] = $doc_id;
        agent_profile_activity($doc_id);
    }else{
        $errors = $validate->getErrors();
        $result['errors'] = $errors;
        $result['status'] = "fail";
    }
    header('Content-type: application/json');
    echo json_encode($result); 
    exit;
}
$is_address_verified = $_POST['is_address_verified'];
$agent_update_activity = array();
$company = checkIsset($_POST['company']);
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$address = $_POST['address'];
$address_2 = checkIsset($_POST['address_2']);
$city = $_POST['city'];
$state = $_POST['state'];
$zipcode = str_replace("_",'',$_POST['zipcode']);
$display_in_member = !empty($_POST['display_in_member']) ? 'Y' : 'N';
$public_name = $_POST["public_name"];
$public_email = checkIsset($_POST["public_email"]);
$public_phone = $_POST["public_phone"];
$username = $_POST['username'];        
$email = checkIsset($_POST['email']);
$cell_phone = !empty($_POST['cell_phone']) ? phoneReplaceMain($_POST['cell_phone']) : '';
$dob = $_POST['dob'];
$cust_id = $pdo->selectONe("SELECT id from customer where md5(id)=:id",array(":id"=>$agent_id));
$ssn = phoneReplace($_POST['ssn']);
$is_ssn_edit = $_POST['is_ssn_edit'];
$password = !empty($_POST['password']) ? $_POST['password'] : '';
$c_password = !empty($_POST['c_password']) ? $_POST['c_password'] : '';
$is_2fa = !empty($_POST['is_2fa']) ? $_POST['is_2fa'] : 'N';
$is_ip_restriction = !empty($_POST['is_ip_restriction']) ? $_POST['is_ip_restriction'] : 'N';
$allowed_ip_res = !empty($_POST['allowed_ip_res']) ? $_POST['allowed_ip_res'] : array();
if($is_ip_restriction == "N") {
    $allowed_ip_res = array();
}
$send_via = checkIsset($_POST['send_via']);
$via_mobile = checkIsset($_POST['via_mobile'])!='' ? phoneReplaceMain($_POST['via_mobile']) : '';
$via_email = checkIsset($_POST['via_email']);

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
if($is_ip_restriction == "Y") {
    foreach ($allowed_ip_res as $key => $allowed_ip) {
        $validate->string(array('required' => true, 'field' => 'ip_address_'.$key, 'value' => $allowed_ip), array('required' => 'IP Address is required'));
        if (!empty($allowed_ip) && !filter_var($allowed_ip, FILTER_VALIDATE_IP)) {
            $validate->setError('ip_address_'.$key, 'IP Address not valid');
        }
    }
}

if($is_2fa == 'Y'){
    if($send_via == ''){
      $validate->setError('send_via', 'Please select any method.');
    }else{
      if($send_via == 'sms'){
        $validate->phoneDigit(array('required' => true, 'field' => 'via_mobile', 'value' => $via_mobile), array('required' => 'Phone number is required', 'invalid' => 'Enter valid phone number'));
      }else{
        $validate->email(array('required' => true, 'field' => 'via_email', 'value' => $via_email), array('required' => 'Email Address is required.', 'invalid' => 'Please enter valid Email Address'));
      }
    }
}

if($display_in_member == 'N'){
    if ($public_name == "") {
        $validate->string(array('required' => true, 'field' => 'public_name', 'value' => $public_name), array('required' => 'Name is required'));
    }
    if ($public_email == "") {
        $validate->email(array('required' => true, 'field' => 'public_email', 'value' => $public_email), array('required' => 'Email is required.', 'invalid' => 'Please enter valid email'));
    }
    if ($public_phone == "") {
        $validate->digit(array('required' => true, 'field' => 'public_phone', 'value' => $public_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
    }
}

if ($username != "") {
    $validate->regex(array('required' => true, 'pattern' => '/^[A-Za-z0-9]+$/', 'field' => 'username', 'value' => $username, 'min' => 4, 'max' => 20), array('required' => 'Username is required', 'invalid' => 'Valid Username is required'));
    if (!$validate->getError('username')) {
        if (!isValidUserName($username, $cust_id['id'])) {
            $validate->setError("username", "Username already exist");
        }
    }
}else{
    $validate->string(array('required' => true, 'field' => 'username', 'value' => $username), array('required' => 'Username is required'));
}
if ($fname == "") {
    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'Firstname is required'));
}

if ($lname == "") {
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Lastname is required'));
}

$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
/*if ($email != "") {
    $selectEmail = "SELECT email FROM customer WHERE type='Agent' AND email = :email AND md5(id)!=:id AND is_deleted='N'";
    $where_select_email = array(':email' => $email, ":id" => $agent_id);
    $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
    if ($resultEmail) {
        $validate->setError("email", "This email is already associated with another agent account. <a href='".$AGENT_HOST."'>Click Here</a> to login");
    }
}*/

$validate->digit(array('required' => true, 'field' => 'cell_phone', 'value' => $cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

if ($address == "") {
    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
}
if(!empty($address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address_2)) {
    $validate->setError('address_2','Special character not allowed');
}
if ($city == "") {
    $validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
}
if ($state == "") {
    $validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
}
// if ($zipcode == "") {
    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5 ), array('required' => 'Zip Code is required'));
// }

$validate->string(array('required' => true, 'field' => 'dob', 'value' => $dob), array('required' => 'Date of Birth is required'));
if (!$validate->getError('dob') && !empty($dob)) {
    list($mm, $dd, $yyyy) = explode('/', $dob);
    if (!checkdate($mm, $dd, $yyyy)) {
        $validate->setError('dob', 'Valid Date of Birth is required');
    }
    if (!$validate->getError('dob')) {
        $age_y = dateDifference($dob, '%y');
        if ($age_y < 18) {
            $validate->setError('dob', 'You must be 18 years of age');
        } else if ($age_y > 90) {
            $validate->setError('dob', 'You must be younger then 90 years of age');
        }
    }
}

if ($is_ssn_edit == "Y") {
    $validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
}

// Account Tab
$account_type = checkIsset($_POST["account_type"]);
$license_number = !empty($_POST['license_number']) ? $_POST['license_number'] : '';;
$license_state = !empty($_POST['license_state']) ? $_POST['license_state'] : '';
$license_expiry = !empty($_POST["license_expiry"]) ? $_POST["license_expiry"] : ''  ;
$license_not_exp = !empty($_POST['license_not_expire']) ? $_POST['license_not_expire'] : 'N';
$license_active = !empty($_POST["license_active_date"]) ? $_POST["license_active_date"] : '' ;
$license_type = !empty($_POST["license_type"]) ? $_POST["license_type"] : array();
$license_auth = !empty($_POST["licsense_authority"]) ? $_POST["licsense_authority"] : array() ;
$hdn_license = !empty($_POST["hdn_license"]) ? $_POST["hdn_license"] : array();
$npn_no = $_POST['npn_number'];
$w9_form_business = checkIsset($_FILES["w9_form_business"]);
$e_o_coverage = checkIsset($_POST['e_o_coverage']);
$e_o_by_parent = isset($_POST['e_o_by_parent']) ? $_POST['e_o_by_parent']:'N';
$edit = !empty($_POST['edit']) ? $_POST['edit'] : array();
if ($e_o_coverage == "Y") {
$e_o_amount = str_replace(array("$", ","), array("", ""), $_POST['e_o_amount']);
$e_o_expiration = $_POST['e_o_expiration'];
$e_o_document = checkIsset($_FILES['e_o_document']);
}

$is_special_text_display = !empty($_POST['is_special_text_display']) ? 'Y' :'N';
$special_text_display = $_POST['special_text_display'];

if ($account_type == "Business") {
    $business_name = $_POST['business_name'];
    $business_address = $_POST['business_address'];
    $business_address2 = checkIsset($_POST['business_address2']);
    $business_city = $_POST['business_city'];
    $business_state = $_POST['business_state'];
    $business_zipcode = $_POST['business_zipcode'];
    $business_taxid = $_POST['business_taxid'];
} 
$license_not_expn = array();
foreach($hdn_license as $key => $hdn){
    $license_not_expn[$key] = isset($license_not_exp[$key]) ? $license_not_exp[$key] : 'N' ;
}

if ($account_type == "") {
    $validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Account type is required'));
}

if (!empty($license_number)) {
    foreach ($license_number as $lnkey => $lNum) {
        $validate->string(array('required' => true, 'field' => 'license_number_' . $lnkey, 'value' => $lNum), array('required' => 'License number is required', 'invalid' => 'Valid license Number is required'));
    }
}

if(!empty($license_expiry)){
    $tempArr =array_keys($license_expiry);
    $tempId = end($tempArr);
    $temp_l_type = checkIsset($license_type[$tempId]);
    $temp_license_auth = checkIsset($license_auth[$tempId]);
    $templ_state = checkIsset($license_state[$tempId]);

    foreach ($license_expiry as $lekey => $lexpiry) {
        
        $temp_license_typeArr = $license_type;
        $temp_license_authArr = $license_auth;
        $temp_license_state = $license_state;

        if(isset($temp_license_typeArr[$tempId]))
            unset($temp_license_typeArr[$tempId]);
        if(isset($temp_license_state[$tempId]))
            unset($temp_license_state[$tempId]);
        if(isset($temp_license_authArr[$tempId]))
            unset($temp_license_authArr[$tempId]);

        if(empty($temp_license_state)){
            $validate->string(array('required' => true, 'field' => 'license_state_' . $lekey, 'value' => $templ_state), array('required' => 'License state is required'));
        }
        
        
        if($tempId != $lekey){
            if ($templ_state == $temp_license_state[$lekey] && $temp_l_type == checkIsset($temp_license_typeArr[$lekey]) &&$temp_license_auth == checkIsset($temp_license_authArr[$lekey])) {
                $validate->setError("license_state_" . $tempId, "Please select different license state");
            }
        }
    }
}

if (!empty($license_expiry)) {
    check_agent_license_validation($agent_dec_id,$validate,$hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_expn,$edit);
}


// if ($npn_no == "") {
    $validate->digit(array('required' => true, 'field' => 'npn_number', 'value' => $npn_no), array('required' => 'NPN number is required', 'invalid' => 'Valid NPN number is required'));
// }

    $validate->string(array('required' => true, 'field' => 'e_o_coverage', 'value' => $e_o_coverage), array('required' => 'Select any option'));
    if ($e_o_coverage == 'Y' && $e_o_by_parent=="N") {
            $validate->string(array('required' => true, 'field' => 'e_o_expiration', 'value' => $e_o_expiration), array('required' => 'Expiration Date is required'));
            if ($e_o_expiration != "") {
                if (validateDate($e_o_expiration,'m/d/Y')) {
                    // if (!isFutureDateMain($e_o_expiration,'m/d/Y')) {
                    //     $validate->setError("e_o_expiration", "Please Add Future Expiration Date is required");
                    // }
                } else {
                    $validate->setError("e_o_expiration", "Valid Expiration Date is required");
                }
            }
    }

if ($e_o_coverage == "Y" && $e_o_by_parent == 'N') {
    if (empty($_POST["chk_e_o_document"]) && !empty($e_o_document)) {
        if (checkIsset($e_o_document['error']) == UPLOAD_ERR_NO_FILE) {
            $validate->setError('e_o_document', "Please add E&O document");
        } else {
            if (!empty($e_o_document["name"]) && !in_array($e_o_document["type"], array("application/pdf", "application/doc"))) {
                $validate->setError('e_o_document', "Please add valid E&O document");
            }
        }
    }
}

if($is_special_text_display == 'Y'){
    $validate->string(array('required' => true, 'field' => 'special_text_display', 'value' => $special_text_display), array('required' => 'Display Text is required'));
}

if ($account_type == "Business") {              
    if ($business_name == "") {
        $validate->string(array('required' => true, 'field' => 'business_name', 'value' => $business_name), array('required' => 'Agency Legal Name is required.'));
    }
    if ($business_address == "") {
        $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $business_address), array('required' => 'Address required.'));
    }
    
    if(!empty($business_address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$business_address2)) {
        $validate->setError('business_address2','Special character not allowed');
    }

    if ($business_city == "") {
        $validate->string(array('required' => true, 'field' => 'business_city', 'value' => $business_city), array('required' => 'City required.'));
    }
    if ($business_state == "") {
        $validate->string(array('required' => true, 'field' => 'business_state', 'value' => $business_state), array('required' => 'State required.'));
    }
    // if ($business_zipcode == "") {
        $validate->string(array('required' => true, 'field' => 'business_zipcode', 'value' => $business_zipcode ,'min'=>5), array('required' => 'Zip Code required.'));
    // }
    if (!$validate->getError('business_zipcode')){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($business_zipcode);
        if($zipAddress['status'] !='success'){
            $validate->setError("business_zipcode",$zipAddress['error_message']);
        }
    }
                
    if (empty($_POST["w9_pdf"]) && !empty($w9_form_business)) {
            if (!isset($w9_form_business) || $w9_form_business['error'] == UPLOAD_ERR_NO_FILE) {
                $validate->setError('w9_form_business', "Please add w9 file");
            } else {
                if ($w9_form_business["type"] != "application/pdf") {
                    $validate->setError('w9_form_business', "Please add valid w9 pdf file");
                }
            }
    }

}

function add_update_license($hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_exp,$ajax=''){
    $agent_doc_id ='';
    global $pdo;

    $license_key_arr = array(
        'selling_licensed_state' => 'Selling License state',
        'license_num' => 'License Number',
        'license_active_date' => 'License Active Date',
        'license_type' => 'License Type',
        'license_not_expire' => 'License Not Expire',
        'license_exp_date' => 'License Expire Date',
        'license_auth' => 'License Auth',
        'license_status' => 'License Status',
    );
    //insert and update license
    $agent_licence_activity = array();
    $i=0;
    foreach ($hdn_license as $hkey => $h_id) {
        $i++;        
        // pre_print($h_id,false);
        //check if license id is empty/zero then we need to insert else we need to update
        if (empty($h_id)) {
            $h_id = 0;
        }
        $selADoc = "SELECT id FROM agent_license WHERE md5(agent_id)=:agent_id AND id=:id AND is_deleted='N'";
        $whrADoc = array(":agent_id" => $GLOBALS['agent_id'], ":id" => $h_id);
        $resADoc = $pdo->selectOne($selADoc, $whrADoc);       
        if (!empty($resADoc['id'])) {
            //update license information
            $updateParams = array(
                'selling_licensed_state' => $license_state[$hkey],
                'license_num' => $license_number[$hkey],
                'license_active_date' => date('Y-m-d', strtotime($license_active[$hkey])),
                'license_type' => isset($license_type[$hkey]) ?  $license_type[$hkey] : '',
                'license_not_expire' => $license_not_exp[$hkey],
                'license_auth' => isset($license_auth[$hkey]) ? $license_auth[$hkey] : '',
                'updated_at' => 'msqlfunc_NOW()',
            );
            if ($license_expiry[$hkey] != "" && $license_not_exp[$hkey] =='N') {
                $updateParams['license_exp_date'] = date('Y-m-d', strtotime($license_expiry[$hkey]));
            }else{
                $updateParams['license_exp_date'] = date('Y-m-d', strtotime(date('12/31/2099')));
            }
            $upd_where = array(
                'clause' => 'md5(agent_id) = :agent_id and id = :id',
                'params' => array(
                    ':id' => $resADoc['id'],
                    ':agent_id' => $GLOBALS['agent_id'],
                ),
            );
            
            if(strtotime($updateParams['license_exp_date']) >= strtotime(date("Y-m-d"))){
                $updateParams['license_status'] = "Active";
            } else {
                $updateParams['license_status'] = "Inactive";
            }

            $updateParams = array_filter($updateParams, "strlen"); //removes null and blank array fields from array
            $updated_license_data = $pdo->update('agent_license', $updateParams, $upd_where,true);
            $j=$resADoc['id'];
            if(!empty($updated_license_data)){
                foreach($updated_license_data as $key => $license){
                    if(in_array($key,array('license_exp_date','license_active_date'))){
                        $license = getCustomDate($license);
                        $updateParams[$key] = getCustomDate($updateParams[$key]);
                    }
                    if(in_array($license,array('Y','N'))){
                        $license = $license == 'Y' ? "Selected" : "Unselected";
                        $updateParams[$key] = $updateParams[$key] == 'Y' ? "Selected" : "Unselected";
                    }

                    if($updateParams[$key] == 'Business'){
                        $updateParams[$key] ='Agency';
                    }else if($updateParams[$key] == 'Personal'){
                        $updateParams[$key] ='Agent';
                    }

                    if($license == 'Business'){
                        $license ='Agency';
                    }else if($license == 'Personal'){
                        $license ='Agent';
                    }

                    if(array_key_exists($key,$updateParams)){
                        if($resADoc['id']==$j){
                            $agent_licence_activity[] = 'In License '.$i.' - License Number : '.$updateParams['license_num'].' , License State : '.$updateParams['selling_licensed_state'].'<br>';
                            $j++;
                        }
                        $agent_licence_activity[] = '&nbsp;&nbsp;'.$license_key_arr[$key] .' Updated : From '.$license.' To '.$updateParams[$key]."<br>";
                    }
                }
            }
        } else {
            //ishit
            $ag = "SELECT id FROM customer WHERE md5(id)=:agent_id AND is_deleted='N'";
            $whrADoc = array(":agent_id" => $GLOBALS['agent_id']);
            $ag_res = $pdo->selectOne($ag, $whrADoc);
            $insparams = array(
                'agent_id' => $ag_res['id'],
                'selling_licensed_state' => $license_state[$hkey],
                'license_num' => $license_number[$hkey],
                'license_added_date'=>'msqlfunc_NOW()',
                'license_active_date'=>date('Y-m-d', strtotime($license_active[$hkey])),
                'license_not_expire' => $license_not_exp[$hkey],
                'license_type' => isset($license_type[$hkey]) ? $license_type[$hkey] : ''  ,
                'license_auth' => isset($license_auth[$hkey]) ?  $license_auth[$hkey] : '',
                'created_at' => 'msqlfunc_NOW()',
                'updated_at' => 'msqlfunc_NOW()',
            );
            if ($license_expiry[$hkey] != "") {
                $insparams['license_exp_date'] = $license_not_exp[$hkey]=='Y' ? date('Y-m-d', strtotime(date('12/31/2099'))) : date('Y-m-d', strtotime($license_expiry[$hkey]));
            }
            $insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
            $agent_doc_id = $pdo->insert('agent_license', $insparams);
            $agent_licence_activity[] = 'New License Addedd for State : '.$license_state[$hkey].'.<br>';
        }
    }
    // return $agent_doc_id;
    return $agent_licence_activity;
}

//writing number start
$writing_number = isset($_POST['writing_number']) ? $_POST['writing_number'] : array();
$writing_state = isset($_POST['writing_state']) ? $_POST['writing_state'] : array();

if(count($writing_number) > 0){
    foreach($writing_number as $id => $number){

        if(!empty($writing_state[$id]) && empty($writing_number[$id])){
            $validate->setError("writing_number_".$id, "Please enter writing number");
        }
        if(empty($writing_state[$id]) && !empty($writing_number[$id])){
            $validate->setError("writing_state_".$id, "Please enter writing State");
        }
    }
}
//writing number end
$new_update_details =array(
    'account_type' => checkIsset($account_type)=='Business' ? 'Agency' : 'Agent',
    'fname' => checkIsset($fname),
    'lname' => checkIsset($lname),
    'address' => checkIsset($address),
    'address_2' => checkIsset($address_2),
    'city' => checkIsset($city),
    'state' => checkIsset($state),
    'zip' => checkIsset($zipcode),
    'public_name' => checkIsset($public_name),
    'public_email' => checkIsset($public_email),
    'public_phone' => checkIsset($public_phone),
    'user_name' => checkIsset($username),
    'email' => checkIsset($email),
    'cell_phone' => checkIsset($cell_phone),
    'last_four_ssn' =>substr($ssn, -4),
    'company' => checkIsset($company),
    'company_name' => checkIsset($business_name),
    'company_address' => checkIsset($business_address),
    'company_address_2' => checkIsset($business_address2),
    'company_city' => checkIsset($business_city),
    'company_state' => checkIsset($business_state),
    'company_zip' => checkIsset($business_zipcode),
    'tax_id' => checkIsset($business_taxid),
    'npn' => checkIsset($npn_no),
    'display_in_member' =>  checkIsset($_POST['display_in_member']) == 'Y' ? 'Selected' : 'Unselected' ,
    'is_branding' => !empty($_POST['is_branding']) ? 'Y' : 'N',
    'e_o_coverage' => checkIsset($e_o_coverage),
    'e_o_amount' => checkIsset($e_o_amount),
    'e_o_expiration' => checkIsset($e_o_expiration),
    'birth_date' => $dob,
    'is_special_text_display' => checkIsset($is_special_text_display) == 'Y' ? 'Selected' : 'Unselected' ,
    'special_text_display' => checkIsset($special_text_display),
    'is_2fa' => isset($_POST['is_2fa']) ? 'Selected' : 'Unselected',
    'is_ip_restriction' => isset($_POST['is_ip_restriction']) ? 'Selected' : 'Unselected',
    'allowed_ip' => implode(',',array_values($allowed_ip_res)),
    'send_otp_via' => checkIsset($send_via),
    'via_sms' => checkIsset($via_mobile),
    'via_email' => checkIsset($via_email),
);
    if (!$validate->getError('zipcode')){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zipcode);
        if($zipAddress['status'] !='success'){
            $validate->setError("zipcode",$zipAddress['error_message']);
        }
    }
if($validate->isValid()){

    if ($account_type == "Business") {
        $upd_cs_param = array(
            'company_name' => makeSafe($business_name),
            'company_address' => makeSafe($business_address),
            'company_address_2' => makeSafe($business_address2),
            'company_city' => $business_city,
            'company_state' => $business_state,
            'company_zip' => $business_zipcode,
            'tax_id' => $business_taxid,
        );
    }
    $upd_cs_param['account_type'] = $account_type;
    $upd_cs_param['npn'] = $npn_no;
    $upd_cs_param['company'] = $company;
    $upd_cs_param['is_address_verified'] = $is_address_verified;
    $upd_cs_param['display_in_member'] = !empty($_POST['display_in_member']) ? 'Y' : 'N';
    $upd_cs_param['is_branding'] = !empty($_POST['is_branding']) ? 'Y' : 'N';
    $upd_cs_param['is_special_text_display'] = $is_special_text_display;
    $upd_cs_param['special_text_display'] = $special_text_display;
    $upd_cs_param['is_2fa'] = $is_2fa;
    if($send_via !=''){
      $upd_cs_param['send_otp_via'] = $send_via;
      if($send_via == 'sms'){
        $upd_cs_param['via_sms'] = $via_mobile;
      }else{
        $upd_cs_param['via_email'] = $via_email;
      }
    }
    $upd_cs_param['is_ip_restriction'] = $is_ip_restriction;
    if($is_ip_restriction == "Y") {
        $upd_cs_param['allowed_ip'] = implode(',',array_values($allowed_ip_res));
    } else {
        $upd_cs_param['allowed_ip'] = "";
    }
    $upd_params = array(
        'fname' => $fname,
        'lname' => $lname,
        'email' => $email,
        'cell_phone' => $cell_phone,
        'address' => $address,
        'address_2' => $address_2,
        'city' => $city,
        'state' => $state,
        'zip' => $zipcode,
        'public_name' => $public_name,
        'public_email' => $public_email,
        'public_phone' => $public_phone,
        'user_name' => $username,
        'birth_date' => date('Y-m-d',strtotime($dob)),
        'updated_at' => 'msqlfunc_NOW()'
    );

    if ($is_ssn_edit == "Y" && $ssn != "") {
        $upd_params['ssn'] = "msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
        $upd_params['last_four_ssn'] = substr($ssn, -4);
    }
    if ($password != "") {
        $upd_params['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
    }

    $c_upd_where = array(
        'clause' => 'md5(id)=:id',
        'params' => array(
            ':id' => $agent_id,
        ),
    );

    $cs_upd_where = array(
        'clause' => 'md5(customer_id) = :id',
        'params' => array(
            ':id' => $agent_id,
        ),
    );

    $w9_doc = $w9_form_business;

    if (!empty($w9_doc["name"])) {
        $agent_res = $pdo->selectOne("SELECT w9_pdf from customer_settings where md5(customer_id)=:id",array(":id"=>$agent_id));
        $w9_pdf_extension_tmp = explode(".", $w9_doc['name']);
        $w9_pdf_extension = end($w9_pdf_extension_tmp);
        $w9_pdf_tmp_name = $w9_doc['tmp_name'];
        $new_w9_pdf_name = 'w9_doc_' . round(microtime(true)) . '.' . $w9_pdf_extension;
        $new_update_details['w9_pdf'] = $new_w9_pdf_name;
        $existingW9FileName = $agent_res["w9_pdf"];
        if (!empty($existingW9FileName)) {
            if (file_exists($AGENT_DOC_DIR . $existingW9FileName)) {
                unlink($AGENT_DOC_DIR . $existingW9FileName);
            }
        }
        move_uploaded_file($w9_pdf_tmp_name, $AGENT_DOC_DIR . $new_w9_pdf_name);
        $response["w9_pdf"] = $AGENT_DOC_WEB . $new_w9_pdf_name;
        $update = array(
            'w9_pdf' => $new_w9_pdf_name,
        );
        $upd_where = array(
            'clause' => 'md5(customer_id) = :id',
            'params' => array(
                ':id' => $agent_id,
            ),
        );
        $update = array_filter($update, "strlen"); //removes null and blank array fields from array
        $pdo->update('customer_settings', $update, $upd_where);
        $agent_update_activity['customer_setting_doc'] = array("w9 document updated.");
    }

    $current_account_type = getname('customer_settings',$agent_id,'account_type','md5(customer_id)');

    $agent_update_activity['customer'] = $pdo->update('customer',$upd_params,$c_upd_where,true);
    $agent_update_activity['customer_settings'] = $pdo->update('customer_settings',$upd_cs_param,$cs_upd_where,true);

    // Link Agent to Their Agency
    if($upd_cs_param['account_type'] != $current_account_type){

        $agencyId = $functionsList->getAgencyId($agent_dec_id);
        $customer_settings = array("agency_id" => $agencyId);
        $functionsList->addCustomerSettings($customer_settings,$agent_dec_id);
        
        $selDownlineAgents = "SELECT id,rep_id,sponsor_id FROM customer WHERE is_deleted='N' AND type='Agent' AND upline_sponsors LIKE CONCAT('%,',".$agent_dec_id.",',%')";
        $resDownlineAgents = $pdo->select($selDownlineAgents);

        if(!empty($resDownlineAgents)){
            foreach ($resDownlineAgents as $downAgents) {
                $agencyId = $functionsList->getAgencyId($downAgents["id"]);
                $customer_settings = array("agency_id" => $agencyId);
                $functionsList->addCustomerSettings($customer_settings,$downAgents["id"]);
            }
        }
    }

    $agent_update_activity['agent_license'] = add_update_license($hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_expn);
    //e-o entry
    {
        if(!empty($e_o_document)){
        $tmp_v1 = explode(".", $e_o_document['name']);
        $extension = end($tmp_v1);
        $doc_tmp_name = $e_o_document['tmp_name'];
        $e_o_coverage_filename = 'agent_doc_' . round(microtime(true)) . '.' . $extension;
        $selADoc = "SELECT e_o_document FROM agent_document WHERE md5(agent_id)=:agent_id";
        $whrADoc = array(":agent_id" => $agent_id);
        $resADoc = $pdo->selectOne($selADoc, $whrADoc);
            if ($resADoc) {
                $updateparams = array(
                    'e_o_coverage' => $e_o_coverage,
                    'updated_at' => 'msqlfunc_NOW()',
                );
                if ($e_o_coverage == 'Y' &&!empty($e_o_document['name'])) {
                    $updateparams['e_o_document'] = $e_o_coverage_filename;

                    $existingErrorDocument = $resADoc["e_o_document"];
                    if ($existingErrorDocument != "") {
                        if (file_exists($AGENT_DOC_DIR . $existingErrorDocument)) {
                            unlink($AGENT_DOC_DIR . $existingErrorDocument);
                        }
                    }
                    move_uploaded_file($doc_tmp_name, $AGENT_DOC_DIR . $e_o_coverage_filename);
                    $response["e_o_document"] = $AGENT_DOC_WEB . $e_o_coverage_filename;
                }
                $upd_where = array(
                    'clause' => 'md5(agent_id) = :id',
                    'params' => array(
                        ':id' => $agent_id,
                    ),
                );
                $new_update_details['e_o_document'] = $updateparams['e_o_document'];
                $updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
                $pdo->update('agent_document', $updateparams, $upd_where);
                $agent_update_activity['agent_document_file'] = array('E&O Document Updated.');
            }  else {
                $insparamsAg = array(
                    'agent_id' => $agent_dec_id,
                    'e_o_coverage' => $e_o_coverage,
                    'created_at' => 'msqlfunc_NOW()',
                    'updated_at' => 'msqlfunc_NOW()',
                );
                if ($e_o_coverage == 'Y' && !empty($e_o_document['name'])) {
                    $insparamsAg['e_o_document'] = $e_o_coverage_filename;
                    move_uploaded_file($doc_tmp_name, $AGENT_DOC_DIR . $e_o_coverage_filename);
                    $response["e_o_document"] = $AGENT_DOC_WEB . $e_o_coverage_filename;
                }
                $insparamsAg = array_filter($insparamsAg, "strlen"); //removes null and blank array fields from array
                $agent_doc_id = $pdo->insert('agent_document', $insparamsAg);
                $agent_update_activity['agent_document'] = 'Agent Error and Ommissions Insurance (E&O) Detail Document Added.';
            } 
        }
        $selADoc = "SELECT id FROM agent_document WHERE md5(agent_id)=:agent_id";
        $whrADoc = array(":agent_id" => $agent_id);
        $resADoc = $pdo->selectOne($selADoc, $whrADoc);
        if (!empty($resADoc) && count($resADoc) > 0) {
            $updateparams = array(
                'e_o_coverage' => $e_o_coverage,
                'e_o_amount' => $e_o_amount,
                'by_parent'=>$e_o_by_parent,
                'updated_at' => 'msqlfunc_NOW()',
            );
            if ($e_o_expiration != "") {
                $updateparams['e_o_expiration'] = date('Y-m-d', strtotime($e_o_expiration));
            }
            $upd_where = array(
                'clause' => 'md5(agent_id) = :id',
                'params' => array(
                    ':id' => $agent_id,
                ),
            );
            $updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
            $agent_update_activity['agent_document'] = $pdo->update('agent_document', $updateparams, $upd_where,true);
        }else{
            $insparamsAgDoc = array(
                'agent_id' => $agent_dec_id,
                'e_o_coverage' => $e_o_coverage,
                'e_o_amount' => $e_o_amount,
                'created_at' => 'msqlfunc_NOW()',
                'updated_at' => 'msqlfunc_NOW()',
            );
            
            $insparamsAgDoc = array_filter($insparamsAgDoc, "strlen"); //removes null and blank array fields from array
            $agent_doc_id = $pdo->insert('agent_document', $insparamsAgDoc);
            $agent_update_activity['agent_document'] = 'Agent Error and Ommissions Insurance (E&O) Detail Added.';
        }
    }
//writing number start
    if(!empty($writing_number)){
        foreach($writing_number as $id => $number){
            $db_writing_number = $pdo->selectOne("SELECT id,writing_number from agent_writing_number where agent_id=:agent_id and carrier_id=:carrier_id and is_deleted='N'",array(":agent_id"=>$cust_id['id'],":carrier_id"=>$id));

            if(!empty($db_writing_number['id'])){
                $upd_writing_number_params = array(
                    'writing_number' => $number,
                    'updated_at' => 'msqlfunc_NOW()',
                );
                if($db_writing_number['writing_number']!=$number){
                    $agent_update_activity['writing_number'.$id] = array("update" =>' Updated writing number from '.$db_writing_number['writing_number']." To ".$number);
                }
                
                if(empty($number)){
                    $upd_writing_number_params  = array(
                        'is_deleted' => 'Y',
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                }
                $where = array(
                    "clause" => "id=:id",
                    "params" => array(":id"=>$db_writing_number['id']) 
                );
                if($db_writing_number['writing_number']!=$number){
                    $pdo->update("agent_writing_number",$upd_writing_number_params,$where);
                    if(empty($number)){
                        $agent_update_activity['writing_number'.$id] = array("deleted"=>' Deleted writing number '.$db_writing_number['writing_number']);
                    }
                }
                $db_states = $pdo->select("SELECT id,state from agent_writing_states where writing_id=:id and is_deleted='N'",array(":id"=>$db_writing_number['id']));
                $db_state = array();
                if(!empty($db_states)){
                    foreach($db_states as $state)
                    $db_state[$state['id']] = $state['state'] ;
                }

                $wr_state = !empty($writing_state[$id]) ? $writing_state[$id] : array();
                $update_state = array();
                $update_state = array_diff($db_state,$wr_state);
                if(!empty($update_state)){
                    foreach($update_state as $sid => $state){
                        $writing_state_params = array(
                            'is_deleted' => 'Y',
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                        $upd_where = array(
                            "clause" => "id=:id",
                            "params" => array(":id"=>$sid)
                        );
                        $pdo->update("agent_writing_states",$writing_state_params,$upd_where);
                    }
                    $agent_update_activity['agent_writing_states'.$id] = array("deleted"=>" State Deleted For writing number ".$number." : ".implode(',',$update_state));
                }                

                $insert_state = array();
                $insert_state = array_diff($wr_state,$db_state);

                if(!empty($insert_state) && !empty($number)){
                    foreach($insert_state as $state){
                        $writing_state_ins_params = array(
                            'writing_id' => $db_writing_number['id'],
                            'state' => $state,
                            'updated_at' => 'msqlfunc_NOW()',
                            'created_at' => 'msqlfunc_NOW()'
                        );
                        $pdo->insert("agent_writing_states",$writing_state_ins_params);
                    }
                    $agent_update_activity['agent_writing_states'.$db_writing_number['id']]=array("inserted"=>" State Inserted For writing number ".$number." : ".implode(',',$insert_state));
                }

            }else{
                $writing_number_params = array(
                    'carrier_id' => $id,
                    'agent_id' => $cust_id['id'],
                    'writing_number' => $number,
                    'updated_at' => 'msqlfunc_NOW()',
                    'created_at' => 'msqlfunc_NOW()'
                );
                $inserted_id = $pdo->insert("agent_writing_number",$writing_number_params);

                if(!empty($writing_state[$id])){
                    foreach($writing_state[$id] as $state){
                        $writing_state_params = array(
                            'writing_id' => $inserted_id,
                            'state' => $state,
                            'updated_at' => 'msqlfunc_NOW()',
                            'created_at' => 'msqlfunc_NOW()'
                        );
                        $pdo->insert("agent_writing_states",$writing_state_params);
                    }
                    $agent_update_activity['agent_writing_states'.$number]=array("inserted" => " State Inserted For Writing number ".$number." : ".implode(',',$writing_state[$id]));
                }
            }
        }
        
    }
//writing number end
    $response['status'] = "success";   
}
$description = array();
if(!empty($agent_update_activity)){

    agent_profile_activity($agent_update_activity);
}
function agent_profile_activity($agent_update_activity){
    global $pdo,$agent_id,$ADMIN_HOST,$new_update_details,$password;
    $flg = "true";
    $agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where md5(id)=:id",array(":id"=>$agent_id));
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'  Updated Profile In Agent '.$agent_name['name'].' (',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
            'title'=> $agent_name['rep_id'],
        ),
        'ac_message_2' =>')<br>',
        );
    foreach($agent_update_activity as $key => $value){
        if(!empty($value) && is_array($value)){
            foreach($value as $key2 => $val){
                if(array_key_exists($key2,$new_update_details)){
                        if(in_array($val,array('Y','N'))){
                            $val = $val == 'Y' ? "selected" : "unselected";
                        }
                        if($key2=='account_type'){
                            $val = $val =='Business' ? 'Agency' : 'Agent';
                        }
                        if($key2=='birth_date'){
                            $val = date('m/d/Y',strtotime($val));
                        }
                        $tmp_key2 = ucfirst(str_replace('_',' ',$key2));

                        if(in_array($key2,array('is_2fa'))){
                            $tmp_key2 = "Two-Factor Authentication (2FA)";
                        }
                        if(in_array($key2,array('is_ip_restriction'))){
                            $tmp_key2 = "IP Address Restriction";
                        }
                        
                        $description['key_value']['desc_arr'][$tmp_key2] = ' Updated From '.$val." To ".$new_update_details[$key2].".<br>";
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
        $description['description_novalue'] = 'No updates in agent profile page.';
    }
    
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $agent_name['id'], 'Agent', 'Agent Profile Updated',$_SESSION['admin']['name'],"",$desc);
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