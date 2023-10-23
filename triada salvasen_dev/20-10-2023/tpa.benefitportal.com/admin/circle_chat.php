<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(66);

include_once dirname(__FILE__) .'/circleChat.class.php';
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$circleChat = new circleChat();
// pre_print($name);

$new_chat_id = checkIsset($_POST['new_chat_id']);
if(!empty($new_chat_id)){
    $status = 'false';
    $db_new_chat_id = $pdo->selectOne("SELECT max(id) as id from admin_circle_chat where is_deleted='N'");
    if($new_chat_id != $db_new_chat_id['id']){
        $status = "true";
    }

    $response['status'] = $status;
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}
$contact_ajax = checkIsset($_POST['contact_ajax']);
if($contact_ajax){

    $id = checkIsset($_POST['id']);
    $id = getname("admin_circle",$id,'id','md5(id)');
    if(!empty($id)){
        $circleChat->updateCirclemessageToRead($id);
    }
    $new_chat_id = $pdo->selectOne("SELECT max(id) as id from admin_circle_chat where is_deleted='N'");
    $contactList = $circleChat->getchatCircleList($_SESSION['admin']['id']);
    $contact_html = '';
    foreach ($contactList as $key => $value) {

        $short_name = getShortName($value['name']);
        $unread_msg = '';
        $unreadList = $circleChat->getUnreadCircleChat($value['id']);
        if(!empty($unreadList['unread_message']) && $unreadList['unread_message'] > 0){
            $unread_msg = '<span class="badge pull-right">'.$unreadList['unread_message'].'</span>';
        }
        $status_class = '';
        if($value['status'] == 'Active'){
            $status_class = 'online';
        }else if($value['status'] == 'Away'){
            $status_class = 'away';
        }
        $ajax_loader = '';//"$('.chat_right .ajex_loader').show();";
        $contact_html.='
        <div class="media show_ajex" data-id="'.md5($value['id']).'" onclick="getCircleChat('."'".md5($value['id'])."'".');'.$ajax_loader.'">
        <div class="media-left">
            <div class="profile-img '.$status_class.'">'.strtoupper($short_name).'</div>
        </div>
        <div class="media-body">
            <p class="mn">'.$value['name'].' '.$unread_msg.'</p>
        </div></div>
        ';
    }
    $response['html'] = $contact_html;
    $response['new_chat_id'] = $new_chat_id['id'];
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}

$admin_circle_status = checkIsset($_POST['admin_circle_status']);

if($admin_circle_status){
    $admin_id = $_POST['id'];
    $status = $_POST['status'];
    $updateArr['status'] = $status;
    include_once dirname(__FILE__) .'/adminCircle.class.php';
    $adminCircle = new adminCircle();
    $status_new = $adminCircle->updateAdminStatus($admin_id,$updateArr);
    if($status_new == true){
        $status_new = 'success';
        $response['message'] = 'Status updated successfully.';
    }else{
        $status_new = 'fail';
        $response['message'] = 'Something went wrong.';
    }
    $response['status'] = $status_new;
    
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}

