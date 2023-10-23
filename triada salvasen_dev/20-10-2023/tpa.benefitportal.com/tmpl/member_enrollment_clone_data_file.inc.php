<div style="display:none">
  <div class="login_pag" id="existing_email_div">
    <div class="panel panel-default panel-block panel-shadowless mn">
      <div class="panel-heading br-b">
        <h4 class="mn">Welcome to Member application form</h4>
      </div>
      <div class="panel-body">
        <div class="login_section">
          <form id="form_login" class="theme-form">
            <div class="form-group">
              <input class="form-control" name="login_email" id="login_email" placeholder="Email"type="text"/>
              <p class="error" id="error_login_email"></p> 
            </div>
            <div class="form-group">
              <input class="form-control" name="login_password" id="login_password" placeholder="Password" type="password"/>
              <p class="error" id="error_login_password"></p> 
            </div>
            <div class="m-b-25">
              <div class="clearfix">
                <div class="pull-left m-t-7"> <a href="javascript:void(0);" onclick="forgot_password();" class="blue-link">Forgot Password?</a> </div>
                <div class="pull-right">
                  <button type="button" id="btn_login" class="btn btn-action">Submit</button>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div style="display: none;" class="forgot_password_section">
          <form id="form_forgot_password" class="theme-form ">
            <div class="form-group">
              <input class="form-control" name="fp_email" id="fp_email" placeholder="Email" type="text"/>
              <p class="error" id="error_fp_email"></p> 
            </div>
            <div class="m-b-25">
              <div class="clearfix text-center">
                <button type="button" id="btn_forgot_password" class="btn btn-action">Submit</button>
                <button type="button" onclick="cancel_forgot_password();" class="btn red-link"> Cancel</button>
              </div>
            </div>
          </form>
        </div>
        <p class="ftr_text text-light-gray text-center mn fs16"> Its looks like you already have an existing account.</p>
      </div>
    </div>
  </div>
</div>
<div style="display: none;">
  <div class="panel panel-default panel-block panel-shadowless mn" id="bob_member">
    <div class="panel-heading br-b">
      <h4 class="mn">Member Application</h4>
    </div>
    <div class="panel-body">
      <div class="text-center">
        <p class="m-b-30">This member already exists and is part of your book of business. Click below for available plan options to this account.</p>
        <a href="javascript:void(0);" id="bob_member_enrollment_url" class="btn btn-action">Enroll</a> &nbsp; &nbsp;
        <a href="javascript:void(0);" class="pn red-link" onclick="colorBoxClose();">Cancel</a>
      </div>
    </div>
  </div>
</div>
<div style="display: none;">
  <div class="panel panel-default panel-block panel-shadowless mn" id="bob_lead">
    <div class="panel-heading br-b">
      <h4 class="mn">Member Application</h4>
    </div>
    <div class="panel-body">
      <div class="text-center">
        <p class="m-b-30">This lead already exists and is part of your book of business. Click below to complete the application process.</p>
        <a href="javascript:void(0);" id="bob_lead_enrollment_url" class="btn btn-action">Enroll</a> &nbsp; &nbsp;
        <a href="javascript:void(0);" class="pn red-link" onclick="colorBoxClose();">Cancel</a>
      </div>
    </div>
  </div>
</div>
<div style="display: none;">
  <div class="panel panel-default panel-block panel-shadowless mn" id="none_bob_member">
    <div class="panel-heading br-b">
      <h4 class="mn">Member Application</h4>
    </div>
    <div class="panel-body">
      <div class="text-center">
        <?php 
        $member_services_cell_phone = get_app_settings('member_services_cell_phone');
        ?>
        <p class="m-b-30">This member already exists, please have them contact Member Services at <?=format_telephone($member_services_cell_phone);?> for assistance.</p>
        <a href="javascript:void(0);" class="pn red-link" onclick="colorBoxClose();">Close</a>
      </div>
    </div>
  </div>
</div>
<div id="spouseCoverageDynamicDiv" style="display: none">
  <p><strong>Spouse</strong></p>
  <div class="row enrollment_auto_row">
    <div class="col-lg-7 col-md-8">
      <div class="row ">
        <div class="col-sm-4 ">
          <div class="form-group">
            <input type="text" id="spouse_fname" autocomplete="off" name="spouse_fname" value="" class="form-control spouseCoverageData spouse_fname_1" >
            <label>First Name</label>
            <!-- <p class="error" id="error_spouse_fname"></p> -->
          </div>
        </div>
        <div class="col-sm-4 text-center">
          <div class="form-group">
            <div class="btn-group colors btn-group-justified" data-toggle="buttons">
                <label class="btn btn-info">
                  <input type="radio" name="spouse_gender" value="Male" class="js-switch spouseCoverageData spouse_gender_1" autocomplete="off"> Male
                </label>
                <label class="btn btn-info">
                  <input type="radio" name="spouse_gender" value="Female" class="js-switch spouseCoverageData spouse_gender_1" autocomplete="off"> Female
                </label>
            </div>
            <!-- <p class="error" id="error_spouse_gender"></p> -->
          </div>
        </div>
        <div class="col-sm-4 ">
          <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon datePickerIcon" data-applyon="spouse_birthdate"><i class="material-icons fs16">date_range</i></div>
                <div class="pr">
                    <input name="spouse_birthdate" id="spouse_birthdate" type="text" class="form-control dateClass spouseCoverageData spouse_birthdate_1" />
                      <label>DOB (MM/DD/YYYY)</label> 
                </div>
            </div>
            <!-- <p class="error" id="error_spouse_birthdate"></p> -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <hr />
