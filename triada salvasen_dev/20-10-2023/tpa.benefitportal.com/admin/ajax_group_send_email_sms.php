<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__FILE__) . './../includes/function.class.php';
$function_list = new functionsList();
$validate = new Validation();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$response = array();

$customer_id = isset($_POST['customer_id'])?$_POST['customer_id']:"";
$sent_via = isset($_POST['sent_via'])?$_POST['sent_via']:"";
$email_content = isset($_POST['email_content'])?$_POST['email_content']:"";
$sms_content = isset($_POST['sms_content'])?$_POST['sms_content']:"";
$email_subject = isset($_POST['email_subject'])?$_POST['email_subject']:"";
$to_phone = isset($_POST['to_phone'])? phoneReplaceMain($_POST['to_phone']) :"";
$to_email = checkIsset($_POST['to_email']);
$email_from = checkIsset($_POST['email_from']);

$is_direct = isset($_POST['is_direct'])? $_POST['is_direct'] :"";
$trigger_id = isset($_POST['trigger_id'])? $_POST['trigger_id'] :"";
$cust_row = $pdo->selectOne("SELECT id,fname,lname,email,cell_phone,rep_id,sponsor_id,business_name FROM customer c WHERE md5(c.id)=:id", array(":id" => $customer_id));
if (empty($cust_row)) {
    setNotifyError('User Not Found.');
    $response['status'] = 'fail';
    echo json_encode($response);
    exit;
}

$sponsor_row = $pdo->selectOne("SELECT id,fname,lname,email,cell_phone,rep_id,sponsor_id,business_name FROM customer c WHERE c.id=:id", array(":id" => $cust_row['sponsor_id']));

if(!$is_direct){
    $validate->string(array('required' => true, 'field' => 'sent_via', 'value' => $sent_via), array('required' => 'Resend Method is required'));

    if ($sent_via == 'SMS' || $sent_via == 'Both') {
        $validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'SMS Content is required'));
        if (!$validate->getError("sms_content")) {
            if (strlen($sms_content) > 160) {
                $validate->setError('sms_content', 'SMS Content must be less then 160 character');
            }
            // $is_link = stripos($sms_content, "[[lname]]");
            // if ($is_link <= 0) {
            //     $validate->setError('sms_content', 'SMS content must have [[fname]] and [[lname]] tag');
            // }
    
            // $is_link = stripos($sms_content, "[[fname]]");
            // if ($is_link <= 0) {
            //     $validate->setError('sms_content', 'SMS content must have [[fname]] and [[lname]] tag');
            // }
            
        }
        // if(!empty($to_phone)){
            $validate->digit(array('required' => true, 'field' => 'to_phone', 'value' => $to_phone,'min'=>10,'max'=>10), array('required' => 'Phone Number is required', 'invalid' => "Enter valid Phone Number"));
        // }
    }
    if ($sent_via == 'Email' || $sent_via == 'Both') {
        $validate->email(array('required' => true, 'field' => 'email_from', 'value' => $email_from), array('required' => 'From Email is required','invalid'=>'Please enter valid email'));
        $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email Content is required'));
        $validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Email Subject is required'));
        $validate->email(array('required' => true, 'field' => 'to_email', 'value' => $to_email), array('required' => 'Email is required', 'invalid' => 'Please enter valid email'));
    
        // if (!$validate->getError("email_content")) {
        //     $is_link = stripos($email_content, "[[lname]]");
        //     if ($is_link <= 0) {
        //         $validate->setError('email_content', 'Email content must have [[fname]] and [[lname]] tag');
        //     }
    
        //     $is_link = stripos($email_content, "[[fname]]");
        //     if ($is_link <= 0) {
        //         $validate->setError('email_content', 'Email content must have [[fname]] and [[fname]] tag');
        //     }
        // }
    }
}

