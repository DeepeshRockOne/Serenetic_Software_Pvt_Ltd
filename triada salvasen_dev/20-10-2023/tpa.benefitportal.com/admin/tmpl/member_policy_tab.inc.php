<p class="agp_md_title">Primary Plan Holder</p>
<div class="theme-form">
   <form action="" name="primary_policy_form" id="primary_policy_form">
      <input type="hidden" name="is_valid_address" id="is_valid_address" value="Y">
      <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
      <input type="hidden" name="is_address_verified" id="is_address_verified" value="<?=$row['is_address_verified']?>">
      <input type="hidden" name="is_ajax_member_form" id="is_ajax_member_form" value="1">
      <input type="hidden" name="is_update" id="is_update" value="1">
      <input type="hidden" name="customer_id" id="customer_id" value="<?=$id?>">
      <input type="hidden" name="sponsor_type" value="<?=$sponsor_type?>">
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" id="fname" name="fname" value="<?=trim($row['fname'])?>" class="form-control">
               <label>First Name</label>
               <p class="error error_fname"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" id="lname" name="lname" value="<?=trim($row['lname'])?>" class="form-control">
               <label>Last Name</label>
               <p class="error error_lname"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="email" value="<?=$row['email']?>" class="form-control no_space">
               <label>Email</label>
               <p class="error error_email"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="cell_phone" id="cell_phone" value="<?=format_telephone($row['cell_phone'])?>" class="form-control">
               <label>Phone</label>
               <p class="error error_cell_phone"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="address" id="address" value="<?=$row['address']?>" class="form-control">
               <label>Address 1</label>
               <p class="error error_address" id="error_address"></p>
               <input type="hidden" name="old_address" value="<?=$row['address']?>">
            </div>
         </div>
         <div class="col-sm-6">
             <div class="form-group">
               <input type="text" class="form-control" name="address_2" id="address_2" value="<?=$row['address_2']?>" onkeypress="return block_special_char(event)" />
               <label>Address 2 (suite, apt)</label>
               <p class="error error_address_2" id="error_address_2"></p>
             </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="text" name="city" value="<?=$row['city']?>" id="city" readonly="readonly" class="form-control">
               <label>City</label>
               <p class="error error_city"></p>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="hidden" name="old_state" value="<?=$row['state']?>" readonly='readonly'>
               <input type="text" name="state" class="form-control"  id="state" value="<?=$row['state']?>" readonly='readonly'>
               <!-- <select class="form-control" name="state" id="state">
                  <option data-hidden="true"></option>
                  <?php if (!empty($allStateRes)) {?>
                     <?php foreach ($allStateRes as $state) {?>
                     <option value="<?=$state["name"];?>" <?= $state['name'] == $row['state'] ? 'selected="selected"' : ''?>><?php echo $state['name']; ?></option>
                     <?php }?>
                  <?php }?>
               </select> -->
               <label>State</label>
               <p class="error error_state"></p>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="text" name="zip" value="<?=$row['zip']?>" maxlength="5" onkeypress="return isNumberKey(event)" id="primary_zip" class="form-control">
               <label>Zip Code</label>
               <p class="error error_primary_zip"></p>
               <input type="hidden" name="old_zip" value="<?=$row['zip']?>">
            </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="text" name="birth_date" id="birth_date" value="<?=getCustomDate($row['birth_date'])?>" class="form-control">
               <label>DOB</label>
               <p class="error error_birth_date"></p>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="phone-control-wrap">
               <div class="phone-addon">
                  <div class="form-group">
                     <input type="text" name="ssn" id="display_ssn"  readonly='readonly'  class="form-control" value="<?= secure_string_display_format($row['dssn'], 4); ?>">
                     <label id="display_ssn_label">SSN</label>
                     <input type="text" class="form-control" id="ssn" name="ssn" value="" style="display:none" />
                     <label id="ssn_label" style="display:none">SSN</label>
                     <input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value='N'/>
                     <p class="error error_ssn"></p>
                  </div>
               </div>
               <div class="phone-addon w-30">
                  <div class="m-b-25">
                     <!-- <a href="javascript:void(0);" class="text-action icons"><i class="fa fa-edit fa-lg"></i></a> -->
                     <a href="javascript:void(0)" id="edit_ssn" class="text-action icons" style="display:block">
                     <i class="fa fa-edit fa-lg"></i></a>
                     <a href="javascript:void(0)" id="cancel_ssn" class="text-action icons" style="display:none">
                     <i class="fa fa-remove fa-lg"></i></a>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <select class="form-control" name="gender" id="gender">
                  <option data-hidden="true"></option>
                  <option value="Male" <?=$row['gender'] == 'Male' ? 'selected="selected"' : '' ?>>Male</option>
                  <option value="Female" <?=$row['gender'] == 'Female' ? 'selected="selected"' : '' ?> >Female</option>
               </select>
               <label>Gender</label>
            </div>
         </div>
         <?php if($sponsor_type == 'Group') { ?>
            <div class="clearfix"></div>
            <div class="col-sm-4">
               <div class="form-group">
                  <select class="form-control" name="group_class" id="group_class">
                     <option value=""></option>
                     <?php if(!empty($resGroupClass)) { 
                        foreach($resGroupClass as $class){ ?>
                        <option value="<?= $class['id'] ?>" <?= !empty($group_classes_id) && $group_classes_id==$class['id'] ? 'selected' : '' ?>><?= $class['class_name'] ?></option>
                     <?php } } ?>
                  </select>
                  <label>Enrollee Class<em>*</em></label>
                  <p class="error error_group_class"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <select class="form-control" id="group_company_id" name="group_company_id"> 
                     <option value=""></option>
                     <option value="0" <?= $group_company_id == 0 ? 'selected' : '' ?>><?= $group_name ?></option>
                        <?php if(!empty($group_cmp_res)) { ?>
                           <?php foreach ($group_cmp_res as $key => $value) { ?>
                              <option value="<?= $value['id'] ?>" data-location="<?= $value['location'] ?>" <?= !empty($group_company_id) && $value['id'] == $group_company_id ? 'selected' : '' ?>><?= $value['name'] ?></option>
                           <?php } ?>
                        <?php } ?>
                  </select>
                  <label>Company<em>*</em></label>
                  <p class="error error_group_company_id"></p>
               </div>
            </div>
         <?php } ?>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="password" id="password" name="password" value="" class="form-control"  maxlength="20" 
										onblur="check_password(this, 'password_err','error_password', event, 'input_validation');" 
										onkeyup="check_password_Keyup(this, 'password_err','error_password', event, 'input_validation');">
               <label>Password</label>
               <div id="password_err" class="mid"><span></span></div>
               <p class="error error_password" ></p>
               <div id="pswd_info" class="pswd_popup" style="display: none">
                  <div class="pswd_popup_inner">
                  <h4>Password Requirements</h4>
                  <ul>
                     <li id="pwdLength" class="invalid"><em></em>Minimum 8 Characters</li>
                     <li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase letter </li>
                     <li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowercase letter </li>
                     <li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
                  </ul>
                  <div class="btarrow"></div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   <div class="text-right">
      <!-- <a href="address_change.php" class="address_change btn btn-action">Save</a> -->
      <button type="button"  class="btn btn-action" id="save_details">Save</button>
   </div>
   </form>
   <hr>
