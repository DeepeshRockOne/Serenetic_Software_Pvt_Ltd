<table style="display: none;">
   <tbody id="waived_coverage_dynamic_div" style="display: none">
      <tr id="row_waived_coverage_~category_id~">
         <td>~category_title~<p class="error" id="error_waive_checkbox_~category_id~"></p></td>
         <td class="label_waive_coverage_reason_~category_id~">-</td>
         <td><a href="javascript:void(0);" class="waived-badge btn_waived_coverage" data-category_id="~category_id~">Waive Coverage</a>
          <input name="waive_checkbox[~category_id~]" type="checkbox" value="~category_id~" class="waive_checkbox" id="waive_checkbox_~category_id~" checked="checked" style="display:none;">
          <!-- Waive Coverage html start -->
          <div style='display:none'>
            <div id="waive_coverage_popup_~category_id~" data-category_id="~category_id~">
              <div class="panel panel-default panel-shadowless mn theme-form">
                <div class="panel-heading">
                  <h4 class="panel-title">Waived Coverage</h4>
                </div>
                <div class="panel-body p-t-0 p-b-0">
                    <p>Enrollees who decline one or more lines of plan offered for themselves or their dependents must provide a reason for declining, select reason below:</p>
                    <div class="waive-block">
                      <div class="waive-block-head">
                          <a data-toggle="collapse" href="#waive_coverage_tab_~category_id~">
                            <i class="fa fa-info-circle fa-lg p-r-5"></i> ~category_title~
                            <span class="caret"></span>
                            <div class="waive-badge">Waived</div>
                          </a>
                      </div>
                      <div class="collapse in" id="waive_coverage_tab_~category_id~">
                        <div class="waive-block-body">
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
                            <a href="javascript:void(0);"  class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        <!-- Waive Coverage html end -->
         </td>
      </tr>
   </tbody>
</table>
<table style="display: none;">
   <tbody id="terms_condition_prd_dynamic_div" style="display: none">
        <tr id="row_terms_condition_prd_~product_id~">
            <td class="text-left">
              <input  type="checkbox" 
                  name="product_check[~product_id~]" 
                  id="product_check_~product_id~" 
                  class="product_terms_check"
                  value="~product_id~"><br>
              <span class="error" id="error_product_check_~product_id~"></span>
            </td>
            <td><span bablic-exclude>~product_category~</span></td> 
            <td>~product_name~</td>
            <td class="text-center">
              <a href="javascript:void(0);" data-product-id="~product_id_md5~" class="verification_terms"><i class="fa fa-eye fa-lg"></i></a>
            </td>
            <td id="td_terms_products_~product_category~"><span bablic-exclude>~coverage_date~</span></td>
            <td class="icons text-center">
              <a href="javascript:void(0);"  data-product-id="~product_id_md5~" class="prd_terms_popup"><i class="fa fa-file-text-o fa-lg"></i></a>
            </td>
        </tr>
    </tbody>
