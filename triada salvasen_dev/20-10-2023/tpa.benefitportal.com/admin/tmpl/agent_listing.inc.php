<?php if ($is_ajaxed) { ?> 
<div class="clearfix tbl_filter">
    <?php if ($total_rows > 0) {?>
        <div class="pull-left">
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                <div class="form-group mn">
                    <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group mn">
                    <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
                        <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="pull-right">
            <div class="m-b-15">
                <?php if($module_access_type == "rw") { ?>
                    <a class="btn btn-default m-r-5" href="manage_agents.php"> Manage Agents</a>
                    <a class="btn btn-action mn" href="invite_agent.php">+ Agent</a>
                <?php } ?>
            </div>
        </div>
        <?php }?>
</div>
<div class="table-responsive">
    <table class="<?=$table_class?> ">
        <thead>
            <tr class="data-head">
                <th><a href="javascript:void(0);" data-column="c.joined_date" data-direction="<?php echo $SortBy == 'c.joined_date' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date ID</a></th>
                <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
                <th><a href="javascript:void(0);" data-column="cs.company" data-direction="<?php echo $SortBy == 'cs.company' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Company</a></th>
                <th width="10%"><a href="javascript:void(0);" data-column="c.type" data-direction="<?php echo $SortBy == 'c.type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Account Type</a></th>
                <th class="text-center"><a href="javascript:void(0);" data-column="total_products" data-direction="<?php echo $SortBy == 'total_products' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Products</a></th>
                <th><a href="javascript:void(0);" data-column="cs.agent_coded_level" data-direction="<?php echo $SortBy == 'cs.agent_coded_level' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Level</a></th>
                <?php if($module_access_type == "rw") { ?>
                    <th class="text-center">Tree</th>
                <?php } ?>
                <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
                <th  class="text-center">Alerts</th>
                <?php if($module_access_type == "rw") { ?>
                    <th width="130px" >Actions</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td>
                            <?php if($module_access_type == "rw") { ?>
                                <a href="agent_detail_v1.php?id=<?=$rows['id']?>" target="_blank"  class="text-red">
                                <strong class="fw600"><?php echo $rows['rep_id']; ?></strong></a></br>
                            <?php } else { ?>
                                <a href="javascript:void(0)" class="text-red">
                                <strong class="fw600"><?php echo $rows['rep_id']; ?></strong></a></br>
                            <?php } ?>
                            
                            <?php echo empty($rows['joined_date']) ? date('m/d/Y', strtotime($rows['invite_at']))  : date('m/d/Y', strtotime($rows['joined_date'])); ?>
                        </td>
                        <td>
                            <strong><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></strong> <br />
                            <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $rows['cell_phone']) ?><br/>
                            <?php echo $rows['email']; ?><br />
                            <?php echo $rows['company_name']; ?>
                        </td>
                        <td><?=$rows['company']?></td>
                        <td><?php
                            if($rows['account_type'] == 'Business'){
                                echo "Agency";
                            }else if($rows['account_type'] == 'Personal'){
                                echo "Agent";
                            }else{
                                echo !empty($rows['account_type']) ? $rows['account_type'] : '-' ;
                            }
                        ?></td>
                        <td class="text-center"><a href="javascript:void(0);" class="fw600 text-red agent_products" data-id="<?=$rows['id'];?>"><?=$rows['total_products']?></a></td>

                        <td class="w-200 text-center" id="agent_level_td_<?= $rows['id'] ?>">
                            <?php if ($rows['id'] == md5(1)) { ?>
                                <span>Root</span>
                            <?php } else { ?>
                                <?php foreach ($agentCodedRes as $level) { ?>
                                    <?php if ($level['id'] == $rows['agent_coded_id']) { ?>
                                        <div class="phone-control-wrap">
                                            <div class="phone-addon">
                                                <label class="mn"><?= $level['level_heading'] ?>&nbsp;</label>
                                            </div>
                                            <div class="phone-addon">
                                                <div class="input-group">
                                                    <?php if ($module_access_type == "rw") { ?>
                                                        <a href="javascript:void(0);" data-id="<?= $rows['id'] ?>" id="level_edit" data-old_lvl_id="<?= $rows['agent_coded_id'] ?>" data-old_lvl_val="<?= $agentCodedRes[$rows['agent_coded_id']]['level'] ?> ">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </div><span></span>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                        </td>
                        
                        <?php if($module_access_type == "rw") { ?>
                        <td class="text-center">
                            <a href="javascript:void(0);" data-href="agent_tree_popup.php?agent_id=<?=$rows['id']?>" class="agent_tree_popup"><img src="images/icons/icon-tree.svg" width="24px" /></a> 
                        </td>
                        <?php } ?>

                        <?php if($module_access_type == "rw") { ?>
                        <td class="w-200">
                            <?php if (in_array($rows['status'], array('Invited', 'Pending Approval', 'Pending Contract', 'Pending Documentation', 'Agent Abandon'))) { ?>
                                <?php if($rows['status'] != 'Invited'){ ?>
                                    <a href="javascript:void(0)" class="agent_status" id = "agent_status_<?=$rows['id']?>" data-status="<?=$rows["status"] == 'Active'?'Contracted':$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content=""> <?=$rows["status"] == 'Active'?'Contracted': $rows["status"]?></a>
                                <?php } else if(($rows['status'] == 'Invited') && ($rows['invite_time_diff'] >168)){ ?>
                                    <a href= "reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="resend_popup btn btn-action btn-action-o w-130"  data-toggle="tooltip" title="Re-Invite"  id = "agent_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content="" > <i class="fa fa-envelope re_invite_icon" aria-hidden="true"></i>&nbsp;<?=$rows["invite_time_diff"] > 168 ? 'Re-invite':'Re-invite'?></a>
                                <?php } else { ?>
                                    <a href= "reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="resend_popup btn btn-action btn-action-o w-130" data-toggle="tooltip" title="Re-invite"  id = "agent_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content=""><i class="fa fa-envelope re_invite_icon" aria-hidden="true"></i>&nbsp; <?=$rows["status"] == 'Active'?'Contracted': 'Re-invite'?></a>
                                <?php } ?>
                                <div id="popover_content_<?=$rows['id']?>" style="display: none">
                                    <h4 class="font-normal"><i class="fa fa-info-circle text-red"></i> Agent Contracting Process</h4>
                                    <hr />
                                    <table>
                                        <tr class="status_div <?=$rows['status']=='Invited'?'text-success':'' ?>">
                                            <td width="180px" valign="top"><strong>Invited</strong></td>
                                            <td class="p-b-10">Agent has been extended an invite but has yet to accept and create account.</td>
                                        </tr>
                                        <tr class="status_div <?=$rows['status']=='Pending Documentation'?'text-success':'' ?>">
                                            <td valign="top"><strong class="text-nowrap">Pending Documentation</strong></td>
                                            <td class="p-b-10"> Agent has accepted the invite, but has yet to submit account documentation (W9, Agent License, E&O Insurance, etc.) for approval by admin </td>
                                        </tr>
                                        <tr class="status_div <?=(($rows['status']=='Pending Approval') || ($rows['status']=='Pending Review')) ?'text-success':'' ?>">
                                            <td valign="top"><strong>Pending Approval</strong></td>
                                            <td class="p-b-10">Agent has submitted documentation for review by admin, but admin has yet to review.</td>
                                        </tr>
                                        <tr class="status_div <?=$rows['status']=='Pending Contract'?'text-success':'' ?>">
                                            <td valign="top"><strong>Pending Contract</strong></td>
                                            <td class="p-b-10">Admin has approved account, but agent has yet to login to account and sign the contract.</td>
                                        </tr>
                                    </table>
                                </div>
                            <?php }else if(in_array($rows['status'], array('Contracted','Suspended','Terminated','Active'))){?>
                                <div class="theme-form pr">
                                    <select name="member_status" class="form-control member_status has-value" id="member_status_<?=$rows['id'];?>" data-old_member_status="<?=$rows['status'] ?>">
                                        <option value="Active" <?php if ($rows['status'] == 'Active') { echo "selected='selected'"; } ?>>Contracted</option>
                                        <option value="Suspended" <?php if ($rows['status'] == 'Suspended') { echo "selected='selected'"; } ?>>Suspended</option>
                                        <option value="Terminated" <?php if ($rows['status'] == 'Terminated') { echo "selected='selected'"; } ?>>Terminated</option>
                                    </select>
                                    <label>Status</label>
                                </div>
                            <?php } else {
                                echo $rows['status'];
                            }?>
                        </td>
                        <?php } else { ?>
                        <td class="w-200"><?=$rows['status']?></td>
                        <?php } ?>
                        
                        <td class="text-center">
                            <?php
                            if($rows['countNewLicenseRequest'] > 0){
                                echo '<a data-toggle="tooltip" data-trigger="hover" href="javascript:void(0);" title="License Approval Request"><i class="fa fa-exclamation-circle fa-lg"></i></a>';
                            } else if($rows['countExpired'] != 0){
                                echo '<a data-toggle="tooltip" data-trigger="hover" href="javascript:void(0);" title="License Expired"><i class="fa fa-exclamation-circle fa-lg"></i></a>';
                            }else if(strtotime(date('Y-m-d')) > strtotime($rows['e_o_expiration'])){
                                echo '<a data-toggle="tooltip" data-trigger="hover" href="javascript:void(0);" title="E&O Expired"><i class="fa fa-exclamation-circle fa-lg"></i></a>';
                            }
                            else{
                                echo '-';
                            }
                             ?>
                        </td>

                        <?php if($module_access_type == "rw") { ?>
                        <td class="icons">
                                <a href="agent_detail_v1.php?id=<?=$rows['id']?>" target="_blank" data-toggle="tooltip" data-trigger="hover" title="Edit Profile"><i
                                class="fa fa-eye"></i></a>
                                <?php if (!in_array($rows['status'], array('Invited')) && $rows["stored_password"] != "") { ?>
                                    <a data-toggle="tooltip" data-trigger="hover" href="switch_login.php?id=<?php echo $rows['id']; ?>" target="blank" title="Access Agent Site"><i class="fa fa-lock"></i></a>
                                <?php }?>

                                <?php if($rows['status']=="Terminated" || $rows['status']!="Active") { ?>
                                <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" title="Delete" onclick="delete_agent('<?=$rows['id']?>')"><i class="fa fa-trash"></i></a>
                            <?php } ?>
                        </td>
                        <?php } ?>
                        
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <?php if($module_access_type == "rw") { ?>
                    <td colspan="10" align="center">No record(s) found</td>
                    <?php } else {?>
                    <td colspan="8" align="center">No record(s) found</td>
                    <?php }?>
                </tr>
            <?php }?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                <?php if($module_access_type == "rw") { ?>
                <td colspan="10"><?php echo $paginate->links_html; ?></td>
                <?php } else {?>
                <td colspan="8"><?php echo $paginate->links_html; ?></td>
                <?php }?>
            </tr>
            </tfoot>
        <?php } ?>
    </table>
</div>
<?php } else { ?>
<div class="panel panel-default panel-block panel-title-block">
    <form id="frm_search" action="agent_listing.php" method="GET" class="theme-form" autocomplete="off">
        <div class="panel-left">
            <div class="panel-left-nav">
                <ul>
                    <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-right">
            <div class="panel-heading">
                <div class="panel-search-title"> 
                    <span class="clr-light-blk">SEARCH</span>
                </div>
            </div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body theme-form">
                    <div class="row">
                        <?php /* <div class="col-md-6">
                            <div class="form-group ">
                                    <select class="se_multiple_select listing_search" name="rep_id[]" id="rep_id" multiple="multiple">
                                        <?php if(!empty($tree_agent_res)){ ?>
                                            <?php foreach($tree_agent_res as $value){ ?>
                                                <option value="<?=$value['rep_id']?>"><?=$value['rep_id'].' - '.$value['fname'].' '.$value['lname']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <label>ID Number(s)</label>
                            </div>
                        </div> */ ?>
                        <div class="col-md-6">
                            <div class="form-group height_auto">
                            <input name="rep_id" id="rep_id" type="text" class="listing_search" value="<?= checkIsset($rep_id) ?>"/>
                            <label>Agent ID/Name(s)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row" id="show_date">
                            <div id="date_range" class="col-md-12">
                              <div class="form-group">
                                <select class="form-control" id="join_range" name="join_range">
                                  <option value=""> </option>
                                  <option value="Range">Range</option>
                                  <option value="Exactly">Exactly</option>
                                  <option value="Before">Before</option>
                                  <option value="After">After</option>
                                </select>
                                <label>Added Date</label>
                              </div>
                            </div>
                            <div class="select_date_div col-md-9" style="display:none">
                              <div class="form-group">
                                <div id="all_join" class="input-group"> 
                                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                  <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                                </div>
                                <div  id="range_join" style="display:none;">
                                  <div class="phone-control-wrap">
                                    <div class="phone-addon">
                                      <label class="mn">From</label>
                                    </div>
                                    <div class="phone-addon">
                                      <div class="input-group"> 
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                      </div>
                                    </div>
                                    <div class="phone-addon">
                                      <label class="mn">To</label>
                                    </div>
                                    <div class="phone-addon">
                                      <div class="input-group"> 
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control listing_search" name="bus_name" value="<?= checkIsset($bus_name) ?>">
                                <label>Agency Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group height_auto">
								<input name="company" id="company" type="text" class="listing_search" value="<?= checkIsset($company) ?>" />
								<label>Company</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control listing_search" name="p_agent_name" id="p_agent_name" value="">
                                <label>Principal Agent Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control listing_search" name="phone" maxlength='10' onkeypress="return isNumberKey(event)" value="<?php echo $phone ?>">
                                <label>Phone</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control listing_search" name="email" value="<?= checkIsset($email) ?>">
                                <label>Email</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="form-control listing_search" name="account_type" id="account_type">
                                    <option value=""></option>
                                    <option value="Business">Agency</option>
                                    <option value="Personal">Agent</option>
                                </select>
                                <label>Account Type</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group ">
                                    <select class="se_multiple_select listing_search" name="agent_level[]"  id="agent_level" multiple="multiple">
                                        <?php 
                                        if (!empty($agentCodedRes)) { ?>
                                            <?php foreach ($agentCodedRes as $level) { ?>
                                                <option value="<?=$level['id']?>"><?=$level['level_heading']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <label>Level</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select name="agents_status" id="agents_status" class="form-control listing_search"
                                    id="member_status" >
                                    <option value=""> </option>
                                    <option value="Invited" >Invited</option>
                                    <option value="Pending Documentation" >Pending Documentation</option>
                                    <option value="Pending Approval" >Pending Approval</option>
                                    <option value="Pending Contract" >Pending Contract</option>
                                    <option value="Active" >Contracted</option>
                                    <option value="Suspended" >Suspended</option>
                                    <option value="Terminated" >Terminated</option>
                                </select>
                                <label>Status</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="form-control listing_search" name="select_alert" id="select_alert">
                                    <option value=""> </option>
                                    <option value="eo_expired">E&O Expired</option>
                                    <option value="license_expired">License Expired</option>
                                    <option value="license_approval_request">License Approval Request</option>
                                </select>
                                <label>Alerts</label>
                            </div>
                        </div>
                        <?php /*<div class="col-sm-6">
                            <div class="form-group height_auto">
                            <input name="tree_agent_id" id="tree_agent_id" type="text" class="listing_search" value="<?= checkIsset($tree_agent_id) ?>"/>
                            <label>Tree Agent ID/Name(s)</label>
                            </div>
                        </div> */ ?>
                        <!-- <?php //echo getAgencySelect('tree_agent_id')?> -->
                        <div class="col-sm-6">
                            <div class="form-group height_auto">
                                <input name="combination_product" id="combination_product" type="text" class="listing_search" value="<?= checkIsset($combination_product) ?>" />
                                <label>Assigned Product(s)</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group height_auto">
                                <input name="tree_agent_id" id="treeAgentID" type="text" class="listing_search" value="<?= checkIsset($treeAgentID) ?>" />
                                <label>Agency</label>
                            </div>
                        </div>
                        <?php /*<div class="col-sm-6">
                            <div class="form-group ">
                                    <select class="se_multiple_select listing_search" name="tree_agent_id[]" id="tree_agent_id" multiple="multiple">
                                        <?php if(!empty($tree_agent_res)){ ?>
                                            <?php foreach($tree_agent_res as $value){ ?>
                                                <option value="<?=$value['id']?>"><?=$value['rep_id'].' - '.$value['fname'].' '.$value['lname']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <label>Tree Agent ID(s)</label>
                            </div>
                        </div> */ ?> 
                        <div class="col-sm-6">
                            <div class="form-group height_auto">
                                <select class="form-control listing_search" name="advances"  title="&nbsp;">
                                    <option value=""></option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                                <label>Assigned Advances</label>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="form-control listing_search" name="pmpms" >
                                    <option value=""> </option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                                <label>Assigned PMPMs</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group height_auto">
                                    <input name="license_state" id="license_state" type="text" class="listing_search" value="<?= checkIsset($license_state) ?>" />
                                    <label>License State</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="form-control listing_search" name="license_status" id="license_status" >
                                    <option value=""></option>
                                    <option value="Active">Active</option>
                                    <option value="Expired">Expired</option>
                                    <option value="Not Setup">Not Setup</option>
                                </select>
                                <label>License Refine Filter </label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-info" name="search" id="search"><i class="fa fa-search"></i> Search
                        </button>
                        <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'agent_listing.php'"><i class="fa fa-search-plus"></i>  View All
                        </button>
                        <?php if($module_access_type == "rw") { ?>
                        <button type="button" name="export" id="export" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
                        <?php } ?>
                        
                        <input type="hidden" name="export_val" id="export_val" value="">
                        <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                        <input type="hidden" name="dis_id" id="dis_id" value="<?=$dis_id?>"/>
                        <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
                        <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
                        <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="search-handle">
        <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
    </div>
</div>
<div class="panel panel-default panel-block">
        <div id="ajax_loader" class="ajex_loader" style="display: none;">
            <div class="loader"></div>
        </div>
        <div id="ajax_data" class="panel-body"></div>
</div>
 
<script type="text/javascript">
$(document).off('click',".agent_tree_popup");
$(document).on('click',".agent_tree_popup",function(e){
    $href = $(this).attr('data-href');
    $.colorbox({
        iframe:true,
        href:$href,
        width: '900px',
        height: '650px'
    });
});

$(document).off('click', '.agent_products');
  $(document).on('click', '.agent_products', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $.colorbox({
      href: "agent_products_popup.php?id=" + id,
      iframe: true,
      width: '800px',
      height: '450px'
    });
  });

// Agent level change
$(document).off('change', '.agent_level_change');
$(document).on('change', '.agent_level_change', function(e) {
  e.stopPropagation();
  var id = $(this).attr('id').replace('agent_level_change_', '');
  var level = $(this).val();
  var level_id = $("#agent_level_change_"+id+" option:selected").attr('data-id');
  var old_show = $("#agent_level_change_" + id).data('old_show');
  var old_level_id = $(this).attr('data-old_lvl_id');
  var old_lvl_val = $(this).attr('data-old_lvl_val');
  console.log(id+" "+level+" "+level_id+" "+old_level_id);
  if(level_id !== old_level_id){
    swal({
      text: "<br>Change Level: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
      window.location = 'ajax_agent_level_change.php?agent_id=' + id + '&level_id=' + level_id+"&level="+level;
    },function(dismiss){
        add_normal_level(id,old_show,old_level_id,old_lvl_val);
        return false;
    });
  }
});
    
    $(document).ready(function() {

        dropdown_pagination('ajax_data');

        $(".date_picker").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true
        });

        ajax_submit();

        $("#agent_level").multipleSelect({
            selectAll: false,
            filter:true
        });

        initSelectize('rep_id','AgentID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
		initSelectize('company','agentCompanyID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
		initSelectize('combination_product','agentProductsID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
		initSelectize('treeAgentID','treeAgentID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
		initSelectize('license_state','license_state',1);

    });

    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
        e.preventDefault();
        $('.date_picker').val('');
        if ($(this).val() == '') {
            $('.select_date_div').hide();
            $('#date_range').removeClass('col-md-3').addClass('col-md-12');
        } else {
            $('#date_range').removeClass('col-md-12').addClass('col-md-3');
            $('.select_date_div').show();
            if ($(this).val() == 'Range') {
                $('#range_join').show();
                $('#all_join').hide();
            } else {
                $('#range_join').hide();
                $('#all_join').show();
            }
        }
    });

    $(document).off('click', '.resend_popup');
    $(document).on('click', '.resend_popup', function (e) {
        e.preventDefault();
        $.colorbox({
          href: $(this).attr('href'),
          iframe: true, 
          width: '768px', 
          height: '240px'
        })
    });

    $(document).off('change', '.member_status');
    $(document).on("change", ".member_status", function(e) {
        e.stopPropagation();
        var id = $(this).attr('id').replace('member_status_', '');
        var member_status = $(this).val();
        var $val = $(this).val();
        var old_status = $(this).attr('data-old_member_status');

        var selText = $("#member_status_"+id+" option:selected").text();
        var $txt = '';
        if(selText === 'Contracted'){
            $txt = 'Contracted Status: Contracted status allows agent to login to account, continues payment of renewal commissions, and allows new applications.';
        }else if($val === 'Suspended'){
            $txt = 'Suspended Status: Suspended status allows agent to login to account, continues payment of renewal commissions, but stops new applications.'
        }else if($val === 'Terminated'){
            $txt = 'Terminated Status: Terminated status blocks agent access to login to account, stops payment of renewal commissions, and stops new applications.';
        }
        
        swal({
            title: "<h4>Change Agent Status to  <span class='text-blue'>"+$val+"</span>: Are you sure?</h4>",
            html:'<p class="fs14 m-b-15">'+$txt+'</p><div class="text-center fs14"><div class="d-inline  text-left"><label class="m-b-10 "><input name="downline" type="checkbox" value="downline" id="downline" class="js-switch" autofocus> <span class="p-l-10"> Apply to downline agents? </span> </label><div class="clearfix"></div>' + '<label class="m-b-10" ><input name="loa" type="checkbox" value="loa" id="loa" class="js-switch"> <span class="p-l-10"> Apply to LOA agents?</span></label></div></div>',
            showCancelButton : true,
            confirmButtonText: "Confirm",
            cancelButtonText: "Cancel",
        }).then(function(e1) {
            if(e1){

                var $downline = '';
                if($("#downline").is(":checked")){
                    var $downline = $("#downline").val();
                }
                var $loa = '';
                if($("#loa").is(":checked")){
                    $loa = $("#loa").val();
                }
                
                if (member_status == 'Terminated' || member_status == 'Suspended') {
                    $.colorbox({
                        iframe: true,
                        href: "<?=$ADMIN_HOST?>/reason_change_agent_status.php?id=" + id + "&status=" + member_status + "&downline="+$downline+"&loa="+$loa+"&old_status="+old_status,
                        width: '600px',
                        height: '260px',
                        trapFocus: false,
                        closeButton: false,
                        overlayClose: false,
                        escKey: false
                    });
                } else {
                    $.ajax({
                        url: 'ajax_change_agent_status.php',
                        data: {
                            id: id,
                            downline:$downline,
                            loa:$loa,
                            status: member_status
                        },
                        method: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.status == "success") {
                                setNotifySuccess(res.msg);
                            }else{
                                setNotifyError(res.msg);
                            }
                            ajax_submit();
                        }
                    });
                }
            }
        }, function(dismiss) {
            $('#member_status_'+id).val(old_status);
            $('#member_status_'+id).selectpicker('render');
            return false;
        })
    });

    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit();
    });

    $(document).off('click', '#ajax_data ul.pagination li a');
    $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#ajax_data').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
            }
        });
    });
           
    $(document).off("submit","#frm_search");
    $(document).on("submit","#frm_search",function(e){
        e.preventDefault();
        disable_search();
    });

    function get_status($id) {
        $('#agent_status_' + $id).html('Re-Invite');
    }

    function delete_agent(agent_id) {
        swal({
            text: '<br>Delete Record: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function() {
            $("#ajax_loader").show();
            $.ajax({
                url: "ajax_delete_agent.php",
                type: 'GET',
                data: {
                    id: agent_id,
                    search:'Y'
                },
                dataType: 'JSON',
                success: function(res) {
                    $("#ajax_loader").hide();
                    if (res.status == 'success') {
                        setNotifySuccess(res.msg);
                        ajax_submit();
                    } else {
                        setNotifyError(res.msg);
                    }
                }
            });
        }, function(dismiss) {})
    }

    function ajax_submit() {
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $('#is_ajaxed').val('1');
        var params = $('#frm_search').serialize();
        $.ajax({
            url: $('#frm_search').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#ajax_data').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
                $("[data-toggle=popover]").each(function(i, obj) {
                    $(this).popover({
                        html: true,
                        placement: 'auto bottom',
                        content: function() {
                            var id = $(this).attr('data-user_id')
                            return $('#popover_content_' + id).html();
                        }
                    });
                });
            }
        });
        return false;
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)){
            return false;
        }
        return true;
    }

    $(document).off('click', '#export');
    $(document).on('click', '#export', function (e) {
        e.stopPropagation();

        confirm_export_data(function() {
            $("#export_val").val(1);
            $('#ajax_loader').show();
            $('#is_ajaxed').val('1');
            var params = $('#frm_search').serialize();
            $.ajax({
                url: $('#frm_search').attr('action'),
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(res) {
                    $('#ajax_loader').hide();
                    $("#export_val").val('');
                    if(res.status == "success") {
                        confirm_view_export_request();
                    } else {
                        setNotifyError(res.message);
                    }
                }
            });
        });
    });

    function refreshMemberStatus(memberId,oldStatus){
        $('#member_status_'+memberId).val(oldStatus);
        $('#member_status_'+memberId).attr('data-old_member_status',oldStatus);
        $('#member_status_'+memberId).selectpicker('render');
        $.colorbox.close();
        return false;
    }
    
    $(document).off('click', '#level_edit');
    $(document).on('click', '#level_edit', function(e) {
        var id = $(this).data('id');
        var data = $('#agent_level_td_' + id + ' label').html();
        $.ajax({
            url: "ajax_agent_level_data.php",
            type: 'POST',
            data: {
                id: id,
                data: data
            },
            success: function(res) {
                $('#agent_level_td_' + id).html(res);
                common_select();
            }
        });
    });

    $(document).off('click', '.cancel_level_select');
    $(document).on('click', '.cancel_level_select', function(e) {
        e.preventDefault();
        var id = $(this).attr('id').replace('cancel_level_select_', '');
        var level = $(this).val();
        var level_id = $("#agent_level_change_" + id + " option:selected").attr('data-id');
        var old_show = $("#agent_level_change_" + id).data('old_show');
        var old_level_id = $(this).attr('data-old_lvl_id');
        var old_lvl_val = $(this).attr('data-old_lvl_val');
        $('.agent_level_change').removeClass('open');
        $('.agent_level_change > .dropdown-toggle').attr("aria-expanded","false");
        add_normal_level(id,old_show,old_level_id,old_lvl_val);
    });

    add_normal_level = function(id,old_show,old_level_id,old_lvl_val){
        $html = '<div class="phone-control-wrap">';
        $html +='<div class="phone-addon">';
        $html += '<label class="mn">'+ old_show +'</label>';
        $html += '</div>';
        $html += '<div class="phone-addon">';
        $html += '<div class="input-group">';
        $html += '<a href="javascript:void(0);" data-id="'+id+'" id="level_edit" data-old_lvl_id="' + old_level_id + '" data-old_lvl_val="' + old_lvl_val + '">';
        $html += '<i class="fa fa-pencil"></i>';
        $html += '</a> </div> </div><span></span> </div>';
        $('#agent_level_td_' + id).html($html);
    }
</script>
<?php } ?>