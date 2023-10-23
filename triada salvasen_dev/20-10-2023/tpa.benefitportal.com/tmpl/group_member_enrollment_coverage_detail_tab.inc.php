<div class="pull-left">
   <h2 class="m-t-0 m-b-30">Enrollment Information</h2>
</div>
<div class="pull-right" style="<?=!empty($member_rep_id) ? '' : 'display: none;'?>">
   <a href="javascript:void(0)" class="btn red-link" id="edit_enroll" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
</div>
<div class="clearfix"></div>
<div class="row enrollment_auto_row">
   <div class="col-sm-12">
      <div id="edit_show_enroll" style="<?=empty($member_rep_id) ? '' : 'display: none;'?>">
         <div class="row">
            <div class="col-sm-6">
               <div class="form-group">
                  <select class="form-control" name="coveragePeriod" id="coverage_period">
                     <option data-hidden="true"></option>
                     <?php if(!empty($coverage_period_row)){ ?>
                        <?php foreach($coverage_period_row as $coverage){ ?>
                           <option value="<?php echo $coverage['id']?>" <?php echo !empty($coverage_period) && $coverage_period == $coverage['id'] ? "selected='selected'" : '' ?>><?php echo $coverage['coverage_period_name']?></option>
                        <?php } ?>
                     <?php } ?>
                  </select>
                  <label>Coverage Period*</label>
                  <p class="error" id="error_coveragePeriod"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <select class="form-control" name="groupCompany" id="group_company" class="groupCompany">
                     <option data-hidden="true"></option>
                     <?php if(!empty($groupCompany)){ ?>
                        <?php foreach($groupCompany as $company){ ?>
                           <option value="<?php echo $company['id']?>" <?php echo $group_company == $company['id'] ? "selected='selected'" : '' ?>><?php echo $company['name']?></option>
                        <?php } ?>
                     <?php } ?>
                  </select>
                  <label>Company/Group Name*</label>
                  <p class="error" id="error_groupCompany"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="hidden" name="hdn_enrolle_class" id="hdn_enrolle_class" value="<?=$enrollee_class?>">
                  <select class="form-control" name="enrolleeClass" id="enrollee_class" <?=!empty($enrollee_class) && $memberType == 'lead' ? 'disabled' : "" ?>>
                     <option data-hidden="true"></option>
                     <?php if(!empty($enrollee_class_row)){ ?>
                        <?php foreach($enrollee_class_row as $enrolleeclass){ ?>
                           <option value="<?php echo $enrolleeclass['id']?>" <?php echo !empty($enrollee_class) && $enrollee_class == $enrolleeclass['id'] ? "selected='selected'" : '' ?> data-pay_period="<?=!empty($enrolleeclass['pay_period']) ? $enrolleeclass['pay_period'] : ''?>"><?php echo $enrolleeclass['class_name']?></option>
                        <?php } ?>
                     <?php } ?>
                  </select>
                  <label>Enrollee Class*</label>
                  <p class="error" id="error_enrolleeClass"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <div class="input-group">
                     <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                     <div class="pr">
                        <input type="text" class="form-control date_picker dateClass" name="relationshipDate" id="relationship_date" value="<?=$relationship_date?>" <?=!empty($relationship_date) && $memberType == 'lead' ? 'readonly' : "" ?>>
                        <label>Relationship Date (MM/DD/YYYY)*</label>
                     </div>
                  </div>
                  <p class="error" id="error_relationshipDate"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="hidden" name="hdn_relationship_to_group" id="hdn_relationship_to_group" value="<?=$relationship_of_group?>">
                  <select class="form-control" name="relationshipOfGroup" id="relationship_of_group" <?=!empty($relationship_of_group) && $memberType == 'lead' ? 'disabled' : "" ?>>
                     <option data-hidden="true"></option>
                     <option value="Existing" <?= !empty($relationship_of_group) && $relationship_of_group == 'Existing' ? 'selected' : '' ?>>Existing</option>
                     <option value="New" <?= !empty($relationship_of_group) && $relationship_of_group == 'New' ? 'selected' : '' ?>>New</option>
                     <option value="Renew" <?= !empty($relationship_of_group) && $relationship_of_group == 'Renew' ? 'selected' : '' ?>>Renew</option>
                  </select>
                  <label>Relationship of Group*</label>
                  <p class="error" id="error_relationshipOfGroup"></p>
               </div>
            </div>
         </div>
      </div>
      <div id="edit_hide_enroll" style="<?=!empty($member_rep_id) ? '' : 'display: none;'?>">
         <div class="table-responsive mdp_info_table">
            <table cellspacing="0" cellpadding="0" width="100%">
               <tbody>
                  <tr>
                     <td>Coverage Period</td>
                     <td><?=implode(',',array_column($coverage_period_table, 'coverage_period_name'))?></td>
                  </tr>
                  <tr>
                     <td>Company/Group Name</td>
                     <td><?=$group_company_name?></td>
                  </tr>
                  <tr>
                     <td>Enrollee Class</td>
                     <td><?=implode(',',array_column($enrollee_class_table, 'class_name'))?></td>
                  </tr>
                  <tr>
                     <td>Relationship Date</td>
                     <td><?=$relationship_date?></td>
                  </tr>
                  <tr>
                     <td>Relationship of Group</td>
                     <td><?=$relationship_of_group?></td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<hr>