</table>
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
      <a href="javascript:void(0)" data-counter="~number~"class="text-danger m-l-10 m-b-10 pull-left remove_voiceRecording" data-toggle="tooltip" data-trigger="hover" data-title="Remove additional recording">
        <i class="fa fa-remove fa-lg"></i>
      </a>
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
        <div class="media-right mw-90">
          <div class="text-right">
            <a href="javascript:void(0);" class="text-info fw500 autoAssign_colorbox_details" data-product-id="~product_id~">Details <i class="fa fa-external-link" aria-hidden="true"></i></a>
            <p class="fs16 m-t-20 m-b-0" bablic-exclude>$<span id="autoAssign_colorbox_price_~product_id~" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div style="display: none">
  <div id="autoAssignColorboxDiv">
    <div class="panel panel-default panel-shadowless mn">
      <div class="panel-body">
        <p class="text-center p-15 mn">An additional product is automatically assigned when enrolling in <span id="autoAssign_product_name"></span>.  The product(s) is listed below, accept by clicking “Checkmark” button or reject by clicking “X” button:</p>
        <div id="autoAssignColorboxProductDiv"></div>
        <div class="text-right m-b-30">
          Total:&nbsp;&nbsp;<span bablic-exclude>$</span><span id="autoAssignTotal" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?>
        </div>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="autoAssign_colorbox_confirm">Confirm</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Required product div -->
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
            <p class="fs16 m-t-20 m-b-0" bablic-exclude>$<span id="required_colorbox_price_~product_id~" bablic-exclude>0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?></p>
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
          Total:&nbsp;&nbsp;$<span id="requiredTotal">0.00</span><?php echo $enrollmentLocation == "groupSide" || $is_group_member == "Y"?'':'/month'; ?>
        </div>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn btn-action" id="required_colorbox_confirm">Confirm</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div id='calculate_rate_main_dev' style="display:none">
    <div class="panel panel-default panel-shadowless mn">
        <div class="panel-heading br-b">
            <h4 class="panel-title"><span bablic-exclude>~product_name~</span> - <span class="fw300" bablic-include>Exact Estimate</span></h4>
        </div>
        <div class="panel-body theme-form">
            <p class="m-b-15">Please answer the additional questions to get an exact estimate on this product.</p>
            <p class="m-b-15 text-action max_benefit_amount_instruction_~product_id~" style="display: none;">Maximum benefit amount for this product is <span class="max_benefit_percentage_~product_id~"></span> of your monthly salary with <span class="max_benefit_amount_~product_id~"></span> maximum.</p>
            <div id="inner_calculate_rate_main_div_~product_id~"></div>
            <span class="error" id="error_add_coverage_~product_id~"></span>
        </div>
        <div class="panel-footer text-center">
            <a href="javascript:void(0);"   class="btn btn-info calculatedCoverage" data-product-id="~product_id~" id="calculatedCoverage_~product_id~" data-pricing-model="~pricing-model~" data-is-add-on-product="N" data-from-tab="~from_tab~" data-bundleid="~bundleId~">Calculate Plan</a>
            <a href="javascript:void(0);"   class="btn btn-action addCalculatePlanSelf" data-product-id="~product_id~" id="addCalculatedCoverage_~product_id~" data-pricing-model="~pricing-model~" data-is-add-on-product="N">Add Plan</a>
            <a href="javascript:void(0);" data-from-tab="~fromTab~" data-product-id="~product_id~" data-pricing-model="~pricing-model~" class="btn red-link cancelPopup">Close</a>
        </div>
    </div>
</div>