</div>
<div id="childCoverageDynamicDiv" style="display: none">
  <div id="childCoverageInnerDiv~number~" data-id="~number~" class="childCoverageInnerDiv">
    <p><strong>Child (<span data-display-number="~number~" class="display_number" id="display_number_~number~">~number~</span>)</strong> <span class="pull-right"><a href="javascript:void(0);" class="red-link pn fw500 removeChildCoverageInnerDiv" data-id="~number~" id="removeChildCoverageInnerDiv~number~">Remove</a></span></p>
    <div class="row enrollment_auto_row">
      <div class="col-lg-7 col-md-8">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <input type="text" id="child_fname_~number~"  name="tmp_child_fname[~number~]"  data-id="~number~" autocomplete="off" value="" class="form-control childCoverageData child_fname_~number~" data-element="fname">
              <label>First Name</label>
              <!-- <p class="error" id="error_child_fname_~number~"></p> -->
            </div>
          </div>
          <div class="col-sm-4  text-center">
            <div class="form-group">
              <div class="btn-group colors btn-group-justified" data-toggle="buttons">
                  <label class="btn btn-info">
                    <input type="radio" data-id="~number~" name="tmp_child_gender[~number~]" value="Male" class="js-switch childCoverageData child_gender_~number~" autocomplete="off" data-element="gender"> Male
                  </label>
                  <label class="btn btn-info">
                    <input type="radio" data-id="~number~" name="tmp_child_gender[~number~]" value="Female" class="js-switch childCoverageData child_gender_~number~" autocomplete="off" data-element="gender"> Female
                  </label>
              </div>
              <!-- <p class="error" id="error_child_gender_~number~"></p> -->
            </div>
          </div>
          <div class="col-sm-4 ">
            <div class="form-group">
              <div class="input-group">
                  <div class="input-group-addon datePickerIcon" data-applyon="child_birthdate_~number~" data-element="birthdate" data-id="~number~" id="datePickerApplyOn~number~"><i class="material-icons fs16">date_range</i></div>
                  <div class="pr">
                      <input data-id="~number~" id="child_birthdate_~number~" name="tmp_child_birthdate[~number~]" type="text" class="form-control dateClass childCoverageData child_birthdate_~number~" data-element="birthdate"/>
                        <label>DOB (MM/DD/YYYY)</label> 
                  </div>
              </div>
              <!-- <p class="error" id="error_child_birthdate_~number~"></p> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="populateCategoryDynamicDiv" style="display: none">
    <div class="panel panel-default enroll_plan_wrap category_div" id="category_~category_id~" data-category-id="~category_id~">
      <div class="panel-heading">
        <h4 class="panel-title" data-toggle="tooltip" data-placement="top" title="Click to Expand" data-container="body" data-trigger="hover">
          <a data-toggle="collapse" class="collapsed" data-parent="#product_category_div" href="#collapse_~category_id~" id="category_name_~category_id~" data-category-id="~category_id~">~category_name~</a>
        </h4>
      </div>
      <div id="collapse_~category_id~" class="panel-collapse collapse">
        <div class="panel-body">
          <div class="row">
            <?php if ($enrollmentLocation == "groupSide"){ ?>
              <div class="col-sm-12 m-b-20">
                <div class="waive_coverage">
                  <label for="waive_checkbox">
                    <input name="waive_checkbox[~category_id~]" type="checkbox" value="~category_id~" class="waive_checkbox" id="waive_checkbox_~category_id~"> Waive Plan</label>
                </div>
              </div>
              <input type="hidden" name="all_category[]" value="~category_id~">
              <!-- Waive Coverage html start -->
                <div style='display:none'>
                  <div id="waive_coverage_popup_~category_id~" data-category_id="~category_id~">
                    <div class="panel panel-default panel-shadowless mn">
                      <div class="panel-body">
                          <h4 class="text-action text-center">Waive Plan</h4>
                          <p class="fw500 m-b-25 text-center">Enrollees who decline one or more lines of plan offered for themselves or their dependents must provide a reason for declining, select reason below:</p>
                          <div class="clearfix p-l-30">
                            <div class="m-b-15">
                              <label class="mn label-input"><input type="radio"  name="waive_coverage_reason[~category_id~]" data-category_id="~category_id~" class="waive_coverage_reason" id="medicare_~category_id~" value="Medicare"> Medicare</label>
                            </div>
                            <div class="m-b-15">
                              <label class="mn label-input"><input type="radio" name="waive_coverage_reason[~category_id~]" data-category_id="~category_id~" class="waive_coverage_reason" id="not_enrolled_in_any_health_coverage_plan_but_do_not_want_this_coverage_~category_id~" value="Not enrolled in any health coverage plan, but do not want this coverage"> Not enrolled in any health plan, but do not want this plan</label>
                            </div>
                            <div class="m-b-15">
                              <label class="mn label-input"><input type="radio" name="waive_coverage_reason[~category_id~]" data-category_id="~category_id~" class="waive_coverage_reason" id="other_group_health_coverage_~category_id~" value="Other group coverage"> Other group plan</label>
                            </div>
                            <div class="m-b-15">
                              <label class="mn label-input"><input type="radio" name="waive_coverage_reason[~category_id~]" data-category_id="~category_id~" class="waive_coverage_reason" id="other_individual_health_coverage_~category_id~" value="Other individual coverage"> Other individual plan</label>
                            </div>
                            <div class="m-b-15">
                              <label class="mn label-input"><input type="radio" name="waive_coverage_reason[~category_id~]" data-category_id="~category_id~" class="waive_coverage_reason" id="other_~category_id~" value="Other"/>&nbsp;Other</label>
                            </div>
                            <span class="error" id="error_waive_coverage_reason_~category_id~"></span>
                            <div class="form-group" id="waive_coverage_other_reason_div_~category_id~" data-category_id="~category_id~" style="display: none;" />
                              <textarea class="waive_coverage_other_reason form-control" name="waive_coverage_other_reason[~category_id~]" id="waive_coverage_other_reason_~category_id~" placeholder="Explain.." data-category_id="~category_id~"></textarea>
                              <span class="error" id="error_waive_coverage_other_reason_~category_id~"></span>
                            </div>
                            <div class="clearfix text-center">
                              <a href="javascript:void(0);" class="btn btn-action waive_coverage_submit" id="waive_coverage_submit_~category_id~" data-category_id="~category_id~">Confirm</a>
                              <a href="javascript:void(0);" class="btn red-link waive_coverage_close" id="waive_coverage_close_~category_id~" data-category_id="~category_id~">Cancel</a>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              <!-- Waive Coverage html end -->
            <?php } ?>
          </div>
          <div id="populateCategoryProductsMainDiv~category_id~" data-category-id="~category_id~"></div>
        </div>
      </div>
    </div> 
</div>

<div id="populateProductDynamicDiv" style="display: none">
  <div id="populateCategoryProductsInnerDiv~category_id~" data-category-id="~category_id~">
    <input type="hidden" name="waive_products[~category_id~][]" class="waive_products_~category_id~" value="~product_id~">
    <div class="plan_body" id="product_body_div_~product_id~">
      <div class="plan_left">
        <h4 class="m-t-n fs20"><span id="product_name_~product_id~">~product_name~</span></h4>
          <p>~carrier_name~</p>
          <a href="javascript:void(0)" class="btn btn-info show_product_details" data-category-id="~category_id~" data-product-id="~product_id~">Details </a>
      </div>
      <div id="excluded_content_~product_id~" style="display: none">
        <div class="plan-center">
            <h2 class="font-normal">Excluded</h2>
            <p>This product is excluded because you added <span id="excluded_content_product_name_~product_id~"></span>.</p>
        </div>
      </div>
      <div id="packaged_content_~product_id~" style="display: none">
        <div class="plan-center">
            <h2 class="font-normal">Packaged</h2>
            <p>This product is excluded until you add at at least one of the following <span id="packaged_content_product_name_~product_id~">~packaged_product_name~</span>.</p>
        </div>
      </div>
      <div id="waived_content_~product_id~" style="display: none">
        <div class="plan-center">
            <h2 class="font-normal">Waived</h2>
        </div>
      </div>
      <div class="plan_right">
        <div class="row theme-form">
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="hidden" name="product_price[~product_id~]" id="product_price_~product_id~" value="0.00" data-excluded-product-for=""  data-is-required-for="" data-required-product="" data-packaged-product-for="~packaged_products~" class="hidden_product_price" data-product-id="~product_id~">
                    <input type="hidden" name="display_product_price[~product_id~]" id="display_product_price_~product_id~" value="0.00" class="hidden_product_price" data-product-id="~product_id~">
                    <input type="hidden" name="product_matrix[~product_id~]" id="product_matrix_~product_id~" value="0" data-product-id="~product_id~">
                    <input type="hidden" name="product_category[~product_id~]" id="product_category_~product_id~" value="" class="hidden_product_category" data-product-id="~product_id~">

                    <select name="product_plan[~product_id~]" id="product_plan_~product_id~" class="product_plan" data-category-id="~category_id~" data-product-id="~product_id~">
                        <option data-hiddent="true" value=""></option>
                        <?php if(!empty($prdPlanTypeArray)) { ?>
                          <?php foreach ($prdPlanTypeArray as $key => $value) { ?>
                            <option data-plan_name="<?= $value['title'] ?>" value="<?= $value['id'] ?>" data-prd-matrix-id= '' data-price='' data-display-price='' style="display: none"><?= $value['title'] ?>
                            </option>
                          <?php } ?>
                        <?php } ?>
                    </select>
                    <label>Plan</label>
                    <span class="error center_error" id="error_product_plan_~product_id~"></span>
                  </div>
              </div>
              <div class="col-sm-6 text-right" id="coverage_div_~product_id~">
                <div class="m-l-20"><a href="javascript:void(0)" class="btn btn-white-o btn-block addCoverage" id="addCoverageButton_~product_id~" data-category-id="~category_id~" data-product-id="~product_id~" data-is-add-on-product="~is_add_on_product~" data-pricing-model="~pricing_model~">Add Plan </a></div>
              </div>

              <div id="calcualte_rate_div_~product_id~" style="display: none">
                <div class="col-sm-6 text-right">
                      <div class="m-l-20"><a href="javascript:void(0)" class="btn btn-secondary btn-block calculateRate" data-category-id="~category_id~" data-product-id="~product_id~" id="calculateRateButton_~product_id~" data-product-name="~product_name~" data-pricing-model="~pricing_model~" data-is-add-on-product="~is_add_on_product~">Calculate Rate </a>
                      </div>
                </div>
              </div>
          </div>
          <div class="plan_price">
              <table >
                  <tr>
                      <td><h3 class="mn fs26">$<span id="product_price_label_~product_id~"></span><small class="fs10"><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?></small></h3></td>
                      <td class="text-light-gray fs12">Estimated <?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'Paycheck':'~member_payment_type~'; ?> <br /> Premium </td>
                  </tr>
              </table>
          </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    <div class="collapse" id="product_calcualate_rate_~product_id~">
      <div class="plan_body_expand">
        <div class="pull-left">
            <h4 class="m-t-n fs20">~product_name~ - <span class="fw300">Calculate Rate</span></h4>
            <p class="text-action max_benefit_amount_instruction_~product_id~" style="display: none;">Maximum benefit amount for this product is <span class="max_benefit_percentage_~product_id~"></span> of your monthly salary with <span class="max_benefit_amount_~product_id~"></span> maximum.</p>
        </div>
        <div class="right_plan_box">
            <div class="p-10 fs18">
                <div id="calculate_rate_plan_name_~product_id~"></div>
                <div class="fw300">$<span id="calculate_rate_price_~product_id~">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?></div>
            </div>
            <div class="bottom_btn">
                <a href="javascript:void(0)" class="btn btn-white-o calculatedCoverage" data-category-id="~category_id~" data-product-id="~product_id~" id="calculatedCoverage_~product_id~" data-pricing-model="~pricing_model~" data-is-add-on-product="~is_add_on_product~">Calculate Plan </a>

                <a href="javascript:void(0)" class="btn btn-white-o addCalculatedCoverage" data-category-id="~category_id~" data-product-id="~product_id~" id="addCalculatedCoverage_~product_id~" disabled data-pricing-model="~pricing_model~" data-is-add-on-product="~is_add_on_product~">Add Plan </a>

            </div>
        </div>
        <div class="clearfix"></div>
        <span class="error center_error pull-right" id="error_add_coverage_~product_id~"></span>
        <div class="clearfix"></div>
        <div id="inner_calculate_rate_main_div_~product_id~" data-category-id="~category_id~" data-product-id="~product_id~"></div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="plan_details_bottom collapse" id="product_details_~product_id~" >
    </div>
  </div>
