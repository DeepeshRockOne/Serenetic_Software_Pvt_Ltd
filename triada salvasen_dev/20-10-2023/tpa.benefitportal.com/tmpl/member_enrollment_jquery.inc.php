<script type="text/javascript">
    //*************** General Code Start ***************************

        var enrollmentLocation = "<?=isset($enrollmentLocation)?$enrollmentLocation:'agentSide'?>";
        var is_group_member = "<?=isset($is_group_member)?$is_group_member:'N'?>";
        var wrapper = document.getElementById("signature-pad"),
        clearButton = wrapper.querySelector("[data-action=clear]"),
        savePNGButton = wrapper.querySelector("[data-action=save-png]"),
        saveSVGButton = wrapper.querySelector("[data-action=save-svg]"),
        canvas = wrapper.querySelector("canvas"),
        signaturePad;
        var datesDisabledArr = [];

        var placeSearch, autocomplete;
        var primary_address1 = '';
        var cart_products=<?php echo isset($quote_products)?json_encode($quote_products):'[]'; ?>;
        var tmp_cart_products =<?php echo isset($quote_products)?json_encode($quote_products):'[]'; ?>;
        var $waive_coverage=<?php echo isset($waive_coverage)?json_encode($waive_coverage):'[]'; ?>;
        var quote_healthy_step_fee=<?php echo isset($quote_healthy_step_fee)?$quote_healthy_step_fee:0; ?>;

        var coverage_date_prd_arr=<?php echo isset($coverage_date_selection_prd_array)?json_encode($coverage_date_selection_prd_array):'[]'; ?>;
        var coverage_date_date_arr=<?php echo isset($coverage_date_selection_date_array)?json_encode($coverage_date_selection_date_array):'[]'; ?>;
        var is_future_payment = 'false';
        var is_slider_updated = false;
        var is_amount_accepted = 'N';
        
        var primary_additional_data = <?php echo !empty($primary_additional_data)?json_encode($primary_additional_data):'[]'; ?>;
        var spouse_dep = <?php echo !empty($spouse_dep)?json_encode($spouse_dep):'[]'; ?>;
        var child_dep = <?php echo !empty($child_dep)?json_encode($child_dep):'[]'; ?>;
        var quote_contingent_beneficiary = <?php echo !empty($contingent_beneficiary)?json_encode($contingent_beneficiary):'[]'; ?>;
        var quote_principal_beneficiary = <?php echo !empty($principal_beneficiary)?json_encode($principal_beneficiary):'[]'; ?>;
        var already_puchase_product = [];
        var is_add_product = <?=isset($is_add_product)?$is_add_product:0;?>;
        var pre_tax_deductions = [];
        var post_tax_deductions = [];
        $dependent_count = 0;
        $principal_beneficiary_count = 0;
        $contingent_beneficiary_count = 0;

        $enrolleeElements = [];
        $enrolleeElementsVal = {};
        $tmpEnrolleeElementsVal = {};
        $primary_product_change = false;
        $spouse_product_change = false;
        $verification_option_product_change = false;

        $autoAssignedProductArr = [];
        $requiredProductArr = [];
        $riderProductArr = [];
        $is_auto_assign_product = false;
        $is_required_product = false;
        $is_rider_product = false;
        $is_new_healthy_step = false;
        $is_manually_open_healthy_step = false;
        $verification_running = false;

        $(document).on('shown.bs.tab', '.enrollment_tabs', function (e) {
            $id= $(this).attr('id');
            
            $("#filterByProductBar").hide();
            if($id=="coverage_tab"){
                $("#cartAmountBar").hide();
                $("#TopSummaryBar").hide();
            }
            if($id=="products_tab"){
                $("#filterByProductBar").show();
                $("#cartAmountBar").show();
                $("#TopSummaryBar").show();
            }
            if($id=="basic_detail_tab"){
                $("#cartAmountBar").show();
                $("#TopSummaryBar").show();

                /*--- remove extra products from cart_products ----*/
                var exrta_prds = [];
                $.each(cart_products,function(key,tmp_value) {
                    if(!$("#calculateRateButton_"+tmp_value.product_id).hasClass('added_btn') && !$("#addCoverageButton_"+tmp_value.product_id).hasClass('added_btn')) {
                        exrta_prds.push(tmp_value.product_id);
                    }
                });
                if(exrta_prds.length > 0) {
                    $.each(exrta_prds,function(key,product_id){
                        removeItemByKeyValue(cart_products,'product_id',product_id);
                    });
                }
                /*--- remove extra products from cart_products ----*/

                if($primary_product_change){
                    primary_member_field();
                }
                if($spouse_product_change){
                    is_dependent_required();
                }
            }
            if($id=="payment_detail_tab"){
                if($("#primary_email").val() != $("#que_primary_email").val()) {
                    $("#primary_email").val($("#que_primary_email").val());
                }
                if($("#primary_fname").val() != $("#que_primary_fname").val()) {
                    $("#primary_fname").val($("#que_primary_fname").val());
                }
                $("#cartAmountBar").show();
                $("#TopSummaryBar").show();

                if($verification_option_product_change){
                    verification_option();
                    enrollment_summary();
                }  

                if($("input[name='application_type']:checked").val() == 'member'){
                    $(".lead_sms_phoneno").html($('#primary_phone').val());
                    $("#email_name_to").val($('#primary_email').val());
                    $("#email_name_to").addClass('has-value');
                    $("#phone_number_to").val($('#que_primary_phone').val());
                    $("#phone_number_to").addClass('has-value');
                }
            }

            
            
        });

        $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 
        $(".phoneClass").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 

        $(document).on("click",".datePickerIcon",function(){
            $id=$(this).attr('data-applyon');
            if ($id == 'spouse_birthdate' || $id == 'primary_birthdate') {
                $("#" + $id).datepicker({
                    format: "mm/dd/yyyy",
                    autoclose: true,
                    endDate: new Date(),
                });
            }else if($(this).attr('data-element') == 'birthdate'){
                $("#"+$id).datepicker({
                    format: "mm/dd/yyyy",
                    autoclose: true,
                    endDate: new Date(),
                });
            } else {
                $("#" + $id).datepicker({
                    format: "mm/dd/yyyy",
                    autoclose: true,
                });
            }
            $("#"+$id).datepicker('show');
        });

        // function not allow future date from datepicker
        notAllowFututeDate("spouse_birthdate");
        notAllowFututeDate("primary_birthdate");

        //$("#populate_products_tooltip").attr('data-title','Cannot populate available products untill the zip code, date of birth and email fields are valid.');
        
        $("#product_filter").multipleSelect({selectAll: true,
            onClick:function(e){
                $text = e.text;
                $filterID = e.value;
                filterCategory();
            },
            onTagRemove:function(e){
                filterCategory();
            },
            onCheckAll:function(e){
                filterCategory();
            }
        });
    //*************** General Code end ***************************
    
    $(document).ready(function () {
        
        checkEmail();
        $(".cart_body_scroll").mCustomScrollbar({theme: "dark", scrollbarPosition: "outside"});
        if(cart_products.length > 0 || is_add_product == 1) {
            $("#primary_email").change();
        }

         // *************** group enrollment get started code started ***************
        $('#add_new_enrollee').click(function(){
            $('#group_enroll_enrollee_inport_div').hide();
            $('#enroll_get_started_div').show();
        });

        // $(document).off("click","#manual_refresh");
        // $(document).on("click","#manual_refresh",function(){
        //     window.location.reload();
        // });

        $(document).off("click","#btn_end_enrollment");
        $(document).on("click","#btn_end_enrollment",function(){
            var reload_url = $(this).attr('data-href');
            if(reload_url == ''){
                reload_page();
            }else{
                window.location.href = reload_url;
            }
        });

        $(document).off("click","#btn_update_enrollment");
        $(document).on("click","#btn_update_enrollment",function(){
            var reload_url = $(this).attr('data-href');
            if(reload_url != ''){
                exit_by_system=true;
                window.location.href = reload_url;
            } else {
                $(".data_tab li").removeClass("disabled");
                $(".data_tab li.active").prev().find("a").trigger("click");
            }
        });

        $(document).off('click', '.odrReceipt');
        $(document).on('click', '.odrReceipt', function(e) {
          var odrId = $(this).attr("data-odrId");
          $href = "<?=$HOST?>/order_receipt.php?orderId="+odrId;
          window.open($href, "myWindow", "width=1024,height=630");
        }); 

         $('#populate_get_started').click(function(){
            var enrolleeId = $("#select_enrollee_id :selected").data('enrolleeid');
            if(enrolleeId !== undefined && enrolleeId!==''){
                window.open('<?=$HOST?>/group_enroll/<?=checkIsset($user_name)?>/'+$("#select_enrollee_id :selected").data('enrolleeid'), '_blank');;
            }
            return false;
            $('#error_select_enrollee_id').html('');
            var enrollLoc = $(this).attr("data-type");
            if(enrollLoc == "quoteEnroll"){
                var sponsor_id = $("#sponsor_id").val();
                var enrollee_id = $("#select_enrollee_id").val();
                $.ajax({
                    url: '<?=$HOST?>/ajax_enrollment_get_enrollee.php',
                    data: {sponsor_id: sponsor_id,enrollee_id:enrollee_id},
                    dataType: 'json',
                    type: 'post',
                    success: function (res) {
                        if(res.status = "success"){
                            $leadID = res.leadID;
                            $leadFname = res.leadFname;
                            $leadBirth_date = res.leadBirth_date;
                            $leadZip = res.leadZip;
                            $leadEmail = res.leadEmail;
                            $leadGender = res.leadGender;
                            $leadHire_date = res.leadHire_date;
                            $leadGroup_coverage_id = res.leadGroup_coverage_id;
                            $leadGroup_classes_id = res.leadGroup_classes_id;
                            $leadEmployee_type = res.leadEmployee_type;
                            $leadGroup_company_id = res.leadGroup_company_id;

                            $('#lead_id').val($leadID);
                            $("#primary_fname").val($leadFname);
                            $("#primary_birthdate").val($leadBirth_date);
                            $("#primary_zip").val($leadZip);
                            $("#primary_email").val($leadEmail);
                            $("#primary_gender").val($leadGender);
                            
                            $("#coverage_period").val($leadGroup_coverage_id);
                            if($leadGroup_classes_id!=''){
                                $("#hdn_enrolle_class").val($leadGroup_classes_id);
                                $("#enrolle_class").val($leadGroup_classes_id);
                                $("#enrolle_class").prop('disabled',true);
                            }
                            if($leadEmployee_type!=''){
                                $("#hdn_relationship_to_group").val($leadEmployee_type);
                                $("#relationship_to_group").val($leadEmployee_type);
                                $("#relationship_to_group").prop('disabled',true);
                            }
                            if($leadHire_date!=''){
                                $("#relationship_date").val($leadHire_date);
                                $("#relationship_date").prop('readonly',true);
                            }
                            
                            if($leadGender=='Male'){
                                $("#primary_gender_male").prop("checked", true);
                                $("#primary_gender_male").parent().addClass("active");
                            }
                            if($leadGender=='Female'){
                                $("#primary_gender_female").prop("checked", true);
                                $("#primary_gender_female").parent().addClass("active");
                            } 
                            $("#relationship_to_group").selectpicker('refresh');               
                            $("#coverage_period").selectpicker('refresh');               
                            $("#enrolle_class").selectpicker('refresh');

                            $('#group_enroll_enrollee_inport_div').hide();
                            $('#enroll_get_started_div').show();
                             fRefresh();
                        }else{
                             $('#error_select_enrollee_id').html(res.message);
                        }
                    }
                });
            }else{

               if($('#select_enrollee_id').val()!=""){
                    $leadID = $("#select_enrollee_id").val();
                    $leadFname = $("#select_enrollee_id").find(':selected').attr('data-fname');
                    $leadBirth_date = $("#select_enrollee_id").find(':selected').attr('data-birth_date');
                    $leadZip = $("#select_enrollee_id").find(':selected').attr('data-zip');
                    $leadEmail = $("#select_enrollee_id").find(':selected').attr('data-email');
                    $leadGender = $("#select_enrollee_id").find(':selected').attr('data-gender');
                    $leadHire_date = $("#select_enrollee_id").find(':selected').attr('data-hire_date');
                    $leadGroup_coverage_id = $("#select_enrollee_id").find(':selected').attr('data-group_coverage_id');
                    $leadGroup_classes_id = $("#select_enrollee_id").find(':selected').attr('data-group_classes_id');
                    $leadEmployee_type = $("#select_enrollee_id").find(':selected').attr('data-employee_type');
                    $leadGroup_company_id = $("#select_enrollee_id").find(':selected').attr('data-group_company_id');
                    $('#lead_id').val($leadID);
                    $("#primary_fname").val($leadFname);
                    $("#primary_birthdate").val($leadBirth_date);
                    $("#primary_zip").val($leadZip);
                    $("#primary_email").val($leadEmail);
                    $("#primary_gender").val($leadGender);
                    
                    $("#coverage_period").val($leadGroup_coverage_id);
                    if($leadGroup_classes_id!=''){
                        $("#hdn_enrolle_class").val($leadGroup_classes_id);
                        $("#enrolle_class").val($leadGroup_classes_id);
                        $("#enrolle_class").prop('disabled',true);
                    }
                    if($leadEmployee_type!=''){
                        $("#hdn_relationship_to_group").val($leadEmployee_type);
                        $("#relationship_to_group").val($leadEmployee_type);
                        $("#relationship_to_group").prop('disabled',true);
                    }
                    if($leadHire_date!=''){
                        $("#relationship_date").val($leadHire_date);
                        $("#relationship_date").prop('readonly',true);
                    }
                    
                    if($leadGender=='Male'){
                        $("#primary_gender_male").prop("checked", true);
                        $("#primary_gender_male").parent().addClass("active");
                    }
                    if($leadGender=='Female'){
                        $("#primary_gender_female").prop("checked", true);
                        $("#primary_gender_female").parent().addClass("active");
                    } 
                    $("#relationship_to_group").selectpicker('refresh');               
                    $("#coverage_period").selectpicker('refresh');               
                    $("#enrolle_class").selectpicker('refresh');

                    $('#group_enroll_enrollee_inport_div').hide();
                    $('#enroll_get_started_div').show();
                }else{
                    $('#error_select_enrollee_id').html('Please select enrollee to import or invalid enrollee');
                }
            }
            fRefresh();
        });
        
       
        
        $(".contibution_price_td").hide();
        if(enrollmentLocation == "groupSide" || is_group_member == "Y"){
            $(".contibution_price_td").show();
        }
        $(".ui-helper-hidden-accessible").remove();
        
        if(typeof($('#select_enrollee_id').val()) !== "undefined" && $('#select_enrollee_id').val() != ""){
            setTimeout(function(){
                $('#populate_get_started').trigger('click');
            },2000);
        }
        // *************** group enrollment get started code end ***************

        if(spouse_dep.length > 0) {
            $("#addSpouseCoverage").trigger('click');
            $(".spouseCoverageData.spouse_fname_1").val(spouse_dep[0].fname);
            $(".spouseCoverageData.spouse_gender_1[value='"+spouse_dep[0].gender+"']").trigger('click');
            $(".spouseCoverageData.spouse_birthdate_1").val(spouse_dep[0].birthdate);
        }

        if(child_dep.length > 0) {
            $("#addChildCoverage").addClass('active');
            $("#addChildCoverageButtonDiv").show();
            for(var i = 0; i < child_dep.length;i++){
                var ii = i + 1;
                addChildCoverage();
                $(".childCoverageData.child_fname_"+ii).val(child_dep[i].fname);
                $(".childCoverageData.child_gender_"+ii+"[value='"+child_dep[i].gender+"']").trigger('click');
                $(".childCoverageData.child_birthdate_"+ii).val(child_dep[i].birthdate);
            }
        }
        
        //******************** Products tab Code start *******************************
        //******************** Products tab Code end   *******************************
        
        //******************** Enroll tab Code start *******************************
            
        //******************** Enroll tab Code end   *******************************
        
        //******************** Form Submit tab Code start *******************************
            $('#enrollment_form').ajaxForm({
                beforeSend: function () {
                    $("#ajax_loader").show();
                },
                dataType: 'json',
                success: function (res) {                
                    $("#ajax_loader").hide();
                    $("#dependent_array").val('');
                    if(res.dependent_array){
                        $("#dependent_array").val(res.dependent_array);
                    }
                    if(res.only_waive_products){
                        $("#only_waive_products").val(res.only_waive_products);
                        $("#billing_display").val(res.billing_display);
                        if(res.only_waive_products == 'Y'  || res.billing_display == 'N'){
                            $("#enrollmentPaymentDiv").hide();
                        }else{
                            $("#enrollmentPaymentDiv").show();
                        }
                    }
                    if(res.address_response_status =="success"){
                        $(".suggestedAddressEnteredName").html($("#que_primary_fname").val()+" "+$("#que_primary_lname").val());
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
                                    $("#is_address_verified").val('Y');
                                    $("#que_primary_address1").val(res.address).addClass('has-value');
                                    $("#que_primary_address2").val(res.address2).addClass('has-value');
                                }else{
                                    $("#is_address_verified").val('N');
                                }
                            },
                        });
                    }else if(res.status=="success"){
                        if(res.submit_type=="continue"){
                            $(".data_tab li.active").addClass("completed");
                            $(".data_tab li.active").next().find("a").trigger("click");
                            $(".data_tab li.active").removeClass("disabled");
                            $('html, body').animate({
                              scrollTop: $('.data_tab').offset().top-100
                            }, 1000);
                            $payment_mode = $('#payment_mode').val();
                            if($payment_mode=='CC'){
                                $("#div_payment_mode_ach").hide();
                                $("#ach_text").hide();
                                $("#div_payment_mode_card").show();
                            }
                        }

                        if(typeof(res.sub_status) !== "undefined" && res.sub_status == 'verification'){
                            $(".lead_dislay_info").text(res.lead_display_id + " " + res.lead_name);
                            if(!$verification_running){
                                get_enrollment_verification_status(res.sent_via,res.sent_to_member,res.lead_quote_detail_id,res.enrollmentLocation,res.is_add_product);
                            }
                            $(".data_tab li").addClass("disabled");
                        }

                        if(res.cc_html != ""){
                            $('.cc_type_wrapper').html('');
                            $('.cc_type_wrapper').html(res.cc_html);
                            common_select();
                        }
                        if(res.joinder_agreement_require == 'Y'){
                            $("#joinderAgreementDiv").show();
                            $("#joinder_agreement").val("Y");
                        }else{
                            $("#joinderAgreementDiv").hide();
                            $("#joinder_agreement").val("N");
                        }
                    }else if (res.status == 'order_receipt') {
                        if (res.order_receipt != '') {
                            openOrderReceipt(res);
                        }
                    } else if (res.status == 'account_approved') {
                        if(typeof(res.sent_to_member) !== "undefined" && res.sent_to_member != "" && typeof(res.lead_quote_detail_id) !== "undefined" && res.lead_quote_detail_id != "") {
                            $("#lead_quote_detail_id").val(res.lead_quote_detail_id);

                            if(typeof(res.customer_id) !== "undefined") {
                                $("#customer_id").val(res.customer_id);
                            }
                            if(typeof(res.lead_id) !== "undefined") {
                                $("#lead_id").val(res.lead_id);
                            }
                            if(typeof(res.order_id) !== "undefined") {
                                $("#order_id").val(res.order_id);
                            }

                            //Display Enrollment Live Status
                            if(res.message_delivered_status == "success") {
                                exit_by_system = true;
                                var is_add_product = 'N';
                                if(typeof(res.is_add_product) !== "undefined") {
                                    if(res.is_add_product == 1){
                                        is_add_product = 'Y';    
                                    }
                                }

                                // if(enrollmentLocation == "self_enrollment_site" || enrollmentLocation == "aae_site") {
                                //     if(enrollmentLocation == "agentSide") {
                                //         window.location.href = "<?=$AGENT_HOST?>/enrollment_status.php?sent_via="+res.sent_to_member+"&id="+res.lead_quote_detail_id+"&enrollmentLocation="+enrollmentLocation+"&is_add_product="+is_add_product+'&site_user_name='+$("#site_user_name").val();
                                //     } else {
                                //         window.location.href = "<?=$HOST?>/enrollment_status.php?sent_via="+res.sent_to_member+"&id="+res.lead_quote_detail_id+"&enrollmentLocation="+enrollmentLocation+"&is_add_product="+is_add_product+'&site_user_name='+$("#site_user_name").val();;
                                //     }
                                // } else {
                                //     setNotifySuccess("Verification sent successfully");
                                //     $.colorbox({
                                //         href : "<?=$HOST?>/enrollment_status.php?is_popup=1&sent_via="+res.sent_to_member+"&id="+res.lead_quote_detail_id+"&enrollmentLocation="+enrollmentLocation+"&is_add_product="+is_add_product,
                                //         iframe: 'true', 
                                //         width: '750px', 
                                //         height: '550px',
                                //         escKey: false,
                                //         overlayClose: false,
                                //         closeButton:false,
                                //         onClosed: function () {
                                //             exit_by_system = false;
                                //         }
                                //     });
                                // }
                            } else {
                                setNotifyError("Verification delivery failed");
                            }
                        } else {
                            if(typeof(res.md5_customer_id) !== "undefined") {
                                $("#md5_customer_id").val(res.md5_customer_id);
                            }
                            if(typeof(res.md5_order_id) !== "undefined") {
                                $("#md5_order_id").val(res.md5_order_id);
                            }
                            if(typeof(res.payment_type) !== "undefined") {
                                $("#payment_type").val(res.payment_type);
                            }
                            exit_by_system = true;
                            reload_page();
                        }
                    } else if (res.status == 'fail') {
                        if (res.div_step_error.length) {
                          if (!$('#' + res.div_step_error).is(":visible"))
                            $("[href='#" + res.div_step_error + "']").click();
                        }
                        var is_error = true;
                        $.each(res.errors, function (index, error) {
                            console.log(index,error);
                            $('#error_' + index).html(error).show();
                            if(typeof($('#error_' + index).closest('div.panel-collapse.collapse')) !== "undefined") {
                                if(!$('#error_' + index).closest('div.panel-collapse.collapse').hasClass('in')) {
                                    $("#"+$('#error_' + index).closest('div.panel-collapse.collapse').attr('id')).collapse("show");
                                }
                            }
                            if (is_error) {
                                var offset = $('#error_' + index).offset();
                                if(typeof(offset) === "undefined"){
                                    console.log("Not found : "+index);
                                }else{
                                    scrollToElement($('#error_' + index));
                                    is_error = false;
                                }
                            }
                            if(index == "signature_data") {
                                resizeCanvas();
                            }
                        });

                        if(typeof (res.primary_is_dependent) !== "undefined"){
                            var pd_data = res.pd_data;
                            swal("Sorry!", pd_data.member_name+" already had plan during that period ("+pd_data.member_id+") and first available effective date is "+pd_data.effective_date+" or later. Please adjust effective date and submit again.");
                        } else if (typeof (res.primary_is_exist) !== "undefined") {
                            var pe_data = res.pe_data;
                            swal({
                                title: "Duplicate Member",
                                html: "Member ("+pe_data.member_id+") has the same First Name, Last Name, and DOB. Please change these details",
                                type: "question",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                showConfirmButton: true,
                                showCloseButton: false,
                                allowOutsideClick: false,
                            }).then(function () {
                                /*displayLoader();
                                $.ajax({
                                    url: '<?=$HOST?>/check_already_email.php?set_duplicate_primary=Y',
                                    data: {rep_id: pe_data.member_id},
                                    dataType: 'json',
                                    type: 'post',
                                    success: function (res) {
                                        hideLoader();
                                    }
                                });*/
                            }, function (dismiss) {
                                /*displayLoader();
                                $.ajax({
                                    url: '<?=$HOST?>/check_already_email.php?unset_duplicate_primary=Y',
                                    dataType: 'json',
                                    type: 'post',
                                    success: function (res) {
                                        hideLoader();
                                    }
                                });*/
                            });
                        }

                        if(res.required_products_error){
                            $.each(res.required_products_error,function($k,$v){
                                $category_id = $("#product_tr_"+$k).parent('tbody').attr('data-category_id');
                                
                                if($("#error_product_"+$k).length > 0){
                                    $("#panel_"+$category_id).collapse("show");
                                    $("#error_product_"+$k).html("This product is required to purchase for : "+$v['productName']).show();
                                }else{
                                    $("#error_product_"+$v['product_id']).html($v['requiredProductName'] + " is required to purchase for this product").show();
                                }
                            });
                        }
                    } else if (res.status == 'payment_fail') {
                        $("#lead_quote_detail_id").val(res.lead_quote_detail_id);
                        $("#customer_id").val(res.customer_id);
                        if($("#billing_profile").val() == "new_billing" && typeof(res.billing_profile_id) !== "undefined") {
                            $("#last_billing_profile_id").val(res.billing_profile_id);
                        }

                        colorBoxClose();
                        swal({
                            title: "Payment Failed",
                            html: res.payment_error,
                            type: "error",
                            showCancelButton: true,
                            cancelButtonText: "Update Billing",
                            confirmButtonText: "Save to Lead",
                            allowOutsideClick: false,
                        }).then(function () {
                            swal({
                                title: "Save to lead",
                                html: "You have successfully saved this record to lead",
                                type: "success",
                                confirmButtonText: "Ok",
                                allowOutsideClick: false,
                            }).then(function () {
                                exit_by_system = true;
                                reload_page(true);
                            }, function (dismiss) {
                                exit_by_system = true;
                                reload_page(true);
                            });
                        }, function (dismiss) {
                            
                        });
                    } else if (res.status == 'application_already_submitted') {
                        exit_by_system = true;
                        reload_page();
                    }
                },
                error: function () {
                    alert('Due to some technical error file couldn\'t uploaded.');
                }
            });
        //******************** Form Submit tab Code end   *******************************
        if('<?= $enrollmentLocation;?>' == 'adminSide'){
            $('#TopSummaryBar').addClass('adminSide');
        }
        if ($(window).width() >= 768) {
            $('#TopSummaryBar ').scrollFix({
                side: 'top',
                syncPosition:false,
                syncSize:false,
                topUnfixOffset:-160,
                styleSubstitute:false
            });
            enrollTab_resize();
            $('.bottom_btn_wrap ').scrollFix({
                side: 'bottom'
            });
        }
    });


      function enrollTab_resize(){
        var mainCont = $("ul.nav-noscroll.data_tab").width();
        var minusWidth = '30';
        $(".scrollfix-top").width(mainCont - minusWidth);
      }
      $(window).on("resize scroll",function(e){
            enrollTab_resize();
      });
    //******************** Coverage Details tab Code start *******************************
        
        $(document).off("click","#addSpouseCoverage");
        $(document).on("click","#addSpouseCoverage",function(e){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('#spouseCoverageMainDiv').html('');
            }else{
                $(this).addClass('active');
                html = $('#spouseCoverageDynamicDiv').html();
                $('#spouseCoverageMainDiv').html(html);
                $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 
            }
        });

        $(document).off("click","#addChildCoverage");
        $(document).on("click","#addChildCoverage",function(e){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('#childCoverageMainDiv').html('');
                $("#addChildCoverageButtonDiv").hide();
            }else{
                $(this).addClass('active');
                addChildCoverage();
                $("#addChildCoverageButtonDiv").show();
            }
        });

        $(document).off("click","#addChildCoverageButton");
        $(document).on("click","#addChildCoverageButton",function(e){
            addChildCoverage();
        });

        $(document).off("change","#billing_profile");
        $(document).on("change","#billing_profile",function(e){
            e.stopPropagation();
            $val = $(this).val();
            if($val == "new_billing"){
                $("#new_payment_method").show();
            } else {
                $("#new_payment_method").hide();
            }
        });

        $(document).off("change","#relationship_to_group");
        $(document).on("change","#relationship_to_group",function(e){
            e.stopPropagation();
            $val = $(this).val();
            $("#hdn_relationship_to_group").val($val);
        }); 
        $(document).off("change","#enrolle_class");
        $(document).on("change","#enrolle_class",function(e){
            e.stopPropagation();
            $val = $(this).val();
            $("#hdn_enrolle_class").val($val);
        }); 

        $(document).off("click",".removeChildCoverageInnerDiv");
        $(document).on("click",".removeChildCoverageInnerDiv",function(e){
            $id=$(this).attr('data-id');
            $removed_display_number = parseInt($("#display_number_"+$id).attr('data-display-number'));
            $("#childCoverageInnerDiv"+$id).remove();

            $innerDivLength=$("#childCoverageMainDiv .childCoverageInnerDiv").length;

            if($innerDivLength > 0){
                $('#childCoverageMainDiv .display_number').each(function(){
                
                    $count = parseInt($(this).attr('data-display-number'));
                    if($count > $removed_display_number){
                        $newCount = $count - 1;

                        $(this).attr('id',"display_number_"+$newCount);
                        $(this).attr('data-display-number',$newCount);
                        $(this).html($newCount);

                        $("#childCoverageInnerDiv"+$count).attr('data-id',$newCount);
                        $("#childCoverageInnerDiv"+$count).attr('id',"childCoverageInnerDiv"+$newCount);
                        
                        $("#child_fname_"+$count).attr('data-id',$newCount);
                        $("#child_fname_"+$count).attr("name","child_fname["+$newCount+"]");
                        $("#child_fname_"+$count).attr('id',"child_fname_"+$newCount);
                        $("#child_fname_"+$count).removeClass("child_fname_"+$count);
                        $("#child_fname_"+$count).addClass("child_fname_"+$newCount);
                        $("#error_child_fname_"+$count).attr('id',"error_child_fname_"+$newCount);
                        
                        $("input[name='child_gender["+$count+"]']").attr('data-id',$newCount);
                        $("input[name='child_gender["+$count+"]']").removeClass("child_gender_"+$count);
                        $("input[name='child_gender["+$count+"]']").addClass("child_gender_"+$newCount);
                        $("input[name='child_gender["+$count+"]']").attr('name',"child_gender["+$newCount+"]");
                        $("#error_child_gender_"+$count).attr('id',"error_child_gender_"+$newCount);
                        
                        $("#datePickerApplyOn"+$count).attr('data-applyon',"child_birthdate_"+$newCount);
                        $("#datePickerApplyOn"+$count).attr('id',"datePickerApplyOn"+$newCount);
                        $("#child_birthdate_"+$count).attr('data-id',$newCount);
                        $("#child_birthdate_"+$count).attr("name","child_birthdate["+$newCount+"]");
                        $("#child_birthdate_"+$count).removeClass("child_birthdate_"+$count);
                        $("#child_birthdate_"+$count).addClass("child_birthdate_"+$newCount);
                        $("#child_birthdate_"+$count).attr('id',"child_birthdate_"+$newCount);
                        
                        $("#removeChildCoverageInnerDiv"+$count).attr('data-id',$newCount);
                        $("#removeChildCoverageInnerDiv"+$count).attr('id',"removeChildCoverageInnerDiv"+$newCount);
                    }
                });
            }else{
                $("#addChildCoverage").removeClass('active');
                $('#childCoverageMainDiv').html('');
                $("#addChildCoverageButtonDiv").hide();
            }
        });

        $(document).off("keyup","#que_primary_lname");
        $(document).on("keyup","#que_primary_lname",function(e){
            $plname = $(this).val(); 
            $('.spouse_last_name').val($plname);
            $('.child_last_name').val($plname);
            if($plname != ""){
                $('.spouse_last_name').addClass('has-value');
                $('.child_last_name').addClass('has-value');
            }else{
                $('.spouse_last_name').removeClass('has-value');
                $('.child_last_name').removeClass('has-value');
            }
        });       

        $(document).off("blur change",".populate_product_rule");
        $(document).on("blur change",".populate_product_rule",function(e){
            e.stopPropagation();
            $primary_fname = $("#primary_fname").val();
            $primary_zip = $("#primary_zip").val();
            $primary_gender = $("input[name='primary_gender']:checked"). val();
            $primary_birthdate = $("#primary_birthdate").val();
            $primary_email = $("#primary_email").val();

            $element_name = $(this).attr('name');
            $element_type=$(this).attr('type');
            $value = $(this).val();

            if($element_type == "radio"){
                if(!$("."+$element_name+"_1").attr('readonly')){
                    $("."+$element_name+"_1[value='"+$value+"']").trigger('click');
                    $(".hidden_"+$element_name+"_1").val($value);
                }
            }else{
                $("."+$element_name+"_1").val($value);
                $(".hidden_"+$element_name+"_1").val($value);
            }

            $("#li_coverage_detail").removeClass("completed");
            $("#li_product_detail").removeClass("completed");
            $("#li_basic_detail").removeClass("completed");
            $("#li_payment_detail").removeClass("completed");
            
            $("#li_product_detail").addClass("disabled");
            $("#li_basic_detail").addClass("disabled");
            $("#li_payment_detail").addClass("disabled");
        });

        $(document).off('blur change', '.spouseCoverageData');
        $(document).on('blur change', '.spouseCoverageData', function (e) {
            e.stopPropagation();
            $element_type = $(this).attr('type');
            $value = $(this).val();
            $element_name = $(this).attr('name');

            if($element_type == "radio"){
                if(!$("."+$element_name+"_1").attr('readonly')){
                    $("."+$element_name+"_1[value='"+$value+"']").trigger('click');
                    $(".hidden_"+$element_name+"_1").val($value);
                }
            }else{
                $("."+$element_name+"_1").val($value);
                $(".hidden_"+$element_name+"_1").val($value);
            }

            if($value!= undefined){
                $element = $element_name+"_1";
                $tmpEnrolleeElementsVal[$element] = $value;
            }

            $("#li_coverage_detail").removeClass("completed");
            $("#li_product_detail").removeClass("completed");
            $("#li_basic_detail").removeClass("completed");
            $("#li_payment_detail").removeClass("completed");
            
            $("#li_product_detail").addClass("disabled");
            $("#li_basic_detail").addClass("disabled");
            $("#li_payment_detail").addClass("disabled");
        });

        $(document).off('blur change', '.childCoverageData');
        $(document).on('blur change', '.childCoverageData', function (e) {
            e.stopPropagation();
            $element_type = $(this).attr('type');
            $value = $(this).val();
            $data_id = $(this).attr('data-id');
            $data_element = $(this).attr('data-element');
            $data_enrollee_type = 'child';

            if($element_type == "radio"){
                if(!$("."+$data_enrollee_type+"_"+$data_element+"_"+$data_id).attr('readonly')){
                    $("."+$data_enrollee_type+"_"+$data_element+"_"+$data_id+"[value='"+$value+"']").trigger('click');
                    $(".hidden_"+$data_enrollee_type+"_"+$data_element+"_"+$data_id).val($value);
                }
            }else{
                $("."+$data_enrollee_type+"_"+$data_element+"_"+$data_id).val($value);
                $(".hidden_"+$data_enrollee_type+"_"+$data_element+"_"+$data_id).val($value);

            }
            if($value!= undefined){
                $element = $data_enrollee_type+"_"+$data_element+"_"+$data_id;
                $tmpEnrolleeElementsVal[$element] = $value;
            }

            $("#li_coverage_detail").removeClass("completed");
            $("#li_product_detail").removeClass("completed");
            $("#li_basic_detail").removeClass("completed");
            $("#li_payment_detail").removeClass("completed");
            
            $("#li_product_detail").addClass("disabled");
            $("#li_basic_detail").addClass("disabled");
            $("#li_payment_detail").addClass("disabled");
        });

        $(document).off("click","#populate_products");
        $(document).on('click','#populate_products',function(){
            $("#dataStep").val("1");
            $("#submit_type").val('continue');
            $("#action").val('continue_application');

            populate_product();
        });

        $(document).off("click","#btn_login");
        $(document).on('click', "#btn_login", function () {
            $this = $(this);
            $(".error").html('');
            $("#ajax_loader").show();
            $.ajax({
                url: '<?=$HOST?>/ajax_enrollment_existing_user_check.php',
                data: $("#form_login").serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $this.prop("disabled", true);
                    $this.append("<i class='fa fa-spin fa-spinner quote_loader'></i>")
                },
                success: function (res) {
                    $this.html("Submit");
                    $this.prop("disabled", false);
                    $("#ajax_loader").hide();

                    if (res.status == 'success') {
                        
                        sponsor_id = $('#sponsor_id').val();
                        customer_sposnsor = res.customer_sponsor_id;

                        $(".login_section").hide();

                        if(customer_sposnsor == sponsor_id){
                            $("#customer_id").val(res.customer_id);
                            
                            $.colorbox({inline: true, href: "#successfullMemberDiv", width: '530px', height: '250px'});
                        }else{
                            $("#customer_id").val(0);
                            $('#primary_email').val('');
                             $.colorbox({inline: true, href: "#failedMemberDiv", width: '530px', height: '250px'});
            
                        }                        
                    } else {
                        $.each(res.errors, function (index, error) {
                            $('#error_' + index).html(error).show();
                        });
                    }
                }
            });
        });

        $(document).off("click","#btn_forgot_password");
        $(document).on('click', "#btn_forgot_password", function () {
            $this = $(this);
            $(".error").html('');
            $("#ajax_loader").show();
            $.ajax({
                url: '<?=$HOST?>/ajax_enrollment_forgot_password.php',
                data: $("#form_forgot_password").serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $this.prop("disabled", true);
                    $this.append("<i class='fa fa-spin fa-spinner quote_loader'></i>")
                },
                success: function (res) {
                    $this.html("Submit");
                    $this.prop("disabled", false);
                    $("#ajax_loader").hide();
                    $('#form_forgot_password .error').html('');
                    if (res.status == 'success') {
                        exit_by_system = true;
                        reload_page(true);
                    } else {
                        $.each(res.errors, function (index, error) {
                            $('#error_' + index).html(error).show();
                        });
                    }
                }
            });
        });
    //******************** Coverage Details tab Code end   *******************************

    //******************** Products tab Code start *******************************
        $(document).off('click',".waive_checkbox");
        $(document).on('click', '.waive_checkbox', function(e){
            e.stopPropagation();
            cat_id = $(this).val();
            if($(this).is(":checked")) {
                $.colorbox({
                    inline:true,
                    href:"#waive_coverage_popup_"+cat_id,
                    height:"400px",
                    width:"575px",
                    closeButton:false,
                    escKey:false,
                    overlayClose:false,
                });
            } else {
                if($(".waive_coverage_reason[data-category_id='"+cat_id+"']:checked").val() != ''){
                  value_id = $(".waive_coverage_reason[data-category_id='"+cat_id+"']:checked").attr('id');
                  $("#"+value_id).prop("checked", false);
                  $("#waive_coverage_other_reason_"+cat_id).val('');
                  $("#waive_coverage_other_reason_div_"+cat_id).hide();
                  $("#error_waive_coverage_other_reason_"+cat_id).html("").hide();
                  $("#error_waive_coverage_reason_"+cat_id).html("").hide();
                  waived_checkbox(cat_id);
                }
            }
            $(".waive_checkbox").not('.js-switch').uniform();
            $(".waive_coverage_reason").not('.js-switch').uniform();
        });

        $(document).off('click',".waive_coverage_close");
        $(document).on('click','.waive_coverage_close', function(){
            cat_id = $(this).attr('data-category_id');
            $("#waive_checkbox_"+cat_id).prop('checked', false);
            if($(".waive_coverage_reason[data-category_id='"+cat_id+"']:checked").val() != ''){
                value_id = $(".waive_coverage_reason[data-category_id='"+cat_id+"']:checked").attr('id');
                $("#"+value_id).prop("checked", false);
                $("#waive_coverage_other_reason_"+cat_id).val('');
                $("#waive_coverage_other_reason_div_"+cat_id).hide();
                $("#error_waive_coverage_other_reason_"+cat_id).html("").hide();
                $("#error_waive_coverage_reason_"+cat_id).html("").hide();
            }
            $(".waive_checkbox").not('.js-switch').uniform();
            $(".waive_coverage_reason").not('.js-switch').uniform();
            parent.$.colorbox.close();
        });

        $(document).off('click',".waive_coverage_submit");
        $(document).on('click','.waive_coverage_submit', function(){
            cat_id = $(this).attr('data-category_id');
            checked_val = $(".waive_coverage_reason[data-category_id='"+cat_id+"']:checked").val();
            other_reason = $("#waive_coverage_other_reason_"+cat_id).val();
            if(checked_val != '' && checked_val != undefined){
                if(checked_val != 'Other'){
                    $("#error_waive_coverage_other_reason_"+cat_id).html("").hide();
                    $("#error_waive_coverage_reason_"+cat_id).html("").hide();
                    waived_checkbox(cat_id);
                    parent.$.colorbox.close();
                } else {
                    if(other_reason != ''){
                        $("#error_waive_coverage_other_reason_"+cat_id).html("").hide();
                        $("#error_waive_coverage_reason_"+cat_id).html("").hide();
                        waived_checkbox(cat_id);
                        parent.$.colorbox.close();
                    } else {
                        $("#error_waive_coverage_other_reason_"+cat_id).html("Reason is required").show();
                    }
                }
            } else {
                $("#error_waive_coverage_reason_"+cat_id).html("Please select any option").show();
            }
        });

        $(document).off('click',".waive_coverage_reason");
        $(document).on('click','.waive_coverage_reason', function(){
            cat_id = $(this).attr('data-category_id');
            $("#error_waive_coverage_other_reason_"+cat_id).html("").hide();
            $("#error_waive_coverage_reason_"+cat_id).html("").hide();
            if($(this).val() == "Other"){
                $("#waive_coverage_other_reason_div_"+$(this).attr('data-category_id')).show();
            } else {
                $("#waive_coverage_other_reason_div_"+$(this).attr('data-category_id')).hide();
            }
        });
        
        $(document).on('keyup','.waive_coverage_other_reason', function(){
          $("#error_waive_coverage_other_reason_"+$(this).attr('id').replace("waive_coverage_other_reason_","")).html("").hide();
        });

        $(document).off("click",".show_product_details");
        $(document).on("click",".show_product_details",function(e){
            $product_id = $(this).attr('data-product-id');
            $prd_desc = $(".tmp_prd_desc_" + $product_id).html();
            $aMyUTF8Output = base64DecToArr($prd_desc);
            $details = UTF8ArrToStr($aMyUTF8Output);
            $("#product_details_" + $product_id).html($details);
            $("#product_details_"+$product_id+" .plan_details_bottom_scroll").mCustomScrollbar({
                theme:"dark",
                scrollbarPosition : "outside"
            });
            $("#product_details_"+$product_id).collapse("toggle");
        });

        $(document).off("click",".hide_product_details");
        $(document).on("click",".hide_product_details",function(e){
            $product_id = $(this).attr('data-product-id');
            $("#product_details_"+$product_id).collapse("toggle");
        });

        $(document).off("click",".calculateRate");
        $(document).on("click",".calculateRate",function(e){
            $product_id = $(this).attr('data-product-id');
            $plan_id = $("#product_plan_"+$product_id).val();
            $pricing_model = $(this).attr('data-pricing-model');
            
            $(".error").html('');
            $("#inner_calculate_rate_main_div_"+$product_id).html('');

            if($plan_id>0){
                $("#product_calcualate_rate_"+$product_id).collapse("show");
                $("#calculateRateButton_"+$product_id).removeClass('added_btn');
                $("#calculateRateButton_"+$product_id).removeClass('calculateRate');
                $("#calculateRateButton_"+$product_id).addClass('cancelCalculateRate');
                $("#calculateRateButton_"+$product_id).html('Cancel');
                $("#addCalculatedCoverage_"+$product_id).removeAttr('disabled','disabled');
                addAdditionalQuestions($product_id,$pricing_model,'','displayQuestion');
                
            }else{
                $("#error_product_plan_"+$product_id).html("Please Select Plan");
                $("#calculateRateButton_"+$product_id).removeClass('added_btn');
                $("#calculateRateButton_"+$product_id).removeClass('cancelCalculateRate');
                $("#calculateRateButton_"+$product_id).addClass('calculateRate');
                $("#calculateRateButton_"+$product_id).html('Calculate Rate');
                $("#addCalculatedCoverage_"+$product_id).attr('disabled','disabled');
                $("#product_calcualate_rate_"+$product_id).collapse("hide");
            }
        });

        $(document).off("click",".cancelCalculateRate");
        $(document).on("click",".cancelCalculateRate",function(e){
            $product_id = $(this).attr('data-product-id');
                        
            $(".error").html('');
            $("#inner_calculate_rate_main_div_"+$product_id).html('');
            
            $("#product_calcualate_rate_"+$product_id).collapse("hide");
            $("#calculateRateButton_"+$product_id).removeClass('added_btn');
            $("#calculateRateButton_"+$product_id).removeClass('cancelCalculateRate');
            $("#calculateRateButton_"+$product_id).addClass('calculateRate');
            $("#calculateRateButton_"+$product_id).html('Calculate Rate');
            $("#addCalculatedCoverage_"+$product_id).attr('disabled','disabled');
        });

        $(document).off("click",".added_btn");
        $(document).on("click",".added_btn",function(e){
            $product_id = $(this).attr('data-product-id');
            $pricing_model = $(this).attr('data-pricing-model'); 
            removeProductFromCart($product_id,$pricing_model);
            if(tmp_cart_products.length > 0){
                $.map( tmp_cart_products, function( crtPrd, i ) {
                    if(crtPrd.product_id == $product_id && crtPrd.is_short_term_disablity_product == 'Y'){
                        $('#calculatedCoverage_'+$product_id).click();
                    }
                });
            }
        });

        

        $(document).off("click",".addCalculatedCoverage");
        $(document).on("click",".addCalculatedCoverage",function(e){
            $product_id = $(this).attr('data-product-id');
            $pricing_model = $(this).attr('data-pricing-model');
            $matrix_id = $('#product_plan_' + $product_id + ' :selected').attr('data-prd-matrix-id');
            addAdditionalQuestions($product_id,$pricing_model,'','addCoverage','',$matrix_id);
        });

        $(document).off("click",".calculatedCoverage");
        $(document).on("click",".calculatedCoverage",function(e){
            e.preventDefault();
            $product_id = $(this).attr('data-product-id');
            $pricing_model = $(this).attr('data-pricing-model');
            $matrix_id = $('#product_plan_' + $product_id + ' :selected').attr('data-prd-matrix-id');
            addAdditionalQuestions($product_id,$pricing_model,'','calculateRate','',$matrix_id);
        });

        $(document).off("click",".gap_btn_calculate_coverage");
        $(document).on("click",".gap_btn_calculate_coverage",function(e){
            $product_id = $(this).attr('data-product-id');
            $pricing_model = $(this).attr('data-pricing-model');
            $matrix_id = $('#product_plan_' + $product_id + ' :selected').attr('data-prd-matrix-id');
            addAdditionalQuestions($product_id,$pricing_model,'','calculateRate','',$matrix_id,'calculateGapRate');
        });

        $(document).off("click",".addCoverage");
        $(document).on("click",".addCoverage",function(e){
            $product_id = $(this).attr('data-product-id');
            $pricing_model = $(this).attr('data-pricing-model');
            $price = $("#product_plan_"+$product_id).find(':selected').attr('data-price');
            $display_price = $("#product_plan_"+$product_id).find(':selected').attr('data-display-price');
            $plan_id = $("#product_plan_"+$product_id).val();
            $matrix_id = $("#product_plan_"+$product_id).find(':selected').attr('data-prd-matrix-id');
            if($plan_id>0){
                addProductToCart($product_id,$price,$matrix_id,$pricing_model,$display_price);
                productPriceDisplay($product_id,$display_price);
            }else{
                $("#error_product_plan_"+$product_id).html("Please Select Plan");
            }

        });

        $(document).off("click",".removeCartProduct");
        $(document).on("click",".removeCartProduct",function(e){
            $product_id = $(this).attr('data-product-id');
            removeProductFromCart($product_id,'Default');
        });
        
        
        
        $(document).off("click",".addChildQuestion");
        $(document).on("click",".addChildQuestion",function(e){
            $product_id = $(this).attr('data-product-id');
            $enrolleeType = $(this).attr('data-enrollee-type');
            $pricing_model = '';
            $length = $(".display_number_"+$product_id+"_"+$enrolleeType).length;
            $childCount = $length + 1;
            $addType = 'addChild_'+$childCount;

            
            addAdditionalQuestions($product_id,$pricing_model,$addType,'displayQuestion');
        });

        $(document).off("click",".removeChildQuestion");
        $(document).on("click",".removeChildQuestion",function(e){
            $enrolleeType = $(this).attr('data-enrollee-type');
            $product_id = $(this).attr('data-product-id');
            $id=$(this).attr('data-id');
            $removed_display_number = parseInt($("#display_number_"+$product_id+"_"+$enrolleeType+"_"+$id).attr('data-display-number'));
            $("#productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType+"_"+$id).remove();

            $dynamicElement = ["fname", "gender", "birthdate","zip","smoking_status","tobacco_status","height","weight","no_of_children","has_spouse","benefit_amount","in_patient_benefit","out_patient_benefit","monthly_income","benefit_percentage"];
            $('#inner_calculate_rate_main_div_'+$product_id+' .display_number_'+$product_id+'_'+$enrolleeType).each(function(){
            
                $count = parseInt($(this).attr('data-display-number'));
                if($count > $removed_display_number){
                    $newCount = $count - 1;

                    $(this).attr('id',"display_number_"+$product_id+"_"+$enrolleeType+"_"+$newCount);
                    $(this).attr('data-display-number',$newCount);
                    $(this).html($newCount);

                    $("#productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType+"_"+$count).attr('data-id',$newCount);
                    $("#productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType+"_"+$count).attr('id',"productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType+"_"+$newCount);
                    
                    $("#addChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$count+"_div").attr('id',"productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType+"_"+$newCount+"_div");

                    $("#addChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$count).attr('data-id',$newCount);
                    $("#addChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$count).attr('id',"addChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$newCount);

                    $("#removeChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$count).attr('data-id',$newCount);
                    $("#removeChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$count).attr('id',"removeChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$newCount);

                    $.each($dynamicElement,function($k,$v){
                        $("#"+$enrolleeType+"_"+$v+"_div_"+$product_id+"_"+$count).attr('id',$enrolleeType+"_"+$v+"_div_"+$product_id+"_"+$newCount);
                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$count+"]["+$v+"]']").attr('data-id',$newCount);
                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$count+"]["+$v+"]']").removeClass($enrolleeType+'_'+$v+'_'+$count);
                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$count+"]["+$v+"]']").addClass($enrolleeType+'_'+$v+'_'+$newCount);

                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$count+"]["+$v+"]']").attr('id',$v+"_"+$enrolleeType+"_"+$product_id+"_"+$newCount);
                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$count+"]["+$v+"]']").attr('name',$enrolleeType+"["+$product_id+"]["+$newCount+"]["+$v+"]");

                        $("input[name='hidden_"+$enrolleeType+"["+$product_id+"]["+$count+"]["+$v+"]']").attr('id',"hidden_"+$enrolleeType+"_"+$product_id+"_"+$newCount+"_"+$v);
                        $("input[name='hidden_"+$enrolleeType+"["+$product_id+"]["+$count+"]["+$v+"]']").attr('name',"hidden_"+$enrolleeType+"["+$product_id+"]["+$newCount+"]["+$v+"]");

                    });
                    
                    
                }
            });
        });
        
        $(document).off('blur change', '.additional_question');
        $(document).on('blur change', '.additional_question', function (e) {
            e.stopPropagation();
            $data_enrollee_type = $(this).attr('data-enrollee-type');
            $data_id = $(this).attr('data-id');
            $data_element = $(this).attr('data-element');
            $element_type = $(this).attr('type');
            $value = $(this).val();

            if($data_enrollee_type=="primary" && ($data_element=="gender" || $data_element=="birthdate" || $data_element=="zip")){
                return false;
            }

            if($element_type == "radio"){
                if(!$("."+$data_enrollee_type+"_"+$data_element+"_"+$data_id).attr('readonly')){
                    $("."+$data_enrollee_type+"_"+$data_element+"_"+$data_id+"[value='"+$value+"']").trigger('click');
                    $(".hidden_"+$data_enrollee_type+"_"+$data_element+"_"+$data_id).val($value);
                }
            }else{
                $("."+$data_enrollee_type+"_"+$data_element+"_"+$data_id).val($value);
                $(".hidden_"+$data_enrollee_type+"_"+$data_element+"_"+$data_id).val($value);
            }
        });

        
        
        $(document).off('change', '.product_plan');
        $(document).on('change', '.product_plan', function (e) {
            e.stopPropagation();
            $product_id = $(this).attr('data-product-id');
            $price = $(this).find('option:selected').attr('data-price');
            $display_price = $(this).find('option:selected').attr('data-display-price');
            $plan_name = $(this).find("option:selected").text();

            if ($(this).val() != "") {
                productPriceDisplay($product_id,$display_price);
            } else {
                productPriceDisplay($product_id,'0.00');
            }
            $("#calculate_rate_plan_name_"+$product_id).html($plan_name);
            $("#inner_calculate_rate_main_div_"+$product_id).html('');

            removeProductFromCart($product_id,'Default');
        });

        $(document).off("click",".show_details_on_new_tab");
        $(document).on("click",".show_details_on_new_tab",function(e){
            $product_id = $(this).attr('data-product-id');
            $aMyUTF8Output = base64DecToArr($(this).attr('data-details'));
            $details = UTF8ArrToStr($aMyUTF8Output);

            $href = $(this).attr('data-href');
            var not_win = window.open("", "myWindow"+$product_id, "width=1024,height=767");
            not_win.document.body.innerHTML = $details;
        });

        $(document).off("click",".autoAssign_colorbox_details");
        $(document).on("click",".autoAssign_colorbox_details",function(e){
            $product_id = $(this).attr('data-product-id');
            $("#show_details_on_new_tab_"+$product_id).trigger('click');
        });

        $(document).off("click",".autoAssginedProductApproved");
        $(document).on("click",".autoAssginedProductApproved",function(e){
            $product_id = $(this).attr('data-product-id');
            $price = $("#autoAssign_colorbox_price_"+$product_id).html();
            $autoAssignTotal = $("#autoAssignTotal").html();

            if($(this).hasClass('text-success')){
                $autoAssignTotal = parseFloat($autoAssignTotal) -  parseFloat($price);
            }else{
                $autoAssignTotal = parseFloat($autoAssignTotal) + parseFloat($price);
            }
            $("#autoAssignTotal").html(parseFloat($autoAssignTotal).toFixed(2));

            $(this).toggleClass('text-success');
            $(this).toggleClass('text-light-gray');

            $("#autoAssginedProductReject_"+$product_id).removeClass('text-action');
            $("#autoAssginedProductReject_"+$product_id).addClass('text-light-gray');
        });

        $(document).off("click",".autoAssginedProductReject");
        $(document).on("click",".autoAssginedProductReject",function(e){
            $product_id = $(this).attr('data-product-id');

            $price = $("#autoAssign_colorbox_price_"+$product_id).html();
            $autoAssignTotal = $("#autoAssignTotal").html();

            if($("#autoAssginedProductApproved_"+$product_id).hasClass('text-success')){
                $autoAssignTotal = parseFloat($autoAssignTotal) - parseFloat($price);
                $("#autoAssignTotal").html(parseFloat($autoAssignTotal).toFixed(2));
            }

            $(this).toggleClass('text-action');
            $(this).toggleClass('text-light-gray');

            $("#autoAssginedProductApproved_"+$product_id).removeClass('text-success');
            $("#autoAssginedProductApproved_"+$product_id).addClass('text-light-gray');
        });

        $(document).off("click","#autoAssign_colorbox_confirm");
        $(document).on("click","#autoAssign_colorbox_confirm",function(e){

            if($(".autoAssginedProductApproved.text-success").length > 0){
                $(".autoAssginedProductApproved.text-success").each(function(){
                    $product_id = $(this).attr('data-product-id');
                    $product_plan = $("#autoAssign_colorbox_plan_"+$product_id).val();
                    
                    if ($product_plan != "") {
                        $("#product_plan_"+$product_id).val($product_plan);
                        $("#product_plan_"+$product_id).selectpicker('refresh');

                        if(!$("#addCoverageButton_"+$product_id).hasClass('added_btn')){
                            $autoAssignedProductArr = [];
                            $("#addCoverageButton_"+$product_id).trigger('click');
                        }
                        $("#autoAssign_colorbox_product_"+$product_id).remove();
                        $("#required_colorbox_product_"+$product_id).remove();
                        
                    }

                });
            }else{
                 if($(".autoAssginedProductReject.text-action").length > 0){
                    feeColorBoxClose();
                 }
            }
            
            
        });
       
        $(document).off("change",".autoAssign_colorbox_plan");
        $(document).on("change",".autoAssign_colorbox_plan",function(e){
            e.stopPropagation();
            $val = $(this).val();
            $product_id = $(this).attr('data-product-id');
            $price = $(this).find('option:selected').attr('data-price');
            $("#product_plan_"+$product_id).val($val);
            $("#product_plan_"+$product_id).selectpicker('refresh');

            if ($(this).val() != "") {
                productPriceDisplay($product_id,$price);
                $("#autoAssign_colorbox_price_"+$product_id).html($price);
            } else {
                productPriceDisplay($product_id,'0.00');
                $("#autoAssign_colorbox_price_"+$product_id).html('0.00');
            }

            $autoAssignTotal = 0.00;

            if($(".autoAssginedProductApproved.text-success").length > 0){
                $(".autoAssginedProductApproved.text-success").each(function(){
                    $product_id = $(this).attr('data-product-id');
                    $price = $("#autoAssign_colorbox_price_"+$product_id).html();

                    $autoAssignTotal = parseFloat($autoAssignTotal) + parseFloat($price);
                })
            }
            $("#autoAssignTotal").html(parseFloat($autoAssignTotal).toFixed(2));

        });


        $(document).off("click",".required_colorbox_details");
        $(document).on("click",".required_colorbox_details",function(e){
            $product_id = $(this).attr('data-product-id');
            $("#show_details_on_new_tab_"+$product_id).trigger('click');
        });

        $(document).off("click","#required_colorbox_confirm");
        $(document).on("click","#required_colorbox_confirm",function(e){
            
            if($("#requiredColorboxProductDiv .required_colorbox_product").length > 0){
                $("#requiredColorboxProductDiv .required_colorbox_product").each(function(){
                    $product_id = $(this).attr('data-product-id');
                    $product_plan = $("#required_colorbox_plan_"+$product_id).val();
                    if ($product_plan != "") {
                        $("#product_plan_"+$product_id).val($product_plan);
                        $("#product_plan_"+$product_id).selectpicker('refresh');

                        if(!$("#addCoverageButton_"+$product_id).hasClass('added_btn')){
                            $requiredProductArr = [];
                            $("#addCoverageButton_"+$product_id).trigger('click');
                            
                        }
                        $("#autoAssign_colorbox_product_"+$product_id).remove();
                        $("#required_colorbox_product_"+$product_id).remove();
                    }
                });
            }
            
        });

        $("#primary_fname").on("input",function(){
            removeNumberSpecialChar($(this));
        });

        $(document).off("input","#que_primary_fname");
        $(document).on("input","#que_primary_fname",function(){
            removeNumberSpecialChar($(this));
        });

        $(document).off("input","#que_primary_lname");
        $(document).on("input","#que_primary_lname",function(){
            removeNumberSpecialChar($(this));
        });

        $(document).off("input","#expiration");
        $(document).on("input","#expiration",function() {
            removeCharAndSpecialChar($(this));
        });

        $(document).off("input","#ach_bill_fname");
        $(document).on("input","#ach_bill_fname",function() {
            removeNumberSpecialChar($(this));
        });

        $(document).off("input","#ach_bill_lname");
        $(document).on("input","#ach_bill_lname",function() {
            removeNumberSpecialChar($(this));
        });
        
        $(document).off("change",".required_colorbox_plan");
        $(document).on("change",".required_colorbox_plan",function(e){
            e.stopPropagation();
            $val = $(this).val();
            $product_id = $(this).attr('data-product-id');
            $price = $(this).find('option:selected').attr('data-price');
            $("#product_plan_"+$product_id).val($val);
            $("#product_plan_"+$product_id).selectpicker('refresh');

            if ($(this).val() != "") {
                productPriceDisplay($product_id,$price);
                $("#required_colorbox_price_"+$product_id).html($price);
            } else {
                productPriceDisplay($product_id,'0.00');
                $("#required_colorbox_price_"+$product_id).html('0.00');
            }

            $requiredTotal = 0.00;

            if($("#requiredColorboxProductDiv .required_colorbox_product").length > 0){
                $("#requiredColorboxProductDiv .required_colorbox_product").each(function(){
                    $product_id = $(this).attr('data-product-id');
                    $price = $("#required_colorbox_price_"+$product_id).html();

                    $requiredTotal = parseFloat($requiredTotal) + parseFloat($price);
                })
            }
            $("#requiredTotal").html(parseFloat($requiredTotal).toFixed(2));
        });
        
        $(document).off("click","#healthyStepConfirm");
        $(document).on("click","#healthyStepConfirm",function(e){
            $product_id = $(".healthyStep_button:checked").val();

            if($product_id > 0){
                quote_healthy_step_fee = $product_id;
                $("#healthy_step_fee").val($product_id);
                colorBoxClose();            
                addProductToCart($product_id,0,0,0,0);
            }
        });

        $(document).off("click","#healthyStepDetails");
        $(document).on("click","#healthyStepDetails",function(e){
            $healthyStepArr=[];
            $("#healthyStepMainDiv .healthyStep_button").each(function(){
                $healthyStepArr.push($(this).attr('data-healthy-step-id'));
            });
            if($healthyStepArr.length > 0){
                $id = $healthyStepArr.join("_");
                window.open("<?= $HOST ?>/member_enrollment_healthy_step_details.php?healthy_steps="+$id, "myWindow", "width=1024,height=767,toolbar=0");
            }
        });


        $(document).off("click","#cart_display_healthy_step");
        $(document).on("click","#cart_display_healthy_step",function(e){
            $is_manually_open_healthy_step = true;
            $is_new_healthy_step = true;
            displayHealthyStepFee();
        });

        $(document).off("change",".benefit_amount");
        $(document).on("change",".benefit_amount",function(e){
            $val = $(this).val();
            $enrolleeType = $(this).attr('data-enrollee-type');
            $product_id = $(this).attr('data-product-id');
            $number = $(this).attr('data-id');
            $elementName = $(this).attr('data-element');
            $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($val)
        });

        $(document).off("change",".in_patient_benefit");
        $(document).on("change",".in_patient_benefit",function(e){
            $val = $(this).val();
            $enrolleeType = $(this).attr('data-enrollee-type');
            $product_id = $(this).attr('data-product-id');
            $number = $(this).attr('data-id');
            $elementName = $(this).attr('data-element');
            $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($val)
        });

        $(document).off("change",".out_patient_benefit");
        $(document).on("change",".out_patient_benefit",function(e){
            $val = $(this).val();
            $enrolleeType = $(this).attr('data-enrollee-type');
            $product_id = $(this).attr('data-product-id');
            $number = $(this).attr('data-id');
            $elementName = $(this).attr('data-element');
            $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($val)
        });

        $(document).off("change",".monthly_income");
        $(document).on("change",".monthly_income",function(e){
            $val = $(this).val();
            $enrolleeType = $(this).attr('data-enrollee-type');
            $product_id = $(this).attr('data-product-id');
            $number = $(this).attr('data-id');
            $elementName = $(this).attr('data-element');
            $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($val)
        });

        $(document).off("change",".benefit_percentage");
        $(document).on("change",".benefit_percentage",function(e){
            $val = $(this).val();
            $enrolleeType = $(this).attr('data-enrollee-type');
            $product_id = $(this).attr('data-product-id');
            $number = $(this).attr('data-id');
            $elementName = $(this).attr('data-element');
            $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($val)
        });
        
        $(document).off("click",".riderProduct_colorbox_details");
        $(document).on("click",".riderProduct_colorbox_details",function(e){
            $product_id = $(this).attr('data-product-id');
            $("#show_details_on_new_tab_"+$product_id).trigger('click');
        });

        $(document).off("click",".riderProductApproved");
        $(document).on("click",".riderProductApproved",function(e){
            $product_id = $(this).attr('data-product-id');
            $price = $("#riderProduct_colorbox_price_"+$product_id).html();
            $riderProductTotal = $("#riderProductTotal").html();

            if($(this).hasClass('text-success')){
                $riderProductTotal = parseFloat($riderProductTotal) -  parseFloat($price);
            }else{
                $riderProductTotal = parseFloat($riderProductTotal) + parseFloat($price);
            }
            $("#riderProductTotal").html(parseFloat($riderProductTotal).toFixed(2));

            $(this).toggleClass('text-success');
            $(this).toggleClass('text-light-gray');

            $("#riderProductReject_"+$product_id).removeClass('text-action');
            $("#riderProductReject_"+$product_id).addClass('text-light-gray');
        });

        $(document).off("click",".riderProductReject");
        $(document).on("click",".riderProductReject",function(e){
            $product_id = $(this).attr('data-product-id');

            $price = $("#riderProduct_colorbox_price_"+$product_id).html();
            $riderProductTotal = $("#riderProductTotal").html();

            if($("#riderProductApproved_"+$product_id).hasClass('text-success')){
                $riderProductTotal = parseFloat($riderProductTotal) - parseFloat($price);
                $("#riderProductTotal").html(parseFloat($riderProductTotal).toFixed(2));
            }

            $(this).toggleClass('text-action');
            $(this).toggleClass('text-light-gray');

            $("#riderProductApproved_"+$product_id).removeClass('text-success');
            $("#riderProductApproved_"+$product_id).addClass('text-light-gray');
        });

        $(document).off("click","#riderProduct_colorbox_confirm");
        $(document).on("click","#riderProduct_colorbox_confirm",function(e){

            if($(".riderProductApproved.text-success").length > 0){
                $(".riderProductApproved.text-success").each(function(){
                    $product_id = $(this).attr('data-product-id');
                    $product_plan = $("#riderProduct_colorbox_plan_"+$product_id).val();
                    
                    if ($product_plan != "") {
                        $("#product_plan_"+$product_id).val($product_plan);
                        $("#product_plan_"+$product_id).selectpicker('refresh');

                        if(!$("#addCoverageButton_"+$product_id).hasClass('added_btn')){
                            $autoAssignedProductArr = [];
                            $("#addCoverageButton_"+$product_id).trigger('click');
                        }
                        $("#autoAssign_colorbox_product_"+$product_id).remove();
                        $("#required_colorbox_product_"+$product_id).remove();
                        $("#riderProduct_colorbox_product_"+$product_id).remove();
                        
                    }

                });
            }else{
                 if($(".riderProductReject.text-action").length > 0){
                    feeColorBoxClose();
                 }
            }
        });
       
        $(document).off("change",".riderProduct_colorbox_plan");
        $(document).on("change",".riderProduct_colorbox_plan",function(e){
            e.stopPropagation();
            $val = $(this).val();
            $product_id = $(this).attr('data-product-id');
            $price = $(this).find('option:selected').attr('data-price');
            $("#product_plan_"+$product_id).val($val);
            $("#product_plan_"+$product_id).selectpicker('refresh');

            if ($(this).val() != "") {
                productPriceDisplay($product_id,$price);
                $("#riderProduct_colorbox_price_"+$product_id).html($price);
            } else {
                productPriceDisplay($product_id,'0.00');
                $("#riderProduct_colorbox_price_"+$product_id).html('0.00');
            }

            $riderProductTotal = 0.00;

            if($(".riderProductApproved.text-success").length > 0){
                $(".riderProductApproved.text-success").each(function(){
                    $product_id = $(this).attr('data-product-id');
                    $price = $("#riderProduct_colorbox_price_"+$product_id).html();

                    $riderProductTotal = parseFloat($riderProductTotal) + parseFloat($price);
                })
            }
            $("#riderProductTotal").html(parseFloat($riderProductTotal).toFixed(2));

        });

        /*--------------- GAP Plus Product -------------*/
        $(document).off("change",".gap_payroll_type_radio");
        $(document).on("change",".gap_payroll_type_radio",function(){
            $val = $(this).val();
            $product_id = $(this).data('product_id');
            if($val == "Hourly"){
                $('.payroll_type_hourly_div_' + $product_id).show();
                $('.payroll_type_salary_div_' + $product_id).hide();
            }
            if($val == "Salary"){
                $('.payroll_type_salary_div_' + $product_id).show();   
                $('.payroll_type_hourly_div_' + $product_id).hide();   
            }
        });
        
        $(document).off("click",".remove_hourly_rate");
        $(document).on("click",".remove_hourly_rate",function(e){
            e.preventDefault();
            var $product_id = $(this).data('product_id');
            var $row_index = $(this).data('index');
            $(".hourly_rates_row_"+$product_id+"_"+$row_index).remove();

        });
        
        $(document).off("click",".add_hourly_rate");
        $(document).on("click",".add_hourly_rate",function(e){
            e.preventDefault();
            var $product_id = $(this).data('product_id');
            var $row_index = 1 + $(".hourly_rates_row_" + $product_id + ":last").data("index");
            if($row_index > 0) {
                var $is_error = false;
                var $row_index1 = $row_index - 1;
                if($("#gap_payroll_type_hourly_wage_primary_"+ $product_id +"_"+$row_index1).val() == "") {
                    $("#error_gap_payroll_type_hourly_wage_primary_"+ $product_id +"_"+$row_index1).html('Amount is required');
                    $is_error = true;
                }
                if($("#gap_payroll_type_hours_primary_"+ $product_id +"_"+$row_index1).val() == "") {
                    $("#error_gap_payroll_type_hours_primary_"+ $product_id +"_"+$row_index1).html('Hours is required');
                    $is_error = true;
                }
                if($is_error) {
                    return false;
                }
            }
            var $row_html = $(".hourly_rates_template").html();
            $row_html = $row_html.replace(/~product_id~/g, $product_id);
            $row_html = $row_html.replace(/~index~/g, $row_index);
            $(".payroll_type_hourly_rates_div_"+$product_id).append($row_html);
            
            $('#gap_payroll_type_hourly_wage_primary_'+$product_id+'_'+$row_index).priceFormat({
                prefix: '$',
                suffix: '',
                centsSeparator: '.',
                thousandsSeparator: ',',
                limit: false,
                centsLimit: 2,
            });
            $('#gap_payroll_type_hours_primary_'+$product_id+'_'+$row_index).priceFormat({
                prefix: '',
                suffix: '',
                centsSeparator: '.',
                thousandsSeparator: ',',
                limit: 2,
                centsLimit: 0,
            });
        });
        
        $(document).off("click",".add_deduction");
        $(document).on("click",".add_deduction",function(e){
            e.preventDefault();
            $('.deduct_table .error').html('');
            var $product_id = $(this).data('product_id');
            var $tax_type = $(this).data('tax_type');
            var $row_index = 1 + $("tr."+$tax_type+"_deduction_row_" + $product_id + ":last").data("index");
            if($row_index > 0) {
                var $is_error = false;
                var $row_index1 = $row_index - 1;
                if($("#"+$tax_type+"_deduction_name_"+ $product_id +"_"+$row_index1).val() == "") {
                    $("#error_"+$tax_type+"_deduction_name_"+ $product_id +"_"+$row_index1).html('Deduction Name is required');
                    $is_error = true;
                }
                if($("#"+$tax_type+"_deduction_method_"+ $product_id +"_"+$row_index1).val() == "") {
                    $("#error_"+$tax_type+"_deduction_method_"+ $product_id +"_"+$row_index1).html('Method is required');
                    $is_error = true;
                }
                if($("#"+$tax_type+"_deduction_amount_"+ $product_id +"_"+$row_index1).val() == "" || $("#"+$tax_type+"_deduction_amount_"+ $product_id +"_"+$row_index1).val() == 0) {
                    $("#error_"+$tax_type+"_deduction_amount_"+ $product_id +"_"+$row_index1).html('Amount is required');
                    $is_error = true;
                }
                if($is_error) {
                    return false;
                }
            }
            add_deduction_row($product_id,$row_index,$tax_type);
        });

        $(document).off("change",".deduction_method_select");
        $(document).on("change",".deduction_method_select",function(){
            var $product_id = $(this).data('product_id');
            var $tax_type = $(this).data('tax_type');
            var $row_index = $(this).data('index');
            var $deduction_method = $(this).val();
            $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).unpriceFormat();
            if($deduction_method == "fixed_amount") {
                $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).priceFormat({
                    prefix: '',
                    suffix: '',
                    centsSeparator: '.',
                    thousandsSeparator: ',',
                    limit: false,
                    centsLimit: 2,
                });
            }
            if($deduction_method == "gross_pay") {
                $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).priceFormat({
                    prefix: '',
                    suffix: '',
                    centsSeparator: '.',
                    thousandsSeparator: ',',
                    limit: 4,
                    centsLimit: 2,
                });
            }
        });

        $(document).off("keyup",".deduction_amount_input");
        $(document).on("keyup",".deduction_amount_input",function(e){
            var $product_id = $(this).data('product_id');
            var $row_index = $(this).data('index');
            var $tax_type = $(this).data('tax_type');
            calculate_tax_deduction_row($tax_type,$row_index,$product_id);
        });

        $(document).off("click",".deduction_remove_row");
        $(document).on("click",".deduction_remove_row",function(e){
            e.preventDefault();
            var $product_id = $(this).data('product_id');
            var $row_index = $(this).data('index');
            var $tax_type = $(this).data('tax_type');
            $("."+$tax_type+"_deduction_row_"+$product_id+"_"+$row_index).remove();
            calculate_tax_deduction_total($tax_type,$product_id);
        });

        $(document).off("click",".deduction_remove_row2");
        $(document).on("click",".deduction_remove_row2",function(e){
            e.preventDefault();
            var $product_id = $(this).data('product_id');
            var $row_index = $(this).data('index');
            var $tax_type = $(this).data('tax_type');
            if($tax_type == "pre_tax") {
                var deduction_data = pre_tax_deductions[$product_id];
                var deduction_data2 = [];
                $.each(deduction_data, function ($k, $v) {
                    if($row_index != $k) {
                        deduction_data2[$k] = $v;
                    }
                });
                pre_tax_deductions[$product_id] = deduction_data2;
                $(".pre_tax_deduction_row2_"+$product_id+"_"+$row_index).remove();
                if(deduction_data2.length > 0) {
                    $("#input_pre_tax_deductions_"+$product_id).val(JSON.stringify(deduction_data2));
                } else {
                    $("#input_pre_tax_deductions_"+$product_id).val('');
                }
            }
            if($tax_type == "post_tax") {
                var deduction_data = post_tax_deductions[$product_id];
                var deduction_data2 = [];
                $.each(deduction_data, function ($k, $v) {
                    if($row_index != $k) {
                        deduction_data2[$k] = $v;
                    }
                });
                post_tax_deductions[$product_id] = deduction_data2;
                $(".post_tax_deduction_row2_"+$product_id+"_"+$row_index).remove();
                if(deduction_data2.length > 0) {
                    $("#input_post_tax_deductions_"+$product_id).val(JSON.stringify(deduction_data2));
                } else {
                    $("#input_post_tax_deductions_"+$product_id).val('');
                }
            }
        });

        $(document).off("keyup",".gap_payroll_type_salary_input, .hourly_wage_input, .hours_input");
        $(document).on("keyup",".gap_payroll_type_salary_input, .hourly_wage_input, .hours_input",function(e){
            var $product_id = $(this).data('product_id');
            calculate_tax_deduction_by_product($product_id);
        });

        $(document).off("change","select.gap_pay_frequency_select");
        $(document).on("change","select.gap_pay_frequency_select",function(e){
            var $product_id = $(this).data('product_id');
            calculate_tax_deduction_by_product($product_id);
        });

        $(document).off("click",".deductions_done");
        $(document).on("click",".deductions_done",function(e){
            e.preventDefault();
            var $product_id = $(this).data('product_id');
            var $tax_type = $(this).data('tax_type');
            var $row_index1 = $("tr."+$tax_type+"_deduction_row_" + $product_id + ":last").data("index");
            $('.deduct_table .error').html('');
            if($row_index1 >= 0) {
                var $is_error = false;
                if($("#"+$tax_type+"_deduction_name_"+ $product_id +"_"+$row_index1).val() == "") {
                    $("#error_"+$tax_type+"_deduction_name_"+ $product_id +"_"+$row_index1).html('Deduction Name is required');
                    $is_error = true;
                }
                if($("#"+$tax_type+"_deduction_method_"+ $product_id +"_"+$row_index1).val() == "") {
                    $("#error_"+$tax_type+"_deduction_method_"+ $product_id +"_"+$row_index1).html('Method is required');
                    $is_error = true;
                }
                if($("#"+$tax_type+"_deduction_amount_"+ $product_id +"_"+$row_index1).val() == "" || $("#"+$tax_type+"_deduction_amount_"+ $product_id +"_"+$row_index1).val() == 0) {
                    $("#error_"+$tax_type+"_deduction_amount_"+ $product_id +"_"+$row_index1).html('Amount is required');
                    $is_error = true;
                }
                if($is_error) {
                    return false;
                }
            }
            display_tax_deduction($tax_type,$product_id);
            //$(".view_"+$tax_type+"_deduct_"+$product_id).popover('hide');
            $(".view_"+$tax_type+"_deduct_"+$product_id).trigger('click');
        });

        $(document).off("click",".close_deduction_popover");
        $(document).on("click",".close_deduction_popover",function(e){
            e.preventDefault();
            var $product_id = $(this).data('product_id');
            var $tax_type = $(this).data('tax_type');

            //$(".view_"+$tax_type+"_deduct_"+$product_id).popover('hide');
            $(".view_"+$tax_type+"_deduct_"+$product_id).trigger('click');
        });
        /*---------------/GAP Plus Product -------------*/

    //******************** Products tab Code end   *******************************
    
    //******************** Details tab Code start *******************************
        $(document).off("blur","#que_primary_email");
        $(document).on("blur","#que_primary_email",function(){
            $("#primary_email").val($("#que_primary_email").val());          
        });
        $(document).off("blur","#que_primary_fname");
        $(document).on("blur","#que_primary_fname",function(){
            $("#primary_fname").val($("#que_primary_fname").val());
        });

        /*$(document).off("blur","#que_primary_address1");
        $(document).on("blur","#que_primary_address1",function(){
            $("#ajax_loader").show();
            $(".error").html('');
            $.ajax({
                url:'<?= $HOST ?>/ajax_address_verification.php',
                data:$("#enrollment_form").serialize(),
                dataType:'JSON',
                type:'POST',
                success:function(res){
                    $("#ajax_loader").hide();
                    if(res.status=="success"){
                        $("#que_primary_address1").val(res.address);
                    }else{
                        $("#error_primary_address1").html(res.error);
                        $("#que_primary_address1").val('');
                    }
                }
            });
        });*/

        $(document).off("click",".removeChildField");
        $(document).on("click",".removeChildField",function(){
            $number = $(this).attr('data-id');

            $selected = $("#que_child_assign_products_"+$number).multipleSelect('getSelects');
            if($selected.length > 0){
                $.each($selected,function($k,$v){
                    $value = $v;
                    $productPlan = $("#product_plan_"+$value).val();
                    if($productPlan==5){

                        $("select.child_dependent_multiple_select").each(function(){
                            $childID = $(this).attr('data-id');
                            $("#que_child_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                            $("#que_child_assign_products_"+$childID).multipleSelect('refresh');
                        });

                        $("select.spouse_dependent_multiple_select").each(function(){
                            $childID = $(this).attr('data-id');
                            $("#que_spouse_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                            $("#que_spouse_assign_products_"+$childID).multipleSelect('refresh');
                        });
                          
                        
                    }
                });
            }

            $('#existing_child_dependent_'+$number).each(function(index,value) {
                var selected = $(this).find(":selected");
                $('.existing_child_dependent').find('option[value="'+selected.val()+'"]').show();
                $('.existing_child_dependent').find("option[value='']").show();
                $('.existing_child_dependent').selectpicker('refresh');

            });
            
            $removed_display_number = parseInt($("#dependent_number_"+$number).attr('data-display_number'));
            $("#inner_child_field_"+$number).remove();

            $('#enrollment_form .display_number').each(function(){
                $display_number = parseInt($(this).attr('data-display_number'));

                if($display_number > $removed_display_number){
                    $display_number = $display_number - 1;
                    $(this).attr('data-display_number',$display_number);
                    $(this).html($display_number);
                }
            });
            
            
        });

        $(document).off("click","#addSpouseField");
        $(document).on("click","#addSpouseField",function(e){
            spouse_field();
        });

        $(document).off("click","#removeSpouseField");
        $(document).on("click","#removeSpouseField",function(e){  
            $selected = $("#que_spouse_assign_products_0").multipleSelect('getSelects');
            if($selected.length > 0){
                $.each($selected,function($k,$v){
                    $value = $v;
                    $productPlan = $("#product_plan_"+$value).val();
                    if($productPlan==5){

                        $("select.child_dependent_multiple_select").each(function(){
                            $childID = $(this).attr('data-id');
                            $("#que_child_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                            $("#que_child_assign_products_"+$childID).multipleSelect('refresh');
                        });

                        $("select.spouse_dependent_multiple_select").each(function(){
                            $childID = $(this).attr('data-id');
                            $("#que_spouse_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                            $("#que_spouse_assign_products_"+$childID).multipleSelect('refresh');
                        });
                          
                        
                    }
                });
            }

            $("#addSpouseField").show();
            $("#dependent_spouse_main_div").html('');
        });

        $(document).off("click","#addChildField");
        $(document).on("click","#addChildField",function(e){
            child_field();
        });

        $(document).off("click","#addPrincipalBeneficiaryField");
        $(document).on("click","#addPrincipalBeneficiaryField",function(e){
            $allow_upto = $(this).attr('data-allow-upto');
            $count = $("#enrollment_form .inner_principal_beneficiary_field").length;

            $principal_beneficiary_count = $principal_beneficiary_count + 1;
            $display_number = $count + 1;

            $number = $principal_beneficiary_count;
            if($allow_upto == '' || $display_number <= $allow_upto){
                principal_beneficiary_field($number+"_"+$display_number);
            }
        });

        $(document).off("click","#addContingentBeneficiaryField");
        $(document).on("click","#addContingentBeneficiaryField",function(e){
            $allow_upto = $(this).attr('data-allow-upto');
            $count = $("#enrollment_form .inner_contingent_beneficiary_field").length;

            $contingent_beneficiary_count = $contingent_beneficiary_count + 1;
            $display_number = $count + 1;

            $number = $contingent_beneficiary_count;
            if($allow_upto == '' || $display_number <= $allow_upto){
                contingent_beneficiary_field($number+"_"+$display_number);
            }
        });

        $(document).off("click",".removePrincipalBeneficiaryField");
        $(document).on("click",".removePrincipalBeneficiaryField",function(){
            $number = $(this).attr('data-id');
            
            var principal_beneficiary_id = $("#principal_beneficiary_id_"+$number).val();

            if(principal_beneficiary_id > 0 && principal_beneficiary_id != '') {
                removeItemByKeyValue(quote_principal_beneficiary,'id',principal_beneficiary_id);
            }

            $removed_display_number = parseInt($("#principal_beneficiary_number_"+$number).attr('data-display_number'));
            $("#inner_principal_beneficiary_field_"+$number).remove();

            $('#enrollment_form .display_principal_beneficiary_number').each(function(){
                $display_number = parseInt($(this).attr('data-display_number'));

                if($display_number > $removed_display_number){
                    $display_number = $display_number - 1;
                    $(this).attr('data-display_number',$display_number);
                    $(this).html($display_number);
                }
                
            });

            if( $.trim( $('#principal_beneficiary_field_div').html() ).length  == 0){
                $("#is_principal_beneficiary").val('not_displayed');
            }
            
        });

        $(document).off("click",".removeContingentBeneficiaryField");
        $(document).on("click",".removeContingentBeneficiaryField",function(){
            $number = $(this).attr('data-id');
            
            var contingent_beneficiary_id = $("#contingent_beneficiary_id_"+$number).val();

            if(contingent_beneficiary_id > 0 && contingent_beneficiary_id != '') {
                removeItemByKeyValue(quote_contingent_beneficiary,'id',contingent_beneficiary_id);
            }

            $removed_display_number = parseInt($("#contingent_beneficiary_number_"+$number).attr('data-display_number'));
            $("#inner_contingent_beneficiary_field_"+$number).remove();

            $('#enrollment_form .display_contingent_beneficiary_number').each(function(){
                $display_number = parseInt($(this).attr('data-display_number'));

                if($display_number > $removed_display_number){
                    $display_number = $display_number - 1;
                    $(this).attr('data-display_number',$display_number);
                    $(this).html($display_number);
                }

            });
            if($.trim($('#contingent_beneficiary_field_div').html()).length  == 0){
                $("#is_contingent_beneficiary").val('not_displayed');
            }
        });

        $(document).off("change",".contingent_beneficiary_select");
        $(document).on("change",".contingent_beneficiary_select",function(e){
            e.stopPropagation();
            $number = $(this).attr('data-id');
            
            $depFname=$(this).find('option:selected').attr('data-fname');
            $depLname=$(this).find('option:selected').attr('data-lname');
            $depPhone=$(this).find('option:selected').attr('data-phone');
            $depEmail=$(this).find('option:selected').attr('data-email');
            $depSSN=$(this).find('option:selected').attr('data-ssn');
            $depType=$(this).find('option:selected').attr('data-type');
            $depAddress=$(this).find('option:selected').attr('data-address');
            $depFullName=$(this).find('option:selected').attr('data-full-name');
            $value=$(this).val();

            $("#contingent_queBeneficiaryFullName_"+$number).val($depFullName);
            $("#contingent_queBeneficiaryPhone_"+$number).val($depPhone);
            $("#contingent_queBeneficiaryEmail_"+$number).val($depEmail);
            $("#contingent_queBeneficiarySSN_"+$number).val($depSSN);
            $("#contingent_queBeneficiaryAddress_"+$number).val($depAddress);
            $("#contingent_queBeneficiaryRelationship_"+$number).val($depType);
            $("#contingent_queBeneficiaryRelationship_"+$number).selectpicker('refresh');

            $selected=$(this).attr('data-select-val');
            if($selected!=''){
                $(".contingent_beneficiary_select option[value="+$selected+"]").show();
                $(this).attr('data-select-val','');
            }

            if($value!=''){
                $(this).attr('data-select-val',$value);
                $(".contingent_beneficiary_select option[value="+ $value +"]").hide();
                $("#contingent_existing_dependent_"+$number+" option[value="+$value+"]").show();
            }
            $(".contingent_beneficiary_select").selectpicker('refresh');
            fRefresh();

        });

        $(document).off("change",".principal_beneficiary_select");
        $(document).on("change",".principal_beneficiary_select",function(e){
            e.stopPropagation();
            $number = $(this).attr('data-id');



            $depFname=$(this).find('option:selected').attr('data-fname');
            $depLname=$(this).find('option:selected').attr('data-lname');
            $depPhone=$(this).find('option:selected').attr('data-phone');
            $depEmail=$(this).find('option:selected').attr('data-email');
            $depSSN=$(this).find('option:selected').attr('data-ssn');
            $depType=$(this).find('option:selected').attr('data-type');
            $depAddress=$(this).find('option:selected').attr('data-address');
            $depFullName=$(this).find('option:selected').attr('data-full-name');
            $value=$(this).val();

            $("#principal_queBeneficiaryFullName_"+$number).val($depFullName);
            $("#principal_queBeneficiaryPhone_"+$number).val($depPhone);
            $("#principal_queBeneficiaryEmail_"+$number).val($depEmail);
            $("#principal_queBeneficiarySSN_"+$number).val($depSSN);
            $("#principal_queBeneficiaryAddress_"+$number).val($depAddress);
            $("#principal_queBeneficiaryRelationship_"+$number).val($depType);
            $("#principal_queBeneficiaryRelationship_"+$number).selectpicker('refresh');
            
            $selected=$(this).attr('data-select-val');
            
            if($selected!=''){
                $(".principal_beneficiary_select option[value='"+$selected+"']").show();
                $(this).attr('data-select-val','');
            }
            
            if($value!=''){
                $(this).attr('data-select-val',$value);   
                $(".principal_beneficiary_select option[value='"+$value+"']").hide();
                $("#principal_existing_dependent_"+$number+" option[value='"+$value+"']").show();
            }
            $(".principal_beneficiary_select").selectpicker('refresh');
            fRefresh();
            
            
            
        });

        
        
    //******************** Details tab Code end   *******************************
    
    //******************** Enroll tab Code start   *******************************
        $(document).off("change",".application_type");
        $(document).on('change','.application_type', function(){
            var application_type = $(this).val();
            $("#verification_option_html_div").hide();
            $("#phone_number_to").val($('#que_primary_phone').val());
            $("#phone_number_to").addClass('has-value');
            if(application_type != ""){
                $("#verification_option_html_div").show();
                if(application_type == 'member'){
                    $(".lead_sms_phoneno").html($('#primary_phone').val()); 
                    $("#upload_text_info").html('');
                    $("#physical_file_div").hide();
                    $('#member_submit_button').show();
                    $('#direct_member').hide();
                }else if(application_type == 'admin') {
                    $("#physical_file_div").show();
                    $("#physical_file_input").show();
                    $("#physical_voice_file_input").hide();
                    $('#member_submit_button').hide();
                    $('#direct_member').hide();
                } else if(application_type == 'voice_verification'){
                    $("#physical_file_div").show();
                    $("#physical_file_input").hide();
                    $("#physical_voice_file_input").show();
                    $('#member_submit_button').hide();
                    $('#direct_member').hide();
                } else {
                   $(".application_type").not('.js-switch').uniform();

                    $("#physical_file_div").hide();
                    $('#member_submit_button').hide();
                    $('#direct_member').show();
                    
                    resizeCanvas();
                    signaturePad = new SignaturePad(canvas);
                }


                if(application_type == 'member'){
                    $('.back_tab_button').hide();
                    $('.exit_enrollment_button').show();
                } else {
                    $('.exit_enrollment_button').hide();
                    $('.back_tab_button').show();
                }
                
            } else {
                $('#error_application_type').html('Verification option is required.').show();
            }
        });

        $(document).off("click","#bob_lead_enrollment_url,#bob_member_enrollment_url");
        $(document).on('click', '#bob_lead_enrollment_url,#bob_member_enrollment_url', function () {
            exit_by_system = true;
            window.location.href = $(this).attr("data-href");
        });

        $(document).off("click",".exit_enrollment_button");
        $(document).on('click', '.exit_enrollment_button', function () {
            swal({
                text: " You Want to Exit: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm"
            }).then(function () {
                exit_by_system = true;
                reload_page();
            }, function (dismiss) {

            });
        });

        $(document).off("change","#enroll_with_post_date");
        $(document).on('change', '#enroll_with_post_date', function () {
            if ($(this).is(':checked')) {
                $("#post_date_div").show();
            } else {
                $("#post_date_div").hide();
            }
        });

        $(document).off("click","#additional_voiceRecording");
        $(document).on("click","#additional_voiceRecording",function(){
            $counter = $("#total_voiceRecord").val();
            $counter = parseInt($counter) + 1;
            $("#total_voiceRecord").val($counter);

            voice_record_html = $('#voice_record_dynamic_div').html();
            voice_record_html = voice_record_html.replace(/~number~/g, $counter);

            $("#additional_voiceRecording_div").append(voice_record_html);
        });

        $(document).off("click",".remove_voiceRecording");
        $(document).on("click",".remove_voiceRecording",function(){
            $counter = $(this).attr('data-counter');
            $("#add_voiceRecord_"+$counter).remove();          
        });

        $(document).off("click",".terms_popup");
        $(document).on("click",".terms_popup",function(e){
            e.preventDefault();
            $product_list = $("#product_list").val();
            $tmpsponsor_id = '<?=md5($sponsor_id)?>';
            //$link = "<?=$HOST?>/verification_terms.php?product_list="+$product_list;
            $link = "<?=$HOST?>/verification_terms.php?display_member_terms=Y&sponsor_id="+$tmpsponsor_id;
            $.colorbox({
                href : $link,
                iframe: 'true', 
                width: '800px', 
                height: '585px'
            });   
        });

        $(document).off("click",".prd_terms_popup");
        $(document).on("click",".prd_terms_popup",function(e){
            e.preventDefault();
            $search_query = $(this).attr('data-desc');
            $link = "<?=$HOST?>/prd_term_popup.php?"+$search_query;
            $.colorbox({
                href : $link,
                iframe: 'true', 
                width: '800px', 
                height: '600px'
            });   
        });

        $(document).off("click",".prd_agreement_popup");
        $(document).on("click",".prd_agreement_popup",function(e){
            e.preventDefault();
            $product_list = $("#product_list").val();
            $link = "<?=$HOST?>/prd_agreement_popup.php?product_list="+$product_list;
            $.colorbox({
                href : $link,
                iframe: 'true', 
                width: '800px', 
                height: '585px'
            });   
        });

        $(document).off("click",".verification_terms");
        $(document).on("click",".verification_terms",function(e){
            e.preventDefault();
            $product_id = $(this).attr('data-product-id');
            $link = "<?=$HOST?>/prd_description_popup.php?product_id="+$product_id;
            $.colorbox({
                href : $link,
                iframe: 'true', 
                width: '800px', 
                height: '585px'
            });   
        });

        $(document).off('click','#product_terms_check_all');
        $(document).on('click','#product_terms_check_all',function(e){
            if($(this).is(":checked")){
                $(".product_terms_check").prop('checked',true);
            }else{
                $(".product_terms_check").prop('checked',false);
            }
            $("#product_terms_check_all").uniform();
            $(".product_terms_check").uniform();
        });

        $(document).off('click','.product_terms_check');
        $(document).on('click','.product_terms_check',function(){
            is_all_checked = true;
            $('.product_terms_check').each(function(){
                if(!$(this).is(':checked')){
                    is_all_checked = false;
                    $("#product_terms_check_all").prop('checked',false);
                    return false;
                }
            });
            if(is_all_checked){
                $('#product_terms_check_all').prop('checked',true);
            }
            $('#product_terms_check_all').uniform();
            $(this).uniform();
        });
        
        $(document).off("change","#sent_via");
        $(document).on('change','#sent_via',function(){
            method = $(this).val();   
            $(".lead_sms_phoneno").html($('#primary_phone').val());
            $("#email_name_to").val($('#primary_email').val());
            $("#email_name_to").addClass('has-value');
            $("#phone_number_to").val($('#que_primary_phone').val());
            $("#phone_number_to").addClass('has-value');
            // alert($('#que_primary_phone').val());
            if(method == "text"){
                $('.smstp').show();
                $('.emailtp').hide();
            } else if(method == "email"){
                $('.emailtp').show();
                $('.smstp').hide();
            } else if(method == "Both"){
                $('.emailtp').show();
                $('.smstp').show();
            }
        });
        var chars = jQuery("#sms_content").val().length;
        jQuery("#message1").text(160 - chars);

        jQuery("#sms_content").keyup(function (e) {
            var chars = jQuery(this).val().length;
            jQuery("#message1").text(160 - chars);

            if (chars > 160 || chars <= 0) {
                jQuery("#message1").addClass("minus");
                jQuery(this).css("text-decoration", "line-through");
            } else {
                jQuery("#message1").removeClass("minus");
                jQuery(this).css("text-decoration", "");
                e.preventDefault();
            }
        });
        
        $(document).off("click","#summary_display_healthy_step");
        $(document).on("click","#summary_display_healthy_step",function(e){
            $is_manually_open_healthy_step = true;
            $is_new_healthy_step = true;
            displayHealthyStepFee();
        });

        $(document).off("click","#edit_billing_address");
        $(document).on("click","#edit_billing_address",function(e){
            $.colorbox({
                inline : true,
                href : '#billing_address_popup',
                width: '580px', 
                height: '330px',
            });
        });

        $(document).off("click","#billing_save");
        $(document).on("click","#billing_save",function(e){
            $bill_name = $("#bill_name").val();
            $bill_address = $("#bill_address").val();
            $bill_address2 = $("#bill_address2").val();
            $bill_city = $("#bill_city").val();
            $bill_state = $("#bill_state").val();
            $bill_zip = $("#bill_zip").val();
            
            $("#display_bill_name").html($bill_name);
            $("#display_bill_address").html($bill_address);
            $("#display_bill_address2").html($bill_address2);
            $("#display_bill_city").html($bill_city);
            $("#display_bill_state").html($bill_state);
            $("#display_bill_zip").html($bill_zip);

            colorBoxClose();
        });

        $(document).off("click",".product_dependents");
        $(document).on("click",".product_dependents",function(e){
            $dependent_count = $(this).attr('data-dependent-count');
            $product_id = $(this).attr('data-product-id');
            $dependentJson = $("#dependent_array").val();

            $dependentArr = jQuery.parseJSON($dependentJson);

            if($dependent_count > 0){
                $rows = $dependentArr[$product_id]['dependent'];
                $("#dependent_edit_popup_body").html('');
                $productName = $("#product_name_"+$product_id).html();
                $("#dependent_edit_popup_product_name").html($productName);

                
                $dependentCount = 0;
                $.each($rows,function($k,$v){
                    $relation = $v['relation'];
                    $dep_relation = $v['dependent_relation_input'];
                    $depFname= $dep_relation+"_fname";
                    $depLname= $dep_relation+"_lname";
                    
                    $dep_name = $v[$depFname]+ " "+$v[$depLname];
                    $dep_birthdate =$v[$dep_relation+"_birthdate"];
                    dep_html = $('#dependent_edit_popup_dynamic_table').html();
                    dep_html = dep_html.replace(/~dep_relation~/g, $relation);
                    dep_html = dep_html.replace(/~dep_name~/g, $dep_name);
                    dep_html = dep_html.replace(/~dep_birthdate~/g, $dep_birthdate);
                    $("#dependent_edit_popup_body").append(dep_html);
                    $dependentCount = $dependentCount+1;
                });
                $("#dependent_edit_popup_count").html($dependentCount);

                $.colorbox({
                    inline : true,
                    href : '#dependent_edit_popup',
                    width: '580px', 
                    height: '460px',
                });
            }
        });

        $(document).off("click",".dependent_popup_edit_button");
        $(document).on("click",".dependent_popup_edit_button",function(e){
            colorBoxClose();
            $("#li_basic_detail").find("a").trigger("click");
        });


        $(document).off("click",".payment_method");
        $(document).on("click",".payment_method",function(e){
            $payment_method = $(this).attr('data-payment-method');
            $("#payment_mode").val($payment_method);
        });

        

        
    //******************** Enroll tab Code end   *******************************
    
    //******************** Button Click Code start *******************************
        $(document).off("click",".next_tab_button");
        $(document).on("click",".next_tab_button",function(){
            $step=$(this).attr('data-step');
            $("#dataStep").val($step);
            $("#submit_type").val('continue');
            $("#action").val('continue_application');
            $('.error ').html('');

            if($("input[name='application_type']:checked").val()=='member_signature'){
                if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
                    $("#hdn_signature_data").val(signaturePad.toDataURL());
                }
            }

            $("#enrollment_form").submit();           
            
        });

        $(document).off("click",".back_tab_button");
        $(document).on("click",".back_tab_button",function(){
            $step=$(this).attr('data-step');
            $("#dataStep").val($step);
            $("#submit_type").val('continue');
            $(".data_tab li.active").prev().find("a").trigger("click");
            $(".data_tab li.active").removeClass("disabled");
            $('html, body').animate({
              scrollTop: $('.data_tab').offset().top-100
            }, 1000);
        });

        $(document).off("click",".cancel_enrollment");
        $(document).on("click",".cancel_enrollment",function(){
            reload_page(true);
        });
        
        $(document).off("click","#btn_submit_application");
        $(document).on("click","#btn_submit_application",function(){
            $step=$(this).attr('data-step');
            $("#dataStep").val($step);
            $("#submit_type").val('Submit');
            $("#action").val('submit_application');
            $('.error ').html('');

            if($("input[name='application_type']:checked").val()=='member_signature'){
                if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
                    $("#hdn_signature_data").val(signaturePad.toDataURL());
                }
            }
            
            $("#enrollment_form").submit();
        });
    //******************** Button Click Code end   *******************************


    //******** Functions Code start *******************
        //*************** General Code start ***************************
            isNumber = function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    return false;
                }
                return true;
            }
            openOrderReceipt = function(res){
                $('#order_receipt_html').html(res.order_receipt);
                $.colorbox({
                    href: "#order_receipt_div",
                    inline: true, 
                    height: "550px", 
                    width: "980px", 
                    overlayClose:false,
                    escKey:false, 
                    closeButton:true
                });
            }
            colorBoxClose = function() {
                window.parent.$.colorbox.close();
            }

            scrollToElement = function(e) {
                add_scroll = 0;
                element_id = $(e).attr('id');
                if(both_button == element_id)
                    add_scroll = 50;
                var offset = $(e).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 200 + add_scroll;
                $('body,html').animate({
                    scrollTop: totalScroll
                }, 1200);

            }
        //*************** General Code end   ***************************

        //******************** Coverage Details tab Code start *******************************
            getExistingUserDate = function(){
                $customer_id = $("#customer_id").val();
                $("#ajax_loader").show();
                $.ajax({
                    url: '<?=$HOST?>/ajax_enrollment_existing_user_get_data.php?customer_id=' + $customer_id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide();
                        if (res.status == "success") {
                            var customer_row = res.customer_row;
                            $("#primary_fname").val(customer_row.fname);
                            $("#primary_lname").val(customer_row.lname);
                            $("#primary_phone").val(customer_row.cell_phone);
                            $("#primary_birthdate").val(customer_row.birth_date);
                            $("#primary_gender").val(customer_row.gender);
                            $("#primary_SSN").val(customer_row.ssn_itn_number);
                            $("#primary_address").val(customer_row.address);
                            $("#primary_address2").val(customer_row.address_2);
                            $("#primary_city").val(customer_row.city);
                            $("#primary_state").val(customer_row.state);
                            $("#primary_zip").val(customer_row.zip);
                        } else {
                            setNotifyError(res.error_message);
                        }
                        $.colorbox.close();
                    }
                });
            }
            addChildCoverage = function(){
                $count=$("#enrollment_form .childCoverageInnerDiv").length;
                $number=$count+1;

                html = $('#childCoverageDynamicDiv').html();
                html = html.replace(/~number~/g,$number);
                $('#childCoverageMainDiv').append(html);
                notAllowFututeDate("child_birthdate_"+$number);
                $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 
            }

            populate_product = function(){
                $(".error").html('');
                $("#product_category_div").html('');
                $("#product_filter").html('');
                $("#autoAssignColorboxProductDiv").html('');
                $("#requiredColorboxProductDiv").html('');
                $("#riderProductColorboxProductDiv").html('');
                $("#healthy_step_fee").val('0');
                $("#healthyStepMainDiv").html('');
                $enrolleeElements = [];
                $enrolleeElementsVal = {};
                if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
                    $enrolleeElementsVal = Object.assign({}, $tmpEnrolleeElementsVal);
                }
                $("#ajax_loader").show();
                $.ajax({
                    url: '<?=$HOST?>/ajax_populate_products.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide();

                        if(res.status == 'success'){
                            if(is_add_product == 1 && typeof(res.already_puchase_product) !== "undefined") {
                                already_puchase_product = res.already_puchase_product;
                                $("#already_puchase_product").val(already_puchase_product.join(','));
                            }
                            $("#zip").val(res.zip);
                            $("#display_email").val($("#primary_email").val());

                            $(".data_tab li.active").addClass("completed");
                            $(".data_tab li.active").next().find("a").trigger("click");
                            $(".data_tab li.active").removeClass("disabled");
                            $('html, body').animate({
                              scrollTop: $('.data_tab').offset().top-100
                            }, 1000);

                            $("#addToCartTable").html('');
                            $("#cart_sub_total").html('0.00');
                            $("#cart_sub_total_group_price").html('0.00');
                            $("#total_cart_sub_total").html('0.00');
                            $("#summary_member_rate_sub_total").html('0.00');
                            $("#summary_group_rate_sub_total").html('0.00');
                            $("#summary_sub_total").html('0.00');
                            $("#summary_member_rate_monthly_payment").html('0.00');
                            $("#cart_monthly_total").html('0.00');
                            $("#summary_group_rate_monthly_payment").html('0.00');
                            $("#cart_monthly_total_group_price").html('0.00');
                            $("#summary_monthly_payment").html('0.00');                            
                            $("#total_cart_monthly_total").html('0.00');                            
                            $("#cart_total_group_price").html('0.00');
                            $("#total_cart_total").html('0.00');
                            $("#cart_total").html('0.00');
                            $("#summary_member_rate_total").html('0.00');
                            $("#summary_group_rate_total").html('0.00');
                            $("#summary_total").html('0.00');


                            $("#cart_healthy_step_name").html('');
                            $("#summary_healthy_step_name").html('');
                            $("#cart_healthy_step_total").html('0.00');
                            $("#summary_healthy_step_total").html('0.00');
                            $(".cart_healthy_step_row").hide();
                            
                            $("#cart_service_fee_total").html('0.00');
                            $("#summary_service_fee_total").html('0.00');

                            $("#total_amount").html('0.00');
                            $("#cart_counter").html('0');

                            if(res.product_list){
                                var i=0;
                                $.each(res.product_list,function(key,product){
                                    i = i+1;
                                    $default_plan_id = product.default_plan_id;
                                    $category_id = product.category_id;
                                    $category_name = product.category_name;
                                    
                                    $product_id = product.product_id;
                                    $product_name = product.product_name;
                                    $product_code = product.product_code;
                                    $parent_product_id = product.parent_product_id;
                                    $company_id = product.company_id;
                                    $product_type = product.product_type;
                                    $is_add_on_product = product.is_add_on_product;
                                    $pricing_model = product.pricing_model;
                                    $is_short_term_disablity_product = product.is_short_term_disablity_product;
                                    $monthly_benefit_allowed = product.monthly_benefit_allowed;
                                    $percentage_of_salary = product.percentage_of_salary;
                                    
                                    $carrier_name = product.carrier_name;
                                    $member_payment_type = product.member_payment_type;
                                    $packaged_product_name = '';
                                    $packaged_products = '';

                                    if(res.combination_products){
                                        if(res.combination_products[$product_id]){
                                            if(res.combination_products[$product_id]['Packaged']){
                                                $packaged_product_name = res.combination_products[$product_id]['Packaged']['product_name'];
                                                $packaged_products = res.combination_products[$product_id]['Packaged']['product_id'];
                                            }
                                        }
                                    }

                                    category_html = $('#populateCategoryDynamicDiv').html();
                                    category_html = category_html.replace(/~category_id~/g, $category_id);
                                    category_html = category_html.replace(/~category_name~/g, $category_name);
                                   

                                    if($("#enrollment_form").find($("#category_"+$category_id)).length <= 0){

                                        $('#product_category_div').append(category_html);

                                            if(i == 1){
                                                $('#category_name_'+$category_id).removeClass("collapsed");
                                                $("#collapse_"+$category_id).addClass('panel-collapse collapse in');
                                            }
                                        
                                        $("#category_"+$category_id).show();
                                        
                                        $option_html='<option value="'+$category_id+'">'+$category_name+'</option>';
                                        $("#product_filter").append($option_html);
                                    }

                                    product_html = $("#populateProductDynamicDiv").html();
                                    
                                    product_html = product_html.replace(/~default_plan_id~/g, $default_plan_id);
                                    product_html = product_html.replace(/~category_id~/g, $category_id);
                                    product_html = product_html.replace(/~category_name~/g, $category_name);
                                    product_html = product_html.replace(/~product_id~/g, $product_id);

                                    product_html = product_html.split('~product_name~').join($product_name);

                                    product_html = product_html.replace(/~product_code~/g, $product_code);
                                    product_html = product_html.replace(/~parent_product_id~/g, $parent_product_id);
                                    product_html = product_html.replace(/~company_id~/g, $company_id);
                                    product_html = product_html.replace(/~product_type~/g, $product_type);
                                    product_html = product_html.replace(/~is_add_on_product~/g, $is_add_on_product);
                                    product_html = product_html.replace(/~carrier_name~/g, $carrier_name);
                                    product_html = product_html.replace(/~pricing_model~/g, $pricing_model);
                                    product_html = product_html.replace(/~packaged_product_name~/g, $packaged_product_name);
                                    product_html = product_html.replace(/~packaged_products~/g, $packaged_products);
                                    product_html = product_html.replace(/~member_payment_type~/g,$member_payment_type);

                                    
                                    $('#populateCategoryProductsMainDiv'+$category_id).append(product_html); 
                                    if(enrollmentLocation == "groupSide"){
                                        $(".waive_checkbox").uniform();   
                                    }
                                    if(product.Matrix){

                                        $.each(product.Matrix,function($priceType,$priceVal){
                                            $display_product_price = $priceVal.display_member_price;        
                                            $product_price = $priceVal.product_price;        
                                            $plan_name = $priceVal.plan_name;        
                                            $plan_id = $priceVal.plan_id;        
                                            $matrix_id = $priceVal.matrix_id;
                                            
                                            $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").attr('data-prd-matrix-id',$matrix_id);
                                            $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").attr('data-price',$product_price);
                                            $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").attr('data-display-price',$display_product_price);
                                            $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").show();
                                           

                                            if($default_plan_id==$plan_id){
                                               $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").attr('selected','selected');
                                               $("#product_plan_"+$product_id).addClass('has-value');
                                                productPriceDisplay($product_id,$display_product_price);
                                                $("#calculate_rate_plan_name_"+$product_id).html($plan_name);
                                            }
                                        });
                                        
                                    }else{
                                        if(product.Enrollee_Matrix){
                                            $.each(product.Enrollee_Matrix,function($priceType,$priceVal){
                                                $display_product_price = $priceVal.display_member_price;        
                                                $product_price = $priceVal.product_price;        
                                                $plan_name = $priceVal.plan_name;        
                                                $plan_id = $priceVal.plan_id;        
                                                $matrix_id = $priceVal.matrix_id;

                                                $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").attr('data-prd-matrix-id',$matrix_id);
                                                $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").attr('data-price',$product_price);
                                                $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").attr('data-display-price',$display_product_price);
                                                $("#product_plan_"+$product_id+" option[value=" + $plan_id + "]").show();

                                                productPriceDisplay($product_id,$display_product_price);
                                            });
                                        }

                                    }
                                    if(!$("#product_plan_"+$product_id).hasClass('form-control')){
                                        $("#product_plan_"+$product_id).addClass('form-control');
                                        $("#product_plan_"+$product_id).selectpicker({ 
                                            container: 'body', 
                                            style:'btn-select',
                                            noneSelectedText: '',
                                            dropupAuto:false,
                                        });

                                    }else{
                                        $("#product_plan_"+$product_id).selectpicker('refresh');
                                    }
                                    
                                    if($pricing_model != "FixedPrice"){
                                        $("#coverage_div_"+$product_id).hide();
                                        $("#calcualte_rate_div_"+$product_id).show();
                                        if($is_short_term_disablity_product == 'Y'){

                                            $('.max_benefit_amount_instruction_' + $product_id).show();;

                                            $('.max_benefit_percentage_' + $product_id).text($percentage_of_salary + '%');
                                            $('.max_benefit_amount_' + $product_id).text('$' + $monthly_benefit_allowed);

                                            $('.max_percentage_' + $product_id).text($percentage_of_salary + '%');
                                            // console.log($percentage_of_salary);
                                        }
                                    }

                                    if(res.combination_products){
                                        if(res.combination_products[$product_id]){
                                            if(res.combination_products[$product_id]['Packaged']){
                                                if(!$("#product_body_div_"+$product_id).hasClass('excluded_body')){
                                                    $("#product_body_div_"+$product_id).addClass('packaged_body');
                                                    $("#packaged_content_"+$product_id).show();
                                                }
                                            }
                                        }
                                    }

                                });
                                
                                if($("#category_addOnCategory").length > 0){
                                    $addOnProducts = $("#category_addOnCategory").detach();
                                    $('#product_category_div').append($addOnProducts);
                                    if(res.is_add_product == ''){
                                       $("#category_addOnCategory").hide(); 
                                    }
                                    if(res.addOnDisplay == 'false'){
                                       $("#category_addOnCategory").hide(); 
                                    }
                                }
                                if($("#product_filter option[value=addOnCategory]").length > 0){
                                    $addOnFilter =$("#product_filter option[value=addOnCategory]").detach();
                                    $('#product_filter').append($addOnFilter);
                                    $("#product_filter option[value=addOnCategory]").prop("disabled",true);
                                }

                                $("#product_filter").multipleSelect('refresh');

                                if(cart_products.length > 0) {
                                    addQuoteProductToCart(cart_products);
                                }
                                if($waive_coverage.length > 0){
                                    $.each($waive_coverage,function(wkey,waive_row) {
                                        wcat_id = waive_row.category_id;
                                        $("#waive_checkbox_"+wcat_id).parent().addClass('active');
                                        $("#waive_checkbox_"+wcat_id).prop('checked', true);
                                        $(".waive_coverage_reason[data-category_id='"+wcat_id+"'][value='"+waive_row.reason+"']").prop('checked',true);
                                        if(waive_row.reason == "Other"){
                                            $("#waive_coverage_other_reason_"+wcat_id).val(waive_row.other_reason);
                                        }
                                        waived_checkbox(wcat_id);
                                    });
                                }
                            }else{
                                $("#product_category_div").html('<div class="text-center no_prd_msg"> <p>Product Not Found</p></div>');

                                if(res.is_main_products == false){
                                    swal({   
                                            title: "",   
                                            text: "Based on current demographic and product elections, there are no products available to this member.",
                                            showConfirmButton: false,
                                            showCancelButton: true,
                                            cancelButtonText: 'Close'   
                                    });
                                }
                            }
                            
                            
                            
                        }else if(res.status == 'fail'){
                            if(typeof(res.existing_email) !== "undefined"){
                                if(res.existing_status == "bob_lead") {
                                    $("#bob_lead_enrollment_url").attr('data-href',res.enrollment_url);
                                }
                                if(res.existing_status == "bob_member") {
                                    $("#bob_member_enrollment_url").attr('data-href',res.enrollment_url);
                                }
                                $.colorbox({inline: true, href: "#" + res.existing_status, width: '530px', height: '250px'});
                            }else{
                                $.each(res.errors, function (index, error) {
                                    $('#error_' + index).html(error).show();
                                    scrollToElement($('#error_' + index));
                                });
                            }
                        }
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                });
            }

            cancel_forgot_password = function() {
                $(".forgot_password_section").hide();
                $(".login_section").slideDown();
            }

            forgot_password = function() {
                $(".login_section").hide();
                $(".forgot_password_section").slideDown();
            }
        //******************** Coverage Details tab Code end   *******************************

        //******************** Products tab Code start *******************************
            waived_checkbox = function($category_id){
                if($("#waive_checkbox_"+$category_id).is(":checked")){
                    $(".waive_products_"+$category_id).each(function(){
                        $product_id = $(this).val();
                        removeProductFromCart($product_id,'Default');
                        $("#product_body_div_"+$product_id).addClass('waived_body');
                        $("#waived_content_"+$product_id).show();
                    })
                    
                    
                } else {
                    $(".waive_products_"+$category_id).each(function(){
                        $product_id = $(this).val();
                        $("#product_body_div_"+$product_id).removeClass('waived_body');
                        $("#waived_content_"+$product_id).hide();
                    })
                }
                
                $(".waive_checkbox").not('.js-switch').uniform();
                $(".waive_coverage_reason").not('.js-switch').uniform();
                $primary_product_change = true;
            }
            filterCategory = function(){
                $selected=$("#product_filter").multipleSelect('getSelects');
                $(".category_div").hide();
                if($selected.length > 0){
                    $.each($selected,function($k,$v){
                        $("#category_"+$v).show();
                    });
                }else{
                    $(".category_div").show();
                }
            }
            addAdditionalQuestions = function($product_id,$pricing_model,$addType,$submitType,$load_enrollee_data,$matrix_id,$submitSubType=''){
                if(typeof($load_enrollee_data) === "undefined") {
                    $load_enrollee_data = false;
                }
                if($submitType=="addCoverage"){
                    removeProductFromCart($product_id,'');
                    productPriceDisplay($product_id,'0.00');
                }

                $(".calculate_rate_price_"+$product_id+"_Primary").html('0.00');
                $(".calculate_rate_price_"+$product_id+"_Spouse").html('0.00');
                $(".calculate_rate_price_"+$product_id+"_Child").html('0.00');
                $("#ajax_loader").show();
                $(".error").html('');
                $.ajax({
                    url:'<?= $HOST ?>/ajax_enrollment_calculate_rate.php?product='+$product_id+'&pricing_model='+$pricing_model+'&addType='+$addType+'&submitType='+$submitType+'&matrix_id='+$matrix_id+'&accepted='+is_amount_accepted+'&submitSubType='+$submitSubType,
                    dataType:'JSON',
                    data:$("#enrollment_form").serialize(),
                    type:'POST',
                    async:($load_enrollee_data == true?false:true),
                    success:function(res){
                        $("#ajax_loader").hide();
                        if($submitType=="displayQuestion"){
                            post_tax_deductions = [];
                            pre_tax_deductions = [];
                            if(res.data){
                                $.each(res.data,function($enrolleeType,$elementArrMain){
                                $.each($elementArrMain,function($elementArrMainKey,$elementArr){
                                        $EnrollmentType=$enrolleeType.charAt(0).toUpperCase() + $enrolleeType.slice(1);
                                        
                                        $count=$("#enrollment_form .productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType).length;
                                        $number=$count+1;

                                        html = $('#productQuestionDynamicDiv').html();
                                        html = html.replace(/~Enrollee_Type~/g,$EnrollmentType);
                                        html = html.replace(/~enrollee_type~/g,$enrolleeType);
                                        html = html.replace(/~product_id~/g,$product_id);
                                        html = html.replace(/~pricing_model~/g,$pricing_model);
                                        html = html.replace(/~number~/g,$number);

                                        if($enrolleeType=="child"){
                                            html = html.replace(/~child_counter~/g,$number);
                                        }else{
                                            html = html.replace(/~child_counter~/g,'');
                                        }
                                        
                                        
                                        $("#inner_calculate_rate_main_div_"+$product_id).append(html);
                                        
                                        if($enrolleeType=="child"){
                                            if($number==1){
                                                $("#addChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$number).show();
                                                $("#addChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$number+"_div").addClass('hline-title-btn');   
                                            }else{
                                                $("#removeChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$number).show();
                                                $("#addChildQuestion_"+$product_id+"_"+$enrolleeType+"_"+$number+"_div").addClass('hline-title-btn');   
                                            }
                                        }
                                        $("input[name='primary["+$product_id+"]["+$number+"][gender]']").prop('readonly',true);
                                        $("input[name='primary["+$product_id+"]["+$number+"][birthdate]']").prop('readonly',true);
                                        $("input[name='primary["+$product_id+"]["+$number+"][zip]']").prop('readonly',true);
                                       
                                        $.each($elementArr,function($elementName,$elementValue){
                                            
                                            
                                            if(typeof($elementValue) != "undefined" && $elementValue !== null) {
                                                $("#"+$enrolleeType+"_"+$elementName+"_div_"+$product_id+"_"+$number).show();
                                               
                                                if($elementName == "gender"){
                                                    $(".primary_gender").addClass('disabled');
                                                }
                                                
                                                $("#"+$enrolleeType+"_"+$elementName+"_div_"+$product_id+"_"+$number+" :input").removeAttr('disabled');
                                                
                                                $element = $enrolleeType+"_"+$elementName+"_"+$number;
                                                $elVal = $enrolleeElementsVal[$element];

                                                
                                                if($elementName=="gender" || $elementName == "smoking_status" || $elementName == "tobacco_status" || $elementName == "has_spouse"){
                                                    
                                                    $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").parent().removeClass('active');
                                                    $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").removeAttr('checked',true);
                                                    if($elVal == "" || $elVal==undefined){
                                                        $elVal=$("."+$enrolleeType+"_"+$elementName+"_"+$number+":checked").val();
                                                    }
                                                    if($elVal != '' && $elVal !=undefined){
                                                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]'][value='"+$elVal+"']").parent().addClass('active');
                                                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]'][value='"+$elVal+"']").attr('checked',true);

                                                        $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($elVal);

                                                        
                                                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").parent().attr('disabled','disabled');
                                                        
                                                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").prop('readonly',true);
                                                    }else{
                                                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").parent().removeAttr('disabled');
                                                        $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").prop('readonly',false);
                                                    }
                                                }else if($elementName=="height" || $elementName == "weight" || $elementName == "no_of_children"){
                                                    if($elVal == "" || $elVal==undefined){
                                                        $elVal=$("."+$enrolleeType+"_"+$elementName+"_"+$number).val();
                                                    }
                                                    if($elVal != '' && $elVal !=undefined){
                                                        $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").val($elVal);

                                                        $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($elVal);
                                                        $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").prop('disabled',true);         
                                                    }else{
                                                       
                                                       $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").prop('disabled',false);
                                                    }
                                                }else{
                                                    if($elVal == "" || $elVal==undefined){
                                                        $elVal=$("."+$enrolleeType+"_"+$elementName+"_"+$number).val();
                                                    }
                                                    if($elVal != '' && $elVal !=undefined){
                                                        $("."+$enrolleeType+"_"+$elementName+"_"+$number).val($elVal);
                                                        $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($elVal);
                                                        $(".coverage_form ."+$enrolleeType+"_"+$elementName+"_"+$number).prop('readonly',true);              
                                                    }else{
                                                       $(".coverage_form ."+$enrolleeType+"_"+$elementName+"_"+$number).prop('readonly',false);
                                                    }
                                                }
                                                
                                                if($elementName=="height" || $elementName == "weight" || $elementName == "no_of_children" || (jQuery.inArray($elementName,["benefit_amount","in_patient_benefit","out_patient_benefit","monthly_income","benefit_percentage"]) !== -1)){
                                                    if(jQuery.inArray($elementName,["benefit_amount","in_patient_benefit","out_patient_benefit","monthly_income","benefit_percentage"]) !== -1){
                                                        if($elementValue.length > 0){
                                                            if($elementName == 'benefit_percentage'){
                                                                $.each($elementValue,function($optionK,$optionV){
                                                                    $option_html='<option value="'+$optionV+'">'+$optionV+'%</option>';
                                                                    $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").append($option_html);
                                                                });
                                                            }else{
                                                                $.each($elementValue,function($optionK,$optionV){
                                                                    $option_html='<option value="'+$optionV+'">$'+$optionV+'</option>';
                                                                    $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").append($option_html);
                                                                });
                                                            }
                                                        }
                                                    }
                                                    $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").addClass("form-control");
                                                    $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").selectpicker({ 
                                                            container: 'body', 
                                                            style:'btn-select',
                                                            noneSelectedText: '',
                                                            dropupAuto:false,
                                                    });
                                                }
                                            }
                                        });
                                    });
                                });

                                if($load_enrollee_data == true) {
                                    if(typeof(primary_additional_data.fname) !== "undefined") {
                                        var primary_key = 1;
                                        $.each(primary_additional_data,function(column_key,column_value){
                                            if(column_key == "benefit_amount") {
                                                $.each(column_value,function(tmp_prd_id,tmp_benefit_amount){
                                                    if($product_id == tmp_prd_id && tmp_benefit_amount != '') {
                                                        if($('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][benefit_amount]"]').length > 0) {
                                                            $('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][benefit_amount]"]').val(tmp_benefit_amount);
                                                        }
                                                        $('select.benefit_amount[name="primary['+tmp_prd_id+']['+primary_key+'][benefit_amount]"]').val(tmp_benefit_amount);
                                                        $('select.benefit_amount[name="primary['+tmp_prd_id+']['+primary_key+'][benefit_amount]"]').selectpicker('refresh');
                                                    }                                                
                                                });
                                            }else if(column_key == "in_patient_benefit") {
                                                $.each(column_value,function(tmp_prd_id,tmp_in_patient_benefit){
                                                    if($product_id == tmp_prd_id && tmp_in_patient_benefit != '') {
                                                        if($('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][in_patient_benefit]"]').length > 0) {
                                                            $('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][in_patient_benefit]"]').val(tmp_in_patient_benefit);
                                                        }

                                                        $('select.in_patient_benefit[name="primary['+tmp_prd_id+']['+primary_key+'][in_patient_benefit]"]').val(tmp_in_patient_benefit);
                                                        $('select.in_patient_benefit[name="primary['+tmp_prd_id+']['+primary_key+'][in_patient_benefit]"]').selectpicker('refresh');
                                                    }                                                
                                                });
                                            }else if(column_key == "out_patient_benefit") {
                                                $.each(column_value,function(tmp_prd_id,tmp_out_patient_benefit){
                                                    if($product_id == tmp_prd_id && tmp_out_patient_benefit != '') {
                                                        if($('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][out_patient_benefit]"]').length > 0) {
                                                            $('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][out_patient_benefit]"]').val(tmp_out_patient_benefit);
                                                        }

                                                        $('select.out_patient_benefit[name="primary['+tmp_prd_id+']['+primary_key+'][out_patient_benefit]"]').val(tmp_out_patient_benefit);
                                                        $('select.out_patient_benefit[name="primary['+tmp_prd_id+']['+primary_key+'][out_patient_benefit]"]').selectpicker('refresh');
                                                    }                                                
                                                });
                                            }else if(column_key == "monthly_income") {
                                                $.each(column_value,function(tmp_prd_id,tmp_monthly_income){
                                                    if($product_id == tmp_prd_id && tmp_monthly_income != '') {
                                                        if($('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][monthly_income]"]').length > 0) {
                                                            $('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][monthly_income]"]').val(tmp_monthly_income);
                                                        }

                                                        $('select.monthly_income[name="primary['+tmp_prd_id+']['+primary_key+'][monthly_income]"]').val(tmp_monthly_income);
                                                        $('select.monthly_income[name="primary['+tmp_prd_id+']['+primary_key+'][monthly_income]"]').selectpicker('refresh');
                                                    }                                                
                                                });
                                            }else if(column_key == "benefit_percentage") {
                                                $.each(column_value,function(tmp_prd_id,tmp_benefit_percentage){
                                                    if($product_id == tmp_prd_id && tmp_benefit_percentage != '') {
                                                        if($('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][benefit_percentage]"]').length > 0) {
                                                            $('input[name="hidden_primary['+tmp_prd_id+']['+primary_key+'][benefit_percentage]"]').val(tmp_benefit_percentage);
                                                        }

                                                        $('select.benefit_percentage[name="primary['+tmp_prd_id+']['+primary_key+'][benefit_percentage]"]').val(tmp_benefit_percentage);
                                                        $('select.benefit_percentage[name="primary['+tmp_prd_id+']['+primary_key+'][benefit_percentage]"]').selectpicker('refresh');
                                                    }                                                
                                                });
                                            } else {
                                                if($('.hidden_primary_'+column_key+'_'+primary_key).length > 0) {
                                                    $('.hidden_primary_'+column_key+'_'+primary_key).val(column_value);
                                                }

                                                if($('.additional_question.primary_'+column_key+'_'+primary_key).length > 0) {
                                                    var tmp_control_type = $('.additional_question.primary_'+column_key+'_'+primary_key);                                                
                                                    if (tmp_control_type.is('input:text')) {
                                                        tmp_control_type.val(column_value);
                                                    
                                                    } else if (tmp_control_type.is('select')) {
                                                        tmp_control_type.val(column_value);
                                                        tmp_control_type.selectpicker('refresh');
                                                    } else if (tmp_control_type.is('input:checkbox') || tmp_control_type.is('input:radio')) {
                                                        $('.additional_question.primary_'+column_key+'_'+primary_key+'[value="'+column_value+'"]').trigger('click');
                                                        $('.additional_question.primary_'+column_key+'_'+primary_key+'[value="'+column_value+'"]').attr('checked','checked');
                                                    }
                                                }
                                            }
                                        });
                                    }                                   

                                    if(spouse_dep.length > 0) {
                                        $.each(spouse_dep,function(spouse_key,spouse_data) {
                                            spouse_key = spouse_key + 1;
                                            $.each(spouse_data,function(column_key,column_value){
                                                if(column_key == "benefit_amount") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_benefit_amount){
                                                        if($product_id == tmp_prd_id && tmp_benefit_amount != '') {
                                                            if($('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][benefit_amount]"]').length > 0) {
                                                                $('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][benefit_amount]"]').val(tmp_benefit_amount);
                                                            }

                                                            $('select.benefit_amount[name="spouse['+tmp_prd_id+']['+spouse_key+'][benefit_amount]"]').val(tmp_benefit_amount);
                                                            $('select.benefit_amount[name="spouse['+tmp_prd_id+']['+spouse_key+'][benefit_amount]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                }else if(column_key == "in_patient_benefit") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_in_patient_benefit){
                                                        if($product_id == tmp_prd_id && tmp_in_patient_benefit != '') {
                                                            if($('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][in_patient_benefit]"]').length > 0) {
                                                                $('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][in_patient_benefit]"]').val(tmp_in_patient_benefit);
                                                            }

                                                            $('select.in_patient_benefit[name="spouse['+tmp_prd_id+']['+spouse_key+'][in_patient_benefit]"]').val(tmp_in_patient_benefit);
                                                            $('select.in_patient_benefit[name="spouse['+tmp_prd_id+']['+spouse_key+'][in_patient_benefit]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                }else if(column_key == "out_patient_benefit") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_out_patient_benefit){
                                                        if($product_id == tmp_prd_id && tmp_out_patient_benefit != '') {
                                                            if($('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][out_patient_benefit]"]').length > 0) {
                                                                $('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][out_patient_benefit]"]').val(tmp_out_patient_benefit);
                                                            }

                                                            $('select.out_patient_benefit[name="spouse['+tmp_prd_id+']['+spouse_key+'][out_patient_benefit]"]').val(tmp_out_patient_benefit);
                                                            $('select.out_patient_benefit[name="spouse['+tmp_prd_id+']['+spouse_key+'][out_patient_benefit]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                }else if(column_key == "monthly_income") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_monthly_income){
                                                        if($product_id == tmp_prd_id && tmp_monthly_income != '') {
                                                            if($('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][monthly_income]"]').length > 0) {
                                                                $('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][monthly_income]"]').val(tmp_monthly_income);
                                                            }

                                                            $('select.monthly_income[name="spouse['+tmp_prd_id+']['+spouse_key+'][monthly_income]"]').val(tmp_monthly_income);
                                                            $('select.monthly_income[name="spouse['+tmp_prd_id+']['+spouse_key+'][monthly_income]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                }else if(column_key == "benefit_percentage") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_benefit_percentage){
                                                        if($product_id == tmp_prd_id && tmp_benefit_percentage != '') {
                                                            if($('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][benefit_percentage]"]').length > 0) {
                                                                $('input[name="hidden_spouse['+tmp_prd_id+']['+spouse_key+'][benefit_percentage]"]').val(tmp_benefit_percentage);
                                                            }

                                                            $('select.benefit_percentage[name="spouse['+tmp_prd_id+']['+spouse_key+'][benefit_percentage]"]').val(tmp_benefit_percentage);
                                                            $('select.benefit_percentage[name="spouse['+tmp_prd_id+']['+spouse_key+'][benefit_percentage]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                } else {
                                                    if(column_key == "birth_date") {
                                                        column_key = "birthdate";
                                                    }
                                                    if(column_key == "zip_code") {
                                                        column_key = "zip";
                                                    }
                                                    if(column_key == "smoke_use") {
                                                        column_key = "smoking_status";
                                                    }
                                                    if(column_key == "tobacco_use") {
                                                        column_key = "tobacco_status";
                                                    }
                                                    if(column_key == "employmentStatus") {
                                                        column_key = "employment_status";
                                                    }
                                                    if(column_key == "height_feet") {
                                                        column_key = "height";
                                                        column_value = column_value+"."+spouse_data.height_inches;
                                                    }

                                                    if($('.hidden_spouse_'+column_key+'_'+spouse_key).length > 0) {
                                                        $('.hidden_spouse_'+column_key+'_'+spouse_key).val(column_value);
                                                    }

                                                    if($('.additional_question.spouse_'+column_key+'_'+spouse_key).length > 0) {
                                                        var tmp_control_type = $('.additional_question.spouse_'+column_key+'_'+spouse_key);                                                
                                                        if (tmp_control_type.is('input:text')) {
                                                            tmp_control_type.val(column_value);
                                                        
                                                        } else if (tmp_control_type.is('select')) {
                                                            tmp_control_type.val(column_value);
                                                            tmp_control_type.selectpicker('refresh');
                                                        } else if (tmp_control_type.is('input:checkbox') || tmp_control_type.is('input:radio')) {
                                                            $('.additional_question.spouse_'+column_key+'_'+spouse_key+'[value="'+column_value+'"]').trigger('click');
                                                            $('.additional_question.spouse_'+column_key+'_'+spouse_key+'[value="'+column_value+'"]').attr('checked','checked');
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                    }

                                    if(child_dep.length > 0) {
                                        $.each(child_dep,function(child_key,child_data) {
                                            child_key = child_key + 1;
                                            if(child_key > 1) {
                                                $('.addChildQuestion[data-product-id="'+$product_id+'"][data-id="1"]').trigger('click');
                                            }

                                            $.each(child_data,function(column_key,column_value){
                                                if(column_key == "benefit_amount") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_benefit_amount){
                                                        if($product_id == tmp_prd_id && tmp_benefit_amount != '') {
                                                            if($('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][benefit_amount]"]').length > 0) {
                                                                $('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][benefit_amount]"]').val(tmp_benefit_amount);
                                                            }
                                                            $('select.benefit_amount[name="child['+tmp_prd_id+']['+child_key+'][benefit_amount]"]').val(tmp_benefit_amount);
                                                            $('select.benefit_amount[name="child['+tmp_prd_id+']['+child_key+'][benefit_amount]"]').selectpicker('refresh');
                                                        }
                                                    });
                                                }else if(column_key == "in_patient_benefit") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_in_patient_benefit){
                                                        if($product_id == tmp_prd_id && tmp_in_patient_benefit != '') {
                                                            if($('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][in_patient_benefit]"]').length > 0) {
                                                                $('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][in_patient_benefit]"]').val(tmp_in_patient_benefit);
                                                            }

                                                            $('select.in_patient_benefit[name="child['+tmp_prd_id+']['+child_key+'][in_patient_benefit]"]').val(tmp_in_patient_benefit);
                                                            $('select.in_patient_benefit[name="child['+tmp_prd_id+']['+child_key+'][in_patient_benefit]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                }else if(column_key == "out_patient_benefit") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_out_patient_benefit){
                                                        if($product_id == tmp_prd_id && tmp_out_patient_benefit != '') {
                                                            if($('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][out_patient_benefit]"]').length > 0) {
                                                                $('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][out_patient_benefit]"]').val(tmp_out_patient_benefit);
                                                            }

                                                            $('select.out_patient_benefit[name="child['+tmp_prd_id+']['+child_key+'][out_patient_benefit]"]').val(tmp_out_patient_benefit);
                                                            $('select.out_patient_benefit[name="child['+tmp_prd_id+']['+child_key+'][out_patient_benefit]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                }else if(column_key == "monthly_income") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_monthly_income){
                                                        if($product_id == tmp_prd_id && tmp_monthly_income != '') {
                                                            if($('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][monthly_income]"]').length > 0) {
                                                                $('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][monthly_income]"]').val(tmp_monthly_income);
                                                            }

                                                            $('select.monthly_income[name="child['+tmp_prd_id+']['+child_key+'][monthly_income]"]').val(tmp_monthly_income);
                                                            $('select.monthly_income[name="child['+tmp_prd_id+']['+child_key+'][monthly_income]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                }else if(column_key == "benefit_percentage") {
                                                    $.each(column_value,function(tmp_prd_id,tmp_benefit_percentage){
                                                        if($product_id == tmp_prd_id && tmp_benefit_percentage != '') {
                                                            if($('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][benefit_percentage]"]').length > 0) {
                                                                $('input[name="hidden_child['+tmp_prd_id+']['+child_key+'][benefit_percentage]"]').val(tmp_benefit_percentage);
                                                            }

                                                            $('select.benefit_percentage[name="child['+tmp_prd_id+']['+child_key+'][benefit_percentage]"]').val(tmp_benefit_percentage);
                                                            $('select.benefit_percentage[name="child['+tmp_prd_id+']['+child_key+'][benefit_percentage]"]').selectpicker('refresh');
                                                        }                                                
                                                    });
                                                } else {
                                                    if(column_key == "birth_date") {
                                                        column_key = "birthdate";
                                                    }
                                                    if(column_key == "zip_code") {
                                                        column_key = "zip";
                                                    }
                                                    if(column_key == "smoke_use") {
                                                        column_key = "smoking_status";
                                                    }
                                                    if(column_key == "tobacco_use") {
                                                        column_key = "tobacco_status";
                                                    }
                                                    if(column_key == "employmentStatus") {
                                                        column_key = "employment_status";
                                                    }
                                                    if(column_key == "height_feet") {
                                                        column_key = "height";
                                                        column_value = column_value+"."+child_data.height_inches;
                                                    }

                                                    if($('.hidden_child_'+column_key+'_'+child_key).length > 0) {
                                                        $('.hidden_child_'+column_key+'_'+child_key).val(column_value);
                                                    }

                                                    if($('.additional_question.child_'+column_key+'_'+child_key).length > 0) {
                                                        var tmp_control_type = $('.additional_question.child_'+column_key+'_'+child_key);                                                
                                                        if (tmp_control_type.is('input:text')) {
                                                            tmp_control_type.val(column_value);
                                                        
                                                        } else if (tmp_control_type.is('select')) {
                                                            tmp_control_type.val(column_value);
                                                            tmp_control_type.selectpicker('refresh');
                                                        } else if (tmp_control_type.is('input:checkbox') || tmp_control_type.is('input:radio')) {
                                                            $('.additional_question.child_'+column_key+'_'+child_key+'[value="'+column_value+'"]').trigger('click');
                                                            $('.additional_question.child_'+column_key+'_'+child_key+'[value="'+column_value+'"]').attr('checked','checked');
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                    }
                                }

                                $('#enrollment_form .dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 

                                $("#enrollment_form .formatPricing").priceFormat({
                                    prefix: '',
                                    suffix: '',
                                    centsSeparator: '.',
                                    thousandsSeparator: ',',
                                    limit: false,
                                    centsLimit: 2,
                                });
                                fRefresh();

                                if(res.is_short_term_disability_product == 'Y'){
                                    $('.primary_annual_salary_div_' + $product_id).show();
                                    $('.primary_monthly_benefit_percentage_div_' + $product_id).show();
                                    $('.monthly_benefit_amount_div_' + $product_id).show();

                                    $("#annual_salary_input_"+$product_id).removeAttr('readonly');
                                    $("#annual_salary_input_"+$product_id).val(res.annual_salary).addClass('has-value');
                                    $("input[name='primary["+$product_id+"][monthly_benefit_percentage]']").removeAttr('readonly');
                                    $("input[name='primary["+$product_id+"][monthly_benefit_percentage]']").val(res.salary_percentage);
                                    $("input[name='monthly_benefit_amount_"+$product_id+"']").val(res.db_monthly_benefit);
                                    $("#hidden_monthly_salary_percentage_"+$product_id).val(res.salary_percentage);
                                    $("#hidden_primary_"+$product_id+"_1_annual_salary").val(res.annual_salary);

                                    $('.spouse_annual_salary_div_' + $product_id).remove();
                                    $('.child_annual_salary_div_' + $product_id).remove();
                                    $('.spouse_monthly_benefit_percentage_div_' + $product_id).remove();
                                    $('.child_monthly_benefit_percentage_div_' + $product_id).remove();
                                    $('.spouse_monthly_benefit_amount_div_' + $product_id).remove();
                                    $('.child_monthly_benefit_amount_div_' + $product_id).remove();
                                }

                                if(res.is_gap_plus_product == 'Y'){
                                    var gp_row = res.gap_plus_details;
                                    if(gp_row.is_require_out_of_pocket_maximum == "Y") {
                                        $("#out_of_pocket_maximum_div_primary_" + $product_id).show();
                                    } else {
                                        $("#out_of_pocket_maximum_div_primary_" + $product_id).hide();
                                    }

                                    if(gp_row.is_set_default_out_of_pocket_maximum == "Y") {
                                        $("#out_of_pocket_maximum_primary_" + $product_id).val(gp_row.default_out_of_pocket_maximum);
                                        $("#out_of_pocket_maximum_primary_" + $product_id).addClass('has-value');

                                        $('#gap_benefit_amount_primary_' + $product_id).val(parseFloat(gp_row.default_out_of_pocket_maximum).toFixed(2));
                                        update_primary_benefit_amount($product_id,gp_row.default_out_of_pocket_maximum);
                                    }
                                    if(gp_row.out_of_pocket_maximum > 0) {
                                        $("#out_of_pocket_maximum_primary_" + $product_id).val(gp_row.out_of_pocket_maximum);
                                        $("#out_of_pocket_maximum_primary_" + $product_id).addClass('has-value');

                                        $('#gap_benefit_amount_primary_' + $product_id).val(parseFloat(gp_row.out_of_pocket_maximum).toFixed(2));
                                        update_primary_benefit_amount($product_id,gp_row.out_of_pocket_maximum);
                                    }

                                    $('#out_of_pocket_maximum_primary_'+$product_id).priceFormat({
                                        prefix: '',
                                        suffix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: '',
                                        limit: false,
                                        centsLimit: 2,
                                    });
                                    
                                    $(".gap_minimum_benefit_amount_label_" + $product_id).html('$' + parseFloat(gp_row.minimum_benefit_amount).toFixed(2));
                                    $(".gap_maximum_benefit_amount_label_" + $product_id).html('$' + parseFloat(gp_row.maximum_benefit_amount).toFixed(2)); 

                                    benefits = JSON.parse(res.benefitAmountArr);
                                    benefitAmount = [];
                                    if(benefits.length > 0){
                                        $(benefits).each(function(k,v){
                                            benefitAmount[k] = parseFloat(v);
                                        });
                                    } else {
                                        benefitAmount=[0];
                                    }
                                    
                                    $range = $("#gap_benefit_amount_slider_primary_" + $product_id);
                                    $range.ionRangeSlider({
                                        type: "single",
                                        skin: "round",
                                        grid: false,
                                        values: benefitAmount,
                                        prefix: "$",
                                        onStart: function (value) {
                                            $('#gap_benefit_amount_primary_' + $product_id).val(parseFloat(value.from_value).toFixed(2));
                                            $('#gap_benefit_amount_primary_'+$product_id).priceFormat({
                                                prefix: '$',
                                                suffix: '',
                                                centsSeparator: '.',
                                                thousandsSeparator: ',',
                                                limit: false,
                                                centsLimit: 2,
                                            });
                                            update_primary_benefit_amount($product_id,parseFloat(value.from_value));
                                        },
                                        onChange: function(value) {
                                            $('#gap_benefit_amount_primary_' + $product_id).val(parseFloat(value.from_value).toFixed(2));
                                            $('#gap_benefit_amount_primary_'+$product_id).priceFormat({
                                                prefix: '$',
                                                suffix: '',
                                                centsSeparator: '.',
                                                thousandsSeparator: ',',
                                                limit: false,
                                                centsLimit: 2,
                                            });
                                            update_primary_benefit_amount($product_id,parseFloat(value.from_value));
                                        },
                                        onUpdate : function(value){
                                            $('#gap_benefit_amount_primary_' + $product_id).val(parseFloat(value.from_value).toFixed(2));
                                        }
                                    });

                                    slider_instance = $range.data("ionRangeSlider");

                                    if(gp_row.is_require_out_of_pocket_maximum == "Y" && gp_row.is_set_default_out_of_pocket_maximum == "Y" && benefitAmount.length > 0) {
                                        indx = -1;
                                        tmp_values = [];
                                        $.each(benefitAmount,function(ind,val){
                                            if(parseInt(val) <= parseInt(gp_row.default_out_of_pocket_maximum)){
                                                indx = ind;
                                                tmp_values.push(val);
                                            }
                                        });
                                        if(indx >= 0) {
                                            slider_instance.update({
                                                values:tmp_values,
                                                from:indx,
                                                to:indx
                                            });
                                            $(".gap_maximum_benefit_amount_label_" + $product_id).html('$' + parseFloat(gp_row.default_out_of_pocket_maximum).toFixed(2));
                                        } else {
                                            slider_instance.update({
                                                from:-1,
                                                hide_from_to:true,
                                                disable:true
                                            });
                                            $(".gap_minimum_benefit_amount_label_" + $product_id).html('-');
                                            $(".gap_maximum_benefit_amount_label_" + $product_id).html('-');
                                        }
                                    }
                                    
                                    $('#out_of_pocket_maximum_primary_'+$product_id).on('keyup',function(){                                        
                                        $max = $(this).val();
                                        if(parseInt($max) > parseInt(gp_row.maximum_benefit_amount)){
                                            $max = gp_row.maximum_benefit_amount;
                                            $('#out_of_pocket_maximum_primary_'+$product_id).val($max);
                                            
                                        }

                                        indx = -1;
                                        if(benefitAmount.length > 0) {
                                            tmp_values = [];
                                            $.each(benefitAmount,function(ind,val){
                                                if(parseInt(val) <= parseInt($max)){
                                                    tmp_values.push(val);
                                                }
                                            });
                                            if(tmp_values.length > 0) {
                                                $.each(tmp_values,function(ind,val){
                                                    if(parseInt(val) <= parseInt($max)){
                                                        indx = ind;
                                                    }
                                                });
                                            }
                                        }
                                        if(indx >= 0) {
                                            slider_instance.update({
                                                hide_from_to:false,
                                                disable:false,
                                                values:tmp_values,
                                                to:indx
                                            });
                                            $(".gap_minimum_benefit_amount_label_" + $product_id).html('$' + parseFloat(gp_row.minimum_benefit_amount).toFixed(2));
                                            $(".gap_maximum_benefit_amount_label_" + $product_id).html('$' + parseFloat($max).toFixed(2));

                                            $('#gap_benefit_amount_primary_' + $product_id).val(parseFloat(tmp_values[slider_instance.options.from]).toFixed(2));
                                            update_primary_benefit_amount($product_id,tmp_values[slider_instance.options.from]);
                                        } else {
                                            slider_instance.update({
                                                hide_from_to:true,
                                                disable:true
                                            });
                                            $(".gap_minimum_benefit_amount_label_" + $product_id).html('-');
                                            $(".gap_maximum_benefit_amount_label_" + $product_id).html('-');

                                            $('#gap_benefit_amount_primary_' + $product_id).val(parseFloat(0).toFixed(2));
                                            update_primary_benefit_amount($product_id,0);
                                        }
                                        $(this).focus();
                                    });

                                    $('#gap_benefit_amount_primary_'+$product_id).priceFormat({
                                        prefix: '$',
                                        suffix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ',',
                                        limit: false,
                                        centsLimit: 2,
                                    });

                                    if(typeof(primary_additional_data.benefit_amount) !== "undefined") {
                                        $.each(primary_additional_data.benefit_amount,function(tmp_prd_id,tmp_benefit_amount){
                                            if($product_id == tmp_prd_id && tmp_benefit_amount != "") {
                                                update_primary_benefit_amount($product_id,parseFloat(tmp_benefit_amount).toFixed(2));
                                            }
                                        });
                                    }

                                    $('.gap_radio_input_'+$product_id).uniform();
                                    $('.gap_select_input_'+$product_id).addClass('form-control');
                                    $('.gap_select_input_'+$product_id).selectpicker({ 
                                        container: 'body', 
                                        style:'btn-select',
                                        noneSelectedText: '',
                                        dropupAuto:false,
                                    });
                                    $('#gap_payroll_type_hourly_wage_primary_'+$product_id+'_0').priceFormat({
                                        prefix: '$',
                                        suffix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ',',
                                        limit: false,
                                        centsLimit: 2,
                                    });
                                    $('#gap_payroll_type_hours_primary_'+$product_id+'_0').priceFormat({
                                        prefix: '',
                                        suffix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ',',
                                        limit: 2,
                                        centsLimit: 0,
                                    });
                                    $('#gap_payroll_type_salary_primary_'+$product_id).priceFormat({
                                        prefix: '$',
                                        suffix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ',',
                                        limit: false,
                                        centsLimit: 0,
                                    });

                                    $('.view_pre_tax_deduct_'+$product_id).popover({
                                        html: true,
                                        container: 'body',
                                        trigger: 'click',
                                        title: '&nbsp;',
                                        template: '<div class="popover gaptax_deduct_popover"><div class="gaptax_popover_head"></div><div class="popover-content"></div></div>',
                                        placement: 'bottom',
                                        content: function() {
                                            var popover_content = $('#pretax_deduct_popover_content').html();
                                            popover_content = popover_content.replace(/~product_id~/g,$product_id);
                                            return popover_content;
                                        }
                                    }).on('shown.bs.popover', function() {
                                        if(pre_tax_deductions.length > 0 && pre_tax_deductions[$product_id].length > 0) {
                                            var deduction_data = pre_tax_deductions[$product_id];
                                            var is_row_added = false;
                                            $.each(deduction_data, function ($k, $v) {
                                                if(typeof($v) !== "undefined") {
                                                    add_deduction_row($product_id,$k,'pre_tax',$v);
                                                    is_row_added = true;
                                                }
                                            });
                                            if(is_row_added == false) {
                                                add_deduction_row($product_id,0,'pre_tax');
                                            } else {
                                                calculate_tax_deduction_total('pre_tax',$product_id);
                                            }
                                        } else {
                                            add_deduction_row($product_id,0,'pre_tax');
                                        }
                                    });

                                    $('.view_post_tax_deduct_'+$product_id).popover({
                                        html: true,
                                        container: 'body',
                                        trigger: 'click',
                                        title: '&nbsp;',
                                        template: '<div class="popover gaptax_deduct_popover"><div class="gaptax_popover_head"></div><div class="popover-content"></div></div>',
                                        placement: 'bottom',
                                        content: function() {
                                            var popover_content = $('#posttax_deduct_popover_content').html();
                                            popover_content = popover_content.replace(/~product_id~/g,$product_id);
                                            return popover_content;
                                        }
                                    }).on('shown.bs.popover', function() {
                                        if(post_tax_deductions.length > 0 && post_tax_deductions[$product_id].length > 0) {
                                            var deduction_data = post_tax_deductions[$product_id];
                                            var is_row_added = false;
                                            $.each(deduction_data, function ($k, $v) {
                                                if(typeof($v) !== "undefined") {
                                                    add_deduction_row($product_id,$k,'post_tax',$v);
                                                    is_row_added = true;
                                                }
                                            });
                                            if(is_row_added == false) {
                                                add_deduction_row($product_id,0,'post_tax');
                                            } else {
                                                calculate_tax_deduction_total('post_tax',$product_id);
                                            }
                                        } else {
                                            add_deduction_row($product_id,0,'post_tax');
                                        }
                                    });
                                }

                                $percentage = $('.max_benefit_percentage_' + $product_id).text();
                                $('.max_percentage_' + $product_id).text($percentage);
                                $percentage = $percentage.replace('%','');
                                $('#monthly_salary_percentage_' + $product_id).attr('max',$percentage);

                                $(".rangeslider_" + $product_id).asRange({
                                    format: function(value) {
                                        return value + '%';
                                    },
                                    onChange: function(value) {
                                        $('#hidden_monthly_salary_percentage_' + $product_id).val(value);
                                        $('.selected_percentage_div_' + $product_id).text(value + '%');
                                        $anu_sal = $('#hidden_primary_'+$product_id+'_1_annual_salary').val();
                                        if($anu_sal){
                                            calculateSTDRate($anu_sal,value,$product_id);
                                        }
                                    }
                                });

                                $('#annual_salary_input_' + $product_id).on('keyup',function(){
                                    $percentage = $('#hidden_monthly_salary_percentage_' + $product_id).val();
                                    $anu_sal = $(this).val();
                                    calculateSTDRate($anu_sal,$percentage,$product_id);

                                });

                                $('.monthly_benefit_amount_input').on('keyup',function(){
                                    $product_id = $(this).data('product_id');
                                    $annual_sal = $('#hidden_primary_'+$product_id+'_1_annual_salary').val();
                                    $mon_ben_amount = $(this).val();
                                    calculateSTDRate($annual_sal,0,$product_id,$mon_ben_amount,true);
                                });

                                if(res.adjusted_percentage > 0){
                                    $('.rangeslider_' + $product_id).asRange('set', res.adjusted_percentage);
                                }

                            }else{
                                if(res.is_short_term_disability_product == 'Y'){
                                    $('.primary_annual_salary_div_' + $product_id).show();
                                    $('.primary_monthly_benefit_percentage_div_' + $product_id).show();

                                    $('.spouse_annual_salary_div_' + $product_id).remove();
                                    $('.child_annual_salary_div_' + $product_id).remove();
                                    $('.spouse_monthly_benefit_percentage_div_' + $product_id).remove();
                                    $('.child_monthly_benefit_percentage_div_' + $product_id).remove();
                                    $('.spouse_monthly_benefit_amount_div_' + $product_id).remove();
                                    $('.child_monthly_benefit_amount_div_' + $product_id).remove();
                                }
                            }

                        }else{
                            $productPrice= $("#product_price_"+$product_id).val();
                            $displayProductPrice= $("#product_price_"+$product_id).val();
                            $matrix_id_arr= [];
                            if(res.enrollee){
                                $.each(res.enrollee,function($enrolleeType,$priceArr){
                                    $.each($priceArr,function($k,$v){
                                        $productPrice = parseFloat($productPrice) + parseFloat($v.price);
                                        $displayProductPrice = parseFloat($displayProductPrice) + parseFloat($v.display_member_price);
                                        $v_price = parseFloat($v.display_member_price).toFixed(2);
                                        $monthly_benefit = parseFloat($v.monthly_benefit).toFixed(2);
                                        $matrix_id_arr.push($v.matrix_id);
                                        $("#calculate_rate_price_"+$product_id+"_"+$enrolleeType+"_"+$k).html($v_price);
                                        $(".monthly_benefit_amount_"+$product_id).val($monthly_benefit);
                                    });
                                });
                                // if(res.adjusted_percentage > 0 && res.accepted == 'Y'){
                                //     $('.rangeslider_' + $product_id).asRange('set', res.adjusted_percentage);
                                // }
                                if($submitType=="addCoverage"){
                                    addProductToCart($product_id,$productPrice,$matrix_id_arr,$pricing_model,$displayProductPrice);
                                }
                                productPriceDisplay($product_id,$displayProductPrice);

                                if(typeof(res.gapCalculationRes) !== 'undefined'){
                                    if(typeof(res.gapCalculationRes['calculation_data']) !== 'undefined'){
                                        $calculation_data = res.gapCalculationRes['calculation_data'];
                                        $.each(res.gapCalculationRes['calculation_data'],function(ind,val){
                                            $("."+ind + "_primary_" + $product_id).html(val);
                                        });
                                    }
                                    if(typeof(res.gapCalculationRes['curl_response']) !== 'undefined'){
                                        if(typeof(res.gapCalculationRes['curl_response']['errors']) !== 'undefined'){
                                            $tmpErrors = "";
                                            $.each(res.gapCalculationRes['curl_response']['errors'],function(ind,val){
                                                $tmpErrors += val.message;
                                            });
                                            $('.gap_input_errors_' + $product_id).html($tmpErrors);
                                        }
                                    }
                                }
                                if(typeof(res.savings_recommend_text) !== 'undefined'){
                                    if(res.savings_recommend_text == "custom_recommendation") {
                                        $(".savings_recommend_text").hide();
                                        $(".custom_savings_recommend_text").html(res.custom_savings_recommend_text).show();
                                    } else {
                                        $(".custom_savings_recommend_text").hide();
                                        $(".savings_recommend_text").show();
                                        if(typeof(res.saving_details) !== 'undefined'){
                                            $.each(res.saving_details,function(ind,val){
                                                $("."+ind + "_" + $product_id).text(val);
                                            });
                                        }
                                    }
                                }

                            }else{
                                if(res.error_display){
                                    $("#error_add_coverage_"+$product_id).html(res.error_display);
                                    if(res.amount_limit_error === true){
                                        if(!res.adjusted_member_price){
                                            $("#error_add_coverage_"+$product_id).append("<br>Please enter valid annual salary");
                                        }else{
                                            swal({
                                                text: res.amount_limit_error_text,
                                                showCancelButton: true,
                                                confirmButtonText: "Accept"
                                            }).then(function () {
                                                is_amount_accepted = 'Y';
                                                calculateSTDRate(res.annual_salary,0,$product_id,res.monthly_benefit_allowed);
                                                
                                                is_amount_accepted = 'N';

                                                $displayProductPrice = parseFloat(res.adjusted_member_price);

                                                $("#calculate_rate_price_"+$product_id).html(parseFloat($displayProductPrice).toFixed(2));
                                                $("#calculate_rate_price_"+$product_id+"_Primary_1").html(parseFloat($displayProductPrice).toFixed(2));
                                                $("#product_price_label_"+$product_id).html(parseFloat($displayProductPrice).toFixed(2));
                                                $("#error_add_coverage_"+$product_id).html('');
                                            }, function (dismiss) {

                                            });
                                        }
                                    }
                                }else{
                                    if(typeof(res.submitSubType) !== "undefined" && res.submitSubType == 'calculateGapRate'){
                                        $('.gap_input_errors_' + $product_id).html('Criteria selection required for rates and coverage');
                                    }
                                    $("#error_add_coverage_"+$product_id).html('Criteria selection required for rates and plan');
                                }
                            }
                        }
                        $('[data-toggle="popover"]').popover();
                        
                    }
                });
            }

            productPriceDisplay = function($product_id,$price){
                $("#calculate_rate_price_"+$product_id).html(parseFloat($price).toFixed(2));
                $("#product_price_label_"+$product_id).html(parseFloat($price).toFixed(2));
            }

            addProductToCart = function($product_id,$price,$matrix_id,$pricing_model,$display_price){
                $price = parseFloat($price).toFixed(2);
                $display_price = parseFloat($display_price).toFixed(2);
                $("#product_price_"+$product_id).val($price);
                $("#display_product_price_"+$product_id).val($display_price);
                $("#product_matrix_"+$product_id).val($matrix_id);
                $("#added_product").val($product_id);
                $category_id = $("#product_plan_"+$product_id).attr('data-category-id');
                $("#product_category_"+$product_id).val($category_id);

                if($pricing_model=="FixedPrice"){
                    $("#addCoverageButton_"+$product_id).removeClass('addCoverage');
                    $("#addCoverageButton_"+$product_id).addClass('added_btn');
                    $("#addCoverageButton_"+$product_id).html('Added');
                }else{
                    $("#calculateRateButton_"+$product_id).removeClass('cancelCalculateRate');
                    $("#calculateRateButton_"+$product_id).removeClass('calculateRate');
                    $("#calculateRateButton_"+$product_id).addClass('added_btn');
                    $("#calculateRateButton_"+$product_id).html('Added');
                    $("#addCalculatedCoverage_"+$product_id).attr('disabled','disabled');
                    $("#product_calcualate_rate_"+$product_id).collapse("hide");
                    if($matrix_id != 0) {
                        $matrix_id = $matrix_id.join(',');    
                    }
                }

                $prd_plan_type_id = $("#product_plan_"+$product_id).val();
                var cart_product =  {product_id: $product_id, price: $price, matrix_id: $matrix_id, prd_plan_type_id: $prd_plan_type_id, pricing_model: $pricing_model};
                cart_products.push(cart_product);

                cartTotalCalculate($product_id,'add');

                if($price==0 && $matrix_id==0 && $pricing_model==0){

                }else{
                    $primary_product_change=true;
                    $spouse_product_change=true;
                    $verification_option_product_change=true;
                }
                
            }

            addQuoteProductToCart = function(cart_products){
                $("#ajax_loader").show();
                var added_product = '';

                $.each(cart_products,function(key,quote_prd_row) {
                    $product_id = quote_prd_row.product_id;
                    $price = parseFloat(quote_prd_row.price).toFixed(2);
                    $matrix_id = quote_prd_row.matrix_id;
                    $prd_plan_type_id = quote_prd_row.prd_plan_type_id;
                    $pricing_model = quote_prd_row.pricing_model;
                    $prd_is_short_term_disablity_product = quote_prd_row.is_short_term_disablity_product;
                    if(enrollmentLocation == "groupSide"){
                        $prd_category_id = quote_prd_row.category_id;
                    }

                    if($("#addCoverageButton_"+$product_id).length > 0 || $("#calculateRateButton_"+$product_id).length > 0) {
                        if(added_product != '') {
                            added_product += ',';
                        }
                        added_product += $product_id;

                        if(enrollmentLocation == "groupSide"){
                            $("#product_category_"+$product_id).val($prd_category_id);
                        }
                        $("#product_price_"+$product_id).val($price);
                        $("#product_plan_"+$product_id).val($prd_plan_type_id);
                        $("#product_plan_"+$product_id).selectpicker('refresh');
                        productPriceDisplay($product_id,$price);

                        if($pricing_model != "FixedPrice" || $prd_is_short_term_disablity_product == 'Y') {
                            addAdditionalQuestions($product_id,$pricing_model,'','displayQuestion',true);
                            $matrix_id_arr = $matrix_id.split(',');
                            $("#product_matrix_"+$product_id).val($matrix_id_arr);
                        } else {
                            $("#product_matrix_"+$product_id).val($matrix_id);
                        }

                        $("#addCoverageButton_"+$product_id).removeClass('addCoverage');
                        $("#addCoverageButton_"+$product_id).addClass('added_btn');
                        $("#addCoverageButton_"+$product_id).html('Added');

                        $("#calculateRateButton_"+$product_id).removeClass('cancelCalculateRate');
                        $("#calculateRateButton_"+$product_id).removeClass('calculateRate');
                        $("#calculateRateButton_"+$product_id).addClass('added_btn');
                        $("#calculateRateButton_"+$product_id).html('Added');
                        $("#addCalculatedCoverage_"+$product_id).attr('disabled','disabled');
                        $("#product_calcualate_rate_"+$product_id).collapse("hide");
                    }
                
                });

                $("#added_product").val(added_product);
                if(quote_healthy_step_fee != '' && quote_healthy_step_fee > 0) {
                    $("#healthy_step_fee").val(quote_healthy_step_fee);    
                }              

                $primary_product_change = true;
                $spouse_product_change = true;
                $verification_option_product_change = true;

                $("#submit_type").val('CalculatePrice');
                $(".error").html('');
                $.ajax({
                    url: '<?=$HOST?>/ajax_member_enrollment.php?quote_prds',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    success: function (res) {
                        $("#added_product").val(0);
                        $("#addToCartTable").html('');
                        $("#product_list").val(res.product_list);

                        if(res.display_contribution=='N'){
                            $('.contibution_price_td').hide();
                        }else{
                            $('.contibution_price_td').show();
                        }
                        if(res.premium_products){
                            $.each(res.premium_products,function($id,$value){
                                $tmp_product_price = parseFloat($value.display_member_price).toFixed(2);
                                $tmp_group_price = parseFloat($value.display_group_price).toFixed(2);
                                $tmp_total_price=parseFloat(parseFloat($value.display_member_price)+parseFloat($value.display_group_price)).toFixed(2);
                                cart_html = $("#addToCartDynamicTable").html();
                                cart_html = cart_html.replace(/~product_id~/g, $value.product_id);
                                cart_html = cart_html.split('~product_name~').join($value.product_name);
                                cart_html = cart_html.replace(/~plan_name~/g, $value.plan_name);
                                cart_html = cart_html.replace(/~product_price~/g, $tmp_product_price);
                                cart_html = cart_html.replace(/~group_price~/g, $tmp_group_price);
                                cart_html = cart_html.replace(/~total_price~/g, $tmp_total_price);
                                $("#addToCartTable").append(cart_html);

                                productPriceDisplay($value.product_id,$tmp_product_price);
                                $("#display_product_price_"+$value.product_id).val($tmp_product_price);
                            });
                        }

                        if(res.linked_Fee){
                            $.each(res.linked_Fee,function($id,$value){
                                $tmp_product_price = parseFloat($value.price).toFixed(2);
                                $tmp_group_price = parseFloat($value.group_price).toFixed(2);
                                $tmp_total_price=parseFloat(parseFloat($value.price)+parseFloat($value.group_price)).toFixed(2);
                                cart_fee_html = $("#addToCartDynamicTable").html();
                                cart_fee_html = cart_fee_html.replace(/~product_id~/g, $value.product_id);
                                cart_fee_html = cart_fee_html.split('~product_name~').join($value.product_name);
                                cart_fee_html = cart_fee_html.replace(/~plan_name~/g, $value.product_code);
                                cart_fee_html = cart_fee_html.replace(/~product_price~/g, $tmp_product_price);
                                cart_fee_html = cart_fee_html.replace(/~group_price~/g, $tmp_group_price);
                                cart_fee_html = cart_fee_html.replace(/~total_price~/g, $tmp_total_price);
                                $("#addToCartTable").append(cart_fee_html);
                                $("#remove_product_from_cart_div_"+$value.product_id).remove();
                            });
                        }

                        if(res.membership_Fee){
                            $.each(res.membership_Fee,function($id,$value){
                                $tmp_product_price = parseFloat($value.price).toFixed(2);
                                $tmp_group_price = parseFloat($value.group_price).toFixed(2);
                                $tmp_total_price=parseFloat(parseFloat($value.price)+parseFloat($value.group_price)).toFixed(2);
                                cart_fee_html = $("#addToCartDynamicTable").html();
                                cart_fee_html = cart_fee_html.replace(/~product_id~/g, $value.product_id);
                                cart_fee_html = cart_fee_html.split('~product_name~').join($value.product_name);
                                cart_fee_html = cart_fee_html.replace(/~plan_name~/g, $value.product_code);
                                cart_fee_html = cart_fee_html.replace(/~product_price~/g, $tmp_product_price);
                                cart_fee_html = cart_fee_html.replace(/~group_price~/g, $tmp_group_price);
                                cart_fee_html = cart_fee_html.replace(/~total_price~/g, $tmp_total_price);
                                $("#addToCartTable").append(cart_fee_html);
                                $("#remove_product_from_cart_div_"+$value.product_id).remove();
                            });
                        }
                        
                        $calculateSubTotal = parseFloat(res.display_sub_total).toFixed(2);
                        $calculateGroupPriceSubTotal = parseFloat(res.display_group_price_sub_total).toFixed(2);
                        $calculateTotal = parseFloat(res.display_order_total).toFixed(2);
                        $service_fee_total = parseFloat(res.service_fee_total).toFixed(2);
                        
                        $healthy_step_fees_total = parseFloat(res.healthy_step_fees_total).toFixed(2);
                        $healthy_step_fees_name = res.healthy_step_fees_name;

                        $summary_monthly_payment = (parseFloat($calculateSubTotal)+parseFloat($service_fee_total)).toFixed(2);
                        
                        $("#cart_sub_total").html($calculateSubTotal);
                        $("#cart_sub_total_group_price").html($calculateGroupPriceSubTotal);
                        $("#total_cart_sub_total").html((parseFloat($calculateSubTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));

                        $("#summary_member_rate_sub_total").html($calculateSubTotal);
                        $("#summary_group_rate_sub_total").html($calculateGroupPriceSubTotal);
                        $("#summary_sub_total").html((parseFloat($calculateSubTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));

                        $("#summary_member_rate_monthly_payment").html($summary_monthly_payment);
                        $("#cart_monthly_total").html($summary_monthly_payment);
                        $("#summary_group_rate_monthly_payment").html($calculateGroupPriceSubTotal);
                        $("#cart_monthly_total_group_price").html($calculateGroupPriceSubTotal);
                        $("#summary_monthly_payment").html((parseFloat($summary_monthly_payment)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                        $("#total_cart_monthly_total").html((parseFloat($summary_monthly_payment)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                        

                        $("#cart_total").html($calculateTotal);
                        $("#cart_total_group_price").html($calculateGroupPriceSubTotal);
                        $("#total_cart_total").html((parseFloat($calculateTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                        

                        $("#summary_member_rate_total").html($calculateTotal);
                        $("#summary_group_rate_total").html($calculateGroupPriceSubTotal);
                        $("#summary_total").html((parseFloat($calculateTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                        
                        $("#total_amount").html($calculateTotal);
                        $("#cart_healthy_step_name").html($healthy_step_fees_name);
                        $("#summary_healthy_step_name").html($healthy_step_fees_name);
                        $("#cart_healthy_step_total").html($healthy_step_fees_total);
                        $("#total_cart_healthy_step_total").html($healthy_step_fees_total);
                        $("#summary_healthy_step_total").html($healthy_step_fees_total);                        
                        $("#cart_counter").html(res.premium_products_count);
                        $("#cart_service_fee_total").html($service_fee_total);
                        $("#total_cart_service_fee_total").html($service_fee_total);
                        $("#summary_service_fee_total").html($service_fee_total);

                        if($healthy_step_fees_name != ''){
                            $(".cart_healthy_step_row").show();
                        } else {
                            $(".cart_healthy_step_row").hide();
                        }
                        if(res.healthyStepFeeList){
                            $(".cart_add_healthy_step_row").show();
                        } else {
                            $(".cart_add_healthy_step_row").hide();
                        }

                        if(res.healthyStepFeeList){
                            $.each(res.healthyStepFeeList,function($key,$value){
                                healthy_step_html = $("#healthyStepDynamicDiv").html();
                                healthy_step_html = healthy_step_html.replace(/~healthy_step_id~/g, $value.product_id);
                                healthy_step_html = healthy_step_html.split('~healthy_step_name~').join($value.product_name);
                                healthy_step_html = healthy_step_html.replace(/~healthy_step_price~/g, $value.price);
                                

                                if($("#healthyStep_"+$value.product_id).length > 0){
                                    $("#healthyStep_"+$value.product_id).show();
                                }else{
                                    $is_new_healthy_step = true;
                                    $("#healthyStepMainDiv").append(healthy_step_html);
                                }
                                if($value.healthy_step_description == 'Y'){

                                    $("#healthyStepDetails").show();
                                }
                            });

                            if($("#healthyStep_"+quote_healthy_step_fee).length > 0) {
                                $("#healthyStep_"+quote_healthy_step_fee).addClass('active');
                                $("#healthyStep_button_"+quote_healthy_step_fee).prop('checked',true);
                                $is_new_healthy_step = false;
                            }
                        } else {
                            $("#healthy_step_fee").val('-1');
                        }

                        $("#product_category_div .hidden_product_price").each(function(){
                            $packagedForArr = [];
                            $tmp_product_id = $(this).attr('data-product-id');
                            $packagedFor = $("#product_price_"+$tmp_product_id).attr('data-packaged-product-for');
                            if($packagedFor!='' && $packagedFor != undefined){
                                $packagedForArr = $packagedFor.split(",");
                            }
                            if($packagedForArr.length > 0){
                                $.each($packagedForArr,function($k,$v){
                                    if($v==$product_id){
                                        $("#product_body_div_"+$tmp_product_id).removeClass('packaged_body');
                                        $("#packaged_content_"+$tmp_product_id).hide();
                                    }
                                });
                            }
                        });

                        if(res.combination_products){
                            $.each(res.combination_products,function($product_id,combination_products){
                                $product_plan = $("#product_plan_"+$product_id).val();

                                if(combination_products['Excludes'] && combination_products['Excludes']['product_id']){
                                    $Excludes=combination_products['Excludes']['product_id'].split(',');
                                    
                                    $.each($Excludes,function($k,$v){
                                        $excludedFor = $("#product_price_"+$v).attr('data-excluded-product-for');
                                        
                                        if($excludedFor!='' && $excludedFor != undefined){
                                            $excludedForArr = $excludedFor.split(",");
                                            if(jQuery.inArray($product_id, $excludedForArr ) < 0){
                                                $excludedForArr.push($product_id);
                                            }
                                        }else{
                                            $excludedForArr = [];
                                            $excludedForArr.push($product_id);
                                        }

                                        $excludedForNameArr = [];
                                        $.each($excludedForArr,function($k1,$v1){
                                            $excludedForNameArr.push($("#product_name_"+$v1).html());
                                        });
                                        
                                        $excludedForList = $excludedForArr.join(",");
                                        $excludedForNameList = $excludedForNameArr.join(",");

                                        if($("#addCoverageButton_"+$v).hasClass('added_btn') || $("#calculateRateButton_"+$v).hasClass('added_btn')){
                                            removeProductFromCart($v,'Default');
                                        }

                                        if(!$("#product_body_div_"+$v).hasClass('packaged_body')){
                                            $("#product_body_div_"+$v).addClass('excluded_body');
                                            
                                            $("#excluded_content_"+$v).show();
                                            $("#product_price_"+$v).attr('data-excluded-product-for',$excludedForList);
                                            $("#excluded_content_product_name_"+$v).html($excludedForNameList);
                                        }
                                    });
                                }

                                if($("#inner_calculate_rate_main_div_"+$product_id+" .additional_tmp_question").length > 0){
                                    $("#inner_calculate_rate_main_div_"+$product_id+" .additional_tmp_question").each(function(){
                                        $elName=$(this).attr('data-element');
                                        $elId=$(this).attr('data-id');
                                        $elType=$(this).attr('data-enrollee-type');
                                        $val=$("#hidden_"+$elType+"_"+$product_id+"_"+$elId+"_"+$elName).val();
                                        
                                        $element = $elType+"_"+$elName+"_"+$elId;
                                        
                                        if ($('#'+$elType+"_"+$elName+"_div_"+$product_id+"_"+$elId).css('display') != 'none' && $('#'+$elType+"_"+$elName+"_div_"+$product_id+"_"+$elId).css('display') != undefined) {
                                            if($val!= undefined){
                                                $enrolleeElements.push($element);
                                                $enrolleeElementsVal[$element] = $val;
                                                enrolleeElements($element,$elType,$elName,$elId);
                                            }
                                        }
                                    });
                                }
                            });                       
                            fRefresh();
                        }

                        //feeColorBoxClose();

                        $("#ajax_loader").hide();

                        addOnProducts();
                    }
                });
            }

            addQuoteProductToCartV1 = function(cart_products){
                $.each(cart_products,function(key,quote_prd_row){
                    $product_id = quote_prd_row.product_id;
                    $price = quote_prd_row.price;
                    $matrix_id = quote_prd_row.matrix_id;
                    $prd_plan_type_id = quote_prd_row.prd_plan_type_id;
                    $pricing_model = quote_prd_row.pricing_model;
                    if($pricing_model == "VariableEnrollee") {
                        $("#product_plan_"+$product_id).val($prd_plan_type_id);
                        $("#product_plan_"+$product_id).selectpicker('refresh');

                        addAdditionalQuestions($product_id,$pricing_model,'','displayQuestion',true);
                        //$("#calculateRateButton_"+$product_id).trigger('click');
                    } else {
                        if($("#product_plan_"+$product_id + " option[data-prd-matrix-id='"+$matrix_id+"']").length > 0) {
                            $("#product_plan_"+$product_id).val($prd_plan_type_id);
                            $("#product_plan_"+$product_id).selectpicker('refresh');
                            //$("#addCoverageButton_"+$product_id).trigger('click');
                            addProductToCart($product_id,$price,$matrix_id,$pricing_model,$price);
                            productPriceDisplay($product_id,$price);
                        }
                    }
                    
                });
            }

            removeProductFromCart = function($product_id,$removeType){
                $("#product_category_"+$product_id).val('');
                if($("#cart_product_"+$product_id).length > 0 ){
                    $RequiredList=$("#product_price_"+$product_id).attr('data-required-product');
                    
                    if($RequiredList!='' && $RequiredList!=undefined){
                        $Required = $RequiredList.split(',');
                        $.each($Required,function($k,$v){
                            $requiredForList=$("#product_price_"+$v).attr('data-is-required-for');

                            if($requiredForList !='' && $requiredForList!=undefined){
                                $requiredForArr=$requiredForList.split(',');
                                $requiredForArr = jQuery.grep($requiredForArr, function(value) {
                                  return value != $product_id;
                                });
                            }else{
                                $requiredForArr = [];
                            }
                            
                            if($requiredForArr.length > 0){
                                $requiredFor = $requiredForArr.join(",");
                            }else{
                                $requiredFor = "";
                            }
                            $("#product_price_"+$v).attr('data-is-required-for',$requiredFor);
                            
                        });
                        $("#product_price_"+$product_id).attr('data-required-product','');
                    }
                    $requiredProduct = $("#product_price_"+$product_id).attr('data-is-required-for');

                    if($requiredProduct==''){
                        $price = $("#product_price_"+$product_id).val();
                        $("#product_price_"+$product_id).val('0.00');
                        $("#product_matrix_"+$product_id).val('0');
                        $("#cart_product_"+$product_id).remove();

                        removeItemByKeyValue(cart_products,'product_id',$product_id);

                        cartTotalCalculate($product_id,'remove');
                    } else{
                        $removeType = '';
                        $product_name = $("#product_name_"+$product_id).html();
                        $ReuiredProductName = [];
                        
                        $requiredForArr=$("#product_price_"+$product_id).attr('data-is-required-for').split(',');
                        $.each($requiredForArr,function($k,$v){
                            $ReuiredProductName.push($("#product_name_"+$v).html());
                        });
                        $RequiredProductNamList = $ReuiredProductName.join(",");

                        $ErrorMessage = $product_name +" is required with the purchase of "+ $RequiredProductNamList +".  To remove product, you must remove the required product first.";

                        swal({   
                            title: "Plan Required",   
                            text: $ErrorMessage,
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: 'Close'   
                        });
                        
                    }
                }

                if($removeType!=''){
                    if($removeType=="Default"){
                        $("#addCoverageButton_"+$product_id).addClass('addCoverage');
                        $("#addCoverageButton_"+$product_id).removeClass('added_btn');
                        $("#addCoverageButton_"+$product_id).html('Add Plan');

                        $("#product_calcualate_rate_"+$product_id).collapse("hide");
                        $("#calculateRateButton_"+$product_id).removeClass('added_btn');
                        $("#calculateRateButton_"+$product_id).removeClass('cancelCalculateRate');
                        $("#calculateRateButton_"+$product_id).addClass('calculateRate');
                        $("#calculateRateButton_"+$product_id).html('Calculate Rate');
                        $("#addCalculatedCoverage_"+$product_id).attr('disabled','disabled');
                    }else if($removeType=="FixedPrice"){
                        $("#addCoverageButton_"+$product_id).addClass('addCoverage');
                        $("#addCoverageButton_"+$product_id).removeClass('added_btn');
                        $("#addCoverageButton_"+$product_id).html('Add Plan');
                        $("#calculateRateButton_"+$product_id).removeClass('added_btn');
                        $("#calculateRateButton_"+$product_id).removeClass('cancelCalculateRate');
                        $("#calculateRateButton_"+$product_id).addClass('calculateRate');
                        $("#calculateRateButton_"+$product_id).html('Calculate Rate');
                    }else{
                        $("#product_calcualate_rate_"+$product_id).collapse("show");
                        $("#calculateRateButton_"+$product_id).removeClass('added_btn');
                        $("#calculateRateButton_"+$product_id).removeClass('calculateRate');
                        $("#calculateRateButton_"+$product_id).addClass('cancelCalculateRate');
                        $("#calculateRateButton_"+$product_id).html('Cancel');
                        $("#addCalculatedCoverage_"+$product_id).removeAttr('disabled','disabled');
                    }
                }

                $primary_product_change=true;
                $spouse_product_change=true;
                $verification_option_product_change=true;
            }

            cartTotalCalculate = function($product_id,$cartAction){
                $("#submit_type").val('CalculatePrice');
                $(".error").html('');
                $("#ajax_loader").show();
                $.ajax({
                    url: '<?=$HOST?>/ajax_member_enrollment.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    //async: false,
                    success: function (res) {
                        $("#ajax_loader").hide();
                        $("#added_product").val(0);
                        $("#addToCartTable").html('');
                        $("#product_list").val(res.product_list);

                        if(res.display_contribution=='N'){
                            $('.contibution_price_td').hide();
                        }else{
                            $('.contibution_price_td').show();
                        }
                        
                        if(res.premium_products){
                            $.each(res.premium_products,function($id,$value){
                                $tmp_product_price = parseFloat($value.display_member_price).toFixed(2);
                                $tmp_group_price = parseFloat($value.display_group_price).toFixed(2);
                                $tmp_total_price=parseFloat(parseFloat($value.display_member_price)+parseFloat($value.display_group_price)).toFixed(2);
                                
                                cart_html = $("#addToCartDynamicTable").html();
                                cart_html = cart_html.replace(/~product_id~/g, $value.product_id);
                                cart_html = cart_html.split('~product_name~').join($value.product_name);
                                cart_html = cart_html.replace(/~plan_name~/g, $value.plan_name);
                                cart_html = cart_html.replace(/~product_price~/g, $tmp_product_price);
                                cart_html = cart_html.replace(/~group_price~/g, $tmp_group_price);
                                cart_html = cart_html.replace(/~total_price~/g, $tmp_total_price);
                                $("#addToCartTable").append(cart_html);
                            });
                        }
                        if(res.linked_Fee){
                            $.each(res.linked_Fee,function($id,$value){
                                $tmp_product_price = parseFloat($value.price).toFixed(2);
                                $tmp_group_price = parseFloat($value.group_price).toFixed(2);
                                $tmp_total_price=parseFloat(parseFloat($value.price)+parseFloat($value.group_price)).toFixed(2);
                                cart_fee_html = $("#addToCartDynamicTable").html();
                                cart_fee_html = cart_fee_html.replace(/~product_id~/g, $value.product_id);
                                cart_fee_html = cart_fee_html.split('~product_name~').join($value.product_name);
                                cart_fee_html = cart_fee_html.replace(/~plan_name~/g, $value.product_code);
                                cart_fee_html = cart_fee_html.replace(/~product_price~/g, $tmp_product_price);
                                cart_fee_html = cart_fee_html.replace(/~group_price~/g, $tmp_group_price);
                                cart_fee_html = cart_fee_html.replace(/~total_price~/g, $tmp_total_price);
                                $("#addToCartTable").append(cart_fee_html);
                                $("#remove_product_from_cart_div_"+$value.product_id).remove();
                            });
                        }
                        if(res.membership_Fee){
                            $.each(res.membership_Fee,function($id,$value){
                                $tmp_product_price = parseFloat($value.price).toFixed(2);
                                $tmp_group_price = parseFloat($value.group_price).toFixed(2);
                                $tmp_total_price=parseFloat(parseFloat($value.price)+parseFloat($value.group_price)).toFixed(2);
                                cart_fee_html = $("#addToCartDynamicTable").html();
                                cart_fee_html = cart_fee_html.replace(/~product_id~/g, $value.product_id);
                                cart_fee_html = cart_fee_html.split('~product_name~').join($value.product_name);
                                cart_fee_html = cart_fee_html.replace(/~plan_name~/g, $value.product_code);
                                cart_fee_html = cart_fee_html.replace(/~product_price~/g, $tmp_product_price);
                                cart_fee_html = cart_fee_html.replace(/~group_price~/g, $tmp_group_price);
                                cart_fee_html = cart_fee_html.replace(/~total_price~/g, $tmp_total_price);
                                $("#addToCartTable").append(cart_fee_html);
                                $("#remove_product_from_cart_div_"+$value.product_id).remove();
                            });
                        }
                        
                        $calculateSubTotal = parseFloat(res.display_sub_total).toFixed(2);
                        $calculateGroupPriceSubTotal = parseFloat(res.display_group_price_sub_total).toFixed(2);
                        $calculateTotal = parseFloat(res.display_order_total).toFixed(2);
                        $service_fee_total = parseFloat(res.service_fee_total).toFixed(2);
                        
                        $healthy_step_fees_total = parseFloat(res.healthy_step_fees_total).toFixed(2);
                        $healthy_step_fees_name = res.healthy_step_fees_name;

                        $summary_monthly_payment = (parseFloat($calculateSubTotal)+parseFloat($service_fee_total)).toFixed(2);
                        
                        $("#cart_sub_total").html($calculateSubTotal);
                        $("#cart_sub_total_group_price").html($calculateGroupPriceSubTotal);
                        $("#total_cart_sub_total").html((parseFloat($calculateSubTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                        
                        $("#summary_member_rate_sub_total").html($calculateSubTotal);
                        $("#summary_group_rate_sub_total").html($calculateGroupPriceSubTotal);
                        $("#summary_sub_total").html((parseFloat($calculateSubTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));

                        $("#summary_member_rate_monthly_payment").html($summary_monthly_payment);
                        $("#cart_monthly_total").html($summary_monthly_payment);
                        $("#summary_group_rate_monthly_payment").html($calculateGroupPriceSubTotal);
                        $("#cart_monthly_total_group_price").html($calculateGroupPriceSubTotal);
                        $("#summary_monthly_payment").html((parseFloat($summary_monthly_payment)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                        $("#total_cart_monthly_total").html((parseFloat($summary_monthly_payment)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                       
                        $("#cart_total").html($calculateTotal);
                        $("#cart_total_group_price").html($calculateGroupPriceSubTotal);
                        $("#total_cart_total").html((parseFloat($calculateTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                       
                        $("#summary_member_rate_total").html($calculateTotal);
                        $("#summary_group_rate_total").html($calculateGroupPriceSubTotal);
                        $("#summary_total").html((parseFloat($calculateTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));
                        
                        $("#cart_healthy_step_name").html($healthy_step_fees_name);
                        $("#summary_healthy_step_name").html($healthy_step_fees_name);
                        $("#cart_healthy_step_total").html($healthy_step_fees_total);
                        $("#total_cart_healthy_step_total").html($healthy_step_fees_total);
                        $("#summary_healthy_step_total").html($healthy_step_fees_total);
                        
                        $("#cart_service_fee_total").html($service_fee_total);
                        $("#total_cart_service_fee_total").html($service_fee_total);
                        $("#summary_service_fee_total").html($service_fee_total);

                        if($healthy_step_fees_name != ''){
                            $(".cart_healthy_step_row").show();
                        }else{
                            $(".cart_healthy_step_row").hide();
                        }
                        if(res.healthyStepFeeList){
                            $(".cart_add_healthy_step_row").show();
                        }else{
                            $(".cart_add_healthy_step_row").hide();
                        }
                        
                        $("#total_amount").html($calculateTotal);
                        $("#cart_counter").html(res.premium_products_count);

                        if($cartAction == "add"){
                            $is_auto_assign_product = false;
                            $is_required_product = false;

                            if(res.healthyStepFeeList){
                                $.each(res.healthyStepFeeList,function($key,$value){
                                    healthy_step_html = $("#healthyStepDynamicDiv").html();
                                    healthy_step_html = healthy_step_html.replace(/~healthy_step_id~/g, $value.product_id);
                                    healthy_step_html = healthy_step_html.split('~healthy_step_name~').join($value.product_name);
                                    healthy_step_html = healthy_step_html.replace(/~healthy_step_price~/g, $value.price);
                                    

                                    if($("#healthyStep_"+$value.product_id).length > 0){
                                        $("#healthyStep_"+$value.product_id).show();
                                    }else{
                                        $is_new_healthy_step = true;
                                        $("#healthyStepMainDiv").append(healthy_step_html);
                                    }
                                    if($value.healthy_step_description == 'Y'){

                                        $("#healthyStepDetails").show();
                                    }
                                });
                            }else{
                                $("#healthy_step_fee").val('-1');
                                quote_healthy_step_fee = 0;
                            }
                            
                            $product_plan = $("#product_plan_"+$product_id).val();

                            $("#product_category_div .hidden_product_price").each(function(){
                                $packagedForArr = [];
                                $tmp_product_id = $(this).attr('data-product-id');
                                $packagedFor = $("#product_price_"+$tmp_product_id).attr('data-packaged-product-for');
                                if($packagedFor!='' && $packagedFor != undefined){
                                    $packagedForArr = $packagedFor.split(",");
                                }

                                if($packagedForArr.length > 0){
                                    $.each($packagedForArr,function($k,$v){
                                        if($v==$product_id){
                                            $("#product_body_div_"+$tmp_product_id).removeClass('packaged_body');
                                            $("#packaged_content_"+$tmp_product_id).hide();
                                        }
                                    });
                                }
                            });
                            

                            if(res.combination_products){
                                if(res.combination_products['Auto Assign'] && res.combination_products['Auto Assign']['product_id']){
                                    $AutoAssigned=res.combination_products['Auto Assign']['product_id'].split(',');
                                    $.each($AutoAssigned,function($k,$v){

                                        if($("#addCoverageButton_"+$v).length > 0 && $("#addCoverageButton_"+$v).hasClass('addCoverage') && !$("#product_body_div_"+$v).hasClass('excluded_body') && !$("#product_body_div_"+$v).hasClass('packaged_body')){
                                            $autoAssignedProductArr.push($v);
                                            $is_auto_assign_product = true;
                                            $product_name = $("#product_name_"+$v).html();

                                            colorbox_html = $("#autoAssignColorboxProductDynamicDiv").html();
                                            colorbox_html = colorbox_html.replace(/~product_id~/g, $v);
                                            colorbox_html = colorbox_html.split('~product_name~').join($product_name);


                                            if($("#autoAssignColorboxProductDiv #autoAssign_colorbox_product_"+$v).length <=0){
                                                $("#autoAssignColorboxProductDiv").append(colorbox_html);
                                                $count = $("#autoAssignColorboxProductDiv .autoAssign_colorbox_product").length;

                                                
                                                $planHtml = $("#product_plan_"+$v).html();
                                                $("#autoAssign_colorbox_plan_"+$v).html($planHtml);

                                                $("#autoAssign_colorbox_plan_"+$v+" option").each(function() {
                                                    if($(this).css('display') == 'none'){
                                                        $(this).remove();
                                                    }
                                                });

                                                if($("#autoAssign_colorbox_plan_"+$v+" option[value='"+$product_plan+"']").length > 0){
                                                    
                                                    $("#autoAssign_colorbox_plan_"+$v).val($product_plan);
                                                    $price = $("#autoAssign_colorbox_plan_"+$v+" option[value='"+$product_plan+"']").attr('data-price');
                                                    $("#autoAssign_colorbox_price_"+$v).html($price);
                                                }else{
                                                    $("#autoAssign_colorbox_plan_"+$v).val('');
                                                    $("#autoAssign_colorbox_price_"+$v).html('0.00');
                                                }
                                                
                                                $("#autoAssign_colorbox_plan_"+$v).addClass('form-control');
                                                //$("#autoAssign_colorbox_plan_"+$v).selectpicker('refresh');
                                                $("#autoAssign_colorbox_plan_"+$v).selectpicker('setStyle', 'btn-select');
                                            }
                                        }
                                    });
                                }
                                if(res.combination_products['Excludes'] && res.combination_products['Excludes']['product_id']){
                                    $Excludes=res.combination_products['Excludes']['product_id'].split(',');
                                    
                                    $.each($Excludes,function($k,$v){
                                        $excludedFor = $("#product_price_"+$v).attr('data-excluded-product-for');
                                        
                                        if($excludedFor!='' && $excludedFor != undefined){
                                            $excludedForArr = $excludedFor.split(",");
                                            if(jQuery.inArray($product_id, $excludedForArr ) < 0){
                                                $excludedForArr.push($product_id);
                                            }
                                        }else{
                                            $excludedForArr = [];
                                            $excludedForArr.push($product_id);
                                        }

                                        $excludedForNameArr = [];
                                        $.each($excludedForArr,function($k1,$v1){
                                            $excludedForNameArr.push($("#product_name_"+$v1).html());
                                        });
                                        
                                        $excludedForList = $excludedForArr.join(",");
                                        $excludedForNameList = $excludedForNameArr.join(",");

                                        if($("#addCoverageButton_"+$v).hasClass('added_btn') || $("#calculateRateButton_"+$v).hasClass('added_btn')){
                                            removeProductFromCart($v,'Default');
                                        }
                                        if(!$("#product_body_div_"+$v).hasClass('packaged_body')){
                                            $("#product_body_div_"+$v).addClass('excluded_body');
                                            
                                            $("#excluded_content_"+$v).show();
                                            $("#product_price_"+$v).attr('data-excluded-product-for',$excludedForList);
                                            $("#excluded_content_product_name_"+$v).html($excludedForNameList);
                                        }
                                    });
                                }
                                if(res.combination_products['Required'] && res.combination_products['Required']['product_id']){
                                    $Required=res.combination_products['Required']['product_id'].split(',');
                                    $("#product_price_"+$product_id).attr('data-required-product',res.combination_products['Required']['product_id']);
                                    $.each($Required,function($k,$v){
                                        $requiredFor=$("#product_price_"+$v).attr('data-is-required-for');
                                        if($requiredFor!='' && $requiredFor!=undefined){
                                            $requiredForArr = $requiredFor.split(',');
                                            if(jQuery.inArray($product_id, $requiredForArr ) < 0){
                                                $requiredForArr.push($product_id);
                                            }
                                        }else{
                                            $requiredForArr = [];
                                            $requiredForArr.push($product_id);
                                        }

                                        $requiredFor = $requiredForArr.join(",");
                                        
                                        $("#product_price_"+$v).attr('data-is-required-for',$requiredFor);

                                        if($("#addCoverageButton_"+$v).length > 0 && $("#addCoverageButton_"+$v).hasClass('addCoverage') && !$("#product_body_div_"+$v).hasClass('excluded_body') && !$("#product_body_div_"+$v).hasClass('packaged_body')){
                                            $requiredProductArr.push($v);
                                            $is_required_product = true;
                                            $product_name = $("#product_name_"+$v).html();

                                            colorbox_html = $("#requiredColorboxProductDynamicDiv").html();
                                            colorbox_html = colorbox_html.replace(/~product_id~/g, $v);
                                            colorbox_html = colorbox_html.split('~product_name~').join($product_name);


                                            if($("#requiredColorboxProductDiv #required_colorbox_product_"+$v).length <=0){
                                                $("#requiredColorboxProductDiv").append(colorbox_html);
                                                $count = $("#requiredColorboxProductDiv .required_colorbox_product").length;

                                                $planHtml = $("#product_plan_"+$v).html();
                                                $("#required_colorbox_plan_"+$v).html($planHtml);

                                                $("#required_colorbox_plan_"+$v+" option").each(function() {
                                                    if($(this).css('display') == 'none'){
                                                        $(this).remove();
                                                    }
                                                });

                                                if($("#required_colorbox_plan_"+$v+" option[value='"+$product_plan+"']").length > 0){
                                                    $("#required_colorbox_plan_"+$v).val($product_plan);
                                                    $price = $("#required_colorbox_plan_"+$v+" option[value='"+$product_plan+"']").attr('data-price');
                                                    $("#required_colorbox_price_"+$v).html($price);
                                                }else{
                                                    $("#required_colorbox_plan_"+$v).val('');
                                                    $("#required_colorbox_price_"+$v).html('0.00');
                                                }
                                                
                                                $("#required_colorbox_plan_"+$v).addClass('form-control');
                                                //$("#required_colorbox_plan_"+$v).selectpicker('refresh');
                                                $("#required_colorbox_plan_"+$v).selectpicker('setStyle', 'btn-select');
                                            }
                                        }
                                    });
                                    if($is_required_product){
                                        $product_name = $("#product_name_"+$product_id).html();
                                        $("#required_product_name").html($product_name);
                                    }
                                }
                                
                                fRefresh();
                            }
                            
                            if(res.riderProduct){
                                if(res.riderProduct['Rider'] && res.riderProduct['Rider']['product_id']){
                                    $RiderProduct=res.riderProduct['Rider']['product_id'].split(',');
                                    $.each($RiderProduct,function($k,$v){

                                        if($("#addCoverageButton_"+$v).length > 0 && $("#addCoverageButton_"+$v).hasClass('addCoverage') && !$("#product_body_div_"+$v).hasClass('excluded_body') && !$("#product_body_div_"+$v).hasClass('packaged_body')){
                                            $riderProductArr.push($v);
                                            $is_rider_product = true;
                                            $product_name = $("#product_name_"+$v).html();

                                            colorbox_html = $("#riderProductColorboxProductDynamicDiv").html();
                                            colorbox_html = colorbox_html.replace(/~product_id~/g, $v);
                                            colorbox_html = colorbox_html.split('~product_name~').join($product_name);


                                            if($("#riderProductColorboxProductDiv #riderProduct_colorbox_product_"+$v).length <=0){
                                                $("#riderProductColorboxProductDiv").append(colorbox_html);
                                                $count = $("#riderProductColorboxProductDiv .riderProduct_colorbox_product").length;

                                                
                                                $planHtml = $("#product_plan_"+$v).html();
                                                $("#riderProduct_colorbox_plan_"+$v).html($planHtml);

                                                $("#riderProduct_colorbox_plan_"+$v+" option").each(function() {
                                                    if($(this).css('display') == 'none'){
                                                        $(this).remove();
                                                    }
                                                });

                                                if($("#riderProduct_colorbox_plan_"+$v+" option[value='"+$product_plan+"']").length > 0){
                                                    
                                                    $("#riderProduct_colorbox_plan_"+$v).val($product_plan);
                                                    $price = $("#riderProduct_colorbox_plan_"+$v+" option[value='"+$product_plan+"']").attr('data-price');
                                                    $("#riderProduct_colorbox_price_"+$v).html($price);
                                                }else{
                                                    $("#riderProduct_colorbox_plan_"+$v).val('');
                                                    $("#riderProduct_colorbox_price_"+$v).html('0.00');
                                                }
                                                
                                                $("#riderProduct_colorbox_plan_"+$v).addClass('form-control');
                                                //$("#riderProduct_colorbox_plan_"+$v).selectpicker('refresh');
                                                $("#riderProduct_colorbox_plan_"+$v).selectpicker('setStyle', 'btn-select');
                                            }
                                        }
                                    });
                                }
                                
                                fRefresh();
                            }
                            $autoAssignedProductArr = jQuery.grep($autoAssignedProductArr, function(value) {
                              return value != $product_id;
                            });

                            $riderProductArr = jQuery.grep($riderProductArr, function(value) {
                              return value != $product_id;
                            });

                            $requiredProductArr = jQuery.grep($requiredProductArr, function(value) {
                              return value != $product_id;
                            });
                            
                            if($is_auto_assign_product || $autoAssignedProductArr.length > 0){
                                autoAssignProduct($product_id);
                            }else{
                                if($is_required_product || $requiredProductArr.length > 0){
                                    requiredProduct();
                                }else{
                                    if($is_required_product || $riderProductArr.length > 0){
                                        riderProduct($product_id);
                                    }else{
                                        if($autoAssignedProductArr.length <= 0 && $requiredProductArr.length <= 0 && $riderProductArr.length <=0){
                                            if($("#healthy_step_fee").val() != 0 || $("#healthy_step_fee").val() != '') {
                                                feeColorBoxClose();
                                            }
                                        }
                                    }
                                }
                            }
                            if($("#inner_calculate_rate_main_div_"+$product_id+" .additional_tmp_question").length > 0){
                                $("#inner_calculate_rate_main_div_"+$product_id+" .additional_tmp_question").each(function(){
                                    $elName=$(this).attr('data-element');
                                    $elId=$(this).attr('data-id');
                                    $elType=$(this).attr('data-enrollee-type');
                                    $val=$("#hidden_"+$elType+"_"+$product_id+"_"+$elId+"_"+$elName).val();
                                    
                                    $element = $elType+"_"+$elName+"_"+$elId;
                                    
                                    if ($('#'+$elType+"_"+$elName+"_div_"+$product_id+"_"+$elId).css('display') != 'none' && $('#'+$elType+"_"+$elName+"_div_"+$product_id+"_"+$elId).css('display') != undefined) {
                                        if($val!= undefined){
                                            $enrolleeElements.push($element);
                                            $enrolleeElementsVal[$element] = $val;
                                            enrolleeElements($element,$elType,$elName,$elId);
                                        }
                                    }
                                });
                            }
                        }else if($cartAction == "remove"){
                            $("#healthyStepMainDiv .healthyStep").hide();
                            if(res.healthyStepFeeList){
                                $.each(res.healthyStepFeeList,function($key,$value){
                                    if($("#healthyStep_"+$value.product_id).length > 0){
                                        $("#healthyStep_"+$value.product_id).show();
                                    }
                                });
                            }

                            $("#product_category_div .hidden_product_price").each(function(){
                                $packagedForArr = [];
                                $excludedForArr = [];
                                $excludedForNameArr = [];
                                $tmp_product_id = $(this).attr('data-product-id');
                                $packagedFor = $("#product_price_"+$tmp_product_id).attr('data-packaged-product-for');
                                $excludedFor = $("#product_price_"+$tmp_product_id).attr('data-excluded-product-for');

                                if($packagedFor!='' && $packagedFor != undefined){
                                    $packagedForArr = $packagedFor.split(",");
                                }
                                if($excludedFor!='' && $excludedFor != undefined){
                                    $excludedForArr = $excludedFor.split(",");
                                }
                                if($packagedForArr.length > 0){
                                    $is_remove_packaged = true;
                                    $.each($packagedForArr,function($k,$v){
                                        if($("#addCoverageButton_"+$v).hasClass('added_btn') || $("#calculateRateButton_"+$v).hasClass('added_btn')){
                                            $is_remove_packaged = false;
                                        }
                                    });
                                    if($is_remove_packaged){
                                        if($("#addCoverageButton_"+$tmp_product_id).hasClass('added_btn') || $("#calculateRateButton_"+$tmp_product_id).hasClass('added_btn')){
                                            removeProductFromCart($tmp_product_id,'Default');
                                        }
                                        if(!$("#product_body_div_"+$tmp_product_id).hasClass('excluded_body')){
                                            $("#product_body_div_"+$tmp_product_id).addClass('packaged_body');
                                            $("#packaged_content_"+$tmp_product_id).show();
                                        }
                                    }
                                }
                                if($excludedForArr.length > 0){

                                    $excludedForArr = jQuery.grep($excludedForArr, function(value) {
                                      return value != $product_id;
                                    });
                                    $is_remove_excluded = true;

                                    if($excludedForArr.length > 0){
                                        $.each($excludedForArr,function($k,$v){
                                            $excludedForNameArr.push($("#product_name_"+$v).html());
                                            if($("#addCoverageButton_"+$v).hasClass('added_btn') || $("#calculateRateButton_"+$v).hasClass('added_btn')){
                                                $is_remove_excluded = false;
                                            }
                                        });
                                        $excludedForList = $excludedForArr.join(",");
                                        $excludedForNameList = $excludedForNameArr.join(",");
                                        $("#excluded_content_product_name_"+$tmp_product_id).html($excludedForNameList);
                                        $("#product_price_"+$tmp_product_id).attr('data-excluded-product-for',$excludedForList);
                                    }

                                    if($is_remove_excluded){
                                        $("#product_body_div_"+$tmp_product_id).removeClass('excluded_body');
                                        $("#excluded_content_product_name_"+$tmp_product_id).html('');
                                        $("#product_price_"+$tmp_product_id).attr('data-excluded-product-for','');
                                        $("#excluded_content_"+$tmp_product_id).hide();
                                    }
                                }
                            });
                           

                            $("#li_product_detail").removeClass("completed");
                            $("#li_basic_detail").removeClass("completed");
                            $("#li_payment_detail").removeClass("completed");
                            $("#li_basic_detail").addClass("disabled");
                            $("#li_payment_detail").addClass("disabled");
                            $("#li_product_detail").find("a").trigger("click");

                            if($("#inner_calculate_rate_main_div_"+$product_id+" .additional_tmp_question").length > 0){
                                $("#inner_calculate_rate_main_div_"+$product_id+" .additional_tmp_question").each(function(){
                                    $elName=$(this).attr('data-element');
                                    $elId=$(this).attr('data-id');
                                    $elType=$(this).attr('data-enrollee-type');

                                    $element = $elType+"_"+$elName+"_"+$elId;
                                    if ($('#'+$elType+"_"+$elName+"_div_"+$product_id+"_"+$elId).css('display') != 'none' && $('#'+$elType+"_"+$elName+"_div_"+$product_id+"_"+$elId).css('display') != undefined) {
                                        $.each($enrolleeElements, function(idx, item) {
                                            if (item == $element) {
                                                $enrolleeElements.splice(idx, 1);
                                                return false;
                                            }
                                        });
                                        enrolleeElements($element,$elType,$elName,$elId);
                                    }
                                    
                                });
                                
                            }
                            removeHealthyStepFee();
                        }
                        addOnProducts();
                    }
                });
            }


            autoAssignProduct = function($product_id){
                
                $product_name = $("#product_name_"+$product_id).html();
                $("#autoAssign_product_name").html($product_name); 
                $("#autoAssignTotal").html(0.00);


                if($(".autoAssginedProductApproved.text-success").length > 0){
                    $(".autoAssginedProductApproved.text-success").each(function(){
                        if($(this).hasClass('text-success')){
                            $(this).toggleClass('text-success');
                            $(this).toggleClass('text-light-gray');
                        }
                        
                    })
                }
                        
                $.colorbox({
                    inline : true,
                    href : '#autoAssignColorboxDiv',
                    width: '580px', 
                    height: '460px',
                    escKey:false, 
                    overlayClose:false,
                    closeButton:false,
                    fastIframe:false,
                    onClosed : function(){
                        $autoAssignedProductArr = [];
                        if($requiredProductArr.length > 0){
                            requiredProduct();
                        }else{
                            if($riderProductArr.length > 0){
                                riderProduct();
                            }
                        }
                    }
                });
            }

            riderProduct = function($product_id){
                
                $product_name = $("#product_name_"+$product_id).html();
                $("#riderProduct_product_name").html($product_name); 
                $("#riderProductTotal").html(0.00);


                if($(".riderProductApproved.text-success").length > 0){
                    $(".riderProductApproved.text-success").each(function(){
                        if($(this).hasClass('text-success')){
                            $(this).toggleClass('text-success');
                            $(this).toggleClass('text-light-gray');
                        }
                        
                    })
                }
                        
                $.colorbox({
                    inline : true,
                    href : '#riderProductColorboxDiv',
                    width: '580px', 
                    height: '460px',
                    escKey:false, 
                    overlayClose:false,
                    closeButton:false,
                    fastIframe:false,
                    onClosed : function(){
                        $riderProductArr = [];
                        
                    }
                });
            }

            requiredProduct = function(){
                
                $requiredTotal = 0.00;
                if($("#requiredColorboxProductDiv .required_colorbox_product").length > 0){
                    $("#requiredColorboxProductDiv .required_colorbox_product").each(function(){
                        $product_id = $(this).attr('data-product-id');
                        $price = $("#required_colorbox_price_"+$product_id).html();

                        $requiredTotal = parseFloat($requiredTotal) + parseFloat($price);
                    })
                }
                $("#requiredTotal").html(parseFloat($requiredTotal).toFixed(2));
                $.colorbox({
                    inline : true,
                    href : '#requiredColorboxDiv',
                    width: '580px', 
                    height: '460px',
                    escKey:false, 
                    overlayClose:false,
                    closeButton:false,
                    fastIframe:false,
                    onClosed : function(){
                        $requiredProductArr = [];  
                        if($riderProductArr.length > 0){
                            riderProduct();
                        }
                    }
                });
            }

            addOnProducts = function() {
                if($("#enrollment_form").find($("#populateCategoryProductsMainDivaddOnCategory")).length > 0){
                    if($("#customer_id").val() > 0 || $(".added_btn[data-is-add-on-product='N']").length > 0) {
                        $("#category_addOnCategory").show();
                        $("#product_filter option[value=addOnCategory]").prop("disabled",false);
                        if(is_add_product == 0 && $(".added_btn[data-is-add-on-product='N']").length == 0){
                            if($(".added_btn[data-is-add-on-product='Y']").length > 0){
                                $(".added_btn[data-is-add-on-product='Y']").each(function($k,$v){
                                    $product_id = $(this).attr('data-product-id');
                                    removeProductFromCart($product_id,'Default');
                                });
                            }
                            $("#category_addOnCategory").hide();
                            $("#product_filter option[value=addOnCategory]").prop("disabled",true);
                        }
                    } else {
                        if($(".added_btn[data-is-add-on-product='Y']").length > 0){
                            $(".added_btn[data-is-add-on-product='Y']").each(function($k,$v){
                                $product_id = $(this).attr('data-product-id');
                                removeProductFromCart($product_id,'Default');
                            });
                        }
                        $("#category_addOnCategory").hide();
                        $("#product_filter option[value=addOnCategory]").prop("disabled",true);
                        
                    }
                    $("#product_filter").multipleSelect('refresh');
                }
            }
            
            feeColorBoxClose = function() {
                displayHealthyStepFee();
            }

            displayHealthyStepFee = function(){
                $product_id = $("#healthy_step_fee").val();
                if($is_new_healthy_step || $product_id == 0){
                    $.colorbox({
                        inline : true,
                        href : '#healthyStepColorboxDiv',
                        width: '525px', 
                        height: '400px',
                        escKey:false, 
                        overlayClose:false,
                        closeButton:false,
                        fastIframe:false,
                        onClosed:function(){
                            $is_new_healthy_step = false;
                        }
                    });
                }else{
                    window.parent.$.colorbox.close();
                }
                $is_manually_open_healthy_step = false;
            }

            removeHealthyStepFee = function(){
                $product_id = $("#healthy_step_fee").val();
                if($(".added_btn").length <= 0 && $product_id > 0) {
                    quote_healthy_step_fee = 0;
                    $("#healthy_step_fee").val(0);
                    $("#healthyStepMainDiv").html('');
                    removeProductFromCart($product_id,'Default');
                }
            }

            enrolleeElements = function($element,$elType,$elName,$elId){
                if(jQuery.inArray($element, $enrolleeElements ) < 0){
                    if($elName=="gender" || $elName == "smoking_status" || $elName == "tobacco_status" || $elName == "has_spouse"){
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).parent().removeAttr('disabled');
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).prop('readonly',false);
                        
                    }else if($elName=="height" || $elName == "weight" || $elName == "no_of_children" || $elName =="benefit_amount"){
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).prop('disabled',false);
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).selectpicker('refresh');
                    }else{
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).prop('readonly',false);
                    }
                    $enrolleeElementsVal[$element]='';
                }else{
                    if($elName=="gender" || $elName == "smoking_status" || $elName == "tobacco_status" || $elName == "has_spouse"){
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).parent().attr('disabled','disabled');
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).prop('readonly',true);
                    }else if($elName=="height" || $elName == "weight" || $elName == "no_of_children"){
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).prop('disabled',true);
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).selectpicker('refresh');
                    }else{
                        $(".coverage_form ."+$elType+"_"+$elName+"_"+$elId).prop('readonly',true);
                    }
                }
            }
        //******************** Products tab Code end   *******************************



        //******************** Details tab Code start *******************************
            $(document).on('change','#que_primary_address1,#que_primary_address2,#primary_zip',function(){
               $("#is_valid_address").val('N');
               $("#is_address_verified").val('N');
            });
            
            primary_member_field = function(){
                $("#ajax_loader").show();
                $("#enrolleeElementsVal").val(JSON.stringify($enrolleeElementsVal));
                $.ajax({
                    url: '<?=$HOST?>/ajax_get_primary_member_field.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide();    
                        $primary_product_change = false;
                        if(res.status=="success"){
                            $("#primary_member_field_div").html(res.html);
                            
                            $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
                            $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                            $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
                            $(".primary_multiple_select").multipleSelect({
                                width:'100%'
                            });
                            $(".primary_select").addClass('form-control');
                            $(".primary_select").selectpicker({
                                container: 'body', 
                                style:'btn-select',
                                noneSelectedText: '',
                                dropupAuto:false,
                            });
                            $(".Salary_mask").priceFormat({
                                prefix: '',
                                suffix: '',
                                centsSeparator: '.',
                                thousandsSeparator: ',',
                                limit: false,
                                centsLimit: 2,
                            });
                            if($('.primary_annual_salary_1').val() !== undefined && $('.primary_annual_salary_1').val() !== ''){
                                $('#primary_salary').val($('.primary_annual_salary_1').val());
                                $('#primary_salary').addClass('has-value');
                                $('#primary_salary').prop('readonly', true);
                            }else{
                                if($('#primary_salary').val() == ''){
                                    $('#primary_salary').val(0.00);
                                }
                                $('#primary_salary').addClass('has-value');
                            }

                            // $("input[name='primary_benefit_amount']").each(function(index,data) {
                            //    $id_parts = $(this).attr('id').split('_');
                            //    console.log($id_parts);
                            //    console.log(index);
                            // });
                               
                        }
                        checkEmail();                
                    }
                });
            }

            is_dependent_required = function($state){
                $dependent_count = 0;
                $principal_beneficiary_count = 0;
                $contingent_beneficiary_count = 0;

                $("#addSpouseField").hide();
                $("#addChildField").hide();
                $("#dependent_spouse_main_div").html('');
                $("#dependent_child_main_div").html('');
                $("#dependent_field_div").hide();
                $("#spouse_products_list").val('');
                $("#child_products_list").val('');

                $("#beneficiary_information_div").hide();
                $("#principal_beneficiary_div").hide();
                $("#contingent_beneficiary_div").hide();
                $("#principal_beneficiary_field_div").html('');
                $("#contingent_beneficiary_field_div").html('');
                $("#addPrincipalBeneficiaryField").attr('data-allow-upto','');
                $("#addContingentBeneficiaryField").attr('data-allow-upto','');
                
                $("#is_principal_beneficiary").val('');
                $("#is_contingent_beneficiary").val('');
                
                
                $("#ajax_loader").show();
                $.ajax({
                    url: '<?=$HOST?>/ajax_check_is_dependent_required.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide();    
                        $spouse_product_change = false;
                        if(res.status=="success"){
                            if(res.is_spouse){
                                $("#dependent_field_div").show();
                                $("#spouse_products_list").val(res.is_spouse);
                                $("#addSpouseField").show();
                                if(spouse_dep.length > 0) {
                                    spouse_field(spouse_dep[0].cd_profile_id,spouse_dep[0].order_id);
                                    //spouse_dep = [];
                                } else {
                                    spouse_field();
                                }
                            }
                            if(res.is_child){
                                $("#dependent_field_div").show();
                                $("#child_products_list").val(res.is_child);
                                $("#addChildField").show();
                                if(child_dep.length > 0) {
                                    for(var i = 0; i < child_dep.length;i++){
                                        child_field(child_dep[i].cd_profile_id,child_dep[0].order_id);
                                    }
                                    //child_dep = [];
                                } else {
                                    if(res.child_count > 0){
                                        for(var i = 1; i <= res.child_count;i++){
                                            child_field();
                                        }
                                    }
                                }                                
                            }
                        }
                        if(res.principal_beneficiary){
                            $("#beneficiary_information_div").show();
                            $("#principal_beneficiary_div").show();

                            if(res.principal_beneficiary_allow_upto_3){
                                $("#addPrincipalBeneficiaryField").attr('data-allow-upto',3);
                            }
                            $("#is_principal_beneficiary").val('not_displayed');

                            if(quote_principal_beneficiary.length > 0) {
                                $allow_upto = $("#addPrincipalBeneficiaryField").attr('data-allow-upto');
                                $count = $("#enrollment_form .inner_principal_beneficiary_field").length;

                                $.each(quote_principal_beneficiary,function(index,beneficiary_row){
                                    $principal_beneficiary_count = $principal_beneficiary_count + 1;
                                    $display_number = $count + 1;

                                    $number = $principal_beneficiary_count;
                                    if($allow_upto == '' || $display_number <= $allow_upto){
                                        principal_beneficiary_field($number+"_"+$display_number,beneficiary_row);
                                    }
                                    $count++;
                                });
                                //quote_principal_beneficiary = [];
                            }

                        }
                        if(res.contingent_beneficiary){
                            $("#beneficiary_information_div").show();
                            $("#contingent_beneficiary_div").show();
                            
                            if(res.contingent_beneficiary_allow_upto_3){
                                $("#addContingentBeneficiaryField").attr('data-allow-upto',3);
                            }
                            $("#is_contingent_beneficiary").val('not_displayed');

                            if(quote_contingent_beneficiary.length > 0) {
                                $allow_upto = $("#addContingentBeneficiaryField").attr('data-allow-upto');
                                $count = $("#enrollment_form .inner_contingent_beneficiary_field").length;

                                $.each(quote_contingent_beneficiary,function(index,beneficiary_row){
                                    $contingent_beneficiary_count = $contingent_beneficiary_count + 1;
                                    $display_number = $count + 1;

                                    $number = $contingent_beneficiary_count;
                                    if($allow_upto == '' || $display_number <= $allow_upto){
                                        contingent_beneficiary_field($number+"_"+$display_number,beneficiary_row);
                                    }
                                    $count++;
                                });
                                //quote_contingent_beneficiary = [];
                            }
                        }
                    }
                });
            }
            
            dependent_member_field = function(){
              /*$("#dependent_inner_div").html('');
              $("#ajax_loader").show();
                  $.ajax({
                      url: '<?=$HOST?>/ajax_get_dependent_member_field_v2.php',
                      data: $("#enrollment_form").serialize(),
                      type: 'POST',
                      dataType: 'json',
                      success: function (res) {
                          $("#ajax_loader").hide();    
                          if(res.status=="success"){
                              $i=0;
                              $depedent_count=0;
                              $.each(res.dependent_array,function($k1,$v1){
                                $i++;
                                $number = $k1;
                               // $number = $i;
                                $count = $("#enrollment_form .dependent_div").length;
                                $display_number = $count + 1;
                               
                                dependent_html = $('#dependent_dynamic_relation_div').html();
                                dependent_html = dependent_html.replace(/~number~/g, $number);
                                dependent_html = dependent_html.replace(/~display_number~/g, $display_number);
                                dependent_html = dependent_html.replace(/~dependent_id~/g, 0);

                                $('#dependent_inner_div').append(dependent_html);
                                $("#dependent_main_div").show();
                                $.each(res.purchase_product_array,function($prd_key,$prd_val){
                                  $option='<option value="'+$prd_val['product_id']+'" data-id="'+$number+'" data-plan_id='+$prd_val['product_plan_id']+'>'+$prd_val['product_name']+' ['+$prd_val['plan_name']+']'+'</option>';
                                  $("#enrollment_form #dependent_product_input_"+$number).append($option);
                                  $.each($v1,function($k,$v){
                                    $("#dependent_product_input_"+$number+" option[value='"+$v['product_id']+"']").prop("selected", true);                   
                                    $("#enrollment_form #dependent_product_input_"+$number).multipleSelect();
                                    $("#enrollment_form #dependent_relation_input_id_"+$number).val($v['cd_profile_id']);
                                    $('#remove_dependent_'+$number).attr('data-dependent_id',$v['md5_cd_profile_id']);
                                    $("#dependent_relation_input_"+$number+" option[value='"+$v['relation_name']+"']").prop("selected", true).show().trigger('change');
                                  });
                                });
                                //if($display_number > 1){
                                    $("#remove_dependent_"+$number).show();
                                //}
                                
                                $("#additional_dependent_div").show();
                              });
                             
                          }else{
                            $("#dependent_inner_div").html('');
                            is_dependent_required();
                          }        
                      }
                  });*/
            }

            $(document).on("change","#existing_spouse_dependent",function(){
                var spouseId = $(this).data('id');
                if($(this).val() != ""){
                    var cd_profile_id = $(this).val();
                    var fname = $(this).find(':selected').data('fname');
                    var lname = $(this).find(':selected').data('lname');
                    var email = $(this).find(':selected').data('email');
                    var birth_date = $(this).find(':selected').data('birth_date');
                    var gender = $(this).find(':selected').data('gender');
                    var ssn = $(this).find(':selected').data('ssn');

                    $("#spouse_cd_profile_id_"+spouseId).val(cd_profile_id);
                    $('#que_spouse_fname').val(fname).addClass('has-value');
                    $('#que_spouse_lname').val(lname).addClass('has-value');
                    $('#que_spouse_email').val(email).addClass('has-value');
                    $('#que_spouse_SSN').val(ssn).addClass('has-value');
                    $('#que_spouse_birthdate').val(birth_date).addClass('has-value');
                    $("input[name='spouse_gender["+spouseId+"]'][value='"+gender+"']").prop('checked', true).trigger("click");
                    $.uniform.update();
                }else{
                    $("#spouse_cd_profile_id_"+spouseId).val(0);
                    $('#que_spouse_fname').val('').removeClass('has-value');
                    $('#que_spouse_lname').val('').removeClass('has-value');
                    $('#que_spouse_email').val('').removeClass('has-value');
                    $('#que_spouse_SSN').val('').removeClass('has-value');
                    $('#que_spouse_birthdate').val('').removeClass('has-value');
                    $("input[name='spouse_gender["+spouseId+"]']:checked").prop('checked', false).trigger("click");
                    $.uniform.update();
                }                
            });

            $(document).on("change",".existing_child_dependent",function(){
                var childId = $(this).data('id');
                if($(this).val() != ""){
                    var cd_profile_id = $(this).val();
                    var fname = $(this).find(':selected').data('fname');
                    var lname = $(this).find(':selected').data('lname');
                    var email = $(this).find(':selected').data('email');
                    var birth_date = $(this).find(':selected').data('birth_date');
                    var gender = $(this).find(':selected').data('gender');
                    var ssn = $(this).find(':selected').data('ssn');

                    $("#child_cd_profile_id_"+childId).val(cd_profile_id);
                    $('#que_child_fname_'+childId).val(fname).addClass('has-value');
                    $('#que_child_lname_'+childId).val(lname).addClass('has-value');
                    $('#que_child_email_'+childId).val(email).addClass('has-value');
                    $('#que_child_SSN_'+childId).val(ssn).addClass('has-value');
                    $('#que_child_birthdate_'+childId).val(birth_date).addClass('has-value');
                    $("input[name='child_gender["+childId+"]'][value='"+gender+"']").prop('checked', true).trigger("click");
                    $.uniform.update();
                }else{
                    $("#child_cd_profile_id_"+childId).val(0);
                    $('#que_child_fname_'+childId).val('').removeClass('has-value');
                    $('#que_child_lname_'+childId).val('').removeClass('has-value');
                    $('#que_child_email_'+childId).val('').removeClass('has-value');
                    $('#que_child_SSN_'+childId).val('').removeClass('has-value');
                    $('#que_child_birthdate_'+childId).val('').removeClass('has-value');
                    $("input[name='child_gender["+childId+"]']:checked").prop('checked', false).trigger("click");
                    $.uniform.update();
                }
                var prevValue = $(this).data('previous');
                $('.existing_child_dependent').not(this).find('option[value="'+prevValue+'"]').show();
                //hide option selected                
                var value = $(this).val();
                //update previously selected data
                $(this).data('previous',value);
                $('.existing_child_dependent').not(this).find('option[value="'+value+'"]').hide();
                $('.existing_child_dependent').find("option[value='']").show();
                $('.existing_child_dependent').selectpicker('refresh');
                


            })

            spouse_field = function(tmp_cd_profile_id,tmp_order_id){
                if(typeof(tmp_cd_profile_id) === "undefined") {
                    var tmp_cd_profile_id = 0;
                }
                if(typeof(tmp_order_id) === "undefined") {
                    var tmp_order_id = 0;
                }
                $("#ajax_loader").show();
                $.ajax({
                    url: '<?=$HOST?>/ajax_get_spouse_field.php?cd_profile_id='+tmp_cd_profile_id+"&order_id="+tmp_order_id,
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    async:false,
                    success: function (res) {
                        $("#ajax_loader").hide();    
                        if(res.status=="success"){
                            $("#addSpouseField").hide();
                            $("#dependent_spouse_main_div").html(res.html);
                            $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
                            $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                            $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
                            $(".spouse_multiple_select").multipleSelect({
                                width:'100%',
                                
                            });
                            $("#que_spouse_assign_products_"+res.number).multipleSelect({
                                width:'100%',
                                selectAll:false,
                                onClick:function(e){
                                    $value = e.value;
                                    
                                    $productPlan = $("#product_plan_"+$value).val();
                                    if($productPlan==5){

                                        $("select.child_dependent_multiple_select").each(function(){
                                            $childID = $(this).attr('data-id');
                                            if(e.checked){
                                                $("#que_child_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',true);
                                            }else{
                                                $("#que_child_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                                            }  
                                            $("#que_child_assign_products_"+$childID).multipleSelect('refresh');
                                        });

                                        $("select.spouse_dependent_multiple_select").each(function(){
                                            $childID = $(this).attr('data-id');
                                            if(res.number != $childID){
                                                if(e.checked){
                                                    $("#que_spouse_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',true);
                                                }else{
                                                    $("#que_spouse_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                                                }  
                                                $("#que_spouse_assign_products_"+$childID).multipleSelect('refresh');
                                            }
                                        });
                                        
                                    }
                                    
                                }
                            });
                            $(".spouse_select").addClass('form-control');
                            $(".spouse_select").selectpicker({
                                container: 'body', 
                                style:'btn-select',
                                noneSelectedText: '',
                                dropupAuto:false,
                            });
                            $("#existing_spouse_dependent").selectpicker({
                                container: 'body', 
                                style:'btn-select',
                                noneSelectedText: '',
                                dropupAuto:false,
                            });
                            $(".Salary_mask").priceFormat({
                                prefix: '',
                                suffix: '',
                                centsSeparator: '.',
                                thousandsSeparator: ',',
                                limit: false,
                                centsLimit: 2,
                            });
                            // common_select();
                        } else{
                            $("#addSpouseField").show();
                        }
                        checkEmail();              
                    }
                });
            }

            child_field = function(tmp_cd_profile_id,tmp_order_id){
                if(typeof(tmp_cd_profile_id) === "undefined") {
                    var tmp_cd_profile_id = 0;
                }
                if(typeof(tmp_order_id) === "undefined") {
                    var tmp_order_id = 0;
                }

                $count = $("#enrollment_form .inner_child_field").length;

                $dependent_count = $dependent_count + 1;
                $display_number = $count + 1;
                $number = $dependent_count+"_"+$display_number;

                $("#dependent_field_number").val($number);
                $("#ajax_loader").show();

                $.ajax({
                    url: '<?=$HOST?>/ajax_get_child_field.php?cd_profile_id='+tmp_cd_profile_id+"&order_id="+tmp_order_id,
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    async:false,
                    success: function (res) {
                        $("#ajax_loader").hide();    
                        if(res.status=="success"){
                            $("#dependent_child_main_div").append(res.html);
                            $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
                            $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                            $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
                            $(".child_multiple_select_"+res.number).multipleSelect({
                                width:'100%',
                            });
                            $("#que_child_assign_products_"+res.number).multipleSelect({
                                width:'100%',
                                selectAll:false,
                                onClick:function(e){
                                    $value = e.value;
                                    
                                    $productPlan = $("#product_plan_"+$value).val();
                                    if($productPlan==5){

                                        $("select.child_dependent_multiple_select").each(function(){
                                            $childID = $(this).attr('data-id');
                                            if(res.number != $childID){
                                                if(e.checked){
                                                    $("#que_child_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',true);
                                                }else{
                                                    $("#que_child_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                                                }  
                                                $("#que_child_assign_products_"+$childID).multipleSelect('refresh');
                                            }
                                        });

                                        $("select.spouse_dependent_multiple_select").each(function(){
                                            $childID = $(this).attr('data-id');
                                            if(e.checked){
                                                $("#que_spouse_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',true);
                                            }else{
                                                $("#que_spouse_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                                            }  
                                            $("#que_spouse_assign_products_"+$childID).multipleSelect('refresh');
                                        });
                                          
                                        
                                    }
                                }
                            });
                            $(".child_select_"+res.number).addClass('form-control');
                            $("#existing_child_dependent_"+res.number).selectpicker({
                                container: 'body', 
                                style:'btn-select',
                                noneSelectedText: '',
                                dropupAuto:false,
                            });
                            $(".child_select_"+res.number).selectpicker({
                                container: 'body', 
                                style:'btn-select',
                                noneSelectedText: '',
                                dropupAuto:false,
                            });
                            $(".Salary_mask").priceFormat({
                                prefix: '',
                                suffix: '',
                                centsSeparator: '.',
                                thousandsSeparator: ',',
                                limit: false,
                                centsLimit: 2,
                            });
                            // common_select();
                            if(res.number > 1){
                                $('.existing_child_dependent').each(function(index,value) {
                                    var selected = $(this).find(":selected");
                                    $('#existing_child_dependent_'+ res.number).find('option[value="'+selected.val()+'"]').hide();
                                    $id = $(this).attr('id');
                                    $('.existing_child_dependent').find("option[value='']").show();
                                    $("#"+$id).selectpicker('refresh');
                                    
                                });
                            }

                        }
                        checkEmail();                
                    }
                });
            }

            principal_beneficiary_field = function($number,beneficiary_row){
                $("#principal_beneficiary_field_number").val($number);
                $("#is_principal_beneficiary").val('displayed');

                $("#ajax_loader").show();
                $.ajax({
                    url: '<?=$HOST?>/ajax_get_principal_beneficiary_field.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    async:false,
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide();    
                        if(res.status=="success"){
                            $("#principal_beneficiary_field_div").append(res.html);
                            $(".principal_beneficiary_multiple_select_"+res.number).multipleSelect({
                                width:'100%',
                                selectAll:false,
                            });
                            if(typeof(beneficiary_row) !== "undefined") {
                                $("#principal_beneficiary_id_"+res.number).val(beneficiary_row.id);
                                var selected_option = $("#principal_existing_dependent_"+res.number+" option[data-full-name='"+beneficiary_row.name+"'][data-type='"+beneficiary_row.relationship+"']");
                                if(typeof(selected_option) !== "undefined") {
                                    var principal_existing_dependent = selected_option.attr('value');
                                    $("#principal_existing_dependent_"+res.number).val(principal_existing_dependent);
                                    $(".principal_beneficiary_select_"+res.number).addClass('has-value');

                                    selected_option.attr('data-full-name',beneficiary_row.name);
                                    selected_option.attr('data-type',beneficiary_row.relationship);
                                    selected_option.attr('data-phone',beneficiary_row.cell_phone);
                                    selected_option.attr('data-email',beneficiary_row.email);
                                    selected_option.attr('data-ssn',beneficiary_row.ssn);
                                    selected_option.attr('data-address',beneficiary_row.address);
                                }

                                $("#principal_existing_dependent_"+res.number).val(principal_existing_dependent).addClass('has-value');
                                $("#principal_queBeneficiaryFullName_"+res.number).val(beneficiary_row.name).addClass('has-value');
                                $("#principal_queBeneficiaryAddress_"+res.number).val(beneficiary_row.address).addClass('has-value');
                                $("#principal_queBeneficiaryPhone_"+res.number).val(beneficiary_row.cell_phone).addClass('has-value');
                                $("#principal_queBeneficiaryEmail_"+res.number).val(beneficiary_row.email).addClass('has-value');
                                $("#principal_queBeneficiarySSN_"+res.number).val(beneficiary_row.ssn).addClass('has-value');
                                $("#principal_queBeneficiaryRelationship_"+res.number).val(beneficiary_row.relationship).addClass('has-value');
                                $("#principal_queBeneficiaryPercentage_"+res.number).val(beneficiary_row.percentage).addClass('has-value');
                                tmpPrdArr = beneficiary_row.product_ids.split(',');
                                $("#principal_product_"+res.number).multipleSelect('setSelects',tmpPrdArr);
                            }

                            $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
                            $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                            $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
                            $(".principal_beneficiary_select_"+res.number).addClass('form-control');
                            $(".principal_beneficiary_select_"+res.number).selectpicker({
                                container: 'body', 
                                style:'btn-select',
                                noneSelectedText: '',
                                dropupAuto:false,
                            });
                            
                        }
                        checkEmail();                
                    }
                });
            }

            contingent_beneficiary_field = function($number,beneficiary_row){
                $("#contingent_beneficiary_field_number").val($number);
                $("#is_contingent_beneficiary").val('displayed');
                $("#ajax_loader").show();
                $.ajax({
                    url: '<?=$HOST?>/ajax_get_contingent_beneficiary_field.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    async:false,
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide();    
                        if(res.status=="success"){
                            $("#contingent_beneficiary_field_div").append(res.html);

                            $(".contingent_beneficiary_multiple_select_"+res.number).multipleSelect({
                                width:'100%',
                                selectAll:false,
                            });
                            
                            if(typeof(beneficiary_row) !== "undefined") {
                                $("#contingent_beneficiary_id_"+res.number).val(beneficiary_row.id);
                                var selected_option = $("#contingent_existing_dependent_"+res.number+" option[data-full-name='"+beneficiary_row.name+"'][data-type='"+beneficiary_row.relationship+"']");
                                if(typeof(selected_option) !== "undefined") {
                                    var contingent_existing_dependent = selected_option.attr('value');
                                    $("#contingent_existing_dependent_"+res.number).val(contingent_existing_dependent);
                                    $(".contingent_beneficiary_select_"+res.number).addClass('has-value');

                                    selected_option.attr('data-full-name',beneficiary_row.name);
                                    selected_option.attr('data-type',beneficiary_row.relationship);
                                    selected_option.attr('data-phone',beneficiary_row.cell_phone);
                                    selected_option.attr('data-email',beneficiary_row.email);
                                    selected_option.attr('data-ssn',beneficiary_row.ssn);
                                    selected_option.attr('data-address',beneficiary_row.address);
                                }                                
                                $("#contingent_queBeneficiaryFullName_"+res.number).val(beneficiary_row.name).addClass('has-value');
                                $("#contingent_queBeneficiaryAddress_"+res.number).val(beneficiary_row.address).addClass('has-value');
                                $("#contingent_queBeneficiaryPhone_"+res.number).val(beneficiary_row.cell_phone).addClass('has-value');
                                $("#contingent_queBeneficiaryEmail_"+res.number).val(beneficiary_row.email).addClass('has-value');
                                $("#contingent_queBeneficiarySSN_"+res.number).val(beneficiary_row.ssn).addClass('has-value');
                                $("#contingent_queBeneficiaryRelationship_"+res.number).val(beneficiary_row.relationship).addClass('has-value');
                                $("#contingent_queBeneficiaryPercentage_"+res.number).val(beneficiary_row.percentage).addClass('has-value');
                                tmpPrdArr = beneficiary_row.product_ids.split(',');
                                $("#contingent_product_"+res.number).multipleSelect('setSelects',tmpPrdArr);
                                
                            }

                           $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
                            $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                            $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
                            $(".contingent_beneficiary_select_"+res.number).addClass('form-control');
                            $(".contingent_beneficiary_select_"+res.number).selectpicker({
                                container: 'body', 
                                style:'btn-select',
                                noneSelectedText: '',
                                dropupAuto:false,
                            });
                            
                        }
                        checkEmail();                
                    }
                });
            }

        //******************** Details tab Code end   *******************************
        
        //******************** Enroll tab Code start   *******************************
            verification_option = function(){
                $("#ajax_loader").show();
                $("#verification_option_div").html('');
                $("#product_checkbox_signature_div").html('');
                $("#verification_option_html_div").hide();
                $.ajax({
                    url: '<?=$HOST?>/ajax_get_verification_option.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide(); 
                        $verification_option_product_change = false;   
                        if(res.status=="success"){
                            $("#member_verification_div").show();
                            $("#verification_option_div").html(res.html);

                            if(res.product_checkbox_html){
                                $("#product_checkbox_signature_div").html(res.product_checkbox_html);
                                $('[data-toggle="tooltip"]').tooltip();
                            }
                            $('#email_content').summernote({
                              toolbar: $SUMMERNOTE_TOOLBAR,
                              disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
                              focus: true, // set focus to editable area after initializing summernote
                              height:125,
                              callbacks: {
                                onKeyup: function(e) {
                                },
                                onImageUpload: function(image) {
                                  editor = $(this);
                                  uploadImageContent(image[0], editor);
                                },
                                onMediaDelete : function(target) {
                                    deleteImage(target[0].src);
                                    target.remove();
                                }
                              }
                            });
                            $(".application_type").not('.js-switch').uniform();
                            $("#product_terms_check_all").uniform();
                            $(".product_terms_check").uniform();


                            if(enrollmentLocation == "self_enrollment_site") {
                                $("#application_type_member_signature").prop('checked',true).trigger('click');
                                $("#application_type_member_signature").trigger('change');
                                $("#verification_option_title").hide();
                                $("#verification_option_div").hide();
                            }
                        }                
                    }
                });
            }

            enrollment_summary = function(){
                $("#ajax_loader").show();
                $("#enrollment_summary_details_div").html('');
                $("#post_date_payment_div").hide();
                $.ajax({
                    url: '<?=$HOST?>/ajax_get_enrollment_summary.php',
                    data: $("#enrollment_form").serialize(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        $("#ajax_loader").hide(); 
                        $verification_option_product_change = false;   
                        if(res.status=="success"){
                            $("#enrollment_summary_div").show();

                            $.each(res.summaryList, function ($k, $v) {
                                $product_id=$v['product_id'];
                                $product_name=$v['product_name'];
                                $carrier_name=$v['carrier_name'];
                                $primary_member_name=$v['primary_member_name'];
                                $plan_name=$v['plan_name'];
                                $product_total=$v['product_total'];
                                $display_product_total=$v['display_product_total'];
                                $dependent_count=$v['dependent_count'];
                                $coverage_date=$v['coverage_date'];
                                $coverage_end_date=$v['coverage_end_date'];
                                $startView=$v['startView'];
                                $minViewMode=$v['minViewMode'];
                                $coverage_period=$v['coverage_period'];
                                $datesDisabled = $v['datesDisabled'];
                                $member_payment_type = $v['member_payment_type'];

                                summary_html = $('#enrollment_summary_details_dynamic_div').html();
                                summary_html = summary_html.replace(/~product_id~/g, $product_id);
                                summary_html = summary_html.split('~product_name~').join($product_name);
                                summary_html = summary_html.split('~carrier_name~').join($carrier_name);
                                summary_html = summary_html.split('~primary_member_name~').join($primary_member_name);
                                summary_html = summary_html.split('~plan_name~').join($plan_name);
                                summary_html = summary_html.replace(/~product_total~/g, $display_product_total);
                                summary_html = summary_html.replace(/~dependent_count~/g, $dependent_count);
                                summary_html = summary_html.replace(/~member_payment_type~/g, $member_payment_type);
                                $('#enrollment_summary_details_div').append(summary_html); 

                                $("#coverage_date_"+$product_id).val($coverage_date);

                                if ($.inArray($product_id,coverage_date_prd_arr) !== -1) {
                                    if(coverage_date_date_arr[$product_id] && coverage_date_date_arr[$product_id]!=""){
                                        $("#coverage_date_"+$product_id).val(coverage_date_date_arr[$product_id]);
                                    }
                                }
                                datesDisabledArr[$product_id] = $datesDisabled;
                                $('#coverage_date_'+$product_id).datepicker({
                                    startDate: $coverage_date,
                                    endDate: $coverage_end_date,
                                    orientation: "bottom",
                                    startView: $startView,
                                    minViewMode: $minViewMode,
                                    datesDisabled : $datesDisabled,
                                }).on('changeDate', function(ev){
                                    setPostDate();
                                }).on('blur', function () {
                                    $tmpProductID = $(this).attr('data-product-id');
                                    $tmpDisabledArr = datesDisabledArr[$tmpProductID];
                                    
                                    if($tmpDisabledArr.length > 0){
                                        if ($.inArray(this.value, $tmpDisabledArr) !== -1) {
                                            $(this).val("").datepicker("update");
                                        }
                                    }
                                });
                            });

                            coverage_date_prd_arr = [];
                            coverage_date_date_arr = [];

                            $('#expiration').datepicker({
                                format: 'mm/yy',
                                startView : 1,
                                minViewMode: 1,
                                autoclose: true,    
                                startDate:new Date(),
                                endDate : '+15y'
                            });
                            setPostDate();    
                            fRefresh();
                        }else{
                            $("#enrollment_summary_div").hide();
                        }         
                    }
                });
                
                $bill_name = $("#que_primary_fname").val() + " " + $("#que_primary_lname").val();
                $bill_address = $("#que_primary_address1").val();
                $bill_address2 = $("#que_primary_address2").val();
                $bill_city = $("#que_primary_city").val();
                $bill_state = $("#que_primary_state").val();
                $bill_zip = $("#que_primary_zip").val();
                
                $("#bill_name").val($bill_name);
                $("#bill_address").val($bill_address);
                $("#bill_address2").val($bill_address2);
                $("#bill_city").val($bill_city);
                $("#bill_state").val($bill_state).change();
                $("#bill_zip").val($bill_zip);

                $("#display_bill_name").html($bill_name);
                $("#display_bill_address").html($bill_address);
                $("#display_bill_address2").html($bill_address2);
                $("#display_bill_city").html($bill_city);
                $("#display_bill_state").html($bill_state);
                $("#display_bill_zip").html($bill_zip);
            }
            setPostDate = function(){
                
                var effective_dates = [];
                var cnt = 0;
                var is_annualy = true;
                $("#enrollment_form .coverage_date_input").each(function(index,element){
                    effective_dates[cnt] = $(this).val();
                    $coverage_product_id = $(this).attr('data-product-id');
                    $("#td_terms_products_"+$coverage_product_id).html($(this).val());
                    if($(this).attr('data-member-payment-type') == 'Monthly'){
                        is_annualy = false;
                    }
                    cnt++;
                });
                if(effective_dates.length > 0) {

                    var lowest_effective_date = effective_dates.reduce(function (a, b) { return a < b ? a : b; }); 
                    
                    var next_billing_date = moment(lowest_effective_date).add(1, 'M').add(-1,'d').format("MM/DD/YYYY");
                    if(is_annualy){
                        next_billing_date = moment(lowest_effective_date).add(1, 'Y').add(-1,'d').format("MM/DD/YYYY");
                    }
                    $("#summary_next_billing_date").html(next_billing_date);
                    if(lowest_effective_date == "<?=date('m/d/Y',strtotime('+1 day'))?>") {
                        hidePostDate();
                    } else {
                        $("#post_date_payment_div").show();
                        var old_post_date = $("#post_date").val();
                        if((new Date(lowest_effective_date) < new Date(old_post_date)) || lowest_effective_date == old_post_date) {
                            $("#post_date").val(moment(lowest_effective_date).add(-1,'d').format("MM/DD/YYYY"));
                        }
                        try{ $('#post_date').data('datepicker').remove(); }catch(e){}
                        $("#post_date").datepicker({
                            startDate: "<?=date("m/d/Y",strtotime("+1 days"))?>",
                            endDate: moment(lowest_effective_date).add(-1,'d').format("MM/DD/YYYY"),
                            orientation: "bottom",
                            enableOnReadonly: true
                        });
                    }
                }
            }
            hidePostDate = function(){
                if($("#post_date_payment_div").is(":visible")){
                    $("#enroll_with_post_date").prop("checked",false).trigger("change");
                    $.uniform.update();
                    $("#post_date_payment_div").hide();
                }else{
                    if(is_future_payment){
                        $("#enroll_with_post_date").prop("checked",false).trigger("change");
                        $.uniform.update();
                        $("#post_date_payment_div").hide();
                    }
                }
            }
        //******************** Enroll tab Code end   *******************************
        
        //******************** Order Receipt Window Code start *******************************
            function edit_contact_detail() {
                colorBoxClose();
                $('[href="#basic_detail"]').trigger("click");
                scrollToElement($('#title_primary_contact'));
            }

            function edit_address_detail() {
                colorBoxClose();
                $('[href="#basic_detail"]').trigger("click");
                scrollToElement($('#title_primary_contact'));
            }

            function edit_account_detail() {
                colorBoxClose();
                $('[href="#basic_detail"]').trigger("click");
                scrollToElement($('#title_primary_contact'));
            }

            function edit_dependant_detail() {
                colorBoxClose();
                $('[href="#basic_detail"]').trigger("click");
                scrollToElement($('#title_dependant_detail'));
            }
        //******************** Order Receipt Window Code end   *******************************
    //******** Functions Code end   *******************

    count_dep_age = function ($dob, $val) {
        if ($dob != '' && $dob != "__/__/____") {
            $dob = new Date($dob);
            var today = new Date();
            var age = Math.floor((today - $dob) / (365.25 * 24 * 60 * 60 * 1000));
            $("#dependent_age_input_" + $val).val(age);
            $("#dependent_age_input_" + $val).addClass('has-value');
        } else {
            $("#dependent_age_input_" + $val).val('');
            $("#dependent_age_input_" + $val).removeClass('has-value');
        }
    }

    count_age = function ($dob) {

        if ($dob != '' && $dob != "__/__/____") {
            $dob = new Date($dob);
            var today = new Date();
            var age = Math.floor((today - $dob) / (365.25 * 24 * 60 * 60 * 1000));
            $("#age_input").val(age);
            $("#age_input").addClass('has-value');
        } else {
            $("#age_input").val('');
            $("#age_input").removeClass('has-value');
        }
    }

    function reload_page(open_new_enrollment)
    {
        if(typeof(open_new_enrollment) === "undefined") {
            open_new_enrollment = false;
        }
        var reload_url = '';
        if(enrollmentLocation == "agentSide") {
            if(open_new_enrollment == true) {
                reload_url = "<?=$AGENT_HOST?>/member_enrollment.php";
            } else {
                reload_url = "<?=$AGENT_HOST?>";
            }

        } else if(enrollmentLocation == "groupSide") {
            if(open_new_enrollment == true) {
                reload_url = "<?=$GROUP_HOST?>/member_enrollment.php";
            } else {
                reload_url = "<?=$GROUP_HOST?>";
            }
        
            $siteUseName = $("#site_user_name").val();
            $payment_type = $("#payment_type").val();
            $memberId = $("#md5_customer_id").val();
            $orderId = $("#md5_order_id").val();
            if($siteUseName !=='' && $payment_type!==''){
                if($payment_type === 'list_bill' || $payment_type === 'tpa'){
                    reload_url="<?=$HOST?>/enrollment_successfull.php?memberId="+$memberId;
                }else{
                    reload_url="<?=$HOST?>/enrollment_successfull.php?orderId="+$orderId;
                }
            }else if($siteUseName !==''){
                reload_url = "<?=$AAE_WEBSITE_HOST?>/" + $siteUseName;
            }
        } else if(enrollmentLocation == "adminSide") {
            reload_url = "<?=$ADMIN_HOST?>/members_details.php?id=<?=md5($customer_id)?>";

        } else if(enrollmentLocation == "aae_site") {
            reload_url = "<?=$AAE_WEBSITE_HOST?>/" + $("#site_user_name").val();

        } else if(enrollmentLocation == "self_enrollment_site") {
            reload_url = "<?=$ENROLLMENT_WEBSITE_HOST?>/" + $("#site_user_name").val();

        }

        if(reload_url == '') {
            window.location.reload();
        } else {
            window.location.href = reload_url;
        }
    }

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

      window.onresize = resizeCanvas;

      clearButton.addEventListener("click", function(event) {
        signaturePad.clear();
        $("#signature_name").val("");
        $("#hdn_signature_data").val("");
      });
  //******** Signature Code end   *******************

  function calculateSTDRate($annual_salary,$percentage,$product_id,$monthly_benefit_temp=0,$monthly_benefit_input=false){
    if($monthly_benefit_temp){
        $monthly_salary = $annual_salary.replace(/,/g, "") / 12;
        $percentage = parseFloat($monthly_benefit_temp.replace(/,/g, "")) / parseFloat($monthly_salary) * 100;
        is_slider_updated = true;
        $percentage = Math.floor(parseFloat($percentage) * 100) / 100;
        $('.rangeslider_' + $product_id).asRange('set', $percentage);
        $('#monthly_benefit_amount_' + $product_id).focus();
        is_slider_updated = false;
        calculateSTDRate($annual_salary,parseFloat($percentage),$product_id,0,$monthly_benefit_input); 
    }else{
        $monthly_salary = parseFloat($annual_salary.replace(/,/g, "")) / 12;
        $monthly_benefit = (parseFloat($monthly_salary) * (Math.floor(parseFloat($percentage) * 100) / 100) / 100);
        $monthly_benefit = $monthly_benefit.toFixed(2);
        if(!is_slider_updated && !$monthly_benefit_input){
            if($monthly_benefit == 'NaN'){
                $monthly_benefit = 0.00;
            }
            $('#monthly_benefit_amount_' + $product_id).val($monthly_benefit);
        }
        is_slider_updated = false;
    }

  }
    function parseF(amount) {
        if(typeof(amount) !== 'undefined'){
            amount = amount.toString().replace(/[^0-9.]/g,"");
            amount = parseFloat(amount);
            if(isNaN(amount)) {
                amount = 0;
            }
            return amount.toFixed(2);
        } else {
            return 0.00;
        }
    }

    function calculate_tax_deduction_by_product($product_id)
    {
        if(typeof(pre_tax_deductions[$product_id]) !== "undefined" && pre_tax_deductions[$product_id].length > 0) {
            $.each(pre_tax_deductions[$product_id],function(ind,val){
                if(typeof(val['deduction_name']) !== "undefined") {
                    calculate_tax_deduction_row('pre_tax',ind,$product_id,true);
                }
            });
        }
        if(typeof(post_tax_deductions[$product_id]) !== "undefined" && post_tax_deductions[$product_id].length > 0) {
            $.each(post_tax_deductions[$product_id],function(ind,val){
                if(typeof(val['deduction_name']) !== "undefined") {
                    calculate_tax_deduction_row('post_tax',ind,$product_id,true);
                }
            });
        }
    }

    function calculate_tax_deduction_row($tax_type,$row_index,$product_id,$is_from_arr)
    {   
        if(typeof($is_from_arr) !== "undefined") {
            if($tax_type == "pre_tax") {
                var $amount = pre_tax_deductions[$product_id][$row_index]['deduction_amount'];
                var $deduction_method = pre_tax_deductions[$product_id][$row_index]['deduction_method'];
            } else {
                var $amount = post_tax_deductions[$product_id][$row_index]['deduction_amount'];
                var $deduction_method = post_tax_deductions[$product_id][$row_index]['deduction_method'];
            }            
        } else {            
            var $amount = parseF($("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).val());
            var $deduction_method = $("#"+$tax_type+"_deduction_method_"+$product_id+"_"+$row_index).val();
        }
        var $gap_payroll_type = $("[name='gap_payroll_type_primary_"+$product_id+"']:checked").val();
        
        var $gross_income = 0;
        var $row_total = 0;
        if($gap_payroll_type == "Hourly"){
            $('input[id^="gap_payroll_type_hourly_wage_primary_'+$product_id+'"]').each(function(ind,ele){
                var $tmp_ind = $(this).data('index');
                
                var $hourly_wage = parseF($("#gap_payroll_type_hourly_wage_primary_"+$product_id+"_"+$tmp_ind).val());
                var $hours = parseF($("#gap_payroll_type_hours_primary_"+$product_id+"_"+$tmp_ind).val());
                $gross_income += $hourly_wage * $hours;
            });
        } else if($gap_payroll_type == "Salary"){
            $gross_income = parseF($("#gap_payroll_type_salary_primary_"+$product_id).val());
            
            var pay_frequency = $("#gap_pay_frequency_primary_"+$product_id).val();
            if(pay_frequency == "DAILY") {
                $gross_income = $gross_income / 260;

            } else if(pay_frequency == "WEEKLY") {
                $gross_income = $gross_income / 52;

            } else if(pay_frequency == "BI_WEEKLY") {
                $gross_income = $gross_income / 26;

            } else if(pay_frequency == "SEMI_MONTHLY") {
                $gross_income = $gross_income / 24;

            } else if(pay_frequency == "MONTHLY") {
                $gross_income = $gross_income / 12;

            } else if(pay_frequency == "QUARTERLY") {
                $gross_income = $gross_income / 4;

            } else if(pay_frequency == "SEMI_ANNUAL") {
                $gross_income = $gross_income / 2;

            } else if(pay_frequency == "ANNUAL") {
                $gross_income = $gross_income / 1;
            }
            $gross_income = parseF($gross_income);
        }

        if($deduction_method == "fixed_amount") {
            $row_total = $amount;
        } else if($deduction_method == "gross_pay") {
            $row_total = (($gross_income * $amount) / 100);
        }
        $("."+$tax_type+"_deduction_row_total_"+$product_id+"_"+$row_index).html('$' + parseF($row_total));
        if(typeof($is_from_arr) !== "undefined") {
            if($tax_type == "pre_tax") {
                pre_tax_deductions[$product_id][$row_index]['deduction_row_total'] = '$' + parseF($row_total);
            } else {
                post_tax_deductions[$product_id][$row_index]['deduction_row_total'] = '$' + parseF($row_total);
            }            
        }
        calculate_tax_deduction_total($tax_type,$product_id);
    }

    function calculate_tax_deduction_total($tax_type,$product_id)
    {
        var $total_deduction = 0.0;
        $("."+$tax_type+"_deduction_row_"+$product_id).each(function(index,ele){
            if($(this).data('index') >= 0) {
                var row_total_amount = parseF($(this).find('.row_total_amount').html());
                $total_deduction = +$total_deduction + +row_total_amount;
            }
        });
        $("."+$tax_type+"_deductions_total_"+$product_id).html('$'+parseF($total_deduction));
    }

    function add_deduction_row($product_id,$row_index,$tax_type,$row_data)
    {
        var $row_html = $("."+$tax_type+"_deduction_row_template").html();
        $row_html = $row_html.replace(/~product_id~/g, $product_id);
        $row_html = $row_html.replace(/~index~/g, $row_index);
        $("tr."+$tax_type+"_deduction_row_" + $product_id + ":last").after($row_html);
        
        if(typeof($row_data) !== "undefined") {
            $("#"+$tax_type+"_deduction_name_"+$product_id+"_"+$row_index).val($row_data.deduction_name);
            $("#"+$tax_type+"_deduction_method_"+$product_id+"_"+$row_index).val($row_data.deduction_method);
            $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).val($row_data.deduction_amount);
            $("."+$tax_type+"_deduction_row_total_"+$product_id+"_"+$row_index).html($row_data.deduction_row_total);

            $("#"+$tax_type+"_deduction_name_"+$product_id+"_"+$row_index).addClass('has-value');
            $("#"+$tax_type+"_deduction_method_"+$product_id+"_"+$row_index).addClass('has-value');
            $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).unpriceFormat();
            if($row_data.deduction_method == "fixed_amount") {
                $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).priceFormat({
                    prefix: '',
                    suffix: '',
                    centsSeparator: '.',
                    thousandsSeparator: ',',
                    limit: false,
                    centsLimit: 2,
                });
            }
            if($row_data.deduction_method == "gross_pay") {
                $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).priceFormat({
                    prefix: '',
                    suffix: '',
                    centsSeparator: '.',
                    thousandsSeparator: ',',
                    limit: 4,
                    centsLimit: 2,
                });
            }
        }

        $("#"+$tax_type+"_deduction_method_"+ $product_id+"_"+$row_index).addClass('form-control');
        $("#"+$tax_type+"_deduction_method_"+ $product_id+"_"+$row_index).selectpicker({
            container: 'body',
            style: 'btn-select',
            noneSelectedText: '',
            dropupAuto: false,
        });
    }

    function display_tax_deduction($tax_type,$product_id)
    {
        var deduction_data = [];
        var deduction_html = "";
        var cnt = 0;
        $("."+$tax_type+"_deduction_row_"+$product_id).each(function(index,ele){
            var $row_index = $(this).data('index');
            if($row_index >= 0) {
                var deduction_name = $("#"+$tax_type+"_deduction_name_"+$product_id+"_"+$row_index).val();
                var deduction_method = $("#"+$tax_type+"_deduction_method_"+$product_id+"_"+$row_index).val();
                var deduction_amount = $("#"+$tax_type+"_deduction_amount_"+$product_id+"_"+$row_index).val();
                var deduction_row_total = $("."+$tax_type+"_deduction_row_total_"+$product_id+"_"+$row_index).html();

                deduction_html += "<tr class='"+$tax_type+"_deduction_row2_"+$product_id+"_"+cnt+"'>";
                    deduction_html += "<td><strong>"+ deduction_name +"</strong></td>";
                    deduction_html += "<td>"+ (deduction_method == "fixed_amount"?"Fixed Amount":"Gross Pay") +"</td>";
                    deduction_html += "<td class='"+$tax_type+"_deduction_row_total_"+$product_id+"_"+$row_index+"'>"+ deduction_row_total +"</td>";
                    deduction_html += "<td><a href='javascript:void(0);' class='text-action deduction_remove_row2' data-product_id='"+$product_id+"' data-tax_type='"+$tax_type+"' data-index='"+cnt+"'>X</a></td>";
                deduction_html += "</tr>";

                deduction_data[cnt] = {
                    'deduction_name':deduction_name,
                    'deduction_method':deduction_method,
                    'deduction_amount':deduction_amount,
                    'deduction_row_total':deduction_row_total,
                };
                cnt++;
            }
        });
        if($tax_type == "pre_tax") {
            pre_tax_deductions[$product_id] = deduction_data;
        }
        if($tax_type == "post_tax") {
            post_tax_deductions[$product_id] = deduction_data;   
        }
        $("."+$tax_type+"_deductions_tbody_"+$product_id).html(deduction_html);
        if(deduction_data.length > 0) {
            $("#input_"+$tax_type+"_deductions_"+$product_id).val(JSON.stringify(deduction_data));
        } else {
            $("#input_"+$tax_type+"_deductions_"+$product_id).val('');
        }
    }

    function get_enrollment_verification_status($sent_via,$sent_to_member,$id,$enrollmentLocation,$is_add_product){
        $.ajax({
            url: "<?=$HOST?>/ajax_enrollment_status.php?sent_via="+$sent_to_member+"&id="+$id+"&enrollmentLocation="+$enrollmentLocation+"&is_add_product="+$is_add_product+'&site_user_name='+$("#site_user_name").val(),
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if(res.status == 'success'){
                    $('.verification_track_div').html(res.html);
                }else{
                    setNotifyError(res.message);
                    return false;
                }
            },
        });

        $verification_running = true;

        setTimeout(function(){
            get_enrollment_verification_status($sent_via,$sent_to_member,$id,enrollmentLocation,$is_add_product);
        },15000);
    }

    function update_primary_benefit_amount($product_id,$benefit_amount) {
        $benefit_amount = parseF($benefit_amount);
        $('input[name="hidden_primary['+$product_id+'][1][benefit_amount]"]').val($benefit_amount);
        $('select[name="primary['+$product_id+'][1][benefit_amount]"]').html('<option value="'+$benefit_amount+'" selected>'+$benefit_amount+'</option>');
        $('select[name="primary['+$product_id+'][1][benefit_amount]"]').removeAttr('disabled');
        return true;
    }
  function removeNumberSpecialChar(str) {
        str.val(str.val().replace(/[^a-zA-Z-' ]/g, ""));
  }

  function removeCharAndSpecialChar(str) {
        str.val(str.val().replace(/[^0-9\/]/g, ""));
  }

  function notAllowFututeDate(field){
    $(document).on("focus","#"+field,function(){
        $(this).datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: new Date(),
        });
    });
  }
</script>