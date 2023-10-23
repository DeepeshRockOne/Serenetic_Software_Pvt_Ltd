<style type="text/css">
  @media screen{
    .bg_light_gray{max-height: 375px;}
  }
   @media print {
    .bg_light_gray{background: #ffffff!important; max-height: 100%!important; overflow: none!important;}
  }
</style>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">Definitions - <span class="fw300"><?= $report_row['report_name']?></span></h4>
    </div>
  </div>
  <div id="printableArea">
  <div class="panel-body">
    <p class="fw500 text-uppercase">Purpose Summary</p>
    <p class="mn"><?= $report_row['purpose_summary']?></p>
  </div>
  <div class="bg_light_gray panel-body" id="report_info_scroll">
      <p class="fw500 text-uppercase">Definitions</p>
      <?= htmlspecialchars_decode($report_row['definitions'])?>
  </div>
</div>
  <div class="m-t-10 text-center">
    <input type="button" class="btn btn-action" onclick="printDiv('printableArea')" value="Print" />
    <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Close</a>
  </div>
</div>
<script type="text/javascript">
$(document).ready (function (){
    $('#report_info_scroll').slimScroll({
        height: '100%'
    });
});
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>