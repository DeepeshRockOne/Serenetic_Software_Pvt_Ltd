<h2 class="m-t-0 m-b-30">Application</h2>
<h4 class="m-t-0 m-b-15">Primary Policy Holder Information</h4>
<p>Coverage Details</p>
<div id="primary_member_field_div">
</div>
<div class="row enrollment_auto_row">
   <div class="col-lg-4 col-md-6 col-sm-6">
      <div class="form-group">
         <select class="form-control" name="memberGroupCompany">
            <option data-hidden="true"></option>
            <?php if(!empty($group_class_row)){ ?>
               <?php foreach($group_class_row as $groupclass){ ?>
                  <option value="<?php echo $groupclass['id']?>" <?php echo !empty($group_company) && $group_company == $groupclass['id'] ? "selected='selected'" : '' ?>><?php echo $groupclass['name']?></option>
               <?php } ?>
            <?php } ?>
         </select>
         <label>Group Company*</label>
         <p class="error" id="error_groupCompany"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_fname" id="primary_fname">
         <label>First Name*</label>
         <p class="error" id="error_primary_fname"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_lname" id="primary_lname">
         <label>Last Name*</label>
         <p class="error" id="error_primary_lname"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control phone_mask" name="primary_phone">
         <label>Phone*</label>
         <p class="error" id="error_primary_phone"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_email" id="primary_email">
         <label>Email*</label>
         <p class="error" id="error_primary_email"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control SSN_mask" name="primary_SSN" id="primary_SSN">
         <label>SSN*</label>
         <p class="error" id="error_primary_SSN"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <div class="pr">
               <input type="text" class="form-control date_picker dateClass" name="primary_birthdate" id="primary_birthdate">
               <label>DOB (MM/DD/YYYY)*</label>
               <p class="error" id="error_primary_birthdate"></p>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="form-group">
         <input type="text" class="form-control" id="primary_address1" name="primary_address1">
         <label>Address</label>
         <p class="error" id="error_primary_address1"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_address2" id="primary_address2">
         <label>Address 2 (suite, apt)</label>
         <p class="error" id="error_primary_address2"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_city" id="primary_city">
         <label>City*</label>
         <p class="error" id="error_primary_city"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_state" id="primary_state">
         <label>State*</label>
         <p class="error" id="error_primary_state"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control zip" name="primary_zip" id="primary_zip">
         <label>Zip Code*</label>
         <p class="error" id="error_primary_zip"></p>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <div class="btn-group btn-custom-group btn-group-justified">
            <div class="toggle-item">
               <input class="js-switch"   type="radio" id="primary_gender" name="primary_gender"  value="Male" />
               <label for="radio-1" class="btn btn-info">Male</label>
            </div>
            <div class="toggle-item">
               <input class="js-switch"  type="radio" id="primary_gender" name="primary_gender" value="Female" />
               <label for="radio-2" class="btn btn-info">Female</label>
            </div>
         </div>
         <p class="error" id="error_primary_gender"></p>
      </div>
   </div>