$chat_ajax = checkIsset($_POST['chat_ajax']);
if($chat_ajax){
    $id = $_POST['id'];
    $id = getname("admin_circle",$id,'id','md5(id)');

    $adminList = $circleChat->getCircleAdminList($id);
    $chatList = $circleChat->getchatCircleChats($id);
    $nameArr = $circleChat->getchatCircleName($id);
    $circleChat->updateCirclemessageToRead($id);

    $short_name = getShortName($nameArr['name']);

    $chat_list_html = '';

    $chat_list_html .='
        <div class="chat_person_info" id="chat_person_info">
            <div class="chat_info_circle">'.$short_name.'</div>
            <div class="chat_person_info_in ">
                <h4 class="mn">'.ucwords($nameArr['name']).'</h4>
            </div>
            <div class="dropdown">
            <a href="javascript:void(0);" data-toggle="dropdown" ><span></span><span></span><span></span></a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
            
            if(!empty($adminList)){
                foreach($adminList as $admin){
                    $classTmp = 'status bg-success';
                    if($admin['status'] == 'Active'){
                        $classTmp = 'status bg-success';
                    }else if($admin['status'] == 'Away'){
                        $classTmp = 'status bg-warning';
                    }else if($admin['status'] == 'Do_Not_Distrub'){
                        $classTmp = 'status bg-danger';
                    }else if($admin['status'] == 'Invisible'){
                        $classTmp = 'status';
                    }
                    $chat_list_html .='<li style="pointer-events: none;"><a href="javascript:void(0)">- '.$admin['display_id'].' &nbsp; '.$admin['fname'].' '.$admin['lname'].' &nbsp;<strong class="'.$classTmp.'"></strong></a></li>';
                }
            }
            
    $chat_list_html .='
            </ul>
            </div>
        </div>';
        $chat_list_html .='<div class="chat_window" id="chat_window_scroll">';
        $last_id=0;
        $oldest_id = 0;
        $tmpDate1 = $record_date1 = date('Y-m-d');
        $Today = 1;
        if(!empty($chatList)){
            foreach($chatList as $chat){
                if($chatList[0]['message_id'] == $chat['message_id']){
                    $chatListTmp = $circleChat->getOldestCircleChat($chatList[0]['id'],$chatList[0]['message_id']);

                    if(count($chatListTmp) > 0){
                        $chat_list_html .='<div class="get_old_chat test_'.count($chatListTmp).'"><input type="button" id="show_old_record" value="Get Old Chats" class="show_old_record" name="show_old_record" ></div>';
                    }
                }

                $new_record_date = $chat['created_at'];
                if($tz->getDate($tmpDate1,'m/d/Y') == $tz->getDate($chat['created_at'],'m/d/Y') ){
                    if($Today == 1){
                        $chat_list_html.='<div class="chat_day">Today</div>';
                        $chat_list_html.='<div class="chat_line"></div>';
                        $Today = 2;
                    }
                    $record_date1 = $new_record_date;
               }else if($tz->getDate($record_date1,'m/d/Y') != $tz->getDate($new_record_date,'m/d/Y')){
                    
                    $date1=date_create(date('Y-m-d'));
                    $date2=date_create(date('Y-m-d',strtotime($tz->getDate($chat['created_at'],'m/d/Y'))));
                    $diff=date_diff($date2,$date1);
                    $diffDay =  $diff->format("%R%a");
                    if($diffDay > 6){
                        $chat_list_html.='<div class="chat_day">'.$tz->getDate($chat['created_at'],'m/d/Y').'</div>';
                        $chat_list_html.='<div class="chat_line"></div>';
                    }else{
                        $chat_list_html.='<div class="chat_day">'.$tz->getDate($chat['created_at'],'l').'</div>';
                       $chat_list_html.='<div class="chat_line"></div>';
                    }
                    $record_date1 = $new_record_date;
                }

                if($chat['message_id'] > $last_id){
                    $last_id = $chat['message_id'];
                }
                if($oldest_id == 0 || $chat['message_id'] < $oldest_id){
                    $oldest_id = $chat['message_id'];
                }
                $nameArr = $pdo->selectOne("SELECT CONCAT(fname,' ',lname) as name from admin where id=:id",array(":id"=>$chat['sender_admin_id']));
                $short_name = getShortName($nameArr['name']);
                $chat_class = '';
                if($chat['sender_admin_id'] == $_SESSION['admin']['id']){
                    $chat_class = "recevier_msg";
                }
                $chat_list_html .='
                        <div class="chat_msg_div '.$chat_class.'"> 
                            <div class="chat-name-circle online">'.$short_name.'</div>              
                            <div class="chat-messge">';
                                if(!empty($chat['file_id'])){
                                    $chat_list_html.='<p class="chat-file-name"><a class="text-white"  href="'.$CIRCLE_DOCUMENT_WEB.$chat['file'].'" target="download"><i class="fa fa-download"></i> &nbsp;' .$chat['file_name'].'</a></p>';
                                }
                $chat_list_html.='<p class="chat-name">'.ucwords($nameArr['name']).', '.$tz->getDate($chat['created_at'],'h:i A').'</p>
                                <p>'.$chat['message'].'</p>
                            </div> 
                        </div>
                        <div class="clearfix"></div>
                ';
            }
        }
        $fileClick="$('#docFileNote').click();";
        $fileChange="$('#docFilelabelNote').text($(this).val()); $('.file_upload_name').show(); $('#removeFileNote').show();";
        $removeClick="$('#docFileNote').val('');$(this).hide();$('#docFilelabelNote').text(''); $('.file_upload_name').hide(); ";
        $submitForm ="$('#chat_form').submit();";
        $onclickTextaria = ''//'onkeyup=" if (navigator.appVersion.indexOf("Mac") != -1) {if(this.keyCode == 76 || this.keyCode ==36){this.preventDefault();$("#save_chat").click();}} else{if(this.keyCode == 13){this.preventDefault();$("#save_chat").click();}}  "
;
    $t_val = $Today == 2 ? 1 :0;
        $chat_list_html .= '
            </div>
            </div>
            <form name="chat_form" id="chat_form" enctype="multipart/form-data">
            <input type="hidden" name="oldest_chat_id" id="oldest_chat_id" value="'.$oldest_id.'">
            <input type="hidden" name="id" value="'.md5($id).'" id="id">
            <input type="hidden" name="save_chat" value="1">
            <input type="hidden" name="today" id="today" value="'.$t_val.'">
            <input type="file" name="docFile" id="docFileNote" value="" class="hidden" onchange="'.$fileChange.'">
            <div class="file_upload_wrap">
            <div class="file_upload_name" style="display:none">
            <label id="docFilelabelNote" class="mn" ></label><a href="javascript:void(0)" class="file_upload_remove" id="removeFileNote" style="display:none" onclick="'.$removeClick.'"><i class="fa fa-times-circle"></i></a>
            </div>
            <p class="error error_docFileNote">
            </div>
            <div class="chat_input_wrap">
                <textarea class="form-control autoExpand summernote"  name="message" id="message" '.$onclickTextaria.' data-min-rows="1" placeholder="Type Something"></textarea>
                <p class="error error_message">
                <div class="right_btn">
                    <a href="javascript:void(0);" class="text-gray" onclick="'.$fileClick.'"><i class="fa fa-paperclip fa-flip-horizontal"></i></a>
                    <button class="text-primary" id="save_chat" data-id="'.md5($id).'"><i class="fa fa-paper-plane"></i></button>
                </div>
            </div>
            </form>
        ';
        $chat_list_html.='
        <script type="text/javascript">
        $(".summernote").summernote({
              placeholder:"Type Something",
              toolbar: [], 
              disableResizeEditor: true,
            callbacks: {
                onKeyup :function(e){
                    if (navigator.appVersion.indexOf("Mac") != -1) {
                          if((e.keyCode == 13) && !e.shiftKey){
                          e.preventDefault();
                          $("#save_chat").click();
                        }
                      } else{
                        if(e.keyCode == 13 && !e.shiftKey){
                          e.preventDefault();
                          $("#save_chat").click();
                        }
                      }
                },
                onImageUpload: function(image) {
                  editor = $(this);
                  uploadImageContent(image[0], editor);
                },
                onMediaDelete : function(target) {
                    deleteImage(target[0].src);
                    target.remove();
                }
            }
        });
        $("#chat_form").ajaxForm({
            beforeSend: function(e) {
                $("#message").attr("rows",1);
                $("#docFileNote").val("");
                $("#docFilelabelNote").text("");
                $("#removeFileNote").hide();
                $(".file_upload_name").hide();
                disableButton($("#save_chat"));
            },
            beforeSubmit:function(arr, $form, options){

                var tempmsg = $(".summernote").summernote("code");
                tempmsg = tempmsg.replace("/</div>/gi", "");
                if(tempmsg == "<div><br></div><div><br></div>" || tempmsg == "<div><br></div>" || $(".summernote").summernote("isEmpty") || tempmsg==""){
                    alert("Please Enter message!");
                    $(".summernote").summernote("reset");
                    return false;
                }else{
                    var killId = setTimeout(function() {
                        for (var i = killId; i > 0; i--) {
                            clearInterval(i);
                        }
                    }, 3000);
                    // console.log(killId);
            }
            },
            url:"circle_chat.php",
            type: "post",
            dataType: "json",
            success: function(res) {
              enableButton($("#save_chat"));
              $(".error").html("").hide();
                $("#ajax_loader").hide();
                if(res.status == "success"){
                    $("#message").val("");
                    $(".summernote").summernote("reset");
                    $("#today").val(1);
                    $("#new_chat_id").val(res.last_id);
                    $("#last_id").val(res.last_id);
                    $(".mCSB_container").append(res.html);
                    $("#chat_window_scroll").mCustomScrollbar("scrollTo","bottom",{scrollInertia:0});
                    $(".summernote").summernote("reset");
                    $("#docFileNote").val("");
                    $("#docFilelabelNote").text("");
                    $("#removeFileNote").hide();
                    var myVar = setInterval(getStateOfChat, 5000);
                }else if(res.status == "fail"){
                }else{
                  $.each(res.errors, function(key, value) {
                    $(".error_" + key).html(value).show();
                  })
                }
            }
          });
          </script>
        ';
        $response['html'] = $chat_list_html;
        $response['last_id'] = $last_id;
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
}

