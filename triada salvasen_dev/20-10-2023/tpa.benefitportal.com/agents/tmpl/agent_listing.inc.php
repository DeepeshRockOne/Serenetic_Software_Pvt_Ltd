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
                <a class="btn btn-action mn" href="invite_agent.php"><i class="fa fa-plus"></i> Agents</a>
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
                <th><a href="javascript:void(0);" data-column="c.type" data-direction="<?php echo $SortBy == 'c.type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Account Type</a></th>
                <th><a href="javascript:void(0);" data-column="cs.agent_coded_level" data-direction="<?php echo $SortBy == 'cs.agent_coded_level' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Level</a></th>
                <th class="text-center">Tree</th>
                <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
                <th  class="text-center">Alerts</th>
                <th  class="text-center" width="90px">Production</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td>
                            <a href="javascript:void(0);" class="text-red">
                            <strong class="fw600"><?php echo $rows['rep_id']; ?></strong></a></br>
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
                        <td class="w-200">
                            <div class="theme-form pr">
                                <select class="form-control has-value agent_level_change" id="agent_level_change_<?=$rows['id']?>" data-old_lvl_id="<?=$rows['agent_coded_id']?>">
                                    <?php if($rows['id'] == md5(1)){ ?>
                                        <option value="root">Root</option>
                                    <?php }else{ ?>
                                        <?php if(!empty($agentCodedRes)){ ?>
                                            <?php foreach($function->get_agent_level_range($rows['id']) as $level){ ?>
                                                <option value="<?=$level['level']?>" data-id="<?=$level['id']?>" <?= ($level['id']==$rows['agent_coded_id']) ? 'selected' : '' ?>  disabled="disabled" ><?=$level['level_heading']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0);" data-href="agent_tree_popup.php?agent_id=<?=$rows['id']?>" class="agent_tree_popup"><img src="images/icons/icon-tree.svg" width="24px" /></a> 
                        </td>
                        <td class="w-200">
                            <?php if (in_array($rows['status'], array('Invited', 'Pending Approval', 'Pending Contract', 'Pending Documentation', 'Agent Abandon'))) { ?>
                                <?php if($rows['status'] != 'Invited'){ ?>
                                    <a href="javascript:void(0)" class="agent_status" id = "agent_status_<?=$rows['id']?>" data-status="<?=$rows["status"] == 'Active'?'Contracted':$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content=""> <?=$rows["status"] == 'Active'?'Contracted': $rows["status"]?></a>
                                <?php } else if(($rows['status'] == 'Invited') && ($rows['invite_time_diff'] >168)){ ?>
                                    <label class="agent_status" data-status="<?=$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content="">Invited</label><br/>
                                    <a href= "reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="resend_popup" data-toggle="tooltip" title="Re-invite"  id = "agent_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content="" style="color: red;"> <?=$rows["invite_time_diff"] > 168 ? 'Expired':'Re-invited'?></a>
                                <?php } else { ?>
                                    <label class="agent_status" id = "agent_status_<?=$rows['id']?>" data-status="<?=$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content="">Invited</label><br/>
                                    <a href= "reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="resend_popup" data-toggle="tooltip" title="Re-invite"  id = "agent_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content=""> <?=$rows["status"] == 'Active'?'Contracted': 'Re-invite'?></a>
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
                                    <select name="member_status" class="form-control member_status has-value" id="member_status_<?=$rows['id'];?>">
                                        <option value="Active" <?php if ($rows['status'] == 'Active') { echo "selected='selected'"; } ?> disabled="disabled">Contracted</option>
                                        <option value="Suspended" <?php if ($rows['status'] == 'Suspended') { echo "selected='selected'"; } ?> disabled="disabled">Suspended</option>
                                        <option value="Terminated" <?php if ($rows['status'] == 'Terminated') { echo "selected='selected'"; } ?> disabled="disabled">Terminated</option>
                                    </select>
                                    <label>Status</label>
                                </div>
                            <?php } else {
                                echo $rows['status'];
                            }?>

                            <?php if ($rows['status'] == 'Agent Abandon') { ?>
                                <a data-id='reinvite_<?=$rows['id']?>' id='agent_status_as_<?=$rows['id']?>' href="reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>" class="resend_popup" data-toggle="tooltip" title="Re-invite" ><i class="fa fa-reply fa-lg" style="padding-left: 7px; <?php if ($rows['invite_time_diff'] > 168) {}?>"></i></a>
                            <?php }else if(in_array($rows['status'], array('Invited')) && empty($rows["stored_password"])){ ?>
                                <a data-id='reinvite_<?=$rows['id']?>'  href="reinvite_agent.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>" class="resend_popup" data-toggle="tooltip" title="Re-invite"><i id='agent_status_as_<?=$rows['id']?>' class="fa fa-reply fa-lg" style=" <?php if ($rows['invite_time_diff'] > 168) {echo 'color:red';}?>" ></i></a>
                            <?php }?>
                        </td>
                        <td class="text-center">
                            <?php
                            if($rows['countExpired'] != 0){
                                echo '<a data-toggle="tooltip" data-trigger="hover" href="javascript:void(0);" title="License Expired"><i class="fa fa-exclamation-circle fa-lg"></i></a>';
                            }else if(strtotime(date('Y-m-d')) > strtotime($rows['e_o_expiration'])){
                                echo '<a data-toggle="tooltip" data-trigger="hover" href="javascript:void(0);" title="E&O Expired"><i class="fa fa-exclamation-circle fa-lg"></i></a>';
                            }
                            else{
                                echo '-';
                            }
                             ?>
                        </td>
                     <td class="icons text-center">
                            <!-- <a href="javascript:void(0);"><img src="<?=$HOST?>/images/icons/production-icon.svg" width="30px"></a> -->
                            <a href="javascript:void(0);" data-href="personal_production_report.php?agent_id=<?=$rows['id']?>" class="personal_production_report"><img src="<?=$HOST?>/images/icons/production-icon.svg" width="30px"></a> 
                    </td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="9" align="center">No record(s) found</td>
                </tr>
            <?php }?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                    <td colspan="9">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php } ?>
    </table>
</div>
<?php } else { ?>
<div class="container m-t-30">
<div class="panel panel-default panel-block panel-title-block">
    <form id="frm_search" action="agent_listing.php" method="GET" class="theme-form">
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
                        <div class="col-md-6">
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
                                    <!-- <input type="text" class="form-control listing_search" name="bus_name" value="<?php echo $bus_name ?>"> -->
                                    <select class="se_multiple_select listing_search" name="bus_name[]"  id="agency_name" multiple="multiple">
                                        <!-- <option>Agency Name1</option>
                                        <option>Agency Name2</option>
                                        <option>Agency Name3</option> -->
                                        <?php if(!empty($tree_agent_res)){ ?>
                                            <?php foreach($tree_agent_res as $value){ 
                                                if(!empty($value['company_name'])){
                                                ?>
                                                <option value="<?=$value['company_name']?>"><?=$value['company_name']?></option>
                                            <?php }} ?>
                                        <?php } ?>
                                    </select>
                                    <label>Agency Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="se_multiple_select listing_search" name="company[]" id="company" multiple="multiple">
                                    <?php 
                                    if(!empty($companyArr)){ ?>
                                        <?php foreach($companyArr as $company){ ?>
                                            <option value="<?=$company['company']?>"><?=$company['company']?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label>Company</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                                      <div class="form-group">

                                    <select class="se_multiple_select listing_search" name="p_agent_name[]" id="p_agent_name" multiple="multiple">
                                        <?php if(!empty($tree_agent_res)){ ?>
                                            <?php foreach($tree_agent_res as $value){ ?>
                                                <option value="<?=$value['rep_id']?>"><?=$value['rep_id'].' - '.$value['fname'].' '.$value['lname']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <label>Principal Agent Name</label>
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
                                        <?php if(!empty($agentCodedRes)){ ?>
                                            <?php foreach($agentCodedRes as $level){ 
                                                if($level['id'] < $agent_coded_id){
                                                ?>
                                                <option value="<?=$level['id']?>"><?=$level['level_heading']?></option>
                                            <?php
                                                }
                                        } ?>
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
                                </select>
                                <label>Alerts</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                                <div class="form-group ">
                                    <select name="combination_product[]" id="agent_select_product" class=" combination_product_select  listing_search se_multiple_select"  multiple="multiple">
                                        <?php if(isset($excludePrdList) && !empty($excludePrdList)){ ?>
                                            <?php foreach ($excludePrdList as $key => $row) { ?>
                                                <optgroup label='<?=$key?>'>
                                                    <?php if(!empty($row)){ ?>
                                                        <?php foreach ($row as $key1 => $row1) { ?>
                                                            <option value="<?= $row1['id'] ?>" <?=!empty($combination_product) && in_array($row1['id'],$combination_product)?'selected="selected"':''?> data-id="combination_product_check_<?= $row1['id'] ?>"><strong><?= $row1['name'] .' ('.$row1['product_code'].')' ?></strong></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </optgroup>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <label>Products</label>
                            </div>
                        </div>
                        <?php echo getAgencySelect('tree_agent_id',$_SESSION['agents']['id'],'Agent'); /*<div class="col-sm-6">
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
                        </div>*/ ?>
                        <!-- <div class="clearfix"></div> -->
                        <div class="col-sm-6">
                            <div class="form-group ">
                                    <select class="se_multiple_select listing_search" name="license_state[]" id="license_state" multiple="multiple">
                                        <?php if(!empty($license_state_res)){ ?>
                                            <?php foreach($license_state_res as $state){ ?>
                                                <option value="<?=$state['name']?>"><?=$state['name']?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
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
                        <button type="button" name="" id="" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
                        <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
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
    <div class="panel-body">
        <div class="clearfix tbl_filter">
            <div class="pull-left">
                <h4 class="m-t-0 m-b-15">Agents Summary</h4>
            </div>
        </div>
        <div class="table-responsive">
            <table class="<?=$table_class?> table-action text-center">
                <thead>
                    <tr>
                        <th>Invited #</th>
                        <th>Pending #</th>
                        <th>Contracted #</th>
                        <th>Suspended</th>
                        <th>Terminated</th>
                        <th width="90px">Production</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?=$agent_summery_arr['Invited']?></td>
                        <td><?=$agent_summery_arr['Pending Contract']+$agent_summery_arr['Pending Documentation']+$agent_summery_arr['Pending Approval']?></td>
                        <td><?=$agent_summery_arr['Active']?></td>
                        <td><?=$agent_summery_arr['Suspended']?></td>
                        <td><?=$agent_summery_arr['Terminated']?></td>
                        <td class="icons">
                            <!-- <a href="javascript:void(0);"><img src="<?=$HOST?>/images/icons/production-icon.svg" width="30px"></a> -->
                            <a href="javascript:void(0);" data-href="personal_production_report.php?agent_id=<?=md5($agent_id)?>" class="personal_production_report"><img src="<?=$HOST?>/images/icons/production-icon.svg" width="30px"></a> 
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="panel panel-default panel-block">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
    </div>
    <div id="ajax_data" class="panel-body"></div>
</div>
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


// Agent level change
/*
$(document).off('change', '.agent_level_change');
$(document).on('change', '.agent_level_change', function(e) {
  e.stopPropagation();
  var id = $(this).attr('id').replace('agent_level_change_', '');
  var level = $(this).val();
  var level_id = $("#agent_level_change_"+id+" option:selected").attr('data-id');
  var old_level_id = $(this).attr('data-old_lvl_id');
  console.log(id+" "+level+" "+level_id+" "+old_level_id);
//   return false;
  if(level_id !== old_level_id){
    swal({
      //title: "Are you sure ",
      text: "Are you sure you want to change the Agent Level?",
      //type: "warning",
      showCancelButton: true,
      //confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes",
      //showCloseButton: true
    }).then(function() {
      window.location = 'ajax_agent_level_change.php?agent_id=' + id + '&level_id=' + level_id+"&level="+level;
    });
  }
});
    */
    $(document).ready(function() {
    dropdown_pagination('ajax_data')
        $(".date_picker").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true
        });

        ajax_submit();

        $("#rep_id, #agent_level, #tree_agent_id, #license_state, #agent_level, #agency_name, #p_agent_name, #company").multipleSelect({
            selectAll: false,
            filter:true
        });

        $("#agent_select_product").multipleSelect({
        });
        // $('.personal_production_report').colorbox({iframe: true, width: '900px', height: '800px'});
    });

    $(document).on('click','.personal_production_report',function(e){
        $href=$(this).attr('data-href');
        $.colorbox({
            iframe: true,
            href:$href,
            width: '900px', 
            height: '800px'
        });
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

   /* $(document).off('change', '.member_status');
    $(document).on("change", ".member_status", function(e) {
        e.stopPropagation();
        var id = $(this).attr('id').replace('member_status_', '');
        var member_status = $(this).val();
        swal({
            //title: "Are you sure ",
            text: "Are you sure you want to change this status ?",
            //type: "warning",
            showCancelButton: true,
            //confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            //showCloseButton: true
        }).then(function() {
            if (member_status == 'Terminated' || member_status == 'Suspended') {
                $.colorbox({
                    iframe: true,
                    href: "<?=$ADMIN_HOST?>/reason_change_agent_status.php?id=" + id + "&status=" + member_status,
                    width: '600px',
                    height: '400px',
                    trapFocus: false,
                    closeButton: false,
                    overlayClose: false,
                    escKey: false,
                    onClosed: function() {
                        $.ajax({
                            url: "reason_change_agent_status.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                customer_id: id,
                                action: 'OldStatus'
                            },
                            success: function(data) {
                                if (data.status == 'success') {
                                    $status = data.member_status;
                                    $('.member_status [value=' + $status + ']').attr('selected', 'true');
                                }
                            }
                        });
                    }
                });
            } else {
                $.ajax({
                    url: 'change_agent_status.php',
                    data: {
                        id: id,
                        status: member_status
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == "success") {
                            setNotifySuccess(res.msg);
                        }else{
                            setNotifyError(res.msg);
                            ajax_submit();
                        }
                    }
                });
            }
        }, function(dismiss) {
            ajax_submit();
        })
    }); */

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
        $("#agent_status_" + $id).css('color', '#2C4C80');
        $("#agent_status_as_" + $id).css('color', '#2C4C80');
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

</script>
<?php } ?>