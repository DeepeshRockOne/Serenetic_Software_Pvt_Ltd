<form action="ajax_update_agent_account_detail.php" name="account_detail" id="account_detail" method="POST">
    <input type="hidden" name="is_valid_address" id="is_valid_address" value="Y">
    <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
    <input type="hidden" name="is_agency_address_ajaxed" id="is_agency_address_ajaxed" value="">
    <input type="hidden" name="is_address_verified" id="is_address_verified" value="<?=$row['is_address_verified']?>">
    <input type="hidden" name="agent_id" id="agent_id" value="<?=$row['id']?>">
    <input type="hidden" name="agent_id_" id="agent_id_" value="<?=$row['_id']?>">
    <input type="hidden" name="ip_group_count" value="1" id="ip_group_count">
    <input type="hidden" name="ip_display_counter" value="0" id="ip_display_counter">
    <div >
    <p class="agp_md_title">Account</p>
    <div class="theme-form">
        <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
            <select class="form-control account_type" name="account_type">
                <option value="" disabled selected hidden> </option>
                <option value="Business" <?=($row['account_type'] == 'Business') ? "selected='selected'" : ''?>>Agency</option>
                <option value="Personal" <?=($row['account_type'] == 'Personal') ? "selected='selected'" : ''?>>Agent</option>
            </select>
            <label>Account Type</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <input type="text" class="form-control" name="company" id="company" value="<?=$row['company']?>" onkeypress="return block_special_char(event)" />
            <label>Company</label>
            <p class="error" id="error_company"></p>
            </div>
        </div>
        </div>
        <div id="BusinessDiv" <?php echo $row['account_type'] == 'Business' ? '' : 'style="display:none"' ?>>
        <p class="agp_md_title">Agency</p>
        <div class="row">
            <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" id="business_name" name="business_name" value="<?=$row['company_name']?>" />
                <label>Agency Legal Name</label>
                <p class="error" id="error_business_name"></p>
            </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="business_address" id="business_address" value="<?=$row['company_address']?>" />
                <label>Address</label>
                <p class="error" id="error_business_address"></p>
                <input type="hidden" name="old_business_address"  value="<?=$row['company_address']?>">
            </div>
            </div>
            <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="business_address2" id="business_address2" value="<?=$row['company_address_2']?>" onkeypress="return block_special_char(event)" />
                <label>Address 2 (suite, apt)</label>
                <p class="error" id="error_business_address2"></p>
            </div>
            </div>
            <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="business_city" id="business_city" value="<?=$row['company_city']?>" />
                <label>City</label>
                <p class="error" id="error_business_city"></p>
            </div>
            </div>
            <div class="col-sm-6">
            <div class="form-group">
                <select name="business_state" id="business_state"  class="form-control" >
                    <option value=""></option>
                    <?php if ($allStateRes) {?>
                    <?php foreach ($allStateRes as $state) {
                        $hide_states=(!empty($selectedState)?array_diff($selectedState,$state):array());?>
                    <option <?=$state["name"] == $row['company_state'] ? 'selected' : ''?> value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
                    <?php }?>
                    <?php }?>
                </select>
                <label>State<em>*</em></label>
                <p class="error" id="error_business_state"></p>
            </div>
            </div>
            <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="business_zipcode" id="business_zipcode" value="<?=$row['company_zip']?>" />
                <label>Zip Code</label>
                <p class="error" id="error_business_zipcode"></p>
                <input type="hidden" name="old_business_zipcode" value="<?=$row['company_zip']?>">
            </div>
            </div>
            <div class="col-sm-6">
            <div class="form-group">
                <input type="text" class="form-control" name="business_taxid" id="business_taxid" value="<?=$row['tax_id']?>" />
                <label>Business Tax ID (EIN)</label>
                <p class="error" id="error_business_taxid"></p>
            </div>
            </div>
        </div>
        </div>
        <div id="PersonalDiv">
            <p class="agp_md_title">Principal Agent</p>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control" id="fname" name="fname" value="<?=$row['fname']?>" />
                    <label>First Name</label>
                    <p class="error" id="error_fname"></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control" id="lname" name="lname" value="<?=$row['lname']?>" />
                    <label>Last Name</label>
                    <p class="error" id="error_lname"></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control no_space" name="email" value="<?=$row['email']?>" />
                    <label>Email</label>
                    <p class="error" id="error_email"></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control" name="cell_phone" id="cell_phone" value="<?=$row['cell_phone']?>" />
                    <label>Phone</label>
                    <p class="error" id="error_cell_phone"></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control" name="address" id="address" value="<?=$row['address']?>" />
                    <label>Address</label>
                    <p class="error" id="error_address"></p>
                    <input type="hidden" name="old_address" value="<?=$row['address']?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control" name="address_2" id="address_2" value="<?=$row['address_2']?>" onkeypress="return block_special_char(event)" />
                    <label>Address 2 (Suite, Apt)</label>
                    <p class="error" id="error_address_2"></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control" name="city" id="city" value="<?=$row['city']?>" />
                    <label>City</label>
                    <p class="error" id="error_city"></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <select name="state" id="state"  class="form-control" >
                        <option value=""></option>
                        <?php if ($allStateRes) {?>
                        <?php foreach ($allStateRes as $state) {
                            $hide_states=(!empty($selectedState)?array_diff($selectedState,$state):array());?>
                        <option <?=$state["name"] == $row['state'] ? 'selected' : ''?> value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
                        <?php }?>
                        <?php }?>
                    </select>
                    <label>State<em>*</em></label>
                    <p class="error" id="error_state"></p>
                    <!-- <input type="text" class="form-control" name="state" value="<?=$row['state']?>" />
                    <label>State</label>
                    <p class="error"><span id="error_state"></span></p> -->
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?=$row['zip']?>" />
                    <label>Zip Code</label>
                    <p class="error" id="error_zipcode"></p>
                    <input type="hidden" name="old_zipcode" value="<?=$row['zip']?>">
                    </div>
                </div>
                   <div class="col-sm-6">
                      <div class="form-group">
                        <div class="password_unlock">
                          <div style="display:none" id="ssn_popup" class="password_popup_content">
                            <div class="phone-control-wrap">
                              <div class="phone-addon"><input type="password" class="form-control" name="" id="showing_ssn"></div>
                              <div class="phone-addon w-65"><button type="button" class="btn btn-info" id="show_ssn">Unlock</button></div>
                            </div>
                          </div>
                        </div>
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <div class="form-group">
                              <input class="form-control" id="display_ssn" readonly='readonly' value="<?= secure_string_display_format($row['dssn'], 4); ?>">
                              <label id="display_ssn_label">SSN</label>
                              <input type="text" class="form-control" id="ssn" name="ssn" value="" style="display:none" />
                              <label id="ssn_label" style="display:none">SSN</label>
                              <input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value='N'/>
                            </div>
                          </div>
                          <div class="phone-addon w-30">
                            <div class="m-b-25">
                              <a href="javascript:void(0)" id="edit_ssn" class="text-action icons" style="display:block"><i
                              class="fa fa-edit fa-lg"></i></a>
                              <a href="javascript:void(0)" id="cancel_ssn" class="text-action icons" style="display:none">
                              <i class="fa fa-remove fa-lg"></i></a>
                            </div>
                          </div>
                          <div class="phone-addon w-30">
                            <div class="m-b-25">
                              <a href="javascript:void(0);" id="click_to_show_ssn"><i class="fa fa-eye fa-lg"></i></a>
                            </div>
                          </div>
                          <p class="error" id="error_ssn"></p>
                            </div>
                          </div>
                      <!-- <div class="form-group">
                        <input type="text" class="form-control" maxlength='9' name="ssn" id="ssn" value="<?=str_replace(range(0,5),'*',$row['dssn'])?>" />
                        <label>SSN</label>
                          <p class="error"><span id="error_ssn"></span></p>
                      </div> -->
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                           <input type="text" name="dob" id="dob" value="<?=getCustomDate($row['birth_date'])?>" class="form-control">
                           <label>DOB</label>
                           <p class="error" id="error_dob"></p>
                        </div>
                    </div>
                <div class="clearfix"></div>
                
            </div>
            <p class="agp_md_title">Login Security</p>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="password" id="password" name="password" value="" class="form-control"  maxlength="20" 
                        onblur="check_password(this, 'password_err', 'err_password' , event, 'input_validation');" 
                        onkeyup="check_password_Keyup(this, 'password_err', 'err_password' , event);">
                        <label>Password</label>
                        <div id="password_err" class="mid"><span></span></div>
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
                        <div id="c_password_err" class="mid"><span></span></div>
                        <p class="error" id="error_c_password"></p>
                    </div>
                </div>
                <?php echo generate2FactorAuthenticationUI($row); /*<div class="col-sm-6">
                   <div class="phone-control-wrap m-b-25">

                      <div class="phone-addon text-left">
                         <strong>Two-Factor Authentication (2FA):</strong><br>
                         Two-factor authentication is an extra layer of security on login designed to ensure that user is the only person who can access their account, even if someone knows their password.
                      </div>
                      <div class="phone-addon w-90">
                            <div class="custom-switch">
                               <label class="smart-switch">
                                  <input type="checkbox" class="js-switch" name="is_2fa" id="is_2fa" <?=$row['is_2fa']=='Y' ? 'checked' : ''?> value="Y" />
                                  <div class="smart-slider round"></div>
                               </label>
                            </div>
                      </div>
                   </div>
                   <div class="phone-control-wrap">
                      <div class="phone-addon text-left">
                         <strong>IP Address Restriction:</strong><br>
                         IP restrictions allow user to specify which IP addresses have access to sign in to their account. We recommend using IP restrictions if user desires to access account when they are in office, mobile, etc.
                      </div>
                      <div class="phone-addon w-90">
                            <div class="custom-switch">
                               <label class="smart-switch">
                                  <input type="checkbox" class="js-switch" name="is_ip_restriction" id="is_ip_restriction" <?=$row['is_ip_restriction']=='Y' ? 'checked' : ''?> value="Y" />
                                  <div class="smart-slider round"></div>
                               </label>
                            </div>
                      </div>
                   </div>
                </div>
                <?php 
                   $allowed_ip_res = array();
                   if($row['is_ip_restriction'] == 'Y' && !empty($row['allowed_ip'])) {
                       $allowed_ip_res = explode(',',$row['allowed_ip']);
                   
                   }
                   ?>
                <div class="clearfix"></div>
                <div class="ip_address_div m-t-25" style="<?=$row['is_ip_restriction']=='Y' ? '' : 'display: none;'?>">
                   <div class="col-sm-5 col-sm-offset-1">
                      <div id="ip_address_row_div">
                         <?php 
                            if(!empty($allowed_ip_res)) {
                                foreach ($allowed_ip_res as $key => $allowed_ip) {
                                    ?>
                         <div class="ip_address_row" id="ip_address_row_<?=$key?>" data-id="<?=$key?>">
                            <div class="phone-control-wrap">
                               <div class="phone-addon">
                                  <div class="form-group">
                                     <input type="text" name="allowed_ip_res[<?=$key?>]" class="form-control ip_input" value="<?=$allowed_ip?>">
                                     <label>IP Address</label>
                                     <p class="error text-left"><span id="error_ip_address_<?=$key?>"></span></p>
                                  </div>
                               </div>
                               <?php if($key > 0) { ?>
                               <div class="phone-addon">
                                <div class="form-group">
                                  <a href="javascript:void(0);" class="text-light-gray fw700 remove_ip_address"  data-id="<?=$key?>">X</a>
                              </div>
                               </div>
                               <?php } ?>
                            </div>
                         </div>
                         <?php
                            }
                            } else {
                            ?>
                         <div class="ip_address_row" id="ip_address_row_0" data-id="0">
                            <div class="form-group">
                               <input type="text" name="allowed_ip_res[0]" class="form-control ip_input"  value="<?=checkIsset($allowed_ip[0])?>">
                               <label>IP Address</label>
                               <p class="error"><span id="error_ip_address_0"></span></p>
                            </div>
                         </div>
                         <?php
                            }
                            ?>
                      </div>
                      <div class="clearfix"></div>
                      <div class="add_ip_address_row text-right">
                         <button id="add_ip_address" type="button" class="btn btn-action">+ IP Address
                         </button>
                      </div>
                   </div>
                </div> */ ?>
            </div>
        </div>
    </div>
    <hr />
    </div>
    <div role="tabpanel" class="tab-pane " id="agp_attributes">
    <p class="agp_md_title">Attributes</p>
    <div class="theme-form">
        <div class="clearfix m-b-10 attributes_btn"> 
        <a  href="javascript:void(0);" data-href="agent_tree_popup.php?agent_id=<?=$row['id']?>" class="btn btn-info m-b-10 agent_tree_popup">Agent Tree</a> 
        <?php if($row['id']==md5(1)){
            $tmpLevelName = "Root";
        }else{
            $tmpLevelName = $agentCodedRes[$row['agent_coded_id']]['level_heading'];
        }
        ?>
        <a href="agents_access_edit.php?agent_id=<?=$row['id']?>&lvl_name=<?= $tmpLevelName ?>" class="btn btn-info-light m-b-10 agents_access_edit">Access</a> 
        <a href="agents_account_managers.php?agent_id=<?=$row['id']?>" class="btn btn-info btn-outline m-b-10 agents_account_managers">Account Managers</a> 
        <a href="personal_production_report.php?agent_id=<?=$row['id']?>" class="btn btn-action btn-outline m-b-10 personal_production_report">Personal Production Report</a> 
        <!-- <a href="<?=$SIGNATURE_WEB.$row['signature_file']?>" class="btn btn-action btn-outline" download >Agent Agreement</a> -->
        <?php if($row["allow_download_agreement"] == "N"){ ?>
            <a href="javascript:void(0);" class="btn btn-action btn-outline m-b-10" onclick="setNotifyError('Agreement Not Found!');">Agent Agreement</a>
        <?php }else{ ?>
            <?php if(!empty($row) && $row['status'] == 'Active'){ ?>
                <a href="<?=$HOST?>/downloads3bucketfile.php?file_path=<?=urlencode($AGENT_AGREEMENT_CONTRACT_FILE_PATH)?>&file_name=<?=urlencode($row['agent_contract_file'])?>&user_id=<?=$row['id']?>&location=admin_agent_details" class="btn btn-action btn-outline m-b-10" >Agent Agreement</a>
            <?php } ?>
        <?php } ?>
    </div>
        <div class="row ">
        <div class="col-sm-6">
            <div class="form-group">
            <div class="phone-control-wrap">
                <div class="phone-addon">
                <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload</span>
                    <input type="hidden" name="w9_pdf" value="<?=checkIsset($row['w9_pdf'])?>" />
                    <input type="file" class="gui-file" id="w9_form_business" name="w9_form_business">
                    <input type="text" class="gui-input" placeholder="<?php echo  checkIsset($row['w9_pdf']) != '' ? $row['w9_pdf'] : 'Choose File'  ?>">
                    <label>W9</label>
                </div>
                </div>
                 <?php if (checkIsset($row['w9_pdf']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($row['w9_pdf']))) {?>
                <div class="phone-addon" style="width:35px;">
                <!-- <div class="phone-addon" style="width:35px;"> <i class="fa fa-download fs20 text-red"></i> </div> -->
                <a href="<?php echo $AGENT_DOC_WEB . $row['w9_pdf']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
             </div>
             <?php }?>
            </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <input type="text" class="form-control" name="npn_number" value="<?=$row['npn']?>" />
            <label>NPN Number</label>
            <p class="error" id="error_npn_number"></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <div class="phone-control-wrap">
                <div class="phone-addon">
                <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload</span>
                    <input type="hidden" name="chk_e_o_document" value="<?=checkIsset($row['w9_pdf'])?>" />
                    <input type="file" class="gui-file" id="e_o_document" name="e_o_document">
                    <input type="text" class="gui-input" placeholder="<?php echo  checkIsset($resDoc['e_o_document']) != '' ? $resDoc['e_o_document'] : 'Choose File'  ?>">
                    <label>E&O Document</label>
                </div>
                </div>
                <?php if (checkIsset($resDoc['e_o_document']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($resDoc['e_o_document']))) {?>
                <div class="phone-addon" style="width:35px;">
                <a href="<?php echo $AGENT_DOC_WEB . $resDoc['e_o_document']; ?>" title="View Document" class="phone-addon red-link" style="width:35px;font-size:20px;" target="_blank"><i class="fa fa-download"></i></a>
                </div>
                <?php }?> 
            </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <input type="radio" name="e_o_coverage" checked value="Y" id="e_o_yes" style="display: none">
            <input type="text" class="form-control" value="<?=$resDoc['e_o_amount']?>" name="e_o_amount" id="e_o_amount" onkeypress="return isNumberKey(event)" />
            <label>E&O Amount</label>
            <p class="error" id="error_e_o_amount"></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <input type="text" class="form-control" id="e_o_expiration" name="e_o_expiration" value="<?=checkIsset($resDoc['e_o_expiration']) ? getCustomDate($resDoc['e_o_expiration']) : '' ?>" />
            <label>E&O Expiration (MM/DD/YYYY)</label>
            <p class="error" id="error_e_o_expiration"></p>
            </div>
        </div>
        <?php if(!empty($carrier_res)){ ?>
                <?php foreach ($carrier_res as $k => $v) { ?>
                    <?php 
                    $selected_value = $pdo->selectOne("SELECT writing_number,GROUP_CONCAT(state) AS states FROM agent_writing_number wn LEFT JOIN agent_writing_states ws ON(ws.writing_id = wn.id AND ws.is_deleted='N') WHERE agent_id=:agent_id AND carrier_id=:c_id and wn.is_deleted='N'",array(":agent_id"=>$row['_id'],':c_id'=>$v['id']));
                    $selected_state = explode(",",$selected_value['states']); ?>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="text" name="writing_number[<?=$v['id']?>]" value="<?=checkIsset($selected_value['writing_number'])?>" id="writing_number_<?=$v['id']?>" class="form-control" />
                            <label><?=$v['name']?> Writing Number</label>
                            <span class="error" id="error_writing_number_<?=$v['id']?>"></span>
                        </div>
                    </div>
                  <?php
                      if(!empty($v['states']) && in_array($v['sale_type'],array('Just-In-Time','Pre-Sale'))){  $states = explode(',',$v['states']); ?>
                        <div class="col-sm-6">
                          <div class="form-group">
                                <select name="writing_state[<?=$v['id']?>][]" multiple="multiple" id="writing_state_<?=$v['id']?>" class="se_multiple_select has-value writing_state" >
                                <!-- <option value=""></option> -->
                                <?php foreach ($states as $state) { ?>
                                    <option value="<?=$state?>" <?=!empty($selected_state) && in_array($state,$selected_state) ? "selected" : '' ?> ><?=$state?></option>
                                <?php } ?>
                                </select>
                                <label><?=$v['name']?> Writing States</label>
                                <span class="error" id="error_writing_state_<?=$v['id']?>"></span>
                          </div>
                        </div>
                      <?php }else{ ?>
                          <div class="col-sm-6">
                          <div class="form-group">
                                <select name="writing_state[<?=$v['id']?>][]" multiple="multiple" id="writing_state_<?=$v['id']?>" class="se_multiple_select has-value writing_state" >
                                <!-- <option value=""></option> -->
                                <?php foreach ($allStateRes as $state) { ?>
                                    <option value="<?=$state['name']?>" <?=!empty($selected_state) && in_array($state['name'],$selected_state) ? "selected" : '' ?> ><?=$state['name']?></option>
                                <?php } ?>
                                </select>
                                <label><?=$v['name']?> Writing States</label>
                                <span class="error" id="error_writing_state_<?=$v['id']?>"></span>
                          </div>
                        </div>
                    <?php  }
                    ?>  
                <?php } ?>
              <?php } ?>
        </div>
        <!-- SPECIAL DISPLAY TEXT HTML START  -->
        <div class="clearfix m-b-20">
            <label class="fs14 label-input">
            <input type="checkbox" id="special_display" name="is_special_text_display" <?=$row['is_special_text_display'] == 'Y' ? 'checked' : ''?> value="<?=$row['is_special_text_display']?>">
            Check this box to display special text on top of member detail page</label>
        </div>
        <div class="pr m-b-20">
            <textarea class="form-control" rows="3" name="special_text_display" id="special_text_display" maxlength="140"><?=$row['special_text_display']?></textarea>
            <label>Display Text</label>
            <p class="error" id="error_special_text_display"></p>
        </div>
        <!-- SPECIAL DISPLAY TEXT HTML END  -->
        <p class="agp_md_title">State(s) Licensed</p>
        <?php include_once __DIR__ . '/../agent_license.php'; ?>
        <div class="m-t-20"></div>
        <div class="row" id="add_license_div">
        <div class="col-sm-12">
            <div class="pull-left"><a href="javascript:void(0);" class="btn btn-info add_more_license" id="add_more_license">+ License</a></div>
        </div>
        </div>
    </div>
    <hr />
    </div>
    <div role="tabpanel" class="tab-pane" id="agp_brand_links">
    <p class="agp_md_title">Branding and Vanity URL&nbsp;<i class="fa fa-info-circle text-blue" aria-hidden="true"></i></p>
    <p class="fw500 m-b-20" >This unique url allows your members to self enroll quickly and easily without having to login to your group portal. Please provide your custom vanity url.</p>
    <div class="clearfix m-b-20">
        <label class="fs14 label-input">
        <input type="checkbox" id="display_in_member" name="display_in_member" <?=$row['display_in_member'] == 'Y' ? 'checked' : ''?> value="<?=$row['display_in_member']?>">
        Check this box if you do not want display name, display phone, or display email shown as a point of contact.</label>
    </div>
    <div class="theme-form">
        <div class="row ">
        <div class="col-sm-6">
            <div class="form-group">
            <input type="text" class="form-control" value="<?=$row["public_name"]?>" name="public_name" id="public_name" />
            <label>Display Name</label>
            <p class="error" id="error_public_name"></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <input type="text" class="form-control" value="<?=$row["public_phone"]?>" name="public_phone" id="public_phone" />
            <label>Display Phone</label>
            <p class="error" id="error_public_phone"></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <input type="text" class="form-control no_space" value="<?=$row["public_email"]?>" name="public_email" id="public_email"  />
            <label>Display Email</label>
            <p class="error" id="error_public_email"></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <div class="input-group m-b-5"> <span class="input-group-addon fs14"><?= $DEFAULT_SITE_URL ?>/</span>
                <input  type="text" class="form-control" id="username" name="username" value="<?=$row["user_name"]?>" placeholder="">
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
            <a href="javascript:void(0);" class="red-link pn" style="word-break: break-all;"><?=$HOST?>/quote/<?=$row['user_name']?></a> 
            </div>
            </div>
        </div>
        <p class="fs16 fw500 m-b-20 m-t-20">Brand Customization</p>
        <div class="clearfix m-b-20">
        <label class="fs14 label-input">
            <input type="checkbox" id="is_branding" name="is_branding" <?=$row['is_branding'] == 'Y' ? 'checked' : ''?> value="Y">
            Check this box to allow personal branding of agent portal</label>
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
        <div class="text-center"> <button type="button" name="save_account" class="btn btn-action" id="save_account">Save</button> </div>
        <hr />
    </div>
    </div>
