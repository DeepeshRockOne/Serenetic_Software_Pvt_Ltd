<div class="section-padding">
    <div class="container">
        <div class="panel panel-defualt">
            <div class="panel-body">
                <?php $csv_lead = 'active';
                    include_once 'group_member_import_tabs.inc.php';
                ?>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="csv_file_tab">
                        <form action="ajax_upload_data_file.php"  autocomplete="off" role="form" method="post" class="uform" name="frm_mass_updates" id="frm_mass_updates" enctype="multipart/form-data">
                            <input type="hidden" name="save_as" id="save_as" v-model="save_as">
                            <div class="theme-form">
                                <div class="inner_box">
                                    <p class="m-b-15">
                                        Upload CSV file to autofill Member(s).
                                        <a href="<?= $HOST ?>/uploads/csv/MEMBER_IMPORT_TEMPLATE.xlsx" class="red-link pn fw500">Download
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
                                    
                                    <div id="fields_wrapper" style="display: none;">
                  
                                    </div>

                                    <div class="text-center m-b-30 display_data">
                                        <button type="button" class="btn btn-info " id="btn_import">Import
                                        </button>
                                        <a href="javascript:void(0);" class="btn red-link cancel_btn">Cancel</a>
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

    $(document).ready(function () {
        $(document).on('change', "#csv_file", function () {
            if ($(this).val() != '') {
                $("#save_as").val("upload_csv");
                $("#ajax_loader").show();
                $("#frm_mass_updates").submit();
            }
        });
        $(document).on('click',"#csv_file_tab",function(e){
            $("#btn_import").prop("disabled", false);
        });
        $(document).on('click', "#btn_import", function (e) {
            e.stopImmediatePropagation();
            $("#save_as").val("add_request");
            $("#ajax_loader").show();
            $("#frm_mass_updates").submit();
            $(this).prop("disabled", true);
            return false;
        });

        $('#frm_mass_updates').ajaxForm({
            type: "POST",
            dataType: 'JSON',
            success: function (res) {
                $(".error").html('');

                if (res.html) {
                    $('#fields_wrapper').html(res.html);
                    $('#fields_wrapper').show();
                    $('#instructions').show();
                    $('[data-toggle="tooltip"]').tooltip(); 
                    common_select();
                    fRefresh();
                } else if (res.status == 'fail') {

                    var is_error = true;
                    $.each(res.errors, function (index, error) {

                        if(index == 'csv_file'){
                           $("#ajax_loader").hide();
                           swal({
                                text: res.errors['csv_file'],
                                showCancelButton: false,
                                confirmButtonText: "Close",
                                showCloseButton: false
                            }).then(function () {
                                $('#csv_file').val('');
                                $('#choose_file').val('');
                                $('#fields_wrapper').html('');
                                $('#fields_wrapper').hide();
                            });
                            $("#btn_import").prop("disabled", false);
                            return false;
                        }  

                        $('#err_' + index).html(error).show();
                        if (is_error) {
                            var offset = $('#err_' + index).offset();
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 50;
                            $('body,html').animate({scrollTop: totalScroll}, 1200);
                            is_error = false;
                        }
                    });
                } else if (res.status == 'success') {
                    window.location = 'members_import_summary.php';
                }
                $("#ajax_loader").hide();
                // $('.select').selectpicker('refresh');
            }
        });

        $(document).on('click', '.cancel_btn', function () {
            swal({
                text: "Cancel Changes: Are you sure?",
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then(function () {
                window.location = 'member_listing.php';
            }, function (dismiss) {

            });

        });

        // get_import_progress();
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
  