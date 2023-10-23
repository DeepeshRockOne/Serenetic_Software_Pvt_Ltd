<div class="" id="smarteapp_vue">
    <form action="ajax_upload_csv.php" role="form" method="post" class="uform" name="lead_import_form"
          id="lead_import_form"
          enctype="multipart/form-data">
        <input type="hidden" name="save_as" id="save_as" v-model="save_as">
        <div class="panel panel-default panel-block mn">
            <div class="panel-body advance_info_div">
                <div class="phone-control-wrap ">
                    <div class="phone-addon w-90 v-align-top">
                        <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
                    </div>
                    <div class="phone-addon text-left">
                        <div class="info_box_max_width">
                            <p class="fs14">Upload a CSV file to add multiple leads by aligning system requirements to
                                the
                                file headers. <br/> First name, last name, and either email or phone are required
                                fields.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <h4>+ Multiple Leads</h4>
                <div class="theme-form">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group height_auto">
                                <select name="lead_type" id="lead_type" class="form-control" v-model="lead_type">
                                    <option value="Agent/Group">Agent/Group</option>
                                    <option value="Member">Member</option>
                                    <option value="Group Enrollee">Group Enrollee</option>
                                </select>
                                <label>Lead Type<em>*</em></label>
                                <p class="error" id="error_lead_type"></p>
                            </div>
                        </div>
                        <div class="col-sm-6" v-show="lead_type != 'Group Enrollee'">
                            <div class="form-group height_auto">
                                <select name="agent_id" id="agent_id" class="form-control" v-model="agent_id" data-live-search="true">
                                    <?php 
                                      if(!empty($agent_res)) {
                                        foreach ($agent_res as $key => $agent_row) {
                                          ?>
                                          <option value="<?=$agent_row['id']?>"><?=$agent_row['rep_id'].' - '.$agent_row['name']?></option>
                                          <?php    
                                        }
                                      }
                                    ?>
                                </select>
                                <label>Assign Leads<em>*</em></label>
                                <p class="error" id="error_agent_id"></p>
                            </div>
                        </div>
                        <div class="col-sm-6" v-show="lead_type == 'Group Enrollee'">
                            <div class="form-group height_auto">
                                <select name="group_id" id="group_id" class="form-control" v-model="group_id" data-live-search="true">
                                    <?php 
                                       if(!empty($group_res)) {
                                        foreach ($group_res as $key => $group_row) {
                                          ?>
                                          <option value="<?=$group_row['id']?>"><?=$group_row['rep_id'].' - '.$group_row['name']?></option>
                                          <?php    
                                        }
                                      }
                                    ?>
                                </select>
                                <label>Assign Leads<em>*</em></label>
                                <p class="error" id="error_group_id"></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select name="tag_from" id="tag_from" class="form-control" v-model="tag_from">
                                    <option value="existing">Existing Tag</option>
                                    <option value="new">New Tag</option>
                                </select>
                                <label>Lead Tag<em>*</em></label>
                                <p class="error" id="error_tag_from"></p>
                            </div>
                        </div>
                        <div class="col-sm-6" v-show="tag_from === 'existing'">
                            <div class="form-group">
                                <select name="existing_tag" id="existing_tag" class="form-control has-value"
                                        v-model="existing_tag" data-live-search="true">
                                    <?php
                                    if (!empty($lead_tag_res)) {
                                        foreach ($lead_tag_res as $key => $lead_tag_row) {
                                            if(!(in_array('1',explode(',',$lead_tag_row['sponsor_ids'])) || in_array('all',explode(',',$lead_tag_row['sponsor_ids'])))) {
                                                continue;
                                            }
                                            ?>
                                            <option value="<?= $lead_tag_row['tag'] ?>"><?= $lead_tag_row['tag'] ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label>Select Tag<em>*</em></label>
                                <p class="error" id="error_existing_tag"></p>
                            </div>
                        </div>
                        <div class="col-sm-6" v-show="tag_from === 'new'">
                            <div class="form-group">
                                <input type="text" name="new_tag" id="new_tag" class="form-control" v-model="new_tag">
                                <label>Enter Tag<em>*</em></label>
                                <p class="error" id="error_new_tag"></p>
                            </div>
                        </div>
                    </div>
                    <div class="inner_box" v-show="lead_type != ''">
                        <p class="m-b-15">
                            Upload CSV file to autofill lead(s).
                            <a href="<?= $HOST ?>/uploads/csv/lead_template.csv" class="red-link pn fw500" v-show="lead_type != 'Group Enrollee'">Download</a>
                            <a href="<?= $HOST ?>/uploads/csv/enrollee_template.csv" class="red-link pn fw500" v-show="lead_type == 'Group Enrollee'">Download</a>
                        </p>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="custom_drag_control">
                                        <span class="btn btn-action" style="border-radius:0px;">Upload CSV</span>
                                        <input type="file" class="gui-file" id="csv_file" name="csv_file" accept=".csv"
                                               v-model="csv_file"
                                               :disabled="!(lead_type !== '' && (agent_id !== '' || group_id !== '') && tag_from !== '' && ((tag_from === 'existing' && existing_tag !== '' && existing_tag !== 'undefined') || (tag_from === 'new' && new_tag !== '')))">
                                        <input type="text" class="gui-input form-control" placeholder="Choose File">
                                        <p class="error" id="error_csv_file"></p>
                                    </div>
                                </div>
                              
                            </div>
                        </div>

                        <div v-show="csv_file != ''">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p>The system reads the first record of the uploaded file, so headers are required or
                                    first record in template will not load. Please match the data fields of the system (left grey area) with headers from your file (right dropdown).</p>
                                </div>
                            </div>

                            <div class="row csv_matrix_tab" id="agent_member_fields" v-show="lead_type != 'Group Enrollee'">
                                <div class="col-sm-6"  v-show="lead_type === 'Agent/Group'">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <strong>Company Name</strong>
                                            </div>
                                            <div class="pr">
                                                <select name="company_name_field" id="company_name_field" v-model="company_name_field"
                                                        class="form-control select_field" data-live-search="true">
                                                    <option value=""></option>
                                                </select>
                                                <label>Select Field</label>
                                            </div>
                                        </div>
                                        <p class="error" id="error_company_name_field"></p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <strong>First Name</strong>
                                            </div>
                                            <div class="pr">
                                                <select name="fname_field"
                                                        class="form-control select_field" id="fname_field" data-live-search="true">
                                                    <option value=""></option>
                                                </select>
                                                <label>Select Field</label>
                                            </div>
                                        </div>
                                        <p class="error" id="error_fname_field"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <strong>Last Name</strong>
                                            </div>
                                            <div class="pr">
                                                <select name="lname_field" id="lname_field" v-model="lname_field"
                                                        class="form-control select_field" data-live-search="true">
                                                    <option value=""></option>
                                                </select>
                                                <label>Select Field</label>
                                            </div>
                                        </div>
                                        <p class="error" id="error_lname_field"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <strong>Email</strong>
                                            </div>
                                            <div class="pr">
                                                <select name="email_field" id="email_field" v-model="email_field"
                                                        class="form-control select_field" data-live-search="true">
                                                    <option value=""></option>
                                                </select>
                                                <label>Select Field</label>
                                            </div>
                                        </div>
                                        <p class="error" id="error_email_field"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <strong>Phone</strong>
                                            </div>
                                            <div class="pr">
                                                <select name="cell_phone_field"
                                                        v-model="cell_phone_field" class="form-control select_field" data-live-search="true" id="cell_phone_field">
                                                    <option value=""></option>
                                                </select>
                                                <label>Select Field</label>
                                            </div>
                                        </div>
                                        <p class="error" id="error_cell_phone_field"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <strong>State</strong>
                                            </div>
                                            <div class="pr">
                                                <select name="state_field" id="state_field" v-model="state_field"
                                                        class="form-control select_field" data-live-search="true">
                                                    <option value=""></option>
                                                </select>
                                                <label>Select Field</label>
                                            </div>
                                        </div>
                                        <p class="error" id="error_state_field"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row csv_matrix_tab" id="group_enrollee_fields" v-show="lead_type == 'Group Enrollee'">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Enrollee ID</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="enrollee_id_field" id="enrollee_id_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_enrollee_id_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Annual Earnings/Income</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="annual_earnings_field" id="annual_earnings_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_annual_earnings_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Company Name</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="company_name_field" id="group_company_name_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_company_name_field"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Enrollee Type</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="employee_type_field" id="employee_type_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_employee_type_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Relationship Date</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="hire_date_field" id="hire_date_field" 
                                                                    class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_hire_date_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Termination Date</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="termination_date_field" id="termination_date_field" 
                                                                    class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_termination_date_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Enrollee First Name</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="fname_field" id="group_fname_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_fname_field"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Enrollee Last Name</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="lname_field" id="group_lname_field"  class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_lname_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Address</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="address_field" id="address_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_address_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Address 2 (suite, apt)</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="address2_field" id="address2_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_address2_field"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>City</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="city_field" id="city_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_city_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>State</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="state_field" id="group_state_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_state_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Zip Code</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="zipcode_field" id="zipcode_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_zipcode_field"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Gender</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="gender_field" id="gender_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_gender_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>DOB</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="dob_field" id="dob_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_dob_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>SSN</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="ssn_field" id="ssn_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_ssn_field"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Email</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="email_field" id="group_email_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_email_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Phone</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="cell_phone_field" class="form-control select_field" data-live-search="true" id="group_cell_phone_field">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_cell_phone_field"></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Class</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="class_name_field" id="class_name_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_class_name_field"></p>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <strong>Plan Period</strong>
                                                        </div>
                                                        <div class="pr">
                                                            <select name="coverage_period_field" id="coverage_period_field" class="form-control select_field" data-live-search="true">
                                                                <option value=""></option>
                                                            </select>
                                                            <label>Select Field</label>
                                                        </div>
                                                    </div>
                                                    <p class="error" id="error_coverage_period_field"></p>
                                                </div>
                                            </div>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label class="mn">
                                <input type="checkbox" id="lead_report" name="lead_report">
                                Check box if you wish to receive an error report on any leads that were not added and
                                the reason for it.
                            </label>
                        </div> -->
                        <div class="text-center m-b-30">
                            <button type="button" class="btn btn-info btn_import_csv"
                                    :disabled="csv_file === ''">Import
                            </button>
                            <a href="javascript:void(0);" class="btn red-link cancel_btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    var smarteapp = new Vue({
        el: '#smarteapp_vue',
        data: {
            lead_type: '',
            agent_id: '',
            group_id: '',
            tag_from: '',
            lead_tag: '',
            existing_tag: '',
            new_tag: '',
            csv_file: '',
            save_as: '',
            company_name_field: '',
            fname_field: '',
            lname_field: '',
            email_field: '',
            cell_phone_field: '',
            state_field: '',
        },
        methods: {},
        computed: {}
    });

    $(document).ready(function () {
        smarteapp.agent_id = "1";
        $("#agent_id").val('1').change();

        $(document).on('change',"#lead_type", function () {
            var lead_type = $(this).val();
            if(lead_type == "Group Enrollee"){
                $("#company_name_field").attr("disabled","disabled");
                $("#fname_field").attr("disabled","disabled");
                $("#lname_field").attr("disabled","disabled");
                $("#email_field").attr("disabled","disabled");
                $("#cell_phone_field").attr("disabled","disabled");
                $("#state_field").attr("disabled","disabled");

                $("#group_company_name_field").removeAttr("disabled","disabled");
                $("#group_fname_field").removeAttr("disabled","disabled");
                $("#group_lname_field").removeAttr("disabled","disabled");
                $("#group_email_field").removeAttr("disabled","disabled");
                $("#group_cell_phone_field").removeAttr("disabled","disabled");
                $("#group_state_field").removeAttr("disabled","disabled");
            }else{
                $("#group_company_name_field").attr("disabled","disabled");
                $("#group_fname_field").attr("disabled","disabled");
                $("#group_lname_field").attr("disabled","disabled");
                $("#group_email_field").attr("disabled","disabled");
                $("#group_cell_phone_field").attr("disabled","disabled");
                $("#group_state_field").attr("disabled","disabled");

                $("#company_name_field").removeAttr("disabled","disabled");
                $("#fname_field").removeAttr("disabled","disabled");
                $("#lname_field").removeAttr("disabled","disabled");
                $("#email_field").removeAttr("disabled","disabled");
                $("#cell_phone_field").removeAttr("disabled","disabled");
                $("#state_field").removeAttr("disabled","disabled");
            }
            $('select').selectpicker('refresh');
            fRefresh();
        });



        $(document).on('change',"#agent_id", function () {
            var agent_id = $("#agent_id").val();
            update_existing_tag(agent_id);
        });

        $(document).on('change',"#group_id", function () {
            var agent_id = $("#group_id").val();
            update_existing_tag(group_id);
        });

        $(document).on('change', "#csv_file", function () {
            $('select.select_field').html('<option value=""></option>');
            $('select.select_field').val('');
            $('select').selectpicker('refresh');

            if ($(this).val() != '') {
                $("#save_as").val("upload_csv");
                $("#ajax_loader").show();
                $("#lead_import_form").submit();
            }
        });

        $(document).on('click', '.btn_show_lead_agreement', function () {
            window.open('<?=$HOST?>/lead_agreement.php',"lead_agreement_window", "width=600,height=550");
        });

        $(document).on('click', '.cancel_btn', function () {
            swal({
                text: "Cancel Changes: Are you sure?",
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then(function () {
                window.location = 'lead_listing.php';
            }, function (dismiss) {

            });

        });

        $(document).on('click', '.btn_import_csv', function (e) {
            swal({
                text: "<br/><p>I acknowledge that I have read and <br/> agree to the <a href='javascript:void(0);' class='btn_show_lead_agreement red-link'>terms and conditions</a> in this agreement.</p>",
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then(function () {
                $("#save_as").val("save");
                $("#ajax_loader").show();
                $("#lead_import_form").submit();
            }, function (dismiss) {

            });
        });

        $('#lead_import_form').ajaxForm({
            type: "POST",
            dataType: 'JSON',
            success: function (res) {
                $(".error").html('');

                if (res.csv_data) {
                    $.each(res.csv_data, function (key, val) {
                        $('select.select_field').append('<option value="' + val + '">' + val + '</option>');
                    });
                    $('select').selectpicker('refresh');
                } else if (res.status == 'fail') {
                    var is_error = true;
                    $.each(res.errors, function (index, error) {
                        var lead_type = $("#lead_type").val();
                        if(lead_type == "Group Enrollee"){
                            $('#group_enrollee_fields #error_' + index).html(error).show();
                        }else{
                            $('#agent_member_fields #error_' + index).html(error).show();
                        }

                        if (is_error) {
                            var offset = $('#error_' + index).offset();
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 50;
                            $('body,html').animate({scrollTop: totalScroll}, 1200);
                            is_error = false;
                        }
                    });
                } else if (res.status == 'success') {
                    window.location.href = 'lead_listing.php';
                }
                $("#ajax_loader").hide();
            }
        });
    });
    
    function update_existing_tag(agent_id)
    {
        var lead_tag_res = <?=isset($lead_tag_res)?json_encode($lead_tag_res):'[]'?>;
        var existing_tag = $("select#existing_tag").val();
        $("select#existing_tag").html('');
        var existing_tag_html = '';

        if(lead_tag_res.length > 0) {
            $.each(lead_tag_res,function(index,value){
                var sponsor_ids = value.sponsor_ids;
                sponsor_ids = sponsor_ids.split(',');
                if(agent_id != "") {
                    if(jQuery.inArray("all",sponsor_ids) !== -1 || jQuery.inArray(String(agent_id),sponsor_ids) !== -1) {
                        existing_tag_html += '<option value="'+value.tag+'">'+value.tag+'</option>';
                    }
                } else {
                    if(jQuery.inArray("all",sponsor_ids) !== -1) {
                        existing_tag_html += '<option value="'+value.tag+'">'+value.tag+'</option>';
                    }
                }
            });
        }
        $("select#existing_tag").html(existing_tag_html);
        if(existing_tag != '' && $("select#existing_tag option[value='"+existing_tag+"']").length > 0) {
            $("select#existing_tag").val(existing_tag).change();
        } else {
            $("select#existing_tag").val('').change();
        }
        $("select#existing_tag").selectpicker('refresh'); 
    }
</script>