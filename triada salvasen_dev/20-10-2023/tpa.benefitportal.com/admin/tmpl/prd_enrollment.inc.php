<div class="section_space gray-bg theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="AgeRestrictions" id="matchGlobal_AgeRestrictions" <?= empty($match_globals) || in_array("AgeRestrictions",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title">Age Restrictions
      <a href="prd_history.php" data-type="Age Restrictions" class="popup_lg">
      <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
      </a>
  </h4>
  <p class="m-b-25"><em>Set age restrictions for the following enrollees.</em></p>
  <div class="row">
    <div class="col-sm-6 col-md-3" id="primary_age_div">
      <p class="fw600">Primary</p>
      <div class="radio-v">
        <label>
          <input name="is_primary_age_restrictions" <?= isset($is_primary_age_restrictions) && $is_primary_age_restrictions=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"> No
        </label>
      </div>
      <div class="radio-v">
        <label>
          <input name="is_primary_age_restrictions" <?= isset($is_primary_age_restrictions) && $is_primary_age_restrictions=="Y" ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"> Yes
        </label>
      </div>
              
      <p class="error" id="error_is_primary_age_restrictions"></p>
      <div class="form-inline m-t-20" id="primary_age_restrictions_div" style="<?= isset($is_primary_age_restrictions) && $is_primary_age_restrictions=="Y" ? '' : 'display: none' ?>">
       <div class="form-inline theme-form age_restrictions">
        <div class="form-group ">
              <select id="primary_age_restrictions_from" name="primary_age_restrictions_from" class="form-control input-sm mw-90 <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" data-live-search="true">
                <option value="0">0</option>
                <?php for($i=1;$i<=120;$i++) { ?>
                <option value="<?= $i ?>" <?= isset($primary_age_restrictions_from) && $primary_age_restrictions_from==$i ? 'selected=selected' :'' ?>><?= $i ?></option>
                <?php } ?>
              </select>
              <label>From</label>
          </div>
          <div class="form-group "> <span class="p-l-5 p-r-5">|</span> </div>
          <div class="form-group ">
            <select id="primary_age_restrictions_to" name="primary_age_restrictions_to" class="form-control input-sm mw-90 <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" data-live-search="true">
                <option value="0">0</option>
                <?php for($i=1;$i<=120;$i++) { ?>
                <option value="<?= $i ?>" <?= isset($primary_age_restrictions_to) && $primary_age_restrictions_to==$i ? 'selected=selected' :'' ?>><?= $i ?></option>
                <?php } ?>
              </select>
              <label>To</label>
          </div>
       </div> 
       <p class="error" id="error_primary_age_restrictions"></p>
      </div>
    </div>
    <div class="col-sm-6 col-md-3" id="spouse_age_div">
      <p class="fw600">Spouse</p>
      <div class="radio-v">
        <label>
          <input name="is_spouse_age_restrictions" <?= isset($is_spouse_age_restrictions) && $is_spouse_age_restrictions=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"> No
        </label>
      </div>
      <div class="radio-v">
        <label>
          <input name="is_spouse_age_restrictions" <?= isset($is_spouse_age_restrictions) && $is_spouse_age_restrictions=="Y" ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"> Yes
        </label>
      </div>
      
      
      <p class="error" id="error_is_spouse_age_restrictions"></p>
      <div class="form-inline m-t-20 age_restrictions" id="spouse_age_restrictions_div" style="<?= isset($is_spouse_age_restrictions) && $is_spouse_age_restrictions=="Y" ? '' : 'display: none' ?>">
       <div class="form-inline theme-form">
        <div class="form-group ">
              <select id="spouse_age_restrictions_from" name="spouse_age_restrictions_from" class="form-control input-sm mw-90 <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" data-live-search="true">
                <option value="0">0</option>
                <?php for($i=1;$i<=120;$i++) { ?>
                <option value="<?= $i ?>" <?= isset($spouse_age_restrictions_from) && $spouse_age_restrictions_from==$i ? 'selected=selected' :'' ?>><?= $i ?></option>
                <?php } ?>
              </select> 
              <label>From</label>
          </div>
          <div class="form-group "> <span class="p-l-5 p-r-5">|</span> </div>
          <div class="form-group ">
            <select id="spouse_age_restrictions_to" name="spouse_age_restrictions_to" class="form-control input-sm mw-90 <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" data-live-search="true">
                <option value="0">0</option>
                <?php for($i=1;$i<=120;$i++) { ?>
                <option value="<?= $i ?>" <?= isset($spouse_age_restrictions_to) && $spouse_age_restrictions_to==$i ? 'selected=selected' :'' ?>><?= $i ?></option>
                <?php } ?>
              </select>
              <label>To</label>
          </div>
       </div>  
       <p class="error" id="error_spouse_age_restrictions"></p>
      </div>
    </div>
    <div class="col-sm-6 col-md-3" id="children_age_div">
      <p class="fw600">Child</p>
      <div class="radio-v">
        <label>
          <input name="is_children_age_restrictions" <?= isset($is_children_age_restrictions) && $is_children_age_restrictions=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"> No
        </label>
      </div> 
      <div class="radio-v">
        <label>
          <input name="is_children_age_restrictions" <?= isset($is_children_age_restrictions) && $is_children_age_restrictions=="Y" ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"> Yes
        </label>
      </div>
             
      <p class="error" id="error_is_children_age_restrictions"></p>
      <div class="form-inline m-t-20" id="children_age_restrictions_div" style="<?= isset($is_children_age_restrictions) && $is_children_age_restrictions=="Y" ? '' : 'display: none' ?>">
        <div class="form-inline theme-form age_restrictions">
        <div class="form-group ">
              <select id="children_age_restrictions_from" name="children_age_restrictions_from" class="form-control input-sm mw-90 <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" data-live-search="true">
                <option value="0">0</option>
                <?php for($i=1;$i<=120;$i++) { ?>
                <option value="<?= $i ?>" <?= isset($children_age_restrictions_from) && $children_age_restrictions_from==$i ? 'selected=selected' :'' ?>><?= $i ?></option>
                <?php } ?>
              </select>
              <label>From</label>
          </div>
          <div class="form-group "> <span class="p-l-5 p-r-5">|</span> </div>
          <div class="form-group ">
            <select id="children_age_restrictions_to" name="children_age_restrictions_to" class="form-control input-sm mw-90 <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" data-live-search="true">
                <option value="0">0</option>
                <?php for($i=1;$i<=120;$i++) { ?>
                <option value="<?= $i ?>" <?= isset($children_age_restrictions_to) && $children_age_restrictions_to==$i ? 'selected=selected' :'' ?>><?= $i ?></option>
                <?php } ?>
              </select>
              <label>To</label>
          </div>
       </div> 
       <p class="error" id="error_children_age_restrictions"></p>
      </div>
    </div>
  </div>
  
  
  <div id="ageRestrictionDiv" style="<?= (
    (!empty($is_primary_age_restrictions) && $is_primary_age_restrictions == 'Y') ||  
    (!empty($is_spouse_age_restrictions) && $is_spouse_age_restrictions == 'Y') || 
    (!empty($is_children_age_restrictions) && $is_children_age_restrictions == 'Y')
    ) ? '' : 'display: none' ?>">
    <div class="m-b-25"> 
      <p><strong>Upon reaching maximum age, should any member automatically be terminated at the end of current Plan period?</strong></p>

      <div class="radio-v">
        <label><input name="maxAgeAutoTermed" type="radio" value="N" <?= !empty($maxAgeAutoTermed) && $maxAgeAutoTermed =="N" ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"/> No</label>
      </div>
      <div class="radio-v">
        <label><input name="maxAgeAutoTermed" type="radio" value="Y" <?= !empty($maxAgeAutoTermed) && $maxAgeAutoTermed =="Y" ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"/> Yes</label>
      </div>
      <p class="error" id="error_maxAgeAutoTermed"></p>
    </div>

    <div id="maxAgeAutoTermedDiv" style="<?= !empty($maxAgeAutoTermed) && $maxAgeAutoTermed =='Y' ? '' : 'display:none' ?>">
      <div class="m-b-25"> 
        <p class="fw500">Select Member</p>
        <div class="input-question">
          <div class="checkbox-inline">
            <label><input id="autoTermedMemberTypeAll" name="autoTermedMemberType[All]" type="checkbox" value="All" <?= !empty($resCheckMaxAgeTerm) && 
            array_key_exists('Primary',$resCheckMaxAgeTerm) && 
            array_key_exists('Spouse',$resCheckMaxAgeTerm) && 
            array_key_exists('Child',$resCheckMaxAgeTerm) ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"/> All</label>
          </div>
          <div class="checkbox-inline">
            <label><input class="autoTermedMemberType <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" name="autoTermedMemberType[Primary]" type="checkbox" value="Primary" <?= !empty($resCheckMaxAgeTerm) && 
            array_key_exists('Primary',$resCheckMaxAgeTerm) ? 'checked' : '' ?>/> Primary</label>
          </div>
          <div class="checkbox-inline">
            <label><input class="autoTermedMemberType <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" name="autoTermedMemberType[Spouse]" type="checkbox" value="Spouse" <?= !empty($resCheckMaxAgeTerm) && 
            array_key_exists('Spouse',$resCheckMaxAgeTerm) ? 'checked' : '' ?>/> Spouse</label>
          </div>
          <div class="checkbox-inline">
            <label><input class="autoTermedMemberType <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" name="autoTermedMemberType[Child]" type="checkbox" value="Child" <?= !empty($resCheckMaxAgeTerm) && 
            array_key_exists('Child',$resCheckMaxAgeTerm) ? 'checked' : '' ?>/> Child</label>
          </div>
        </div>  
        <p class="error" id="error_autoTermedMemberType"></p>
      </div>
      <div id="autoTermedMemberSettingMainDiv">
        <?php if(!empty($resCheckMaxAgeTerm)){ ?>
          <?php foreach ($resCheckMaxAgeTerm as $autoTermType => $termTypeArr) { ?>
            <div class="m-l-30 m-t-25 m-b-25" id="autoTermedInnerMainDiv<?= $autoTermType ?>">  
              <h5 class="h5_title"><?= $autoTermType ?></h5> 
              
              <div id="autoTermedInnerDiv<?= $autoTermType ?>" data-title="<?= $autoTermType ?>">
                <?php foreach ($termTypeArr as $innerKey => $innerRow) { ?>
                  <div class="row autoTermMemberSettingInner" id="autoTermMemberSettingInner_div_<?= $autoTermType ?>_<?= $innerRow['id'] ?>">
                      <div class="col-sm-6 col-lg-5">
                            <p>Termination Date</p>
                            <div class="form-inline age_restrictions">
                                <div class="form-group ">
                                    <select name="autoTermMemberSettingWithin[<?= $innerRow['id'] ?>][<?= $autoTermType ?>]" class="form-control add_control_<?= $autoTermType ?>_<?= $innerRow['id'] ?> <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions">
                                        <?php for($i=1;$i<=30;$i++) { ?>
                                          <option value="<?= $i ?>" <?= $innerRow['terminate_within'] == $i ? 'selected' : '' ?>><?=$i?></option>
                                        <?php } ?>
                                    </select>
                                    <p class="error" id="error_autoTermMemberSettingWithin_<?= $autoTermType ?>_<?= $innerRow['id'] ?>"></p>
                                </div>
                                <div class="form-group "><span class="p-l-5 p-r-5">|</span></div>
                                <div class="form-group ">
                                    <select name="autoTermMemberSettingWithinType[<?= $innerRow['id'] ?>][<?= $autoTermType ?>]" class="form-control add_control_<?= $autoTermType ?>_<?= $innerRow['id'] ?> <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions">
                                        <option value="Days" <?= $innerRow['terminate_within_type'] == 'Days' ? 'selected' : '' ?>>Days</option>
                                        <option value="Weeks" <?= $innerRow['terminate_within_type'] == 'Weeks' ? 'selected' : '' ?>>Weeks</option>
                                        <option value="Months" <?= $innerRow['terminate_within_type'] == 'Months' ? 'selected' : '' ?>>Months</option>
                                    </select>
                                    <p class="error" id="error_autoTermMemberSettingWithinType_<?= $autoTermType ?>_<?= $innerRow['id'] ?>"></p>
                                </div>
                                <div class="form-group "><span class="p-l-5 p-r-5">|</span></div>
                                <div class="form-group ">
                                    <select name="autoTermMemberSettingRange[<?= $innerRow['id'] ?>][<?= $autoTermType ?>]" class="form-control add_control_<?= $autoTermType ?>_<?= $innerRow['id'] ?> <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions">
                                        <option value="Before" <?= $innerRow['terminate_range'] == 'Before' ? 'selected' : '' ?>>Before</option>
                                        <option value="After" <?= $innerRow['terminate_range'] == 'After' ? 'selected' : '' ?>>After</option>
                                    </select>
                                    <p class="error" id="error_autoTermMemberSettingRange_<?= $autoTermType ?>_<?= $innerRow['id'] ?>"></p>
                                </div>
                            </div>
                      </div>
                      <div class="col-sm-6 col-lg-5 add_product_dash">
                          <p>Trigger<span class="pull-right"><a href="javascript:void(0);" class="text-action removeAutoTermMemberSettingInner <?= $record_type=="Variation" ? 'matchGlobalBtn' : '' ?>" data-match-on="AgeRestrictions" id="removeAutoTermMemberSettingInner_<?= $autoTermType ?>_<?= $innerRow['id'] ?>" data-id="<?= $innerRow['id'] ?>" data-title="<?= $autoTermType ?>"><i class="fa fa-times"></i>
                </a> </span></p>
                          <div class="form-group">
                            <select name="autoTermMemberSettingWithinTrigger[<?= $innerRow['id'] ?>][<?= $autoTermType ?>]" class="form-control add_control_<?= $autoTermType ?>_<?= $innerRow['id'] ?> <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" data-live-search="true">
                                <option value=""></option>
                                <?php if(!empty($triggerRes)) {?>
                                  <?php foreach ($triggerRes as $key => $value) { ?>
                                    <option value="<?= $value['id'] ?>" <?= $innerRow['terminate_trigger'] == $value['id'] ? 'selected' : '' ?>><?= $value['display_id'] ?></option>
                                  <?php } ?>
                                <?php } ?>
                            </select>
                            <label>Select Trigger</label>
                            <p class="error" id="error_autoTermMemberSettingWithinTrigger_<?= $autoTermType ?>_<?= $innerRow['id'] ?>"></p>
                            
                          </div>
                      </div>
                  </div>
                <?php } ?>
              </div>
              
              <div class="row">
                  <div class="col-sm-12">
                    <button data-title="<?= $autoTermType ?>" type="button" class="btn btn-primary-o addTrigger <?= $record_type=="Variation" ? 'matchGlobalBtn' : '' ?>" data-match-on="AgeRestrictions">+ Trigger</button>
                  </div>
              </div>
            </div>
            
          <?php } ?>
        <?php } ?>
      </div>
      
    </div>
    <div class="clearfix"></div>
    <div class="m-b-25"> 
      <p><strong>With a documented disability is this product allowed beyond the age of restriction?</strong></p>
      <div class="radio-v">
        <label><input name="allowedBeyoundAge" <?= !empty($allowedBeyoundAge) && $allowedBeyoundAge == 'N' ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"/> No</label>
      </div>
      <div class="radio-v">
        <label><input name="allowedBeyoundAge" <?= !empty($allowedBeyoundAge) && $allowedBeyoundAge == 'Y' ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions"/> Yes</label>
      </div> 
      <p class="error" id="error_allowedBeyoundAge"></p>
    </div>
    <div id="allowedBeyoundAgedDiv" style="<?= !empty($allowedBeyoundAge) && $allowedBeyoundAge =='Y' ? '' : 'display:none' ?>">
      <div class="mn"> 
        <p>Select Member</p>
        <div class="checkbox-inline">
          <label><input class="allowedBeyoundAge <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" name="allowedBeyoundAgeType['Spouse']" type="checkbox" value="Spouse" <?= !empty($resCheckBeyondAge) && 
          array_key_exists('Spouse',$resCheckBeyondAge) ? 'checked' : '' ?>/> Spouse</label>
        </div>
        <div class="checkbox-inline">
          <label><input class="allowedBeyoundAge <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgeRestrictions" name="allowedBeyoundAgeType['Child']" type="checkbox" value="Child" <?= !empty($resCheckBeyondAge) && 
          array_key_exists('Child',$resCheckBeyondAge) ? 'checked' : '' ?>/> Child</label>
        </div>
        <p class="error" id="error_allowedBeyoundAgeType"></p>
      </div>
    </div>
  </div>
