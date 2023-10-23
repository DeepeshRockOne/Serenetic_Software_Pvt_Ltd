<div class="panel panel-default panel-block panel-space">
   <form class="" name="membership_form" id="membership_form" method="POST" action="">
      <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
      <div class="panel-heading">
         <div class="panel-title">
            <p class="fs16 mn"><strong class="fw500">Membership Settings</strong></p>
         </div>
      </div>
      <div class="panel-body theme-form ">
         <div class="row">
            <div class="col-sm-6 col-md-4">
               <div class="form-group">
                  <input name="name" id="name" type="text" class="form-control" value="<?=$membership_name?>" />
                  <label class="label-wrap">Membership Name (Must Be Unique)*</label>
                  <p class="error" id="error_name"></p>
               </div>
            </div>
            <div class="col-sm-6 col-md-4">
               <div class="form-group">
                  <input name="display_id" type="text" class="form-control" value="<?=$membership_id?>" />
                  <label>Membership ID (Must be unique)*</label>
                  <p class="error" id="error_display_id"></p>
               </div>
            </div>
            <div class="col-sm-12 col-md-4">
               <div class="row">
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input name="fname" type="text" class="form-control" value="<?=$contact_fname?>" />
                        <label>Contact First Name</label>
                        <p class="error" id="error_fname"></p>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input name="lname" type="text" class="form-control" value="<?=$contact_lname?>" />
                        <label>Contact Last Name</label>
                        <p class="error" id="error_lname"></p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm-12 col-md-4">
               <div class="row">
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input name="phone" type="text" class="form-control phone_mask" value="<?=$phone?>" />
                        <label>Phone</label>
                        <p class="error" id="error_phone"></p>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input name="email" type="text" class="form-control no_space" value="<?=$email?>" />
                        <label>Email</label>
                        <p class="error" id="error_email"></p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm-6 col-md-4">
               <div class="form-group">
                  <input name="address" id="address" type="text" class="form-control" value="<?=$address?>" />
                  <label>Address</label>
                  <p class="error" id="error_address"></p>
                  <input type="hidden" name="old_address" id="old_address" value="<?=$address?>">
               </div>
            </div>
            <div class="col-sm-6 col-md-4">
               <div class="form-group">
                  <input name="address2" id="address2" type="text" class="form-control" value="<?=$address2?>" onkeypress="return block_special_char(event)" />
                  <label>Address 2 (apt, suite)</label>
                  <p class="error" id="error_address2"></p>
               </div>
            </div>
            <div class="col-sm-12 col-md-8">
               <div class="row">
                  <div class="col-sm-4 col-md-3">
                     <div class="form-group">
                        <input name="city" id="city" type="text" class="form-control" value="<?=$city?>" />
                        <label>City</label>
                        <p class="error" id="error_city"></p>
                     </div>
                  </div>
                  <div class="col-sm-4 col-md-3">
                     <div class="form-group">
                        <select name="state" id="state" class="state form-control">
                           <option value="" disabled selected hidden> </option>
                           <?php if(count($state_res) > 0){ ?>
                           <?php foreach ($state_res as $key => $state) {
                              $state_name=$state['name']; 
                              ?>
                           <option value="<?= $state_name ?>" <?= $state_name == $membership_state ? 'selected="selected"' : ""?>><?= $state_name ?></option>
                           <?php } ?>
                           <?php } ?>
                        </select>
                        <label>State</label>
                        <p class="error" id="error_state"></p>
                     </div>
                  </div>
                  <div class="col-sm-4 col-md-3">
                     <div class="form-group">
                        <input name="zip" id="zip" type="text" class="form-control" maxlength="5" value="<?=$zip?>"/>
                        <label>Zip Code</label>
                        <p class="error" id="error_zip"></p>
                        <input type="hidden" name="old_zip" id="old_zip" value="<?=$zip?>">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <!-- membership benefits summernote start -->
         <div class="membership_benefits">
            <p class="fs16 m-t-0 m-b-20 fw500">Add Membership Benefits</p>
            <textarea rows="13" name="content" id="content" class="summernote" placeholder="Add">
                <?php echo $content; ?>
            </textarea>
            <p class="error" id="error_content"></p>
         </div>
         <!-- membership benefits end -->
         <div class="clearfix"></div>
         <div id="fee_table">
            <p class="fs16 m-t-20 m-b-20"><strong class="fw500">Fees</strong></p>
            <div class="clearfix"></div>
            <div id="membership_fee_div">
            </div>
         </div>
         <div class="step_btn_wrap m-t-30 text-right"> 
            <input type="button" name="add_membership" id="add_membership" class="btn btn-action" value="Save Membership">
            <input type="button" name="" id="" class="btn red-link" value="Cancel" onclick="window.location='memberships.php'">
            <input type="hidden" name="fee_id" id="fee_id" value="<?= $fee_id ?>">
            <input type="hidden" name="is_clone" id="is_clone" value="<?= $is_clone ?>">
            <input type="hidden" name="ids" id="ids" value="<?= $membership_fee_id ?>">
         </div>
      </div>
   </form>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script>
   $(document).ready(function() {
    checkEmail();
    initCKEditor("content");
   load_membership_fee_div();
   $('.add_fee_window').colorbox({iframe: true, width: '900x', height: '660px'});
   $('.add_vendor_fee').colorbox({iframe: true, width: '900x', height: '660px'});
   $('.phone_mask').mask('999-999-9999');
   
   $(document).on('focus','#address,#zip',function(){
      $('#is_address_ajaxed').val(1);
   });

   display_message = function(message,type){
   if(type=="success"){
       setNotifySuccess(message);
   }else{
       setNotifyError(message);
   }
   }
   // $('#membership_form').ajaxForm({
    $(document).off('click','#add_membership');
    $(document).on('click','#add_membership',function(){
      $is_address_ajaxed = $('#is_address_ajaxed').val();
      if($is_address_ajaxed == 1){
        updateAddress();
      }else{
        ajaxSaveAccountDetails();
      }
    });

    function ajaxSaveAccountDetails(){
       for(instance in CKEDITOR.instances) {
         CKEDITOR.instances[instance].updateElement();
       }
       $("#ajax_loader").show();
       $.ajax({
           url: '<?= $ADMIN_HOST ?>/ajax_manage_membership.php',
           type: 'POST',
           data: $('#membership_form').serialize(),
           dataType: 'json',
           success: function (res) {
               $("#ajax_loader").hide();
               $('.error').html('');
               if(res.status=="success"){
                   $("#fee_id").val(res.fee_id);
                   load_membership_fee_div();
                   window.location.href=res.redirect_url;
               }else{
                   var is_error = true;
                   $.each(res.errors, function (index, error) {
                       $('#error_' + index).html(error);
                       if (is_error) {
                           var offset = $('#error_' + index).offset();
                           if(typeof(offset) === "undefined"){
                               console.log("Not found : "+index);
                           }else{
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
   }

   function updateAddress(){
      $.ajax({
          url : "<?= $ADMIN_HOST ?>/ajax_manage_membership.php",
          type : 'POST',
          data:$("#membership_form").serialize(),
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
             }
             $("#state").selectpicker('refresh');
          }
       });
   }
   
   $(document).off('change', '.fee_status');
   $(document).on("change", ".fee_status", function(e) {
   e.stopPropagation();
   var status = $(this).val();
   var id = $(this).attr('id').replace('fee_status_', '');
   var fee_id = $(this).attr("data-id");
   var old_status = $(this).attr('data-old_status');
   if (status != "") {
     swal({
       text: 'Change Status: Are you sure?',
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
      $("#fee_status_" + id).val(old_status);
       $('select.form-control').selectpicker('refresh');
     })
   }
   });
   
   
   $(document).on("click",".delete_vendor_fee",function(){
   $id=$(this).attr('data-id');
   swal({
       text: 'Delete Record: Are you sure?',
       showCancelButton: true,
       confirmButtonText: 'Confirm',
       cancelButtonText: 'Cancel',
   }).then(function () {
       $("#ajax_loader").show();
       $.ajax({
           url:'ajax_delete_fee.php',
           dataType:'JSON',
           type:'POST',
           data:{id:$id},
           success:function(res){
               $("#ajax_loader").hide();
               if(res.status=="success"){
                   load_membership_fee_div();
                   setNotifySuccess(res.message);       
               }else{
                   setNotifyError(res.message);       
               }
           }
       })
   }, function (dismiss) {
       
   });
   });
   });
   load_membership_fee_div = function(){
   $fee_id = $("#fee_id").val();
   $ids = $("#ids").val();
   $is_clone = $("#is_clone").val();
   
   $.ajax({
       url:'ajax_load_membership_fee.php',
       dataType:'JSON',
       data:{fee_id:$fee_id,ids:$ids,is_clone:$is_clone},
       type:'POST',
       success:function(res){
           if(res.status=="success"){
               $("#membership_fee_div").html(res.membership_fee_div);
               $('[data-toggle="popover"]').popover();
               $('.phone_mask').mask('999-999-9999');
               $('.add_vendor_fee').colorbox({iframe: true, width: '855px', height: '660px'});
               $('.data_mask').mask('99/99/9999',{ "clearIncomplete": true,  showMaskOnHover: false, showMaskOnFocus: false, });
               common_select();
           }
       }
   });
   }
</script>