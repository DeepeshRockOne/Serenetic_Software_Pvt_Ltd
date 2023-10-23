searchVisible = 0;
transparent = true;
$currentTab = 0;
$prevTab = 0;

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
};

var wrapper;
var clearButton;
var savePNGButton;
var saveSVGButton;
var canvas;
var signaturePad;

//Business Tab Code Start
$(document).on("change", ".account_type", function() {
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

getPersonalData = function() {
    $("#accnt_firstname").html($("#fname").val());
    $("#accnt_lastname").html($("#lname").val());
    $("#accnt_emails").html($("#emails").val());
    $("#accnt_phone").html($("#phone").val());
    $("#accnt_address").html($("#address").val());
    $("#accnt_address2").html($("#address2").val());
    $("#accnt_city").html($("#city").val());
    $("#accnt_state").html($("#state").val());
    $("#accnt_zipcode").html($("#zipcode").val());
    $("#accnt_dob").html($("#dob").val());
    //$("#accntssn").html($("#ssn").val());
    var ssn = $("#ssn").val();
    $("#accnt_ssn").html('*' + ssn.substr(ssn.length - 4));

    $("#accnt_dl_number").html($("#dl_number").val());
    $("#accnt_dl_state").html($("#dl_state").val());

    // $("#admin_emails").val($("#emails").val());
    $("#login_emails").val($("#emails").val());
}

$(document).on("change", ".e_o_coverage", function() {
    $value = $(this).val();
    if ($value == "Y") {
        $("#eoDiv").show();
    } else {
        $("#eoDiv").hide();
    }
});
//Business Tab Code End

//Commission Tab Code Start
$(document).on("change", ".process_commission", function() {
    $value = $(this).val();
    $("#bankInfoDiv").show();
    if ($value == "Personal") {
        $("#pearsonalAccountDiv").show();
        $("#businessAccountDiv").hide();
    } else if ($value == "Business") {
        $("#businessAccountDiv").show();
        $("#pearsonalAccountDiv").hide();
    }
});
//Commission Tab Code End

//Admin tab code start
getAdminData = function() {
    $value = $("input[name='account_type']:checked").val();
    if ($value == "Personal") {
        $name = $("#fname").val();
    } else if ($value == "Business") {
        $name = $("#business_name").val();
    } else {
        $name = "";
    }
    $("#login_emails").val($("#emails").val());
}
$(document).on("keypress", "#password", function() {
    $("#pswd_info").show();
});
//Admin tab code end

//Agreement Code Start
function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);

}

function edit_contact_detail() {
    $.colorbox.close();
    $('[href="#Account"]').trigger("click");
}

function edit_address_detail() {
    $.colorbox.close();
    $('[href="#Account"]').trigger("click");
}