<div style="display:none;">
   <div id="productQuestionDynamicDiv">
      <div id="productQuestionInnerDiv_~product_id~_~enrollee_type~_~number~" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" class="productQuestionInnerDiv_~product_id~_~enrollee_type~ coverage_form" >
          <div class="clearfix m-b-15" id="addChildQuestion_~product_id~_~enrollee_type~_~number~_div">
            <span class="fw700">~Enrollee_Type~ <label data-display-number="~number~" class="display_number_~product_id~_~enrollee_type~" id="display_number_~product_id~_~enrollee_type~_~number~">~child_counter~</label> Enrollee</span>
            <a href="javascript:void(0)" class="pull-right addChildQuestion btn red-link" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" id="addChildQuestion_~product_id~_~enrollee_type~_~number~" style="display: none">+ Child</a>
            <a href="javascript:void(0)" class="pull-right removeChildQuestion btn red-link" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" id="removeChildQuestion_~product_id~_~enrollee_type~_~number~" style="display: none">Remove</a>
          </div>
          <div class="row">
            <div class="col-sm-6 ~enrollee_type~_annual_salary_div_~product_id~_~number~" id="~enrollee_type~_annual_salary_div_~product_id~_~number~" style="display: none;">
                <p class="fw500">Fill out required information for this product</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][annual_salary]" id="hidden_~enrollee_type~_~product_id~_~number~_annual_salary" value="" class="hidden_~enrollee_type~_annual_salary_~number~">
                  <input type="text" name="~enrollee_type~[~product_id~][annual_salary]" autocomplete="off" value="" class="form-control ~enrollee_type~_annual_salary_~number~ additional_question additional_tmp_question formatPricing" data-enrollee-type="~enrollee_type~" id="annual_salary_input_~product_id~" data-product-id="~product_id~" data-id="~number~" data-element="annual_salary">
                  <label>Annual Salary</label>
                </div>
            </div>

            <div class="col-sm-8 ~enrollee_type~_monthly_benefit_percentage_div_~product_id~_~number~" id="~enrollee_type~_monthly_benefit_percentage_div_~product_id~_~number~" style="display: none;">
                <p class="fw500">Select Monthly Benefit Amount</p>
                <div class="phone-control-wrap">
                  <div class="phone-addon w-30 v-align-top"><span class="m-r-5">0%</span></div>
                  <div class="phone-addon v-align-top">
                      <input type="hidden" name="hidden_~enrollee_type~[~product_id~][monthly_benefit_percentage]" id="hidden_monthly_salary_percentage_~product_id~" value="" class="hidden_~enrollee_type~_monthly_benefit_percentage_~number~">
                      <input 
                      type="text"
                      min="0"                    
                      max="100"                  
                      step="0.01"                   
                      value="" id="monthly_salary_percentage_~product_id~" class="rangeslider_~product_id~ form-control monthly_benefit_percentage_~product_id~ additional_question additional_tmp_question" data-orientation="vertical" name="~enrollee_type~[~product_id~][monthly_benefit_percentage]" autocomplete="off" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="monthly_benefit_percentage">
                      <label>Annual Salary</label>
                  </div>
                  <div class="phone-addon w-30 v-align-top"><span class="m-l-10 max_percentage_~product_id~">60%</span></div>
                </div>
            </div>

            <div class="col-sm-4 ~enrollee_type~_monthly_benefit_amount_div_~product_id~ monthly_benefit_range monthly_benefit_amount_div_~product_id~" style="display: none;">
              <p class="fw600 text-center">Your Monthly Benefit</p>
              <div class="m-b-25">
                <input type="text" class="form-control formatPricing monthly_benefit_amount_input" data-product_id="~product_id~" name="monthly_benefit_amount_~product_id~" id="monthly_benefit_amount_~product_id~">
              </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_fname_div_~product_id~_~number~" style="display: none;">
                <p >First Name</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][fname]" id="hidden_~enrollee_type~_~product_id~_~number~_fname" value="" class="hidden_~enrollee_type~_fname_~number~">
                  <input type="text" class="form-control ~enrollee_type~_fname_~number~ additional_question additional_tmp_question" name="~enrollee_type~[~product_id~][~number~][fname]" autocomplete="off" value="" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="fname" disabled>
                  <label>First Name</label>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_birthdate_div_~product_id~_~number~" style="display: none;">
                <p >Birth Date</p>
                <div class="form-group">
                  <div class="input-group">
                      <div class="input-group-addon datePickerIcon" data-applyon="birthdate_~enrollee_type~_~product_id~_~number~"><i class="material-icons fs16">date_range</i></div>
                      <div class="pr">
                        <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][birthdate]" id="hidden_~enrollee_type~_~product_id~_~number~_birthdate" value="" class="hidden_~enrollee_type~_birthdate_~number~">
                        <input type="text" class="form-control dateClass ~enrollee_type~_birthdate_~number~ additional_question additional_tmp_question" name="~enrollee_type~[~product_id~][~number~][birthdate]"  id="birthdate_~enrollee_type~_~product_id~_~number~" value="" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="birthdate" disabled>
                        <label class="label-wrap">DOB (MM/DD/YYYY)</label>
                      </div>
                  </div>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_zip_div_~product_id~_~number~" style="display: none;">
                <p >Zipcode</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][zip]" id="hidden_~enrollee_type~_~product_id~_~number~_zip" value="" class="hidden_~enrollee_type~_zip_~number~">
                  <input type="text" class="form-control ~enrollee_type~_zip_~number~ additional_question additional_tmp_question" name="~enrollee_type~[~product_id~][~number~][zip]" autocomplete="off" value="" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="zip" disabled>
                  <label>Zip Code</label>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_gender_div_~product_id~_~number~" style="display: none;">
                <p >Gender</p>
                <div class="form-group">
                    <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][gender]" id="hidden_~enrollee_type~_~product_id~_~number~_gender" value="" class="hidden_~enrollee_type~_gender_~number~">
                  <div class="radio-inline">
                      <input type="radio" name="~enrollee_type~[~product_id~][~number~][gender]" value="Male" class="~enrollee_type~_gender_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="gender" data-enrollee-type="~enrollee_type~">
                      <label>Male</label>
                  </div>
                  <div class="radio-inline">
                      <input type="radio" name="~enrollee_type~[~product_id~][~number~][gender]" value="Female" class="~enrollee_type~_gender_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="gender" data-enrollee-type="~enrollee_type~">
                      <label>Female</label>
                  </div>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_smoking_status_div_~product_id~_~number~" style="display: none;">
                <p >Smoking Status</p>
                <div class="form-group">
                    <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][smoking_status]" id="hidden_~enrollee_type~_~product_id~_~number~_smoking_status" value="" class="hidden_~enrollee_type~_smoking_status_~number~">
                  <div class="radio-inline">
                      <input type="radio" name="~enrollee_type~[~product_id~][~number~][smoking_status]" value="Y" class="~enrollee_type~_smoking_status_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="smoking_status" data-enrollee-type="~enrollee_type~">
                      <label>Yes</label>
                  </div>
                  <div class="radio-inline">
                      <input type="radio" name="~enrollee_type~[~product_id~][~number~][smoking_status]" value="N" class="~enrollee_type~_smoking_status_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="smoking_status" data-enrollee-type="~enrollee_type~">
                      <label>No</label>
                  </div>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_tobacco_status_div_~product_id~_~number~" style="display: none;">
                <p >Tobacco Status</p>
                <div class="form-group">
                    <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][tobacco_status]" id="hidden_~enrollee_type~_~product_id~_~number~_tobacco_status" value="" class="hidden_~enrollee_type~_tobacco_status_~number~">
                  <div class="radio-inline">
                    <input type="radio" name="~enrollee_type~[~product_id~][~number~][tobacco_status]" value="Y" class="~enrollee_type~_tobacco_status_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="tobacco_status" data-enrollee-type="~enrollee_type~">
                    <label>Yes</label>
                  </div>
                  <div class="radio-inline">
                    <input type="radio" name="~enrollee_type~[~product_id~][~number~][tobacco_status]" value="N" class="~enrollee_type~_tobacco_status_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="tobacco_status" data-enrollee-type="~enrollee_type~">
                    <label>No</label>
                  </div>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_height_div_~product_id~_~number~" style="display: none;">
                <p >Height</p>
                <div class="form-group">
                    <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][height]" id="hidden_~enrollee_type~_~product_id~_~number~_height" value="" class="hidden_~enrollee_type~_height_~number~">
                    <select name="~enrollee_type~[~product_id~][~number~][height]" class="~enrollee_type~_height_~number~ additional_question additional_tmp_question" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="height" data-live-search="true">
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

            <div class="col-sm-6" id="~enrollee_type~_weight_div_~product_id~_~number~" style="display: none;">
                <p >Weight</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][weight]" id="hidden_~enrollee_type~_~product_id~_~number~_weight" value="" class="hidden_~enrollee_type~_weight_~number~">
                  <select name="~enrollee_type~[~product_id~][~number~][weight]" class="~enrollee_type~_weight_~number~ additional_question additional_tmp_question " data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="weight" data-live-search="true">
                    <option value=""></option>
                    <?php for($i=1; $i<=1000;$i++){?>
                      <option value="<?= $i ?>"><?= $i ?></option>
                    <?php }?>
                  </select>
                  <label>Weight</label>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_no_of_children_div_~product_id~_~number~" style="display: none;">
                <p >Number Of Children</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][no_of_children]" id="hidden_~enrollee_type~_~product_id~_~number~_no_of_children" value="" class="hidden_~enrollee_type~_no_of_children_~number~">
                  <select name="~enrollee_type~[~product_id~][~number~][no_of_children]" class="~enrollee_type~_no_of_children_~number~ additional_question additional_tmp_question" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="no_of_children" data-live-search="true">
                    <option value=""></option>
                    <?php for($i=1; $i<=15;$i++){?>
                      <option value="<?=$i?>"><?= $i ?></option>
                    <?php }?>
                  </select>
                  <label># of Children</label>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_has_spouse_div_~product_id~_~number~" style="display: none;">
                <p >Has Spouse</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][has_spouse]" id="hidden_~enrollee_type~_~product_id~_~number~_has_spouse" value="" class="hidden_~enrollee_type~_has_spouse_~number~">
                  <div class="radio-inline">
                    <input type="radio" name="~enrollee_type~[~product_id~][~number~][has_spouse]" value="Y" class="~enrollee_type~_has_spouse_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="has_spouse" data-enrollee-type="~enrollee_type~">
                    <label>Yes</label>
                  </div>
                  <div class="radio-inline">
                    <input type="radio" name="~enrollee_type~[~product_id~][~number~][has_spouse]" value="N" class="~enrollee_type~_has_spouse_~number~ additional_question additional_tmp_question radio_btn_refresh" data-product-id="~product_id~" data-id="~number~" data-element="has_spouse" data-enrollee-type="~enrollee_type~">
                    <label>No</label>
                  </div>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_benefit_amount_div_~product_id~_~number~" style="display: none;">
                <p >Benefit Amount</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][benefit_amount]" id="hidden_~enrollee_type~_~product_id~_~number~_benefit_amount" value="" class="hidden_~enrollee_type~_benefit_amount_~number~">
                  <select name="~enrollee_type~[~product_id~][~number~][benefit_amount]" class="benefit_amount" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="benefit_amount" data-live-search="true" data-live-search="true">
                      <option value=""></option>
                  </select>
                  <label>Benefit Amount</label>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_in_patient_benefit_div_~product_id~_~number~" style="display: none;">
                <p >InPatient Benefit</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][in_patient_benefit]" id="hidden_~enrollee_type~_~product_id~_~number~_in_patient_benefit" value="" class="hidden_~enrollee_type~_in_patient_benefit_~number~">
                  <select name="~enrollee_type~[~product_id~][~number~][in_patient_benefit]" class="in_patient_benefit" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="in_patient_benefit" data-live-search="true"  data-live-search="true">
                      <option value=""></option>
                  </select>
                  <label>In Patient Benefit</label>
                </div>
            </div>

            <div class="col-sm-6 enrollment_auto_row" id="~enrollee_type~_out_patient_benefit_div_~product_id~_~number~" style="display: none;">
                <p >OutPatient Benefit</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][out_patient_benefit]" id="hidden_~enrollee_type~_~product_id~_~number~_out_patient_benefit" value="" class="hidden_~enrollee_type~_out_patient_benefit_~number~">
                  <select name="~enrollee_type~[~product_id~][~number~][out_patient_benefit]" class="out_patient_benefit" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="out_patient_benefit" data-live-search="true"  data-live-search="true">
                      <option value=""></option>
                  </select>
                  <label>Out Patient Benefit</label>
                </div>
            </div>

            <div class="col-sm-6" id="~enrollee_type~_monthly_income_div_~product_id~_~number~" style="display: none;">
                <p >Monthly Income</p>
                <div class="form-group">
                  <input type="hidden" name="hidden_~enrollee_type~[~product_id~][~number~][monthly_income]" id="hidden_~enrollee_type~_~product_id~_~number~_monthly_income" value="" class="hidden_~enrollee_type~_monthly_income_~number~">
                  <select name="~enrollee_type~[~product_id~][~number~][monthly_income]" class="monthly_income" data-enrollee-type="~enrollee_type~" data-product-id="~product_id~" data-id="~number~" data-element="monthly_income" data-live-search="true"  data-live-search="true">
                      <option value=""></option>
                  </select>
                  <label>Monthly Income</label>
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
            </div>
          </div>
          <div class="p-15 bg_light_gray d-inline w-100">
              <span class="fw600">~Enrollee_Type~ Rate:</span>
              <div class="pull-right fs18 fw600"><span  bablic-exclude>$</span><span class="calculate_rate_price_~product_id~_~Enrollee_Type~" id="calculate_rate_price_~product_id~_~Enrollee_Type~_~number~" bablic-exclude>0.00</span> <span class="fs12">/ pay period</span></div>
          </div>            
         </div>
      </div>
   </div>
