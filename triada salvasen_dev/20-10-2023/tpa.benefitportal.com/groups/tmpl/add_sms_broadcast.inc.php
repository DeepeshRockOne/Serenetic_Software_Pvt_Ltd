<style type="text/css">
.editor_tag_wrap_inner  .mCSB_outside + .mCSB_scrollTools{ right:-24px; }
</style>
<div class="container m-t-30">
  <form action="" role="form" method="post" name="smsBroadcastFrm" id="smsBroadcastFrm" enctype="multipart/form-data">
    <input type="hidden" name="msgCnt" id="msgCnt" value="<?=$msgCnt?>">
  <div class="panel panel-default panel-block communication_panel">
    <div class="panel-body">
      <div class="phone-control-wrap">
        <div class="phone-addon w-90 v-align-top">
          <img class="media-object" src="<?=$HOST?>/images/icons/circle_sms.svg" alt="...">
        </div>
        <div class="phone-addon theme-form text-left">
          <p class="fs20 m-b-20">+ Text Message (SMS) Broadcast</p>
              <div class="row">
                <div class="col-md-3">
              <div class="form-group ">
                <select class="form-control" name="user_group" id="userGroupSel">
                  <option></option>
                  <?php /*<option value="Agents" <?=($userGroup == 'Agents') ? "selected='selected'" : '' ?>>Agents</option>*/?>
                  <option value="Members" <?=($userGroup == 'Members') ? "selected='selected'" : '' ?>>Members</option>
                  <option value="Leads" <?=($userGroup == 'Leads') ? "selected='selected'" : '' ?>>Enrollee</option>
                </select>
                <label>Select User Group</label>
                <span class="error" id="error_user_group"></span>
              </div>
            </div>
            <div id="userFilterDiv" style="display: none;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="panel panel-default panel-block">
    <div class="panel-body ">
      <div class="theme-form">
        <h4 class="m-t-n">Text Message (SMS) Content</h4>
        <p class="m-b-20">Use the tool below to bulid your SMS.</p>
        <div class="row">
         <div class="col-lg-9 col-md-9 col-sm-8">
            <div class="form-group">
              <input type="text" class="form-control" name="broadcast_name" id="broadcastName" value="<?=$broadcastName?>">
              <label>Name SMS Broadcast</label>
              <span class="error" id="error_broadcast_name"></span>
            </div>
            <div id="mainMsgDivs">
              <?php if(!empty($broadcasterMessageRes)){
                $msgNo = 1;
                foreach ($broadcasterMessageRes as $key => $message) {
              ?>
                  <div class="allMsgDiv" id="msgDiv<?=$msgNo?>">
                    <textarea class="form-control msgInput" id="msg<?=$msgNo?>" name="messages[<?=$message['id']?>]" rows="17"  maxlength="160" placeholder="Type Something...."><?=$message['message']?></textarea>
                    <span class="error" id="error_messages_<?=$message['id']?>"></span>
                  </div> 
              <?php
              $msgNo++;    
                }
              }else{ ?>
                <div class="allMsgDiv" id="msgDiv1">
                  <textarea class="form-control msgInput" id="msg1" name="messages[1]" rows="17"  maxlength="160" placeholder="Type Something...."></textarea>
                  <span class="error" id="error_messages_1"></span>
                </div> 

              <?php } ?>
            
            </div>

            <div class="clearfix m-b-20">
              <div class="pull-left m-t-7">
                <p class="mn">Characters Remaining : <span id="remainChr">160</span></p>
              </div>
              <div class="pull-right">
                <div id="msgLinkDivs">
                  <?php 
                    if(!empty($broadcasterMessageRes)){
                      $msgLink = 1;
                      $closeIcon = '';
                      foreach ($broadcasterMessageRes as $key => $message) {
                        if($msgLink > 1){
                          $closeIcon = '<span onclick="removeMsgBox('.$msgLink.')" class="text-light-gray">X</span>';
                        }
                      
                  ?>
                    <span id="msgLink<?=$msgLink?>"><a href="javascript:void(0);" class="btn red-link" onclick="viewMsgBox(<?=$msgLink?>)">Text <?=$msgLink?></a><?=$closeIcon?></span>

                  <?php
                      $msgLink++;    
                      }
                    }else{ 
                  ?>
               <span id="msgLink1"><a href="javascript:void(0);" class="btn blue-link p-r-0 p-l-0" onclick="viewMsgBox(1)">Text 1</a></span>

              <?php } ?>

                </div>
              </div>
            </div>
             <div class="clearfix">
              <div class="pull-left m-b-20">
                <a href="javascript:void(0);" class="btn btn-info" id="addMsg" onclick="addMsgBox()">+ Text</a>
                <a href="javascript:void(0);" class="btn btn-action btn-outline" id="sendSmsBtn">Send Test</a>
              </div>
              <div class="pull-right m-b-20">
                 <div class="text-right">
                 <a data-href="smart_tag_popup.php" class="btn btn-action-o smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
                <p class="mn p-t-10"> Dynamic tags may affect character</p>
              </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-4">
            <div class="phone_preview_wrap">
              <div class="phone_preview_thumb">
                <img src="<?=$GROUP_HOST?>/images/iphone.png" class="img-responsive">
              </div>
              <div class="phone_screen_wrap">
                <div class="sms_message_preview">
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr>
           <div class="clearfix">
            <h4 class="m-t-n">Delivery <i class="fs14">(all communications sent on the hour)</i></h4>
            <label><input type="checkbox" name="future_check_box" id="future_check_box" <?=(!empty($future_check_box) && $future_check_box == 'Y') ? "checked" : "" ?>> Set this broadcast at a future date?</label>
            <h4 class="m-t-20 display_schedule" style="<?=(!empty($future_check_box) && $future_check_box == 'Y') ? "" : "display: none;" ?>">Schedule</h4>
          </div>
          <div id="main_schedule_div">
            <div class="clearfix"></div>
            <?php $foreach_counter = 0;
            if($future_check_box == 'Y' && !empty($broadcasterScheduleRes) && count($broadcasterScheduleRes) > 0) {
              foreach ($broadcasterScheduleRes as $br_key => $br_value) { 
                $foreach_counter++;?>
                <div class="inner_schedule_div" id="inner_schedule_div_<?=$br_value['id']?>">
                  <input type="hidden" name="dynamic_fields[<?=$br_value['id']?>]">
                  <div class="row">
                    <div class="col-sm-3">
                      <div class="form-group ">
                        <div class="input-group">
                          <a href="javascript:void(0);" class="input-group-addon schedule_date_icon" id="schedule_date_icon_<?=$br_value['id']?>"><i class="fa fa-calendar "></i></a>
                          <div class="pr">
                            <input type="text" id="schedule_date_<?=$br_value['id']?>" name="schedule_date[<?=$br_value['id']?>]" class="form-control schedule_date_input_class" data-id="<?=$br_value['id']?>" placeholder="MM / DD / YYYY" value="<?= (!empty($br_value['schedule_date']) && $br_value['schedule_date'] != '0000-00-00' ) ? date('m/d/Y', strtotime($br_value['schedule_date'])) : '' ?>">
                          </div>
                        </div>
                        <span class="error error_preview" id="error_schedule_date_<?=$br_value['id']?>"></span>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group ">
                        <select class="add_control_<?=$br_value['id']?> form-control" name="schedule_hour[<?=$br_value['id']?>]">
                          <option value=""></option>
                          <?php for ($i=1; $i < 25; $i++) {
                            $time_span = 'AM';
                            $j = $i;
                            if($i > 12) {
                              $j = $i - 12;
                              $time_span = 'PM';
                            } ?>
                            <option value="<?=$i?>" <?= (!empty($br_value['schedule_hour']) && $br_value['schedule_hour'] == $i) ? "selected='selected'" : '' ?>><?=$j . ' ' . $time_span?></option>
                          <?php } ?>
                        </select>
                        <label>Hour</label>
                        <span class="error error_preview" id="error_schedule_hour_<?=$br_value['id']?>"></span>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <div class="form-group ">
                        <select class="add_control_<?=$br_value['id']?> form-control" name="schedule_time_zone[<?=$br_value['id']?>]">
                          <option value=""></option>
                          <option value="CST" <?= (!empty($br_value['time_zone']) && $br_value['time_zone'] == 'CST') ? "selected='selected'" : '' ?>>CST</option>
                          <option value="EST" <?= (!empty($br_value['time_zone']) && $br_value['time_zone'] == 'EST') ? "selected='selected'" : '' ?>>EST</option>
                        </select>
                        <label>Time Zone</label>
                        <span class="error error_preview" id="error_schedule_time_zone_<?=$br_value['id']?>"></span>
                      </div>
                    </div>
                    <div class="col-sm-1 m-t-7">
                      <div class="form-group ">
                        <a class="addon_close  remove_schedule_group" href="javascript:void(0);" id="remove_schedule_group_<?=$br_value['id']?>" style="<?=$foreach_counter<2 ? 'display: none;' : ''?>">X</a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php }
            } ?>

          </div>
            <div class="clearfix">
              <a href="javascript:void(0);" class="btn red-link display_schedule" id="add_schedule" style="<?=(!empty($future_check_box) && $future_check_box == 'Y') ? '' : 'display: none;' ?>">+ Additional Date</a>
            </div>
            <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
            <input type="hidden" name="display_counter" id="display_counter" value="<?=$div_counter_in_php?>">
            <input type="hidden" name="div_counter" id="div_counter" value="<?=$div_counter_in_php?>">
            <input type="hidden" name="send_user_sms" id="send_user_sms" value="">
            <input type="hidden" name="broadcaster_id" id="broadcaster_id" value="<?=!empty($broadcasterRes['id']) ? $broadcasterRes['id'] : ''?>">
            <hr>
          </div>
          <input type="hidden" name="action_type" value="" id="action_type">
          <div class="text-right m-b-10">
            <?php if(empty($broadcastStatus) || ($broadcastStatus != 'Completed') || $is_clone == 'Y') { ?>
              <a href="javascript:void(0);" class="btn btn-action" id="send_brodcast_btn">Send</a>
              <a href="javascript:void(0);" class="btn btn-info" id="draft_brodcast_btn">Save As Draft</a>
            <?php } ?>
            <a href="communications_queue.php" class="btn red-link">Cancel</a>
          </div>

    </div>
  </div>

  <!-- Send SMS popup Code Start -->
  <div style="display: none;">
    <div class="panel panel-default panel-block panel-shadowless mn" id="sendSmsPopup">
    <div class="panel-body">
      <p class="fs18">What Phone # would you like to send this test to?</p>
      <div class="theme-form">
        <div class="form-group">
            <input type="text" class="form-control" name="send_sms" id="sendTestSms">
            <label>Phone (xxx) xxx-xxxx</label>
              <span class="error" id="error_send_user_sms"></span>
        </div>
      </div>
      <div class="clearfix  text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="sendTestSmsBtn">Send Test</a> 
          <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
        </div>
    </div>
    </div>
  </div>
  <!-- Send SMS popup Code Start -->
  </form>

  <!-- dynamic schedule div add start -->
  <div id="dynamic_schedule_div" style="display: none;">
    <div class="inner_schedule_div" id="inner_schedule_div_~number~">
      <input type="hidden" name="dynamic_fields[~number~]">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group ">
            <div class="input-group">
              <a href="javascript:void(0);" class="input-group-addon schedule_date_icon" id="schedule_date_icon_~number~"><i class="fa fa-calendar "></i></a>
              <div class="pr">
                <input type="text" id="schedule_date_~number~" name="schedule_date[~number~]" class="form-control schedule_date_input_class" data-id="~number~" placeholder="MM / DD / YYYY">
              </div>
            </div>
            <span class="error error_preview" id="error_schedule_date_~number~"></span>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group ">
            <select class="add_control_~number~" name="schedule_hour[~number~]">
              <option value=""></option>
              <?php for ($i=1; $i < 25; $i++) {
                $time_span = 'AM';
                $j = $i;
                if($i > 12) {
                  $j = $i - 12;
                  $time_span = 'PM';
                } ?>
                <option value="<?=$i?>"><?=$j . ' ' . $time_span?></option>
              <?php } ?>
            </select>
            <label>Hour</label>
            <span class="error error_preview" id="error_schedule_hour_~number~"></span>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group ">
            <select class="add_control_~number~" name="schedule_time_zone[~number~]">
              <option value=""></option>
              <option value="CST">CST</option>
              <option value="EST">EST</option>
            </select>
            <label>Time Zone</label>
            <span class="error error_preview" id="error_schedule_time_zone_~number~"></span>
          </div>
        </div>
        <div class="col-sm-1 m-t-7">
          <div class="form-group ">
          <a class="addon_close  remove_schedule_group" href="javascript:void(0);" id="remove_schedule_group_~number~" style="display: none;">X</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- dynamic schedule div add end -->