</div>
<?php if(strtolower($sponsor_type) == 'group') { ?>
   <p class="agp_md_title">Tax Information</p>
   <form action="" name="tax_information_form" id="tax_information_form">
      <input type="hidden" name="is_ajax_tax_form" id="is_ajax_tax_form" value="1">
      <input type="hidden" name="is_update" id="is_update_tax" value="1">
      <input type="hidden" name="lead_id" id="ai_lead_id" value="<?=$ai_lead_id?>">
      <input type="hidden" name="customer_id" value="<?=$id?>">
      <div class="theme-form">
         <div class="row lead_page">
            <div class="col-sm-6">
               <?php if($sponsor_rep_id == 'G56118' && $_SESSION['admin']['display_id'] != 'AD2904'){ ?>
                  <div class="password_unlock">
                        <div style="display:none" id="password_popup_salary">
                           <div class="phone-control-wrap">
                              <div class="phone-addon"><input type="password" class="form-control" name="password" id="showing_pass_salary"></div>
                              <div class="phone-addon w-65"><button type="button" class="btn btn-info" id="show_password_salary">Unlock</button></div>
                           </div>
                        </div>
                        <div class="form-group">
                           <div class="phone-control-wrap">
                              <div class="phone-addon">
                                    <input type="password" name="income" value="<?=base64_encode($row['income'])?>" id="income" class="form-control dot_password" readonly>
                                    <label>Annual Salary</label>
                              </div>
                              <div class="phone-addon w-25">
                                    <a href="javascript:void(0);" id="click_to_show_salary"><i class="fa fa-eye fa-lg"></i></a>
                              </div>
                           </div>
                        </div>
                  </div>
                  <input type="hidden" id="salary_encrypted" value="Y" name="salary_encrypted">
               <?php } else { ?>
                  <div class="form-group height_auto">
                     <input type="text" name="income" id="income"
                           class="form-control tax_price_field" value="<?=$row['income']?>">
                     <label>Annual Salary</label>
                     <p class="error"><span id="error_income"></span></p>
                  </div>
                  <input type="hidden" id="salary_encrypted" value="N" name="salary_encrypted">
               <?php }?>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="pre_tax_deductions_field" id="pre_tax_deductions_field"
                           class="form-control tax_price_field" value="<?=$row['pre_tax_deductions_field']?>">
                     <label>Pre Tax</label>
                     <p class="error"><span id="error_pre_tax_deductions_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="post_tax_deductions_field" id="post_tax_deductions_field"
                           class="form-control tax_price_field" value="<?=$row['post_tax_deductions_field']?>">
                     <label>Post Tax</label>
                     <p class="error"><span id="error_post_tax_deductions_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <select name="w4_filing_status_field" id="w4_filing_status_field" class="form-control">
                        <option value="" hidden selected></option>
                        <option value="Single" <?= $row['w4_filing_status_field'] == 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?= $row['w4_filing_status_field'] == 'Married' ? 'selected' : '' ?>>Married</option>
                     </select>
                     <label>Marital Status</label>
                     <p class="error"><span id="error_w4_filing_status_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <select name="w4_no_of_allowances_field" id="w4_no_of_allowances_field" class="form-control">
                        <option value="" hidden selected></option>
                        <?php for($i=1;$i<=12;$i++){ ?>
                              <option value="<?=$i?>" <?= $row['w4_no_of_allowances_field'] == $i ? 'selected' : '' ?>><?=$i?></option>
                        <?php } ?>
                     </select>
                     <label>Default Allowances</label>
                     <p class="error"><span id="error_w4_no_of_allowances_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <select name="w4_two_jobs_field" id="w4_two_jobs_field" class="form-control">
                        <option value="" hidden selected></option>
                        <option value="Yes" <?= $row['w4_two_jobs_field'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                        <option value="No" <?= $row['w4_two_jobs_field'] == 'No' ? 'selected' : '' ?>>No</option>
                     </select>
                     <label>Two Jobs?</label>
                     <p class="error"><span id="error_w4_two_jobs_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="w4_dependents_amount_field" id="w4_dependents_amount_field"
                           class="form-control tax_price_field" value="<?=$row['w4_dependents_amount_field']?>" data-value="<?=$row['w4_dependents_amount_field']?>">
                     <label>Dependents Amount</label>
                     <p class="error"><span id="error_w4_dependents_amount_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="w4_4a_other_income_field" id="w4_4a_other_income_field"
                           class="form-control tax_price_field" value="<?=$row['w4_4a_other_income_field']?>">
                     <label>Other Income</label>
                     <p class="error"><span id="error_w4_4a_other_income_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="w4_4b_deductions_field" id="w4_4b_deductions_field"
                           class="form-control tax_price_field" value="<?=$row['w4_4b_deductions_field']?>">
                     <label>4B Deduction</label>
                     <p class="error"><span id="error_w4_4b_deductions_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="w4_additional_withholding_field" id="w4_additional_withholding_field"
                           class="form-control" value="<?=$row['w4_additional_withholding_field']?>" onkeypress="return isNumber(event)">
                     <label>Additional Withholding</label>
                     <p class="error"><span id="error_w4_additional_withholding_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <select name="state_filing_status_field" id="state_filing_status_field" class="form-control">
                        <option value="" hidden selected></option>
                        <option value="Single" <?= $row['state_filing_status_field'] == 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?= $row['state_filing_status_field'] == 'Married' ? 'selected' : '' ?>>Married</option>
                     </select>
                     <label>State Filling Status</label>
                     <p class="error"><span id="error_state_filing_status_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="state_dependents_field" id="state_dependents_field"
                           class="form-control" value="<?=$row['state_dependents_field']?>" onkeypress="return isNumber(event)">
                     <label>State Dependents</label>
                     <p class="error"><span id="error_state_dependents_field"></span></p>
                  </div>
            </div>
            <div class="col-sm-6">
                  <div class="form-group height_auto">
                     <input type="text" name="state_additional_withholdings_field" id="state_additional_withholdings_field"
                           class="form-control" value="<?=$row['state_additional_withholdings_field']?>" onkeypress="return isNumber(event)">
                     <label>State Additional Withholding</label>
                     <p class="error"><span id="error_state_additional_withholdings_field"></span></p>
                  </div>
            </div>
         </div>
         <div class="text-right">
            <button type="button"  class="btn btn-action" onclick="ajaxSaveTaxDetails()">Save</button>
         </div>
      </div>
   </form>
   <hr />
<?php } ?>
<div style="display:none">
   <div class="panel panel-default mn panel-shadowless" id="address_change">
      <div class="panel-heading">
         <h4 class="mn">Address Change - <span class="fw300"><?=$row['fname'].' '.$row['lname']?></span></h4>
      </div>
      <hr class="mn">
      <div class="panel-body">
         <p class="m-b-30">There is a conflict with one or more of this members products.</p>
         <p class="m-b-30"><span class="fs18 text-action fw500" id="conflict_product"></span><br><br><strong>The Above products are not available in the state of <span id="state_span"></span></strong></p>
         <p class="m-b-30">In order to make this address change, the above product(s) must be terminated and following the termination date the address may be updated. </p>
         <div class="text-center">
         <a href="javascript:void(0)" class="btn red-link pn" onclick="parent.$.colorbox.close(); return false;">Close</a>
      </div>
      </div>
   </div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script src="<?=$HOST?>/js/password_validation.js<?=$cache?>" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
   // $(".address_change").colorbox({iframe: true, width: '585px', height: '330px'});
   checkEmail();
   $("#primary_policy_form :input").each(function(e){
      if($(this).val() !== ''){
         $(this).addClass('has-value');
      }
   })
   $("#cell_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
   $("#ssn").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
   $("#birth_date").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});

   $('#edit_ssn').click(function () {
      $(this).hide();
      $('#display_ssn').hide();
      $('#display_ssn_label').hide();
      $('#ssn').show();
      $('#ssn_label').show();
      $('#is_ssn_edit').val('Y');
      $('#cancel_ssn').show();

   });

   $('#cancel_ssn').click(function () {
      $(this).hide();
      $('#display_ssn').show();
      $('#display_ssn_label').show();
      $('#ssn').hide();
      $('#ssn_label').hide();
      $('#is_ssn_edit').val('N');
      $('#edit_ssn').show();
      $('#error_ssn').html('');
   });
   <?php /*if($SITE_ENV == 'Live') { ?>
      initAutocomplete();     
   <?php }*/ ?>
});

