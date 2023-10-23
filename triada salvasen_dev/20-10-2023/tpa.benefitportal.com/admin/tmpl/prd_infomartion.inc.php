<div class="section_space gray-bg  theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="Settings" id="matchGlobal_Settings" <?= empty($match_globals) || in_array("Settings",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title">Settings 
  	  <a href="prd_history.php" data-type="Settings" class="popup_lg">	<i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
  </h4>
  
  <p><em>Fill in the settings for this product below.</em></p>
  <div class="row m-t-20 ">
    <div class="col-sm-4">
      <div class="form-group">
        <input name="product_name" id="product_name" type="text" class="form-control" value="<?= ((!empty($product_name))?$product_name:''); ?>" maxlength="50"/>
        <label>Product Name</label>
        <p class="error" id="error_product_name"></p>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <input name="product_code" id="product_code" type="text" class="form-control"  value="<?=!empty($product_code) ? $product_code : '' ?>"/>
        <label>Product ID (Must be unique)</label>
        <p class="error" id="error_product_code"></p>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="product_type" id="product_type" data-match-on="Settings">
          <option value="" hidden selected="selected"></option>
          <option value="Direct Sale Product" <?= isset($product_type) && $product_type=="Direct Sale Product" ? 'selected=selected' : '' ?>>Direct Sale Product</option>
          <option value="Group Enrollment" <?= isset($product_type) && $product_type=="Group Enrollment" ? 'selected=selected' : '' ?>>Group Application</option>
          <option value="Admin Only Product" <?= isset($product_type) && $product_type=="Admin Only Product" ? 'selected=selected' : '' ?>>Admin Only Product</option>
          <option value="Add On Only Product" <?= isset($product_type) && $product_type=="Add On Only Product" ? 'selected=selected' : '' ?>>Add-On Only Product</option>
        </select>
        <label>Application Method </label>
        <p class="error" id="error_product_type"></p>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 col-lg-4">
      <div class="form-group">
        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="company_id" id="company_id" data-match-on="Settings">
          <option value="" hidden selected="selected"></option>
          <option value="new_company">Create New Company</option>
          <?php if(isset($company_res) && !empty($company_res)) { ?>
            <?php foreach ($company_res as $company) {?>
              <option value="<?php echo isset($company["id"])?$company["id"]:''; ?>" <?php echo isset($company_id) && $company['id'] == $company_id ? 'selected' : ''; ?>>
                <?php echo isset($company["company_name"])?$company["company_name"]:""; ?>
              </option>
            <?php }?>
          <?php }?>
        </select>
        <label>Company Offering Product </label>
        <p class="error" id="error_company_id"></p>
        
      </div>
      <div class="m-b-15" id="new_company_div" style="display: none">
        <div class="phone-control-wrap">
          <div class="phone-addon">
            <label class="text-nowrap">Enter New Company: </label>
          </div>
          <div class="phone-addon">
            <input name="company_name" id="company_name" type="text" class="form-control" />
          </div>
          <div class="phone-addon">
            <a href="javascript:void(0);" id="add_new_company" class="btn btn-info btn-outline fw600">Add</a>
          </div>
        </div>
        <p class="error mn text-nowrap" id="error_company_name"></p>
      </div>
    </div>
    <div class="col-sm-6 col-lg-4">
      <div class="form-group">
        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="category_id" id="category_id" data-match-on="Settings">
          <option value="" hidden selected="selected"></option>
          <!-- <option value="new_category">Create New Category</option> -->
          <?php if(!empty($categories)) { ?>
          <?php foreach ($categories as $cat) {?>
          <option value="<?php echo $cat["id"]; ?>" <?php echo isset($category_id) && $cat['id'] == $category_id ? 'selected=selected' : ''; ?>><?php echo $cat["title"]; ?></option>
          <?php }?>
          <?php }?>
        </select>
         <label>Category </label>
        <p class="error" id="error_category_id"></p>
        
      </div>
      <div class="m-b-15" id="new_category_div" style="display: none">
        <div class="phone-control-wrap">
          <div class="phone-addon">
            <label class="text-nowrap">Enter New Category: </label>
          </div>
          <div class="phone-addon">
            <input name="category_name" id="category_name" type="text" class="form-control" />
          </div>
          <div class="phone-addon">
            <a href="javascript:void(0);" id="add_new_category" class="btn btn-info btn-outline fw600">Add</a>
          </div>
        </div>
        <p class="error mn text-nowrap" id="error_category_name"></p>
      </div>
    </div>
    <div class="col-sm-6 col-lg-4">
      <div class="form-group">
        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="product_carrier" id="product_carrier" data-match-on="Settings">
          <option value="" hidden selected="selected"></option>
          <?php if(!empty($prdFeeArray['Carrier'])){ ?>
            <?php foreach ($prdFeeArray['Carrier'] as $crow) {?>
              <option  value="<?php echo $crow["id"]; ?>" <?php echo isset($carrier_id) && $crow['id'] == $carrier_id ? 'selected=selected' : ''; ?>><?php echo $crow["name"] .' ('.$crow['product_code'].')'; ?></option>
            <?php } ?>
          <?php } ?>
        </select>
        <label>Primary Carrier </label>
        <p class="error" id="error_product_carrier"></p>
        
      </div>
    </div>
    <div class="col-sm-6 col-lg-4">
      <div class="form-group">
        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="main_product_type" id="main_product_type" data-match-on="Settings">
          <option value="" hidden selected="selected"></option>
          
            <option  value="Core Product" <?php echo isset($main_product_type) && $main_product_type == "Core Product" ? 'selected=selected' : ''; ?>>Core Product</option>
            <option  value="Ancillary" <?php echo isset($main_product_type) && $main_product_type == "Ancillary" ? 'selected=selected' : ''; ?>>Ancillary</option>
           
        </select>
        <label>Product Type </label>
        <p class="error" id="error_main_product_type"></p>
        
      </div>
    </div>
    <div class="col-sm-12 col-lg-8" id="core_product_info_div" style="<?= isset($main_product_type) && $main_product_type == 'Core Product' ? '' : 'display:none'; ?>" >
       <div class="form-group height_auto">
        <div class="visible-lg"><div class="m-t-5"></div></div>
        <p class="mn">A member may have only one (1) active <strong>Core Product</strong> at a time. If this does not meet your requirements, select <strong>Ancillary Product</strong> type. </p>
       </div>
    </div>
    <div class="col-sm-12">
    <div class="clearfix"></div>
    <div class="m-b-10 col-sm-3 col-lg-3">
      <p >Is this a Life Insurance product?</p>
        <div class="radio-v">
          <label>
            <input name="is_life_insurance_product" <?= isset($is_life_insurance_product) && $is_life_insurance_product=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> No
          </label>
        </div>
        <div class="radio-v">
          <label>
            <input name="is_life_insurance_product" <?= isset($is_life_insurance_product) && $is_life_insurance_product=="Y" ? 'checked' : '' ?>  type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Yes
          </label>
        </div>
        <p class="error" id="error_is_life_insurance_product"></p>
    </div>
  </div>
  <div class="clearfix"></div>
    <div id="life_insurance_div" style="<?= isset($is_life_insurance_product) && $is_life_insurance_product=="Y" ? '' : 'display: none' ?>">
      <div class="col-sm-12 clearfix">
        <hr class="m-b-30 m-t-10" />
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="life_term_type" id="life_term_type" data-match-on="Settings">
            <option></option>
            <option value="Level Term" <?= isset($life_term_type) && $life_term_type =="Level Term" ? "selected" : "" ?>>Level Term</option>
            <option value="Annual Term" <?= isset($life_term_type) && $life_term_type =="Annual Term" ? "selected" : "" ?>>Annual Term</option>
          </select>
          <label>Life Term</label>
          <p class="error" id="error_life_term_type"></p>
        </div>
      </div>
      <div class="col-sm-8">
        <div class="form-group height_auto">
          <div class="m-t-5 input-question">
            <label class="checkbox-inline">Guarantee Isuue Amount?</label>
            <label class="checkbox-inline"><input type="checkbox" name="guarantee_issue_amount_type[]" class="guarantee_issue_amount_type <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="Primary" <?= !empty($guarantee_issue_amount_type) && in_array("Primary", $guarantee_issue_amount_type) ? 'checked' : '' ?> data-match-on="Settings">Primary</label>
            <label class="checkbox-inline"><input type="checkbox" name="guarantee_issue_amount_type[]" class="guarantee_issue_amount_type <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="Spouse" <?= !empty($guarantee_issue_amount_type) && in_array("Spouse", $guarantee_issue_amount_type) ? 'checked' : '' ?> data-match-on="Settings">Spouse</label>
            <label class="checkbox-inline"><input type="checkbox" name="guarantee_issue_amount_type[]" class="guarantee_issue_amount_type <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="Child" <?= !empty($guarantee_issue_amount_type) && in_array("Child", $guarantee_issue_amount_type) ? 'checked' : '' ?> data-match-on="Settings">Child</label>
            <p class="error" id="error_guarantee_issue_amount_type"></p>
         </div>
        </div>
      </div>

      <div class="col-sm-4" id="primary_issue_amount_div" style="<?= !empty($guarantee_issue_amount_type) && in_array("Primary", $guarantee_issue_amount_type) ? '' : 'display: none' ?>">
        <h5 class="m-t-0">Primary</h5>
        <div class="form-group">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
            <input type="text" class="form-control formatPricing <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="primary_issue_amount" id="primary_issue_amount" placeholder="0.00" value="<?= isset($primary_issue_amount) ? $primary_issue_amount : '0.00' ?>" data-match-on="Settings">
          </div>
          <p class="error" id="error_primary_issue_amount"></p>
        </div>
      </div>
        <div class="col-sm-4" id="spouse_issue_amount_div" style="<?= !empty($guarantee_issue_amount_type) && in_array("Spouse", $guarantee_issue_amount_type) ? '' : 'display: none' ?>">
        <h5 class="m-t-0">Spouse</h5>
        <div class="form-group">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
            <input type="text" class="form-control formatPricing <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="spouse_issue_amount" id="spouse_issue_amount"  placeholder="0.00" value="<?= isset($spouse_issue_amount) ? $spouse_issue_amount : '0.00' ?>" data-match-on="Settings">
             
          </div>
          <p class="error" id="error_spouse_issue_amount"></p>
        </div>
      </div>
        <div class="col-sm-4" id="child_issue_amount_div" style="<?= !empty($guarantee_issue_amount_type) && in_array("Child", $guarantee_issue_amount_type) ? '' : 'display: none' ?>">
        <h5 class="m-t-0">Child</h5>
        <div class="form-group">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
            <input type="text" class="form-control formatPricing <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="child_issue_amount" id="child_issue_amount"  placeholder="0.00" value="<?= isset($child_issue_amount) ? $child_issue_amount : '0.00' ?>" data-match-on="Settings">
          </div>
          <p class="error" id="error_child_issue_amount"></p>
        </div>
      </div>
      <div class="col-sm-12" id="is_spouse_issue_amount_larger_div" style="<?= !empty($guarantee_issue_amount_type) && in_array("Spouse", $guarantee_issue_amount_type) ? '' : 'display: none' ?>">
        <div class="m-b-10">
        <p >Can spouse Benefit Amount be larger than Primary?</p>
          <div class="radio-v">
            <div class="radio-v">
              <label>
                <input name="is_spouse_issue_amount_larger" type="radio" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="N" <?= isset($is_spouse_issue_amount_larger) && $is_spouse_issue_amount_larger=="N" ? 'checked' : '' ?> data-match-on="Settings"> No
              </label>
            </div>
            <label>
              <input name="is_spouse_issue_amount_larger" type="radio" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="Y" <?= isset($is_spouse_issue_amount_larger) && $is_spouse_issue_amount_larger=="Y" ? 'checked' : '' ?> data-match-on="Settings"> Yes
            </label>
          </div>
          <p class="error" id="error_is_spouse_issue_amount_larger"></p>
        </div>
       </div>
    </div>
    <div class="col-sm-12">
      <div class="m-b-10 col-sm-3 col-lg-3">
        <p >Is this a Short Term Disability Product?</p>
          <div class="radio-v">
            <label>
              <input name="is_short_term_disablity_product" <?= isset($is_short_term_disablity_product) && $is_short_term_disablity_product=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> No
            </label>
          </div>
          <div class="radio-v">
            <label>
              <input name="is_short_term_disablity_product" <?= isset($is_short_term_disablity_product) && $is_short_term_disablity_product=="Y" ? 'checked' : '' ?>  type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Yes
            </label>
          </div>
          <p class="error" id="error_is_short_term_disablity_product"></p>
      </div>
    </div>
    <div class="clearfix"></div>
    <div id="short_term_disablity_div" style="<?= isset($is_short_term_disablity_product) && $is_short_term_disablity_product=="Y" ? '' : 'display: none' ?>">
      <div class="col-sm-12 clearfix">
        <hr class="m-b-30 m-t-10" />
      </div>
      <div class="row" id="monthly_benefit_allowed_div">
        <div class="col-sm-4">
          <p>What is the maximum monthly benefit allowed?</p>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-usd"></i></span>
              <input type="text" class="form-control formatPricing <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="monthly_benefit_allowed" id="monthly_benefit_allowed" placeholder="0.00" value="<?= isset($monthly_benefit_allowed) ? $monthly_benefit_allowed : '0.00' ?>" data-match-on="Settings">
            </div>
            <p class="error" style="width: 300px;" id="error_monthly_benefit_allowed"></p>
          </div>
        </div>
      </div>
      <div class="row" id="percentage_of_salary_div">
        <div class="col-sm-4">
          <p>What is the maximum percentage of sallary allowed?</p>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <div class="input-group">
              <input type="text" class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="percentage_of_salary" id="percentage_of_salary" placeholder="0.00" value="<?= isset($percentage_of_salary) ? $percentage_of_salary : '0.00' ?>" onkeyup="setPercentage($(this))" onblur="setPercentage($(this))" data-match-on="Settings">
              <span class="input-group-addon"><i class="fa fa-percent"></i></span>
            </div>
            <p class="error" style="width: 300px;" id="error_percentage_of_salary"></p>
          </div>
        </div>
      </div>
    </div>
    <div id="deduction_div_main" style="<?= isset($product_type) && $product_type=="Group Enrollment" ? 'display: block;' : 'display: none;' ?>">
      <div class="m-b-20 clearfix">
        <div class="col-sm-12">
          <p >Deductions</p>
            <div class="radio-v">
              <label>
                <input name="deduction" <?= isset($deduction) && $deduction=="pre_tax" ? 'checked' : '' ?> type="radio" value="pre_tax" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Pre-Tax
              </label>
            </div>
            <div class="radio-v">
              <label>
                <input name="deduction" <?= isset($deduction) && $deduction=="post_tax" ? 'checked' : '' ?>  type="radio" value="post_tax" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Post-Tax
              </label>
            </div>
            <p class="error" id="error_deduction"></p>
        </div>
      </div>
    </div>
    <div id="gap_plus_div_main" style="<?= isset($product_type) && $product_type=="Group Enrollment" ? 'display: block;' : 'display: none;' ?>">
      <div class="m-b-20 clearfix">
        <div class="col-sm-12">
          <p >Is this a Gap+ product?</p>
            <div class="radio-v">
              <label>
                <input name="is_gap_plus_product" <?= isset($is_gap_plus_product) && $is_gap_plus_product=="N" ? 'checked' : ($allowGapPlusUpdate ? 'disabled' : '') ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> No
              </label>
            </div>
            <div class="radio-v">
              <label>
                <input name="is_gap_plus_product" <?= isset($is_gap_plus_product) && $is_gap_plus_product=="Y" ? 'checked' : ($allowGapPlusUpdate ? 'disabled' : '') ?>  type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Yes
              </label>
            </div>
            <p class="error" id="error_is_gap_plus_product"></p>
        </div>
      </div>
      <div id="gap_plus_div" style="<?= isset($is_gap_plus_product) && $is_gap_plus_product=="Y" ? '' : 'display: none;' ?>">
        <div class="col-sm-12">
            <h5 class="h5_title m-b-15">Annual HRM Payment Amount</h5>
          </div>
          <?php if(!empty($prdPlanTypeArray)) { ?>
            <?php foreach ($prdPlanTypeArray as $key => $tier) { ?>
              <div class="col-lg-2 col-md-4 col-sm-4">
                <div class="form-group">
                    <input name="annual_hrm_payment[<?= $tier['id'] ?>]" id="annual_hrm_payment_<?= $tier['id'] ?>" type="text" class="form-control hrm_payment_input <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="<?=isset($annual_hrm_payment[$tier['id']])?$annual_hrm_payment[$tier['id']]:''?>" data-match-on="Settings" <?=$gapPlusReadonly?>/>
                    <label><?=$tier['title']?></label>
                    <p class="error" id="error_annual_hrm_payment_<?= $tier['id'] ?>"></p>
                </div>
              </div>
            <?php } ?>
          <?php } ?>            
          <div class="m-b-20 clearfix">
            <div class="col-sm-12 ">
              <p >Should system require user to enter their Out-of-Pocket Maximum?</p>
                <div class="radio-v">
                  <label>
                    <input name="is_require_out_of_pocket_maximum" <?= isset($is_require_out_of_pocket_maximum) && $is_require_out_of_pocket_maximum=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> No
                  </label>
                </div>
                <div class="radio-v">
                  <label>
                    <input name="is_require_out_of_pocket_maximum" <?= isset($is_require_out_of_pocket_maximum) && $is_require_out_of_pocket_maximum=="Y" ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Yes
                  </label>
                </div>
                <p class="error" id="error_is_require_out_of_pocket_maximum"></p>
            </div>
          </div>
          <div class="clearfix">
            <div class="col-sm-12 m-b-20">
              <p >Should system limit the benefit amount available for this product by setting a minimum and maximum benefit amount?</p>
                <div class="radio-v">
                  <label>
                    <input name="is_benefit_amount_limit" <?= isset($is_benefit_amount_limit) && $is_benefit_amount_limit=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> No
                  </label>
                </div>
                <div class="radio-v">
                  <label>
                    <input name="is_benefit_amount_limit" <?= isset($is_benefit_amount_limit) && $is_benefit_amount_limit=="Y" ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Yes
                  </label>
                </div>
                <p class="error" id="error_is_benefit_amount_limit"></p>
            </div>
            <div id="benefit_amount_limit_div" class="col-sm-12 m-l-30" style="<?= isset($is_benefit_amount_limit) && $is_benefit_amount_limit=="Y" ? '' : 'display: none;' ?>">
                <div class="col-lg-2 col-md-4 col-sm-4">
                    <div class="form-group">
                        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="minimum_benefit_amount" id="minimum_benefit_amount" data-match-on="Settings" data-live-search="true">
                          <?php 
                            for ($i=500; $i <= 10000; $i+=500) { 
                              ?>
                              <option value="<?=$i?>" <?=isset($minimum_benefit_amount) && $minimum_benefit_amount == $i?'selected="selected"':''?>><?=displayAmount($i)?></option>
                              <?php  
                            }
                          ?>
                        </select>
                        <label>Minimum Available</label>
                        <p class="error" id="error_minimum_benefit_amount"></p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4">
                    <div class="form-group">
                        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="maximum_benefit_amount" id="maximum_benefit_amount" data-match-on="Settings" data-live-search="true">
                          <?php 
                            for ($i=500; $i <= 10000; $i+=500) { 
                              ?>
                              <option value="<?=$i?>" <?=isset($maximum_benefit_amount) && $maximum_benefit_amount == $i?'selected="selected"':''?>><?=displayAmount($i)?></option>
                              <?php  
                            }
                          ?>
                        </select>
                        <label>Maximum Available</label>
                        <p class="error" id="error_maximum_benefit_amount"></p>
                    </div>
                </div>
            </div>
          </div>
          <div class="clearfix">
            <div class="col-sm-12 m-b-20">
              <p >Set default Out-of-Pocket Maximum?</p>
                <div class="radio-v">
                  <label>
                    <input name="is_set_default_out_of_pocket_maximum" <?= isset($is_set_default_out_of_pocket_maximum) && $is_set_default_out_of_pocket_maximum=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> No
                  </label>
                </div>
                <div class="radio-v">
                  <label>
                    <input name="is_set_default_out_of_pocket_maximum" <?= isset($is_set_default_out_of_pocket_maximum) && $is_set_default_out_of_pocket_maximum=="Y" ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Yes
                  </label>
                </div>
                <p class="error" id="error_is_set_default_out_of_pocket_maximum"></p>
            </div>
            <div id="default_out_of_pocket_maximum_div" class="col-sm-12 m-l-30" style="<?= isset($is_set_default_out_of_pocket_maximum) && $is_set_default_out_of_pocket_maximum=="Y" ? '' : 'display: none;' ?>">
                <div class="col-lg-2 col-md-4 col-sm-4">
                    <div class="form-group">
                        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="default_out_of_pocket_maximum" id="default_out_of_pocket_maximum" data-match-on="Settings" data-live-search="true">
                          <?php 
                            for ($i=500; $i <= 10000; $i+=500) { 
                              ?>
                              <option value="<?=$i?>" <?=isset($default_out_of_pocket_maximum) && $default_out_of_pocket_maximum == $i?'selected="selected"':''?>><?=displayAmount($i)?></option>
                              <?php  
                            }
                          ?>
                        </select>
                        <label>Default Amount</label>
                        <p class="error" id="error_default_out_of_pocket_maximum"></p>
                    </div>
                </div>
            </div>
          </div>
          <div class="clearfix">
            <div class="col-sm-12 m-b-15">
              <p>With Gap+ enrollment take home savings, recommend in the following priority order:</p>
                <div class="radio-v">
                  <label>
                    <input name="gap_home_savings_recommend_text" <?= isset($gap_home_savings_recommend_text) && $gap_home_savings_recommend_text=="most_expensive" ? 'checked' : '' ?> type="radio" value="most_expensive" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Most expensive product available with savings
                  </label>
                </div>
                <div class="radio-v">
                  <label>
                    <input name="gap_home_savings_recommend_text" <?= isset($gap_home_savings_recommend_text) && $gap_home_savings_recommend_text=="least_expensive" ? 'checked' : '' ?> type="radio" value="least_expensive" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Least expensive product available with savings
                  </label>
                </div>
                <div class="radio-v">
                  <label>
                    <input name="gap_home_savings_recommend_text" <?= isset($gap_home_savings_recommend_text) && $gap_home_savings_recommend_text=="custom_recommendation" ? 'checked' : '' ?> type="radio" value="custom_recommendation" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Settings"> Custom recommendation
                  </label>
                </div>
                <p class="error" id="error_gap_home_savings_recommend_text"></p>
            </div>
            <div id="gap_home_savings_recommend_text_div" class="m-b-15  col-sm-12 m-l-30" style="<?= isset($gap_home_savings_recommend_text) && $gap_home_savings_recommend_text=="custom_recommendation" ? '' : 'display: none;' ?>">
              <div class="col-sm-6">
                  <div class="form-group height_auto">
                      <textarea placeholder="Recommendation Text" class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" name="gap_custom_recommendation_text" id="gap_custom_recommendation_text" data-match-on="Settings" rows="3"><?= isset($gap_custom_recommendation_text)?$gap_custom_recommendation_text:''?></textarea>
                      <p class="error" id="error_gap_custom_recommendation_text"></p>
                  </div>
              </div>
            </div>
          </div>
      </div>
    </div>
    <div class="col-sm-12">
    <div class="clearfix"></div>
    <hr class="m-b-30 m-t-10" />
    </div>
    <?php if(!empty($resProductCode)) { ?>
      <?php foreach ($resProductCode as $key => $value) { ?>
          <div class="col-lg-2 col-md-4 col-sm-4 product_plan_code" id="product_plan_code_div_<?= $plan_code_counter ?>" data-counter="<?= $plan_code_counter ?>">
            <div class="form-group">
              <div class="input-group">
              <?php $plan_code_label="";
                if($value['code_no']=="GC"){
                  $plan_code_label = "Group Code";
                }else{
                  $plan_code_label = "Plan Code ";
                }
              ?>
              
              
              <?php $plan_code_name = str_replace(" ", "_", $value['code_no']); ?>
              <input name="product_plan_code[<?= $value['id'] ?>]" id="product_plan_code_<?= $value['id'] ?>" type="text" class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="<?= $value['plan_code_value'] ?>" maxlength="40" data-match-on="Settings"/>
              
              <label>
                <?= $plan_code_label ?>
                <?php if($plan_code_counter > 0) { ?>
                <span id="product_plan_code_display_number_<?= $plan_code_counter ?>" class="product_plan_code_display_number" data-display_number="<?= $plan_code_counter ?>"><?= $plan_code_counter ?></span>
                <?php } ?>
              </label>  
              <?php if($plan_code_counter > 2) { ?>
              <span class="input-group-btn">
                <a href="javascript:void(0);" class="btn text-action remove_product_plan_code <?= $record_type=="Variation" ? 'matchGlobalBtn' : '' ?>" id="remove_product_plan_code_<?= $plan_code_counter ?>" data-removeId="<?= $plan_code_counter ?>" data-id="<?= $value['id'] ?>" data-match-on="Settings">
                  <i class="fa fa-times"></i>
                </a>
              </span>
              <?php } ?> 
               </div>           
              <p class="error" id="error_product_plan_code_<?= $value['id'] ?>"></p>
              <?php $plan_code_counter++; ?>
            </div>
          </div>
      <?php } ?>
    <?php }else {?>
      <?php if(!empty($defaultPlanCode)) { ?>
        <?php foreach ($defaultPlanCode as $key => $value) { ?>
            <div class="col-lg-2 col-md-4 col-sm-4 product_plan_code" id="product_plan_code_div_-<?= $plan_code_counter ?>" data-counter="-<?= $plan_code_counter ?>">
              <div class="form-group">
                <?php $plan_code_label="";
                  if($value=="GC"){
                    $plan_code_label = "Group Code";
                  }else{
                    $plan_code_label = "Plan Code ".$plan_code_counter;
                  }
                ?>
                <input name="product_plan_code[-<?= $plan_code_counter ?>]" id="product_plan_code_-<?= $plan_code_counter ?>" type="text" class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" value="" maxlength="40" data-match-on="Settings"/>

                <label><?= $plan_code_label ?></label>
                <p class="error" id="error_product_plan_code_-<?= $plan_code_counter ?>"></p>
                <?php $plan_code_counter++; ?>
              </div>
            </div>
        <?php } ?>
      <?php } ?>
    <?php } ?>
    <div id="product_plan_code_div"></div>
    <div class="col-lg-2 col-md-4 col-sm-4 ">
      <div class="form-group">
        <a href="javascript:void(0);" id="add_product_plan_code" class="btn btn-info btn-outline <?= $record_type=="Variation" ? 'matchGlobalBtn' : '' ?>" data-match-on="Settings">+ Plan Code</a>
      </div>
    </div>
  </div>