</div>

<div id="productQuestionDynamicDiv" style="display: none">
  <div id="productQuestionInnerDiv_~product_id~_~enrollee_type~_~number~" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" class="productQuestionInnerDiv_~product_id~_~enrollee_type~ coverage_form"> 
    <div class="hline-title" id="addChildQuestion_~product_id~_~enrollee_type~_~number~_div">
      <span class="text-action fw500">~Enrollee_Type~ <label data-display-number="~number~" class="display_number_~product_id~_~enrollee_type~" id="display_number_~product_id~_~enrollee_type~_~number~">~child_counter~</label> Enrollee</span>
      <a href="javascript:void(0)" class="btn btn-action pull-right addChildQuestion" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" id="addChildQuestion_~product_id~_~enrollee_type~_~number~" style="display: none">+ Child</a>
      
      <a href="javascript:void(0)" class="pull-right removeChildQuestion btn red-link fw500" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" id="removeChildQuestion_~product_id~_~enrollee_type~_~number~" style="display: none">Remove</a>
    </div>
    <p>Fill out required information for this product</p>
    <div class="row enrollment_auto_row theme-form">
      <div class="col-lg-4 col-md-6 col-sm-6 ~enrollee_type~_annual_salary_div_~product_id~_~number~" id="~enrollee_type~_annual_salary_div_~product_id~_~number~" style="display: none;">
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][annual_salary]" id="hidden_~enrollee_type~_~product_id~_~number~_annual_salary" value="" class="hidden_~enrollee_type~_annual_salary_~number~">

          <input type="text" name="~enrollee_type~[~product_id~][annual_salary]" autocomplete="off" value="" class="form-control ~enrollee_type~_annual_salary_~number~ additional_question additional_tmp_question formatPricing" data-enrollee-type="~enrollee_type~" id="annual_salary_input_~product_id~" data-product-id="~product_id~" data-id="~number~" data-element="annual_salary">
          <label>Annual Salary</label>
        </div>
      </div>
    </div>
    <div class="row enrollment_auto_row theme-form">
      <div class="monthly_benefit_range col-lg-9 col-md-8 col-sm-8 ~enrollee_type~_monthly_benefit_percentage_div_~product_id~_~number~" id="~enrollee_type~_monthly_benefit_percentage_div_~product_id~_~number~" style="display: none;">
        <p class="m-b-15">Select Monthly Benefit Amount</p>
        <div class="phone-control-wrap">
          <div class="phone-addon w-30 v-align-top"><span class="m-r-5">0%</span></div>
          <div class="phone-addon v-align-top">
              <input type="hidden" name="hidden_~enrollee_type~[~product_id~][monthly_benefit_percentage]" id="hidden_monthly_salary_percentage_~product_id~" value="" class="hidden_~enrollee_type~_monthly_benefit_percentage_~number~" autocomplete="off">
              <input 
                type="text"
                min="0"                    
                max="100"                  
                step="0.01"                   
                value="" id="monthly_salary_percentage_~product_id~" class="rangeslider_~product_id~ form-control monthly_benefit_percentage_~product_id~ additional_question additional_tmp_question" data-orientation="vertical" name="~enrollee_type~[~product_id~][monthly_benefit_percentage]" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="monthly_benefit_percentage">
          </div>
          <div class="phone-addon w-30 v-align-top"><span class="m-l-10 max_percentage_~product_id~">60%</span></div>
        </div>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-4 ~enrollee_type~_monthly_benefit_amount_div_~product_id~ monthly_benefit_range monthly_benefit_amount_div_~product_id~" style="display: none;">
        <p class="fw600 text-center">Your Monthly Benefit</p>
        <div class="m-b-25">
          <input type="text" class="form-control formatPricing monthly_benefit_amount_input" data-product_id="~product_id~" name="monthly_benefit_amount_~product_id~" id="monthly_benefit_amount_~product_id~">
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_fname_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][fname]" id="hidden_~enrollee_type~_~product_id~_~number~_fname" value="" class="hidden_~enrollee_type~_fname_~number~">
          <input type="text" name="~enrollee_type~[~product_id~][~number~][fname]" autocomplete="off" value="" class="form-control ~enrollee_type~_fname_~number~ additional_question additional_tmp_question" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="fname" disabled>
          <label>First Name</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 " id="~enrollee_type~_gender_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][gender]" id="hidden_~enrollee_type~_~product_id~_~number~_gender" value="" class="hidden_~enrollee_type~_gender_~number~">

          <div class="btn-group colors" data-toggle="buttons">
            <label class="btn btn-info ~enrollee_type~_gender">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][gender]" value="Male" class="js-switch  ~enrollee_type~_gender_~number~ additional_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="gender" disabled> Male
            </label>
            <label class="btn btn-info ~enrollee_type~_gender">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][gender]" value="Female" class="js-switch ~enrollee_type~_gender_~number~ additional_question additional_tmp_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="gender" disabled> Female
            </label>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_birthdate_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon datePickerIcon" data-applyon="birthdate_~enrollee_type~_~product_id~_~number~"><i class="material-icons fs16">date_range</i></div>
          <div class="pr">
            <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][birthdate]" id="hidden_~enrollee_type~_~product_id~_~number~_birthdate" value="" class="hidden_~enrollee_type~_birthdate_~number~">
            <input  type="text" class="form-control dateClass ~enrollee_type~_birthdate_~number~ additional_question additional_tmp_question" name="~enrollee_type~[~product_id~][~number~][birthdate]"  id="birthdate_~enrollee_type~_~product_id~_~number~" value="" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="birthdate" disabled>
            <label>DOB (MM/DD/YYYY)</label>
          </div>
        </div>
      </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_zip_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][zip]" id="hidden_~enrollee_type~_~product_id~_~number~_zip" value="" class="hidden_~enrollee_type~_zip_~number~">
          <input type="text" name="~enrollee_type~[~product_id~][~number~][zip]" autocomplete="off" value="" class="form-control ~enrollee_type~_zip_~number~ additional_question additional_tmp_question" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="zip" disabled>
          <label>Zip Code</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_smoking_status_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][smoking_status]" id="hidden_~enrollee_type~_~product_id~_~number~_smoking_status" value="" class="hidden_~enrollee_type~_smoking_status_~number~">
          <div class="btn-group colors" data-toggle="buttons">
            <label class="btn btn-info">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][smoking_status]" value="Y" class="js-switch ~enrollee_type~_smoking_status_~number~ additional_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="smoking_status" disabled> Smokes
            </label>
            <label class="btn btn-info">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][smoking_status]" value="N" class="js-switch ~enrollee_type~_smoking_status_~number~ additional_question additional_tmp_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="smoking_status" disabled> Non Smokes
            </label>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_tobacco_status_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][tobacco_status]" id="hidden_~enrollee_type~_~product_id~_~number~_tobacco_status" value="" class="hidden_~enrollee_type~_tobacco_status_~number~">
          <div class="btn-group colors" data-toggle="buttons">
            <label class="btn btn-info">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][tobacco_status]" value="Y" class="js-switch ~enrollee_type~_tobacco_status_~number~ additional_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="tobacco_status" disabled> Tobacco
            </label>
            <label class="btn btn-info">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][tobacco_status]" value="N" class="js-switch ~enrollee_type~_tobacco_status_~number~ additional_question additional_tmp_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="tobacco_status" disabled> Non Tobacco
            </label>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_height_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][height]" id="hidden_~enrollee_type~_~product_id~_~number~_height" value="" class="hidden_~enrollee_type~_height_~number~">
          <select name="~enrollee_type~[~product_id~][~number~][height]" class="~enrollee_type~_height_~number~ additional_question additional_tmp_question" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="height" disabled data-live-search="true">
            <option value=""></option>
            <?php for($i=1; $i<=8;$i++){?>
              <?php for($j=0; $j<=11;$j++){?>
                <option value="<?=$i.'.'.$j?>">
                  <?= $i.' Ft. '. (($j>0) ? $j.' In. ' : '') ?>
                </option>
              <?php }?>
            <?php }?>
          </select>
          <label>Height</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_weight_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][weight]" id="hidden_~enrollee_type~_~product_id~_~number~_weight" value="" class="hidden_~enrollee_type~_weight_~number~">
          <select name="~enrollee_type~[~product_id~][~number~][weight]" class="~enrollee_type~_weight_~number~ additional_question additional_tmp_question" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="weight" disabled data-live-search="true">
            <option value=""></option>
            <?php for($i=1; $i<=1000;$i++){?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php }?>
          </select>
          <label>Weight</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_no_of_children_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][no_of_children]" id="hidden_~enrollee_type~_~product_id~_~number~_no_of_children" value="" class="hidden_~enrollee_type~_no_of_children_~number~">
          <select name="~enrollee_type~[~product_id~][~number~][no_of_children]" class="~enrollee_type~_no_of_children_~number~ additional_question additional_tmp_question" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="no_of_children" disabled data-live-search="true">
            <option value=""></option>
            <?php for($i=1; $i<=15;$i++){?>
                <option value="<?=$i?>"><?= $i ?></option>
            <?php }?>
          </select>
          <label># of Children</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_has_spouse_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][has_spouse]" id="hidden_~enrollee_type~_~product_id~_~number~_has_spouse" value="" class="hidden_~enrollee_type~_has_spouse_~number~">
          <div class="btn-group colors" data-toggle="buttons">
            <label class="btn btn-info">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][has_spouse]" value="Y" class="js-switch ~enrollee_type~_has_spouse_~number~ additional_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="has_spouse" disabled> Spouse
            </label>
            <label class="btn btn-info">
              <input type="radio" name="~enrollee_type~[~product_id~][~number~][has_spouse]" value="N" class="js-switch ~enrollee_type~_has_spouse_~number~ additional_question additional_tmp_question" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="has_spouse" disabled> No Spouse
            </label>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_benefit_amount_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][benefit_amount]" id="hidden_~enrollee_type~_~product_id~_~number~_benefit_amount" value="" class="hidden_~enrollee_type~_benefit_amount_~number~">

          <select name="~enrollee_type~[~product_id~][~number~][benefit_amount]" class="benefit_amount" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="benefit_amount" data-live-search="true" disabled data-live-search="true">
            <option value=""></option>
          </select>
          <label>Benefit Amount</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_in_patient_benefit_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][in_patient_benefit]" id="hidden_~enrollee_type~_~product_id~_~number~_in_patient_benefit" value="" class="hidden_~enrollee_type~_in_patient_benefit_~number~">

          <select name="~enrollee_type~[~product_id~][~number~][in_patient_benefit]" class="in_patient_benefit" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="in_patient_benefit" data-live-search="true" disabled data-live-search="true">
            <option value=""></option>
          </select>
          <label>In Patient Benefit</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_out_patient_benefit_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][out_patient_benefit]" id="hidden_~enrollee_type~_~product_id~_~number~_out_patient_benefit" value="" class="hidden_~enrollee_type~_out_patient_benefit_~number~">

          <select name="~enrollee_type~[~product_id~][~number~][out_patient_benefit]" class="out_patient_benefit" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="out_patient_benefit" data-live-search="true" disabled data-live-search="true">
            <option value=""></option>
          </select>
          <label>Out Patient Benefit</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_monthly_income_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][monthly_income]" id="hidden_~enrollee_type~_~product_id~_~number~_monthly_income" value="" class="hidden_~enrollee_type~_monthly_income_~number~">

          <select name="~enrollee_type~[~product_id~][~number~][monthly_income]" class="monthly_income" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="monthly_income" data-live-search="true" disabled data-live-search="true">
            <option value=""></option>
          </select>
          <label>Monthly Income</label>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6" id="~enrollee_type~_benefit_percentage_div_~product_id~_~number~" style="display: none;" >
        <div class="form-group">
          <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][benefit_percentage]" id="hidden_~enrollee_type~_~product_id~_~number~_benefit_percentage" value="" class="hidden_~enrollee_type~_benefit_percentage_~number~">

          <select name="~enrollee_type~[~product_id~][~number~][benefit_percentage]" class="benefit_percentage" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="benefit_percentage" data-live-search="true" disabled data-live-search="true">
            <option value=""></option>
          </select>
          <label>Benefit Percentage</label>
        </div>
      </div>
      <div class="col-sm-12 ~enrollee_type~_gap_plus_inputs_div_~product_id~_~number~" id="~enrollee_type~_gap_plus_inputs_div_~product_id~_~number~" style="display: none;">
        <div class="panel panel-default m-b-25">
          <div class="panel-body">
              <p class="fw500">Please enter your current 'out-of-pocket maximum' coverage:</p>
              <div class="row">
                  <div class="col-sm-4 out_of_pocket_maximum_div_~enrollee_type~_~product_id~">
                      <div class="form-group">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                              <div class="pr">
                                  <input name="out_of_pocket_maximum_~enrollee_type~_~product_id~" id="out_of_pocket_maximum_~enrollee_type~_~product_id~" type="text" class="form-control amount_input out_of_pocket_maximum_input" data-product_id="~product_id~">
                                  <label>Out-of-Pocket Max</label>
                                  <p class="error" id="error_out_of_pocket_maximum_~enrollee_type~_~product_id~"></p>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-12 m-b-20">
                    <p class="text-justify">Use the slider to decide the annual gap benefit you would like to purchase. Gap health coverage may cover many out-of-pocket expenses not paid by your major medical insurance such as deductibles, co-pays and coinsurance. It helps reduce your out-of-pocket expenses for treatment of accidents and sicknesses that can happen at any time.</p>
                  </div>
                  <div class="clearfix"></div>
                  <div class="col-sm-12 m-b-20">
                      <div class="phone-control-wrap pocket-range-slider">
                          <div class="phone-addon w-90 text-left v-align-middle"><span class="fw500 fs18 text-action gap_minimum_benefit_amount_label_~product_id~">$0.00</span></div>
                          <div class="phone-addon v-align-middle">
                              <input class="range-example-input" type="text" min="500" max="10000" name="gap_benefit_amount_slider_primary_~product_id~" id="gap_benefit_amount_slider_primary_~product_id~" step="500.00">
                              
                          </div>
                          <div class="phone-addon w-90 v-align-middle"><span class="fw500 fs18 text-action gap_maximum_benefit_amount_label_~product_id~">$0.00</span></div>
                      </div>
                  </div>
              </div>
              <hr>
              <div class="row">
                  <div class="col-sm-4 col-sm-offset-4">
                      <p class="text-center fw500 fs18">Annual Benefit</p>
                      <div class="form-group">
                          <input type="text" class="form-control text-center" name="gap_benefit_amount_~enrollee_type~_~product_id~" id="gap_benefit_amount_~enrollee_type~_~product_id~" readonly="" value="500.00">
                          <p class="error" id="error_gap_benefit_amount_~enrollee_type~_~product_id~"></p>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        <div>
          <p>After clicking 'Calculate Coverage' in the top right section of this product, fill out the calculator below to see the impact Gap+ coverage may have on your take home pay.</p>
        </div>
        <div class="row">
          <div class="col-sm-4">
              <div class="panel panel-default m-b-25">
                  <div class="p-15 bg_dark_primary">
                      <h4 class="mn text-white">The Power of +</h4>
                  </div>
                  <div class="panel-body theme-form">
                      <p class="fw500">Payroll Type</p>
                      <div class="clearfix m-b-25">
                          <label class="radio-inline">
                              <input type="radio" name="gap_payroll_type_~enrollee_type~_~product_id~" data-product_id="~product_id~" value="Hourly" class="gap_payroll_type_radio gap_radio_input_~product_id~">Hourly
                          </label>
                          <label class="radio-inline">
                              <input type="radio" name="gap_payroll_type_~enrollee_type~_~product_id~" data-product_id="~product_id~" value="Salary" class="gap_payroll_type_radio gap_radio_input_~product_id~">Salary
                          </label>
                          <p class="error" id="error_gap_payroll_type_~enrollee_type~_~product_id~"></p>
                      </div>
                      <div class="row payroll_type_hourly_div_~product_id~" style="display:none;">
                          <div class="hourly_rates_row_~product_id~ hourly_rates_row_~product_id~_0" data-index='0'>
                              <div class="col-sm-6">
                                  <div class="form-group">
                                      <div class="input-group">
                                          <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                          <div class="pr">
                                              <input name="gap_payroll_type_hourly_wage_~enrollee_type~_~product_id~[0]" id="gap_payroll_type_hourly_wage_~enrollee_type~_~product_id~_0" type="text" data-index="0" data-product_id="~product_id~" class="form-control amount_input hourly_wage_input">
                                              <label>Hourly Wage</label>
                                          </div>
                                      </div>
                                      <p class="error" id="error_gap_payroll_type_hourly_wage_~enrollee_type~_~product_id~_0"></p>
                                  </div>
                              </div>
                              <div class="col-sm-4">
                                  <div class="form-group">
                                      <input name="gap_payroll_type_hours_~enrollee_type~_~product_id~[0]" id="gap_payroll_type_hours_~enrollee_type~_~product_id~_0" type="text" maxlength="2" data-index="0" data-product_id="~product_id~" class="form-control hours_input">
                                      <label>Hours</label>
                                      <p class="error" id="error_gap_payroll_type_hours_~enrollee_type~_~product_id~_0"></p>
                                  </div>
                              </div>
                          </div>
                          <div class="payroll_type_hourly_rates_div_~product_id~">

                          </div>
                          <div class="col-sm-12">
                              <a href="javascript:void(0);" class="red-link add_hourly_rate pull-right" data-product_id="~product_id~">+ Add Rate</a>
                          </div>
                      </div>
                      <div class="row payroll_type_salary_div_~product_id~" style="display:none;">
                          <div class="col-sm-12">
                              <div class="form-group">
                                  <div class="input-group">
                                      <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                      <div class="pr">
                                          <input name="gap_payroll_type_salary_~enrollee_type~_~product_id~" id="gap_payroll_type_salary_~enrollee_type~_~product_id~" data-product_id="~product_id~" type="text" class="form-control amount_input gap_payroll_type_salary_input">
                                          <label>Annual Salary</label>
                                      </div>
                                  </div>
                                  <p class="error" id="error_gap_payroll_type_salary_~enrollee_type~_~product_id~"></p>
                              </div>
                          </div>
                      </div>
                      <p class="fw500">Marital Status</p>
                      <div class="clearfix m-b-25">
                          <label class="radio-inline">
                              <input type="radio" name="gap_marital_status_~enrollee_type~_~product_id~" value="SINGLE" class=" gap_radio_input_~product_id~">Single
                          </label>
                          <label class="radio-inline">
                              <input type="radio" name="gap_marital_status_~enrollee_type~_~product_id~" value="MARRIED" class=" gap_radio_input_~product_id~">Married
                          </label>
                          <p class="error" id="error_gap_marital_status_~enrollee_type~_~product_id~"></p>
                      </div>
                      <p class="fw500">Pay Frequency</p>
                      <div class="form-group">
                          <select name="gap_pay_frequency_~enrollee_type~_~product_id~" id="gap_pay_frequency_~enrollee_type~_~product_id~" class="gap_select_input_~product_id~ gap_pay_frequency_select" data-product_id="~product_id~">
                              <option value=""></option>
                              <option value="DAILY">Daily</option>
                              <option value="WEEKLY">Weekly</option>
                              <option value="BI_WEEKLY">Bi-Weekly</option>
                              <option value="SEMI_MONTHLY">Semi-Monthly</option>
                              <option value="MONTHLY">Monthly</option>
                              <option value="QUARTERLY">Quarterly</option>
                              <option value="SEMI_ANNUAL">Semi-Annually</option>
                              <option value="ANNUAL">Annually</option>
                          </select>
                          <label>Select</label>
                          <p class="error" id="error_gap_pay_frequency_~enrollee_type~_~product_id~"></p>
                      </div>
                      <p class="fw500">Default Allowances&nbsp;<i class="fa fa-info-circle" aria-hidden="true"></i></p>
                      <div class="row">
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <select class="gap_select_input_~product_id~ has-value" name="gap_default_allowances_federal_~enrollee_type~_~product_id~" id="gap_default_allowances_federal_~enrollee_type~_~product_id~">
                                      <?php
                                      for ($i=0; $i < 13; $i++) { 
                                        echo "<option value='".$i."' ".($i==1?'selected':'').">".$i."</option>";
                                      }
                                      ?>
                                  </select>
                                  <label>Federal</label>
                              </div>
                          </div>
                      </div>
                      <div class="gap_tax_deduct">
                          <div class="gap_text_warp">
                              <div class="gap_text_head">
                                  Pre-Tax Deductions
                                  <a href="javascript:void(0);" class="view_pre_tax_deduct_~product_id~ gap_add" data-product_id="~product_id~">+</a>
                              </div>
                              <div class="gap_text_body">
                                  <div class="table-responsive">
                                      <input type="hidden" name="pre_tax_deductions_~enrollee_type~_~product_id~" id="input_pre_tax_deductions_~product_id~">
                                      <table cellspacing="0" cellpadding="0" width="100%" border="0">
                                          <tbody class="pre_tax_deductions_tbody_~product_id~">
                                              
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                          <div class="gap_text_warp">
                              <div class="gap_text_head">
                                  Post-Tax Deductions
                                  <a href="javascript:void(0);" class="view_post_tax_deduct_~product_id~ gap_add" data-product_id="~product_id~">+</a>
                              </div>
                              <div class="gap_text_body">
                                  <div class="table-responsive">
                                      <input type="hidden" name="post_tax_deductions_~enrollee_type~_~product_id~" id="input_post_tax_deductions_~product_id~">
                                      <table cellspacing="0" cellpadding="0" width="100%" border="0">
                                          <tbody class="post_tax_deductions_tbody_~product_id~">
                                              
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="m-t-30 text-center">
                          <button type="button" class="btn btn-action gap_btn_calculate_coverage" data-product-id="~product_id~" data-pricing-model="~pricing_model~">Calculate PayCheck</button>
                      </div>
                      <div>
                        <p class="error gap_input_errors_~product_id~"></p>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-sm-8">
              <div class="panel panel-default m-b-25">
                  <div class="p-15 bg_dark_primary">
                      <h4 class="mn text-white">PayCheck Estimator</h4>
                  </div>
                  <div class="panel-body pn">
                      <div class="table-responsive">
                          <table class="table gap_table text-center">
                             <thead>
                              <tr>
                                  <th class="text-left">Gross Income <span class="gross_income_~enrollee_type~_~product_id~"></span></th>
                                  <th>Without Gap +</th>
                                  <th class="bg-success text-white">With Gap +</th>
                              </tr>
                          </thead>
                              <tbody>
                                  <tr>
                                      <td class="text-left">
                                          <p><strong>Federal Taxes</strong></p>
                                          <p><strong>State Taxes</strong></p>
                                          <p class="p-l-30"><i>FICA</i></p>
                                          <p class="p-l-30"><i>Medicare</i></p>
                                      </td>
                                      <td>
                                          <p class="text-danger without_gap_federal_taxes_~enrollee_type~_~product_id~">$0.00</p>
                                          <p class="text-danger without_gap_state_taxes_~enrollee_type~_~product_id~">$0.00</p>
                                          <p class="text-danger without_gap_fica_~enrollee_type~_~product_id~">$0.00</p>
                                          <p class="text-danger without_gap_medicare_~enrollee_type~_~product_id~">$0.00</p>
                                      </td>
                                      <td>
                                          <p class="text-danger with_gap_federal_taxes_~enrollee_type~_~product_id~">$0.00</p>
                                          <p class="text-danger with_gap_state_taxes_~enrollee_type~_~product_id~">$0.00</p>
                                          <p class="text-danger with_gap_fica_~enrollee_type~_~product_id~">$0.00</p>
                                          <p class="text-danger with_gap_medicare_~enrollee_type~_~product_id~">$0.00</p>
                                      </td>
                                  </tr>
                                  <tr>
                                      <td class="text-left">
                                          <p><strong>PreTax Deductions</strong></p>
                                          <p class="p-l-30"><i>Gap+ Premium</i></p>
                                          <div class="pre_tax_deductions_line_items_names_~enrollee_type~_~product_id~"></div>
                                          <p><strong>PostTax Deductions</strong></p>
                                          <div class="post_tax_deductions_line_items_names_~enrollee_type~_~product_id~"></div>
                                      </td>
                                      <td>
                                          <p class="text-danger">&nbsp;</p>
                                          <p class="without_gap_premium_~enrollee_type~_~product_id~">$0.00</p>
                                          <div class="pre_tax_deductions_line_items_totals_~enrollee_type~_~product_id~"></div>
                                          <p class="text-danger">&nbsp;</p>
                                          <div class="post_tax_deductions_line_items_totals_~enrollee_type~_~product_id~"></div>
                                      </td>
                                      <td>
                                          <p class="text-danger">&nbsp;</p>
                                          <p class="text-danger with_gap_premium_~enrollee_type~_~product_id~">$0.00</p>
                                          <div class="pre_tax_deductions_line_items_totals_~enrollee_type~_~product_id~"></div>
                                          <p class="text-danger">&nbsp;</p>
                                          <div class="post_tax_deductions_line_items_totals_~enrollee_type~_~product_id~"></div>
                                      </td>
                                  </tr>
                                  <tr class="bg_light_success">
                                      <td class="text-left"><i class="fa fa-info-circle" aria-hidden="true" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-container="body" data-content="<p class='m-b-20'>Participating in health screening and treatments qualify you to receive monthly claim payments from your GAP+ policy. These payments offset the cost of your GAP+ policy, making excellent coverage much easier to afford. Depending on your coverage level and income, you could fund additional policies through Triada just by enrolling in GAP+.</p><p class='fw600'>Example</p><p>Payments are deposited into Ed’s bank account. It is deposited on the same day as his paycheck and help offset the cost of his GAP+ policy.</p>" data-html="true"></i> Claim Payment</td>
                                      <td class="without_gap_claim_payment_~enrollee_type~_~product_id~">$0.00</td>
                                      <td class="text-success fs18 with_gap_claim_payment_~enrollee_type~_~product_id~">$0.00</td>
                                  </tr>
                                  <tr>
                                      <td class="text-left"><strong>Take Home</strong></td>
                                      <td class="without_gap_take_home_~enrollee_type~_~product_id~">$0.00</td>
                                      <td class="text-white fs24 bg-success with_gap_take_home_~enrollee_type~_~product_id~">$0.00</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
              <div class="panel panel-default m-b-25">
                  <div class="panel-body">
                      <div class="text-center gap_savings">
                          <p class="fs24 m-b-30">With your savings…</p>
                          <p class="fs18 custom_savings_recommend_text" style="display: none;"></p>
                          <div class="savings_recommend_text">
                              <h4 class="saving_prd_name_~product_id~"></h4>
                              <!-- <p class="saving_prd_desc_~product_id~"></p> -->
                              <table cellspacing="0" cellpadding="0" border="0" align="center">
                                  <tbody>
                                      <tr>
                                          <td class="fs18">Starting at: <strong class="text-success">&nbsp;&nbsp;&nbsp;<span class="saving_prd_price_~product_id~"></span></strong></td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <table width="180px " class="fs16 fw600" align="right">
        <tr>
            <td>~Enrollee_Type~ Rate:</td>
            <td class="text-action text-right">$<span class="calculate_rate_price_~product_id~_~Enrollee_Type~" id="calculate_rate_price_~product_id~_~Enrollee_Type~_~number~">0.00</span></td>
        </tr>
    </table>
    <div class="clearfix"></div>
  </div>
