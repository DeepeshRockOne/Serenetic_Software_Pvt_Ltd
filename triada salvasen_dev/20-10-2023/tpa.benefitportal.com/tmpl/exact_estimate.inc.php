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
<div id="spouseCoverageDynamicDiv" style="display: none;">
  <hr>
  <p><span class="font-bold">Spouse</span> <a href="javascript:void(0);" id="spouseCoverageRemove" class="btn red-link" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Remove" data-placement="bottom">Remove</a></p>
  <div class="row enrollment_auto_row">
    <div class="col-sm-4">
        <div class="form-group">
          <input type="text" class="form-control spouseCoverageData spouse_fname_1" name="spouse_fname" id="spouse_fname_1" value="">
          <label>Spouse First Name</label>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
          <div class="btn-group btn-custom-group btn-group-justified">
              <div class="toggle-item">
                <input class="js-switch spouseCoverageData spouse_gender_1" type="radio" id="spouse_gender_male" name="spouse_gender" value="Male" />
                <label for="spouse_gender_male" class="btn btn-info spouse_gender_1">Male</label>
              </div>
              <div class="toggle-item">
                <input class="js-switch spouseCoverageData spouse_gender_1" type="radio" id="spouse_gender_female" name="spouse_gender" value="Female" />
                <label for="spouse_gender_female" class="btn btn-info">Female</label>
              </div>
          </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
          <div class="input-group">
              <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
              <div class="pr">
                <input type="text" class="form-control date_picker dateClass spouseCoverageData spouse_birthdate_1" name="spouse_birthdate" id="spouse_birthdate">
                <label>DOB</label>
              </div>
          </div>
        </div>
    </div>
    <?php /*
    <div class="col-sm-4" id="spouseVerificationAlready">
        <div class="form-group">
          <a href="javascript:void(0);" class="btn red-link" id="spouseVerification" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Add Verification Doc" data-placement="bottom">+ Verification Doc  <i class="fw300">(Optional)</i></a>
        </div>
    </div>
    <div id="spouseVerificationDiv" style="display: none;">
        <div class="col-sm-8">
          <div class="phone-control-wrap">
              <div class="phone-addon w-30">
                <div class="form-group">
                    <a href="javascript:void(0);" class="btn red-link"><i class="fa fa-info-circle"  data-container="body" data-toggle="popover" title="Ways to Verify Dependents" data-trigger="hover" data-placement="top" data-html="true" data-content="<p>Upload Drivers License</p>
                      <p>Other Form of Verification</p><p class='m-b-0'>Other Form of Verification</p>"></i></a>
                </div>
              </div>
              <div class="phone-addon">
                <div class="form-group">
                    <div class="custom_drag_control"> 
                      <span class="btn btn-info">Upload</span>
                      <input type="file" class="gui-file" id="coverage_spouse_verification_doc" name="coverage_spouse_verification_doc[0]">
                      <input type="text" class="gui-input" placeholder="Choose File(s)" size="">
                      <p class="error text-left" id="error_coverage_spouse_verification_doc_0"></p>
                    </div>
                </div>
              </div>
              <div class="phone-addon w-30">
                <div class="form-group">
                    <a href="javascript:void(0);" class="btn text-action btn-lg" id="spouseVerificationRemove" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Close Verification Doc" data-placement="bottom">X</a>
                </div>
              </div>
          </div>
        </div>
    </div>
    */?>
  </div>
