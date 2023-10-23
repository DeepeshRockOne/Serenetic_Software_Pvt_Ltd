<?php
if(empty($agent_id)){
if($module_access_type == "rw") { ?>
<div class="row  commissions_tab_wrap">
  <div class="col-md-6">
    <div class="email_tab_box ">
      <a href="commissions_payables.php" class="btn btn-action text-left">Commission Payables<br>
        <span class="fs14 fw300">This section is where you create NACHA/CSV payment files, view previous exports, and agent specific debit/credits.</span>
      </a>
    </div>
  </div>
  <div class="col-md-6">
    <div class="email_tab_box">
      <a href="regenerate_commissions.php" class="btn btn-info text-left">Regenerate<br>
        <span class="fs14 fw300">This section is where you regenerate commissions and view previous regenerations.</span>
      </a>
    </div>
  </div>
</div>
<?php } ?>

<!-- Commission Summary Code Start -->
<form id="commSummaryFrm" method="GET" class="theme-form" autocomplete="off">
  <div class="dashboard_topbar clearfix pr">
      <div class="row">
          <div class="col-lg-4 col-md-3 ">
              <p class="fs18 mn text-action fw500">New Business Commissions</p>
          </div>
          <div class="col-lg-8 col-md-9 dashboard_topbar_right top_header_section">
              <div class="dash_top_counter">
                  <p class="fs14 text-light-gray text-uppercase mn">Total Commissions</p>
                  <p class="fw600 text-action totalCommAmt"></p>
              </div>
              <div class="dash_top_counter">
                  <p class="fs14 text-light-gray text-uppercase mn">Total Credits</p>
                  <p class="fw600 text-action totalCredits"></p>
              </div>
              <div class="dash_top_counter">
                  <p class="fs14 text-light-gray text-uppercase mn">Total Debits</p>
                  <p class="fw600 text-action totalDebits"></p>
              </div>
              <div class="dash_top_counter">
                  <a href="javascript:void(0);" class="btn btn-info pull-right" id="btn_select_date"><?=date('m/d/Y')?></a>
              </div>
          </div>
      </div>
      <div class="custom-data-wrap theme-form" id="custom-date-toggle" style="display: none;">
        <div id="date_range" class="col-md-12">
            <div class="form-group  m-b-15">
                <select class="form-control" id="join_range" name="join_range">
                    <option value=""></option>
                    <option value="Range">Range</option>
                    <option value="Exactly" selected>Exactly</option>
                    <option value="Before">Before</option>
                    <option value="After">After</option>
                </select>
                <label>Date Type</label>
                <p class="error error_join_range"></p>
            </div>
        </div>
        <div class="select_date_div col-md-9" style="display:none">
            <div class="form-group  mn">
                <div id="all_join">
                    <div class="phone-control-wrap">
                        <div class="phone-addon">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <div class="pr">
                                    <input type="text" name="added_date" id="added_date" value="<?=date('m/d/Y')?>" class="form-control date_picker"/>
                                    <label>Date</label>
                                </div>
                            </div>
                            <p class="error error_added_date text-left"></p>
                        </div>
                        <div class="phone-addon w-65">
                            <button type="button" class="btn btn-action btn_set_date">Submit</button>
                        </div>
                    </div>
                </div>
                <div id="range_join" style="display:none;">
                    <div class="phone-control-wrap">
                        <div class="phone-addon">
                            <label class="mn">From</label>
                        </div>
                        <div class="phone-addon">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <div class="pr">
                                    <input type="text" name="fromdate" id="fromdate" value="<?=date('m/d/Y')?>" class="form-control date_picker"/>
                                    <label>Date</label>
                                </div>
                            </div>
                            <p class="error error_fromdate text-left"></p>
                        </div>
                        <div class="phone-addon">
                            <label class="mn">To</label>
                        </div>
                        <div class="phone-addon">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <div class="pr">
                                    <input type="text" name="todate" id="todate" value="<?=date('m/d/Y')?>" class="form-control date_picker"/>
                                    <label>Date</label>
                                </div>
                            </div>
                            <p class="error error_todate text-left"></p>
                        </div>
                        <div class="phone-addon w-65">
                            <button type="button" class="btn btn-action btn_set_date">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-body">
      <div class="theme-form">
        <div class="row">
          <div class="col-md-6">
            <div class="m-b-20 pr">
            <div class="phone-control-wrap">
              <div id="agency_div" class="phone-addon text-left">
                <select class="se_multiple_select" name="agency_ids[]" multiple="multiple" id="agencySel">
                  <option value="<?=$resAgent['agentId']?>" selected="selected"><?=$resAgent["agentDispId"]?> - <?=!empty($resAgent["company_name"])?$resAgent["company_name"]:$resAgent["agentName"]?></option>
                  <?php if(!empty($resCommAgency)){
                    foreach ($resCommAgency as $agent) {
                  ?>
                    <option value="<?=$agent["agentId"]?>" <?=$agent["agentId"]==1 ? "selected='selected'" : ""?>><?=$agent["agentDispId"]?> - <?=$agent["agentName"]?></option>
                  <?php
                    }
                  }
                  ?>
                </select>
                <label>ID Number(s)</label>
              </div>
              <div id="agent_div" class="phone-addon text-left" style="display: none;">
                <select class="se_multiple_select" name="agents_ids[]" multiple="multiple" id="agentsSel">
                  <option value="<?=$resAgent['agentId']?>" selected="selected"><?=$resAgent["agentDispId"]?> - <?=$resAgent["agentName"]?></option>
                  <?php if(!empty($resCommAgent)){
                    foreach ($resCommAgent as $agent) {
                  ?>
                    <option value="<?=$agent["agentId"]?>" <?=$agent["agentId"]==1 ? "selected='selected'" : ""?>><?=$agent["agentDispId"]?> - <?=$agent["agentName"]?></option>
                  <?php
                    }
                  }
                  ?>
                </select>
                <label>ID Number(s)</label>
              </div>
              <div class="phone-addon w-160">
                <label class="mn"><input type="checkbox" id="includeDownline" name="includeDownline" value="Y" checked="checked">Include Downline</label>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="text-right p-t-5 input-question">
              <label class="radio-inline">
                <input type="radio" class="accountType" name="accountType" value="Business" checked="checked">By Agency
              </label>
              <label class="radio-inline">
                <input type="radio" class="accountType" name="accountType" value="Personal">By Agent
              </label>
              <span class="text-light-gray">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
              <label class="checkbox-inline">
                <input type="checkbox" class="commType" name="commType[]" value="earnedComm" checked="checked">Earned
              </label>
              <label class="checkbox-inline">
                <input type="checkbox" class="commType" name="commType[]" value="advanceComm" checked="checked">Advanced
              </label>
            </div>
          </div>
        </div>
      </div>
      <div id="commTblSumm">
      </div>
    </div>
  </div>
</form>
<!-- Commission Summary Code Ends -->
<?php } ?>

  <form id="frm_search" method="GET" class="theme-form" autocomplete="off">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <?php if(!empty($agent_id)){ ?>
    <input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_id?>" />
    <?php } ?>
    <div id="weeklyCommissionDiv"></div>
    <div id="monthlyCommissionDiv"></div>
  </form>

