<div class="panel panel-default panel-block go_live_panel">
  <div class="panel-heading table_dark_black">
    <div class="panel-title">
      <h4 class="text-white m-t-0 fs16"><?= $_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname'] ?> </h4>
      <p class="text-white mn fs12">ADMIN</p>
    </div>
  </div>
  <div class="panel-body p-t-10 bg_white">
    <div class="row">
      <div class="col-sm-6">
        <ul class="nav nav-tabs tabs customtab nav-noscroll">
          <li role="presentation" class="active">
            <a href="#My_Chat_tab" class="tab_click" data-tab="My_Chat_tab" aria-controls="My_Chat_tab" role="tab" data-toggle="tab">My Chats (<span id="mychatsCount">0</span>)</a>
          </li>
          <li role="presentation" >
            <a href="#In_Queue_tab" class="tab_click" data-tab="In_Queue_tab" aria-controls="In_Queue_tab" role="tab" data-toggle="tab">In Queue (<span class="queueChatCount">0</span>)</a>
          </li>
          <li role="presentation" >
            <a href="#Chatting_tab" class="tab_click" data-tab="Chatting_tab" aria-controls="Chatting_tab" role="tab" data-toggle="tab">Chatting (<span class="chattingCount">0</span>)</a>
          </li>
        </ul>
      </div>
      <div class="col-sm-6">
        <div class="chat_user_type pull-right">
          <ul class="list-unstyled mn">
            <li class="member_portal">
              Member Portal
            </li>
            <li class="agent_portal">
              Agent Portal
            </li>
            <li class="group_portal">
              Group Portal
            </li>
            <li class="external_web">
              External Website
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body pn  br-t">
    <div class="tab-content mn">
      <div role="tabpanel" class="tab-pane active" id="My_Chat_tab">
        <div class="chat_wrapper ">
          <div class="go_chat_live">
            <div class="row">
              <div class="col-sm-4 pn">
                <div class="contact_list" id="contact_list_scroll">
                  <div id="conversationDiv"></div>
                </div>
              </div>
              <div class="col-sm-8">
                <div class="chat_right" id="right_chat" style="display: none">
                  <div class="chat_person_info" >
                    <div class="chat_info_circle" id="chatHeadingTitleHtml"></div>
                    <div class="chat_person_info_in clearfix">
                      <h4 class="pull-left fs12 mn" id="chatHeadingNameLinkHtml"></h4>
                      <div class="pull-right">
                        <ul class="chat_person_action">
                          <li class="dropdown go_chat_catdrop">
                            <a href="javascript:void(0);" class="dropdown-toggle" type="button" data-toggle="dropdown"> 
                              <span id="chatDepartmentText" class="text-black">Select</span>
                              <span class="m-l-5"><img src="<?=$HOST?>/images/select_arrow.png"></span>
                            </a>
                            <ul class="dropdown-menu">
                              <?php if(!empty($lc_department_res)) { ?>
                                <?php foreach ($lc_department_res as $key => $value) { ?>
                                <li><a href="javascript:void(0);" data-value="<?= $value['name'] ?>" data-id="<?= $value['id'] ?>" class="chatDepartment chatDepartment_<?= $value['id'] ?>" 
                                data-conversation-id="" data-user=""><?= $value['name'] ?></a></li>
                                <?php } ?>
                              <?php } ?>
                            </ul>
                          </li> 
                          <li>
                            
                            <a href="javascript:void(0);" id="switchLogin" class="text-info" type="button" ><i class="fa fa-lock" aria-hidden="true"></i></a>
                              
                          </li>
                          <li>
                            <a href="javascript:void(0);" data-toggle="tooltip" title="Leave Chat" data-container="body" data-placement="top" class="text-action" id="LeaveChat" data-conversation-id="" data-user=""><i class="fa fa-sign-out" aria-hidden="true"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  
                  <div class="chat_window" id="chat_window_scroll">
                     <div id="messageDiv"></div>
                  </div>
                  <div class="chat_typing">
                    <p id="userIsTyping" class="text-light-gray mn" style="display: none"><em>User is typing.....</em></p>
                  </div>
                  <div class="chat_input_wrap theme-form">
                      <div class="chat_textarea">
                        <div class="pr">
                          <textarea class="form-control" id="textMessage" rows="2" placeholder="Say Something…" data-conversation-id="" data-user=""></textarea>
                        </div>
                      </div>
                      <div class="chat_action">
                        <div class="dropup">
                           <a href="javascript:void(0);" class="btn btn-default btn-block m-b-5 dropdown-toggle" data-toggle="dropdown">Quick Reply</a>
                          <ul class="dropdown-menu">
                            <?php if(!empty($saved_replies_res)) { ?>
                                <?php foreach ($saved_replies_res as $key => $value) { ?>
                                    <li><a href="javascript:void(0)" data-value="<?= $value['reply-text'] ?>" class="quick-reply"><?= $value['reply-name'] ?></a></li>
                                <?php } ?>
                            <?php } ?>
                          </ul>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-default-o mn" id="attachFile"><i class="fa fa-paperclip fa-rotate-90 fs18" aria-hidden="true"></i></a>
                        <button type="button" class="btn btn-black mn" id="sendMessage" data-conversation-id="" data-user=""><i class="fa fa-paper-plane fa-rotate-45 fs16" aria-hidden="true"></i></button>
                      </div>
                  </div>
                   <div id="messageAttachment" class="chat_attachment_tag"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane " id="In_Queue_tab">
        <div class="queue_wrap">
          <div class="p-15 bg_white theme-form">
            <div class="row">
              <div class="col-sm-6">
                <h4>In Queue (<span class="queueChatCount">0</span>)</h4>
              </div>
              <div class="col-sm-6">
                <div class="phone-control-wrap">
                  <div class="phone-addon v-align-top w-90">
                    <h5>Filter</h5>
                  </div>
                  <div class="phone-addon">
                    <div class="form-group height_auto mn">
                      <select class="form-control" data-live-search="true" id="chatFrom">
                        <option value=""></option>
                        <option value="Customer">Member Portal</option>
                        <option value="Agent">Agent Portal</option>
                        <option value="Group">Group Portal</option>
                        <option value="Website">External Website</option>
                      </select>
                      <label>Access</label>
                    </div>
                  </div>
                  <div class="phone-addon w-55 v-align-top">
                    <a href="javascript:void(0);" class="btn btn-info" id="queueSearch"><i class="fa fa-search"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <table data-toggle="table" class="<?=$table_class?> bg_white" id="queueChatTable">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Access</th>
                <th>Action</th>
                <th class="text-center">Queue Time</th>
              </tr>
            </thead>
            <tbody id="queueChatHtml">
              
            </tbody>
          </table>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="Chatting_tab">
        <div class="queue_wrap">
          <div class="p-15 bg_white theme-form">
            <div class="row">
              <div class="col-sm-5">
                <h4>Active Chats (<span class="chattingCount">0</span>)</h4>
              </div>
              <div class="col-sm-7">
                <div class="phone-control-wrap">
                  <div class="phone-addon v-align-top w-90">
                    <h5>Filter</h5>
                  </div>
                  <div class="phone-addon">
                    <div class="form-group height_auto mn">
                      <input type="text" name="" class="form-control" id="chattingAdmin">
                      <label>Admin Name/ID</label>
                    </div>
                  </div>

                  <div class="phone-addon w-200">
                    <div class="form-group height_auto mn">
                      <select class="form-control" data-live-search="true" id="chattingFrom">
                        <option value=""></option>
                        <option value="Customer">Member Portal</option>
                        <option value="Agent">Agent Portal</option>
                        <option value="Group">Group Portal</option>
                        <option value="Website">External Website</option>
                      </select>
                      <label>Access</label>
                    </div>
                  </div>
                  <div class="phone-addon w-55 v-align-top">
                    <a href="javascript:void(0);" class="btn btn-info" id="chattingSearch"><i class="fa fa-search"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <table data-toggle="table" id="chattingTable" class="<?=$table_class?> bg_white">
            <thead>
              <tr>
                <th>Name/ID/Activity</th>
                <th>Access</th>
                <th>Action</th>
                <th class="text-right">Admin Name</th>
              </tr>
            </thead>
            <tbody id="chattingHtml">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="conversationDynamicDiv" style="display: none">
  <div class="media conversation_list conversation_~conversationID~" data-conversation-id="~conversationID~" data-user="~userID~" data-chat-class="~chatClass~" data-encr="~encryptId~" data-type="~appUserType~">
      <div class="media-left">
        <div class="profile-img" id="leftTitleHtml_~conversationID~">~userFirstLetter~</div>
      </div>
      <div class="media-body">
        <div class="chat_head clearfix">
          <div class="pull-left">
            <h5 class="mn fs12" id="leftNameLinkHtml_~conversationID~">~userName~ <span id="chat_dashed_~conversationID~"> - </span><a href="javascript:void(0);" class="appUserClick" data-id="~encryptId~" data-type="~appUserType~" id="appUserClick_~conversationID~">  ~repID~</a></h5>
          </div>
          <div class="pull-right">
            <h5 class="mn fs12" id="last_conversation_time_~conversationID~">~conversationTime~</h5>
          </div>
        </div>
        <p class="fs12 mn" id="last_conversation_message_~conversationID~">~lastMessage~</p>
      </div>
  </div>
