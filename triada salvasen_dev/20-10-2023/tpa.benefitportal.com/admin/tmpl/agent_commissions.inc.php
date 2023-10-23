
    <p class="agp_md_title">Commissions</p>
    <div class="tabbing-tab mn"> 
    <!-- Nav tabs -->
    <ul class="nav nav-tabs tabs customtab" role="tablist">
        <li role="presentation"  class="active">
            <a href="#agp_weekly_com" aria-controls="agp_weekly_com" role="tab" data-toggle="tab">Weekly</a>
        </li>
        <li role="presentation" >
            <a href="#agp_monthly_com" aria-controls="agp_monthly_com" role="tab" data-toggle="tab">Monthly</a>
        </li>
        <li role="presentation" >
            <a href="#agp_direct_deposit" aria-controls="agp_direct_deposit" role="tab" data-toggle="tab">Direct Deposit</a>
        </li>
    </ul>
    </div>
    <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="agp_weekly_com">
        <p class="fs16 fw500 m-b-20">Weekly Commission Summary</p>
        <table class="m-b-30 weekly_com_sum">
            <tbody>
                <tr>
                <td width="150px"><?=$agent_info['name']?></td>
                <?php 
                    $startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($weeklyPayPeriod)));;
                    $endPayPeriod=date('m/d/Y', strtotime($weeklyPayPeriod));
                ?>
                <td><strong>Period:</strong> <?=$startPayPeriod .' - '.$endPayPeriod?></td>
                </tr>
                <tr>
                <td width="150px"><a href="javascript:void(0);" class="red-link"><?=$agent_info['rep_id']?></a></td>
                <td><strong>Total Commissions:</strong> <?=dispCommAmt($totalCommWeekly)?> </td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-sm-10">
                <div class="thumbnail">
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
        <div class="clearfix"></div>
        <div id="weekly_commission_div"></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="agp_monthly_com">
        <p class="agp_md_title m-b-20">Monthly Commission Summary</p>
        <table class="m-b-30 weekly_com_sum">
        <tbody>
            <?php 
                $startPayPeriod=date('m/01/Y', strtotime($monthlyPayPeriod));
                $endPayPeriod=date('m/d/Y', strtotime($monthlyPayPeriod));
            ?>
            <tr>
                <td width="150px"><?=$agent_info['name']?></td>
                <td><strong>Period: </strong><?=$startPayPeriod.' - '.$endPayPeriod?></td>
            </tr>
            <tr>
                <td width="150px"><a href="javascript:void(0);" class="red-link"><?=$agent_info['rep_id']?></a></td>
                <td><strong>Total Commissions:</strong> <?=dispCommAmt($totalCommMonthly)?></td>
            </tr>
        </tbody>
        </table>
        <div class="row">
        <div class="col-sm-10">
            <div class="thumbnail">
            <div class="row">
                <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-striped table-small  com_small_tbl monthly text-right">
                    <thead>
                        <tr>
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
         <a href="commissions_debit_balance.php?agent_id=<?= $agent_info['id'] ?>" class="btn btn-info commissions_debit_balance">Debit Balance</a>
         <a class="btn red-link fw500 no-underline"><?=$debitBalance > 0 ? "(".displayAmount($debitBalance,2).")" : displayAmount($debitBalance,2)?></a>
        </div>
        <div class="clearfix"></div>
        <div id="monthly_commission_div"></div>
    </div>
    <div role="tabpanel" class="tab-pane " id="agp_direct_deposit">
        <div class="clearfix m-b-10">
        <div class="pull-left"> <h4 class="agp_md_title m-t-0">Direct Deposit Accounts</h4> </div>
        <div class="pull-right"> <button class="btn btn-info" id="add_account" onclick="$('#add_new_account').show()">+ New Account</button> </div>
        </div>
        <div class="table-responsive m-b-30">
            <div id="direct_deposit_div"></div>
        </div>
        <div id="add_new_account" style="display:none" class="add_new_account">
            <p class="agp_md_title">+ New Account</p>
            <div class="theme-form">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control" name="effective_date" id="effective_date"  placeholder="Effective Date (MM/DD/YYYY)">
                            </div>
                            <p class="error"><span id="error_effective_date"></span></p>
                        </div>
                        <div class="form-group height_auto">
                            <label class="radio-inline">Account Type<em>*</em></label>
                            <div class="radio-question">
                                <label class="radio-inline">
                                <input type="radio" name="bank_account_type" value="checking" >
                                Checking</label>
                                <label class="radio-inline">
                                <input type="radio" name="bank_account_type" value="saving">
                                Savings</label>
                            </div>    
                            <p class="error"><span id="error_bank_account_type"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" name="new_bank_name" id="new_bank_name" />
                        <label>Bank Name</label>
                        <p class="error"><span id="error_new_bank_name"></span></p>
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" name="new_routing_number" id="new_routing_number" oninput="isValidNumber(this)" maxlength='9' />
                        <label>Bank Routing Number</label>
                        <p class="error"><span id="error_new_routing_number"></span></p>
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" name="new_account_number" id="new_account_number" oninput="isValidNumber(this)" maxlength='17' />
                        <label>Bank Account Number</label>
                        <p class="error"><span id="error_new_account_number"></span></p>
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" name="confirm_account_number" id="confirm_account_number" oninput="isValidNumber(this)" maxlength='17' />
                        <label>Confirm Bank Account Number</label>
                        <p class="error"><span id="error_confirm_account_number"></span></p>
                    </div>
                    </div>
                </div>
                <div class="text-center"> <a href="javascript:void(0);" class="btn btn-action" id="save_account_detail">Save</a> <a href="javascript:void(0);" class="btn red-link"  onclick="$('#add_new_account').hide();">Cancel</a> </div>
            </div>
        </div>
    </div>
    </div>
    <hr />