</div>
<div class="section_space gray-bg">   
  <div class="clearfix">
    <div class="pull-left">	
        <h4 class="h4_title">Application Page
        <a href="prd_history.php" data-type="Enrollment Page" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
        </h4>
        
    </div>
    <div class="pull-right m-b-10">
      <div class="radio-v pull-left m-t-7 m-r-10 " style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
        <label><input name="matchGlobal[]" type="checkbox" value="EnrollmentPage" id="matchGlobal_EnrollmentPage" <?= empty($match_globals) || in_array("EnrollmentPage",$match_globals) ? 'checked' : '' ?>> Match Global</label>
      </div>
      <a href="javascript:void(0);" id="enrollmentPageExample" class="btn btn-skyblue">Preview</a>
    </div>
  </div>
  
  <div class="clearfix "></div>
  <p class="p-b-20"><em>This summary is visible to prospective members on consumer facing application pages.</em></p>
  
  <div class="form-group">
    <textarea name="enrollmentPage" id="enrollmentPage"  class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?> summernoteClass" data-match-on="EnrollmentPage"><?= !empty($enrollmentPage) ? $enrollmentPage : ''?></textarea>
    <p class="error" id="error_enrollmentPage"></p>
    
  </div>
</div>
<div class="section_space gray-bg">
  <div class="clearfix">
    <div class="pull-left"> 
        <h4 class="h4_title">Member Portal Information 
        <a href="prd_history.php" data-type="Member Portal Information" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
        </h4>
    </div>
    <div class="pull-right m-b-10">
      <div class="radio-v pull-left m-t-7 m-r-10" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
        <label><input name="matchGlobal[]" type="checkbox" value="MemberPortalInformation" id="matchGlobal_MemberPortalInformation" <?= empty($match_globals) || in_array("MemberPortalInformation",$match_globals) ? 'checked' : '' ?>> Match Global</label>
      </div>
      <a href="javascript:void(0);" id="memberPortalPreview" class="btn btn-skyblue">Preview</a>
    </div>
  </div>
  <div class="clearfix"></div>
  <p><em>These sections are visible to a member in their online portal after purchasing this product.  Create the label for the tab and then corresponding benefits/information in the text box provided (ie., Claims, Attach forms, phone numbers, URLs etc, to process claims).</em></p>

  <div class="visible-lg m-t-20 p-t-20"></div>
  <div class="row">
    <div id="department_div">
      <?php if(!empty($resDepartment)){ 
        $sectionCount = 1;
        $i=0;
        ?>
        <?php foreach ($resDepartment as $key => $value) { ?>
          <?php if($i % 2 == 0){ ?>
            <div class="clearfix"></div>
          <?php }  ?>
        <div class="col-md-12 department_div" id="department_div_<?= $value['id'] ?>">
          <p class="text-right">
            <?php if($i >= 1) { ?>
              <a href="javascript:void(0);" id="removeDepartment_<?= $value['id'] ?>" class="removeDepartment text-action <?= $record_type=="Variation" ? 'matchGlobalBtn' : '' ?>" data-match-on="MemberPortalInformation" data-id="<?= $value['id'] ?>" data-removeId="<?= $value['id'] ?>"><i class="fa fa-times fa-lg"></i></a>
            <?php } ?>
          </p>
          <div class="row">
          	<div class="col-md-4 m-b-30">
                <div class="phone-control-wrap">
                	<div class="phone-addon horizontal-line">
                  	<strong class="primary-label">Section <?= $sectionCount ?> </strong>
                  </div>
                	<div class="phone-addon">
                  <input name="department_name[<?=  $value['id'] ?>]" id="department_name_<?= $value['id'] ?>" type="text" placeholder="Tab Label (Name)" class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberPortalInformation" value="<?= $value['name'] ?>"/>
                  <p class="error" id="error_department_name_<?= $value['id'] ?>"></p>
                  
                  </div>
                </div>
                <div class="departmentAddButton"></div>
             </div>
             <div class="col-md-8">		
                  <div class="form-group">
              <textarea name="department_desc[<?= $value['id'] ?>]" id="department_desc_<?= $value['id'] ?>" class="summernoteClass <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MemberPortalInformation"><?= $value['description'] ?></textarea>
              <p class="error" id="error_department_desc_<?= $value['id'] ?>"></p>
              
            </div>
             </div>
          </div>
          <div class="m-t-10 p-b-10"></div>
        </div>
        <?php $sectionCount++;$i++; } ?>
      <?php }?>
    </div>
    <div id="addButtonDiv" style="display: none;">
      <a href="javascript:void(0);" id="add_department_div" class="btn btn-default <?= $record_type=="Variation" ? 'matchGlobalBtn' : '' ?>" data-match-on="MemberPortalInformation">+ Section</a>
    </div>
  </div> 