</div>
<div class="section_space gray-bg theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="MemberEnrollmentInformation" id="matchGlobal_MemberEnrollmentInformation" <?= empty($match_globals) || in_array("MemberEnrollmentInformation",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title">Member Application Information
  	  <a href="prd_history.php" data-type="Member Enrollment Information" class="popup_lg">
  		<i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
      </a>
  </h4>
  <p><em>Preselected items cannot be deselected because they are required by the <?= $DEFAULT_SITE_NAME ?> System. Select any additional information that is required for your product.</em></p>
  <div class="row">
  	<div class="col-sm-6 col-lg-4">
      <h5 class="h5_title m-t-20"><span class="text-action">Member</span> Details</h5>
      <div class="table-responsive" id="member_detail_table_div">
        <table class="<?= isset($table_class)? $table_class:"";?> br-a enroll_info_table">
          <thead>
            <tr>
              <th width="50px" class="text-center">Asked</th>
              <th width="50px" class="text-center">Required</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($prdQuestionRes)) { ?>
              <?php foreach ($prdQuestionRes as $key => $row) { ?>
                <?php
                  $questionID=$row['id'];
                  $label=$row['label'];
                  $displayLabel=$row['display_label'];
                  $is_asked=$row['is_member_asked'];
                  $is_required=$row['is_member_required'];
                  $is_member=$row['is_member'];
                  if($is_member=='N'){
                    continue;
                  }

                ?>
                <tr>
                  <td class="text-center">
                    <label class="mn <?= !empty($is_asked) && $is_asked=='Y' ? 'check_black': 'red_checkbox' ?>">
                      <input data-que-id="<?= $questionID ?>" id="memberQuestion_<?= $questionID ?>_asked" name="memberQuestion[<?= $questionID ?>][asked]" class="member_details_asked <?= !empty($is_asked) && $is_asked=='Y' ? 'disableCheckbox' : ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="MemberEnrollmentInformation" <?=((!empty($is_asked) && $is_asked=='Y') || (isset($questionList[$questionID]) && $questionList[$questionID]['is_member_asked']=='Y')) ?'checked':''?> type="checkbox" value="<?= $questionID ?>" />
                    </label>
                  </td>
                  <td class="text-center">
                    <label class="mn <?= !empty($is_required) && $is_required=='Y' ? 'check_black': 'red_checkbox' ?>">
                      <input data-que-id="<?= $questionID ?>" id="memberQuestion_<?= $questionID ?>_required" name="memberQuestion[<?= $questionID ?>][required]" class="member_details_required <?= !empty($is_required) && $is_required=='Y' ? 'disableCheckbox' : ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="MemberEnrollmentInformation" <?=((!empty($is_required) && $is_required=='Y') || (isset($questionList[$questionID]) && $questionList[$questionID]['is_member_required']=='Y')) ?'checked':''?> type="checkbox" value="<?= $questionID ?>" />
                    </label>
                  </td>
                  <td><?= $displayLabel ?></td>
                </tr>
              <?php } ?>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="clerfix"></div>
  	  <p class="error" id="error_memberQuestion"></p>
    </div>
    <div class="col-sm-6 col-lg-4">
    	<div id="spouse_details_field_table_div">
        <h5 class="h5_title m-t-20"><span class="text-action">Spouse</span> Details </h5>
        <div class="table-responsive" id="spouse_detail_table_div">
          <table class="<?=$table_class?> spouse_detail_table enroll_info_table">
            <thead>
              <tr>
                <th width="50px" class="text-center">Asked</th>
                <th width="50px" class="text-center">Required</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($prdQuestionRes)) { ?>
                <?php foreach ($prdQuestionRes as $key => $row) { ?>
                  <?php
                    $questionID=$row['id'];
                    $label=$row['label'];
                    $displayLabel=$row['display_label'];
                    $is_asked=$row['is_spouse_asked'];
                    $is_required=$row['is_spouse_required'];
                    $is_spouse=$row['is_spouse'];
                    if($is_spouse=='N'){
                      continue;
                    }
                  ?>
                  <tr>
                    <td class="text-center">
                      <label class="mn <?= !empty($is_asked) && $is_asked=='Y' ? 'check_black': 'red_checkbox' ?>">
                        <input data-que-id="<?= $questionID ?>" id="spouseQuestion_<?= $questionID ?>_asked" name="spouseQuestion[<?= $questionID ?>][asked]" class="spouse_details_asked <?= !empty($is_asked) && $is_asked=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="MemberEnrollmentInformation" <?=((!empty($is_asked) && $is_asked=='Y') || (isset($questionList[$questionID]) && $questionList[$questionID]['is_spouse_asked']=='Y')) ?'checked':''?> type="checkbox" value="<?= $questionID ?>" />
                      </label>
                    </td>
                    <td class="text-center">
                      <label class="mn <?= !empty($is_required) && $is_required=='Y' ? 'check_black': 'red_checkbox' ?>">
                        <input data-que-id="<?= $questionID ?>" id="spouseQuestion_<?= $questionID ?>_required" name="spouseQuestion[<?= $questionID ?>][required]" class="spouse_details_required <?= !empty($is_required) && $is_required=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="MemberEnrollmentInformation" <?=((!empty($is_required) && $is_required=='Y') || (isset($questionList[$questionID]) && $questionList[$questionID]['is_spouse_required']=='Y')) ?'checked':''?> type="checkbox" value="<?= $questionID ?>" />
                      </label>
                    </td>
                    <td><?= $displayLabel ?></td>
                  </tr>
                <?php } ?>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="clerfix"></div>
        <p class="error" id="error_spouseQuestion"></p>
      </div>
    </div>
    <div class="col-sm-6 col-lg-4">
    	<div id="child_details_field_table_div">
            <h5 class="h5_title m-t-20"> <span class="text-action">Child/Dependent</span> Details</h5> 
            <div class="table-responsive" id="dependent_detail_table_div">
              <table class="<?=$table_class?> dependent_detail_table enroll_info_table">
                <thead>
                  <tr>
                    <th width="50px" class="text-center">Asked</th>
                    <th width="50px" class="text-center">Required</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($prdQuestionRes)) { ?>
                    <?php foreach ($prdQuestionRes as $key => $row) { ?>
                      <?php
                        $questionID=$row['id'];
                        $label=$row['label'];
                        $displayLabel=$row['display_label'];
                        $is_asked=$row['is_child_asked'];
                        $is_required=$row['is_child_required'];
                        $is_child=$row['is_child'];
                        if($is_child=='N'){
                          continue;
                        }
                      ?>
                      <tr>
                        <td class="text-center">
                          <label class="mn <?= !empty($is_asked) && $is_asked=='Y' ? 'check_black': 'red_checkbox' ?>">
                            <input data-que-id="<?= $questionID ?>" id="childQuestion_<?= $questionID ?>_asked" name="childQuestion[<?= $questionID ?>][asked]" class="dependent_details_asked <?= !empty($is_asked) && $is_asked=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="MemberEnrollmentInformation" <?=((!empty($is_asked) && $is_asked=='Y') || (isset($questionList[$questionID]) && $questionList[$questionID]['is_child_asked']=='Y')) ?'checked':''?> type="checkbox" value="<?= $questionID ?>" />
                          </label>
                        </td>
                        <td class="text-center">
                          <label class="mn <?= !empty($is_required) && $is_required=='Y' ? 'check_black': 'red_checkbox' ?>">
                            <input data-que-id="<?= $questionID ?>" id="childQuestion_<?= $questionID ?>_required" name="childQuestion[<?= $questionID ?>][required]" class="dependent_details_required <?= !empty($is_required) && $is_required=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="MemberEnrollmentInformation" <?=((!empty($is_required) && $is_required=='Y') || (isset($questionList[$questionID]) && $questionList[$questionID]['is_child_required']=='Y')) ?'checked':''?> type="checkbox" value="<?= $questionID ?>" />
                          </label>
                        </td>
                        <td><?= $displayLabel ?></td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div> 
            <div class="clerfix"></div>
            <p class="error" id="error_childQuestion"></p>
  		 </div>
    </div>
  </div>
  <h5 class="h5_title top_space">Custom Questions</h5>
  <div class="table-responsive">
  	<table width="100%">
    	<tr>
        	<td width="440px"  valign="top">
            	<table class="<?=$table_class?>">
                  <thead>
                  	<tr class="text-center">
                      <th class="table_light_primary text-center text-black" width="14%">Add question/answer to member agreement</th>
                      <th class="bg_white pn" width="10px"></th>
                    	<th colspan="2" class="table_light_primary text-center text-black">Member</th>
                        <th class="bg_white pn" width="10px"></th>
                        <th colspan="2" class="table_light_primary  text-center text-black">Spouse</th>
                        <th class="bg_white pn" width="10px"></th>
                        <th colspan="2" class="table_light_primary  text-center text-black">Child/Dependent</th>
                        <th colspan="3" class="bg_white"></th>
                    </tr>
                    <tr>
                      <th class="text-center" colspan="2"></th>
                    	<th class="text-center">Asked</th>
                        <th class="text-center">Required</th>
                        <th class="pn" ></th>
                        <th class="text-center">Asked</th>
                        <th class="text-center">Required</th>
                        <th class="pn" ></th>
                        <th class="text-center">Asked</th>
                        <th class="text-center">Required</th>
                        <th >Question</th>
                        <th class="text-center">Answers</th>
                        <th class="text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="customQuestionData">
                    
                  </tbody>
                </table>
            </td>
            
        </tr>    
    </table>
    <p class="error" id="error_customQuestion"></p>
  </div>
  
  <div class="m-t-25 text-center">
  <a href="javascript:void(0);"  data-id="" class="btn btn-primary add_custom_question">+ Question</a>
  </div>
</div>
<div class="section_space gray-bg theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="BeneficiaryInformation" id="matchGlobal_BeneficiaryInformation" <?= empty($match_globals) || in_array("BeneficiaryInformation",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title">Beneficiary Information
  	  <a href="prd_history.php" data-type="Beneficiary Information" class="popup_lg">
  		<i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
      </a>
  </h4>
  
  <div class="m-b-25">
  	<p><strong>Is beneficiary information required for this product?</strong></p>
    <div class="radio-v">
      <label><input name="is_beneficiary_required" <?= !empty($is_beneficiary_required) && $is_beneficiary_required=='N' ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="BeneficiaryInformation"/> No</label>
    </div>
    <div class="radio-v">
    	<label><input name="is_beneficiary_required" <?= !empty($is_beneficiary_required) && $is_beneficiary_required=='Y' ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="BeneficiaryInformation"/> Yes</label>
    </div>
    <p class="error" id="error_is_beneficiary_required"></p>
    
  </div>
  <div class="row" id="beneficiary_details_div" style="<?= !empty($is_beneficiary_required) && $is_beneficiary_required=='Y' ? '' : 'display:none' ?>">
  	<div class="col-sm-6 col-lg-5">
    	<h5 class="h5_title"><span class="text-action">Principal Beneficiary </span> Details</h5>
        <div class="table-responsive">
         	<table class="<?= isset($table_class)? $table_class:"";?> br-a">
            	<thead>
                	<tr>
                    <th class="text-center" width="50px">Asked</th>
                    <th class="text-center" width="50px">Required</th>
                    <th></th>                    
                    </tr>
                </thead>	
                <tbody>
                  <?php if(!empty($prdBeneficiaryQuestionRes)) { ?>
                    <?php foreach ($prdBeneficiaryQuestionRes as $key => $row) { ?>
                      <?php
                        $questionID=$row['id'];
                        $label=$row['label'];
                        $displayLabel=$row['display_label'];
                        $is_asked=$row['is_principal_beneficiary_asked'];
                        $is_required=$row['is_principal_beneficiary_required'];
                        $is_principal_beneficiary=$row['is_principal_beneficiary'];
                        if($is_principal_beneficiary=='N'){
                          continue;
                        }

                      ?>
                      <tr>
                        <td class="text-center">
                          <label class="mn <?= !empty($is_asked) && $is_asked=='Y' ? 'check_black': 'red_checkbox' ?>">
                            <input name="principalBeneficiary[<?= $questionID ?>][asked]" class="principal_beneficiary_asked <?= !empty($is_asked) && $is_asked=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="BeneficiaryInformation" <?=((!empty($is_asked) && $is_asked=='Y') || (!empty($beneficiaryQuestionList[$questionID]) && $beneficiaryQuestionList[$questionID]['is_principal_beneficiary_asked']=='Y')) ?'checked':''?> type="checkbox" value="<?= $label ?>" />
                          </label>
                        </td>
                        <td class="text-center">
                          <label class="mn <?= !empty($is_required) && $is_required=='Y' ? 'check_black': 'red_checkbox' ?>">
                            <input name="principalBeneficiary[<?= $questionID ?>][required]" class="principal_beneficiary_required <?= !empty($is_required) && $is_required=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="BeneficiaryInformation" <?=((!empty($is_required) && $is_required=='Y') || (!empty($beneficiaryQuestionList[$questionID]) && $beneficiaryQuestionList[$questionID]['is_principal_beneficiary_required']=='Y')) ?'checked':''?> type="checkbox" value="<?= $label ?>" />
                          </label>
                        </td>
                        <td><?= $displayLabel ?></td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
          </table>
        </div>
        <div class="clerfix"></div>
        <p class="error" id="error_principalBeneficiary"></p>
    </div>
    <div class="col-sm-6 col-lg-5">
    	<h5 class="h5_title"><span class="text-action">Contingent Beneficiary</span> Details</h5>
        <div class="table-responsive">
         	<table class="<?= isset($table_class)? $table_class:"";?> br-a">
            	<thead>
                	<tr>
                    <th class="text-center" width="50px">Asked</th>
                    <th class="text-center" width="50px">Required</th>
                    <th></th>                    
                    </tr>
                </thead>	
                <tbody>
                  <?php if(!empty($prdBeneficiaryQuestionRes)) { ?>
                    <?php foreach ($prdBeneficiaryQuestionRes as $key => $row) { ?>
                      <?php
                        $questionID=$row['id'];
                        $label=$row['label'];
                        $displayLabel=$row['display_label'];
                        $is_asked=$row['is_contingent_beneficiary_asked'];
                        $is_required=$row['is_contingent_beneficiary_required'];
                        $is_contingent_beneficiary=$row['is_contingent_beneficiary'];
                        if($is_contingent_beneficiary=='N'){
                          continue;
                        }

                      ?>
                      <tr>
                        <td class="text-center">
                          <label class="mn <?= !empty($is_asked) && $is_asked=='Y' ? 'check_black': 'red_checkbox' ?>">
                            <input name="contingentBeneficiary[<?= $questionID ?>][asked]" class="contingent_beneficiary_asked <?= !empty($is_asked) && $is_asked=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="BeneficiaryInformation" <?=((!empty($is_asked) && $is_asked=='Y') || (!empty($beneficiaryQuestionList[$questionID]) && $beneficiaryQuestionList[$questionID]['is_contingent_beneficiary_asked']=='Y')) ?'checked':''?> type="checkbox" value="<?= $label ?>" />
                          </label>
                        </td>
                        <td class="text-center">
                          <label class="mn <?= !empty($is_required) && $is_required=='Y' ? 'check_black': 'red_checkbox' ?>">
                            <input name="contingentBeneficiary[<?= $questionID ?>][required]" class="contingent_beneficiary_required <?= !empty($is_required) && $is_required=='Y' ? 'disableCheckbox': ($record_type=="Variation" ? 'matchGlobal' : '') ?>" data-match-on="BeneficiaryInformation" <?=((!empty($is_required) && $is_required=='Y') || (!empty($beneficiaryQuestionList[$questionID]) && $beneficiaryQuestionList[$questionID]['is_contingent_beneficiary_required']=='Y')) ?'checked':''?>  type="checkbox" value="<?= $label ?>" />
                          </label>
                        </td>
                        <td><?= $displayLabel ?></td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
          </table>
        </div>
        <div class="clerfix"></div>
        <p class="error" id="error_contingentBeneficiary"></p>
    </div>
  </div>
</div>  
<div class="section_space gray-bg theme-form"> 
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="EnrollmentVerification" id="matchGlobal_EnrollmentVerification" <?= empty($match_globals) || in_array("EnrollmentVerification",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title mn">Application Verification  <a href="prd_history.php" data-type="Application Verification" class="popup_lg">
  		<i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
      </a></h4>
  <p class="m-b-20"><em>Select how a member will be varified.</em></p>
  <div class="m-b-25">
    <div class="clearfix"></div>
    <div class="checkbox-v">
      <label><input name="enrollment_verification[]" id="eSignVerification" class="enrollment_verification <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EnrollmentVerification" <?=!empty($enrollment_verification) && ((in_array('eSign',$enrollment_verification)))?'checked':''?> type="checkbox" value="eSign"> eSign</label>
    </div>
    <div class="checkbox-v" id="voiceVerificationDIV" style="<?= isset($product_type) && $product_type=="Group Enrollment" ? 'display: none' : '' ?>">
      <label><input name="enrollment_verification[]" id="voiceVerification" class="enrollment_verification <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EnrollmentVerification" <?=!empty($enrollment_verification) && ((in_array('voice_verification',$enrollment_verification)))?'checked':''?> type="checkbox" value="voice_verification"> Voice Verification</label>
    </div>
    <div class="checkbox-v">
      <label><input name="enrollment_verification[]" id="emailSMSVerification" class="enrollment_verification <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EnrollmentVerification" <?=!empty($enrollment_verification) && ((in_array('email_sms_verification',$enrollment_verification)))?'checked':''?> type="checkbox" value="email_sms_verification"> Email/SMS Verification</label>
    </div>
    <p class="error" id="error_enrollment_verification"></p>
  </div>
  
  <div class="m-b-25">
    <div class="checkbox-v">
      <label class="label-input"><input name="joinder_agreement_require" id="joinderAgreementRequire" class="joinder_agreement_require <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EnrollmentVerification" <?=checkisset($joinder_agreement_require) == 'Y' ?'checked':''?> type="checkbox" value="Y"> Does this product require a separate Joinder Agreement?</label>
    </div>
  </div>
    
  
  <div class="clearfix "></div>
  
  <h5 class="h5_title">Terms & Conditions</h5>
  <p class=" m-b-20"><em>The system displays a required agreement above the area to input a digital signature. Additional Terms & Conditions can be added to the end of the existing agreement.</em></p>
  <div class="clearfix">
    <div id="termsConditionDiv" >
      <div id="termsConditionInputDiv">
            <div class="m-b-25">
              <textarea name="termsConditionData" id="termsConditionData"  class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EnrollmentVerification"><?= !empty($resCheckTerms) ? $resCheckTerms['terms_condition'] : ''?></textarea>
            </div>
            <div class="clearfix text-right">
               <a data-href="product_smart_tag_popup.php" class="btn btn-action-o smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
            </div>
        </div>
      <div class="clerfix"></div>
      <p class="error" id="error_termsCondition"></p>
    </div>
  </div>

  <div class="clearfix "></div>
  
<div id="joinderAgreementDiv" style="display: <?=checkIsset($joinder_agreement_require) == 'Y' ? 'block' : 'none';?>;">
  <h5 class="h5_title">Joinder Agreement</h5>
  <p class=" m-b-20"><em>The system will create a separate PDF document with the information that is entered into the system upon enrolment.</em></p>
  <div class="clearfix">
    <div id="joinderAgreementInputDiv">
      <div class="m-b-25">
        <textarea name="joinder_agreement" id="joinderAgreementData" class="summernoteClass <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EnrollmentVerification"><?= !empty($resJoinderAgreement) ? $resJoinderAgreement['joinder_agreement'] : ''?></textarea>
        <p class="error" id="error_joinder_agreement"></p>
      </div>
      <div class="clearfix text-right">
        <a data-href="product_smart_tag_popup.php" class="btn btn-action-o smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
      </div>
    </div>
    <div class="clerfix"></div>

  </div>
</div>

  <div class="clerfix"></div>
</div>
<div class="section_space gray-bg theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="AgentRequirements" id="matchGlobal_AgentRequirements" <?= empty($match_globals) || in_array("AgentRequirements",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <div id="AgentRequirementsDiv" style="<?= isset($product_type) && $product_type=="Group Enrollment" ? 'display: none' : '' ?>">
    <h4 class="h4_title ">Agent Requirements <a href="prd_history.php" data-type="Agent Requirements" class="popup_lg">
    		<i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i>
        </a></h4>
    <p class="m-b-30"><em>Fill out agent requirements below.</em></p>
    <h5 class="h5_title">Licenses </h5>
    <div class="m-b-25">
      <div class="radio-v">
        <label><input name="is_license_require"  <?= isset($is_license_require) && $is_license_require=="N" ? 'checked' : '' ?>  type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"> Not Required</label>
      </div>
      <div class="radio-v">
        <label><input name="is_license_require"  <?= isset($is_license_require) && $is_license_require=="Y" ? 'checked' : '' ?>  type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"> Required</label>
      </div>
      <p class="error" id="error_is_license_require"></p>
    </div>
    <div id="license_require_div" style="<?= isset($is_license_require) && $is_license_require=="Y" ? '' : 'display: none' ?>">
      <div class="row theme-form p-b-10">
         <div class="col-sm-4">   
            <div class="form-group">
                <select name="license_type[]" id="license_type" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements" multiple="multiple" >
                    <option value="Health" <?= !empty($license_type) && in_array("Health", $license_type) ? 'selected' : '' ?>>Health</option>
                    <option value="Life" <?= !empty($license_type) && in_array("Life", $license_type) ? 'selected' : '' ?>>Life</option>
                </select>
                <label>License Type</label>
                <p class="error" id="error_license_type"></p>
            </div>
         </div>
      </div>

      <h5 class="h5_title">License Rules</h5>
      <div class="m-b-25">
        <div class="radio-v">
          <label><input name="license_rule" <?= isset($license_rule) && $license_rule=="Licensed Only" ? 'checked' : '' ?> type="radio" value="Licensed Only" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"/> Licensed Only</label>
        </div>
        <div class="radio-v">
          <label><input name="license_rule" <?= isset($license_rule) && $license_rule=="Licensed in Sale State" ? 'checked' : '' ?> type="radio" value="Licensed in Sale State" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"/> Licensed in Sale State</label>
        </div>
        <div class="radio-v">
          <label><input name="license_rule" <?= isset($license_rule) && $license_rule=="Licensed in Specific States Only" ? 'checked' : '' ?> type="radio" value="Licensed in Specific States Only" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"/> Licensed in Specific States Only</label>
        </div>
        <div class="radio-v">
          <label><input name="license_rule" <?= isset($license_rule) && $license_rule=="Licensed and Appointed" ? 'checked' : '' ?> type="radio" value="Licensed and Appointed" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"/> Licensed and Appointed</label>
        </div>
        <p class="error" id="error_license_rule"></p>
        <div class="clearfix"></div>
      </div>

      <div id="specific_license_div" style="<?= isset($license_rule) && $license_rule=="Licensed in Specific States Only" ? '' : 'display: none' ?>">
        <div id="specificStateDiv">
          <p><strong>Available State</strong></p>
          <p>Below are the available states for application. Select state(s) in which the agent must hold at least one license in order to sell this product in all available states.</p>
          <div class="row m-b-15">
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
              <div class="table-responsive">
                <table class="<?=$table_class?> br-a">
                  <thead>
                    <tr>
                      <th width="20px" >States</th>
                      <th ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(!empty($allStateRes)){
                      foreach ($allStateRes as $key => $state) {
                        if(in_array($state['name'],$specificState)){
                          array_push($specificStateCheckAll, 'Check');
                        }else{
                          array_push($specificStateCheckAll, 'unCheck');
                        }
                      }
                    } ?>
                    <tr>
                      <td width="20px">
                        <label class="red_checkbox"><input name="specificStateCheckAll" id="specificStateCheckAll" type="checkbox" value="specificStateCheckAll" <?=in_array('unCheck',$specificStateCheckAll)?'':'checked'?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"/>
                        </label>
                      </td>
                      <td >Select All States</td>
                    </tr>
                    <?php $stateRowCount=1; ?>
                    <?php if(!empty($allStateRes)){ ?>
                    <?php foreach ($allStateRes as $key => $state) { ?>
                    <?php if($stateRowCount!=1 && $stateRowCount%13 == 0){?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
              <div class="table-responsive">
                <table class="<?=$table_class?> br-a">
                  <thead>
                    <tr>
                      <th width="20px" >States</th>
                      <th ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php } ?>
                    <tr>
                      <td width="20px" >
                        <div class="list_label" style="width:100%">
                          <label class="red_checkbox"><input name="specificState[]" class="specificState <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements" <?=!empty($licenseStateArray['Specific']) && in_array($state['name'],$licenseStateArray['Specific'])?'checked':''?> type="checkbox" value="<?= $state['name'] ?>" data-state-id="<?= $state['id'] ?>" data-short-name="<?= $state['short_name'] ?>" data-name="<?= $state['name'] ?>"></label>
                        </div>
                      </td>
                      <td><?= $state['short_name']. ', '.$state['name'] ?> </td>
                    </tr>
                    <?php $stateRowCount++;} ?>
                    <?php } ?>
                    
                  </tbody>
                </table>
              </div>
            </div>
            <div class="clearfix"></div>
            <p class="error" id="error_specificState"></p>
          </div>
        </div>
      </div>

      <div id="appointed_license_div" style="<?= isset($license_rule) && $license_rule=="Licensed and Appointed" ? '' : 'display: none' ?>">
        <div id="preSaleAppointmentDiv">
          <p><strong>Pre-Sale Appointment</strong></p>
          <p>Below are the available states for application. Select the state(s) in which the agent must be appointed in prior to selling this product.</p>
          <div class="row m-b-15">
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
              <div class="table-responsive">
                <table class="<?=$table_class?> br-a">
                  <thead>
                    <tr>
                      <th width="20px" >States</th>
                      <th ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(!empty($allStateRes)){
                      foreach ($allStateRes as $key => $state) {
                        if(in_array($state['name'],$preSaleState)){
                          array_push($preSaleCheckAll, 'Check');
                        }else{
                          array_push($preSaleCheckAll, 'unCheck');
                        }
                      }
                    } ?>
                    <tr>
                      <td width="20px">
                        <label class="red_checkbox"><input name="preSaleCheckAll" id="preSaleCheckAll" type="checkbox" value="preSaleCheckAll" <?=in_array('unCheck',$preSaleCheckAll)?'':'checked'?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"/>
                        </label>
                      </td>
                      <td >Select All States</td>
                    </tr>
                    <?php $stateRowCount=1; ?>
                    <?php if(!empty($allStateRes)){ ?>
                    <?php foreach ($allStateRes as $key => $state) { ?>
                    <?php if($stateRowCount!=1 && $stateRowCount%13 == 0){?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
              <div class="table-responsive">
                <table class="<?=$table_class?> br-a">
                  <thead>
                    <tr>
                      <th width="20px" >States</th>
                      <th ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php } ?>
                    <tr>
                      <td width="20px" >
                        <div class="list_label" style="width:100%">
                          <label class="red_checkbox"><input name="preSaleState[]" id="preSaleState_<?= $state['name'] ?>" class="preSaleState <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements" <?=!empty($licenseStateArray['Pre-Sale']) && in_array($state['name'],$licenseStateArray['Pre-Sale'])?'checked':''?> type="checkbox" value="<?= $state['name'] ?>" data-state-id="<?= $state['id'] ?>" data-short-name="<?= $state['short_name'] ?>" data-name="<?= $state['name'] ?>"></label>
                        </div>
                      </td>
                      <td><?= $state['short_name']. ', '.$state['name'] ?> </td>
                    </tr>
                    <?php $stateRowCount++;} ?>
                    <?php } ?>
                    
                  </tbody>
                </table>
              </div>
            </div>
            <div class="clearfix"></div>
            <p class="error" id="error_preSaleState"></p>
          </div>
        </div>
        <div id="justInTimeAppointmentDiv" class="m-t-25">
          <p><strong>Just-in-Time Appointment</strong></p>
          <p>Below are the available states for application. Select the state(s) in which the agent may be appointed after their initial sale occurs.</p>
          <div class="row m-b-15">
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
              <div class="table-responsive">
                <table class="<?=$table_class?> br-a">
                  <thead>
                    <tr>
                      <th width="20px" >States</th>
                      <th ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(!empty($allStateRes)){
                      foreach ($allStateRes as $key => $state) {
                        if(in_array($state['name'],$justInTimeSaleState)){
                          array_push($justInTimeSaleCheckAll, 'Check');
                        }else{
                          array_push($justInTimeSaleCheckAll, 'unCheck');
                        }
                      }
                    } ?>
                    <tr>
                      <td width="20px">
                        <label class="red_checkbox"><input name="justInTimeSaleCheckAll" id="justInTimeSaleCheckAll" type="checkbox" value="justInTimeSaleCheckAll" <?=in_array('unCheck',$justInTimeSaleCheckAll)?'':'checked'?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements"/>
                        </label>
                      </td>
                      <td >Select All States</td>
                    </tr>
                    <?php $stateRowCount=1; ?>
                    <?php if(!empty($allStateRes)){ ?>
                    <?php foreach ($allStateRes as $key => $state) { ?>
                    <?php if($stateRowCount!=1 && $stateRowCount%13 == 0){?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
              <div class="table-responsive">
                <table class="<?=$table_class?> br-a">
                  <thead>
                    <tr>
                      <th width="20px" >States</th>
                      <th ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php } ?>
                    <tr>
                      <td width="20px" >
                        <div class="list_label" style="width:100%">
                          <label class="red_checkbox"><input name="justInTimeSaleState[]" id="justInTimeSaleState_<?= $state['name'] ?>" class="justInTimeSaleState <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="AgentRequirements" <?=!empty($licenseStateArray['Just-In-Time']) && in_array($state['name'],$licenseStateArray['Just-In-Time'])?'checked':''?> type="checkbox" value="<?= $state['name'] ?>" data-state-id="<?= $state['id'] ?>" data-short-name="<?= $state['short_name'] ?>" data-name="<?= $state['name'] ?>"></label>
                        </div>
                      </td>
                      <td><?= $state['short_name']. ', '.$state['name'] ?> </td>
                    </tr>
                    <?php $stateRowCount++;} ?>
                    <?php } ?>
                    
                  </tbody>
                </table>
              </div>
            </div>
            <div class="clearfix"></div>
            <p class="error" id="error_justInTimeSaleState"></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="section_space text-right step_btn_wrap">
 <!--  <a href="javascript:void(0);" class="btn btn-primary pull-left"> Summary</a> -->
  <a href="javascript:void(0);" class="btn btn-action  btn_next"> Next</a>
  <a href="javascript:void(0);" class="btn btn-action-o  btn_save_exit"> Save & Exit</a>
  <a href="javascript:void(0);" class="red-link btn_cancel"> Cancel</a>
</div>
<script type="text/javascript">
$(function() {
  $('.enroll_info_table').matchHeight({ property: 'min-height' });
});
$(document).ready(function(){
  $(".smart_tag_popup").on('click',function(){
    $href = $(this).attr('data-href');
    var not_win = window.open($href, "myWindow", "width=768,height=600");
    if(not_win.closed) {  
      alert('closed');  
    } 
  });
});

CKEDITOR.replace( 'termsConditionData', {
 toolbar: [
      ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Link', 'Unlink', 'Format','Font', 'FontSize','TextColor', 'BGColor','Source','-','Maximize','Image', 'Form', '-', 'Checkbox' ],
    ],
   height : 350,
});
</script>