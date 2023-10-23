<form action="" name="account_detail" id="account_detail" method="POST">
    <input type="hidden" name="group_id" id="group_id" value="<?=$group_row['id']?>">
    <input type="hidden" name="group_id_" id="group_id_" value="<?=$group_row['_id']?>">
    <input type="hidden" name="is_valid_address" id="is_valid_address" value="<?=$is_valid_address?>">
    <input type="hidden" name="company_count" id="company_count" value=">">
    <input type="hidden" name="ip_display_counter" value="0" id="ip_display_counter">
    <input type="hidden" name="ip_group_count" value="1" id="ip_group_count">
    <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
    <div >
    <p class="agp_md_title">Account</p>
       <div class="theme-form">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="group_name" id="group_name" class="form-control" value="<?= $group_name ?>">
                        <label>Group Name<em>*</em></label>
                        <p class="error" id="error_group_name"></p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="business_address" id="business_address" class="form-control" value="<?= $business_address ?>" placeholder="">
                        <label>Business Address<em>*</em></label>
                        <p class="error" id="error_business_address"></p>
                        <input type="hidden" name="old_business_address" id="old_business_address" value="<?= $business_address ?>">
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="business_address_2" id="business_address_2" class="form-control" value="<?= $business_address_2 ?>" onkeypress="return block_special_char(event)" placeholder="">
                        <label>Address 2 (suite, apt)</label>
                        <p class="error" id="error_business_address_2"></p>
                     </div>
                </div>
                <div class="col-sm-2">
                     <div class="form-group">
                        <input type="text" name="city" id="city" class="form-control" value="<?= $city ?>">
                        <label>City<em>*</em></label>
                        <p class="error" id="error_city"></p>
                     </div>
                </div>
                <div class="col-sm-2">
                     <div class="form-group">
                        <select class="form-control" name="state" id="state">
                           <option data-hidden="true"></option>
                           <?php foreach ($allStateRes as $key => $value) { ?>
                              <option value="<?= $value['name'] ?>" <?= $state==$value['name'] ? 'selected' : '' ?>><?= $value['name'] ?></option>
                           <?php } ?>
                        </select>
                        <label>State<em>*</em></label>
                        <p class="error" id="error_state"></p>
                     </div>
                </div>
                <div class="col-sm-2">
                     <div class="form-group">
                        <input type="text" name="zipcode" id="zipcode" class="form-control"  value="<?= $zipcode ?>">
                        <label>Zip Code<em>*</em></label>
                        <p class="error" id="error_zipcode"></p>
                        <input type="hidden" name="old_zipcode" id="old_zipcode" value="<?= $zipcode ?>">
                     </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="business_phone" id="business_phone" class="form-control phone_mask" value="<?= $business_phone ?>">
                        <label>Business Phone<em>*</em></label>
                        <p class="error" id="error_business_phone"></p>
                    </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="business_email" id="business_email" class="form-control no_space" value="<?= $business_email ?>">
                        <label>Business Email<em>*</em></label>
                        <p class="error" id="error_business_email"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="no_of_employee" id="no_of_employee" class="form-control" value="<?= $no_of_employee ?>">
                        <label>Number of Employees<em>*</em></label>
                        <p class="error" id="error_no_of_employee"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="years_in_business" id="years_in_business" class="form-control" value="<?= $years_in_business ?>">
                        <label>Years In Business<em>*</em></label>
                        <p class="error" id="error_years_in_business"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="ein" id="ein" class="form-control ein_mask" value="<?= $ein ?>">
                        <label>EIN/FEIN<em>*</em></label>
                        <p class="error" id="error_ein"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <select class="form-control" name="nature_of_business" id="nature_of_business">
                           <option value=""></option>
                           <?php if(count($res_Business) > 0){ 
                              foreach ($res_Business as $value) {?>
                                 <option value="<?=$value['id']?>" <?=($nature_of_business == $value['id']) ? "selected='selected'" : ""?>><?=$value['industry_title']?></option>
                              <?php }
                           } ?>
                        </select>
                        <label>Nature of Business</label>
                        <p class="error" id="error_nature_of_business"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <select class="form-control" name="sic_code" id="sic_code">
                          <option value=""></option>

                          <?php if(!empty($sicCodeRes)) { ?>
                            <?php foreach ($sicCodeRes as $key => $value) { ?>
                              <option value="<?= $value['id'] ?>" <?=$value['id']==$sic_code ?'selected' : '' ?>> <?= $value['code'].' - '.$value['title'] ?></option>
                            <?php } ?>
                          <?php } ?>
                          
                        </select>
                        <label>SIC Code</label>
                        <p class="error" id="error_sic_code"></p>
                     </div>
                </div>
            </div>
            
            <p class="agp_md_title">Group Contact</p>
            <div class="row">
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="fname" id="fname" maxlength="25"  class="form-control" value="<?= $fname ?>">
                        <label>First Name<em>*</em></label>
                        <p class="error" id="error_fname"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" name="lname" id="lname" maxlength="25" class="form-control" value="<?= $lname ?>">
                        <label>Last Name<em>*</em></label>
                        <p class="error" id="error_lname"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" id='phone' name="phone" class="form-control phone_mask" value="<?= $phone ?>">
                        <label>Phone<em>*</em></label>
                        <p class="error" id="error_phone"></p>
                     </div>
                </div>
                <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" id='email' name="email" class="form-control no_space" value="<?= $email ?>">
                        <label>Email<em>*</em></label>
                        <p class="error" id="error_email"></p>
                     </div>
                </div>
            </div>
            
            <p class="agp_md_title">Password</p>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="password" id="password" name="password" value="" class="form-control"  maxlength="20" onblur="check_password(this, 'password_err', 'error_password', event, 'input_validation');" onkeyup="check_password_Keyup(this, 'password_err', 'error_password', event, 'input_validation');">
                        <label>Set Password<em>*</em></label>
                        <p class="error" id="error_password"></p>
                     
                        <div id="pswd_info" class="pswd_popup" style="display: none">
                             <div class="pswd_popup_inner">
                               <h4>Password Requirements</h4>
                               <ul>
                                 <li id="pwdLength" class="invalid"><em></em>Minimum 8 Characters</li>
                                 <li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase letter </li>
                                 <li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowercase letter </li>
                                 <li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
                               </ul>
                               <div class="btarrow"></div>
                             </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <input type="password" id="c_password" name="c_password" class="form-control"  maxlength="20">
                    <label>Confirm Password</label>
                    <p class="error" id="error_c_password"></p>
                  </div>
                </div>
                <?php 
                  echo generate2FactorAuthenticationUI($group_row);
                ?>
            </div>
            <hr>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="gp_attributes">
       <p class="agp_md_title">Attributes</p>
       <div class="form-group attributes_btn height_auto clearfix">
            <a href="groups_access_edit.php?group_id=<?=$group_id?>" class="btn btn-info-light group_access_edit">Access</a> 
            <a href="groups_account_managers.php?group_id=<?=$group_id?>" class="btn btn-info btn-outline group_account_managers">Account Managers</a> 
            <a href="group_personal_production_report.php?group_id=<?=$group_id?>" class="btn btn-action btn-outline personal_production_report">Personal Production Report</a> 
            <?php if(!empty($group_row) && $group_row['status'] == 'Active'){ ?>
                <a href="<?=$HOST?>/downloads3bucketfile.php?file_path=<?=urlencode($GROUP_AGREEMENT_CONTRACT_FILE_PATH)?>&file_name=<?=urlencode($group_row['agent_contract_file'])?>&user_id=<?=$group_id?>&location=admin_group_details" class="btn btn-action btn-outline">Group Agreement</a>
            <?php } ?>
       </div>
       
       <p class="agp_md_title">Group Operations</p>
       <div class="form-group height_auto">
          <p class="fw500">Does your group have additional locations and/or companies under common ownership?</p>
          <div class="m-b-15">
             <label class="mn"><input type="radio" name="group_company" value="Y" <?= !empty($group_company) && $group_company == 'Y' ? 'checked' : '' ?>>Yes</label>
          </div>
          <div class="mn">
             <label class="mn"><input type="radio" name="group_company" value="N" <?= !empty($group_company) && $group_company == 'N' ? 'checked' : '' ?>>No</label>
          </div>
          <p class="error" id="error_group_company"></p>
       </div>
       
       <div id="group_company_div" style="<?= !empty($group_company) && $group_company == 'Y' ? '' : 'display:none' ?>">
          <div class="table-responsive m-b-10">
             <table class="<?=$table_class?>">
                <thead>
                   <tr>
                      <th>Location/Company</th>
                      <th>EIN/FEIN</th>
                      <th>Location Code</th>
                      <th width="130px">Actions</th>
                   </tr>
                </thead>
                <tbody id="groupCompanyDate">
                    <?php if(!empty($selCompanyRes)) { 
                        foreach ($selCompanyRes as $key => $value) { ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td><?= $value['ein'] ?></td>
                                <td><?= $value['location'] ?></td>
                               
                                <td class="icons">
                                  <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="edit_group_company"><i class="fa fa-edit"></i></a>
                                  <?php if(!$disable_change){ ?>
                                    <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="delete_group_company"><i class="fa fa-trash"></i></a>
                                  <?php } ?>
                                </td>

                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                      <tr><td colspan="4" class="text-center">No Record(s) Found</td></tr>
                    <?php } ?>
                </tbody>
             </table>
          </div>
          <p class="error" id="error_company_count"></p>
          <div class="form-group height_auto">
             <a href="javascript:void(0);" class="btn btn-info" id="add_group_company">+ Location/Company</a>
          </div>
          <div class="form-group height_auto">
             <p class="fw500">Should the billing be broken out by individual locations/companies?</p>
             <div class="m-b-15">
                <label class="mn"><input type="radio" name="billing_broken" value="Y" <?= !empty($billing_broken) && $billing_broken == 'Y' ? 'checked' : '' ?>>Yes</label>
             </div>
             <div class="mn">
                <label class="mn"><input type="radio" name="billing_broken" value="N" <?= !empty($billing_broken) && $billing_broken == 'N' ? 'checked' : '' ?>>No</label>
             </div>
             <p class="error" id="error_billing_broken"></p>
          </div>
          <div class="form-group height_auto">
            <p class="fw500 m-b-20">Select which type of users you would like to receive automated communications from the system when an action occurs</p>
            <div class="row">
               <div class="col-md-2 col-sm-4">
                  <p>Group Adminstration</p>
                  <div class="custom-switch">
                     <label class="smart-switch">
                        <input type="checkbox" name="automated_communication[]" class="js-switch"  value="Group" <?= !empty($automated_communication) && in_array("Group",$automated_communication) ? 'checked' : '' ?> />
                        <div class="smart-slider round"></div>
                     </label>
                  </div>
               </div>
               <div class="col-md-2 col-sm-4">
                  <p>Members</p>
                  <div class="custom-switch">
                     <label class="smart-switch">
                        <input type="checkbox" name="automated_communication[]" class="js-switch" value="Members" <?= !empty($automated_communication) && in_array("Members",$automated_communication) ? 'checked' : '' ?>/>
                        <div class="smart-slider round"></div>
                     </label>
                  </div>
               </div>
               <div class="col-md-2 col-sm-4">
                  <p>Enrollees</p>
                  <div class="custom-switch">
                     <label class="smart-switch">
                        <input type="checkbox" name="automated_communication[]" class="js-switch" value="Enrollees" <?= !empty($automated_communication) && in_array("Enrollees",$automated_communication) ? 'checked' : '' ?>/>
                        <div class="smart-slider round"></div>
                     </label>
                  </div>
               </div>
               <p class="error" id="error_automated_communication"></p>
            </div>
          </div>
       </div>
        
       <div class="clearfix text-center">
         <a href="javascript:void(0);" class="btn btn-action" id="save_account">Save</a>
       </div>
       
       <hr>
       <p class="agp_md_title">Resources</p>
       <p>Add unique resource links that will appear in the resources dropdown in the navigation.</p>
       <div class="table-responsive m-b-15">
         <table class="<?=$table_class?>">
           <thead>
             <tr>
               <th>Label</th>
               <th>URL</th>
               <th width="100px">Actions</th>
             </tr>
           </thead>
           <tbody id="groupResourceData">
                <?php if(!empty($selResourceRes)) { 
                    foreach ($selResourceRes as $key => $value) { ?>
                        <tr>
                            <td><?= $value['label'] ?></td>
                            <td><?= $value['url'] ?></td>
                           
                            <td class="icons">
                              <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="edit_group_resource"><i class="fa fa-edit"></i></a>
                              <a href="javascript:void(0);" data-id="<?= $value['id'] ?>" class="delete_group_resource"><i class="fa fa-trash"></i></a>
                            </td>

                        </tr>
                    <?php } ?>
                <?php }else{ ?>
                  <tr><td colspan="4" class="text-center">No Record(s) Found</td></tr>
                <?php } ?>
           </tbody>
         </table>
       </div>
       <div class="clearfix m-b-20">
         <a href="javascript:void(0)" class="btn btn-info" id="add_group_resource">+ Resource Link</a>
       </div>
       <hr>
    </div>
</form>
<?=generateIPAddressUI()?>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script src="<?=$HOST?>/js/password_validation.js<?=$cache?>" type="text/javascript"></script>
<script type="text/javascript">
    $disable_change ='<?= $disable_change ?>';
    $(document).ready(function(){
        checkEmail();
        var $site_location = '<?= $SITE_ENV ?>';

        var placeSearch, autocomplete;

        $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        $(".ein_mask").inputmask({"mask": "99-9999999",'showMaskOnHover': false});
        $("#via_mobile").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});

        $group_company = '<?= $group_company ?>';
        $("input[name='group_company']").uniform();
        $("input[name='billing_broken']").uniform();
        $('.group_access_edit').colorbox({iframe: true, width: '600px', height: '630px'});
        $('.group_account_managers').colorbox({iframe: true, width: '900px', height: '470px'});
        $('.personal_production_report').colorbox({iframe: true, width: '900px', height: '800px'});

        $(document).on('focus','#business_address,#zipcode',function(){
          $('#is_address_ajaxed').val(1);
        });
    });

    $(document).off("change","#nature_of_business");
    $(document).on("change","#nature_of_business",function(){
          getSICCode();
    });
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

    $(document).off("click","#add_group_company");
    $(document).on("click","#add_group_company",function(){
         $group_id = $("#group_id_").val();
         $.colorbox({
            href:'<?= $ADMIN_HOST ?>/groups_add_company.php?group_id='+$group_id,
            iframe: true, 
            width: '768px', 
            height: '550px',
            onClosed:function(){
               load_group_company();
            }
         })
    });

    $(document).off("click",".edit_group_company");
    $(document).on("click",".edit_group_company",function(){
         $id = $(this).attr('data-id');
         $group_id = $("#group_id_").val();
         $.colorbox({
            href:'<?= $ADMIN_HOST ?>/groups_add_company.php?id='+$id+'&group_id='+$group_id,
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
        $group_id = $("#group_id_").val();
        swal({
          text: "Delete Record: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm"
        }).then(function () {
          $("#ajax_loader").show();
          $.ajax({
              url:'<?= $ADMIN_HOST ?>/ajax_delete_group_company.php',
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
    $(document).off("change","input[name=group_company]");
    $(document).on("change","input[name=group_company]",function(e){
        e.stopPropagation();
        $val=$(this).val();

        if($disable_change){
          if($val=='Y'){
            $("input[name=group_company][value='N']").prop("checked",true);
          }else{
            $("input[name=group_company][value='Y']").prop("checked",true);
          }
          $("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
        }else{
          $("#group_company_div").hide();
          if($val=='Y'){
              $("#group_company_div").show();
              load_group_company();
          }
        }
    });

    $(document).off("change","input[name=billing_broken]");
    $(document).on("change","input[name=billing_broken]",function(e){
        e.stopPropagation();
        $val=$(this).val();

        if($disable_change){
          if($val=='Y'){
            $("input[name=billing_broken][value='N']").prop("checked",true);
          }else{
            $("input[name=billing_broken][value='Y']").prop("checked",true);
          }
          $("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
        }
    });


    
    load_group_company = function(){
      $("#ajax_loader").show();
      $group_id = $("#group_id_").val();
      $.ajax({
         url:'<?= $ADMIN_HOST ?>/ajax_load_group_company.php',
         dataType:'JSON',
         data:{group_id:$group_id,disable_change:$disable_change},
         type:'POST',
         success: function(res) {
            $('#ajax_loader').hide();
            $("#groupCompanyDate").html(res.html);
            $("#company_count").val(res.company_count);
         }
       });
    }

    $(document).off("click","#add_group_resource");
    $(document).on("click","#add_group_resource",function(){
         $group_id = $("#group_id_").val();
         $.colorbox({
            href:'<?= $ADMIN_HOST ?>/groups_add_resource.php?group_id='+$group_id,
            iframe: true, 
            width: '768px', 
            height: '320px',
            onClosed:function(){
               load_group_resource();
            }
         })
    });

    $(document).off("click",".edit_group_resource");
    $(document).on("click",".edit_group_resource",function(){
         $id = $(this).attr('data-id');
         $group_id = $("#group_id_").val();
         $.colorbox({
            href:'<?= $ADMIN_HOST ?>/groups_add_resource.php?id='+$id+'&group_id='+$group_id,
            iframe: true, 
            width: '768px', 
            height: '320px',
            onClosed:function(){
               load_group_resource();
            }
         })
    });

    $(document).off("click",".delete_group_resource");
    $(document).on("click",".delete_group_resource",function(){
         $id = $(this).attr('data-id');
         $group_id = $("#group_id_").val();
        swal({
            text: "Delete Record: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }).then(function () {
         $.ajax({
            url:'<?= $ADMIN_HOST ?>/ajax_delete_group_resource.php',
            dataType:'JSON',
            data:{id:$id,group_id:$group_id},
            type:'POST',
            success:function(res){
               if(res.status == "success"){
                  setNotifySuccess(res.msg);
                  load_group_resource();
               }
            }
         });
         }, function (dismiss) {
        });
    });
    load_group_resource = function(){
      $("#ajax_loader").show();
      $group_id = $("#group_id_").val();
      $.ajax({
         url:'<?= $ADMIN_HOST ?>/ajax_load_group_resource.php',
         dataType:'JSON',
         data:{group_id:$group_id},
         type:'POST',
         success: function(res) {
            $('#ajax_loader').hide();
            $("#groupResourceData").html(res.html);
            
         }
       });
    }

    $(document).off('click','#save_account');
    $(document).on('click','#save_account',function(e){
        $("#ajax_loader").show();
        // $(".error").html('');
        $is_address_ajaxed = $("#is_address_ajaxed").val();
        if($is_address_ajaxed == 1){
          updateAddress();
        }else{
          ajaxSaveAccountDetails();
        }
    });

    function updateAddress(){
      $.ajax({
      url : "ajax_update_group_account_detail.php",
      type : 'POST',
      data:$("#account_detail").serialize(),
      dataType:'json',
      beforeSend :function(e){
         $("#ajax_loader").show();
         $(".error").html('');
      },success(res){
         $("#is_address_ajaxed").val("");
         $("#ajax_loader").hide();
         $(".suggested_address_box").uniform();
         if(res.zip_response_status =="success"){
            $("#is_address_verified").val('N');
            $("#state").val(res.state).addClass('has-value');
            $("#city").val(res.city).addClass('has-value');
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
                        $("#business_address").val(res.address).addClass('has-value');
                        $("#business_address_2").val(res.address2).addClass('has-value');
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
         $('#state').selectpicker('refresh');
      }
   });
    }

    function ajaxSaveAccountDetails(){
      $.ajax({
          url:'ajax_update_group_account_detail.php',
          dataType:'JSON',
          data:$("#account_detail").serialize(),
          type:'POST',
          success:function(data){
              $("#ajax_loader").hide();
              $(".error").hide();
              if (data.status == 'success') {
                  setNotifySuccess("Group detail updated successfully!");
              } else if (data.status == "fail") {
                  setNotifyError("Oops... Something went wrong please try again later");
              } else {
                  var tmp_flag = true;
                  $.each(data.errors, function(key, value) {
                      $('#error_' + key).parent("p.error").show();
                      $('#error_' + key).html(value).show();
                      $('.error_' + key).parent("p.error").show();
                      $('.error_' + key).html(value).show();
                      if (tmp_flag == true) {
                          if($("[name='" + key + "']").length > 0) {
                              tmp_flag = false;
                              $('html, body').animate({
                                  scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                              }, 1000);
                          }
                          if(tmp_flag == true && $("#error_" + key).length > 0) {
                              tmp_flag = false;
                              $('html, body').animate({
                                  scrollTop: parseInt($("#error_" + key).offset().top) - 100
                              }, 1000);
                          }
                      }
                  });
              }
          }
      });
    }

<?=generate2FactorAuthenticationJS();?>
</script>