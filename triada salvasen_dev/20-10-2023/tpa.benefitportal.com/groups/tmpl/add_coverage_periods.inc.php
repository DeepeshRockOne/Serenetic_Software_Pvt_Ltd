<div class="container">
   <div class="section-padding "> 
        <div class="panel panel-default panel-block">
            <div class="panel-body theme-form">
                <h4 class="fs16 m-b-25">Plan Period Settings</h4>
                <form  method="POST" id="manage_coverage_form" enctype="multipart/form-data"  autocomplete="off">
                    <input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>">
                    <input type="hidden" name="is_clone" id="is_clone" value="<?= $is_clone  ?>">
                    <input type="hidden" name="tmp_coverage_id" id="tmp_coverage_id" value="<?= $tmp_coverage_id ?>">
                    <input type="hidden" name="coverage_id" id="coverage_id" value="<?= $coverage_id ?>">
                    <input type="hidden" name="save_type" id="save_type" value="">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" name="coverage_period_name" id="coverage_period_name" class="form-control" value="<?= !empty($coverage_period_name) ? $coverage_period_name : '' ?>">
                                <label>Plan Period Name</label>
                                <p class="error" id="error_coverage_period_name"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" name="display_id" id="display_id" class="form-control" value="<?= !empty($display_id) ? $display_id : '' ?>">
                                <label>Plan Period ID</label>
                                <p class="error" id="error_display_id"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select class="form-control" name="status" id="status">
                                    <option></option>
                                    <option value="Active" <?= !empty($status) && $status=='Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= !empty($status) && $status=='Inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                                <label>Status</label>
                                <p class="error" id="error_status"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon datePickerIcon" data-applyon="coverage_period_start"> <i class="material-icons fs16">date_range</i> </div>
                                    <div class="pr">
                                        <input type="text" class="form-control dates" name="coverage_period_start" id="coverage_period_start" value="<?= !empty($coverage_period_start) ? $coverage_period_start : '' ?>">
                                        <label class="label-wrap">Plan Period Start (MM/DD/YYYY)</label>
                                    </div>
                                </div>
                                <p class="error" id="error_coverage_period_start"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon datePickerIcon" data-applyon="coverage_period_end"> <i class="material-icons fs16">date_range</i> </div>
                                    <div class="pr">
                                        <input type="text" class="form-control dates" name="coverage_period_end" id="coverage_period_end" value="<?= !empty($coverage_period_end) ? $coverage_period_end : '' ?>">
                                        <label class="label-wrap">Plan Period End (MM/DD/YYYY)</label>
                                    </div>
                                </div>
                                <p class="error" id="error_coverage_period_end"></p>
                            </div>
                        </div>
                    </div>
                </form>
                <h4 class="fs16 m-b-25">Offerings</h4>
                <div class="table-responsive" id="load_coverage_offering_div">
                </div>
                <div class="text-right">
                    <a href="javascript:void(0);" class="btn btn-action" id="save_coverage">Save</a>
                    <a href="javascript:void(0);" class="btn red-link" id="cancel_coverage">Cancel</a>
                </div>
            </div>
        </div>
     </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(".dates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $(".dates").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
      });
    load_coverage_offering();
});
$(document).on("click focus",".datePickerIcon",function(){
      $id=$(this).attr('data-applyon');
      $("#"+$id).datepicker('show');
      $("#"+$id).trigger("blur");
});

$(document).on("click","#save_coverage",function(){
    save_coverage('');
});
$(document).on("click","#offering_coverage_periods",function(){
    save_coverage('add_offering');
});
$(document).on("click","#cancel_coverage",function(){
     window.location.href="coverage_periods.php";
});

save_coverage = function($save_type){
    $("#save_type").val($save_type);
    $(".error").html('');
    $("#ajax_loader").show();
    $.ajax({
      url:'ajax_add_coverage_periods.php',
      dataType:'JSON',
      data:$("#manage_coverage_form").serialize(),
      type:'POST',
      success:function(res){
        $("#ajax_loader").hide();
        if(res.status=="success"){
            $("#tmp_coverage_id").val(res.coverage_id);
            $("#coverage_id").val(res.coverage_id);
            if($save_type == "add_offering"){
                $.colorbox({
                    href:res.href,
                    iframe:true, 
                    width:"900px",
                    height:"550px",
                    escKey: false,
                    overlayClose: false,
                    onClosed : function(){
                        load_coverage_offering();
                    }
                });
            }else{
                window.location.href="coverage_periods.php";           
            }
          
        }else{
          var is_error = true;
          $.each(res.errors, function (index, value) {
            $('#error_' + index).html(value).show();
            if(is_error){
                var offset = $('#error_' + index).offset();
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

$(document).off("click",".clone_offering");
$(document).on("click",".clone_offering",function(){
    $href=$(this).attr('data-href');
    $.colorbox({
        href:$href,
        iframe:true, 
        width:"900px",
        height:"550px",
        onClosed : function(){
            load_coverage_offering();
        }
    });
});

$(document).off("click",".edit_offering");
$(document).on("click",".edit_offering",function(){
    $href=$(this).attr('data-href');
    $.colorbox({
        href:$href,
        iframe:true, 
        width:"900px",
        height:"550px",
        escKey: false,
        overlayClose: false,
        onClosed : function(){
            load_coverage_offering();
        }
    });
});



function changeStatus(id, coverage_status, old_val) {
    swal({
          text: "Change Status: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
    }).then(function() {
        $.ajax({
              url: 'ajax_change_offering_status.php',
              data: {
                  id: id,
                  status: coverage_status
              },
              method: 'POST',
              dataType: 'json',
              success: function(res) {
                  if (res.status == "success") {
                      setNotifySuccess(res.msg);
                  }else{
                      setNotifyError(res.msg);
                  }
              }
        });
          
    }, function(dismiss) {
        $('#offering_status_' + id).val(old_val);
        $('select.form-control').selectpicker('render');
        return false;
    })
}

$(document).off("click",".delete_offering");
$(document).on("click",".delete_offering",function(){
    var id = $(this).attr('data-id');
    swal({
          text: "Delete Record: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
    }).then(function() {
        $.ajax({
            url: 'ajax_delete_offering_coverage_periods.php',
             data: {
                  id: id,
            },
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.status == "success") {
                    load_coverage_offering();
                    setNotifySuccess(res.msg);
                }else{
                    setNotifyError(res.msg);
                }
            }
        });
          
    }, function(dismiss) {
    })
});

load_coverage_offering = function(){
    $coverage_id = $("#tmp_coverage_id").val();
    $is_clone = $("#is_clone").val();
    $('#ajax_loader').show();
    $.ajax({
      url: 'ajax_load_coverage_offering.php',
      type: 'POST',
      data: {coverage: $coverage_id,is_clone:$is_clone},
      dataType:'JSON',
      success: function(res) {
        $('#ajax_loader').hide();
        $('#load_coverage_offering_div').html(res.html).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
}
$(document).off("click",".coverage_periods_product");
$(document).on("click",".coverage_periods_product",function(){
    $href= $(this).attr('data-href');
    $.colorbox({href:$href,iframe:true, width:"500px",height:"550px"});
});
</script>

    
  