$(document).off('click','#save_details');
$(document).on('click','#save_details',function(e){
   e.preventDefault();
   $is_address_ajaxed = $("#is_address_ajaxed").val();
   if($is_address_ajaxed == 1){
      updateAddress();
   }else{
      ajaxSaveAccountDetails();
   }
   
});

function updateAddress(){
   $(".error").html("");
   $.ajax({
      url : "member_policy_tab.php",
      type : 'POST',
      data:$("#primary_policy_form").serialize(),
      dataType:'json',
      beforeSend :function(e){
         $("#ajax_loader").show();
      },success(res){
         $("#is_address_ajaxed").val("");
         $("#ajax_loader").hide();
         $(".suggested_address_box").uniform();
         if(res.zip_response_status =="success"){
            $("#state").val(res.state).addClass('has-value');
            $("#city").val(res.city).addClass('has-value');
            $("#is_address_verified").val('N');
            ajaxSaveAccountDetails();
         }else if(res.address_response_status =="success"){
            $(".suggestedAddressEnteredName").html($("#fname").val()+" "+$("#lname").val());
            $("#state").val(res.state).addClass('has-value');
            $("#city").val(res.city).addClass('has-value');
            $(".suggestedAddressEntered").html(res.enteredAddress);
            $(".suggestedAddressAPI").html(res.suggestedAddress);
            $("#is_valid_address").val('Y');
            $.colorbox({
                  inline:true,
                  href:'#suggestedAddressPopup',
                  height:'500px',
                  width:'650px',
                  escKey:false, 
                  overlayClose:false,
                  closeButton:false,
                  onClosed:function(){
                     $suggestedAddressRadio = $("input[name='suggestedAddressRadio']:checked"). val();
                     
                     if($suggestedAddressRadio=="Suggested"){
                        $("#address").val(res.address).addClass('has-value');
                        $("#address_2").val(res.address2).addClass('has-value');
                        $("#primary_zip").val(res.zip).addClass('has-value');
                        $("#is_address_verified").val('Y');
                     }else{
                        $("#is_address_verified").val('N');
                     }
                     ajaxSaveAccountDetails();
                  },
            });
         }else if(res.status == 'success'){
            $("#is_address_verified").val('N');
            ajaxSaveAccountDetails();
         }else{
            $.each(res.errors,function(index,error){
               $(".error_"+index).html(error).show();
           });
         }
      }
   });
}