</div>
<div class="section_space gray-bg">
  <div class="clearfix">
    <div class="pull-left"> 
        <h4 class="h4_title">Agent Application Information
        <a href="prd_history.php" data-type="Agent Application Information" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
        </h4>
    </div>
    <div class="pull-right m-b-10">
      <div class="radio-v pull-left m-t-7 m-r-10" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
        <label><input name="matchGlobal[]" type="checkbox" value="AgentEnrollmentInformation" id="matchGlobal_AgentEnrollmentInformation" <?= empty($match_globals) || in_array("AgentEnrollmentInformation",$match_globals) ? 'checked' : '' ?>> Match Global</label>
      </div>
      <a href="javascript:void(0);" id="agentPortalPreview" class="btn btn-skyblue">Preview</a>
    </div>
  </div>
  <div class="clearfix"></div>
  <p class="p-b-20"><em>This is the full description of the plan and it's benefits as visible to an Agent within their online portal.</em></p>
  
  <div class="form-group">
    <textarea name="agent_portal" id="agent_portal" class="summernoteClass <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentEnrollmentInformation"><?= !empty($agent_portal) ? $agent_portal : ''?></textarea>
    <p class="error" id="error_agent_portal"></p>
    
  </div>
  <p class="fw500">Information displayed above benefits &nbsp; 
  	 <a href="prd_info_benefits.php" class="popup_sm"><i class="fa fa-info-circle"></i></a>
  </p>
  <div class="agent_benefit_check">
    <table>
    	<tr>
        <td>
          <label><input name="agentInfoProductBox[]" type="checkbox" value="Effective Date" <?= !empty($agent_info) && in_array("Effective Date", $agent_info) ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentEnrollmentInformation"/> Effective Date</label>
          

        </td>
        <td>
          <label><input name="agentInfoProductBox[]" type="checkbox" value="Available State" <?= !empty($agent_info) && in_array("Available State", $agent_info) ? 'checked' : '' ?>  class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentEnrollmentInformation"/> Available States</label>
          
        </td>
      </tr>
      <tr>
      	<td class="p-r-10">
          <label><input name="agentInfoProductBox[]" type="checkbox" value="Product Required" <?= !empty($agent_info) && in_array("Product Required", $agent_info) ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentEnrollmentInformation" /> Products Required</label>
          
        </td>
        <td>
        	<label><input name="agentInfoProductBox[]" type="checkbox" value="Product Excluded" <?= !empty($agent_info) && in_array("Product Excluded", $agent_info) ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentEnrollmentInformation" /> Products Excluded</label>
          
        </td>
      </tr>
    </table>
  </div> 