function edit_account_detail() {
    $.colorbox.close();
    $('[href="#Admin"]').trigger("click");
}
trigger(".draft_enrollment", function($this, e) {
    e.preventDefault();
    $(".btn-finish:visible").parents('form').find(".btn-draft-saver").trigger("click")
});
trigger(".btn-finish,.btn-draft-saver", function($this, e) {
    e.preventDefault();
    $(".error").hide();
    $isDraft = 0;
    if ($this.hasClass("btn-draft-saver")) {
        $isDraft = 1;
    }
    $formName = $this.closest("form").attr("id");
    $("#" + $formName + " [name='is_draft']").val($isDraft);
    if ($formName == "self_enrollment_final") {
        if (!(signaturePad.isEmpty())) {
            $("#signature_data").val(signaturePad.toDataURL());
        }
    }
    //validation
    formHandler($("#" + $formName),
        function() {
            $("#ajax_loader").show();
        },
        function(data) {
            $("#ajax_loader").hide();
            $(document).find("p.last_updated").html("<i>Last saved "+data.updated_at+"</i>");
            if (data.status == 'account_approved') {
                if (data.step == "first") {
                    if (data.is_draft == 0) {
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
                    if (typeof data.license_doc != "undefined") {
                        $.each(data.license_doc, function(key, val) {
                            $("#hdn_physical_license_" + key).val(val.doc_id);
                            $("#span_physical_license_" + key).html(getDocumentLink(val.doc_link)).show();
                        });
                    }
                    if (data.is_draft == 0) {
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

                        // if (data.is_force == 0) {
                        //     // $('[href="#firststep"],[href="#secondstep"]').parent("li").addClass("disabled");
                        //     $ref = $("[href='#thirdstep']");
                        //     $ref.parent("li").removeClass("disabled");
                        //     $ref.trigger("click");
                        //     // $(".final_step_text").show();
                        //     $(".draft_enrollment,.btn-finish:hidden").remove();
                        // } else {
                        //     $('[href="#thirdstep"]').trigger('click');
                        // }
                    } else {
                        setNotifySuccess("Documents and details information saved successfully!");
                    }
                } else if (data.step == "third") {
                    swal("Thank You", "Your have now completed the agent enrollment process and are ready to start using your portal. Let's get started, today!", 'success').then(function() {
                        window.location.href = $AGENT_HOST + '/dashboard.php';
                    }, function(dismiss) {
                        window.location.href = $AGENT_HOST + '/dashboard.php';
                    });
                }
                //window.location = $AGENT_HOST + '/enrollment_complete.php';
            } else if (data.status == "session_fail") {
                setNotifyError("Oops... Something went wrong please try again later");
            } else {
                $setup = false;
                $(".error").remove();
                if (typeof data.errors.first_step != "undefined") {
                    $('[href="#firststep"]').trigger('click')
                    setNotifyError("Sorry! " + data.errors.first_step);
                } else if (typeof data.errors.final_contract_error != "undefined") {
                    setNotifyError("Sorry! " + data.errors.final_contract_error);
                }
                $.each(data.errors, function(key, val) {
                    error = '<div id="' + key + '-error" class="error error_preview">' + val + '</div>';
                    if ($setup == false) {
                        //tab-pane
                        $id = $("[name='" + key + "']").parents(".tab-pane").attr("id");
                        if (key == "socialMedia[]") {
                            $id = $("#error_social_media").parents(".tab-pane").attr("id");
                        } else if (key.indexOf("license_number") != -1) {
                            $id = $("#error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("license_state") != -1) {
                            $id = $("#error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("license_expiry") != -1) {
                            $id = $("#error_" + key).parents(".tab-pane").attr("id");
                        } else if (key.indexOf("physical_license") != -1) {
                            $id = $("#error_" + key).parents(".tab-pane").attr("id");
                        }

                        $("[href='#" + $id + "']").trigger("click");
                        if ($("[name='" + key + "']").length > 0) {
                            $('html, body').animate({
                                scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                            }, 1000);
                        }
                        $setup = true;
                    }

                    // $('#error_' + key).html(val).show();

                    if (key == "socialMedia[]") {
                        $("#error_social_media").html(error);
                    } else if (key.indexOf("license_number") != -1) {
                        $id = $("#error_" + key).html(error);
                    } else if (key.indexOf("license_state") != -1) {
                        $id = $("#error_" + key).html(error);
                    } else if (key.indexOf("license_expiry") != -1) {
                        $id = $("#error_" + key).html(error);
                    } else if (key.indexOf("license_active_date") != -1) {
                        $id = $("#error_" + key).html(error);
                    } else if (key.indexOf("license_type") != -1) {
                        $id = $("#error_" + key).html(error);
                    }else if (key.indexOf("licsense_authority") != -1) {
                        $id = $("#error_" + key).html(error);
                    } else {
                        $("[name='" + key + "']:first").next('label').after(error);
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
                    }
                });
            }
        });
});
getDocumentLink = function($link) {
    return '<a href="' + $link + '" title="View Document" target="_blank"><span class="fa fa-paperclip"></a>';
};
trigger("[name='socialMedia[]']", function($this) {
    $($this.attr("data-id")).toggle();
}, "change");

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
            $('#error_' + id).html('Minimum 4 chracter(s) required');
            $("#" + id + '_err').removeClass('rightmark wrongmark wrongmark_red');
            $("#" + id + '_err').addClass('wrongmark_red');
            return false;
        } else if ($(this).val().length > 20) {
            $('#error_' + id).html('Maximum 20 chracter(s) allow');
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
    
//google map api for address start
function fillInAddress() {
    $("#is_valid_address").val('N');
    var place = autocomplete.getPlace();
    var address = "";
    var zip = "";
    var city = "";
    var state = "";
    console.log(place);
    /* var defaultZip = $("#self_enrollment_second #zip").val(); */
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
        $("#self_enrollment_second #zipcode").val(zip);
        $("#self_enrollment_second #address").val(address);
        $("#self_enrollment_second #city").val(city);
        $("#self_enrollment_second #state").val(state).change();
        /* $("#is_valid_address").val('Y'); */
}

function fillInAddressAgency() {
    $("#is_valid_address").val('N');
    var place = autocomplete_Agency.getPlace();
    var address = "";
    var zip = "";
    var city = "";
    var state = "";
    console.log(place);
    /* var defaultZip = $("#self_enrollment_second #business_zipcode").val(); */
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
        $("#self_enrollment_second #business_zipcode").val(zip);
        $("#self_enrollment_second #business_address").val(address);
        $("#self_enrollment_second #business_city").val(city);
        $("#self_enrollment_second #business_state").val(state).change();
        /* $("#is_valid_address").val('Y'); */

}
//google map api for address end

$("#business_address #address").unbind();

function agentLicenseRefresh(){
    $.ajax({
        url:"ajax_get_agent_license.php",
        data:{ajax_license:1},
        type:"post",
        beforeSend:function(e){
            $("#ajax_loader").show();
            $("#agent_licenses").html('');
        },
        success:function(res){
            $("#ajax_loader").hide();
            $("#agent_licenses").append(res);
            fRefresh();
            common_select();
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

            $(".license_not_expire").uniform();
            $("#btn_finish").attr("disabled", false);
            $("#btn_finish").html('Submit');
        }
    });
}