</div>

<div id="messageDynamicDiv" style="display: none">
  <div class="chat_msg_div" id="messageID_~messageID~" data-message-id="~messageID~" data-conversation-id="~conversationID~" data-user="~userID~">
    <div class="chat-name-circle online">~userFirstLetter~</div>
    <div class="chat-messge">
      <p class="chat-name">~userName~</p>
      <p> ~chatMessage~ </p>
      <div id="chatMessageAttachments_~messageID~" data-message-id="~messageID~" data-conversation-id="~conversationID~" data-user="~userID~" class="sb-message-attachments">
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
</div>
<form id="uploadForm" action="#" method="post" enctype="multipart/form-data" style="display: none">
    <input type="file" name="files[]" class="liveChat-upload-files" multiple="">
</form>

<div id="messageAttachmentDynamicDiv" style="display: none">
    <div class="messageAttachment" data-name="~attachmentName~" data-val="~attachmentValue~">
        <span>~attachmentName~</span>
        <a href="javascript:void(0)" class="removeAttachment"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
    </div>
</div>
<script type="text/javascript">
  var $newConversationData ='';
  var $newUserTypingData ='';
  var $newQueueData ='';
  var $newQueueDataInterval =10000;
  var $newChattingData ='';
  var $newChattingDataInterval =10000;
  var $newMessageData ='';
  var $lastConversationTime = '';
  var $lastMessageTime = '';
  var $agent_id = '<?= $loginChatID ?>';
  var $defaultChatDisplayed = 'No';
  var typingTimer;                
  var doneTypingInterval = 2000; 
  var $setTyping = '-1';
  