</div>

<div style="display:none;">
  <div class="iframe" id="additional_offer_popup">
      <div class="panel panel-default panel-block">
            <div class="panel-heading"> Additional Offering</div>
            <div class="panel-body">
                <p class="m-b-25">Would like to add a rider to any of the enrollees on this products?</p>
                <div class="text-center">
                    <a href="javascript:void(0)" class="btn btn-action">Yes</a>
                    <a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close();">No</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="inline_colorbox_div" style="display: none;">
  <div id="productDetailsColorbox">
    <div class="panel panel-default">
      <div class="panel-heading br-b">Product Details</div>
      <div class="panel-body panel-shadowless" id="productDetailBody"> </div>
    </div>
  </div>
</div>

<table style="display: none">
  <tbody id="addToCartDynamicTable" style="display: none">
    <tr id="cart_product_~product_id~" class="cart_products">
        <td>~product_name~ <div class="text-muted m-b-10">~plan_name~</div></td>
        <td class="text-right"><strong class="text-primary">$~product_price~ </strong></td>
        <td class="text-right"><strong class="text-primary contibution_price_td">$~group_price~ </strong></td>
        <td class="text-right"><strong class="text-primary contibution_price_td">$~total_price~ </strong></td>
        <td class="text-right" id="remove_product_from_cart_div_~product_id~">
          <a href="javascript:void(0);" class="text-action removeCartProduct" data-product-id="~product_id~">
            <i class="material-icons fs14">cancel</i>
          </a>
        </td>
    </tr>
  </tbody>