$save_chat = checkIsset($_POST['save_chat']);
if($save_chat){
    $response = array();
    $last_id='';
    $docFile = isset($_FILES['docFile']) ? $_FILES['docFile']  : array() ;
    $chat_list_html ='';
    $today = checkIsset($_POST['today']);
    $message = trim($_POST['message']);
    $validate = new Validation();
    if(!empty($docFile) && $docFile['size'] > 10485760){
        $validate->setError('docFileNote',"Please select file less then 10MB.");
    }else if(!empty($docFile)){
        $type = $docFile['type'];
        $ext = @end((explode("/",$type)));
        $ext = pathinfo($docFile['name'],PATHINFO_EXTENSION);
        if( !in_array($ext,array('jpg','jpeg','gif','jpe ','png','webp','bmp','pdf','mp3','wma','pkg','zip','7z','csv','sql','ppt','pptx','xlsx','xls','doc','docx','wpd','txt'))){
            $validate->setError('docFileNote',"Invalid File Formate.");
        }
    }
    if($validate->isValid()){

        $id = $_POST['id'];
        $message = rtrim(ltrim($_POST['message']));
        $id = getname("admin_circle",$id,'id','md5(id)');
    
        $last_id = $circleChat->sendCircleMessage($id,$message,$docFile);
        $chat = $circleChat->getCircleMessageFile($id,$last_id);
        $name =$_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname'];
        $short_name = getShortName($name);
        $time = getname('admin_circle_chat',$last_id,'created_at');
        if($today == 0){
            $chat_list_html.='<div class="chat_day">Today</div>';
            $chat_list_html.='<div class="chat_line"></div>';
        }
        $chat_list_html .='
                <div class="chat_msg_div recevier_msg"> 
                    <div class="chat-name-circle online">'.$short_name.'</div>              
                    <div class="chat-messge">';
                        if(!empty($chat['file_id'])){
                            $chat_list_html.='<p class="chat-file-name"><a class="text-white"  href="'.$CIRCLE_DOCUMENT_WEB.$chat['file'].'" target="download"><i class="fa fa-download"></i> &nbsp;' .$chat['file_name'].'</a></p>';
                        }
        $chat_list_html.='<p class="chat-name">'.ucwords($name).', '.$tz->getDate($time,'h:i A').'</p>
                        <p>'.htmlspecialchars_decode($message).'</p>
                    </div> 
                </div>
                <div class="clearfix"></div>
        ';
        $response['today'] = 'true';
        $response['status'] = 'success';
    }else{
        $response['status'] = 'error';
        $response['errors'] = $validate->getErrors();
    }
    $response['html'] = $chat_list_html ;
    $response['last_id'] = $last_id;
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}