<script type="text/javascript">
  $(document).ready(function() {
    dropdown_pagination('weeklyCommissionDiv','monthlyCommissionDiv');

    loadStatement();
    loadCommissionSummary();
    
    $("#agentsSel").multipleSelect({
      selectAll: false,
      width:"100%",
      filter:true,
      onClick:function(e){
          loadCommissionSummary();
      }
    });
    $("#agencySel").multipleSelect({
      selectAll: false,
      width:"100%",
      filter:true,
      onClick:function(e){
          loadCommissionSummary();
      }
    });

    $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true,
      autoclose:true
    });

    
    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
      e.preventDefault();
      if($(this).val() == ''){
        $('.select_date_div').hide();
        $('#date_range').removeClass('col-md-3').addClass('col-md-12');
      } else {
        $('#date_range').removeClass('col-md-12').addClass('col-md-3');
        $('.select_date_div').show();
        if ($(this).val() == 'Range') {
          $('#range_join').show();
          $('#all_join').hide();
        } else {
            $('#range_join').hide();
            $('#all_join').show();
        }
      }
    });

    $("#join_range").val("Exactly").change();

    $("#btn_select_date").click(function () {
        $('#custom-date-toggle').slideToggle('slow');
    });

    $(document).off('click', '.btn_set_date');
    $(document).on('click', '.btn_set_date', function(e) {
      var is_error = false;
      $('.error').html('');

      if ($("#join_range").val() == '') {
        $('.error_join_range').html('Please select Date Type');
        is_error = true;
      }
      if ($("#join_range").val() == 'Range') {
        if($("#fromdate").val() == "") {
          $('.error_fromdate').html('Please select From Date');
          is_error = true;
        }
        if($("#todate").val() == "") {
          $('.error_todate').html('Please select To Date');
          is_error = true;
        }
      } else {
        if($("#added_date").val() == "") {
          $('.error_added_date').html('Please select Date');
          is_error = true;
        }
      }

      if(is_error == false) {
        $('#custom-date-toggle').slideToggle('slow');
        var selected_date = '';
        if ($("#join_range").val() == 'Range') {
          selected_date += $("#fromdate").val() + ' - ' + $("#todate").val();
        } else {
          if($("#join_range").val() != "Exactly") {
              selected_date = $("#join_range").val() + ' ' + $("#added_date").val();    
          } else {
              selected_date = $("#added_date").val();
          }             
        }
        $("#btn_select_date").html(selected_date);

        loadCommissionSummary();
      }
    });

    $(document).off("click","#includeDownline");
    $(document).on("click","#includeDownline",function(e){
      e.stopPropagation();
      loadCommissionSummary();
    });

    $(document).off("click",".accountType");
    $(document).on("click",".accountType",function(e){
      e.stopPropagation();
      if($(this).val() == "Business") {
        $("#agent_div").hide();
        $("#agency_div").show();
      } else {
        $("#agency_div").hide();
        $("#agent_div").show();
      }
      loadCommissionSummary();
    });

    $(document).off("click",".commType");
    $(document).on("click",".commType",function(e){
      e.stopPropagation();
      loadCommissionSummary();
    });

  });

  loadStatement = function(){
    weeklyCommissionStatement();
  }

  weeklyCommissionStatement = function() {
    $('#weeklyCommissionDiv').hide();
    var params = $("#frm_search").serialize();
    $.ajax({
      url: 'weekly_commission_statement.php',
      type: 'GET',
      data: params,
      beforeSend:function(){
        $("#ajax_loader").show();
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#weeklyCommissionDiv').html(res).show();
        common_select();
        monthlyCommissionStatement();
      }
    });
  }

  monthlyCommissionStatement = function() {
    $('#monthlyCommissionDiv').hide();
    var params = $("#frm_search").serialize();
    $.ajax({
      url: 'monthly_commission_statement.php',
      type: 'GET',
      data: params,
      beforeSend:function(){
        $("#ajax_loader").show();
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#monthlyCommissionDiv').html(res).show();
        common_select();
      }
    });
  }

  loadCommissionSummary = function(){
    $('#commTableDiv').hide();
    var params = $("#commSummaryFrm").serialize();
    $.ajax({
      url: '<?=$HOST?>/ajax_load_commission_summary.php',
      type: 'POST',
      data: params,
      dataType: 'JSON',
      beforeSend:function(){
        $("#ajax_loader").show();
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('.totalCommAmt').html(res.totalCommAmt).show();
        $('.totalCredits').html(res.totalCredits).show();
        $('.totalDebits').html(res.totalDebits).show();
     
        $('#commTblSumm').html(res.commTblSumm).show();
        $('[data-toggle="tooltip"]').tooltip();
        common_select();
      }
    });
  }
</script>