<script type="text/javascript">
    $(document).ready(function () {
        dropdown_pagination('weekly_commission_div','monthly_commission_div','direct_deposit_div')
        weekly_commission_statement();
        monthly_commission_statement();
        direct_deposit_statement();
        $('.commissions_debit_balance').colorbox({iframe:true, width: '385px', height: '260px'});
    });

    
    $(document).off('click', '#save_account_detail');
    $(document).on('click', '#save_account_detail', function (e) {
        $('#ajax_loader').show();
        var $data = {
            effective_date: $("#effective_date").val(),
            bankname:$("#new_bank_name").val(),
            bank_rounting_number:$("#new_routing_number").val(),
            bank_account_number:$("#new_account_number").val(),
            bank_number_confirm:$("#confirm_account_number").val(),
            bank_account_type:$("input[name=bank_account_type]:checked").val(),
            agent_id:<?=$agent_info['id']?>
        };
        $.ajax({
            url: 'ajax_add_direct_deposite_account.php',
            type: 'POST',
            data: $data,
            success: function (res) {
                $('#ajax_loader').hide();
                if(res.status == 'success'){
                    // window.location = 'agent_detail_v1.php?id=<?=md5($agent_info['id'])?>';
                    $("#effective_date").val("");
                    $("#new_bank_name").val("");
                    $("#new_routing_number").val("");
                    $("#new_account_number").val("");
                    $("#confirm_account_number").val("");
                    $("input[name=bank_account_type]").prop("checked",false);
                    $("#add_new_account").hide();
                    direct_deposit_statement();
                    setNotifySuccess("New direct deposite account Added!");
                }else if(res.status=='fail'){
                    setNotifyError("Oops... Something went wrong please try again later");
                }else{
                    $(".error").hide();
                    $.each(res.errors, function(key, value) {
                        $('#error_' + key).parent("p.error").show();
                        $('#error_' + key).html(value).show();
                    });
                }
            }
        });
    });

    $(document).on('click','.agent_weekly_com_popup',function(e){
      var $href = $(this).attr('data-href');
      $.colorbox({
        href:$href,
        iframe: true,
        width: '1000px', 
        height: '565px'
        });
   });

   $(document).on('click','.agent_monthly_com_popup',function(e){
      var $href = $(this).attr('data-href');
      $.colorbox({
        href:$href,
        iframe: true,
        width: '1000px', 
        height: '565px'
        });
   });

   $(document).on('click','.direct_deposit_account',function(e){
      var $href = $(this).attr('data-href');
      $.colorbox({
        href:$href,
        iframe: true,
        width: '900px',
        height: '390px'
        });
   });

    function weekly_commission_statement(){
    $('#ajax_loader').show();
    $('#weekly_commission_div').hide();
    $.ajax({
        url: 'agent_weekly_commission.php',
        type: 'GET',
        data: {is_ajaxed:1,agent_id:<?=$agent_info['id']?>},
        success: function (res) {
            $('#ajax_loader').hide();
            $('#weekly_commission_div').html(res).show();
            common_select();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
  }

  function monthly_commission_statement(){
    $('#ajax_loader').show();
    $('#monthly_commission_div').hide();
    $.ajax({
        url: 'agent_monthly_commission.php',
        type: 'GET',
        data: {is_ajaxed:1,agent_id:<?=$agent_info['id']?>},
        success: function (res) {
            $('#ajax_loader').hide();
            $('#monthly_commission_div').html(res).show();
            common_select();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
  }

  function direct_deposit_statement(){
    $('#ajax_loader').show();
    $('#direct_deposit_div').hide();
    $.ajax({
        url: 'agent_direct_deposite_accounts.php',
        type: 'GET',
        data: {is_ajaxed:1,agent_id:<?=$agent_info['id']?>},
        success: function (res) {
            $('#ajax_loader').hide();
            $('#direct_deposit_div').html(res).show();
            $('[data-toggle="tooltip"]').tooltip();
            $("input[name=bank_account_type]").uniform();
            common_select();
        }
    });
  }

    $(document).off("click",".exportCSV");
    $(document).on('click', '.exportCSV', function(e) {
      e.preventDefault();
      var link = $(this).attr("href");
      parent.confirm_export_data(function() {
      $('#ajax_loader').show();
        $.ajax({
            url: link,
            type: 'GET',
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


$(document).off('click', '#weekly_commission_div ul.pagination li a');
$(document).on('click', '#weekly_commission_div ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#weekly_commission_div").hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $("#weekly_commission_div").html(res).show();
            common_select();
        }
    });
});

$(document).off('click', '#monthly_commission_div ul.pagination li a');
$(document).on('click', '#monthly_commission_div ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#monthly_commission_div").hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $("#monthly_commission_div").html(res).show();
            common_select();
        }
    });
});

$(document).off('click', '#direct_deposit_div ul.pagination li a');
$(document).on('click', '#direct_deposit_div ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#direct_deposit_div").hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $("#direct_deposit_div").html(res).show();
            common_select();
        }
    });
});

$("#effective_date").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true,
    startDate:new Date()
});
</script>