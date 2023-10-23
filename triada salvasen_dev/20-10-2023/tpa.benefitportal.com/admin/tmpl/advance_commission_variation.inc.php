<div class="panel panel-default  panel-space">
  <form  name="variationFeeForm" id="variationFeeForm" method="POST" action="">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs16 mn"><strong class="fw500">Advance Commission </strong> <span class="fw300">+ Variation</span></p>
    </div>
  </div>
  <div class="panel-body theme-form">
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group height_auto">
          <select class="form-control" name="receiving_agent" id="receiving_agent" data-live-search="true">
            <option data-hidden="true"></option>
            <?php foreach ($agents as $key => $value) { ?>
              <option value="<?=$value['id']?>" <?=($value['id']==$agentId) ? "selected = 'selected'" : ''?>><?=$value['rep_id']?> - <?=$value['agent_name']?></option>                  
            <?php } ?>
          </select>
          <label>Agent Receiving Advance</label>
          <p class="error" id="error_receiving_agent"></p>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <input type="text" id="display_id" name="display_id" class="form-control" value="<?=$displayId?>">
          <label>Advance ID (Must be Unique)</label>
          <p class="error" id="error_display_id"></p>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
        <select class="form-control" name="status" data-old_status="<?=$status?>"  id="status" data-ruleType="Variation" data-chargedTo="<?=checkIsset($advRes['charged_to'])?>">
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
       <input type="submit" id="addVariationFee" class="btn btn-action" value="Save">
        <input type="button" class="btn red-link" value="Cancel" onclick="window.location='advances_commission.php'">
        <input type="hidden" name="advRuleId" id="advRuleId" value="<?=checkIsset($advRuleId)?>">
        <input type="hidden" name="advFeeIds" id="advFeeIds" value="<?=checkIsset($advFeeIds)?>">
        <input type="hidden" name="chargedTo" id="chargedTo" value="<?=checkIsset($chargedTo)?>">
        <input type="hidden" name="is_clone" id="is_clone" value="<?=checkIsset($is_clone)?>">
        <input type="hidden" name="ruleType" id="ruleType" value="Variation">
    </div>
  </div>
  </form>
</div>
<script type="text/javascript">

$(document).ready(function(){
  load_advance_fee_div();
  
  $('#variationFeeForm').on('submit', function(e) {
    e.preventDefault();
    $("#ajax_loader").show();
    $.ajax({
      url: '<?= $ADMIN_HOST ?>/ajax_manage_advance.php',
      type: 'POST',
      data: $('#variationFeeForm').serialize(),
      dataType: 'json',
      success: function(res) {
        $("#ajax_loader").hide();
        $('.error').html('');
        if (res.status == "success") {
          $("#advFeeIds").val(res.advFeeIds);
          parent.setNotifySuccess('Advance Commission Rule Saved Successfully');
          setTimeout(function() {
            window.location = "advances_commission.php";
          }, 1500);
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
      text: '<br>Delete Record: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      $("#ajax_loader").show();
      $.ajax({
        url: "advance_commission_variation.php",
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

  $('#receiving_agent').on('change',function(){
    load_advance_fee_div();
  });

  $('body').off("click",".addAdvanceFee");
  $('body').on("click",".addAdvanceFee",function(e){
    if($('#receiving_agent').val() == ""){
     alert("Please select Agent");
     e.preventDefault();
    }else{
      $(this).colorbox({iframe: true, href: this.href, width: '800px', height: '600px'});
    }
  });
});

load_advance_fee_div = function() {
  $advRuleId = $("#advRuleId").val();
  $advFeeIds = $("#advFeeIds").val();
  $chargedTo = $("#chargedTo").val();
  $ruleType = $("#ruleType").val();
  $agentId = $("#receiving_agent").val();
  $is_clone = $("#is_clone").val();

  $.ajax({
    url: 'ajax_load_advance_rules.php',
    dataType: 'JSON',
    data: {
      advRuleId: $advRuleId,
      advFeeIds: $advFeeIds,
      chargedTo: $chargedTo,
      agentId: $agentId, 
      is_clone: $is_clone 
    },
    type: 'POST',
    success: function(res) {
      if (res.status == "success") {
        $("#advanceFeeDiv").html(res.advanceFeeDiv);
        $('[data-toggle="popover"]').popover();
        $('[data-toggle="tooltip"]').tooltip();
        common_select();
        $('body').off("click",".addAdvanceFee");
        $('body').on("click",".addAdvanceFee",function(e){
          if($('#receiving_agent').val() == ""){
           alert("Please select Agent");
           e.preventDefault();
          }else{
            $(this).colorbox({iframe: true, href: this.href, width: '800px', height: '600px'});
          }
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
  $chargedTo = $(this).attr('data-chargedTo');
  var old_val = $('#status').attr('data-old_status');
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