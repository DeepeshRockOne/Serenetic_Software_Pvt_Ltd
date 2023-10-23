<?php
include_once __DIR__ . '/includes/connect.php'; 
$validate = new Validation();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$response = array();

$id = isset($_POST['id'])?$_POST['id']:"";
$full_name = isset($_POST['full_name'])?$_POST['full_name']:"";
$email = isset($_POST['email'])?$_POST['email']:"";
$phone = !empty($_POST['phone'])?phoneReplaceMain($_POST['phone']) : '';
$comment = isset($_POST['comment'])?$_POST['comment']:"";

$pb_sql = "SELECT pg.* FROM page_builder pg WHERE pg.is_deleted='N' AND pg.status='Active' AND md5(pg.id)=:id";
$pb_row = $pdo->selectOne($pb_sql,array(":id"=>$id));

if(empty($pb_row)) {
    setNotifyError('Please try again.');
    $response['status'] = "page_not_found";
    echo json_encode($response);
    exit();
}

$validate->string(array('required' => true, 'field' => 'full_name', 'value' => $full_name), array('required' => 'Full Name is required'));
$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

if(!$validate->getError('email')){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validate->setError("email", "Valid Email is required");
    }
}
$validate->digit(array('required' => true, 'field' => 'phone', 'value' => $phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));


if ($validate->isValid()) {
    $trigger_row = $pdo->selectOne("SELECT * FROM triggers WHERE display_id='T254'");
    $url_link = $ENROLLMENT_WEBSITE_HOST . '/' . $pb_row['user_name'];

    $user_type = getname('customer',$pb_row['agent_id'],'type');
    $mail_data = array();
    $mail_data['link'] = $url_link;
    $mail_data['name'] = $full_name;
    $mail_data['email'] = $email;
    $mail_data['phone'] = format_telephone($phone);
    $mail_data['comment'] = $comment;

    $smart_tags = get_user_smart_tags($pb_row['agent_id'],'agent');
                
    if($smart_tags){
        $mail_data = array_merge($mail_data,$smart_tags);
    }

    if (!empty($trigger_row)) {
        $to_email = explode(';',$pb_row['contact_us_emails']);
        trigger_mail($trigger_row['id'],$mail_data,$to_email);
    }

    $lead_exist = $pdo->selectOne("SELECT id,status FROM leads WHERE lead_type='Member' AND email=:email AND sponsor_id=:sponsor_id AND is_deleted='N'", array(":email" => $email,":sponsor_id" => $pb_row['agent_id']));
    if(empty($lead_exist)) {
        $full_name = split_name($full_name);
        $lead_data = array(
            "lead_id" => get_lead_id(),
            'sponsor_id' => $pb_row['agent_id'],
            'fname' => isset($full_name[0])?$full_name[0]:'',
            'lname' => isset($full_name[1])?$full_name[1]:'',
            'email' => $email,
            'cell_phone' => $phone,
            'lead_type'=>'Member',
            'status' => "New",
            'generate_type' => "Manual",
            'opt_in_type' => $pb_row['user_name'],
            'ip_address' => $_SERVER['SERVER_ADDR'],
            'updated_at' => 'msqlfunc_NOW()',
        );
        $pdo->insert("leads",$lead_data);
    } 

    //setNotifySuccess('You have successfully requested quote.');
    $response['status'] = 'success';
    if($user_type == 'Agent'){
        $response['msg'] = 'You have successfully requested quote.';
    }elseif($user_type == 'Group'){
        $response['msg'] = 'Contact request successfully sent.';
    }
    
    echo json_encode($response);
    exit;
} else {
    if(count($validate->getErrors()) > 0){
        $response['status'] = "errors";   
        $response['errors'] = $validate->getErrors();   
    }
    echo json_encode($response);
    exit();
}
?>
