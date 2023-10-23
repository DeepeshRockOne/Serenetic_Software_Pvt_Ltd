<style type="text/css">
.iframe .dropdown.bootstrap-select .dropdown-menu.open{max-height:75px!important; min-height: 100%!important;}
.iframe .dropdown.bootstrap-select .dropdown-menu .inner.open{max-height:75px!important; min-height: 100%!important;}
</style>
<?php if ($is_ajaxed) { ?>
   <hr>
   <?php if ($total_rows > 0) {?>
    <div class="clearfix m-b-20 tbl_search_filter">
      <div id="commActionBtns" style="display: none;">
        <div class="pull-left responsive_btn">
          <?php if($module_access_type == "rw") { ?>
          <a href="javascript:void(0);" id="approveToPayBtn" class="btn btn-info">Approve To Pay Selected</a>
          <a href="javascript:void(0);" id="denyToPayBtn" class="btn btn-info btn-outline">Deny Selected</a>
          <a href="javascript:void(0);" id="exportCsvBtn" class="btn red-link"><i class="fa fa-download" aria-hidden="true"></i> Export Selected To CSV</a>
          <?php } ?>
        </div>
      </div>
      <div class="pull-right">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
              <div class="form-group mn">
                  <label for="user_type">Records Per Page </label>
              </div>
              <div class="form-group mn">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);pendingCommission();">
                      <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                      <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                      <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                      <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                      <option value="500" <?php echo isset($_GET['pages']) && $_GET['pages'] == 500 ? 'selected' : ''; ?>>500</option>
                  </select>
              </div>
          </div>
      </div>
    </div>
   <?php } ?>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr>
          <?php if($module_access_type == "rw") { ?>
          <th width="7%">
            <div class="checkbox checkbox-custom mn">
              <input type="checkbox" id="payToAllAgentBox" class="js-switch" data-toggle="tooltip" title="Select All"/>
              <label for="payToAllAgentBox">&nbsp;</label>
            </div>
          </th>
          <?php } ?>
          <th>Agent ID/Name</th>
          <th>Earned</th>
          <th>Advanced</th>
          <th>PMPM</th>
          <th>Reversals</th>
          <th>Fees</th>
          <th>Adjustments</th>
          <th>Total</th>
          <th width="90px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
          foreach ($fetch_rows as $rows) { ?>
            <tr>
              <?php if($module_access_type == "rw") { ?>
              <td>
                 <div class="checkbox checkbox-custom mn">
                   <input type="checkbox" name="commission_agents[]" class="js-switch payToSpecAgentBox" id="cs_approve_to_pay_<?=$rows['customer_id']?>" value="<?=$rows['customer_id']?>" />
                  <label for="cs_approve_to_pay_<?=$rows['customer_id']?>">&nbsp;</label>
                </div>
              </td>
              <?php } ?>              
              <td><a href="agent_weekly_com_popup.php?pay_period=<?=$pay_period?>&agent_id=<?=md5($rows['customer_id'])?>&status=<?=$rows['status']?>" class="fw500 text-action agent_weekly_com_popup"><?=$rows["agentRepId"]?></a><br><?php if(!empty($agent_id)){ ?><?=$rows["age_name"]?><?php }else{ ?><?=$rows["agentName"]?><?php } ?></td>
              <td><?=dispCommAmt($rows['earnedComm'])?></td>
              <td><?=dispCommAmt($rows['advanceComm'])?></td>
              <td><?=dispCommAmt($rows['pmpmComm'])?></td>
              <td><?=dispCommAmt($rows['reverseComm'])?></td>
              <td><?=dispCommAmt($rows['feeComm'])?></td>
              <td><?=dispCommAmt($rows['adjustComm'])?></td>
              <td><?=dispCommAmt($rows['totalComm'])?></td>
              <td class="icons">
               <a href="agent_weekly_com_popup.php?pay_period=<?=$pay_period?>&agent_id=<?=md5($rows['customer_id'])?>&status=<?=$rows['status']?>" data-toggle="tooltip" title="View Statement" data-placement="top" class="agent_weekly_com_popup"><i class="fa fa-eye" aria-hidden="true"></i></a>
                <?php if($module_access_type == "rw") { ?>
                <a href="javascript:void(0);" class="exportAgentCSV" data-agent_id="<?=$rows['customer_id']?>" data-toggle="tooltip" title="Export CSV" data-placement="top"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                <?php } ?>
                
              </td>
            </tr>
          <?php } ?>
        <?php } else {?>
          <tr>
            <td colspan="10">No record(s) found</td>
          </tr>
        <?php }?>
      </tbody>
      <?php if ($total_rows > 0) {?>
        <tfoot>
          <tr>
            <td colspan="10"><?php echo $paginate->links_html; ?></td>
          </tr>
        </tfoot>
      <?php }?>
    </table>
  </div>
