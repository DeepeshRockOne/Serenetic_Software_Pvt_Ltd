<script type="text/javascript">
//******************** Form Submit tab Code start *******************************
 var wrapper;
 var clearButton;
 var savePNGButton;
 var saveSVGButton;
 var canvas;
 var signaturePad;

 var autoSaveInt = setInterval(setCustomInterval,60000);
    $(document).ready(function(){
        checkEmail();
    });
    $('#enrollment_form').ajaxForm({
        beforeSend: function () {
            $("#ajax_loader").show();
        },
        dataType: 'json',
        success: function (data) {                
            $("#ajax_loader").hide();
            enableButton($(".btn"));
            $("#ajax_loader").hide();
            $(document).find("p.last_updated").html("<i>Last saved "+data.updated_at+"</i>");
            if (data.status == 'account_approved') {
                
                if(data.step != "first"){
                    var licenseLength = $("#agent_licenses").children(".license_portion").length;
                    if(licenseLength > 0){ 
                        $(".license_state").selectpicker('destroy');
                        agentLicenseRefresh();
                    }
                }

                if (data.step == "first") {
                    if (data.is_draft == 0 && data.next_step=='third') {
                        $('#fTabStep').addClass('disabled');
                        $('[href="#thirdstep"]').trigger('click');
                        $('#firststep').hide();
                    }else if (data.is_draft == 0) {
                        $('[href="#secondstep"]').trigger('click');
                    } else {
                        setNotifySuccess("Documents and details information saved successfully!");
                    }
                } else if (data.step == "second") {
                    if (typeof data.w9_pdf != "undefined") {
                        $(".span_w9_pdf").html(getDocumentLink(data.w9_pdf));
                    }
                    if (typeof data.e_o_document != "undefined") {
                        $(".span_e_o_document").html(getDocumentLink(data.e_o_document));
                    }
                    if (data.is_draft == 0 && data.submit_type!='next') {
                        $("#bap1").show();
                        $.colorbox({
                            inline: true,
                            width: "525px",
                            height: "200px",
                            overlayClose: false,
                            fixed:true,
                            closeButton: false,
                            href: "#bap1"
                        });
                        clearInterval(autoSaveInt);
                    } else {
                        setNotifySuccess("Documents and details information saved successfully!");
                    }
                } else if (data.step == "third") {
                    clearInterval(autoSaveInt);
                     swal({
                        text: '<br>Success: Application Complete',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Next'
                      }).then(function() {
                        window.location.href = $AGENT_HOST + '/dashboard.php';
                    }, function(dismiss) {
                        window.location.href = $AGENT_HOST + '/dashboard.php';
                    });
                }
            } else if (data.status == "session_fail") {
                setNotifyError("Oops... Something went wrong please try again later");
            } else {
                $(".error").hide();
                if (typeof data.errors.final_contract_error != "undefined") {
                    setNotifyError("Sorry! " + data.errors.final_contract_error);
                }
                var $setup = false;
                var is_error = true;
                $.each(data.errors, function(index, error) {
                    $('#error_' + index).html(error).show();
                    if (is_error) {
                        var offset = $('#error_' + index).offset();
                        if(typeof(offset) === "undefined"){
                            console.log("Not found : "+index);
                        }else{
                            scrollToElement($('#error_' + index));
                            is_error = false;
                        }
                    }
                    if ($setup == false) {
                        $id = $("[name='" + index + "']").parents(".tab-pane").attr("id");
                        if($id === 'firststep'){
                            $("#firststep").show();
                        }else if($id === 'secondstep'){
                            $("#secondstep").show();
                        }
                        $("[href='#" + $id + "']").trigger("click");
                        $setup = true;
                    }
                });
            }
        },
        error: function () {
            alert('Due to some technical error file couldn\'t uploaded.');
        }
    });
//******************** Form Submit tab Code end   *******************************

