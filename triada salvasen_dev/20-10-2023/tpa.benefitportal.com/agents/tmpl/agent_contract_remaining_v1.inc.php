<style type="text/css">
    @media (min-width: 768px){}
</style>
<div class="panel panel-default panel-block panel-shadowless mn " id="bap1" style="display:none">
    <div class="panel-body login-alert-modal" >
        <div class="media br-n pn mn">
            <div class="media-left"> <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left" height="105px"> </div>
            <div class="media-body">
                <h3 class="blue-link m-t-n fw500 fs24 m-b-10" >Application Submitted</h3>
                <p class="m-b-15">Your account is being reviewed by an admin. Once reviewed, you will be notified by email on the next steps to complete your application and begin selling.</p>
            </div>
            <div class="text-center"><a href="javascript:void(0);" target="_SELF" class="red-link sign_out">Sign Out</a></div>
        </div>
    </div>
</div>
<?php /*License Information Clone Start*/ ?>
<?php if (!in_array($agent_res["status"], array("Pending Contract"))) {?>
 <div class="license_template license_tempmdsm  " style="display: none">    
  <div class="license_portion pr div_license_~i~ m-t-25"> 
    <div class="row seven-cols">
      <input type="hidden" name='hdn_license[~i~]' value="~i~" id='hdn_license_~i~'>
      <div class="col-md-1">
        <div class="form-group ">
          <select name="license_state[~i~]" id="license_state_~i~"  class="license_state select_class_~i~" data-live-search="true">
            <option value="" ></option>
            <?php if (!empty($allStateRes)) {?>
              <?php foreach ($allStateRes as $state) {?>
              <option value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
              <?php }?>
            <?php }?>
          </select>
          <label>License State<em>*</em></label>
          <p class="error" id="error_license_state_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group  ">        
          <input name="license_number[~i~]" id="license_number_~i~" type="text" class="form-control license_number"   value="">
          <label for="license_number[~i~]">License Number<em>*</em></label>
          <p class="error" id="error_license_number_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <input type="text" name="license_active_date[~i~]" id="license_active_date_~i~" class="form-control license_active" />
            <label for="license_active_date_~i~">License Active Date<em>*</em></label>
            <p class="error" id="error_license_active_date_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group" id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
            <input name="license_expiry[~i~]" id="license_expiry_~i~" type="text" class="form-control license_expiry"  value="">
            <label for="license_expiry[~i~]">License Expiration<em>*</em></label>
            <p class="error" id="error_license_expiry_~i~"></p>
            <div class="clearfix m-t-5">
                <label for="license_not_expire[~i~]" class="text-red mn fs12">
                <input type="checkbox" name="license_not_expire[~i~]" id="license_not_expire_~i~" class="license_not_expire" data-id="~i~" value="Y">License does not expire</label>
            </div>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <select name="license_type[~i~]" id="license_type_~i~" class="select_class_~i~">
                <option value="" disabled selected hidden> </option>
                <option value="Business">Agency</option>
                <option value="Personal">Agent</option>
            </select>
            <label for="license_type~i~">License Type<em>*</em></label>
            <p class="error" id="error_license_type_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <select name="licsense_authority[~i~]" id="licsense_authority_~i~" class="select_class_~i~">
                <option value="" disabled selected hidden> </option>
                <option value="Health">Health</option>
                <option value="Life">Life</option>
                <option value="general_lines">General Lines (Both)</option>
            </select>
            <label for="licsense_authority~i~">License of Authority<em>*</em></label>
            <p class="error" id="error_licsense_authority_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <!--<a href="javascript:void(0)" class="edit_license btn red-link" style="display:none" id="edit_license_~i~" data-id="~i~" > Edit </a>
        <div class="form-group " id="hidden_btn_~i~">
          <button type="button" class="btn btn-primary ajax_add_license" data-id="~i~">Save</button> -->
          <a href="javascript:void(0)" class="remove_license btn red-link"  data-id="~i~"> Delete </a>
        <!--</div>-->
        
      </div>
      <div class="clearfix"></div>
    </div>
  </div>  
  <div class="clearfix"></div>
 </div>
<?php } ?>
<?php /*License Information Clone Start*/ ?>