if ($validate->isValid()) {
    $description = array();
    $email_status = $sms_status = $email_content_org = $sms_content_org = '';
    if ($sent_via == 'SMS' || $sent_via == 'Both') {
        $sms_data = array();
        $sms_data['GroupName'] = $cust_row['business_name'];
        $sms_data['fname'] = $cust_row['fname'];
        $sms_data['lname'] = $cust_row['lname'];
        $sms_data['email'] = $cust_row['email'];
        $tophone = "+1". !empty($to_phone) ? $to_phone : $cust_row['cell_phone'];

        if ($SITE_ENV=='Local') {
            $tophone = '+919429548647';
        }
        if(empty($sms_content)){
            $sms_content = getname('triggers',$trigger_id,'sms_content');
        }
        $sms_content_org = $sms_content;
        foreach ($sms_data as $placeholder => $value) {
            $sms_content_org = str_replace("[[" . $placeholder . "]]", $value, $sms_content_org);
        }

        $sms_status = trigger_sms($trigger_id, $tophone, $sms_data, true, $sms_content);
        // send_sms_to_phone($tophone,$sms_content,$sms_data);
    }

    if ($sent_via == 'Email' || $sent_via == 'Both') {

        $mail_data = array();
        $mail_data['GroupName'] = $cust_row['business_name'];
        $mail_data['fname'] = $cust_row['fname'];
        $mail_data['lname'] = $cust_row['lname'];
        $mail_data['email'] = $cust_row['email'];
        $mail_data['Email'] = $cust_row['email'];
        $mail_data['GroupID'] = $cust_row['rep_id'];
        $mail_data['ParentAgent'] = $sponsor_row['rep_id'].' - '.$sponsor_row['fname'].' '.$sponsor_row['lname'];
        $mail_data['Group'] = $sponsor_row['rep_id'].' - '.$sponsor_row['fname'].' '.$sponsor_row['lname'];
        $mail_data['phone'] = $cust_row['cell_phone'];
        $mail_data['Phone'] = $cust_row['cell_phone'];

        $is_link = (stripos($email_content, "[[product]]") || stripos($email_content, "[[ActiveProducts]]") ) ;
        if ($is_link > 0) {
            $products = $pdo->select("SELECT CONCAT(p.product_code,' - ',p.name,'<br>') as name from website_subscriptions ws LEFT JOIN prd_main p ON(p.id=ws.product_id and p.type!='Fees' and p.is_deleted='N') where ws.status='Active' AND ws.customer_id=:id",array(":id"=>$cust_row['id']));
            $prd_str = '';
            foreach($products as $product){
                $prd_str .= $product['name'];
            }
            $mail_data['product'] = $prd_str;
        }

        if(empty($email_content)){
            $email_content = getname('triggers',$trigger_id,'email_content');
        }

        $email_content_org = $email_content;
        foreach ($mail_data as $placeholder => $value) {
            $email_content_org = str_replace("[[" . $placeholder . "]]", $value, $email_content_org);
        }

        $to_email = $toemail = !empty($to_email) ? $to_email : $cust_row['email'];
        if ($SITE_ENV == 'Local') {
            $toemail = 'karan@cyberxllc.com';
        }

        if($email_from){
            $mail_data['EMAILER_SETTING']['from_mailid'] = $email_from;
        }

        $smart_tags = get_user_smart_tags($cust_row['id'],'member');
                
        if($smart_tags){
            $mail_data = array_merge($mail_data,$smart_tags);
        }

        $email_content = preg_replace('/[[:^print:]]/', '', $email_content);
        $email_status = trigger_mail($trigger_id, $mail_data, $to_email, true, 3, $email_content, $email_subject);
        // trigger_mail_to_mail($mail_data, $toemail,3, $email_subject,htmlspecialchars_decode($email_content));
    }

    $cm_message = $msg = '';
    if($sent_via == 'Email'){
        if($email_status == 'fail'){
            $msg = 'Something went wrong Email not Sent.';
            $response['status'] = 'fail';
        }else{
            $msg = 'You have successfully Sent Email';
            $response['status'] = 'success';
        }
        $cm_message = ' Sent Email To Group ';
    }else if( $sent_via == 'SMS'){
        if($sms_status == 'fail'){
            $msg = 'Something went wrong SMS not Sent.';
            $response['status'] = 'fail';
        }else{

            $msg = 'You have successfully Sent SMS';
            $response['status'] = 'success';
        }
        $cm_message = ' Sent SMS To Group ';
    }else{

        if($sms_status == 'fail' && $email_status == 'fail'){
            $msg = 'Something went wrong SMS OR Email not sent';
            $response['status'] = 'fail';
        }else{
            $msg = 'You have successfully Sent Email or SMS';
            $response['status'] = 'success';
        }
        $cm_message = ' Sent SMS or Email To Group ';
    }

    $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>$cm_message.' '.$cust_row['fname'].' '.$cust_row['lname'].'(',
        'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/groups_details.php?id='.$cust_row['id'],
          'title'=> $cust_row['rep_id'],
        ),
        'ac_message_2'=>')',
    );     
      if($sent_via == 'SMS' || $sent_via == 'Both' ){
        $description['ac_descriptions_sms'] = ' To : '.$tophone.'<br>';
        $description["ac_description_link_1"] = array(
            'SMS'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'SMS','data-desc'=>checkIsset($sms_content_org),'data-encode'=>'no')
        );
        if($sms_status == 'fail'){
            $description['ac_descriptions_sms_des'] = 'Something went wrong SMS not Sent.';
        }
      }
      if($sent_via == 'Email' || $sent_via == 'Both' ){
        $description['ac_descriptions_email'] = ' To : '.$to_email.'<br>';
        $description["ac_description_link_2"] = array(
            'Email'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Email','data-desc'=>htmlspecialchars(checkIsset($email_content_org)),'data-encode'=>'no')
        );       
        if($email_status == 'fail'){
            $description['ac_descriptions_email_desc'] = 'Something went wrong Email not Sent.';
        }
      }
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'], 'admin','Admin Sent Communication to Group',$_SESSION['admin']['name'],"",$desc);

    $trigger_row = $pdo->selectOne("SELECT * from triggers where id=:id",array(":id"=>$trigger_id));
    if(!empty($trigger_row)) {
        $desc = array();
        $desc['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' sent ',
            'ac_red_2'=>array(
              'href'=> $ADMIN_HOST.'/groups_details.php?id='.$cust_row['id'],
              'title'=> $cust_row['rep_id'],
            ),
            'ac_message_2'=>($sent_via == "Both"?' email/sms':strtolower($sent_via)).' communication ',
            'ac_red_3'=>array(
                'href'=> 'javascript:void(0)',
                'class'=> 'descriptionPopup',
                'title'=> $trigger_row['display_id'],
                'data-desc'=> htmlspecialchars(checkIsset($email_content_org).' <br/> '.checkIsset($sms_content_org)),
                'data-encode'=>'no'
            ),
            'ac_message_3' => $trigger_row['title'],
        );
        $desc=json_encode($desc);
        activity_feed(3,$cust_row['id'], 'customer', $cust_row['id'], 'customer', 'Admin Sent Communication to Group',$cust_row['fname'],"",$desc);    
    }
    

    $response['msg'] = $msg;
    echo json_encode($response);
    dbConnectionClose();
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