</div>
<h5 class="m-t-15">Additional</h5>
<div class="row enrollment_auto_row">
   <?php if(!empty($additionalInfo)){
      $primary_benefit_arr = array('primary_benefit_amount','primary_in_patient_benefit','primary_out_patient_benefit','primary_monthly_income','primary_benefit_percentage');
      foreach($additionalInfo as $key => $row){
         $prd_question_id = $row['id'];
         $questionType = $row['questionType'];
         $control_type = $row['control_type'];
         $control_name = "primary_".$row['label'];
         $class = $row['control_class'];
         $maxlength = $row['control_maxlength'];
         $label = $row['display_label'];
         $is_required= $row['required'];

         if(in_array($row['label'],array('fname','lname','SSN','phone','city','state','zip','email','birthdate','gender','address1','address2','SSN'))){
            continue;
         }
         
         if($questionType == "Default"){
            if($control_type == "text" && !in_array($control_name,$primary_benefit_arr)){ ?>

               <div class="col-sm-4">
                  <div class="form-group">
                     <input type="text" maxlength="<?= $maxlength ?>" class="form-control <?= $class ?>"  required name="<?= $control_name ?>" id="<?= $control_name ?>" value="">
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label> 
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>      
            <?php }else if($control_type == "date_mask" && !in_array($control_name,$primary_benefit_arr)) {?>
               <div class="col-sm-4">
                  <div class="form-group">
                     <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <div class="pr">
                           <input type="text" class="form-control date_picker dateClass <?= $class ?>" name="<?= $control_name ?>" id="<?= $control_name ?>" value="">
                           <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                        </div>
                     </div>
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>
            <?php }else if($control_type=='select' && !in_array($control_name,$primary_benefit_arr)){ ?>
               <div class="col-sm-4">
                  <div class="form-group">
                     <select class="form-control <?= $class ?>" name="<?= $control_name ?>" id="<?= $control_name ?>" required data-live-search="true">
                        <?php if(in_array($control_name,array('primary_height'))){ ?>
                           <option value=""></option>
                           <?php for($i=1; $i<=8;$i++){?>
                              <?php for($j=0; $j<=11;$j++){?>
                                 <option value="<?=$i.'.'.$j?>" <?= !empty($height_feet) && !empty($height_inch) && $i ==  $height_feet && $j == $height_inch ? "" : "" ?>>
                                    <?php
                                       echo $i.' Ft. ';
                                       if($j>0){
                                          echo $j.' In. ';
                                       }  
                                    ?>
                                 </option>
                              <?php }?>
                           <?php }?>
                        <?php }else if(in_array($control_name,array('primary_weight'))){ ?>
                           <option value=""></option>
                           <?php for($i=1; $i<=1000;$i++){?>
                              <option value="<?= $i ?>" <?= $primary_weight == $i ? "" : ""  ?> ><?= $i ?></option>
                           <?php }?>
                        <?php }else if(in_array($control_name,array('primary_no_of_children'))){ ?>
                           <option value=""></option>
                           <?php for($i=1; $i<=15;$i++){?>
                              <option value="<?=$i?>"><?= $i ?></option>
                           <?php }?>
                        <?php }else if(in_array($control_name,array('primary_pay_frequency'))){ ?>
                           <option value=""></option>
                           <option value="Annual">Annual</option>
                           <option value="Monthly">Monthly</option>
                           <option value="Semi-Monthly">Semi-Monthly</option>
                           <option value="Semi-Weekly">Semi-Weekly</option>
                           <option value="Weekly">Weekly</option>
                           <option value="Hourly">Hourly</option>
                        <?php } ?>
                     </select>
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>
            <?php }else if($control_type=='radio' && !in_array($control_name,$primary_benefit_arr)){ ?>
               <div class="col-sm-4">
                  <div class="form-group">
                     <div class="btn-group btn-custom-group btn-group-justified">
                        <?php if($control_name=='primary_smoking_status'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_y" name="<?= $control_name ?>" value="Y"/>
                              <label for="<?= $control_name ?>_y" class="btn btn-info">Smokes</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_n" name="<?= $control_name ?>" value="N"/>
                              <label for="<?= $control_name ?>_n" class="btn btn-info">Non Smokes</label>
                           </div>
                        <?php }else if($control_name=='primary_tobacco_status'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="Y"/>
                              <label for="primary_tobacco_y" class="btn btn-info">Tobacco</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="N"/>
                              <label for="primary_tobacco_n" class="btn btn-info">Non Tobacco</label>
                           </div>
                        <?php }else if($control_name=='primary_has_spouse'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="Y"/>
                              <label for="primary_spouse_y" class="btn btn-info">Spouse</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="N"/>
                              <label for="primary_spouse_n" class="btn btn-info">Non Spouse</label>
                           </div>
                        <?php }else if($control_name == 'primary_employment_status'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="Y"/>
                              <label for="primary_employed_y" class="btn btn-info">Employed</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="N"/>
                              <label for="primary_employed_n" class="btn btn-info">Unemployed</label>
                           </div>
                        <?php }else if($control_name == 'primary_us_citizen'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="Y"/>
                              <label for="primary_UScitizen_y" class="btn btn-info"> U.S. Citizen</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>" name="<?= $control_name ?>" value="N"/>
                              <label for="primary_UScitizen_n" class="btn btn-info"> Not  U.S. Citizen</label>
                           </div>
                        <?php } ?>
                     </div>
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>
            <?php } ?>
            <?php if(in_array($control_name,$primary_benefit_arr)){ ?>

            <?php } ?>
         <?php }else{
            $custom_name = str_replace($prd_question_id,"", $control_name);
            $resAnswer = $ajaxApiCall->ajaxApiCall(['api_key'=>'customeQuestionAnswer','questionId'=>$prd_question_id],true);
         ?>
            <div class="clearfix"></div>
            <?php if($control_type == 'select'){ ?>
               <div class="col-sm-12">
                  <p>
                     <label><?= $label ?></label>
                  </p>
                  <div class="form-group height_auto w-300 custom_question">
                     <select class="form-control primary_select" name="<?= $custom_name ?>[<?= $prd_question_id ?>]" id="<?= $control_name ?>" required>
                        <option value=""></option>
                        <?php if(!empty($resAnswer)){
                           foreach($resAnswer as $key => $value){ ?>
                              <option value="<?= $value['answer'] ?>" data-ans-eligible="<?= $value['answer_eligible'] ?>"><?= $value['answer']; ?></option>
                           <?php } ?>
                        <?php } ?>
                     </select>
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                  </div>
                  <p class="error" id="error_<?= $custom_name ?>_<?= $prd_question_id ?>"></p>
               </div>
            <?php }else if($control_type=='radio'){ ?>
               <div class="col-sm-12 m-b-25">
                  <p>
                     <label><?= $label ?></label>
                  </p>
                  <div class="radio-button">
                     <div class="btn-group colors  custom-question-btn" data-toggle="buttons">
                     <?php if(!empty($resAnswer)){
                       foreach ($resAnswer as $key => $value) { ?>
                           <label class="btn btn-info">
                              <input type="radio" name="<?= $custom_name ?>[<?= $prd_question_id ?>]" value="<?= $value['answer'] ?>" data-ans-eligible="<?= $value['answer_eligible'] ?>" class="js-switch primary_member_field" autocomplete="false"> <?= $value['answer'] ?>
                             </label>
                       <?php } ?>
                     <?php } ?>
                   </div>
                 </div>
                 <p class="error" id="error_<?= $custom_name ?>_<?= $prd_question_id ?>"></p>
               </div>
            <?php }elseif($control_type == 'select_multiple'){ ?>
               <div class="col-sm-12">
                  <p>
                     <?= $label ?>
                  </p>
                  <div class="form-group height_auto w-300 custom_question">
                     <select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $prd_question_id ?>][]" class="se_multiple_select primary_multiple_select primary_member_field" required multiple="multiple">
                     <?php if(!empty($resAnswer)){
                       foreach ($resAnswer as $key => $value) { ?>
                           <option value="<?= $value['answer'] ?>" data-ans-eligible="<?= $value['answer_eligible'] ?>"><?= $value['answer'] ?></option>
                        <?php } ?>
                     <?php } ?>
                     </select>
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                     <p class="error" id="error_<?= $custom_name ?>_<?= $prd_question_id ?>"></p>
                  </div>
               </div>
            <?php }else if($control_type=='textarea'){ ?>
               <div class="col-sm-12 form-inline m-b-25">
                  <p>
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                  </p>
                  <textarea id="<?= $control_name ?>" class="form-control" name="<?= $custom_name ?>[<?= $prd_question_id ?>]"  rows="3" cols="50" maxlength="300"></textarea>
                  <p class="error" id="error_<?= $custom_name ?>_<?= $prd_question_id ?>"></p>
               </div>
            <?php } ?>
         <?php }
      }
   }
   ?>
