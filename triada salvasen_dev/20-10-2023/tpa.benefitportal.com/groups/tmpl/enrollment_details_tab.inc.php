<div class="theme-form">
   <h4 class="m-t-0 m-b-20">Group Information</h4>
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
            <input type="hidden" name="old_business_address" id="old_business_address">
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
         <div class="form-group height_auto">
            <input type="text" name="city" id="city" class="form-control" value="<?= $city ?>">
            <label>City<em>*</em></label>
            <p class="error" id="error_city"></p>
         </div>
      </div>
      <div class="col-sm-2">
         <div class="form-group height_auto">
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
         <div class="form-group height_auto">
            <input type="text" name="zipcode" id="zipcode" class="form-control"  value="<?= $zipcode ?>">
            <label>Zip Code<em>*</em></label>
            <p class="error" id="error_zipcode"></p>
            <input type="hidden" name="old_zipcode" id="old_zipcode">
         </div>
      </div>
      <div class="clearfix"></div>
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
   <h4 class="m-t-20 m-b-20">Group Contact</h4>
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

   <h4 class="m-t-20 m-b-20"><a href="javascript:void(0)" id="display_popup">Display &nbsp;<i class="fa fa-info-circle text-blue fs20" aria-hidden="true"></i></a></h4>
   <div class="row">
     <div class="col-sm-12">
       
       <p class="m-b-25"><label class="label-input"><input type="checkbox" name="display_in_member" value="Y" <?= $display_in_member == 'Y' ? 'checked' : '' ?>>Check this box if you do not want display name, display phone, or display email shown as a point of contact.</label></p>
     </div>
   </div>
   <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <input name="admin_name" id="admin_name" type="text" class="form-control"  value="<?= $public_name ?>">
          <label>Display Name<em>*</em></label>
          <p class="error" id="error_admin_name"></p>
        </div>
      </div>
      <div class="col-sm-4">
         <div class="form-group">
          <input id='admin_phone' name="admin_phone" type="text" class="form-control phone_mask"  value="<?= $public_phone ?>">
          <label>Display Phone<em>*</em></label>
          <p class="error" id="error_admin_phone"></p>
        </div>
      </div>
      <div class="col-sm-4">
      <div class="form-group">
        <input id='admin_emails' name="admin_email" type="text" class="form-control no_space"  value="<?= $public_email ?>">
        <label>Display Email<em>*</em></label>
        <p class="error" id="error_admin_email"></p>
      </div>
      </div>
   </div>
   
   <h4 class="m-t-20 m-b-20">Branding and Vanity URL <i class="fa fa-info-circle text-info" aria-hidden="true"></i></h4>
   <p class="fw500 m-b-20" >This unique url allows your members to self enroll quickly and easily without having to login to your group portal. Please provide your custom vanity url.</p>
   <div class="row">
      <div class="col-sm-12">
         <p class="m-b-20">Without having to login to your group portal, this unique application site allows you to enroll members quickly and easily. Please create a unique username. </p>
      </div>
   </div>
   <div class="form-inline m-b-30">
      <div class="form-group height_auto mn"><?= $DEFAULT_SITE_URL ?>/</div>
      <div class="form-group height_auto mn">
         <input type="text" name="username" id="username" class="form-control" value="<?= $username ?>">
         <label>Username<em>*</em></label>
         <div id="username_info" class="pswd_popup" style="display: none">
            <div class="pswd_popup_inner">
               <h4>URL Requirements</h4>
               <ul style="list-style:none; padding-left:10px;">
                 <li id="ulength" class="invalid"><em></em>Be between 4-20 characters</li>
                 <li id="alpha" class="invalid"><em></em>Contain no spaces or special characters</li>
                 <li id="unique" class="invalid"><em></em>Unique URL</li>
               </ul>
               <div class="btarrow"></div>
            </div>
         </div>
      </div>
       <p class="error" id="error_username"></p>
   </div>
   <p class="m-b-20 fw600">Logo</p>
   <p class="fw500 m-b-20">Click the box below to upload your customized branding logo.</p>
   <div class="row">
     <div class="col-sm-6">
       <div class="agent_drop_div pro_drop_div m-b-20" id="enrollment_profile">
          <input type="hidden" id="contract_profile_image_size" name="profile_image[size]" value="" />
          <input type="hidden" id="contract_profile_image_name" name="profile_image[name]" value="" />
          <input type="hidden" id="contract_profile_image_type" name="profile_image[type]" value="" />
          <input type="hidden" id="contract_profile_image_tmp_name" name="profile_image[tmp_name]" value="" />
            <div class="dropzone profile-dropzone">
                <div class="dropzone-previews" >
                </div>
            </div>
        </div>
        <?php
          $tmp_style = 'display: none;';
          if (file_exists($GROUPS_BRAND_ICON_DIR . $contract_business_image) && $contract_business_image != "") {
              $tmp_style = 'display: block;';
          } ?>
        <div class="text-right pro_link_div m-t-15" style="<?=$tmp_style;?>">
            <!-- <a href="javascript:void(0);" class="btn btn-info">Upload</a> -->
            <a href="javascript:void(0);" onclick="return delete_brand_icon();" class="btn red-link">Remove</a>
        </div>
     </div>
   </div>
   <div class="text-right">
      <a href="javascript:void(0);" class="btn btn-action next_tab_button" data-step="1">Next</a>
      <a href="javascript:void(0);" class="btn red-link cancel_tab_button">Cancel</a>
   </div>
   <div class="text-right m-t-20">
     <span><small>Last Saved Timestamp : <?=$last_saved?></small></span>
   </div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>