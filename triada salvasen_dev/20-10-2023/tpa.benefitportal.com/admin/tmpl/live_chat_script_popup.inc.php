<div class="panel panel-default panel-block">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">
         Script ID -  <span class="fw300">(<?=$con_data['details']['display_id']?>)</span>
         </h4>
      </div>
   </div>
   <div class="panel-body">
      <div class="chat_wrapper live_chat_circle">
         <div class="chat_right <?= $chatClass ?>" id="chat_window_scroll"  style="max-height: 370px; padding: 0px 15px 0px 0px;">
            <div class="chat_window script_chat" >
               <div id="messageDiv" >
                  <?php
                    if(!empty($con_data['messages'])) {
                        foreach ($con_data['messages'] as $message_row) {
                          $tmpString = $message_row['first_name'] .' '. $message_row['last_name']; 
                          $tmpExplode = explode(" ", $tmpString);
                  
                          $user_short_name = strtoupper(substr($tmpExplode[0], 0, 1).substr($tmpExplode[1], 0, 1));

                          if($message_row['user_type'] == "admin") {
                              $userName=$message_row['first_name'];
                              $chatReceiver = "";
                          }else{
                              $userName=$message_row['first_name'].' '.$message_row['last_name'];
                              $chatReceiver = "recevier_msg";
                          }

                          $supported_image = array(
                              'gif',
                              'jpg',
                              'jpeg',
                              'png'
                          );

                          
                          ?>
                          <div class="chat_msg_div <?= $chatReceiver ?>">
                            <div class="chat-name-circle online"><?= $user_short_name ?></div>
                            <div class="chat-messge">
                              <p class="chat-name"><?= $userName ?></p>
                              <p> <?=nl2br($message_row['message']);?> </p>
                              <div class="sb-message-attachments">
                                <?php if(!empty($message_row['attachments'])) { 
                                   $attachments = json_decode($message_row['attachments'],true);
                                   if(!empty($attachments)) {
                                     foreach ($attachments as $key => $value) {
                                        $url = $value[1];
                                        $src_file_name = $value[0];
                                        $ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION));
                                        if (in_array($ext, $supported_image)) { ?>
                                            <div class="sb-image"><img src="<?= $url ?>" /></div>
                                        <?php } else { ?>
                                            <a target="_blank" href="<?= $url ?>"><?= $src_file_name ?></a> </br>
                                        <?php }
                                       
                                     }
                                   }
                                } ?>
                              </div>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                          <?php
                        }
                    }
                  ?>
               </div>
            </div>
          </div>
      </div>
   </div>
   <div class="text-center p-t-20 p-b-20">
      <a href="javascript:void(0);" class="btn red-link pn" onclick='parent.$.colorbox.close(); return false;'>Close</a>
   </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {   
    $("#chat_window_scroll").mCustomScrollbar({
        theme: "dark",
        scrollbarPosition: "outside",
    });
  });
</script>