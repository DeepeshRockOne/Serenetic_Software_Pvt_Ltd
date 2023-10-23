<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$response = array();

$lead_update_activity = array(); 
$lead_id = $_REQUEST['id'];
$lead_type = $_POST["lead_type"];
$company_name = !empty($_POST["company_name"])?$_POST["company_name"]:'';
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$state = !empty($_POST['state'])?$_POST['state']:'';
$email = checkIsset($_POST['email']);
$cell_phone = !empty($_POST['cell_phone'])?phoneReplaceMain($_POST['cell_phone']) : '';

$lead_sql = "SELECT l.id, CONCAT(l.fname,' ',l.lname) as name ,l.lead_id as rep_id,l.sponsor_id,l.customer_id,c.status as customer_status ,s.rep_id as sponsor_rep_id
            FROM leads l
            LEFT JOIN customer c ON(c.id = l.customer_id)
            LEFT JOIN customer s ON(s.id=l.sponsor_id)
            WHERE md5(l.id)=:id";
$lead_row = $pdo->selectOne($lead_sql,array(":id"=>$lead_id));
$customer_id = 0;
if(!empty($lead_row['customer_id'])) {
    $customer_id = $lead_row['customer_id'];
}
if ($fname == "") {
    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'Firstname is required'));
}
if ($lname == "") {
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Lastname is required'));
}

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

$validate->digit(array('required' => true, 'field' => 'cell_phone', 'value' => $cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

$new_update_details =array(
    'lead_type' => checkIsset($lead_type),
    'company_name' => checkIsset($company_name),
    'fname' => checkIsset($fname),
    'lname' => checkIsset($lname),
    'state' => checkIsset($state),
    'email' => checkIsset($email),
    'cell_phone' => checkIsset($cell_phone),
);


$sponsorType = getname('customer',$lead_row['sponsor_id'],'type');
$tax_details = [];
if(strtolower($sponsorType) == 'group'){
    //add tax field value only if sponsor type is group 
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
}

if($validate->isValid()){    
    $upd_params = array(
        'lead_type' => $lead_type,
        'company_name' => $company_name,
        'fname' => $fname,
        'lname' => $lname,
        'state' => $state,
        'email' => $email,
        'cell_phone' => $cell_phone,
        'updated_at' => 'msqlfunc_NOW()'
    );
    if(strtolower($sponsorType) == 'group'){
        $upd_params = array_merge($upd_params,$tax_details);
    }
    $upd_where = array(
        'clause' => 'md5(id)=:id',
        'params' => array(
            ':id' => $lead_id,
        ),
    );
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
}

if(!empty($lead_update_activity)){
    $flg = "true";
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
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
                        $flg = "false";

                        if($key2 == 'income' && $lead_row['sponsor_rep_id'] == 'G56118'){
                            $description['key_value']['desc_arr'][$key2] = 'Salary updated';
                        }
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
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $lead_row['customer_id'], 'customer', 'Admin Update Member Detail',($_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']),"",$desc);    
    }
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $lead_row['id'], 'Lead', 'Lead Profile Updated',($_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']),"",$desc);
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