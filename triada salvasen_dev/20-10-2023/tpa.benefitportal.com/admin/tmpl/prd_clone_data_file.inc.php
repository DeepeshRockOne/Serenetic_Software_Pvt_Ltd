<div id="product_plan_code_dynamic_div" style="display: none">
  <div id="product_plan_code_div_~plan_code_counter~" data-counter="~plan_code_counter~">
    <div class="col-lg-2 col-md-4 col-sm-4 product_plan_code">
      <div class="form-group">        
        <div class="input-group">
        	<input name="product_plan_code[~plan_code_counter~]" id="product_plan_code_~plan_code_counter~" type="text" class="form-control" value="" maxlength="40"/>
            <label>Plan Code 
              <span id="product_plan_code_display_number_~plan_code_counter~" class="product_plan_code_display_number" data-display_number="~plan_code_counter~">~display_plan_code_counter~</span>
            </label>
            <span class="input-group-btn">
              <a href="javascript:void(0);" class="btn text-action remove_product_plan_code" id="remove_product_plan_code_~plan_code_counter~" data-id="" data-removeId="~plan_code_counter~">
                <i class="fa fa-times"></i>
              </a>
            </span>
         </div>
         
        <p class="error" id="error_product_plan_code_~plan_code_counter~"></p>
      </div>
    </div>
  </div>
</div>

<div id="department_dynamic_div" style="display: none">
  <div class="col-md-12  department_div" id="department_div_~number~">
    <p class="text-right">
          <a href="javascript:void(0);" class="removeDepartment text-action" id="removeDepartment_~number~" data-removeId="~number~"><i class="fa fa-times fa-lg"></i></a>
    </p>
    <div class="row">
      <div class="col-md-4">
        <div class="phone-control-wrap">
          <div class="phone-addon horizontal-line">
              <strong class="primary-label">Section ~display_number~ </strong>
          </div>

          <div class="phone-addon">
            <input name="department_name[~number~]" id="department_name_~number~" type="text" class="form-control" placeholder="Tab Label (Name)" value=""/>
              <p class="error" id="error_department_name_~number~"></p>
          </div>
        </div>
        <div class="departmentAddButton"></div>
      </div>
      <div class="col-md-8">  
          <div class="form-group">
              <textarea name="department_desc[~number~]" id="department_desc_~number~" class=""></textarea>
              <p class="error" id="error_department_desc_~number~"></p>
            </div>
      </div>
    </div>
    <div class="m-t-10 p-b-10"></div>
  </div>
</div>

<div id="inline_colorbox_div" style="display: none;">
  <div id="enrollmentPageColorbox">
    <div class="panel panel-default panel-shadowless">
      <div class="panel-heading br-b">Application Page</div>
      <div class="panel-body">
        <div class="portal_prdinfo_scroll">
          <div id="enrollmentPageBody">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="agentEnrollmentInformationColorbox">
    <div class="panel panel-default panel-shadowless">
      <div class="panel-heading br-b">Agent Application Information</div>
      <div class="panel-body" > 
        <div id="agentInfoHeading" class="br-b">
          <p id="agentInfoProductHeading">Product: <span id="agentInfoProductBody"></span></p>
          <p id="agentInfoEffectiveHeading" style="display: none">Effective Date: <span id="agentInfoEffectiveBody"></span></p>
          <p id="agentInfoAvailableStateHeading" style="display: none">Available State: <span id="agentInfoAvailableStateBody"></span></p>
          <p id="agentInfoProductRequiredHeading" style="display: none">Product Required: <span id="agentInfoProductRequiredBody"></span></p>
          <p id="agentInfoProductExcludedHeading" style="display: none">Products Excluded: <span id="agentInfoProductExcludedBody"></span></p>
        </div>
        <div id="agentEnrollmentInformationBody" class="m-t-10 panel-shadowless">
        </div>
      </div>
    </div>
  </div>
  <div id="limitationAndExclusionColorbox">
    <div class="panel panel-default panel-shadowless">
      <div class="panel-heading br-b">Limitation And Exclusion</div>
      <div class="panel-body"> 
        <div class="portal_prdinfo_scroll">
          <div id="limitationAndExclusionBody">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="memberPortalColorbox">
    <div class="panel panel-default panel-shadowless">
      <div class="panel-heading br-b">Member Portal Information</div>
      <div class="panel-body" id="memberPortalBody">
        <ul class="nav nav-tabs customtab" role="tablist" id="memberPortalTitle">
      </ul>
         <!-- <div class="tab-pane active">  -->
            <div id="memberPortalContent" class="tab-content">
          </div>

      </div>
    </div>
  </div>