</table>

<div style="display: none">
  <div id="autoAssignColorboxDiv">
    <div class="panel panel-default panel-shadowless mn">
      <div class="panel-body">
        <p class="text-center p-15 mn">An additional product is automatically assigned when enrolling in <span id="autoAssign_product_name"></span>.  The product(s) is listed below, accept by clicking “Checkmark” button or reject by clicking “X” button:</p>
        <div id="autoAssignColorboxProductDiv"></div>
        <div class="text-right m-b-30">
          Total:&nbsp;&nbsp;$<span id="autoAssignTotal">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?>
        </div>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="autoAssign_colorbox_confirm">Confirm</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="autoAssignColorboxProductDynamicDiv" style="display: none;">
  <div class="autoAssign_colorbox_product" id="autoAssign_colorbox_product_~product_id~">
    <div class="auto_assign_box">
      <div class="media">
        <div class="media-left">
          <a href="javascript:void(0);" id="autoAssginedProductApproved_~product_id~" class="autoAssginedProductApproved text-light-gray" data-product-id="~product_id~"><i class="material-icons  w-30" style="font-size: 28px;">check_circle_outline</i></a>
          <div class="clearfix"></div>
          <a href="javascript:void(0);" id="autoAssginedProductReject_~product_id~" class="autoAssginedProductReject text-light-gray" data-product-id="~product_id~"><i class="material-icons w-30" style="font-size: 28px;">highlight_off</i> </a>
        </div>
        <div class="media-body">
          <p class="fw500">~product_name~</p>
          <div class="theme-form pr max-w175">
            <div class="form-group">
              <select id="autoAssign_colorbox_plan_~product_id~" class="autoAssign_colorbox_plan" data-product-id="~product_id~">
              </select>
              <label>Plan</label>
            </div>
          </div>
        </div>
        <div class="media-right">
          <div class="text-right">
            <a href="javascript:void(0);" class="text-info fw500 autoAssign_colorbox_details" data-product-id="~product_id~">Details <i class="fa fa-external-link" aria-hidden="true"></i></a>
            <p class="fs16 m-t-20 m-b-0">$<span id="autoAssign_colorbox_price_~product_id~">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'~/member_payment_type~'; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div style="display: none">
  <div id="requiredColorboxDiv">
    <div class="panel panel-default panel-shadowless mn">
      <div class="panel-body">
        <p class="text-center p-15 mn">An additional product is required when enrolling in <span id="required_product_name"></span>.  The product(s) is listed below, accept by selecting plan needs and clicking "Confirm" button:</p>
        <div id="requiredColorboxProductDiv"></div>
        <div class="text-right m-b-30">
          Total:&nbsp;&nbsp;$<span id="requiredTotal">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?>
        </div>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="required_colorbox_confirm">Confirm</a>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="requiredColorboxProductDynamicDiv" style="display: none;">
  <div class="required_colorbox_product" id="required_colorbox_product_~product_id~" data-product-id="~product_id~">
    <div class="auto_assign_box">
      <div class="media">
        <div class="media-body">
          <p class="fw500">~product_name~</p>
          <div class="theme-form pr max-w175">
            <div class="form-group">
              <select id="required_colorbox_plan_~product_id~" class="required_colorbox_plan" data-product-id="~product_id~" data-continer="body">
                
              </select>
              <label>Plan</label>
            </div>
          </div>
        </div>
        <div class="media-right">
          <div class="text-right">
            <a href="javascript:void(0);" class="text-info required_colorbox_details fw500" data-product-id="~product_id~">Details <i class="fa fa-external-link" aria-hidden="true"></i></a>
            <p class="fs16 m-t-20 m-b-0">$<span id="required_colorbox_price_~product_id~">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
 
