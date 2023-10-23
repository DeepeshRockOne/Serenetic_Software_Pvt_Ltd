<div class="panel panel-default panel-block panel-space">
   <form id="vendor_frm" name="vendor_frm" method="POST" class="" enctype="multipart/form-data">
      <input type="hidden" name="is_valid_address" id="is_valid_address" value="Y">
      <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
      <div class="panel-heading">
         <div class="panel-title">
            <p class="fs16 mn"><strong class="fw500">Vendor Settings</strong></p>
         </div>
      </div>
      <div class="panel-body theme-form">
         <div class="row">
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="name" id="name" class="form-control" value="<?= checkIsset($name); ?>"/>
                  <label>Vendor Name</label>
                  <p class="error" id="error_name"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="display_id" id="display_id" class="form-control" value="<?= checkIsset($display_id); ?>" />
                  <label class="label-wrap">Vendor ID (Must be Unique, ex. V1234567)</label>
                  <p class="error" id="error_display_id"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" name="contact_fname" id="contact_fname" class="form-control" value="<?= checkIsset($contact_fname); ?>" />
                  <label>Contact Name</label>
                  <p class="error" id="error_contact_fname"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" name="phone" id="phone" class="form-control phone_mask" value="<?= checkIsset($phone); ?>" />
                  <label>Phone Number</label>
                  <p class="error" id="error_phone"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" name="email" id="email" class="form-control no_space" value="<?= checkIsset($email); ?>" />
                  <label>Email</label>
                  <p class="error" id="error_email"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="address" id="address" class="form-control" value="<?= checkIsset($address); ?>" />
                  <label>Address</label>
                  <p class="error" id="error_address"></p>
                  <input type="hidden" name="old_address" id="old_address" value="<?= checkIsset($address); ?>">
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="address2" id="address2" class="form-control" value="<?= checkIsset($address2); ?>" onkeypress="return block_special_char(event)" />
                  <label>Address 2 (suite, apt)</label>
                  <p class="error" id="error_address2"></p>
               </div>
            </div>
            <div class="col-md-2 col-sm-6">
               <div class="form-group">
                  <input type="text" name="city" id="city" class="form-control" value="<?= checkIsset($city); ?>" />
                  <label>City</label>
                  <p class="error" id="error_city"></p>
               </div>
            </div>
            <div class="col-md-2 col-sm-4">
               <div class="form-group">
                  <select class="form-control" name="state" id="state">
                     <option value=""> </option>
                     <?php if(!empty($allStateRes)){ ?>
                     <?php foreach ($allStateRes as $stateRow) { ?> 
                     <option value="<?= $stateRow['name'] ?>" <?= (!empty($state) && $state == $stateRow['name']) ? "selected" : '' ?> ><?= $stateRow['name'] ?></option>
                     <?php } ?>
                     <?php } ?>
                  </select>
                  <label>State</label>
                  <p class="error" id="error_state"></p>
               </div>
            </div>
            <div class="col-md-2 col-sm-4">
               <div class="form-group">
                  <input type="text" name="zipcode" id="zipcode" class="form-control" value="<?= checkIsset($zipcode); ?>" />
                  <label>Zip Code</label>
                  <p class="error" id="error_zipcode"></p>
                  <input type="hidden" name="old_zipcode" id="old_zipcode" value="<?= checkIsset($zipcode); ?>">
               </div>
            </div>
            <div class="col-md-4 col-sm-4">
               <div class="form-group">
                  <input type="text" name="taxid" id="taxid" class="form-control" value="<?= checkIsset($taxid); ?>" />
                  <label>Tax ID</label>
                  <p class="error" id="error_taxid"></p>
               </div>
            </div>
            <div class="clearfix"></div>
            <div id="file_div_inner">
               <div class="col-md-4 col-sm-6">
                  <div class="form-group height_auto">
                     <div class="phone-control-wrap">
                        <div class="phone-addon">
                           <div class="custom_drag_control">
                              <span class="btn btn-action" style="border-radius:0px;">Upload Document</span>
                              <input type="file" class="gui-file" id="attachements" name="vendor_attachements[]" multiple>
                              <input type="text" class="gui-input" placeholder="Attach Document">
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php if(!empty($attachmentRow)){ ?>
                  <?php foreach ($attachmentRow as $key => $row) { ?>
                  <?php
                     $imageExt=array_reverse(explode(".", $row['file_name']));
                     $is_image=false;
                     if(strtolower($imageExt[0])=="jpg" || strtolower($imageExt[0])=="jpeg" || strtolower($imageExt[0])=="png" || strtolower($imageExt[0])=="gif" || strtolower($imageExt[0])=="tif"){ $is_image=true; }
                     ?>
                  <div class="form-group height_auto" id="attachment_file_div_<?= $row['id'] ?>">
                     <div class="phone-control-wrap">
                        <div class="phone-addon">
                           <input type="text" name="" placeholder="<?= $row['file_name'] ?>" class="form-control" readonly="readonly">
                        </div>
                        <div class="phone-addon w-90" class="">
                           <a href="<?= $FEES_ATTACHMENS_WEB.$row['file_name'] ?>" download class="btn text-blue fs20"> <i class="fa fa-download"></i></a>
                           <a href="javascript:void(0)" data-id="<?= $row['id'] ?>" class="btn red-link fs20 delete_attachment"><i class="fa fa-trash"></i></a>
                        </div>
                     </div>
                  </div>
                  <?php } ?>
                  <?php } ?>
               </div>
            </div>
         </div>
         <div class="row" id="attachements_inner_div"></div>
         <div id="fee_table">
            <p class="fs16  m-b-20"><strong class="fw500">Fees</strong></p>
            <div class="clearfix"></div>
            <div id="vendor_fee_div">
            </div>
            <p class="error" id="error_vendor_fee_id"></p>
         </div>
         <div class="step_btn_wrap m-t-30 text-right">
            <input type="button" name="add_vendor" id="add_vendor" class="btn btn-action" value="Save Vendor">
            <input type="button" name="" id="" class="btn red-link" value="Cancel" onclick="window.location='vendors.php'"> 
            <input type="hidden" name="is_clone" id="is_clone" value="<?= $is_clone ?>">
            <input type="hidden" name="vendor_id" id="vendor_id" value="<?= $vendor_id ?>">
            <input type="hidden" name="vendor_fee_id" id="vendor_fee_id" value="<?= $vendor_fee_id ?>">
            <input type="hidden" name="vendor_attachment_id" id="vendor_attachment_id" value="<?= $vendor_attachment_id ?>">
            <input type="hidden" name="upload_type" id="upload_type" value="">
         </div>
      </div>
   </form>
