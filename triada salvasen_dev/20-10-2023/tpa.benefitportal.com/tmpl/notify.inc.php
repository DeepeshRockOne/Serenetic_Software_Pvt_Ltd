<?php
$notify_message = "";
$notify_class = "";
if (hasNotify('success')) {
  $notify_class = "alert-success";
  $notify_message = getNotify('success');
} elseif (hasNotify('error')) {
  $notify_class = "alert-danger";
  $notify_message = getNotify('error');
} elseif (hasNotify('alert')) {
  $notify_class = "alert-warning";
  $notify_message = getNotify('alert');
}
?>

<div id="notification_cont" class="myadmin-alert myadmin-alert-icon myadmin-alert-click myadmin-alert-top alerttop <?= $notify_class ?>" style="display: <?= $notify_message != "" ? 'block' : 'none' ?>;">
  <div id="notification_msg_cont"><?= $notify_message ?></div>
  <a href="#" class="closed">×</a>
</div>
<?php if(!empty($notify_class)) { ?>
<script type="text/javascript">
  setTimeout(function(){
    $("#notification_cont").fadeOut('slow');    
  },3000);
  
</script>
<?php } ?>