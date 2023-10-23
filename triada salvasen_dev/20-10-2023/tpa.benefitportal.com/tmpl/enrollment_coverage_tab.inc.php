<?php if ($enrollmentLocation == "groupSide" && !$from_group_side && empty($customer_id)) { ?>
<div id="group_enroll_enrollee_inport_div">
   <div  style="<?= !empty($token) ? 'display: none' : '' ?>">
      <div class="theme-form">
        <h4 class="m-b-20 m-t-0  fs20">Get Started</h4>
        <div class="row">
          <div class="col-sm-4 col-sm-offset-4">
            <p class="m-b-5 fw500">Select Existing Enrollee</p>
            <div class="phone-control-wrap">
                  <div class="phone-addon text-left">
                    <div class="form-group">
                      <input name="user" type="text" style="display:none"/>
                      <input name="pass" type="password" autocomplete="new-password" style="display:none"/>
                      

                    <?php if(isset($display_add_enrollee)){  ?>
                      <input type="hidden" name="check_open_enrollment" value="true">
                      <select name="select_enrollee_id" id="select_enrollee_id" class="form-control" data-live-search="true">
                        <option value=""></option>
                        <?php if(!empty($selLeadRows)) { ?>
                          <?php foreach ($selLeadRows as $leadKey => $leadValue) { ?>
                            <option value="<?= $leadValue['id'] ?>" <?=!empty($lead_id) && $lead_id == $leadValue['id']?"selected='selected'":""?> data-fname="<?= $leadValue['fname'] ?>" data-lname="<?= $leadValue['lname'] ?>"  data-email="<?= $leadValue['email'] ?>" data-hire_date="<?= !empty($leadValue['hire_date']) ? date('m/d/Y',strtotime($leadValue['hire_date'])) : '' ?>" data-birth_date="<?= !empty($leadValue['birth_date']) ? date('m/d/Y',strtotime($leadValue['birth_date'])) : '' ?>" data-gender="<?= !empty($leadValue['gender']) ? $leadValue['gender'] : '' ?>" data-zip="<?= !empty($leadValue['zip']) ? $leadValue['zip'] : '' ?>" data-employee_type="<?= !empty($leadValue['employee_type']) ? $leadValue['employee_type'] : '' ?>"
                            data-group_coverage_id="<?= !empty($leadValue['group_coverage_id']) ? $leadValue['group_coverage_id'] : '' ?>"
                            data-group_classes_id="<?= !empty($leadValue['group_classes_id']) ? $leadValue['group_classes_id'] : '' ?>" 
                            data-group_company_id="<?= !empty($leadValue['group_company_id']) ? $leadValue['group_company_id'] : '' ?>" data-enrolleeid="<?= $leadValue['employee_id'] ?>"
                              > <?= $leadValue['employee_id'].' - '. $leadValue['fname'].' '.$leadValue['lname'] ?> </option>
                          <?php } ?>
                        <?php } ?>
                      </select> 
                    <?php }else{?>
                      <input type="text" class="form-control" name="select_enrollee_id" id="select_enrollee_id">
                    <?php } ?>
                      <p class="error" id="error_select_enrollee_id"></p>
                    </div>
                  </div>
                  <div class="phone-addon w-80 v-align-top">
                    <div class="form-group">
                      <a href="javascript:void(0)" id="populate_get_started"  class="btn btn-action-o btn-block" data-type="<?=isset($display_add_enrollee) ? 'groupEnroll' : 'quoteEnroll'?>">Import</a>
                    </div>
                  </div>
            </div>
            <div class="text-right" style="<?= isset($display_add_enrollee) ? '' : 'display: none' ?>">
              <a href="<?=$HOST?>/group_enroll/<?=$user_name?>" target="_blank" <?php /*id="add_new_enrollee"*/ ?> class="btn red-link pn">Add New Member</a>
            </div> 
          </div>
        </div>   
      </div>
   </div>
</div>
<?php } ?>
<div id="enroll_get_started_div" style="<?=$enrollmentLocation == "groupSide" && !$from_group_side  && empty($customer_id) ? 'display: none' : '' ?>">
  <div class="zip_sections" style="<?= !empty($token) ? 'display: none' : '' ?>">
    <h4 class="m-b-20 m-t-0">Get Started</h4>
    <?php if ($enrollmentLocation == "groupSide" || $is_group_member == 'Y') { ?>
      <div style="">
      <p><strong>Enrollee Information</strong></p>
      <div class="theme-form">
        <div class="row enrollment_auto_row ">
          <div class="col-lg-12">
            <div class="row">
              <div class="col-lg-12 col-md-12">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <select id="coverage_period" name="coverage_period" class="tblur form-control populate_product_rule">
                        <option value=""></option>
                        <?php if(isset($resGroupCoveragePeriod) && $resGroupCoveragePeriod){
                              foreach ($resGroupCoveragePeriod as $key => $value) { ?>
                                <option value="<?php echo $value['id']; ?>" <?= !empty($coverage_period) && $coverage_period == $value['id'] ? 'selected' : '' ?>><?php echo $value['coverage_period_name']; ?></option>
                             <?php  } 
                           } ?>
                      </select>
                      <label>Plan Period*</label>
                      <span class="error" id="error_coverage_period"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <input type="hidden" name="hdn_enrolle_class" id="hdn_enrolle_class" value="<?= !empty($enrolle_class) ? $enrolle_class : '' ?>">
                      <select id="enrolle_class" name="enrolle_class" class="tblur form-control populate_product_rule">
                        <option value=""></option>
                        <?php if(isset($resGroupClass) && $resGroupClass){
                              foreach ($resGroupClass as $key => $value) { ?>
                                <option value="<?php echo $value['id']; ?>" <?= !empty($enrolle_class) && $enrolle_class == $value['id'] ? 'selected' : '' ?>><?php echo $value['class_name']; ?></option>
                             <?php  } 
                           } ?>
                      </select>
                      <label>Enrollee Class*</label>
                      <span class="error" id="error_enrolle_class"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <input type="hidden" name="hdn_relationship_to_group" id="hdn_relationship_to_group" value="<?= !empty($relationship_to_group) ? $relationship_to_group : '' ?>">
                      <select id="relationship_to_group" name="relationship_to_group" class="tblur form-control populate_product_rule">
                        <option value=""></option>
                        <option value="Existing" <?= !empty($relationship_to_group) && $relationship_to_group == 'Existing' ? 'selected' : '' ?>>Existing</option>
                        <option value="New" <?= !empty($relationship_to_group) && $relationship_to_group == 'New' ? 'selected' : '' ?>>New</option>
                        <option value="Renew" <?= !empty($relationship_to_group) && $relationship_to_group == 'Renew' ? 'selected' : '' ?>>Renew</option>
                      </select>
                      <label>Relationship to Group*</label>
                      <span class="error" id="error_relationship_to_group"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-addon datePickerIcon" data-applyon="relationship_date"><i class="material-icons fs16">date_range</i></div>
                        <div class="pr">
                          <input type="text" class="form-control dateClass populate_product_rule" name="relationship_date" id="relationship_date" value="<?= !empty($relationship_date) ? $relationship_date : ""; ?>">
                          <label>Relationship Date (MM/DD/YYYY)*</label>
                        </div>
                      </div>
                      <p class="error" id="error_relationship_date"></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    <?php } ?>
    <p><strong>Primary</strong></p>
    <div class="theme-form">
      <div class="row enrollment_auto_row ">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-7 col-md-8">
              <div class="row">
                <div class="col-sm-4 ">
                  <div class="form-group">
                    <input type="text" id="primary_fname" autocomplete="false" name="primary_fname" value="<?= !empty($primary_fname) ? $primary_fname : ""; ?>" class="form-control populate_product_rule <?= !empty($primary_fname) ? "has-value" : "" ?> primary_fname_1">
                    <label>First Name</label>
                    <p class="error" id="error_primary_fname1"></p>
                  </div>
                </div>

                <div class="col-sm-4  text-center">
                  <div class="form-group">
                    <div class="btn-group colors btn-group-justified" data-toggle="buttons">
                      <label class="btn btn-info <?= (!empty($primary_gender) && $primary_gender == 'Male' ? 'active' : '') ?>">
                        <input type="radio" name="primary_gender" id="primary_gender_male" value="Male" class="js-switch populate_product_rule primary_gender_1" autocomplete="false" <?= (!empty($primary_gender) && $primary_gender == 'Male' ? 'checked' : '') ?>> Male
                      </label>
                      <label class="btn btn-info <?= (!empty($primary_gender) && $primary_gender == 'Female' ? 'active' : '') ?>">
                        <input type="radio" name="primary_gender"id="primary_gender_female"  value="Female" class="js-switch populate_product_rule primary_gender_1" autocomplete="false" <?= (!empty($primary_gender) && $primary_gender == 'Female' ? 'checked' : '') ?>> Female
                      </label>
                    </div>
                    <p class="error" id="error_primary_gender"></p>
                  </div>
                </div>
                <div class="col-sm-4 ">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon datePickerIcon" data-applyon="primary_birthdate"><i class="material-icons fs16">date_range</i></div>
                      <div class="pr">
                        <input type="text" class="form-control dateClass populate_product_rule primary_birthdate_1" name="primary_birthdate" id="primary_birthdate" value="<?= !empty($primary_birthdate) ? $primary_birthdate : ""; ?>">
                        <label>DOB (MM/DD/YYYY)*</label>
                      </div>
                    </div>
                    <p class="error" id="error_primary_birthdate"></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5 col-md-4">
              <div class="row">
                <div class="col-sm-6 ">
                  <div class="form-group">
                    <input type="text" id="primary_zip" autocomplete="false" name="primary_zip" value="<?= !empty($primary_zip) ? $primary_zip : ""; ?>" class="form-control populate_product_rule <?= !empty($primary_zip) ? "has-value" : "" ?> primary_zip_1">
                    <label>Zip Code*</label>
                    <p class="error" id="error_primary_zip"></p>
                  </div>
                </div>
                <div class="col-sm-6 ">
                  <div class="form-group">
                    <input type="text" id="primary_email" autocomplete="false" name="primary_email" value="<?= !empty($primary_email) ? $primary_email : ""; ?>" class="form-control no_space populate_product_rule <?= !empty($primary_email) ? "has-value" : "" ?>">
                    <label>Email*</label>
                    <p class="error" id="error_primary_email"></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <p><strong>Plan Options <i class="text-light-gray">(Optional)</i></strong></p>
      <p>Would this enrollee wish to consider their spouse and/or child in plan?</p>
      <div class="btn-group">
        <label class="btn btn-info btn-outline" id="addSpouseCoverage">+ Spouse</label>
        <label class="btn btn-info btn-outline" class="btn btn-info btn-outline" id="addChildCoverage">+ Child</label>
      </div>
      <div class="clearfix"></div>
      <hr class="m-b-10" />
      <div id="spouseCoverageMainDiv"></div>

      <div id="childCoverageMainDiv"></div>
      <div class="" id="addChildCoverageButtonDiv" style="display: none">
        <div class="row">
          <div class="col-sm-7 text-right m-b-20">
            <a href="javascript:void(0)" class="red-link fw500" id="addChildCoverageButton"> + Child</a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="bottom_btn_wrap text-right ">
    <!-- <span class="tooltip_prd" id="populate_products_tooltip" style="display:inline-block;" data-toggle="tooltip" data-title="Cannot populate available products untill the zip code, date of birth and email fields are valid."> -->
      <a href="javascript:void(0)" id="populate_products" class="btn btn-action">Continue</a>
    <!-- </span> -->
    <a href="javascript:void(0)" class="btn red-link cancel_enrollment">Cancel</a>
  </div>
</div>