function ajaxSaveAccountDetails(){
   $.ajax({
      url : "member_policy_tab.php",
      type : 'POST',
      data:$("#primary_policy_form").serialize(),
      dataType:'json',
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success : function(res){
        $("#ajax_loader").hide();
        $(".error").html("");
        if(res.status =='success'){
            parent.ajax_get_member_data('member_policy_tab.php','policy_tab','<?=$id?>');
            setNotifySuccess("Member Detail updated successfully.");
        }else{
           if(res.product_popup !== undefined && res.product_popup == 'product_popup' && res.products !== undefined){
               $("#conflict_product").text('');
               $("#conflict_product").html(res.products);
               $("#state_span").text($("#state").val());
               $.colorbox({
                  href:'#address_change',
                  inline:true,
                  width: '585px', 
                  height: '330px'
               });
           }
           $.each(res.errors,function(index,error){
               $(".error_"+index).html(error).show();
           });
        }
      }
   });
}

function ajaxSaveTaxDetails(){
   $.ajax({
      url : "member_policy_tab.php",
      type : 'POST',
      data:$("#tax_information_form").serialize(),
      dataType:'json',
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success : function(res){
        $("#ajax_loader").hide();
        $(".error").html("");
        if(res.status =='success'){
            setNotifySuccess("Member Tax Detail updated successfully.");
        }else{
           $.each(res.errors,function(index,error){
               $(".error_"+index).html(error).show();
           });
        }
      }
   });
}

