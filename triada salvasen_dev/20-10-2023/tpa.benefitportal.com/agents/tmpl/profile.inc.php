<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>
<form action="ajax_update_agent_account_detail.php" name="account_detail" id="account_detail" method="POST">
    <input type="hidden" name="section" id="section" value="">
    <input type="hidden" name="agent_id" id="agent_id" value="<?= $agent_id ?>">
    <input type="hidden" name="agent_id_org" id="agent_id_org" value="<?= $agent_id_org ?>">
    <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
    <input type="hidden" name="is_agency_address_ajaxed" id="is_agency_address_ajaxed" value="">
    <input type="hidden" name="is_address_verified" id="is_address_verified" value="<?=$agent_row['is_address_verified']?>">
    <input type="hidden" name="ip_group_count" value="1" id="ip_group_count">
    <input type="hidden" name="ip_display_counter" value="0" id="ip_display_counter">
    <div class="container m-t-30">
        <div class="panel panel-default panel-block">
            <div class="panel-body">
                <ul class="nav nav-tabs tabs  customtab  fixed_tab_top" role="tablist">
                    <li class="active" role="presentation">
                        <a href="#account_tab" data-toggle="tab"
                           onclick="scrollToDiv($('#account_tab'), 0);" aria-expanded="true">Account</a>
                    </li>
                    <li role="presentation">
                        <a href="#products_tab" data-toggle="tab"
                           onclick="scrollToDiv($('#products_tab'), 0);"
                           aria-expanded="true">Products</a>
                    </li>
                    <li role="presentation">
                        <a href="#attribute_tab" data-toggle="tab"
                           onclick="scrollToDiv($('#attribute_tab'), 0);" aria-expanded="true">Attributes</a>
                    </li>
                    <li role="presentation">
                        <a href="#personal_brand" data-toggle="tab"
                           onclick="scrollToDiv($('#personal_brand'), 0);" aria-expanded="true">Personal
                            Brand & Links</a>
                    </li>
                    <?php if($IS_NOT_LOA_AGENT == true ) { ?>
                    <li role="presentation">
                        <a href="#commission_tab" data-toggle="tab"
                           onclick="scrollToDiv($('#commission_tab'), 0);" aria-expanded="true">Commissions</a>
                    </li>
                    <?php } ?>
                </ul>
                <div class="m-t-20">
                    <div role="tabpanel" class="tab-pane active" id="account_tab">
                        <p class="agp_md_title">Account</p>
                        <div class="theme-form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <select class="form-control account_type" name="account_type">
                                            <option value="" disabled selected hidden></option>
                                            <option value="Business" <?= ($agent_row['account_type'] == 'Business') ? "selected='selected'" : '' ?>>
                                                Agency
                                            </option>
                                            <option value="Personal" <?= ($agent_row['account_type'] == 'Personal') ? "selected='selected'" : '' ?>>
                                                Agent
                                            </option>
                                        </select>
                                        <label>Account Type</label>
                                    </div>
                                </div>
                            </div>
                            <div id="BusinessDiv" <?php echo $agent_row['account_type'] == 'Business' ? '' : 'style="display:none"' ?>>
                                <p class="agp_md_title">Agency</p>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="business_name" id="business_name"
                                                   value="<?= $agent_row['company_name'] ?>"/>
                                            <label>Agency Legal Name</label>
                                            <p class="error" id="error_business_name"></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="business_address" id="business_address" value="<?= $agent_row['company_address'] ?>"/>
                                            <label>Address</label>
                                            <p class="error" id="error_business_address"></p>
                                            <input type="hidden" name="old_business_address" value="<?= $agent_row['company_address'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="business_address2" id="business_address2" value="<?=$agent_row['company_address_2']?>" onkeypress="return block_special_char(event)" />
                                            <label>Address 2 (suite, apt)</label>
                                            <p class="error" id="error_business_address2"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="business_city" id="business_city" value="<?= $agent_row['company_city'] ?>"/>
                                            <label>City</label>
                                            <p class="error" id="error_business_city"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select name="business_state" id="business_state" class="form-control">
                                                <option value=""></option>
                                                <?php if ($allStateRes) { ?>
                                                    <?php foreach ($allStateRes as $state) {
                                                        $hide_states = (!empty($selectedState) ? array_diff($selectedState, $state) : array()); ?>
                                                        <option <?= $state["name"] == $agent_row['company_state'] ? 'selected' : '' ?>
                                                                value="<?= $state["name"]; ?>"><?php echo $state['name']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <label>State<em>*</em></label>
                                            <p class="error" id="error_business_state"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="business_zipcode"
                                                   id="business_zipcode" value="<?= $agent_row['company_zip'] ?>"/>
                                            <label>Zip Code</label>
                                            <p class="error" id="error_business_zipcode"></p>
                                            <input type="hidden" name="old_business_zipcode" value="<?= $agent_row['company_zip'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="business_taxid"
                                                   id="business_taxid" value="<?= $agent_row['tax_id'] ?>"/>
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
                                            <input type="text" class="form-control" name="fname" id="fname"
                                                   value="<?= $agent_row['fname'] ?>"/>
                                            <label>First Name</label>
                                            <p class="error" id="error_fname"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="lname" id="lname"
                                                   value="<?= $agent_row['lname'] ?>"/>
                                            <label>Last Name</label>
                                            <p class="error" id="error_lname"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control no_space" name="email"
                                                   value="<?= $agent_row['email'] ?>"/>
                                            <label>Email</label>
                                            <p class="error" id="error_email"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="cell_phone" id="cell_phone"
                                                   value="<?= $agent_row['cell_phone'] ?>"/>
                                            <label>Phone</label>
                                            <p class="error" id="error_cell_phone"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="address" id="address" 
                                                   value="<?= $agent_row['address'] ?>"/>
                                            <label>Address</label>
                                            <p class="error" id="error_address"></p>
                                            <input type="hidden" name="old_address" value="<?=$agent_row['address']?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                        <input type="text" class="form-control" name="address_2" id="address_2" value="<?=$agent_row['address_2']?>" onkeypress="return block_special_char(event)" />
                                        <label>Address 2 (Suite, Apt)</label>
                                        <p class="error" id="error_address_2"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="city" id="city" 
                                                   value="<?= $agent_row['city'] ?>"/>
                                            <label>City</label>
                                            <p class="error" id="error_city"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select name="state" id="state" class="form-control">
                                                <option value=""></option>
                                                <?php if ($allStateRes) { ?>
                                                    <?php foreach ($allStateRes as $state) {
                                                        $hide_states = (!empty($selectedState) ? array_diff($selectedState, $state) : array()); ?>
                                                        <option <?= $state["name"] == $agent_row['state'] ? 'selected' : '' ?>
                                                                value="<?= $state["name"]; ?>"><?php echo $state['name']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <label>State<em>*</em></label>
                                            <p class="error" id="error_state"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="zipcode" name="zipcode"
                                                   value="<?= $agent_row['zip'] ?>"/>
                                            <label>Zip Code</label>
                                            <p class="error" id="error_zipcode"></p>
                                            <input type="hidden" name="old_zipcode" value="<?=$agent_row['zip']?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="phone-control-wrap">
                                                <div class="phone-addon">
                                                    <div class="form-group">
                                                        <input class="form-control" id="display_ssn" readonly='readonly'
                                                               value="<?= secure_string_display_format($agent_row['dssn'], 4); ?>">
                                                        <label>SSN</label>
                                                        <input type="text" class="form-control" id="ssn" name="ssn"
                                                               value=""
                                                               style="display:none"/>
                                                        <input type="hidden" name="is_ssn_edit" id='is_ssn_edit'
                                                               value='N'/>
                                                    </div>
                                                </div>
                                                <div class="phone-addon w-30">
                                                    <div class="m-b-25">
                                                        <a href="javascript:void(0)" id="edit_ssn"
                                                           class="text-action icons"
                                                           style="display:block"><i
                                                                    class="fa fa-edit fa-lg"></i></a>
                                                        <a href="javascript:void(0)" id="cancel_ssn"
                                                           class="text-action icons" style="display:none">
                                                            <i class="fa fa-remove fa-lg"></i></a>
                                                    </div>
                                                </div>
                                                <p class="error" id="error_ssn"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                           <input type="text" name="dob" id="dob" value="<?=getCustomDate($agent_row['birth_date'])?>" class="form-control">
                                           <label>DOB</label>
                                           <p class="error" id="error_dob"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="agp_md_title">Login Security</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" class="form-control"
                                               maxlength="20" value="" autocomplete="new-password"
                                               onblur="check_password(this, 'password_err','error_password', event, 'input_validation');"
                                               onkeyup="check_password_Keyup(this,'password_err','error_password', event, 'input_validation');">
                                        <label>Create New Password</label>
                                        <p class="error" id="error_password"></p>
                                        <div id="password_err" class="mid"><span></span></div>
                                        <div id="pswd_info" class="pswd_popup" style="display: none">
                                            <div class="pswd_popup_inner">
                                                <h4>Password Requirements</h4>
                                                <ul>
                                                    <li id="pwdLength" class="invalid"><em></em>Minimum 8 Characters
                                                    </li>
                                                    <li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase
                                                        letter
                                                    </li>
                                                    <li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowercase
                                                        letter
                                                    </li>
                                                    <li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
                                                </ul>
                                                <div class="btarrow"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="password" id="c_password" name="c_password" class="form-control"
                                               maxlength="20">
                                        <label>Confirm Password</label>
                                        <div id="c_password_err" class="mid"><span></span></div>
                                        <p class="error" id="error_c_password"></p>
                                    </div>
                                </div>
                        <?php echo generate2FactorAuthenticationUI($agent_row,array('addOnClass'=>'phone-addon text-left w-160'));/*<div class="col-sm-6">
                           <div class="phone-control-wrap m-b-25">
                           <div class="phone-addon text-left">
                              <strong>Two-Factor Authentication (2FA):</strong><br>
                              Two-factor authentication is an extra layer of security on login designed to ensure that user is the only person who can access their account, even if someone knows their password.
                           </div>
                           <div class="phone-addon w-90">
                                 <div class="custom-switch">
                                    <label class="smart-switch">
                                       <input type="checkbox" class="js-switch" name="is_2fa" id="is_2fa" <?=$agent_row['is_2fa']=='Y' ? 'checked' : ''?> value="Y" />
                                       <div class="smart-slider round"></div>
                                    </label>
                                 </div>
                           </div>
                        </div>
                         <div class="phone-control-wrap m-b-0">
                           <div class="phone-addon text-left">
                              <strong>IP Address Restriction:</strong><br>
                              IP restrictions allow user to specify which IP addresses have access to sign in to their account. We recommend using IP restrictions if user desires to access account when they are in office, mobile, etc.
                           </div>
                           <div class="phone-addon w-90">
                                 <div class="custom-switch">
                                    <label class="smart-switch">
                                       <input type="checkbox" class="js-switch" name="is_ip_restriction" id="is_ip_restriction" <?=$agent_row['is_ip_restriction']=='Y' ? 'checked' : ''?> value="Y" />
                                       <div class="smart-slider round"></div>
                                    </label>
                                 </div>
                           </div>
                        </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php 
                           $allowed_ip_res = array();
                           if($agent_row['is_ip_restriction'] == 'Y' && !empty($agent_row['allowed_ip'])) {
                               $allowed_ip_res = explode(',',$agent_row['allowed_ip']);
                           
                           }
                           ?>
                        <div class="ip_address_div m-t-25" style="<?=$agent_row['is_ip_restriction']=='Y' ? '' : 'display: none;'?>">
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
                                 <div class="clearfix"></div>
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
                        </div>
                        */ ?>
                        <div class="col-sm-12 clearfix m-t-25 text-center">
                           <button value="account_tab" type="button" class="btn btn-action save_account">Save
                           </button>
                        </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="products_tab">
                        <?php include_once __DIR__ . '/../agent_products.php'; ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="attribute_tab">
                        <p class="agp_md_title">Attributes</p>
                        <div class="clearfix m-b-15">
                            <div class="pull-left responsive_btn">
                                <a href="agents_account_managers.php?agent_id=<?= $agent_row['id'] ?>"
                                   class="btn btn-info btn-outline agents_account_managers">Account Managers</a>
                                <?php if($agent_row["allow_download_agreement"] == "N"){ ?>
                                    <a href="javascript:void(0);" class="btn btn-action btn-outline" onclick="setNotifyError('Agreement Not Found!');">Agent Agreement</a>
                                <?php }else{ ?>
                                    <?php if(!empty($agent_row) && $agent_row['status'] == 'Active'){ ?>
                                        <a href="<?=$HOST?>/downloads3bucketfile.php?file_path=<?=urlencode($AGENT_AGREEMENT_CONTRACT_FILE_PATH)?>&file_name=<?=urlencode($agent_row['agent_contract_file'])?>&user_id=<?=$agent_row['id']?>&location=agent_profile" class="btn btn-action btn-outline" >Agent Agreement</a>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="pull-right hidden">
                                <a href="javascript:void(0);" class="text-info fs18 m-t-7 btn_edit_attributes"><i
                                            class="fa fa-edit"></i></a>
                            </div>
                        </div>
                        <div class="theme-form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="phone-control-wrap">
                                        <div class="phone-addon">
                                            <div class="form-group ">
                                                <div class="custom_drag_control">
                                                    <span class="btn btn-action">W9 Document Upload</span>
                                                    <input type="hidden" name="w9_pdf"
                                                           value="<?= checkIsset($agent_row['w9_pdf']) ?>"/>
                                                    <input type="file" class="gui-file" id="w9_form_business"
                                                           name="w9_form_business" disabled>
                                                    <input type="text" class="gui-input"
                                                           placeholder="<?php echo checkIsset($agent_row['w9_pdf']) != '' ? $agent_row['w9_pdf'] : 'Choose File' ?>" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="phone-addon w-30">
                                            <?php if (checkIsset($agent_row['w9_pdf']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($agent_row['w9_pdf']))) { ?>
                                                <a href="<?php echo $AGENT_DOC_WEB . $agent_row['w9_pdf']; ?>"
                                                   title="View Document" class="text-action w9_pdf_link" target="_blank"><i
                                                            class="fa fa-download fs20"></i></a>
                                            <?php } else { ?>
                                                <a style="display: none;" href="javascript:void(0);"
                                                   title="View Document" class="text-action w9_pdf_link" target="_blank"><i class="fa fa-download fs20"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="npn_number"
                                               value="<?= $agent_row['npn'] ?>" disabled />
                                        <label>NPN Number</label>
                                        <p class="error" id="error_npn_number"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="phone-control-wrap">
                                        <div class="phone-addon">
                                            <div class="form-group ">
                                                <div class="custom_drag_control">
                                                    <span class="btn btn-action">E&O Document Upload</span>
                                                    <input type="hidden" name="chk_e_o_document"
                                                           value="<?= checkIsset($agent_row['w9_pdf']) ?>"/>
                                                    <input type="file" class="gui-file" id="e_o_document"
                                                           name="e_o_document" disabled>
                                                    <input type="text" class="gui-input"
                                                           placeholder="<?php echo checkIsset($resDoc['e_o_document']) != '' ? $resDoc['e_o_document'] : 'Choose File' ?>" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="phone-addon w-30">
                                            <?php if (checkIsset($resDoc['e_o_document']) != "" && file_exists($AGENT_DOC_DIR . checkIsset($resDoc['e_o_document']))) { ?>
                                                <a href="<?php echo $AGENT_DOC_WEB . $resDoc['e_o_document']; ?>"
                                                   title="View Document" class="text-action e_o_document_link" target="_blank"><i
                                                            class="fa fa-download fs20"></i></a>
                                            <?php } else { ?>
                                                <a style="display: none;" href="javascript:void(0);"
                                                   title="View Document" class="text-action e_o_document_link" target="_blank"><i class="fa fa-download fs20"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group height_auto">
                                        <input type="radio" name="e_o_coverage" checked value="Y" id="e_o_yes"
                                               style="display: none">
                                        <input type="text" class="form-control" value="<?= $resDoc['e_o_amount'] ?>"
                                               name="e_o_amount" id="e_o_amount" disabled />
                                        <label>E&O Amount (Minimum of $1million)</label>
                                        <div class="text-right">
                                            <i class="fa fa-check-circle text-success" aria-hidden="true"></i> Approved
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="e_o_expiration"
                                               name="e_o_expiration"
                                               value="<?= checkIsset($resDoc['e_o_expiration']) ? getCustomDate($resDoc['e_o_expiration']) : '' ?>" />
                                        <label>E&O Expiration (MM/DD/YYYY)</label>
                                        <p class="error" id="error_e_o_expiration"></p>
                                    </div>
                                </div>
                                <div class="col-sm-12 m-b-15 text-center">
                                    <button value="attribute_tab" type="button" class="btn btn-action save_account">
                                        Save
                                    </button>
                                </div>
                            </div>
                            <p class="agp_md_title">State(s) Licensed</p>
                            <?php include_once __DIR__ . '/../agent_license.php'; ?>
                            <p class="agp_md_title">State(s) Pending Approval</p>
                            <?php include_once __DIR__ . '/../agent_pending_license.php'; ?>
                            <div class="m-t-20"></div>
                            <div class="row" id="add_license_div">
                                <div class="col-sm-12">
                                    <div class="pull-left">
                                        <a href="javascript:void(0);" class="btn btn-info add_more_license" id="add_more_license">+ License</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="personal_brand">
                        <p class="agp_md_title">
                            Branding and Vanity URL
                        </p>
                        <p class="fw500 m-b-20" >This unique url allows your members to self enroll quickly and easily without having to login to your group portal. Please provide your custom vanity url.</p>
                        <div class="row theme-form">
                            <div class="col-sm-12">
                                <div class="form-group height_auto">
                                    <label class="fs14 label-input">
                                        <input type="checkbox"
                                               name="display_in_member" <?= $agent_row['display_in_member'] == 'Y' ? 'checked' : '' ?>
                                               value="<?= $agent_row['display_in_member'] ?>">
                                        Check this box if you do not wish to display your name, email, and phone as a
                                        point
                                        of contact inside the member portal.
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" value="<?= $agent_row["public_name"] ?>"
                                           name="public_name" id="public_name"/>
                                    <label>Display Name</label>
                                    <p class="error" id="error_public_name"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" value="<?= $agent_row["public_phone"] ?>"
                                           name="public_phone" id="public_phone"/>
                                    <label>Display Phone</label>
                                    <p class="error" id="error_public_phone"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control no_space" value="<?= $agent_row["public_email"] ?>"
                                           name="public_email" id="public_email"/>
                                    <label>Display Email</label>
                                    <p class="error" id="error_public_email"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="input-group m-b-5"><span
                                                class="input-group-addon fs14"><?= get_display_url($AAE_WEBSITE_HOST) . '/'; ?></span>
                                        <input type="text" class="form-control" id="username" name="username"
                                               value="<?= $agent_row["user_name"] ?>" placeholder="" readonly="">
                                        <div id="username_info" class="pswd_popup" style="display: none">
                                            <div class="pswd_popup_inner">
                                                <h4>URL Requirements</h4>
                                                <ul style="list-style:none; padding-left:10px;">
                                                    <li id="ulength" class="invalid"><em></em>Be between 4-20 characters
                                                    </li>
                                                    <li id="alpha" class="invalid"><em></em>Contain no spaces or special
                                                        characters
                                                    </li>
                                                    <li id="unique" class="invalid"><em></em>Unique URL</li>
                                                </ul>
                                                <div class="btarrow"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="error" id="error_username"></p>
                                    <a href="<?= $AAE_WEBSITE_HOST . '/' . $_SESSION['agents']['user_name']; ?>" target="_blank" class="red-link pn" style="word-break: break-all;"><?= $AAE_WEBSITE_HOST . '/' . $_SESSION['agents']['user_name']; ?></a>
                                </div>
                            </div>
                            <div <?= $agent_row['is_branding'] == 'Y' ? '' : 'style="display: none;"';?>>
                                <p class="fs16 fw500 m-b-20 m-t-20">Brand Customization</p>
                                <div class="clearfix">
                                    <label class="fs14 label-input" style="display: none;">
                                        <input type="checkbox"
                                               name="is_branding" <?= $agent_row['is_branding'] == 'Y' ? 'checked' : '' ?>
                                               value="Y">
                                        Check this box to allow personal branding of agent portal</label>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="fw600 lato_font m-b-20">Logo</p>
                                        <p class="fw500 m-b-20">Click the box below to upload your customized branding logo.</p>
                                        <div class="agent_drop_div pro_drop_div m-b-20" id="enrollment_profile">
                                            <input type="hidden" id="contract_profile_image_size" name="profile_image[size]"
                                                   value=""/>
                                            <input type="hidden" id="contract_profile_image_name" name="profile_image[name]"
                                                   value=""/>
                                            <input type="hidden" id="contract_profile_image_type" name="profile_image[type]"
                                                   value=""/>
                                            <input type="hidden" id="contract_profile_image_tmp_name"
                                                   name="profile_image[tmp_name]" value=""/>
                                            <div class="dropzone profile-dropzone">
                                                <div class="dropzone-previews">
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $tmp_style = 'display: none;';
                                        if (!empty($agent_row["brand_icon"]) && file_exists($AGENTS_BRAND_ICON . $agent_row["brand_icon"])) {
                                            $tmp_style = 'display: block;';
                                        } ?>
                                        <div class="text-right pro_link_div m-t-15" style="<?= $tmp_style; ?>">
                                            <a href="javascript:void(0);" onclick="return delete_business_image();"
                                               class="btn red-link">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display: none;">
                            <p class="agp_md_title">Connected Social Accounts</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-facebook"></i></span>
                                            <input type="text" class="form-control" placeholder="Link Account">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix m-t-15 text-center">
                            <button value="personal_brand_tab" type="button" class="btn btn-action save_account">Save
                            </button>
                        </div>
                    </div>
                    <?php if($IS_NOT_LOA_AGENT == true ) { ?>
                    <hr>
                    <div role="tabpanel" class="tab-pane" id="commission_tab">
                        <p class="agp_md_title">Commissions</p>
                        <ul class="nav nav-tabs tabs  customtab nav-noscroll " role="tablist">
                            <li class="active" role="presentation">
                                <a href="#direct_deposit_tab" data-toggle="tab" aria-expanded="true">Direct Deposit</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active direct_deposit_tab" id="direct_deposit_tab">
                                <?php if (!empty($dd_row))  { ?>
                                    <ul class="drict_deposit_li" id="direct_deposit_detail">
                                       <?=$direct_deposit_detail;?>
                                    </ul>
                                <?php } else { ?>                                
                                    <ul class="drict_deposit_li" id="direct_deposit_detail"></ul>
                                <?php } ?>                                
                                <div class="row theme-form">
                                    <div class="col-md-6">
                                        <div class="m-b-20">
                                            <div class="input-question">
                                                <label class="mn">Account Type*&nbsp;&nbsp;</label>
                                                <label class="mn"><input type="radio" name="d_account_type" value="checking">Checking Account&nbsp;</label>
                                                <label class="mn"><input type="radio" name="d_account_type" value="saving">Savings Account&nbsp;</label>
                                            </div>    
                                            <p class="error" id="error_d_account_type"></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" id="d_bank_name" name="d_bank_name" class="form-control">
                                            <label>Bank Name</label>
                                            <p class="error" id="error_d_bank_name"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" id="d_routing_number" name="d_routing_number" class="form-control" maxlength="9" oninput="isValidNumber(this)">
                                            <label>Bank Routing Number</label>
                                            <p class="error" id="error_d_routing_number"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" id="d_account_number" name="d_account_number" class="form-control" maxlength="17" oninput="isValidNumber(this)">
                                            <label>Bank Account Number</label>
                                            <p class="error" id="error_d_account_number"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" id="d_c_account_number" name="d_c_account_number" class="form-control" maxlength="17" oninput="isValidNumber(this)">
                                            <label>Confirm Bank Account Number</label>
                                            <p class="error" id="error_d_c_account_number"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix m-t-15 text-center">
                                    <button value="direct_deposit_tab" type="button" class="btn btn-action save_account">Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }  ?>
                </div>
            </div>
        </div>
    </div>
</form>
<?=generateIPAddressUI();?>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script type="text/javascript">
    function refreshCurrencyFormatter() {
        $("#e_o_amount").formatCurrency({
            colorize: true,
            negativeFormat: '-%s%n',
            roundToDecimalPlace: 0
        });
    }

    $(document).ready(function () {
        checkEmail();
        $("#e_o_expiration").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            startDate: new Date()
        });

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
        

        $('#e_o_amount').blur(function () {
            refreshCurrencyFormatter();
        }).keyup(function (e) {
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
        });

        $(".writing_state").multipleSelect({});

        $('#edit_ssn').click(function () {
            $(this).hide();
            $('#display_ssn').hide();
            $('#ssn').show();
            $('#is_ssn_edit').val('Y');
            $('#cancel_ssn').show();

        });

        $('#cancel_ssn').click(function () {
            $(this).hide();
            $('#display_ssn').show();
            $('#ssn').hide();
            $('#is_ssn_edit').val('N');
            $('#edit_ssn').show();
            $('#error_ssn').html('');
        });

        $('.agents_access_edit').colorbox({iframe: true, width: '450px', height: '630px'});
        $('.agents_account_managers').colorbox({iframe: true, width: '900px', height: '570px'});
        $('.personal_production_report').colorbox({iframe: true, width: '900px', height: '800px'});
        $('.agent_tree_popup').colorbox({iframe: true, width: '900', height: '650'});

        trigger(".add_more_license", function ($this, e) {
            index = parseInt($(".license_portion").length);
            $display_counter = parseInt($('#license_display_counter').val());
            $number=index+1;
            if($display_counter > index){
            $number = $display_counter + 1;
            }
            pos_number = $number;
            $('#add_license_div').before($(".license_template").html().replace(/~i~/g, pos_number));
            $("#license_display_counter").val($number);
            $('.select_class_' + pos_number).addClass('form-control');
            $('.select_class_' + pos_number).selectpicker({
                container: 'body',
                style: 'btn-select',
                noneSelectedText: '',
                dropupAuto: false,
            });
            $(".div_license_" + pos_number + " :input").selectpicker('refresh');
            $(".license_expiry").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
                startDate: new Date()
            });

            $(".license_active").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
            });
            $(".license_not_expire").uniform();
            // if (index >= 52)
            //     $(".add_more_license").hide();

        });

        trigger(".remove_license", function ($this, e) {
            var id = $this.attr('data-id');
            var lid = $("#hdn_license_" + id).val();
            // index = parseInt($(".license_portion").length);
            // if (index < 54)
            //     $(".add_more_license").show();
            $this.parents(".license_portion").fadeOut("slow", function () {
                $(this).remove();
            });
        });

        trigger(".license_not_expire", function ($this, e) {
            var id = $this.attr('data-id');
            if ($('#license_not_expire_' + id).is(":checked")) {
                $("#license_expiry_" + id).attr('readonly', 'readonly');
                $("#license_expiry_" + id).val("12/31/2099");
            } else {
                $("#license_expiry_" + id).removeAttr('readonly');
                $("#license_expiry_" + id).val("");
            }
        });

        trigger(".pending_license_not_expire", function ($this, e) {
            var id = $this.attr('data-id');
            if ($('#pending_license_not_expire_' + id).is(":checked")) {
                $("#pending_license_expiry_" + id).attr('readonly', 'readonly');
                $("#pending_license_expiry_" + id).val("12/31/2099");
            } else {
                $("#pending_license_expiry_" + id).removeAttr('readonly');
                $("#pending_license_expiry_" + id).val("");
            }
        });

        $(document).off('click', ".edit_license");
        $(document).on('click', ".edit_license", function (e) {
            var $id = $(this).attr('data-id');
            $(".ed_license__"+$id).val($id);
            $(".primary_" + $id).hide();
            $(".secondary_" + $id).show();
            $('.edit_license').hide()
        });

        $(document).off('click', ".edit_pending_license");
        $(document).on('click', ".edit_pending_license", function (e) {
            var $id = $(this).attr('data-id');
            $(".pending_primary_" + $id).hide();
            $(".ed_license__"+$id).val($id);
            $(".pending_secondary_" + $id).show();
            $('.edit_pending_license').hide()
        });

        $(document).on('focus','#address,#zipcode',function(){
           $("#is_address_ajaxed").val(1);
        });

        $(document).on('focus','#business_address,#business_zipcode',function(){
           $("#is_agency_address_ajaxed").val(1);
        });

        $(document).off('click', '.save_account');
        $(document).on('click', '.save_account', function (e) {
            $is_address_ajaxed = $("#is_address_ajaxed").val();
            $is_agency_address_ajaxed = $("#is_agency_address_ajaxed").val();
            if($is_address_ajaxed == 1){
                updateAddress();
            }else if($is_agency_address_ajaxed == 1){
                updateAddress();
            }else{
                ajaxSaveAccountDetails(this.value);
            }
        });

        function ajaxSaveAccountDetails(value){
            var this_obj = $('.save_account');
            var section = this_obj.val();
            if(value != ''){
                section = value;
            }
            $("#section").val(value);

            formHandler($("#account_detail"),
                function () {
                    $("#ajax_loader").show();
                    this_obj.prop('disabled',true);
                },
                function (data) {
                    $("#ajax_loader").hide();
                    this_obj.prop('disabled', false);
                    $(".error").hide();
                    console.log(data);

                    if(typeof(data.w9_pdf_link) !== "undefined") {
                        $(".w9_pdf_link").attr('href',data.w9_pdf_link);
                        $(".w9_pdf_link").show();
                    }

                    if(typeof(data.e_o_document_link) !== "undefined") {
                        $(".e_o_document_link").attr('href',data.e_o_document_link);
                        $(".e_o_document_link").show();
                    }

                    if (data.status == 'success') {
                        if(section == "account_tab") {
                            $("#password").val('').trigger('change');
                            $("#c_password").val('').trigger('change');
                        }

                        if(section == "direct_deposit_tab") {
                            $('input[name="d_account_type"]').prop('checked',false);
                            $('input[name="d_bank_name"]').val('').trigger('change');
                            $('input[name="d_routing_number"]').val('').trigger('change');
                            $('input[name="d_account_number"]').val('').trigger('change');
                            $('input[name="d_c_account_number"]').val('').trigger('change');
                            $('input[name="d_account_type"]').uniform();

                            if(typeof(data.direct_deposit_detail) !== "undefined") {
                                $("#direct_deposit_detail").html(data.direct_deposit_detail);
                            }
                        }
                        setNotifySuccess("Profile updated successfully!");

                    } else if (data.status == "fail") {
                        setNotifyError("Oops... Something went wrong please try again later");
                    } else {
                        var tmp_flag = true;
                        $.each(data.errors, function (key, value) {
                            $('#error_' + key).parent("p.error").show();
                            $('#error_' + key).html(value).show();
                            
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
                        ajaxSaveAccountDetails('');
                    }else{
                        $("#business_state").val(res.state).addClass('has-value');
                        $("#business_city").val(res.city).addClass('has-value');
                        ajaxSaveAccountDetails('');
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
                                ajaxSaveAccountDetails('');
                             }
                          },
                    });
                 }else if(res.status == 'success'){
                    $("#is_address_verified").val('N');
                    if(res.agencyApi=="success"){
                        updateAddress();
                    }else{
                        ajaxSaveAccountDetails('');
                    }
                 }else{
                    $.each(res.errors,function(index,error){
                       $("#error_"+index).html(error).show();
                   });
                 }
                 $("#state").selectpicker('refresh');
                 $("#business_state").selectpicker('refresh');
              }
           });
        }
        $(document).on("change", ".account_type", function () {
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

        $(document).on('focusin click keyup', '#username', function () {
            $('#username_info').show();
            var user_name = $(this).val();
            var user_email = $('#emails').val();
            var agent_id = '<?=$agent_id_org?>';
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
                    success: function (res) {
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
        }).on('blur', '#username', function () {
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
        $currentImage = '';
        if (file_exists($AGENTS_BRAND_ICON . $contract_business_image) && $contract_business_image != "") {
            $currentImage = $HOST . '/uploads/agents/brand_icon/' . $contract_business_image;
        }
        ?>

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
                    var $agent_id = '<?=$agent_id_org?>';
                    $.ajax({
                        url: '<?=$AGENT_HOST?>/ajax_update_rep_business_picture.php?id=' + $agent_id + '&is_my_profile=Y',
                        data: 'profile_picture=' + dataUrl,
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {
                            $('#ajax_loader').show();
                        },
                        success: function (res) {
                            $('#ajax_loader').hide();
                            if (res.status == 'fail') {
                                if (res.error != ""){
                                    //alert(res.error);
                                }
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
                if (error_msg.search('The maximum image size to upload is') != -1) {
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

    });

    getDocumentLink = function ($link) {
        return '<a href="' + $link + '" title="View Document" target="_blank"><span class="fa fa-paperclip"></a>';
    }

    function delete_business_image() {
        if (confirm('Are you sure you want to delete business logo?')) {
            var $agent_id = '<?=$agent_id_org?>';
            $.ajax({
                url: '<?=$AGENT_HOST?>/delete_business_image.php?id=' + $agent_id,
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
                        $("#enrollment_profile .profile-dropzone").attr('style', 'background:url(' + default_business_image + ') no-repeat scroll center center /100% 100%;height:100px;');
                        $(".pro_link_div").hide();
                        $('#enrollment_profile .dropzone-previews').empty();
                        setNotifySuccess(res.message);
                    }
                }
            });
        }
    }

    /*scroll div function start */
    function scrollToDiv(element, navheight) {
        if ($(element).length) {
            var offset = element.offset();
            var offsetTop = offset.top;
            var totalScroll = offsetTop - navheight;
            if ($(window).width() >= 1099) {
                var totalScroll = offsetTop - $("nav.navbar-default").outerHeight() - 57
            } else {
                var totalScroll = offsetTop - $("nav.navbar-default ").outerHeight() - 57
            }
            $('body,html').animate({
                scrollTop: totalScroll
            }, 1200);
        }
    }

    function refreshControl(id_class) {
        $(id_class).addClass('form-control');
        $(id_class).selectpicker({
            container: 'body',
            style: 'btn-select',
            noneSelectedText: '',
            dropupAuto: false,
        });
        //$("input[type='checkbox']").uniform();
    }

    if ($(window).width() >= 1199) {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 431) {
                $('.fixed_tab_top').addClass('fixed');
            } else {
                $('.fixed_tab_top').removeClass('fixed');
            }
        });
    }

    if ($(window).width() <= 1170) {
        if ($('.nav-tabs:not(.nav-noscroll)').length) {
            ;(function () {
                'use strict';
                $(activate);

                function activate() {
                    $('.nav-tabs:not(.nav-noscroll)').scrollingTabs({
                        scrollToTabEdge: true,
                        enableSwiping: true
                    });
                }
            }());
        }
    }
    <?=generate2FactorAuthenticationJS()?>
</script>