<div class="pull-left">
   <h4 class="m-t-0 m-b-30">Primary Policy Holder Information</h4>
</div>
<div class="pull-right" style="<?=!empty($member_rep_id) ? '' : 'display: none;'?>">
   <a href="javascript:void(0)" class="btn red-link" id="edit_primary" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
</div>
<div class="clearfix"></div>
<div class="row">
   <div class="col-sm-12">
      <p class="m-b-15">Coverage Details</p>
      <div id="edit_show_primary" style="<?=empty($member_rep_id) ? '' : 'display: none;'?>">
         <div class="row enrollment_auto_row">
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" class="form-control primary_fname_coverage primary_fname_1" name="primaryName" id="primary_fname_coverage" value="<?=$primary_name?>">
                  <label>Primary Name*</label>
                  <p class="error" id="error_primaryName"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" class="form-control primary_email_coverage" name="primaryEmail" id="primary_email_coverage" value="<?=$primary_email?>">
                  <label>Email*</label>
                  <p class="error" id="error_primaryEmail"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <div class="input-group">
                     <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                     <div class="pr">
                        <input type="text" class="form-control date_picker dateClass primary_birthdate_coverage primary_birthdate_1" name="primaryBirthdate" id="primary_birthdate_coverage" value="<?=$primary_birthdate?>">
                        <label>DOB (MM/DD/YYYY)*</label>
                     </div>
                  </div>
                  <p class="error" id="error_primaryBirthdate"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" class="form-control zip primary_zip_coverage primary_zip_1" name="primaryZipcode" id="primary_zip_coverage" value="<?=$primary_zipcode?>">
                  <label>Zip Code*</label>
                  <p class="error" id="error_primaryZipcode"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <div class="btn-group btn-custom-group btn-group-justified">
                     <div class="toggle-item">
                        <input class="js-switch primary_gender_1" type="radio" id="primary_gender_male" name="primaryGender" value="Male" <?= (!empty($primary_gender) && $primary_gender == 'Male' ? 'checked' : '') ?> />
                        <label for="primary_gender_male" class="btn btn-info">Male</label>
                     </div>
                     <div class="toggle-item">
                        <input class="js-switch primary_gender_1" type="radio" id="primary_gender_female" name="primaryGender" value="Female" <?= (!empty($primary_gender) && $primary_gender == 'Female' ? 'checked' : '') ?> />
                        <label for="primary_gender_female" class="btn btn-info">Female</label>
                     </div>
                  </div>
                  <p class="error" id="error_primaryGender"></p>
               </div>
            </div>
         </div>
      </div>
      <div id="edit_hide_primary" style="<?=!empty($member_rep_id) ? '' : 'display: none;'?>">
         <div class="table-responsive mdp_info_table">
            <table cellspacing="0" cellpadding="0" width="100%">
               <tbody>
                  <tr>
                     <td>Primary Name</td>
                     <td><?=$primary_fname?></td>
                  </tr>
                  <tr>
                     <td>Email</td>
                     <td><?=$primary_email?></td>
                  </tr>
                  <tr>
                     <td>Date of Birth</td>
                     <td><?=date('m/d/Y', strtotime($primary_birthdate))?></td>
                  </tr>
                  <tr>
                     <td>Zip Code</td>
                     <td><?=$primary_zipcode?></td>
                  </tr>
                  <tr>
                     <td>Gender</td>
                     <td><?=$primary_gender?></td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<?php if(!empty($member_rep_id)){ ?>
<p class="m-b-15 m-t-30">Additional Information</p>
<div id="edit_show_primary_additional" style="<?=empty($member_rep_id) ? '' : 'display: none;'?>">
   <div class="row enrollment_auto_row">
      <?php if(!empty($additionalInfo)){
         $primary_benefit_arr = array('primaryBenefit_amount','primaryIn_patient_benefit','primaryOut_patient_benefit','primaryMonthly_income','primaryBenefit_percentage');
         foreach($additionalInfo as $key => $row){

            $prd_question_id = $row['id'];
            $questionType = $row['questionType'];
            $control_type = $row['control_type'];
            $control_name = "primary".ucfirst($row['label']);
            $control_id = $control_name."_coverage";
            $maxlength = $row['control_maxlength'];
            $label = $row['display_label'];
            $is_required= $row['required'];
            $control_class= $row['control_class'];

            if(in_array($row['label'],array('fname','lname','SSN','phone','city','state','zip','email','birthdate','gender'))){
               continue;
            }
            $readOnly = !empty($additionalDisabledQuestion[$prd_question_id]['label']) && $additionalDisabledQuestion[$prd_question_id]['label'] == $row['label'] ? 'disabled="disabled"' : '';
            $control_value = "";
           
            if($questionType == "Default"){
               if($control_type == "text" && !in_array($control_name,$primary_benefit_arr)){ 
                     if($control_name == "primaryAddress1"){
                        $control_value = $primary_address1;
                     }elseif($control_name == "primaryAddress2"){
                        $control_value = $primary_address2;
                     }elseif($control_name == "primaryHours_per_week"){
                        $control_value = $primary_hours_per_week;
                     }elseif($control_name == "primarySalary"){
                        $control_value = $primary_salary;
                     }
                  ?>

                  <div class="col-sm-4">
                     <div class="form-group">
                        <input type="text" maxlength="<?= $maxlength ?>" class="form-control coverage_tab_input <?= $control_id ?> <?=$control_class?>"  required name="<?= $control_name ?>" id="<?= $control_id ?>" value="<?= $control_value ?>" <?=in_array($row['label'],array('address1','address2')) ? '' : $readOnly?> data-type="<?=$control_type?>">
                        <label><?= $label ?><?php if($is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                     </div>
                  </div>
                  <?php if($control_name == "primarySalary"){?>
                     <input type='hidden' class="hidden_coverage" data-name='primary_annual_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_name?>' value='<?=$control_value?>'>
                  <?php }else{ ?>
                     <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_name?>' value='<?=$control_value?>'>
                  <?php } ?>
               <?php }else if($control_type == "date_mask" && !in_array($control_name,$primary_benefit_arr)) {?>
                  <div class="col-sm-4">
                     <div class="form-group">
                        <div class="input-group">
                           <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                           <div class="pr">
                              <input type="text" class="form-control date_picker dateClass coverage_tab_input <?= $control_id ?> <?=$control_class?>" name="<?= $control_name ?>" id="<?= $control_id ?>" value="<?= displayDate($primary_date_of_hire) ?>" <?=$readOnly?> data-type="<?=$control_type?>">
                              <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_name?>' value='<?=$primary_date_of_hire?>'>
               <?php }else if($control_type=='select' && !in_array($control_name,$primary_benefit_arr)){ ?>
                  <div class="col-sm-4">
                     <div class="form-group">
                        <select class="form-control coverage_tab_input <?= $control_id ?> <?=$control_class?>" name="<?= $control_name ?>" id="<?= $control_id ?>" required data-live-search="true" <?=$readOnly?> data-type="<?=$control_type?>">
                           <?php if(in_array($control_name,array('primaryHeight'))){ ?>
                              <option value=""></option>
                              <?php for($i=1; $i<=8;$i++){?>
                                 <?php for($j=0; $j<=11;$j++){?>
                                    <option value="<?=$i.'.'.$j?>" <?= !empty($height_feet) && !empty($height_inch) && $i ==  $height_feet && $j == $height_inch ? "selected='selected'" : "" ?>>
                                       <?php
                                          echo $i.' Ft. ';
                                          if($j>0){
                                             echo $j.' In. ';
                                          }  
                                       ?>
                                    </option>
                                 <?php }?>
                              <?php }?>
                           <?php }else if(in_array($control_name,array('primaryWeight'))){ ?>
                              <option value=""></option>
                              <?php for($i=1; $i<=1000;$i++){?>
                                 <option value="<?= $i ?>" <?= $primary_weight == $i ? "selected='selected'" : ""  ?> ><?= $i ?></option>
                              <?php }?>
                           <?php }else if(in_array($control_name,array('primaryNo_of_children'))){ ?>
                              <option value=""></option>
                              <?php for($i=1; $i<=15;$i++){?>
                                 <option value="<?=$i?>" <?= $primary_no_of_children == $i ? "selected='selected'" : ""?>><?= $i ?></option>
                              <?php }?>
                           <?php }else if(in_array($control_name,array('primaryPay_frequency'))){ ?>
                              <option value=""></option>
                              <option value="Annual" <?= $primary_pay_frequency == "Annual" ? "selected='selected'" : "" ?>>Annual</option>
                              <option value="Monthly" <?= $primary_pay_frequency == "Monthly" ? "selected='selected'" : "" ?>>Monthly</option>
                              <option value="Semi-Monthly" <?= $primary_pay_frequency == "Semi-Monthly" ? "selected='selected'" : "" ?>>Semi-Monthly</option>
                              <option value="Semi-Weekly" <?= $primary_pay_frequency == "Semi-Weekly" ? "selected='selected'" : "" ?>>Semi-Weekly</option>
                              <option value="Weekly" <?= $primary_pay_frequency == "Weekly" ? "selected='selected'" : "" ?>>Weekly</option>
                              <option value="Hourly" <?= $primary_pay_frequency == "Hourly" ? "selected='selected'" : "" ?>>Hourly</option>
                           <?php } ?>
                        </select>
                        <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                     </div>
                  </div>
                  <?php if(in_array($control_name,array('primaryHeight'))){ ?>
                     <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_name?>' value='<?=$height_feet.'.'.$height_inch?>'>
                  <?php }else if(in_array($control_name,array('primaryWeight'))) {?>
                     <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_name?>' value='<?=$primary_weight?>'>
                  <?php }else if(in_array($control_name,array('primaryNo_of_children'))) {?>
                     <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_name?>' value='<?=$primary_no_of_children?>'>
                  <?php }else if(in_array($control_name,array('primaryPay_frequency'))) {?>
                     <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_name?>' value='<?=$primary_pay_frequency?>'>
                  <?php } ?>
               <?php }else if($control_type=='radio' && !in_array($control_name,$primary_benefit_arr)){ ?>
                  <div class="col-sm-4">
                     <div class="form-group">
                        <div class="btn-group btn-custom-group btn-group-justified">
                           <?php if($control_name=='primarySmoking_status'){ ?>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_y" name="<?= $control_id ?>" value="Y" <?= $primary_smoking_status == 'Y' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_y" class="btn btn-info">Smokes</label>
                              </div>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?>" type="radio" id="<?= $control_id ?>_n" name="<?= $control_id ?>" value="N" <?= $primary_smoking_status == 'N' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_n" class="btn btn-info">Non Smokes</label>
                              </div>
                              <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_id?>' value='<?=$primary_smoking_status?>'>
                           <?php }else if($control_name=='primaryTobacco_status'){ ?>
                              <div class="toggle-item">
                                 <input class="js-switch <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_y" name="<?= $control_id ?>" value="Y" <?= $primary_tobacco_status == 'Y' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_y" class="btn btn-info">Tobacco</label>
                              </div>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_n" name="<?= $control_id ?>" value="N" <?= $primary_tobacco_status == 'N' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_n" class="btn btn-info">Non Tobacco</label>
                              </div>
                              <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_id?>' value='<?=$primary_tobacco_status?>'>
                           <?php }else if($control_name=='primaryHas_spouse'){ ?>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_y" name="<?= $control_id ?>" value="Y" <?= $primary_has_spouse == 'Y' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_y" class="btn btn-info">Spouse</label>
                              </div>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_n" name="<?= $control_id ?>" value="N" <?= $primary_has_spouse == 'N' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_n" class="btn btn-info">Non Spouse</label>
                              </div>
                              <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_id?>' value='<?=$primary_has_spouse?>'>
                           <?php }else if($control_name == 'primaryEmployment_status'){ ?>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_y" name="<?= $control_id ?>" value="Y" <?= $primary_employment_status == 'Y' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_y" class="btn btn-info">Employed</label>
                              </div>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_n" name="<?= $control_id ?>" value="N" <?= $primary_employment_status == 'N' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_n" class="btn btn-info">Unemployed</label>
                              </div>
                              <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_id?>' value='<?=$primary_employment_status?>'>
                           <?php }else if($control_name == 'primaryUs_citizen'){ ?>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_y" name="<?= $control_id ?>" value="Y" <?= $primary_us_citizen == 'Y' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_y" class="btn btn-info"> U.S. Citizen</label>
                              </div>
                              <div class="toggle-item">
                                 <input class="js-switch coverage_tab_input  <?= $control_id ?> <?=$control_class?>" type="radio" id="<?= $control_id ?>_n" name="<?= $control_id ?>" value="N" <?= $primary_us_citizen == 'N' ? "checked" : ""; ?> <?=$readOnly?> data-type="<?=$control_type?>"/>
                                 <label for="<?= $control_id ?>_n" class="btn btn-info"> Not  U.S. Citizen</label>
                              </div>
                              <input type='hidden' class="hidden_coverage" data-name='primary_<?=$row['label']?>_1' name="hidden_<?=$control_name?>" id='hidden_<?=$control_id?>' value='<?=$primary_us_citizen?>'>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
               <?php }
            }
         }
      }
      ?>
   </div>
</div>
<div id="edit_hide_primary_additional" style="<?=!empty($member_rep_id) ? '' : 'display: none;'?>">
   <div class="table-responsive mdp_info_table">
      <table cellspacing="0" cellpadding="0" width="100%">
         <tbody>
            <?php if(!empty($additionalInfo)){
               $primary_benefit_arr = array('benefit_amount','in_patient_benefit','out_patient_benefit','monthly_income','benefit_percentage');
               foreach($additionalInfo as $key => $row){
                  $questionType = $row['questionType'];
                  $label = $row['label'];
                  if(in_array($row['label'],array('fname','lname','SSN','phone','city','state','zip','email','birthdate','gender','benefit_amount','in_patient_benefit','out_patient_benefit','monthly_income','benefit_percentage'))){
                     continue;
                  }
                  if($questionType == "Default"){ 
                        $table_value = "";
                        $display_label = 'primary_'.$label;
                        if($label == "weight"){
                           ${$display_label} = ${$display_label}." lbs";
                        }else if($label == "height"){
                           ${$display_label} = ${$display_label};
                        }else if($label == "employment_status"){
                           if(${$display_label} == "Y"){
                              ${$display_label} = "Employed";
                           }else if(${$display_label} == "N"){
                              ${$display_label} = "Unemployed";
                           }
                        }else if($label == "salary"){
                           ${$display_label} = displayAmount(str_replace(',','',${$display_label}));
                        }else if($label == "date_of_hire"){
                           ${$display_label} = displayDate(${$display_label});
                        }else{
                           if(${$display_label} == "Y"){
                              ${$display_label} = "Yes";
                           }else if(${$display_label} == "N"){
                              ${$display_label} = "No";
                           }
                        }
                     ?>
                        <tr>
                           <td><?= $row['display_label'] ?></td>
                           <td><?= ${$display_label} ?></td>
                        </tr>
                  <?php }
               }
            }
            ?>
         </tbody>
      </table>
   </div>
</div>
<?php } ?>

<hr>
<h4>Coverage Options <span class="fw300">(Optional)</span></h4>
<p class="m-b-20">Would this enrollee wish to consider their spouse and/or child in coverage?</p>
<div class="d-clearfix">
      <a href="javascript:void(0);" class="btn btn-info btn-outline" id="addSpouseCoverage" data-toggle="tooltip" data-container="body" data-trigger="hover" title="+ Spouse" data-placement="bottom">+ Spouse</a>
      <a href="javascript:void(0);" class="btn btn-info btn-outline" id="addChildCoverage" data-toggle="tooltip" data-container="body" data-trigger="hover" title="+ Child" data-placement="bottom">+ Child</a>
      <span id="additionalChildShow" style="display: none" class="m-l-10">
         <a href="javascript:void(0);" class="btn red-link" id="addChildCoverageButton" data-toggle="tooltip" data-container="body" data-trigger="hover" title="Additional Child(ren)" data-placement="bottom">+ Additional Child(ren) </a>
      </span>
</div>
<div id="spouseCoverageMainDiv"></div>
<div id="childCoverageMainDiv"></div>
<div class="" id="addChildCoverageButtonDiv" style="display: none"></div>

<hr class="m-t-20">
<div class="clearfix">
   <button type="button" class="btn btn-action form_submit" data-step="1">Continue</button>
   <a href="javascript:void(0);" class="btn red-link group_enrollment_cancel_button">Cancel</a>
</div>