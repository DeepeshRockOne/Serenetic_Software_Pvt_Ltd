<p class="agp_md_title">Primary Plan Holder</p>
<div class="theme-form">
   <form action="" name="primary_policy_form" id="primary_policy_form">
      <input type="hidden" name="has_full_access" id="has_full_access" value="<?=$has_full_access == false?0:1?>">
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
               <input type="text" id="fname" name="fname" value="<?=$row['fname']?>" class="form-control">
               <label>First Name</label>
               <p class="error error_fname"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" id="lname" name="lname" value="<?=$row['lname']?>" class="form-control">
               <label>Last Name</label>
               <p class="error error_lname"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="email" value="<?=$row['email']?>" class="form-control no_space" <?=$has_full_access == false?"readonly":""?>>
               <label>Email</label>
               <p class="error error_email"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="cell_phone" id="cell_phone" value="<?=format_telephone($row['cell_phone'])?>" class="form-control" <?=$has_full_access == false?"readonly":""?>>
               <label>Phone</label>
               <p class="error error_cell_phone"></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="address" id="address" value="<?=$row['address']?>" class="form-control" <?=$has_full_access == false?"readonly":""?>>
               <label>Address 1</label>
               <p class="error error_address" id="error_address"></p>
               <input type="hidden" name="old_address" value="<?=$row['address']?>">
            </div>
         </div>
         <div class="col-sm-6">
             <div class="form-group">
               <input type="text" class="form-control" name="address_2" id="address_2" value="<?=$row['address_2']?>" onkeypress="return block_special_char(event)" <?=$has_full_access == false?"readonly":""?> />
               <label>Address 2 (suite, apt)</label>
               <p class="error error_address_2" id="error_address_2"></p>
             </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="text" name="city" value="<?=$row['city']?>" id="city" class="form-control" <?=$has_full_access == false?"readonly":""?>>
               <label>City</label>
               <p class="error error_city"></p>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="hidden" name="old_state" value="<?=$row['state']?>" readonly='readonly'>
               <input type="text" name="state" class="form-control"  id="state" value="<?=$row['state']?>" readonly='readonly'>
               <label>State</label>
               <p class="error error_state"></p>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="text" name="zip" value="<?=$row['zip']?>" readonly='readonly' maxlength="5" onkeypress="return isNumberKey(event)" id="primary_zip" class="form-control">
               <label>Zip Code</label>
               <p class="error error_primary_zip"></p>
               <input type="hidden" name="old_zip" value="<?=$row['zip']?>">
            </div>
         </div>
         <div class="col-sm-4">
            <div class="form-group">
               <input type="text" name="birth_date" id="birth_date" value="<?=getCustomDate($row['birth_date'])?>" class="form-control" <?=$has_full_access == false?"readonly":""?>>
               <label>DOB</label>
               <p class="error error_birth_date"></p>
            </div>
         </div>
         <div class="col-sm-4">
            <div class="phone-control-wrap">
               <div class="phone-addon">
                  <div class="form-group">
                     <input type="text" name="ssn" id="display_ssn"  readonly='readonly'  class="form-control" value="<?= secure_string_display_format($row['dssn'], 4); ?>">
                     <label>SSN</label>
                     <input type="text" class="form-control" id="ssn" name="ssn" value="" style="display:none" />
                     <input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value='N'/>
                     <p class="error error_ssn"></p>
                  </div>
               </div>
               <div class="phone-addon w-30"  <?=$has_full_access == false?"style='display:none;'":""?>>
                  <div class="m-b-25">
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
               <?php if($has_full_access == true) { ?>
               <select class="form-control" name="gender" id="gender">
                  <option data-hidden="true"></option>
                  <option value="Male" <?=$row['gender'] == 'Male' ? 'selected="selected"' : '' ?>>Male</option>
                  <option value="Female" <?=$row['gender'] == 'Female' ? 'selected="selected"' : '' ?> >Female</option>
               </select>
               <?php } else { ?>
               <input type="text" name="gender" class="form-control"  id="gender" value="<?=$row['gender']?>" readonly='readonly'>
               <?php } ?>
               <label>Gender</label>
            </div>
         </div>
         <?php if($sponsor_type == 'Group') { ?>
            <div class="clearfix"></div>
            <div class="col-sm-4">
               <div class="form-group">
                  <?php /*<select class="form-control" name="group_class" id="group_class" <?=$has_full_access == true ? '' : 'disabled="disabled"' ?>>
                     <option value=""></option>
                     <?php if(!empty($resGroupClass)) { 
                        foreach($resGroupClass as $class){ ?>
                        <option value="<?= $class['id'] ?>" <?= !empty($group_classes_id) && $group_classes_id==$class['id'] ? 'selected' : '' ?>><?= $class['class_name'] ?></option>
                     <?php } } ?>
                  </select> */ ?>
                  <input type="text" class="form-control" name="group_class" value="<?=$className?>" id="group_class" disabled="disabled">
                  <label>Enrollee Class<em>*</em></label>
                  <p class="error error_group_class"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <?php /*<select class="form-control" id="group_company_id" name="group_company_id" <?=$has_full_access == true ? '' : 'disabled="disabled"' ?>> 
                     <option value=""></option>
                     <option value="0" <?= $group_company_id == 0 ? 'selected' : '' ?>><?= $group_name ?></option>
                        <?php if(!empty($group_cmp_res)) { ?>
                           <?php foreach ($group_cmp_res as $key => $value) { ?>
                              <option value="<?= $value['id'] ?>" data-location="<?= $value['location'] ?>" <?= !empty($group_company_id) && $value['id'] == $group_company_id ? 'selected' : '' ?>><?= $value['name'] ?></option>
                           <?php } ?>
                        <?php } ?>
                  </select> */ ?>
                  <input type="text" class="form-control" name="group_company_id" value="<?=$group_name?>" id="group_company_id" disabled="disabled">
                  <label>Company<em>*</em></label>
                  <p class="error error_group_company_id"></p>
               </div>
            </div>
         <?php } ?>
         <div class="col-sm-4">
            <div class="form-group">
               <?php if($has_full_access == true) { ?>
               <input type="password" id="password" name="password" value="" class="form-control"  maxlength="20" 
                              onblur="check_password(this, 'password_err','error_password', event, 'input_validation');" 
                              onkeyup="check_password_Keyup(this, 'password_err','error_password', event, 'input_validation');" <?=$has_full_access == false?"readonly":""?>>
               <?php } else { ?>
               <input type="password" name="password" value="" class="form-control" readonly='readonly'>
               <?php } ?>
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
      <button type="button"  class="btn btn-action" id="save_details" <?=$has_full_access == false?"disabled":""?>>Save</button>
   </div>
   </form>
   <hr>
</div>
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
   $("#birth_date").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
   $("#ssn").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});

   $('#edit_ssn').click(function () {
      $(this).hide();
      $('#display_ssn').hide();
      $('#ssn').show();
      $('#is_ssn_edit').val('Y');
      $('#cancel_ssn').show();

   });

   $('#cancel_ssn').click(function () {
      $(this).hide();
      $('#display_ssn').show();
      $('#ssn').hide();
      $('#is_ssn_edit').val('N');
      $('#edit_ssn').show();
      $('#error_ssn').html('');
   });

});

$(document).off('click','#save_details');
$(document).on('click','#save_details',function(e){
   e.preventDefault();
   disableButton($(this));
   $is_address_ajaxed = $("#is_address_ajaxed").val();
   if($is_address_ajaxed == 1){
      updateAddress();
   }else{
      ajaxSaveAccountDetails();
   }
});

function updateAddress(){
   
   $.ajax({
      url : "member_policy_tab.php",
      type : 'POST',
      data:$("#primary_policy_form").serialize(),
      dataType:'json',
      beforeSend :function(e){
         $("#ajax_loader").show();
         $(".error").html('');
      },success(res){
         enableButton($("#save_details"));
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
        enableButton($("#save_details"));
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
</script>