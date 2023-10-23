<div id="page-wrapper" class="mn pn">
  <div class="container m-t-40 m-b-40">
    <div class="row m-b-30">
      <div class="col-md-8 col-md-offset-2 text-center">
      <img src="<?=$HOST?>/images/right_shield_purple.svg" width="70px" class="m-b-30" alt="">
      <p class="fs18">Congratulations! Your application has successfully been processed and you will receive a confirmation email once complete. Please see your application summary below.</p>
      </div>
    </div>
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default panel-block panel-title-block bg-transparent">
        <div class="panel-body pn transaction_receipt">
          <div class="row bg_white">
            <div class="col-sm-3 receipt_left">
              <div class="bg_dark_primary">
                <div class="panel-body">
                  <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);"><?=checkIsset($resOrder["user_type"]) == "Group"?"GROUP":"MEMBER"?></h4>
                  <p class="text-white mn"><span class="fw700  fs18"><?=checkIsset($resOrder["mbrName"])?></span><br><?=checkIsset($resOrder["mbrStatus"]) == "Post Payment" ? checkIsset($resOrder["leadDispId"]) : checkIsset($resOrder["mbrDispId"])?>
                  <br><?=format_telephone(checkIsset($resOrder["mbrPhone"]))?><br><?=checkIsset($resOrder["mbrEmail"])?></p>
                </div>
              </div>
                <?php if(empty($resOrder['billing_type'])) { ?>
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
                <?php } ?>
            </div>
            <div class="col-sm-9 receipt_right">
              <div class="p-10">
                <div class="clearfix m-b-30 m-t-10">
                  <?php if(empty($resOrder['billing_type'])) { ?>
                  <div class="pull-left">
                    <p class="<?=$txtClass?> fw500 mn"><?=$orderStatus?> <i class="fa <?=$iconClass?>" aria-hidden="true"></i></p>
                    <p class="text-gray fs10"><?=date("M d, Y h:i:s A",strtotime($resOrder["odrDate"]))?> CST</p>
                  </div>
                  <?php } ?>
                  <!-- <div class="pull-right">
                    <img src="<?=$HOST?>/images/logo.png" height="25px">
                  </div> -->
                </div>
                <?php if(in_array($orderStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){ ?>
                <p class="m-b-20"><strong class="text-action">Reason : </strong> <?=checkIsset($reason)?></p>
                <?php } ?>
                 <?php if(in_array($orderStatus, array("Post Payment")) && !empty($odrPostDate)){ ?>
                <p class="m-b-20"><strong>Date :</strong> <?=$odrPostDate?></p>
                <?php } ?>
                <div class="table-responsive br-n">
                  <?=get_order_summary_table($orderId,$memberId)?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    
  <div class="row m-t-30 m-b-30">
    <div class="col-md-8 col-md-offset-2 text-center">
    <a href="<?= $CUSTOMER_HOST ?>" class="btn btn-primary btn-block">Member Portal</a>
    </div>
  </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() { 
  var host = "<?=$HOST?>";
});

  $(function() {
   $('.receipt_left').matchHeight({
         target: $('.receipt_right'),
         property: 'min-height'
     });
   });
</script>