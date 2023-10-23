<div class="" id="participants_vue">
    <form action="ajax_participants_file_import.php" role="form" method="post" class="uform" name="participantsForm"
          id="participantsForm" enctype="multipart/form-data">
        <input type="hidden" name="save_as" id="save_as" v-model="save_as">
        <div class="panel panel-default panel-block mn">
            <div class="panel-body advance_info_div">
                <div class="phone-control-wrap ">
                <div class="phone-addon w-90 v-align-top">
                    <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
                </div>
                <div class="phone-addon text-left">
                    <div class="info_box_max_width">
                        <p class="fs14">Upload a CSV file to add multiple participants by aligning system requirements to the file headers.</p>
                    </div>
                </div>
                </div>
            </div>
            <div class="panel-body">
                <h4 class="m-b-30">+ Multiple Participants</h4>
                <div class="theme-form">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <select name="participants_type" id="participants_type" class="form-control">
                                <option value="" data-hidden="true"></option>
                                <option value="Member">Member</option>
                            </select>
                            <label>Participants Type<em>*</em></label>
                            <p class="error" id="error_participants_type"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <select name="agent_id" id="agent_id" class="form-control" v-model="agent_id" data-live-search="true">
                            <?php if(!empty($agent_res)){ ?>
                                <option value="<?=$agent_res['id']?>"><?=$agent_res['rep_id'].' - '.$agent_res['name']?></option>
                            <?php } ?>
                            </select>
                            <label>Assign Participants<em>*</em></label>
                            <p class="error" id="error_agent_id"></p>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <select name="tag_from" id="tag_from" class="form-control" v-model="tag_from">
                                <option value="existing">Existing Tag</option>
                                <option value="new">New Tag</option>
                            </select>
                            <label>Participants Tag<em>*</em></label>
                            <p class="error" id="error_tag_from"></p>
                        </div>
                    </div>
                    <div class="col-sm-6" v-show="tag_from === 'existing'">
                        <div class="form-group">
                            <select name="existing_tag" id="existing_tag" class="form-control has-value"
                                    v-model="existing_tag" data-live-search="true">
                                <?php
                                if (!empty($participants_tag_res)) {
                                    foreach ($participants_tag_res as $tagRow) {
                                ?>
                                        <option value="<?= $tagRow['tag'] ?>"><?= $tagRow['tag'] ?></option>
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
                <div class="inner_box uploadCsvDiv" style="display: none;">
                    <p class="m-b-15">Upload CSV file to autofill participant(s).
                        <a href="<?= $HOST ?>/uploads/csv/participants_template.xlsx" class="red-link pn fw500">Download</a>
                    </p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                            <div class="custom_drag_control">
                                <span class="btn btn-action" style="border-radius:0px;">Upload CSV</span>
                                <input type="file" class="gui-file" id="csv_file" name="csv_file" 
                                accept=".csv" v-model="csv_file">
                                <input type="text" class="gui-input form-control" id="choose_file" placeholder="Choose File">
                                <p class="error" id="error_csv_file"></p>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div v-show="csv_file != ''">
                        <div class="row">
                            <div class="col-sm-6">
                                <p>The system reads the first record of the uploaded file, so headers are required or first record in template will not load. Please match the data fields of the system (left grey area) with headers from your file (right dropdown).</p>
                            </div>
                        </div>
                        <div class="row csv_matrix_tab" id="participants_field">
                        </div>
                        <p class="error" id="error_fields"></p>
                    </div>
                    <div class="text-center m-b-30">
                        <button type="button" class="btn btn-info btn_import_csv" :disabled="csv_file === ''">Import </button>
                        <a href="javascript:void(0);" class="btn red-link cancel_btn">Cancel</a>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    var participants = new Vue({
        el: '#participants_vue',
        data: {
            agent_id: '',
            tag_from: '',
            participants_tag: '',
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

         $(document).on('change', "#participants_type", function (e) {
            e.preventDefault();
            if($(this).val() != ''){
                $(".uploadCsvDiv").show();
            }else{
                $(".uploadCsvDiv").hide();
            }
        });

        $(document).on('change', "#csv_file", function () {
            $('select.select_field').html('<option value=""></option>');
            $('select.select_field').val('');
            $('select').selectpicker('refresh');

            if ($(this).val() != '') {
                $("#save_as").val("upload_csv");
                $("#ajax_loader").show();
                $("#participantsForm").submit();
            }
        });

        $(document).on('click', '.btn_import_csv', function (e) {
            swal({
                text: "<br/><p>I acknowledge that I have read and <br/> agree to the <a href='javascript:void(0);' class='btnShowParticipantAgree red-link'>terms and conditions</a> in this agreement.</p>",
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then(function () {
                $("#save_as").val("save");
                $("#ajax_loader").show();
                $("#participantsForm").submit();
            }, function (dismiss) {

            });
        });

        $(document).on('click', '.btnShowParticipantAgree', function () {
            window.open('<?=$HOST?>/participants_agreement.php',"participants_agreement_window", "width=600,height=550");
        });

        $(document).on('click', '.cancel_btn', function () {
            swal({
                title: 'Are you sure?',
                text: "You want to cancel your changes!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then(function () {
                window.location = 'participants_listing.php';
            }, function (dismiss) {

            });
        });
      
        $('#participantsForm').ajaxForm({
            type: "POST",
            dataType: 'JSON',
            success: function (res) {
                $(".error").html('');
                 $("#ajax_loader").hide();

                if (res.html) {
                    $('#participants_field').html(res.html);
                    $('#participants_field').show();
                    $('[data-toggle="tooltip"]').tooltip(); 
                    common_select();
                    fRefresh();
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
                    window.location.href="manage_participants.php";
                }
               
            }
        });
    });
</script>