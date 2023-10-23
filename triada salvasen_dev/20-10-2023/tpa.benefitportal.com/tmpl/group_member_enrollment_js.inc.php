<script type="text/javascript">
   var exit_by_system = false;
   $(window).bind('beforeunload', function(){
      if(exit_by_system === false) {
         return 'Are you sure you want leave?';
      }
   });

   var enrollmentLocation = "<?=isset($enrollmentLocation)?$enrollmentLocation:'groupSide'?>";
   var datesDisabledArr = [];
   var vs_data = [];
   var add_refresh_request = false;
   var coverage_date_prd_arr=<?php echo isset($coverage_date_selection_prd_array)?json_encode($coverage_date_selection_prd_array):'[]'; ?>;
   var coverage_date_date_arr=<?php echo isset($coverage_date_selection_date_array)?json_encode($coverage_date_selection_date_array):'[]'; ?>;
   var is_future_payment = 'false';
   var $primary_product_change = true;
   var $spouse_product_change = false;
   var benefit_tier_arr = [];
   var remove_product_array = [];
   var quote_principal_beneficiary  = [];
   var quote_contingent_beneficiary = [];
   var already_puchase_product = [];
   var codeExecuted = false;
   var calledTakeHomePay = false;
   var tempproductlist = '';
   //var selectedQuestions = {'select':[],'multiselect':[],'checkbox':[],'radio':[]};
   var radio_array = [];

   var is_amount_accepted = 'N';
   var take_home_breakdown = 'N';

   var cart_products=<?php echo isset($quote_products)?json_encode($quote_products):'[]'; ?>;
   var $waive_coverage=<?php echo isset($waive_coverage)?json_encode($waive_coverage):'[]'; ?>;
   $autoAssignedProductArr = [];
   $requiredProductArr = [];

   $is_auto_assign_product = false;

   $enrolleeElements = [];
   $enrolleeElementsVal = {};
   $tmpEnrolleeElementsVal = {};

   $principal_beneficiary_count = 0;
   $contingent_beneficiary_count = 0;
   $dependent_count = 0;

   $verified_address = false;
   $verification_running = false;

   var is_slider_updated = false;
   var is_add_product = <?=isset($is_add_product)?$is_add_product:0;?>;
   var primary_additional_data = <?php echo !empty($primary_additional_data)?json_encode($primary_additional_data):'[]'; ?>;
   var spouse_dep = <?php echo !empty($spouse_dep)?json_encode($spouse_dep):'[]'; ?>;
   var child_dep = <?php echo !empty($child_dep)?json_encode($child_dep):'[]'; ?>;
   var quote_contingent_beneficiary = <?php echo !empty($contingent_beneficiary)?json_encode($contingent_beneficiary):'[]'; ?>;
   var quote_principal_beneficiary = <?php echo !empty($principal_beneficiary)?json_encode($principal_beneficiary):'[]'; ?>;
   var display_admin_fee = '<?=isset($group_billing_method) && $group_billing_method == 'list_bill' ? false : true?>';



   $(document).ready(function(){

      $('#gap_pay_frequency').val($("#gap_pay_frequency_form").val());

      $(".Salary_mask").priceFormat({
         prefix: '',
         suffix: '',
         centsSeparator: '.',
         thousandsSeparator: ',',
         limit: false,
         centsLimit: 2,
      });

      $(".enrollmentLeftmenuActive [data-stepli='"+($("#step").val())+"']").addClass('active');
      $(".enrollmentLeftmenuActive li.selfGuidingBenefitsMenu").hide();

      $(".date_picker").datepicker({
         changeDay: true,
         changeMonth: true,
         changeYear: true
      });

      $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
      $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
      $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
      $(".zip").inputmask({"mask": "99999",'showMaskOnHover': false});

      $('.priceControl').priceFormat({
         prefix: '$',
         suffix: '',
         centsSeparator: '.',
         thousandsSeparator: ',',
         limit: false,
         centsLimit: 2,
      });

      /*----------- waived_coverage ------- */
      $(document).off('click',".btn_waived_coverage");
      $(document).on('click', '.btn_waived_coverage', function(e){
         e.stopPropagation();
         cat_id = $(this).attr('data-category_id');
         $.colorbox({
            inline:true,
            href:"#waive_coverage_popup_"+cat_id,
            height:"450px",
            width:"575px",
            closeButton:true,
            escKey:false,
            overlayClose:false,
         });
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
      /*-----------/waived_coverage ------- */

      if(spouse_dep.length > 0) {
         $("#addSpouseCoverage").trigger('click');
         $(".spouseCoverageData.spouse_fname_1").val(spouse_dep[0].fname);
         $(".spouseCoverageData.spouse_gender_1[value='"+spouse_dep[0].gender+"']").trigger('click');
         $(".spouseCoverageData.spouse_birthdate_1").val(spouse_dep[0].birthdate);
         $(".spouseCoverageData").blur();
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
         $(".childCoverageData").blur();
      }
   
      /*----------- esign section ------- */
      //Start voiceRecording
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
      //End voiceRecording

      $(document).off("click",".terms_popup");
      $(document).on("click",".terms_popup",function(e){
         e.preventDefault();
         $product_list = $("#product_list").val();
         $tmpsponsor_id = '<?=md5($sponsor_id)?>';
         $link = "<?=$HOST?>/verification_terms.php?display_member_terms=Y&sponsor_id="+$tmpsponsor_id;
         $.colorbox({
             href : $link,
             iframe: 'true', 
             width: '800px', 
             height: '750px'
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
                height: '750px'
            });   
      });
   
      $(document).off("click",".prd_terms_popup");
      $(document).on("click",".prd_terms_popup",function(e){
         e.preventDefault();
         $product_id = $(this).attr('data-product-id');
         $link = "<?=$HOST?>/prd_term_popup.php?product_id="+$product_id;
         $.colorbox({
             href : $link,
             iframe: 'true', 
             width: '800px', 
             height: '600px'
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
                    
                    signaturePadInit();
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
      /*-----------/esign section ------- */
   });
   
   $(document).on("focus",".date_picker",function(){
        $(this).datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: new Date(),
        });
    });

   $(document).off("click","#edit_enroll");
   $(document).on("click","#edit_enroll",function(e){
      $('#edit_show_enroll').show();
      $('#edit_hide_enroll').hide();
      $('#edit_enroll').hide();
   });

   $(document).off("click","#edit_primary");
   $(document).on("click","#edit_primary",function(e){
      $('#edit_show_primary').show();
      $('#edit_show_primary_additional').show();
      $('#edit_hide_primary').hide();
      $('#edit_hide_primary_additional').hide();
      $('#edit_primary').hide();
   });

   $(document).off("click","#addSpouseCoverage");
   $(document).on("click","#addSpouseCoverage",function(e){
      if($(this).hasClass('active')){
         $(this).removeClass('active');
         $('#spouseCoverageMainDiv').html('');
            if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
                $del_spouse_fname_key = 'spouse_fname_1';
                $del_spouse_gender_key = 'spouse_gender_1';
                $del_spouse_birth_key = 'spouse_birthdate_1';

                if($tmpEnrolleeElementsVal.hasOwnProperty($del_spouse_fname_key)){
                    delete $tmpEnrolleeElementsVal[$del_spouse_fname_key];
                }

                if($tmpEnrolleeElementsVal.hasOwnProperty($del_spouse_gender_key)){
                    delete $tmpEnrolleeElementsVal[$del_spouse_gender_key];
                }

                if($tmpEnrolleeElementsVal.hasOwnProperty($del_spouse_birth_key)){
                    delete $tmpEnrolleeElementsVal[$del_spouse_birth_key];
                }
            }
      }else{
         $(this).addClass('active');
         html = $('#spouseCoverageDynamicDiv').html();
         $('#spouseCoverageMainDiv').html(html);
         $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 
      }
      fRefresh();
      $('[data-toggle="tooltip"]').tooltip();
   });

   $(document).off("click","#spouseVerification");
   $(document).on("click","#spouseVerification", function(e){
      $('[data-toggle="popover"]').popover();
      $('#spouseVerificationDiv').show();
      $("#spouseVerificationAlready").hide();
   });

   $(document).off("click","#spouseVerificationRemove");
   $(document).on("click","#spouseVerificationRemove", function(e){
      $("#spouseVerificationAlready").show();
      $('#spouseVerificationDiv').hide();
   });

   $(document).off("click","#spouseCoverageRemove");
   $(document).on("click","#spouseCoverageRemove", function(e){
      $('[data-toggle="tooltip"]').tooltip('hide');
      if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
        $del_spouse_fname_key = 'spouse_fname_1';
        $del_spouse_gender_key = 'spouse_gender_1';
        $del_spouse_birth_key = 'spouse_birthdate_1';

        if($tmpEnrolleeElementsVal.hasOwnProperty($del_spouse_fname_key)){
            delete $tmpEnrolleeElementsVal[$del_spouse_fname_key];
        }

        if($tmpEnrolleeElementsVal.hasOwnProperty($del_spouse_gender_key)){
            delete $tmpEnrolleeElementsVal[$del_spouse_gender_key];
        }

        if($tmpEnrolleeElementsVal.hasOwnProperty($del_spouse_birth_key)){
            delete $tmpEnrolleeElementsVal[$del_spouse_birth_key];
        }
      }
      $("#addSpouseCoverage").removeClass('active');
      $("#spouseCoverageMainDiv").html('');
   });

   $(document).off("click","#addChildCoverage");
   $(document).on("click","#addChildCoverage",function(e){
      if($(this).hasClass('active')){
        $('#childCoverageMainDiv .display_number').each(function(){
            $del_count = parseInt($(this).attr('data-display-number'));
            if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
                $del_child_fname_key = 'child_fname_'+$del_count;
                $del_child_gender_key = 'child_gender_'+$del_count;
                $del_child_birth_key = 'child_birthdate_'+$del_count;

                if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_fname_key)){
                    delete $tmpEnrolleeElementsVal[$del_child_fname_key];
                }

                if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_gender_key)){
                    delete $tmpEnrolleeElementsVal[$del_child_gender_key];
                }

                if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_birth_key)){
                    delete $tmpEnrolleeElementsVal[$del_child_birth_key];
                }
            }
        });

         $(this).removeClass('active');
         $('#childCoverageMainDiv').html('');
         $("#addChildCoverageButtonDiv").hide();
         $('#additionalChildShow').hide();
      }else{
         $(this).addClass('active');
         $("#addChildCoverageButtonDiv").show();
         addChildCoverage();
         $('#additionalChildShow').show();
         $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
      }
      $('[data-toggle="tooltip"]').tooltip();
   });

   $(document).off("click","#addChildCoverageButton");
   $(document).on("click","#addChildCoverageButton",function(e){
      addChildCoverage();
      $('[data-toggle="tooltip"]').tooltip();
   });

   $(document).off("change","#billing_profile");
   $(document).on("change","#billing_profile",function(e){
      e.stopPropagation();
      $val = $(this).val();
      if($val == "new_billing"){
            $("#payment_mode_cc").prop('checked',true).click();
            $("#new_payment_method").show();
            $("#select_payment_method").show();
      } else {
            $('#new_payment_method').hide();
            $("#select_payment_method").hide();
            $("#payment_credit_card").hide();
            $('#payment_bank_draft').show();
      }
   });

   $(document).off("change",".groupCompany");
   $(document).on('change','.groupCompany', function(e){
      e.stopPropagation();
      $("#group_company").val($(this).val());
      $(this).val($(this).val());
      $('.groupCompany').selectpicker('refresh');
   });

   addChildCoverage = function(){
      $count=$("#frmGroupMemberEnrollment .childCoverageInnerDiv").length;
      $number=$count+1;

      html = $('#childCoverageDynamicDiv').html();
      html = html.replace(/~number~/g,$number);
      $('#childCoverageMainDiv').append(html);
      $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 
   }

   $(document).off("blur change",".spouseCoverageData");
   $(document).on("blur change",".spouseCoverageData",function(e){
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

   });

   $(document).off("blur change",".childCoverageData");
   $(document).on("blur change",".childCoverageData",function(e){
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
   });

   $(document).off("click",".childVerification");
   $(document).on("click",".childVerification", function(e){
      $('[data-toggle="popover"]').popover();
      $id=$(this).attr('data-id');
      $('#childVerificationDiv'+$id).show();
      $("#childVerificationAlready"+$id).hide();
   });

   $(document).off("click",".childVerificationRemove");
   $(document).on("click",".childVerificationRemove", function(e){
      $id=$(this).attr('data-id');
      $("#childVerificationAlready"+$id).show();
      $('#childVerificationDiv'+$id).hide();
   });

   $(document).off("click",".removeChildCoverageInnerDiv");
   $(document).on("click",".removeChildCoverageInnerDiv",function(e){
      $('[data-toggle="tooltip"]').tooltip('hide');
      $id=$(this).attr('data-id');
      $removed_display_number = parseInt($("#display_number_"+$id).attr('data-display-number'));

        if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
            $del_child_fname_key = 'child_fname_'+$id;
            $del_child_gender_key = 'child_gender_'+$id;
            $del_child_birth_key = 'child_birthdate_'+$id;

            if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_fname_key)){
                delete $tmpEnrolleeElementsVal[$del_child_fname_key];
            }

            if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_gender_key)){
                delete $tmpEnrolleeElementsVal[$del_child_gender_key];
            }

            if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_birth_key)){
                delete $tmpEnrolleeElementsVal[$del_child_birth_key];
            }
        }
        
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
               
               $("#removeChildCoverageInnerDiv"+$count).attr('data-id',$newCount);
               $("#removeChildCoverageInnerDiv"+$count).attr('id',"removeChildCoverageInnerDiv"+$newCount);

               // ***** updated data when child div removed code start *****

               $("#child_fname_"+$count).attr('data-id',$newCount);
               $("#child_fname_"+$count).attr('name','tmp_child_fname['+$newCount+']');
               $("#child_fname_"+$count).removeClass('child_fname_'+$count);
               $("#child_fname_"+$count).addClass('child_fname_'+$newCount);
               $("#child_fname_"+$count).attr('id','child_fname_'+$newCount);


               $("#child_gender_male_"+$count).attr('data-id',$newCount);
               $("#child_gender_male_"+$count).attr('name','child_gender['+$newCount+']');
               $("#child_gender_male_"+$count).removeClass('child_gender_'+$count);
               $("#child_gender_male_"+$count).addClass('child_gender_'+$newCount);
               $("#child_gender_male_"+$count).attr('id','child_gender_male_'+$newCount);

               $("#child_gender_female"+$count).attr('data-id',$newCount);
               $("#child_gender_female"+$count).attr('name','child_gender['+$newCount+']');
               $("#child_gender_female"+$count).removeClass('child_gender_'+$count);
               $("#child_gender_female"+$count).addClass('child_gender_'+$newCount);
               $("#child_gender_female"+$count).attr('id','child_gender_female'+$newCount);

               $("#child_gender_male_label_"+$count).attr('for','child_gender_male_'+$newCount);
               $("#child_gender_male_label_"+$count).attr('id','child_gender_male_label_'+$newCount);

               $("#child_gender_female_label_"+$count).attr('for','child_gender_female'+$newCount);
               $("#child_gender_female_label_"+$count).attr('id','child_gender_female_label_'+$newCount);

               $("#child_birthdate_"+$count).attr('data-id',$newCount);
               $("#child_birthdate_"+$count).attr('name','tmp_child_birthdate['+$newCount+']');
               $("#child_birthdate_"+$count).removeClass('child_birthdate_'+$count);
               $("#child_birthdate_"+$count).addClass('child_birthdate_'+$newCount);
               $("#child_birthdate_"+$count).attr('id','child_birthdate_'+$newCount);

               $('#child_fname_'+$newCount).blur();
               $('#child_birthdate_'+$newCount).blur();
               $temp_val = $("input[name='child_gender["+$newCount+"]']:checked").val();
               if($temp_val != '' && $temp_val != undefined){
                    $temp_id = $("input[name='child_gender["+$newCount+"]']:checked").attr('id');
                    $("#"+$temp_id).blur();
               }

                if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
                    $del_child_fname_key = 'child_fname_'+$count;
                    $del_child_gender_key = 'child_gender_'+$count;
                    $del_child_birth_key = 'child_birthdate_'+$count;

                    if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_fname_key)){
                        delete $tmpEnrolleeElementsVal[$del_child_fname_key];
                    }

                    if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_gender_key)){
                        delete $tmpEnrolleeElementsVal[$del_child_gender_key];
                    }

                    if($tmpEnrolleeElementsVal.hasOwnProperty($del_child_birth_key)){
                        delete $tmpEnrolleeElementsVal[$del_child_birth_key];
                    }
                }

               // ***** updated data when child div removed code end *****
            }
         });
      }else{
         $("#addChildCoverage").removeClass('active');
         $('#childCoverageMainDiv').html('');
         $("#addChildCoverageButtonDiv").hide();
         $('#additionalChildShow').hide();
      }
   });

   $(document).off('click','.removeChildField');
   $(document).on('click','.removeChildField',function(){
      $number = $(this).attr('data-id');
      $('[data-toggle="tooltip"]').tooltip('hide');
      $selected = $("#child_assign_products_"+$number).multipleSelect('getSelects');
      
      if($selected.length > 0){
         $.each($selected,function($k,$v){

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
      $('#frmGroupMemberEnrollment .display_number').each(function(){
         $display_number = parseInt($(this).attr('data-display_number'));
         if($display_number > $removed_display_number){
            $display_number = $display_number - 1;
            $(this).attr('data-display_number',$display_number);
            $(this).html($display_number);
         }
      });
   });

   $(document).off("click",".removeSpouseField");
   $(document).on("click",".removeSpouseField",function(){
    $('[data-toggle="tooltip"]').tooltip('hide');
      $selected = $("#spouse_assign_products_0").multipleSelect('getSelects');
      if($selected.length > 0){
         $.each($selected,function($k,$v){
            $value = $v;
            $productPlan = $("#product_plan_"+$value).val();
            if($productPlan==5){

               $("select.child_dependent_multiple_select").each(function(){
                   $childID = $(this).attr('data-id');
                   $("#child_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                   $("#child_assign_products_"+$childID).multipleSelect('refresh');
               });

               $("select.spouse_dependent_multiple_select").each(function(){
                   $childID = $(this).attr('data-id');
                   $("#spouse_assign_products_"+$childID+" [value='"+$value+"']").prop('disabled',false);
                   $("#spouse_assign_products_"+$childID).multipleSelect('refresh');
               });   
            }
         });
      }
      $("#addSpouseField").show();
      $("#dependent_spouse_main_div").html('');
   });

   $(document).off("change",".existing_child_dependent");
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
         $('#child_fname_'+childId).val(fname).addClass('has-value');
         $('#child_lname_'+childId).val(lname).addClass('has-value');
         $('#child_email_'+childId).val(email).addClass('has-value');
         $('#child_SSN_'+childId).val(ssn).addClass('has-value');
         $('#child_birthdate_'+childId).val(birth_date).addClass('has-value');
         $("input[name='child_gender["+childId+"]'][value='"+gender+"']").prop('checked', true).trigger("click");
      }else{
         $("#child_cd_profile_id_"+childId).val(0);
         $('#child_fname_'+childId).val('').removeClass('has-value');
         $('#child_lname_'+childId).val('').removeClass('has-value');
         $('#child_email_'+childId).val('').removeClass('has-value');
         $('#child_SSN_'+childId).val('').removeClass('has-value');
         $('#child_birthdate_'+childId).val('').removeClass('has-value');
         $("input[name='child_gender["+childId+"]'][value='"+gender+"']").prop('checked', false).trigger("click");
         $.uniform.update();
      }
      var prevValue = $(this).data('previous');
      $('.existing_child_dependent').not(this).find('option[value="'+prevValue+'"]').show();
      var value = $(this).val();
      $(this).data('previous',value);
      $('.existing_child_dependent').not(this).find('option[value="'+value+'"]').hide();
       $('.existing_child_dependent').find("option[value='']").show();
       $('.existing_child_dependent').selectpicker('refresh');
   });

   $(document).off("change",".existing_spouse_dependent");
   $(document).on("change",".existing_spouse_dependent",function(){
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
         $('#spouse_fname_'+spouseId).val(fname).addClass('has-value');
         $('#spouse_lname_'+spouseId).val(lname).addClass('has-value');
         $('#spouse_email_'+spouseId).val(email).addClass('has-value');
         $('#spouse_SSN_'+spouseId).val(ssn).addClass('has-value');
         $('#spouse_birthdate_'+spouseId).val(birth_date).addClass('has-value');
         $("input[name='spouse_gender["+spouseId+"]'][value='"+gender+"']").prop('checked', true).trigger("click");
      }else{
         $("#spouse_cd_profile_id_"+spouseId).val(0);
         $('#spouse_fname_'+spouseId).val('').removeClass('has-value');
         $('#spouse_lname_'+spouseId).val('').removeClass('has-value');
         $('#spouse_email_'+spouseId).val('').removeClass('has-value');
         $('#spouse_SSN_'+spouseId).val('').removeClass('has-value');
         $('#spouse_birthdate_'+spouseId).val('').removeClass('has-value');
         $("input[name='spouse_gender["+spouseId+"]'][value='"+gender+"']").prop('checked', false).trigger("click");
         $.uniform.update();
      }
      var prevValue = $(this).data('previous');
      $('.existing_spouse_dependent').not(this).find('option[value="'+prevValue+'"]').show();
      var value = $(this).val();
      $(this).data('previous',value);
      $('.existing_spouse_dependent').not(this).find('option[value="'+value+'"]').hide();
       $('.existing_spouse_dependent').find("option[value='']").show();
       $('.existing_spouse_dependent').selectpicker('refresh');
   });

   $(document).off("click","#addChildField");
   $(document).on("click","#addChildField",function(e){
      child_field();
   });

   $(document).off("click","#addSpouseField");
   $(document).on("click","#addSpouseField",function(e){
      spouse_field();
   });