</form>

<div class="license_template license_tempmdsm  " style="display: none"> 
  <div class="license_portion pr div_license_~i~ "> 
    <div class="row seven-cols">
      <input type="hidden" name='hdn_license[~i~]' value="~i~" id='hdn_license_~i~'>
      <input type="hidden" name="edit[~i~]" value="~i~" id="ed_license__~i~" class="ed_license__~i~">
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
          <p class="error error_license_state" id="error_license_state_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group  ">        
          <input name="license_number[~i~]" id="license_number_~i~" type="text" class="form-control license_number"   value="">
          <label for="license_number[~i~]">License Number<em>*</em></label>
          <p class="error error_license_number" id="error_license_number_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <input type="text" name="license_active_date[~i~]" id="license_active_date_~i~" class="form-control license_active" />
            <label for="license_active_date_~i~">License Active Date<em>*</em></label>
            <p class="error error_license_active_date" id="error_license_active_date_~i~"></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group  height_auto m-b-10" id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
          <input name="license_expiry[~i~]" id="license_expiry_~i~" type="text" class="form-control license_expiry"  value="">
          <label for="license_expiry[~i~]">License Expiration<em>*</em></label>
          <p class="error error_license_expiry" id="error_license_expiry_~i~"></p>
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
              <p class="error error_license_type" id="error_license_type_~i~"></p>
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
              <p class="error error_licsense_authority" id="error_licsense_authority_~i~"></p>
          </div>
      </div>
      <div class="col-md-1">
        <div class="form-group">
        <!--<a href="javascript:void(0)" class="edit_license btn red-link" style="display:none" id="edit_license_~i~" data-id="~i~" > Edit </a>
        <div class="form-group " id="hidden_btn_~i~"> -->
          <button type="button" class="btn btn-primary ajax_add_license" data-id="~i~">Save</button>
          <a href="javascript:void(0)" class="remove_license btn red-link"  data-id="~i~"> Delete </a>
        <!--</div>-->
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>  
  <div class="clearfix"></div>
