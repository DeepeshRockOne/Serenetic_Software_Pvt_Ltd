<div class="container m-t-30" id="smarteapp_vue">
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
                                </select>
                                <label>Lead Type<em>*</em></label>
                                <p class="error" id="error_lead_type"></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select name="tag_from" id="tag_from" class="form-control" v-model="tag_from">
                                    <option value="existing">Select Existing</option>
                                    <option value="new">Create New</option>
                                </select>
                                <label>Lead Tag<em>*</em></label>
                                <p class="error" id="error_tag_from"></p>
                            </div>
                        </div>
                        <div class="col-sm-6" v-show="tag_from === 'existing'">
                            <div class="form-group">
                                <select name="existing_tag" id="existing_tag" class="form-control"
                                        v-model="existing_tag">
                                    <?php
                                    if (!empty($lead_tag_res)) {
                                        foreach ($lead_tag_res as $key => $lead_tag_row) {
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
                    <div class="inner_box">
                        <p class="m-b-15">
                            Upload CSV file to autofill lead(s).
                            <a href="<?= $HOST ?>/uploads/csv/lead_template.csv" class="red-link pn fw500">Download
                                Template</a>
                        </p>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="custom_drag_control">
                                        <span class="btn btn-action" style="border-radius:0px;">Upload CSV</span>
                                        <input type="file" class="gui-file" id="csv_file" name="csv_file" accept=".csv"
                                               v-model="csv_file"
                                               :disabled="!(lead_type !== '' && tag_from !== '' && ((tag_from === 'existing' && existing_tag !== '') || (tag_from === 'new' && new_tag !== '')))">
                                        <input type="text" class="gui-input form-control" placeholder="Choose File">
                                        <p class="error" id="error_csv_file"></p>
                                    </div>
                                </div>
                                <p>The system reads the first record of the uploaded file, so headers are required or
                                    first
                                    record in template will not load. Please match the data fields of the system (left
                                    grey
                                    area) with headers from your file (right dropdown).</p>
                            </div>
                        </div>
                        <div class="row csv_matrix_tab">
                            <div class="col-sm-6"  v-show="lead_type === 'Agent/Group'">
                                <div class="form-group height_auto">
                                    <div class="input-group resources_addon">
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
                                <div class="form-group height_auto">
                                    <div class="input-group resources_addon">
                                        <div class="input-group-addon">
                                            <strong>First Name<em>*</em></strong>
                                        </div>
                                        <div class="pr">
                                            <select name="fname_field" id="fname_field" v-model="fname_field"
                                                    class="form-control select_field" data-live-search="true">
                                                <option value=""></option>
                                            </select>
                                            <label>Select Field</label>
                                        </div>
                                    </div>
                                    <p class="error" id="error_fname_field"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group height_auto">
                                    <div class="input-group resources_addon">
                                        <div class="input-group-addon">
                                            <strong>Last Name<em>*</em></strong>
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
                                <div class="form-group height_auto">
                                    <div class="input-group resources_addon">
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
                                <div class="form-group height_auto">
                                    <div class="input-group resources_addon">
                                        <div class="input-group-addon">
                                            <strong>Phone</strong>
                                        </div>
                                        <div class="pr">
                                            <select name="cell_phone_field" id="cell_phone_field"
                                                    v-model="cell_phone_field" class="form-control select_field" data-live-search="true">
                                                <option value=""></option>
                                            </select>
                                            <label>Select Field</label>
                                        </div>
                                    </div>
                                    <p class="error" id="error_cell_phone_field"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group height_auto">
                                    <div class="input-group resources_addon">
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
                        <div class="text-center m-b-30">
                            <button type="button" class="btn btn-info btn_import_csv"
                                    :disabled="csv_file === '' || fname_field === '' || lname_field === ''">Import
                            </button>
                            <a href="javascript:void(0);" class="btn red-link cancel_btn">Cancel</a>
                        </div>
                        <div class="pn hidden">
                            <div class="progress-status">
                                <div class="status-bar clearfix">
                                    <div class="pull-left">
                                        <p class="fw500 mn">Uploading CSV - <span  class="text-success lead_import_eta"></span>
                                        </p>
                                    </div>
                                    <div class="pull-right">
                                        <p class="fw500 mn text-light-gray"><span class="pending_files_count"></span> 
                                            <a class="text-info" href="javascript:void(0);" onclick="get_import_progress()">
                                                <i class="fa fa-refresh m-l-5" aria-hidden="true"></i>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="progress mn">
                                    <div class="progress-bar progress-bar-success lead_import_progress_bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
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
                text: "Cancel Changes: Are you sure!",
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
                        $('#error_' + index).html(error).show();
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

        get_import_progress();
    });
    function get_import_progress() 
    {
        $('#ajax_loader').show();
        $.ajax({
            url: "ajax_get_lead_import_summary.php",
            type: 'GET',
            dataType:'json',
            success: function (res) {
                $('#ajax_loader').hide();
                $(".pending_files_count").html(res.pending_files_count);
                $(".lead_import_eta").html(res.lead_import_eta);
                $(".lead_import_progress_bar").attr('style','width:'+ res.completed_lead_per +'%');
            }
        });        
    }
</script>