</div>

<div id="available_no_sale_state_dynamic_div" style="display: none">
  <div id="available_no_sale_state_main_~state_id~" class="available_no_sale_state_main"></div>
</div>

<div id="available_no_sale_state_dynamic_additional_div" style="display: none">
  <div id="available_no_sale_state_~state_id~_~id~" class="available_no_sale_state_inner">
    <div class="no_sale_pricing">
      <div class="row theme-form">
        <div class="col-sm-3" id="state_name_~state_id~_~id~">
          <h5 class="h5_title m-t-10 text-action">~state_short_name~, ~state_full_name~</h5>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="input-group"> 
            <span class="input-group-addon datePickerIcon" data-applyon="available_no_sale_state_~state_id~_~id~_effective_date" id="apply_on_available_no_sale_state_~state_id~_~id~_effective_date">
              <i class="fa fa-calendar" aria-hidden="true"></i>
            </span>
            <div class="pr">
                <input type="text" name="available_no_sale_state[~state_id~][~id~][effective_date]" class="form-control available_no_sale_state availableEffectiveDate availableEffectiveDate~state_id~ checkTermed" data-id="~id~" data-state-id="~state_id~"  id="available_no_sale_state_~state_id~_~id~_effective_date">
                <label>Effective Date (Required)</label>
            </div>    
          </div>
            <p class="error" id="error_available_no_sale_state_effective_date_~state_id~_~id~"></p>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
              <div class="input-group"> 
            <span class="input-group-addon datePickerIcon" data-applyon="available_no_sale_state_~state_id~_~id~_termination_date" id="apply_on_available_no_sale_state_~state_id~_~id~_termination_date">
              <i class="fa fa-calendar" aria-hidden="true"></i>
            </span>
            <div class="pr">
                <input  type="text" name="available_no_sale_state[~state_id~][~id~][termination_date]" class="form-control available_no_sale_state availableTerminationDate availableTerminationDate~state_id~ checkTermed "  placeholder="" data-short-name="~state_short_name~" data-name="~state_full_name~" data-id="~id~" data-state-id="~state_id~" id="available_no_sale_state_~state_id~_~id~_termination_date" >
                <label>Termination Date</label>
            </div>
          </div>
              <p class="error" id="error_available_no_sale_state_termination_date_~state_id~_~id~"></p>
            </div>
        </div>
        <div class="col-sm-3" style="display: none">
          <div class="form-group">
            <a href="javascript:void(0);" data-id="~state_id~" class="btn btn-default add_no_sale_state_effective_date"> + Effective Date</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="available_specific_zipcode_dynamic_div" style="display:none;">
  <div class="col-sm-6 col-md-4" id="available_specific_zipcode_div_~state_name~" data-state-name="~state_name~">
     <p class="text-action fw500 mn">~state_full_name~ - Zip Code(s)</p>
      <div class="form-group ">
        <div class="cust-tag-bs">
          <input name="available_state_zipcode[~state_names~]" id="available_state_zipcode_~state_name~" type="text" class="tagsinput" value=""/>
        </div>
        <p class="error" id="error_available_state_zipcode_~state_name~"></p>
      </div>
  </div>
</div>

<div id="autoTermedMemberSettingDynamicDiv" style="display: none">
  <div class="m-l-30 m-t-25 m-b-25" id="autoTermedInnerMainDiv~title~">  
    <h5 class="h5_title">~title~</h5> 
    
    <div id="autoTermedInnerDiv~title~" data-title="~title~"></div>
    
    <div class="row">
        <div class="col-sm-12">
          <button data-title="~title~" type="button" class="btn btn-primary-o addTrigger">+ Trigger</button>
        </div>
    </div>
  </div>
</div>