</div>
<div class="section_space gray-bg">
  <div class="clearfix">
    <div class="pull-left"> 
        <h4 class="h4_title">Limitations and Exclusions
        <a href="prd_history.php" data-type="Limitations and Exclusions" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
        </h4>
    </div>
    <div class="pull-right m-b-10">
      <div class="radio-v pull-left m-t-7 m-r-10" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
        <label><input name="matchGlobal[]" type="checkbox" value="LimitationAndExclusions" id="matchGlobal_LimitationAndExclusions" <?= empty($match_globals) || in_array("LimitationAndExclusions",$match_globals) ? 'checked' : '' ?>> Match Global</label>
      </div>
      <a href="javascript:void(0)" id="limitationExclusionPreview" class="btn btn-skyblue">Preview</a>
    </div>
  </div>
  <div class="clearfix"></div>
  <p class="p-b-20"><em>This will display on all application forms as well as inside the agent portal.</em></p>
  <div class="row">
  	<div class="col-sm-12">
        <textarea name="limitations_exclusions" id="limitations_exclusions"  class="summernoteClass tagHeight <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="LimitationAndExclusions"><?= !empty($limitations_exclusions) ? $limitations_exclusions : ''?></textarea>
        <p class="error" id="error_limitations_exclusions"></p>
      </div>