</div>
<div id="childCoverageDynamicDiv" style="display: none;">
  <div id="childCoverageInnerDiv~number~" data-id="~number~" class="childCoverageInnerDiv">
    <hr>
    <p><strong>Child <span data-display-number="~number~" class="display_number" id="display_number_~number~">~number~</span> </strong> <a href="javascript:void(0);"  class="btn red-link removeChildCoverageInnerDiv" data-id="~number~" id="removeChildCoverageInnerDiv~number~" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Remove" data-placement="bottom">Remove</a></p>

    <div class="row enrollment_auto_row">
        <div class="col-sm-4">
          <div class="form-group">
              <input type="text" class="form-control childCoverageData child_fname_~number~" name="tmp_child_fname[~number~]" id="child_fname_~number~" data-id="~number~" data-element="fname"value="">
              <label>Child First Name</label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
              <div class="btn-group btn-custom-group btn-group-justified">
                <div class="toggle-item">
                    <input class="js-switch childCoverageData child_gender_~number~" type="radio" id="child_gender_male_~number~" data-id="~number~" name="child_gender[~number~]" value="Male" data-element="gender"/>
                    <label for="child_gender_male_~number~" id="child_gender_male_label_~number~" class="btn btn-info">Male</label>
                </div>
                <div class="toggle-item">
                    <input class="js-switch childCoverageData child_gender_~number~" type="radio" id="child_gender_female~number~" data-id="~number~" name="child_gender[~number~]" value="Female" data-element="gender" />
                    <label for="child_gender_female~number~" id="child_gender_female_label_~number~" class="btn btn-info">Female</label>
                </div>
              </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <div class="pr">
                    <input type="text" data-id="~number~" class="form-control date_picker dateClass childCoverageData child_birthdate_~number~" name="tmp_child_birthdate[~number~]" id="child_birthdate_~number~" value="" data-element="birthdate">
                    <label>DOB</label>
                </div>
              </div>
          </div>
        </div>
        <?php /*
        <div class="col-sm-4" id="childVerificationAlready~number~">
          <div class="form-group">
              <a href="javascript:void(0);" class="btn red-link childVerification" id="childVerification~number~" data-id="~number~" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Add Verification Doc" data-placement="bottom">+ Verification Doc  <i class="fw300">(Optional)</i></a>
          </div>
        </div>
        <div id="childVerificationDiv~number~" style="display: none;">
          <div class="col-sm-8">
              <div class="phone-control-wrap">
                <div class="phone-addon w-30">
                    <div class="form-group">
                      <a href="javascript:void(0);" class="btn red-link"><i class="fa fa-info-circle"  data-container="body" data-toggle="popover" title="Ways to Verify Dependents" data-trigger="hover" data-placement="top" data-html="true" data-content="<p>Upload Drivers License</p>
                          <p>Other Form of Verification</p><p class='m-b-0'>Other Form of Verification</p>"></i></a>
                    </div>
                </div>
                <div class="phone-addon">
                    <div class="form-group">
                      <div class="custom_drag_control"> 
                          <span class="btn btn-info">Upload</span>
                          <input type="file" class="gui-file coverage_child_verification_doc" id="coverage_child_verification_doc" name="coverage_child_verification_doc[~number~]">
                          <input type="text" class="gui-input" placeholder="Choose File(s)" size="">
                          <p class="error text-left" id="error_coverage_child_verification_doc_~number~"></p>
                      </div>
                    </div>
                </div>
                <div class="phone-addon w-30">
                    <div class="form-group">
                      <a href="javascript:void(0);" id="childVerificationRemove~number~" class="btn text-action btn-lg childVerificationRemove" data-id="~number~" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Close Verification Doc" data-placement="bottom">X</a>
                    </div>
                </div>
              </div>
          </div>
        </div>
        */ ?>
    </div>
  </div>
</div>

