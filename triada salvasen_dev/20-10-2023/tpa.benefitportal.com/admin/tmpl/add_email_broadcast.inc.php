<form action="" role="form" method="post" class="" name="broadcast_form" id="broadcast_form" enctype="multipart/form-data">
  <input type="hidden" name="upload_type" id="upload_type" value="">
  <input type="hidden" name="email_attachment_id[]" id="email_attachment_id" value="<?= $email_attachment_id ?>">
  <div class="panel panel-default panel-block communication_panel">
    <div class="panel-body">
      <div class="phone-control-wrap">
        <div class="phone-addon w-90 v-align-top">
          <img class="media-object" src="<?=$HOST?>/images/icons/circle_envelope.svg" alt="...">
        </div>
        <div class="phone-addon theme-form text-left">
          <p class="fs20 m-b-20">+ Email Broadcast</p>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <select class="form-control" name="user_group" id="user_group">
                  <option value="" ></option>
                  <option value="Admins" <?= (!empty($broadcaster_res) && $user_type == 'Admins') ? "selected='selected'" : '' ?>>Admins</option>
                  <option value="Agents" <?= (!empty($broadcaster_res) && $user_type == 'Agents') ? "selected='selected'" : '' ?>>Agents</option>
                  <option value="Employer Groups" <?= (!empty($broadcaster_res) && $user_type == 'Employer Groups') ? "selected='selected'" : '' ?>>Groups</option>
                  <option value="Members" <?= (!empty($broadcaster_res) && $user_type == 'Members') ? "selected='selected'" : '' ?>>Members</option>
                  <option value="Leads" <?= (!empty($broadcaster_res) && $user_type == 'Leads') ? "selected='selected'" : '' ?>>Leads</option>
                </select>
                <label>Select User Group</label>
                <span class="error error_preview" id="error_user_group"></span>
              </div>
            </div>
            <div class="filter_div" id="filter_div_expect_admin" style="<?=(!empty($broadcaster_res) && in_array($user_type, array('Agents','Employer Groups', 'Members')) && $specific_user_group == 'N') ? '' : 'display: none;'?>">
              <div class="col-md-1">
                <div class="form-group  text-center">
                  <p class="mn text-light-gray fs12 p-t-7">— Filter —</p>
                </div>
              </div>
              <div class="col-md-3">
                  <div class="form-group ">
                     <div class="group_select">
                        <select name="product_value[]" class="se_multiple_select" multiple="multiple">
                          <?php if(!empty($company_arr) && count($company_arr) > 0) {
                            foreach ($company_arr as $key => $company) { ?>
                              <optgroup label="<?= $key ?>">
                                <?php foreach ($company as $pkey => $row) { ?>
                                  <option value="<?= $row['id'] ?>" <?= (!empty($broadcaster_res) && !empty($product_ids_array) && in_array($row['id'], $product_ids_array)) ? "selected='selected'" : '' ?>><?= $row['name'] . ' (' . $row['product_code'] . ') ' ?></option>
                                <?php } ?>
                              </optgroup>
                            <?php } 
                          } ?>
                        </select>
                        <label>Select Product(s)</label>
                    <span class="error error_preview" id="error_product_value"></span>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="form-group  text-center">
                  <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group ">
                  <select name="product_status" class="form-control">
                    <option value=""></option>
                    <option value="Contracted" <?= (!empty($broadcaster_res) && !empty($product_status) && $product_status == "Contracted") ? "selected='selected'" : '' ?>>Active</option>
                    <option value="Pending Approval" <?= (!empty($broadcaster_res) && !empty($product_status) && $product_status == "Pending Approval") ? "selected='selected'" : '' ?>>Pending</option>
                    <option value="Suspended" <?= (!empty($broadcaster_res) && !empty($product_status) && $product_status == "Suspended") ? "selected='selected'" : '' ?>>Suspended</option>
                    <option value="Extinct" <?= (!empty($broadcaster_res) && !empty($product_status) && $product_status == "Extinct") ? "selected='selected'" : '' ?>>Extinct</option>
                  </select>
                  <label>Product Status</label>
                  <span class="error error_preview" id="error_product_status"></span>
                </div>
              </div>
              <div class="filter_div_agents" style="display:  <?=$user_type == 'Agents' ? 'block' : 'none'; ?>">
                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3">
                  <div class="form-group ">
                    <select name="agent_level[]" class="se_multiple_select" multiple="multiple">
                      <!-- <option value=""></option> -->
                      <?php foreach ($agentCodedRes as $key => $value) { ?>
                        <option value="<?=$value['id']?>" <?=in_array($value['id'],$specific_agent_level) ? "selected = 'selected'" : ""?>><?=$value['level_heading']?></option>
                      <?php } ?>  
                    </select>
                    <label>Agent Level</label>
                    <span class="error error_preview" id="error_agent_level"></span>
                  </div>
                </div>
                 <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group ">
                    <select name="agent_status[]" class="se_multiple_select" multiple="multiple" id="agent_status">
                        <option value="Invited" <?=in_array("Invited",$agent_status) ? "selected = 'selected'" : ""?>>Invited</option>
                        <option value="Pending Documentation" <?=in_array("Pending Documentation",$agent_status) ? "selected = 'selected'" : ""?>>Pending Documentation</option>
                        <option value="Pending Approval" <?=in_array("Pending Approval",$agent_status) ? "selected = 'selected'" : ""?>>Pending Approval</option>
                        <option value="Pending Contract" <?=in_array("Pending Contract",$agent_status) ? "selected = 'selected'" : ""?>>Pending Contract</option>
                        <option value="Active" <?=in_array("Active",$agent_status) ? "selected = 'selected'" : ""?>>Contracted</option>
                        <option value="Suspended" <?=in_array("Suspended",$agent_status) ? "selected = 'selected'" : ""?>>Suspended</option>
                        <option value="Terminated" <?=in_array("Invited",$agent_status) ? "selected = 'selected'" : ""?>>Terminated</option>
                    </select>
                    <label>Agent Status</label>
                  </div>
                </div>

              </div>
              <div class="filter_div_groups" style="display: <?=in_array($user_type, array('Employer Groups','Members','Leads')) ? 'block' : 'none'?>">
                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3">
                  <div class="form-group ">
                    <select name="group_agent_ids[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($agent_res)) { ?>
                        <?php foreach ($agent_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && in_array($value['id'], $enrolling_agent)) ? "selected='selected'" : '' ?>><?= $value['rep_id'] . ' - ' . $value['name'] ?></option>
                        <?php } ?>
                      <?php } ?> 
                    </select>
                    <label>Agent</label>
                    <span class="error error_preview" id="error_group_agent_ids"></span>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group ">
                    <select name="group_agent_tree_ids[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($agent_res)) { ?>
                        <?php foreach ($agent_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && in_array($value['id'], $tree_agent_ids)) ? "selected='selected'" : '' ?>><?= $value['rep_id'] . ' - ' . $value['name']?></option>
                        <?php } ?>
                      <?php } ?>  
                    </select>
                    <label>Agent Tree</label>
                    <span class="error error_preview" id="error_group_agent_tree_ids"></span>
                  </div>
                </div>
              </div>
              <div class="member_state" style="display: <?=in_array($user_type, array('Members','Leads')) ? 'block' : 'none'?>">
                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group ">
                    <select name="member_state[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($states)) { ?>
                        <?php foreach ($states as $key => $value) { ?>
                          <option value="<?= $value['name'] ?>" <?= (!empty($broadcaster_res) && in_array($value['name'], $member_state)) ? "selected='selected'" : '' ?>><?= $value['name']?></option>
                        <?php } ?>
                      <?php } ?> 
                    </select>
                    <label>State</label>
                    <span class="error error_preview" id="error_member_state"></span>
                  </div>
                </div>
              </div>

              </div>
            <div class="filter_div admins_filter_div" id="filter_div_admins" style="<?=(!empty($broadcaster_res) && $user_type == 'Admins' && $specific_user_group == 'N') ? '' : 'display: none;'?>">
              <div class="col-md-1">
                <div class="form-group  text-center">
                  <p class="mn text-light-gray fs12 p-t-7">— Filter —</p>
                </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group ">
                    <select name="admin_level[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($acl_features)) { ?>
                        <?php foreach ($acl_features as $key => $value) { ?>
                          <option value="<?= $key ?>" <?= (!empty($broadcaster_res) && !empty($admin_level_array) && in_array($key, $admin_level_array)) ? "selected='selected'" : '' ?>><?= $key ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <label>Admin Level</label>
                    <span class="error error_preview" id="error_admin_level"></span>
                </div>
              </div>
            </div>
            <div class="filter_div admins_filter_div specific_value_div" id="filter_div_specific_admins" style="<?=(!empty($broadcaster_res) && $user_type == 'Admins' && $specific_user_group == 'Y') ? '' : 'display: none;'?>">
              <div class="col-md-7">
                  <div class="form-group ">
                    <!-- <input type="text" class="form-control" name="specific_admin" id="specific_admin" value="<?=$specific_user_ids_array?>"> -->
                    <select name="specific_admin[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($res_admins)) { ?>
                        <?php foreach ($res_admins as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && !empty($specific_user_ids_array) && in_array($value['id'], $specific_user_ids_array)) ? "selected='selected'" : '' ?>><?= $value['display_id'] . ' - ' . $value['name']?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <label>Specific Admin(s)</label>
                    <span class="error error_preview" id="error_specific_admin"></span>
                </div>
              </div>
            </div>
            <div class="filter_div leads_filter_div" id="filter_div_leads" style="<?=(!empty($broadcaster_res) && $user_type == 'Leads' && $specific_user_group == 'N') ? '' : 'display: none;'?>">
              <div class="col-md-1">
                <div class="form-group  text-center">
                  <p class="mn text-light-gray fs12 p-t-7">— Filter —</p>
                </div>
              </div>
              <div class="col-md-3">
                  <div class="form-group ">
                    <select name="lead_tags[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($lead_tag_res)) { ?>
                        <?php foreach ($lead_tag_res as $key => $value) { ?>
                          <option value="<?= $value ?>" <?= (!empty($broadcaster_res) && !empty($lead_tags_array) && in_array($value, $lead_tags_array)) ? "selected='selected'" : '' ?>><?= $value ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <label>Lead Tag</label>
                    <span class="error error_preview" id="error_lead_tags"></span>
                </div>
              </div>

                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group ">
                    <select name="lead_agent_ids[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($agent_res)) { ?>
                        <?php foreach ($agent_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && in_array($value['id'], $enrolling_agent)) ? "selected='selected'" : '' ?>><?= $value['rep_id'] . ' - ' . $value['name']?></option>
                        <?php } ?>
                      <?php } ?> 
                    </select>
                    <label>Agent</label>
                    <span class="error error_preview" id="error_lead_agent_ids"></span>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3">
                  <div class="form-group ">
                    <select name="lead_agent_tree_ids[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($agent_res)) { ?>
                        <?php foreach ($agent_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res)  && in_array($value['id'], $tree_agent_ids)) ? "selected='selected'" : '' ?>><?= $value['rep_id'] . ' - ' . $value['name']?></option>
                        <?php } ?>
                      <?php } ?>  
                    </select>
                    <label>Agent Tree</label>
                    <span class="error error_preview" id="error_lead_agent_tree_ids"></span>
                  </div>
                </div>
                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group ">
                    <select name="lead_state[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($states)) { ?>
                        <?php foreach ($states as $key => $value) { ?>
                          <option value="<?= $value['name'] ?>" <?= (!empty($broadcaster_res) && in_array($value['name'], $member_state)) ? "selected='selected'" : '' ?>><?= $value['name']?></option>
                        <?php } ?>
                      <?php } ?> 
                    </select>
                    <label>State</label>
                    <span class="error error_preview" id="error_lead_state"></span>
                  </div>
                </div>
            </div>
            <div class="filter_div leads_filter_div specific_value_div" id="filter_div_specific_leads" style="<?=(!empty($broadcaster_res) && $user_type == 'Leads' && $specific_user_group == 'Y') ? '' : 'display: none;'?>">
              <div class="col-md-7">
                  <div class="form-group ">
                    <!-- <input type="text" name="specific_leads" class="form-control" value="<?=$specific_user_ids_array?>"> -->
                    <select name="specific_leads[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($lead_res)) { ?>
                        <?php foreach ($lead_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && !empty($specific_user_ids_array) && in_array($value['id'], $specific_user_ids_array)) ? "selected='selected'" : '' ?>><?= $value['lead_id'] . ' - ' . $value['lead_name']?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <label>Specific Lead(s)</label>
                    <span class="error error_preview" id="error_specific_leads"></span>
                </div>
              </div>
            </div>
            <div class="filter_div agents_filter_div specific_value_div" id="filter_div_specific_agents" style="<?=(!empty($broadcaster_res) && $user_type == 'Agents' && $specific_user_group == 'Y') ? '' : 'display: none;'?>">
              <div class="col-md-7">
                  <div class="form-group ">
                    <!-- <input type="text" name="specific_agents" class="form-control" value="<?=$specific_user_ids_array?>"> -->
                    <select name="specific_agents[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($agent_res)) { ?>
                        <?php foreach ($agent_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && !empty($specific_user_ids_array) && in_array($value['id'], $specific_user_ids_array)) ? "selected='selected'" : '' ?>><?= $value['rep_id'] . ' - ' . $value['name']?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <label>Specific Agent(s)</label>
                    <span class="error error_preview" id="error_specific_agents"></span>
                </div>
              </div>
            </div>
            <div class="filter_div group_filter_div specific_value_div" id="filter_div_specific_group" style="<?=(!empty($broadcaster_res) && $user_type == 'Employer Groups' && $specific_user_group == 'Y') ? '' : 'display: none;'?>">
              <div class="col-md-7">
                  <div class="form-group ">
                    <!-- <input type="text" name="specific_group" class="form-control" value="<?=$specific_user_ids_array?>"> -->
                    <select name="specific_group[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($group_res)) { ?>
                        <?php foreach ($group_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && !empty($specific_user_ids_array) && in_array($value['id'], $specific_user_ids_array)) ? "selected='selected'" : '' ?>><?= $value['rep_id'] . ' - ' . $value['name']?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <label>Specific Employer Group(s)</label>
                    <span class="error error_preview" id="error_specific_group"></span>
                </div>
              </div>
            </div>
            <div class="filter_div member_filter_div specific_value_div" id="filter_div_specific_member" style="<?=(!empty($broadcaster_res) && $user_type == 'Members' && $specific_user_group == 'Y') ? '' : 'display: none;'?>">
              <div class="col-md-7">
                  <div class="form-group ">
                    <!-- <input type="text" name="specific_member" class="form-control" value="<?=$specific_user_ids_array?>"> -->
                    <select name="specific_member[]" class="se_multiple_select" multiple="multiple">
                      <?php  if(!empty($member_res)) { ?>
                        <?php foreach ($member_res as $key => $value) { ?>
                          <option value="<?= $value['id'] ?>" <?= (!empty($broadcaster_res) && !empty($specific_user_ids_array) && in_array($value['id'], $specific_user_ids_array)) ? "selected='selected'" : '' ?>><?= $value['rep_id'] . ' - ' . $value['name']?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <label>Specific Member(s)</label>
                    <span class="error error_preview" id="error_specific_member"></span>
                </div>
              </div>
            </div>
            <div class="filter_div" id="filter_specific_selection_div" style="<?=!empty($broadcaster_res) ? '' : 'display: none;'?>">
              <div class="col-md-2 text-left">
                <div class="form-group ">
                  <label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" <?=(!empty($broadcaster_res) && !empty($specific_user_group) && $specific_user_group == 'Y') ? "checked" : "" ?>><span id="specific_user_group_label">Specific <?=!empty($broadcaster_res) && !empty($user_type) ? (($user_type != 'Employer Groups') ? $user_type : 'Groups') : 'Agents' ?> </span></label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-body ">
      <div class="theme-form">
        <h4 class="m-t-n">New Email Broadcast</h4>
        <p class="m-b-20">Follow the steps below to build your email. Set from address, subject, and email template before adding content.</p>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <input type="text" class="form-control" name="broadcast_name" id="broadcast_name" value="<?=!empty($broadcaster_res['brodcast_name']) ? $broadcaster_res['brodcast_name'] : '' ?>">
              <label>Name Email Broadcast</label>
              <span class="error error_preview" id="error_broadcast_name"></span>
            </div>
          </div>
        </div>
        <h5 class="m-t-n m-b-20">Create Email</h5>
        <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="form-group ">
              <input type="text" class="form-control no_space" name="broadcast_from_address" value="<?=$app_setting_res['default_email_from']; ?>">
              <label>From Address</label>
              <span class="error error_preview" id="error_broadcast_from_address"></span>
            </div>
            <div class="form-group ">
              <input type="text" class="form-control" name="broadcast_from_name" value="<?=isset($app_setting_res['default_from_name']) ? $app_setting_res['default_from_name'] : "" ?>">
              <label>From Name</label>
              <span class="error error_preview" id="error_broadcast_from_name"></span>
            </div>
            <div class="form-group ">
              <input type="text" class="form-control" name="broadcast_subject" id="broadcast_subject" value="<?=!empty($broadcaster_res['subject']) ? $broadcaster_res['subject'] : '' ?>">
              <label>Subject</label>
              <span class="error error_preview" id="error_broadcast_subject"></span>
            </div>
            <div class="form-group ">
              <select name="email_template" class="form-control" id="email_template">
                <option value=""></option>
                <?php  if(!empty($templatedata)) { ?>
                  <?php foreach ($templatedata as $key => $value) { ?>
                    <option value="<?=$value['id']?>" <?= (!empty($broadcaster_res['email_template_id']) && $broadcaster_res['email_template_id'] == $value['id']) ? "selected='selected'" : '' ?>><?=$value['title']?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <label>Email Template</label>
              <span class="error error_preview" id="error_email_template"></span>
            </div>
            <div class="panel panel-default panel-block create_mail_panel">
              <div class="panel-heading">
                Actions
              </div>
              <div class="panel-body">
                <div class="email_action">
                  <a href="javascript:void(0);" class="send_brodcast">
                    <img src="images/icons/send_email.svg" width="20px"> Send Test
                  </a>
                  <a href="javascript:void(0);" class="broadcast_preview">
                    <img src="images/icons/email_eye.svg" width="20px"> Preview
                  </a>
                  <div id="file_div_inner" class="m-t-10">
                                        <a href="javascript:void(0);" class="" id="add_attachment">
                                          <img src="images/icons/paperclip.svg" width="20px">&nbsp; Add attachment
                                        </a>
                                        <input id="attachment" name="attachment[]" type="file" style="display: none;" />
                        <?php if(!empty($attachmentRow)){ ?>
                            <?php foreach ($attachmentRow as $key => $row) { ?>
                                <?php
                                $imageExt=array_reverse(explode(".", $row['file_name']));
                                $is_image=false;
                                if(strtolower($imageExt[0])=="jpg" || strtolower($imageExt[0])=="jpeg" || strtolower($imageExt[0])=="png" || strtolower($imageExt[0])=="gif" || strtolower($imageExt[0])=="tif"){ $is_image=true; }
                                ?>
                                <div class="m-t-15" id="attachment_file_div_<?= $row['id'] ?>">
                                    <div class="phone-control-wrap">
                                        <div class="phone-addon">
                                            <input type="text" name="" placeholder="<?= $row['file_name'] ?>" class="form-control" readonly="readonly">
                                        </div>
                                        <div class="phone-addon w-30">
                                            <a href="<?= $ATTACHMENS_WEB.$row['file_name'] ?>" download class="text-info fa-lg"> <i class="fa fa-download"></i></a>
                                          </div>
                                          <div class="phone-addon w-30">
                                            <a href="javascript:void(0)" data-id="<?= $row['id'] ?>" class="text-action fa-lg delete_attachment"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                  </div>
                  
                </div>
                <div class="row" id="attachements_inner_div"></div>
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-md-8 col-sm-8">
            <textarea class="summernote" name="broadcast_content" id="broadcast_content"><?= !empty($broadcaster_res['mail_content']) ? htmlspecialchars_decode($broadcaster_res['mail_content']) : '';?></textarea>
            <span class="error error_preview" id="error_broadcast_content"></span>
            <div class="clearfix m-t-25 text-right">
               <a data-href="smart_tag_popup.php" class="btn btn-action-o smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
            </div>
          </div>
          <!-- <div class="col-lg-2 col-md-3 col-sm-3">
            <div class="editor_tag_wrap">
              <div class="tag_head"><h4>AVAILABLE TAGS&nbsp;</h4></div>
              <div class="editor_tag_wrap_inner" style="max-height:335px;">
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[fname]]</label>
                  </div>
                </div>
              </div>
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[lname]]</label>
                  </div>
                </div>
              </div>
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[email]]</label>
                  </div>
                </div>
              </div>
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[phone]]</label>
                  </div>
                </div>
              </div>
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[ParentAgent]]</label>
                  </div>
                </div>
              </div>
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[MemberID]]</label>
                  </div>
                </div>
              </div>
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[ActiveProducts]]</label>
                  </div>
                </div>
              </div>
              <div>
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left" style="width: 30px;">
                    <span class="fa fa-info-circle text-blue fs18"></span>
                  </div>
                  <div class="phone-addon">
                    <label>[[AgentID]]</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div> -->
        </div>
        <hr>
        <div class="clearfix">
          <h4 class="m-t-n">Delivery <i class="fs14">(all communications sent on the hour)</i></h4>
          <label><input type="checkbox" name="future_check_box" id="future_check_box" <?=(!empty($future_check_box) && $future_check_box == 'Y') ? "checked" : "" ?>> Set this broadcast at a future date?</label>
          <h4 class="m-t-20 display_schedule" style="<?=(!empty($future_check_box) && $future_check_box == 'Y') ? "" : "display: none;" ?>">Schedule</h4>
        </div>
        <div id="main_schedule_div">
          <div class="clearfix"></div>
          <?php $foreach_counter = 0;
          if($future_check_box == 'Y' && !empty($broadcaster_schedule_settings_res) && count($broadcaster_schedule_settings_res) > 0) {
            foreach ($broadcaster_schedule_settings_res as $br_key => $br_value) { 
              $foreach_counter++;?>
              <div class="inner_schedule_div" id="inner_schedule_div_<?=$br_value['id']?>">
                <input type="hidden" name="dynamic_fields[<?=$br_value['id']?>]">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group ">
                      <div class="input-group">
                        <a href="javascript:void(0);" class="input-group-addon schedule_date_icon" id="schedule_date_icon_<?=$br_value['id']?>"><i class="fa fa-calendar "></i></a>
                        <div class="pr">
                          <input type="text" id="schedule_date_<?=$br_value['id']?>" name="schedule_date[<?=$br_value['id']?>]" class="form-control schedule_date_input_class" data-id="<?=$br_value['id']?>" placeholder="MM / DD / YYYY" value="<?= (!empty($br_value['schedule_date']) && $br_value['schedule_date'] != '0000-00-00' ) ? date('m/d/Y', strtotime($br_value['schedule_date'])) : '' ?>">
                        </div>
                      </div>
                      <span class="error error_preview" id="error_schedule_date_<?=$br_value['id']?>"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group ">
                      <select class="add_control_<?=$br_value['id']?> form-control" name="schedule_hour[<?=$br_value['id']?>]">
                        <option value=""></option>
                        <?php for ($i=1; $i < 25; $i++) {
                          $time_span = 'AM';
                          $j = $i;
                          if($i > 12) {
                            $j = $i - 12;
                            $time_span = 'PM';
                          } ?>
                          <option value="<?=$i?>" <?= (!empty($br_value['schedule_hour']) && $br_value['schedule_hour'] == $i) ? "selected='selected'" : '' ?>><?=$j . ' ' . $time_span?></option>
                        <?php } ?>
                      </select>
                      <label>Hour</label>
                      <span class="error error_preview" id="error_schedule_hour_<?=$br_value['id']?>"></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group ">
                      <select class="add_control_<?=$br_value['id']?> form-control" name="schedule_time_zone[<?=$br_value['id']?>]">
                        <option value=""></option>
                        <option value="CST" <?= (!empty($br_value['time_zone']) && $br_value['time_zone'] == 'CST') ? "selected='selected'" : '' ?>>CST</option>
                        <option value="EST" <?= (!empty($br_value['time_zone']) && $br_value['time_zone'] == 'EST') ? "selected='selected'" : '' ?>>EST</option>
                      </select>
                      <label>Time Zone</label>
                      <span class="error error_preview" id="error_schedule_time_zone_<?=$br_value['id']?>"></span>
                    </div>
                  </div>
                  <div class="col-sm-1 m-t-7">
                    <div class="form-group ">
                      <a class="addon_close  remove_schedule_group" href="javascript:void(0);" id="remove_schedule_group_<?=$br_value['id']?>" style="<?=$foreach_counter<2 ? 'display: none;' : ''?>">X</a>
                    </div>
                  </div>
                </div>
              </div>
            <?php }
          } ?>

        </div>
          <div class="clearfix">
            <a href="javascript:void(0);" class="btn red-link display_schedule" id="add_schedule" style="<?=(!empty($future_check_box) && $future_check_box == 'Y') ? '' : 'display: none;' ?>">+ Additional Date</a>
          </div>
          <input type="hidden" name="display_counter" id="display_counter" value="<?=$div_counter_in_php?>">
          <input type="hidden" name="div_counter" id="div_counter" value="<?=$div_counter_in_php?>">
          <input type="hidden" name="send_user_email" id="send_user_email" value="">
          <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
          <input type="hidden" name="broadcaster_id" id="broadcaster_id" value="<?=!empty($broadcaster_res['id']) ? $broadcaster_res['id'] : ''?>">
          <hr>
        </div>
        <input type="hidden" name="action_type" value="" id="action_type">
        <div class="text-right m-b-10">
          <?php if(empty($broadcaster_res) || (!empty($broadcaster_res) && $broadcaster_res['status'] != 'Completed') || $is_clone == 'Y') { ?>
            <a href="javascript:void(0);" class="btn btn-action" id="send_brodcast_btn">Send</a>
            <a href="javascript:void(0);" class="btn btn-info" id="draft_brodcast_btn">Save As Draft</a>
          <?php } ?>
          <a href="emailer_broadcaster.php" class="btn red-link">Cancel</a>
        </div>
      </div>
    </div>
  </div>
