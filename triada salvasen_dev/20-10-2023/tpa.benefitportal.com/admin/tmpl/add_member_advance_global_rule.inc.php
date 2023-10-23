<div class="panel panel-default  panel-space">
  <form  name="mbrGlobalRule" id="mbrGlobalRule" method="post">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs16 mn"><strong class="fw500">Advance Commission -</strong> <span class="fw300">Charged to Members</span></p>
    </div>
  </div>
  <div class="panel-body theme-form">
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <input type="text" id="displayId" name="display_id" class="form-control" value="<?=$displayId?>">
          <label>Advance ID (Must be Unique)</label>
          <p class="error" id="error_display_id"></p>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
        <select class="form-control" data-old_status="<?=$status?>" name="status" id="status" data-ruleType="Global" data-chargedTo="<?=checkIsset($advRes['charged_to'])?>">
            <option value="Active" <?=$status == 'Active' ? "selected = 'selected'" : ""?>>Active</option>
            <option value="Inactive" <?=$status == 'Inactive' ? "selected = 'selected'" : ""?>>Inactive</option>
          </select>
          <label>Status</label>
            <p class="error" id="error_status"></p>
        </div>
      </div>
    </div>
    <h4 class="fs16 m-t-20 m-b-20">Advances</h4>
    <div id="fee_table">
        <div class="clearfix"></div>
        <div id="advanceFeeDiv">
        </div>
         <p class="error" id="error_advFeeIds"></p>
    </div>
    <div class="step_btn_wrap m-t-30 text-right"> 
        <input type="submit" id="addMbrGlobalFee" class="btn btn-action" value="Save">
        <input type="button" class="btn red-link" value="Cancel" onclick="window.location='advances_commission.php'">
        <input type="hidden" name="advRuleId" id="advRuleId" value="<?=checkIsset($advRuleId)?>">
        <input type="hidden" name="advFeeIds" id="advFeeIds" value="<?=checkIsset($advFeeIds)?>">
        <input type="hidden" name="chargedTo" id="chargedTo" value="Members">
        <input type="hidden" name="ruleType" id="ruleType" value="Global">
    </div>
  </div>
  </form>
</div>
<script type="text/javascript">
$(document).ready(function(){
  load_advance_fee_div();

  $('#mbrGlobalRule').on('submit', function(e) {
    e.preventDefault();
    $("#ajax_loader").show();
    $.ajax({
      url: '<?= $ADMIN_HOST ?>/ajax_manage_advance.php',
      type: 'POST',
      data: $('#mbrGlobalRule').serialize(),
      dataType: 'json',
      success: function(res) {
        $("#ajax_loader").hide();
        $('.error').html('');
        if (res.status == "success") {
          parent.setNotifySuccess('Advance Commission Rule Saved Successfully');
          setTimeout(function() {
            window.location = "advances_commission.php";
          }, 1000);
        } else {
          var is_error = true;
          $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
            if (is_error) {
              var offset = $('#error_' + index).offset();
              if (typeof(offset) === "undefined") {
                console.log("Not found : " + index);
              } else {
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
  $(document).on("click", ".delAdvanceFee", function(e) {
    e.preventDefault();
    $feeId = $(this).attr("data-id");
    swal({
      text: 'Delete Advance Commission Fee: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
      showCloseButton: true,
    }).then(function() {
      $("#ajax_loader").show();
      $.ajax({
        url: "add_member_advance_global_rule.php",
        dataType: 'JSON',
        type: 'GET',
        data: {
          feeId: $feeId,
          delete: 'Y'
        },
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
      window.location.reload();
    })
  });

});
load_advance_fee_div = function() {
  $advRuleId = $("#advRuleId").val();
  $advFeeIds = $("#advFeeIds").val();
  $ruleType = $("#ruleType").val();
  $chargedTo = $("#chargedTo").val();

  $.ajax({
    url: 'ajax_load_advance_rules.php',
    dataType: 'JSON',
    data: {
      advRuleId: $advRuleId,
      advFeeIds: $advFeeIds,
      ruleType: $ruleType,
      chargedTo: $chargedTo
    },
    type: 'POST',
    success: function(res) {
      if (res.status == "success") {
        $("#advanceFeeDiv").html(res.advanceFeeDiv);
        $('[data-toggle="popover"]').popover();
        $('[data-toggle="tooltip"]').tooltip();
        common_select();
        $('.addAdvanceFee').colorbox({
          iframe: true,
          width: '855px',
          height: '660px'
        });
        $('.advPrdPopup').colorbox({
          iframe: true,
          width: '800px',
          height: '400px'
        });
      }
    }
  });
}

$(document).off('change','#status');
$(document).on('change','#status',function(){
  $advRuleId = $('#advRuleId').val();
  $status = $(this).val();
  var old_val = $('#status').attr('data-old_status');
  $chargedTo = $(this).attr('data-chargedTo');
  $ruleType = $(this).attr('data-ruleType');
  
  swal({
    text: '<br>Change Status: Are you sure?',
    showCancelButton: true,
    confirmButtonText: 'Confirm',
    cancelButtonText: 'Cancel',
  }).then(function() {
    window.location = 'advances_commission.php?rule_id=' + $advRuleId + '&rule_status=' + $status + '&chargedTo=' + $chargedTo + '&ruleType=' + $ruleType;
  }, function(dismiss) {
    $('#status').val(old_val);
    $('#status').selectpicker('render');
  });
});

</script>