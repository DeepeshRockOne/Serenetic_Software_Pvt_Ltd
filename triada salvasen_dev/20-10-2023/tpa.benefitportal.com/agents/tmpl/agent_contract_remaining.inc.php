<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script> 
<script type="text/javascript" src="<?=$AGENT_HOST?>/js/agent_remaining_contract.js<?=$cache?>"></script>
<style type="text/css">
@media (min-width: 768px){


<?php 
if($popup_open_res){ // this variable comes from header file
  ?>
  .preli_commissions{display: none};
  <?php
}
?>
</style>
<div class="panel panel-default panel-block panel-shadowless mn " id="bap1" style="display:none">
<div class="panel-body login-alert-modal" >
  <div class="media br-n pn mn">
    <div class="media-left"> <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left" height="105px"> </div>
    <div class="media-body">
      <h3 class="blue-link m-t-n fw500 fs24 m-b-10" >Application Submitted</h3>
      <p class="m-b-15">Your account is being reviewed by an admin. Once reviewed, you will be notified by email on the next steps to complete your application and begin selling.</p>
      </div>
      <div class="text-center"><a href="javascript:void(0);" target="_SELF" class="red-link sign_out">Sign Out</a>
    </div>
  </div>
</div>
</div>
<div class="container m-t-30">
<div class="panel panel-default panel-block panel-title-block ">
      <!--   Creative Tim Branding   -->
      <?php include_once 'notify.inc.php';?>
      <div class="panel-body contract-panel-body">
      <!--   Big container   -->
      <div class="cust_tab_ui">
          <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
                <li class="<?=checkIsset($firstTabOpen)?> <?=checkIsset($firstTabComplete)?>" data-tab="firststep" id="fTabStep"> <a data-toggle="tab" href="#firststep" class="btn_step_heading" >
                  <div class="column-step ">
                    <div class="step-number">1</div>
                    <div class="step-title">BRAND</div>
                    <div class="step-info">Brand yourself by adding information below</div>
                  </div>
                  </a> </li>
                <li class="<?=checkIsset($secondTabOpen)?> <?=checkIsset($secondTabComplete)?>" data-tab="secondstep" id="sTabStep"> <a data-toggle="tab" href="#secondstep" class="btn_step_heading" >
                  <div class="column-step">
                    <div class="step-number">2</div>
                    <div class="step-title">DOCUMENTS</div>
                    <div class="step-info">Fill out licensing information etc.</div>
                  </div>
                  </a> </li>
                <li class="<?=checkIsset($thirdTabOpen)?> <?=checkIsset($lastTabDisabled)?>" data-tab="thirdstep"> <a data-toggle="tab" href="#thirdstep" class="btn_step_heading">
                  <div class="column-step">
                    <div class="step-number">3</div>
                    <div class="step-title">Agreement</div>
                    <div class="step-info">Agree to Terms and Conditions, etc.</div>
                  </div>
                  </a> 
                </li>
        </ul>
      </div>
        <div class="tab-content m-t-30">
          <div id="firststep" class="tab-pane fade <?=$firstTabOpen?>">
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
           		<form id="self_enrollment_first" name="self_enrollment_first" action="<?=$AGENT_HOST?>/ajax_agent_remaining_contract.php" method="post">
              <input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_res["id"]?>" />
              <input type="hidden" name="step" value="1" />
              <input name="is_draft" type="hidden" value="" />
              <input name="is_force" type="hidden" value="<?=$forceFullyAllow ? 1 : 0?>" />

              <div class="theme-form">
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
                          </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                            <input id='admin_phone' name="admin_phone" type="text" class="form-control"  value="<?=$agent_res["public_phone"]?>">
                            <label>Display Phone<em>*</em></label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                          <input id='admin_emails' name="admin_email" type="text" class="form-control"  value="<?=$agent_res["public_email"]?>">
                          <label>Display Email<em>*</em></label>
                        </div>
                        </div>
                      </div>
                        <div class="clearfix"></div>
                        <div class="step-title">
                        	<h4>Personal Application URL</h4>
                        </div>
                       <div class="row">
                        <div class="col-sm-5">
                          <p class="m-b-30">Without having to login to your agent portal, this unique application site allows you to enroll members quickly and easily. Please create a unique username. </p>
                          <div class="form-inline m-b-30">
                              <div class="form-group height_auto mn"><?= $DEFAULT_SITE_URL ?>/</div>
                              <div class="form-group height_auto mn">
                          			<input name="username" id="username" type="text"  class="form-control text-blue"  value="<?=$agent_res["user_name"]?>">
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
                          </div>
                        </div>
                      </div>
                    <hr class="m-t-30 m-b-30">
                    <div class="row">
                    <div class="col-md-6">
                     <div class="step-title">
                          <h4>Brand Customization</h4>
                     </div>
                      <!--<p class="m-b-20"><label><input type="checkbox" name="is_branding" <?=$agent_res['is_branding'] == 'Y' ? 'checked' : ''?> value="<?=$agent_res['is_branding']?>">Check this box if you wish to skip branding your portal.  You can do this anytime by clicking on the Powered by smartE logo on the bottom right of the screen</label></p>-->
                      <p class="m-b-20"><label for="">White labeling is available to you now and inside your portal. If you wish to replace the <?= $DEFAULT_SITE_NAME ?> logo with yours, please click the box below.</label></p>
                    </div>
                    </div>
                     <div class="row">
                       <div class="col-sm-6">
                        <p class="fw600 lato_font m-b-20">Logo</p>
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
                <?php //if($agent_res["status"]=="Pending Approval"&&$agent_res["user_name"]==""){?>
              <div class="clearfix m-t-30 ">
                <div class="pull-left">
                  <p class="text-light-gray last_updated"><i>Last saved <?=$tz->getDate($agent_res['updated_at']);?></i></p>
                </div>
                  <div class="pull-right">
                    <input type="submit" class="btn btn-info btn-draft-saver" value='Save'/>
                    <input type='submit' class='btn btn-action btn-finish' name='finish' value='Next' />
                  </div>
                </div>
               </div>
              </form>
          </div>
          <div id="secondstep" class="tab-pane fade <?=$secondTabOpen?>">
           	 <form id="self_enrollment_second" name="self_enrollment_second" action="<?=$AGENT_HOST?>/ajax_agent_remaining_contract.php" method="post">
              <input type="hidden" name="agent_id" value="<?=$agent_res["id"]?>" />
              <input type="hidden" name="step" value="2" />
              <input name="is_draft" type="hidden" value="" />
              <input name="is_force" type="hidden" value="<?=$forceFullyAllow ? 1 : 0?>" />
              <div class="theme-form">
                  		<div class="step-title">
                  			<h4>Account type</h4>
											</div>
                        <div class="form-group">
                          <label class="m-r-10">What type of account is this?<em>*</em></label>
                          <label class="radio-inline">
                            <input type="radio" id="business" name="account_type" class="account_type"
                            value="Business" <?=($agent_res['account_type'] == 'Business'||empty($agent_res['account_type'])) ? "checked='checked'" : ''?>>
                          Agency </label>
                          <label class="radio-inline m-l-10">
                            <input type="radio" id="personal" name="account_type" class="account_type"
                            value="Personal" <?=($agent_res['account_type'] == 'Personal') ? "checked='checked'" : ''?>>
                          Agent </label>
                        </div>

                      <div id="BusinessDiv" style="<?=(($agent_res['account_type'] == 'Business'||empty($agent_res['account_type'])) ? '' : 'display: none')?>">
                        <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="business_name" id="business_name" type="text" class="form-control"  value="<?=$agent_res['company_name']?>">
                                <label>Agency Legal Name<em>*</em></label>
                              </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="business_address" id="business_address" type="text" class="form-control"  value="<?=checkIsset($agent_res['company_address'])?>">
                                <label>Address<em>*</em></label>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                  <input type="text" class="form-control" name="business_address2" id="business_address2" value="<?=$agent_res['company_address_2']?>" onkeypress="return block_special_char(event)" />
                                  <label>Address 2 (suite, apt)</label>
                                  <p class="error"><span id="error_business_address2"></span></p>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="business_city" id="business_city" type="text" class="form-control"   value="<?=checkIsset($agent_res['company_city'])?>">
                                <label>City<em>*</em></label>
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
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="business_zipcode" id="business_zipcode" maxlength="5" type="text" class="form-control"   value="<?=checkIsset($agent_res['company_zip'])?>">
                                <label>Zip Code<em>*</em></label>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="business_taxid" id="business_taxid"  type="text" class="form-control"   value="<?=checkIsset($agent_res['tax_id'])!='' ? $agent_res['tax_id'] : ''?>">
                                <label>Business Tax ID (EIN)</label>
                              </div>
                            </div>
                         </div>
                      </div>
                      
                      <div style="<?=(($agent_res['account_type'] == 'Personal' || $agent_res['account_type'] == 'Business' || empty($agent_res['account_type'])) ? "" : "display: none")?>" id="PersonalDiv" class="<?=($agent_res['account_type'] == 'Personal') ? "removeLines" : ""?>">
                      
                        <div class="step-title">	
                          <h4>Principal Agent</h4>
                        </div>
                        <div class="row">	
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input name="fname" id="fname" type="text" class="form-control"  value="<?=$fname?>">
                                    <label>First Name<em>*</em></label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input name="lname" id="lname" type="text" class="form-control"   value="<?=$lname?>">
                                    <label>Last Name<em>*</em></label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="address" id="address" type="text" class="form-control"   value="<?=$agent_res["address"]?>">
                                <label>Address<em>*</em></label>
                              </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                <input type="text" class="form-control" name="address_2" id="address_2" value="<?=$agent_res['address_2']?>" onkeypress="return block_special_char(event)" />
                                <label>Address 2 (Suite, Apt)</label>
                                <p class="error"><span id="error_address_2"></span></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="city" id="city" type="text" class="form-control"   value="<?=$agent_res["city"]?>">
                                <label>City<em>*</em></label>
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
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="zipcode" id="zipcode" maxlength="5" type="text" class="form-control"   value="<?=$agent_res["zip"]?>">
                                <label>Zip Code<em>*</em></label>
                              </div>
                            </div>
                           
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="dob" id="dob" type="text" class="form-control"  value="<?=(isset($agent_res["birth_date"]) && $agent_res["birth_date"] != "" && $agent_res["birth_date"] != "0000-00-00") ? date("m/d/Y", strtotime($agent_res["birth_date"])) : ""?>">
                                <label>Date of Birth (MM/DD/YYYY)<em>*</em></label>
                                <input type="hidden" id="age_count" name="age_count" value=''>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="ssn" id="ssn" type="text" class="form-control"   value="<?=$agent_res["dssn"]?>">
                                <label>SSN<em>*</em></label>
                              </div>
                            </div>    
                        </div>
                      </div>
                  <div class="all_data">
                    <div class="step-title">	
                      <h4>License Information</h4>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <input name="npn_number" id="npn_number" type="text" class="form-control"   value="<?=checkIsset($agent_res["npn"])?>">
                          <label>NPN Number<em>*</em></label>
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
                                <select name="license_state[<?=$rkey?>]" id="license_state_<?=$rkey?>"  class="select_class_<?=$rkey?> form-control" >
                                  <option value=""></option>
                                  <?php if ($allStateRes) {?>
                                    <?php foreach ($allStateRes as $state) {
                                      $hide_states=(!empty($selectedState)?array_diff($selectedState,$state):array());?>
                                    <option <?=in_array($state["name"],$resADocStatesArray) ? 'selected' : ''?> value="<?=$state["name"];?>" <?=in_array($state,$hide_states)?'disabled':'' ?>><?php echo $state['name']; ?></option>
                                    <?php }?>
                                  <?php }?>
                                </select>
                                <label>License State<em>*</em></label>
                                <div id="error_license_state_<?=$rkey?>" class="error_license_state"></div>
                              </div>
                            </div>
                              <div class="col-md-1">
                                <div class="form-group ">
                                  <input name="license_number[<?=$rkey?>]" id="license_number_<?=$rkey?>" type="text" class="form-control license_number"   value="<?=$doc["license_num"]?>">
                                  <label>License Number<em>*</em></label>
                                  <div id="error_license_number_<?=$rkey?>" class="error_license_number"></div>
                                </div>
                              </div>
                              <div class="col-md-1">
                                <div class="form-group ">
                                    <input type="text" name="license_active_date[<?=$rkey?>]" value="<?=(isset($doc["license_active_date"]) && $doc["license_active_date"] != "" && $doc["license_active_date"] != "0000-00-00" && strtotime($doc["license_active_date"]) > 0) ? date("m/d/Y", strtotime($doc["license_active_date"])) : ""?>" id="license_active_date_<?=$rkey?>" class="form-control license_active" />
                                      <label>License Active Date<em>*</em></label>
                                    <div id="error_license_active_date_<?=$rkey?>" class="error_license_active_date"></div>
                                  </div>
                              </div>
                              <div class="col-md-1">
                                <div class="form-group mn " id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
                                  <input name="license_expiry[<?=$rkey?>]" id="license_expiry_<?=$rkey?>" type="text" class="form-control license_expiry"  value="<?=(isset($doc["license_exp_date"]) && $doc["license_exp_date"] != "" && $doc["license_exp_date"] != "0000-00-00" && strtotime($doc["license_exp_date"]) > 0) ? date("m/d/Y", strtotime($doc["license_exp_date"])) : "Does Not Expire"?>" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "readonly='readonly'" : '' ?>>
                                  <label>License Expiration<em>*</em></label>
                                  <div id="error_license_expiry_<?=$rkey?>" class="error_license_expiry"></div>
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
                                    <div id="error_license_type_<?=$rkey?>" class="error_license_type"></div>
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
                                  <div id="error_licsense_authority_<?=$rkey?>" class="error_licsense_authority"></div>
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
                          <?php }?>
                          <div id="e_o_information" style="<?=checkIsset($resDoc['by_parent']) == 'Y' ? 'display:none;' : ''?>">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="e_o_amount" id="e_o_amount" type="text" class="form-control"  value="<?=(checkIsset($resDoc["e_o_amount"]) > 0 ? $resDoc["e_o_amount"] : "")?>">
                                <label>E&O Amount<em>*</em></label>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <input name="e_o_expiration" id="e_o_expiration" type="text" class="form-control"  value="<?=(!empty($resDoc["e_o_expiration"]) && $resDoc["e_o_expiration"] != "0000-00-00") ? date("m/d/Y", strtotime($resDoc["e_o_expiration"])) : ""?>">
                                <label>E&O Expiration (MM/DD/YYYY)<em>*</em></label>
                              </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group cdrop_wrapper">
                                  <div class="phone-control-wrap">
                                    <div class="phone-addon">
                                      <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload</span>
                                        <input type="file" class="gui-file" accept="application/pdf" id="e_o_document" name="e_o_document">
                                        <input type="text" class="gui-input" placeholder="<?=checkIsset($resDoc['e_o_document'])?>">
                                        <label>Upload E&O</label>
                                      </div>
                                    </div>
                                    <?php if (checkIsset($resDoc['e_o_document']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($resDoc['e_o_document']))) {?>
                                      <a href="<?php echo $AGENT_DOC_WEB . $resDoc['e_o_document']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
                                    <?php }?>
                                  </div>
                                </div>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                    </div>
                    <script type="text/javascript">
                      $(function(){
                        $("#e_o_by_parent").trigger("change");
                      });
                      trigger("#e_o_by_parent",function($this,e){
                        if($this.prop('checked')){
                          $("#eoDiv").find("em").hide();
                          $("#e_o_information").slideUp();
                        }else{
                          $("#eoDiv").find("em").show();
                          $("#e_o_information").slideDown();
                        }
                      },"change");
                    </script>
                    <div class="clearfix"></div>
                    <?php if(!in_array($_SESSION['agents']['agent_coded_level'],array("LOA"))){?>
                    <div id="bankInfoDiv">
                        <div class="step-title m-t-25">	
                          <h4>Commissions Bank Account</h4>
                        </div>                        
                        <div id="pearsonalAccountDiv" class="m-b-15" style="" >
                      		 <div class="personal_err">
                              <label>Account Type<em>*</em></label>
                              <label  class="radio-inline m-l-15">
                                <input type="radio" <?=checkIsset($resDirect["account_type"]) == "checking" ? "checked" : ""?> name="bank_account_type" value="checking" id="p_checking" class="form-control">
                               Checking</label>
                              <label class="radio-inline">
                                <input type="radio" <?=checkIsset($resDirect["account_type"]) == "savings" ? "checked" : ""?> name="bank_account_type" value="savings" id="p_savings" class="form-control">
                               Savings </label>
                            </div>
                        </div>
                    	<div class="row">    
                        <div class="col-sm-6">
                          <div class="form-group">
                            <input name="bankname" id="bankname" type="text" class="form-control"  value="<?=checkIsset($resDirect["bank_name"])?>">
                            <label>Bank Name<em>*</em></label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <input name="bank_rounting_number" maxlength="9" id="bank_rounting_number" type="text" class="form-control"  value="<?=checkIsset($resDirect["routing_number"])?>">
                            <label>Bank Routing Number<em>*</em></label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <input name="bank_account_number" id="bank_account_number" type="text" class="form-control"  value="<?=checkIsset($resDirect["account_number"])?>">
                            <label>Bank Account Number<em>*</em></label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <input name="bank_number_confirm" id="bank_number_confirm" type="text" class="form-control"  value="">
                            <label>Confirm Bank Account Number<em>*</em></label>
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
                                  <label>Upload W9</label>
                                </div>
                              </div>
                              <?php if (checkIsset($agent_res['w9_pdf']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($agent_res['w9_pdf']))) {?>
                                <!-- <div class="phone-addon" style="width:35px;"> <i class="fa fa-download fs20 text-red"></i> </div> -->
                                <a href="<?php echo $AGENT_DOC_WEB . $agent_res['w9_pdf']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
                              <?php }?>
                            </div>
                            <a class="red-link" href="<?=$AGENT_DOC_WEB?>W9_Fillable.pdf" download>Click to download W9</a>
                          </div>
                          <div id="business_w9_form_business-error" class="error error_preview"></div>
                        </div>
                      </div>
                    </div>
                    <?php }?>              
                    <hr>
                     <div class="clearfix ">
                     <div class="pull-left">
                      <p class="text-light-gray last_updated mn"><i>Last saved <?=$tz->getDate($agent_res['updated_at']);?>.</i></p>
                        <p class="text-light-gray" <?=$secondTabOpen == 'in active' ? '' : 'style="display:none"'?>>Review and click submit to have your account reviewed by an admin. Once reviewed, you will be notified via email by the administrative team on next steps to sign your contract and begin selling.</p>
                      </div>
                        <div class="pull-right">
                           <input type="submit" class="btn btn-info btn-draft-saver" value='Save'/ style="display:none">
                           <button type='button' id='btn_finish' class='btn btn-action btn btn-finish' name='finish'><?=$forceFullyAllow ? "Submit" : "Submit"?></button>
                          <a data-toggle="tab" href="#firststep" class='btn red-link back_to_first' >Back</a>
                        </div>
                      </div>
                    <div class="clearfix"></div>
                  </div>
                </div>
             </form>
          </div>
          <div id="thirdstep" class="tab-pane fade <?=checkIsset($thirdTabOpen)?>" data-contract="<?=$agent_res['status']?>">
            <?php if (in_array($agent_res["status"], array("Pending Contract"))) {?>
               <form id="self_enrollment_final" name="self_enrollment_final" action="<?=$AGENT_HOST?>/ajax_agent_remaining_contract.php" method="post">
                  <input type="hidden" name="agent_id" value="<?=$agent_res["id"]?>" />
                  <input type="hidden" name="step" value="3" />
                  <input name="is_draft" type="hidden" value="" />
                  <input name="is_force" type="hidden" value="<?=$forceFullyAllow ? 1 : 0?>" />
                  <div class="theme-form">
                        <div class="step-title">
                          <h4><?= $DEFAULT_SITE_NAME ?> Agent Agreement and Terms and Conditions</h4>
                        </div>
                        <div class="agreement_div" id="agreem">
                          <?=checkIsset($terms,'arr');?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="clearfix check_agree_div">
                          <label class="pr mn label-input">  
                            <input type="checkbox" name="check_agree" id="check_agree" value="Y">
                            I agree to Terms and  Conditions and  accept Agreement<em>*</em>
                          </label>
                        </div>
                     
                      <div class="clearfix"></div>
                      <!--Signature Pad Start  -->
                      <div id="error_signature-pad" class="pr">
                        <input type="text" style="opacity: 0;width: 0;height: 0" name="signature_data" value="" id="signature_data">
                      </div>
                      <div id="signature-pad" class="m-signature-pad" style="height:400px">
                        <div class="m-signature-pad--body">
                          <canvas></canvas>
                        </div>
                        <div class="m-signature-pad--footer">
                          <div class="description pull-left">Draw your signature above<em>*</em>
                            <p class="error"><span id="error_signature_data"></span></p>
                          </div>

                          <div class="pull-right">
                            <!-- <div class="col-xs-7">
                              <div class="form-group h-80">
                                <label class="control-label">SSN</label>
                                <input class="form-control tblur" id="signature_name" name="signature_name"  type="text" data-error="Name is required" >
                                <span class="highlight"></span> <span class="bar"></span>
                                <label class="control-label">Type Your Name<em>*</em></label>
                                <div id="signature_name_err" class="mid"><span></span></div>
                                <span class="error"><span id="error_signature_name"></span></span> </div>
                              </div> -->
                                <button type="button" class="btn red-link m-t-5" data-action="clear">Erase</button>
                            </div>

                          </div>
                        </div>
                        <!--Signature Pad End  -->
                      <hr>
                      <div class="text-center ">
                        <input type='button' class='btn btn-action btn-finish' name='finish' value='Complete Contract' />
                        <!-- <input type="button" class="btn red-link" value="Back" /> -->
                      </div>
                      <div class="clearfix"></div>
                    </div>
               </form>
            <?php }?>
          </div>
        </div>
      </div>