function updateAddress(){
   
   $.ajax({
      url : "ajax_agent_remaining_contract_v1.php",
      type : 'POST',
      data:$("#enrollment_form").serialize(),
      dataType:'json',
      beforeSend :function(e){
         $("#ajax_loader").show();
         $(".error").html('');
      },success(res){
         enableButton($(".btn"));
         $("#is_address_ajaxed").val("");
         if(res.agencyApi=="success"){
            $("#is_agency_address_ajaxed").val(1);
         }else{
            $("#is_agency_address_ajaxed").val("");
         }
         $("#ajax_loader").hide();
         $(".suggested_address_box").uniform();
         if(res.zip_response_status =="success"){
            $("#is_address_verified").val('N');
            if(res.agencyApi=="success"){
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                updateAddress();
            }else if(res.agencyApi==""){
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                $("#enrollment_form").submit();
            }else{
                $("#business_state").val(res.state).addClass('has-value');
                $("#business_city").val(res.city).addClass('has-value');
                $("#enrollment_form").submit();
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
                        $("#is_address_verified").val('Y');
                     }else{
                        $("#is_address_verified").val('N');
                     }
                     if(res.agencyApi=="success"){
                        updateAddress();
                     }else{
                        $("#enrollment_form").submit();
                     }
                  },
            });
         }else if(res.status == 'success'){
            $("#is_address_verified").val('N');
            if(res.agencyApi=="success"){
                updateAddress();
            }else{
                $("#enrollment_form").submit();
            }
         }else{
            $.each(res.errors,function(index,error){
               $("#error_"+index).html(error).show();
           });
         }
         $("#state").selectpicker('refresh');
         $("#business_state").selectpicker('refresh');
      }
   });
}

scrollToElement = function(e) {
    add_scroll = 0;
    element_id = $(e).attr('id');
    var offset = $(e).offset();
    var offsetTop = offset.top;
    var totalScroll = offsetTop - 200 + add_scroll;
    $('body,html').animate({
        scrollTop: totalScroll
    }, 1200);
}

getDocumentLink = function($link) {
    return '<a href="' + $link + '" title="View Document" target="_blank"><span class="fa fa-paperclip"></a>';
};

$(document).on('focus',"#address,#zipcode",function(){
    $("#is_address_ajaxed").val(1);
});

$(document).on('focus',"#business_address,#business_zipcode",function(){
    $("#is_agency_address_ajaxed").val(1);
});

//Button click code end
$(document).off("click",".btn-draft-saver");
$(document).on("click",".btn-draft-saver",function(){
    disableButton($(this));
    $step = $(".data_tab li.active a").attr('data-step');
    $("#is_draft").val(1);
    $("#dataStep").val($step);
    $("#enrollment_form").submit();
});

$(document).off("click","#btn_finish");
$(document).on("click","#btn_finish",function(){
    disableButton($(this));
    $("#is_draft").val(0);
    $step = $(".data_tab li.active a").attr('data-step');
    $("#dataStep").val($step);
    $("#submit_type").val('submit');
    $is_address_ajaxed = $("#is_address_ajaxed").val();
    $is_agency_address_ajaxed = $("#is_agency_address_ajaxed").val();
    if($is_address_ajaxed == 1){
        updateAddress();  
    }else if($is_agency_address_ajaxed == 1){
        updateAddress();
    }else{
        $("#enrollment_form").submit();
    }
});

$(document).off("click","#btn_next");
$(document).on("click","#btn_next",function(){
    disableButton($(this));
    $step = $(".data_tab li.active a").attr('data-step');
    $("#dataStep").val($step);
    $("#submit_type").val('next');
    $("#is_draft").val(0);
    $("#enrollment_form").submit();
});

$(document).off("click","#final_step");
$(document).on("click","#final_step",function(){
    $step = $(".data_tab li.active a").attr('data-step');
    $("#dataStep").val($step);
    if($step==3){
        if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
          $("#hdn_signature_data").val(signaturePad.toDataURL());
        }
     }
    disableButton($(this));
    $("#is_draft").val(0);
    $("#enrollment_form").submit();
});

$(document).off("change", ".e_o_coverage");
$(document).on("change", ".e_o_coverage", function() {
    $value = $(this).val();
    if ($value == "Y") {
        $("#eoDiv").show();
    } else {
        $("#eoDiv").hide();
    }
});

$(document).off("change", ".account_type");
$(document).on("change", ".account_type", function(e) {
    e.preventDefault();
    $value = $(this).val();
    $("#PersonalDiv").show();
    if ($value == "Personal") {
        $("#PersonalDiv").addClass("removeLines")
        $("#BusinessDiv").hide("slow");
        $(".all_data").show("slow");
        $("#personal[value='Personal']").trigger("click");
        $("[data-personal]").show("slow");
        $("[data-business]").hide("slow");
    } else if ($value == "Business") {
        $("#PersonalDiv").removeClass("removeLines")
        $("#BusinessDiv").show("slow");
        $(".all_data").show("slow");
        $("#business[value='Business']").trigger("click");
        $("[data-personal]").hide("slow");
        $("[data-business]").show("slow");
    }
});
//Button click code end