</div>
<?=generateIPAddressUI()?>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script src="<?=$HOST?>/js/password_validation.js<?=$cache?>" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
    refreshCurrencyFormatter();
    $("#cell_phone,#via_mobile").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#accnt_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#public_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    $("#dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $("#e_o_expiration").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $("#zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
    $("#business_zipcode").inputmask({"mask": "99999",'showMaskOnHover': false});
    $("#ssn").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
    $("#business_taxid").inputmask({"mask": "99-9999999",'showMaskOnHover': false});
  });
$(document).on('focus','#address,#zipcode',function(){
   $("#is_address_ajaxed").val(1);
});
$(document).on('focus','#business_address,#business_zipcode',function(){
   $("#is_agency_address_ajaxed").val(1);
});
$(document).ready(function(){

    checkEmail();

    $(document).off('click','#click_to_show_ssn');
    $(document).on('click','#click_to_show_ssn',function(){
        if($("#display_ssn").val() != '<?=$row['dssn']?>') {
            $("#ssn_popup").show();
        } else {
            $("#display_ssn").val('<?= secure_string_display_format($row['dssn'], 4); ?>');
        }
    });
    $(document).off('click','#show_ssn');
    $(document).on('click','#show_ssn',function(){
        if($("#showing_ssn").val() === '5401') {
            $("#showing_ssn").val("");
            $("#ssn_popup").hide();
            $("#display_ssn").val('<?=$row['dssn'];?>');
        } else {
            $("#ssn_popup").hide();
        }
    });

    $("#e_o_expiration").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        // startDate: new Date()
    });
    
    refreshCurrencyFormatter();
    $(".writing_state").multipleSelect({});

    $('#edit_ssn').click(function () {
        $(this).hide();
        $('#display_ssn').hide();
        $('#display_ssn_label').hide();
        $('#ssn').show();
        $('#ssn_label').show();
        $('#is_ssn_edit').val('Y');
        $('#cancel_ssn').show();

    });

    $('#cancel_ssn').click(function () {
        $(this).hide();
        $('#display_ssn').show();
        $('#display_ssn_label').show();
        $('#ssn').hide();
        $('#ssn_label').hide();
        $('#is_ssn_edit').val('N');
        $('#edit_ssn').show();
        $('#error_ssn').html('');
    });
    $('.agents_access_edit').colorbox({iframe: true, width: '450px', height: '630px'});
    $('.agents_account_managers').colorbox({iframe: true, width: '900px', height: '570px'});
    $('.personal_production_report').colorbox({iframe: true, width: '900px', height: '800px'});
    // $('.agent_tree_popup').colorbox({iframe:true, width: '900', height: '650'});

});
$(document).off('click','#save_account');
$(document).on('click','#save_account',function(e){
    $is_address_ajaxed = $("#is_address_ajaxed").val();
    $is_agency_address_ajaxed = $("#is_agency_address_ajaxed").val();
    if($is_address_ajaxed == 1){
        updateAddress();
    }else if($is_agency_address_ajaxed == 1){
        updateAddress();
    }else{
        ajaxSaveAccountDetails();
    }
});

