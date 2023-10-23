<?php 
  include_once "notify.inc.php";
?>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <h4 class="mn">Commission Payments</h4>
  </div>
  <form id="approvePayComm">
      <input type="hidden" name="commission_duration" value="<?=$commission_duration?>">
      <input type="hidden" name="agentIds" value="<?=$agentIds?>">
      <input type="hidden" name="pay_period" value="<?=$pay_period?>">
      <input type="hidden" name="earnedApply" id="earnedApply" value="">
      
      
    <div class="panel-body">
               
      <!-- Agent listing code start -->
        <div class="table table-responsive br-n agentListTbl" id="tableAgents">
          <table class="<?=$table_class?>">
            <thead>
              <th>Agnet ID</th>
              <th>Agency/Agent Name</th>
              <th>Debit Balance</th>
              <th>Earned</th>
              <th>PMPMs</th>
            </thead>
            <tbody>
                <?php if(!empty($resAgentBalance)){
                  $totalDebitBalance = 0;
                  $totalEarnedCredit = 0;
                  $totalPmpmCredit = 0;
                  foreach ($resAgentBalance as $rows){
                ?>
                <input type="hidden" name="debitBalance[<?=$rows['agentId']?>]" value="<?=$rows['debitBalance']?>">
                <input type="hidden" name="earnedCredit[<?=$rows['agentId']?>]" value="<?=$rows['earnedCredit']?>">
                <input type="hidden" name="pmpmCredit[<?=$rows['agentId']?>]" value="<?=$rows['pmpmCredit']?>">

                <tr>
                  <td><a href="javascript:void(0);" class="text-action fw500"><?=$rows['agentDispId']?></a></td>
                  <td><?=$rows['agentName']?></td>
                  <td><?=dispCommAmt($rows['debitBalance'])?></td>
                  <td><?=dispCommAmt($rows['earnedCredit'])?></td>
                  <td><?=dispCommAmt($rows['pmpmCredit'])?></td>
                  <?php
                    $totalDebitBalance += $rows['debitBalance'];
                    $totalEarnedCredit += $rows['earnedCredit'];
                    $totalPmpmCredit += $rows['pmpmCredit'];
                  ?>
                </tr>
                   <?php } ?>
                <tr>
                  <td> <a href="javascript:void(0);" class="text-action fw500">Total</a> </td>
                  <td> - </td>
                  <td> <?= dispCommAmt($totalDebitBalance) ?> </td>
                  <td> <?= dispCommAmt($totalEarnedCredit) ?> </td>
                  <td> <?= dispCommAmt($totalPmpmCredit) ?> </td>
                </tr>
                    <?php } else {
                  ?>
                  <tr>
                    <td colspan="4" class="text-center">No record(s) found</td>
                  </tr>
                  <?php }?>
            </tbody>
          </table>
        </div>
        <p class="error error_agentIds"></p>
        <p class="error error_pay_period"></p>
        
        <div class="clearfix">
          <table class="pull-right m-t-20 pmpmSwitchDiv">
            <tbody>
              <tr>
                <td>Apply PMPMs: </td>
                <td>
                  <div class="custom-switch commission-switch">
                    <label class="smart-switch">
                      <input type="checkbox" class="js-switch" id="pmpmApplySwitch" name="pmpmApply" checked="checked"/>
                      <div class="smart-slider round"></div>
                    </label>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      
      <!-- Agent listing code ends -->
      
      <?php if($selType == "allAgent") { ?>
        <span class="error error_earnedApply"></span>

        <div class="form-group text-center m-t-30">
          <a href="javascript:void(0);" class="btn btn-info earnedApplyBtn" data-val="applyToAgent">Apply to Agent</a>
          <a href="javascript:void(0);" class="btn btn-info earnedApplyBtn" data-val="applyToDebit">Apply to Debit Balance</a>
          <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
        </div>

        <div class="text-center">
          <p class="mn"><i class="fs10 text-center" style="color: #999999;">By default, all PMPM commissions are paid to the agent. To apply partial amount to <br/>debit balance and/or agent, select one agent at a time for approval.</i></p>
        </div>
      
      <?php }else if($selType == "singleAgent"){ ?>

        <p class="m-t-20">How Would you like to apply these Commissions?</p>

        <label class="m-b-10"><input type="radio" class="earnedApplyBtn" name="earnedApply" value="applyToDebit"> Debit Balance</label>
        <div class="clearfix"></div>
        <label class="m-b-10"><input type="radio" class="earnedApplyBtn" name="earnedApply" value="applyToAgent"> Agent Payment</label>
        <div class="clearfix"></div>
        <label class="m-b-10"><input type="radio" class="earnedApplyBtn" name="earnedApply" value="applyCustomAmt"> Customized Distribution</label>
        <p class="error error_earnedApply"></p>

        <div id="customAmtPrdDiv">
          <div class="table-responsive br-n m-t-20">
            <table class="<?=$table_class?>">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Earned</th>
                  <th>Earned Apply</th>
                  <th>PMPM</th>
                  <th>PMPM Apply</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  if(!empty($customCommArr)){
                    foreach ($customCommArr as $commPrd){
                ?>
                  <tr>
                    <td><?=$commPrd['prdName']?></td>
                    <td><?=dispCommAmt($commPrd['earnedPrdCredit'])?></td>
                    <td>
                      <input type="hidden" name="earnedPrdCredit[<?=$commPrd['prdId']?>]" value="<?=$commPrd['earnedPrdCredit']?>">
                      <div class="custom-switch commission-switch">
                        <label class="smart-switch">
                        <input type="checkbox" class="js-switch" name="earnedPrdApply[<?=$commPrd['prdId']?>]" <?=$commPrd["type"] == 'Adjustment' ? "checked='checked'" : ""?>/>
                        <div class="smart-slider round"></div>
                        </label>
                      </div>
                    </td>
                    <td><?=dispCommAmt($commPrd['pmpmPrdCredit'])?></td>
                    <td>
                      <input type="hidden" name="pmpmPrdCredit[<?=$commPrd['prdId']?>]" value="<?=$commPrd['pmpmPrdCredit']?>">
                      <div class="custom-switch commission-switch">
                        <label class="smart-switch">
                        <input type="checkbox" class="js-switch" name="pmpmPrdApply[<?=$commPrd['prdId']?>]" checked="checked"/>
                        <div class="smart-slider round"></div>
                        </label>
                      </div>
                    </td>
                  </tr>
                <?php
                    }
                  }else{
                ?>
                <td class="text-center" colspan="5">Record(s) not found</td>
                <?php              
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="applyCommission">Apply</a>
          <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
        </div>
      <?php } ?>
    </div>
  </form>
</div>


<?php if($selType=="allAgent") { ?>
<script type="text/javascript">
  $(document).ready(function(){
    $('.agentListTbl').slimScroll({
      height: '100%',
      width: '100%',
      alwaysVisible: true
    });
    
    $(".earnedApplyBtn").click(function(){
      $("#ajax_loader").show();
      $(".error").html("");
      
      $earnedApply = $(this).attr("data-val");
      $("#earnedApply").val($earnedApply);
      
      var params = $("#approvePayComm").serialize();
        $.ajax({
          url: 'ajax_pay_agent_commissions.php',
          type: 'POST',
          dataType: "json",
          data: params,
          success: function(res) {
            $(".error").html("");
              $("#ajax_loader").hide();
            if (res.status == "success") {
              parent.setNotifySuccess(res.message);
               setTimeout(function(){
                 window.parent.location.reload();
               }, 1000);
            }else if(res.status == "fail"){
              parent.setNotifyError(res.message);
            }else if(res.status == "error"){
                $.each(res.errors, function (index, error) {
                  $('.error_' + index).html(error).show();
                });
            }
          }
        });
    });
  });

  window.onload = function () {
    document.querySelector('#tableAgents').addEventListener('scroll', function () {
      var scrollTop = this.scrollTop;
      this.querySelector('thead').style.transform = 'translateY(' + scrollTop + 'px)';
    });
  }
</script>

<?php }else if($selType == "singleAgent"){ ?>

<script type="text/javascript">
  $(document).ready(function(){
    $("#customAmtPrdDiv").hide();
   
     $('.agentListTbl').slimScroll({
      height: '100%',
      width: '100%',
      alwaysVisible: true
    });

    $(document).on("click",'.earnedApplyBtn',function(){
      $val = $(this).val();
      if($val == 'applyCustomAmt'){
        $("#customAmtPrdDiv").show().fadeIn();
        $(".pmpmSwitchDiv").hide().fadeOut();
      }else{
        $(".pmpmSwitchDiv").show().fadeIn();
        $("#customAmtPrdDiv").hide().fadeOut();
        if($val == "applyToDebit"){
          $("#pmpmApplySwitch").prop("checked", false);
        }else if($val == "applyToAgent"){
          $("#pmpmApplySwitch").prop("checked", true);
        }
      }
    });


    $("#applyCommission").click(function(){
      $("#ajax_loader").show();
      $(".error").html("");
      var params = $("#approvePayComm").serialize();
        $.ajax({
          url: 'ajax_pay_agent_commissions.php',
          type: 'POST',
          dataType: "json",
          data: params,
          success: function(res) {
            $(".error").html("");
              $("#ajax_loader").hide();
            if (res.status == "success") {
              parent.setNotifySuccess(res.message);
               setTimeout(function(){
                 window.parent.location.reload();
               }, 1000);
            }else if(res.status == "fail"){
              parent.setNotifyError(res.message);
            }else if(res.status == "error"){
                $.each(res.errors, function (index, error) {
                  $('.error_' + index).html(error).show();
                });
            }
          }
        });
    });

 });

 window.onload = function () {
    document.querySelector('#tableAgents').addEventListener('scroll', function () {
      var scrollTop = this.scrollTop;
      this.querySelector('thead').style.transform = 'translateY(' + scrollTop + 'px)';
    });
  }
</script>
<?php } ?>