</div>

<div id="dependent_field_div" style="display:none;">
   <input type="hidden" name="dependent_field_number" id="dependent_field_number" value="">
   <input type="hidden" name="child_products_list" id="child_products_list" value="">
   <input type="hidden" name="spouse_products_list" id="spouse_products_list" value="">
<h4>Dependent Information </h4>
<p class="m-b-20">Would this enrollee wish to consider their spouse and/or child in coverage?</p>
<div class="clearfix">
   <a href="javascript:void(0);" id="addSpouseField" class="btn btn-info btn-outline" data-toggle="tooltip" data-container="body" title="+ Spouse" data-placement="bottom">+ Spouse</a>
   <a href="javascript:void(0);" id="addChildField" class="btn btn-info btn-outline" data-toggle="tooltip" data-container="body" title="+ Child" data-placement="bottom">+ Child</a>
</div>
   <div id="dependent_spouse_main_div"></div>
   <div id="dependent_child_main_div"></div>
   <p class="error" id="error_dependent_general"></p>
</div>
<!-- Beneficiary Code Start -->
<div id="beneficiary_information_div" style="display: none;">
   <input type="hidden" name="principal_beneficiary_field_number" id="principal_beneficiary_field_number" value="">
   <input type="hidden" name="contingent_beneficiary_field_number" id="contingent_beneficiary_field_number" value="">
   <input type="hidden" name="is_principal_beneficiary" id="is_principal_beneficiary" value="">
   <input type="hidden" name="is_contingent_beneficiary" id="is_contingent_beneficiary" value="">
   <h4 class="m-b-25 m-t-25">Beneficiary Information</h4>

   <div id="principal_beneficiary_div" style="display: none;">
      <p class="font-bold m-b-15">Principal Beneficiary</p>
      <p class="m-b-25">I choose the person(s) named below to be the principal beneficiary(ies) of the Life Insurance benefits that may be payable at the time of my death. If any principal beneficiary(ies) is disqualified or dies before me, his/her percentage of this benefit will be paid to the remaining principal beneficiary(ies).</p>
      <p class="m-b-25">*The percentage awarded between all principal beneficiary(ies) must add up to 100%</p>
      <div class="theme-form">  
      <div id="principal_beneficiary_field_div">
      </div>
      <p class="error" id="error_principal_beneficiary_general"></p>
      </div>
      <a href="javascript:void(0)" class="btn btn-outline btn-info" id="addPrincipalBeneficiaryField">+ Beneficiary</a>
      <hr>
   </div>
   <div id="contingent_beneficiary_div" style="display: none;">
      <p class="font-bold m-b-15">Contingent Beneficiary</p>
      <p class="m-t-25">If all principal beneficiaries are disqualified or die before me, I choose the person(s) named below to be my contingent beneficiar(ies).</p>
      <p class="m-t-25">*The percentage awarded between all contingent beneficiary(ies) must add up to 100%</p>
      <div class="theme-form">  
      <div id="contingent_beneficiary_field_div">
      </div>
      <p class="error" id="error_contingent_beneficiary_general"></p>
      </div>
      <a href="javascript:void(0)" class="btn btn-info btn-outline m-t-25" id="addContingentBeneficiaryField">+ Contingent Beneficiary</a>
   </div>