$(document).ready(function() { 
    $("#chat_window_scroll, #contact_list_scroll").mCustomScrollbar({
        theme: "dark",
        scrollbarPosition: "outside"
    });
    $('#queueChatTable').bootstrapTable().removeClass("table-hover");
    $('#chattingTable').bootstrapTable().removeClass("table-hover");
    
    
});

$(document).on('click',".tab_click",function(e){
    $tab = $(this).attr('data-tab');
    if($tab=='My_Chat_tab'){
      $defaultChatDisplayed = 'No';
      getConversationsList(); 
    }else if($tab=='In_Queue_tab'){
      $("#ajax_loader").show();
      getInQueueList();
    }else if($tab=='Chatting_tab'){
      $("#ajax_loader").show();
      getChattingList();
    }
    //updateAgentActivity();
});
$(document).off('keyup',"#textMessage");
$(document).on('keyup',"#textMessage",function(e){
  clearTimeout(typingTimer);
  $userId = $(this).attr('data-user');
  typingTimer = setTimeout(function(){
    doneTyping($agent_id)
  }, doneTypingInterval);
});
$(document).off('keydown',"#textMessage");
$(document).on('keydown',"#textMessage",function(e){

    var code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13) {
        $("#sendMessage").trigger('click');
        return true;
    }else if((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 65 && event.keyCode <= 90)){
      $conversationID = $(this).attr('data-conversation-id');
      $userId = $(this).attr('data-user');
      clearTimeout(typingTimer);
      adminTyping($agent_id,$conversationID);
    }
    
});
$(document).on('click',".quick-reply",function(){
   $val = $(this).attr('data-value');
   $("#textMessage").val($val);
});

