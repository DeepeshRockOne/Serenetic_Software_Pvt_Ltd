<div class="panel panel-default  panel-space">
  <form  name="pmpm_form" id="pmpm_form" method="POST" action="">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs16 mn"><strong class="fw500">Assign PMPM Commission</strong></p>
    </div>
  </div>
  <div class="panel-body theme-form">
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group height_auto">
            <select class="form-control" name="receiving_agents" id="receiving_agents" data-live-search="true">
              <option data-hidden="true"></option>
              <?php foreach ($agents as $key => $value) { ?>
                <option value="<?=$value['id']?>" <?=in_array($value['id'],$receiving_agents) ? "selected = 'selected'" : ''?>><?=$value['rep_id']?> - <?=$value['agent_name']?></option>                  
              <?php } ?>
            </select>
            <label>Agent Receiving PMPMs</label>
            <p class="error" id="error_receiving_agents"></p>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <input type="text" id="rule_code" name="rule_code" class="form-control" value="<?=$rule_code?>">
          <label>PMPM ID (Must be Unique)</label>
          <p class="error" id="error_rule_code"></p>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
        <select class="form-control" data-old_status="<?=$status?>" name="status" id="status">
            <option value="Active" <?=$status == 'Active' ? "selected = 'selected'" : ''?>>Active</option>
            <option value="Inactive" <?=$status == 'Inactive' ? "selected = 'selected'" : ''?>>Inactive</option>
          </select>
          <label>Status</label>
          <p class="error" id="error_status"></p>
        </div>
      </div>
    </div>

    <div class="clearfix"></div>
    <div id="fee_table">
        <p class="fs16 m-t-20 m-b-20"><strong class="fw500">Fees</strong></p>
        <div class="clearfix"></div>
        <div id="pmpm_fee_div">
        </div>
    </div>

    <div class="step_btn_wrap m-t-30 text-right"> 
        <input type="submit" name="add_pmpm_fee" id="add_pmpm_fee" class="btn btn-action" value="Save">
        <input type="button" name="" id="" class="btn red-link" value="Cancel" onclick="window.location='pmpm_commission.php'">
        <input type="hidden" name="pmpm_id" id="pmpm_id" value="<?= $pmpm_id ?>">
        <input type="hidden" name="is_clone" id="is_clone" value="<?= $is_clone ?>">
        <!-- <input type="hidden" name="ids" id="ids" value="1"> -->
        <input type="hidden" name="ids" id="ids" value="<?= $pmpm_fee_ids?>">
    </div>
  </div>
  </form>
</div>
<script type="text/javascript">
$(document).ready(function(){
  load_pmpm_fee_div();
  $('.add_pmpm_fee').colorbox({iframe: true, width: '855px', height: '660px'});
	$('.pmpm_com_product').colorbox({iframe: true, width: '800px', height: '400px'});
	$('.pmpm_agent_popup').colorbox({iframe: true, width: '800px', height: '400px'});
    $(".popup").colorbox({iframe: true, width: '800px', height: '600px'});
	

  $('#pmpm_form').on('submit', function(e){
        e.preventDefault();
        $("#ajax_loader").show();
        $.ajax({
            url: '<?= $ADMIN_HOST ?>/ajax_manage_pmpm.php',
            type: 'POST',
            data: $('#pmpm_form').serialize(),
            dataType: 'json',
            success: function (res) {
                $("#ajax_loader").hide();
                $('.error').html('');
                if(res.status=="success"){
                    $("#ids").val(res.pmpm_fee_id);
                    load_pmpm_fee_div();
                    window.location.href=res.redirect_url;
                }else{
                    var is_error = true;
                    $.each(res.errors, function (index, error) {
                        $('#error_' + index).html(error);
                        if (is_error) {
                            var offset = $('#error_' + index).offset();
                            if(typeof(offset) === "undefined"){
                                console.log("Not found : "+index);
                            }else{
                                var offsetTop = offset.top;
                                var totalScroll = offsetTop - 195;
                                $('body,html').animate({
                                    scrollTop: totalScroll
                                }, 1200);
                                is_error = false;
                            }
                        }
                    });
                }
            }
        });
  });

  $(document).on("click",".delete_vendor_fee",function(){
    $id=$(this).attr('data-id');
    swal({
        text: '<br>Delete Record: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function () {
        $("#ajax_loader").show();
        $.ajax({
            url:'add_pmpm_commission.php',
            dataType:'JSON',
            type:'POST',
            data:{delete_id:$id,action:'delete'},
            success:function(res){
                $("#ajax_loader").hide();
                if(res.status=="success"){
                    load_pmpm_fee_div();
                    setNotifySuccess(res.message);
                    // window.location.reload();       
                }else{
                    setNotifyError(res.message);
                    // window.location.reload();       
                }
            }
        })
    }, function (dismiss) {
        
    });

  });

  $(document).off('change', '.fee_status');
  $(document).on("change", ".fee_status", function(e) {
    e.stopPropagation();
    $feeId = $(this).attr("data-id");
    var old_val = $(this).attr('data-old_status');
    $status = $(this).val();
    swal({
      text: 'Change Status: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
      showCloseButton: true,
    }).then(function() {
      $("#ajax_loader").show();
      $.ajax({
        url: "add_pmpm_commission.php",
        dataType: 'JSON',
        type: 'GET',
        data: {feeId: $feeId,status: $status},
        success: function(res) {
          $("#ajax_loader").hide();
          if (res.status == 'success') {
            setNotifySuccess(res.message);
            setTimeout(function() {
              window.location.reload();
            }, 500);
          }
        }
      });
    }, function(dismiss) {
      $('#fee_status_'+$feeId).val(old_val);
      $('#fee_status_'+$feeId).selectpicker('render');
    })
  });
});

load_pmpm_fee_div = function(){
    $fee_id = $("#pmpm_id").val();
    $ids = $("#ids").val();
    $is_clone = $("#is_clone").val();

    $.ajax({
        url:'ajax_load_pmpm_com.php',
        dataType:'JSON',
        data:{pmpm_id:$fee_id,ids:$ids,is_clone:$is_clone},
        type:'POST',
        success:function(res){
            if(res.status=="success"){
                $("#pmpm_fee_div").html(res.pmpm_fee_div);
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
                $('.add_pmpm_fee').colorbox({iframe: true, width: '855px', height: '660px'});
                $(".popup").colorbox({iframe: true, width: '800px', height: '600px'});
                common_select();
            }
        }
    });
}

$(document).off('change','#status');
$(document).on('change','#status',function(){
    $pmpm_id = $('#pmpm_id').val();
    var old_val = $(this).attr('data-old_status');
    $status = $(this).val();
    swal({
        text: '<br>Change Status: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function() {
        window.location = 'pmpm_commission.php?fee_id=' + $pmpm_id + '&fees_status=' + $status;
    }, function(dismiss) {
      $('#status').val(old_val);
      $('#status').selectpicker('render');
    });
});
</script> 
