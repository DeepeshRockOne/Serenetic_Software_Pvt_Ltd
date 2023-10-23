<div class="panel panel-default">
  <div class="panel-body">
    <p class="fs18 mn"><strong class="fw600">
      <?=$res_name['name']?>
      </strong> - <span class="red-link">
      <?=$res_name['display_id']?>
      </span></p>
    <p class="fs12 mn text-blue">
      <?=isset($note_res['date']) ? $tz->getDate($note_res['cdate']) :  $tz->getDate($res_name['cdate'])?>
    </p>
  </div>
</div>
<div class="panel panel-default panel-block">
  <div class="panel-body p-t-0">
    <?php if($show!='show') { ?>
    <div class="panel-heading bg_light_primary text-white fs18">
      <span class="text-white">
      <?php if(!empty($note_id)) {
            echo "Edit Note";
        } else if(!empty($reply_id)){
            echo "Reply to Note: ". $reply_note_subject;
        } else {
            echo "Add Note";
        }?>
     </span>
    </div>
    <?php } ?>
    <div class="custom_notes_area" style="display : <?= empty($note_id) ? 'block' : 'none' ?>">
      <form name="frm_note_add_main" id="frm_note_add_main" method="POST"
                      action="ajax_account_note_operation.php?customer_id=<?= $customer_id; ?>&operation=add_note"
                      enctype="multipart/form-data">
        <input type="hidden" name="reply_id" value="<?=($reply_id != '') ? $reply_id : ''?>">
        <input type="hidden" name="from_page" value="<?=$from?>">
        <input type="hidden" name="user_type" value="<?=$type?>">
        <div class="form-group m-b-0">
          <textarea name="note_text" class="form-control resizable" rows="17"
                                          placeholder="Type something..." id="add_note_text"></textarea>
          <p class="error" id="error_description"></p>
          <input type="file" name="note_file" id="note_file"  onchange="javascript:updateList()" style="display: none">
        </div>
        <div class="row m-t-30">
          <div class="col-xs-6"><button type="button" id="post_note" class="btn btn-action">
            <?=($reply_id != '') ? 'Reply' : 'Post' ?>
            </button> </div>
          <div class="col-xs-6">
          <div class="pull-right">
          <div class="phone-control-wrap">
          <div class="phone-addon">
          <div id="fileList" class="file_name_div" style="display:none;">
            <div class="input-group" >
                <input type="text" name="note_file_display" id="note_file_display" placeholder="File Name" class="form-control " value="<?= isset($note_res['file_name'])?$note_res['file_name']:'' ?>">
                    <span class="input-group-addon"><a href="javascript:void(0)" class="delete_file"><i class="fa fa-close"></i></a>
                    <input type="hidden" name="delete_file_value" value="N" class="delete_file_value">
                  </span> 
              </div>
              </div>
              </div>
              <!-- <a href="javascript:void(0)" title="Add document with Note" id="note_file_link" class="text-light-gray phone-addon"> <i class="fa fa-paperclip fa-2x"></i></a> -->
              </div>
          </div>
          </div>
          <!-- <div id="fileList" class="pull-right"></div> -->
        </div>
      </form>
    </div>
    <div class="custom_edit_notes_area table_br_gray" style="display : <?= empty($note_id) ? 'none' : 'block' ?>">
      <form name="frm_note_edit_main" id="frm_note_edit_main" method="POST" action="ajax_account_note_operation.php?id=<?= $customer_id; ?>&operation=edit_note"
                      enctype="multipart/form-data">
        <input type="hidden" id="note_id" name="note_id" value="<?= $note_res['id'] ?>">
        <div class="panel panel-default">
        <div class="panel-body">

          <div class="form-group">
            <textarea name="note_text" cols="3" class="form-control resizable" rows="15"
                                          placeholder="Notes"
                                          id="note_edit_text" <?php echo $show =='show' ? 'readonly="readonly"' : ''?>><?= $note_res['description'] ?></textarea>
            <p class="error" id="error_description"></p>
            <input type="file" name="note_file" id="note_file_edit" onchange="javascript:updateListEdit()" style="display: none">
          </div>

          <div class="file_name_div form-group" style="display: <?php echo ($note_res['file_name'] != '') ? 'block' : 'none'; ?>">
            <div class="input-group">
                     <input type="text" name="note_file_name_text" id="note_file_name_text" placeholder="File Name" class="form-control " value="<?= $note_res['file_name'] ?>">
                    <span class="input-group-addon"><a href="<?php echo $show!='show' ? 'javascript:void(0)' : $AGENT_HOST.'/download_note_file.php?file_name='.$note_res['file_name'];?>" class="<?php if($show!='show') { ?>delete_file <?php } ?>"><i class="fa <?php echo $show!='show' ? 'fa-close' : 'fa-download'?>"></i></a>
                    <input type="hidden" name="delete_file_value" value="N" class="delete_file_value">
                    </span>
              </div>
          </div>
          
          <div class="clearfix">
            <div class="pull-left">
              <?php if($show!='show') { ?>
              <button type="button" id="edit_post_note" class="btn btn-action ">Save</button> 
              <a href="javascript:void(0)" class="btn red-link m-l-15" onclick="parent.$.colorbox.close();">Cancel</a>
              <?php } ?>
            </div>
            <?php /*if($show=='show') { ?>
            <div class="clearfix">
                <div class="pull-right" >
                    <a class="m-r-5" href="javascript:void(0);" class="m-r-5" data-toggle="tooltip" title="Reply" id="replay_note_id" data-value="<?= $note_res['user_type'] ?>" onclick="replay_note(<?= $note_res['id'] ?>)"><i class="fa fa-mail-reply fs18"></i></a>
                </div>
            </div>    
            <?php } */?>
            <div class="clearfix">
            <div class="pull-right">
              <?php if($show!='show') { ?>
              <!-- <a href="javascript:void(0)" title="Add document with Note" id="note_file_edit_link" class="text-light-gray "><i class="fa fa-paperclip fa-2x"></i></a> -->
              <?php } ?>
                <div id="fileListedit" class="file_name_div form-group" style="display:none;">
                    <div class="input-group" >
                        <input type="text" name="note_file_display_edit" id="note_file_display_edit" placeholder="File Name" class="form-control " value="<?= $note_res['file_name'] ?>">
                            <span class="input-group-addon"><a href="javascript:void(0)" class="delete_file"><i class="fa fa-close"></i></a>
                            <input type="hidden" name="delete_file_value" value="N" class="delete_file_value">
                        </span> 
                    </div>
                </div>
              <?php if($show=='show'){?><div class=" fs12 "><em><strong>Created by : </strong></em> <?php echo $admin_name['name'].' - '.$admin_name['display_id']; } ?></div>
               </div>
              </div>
          </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#post_note").click(function () {
            parent.disableButton($(this));
            $("#frm_note_add_main").submit();
        });

        $(".delete_file").click(function () {
            $(".delete_file_value").val("Y");
            $(".file_name_div").hide();
        });

        $("#edit_post_note").click(function () {
            parent.disableButton($(this));
            $("#frm_note_edit_main").submit();
        });

        $("#note_file_link").click(function () {
            $("#note_file").click();
            // $("#fileList").show();
        });

        $("#note_file_edit_link").click(function () {
            $("#note_file_edit").click();
        });

        $('#frm_note_add_main').ajaxForm({
            beforeSubmit:function(arr, $form, options){
                $("#ajax_loader").show();
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                $("#ajax_loader").hide();
                parent.enableButton($("#post_note"));
                if (res.status == 'success') {
                    window.opener.setNotifySuccess("Note created successfully.");
                    var user_type = '<?=$type?>';
                    if(user_type == 'Admin'){
                        url = "admin_profile.php";                            
                    } else if(user_type == 'Agent'){
                        url = "agent_detail_v1.php";
                    } else if(user_type == 'Customer'){
                        url = "members_details.php";
                    } else if(user_type == 'Group'){
                        url = "groups_details.php";  
                    }else if(user_type == 'Lead'){
                        if(res.from_page == 'all_popup') {
                            window.location.href = 'all_activity_popup_leads.php?id=<?=$customer_id?>&user_type=' + res.user_type;
                        } else {
                            url = "lead_details.php";
                        }
                    }
                    // window.opener.location.href = '<?=$AGENT_HOST?>/'+ url +'?id=<?=$customer_id?>';
                    window.onunload = refreshParent(url);
                    window.location.reload();
                    window.close();
                } else if (res.status == 'fail') {
                    $('.error').html('');
                    var is_error = true;
                    $.each(res.errors, function (index, error) {
                        $('#frm_note_add_main #error_' + index).html(error);
                        if (is_error) {
                            var offset = $('#error_' + index).offset();
                            if(typeof(offset) === "undefined"){
                                console.log("Not found : "+index);
                            }else{
                                var offsetTop = offset.top;
                                var totalScroll = offsetTop - 195;
                                $('body,html').animate({
                                    scrollTop: totalScroll
                                }, 1200);
                                is_error = false;
                            }
                        }
                    });
                }
            }
        });

        $('#frm_note_edit_main').ajaxForm({
            beforeSubmit:function(arr, $form, options){
                $("#ajax_loader").show();
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                parent.enableButton($("#edit_post_note"));
                $("#ajax_loader").hide();
                if (res.status == 'success') {
                    window.opener.setNotifySuccess("Note Updated successfully.");
                    if(res.from_page == 'agent_notes') {
                        window.location.href = 'agent_notes.php?id=<?=$customer_id?>&user_type=' + res.user_type;
                    } else {
                        if(res.user_type == 'Admin'){
                            url = "admin_profile.php";
                        } else if(res.user_type == 'Agent'){
                            url = "agent_detail_v1.php";
                        } else if(res.user_type == 'Customer'){
                            url = "members_details.php";
                        } else if(res.user_type == 'Group'){
                            url = "groups_details.php";
                        } else if(res.user_type == 'Lead'){
                            url = "lead_details.php";
                        }
                    }
                    if(!(res.from_page == 'all_popup')){
                        // window.opener.location.href = '<?=$AGENT_HOST?>/'+ url +'?id=<?=$customer_id?>';
                        window.onunload = refreshParent(url);
                        window.location.reload();
                        window.close();
                    }
                } else if (res.status == 'fail') {
                    $('.error').html('');
                    var is_error = true;
                    $.each(res.errors, function (index, error) {
                        $('#frm_note_edit_main #error_' + index).html(error);
                        if (is_error) {
                            var offset = $('#error_' + index).offset();
                            if(typeof(offset) === "undefined"){
                                console.log("Not found : "+index);
                            }else{
                                var offsetTop = offset.top;
                                var totalScroll = offsetTop - 195;
                                $('body,html').animate({
                                    scrollTop: totalScroll
                                }, 1200);
                                is_error = false;
                            }
                        }
                    });
                }
            }
        });
    });

    updateList = function() {
    var input = document.getElementById('note_file');
    var output = document.getElementById('note_file_display');
    // $("#fileList").show();
    // output.innerHTML = '<ul>';
    for (var i = 0; i < input.files.length; ++i) {
        output.value = input.files.item(i).name ;
    }
    // output.innerHTML += '</ul>';
    var field = document.getElementById('fileList');
    // $("#fileList").show();
    field.style.display = "block";
    }

    updateListEdit = function() {
    var input = document.getElementById('note_file_edit');
    var output = document.getElementById('note_file_display_edit');
    // $("#fileList").show();
    // output.innerHTML = '<ul>';
    for (var i = 0; i < input.files.length; ++i) {
        output.value = input.files.item(i).name ;
    }
    // output.innerHTML += '</ul>';
    var field = document.getElementById('fileListedit');
    // $("#fileList").show();
    field.style.display = "block";
    }

    function replay_note(note_id, t) {

        var url = "";
        var user_type = $("#replay_note_id").attr("data-value");
        var customer_id = '<?=checkIsset($_GET['id'])?>';

        $.colorbox({
            iframe: true,
            width: '800px',
            height: '400px',
            href: "account_note.php?id=" + customer_id + "&reply_id=" + note_id + "&type=" + user_type
        });

        }

function refreshParent(url) 
{
    // window.opener.location.reload();
    window.opener.interactionUpdate('<?=$customer_id?>','notes',url,'agents');
}
</script>