<?php /*Model Content Start */ ?>
    <div id="rejectModal" class="modal fade enrollment_model" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="login-alert-modal">
                        <div class="media br-n pn mn">
                            <div class="media-left"> <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left" height="105px"></div>
                            <div class="media-body">
                                <h3 class="text-action m-t-n fw500 fs24 m-b-10" >Application Rejected</h3>
                                <div class="reject_enroll_text">
                                    <p class="mn"> <?=$rejection_text_new?></p>
                                </div>
                            </div>
                            <div class="text-center m-t-20"><a href="javascript:void(0);" data-dismiss="modal" class="red-link">Fix Issues</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="approvedModal" class="modal fade enrollment_model" role="dialog">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="login-alert-modal">
                            <div class="media br-n pn mn">
                                <div class="media-left"> <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left" height="80px"> </div>
                                <div class="media-body">
                                    <h3 class="text-blue m-t-n fw500 fs24 m-b-10" >Application Approved</h3>
                                    <div class="reject_enroll_text">
                                        <p class="mn">  Your account has been approved by an admin. Click next to review and sign the terms and conditions.</p>
                                    </div>
                                </div>
                                <div class="text-center m-t-20">
                                    <a data-toggle="tab" href="#thirdstep" data-dismiss="modal" class="red-link">Next</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