$(document).on('click',"#attachFile",function(){
    $(".liveChat-upload-files").trigger('click');
});

$(document).on('change',".liveChat-upload-files",function(){
    let files = $(this).prop('files');
    sendButtonSpinner(true);
    for (var i = 0; i < files.length; i++) {
        let file = files[i];
        let form = new FormData();
        form.append('file', file);

        $.ajax({
            url: '<?= $LIVE_CHAT_HOST ?>' + '/include/upload.php',
            cache: false,
            contentType: false,
            processData: false,
            data: form,
            type: 'POST',
            success: function (response) {
                
                response = JSON.parse(response);
                if (response[0] == 'success') {
                    var name = response[1].substr(response[1].lastIndexOf('/') + 1);
                    var value = response[1];
                    
                    html = $('#messageAttachmentDynamicDiv').html();
                    html = html.replace(/~attachmentName~/g, name);
                    html = html.replace(/~attachmentValue~/g, value);
                    $("#messageAttachment").append(html);
                }
            }
        });
    }
    sendButtonSpinner(false);
    $(this).value = '';
});
$(document).on('click',"#sendMessage",function(){
  $text = $("#textMessage").val();
  $conversationID = $(this).attr('data-conversation-id');
  $userId = $(this).attr('data-user');
  $attachments = [];
  $("#messageAttachment .messageAttachment").each(function(){
      var name = $(this).attr('data-name');
      var value = $(this).attr('data-val');
      $attachments.push([name,value]);
  });
  sendButtonSpinner(true);
  
    SBF.ajax({
        function: 'send-message',
        user_id: $agent_id,
        conversation_id: $conversationID,
        message: $text,
        attachments: $attachments,
        conversation_status_code: 0,
        queue: false,
        payload: false
    }, (response) => {
        sendButtonSpinner(false);
        $("#textMessage").val('');
        $("#messageAttachment").html('');
        //updateAgentActivity();
        $attachments = [];
        getNewMessage($userId,$conversationID); 
    });
  

});
$(document).on('click',".removeAttachment",function(){
    $(this).parent().remove();
});

$(document).on("SBReady", function () {
    getConversationsList(); 
    getInQueueList();
    getChattingList();
    updateAgentActivity();
    
    $newQueueData = setInterval(getInQueueList,$newQueueDataInterval);
    $newChattingData = setInterval(getChattingList,$newChattingDataInterval);
    
    setInterval(function(){
      updateAgentActivity();
    },10000);
});



getConversationsList = function(){
    $("#ajax_loader").show();
    SBF.ajax({
        function: 'get-agent-conversations',
        agent_id:$agent_id,
        routing: false
    }, (response) => {
      if(response){
        $("#ajax_loader").hide();
        $("#conversationDiv").html('');
        $("#mychatsCount").html(response.length);
        $.each(response,function($key,$object){
            addConversationList($object,'Existing');
        });
      }
    });
}

getInQueueList = function(){
  
  $.ajax({
    url:'ajax_get_live_chat_queue.php',
    dataType:'JSON',
    data:{chatFrom:$("#chatFrom").val()},
    type:'POST',
    success:function(res){
      $("#ajax_loader").hide();
      $(".queueChatCount").html(res.queueCount);
      $("#queueChatHtml").html(res.html);
      $('#queueChatTable').bootstrapTable(
          'resetView',{height:430}
      );
    }
  });
  
}

getChattingList = function(){
  $.ajax({
    url:'ajax_get_live_chatting.php',
    dataType:'JSON',
    data:{chatFrom:$("#chattingFrom").val(),adminDetail:$("#chattingAdmin").val()},
    type:'POST',
    success:function(res){
      $("#ajax_loader").hide();
      $(".chattingCount").html(res.queueCount);
      $("#chattingHtml").html(res.html);
      $('#chattingTable').bootstrapTable(
          'resetView',{height:430}
      );
    }
  });
  
}

