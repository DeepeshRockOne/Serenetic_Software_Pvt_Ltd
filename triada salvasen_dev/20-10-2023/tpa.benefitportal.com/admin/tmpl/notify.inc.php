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

<div id="notification_cont" class="card-alert <?= $notify_class ?>" style="display: none;">
  <div class="media">
    <div class="media-left"> <img class="media-object" src="<?php echo $notify_class=='success' ? $HOST.'/images/card_right.svg' : $HOST.'/images/card_close.svg';?> "> </div>
    <div class="media-body">
      <h4 class="media-heading ">
      Update Status:<br>
      <?php $notify_class == 'success' ? 'Success!' : 'Failed!'; ?></h4>
      <p class="mn  msg_div" id="notification_msg_cont"><?= $notify_message ?></p>
    </div>
    <a class="media-close" href="javascript:void(0);">x</a>
  </div>
</div>
<?php if(!empty($notify_class)) { ?>
<script type="text/javascript">
  $(document).ready(function() {
    window.parent.setNotify('<?= $notify_class; ?>','<?= $notify_message ?>');
  });
</script>
<?php } ?>