</div>
</div>
<div class="license_template license_tempmdsm  " style="display: none">	
  <div class="license_portion pr div_license_~i~ m-t-25"> 
    <div class="row seven-cols">
      <input type="hidden" name='hdn_license[~i~]' value="~i~" id='hdn_license_~i~'>
      <div class="col-md-1">
        <div class="form-group ">
          <select name="license_state[~i~]" id="license_state_~i~"  class="license_state select_class_~i~">
            <option value="" ></option>
            <?php if (!empty($allStateRes)) {?>
              <?php foreach ($allStateRes as $state) {?>
              <option value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
              <?php }?>
            <?php }?>
          </select>
          <label>License State<em>*</em></label>
          <div id="error_license_state_~i~" class="error_license_state"></div>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group  ">        
          <input name="license_number[~i~]" id="license_number_~i~" type="text" class="form-control license_number"   value="">
          <label for="license_number[~i~]">License Number<em>*</em></label>
          <div id="error_license_number_~i~" class="error_license_number"></div>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <input type="text" name="license_active_date[~i~]" id="license_active_date_~i~" class="form-control license_active" />
            <label for="license_active_date_~i~">License Active Date<em>*</em></label>
            <div id="error_license_active_date_~i~" class="error_license_active_date"></div>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group" id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
          <input name="license_expiry[~i~]" id="license_expiry_~i~" type="text" class="form-control license_expiry"  value="">
          <label for="license_expiry[~i~]">License Expiration<em>*</em></label>
          <div id="error_license_expiry_~i~" class="error_license_expiry"></div>
           <div class="clearfix m-t-5">
           <label for="license_not_expire[~i~]" class="text-red mn fs12">
            <input type="checkbox" name="license_not_expire[~i~]" id="license_not_expire_~i~" class="license_not_expire" data-id="~i~" value="Y">
          License does not expire</label>
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
              <div id="error_license_type_~i~" class="error_license_type"></div>
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
              <div id="error_licsense_authority_~i~" class="error_licsense_authority"></div>
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
<script type="text/javascript">
  $(function(){
    trigger(".add_more_license",function($this,e){
      index = parseInt($(".license_portion").length);
      $display_counter = parseInt($('#license_display_counter').val());
      $number=index+1;
      if($display_counter > index){
      $number = $display_counter + 1;
      }
      pos_number = $number;
      $("#agent_licenses").append($(".license_template").html().replace(/~i~/g,pos_number));
      $("#license_display_counter").val($number);
      
      $('.select_class_'+pos_number).addClass('form-control');
      $('.select_class_'+pos_number).selectpicker({ 
          container: 'body', 
          style:'btn-select',
          noneSelectedText: '',
          dropupAuto:false,
        });
      $(".div_license_"+pos_number+" :input").selectpicker('refresh');
      $(".license_expiry").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true,
          startDate:new Date()
      });

      $(".license_active").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true,
          /* startDate:new Date() */
      });
      $("input[type='checkbox']").uniform();
    });
    trigger(".remove_license",function($this,e){
      var id = $this.attr('data-id');
      var lid = $("#hdn_license_"+id).val();
      $("#ajax_loader").show();
      $.ajax({
        url : 'ajax_agent_remaining_contract.php',
        method: 'POST',
        data:{lid:lid,ajax_delete:"1",step:'ajax'},
        dataType: 'json',
        success : function(res){
          if(res.status == 'success') {
            $("#ajax_loader").hide();
            $this.parents(".license_portion").fadeOut("slow",function(){
              $(this).remove();
            });
            /* refreshLicense("#license_state"); */
          }else if(res.status == 'fail'){
            $("#ajax_loader").hide();
          }
        }
      });
    });

    /* trigger(".edit_license",function($this,e){
      var id = $this.attr('data-id');
      $("#edit_license_"+id).hide();
      $("#hidden_btn_"+id).show();
      // $(".div_license_"+id+" :input").removeAttr('disabled'); 
      $(".div_license_"+id+" :input").selectpicker('refresh');
    }); */

    trigger(".license_not_expire",function($this,e){
      var id = $this.attr('data-id');
      if($('#license_not_expire_'+id).is(":checked"))
        {
          $("#license_expiry_"+id).attr('readonly','readonly');
          $("#license_expiry_"+id).val("12/31/2099");
        }
      else
        {
          $("#license_expiry_"+id).removeAttr('readonly');
          $("#license_expiry_"+id).val("");
        }
    });

    /*trigger(".ajax_add_license",function($this,e){
      $("#ajax_loader").show();
      $(".error").html('').hide();
      // console.log($this.attr('data-id')); 
      var id = $this.attr('data-id');
      if($('#license_not_expire_'+id).is(":checked"))
        var license_not_expire = 'Y';
      else
        var license_not_expire = 'N';
      var license_expiry = $('#license_expiry_'+id).val();
      var license_number = $('#license_number_'+id).val();
      var license_active_date = $('#license_active_date_'+id).val();
      var license_state = $('#license_state_'+id).val();
      var license_type = $('#license_type_'+id).val();
      var licsense_authority = $('#licsense_authority_'+id).val();
      var lid = $("#hdn_license_"+id).val();
      var hdn_license = [id];
      // console.log(license_active_date);
      $.ajax({
        url : 'ajax_agent_remaining_contract.php',
        data : { license_expiry:license_expiry,license_not_expire:license_not_expire,license_number:license_number,license_active_date:license_active_date,license_state:license_state,license_type:license_type,license_type:license_type,licsense_authority:licsense_authority,hdn_license:hdn_license,is_ajax_license:1,step:'ajax',lid:lid},
        method: 'POST',
        dataType: 'json',
        success : function(res){
          if (res.status == 'success') {
            $("#ajax_loader").hide();
            $("#edit_license_"+id).show();
            $("#hidden_btn_"+id).hide();
            if(res.doc_id !== '')
              $("#hdn_license_"+id).val(res.doc_id);
            // $(".div_license_"+id+" :input").attr("disabled","disabled");
            //$("#license_state_"+id).removeAttr("disabled");
            // $(".div_license_"+id+" :input").selectpicker('refresh');
          }else if(res.status == 'fail'){
            $("#ajax_loader").hide();
            $.each(res.errors, function (key, val) {
              error = '<div id="' + key + '-error" class="error error_preview">' + val + '</div>';
              // $('#error_' + index).html(error).show();
              if (key.indexOf("license_number_"+id) != -1) {
                  $id = $("#error_" + key).html(error);
              } else if (key.indexOf("license_state_"+id) != -1) {
                  $id = $("#error_" + key).html(error);
              } else if (key.indexOf("license_expiry_"+id) != -1) {
                  $id = $("#error_" + key).html(error);
              } else if (key.indexOf("license_active_date_"+id) != -1) {
                  $id = $("#error_" + key).html(error);
              } else if (key.indexOf("license_type_"+id) != -1) {
                  $id = $("#error_" + key).html(error);
              }else if (key.indexOf("licsense_authority_"+id) != -1) {
                  $id = $("#error_" + key).html(error);
              } 
            });
          }
        }
      });
    }); */

  });
  $(function(){
  refreshLicense(".license_state");
});

