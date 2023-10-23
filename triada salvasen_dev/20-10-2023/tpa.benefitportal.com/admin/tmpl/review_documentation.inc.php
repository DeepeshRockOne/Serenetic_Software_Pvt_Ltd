<div class="panel panel-default panel-block review_doc_panel">
  <div class="panel-heading text-center ">
    <div class="panel-title ">
      <p class="mn fs26 text-black lato_font fw600">REVIEW DOCUMENTATION - <span class="fw300"><?=ucfirst($row['fname'])." ".ucfirst($row['lname']) ?></span> </p>
    </div>
  </div>
  <div class="panel-body">
  <form id="self_enrollment_second" action="ajax_review_documentation.php" method="post">
      <input type="hidden" name="agent_id" value="<?=md5($agent_id)?>">
      <input type="hidden" name="contract_status" value="Approved">
      <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
      <input type="hidden" name="is_agency_address_ajaxed" id="is_agency_address_ajaxed" value="">
      <input type="hidden" name="is_valid_address" id="is_valid_address" value="Y">
      <p class="agp_md_title m-t-10">Account Type</p>
        <div class="clearfix m-b-20">
          <div class="input-question">
            <label class="radio-inline">What type of account is this?<em>*</em></label>
            <label class="radio-inline">
              <input type="radio" name="account_type" id="business" value="Business" class="account_type" <?=checkIsset($row['account_type']) == 'Business' ? 'checked="checked"' : ''?>>
              Agency</label>
            <label class="radio-inline">
              <input type="radio" name="account_type" id="personal" value="Personal" class="account_type" <?=checkIsset($row['account_type']) == 'Personal' ? 'checked="checked"' : ''?>>
              Agent</label>
          </div>
        </div>
        <div class="theme-form">
          <div class="row" id="BusinessDiv" <?=checkIsset($row['account_type']) != 'Business' ? 'style="display:none"' : ''?>>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="business_name" id="business_name" class="form-control" value="<?=checkIsset($row['company_name'])?>" />
                <label>Agency Legal Name</label>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="business_address" id="business_address" class="form-control" value="<?=checkIsset($row['company_address'])?>" />
                <label>Address</label>
                <input type="hidden" name="old_business_address" id="old_business_address" value="<?=checkIsset($row['company_address'])?>">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                  <input type="text" class="form-control" name="business_address2" id="business_address2" value="<?=$row['company_address_2']?>" onkeypress="return block_special_char(event)" />
                  <label>Address 2 (suite, apt)</label>
                  <p class="error"><span id="error_business_address2"></span></p>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="business_city" id="business_city" class="form-control" value="<?=checkIsset($row['company_city'])?>" />
                <label>City</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control" name="business_state" id="business_state">
                  <option value="" disabled selected hidden> </option>
                  <?php if (!empty($allStateRes)) {?>
                    <?php foreach ($allStateRes as $state) {?>
                    <option <?=$state["name"] == checkIsset($row['company_state']) ? 'selected' : ''?> value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
                    <?php }?>
                  <?php }?>
                </select>
                <label>State</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="business_zipcode" id="business_zipcode" maxlength="5" class="form-control" value="<?=checkIsset($row['company_zip'])?>" />
                <label>Zip Code</label>
                <input type="hidden" name="old_business_zipcode" id="old_business_zipcode" value="<?=checkIsset($row['company_zip'])?>">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="business_taxid" id="business_taxid" value="<?=checkIsset($row['tax_id'])?>" />
                <label>Business Tax ID (EIN)</label>
              </div>
            </div>
          </div>
          <p class="agp_md_title">Principal Agent</p>
          <div class="row" id="PersonalDiv">
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="fname" id="fname" class="form-control" value="<?=checkIsset($row['fname'])?>" />
                <label>First Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="lname" id="lname" class="form-control" value="<?=checkIsset($row['lname'])?>" />
                <label>Last Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="address" id="address" class="form-control" value="<?=checkIsset($row['address'])?>" />
                <label>Address</label>
                <input type="hidden" name="old_address" id="old_address" value="<?=checkIsset($row['address'])?>">
              </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                <input type="text" class="form-control" id="address_2" name="address_2" value="<?=$row['address_2']?>" onkeypress="return block_special_char(event)" />
                <label>Address 2 (Suite, Apt)</label>
                <p class="error"><span id="error_address_2"></span></p>
                </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="city" id="city" class="form-control" value="<?=checkIsset($row['city'])?>" />
                <label>City</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control"  name="state" id="state">
                  <option value="" disabled selected hidden> </option>
                  <?php if (!empty($allStateRes)) {?>
                    <?php foreach ($allStateRes as $state) {?>
                    <option <?=$state["name"] == checkIsset($row['state']) ? 'selected' : ''?> value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
                    <?php }?>
                  <?php }?>
                </select>
                <label>State</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="zipcode" id="zipcode" value="<?=checkIsset($row['zip'])?>" />
                <label>Zip Code</label>
                <input type="hidden" name="old_zipcode" id="old_zipcode" value="<?=checkIsset($row['zip'])?>">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="dob" id="dob" value="<?=(isset($row["birth_date"]) && $row["birth_date"] != "" && $row["birth_date"] != "0000-00-00") ? date("m/d/Y", strtotime($row["birth_date"])) : "" ?>" />
                <label>DOB (MM/DD/YYYY)</label>
              </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                  <div class="phone-control-wrap">
                    <div class="phone-addon">
                      <div class="form-group">
                        <input class="form-control" id="display_ssn" readonly='readonly' value="<?= secure_string_display_format($row['dssn'], 4); ?>">
                        <label>SSN</label>
                        <input type="text" class="form-control" id="ssn" name="ssn" value="" style="display:none" />
                        <input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value='N'/>
                      </div>
                    </div>
                    <div class="phone-addon w-30 ssn_error">
                      <div class="m-b-25">
                        <a href="javascript:void(0)" id="edit_ssn" class="text-action icons" style="display:block"><i
                        class="fa fa-edit fa-lg"></i></a>
                        <a href="javascript:void(0)" id="cancel_ssn" class="text-action icons" style="display:none">
                        <i class="fa fa-remove fa-lg"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <p class="agp_md_title">License Information</p>
          <div class="row">
            <div class="col-sm-5">
              <div class="form-group">
                <input type="text" class="form-control" name="npn_number" id="npn_number" value="<?=checkIsset($row['npn'])?>" />
                <label>NPN Number</label>
              </div>
            </div>
          </div>
          <?php include_once __DIR__ . '/../agent_license.php'; ?>
          <div class="m-t-20"></div>
          <div class="row" id="add_license_div">
          <div class="col-sm-12">
              <div class="pull-left"><a href="javascript:void(0);" class="btn btn-info add_more_license" id="add_more_license">+ License</a></div>
          </div>
          <div class="clearfix m-t-5"></div>
          <p class="agp_md_title m-t-20">Error and Ommissions Insurance (E&O) </p>
          <div class="row ">
            <?php if(in_array($row['agent_coded_level'],array("LOA"))){?>
              <div class="form-group height_auto m-l-10">
                <label  class="label-input">  My E&O insurance is covered under my parent agent &nbsp;
                  <input type="checkbox" <?=checkIsset($resDoc['by_parent']) == "Y" ? "checked" : ""?> name="e_o_by_parent" value="Y" id="e_o_by_parent">
                </label>
              </div>
            <?php }?>
            <input type="radio" name="e_o_coverage" checked value="Y" id="e_o_yes" style="display: none">
            <div id="e_o_information" style="<?=checkIsset($resDoc['by_parent']) == 'Y' ? 'display:none;' : ''?>">
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" class="form-control" name="e_o_amount" id="e_o_amount" value="<?=checkIsset($resDoc['e_o_amount'])?>" />
                  <label>E&O Amount (Minimum of $1million)<em>*</em></label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" class="form-control" name="e_o_expiration" id="e_o_expiration" value="<?=(!empty($resDoc["e_o_expiration"]) && $resDoc["e_o_expiration"] != "0000-00-00") ? date("m/d/Y", strtotime($resDoc["e_o_expiration"])) : ""?>" />
                  <label>E&O Expiration (MM/DD/YYYY) <em>*</em></label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group cdrop_wrapper">
                  <div class="phone-control-wrap">
                    <div class="phone-addon">
                      <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload</span>
                        <input type="file" class="gui-file" accept="application/pdf" id="e_o_document" name="e_o_document">
                        <input type="text" class="gui-input" placeholder="<?=$resDoc['e_o_document']?>">
                        <label>Upload E&O</label>
                      </div>
                    </div>
                    <?php if (checkIsset($resDoc['e_o_document']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($resDoc['e_o_document']))) {?>
                      <a href="<?php echo $AGENT_DOC_WEB . $resDoc['e_o_document']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
                    <?php }?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php if(!in_array($row['agent_coded_level'],array("LOA"))){?>
            <p class="agp_md_title">Commissions Bank Account</p>
            <div id="pearsonalAccountDiv" class="m-b-15">
                <div class="personal_err">
                  <div class="input-question">
                    <label>Account Type<em>*</em></label>
                    <label  class="radio-inline m-l-15">
                      <input type="radio" <?=checkIsset($resDirect["account_type"]) == "checking" ? "checked" : ""?> name="bank_account_type" value="checking" id="p_checking" class="form-control">
                      Checking</label>
                    <label class="radio-inline">
                      <input type="radio" <?=checkIsset($resDirect["account_type"]) == "savings" ? "checked" : ""?> name="bank_account_type" value="savings" id="p_savings" class="form-control">
                      Savings </label>
                  </div>    
                </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="bankname" id="bankname" class="form-control" value="<?=checkIsset($resDirect['bank_name'])?>" />
                  <label>Bank Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text"  name="bank_rounting_number" maxlength="9" id="bank_rounting_number" class="form-control" value="<?=checkIsset($resDirect['routing_number'])?>" oninput="isValidNumber(this)" />
                  <label>Bank Routing Number</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div style="<?=!empty($resDirect['account_number']) ? '' : 'display:none'?>">
                  <label>Account Number <span id="ach_billing_detail"><?= !empty($resDirect['account_number']) ? "(*" . substr($resDirect['account_number'],-4) . ")" : '' ?></span></label>
                </div>
                <input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=checkIsset($resDirect['account_number'])?>" maxlength='50' class="form-control">
                <div class="form-group">
                  <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" value="" oninput="isValidNumber(this)" maxlength="17" />
                  <label>Bank Account Number</label>
                </div>
              </div>
              <!-- <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="bank_number_confirm" id="bank_number_confirm" class="form-control" value="" />
                  <label>Confirm Bank Account Number</label>
                </div>
              </div> -->
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <div class="phone-control-wrap">
                    <div class="phone-addon">
                      <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload W9</span>
                        <input type="file" class="gui-file" accept="application/pdf" id="w9_form_business" name="w9_form_business">
                        <input type="text" class="gui-input" placeholder="<?=$row['w9_pdf']?>">
                        <label>Upload W9</label>
                      </div>
                    </div>
                    <?php if (checkIsset($row['w9_pdf']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($row['w9_pdf']))) {?>
                      <!-- <div class="phone-addon" style="width:35px;"> <i class="fa fa-download fs20 text-red"></i> </div> -->
                      <a href="<?php echo $AGENT_DOC_WEB . $row['w9_pdf']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
                    <?php }?>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
          <div class="text-center m-t-15 m-b-10">
            <a href="javascript:void(0);" class="btn btn-info" id="contract_approved">Approve</a>
            <a href="review_doc_rejected.php?agent_id=<?=$row['eid']?>&contract_status=Pending Resubmission" class="review_doc_rejected btn btn-action">Reject</a>
            <a href="agent_detail_v1.php?id=<?=$_GET['id']?>" class="btn red-link">Back</a>
          </div>
        </div>
      </div>
  </form>
</div>

<div class="license_template license_tempmdsm  " style="display: none">	
  <div class="license_portion pr div_license_~i~ "> 
    <div class="row seven-cols">
      <input type="hidden" name='hdn_license[~i~]' value="~i~" id='hdn_license_~i~'>
      <input type="hidden" name="edit[~i~]" value="~i~" id="ed_license__~i~" class="ed_license__~i~">
      <div class="col-md-1">
        <div class="form-group ">
          <select name="license_state[~i~]" id="license_state_~i~" class="selected_license_states license_state select_class_~i~"  data-id="~i~" >
            <option value="" ></option>
            <?php if (!empty($allStateRes)) {?>
              <?php foreach ($allStateRes as $state) {?>
              <option value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
              <?php }?>
            <?php }?>
          </select>
          <label>License State<em>*</em></label>
          <p class="error"><span id="error_license_state_~i~" class="error_license_state"></span></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group  ">        
          <input name="license_number[~i~]" id="license_number_~i~" type="text" class="form-control license_number"   value="">
          <label for="license_number[~i~]">License Number<em>*</em></label>
          <p class="error"><span id="error_license_number_~i~" class="error_license_number"></span></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <input type="text" name="license_active_date[~i~]" id="license_active_date_~i~" class="form-control license_active" />
            <label for="license_active_date_~i~">License Active Date<em>*</em></label>
            <p class="error"><span id="error_license_active_date_~i~" class="error_license_active_date"></span>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group height_auto m-b-10" id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
          <input name="license_expiry[~i~]" id="license_expiry_~i~" type="text" class="form-control license_expiry"  value="">
          <label for="license_expiry[~i~]">License Expiration<em>*</em></label>
          <p class="error"><span id="error_license_expiry_~i~" class="error_license_expiry"></span></p>
           <div class="clearfix m-t-5">
           <label for="license_not_expire[~i~]" class="text-red mn fs12">
            <input type="checkbox" name="license_not_expire[~i~]" id="license_not_expire_~i~" class="license_not_expire" data-id="~i~" value="Y">
          License does not expire</label>
        </div>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <select name="license_type[~i~]" id="license_type_~i~" class="license_types select_class_~i~" data-id="~i~">
                <option value="" disabled selected hidden> </option>
                  <option value="Business">Agency</option>
                  <option value="Personal">Agent</option>
              </select>
              <label for="license_type~i~">License Type<em>*</em></label>
              <p class="error"><span id="error_license_type_~i~" class="error_license_type"></span></p>
          </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <select name="licsense_authority[~i~]" id="licsense_authority_~i~" class="licsense_authority select_class_~i~" data-id="~i~">
                <option value="" disabled selected hidden> </option>
                  <option value="Health">Health</option>
                  <option value="Life">Life</option>
                  <option value="general_lines">General Lines (Both)</option>
              </select>
              <label for="licsense_authority~i~">License of Authority<em>*</em></label>
              <p class="error"><span id="error_licsense_authority_~i~" class="error_licsense_authority"></span></p>
          </div>
      </div>
      <div class="col-md-1">
        <div class="form-group height_auto">
        <!--<a href="javascript:void(0)" class="edit_license btn red-link" style="display:none" id="edit_license_~i~" data-id="~i~" > Edit </a>
        <div class="form-group " id="hidden_btn_~i~"> -->
          <button type="button" class="btn btn-primary ajax_add_license" data-id="~i~">Save</button>
          <a href="javascript:void(0)" class="remove_license btn red-link"  data-id="~i~"> Delete </a>
        <!--</div>-->
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>  
  <div class="clearfix"></div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $("#e_o_expiration").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        startDate: new Date()
    });
  
  $('.review_doc_rejected').colorbox({iframe: true, width: '550px', height: '470px'});

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

