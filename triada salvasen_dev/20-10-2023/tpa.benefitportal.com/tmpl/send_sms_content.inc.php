<style type="text/css">
  a {
  pointer-events: none;
  cursor: default;
}
</style>

<div class="panel panel-default panel-block ">
  <div class="panel-heading">
    <div class="panel-title">
      <?php if(!empty($triggerDispId)){ ?>
        <p class="fs18"><strong class="fw500">Trigger - </strong> <span class="fw300"><?=$triggerDispId?></span></p>
      <?php }else{ ?>
        <p class="fs18"><strong class="fw500">SMS Content </strong> <span class="fw300"><?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $toNumber) ?>
      <?php } ?>
    </div>
  </div>
  <div class="panel-body">
    <p><?=$smsContent?></p>
    <div class="text-center">
      <button class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</button>
    </div>
  </div>
</div>