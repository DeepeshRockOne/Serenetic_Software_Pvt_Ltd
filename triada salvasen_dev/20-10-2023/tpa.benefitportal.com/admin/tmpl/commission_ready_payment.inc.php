 <hr>
<?php if ($total_rows > 0) { ?>
      <div class="clearfix m-b-15">
        <div id="paymentBtns" style="display: none;">
            <div class="pull-left">
                <a href="javascript:void(0);" class="btn btn-info payCommFile" data-fileType="NACHA">Export NACHA File To Pay</a>
                <a href="javascript:void(0);" class="btn red-link payCommFile" data-fileType="CSV"><i class="fa fa-download" aria-hidden="true"></i> Export CSV File to Pay</a>
            </div>  
        </div>
        <input type="hidden" name="fileType" id="fileType">
        <div class="pull-right">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn height_auto">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn height_auto">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);loadPaymentDiv();">
                <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
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
                  <th>
                    <div class="checkbox checkbox-custom mn">
                      <input type="checkbox" id="payToAllAgentBox" class="js-switch" data-container="body" data-trigger="hover" data-toggle="tooltip" title="Select All"/>
                      <label for="payToAllAgentBox"></label>
                    </div>
                  </th>
                  <th>Added Date</th>
                  <th>Agent ID/Name</th>
                  <th class="text-center">Balance</th>
                  <th class="text-center">Direct Deposit Account</th>
                  <th width="130px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { 
                ?>
                <tr>
             <td>
               <div class="checkbox checkbox-custom mn">
                 <input type="checkbox" name="wallet_history_ids[]" class="js-switch payToSpecAgentBox" value="<?= $rows['historyId'] ?>" data-agent_id="<?= $rows['agentId'] ?>" />
                 <label for="wallet_history_ids[]"></label>
               </div>
             </td>
             <td><?= date("m/d/Y", strtotime($rows["addedDate"])) ?></td>
             <td><a href="javascript:void(0);" class="fw500 text-action"><?= $rows["agentRepId"] ?></a><br><?= $rows["agentName"] ?></td>
             <td class="text-center"><?= dispCommAmt($rows["amount"], 2) ?></td>
             <td class="text-center"><?php echo $rows['account_number'] != "" ? '*' . substr($rows['account_number'], -4) : '-'; ?></td>
             <input type="hidden" name="account_number" class="account_number_<?= $rows['historyId'] ?>" value="<?= !empty($rows['account_number']) ? 'Y' : 'N' ?>">
             <td class="icons">
               <?php if ($rows["is_overpay_balance"] == 'N') { ?>
                 <a href="javascript:void(0);" class="reversePaidComm" data-toggle="tooltip" data-trigger="hover" title="Reverse" data-placement="top" data-history_id="<?= $rows['historyId'] ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a>
               <?php } ?>
               <a href="commission_wallet_history.php?agentId=<?= md5($rows["agentId"]) ?>" data-toggle="tooltip" data-trigger="hover" title="Wallet" data-placement="top" class="commission_wallet_history"><i class="ti-wallet" aria-hidden="true"></i></a>
               <?php if ($rows["amount"] < 0) { ?>
                 <a href="javascript:void(0)" data-agentId="<?= md5($rows["agentId"]) ?>" data-historyId="<?= md5($rows["historyId"]) ?>" data-walletId="<?= md5($rows["walletId"]) ?>" data-toggle="tooltip" data-trigger="hover" title="Wallet to debit" data-placement="top" class="wallet_transfer_ready"><i class="ti-move" aria-hidden="true"></i></a>
               <?php } ?>
             </td>
           </tr>
                    <?php }?>
                <?php } else {?>
                    <tr>
                        <td colspan="6" class="text-center">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php 
            if ($total_rows > 0) {?>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                </tfoot>
            <?php }?>
        </table>
    </div>

<div style="display:none" >
<div class="panel panel-default" id="acctAlertDiv">
  <div class="panel-body login-alert-modal">
    <div class="media br-n pn mn">
      <div class="media-left"> <img src="<?php echo $ADMIN_HOST; ?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
      <div class="media-body theme-form">
         <h3 class="text-action m-t-n fw600" >Uh Oh!</h3>
         <p class="">There are account(s) that do not have direct deposit setup yet. These accounts won’t be included in the NACHA File.</p>
         <div class="clearfix">
           <a href="javascript:void(0);" class="btn btn-action" id="continuePayComm">Continue</a>
           <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
         </div>
      </div>
    </div>
  </div>
</div>
</div>
<div style="display:none" id="download_div">

</div>
<script type="text/javascript">
  $(document).ready(function(){
    $(".commission_wallet_history").colorbox({iframe: true, width: '990px', height: '500px'});
    
    $(document).off('click', '#paymentDiv ul.pagination li a');
    $(document).on('click', '#paymentDiv ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#paymentDiv').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#paymentDiv').html(res).show();
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

    // Agent Commission Pay Code Start
    $(document).off("click","#payToAllAgentBox");
    $(document).on("click","#payToAllAgentBox",function(){
      if ($('#payToAllAgentBox').is(':checked')) {
        $('.payToSpecAgentBox').prop('checked',true);
        if($('.payToSpecAgentBox').length > 0){
            $("#paymentBtns").show();
        }
      }else {
        $('.payToSpecAgentBox').prop('checked', false);
         $("#paymentBtns").hide();
      }
    });

    $(document).off("click",".payToSpecAgentBox");
    $(document).on("click",".payToSpecAgentBox",function(){
      if ($('.payToSpecAgentBox[type=checkbox]:checked').length > 0){
         $("#paymentBtns").show();
      }else{
        $("#paymentBtns").hide();
      }
    });


    $(document).off("click",".payCommFile");
    $(document).on('click', '.payCommFile', function(e) {
      e.preventDefault();

      $fileType = $(this).attr("data-fileType");
      $("#fileType").val($fileType);
      var agentsAcct = "Y";

      var historyIds = $('input:checkbox:checked.payToSpecAgentBox').map(function() {
        if($(".account_number_"+this.value).val() == "N"){
            agentsAcct = "N";
        }
        return this.value;
      }).get();

      if(agentsAcct == "Y"){
        parent.swal({
            text: '<br>Pay Commissions: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function () {
          exportFilePayComm();
        }, function (dismiss){  
        });
      }else{
        $.colorbox({
          inline:true,
          href:"#acctAlertDiv",
          height:"250px",
          width:"450px",
        });
      }
    });

    $(document).off("click","#continuePayComm");
    $(document).on('click', '#continuePayComm', function(e) {
      e.preventDefault();
      exportFilePayComm();
    });

    $(".reversePaidComm").click(function(){
      $historyId = $(this).data("history_id");
      swal({
        text: '<br>Reverse Commissions: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
      }).then(function () {
        $.ajax({
          url: 'commissions_export_pay.php',
          type: 'POST',
          dataType: "json",
          data: {action:'reversePaidComm',historyId:$historyId},
          beforeSend: function () {
            $("#ajax_loader").show();
          },
          success: function(res) {
            $(".error").html("");
            $("#ajax_loader").hide();
            if (res.status == "success") {
                parent.swal({
                  text: '<br>Reverse Commissions: Successful',
                  showCancelButton: true,
                  showConfirmButton: false,
                  cancelButtonText: 'Close',
                }).then(function(){
                  window.parent.location.reload();
                }, function (dismiss){  
                  window.parent.location.reload();
                });
            }else if(res.status == "fail"){
              parent.swal({
                text: '<br>Reverse Commissions: Failed',
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

  });
  $(document).off('click', '.wallet_transfer_ready');
   $(document).on('click', '.wallet_transfer_ready', function() {
     var agentId = $(this).attr('data-agentId');
     var walletId = $(this).attr('data-walletId');
     var historyId = $(this).attr('data-historyId');
     parent.swal({
       text: "Apply to Debit Balance: Are you sure?",
       showCancelButton: true,
       confirmButtonText: "Confirm",
     }).then(function() {
       $.ajax({
         url: 'ajax_wallet_apply_to_debit_balance.php',
         method: 'POST',
         data: {
           agent_id: agentId,
           walletId: walletId,
           historyId: historyId,
         },
         dataType: 'json',
         beforeSend: function(e) {
           $("#ajax_loader").show();
         },
         success: function(res) {
           $("#ajax_loader").hide();
           if (res.status == "success") {
             parent.setNotifySuccess('Wallet balance applied to debit balance successfully');
           } else {
             //parent.setNotifyError('Something went wrong');
           }
           window.location.reload();
         }
       });
     }, function(dismiss) {

     });
   });

    exportFilePayComm = function(){
      var historyIds = $('input:checkbox:checked.payToSpecAgentBox').map(function() {
        return this.value;
      }).get();

      var agentIds = $('input:checkbox:checked.payToSpecAgentBox').map(function() {
        return $(this).attr("data-agent_id");
      }).get();

      $fileType = $("#fileType").val();
      
      $.ajax({
       type: 'GET',
       url: "commissions_export_pay.php?action=payCommFile&historyIds=" + historyIds + "&fileType=" + $fileType + "&agentIds=" + agentIds,
       data: {},
       dataType: 'json',
       beforeSend: function(e) {
         $("#ajax_loader").show();
       }
     }).done(function(data) {
       if(data.status=='success'){
        $("#ajax_loader").hide();
          var mydiv = document.getElementById("download_div");
          var aTag = document.createElement('a');
          aTag.setAttribute('href', data.file);
          aTag.setAttribute('download', data.file_name);
          aTag.innerText = "Download";
          mydiv.appendChild(aTag);
          aTag.click();
          aTag.remove();
          setTimeout(function() {
            window.parent.location.reload();
          }, 1000);
       }else{
          window.parent.location.reload();
       }    
     });
    }



</script>