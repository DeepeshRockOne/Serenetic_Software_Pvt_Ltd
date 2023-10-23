<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();

$is_ajaxed = checkIsset($_POST['is_ajax_member_form']) ;
$is_update = checkIsset($_POST['is_update']) ;
$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']) ;
$is_ajax_tax_form = checkIsset($_POST['is_ajax_tax_form']) ;

if(!empty($_POST['showing_pass']) || (!empty($_POST['type']) && $_POST['type'] == 'encrypt')){
    $salaryRes = salaryEncryptDecrypt('admin','customer');
    header('Content-Type: application/json');
    echo json_encode($salaryRes);
    exit;
}

if($is_ajax_tax_form && $is_update){
    $lead_id = $_POST['lead_id'];
    $customer_id = $_POST['customer_id'];

    $lead_sql = "SELECT l.id, CONCAT(l.fname,' ',l.lname) as lead_name ,l.lead_id as rep_id,l.sponsor_id,l.customer_id,c.status as customer_status ,s.rep_id as sponsor_rep_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.rep_id as customer_rep_id
            FROM leads l
            JOIN customer c ON(c.id = l.customer_id)
            JOIN customer s ON(s.id=l.sponsor_id)
            WHERE md5(l.id)=:id";
    $lead_row = $pdo->selectOne($lead_sql,array(":id"=>$lead_id));

    
    if(empty($lead_row['id'])){
        setNotifyError("No member found!");
        redirect("member_listing.php");
    }

    $validate = new Validation();
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

    $response = array('status'=>'success');

    if($validate->isValid()){

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

        $upd_where = array(
            'clause' => 'md5(id)=:id AND customer_id=:customer_id',
            'params' => array(
                ':id' => $lead_id,
                ':customer_id' => $lead_row['customer_id'],
            ),
        );
        $lead_update_activity['leads'] = $pdo->update('leads',$tax_details,$upd_where,true);

        if(!empty($lead_update_activity)){
            $flg = "false";
            
            $description['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>'  Updated Tax info In Member '.$lead_row['customer_name'].' (',
                'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/members_details.php?id='.$customer_id,
                'title'=> $lead_row['customer_rep_id'],
                ),
                'ac_message_2'=>') details : ',
            );
            foreach($lead_update_activity as $key => $value){
                if(!empty($value) && is_array($value)){
                    foreach($value as $key2 => $val){
                        if($val == $tax_details[$key2]){
                            continue;
                        }
                        if(array_key_exists($key2,$tax_details)){
                            $description['key_value']['desc_arr'][$key2] = ' Updated From '.addDashtoBlankField($val)." To ".$tax_details[$key2]."<br>";
                            $flg = "true";
                            if($key2 == 'income' && $lead_row['sponsor_rep_id'] == 'G56118'){
                                $description['key_value']['desc_arr'][$key2] = 'Salary updated';
                            }
                        } else {
                            $description['description2'][] = ucwords(str_replace('_',' ',$val));
                            $flg = "true";
                        }
                    }
                }
            }
            if($flg == "true"){
                $desc=json_encode($description);
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $lead_row['customer_id'], 'customer', 'Admin Update Member Detail',($_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']),"",$desc);    
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $lead_row['id'], 'Lead', 'Lead Profile Updated',($_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']),"",$desc);
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

if($is_address_ajaxed){

    $response = array("status"=>'success');
    $address = $_POST['address'];
    $address_2 = checkIsset($_POST['address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zip = $_POST['zip'];
    $old_address = $_POST['old_address'];
    $old_zip = $_POST['old_zip'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'primary_zip', 'value' => $zip,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zip);

        if($old_address != $address || $zip!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
            
            if($zipAddress['status'] =='success'){
                $response['city'] = $zipAddress['city'];
                $response['state'] = $getStateNameByShortName[$zipAddress['state']];
                $response['zip_response_status']='success';

                $tmpAdd1=$address;
                $tmpAdd2=!empty($address_2) ? $address_2 : '#';
                $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zip);
                
                if(!empty($address_response)){
                    if($address_response['status']=='success'){
                        $response['address'] = $address_response['address'];
                        $response['address2'] = $address_response['address2'];
                        $response['city'] = $address_response['city'];
                        $response['state'] = $getStateNameByShortName[$address_response['state']];
                        $response['zip'] = $address_response['zip'];
                        $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zip;
                        $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
                        $response['zip_response_status']='';
                        $response['address_response_status']='success';
                    }
                }
            }else if($zipAddress['status'] =='fail'){
                $response['status'] = 'fail';
                $response['errors'] = array("primary_zip"=>$zipAddress['error_message']);
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
if($is_ajaxed && $is_update){
    $customer_id = $_POST['customer_id'];
    $rows = $pdo->selectOne("SELECT c.id,CONCAT(c.fname,' ',c.lname) as name,c.rep_id,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn,s.business_name from customer c LEFT JOIN customer s ON(s.id=c.sponsor_id) where md5(c.id)=:id and c.is_deleted='N'",array(":id"=>$customer_id));
    if(empty($rows['id'])){
        setNotifyError("No member found!");
        redirect("member_listing.php");
    }
    $response = array();
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = checkIsset($_POST['email']);
    $cell_phone = phoneReplaceMain($_POST['cell_phone']);
    $address = $_POST['address'];
    $address_2 = checkIsset($_POST['address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $old_state = checkIsset($_POST['old_state']);
    $zip = $_POST['zip'];
    $birth_date = $_POST['birth_date'];
    $ssn = phoneReplaceMain($_POST['ssn']);
    $is_ssn_edit = $_POST['is_ssn_edit'];
    $password = !empty($_POST['password']) ? $_POST['password'] : '';
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : '';
    $group_class = !empty($_POST['group_class']) ? $_POST['group_class'] : '';
    $group_company_id = !empty($_POST['group_company_id']) ? $_POST['group_company_id'] : '';
    $sponsor_type = !empty($_POST['sponsor_type']) ? $_POST['sponsor_type'] : '';
    $is_address_verified = $_POST['is_address_verified'];
    
    $validate = new Validation();

    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
    $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Please enter valid email'));
    if (!empty($email) && !$validate->getError('email')) {
        $selectEmail = "SELECT email FROM customer WHERE type='customer' AND email = :email AND id!=:id AND is_deleted='N'";
        $where_select_email = array(':email' => $email, ":id" => $rows['id']);
        $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
        if ($resultEmail) {
            $validate->setError("email", "This email is already associated with another member account.");
        }
    }

    $validate->string(array('required' => true, 'field' => 'birth_date', 'value' => $birth_date), array('required' => 'Date of Birth is required'));
    if (!$validate->getError('birth_date') && !empty($birth_date)) {
        list($mm, $dd, $yyyy) = explode('/', $birth_date);
        if (!checkdate($mm, $dd, $yyyy)) {
            $validate->setError('birth_date', 'Valid Date of Birth is required');
        }
        if (!$validate->getError('birth_date')) {
            $age_y = dateDifference($birth_date, '%y');
            if ($age_y < 18) {
                $validate->setError('birth_date', 'You must be 18 years of age');
            } else if ($age_y > 90) {
                $validate->setError('birth_date', 'You must be younger then 90 years of age');
            }
        }
    }
    $validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
    if(empty($state)){
        $validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
    }
    $validate->digit(array('required' => true, 'field' => 'primary_zip', 'value' => $zip,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));
    $validate->digit(array('required' => true, 'field' => 'cell_phone', 'value' => $cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));

    // if($_POST['is_valid_address'] !='Y'){
    //     $validate->setError("address","Valid Address is required");
    // }
    
    if(!empty($address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address_2)) {
        $validate->setError('address_2','Special character not allowed');
    }

    if ($is_ssn_edit == "Y") {
        $validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
    }

    if($sponsor_type == 'Group'){
        $validate->string(array('required' => true, 'field' => 'group_class', 'value' => $group_class), array('required' => 'Enrolee Class is required'));
        if($group_company_id !=0){
            $validate->string(array('required' => true, 'field' => 'group_company_id', 'value' => $group_company_id), array('required' => 'Company is required'));
        }
    }

    //for strong password
    if (!$validate->getError('password') && !empty($password)) {
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

    if($state != $old_state){
        $productSql = "SELECT GROUP_CONCAT(p.id) as product_ids 
                    FROM prd_main p
                    JOIN website_subscriptions ws ON (ws.product_id=p.id AND ws.status NOT IN('Inactive') AND md5(ws.customer_id)=:memberId)
                    WHERE p.is_deleted='N' AND p.status='Active'";
        $productRes = $pdo->selectOne($productSql, array(":memberId" => $customer_id));
        $product_state = array();
        if(!empty($productRes['product_ids'])){
           $product_state = $pdo->selectOne("SELECT  GROUP_CONCAT(distinct(product_id)) as product_ids FROM `prd_no_sale_states` ps  LEFT JOIN prd_main p ON(p.id=ps.product_id and p.is_deleted='N') WHERE ps.is_deleted='N' AND product_id IN(".$productRes['product_ids'].") AND state_name = :name AND (ps.termination_date IS NULL OR DATE(ps.termination_date) >= CURDATE()) and p.no_sale_state_coverage_continue='N'",array(":name"=>$state));

        }
        if(!empty($product_state['product_ids'])){
            $products_arr = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(product_code,' - ',name) SEPARATOR '<br>') as product,GROUP_CONCAT(distinct(no_sale_state_coverage_continue)) as coverage_continue  from prd_main where id in(".$product_state['product_ids'].")");
            if(in_array('N',explode(',',$products_arr['coverage_continue']))){
                $response['products'] = $products_arr['product'];
                $response['product_popup'] = 'product_popup';
                $validate->SetError('state','There is conflict with one or more of this member products.');
                $validate->SetError('city','There is conflict with one or more of this member products.');
                $validate->SetError('primary_zip','There is conflict with one or more of this member products.');
            }            
        }
    }
    if (!$validate->getError('primary_zip')){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zip);
        if($zipAddress['status'] !='success'){
            $validate->setError("primary_zip",$zipAddress['error_message']);
        }
    }
    
    if($validate->isValid()){
        $acUPdate = false;
        $update_param = array(
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'cell_phone' => $cell_phone,
            'address' => $address,
            'address_2' => $address_2,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'birth_date' => date('Y-m-d',strtotime($birth_date)),
            'gender' => $gender,
        );

        if ($is_ssn_edit == "Y" && !empty($ssn)) {
            $update_param['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("_", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
            $update_param['last_four_ssn'] = substr($ssn,-4);
            $acUPdate = true;
        }
        
        if($sponsor_type == 'Group'){
            $update_param['group_company_id'] = $group_company_id;
        }

        if(!empty($password)){
            $update_param['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
            $update_param['is_password_set'] = "Y";
            $acUPdate = true;
        }
        $upd_where = array("clause"=>' id = :id ',"params"=>array(":id"=>$rows['id']));
        $upd_customer = $pdo->update('customer',$update_param,$upd_where,true);

        $upd_where = array("clause"=>' customer_id = :id ',"params"=>array(":id"=>$rows['id']));
        $pdo->update('leads',array('email' => $email),$upd_where);

        $MemberEnrollment->unqualified_leads_with_duplicate_email($email,$rows['id']);

        
        if($sponsor_type == 'Group' && !empty($group_class)){
            $cs_where1 = array("clause"=>' customer_id = :id ',"params"=>array(":id"=>$rows['id']));
            $cs_update = $pdo->update("customer_settings",array("class_id"=>$group_class),$cs_where1,true);
        }

        if(!empty($is_address_verified)){
            $cs_where1 = array("clause"=>' customer_id = :id ',"params"=>array(":id"=>$rows['id']));
            $pdo->update("customer_settings",array("is_address_verified"=>$is_address_verified),$cs_where1);
        }

        $ac_desc['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>'  update Member '.$rows['name'].'(',
            'ac_red_2'=>array(
              'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($rows['id']),
              'title'=> $rows['rep_id'],
            ),
            'ac_message_2'=>') details : ',
          );

        if(!empty($upd_customer)){
            foreach($upd_customer as $key => $value){
                if($key=='birth_date'){
                    $value = date('m/d/Y',strtotime($value));
                    $update_param[$key] = date('m/d/Y',strtotime($update_param[$key]));
                }else if($key == 'cell_phone'){
                    $value = format_telephone($value);
                    $update_param[$key] = format_telephone($update_param[$key]);
                }else if($key == 'is_password_set'){
                    continue;
                }else if($key == 'group_company_id'){
                    if($value == '0'){
                        $value = $rows['business_name'];
                    }else{
                        $value = getname("group_company",$value,'name','id');
                    }

                    if($update_param[$key] == 0){
                        $update_param[$key] = $rows['business_name'];
                    }else{
                        $update_param[$key] = getname("group_company",$update_param[$key],'name','id');
                    }
                }
                $ac_desc['key_value']['desc_arr'][$key] = ' From '.$value.' to '.$update_param[$key]; 
                $acUPdate = true;
            }
        }

        if($sponsor_type == 'Group' && !empty($group_class) && !empty($cs_update)){
            $vincr = $cs_update['class_id'].','.$group_class;
            $value_f = 'Blank';
            $value_t = 'Blank';
            if($vincr!=''){
                $valueArr = $pdo->select("SELECT class_name,id from group_classes WHERE id IN(".$vincr.") LIMIT 2");
                foreach($valueArr as $vl){
                    if($vl['id'] == $cs_update['class_id']){
                        $value_f = $vl['class_name'];
                    }else if($vl['id'] == $group_class){
                        $value_t = $vl['class_name'];
                    }
                }
            }
            $ac_desc['customer_settings_desc'] = ' Class updated from '.$value_f.' to '.$value_t; 
        }

        if(!empty($password)){
            $ac_desc['key_value']['desc_arr']['password'] = 'Password updated.';
        }

        if($acUPdate){
            activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $rows['id'], 'customer', 'Admin Update Member Detail',$_SESSION['admin']['name'],"",json_encode($ac_desc));
            // activity_feed(3,$rows['id'], 'customer' , $rows['id'], 'customer', 'Admin Update Member Detail',$rows['name'],"",json_encode($ac_desc));
        }
       

         $response['status'] = 'success';
    }else{
        $errors = $validate->getErrors();
        $response['status'] = 'fail';
        $response['errors'] = $errors;
    }

    header('Content-type: application/json');
    echo json_encode($response);
    exit();
}
$id = $_POST['id'];
$row = $pdo->selectOne("SELECT md5(c.id)  as id,c.fname,c.lname,c.rep_id,c.address,c.address_2,c.city,c.state as state,c.zip,c.birth_date,c.cell_phone,c.gender,c.email,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn, c.status,cs.class_id,c.sponsor_id,c.group_company_id,s.type as sponsor_type,s.rep_id as sponsor_rep_id,s.business_name as sbusiness_name,cs.is_address_verified,l.income,l.pre_tax_deductions_field,l.post_tax_deductions_field,l.w4_filing_status_field,l.w4_no_of_allowances_field,l.w4_two_jobs_field,l.w4_dependents_amount_field,l.w4_4a_other_income_field,l.w4_4b_deductions_field,l.w4_additional_withholding_field,l.state_filing_status_field,l.state_dependents_field,l.state_additional_withholdings_field,l.id as ai_lead_id from customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) LEFT JOIN customer s ON(s.id=c.sponsor_id) LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N') where md5(c.id)=:id and c.type='Customer' and c.is_deleted='N'",array(":id"=>$id));

$sponsor_type = $row['sponsor_type'];
$sponsor_rep_id = $row['sponsor_rep_id'];
$ai_lead_id = md5($row['ai_lead_id']);
$group_name = $row['sbusiness_name'];
$group_cmp_res = $resGroupClass = array();
$group_classes_id = '';
$group_company_id = '';
if($sponsor_type == 'Group'){
    $group_classes_id = $row['class_id'];
    $group_company_id = $row['group_company_id'];
    $sqlGroupClass="SELECT id, class_name FROM group_classes WHERE group_id=:group_id AND is_deleted='N'";
    $sqlGroupClassWhere=array(':group_id'=>$row['sponsor_id']);
    $resGroupClass=$pdo->select($sqlGroupClass,$sqlGroupClassWhere);
    $group_cmp_res = $pdo->select("SELECT id,name,location FROM group_company where group_id = :id AND is_deleted = 'N'",array(':id' => $row['sponsor_id']));
}

include_once 'tmpl/member_policy_tab.inc.php';
?>