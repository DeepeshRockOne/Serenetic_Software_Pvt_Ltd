<div id="smarteapp_vue" class="container m-t-30">
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
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="New">New</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Working">Working</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status"
                                               data-status="Open">Open</a></li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Unqualified">Unqualified</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Converted">Converted</a>
                                        </li>
                                        <li><a href="javascript:void(0);" class="lead_status" data-status="Abandoned">Abandoned</a>
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
                                    <td>
                                        <a href="javascript:void(0);" onclick="scrollToDiv($('#aae_section'),0);"
                                           class="btn btn-info btn-outline pull-right">Edit</a>
                                    </td>
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
                                                    <p class="mn"><?= note_custom_charecter('agent', 'lead', $note['description'], 400, $note['added_by_name'], $note['added_by_rep_id'],$note['added_by_id'],$note['added_by_detail_page']) ?></p>
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
                        <input type="hidden" name="is_valid_address" id="is_valid_address" value="<?= !empty($address) ? 'Y' : 'N' ?>">
                        <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
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
                                    <div class="clearfix"></div>
                                    <div class="row m-t-15">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="enrollee_id" id="enrollee_id" class="form-control" value="<?= !empty($employee_id) ? $employee_id : '' ?>">
                                                <label>Enrollee ID <em>*</em></label>
                                                <p class="error" id="error_enrollee_id"></p>
                                            </div>
                                        </div>
                                        <!-- <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="annual_earnings" id="annual_earnings" class="form-control" onkeypress="return isNumberOnly(event)" value="<?= !empty($income) ? $income : '' ?>">
                                                <label>Annual Earnings/Income <em>*</em></label>
                                                <p class="error" id="error_annual_earnings"></p>
                                            </div>
                                        </div> -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select class="form-control" id="company_id" name="company_id"> 
                                                  <option value=""></option>
                                                  <option value="0" <?= $group_company_id == 0 ? 'selected' : '' ?>><?= $group_name ?></option>
                                                    <?php if(!empty($resCompany)) { ?>
                                                        <?php foreach ($resCompany as $key => $value) { ?>
                                                            <option value="<?= $value['id'] ?>" data-location="<?= $value['location'] ?>" <?= !empty($group_company_id) && $value['id'] == $group_company_id ? 'selected' : '' ?>><?= $value['name'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label>Company<em>*</em></label>
                                                <p class="error" id="error_company_id"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select class="form-control" name="employee_type" id="employee_type">
                                                    <option value=""></option>
                                                    <option value="Existing" <?= !empty($employee_type) && $employee_type=="Existing" ? 'selected' : '' ?>>Existing</option>
                                                    <option value="New" <?= !empty($employee_type) && $employee_type=="New" ? 'selected' : '' ?>>New</option>
                                                    <option value="Renew" <?= !empty($employee_type) && $employee_type=="Renew" ? 'selected' : '' ?>>Renew</option>
                                                </select>
                                                <label>Enrollee Type <em>*</em></label>
                                                <p class="error" id="error_employee_type"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                     <div class="input-group-addon datePickerIcon"  data-applyon="hire_date"> <i class="material-icons fs16">date_range</i> </div>
                                                     <div class="pr">
                                                        <input type="text" class="form-control dates" name="hire_date" id="hire_date" value="<?= !empty($hire_date) ? $hire_date : '' ?>">
                                                        <label>Relationship Date (MM/DD/YYYY)<em>*</em></label>
                                                     </div>
                                                </div>
                                                <p class="error" id="error_hire_date"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="fname" class="form-control" id="fname" value="<?= !empty($fname) ? $fname : '' ?>">
                                                <label>Enrollee First Name<em>*</em></label>
                                                <p class="error" id="error_fname"></p>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="lname" class="form-control" id="lname" value="<?= !empty($lname) ? $lname : '' ?>">
                                                <label>Enrollee Last Name<em>*</em></label>
                                                <p class="error" id="error_lname"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="address" id="address" class="form-control" placeholder="" value="<?= !empty($address) ? $address : '' ?>">
                                                <label>Address<em>*</em></label>
                                                <p class="error" id="error_address"></p>
                                                <input type="hidden" name="old_address" value="<?= !empty($address) ? $address : '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="address_2" id="address_2" class="form-control" placeholder="" value="<?= !empty($address2) ? $address2 : '' ?>">
                                                <label>Address 2</label>
                                                <p class="error" id="error_address2"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="city" id="city" class="form-control" value="<?= !empty($city) ? $city : '' ?>">
                                                <label>City<em>*</em></label>
                                                <p class="error" id="error_city"></p>
                                                <input type="hidden" name="old_city" value="<?= !empty($city) ? $city : '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select class="form-control" name="state" id="state">
                                                   <option data-hidden="true"></option>
                                                   <?php foreach ($allStateRes as $key => $value) { ?>
                                                      <option value="<?= $value['name'] ?>" <?= !empty($state) && $state==$value['name'] ? 'selected' : '' ?>><?= $value['name'] ?></option>
                                                   <?php } ?>
                                                </select>
                                                <label>State<em>*</em></label>
                                                <p class="error" id="error_state"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="zipcode" id="zipcode" class="form-control" value="<?= !empty($zip) ? $zip : '' ?>">
                                                <label>Zip Code<em>*</em></label>
                                                <p class="error" id="error_zipcode"></p>
                                                <input type="hidden" name="old_zipcode" value="<?= !empty($zip) ? $zip : '' ?>">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select id="gender" name="gender"  class="form-control">
                                                    <option data-hidden="true"></option>
                                                    <option value="Male" <?= !empty($gender) && $gender =='Male' ? 'selected' : '' ?>>Male</option>
                                                    <option value="Female"  <?= !empty($gender) && $gender =='Female' ? 'selected' : '' ?>>Female</option>
                                                  </select>
                                                <label>Gender<em>*</em></label>
                                                <p class="error" id="error_gender"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                     <div class="input-group-addon datePickerIcon"  data-applyon="dob"> <i class="material-icons fs16">date_range</i> </div>
                                                     <div class="pr">
                                                        <input type="text" class="form-control dates" name="dob" id="dob" value="<?= !empty($birth_date) ? $birth_date : '' ?>">
                                                        <label>DOB (MM/DD/YYYY)<em>*</em></label>
                                                     </div>
                                                </div>
                                                <p class="error" id="error_dob"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="hidden" name="entered_ssn" id="entered_ssn" value="<?= !empty($ssn_itin_num) ? $ssn_itin_num : '' ?>">
                                                <input type="text" name="ssn" id="ssn" class="form-control ssn_mask">
                                                <label>SSN <?= !empty($last_four_ssn) ? "( *".$last_four_ssn .")" : '' ?></label>
                                                <p class="error" id="error_ssn"></p>


                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="email" class="form-control no_space" value="<?= !empty($email) ? $email : '' ?>">
                                                <label>Email<em>*</em></label>
                                                <p class="error" id="error_email"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="phone" class="form-control phone_mask" value="<?= !empty($cell_phone) ? $cell_phone : '' ?>">
                                                <label>Phone<em>*</em></label>
                                                <p class="error" id="error_phone"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select class="form-control" id="class_id" name="class_id"> 
                                                  <option value=""></option>
                                                    <?php if(!empty($resClass)) { ?>
                                                        <?php foreach ($resClass as $key => $value) { ?>
                                                            <option value="<?= $value['id'] ?>" <?= !empty($group_classes_id) && $group_classes_id==$value['id'] ? 'selected' : '' ?>><?= $value['class_name'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label>Class<em>*</em></label>
                                                <p class="error" id="error_class_id"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <p><strong>Select Plan Period(s)</strong></p>
                                    <div class="row">
                                       <div class="col-sm-6">
                                            <div class="form-group">
                                                <select class="form-control" name="coverage_id" id="coverage_id">
                                                    <option value=""></option>
                                                    <?php if(!empty($resCoverage)){
                                                        foreach ($resCoverage as $key => $value) { ?>
                                                            <option value="<?= $value['id'] ?>" <?= !empty($group_coverage_id) && $group_coverage_id==$value['id'] ? 'selected' : '' ?>><?= $value['coverage_period_name'] ?></option>  
                                                        <?php } ?>
                                                    <?php } ?>

                                                </select>
                                                <label>Select Plan Period(s)</label>
                                                <p class="error" id="error_coverage_id"></p>
                                            </div>
                                        </div> 
                                    </div>

                                    <p><strong>Allow Application For Plan Period(s)</strong></p>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php if(!empty($resCoverageCheck)){ ?>
                                                <?php foreach ($resCoverageCheck as $keyCoverahe => $valueCoverage) { ?>
                                                    <div class="m-b-15">
                                                        <label class="mn label-input"><input type="checkbox"  name="allowedCoverage[]" value="<?= $valueCoverage['id'] ?>" <?= !empty($assignCoverageArr) && in_array($valueCoverage['id'], $assignCoverageArr) ? 'checked' : ''  ?>> <?= $valueCoverage['coverage_period_name'] .' ('.date('m/d/Y',strtotime($valueCoverage['coverage_period_start'])) .' - '. date('m/d/Y',strtotime($valueCoverage['coverage_period_end'])).')'?></label>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <hr />
                            <p class="agp_md_title">Tax Information</p>
                            <div class="theme-form">
                                <div class="row lead_page">
                                    <div class="col-sm-6">
                                    <?php if($_SESSION['groups']['rep_id'] == 'G56118'){ ?>
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
                                                if($group_billing_method=='individual'){
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
                                    if($enroll_row['status'] == "Completed" || in_array($enroll_row['order_status'],array('Payment Approved','Post Payment','Pending Settlement'))) {
                                        ?>
                                         <div class="text-center">
                                            <?php if($enroll_row['order_status'] != "Payment Approved" && $enroll_row['future_payment'] == "Y") { ?>
                                                <p>This application successfully set as Post Payment.</p>
                                            <?php } else { ?>
                                                <p>This application is enrolled successfully.</p>
                                            <?php } ?>
                                        </div>
                                        <?php
                                    } elseif ($enroll_row['status'] == "Pending") {
                                        if(strtotime(date('Y-m-d H:i:s')) < strtotime($enroll_row['expire_time'])) {
                                            ?>
                                            <a href="member_enrollment.php?quote_id=<?= md5($enroll_row['id']) ?>&customer_id=<?= md5($enroll_row['customer_ids']) ?>"
                                               class="btn btn-action pull-right" target="_blank">Edit
                                                Application</a>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="text-center">
                                                <p>This application has expired as of <?php echo $tz->getDate(date('Y-m-d H:i:s', strtotime($enroll_row['expire_time']))); ?>, start a new  application <a href="member_enrollment.php?lead_id=<?=$lead_id?>" target="_blank" class="red-link">here.</a></p>
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
                                    <p>Start a new application <a href="member_enrollment.php?lead_id=<?=$lead_id?>" target="_blank" class="red-link">here.</a></p>
                                </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <hr>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="activity_history_tab">
                    </div>
                    <?php /*
                    <div role="tabpanel" class="tab-pane" id="activity_history_tab">
                        <div class="clearfix ">
                            <p class="agp_md_title pull-left ">Activity History</p>
                            <div class="pull-right hidden">
                                <a href="javascript:void(0);"><i class="fa fa-download text-action fs16"></i></a>
                                <a href="javascript:void(0);" class="red-link m-l-5 pn">Export</a>
                            </div>
                        </div>
                        <?php include 'activity_feed_lead.inc.php'; ?>
                    </div>*/?>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
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
            state: '<?= checkIsset($allStateRes[$row['state']]['name'])?>',
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
        $(".tax_price_field").priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: '',
            limit: false,
            centsLimit: 2,
        });
        checkEmail();
        if ($(window).width() >= 1199) {
          $(window).scroll(function() {
          if ($(this).scrollTop() > 569) {
             $('.fixed_tab_top').addClass('fixed');
          } else {
             $('.fixed_tab_top').removeClass('fixed');
          }

           var $site_location = '<?= $SITE_ENV ?>';

            var placeSearch, autocomplete;

            $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
            $(".dates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
            $(".ssn_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
       });
       }
        $(".enroll_msg1").click(function () {
            swal({
                text: "Sorry! This application is no longer valid as this individual is an existing member.  To add an additional plan please locate this individual under members in your book of business.",
                type: "warning",
                confirmButtonText: "Ok",
            });
        });
        $(".enroll_msg2").click(function () {
            swal({
                text: "Sorry! This application is no longer valid as this individual is an existing member.",
                type: "warning",
                confirmButtonText: "Ok",
            });
        });
        $('#srh_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $("#srh_btn_note").hide();
            $("#srh_close_btn_note").show();
            $("#search_div").show();
        });
        $('#srh_close_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $('#srh_btn_note').hide();
            $("#srh_close_btn_note").hide();
            $("#search_div").hide();
            $("#srh_btn_note").show();
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

        $(document).off('click', '#save_lead_account');
        $(document).on('click', '#save_lead_account', function (e) {
            $is_address_ajaxed = $("#is_address_ajaxed").val();
            if($is_address_ajaxed == 1){
              updateAddress();
            }else{
              ajaxSaveAccountDetails();
            }
        });

        function ajaxSaveAccountDetails(){
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
                        var is_error = true;
                        $.each(data.errors, function (key, value) {
                            $('#error_' + key).parent("p.error").show();
                            $('#error_' + key).html(value).show();
                            $('.error_' + key).parent("p.error").show();
                            $('.error_' + key).html(value).show();
                            if ($("[name='" + key + "']").length > 0 && is_error) {
                                $('html, body').animate({
                                    scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                                }, 1000);
                                is_error = false;
                            }
                        });
                    }
                });
        }

        function updateAddress(){

          $.ajax({
              url : "ajax_update_lead_account_detail.php",
              type : 'POST',
              data:$("#form_lead_account_detail").serialize(),
              dataType:'json',
              beforeSend :function(e){
                 $("#ajax_loader").show();
                 $(".error").html('');
              },success(res){
                 $("#is_address_ajaxed").val("");
                 $("#ajax_loader").hide();
                 $(".suggested_address_box").uniform();
                 if(res.zip_response_status =="success"){
                    $("#state").val(res.state).addClass('has-value');
                    $("#city").val(res.city).addClass('has-value');
                    // $("#is_address_verified").val('N');
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
                                $("#address").val(res.address).addClass('has-value');
                                $("#address_2").val(res.address2).addClass('has-value');
                                // $("#is_address_verified").val('Y');
                             }
                             ajaxSaveAccountDetails();
                          },
                    });
                 }else if(res.status == 'success'){
                    // $("#is_address_verified").val('N');
                    ajaxSaveAccountDetails();
                 }else{
                    $.each(res.errors,function(index,error){
                       $("#error_"+index).html(error).show();
                   });
                   ajaxSaveAccountDetails();
                 }
                 $("#state").selectpicker('refresh');
              }
          });
        }

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
            var id = '<?=$_GET['id']?>';
            interactionUpdate(id,'notes','lead_details.php','groups');
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
    });
    
    $(document).on("change","#employee_type",function(){
        $val=$(this).val();
    });
    $(document).on("change","#class_id",function(){
        $val=$(this).val();
        $("#ajax_loader").show();
        $.ajax({
            url:'ajax_load_leads_coverage.php',
            data:{class:$val},
            dataType:'JSON',
            type:"POST",
            success:function(res){
                $("#ajax_loader").hide();
                $("#coverage_id").html(res.html);
                $("#coverage_id").selectpicker('refresh');
            }
        });

    });
    
    $(document).on("click",".datePickerIcon",function(){
      $id=$(this).attr('data-applyon');
      $("#"+$id).datepicker('show');
      $("#"+$id).trigger("blur");
    });

    $(document).on('focus','#address,#zipcode,#city',function(){
     $("#is_address_ajaxed").val(1);
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
    
    isNumberOnly = function(evt) {
          evt = (evt) ? evt : window.event;
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 8 && charCode != 46 && charCode != 47 && charCode != 0 && (charCode < 48 || charCode > 57)) {
              return false;
          }
          return true;
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
            var $href = "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type + "&show=" + show
            window.open($href, "myWindow", "width=500,height=580");
        } else {
            window.location.href = url + "?id=" + '<?=$_GET['id']?>' + "&note_id=" + note_id;
        }
    }

    function delete_note(note_id, activity_feed_id) {
        var id = '<?=$_REQUEST['id']?>';
        var url = "";
        url = "lead_details.php";
        swal({
            text: "Delete Record: Are you sure?",
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
                        interactionUpdate(id,'notes','lead_details.php','groups');
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

if ($(window).width() <= 1170) {
if ($('.nav-tabs:not(.nav-noscroll)').length){
   ;(function() {
     'use strict';
      $(activate);
      function activate() {
      $('.nav-tabs:not(.nav-noscroll)')
        .scrollingTabs({
            scrollToTabEdge: true,
            enableSwiping: true  
         })
     }
   }());
}
}

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

