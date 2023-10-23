<?php
include_once 'includes/connect.php';
include_once 'includes/function.class.php';
$function_list = new functionsList();

$validate = new Validation();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$response = array();

$ws_id = isset($_POST['ws_id'])?$_POST['ws_id']:""; //MD5
$customer_id = isset($_POST['customer_id'])?$_POST['customer_id']:""; //MD5
$id_card_id = isset($_POST['id_card_id'])?$_POST['id_card_id']:""; //MD5
$sent_via = isset($_POST['sent_via'])?$_POST['sent_via']:"";
$email_to = isset($_POST['email_to'])?$_POST['email_to']:"";
$sms_to = isset($_POST['sms_to'])?phoneReplaceMain($_POST['sms_to']):"";

$send_by_user_id = isset($_POST['user_id'])?$_POST['user_id']:""; //MD5
$send_by_user_type = isset($_POST['user_type'])?$_POST['user_type']:"";

$validate->string(array('required' => true, 'field' => 'sent_via', 'value' => $sent_via), array('required' => 'Please select any option'));

if ($sent_via == 'text') {
    $validate->digit(array('required' => true, 'field' => 'sms_to', 'value' => $sms_to, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
}

if ($sent_via == 'email') {
    $validate->email(array('required' => true, 'field' => 'email_to', 'value' => $email_to), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
    if(!$validate->getError('email_to')){
        if (!filter_var($email_to, FILTER_VALIDATE_EMAIL)) {
            $validate->setError("email_to", "Valid Email is required");
        }
    }
}

if ($validate->isValid()) {
    $ws_sql = "SELECT ws.*,p.product_code,p.name as product_name,CONCAT(c.fname,' ',c.lname) as member_name,c.rep_id,c.sponsor_id
                    FROM website_subscriptions ws
                    JOIN prd_main p on(p.id=ws.product_id)
                    JOIN customer c on(c.id=ws.customer_id)
                    WHERE MD5(ws.id)=:ws_id";
    $ws_where = array(":ws_id" => $ws_id);
    $ws_row = $pdo->selectOne($ws_sql, $ws_where);
    if ($sent_via == 'physical_card') {

        $whr_update = array(
            "clause" => ":website_id=website_id",
            "params" => array(
                ":website_id" => $ws_row['id']
            )
        );
        $pdo->update('customer_enrollment',array('is_fulfillment' => 'R'),$whr_update);

        if($send_by_user_type != '' && $send_by_user_id != '') {
            if($send_by_user_type == "Customer") {
                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1'=>array(
                        'href'=> 'members_details.php?id='.$send_by_user_id,
                        'title'=> $ws_row['rep_id'],
                    ),
                    'ac_message_1' => ' self requested physical ID card for '.$ws_row['product_name'].'(',
                    'ac_red_2' => array(
                        'href' => 'javascript:void(0);',
                        'title' => $ws_row['product_code'],
                    ),
                    'ac_message_2' => ')',
                ); 
                $desc = json_encode($desc);
                activity_feed(3,$ws_row['customer_id'],'customer',$ws_row['customer_id'],'customer','Requested Physical ID Card','','',$desc);
            } else {
                if (strtolower($send_by_user_type) == 'admin') {
                    $ac_red_1 = array(
                        'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                        'title'=> $_SESSION['admin']['display_id'],
                    );
                } else if (strtolower($send_by_user_type) == 'agent') {
                    $ac_red_1 = array(
                        'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                        'title'=> $_SESSION['agents']['rep_id'],
                    );

                } else if (strtolower($send_by_user_type) == 'group') {
                    $ac_red_1 = array(
                        'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                        'title'=> $_SESSION['groups']['rep_id'],
                    );
                }
                
                if(!empty($ac_red_1)) {
                    $desc = array();
                    $desc['ac_message'] = array(
                        'ac_red_1'=> $ac_red_1,
                        'ac_message_1' => ' send physical ID card to ',
                        'ac_red_2' => array(
                            'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                            'title'=> $ws_row['rep_id'],
                        ),
                        'ac_message_2' => 'for '.$ws_row['product_name'].'(',
                        'ac_red_3' => array(
                            'href' => 'javascript:void(0);',
                            'title' => $ws_row['product_code'],
                        ),
                        'ac_message_3' => ')',
                    );
                    $desc = json_encode($desc);
                    activity_feed(3,$ws_row['customer_id'],'customer',$ws_row['customer_id'],'customer','Send Physical ID Card','','',$desc);
                }
            }
        }
    } else {
        $trigger_id = 45;
        $url_link = $HOST . '/download_id_card.php?ws_id='.$ws_id;
        if(!empty($id_card_id)) {
            $url_link .= '&id_card_id='.$id_card_id;
        }
        $url_params = array(
            'dest_url' => $url_link,
            'customer_id' => $ws_row['customer_id'],
        );
        $id_card_link = get_short_url($url_params);

        $description = array();
        if ($sent_via == 'text') {
            $sms_data = array();
            $sms_data['link'] = $id_card_link;
            $sms_data['name'] = $ws_row['member_name'];
            $sms_to = "+1". $sms_to;

            $smart_tags = get_user_smart_tags($ws_row['customer_id'],'member',$ws_row['product_id'],$ws_row['id']);
                    
            if($smart_tags){
                $sms_data = array_merge($sms_data,$smart_tags);
            }

            if ($SITE_ENV=='Local') {
                $sms_to = '+919429548647';
            }
            trigger_sms($trigger_id,$sms_to,$sms_data);

            $description['phone'] = "Phone : ".format_telephone($sms_to);
        }

        if ($sent_via == 'email') {
            $agent_detail = $function_list->get_sponsor_detail_for_mail($ws_row['customer_id'],$ws_row['sponsor_id']);

            $mail_data = array();
            $mail_data['link'] = $id_card_link;
            $mail_data['name'] = $ws_row['member_name'];

            if(!empty($ws_row['sponsor_id'])){
                $parent_agent_detail = $function_list->get_sponsor_detail_for_mail($ws_row['customer_id'], $ws_row['sponsor_id']);
                $mail_data['ParentAgent'] = $parent_agent_detail['agent_name'];
            }

            if (!empty($agent_detail)) {
                $mail_data['agent_name'] = $agent_detail['agent_name'];
                $mail_data['agent_email'] = $agent_detail['agent_email'];
                $mail_data['agent_phone'] = format_telephone($agent_detail['agent_phone']);
                $mail_data['agent_id'] = $agent_detail['agent_id'];
                $mail_data['is_public_info'] = $agent_detail['is_public_info'];
            } else {
                $mail_data['is_public_info'] = 'display:none';
            }

            $mail_data['USER_IDENTITY'] = array('rep_id'=>$ws_row['rep_id'],'cust_type' => 'Member','location' => $REQ_URL);

            if ($SITE_ENV == 'Local') {
                $email_to = 'karan@cyberxllc.com';
            }

            $smart_tags = get_user_smart_tags($ws_row['customer_id'],'member',$ws_row['product_id'],$ws_row['id']);
                    
            if($smart_tags){
                $mail_data = array_merge($mail_data,$smart_tags);
            }

            trigger_mail($trigger_id,$mail_data,$email_to,false,3);
            $description['email'] = "Email : ".$email_to;
        }
    }

    if($send_by_user_type != '') {
        if($send_by_user_type == "Customer") {
            //[[MemberID]] self requested ID card via email/text message for [[ProductID]]
            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> 'members_details.php?id='.$send_by_user_id,
                    'title'=> $ws_row['rep_id'],
                ),
                'ac_message_1' => ' self requested ID card via email/text message for '.$ws_row['product_name'].'(',
                'ac_red_2' => array(
                    'href' => 'product_builder.php?product=' . md5($ws_row['product_id']),
                    'title' => $ws_row['product_code'],
                ),
                'ac_message_2' => ')',
            );
            $desc = json_encode($desc);
            activity_feed(3,$ws_row['customer_id'],'Customer',$ws_row['customer_id'],'Customer','Requested ID Card','','',$desc);        
        }
    }

    $response['status'] = 'success';
    $response['msg'] = 'Id Card Sent Successfully';
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
