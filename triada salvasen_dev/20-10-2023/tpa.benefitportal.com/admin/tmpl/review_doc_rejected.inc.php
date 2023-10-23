<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn">Documentation - <span class="fw300">Rejected</span> </h4>
    </div>
  </div>
  <form action="" method="post">
  <input type="hidden" name="agent_id" value="<?=$agent_id?>">
  <input type="hidden" name="contract_status" value="<?=$contract_status?>">
  <div class="panel-body">
  <p class="fs16">Reason for submission being rejected…</p>
    <textarea  class="summernote" name="reject_msg" id="reject_msg">
      Your documentation submission needs revision, please fix the following:
    </textarea>
    <?php if (isset($errors['reject_msg'])): ?>
      <ul class="parsley-error-list">
        <li class="error">Please Enter Reason For Documentation Reject.</li>
      </ul>
    <?php endif;?>
    <div class="text-center m-t-20">
      <input type="submit" value="Send" name="save" onclick="$('#ajax_loader').show();" class="btn btn-action" />
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
    </div>
  </div>
  </form>
</div>

<script type="text/javascript">
$(document).ready(function() {
  initCKEditor("reject_msg",false,"200px");
});
</script>