<div id="autoTermedMemberSettingInnerDynamicDiv" style="display: none">
  <div class="row autoTermMemberSettingInner" id="autoTermMemberSettingInner_div_~title~_~number~">
      <div class="col-sm-6 col-lg-5">
            <p>Termination Date</p>
            <div class="form-inline age_restrictions">
                <div class="form-group ">
                    <select name="autoTermMemberSettingWithin[~number~][~title~]"class="add_control_~title~_~number~">
                        <?php for($i=1;$i<=30;$i++) { ?>
                          <option value="<?= $i ?>"><?=$i?></option>
                        <?php } ?>
                    </select>
                    <p class="error" id="error_autoTermMemberSettingWithin_~title~_~number~"></p>
                </div>
                <div class="form-group "><span class="p-l-5 p-r-5">|</span></div>
                <div class="form-group ">
                    <select name="autoTermMemberSettingWithinType[~number~][~title~]" class="add_control_~title~_~number~">
                        <option value="Days">Days</option>
                        <option value="Weeks">Weeks</option>
                        <option value="Months">Months</option>
                    </select>
                    <p class="error" id="error_autoTermMemberSettingWithinType_~title~_~number~"></p>
                </div>
                <div class="form-group "><span class="p-l-5 p-r-5">|</span></div>
                <div class="form-group ">
                    <select name="autoTermMemberSettingRange[~number~][~title~]" class="add_control_~title~_~number~">
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                    </select>
                    <p class="error" id="error_autoTermMemberSettingRange_~title~_~number~"></p>
                </div>
            </div>
      </div>
      <div class="col-sm-6 col-lg-5 add_product_dash">
          <p>Trigger<span class="pull-right"><a href="javascript:void(0);" class="text-action removeAutoTermMemberSettingInner" id="removeAutoTermMemberSettingInner_~title~_~number~" data-id="~number~" data-title="~title~">
                  <i class="fa fa-times"></i>
                </a> </span></p>
          <div class="form-group">
              
                <select name="autoTermMemberSettingWithinTrigger[~number~][~title~]" class="add_control_~title~_~number~" data-live-search="true">
                   <option value=""></option>
                    <?php if(!empty($triggerRes)) {?>
                      <?php foreach ($triggerRes as $key => $value) { ?>
                        <option value="<?= $value['id'] ?>"><?= $value['display_id'] ?></option>
                      <?php } ?>
                    <?php } ?>
                </select>
                <label>Select Trigger</label>
                <p class="error" id="error_autoTermMemberSettingWithinTrigger_~title~_~number~"></p>
              
          </div>
      </div>
  </div>
</div>

