<style>
  #livechat_frame_session table{ width:100% !important;}
  .panel.panel-block .list-group .list-group-item { padding: 15px 12px 20px; }
</style>
<div id="open_chat">
    <div class="comunipart">
      <div class="panel panel-default">
        <div id="pprofile" class="tab-pane list-group active">
          <div class="list-group-item button-demo">
            <?php //include_once 'chat_tabs.inc.php'; ?>
            <div class="clearfix"></div>
             
              <div class="section-title">
               	
                <h4 class="pull-left">Ongoing Chat</h4>
             
              <div class="pull-right">


                  <div class="form-inline">
                    <div class="form-group">
                    	<label class="">Support Agents Online</label>
                      <div class="clearfix"></div>
                      <select id="change_operator" name="change_operator" class="form-control">
                        <?php if (count($operators) > 0) { ?>
                          <?php foreach ($operators as $operator_id => $operator) { ?>
                            <option value="<?php echo $operator_id ?>" <?php echo $agentid == $operator_id ? 'selected' : '' ?>><?php echo $operator ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="form-group">
                    	<label style="display:block">&nbsp;</label>
                      <?php
                      if ($original_row) {
                        echo " : <a href=\"{$original_row['href_link']}\">{$original_row['fname']} {$original_row['lname']} ({$original_row['display_id']})</a>";
                      }
                      $display_closechat = true;
                      /*$display_closechat = true;
                      if ($_SESSION['operator']['operatorid'] != $agentid) {
                        $display_closechat = false;
                      }*/
                      ?>
                      <?php
                      if ($display_closechat) {
                        ?>
                        <a id="close_livechat" href="javascript:void(0)" class="btn btn-info btn-sm" chatclose="yes">Close Chat</a>                    
                      <?php } else { ?>
                        <a id="close_livechat" href="javascript:void(0)" class="btn btn-info btn-sm" chatclose="yes">Close Chat</a>                    
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
                <div class="clearfix"></div>
               


              <iframe src="<?= $_GET['chaturl']; ?>" id="livechat_frame_session" height="450px" width="100%" scrollbar="no" frameborder="0">
              </iframe>
            </div>
          </div>
        </div>
      </div>
    </div>    
</div>      

<script type="text/javascript">

  function load_new_tab(url) {
    new_window = window.open(url, "_blank");
  }

  function close_livechat(status) {
    $.ajax({
      url: 'set_livechat_status.php?status=' + status,
      method: 'POST',
      data: {user_id:<?php echo $chcustid ?>, user_type: '<?php echo $user_type ?>', sub_user_type: '<?php echo $user_sub_type ?>', thread_id: '<?php echo $chats[1] ?>'},
      dataType: 'json',
      success: function(res) {
        if (res.status == "success") {
          opener.location.reload();
          window.close();
        }
      }
    });
  }

  $(document).ready(function() {

    $(document).off("click", "#close_livechat");
    $(document).on("click", "#close_livechat", function(e) {
      e.preventDefault();
      status = $(this).attr('chatclose');
      if ($.trim(status) == "no") {
        close_livechat("no");
      } else if ($.trim(status) == "yes") {
        close_livechat("yes");
      } else {
        window.location.href = 'chat_queue.php';
      }
    });

    $('#change_operator').change(function() {
      if (confirm('Are you sure to assign this chat to another operator ?')) {
        window.location = window.location+'&assign_operator='+$(this).val()+'&threadid=<?php echo $threadid ?>';
      }
    });

    $(".show-description").popover({
      trigger: 'hover',
      title: '',
      content: function() {
        return $(this).next().html();
      }
    });
  });
</script> 