//Unique Username start
$(document).on('focusin click keyup', '#username', function() {
    $('#username_info').show();
    var user_name = $(this).val();
    var user_email = $('#emails').val();
    var agent_id = $("#agent_id").val();
    var pattern = new RegExp('^[0-9a-zA-Z]+$');
    if (user_name.match(pattern)) {
        $('#alpha').removeClass('invalid').addClass('valid');
    } else {
        $('#alpha').removeClass('valid').addClass('invalid');
    }
    if (user_name.length < 4 || user_name.length > 20) {
        $('#ulength').removeClass('valid').addClass('invalid');
    } else {
        $('#ulength').removeClass('invalid').addClass('valid');
    }
    if (user_name.match(pattern) && user_name.length > 3 && user_name.length < 21) {
        $.ajax({
            url: $AGENT_HOST + "/check_already_username.php",
            data: {
                username: user_name,
                email: user_email,
                agent_id: agent_id
            },
            dataType: 'json',
            type: 'post',
            success: function(res) {
                if (!res) {
                    $('#unique').removeClass('valid').addClass('invalid');
                } else if (res) {
                    $('#unique').removeClass('invalid').addClass('valid');
                }
            }
        });
    } else {
        $('#unique').removeClass('valid').addClass('invalid');
    }
}).on('blur', '#username', function() {
    id = $(this).attr('id');
    $('#username_info').hide();
    if ($.trim($(this).val()) == "") {
        $('#error_' + id).html('Web Alias is required');
        $("#" + id + '_err').removeClass('rightmark wrongmark wrongmark_red');
        $("#" + id + '_err').addClass('wrongmark_red');
        return false;
    } else {
        if ($(this).val().length < 4) {
            $('#error_' + id).html('Minimum 4 character(s) required');
            $("#" + id + '_err').removeClass('rightmark wrongmark wrongmark_red');
            $("#" + id + '_err').addClass('wrongmark_red');
            return false;
        } else if ($(this).val().length > 20) {
            $('#error_' + id).html('Maximum 20 character(s) allow');
            $("#" + id + '_err').removeClass('rightmark wrongmark wrongmark_red');
            $("#" + id + '_err').addClass('wrongmark_red');
            return false;
        } else {
            $('#error_' + id).html('');
            //checkUsername($(this).val(), $('#email').val(), id);
            $("#pws_username_url").html($(this).val());
        }
    }
});
//Unique Username End

//google map api for address start
 function initAutocomplete() {
    var input = document.getElementById('address');
    var options = {
        types: ['geocode'],
        componentRestrictions: {country: 'us'}
    };
    autocomplete = new google.maps.places.Autocomplete(input, options);
    autocomplete.setFields(['address_component']);
    autocomplete.addListener('place_changed', fillInAddress);
 } 
 function initAutocompleteAgency() {
    var input = document.getElementById('business_address');
    var options = {
        types: ['geocode'],
        componentRestrictions: {country: 'us'}
    };
    autocomplete_Agency = new google.maps.places.Autocomplete(input, options);
    autocomplete_Agency.setFields(['address_component']);
    autocomplete_Agency.addListener('place_changed', fillInAddressAgency);
 }
 function fillInAddress() {
    $("#is_valid_address").val('N');
    var place = autocomplete.getPlace();
    var address = "";
    var zip = "";
    var city = "";
    var state = "";
    // console.log(place);
    /* var defaultZip = $("#enrollment_form #zip").val(); */
    $(".error").html('');
    for (var i = 0; i < place.address_components.length; i++) {
        var addressType = place.address_components[i].types[0];
        if(addressType == "street_number"){
        var val = place.address_components[i]["short_name"];
            address = address + " "+ val;
        }else if(addressType=="route"){
        var val = place.address_components[i]["long_name"];
        address = address + " "+ val;
        }else if(addressType=="postal_code"){
        zip = place.address_components[i]["short_name"];
        }else if(addressType=="locality"){
        city = place.address_components[i]["short_name"];
        }else if(addressType == "administrative_area_level_1"){
        state = place.address_components[i]["long_name"];
        }
    }
        $("#enrollment_form #zipcode").val(zip);
        $("#enrollment_form #address").val(address);
        $("#enrollment_form #city").val(city);
        $("#enrollment_form #state").val(state).change();
        /* $("#is_valid_address").val('Y'); */
 }
 function fillInAddressAgency() {
    $("#is_valid_address").val('N');
    var place = autocomplete_Agency.getPlace();
    var address = "";
    var zip = "";
    var city = "";
    var state = "";
    /* var defaultZip = $("#enrollment_form #business_zipcode").val(); */
    $(".error").html('');
    for (var i = 0; i < place.address_components.length; i++) {
        var addressType = place.address_components[i].types[0];
        if(addressType == "street_number"){
        var val = place.address_components[i]["short_name"];
            address = address + " "+ val;
        }else if(addressType=="route"){
        var val = place.address_components[i]["long_name"];
        address = address + " "+ val;
        }else if(addressType=="postal_code"){
        zip = place.address_components[i]["short_name"];
        }else if(addressType=="locality"){
        city = place.address_components[i]["short_name"];
        }else if(addressType == "administrative_area_level_1"){
        state = place.address_components[i]["long_name"];
        }
    }
        $("#enrollment_form #business_zipcode").val(zip);
        $("#enrollment_form #business_address").val(address);
        $("#enrollment_form #business_city").val(city);
        $("#enrollment_form #business_state").val(state).change();
        /* $("#is_valid_address").val('Y'); */

 }
 $("#enrollment_form #address").unbind();