updateAgentActivity = function(){
  SBF.ajax({
      function: 'update-users-last-activity',
      user_id: $agent_id,
      return_user_id: $agent_id,
      check_slack: false,
  }, (response) => {
  });
}

$(document).on("click",".appUserClick",function(){

    $type= $(this).attr('data-type');
    $id= $(this).attr('data-id');

    if($type=="Agent"){
         window.open("agent_detail_v1.php?id="+$id, '_blank');
    }else if($type=="Group"){
         window.open("groups_details.php?id="+$id, '_blank');
    }else if($type == "Customer"){
        window.open("members_details.php?id="+$id, '_blank');
    }
});

$(document).on("click","#queueSearch",function(){  
    $("#ajax_loader").show();
    getInQueueList();
});

$(document).on("click","#chattingSearch",function(){  
    $("#ajax_loader").show();
    getChattingList();
});

$(document).on('click',".assignQueue",function(){
  $conversationID = $(this).attr('data-conversation-id');
  
  assignChatQueue($conversationID);
});

$(document).on('click',".assistConversation",function(){
  $conversationID = $(this).attr('data-conversation-id');
  
  assistChat($conversationID);
});

$(document).on('click',".chatDepartment",function(e){
  e.preventDefault();
  $("#chatDepartmentText").html($(this).attr('data-value'));
  $val = $(this).attr('data-id');
  $conversationID = $(this).attr('data-conversation-id');
  if($val != ''){
    SBF.ajax({
      function: 'update-conversation-department',
      conversation_id: $conversationID,
      department: $val,
    }, (response) => {
    });
    
  }
  
});
$(document).on('click',"#LeaveChat",function(e){
  e.preventDefault();
  $tmpConversationID = $(this).attr('data-conversation-id');
    swal({
      text: "Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Close Chat",
    }).then(function() {
        $("#ajax_loader").show();
        $.ajax({
          url:'ajax_close_chat.php',
          dataType:'JSON',
          data:{conversationID : $tmpConversationID,agent_id:$agent_id},
          type:'POST',
          success:function(res){
            $("#ajax_loader").hide();
            if(res.status == "success"){
              $mychatsCount = $("#mychatsCount").html();
              $mychatsCount = $mychatsCount - 1;
              $("#mychatsCount").html($mychatsCount);

              $prevLength =$(".conversation_"+$tmpConversationID).prev('.conversation_list').length;
              $nextLength =$(".conversation_"+$tmpConversationID).next('.conversation_list').length;

              if($nextLength != 0){
                $(".conversation_"+$tmpConversationID).next('.conversation_list').trigger('click');
              }else if($prevLength !=0){
                $(".conversation_"+$tmpConversationID).prev('.conversation_list').trigger('click');
              }else{
                $("#right_chat").hide();
              }
              $(".conversation_"+$tmpConversationID).remove();
            }
          }
        });
    }, function(dismiss) {
      
    })
  
});

assignChatQueue = function($conversationID){
    $.ajax({
      url:'ajax_assign_live_chat_queue.php',
      dataType:'JSON',
      data:{conversationID : $conversationID},
      type:'POST',
      success:function(res){
         if(res.status == "success"){
            getConversationsList();
            getChattingList();
            $("#ajax_loader").show();
            getInQueueList();
         }
      }
    });
}

assistChat = function($conversationID){
    $.ajax({
      url:'ajax_assist_live_chat.php',
      dataType:'JSON',
      data:{conversationID : $conversationID},
      type:'POST',
      success:function(res){
         if(res.status == "success"){
            getConversationsList();
            getChattingList();
            $("#ajax_loader").show();
            getInQueueList();
         }
      }
    });
}