<div style="display: none">
  <div id="riderProductColorboxDiv">
    <div class="panel panel-default panel-shadowless mn">
      <div class="panel-body">
        <p class="text-center p-15 mn">Would you like to add a rider to this life insurance plan? <span id="riderProduct_product_name"></span>.  The product(s) is listed below, accept by clicking “Checkmark” button or reject by clicking “X” button:</p>
        <div id="riderProductColorboxProductDiv"></div>
        <div class="text-right m-b-30">
          Total:&nbsp;&nbsp;$<span id="riderProductTotal">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?>
        </div>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="riderProduct_colorbox_confirm">Confirm</a>
          <a href="javascript:void(0);" class="btn btn-action" id="riderProduct_colorbox_rejectAll">Reject All</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="riderProductColorboxProductDynamicDiv" style="display: none;">
  <div class="riderProduct_colorbox_product" id="riderProduct_colorbox_product_~product_id~">
    <div class="auto_assign_box">
      <div class="media">
        <div class="media-left">
          <a href="javascript:void(0);" id="riderProductApproved_~product_id~" class="riderProductApproved text-light-gray" data-product-id="~product_id~"><i class="material-icons  w-30" style="font-size: 28px;">check_circle_outline</i></a>
          <div class="clearfix"></div>
          <a href="javascript:void(0);" id="riderProductReject_~product_id~" class="riderProductReject text-light-gray" data-product-id="~product_id~"><i class="material-icons w-30" style="font-size: 28px;">highlight_off</i> </a>
        </div>
        <div class="media-body">
          <p class="fw500">~product_name~</p>
          <div class="theme-form pr max-w175">
            <div class="form-group ">
              <select id="riderProduct_colorbox_plan_~product_id~" class="riderProduct_colorbox_plan" data-product-id="~product_id~">
              </select>
              <label>Plan</label>
            </div>
          </div>
        </div>
        <div class="media-right">
          <div class="text-right">
            <a href="javascript:void(0);" class="text-info fw500 riderProduct_colorbox_details" data-product-id="~product_id~">Details <i class="fa fa-external-link" aria-hidden="true"></i></a>
            <p class="fs16 m-t-20 m-b-0">$<span id="riderProduct_colorbox_price_~product_id~">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div style="display: none;">
  <div id="healthyStepColorboxDiv">
    <div class="panel panel-default healthy_step_popup panel-shadowless mn">
      <div class="panel-body">
        <div class="text-center">
          <h4>Healthy Steps <a href="javascript:void(0);" id="healthyStepDetails" style="display: none;"><i class="fa fa-info-circle fa-lg text-info" aria-hidden="true"></i></a></h4>
          <p class="m-b-25">A one-time healthy step is required for this enrollment.  Please select which healthy step you would like to assign:</p>
        </div>
        <div class="btn-group" data-toggle="buttons" id="healthyStepMainDiv"></div>
        <div class="text-center m-t-10">
          <a href="javascript:void(0);" class="btn btn-action" id="healthyStepConfirm">Confirm</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="healthyStepDynamicDiv" style="display: none;">
  <label class="btn btn-info healthyStep" id="healthyStep_~healthy_step_id~" data-healthy-step-id="~healthy_step_id~">
    <input type="radio" name="healthyStep" id="healthyStep_button_~healthy_step_id~" data-healthy-step-id="~healthy_step_id~" autocomplete="off" class="js-switch healthyStep_button" value="~healthy_step_id~"> ~healthy_step_name~ - $~healthy_step_price~
  </label>
