<?php include_once('notify.inc.php'); ?>
<style type="text/css">
  .col-lg-12, .col-lg-6, .col-lg-3 { padding: 0px;}
  /*.noEdit .form-control { background: none; border: none;}*/
  /*.noEdit .select2-container .select2-choice { background: none; border: none;}*/
  .noEdit .form-control {background: #F9F9F9; cursor: not-allowed; color: #CECECE;}
  .noEdit .select2-container .select2-choice { background: #F9F9F9; cursor: not-allowed; color: #CECECE;}
  .fEdit .form-control {background: #fff; cursor: default; color: #555555;}
  .fEdit .select2-container .select2-choice { background: #fff; cursor: inherit; color: #555555;}
  .noEdit .readonly,.fEdit .readonly {background: #F9F9F9; cursor: not-allowed; color: #CECECE;}
  a.gsch_page {
    padding: 10px 10px !important;
}
 /* #ajex_loader { position: fixed; text-align: center; top:0px; z-index:100; width:100%; height:100%; background:url(images/white_trans.png) repeat; border-bottom:1px solid #3b7b3b;}
  .loader { display:inline-block; width:66px; height:66px; background:url(images/Loading1.gif) no-repeat; position:relative; top:30%;}*/
</style>  
<div id="ajex_loader" style="display:none;">
  <div class="loader"></div>
</div>
<div class="searchdiv"> 
  <div> 
    <?php if ($total_admins > 0 || $total_ambassadors > 0 || $total_agents > 0 || $total_agents > 0 || $total_call_center > 0 || $total_groups > 0 || $total_customers > 0 || $total_organizations > 0 || $total_leads > 0) { ?>
      <div class="preface">
      	<div class="tabbing-tab">
       		 <ul class="nav nav-tabs customtab" id="gschtab">				
         		 <?php //if (has_menu_access(34)) { ?>
              <li><a class="gsch_page" href="global_admins.php">Admins (<?= $total_admins; ?>)</a></li>
              <li><a class="gsch_page" href="global_ambassadors.php">Ambassadors (<?= $total_ambassadors; ?>)</a></li>
              <li><a class="gsch_page" href="global_agents.php">Agents (<?= $total_agents; ?>)</a></li>
              <li><a class="gsch_page" href="global_call_center.php">Call Center (<?= $total_call_center; ?>)</a></li>
              <li><a class="gsch_page" href="global_groups.php">Groups (<?= $total_groups; ?>)</a></li>
              <li><a class="gsch_page" href="global_leads.php">Leads (<?= $total_leads; ?>)</a></li>
              <li><a class="gsch_page" href="global_customers.php">Members (<?= $total_customers; ?>)</a></li>
              <li><a class="gsch_page" href="global_providers.php">Providers (<?= $total_providers; ?>)</a></li>
              <li><a class="gsch_page" href="global_organizations.php">Organizations (<?= $total_organizations; ?>)</a></li>
          		<!-- <li class=""><a class="gsch_page" href="global_monthly_commissions.php">Monthly Commissions (<?= $total_monthly_commission; ?>)</a></li>-->
        </ul>
        </div>
        <div class="tab-content m-t-0 panel panel-default panel-block" >
        	<div class="panel-body">
            <div id="global_search_result" class="tab-pane list-group active m-b-0">
            </div>
          </div>
        </div>
      </div>
<?php } else { ?>
      <div class="panel panel-default panel-block">
        <div class="list-group">
          <div class="list-group-item button-demo" id="sparkline-boxes">
            <h4><i class="icon-warning-sign"></i> &nbsp; No Record(s).</h4>
          </div>
        </div>
      </div>
<?php } ?>

  </div>
</div>
<script type="text/javascript">

  $(document).ready(function() {
    $('#grefresh').click(function() {
      window.location = 'dashboard.php';
    });
    $('#grefresh').show();
    $('.gsch_page, #global_search_result .live-link a').die('click');
    $('.gsch_page, #global_search_result .live-link a').live('click', function(e) {
      $("#ajex_loader").fadeIn('1500');
      e.preventDefault();
      var obj = this;
      var gsearch = '<?=$_GET['gsearch']?>';
      $.ajax({
        url: this.href,
        type: 'GET',
        data: {gsearch: gsearch},
        success: function(res) {
          if ($(obj).hasClass('gsch_page')) {
            $('#gschtab li.active').removeClass('active');
            $(obj).parent().addClass('active');
          }
          $("#ajex_loader").fadeOut('1500');
          $('#global_search_result').html(res).fadeIn('slow');
        }
      });
    });

    if ($('.gsch_page').size() > 0) {
      $('.gsch_page:first').click();
    }
  });
</script>