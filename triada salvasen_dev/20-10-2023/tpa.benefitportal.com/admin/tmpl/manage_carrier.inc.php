<div class="panel panel-default  panel-space">
   <form id="carrier_frm" name="carrier_frm" method="POST"  action="">
      <div class="panel-heading">
         <div class="panel-title">
            <p class="fs16 mn"><strong class="fw500">Carrier Settings</strong></p>
         </div>
      </div>
      <div class="panel-body theme-form manage_carrier">
         <div class="row">
            <div class="col-sm-4">
               <div class="form-group ">
                  <input type="text" class="form-control" name="name" id="name" value="<?= checkIsset($name); ?>">
                  <label>Carrier Name *</label>
                  <p class="error" id="error_name"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" class="form-control" name="display_id" id="display_id" value="<?= checkIsset($display_id); ?>">
                  <label>Carrier ID (Must Be Unique)*</label>
                  <p class="error" id="error_display_id"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" class="form-control" name="contact_fname" id="contact_fname" value="<?= checkIsset($contact_fname); ?>">
                  <label>Carrier Contact Name</label>
                  <p class="error" id="error_contact_fname"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" class="form-control" name="phone" id="phone" value="<?= checkIsset($phone); ?>">
                  <label>Phone</label>
                  <p class="error" id="error_phone"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <input type="text" class="form-control no_space" name="email" id="email" value="<?= checkIsset($email); ?>">
                  <label>Email</label>
                  <p class="error" id="error_email"></p>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
                  <select class="form-control" name="status" id="status" >
                     <option value=""></option>
                     <option value="Active" <?php if (checkIsset($status) == 'Active') { echo "selected='selected'"; } ?> >Active</option>
                     <option value="Inactive" <?php if (checkIsset($status) == 'Inactive') { echo "selected='selected'"; } ?> >Inactive</option>
                  </select>
                  <label>Status</label>
                  <p class="error" id="error_status"></p>
               </div>
            </div>
            <div class="col-sm-12">
              <div class="input-question m-b-20">
                  <div class="radio-inline">
                     <label>Does carrier use appointments?</label>
                     <p class="error" id="error_appointments"></p>
                  </div>
                  <div class="radio-inline">
                     <label class="mn">
                     <input type="radio" name="appointments" id="appointments_Y" value="Y" class="appointments" <?=(checkIsset($appointments)) == 'Y' ? "checked='checked'" : ""?> />Yes
                     </label>
                  </div>
                  <div class="radio-inline">
                     <label class="mn">
                     <input type="radio" name="appointments" id="appointments_N" value="N" class="appointments" <?=(checkIsset($appointments)) == 'N' ? "checked='checked'" : ""?> />No
                     </label>
                  </div>
                </div>
            </div>
         </div>
         <p class="fs16 m-b-20"><strong class="fw500">Fees</strong></p>
         <div class="clearfix"></div>
         <div id="carrier_fee_div"></div>
         <p class="error" id="error_carrier_fee_id"></p>
         <div class="step_btn_wrap m-t-30 text-right">
            <input type="submit" id="SaveCarrier" class="btn btn-action" value="Save Carrier">
            <input type="button" class="btn red-link" value="Cancel" onclick="window.location='carrier.php'">
            <input type="hidden" name="is_clone" id="is_clone" value="<?= $is_clone ?>">
            <input type="hidden" name="carrier_id" id="carrier_id" value="<?= $carrier_id ?>">
            <input type="hidden" name="carrier_fee_id" id="carrier_fee_id" value="<?= $carrier_fee_id ?>">
         </div>
      </div>
   </form>
</div>
<script type="text/javascript">
   $(document).ready(function(){
     checkEmail();
     $("#phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
     load_carrier_fee_div();
   
     $('#carrier_frm').ajaxForm({
       beforeSubmit: function(arr, $form, options) {
         $("#ajax_loader").show();
       },
       url: '<?= $ADMIN_HOST ?>/ajax_manage_carrier.php',
       type: 'POST',
       dataType: 'json',
       success: function(res) {
         $("#ajax_loader").hide();
         $('.error').html('');
         if (res.status == "success") {
           window.location.href = res.redirect_url;
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
   
   $(document).off('click', '.add_carrier_fee');
   $(document).on('click', '.add_carrier_fee', function(e) {
     e.preventDefault();
     $.colorbox({
       href: $(this).attr('href'),
       iframe: true,
       width: '855px',
       height: '600px'
     })
   });
   
   $(document).off('click', '.carrie_productsr_details');
   $(document).on('click', '.carrie_productsr_details', function(e) {
     e.preventDefault();
     $.colorbox({
       href: $(this).attr('href'),
       iframe: true,
       width: '800px',
       height: '500px'
     })
   });
   
   $(document).off('change', '.fee_status');
   $(document).on("change", ".fee_status", function(e) {
     e.stopPropagation();
     var status = $(this).val();
     var fee_id = $(this).attr("data-id");
     var old_status = $(this).attr('data-old_status');
     if (status != "") {
       swal({
         text: 'Change Fee Status to <strong class="text-blue">'+status+'</strong>: Are you sure?',
         showCancelButton: true,
         confirmButtonText: "Confirm",
         showCloseButton: true
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
   
   $(document).off('click', '.delete_fee');
   $(document).on("click", ".delete_fee", function(e) {
     e.stopPropagation();
     var id = $(this).attr('data-id');
     swal({
       //title: 'Are you sure?',
       text: 'Delete Fee: Are you sure?',
       //type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Confirm',
       cancelButtonText: 'Cancel',
     }).then(function() {
       $("#ajax_loader").show();
       $.ajax({
         url: '<?= $ADMIN_HOST ?>/ajax_delete_fee.php',
         dataType: 'JSON',
         type: 'POST',
         data: {
           id: id
         },
         success: function(res) {
           $("#ajax_loader").hide();
           if (res.status == "success") {
             $("#row_" + id).remove();
             setNotifySuccess(res.message);
           } else {
             setNotifyError(res.message);
           }
         }
       })
     }, function(dismiss) {
   
     });
   });
   
   function load_carrier_fee_div() {
     var carrier_id = $("#carrier_id").val();
     var carrier_fee_id = $("#carrier_fee_id").val();
   
     $.ajax({
       url: '<?= $ADMIN_HOST ?>/ajax_load_carrier_fee.php',
       dataType: 'JSON',
       data: {
         carrier_id: carrier_id,
         carrier_fee_id: carrier_fee_id
       },
       type: 'POST',
       success: function(res) {
         $('.error').html('');
         if (res.status == "success") {
           $("#carrier_fee_div").html(res.carrier_fee_div); 
           common_select();
         }
       }
     });
   }
   
</script>