$(document).on('focus','#address,#zipcode',function(){
   $("#is_address_ajaxed").val(1);
});
$(document).on('focus','#business_address,#business_zipcode',function(){
   $("#is_agency_address_ajaxed").val(1);
});

$(document).off('click','#contract_approved');
$(document).on('click','#contract_approved',function(e){
    $is_address_ajaxed = $("#is_address_ajaxed").val();
    $is_agency_address_ajaxed = $("#is_agency_address_ajaxed").val();
    if($is_address_ajaxed == 1){
        updateAddress();
    }else if($is_agency_address_ajaxed == 1){
        updateAddress();
    }else{
        ajaxSaveAccountDetails();
    }
});

function updateAddress(){
  $.ajax({
      url : "ajax_review_documentation.php",
      type : 'POST',
      data:$("#self_enrollment_second").serialize(),
      dataType:'json',
      beforeSend :function(e){
         $("#ajax_loader").show();
         $(".error").html('');
      },success(res){
         $("#is_address_ajaxed").val("");
         if(res.agencyApi=="success"){
            $("#is_agency_address_ajaxed").val(1);
         }else{
            $("#is_agency_address_ajaxed").val("");
         }
         $("#ajax_loader").hide();
         $(".suggested_address_box").uniform();
         if(res.zip_response_status =="success"){
            // $("#is_address_verified").val('N');
            if(res.agencyApi=="success"){
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                updateAddress();
            }else if(res.agencyApi==""){
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                ajaxSaveAccountDetails();
            }else{
                $("#business_state").val(res.state).addClass('has-value');
                $("#business_city").val(res.city).addClass('has-value');
                ajaxSaveAccountDetails();
            }
         }else if(res.address_response_status =="success"){
            if(res.agencyApi=="success"){
                $(".suggestedAddressEnteredName").html($("#fname").val()+" "+$("#lname").val());
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
            }else if(res.agencyApi==""){
                $(".suggestedAddressEnteredName").html($("#fname").val()+" "+$("#lname").val());
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
            }else{
                $(".suggestedAddressEnteredName").html($("#business_name").val());
                $("#business_state").val(res.state).addClass('has-value');
                $("#business_city").val(res.city).addClass('has-value');
            }
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
                        if(res.agencyApi=="success"){
                            $("#address").val(res.address).addClass('has-value');
                            $("#address_2").val(res.address2).addClass('has-value');
                        }else if(res.agencyApi==""){
                            $("#address").val(res.address).addClass('has-value');
                            $("#address_2").val(res.address2).addClass('has-value');
                        }else{
                            $("#business_address").val(res.address).addClass('has-value');
                            $("#business_address2").val(res.address2).addClass('has-value');
                        }
                        // $("#is_address_verified").val('Y');
                     }
                     if(res.agencyApi=="success"){
                        updateAddress();
                     }else{
                        ajaxSaveAccountDetails();
                     }
                  },
            });
         }else if(res.status == 'success'){
            if(res.agencyApi=="success"){
                updateAddress();
            }else{
                ajaxSaveAccountDetails();
            }
         }else{
            $setup = false;
                $(".error").hide();
                $.each(res.errors, function(key, val) {
                    error = '<div id="' + key + '-error" class="error error_preview">' + val + '</div>';
                    if ($setup == false) {
                        //tab-pane
                        $id = $("[name='" + key + "']").parents(".tab-pane").attr("id");
                        if (key.indexOf("license_number") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("license_state") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("license_expiry") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("physical_license") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        }

                        $("[href='#" + $id + "']").trigger("click");
                        if ($("[name='" + key + "']").length > 0) {
                            $('html, body').animate({
                                scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                            }, 1000);
                        }
                        $setup = true;
                    }

                    $("p.error").show();
                    $('#error_' + key).parent("p.error").show();
                    // $('#error_' + key).html(value).show();
                    if (key.indexOf("license_number") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();
                    } else if (key.indexOf("license_state") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else if (key.indexOf("license_expiry") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else if (key.indexOf("license_active_date") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else if (key.indexOf("license_type") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    }else if (key.indexOf("licsense_authority") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else {
                        $("[name='" + key + "']:first").next('label').after(error);
                        if(key === "fname"){
                          $("[name='" + key + "']:last").next('label').after(error);
                        }
                        if(key === "w9_form_business" || key==="e_o_document"){
                            $("[name='"+key+"']:first").parents('.cdrop_wrapper').after(error);
                        }
                        if(key === "state" || key === "business_state"){
                            $("[name='"+key+"']:first").parents('.fixedCustom').next('label').after(error);
                        }
                        if(key === "bank_account_type"){
                            $("[name='"+key+"']:first").parents("#pearsonalAccountDiv .personal_err").after(error);
                        }
                        if(key === "signature_data"){
                            $("[name='"+key+"']:first").parents("#error_signature-pad").siblings('#signature-pad').after(error);
                            $("#signature_data-error").css('margin-left','20%');
                        }
                        if(key === 'check_agree'){
                            $("[name='" + key + "']:first").parents('.check_agree_div').after(error);
                        }
                        if(key === 'ssn'){
                          $(".ssn_error").after(error);
                        }
                    }
                });
         }
         $("#state").selectpicker('refresh');
         $("#business_state").selectpicker('refresh');
      }
   });
}

