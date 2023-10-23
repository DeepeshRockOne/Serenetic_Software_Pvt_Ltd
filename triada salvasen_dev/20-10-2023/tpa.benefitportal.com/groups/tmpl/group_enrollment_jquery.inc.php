<script type="text/javascript">
   //*************** General Code Start ***************************
      var wrapper = document.getElementById("signature-pad"),
        clearButton = wrapper.querySelector("[data-action=clear]"),
        savePNGButton = wrapper.querySelector("[data-action=save-png]"),
        saveSVGButton = wrapper.querySelector("[data-action=save-svg]"),
        canvas = wrapper.querySelector("canvas"),
        signaturePad;

        setInterval(function() {
            $step = $(".data_tab li.active a").attr('data-step');
            autoSaveForm($step);
        }, 60000);

        var $site_location = '<?= $SITE_ENV ?>';

        var placeSearch, autocomplete,billingAutocomplete;

   //*************** General Code End ***************************

   $(document).ready(function() {
      checkEmail();
      $('.groups_add_company').colorbox({});
      //******************** details tab Code start *******************************
         $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
         $(".ein_mask").inputmask({"mask": "99-9999999",'showMaskOnHover': false});

        
         
         $("#exit_business_img").click(function () {
            $("#enrollment_profile .profile-dropzone").click();
         });

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
                   });

               }, thumbnail: function (file, dataUrl) {
                   if (this.options.customErrorHandlingCode == 200) {
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
                           url: 'ajax_manage_brand_icon.php?id=<?= $group_id ?>&is_enroll=Y',
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
      //******************** details tab Code end   *******************************
      
      //******************** Billing tab Code Start *******************************
        $group_company = '<?= $group_company ?>';

        if($group_company =='Y'){
          load_group_company();
        }
        $('#expiration').datepicker({
          format: 'mm/yy',
          startView : 1,
          minViewMode: 1,
          autoclose: true,  
          startDate:new Date(),
          endDate : '+15y'
        });
      //******************** Billing tab Code end   *******************************


      //******************** Form Submit tab Code start *******************************
         $('#enrollment_form').ajaxForm({
             beforeSend: function () {
                 $("#ajax_loader").show();
             },
             dataType: 'json',
             success: function (res) {                
                 $("#ajax_loader").hide();
                 
                 if(res.status=="success"){
                     if(res.submit_type=="continue"){

                        $(".data_tab li.active").addClass("completed");
                        $(".data_tab li.active").next().find("a").trigger("click");
                        $(".data_tab li.active").removeClass("disabled");
                        $('html, body').animate({
                           scrollTop: $('.data_tab').offset().top-100
                        }, 1000);
                        
                        if(res.step==2){
                           setTimeout(function(){
                              resizeCanvas();
                              signaturePad = new SignaturePad(canvas);
                           }, 1000);
                           
                        }
                     }else if(res.submit_type=="auto_save"){
                      setNotifySuccess("Documents and details information saved successfully!");
                     }
                 }else if (res.status == 'account_approved') {
                     window.location.href = "<?=$GROUP_HOST?>";
                 } else if (res.status == 'fail') {
                     if (res.div_step_error.length) {
                       if (!$('#' + res.div_step_error).is(":visible"))
                         $("[href='#" + res.div_step_error + "']").click();
                     }
                     var is_error = true;
                     $.each(res.errors, function (index, error) {
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
                     });
                 } 
             },
             error: function () {
                 alert('Due to some technical error file couldn\'t uploaded.');
             }
         });
      //******************** Form Submit tab Code end   *******************************
   });
   
   //******************** details tab Code start ******************************* 
      $(document).on('focus','#business_address,#zipcode',function(){
         $("#is_address_ajaxed").val(1);
      });

      function updateAddress(){
           $.ajax({
              url : "ajax_group_enrollment.php",
              type : 'POST',
              data:$("#enrollment_form").serialize(),
              dataType:'json',
              beforeSend :function(e){
                 $("#ajax_loader").show();
                 $(".error").html('');
              },success(res){
                 $("#is_address_ajaxed").val("");
                 $("#is_bill_address_ajaxed").val("");
                 $("#ajax_loader").hide();
                 $(".suggested_address_box").uniform();
                 if(res.zip_response_status =="success"){
                    if(res.type == 'billing'){
                      $("#bill_state").val(res.state).addClass('has-value');
                      $("#bill_city").val(res.city).addClass('has-value');
                    }else{
                      $("#state").val(res.state).addClass('has-value');
                      $("#city").val(res.city).addClass('has-value');
                    }
                    $("#is_address_verified").val('N');
                    $("#enrollment_form").submit();
                 }else if(res.address_response_status =="success"){
                    $(".suggestedAddressEnteredName").html($("#group_name").val());
                    if(res.type == 'billing'){
                      $("#bill_state").val(res.state).addClass('has-value');
                      $("#bill_city").val(res.city).addClass('has-value');
                      $("#is_valid_billing_address").val('Y');
                    }else{
                      $("#state").val(res.state).addClass('has-value');
                      $("#city").val(res.city).addClass('has-value');
                      $("#is_valid_address").val('Y');
                    }
                    $(".suggestedAddressEntered").html(res.enteredAddress);
                    $(".suggestedAddressAPI").html(res.suggestedAddress);
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
                                if(res.type == 'billing'){
                                  $("#bill_address").val(res.address).addClass('has-value');
                                  $("#bill_address_2").val(res.address2).addClass('has-value');
                                }else{
                                  $("#business_address").val(res.address).addClass('has-value');
                                  $("#business_address_2").val(res.address2).addClass('has-value');
                                }
                                $("#is_address_verified").val('Y');
                             }else{
                                $("#is_address_verified").val('N');
                             }
                             $("#enrollment_form").submit();
                          },
                    });
                 }else if(res.status == 'success'){
                    $("#is_address_verified").val('N');
                    $("#enrollment_form").submit();
                 }else{
                    $.each(res.errors,function(index,error){
                       $("#error_"+index).html(error).show();
                   });
                 }
                 $("#state").selectpicker('refresh');
                 $("#bill_state").selectpicker('refresh');
              }
           });
        }
      function initAutocomplete() {              

          var input = document.getElementById('business_address');
          var options = {
              types: ['geocode'],
              componentRestrictions: {country: 'us'}
          };

          autocomplete = new google.maps.places.Autocomplete(input, options);

          autocomplete.setFields(['address_component']);

          autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        $("#is_valid_address").val('N');
        var place = autocomplete.getPlace();
        var address = "";
        var zip = "";
        var state = "";
        var city = "";
        var defaultZip = $("#zipcode").val();
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
          }else if(addressType=="administrative_area_level_1"){
            state = place.address_components[i]["long_name"];
          }
        }

        if(zip != ''){
          $("#zipcode").val(zip);
          $("#city").val(city);
          $('#state option').filter(function() { return $.trim( $(this).text() ) == state; }).attr('selected','selected');
          $("#state").selectpicker('refresh');
          fRefresh();
        }
        $("#business_address").val(address);
        $("#business_address").addClass('has-value');
        $("#is_valid_address").val('Y');
        
      }
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
      $(document).off("change","#nature_of_business");
      $(document).on("change","#nature_of_business",function(){
          getSICCode();
      });

      $(document).on('focusin click keyup', '#username', function() {
          $('#username_info').show();
          var user_name = $(this).val();
          var user_email = $('#email').val();
          var group_id = $("#group_id").val();
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
                  url: "<?= $GROUP_HOST ?>/check_already_username.php",
                  data: {
                      username: user_name,
                      email: user_email,
                      group_id: group_id
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
   //******************** details tab Code end   *******************************
   
   //******************** billing tab Code start *******************************
      
      $(document).on('focus','#bill_address,#bill_zip',function(){
         $("#is_bill_address_ajaxed").val(1);
      });

      function initBillingAutocomplete() {              

          var input = document.getElementById('bill_address');
          var options = {
              types: ['geocode'],
              componentRestrictions: {country: 'us'}
          };

          billingAutocomplete = new google.maps.places.Autocomplete(input, options);

          billingAutocomplete.setFields(['address_component']);

          billingAutocomplete.addListener('place_changed', fillInBillingAddress);
      }

      function fillInBillingAddress() {
        $("#is_valid_billing_address").val('N');
        var place = billingAutocomplete.getPlace();
        var address = "";
        var zip = "";
        var state = "";
        var city = "";
        var defaultZip = $("#bill_zip").val();
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
          }else if(addressType=="administrative_area_level_1"){
            state = place.address_components[i]["long_name"];
          }
        }

        if(zip != ''){
          $("#bill_zip").val(zip);
          $("#bill_city").val(city);
          $('#bill_state option').filter(function() { return $.trim( $(this).text() ) == state; }).attr('selected','selected');
          $("#bill_state").selectpicker('refresh');
          fRefresh();
        }
        $("#bill_address").val(address);
        $("#is_valid_billing_address").val('Y');
        
      }
      $(document).on("click","#add_group_company",function(){
         $group_id = $("#group_id").val();
         $.colorbox({
            href:'groups_add_company.php?group_id='+$group_id,
            iframe: true, 
            width: '768px', 
            height: '550px',
            onClosed:function(){
               load_group_company();
            }
         })
      });
      $(document).on("click",".edit_group_company",function(){
         $id = $(this).attr('data-id');
         $group_id = $("#group_id").val();
         $.colorbox({
            href:'groups_add_company.php?id='+$id+'&group_id='+$group_id,
            iframe: true, 
            width: '768px', 
            height: '550px',
            onClosed:function(){
               load_group_company();
            }
         })
      });
      $(document).off("click",".delete_group_company");
      $(document).on("click",".delete_group_company",function(e){
          e.preventDefault();
          $id = $(this).attr('data-id');
          $group_id = $("#group_id").val();
          swal({
            text: "Delete Record: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
          }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                url:'ajax_delete_group_company.php',
                dataType:'JSON',
                data:{id:$id,group_id:$group_id},
                type:'POST',
                success:function(res){
                  $("#ajax_loader").hide();

                  if(res.status == "success"){
                      setNotifySuccess(res.msg);
                      load_group_company();
                  }
                }
            })
          }, function (dismiss) {
          });
      });
      $(document).on("change","input[name=group_company]",function(){
         $val=$(this).val();
         $("#group_company_div").hide();
         if($val=='Y'){
            $("#group_company_div").show();
            load_group_company();
         }
      });

      $(document).on("change","#billing_type",function(){
         $val=$(this).val();
         $("#list_bill_div").hide();
         if($val=='list_bill'){
            $("#list_bill_div").show();
         }
      });
      $(document).on("change","input[name=payment_type]",function(){
         $val=$(this).val();
         $("#achDiv").hide();
         $("#CCDiv").hide();

         if($val=='ACH'){
            $("#achDiv").show();
         }else if($val == "CC"){
             $("#CCDiv").show();
         }
      });
   //******************** billing tab Code end   *******************************
   
   //******************** Button Click Code start *******************************
      $(document).off("click",".next_tab_button");
      $(document).on("click",".next_tab_button",function(){
         $step=$(this).attr('data-step');
         $("#dataStep").val($step);
         $("#submit_type").val('continue');
         $("#action").val('continue_application');
         $('.error ').html('');

         if($step==3){
            if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
              $("#hdn_signature_data").val(signaturePad.toDataURL());
            }
         }
         $is_address_ajaxed = $("#is_address_ajaxed").val();
         $is_bill_address_ajaxed = $("#is_bill_address_ajaxed").val();
         if($is_address_ajaxed == 1 || $is_bill_address_ajaxed == 1){
            updateAddress();
         }else{
            $("#enrollment_form").submit();
         }
      });

      $(document).off("click",".cancel_tab_button");
      $(document).on("click",".cancel_tab_button",function(){
         window.location.reload();   
      });
   //******************** Button Click Code end   *******************************
   
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

   function delete_brand_icon() {
       if (confirm('Are you sure you want to delete logo?')) {
           $.ajax({
               url: 'ajax_delete_brand_icon.php?id=<?=$group_id?>',
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
    function getSICCode(){
      var $business_Id = $("#nature_of_business").val();
      if($business_Id != ''){
         $.ajax({
          url: '<?=$HOST?>/ajax_get_sic_code.php',
            data: {business_id: $business_Id},
            dataType: 'JSON',
            type: 'POST',
            success: function (res) {
              $("#sic_code").html(res.data);
              $("#sic_code").selectpicker('refresh');
            }
        });
      }else{
         $("#sic_code").html('<option value=""></option>');
         $("#sic_code").selectpicker('refresh');
        
      }
    }
    scrollToElement = function(e) {
       add_scroll = 0;
       element_id = $(e).attr('id');
       if("both_button" == element_id)
           add_scroll = 50;
       var offset = $(e).offset();
       var offsetTop = offset.top;
       var totalScroll = offsetTop - 200 + add_scroll;
       $('body,html').animate({
           scrollTop: totalScroll
       }, 1200);

   }

   load_group_company = function(){
      $("#ajax_loader").show();
      $group_id = $("#group_id").val();
      $.ajax({
         url:'<?= $GROUP_HOST ?>/ajax_load_group_company.php',
         dataType:'JSON',
         data:{group_id:$group_id},
         type:'POST',
         success: function(res) {
            $('#ajax_loader').hide();
            $("#groupCompanyDate").html(res.html);
            $("#company_count").val(res.company_count);
         }
       });
   }

  autoSaveForm = function($step){
    $("#dataStep").val($step);
    $("#submit_type").val('auto_save');
    $("#action").val('auto_save');
    $('.error ').html('');

    if($step==3){
        if ((typeof signaturePad != 'undefined') && (!(signaturePad.isEmpty()))) {
          $("#hdn_signature_data").val(signaturePad.toDataURL());
        }
    }
    $("#enrollment_form").submit();
  }
</script>