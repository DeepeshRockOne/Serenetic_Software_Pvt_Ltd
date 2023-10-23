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
          <div class="theme-form">
            <?php 
                if($report_row['category_id'] == 14) { //Sales Reports
                    include_once("sales_reports_fields.inc.php");

                } else if($report_row['category_id'] == 15) { //Retention Reports
                    include_once("retention_reports_fields.inc.php");
                
                } else if($report_row['category_id'] == 16) { //Commission Reports
                    include_once("commission_reports_fields.inc.php");
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
    $(document).ready(function() {
        $(document).off('click','#btn_generate_report');
        $(document).on('click','#btn_generate_report',function(){
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
                        confirm_view_export_request(true,'agent');

                    } else if (res.status == 'report_not_found') {
                        window.parent.location = 'reporting.php';

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
        } else {
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