function ajaxSaveAccountDetails(){
  formHandler($("#self_enrollment_second"),
        function() {
            $("#ajax_loader").show();
        },
        function(data) {
            $("#ajax_loader").hide();
            if (data.ibo_conf_summary && data.ibo_conf_summary != '') {
                $('#basic_details').html(data.ibo_conf_summary);
                $(".con_popup").click();
            } else if (data.status == 'account_approved') {
                if (data.step == "second") {
                    if (typeof data.w9_pdf != "undefined") {
                        $(".span_w9_pdf").html(getDocumentLink(data.w9_pdf));
                    }
                    if (typeof data.e_o_document != "undefined") {
                        $(".span_e_o_document").html(getDocumentLink(data.e_o_document));
                    }
                    if (typeof data.license_doc != "undefined") {
                        $.each(data.license_doc, function(key, val) {
                            $("#hdn_physical_license_" + key).val(val.doc_id);
                            $("#span_physical_license_" + key).html(getDocumentLink(val.doc_link)).show();
                        });
                    }
                    setNotifySuccess("You have successfully approved agent <?=$row['fname']." ".$row['lname']?>",true);
                    window.location = '<?=$ADMIN_HOST?>/agent_detail_v1.php?id=<?=$row['eid']?>';
                }
            } else if (data.status == "session_fail") {
                setNotifyError("Oops... Something went wrong please try again later");
            } else {
                $setup = false;
                $(".error").hide();
                $.each(data.errors, function(key, val) {
                    error = '<div id="' + key + '-error" class="error error_preview">' + val + '</div>';
                    if ($setup == false) {
                        //tab-pane
                        $id = $("[name='" + key + "']").parents(".tab-pane").attr("id");
                        if (key.indexOf("license_number") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("license_state") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("license_expiry") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("physical_license") != -1) {
                            $id = $(".error_" + key).parents(".tab-pane").attr("id");
                        }

                        $("[href='#" + $id + "']").trigger("click");
                        if ($("[name='" + key + "']").length > 0) {
                            $('html, body').animate({
                                scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                            }, 1000);
                        }
                        $setup = true;
                    }

                    $("p.error").show();
                    $('#error_' + key).parent("p.error").show();
                    // $('#error_' + key).html(value).show();
                    if (key.indexOf("license_number") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();
                    } else if (key.indexOf("license_state") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else if (key.indexOf("license_expiry") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else if (key.indexOf("license_active_date") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else if (key.indexOf("license_type") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    }else if (key.indexOf("licsense_authority") != -1) {
                        $(".error_" + key).html(error).show();
                        $("#error_" + key).html(error).show();;
                    } else {
                        $("[name='" + key + "']:first").next('label').after(error);
                        if(key === "fname"){
                          $("[name='" + key + "']:last").next('label').after(error);
                        }
                        if(key === "w9_form_business" || key==="e_o_document"){
                            $("[name='"+key+"']:first").parents('.cdrop_wrapper').after(error);
                        }
                        if(key === "state" || key === "business_state"){
                            $("[name='"+key+"']:first").parents('.fixedCustom').next('label').after(error);
                        }
                        if(key === "bank_account_type"){
                            $("[name='"+key+"']:first").parents("#pearsonalAccountDiv .personal_err").after(error);
                        }
                        if(key === "signature_data"){
                            $("[name='"+key+"']:first").parents("#error_signature-pad").siblings('#signature-pad').after(error);
                            $("#signature_data-error").css('margin-left','20%');
                        }
                        if(key === 'check_agree'){
                            $("[name='" + key + "']:first").parents('.check_agree_div').after(error);
                        }
                        if(key === 'ssn'){
                          $(".ssn_error").after(error);
                        }
                    }
                });
            }
        });
}

$(function() {
  refreshCurrencyFormatter();
  $("#phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
  $("#accnt_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
  $("#admin_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
  $("#dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
  $("#e_o_expiration").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
  $("#zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
  $("#accnt_zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
  $("#business_zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
  $("#ssn").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
  $("#business_taxid").inputmask({"mask": "99-9999999",'showMaskOnHover': false});

});
function refreshCurrencyFormatter(){
  $("#e_o_amount").formatCurrency({
    colorize: true,
    negativeFormat: '-%s%n',
    roundToDecimalPlace: 0
  });
}

$('#e_o_amount').blur(function() {
        refreshCurrencyFormatter();
      })
      .keyup(function(e) {
        var e = window.event || e;
        var keyUnicode = e.charCode || e.keyCode;
        if (e !== undefined) {
          switch (keyUnicode) {
            case 16:
              break; // Shift
            case 17:
              break; // Ctrl
            case 18:
              break; // Alt
            case 27:
              this.value = '';
              break; // Esc: clear entry
            case 35:
              break; // End
            case 36:
              break; // Home
            case 37:
              break; // cursor left
            case 38:
              break; // cursor up
            case 39:
              break; // cursor right
            case 40:
              break; // cursor down
            case 78:
              break; // N (Opera 9.63+ maps the "." from the number key section to the "N" key too!) (See: http://unixpapa.com/js/key.html search for ". Del")
            case 110:
              break; // . number block (Opera 9.63+ maps the "." from the number block to the "N" key (78) !!!)
            case 190:
              break; // .
            default:
              $(this).formatCurrency({
                colorize: true,
                negativeFormat: '-%s%n',
                roundToDecimalPlace: -1,
                eventOnDecimalsEntered: true
              });
          }
        }
      })
      /*.bind('decimalsEntered', function(e, cents) {
        if (String(cents).length > 2) {
          var errorMsg = 'Please do not enter any cents (0.' + cents + ')';
          $('#formatWhileTypingAndWarnOnDecimalsEnteredNotification2').html(errorMsg);
          log('Event on decimals entered: ' + errorMsg);
        }
      })*/;

    $(document).on("change", ".account_type", function() {
      $value = $(this).val();
      $("#PersonalDiv").show();
      if ($value == "Personal") {
          $("#PersonalDiv").addClass("removeLines")
          $("#BusinessDiv").hide("slow");
          $("#personal[value='Personal']").trigger("click");
      } else if ($value == "Business") {
          $("#PersonalDiv").removeClass("removeLines")
          $("#BusinessDiv").show("slow");
          $("#business[value='Business']").trigger("click");
      }
    });

    $(function(){
      $("#e_o_by_parent").trigger("change");
    });
    trigger("#e_o_by_parent",function($this,e){
      if($this.prop('checked')){
        $("#eoDiv").find("em").hide();
        $("#e_o_information").slideUp();
      }else{
        $("#eoDiv").find("em").show();
        $("#e_o_information").slideDown();
      }
    },"change");

</script>