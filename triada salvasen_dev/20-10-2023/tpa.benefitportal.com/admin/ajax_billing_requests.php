<?php
include_once 'layout/start.inc.php';

$validate = new Validation();
// pre_print($_REQUEST);
$file = isset($_POST['file']) ? $_POST['file'] : '';
$file_type = "full_file";
$generate_via = isset($_POST['generate_via']) ? $_POST['generate_via'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$ftp = isset($_POST['ftp_name']) ? $_POST['ftp_name'] : '';

// full file
// $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
// add_change_file
// $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$join_range = checkIsset($_POST['join_range']);
$fromdate = checkIsset($_POST["fromdate"]);
$todate = checkIsset($_POST["todate"]);
$added_date = !empty($_POST["added_date"]) ? $_POST["added_date"] : '';

if(empty($join_range)){
    $validate->setError('join_range','Please select Date Type');
}

if (strtolower($join_range) == 'range') {
    if($fromdate == "") {
        $validate->setError('fromdate','Please select From Date');
    } else {
        list($mm, $dd, $yyyy) = explode('/', $fromdate);
        if (!checkdate($mm, $dd, $yyyy)) {
            $validate->setError('fromdate','Valid Date is required');
        }
    }
    if($todate == "") {
        $validate->setError('todate','Please select To Date');
    } else {
        list($mm, $dd, $yyyy) = explode('/', $todate);
        if (!checkdate($mm, $dd, $yyyy)) {
            $validate->setError('todate','Valid Date is required');
        }
    }
} else {
    if($added_date == "") {
        $validate->setError('added_date','Please select Date');
    } else {
        list($mm, $dd, $yyyy) = explode('/', $added_date);
        if (!checkdate($mm, $dd, $yyyy)) {
            $validate->setError('added_date','Valid Date is required');
        }
    }
}

$validate->string(array('required' => true, 'field' => 'generate_via', 'value' => $generate_via), array('required' => 'Please select any option'));

// $validate->string(array('required' => true, 'field' => 'start_date', 'value' => $start_date), array('required' => 'Please select start date'));

// $validate->string(array('required' => true, 'field' => 'end_date', 'value' => $end_date), array('required' => 'Please select end date'));

if(!$validate->getError("generate_via")){
    if($generate_via == 'Email'){
        $validate->string(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required'));    
        if (!$validate->getError('email')) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validate->setError("email", "Valid Email is required");
            }   
        } 
        $validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
        if (!$validate->getError('password')) {
                if (strlen($password) < 6 || strlen($password) > 20) {
                    $validate->setError('password', 'Password must be 6-20 characters');
                } else if (!ctype_alnum($password)) {
                    $validate->setError('password', 'Special character not allowed');
                }
        }
    }else if($generate_via == 'FTP'){
        $validate->string(array('required' => true, 'field' => 'ftp', 'value' => $ftp), array('required' => 'FTP is required'));
    }
}

if ($validate->isValid()) {
    $extra_params = array();

    $extra_params['join_range'] = strtolower($join_range);
    if($join_range != ""){
        if(strtolower($join_range) == "range" && $fromdate!='' && $todate!=''){
            $extra_params['fromdate'] = $fromdate;
            $extra_params['todate'] = $todate;
        }else if(in_array(strtolower($join_range),array('exactly','before','after')) && $added_date!=''){
            $extra_params['added_date'] = $added_date;
        }
    }

    $extra_params = json_encode($extra_params);
    $file_name = getname("billing_files",$file,"file_name","id");

    $req_where = array(
        "clause"=>"id=:id",
        "params"=>array(
          ":id"=>$file,
            )
        );
    $req_data = array(
        'cancel_processing' => 'N',
    );
    $pdo->update("billing_files",$req_data,$req_where);  

     $ins_params = array(
        "file_id" => $file,
        "file_name" =>  $file_name,
        "file_type" => $file_type,
        "user_id" => $_SESSION['admin']['id'],
        "user_type" => "Admin",
        "extra_params" => $extra_params,
        "generate_via" => $generate_via,
        "is_manual" => 'Y',
        "file_process_date" => date('Y-m-d H:i:s'),
        "status" => "Pending",
        "created_at" => "msqlfunc_NOW()"
    );


    if($generate_via == "Email"){
        $ins_params['email'] = $email;
        $ins_params['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
    }else if($generate_via == "FTP"){
        $ins_params['ftp'] = $ftp;
    }
    $insert_id = $pdo->insert("billing_requests",$ins_params);

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' manually created billing file ',
      'ac_red_2'=>array(
        //'href'=>  '',
        'title'=>$file_name,
      ),
    ); 
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insert_id, 'billing_requests','Admin manually created billing file', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    add_billing_request('billing_files',$insert_id);

    $response['file_type'] = $file_type;
    $response['status'] = 'success';
} else {
    $errors = $validate->getErrors();
    $response['status'] = 'fail';
    $response['errors'] = $errors;
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>