</form>
<div style="display:none">
  <div class="panel panel-default panel-block panel-shadowless mn" id="broadcast_preview_popup" >
    <div class="panel-heading">
      <div class="panel-title">
        <h4 class="fs18 mn">Preview -  <span class="fw300" id="broadcast_pre_name"></span></h4>
      </div>
    </div>
    <div class="panel-body p-t-0">
      <div class="bg_light_bg p-15">
        <p class="m-b-30" id="broadcast_pre_content"></p>
      </div>
      <div class="clearfix  text-center m-t-30">
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
      </div>
    </div>
  </div>
</div>
<!-- dynamic schedule div add start -->
<div id="dynamic_schedule_div" style="display: none;">
  <div class="inner_schedule_div" id="inner_schedule_div_~number~">
    <input type="hidden" name="dynamic_fields[~number~]">
    <div class="row">
      <div class="col-sm-3">
        <div class="form-group ">
          <div class="input-group">
            <a href="javascript:void(0);" class="input-group-addon schedule_date_icon" id="schedule_date_icon_~number~"><i class="fa fa-calendar "></i></a>
            <div class="pr">
              <input type="text" id="schedule_date_~number~" name="schedule_date[~number~]" class="form-control schedule_date_input_class" data-id="~number~" placeholder="MM / DD / YYYY">
            </div>
          </div>
          <span class="error error_preview" id="error_schedule_date_~number~"></span>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="form-group ">
          <select class="add_control_~number~" name="schedule_hour[~number~]">
            <option value=""></option>
            <?php for ($i=1; $i < 25; $i++) {
              $time_span = 'AM';
              $j = $i;
              if($i > 12) {
                $j = $i - 12;
                $time_span = 'PM';
              } ?>
              <option value="<?=$i?>"><?=$j . ' ' . $time_span?></option>
            <?php } ?>
          </select>
          <label>Hour</label>
          <span class="error error_preview" id="error_schedule_hour_~number~"></span>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="form-group ">
          <select class="add_control_~number~" name="schedule_time_zone[~number~]">
            <option value=""></option>
            <option value="CST">CST</option>
            <option value="EST">EST</option>
          </select>
          <label>Time Zone</label>
          <span class="error error_preview" id="error_schedule_time_zone_~number~"></span>
        </div>
      </div>
      <div class="col-sm-1 m-t-7">
        <div class="form-group ">
        <a class="addon_close  remove_schedule_group" href="javascript:void(0);" id="remove_schedule_group_~number~" style="display: none;">X</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- dynamic schedule div add end -->