<?php /*Model Content End */ ?>
<div class="container m-t-30">
    <div class="panel panel-default panel-block panel-title-block ">
        <div class="panel-body contract-panel-body">
            <div class="cust_tab_ui">
                <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
                    <li class="<?=checkIsset($firstTabOpen)?> <?=checkIsset($firstTabComplete)?>" data-tab="firststep" id="fTabStep" >
                        <a data-toggle="tab" href="#firststep" class="btn_step_heading" data-step="1">
                            <div class="column-step ">
                                <div class="step-number">1</div>
                                <div class="step-title">BRAND</div>
                                <div class="step-info">Brand yourself by adding information below</div>
                            </div>
                        </a>
                    </li>
                    <li class="<?=checkIsset($secondTabOpen)?> <?=checkIsset($secondTabComplete)?>" data-tab="secondstep" id="sTabStep">
                        <a data-toggle="tab" href="#secondstep" class="btn_step_heading" data-step="2">
                            <div class="column-step">
                                <div class="step-number">2</div>
                                <div class="step-title">DOCUMENTS</div>
                                <div class="step-info">Fill out licensing information etc.</div>
                            </div>
                        </a> 
                    </li>
                    <li class="<?=checkIsset($thirdTabOpen)?> <?=checkIsset($lastTabDisabled)?>" data-tab="thirdstep">
                        <a data-toggle="tab" href="#thirdstep" class="btn_step_heading" data-step="3">
                            <div class="column-step">
                                <div class="step-number">3</div>
                                <div class="step-title">Agreement</div>
                                <div class="step-info">Agree to Terms and Conditions, etc.</div>
                            </div>
                        </a> 
                    </li>
                </ul>
            </div>
            <?php /*Form Start*/ ?>
                <form id="enrollment_form" name="enrollment_form" action="<?=$AGENT_HOST?>/ajax_agent_remaining_contract_v1.php" method="post">
                    <input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_res["id"]?>" />
                    <input name="is_draft" id="is_draft" type="hidden" value="" />
                    <input name="is_force" type="hidden" value="<?=$forceFullyAllow ? 1 : 0?>" />
                    <input type="hidden" name="dataStep" id="dataStep" value="0">
                    <input type="hidden" name="submit_type" id="submit_type" value="">
                    <input type="hidden" name="signature_data" id="hdn_signature_data" value="">
                    <input type="hidden" name="signature_name" id="signature_name" value="">
                    <input type="hidden" name="is_valid_address" id="is_valid_address" value="Y">
                    <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
                    <input type="hidden" name="is_agency_address_ajaxed" id="is_agency_address_ajaxed" value="">
                    <div class="tab-content m-t-30 theme-form">
                        <?php /*First Step Start*/ ?>
                            <div id="firststep" class="tab-pane fade <?=$firstTabOpen?>" <?= $lastTabDisabled !='' ? '' : 'style="display:none"'?>>
                            <div class="" id="display_popup_content" style="display:none">
                                <div class="panel-heading p-t-10 p-b-5 ">
                                    <div class="panel-title">
                                        <p class=" text-blue fs20 fw500 p-l-10">Display</p>
                                    </div>
                                </div>
                                <div class="panel-body p-t-0">
                                    <p class="m-b-25 p-l-10" style="font-size:13px">If box is checked, your members will not be able to see your name, phone, and email inside their member portal. See example below:</p>
                                    <div class="display_agent_info">
                                        <div class="p-l-10"><i class="fa fa-info-circle fs18 text-action" style="vertical-align: middle;" aria-hidden="true"></i> <span class="text-black fw500 fs15"> &nbsp; Your Agent &nbsp;|&nbsp;</span><?=$sponsor_detail['fname']." ".$sponsor_detail['lname']?>&nbsp; | &nbsp;<?=checkIsset($sponsor_detail['cell_phone']) ? format_telephone($sponsor_detail['cell_phone']) :"" ?>&nbsp; | &nbsp;<?=$sponsor_detail['email']?> </div>
                                    </div>
                                    <div class="clearfix m-t-20 text-center">
                                        <a href="javascript:void(0);" class="btn red-link pn" onclick="$.colorbox.close()">Close</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="step-title">
                                        <h4><a href="javascript:void(0)" id="display_popup">Display &nbsp;<i class="fa fa-info-circle text-blue fs20" aria-hidden="true"></i></a></h4>
                                    </div>
                                    <p class="m-b-25"><label class="label-input"><input type="checkbox" name="display_in_member" <?=$agent_res['display_in_member'] == 'Y' ? 'checked' : ''?> value="<?=$agent_res['display_in_member']?>">Check this box if you do not want display name, display phone, or display email shown as a point of contact.</label></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input name="admin_name" id="admin_name" type="text" class="form-control"  value="<?=$agent_res["public_name"]?>">
                                        <label>Display Name<em>*</em></label>
                                        <p class="error" id="error_admin_name"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input id='admin_phone' name="admin_phone" type="text" class="form-control"  value="<?=$agent_res["public_phone"]?>">
                                        <label>Display Phone<em>*</em></label>
                                        <p class="error" id="error_admin_phone"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input id='admin_emails' name="admin_email" type="text" class="form-control no_space"  value="<?=$agent_res["public_email"]?>">
                                        <label>Display Email<em>*</em></label>
                                        <p class="error" id="error_admin_email"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="step-title"><h4>Personal Application URL</h4></div>
                            <div class="row">
                                <div class="col-sm-5">
                                <p class="m-b-30">Without having to login to your agent portal, this unique application site allows you to enroll members quickly and easily. Please create a unique username. </p>
                                <div class="form-inline m-b-30">
                                    <div class="form-group height_auto mn"><?= $DEFAULT_SITE_URL ?>/</div>
                                    <div class="form-group height_auto mn">
                                            <input name="username" id="username" type="text"  class="form-control text-blue"  value="<?=$agent_res["user_name"]?>">
                                            <label>Username<em>*</em></label>
                                            <p class="error" id="error_username"></p>
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
                                </div>
                                </div>
                            </div>
                            <hr class="m-t-30 m-b-30">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="step-title">
                                        <h4>Brand Customization</h4>
                                    </div>
                                    <?php /*<p class="m-b-20"><label><input type="checkbox" name="is_branding" <?=$agent_res['is_branding'] == 'Y' ? 'checked' : ''?> value="<?=$agent_res['is_branding']?>">Check this box if you wish to skip branding your portal.  You can do this anytime by clicking on the Powered by smartE logo on the bottom right of the screen</label></p>*/?>
                                    <p class="m-b-20"><label for="">White labeling is available to you now and inside your portal. If you wish to replace the <?= $DEFAULT_SITE_NAME ?> logo with yours, please click the box below.</label></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="fw600 lato_font m-b-20">Logo</p>
                                    <p class="fw500 m-b-20">Click the box below to upload your customized branding logo.</p>
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
                                    <?php $tmp_style = 'display: none;';
                                        if (file_exists($AGENTS_BRAND_ICON . $contract_business_image) && $contract_business_image != "") {
                                            $tmp_style = 'display: block;';
                                    } ?>
                                    <div class="text-right pro_link_div m-t-15" style="<?=$tmp_style;?>">
                                        <!-- <a href="javascript:void(0);" class="btn btn-info">Upload</a> -->
                                        <a href="javascript:void(0);" onclick="return delete_business_image();" class="btn red-link">Remove</a>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="clearfix m-t-30 ">
                                <div class="pull-left">
                                    <p class="text-light-gray last_updated"><i>Last saved <?=$tz->getDate($agent_res['updated_at']);?></i></p>
                                </div>
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-info btn-draft-saver">Save</button>
                                    <button type='submit' class='btn btn-action btn-finish' id="btn_next" name='finish'>Next</button>
                                </div>
                            </div>
                            </div>
                        <?php /*First Step End*/ ?>
                        <?php /*Second Step Start*/ ?>
                            <div id="secondstep" class="tab-pane fade <?=$secondTabOpen?>" <?= $lastTabDisabled !='' ? '' : 'style="display:none"'?>>
                            <div class="step-title">
                                <h4>Account type</h4>
                            </div>
                            <div class="form-group">
                                <label class="m-r-10">What type of account is this?<em>*</em></label>
                                <label class="radio-inline">
                                    <input type="radio" id="business" name="account_type" class="account_type" value="Business" <?=($agent_res['account_type'] == 'Business'||empty($agent_res['account_type'])) ? "checked='checked'" : ''?>>Agency
                                </label>
                                <label class="radio-inline m-l-10">
                                    <input type="radio" id="personal" name="account_type" class="account_type" value="Personal" <?=($agent_res['account_type'] == 'Personal') ? "checked='checked'" : ''?>>Agent
                                </label>
                                <p class="error" id="error_account_type"></p>
                            </div>
                            <?php /* Business Div End */?>
                                <div id="BusinessDiv" style="<?=(($agent_res['account_type'] == 'Business'||empty($agent_res['account_type'])) ? '' : 'display: none')?>">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="business_name" id="business_name" type="text" class="form-control"  value="<?=$agent_res['company_name']?>">
                                            <label>Agency Legal Name<em>*</em></label>
                                            <p class="error" id="error_business_name"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="business_address" id="business_address" type="text" class="form-control" placeholder=""  value="<?=checkIsset($agent_res['company_address'])?>">
                                            <label>Address<em>*</em></label>
                                            <p class="error" id="error_business_address"></p>
                                            <input type="hidden" name="old_business_address" value="<?=checkIsset($agent_res['company_address'])?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="business_address2" id="business_address2" value="<?=$agent_res['company_address_2']?>" onkeypress="return block_special_char(event)" />
                                            <label>Address 2 (suite, apt)</label>
                                            <p class="error" id="error_business_address2"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="business_city" id="business_city" type="text" class="form-control"   value="<?=checkIsset($agent_res['company_city'])?>">
                                            <label>City<em>*</em></label>
                                            <p class="error" id="error_business_city"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select name="business_state" id="business_state"  class="form-control has-value" >
                                                <option value=""></option>
                                                <?php if (!empty($allStateRes)) {?>
                                                    <?php foreach ($allStateRes as $state) {?>
                                                        <option <?=$state["name"] == checkIsset($agent_res['company_state']) ? 'selected' : ''?> value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
                                                    <?php }?>
                                                <?php }?>
                                            </select>
                                            <label>State<em>*</em></label>
                                            <p class="error" id="error_business_state"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="business_zipcode" id="business_zipcode" maxlength="5" type="text" class="form-control"   value="<?=checkIsset($agent_res['company_zip'])?>">
                                            <label>Zip Code<em>*</em></label>
                                            <p class="error" id="error_business_zipcode"></p>
                                            <input type="hidden" name="old_business_zipcode" value="<?=checkIsset($agent_res['company_zip'])?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="business_taxid" id="business_taxid"  type="text" class="form-control"   value="<?=checkIsset($agent_res['tax_id'])!='' ? $agent_res['tax_id'] : ''?>">
                                            <label>Business Tax ID (EIN)</label>
                                            <p class="error" id="error_business_taxid"></p>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            <?php /* Business Div End */?>
                            <?php /* Agent Div Start */?>
                                <div style="<?=(($agent_res['account_type'] == 'Personal' || $agent_res['account_type'] == 'Business' || empty($agent_res['account_type'])) ? "" : "display: none")?>" id="PersonalDiv" class="<?=($agent_res['account_type'] == 'Personal') ? "removeLines" : ""?>">
                                <div class="step-title">   
                                    <h4>Principal Agent</h4>
                                </div>
                                <div class="row">   
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="fname" id="fname" type="text" class="form-control"  value="<?=$fname?>">
                                            <label>First Name<em>*</em></label>
                                            <p class="error" id="error_fname"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="lname" id="lname" type="text" class="form-control"   value="<?=$lname?>">
                                            <label>Last Name<em>*</em></label>
                                            <p class="error" id="error_lname"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                        <input name="address" id="address" type="text" class="form-control" placeholder="" value="<?=$agent_res["address"]?>">
                                        <label>Address<em>*</em></label>
                                        <p class="error" id="error_address"></p>
                                        <input type="hidden" name="old_address" value="<?=$agent_res["address"]?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="address_2" id="address_2" value="<?=$agent_res['address_2']?>" onkeypress="return block_special_char(event)" />
                                            <label>Address 2 (Suite, Apt)</label>
                                            <p class="error" id="error_address_2"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="city" id="city" type="text" class="form-control"   value="<?=$agent_res["city"]?>">
                                            <label>City<em>*</em></label>
                                            <p class="error" id="error_city"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select name="state" id="state"  class="form-control" >
                                                <option value=""></option>
                                                <?php if (!empty($allStateRes)) {?>
                                                    <?php foreach ($allStateRes as $state) {?>
                                                        <option <?=($state["name"] == $agent_res['state'] ? 'selected' : '')?> value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
                                                    <?php }?>
                                                <?php }?>
                                            </select>
                                            <label>State<em>*</em></label>
                                            <p class="error" id="error_state"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="zipcode" id="zipcode" maxlength="5" type="text" class="form-control"   value="<?=$agent_res["zip"]?>">
                                            <label>Zip Code<em>*</em></label>
                                            <p class="error" id="error_zipcode"></p>
                                            <input type="hidden" name="old_zipcode" value="<?=$agent_res["zip"]?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="dob" id="dob" type="text" class="form-control"  value="<?=(isset($agent_res["birth_date"]) && $agent_res["birth_date"] != "" && $agent_res["birth_date"] != "0000-00-00") ? date("m/d/Y", strtotime($agent_res["birth_date"])) : ""?>">
                                            <label>Date of Birth (MM/DD/YYYY)<em>*</em></label>
                                            <p class="error" id="error_dob"></p>
                                            <input type="hidden" id="age_count" name="age_count" value=''>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input name="ssn" id="ssn" type="text" class="form-control"   value="<?=$agent_res["dssn"]?>">
                                            <label>SSN<em>*</em></label>
                                            <p class="error" id="error_ssn"></p>
                                        </div>
                                    </div>    
                                </div>
                                </div>
                            <?php /* Agent Div End */?>
                            <?php /* Other Data Div Start */?>
                                <div class="all_data">
                                <?php /* License Information Start */?>
                                    <div class="step-title">    
                                        <h4>License Information</h4>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input name="npn_number" id="npn_number" type="text" class="form-control"   value="<?=checkIsset($agent_res["npn"])?>">
                                                <label>NPN Number<em>*</em></label>
                                                <p class="error" id="error_npn_number"></p>
                                                <a class="red-link" href="https://pdb.nipr.com/html/PacNpnSearch.html" target="_blank" >Click to Search NPN Website</a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="license_display_counter" id="license_display_counter" value="0">
                                    <div class="pr m-t-25" id="agent_licenses"> 
                                        <?php if(!empty($resADoc)){ 
                                            foreach($resADoc as $rkey => $doc){
                                                $resADocStatesArray=$doc['selling_licensed_state']!=""?explode(',',$doc['selling_licensed_state']):array();
                                        ?>
                                        <div class="license_portion license_tempmdsm  m-t-25 div_license_<?=$rkey?>" >
                                            <?php /*if($rkey==1){?>
                                            <div class="step-title border-top additional_license">
                                            <h4>Additional License(s)</h4>
                                            </div> 
                                            <?php } */?>
                                            <div class="row seven-cols">
                                                <input type="hidden" name='hdn_license[<?=$rkey?>]' value="<?=$doc['id']?>" id='hdn_license_<?=$rkey?>'>
                                                <div class="col-md-1">
                                                    <div class="form-group ">
                                                        <select name="license_state[<?=$rkey?>]" id="license_state_<?=$rkey?>"  class="license_state select_class_<?=$rkey?> form-control" data-live-search="true" >
                                                            <option value=""></option>
                                                            <?php if ($allStateRes) {?>
                                                                <?php foreach ($allStateRes as $state) {
                                                                    $hide_states=(!empty($selectedState)?array_diff($selectedState,$state):array());
                                                                ?>
                                                                    <option <?=in_array($state["name"],$resADocStatesArray) ? 'selected' : ''?> value="<?=$state["name"];?>" <?=in_array($state,$hide_states)?'disabled':'' ?>><?php echo $state['name']; ?></option>
                                                                <?php }?>
                                                            <?php }?>
                                                        </select>
                                                        <label>License State<em>*</em></label>
                                                        <p class="error" id="error_license_state_<?=$rkey?>"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group ">
                                                    <input name="license_number[<?=$rkey?>]" id="license_number_<?=$rkey?>" type="text" class="form-control license_number"   value="<?=$doc["license_num"]?>">
                                                    <label>License Number<em>*</em></label>
                                                    <p class="error" id="error_license_number_<?=$rkey?>"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group ">
                                                        <input type="text" name="license_active_date[<?=$rkey?>]" value="<?=(isset($doc["license_active_date"]) && $doc["license_active_date"] != "" && $doc["license_active_date"] != "0000-00-00" && strtotime($doc["license_active_date"]) > 0) ? date("m/d/Y", strtotime($doc["license_active_date"])) : ""?>" id="license_active_date_<?=$rkey?>" class="form-control license_active" />
                                                        <label>License Active Date<em>*</em></label>
                                                        <p class="error" id="error_license_active_date_<?=$rkey?>"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group mn " id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
                                                    <input name="license_expiry[<?=$rkey?>]" id="license_expiry_<?=$rkey?>" type="text" class="form-control license_expiry"  value="<?=(isset($doc["license_exp_date"]) && $doc["license_exp_date"] != "" && $doc["license_exp_date"] != "0000-00-00" && strtotime($doc["license_exp_date"]) > 0) ? date("m/d/Y", strtotime($doc["license_exp_date"])) : "Does Not Expire"?>" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "readonly='readonly'" : '' ?>>
                                                    <label>License Expiration<em>*</em></label>
                                                    <p class="error" id="error_license_expiry_<?=$rkey?>"></p>
                                                    <div class="clearfix m-t-5">
                                                        <label for="license_not_expire[<?=$rkey?>]" class="text-red fs12 mn" >
                                                        <input type="checkbox" name="license_not_expire[<?=$rkey?>]" id="license_not_expire_<?=$rkey?>" value="Y" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "checked='checked'" : '' ?> class="license_not_expire" data-id="<?=$rkey?>">
                                                        License does not expire</label>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group ">
                                                        <select class="form-control" name="license_type[<?=$rkey?>]" id="license_type_<?=$rkey?>">
                                                            <option value="" disabled selected hidden> </option>
                                                                <option value="Business" <?=$doc["license_type"] == 'Business' ? 'selected' : ''?>>Agency</option>
                                                                <option value="Personal" <?=$doc["license_type"] == 'Personal' ? 'selected' : ''?>>Agent</option>
                                                            </select>
                                                            <label>License Type<em>*</em></label>
                                                            <p class="error" id="error_license_type_<?=$rkey?>"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group ">
                                                        <select class="form-control" name="licsense_authority[<?=$rkey?>]" id="licsense_authority_<?=$rkey?>">
                                                            <option value="" disabled selected hidden> </option>
                                                            <option value="Health" <?=$doc["license_auth"] == 'Health' ? 'selected' : ''?>>Health</option>
                                                            <option value="Life" <?=$doc["license_auth"] == 'Life' ? 'selected' : ''?>>Life</option>
                                                            <option value="general_lines" <?=$doc["license_auth"] == 'general_lines' ? 'selected' : ''?>>General Lines (Both)</option>
                                                        </select>
                                                        <label>License of Authority<em>*</em></label>
                                                        <p class="error" id="error_licsense_authority_<?=$rkey?>"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group ">
                                                        <!--<a href="javascript:void(0)" class="edit_license btn red-link" id="edit_license_<?=$rkey?>" data-id="<?=$rkey?>" > Edit </a>
                                                        <div id="hidden_btn_<?=$rkey?>" style="display:none">
                                                            <button type="button" class="btn btn-primary ajax_add_license" data-id="<?=$rkey?>">Save</button> -->
                                                            <?php //if($rkey!=0){?>
                                                            <a href="javascript:void(0)" class="remove_license btn red-link" data-id="<?=$rkey?>" > Delete </a>
                                                            <?php //} ?>
                                                        <!--</div>-->
                                                    </div>
                                                </div>
                                                <?php /*if($rkey!=0){?>
                                                    <div class="col-md-1">
                                                    <div class="form-group">
                                                        <a href="javascript:void(0)" class="remove_license btn red-link"> Delete </a>
                                                    </div>
                                                    </div>
                                                    <?php }*/ ?>
                                            </div>
                                        </div>
                                        <?php } }?>  
                                    </div>
                                    <div class="right-add-btnwrap text-left ">            
                                        <a href="javascript:void(0)" class="add_more_license btn btn-info"><strong>+ License</strong></a>
                                    </div>
                                <?php /* License Information End */?>
                                <?php /* Error and Ommissions Insurance Start */?>
                                    <input type="radio" name="e_o_coverage" checked value="Y" id="e_o_yes" style="display: none">
                                    <div class="step-title m-t-25"> 
                                    <h4>Error and Ommissions Insurance (E&O)</h4>
                                    </div>
                                    <div id="eoDiv" class="row"> <!--style="<?php //=$resDoc["e_o_coverage"] == "Y" ? "" : "display: none"?>"-->
                                        <?php if(in_array($_SESSION['agents']['agent_coded_level'],array("LOA"))){?>
                                            <div class="form-group height_auto m-l-10">
                                            <label  class="label-input">  My E&O insurance is covered under my parent agent &nbsp;
                                                <input type="checkbox" <?=checkIsset($resDoc['by_parent']) == "Y" ? "checked" : ""?> name="e_o_by_parent" value="Y" id="e_o_by_parent">
                                            </label>
                                            </div>
                                        <?php } ?>
                                        <div id="e_o_information" style="<?=checkIsset($resDoc['by_parent']) == 'Y' ? 'display:none;' : ''?>">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input name="e_o_amount" id="e_o_amount" type="text" class="form-control"  value="<?=(checkIsset($resDoc["e_o_amount"]) > 0 ? $resDoc["e_o_amount"] : "")?>">
                                                    <label>E&O Amount</label>
                                                    <p class="error" id="error_e_o_amount"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input name="e_o_expiration" id="e_o_expiration" type="text" class="form-control"  value="<?=(!empty($resDoc["e_o_expiration"]) && $resDoc["e_o_expiration"] != "0000-00-00") ? date("m/d/Y", strtotime($resDoc["e_o_expiration"])) : ""?>">
                                                    <label>E&O Expiration (MM/DD/YYYY)<em>*</em></label>
                                                    <p class="error" id="error_e_o_expiration"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group cdrop_wrapper">
                                                    <div class="phone-control-wrap">
                                                        <div class="phone-addon">
                                                            <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload</span>
                                                                <input type="file" class="gui-file" accept="application/pdf" id="e_o_document" name="e_o_document">
                                                                <input type="text" class="gui-input" placeholder="<?=checkIsset($resDoc['e_o_document'])?>">
                                                                <label>Upload E&O<em>*</em></label>
                                                            </div>
                                                        </div>
                                                        <?php if (checkIsset($resDoc['e_o_document']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($resDoc['e_o_document']))) {?>
                                                            <a href="<?php echo $AGENT_DOC_WEB . $resDoc['e_o_document']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
                                                        <?php }?>
                                                    </div>
                                                    <p class="error" id="error_e_o_document"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                <?php /* Error and Ommissions Insurance End */?>
                                <div class="clearfix"></div>
                                <?php /* Commissions Bank Account Start */?>
                                    <?php if(!in_array($_SESSION['agents']['agent_coded_level'],array("LOA"))){?>
                                        <div id="bankInfoDiv">
                                            <div class="step-title m-t-25">
                                            <h4>Commissions Bank Account</h4>
                                            </div>                        
                                            <div id="pearsonalAccountDiv" class="m-b-15">
                                                <div class="personal_err">
                                                    <label>Account Type<em>*</em></label>
                                                    <label  class="radio-inline m-l-15">
                                                        <input type="radio" <?=checkIsset($resDirect["account_type"]) == "checking" ? "checked" : ""?> name="bank_account_type" value="checking" id="p_checking" class="form-control">
                                                    Checking</label>
                                                    <label class="radio-inline">
                                                        <input type="radio" <?=checkIsset($resDirect["account_type"]) == "savings" ? "checked" : ""?> name="bank_account_type" value="savings" id="p_savings" class="form-control">
                                                    Savings </label>
                                                </div>
                                                <p class="error" id="error_bank_account_type"></p>
                                            </div>
                                            <div class="row">    
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input name="bankname" id="bankname" type="text" class="form-control"  value="<?=checkIsset($resDirect["bank_name"])?>">
                                                        <label>Bank Name<em>*</em></label>
                                                        <p class="error" id="error_bankname"></p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input name="bank_rounting_number" maxlength="9" id="bank_rounting_number" type="text" class="form-control"  value="<?=checkIsset($resDirect["routing_number"])?>" oninput="isValidNumber(this)">
                                                        <label>Bank Routing Number<em>*</em></label>
                                                        <p class="error" id="error_bank_rounting_number"></p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input name="bank_account_number" id="bank_account_number" type="text" class="form-control"  value="<?=checkIsset($resDirect["account_number"])?>" oninput="isValidNumber(this)" maxlength="17">
                                                        <label>Bank Account Number<em>*</em></label>
                                                        <p class="error" id="error_bank_account_number"></p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input name="bank_number_confirm" id="bank_number_confirm" type="text" class="form-control"  value="" oninput="isValidNumber(this)" maxlength="17">
                                                        <label>Confirm Bank Account Number<em>*</em></label>
                                                        <p class="error" id="error_bank_number_confirm"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group cdrop_wrapper">
                                                        <div class="phone-control-wrap">
                                                            <div class="phone-addon">
                                                                <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload W9</span>
                                                                    <input type="file" class="gui-file" accept="application/pdf" id="w9_form_business" name="w9_form_business">
                                                                    <input type="text" class="gui-input" placeholder="<?=$agent_res['w9_pdf']?>">
                                                                    <label>Upload W9<em>*</em></label>
                                                                </div>
                                                            </div>
                                                            <?php if (checkIsset($agent_res['w9_pdf']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($agent_res['w9_pdf']))) {?>
                                                                <!-- <div class="phone-addon" style="width:35px;"> <i class="fa fa-download fs20 text-red"></i> </div> -->
                                                                <a href="<?php echo $AGENT_DOC_WEB . $agent_res['w9_pdf']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
                                                            <?php }?>
                                                        </div>
                                                        <a class="red-link" href="<?=$AGENT_DOC_WEB?>W9_Fillable.pdf" download>Click to download W9</a>
                                                    </div>
                                                    <p class="error" id="error_w9_form_business"></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>              
                                    <hr />
                                    <div class="clearfix">
                                        <div class="pull-left">
                                            <p class="text-light-gray last_updated mn"><i>Last saved <?=$tz->getDate($agent_res['updated_at']);?>.</i></p>
                                            <p class="text-light-gray" <?=$secondTabOpen == 'in active' ? '' : 'style="display:none"'?>>Review and click submit to have your account reviewed by an admin. Once reviewed, you will be notified via email by the administrative team on next steps to sign your contract and begin selling.</p>
                                        </div>
                                    </div>
                                <?php /* Commissions Bank Account End */?>
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-info btn-draft-saver" style="display:none">Save</button>
                                    <button type='button' id='btn_finish' class='btn btn-action btn btn-finish' name='finish'><?=$forceFullyAllow ? "Submit" : "Submit"?></button>
                                    <a data-toggle="tab" href="#firststep" class='btn red-link back_to_first' >Back</a>
                                </div>
                                <div class="clearfix"></div>
                                </div>
                            <?php /* Other Data Div End */?>
                            </div>
                        <?php /*Second Step End*/ ?>
                        <?php /*Third Step Start*/ ?>
                            <div id="thirdstep" class="tab-pane fade <?=checkIsset($thirdTabOpen)?>" data-contract="<?=$agent_res['status']?>">
                                <?php if (in_array($agent_res["status"], array("Pending Contract"))) {?>
                                    <div class="theme-form">
                                        <div class="step-title">
                                            <h4><?= $DEFAULT_SITE_NAME ?> Agent Agreement and Terms and Conditions</h4>
                                        </div>
                                        <div class="agreement_div" id="agreem">
                                            <?=checkIsset($terms,'arr');?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <br />
                                        <div class="clearfix check_agree_div">
                                            <label class="pr mn label-input">
                                                <input type="checkbox" name="check_agree" id="check_agree" value="Y">
                                                I agree to Terms and  Conditions and  accept Agreement<em>*</em><p class="error" id="error_check_agree"></p>
                                            </label>
                                        </div>
                                        <div class="clearfix"></div>
                                        <br />
                                        <?php /*Signature Pad Start */?>
                                            
                                        <!--Signature Pad Start  -->
                                        <div id="signature-pad" class="m-signature-pad" style="height:300px">
                                            <div class="m-signature-pad--body">
                                                <canvas></canvas>
                                            </div>
                                            <div class="m-signature-pad--footer">
                                                <div class="description pull-left">Draw your signature above<span class="text-action">*</span></div>
                                                <div class="pull-right">
                                                    <button type="button" class="btn btn-link m-t-5" data-action="clear">Erase</button>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="error" id="error_signature_data"></p>
                                        <?php /*Signature Pad End  */?>
                                        <hr />
                                        <div class="text-center ">
                                            <button type='button' class='btn btn-action btn-finish' id="final_step" name='finish'>Complete Contract</button>
                                            <!-- <input type="button" class="btn red-link" value="Back" /> -->
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                <?php }?>
                            </div>
                        <?php /*Third Step End*/ ?>
                    </div>
                </form>
            <?php /*Form End*/ ?>
        </div>
    </div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<?php include_once __DIR__.'/agent_remaining_contract_jquery.inc.php';?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>