</div>
<div  id="attachements_dynamic_div" style="display: none">
   <div class="col-sm-4">
      <div class="form-group height_auto" id="attachment_file_div_~file_id~">
         <div class="phone-control-wrap">
            <div class="phone-addon">
               <input type="text" name="" placeholder="~file_name~" class="form-control" readonly="readonly">
            </div>
            <div class="phone-addon w-90" class="">
               <a href="<?= $FEES_ATTACHMENS_WEB ?>~file_name~" download class="btn text-blue fs20"> <i class="fa fa-download"></i></a>
               <a href="javascript:void(0)" data-id="~file_id~" class="btn red-link fs20 delete_attachment"><i class="fa fa-trash"></i></a>
            </div>
         </div>
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
    checkEmail();
    common_select();
       $file_div_inner=$("#file_div_inner").html();
       $("#phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    
       load_vendor_fee_div();
   
       var auto_complete_address = {
           autoFocus: true,
           source: function(request, response) {
               $.ajax({
                   url: "<?= $ADMIN_HOST ?>/ajax_get_auto_complete_data.php?action=getaddress",
                   type: "POST",
                   dataType: "json",
                   data: {
                       query: request.term
                   },
                   success: function(data) {
                       response(data);
                   }
               });
           },
           minLength: 2, 
       };
   
       $("#address").autocomplete(auto_complete_address);
   
        
       $('#vendor_frm').ajaxForm({
           beforeSubmit: function(arr, $form, options) {
               $("#ajax_loader").show();
           },
           url: '<?= $ADMIN_HOST ?>/ajax_manage_vendor.php',
           type: 'POST',
           dataType: 'json',
           success: function(res) {
               $("#ajax_loader").hide();
               $('.error').html('');
               if (res.status == "success") { 
                   setNotifySuccess(res.message);
                   setTimeout(function(){   
                       window.location.href = res.redirect_url;
                   }, 1500);
               } else if (res.status == "success_file") {
                   $("#vendor_attachment_id").val(res.attachment);
                   if (res.files_info.length > 0) {
                       $.each(res.files_info, function($k, $v) {
                           $html_append = $('#attachements_dynamic_div').html();
                           $html_append = $html_append.replace(/~file_name~/g, $v.file_name);
                           $html_append = $html_append.replace(/~file_display_name~/g, $v.file_display_name);
                           $html_append = $html_append.replace(/~file_id~/g, $v.file_id);
                           $('#attachements_inner_div').append($html_append);
                       });
                   }
                   $("#file_div_inner").html($file_div_inner);
                   setNotifySuccess(res.message);
               } else {
                   var is_error = true;
                   $.each(res.errors, function(index, error) {
                       $('#error_' + index).html(error);
                       if (is_error) {
                           var offset = $('#error_' + index).offset();
                           if (typeof(offset) === "undefined") {
                               console.log("Not found : " + index);
                           } else {
                               var offsetTop = offset.top;
                               var totalScroll = offsetTop - 195;
                               $('body,html').animate({
                                   scrollTop: totalScroll
                               }, 1200);
                               is_error = false;
                           }
                       }
                   });
               }
           }
       });
   
   });
   
   $(document).on('focus','#address,#zipcode',function(){
    $('#is_address_ajaxed').val(1);
   });

   $(document).off('click', '.add_vendor_fee');
   $(document).on('click', '.add_vendor_fee', function(e) {
       e.preventDefault();
       $.colorbox({
           href: $(this).attr('href'),
           iframe: true,
           width: '855px',
           height: '600px'
       })
   });
   
   $(document).off('click', '.vendor_productsr_details');
   $(document).on('click', '.vendor_productsr_details', function(e) {
       e.preventDefault();
       $.colorbox({
           href: $(this).attr('href'),
           iframe: true,
           width: '800px',
           height: '500px'
       })
   });
     
   $(document).off('change', '#attachements');
   $(document).on("change", "#attachements", function(e) {
       e.preventDefault();
       var filename = $('#attachements').val();
       if (filename != '') {
           $("#upload_type").val("file");
           $("#vendor_frm").submit();
       } else {
           $("#upload_type").val("");
       }
   }); 
   
   $(document).off('click', '#add_vendor');
   $(document).on("click", "#add_vendor", function() {
       $("#upload_type").val("form");
       $is_address_ajaxed = $('#is_address_ajaxed').val();
       if($is_address_ajaxed == 1){
        updateAddress();
       }else{
        $("#vendor_frm").submit();
       }
   });
   
   $(document).off('click', '.delete_attachment');
   $(document).on("click", ".delete_attachment", function(e) {
       e.stopPropagation();
       var id = $(this).attr('data-id');
       swal({
          text: '<br>Delete Attachment: Are you sure?',
          showCancelButton: true,
          confirmButtonText: 'Confirm',
       }).then(function() {
           $("#ajax_loader").show();
           $.ajax({
               url: '<?= $ADMIN_HOST ?>/ajax_delete_vendor_attachment.php',
               dataType: 'JSON',
               type: 'POST',
               data: {id: id},
               success: function(res) {
                   $("#ajax_loader").hide();
                   if (res.status == "success") {
                       $("#attachment_file_div_" + id).remove();
                       setNotifySuccess(res.message);
                   } else {
                       setNotifyError(res.message);
                   }
               }
           });
       }, function(dismiss) {
   
       });
   });
   
   $(document).off('click', '.delete_fee');
   $(document).on("click", ".delete_fee", function(e) {
       e.stopPropagation();
       var id = $(this).attr('data-id');
       swal({
           text: '<br>Delete Record: Are you sure?',
           showCancelButton: true,
           confirmButtonText: 'Confirm',
       }).then(function() {
           $("#ajax_loader").show();
           $.ajax({
               url: '<?= $ADMIN_HOST ?>/ajax_delete_fee.php',
               dataType: 'JSON',
               type: 'POST',
               data: {id: id},
               success: function(res) {
                   $("#ajax_loader").hide();
                   if (res.status == "success") {
                       $("#row_" + id).remove();
                       setNotifySuccess(res.message);
                   } else {
                       setNotifyError(res.message);
                   }
               }
           });
       }, function(dismiss) {
   
       });
   });
   
   $(document).off('change', '.fee_status');
   $(document).on("change", ".fee_status", function(e) {
       e.stopPropagation();
       var status = $(this).val();
       var fee_id = $(this).attr("data-id");
       var old_status = $(this).attr('data-old_status');
   
       if (status != "") {
           swal({
               text: "<br>Change Status: Are you sure?",
               showCancelButton: true,
               confirmButtonText: "Confirm",
           }).then(function() {
               $("#ajax_loader").show();
               $.ajax({
                   url: '<?= $ADMIN_HOST ?>/ajax_update_fee_status.php',
                   data: {
                       id: fee_id,
                       status: status
                   },
                   type: 'POST',
                   dataType: 'json',
                   success: function(res) {
                       $("#ajax_loader").hide();
                       if (res.status == "success") {
                           $("#fee_status_" + fee_id).attr('data-old_status', status);
                           setNotifySuccess(res.msg);
   
                       } else {
                           setNotifyError(res.msg);
                           $("#fee_status_" + fee_id).val(old_status);
                           $('select.form-control').selectpicker('refresh');
                       }
                   }
               });
           }, function(dismiss) {
               $("#fee_status_" + fee_id).val(old_status);
               $('select.form-control').selectpicker('refresh');
           })
       }
   });
   
   function updateAddress(){
      $.ajax({
          url : "<?= $ADMIN_HOST ?>/ajax_manage_vendor.php",
          type : 'POST',
          data:$("#vendor_frm").serialize(),
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
                // ajaxSaveAccountDetails();
                $("#upload_type").val("form");
                $("#vendor_frm").submit();
             }else if(res.address_response_status =="success"){
                $(".suggestedAddressEnteredName").html($("#name").val());
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
                            $("#address2").val(res.address2).addClass('has-value');
                            $("#is_address_verified").val('Y');
                         }else{
                            $("#is_address_verified").val('N');
                         }
                         
                         $("#upload_type").val("form");
                         $("#vendor_frm").submit();
                      },
                });
             }else if(res.status == 'success'){
                $("#is_address_verified").val('N');
                $("#upload_type").val("form");
                $("#vendor_frm").submit();
             }else{
                $.each(res.errors,function(index,error){
                   $("#error_"+index).html(error).show();
               });
             }
             $("#state").selectpicker('refresh');
          }
       });
   }

   function load_vendor_fee_div() {
       var vendor_id = $("#vendor_id").val();
       var vendor_fee_id = $("#vendor_fee_id").val();
       $.ajax({
           url: '<?= $ADMIN_HOST ?>/ajax_load_vendor_fee.php',
           dataType: 'JSON',
           data: {
               vendor_id: vendor_id,
               vendor_fee_id: vendor_fee_id
           },
           type: 'POST',
           success: function(res) {
               if (res.status == "success") {
                   $("#vendor_fee_div").html(res.vendor_fee_div);
                   common_select();
               }
           }
       });
   }
   
</script>
<script type="text/javascript">
   $(document).ready(function() {  
       $(".ui-helper-hidden-accessible").remove();
   });
</script>