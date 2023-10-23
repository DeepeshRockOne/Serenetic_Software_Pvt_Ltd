<div class="panel panel-block panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h4 class="mn">Report</h4>
        </div>
    </div>
    <div class="panel-body">
        <form name="frm_report" id="frm_report" method="POST" action="">
            <input type="hidden" name="report_id" value="<?=$report_id?>">
            <input type="hidden" name="order_by" value="<?=$order_by?>">
            <input type="hidden" name="file_type" value="<?=$file_type?>">
            <input type="hidden" name="ftp_name" value="<?=$ftp_name?>">
            <div class="theme-form">
                <div class="form-group">
                    <select class="form-control" id="portal" name="portal">
                        <option data-hidden="true"></option>
                        <option value="Admin" <?="Admin" == $portal?'selected="selected"':''?>>Admin</option>
                        <option value="Agent" <?="Agent" == $portal?'selected="selected"':''?>>Agent</option>
                        <option value="Group" <?="Group" == $portal?'selected="selected"':''?>>Group</option>
                    </select>
                    <label>Portal</label>
                    <p class="error"><span class="error_portal"></span></p>
                </div>
                <div class="form-group height_auto">
                    <select id="category_id"  name="category_id" data-live-search="true">
                        <option data-hidden="true"></option>
                        <?php foreach($category_res as $category_row){ ?>
                        <option value="<?=$category_row['id']?>" <?=$category_row['id'] == $category_id?'selected="selected"':''?> data-portal="<?=$category_row['portal']?>"><?=$category_row['title']?></option>
                        <?php } ?>
                    </select>
                    <label>Report Category</label>
                    <p class="error"><span class="error_category_id"></span></p>
                </div>
                <div class="form-group height_auto" style="display: none;">
                    <input type="text" class="form-control" name="report_key" value="<?=$report_key?>">
                    <label>Report Key (Need to do changes in programming for change key)</label>
                    <p class="error"><span class="error_report_key"></span></p>
                </div>
                <div class="form-group height_auto">
                    <input type="text" class="form-control" name="report_name" value="<?=$report_name?>">
                    <label>Report Name</label>
                    <p class="error"><span class="error_report_name"></span></p>
                </div>
                <div class="form-group height_auto">
                    <input type="text" class="form-control" name="export_file_report_name" value="<?=$export_file_report_name?>">
                    <label>Export File : Report Name</label>
                    <p class="error"><span class="error_export_file_report_name"></span></p>
                </div>
                <div class="form-group height_auto">
                    <textarea name="purpose_summary" id="purpose_summary" class="form-control <?=!empty($purpose_summary)?'has-value':''?>" rows="3"><?=$purpose_summary?></textarea>
                    <label for="purpose_summary">Purpose Summary</label>
                    <p class="error"><span class="error_purpose_summary"></span></p>
                </div>
                <div class="form-group height_auto m-t-15">
                    <label for="definitions">Enter Definitions</label>
                    <textarea name="definitions" id="definitions" class="summernote"><?=$definitions?></textarea>
                    <p class="error"><span class="error_definitions"></span></p>
                </div>
                <div class="form-group m-t-15">
                    <label for="is_allow_schedule">
                    <input type="checkbox" name="is_allow_schedule" id="is_allow_schedule" value="Y" <?=$is_allow_schedule == "Y"?"checked":""?>>    Allow For Schedule
                    </label>
                    <p class="error"><span class="error_is_allow_schedule"></span></p>
                </div>
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-action" id="btn_submit" type="button">Save</button>
                <a href="javascript:void(0)"  onclick="window.parent.$.colorbox.close()" class="btn red-link">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    initCKEditor("definitions",false,"200px");
    $(document).off('change',"#portal");
    $(document).on('change',"#portal",function(){
        hide_show_category_by_portal();
    });
    $('#category_id').addClass('form-control');
    $('#category_id').selectpicker({ 
        container: 'body', 
        style:'btn-select',
        noneSelectedText: '',
        dropupAuto:false,
    });

    $(document).on('blur',"#purpose_summary",function(){
        if($(this).val() != '') {
            $('#purpose_summary').addClass('has-value');
        } else {
            $('#purpose_summary').removeClass('has-value');
        }
        
    });

    $(document).off('click','#btn_submit');
    $(document).on('click','#btn_submit',function(){
        $("#definitions").val(CKEDITOR.instances.definitions.getData());
        $.ajax({
            url: 'ajax_add_report.php',
            data: $("#frm_report").serialize(),
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                parent.$("#ajax_loader").show();
            },
            success: function(res) {
                $(".error").hide();
                if (res.status == 'success') {
                    window.parent.location.reload();
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

    hide_show_category_by_portal();
});
function hide_show_category_by_portal()
{
    var portal = $("#portal").val();
    var category_res = <?=json_encode($category_res)?>;
    var category_id = $("select#category_id").val();
    $("select#category_id").html('');
    category_id_html = '';
    $.each(category_res,function(index,value){
        if(value.portal == portal) {
            category_id_html += '<option value="'+value.id+'">'+value.title+'</option>';
        }
    });
    $("select#category_id").html(category_id_html);
    if(category_id != '' && $("select#category_id option[value='"+category_id+"']").length > 0) {
        $("select#category_id").val(category_id).change();
    } else {
        $("select#category_id").val('').change();
    }
    $("select#category_id").selectpicker('refresh'); 
}
</script>