$(document).on('click',".conversation_list",function(){
  $conversationID = $(this).attr('data-conversation-id');
  $userId = $(this).attr('data-user');
  $chatClass = $(this).attr('data-chat-class');
  $encryID = $(this).attr('data-encr');
  $appUserType = $(this).attr('data-type');
  if($newConversationData!=''){
    clearInterval($newConversationData);
  }
  if($newUserTypingData!=''){
    clearInterval($newUserTypingData);
  }
  getConversation($conversationID,$userId);
  //updateAgentActivity();
  $("#textMessage").val('');
  $("#messageAttachment").html('');
  $("#switchLogin").attr('href','javascript:void(0)');
  $("#switchLogin").removeAttr('target');
  $("#sendMessage").attr('data-conversation-id','');
  $("#sendMessage").attr('data-user','');
  $("#textMessage").attr('data-conversation-id','');
  $("#textMessage").attr('data-user','');
  $(".chatDepartment").attr('data-conversation-id','');
  $(".chatDepartment").attr('data-user','');
  $("#LeaveChat").attr('data-conversation-id','');
  $("#LeaveChat").attr('data-user','');

  $userTypeArray = ["Agent","Group","Customer"];

    
  if(jQuery.inArray($appUserType, $userTypeArray) >= 0){
      $("#switchLogin").attr('href','switch_login.php?id='+$encryID);
      $("#switchLogin").attr('target','_BLANK');
  } 
  $("#sendMessage").attr('data-conversation-id',$conversationID);
  $("#sendMessage").attr('data-user',$userId);
  $("#textMessage").attr('data-conversation-id',$conversationID);
  $("#textMessage").attr('data-user',$userId);

  $(".chatDepartment").attr('data-conversation-id',$conversationID);
  $(".chatDepartment").attr('data-user',$userId);

  $("#LeaveChat").attr('data-conversation-id',$conversationID);
  $("#LeaveChat").attr('data-user',$userId);

  $("#right_chat").removeClass('group');
  $("#right_chat").removeClass('member');
  $("#right_chat").removeClass('ex_web');
  $("#right_chat").removeClass('agent');
  $("#right_chat").addClass($chatClass);
  $(".conversation_list").removeClass('active');
  $(this).addClass('active');
  $(this).removeClass('unReadMessage');
});


getConversation = function($conversationID,$userId){

  $("#ajax_loader").show();
  $("#messageDiv").html('');
  $("#right_chat").hide();
  
  SBF.ajax({
      function: 'get-agent-conversation',
      conversation_id: $conversationID,
      user_id: $userId
  }, (response) => {
      if(response.details.conversation_status_code==4){
        if($("#conversationDiv .conversation_"+$conversationID).length != 0){
          $("#sendMessage").attr('disabled',true);
        }
      }else{
        $("#sendMessage").attr('disabled',false);
      }
      if(response.details.department>0){
         $("#chatDepartmentText").html($(".chatDepartment_"+response.details.department).attr('data-value'));

      }
      $("#ajax_loader").hide();
      $("#chatHeadingTitleHtml").html($("#leftTitleHtml_"+$conversationID).html());
      $("#chatHeadingNameLinkHtml").html($("#leftNameLinkHtml_"+$conversationID).html());
      $response = response.messages;
      $.each($response,function($k,$object){
        addMessage($object);
        if($lastConversationTime =='' || ($object.creation_time > $lastConversationTime)){
          $lastConversationTime = $object.creation_time;
        }
      });
      $newConversationData = setInterval(function(){
          getNewConversations($userId,$conversationID,$agent_id); 
      },10000);   

      $newUserTypingData = setInterval(function(){
          checkUserTyping($userId,$conversationID); 
      },3000);     
  });
  /*SBF.ajax({
      function: 'update-users-last-activity',
      user_id: $agent_id,
      return_user_id: $userId,
      check_slack: false,
  },(response) => {
    if (response === 'online') {
    } else {
    }
  });*/
}

