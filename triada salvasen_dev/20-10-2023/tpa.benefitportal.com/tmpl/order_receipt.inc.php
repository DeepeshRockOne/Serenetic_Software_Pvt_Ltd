<div class="panel panel-default panel-block panel-title-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18">
        <strong class="fw500">Receipt  - </strong> <span class="fw300">Order <?=$orderDispId?></span>
      </p>
    </div>
  </div>
  <div class="panel-body pn transaction_receipt">
    <div class="row">
      <div class="col-sm-3 receipt_left">
        <div class="bg_dark_primary">
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);"><?=$resOrder["user_type"] == "Group"?"GROUP":"MEMBER"?></h4>
            <p class="text-white mn"><span class="fw700  fs18"><?=checkIsset($resOrder["mbrName"])?></span><br><?=checkIsset($resOrder["mbrDispId"])?><br><?=format_telephone(checkIsset($resOrder["mbrPhone"]))?><br><?=checkIsset($resOrder["mbrEmail"])?></p>
          </div>
        </div>
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">BILLING ADDRESS</h4>
            <p class="text-white mn">
                <?=checkIsset($resOrder["billAdd"])?>
                <br/>
                <?php if(!empty($resOrder["billAdd2"])){
                  echo $resOrder["billAdd2"]; ?>
                  <br/>
                <?php } ?>
                <?=checkIsset($resOrder["billCity"])?>, <?=checkIsset($allStateShortName[$resOrder["billState"]])?>
                <br/> 
                <?=checkIsset($resOrder["billZip"])?>
              </p>
          </div>
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Payment</h4>
            <p class="text-white mn"><?=displayAmount($grandTotal)?> (<?=checkIsset($billType)?> *<?=checkIsset($resOrder["lastPayNo"])?>)</p>
          </div>
          <div class="panel-body">
            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">TRANSACTION INFO</h4>
            <p class="text-white mn"><?=checkIsset($resOrder["transactionId"])?><br><?=checkIsset($resOrder["transactionDate"])?></p>
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
              <p class="<?=$txtClass?> fw500 mn"><?=$orderStatus?> <i class="fa <?=$iconClass?>" aria-hidden="true"></i></p>
              <p class="text-gray fs10"><?=date("M d, Y h:i:s A",strtotime($resOrder["odrDate"]))?> CST</p>
            </div>
            <div class="pull-right">
              <h4>RECEIPT</h4>
            </div>
          </div>
          <?php if(in_array($orderStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){ ?>
          <p class="m-b-20"><strong class="text-danger">Reason : </strong> <?=checkIsset($reason)?></p>
          <?php } ?>
           <?php if(in_array($orderStatus, array("Post Payment")) && !empty($odrPostDate)){ ?>
          <p class="m-b-20"><strong>Date :</strong> <?=$odrPostDate?></p>
          <?php } ?>
          <?=get_order_summary_table($orderId)?>
          <div class="theme-form m-t-20">
              <div class="form-group height_auto">
                  <select class="form-control" id="delvrReceipt" data-odrId="<?=$odrId?>">
                    <option></option>
                    <option value="emailReceipt">Email</option>
                    <option value="downloadReceipt">Download</option>
                  </select>
                  <label>Delivery Method</label>
                </div>
              <div class="phone-control-wrap">
                <div class="phone-addon" id="emailDiv"  style="display: none;">
                  <div class="form-group">
                  <input type="text" name="email" class="form-control" id="email">
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
        $.fn.matchHeight._update();
      }else{
        $(".sendReceipt").html("Download");
        $.fn.matchHeight._update();
      }
    }
    
  });
  
  $(".sendReceipt").click(function(){
    var isSend = true;
    var action = $("#delvrReceipt option:selected").val();
    var odrId = $("#delvrReceipt").attr("data-odrId");
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
      window.location = host+"/download_order_receipt.php?orderId="+odrId+"&action="+action+"&email="+email;
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