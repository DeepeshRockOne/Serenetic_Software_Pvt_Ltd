<!-- Paid Commissions Code Start -->
  <div class="top_comission_boxes">
     <div class="container">
        <div class="row">
           <div class="col-md-4 col-sm-6">
              <div class="top_commission_singlebox bg_dark_danger">
                <div class="top_commission_text">Total Paid Commissions</div>
                <div class="top_commission_digit" id="paidCommissionTotal"><?= displayAmount($totalPaidCommission);?></div>
              </div>
           </div>
           <div class="col-md-4 col-sm-6">
            <div class="top_commission_singlebox bg_light_primary">
                <div class="top_commission_text">Weekly Paid Commissions</div>
                <div class="top_commission_digit" id="weeklyCommissionTotal"><?= displayAmount($totalWeeklyCommission);?></div>
              </div>
           </div>
           <div class="col-md-4 col-sm-6">
            <div class="top_commission_singlebox bg_dark_cyan">
                <div class="top_commission_text">Monthly Paid Commissions</div>
                <div class="top_commission_digit" id="monthlyCommissionTotal"><?= displayAmount($totalMonthlyCommission);?></div>
              </div>
           </div>
        </div>
     </div>
  </div>
<!-- Paid Commissions Code Ends -->


<div class="container m-t-30">
  <!-- Commission Summary Code Start -->
  <form id="commSummaryFrm" class="theme-form" autocomplete="off">
    <input type="hidden" name="location" value="agent">
    <input type="hidden" name="agency_ids[]" value="<?=$_SESSION['agents']['id']?>">
    <input type="hidden" name="agents_ids[]" value="<?=$_SESSION['agents']['id']?>">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="dashboard_topbar">
          <div class="row">
              <div class="col-lg-4 col-md-3 ">
                <p class="fs18 mn text-action fw500">New Business Commissions</p>
              </div>
              <div class="col-lg-8 col-lg-offset-4 dashboard_topbar_right top_header_section">
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
                <div class="form-group height_auto m-b-15">
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
                <div class="form-group height_auto mn">
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

        <div class="clearfix m-b-10 input-question">
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
          <!-- <div class="phone-addon w-160"> -->
            <span class="text-light-gray">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <label class="mn"><input type="checkbox" id="includeDownline" name="includeDownline" value="Y" checked="checked">Include Downline</label>
          <!-- </div> -->
        </div>

         <div id="commTblSumm">
        </div>
      </div>
    </div>
  </form>
  <!-- Commission Summary Code Start -->

  <!-- Search Commission Code Start -->  
    <div class="panel panel-default">
       <form id="frm_search" method="GET" class="theme-form">
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />   
          <?php if($payPeriod!=''){?>
          <input type="hidden" name="pay_period" id="pay_period" value="<?= $payPeriod ?>">
          <?php } ?>     
          <div class="panel-body">
             <div class="row">
                <div class="col-sm-7">
                   <div class="form-group height_auto mn">
                      <div class="input-group">
                         <div class="pr">
                            <input class="form-control" type="text" name="idEmailName" id="idEmailName" />
                            <label>Search Member ID or Order ID</label>
                         </div>
                         <span class="input-group-addon btn bg_dark_primary" name="search" id="search"><i class="fa fa-search text-white"></i></span>
                      </div>
                   </div>
                </div>
                <div class="col-sm-2">
                   <div id="searchInStatementDiv_close" style="display: none;">
                    <a href="javascript:void(0);" id="closeSearchStatement" class="btn btn-info">Clear Search</a>
                  </div>
                </div>
             </div>
          </div>
       </form>
       <div class="clearfix"></div>
       <!-- Search Statement Detail Table Code Start -->
       <div id="searchInStatementDiv" class="panel-body p-t-0" style="display: none;">
          <div id="searchInStatementDivHtml"></div>
       </div>
       <!-- Search Statement Detail Table Code End -->
    </div>
  <!-- Search Commission Code Ends -->  

  <!-- Statement Box Code Start -->
    <div class="panel panel-default" id="weeklyCommDiv">
       <div class="panel-body">
          <div class="thumbnail mn">
             <div class="row">
                <div class="col-md-4">
                   <div class="weekcom_left_infoscroll">
                      <h4 class="m-b-20 m-t-0">Weekly Commission Summary</h4>
                      <table class="weekly_com_sum">
                         <tbody>
                            <tr>
                               <td width="150px"><?=$agentRow['name']?></td>
                               <?php 
                                  $startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($weeklyPayPeriod)));;
                                  $endPayPeriod=date('m/d/Y', strtotime($weeklyPayPeriod));
                                  ?>
                               <td>
                                  <strong>Period:</strong> <?=$startPayPeriod .' - '.$endPayPeriod?>
                               </td>
                            </tr>
                            <tr>
                               <td width="150px"><a href="javascript:void(0);" class="red-link"><?=$agentRow['rep_id']?></a></td>
                               <td>
                                  <strong>Total Commissions:</strong> <?=dispCommAmt($totalCommWeekly)?>
                               </td>
                            </tr>
                         </tbody>
                      </table>
                   </div>
                </div>
                <div class="col-md-8">
                   <div class="row">
                      <div class="col-md-8">
                         <div class="table-responsive">
                            <table class="table table-striped table-small  com_small_tbl weekly text-right">
                               <thead>
                                  <tr>
                                    <th></th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Total</th>
                                  </tr>
                               </thead>
                                <tbody>
                                  <tr>
                                    <td class="table_light_danger text-left">Earned</td>
                                    <td><?=dispCommAmt($earnedCommWeekly)?></td>
                                    <td><?=dispCommAmt($earnedCommRevWeekly)?></td>
                                    <td><?=dispCommAmt($earnedNetCommWeekly)?></td>
                                  </tr>
                                  <tr>
                                      <td class="table_dark_danger text-left">Advanced</td>
                                      <td><?=dispCommAmt($advanceCommWeekly)?></td>
                                      <td><?=dispCommAmt($advanceCommRevWeekly)?></td>
                                      <td><?=dispCommAmt($advanceNetCommWeekly)?></td>
                                  </tr>
                                  <tr>
                                  <td class="table_light_danger text-left">PMPM</td>
                                      <td><?=dispCommAmt($pmpmCommWeekly)?></td>
                                      <td><?=dispCommAmt($pmpmCommRevWeekly)?></td>
                                      <td><?=dispCommAmt($pmpmNetCommWeekly)?></td>
                                  </tr>
                                  <tr>
                                  <td class="table_dark_danger text-left"><strong>Total</strong></td>
                                      <td><?=dispCommAmt($totalEarnedCommWeekly)?></td>
                                      <td><?=dispCommAmt($totalRevCommWeekly)?></td>
                                      <td><?=dispCommAmt($totalNetCommWeekly)?></td>
                                  </tr>
                                </tbody>
                            </table>
                         </div>
                      </div>
                       <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-small  com_small_tbl weekly text-right">
                                <thead>
                                    <tr>
                                    <th></th>
                                    <th class="text-right" colspan="2">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="table_light_danger text-left">Past Reversals</td>
                                        <td><?=dispCommAmt($pastCommRevWeekly)?></td>
                                    </tr>
                                    <tr>
                                        <td class="table_dark_danger text-left">Fees</td>
                                        <td><?=dispCommAmt($feeCommWeekly)?></td>
                                    </tr>
                                    <tr>
                                        <td class="table_light_danger text-left">Adjustments</td>
                                        <td><?=dispCommAmt($adjustmentCommWeekly)?></td>
                                    </tr>
                                    <tr>
                                        <td class="table_dark_danger text-left"><strong>Total</strong></td>
                                        <td><?=dispCommAmt($otherTotalCommWeekly)?></td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                   </div>
                </div>
             </div>
          </div>
       </div>
       <div class="panel-body p-t-0">
          <div id="wekkly_commission_statements_div"></div>
       </div>
    </div>
    <div class="panel panel-default">
       <div class="panel-body">
          <div class="thumbnail mn">
             <div class="row">
                <div class="col-md-4">
                   <div class="monthcom_left_infoscroll">
                      <h4 class="m-t-0 m-b-25"> Monthly Commission Summary</h4>
                      <table class="m-b-30 weekly_com_sum">
                         <tbody>
                            <?php 
                               $startPayPeriod=date('m/01/Y', strtotime($monthlyPayPeriod));
                               $endPayPeriod=date('m/d/Y', strtotime($monthlyPayPeriod));
                               ?>
                            <tr>
                               <td width="150px"><?=$agentRow['name']?></td>
                               <td><strong>Period: </strong><?=$startPayPeriod.' - '.$endPayPeriod?></td>
                            </tr>
                            <tr>
                               <td width="150px"><a href="javascript:void(0);" class="red-link"><?=$agentRow['rep_id']?></a></td>
                               <td><strong>Total Commissions:</strong> <?=dispCommAmt($totalCommMonthly)?></td>
                            </tr>
                         </tbody>
                      </table>
                   </div>
                </div>
                <div class="col-md-8">
                   <div class="row">
                      <div class="col-md-8">
                         <div class="table-responsive">
                            <table class="<?=$table_class?> com_small_tbl text-right">
                               <thead>
                                  <tr>
                                    <th></th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Total</th>
                                  </tr>
                               </thead>
                               <tbody>
                                  <tr>
                                     <td class="table_light_primary text-left">Earned</td>
                                     <td><?=dispCommAmt($earnedCommMonthly)?></td>
                                     <td><?=dispCommAmt($earnedCommRevMonthly)?></td>
                                     <td><?=dispCommAmt($earnedNetCommMonthly)?></td>
                                  </tr>
                                  <tr>
                                     <td class="table_dark_primary text-left">Advanced</td>
                                     <td><?=dispCommAmt($advanceCommMonthly)?></td>
                                     <td><?=dispCommAmt($advanceCommRevMonthly)?></td>
                                     <td><?=dispCommAmt($advanceNetCommMonthly)?></td>
                                  </tr>
                                  <tr>
                                     <td class="table_light_primary text-left">PMPM</td>
                                     <td><?=dispCommAmt($pmpmCommMonthly)?></td>
                                     <td><?=dispCommAmt($pmpmCommRevMonthly)?></td>
                                     <td><?=dispCommAmt($pmpmNetCommMonthly)?></td>
                                  </tr>
                                  <tr>
                                     <td class="table_dark_primary text-left"><strong>Total</strong></td>
                                     <td><?=dispCommAmt($totalEarnedCommMonthly)?></td>
                                     <td><?=dispCommAmt($totalRevCommMonthly)?></td>
                                     <td><?=dispCommAmt($totalNetCommMonthly)?></td>
                                  </tr>
                                </tbody>
                            </table>
                         </div>
                      </div>
                      <div class="col-md-4">
                        <div class="table-responsive">
                           <table class="table table-striped table-small  com_small_tbl monthly text-right">
                              <thead>
                                 <tr>
                                    <th class="text-right"></th>
                                    <th class="text-right">Total</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td class="table_light_primary text-left">Past Reversals</td>
                                    <td><?=dispCommAmt($pastCommRevMonthly)?></td>
                                 </tr>
                                 <tr>
                                    <td class="table_dark_primary text-left">Fees</td>
                                    <td><?=dispCommAmt($feeCommMonthly)?></td>
                                 </tr>
                                 <tr>
                                    <td class="table_light_primary text-left">Adjustments</td>
                                    <td><?=dispCommAmt($adjustmentCommMonthly)?></td>
                                 </tr>
                                 <tr>
                                    <td class="table_dark_primary text-left"><strong>Total</strong></td>
                                    <td><?=dispCommAmt($otherTotalCommMonthly)?></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
          <div class="p-15 text-right bg_white br-b">
             <a href="commissions_debit_balance.php" class="btn btn-action commissions_debit_balance">Debit Balance</a>
             <a href="monthly_ledger_popup.php" class="btn btn-action-o monthly_ledger_popup">Ledger</a>
            </div>
       </div>
       <div class="panel-body p-t-0">
          <div id="monthly_commission_statements_div"></div>
       </div>
    </div>
  <!-- Statement Box Code End -->
