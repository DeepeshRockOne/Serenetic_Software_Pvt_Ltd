<div class="section-padding">
    <div class="container">
        <div class="panel panel-defualt">
            <div class="panel-body">
                <?php $manual_lead = 'active';
                    include_once 'group_lead_tabs.inc.php';
                ?>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="Individual_Manually_tab">
                        <div class="activity_wrap">
                            <form method="post" name="manual_enrollee_form"  id="manual_enrollee_form" enctype="multipart/form-data">
                                <input type="hidden" name="is_valid_address" id="is_valid_address" value="N">
                                <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
                                <div class="theme-form"> 
                                    <div class="form-group height_auto m-b-20">
                                        <p class="fw500">How will this enrollee be tagged??</p>
                                        <div class="radio-v">
                                            <label>
                                                <input type="radio" name="tag_from" value="existing"> Select Existing 
                                            </label>
                                        </div>
                                        <div class="radio-v">
                                            <label>
                                                <input type="radio" name="tag_from" value="new" > Create New
                                            </label>
                                        </div>
                                        <p class="error" id="error_tag_from"></p>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4" id="existing_tag_div" style="display: none">
                                        <div class="form-group">
                                            <select name="existing_tag" id="existing_tag" class="form-control has-value" data-live-search="true">
                                                <?php
                                                if (!empty($lead_tag_res)) {
                                                    foreach ($lead_tag_res as $key => $lead_tag_row) { ?>
                                                        <option value="<?= $lead_tag_row['tag'] ?>"><?= $lead_tag_row['tag'] ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <label>Select Tag<em>*</em></label>
                                            <p class="error" id="error_existing_tag"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="new_tag_div" style="display: none">
                                        <div class="form-group">
                                            <input type="text" name="new_tag" id="new_tag" class="form-control">
                                            <label>Enter Tag<em>*</em></label>
                                            <p class="error" id="error_new_tag"></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr>
                                        <p class="fw500">+ Enrollee</p>
                                        <div class="row m-t-15">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="enrollee_id" id="enrollee_id" class="form-control">
                                                    <label>Enrollee ID <em>*</em></label>
                                                    <p class="error" id="error_enrollee_id"></p>
                                                </div>
                                            </div>
                                            <!-- <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="annual_earnings" id="annual_earnings" class="form-control" onkeypress="return isNumberOnly(event)">
                                                    <label>Annual Earnings/Income </label>
                                                    <p class="error" id="error_annual_earnings"></p>
                                                </div>
                                            </div> -->
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <select class="form-control" id="company_id" name="company_id"> 
                                                        <option value=""></option>
                                                        <option value="0"><?= $sponsorRes['business_name'] ?></option>
                                                        <?php if(!empty($resCompany)) { ?>
                                                            <?php foreach ($resCompany as $key => $value) { ?>
                                                                <option value="<?= $value['id'] ?>" data-location="<?= $value['location'] ?>"><?= $value['name'] ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                    <label>Company<em>*</em></label>
                                                    <p class="error" id="error_company_id"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <select class="form-control" name="employee_type" id="employee_type">
                                                        <option value=""></option>
                                                        <option value="Existing">Existing</option>
                                                        <option value="New">New</option>
                                                        <option value="Renew">Renew</option>
                                                    </select>
                                                    <label>Enrollee Type <em>*</em></label>
                                                    <p class="error" id="error_employee_type"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                         <div class="input-group-addon datePickerIcon"  data-applyon="hire_date"> <i class="material-icons fs16">date_range</i> </div>
                                                         <div class="pr">
                                                            <input type="text" class="form-control dates" name="hire_date" id="hire_date">
                                                            <label>Relationship Date (MM/DD/YYYY)<em>*</em></label>
                                                         </div>
                                                    </div>
                                                    <p class="error" id="error_hire_date"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4" id="termination_date_div" style="display: none;">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                         <div class="input-group-addon datePickerIcon"  data-applyon="termination_date"> <i class="material-icons fs16">date_range</i> </div>
                                                         <div class="pr">
                                                            <input type="text" class="form-control dates" name="termination_date" id="termination_date">
                                                            <label>Enrollee Termination Date (MM/DD/YYYY)<em>*</em></label>
                                                         </div>
                                                    </div>
                                                    <p class="error" id="error_termination_date"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="fname" id="fname" class="form-control">
                                                    <label>Enrollee First Name<em>*</em></label>
                                                    <p class="error" id="error_fname"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="lname" id="lname" class="form-control">
                                                    <label>Enrollee Last Name<em>*</em></label>
                                                    <p class="error" id="error_lname"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="address" id="address" class="form-control" placeholder="">
                                                    <label>Address<em>*</em></label>
                                                    <p class="error" id="error_address"></p>
                                                    <input type="hidden" name="old_address" id="old_address" value="">
                                                </div> 
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="address_2" id="address_2" class="form-control" placeholder="" onkeypress="return block_special_char(event)">
                                                    <label>Address 2 (suite, apt)</label>
                                                    <p class="error" id="error_address_2"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="city" id="city" class="form-control">
                                                    <label>City<em>*</em></label>
                                                    <p class="error" id="error_city"></p>
                                                    <input type="hidden" name="old_city" id="old_city" value="">
                                                </div>
                                            </div>
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                    <select class="form-control" name="state" id="state">
                                                       <option data-hidden="true"></option>
                                                       <?php foreach ($allStateRes as $key => $value) { ?>
                                                          <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                       <?php } ?>
                                                    </select>
                                                    <label>State<em>*</em></label>
                                                    <p class="error" id="error_state"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="zipcode" id="zipcode" class="form-control">
                                                    <label>Zip Code<em>*</em></label>
                                                    <p class="error" id="error_zipcode"></p>
                                                    <input type="hidden" name="old_zipcode" id="old_zipcode" value="">
                                                </div> 
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <select id="gender" name="gender"  class="form-control">
                                                        <option data-hidden="true"></option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female" >Female</option>
                                                      </select>
                                                    <label>Gender<em>*</em></label>
                                                    <p class="error" id="error_gender"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                         <div class="input-group-addon datePickerIcon"  data-applyon="dob"> <i class="material-icons fs16">date_range</i> </div>
                                                         <div class="pr">
                                                            <input type="text" class="form-control dates" name="dob" id="dob">
                                                            <label>DOB (MM/DD/YYYY)<em>*</em></label>
                                                         </div>
                                                    </div>
                                                    <p class="error" id="error_dob"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="ssn" id="ssn" class="form-control ssn_mask">
                                                    <label>SSN</label>
                                                    <p class="error" id="error_ssn"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="email" class="form-control no_space">
                                                    <label>Email<em>*</em></label>
                                                    <p class="error" id="error_email"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <input type="text" name="phone" class="form-control phone_mask">
                                                    <label>Phone<em>*</em></label>
                                                    <p class="error" id="error_phone"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <select class="form-control" id="class_id" name="class_id"> 
                                                      <option value=""></option>
                                                        <?php if(!empty($resClass)) { ?>
                                                            <?php foreach ($resClass as $key => $value) { ?>
                                                                <option value="<?= $value['id'] ?>"><?= $value['class_name'] ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                    <label>Class<em>*</em></label>
                                                    <p class="error" id="error_class_id"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="fw500">Tax Information</p>
                                        <div class="row m-t-15">
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="income" id="income"
                                                        class="form-control tax_price_field" value="">
                                                    <label>Annual Salary</label>
                                                    <p class="error" id="error_income"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="pre_tax_deductions_field" id="pre_tax_deductions_field"
                                                        class="form-control tax_price_field" value="">
                                                    <label>Pre Tax</label>
                                                    <p class="error" id="error_pre_tax_deductions_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="post_tax_deductions_field" id="post_tax_deductions_field"
                                                        class="form-control tax_price_field" value="">
                                                    <label>Post Tax</label>
                                                    <p class="error" id="error_post_tax_deductions_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <select name="w4_filing_status_field" id="w4_filing_status_field" class="form-control">
                                                        <option hidden selected></option>
                                                        <option value="Single">Single</option>
                                                        <option value="Married">Married</option>
                                                    </select>
                                                    <label>Marital Status</label>
                                                    <p class="error" id="error_w4_filing_status_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <select name="w4_no_of_allowances_field" id="w4_no_of_allowances_field" class="form-control">
                                                        <option hidden selected></option>
                                                        <?php for($i=1;$i<=12;$i++){ ?>
                                                            <option value="<?=$i?>"><?=$i?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label>Default Allowances</label>
                                                    <p class="error" id="error_w4_no_of_allowances_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <select name="w4_two_jobs_field" id="w4_two_jobs_field" class="form-control">
                                                        <option hidden selected></option>
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>
                                                    <label>Two Jobs?</label>
                                                    <p class="error" id="error_w4_two_jobs_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="w4_dependents_amount_field" id="w4_dependents_amount_field"
                                                        class="form-control tax_price_field" value="">
                                                    <label>Dependents Amount</label>
                                                    <p class="error" id="error_w4_dependents_amount_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="w4_4a_other_income_field" id="w4_4a_other_income_field"
                                                        class="form-control tax_price_field" value="">
                                                    <label>Other Income</label>
                                                    <p class="error" id="error_w4_4a_other_income_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="w4_4b_deductions_field" id="w4_4b_deductions_field"
                                                        class="form-control tax_price_field" value="">
                                                    <label>4B Deduction</label>
                                                    <p class="error" id="error_w4_4b_deductions_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="w4_additional_withholding_field" id="w4_additional_withholding_field"
                                                        class="form-control" value="" onkeypress="return isNumber(event)">
                                                    <label>Additional Withholding</label>
                                                    <p class="error" id="error_w4_additional_withholding_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <select name="state_filing_status_field" id="state_filing_status_field" class="form-control">
                                                        <option hidden selected></option>
                                                        <option value="Single">Single</option>
                                                        <option value="Married">Married</option>
                                                    </select>
                                                    <label>State Filling Status</label>
                                                    <p class="error" id="error_state_filing_status_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="state_dependents_field" id="state_dependents_field"
                                                        class="form-control" value="" onkeypress="return isNumber(event)">
                                                    <label>State Dependents</label>
                                                    <p class="error" id="error_state_dependents_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group height_auto">
                                                    <input type="text" name="state_additional_withholdings_field" id="state_additional_withholdings_field"
                                                        class="form-control" value="" onkeypress="return isNumber(event)">
                                                    <label>State Additional Withholding</label>
                                                    <p class="error" id="error_state_additional_withholdings_field"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <p><strong>Select Plan Period(s)</strong></p>
                                        <div class="row">
                                           <div class="col-sm-6">
                                                <div class="form-group">
                                                    <select class="form-control" name="coverage_id" id="coverage_id">
                                                        <option value=""></option>
                                                    </select>
                                                    <label>Select Plan Period(s)</label>
                                                    <p class="error" id="error_coverage_id"></p>
                                                </div>
                                            </div> 
                                        </div>
                                        <p><strong>Plan Period(s)</strong></p>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?php if(!empty($resCoverageCheck)){ ?>
                                                    <?php foreach ($resCoverageCheck as $keyCoverahe => $valueCoverage) { ?>
                                                        <div class="m-b-15">
                                                            <label class="mn label-input"><input type="checkbox"  name="allowedCoverage[]" value="<?= $valueCoverage['id'] ?>" <?= !empty($assignCoverageArr) && in_array($valueCoverage['id'], $assignCoverageArr) ? 'checked' : ''  ?>> <?= $valueCoverage['coverage_period_name'] .' ('.date('m/d/Y',strtotime($valueCoverage['coverage_period_start'])) .' - '. date('m/d/Y',strtotime($valueCoverage['coverage_period_end'])).')'?></label>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        
                                </div>
                            </form>
                            <div class="clearfix text-center m-t-30">
                                <a href="javascript:void(0);" class="btn btn-action" id="save_enrollee">Save</a>
                                <a href="javascript:void(0);" class="btn red-link" id="cancel_enrollee">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".tax_price_field").priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: '',
            limit: false,
            centsLimit: 2,
        });
        checkEmail();
        var $site_location = '<?= $SITE_ENV ?>';

        var placeSearch, autocomplete;

        $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        $(".dates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
        $(".ssn_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
    });
    $(document).on("change","input[name=tag_from]",function(){
         $val=$(this).val();
         $("#existing_tag_div").hide();
         $("#new_tag_div").hide();
         
         if($val=='existing'){
            $("#existing_tag_div").show();
         }else{
            $("#new_tag_div").show();
         }
    });
    /*$(document).on("change","#employee_type",function(){
        $val=$(this).val();
         $("#termination_date_div").hide();
         if($val=='Renew'){
            $("#termination_date_div").show();
         }

    });*/
    $(document).on("change","#class_id",function(){
        $val=$(this).val();
        $("#ajax_loader").show();
        $.ajax({
            url:'ajax_load_leads_coverage.php',
            data:{class:$val},
            dataType:'JSON',
            type:"POST",
            success:function(res){
                $("#ajax_loader").hide();
                $("#coverage_id").html(res.html);
                $("#coverage_id").selectpicker('refresh');
            }
        });

    });
    
    $(document).on("click",".datePickerIcon",function(){
      $id=$(this).attr('data-applyon');
      $("#"+$id).datepicker('show');
      $("#"+$id).trigger("blur");
    });

    $(document).on('focus','#address,#zipcode,#city',function(){
       $('#is_address_ajaxed').val(1);
    });

    $(document).on("click","#cancel_enrollee",function(){
        window.location.href="group_enrollees.php";
    });

    $(document).on("click","#save_enrollee",function(){
        $is_address_ajaxed = $('#is_address_ajaxed').val();
        if($is_address_ajaxed == 1){
            updateAddress();
        }else{
            ajaxSaveAccountDetails();
        }
    });

    function ajaxSaveAccountDetails(){
        $(".error").html('');
        $("#ajax_loader").show();
        $.ajax({
          url:'ajax_group_add_manual_enrollee.php',
          dataType:'JSON',
          data:$("#manual_enrollee_form").serialize(),
          type:'POST',
          success:function(res){
            $("#ajax_loader").hide();
            if(res.status=="success"){
                window.location.href="group_enrollees.php";           
            }else{
              var is_error = true;
              $.each(res.errors, function (index, value) {
                $('#error_' + index).html(value).show();
                if(is_error){
                    var offset = $('#error_' + index).offset();
                    var offsetTop = offset.top;
                    var totalScroll = offsetTop - 50;
                    $('body,html').animate({scrollTop: totalScroll}, 1200);
                    is_error = false;
                }
              });
            }
          }
        });
    }

    function updateAddress(){
        $.ajax({
          url : "ajax_group_add_manual_enrollee.php",
          type : 'POST',
          data:$("#manual_enrollee_form").serialize(),
          dataType:'json',
          beforeSend :function(e){
             $("#ajax_loader").show();
             $(".error").html('');
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
                   $("#error_"+index).html(error).show();
               });
               ajaxSaveAccountDetails();
             }
             $("#state").selectpicker('refresh');
          }
       });
    }

    isNumberOnly = function(evt) {
          evt = (evt) ? evt : window.event;
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 8 && charCode != 46 && charCode != 47 && charCode != 0 && (charCode < 48 || charCode > 57)) {
              return false;
          }
          return true;
    }
    
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
  