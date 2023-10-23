<style type="text/css">
.iframe .dropdown.bootstrap-select .dropdown-menu.open{max-height:170px!important; min-height: 100%!important;}
.iframe .dropdown.bootstrap-select .dropdown-menu .inner.open{max-height:120px!important; min-height: 100%!important;}
.ms-drop ul { max-height: 120px !important; }
.generate_report_panel.panel .panel-body{min-height: 85vh;}
</style>
<div class="panel panel-default panel-block generate_report_panel">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">Generate -  <span class="fw300"><?=$report_row['report_name']?></span></h4>
      </div>
   </div>
   <div class="panel-body">
      <form name="frm_generate_report" id="frm_generate_report" method="POST" action="">
          <input type="hidden" name="report_id" value="<?=$report_id?>">
          <input type="hidden" name="hdn_columns" id="hdn_columns">
          <div class="theme-form">
            <?php 
                if($report_row['category_id'] == 1) { //Admin Reports
                    include_once("admin_reports_fields.inc.php");
                }else if($report_row['category_id'] == 2) { //Agent Reports
                    include_once("agent_reports_fields.inc.php");
                }else if($report_row['category_id'] == 3) { //Group Reports
                    include_once("group_reports_fields.inc.php");
                }else if($report_row['category_id'] == 9) { //Payment Reports
                    include_once("payment_reports_fields.inc.php");
                }else if($report_row['category_id'] == 5) { //Member Reports
                    include_once("member_reports_fields.inc.php");
                }else if($report_row['category_id'] == 8) { //Commission Reports
                    include_once("commission_reports_fields.inc.php");
                }else if($report_row['category_id'] == 7) { //Product Reports
                    include_once("product_reports_fields.inc.php");
                }else if($report_row['category_id'] == 4){
                    include_once("lead_reports_fields.inc.php");
                }else if($report_row['category_id'] == 19){
                    include_once("participants_reports_fields.inc.php");
                }else if($report_row['category_id'] == 10){//Client Support Reports
                    include_once("client_support_reports_fields.inc.php");
                }
            ?>
          </div>
      </form>
   </div>
   <div class="panel-footer text-center">
        <button type="button" id="btn_generate_report" class="btn btn-action">Export</button>
        <a href="javascript:void(0);" class="btn red-link"  onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
   </div>
</div>
<script type="text/javascript">
    var is_columns_selections = false;
    $(document).ready(function() {

        if($('#pre-selected-options').length > 0) {
            is_columns_selections = true;
        }

        if(is_columns_selections == true) {
            $('#pre-selected-options').multiSelect({
                selectableHeader : "<div class='multiselect_leftlabel'>Available Columns</div> <a href='#' id='select-all'>Move All</a>",
                selectionHeader: "<div class='multiselect_rightlabel'>Selected Columns</div><a href='#' id='deselect-all'>Move All</a>",
            });
            $('#select-all').click(function(){
                $('#pre-selected-options').multiSelect('select_all');
                return false;
            });
            $('#deselect-all').click(function(){
                $('#pre-selected-options').multiSelect('deselect_all');
                return false;
            });
        }

        $(document).off('click','#btn_generate_report');
        $(document).on('click','#btn_generate_report',function(){
            if(is_columns_selections == true) {
                var selectedValues = [];
                $(".visible_columns li").each(function(ind,val){
                    if($(val).css('display') != 'none'){
                        $text = val.innerText;
                        if($text != ""){
                            selectedValues.push($text); 
                        }
                    } 
                });
                $('#hdn_columns').val(selectedValues);
            }       
            $.ajax({
                url: 'ajax_generate_report.php',
                data: $("#frm_generate_report").serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    parent.$("#ajax_loader").show();
                },
                success: function(res) {
                    $(".error").hide();
                    
                    if (res.status == 'success') {
                        parent.$("#ajax_loader").hide();
                        confirm_view_export_request(true);

                    } else if (res.status == 'report_not_found') {
                        window.parent.location = 'set_reports.php';

                    } else if(res.status == 'custom_error') {
                        parent.$("#ajax_loader").hide();
                        parent.setNotifyError(res.message);
                    } else {
                        parent.$("#ajax_loader").hide();
                        var is_error = true;
                        $.each(res.errors, function(key, value) {
                            $('.error_' + key).parent("p.error").show();
                            $('.error_' + key).html(value).show();

                            if (is_error == true && $('.error_' + key).length > 0) {
                                is_error =false;
                                $('html, body').animate({
                                    scrollTop: parseInt($('.error_' + key).offset().top) - 100
                                }, 1000);
                            }
                        });
                    }
                }
            });
        });
    });
    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
        e.preventDefault();
        if($(this).val() == ''){			        
            $('#range_join').hide();
            // $('#all_join').hide();
            // $("#date_range").removeClass('col-xs-4').addClass('col-xs-6');
            // $(".select_date_div").removeClass('col-xs-8').addClass('col-xs-6');
        } else {
            $("#date_range").removeClass('col-xs-8').addClass('col-xs-4');
            $(".select_date_div").removeClass('col-xs-6').addClass('col-xs-8');
            if ($(this).val() == 'Range') {
                $('#range_join').show();
                $('#all_join').hide();
            } else {
                $('#range_join').hide();
                $('#all_join').show();
            }
        }
    });
</script>