</div>

<script type="text/javascript">
  $(document).ready(function () { 
    dropdown_pagination('monthly_commission_statements_div','wekkly_commission_statements_div','searchInStatementDivHtml');

    $('.commissions_debit_balance').colorbox({iframe:true, width: '385px', height: '260px'}); 
    $('.monthly_ledger_popup').colorbox({iframe:true, width: '1000px', height: '565px'});   
    
      load_statement(); //this will search in statement boxes
      loadCommissionSummary();

    $(document).on("click","#search",function(){
      if($("#idEmailName").val()!=''){
        search_in_statement();
      }else{
         $("#searchInStatementDivHtml").html('');
        $("#searchInStatementDiv,#searchInStatementDiv_close").hide();
      }
    });
    $(document).on("click","#closeSearchStatement",function(){
      $("#searchInStatementDivHtml").html('');
      $("#searchInStatementDiv,#searchInStatementDiv_close").hide();
    });

    $(document).off("click",".exportCSV");
    $(document).on('click', '.exportCSV', function(e) {
      e.preventDefault();
      var link = $(this).attr("href");
      confirm_export_data(function() {
      $('#ajax_loader').show();
        $.ajax({
            url: link,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#ajax_loader').hide();
                if(res.status == "success") {
                    confirm_view_export_request(true,'agent');
                } else {
                    setNotifyError(res.message);
                }
            }
        });
      });
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

    $(document).off("click",".commType");
    $(document).on("click",".commType",function(e){
      e.stopPropagation();
      loadCommissionSummary();
    });

    $(document).off("click",".accountType");
    $(document).on("click",".accountType",function(e){
      e.stopPropagation();
      loadCommissionSummary();
    });

    $(document).off("click","#includeDownline");
    $(document).on("click","#includeDownline",function(e){
      e.stopPropagation();
      loadCommissionSummary();
    });

  });

  var weeklyCommissionBoxHeight='';
  var monthlyCommissionBoxHeight='';
  
  load_statement = function(){
    wekkly_commission_statement();
  }

  wekkly_commission_statement = function(){
    $('#ajax_loader').show();
    $('#wekkly_commission_statements_div').hide();
    var params = $('#frm_search').serialize();
    $.ajax({
        url: 'weekly_commission_statement.php',
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            monthly_commission_statement();
            $('#wekkly_commission_statements_div').html(res).show();
            $('.weekly_detail_popup').colorbox({iframe:true, width: '1000px', height: '565px'});
            weeklyCommissionBoxHeight = $('.lhs_content .white-box').outerHeight();
            common_select();
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        }
    });
  }

  monthly_commission_statement = function(){
    $('#ajax_loader').show();
    $('#monthly_commission_statements_div').hide();
    var params = $('#frm_search').serialize();
    $.ajax({
        url: 'monthly_commission_statement.php',
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#monthly_commission_statements_div').html(res).show();
            $('.monthly_detail_popup').colorbox({iframe:true, width: '1000px', height: '565px'});
            monthlyCommissionBoxHeight = $('.rhs_content .white-box').outerHeight();
            setCommissionBoxHeight();
            common_select();
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        }
    });
  }

  setCommissionBoxHeight = function(){
    if(weeklyCommissionBoxHeight<monthlyCommissionBoxHeight){
      $('.lhs_content .white-box').css({'height':monthlyCommissionBoxHeight});
    }else{
      $('.rhs_content .white-box').css({'height':weeklyCommissionBoxHeight});
    }
  }

  search_in_statement = function(){
    $("#searchInStatementDiv,#searchInStatementDiv_close").show();
    $('#ajax_loader').show();
    $('#searchInStatementDivHtml').hide();
    var params = $('#frm_search').serialize();
    $.ajax({
        url: 'commission_statement_search.php',
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#searchInStatementDivHtml').html(res).show();
            $('.detail_popup').colorbox({iframe:true, width: '1000px', height: '565px'});
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