<!-- Send popup div start -->
<div style="display:none">
  <div class="panel panel-default panel-block panel-shadowless mn" id="send_brodcast_template" >
    <div class="panel-body">
      <p class="fs16">What Email address would you like to send this test to?</p>
      <div class="theme-form">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <input type="text" class="form-control no_space" name="send_user_email_display" id="send_user_email_display">
              <label>Email</label>
              <span class="error error_preview" id="error_send_user_email"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix  text-center">
        <a href="javascript:void(0);" class="btn btn-action" id="send_test_email">Send Test</a> 
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
      </div>
    </div>
  </div>
</div>
<div  id="attachements_dynamic_div" style="display: none">
    <div id="attachment_file_div_~file_id~" class="m-t-15">
        <div class="phone-control-wrap">
            <div class="phone-addon">
                <input type="text" name="" placeholder="~file_name~" class="form-control" readonly="readonly">
            </div>
            <div class="phone-addon w-30">
                <a href="<?= $ATTACHMENS_WEB ?>~file_name~" download class="text-info fa-lg"> <i class="fa fa-download"></i></a>
              </div>
              <div class="phone-addon w-30">
                <a href="javascript:void(0)" data-id="~file_id~" class="text-action fa-lg delete_attachment"><i class="fa fa-trash"></i></a>
            </div>
        </div>