$(document).off('click', '.removeplan');
$(document).on('click','.removeplan',function(){
   var productId = $(this).data('productid');
   var product_id = $(this).attr('data-prdId');
   var bundleID = $(this).attr('data-bundleID');
   var dataPricingModel = $(this).attr('data-pricing_model');
   var isChecked = $(this).is(':checked');
   if(isChecked){
      $('#removedbundle'+productId).show();
      remove_product_array.push({name:bundleID , value: product_id});
      var electedBundle = $("#elected_bundle").val();
      if(electedBundle !=''){
         $('#removeproduct'+bundleID+'_'+product_id).hide();
         $('.selecte'+bundleID).hide();
      }
   }else{
      $('#removedbundle'+productId).hide();
      //remove_product_array.pop({name:bundleID,value: product_id});
        remove_product_array = remove_product_array.filter(object => {
            return !(object.value === product_id && object.name === bundleID)
        
        });
   }
   if(cart_products.length > 0 && $("#elected_bundle").val() != ''){
      //  removeItemByKeyValue(cart_products,'product_id',product_id);
      removeProductFromCart(product_id,dataPricingModel,'recommended')
   }

   if(cart_products.length == 0 && $("#elected_bundle").val() != ''){
      removeProductFromCart(product_id,dataPricingModel,'recommended');
      // $("#self_guiding_benefits").html('');
      $("#self_guided_menu").removeClass('active');
      $("#self_guided_menu").addClass('disabled');
      $("#recommendationsTab").removeClass('completed');
      // $(".bundle_page_total_amount").text('00.00');
      $("#elect_bundle_text"+bundleID).text('ELECT BUNDLE');
   }
   /*else{
      var tempPrice = parseFloat(0.00);
      $.each(cart_products,function(key,value){
         tempPrice += parseFloat(value['display_price']).toFixed(2);
      });
      // $(".bundle_page_total_amount").text(parseFloat(tempPrice).toFixed(2));
   }*/
   bundleTotal(bundleID);
    if(cart_products.length == 0){
        $('.electbundlegreen').click();
    }

    if($("#elected_bundle").val() == ''){
      $(".removeplan").each(function(){
         var productId = $(this).data("productid");
         $('#removeproduct'+productId).show();
      });
    }
});


   $(document).off('click', '.compare_bundle');
   $(document).on('click', '.compare_bundle', function (e) {
      e.preventDefault();
      $tempApiKey = $('#api_key').val();
      $('#api_key').val('bundleComparision');
      $tempStep = $("#step").val();
      $("#step").val(0);
      $("#ajax_loader").show();
      var bunleidCmp = $(this).data('bunleid');

      var allDesSelect = true;
      $(".compareBundle").each(function(ind,val){
         if($(this).is(':checked') === true && $(this).data('id') == bunleidCmp){
            allDesSelect = false;
         }
      });
      if(allDesSelect){
         $("span .compareBundle").click();
      }
      var params_array = $("#frmGroupMemberEnrollment").serializeArray();
      params_array.push({'name':'removeProduct','value':JSON.stringify(remove_product_array)});
      $.ajax({
         url : "<?=$HOST?>/ajax_group_member_enrollment.php",
         method: 'POST',
         data : params_array,
         dataType : 'JSON',
         success:function(res){
            $("#ajax_loader").hide();
            $("#step").val($tempStep);
            $('#api_key').val($tempApiKey);
            $.colorbox({
               html: res.html,
               iframe: false,
               width: '1310px',
               height: '90%',
               closeButton: false,
               overlayClose: false,
               onClosed: function(){
                  $electedBundle = $("#elected_bundle").val();
                  $is_elected = $("#is_elected").val();
                  if($electedBundle != '' && $is_elected == 'N'){
                     $('#is_elected').val('Y');
                     $("#electbundle"+$electedBundle).click();
                  }
                  if(allDesSelect){
                     $("span .compareBundle").click();
                  }
               }
            });
         }
      });
   });

   $(document).off('click', '.bundle_waived_coverage');
   $(document).on('click', '.bundle_waived_coverage', function (e) {
      e.preventDefault();
      $.colorbox({
         href: $(this).attr('href'),
         iframe: true, 
         width: '580px', 
         height: '600px',
      });
   });
   $(document).off('click', '.group_enroll_planinfo');
   $(document).on('click', '.group_enroll_planinfo', function (e) {
      e.preventDefault();
      $.colorbox({
         href: $(this).attr('href'),
         iframe: true, 
         width: '850px', 
         height: '600px'
      });
   });
   $(document).off('click', '.enrol_plan_depedent');
   $(document).on('click', '.enrol_plan_depedent', function (e) {
      e.preventDefault();
      $.colorbox({
         href: $(this).attr('href'),
         iframe: true, 
         width: '850px', 
         height: '300px'
      });
   });

   // $(document).off('click', '.bundle_billing_info');
   // $(document).on('click', '.bundle_billing_info', function (e) {
   //    e.preventDefault();
   //    $.colorbox({
   //       href: $(this).attr('href'),
   //       iframe: true, 
   //       width: '580px', 
   //       height: '500px'
   //    });
   // });
   $("input[name='payment_mode']").click(function(){
      var data_val = $(this).val();
      if(data_val == 'CC'){
         $('#payment_credit_card').show();
         $('#payment_bank_draft').hide();
      }else{
         $('#payment_credit_card').hide();
         $('#payment_bank_draft').show();
      }
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
        $("#ajax_loader").show();
        $bill_name = $("#bill_name").val();
        $bill_address = $("#bill_address").val();
        $bill_address2 = $("#bill_address2").val();
        $bill_city = $("#bill_city").val();
        $bill_state = $("#bill_state").val();
        $bill_zip = $("#bill_zip").val();
        
        $.ajax({
            url: '<?= $HOST ?>/ajax_api_call.php',
            data: {
            bill_name:$bill_name,
            bill_address:$bill_address,
            bill_address2:$bill_address2,
            bill_city:$bill_city,
            bill_state:$bill_state,
            bill_zip:$bill_zip,
            'api_key' : 'validateBillingAddress',
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                $("#ajax_loader").hide();
                if(res.status == 'Success'){
                    $("#display_bill_name").html($bill_name);
                    $("#display_bill_address").html($bill_address);
                    $("#display_bill_address2").html($bill_address2);
                    $("#display_bill_city").html($bill_city);
                    $("#display_bill_state").html($bill_state);
                    $("#display_bill_zip").html($bill_zip);
                    colorBoxClose();
                }else{
                    var is_error = true;
                    $.each(res.data,function(index,error){
                        index = index.replace(/[.]/g,'_');
                        if(typeof($('#error_'+index).html()) == "undefined") {
                            console.log(index + " : Element not found");
                        } else {
                            $('#error_'+index).html(error).show();
                            if(is_error) {
                                var offset = $('#error_'+index).offset();
                                var offsetTop = offset.top;
                                var totalScroll = offsetTop - 250;
                                $('body,html').animate({scrollTop:totalScroll},1200);
                                is_error = false;
                            }
                        }
                    });
                    return false;
                }
            },
        });
    });

   // Beneficiary Information Code Start
   $(document).off("click","#addPrincipalBeneficiaryField");
   $(document).on("click","#addPrincipalBeneficiaryField",function(e){
      $allow_upto = $(this).attr('data-allow-upto');
      $("#error_principal_beneficiary_general").html('');
      $count = $("#frmGroupMemberEnrollment .inner_principal_beneficiary_field").length;

      $principal_beneficiary_count = $principal_beneficiary_count + 1;
      $display_number = $count + 1;

      $number = $principal_beneficiary_count;
      if($allow_upto == '' || $display_number <= $allow_upto){
            principal_beneficiary_field($number+"_"+$display_number);
      }else{
         $("#error_principal_beneficiary_general").html('Maximum '+$allow_upto+' Principal Beneficiary Allowed');
      }
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

   $(document).off("click",".removePrincipalBeneficiaryField");
   $(document).on("click",".removePrincipalBeneficiaryField",function(){
      $("#error_principal_beneficiary_general").html('');
      $number = $(this).attr('data-id');
      
      var principal_beneficiary_id = $("#principal_beneficiary_id_"+$number).val();
      if(principal_beneficiary_id > 0 && principal_beneficiary_id != '') {
            removeItemByKeyValue(quote_principal_beneficiary,'id',principal_beneficiary_id);
      }

      $removed_display_number = parseInt($("#principal_beneficiary_number_"+$number).attr('data-display_number'));
      $("#inner_principal_beneficiary_field_"+$number).remove();

      $('#frmGroupMemberEnrollment .display_principal_beneficiary_number').each(function(){
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

   function principal_beneficiary_field($number,beneficiary_row){
      $("#principal_beneficiary_field_number").val($number);
      $("#is_principal_beneficiary").val('displayed');

      $("#ajax_loader").show();
      $.ajax({
         url: '<?=$HOST?>/ajax_get_group_principal_beneficiary_field.php',
         data: $("#frmGroupMemberEnrollment").serialize(),
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

   $(document).off("click","#addContingentBeneficiaryField");
   $(document).on("click","#addContingentBeneficiaryField",function(e){
      $("#error_contingent_beneficiary_general").html('');
      $allow_upto = $(this).attr('data-allow-upto');
      $count = $("#frmGroupMemberEnrollment .inner_contingent_beneficiary_field").length;

      $contingent_beneficiary_count = $contingent_beneficiary_count + 1;
      $display_number = $count + 1;

      $number = $contingent_beneficiary_count;
      if($allow_upto == '' || $display_number <= $allow_upto){
            contingent_beneficiary_field($number+"_"+$display_number);
      }else{
         $("#error_contingent_beneficiary_general").html('Maximum '+$allow_upto+' Contingent Beneficiary Allowed');
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

   $(document).off("click",".removeContingentBeneficiaryField");
   $(document).on("click",".removeContingentBeneficiaryField",function(){
      $("#error_contingent_beneficiary_general").html('');
      $number = $(this).attr('data-id');
      
      var contingent_beneficiary_id = $("#contingent_beneficiary_id_"+$number).val();
      if(contingent_beneficiary_id > 0 && contingent_beneficiary_id != '') {
            removeItemByKeyValue(quote_contingent_beneficiary,'id',contingent_beneficiary_id);
      }

      $removed_display_number = parseInt($("#contingent_beneficiary_number_"+$number).attr('data-display_number'));
      $("#inner_contingent_beneficiary_field_"+$number).remove();

      $('#frmGroupMemberEnrollment .display_contingent_beneficiary_number').each(function(){
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

   function contingent_beneficiary_field($number,beneficiary_row){
         $("#contingent_beneficiary_field_number").val($number);
         $("#is_contingent_beneficiary").val('displayed');
         $("#ajax_loader").show();
         $.ajax({
            url: '<?=$HOST?>/ajax_get_group_contingent_beneficiary_field.php',
            data: $("#frmGroupMemberEnrollment").serialize(),
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
   // Beneficiary Information Code Ends

   var wrapper = document.getElementById("signature-pad");
   var clearButton = wrapper.querySelector("[data-action=clear]");
   var canvas = wrapper.querySelector("canvas");
   var signaturePad = new SignaturePad(canvas, {
     backgroundColor: 'rgba(117,129,153,0.2)'
   });

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

   clearButton.addEventListener("click", function (event) {
      signaturePad.clear();
   });

   $(document).off("click",".group_enrollment_cancel_button");
   $(document).on("click",".group_enrollment_cancel_button",function(e){
      e.preventDefault();
      var pb_id = "<?=$pb_id?>";
      if(pb_id > 0){
         window.location.href = '<?=$pageBuilderLink?>';
      }else{
         window.location.href = '<?=$HOST?>';
      }
   });

   $(document).off("click",".form_submit");
   $(document).on("click",".form_submit",function(){
      var oldHtml = $(this).html();
      $(this).prop("disabled", true);
      $(this).append(" <i class='fa fa-spin fa-spinner quote_loader'></i>");

      step = $(this).attr('data-step');
      var catID = $(this).data('category-id');
      var dataFrom = $(this).data('from');
      if($("input[name='application_type']:checked").val()=='member_signature'){
            if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
               $("#hdn_signature_data").val(signaturePad.toDataURL());
            }
      }
      $("#step").val(parseFloat(step)+1);
      if(step == 3){
         $("#api_key").val("getProducts");
      }
      if(step == 4){
         $("#api_key").val("productAddToCart");
      }
      if(dataFrom !== undefined){
         $(".selfGuidingBenefitsMenu").each(function(){
            $categoryId = $(this).attr("data-category-stepli");
            checkSelfGuidingAddedProduct($categoryId,true);
         });
      }
      $(".enrollmentLeftmenuActive li.selfGuidingBenefitsMenu").hide();
      $("#frmGroupMemberEnrollment").submit();
      setTimeout(function(){
         $(this).html(oldHtml);
      },3000);
   });

   $(document).off("click",".spouse_gender_class");
   $(document).on("click",".spouse_gender_class",function(){
        $temp_radio_name = $(this).attr('name');
        $temp_radio_value = $("input[name='"+$temp_radio_name+"']:checked").val();
        if($temp_radio_value != "" && $temp_radio_value != undefined){
                $('#hidden_spouse_gender_0').val($temp_radio_value);
        }
   });

   $(document).off("click",".child_gender");
   $(document).on("click",".child_gender",function(){
        $data_id = $(this).attr('data-id');
        $temp_radio_name = $(this).attr('name');
        $temp_radio_value = $("input[name='"+$temp_radio_name+"']:checked").val();
        if($temp_radio_value != "" && $temp_radio_value != undefined){
            $('#hidden_child_gender_'+$data_id).val($temp_radio_value);
        }
   });

   function getAdditionalData(additionalInfo){
      $.each(additionalInfo,function(index,value){

         if(value.label == "gender"){
            var gender_value = $("input[name='primaryGender']:checked").val();
            $("input[name='primary_"+value.label+"'][value='"+gender_value+"']").prop('checked',true);
         }

         if($(".primary_"+value.label+"_coverage").length){

            if(value.control_type !="radio"){
               $("#primary_"+value.label).val($(".primary_"+value.label+"_coverage").val());
               if(value.label == 'zip'){
                  $("#primary_"+value.label).prop('readonly',true);
               }
            }

            if(value.control_type == "select"){
               $("#primary_"+value.label).selectpicker('refresh');
            }
            if(value.control_type == "date_mask"){
               $("#primary_"+value.label).datepicker();  
               $("#primary_"+value.label).datepicker("setDate",$(".primary_"+value.label+"_coverage").val());
               $("#primary_"+value.label).datepicker('refresh');
               if(value.label == "birthdate"){
                  $("#primary_"+value.label).prop('readonly',true);
               }
            }

            if(value.control_type == "radio"){
               var control_value = $("input[name='primary_"+value.label+"_coverage']:checked").val();
               if(control_value){
                  $("input[name='primary_"+value.label+"'][value='"+control_value+"']").prop('checked',true);
               }
            }
         }
      });
      fRefresh();
   }

   $(document).on('change','#primary_address1,#primary_address2,#primary_zip',function(){
      $("#is_valid_address").val('N');
      $("#is_address_verified").val('N');
   });

      $('#frmGroupMemberEnrollment').ajaxForm({
         beforeSend: function () {
            $("#ajax_loader").show();
         },
          dataType : 'json',
          cache: false,
          beforeSend:function(){
            $("#ajax_loader").show();
          },
          success:function(res){
            $('#ajax_loader').hide();
            $('.error').html('');
            if(res.status == 'Success'){
               $("#ajax_loader").hide();
               var step = parseFloat($("#step").val());
               var response_data = res.data;
               $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
               $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
               $(".dateClass").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});

               var is_add_product = <?=isset($is_add_product)?$is_add_product:0;?>;
               if(is_add_product == 1 && typeof(res.already_puchase_product) !== "undefined" && res.already_puchase_product!=='') {
                  already_puchase_product = res.already_puchase_product;
                  $("#already_puchase_product").val(already_puchase_product.join(','));
               }

               if(step==2){
                  $("#enrollee_question").show();
                  $('#htmlcustomquestion').html(res.html);
                  $("#coverage_detail").hide();
                  $(".bundleQuestion").uniform();

                  /*
                  $.each(selectedQuestions,function(i,val){
                     if($(this).length > 0){
                        if(i == 'select'){
                           $("select.bundleSelect").each(function(){
                              if(val[$(this).data('question')] !== undefined){
                                 $(this).val(val[$(this).data('question')]).change();
                              }
                           });
                        }else if(i == 'multiselect'){
                           $("select.multipleBundleSelect").each(function(){
                              if(val[$(this).data('question')] !== undefined){
                                 $(this).selectpicker('val',val[$(this).data('question')]).change();
                              }
                           });
                        } else if(i == 'checkbox'){
                           $(".bundleQuestionCheckbox").each(function(){
                              var $this = $(this);
                              var tempAnsVal = $(this).val();
                              if(val[$(this).data('question')] !== undefined){
                                 $.each(val[$(this).data('question')],function(ind,e){
                                    if(e == tempAnsVal){
                                       $this.prop('checked',true);
                                       $.uniform.update();
                                       $this.change();
                                    }
                                 });
                              }
                           });
                        }else if(i == 'radio'){
                           $(".bundleQuestionRadio").each(function(){
                              var $this = $(this);
                              var tempAnsVal = $(this).val();
                              if(val[$(this).data('question')] !== undefined){
                                 if(val[$(this).data('question')] == $this.val()){
                                    $this.prop('checked',true);
                                    $.uniform.update();
                                    $this.change();
                                 }
                              }
                           });
                        }
                     }
                  });
                  */
               }else if(step==3){
                  $('#htmlrecommended_benefit').html("");
                  $('#htmlrecommended_benefit').html(res.html);
                  $('#recommended_benefit').show();
                  $("#enrollee_question").hide();

                  if($("#edit_show_primary_additional").length > 0){
                     $.each($("#edit_show_primary_additional .hidden_coverage"),function(ind,val){
                        $tmpEnrolleeElementsVal[$(this).attr('data-name')] = $(this).val();
                     });
                  }

                  if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
                     $enrolleeElementsVal = Object.assign({}, $tmpEnrolleeElementsVal);
                  }
                  $("#htmlrecommended_benefit").show();
                  $("#recommended_benefit").show();
                  $("#enrolleeElementsVal").val(JSON.stringify($enrolleeElementsVal));
                  $("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
                  var bundletitleHeight = 0;
                  $('.plan-bundle-title').each(function() {
                      if(bundletitleHeight < $(this).height()){
                          bundletitleHeight = $(this).height();
                      };
                  });
                  $('.plan-bundle-title').height(bundletitleHeight);
                  $.each($('.prd_benefit_tier'),function($bundeId,$elementArrMain){
                     var product_value = $(this).find('option:selected').attr('data-price');
                     var displayvalue = $(this).find('option:selected').attr('data-display-price');
                     // var pricing_model = $(this).attr('data-pricing_model');
                     $productId = $(this).attr('data-product-id');
                     var id = $(this).attr('data-bundlId');
                     var matrixID = $(this).find('option:selected').attr('data-prd-matrix-id');
                     $("#benefit_tierName_"+id+"_"+$productId).val(parseFloat(product_value).toFixed(2));
                     $("#bundle_product_price_"+id+$productId).val(parseFloat(product_value).toFixed(2));
                     $("#bundle_display_product_price_"+id+$productId).val(parseFloat(displayvalue).toFixed(2));
                     $("#bundle_product_matrix_"+id+$productId).val(matrixID);
                     $("#bundle_added_product").val($productId);
                  });
                  if(res.takeHomePayDisplay == 'Y'){
                     take_home_breakdown = 'Y';
                     $(".takeHomePayBtnDiv").show();
                  }else{
                     $(".takeHomePayBtnDiv").hide();
                  }
                  $('[data-toggle="tooltip"]').tooltip();
                  if(res.is_main_products == false){
                     swal({
                        title: "",
                        text: "Based on current demographic and product elections, there are no products available to this member.",
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close'
                     });
                  }
                  if($("#elected_bundle").val() !== ''){
                     if(remove_product_array.length > 0){
                        $.each(remove_product_array,function(i,rp){
                           $('.removep'+rp.name+'_'+rp.value).click();
                        });
                     }
                     $("#electbundle"+$("#elected_bundle").val()).click();
                  }
               }else if(step==4){
                  if(res.is_main_products == false){
                     swal({
                        title: "",
                        text: "Based on current demographic and product elections, there are no products available to this member.",
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close'
                     });
                  }

                  $("#self_guiding_benefits").html(res.htmlData);

                  var categoryData = res.categoryData;
                  var subMenu = '';
                  $('#self_benefits_categoty').html('');

                  $.each(categoryData,function(key,value){

                     subMenu += '<li class="selfGuidingBenefitsMenu" data-category-stepli="'+value.categoryId+'" id="selfGuidingBenefitsMenu'+value.categoryId+'"><a href="#categoryDiv'+value.categoryId+'" class="selfGuidingBenefitsHref" id="category'+value.categoryId+'" role="tab" data-toggle="tab" aria-controls="categoryDiv'+value.categoryId+'" data-category-id="'+value.categoryId+'" data-step="4" data-category-step="'+value.categoryId+'">'+value.categoryName+'</a></li>';
                  });

                  $('#self_benefits_categoty').append(subMenu);
                  var categoryKey = $("#categoryKey").val();
                  $('#categoryDiv' + categoryKey).addClass("active pn");

                  $('.benefittier_type').each(function(i) {
                     var product_id = $(this).attr("data-product-id");
                     var price = $('#product_benefit_tier_' + product_id).find('option:selected').attr('data-display-price');
                     if(price == undefined){
                        price = 0;
                     }
                     var extraHtml = '$';
                     if($(".pricing_model_"+product_id).first().val() !== 'FixedPrice'){
                        extraHtml = '<sub>Starting at </sub>$';
                     }
                     $("#product_price_label_" + product_id).html(extraHtml+parseFloat(price).toFixed(2) + '<sub class="fs12" style="bottom:0px;">/ pay period</sub>');
                  });

                  $("#self_guiding_benefits").show();
                  $("#htmlrecommended_benefit").hide();

                  if(res.takeHomePayDisplay == 'Y'){
                     take_home_breakdown = 'Y';
                     $(".takeHomePayBtnDiv").show();
                  }else{
                     $(".takeHomePayBtnDiv").hide();
                  }

                  if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
                     $enrolleeElementsVal = Object.assign({}, $tmpEnrolleeElementsVal);
                  }

                  $("#enrolleeElementsVal").val(JSON.stringify($enrolleeElementsVal));
                  $('[data-toggle="tooltip"]').tooltip();
               }else if(step==5){
                  // if($("#api_key").val() == "productAddToCart"){
                  // }else
                  {
                     if($primary_product_change){
                        primary_member_field();
                     }

                     if(res.data.addressStatus == 'success'){
                        $('#primary_city').val(res.data.primaryCity).prop('readonly',true);
                        $('#primary_state').val(res.data.primaryState).prop('readonly',true);
                     }

                     $principal_beneficiary_count = 0;
                     $contingent_beneficiary_count = 0;

                     $("#addSpouseField").hide();
                     $("#addChildField").hide();

                     if(typeof($("#primary_salary")) !==undefined && $("#primary_salary").length > 0){
                        $("#primary_salary").val($("#gap_payroll_type_salary").val());
                     }
                     if(typeof($("#primary_pay_frequency")) !==undefined && $("#primary_pay_frequency").length > 0){
                        $("#primary_pay_frequency").val($("#gap_pay_frequency").val()).change();
                     }

                     // $("#beneficiary_information_div").hide();
                     $("#principal_beneficiary_div").hide();
                     $("#contingent_beneficiary_div").hide();
                     $("#principal_beneficiary_field_div").html('');
                     $("#contingent_beneficiary_field_div").html('');
                     $("#addPrincipalBeneficiaryField").attr('data-allow-upto','');
                     $("#addContingentBeneficiaryField").attr('data-allow-upto','');

                     $("#is_principal_beneficiary").val('');
                     $("#is_contingent_beneficiary").val('');

                     if(res.data.status == "success"){
                        if(res.data.isSpouse){
                           $("#dependent_field_div").show();
                           $("#spouse_products_list").val(res.data.isSpouse);
                           if($('div.inner_spouse_field').length == 0){
                                $("#addSpouseField").show(); 
                           }
                           
                            if($spouse_product_change){
                               if(spouse_dep.length > 0){
                                  spouse_field(spouse_dep[0].cd_profile_id,spouse_dep[0].order_id);
                               }else{
                                  spouse_field();
                               }
                            }
                            $('[data-toggle="tooltip"]').tooltip();
                        }

                        if(res.data.isChild){
                           $("#dependent_field_div").show();
                           $("#child_products_list").val(res.data.isChild);
                           $("#addChildField").show();
                            if($spouse_product_change){
                               if(child_dep.length > 0){
                                  for( var i = 0; i < child_dep.length; i++){
                                     child_field(child_dep[i].cd_profile_id,child_dep[0].order_id);
                                  }
                               }else{
                                  if(res.data.childCount > 0){
                                     for( var i=1 ; i <= res.data.childCount; i++){
                                        child_field();
                                     }
                                  }
                               }
                            }
                            $('[data-toggle="tooltip"]').tooltip();
                        }
                        $spouse_product_change = false;
                     }

                  if(res.data.principalBeneficiary){
                     $("#beneficiary_information_div").show();
                     $("#principal_beneficiary_div").show();

                     if(res.data.principalBeneficiaryAllowUpto3){
                        $("#addPrincipalBeneficiaryField").attr('data-allow-upto',3);
                     }

                     $("#is_principal_beneficiary").val('not_displayed');

                     if(quote_principal_beneficiary.length > 0){
                        $allow_upto = $("#addPrincipalBeneficiaryField").attr('data-allow-upto');
                        $count = $("#frmGroupMemberEnrollment .inner_principal_beneficiary_field").length;
                        
                        $.each(quote_principal_beneficiary,function(index,beneficiary_row){
                           $principal_beneficiary_count = $principal_beneficiary_count + 1;
                           $display_number = $count + 1;
                           $number = $principal_beneficiary_count;
                           if($allow_upto = '' || $display_number <= $allow_upto){
                              principal_beneficiary_field($number+"_"+$display_number,beneficiary_row);
                           }
                           $count++;
                        });
                     }
                  }

                  if(res.data.contingentBeneficiary){
                     $("#beneficiary_information_div").show();
                     $("#contingent_beneficiary_div").show();

                     if(res.data.contingentBeneficiaryAllowUpto3){
                        $("#addContingentBeneficiaryField").attr('data-allow-upto',3);
                     }

                     if(quote_contingent_beneficiary.length > 0){
                        $allow_upto = $("#addContingentBeneficiaryField").attr('data-allow-upto');
                        $count = $("#frmGroupMemberEnrollment .inner_contingent_beneficiary_field").length;

                        $.each(quote_contingent_beneficiary,function(index,beneficiary_row){
                           $contingent_beneficiary_count = $contingent_beneficiary_count + 1;
                           $display_number = $count + 1;

                           $number = $contingent_beneficiary_count;
                           if($allow_upto == '' || $display_number <= $allow_upto){
                               contingent_beneficiary_field($number+"_"+$display_number,beneficiary_row);
                           }
                           $count++;
                        });
                     }
                  }

                  // $(".primary_multiple_select").multipleSelect({});
                  $("#application").show();
                  $("#self_guiding_benefits").hide();
                  }
               }else if(step==6){
                  $verified_address = true;
                  $("#dependent_array").val('');
                  if(response_data.dependent_array){
                     $("#dependent_array").val(response_data.dependent_array);
                  }
                  var addressResponse = res.data;
                  if(addressResponse.addressResponseStatus == "success"){
                     $(".suggested_address_box").uniform();
                     $(".suggestedAddressEnteredName").html($("#que_primary_fname").val()+" "+$("#que_primary_lname").val());
                     $(".suggestedAddressEntered").html(addressResponse.enteredAddress);
                     $(".suggestedAddressAPI").html(addressResponse.suggestedAddress);
                     $("#is_valid_address").val('Y');
                     $.colorbox({
                        inline:true,
                        href:'#suggestedAddressPopup',
                        height:'500px',
                        width:'650px',
                        escKey:false, 
                        overlayClose:false,
                        onClosed:function(){
                           $suggestedAddressRadio = $("input[name='suggestedAddressRadio']:checked"). val();

                           if($suggestedAddressRadio=="Suggested"){
                              $("#is_address_verified").val('Y');
                              $("#primary_address1").val(addressResponse.address).addClass('has-value');
                              $("#primary_address2").val(addressResponse.address2).addClass('has-value');
                           }else{
                              $("#is_address_verified").val('N');
                           }
                         },
                     });
                  } else {
                     var summary_section = response_data.summary_section;
                     var waived_coverage_section = response_data.waived_coverage_section;
                     var plan_breakdown_section = response_data.plan_breakdown_section;
                     var verification_option_section = response_data.verification_option_section;
                     var esign_section = response_data.esign_section;
                     var is_gap_or_hip_plus_product_res = response_data.is_gap_or_hip_plus_product;
                     var take_home_pay_breakdown_section = '';
                     if(typeof(response_data.take_home_pay_breakdown_section.original.data.calculation_data) !== 'undefined'){
                        take_home_pay_breakdown_section = response_data.take_home_pay_breakdown_section.original.data.calculation_data;
                        if(cart_products.length > 0){
                           show_take_home_pay_breakdown_section(take_home_pay_breakdown_section);
                        }
                     }
                     if(typeof(is_gap_or_hip_plus_product_res) !== undefined && is_gap_or_hip_plus_product_res === true){
                        $("#is_gap_or_hip_plus_product").val('Y');
                        if($("#is_direct_deposit_account").val() == 'N' && $("#is_gap_or_hip_plus_product").val() == 'Y'){
                           $('#payment_bank_deposit').show();
                        }
                     }else{
                        $('#payment_bank_deposit').hide();
                     }
                     show_enrollment_summary(summary_section);
                     show_waived_coverage(waived_coverage_section);
                     $("#enrollmentPaymentDiv").hide();
                     if(cart_products.length > 0){
                        show_plan_breakdown(plan_breakdown_section);
                     }
                     show_verification_option_section(verification_option_section);
                     show_esign_section(esign_section);
                     update_enrollment_progress();
                  }
               } else if(step==7){
                  if (response_data.status == 'payment_fail') {
                     $("#lead_quote_detail_id").val(response_data.lead_quote_detail_id);
                     $("#customer_id").val(response_data.customer_id);
                     if($("#billing_profile").val() == "new_billing" && typeof(response_data.billing_profile_id) !== "undefined") {
                           $("#last_billing_profile_id").val(response_data.billing_profile_id);
                     }
                     colorBoxClose();
                     swal({
                           title: "Payment Failed",
                           html: response_data.payment_error,
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
                  } else if (response_data.status == 'account_approved') {
                     if(typeof(response_data.sent_to_member) !== "undefined" && response_data.sent_to_member != "" && typeof(response_data.lead_quote_detail_id) !== "undefined" && response_data.lead_quote_detail_id != "") {
                           $("#lead_quote_detail_id").val(response_data.lead_quote_detail_id);

                           if(typeof(response_data.customer_id) !== "undefined") {
                              $("#customer_id").val(response_data.customer_id);
                           }
                           if(typeof(response_data.lead_id) !== "undefined") {
                              $("#lead_id").val(response_data.lead_id);
                           }
                           if(typeof(response_data.order_id) !== "undefined") {
                              $("#order_id").val(response_data.order_id);
                           }

                           //Display Enrollment Live Status
                           if(response_data.message_delivered_status == "success") {
                              exit_by_system = true;
                              var is_add_product = 'N';
                              if(typeof(response_data.is_add_product) !== "undefined") {
                                 if(response_data.is_add_product == 1){
                                       is_add_product = 'Y';
                                 }
                              }
                           } else {
                              setNotifyError("Verification delivery failed");
                           }
                     } else {
                           if(typeof(response_data.md5_customer_id) !== "undefined") {
                              $("#md5_customer_id").val(response_data.md5_customer_id);
                           }
                           if(typeof(response_data.md5_order_id) !== "undefined") {
                              $("#md5_order_id").val(response_data.md5_order_id);
                           }
                           if(typeof(response_data.payment_type) !== "undefined") {
                              $("#payment_type").val(response_data.payment_type);
                           }
                           // if(response_data.payment_type === 'list_bill' || response_data.payment_type === 'tpa' || response_data.only_waive_products == true){
                           //    var $incr = "?memberId="+response_data.customer_id;
                           // }
                           var $pageUserName = "<?=$pageUserName?>";
                           var $incr = "?memberId="+response_data.md5_customer_id+"&subscription_ids="+response_data.subscription_ids+"&user_name="+$pageUserName;
                           exit_by_system = true;
                           window.location="<?=$HOST?>/group_member_enrollment_plan.php"+$incr;
                     }
                  } else if(typeof(response_data.sub_status) !== "undefined" && response_data.sub_status == 'verification'){
                     $(".lead_dislay_info").text(response_data.lead_display_id + " " + response_data.lead_name);
                     if(!$verification_running){
                        vs_data = response_data;
                        get_enrollment_verification_status(response_data.sent_via,response_data.sent_to_member,response_data.lead_quote_detail_id,response_data.enrollmentLocation,response_data.is_add_product);
                     }
                     update_enrollment_progress();
                     $(".enrollmentLeftmenuActive li").addClass("disabled");
                  }
               }
               if(step!=7 && step!=6){
                  update_enrollment_progress();
               }

               if(step == 3 || step ==4){
                  callCalculateTakeHomePay();
               }
               if(step==4){
                  if($("#categoryaddOnCategory").length > 0){
                     if(res.is_add_product == ''){
                        $("#categoryaddOnCategory").parent('.selfGuidingBenefitsMenu').hide();
                     }
                     if(res.addOnDisplay == 'false'){
                        $("#categoryaddOnCategory").parent('.selfGuidingBenefitsMenu').hide(); 
                     }
                  }
               }
               codeExecuted = false;
               if(cart_products.length > 0 && $("#api_key").val() == 'getProducts' && codeExecuted !== true){
                  $("#fromStep").val('selfGuiding');
                  // console.log(cart_products);
                  $("#addingBundleProductToSelfGuiding").val(1);
                  $.each(cart_products,function(key,value){
                     $("#ajax_loader").show();
                     var extraHtml = '$';
                     if($(".pricing_model_"+value['product_id']).first().val() !== 'FixedPrice'){
                        extraHtml = '<sub>Starting at </sub>$';
                     }
                     $("#product_price_label_" + value['product_id']).html(extraHtml+parseFloat(value['display_price']).toFixed(2) + '<sub class="fs12" style="bottom:0px;">/ pay period</sub>');
                     $("#product_benefit_tier_"+value['product_id']).val(value['prd_plan_type_id']);
                     $("#product_benefit_tier_"+value['product_id']).selectpicker('setStyle', 'btn-select');
                     addToCart(value['product_id'],value['price'],value['matrix_id'],value['pricing_model'],value['display_price']);
                  });
                  codeExecuted = true;
               }

               if(codeExecuted == true){
                  setTimeout(function(){
                     $("#addingBundleProductToSelfGuiding").val(0);
                  },1000);
               }

               common_select();
               // setNotifySuccess("save Succesfully");
            }else if(res.status == 'fail'){
               // $verified_address = false;
               if(typeof(res.existing_email) !== "undefined"){
                  if(res.existing_status == "bob_lead") {
                     $("#bob_lead_enrollment_url").attr('data-href',res.enrollment_url);
                  }
                  if(res.existing_status == "bob_member") {
                     $("#bob_member_enrollment_url").attr('data-href',res.enrollment_url);
                  }
                  $.colorbox({inline: true, href: "#" + res.existing_status, width: '530px', height: '250px'});
               }else{
                  var is_error = true;
                  $.each(res.errors,function(index,error){
                     $('#error_'+index).html(error).show();
                     if(is_error) {
                        var offset = $('#error_'+index).offset();
                        var offsetTop = offset.top;
                        var totalScroll = offsetTop - 250;
                        $('body,html').animate({scrollTop:totalScroll},1200);
                        is_error = false;
                     }
                  });
               }
            }else if(res.status == 'Error'){
               var is_error = true;
               if($("#step").val() == 2){
                  $("#edit_enroll").click();
                  $("#edit_primary").click();
                  // $("#edit_show_enroll").show();
                  // $("#edit_show_primary").show();
                  // $("#edit_show_primary_additional").show();
                  // $("#edit_hide_enroll").hide();
                  // $("#edit_hide_primary").hide();
                  // $("#edit_hide_primary_additional").hide();
                  // $('#edit_primary').hide();
                  // $('#edit_enroll').hide();
               }

               if($("#step").val() == 5 || $("#step").val() == 6){
                    if(!$.isEmptyObject(res.data)){
                        $.each($('#coverage_detail :input'),function(){
                            $temp_cove_name = $(this).attr('name');
                            if($temp_cove_name != '' && $temp_cove_name != undefined){
                                if(!$.isEmptyObject(res.data[$temp_cove_name])){
                                    $(".enrollmentDiv").hide();
                                    $("#step").val(1);
                                    $("#coverage_detail").show();
                                    return false;
                                }
                            }
                        });
                    }
               }
               $.each(res.data,function(index,error){
                  index = index.replace(/[.]/g,'_');
                  if(typeof($('#error_'+index).html()) == "undefined") {
                     console.log(index + " : Element not found");
                  } else {
                     $('#error_'+index).html(error).show();
                     if(is_error) {
                        var offset = $('#error_'+index).offset();
                        var offsetTop = offset.top;
                        var totalScroll = offsetTop - 250;
                        $('body,html').animate({scrollTop:totalScroll},1200);
                        is_error = false;
                     }
                  }
               });
            }
            common_select();
            fRefresh();
            setTimeout(function(){
               $(".quote_loader").remove();
               $('.form_submit').removeAttr("disabled");
            },3000);
         }
      });

   primary_member_field = function(){
      $("#ajax_loader").show();
      if(!$.isEmptyObject($tmpEnrolleeElementsVal)){
         $enrolleeElementsVal = Object.assign({}, $tmpEnrolleeElementsVal);
      }
      $("#enrolleeElementsVal").val(JSON.stringify($enrolleeElementsVal));
      var $tempApiKey = $('#api_key').val();

      $("#api_key").val('getPrimaryMemberField');
      $.ajax({
         url: '<?=$HOST?>/ajax_group_member_enrollment.php',
         data: $("#frmGroupMemberEnrollment").serialize(),
         type: 'POST',
         async: false,
         dataType: 'json',
         success: function (res) {
            $("#api_key").val($tempApiKey);
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
                     $('#primary_salary').val(0.00);
                     $('#primary_salary').addClass('has-value');
                  }
            }
            checkEmail();
         }
      });
   }

   function spouse_field(tmp_cd_profile_id=0,tmp_order_id=0){

      $("#ajax_loader").show();

      var spouse_param_arr = $("#frmGroupMemberEnrollment").serializeArray();
      spouse_param_arr.push({name:'coverage_spouse_verification_doc',value:$("#coverage_spouse_verification_doc").val()});

      $.ajax({
         url: '<?=$HOST?>/ajax_get_group_enrollmemt_spouse_field.php?cd_profile_id='+tmp_cd_profile_id+"&order_id="+tmp_order_id,
         data: spouse_param_arr,
         type: 'POST',
         dataType: 'json',
         async:false,
         success: function(res){
            $("#ajax_loader").hide();
            if(res.status=="success"){
               $("#addSpouseField").hide();
               $("#dependent_spouse_main_div").append(res.html);
               $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
               $(".dateClass").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
               $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
               $(".spouse_multiple_select").multipleSelect({
                  width:'100%',
               });
               $(".zipcode_select").inputmask({"mask": "99999",'showMaskOnHover': false});
               $("#spouse_assign_products_"+res.number).multipleSelect({
                  width:'100%',
                  selectAll:false,
               });
               $(".spouse_select_"+res.number).addClass('form-control');
               $(".spouse_select_"+res.number).selectpicker({
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
               checkEmail();
               fRefresh();
               $('[data-toggle="popover"]').popover();
               $('[data-toggle="tooltip"]').tooltip();
            }
         }
      });


   }

   function child_field(tmp_cd_profile_id=0,tmp_order_id=0){

      $count = $("#frmGroupMemberEnrollment .inner_child_field").length;
      $dependent_count = $dependent_count + 1;
      $display_number = $count + 1;
      $number = $dependent_count+"_"+$display_number;
      $("#dependent_field_number").val($number);
      $("#ajax_loader").show();

      var child_param_arr = $("#frmGroupMemberEnrollment").serializeArray();
      if($('#frmGroupMemberEnrollment .coverage_child_verification_doc').length > 0){
         $.each($('#frmGroupMemberEnrollment .coverage_child_verification_doc'),function(ind,val){
            child_param_arr.push({name:'coverage_child_verification_doc['+ind+']',value:$(this).val()});
         })
      }
      $.ajax({
         url: '<?=$HOST?>/ajax_get_group_enrollmemt_child_field.php?cd_profile_id='+tmp_cd_profile_id+"&order_id="+tmp_order_id,
         data: child_param_arr,
         type: 'POST',
         dataType: 'json',
         async:false,
         success: function(res){
            $('#ajax_loader').hide();
            if(res.status=="success"){
               $("#dependent_child_main_div").append(res.html);
               $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
               $(".dateClass").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
               $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
               $(".child_multiple_select_"+res.number).multipleSelect({
                  width:'100%',
               });
               $(".zipcode_select").inputmask({"mask": "99999",'showMaskOnHover': false});
               $("#child_assign_products_"+res.number).multipleSelect({
                  width:'100%',
                  selectAll:false,
               });
               $(".child_select_"+res.number).addClass('form-control');
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
               checkEmail();
               fRefresh();
               $('[data-toggle="tooltip"]').tooltip();
            }
         }
      });


   }

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
               $dep_relation = $v['dependentRelationInput'];
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
      $("#applicationTab").find("a").trigger("click");
   });

   $(document).off("input","#primary_fname_coverage");
   $(document).on("input","#primary_fname_coverage",function(){
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

   $(document).off('click', '.enrollmentLeftmenuItem');
   $(document).on('click', '.enrollmentLeftmenuItem', function() {
      if(!$(this).hasClass('disabled')){
         $id= $(this).attr('id');
         $(".enrollmentDiv").hide();
         if($id == 'coverageDetailTab' || $id == 'coverageDetailTabBack'){
            $("#coverage_detail").show();
         }
         if($id == 'questionTab' || $id == 'questionTabBack'){
            $("#enrollee_question").show();
         }
         if($id == 'recommendationsTab' || $id == 'recommendationsTabBack'){
            $("#htmlrecommended_benefit").show();
            $("#recommended_benefit").show();
         }
         if($id == 'self_guided_menu' || $id == 'self_guided_menu_back'){
            $("#self_guiding_benefits").show();
         }
         if($id == 'applicationTab' || $id == 'applicationTabBack'){
            $("#application").show();
         }
         if($id == 'paymentTab'){
            $("#payment_summary").show();
         }
         let dataStep = $(this).children('a').attr("data-step");
         $("#step").val(parseInt(dataStep));
         update_enrollment_progress();
      }
   });

   $(document).off("change","#relationship_of_group");
   $(document).on("change","#relationship_of_group",function(e){
      e.stopPropagation();
      $val = $(this).val();
      $("#hdn_relationship_to_group").val($val);
   }); 
   $(document).off("change","#enrollee_class");
   $(document).on("change","#enrollee_class",function(e){
      e.stopPropagation();
      $val = $(this).val();
      $("#hdn_enrolle_class").val($val);
      $('#gap_pay_frequency').val($(this).find('option:selected').attr('data-pay_period'));
   }); 

   $(document).off("click","#edit_email");
   $(document).on("click","#edit_email",function(){
         $("#step").val(1);
         $(".enrollmentLeftmenuActive li").removeClass("completed active").addClass("disabled");
         update_enrollment_progress();
   });

    $(document).off("click","#manual_refresh");
    $(document).on("click","#manual_refresh",function(e){
      e.preventDefault();
      add_refresh_request = true;
        get_enrollment_verification_status(vs_data.sent_via,vs_data.sent_to_member,vs_data.lead_quote_detail_id,vs_data.enrollmentLocation,vs_data.is_add_product,true);
    });

   function update_enrollment_progress()
   {
      $(".enrollmentDiv").hide();
      var current_step = $("#step").val();
      var prev_step = current_step - 1;
      $("#fromStep").val('');
      /*--- Current Step ----*/
      $(".enrollmentLeftmenuItem").removeClass("active");
      $(".enrollmentLeftmenuActive [data-stepli='"+ current_step +"']").addClass('active');
      $(".enrollmentLeftmenuActive li.active").removeClass("disabled");
      /*--- Previous Step ----*/
      $(".enrollmentLeftmenuActive [data-stepli='"+prev_step+"']").addClass('completed');

      $(".enrollmentLeftmenuActive li.selfGuidingBenefitsMenu").hide();

      if(current_step == 1) {
         //Coverage Details
         $("#api_key").val("enrollmentSubmit");
         $("#coverage_detail").show();
         $(".progressbarPercentage").width("0%");
         $(".progressbarPercentagetext").text("0%");
      } else if(current_step == 2) {
         //Questions
         $("#api_key").val("enrollmentSubmit");
         $("#enrollee_question").show();
         $(".progressbarPercentage").width("15%");
         $(".progressbarPercentagetext").text("15%");

      } else if(current_step == 3) {
         //Recommendations
         $("#htmlrecommended_benefit").show();
         $("#recommended_benefit").show();
         $("#fromStep").val('bundleRecommandation');
         $(".progressbarPercentage").width("36%");
         $(".progressbarPercentagetext").text("36%");

      } else if(current_step == 4) {
         //Self Guiding Benefits
         $("#self_guiding_benefits").show();
         $("#fromStep").val('selfGuiding');
         var category_step = $("#category_step").val();
         $(".selfGuidingBenefitsMenu").removeClass('active');
         $(".enrollmentLeftmenuActive [data-category-stepli='" + category_step + "']").addClass('active current');

         $(".enrollmentLeftmenuActive li.selfGuidingBenefitsMenu").show();
         $(".progressbarPercentage").width("50%");
         $(".progressbarPercentagetext").text("50%");

         $("#paymentTab").removeClass('active');
         $("#paymentTab").removeClass('completed');
         $("#paymentTab").addClass('disabled');

      } else if(current_step == 5) {
         //Application
         $("#application").show();
         $(".enrollmentLeftmenuActive li.selfGuidingBenefitsMenu").show();
         $(".progressbarPercentage").width("64%");
         $(".progressbarPercentagetext").text("64%");
         $("#paymentTab").removeClass('active');
         $("#paymentTab").removeClass('completed');
         $("#paymentTab").addClass('disabled');
      
      } else if(current_step == 6) {
         //Payment
         if($verified_address){
            updateBillingAddress();
         }
         $("#payment_summary").show();
         $(".enrollmentLeftmenuActive li.selfGuidingBenefitsMenu").show();
         $(".progressbarPercentage").width("83%");
         $(".progressbarPercentagetext").text("83%");
      } else if(current_step == 7) {
         //Verfication Status
         $("#verification_detail").show();
         $(".enrollmentLeftmenuActive li.selfGuidingBenefitsMenu").show();
         $(".progressbarPercentage").width("100%");
         $(".progressbarPercentagetext").text("100%");
      }
   }

   function removeNumberSpecialChar(str) {
      str.val(str.val().replace(/[^a-zA-Z-' ]/g, ""));
   }

   function is_dependent_required(){
      $dependent_count = 0;
      $principal_beneficiary_count = 0;
      $contingent_beneficiary_count = 0;  
   }
   updateBillingAddress = function(){
      $bill_name = $("#que_primary_fname").val() + " " + $("#que_primary_lname").val();
      $bill_address = $("#que_primary_address1").val();
      $bill_address2 = $("#que_primary_address2").val();
      $bill_city = $("#primary_city").val();
      $bill_state = $("#primary_state").val();
      $bill_zip = $("#primary_zip").val();
      
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
      $verified_address = false;
   }

   setPostDate = function(){
      var effective_dates = [];
      var cnt = 0;
      $("#frmGroupMemberEnrollment .coverage_date_input").each(function(index,element){
        effective_dates[cnt] = $(this).val();
        $coverage_product_id = $(this).attr('data-product-id');
        $("#td_terms_products_"+$coverage_product_id).html($(this).val());
        cnt++;
      });
      if(effective_dates.length > 0) {
        var lowest_effective_date = effective_dates.reduce(function (a, b) { return a < b ? a : b; }); 
        
        var next_billing_date = moment(lowest_effective_date).add(1, 'M').add(-5,'d').format("MM/DD/YYYY");
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
            
   waived_checkbox = function($category_id){
       if($("#waive_checkbox_"+$category_id).is(":checked")){
           /*$(".waive_products_"+$category_id).each(function(){
               $product_id = $(this).val();
               removeProductFromCart($product_id,'Default');
               $("#product_body_div_"+$product_id).addClass('waived_body');
               $("#waived_content_"+$product_id).show();
           });*/
           var waived_reason = $('[name="waive_coverage_reason['+$category_id+']"]:checked').val();
           $(".label_waive_coverage_reason_" + $category_id).html(waived_reason);
       } else {
           /*$(".waive_products_"+$category_id).each(function(){
               $product_id = $(this).val();
               $("#product_body_div_"+$product_id).removeClass('waived_body');
               $("#waived_content_"+$product_id).hide();
           })*/
       }
       
       /*$(".waive_checkbox").not('.js-switch').uniform();
       $(".waive_coverage_reason").not('.js-switch').uniform();
       $primary_product_change = true;*/
   }

    function show_verification_option_section(res){
      $("#verification_option_div").html('');
      $("#verification_option_html_div").hide();

      $("#member_verification_div").show();
      $("#verification_option_div").html(res.verification_option_html);

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
   
      if(enrollmentLocation == "self_enrollment_site") {
         $("#application_type_member_signature").prop('checked',true).trigger('click');
         $("#application_type_member_signature").trigger('change');
         $("#verification_option_title").hide();
         $("#verification_option_div").hide();
      }
      updateBillingAddress();
    }

   function show_esign_section(esign_section){
      $("#terms_condition_prd_tbody").html('');
      $.each(esign_section, function ($k, $v) {
         $product_id=$v['product_id'];
         $product_id_md5=$v['product_id_md5'];
         $product_category=$v['product_category'];
         $product_name=$v['product_name'];
         $coverage_date = $("#coverage_date_" + $product_id).val();

         esign_section_html = $('#terms_condition_prd_dynamic_div').html();
         esign_section_html = esign_section_html.replace(/~product_id~/g, $product_id);
         esign_section_html = esign_section_html.replace(/~product_id_md5~/g, $product_id_md5);
         esign_section_html = esign_section_html.split('~product_name~').join($product_name);
         esign_section_html = esign_section_html.split('~coverage_date~').join($coverage_date);
         esign_section_html = esign_section_html.split('~product_category~').join($product_category);
         $('#terms_condition_prd_tbody').append(esign_section_html);
      });

      setTimeout(function(){
         $("#product_terms_check_all").uniform();
         $(".product_terms_check").uniform();
      },1000);
   }

   function show_plan_breakdown(res){

      $("#billing_display").val(res.billing_display);
      // if(res.billing_display == 'N'){
      //    $("#enrollmentPaymentDiv").hide();
      // }else if(res.billing_display == 'Y'){
         $("#enrollmentPaymentDiv").show();
      // }

      $calculateSubTotal = parseFloat(res.display_sub_total).toFixed(2);
      $calculateGroupPriceSubTotal = parseFloat(res.display_group_price_sub_total).toFixed(2);
      $calculateTotal = parseFloat(res.display_order_total).toFixed(2);
      $service_fee_total = parseFloat(res.service_fee_total).toFixed(2);

      $healthy_step_fees_total = parseFloat(res.healthy_step_fees_total).toFixed(2);
      $healthy_step_fees_name = res.healthy_step_fees_name;

      // Start Tax-calculator on enrollment page
      $with_triada_gross_income = $("#with_triada_gross_income").html();
      $with_estimated_payroll_taxes_total = $("#with_estimated_payroll_taxes_total").html();
      $with_gap_federal_taxes = $("#with_gap_federal_taxes").html();
      $with_gap_state_taxes = $("#with_gap_state_taxes").html();
      $with_gap_fica = $("#with_gap_fica").html();
      $with_gap_medicare = $("#with_gap_medicare").html();
      $with_gap_local_taxes = $("#with_gap_premium").html();
      $with_with_claim_amount = $("#with_gap_claim_payment").html();
      $("#grp_take_home_gross_income").html($with_triada_gross_income);
      $("#grp_take_home_payroll_taxes").html($with_estimated_payroll_taxes_total);
      $("#grp_with_gap_federal_taxes").html($with_gap_federal_taxes);
      $("#grp_with_gap_state_taxes").html($with_gap_state_taxes);
      $("#grp_with_gap_fica").html($with_gap_fica); 
      $("#grp_with_gap_medicare").html($with_gap_medicare);
      $("#grp_with_gap_local_taxes").html($with_gap_local_taxes);
      $("#claim_payment").html($with_with_claim_amount);
      // End Tax-calculator on enrollment page

      $summary_monthly_payment = (parseFloat($calculateSubTotal)+parseFloat($service_fee_total)).toFixed(2);

      $("#summary_group_rate_sub_total").html($calculateGroupPriceSubTotal);
      $("#summary_sub_total").html((parseFloat($calculateSubTotal)).toFixed(2));

      $("#summary_healthy_step_name").html($healthy_step_fees_name);
      $("#summary_healthy_step_total").html($healthy_step_fees_total);

      $("#summary_service_fee_total").html($service_fee_total);

      $("#summary_group_rate_total").html($calculateGroupPriceSubTotal);
      $("#summary_total").html((parseFloat($calculateTotal)+parseFloat($calculateGroupPriceSubTotal)).toFixed(2));

   }

   function show_waived_coverage(waived_coverage){
      $("#waived_coverage_section").hide();
      if(Object.keys(waived_coverage).length > 0){
         $("#waived_coverage_section").show();
         $("#waived_coverage_tbody").html('');
         $.each(waived_coverage, function ($k, $v) {
            $category_id=$v['category_id'];
            $category_title=$v['category_title'];
            $category_detail=$v['category_detail'];

            waived_coverage_html = $('#waived_coverage_dynamic_div').html();
            waived_coverage_html = waived_coverage_html.replace(/~category_id~/g, $category_id);
            waived_coverage_html = waived_coverage_html.split('~category_title~').join($category_title);
            $('#waived_coverage_tbody').append(waived_coverage_html);
         });

         $('.waive_coverage_reason').each(function (ind,value){
            $this = $(this);
            $.each($waive_coverage,function(i,cat){
               if($this.data('category_id') == cat.category_id && $this.val() == cat.reason){
                  $this.attr('checked',true);
                  $(".label_waive_coverage_reason_"+$this.data('category_id')).html($this.val());
                  if(cat.reason == 'Other'){
                     $("#waive_coverage_other_reason_"+$this.data('category_id')).val(cat.other_reason);
                  }
               }
            });
         });
      }
   }

   function show_enrollment_summary(summaryList){
      $("#enrollment_summary_details_div").html('');
      $("#post_date_payment_div").hide();
      $verification_option_product_change = false; 
      $.each(summaryList, function ($k, $v) {
         $product_id=$v['product_id'];
         $product_id_md5=$v['product_id_md5'];
         $product_code=$v['product_code'];
         $product_name=$v['product_name'];
         $carrier_name=$v['carrier_name'];
         $primary_member_name=$v['primary_member_name'];
         $plan_name=$v['plan_name'];
         $product_total=$v['product_total'];
         $display_product_total=parseFloat($v['display_product_total']).toFixed(2);
         $dependent_count=$v['dependent_count'];
         $coverage_date=$v['coverage_date'];
         $coverage_end_date=$v['coverage_end_date'];
         $startView=$v['startView'];
         $minViewMode=$v['minViewMode'];
         $coverage_period=$v['coverage_period'];
         $datesDisabled = $v['datesDisabled'];

         summary_html = $('#enrollment_summary_details_dynamic_div').html();
         summary_html = summary_html.replace(/~product_id~/g, $product_id);
         summary_html = summary_html.replace(/~product_code~/g, $product_code);
         summary_html = summary_html.split('~product_name~').join($product_name);
         summary_html = summary_html.split('~carrier_name~').join($carrier_name);
         summary_html = summary_html.split('~primary_member_name~').join($primary_member_name);
         summary_html = summary_html.split('~plan_name~').join($plan_name);
         summary_html = summary_html.replace(/~product_total~/g, $display_product_total);
         summary_html = summary_html.replace(/~dependent_count~/g, $dependent_count);
         $('#enrollment_summary_details_div').append(summary_html); 
         $('.table-responsive').scroll(function(e){
            e.stopPropagation();
            $('.datepicker-dropdown').css("display", "none");
            $('.coverage_date_input').blur();
        });
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

      updateBillingAddress();
   }

   function isNumber(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && charCode != 46 && charCode != 47 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      return true;
   }

   function show_take_home_pay_breakdown_section(calculation_data){
      if(take_home_breakdown == 'Y'){
         $("#plan_breakdown_home_pay_div").show();
      }
      $.each(calculation_data, function(ind, val) {
         if (ind == 'pre_tax_deductions_line_items_names') {
            $("#payment_pre_tax_deductions_line_items_names").html(val);
         } else if (ind == 'pre_tax_deductions_line_items_totals') {
            $("#payment_pre_tax_deductions_line_items_totals").html(val);
         } else if (ind == 'post_tax_deductions_line_items_names') {
            $("#payment_post_tax_deductions_line_items_names").html(val);
         } else if (ind == 'post_tax_deductions_line_items_totals') {
            $("#payment_post_tax_deductions_line_items_totals").html(val);
         } else {
            $("#payment_" + ind + "").html(val);
         }
      });
   }

//custom question tab 
function questionchangecolor(id,type=1){
   // $('#htmlrecommended_benefit').html("");
   // $('#self_guiding_benefits').html("");
   // $("#elected_bundle").val('');
   // $("#product_list").val('');
   
   //recommendations Tab
   $("#recommendationsTab").removeClass('active');
   $("#recommendationsTab").removeClass('completed');
   $("#recommendationsTab").addClass('disabled');

   //self guided menu
   $("#self_guided_menu").removeClass('active');
   $("#self_guided_menu").removeClass('completed');
   $("#self_guided_menu").addClass('disabled');
   
   //Application menu
   $("#applicationTab").removeClass('active');
   $("#applicationTab").removeClass('completed');
   $("#applicationTab").addClass('disabled');

   //Payment menu
   $("#paymentTab").removeClass('active');
   $("#paymentTab").removeClass('completed');
   $("#paymentTab").addClass('disabled');
         // var tempData = {};
         if(type == "multiselect"){
               var multiselect = "multiselect"+id;
               var selectedValues = [];    
               $("#"+multiselect+" :selected").each(function(){
                     selectedValues.push($(this).val()); 
               });
               if(selectedValues == ''){
                     $("#"+id).removeClass("completed");
               }else{
                     $("#"+id).addClass("completed");
               }
            // $(".multipleBundleSelect").each(function(){
            //    if($(this).val() !== ''){
            //       tempData[$(this).attr('data-question')] = $(this).val();
            //    }
            // });
         }else if(type == 'checkbox'){
               var selectcheckbox = 'checkbox'+id;
               var checkedValues = [];
               $('#'+selectcheckbox+' :checked').each(function() {
                     checkedValues.push($(this).val()); 
               });
               if(checkedValues == ''){
                  $("#"+id).removeClass("completed");
               }else{
                  $("#"+id).addClass("completed");
               }
               // $(".bundleQuestionCheckbox").each(function(){
               //    if($(this).is(':checked') === true){
               //       tempData[$(this).attr('data-question')] = tempData[$(this).attr('data-question')] !== undefined && tempData[$(this).attr('data-question')].length > 0 ? tempData[$(this).attr('data-question')] : [];
               //       tempData[$(this).attr('data-question')].push($(this).val());
               //    }
               // });
         }else if(type == 'radio'){
               $("#"+id).addClass("completed");
               // $(".bundleQuestion").each(function(){
               //    if($(this).is(':checked') === true){
               //       tempData[$(this).attr('data-question')] = $(this).val();
               //    }
               // });
         }else if(type == 'select'){
               var selectbox = 'select'+id;
               var selectboxValues = [];
               $('#'+selectbox+' :selected').each(function() {
                     selectboxValues.push($(this).val());
               if(selectboxValues == ''){
                     $("#"+id).removeClass("completed");
               }else{
                     $("#"+id).addClass("completed");
               }
            });
            // $(".bundleSelect").each(function(){
            //    if($(this).val() !== ''){
            //       tempData[$(this).attr('data-question')] = $(this).val();
            //    }
            // });
         }
         // selectedQuestions[type] = tempData;
   }
//end custom question tab 
// Recommended tab js

$(document).off('click', '.electbundle');
$(document).on('click','.electbundle',function(e){
   e.preventDefault();
   $('#ajax_loader').show();
   var getId = $(this).data("id");
   var id = $(this).attr("id");
   $('#api_key').val('productAddToCart');
   $("#fromStep").val('bundleRecommandation');
   $("#elected_bundle").val(getId);

   var allPrdRemove = true;
   $(".prd_benefit_tier"+getId).each(function(){
      var productId =  $(this).data("product-id");
      var checkplan = $('.removep'+getId+'_'+productId).is(':checked');
      if(!checkplan && productId!==undefined){
         allPrdRemove = false;
      }
   });

   if(allPrdRemove){
      alert("Please select any product");
      $('#ajax_loader').hide();
      return false;
   }

   var params_array = $("#frmGroupMemberEnrollment").serializeArray();
   // console.log(params_array);
   $.ajax({
      url : "<?=$HOST?>/ajax_group_member_enrollment.php",
      method: 'POST',
      data : params_array,
      dataType : 'JSON',
      beforeSend:function(){
         $("#bundleSelectError"+getId).hide();
      },
      success:function(res){
         if(res.status == 'Fail'){
            $('#ajax_loader').hide();
            $("#bundleSelectError"+getId).text(res.message).show();
            $("#elected_bundle").val('');
            $('#is_elected').val('N');
         }else if(res.status == 'success' || res.status == 'Success'){

            $("#"+id).addClass("electbundlegreen");
            $("#"+id).removeClass("electbundle");
            $('#is_elected').val('Y');
            $('.bundleblock').hide();
            $('#step').val(3);
            $(".removeplan").each(function(){
               var productId =  $(this).data("productid");
               var checkplan = $('.removep'+productId).is(':checked');
               if(checkplan){
                  $('#removeproduct'+productId).hide();
               }
            });
            $('.selecte'+getId).show();
            $('#changecolor'+getId).addClass('elect-bundle');
            $('#changecolor'+getId).show();
            $('#icon'+getId).addClass('fa fa-check-circle-o p-r-10 fa-lg');
            $("#elect_bundle_text"+getId).text($('#'+id).attr('data-bundle-label')+" ELECTED");

            codeExecuted = false;
            removedBundleProduct = remove_product_array.map(function($val){ return getId == $val['name'] ? $val['value'] : '' });
            var tempRequestAdded = false;
            $.each($('.prd_benefit_tier'),function($bundeId,$elementArrMain){
               var dataProductId = $(this).attr('data-product-id')
               if($(this).attr('data-bundlId') == getId && $.inArray(dataProductId,removedBundleProduct) == -1){
                  if(tempRequestAdded == false){
                     tempRequestAdded = true;
                  }
                  var cart_product = {
                     product_id: $(this).attr('data-product-id'),
                     price: $(this).find('option:selected').attr('data-price'),
                     display_price: $(this).find('option:selected').attr('data-display-price'),
                     matrix_id: $(this).find('option:selected').attr('data-prd-matrix-id'),
                     prd_plan_type_id: $(this).val(),
                     pricing_model: $(this).attr('data-pricing_model')
                  };
                  if($.inArray(dataProductId,cart_products.map(function($val){ return $val['product_id'] })) == -1){
                     cart_products.push(cart_product);
                  }
               }
            });
            if(tempRequestAdded){
               $('#ajax_loader').show();
               addedproducttoCart();
            }
         }
      }
   });
});

$(document).off('click', '.productestimate');
$(document).on('click','.productestimate',function(e){
   $('#api_key').val('calculateRateQuestionsDetails');
   $('#submitType').val('displayQuestion');
   $product_id = $(this).data("productestimate");
   $submitType = "displayQuestion";
   $("#product").val($product_id);
   var pricing_model = $(this).data("pricing_model");
   $("#pricing_model").val(pricing_model);
   // $("#inner_calculate_rate_main_div_"+$product_id).html('');
   if(typeof($load_enrollee_data) === "undefined") {
         $load_enrollee_data = false;
      }
   var params_array = $("#frmGroupMemberEnrollment").serializeArray();
   $.ajax({
      url : "<?=$HOST?>/ajax_group_member_enrollment.php",
      method: 'POST',
      data : params_array,
      dataType : 'JSON',
      success:function(res){
         if($submitType=="displayQuestion"){
                  if(res.data.data){
                     $.each(res.data.data,function($enrolleeType,$elementArrMain){
                     $.each($elementArrMain,function($elementArrMainKey,$elementArr){
                              $EnrollmentType=$enrolleeType.charAt(0).toUpperCase() + $enrolleeType.slice(1);
                              
                              $count=$("#frmGroupMemberEnrollment .productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType).length;
                              $number=$count+1;
                              html = $('#productQuestionDynamicDiv').html();
                              html = html.replace(/~Enrollee_Type~/g,$EnrollmentType);
                              html = html.replace(/~enrollee_type~/g,$enrolleeType);
                              html = html.replace(/~product_id~/g,$product_id);
                              html = html.replace(/~number~/g,$number);
                              $plan_id = $("#product_plan_"+$product_id).val();
                              // console.log(html);
                              if($enrolleeType=="child"){
                                 html = html.replace(/~child_counter~/g,$number);
                              }else{
                                 html = html.replace(/~child_counter~/g,'');
                              }
                              
                              $("#inner_calculate_rate_main_div_"+$product_id).append(html);

                              if($plan_id>0){
                                 $("#product_calcualate_rate_"+$product_id).collapse("show");
                                 $("#calculateRateButton_"+$product_id).removeClass('added_btn');
                                 $("#calculateRateButton_"+$product_id).removeClass('calculateRate');
                                 $("#calculateRateButton_"+$product_id).addClass('cancelCalculateRate');
                                 $("#calculateRateButton_"+$product_id).html('Cancel');
                                 $("#addCalculatedCoverage_"+$product_id).removeAttr('disabled','disabled');
                              }else{
                                 $("#error_product_plan_"+$product_id).html("Please Select Plan");
                                 $("#calculateRateButton_"+$product_id).removeClass('added_btn');
                                 $("#calculateRateButton_"+$product_id).removeClass('cancelCalculateRate');
                                 $("#calculateRateButton_"+$product_id).addClass('calculateRate');
                                 $("#calculateRateButton_"+$product_id).html('Calculate Rate');
                                 $("#addCalculatedCoverage_"+$product_id).attr('disabled','disabled');
                                 $("#product_calcualate_rate_"+$product_id).collapse("hide");
                              }

                              $.colorbox({
                              html : html,
                              width: '60%',
                              height: '700px',
                              closeButton: false
                           });
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
                              $("input[name='primary["+$product_id+"]["+$number+"][gender]']").on('readonly',true);
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

                     $('#frmGroupMemberEnrollment .dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 

                     $("#frmGroupMemberEnrollment .formatPricing").priceFormat({
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

                     $percentage = $('.max_benefit_percentage_' + $product_id).first().text();
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
         }
      }
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

$(document).off('click', '.electbundlegreen');
$(document).on('click','.electbundlegreen',function(e){
   $("#ajax_loader").show();
   var getId = $(this).data("id");
   $('.bundleblock').show();
   $('#changecolor'+getId).removeClass('elect-bundle');
   $('#icon'+getId).addClass('');
   $('.selecte'+getId).hide();
   
   $(this).addClass("electbundle");
   $(this).removeClass("electbundlegreen");
   $("#elect_bundle_text"+getId).text('ELECT BUNDLE');
   $('#is_elected').val('N');
   $(".bundle_page_electNone").show();
   $(".bundle_page_elected").hide();
   var tempRemoveBundleRequest = false;

   var resultPrdArr = cart_products.map(function(product) {
      return parseInt(product.product_id);
   });

   $.each($('select.prd_benefit_tier'),function($bundeId,$elementArrMain){
      var tempPrdId = $(this).data('product-id');
      // if($(this).data('bundlid') == getId || ($.inArray(tempPrdId,resultPrdArr) === -1 && $("#fromStep").val() != 'bundleRecommandation')){
      if($(this).data('bundlid') == getId){
         $("#bundle_product_price_"+getId+$(this).data('product-id')).val('');
         $("#bundle_product_matrix_"+getId+$(this).data('product-id')).val('');

         $("#product_price_"+$(this).data('product-id')).val('');
         $("#product_matrix_"+$(this).data('product-id')).val('');
      }
   });
   $.each($('.prd_benefit_tier'),function($bundeId,$elementArrMain){
      var dataProductId = $(this).attr('data-product-id');
      var dataPricingModel = $(this).attr('data-pricing_model');
      // if($(this).attr('data-bundlId') == getId || ($.inArray(dataProductId,resultPrdArr) === -1 && $("#fromStep").val() != 'bundleRecommandation')){
      if($(this).attr('data-bundlId') == getId){
         if(tempRemoveBundleRequest == false){
            removeProductFromCart(dataProductId,dataPricingModel,'recommended');
            tempRemoveBundleRequest = true;
         }
         if(cart_products.length > 0){
            $("#bundle_product_price_"+getId+dataProductId).val(0);
            $("#product_price_"+getId+dataProductId).val(0);
            removeItemByKeyValue(cart_products,'product_id',dataProductId);
            $(".cart_product_"+dataProductId).remove();
         }
         $("#bundle_product_benefit_tier_"+getId+dataProductId).change();
         $("#product_benefit_tier_"+dataProductId).change();
      }
   });

   $(".removeplan").each(function(){
      var productId = $(this).data("productid");
      $('#removeproduct'+productId).show();
   });

   // $("#self_guiding_benefits").html('');
   // $("#application").html('');
   $("#payment_summary").hide();

   var fromStepElectBundle = $("#fromStep").val();
   //self guided menu
   if(fromStepElectBundle != 'selfGuiding'){
      $("#self_guided_menu").removeClass('active');
      $("#self_guided_menu").removeClass('completed');
      $("#self_guided_menu").addClass('disabled');
      $("#recommendationsTab").removeClass('completed');
   }
   
   //Application menu
   $("#applicationTab").removeClass('active');
   $("#applicationTab").removeClass('completed');
   $("#applicationTab").addClass('disabled');

   //Payment menu
   $("#paymentTab").removeClass('active');
   $("#paymentTab").removeClass('completed');
   $("#paymentTab").addClass('disabled');

   // $(".bundle_page_total_amount").text('00.00');
   $("#elected_bundle").val('');
   $("#product_list").val('');
   $("#homepay_products_form").val('');
   
   $("#ajax_loader").show();
   setTimeout(function(){
      addedproducttoCart();
   },2000);
   setTimeout(function(){
      $("#ajax_loader").hide();
   },1000);
});

$(document).off('change', '.prd_benefit_tier');
$(document).on('change','.prd_benefit_tier',function(e){
   e.stopPropagation();
   var product_value = $(this).find('option:selected').attr('data-price');
   var display_product_value = $(this).find('option:selected').attr('data-display-price');
   var pricing_model = $(this).attr('data-pricing_model');
   $productId = $(this).attr('data-product-id');
   var id = $(this).attr('data-bundlid');
   $("#bundle_product_is_calculated_"+id+'_'+$productId).val('N');
   var matrixID = $(this).find('option:selected').attr('data-prd-matrix-id');
   $("#benefit_tierName_"+id+"_"+$productId).val(parseFloat(product_value).toFixed(2));
   if(pricing_model == 'FixedPrice'){
      $('#prices'+id+$productId).html('$'+parseFloat(display_product_value).toFixed(2)+'<sub>/ pay period</sub>');
   }else{
      $('#prices'+id+$productId).html('<sub>Starting at </sub>$'+parseFloat(display_product_value).toFixed(2));
   }
   var sum = 0;
   $("#inner_calculate_rate_main_div_"+$productId).html('');
   removeProductFromCart($productId,'Default','recommended');
   $("#bundle_product_price_"+id+$productId).val(parseFloat(product_value).toFixed(2));
   $("#bundle_display_product_price_"+id+$productId).val(parseFloat(display_product_value).toFixed(2));
   $("#bundle_product_matrix_"+id+$productId).val(matrixID);
   
   $("#temp_bundle_pricing_model_"+id+'_'+$productId).val(pricing_model);
   $("#bundle_added_product").val($productId);
   bundleTotal(id);
   setTimeout(function(){
      $('.electbundlegreen').click();
   },1000);
});

function bundleTotal(bundeId){
   var tempBundleTotal = parseFloat(0);
   var removedBundleProduct = remove_product_array.map(function($val){ return bundeId == $val['name'] ? $val['value'] : '' });
   $.each($('.prd_benefit_tier'),function($bundeId,$elementArrMain){
      var dataProductId = $(this).attr('data-product-id')
      if($(this).attr('data-bundlId') == bundeId && $.inArray(dataProductId,removedBundleProduct) == -1){
         // tempBundleTotal += $(this).find('option:selected').attr('data-display-price') != undefined ? parseFloat($(this).find('option:selected').attr('data-display-price')) : 0;
         tempBundleTotal += parseFloat($("#bundle_display_product_price_"+bundeId+dataProductId).val());
      }
   });
   $("#bundleTotal_"+bundeId).text(tempBundleTotal.toFixed(2)+'/ pay period');
}

function benefit_tier(id,obj){
   var $option = obj.find('option:selected');
      var value = $option.val();
      $('#prices'+id).html('$'+value+'<sub>/ pay period</sub>');
      var sum = 0;
}
//end Recommended tab js

   /* Self Guided Benefit js start */
      $(document).off('click','.selfGuidingBenefitsHref');
      $(document).on('click','.selfGuidingBenefitsHref', function(e){

        var dataFrom = $(this).data('from');
          var catID = $(this).data('category-id');
         $("#category_step").val(catID);
        if(dataFrom !== undefined){
          checkSelfGuidingAddedProduct(catID,true);
          var nextCategoryId = $(this).data('next-category-id');
         if(nextCategoryId !== undefined){
            $("#category_step").val(nextCategoryId);
            $(".selfGuidingBenefitsMenu").removeClass('active current');
            $("#selfGuidingBenefitsMenu"+nextCategoryId).addClass('active current');
         }
        }else{
          checkSelfGuidingAddedProduct(catID);
          $(".selfGuidingBenefitsMenu").removeClass('active current');
          $("#selfGuidingBenefitsMenu"+catID).removeClass('line');
          $("#selfGuidingBenefitsMenu"+catID).addClass('active current');
        }
        $(".enrollmentLeftmenuItem").removeClass("active");
        $(".enrollmentLeftmenuActive [data-stepli='"+($("#step").val())+"']").addClass('active');
      });

		$(document).off('click', '.productViewDetail');
		$(document).on('click', '.productViewDetail', function() {
			var product_id = $(this).attr('data-product-id');
			$(this).addClass("closeProductViewDetail");
			$(this).removeClass("productViewDetail");
			$(this).text('');
			$(this).append("<i class='fa fa-close fa-lg p-r-5'></i>" + " Close Details");
			$("#span_product_view_detail_" + product_id).attr('data-original-title', 'Close Details');
         $('[data-toggle="tooltip"]').tooltip();
		});

		$(document).off('click', '.closeProductViewDetail');
		$(document).on('click', '.closeProductViewDetail', function() {
			var product_id = $(this).attr('data-product-id');
			$(this).addClass("productViewDetail");
			$(this).removeClass("closeProductViewDetail");
			$(this).text('');
			$(this).append("<i class='fa fa-eye fa-lg p-r-5'></i>" + " View Details");
			$("#span_product_view_detail_" + product_id).attr('data-original-title', 'View Details');
         $('[data-toggle="tooltip"]').tooltip();
		});

		$(document).off('click', '.addPlanself');
		$(document).on('click', '.addPlanself', function(e) {
         e.preventDefault();
			var product_id = $(this).attr('data-product-id');
            var category_id = $(this).attr('data-category-id');
         var pricing_model = $(this).attr('data-pricing-model');
         var price = $("#product_benefit_tier_"+product_id).find('option:selected').attr('data-price');
         var display_price = $("#product_benefit_tier_"+product_id).find('option:selected').attr('data-display-price');
         var matrix_id = $("#product_benefit_tier_"+product_id).find('option:selected').attr('data-prd-matrix-id');
         
			var BenefitType = $('#product_benefit_tier_' + product_id).val();
			if (BenefitType > 0) {
				$(this).addClass("planSelectedself");
				$(this).removeClass("addPlanself");
				$(this).text('');
				$(this).append("<i class='fa fa-check-circle-o p-r-10 fa-lg' aria-hidden='true'></i>" + " Plan Selected");
				$('#product_list_' + product_id).addClass("selected");
				$('#selected_product_id_' + product_id).val(product_id);
				// $('#product_list').val(product_id);
				$('#api_key').val('productAddToCart');
				$("#error_product_benefit_tier_" + product_id).html("");
                checkSelfGuidingAddedProduct(category_id);
                addToCart(product_id,price,matrix_id,pricing_model,display_price);
			} else {
				$("#error_product_benefit_tier_" + product_id).html("Please Select Benefit Tier Type");
			}

         // $("#product_price_"+product_id).val(price);
         // $("#display_product_price_"+product_id).val(display_price);
         // $("#product_matrix_"+product_id).val(matrix_id);
         // $("#added_product").val(product_id);
         // category_id = $("#product_benefit_tier_"+product_id).attr('data-category-id');
		});

      $(document).off('click','.calculatePlanSelf');
      $(document).on('click','.calculatePlanSelf',function(e){
         e.preventDefault();
         $product_id = $(this).attr('data-product-id');
         $plan_id = $("#product_benefit_tier_"+$product_id).val();
         $bundleId = $(this).attr('data-bundleid');
         $pricing_model = $(this).attr('data-pricing-model');
         var recommanded_tab = $(this).attr('data-recommanded-tab');
         if($bundleId !='' && $bundleId !== undefined && $bundleId !== $("#elected_bundle").val()){
            $("#elected_bundle").val($bundleId);
         }
         if(recommanded_tab !== undefined){
            $plan_id = $("#bundle_product_benefit_tier_"+$bundleId+$product_id).find('option:selected').val();
            if($plan_id > 0){
               $("#bundlecalculatePlanSelf_"+$product_id).removeClass("calculatePlanSelf");
               $("#bundlecalculatePlanSelf_"+$product_id).removeClass("addPlanself");
               $("#addCalculatedCoverage_"+$product_id).removeAttr('disabled','disabled');
               addAdditionalQuestions($product_id,$pricing_model,'','displayQuestion','','','recommanded',$bundleId);
            }
         }else{
            if($plan_id > 0){
               $("#calculatePlanSelf_"+$product_id).removeClass("calculatePlanSelf");
               $("#calculatePlanSelf_"+$product_id).removeClass("addPlanself");
               $("#addCalculatedCoverage_"+$product_id).removeAttr('disabled','disabled');
               addAdditionalQuestions($product_id,$pricing_model,'','displayQuestion');
            }
         }
         $(".error").html('');
      });

		$(document).off('click', '.planSelectedself');
		$(document).on('click', '.planSelectedself', function() {
			var product_id = $(this).attr('data-product-id');
         var pricing_model = $(this).attr('data-pricing-model');
         var category_id = $(this).attr('data-category-id');
        
         changeBundleClass(product_id);
         removeProductFromCart(product_id,pricing_model);
         $("#primary_member_field_div").html('');
         $("#payment_summary").hide();
         //Application menu
         $("#applicationTab").removeClass('active');
         $("#applicationTab").removeClass('completed');
         $("#applicationTab").addClass('disabled');

         //Payment menu
         $("#paymentTab").removeClass('active');
         $("#paymentTab").removeClass('completed');
         $("#paymentTab").addClass('disabled');

         $("#self_guided_menu").removeClass('completed');
         changeBundleClass(product_id);
         checkSelfGuidingAddedProduct(category_id);
		});

		$(document).off('change', '.benefittier_type');
		$(document).on('change', '.benefittier_type', function(e) {
			e.stopPropagation();
			var $product_id = $(this).attr("data-product-id");
			var $price = $(this).find('option:selected').attr('data-price');
         $display_price = $(this).find('option:selected').attr('data-display-price');
         var extraHtml = '$';
         if($(".pricing_model_"+$product_id).first().val() != 'FixedPrice'){
            extraHtml = '<sub>Starting at </sub> $';
         }
			

         if ($(this).val() != "") {
            $("#product_price_label_" + $product_id).html(extraHtml+parseFloat($display_price).toFixed(2) + '<sub class="fs12" style="bottom:0px;">/ pay period</sub>');
         } else {
            productPriceDisplay($product_id,'0.00');
         }
         $("#inner_calculate_rate_main_div_"+$product_id).html('');
         removeProductFromCart($product_id,'Default');
         changeBundleClass($product_id);
		});

      $(document).off("click",".autoAssign_colorbox_details");
      $(document).on("click",".autoAssign_colorbox_details",function(e){
         $product_id = $(this).attr('data-product-id');
         showProductDescriptionPopup($product_id);
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

      $(document).off("change",".autoAssign_colorbox_plan");
      $(document).on("change",".autoAssign_colorbox_plan",function(e){
            e.stopPropagation();
            $val = $(this).val();
            $product_id = $(this).attr('data-product-id');
            $price = $(this).find('option:selected').attr('data-price');
            $("#product_benefit_tier_"+$product_id).val($val);
            $("#product_benefit_tier_"+$product_id).selectpicker('refresh');

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

      $(document).off("click","#autoAssign_colorbox_confirm");
      $(document).on("click","#autoAssign_colorbox_confirm",function(e){

         if($(".autoAssginedProductApproved.text-success").length > 0){
             $(".autoAssginedProductApproved.text-success").each(function(){
                 $product_id = $(this).attr('data-product-id');
                 $product_plan = $("#autoAssign_colorbox_plan_"+$product_id).val();
                 
                 if ($product_plan != "") {
                     $("#product_benefit_tier_"+$product_id).val($product_plan);
                     $("#product_benefit_tier_"+$product_id).selectpicker('refresh');

                     if(!$("#addPlanself_"+$product_id).hasClass('planSelectedself')){
                         $autoAssignedProductArr = [];
                         $("#addPlanself_"+$product_id).trigger('click');
                     }
                     $("#autoAssign_colorbox_product_"+$product_id).remove();
                     $("#required_colorbox_product_"+$product_id).remove();
                     
                     window.parent.$.colorbox.close();
                 }

             });
         }else{
            if($(".autoAssginedProductReject.text-action").length > 0){
               $(".autoAssginedProductReject.text-action").each(function(){
                  $product_id = $(this).attr('data-product-id');
                  $product_plan = $("#autoAssign_colorbox_plan_"+$product_id).val();
                  if(cart_products.length > 0 && $product_plan!=""){
                     removeProductFromCart($product_id,$product_plan);
                     removeBundleProduct($product_id);
                  }
               });
               window.parent.$.colorbox.close();
            }
         }
      });

      $(document).off("click",".required_colorbox_details");
      $(document).on("click",".required_colorbox_details",function(e){
         $product_id = $(this).attr('data-product-id');
         showProductDescriptionPopup($product_id);
      });

      $(document).off("click","#required_colorbox_confirm");
      $(document).on("click","#required_colorbox_confirm",function(e){
         
         if($("#requiredColorboxProductDiv .required_colorbox_product").length > 0){
             $("#requiredColorboxProductDiv .required_colorbox_product").each(function(){
                 $product_id = $(this).attr('data-product-id');
                 $product_plan = $("#required_colorbox_plan_"+$product_id).val();
                  if ($product_plan != "") {
                     $("#product_benefit_tier_"+$product_id).val($product_plan);
                     $("#product_benefit_tier_"+$product_id).selectpicker('refresh');

                     if(!$("#addPlanself_"+$product_id).hasClass('planSelectedself')){
                         $requiredProductArr = [];
                         $("#addPlanself_"+$product_id).trigger('click');
                         
                     }
                     $("#autoAssign_colorbox_product_"+$product_id).remove();
                     $("#required_colorbox_product_"+$product_id).remove();
                  }
                  window.parent.$.colorbox.close();
             });
         }
         
      });

      $(document).off("change",".required_colorbox_plan");
      $(document).on("change",".required_colorbox_plan",function(e){
            e.stopPropagation();
            $val = $(this).val();
            $product_id = $(this).attr('data-product-id');
            $price = $(this).find('option:selected').attr('data-price');
            $("#product_benefit_tier_"+$product_id).val($val);
            $("#product_benefit_tier_"+$product_id).selectpicker('refresh');

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


      addToCart = function(product_id,price,matrix_id,pricing_model,display_price){

         $("#primary_member_field_div").html('');
         $("#payment_summary").hide();
         //Application menu
         $("#applicationTab").removeClass('active');
         $("#applicationTab").removeClass('completed');
         $("#applicationTab").addClass('disabled');         

         var params_array = $("#frmGroupMemberEnrollment").serializeArray();
         var price = parseFloat(price).toFixed(2);
         var display_price = parseFloat(display_price).toFixed(2);
         $("#product_price_"+product_id).val(price);
         $("#display_product_price_"+product_id).val(display_price);
         $("#product_matrix_"+product_id).val(matrix_id);
         $("#added_product").val(product_id);
         category_id = $("#product_benefit_tier_"+product_id).attr('data-category-id');
         $("#product_category_"+product_id).val(category_id);
         
         if(pricing_model == "FixedPrice"){
            $("#addPlanself_"+product_id).addClass("planSelectedself");
            $("#addPlanself_"+product_id).removeClass("addPlanself");
            $("#addPlanself_"+product_id).text('');
            $("#addPlanself_"+product_id).append("<i class='fa fa-check-circle-o p-r-10 fa-lg' aria-hidden='true'></i>" + " Plan Selected");
            $('#product_list_' + product_id).addClass("selected");
         }else{
            $("#calculatePlanSelf_"+product_id).removeClass('cancelCalculateRate');
            $("#calculatePlanSelf_"+product_id).removeClass('calculateRate');
            $("#calculatePlanSelf_"+product_id).removeClass('calculatePlanSelf');
            $("#calculatePlanSelf_"+product_id).addClass('planSelectedself');
            $("#calculatePlanSelf_"+product_id).text('');
            $("#calculatePlanSelf_"+product_id).append("<i class='fa fa-check-circle-o p-r-10 fa-lg' aria-hidden='true'></i>" + " Plan Selected");
            $('#product_list_' + product_id).addClass("selected");
            // $("#addCalculatedCoverage_"+$product_id).attr('disabled','disabled');
            // $("#product_calcualate_rate_"+$product_id).collapse("hide");
            // typeof(matrix_id);
            // console.log(matrix_id);
            // if(matrix_id != 0) {
            //    matrix_id = matrix_id.join(',');
            // }
         }

         prd_plan_type_id = $("#product_benefit_tier_"+product_id).val();
         var cart_product = {
            product_id: product_id,
            price: price,
            display_price: display_price,
            matrix_id: matrix_id,
            prd_plan_type_id: prd_plan_type_id,
            pricing_model: pricing_model
         }

         if($.inArray(product_id,cart_products.map(function($val){ return $val['product_id'] })) == -1){
            cart_products.push(cart_product);
         }

         cartTotalCalculate(product_id,'add');

         if(price==0 && matrix_id==0 && pricing_model==0){
         }else{
            $primary_product_change=true;
            $spouse_product_change=true;
            $verification_option_product_change=true;
         }
      }

      cartTotalCalculate = function(product_id,cartAction,$fromTab){
         var tempaddingBundleProductToSelfGuiding = $("#addingBundleProductToSelfGuiding").val();
         $(".error").html('');
         $("#api_key").val('cartTotalCalculate');
         $("#ajax_loader").show();
         var tempStep = $("#step").val();

         $("#step").val(0);
            $.ajax({
            url: '<?=$HOST?>/ajax_group_member_enrollment.php',
            data: $("#frmGroupMemberEnrollment").serializeArray(),
            type: 'POST',
            async: (tempaddingBundleProductToSelfGuiding == 1 ? false : true),
            dataType: 'json',
            beforeSend :function(){
               $("#ajax_loader").show();
            },
            success:function(res){
               $("#step").val(tempStep);
               $("#ajax_loader").hide();
               $("#added_product").val(0);
               // $("#addToCartTable").html('');
               var tempproductlist = res.product_list;
               $("#product_list").val(res.product_list);
               // var $page = '';
               // if($fromTab !== undefined && $fromTab !== ''){
               //    $page = "bundle_page_";
               //    $("#"+$page+"product_display").html('');
               // }else{
                  $page = "self_guiding_benefits_page_";
                  $("."+$page+"product_display").html('');
               // }

               if(res.premium_products){
                  $("#gap_plus_product_list").val('');
                  $.each(res.premium_products,function($id,$value){
                     $randNumber = Math.floor(Math.random() * 100);
                     $tmp_total_price=parseFloat($value.display_member_price).toFixed(2);

                     cart_html = $("#addToCartDynamicTable").html();
                     cart_html = cart_html.replace(/~randNumber~~product_id~/g, $randNumber+''+$value.product_id);
                     cart_html = cart_html.replace(/~product_id~/g, $value.product_id);
                     cart_html = cart_html.split('~product_name~').join($value.product_name);
                     cart_html = cart_html.replace(/~plan_name~/g, $value.plan_name);
                     cart_html = cart_html.replace(/~tmp_total_price~/g, $tmp_total_price);
                     cart_html = cart_html.replace(/~fromTab~/g, $fromTab);

                     if($fromTab !== undefined && $fromTab !== ''){
                        $("#bundle_product_price_label_"+$value.product_id).html(parseFloat($tmp_total_price).toFixed(2)+'<sub>/ pay period</sub>');
                        $("#bundle_display_product_price_"+$value.product_id).val($tmp_total_price);
                        $("#bundle_product_benefit_tier_"+$("#elected_bundle").val()+$value.product_id).find('option:selected').data('display-price',$tmp_total_price);
                     }else{
                        productPriceDisplay($value.product_id,$tmp_total_price);
                        $("#display_product_price_"+$value.product_id).val($tmp_total_price);
                     }

                     $("."+$page+"product_display").append(cart_html);

                     if($("#gap_plus_product_list").val() == '' && $value.is_gap_plus_product == 'Y'){
                        $("#gap_plus_product_list").val($value.product_id);
                     }
                  });
               }

               if(res.linked_Fee && display_admin_fee){
                  $.each(res.linked_Fee,function($id,$value){
                     $tmp_product_price = parseFloat($value.price).toFixed(2);
                     // $tmp_group_price = parseFloat($value.group_price).toFixed(2);
                     // $tmp_total_price=parseFloat(parseFloat($value.price)+parseFloat($value.group_price)).toFixed(2);
                     $randNumber = Math.floor(Math.random() * 100);

                     cart_html = $("#addToCartDynamicTable").html();
                     cart_html = cart_html.replace(/~randNumber~~product_id~/g, $randNumber+''+$value.product_id);
                     cart_html = cart_html.replace(/~product_id~/g, $value.fee_product_id);
                     cart_html = cart_html.split('~product_name~').join($value.product_name);
                     cart_html = cart_html.replace(/~plan_name~/g, '');
                     cart_html = cart_html.replace(/Member Rate/g, '');
                     cart_html = cart_html.replace(/~tmp_total_price~/g, $tmp_product_price);
                     cart_html = cart_html.replace(/~fromTab~/g, $fromTab);

                     $("."+$page+"product_display").append(cart_html);
                  })
               }

               if(res.membership_Fee){
                  $.each(res.membership_Fee,function($id,$value){
                     $tmp_product_price = parseFloat($value.price).toFixed(2);
                     // $tmp_group_price = parseFloat($value.group_price).toFixed(2);
                     // $tmp_total_price=parseFloat(parseFloat($value.price)+parseFloat($value.group_price)).toFixed(2);

                     cart_html = $("#addToCartDynamicTable").html();
                     cart_html = cart_html.replace(/~randNumber~~product_id~/g, $randNumber+''+$value.product_id);
                     cart_html = cart_html.replace(/~product_id~/g, $value.fee_product_id);
                     cart_html = cart_html.split('~product_name~').join($value.product_name);
                     cart_html = cart_html.replace(/~plan_name~/g, '');
                     cart_html = cart_html.replace(/Member Rate/g, '');
                     cart_html = cart_html.replace(/~tmp_total_price~/g, $tmp_product_price);
                     cart_html = cart_html.replace(/~fromTab~/g, $fromTab);

                     $("."+$page+"product_display").append(cart_html);
                  });
               }

               $calculateSubTotal = parseFloat(res.display_sub_total).toFixed(2);
               $calculateGroupPriceSubTotal = parseFloat(res.display_group_price_sub_total).toFixed(2);
               $calculateTotal = parseFloat(res.display_order_total).toFixed(2);
               $service_fee_total = parseFloat(res.service_fee_total).toFixed(2);

               $healthy_step_fees_total = parseFloat(res.healthy_step_fees_total).toFixed(2);
               $healthy_step_fees_name = res.healthy_step_fees_name;

               $summary_monthly_payment = (parseFloat($calculateSubTotal)+parseFloat($service_fee_total)).toFixed(2);

               if($(".recommandedtotalPrice").length > 0){
                  $tempBundleTotalPrice = parseFloat(0);
                  $(".recommandedtotalPrice").each(function(){
                     $tempBundleTotalPrice += parseFloat($(this).text());
                  });
                  // $("#bundle_page_total_amount").html(parseFloat($tempBundleTotalPrice).toFixed(2));

                  var cartTotalBundle_html = $("#dynamicCartTotal").html();
                  cartTotalBundle_html = cartTotalBundle_html.replace(/~pageLocation~/g, 'recommanded');
                  cartTotalBundle_html = cartTotalBundle_html.replace(/~product_total~/g, '$'+parseFloat(res.display_order_total).toFixed(2));
                  cartTotalBundle_html = cartTotalBundle_html.replace(/~product_group_contribution~/g, '$'+parseFloat(res.display_group_price_sub_total).toFixed(2));
                  cartTotalBundle_html = cartTotalBundle_html.replace(/~fee_total~/g, '$'+parseFloat($service_fee_total).toFixed(2));
                  cartTotalBundle_html = cartTotalBundle_html.replace(/~fee_group_contribution~/g, '$0.00');
                  cartTotalBundle_html = cartTotalBundle_html.replace(/~total_amount~/g, '$'+parseFloat($calculateTotal).toFixed(2));
                  $(".bundle_page_totalInfo").attr('data-content',cartTotalBundle_html);
                  $('[data-toggle="popover"]').popover();
                  bundleTotal($("#elected_bundle").val());
               }
               $("."+$page+"total_amount").html($calculateTotal);
               $("."+$page+"elected").show();
               $("."+$page+"electNone").hide();
               hideShowCart();

               var cartTotal_html = $("#dynamicCartTotal").html();
               cartTotal_html = cartTotal_html.replace(/~pageLocation~/g, $page);
               cartTotal_html = cartTotal_html.replace(/~product_total~/g, '$'+parseFloat(res.display_order_total).toFixed(2));
               cartTotal_html = cartTotal_html.replace(/~product_group_contribution~/g, '$'+parseFloat(res.display_group_price_sub_total).toFixed(2));
               cartTotal_html = cartTotal_html.replace(/~fee_total~/g, '$'+parseFloat($service_fee_total).toFixed(2));
               cartTotal_html = cartTotal_html.replace(/~fee_group_contribution~/g, '$0.00');
               cartTotal_html = cartTotal_html.replace(/~total_amount~/g, '$'+parseFloat($calculateTotal).toFixed(2));
               $("."+$page+"totalInfo").attr('data-content',cartTotal_html);
               $('[data-toggle="popover"]').popover();
               if(cartAction == "add"){

                  $is_auto_assign_product = false;
                  $is_required_product = false;

                  if(res.healthyStepFeeList){
                     $.each(res.healthyStepFeeList,function($key,$value){
                        
                     });
                  }else{
                     $("#healthy_step_fee").val('-1');
                     quote_healthy_step_fee = 0;
                  }

                  $product_plan = $("#product_benefit_tier_"+product_id).val();

                  $(".hidden_product_price").each(function(){
                     $packagedForArr = [];
                     $tmp_product_id = $(this).attr('data-product-id');
                     $packagedFor = $("#product_price_"+$tmp_product_id).attr('data-packaged-product-for');
                     if($packagedFor!='' && $packagedFor != undefined){
                        $packagedForArr = $packagedFor.split(",");
                     }
                     if($packagedForArr.length > 0){
                        $.each($packagedForArr,function($k,$v){
                           if($v==product_id){
                              $("#product_list_"+$tmp_product_id).removeClass('packaged_body');
                              $("#packaged_content_"+$tmp_product_id).hide();
                           }
                        });
                     }
                  });

                  if(res.combination_products){
                     if(res.combination_products['Auto Assign'] && res.combination_products['Auto Assign']['product_id']){
                        $AutoAssigned=res.combination_products['Auto Assign']['product_id'].split(',');
                        $.each($AutoAssigned,function($k,$v){
                           
                           if($("#addPlanself_"+$v).length > 0 && $("#addPlanself_"+$v).hasClass('addPlanself') && !$("#product_list_"+$v).hasClass('excluded_body') && !$("#product_list_"+$v).hasClass('packaged_body')){
                              // !$("#product_list_"+$v).hasClass('excluded_body') && !$("#product_list_"+$v).hasClass('packaged_body')

                              // add additional condition on above if statement

                              $autoAssignedProductArr.push($v);

                              $is_auto_assign_product = true;
                              $product_name = $("#product_name_"+$v).html();
                              colorbox_html = $("#autoAssignColorboxProductDynamicDiv").html();
                              colorbox_html = colorbox_html.replace(/~product_id~/g, $v);
                              colorbox_html = colorbox_html.split('~product_name~').join($product_name);

                              if($("#autoAssignColorboxProductDiv #autoAssign_colorbox_product_"+$v).length <=0){
                                 $("#autoAssignColorboxProductDiv").append(colorbox_html);
                                 $count = $("#autoAssignColorboxProductDiv .autoAssign_colorbox_product").length;

                                 $planHtml = $("#product_benefit_tier_"+$v).html();
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

                              // $autoAssignedProductArr = jQuery.grep($autoAssignedProductArr, function(value) {
                              //    return value != product_id;
                              // });

                              // if($is_auto_assign_product || $autoAssignedProductArr.length > 0){
                              //    autoAssignProduct(product_id);
                              // }
                           }
                        });
                     }

                     if(res.combination_products['Excludes'] && res.combination_products['Excludes']['product_id']){
                        $Excludes=res.combination_products['Excludes']['product_id'].split(',');
                        $.each($Excludes,function($k,$v){
                           $excludedFor = $("#product_price_"+$v).attr('data-excluded-product-for');
                           if($excludedFor != "" && $excludedFor != undefined){
                              $excludedForArr = $excludedFor.split(",");
                              if(jQuery.inArray(product_id, $excludedForArr ) < 0){
                                 $excludedForArr.push(product_id);
                             }
                           }else{
                              $excludedForArr = [];
                              $excludedForArr.push(product_id);
                           }

                           $excludedForNameArr = [];
                           $.each($excludedForArr,function($k1,$v1){
                              $excludedForNameArr.push($("#product_name_"+$v1).html());
                           });

                           $excludedForList = $excludedForArr.join(",");
                           $excludedForNameList = $excludedForNameArr.join(",");

                           // if($("#addPlanself_"+$v).hasClass('planSelectedself')){
                              $("#addingBundleProductToSelfGuiding").val(1);
                              removeProductFromCart($v,'Default');
                              setTimeout(function(){
                                 $("#addingBundleProductToSelfGuiding").val(0);
                                 removeBundleProduct($v);
                              },1500);
                           // }

                           if(!$("#product_list_"+$v).hasClass('packaged_body')){
                              $("#product_list_"+$v).addClass('excluded_body');
                              $("#excluded_content_"+$v).show();
                              $("#product_price_"+$v).attr('data-excluded-product-for',$excludedForList);
                              $("#excluded_content_product_name_"+$v).html($excludedForNameList);
                           }

                        });
                     }

                     if(res.combination_products['Required'] && res.combination_products['Required']['product_id']){
                        $Required=res.combination_products['Required']['product_id'].split(',');
                        $("#product_price_"+product_id).attr('data-required-product',res.combination_products['Required']['product_id']);
                        $.each($Required,function($k,$v){
                           $requiredFor=$("#product_price_"+$v).attr('data-is-required-for');
                           if($requiredFor !='' && $requiredFor != undefined){
                              $requiredForArr = $requiredFor.split(',');
                              if(jQuery.inArray(product_id, $requiredForArr ) < 0){
                                 $requiredForArr.push(product_id);
                              }
                           }else{
                              $requiredForArr = [];
                              $requiredForArr.push(product_id);
                           }

                           $requiredFor = $requiredForArr.join(",");

                           $("#product_price_"+$v).attr('data-is-required-for',$requiredFor);

                           if( $("#addPlanself_"+$v).length > 0 && $("#addPlanself_"+$v).hasClass('addPlanself') && !$("#product_list_"+$v).hasClass('excluded_body') && !$("#product_list_"+$v).hasClass('packaged_body') ){

                              $requiredProductArr.push($v);
                              $is_required_product = true;
                              $product_name = $("#product_name_"+$v).html();
                              colorbox_html = $("#requiredColorboxProductDynamicDiv").html();
                              colorbox_html = colorbox_html.replace(/~product_id~/g, $v);
                              colorbox_html = colorbox_html.split('~product_name~').join($product_name);

                              if($("#requiredColorboxProductDiv #required_colorbox_product_"+$v).length <=0){
                                 $("#requiredColorboxProductDiv").append(colorbox_html);
                                 $count = $("#requiredColorboxProductDiv .required_colorbox_product").length;
                                 $planHtml = $("#product_benefit_tier_"+$v).html();
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
                                 $("#required_colorbox_plan_"+$v).selectpicker('setStyle', 'btn-select');
                              }
                           }
                        });
                        if($is_required_product){
                           $product_name = $("#product_name_"+product_id).html();
                           $("#required_product_name").html($product_name);
                        }
                     }

                     fRefresh();
                  }
                  $autoAssignedProductArr = jQuery.grep($autoAssignedProductArr, function(value) {
                     return value != product_id;
                  });
                  if($is_auto_assign_product || $autoAssignedProductArr.length > 0){
                     autoAssignProduct(product_id);
                  }else{
                     if($is_required_product || $requiredProductArr.length > 0){
                        requiredProduct();
                     }
                  }

                  if($("#inner_calculate_rate_main_div_"+product_id+" .additional_tmp_question").length > 0){
                     $("#inner_calculate_rate_main_div_"+product_id+" .additional_tmp_question").each(function(){
                        $elName=$(this).attr('data-element');
                        $elId=$(this).attr('data-id');
                        $elType=$(this).attr('data-enrollee-type');
                        $val=$("#hidden_"+$elType+"_"+product_id+"_"+$elId+"_"+$elName).val();
                        
                        $element = $elType+"_"+$elName+"_"+$elId;
                        
                        if ($('#'+$elType+"_"+$elName+"_div_"+product_id+"_"+$elId).css('display') != 'none' && $('#'+$elType+"_"+$elName+"_div_"+product_id+"_"+$elId).css('display') != undefined) {
                            if($val!= undefined){
                                $enrolleeElements.push($element);
                                $enrolleeElementsVal[$element] = $val;
                                enrolleeElements($element,$elType,$elName,$elId);
                            }
                        }
                     });
                     $tmpEnrolleeElementsVal = $enrolleeElementsVal;
                  }

               }else if(cartAction == "remove"){
                  $(".hidden_product_price").each(function(){
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
                            if($("#addPlanself_"+$v).hasClass('planSelectedself') || $("#calculatePlanSelf_"+$v).hasClass('planSelectedself')){
                                $is_remove_packaged = false;
                            }
                        });

                        if($is_remove_packaged){
                            if($("#addPlanself_"+$tmp_product_id).hasClass('planSelectedself') || $("#calculatePlanSelf_"+$tmp_product_id).hasClass('planSelectedself')){
                                removeProductFromCart($tmp_product_id,'Default');
                                removeBundleProduct($tmp_product_id);
                            }
                            if(!$("#product_list_"+$tmp_product_id).hasClass('excluded_body')){
                                $("#product_list_"+$tmp_product_id).addClass('packaged_body');
                                $("#packaged_content_"+$tmp_product_id).show();
                            }
                        }
                     }

                     if($excludedForArr.length > 0){

                        $excludedForArr = jQuery.grep($excludedForArr, function(value) {
                          return value != product_id;
                        });
                        $is_remove_excluded = true;

                        if($excludedForArr.length > 0){
                            $.each($excludedForArr,function($k,$v){
                                $excludedForNameArr.push($("#product_name_"+$v).html());
                                if($("#addPlanself_"+$v).hasClass('planSelectedself') || $("#calculatePlanSelf_"+$v).hasClass('planSelectedself')){
                                    $is_remove_excluded = false;
                                }
                            });
                            $excludedForList = $excludedForArr.join(",");
                            $excludedForNameList = $excludedForNameArr.join(",");
                            $("#excluded_content_product_name_"+$tmp_product_id).html($excludedForNameList);
                            $("#product_price_"+$tmp_product_id).attr('data-excluded-product-for',$excludedForList);
                        }

                        if($is_remove_excluded){
                            $("#product_list_"+$tmp_product_id).removeClass('excluded_body');
                            $("#excluded_content_product_name_"+$tmp_product_id).html('');
                            $("#product_price_"+$tmp_product_id).attr('data-excluded-product-for','');
                            $("#excluded_content_"+$tmp_product_id).hide();
                        }
                     }
                  });

                  if($("#inner_calculate_rate_main_div_"+product_id+" .additional_tmp_question").length > 0){
                     $("#inner_calculate_rate_main_div_"+product_id+" .additional_tmp_question").each(function(){
                        $elName=$(this).attr('data-element');
                        $elId=$(this).attr('data-id');
                        $elType=$(this).attr('data-enrollee-type');

                        $element = $elType+"_"+$elName+"_"+$elId;
                        if ($('#'+$elType+"_"+$elName+"_div_"+product_id+"_"+$elId).css('display') != 'none' && $('#'+$elType+"_"+$elName+"_div_"+product_id+"_"+$elId).css('display') != undefined) {
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

                  $("#addPlanself_"+product_id).addClass("addPlanself");
                  $('#product_list_' + product_id).removeClass("selected");
                  $("#addPlanself_"+product_id).text('Add Plan');
                  $("#addPlanself_"+product_id).removeClass("planSelectedself");
                  
                  $("#bundle_product_price_"+$("#elected_bundle").val()+product_id).val(0);
                  $("#product_price_"+product_id).val('0.00');
                  $("#display_product_price_"+product_id).val('');
                  $("#product_matrix_"+product_id).val('0');
                  $("#added_product").val('');

                  $(".cart_product_"+product_id).remove();
               }
               addOnProducts();
               setTimeout(function(){
                  $("#ajax_loader").hide();
                  callCalculateTakeHomePay();
               },2000);
            }
         });
      }

      removeProductFromCart = function($product_id,$removeType,$fromTab){
         $("#product_category_"+$product_id).val('');
         
         $product_price_ = 'product_price_';
         $product_matrix_ = 'product_matrix_';
         if($fromTab !== undefined && $fromTab !== ''){
            $product_price_ = 'bundle_product_price_';
            $product_matrix_ = 'bundle_product_matrix_';
         }
         if($(".cart_product_"+$product_id).length > 0){
            $RequiredList = $("#"+$product_price_+$product_id).attr('data-required-product');

            if($RequiredList!='' && $RequiredList!=undefined){
               $Required = $RequiredList.split(',');
               $.each($Required,function($k,$v){
                   $requiredForList=$("#"+$product_price_+$v).attr('data-is-required-for');

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
                   $("#"+$product_price_+$v).attr('data-is-required-for',$requiredFor);
                   
               });
               $("#"+$product_price_+$product_id).attr('data-required-product','');
            }

            $requiredProduct = $("#"+$product_price_+$product_id).attr('data-is-required-for');
            $ReuiredProductName = [];
            $requiredForArr = $requiredProduct !== "undefined" && $requiredProduct !== undefined && $requiredProduct !== '' ? $requiredProduct.split(',') : [];
            if($requiredForArr.length > 0){
               $.each($requiredForArr,function($k,$v){
                  var productExistsInCart = false;
                  $.each(cart_products,function(key,value){
                     if($v == value['product_id']){
                        productExistsInCart = true;
                        return false;
                     }
                  });
                  if(productExistsInCart){
                     $ReuiredProductName.push($("#product_name_"+$v).html());
                  }
               });
            }

            if($ReuiredProductName.length == 0){
               $price = $("#"+$product_price_+$product_id).val();
               $("#"+$product_price_+$product_id).val('0.00');
               $("#"+$product_matrix_+$product_id).val('0');
               $(".cart_product_"+$product_id).remove();

               removeItemByKeyValue(cart_products,'product_id',$product_id);

               cartTotalCalculate($product_id,'remove');
            }else{
               $removeType = '';
               $product_name = $("#product_name_"+$product_id).html();
               if($ReuiredProductName.length > 0){
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

         }

         if($removeType != ""){
            $calculatePlanSelfId = 'calculatePlanSelf_';
            if($fromTab !== undefined && $fromTab !== ''){
               $calculatePlanSelfId = 'bundlecalculatePlanSelf_';
            }
            if($removeType == "Default"){
               $("#addPlanself_"+$product_id).addClass('addPlanself');
               $("#addPlanself_"+$product_id).removeClass('planSelectedself');
               $("#addPlanself_"+$product_id).removeClass('selected');
               
               $("#"+$calculatePlanSelfId+$product_id).addClass('calculatePlanSelf');
               $("#"+$calculatePlanSelfId+$product_id).removeClass('planSelectedself');
               $("#"+$calculatePlanSelfId+$product_id).removeClass('selected');

               $("#addPlanself_"+$product_id).html('Add Plan');
               if($fromTab !== undefined && $fromTab !== ''){
                  $("#"+$calculatePlanSelfId+$product_id).html('<span class="material-icons-outlined">playlist_add_check</span>');
               }else{
                  $("#"+$calculatePlanSelfId+$product_id).html('Calculate Rate');
               }
            }else if($removeType == "FixedPrice"){
               $("#addPlanself_"+$product_id).addClass('addPlanself');
               $("#addPlanself_"+$product_id).removeClass('planSelectedself');
               $("#addPlanself_"+$product_id).removeClass('selected');
               
               $("#"+$calculatePlanSelfId+$product_id).addClass('calculatePlanSelf');
               $("#"+$calculatePlanSelfId+$product_id).removeClass('planSelectedself');
               $("#"+$calculatePlanSelfId+$product_id).removeClass('selected');

               $("#addPlanself_"+$product_id).html('Add Plan');
               if($fromTab !== undefined && $fromTab !== ''){
                  $("#"+$calculatePlanSelfId+$product_id).html('<span class="material-icons-outlined">playlist_add_check</span>');
               }else{
                  $("#"+$calculatePlanSelfId+$product_id).html('Calculate Rate');
               }
            }else{
               $("#addPlanself_"+$product_id).addClass('addPlanself');
               $("#addPlanself_"+$product_id).removeClass('planSelectedself');
               $("#addPlanself_"+$product_id).removeClass('selected');
               
               $("#"+$calculatePlanSelfId+$product_id).addClass('calculatePlanSelf');
               $("#"+$calculatePlanSelfId+$product_id).removeClass('planSelectedself');
               $("#"+$calculatePlanSelfId+$product_id).removeClass('selected');

               $("#addPlanself_"+$product_id).html('Add Plan');
               if($fromTab !== undefined && $fromTab !== ''){
                  $("#"+$calculatePlanSelfId+$product_id).html('<span class="material-icons-outlined">playlist_add_check</span>');
               }else{
                  $("#"+$calculatePlanSelfId+$product_id).html('Calculate Rate');
               }
            }
         }
         hideShowCart();
         $primary_product_change = true;
         $spouse_product_change=true;
      }

      hideShowCart = function(){
         if(cart_products.length == 0){
            $(".self_guiding_benefits_page_electNone").show();
            $(".self_guiding_benefits_page_elected").hide();
         }
      }

      autoAssignProduct = function($product_id){
         $("#cboxLoadingOverlay").hide();
         $("#cboxLoadingGraphic").hide();
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
               }
            }
         });

         setTimeout(function(){
            $("#cboxLoadingOverlay").hide();
            $("#cboxLoadingGraphic").hide();
         },1000);
         
      }

      productPriceDisplay = function($product_id,$price){
          // $("#calculate_rate_price_"+$product_id).html(parseFloat($price).toFixed(2));
          // calculate price related 
          var extraHtml = '$';
          $("#product_price_label_"+$product_id).html(extraHtml+parseFloat($price).toFixed(2)+'<sub>/ pay period</sub>');
      }

      requiredProduct = function(){
         $("#cboxLoadingOverlay").hide();
         $("#cboxLoadingGraphic").hide();
         $requiredTotal = 0.00;
         if($("#requiredColorboxProductDiv .required_colorbox_product").length > 0){
            $("#requiredColorboxProductDiv .required_colorbox_product").each(function(){
               $product_id = $(this).attr('data-product-id');
               $price = $("#required_colorbox_price_"+$product_id).html();
               $requiredTotal = parseFloat($requiredTotal) + parseFloat($price);
            });
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
               // if($riderProductArr.length > 0){
                     // riderProduct();
               // }
            }
         });
         setTimeout(function(){
            $("#cboxLoadingOverlay").hide();
            $("#cboxLoadingGraphic").hide();
         },2000);
      }

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
         e.preventDefault();
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

      $(document).off("click",".addCalculatePlanSelf");
      $(document).on("click",".addCalculatePlanSelf",function(e){
         e.preventDefault();
         $product_id = $(this).attr('data-product-id');
         $pricing_model = $(this).attr('data-pricing-model');
         $matrix_id = $('#product_benefit_tier_' + $product_id + ' :selected').attr('data-prd-matrix-id');
         addAdditionalQuestions($product_id,$pricing_model,'','addCoverage','',$matrix_id);
      });

      $(document).off("click",".calculatedCoverage");
      $(document).on("click",".calculatedCoverage",function(e){
         e.preventDefault();
         $product_id = $(this).attr('data-product-id');
         $pricing_model = $(this).attr('data-pricing-model');
         $matrix_id = $('#product_benefit_tier_' + $product_id + ' :selected').attr('data-prd-matrix-id');
         $bundleId = $(this).attr('data-bundleid');
         var tab = '';
         if($bundleId != 'undefined'){
            tab = 'recommanded';
            $matrix_id = $('#bundle_product_benefit_tier_'+$bundleId + $product_id + ' :selected').attr('data-prd-matrix-id');
         }
         addAdditionalQuestions($product_id,$pricing_model,'','calculateRate','',$matrix_id,tab,$bundleId);
      });

      $(document).off("click",".cancelPopup");
      $(document).on("click",".cancelPopup",function(e){
         e.preventDefault();
         $("#elected_bundle").val('');
         $popupproduct_id = $(this).data('product-id');
         $popuppricing_model = $(this).data('pricing-model');
         if($(this).data('from-tab') === 'recommanded'){
            $("#bundlecalculatePlanSelf_"+$popupproduct_id).addClass('calculatePlanSelf');
         }else{
            removeProductFromCart($popupproduct_id,$popuppricing_model);
         }
         window.parent.$.colorbox.close();
      });

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

      $(document).off('blur change', '.coverage_tab_input');
      $(document).on('blur change', '.coverage_tab_input', function (e) {
         e.stopPropagation();
         $element_type = $(this).attr('type');
         $element_name = $(this).attr('name');
         $value = $(this).val();

         if($element_type == "radio"){
            e.preventDefault();
            $("#hidden_"+$element_name).val($("#"+$(this).attr('id')).val());
         }else{
             $("#hidden_"+$element_name).val($value);
         }
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

      $(document).off("click","#bob_lead_enrollment_url,#bob_member_enrollment_url");
      $(document).on('click', '#bob_lead_enrollment_url,#bob_member_enrollment_url', function () {
         exit_by_system = true;
         window.location.href = $(this).attr("data-href");
      });

      $(document).off("click","#btn_end_enrollment");
      $(document).on("click","#btn_end_enrollment",function(){
         var reload_url = $(this).attr('data-href');
         if(reload_url == ''){
            reload_page();
         }else{
            window.location.href = reload_url;
         }
      });

      $(document).off("click",".leave_session");
      $(document).on("click",".leave_session",function(e){
         e.preventDefault();
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
         exit_by_system=true;
         window.location.href = reload_url;
      });

      addAdditionalQuestions = function($product_id,$pricing_model,$addType,$submitType,$load_enrollee_data,$matrix_id,$fromTab,$bundleId){

         var paramsArray = $("#frmGroupMemberEnrollment").serializeArray();

         $(".calclate_rate_popup_"+$product_id).hide();
         $(".calclate_rate_popup_"+$product_id).first().show();

         $(".calclate_rate_popup_"+$product_id+" input").each(function(i,v){
            var $input_type = v.type;
            var $input_name = v.name;
            var $input_value = v.value;
            if($input_type == 'hidden'){
               paramsArray.push({name:$input_name,value:$input_value});
            }else if($input_type == 'text'){
               paramsArray.push({name:$input_name,value:$input_value});
            }
         });

         var $params = $.param(paramsArray);
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
            url:'<?= $HOST ?>/ajax_group_enrollment_calculate_rate.php?product='+$product_id+'&pricing_model='+$pricing_model+'&addType='+$addType+'&submitType='+$submitType+'&matrix_id='+$matrix_id+'&accepted='+is_amount_accepted,
            dataType:'JSON',
            data:$params,
            type:'POST',
            async:($load_enrollee_data == true?false:true),
            success:function(res){
               $("#ajax_loader").hide();               
               
               if($submitType == 'displayQuestion'){

                  // $('#inner_calculate_rate_main_div_'+$product_id).html('');
                  if($addType == ""){
                     $('.calclate_rate_popup_'+$product_id).html('');
                     $('#inner_calculate_rate_main_div_'+$product_id).html('');
                  }

                  $main_html = $('#calculate_rate_main_dev').html();
                  $main_html = $main_html.replace(/~product_id~/g,$product_id);
                  $main_html = $main_html.replace(/~from_tab~/g,$fromTab);
                  $main_html = $main_html.replace(/~bundleId~/g,$bundleId);
                  $main_html = $main_html.replace(/~fromTab~/g,$fromTab);

                  if(res.data.product_name != ""){
                     $main_html = $main_html.replace(/~product_name~/g,res.data.product_name);
                     $temp_model = '';
                     if($fromTab !== undefined && $fromTab == 'recommanded'){
                        $temp_model = $("#bundlecalculatePlanSelf_"+$product_id).attr('data-pricing-model');
                     }else{
                        $temp_model = $("#calculatePlanSelf_"+$product_id).attr('data-pricing-model');
                     }
                     $main_html = $main_html.replace(/~pricing-model~/g,$temp_model);
                  }
                  
                  if($addType == ""){
                     $('.calclate_rate_popup_'+$product_id).html($main_html);
                  }

                  if(res.data.data){
                     $.each(res.data.data,function($enrolleeType,$elementArrMain){
                        $.each($elementArrMain,function($elementArrMainKey,$elementArr){
                           $EnrollmentType=$enrolleeType.charAt(0).toUpperCase() + $enrolleeType.slice(1);

                           $count=$(".productQuestionInnerDiv_"+$product_id+"_"+$enrolleeType).length;
                           $number=$count+1;
                           
                           html = $("#productQuestionDynamicDiv").html();
                           html = html.replace(/~product_name~/g,res.data.product_name);

                           html = html.replace(/~Enrollee_Type~/g,$EnrollmentType);
                           html = html.replace(/~enrollee_type~/g,$enrolleeType);
                           html = html.replace(/~product_id~/g,$product_id);
                           html = html.replace(/~number~/g,$number);
                           html = html.replace(/~from_tab~/g,$fromTab);
                           html = html.replace(/~bundleId~/g,$bundleId);
                           
                           if($enrolleeType=="child"){
                              html = html.replace(/~child_counter~/g,$number);
                           }else{
                              html = html.replace(/~child_counter~/g,'');
                           }

                           if($fromTab !== undefined && $fromTab == 'recommanded'){
                              $("#addCalculatedCoverage_"+$product_id).hide();
                           }else{
                              $("#addCalculatedCoverage_"+$product_id).show();
                           }
                           $('#inner_calculate_rate_main_div_'+$product_id).append(html);
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

                                    $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").parent().removeClass('checked');
                                    $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").removeAttr('checked',true);

                                    if($elVal == "" || $elVal==undefined){
                                       $elVal=$("."+$enrolleeType+"_"+$elementName+"_"+$number+":checked").val();
                                    }

                                    if($elVal != '' && $elVal !=undefined){

                                       $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]'][value='"+$elVal+"']").parent().addClass('checked');
                                       $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]'][value='"+$elVal+"']").attr('checked',true);

                                       $("#hidden_"+$enrolleeType+"_"+$product_id+"_"+$number+"_"+$elementName).val($elVal);

                                      
                                       // $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").parent().attr('disabled','disabled');
                                      
                                       $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").prop('disabled',true);
                                    }else{
                                       // $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").parent().removeAttr('disabled');
                                       $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").prop('disabled',false);
                                    }
                                    $("input[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").uniform();
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
                                    // $("[name='"+$enrolleeType+"["+$product_id+"]["+$number+"]["+$elementName+"]']").selectpicker({ 
                                    //       container: 'body', 
                                    //       style:'btn-select',
                                    //       noneSelectedText: '',
                                    //       dropupAuto:false,
                                    //    });
                                 }
                              }
                           });
                        });
                     });

                     if($load_enrollee_data == true){
                        if(typeof(primary_additional_data.fname) !== "undefined") {
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

                     $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                     $("#frmGroupMemberEnrollment .formatPricing").priceFormat({
                        prefix: '',
                        suffix: '',
                        centsSeparator: '.',
                        thousandsSeparator: ',',
                        limit: false,
                        centsLimit: 2,
                    });
                    fRefresh();

                     if(res.data.is_short_term_disability_product == 'Y'){
                        $('.primary_annual_salary_div_' + $product_id).show();
                        $('.primary_monthly_benefit_percentage_div_' + $product_id).show();
                        $('.monthly_benefit_amount_div_' + $product_id).show();

                        $("#annual_salary_input_"+$product_id).removeAttr('readonly');
                        $("#annual_salary_input_"+$product_id).val(res.data.annual_salary).addClass('has-value');
                        $("input[name='primary["+$product_id+"][monthly_benefit_percentage]']").removeAttr('readonly');
                        $("input[name='primary["+$product_id+"][monthly_benefit_percentage]']").val(res.data.salary_percentage);
                        $("input[name='monthly_benefit_amount_"+$product_id+"']").val(res.data.db_monthly_benefit);
                        $("#hidden_monthly_salary_percentage_"+$product_id).val(res.data.salary_percentage);
                        $("#hidden_primary_"+$product_id+"_1_annual_salary").val(res.data.annual_salary);

                        $('.spouse_annual_salary_div_' + $product_id).remove();
                        $('.child_annual_salary_div_' + $product_id).remove();
                        $('.spouse_monthly_benefit_percentage_div_' + $product_id).remove();
                        $('.child_monthly_benefit_percentage_div_' + $product_id).remove();
                        $('.spouse_monthly_benefit_amount_div_' + $product_id).remove();
                        $('.child_monthly_benefit_amount_div_' + $product_id).remove();

                        $('.max_benefit_amount_instruction_' + $product_id).show();
                        $percentage_of_salary = $('.percentage_of_salary_'+$product_id).val();
                        $('.max_benefit_percentage_' + $product_id).text($percentage_of_salary + '%');
                        $monthly_benefit_allowed = $('.monthly_benefit_allowed_'+$product_id).val();
                        $('.max_benefit_amount_' + $product_id).text('$' + $monthly_benefit_allowed);
                        $('.max_percentage_' + $product_id).text($percentage_of_salary + '%');
                     }

                     if(res.data.is_gap_plus_product == 'Y'){
                        resData = res.data;
                        var gp_row = resData.gap_plus_details;
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

                        benefits = JSON.parse(resData.benefitAmountArr);
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
                                    from:0,
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
                     }

                     $percentage = $('.max_benefit_percentage_' + $product_id).first().text();
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

                     if(res.data.adjusted_percentage > 0){
                        $('.rangeslider_' + $product_id).asRange('set', res.data.adjusted_percentage);
                     }

                  }else{
                     if(res.data.is_short_term_disability_product == 'Y'){
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

                  $(".calclate_rate_popup_"+$product_id+" select").removeClass('form-control');
                  $(".calclate_rate_popup_"+$product_id+" select").addClass('form-control');
                  $(".calclate_rate_popup_"+$product_id+" select").selectpicker('destroy');
                  $(".calclate_rate_popup_"+$product_id+" select").selectpicker('refresh');
                  $(".calclate_rate_popup_"+$product_id+" select").selectpicker({ 
                                          container: 'body', 
                                          style:'btn-select',
                                          noneSelectedText: '',
                                          dropupAuto:false,
                                       });
                  $(".calclate_rate_popup_"+$product_id+" select").selectpicker('refresh');
                  common_select();
                  if($addType == ""){
                     $.colorbox({
                        inline:true,
                        href:'.calclate_rate_popup_'+$product_id,
                        iframe: false,
                        html:true,
                        width: '768px',
                        height: '500px',
                        overlayClose: false,
                        closeButton:false,
                     });
                  }
                  // $('.radio_btn_refresh').uniform();
               }else{
                  if($fromTab !== undefined && $fromTab == 'recommanded'){
                     $productPrice = $displayProductPrice = 0;
                     $("#bundle_product_price_"+$bundleId+$product_id).val('0.00');
                  }else{
                     $productPrice = $("#product_price_"+$product_id).val();
                     $displayProductPrice = $("#product_price_"+$product_id).val();
                  }
                  
                  $matrix_id_arr= [];
                  if(res.data.enrollee){
                     $.each(res.data.enrollee,function($enrolleeType,$priceArr){
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

                     if($fromTab !== undefined && $fromTab == 'recommanded'){
                        $("#bundle_product_benefit_tier_"+$bundleId+$product_id).find('option:selected').data('display-price',$displayProductPrice);
                        $("#bundle_added_product").val($product_id);
                        $("#bundle_product_benefit_tier_"+$bundleId+$product_id).find('option:selected').attr('data-price',$productPrice);
                     }else{
                        $("#product_benefit_tier_"+$product_id).find('option:selected').data('display-price',$displayProductPrice);
                        $("#added_product").val($product_id);
                        $("#product_benefit_tier_"+$product_id).find('option:selected').attr('data-price',$productPrice);
                     }

                     if($fromTab !== undefined && $fromTab == 'recommanded'){
                        calculatedPrice($product_id,$productPrice,$matrix_id_arr,$pricing_model,$displayProductPrice,$bundleId);
                        // window.parent.$.colorbox.close();
                     }else{
                        if($submitType=="addCoverage"){
                           addToCart($product_id,$productPrice,$matrix_id_arr,$pricing_model,$displayProductPrice);
                           window.parent.$.colorbox.close();
                        }
                     }
                     productPriceDisplay($product_id,$displayProductPrice);
                  }else{
                     if(res.data.error_display){
                        $("#error_add_coverage_"+$product_id).html(res.data.error_display);
                        if(res.data.amount_limit_error === true){
                           if(!res.data.adjusted_member_price){
                              $("#error_add_coverage_"+$product_id).append("<br>Please enter valid annual salary");
                           }else{
                              swal({
                                 text: res.data.amount_limit_error_text,
                                 showCancelButton: true,
                                 confirmButtonText: "Accept"
                              }).then(function () {
                                 is_amount_accepted = 'Y';
                                 calculateSTDRate(res.data.annual_salary,0,$product_id,res.data.monthly_benefit_allowed);
                                 
                                 is_amount_accepted = 'N';

                                 $displayProductPrice = parseFloat(res.data.adjusted_member_price);

                                 $("#calculate_rate_price_"+$product_id).html(parseFloat($displayProductPrice).toFixed(2));
                                 $("#calculate_rate_price_"+$product_id+"_Primary_1").html(parseFloat($displayProductPrice).toFixed(2));

                                 var extraHtml = '$';
                                 if($(".pricing_model_"+$product_id).first().val() !== 'FixedPrice'){
                                    extraHtml = '<sub>Starting at </sub>$';
                                 }
                                 
                                 $("#product_price_label_"+$product_id).html(extraHtml+parseFloat($displayProductPrice).toFixed(2));
                                 $("#error_add_coverage_"+$product_id).html('');
                              }, function (dismiss) {

                              });
                           }
                        }
                     }else{
                        $("#error_add_coverage_"+$product_id).html('Criteria selection required for rates and plan');
                     }
                  }
                  bundleTotal($bundleId);
               }
            }
         });
      }

      addOnProducts = function(){
         if($("#frmGroupMemberEnrollment").find($("#categoryDivaddOnCategory")).length > 0){
            if($("#customer_id").val() > 0 || $(".planSelectedself[data-is-add-on-product='N']").length > 0) {
               $("#categoryaddOnCategory").parent('.selfGuidingBenefitsMenu').show();
               if(is_add_product == 0 && $(".planSelectedself[data-is-add-on-product='N']").length == 0){
                  if($(".planSelectedself[data-is-add-on-product='Y']").length > 0){
                     $(".planSelectedself[data-is-add-on-product='Y']").each(function($k,$v){
                        $product_id = $(this).attr('data-product-id');
                        removeProductFromCart($product_id,'Default');
                     });
                     $("#categoryaddOnCategory").parent('.selfGuidingBenefitsMenu').hide();
                  }
               }
            }else{
               if($(".planSelectedself[data-is-add-on-product='Y']").length > 0){
                  $(".planSelectedself[data-is-add-on-product='Y']").each(function($k,$v){
                     $product_id = $(this).attr('data-product-id');
                     removeProductFromCart($product_id,'Default');
                  });
               }
               $("#categoryaddOnCategory").parent('.selfGuidingBenefitsMenu').hide();
            }

         }
      }

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

      calculatedPrice = function(product_id,productPrice,matrix_id_arr,pricing_model,displayProductPrice,bundleId){
         var price = parseFloat(productPrice).toFixed(2);
         $("#prices"+bundleId+product_id).html('$'+parseFloat(displayProductPrice).toFixed(2)+'<sub>/ pay period</sub>');
         $("#benefit_tierName_"+bundleId+"_"+product_id).val(price);
         $("#bundle_product_price_"+bundleId+product_id).val(productPrice);
         $("#bundle_display_product_price_"+bundleId+product_id).val(displayProductPrice);
         $("#bundle_product_benefit_tier_"+bundleId+product_id).find('option:selected').data('display-price',displayProductPrice);
         $("#bundle_product_matrix_"+bundleId+product_id).val(matrix_id_arr);
         $("#bundle_added_product").val(product_id);
         $("#bundle_product_benefit_tier_"+bundleId+product_id).find('option:selected').attr('data-price',productPrice);
         $("#bundle_product_is_calculated_"+bundleId+"_"+product_id).val('Y');

         bundleTotal(bundleId);
      }

      changeBundleClass = function(product_id){
         var bundleId = $("#elected_bundle").val();
         if(cart_products.length == 0 && bundleId !== ''){
            var tempElectedBundle = bundleId;
            $('.bundleblock').show();
            $('#changecolor'+tempElectedBundle).removeClass('elect-bundle');
            $('#icon'+tempElectedBundle).addClass('');
            $('.selecte'+tempElectedBundle).hide();
            $(".removeplan").each(function(){
               var productId = $(this).data("productid");
               $('#removeproduct'+productId).show();
               removeBundleProduct(productId);
            });
            $("electbundle"+bundleId).addClass("electbundle");
            $("electbundle"+bundleId).removeClass("electbundlegreen");
            // $(".bundle_page_total_amount").text("00.00");
            $("#elect_bundle_text"+tempElectedBundle).text('ELECT BUNDLE');
            $(".bundle_page_electNone").show();
            $(".bundle_page_elected").hide();
         }else{
            removeBundleProduct(product_id);
         }
      }

      removeBundleProduct = function (product_id){
        
         var tempElectedBundle = $("#elected_bundle").val();
         $(".removep"+tempElectedBundle+"_"+product_id).prop('checked',true);
         $(".removep"+tempElectedBundle+"_"+product_id).uniform();
         // var bundlePageTotal =  parseFloat($(".bundle_page_total_amount").text()).toFixed(2);
         var bundleTotalTmp = 0;
         $.each($('.removeplan'),function(){
            if(tempElectedBundle == $(this).attr('data-bundleid') && $(this).is(':checked') && $(this).data('prdid') == product_id){
               var currentPlanPrice = !isNaN($('#bundle_display_product_price_'+tempElectedBundle+product_id).val()) && $('#bundle_display_product_price_'+tempElectedBundle+product_id).val() !== '' ? $('#bundle_display_product_price_'+tempElectedBundle+product_id).val() : 0; 
               bundleTotalTmp += currentPlanPrice;
               // bundlePageTotal = parseFloat(bundlePageTotal).toFixed(2) - parseFloat(currentPlanPrice).toFixed(2);
            }
         });
         $("#bundleTotal_"+tempElectedBundle).text(parseFloat(bundleTotalTmp).toFixed(2)+'/ pay period');
         // $(".bundle_page_total_amount").text(parseFloat(bundlePageTotal).toFixed(2));
         $('#removedbundle'+tempElectedBundle+'_'+product_id).show();
         $('#removeproduct'+tempElectedBundle+'_'+product_id).hide();
         removeItemByKeyValue(cart_products,'product_id',product_id);
         remove_product_array.push({name:tempElectedBundle , value: product_id});

         var allBundlePrdRemove = true;
         $(".prd_benefit_tier"+tempElectedBundle).each(function(){
            var productId =  $(this).data("product-id");
            var checkplan = $('.removep'+tempElectedBundle+'_'+productId).is(':checked');
            if(!checkplan && productId!==undefined){
               allBundlePrdRemove = false;
            }
         });
         if(allBundlePrdRemove){
            $('.electbundlegreen').click();
         }
      }

      colorBoxClose = function() {
         window.parent.$.colorbox.close();
      }

   function reload_page(open_new_enrollment) {
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

   function update_primary_benefit_amount($product_id,$benefit_amount) {
        $benefit_amount = parseF($benefit_amount);
        $('input[name="hidden_primary['+$product_id+'][1][benefit_amount]"]').val($benefit_amount);
        $('select[name="primary['+$product_id+'][1][benefit_amount]"]').html('<option value="'+$benefit_amount+'" selected>'+$benefit_amount+'</option>');
        $('select[name="primary['+$product_id+'][1][benefit_amount]"]').removeAttr('disabled');
        return true;
    }

   function checkSelfGuidingAddedProduct($category_id,$textLine = false){
      if($('.planSelectedself[data-category-id="'+ $category_id +'"]').length > 0){
        $("#selfGuidingBenefitsMenu"+$category_id).addClass('completed');
        $("#selfGuidingBenefitsMenu"+$category_id).removeClass('line');
      }else{
        $("#selfGuidingBenefitsMenu"+$category_id).removeClass('completed');
        if($textLine){
            $("#selfGuidingBenefitsMenu"+$category_id).addClass('line');
        }
      }
   }

   function get_enrollment_verification_status($sent_via,$sent_to_member,$id,$enrollmentLocation,$is_add_product,$show_loader){
        if(typeof($show_loader) === "undefined") {
            $show_loader = false;
        }
        if($show_loader==true) {
            $("#ajax_loader").show();
        }
      $.ajax({
         url: '<?= $HOST ?>/ajax_api_call.php',
         data: {
            sent_via:$sent_to_member,
            id:$id,
            enrollmentLocation:$enrollmentLocation,
            is_add_product:$is_add_product,
            site_user_name:$("#site_user_name").val(),
            'api_key' : 'checkVerificationStatus',
         },
         type: 'POST',
         dataType: 'json',
         success: function (res) {
            if($show_loader==true) {
                $("#ajax_loader").hide();
            }
               if(res.status == 'success'){
                    if(res.is_enrollment_complete == true) {
                        $("#confirmationTab").removeClass('disabled');
                        $("#confirmationTab").addClass('active');
                        $("#confirmationTab").addClass('completed');
                    }
                  $('.verification_track_div').html(res.html);
               }else{
                  setNotifyError(res.message);
                  return false;
               }
         },
      });

      $verification_running = true;

      if(add_refresh_request !== true){
         setTimeout(function(){
            get_enrollment_verification_status($sent_via,$sent_to_member,$id,enrollmentLocation,$is_add_product);
         },15000);
      }
      add_refresh_request = false;
   }
   /* Self Guided benefit js ends */

   function addedproducttoCart(){
      var $tempFromStep = $("#fromStep").val();
      var $tempStep = $("#step").val();
      if(cart_products.length > 0){
         $("#ajax_loader").show();
         $("#fromStep").val('selfGuiding');
         $.each(cart_products,function(key,value){
            var extraHtml = '$';
            if($(".pricing_model_"+value['product_id']).first().val() !== 'FixedPrice'){
               extraHtml = '<sub>Starting at </sub>$';
            }
            $("#product_price_label_" + value['product_id']).html(extraHtml+parseFloat(value['display_price']).toFixed(2) + '<sub class="fs12" style="bottom:0px;">/ pay period</sub>');
            $("#product_benefit_tier_"+value['product_id']).val(value['prd_plan_type_id']);
            $("#product_benefit_tier_"+value['product_id']).selectpicker('setStyle', 'btn-select');
            addToCart(value['product_id'],value['price'],value['matrix_id'],value['pricing_model'],value['display_price']);
            $("#fromStep").val($tempFromStep);
            $("#step").val($tempStep);
         });
      }
      $("#fromStep").val($tempFromStep);
      $("#step").val($tempStep);
   }

   function showProductDescriptionPopup($product_id){
      $prd_desc = $(".tmp_prd_desc_" + $product_id).html();
      $aMyUTF8Output = base64DecToArr($prd_desc);
      $details = UTF8ArrToStr($aMyUTF8Output);

      $href = $(this).attr('data-href');
      var not_win = window.open("", "myWindow"+$product_id, "width=1024,height=767");
      not_win.document.body.innerHTML = $details;
   }

   function callCalculateTakeHomePay(){
      $("#ajax_loader").show();
      var nextCall = 1000;
      if(calledTakeHomePay){
         calledTakeHomePay = false;
         nextCall = 2000;
      }
      setTimeout(function(){
         if(!calledTakeHomePay){
            calculateTakeHomePay();
            calledTakeHomePay = true;
         }
         $("#ajax_loader").hide();
      },nextCall);
   }
</script>