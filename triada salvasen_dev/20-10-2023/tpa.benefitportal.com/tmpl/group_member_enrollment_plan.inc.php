<?php include_once 'layout/group.enroll.header.inc.php'; ?>
<div class="group-enrollment-complete">
   <div class="container-fluid">
      <div class="text-center">
         <h2 class="m-t-0 m-b-30 font-bold">Congratulations <span class="text-primary"><?= $memberName?></span>, you have finished enrollment!</h2>
         <p class="mn"><?=!empty($response) ? "Your Selected Benefits" : "You Waived All Products" ?></p>
      </div>
   </div>
</div>
<?php if(!empty($response)){?>
<div class="enrollment-complete-plan">
   <div class="container-fluid">
      <div class="row">
         <?php foreach ($response as $prdVal) { ?>
            <?php
               if(!empty($prdVal['dependentName'])){
                  $dependentName = $prdVal['dependentName'];
               }
            ?>
         <div class="col-sm-6 col-md-4">
            <div class="final-plan-block">
               <div class="row m-b-25">
                  <div class="col-xs-10">
                     <h4 class="mn"><?= $prdVal['productName']?></h4>
                     <p class="mn"><?= $prdVal['offeringCompany']?></p>
                  </div>
                  <div class="col-xs-2">
                     <div class="text-right">
                        <a href="<?php echo $HOST.'/policy_document.php?userType=enrollment&customer_id='.$prdVal['customerId'].'&ws_id='.$prdVal['ws_id'] ?>" data-toggle="tooltip" data-container="body" title="Download Details" data-placement="bottom" class="text-action"><i class="fa fa-cloud-download fa-lg"></i></a>
                     </div>
                  </div>
               </div>
               <p class="mn"><?= $prdVal['planType']?></p>
               <p class="mn text-primary"><?= $memberName?><?= isset($dependentName)?', '.$dependentName:'';?></p>
            </div>
         </div>
         <?php } ?>
      </div>
   </div>
</div>
<div class="enrollment-helpinfo">
   <div class="container-fluid">
      <div class="phone-control-wrap">
         <div class="phone-addon w-90">
            <img src="<?=$GROUP_HOST?>/images/info-guy.png" class="img-responsive">
         </div>
         <div class="phone-addon text-left">
            <h4 class="m-t-0 font-bold">Helpful things to do right now</h4>
            <div class="helpinfo-list">
               <ul>
                  <li>Look for email with login information (check junk mail)</li>
                  <li>Download Your Plan(s) details above</li>
                  <li class="mn">Login to your Member Portal</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="text-center m-t-40">
         <a href="<?=$CUSTOMER_HOST?>" class="btn btn-action">Member Portal Login</a>
         <a href="javascript:void(0);" class="btn red-link closeApplication">Close</a>
      </div>
   </div>
</div>
<?php }else{ ?>
<div style="height: 20px;" class="bg-success"></div>
<div class="container-fluid">
   <div class="enrollment-helpinfo">
      <div class="phone-control-wrap">
         <div class="phone-addon w-90">
            <img src="<?=$GROUP_HOST?>/images/info-guy.png" class="img-responsive">
         </div>
         <div class="phone-addon text-left">
            <div class="p-l-20">
               <h4 class="mn font-bold fs18">Thank you for taking the time to fill out the group enrollment</h4>
            </div>
         </div>
      </div>
      <div class="text-center m-t-40 p-t-30">
      <a href="javascript:void(0);" class="btn btn-action closeApplication">Close Application</a>
   </div>
   </div>
</div>
<?php } ?>
<style type="text/css">
   .group-enroll .footer{left: 0px;}
</style>
<script>
   $(document).off("click",".closeApplication");
   $(document).on("click",".closeApplication",function(e){
      e.preventDefault();
      window.location.href = '<?=$pageBuilderLink?>';
   });
</script>
<?php include_once 'layout/group.enroll.footer.inc.php';?>