function updateAddress(){
   $.ajax({
      url : "ajax_update_agent_account_detail.php",
      type : 'POST',
      data:$("#account_detail").serialize(),
      dataType:'json',
      beforeSend :function(e){
         $("#ajax_loader").show();
         $(".error").html('');
      },success(res){
         $("#is_address_ajaxed").val("");
         if(res.agencyApi=="success"){
            $("#is_agency_address_ajaxed").val(1);
         }else{
            $("#is_agency_address_ajaxed").val("");
         }
         $("#ajax_loader").hide();
         $(".suggested_address_box").uniform();
         if(res.zip_response_status =="success"){
            
            $("#is_address_verified").val('N');
            if(res.agencyApi=="success"){
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                updateAddress();
            }else if(res.agencyApi==""){
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                ajaxSaveAccountDetails();
            }else{
                $("#business_state").val(res.state).addClass('has-value');
                $("#business_city").val(res.city).addClass('has-value');
                ajaxSaveAccountDetails();
            }
         }else if(res.address_response_status =="success"){
            if(res.agencyApi=="success"){
                $(".suggestedAddressEnteredName").html($("#fname").val()+" "+$("#lname").val());
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
            }else if(res.agencyApi==""){
                $(".suggestedAddressEnteredName").html($("#fname").val()+" "+$("#lname").val());
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
            }else{
                $(".suggestedAddressEnteredName").html($("#business_name").val());
                $("#business_state").val(res.state).addClass('has-value');
                $("#business_city").val(res.city).addClass('has-value');
            }
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
                        if(res.agencyApi=="success"){
                            $("#address").val(res.address).addClass('has-value');
                            $("#address_2").val(res.address2).addClass('has-value');
                        }else if(res.agencyApi==""){
                            $("#address").val(res.address).addClass('has-value');
                            $("#address_2").val(res.address2).addClass('has-value');
                        }else{
                            $("#business_address").val(res.address).addClass('has-value');
                            $("#business_address2").val(res.address2).addClass('has-value');
                        }
                        
                        $("#is_address_verified").val('Y');
                     }else{
                        $("#is_address_verified").val('N');
                     }
                     if(res.agencyApi=="success"){
                        updateAddress();
                     }else{
                        ajaxSaveAccountDetails();
                     }
                  },
            });
         }else if(res.status == 'success'){
            $("#is_address_verified").val('N');
            if(res.agencyApi=="success"){
                updateAddress();
            }else{
                ajaxSaveAccountDetails();
            }
         }else{
            $.each(res.errors,function(index,error){
               $("#error_"+index).html(error).show();
           });
         }
         $('#business_state').selectpicker('refresh');
         $('#state').selectpicker('refresh');
      }
   });
}