<?php } else { ?>
  <?php include_once 'notify.inc.php';?>
   <div class="panel panel-default panel-block panel-title-block">
   <form id="pendingCommFrm" action="weekly_pending_commission.php" method="GET" class="sform">
        <input type="hidden" name="pay_period" value="<?=$pay_period?>" />
        <?php if(!empty($agent_id)){ ?>
        <input type="hidden" name="agent_id" value="<?=$agent_id?>" />
        <?php } ?>
        <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
        <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
        <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
        <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
    <div class="clearfix tbl_filter">
      <h4 class="m-t-0 m-b-10">Pending Commissions</h4>
      <div class="pull-left">
        <p class="fs16 fw300">Period - <?=$startPayPeriod?> - <?=$endPayPeriod?></p>
      </div>
      <div class="pull-right">
        <div class="note_search_wrap auto_size" id="search_div" style="display: none; max-width: 100%;">
          <div class="phone-control-wrap theme-form">
            <div class="phone-addon">
              <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
              </div>
            </div>
            <div class="phone-addon">
                 <div class="form-group height_auto mn">
                   <input type="text" class="form-control" name="agentId" value="<?=$agentId?>">
                   <label>Agent ID</label>
                 </div>
            </div>
            <div class="phone-addon">
               <div class="form-group height_auto mn">
                   <input type="text" class="form-control" name="agentName" value="<?=$agentName?>">
                   <label>Principal Agent</label>
               </div>
            </div>
            <div class="phone-addon w-80">
              <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="pendingCommission();">Search</a>
              </div>
            </div>
          </div>
        </div>
        <a href="javascript:void(0)" class="search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
        <?php if($module_access_type == "rw") { ?>
        <a href="commissions_adjustment.php?pay_period=<?=$pay_period?>&commission_duration=<?=$commission_duration?>" class="btn btn-action m-l-5 commissions_adjustment">+ Adjustment</a>
        <?php } ?>        
      </div>
    </div>
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_data" class=""> </div>
    </form>
  </div>
  


  <script type="text/javascript">
    $(document).ready(function() {
      pendingCommission();
      var execute=function(){
            frame_name = 'comm_pending_iframe';
            parent.resizeIframe($("body").height() + 0, frame_name);
            fRefresh();
      }
      dropdown_pagination(execute,'ajax_data')
    
      // Agent Commission Pay Code Start
        $(document).off("click","#payToAllAgentBox");
        $(document).on("click","#payToAllAgentBox",function(){
          if ($('#payToAllAgentBox').is(':checked')) {
            $('.payToSpecAgentBox').prop('checked',true);
            if($('.payToSpecAgentBox').length > 0){
                $("#commActionBtns").show();
            }
          }else {
            $('.payToSpecAgentBox').prop('checked', false);
             $("#commActionBtns").hide();
          }
        });

        $(document).off("click",".payToSpecAgentBox");
        $(document).on("click",".payToSpecAgentBox",function(){
          if ($('.payToSpecAgentBox[type=checkbox]:checked').length > 0){
             $("#commActionBtns").show();
          }else{
            $("#commActionBtns").hide();
          }
        });

        $(document).off("click","#approveToPayBtn");
        $(document).on('click', '#approveToPayBtn', function(e) {
          e.preventDefault();

          var agentIds = $('input:checkbox:checked.payToSpecAgentBox').map(function() {
            return this.value;
          }).get();

          var pay_period='<?=$pay_period;?>';

          var selAgent = $('input:checkbox:checked.payToSpecAgentBox').length;

          if(selAgent > 1){
               window.parent.$.colorbox({href: 'approve_to_pay_commissions.php?commission_duration=weekly&selType=allAgent&pay_period=' + pay_period + '&type=approved_to_pay&agentIds=' + agentIds, width: '750px', height: '500px', iframe: true});
          }else{
               window.parent.$.colorbox({href: 'approve_to_pay_commissions.php?commission_duration=weekly&selType=singleAgent&pay_period=' + pay_period + '&type=approved_to_pay&agentIds=' + agentIds, width: '750px', height: '500px', iframe: true});
          }
        });

        $(document).off("click","#denyToPayBtn");
        $(document).on('click', '#denyToPayBtn', function(e) {
          e.preventDefault();

          var agentIds = $('input:checkbox:checked.payToSpecAgentBox').map(function() {
            return this.value;
          }).get();

          var pay_period='<?=$pay_period;?>';
          
          parent.swal({
            text: '<br>Deny Commissions: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
          }).then(function () {
            $.ajax({
              url: 'ajax_commission_payperiod_operations.php',
              type: 'GET',
              dataType:'JSON',
              data: {action:'denyToPay',agentIds:agentIds,pay_period:pay_period,commission_duration:'weekly'},
              beforeSend: function () {
                $("#ajax_loader").show();
              },
              success: function(res) {
                $("#ajax_loader").hide();
                if(res.status=='success'){
                  parent.swal({
                  text: '<br>Deny Commissions: Successful',
                  showCancelButton: true,
                  showConfirmButton: false,
                  cancelButtonText: 'Close',
                  }).then(function(){
                    window.parent.location.reload();
                  }, function (dismiss){  
                    window.parent.location.reload();
                  });
                }else{
                  parent.swal({
                  text: '<br>Deny Commissions: Failed',
                  showCancelButton: true,
                  showConfirmButton: false,
                  cancelButtonText: 'Close',
                  }).then(function(){
                    window.parent.location.reload();
                  }, function (dismiss){  
                    window.parent.location.reload();
                  });
                }
              }
            });
          }, function (dismiss){  
          });
        });

        $(document).off("click","#exportCsvBtn");
        $(document).on('click', '#exportCsvBtn', function(e) {

          var agentIds = $('input:checkbox:checked.payToSpecAgentBox').map(function() {
            return this.value;
          }).get();
          var pay_period='<?=$pay_period;?>';

          parent.confirm_export_data(function() {
          $('#ajax_loader').show();
            $.ajax({
                url: "commission_export_csv.php",
                type: 'GET',
                data: {"commission_duration":"weekly","agentIds":agentIds,"pay_period":pay_period,"status":"Pending"},
                dataType: 'json',
                success: function(res) {
                    $('#ajax_loader').hide();
                    if(res.status == "success") {
                        parent.confirm_view_export_request();
                    } else {
                        parent.setNotifyError(res.message);
                    }
                }
            });
          });
        });

        $(document).off("click",".exportAgentCSV");
        $(document).on('click', '.exportAgentCSV', function(e) {
            var agentIds = $(this).attr("data-agent_id");
            var pay_period='<?=$pay_period;?>';
          parent.confirm_export_data(function() {
            $('#ajax_loader').show();
            $.ajax({
                url: "commission_export_csv.php",
                type: 'GET',
                data: {"commission_duration":"weekly","agentIds":agentIds,"pay_period":pay_period,"status":"Pending"},
                dataType: 'json',
                success: function(res) {
                    $('#ajax_loader').hide();
                    if(res.status == "success") {
                        parent.confirm_view_export_request();
                    } else {
                        parent.setNotifyError(res.message);
                    }
                }
            });
          });
        });

      // Agent Commission Pay Code Ends

      $(document).off("click", ".search_btn");
      $(document).on("click", ".search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#search_div").css('display', 'inline-block');
        frame_name = 'comm_pending_iframe';
        parent.resizeIframe($("body").height() + 0, frame_name);
      });

      $(document).off("click", ".search_close_btn");
      $(document).on("click", ".search_close_btn", function(e) {
        e.preventDefault();
        $("#search_div").hide();
        $(".search_btn").show();
        window.location.reload();
        frame_name = 'comm_pending_iframe';
        parent.resizeIframe($("body").height() + 0, frame_name);
      });

      $(document).off("click", ".commissions_adjustment");
      $(document).on("click", ".commissions_adjustment", function(e) {
        e.preventDefault();
        $href = $(this).attr("href");
        window.parent.$.colorbox({href:$href,iframe: true, width: '515px', height: '565px'});
      });

      $(document).off("click", ".agent_weekly_com_popup");
      $(document).on("click", ".agent_weekly_com_popup", function(e) {
        e.preventDefault();
        $href = $(this).attr("href");
        window.parent.$.colorbox({href:$href,iframe: true, width: '85%', height: '565px'});
      });

   

      $(document).off('click', '#ajax_data ul.pagination li a');
      $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
            frame_name = 'comm_pending_iframe';
            parent.resizeIframe($("body").height() + 0, frame_name);
            common_select();
            fRefresh();
          }
        });
      });
    });

    function pendingCommission() {
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#is_ajaxed').val('1');
      var params = $('#pendingCommFrm').serialize();
      $.ajax({
        url: $('#pendingCommFrm').attr('action'),
        type: 'GET',
        data: params,
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          frame_name = 'comm_pending_iframe';
          parent.resizeIframe($("body").height() + 0, frame_name);
          common_select();
          fRefresh();
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
      return false;
    }
  </script>
<?php }?>