<div class="panel panel-default panel-block production_report_tbl">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn"> Personal Production Report - <span class="fw300"><?=$resGroup['business_name']?></span></h4>
    </div>
  </div>
  <div class="tabbing-tab mn">
    <ul class="nav nav-tabs tabs customtab nav-noscroll" role="tablist">
      <li role="presentation" class="active"><a href="#pro_report_group" aria-controls="pro_report_group" role="tab" data-toggle="tab">Per Group</a></li>
    </ul>
  </div>
  <div class="panel-body">
    <div class="tab-content mn">
      <div role="tabpanel" class="tab-pane active" id="pro_report_group">
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    getpersonalproductionReports('agent_per_group_production_report.php',"pro_report_group");
});
getpersonalproductionReports = function(report_url,report_div){
    $('#ajax_loader').show();
    $.ajax({
        url: report_url,
        type: 'GET',
        data: {
          groups_id:'<?=$group_id?>',
          agent_id:'<?=$agent_id?>',
        },
        success: function (res) {
            $('#ajax_loader').hide();
            $('#'+report_div).html(res);
            
        }
    });
}
</script>