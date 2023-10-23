<?php
if(!isset($display_default_billing)) {
   $display_default_billing = 'N';
}
if(!isset($is_group_member)) {
   $is_group_member = 'N';
   if($enrollmentLocation == "groupSide") {
      $is_group_member = 'Y';
   }
}
if(!isset($sponsor_billing_method)) {
   $sponsor_billing_method = 'individual';
   if($is_group_member == 'Y' && !empty($group_billing_method)) {
      $sponsor_billing_method = $group_billing_method;
   }
}
?>
<form method="POST" action="<?=$HOST?>/ajax_group_member_enrollment.php" name="frmGroupMemberEnrollment" id="frmGroupMemberEnrollment" enctype="multipart/form-data" novalidate>
   <input type="hidden" name="group_billing_method" value="<?=isset($group_billing_method) ? $group_billing_method : ""?>">
   <input type="hidden" name="already_puchase_product" id="already_puchase_product" value="">
   <input type="hidden" name="enrollmentLocation" id="enrollmentLocation" value="<?= $enrollmentLocation ?>">
   <input type="hidden" name="isGroupMember" id="isGroupMember" value="<?= $is_group_member ?>">
   <input type="hidden" name="site_user_name" id="site_user_name" value="<?= isset($group_id)?$group_id:'' ?>">
   <input type="hidden" name="pb_id" id="pb_id" value="<?= isset($pb_id)?$pb_id:'0';?>">
   <input type="hidden" name="step" id="step" value="1">
   <input type="hidden" name="leadId" id="lead_id" value="<?= isset($lead_id)?$lead_id:0?>">
   <input type="hidden" name="memberId" id="member_id" value="<?=$member_id?>">
   <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id?>">
   <input type="hidden" name="md5_customer_id" id="md5_customer_id" value="">
   <input type="hidden" name="md5_order_id" id="md5_order_id" value="">
   <input type="hidden" name="payment_type" id="payment_type" value="">
   <input type="hidden" name="groupId" id="group_id" value="<?=$group_id?>">
   <input type="hidden" name="last_selected_product" id="last_selected_product" value="0">
   <input type="hidden" name="sponsor_id" id="sponsor_id" value="<?=$getGroupDetails['id']?>">
   <input type="hidden" name="last_billing_profile_id" id="last_billing_profile_id" value="0">
   <input type="hidden" name="order_id" id="order_id" value="<?php echo isset($order_id) && $order_id>0?$order_id:""; ?>">
   <input type="hidden" name="enrollment_type" value="<?= checkIsset($enrollment_type) ?>"/>
   <input type="hidden" name="upload_type" id="upload_type" value="">
   <input type="hidden" name="is_address_verified" id="is_address_verified" value="<?= !empty($is_address_verified) ? $is_address_verified : 'N' ?>">
   <input type="hidden" name="isValidAddress" id="is_valid_address" value="<?= !empty($is_valid_address) ? $is_valid_address : 'N' ?>">
   <input type="hidden" name="product_list" id="product_list" value="">
   <input type="hidden" name="gap_plus_product_list" id="gap_plus_product_list" value="">
   <input type="hidden" name="lead_quote_detail_id" id="lead_quote_detail_id" value="<?= isset($lead_quote_detail_id)?$lead_quote_detail_id:"" ?>">
   <input type="hidden" name="lead_quote_plan_ids" id="lead_quote_plan_ids" value="<?= isset($lead_quote_plan_ids)?$lead_quote_plan_ids:"" ?>">
   <input type="hidden" name="is_add_product" id="is_add_product" value="<?=checkIsset($is_add_product)?>">
   <input type="hidden" name="added_product" id="added_product" value="">
   <input type="hidden" name="enrolleeElementsVal" id="enrolleeElementsVal" value="">
   <input type="hidden" name="api_key" id="api_key" value="enrollmentSubmit">
   <input type="hidden" name="elected_bundle" id="elected_bundle" value="">
   <input type="hidden" name="is_elected" id="is_elected" value="N">
   <input type="hidden" name="fromStep" id="fromStep" value="">
   <input type="hidden" name="dependent_array" id="dependent_array" value="">
   <input type="hidden" name="only_waive_products" id="only_waive_products" value="N">
   <input type="hidden" name="billing_display" id="billing_display" value="Y">
   <input type="hidden" name="addingBundleProductToSelfGuiding" id="addingBundleProductToSelfGuiding" value="0">
   <input type="hidden" name="homepay_zipcode" id="homepay_zipcode_form" value="">
   <input type="hidden" name="homepay_products" id="homepay_products_form" value="">
   <input type="hidden" name="homepay_plan_ids" id="homepay_plan_ids_form" value="">
   <input type="hidden" name="gap_payroll_type" id="gap_payroll_type_form" value="">
   <input type="hidden" name="gap_payroll_type_salary" id="gap_payroll_type_salary_form" value="">
   <input type="hidden" name="gap_marital_status" id="gap_marital_status_form" value="">
   <input type="hidden" name="gap_pay_frequency" id="gap_pay_frequency_form" value="<?= $gap_pay_frequency_form ?>">
   <input type="hidden" name="gap_default_allowances_federal" id="gap_default_allowances_federal_form" value="">
   <input type="hidden" name="pre_tax_deductions" id="pre_tax_deductions_form" value="">
   <input type="hidden" name="post_tax_deductions" id="post_tax_deductions_form" value="">

   <div class="group_member_enroll">
      <div class="theme-form">
         <div id="coverage_detail" class="enrollmentDiv" data-step='1'>
            <?php include ('group_member_enrollment_coverage_detail_tab.inc.php'); ?>
         </div>
         <div id="enrollee_question" data-step='2' class="enrollmentDiv" style="display: none;">
            <div id="htmlcustomquestion"></div>
         </div>
         <div id="recommended_benefit" data-step='3' class="enrollmentDiv" style="display: none;">
            <div id="htmlrecommended_benefit"></div>
         </div>
         <div id="self_guiding_benefits" data-step='4' class="enrollmentDiv" style="display: none;">
         </div>
         <div id="application" data-step='5' class="enrollmentDiv" style="display: none;">
            <?php //include ('group_member_enrollment_basic_detail_tab.inc.php'); ?>
            <div id="primary_member_field_div"></div>
         </div>
         <div id="payment_summary" data-step='6' class="enrollmentDiv" style="display: none;">
            <?php include ('group_member_enrollment_summary_tab.inc.php'); ?>
            <?php include ('group_member_enrollment_payment_tab.inc.php'); ?>
            <?php include ('group_member_enrollment_verification_mode.inc.php'); ?>
            <div class="clearfix">
            <button type="button" class="btn btn-action form_submit" data-step="6" >SUBMIT</button>
               <button class="enrollmentLeftmenuItem" id="applicationTabBack"><a href="javascript:void(0);" class="btn red-link" data-step="5">Back</a></button>
            </div>
         </div>
         <div id="verification_detail" data-step='7' class="enrollmentDiv" style="display: none;">
            <?php include ('enrollment_verification_tab.inc.php'); ?>
         </div>
      </div>
   </div>
</form>
<?php include_once __DIR__ . '/group_member_enrollment_clone_data_file.inc.php'; ?>

<div style="display: none">
  <div id="suggestedAddressPopup">
    <?php include('suggested_address.inc.php'); ?>
  </div>
</div>

<div style="display: none">
  <div id="takeHomePayDiv">
    <?php include('take_homepay_calculator.inc.php'); ?>
  </div>
</div>

<?php include_once __DIR__ . '/exact_estimate.inc.php'; ?>
<?php include_once __DIR__ . '/product_descriptions_clone_file.inc.php';  ?>
<?php include('group_member_enrollment_js.inc.php'); ?>