//google map api for address end

//******** Agent License Code Start ******************* 
 trigger(".add_more_license",function($this,e){
    index = parseInt($(".license_portion").length);
    $display_counter = parseInt($('#license_display_counter').val());
    $number=index+1;
    if($display_counter > index){
    $number = $display_counter + 1;
    }
    pos_number = $number;
    $("#agent_licenses").append($(".license_template").html().replace(/~i~/g,pos_number));
    $("#license_display_counter").val($number);
    
    $('.select_class_'+pos_number).addClass('form-control');
    $('.select_class_'+pos_number).selectpicker({ 
        container: 'body', 
        style:'btn-select',
        noneSelectedText: '',
        dropupAuto:false,
        });
   $(".div_license_"+pos_number+" :input").selectpicker('refresh');
    refreshLicenseDatePicker();
    $("input[type='checkbox']").uniform();
 });
 trigger(".remove_license",function($this,e){
    var id = $this.attr('data-id');
    var lid = $("#hdn_license_"+id).val();
    $("#ajax_loader").show();
    $.ajax({
        url : 'ajax_agent_remaining_contract_v1.php',
        method: 'POST',
        data:{lid:lid,ajax_delete:"1",step:'ajax'},
        dataType: 'json',
        success : function(res){
            if(res.status == 'success') {
                $("#ajax_loader").hide();
                $this.parents(".license_portion").fadeOut("slow",function(){
                $(this).remove();
                });
                /* refreshLicense(); */
            }else if(res.status == 'fail'){
                $("#ajax_loader").hide();
            }
        }
    });
 });
 trigger(".license_not_expire",function($this,e){
    var id = $this.attr('data-id');
    if($('#license_not_expire_'+id).is(":checked")) {
        $("#license_expiry_"+id).attr('readonly','readonly');
        $("#license_expiry_"+id).val("12/31/2099");
    } else {
        $("#license_expiry_"+id).removeAttr('readonly');
        $("#license_expiry_"+id).val("");
    }
 });
 function agentLicenseRefresh(){
    $.ajax({
        url:"ajax_get_agent_license.php",
        data:{ajax_license:1},
        type:"post",
        beforeSend:function(e){
            $("#ajax_loader").show();
            // $("#agent_licenses").html('');
        },
        success:function(res){
            $("#ajax_loader").hide();
            $("#agent_licenses").html(res);
            fRefresh();
            common_select();
            refreshLicenseDatePicker();
            $(".license_not_expire").uniform();
        }
    });
 }

 function refreshLicenseDatePicker(){
    $(".license_expiry").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        startDate:new Date()
    });

    $(".license_active").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        /* startDate:new Date() */
    });
 }
