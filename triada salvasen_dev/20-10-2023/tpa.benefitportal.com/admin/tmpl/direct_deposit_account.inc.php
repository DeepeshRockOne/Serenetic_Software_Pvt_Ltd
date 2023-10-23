
<div class="panel panel-default panel-block  ">
  <div class="panel-heading">
    <div class="panel-title ">
      <p class="mn fs18 text-black lato_font fw600">Direct Deposit Accounts - <span class="fw300"><?=!empty($account_detail['bank_name']) ? $account_detail['bank_name'] : '-' ?></span> </p>
    </div>
  </div>
  <input type="hidden" name="acc_number" id="acc_number" value="<?=!empty($account_number) ? md5($account_number) : '' ?>">
  <input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_id?>">
  <input type="hidden" name="edate" id="edate" value="<?=!empty($account_detail['effective_date']) ? $account_detail['effective_date'] : '' ?>">
  <div class="panel-body">
    <div class="clearfix"></div>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Date Range</th>
            <th>Account Type</th>
            <th>Entity Name</th>
            <!-- <th>Status</th> -->
            <th>Routing Number</th>
            <th>Account Number</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($account_detail)) { ?>
          <tr>
            <td><?=getCustomDate($account_detail['effective_date'])?> - <?=getCustomDate($account_detail['termination_date']) != '-' ? $account_detail['termination_date'] : 'Present' ?></td>
            <td><?=$account_detail['account_type']?></td>
            <td><?=$account_detail['bank_name']?></td>
            <!-- <td><?=$account_detail['status']?></td> -->
            <td><?=$account_detail['routing_number']?></td>
            <td id="account_number">
              *<?=substr($account_detail['account_number'],-4)?>
            </td>
          </tr>
          <?php } else { ?>
            <tr>
              <td colspan="5">No details found!</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div id="password_info">
      <p class="m-b-20 m-t-15 text-center">To unlock the banking details, enter the 4-digit code below.</p>
      <div class="max-w200 margin-auto">
        <div class="phone-control-wrap">
          <div class="phone-addon pn">
            <input type="password" class="form-control" id="f_digit_code" data-placeholder="4 Digit Code" style="border-radius:0px;">
          </div>
          <div class="phone-addon w-65 ">
            <button class="btn btn-info" id="show_password" style="border-radius:0px;">Unlock</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="text-center m-t-10"> <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
</div>
<script type="text/javascript">
$(document).off("click","#show_password");
$(document).on("click","#show_password",function(){
  if($("#f_digit_code").val() !== ''){
    $("#ajax_loader").show();
    $.ajax({
			url:'direct_deposit_account.php',
      method : 'POST',
      data : {show_pass:"show_pass",f_digit_code:$("#f_digit_code").val(),number:$("#acc_number").val(),agent_id:$("#agent_id").val(),effective_date:$("#edate").val()},
      dataType:'json',
			success:function(res){
        $("#ajax_loader").hide();
        if(res.number !== undefined){
          $("#password_info").hide();
        }
				$("#account_number").text(res.number);
			}
		});
  }else{
    return false;
  }
});
</script>