$get_new_chat = checkIsset($_POST['get_new_chat']);
if($get_new_chat){
    $id = checkIsset($_POST['id']);
    $last_id = $_POST['last_id'];
    $id = getname("admin_circle",$id,'id','md5(id)');
    $chatList = array();
    $chatList = $circleChat->getNewchatCircleChats($id,$last_id);    
    $chat_list_html='';
    if(!empty($chatList)){

        foreach($chatList as $chat){
            if($chat['message_id'] > $last_id){
                $last_id = $chat['message_id'];
            }
            
            $nameArr = $pdo->selectOne("SELECT CONCAT(fname,' ',lname) as name from admin where id=:id",array(":id"=>$chat['sender_admin_id']));
            $short_name = getShortName($nameArr['name']);
            $chat_class = '';
            if($chat['sender_admin_id'] == $_SESSION['admin']['id']){
                $chat_class = "recevier_msg";
            }
            $chat_list_html .='
                    <div class="chat_msg_div '.$chat_class.'"> 
                        <div class="chat-name-circle online">'.$short_name.'</div>              
                        <div class="chat-messge">
                            <p class="chat-name">'.ucwords($nameArr['name']).', '.$tz->getDate($chat['created_at'],'h:i A').'</p>';
                            if(!empty($chat['file_id'])){
                                $chat_list_html.='<p class="chat-file-name"><a class="text-white"  href="'.$CIRCLE_DOCUMENT_WEB.$chat['file'].'" target="download"><i class="fa fa-download"></i> &nbsp;' .$chat['file_name'].'</a></p>';
                            }
            $chat_list_html.='<p>'.$chat['message'].'</p>
                        </div> 
                    </div>
                    <div class="clearfix"></div>
            ';
        }
    }
    $response = array();
    $response['html'] = $chat_list_html;
    $response['last_id'] = $last_id;
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}