//******** Agent License Code End ******************* 
//******** Signature Code Start ******************* 
 function resizeCanvas() {
    var ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
        signaturePad.clear();
    }
    $("#hdn_signature_data").val("");
 }
 function signaturePadInit() {
    $("#hdn_signature_data").val("");
    wrapper = document.getElementById("signature-pad");
    clearButton = wrapper.querySelector("[data-action=clear]");
    savePNGButton = wrapper.querySelector("[data-action=save-png]");
    saveSVGButton = wrapper.querySelector("[data-action=save-svg]");
    canvas = wrapper.querySelector("canvas"), signaturePad;
    signaturePad = new SignaturePad(canvas);
    resizeCanvas();
    if (!(signaturePad.isEmpty())) {
    $("#hdn_signature_data").val(signaturePad.toDataURL());
    }
    clearButton.addEventListener("click", function(event) {
    signaturePad.clear();
    $("#signature_name").val("");
    $("#hdn_signature_data").val("");
    });
 }
//******** Signature Code end   *******************

$(function(){
    $step = $(".data_tab li.active a").attr('data-step');
    $("#dataStep").val($step);    
    refreshLicense();
    refreshLicenseDatePicker();

    $("#e_o_expiration").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        startDate:new Date()
    });
    $('[data-toggle="tooltip"]').tooltip();

    $(document).on("blur keyup", "#dob", function() {
      $dob = $(this).val();
      if ($dob.indexOf('_') == -1) {
        count_age($dob);
      }
    });

    $("#phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#accnt_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#admin_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $("#e_o_expiration").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $("#zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
    $("#accnt_zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
    $("#business_zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
    $('#ssn').unmask().maskSSN('999-99-9999', {maskedChar:'X', maskedCharsLength:4});
    $('#bank_account_number').unmask().maskSSN('99999999999999999', {maskedChar:'X', maskedCharsLength:13});
    $('#bank_number_confirm').unmask().maskSSN('99999999999999999', {maskedChar:'X', maskedCharsLength:13});
    $("input[name='ssn']").addClass('form-control');
    $("input[name='bank_account_number']").addClass('form-control');
    $("input[name='bank_number_confirm']").addClass('form-control');
    $("#business_taxid").inputmask({"mask": "99-9999999",'showMaskOnHover': false});

    $(document).on("blur keyup", "#ssn", function() {
      $ssn_val = $(this).val();
       
      if($ssn_val == ''){
        $("input[name='ssn']").removeClass('has-value');
      } else{
        $("input[name='ssn']").addClass('has-value');
      }
    });

    $(document).on("blur keyup", "#bank_account_number", function() {
      $bank_account_val = $(this).val();
       
      if($bank_account_val == ''){
        $("input[name='bank_account_number']").removeClass('has-value');
      } else{
        $("input[name='bank_account_number']").addClass('has-value');
      }
    });


    $(document).on("blur keyup", "#bank_number_confirm", function() {
      $bank_account_confirm = $(this).val();
       
      if($bank_account_confirm == ''){
        $("input[name='bank_number_confirm']").removeClass('has-value');
      } else{
        $("input[name='bank_number_confirm']").addClass('has-value');
      }
    });

    refreshCurrencyFormatter();
    $('#e_o_amount').blur(function() {
        // $('#formatWhileTypingAndWarnOnDecimalsEnteredNotification2').html(null);
        refreshCurrencyFormatter();
    }).keyup(function(e) {
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

     <?php if (in_array(checkIsset($agent_res["is_contract_approved"]), array("Pending Resubmission"))) {
          if (!empty($rejection_text_new)) { ?>
              // $("#rejectModal").modal("show");
              $("#rejectModal").modal({show:true, backdrop: 'static', keyboard: false});
     <?php }} ?>
     <?php if (in_array(checkIsset($agent_res["is_contract_approved"]), array("Approved"))) { ?>
        $("#approvedModal").modal({show:true, backdrop: 'static', keyboard: false});
        clearInterval(autoSaveInt);
     <?php } ?>

    trigger("#needhelpfloat_button",function(){
        $("#helpmodal").modal("show");
    });
        
    $("#exit_business_img").click(function () {
        $("#enrollment_profile .profile-dropzone").click();
    });

    <?php   
        $currentImage='';
        if (file_exists($AGENTS_BRAND_ICON . $contract_business_image) && $contract_business_image != "") {
            $currentImage=$HOST . '/uploads/agents/brand_icon/' . $contract_business_image;
        }
    ?>
    $("#enrollment_profile .profile-dropzone").attr('style', 'background:url(<?php echo $currentImage;?>) no-repeat scroll center center /100% 100%;border-radius:0;height:100px;');
    // DROPZONE CODE START
     Dropzone.autoDiscover = false;
     var remaingContractDropzone = new Dropzone("#enrollment_profile .profile-dropzone", {
        // The configuration we've talked about above
        url: "#",
        autoProcessQueue: false,
        uploadMultiple: false,
        addRemoveLinks: false,
        parallelUploads: 1,
        thumbnailWidth: null,
        thumbnailHeight: null,
        maxFiles: 1,
        maxFilesize: 2,
        // createImageThumbnails:false,
        acceptedFiles: '.jpg, .gif, .png, .jpeg',
        //previewsContainer: "#imagePreviewEvent",
        dictDefaultMessage: '',
        dictInvalidFileType: 'Please upload .jpg, .gif,.png ,.jpeg files type only',
        dictFileTooBig: 'The maximum image size to upload is {{maxFilesize}} MiB and this image is {{filesize}} MiB Please resize your image and upload it again or try different image.',
        customErrorHandlingCode: 0,
        // The setting up of the dropzone

        init: function () {
            var remaingContractDropzone = this;
            this.on("addedfile", function (file) {
                if (this.files.length > 1) {
                this.files = this.files.slice(1, 2);
                }
                
                //ajax_loader(true);
                $('#ajax_loader').show();

                //set starting error none, if any error occuer then we setting up this code to 300 on error event
                this.options.customErrorHandlingCode = 200;
                contractcurrentDropzone1 = this;
                /* var editButton = Dropzone.createElement("<a href='javascript:void(0)' class='dz-edit'><i class='h-edit'></i></a>");
                file.previewElement.appendChild(editButton);
                var removeButton = Dropzone.createElement("<a href='javascript:void(0)' class='dz-trash'>Remove File</a>");
                file.previewElement.appendChild(removeButton);
                editButton.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $("#cropper_dropzone_type").val("profile");
                });
                removeButton.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    currentDropzone1.removeFile(file);
                });*/

            });

        }, thumbnail: function (file, dataUrl) {
            if (this.options.customErrorHandlingCode == 200) {
                /*if ($("#profile_action_image").val() != 'mock'){
                    $('#contract_profile_image_tmp_name').val(dataUrl);
                }*/
                $('#contract_profile_image_name').val(file.name);
                $('#contract_profile_image_size').val(file.size);
                $('#contract_profile_image_original').val(dataUrl);
                $('#contract_profile_image_type').val(file.type);
                $('#contract_profile_image_editor').addClass('ready');

                $('.cr-slider-wrap').addClass('range-overlay mb15');
                $("#cropper_dropzone_type").val("profile");
                $("#profile_image,#enrollment_profile .profile-dropzone").attr('style', 'background:url(' + dataUrl + ') no-repeat;background-size: 100% 100%;border-radius:0;height:100px;');

                $("#profile_image .dz-message").text(this.options.dictDefaultChangeMessage);
                //ajax_loader(false);
                $('#ajax_loader').hide();
                /* contractOpenCropModal(dataUrl); */
                $.ajax({
                    url: 'ajax_update_rep_business_picture.php?id=<?php echo $_SESSION['agents']['id'] ?>&is_enroll=Y',
                    data: 'profile_picture=' + dataUrl,
                    type: 'POST',
                    dataType: 'json',
                    beforeSend:function(){
                        $('#ajax_loader').show();
                    },
                    success: function (res) {
                        $('#ajax_loader').hide();
                        if (res.status == 'fail') {
                            if (res.error != "")
                                alert(res.error);
                        } else {
                            setNotifySuccess(res.message);
                            $('.pro_link_div').show();
                            $('.dz-remove').remove();
                            $('.dropzone-previews').empty();
                            if (res.url != "") {
                                $(".sidebar-header").find("img").attr("src", res.url);
                                $(".mw55").attr("src", res.url);
                            }
                        }
                    }
                });
                $('#enrollment_profile .dz-preview').remove();
                $('#enrollment_profile .dz-details img').attr('src', $('#contract_profile_image_tmp_name').val());
            }
        },
        error: function (e, error_msg) {
            this.options.customErrorHandlingCode = 300;
            $("#profile_image .dz-message").text(this.options.dictDefaultMessage);
            if (error_msg.search('The maximum image size to upload is') != -1){
                error_type = "Oops, this image is too big!";
            } else {
                error_type = "Oops, something went wrong!";
            }
            swal(error_type, error_msg);
                            //ajax_loader(false);
            $('#ajax_loader').hide();
            remaingContractDropzone.removeAllFiles(true);
            $(".profile-dropzone .dz-preview.dz-file-preview").remove();
        },
        removedfile: function () {
            //when remove file then setting up error to default 200
            this.options.customErrorHandlingCode = 0;

            $('#contract_profile_image_name').val('');
            $('#contract_profile_image_size').val('');
            $('#contract_profile_image_tmp_name').val('');
            $('#contract_profile_image_type').val('');
            //$('#main_profile_image').attr('src', "images/default_profile_image.png");
            $('#pro_image').attr('src', "");
            $('#pro_image').cropper("destroy");
            var _ref;
            // return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;

        }
     });
    // DROPZONE CODE END

    $('.back_to_first').click(function () {
        $('#fTabStep').addClass('active').attr('aria-expanded','true');
        $('#sTabStep').removeClass('active').attr('aria-expanded','false');
    });
    $("#business_address #address").unbind();
    
    var status = "<?php echo !empty($checkThirdStp) ? $checkThirdStp : '' ?>";
    if(status !== '' && status === 'Pending Approval'){
        $("#bap1").show();
        $.colorbox({
            inline: true,
            width: "525px",
            height: "205px",
            fixed:true,
            overlayClose: false,
            closeButton: false,
            href: "#bap1",
        });
        clearInterval(autoSaveInt);
    }
    $(document).on('click','.sign_out',function(e){
        /* window.redirect('<?=$AGENT_HOST?>'+'/logout.php'); */
        window.location = '<?=$AGENT_HOST?>' + '/logout.php';
    });

    $(document).on('click',"#display_popup",function(e){
        $("#display_popup_content").show();
        $.colorbox({
        inline: true , 
        href: '#display_popup_content',
        width: '595px', 
        height: '230px',
        onClosed : function(){
            $("#display_popup_content").hide();
        }
        });
    });

    $("#e_o_by_parent").trigger("change");
    trigger("#e_o_by_parent",function($this,e){
        if($this.prop('checked')){
        $("#eoDiv").find("em").hide();
        $("#e_o_information").slideUp();
        }else{
        $("#eoDiv").find("em").show();
        $("#e_o_information").slideDown();
        }
    },"change");

    if ($("#thirdstep").hasClass("in")) {
        if($("#thirdstep").attr("data-contract")=="Pending Contract"){
          signaturePadInit();
        }
    }
});

 refreshLicense = function(){
    $(".license_expiry").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $(".license_active").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
 };
 count_age = function($dob) {
    if ($dob != '' && $dob != "__/__/____") {
        $dob = new Date($dob);
        var today = new Date();
        var age = Math.floor((today - $dob) / (365.25 * 24 * 60 * 60 * 1000));
        if (!isNaN(age) && (age > 0)) {
            $("#age_count").val(age);
        } else {
            $("#age_count").val('');
        }
    } else {
        $("#age_count").val('');
    }
 };
 function refreshCurrencyFormatter(){
    $("#e_o_amount").formatCurrency({
        colorize: true,
        negativeFormat: '-%s%n',
        roundToDecimalPlace: 0
    });
 }
 function delete_business_image() {
   if (confirm('Are you sure you want to delete business logo?')) {
       $.ajax({
           url: 'delete_business_image.php?id=<?=$agent_res["id"]?>',
           type: 'POST',
           dataType: 'json',
           beforeSend: function () {
             $('#ajax_loader').show();
           },
           success: function (res) {
             $('#ajax_loader').hide();
               if (res.status == false) {
                   setNotifyError(res.message);
               } else {
                   var default_business_image = '';
                   $("#enrollment_profile .profile-dropzone").attr('style', 'background:url('+default_business_image+') no-repeat scroll center center /100% 100%;height:100px;');
                   $(".pro_link_div").hide();
                   $('#enrollment_profile .dropzone-previews').empty();
                   setNotifySuccess(res.message);
               }
           }
       });
   }
 }
 function setCustomInterval(){
    $step = $(".data_tab li.active a").attr('data-step');
    $("#dataStep").val($step);
    $('.error ').html('');
    $("#is_draft").val(1);
    $("#enrollment_form").submit();
 }
 </script>