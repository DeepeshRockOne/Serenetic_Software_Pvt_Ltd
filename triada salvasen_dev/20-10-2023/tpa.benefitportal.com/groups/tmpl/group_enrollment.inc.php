<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>
<div class="bg_white">
   <div class="section-padding">
      <div class="container">
         <div class="text-center">
            <p class="fs32 fw300 mb20"><strong>Hello</strong> <?= $group_full_name ?>,</p>
            <p class="fs18 m-b-40 p-b-30">Welcome to the <?= $DEFAULT_SITE_NAME ?> Group Application System. <i>You were referred to this page by <strong class="fw600 text-info"><?= $sponsor_full_name ?>.</strong></i></p>
         </div>
         <div class="row">
            <div class="col-md-10 col-md-offset-1">
               <div class="group_enroll_box">
                  <div class="group_enroll_header">
                     <div class="cust_tab_ui">
                        <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
                           <li class="active" data-tab="details_tab" id="li_details_tab">
                              <a data-toggle="tab" href="#details_tab" class="btn_step_heading enrollment_tabs" data-step="1">
                                 <div class="column-step ">
                                    <div class="step-number">1</div>
                                    <div class="step-title">Details</div>
                                 </div>
                              </a>
                           </li>
                           <li data-tab="billing_tab" id="li_billing_tab" class="disabled">
                              <a data-toggle="tab" href="#billing_tab" class="btn_step_heading enrollment_tabs" data-step="2">
                                 <div class="column-step">
                                    <div class="step-number">2</div>
                                    <div class="step-title">Operations/Billing</div>
                                 </div>
                              </a>
                           </li>
                           <li data-tab="agreement_tab" id="li_agreement_tab" class="disabled">
                              <a data-toggle="tab" href="#agreement_tab" class="btn_step_heading enrollment_tabs" data-step="3">
                                 <div class="column-step">
                                    <div class="step-number">3</div>
                                    <div class="step-title">Agreement</div>
                                 </div>
                              </a>
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="group_enroll_body">
                     <form action="<?=$GROUP_HOST?>/ajax_group_enrollment.php" role="form" method="post" name="enrollment_form" id="enrollment_form" autocomplete="false" enctype="multipart/form-data" novalidate>
                     <input type="hidden" name="sponsor_id" id="sponsor_id" value="<?= $sponsor_id ?>">
                     <input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>">
                     <input type="hidden" name="dataStep" id="dataStep" value="0">
                     <input type="hidden" name="action" id="action" value="">
                     <input type="hidden" name="submit_type" id="submit_type" value="">
                     <input type="hidden" name="company_count" id="company_count" value="">
                     <input type="hidden" name="signature_data" id="hdn_signature_data" value="">
                     <input type="hidden" name="signature_name" id="signature_name" value="">
                     <input type="hidden" name="is_valid_address" id="is_valid_address" value="<?= $is_valid_address ?>">
                     <input type="hidden" name="is_valid_billing_address" id="is_valid_billing_address" value="<?= checkIsset($is_valid_billing_address) ?>">
                     <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
                     <input type="hidden" name="is_bill_address_ajaxed" id="is_bill_address_ajaxed" value="">
                     <div class="tab-content mn">
                        <div id="details_tab" class="tab-pane fade in active">
                           <?php include ('enrollment_details_tab.inc.php'); ?>
                        </div>
                        <div id="billing_tab" class="tab-pane fade">
                           <?php include ('enrollment_billing_tab.inc.php'); ?>
                        </div>
                        <div id="agreement_tab" class="tab-pane fade ">
                           <?php include ('enrollment_agreement_tab.inc.php'); ?>
                        </div>
                     </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
         </div>
          <div class="verification_banner group_enrollment_hero pr" style="background-image: url(<?= $GROUP_HOST ?>/images/group_contract_bg.jpg?_v=1.00);">
   </div>
      
   </div>
</div>
</div>

<div class="" id="display_popup_content" style="display:none">
   <div class="panel-heading p-t-10 p-b-5 ">
         <div class="panel-title">
           <p class=" text-blue fs20 fw500 p-l-10">Display</p>
         </div>
       </div>
   <div class="panel-body p-t-0">
     <p class="m-b-25 p-l-10" style="font-size:13px">If box is checked, your members will not be able to see your name, phone, and email inside their member portal. See example below:</p>
     <div class="display_agent_info">
       <div class="p-l-10"><i class="fa fa-info-circle fs18 text-action" style="vertical-align: middle;" aria-hidden="true"></i> <span class="text-black fw500 fs15"> &nbsp; Your Group &nbsp;|&nbsp;</span><?=$resSponsor['fname']." ".$resSponsor['lname']?>&nbsp; | &nbsp;<?=checkIsset($resSponsor['cell_phone']) ? format_telephone($resSponsor['cell_phone']) :"" ?>&nbsp; | &nbsp;<?=$resSponsor['email']?> </div>
     </div>
     <div class="clearfix m-t-20 text-center">
       <a href="javascript:void(0);" class="btn red-link pn" onclick="$.colorbox.close()">Close</a>
     </div>
   </div>
 </div>
<?php  //jquery code file; 
include_once __DIR__ . '/group_enrollment_jquery.inc.php'; 
?>