</div>

<div id="addToCartDynamicTable" style="display: none;">
  <div class="enrollee_cost_wrap cart_product_~product_id~">
    <a class="clearfix text-black collapsed" href=".cart_cost~randNumber~~product_id~" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="cart_cost~randNumber~~product_id~">
        <div class="pull-left">
          <span class="font-bold" bablic-exclude>~product_name~</span>
          <br>
          <span bablic-exclude>~plan_name~</span>
        </div>
        <div class="pull-right">
          <span class="caret" data-toggle="tooltip" data-trigger="hover" title="Expand" data-placement="bottom"></span>
        </div>
    </a>
    <div class="collapse p-t-5 cart_cost~randNumber~~product_id~" id="">
        <table border="0" cellspacing="0" cellpadding="5" width="100%">
          <tbody>
              <tr>
                <td>Member Rate</td>
                <td class="text-right font-bold" bablic-exclude>$<span class="~fromTab~totalPrice"bablic-exclude>~tmp_total_price~</span></td>
              </tr>
          </tbody>
        </table>
    </div>
  </div>
</div>

<div id="dynamicCartTotal" style="display: none;">
  <div class="plan-breakdown">
    <div class="table-responsive br-n">
      <table cellspacing="0" width="100%" border="0" class="text-center">
          <tbody>
            <tr>
                <td></td>
                <td class="font-bold text-primary">Your Cost</td>
                <td class="font-bold text-primary">Group Contribution</td>
            </tr>
            <tr>
                <td class="font-bold text-left">Plan Election(s)</td>
                <td class="~pageLocation~ProductTotal" bablic-exclude>~product_total~</td>
                <td class="text-danger" bablic-exclude>(~product_group_contribution~)</td>
            </tr>
            <tr>
                <td class="font-bold text-left">Service Fee(s)</td>
                <td class="~pageLocation~ServiceFee" bablic-exclude>~fee_total~</td>
                <td class="text-danger" bablic-exclude>(~fee_group_contribution~)</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="bg_dark_primary">
                <td class="text-left">Your Total</td>
                <td colspan="2" class="text-success text-right"><span class="~pageLocation~total_amount"bablic-exclude>~total_amount~</span> <span class="fs12">/ pay period</span></td>
            </tr>
          </tfoot>
      </table>
    </div>
  </div>
</div>