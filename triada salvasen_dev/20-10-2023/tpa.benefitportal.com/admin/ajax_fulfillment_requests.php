<?php
include_once 'layout/start.inc.php';

$validate = new Validation();
// pre_print($_REQUEST);
$file = isset($_POST['file']) ? $_POST['file'] : '';
$file_type = "full_file";
$generate_via = isset($_POST['generate_via']) ? $_POST['generate_via'] : '';
$email = checkIsset($_POST['email']);
$password = isset($_POST['password']) ? $_POST['password'] : '';
$ftp = isset($_POST['ftp']) ? $_POST['ftp'] : '';


$validate->string(array('required' => true, 'field' => 'file_type', 'value' => $file_type), array('required' => 'Generate Type is required'));


$validate->string(array('required' => true, 'field' => 'generate_via', 'value' => $generate_via), array('required' => 'Please select any option'));
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

    $extra_params = json_encode($extra_params);
    $file_name = getname("fulfillment_files",$file,"file_name","id");
    
    $req_where = array(
        "clause"=>"id=:id",
        "params"=>array(
          ":id"=>$file,
            )
        );
    $req_data = array(
        'cancel_processing' => 'N',
    );
    $pdo->update("fulfillment_files",$req_data,$req_where);

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
        "created_at" => "msqlfunc_NOW()",
    );


    if($generate_via == "Email"){
        $ins_params['email'] = $email;
        $ins_params['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
    }else if($generate_via == "FTP"){
        $ins_params['ftp'] = $ftp;
    }
    $insert_id = $pdo->insert("fulfillment_requests",$ins_params);

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' manually created fulfillment file ',
      'ac_red_2'=>array(
        //'href'=>  '',
        'title'=>$file_name,
      ),
    ); 
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insert_id, 'fulfillment_requests','Admin manually created fulfillment file', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    add_fulfillment_request('fulfillment_files',$insert_id);

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