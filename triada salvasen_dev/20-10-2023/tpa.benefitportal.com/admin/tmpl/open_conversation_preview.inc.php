<?php if($is_ajaxed_get_data) { ?>
<div id="tab_conversation" class="panel panel-default panel-block info">
<div class="panel-heading">
  <a data-href="open_conversation_preview.php?s_ticket_id=<?=$s_ticket_id?>&view=1" class="open_conversation_preview"><i class="fa fa-external-link" aria-hidden="true"></i></a>
</div>
<div class="panel-body">
  <div class="row">
    <div class="col-sm-6">
      <div class="blue_arrow_tab ">
        <ul class="nav nav-tabs nav-noscroll">
          <li class="active"><a data-toggle="tab" class="tabs_collapse" href="#Public_Reply">Public Reply</a></li>
          <li><a data-toggle="tab" class="tabs_collapse" href="#Internal_Note">Internal Note</a></li>
        </ul>
      </div>
      <div class="tab-content">
        <div id="Public_Reply" class="tab-pane fade in active theme-form">
          <form id="public_reply_form" name="public_reply_form"  class="ticketform" enctype='multipart/form-data' action="open_conversation_preview.php">
            <input type="hidden" name="s_ticket_id" value="<?=$s_ticket_id?>">
            <input type="hidden" name="is_sendEmail" value="1">
            <input type="hidden" name="is_ajaxed" value="1">
            <input type="hidden" name="reply_type" value="reply">
            <input type="hidden" name="view" value="<?=checkIsset($_REQUEST['view'])?>">
            <div class="form-group height_auto p-t-10">
              <textarea class="form-control radius-zero summernote" rows="7"  id="description_public" name="content" placeholder="Content added here will be sent to account holder…"></textarea>
              <p class="error error_description_public"></p>
              <div class="eticket_note_footer clearfix">
                <div class="phone-control-wrap">
                  <div class="phone-addon w-30">
                    <input type="file" name="docFile" id="docFile" value="" class="hidden" onchange="$('#docFilelabel').text($(this).val());$('#removeFile').show()">
                    <a href="javascript:void(0);" class="text-light-gray fs18 " onclick="$('#docFile').click()"><i class="fa fa-paperclip fa-rotate-90" aria-hidden="true"></i>
                    </a>
                  </div>
                  <div class="phone-addon">
                    <div class="form-group height_auto">
                      <select class="form-control" id="quick_reply" name="quick_reply" onchange="getdescription($(this).val())">
                        <option value=""></option>
                        <?php if(!empty($quick_reply)){
                          foreach($quick_reply as $reply){ ?>
                            <option value="<?=$reply['id']?>"><?=$reply['title']?></option>
                          <?php }
                        } ?>                          
                      </select>
                      <label>Quick Reply</label>
                    </div>
                  </div>
                  <div class="phone-addon  w-90 text-right">
                    <a href="javascript:void(0);" id="savEticket" class="btn btn-info savEticket"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</a>
                    <!-- <a href="javascript:void(0);" class="btn btn-action" id="savEticket">Save</a> -->
                  </div>
                </div>
                   <div>
                     <label id="docFilelabel"></label><a href="javascript:void(0)" id="removeFile" style="display:none" onclick="$('#docFile').val('');$(this).hide();$('#docFilelabel').text('')">&nbsp;&nbsp;<i class='fa fa-times-circle'></i></a>
                    <p class="error error_docFile"></p>
                  </div>
              </div>
            </div>
          </form>
              <table cellpadding="0" cellspacing="0" width="100%">
                <tbody>
                  <tr>
                    <td><strong>E-Ticket #:</strong></td>
                    <td><?=$rows['tracking_id']?></td>
                  </tr>
                  <tr>
                    <td><strong>Requester:</strong></td>
                    <?php
                      $link_url = '';
                      if($rows['user_type'] == 'Agent'){
                        $link_url = $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($rows['user_id']);
                      }elseif($rows['user_type'] == 'customer'){
                        $link_url = $ADMIN_HOST.'/members_details.php?id='.md5($rows['user_id']);
                      }elseif($rows['user_type'] == 'group'){
                        $link_url = $ADMIN_HOST.'/groups_details.php?id='.md5($rows['user_id']);
                      }else if($rows['user_type'] == 'Admin'){
                        $link_url = $ADMIN_HOST.'/admin_profile.php?id='.md5($rows['user_id']);
                      }
                    ?>
                    <td><?=$rows['name']?><a href="<?= $link_url ?>" target="_blank"> <span class="red-link"><?=$rows['rep_id']?> </span></a></td>
                  </tr>
                  <tr>
                    <td><strong>Subject:</strong></td>
                    <td><?=$rows['subject']?></td>
                  </tr>
                </tbody>
              </table>
        </div>
        <div id="Internal_Note" class="tab-pane fade theme-form">
          <form action="open_conversation_preview.php" id="notes_form" name="notes_form" class="ticketform"  enctype='multipart/form-data'>
              <input type="hidden" name="s_ticket_id" value="<?=$s_ticket_id?>">
              <input type="hidden" name="is_sendEmail" value="1">
              <input type="hidden" name="is_ajaxed" value="1">
              <input type="hidden" name="reply_type" value="notes">
              <input type="hidden" name="view" value="<?=checkIsset($_REQUEST['view'])?>">
            <div class="form-group height_auto p-t-10">
              <textarea class="form-control radius-zero summernote" name="content_temp" rows="12" id="description_note" placeholder='Content added here will be for internal use only…',></textarea>
              <p class="error error_description_note"></p>
              <div class="eticket_note_footer clearfix">
                <div class="phone-control-wrap">
                  <div class="phone-addon w-30 text-left">
                    <input type="file" name="docFile" id="docFileNote" value="" class="hidden" onchange="$('#docFilelabelNote').text($(this).val());$('#removeFileNote').show()">
                      <a href="javascript:void(0);" class="text-light-gray fs18 " onclick="$('#docFileNote').click()"><i class="fa fa-paperclip fa-rotate-90" aria-hidden="true"></i>
                      </a>
                  </div>
                  <div class="phone-addon w-90 text-right">
                    <a href="javascript:void(0);" class="btn btn-action savEticket" id="saveNote">Save Note</a>
                    <a href="javascript:void(0);" class="btn red-link pn" style="background-color: transparent;">Cancel</a>
                  </div>
                </div>
                <div>
                  <label id="docFilelabelNote"></label><a href="javascript:void(0)" id="removeFileNote" style="display:none" onclick="$('#docFileNote').val('');$(this).hide();$('#docFilelabelNote').text('')">&nbsp;&nbsp;<i class='fa fa-times-circle'></i></a>
                  <p class="error error_docFileNote"></p>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="conversation_wrap">
        <h4 class="m-t-m fs14">Conversation</h4>
        <ul class="nav nav-tabs tabs customtab nav-noscroll">
          <li role="presentation" class="active"><a href="#all_conversation" aria-controls="all_conversation" role="tab" data-toggle="tab" onclick="scrollTobottom()">All (<?= !empty($replys) ? count($replys) : 0 ?>)</a></li>
          <li role="presentation"><a href="#public_conversation" aria-controls="public_conversation" role="tab" data-toggle="tab" onclick="scrollTobottom()">Public (<?=$total_reply['replys']?>)</a></li>
          <li role="presentation"><a href="#notes_conversation" aria-controls="notes_conversation" role="tab" data-toggle="tab" onclick="scrollTobottom()">Notes (<?=$total_notes['notes']?>)</a></li>
        </ul>
        <div class="tab-content">
          <div id="all_conversation" class="tab-pane fade in active" style="overflow:auto;">
            <div class="conversation_activity" style="max-height:290px;">
              <ul class="timeline left-timeline">
              <?php if(!empty($replys)) {
                foreach($replys as $rp){ ?>
                  <li class="timeline-inverted">
                    <div class="timeline-badge <?=$rp['type']=='reply' ? 'info' : 'danger'?>"> <i class="type-e"></i> </div>
                    <div class="timeline-panel">
                      <div class="timeline-heading">
                        <?php
                          $nameArr = explode('_',$rp['name']);
                          $name = $nameArr[0];
                          $rep_id = $nameArr[1];
                        ?>
                        <h4 class="timeline-title text-<?=$rp['type']=='reply' ? 'info' : 'danger'?>"> <?=$name?> - <a href="javascript:void(0);" class="blue-link"><?=$rep_id?></a> </h4>
                        <p> <small class="text-muted"><?=$tz->getDate($rp['created_at'],'D., M. d, Y @ h:i A')?></small> </p>
                      </div>
                      <div class="timeline-body">
                        <?=htmlspecialchars_decode(nl2br($rp['message']))?><br>
                        <?php if(!empty($rp['file_name']) && file_exists($ETICKET_DOCUMENT_DIR . checkIsset($rp['file']))){ ?>
                          <p><a href="<?= $ETICKET_DOCUMENT_WEB . $rp['file']; ?>" title="View Document" target='_blank'><i class="fa fa-download"></i> <?=$rp['file_name']?> </a></p>  
                        <?php } ?>
                      </div>
                    </div>
                  </li>
                <?php } } ?>
              </ul>
            </div>
          </div>
          <div id="public_conversation" class="tab-pane fade">
            <div class="conversation_activity" style="max-height:290px;">
              <ul class="timeline left-timeline">
                <?php if(!empty($replys)) {
                  foreach($replys as $rp){ 
                      if($rp['type'] == 'reply'){
                    ?>
                    <li class="timeline-inverted">
                      <div class="timeline-badge info"> <i class="type-e"></i> </div>
                      <div class="timeline-panel">
                        <div class="timeline-heading">
                          <?php
                            $nameArr = explode('_',$rp['name']);
                            $name = $nameArr[0];
                            $rep_id = $nameArr[1];
                          ?>
                          <h4 class="timeline-title text-info"> <?=$name?> - <a href="javascript:void(0);" class="blue-link"><?=$rep_id?></a> </h4>
                          <p> <small class="text-muted"><?=$tz->getDate($rp['created_at'],'D., M. d, Y @ h:i A')?></small> </p>
                        </div>
                        <div class="timeline-body">
                          <p><?=htmlspecialchars_decode(nl2br($rp['message']))?></p>
                          <?php if(!empty($rp['file_name']) && file_exists($ETICKET_DOCUMENT_DIR . checkIsset($rp['file']))){ ?>
                            <p><a href="<?= $ETICKET_DOCUMENT_WEB . $rp['file']; ?>" title="View Document" target='_blank'><i class="fa fa-download"></i> <?=$rp['file_name']?> </a></p>  
                          <?php } ?>
                          
                        </div>
                      </div>
                    </li>
                  <?php } } } ?>
              </ul>
            </div>
          </div>
          <div id="notes_conversation" class="tab-pane fade">
            <div class="conversation_activity" style="max-height:290px;">
              <ul class="timeline left-timeline">
                <?php if(!empty($replys)) {
                    foreach($replys as $rp){ 
                        if($rp['type'] == 'notes'){
                      ?>
                      <li class="timeline-inverted">
                        <div class="timeline-badge danger"> <i class="type-e"></i> </div>
                        <div class="timeline-panel">
                          <div class="timeline-heading">
                            <?php
                              $nameArr = explode('_',$rp['name']);
                              $name = $nameArr[0];
                              $rep_id = $nameArr[1];
                            ?>
                            <h4 class="timeline-title text-danger"> <?=$name?> - <a href="javascript:void(0);" class="blue-link"><?=$rep_id?></a> </h4>
                            <p> <small class="text-muted"><?=$tz->getDate($rp['created_at'],'D., M. d, Y @ h:i A')?></small> </p>
                          </div>
                          <div class="timeline-body">
                            <p><?=htmlspecialchars_decode(nl2br($rp['message']))?></p>
                          </div>
                        </div>
                      </li>
                  <?php } } } ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script type="text/javascript">
//$(document).ready(function(){
  
  $(document).off('click','#savEticket');
  $(document).on('click','#savEticket',function(){
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    $('#public_reply_form').submit();
  });

  $(document).off('click','#saveNote');
  $(document).on('click','#saveNote',function(){
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    $('#notes_form').submit();
  });

  $('.ticketform').ajaxForm({
    beforeSend: function(e) {
        $("#ajax_loader").show();
        
        // $("#description_public").val(CKEDITOR.instances.description_public.getData());
        // $("#description_note").val(CKEDITOR.instances.description_note.getData());
    },
    beforeSubmit:function(arr, $form, options){
    },
    url:"<?= $ADMIN_HOST ?>/open_conversation_preview.php",
    type: 'post',
    dataType: 'json',
    success: function(res) {
      $(".error").html("").hide();
        $("#ajax_loader").hide();
        if(res.status == 'success'){
          
          if(res.view == 'refresh'){
            location.reload();
            setNotifySuccess(res.msg);
          } else {
            setNotifySuccess(res.msg);
            getConversionData();
          }

          if(typeof(res.updated_at) !== "undefined") {
              $(".updated_at_" + res.s_ticket_id).html(res.updated_at);
          }
        }else if(res.status == 'fail'){
          setNotifyError(res.msg);
        }else{
          $.each(res.errors, function(key, value) {
            $('.error_' + key).html(value).show();
          })
        }
    }
  });
// });  

</script>
<?php }else{ ?>
<input type="hidden" name="view" id="view" value=<?=checkIsset($view)?>>
<div class="open_conversation" id="get_conversion_data">
</div>
<script type="text/javascript">
$(document).on('click', '.tabs_collapse', function(e) {
e.preventDefault(); 
  var this_val = $(this).attr('href');
  if (this_val == '#Public_Reply') {
    $('#tab_conversation').removeClass('danger').addClass('info');
    }else if(this_val == '#Internal_Note'){
    $('#tab_conversation').removeClass('info').addClass('danger'); 
    } 
});

scrollTobottom = function(){
  setTimeout(function(){
    $(".conversation_activity").mCustomScrollbar({
    theme:"dark",
    setTop:"-999999px",
    mouseWheelPixels:400
  }).mCustomScrollbar("scrollTo","bottom",{scrollInertia:0});
  }, 200);
 
}

getConversionData = function(){
  $.ajax({
    url:'open_conversation_preview.php',
    data:{
      s_ticket_id:'<?=$s_ticket_id?>',
      is_ajaxed_get_data:1,
      view:$("#view").val(),
    },
    type:'post',
    beforeSend : function(e){
      $("#ajax_loader").show();
    },
    success : function(res){
      $("#ajax_loader").hide();
      $("#get_conversion_data").html(res);
      common_select();
      $(".conversation_activity").mCustomScrollbar({
          theme:"dark",
          setTop:"-999999px",
          mouseWheelPixels: 400 
      });
      initCKEditor("description_public",false,"140px");
      initCKEditor("description_note",false,"140px");
    //   $('#description_public').summernote({
    //   toolbar: [],
    //   disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
    //   focus: true, // set focus to editable area after initializing summernote
    //   height:140,
    //   placeholder:'Content added here will be sent to account holder…',
    //   callbacks: {
    //     onImageUpload: function(image) {
    //       editor = $(this);
    //       uploadImageContent(image[0], editor);
    //     },
    //     onMediaDelete : function(target) {
    //         deleteImage(target[0].src);
    //         target.remove();
    //     }
    //   }
    // });
    //   $('#description_note').summernote({
    //   toolbar: [],
    //   disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
    //   focus: true, // set focus to editable area after initializing summernote
    //   height:140,
    //   placeholder:'Content added here will be for internal use only…',
    //   callbacks: {
    //     onImageUpload: function(image) {
    //       editor = $(this);
    //       uploadImageContent(image[0], editor);
    //     },
    //     onMediaDelete : function(target) {
    //         deleteImage(target[0].src);
    //         target.remove();
    //     }
    //   }
    // });
    }
  });
}
$(document).ready(function(){
  getConversionData();
});

$(document).off('click',".open_conversation_preview");
$(document).on('click',".open_conversation_preview",function(e){
    var not_win = '';
    $href = $(this).attr('data-href');
    var not_win = window.open($href, "myWindow", "width=1155,height=525");
    if(not_win.closed) {  
      alert('closed');  
    } 
});

getdescription = function(id){
  $.ajax({
    url:'open_conversation_preview.php',
    data:{reply_id:id,is_ajaxed:1,is_description:1},
    dataType:'json',
    type:'post',
    beforeSend : function(e){
      $("#ajax_loader").show();
    },
    success : function(res){
      $("#ajax_loader").hide();
      if(res.status == 'success'){
        $('#description_public').val(res.description);
      }else{
        $('#description_public').val('');
      }
    }
  });
}
</script>
<?php } ?>