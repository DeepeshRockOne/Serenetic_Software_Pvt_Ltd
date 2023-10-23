<div class="panel panel-default panel-block production_report_tbl">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn"> Personal Production Report - <span class="fw300"><?=$agent_name['name']?></span></h4>
      <p class="fs14 mn"><?=isset($agentCodedRes[$agent_name['agent_coded_id']]['level_heading'])?$agentCodedRes[$agent_name['agent_coded_id']]['level_heading']:''?></p>
    </div>
  </div>
  <div class="tabbing-tab mn">
    <ul class="nav nav-tabs tabs customtab" role="tablist">
      <li role="presentation" class="active"><a href="#pro_report_personal" aria-controls="pro_report_personal" role="tab" data-toggle="tab">Personal</a></li>
      <li role="presentation" ><a href="#pro_report_org" aria-controls="pro_report_org" role="tab" data-toggle="tab">Organization</a></li>
      <li role="presentation" ><a href="#pro_report_downine" aria-controls="pro_report_downine" role="tab" data-toggle="tab">Per Downline Agent</a></li>
      <li role="presentation" ><a href="#pro_report_group" aria-controls="pro_report_group" role="tab" data-toggle="tab">Per Group</a></li>
    </ul>
  </div>
  <div class="panel-body">
    <div class="tab-content mn">
      <div role="tabpanel" class="tab-pane active" id="pro_report_personal">
      </div>
      <div role="tabpanel" class="tab-pane " id="pro_report_org">
      </div>
      <div role="tabpanel" class="tab-pane " id="pro_report_downine">
      </div>
      <div role="tabpanel" class="tab-pane " id="pro_report_group">
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    getpersonalproductionReports('agent_report_personal_production.php',"pro_report_personal");
    getpersonalproductionReports('agent_organization_production_report.php',"pro_report_org");
    getpersonalproductionReports('agent_per_agent_production_report.php',"pro_report_downine");
    getpersonalproductionReports('agent_per_group_production_report.php',"pro_report_group");
    autoResizeNav();
});
getpersonalproductionReports = function(report_url,report_div){
    $('#ajax_loader').show();
    $.ajax({
        url: report_url,
        type: 'GET',
        data: {agent_id:'<?=$agent_id?>'},
        success: function (res) {
            $('#ajax_loader').hide();
            $('#'+report_div).html(res);
            $('.production_select').selectpicker('refresh');
        }
    });
}
function autoResizeNav(){
   if ($('.nav-tabs:not(.nav-noscroll)').length){
      ;(function() {
        'use strict';
         $(activate);
         function activate() {
         $('.nav-tabs:not(.nav-noscroll)')
           .scrollingTabs({
               scrollToTabEdge: true,
               enableSwiping: true  
            })
        }
      }());
   }
}
</script>