</div>
<div class="clearfix"></div>
</div>
<!-- Send popup div end -->

<script type="text/javascript">
  $(document).ready(function() {
    checkEmail();
    initCKEditor("broadcast_content",false,'360px');
    var not_win = '';
      $(".smart_tag_popup").on('click',function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=768,height=600");
      if(not_win.closed) {  
        alert('closed');  
      } 
    });

    $(".se_multiple_select").multipleSelect({
      selectAll: true,
      width: '100%',
      filter: true,
      selectableOptgroup: true
    });

    $(function(){
      $("#add_attachment").on('click', function(e){
          e.preventDefault();
          $("#attachment:hidden").trigger('click');
      });
    });

    $(document).off('change', '#attachment');
    $(document).on("change", "#attachment", function(e) {
        e.preventDefault();
        var filename = $('#attachment').val();
        if (filename != '') {
            $("#upload_type").val("file");
            $("#broadcast_content").val(CKEDITOR.instances['broadcast_content'].getData());
            $("#broadcast_form").submit();
        } else {
            $("#upload_type").val("");
        }
    });

    $(document).off('click', '.delete_attachment');
    $(document).on("click", ".delete_attachment", function(e) {
        e.stopPropagation();
        var id = $(this).attr('data-id');
        swal({
            text: 'Delete Attachment: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function() {
            $("#ajax_loader").show();
            $.ajax({
                url: '<?= $ADMIN_HOST ?>/ajax_delete_email_attachment.php',
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

    $('.schedule_date_icon').datepicker({
      "startDate": new Date(),
    }).on('changeDate', function(event){
      $schedule_input_id = event.target.id.replace("schedule_date_icon_","schedule_date_");
      $("#"+$schedule_input_id).val(event.format());
    });

    if($("#future_check_box").prop( "checked" ) === false){
      $("#send_brodcast_btn").html('Send');
    } else {
      $("#send_brodcast_btn").html('Schedule');
    }

  });

  $(document).off("click",".send_brodcast");
  $(document).on("click",".send_brodcast", function(){
    $broadcast_content = $("#broadcast_content").val();
    $broadcast_subject = $("#broadcast_subject").val();
    $email_template_id = $("#email_template").val();
    if($broadcast_content != '' && $broadcast_content != undefined && $broadcast_subject != '' && $broadcast_subject != undefined && $email_template_id != '' && $email_template_id != undefined){
      $("#error_broadcast_content").html('').hide();
      $("#error_broadcast_subject").html('').hide();
      $("#error_email_template").html('').hide();
      $.colorbox({
        inline:true,
        href:"#send_brodcast_template",
        height:"230px",
        width:"400px",
        closeButton: false,
      });
    } else {
      if($broadcast_content == '' || $broadcast_content == undefined){
        $("#error_broadcast_content").html('Content is required');
      }
      if($broadcast_subject == '' || $broadcast_subject == undefined){
        $("#error_broadcast_subject").html('Subject is required');
      }
      if($email_template_id == '' || $email_template_id == undefined){
        $("#error_email_template").html('Template is required');
      }
    }
  });

  $(document).off("click","#send_test_email");
  $(document).on("click","#send_test_email", function(){
    $("#send_user_email").val($("#send_user_email_display").val());
    $("#action_type").val("send_email");
    $("#upload_type").val("form");
    $("#broadcast_content").val(CKEDITOR.instances['broadcast_content'].getData());
    $("#broadcast_form").submit();
  });

  $(document).off("click",".broadcast_preview");
  $(document).on("click",".broadcast_preview", function(){
    $broadcast_name = $("#broadcast_name").val();
    $broadcast_content = CKEDITOR.instances['broadcast_content'].getData();
    if($broadcast_name != '' && $broadcast_name != undefined && $broadcast_content != '' && $broadcast_content != undefined){
      $("#error_broadcast_name").html('').hide();
      $("#error_broadcast_content").html('').hide();
      $("#broadcast_pre_name").html($broadcast_name);
      $("#broadcast_pre_content").html($broadcast_content);
      $.colorbox({
        inline:true,
        href:"#broadcast_preview_popup",
        height:"600px",
        width:"685px",
      });
    } else {
      if($broadcast_name == '' || $broadcast_name == undefined){
        $("#error_broadcast_name").html('Broadcast Name is required');
      }
      if($broadcast_content == '' || $broadcast_content == undefined){
        $("#error_broadcast_content").html('Content is required');
      }
    }
  });

  $(document).off("change", "#user_group");
  $(document).on("change", "#user_group", function(){
    if($(this).val() != '' && $(this).val() != undefined){
      $user_group_name = $(this).val();
      if($user_group_name == 'Employer Groups'){
        $user_group_name = "Groups";
      }
      $("#specific_user_group_label").html('Specific ' + $user_group_name);
      $("#filter_specific_selection_div").show();
      $("#specific_user_group").prop("checked", false);
      $.uniform.update();
      $(".specific_value_div").hide();
      if($(this).val() == 'Admins'){
        $("#filter_div_expect_admin").hide();
        $("#filter_div_admins").show();
        $("#filter_div_leads").hide();
      } else if($(this).val() == 'Leads'){
        $("#filter_div_expect_admin").hide();
        $("#filter_div_admins").hide();
        $("#filter_div_leads").show();
      } else {
        $("#filter_div_expect_admin").show();
        $("#filter_div_admins").hide();
        $("#filter_div_leads").hide();
        if($(this).val() == 'Agents'){
          $('.filter_div_agents').show();
        }else{
          $('.filter_div_agents').hide();
        }
        if($(this).val() == 'Employer Groups' || $(this).val() == 'Members'){
          $('.filter_div_groups').show();
        }else{
          $('.filter_div_groups').hide();
        }
        if($(this).val() == 'Members'){
          $('.member_state').show();
        }else{
          $('.member_state').hide();
        }
      }
    } else {
      if($("#specific_user_group").prop( "checked" ) == true){
        $("#specific_user_group").prop("checked", false);
        $.uniform.update();
      }
      $(".filter_div").hide();
    }
  });

  $(document).off("click","#specific_user_group");
  $(document).on("click","#specific_user_group", function(){
    $user_group_type = $("#user_group").val();
    if($(this).prop( "checked" ) === false){
      if($user_group_type == 'Admins'){
        $("#filter_div_specific_admins").hide();
        $("#filter_div_admins").show();
      } else if($user_group_type == 'Leads'){
        $("#filter_div_specific_leads").hide();
        $("#filter_div_leads").show();
      } else {
        $("#filter_div_expect_admin").show();
        $("#filter_div_specific_agents").hide();
        $("#filter_div_specific_group").hide();
        $("#filter_div_specific_member").hide();
      }
    } else {
      if($user_group_type == 'Admins'){
        $("#filter_div_specific_admins").show();
        $("#filter_div_admins").hide();
      } else if($user_group_type == 'Leads'){
        $("#filter_div_specific_leads").show();
        $("#filter_div_leads").hide();
      } else {

        $("#filter_div_expect_admin").hide();
        if($user_group_type == 'Agents'){
          $("#filter_div_specific_agents").show();
          $("#filter_div_specific_group").hide();
          $("#filter_div_specific_member").hide();
        } else if($user_group_type == 'Employer Groups'){
          $("#filter_div_specific_agents").hide();
          $("#filter_div_specific_group").show();
          $("#filter_div_specific_member").hide();
        } else if($user_group_type == 'Members'){
          $("#filter_div_specific_agents").hide();
          $("#filter_div_specific_group").hide();
          $("#filter_div_specific_member").show();
        } else {
          $("#filter_div_specific_agents").hide();
          $("#filter_div_specific_group").hide();
          $("#filter_div_specific_member").show();
        }
      }
    }
  });

  $(document).off("click","#future_check_box");
  $(document).on("click","#future_check_box", function(){
    if($(this).prop( "checked" ) === false){
      $("#send_brodcast_btn").html('Send');
      $(".display_schedule").hide();
      $("#main_schedule_div").html('');
    } else {
      $("#send_brodcast_btn").html('Schedule');
      $(".display_schedule").show();
      loadScheduleDiv();
      $div_counter = parseInt($("#div_counter").val()) + 1;
      $("#div_counter").val($div_counter);
    }
  });

  $(document).off('click','#add_schedule');
  $(document).on('click','#add_schedule', function(){
    $div_counter = parseInt($("#div_counter").val()) + 1;
    $("#div_counter").val($div_counter);
    loadScheduleDiv();
    if($div_counter > 9){
      $(this).hide();
    }
  });

  $(document).off("click",".remove_schedule_group");
  $(document).on("click",".remove_schedule_group", function(){
    $div_id = $(this).attr('id').replace("remove_schedule_group_","");
    $div_counter = parseInt($("#div_counter").val()) - 1;
    $("#div_counter").val($div_counter);
    $("#inner_schedule_div_"+$div_id).remove();
    if($div_counter < 11){
      $("#add_schedule").show();
    }
  });

  $(document).off("click","#send_brodcast_btn");
  $(document).on("click","#send_brodcast_btn",function(){
    $(".error").html('').hide();
    $("#action_type").val('send');
    $("#upload_type").val("form");
    $("#broadcast_content").val(CKEDITOR.instances['broadcast_content'].getData());
    $("#broadcast_form").submit();
    
  });

  $(document).off("click","#draft_brodcast_btn");
  $(document).on("click","#draft_brodcast_btn",function(){
    $("#action_type").val('draft');
    $("#upload_type").val("form");
    $("#broadcast_content").val(CKEDITOR.instances['broadcast_content'].getData());
    $("#broadcast_form").submit();
  });

  $(document).off("click", ".schedule_date_input_class");
  $(document).on("click", ".schedule_date_input_class", function(){
     $("#schedule_date_icon_"+$(this).attr('data-id')).datepicker('show');
  });

  loadScheduleDiv = function(){
    $count = $("#broadcast_form .inner_schedule_div").length;
    $display_btn = 'N';
    if($count >= 1){
      $display_btn = 'Y';
    }
    $number = $count+1;
    $display_counter = parseInt($('#display_counter').val());
    if($display_counter > $count){
      $number = $display_counter + 1;
    }

    $neg_number = $number * -1;

    html = $('#dynamic_schedule_div').html();
    $('#main_schedule_div').append(html.replace(/~number~/g, $neg_number));
    $("#schedule_date_"+$neg_number).inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    if($display_btn == 'Y'){
      $("#remove_schedule_group_"+$neg_number).show();
    }

    $('#schedule_date_icon_'+$neg_number).datepicker({
      "startDate": new Date(),
    }).on('changeDate', function(event){
      $schedule_input_id = event.target.id.replace("schedule_date_icon_","schedule_date_");
      $("#"+$schedule_input_id).val(event.format());
    });

    $("#display_counter").val($number);
    $("#broadcast_form .add_control_"+$neg_number).addClass("form-control");
    $('#broadcast_form .add_control_'+$neg_number).selectpicker({ 
      container: 'body', 
      style:'btn-select',
      noneSelectedText: '',
      dropupAuto:false,
    });
  };

  $('#broadcast_form').ajaxForm({
      beforeSubmit: function(arr, $form, options) {
          $("#ajax_loader").show();
      },
      url:"<?= $ADMIN_HOST ?>/ajax_add_email_broadcast.php",
      method: 'POST',
      dataType: 'json',
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == 'success') {
          $(".error").html('').hide();
          if(res.action_type == 'send_email'){
            setNotifySuccess("Test Email send Successfully!");
            parent.$.colorbox.close(); return false;
          } else {
            setNotifySuccess("Emailer Broadcaster Added Successfully!");
            setTimeout(function(){ 
              window.location.href = 'emailer_broadcaster.php';
            }, 1000);
          }
          $("#ajax_loader").hide();
        }else if (res.status == "success_file") {
          $("#email_attachment_id").val(res.attachment);
          if (res.files_info.length > 0) {
              $.each(res.files_info, function($k, $v) {
                  $html_append = $('#attachements_dynamic_div').html();
                  $html_append = $html_append.replace(/~file_name~/g, $v.file_name);
                  $html_append = $html_append.replace(/~file_display_name~/g, $v.file_display_name);
                  $html_append = $html_append.replace(/~file_id~/g, $v.file_id);
                  $('#attachements_inner_div').append($html_append);
              });
          }
          // $("#file_div_inner").html($file_div_inner);
          setNotifySuccess(res.message);
        }else if (res.status == 'fail') {
          $("#ajax_loader").hide();
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
          });
        }
      }
    });
</script>