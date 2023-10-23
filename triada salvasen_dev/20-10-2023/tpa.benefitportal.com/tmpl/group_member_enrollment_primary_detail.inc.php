<?php
	include_once dirname(__DIR__) . '/includes/connect.php';
	include_once dirname(__DIR__) .'/includes/Api.class.php';
	include_once dirname(__DIR__) .'/includes/apiUrlKey.php';

	$ajaxApiCall = new Api();

   $primaryData = isset($_POST['primary']) ? $_POST['primary'] : array();
   $product_list = !empty($_POST['product_list'])? explode(",",$_POST['product_list']):array();
   $enrolleeElementsVal = !empty($_POST['enrolleeElementsVal'])? json_decode($_POST['enrolleeElementsVal'],true):array();
   $primary_weight = '';
   if($product_list){
      foreach ($product_list as $k => $p_id) {
         if(isset($_POST['monthly_benefit_amount_'.$p_id])){
            $primaryData[$p_id]['benefit_amount'] = $_POST['monthly_benefit_amount_'.$p_id];
         }
      }
   }

   $customer_id = $_POST['customer_id'];
   $primaryMemberData = [];
   $lead_id = !empty($_POST['leadId']) ? $_POST['leadId'] : 0;
   if(!empty($lead_id)) {
      $leadId = $_POST['leadId'];
      $lead_res = $ajaxApiCall->ajaxApiCall(['api_key'=>'getLeadDetail','leadId'=>$leadId],true);
      if(!empty($lead_res)) {
         $primaryMemberData['cell_phone'] = $lead_res['cell_phone'];
         $primaryMemberData['ssn'] = $lead_res['ssn'];
         $primaryMemberData['address'] = $lead_res['address'];
         $primaryMemberData['address_2'] = $lead_res['address2'];
      }
  }

   if($customer_id > 0){
      $memberRepId = $_POST['memberId'];
      $groupIdRep = $_POST['site_user_name'];
      $primaryMember = $ajaxApiCall->ajaxApiCall(['api_key'=>'getGroupMemberDetail','memberRepId'=>$memberRepId,'groupCompany'=>$groupIdRep],true);
      $primaryMemberData = !empty($primaryMember['data']) ? $primaryMember['data'] : $primaryMemberData;
   }

   $is_ssn_exists = array_key_exists('SSN',$additionalInfo);
