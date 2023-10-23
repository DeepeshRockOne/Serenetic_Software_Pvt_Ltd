<div class="theme-form">
<div class="section_space gray-bg">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="MemberPaymentSubscriptionType" id="matchGlobal_MemberPaymentSubscriptionType" <?= empty($match_globals) || in_array("MemberPaymentSubscriptionType",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title pull-left">Plan Type
  	  <a href="prd_history.php" data-type="Member Payment Subscription Type" class="popup_lg">
  		  <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
      </a>
  </h4>  
  <div class="clearfix"></div>
  <p class="m-b-25"><em>Elect payment type for this product.</em></p>
  <div class="m-b-25">
    <div class="radio-v">
      <label><input name="member_payment" <?= isset($member_payment) && $member_payment=="Single" ? 'checked' : '' ?> type="radio" value="Single" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberPaymentSubscriptionType"/> Single Payment</label>
    </div>
    <div class="radio-v">
      <label><input name="member_payment" <?= isset($member_payment) && $member_payment=="Recurring" ? 'checked' : '' ?> type="radio" value="Recurring" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberPaymentSubscriptionType"/> Recurring Payment</label>
    </div>
    <p class="error" id="error_member_payment"></p>
 </div>
    <div id="member_payment_div" style="<?= isset($member_payment) && $member_payment=="Recurring" ? '' :'display:none' ?>">
      <div class="row">
        <div class="col-sm-4">
          <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberPaymentSubscriptionType" name="member_payment_type" id="member_payment_type">
            <option value="" hidden selected="selected"></option>
            <option value="Monthly" <?= isset($member_payment) && $member_payment_type=="Monthly" ? 'selected=selected' : '' ?>>Monthly</option>
            <option value="Annually" <?= isset($member_payment) && $member_payment_type=="Annually" ? 'selected=selected' : '' ?>>Annually</option>
          </select>
          <label>Select</label>
          <p class="error" id="error_member_payment_type"></p>
        </div>
      </div>
    </div>
</div>
<div class="section_space gray-bg">
  
  <h4 class="h4_title pull-left">Pricing
  	  <a href="prd_history.php" data-type="Pricing" class="popup_lg">
  		<i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
      </a>
  </h4>
    
  <div class="clearfix"></div>
  <p class="m-b-25"><em>Select type of pricing model.</em></p>
  <div class="m-b-25">
    <div class="radio-v">
      <?php if(!($allowPricingUpdate) && isset($pricing_model)){ ?>
          <input type="hidden" name="allow_pricing_model" value="<?= $pricing_model ?>">
      <?php } ?>
      <label>
        <input  name="pricing_model" <?= isset($pricing_model) && $pricing_model=="FixedPrice" ? 'checked' : (!($allowPricingUpdate) ? 'disabled' : '') ?> type="radio" value="FixedPrice" />
      Fixed Pricing</label>
    </div>
    <div class="radio-v">
      <label>
        <input  name="pricing_model" <?= isset($pricing_model) &&  $pricing_model=="VariablePrice" ? 'checked' : (!($allowPricingUpdate) ? 'disabled' : '') ?> type="radio" value="VariablePrice" />
      Variable by Plan Tier </label>
    </div>
     <div class="radio-v">
      <label>
        <input  name="pricing_model" <?= isset($pricing_model) &&  $pricing_model=="VariableEnrollee" ? 'checked' : (!($allowPricingUpdate) ? 'disabled' : '') ?> type="radio" value="VariableEnrollee" />
      Variable by Enrollee </label>
    </div>
  </div>
  <p class="error" id="error_pricing_model"></p>
  

  <div id="fixed_price_div" style="<?= isset($pricing_model) && $pricing_model=="FixedPrice" ? '' :'display:none' ?>" class="m-b-25">
    <div id="benefit_tier_pricing_main_div">
      <?php if(!empty($FixedPriceArr)) { ?>
        <?php 

          $totalPriceGroup = count($FixedPriceArr); 

          $count = 1;
        ?>
        <?php foreach ($FixedPriceArr as $matrix_group => $matrixRow) {  ?>
          <?php 

            $today_date = date($DATE_FORMAT);
            $effectiveDate = $matrixRow['pricing_effective_date'];
            $terminationDate = $matrixRow['pricing_termination_date'];
            $effectiveReadonly = "";
            $termReadonly = "";
            if(!empty($terminationDate) && (
              strtotime($terminationDate) < strtotime($effectiveDate) ||
              strtotime($terminationDate) < strtotime($today_date))
            ){
              $termReadonly = "readonly";
            }

            if(!empty($effectiveDate) && (strtotime($effectiveDate) < strtotime($today_date))){
              $effectiveReadonly = "readonly";
              
            }
          ?>
          <div id="inner_pricing_div_<?= $matrix_group ?>" data-id="<?= $matrix_group ?>" class="inner_pricing_div">
            <div class="row m-t-25">
              <div class="col-md-2 col-sm-6">
                <h5 class="h5_title m-b-10">Plan Tier</h5>
              </div>
              <div class="col-md-2 col-sm-6">
                 <h5 class="h5_title m-b-10">Fixed Prices</h5>
              </div>
            </div>
            <?php if(!empty($prdPlanTypeArray)) {?>
              <?php foreach ($prdPlanTypeArray as $key => $tier) { ?>
                <div class="priceTier_<?= $tier['id'] ?>" style="<?= array_key_exists($tier['id'], $matrixRow) ? '' :'display: none'; ?>">
                  <div class="row">
                    <div class="col-md-2 col-sm-6">
                      <p class="m-b-15"><?= $tier['title'] ?></p>
                    </div>
                    <div class="col-md-2 col-sm-6">
                       <div class="form-group ">
                         <input type="text" name="pricing_fixed_price[<?= $matrix_group ?>][<?= $tier['id'] ?>][Retail]" class="form-control formatPricing caculatePricing"  data-id="<?= $matrix_group ?>" data-tier-id="<?= $tier['id'] ?>" value="<?= $matrixRow[$tier['id']]['price'] ?>" <?=$effectiveReadonly?>>
                         <label>Retail Price</label>
                         <p class="error" id="error_pricing_fixed_price_<?= $matrix_group ?>_<?= $tier['id'] ?>"></p>
                       </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                       <div class="form-group ">
                         <input type="text" name="pricing_fixed_price[<?= $matrix_group ?>][<?= $tier['id'] ?>][NonCommissionable]" class="form-control formatPricing caculatePricing" data-id="<?= $matrix_group ?>" data-tier-id="<?= $tier['id'] ?>" value="<?= $matrixRow[$tier['id']]['non_commission_amount'] ?>" <?=$effectiveReadonly?>>
                         <label>Non-Commissionable Price</label>
                       </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                       <div class="form-group ">
                         <input type="text" name="pricing_fixed_price[<?= $matrix_group ?>][<?= $tier['id'] ?>][Commissionable]" class="form-control formatPricing" data-id="<?= $matrix_group ?>" data-tier-id="<?= $tier['id'] ?>" readonly value="<?= $matrixRow[$tier['id']]['commission_amount'] ?>">
                         <label>Commissionable Price</label>
                       </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            <?php } ?>
            <div class="row">
              <div class="col-sm-2 hidden-sm"></div>
              <div class="col-md-4 col-sm-6">
                <div class="form-group ">
                  <div class="input-group">
                    <span class="input-group-addon datePickerIcon" data-applyon="pricing_effective_date_<?= $matrix_group ?>">
                      <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                    <div class="pr">
                    <input  type="text" class="form-control pricingEffectiveDate pricingDates"  name="pricing_effective_date[<?= $matrix_group ?>]"  id="pricing_effective_date_<?= $matrix_group ?>" data-id="<?= $matrix_group ?>" value="<?= $effectiveDate ?>" <?= $termReadonly ?> <?=$effectiveReadonly?>>
                    <label>Effective Date (MM/DD/YYYY)</label>
                  </div>
                  </div>
                  <p class="error" id="error_pricing_effective_date_<?= $matrix_group ?>"></p>
                </div>
              </div>
              <div class="col-md-4 col-sm-6">
                <div class="form-group ">
                  <div class="input-group">
                    <span class="input-group-addon datePickerIcon" data-applyon="pricing_termination_date_<?= $matrix_group ?>">
                      <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                    <div class="pr">
                      <input  type="text" class="form-control pricingTerminationDate <?= empty($termReadonly) ?'checkTermed' : '' ?> pricingDates" name="pricing_termination_date[<?= $matrix_group ?>]" id="pricing_termination_date_<?= $matrix_group ?>" data-id="<?= $matrix_group ?>" value="<?= $terminationDate ?>" <?= $termReadonly ?>>
                      <label>Termination Date (MM/DD/YYYY)</label>
                    </div>
                  </div>
                  <p class="error" id="error_pricing_termination_date_<?= $matrix_group ?>"></p>
                </div>
              </div>
              <div class="col-sm-2" id="terminationDateClear_<?= $matrix_group ?>" style="<?= $totalPriceGroup > 1 && $totalPriceGroup!=$count && $termReadonly=='' ? '' : 'display: none'?>">
                <a href="javascript:void(0)" class="btn red-link clearPricingDiv" data-id="<?= $matrix_group ?>">Clear</a></div>
            </div>
            <div class="pricing_setting_div" data-id="<?= $matrix_group ?>">
              <?php if($totalPriceGroup==$count && $count > 1) { ?>
                <div class="row">
                  <div class="col-md-12">
                    <p>Should this new pricing be applied on renewals of existing business?</p>
                    <div class="radio-v">
                      <label>
                        <input class="newPricingOnRenewals"  name="newPricingOnRenewals[<?= $matrix_group ?>]" type="radio" value="N" <?= isset($matrixRow['is_new_price_on_renewal']) && $matrixRow['is_new_price_on_renewal'] =='N' ? 'checked' : '' ?>> No</label>
                    </div>
                    <div class="radio-v">
                      <label>
                        <input class="newPricingOnRenewals"  name="newPricingOnRenewals[<?= $matrix_group ?>]" type="radio" value="Y" <?= isset($matrixRow['is_new_price_on_renewal']) && $matrixRow['is_new_price_on_renewal'] =='Y' ? 'checked' : '' ?>> Yes</label>
                    </div>
                    <p class="error" id="error_newPricingOnRenewals_<?= $matrix_group ?>"></p>
                  </div>            
                </div>
              <?php } ?>
            </div>
          </div>
          <?php $count++ ;?>
        <?php } ?>
      <?php } ?>
      
    </div>
  </div>
  <div id="variable_price_div" style="<?= isset($pricing_model) &&  $pricing_model=="VariablePrice" ? '' :'display:none' ?>">
    <div class="m-b-25 clearfix ">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <p class="m-b-25"><strong>Select which criteria you need for pricing</strong></p>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
        <?php if(!empty($prdPricingQuestionRes)) { ?>
          <?php $rowCount=0; ?>
          <?php foreach ($prdPricingQuestionRes as $priceKey => $priceRow) { ?>
            <?php 
              $label = $priceRow['label'];
              $displayLabel = $priceRow['display_label'];
              $controlType = $priceRow['type'];
            ?>
            <?php if($rowCount!=0 && $rowCount%4 == 0){ ?>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
        <?php } ?>
        <div class="list_label" style="width:100%">
          <?php if(!($allowPricingUpdate) && (!empty($price_control) && in_array($priceRow['id'], $price_control))){ ?>
              <input type="hidden" name="allow_price_control[]" value="<?= $priceRow['id'] ?>">
          <?php } ?>
          <label><input name="price_control[]" class="price_control_matrix <?= $controlType=="Spouse" && $displayLabel != 'Has Spouse' ? 'spouseControl'  : ''?> <?= ( $displayLabel=='Has Spouse') ?'HasSpouse' :'' ?>"  type="checkbox" value="<?= $priceRow['id'] ?>" <?= $controlType=="Spouse" && $displayLabel != 'Has Spouse' && (!empty($price_control) && !in_array('Has Spouse', $price_control)) ? 'disabled=disabled'  : ''?> data-label="<?= $displayLabel ?>" <?=(!empty($price_control) && in_array($priceRow['id'], $price_control)) ? "checked" : ''?> <?=  (!($allowPricingUpdate) ? 'disabled' : '') ?>><?= $displayLabel ?></label>
        </div>
        <?php $rowCount++; ?>
        <?php } ?>
        <?php } ?>
      </div>
      <p class="error" id="error_price_control"></p>
    </div>
    <?php if($allowPricingUpdate) { ?>
      <div class="form-group ">
        <a href="javascript:void(0);" id="btn_set_pricing_matrix" class="btn btn-primary"> + Create Matrix</a>
      </div>
    <?php } ?>
    <div id="create_pricing_matrix_div" style="display: none">
      <?php include ('prd_pricing_matrix_add.inc.php'); ?>
    </div>
    <div id="pricingMatrixIframeDiv"></div>
  </div>
  <div id="variable_enrollee_div" style="<?= isset($pricing_model) &&  $pricing_model=="VariableEnrollee" ? '' :'display:none' ?>">
    <?php include ('variable_enrollee.inc.php'); ?>
  </div>
</div>
<div class="section_space gray-bg">
    <h4 class="h4_title"><span class="prd_fee_label">Product Fee</span>
        <a href="prd_history.php" data-type="Product Fee" class="popup_lg">
        <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
        </a>
        
    </h4>
    <p class="m-b-20"><em>Add fee(s) for this product below.</em></p>
    <p><strong>Fees</strong></p>
    <p class="error" id="error_allow_amdin_fee"></p>
    <div id="productFeeIframeDiv"></div>
</div>
</div>



<div class="section_space text-right step_btn_wrap">
 <!--  <a href="javascript:void(0);" class="btn btn-primary pull-left" id="show_summary"> Summary</a> -->
  <a href="javascript:void(0);" class="btn btn-action-o btn_save_exit"> Save & Exit</a>
  <!-- <a href="notify_save_exit.php" class="btn btn-action-o notify_save_exit"> Save & Exit</a> -->
  <a href="javascript:void(0);" class="red-link btn_cancel m-l-15"> Cancel</a>
</div>