</div>

<div id="voice_record_dynamic_div" style="display: none">
  <div id="add_voiceRecord_~number~">
    <div class="form-group mn p-b-10">
      <div class="custom_drag_control"> 
        <span class="btn btn-info btn-sm">Choose File</span>
        <input type="file" class="gui-file" id="voice_physical_upload_~number~" name="voice_physical_upload[~number~]">
        <input type="text" class="gui-input" placeholder="Drag or Select File" size="40">
        <p class="error" id="error_voice_physical_upload_~number~"></p> 
      </div>
      <div class="clearfix"></div>
    </div> 
    <div class="form-group mn">
      <a href="javascript:void(0)" data-counter="~number~"class="text-danger m-l-10 m-b-10 pull-left remove_voiceRecording" data-toggle="tooltip" data-title="Remove additional recording">
        <i class="fa fa-remove fa-lg"></i>
      </a>
    </div>
  </div>
</div>


<div id="enrollment_summary_details_dynamic_div" style="display: none">
  <div class="summary_items" id="summary_items_~product_id~" data-product-id="~product_id~">
    <div class="summary_head clearfix">
      <div class="row">
      <div class="col-sm-6">
        <div class="pull-left">
            <h4 class="m-t-n fs20">~product_name~</h4>
            <p class="mn">~carrier_name~</p>
        </div>
      </div>
       <div class="col-sm-6">
          <div class="pull-right theme-form summary_coverage">
              <div class="input-group">
                  <div class="input-group-addon datePickerIcon" data-applyon="coverage_date_~product_id~"><i class="material-icons fs16">date_range</i></div>
                  <div class="pr">
                      <input name="coverage_date[~product_id~]" id='coverage_date_~product_id~' type="text" class="form-control coverage_date_input" size="27" data-product-id="~product_id~" data-member-payment-type="~member_payment_type~" onkeydown="return false">
                      <label class="fs12">Plan Start Date (MM/DD/YYYY)</label>  
                  </div>
              </div>
              <p class="error" id="error_coverage_date_~product_id~"></p>
          </div>
        </div>
    </div>
  </div>
  <div class="summary_table">
    <div class="table-responsive">
     <table class="<?=$table_class?>">
        <thead>
            <tr>
                <th>Primary Plan Holder</th>
                <th>Plan Type</th>
                <th class="text-center">Dependents Added</th>
                <th class="price_th"><h3 class="mn fs26">$~product_total~<span class="fs10"><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/~member_payment_type~'; ?></span></h3></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>~primary_member_name~</td>
                <td>~plan_name~</td>
                <td class="text-center"><a href="javascript:void(0)" data-product-id="~product_id~" class="text-action product_dependents" data-dependent-count="~dependent_count~">~dependent_count~</a></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    </div>
  </div>
</div>
</div>




<div style="display: none">
  <div id="dependent_edit_popup">
    <div class="panel panel-default panel block">
     <div class="panel-heading">
       <h4 class="mn"><span id="dependent_edit_popup_product_name"></span> - <span class="fw300"> <span id="dependent_edit_popup_count"></span> Records</span></h4>
     </div>
   <div class="panel-body">
     <div class="table-responsive">
        <table class="table table-borderless">
          <thead>
            <tr>
             <th>Relation</th>
             <th>Name</th>
             <th>DOB</th>
             <th width="100px;" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody id="dependent_edit_popup_body"></tbody>
        </table>
       <div class="text-center">
         <a href="javascript:void(0)" class="btn text-red" onclick="window.parent.$.colorbox.close()">Cancel</a>
       </div>
     </div>
   </div>
</div>
  </div>
</div>