?>
<h2 class="m-t-0 m-b-30">Application</h2>
<h4 class="m-t-0 m-b-15">Primary Policy Holder Information</h4>
<p>Coverage Details</p>
<div class="row enrollment_auto_row">
   <div class="col-sm-4">
      <div class="form-group has-value">
         <select class="primary_select has-value" name="memberGroupCompany">
            <option data-hidden="true"></option>
            <?php if(!empty($groupCompany)){ ?>
               <?php foreach($groupCompany as $company){ ?>
                  <option value="<?php echo $company['id']?>" <?php echo checkIsset($_POST['groupCompany']) == $company['id'] ? "selected='selected'" : '' ?>><?php echo $company['name']?></option>
               <?php } ?>
            <?php } ?>
         </select>
         <label>Group Company*</label>
         <p class="error" id="error_groupCompany"></p>
      </div>
   </div>
   <?php if(array_key_exists('fname',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group has-value">
         <input type="text" class="form-control has-value" name="primary_fname" id="que_primary_fname" value="<?=explode(' ',$_POST['primaryName'])[0]?>">
         <label>First Name<?php if($additionalInfo['fname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_fname"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('lname',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group has-value">
         <input type="text" class="form-control has-value" name="primary_lname" id="que_primary_lname" value="<?=checkIsset(explode(' ',$_POST['primaryName'])[1])?>">
         <label>Last Name<?php if($additionalInfo['lname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_lname"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('phone',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control phone_mask" name="primary_phone" id="que_primary_phone" value="<?=checkIsset($primaryMemberData['cell_phone'])?>">
         <label>Phone<?php if($additionalInfo['phone']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_phone"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('email',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="phone-control-wrap">
         <div class="phone-addon text-left">
            <div class="form-group has-value">
               <input type="text" class="form-control has-value" name="primary_email" id="primary_email" value="<?=$_POST['primaryEmail']?>" <?= !empty($_POST['primaryEmail']) ? 'readonly' : '' ?>>
               <label>Email<?php if($additionalInfo['email']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
               <p class="error" id="error_primary_email"></p>
            </div>
         </div>
         <div class="phone-addon w-30">
            <div class="m-b-25">
              <a href="javascript:void(0)" id="edit_email" class="text-action icons"><i class="fa fa-edit fa-lg"></i></a>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('SSN',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control SSN_mask" name="primary_SSN" id="primary_SSN" value="<?=checkIsset($primaryMemberData['ssn'])?>">
         <label>SSN<?php if($additionalInfo['SSN']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_SSN"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('birthdate',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group has-value">
         <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <div class="pr">
               <input type="text" class="form-control date_picker dateClass has-value" name="primary_birthdate" id="primary_birthdate" value="<?=$_POST['primaryBirthdate']?>" <?=!empty($_POST['primaryBirthdate']) ? "readonly" : ""?>>
               <label>DOB (MM/DD/YYYY)<?php if($additionalInfo['birthdate']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
               <p class="error" id="error_primary_birthdate"></p>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('address1',$additionalInfo)) { ?>
   <div class="col-sm-8">
      <div class="form-group">
         <input type="text" class="form-control" id="que_primary_address1" name="primary_address1" value="<?= !empty($_POST['primaryAddress1']) ? $_POST['primaryAddress1'] : checkIsset($primaryMemberData['address']) ?>">
         <label>Address<?php if($additionalInfo['address1']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_address1"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('address2',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_address2" id="primary_address2" value="<?= !empty($_POST['primaryAddress2']) ? $_POST['primaryAddress2'] : checkIsset($primaryMemberData['address_2']) ?>">
         <label>Address 2 (suite, apt)<?php if($additionalInfo['address2']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_address2"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('city',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_city" id="primary_city" value="">
         <label>City<?php if($additionalInfo['city']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_city"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('state',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group">
         <input type="text" class="form-control" name="primary_state" id="primary_state" value="">
         <label>State<?php if($additionalInfo['state']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_state"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('zip',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group has-value">
         <input type="text" class="form-control zip has-value" name="primary_zip" id="primary_zip" value="<?=$_POST['primaryZipcode']?>" <?=!empty($_POST['primaryZipcode']) ? "readonly" : ""?>>
         <label>Zip Code<?php if($additionalInfo['zip']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
         <p class="error" id="error_primary_zip"></p>
      </div>
   </div>
   <?php } ?>
   <?php if(array_key_exists('gender',$additionalInfo)) { ?>
   <div class="col-sm-4">
      <div class="form-group">
         <div class="btn-group btn-custom-group btn-group-justified btn-group-disabled">
            <div class="toggle-item">
               <input class="js-switch primary_gender" type="radio" id="primary_gender_male1" name="primary_gender" <?= $_POST['primaryGender'] == 'Male' ? 'checked="checked"' :''; ?> value="Male" <?=  !empty($_POST['primaryGender']) ? "readonly" : "" ?> value="Y"/>
               <label for="primary_gender_male1" class="btn btn-info" <?=  !empty($_POST['primaryGender']) ? "disabled" : "" ?>>Male</label>
            </div>
            <div class="toggle-item">
               <input class="js-switch primary_gender" type="radio" id="primary_gender_female1" name="primary_gender" <?= $_POST['primaryGender'] == 'Female' ? 'checked="checked"' :''; ?> value="Female" <?=  !empty($_POST['primaryGender']) ? "readonly" : "" ?> />
               <label for="primary_gender_female1" class="btn btn-info" <?=  !empty($_POST['primaryGender']) ? "disabled" : "" ?>>Female</label>
            </div>
         </div>
         <p class="error" id="error_primary_gender"></p>
      </div>
   </div>
   <?php } ?>
</div>
<?php 
$fieldArrTmpPrimary = array('fname','lname','SSN','phone','city','state','zip','email','birthdate','gender','address1','address2');
if(!empty($additionalInfo) && array_diff(array_column($additionalInfo,'label'),$fieldArrTmpPrimary)){ ?>
<h5 class="m-t-15">Additional</h5>
<?php } ?>
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
         if($row['label'] == 'salary'){
            $control_name = "primary_annual_salary";
         }
         $control_value=isset($enrolleeElementsVal[$control_name."_1"])?$enrolleeElementsVal[$control_name."_1"]:"";
         if(empty($control_value) && !empty(${$control_name.'_value'})){
            $control_value = ${$control_name.'_value'};
         }

         if($questionType == "Default"){
            if($control_type == "text" && !in_array($control_name,$primary_benefit_arr)){
               if($row['label'] == 'salary'){
                  $control_name = "primary_salary";
               } ?>

               <div class="col-sm-6 col-md-4">
                  <div class="form-group">
                     <input type="text" maxlength="<?= $maxlength ?>" class="form-control <?= $class ?>"  required name="<?= $control_name ?>" id="<?= $control_name ?>" value="<?= $control_value ?>">
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label> 
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>      
            <?php }else if($control_type == "date_mask" && !in_array($control_name,$primary_benefit_arr)) {?>
               <?php 
                  $dateValue='';
                  if ($control_value != ''){
                     $dateValue = date('m/d/Y', strtotime($control_value));
                  }
               ?>
               <div class="col-sm-6 col-md-4">
                  <div class="form-group">
                     <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <div class="pr">
                           <input type="text" class="form-control date_picker dateClass <?= $dateValue != "" ? "has-value" : "" ?> <?= $class ?>" name="<?= $control_name ?>" id="<?= $control_name ?>" value="<?= $dateValue ?>" required  <?=  !empty($control_value) ? "readonly" : "" ?>>
                           <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                        </div>
                     </div>
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>
            <?php }else if($control_type=='select' && !in_array($control_name,$primary_benefit_arr)){ ?>
               <div class="col-sm-6 col-md-4">
                  <div class="form-group">
                  <?php if(!empty($control_value)) { ?>
                     <input type="text" id="<?= $control_name ?>" name="<?= $control_name ?>" value="<?= $control_value ?>" class="form-control primary_member_field <?= $class ?> <?=  $control_value != '' ? "has-value" : "" ?>"  <?=  !empty($control_value) ? "readonly" : "" ?>>
                  <?php }else{ ?>
                     <select class="form-control <?= $class ?>" name="<?= $control_name ?>" id="<?= $control_name ?>" required data-live-search="true">
                        <?php if(in_array($control_name,array('primary_height'))){ ?>
                           <option value=""></option>
                           <?php for($i=1; $i<=8;$i++){?>
                              <?php for($j=0; $j<=11;$j++){?>
                                 <option value="<?=$i.'.'.$j?>" <?php echo $control_value == $i.'.'.$j ? "selected='selected'" : '' ?>>
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
                              <option value="<?= $i ?>" <?= $primary_weight == $i ? "selected" : ""  ?> ><?= $i ?></option>
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
                     <?php } ?>
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>
            <?php }else if($control_type=='radio' && !in_array($control_name,$primary_benefit_arr)){ ?>
               <div class="col-sm-6 col-md-4">
                  <div class="form-group">
                     <div class="btn-group btn-custom-group btn-group-justified <?=  !empty($control_value) ? "btn-group-disabled" : "" ?>">
                        <?php if($control_name=='primary_smoking_status'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_y" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?> <?= !empty($control_value) ? "readonly" : "" ?> value="Y"/>
                              <label for="<?= $control_name ?>_y" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Smokes</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_n" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> value="N"/>
                              <label for="<?= $control_name ?>_n" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Non Smokes</label>
                           </div>
                        <?php }else if($control_name=='primary_tobacco_status'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_y" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> value="Y"/>
                              <label for="<?= $control_name ?>_y" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Tobacco</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_n" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> value="N"/>
                              <label for="<?= $control_name ?>_n" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Non Tobacco</label>
                           </div>
                        <?php }else if($control_name=='primary_has_spouse'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_y" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> value="Y"/>
                              <label for="<?= $control_name ?>_y" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Spouse</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_n" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> value="N"/>
                              <label for="<?= $control_name ?>_n" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Non Spouse</label>
                           </div>
                        <?php }else if($control_name == 'primary_employment_status'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_y" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> value="Y"/>
                              <label for="<?= $control_name ?>_y" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Employed</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_n" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> value="N"/>
                              <label for="<?= $control_name ?>_n" class="btn btn-info" <?= !empty($control_value) ? "disabled" : "" ?>>Unemployed</label>
                           </div>
                        <?php }else if($control_name == 'primary_us_citizen'){ ?>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_y" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> value="Y"/>
                              <label for="<?= $control_name ?>_y" class="btn btn-info"> U.S. Citizen</label>
                           </div>
                           <div class="toggle-item">
                              <input class="js-switch" type="radio" id="<?= $control_name ?>_n" name="<?= $control_name ?>" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> value="N"/>
                              <label for="<?= $control_name ?>_n" class="btn btn-info"> Not  U.S. Citizen</label>
                           </div>
                        <?php } ?>
                     </div>
                     <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
               </div>
            <?php } ?>
            <?php if(in_array($control_name,$primary_benefit_arr)){
               ?>
               <?php if(!empty($primaryData)) { ?>
                  <?php foreach ($primaryData as $productID => $dataArr) {
                     if(!in_array($productID,$product_list)){
                        continue;
                     } ?>
                     <?php $productName=getname('prd_main',$productID,'name','id'); ?>
                     <?php if(!empty($dataArr)) { ?>
                        <?php foreach ($dataArr as $dataArrkey => $data) { ?>
                        <?php if((isset($data[str_replace('primary_','',$control_name)]) && $data[str_replace('primary_','',$control_name)] >= 0)){ 
                           $benefitControlValue = isset($data[str_replace('primary_','',$control_name)]) ? $data[str_replace('primary_','',$control_name)] : 0;

                           if($control_name == 'salary')
                           $benefitControlValue = isset($data[str_replace('primary_','',$control_name)]) ? $data[str_replace('primary_','',$control_name)] : 0;

                           ?>
                           <div class="col-sm-6 col-md-4">
                              <div class="form-group">
                              <input type="text" name="<?=$control_name?>[<?= $productID ?>]" id="<?=$control_name?>_<?= $productID ?>" class="form-control has-value" value="<?= $benefitControlValue ?>" readonly>
                              <label><?= $productName ?> <?=ucwords(str_replace(array('primary','_'),array('',' '),$control_name))?></label>
                              <p class="error" id="error_<?=$control_name?>_<?= $productID ?>"></p>
                              </div>
                           </div>
                        <?php } ?> 
                        <?php } ?>
                     <?php } ?>
                  <?php } ?>
               <?php } ?>
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
                  <div class="form-group height_auto w-300 custom_question has-value">
                     <select class="form-control primary_select has-value" name="<?= $custom_name ?>[<?= $prd_question_id ?>]" id="<?= $control_name ?>" required>
                        <option value=""></option>
                        <?php if(!empty($resAnswer)){
                           foreach($resAnswer as $key => $value){ ?>
                              <option value="<?= $value['answer'] ?>" data-ans-eligible="<?= $value['answer_eligible'] ?>"><?= $value['answer']; ?></option>
                           <?php } ?>
                        <?php } ?>
                     </select>
                     <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                     <p class="error" id="error_<?= $custom_name ?>_<?= $prd_question_id ?>"></p>
                  </div>
               </div>
            <?php }else if($control_type=='radio'){ ?>
               <div class="col-sm-12 m-b-25">
                  <p>
                     <label><?= $label ?></label>
                  </p>
                  <div class="radio-button ">
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

      foreach ($product_list as $k => $p_id) {
         if(isset($_POST['monthly_benefit_amount_'.$p_id])){ 
            $productData = $ajaxApiCall->ajaxApiCall(['api_key'=>'getProductDetails','productList'=>array($p_id)],true);
            if($productData['status'] =='Success'){
            $productName = $productData['data'][0];
            if($productName['is_short_term_disablity_product'] == 'Y'){
            $benefit=$_POST['monthly_benefit_amount_'.$p_id]; ?>
            <div class="col-sm-3">
               <div class="form-group">
                  <input type="text" name="std_monthly_benefit[<?= $p_id ?>]" id="std_monthly_benefit_<?= $p_id ?>" class="form-control has-value" value="<?= $benefit ?>" readonly>
                  <label><?= $productName['name'] ?> Monthly Benefit</label>
                  <p class="error" id="error_std_monthly_benefit_<?= $p_id ?>"></p>
               </div>
            </div>
       <?php } } } }
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
      <div class="btn-group">
         <a href="javascript:void(0);" id="addSpouseField" class="btn btn-info btn-outline" data-toggle="tooltip" data-trigger="hover" data-container="body" title="+ Spouse" data-placement="bottom">+ Spouse</a>
         <a href="javascript:void(0);" id="addChildField" class="btn btn-info btn-outline" data-toggle="tooltip" data-trigger="hover" data-container="body" title="+ Child" data-placement="bottom">+ Child</a>
      </div>
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
      </div>
      <div class="m-b-20">
         <a href="javascript:void(0)" class="btn btn-outline btn-info" id="addPrincipalBeneficiaryField">+ Beneficiary</a>
      </div>
      <p class="error" id="error_principal_beneficiary_general"></p>
      <hr>
   </div>
   <div id="contingent_beneficiary_div" style="display: none;">
      <p class="font-bold m-b-15">Contingent Beneficiary</p>
      <p class="m-t-25">If all principal beneficiaries are disqualified or die before me, I choose the person(s) named below to be my contingent beneficiar(ies).</p>
      <p class="m-t-25 m-b-20">*The percentage awarded between all contingent beneficiary(ies) must add up to 100%</p>
      <div class="theme-form">  
      <div id="contingent_beneficiary_field_div">
      </div>
      </div>
      <div class="m-b-20">
         <a href="javascript:void(0)" class="btn btn-info btn-outline" id="addContingentBeneficiaryField">+ Contingent Beneficiary</a>
      </div>
      <p class="error" id="error_contingent_beneficiary_general"></p>
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
<hr>
<div class="clearfix">
   <button type="button" class="btn btn-action form_submit" data-step="5">Continue</button>
   <button class="enrollmentLeftmenuItem" id="self_guided_menu_back"><a href="javascript:void(0);" class="btn red-link" data-step="4">Back</a></button>
</div>