   <p class="agp_md_title"> ACH Information</p>
   <p><i class="fa fa-info-circle"></i> Please complete the fields below to enable deposits into your account. All information is secure and encrypted.</p>
<form name="ach_application_form" id="ach_application_form">
    <input type="hidden" name="customer_id" value="<?= checkIsset($customer_id) ?>" />
    <input type="hidden" name="location" id="location" value="<?=$location?>">
    <input type="hidden" name="ach_id" id="ach_id" value="<?=$achId?>">
    <div class="theme-form">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                   <input type="text" class="form-control" name="ach_fname" value="<?=$achfname?>">
                   <label>First Name<em>*</em></label>
                   <p class="error" id="error_ach_fname"></p>
               </div>
           </div>
           <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control" name="ach_lname" value="<?=$achlname?>">
                <label>Last Name<em>*</em></label>
                <p class="error" id="error_ach_lname"></p>
              </div>
           </div>
           <div class="clearfix"></div>
           <div class="col-sm-6">
              <div class="form-group">
                 <input type="text" class="form-control" name="ach_bankname" value="<?=$achBankname?>">
                 <label>Bank Name<em>*</em></label>
                 <p class="error" id="error_ach_bankname"></p>
              </div>
           </div>
           <div class="col-sm-6">
              <div class="form-group">
                 <select name="ach_account_type" id="ach_account_type" class="form-control">
                   <option value=""></option>
                   <option value="checking" <?= $achAccountType=='checking' ? 'selected':''; ?> >Checking</option>
                   <option value="savings" <?= $achAccountType=='savings' ? 'selected':''; ?> >Saving</option>
                 </select>
                 <label>Select Account Type<em>*</em></label>
                 <p class="error" id="error_ach_account_type"></p>
              </div>
           </div>
           <div class="clearfix"></div>
           <div class="col-sm-6">
              <div class="form-group">
                 <input type="text" class="form-control" name="ach_account_number" value="<?=$achAccountNumber?>" oninput="isValidNumber(this)" maxlength='17'>
                <label>Account Number<em>*</em></label>
                <p class="error" id="error_ach_account_number"></p>
              </div>
           </div>
           <div class="col-sm-6">
              <div class="form-group">
                 <input type="text" class="form-control" name="confirm_ach_account_number" value="<?=$achAccountNumber?>" oninput="isValidNumber(this)" maxlength='17'>
                 <label>Confirm Account Number<em>*</em></label>
                 <p class="error" id="error_confirm_ach_account_number"></p>
              </div>
           </div>
           <div class="clearfix"></div>
           <div class="col-sm-6">
              <div class="form-group">
                 <input type="text" class="form-control" name="ach_routing_number" value="<?=$achRoutingNumber?>" oninput="isValidNumber(this)" maxlength='9'>
                 <label>Routing Number<em>*</em></label>
                 <p class="error" id="error_ach_routing_number"></p>
              </div>
           </div>
       </div>
       <div class="text-right">
          <button type="button" class="btn btn-action" name="ach_submit" id="ach_submit">Save</button>
       </div>
       <hr />
    </div>
</form>

<script type="text/javascript">
   $(document).off('click', '#ach_submit');
   $(document).on('click', '#ach_submit', function(e) {
      $('#ajax_loader').show();
      $.ajax({
         url : "<?= $HOST ?>/ajax_member_gap_ach_information.php",
         type : 'POST',
         data:$("#ach_application_form").serialize(),
         dataType:'json',
         beforeSend :function(e){
            $("#ajax_loader").show();
         },
         success : function(res){
            $("#ajax_loader").hide();
            $(".error").html("");
            if(res.status =='success'){
               $("#ach_id").val(res.ach_id);
               setNotifySuccess(res.successfully);
            }else{
               $.each(res.errors,function(index,error){
                  $("#error_"+index).html(error);
               });
            }
         }
      });
   });
</script>