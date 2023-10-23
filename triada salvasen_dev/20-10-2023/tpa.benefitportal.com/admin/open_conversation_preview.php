<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$is_ajaxed = isset($_POST['is_ajaxed']) ? $_POST['is_ajaxed'] : '';
$is_description = isset($_POST['is_description']) ? $_POST['is_description'] : '';
$REAL_IP_ADDRESS = get_real_ipaddress();
if($is_ajaxed && $is_description){
    $response['status'] = 'fail';
    $reply_id = isset($_POST['reply_id']) ? $_POST['reply_id'] : '';
    $description = $pdo->selectOne("SELECT id,description from s_ticket_quick_reply where id=:id and is_deleted='N'",array(":id"=>$reply_id));

    if(!empty($description['id'])){
        $response['description'] = htmlspecialchars_decode($description['description']);
        $response['status'] = 'success';
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$is_sendEmail = isset($_POST['is_sendEmail']) ? $_POST['is_sendEmail'] : '';
if($is_ajaxed && $is_sendEmail){
    $view =  isset($_REQUEST['view']) ? $_REQUEST['view'] : '';
    $response['status'] = 'fail';
    $response['msg'] = 'Something went wrong.';
    $docFile = isset($_FILES['docFile']) ? $_FILES['docFile']  : '' ;
    $s_ticket_id = isset($_POST['s_ticket_id']) ? $_POST['s_ticket_id'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $content_temp = isset($_POST['content_temp']) ? $_POST['content_temp'] : '';
    $reply_type = isset($_POST['reply_type']) ? $_POST['reply_type'] : '';

    $field = $reply_type == 'notes' ? 'description_note' : 'description_public';
    $errordocFile = $reply_type == 'notes' ? 'docFileNote' : 'docFile';
    $Message = $reply_type == 'notes' ? 'Internal Note saved successfully!' : 'Replied Successfully!';
    $validate = new Validation();
    
    if($reply_type == 'notes'){
        $validate->string(array('required' => true, 'field' => 'description_note', 'value' => $content_temp), array('required' => 'Content is required'));
    }else{
        $validate->string(array('required' => true, 'field' => 'description_public', 'value' => $content), array('required' => 'Content is required'));
    }
    if(!empty($docFile) && $docFile['size'] > 10485760){
        $validate->setError($errordocFile,"Please select file less then 10MB.");
    }
    if($reply_type == 'notes'){
        $content = $content_temp;
    }
    if($validate->isValid()){
        $ticket = $pdo->selectOne("SELECT id,user_id,user_type,tracking_id,assigned_admin_id from s_ticket where md5(id)=:id",array(":id"=>$s_ticket_id));
        if(!empty($ticket['id'])){
            $mail_data = $userArr = array();

            if($ticket['user_type'] == 'Admin'){
                $userArr = $pdo->selectOne("SELECT fname,lname,display_id as rep_id,id,email,phone from admin where is_deleted='N' AND id=:user_id",array(":user_id"=>$ticket['user_id']));
            }else{
                $userArr = $pdo->selectOne("SELECT fname,lname,rep_id,id,email,cell_phone as phone from customer where is_deleted='N' and type=:type  AND id=:user_id",array(":type"=>$ticket['user_type'],":user_id"=>$ticket['user_id']));
            }

            if(!empty($userArr)){
                $upd_param = array(
                    "updated_at" => 'msqlfunc_NOW()',
                );
                $upd_where = array(
                    'clause' => 'id=:id',
                    'params' => array(":id"=>$ticket['id'])
                );
                $pdo->update('s_ticket',$upd_param,$upd_where);
                $response['updated_at'] = date("m/d/Y");
                $response['s_ticket_id'] = $s_ticket_id;

                $mail_data['fname'] = $userArr['fname'];
                $mail_data['lname'] = $userArr['lname'];
                $mail_data['email'] = $userArr['email'];
                $mail_data['phone'] = $userArr['phone'];                
                foreach ($mail_data as $placeholder => $value) {
                    $content = str_replace("[[" . $placeholder . "]]", $value, $content);
                }
                $ins_param = array(
                    "ticket_id" => $ticket['id'],
                    "user_id" => $_SESSION['admin']['id'],
                    "user_type" => 'Admin',
                    "type" => $reply_type,
                    "message" => htmlspecialchars($content),
                    "ip_address" => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                );
                $insId = $pdo->insert('s_ticket_message',$ins_param);
                $Attatchment_file ='';
                if(!empty($docFile) && !empty($docFile['name'])){
                    $s_ticket_msg_fileinsParam = array(
                        'ticket_id' =>  $ticket['id'],
                        'message_id' => $insId,
                        'file' => date('mdYhisa').$docFile['name'],
                        'file_name' =>$docFile['name'],
                    );
                    $pdo->insert('s_ticket_message_files',$s_ticket_msg_fileinsParam);
                    $name = basename($s_ticket_msg_fileinsParam['file']);
                    $Attatchment_file = $ETICKET_DOCUMENT_WEB.$name;
                    move_uploaded_file($docFile['tmp_name'], $ETICKET_DOCUMENT_DIR.$name);
                }
                if($reply_type == 'reply'){
                    $Attatchment = array();
                    if(!empty($Attatchment_file) && !empty($Attatchment_file)){
                        $Attatchment = array($Attatchment_file);
                    }
                    $subject = 'Support - eticket #'.$ticket['tracking_id'];
                    
                    $email_content = '';
                    $old_deatils = $pdo->select("SELECT sm.created_at,IF(sm.user_type='Admin',CONCAT(a.fname,' ',a.lname),   CONCAT(c.fname,' ',c.lname)) AS 'name' ,sm.message
                    FROM  s_ticket_message sm
                    LEFT JOIN admin a ON(a.id=sm.user_id AND sm.user_type='Admin')
                    LEFT JOIN customer c ON(c.id=sm.user_id AND sm.user_type!='Admin')
                    WHERE sm.ticket_id = :id AND sm.type='reply' ORDER BY sm.created_at DESC",array(":id"=>$ticket['id']));
                    if(!empty($old_deatils)){
                        $email_content .= '<div class="eticket_replay_wrap" style="font-size: 14px; font-family:Verdana, Geneva, sans-serif; ">';
                        $email_content .='<div style=" background-color:#050606; padding: 15px; color: #fff; font-weight: bold; border-top-left-radius:15px; border-top-right-radius:15px;">
                        eTicket Reply :
                        </div><div class="eticket_replay_body" style="padding:0px 15px 15px 15px; background-color:#fff;  border-bottom-left-radius:15px; border-bottom-right-radius:15px;  border:1px solid #e8e8e8;">';
                        foreach($old_deatils as $details){
                            $email_content.='
                            <div style="padding:15px 0px; ">';
                            $email_content.='<p style="font-size:12px; color:#848484; margin-bottom:15px; padding-bottom:15px; margin-top:0px;"><span style="font-size:14px; font-weight:600; color:#5694cc; border-bottom:1px solid #e7e7e7;">'.$details['name'].'</span> - &nbsp;'.$tz->getDate($details['created_at'],'D., M. d, Y @ h:i A').'</p>';
                            $email_content.='<p style="margin:0px;">'.htmlspecialchars_decode($details['message']).'</div>';
                            $email_content.='</p>';
                        }
                        $email_content .= "</div></div>";
                    }

                    //T948
                    $params = array();
                    $params['fname'] = $userArr['fname'];
                    $params['message'] = $email_content;
                    $params['EMAILER_SETTING']['from_mailid'] = $ETICKET_SUPPORT_EMAIL;
                    $subject = "eTicket Response -[".$ticket['tracking_id']."]";
                    $status = trigger_mail(59,$params,$userArr['email'],true,3,'',$subject,"",$Attatchment);
                    //$status = trigger_mail_to_mail($mail_data, $userArr['email'],3, $subject,htmlspecialchars_decode($email_content),1,$Attatchment);
                }
                $label = '';
                if($reply_type == 'reply'){
                    $label = ' created Public Reply on ';
                }else{
                    $label = ' created Internal Note on  ';
                }
                $description['ac_message'] = array(
                    'ac_red_1'=>array(
                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                      'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>$label.'(',
                    'ac_red_2'=>array(
                      'title'=> $ticket['tracking_id'],
                    ),
                    'ac_message_2' =>')<br>'.htmlspecialchars_decode($content),
                  );
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'E-Ticket Public Reply OR Internal Note',$_SESSION['admin']['name'],"",json_encode($description));
            }

            $response['msg'] = $Message;
            $response['status'] = 'success';
            if($view)
                $response['view'] = 'refresh';
        }
    }else{
        $errors = $validate->getErrors();
        $response['status'] = 'error';
        $response['errors'] = $errors;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$s_ticket_id = isset($_REQUEST['s_ticket_id']) ? $_REQUEST['s_ticket_id'] : '';
$is_ajaxed_get_data = checkIsset($_POST['is_ajaxed_get_data']);
$view =  isset($_REQUEST['view']) ? $_REQUEST['view'] : '';
if($is_ajaxed_get_data){
    $sel_sql = "SELECT s.*,if(s.user_type='Admin',concat(a.fname,' ',a.lname),concat(c.fname,' ',c.lname)) as name,if(s.user_type='Admin',concat(a.display_id),concat(c.rep_id)) as rep_id
            FROM s_ticket s 
            LEFT JOIN admin a ON(a.id=s.user_id and s.user_type='Admin')
            LEFT JOIN customer c ON(c.id=s.user_id and s.user_type!='Admin')
            where 1 and s.user_id!=0 and md5(s.id)=:id";
    $rows = $pdo->selectOne($sel_sql,array(":id"=>$s_ticket_id));
    
    $replys = $pdo->select("SELECT sm.*,smf.file,smf.file_name,if(sm.user_type='Admin',concat(a.fname,' ',a.lname,'_',a.display_id),   concat(c.fname,' ',c.lname,'_',c.rep_id)) as name 
        FROM  s_ticket_message sm 
        LEFT JOIN s_ticket_message_files smf ON(smf.message_id = sm.id)
        LEFT JOIN admin a ON(a.id=sm.user_id and sm.user_type='Admin')
        LEFT JOIN customer c ON(c.id=sm.user_id and sm.user_type!='Admin')
        where md5(sm.ticket_id) = :id order by sm.created_at ASC",array(":id"=>$s_ticket_id));
    $total_reply = $pdo->selectOne("SELECT type,count(id) as replys from s_ticket_message where md5(ticket_id) = :id AND type='reply'",array(":id"=>$s_ticket_id));
    $total_notes = $pdo->selectOne("SELECT count(id) as notes from s_ticket_message where md5(ticket_id) = :id AND type='notes'",array(":id"=>$s_ticket_id));

    $quick_reply = $pdo->select("SELECT * FROM s_ticket_quick_reply where is_deleted='N'");    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'Read E-ticket page On (',
        'ac_red_2'=>array(
          'title'=> $rows['tracking_id'],
        ),
        'ac_message_2' =>')<br>',
      );
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'Read E-Ticket Page',$_SESSION['admin']['name'],"",json_encode($description));
    include_once 'tmpl/open_conversation_preview.inc.php';
    exit;
}
if($view){
    $exStylesheets = array("/thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css", "thirdparty/summernote-master/dist/summernote.css");
    $exJs = array("thirdparty/ajax_form/jquery.form.js","/thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js", "thirdparty/ckeditor/ckeditor.js", "thirdparty/summernote-master/dist/popper.js", "thirdparty/summernote-master/dist/summernote.js");

    // $summernote = true;

    $template = "open_conversation_preview.inc.php";
    include_once 'layout/iframe.layout.php';
}else{
    include_once 'tmpl/open_conversation_preview.inc.php';
}

?>