/* trigger(".license_expiry", function(){
	$('#mdy_tooltip').tooltip("hide");
},"focus"); */
refreshLicense=function($id){
  /* $(".license_state").each(function(){
    /* if($(this).val() == undefined ? '' : $(this).val().trim()=="Texas"){
      $is_show_popup = "N";  
    } 
    $selectedState = $(this).val();
    $(".license_state").not($(this)).find('option[value="' + $selectedState + '"]').prop("disabled", true).hide();
  });   */
  $(".license_expiry").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
  $(".license_active").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
}
 /*trigger(".license_state",function($this,e){
  $is_show_popup = "Y"; 
  //check all license has texas license
  $selectedState = '';
  /* $(".license_state").each(function(){
    /* if($(this).val() == undefined ? '' : $(this).val().trim()=="Texas"){
      $is_show_popup = "N";  
    } 
    $selectedState = $(this).val();
    $(".license_state").not($(this)).find('option[value="' + $selectedState + '"]').prop("disabled", true).hide();
  });   */
  /* if ($is_show_popup=="Y") {
    $("#non-texas-modal").modal("show");
  } 

},"change");*/
</script>
<script type="text/javascript">

  $(document).ready(function() {
    
    $(".license_expiry").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        startDate:new Date()
    });

    $(".license_active").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        /* startDate:new Date() */
    });

    $("#e_o_expiration").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true,
          startDate:new Date()
      });
		 $('[data-toggle="tooltip"]').tooltip();
    $(window).scroll(function() {
      var scroll = $(window).scrollTop();
      if (scroll >= 165) {
        $("#float_div").addClass("save_stick");
      } else {
        $("#float_div").removeClass("save_stick");
      }
    });
    /*$selectedStateArr = '<?=json_encode($selectedState);?>';
    $selectedStateArr = $.parseJSON($selectedStateArr);
    $.each($selectedStateArr, function (index, value) {
      $(".license_state").each(function(){        
        $(".license_state").not($(this)).find('option[value="' + value + '"]').prop("disabled", true).hide();
      });  
    });*/
  });

    $(".con_popup").colorbox({
      width: "980px",
      height: "550px",
      inline: true,
      href: "#confirm_popup"
    });
    //quote rght tab mngt
    $(document).on("blur keyup", "#dob", function() {
      $dob = $(this).val();
      if ($dob.indexOf('_') == -1) {
        count_age($dob);
      }
    });
    count_age = function($dob) {
      if ($dob != '' && $dob != "__/__/____") {
        $dob = new Date($dob);
        var today = new Date();
        var age = Math.floor((today - $dob) / (365.25 * 24 * 60 * 60 * 1000));
        if (!isNaN(age) && (age > 0)) {
          $("#age_count").val(age);
        } else {
          $("#age_count").val('');
        }
      } else {
        $("#age_count").val('');
      }
    };
    $(function() {
      $("#phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
      $("#accnt_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
      $("#admin_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
      $("#dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
      $("#e_o_expiration").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
      $("#zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
      $("#accnt_zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
      $("#business_zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
      $("#ssn").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
      $("#business_taxid").inputmask({"mask": "99-9999999",'showMaskOnHover': false});
    });
   /* trigger("#license_state", function($this) {
      
    }, "change");*/
    function refreshCurrencyFormatter(){
      $("#e_o_amount").formatCurrency({
        colorize: true,
        negativeFormat: '-%s%n',
        roundToDecimalPlace: 0
      });
    }
    $(function(){
      refreshCurrencyFormatter();
    });
    $('#e_o_amount').blur(function() {
        // $('#formatWhileTypingAndWarnOnDecimalsEnteredNotification2').html(null);
        refreshCurrencyFormatter();
      })
      .keyup(function(e) {
        var e = window.event || e;
        var keyUnicode = e.charCode || e.keyCode;
        if (e !== undefined) {
          switch (keyUnicode) {
            case 16:
              break; // Shift
            case 17:
              break; // Ctrl
            case 18:
              break; // Alt
            case 27:
              this.value = '';
              break; // Esc: clear entry
            case 35:
              break; // End
            case 36:
              break; // Home
            case 37:
              break; // cursor left
            case 38:
              break; // cursor up
            case 39:
              break; // cursor right
            case 40:
              break; // cursor down
            case 78:
              break; // N (Opera 9.63+ maps the "." from the number key section to the "N" key too!) (See: http://unixpapa.com/js/key.html search for ". Del")
            case 110:
              break; // . number block (Opera 9.63+ maps the "." from the number block to the "N" key (78) !!!)
            case 190:
              break; // .
            default:
              $(this).formatCurrency({
                colorize: true,
                negativeFormat: '-%s%n',
                roundToDecimalPlace: -1,
                eventOnDecimalsEntered: true
              });
          }
        }
      })
      /*.bind('decimalsEntered', function(e, cents) {
        if (String(cents).length > 2) {
          var errorMsg = 'Please do not enter any cents (0.' + cents + ')';
          $('#formatWhileTypingAndWarnOnDecimalsEnteredNotification2').html(errorMsg);
          log('Event on decimals entered: ' + errorMsg);
        }
      })*/;
    $('.circle-tab a').on('shown.bs.tab', function(event){
      //not required to edit this code, its generalized code
      $currentId=$(this).attr("href");
      $percentage=$(this).attr("data-pecentage");
      $("[href='"+$currentId+"']").parent("li").prevAll().addClass("complete");
      $("[href='"+$currentId+"']").parent("li").nextAll().removeClass("complete");
      $(".progress-bar").css({"width":$percentage});
      tabOpenActions($(this).attr("href"),$(this));
    });
    tabOpenActions=function($tabName,$this){
      if($tabName=="#thirdstep"){
        // alert($this.attr("data-contract"));
        if($this.attr("data-contract")=="Pending Contract"){
          signaturePadInit();
        }
      }
    };
    $(function() {
      if ($("#thirdstep").hasClass("in")) {
        if($("#thirdstep").attr("data-contract")=="Pending Contract"){
          signaturePadInit();
        }
      }
    });
		if ($("#thirdstep").hasClass("in")) {
        if($("#thirdstep").attr("data-contract")=="Pending Contract"){
          signaturePadInit();
        }
      }

    function signaturePadInit() {
      $("#signature_data").val("");
      wrapper = document.getElementById("signature-pad");
      clearButton = wrapper.querySelector("[data-action=clear]");
      savePNGButton = wrapper.querySelector("[data-action=save-png]");
      saveSVGButton = wrapper.querySelector("[data-action=save-svg]");
      canvas = wrapper.querySelector("canvas"), signaturePad;
      signaturePad = new SignaturePad(canvas);
      resizeCanvas();
      if (!(signaturePad.isEmpty())) {
        $("#signature_data").val(signaturePad.toDataURL());
      }
      clearButton.addEventListener("click", function(event) {
        signaturePad.clear();
        $("#signature_name").val("");
        $("#signature_data").val("");
      });
    }
    $(document).on("change keydown",".agent_contract_popup input,.agent_contract_popup select",function(){         
      try{
      if($(this).attr("name")=="socialMedia[]"){
        $("#error_social_media").html("");
      } else if(($(this).attr("id").indexOf("license_state"))!=-1){
        $("#error_"+$(this).attr("id")).html("");
      } else if(($(this).attr("id").indexOf("license_number"))!=-1){
        $("#error_"+$(this).attr("id")).html("");
      } else if(($(this).attr("id").indexOf("license_expiry"))!=-1){
        $("#error_"+$(this).attr("id")).html("");
      } else if(($(this).attr("id").indexOf("physical_license"))!=-1){
        $("#error_"+$(this).attr("id")).html("");
      }else{
        $("#"+$(this).attr("name")+"-error").remove();
      }
    }catch(e){
      console.warn(e)
    }
    });
    trigger(".logout_div a",function(){
      if($("body").hasClass('iframe')){
        window.parent.location.href="logout.php";
      }else{
        window.location.href="logout.php";
      }
    });
    $(function(){
      $(".circle-tab li.active a").trigger("shown.bs.tab");
      <?php if (in_array(checkIsset($agent_res["is_contract_approved"]), array("Pending Resubmission"))) {
          if (!empty($rejection_text_new)) {
            ?>
              // $("#rejectModal").modal("show");
              $("#rejectModal").modal({show:true, backdrop: 'static', keyboard: false})
        <?php
              }
        }?>
      <?php if (in_array(checkIsset($agent_res["is_contract_approved"]), array("Approved"))) {
            ?>
              $("#approvedModal").modal({show:true, backdrop: 'static', keyboard: false})
        <?php } ?>

    });
    trigger("#needhelpfloat_button",function(){
      $("#helpmodal").modal("show");
    });
    /*trigger("#enrollment_profile",function(){
      $display_success_msg=1;
      $("#div_profile_image #left_profile-image-dropzone").trigger("click");
    });*/
    </script>
<div id="rejectModal" class="modal fade enrollment_model" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    	 <div class="modal-body">
		     <div class="login-alert-modal">
		     	  <div class="media br-n pn mn">
				    <div class="media-left"> <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left" height="105px"> </div>
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
    <!-- Modal content-->
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
				      <div class="text-center m-t-20"><a data-toggle="tab" href="#thirdstep" data-dismiss="modal" class="red-link">Next</a>
				    </div>
				  </div>
		     </div>
 </div>
    </div>

  </div>
</div>
<?php //include_once "help_modal.inc.php";?>
<div class="modal fade profile_img_modal" id="contract_trigger_cropper">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="modal_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
                <h4 class="modal-title">Crop Image</h4>
            </div>
            <div class="modal-body">
                <div id="contract_cropper_image">
                    <div class="img-container"> <img id="pro_image"> </div>
                    <div class="text-center m-t-30 m-b-30 contract-docs-buttons">
                        <input type="hidden" name="cropper_dropzone_type" id="cropper_dropzone_type">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default" data-method="zoom" data-option="0.1" title="Zoom In"> <span class="docs-tooltip"> <span class="fa fa-search-plus"></span> </span> </button>
                            <button type="button" class="btn btn-default" data-method="zoom" data-option="-0.1" title="Zoom Out"> <span class="docs-tooltip"> <span class="fa fa-search-minus"></span> </span> </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default" data-method="rotate" data-option="-45" title="Rotate Left"> <span class="docs-tooltip"> <span class="fa fa-rotate-left"></span> </span> </button>
                            <button type="button" class="btn btn-default" data-method="rotate" data-option="45" title="Rotate Right"> <span class="docs-tooltip"> <span class="fa fa-rotate-right"></span> </span> </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default" data-method="scaleX" data-option="-1" title="Flip Horizontal"> <span class="docs-tooltip"> <span class="fa fa-arrows-h"></span> </span> </button>
                            <button type="button" class="btn btn-default" data-method="scaleY" data-option="-1" title="Flip Vertical"> <span class="docs-tooltip"> <span class="fa fa-arrows-v"></span> </span> </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default" data-method="reset" title="Reset"> <span class="docs-tooltip"> <span class="fa fa-refresh"></span> </span> </button>
                        </div>
                        <div class="btn-group btn-group-crop">
                            <button type="button" class="btn btn-info " data-method="saveCropperImage">Save </button>
                        </div>
                        <div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true" aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="getCroppedCanvasTitle">Cropped</h4>
                                    </div>
                                    <div class="modal-body"></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <a class="btn btn-primary" id="download" href="javascript:void(0);" download="cropped.jpg">Download</a> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
        $("#exit_business_img").click(function () {
            $("#enrollment_profile .profile-dropzone").click();
        });

        <?php 
        $currentImage=$POWERED_BY_LOGO;
        if (file_exists($AGENTS_BRAND_ICON . $contract_business_image) && $contract_business_image != "") {
          $currentImage=$HOST . '/uploads/agents/brand_icon/' . $contract_business_image;
        }?>
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
                   /* var editButton = Dropzone.createElement("<a href='javascript:void(0)' class='dz-edit'><i class='h-edit'></i></a>");
                    file.previewElement.appendChild(editButton);
                    var removeButton = Dropzone.createElement("<a href='javascript:void(0)' class='dz-trash'>Remove File</a>");
                    file.previewElement.appendChild(removeButton);
                    editButton.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $("#cropper_dropzone_type").val("profile");
                    });
                    removeButton.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        currentDropzone1.removeFile(file);
                    });*/

                });

            }, thumbnail: function (file, dataUrl) {
                if (this.options.customErrorHandlingCode == 200) {
                    /*if ($("#profile_action_image").val() != 'mock'){
                        $('#contract_profile_image_tmp_name').val(dataUrl);
                    }*/
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
                        url: 'ajax_update_rep_business_picture.php?id=<?php echo $_SESSION['agents']['id'] ?>&is_enroll=Y',
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

//Cropper Code Start
        $image = null;
        function contractInitCropper() {
            $image = $('#pro_image');
            $image.cropper({
                viewMode: 1,
                dragMode: 'move',
                aspectRatio: 4 / 1.5,
                restore: false,
                guides: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                zoomOnWheel: true,
                zoomOnTouch: true,
                preview: '.img-preview',
            });

            // Buttons
            if (!$.isFunction(document.createElement('canvas').getContext)) {
                $('#contract_trigger_cropper button[data-method="getCroppedCanvas"]').prop('disabled', true);
            }

            if (typeof document.createElement('cropper').style.transition === 'undefined') {
                $('#contract_trigger_cropper button[data-method="rotate"]').prop('disabled', true);
                $('#contract_trigger_cropper button[data-method="scale"]').prop('disabled', true);
            }
            // Download

        }
// Methods
        $('.contract-docs-buttons').on('click', '[data-method]', function () {
            var $this = $(this);
            var data = $this.data();
            var $target;
            var result;
            var obj;
            if ($this.prop('disabled') || $this.hasClass('disabled')) {
                return;
            }

            if ($image.data('cropper') && data.method) {
                data = $.extend({}, data); // Clone a new one

                if (typeof data.target !== 'undefined') {
                    $target = $(data.target);

                    if (typeof data.option === 'undefined') {
                        try {
                            data.option = JSON.parse($target.val());
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }

                if (data.method === 'rotate') {
                    $image.cropper('clear');
                }

                result = $image.cropper((data.method == "saveCropperImage" ? "getCroppedCanvas" : data.method), data.option, data.secondOption);

                if (data.method === 'rotate') {
                    $image.cropper('crop');
                }

                switch (data.method) {
                    case 'scaleX':
                    case 'scaleY':
                        $(this).data('option', -data.option);
                        break;
                    case 'saveCropperImage':
                        if (result) {
                            $('#contract_profile_image_tmp_name').val(result.toDataURL('image/jpeg'));
                            $('#enrollment_profile .dz-details img').attr('src', $('#contract_profile_image_tmp_name').val());
                            $('#enrollment_profile .profile-dropzone').css('background', "url("+$('#contract_profile_image_tmp_name').val()+") no-repeat scroll center center /100% 100%;height:100px;");
                            $.ajax({
                                url: 'ajax_update_rep_business_picture.php?id=<?php echo $_SESSION['agents']['id'] ?>&is_enroll=Y',
                                data: 'profile_picture=' + result.toDataURL('image/jpeg'),
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
                            $("#contract_trigger_cropper").modal('hide').on("shown.bs.modal", function () {
                                setTimeout(contractInitCropper, 600);
                            }).on("hidden.bs.modal", function () {
                                $image.cropper("destroy");
                            });

                            //}

                        }
                        break;
                    case 'getCroppedCanvas':
                        if (result) {

                            // Bootstrap's Modal
                            $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);

                            if (!$download.hasClass('disabled')) {
                                $download.attr('href', result.toDataURL('image/jpeg'));
                            }
                        }

                        break;
                }

                if ($.isPlainObject(result) && $target) {
                    try {
                        $target.val(JSON.stringify(result));
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }
        });

        function contractOpenCropModal(dataUrl) {
            $("#contract_trigger_cropper").modal("show").on('shown.bs.modal', function () {
                $("#contract_trigger_cropper").attr("data-modalType", $("#cropper_dropzone_type").val());
                $("#pro_image").attr('src', dataUrl);
                setTimeout(contractInitCropper, 600);
            }).on('hidden.bs.modal', function () {
                $("#pro_image").cropper("destroy");
            });
        }
        //Cropper Code End
    });
  function delete_business_image() {
    if (confirm('Are you sure you want to delete business logo?')) {
        $.ajax({
            url: 'delete_business_image.php?id=<?=$agent_res["id"]?>',
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
                    var default_business_image = '<?=$POWERED_BY_LOGO?>';
                    $("#enrollment_profile .profile-dropzone").attr('style', 'background:url('+default_business_image+') no-repeat scroll center center /100% 100%;height:100px;');
                    $(".pro_link_div").hide();
                    $('#enrollment_profile .dropzone-previews').empty();
                    setNotifySuccess(res.message);
                }
            }
        });
    }
  }
</script>
<script type="text/javascript">
$(document).ready(function() {
  $('.back_to_first').click(function () {
      $('#fTabStep').addClass('active').attr('aria-expanded','true');
      $('#sTabStep').removeClass('active').attr('aria-expanded','false');
  });

  $("#business_address #address").unbind();
  <?php if($SITE_ENV == 'Live') { ?>
    setTimeout(function(){
      initAutocompleteAgency();
      initAutocomplete();
    },3000);
  <?php } ?>

  setInterval(function() {
    $("#btn_finish").attr("disabled", true);
    $("#btn_finish").html('<i class="fa fa-spinner fa-spin"></i> Loading');
    $(".btn-finish:visible").parents('form').find(".btn-draft-saver").trigger("click");
  }, 60000);

  setInterval(function() {
    agentLicenseRefresh();
  }, 63000);
var status = "<?php echo !empty($checkThirdStp) ? $checkThirdStp : '' ?>";
if(status !== '' && status === 'Pending Approval'){
  $("#bap1").show();
  $.colorbox({
    inline: true,
    width: "525px",
    height: "205px",
    fixed:true,
    overlayClose: false,
    closeButton: false,
    href: "#bap1",
  });
}
});
$(document).on('click','.sign_out',function(e){
  /* window.redirect('<?=$AGENT_HOST?>'+'/logout.php'); */
  window.location = '<?=$AGENT_HOST?>' + '/logout.php';
});

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
</script>