getNewMessage = function($userId,$conversationID){
   
   SBF.ajax({
        function: 'get-new-messages',
        conversation_id: $conversationID,
        user_id: $userId,
        datetime: $lastMessageTime,
    }, (response) => {
        
        if(response){
           $.each(response,function($k,$object){
              $messageID = $object.id;
              if($("#messageDiv #messageID_"+$messageID).length == 0){
                addMessage($object);
                $message = $object.message;
                $("#last_conversation_time_"+$conversationID).html(SBF.beautifyTime($object.creation_time));
                $("#last_conversation_message_"+$conversationID).html($message);
              }
              
              if($lastMessageTime =='' || ($object.creation_time > $lastMessageTime)){
                  $lastMessageTime = $object.creation_time;
              }
              if($lastConversationTime =='' || ($object.creation_time > $lastConversationTime)){
                  $lastConversationTime = $object.creation_time;
              }
           });
        }
    });
}
getNewConversations = function($userId,$conversationID,$agent_id){
    SBF.ajax({
        function: 'get-new-agent-conversations',
        routing: false,
        user_id: $userId,
        datetime: $lastConversationTime,
        agent_id : $agent_id,
    }, (response) => {
        if(response){
           $.each(response,function($k,$object){
              $conversationID = $object.conversation_id;
              $messageID = $object.id;
              if($lastConversationTime =='' || ($object.creation_time > $lastConversationTime)){
                  $lastConversationTime = $object.creation_time;
              }
              /*if($object.conversation_status_code==4){
                  if($("#conversationDiv .conversation_"+$conversationID).length != 0){
                    $tmpConversationID = $conversationID;
                    $mychatsCount = $("#mychatsCount").html();
                    $mychatsCount = $mychatsCount - 1;
                    $("#mychatsCount").html($mychatsCount);
                    $(".conversation_"+$tmpConversationID).next('.conversation_list').trigger('click')
                    $(".conversation_"+$tmpConversationID).remove();

                    if($mychatsCount==0){
                      $("#right_chat").hide();
                    }

                  }
                  return true;
              }*/
              if($object.conversation_status_code==4){
                  if($("#conversationDiv .conversation_"+$conversationID).length != 0){
                    $("#sendMessage").attr('disabled',true);
                  }
              }
               if($("#messageDiv #messageID_"+$messageID).length == 0){
                  getNewMessage($userId,$conversationID);
               }

               if($("#conversationDiv .conversation_"+$conversationID).length == 0){
                  addConversationList($object,'New');
               }else{
                  if(!$(".conversation_"+$conversationID).hasClass('active')){
                    $(".conversation_"+$conversationID).addClass('unReadMessage');
                    $(".conversation_"+$conversationID).remove();
                    addConversationList($object,'New');
                  }
                  
                  $message = $object.message;
                  
                  $("#last_conversation_time_"+$conversationID).html(SBF.beautifyTime($object.creation_time));
                  $("#last_conversation_message_"+$conversationID).html($message);
                  
               }
           });
        }
    });
  
}


addConversationList = function($object,$conversationType){
  $first_name = $object.first_name;
  $last_name = $object.last_name;
  
  $userName = $first_name+" "+ $last_name;
  $tmpExplode = $userName.split(' ');

  $userFirstLetter = ($tmpExplode[0].substr(0,1)+""+$tmpExplode[1].substr(0, 1));

  $appUserId = $object.app_user_id;
  $appUserType = $object.app_user_type;
  $conversationID = $object.conversation_id;
  $repID = $object.rep_id;
  $conversationTime = SBF.beautifyTime($object.creation_time);
  $lastMessage = $object.message;
  $encryptId = $object.encryptId;
  $userID = $object.user_id;

  if($appUserType == "Agent"){
    $chatClass='agent';
  }else if($appUserType == "Group"){
    $chatClass='group';
  }else if($appUserType == "Customer"){
    $chatClass='member';
  }else if($appUserType == "Website"){
    $chatClass='ex_web';
  }

  html = $('#conversationDynamicDiv').html();
  html = html.replace(/~userFirstLetter~/g, $userFirstLetter);
  html = html.replace(/~userName~/g, $userName);
  html = html.replace(/~appUserId~/g, $appUserId);
  html = html.replace(/~appUserType~/g, $appUserType);
  html = html.replace(/~conversationID~/g, $conversationID);
  html = html.replace(/~repID~/g, $repID);
  html = html.replace(/~conversationTime~/g, $conversationTime);
  html = html.replace(/~lastMessage~/g, $lastMessage);
  html = html.replace(/~encryptId~/g, $encryptId);
  html = html.replace(/~userID~/g, $userID);
  html = html.replace(/~chatClass~/g, $chatClass);
  if($conversationType=="Existing"){
    $('#conversationDiv').append(html);
  }else if($conversationType=="New"){
    $('#conversationDiv').prepend(html);
    $(".conversation_"+$conversationID).addClass('unReadMessage');
  }
  $userTypeArray = ["Agent","Group","Customer"];

  if(jQuery.inArray($appUserType, $userTypeArray) < 0){
    $("#appUserClick_"+$conversationID).hide();
    $("#chat_dashed_"+$conversationID).hide();
  } 
  $(".conversation_"+$conversationID).addClass($chatClass);

  if($defaultChatDisplayed == 'No'){
     $defaultChatDisplayed ='Yes';
     $(".conversation_"+$conversationID).trigger('click');
  }
  $("#contact_list_scroll").mCustomScrollbar('update');
}