<!--     <div class="col-sm-3 pn ">
     	<div class="editor_tag_wrap">
        	<div class="tag_head"><h4>AVAILABLE TAGS&nbsp;<span class="fa fa-info-circle"></span></h4></div>
            <div><label for="le_productID" onclick="insertAtCaret(this)" id="le_productID">[[ProductID]]</label></div> 
            <div><label for="le_benefitTier" onclick="insertAtCaret(this)" id="le_benefitTier">[[BenefitTier]]</label></div> 
            <div><label for="le_retailPrice" onclick="insertAtCaret(this)" id="le_retailPrice">[[RetailPrice]]</label></div>
            <div><label for="le_healthyStep" onclick="insertAtCaret(this)" id="le_healthyStep">[[HealthyStepFee]]</label></div>
            <div><label for="le_serviceFee" onclick="insertAtCaret(this)" id="le_serviceFee">[[ServiceFee]]</label></div>
            <div><label for="le_productFee" onclick="insertAtCaret(this)" id="le_productFee">[[ProductFee]]</label></div>
      </div>
    </div> -->
  </div>
  <div class="m-t-20 text-right">
    <a data-href="product_smart_tag_popup.php" class="btn btn-info btn-outline product_smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i>
</a>
  </div>
</div>
<div class="section_space text-right step_btn_wrap">
  <!-- <a href="javascript:void(0);" class="btn btn-primary pull-left"> Summary</a> -->
  <a href="javascript:void(0);" class="btn btn-action  btn_next"> Next</a>
  <a href="javascript:void(0);" class="btn btn-action-o btn_save_exit"> Save and  Exit</a>
  <a href="javascript:void(0);" class="red-link btn_cancel"> Cancel</a>
</div>