<div class="section-padding">
    <div class="container">
        <div class="panel panel-defualt">
            <div class="panel-body">
                <?php $csv_lead = 'active';
                    include_once 'group_lead_tabs.inc.php';
                ?>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="csv_file_tab">
                        <form action="ajax_upload_csv.php" role="form" method="post" class="uform" name="lead_import_form" id="lead_import_form" enctype="multipart/form-data">
                            <input type="hidden" name="save_as" id="save_as" v-model="save_as">
                            <div class="theme-form">
                                <div class="row">
                                    <div class="form-group height_auto m-b-20">
                                        <p class="fw500">How will this enrollee be tagged?</p>
                                        <div class="radio-v">
                                            <label>
                                                <input type="radio" name="tag_from" value="existing"> Select Existing 
                                            </label>
                                        </div>
                                        <div class="radio-v">
                                            <label>
                                                <input type="radio" name="tag_from" value="new" > Create New
                                            </label>
                                        </div>
                                        <p class="error" id="error_tag_from"></p>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4" id="existing_tag_div" style="display: none">
                                        <div class="form-group">
                                            <select name="existing_tag" id="existing_tag" class="form-control has-value" data-live-search="true">
                                                <?php
                                                if (!empty($lead_tag_res)) {
                                                    foreach ($lead_tag_res as $key => $lead_tag_row) { ?>
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
                                    <div class="col-sm-4" id="new_tag_div" style="display: none">
                                        <div class="form-group">
                                            <input type="text" name="new_tag" id="new_tag" class="form-control">
                                            <label>Enter Tag<em>*</em></label>
                                            <p class="error" id="error_new_tag"></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <hr>
                                <div class="inner_box">
                                    <p class="m-b-15">
                                        Upload CSV file to autofill lead(s).
                                        <a href="<?= $HOST ?>/uploads/csv/enrollee_template.csv" class="red-link pn fw500">Download
                                            Template</a>
                                    </p>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="custom_drag_control">
                                                    <span class="btn btn-action" style="border-radius:0px;">Upload CSV</span>
                                                    <input type="file" class="gui-file" id="csv_file" name="csv_file">
                                                    <input type="text" class="gui-input form-control" placeholder="Choose File">
                                                    <p class="error" id="error_csv_file"></p>
                                                </div>
                                            </div>
                                            <p class="display_data" style="display: none;">The system reads the first record of the uploaded file, so headers are required or
                                                first
                                                record in template will not load. Please match the data fields of the system (left
                                                grey
                                                area) with headers from your file (right dropdown).</p>
                                        </div>
                                    </div>
                                    <div class="row csv_matrix_tab display_data" style="display: none">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <strong>Enrollee ID</strong>
                                                    </div>
                                                    <div class="pr">
                                                        <select name="enrollee_id_field" id="enrollee_id_field" class="form-control select_field" data-live-search="true">
                                                            <option value=""></option>
                                                        </select>
                                                        <label class="label-wrap">Select CSV Column</label>
                                                    </div>
                                                </div>
                                                <p class="error" id="error_enrollee_id_field"></p>
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
                                    <div class="text-center m-b-30 display_data" style="display: none">
                                        <button type="button" class="btn btn-info " id="btn_import_csv" disabled="">Import
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on("change","input[name=tag_from]",function(){
         $val=$(this).val();
         $("#existing_tag_div").hide();
         $("#new_tag_div").hide();
         
         if($val=='existing'){
            $("#existing_tag_div").show();
         }else{
            $("#new_tag_div").show();
         }
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
                text: "Cancel Changes: Are you sure?",
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then(function () {
                window.location = 'group_enrollees.php';
            }, function (dismiss) {

            });

        });

        $(document).off('click', '#btn_import_csv');
        $(document).on('click', '#btn_import_csv', function (e) {
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
                $("#ajax_loader").hide();
                if (res.html) {
                    $('.csv_matrix_tab').html(res.html);
                    $('.csv_matrix_tab').show();
                    common_select();
                    fRefresh();
                    $(".display_data").show();
                    $("#btn_import_csv").removeAttr('disabled');
                }
                // else if (res.csv_data) {
                //     $(".display_data").show();
                //     $.each(res.csv_data, function (key, val) {
                //         $('select.select_field').append('<option value="' + val + '">' + val + '</option>');
                //     });
                //     $('select').selectpicker('refresh');
                //     $("#btn_import_csv").removeAttr('disabled');
                // }
                else if (res.status == 'fail') {
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
                    window.location.href = 'group_enrollees.php';
                }
                
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
  