</div>

<script type="text/javascript">
  $(document).ready(function() {
    // initialization code start

    var not_win = '';
      $(".smart_tag_popup").on('click',function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=768,height=600");
      if(not_win.closed) {  
        alert('closed');  
      } 
    });

    $('#sendSmsBtn').colorbox({
      inline:true,
      href:"#sendSmsPopup",
      height:"200px",
      width:"450px",
      closeButton: false,
    });
    $("#sendTestSms").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
  
    $(".editor_tag_wrap_inner").mCustomScrollbar({
        theme:"dark",
        scrollbarPosition: "outside"
    });

    broadcasterId = '<?=$broadcasterId?>';
    userGroup = '<?=$userGroup?>';
    specificUser = '<?=$specificUser?>'
    if(userGroup != ''){
      $broadcastId = $("#broadcaster_id").val();
      if(specificUser == 'Y'){
        getUserGroupFilter(userGroup,userGroup,$broadcastId);
      }else{
        getUserGroupFilter(userGroup,'',$broadcastId);    
      }
       $("#userFilterDiv").show();
    }
    if(broadcasterId != ''){
      viewMsgBox(1);
      updateRemainingChr('msg1');
    }

    // User group filter code start
      $(document).off("#userGroupSel");
      $(document).on("change","#userGroupSel",function(){
        $userGroup = $("#userGroupSel option:selected").val();
        if($userGroup != ''){
          $("#userFilterDiv").show();
          getUserGroupFilter($userGroup,'','');
        }else{
          $("#userFilterDiv").hide();
        }
      });

      $(document).off("#specific_user_group");
      $(document).on("click","#specific_user_group",function(){
          $userGroup = $("#userGroupSel option:selected").val();
        if($(this).is(":checked")){
          $specific = $("#userGroupSel option:selected").val();
          getUserGroupFilter($userGroup,$specific,'');
        }else{
          getUserGroupFilter($userGroup,'','');
        }
      });

    // schedule div code start
      $(document).off("click","#future_check_box");
      $(document).on("click","#future_check_box", function(){
        if($(this).prop( "checked" ) === false){
          $("#send_brodcast_btn").html('Send');
          $(".display_schedule").hide();
          $("#main_schedule_div").html('');
        } else {
          $("#send_brodcast_btn").html('Schedule');
          $(".display_schedule").show();
          loadScheduleDiv();
          $div_counter = parseInt($("#div_counter").val()) + 1;
          $("#div_counter").val($div_counter);
        }
      });

      $(document).off('click','#add_schedule');
      $(document).on('click','#add_schedule', function(){
        $div_counter = parseInt($("#div_counter").val()) + 1;
        $("#div_counter").val($div_counter);
        loadScheduleDiv();
        if($div_counter > 9){
          $(this).hide();
        }
      });

      $(document).off("click",".remove_schedule_group");
      $(document).on("click",".remove_schedule_group", function(){
        $div_id = $(this).attr('id').replace("remove_schedule_group_","");
        $div_counter = parseInt($("#div_counter").val()) - 1;
        $("#div_counter").val($div_counter);
        $("#inner_schedule_div_"+$div_id).remove();
        if($div_counter < 11){
          $("#add_schedule").show();
        }
      });

      $('.schedule_date_icon').datepicker({
        "startDate": new Date(),
      }).on('changeDate', function(event){
        $schedule_input_id = event.target.id.replace("schedule_date_icon_","schedule_date_");
        $("#"+$schedule_input_id).val(event.format());
      });

  
      $(document).off("click", ".schedule_date_input_class");
      $(document).on("click", ".schedule_date_input_class", function(){
         $("#schedule_date_icon_"+$(this).attr('data-id')).datepicker('show');
      });

    $(document).off("click","#send_brodcast_btn");
    $(document).on("click","#send_brodcast_btn",function(){
      $(".error").html('').hide();
      $("#action_type").val('send');
      ajaxCall();
    });

    $(document).off("click","#draft_brodcast_btn");
    $(document).on("click","#draft_brodcast_btn",function(){
      $("#action_type").val('draft');
      ajaxCall();
    });

    $(document).off("click","#sendTestSmsBtn");
    $(document).on("click","#sendTestSmsBtn",function(){
      $("#send_user_sms").val($("#sendTestSms").val());
      $("#action_type").val('send_sms');
      ajaxCall();
    });

    // msg code start
    $(".msgInput").keyup(function() {
        msgId = $(this).attr("id");
        updateRemainingChr(msgId);
        
    });

  });


  addMsgBox = function(){
    $msgCnt = parseInt($("#msgCnt").val()) + 1;
    
    $textAreaHtml = '<div class="allMsgDiv" id="msgDiv'+$msgCnt+'"><textarea class="msgInput form-control" id="msg'+$msgCnt+'" name="messages[m'+$msgCnt+']" maxlength="160" rows="17" placeholder="Type Something...."></textarea><span class="error" id="error_messages_m'+$msgCnt+'"></span></div>';

    $textAreaLink = '<span id="msgLink'+$msgCnt+'" class="m-l-10"><a href="javascript:void(0);" class="btn red-link"  onclick="viewMsgBox('+$msgCnt+')">Text '+$msgCnt+'</a>&nbsp;&nbsp;<span onclick="removeMsgBox('+$msgCnt+')" class="text-light-gray">X</span></span>';

      $(".allMsgDiv").hide();
      $("#mainMsgDivs").append($textAreaHtml);
      $("#msgLinkDivs").append($textAreaLink);
      $("#msgCnt").val($msgCnt);
  };
  viewMsgBox = function($msgId){
    $(".allMsgDiv").hide();
    $("#msgDiv"+$msgId).show();
     updateRemainingChr('msg'+$msgId);
  };
  removeMsgBox = function($msgId){
    $("#msgDiv"+$msgId).remove();
    $("#msgLink"+$msgId).remove();
    $(".allMsgDiv").hide();
    $("#msgDiv1").show();
  };
  updateRemainingChr = function($msgId){
    var chars = $('#'+$msgId).val().length;
    $("#remainChr").text(160 - chars);
    $(".sms_message_preview").html($('#'+$msgId).val());
  };

  getUserGroupFilter = function($userGroup,$specific,$broadcasterId){
    $.ajax({
          url: "ajax_get_broadcaster_usergroup.php",
          type: 'POST',
          data: {userGroup:$userGroup,specific:$specific,broadcast_id:$broadcasterId},
          dataType : 'json',
          beforeSend:function(){
                  $("#ajax_loader").show();
                },
          success: function(res) {
             $("#ajax_loader").hide();
              $("#userFilterDiv").html(res.data);
              $(".se_multiple_select").multipleSelect({
              });
               // $(".se_multiple_select").multipleSelect('refresh');
              common_select();
              fRefresh();
              //$("input[type='checkbox']:not(.se_multiple_select)").uniform();
              $("#specific_user_group").uniform();

              $('#triggerActionSel').selectpicker('refresh');
              $('#toEmailUserSel').selectpicker('refresh');

          }
        });
  };
  loadScheduleDiv = function(){
    $count = $("#smsBroadcastFrm .inner_schedule_div").length;
    $display_btn = 'N';
    if($count >= 1){
      $display_btn = 'Y';
    }
    $number = $count+1;
    $display_counter = parseInt($('#display_counter').val());
    if($display_counter > $count){
      $number = $display_counter + 1;
    }

    $neg_number = $number * -1;

    html = $('#dynamic_schedule_div').html();
    $('#main_schedule_div').append(html.replace(/~number~/g, $neg_number));
    $("#schedule_date_"+$neg_number).inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    if($display_btn == 'Y'){
      $("#remove_schedule_group_"+$neg_number).show();
    }

    $('#schedule_date_icon_'+$neg_number).datepicker({
      "startDate": new Date(),
    }).on('changeDate', function(event){
      $schedule_input_id = event.target.id.replace("schedule_date_icon_","schedule_date_");
      $("#"+$schedule_input_id).val(event.format());
    });

    $("#display_counter").val($number);
    $("#smsBroadcastFrm .add_control_"+$neg_number).addClass("form-control");
    $('#smsBroadcastFrm .add_control_'+$neg_number).selectpicker({ 
      container: 'body', 
      style:'btn-select',
      noneSelectedText: '',
      dropupAuto:false,
    });
  };
  ajaxCall = function(){
    $("#ajax_loader").show();
    $.ajax({
      url:"<?= $GROUP_HOST ?>/ajax_add_sms_broadcast.php",
      data: $("#smsBroadcastFrm").serialize(),
      method: 'POST',
      dataType: 'json',
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == 'success') {
          $(".error").html('').hide();
          if(res.action_type == 'send_sms'){
            setNotifySuccess("Test SMS send Successfully!");
            parent.$.colorbox.close(); return false;
          } else {
            setNotifySuccess("SMS Broadcaster Added Successfully!");
            setTimeout(function(){ 
              window.location.href = "communications_queue.php";
            }, 1000);
          }
          $("#ajax_loader").hide();
        } else if (res.status == 'fail') {
           var is_error = true;
          $("#ajax_loader").hide();
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
             if(is_error){
                var offset = $('#error_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 150;
                $('body,html').animate({scrollTop: totalScroll}, 1200);
                is_error = false;
            }
          });
        }
      }
    });
  };
</script>