<div id="populateCategoryDynamicDiv" style="display: none">
    <div class="panel panel-default enroll_plan_wrap category_div" id="category_~category_id~" data-category-id="~category_id~">
      <div class="panel-heading">
        <h4 class="panel-title">
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
                              <label class="mn label-input"><input type="radio" name="waive_coverage_reason[~category_id~]" data-category_id="~category_id~" class="waive_coverage_reason" id="not_enrolled_in_any_health_coverage_plan_but_do_not_want_this_coverage_~category_id~" value="Not enrolled in any health coverage plan, but do not want this coverage"> Not enrolled in any health plan plan, but do not want this plan</label>
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
            <h2 class="font-bold m-t-0">Excluded</h2>
            <p class="mn">This product is excluded because you added <span id="excluded_content_product_name_~product_id~"></span>.</p>
        </div>
      </div>
      <div id="packaged_content_~product_id~" style="display: none">
        <div class="plan-center">
            <h2 class="font-bold m-t-0">Packaged</h2>
            <p class="mn">This product is excluded until you add at at least one of the following <span id="packaged_content_product_name_~product_id~">~packaged_product_name~</span>.</p>
        </div>
      </div>
      <div id="waived_content_~product_id~" style="display: none">
        <div class="plan-center">
            <h2 class="font-bold m-t-0">Waived</h2>
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
                      <td><h3 class="mn fs26">$<span id="product_price_label_~product_id~"></span><small class="fs10"><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?></small></h3></td>
                      <td class="text-light-gray fs12">Estimated <?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'Paycheck':'Monthly'; ?> <br /> Premium </td>
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
                <div class="fw300"><span bablic-exclude>$</span><span id="calculate_rate_price_~product_id~" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?></div>
            </div>
            <div class="bottom_btn">
                <a href="javascript:void(0)" class="btn btn-white-o calculatedCoverage" data-category-id="~category_id~" data-product-id="~product_id~" id="calculatedCoverage_~product_id~" data-pricing-model="~pricing_model~" data-is-add-on-product="~is_add_on_product~" data-from-tab="~from_tab~" data-bundleid="~bundleId~">Calculate Plan </a>

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
  <div id="requiredColorboxDiv">
    <div class="panel panel-default panel-shadowless mn">
      <div class="panel-body">
        <p class="text-center p-15 mn">An additional product is required when enrolling in <span id="required_product_name"></span>.  The product(s) is listed below, accept by selecting plan needs and clicking "Confirm" button:</p>
        <div id="requiredColorboxProductDiv"></div>
        <div class="text-right m-b-30">
          Total:&nbsp;&nbsp;<span bablic-exclude>$</span><span id="requiredTotal" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?>
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
            <p class="fs16 m-t-20 m-b-0"><span bablic-exclude>$</span><span id="required_colorbox_price_~product_id~" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?></p>
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
          Total:&nbsp;&nbsp;<span bablic-exclude>$</span><span id="riderProductTotal" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?>
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
            <p class="fs16 m-t-20 m-b-0"><span bablic-exclude>$</span><span id="riderProduct_colorbox_price_~product_id~" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?></p>
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
      <a href="javascript:void(0)" data-counter="~number~" class="text-danger m-l-10 m-b-10 pull-left remove_voiceRecording" data-toggle="tooltip" data-trigger="hover"  data-title="Remove additional recording">
        <i class="fa fa-remove fa-lg"></i>
      </a>
    </div>
  </div>
</div>


<div id="enrollment_summary_details_dynamic_div" style="display: none">
  <div class="summary_items" id="summary_items_~product_id~" data-product-id="~product_id~">
    <div class="summary_table">
      <div class="table-responsive">
      <table class="<?=$table_class?>">
          <thead>
              <tr>
                <th class="bg_dark_danger" colspan="2">
                  <h4 class="m-t-n fs20 text-white" bablic-exclude>~product_name~ - <small class="mn text-white">~carrier_name~</small></h4>
                </th>
                <th class="bg_dark_danger">
                  <div class="text-right">
                     <a href="<?= $HOST?>/group_enroll_planinfo.php?productId=~product_code~" class="group_enroll_planinfo bg_dark_danger" data-toggle="tooltip" data-trigger="hover" title="DETAILS"><i class="fa fa-eye fs18"></i></a>
                  </div>
                </th>
              </tr>
              <tr>
                  <th>Primary Policy Holder</th>
                  <th>Coverage Type</th>
                  <th class="text-center">Dependents Added</th>
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td>~primary_member_name~</td>
                  <td>~plan_name~</td>
                  <td class="text-center"><a href="javascript:void(0)" data-product-id="~product_id~" class="text-action product_dependents" data-dependent-count="~dependent_count~">~dependent_count~</a></td>
              </tr>
              <tr>
                  <td colspan="2">
                    <div class="theme-form form-inline summary_coverage">
                      <div class="input-group">
                        <div class="input-group-addon datePickerIcon" data-applyon="coverage_date_~product_id~"><i class="material-icons fs16">date_range</i></div>
                        <div class="pr">
                            <input name="coverage_date[~product_id~]" id='coverage_date_~product_id~' type="text" class="form-control coverage_date_input" size="27" data-product-id="~product_id~" onkeydown="return false">
                            <label class="fs12">Plan Start Date (MM/DD/YYYY)</label>  
                        </div>
                      </div>
                      <p class="error" id="error_coverage_date_~product_id~"></p>
                    </div>
                  </td>
                  <td class="pull-right">
                    <h3 class="mn fs26"><span bablic-exclude>$~product_total~</span><sub class="fs10">/pay period</sub></h3>
                  </td>
              </tr>
          </tbody>
      </table>
      </div>
    </div>
  </div>
  <hr />
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