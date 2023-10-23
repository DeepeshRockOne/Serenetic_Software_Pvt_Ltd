<div class="panel panel-default panel-block panel-title-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18">
        <strong class="fw500">Receipt  - </strong> <span class="fw300">Order <?=checkIsset($orderDispId)?></span>
      </p>
    </div>
  </div>
  <div class="panel-body pn transaction_receipt">
    <div class="row">
      <div class="col-sm-3 receipt_left">
        <div class="bg_dark_primary">
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">MEMBER</h4>
            <p class="text-white mn"><span class="fw700  fs18"><?=checkIsset($resTrans["mbrName"])?></span><br><?=checkIsset($resTrans["mbrDispId"])?><br><?=format_telephone(checkIsset($resTrans["mbrPhone"]))?><br><?=checkIsset($resTrans["mbrEmail"])?></p>
          </div>
        </div>
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">BILLING ADDRESS</h4>
            <p class="text-white mn">
                <?=checkIsset($billArr["address"])?>
                <br/>
                <?php if(!empty($billArr["address2"])){
                  echo $billArr["address2"]; ?>
                  <br/>
                <?php } ?>
                <?=checkIsset($billArr["city"])?>, <?=checkIsset($allStateShortName[$billArr["state"]])?>
                <br/> 
                <?=checkIsset($billArr["zip"])?>
              </p>
          </div>
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Payment</h4>
            <p class="text-white mn"><?=displayAmount($transTotal)?> (<?=checkIsset($billType)?> *<?=checkIsset($billArr["last_cc_ach_no"])?>)</p>
          </div>
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">TRANSACTION INFO</h4>
            <p class="text-white mn"><?=checkIsset($resTrans["processorTransId"])?><br><?=checkIsset($transDate)?></p>
          </div>
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">ORDER ID</h4>
            <p class="text-white mn"><?=$orderDispId?></p>
          </div>
      </div>
      <div class="col-sm-9 receipt_right">
        <div class="p-10">
          <div class="clearfix m-b-30 m-t-10">
            <div class="pull-left">
              <p class="<?=$txtClass?> fw500 mn"><?=$transStatus?> <i class="fa <?=$iconClass?>" aria-hidden="true"></i></p>
              <p class="text-gray fs10"><?=$tz->getDate($resTrans["transactionDate"])?></p>
            </div>
            <div class="pull-right">
              <h4>RECEIPT</h4>
            </div>
          </div>
          <?php if(in_array($transStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){ ?>
          <p class="m-b-20"><strong class="text-danger">Reason : </strong> <?=checkIsset($reason)?></p>
          <?php } ?>
           <?php if(in_array($transStatus, array("Post Payment")) && !empty($odrPostDate)){ ?>
          <p class="m-b-20"><strong>Date :</strong> <?=$odrPostDate?></p>
          <?php } ?>
          <p class="fw500"> Summary</p>
          <div class="table-responsive">
            <table class="table table-borderless table-striped <?=$tblClass?>">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Plan Period</th>
                  <th>Plan</th>
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if(!empty($detRes)){
                  $fee_prd_res = array();
                  foreach ($detRes as $key => $order) {
                    if($order["type"] == 'Fees'){
                      if($order["product_type"] == "Healthy Step"){
                        $stepFeePrice = $order["price"];
                        $stepFeeRefund= $order["is_refund"];
                        continue;
                      }
                      if($order["product_type"] == "ServiceFee"){
                        $serviceFeePrice = $order["price"];
                        $serviceFeeRefund = $order["is_refund"];
                        continue;
                      }
                      $fee_prd_res[] = $order;
                      continue;
                    }else{
                      $subTotal += $order["price"];
                    }
                ?>
              <?php if($transStatus != 'Payment Approved'){ ?>
                <tr>
              <?php }elseif($transStatus == 'Payment Approved'){ ?>
                <tr class="<?=$order['is_refund'] == 'Y' ? 'text-danger' : ''?>">
              <?php } ?>
                  <td><?=$order["product_name"]?></td>
                  <td><?=date("m/d/Y",strtotime($order["start_coverage_period"]))?> - <?=date("m/d/Y",strtotime($order["end_coverage_period"]))?></td>
                  <td><?=$order["planTitle"]?></td>
                  <td class="text-right"><?=displayAmount($order["price"])?></td>
                </tr>
                <?php
                  }
                  foreach ($fee_prd_res as $key => $fee_prd_row) {
                    $subTotal += $fee_prd_row["price"];
                    ?>
                  <?php if($transStatus != 'Payment Approved'){ ?>
                    <tr>
                  <?php }elseif($transStatus == 'Payment Approved'){ ?>
                    <tr class="<?=$fee_prd_row['is_refund'] == 'Y' ? 'text-danger' : ''?>">
                  <?php } ?>
                      <td><?=$fee_prd_row["product_name"]?></td>
                      <td></td>
                      <td>Fees</td>
                      <td class="text-right"><?=displayAmount($fee_prd_row["price"])?></td>
                    </tr>
                    <?php
                  }
                }
                ?>
              </tbody>
            </table>
            <table class="table table-borderless pull-right receipt_table m-t-20" style="max-width: 250px;">
              <tbody>
                <tr class="<?=$transStatus == 'Refund' ? 'text-danger' : ''?>">
                  <td>SubTotal(s)</td>
                  <td class="text-right"><?=displayAmount($subTotal)?></td>
                </tr>
                <tr class="<?=$stepFeeRefund == 'Y' ? 'text-danger' : ''?>">
                  <td>Healthy Step(s)</td>
                  <td class="text-right"><?=displayAmount($stepFeePrice)?></td>
                </tr>
                <tr class="<?=$serviceFeeRefund == 'Y' ? 'text-danger' : ''?>">
                  <td>Service Fee(s)</td>
                  <td class="text-right"><?=displayAmount($serviceFeePrice)?></td>
                </tr>
                <tr class="<?=$transStatus == 'Refund' ? 'text-danger' : ''?>">
                  <td class="fw500">Total</td>
                  <td class="text-right fw500"><?=displayAmount($transTotal)?></td>
                </tr>
                <?php if($transStatus == 'Refund'){ ?>
                 <hr>
                 <tr class="<?=$transStatus == 'Refund' ? 'text-danger' : ''?>">
                  <td class="fw500">Refund</td>
                  <td class="text-right fw500">(<?=displayAmount($transTotal)?>)</td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
          <div class="theme-form m-t-20">
              <div class="form-group height_auto">
                  <select class="form-control" id="delvrReceipt" data-transId="<?=md5($transId)?>">
                    <option></option>
                    <option value="emailReceipt">Email</option>
                    <option value="downloadReceipt">Download</option>
                  </select>
                  <label>Delivery Method</label>
                </div>
              <div class="phone-control-wrap">
                <div class="phone-addon" id="emailDiv"  style="display: none;">
                  <div class="form-group">
                  <input type="text" name="email" class="form-control" id="email" value="<?=$mbrEmail?>">
                  <label>Email</label>
                </div>
                </div>
                <div class="phone-addon w-70 v-align-top"  id="btnDiv" style="display: none;">
                    <div class="form-group height_auto">
                      <a href="javascript:void(0);" class="btn btn-info sendReceipt">Send</a>
                    </div>
                 </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() { 
  var host = "<?=$HOST?>";

  $('select.form-control').selectpicker({
    container: 'body',
    style: 'btn-select',
    noneSelectedText: '',
    dropupAuto: true
  });
  $('select.form-control').selectpicker('refresh');
  
  $("#delvrReceipt").change(function(){
    var delvrMethod = $(this).val();
    $("#emailDiv").hide();
    $("#btnDiv").hide();
    
    if(delvrMethod != ''){
      $("#btnDiv").show();
      if(delvrMethod=="emailReceipt"){
        $("#emailDiv").show();
        $(".sendReceipt").html("Send");
      }else{
        $(".sendReceipt").html("Download");
      }
      $.fn.matchHeight._update();
    }
    
  });
  
  $(".sendReceipt").click(function(){

    var isSend = true;
    var action = $("#delvrReceipt option:selected").val();
    var transId = $("#delvrReceipt").attr("data-transId");
    var email = $("#email").val();
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if(action != ''){
      if(action == "emailReceipt"){
        if(email == '' || !emailReg.test(email)){
        alert("Valid email is required...");
        isSend = false;
        }
      }
    }else{
      alert("Please Select Delivery Method");
      isSend = false;
    }
    if(isSend === true){

      window.location = host+"/download_transaction_receipt.php?transId="+transId+"&action="+action+"&email="+email;
    }
  });
  
});

  $(function() {
    $('.receipt_left').matchHeight({
       target: $('.receipt_right'),
       property: 'min-height'
    });
  });
</script>