</div>
<!-- Beneficiary Code Ends -->
<!-- <p><span class="font-bold">Spouse</span> <a href="javascript:void(0);" class="btn red-link" data-toggle="tooltip" data-container="body" title="Remove" data-placement="bottom">Remove</a></p> -->
<div class="row enrollment_auto_row" style="display:none;">
   <div class="col-sm-4">
      <div class="form-group">
         <select class="form-control">
            <option data-hidden="true"></option>
            <option>Dental Elite 1500 </option>
         </select>
         <label>Assign Product(s)*</label>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="">
         <label>Spouse First Name</label>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="">
         <label>Spouse Last Name</label>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               <div class="input-group">
                  <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                  <div class="pr">
                     <input type="text" class="form-control" name="">
                     <label>DOB (MM/DD/YYYY)*</label>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <div class="form-group">
                  <input type="text" class="form-control" name="">
                  <label>SSN*</label>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <div class="btn-group btn-custom-group btn-group-justified">
            <div class="toggle-item">
               <input class="js-switch"   type="radio" id="radio-1" name="switch-1"  value="male" />
               <label for="radio-1" class="btn btn-info">Male</label>
            </div>
            <div class="toggle-item">
               <input class="js-switch"  type="radio" id="radio-2" name="switch-1" value="female" />
               <label for="radio-2" class="btn btn-info">Female</label>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <div class="btn-group btn-custom-group btn-group-justified">
            <div class="toggle-item">
               <input class="js-switch"   type="radio" id="radio-3" name="switch-1"  value="male" />
               <label for="radio-3" class="btn btn-info">Tobacco</label>
            </div>
            <div class="toggle-item">
               <input class="js-switch"  type="radio" id="radio-4" name="switch-1" value="female" />
               <label for="radio-4" class="btn btn-info">Non Tobacco</label>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="clearfix" style="display:none;">
   <a href="javascript:void(0);" class="btn btn-action form_submit" data-step="4">Continue</a>
   <a href="group_prd_preview.php" class="btn red-link group_enrollment_cancel_button">Cancel</a>
</div>