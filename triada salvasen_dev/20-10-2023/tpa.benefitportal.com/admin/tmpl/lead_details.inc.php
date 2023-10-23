<div id="smarteapp_vue" class="">
    <div class="lead_profile">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default profile-info user_lead" v-bind:class="[lead_status]">
                    <div class="panel-header">
                        <div class="media">
                            <div class="media-body">
                                <h4 class="mn">{{ fname }} {{ lname }} -
                                    <small>{{ lead_id }}</small></h4>
                            </div>
                            <div class="media-right">
                                <div class="dropdown">
                                    <button class="btn btn-white text-black text-left dropdown-toggle " type="button"
                                            data-toggle="dropdown" style="width: 130px;">{{ lead_status }} &nbsp; &nbsp;
                                        <span class="fa fa-sort text-red pull-right"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="New" v-show="lead_status !== 'Unqualified'">New</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Working" v-show="lead_status !== 'Unqualified'">Working</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status"
                                               data-status="Open" v-show="lead_status !== 'Unqualified'">Open</a></li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Unqualified">Unqualified</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Converted" v-show="lead_status !== 'Unqualified'">Converted</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Abandoned" v-show="lead_status !== 'Unqualified'">Abandoned</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table width="100%">
                                <tr>
                                    <td>Lead Type:</td>
                                    <td>{{ lead_type }}</td>
                                </tr colspan="2">
                                <tr>
                                    <td>Name:</td>
                                    <td>{{ fname }} {{ lname }}</td>
                                </tr colspan="2">
                                <tr>
                                    <td>Email:</td>
                                    <td colspan="2">{{ email }}</td>
                                </tr>
                                <tr>
                                    <td>Phone:</td>
                                    <td colspan="2" class="cell_phone_label"><?=format_telephone($row['cell_phone']);?></td>
                                </tr>
                                <tr>
                                    <td>State:</td>
                                    <td colspan="2">{{ state }}</td>
                                </tr>
                                <tr  v-show="lead_type === 'Agent/Group'">
                                    <td>Onboarding:</td>
                                    <td colspan="2"><?=$group_agent_status?></td>
                                </tr>
                                <tr  v-show="lead_type === 'Member'">
                                    <td>DOB:</td>
                                    <td colspan="2">{{ dob }}</td>
                                </tr>
                                <tr v-show="lead_type === 'Member'">
                                    <td>AAE:</td>
                                    <td>
                                        <?php
                                        if ($active_aae_id > 0) {
                                            ?>
                                            <a href="send_aae_link.php?quote_id=<?= md5($active_aae_id) ?>"
                                               class="red-link send_aae_link">Resend Link</a>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default lead_intrection_wrap">
                    <div class="ajex_loader" id="intrection_loader" style="display: none;">
                        <div class="loader"></div>
                    </div>
                    <div class="panel-body">
                        <div class="clearfix">
                            <ul class="nav nav-tabs tabs customtab  pull-left nav-noscroll" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#note_tab" aria-controls="note_tab" role="tab" data-toggle="tab">Notes</a>
                                </li>
                            </ul>
                            <div class="text-right note_div">
                                <a href="#" class="search_btn" id='srh_btn_note'><i class="fa fa-search fa-lg text-blue"></i></a>
                                <a href="#" class="search_btn search_close_btn" id="srh_close_btn_note" style="display: none;"><i class="text-light-gray ">X</i></a>
                                <a data-href="account_note.php?id=<?= $_GET['id'] ?>&type=Lead"
                                   class="btn btn-action account_note_popup_new  m-l-5">
                                   <strong>+Note</strong></a>
                                <div class="clearfix"></div>
                                <div class="note_search_wrap " id="search_note"
                                     style="display:none">
                                    <div class="phone-control-wrap">
                                        <div class="phone-addon">
                                            <input type="text" class="form-control" id="note_search_keyword"
                                                   placeholder="Search Keyword(s)">
                                        </div>
                                        <div class="phone-addon w-80">
                                            <button href="javascript:void(0);" class="btn btn-info btn-outline"
                                                    id="search_btn_note">Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active pn" id="note_tab">
                                <div class="activity_wrap">
                                    <?php if (!empty($notes_res) && count($notes_res) > 0) {
                                        foreach ($notes_res as $note) { ?>
                                            <div class="media">
                                                <div class="media-body fs14 br-n">
                                                    <p class="text-light-gray mn"><?= $tz->getDate($note['created_at'], 'D., M. d, Y @ h:i A') ?></p>
                                                    <p class="mn"><?= note_custom_charecter('agent', 'lead', $note['description'], 400, $note['added_by_name'],$note['added_by_rep_id'],$note['added_by_id'],$note['added_by_detail_page']) ?></p>
                                                </div>
                                                <div class="media-right text-nowrap">
                                                    <a href="javascript:void(0);" class="" id="edit_note_id"
                                                       data-original-title="Edit"
                                                       onclick="edit_note(<?= $note['note_id'] ?>,'view')"
                                                       data-value="Lead"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                                    <a href="javascript:void(0);" class="" id="edit_note_id"
                                                       data-original-title="Edit"
                                                       onclick="edit_note(<?= $note['note_id'] ?>,'')"
                                                       data-value="Lead"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                                    <a href="javascript:void(0);" class="" id="delete_note_id"
                                                       data-original-title="Delete"
                                                       onclick="delete_note(<?= $note['note_id'] ?>,<?= $note['ac_id'] ?>)"><i
                                                                class="fa fa-trash fa-lg"></i></a>&nbsp;
                                                </div>
                                            </div>
                                        <?php }
                                    } else echo '<p class="text-center mn"> Add first note by clicking the ‘+ Note’ button above </p>'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default panel-block">
            <div class="panel-body">
                <ul class="nav nav-tabs tabs customtab fixed_tab_top" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#account_tab" data-toggle="tab"
                           onclick="scrollToDiv($('#account_tab'), 0);"
                           aria-expanded="false">Account</a>
                    </li>
                    <li role="presentation" ><a href="#activity_history_tab" data-toggle="tab" onclick="scrollToDiv($('#activity_history_tab'), 0,'tmpl/activity_feed_lead.inc.php','activity_history_tab');" aria-expanded="true">Activity History</a>
                    </li>
                </ul>
                <div class="m-t-20">
                    <div role="tabpanel" class="tab-pane active" id="account_tab">
                        <form action="ajax_update_lead_account_detail.php?id=<?= $_GET['id'] ?>"
                        name="form_lead_account_detail" id="form_lead_account_detail" method="POST">
                            <p class="agp_md_title">Account</p>
                            <div class="theme-form">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="lead_type" id="lead_type"
                                                   class="form-control" v-model="lead_type" readonly="">
                                            <label>Lead Type</label>
                                            <p class="error"><span id="error_lead_type"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" v-show="lead_type === 'Agent/Group'">
                                        <div class="form-group">
                                            <input type="text" name="company_name" id="company_name"
                                                   class="form-control" v-model="company_name">
                                            <label>Company Name</label>
                                            <p class="error"><span id="error_company_name"></span></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="fname" id="fname" v-model="fname"
                                                   class="form-control">
                                            <label>First Name<em>*</em></label>
                                            <p class="error"><span id="error_fname"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="lname" id="lname" v-model="lname"
                                                   class="form-control">
                                            <label>Last Name<em>*</em></label>
                                            <p class="error"><span id="error_lname"></span></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="email" id="email" v-model="email"
                                                   class="form-control no_space">
                                            <label>Email</label>
                                            <p class="error"><span id="error_email"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="cell_phone" id="cell_phone" class="form-control" value="<?=format_telephone($row['cell_phone'])?>">
                                            <label>Phone</label>
                                            <p class="error"><span id="error_cell_phone"></span></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select name="state" id="state" v-model="state" class="form-control">
                                                <?php if (!empty($allStateRes)) { ?>
                                                    <?php foreach ($allStateRes as $state) { ?>
                                                        <option value="<?= $state["name"]; ?>"><?php echo $state['name']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <label>State</label>
                                            <p class="error"><span id="error_state"></span></p>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <?php if(strtolower($row['type']) == 'group') { ?>
                            <p class="agp_md_title">Tax Information</p>
                            <div class="theme-form">
                                <div class="row lead_page">
                                    <div class="col-sm-6">

                                    <?php if($row['sponsor_rep_id'] == 'G56118' && $_SESSION['admin']['display_id'] != 'AD2904'){ ?>
                                    <div class="password_unlock">
                                        <div style="display:none" id="password_popup">
                                            <div class="phone-control-wrap">
                                                <div class="phone-addon"><input type="password" class="form-control" name="password" id="showing_pass"></div>
                                                <div class="phone-addon w-65"><button type="button" class="btn btn-info" id="show_password">Unlock</button></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="phone-control-wrap">
                                                <div class="phone-addon">
                                                    <input type="password" name="income" value="<?=base64_encode($row['income'])?>" id="income" class="form-control dot_password" readonly>
                                                    <label>Annual Salary</label>
                                                </div>
                                                <div class="phone-addon w-25">
                                                    <a href="javascript:void(0);" id="click_to_show"><i class="fa fa-eye fa-lg"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="salary_encrypted" value="Y" name="salary_encrypted">
                                    <?php } else { ?>
                                        <div class="form-group height_auto">
                                            <input type="text" name="income" id="income"
                                                   class="form-control tax_price_field" value="<?=$row['income']?>">
                                            <label>Annual Salary</label>
                                            <p class="error"><span id="error_income"></span></p>
                                        </div>
                                        <input type="hidden" id="salary_encrypted" value="N" name="salary_encrypted">
                                    <?php }?>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="pre_tax_deductions_field" id="pre_tax_deductions_field"
                                                   class="form-control tax_price_field" value="<?=$row['pre_tax_deductions_field']?>">
                                            <label>Pre Tax</label>
                                            <p class="error"><span id="error_pre_tax_deductions_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="post_tax_deductions_field" id="post_tax_deductions_field"
                                                   class="form-control tax_price_field" value="<?=$row['post_tax_deductions_field']?>">
                                            <label>Post Tax</label>
                                            <p class="error"><span id="error_post_tax_deductions_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <select name="w4_filing_status_field" id="w4_filing_status_field" v-model="w4_filing_status_field" class="form-control">
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                            </select>
                                            <label>Marital Status</label>
                                            <p class="error"><span id="error_w4_filing_status_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <select name="w4_no_of_allowances_field" id="w4_no_of_allowances_field" v-model="w4_no_of_allowances_field" class="form-control">
                                                <?php for($i=1;$i<=12;$i++){ ?>
                                                    <option value="<?=$i?>"><?=$i?></option>
                                                <?php } ?>
                                            </select>
                                            <label>Default Allowances</label>
                                            <p class="error"><span id="error_w4_no_of_allowances_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <select name="w4_two_jobs_field" id="w4_two_jobs_field" v-model="w4_two_jobs_field" class="form-control">
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                            <label>Two Jobs?</label>
                                            <p class="error"><span id="error_w4_two_jobs_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="w4_dependents_amount_field" id="w4_dependents_amount_field"
                                                   class="form-control tax_price_field" value="<?=$row['w4_dependents_amount_field']?>" data-value="<?=$row['w4_dependents_amount_field']?>">
                                            <label>Dependents Amount</label>
                                            <p class="error"><span id="error_w4_dependents_amount_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="w4_4a_other_income_field" id="w4_4a_other_income_field"
                                                   class="form-control tax_price_field" value="<?=$row['w4_4a_other_income_field']?>">
                                            <label>Other Income</label>
                                            <p class="error"><span id="error_w4_4a_other_income_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="w4_4b_deductions_field" id="w4_4b_deductions_field"
                                                   class="form-control tax_price_field" value="<?=$row['w4_4b_deductions_field']?>">
                                            <label>4B Deduction</label>
                                            <p class="error"><span id="error_w4_4b_deductions_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="w4_additional_withholding_field" id="w4_additional_withholding_field"
                                                   class="form-control" value="<?=$row['w4_additional_withholding_field']?>" onkeypress="return isNumber(event)">
                                            <label>Additional Withholding</label>
                                            <p class="error"><span id="error_w4_additional_withholding_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <select name="state_filing_status_field" id="state_filing_status_field" v-model="state_filing_status_field" class="form-control">
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                            </select>
                                            <label>State Filling Status</label>
                                            <p class="error"><span id="error_state_filing_status_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="state_dependents_field" id="state_dependents_field"
                                                   class="form-control" value="<?=$row['state_dependents_field']?>" onkeypress="return isNumber(event)">
                                            <label>State Dependents</label>
                                            <p class="error"><span id="error_state_dependents_field"></span></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group height_auto">
                                            <input type="text" name="state_additional_withholdings_field" id="state_additional_withholdings_field"
                                                   class="form-control" value="<?=$row['state_additional_withholdings_field']?>" onkeypress="return isNumber(event)">
                                            <label>State Additional Withholding</label>
                                            <p class="error"><span id="error_state_additional_withholdings_field"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="text-center">
                                <button type="button" class="btn btn-action" id="save_lead_account">Save</button>
                            </div>
                            <hr>
                        </form>
                        <div id="aae_section" v-show="lead_type === 'Member'">
                            <p class="agp_md_title mn">AAE</p>
                            <?php if (!empty($enroll_res)) {
                                foreach ($enroll_res as $enroll_row_key => $enroll_row) {
                                    $sub_total = $enroll_row['sub_total'];
                                    $grand_total = $enroll_row['grand_total'];
                                    $step_fee_price = 0;
                                    $service_fee_price = 0;

                                    ?>
                                    <p class="m-b-20"><?php echo $tz->getDate($enroll_row['created_at']); ?>
                                        - Application</p>
                                    <div class="table-responsive m-b-30">
                                        <table class="<?= $table_class ?>">
                                            <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Plan</th>
                                                <th class="text-center">Plan Period</th>
                                                <th class="text-right">Monthly Premium</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                             if($group_billing_method == 'individual'){
                                                $od_sql = "SELECT od.prd_plan_type_id,od.product_name,od.unit_price as price,p.type,p.product_type,od.start_coverage_period,od.end_coverage_period  
                                                        FROM order_details od
                                                        JOIN prd_main p ON(p.id = od.product_id)
                                                        WHERE od.order_id=:order_id AND od.is_deleted='N'";
                                                $od_res = $pdo->select($od_sql, array(":order_id" => $enroll_row['order_ids']));
                                             }else{
                                                $od_sql = "SELECT od.prd_plan_type_id,od.product_name,od.unit_price as price,p.type,p.product_type,od.start_coverage_period,od.end_coverage_period  
                                                FROM group_order_details od
                                                JOIN prd_main p ON(p.id = od.product_id)
                                                WHERE od.order_id=:order_id AND od.is_deleted='N'";
                                                $od_res = $pdo->select($od_sql, array(":order_id" => $enroll_row['order_ids']));
                                             }

                                                if (!empty($od_res)) {
                                                    $fee_prd_res = array();
                                                    foreach ($od_res as $prd_row) {
                                                        $prd_plan_type = '';
                                                        if(isset($prdPlanTypeArray) && isset($prdPlanTypeArray[$prd_row['prd_plan_type_id']]['title'])) {
                                                            $prd_plan_type = $prdPlanTypeArray[$prd_row['prd_plan_type_id']]['title'];
                                                        }
                                                        if ($prd_row["type"] == 'Fees') {
                                                            if ($prd_row["product_type"] == "Healthy Step") {
                                                                $step_fee_price = $prd_row["price"];
                                                                continue;
                                                            }
                                                            if ($prd_row["product_type"] == "ServiceFee") {
                                                                $service_fee_price = $prd_row["price"];
                                                                continue;
                                                            }
                                                            $fee_prd_res[] = $prd_row;
                                                            continue;
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td><?= $prd_row['product_name']; ?></td>
                                                            <td><?= $prd_plan_type; ?></td>
                                                            <td class="text-center">
                                                                <?php
                                                                if ($prd_row["type"] != 'Fees' && strtotime($prd_row["start_coverage_period"]) > 0 && strtotime($prd_row["end_coverage_period"]) > 0) {
                                                                    echo date("m/d/Y", strtotime($prd_row["start_coverage_period"])) . " - " . date("m/d/Y", strtotime($prd_row["end_coverage_period"]));
                                                                } else {
                                                                    echo "-";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td class="text-right"><?= displayAmount($prd_row['price'], 2, '$') ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    foreach ($fee_prd_res as $key => $prd_row) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $prd_row['product_name']; ?></td>
                                                            <td>Fees</td>
                                                            <td class="text-center">-</td>
                                                            <td class="text-right"><?= displayAmount($prd_row['price'], 2, '$') ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <tr>
                                                        <td colspan=3><b>No Product Selected</b></td>
                                                    </tr>
                                                    <?php
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="pull-right">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>SubTotal</td>
                                                <td class="text-right"><?= displayAmount($sub_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Healthy Step</td>
                                                <td class="text-right"><?= displayAmount($step_fee_price) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Service Fee</td>
                                                <td class="text-right"><?= displayAmount($service_fee_price) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="">Total</td>
                                                <td class="text-right">
                                                    <strong><?= displayAmount($grand_total) ?></strong>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <hr/>
                                    </div>
                                    <div class="clearfix"></div>
                                    <?php
                                    if($enroll_row['status'] == "Completed" || in_array($enroll_row['order_status'],array('Payment Approved','Post Payment','Payment Declined','Pending Settlement','Cancelled'))) {
                                        ?>
                                        <div class="text-center">
                                            <?php if($enroll_row['order_status'] == "Post Payment") { ?>
                                                <p>This application successfully set as Post Payment - <?=displayDate($enroll_row['post_date'])?>.</p>

                                            <?php }else if($enroll_row['order_status']== "Payment Declined") { ?>
                                                <p>This application's <?=$enroll_row['future_payment'] == "Y"?"post payment":"payment"?> is declined. <br/>
                                                    <?php if($has_non_approved_order == true){ ?>
                                                            <a href="<?=$HOST?>/attempt_order.php?location=admin&order_id=<?=md5($ord_row['id'])?>&lead_id=<?=$lead_id?>" class="fw500 red-link  btn_attempt_order">Click to attempt again</a>
                                                    <?php } ?>
                                                </p>

                                            <?php }else if($enroll_row['order_status']== "Cancelled") { ?>
                                                <p>This application is cancelled</p>

                                            <?php } else { ?>
                                                <p>This application is enrolled successfully.</p>
                                            <?php } ?>
                                        </div>
                                        <?php
                                    } elseif ($enroll_row['status'] == "Pending") {
                                        if(strtotime(date('Y-m-d H:i:s')) < strtotime($enroll_row['expire_time'])) {
                                            ?>
                                            
                                            <?php
                                        } else {
                                            ?>
                                            <div class="text-center">
                                                <p>This application has expired as of <?php echo $tz->getDate(date('Y-m-d H:i:s', strtotime($enroll_row['expire_time']))); ?></p>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <div class="clearfix"></div>
                            <?php
                                }
                            } else { ?>
                                <div class="col-sm-12 text-center">
                                    <p>No application found.</p>
                                </div>
                            <?php } ?>
                            <div class="clearfix"></div>

                            <?php if($has_non_approved_order == true){ ?>
                            <div class="col-sm-12">
                                <p>
                                    <?=$row['fname'].' '.$row['lname']?>  <a href="<?=$HOST?>/attempt_order.php?location=admin&order_id=<?=md5($ord_row['id'])?>&lead_id=<?=$lead_id?>" class="btn btn-action btn_attempt_order m-l-20">Edit</a><br/>
                                    <?=!empty($cb_row)?($cb_row['payment_mode'] == "ACH"?"ACH *".$cb_row['last_cc_ach_no']:$cb_row['card_type']." *".$cb_row['last_cc_ach_no']):""?>
                                    <br/>
                                    <?php if($ord_row['status'] == "Post Payment") { ?>
                                        Post Payment Date: <?=displayDate($ord_row['post_date'])?> 
                                    <?php } ?>
                                    </p>
                            </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <hr>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="activity_history_tab">
                    
                    </div>
                    <?php /*<div role="tabpanel" class="tab-pane" id="activity_history_tab">
                        <div class="clearfix ">
                            <p class="agp_md_title pull-left ">Activity History</p>
                            <div class="pull-right hidden">
                                <a href="javascript:void(0);"><i class="fa fa-download text-action fs16"></i></a>
                                <a href="javascript:void(0);" class="red-link m-l-5 pn">Export</a>
                            </div>
                        </div>
                        <?php include 'activity_feed_lead.inc.php'; ?>
                    </div>*/ ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var smarteapp = new Vue({
        el: '#smarteapp_vue',
        data: {
            lead_id: '<?=$row['lead_id']?>',
            lead_type: '<?=$row['lead_type']?>',
            lead_status: '<?=$row['status']?>',
            company_name: '<?=$row['company_name']?>',
            fname: '<?=$row['fname']?>',
            lname: '<?=$row['lname']?>',
            email: '<?=$row['email']?>',
            state: '<?=$row['state']?>',
            dob:'<?= !empty($row['birth_date']) ? displayDate($row['birth_date']).' ('.calculateAge($row['birth_date']).')' : '-' ?>',
            w4_filing_status_field: '<?=$row['w4_filing_status_field']?>',
            w4_no_of_allowances_field: '<?=$row['w4_no_of_allowances_field']?>',
            w4_two_jobs_field: '<?=$row['w4_two_jobs_field']?>',
            state_filing_status_field: '<?=$row['state_filing_status_field']?>',
        },
        methods: {},
        computed: {}
    });
</script>
<script type="text/javascript">
    var not_win = '';
    $(document).ready(function () {
        checkEmail();
        if ($(window).width() >= 1171) {
          $(window).scroll(function() {
          if ($(this).scrollTop() > 569) {
             $('.fixed_tab_top').addClass('fixed');
          } else {
             $('.fixed_tab_top').removeClass('fixed');
          }
       });
       }
        $(".enroll_msg1").click(function () {
            swal({
                text: "Sorry! This application is no longer valid as this individual is an existing member.  To add an additional Plan please locate this individual under members in your book of business.",
                confirmButtonText: "Confirm",
            });
        });
        $(".enroll_msg2").click(function () {
            swal({
                text: "Sorry! This application is no longer valid as this individual is an existing member.",
                confirmButtonText: "Confirm",
            });
        });
        $('#srh_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $("#srh_btn_note").hide();
            $("#srh_close_btn_note").show();
            $("#search_note").slideDown();
            $('.activity_wrap').addClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
        });
        $('#srh_close_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $('#srh_btn_note').hide();
            $("#srh_close_btn_note").hide();
            $("#search_div").hide();
            $("#srh_btn_note").show();
            $('.activity_wrap').removeClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
            var id = '<?=$_GET['id']?>';
            interactionUpdate(id,'notes','lead_details.php');
        });
        $(".activity_wrap").mCustomScrollbar({
            theme: "dark"
        });
        $("#cell_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        $(document).on('input', "#cell_phone", function () {
            $('.cell_phone_label').html($(this).val());
        });

        $(document).off('click', '.send_aae_link');
        $(document).on('click', '.send_aae_link', function (e) {
            e.preventDefault();
            $.colorbox({
                href: $(this).attr('href'),
                iframe: true,
                width: '768px',
                height: '600px'
            })
        });

        $('.btn_attempt_order').colorbox({iframe: true,width:"90%;",height: '600px'});

        $(document).off('click', '#save_lead_account');
        $(document).on('click', '#save_lead_account', function (e) {
            formHandler($("#form_lead_account_detail"),
                function () {
                    $("#ajax_loader").show();
                },
                function (data) {
                    $("#ajax_loader").hide();
                    $("p.error").hide();
                    if (data.status == 'success') {
                        setNotifySuccess("Lead detail updated successfully!");
                    } else if (data.status == "fail") {
                        setNotifyError("Oops... Something went wrong please try again later");
                    } else {
                        $(".error").hide();
                        var tmp_flag = true;
                        $.each(data.errors, function (key, value) {
                            $('#error_' + key).parent("p.error").show();
                            $('#error_' + key).html(value).show();
                            $('.error_' + key).parent("p.error").show();
                            $('.error_' + key).html(value).show();
                            if (tmp_flag == true && $("[name='" + key + "']").length > 0) {
                                tmp_flag = false;
                                $('html, body').animate({
                                    scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                                }, 1000);
                            }
                        });
                    }
                });
        });

        //lead status change
        $(document).off('click', '.lead_status');
        $(document).on("click", ".lead_status", function (e) {
            var id = '<?=$_GET['id']?>';
            var lead_status = $(this).attr('data-status');
            smarteapp.lead_status = lead_status;
            $.ajax({
                url: 'change_lead_status.php',
                data: {
                    id: id,
                    status: lead_status
                },
                method: 'POST',
                dataType: 'json',
                success: function (res) {
                    if (res.status == "success") {
                        setNotifySuccess(res.msg);
                    } else {
                        setNotifyError(res.msg);
                    }
                }
            });
        });

        /*--- notes ---*/
        not_win = '';
        $(document).on('click', ".account_note_popup_new", function () {
            $timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            $href = $(this).attr('data-href');
            window.open($href, "myWindow", "width=500,height=580");
        });

        $('#srh_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $(this).hide();
            $("#srh_close_btn_note").show();
            $("#search_note").slideDown();
            $('.activity_wrap').addClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
        });
        $('#srh_close_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $("#search_note").slideUp();
            $("#srh_close_btn_note").hide();
            $("#srh_btn_note").show();
            $('.activity_wrap').removeClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
        });
        $("#note_search_keyword").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $(".activity_wrap div.media").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        $(document).off("click", ".note_class");
        $(document).on("click", ".note_class", function (e) {
            $(".interaction_div").hide();
            $(".note_div").show();
        });

        $(document).off('click', '#search_btn_note');
        $(document).on('click', '#search_btn_note', function () {
            $("#ajax_loader").show();
            var note_search_keyword = $("#note_search_keyword").val();
            var id = '<?=$_GET['id']?>';
            if (note_search_keyword !== '') {
                $.ajax({
                    url: 'lead_details.php?id=' + id,
                    data: {note_search_keyword: note_search_keyword, id: id},
                    method: 'post',
                    dataType: 'html',
                    success: function (res) {
                        $("#ajax_loader").hide();
                        $("#note_tab").html(res);
                        $(".activity_wrap").mCustomScrollbar({
                            theme: "dark"
                        });
                    }
                });
            } else {
                alert("Please Enter Search Keyword(s)");
                $("#ajax_loader").hide();
            }
        });

        $(document).off('click','#click_to_show');
        $(document).on('click','#click_to_show',function(){
            if($("#income").attr('type') === 'password')
                $("#password_popup").show();
            else{
                if($("#income").val() == ''){
                    $("#password_popup").hide();
                    $("#income").attr('type','password');
                    $("#income").addClass('dot_password');
                    $("#income").removeClass('tax_price_field');
                    $("#income").attr('readonly',true);
                }else{
                    $("#password_popup").hide();
                    $("#income").attr('type','password');
                    salaryEncryptDecrypt('encrypt');
                }
            }
        });

        $(document).off('click','#show_password');
        $(document).on('click','#show_password',function(){
            $("#password_popup").hide();
            if($("#showing_pass").val() !== '') {
                $("#password_popup").hide();
                salaryEncryptDecrypt('decrypt');
            }
        });
    });

    function salaryEncryptDecrypt(type){
        $("#salary_encrypted").val("Y");
        var income = $("#income").val();
        if(type == 'decrypt'){
            $("#salary_encrypted").val("N");
        }
        var id = '<?=$_GET['id']?>';
        if(type == 'decrypt' && income == ''){
            $("#income").priceFormat({
                prefix: '',
                suffix: '',
                centsSeparator: '.',
                thousandsSeparator: '',
                limit: false,
                centsLimit: 2,
            });
            $("#income").removeClass('dot_password');
            $("#income").attr('readonly',false);
            $("#income").attr('type','text');
            return false;
        }
        $("#ajax_loader").show();
        $.ajax({
            url:'lead_details.php',
            method : 'POST',
            data : {id:id,change_salary:"change_salary",type:type,income:income,showing_pass:$("#showing_pass").val()},
            dataType:'json',
            success:function(res){
                $("#ajax_loader").hide();
                $("#showing_pass").val('');
                if(res.error != ''){
                    setNotifyError(res.error);
                    return false;
                }else{
                    if(type == 'decrypt'){
                        $("#income").val(res.dec_income);
                        $("#income").removeClass('dot_password');
                        $("#income").priceFormat({
                            prefix: '',
                            suffix: '',
                            centsSeparator: '.',
                            thousandsSeparator: '',
                            limit: false,
                            centsLimit: 2,
                        });
                        $("#income").attr('readonly',false);
                        $("#income").attr('type','text');
                    }else if(type == 'encrypt' ){
                        $("#income").val(res.enc_income);
                        $("#income").addClass('dot_password');
                        $('#income').unpriceFormat();
                        $("#income").attr('readonly',true);
                    }
                }
            }
        });
    }

    function edit_note(note_id, t) {
        var user_type = $("#edit_note_id").attr("data-value");
        var show = "";
        if (t === 'view') {
            show = "show";
        }
        var customer_id = '<?=$_GET['id']?>';
        url = "lead_details.php";
        if (user_type == 'View' || user_type == 'Lead') {
            $.colorbox({
                iframe: true,
                width: '800px',
                height: '400px',
                href: "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type +"&show="+show
            });
        } else {
            window.location.href = url + "?id=" + '<?=$_GET['id']?>' + "&note_id=" + note_id;
        }
    }

    function delete_note(note_id, activity_feed_id) {
        var id = '<?=$_REQUEST['id']?>';
        var url = "";
        url = "lead_details.php";
        swal({
            text: "Delete Note: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
        }).then(function () {
            $.ajax({
                url: 'ajax_general_note_delete.php',
                data: {
                    note_id: note_id,
                    activity_feed_id: activity_feed_id,
                    usertype: 'Lead',
                    user_id: id,
                },
                dataType: 'json',
                type: 'post',
                success: function (res) {
                    if (res.status == "success") {
                        // window.location = url + '?id=' + id;
                        interactionUpdate(id,'notes','lead_details.php');
                        setNotifySuccess('Note deleted successfully.');
                    }
                }
            });
        }, function (dismiss) {

        });
    }

    /*scroll div function start */
    function scrollToDiv(element, navheight,url,ajax_div) {
      var str = $("#"+ajax_div).html().trim();
      if(str === '' && url!==''){
         ajax_get_lead_data(url,ajax_div,'');
      }
      if ($(element).length) {
         var offset = element.offset();
         var offsetTop = offset.top;
         var totalScroll = offsetTop - navheight;
         if ($(window).width() >= 1171) {
            var totalScroll = offsetTop - $("nav.navbar-default").outerHeight() - 42
         } else {
            var totalScroll = offsetTop - $("nav.navbar-default ").outerHeight() - 42
         }
         $('body,html').animate({
            scrollTop: totalScroll
         }, 1200);
      }
   }

   ajax_get_lead_data = function(url,ajax_div,newid){
        console.log(ajax_div);
      var id = '<?=$_GET['id']?>';
      if(newid !== '' && newid !== undefined){
         id = newid;
      }
    $.ajax({
      url : url,
      type : 'POST',
      data:{
        id:id
      },
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success : function(res){
        $("#ajax_loader").hide();
        $("#"+ajax_div).html(res);
        common_select();
        $('.change_default').uniform();
      }
    });
  }

    $(function() {
     $('.lead_intrection_wrap').matchHeight({
         target: $('.profile-info')
     });
});

$(".tax_price_field").priceFormat({
    prefix: '',
    suffix: '',
    centsSeparator: '.',
    thousandsSeparator: '',
    limit: false,
    centsLimit: 2,
});
function isNumber(event) {
    var charCode = (event.which) ? event.which : event.keyCode;
    // Allow only backspace, delete, left arrow and right arrow keys
    if (charCode == 8 || charCode == 37 || charCode == 39) {
        return true;
    }
    // Allow only digits from 0 to 9
    if (charCode >= 48 && charCode <= 57) {
        return true;
    }
    // Prevent any other input
    return false;
}
</script>