function ajaxSaveAccountDetails(){
formHandler($("#account_detail"),
        function() {
            $("#ajax_loader").show();
        },
        function(data) {
            $("#ajax_loader").hide();
            $(".error").hide();
            console.log(data);
            if (data.status == 'success') {
                setNotifySuccess("Agent detail updated successfully!");
                // window.location = 'agent_detail_v1.php?id='+$("#agent_id").val();
                
            } else if (data.status == "fail") {
                setNotifyError("Oops... Something went wrong please try again later");
            } else {
                $(".error").hide();
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
        });
}
getDocumentLink = function($link) {
    return '<a href="' + $link + '" title="View Document" target="_blank"><span class="fa fa-paperclip"></a>';
}


$(document).on("change", ".account_type", function() {
    $value = $(this).val();
    $("#PersonalDiv").show();
    if ($value == "Personal") {
        $("#PersonalDiv").addClass("removeLines")
        $("#BusinessDiv").hide("slow");
        $(".all_data").show("slow");
        $("#personal[value='Personal']").trigger("click");
        $("[data-personal]").show("slow");
        $("[data-business]").hide("slow");
    } else if ($value == "Business") {
        $("#PersonalDiv").removeClass("removeLines")
        $("#BusinessDiv").show("slow");
        $(".all_data").show("slow");
        $("#business[value='Business']").trigger("click");
        $("[data-personal]").hide("slow");
        $("[data-business]").show("slow");
    }
});

$(document).on('focusin click keyup', '#username', function() {
    $('#username_info').show();
    var user_name = $(this).val();
    var user_email = $('#emails').val();
    var agent_id = $("#agent_id_").val();
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
            url: '<?=$AGENT_HOST?>' + "/check_already_username.php",
            data: {
                username: user_name,
                email: user_email,
                agent_id: agent_id
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

<?php 
    $currentImage='';
    if (file_exists($AGENTS_BRAND_ICON . $contract_business_image) && $contract_business_image != "") {
        $currentImage=$HOST . '/uploads/agents/brand_icon/' . $contract_business_image;
    }?>
    console.log('<?=$currentImage?>');
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
                var $agent_id = $("#agent_id").val();
                $.ajax({
                    url: '<?=$ADMIN_HOST?>/ajax_update_rep_business_picture.php?id='+$agent_id+'&is_enroll=Y',
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
            $('#pro_image').attr('src', "");
            $('#pro_image').cropper("destroy");
            var _ref;
        }
    });
    

function delete_business_image() {
    if (confirm('Are you sure you want to delete business logo?')) {
        var $agent_id = $("#agent_id").val();
        $.ajax({
            url: '<?=$ADMIN_HOST?>/delete_business_image.php?id='+$agent_id,
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
<?=generate2FactorAuthenticationJS();?>
  
</script>