<div id="pricing_dynamic_div" style="display: none">
  <div id="inner_pricing_div_~number~" data-id="~number~" class="inner_pricing_div">
    <div class="row m-t-25">
      <div class="col-md-2 col-sm-6">
        <h5 class="h5_title m-b-10">Plan Tier</h5>
      </div>
      <div class="col-md-2 col-sm-6">
         <h5 class="h5_title m-b-10">Fixing Pricing</h5>
      </div>
    </div>
    <?php if(!empty($prdPlanTypeArray)) {?>
      <?php foreach ($prdPlanTypeArray as $key => $tier) { ?>
        <div class="priceTier_<?= $tier['id'] ?>" style="display: none">
          <div class="row">
            <div class="col-md-2 col-sm-6">
              <p class="m-b-15"><?= $tier['title'] ?></p>
            </div>
            <div class="col-md-2 col-sm-6">
               <div class="form-group">
                 <input type="text" name="pricing_fixed_price[~number~][<?= $tier['id'] ?>][Retail]" class="form-control formatPricing caculatePricing" data-id="~number~" data-tier-id="<?= $tier['id'] ?>" value="0.00">
                 <label>Retail Price</label>
                 <p class="error" id="error_pricing_fixed_price_~number~_<?= $tier['id'] ?>"></p>
               </div>
            </div>
            <div class="col-md-3 col-sm-6">
               <div class="form-group ">
                 <input type="text" name="pricing_fixed_price[~number~][<?= $tier['id'] ?>][NonCommissionable]" class="form-control formatPricing caculatePricing" data-id="~number~" data-tier-id="<?= $tier['id'] ?>" value="0.00">
                 <label>Non-Commissionable Price</label>
               </div>
            </div>
            <div class="col-md-3 col-sm-6">
               <div class="form-group ">
                 <input type="text" name="pricing_fixed_price[~number~][<?= $tier['id'] ?>][Commissionable]" class="form-control formatPricing" data-id="~number~" data-tier-id="<?= $tier['id'] ?>" readonly value="0.00">
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
            <span class="input-group-addon datePickerIcon" data-applyon="pricing_effective_date_~number~" id="apply_on_pricing_effective_date_~number~">
              <i class="fa fa-calendar" aria-hidden="true"></i>
            </span>
            <div class="pr">
            <input  type="text" class="form-control pricingEffectiveDate pricingDates" name="pricing_effective_date[~number~]"  id="pricing_effective_date_~number~" data-id="~number~" value="<?= $productActiveEffectiveDate ?>">
            <label>Effective Date(MM/DD/YYYY)</label>
          </div>
          </div>
          <p class="error" id="error_pricing_effective_date_~number~"></p>
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="form-group ">
          <div class="input-group">
            <span class="input-group-addon datePickerIcon" data-applyon="pricing_termination_date_~number~" id="apply_on_pricing_termination_date_~number~">
              <i class="fa fa-calendar" aria-hidden="true"></i>
            </span>
            <div class="pr">
              <input  type="text" class="form-control pricingTerminationDate checkTermed pricingDates" name="pricing_termination_date[~number~]" id="pricing_termination_date_~number~" data-id="~number~">
              <label>Termination Date(MM/DD/YYYY)</label>
            </div>
          </div>
          <p class="error" id="error_pricing_termination_date_~number~"></p>
        </div>
      </div>
      <div class="col-sm-2" id="terminationDateClear_~number~" style="display: none">
        <a href="javascript:void(0)" class="btn red-link clearPricingDiv" data-id="~number~" id="clearPriceDivTmp_~number~">Clear</a></div>
    </div>
    <div class="pricing_setting_div" data-id="~number~" id="pricing_setting_divTmp_~number~"></div>
  </div>
</div>

<div id="pricing_setting_dynamic_div" style="display: none">
  <div class="row">
    <div class="col-md-12">
      <p>Should this new pricing be applied on renewals of existing business?</p>
      <div class="radio-v">
        <label>
          <input class="newPricingOnRenewals"  name="newPricingOnRenewals[~number~]" type="radio" value="N"> No</label>
      </div>
      <div class="radio-v">
        <label>
          <input class="newPricingOnRenewals"  name="newPricingOnRenewals[~number~]" type="radio" value="Y"> Yes</label>
      </div>
      <p class="error" id="error_newPricingOnRenewals_~number~"></p>
    </div>            
  </div>
</div>


<div id="pricing_matrix_dynamic_div" style="display: none">
  <div id="inner_pricing_matrix_div_~number~" data-id="~number~" class="inner_pricing_matrix_div">
    <div class="row m-t-25">
      <div class="col-sm-2">
        <h5 class="h5_title m-b-10">Pricing ~pricingTitle~</h5>
        <input type="hidden" name="pricing_matrix_group[~number~]" value="~matrixGroup~">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-md-3">
         <div class="form-group">
           <input type="text" name="pricing_matrix_price[~number~][Retail]" id="pricing_matrix_price_~number~_Retail" class="form-control formatPricing caculatePricing" data-id="~number~" value="0.00">
           <label>Retail Price</label>
           <p class="error" id="error_pricing_matrix_price_~number~"></p>
         </div>
      </div>
      <div class="col-sm-6 col-md-3">
         <div class="form-group">
           <input type="text" name="pricing_matrix_price[~number~][NonCommissionable]" id="pricing_matrix_price_~number~_NonCommissionable" class="form-control formatPricing caculatePricing" data-id="~number~" value="0.00">
           <label>Non-Commissionable Price</label>
         </div>
      </div>
      <div class="col-sm-6 col-md-3">
         <div class="form-group">
           <input type="text" name="pricing_matrix_price[~number~][Commissionable]" id="pricing_matrix_price_~number~_Commissionable" class="form-control formatPricing" data-id="~number~" readonly value="0.00">
           <label>Commissionable Price</label>
         </div>
      </div>
      
    </div>

    <div class="row">
      <div class="col-sm-11">
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
                <div class="input-group">
                      <span class="input-group-addon datePickerIcon" data-applyon="pricing_matrix_effective_date_~number~">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                      </span>
                      <div class="pr">
                          <input  type="text" class="form-control pricingMatrixEffectiveDate pricingMatrixDates" name="pricing_matrix_effective_date[~number~]"  id="pricing_matrix_effective_date_~number~" data-id="~number~" value="<?= $productActiveEffectiveDate ?>">
                          <label>Effective Date (MM/DD/YYYY)</label>
                      </div>
                </div>
                <p class="error" id="error_pricing_matrix_effective_date_~number~"></p>
              </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                  <span class="input-group-addon datePickerIcon" data-applyon="pricing_matrix_termination_date_~number~">
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                  </span>   

                  <div class="pr">
                      <input  type="text" class="form-control pricingMatrixTerminationDate checkTermed pricingMatrixDates" name="pricing_matrix_termination_date[~number~]" id="pricing_matrix_termination_date_~number~" data-id="~number~">
                      <label>Termination Date (MM/DD/YYYY)</label>
                  </div>
              </div>
              <p class="error" id="error_pricing_matrix_termination_date_~number~"></p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-1" id="terminationDateMatrixClear_~number~" style="display: none">
          <a href="javascript:void(0)" class="btn red-link clearMarixPricingDiv" data-id="~number~">Clear</a>
      </div>
    </div>
    <div id="pricing_matrix_setting_div_~number~" class="pricing_matrix_setting_div" data-id="~number~"></div>
  </div>
</div>

<div id="pricing_matrix_setting_dynamic_div" style="display: none">
  <div class="row">
    <div class="col-md-12">
      <p>Should this new pricing be applied on renewals of existing business?</p>
      <div class="radio-v">
        <label>
          <input class="newPricingMatrixOnRenewals"  name="newPricingMatrixOnRenewals[~number~]" type="radio" value="N"> No</label>
      </div>
      <div class="radio-v">
        <label>
          <input class="newPricingMatrixOnRenewals"  name="newPricingMatrixOnRenewals[~number~]" type="radio" value="Y"> Yes</label>
      </div>
      <p class="error" id="error_newPricingMatrixOnRenewals_~number~"></p>
    </div>            
  </div>
</div>


<div id="pricing_matrix_dynamic_div_Enrollee" style="display: none">
  <div id="inner_pricing_matrix_div_Enrollee_~number~" data-id="~number~" class="inner_pricing_matrix_div_Enrollee">
    <div class="row m-t-25">
      <div class="col-sm-2">
        <h5 class="h5_title m-b-10">Pricing ~pricingTitle~</h5>
        <input type="hidden" name="pricing_matrix_group_Enrollee[~number~]" value="~matrixGroup_Enrollee~">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-md-3">
         <div class="form-group">
           <input type="text" name="pricing_matrix_price_Enrollee[~number~][Retail]" id="pricing_matrix_price_Enrollee_~number~_Retail" class="form-control formatPricing caculatePricing" data-id="~number~" value="0.00">
           <label>Retail Price</label>
           <p class="error" id="error_pricing_matrix_price_Enrollee_~number~"></p>
         </div>
      </div>
      <div class="col-sm-6 col-md-3">
         <div class="form-group">
           <input type="text" name="pricing_matrix_price_Enrollee[~number~][NonCommissionable]" id="pricing_matrix_price_Enrollee_~number~_NonCommissionable" class="form-control formatPricing caculatePricing" data-id="~number~" value="0.00">
           <label>Non-Commissionable Price</label>
         </div>
      </div>
      <div class="col-sm-6 col-md-3">
         <div class="form-group">
           <input type="text" name="pricing_matrix_price_Enrollee[~number~][Commissionable]" id="pricing_matrix_price_Enrollee_~number~_Commissionable" class="form-control formatPricing" data-id="~number~" readonly value="0.00">
           <label>Commissionable Price</label>
         </div>
      </div>
      
    </div>

    <div class="row">
      <div class="col-sm-11">
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
                <div class="input-group">
                      <span class="input-group-addon datePickerIcon" data-applyon="pricing_matrix_effective_date_Enrollee_~number~">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                      </span>
                      <div class="pr">
                          <input  type="text" class="form-control pricingMatrixEffectiveDate_Enrollee pricingMatrixDates_Enrollee" name="pricing_matrix_effective_date_Enrollee[~number~]"  id="pricing_matrix_effective_date_Enrollee_~number~" data-id="~number~" value="<?= $productActiveEffectiveDate ?>">
                          <label>Effective Date (MM/DD/YYYY)</label>
                      </div>
                </div>
                <p class="error" id="error_pricing_matrix_effective_date_Enrollee_~number~"></p>
              </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                  <span class="input-group-addon datePickerIcon" data-applyon="pricing_matrix_termination_date_Enrollee_~number~">
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                  </span>   

                  <div class="pr">
                      <input  type="text" class="form-control pricingMatrixTerminationDate_Enrollee checkTermed pricingMatrixDates_Enrollee" name="pricing_matrix_termination_date_Enrollee[~number~]" id="pricing_matrix_termination_date_Enrollee_~number~" data-id="~number~">
                      <label>Termination Date (MM/DD/YYYY)</label>
                  </div>
              </div>
              <p class="error" id="error_pricing_matrix_termination_date_Enrollee_~number~"></p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-1" id="terminationDateMatrixClear_Enrollee_~number~" style="display: none">
          <a href="javascript:void(0)" class="btn red-link clearMarixPricingDiv_Enrollee" data-id="~number~">Clear</a>
      </div>
    </div>
    <div id="pricing_matrix_setting_div_Enrollee_~number~" class="pricing_matrix_setting_div_Enrollee" data-id="~number~"></div>
  </div>
</div>

<div id="pricing_matrix_setting_dynamic_div_Enrollee" style="display: none">
  <div class="row">
    <div class="col-md-12">
      <p>Should this new pricing be applied on renewals of existing business?</p>
      <div class="radio-v">
        <label>
          <input class="newPricingMatrixOnRenewals_Enrollee"  name="newPricingMatrixOnRenewals_Enrollee[~number~]" type="radio" value="N"> No</label>
      </div>
      <div class="radio-v">
        <label>
          <input class="newPricingMatrixOnRenewals_Enrollee"  name="newPricingMatrixOnRenewals_Enrollee[~number~]" type="radio" value="Y"> Yes</label>
      </div>
      <p class="error" id="error_newPricingMatrixOnRenewals_Enrollee_~number~"></p>
    </div>            
  </div>
</div>


<div id="rider_dynamic_div" style="display: none">
  <div class="row rider_div" id="rider_div_~number~_~rider_type~" data-id="~number~" data-type="~rider_type~">
    <div class="col-sm-6">
       <div class="row">
          <div class="col-sm-2" style="display: none" id="remove_rider_div_~number~_~rider_type~">
             <a href="javascript:void(0);" id="remove_rider_~number~_~rider_type~" class="text-light-gray fs16 remove_rider" data-id="~number~" data-type="~rider_type~">X</a>
          </div>
          <div class="col-sm-10">
             <div class="form-group ">
                <select id="riderProduct_~number~_~rider_type~" name="riderProduct[~number~][~rider_type~]">
                   <option value="" hidden selected="selected"></option>
                    <?php if(!empty($productAddOnArray)){ ?>
                      <?php foreach ($productAddOnArray as $categoryName => $productRow) { ?>
                          <optgroup label='<?= $categoryName; ?>'>
                            <?php foreach ($productRow as $key1 => $row1) { ?>
                              <option value="<?= $row1['id'] ?>" ><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                            <?php } ?>
                          </optgroup>
                      <?php } ?>
                    <?php } ?>
                </select>
                <label>Select Product</label>
                <p class="error" id="error_riderProduct_~number~_~rider_type~"></p>
             </div>
          </div>
       </div>
    </div>
    <div class="col-sm-6">
       <div class="radio-v">
          <label><input type="radio" value="Seperate Rate" name="riderRate[~number~][~rider_type~]" > Separates Rates</label>
       </div>
       <div class="radio-v">
          <label><input type="radio" value="Combined Rate" name="riderRate[~number~][~rider_type~]" > Combined Rates</label>
       </div>
       <p class="error" id="error_riderRate_~number~_~rider_type~"></p>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-12">
      <div class="br-t m-b-10 p-t-10">
    </div>
    </div>
  </div>
</div>