addMessage = function($object){
  $first_name = $object.first_name;
  $last_name = $object.last_name;
  
  $userName = $first_name+" "+ $last_name;
  $tmpExplode = $userName.split(' ');

  $userFirstLetter = ($tmpExplode[0].substr(0,1)+""+$tmpExplode[1].substr(0, 1));

  $conversationID = $object.conversation_id;
  $userID = $object.user_id;

  $chatMessage = $object.message;
  $messageID = $object.id;
  $senderType = $object.user_type;
  $messageAtachments = $object.attachments;
  

  if($object.user_type=='admin'){
      $userName = $first_name;
  }

  
  html = $('#messageDynamicDiv').html();
  html = html.replace(/~userFirstLetter~/g, $userFirstLetter);
  html = html.replace(/~userName~/g, $userName);
  html = html.replace(/~conversationID~/g, $conversationID);
  html = html.replace(/~chatMessage~/g, $chatMessage);
  html = html.replace(/~userID~/g, $userID);
  html = html.replace(/~messageID~/g, $messageID);
  
 
  

  $('#messageDiv').append(html);
  $("#right_chat").show();
  if($senderType != 'admin'){
      $("#messageID_"+$messageID).addClass('recevier_msg');
  }

  if ($messageAtachments!='') {
      $messageAtachmentsArr = JSON.parse($messageAtachments);
      $.each($messageAtachmentsArr,function($k,$v){
          if (/.jpg|.jpeg|.png|.gif/.test($v[1])) {
              $attachmentData = `<div class="sb-image"><img src="${$v[1]}" /></div>`;
          } else {
              $attachmentData = `<a target="_blank" href="${$v[1]}">${$v[0]}</a> </br>`;
          }
          $("#chatMessageAttachments_"+$messageID).append($attachmentData);
      });
  }

  $("#chat_window_scroll").mCustomScrollbar('update').mCustomScrollbar("scrollTo","bottom",{scrollInertia:0});
}

adminTyping = function($userId,$conversationID){
  if($setTyping=='-1'){
     $setTyping = $conversationID;
      setTyping($userId,$conversationID)
  }
  
  SBF.ajax({
        function: 'is-typing',
        conversation_id: $conversationID,
        user_id: $userId,
  }, (response) => {
  });
}
doneTyping = function($userId) {
   $setTyping = '-1';
   setTyping($userId,-1);
}

setTyping = function($userId,$conversationID){
  SBF.ajax({
        function: 'set-typing',
        conversation_id: $conversationID,
        user_id: $userId,
  }, (response) => {
  });
}

sendButtonSpinner = function($status){
  if($status){
    $("#sendMessage").append(' <i class="buttonSpinner fa fa-spinner fa-spin"></i>');
    $("#sendMessage").attr('disabled',true);
  }else{
    $(".buttonSpinner").remove();
    $("#sendMessage").attr('disabled',false);
  }
}

checkUserTyping = function($userId,$conversationID){
  SBF.ajax({
        function: 'is-typing',
        conversation_id: $conversationID,
        user_id: $userId,
  }, (response) => {
     if(response){
        $("#userIsTyping").show();
     }else{
        $("#userIsTyping").hide();
     }
  });
}
</script>
