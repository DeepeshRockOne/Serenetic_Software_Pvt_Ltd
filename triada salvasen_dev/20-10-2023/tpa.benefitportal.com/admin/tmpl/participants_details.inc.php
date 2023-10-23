<div class="participants_profile">
    <div class="row">
        <div class="col-md-4">
        <div class="panel panel-default profile-info user_lead">
            <div class="panel-header">
            <div class="media">
                <div class="media-body">
                    <h4 class="mn"><?=$row['fname'].' '.$row['lname']?> -
                        <small><?=$row['participants_id']?></small></h4>
                </div>
                <div class="media-right">
                <div class="dropdown">
                    <button class="btn btn-white text-black text-left dropdown-toggle" type="button"
                            data-toggle="dropdown" style="width: 130px;"><?=$row['status']?> &nbsp; &nbsp;
                        <span class="fa fa-sort text-red pull-right"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php if($row['status'] != "Unqualified"){ ?>
                        <li><a href="javascript:void(0);" class="participantsStatus" data-status="New">New</a>
                        </li>
                        <li><a href="javascript:void(0);" class="participantsStatus" data-status="Working">Working</a>
                        </li>
                        <li><a href="javascript:void(0);" class="participantsStatus" data-status="Open">Open</a></li>
                        <li><a href="javascript:void(0);" class="participantsStatus" data-status="Unqualified">Unqualified</a>
                        </li>
                        <li><a href="javascript:void(0);" class="participantsStatus" data-status="Converted">Converted</a>
                        </li>
                        <li><a href="javascript:void(0);" class="participantsStatus" data-status="Abandoned">Abandoned</a>
                        </li>
                        <?php }else{ ?>
                        <li><a href="javascript:void(0);" class="participantsStatus" data-status="Unqualified">Unqualified</a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                </div>
            </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                <table width="100%">
                    <tr>
                        <td>Participants Type:</td>
                        <td><?=$row["participants_type"]?></td>
                    </tr colspan="2">
                    <tr>
                        <td>Name:</td>
                        <td><?=$row["fname"]?> <?=$row["lname"]?></td>
                    </tr colspan="2">
                    <tr>
                        <td>Email:</td>
                        <td colspan="2"><?=$row["email"]?></td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td colspan="2" class="home_phone_label"><?=format_telephone($row['cell_phone']);?></td>
                    </tr>
                    <tr>
                        <td>State:</td>
                        <td colspan="2"><?=$row["state"]?></td>
                    </tr>
                    <tr>
                        <td>DOB:</td>
                        <td colspan="2"><?=displayDate($row["birth_date"])?> (<?=calculateAge($row['birth_date'])?>)</td>
                    </tr>
                </table>
                </div>
            </div>
        </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default pt_intrection_wrap">
            <div class="ajex_loader" style="display: none;">
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
                        <a data-href="account_note.php?id=<?= $_GET['id'] ?>&type=Participants"
                           class="btn btn-action account_note_popup_new m-l-5">
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
                                            <p class="mn"><?= note_custom_charecter('agent', 'particpants', $note['description'], 400, $note['added_by_name'],$note['added_by_rep_id'],$note['added_by_id'],$note['added_by_detail_page']) ?></p>
                                        </div>
                                        <div class="media-right text-nowrap">
                                            <a href="javascript:void(0);" class="" id="edit_note_id"
                                               data-original-title="Edit"
                                               onclick="edit_note(<?= $note['note_id'] ?>,'view')"
                                               data-value="Participants"><i class="fa fa-eye fa-lg"></i></a> &nbsp;
                                            <a href="javascript:void(0);" class="" id="edit_note_id"
                                               data-original-title="Edit"
                                               onclick="edit_note(<?= $note['note_id'] ?>,'')"
                                               data-value="Participants"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
                                            <a href="javascript:void(0);" class="" id=" "
                                               data-original-title="Delete"
                                               onclick="delete_note(<?= $note['note_id'] ?>,<?= $note['ac_id'] ?>)"><i
                                                        class="fa fa-trash fa-lg"></i></a>&nbsp;
                                        </div>
                                    </div>
                                <?php }
                            } else{ echo '<p class="text-center mn"> Add first note by clicking the ‘+ Note’ button above </p>'; }?>
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
            <li role="presentation">
                <a href="#activity_history_tab" data-toggle="tab"
                   onclick="scrollToDiv($('#activity_history_tab'),0,'tmpl/activity_feed_participants.inc.php','activity_history_tab');"
                   aria-expanded="true">Activity History</a>
            </li>
        </ul>
        <div class="m-t-20">
            <div role="tabpanel" class="tab-pane active" id="account_tab">
            <p class="agp_md_title">Account</p>
            <form name="participantsFrm" id="participantsFrm" method="POST">
                <input type="hidden" name="participants_id" value="<?=checkIsset($row["ptId"])?>">
                <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
                <p class="error error_participants_id"></p>
                <div class="theme-form">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group height_auto">
                            <input type="text" name="participants_type" id="participants_type" 
                            class="form-control" readonly="readonly" value="<?=checkIsset($row["participants_type"])?>">
                            <label>Participants Type<em>*</em></label>
                            <p class="error error_participants_type"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="reseller_number" id="reseller_number" class="form-control" value="<?=checkIsset($row["reseller_number"])?>">
                            <label>Reseller<em>*</em></label>
                            <p class="error error_reseller_number"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="client_code" id="client_code" class="form-control" value="<?=checkIsset($row["client_code"])?>">
                            <label>GroupNumber</label>
                            <p class="error error_client_code"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                            <div class="password_unlock">
                            <div class="phone-control-wrap" style="display:none" id="password_popup">
                              <div class="phone-addon"><input type="password" class="form-control" name="" id="showing_pass"></div>
                              <div class="phone-addon w-65"><button class="btn btn-info" id="show_password">Unlock</button></div>
                            </div>
                          </div>
                        <div class="phone-control-wrap">
                           <div class="phone-addon">
                              <div class="form-group">
                                 <input type="text" id="display_ssn" readonly='readonly' class="form-control" value="<?= secure_string_display_format($pssn, 4); ?>">
                                 <label>SSN</label>
                                 <input type="text" class="form-control" id="ssn" name="ssn" value="" style="display:none" />
                                 <input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value='N'/>
                                 <p class="error error_ssn"></p>
                              </div>
                           </div>
                           <div class="phone-addon w-30">
                              <div class="m-b-25">
                                 <a href="javascript:void(0)" id="edit_ssn" class="text-action icons" style="display:block">
                                 <i class="fa fa-edit fa-lg"></i></a>
                                 <a href="javascript:void(0)" id="cancel_ssn" class="text-action icons" style="display:none">
                                 <i class="fa fa-remove fa-lg"></i></a>
                              </div>
                           </div>

                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="fname" id="fname" class="form-control" value="<?=checkIsset($row["fname"])?>">
                            <label>First Name<em>*</em></label>
                            <p class="error error_fname"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="lname" id="lname" value="<?=checkIsset($row["lname"])?>"
                                   class="form-control">
                            <label>Last Name<em>*</em></label>
                            <p class="error error_lname"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="mname" id="mname" value="<?=checkIsset($row["mname"])?>"
                                   class="form-control" maxlength="1">
                            <label>MI</label>
                            <p class="error error_mname"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="birth_date" id="birth_date" value="<?=!empty($row['birth_date']) ? getCustomDate($row['birth_date']) : ''?>" class="form-control date_picker">
                           <label>DOB<em>*</em></label>
                           <p class="error error_birth_date"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <select class="form-control" name="gender" id="gender">
                              <option data-hidden="true"></option>
                              <option value="Male" <?=$row['gender'] == 'Male' ? 'selected="selected"' : '' ?>>Male</option>
                              <option value="Female" <?=$row['gender'] == 'Female' ? 'selected="selected"' : '' ?> >Female</option>
                           </select>
                           <label>Gender<em>*</em></label>
                           <p class="error error_gender"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="employee_number" id="employee_number" value="<?=checkIsset($row["employee_number"])?>" class="form-control">
                            <label>Employee Number</label>
                            <p class="error error_employee_number"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="employee_id" id="employee_id" value="<?=checkIsset($row["employee_id"])?>" class="form-control" readonly="readonly">
                            <label>PrimaryID<em>*</em></label>
                            <p class="error error_employee_id"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="person_code" value="<?=$row['person_code']?>" onkeypress="return isNumberKey(event)" id="person_code" class="form-control">
                           <label>Person Code<em>*</em></label>
                           <p class="error error_person_code"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="address" id="address" value="<?=$row['address']?>" class="form-control">
                           <label>Address<em>*</em></label>
                           <p class="error error_address"></p>
                           <input type="hidden" name="old_address" value="<?=$row['address']?>">
                        </div>
                    </div>
                     <div class="col-sm-4">
                         <div class="form-group">
                           <input type="text" class="form-control" name="address2" id="address2" value="<?=$row['address2']?>" onkeypress="return block_special_char(event)" />
                           <label>Address 2 (suite, apt)</label>
                           <p class="error error_address2"></p>
                         </div>
                     </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="city" value="<?=$row['city']?>" id="city" class="form-control">
                           <label>City<em>*</em></label>
                           <p class="error error_city"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <select class="form-control" name="state" id="state">
                              <option data-hidden="true"></option>
                              <?php if (!empty($allStateRes)) {?>
                                 <?php foreach ($allStateRes as $state) {?>
                                 <option value="<?=$state["name"];?>" <?= $state['name'] == $row['state'] ? 'selected="selected"' : ''?>><?php echo $state['name']; ?></option>
                                 <?php }?>
                              <?php }?>
                           </select>
                           <label>State<em>*</em></label>
                           <p class="error error_state"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="zip" value="<?=$row['zip']?>" id="zip" class="form-control">
                           <label>Zip<em>*</em></label>
                           <p class="error error_zip"></p>
                           <input type="hidden" name="old_zip" value="<?=$row['zip']?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="home_phone" id="home_phone" value="<?=format_telephone($row['home_phone'])?>" class="form-control">
                           <label>Home Phone</label>
                           <p class="error error_home_phone"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="work_phone" id="work_phone" value="<?=format_telephone($row['work_phone'])?>" class="form-control">
                           <label>Work Phone</label>
                           <p class="error error_work_phone"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="cell_phone" id="cell_phone" value="<?=format_telephone($row['cell_phone'])?>" class="form-control">
                           <label>Phone</label>
                           <p class="error error_cell_phone"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="email" id="email" class="form-control no_space" value="<?=$row['email']?>">
                            <label>Email</label>
                            <p class="error error_email"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="hire_date" id="hire_date" value="<?=!empty($row['hire_date']) ? getCustomDate($row['hire_date']) : ''?>" class="form-control date_picker">
                           <label>Hire Date</label>
                           <p class="error error_hire_date"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="employee_term_date" id="employee_term_date" value="<?=!empty($row['employee_term_date']) ? getCustomDate($row['employee_term_date']) : ''?>" class="form-control date_picker">
                           <label>Employment Term Date</label>
                           <p class="error error_employee_term_date"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="employee_term_reason_code" id="employee_term_reason_code"
                                   class="form-control" value="<?=$row['employee_term_reason_code']?>">
                            <label>Employment Term Reason Code</label>
                            <p class="error error_employee_term_reason_code"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="employment_status" id="employment_status" class="form-control" value="<?=$row['employment_status']?>">
                            <label>Employment Status</label>
                            <p class="error error_employment_status"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" name="marital_status" id="marital_status" class="form-control" value="<?=$row['marital_status']?>">
                            <label>Marital Status</label>
                            <p class="error error_marital_status"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="marriage_date" id="marriage_date" value="<?=!empty($row['marriage_date']) ? getCustomDate($row['marriage_date']) : ''?>" class="form-control date_picker">
                           <label>Marriage Date</label>
                           <p class="error error_marriage_date"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group height_auto">
                            IsDisabled : 
                            <label class="radio-inline"><input type="radio" name="is_disabled" value="Y" <?=$row['is_disabled'] == "Y" ? "checked='checked'" : ""?>>Yes</label>
                            <label class="radio-inline"><input type="radio" name="is_disabled" value="N" <?=$row['is_disabled'] == "N" ? "checked='checked'" : ""?>>No</label>
                           <p class="error error_is_disabled"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="disability_effective_date" id="disability_effective_date" value="<?=!empty($row['disability_effective_date']) ? getCustomDate($row['disability_effective_date']) : ''?>" class="form-control date_picker">
                           <label>Disability Status Effective Date</label>
                           <p class="error error_disability_effective_date"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group height_auto">
                            IsDeceased : 
                            <label class="radio-inline"><input type="radio" name="is_deceased" value="Y" <?=$row['is_deceased'] == "Y" ? "checked='checked'" : ""?>>Yes</label>
                            <label class="radio-inline"><input type="radio" name="is_deceased" value="N" <?=$row['is_deceased'] == "N" ? "checked='checked'" : ""?>>No</label>
                           <p class="error error_is_deceased"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="death_date" id="death_date" value="<?=!empty($row['death_date']) ? getCustomDate($row['death_date']) : ''?>" 
                           class="form-control date_picker">
                           <label>Death Date</label>
                           <p class="error error_death_date"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group height_auto">
                            Requires COB : 
                            <label class="radio-inline"><input type="radio" name="requires_cob" value="Y" <?=$row['requires_cob'] == "Y" ? "checked='checked'" : ""?>>Yes</label>
                            <label class="radio-inline"><input type="radio" name="requires_cob" value="N" <?=$row['requires_cob'] == "N" ? "checked='checked'" : ""?>>No</label>
                           <p class="error error_requires_cob"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="pay_frequency" value="<?=$row['pay_frequency']?>" id="pay_frequency" class="form-control">
                           <label>Pay Frequency</label>
                           <p class="error error_pay_frequency"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="annual_salary" value="<?=$row['annual_salary']?>" 
                           onkeypress="return isNumberKey(event)" id="annual_salary" class="form-control">
                           <label>Annual Salary</label>
                           <p class="error error_annual_salary"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="annual_compensation" value="<?=$row['annual_compensation']?>" 
                           onkeypress="return isNumberKey(event)" id="annual_compensation" class="form-control">
                           <label>Additional Annual Compensation</label>
                           <p class="error error_annual_compensation"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="salary_effective_date" id="salary_effective_date" value="<?=!empty($row['salary_effective_date']) ? getCustomDate($row['salary_effective_date']) : ''?>" class="form-control date_picker">
                           <label>Salary Effective Date</label>
                           <p class="error error_salary_effective_date"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="hours_per_week" value="<?=$row['hours_per_week']?>" 
                           onkeypress="return isNumberKey(event)" id="hours_per_week" class="form-control">
                           <label>HoursPerWeek</label>
                           <p class="error error_hours_per_week"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="occupation" value="<?=$row['occupation']?>" id="occupation" class="form-control">
                           <label>Occupation</label>
                           <p class="error error_occupation"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="payroll_class" value="<?=$row['payroll_class']?>" id="payroll_class" class="form-control">
                           <label>Payroll Class</label>
                           <p class="error error_payroll_class"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="ftpt_status" value="<?=$row['ftpt_status']?>" id="ftpt_status" class="form-control">
                           <label>FTPT Status</label>
                           <p class="error error_ftpt_status"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="department" value="<?=$row['department']?>" id="department" class="form-control">
                           <label>Department</label>
                           <p class="error error_department"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="location" value="<?=$row['location']?>" id="location" class="form-control">
                           <label>Location</label>
                           <p class="error error_location"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="area" value="<?=$row['area']?>" id="area" class="form-control">
                           <label>Area</label>
                           <p class="error error_area"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="reporting_field1" value="<?=$row['reporting_field1']?>" id="reporting_field1" class="form-control">
                           <label>Reporting field 1</label>
                           <p class="error error_reporting_field1"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="reporting_field2" value="<?=$row['reporting_field2']?>" id="reporting_field2" class="form-control">
                           <label>Reporting field 2</label>
                           <p class="error error_reporting_field2"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="reporting_field3" value="<?=$row['reporting_field3']?>" id="reporting_field3" class="form-control">
                           <label>Reporting field 3</label>
                           <p class="error error_reporting_field3"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="reporting_field4" value="<?=$row['reporting_field4']?>" id="reporting_field4" class="form-control">
                           <label>Reporting field 4</label>
                           <p class="error error_reporting_field4"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           <input type="text" name="wellness_credit" value="<?=$row['wellness_credit']?>" id="wellness_credit" class="form-control">
                           <label>Wellness Credit</label>
                           <p class="error error_wellness_credit"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group height_auto">
                            Tobacco User : 
                            <label class="radio-inline"><input type="radio" name="tobacco_user" value="Y" <?=$row['tobacco_user'] == "Y" ? "checked='checked'" : ""?>>Yes</label>
                            <label class="radio-inline"><input type="radio" name="tobacco_user" value="N" <?=$row['tobacco_user'] == "N" ? "checked='checked'" : ""?>>No</label>
                           <p class="error error_tobacco_user"></p>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-action" id="saveParticipants">Save</button>
                </div>
                <hr>
                </div>
            </form>
            <!-- particpants product code start -->
            <div>
                <p class="agp_md_title mb10">Products</p>
                <div class="table-responsive m-b-30">
                    <table class="<?= $table_class ?>">
                        <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Coverage Tier</th>
                            <th>Plan Coverage Description</th>
                            <th>Orig Effective Date</th>
                            <th>Event Type</th>
                            <th>Event Description</th>
                            <th>Event Date</th>
                            <th>Effective Date</th>
                            <th>Termination Date</th>
                            <th>Relationship</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($prdRes)) {
                            foreach ($prdRes as $prdRow) {
                        ?>
                           <tr>
                            <td><?=$prdRow["product_code"]?></td>
                            <td><?=$prdRow["plan_identifier"]?></td>
                            <td><?=$prdRow["plan_coverage_tier"]?></td>
                            <td><?=$prdRow["plan_coverage_desc"]?></td>
                            <td><?=displayDate($prdRow["org_effective_date"])?></td>
                            <td><?=!empty($prdRow["event_type"]) ? $prdRow["event_type"] : '-'?></td>
                            <td><?=!empty($prdRow["event_description"]) ? $prdRow["event_description"] : '-'?></td>
                            <td><?=displayDate($prdRow["event_date"])?></td>
                            <td><?=displayDate($prdRow["effective_date"])?></td>
                            <td><?=displayDate($prdRow["termination_date"])?></td>
                            <td><?=!empty($prdRow["relationship"]) ? $prdRow["relationship"] : '-'?></td>
                           </tr>
                        <?php
                            }
                        } else { ?>
                            <tr>
                                <td colspan="11" class="text-center">No products found.</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
                <hr>
            </div>
            <!-- particpants product code ends -->
            </div>

            <div role="tabpanel" class="tab-pane" id="activity_history_tab">
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

        $(document).on('click','#edit_ssn',function(){
            $(this).hide();
            $("#password_popup").show();
            $('#cancel_ssn').show();
        });

        $(document).on('click','#show_password',function(e){
            e.preventDefault();
            $("#password_popup").hide();
            if($("#showing_pass").val() === '5401'){
                $('#display_ssn').hide();
                $("#ssn").val('<?=$pssn?>').show();
                $('#is_ssn_edit').val('Y');
            }else{
                $('#cancel_ssn').hide();
                $('#edit_ssn').show();
                $("#ssn").val('').hide();
                $("#showing_pass").val('');
                $('#is_ssn_edit').val('N');
            }
        });

        $('#cancel_ssn').click(function () {
          $(this).hide();
          $("#password_popup").hide();
          $('#display_ssn').show();
          $('#ssn').hide();
          $('#is_ssn_edit').val('N');
          $('#edit_ssn').show();
          $('.error_ssn').html('');
           $("#showing_pass").val('');
        });

        $(document).on('focus','#address,#zip',function(){
            $('#is_address_ajaxed').val(1);
        });

        $("#cell_phone,#home_phone,#work_phone").inputmask("(999) 999-9999");
        $("#ssn").inputmask("999-99-9999");
        $("#birth_date,#hire_date,#employee_term_date,#marriage_date,#disability_effective_date,#salary_effective_date").inputmask("99/99/9999");

        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
        });
        
        $(".activity_wrap").mCustomScrollbar({
            theme: "dark"
        });

        $(document).off('click', '#saveParticipants');
        $(document).on('click', '#saveParticipants', function (e) {
            e.preventDefault();
            $is_address_ajaxed = $('#is_address_ajaxed').val();
            if($is_address_ajaxed == 1){
                updateAddress();
            }else{
                ajaxSaveAccountDetails();
            }
        });

        $(document).off('click', '.participantsStatus');
        $(document).on("click", ".participantsStatus", function (e) {
            e.stopPropagation();
            var ptId = '<?=$_GET['id']?>';
            var participants_status = $(this).attr('data-status');
            swal({
                text: "Change Status: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm"
            }).then(function () {
                $.ajax({
                    url: 'ajax_participants_operations.php',
                    data: {
                        ptId: ptId,
                        status: participants_status,
                        action:"changeStatus"
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == "success") {
                            setNotifySuccess(res.message);
                            window.location.reload();
                        } else {
                            setNotifyError(res.message);
                        }
                    }
                });
            }, function (dismiss) {
            })
        });

    /* notes code start */   
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
            $("#search_note").slideUp();
            $("#srh_close_btn_note").hide();
            $("#srh_btn_note").show();
            $('.activity_wrap').removeClass('interaction_filter_active');
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
            interactionUpdate(id,'notes','participants_details.php');
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
                    url: 'participants_details.php?id=' + id,
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
    /* notes code ends */
    });

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)){
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
        url = "participants_details.php";
        if (user_type == 'View' || user_type == 'Participants') {
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
        url = "participants_details.php";
        swal({
            text: "Delete Note: Are you sure?",
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function () {
            $.ajax({
                url: 'ajax_general_note_delete.php',
                data: {
                    note_id: note_id,
                    activity_feed_id: activity_feed_id,
                    usertype: 'Participants',
                    user_id: id,
                },
                dataType: 'json',
                type: 'post',
                success: function (res) {
                    if (res.status == "success") {
                        interactionUpdate(id,'notes','participants_details.php');
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
            ajax_get_participant_data(url,ajax_div,'');
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
    ajax_get_participant_data = function(url,ajax_div,newid){
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
     $('.pt_intrection_wrap').matchHeight({
         target: $('.profile-info')
     });
    });

    function ajaxSaveAccountDetails(){
        $.ajax({
            url : "ajax_update_participants_detail.php",
            type : 'POST',
            data: $("#participantsFrm").serialize(),
            dataType:'json',
            beforeSend :function(e){
                $("#ajax_loader").show();
            },
            success : function(res){
                $("#ajax_loader").hide();
                $(".error").html("");
                if(res.status =='success'){
                    setNotifySuccess("Participants Detail updated successfully.");
                    if($('#is_ssn_edit').val() == 'Y'){
                        window.location.reload();
                    }
                } else if (res.status == 'errors') {
                    var is_error = true;
                    $.each(res.errors, function (index, error) {
                        $('.error_' + index).html(error).show();
                        if (is_error) {
                            var offset = $('.error_' + index).offset();
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 50;
                            $('body,html').animate({scrollTop: totalScroll}, 1200);
                            is_error = false;
                        }
                    });
                }
            }
        });   
    }

    function updateAddress(){
        $.ajax({
          url : "ajax_update_participants_detail.php",
          type : 'POST',
          data:$("#participantsFrm").serialize(),
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
                ajaxSaveAccountDetails();
             }else if(res.address_response_status =="success"){
                $(".suggestedAddressEnteredName").html($("#fname").val()+" "+$("#lname").val());
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                $(".suggestedAddressEntered").html(res.enteredAddress);
                $(".suggestedAddressAPI").html(res.suggestedAddress);
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
                            $("#address2").val(res.address2).addClass('has-value');
                         }
                        ajaxSaveAccountDetails();
                      },
                });
             }else if(res.status == 'success'){
                ajaxSaveAccountDetails();
             }else{
                var is_error = true;
                $.each(res.errors, function (index, error) {
                    $('.error_' + index).html(error).show();
                    if (is_error) {
                        var offset = $('.error_' + index).offset();
                        var offsetTop = offset.top;
                        var totalScroll = offsetTop - 50;
                        $('body,html').animate({scrollTop: totalScroll}, 1200);
                        is_error = false;
                    }
                });
             }
             $('#state').selectpicker('refresh');
            }
        });
    }
</script>