$(document).off('click','#click_to_show_salary');
$(document).on('click','#click_to_show_salary',function(){
   if($("#income").attr('type') === 'password')
         $("#password_popup_salary").show();
   else{
         if($("#income").val() == ''){
            $("#password_popup_salary").hide();
            $("#income").attr('type','password');
            $("#income").addClass('dot_password');
            $("#income").removeClass('tax_price_field');
            $("#income").attr('readonly',true);
         }else{
            $("#password_popup_salary").hide();
            $("#income").attr('type','password');
            salaryEncryptDecrypt('encrypt');
         }
   }
});

$(document).off('click','#show_password_salary');
$(document).on('click','#show_password_salary',function(){
   $("#password_popup_salary").hide();
   if($("#showing_pass_salary").val() !== '') {
         $("#password_popup_salary").hide();
         salaryEncryptDecrypt('decrypt');
   }
});

function salaryEncryptDecrypt(type){
   $("#salary_encrypted").val("Y");
   var income = $("#income").val();
   if(type == 'decrypt'){
      $("#salary_encrypted").val("N");
   }
   var id = '<?=$ai_lead_id?>';
   if(type == 'decrypt' && income == ''){
      $("#income").priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: '',
            limit: false,
            centsLimit: 2,
      });
      $("#income").removeClass('dot_password');
      $("#income").attr('readonly',false);
      $("#income").attr('type','text');
      return false;
   }
   $("#ajax_loader").show();
   $.ajax({
      url:'member_policy_tab.php',
      method : 'POST',
      data : {id:id,change_salary:"change_salary",type:type,income:income,showing_pass:$("#showing_pass_salary").val()},
      dataType:'json',
      success:function(res){
            $("#ajax_loader").hide();
            $("#showing_pass_salary").val('');
            if(res.error != ''){
               setNotifyError(res.error);
               return false;
            }else{
               if(type == 'decrypt'){
                  $("#income").val(res.dec_income);
                  $("#income").removeClass('dot_password');
                  $("#income").priceFormat({
                        prefix: '',
                        suffix: '',
                        centsSeparator: '.',
                        thousandsSeparator: '',
                        limit: false,
                        centsLimit: 2,
                  });
                  $("#income").attr('readonly',false);
                  $("#income").attr('type','text');
               }else if(type == 'encrypt' ){
                  $("#income").val(res.enc_income);
                  $("#income").addClass('dot_password');
                  $('#income').unpriceFormat();
                  $("#income").attr('readonly',true);
               }
            }
      }
   });
}

$(".tax_price_field").priceFormat({
    prefix: '',
    suffix: '',
    centsSeparator: '.',
    thousandsSeparator: '',
    limit: false,
    centsLimit: 2,
});
function isNumber(event) {
    var charCode = (event.which) ? event.which : event.keyCode;
    // Allow only backspace, delete, left arrow and right arrow keys
    if (charCode == 8 || charCode == 37 || charCode == 39) {
        return true;
    }
    // Allow only digits from 0 to 9
    if (charCode >= 48 && charCode <= 57) {
        return true;
    }
    // Prevent any other input
    return false;
}
</script>