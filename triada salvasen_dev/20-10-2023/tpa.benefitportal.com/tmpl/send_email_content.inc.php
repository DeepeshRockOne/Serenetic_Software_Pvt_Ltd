<style type="text/css">
  a {
  pointer-events: none;
  cursor: default;
}
@media print {
  .hidden-print {
    display: none !important;
  }
  a[href]:after {
      display: none;
      visibility: hidden;
   }
}
</style>

<div class="panel panel-default panel-block ">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">Email Content </strong> <span class="fw300"><?= (!empty($toEmail))?'('.$toEmail.')':''; ?></span></p>
    </div>
  </div>
  <div class="panel-body">
    <div class="row theme-form">
      <div class="col-sm-6">
        <div class="form-group">
          <input type="text" class="form-control" value="<?= (!empty($subject) ? $subject : '') ?>" readonly="readonly">
          <label>Subject</label>
        </div>
      </div>
    </div>
    <div class="form-group height_auto">
      <?php if(!empty($mailContent)) { ?>
        <div class="thumbnail">
          <?php echo htmlspecialchars_decode($mailContent); ?>
        </div>
      <?php } ?>
    </div>
    <div class="text-center hidden-print">
      <button class="btn red-link" onclick="emailPrint()">Print</button>
      <button class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</button>
    </div>
  </div>
</div>
<script>
function emailPrint(){
  try {
    document.execCommand('print', false, null);
  }
  catch(e) {
    window.print();
  }
}
</script>