$get_oldest_chat =  checkIsset($_POST['get_oldest_chat']);
if($get_oldest_chat){
    $oldest_chat_id = $_POST['oldest_chat_id'];
    $id = checkIsset($_POST['id']);
    $id = getname("admin_circle",$id,'id','md5(id)');
    $last_old_id = $pdo->selectOne("SELECT MIN(id) as id FROM admin_circle_chat WHERE circle_id=:id AND is_deleted='N'",array(":id"=>$id));

    $chatList = $circleChat->getOldestCircleChat($id,$oldest_chat_id);
        $last_id=0;
        $oldest_id = 0;
        $chat_list_html = '';
        $record_date = date('Y-m-d');
        if(!empty($chatList)){
            foreach($chatList as $chat){
                if($chatList[0]['message_id'] == $chat['message_id']){
                    $chatListTmp = $circleChat->getOldestCircleChat($chatList[0]['id'],$chatList[0]['message_id']);

                    if(count($chatListTmp) > 0){
                        $chat_list_html .='<div class="get_old_chat test_'.count($chatListTmp).'"><input type="button" id="show_old_record" value="Get Old Chats" class="show_old_record" name="show_old_record" ></div>';
                    }
                }
                if($oldest_id == 0 || $chat['message_id'] < $oldest_id){
                    $oldest_id = $chat['message_id'];
                }

                $new_record_date = $chat['created_at'];
                if($tz->getDate($record_date,'m/d/Y') != $tz->getDate($chat['created_at'],'m/d/Y')){
                   
                   $date1=date_create(date('Y-m-d'));
                   $date2=date_create(date('Y-m-d',strtotime($tz->getDate($chat['created_at'],'m/d/Y'))));
                   $diff=date_diff($date2,$date1);
                   $diffDay =  $diff->format("%R%a");
                   $response['day'][] = $diffDay;
                   if($diffDay > 6){
                       $chat_list_html.='<div class="chat_day">'.$tz->getDate($chat['created_at'],'m/d/Y').'</div>';
                       $chat_list_html.='<div class="chat_line"></div>';
                   }else{
                       $chat_list_html.='<div class="chat_day">'.$tz->getDate($chat['created_at'],'l').'</div>';
                       $chat_list_html.='<div class="chat_line"></div>';
                   }
                   $record_date = $new_record_date;
               }
               
                $nameArr = $pdo->selectOne("SELECT CONCAT(fname,' ',lname) as name from admin where id=:id",array(":id"=>$chat['sender_admin_id']));
                $short_name = getShortName($nameArr['name']);
                $chat_class = '';
                if($chat['sender_admin_id'] == $_SESSION['admin']['id']){
                    $chat_class = "recevier_msg";
                }
                $chat_list_html .='
                        <div class="chat_msg_div '.$chat_class.'"> 
                            <div class="chat-name-circle online">'.$short_name.'</div>              
                            <div class="chat-messge">';
                                if(!empty($chat['file_id'])){
                                    $chat_list_html.='<p class="chat-file-name"><a class="text-white"  href="'.$CIRCLE_DOCUMENT_WEB.$chat['file'].'" target="download"><i class="fa fa-download"></i> &nbsp;' .$chat['file_name'].'</a></p>';
                                }
                $chat_list_html.='<p class="chat-name">'.ucwords($nameArr['name']).', '.$tz->getDate($chat['created_at'],'h:i A').'</p>
                                <p>'.$chat['message'].'</p>
                            </div> 
                        </div>
                        <div class="clearfix"></div>
                ';
            }
        }

    $response = array();
    $response['html'] = $chat_list_html;
    $response['oldest_id'] = $oldest_id;
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}
$is_get_chat_message = checkIsset($_POST['is_get_chat_message']);
if($is_get_chat_message){
    $response = array();
    $total_message = '';
    $message = $circleChat->getTotalNewMessage();
    if(!empty($message['totalMes']) && $message['totalMes'] > 0){
        $total_message = $message['totalMes'];
    }
    $response['total_message'] = $total_message;
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}
function getShortName($name){
    $short_name = explode(' ',$name);
    $scChar = $fsChar = '';
    $fsChar = substr($short_name[0],0,1);
    if(!empty($short_name[1])){
        $scChar = substr($short_name[1],0,1);
    }else{
        $fsChar = substr($short_name[0],0,2);
    }
    return strtoupper($fsChar.$scChar); 
}

$status = $circleChat->getAssignedAdminStatus($_SESSION['admin']['id']);

$status_class="";
if($status == "Away"){
    $status_class = 'Abandoned';
} else if($status == "Do_Not_Distrub"){
    $status_class = 'Unqualified';
}else if($status == "Invisible"){
    $status_class = 'Invisible';
}

$exStylesheets = array('thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css','thirdparty/summernote-master/dist/summernote.css'.$cache);
$exJs = array('thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js',"thirdparty/ajax_form/jquery.form.js",'thirdparty/summernote-master/dist/popper.js'.$cache, 'thirdparty/summernote-master/dist/summernote.js'.$cache);

$template = 'circle_chat.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
