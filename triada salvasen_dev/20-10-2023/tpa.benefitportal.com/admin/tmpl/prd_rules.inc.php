<div class="section_space gray-bg theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="EffecttiveDate" id="matchGlobal_EffecttiveDate" <?= empty($match_globals) || in_array("EffecttiveDate",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title">Effective Date
  <a href="prd_history.php" data-type="Effective Date" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
  </h4>
  
  <p class="m-b-20"><em>Choose the effective date option for this product. </em></p>
  <div class="row m-t-20 ">
    <div class="col-sm-4">
      <div class="form-group">
        <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EffecttiveDate" name="direct_product" id="direct_product" >
          <option value="" hidden selected="selected"></option>
          <option value="Next Day" <?= !empty($direct_product) && $direct_product=="Next Day" ? 'selected' : '' ?>>Next Day</option>
          <option value="First Of Month" <?= !empty($direct_product) && $direct_product=="First Of Month" ? 'selected' : '' ?>>First of Following Month</option>
          <option value="Select Day Of Month" <?= !empty($direct_product) && $direct_product=="Select Day Of Month" ? 'selected' : '' ?>>Select Day Of Month</option>
          
        </select>
        <label>Direct Plan </label>
        <p class="error" id="error_direct_product"></p>
      </div>
    </div>
    <div class="clearfix"></div>
    <div id="day_of_month_div" style="<?= !empty($direct_product) &&  $direct_product=="Select Day Of Month" ? '' : 'display: none' ?>">
      
      <div class="col-sm-4">
        <div class="form-group">
          <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EffecttiveDate" name="effective_day" id="effective_day">
            <option value="" hidden selected="selected"></option>
            <?php for($i=1;$i<=28;$i++){?>
            <option value="<?= $i ?>" <?= !empty($effective_day) && $effective_day==$i ? 'selected' : '' ?>><?= $functionsList->addOrdinalNumberSuffix($i) ?> of the month</option>
            <?php } ?>
            <option value="LastDayOfMonth" <?= !empty($effective_day) && $effective_day=="LastDayOfMonth" ? 'selected' : '' ?>>Last day of the month </option>
          </select>
          <label>Select Effective Day </label>
          <p class="error" id="error_effective_day"></p>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-sm-4">
        <div class="form-group">
          <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EffecttiveDate" name="effective_day2" id="effective_day2">
            <option value="" selected="selected"></option>
            <?php for($i=1;$i<=28;$i++){?>
            <option value="<?= $i ?>" <?= !empty($effective_day2) && $effective_day2==$i ? 'selected' : '' ?>><?= $functionsList->addOrdinalNumberSuffix($i) ?> of the month</option>
            <?php } ?>
            <option value="LastDayOfMonth" <?= !empty($effective_day2) && $effective_day2=="LastDayOfMonth" ? 'selected' : '' ?>>Last day of the month </option>
          </select>
          <label>Select Effective Day </label>
          <p class="error" id="error_effective_day2"></p>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div id="day_of_sold_month_div" style="<?= !empty($direct_product) &&  ($direct_product=="Select Day Of Month" || $direct_product=="First Of Month") ? '' : 'display: none' ?>">
      <div class="col-sm-4">
        <div class="form-group">
          <select name="sold_day" id="sold_day" class="form-control input-sm <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="EffecttiveDate" >
            <option value="" hidden selected="selected"></option>
            <!-- <option value="LastDayOfMonth" <?= !empty($sold_day) && $sold_day=="LastDayOfMonth" ? 'selected' : '' ?>>Last - day of the previous month</option> -->
            <?php for($i=1;$i<=30;$i++){?>
              <?php if($i > 1){ ?>
                <option value="<?= $i ?>" <?= !empty($sold_day) && $sold_day==$i ? 'selected' : '' ?>><?= $i ?> days prior to effective day</option>
              <?php }else{ ?>
                <option value="<?= $i ?>" <?= !empty($sold_day) && $sold_day==$i ? 'selected' : '' ?>><?= $i ?> day prior to effective day</option>
              <?php } ?>
            <?php } ?>
          </select>
          <label>Can Be Sold Until </label>
          <p class="error" id="error_sold_day"></p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="section_space gray-bg theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
      <div class="radio-v">
        <label><input name="matchGlobal[]" type="checkbox" value="MembershipRequirement" id="matchGlobal_MembershipRequirement" <?= empty($match_globals) || in_array("MembershipRequirement",$match_globals) ? 'checked' : '' ?>> Match Global</label>
      </div>
  </div>
  <div id="membership_div">
    <h4 class="h4_title ">Membership Requirements
    <a href="prd_history.php" data-type="Membership Requirements" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
    </h4>
    
    <div class="m-b-25">
      <p >Does this Product require a Membership?</p>
        <div class="radio-v">
          <label>
            <input name="is_membership_require" <?= isset($is_membership_require) && $is_membership_require=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MembershipRequirement"> No
          </label>
        </div>
        <div class="radio-v">
          <label>
            <input name="is_membership_require" <?= isset($is_membership_require) && $is_membership_require=="Y" ? 'checked' : '' ?>  type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MembershipRequirement"> Yes
          </label>
        </div>
    </div>
    <p class="error" id="error_is_membership_require"></p>
    <div class="row m-t-20" id="membership_required_div" style="<?= isset($is_membership_require) && $is_membership_require=="Y" ? '' : 'display: none' ?>">
      <div class="col-sm-6 col-md-4 theme-form">
        <div class="form-group">
          <select id="membership_ids" name="membership_ids[]"  multiple="multiple" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="MembershipRequirement">
            <?php if(!empty($prdFeeArray['membership'])){ ?>
              <?php foreach ($prdFeeArray['membership'] as $membership) {?>
                <option  value="<?php echo $membership["id"]; ?>" <?=!empty($membership_ids) && in_array($membership['id'],$membership_ids)?'selected="selected"':''?>><?php echo $membership["name"] .' ('.$membership['product_code'].')'; ?></option>
              <?php } ?>
            <?php } ?>
          </select>
          <label>Select Membership</label>
          <p class="error" id="error_membership_ids"></p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="section_space gray-bg theme-form">
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="Availability" id="matchGlobal_Availability" <?= empty($match_globals) || in_array("Availability",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title">Availability 
    <a href="prd_history.php" data-type="Availability" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
  </h4>
  <p class="m-b-25"><em>Choose the states that are AVAILABLE for this product to be sold in.</em></p>
  
  <h5 class="h5_title m-b-10 "><span class="text-action">Available</span> States</h5>
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
                if(in_array($state['name'],$available_state)){
                  array_push($availableCheckAll, 'Check');
                }else{
                  array_push($availableCheckAll, 'unCheck');
                }
              }
            } ?>
            <tr>
              <td width="20px">
                <label class="red_checkbox"><input name="availableCheckAll" id="availableCheckAll" type="checkbox" value="availableCheckAll" <?=in_array('unCheck',$availableCheckAll)?'':'checked'?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability">
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
                  <label class="red_checkbox"><input name="available_state[]" class="available_state <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability" <?=!empty($available_state) && in_array($state['name'],$available_state)?'checked':''?> type="checkbox" value="<?= $state['name'] ?>" data-state-id="<?= $state['id'] ?>" data-short-name="<?= $state['short_name'] ?>" data-name="<?= $state['name'] ?>"></label>
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
    <p class="error" id="error_available_state"></p>
  </div>
  <div id="available_no_sale_state_div" style="<?= !empty($resAvailableNoSaleState) ? '' : 'display: none' ?>">
    <h5 class="h5_title top_space" id="no_sale_state_title" style="
    <?= !empty($displayNoSaleState) ? '' : 'display: none' ?>">No-Sale States</h5>
    <div id="available_no_sale_state_main_div">
      <?php if(!empty($resAvailableNoSaleState)) { ?>
        <?php foreach ($resAvailableNoSaleState as $stateID => $stateRowArray) { ?>

          <div id="available_no_sale_state_main_<?= $stateID ?>" class="available_no_sale_state_main" style="<?= in_array($allStateRes[$stateID]['name'], $available_state) ? 'display: none' : ''; ?>">
            <?php if(!empty($stateRowArray)){ ?>
              <?php foreach ($stateRowArray as $innerKey => $stateRow) { ?>
                <?php 
                  $today_date = date($DATE_FORMAT);
                  $effectiveDate = !empty($stateRow['effective_date']) ?date($DATE_FORMAT,strtotime($stateRow['effective_date'])) : '';
                  $terminationDate = !empty($stateRow['termination_date']) ? date($DATE_FORMAT,strtotime($stateRow['termination_date'])) : '';
                  $termReadonly = "";
                  $effReadonly = "";
                  if(!empty($terminationDate) && (
                    strtotime($terminationDate) < strtotime($effectiveDate) ||
                    strtotime($terminationDate) < strtotime($today_date))
                  ){
                    $termReadonly = "readonly";
                  }
                  if(!empty($effectiveDate) && strtotime($effectiveDate) < strtotime($today_date)
                  ){
                    $effReadonly = "readonly";
                  }
                ?>
                <div id="available_no_sale_state_<?= $stateID ?>_<?= $stateRow['id'] ?>" class="available_no_sale_state_inner">
                  <div class="no_sale_pricing">
                    <div class="row theme-form">
                      <div class="col-sm-3" id="state_name_<?= $stateID ?>_<?= $stateRow['id'] ?>" >
                          <h5 class="h5_title m-t-10 text-action" style="<?= !in_array($stateRow['state_name'],$alreadyAddedStateName) ? '' : 'display: none'; ?>"><?= $allStateShortName[$stateRow['state_name']] ?>, <?= $stateRow['state_name'] ?></h5>
                      </div>
                      <?php if(!in_array($stateRow['state_name'],$alreadyAddedStateName)){ ?>
                        <?php array_push($alreadyAddedStateName, $stateRow['state_name']); ?>
                      <?php } ?>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <div class="input-group"> 
                          <span class="input-group-addon datePickerIcon" data-applyon="available_no_sale_state_<?= $stateID ?>_<?= $stateRow['id'] ?>_effective_date">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                          </span>
                          <div class="pr">
                              <input type="text" name="available_no_sale_state[<?= $stateID ?>][<?= $stateRow['id'] ?>][effective_date]" class="form-control available_no_sale_state availableEffectiveDate availableEffectiveDate<?= $stateID ?> <?= empty($effReadonly) ?'checkTermed' : '' ?> <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability" data-id="<?= $stateRow['id'] ?>" data-state-id="<?= $stateID ?>"  id="available_no_sale_state_<?= $stateID ?>_<?= $stateRow['id'] ?>_effective_date" value="<?= !empty($stateRow['effective_date']) ? date($DATE_FORMAT,strtotime($stateRow['effective_date'])) : '' ?>" <?= $effReadonly ?>>
                              <label>Effective Date (Required)</label>
                          </div>    
                        </div>
                          <p class="error" id="error_available_no_sale_state_effective_date_<?= $stateID ?>_<?= $stateRow['id'] ?>"></p>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                            <div class="input-group"> 
                          <span class="input-group-addon datePickerIcon" data-applyon="available_no_sale_state_<?= $stateID ?>_<?= $stateRow['id'] ?>_termination_date">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                          </span>
                          <div class="pr">
                              <input  type="text" name="available_no_sale_state[<?= $stateID ?>][<?= $stateRow['id'] ?>][termination_date]" class="form-control available_no_sale_state availableTerminationDate availableTerminationDate<?= $stateID ?> <?= empty($termReadonly) ?'checkTermed' : '' ?> <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability"  placeholder="" data-short-name="~state_short_name~" data-name="~state_full_name~" data-id="<?= $stateRow['id'] ?>" data-state-id="<?= $stateID ?>" id="available_no_sale_state_<?= $stateID ?>_<?= $stateRow['id'] ?>_termination_date" value="<?= !empty($stateRow['termination_date']) ? date($DATE_FORMAT,strtotime($stateRow['termination_date'])) : '' ?>" <?= $termReadonly ?>>
                              <label>Termination Date</label>
                          </div>
                        </div>
                            <p class="error" id="error_available_no_sale_state_termination_date_<?= $stateID ?>_<?= $stateRow['id'] ?>"></p>
                          </div>
                      </div>
                      <div class="col-sm-3" style="display: none">
                        <div class="form-group">
                          <a href="javascript:void(0);" data-id="<?= $stateID ?>" class="btn btn-default add_no_sale_state_effective_date"> + Effective Date</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            <?php } ?>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </div>

 
  <h5 class="h5_title m-t-25">Is this product available only in specific zip codes?</h5>
  <div class="m-b-25">
    <div class="radio-v">
      <label>
        <input name="is_specific_zipcode" <?= isset($is_specific_zipcode) && $is_specific_zipcode=="N" ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability"> No
      </label>
    </div>
    <div class="radio-v">
      <label>
        <input name="is_specific_zipcode" <?= isset($is_specific_zipcode) && $is_specific_zipcode=="Y" ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability"> Yes
      </label>
    </div>
    
    <p class="error" id="error_is_specific_zipcode"></p>
  </div>

  <div id="available_only_zipcode_list_div" style="<?= isset($is_specific_zipcode) && $is_specific_zipcode=="Y" ? '' : 'display: none' ?>" >
    <div class="row col-down">
        <div class="col-sm-4">
          <div class="form-group">
              <select name="zipcode_allow_only_state[]" id="zipcode_allow_only_state" multiple="multiple" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability">
                <?php if(!empty($allStateRes)){
                  foreach ($allStateRes as $key => $state) { 
                    if(in_array($state['name'],$available_state)){ ?>
                    <option value="<?= $state['name'] ?>" <?= (array_key_exists($state['name'], $specificZipCodeArr)) ? 'selected' : '' ?>><?= $state['short_name'].', '.$state['name']?></option>
                  <?php } 
                  }
                } ?>
              </select>
              <label>Select State(s) </label>
              <p class="error" id="error_zipcode_allow_only_state"></p>
          </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row" id="available_specific_zipcode">
      <?php if(!empty($specificZipCodeArr)) { ?>
        <?php foreach ($specificZipCodeArr as $stateName => $zipCodes) { ?>
          <?php $zipCodesList = !empty($zipCodes) ? implode(",",$zipCodes) : '' ;?>
          <div class="col-sm-6 col-md-4" id="available_specific_zipcode_div_<?= str_replace(" ","_", $stateName) ?>" data-state-name="<?= $stateName ?>">
              <p class="text-action fw500 mn"><?= $allStateShortName[$stateName] ?>, <?= $stateName ?> - Zip Code(s)</p>
              <div class="form-group">
                <div class="cust-tag-bs">
                  <input name="available_state_zipcode[<?= $stateName ?>]" id="available_state_zipcode_<?=str_replace(" ","_", $stateName) ?>" type="text" class="tagsinput <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability" value="<?= $zipCodesList ?>"/>
                </div>
                <p class="error" id="error_available_state_zipcode_<?= str_replace(" ","_", $stateName) ?>"></p>
              </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </div>

  
  <div class="mn">
     <h5 class="h5_title">If member moves to no sale state, should Plan continue?</h5>
     <div class="radio-v">
        <label>
          <input name="no_sale_state_coverage_continue" type="radio" value="N" <?= !empty($no_sale_state_coverage_continue) && $no_sale_state_coverage_continue=='N' ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability"> No
        </label>
      </div>
      <div class="radio-v">
          <label>
            <input name="no_sale_state_coverage_continue" type="radio" value="Y" <?= !empty($no_sale_state_coverage_continue) && $no_sale_state_coverage_continue=='Y' ? 'checked' : '' ?> class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="Availability"> Yes
          </label>
      </div>
      <p class="error" id="error_no_sale_state_coverage_continue"></p>
  </div>
</div>
<div class="section_space gray-bg theme-form">   
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="CoverageOptions" id="matchGlobal_CoverageOptions" <?= empty($match_globals) || in_array("CoverageOptions",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title ">Plan Options
  <a href="prd_history.php" data-type="Coverage Options" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
  </h4>
  <div class="m-b-15">
    <p class="m-b-20"><strong>Select Plans for this product.</strong></p>
    <div class="radio-hide-btn flex_row">
    <?php if(!empty($prdPlanTypeArray)) {?>
      <?php foreach ($prdPlanTypeArray as $key => $tier) {
        if($tier['title']=="Member Only") {
          $coverage_id="coverage_member_only";
        }
        if($tier['title']=="Member + Child(ren)") {
          $coverage_id="coverage_member_children";
        }
        if($tier['title']=="Member + Spouse") {
          $coverage_id="coverage_member_spouse";
        }
        if($tier['title']=="Family") {
          $coverage_id="coverage_family";
        }
        if($tier['title']=="Member + One") {
          $coverage_id="coverage_member_plus_one";
        }
        ?>
        <div class="coverage_btn">
          <label class="btn btn-primary <?=!empty($coverage_options) && in_array($tier['id'],$coverage_options)?'active':''?>" for="<?= $coverage_id ?>">
            <?php if(!($allowPricingUpdate) && !empty($coverage_options) && in_array($tier['id'],$coverage_options)){ ?>
              <input type="hidden" name="allow_coverage_options[]" value="<?= $tier['id'] ?>">
            <?php } ?>
            <input name="coverage_options[]" class="coverage_options js-switch <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="CoverageOptions" id="<?= $coverage_id ?>" <?=!empty($coverage_options) && in_array($tier['id'],$coverage_options)?'checked':''?> type="checkbox" value="<?= $tier['id'] ?>" data-text="<?= $tier['title'] ?>" data-order="<?= $tier['order_by'] ?>" <?=  (!($allowPricingUpdate) ? 'disabled' : '') ?>> <?= $tier['title'] ?>
          </label>
        </div>
      <?php } ?>
    <?php } ?>
   </div>
    
    <p class="error" id="error_coverage_options"></p>
  </div>
  <div id="family_plan_rule_div" style="<?= !empty($coverage_options) && (in_array('4',$coverage_options)) ? '' : 'display: none' ?>">
    <h5 class="h5_title">Family Plan Options</h5>
    <div class="row">
      <div class="col-sm-6 col-md-6">
        <div class="radio-v">
          <label>
            <input name="family_plan_rule" <?= !empty($family_plan_rule) && $family_plan_rule=="Spouse And Child" ? 'checked' : '' ?> type="radio" value="Spouse And Child" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="CoverageOptions"> Family requires one spouse and child
          </label>
        </div>
        <div class="radio-v">
          <label class="label-input">
            <input name="family_plan_rule" <?= !empty($family_plan_rule) && $family_plan_rule=="Minimum One Dependent" ? 'checked' : '' ?> type="radio" value="Minimum One Dependent" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="CoverageOptions"> Family requires minimum of one dependent
          </label>
        </div>
        <div class="radio-v">
          <label class="label-input">
            <input name="family_plan_rule" <?= !empty($family_plan_rule) && $family_plan_rule=="Minimum Two Dependent" ? 'checked' : '' ?> type="radio" value="Minimum Two Dependent" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="CoverageOptions"> Family requires minimum of two dependents
          </label>
        </div>
        <p class="error" id="error_family_plan_rule"></p>
      </div>
    </div>
  </div>
</div>
<div class="section_space gray-bg theme-form">  
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="SubProducts" id="matchGlobal_SubProducts" <?= empty($match_globals) || in_array("SubProducts",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title ">Sub Products
  <a href="prd_history.php" data-type="Sub Products" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
  </h4>
  <p class="m-b-20"><em>If this is a parent product, enter any products that are within this bundle.</em></p>
  <div class="row">
    <div class="col-sm-6 col-md-4">
      <div class="form-group">
          <select name="sub_product[]" id="sub_product" multiple="multiple" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="SubProducts">
            <?php if(!empty($subProductsArray)){?>
              <?php foreach ($subProductsArray as $carrierName => $subProductsRows) { ?>
                <optgroup label='<?= $carrierName ?>'>

                  <?php foreach ($subProductsRows as $key => $rows) { ?>
                    <option value="<?= $rows['id'] ?>" <?= !empty($subProductList) && in_array($rows['id'], $subProductList) ? 'selected' :'' ?>>
                        <?= $rows['product_name'] .' ('. $rows['product_code'].')' ?>
                    </option>
                  <?php } ?>
                
                </optgroup>
              <?php } ?>
            <?php } ?>
          </select>
          <label>Select Product</label>
          <p class="error" id="error_sub_product ?>"></p>
      </div>
    </div>
  </div>
</div>
<div class="section_space gray-bg theme-form prd_combination_rules"> 
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="ProductCombinationRules" id="matchGlobal_ProductCombinationRules" <?= empty($match_globals) || in_array("ProductCombinationRules",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title ">Product Combination Rules
  <a href="prd_history.php" data-type="Product Combination Rules" class="popup_lg"> <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
  </h4>
  <p class="m-b-25"><em>Enter any products that can not be purchased by a person who has purchased this product, or other product combination rules.</em></p>
  <div class="row theme-form">
    <div id="combination_rules_div">
      <div class="col-sm-12 col-md-6 auto_assign_rules_div" id="auto_assign_rules_div">
        <div class="form-group height_auto">
          <div class="row">
            <div class="col-sm-4">
              <input type="text" name="product_details[Auto Assign]" class="form-control" value="Auto Assign" readonly="">
              <label>Product Details</label>
            </div>
            <div class="col-sm-8">
                <select name="autoAssignProduct[]" id="autoAssignProduct" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="ProductCombinationRules" multiple="multiple">
                  <?php if(!empty($productArray2)){ ?>
                    <?php foreach ($productArray2 as $categoryName => $productRow) { ?>
                      <optgroup label='<?= $categoryName; ?>'>
                        <?php foreach ($productRow as $key1 => $row1) { ?>
                        <option value="<?= $row1['id'] ?>" <?=!empty($autoAssignProduct) && in_array($row1['id'],$autoAssignProduct)?'selected':''?> <?= !in_array($row1['id'],$autoAssignProduct) && in_array($row1['id'], $productCombinationRulesArr)?'disabled':''?> ><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                        <?php } ?>
                      </optgroup>
                    <?php } ?>
                  <?php } ?>
                </select>
                <p class="error" id="error_autoAssignProduct"></p>
            </div>
            <div class="col-sm-12">
              <p class="text-action fs12"><em>Products selected in this category will automatically be assigned but user may remove them from Application</em></p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-6 packaged_rules_div" id="packaged_rules_div">
        <div class="form-group height_auto">
          <div class="row">
            <div class="col-sm-4">
              <input type="text" name="product_details[Packaged]" class="form-control" value="Packaged" readonly="">
              <label>Product Details</label>
            </div>
            <div class="col-sm-8">
                <select name="packagedProduct[]" id="packagedProduct" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="ProductCombinationRules" multiple="multiple">
                  <?php if(!empty($productArray)){ ?>
                    <?php foreach ($productArray as $categoryName => $productRow) { ?>
                      <optgroup label='<?= $categoryName; ?>'>
                        <?php foreach ($productRow as $key1 => $row1) { ?>
                        <option value="<?= $row1['id'] ?>" <?=!empty($packagedProduct) && in_array($row1['id'],$packagedProduct)?'selected':''?> <?= !in_array($row1['id'],$packagedProduct) && in_array($row1['id'], $productCombinationRulesArr)?'disabled':''?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                        <?php } ?>
                      </optgroup>
                    <?php } ?>
                  <?php } ?>
                </select>
                <p class="error" id="error_packagedProduct"></p>
            </div>
            <div class="col-sm-12">
              <p class="text-action fs12"><em>At least one product selected in this category will be required on Application or active on an existing member for this product to be added</em></p>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-sm-12 col-md-6 combination_rules_div" id="combination_rules_div">
        <div class="form-group height_auto">
          <div class="row">
            <div class="col-sm-4">
              <input type="text" name="product_details[Excludes]" class="form-control" value="Excludes" readonly="">
              <label>Product Details</label>
            </div>
            <div class="col-sm-8">
                <select name="excludeProduct[]" id="excludeProduct" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="ProductCombinationRules" multiple="multiple">
                  <?php if(!empty($productArray)){ ?>
                    <?php foreach ($productArray as $categoryName => $productRow) { ?>
                      <optgroup label='<?= $categoryName; ?>'>
                        <?php foreach ($productRow as $key1 => $row1) { ?>
                        <option value="<?= $row1['id'] ?>" <?=!empty($excludeProduct) && in_array($row1['id'],$excludeProduct)?'selected':''?> <?= !in_array($row1['id'],$excludeProduct) && in_array($row1['id'], $productCombinationRulesArr)?'disabled':''?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                        <?php } ?>
                      </optgroup>
                    <?php } ?>
                  <?php } ?>
                </select>
                <p class="error" id="error_excludeProduct"></p>
            </div>
            <div class="col-sm-12">
              <p class="text-action fs12"><em>Products selected in this category will not be allowed to be combined on Application or to existing member if this product is currently active.</em></p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-sm-12 col-md-6 auto_assign_no_delete_rules_div" id="auto_assign_no_delete_rules_div">
        <div class="form-group height_auto">
          <div class="row">
            <div class="col-sm-4">
              <input type="text" name="product_details[Required]" class="form-control" value="Required" readonly="">
              <label>Product Details</label>
            </div>
            <div class="col-sm-8">
                <select name="requiredProduct[]" id="requiredProduct" class="se_multiple_select <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="ProductCombinationRules" multiple="multiple">
                  <?php if(!empty($productArray2)){ ?>
                    <?php foreach ($productArray2 as $categoryName => $productRow) { ?>
                      <optgroup label='<?= $categoryName; ?>'>
                        <?php foreach ($productRow as $key1 => $row1) { ?>
                        <option value="<?= $row1['id'] ?>" <?=!empty($requiredProduct) && in_array($row1['id'],$requiredProduct)?'selected':''?> <?= !in_array($row1['id'],$requiredProduct) && in_array($row1['id'], $productCombinationRulesArr)?'disabled':''?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
                        <?php } ?>
                      </optgroup>
                    <?php } ?>
                  <?php } ?>
                </select>
                <p class="error" id="error_requiredProduct"></p>
            </div>
            <div class="col-sm-12">
              <p class="text-action fs12"><em>Products selected in this category are required to be assigned on Application or active on an existing member for this product to be added</em></p>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div> 
<div class="section_space gray-bg theme-form"> 
  <div class="pull-right" style="<?= $record_type=="Variation" ? '' : 'display:none' ?>">
    <div class="radio-v">
      <label><input name="matchGlobal[]" type="checkbox" value="TerminationRules" id="matchGlobal_TerminationRules" <?= empty($match_globals) || in_array("TerminationRules",$match_globals) ? 'checked' : '' ?>> Match Global</label>
    </div>
  </div>
  <h4 class="h4_title">Termination Rules
   <a href="prd_history.php" data-type="Termination Rules" class="popup_lg">  <i class="fa fa-history text-blue fs16"  data-toggle="tooltip" data-placement="top" title="History"></i> </a>
  </h4>
  <p class="m-b-20"><em>Set the termination rules for this product below.</em></p>
  <div class="row">
    <div class="col-sm-5 col-md-4">
      <div class="form-group">
        <select name="termination_rule" id="termination_rule" class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules">
          <option value="" selected="selected" hidden></option>
          <option value="End of Current Coverage Period" <?= isset($termination_rule) && $termination_rule == "End of Current Coverage Period" ? 'selected' : '' ?>>End of Plan Period</option>
        </select>
        <label>Date Rules </label>
        <p class="error" id="error_termination_rule"></p>
      </div>
    </div>
  </div>
  
  <div class="m-b-25">
    <p>Can product be termed back to effective to eliminate Plan from ever being active?</p>
    <div class="radio-v">
      <label>
        <input name="term_back_to_effective" <?= !empty($term_back_to_effective) && $term_back_to_effective=='N' ? 'checked' : '' ?> type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules"> No
      </label>
    </div>
    <div class="radio-v">
      <label>
        <input name="term_back_to_effective" <?= !empty($term_back_to_effective) && $term_back_to_effective=='Y' ? 'checked' : '' ?> type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules"> Yes
      </label>
    </div>
    <p class="error" id="error_term_back_to_effective"></p>
  </div>

  <div class="m-b-25">
    <p>Does this product terminate automatically after a set period of time?</p>
    <div class="radio-v">
      <label>
        <input name="term_automatically" <?= !empty($term_automatically) && $term_automatically=='N' ? 'checked' : '' ?>  type="radio" value="N" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules"> No
      </label>
    </div>
    <div class="radio-v">
      <label>
        <input name="term_automatically" <?= !empty($term_automatically) && $term_automatically=='Y' ? 'checked' : '' ?>  type="radio" value="Y" class="<?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules"> Yes
      </label>
    </div>
    <p class="error" id="error_term_automatically"></p>
  </div>
  <div id="term_automatically_after_div" style="<?= !empty($term_automatically) && $term_automatically=='Y' ? '' : 'display: none' ?> ">
      <div class="form-inline m-l-25">
        <div class="form-group">
          <select name="term_automatically_within" id="term_automatically_within" class="form-control input-sm <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules">
            <?php
            if(isset($term_automatically_within_type) && $term_automatically_within_type=="Weeks"){
              $end_range = 52;
            }else if(isset($term_automatically_within_type) && $term_automatically_within_type=="Months"){
              $end_range = 24;
            }else if(isset($term_automatically_within_type) && $term_automatically_within_type=="Years"){
              $end_range = 10;
            }else if(isset($term_automatically_within_type) && $term_automatically_within_type=="Coverage Period"){
              $end_range = 24;
            }else{
              $end_range = 365;
            }
            ?>
            <?php for($i=0;$i<=$end_range;$i++){ ?>
              <option value="<?= $i ?>" <?= isset($term_automatically_within) && $i==$term_automatically_within ? 'selected' :'' ?>><?= $i ?></option>
            <?php } ?>
          </select>
          <select name="term_automatically_within_type" id="term_automatically_within_type" class="form-control input-sm <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules">
            <option value="Days" <?= isset($term_automatically_within_type) && 'Days'==$term_automatically_within_type ? 'selected' :'' ?>>Days</option>
            <option value="Weeks" <?= isset($term_automatically_within_type) && 'Weeks'==$term_automatically_within_type ? 'selected' :'' ?>>Weeks</option>
            <option value="Months" <?= isset($term_automatically_within_type) && 'Months'==$term_automatically_within_type ? 'selected' :'' ?>>Months</option>
            <option value="Years" <?= isset($term_automatically_within_type) && 'Years'==$term_automatically_within_type ? 'selected' :'' ?>>Years</option>
            <option value="Coverage Period" <?= isset($term_automatically_within_type) && 'Coverage Period'==$term_automatically_within_type ? 'selected' :'' ?>>Plan Period</option>
          </select>
          <p class="error" id="error_term_automatically_within"></p>
        </div>
      </div>
  </div>

  <div >
    <h5 class="h5_title">Reinstate Options</h5> <p class="m-b-20"><em>Options for after a Plan has lapsed, if the member can backpay and not have a lapse in Plan.</em></p>
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules" name="reinstate_option" id="reinstate_option">
            <option value="" selected="selected" hidden></option>
            <option value="Not Available" <?= isset($reinstate_option) && $reinstate_option=="Not Available" ? 'selected' : '' ?>>Not available</option>
            <option value="Available Without Restrictions" <?= isset($reinstate_option) && $reinstate_option=="Available Without Restrictions" ? 'selected' : '' ?>>Available without restrictions</option>
            <option value="Available Within Specific Time Frame" <?= isset($reinstate_option) && $reinstate_option=="Available Within Specific Time Frame" ? 'selected' : '' ?>>Available within specific time frame</option>
          </select>
          <label>Select Reinstate Option </label>
          <p class="error" id="error_reinstate_option"></p>
        </div>
      </div>
    

      <div class="col-sm-4" id="reinstate_within_div" style="<?= isset($reinstate_option) && $reinstate_option=="Available Within Specific Time Frame" ? '' : 'display: none'; ?>">
        <div class="form-inline">
          <div class="form-group">
            <select name="reinstate_within" id="reinstate_within" class="form-control input-sm <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules">
              <?php
              if(isset($reinstate_within_type) && $reinstate_within_type=="Weeks"){
              $end_range = 52;
              }else if(isset($reinstate_within_type) && $reinstate_within_type=="Months"){
              $end_range = 24;
              }else if(isset($reinstate_within_type) && $reinstate_within_type=="Years"){
              $end_range = 10;
              }else{
              $end_range = 365;
              }
              ?>
              <?php for($i=0;$i<=$end_range;$i++){ ?>
              <option value="<?= $i ?>" <?= isset($reinstate_within) && $i==$reinstate_within ? 'selected' :'' ?>><?= $i ?></option>
              <?php } ?>
            </select>
            <select name="reinstate_within_type" id="reinstate_within_type" class="form-control input-sm <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules">
              <option value="Days" <?= isset($reinstate_within_type) && 'Days'==$reinstate_within_type ? 'selected' :'' ?>>Days</option>
              <option value="Weeks" <?= isset($reinstate_within_type) && 'Weeks'==$reinstate_within_type ? 'selected' :'' ?>>Weeks</option>
              <option value="Months" <?= isset($reinstate_within_type) && 'Months'==$reinstate_within_type ? 'selected' :'' ?>>Months</option>
              <option value="Years" <?= isset($reinstate_within_type) && 'Years'==$reinstate_within_type ? 'selected' :'' ?>>Years</option>
            </select>
            <p class="error" id="error_reinstate_within"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>
    <h5 class="h5_title">Select Reenroll Option</h5>  <p class="m-b-20"><em>Options for how long a member must wait to reenroll again after canceling a Plan.</em></p>
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <select class="form-control <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules" name="reenroll_options" id="reenroll_options">
            <option value="" selected="selected" hidden></option>
            <option value="Not Available" <?= isset($reenroll_options) && $reenroll_options=="Not Available" ? 'selected' : '' ?>>Not available</option>
            <option value="Available Without Restrictions" <?= isset($reenroll_options) && $reenroll_options=="Available Without Restrictions" ? 'selected' : '' ?>>Available without restrictions</option>
            <option value="Available After Specific Time Frame" <?= isset($reenroll_options) && $reenroll_options=="Available After Specific Time Frame" ? 'selected' : '' ?>>Available after specific time frame</option>
          </select>
          <label>Select Reenroll Option</label>
          <p class="error" id="error_reenroll_options"></p>
        </div>
      </div>
      <div class="col-sm-4" id="reenroll_within_div" style="<?= isset($reenroll_options) &&  $reenroll_options=="Available After Specific Time Frame" ? '' : 'display: none'; ?>">
        <div class="form-inline">
          <div class="form-group">
            <select name="reenroll_within" id="reenroll_within" class="form-control input-sm <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules">
              <?php
              if(isset($reenroll_within_type) && $reenroll_within_type=="Weeks"){
                $end_range = 52;
              }else if(isset($reenroll_within_type) && $reenroll_within_type=="Months"){
                $end_range = 24;
              }else if(isset($reenroll_within_type) && $reenroll_within_type=="Years"){
                $end_range = 10;
              }else{
                $end_range = 365;
              }
              ?>
              <?php for($i=0;$i<=$end_range;$i++){ ?>
              <option value="<?= $i ?>" <?= isset($reenroll_within) && $i==$reenroll_within ? 'selected' :'' ?>><?= $i ?></option>
              <?php } ?>
            </select>
            <select name="reenroll_within_type" id="reenroll_within_type" class="form-control input-sm <?= $record_type=="Variation" ? 'matchGlobal' : '' ?>" data-match-on="TerminationRules">
              <option value="Days" <?= isset($reenroll_within_type) && 'Days'==$reenroll_within_type ? 'selected' :'' ?>>Days</option>
              <option value="Weeks" <?= isset($reenroll_within_type) && 'Weeks'==$reenroll_within_type ? 'selected' :'' ?>>Weeks</option>
              <option value="Months" <?= isset($reenroll_within_type) && 'Months'==$reenroll_within_type ? 'selected' :'' ?>>Months</option>
              <option value="Years" <?= isset($reenroll_within_type) && 'Years'==$reenroll_within_type ? 'selected' :'' ?>>Years</option>
            </select>
            <p class="error" id="error_reenroll_within"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="section_space text-right step_btn_wrap">
  <!-- <a href="javascript:void(0);" class="btn btn-primary pull-left"> Summary</a> -->
  <a href="javascript:void(0);" class="btn btn-action btn_next"> Next</a>
  <a href="javascript:void(0);" class="btn btn-action-o btn_save_exit"> Save & Exit</a>
  <a href="javascript:void(0);" class="red-link btn_cancel"> Cancel</a>
</div>