<table style="display: none">
  <tbody id="dependent_edit_popup_dynamic_table" style="display: none">
    <tr>
       <td>~dep_relation~</td>
       <td>~dep_name~</td>
       <td>~dep_birthdate~</td>
       <td class="icons text-center"><a href="javascript:void(0)" class="dependent_popup_edit_button"><i class="fa fa-edit"></i></a></td>
    </tr>
  </tbody>
</table>

<div style="display: none">
  <div id="suggestedAddressPopup">
    <?php include('suggested_address.inc.php'); ?>
  </div>
</div>
<table style="display: none">
  <tbody class="pre_tax_deduction_row_template">
  <tr class="pre_tax_deduction_row_~product_id~ pre_tax_deduction_row_~product_id~_~index~" data-index="~index~">
     <td>
        <div class="pr">
           <input type="text" class="form-control" name="pre_tax_deduction_name_~product_id~[~index~]" id="pre_tax_deduction_name_~product_id~_~index~" maxlength="15">
           <label>Deduction Name</label>
           <p class="error" id="error_pre_tax_deduction_name_~product_id~_~index~"></p>
        </div>
     </td>
     <td>
        <div class="pr">
           <select name="pre_tax_deduction_method_~product_id~[~index~]" id="pre_tax_deduction_method_~product_id~_~index~" class="deduction_method_select" data-product_id="~product_id~" data-tax_type="pre_tax" data-index="~index~">
              <option value=""></option>
              <option value="fixed_amount">$ Fixed Amount</option>
              <option value="gross_pay">% Gross Pay</option>
           </select>
           <label>Method</label>
           <p class="error" id="error_pre_tax_deduction_method_~product_id~_~index~"></p>
        </div>
     </td>
     <td>
        <div class="pr">
           <input type="text" class="form-control deduction_amount_input" name="pre_tax_deduction_amount_~product_id~[~index~]" id="pre_tax_deduction_amount_~product_id~_~index~" data-product_id="~product_id~" data-tax_type="pre_tax" data-index="~index~">
           <p class="error" id="error_pre_tax_deduction_amount_~product_id~_~index~"></p>
        </div>
     </td>
     <td class="text-right"><strong class="row_total_amount pre_tax_deduction_row_total_~product_id~_~index~">$0.00</strong> <a href="javascript:void(0);" class="deduction_remove_row" data-tax_type="pre_tax" data-product_id="~product_id~" data-index="~index~">X</a></td>
  </tr>
  </tbody>
</table>
<table style="display: none">
  <tbody class="post_tax_deduction_row_template">
  <tr class="post_tax_deduction_row_~product_id~ post_tax_deduction_row_~product_id~_~index~" data-index="~index~">
     <td>
        <div class="pr">
           <input type="text" class="form-control" name="post_tax_deduction_name_~product_id~[~index~]" id="post_tax_deduction_name_~product_id~_~index~" maxlength="15">
           <label>Deduction Name</label>
           <p class="error" id="error_post_tax_deduction_name_~product_id~_~index~"></p>
        </div>
     </td>
     <td>
        <div class="pr">
           <select name="post_tax_deduction_method_~product_id~[~index~]" id="post_tax_deduction_method_~product_id~_~index~" class="deduction_method_select" data-product_id="~product_id~" data-tax_type="post_tax" data-index="~index~">
              <option value=""></option>
              <option value="fixed_amount">$ Fixed Amount</option>
              <option value="gross_pay">% Gross Pay</option>
           </select>
           <label>Method</label>
           <p class="error" id="error_post_tax_deduction_method_~product_id~_~index~"></p>
        </div>
     </td>
     <td>
        <div class="pr">
           <input type="text" class="form-control deduction_amount_input" name="post_tax_deduction_amount_~product_id~[~index~]" id="post_tax_deduction_amount_~product_id~_~index~" data-product_id="~product_id~" data-tax_type="post_tax" data-index="~index~">
           <p class="error" id="error_post_tax_deduction_amount_~product_id~_~index~"></p>
        </div>
     </td>
     <td class="text-right"><strong class="row_total_amount post_tax_deduction_row_total_~product_id~_~index~">$0.00</strong> <a href="javascript:void(0);" class="deduction_remove_row" data-product_id="~product_id~"  data-tax_type="post_tax" data-index="~index~">X</a></td>
  </tr>
  </tbody>
</table>
<div id="pretax_deduct_popover_content" style="display: none">
  <div class="table-responsive theme-form pretax_deduct_table deduct_table">
      <table cellspacing="0" cellpadding="0" style="width: 100%;" border="0">
         <thead>
            <tr>
               <th>Deduction Name</th>
               <th>Calculation Method</th>
               <th>Deduction Amount</th>
               <th class="text-right fs24"><a href="javascript:void(0);" class="close_deduction_popover" data-product_id='~product_id~' data-tax_type='pre_tax'><strong>X</strong></a></th>
            </tr>
         </thead>
         <tbody>
            <tr class="pre_tax_deduction_row_~product_id~" style="display: none;" data-index="-1">
              <td><div class="pr">Deduction Name</div></td>
              <td><div class="pr">Deduction Name</div></td>
              <td><div class="pr">Deduction Name</div></td>
              <td><div class="pr">Deduction Name</div></td>
            </tr>
            <tr>
               <td colspan="3"><strong>Total</strong></td>
               <td><strong class="pre_tax_deductions_total_~product_id~">$0.00</strong></td>
            </tr>
            <tr>
              <td colspan="3"><a href="javascript:void(0);" class="red-link add_deduction pull-left" data-product_id="~product_id~" data-tax_type='pre_tax'>+ Add Deduction</a></td>
              <td><a href="javascript:void(0);" class="btn btn-action-o deductions_done" data-product_id="~product_id~" data-tax_type='pre_tax'>Done</a></td>
            </tr>
         </tbody>
      </table>
   </div>
</div>
<div id="posttax_deduct_popover_content" style="display: none">
  <div class="table-responsive theme-form pretax_deduct_table deduct_table">
      <table cellspacing="0" cellpadding="0" style="width: 100%;" border="0">
         <thead>
            <tr>
               <th>Deduction Name</th>
               <th>Calculation Method</th>
               <th>Deduction Amount</th>
               <th class="text-right fs24"><a href="javascript:void(0);" class="close_deduction_popover" data-product_id='~product_id~' data-tax_type='post_tax'><strong>X</strong></a></th>
            </tr>
         </thead>
         <tbody>
            <tr class="post_tax_deduction_row_~product_id~" style="display: none;" data-index="-1">
              <td><div class="pr">Deduction Name</div></td>
              <td><div class="pr">Deduction Name</div></td>
              <td><div class="pr">Deduction Name</div></td>
              <td><div class="pr">Deduction Name</div></td>
            </tr>
            <tr>
               <td colspan="3"><strong>Total</strong></td>
               <td><strong class="post_tax_deductions_total_~product_id~">$0.00</strong></td>
            </tr>
            <tr>
              <td colspan="3"><a href="javascript:void(0);" class="red-link add_deduction pull-left" data-product_id="~product_id~" data-tax_type='post_tax'>+ Add Deduction</a></td>
              <td><a href="javascript:void(0);" class="btn btn-action-o deductions_done" data-product_id="~product_id~" data-tax_type='post_tax'>Done</a></td>
            </tr>
         </tbody>
      </table>
   </div>
</div>
<div class="hourly_rates_template" style="display: none">
    <div class="hourly_rates_row_~product_id~ hourly_rates_row_~product_id~_~index~" data-index="~index~">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    <div class="pr">
                        <input name="gap_payroll_type_hourly_wage_primary_~product_id~[~index~]" id="gap_payroll_type_hourly_wage_primary_~product_id~_~index~" type="text" data-index="~index~" data-product_id="~product_id~" class="form-control amount_input hourly_wage_input">
                        <label>Hourly Wage</label>
                    </div>
                </div>
                <p class="error" id="error_gap_payroll_type_hourly_wage_primary_~product_id~_~index~"></p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <input name="gap_payroll_type_hours_primary_~product_id~[~index~]" id="gap_payroll_type_hours_primary_~product_id~_~index~" type="text" maxlength="2" data-index="~index~" data-product_id="~product_id~" class="form-control hours_input">
                <label>Hours</label>
                <p class="error" id="error_gap_payroll_type_hours_primary_~product_id~_~index~"></p>
            </div>
        </div>
        <div class="col-sm-2">
            <a href="javascript:void(0);" class="remove_hourly_rate" data-product_id="~product_id~" data-index="~index~">X</a>
        </div>
    </div>
</div>