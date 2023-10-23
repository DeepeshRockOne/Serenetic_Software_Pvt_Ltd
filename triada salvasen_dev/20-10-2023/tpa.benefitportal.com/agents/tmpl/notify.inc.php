<?php
$notify_message = "";
$notify_class = "";
if (hasNotify('success')) {
  $notify_class = "success";
  $notify_message = getNotify('success');
} elseif (hasNotify('error')) {
  $notify_class = "danger";
  $notify_message = getNotify('error');
} elseif (hasNotify('alert')) {
  $notify_class = "alert-warning";
  $notify_message = getNotify('alert');
}
?>

<!-- <div id="notification_cont" class="myadmin-alert myadmin-alert-icon myadmin-alert-click myadmin-alert-top alerttop <?= $notify_class ?>" style="display: <?= $notify_message != "" ? 'block' : 'none' ?>;">
  <div id="notification_msg_cont"><?= $notify_message ?></div>
  <a href="#" class="closed">×</a>
</div> -->

<div id="notification_cont" class="card-alert <?= $notify_class ?>" style="display: <?= $notify_message != "" ? 'block' : 'none' ?>;">
  <div class="media">
      <div class="media-left"> <img class="media-object" src="<?php echo $notify_class=='success' ? $HOST.'/images/card_right.svg' : $HOST.'/images/card_close.svg';?> "> </div>
      <div class="media-body">
        <h4 class="media-heading text-uppercase fs18">
          Update Status:<br>
          <?php $notify_class == 'success' ? 'Success!' : 'Failed!'; ?></h4>
        <p class="mn fs16 msg_div" id="notification_msg_cont"><?= $notify_message ?></p>
      </div>
      <a class="media-close" >x</a> </div>
  <!-- <div id="notification_msg_cont"><?= $notify_message ?></div> -->
</div>
<?php if(!empty($notify_class)) { ?>
<script type="text/javascript">
  setTimeout(function(){
    $("#notification_cont").fadeOut('slow');    
  },3000);
  
</script>
<?php } ?>