<div class="panel panel-default panel-block advance_info_div">
  <div class="panel-body">
    <div class="phone-control-wrap ">
      <div class="phone-addon w-90">
        <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="70px">
      </div>
      <div class="phone-addon text-left v-align-top">
        <div class="row">
          <div class="col-sm-12 col-md-10 col-lg-6">
            <p class="fs14 mn">Below are the different settings allowed as they relate to payments within system. Here you may set reasons for reversals, commission periods, recapture failed payments settings, and review payment related script statuses.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reversal Reasons Div Code Start -->
<!-- <div class="white-box">
  <div class="clearfix m-b-15">
    <h4 class="m-t-7 pull-left">Reversal Reasons</h4>
    <div class="pull-right">
      <a href="manage_reversal_reasons.php" class="addReasonsLink btn btn-action">+ Reason</a>
    </div>
  </div>
  <div id="reversalDiv"></div>
</div> -->
<!-- Reversal Reasons Div Code Ends -->

<!-- Global End coverage periods Settings Div Code Start -->
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="pay-set-btn">
      <h4 class="pull-left m-t-0">Settings: End Plan Period</h4>
      <div class="pull-right">
        <div class="m-b-15">
          <a href="javascript:void(0)" class="btn btn-action" id="editEndCoveSettingsBtn"><b>Edit</b></a>
          <a href="javascript:void(0)" class="btn btn-action-o" id="saveEndCoveSettingsBtn" style="display: none;"><b>Save</b></a>
        </div>
      </div>
    </div>  
    <div class="clearfix"></div>
    <div class="theme-form">
      <label class="m-b-15">
        <input type="checkbox" name="isOpenEnrollEndCoverage" id="isOpenEnrollEndCoverage"  class="openEnrollmentInputs" <?= !empty($endCoverageRes) && !empty($endCoverageRes['is_open_enrollment']) && $endCoverageRes['is_open_enrollment'] == 'Y' ? 'checked' : '' ?> disabled="disabled"> &nbsp;Open Application</label>
      <div class="p-l-30">
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <input type="text" class="form-control dateClass openEnrollmentInputs" name="openEnrollEndCoverage" id="openEnrollEndCoverage" value="<?= !empty($endCoverageRes) && !empty($endCoverageRes['end_coverage_date']) ? date('m/d/Y',strtotime($endCoverageRes['end_coverage_date'])) : '' ?>" disabled="disabled">
              <label>End Plan Date</label>
              <span class="error" id="error-endCoverage"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Global End coverage periods Settings Div Code Ends -->


<!-- Commission Periods Settings Div Code Start -->
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="pay-set-btn">
      <h4 class="pull-left m-t-0">Settings: Commission Period(s)</h4>
      <div class="pull-right">
        <div class="m-b-15">
          <a href="javascript:void(0)" class="btn btn-action" id="editCommSettingsBtn"><b>Edit</b></a>
          <a href="javascript:void(0)" class="btn btn-action-o" id="saveCommSettingsBtn" style="display: none;"><b>Save</b></a>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="theme-form">
      <label class="m-b-15"><input type="checkbox" checked="checked" disabled="disabled"> &nbsp;Weekly Period</label>
      <div class="p-l-30">
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <select class="form-control" name="weekly_period" id="weeklyPeriodSel" disabled="disabled">
                <option value="Saturday">Sunday - Saturday</option>
                <option value="Sunday">Monday - Sunday</option>
                <option value="Monday">Tuesday - Monday</option>
                <option value="Tuesday">Wednesday  - Tuesday</option>
                <option value="Wednesday">Thursday - Wednesday</option>
                <option value="Thursday">Friday - Thursday</option>
                <option value="Friday">Saturday - Friday</option>
              </select>
              <label>Day</label>
            </div>
          </div>
        </div>
      </div>
      
      <label class="m-b-15"><input type="checkbox" checked="checked" disabled="disabled">&nbsp;Monthly Period</label>
      <div class="p-l-30">
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <input type="text" class="form-control" value="1st - End of Month" readonly="readonly">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Commission Periods Settings Div Code Ends -->

<!-- Failed Renewal Settings Div Code Start -->
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="pay-set-btn">
      <h4 class="pull-left m-t-0">Settings: Recapture Failed Payment(s)</h4>
      <div class="pull-right">
        <div class="m-b-15">
          <a href="javascript:void(0)" class="btn btn-info addSaveAttemptBtnCls" id="addAttemptBtn" style="display: none;">+ Failed Attempt</a>
          <a href="javascript:void(0)" class="btn btn-action addSaveAttemptBtnCls" id="saveAttemptBtn" style="display: none;">Save</a>
          <a href="javascript:void(0)" class="btn btn-action-o editAttemptBtnCls" id="editAttemptBtn">Edit</a>
        </div>
      </div>
    </div>  
    <div id="reattemptDiv"></div>
  </div>
</div>
<!-- Failed Renewal Settings Div Code Ends -->

<!-- Scripts Div Code Start -->
  <div id="scriptsDiv"></div>
<!-- Scripts Div Code Ends -->

<script type="text/javascript">
$(document).ready(function() {
  dropdown_pagination('scriptsDiv')

  // load_reversal_reason_settings();
  load_failed_renewal_settings();
  load_scripts_settings();

  $(".se_multiple_select").multipleSelect({
    selectAll: false
  });

  $('.dateClass').inputmask({"mask": "99/99/9999",'showMaskOnHover': false}); 


  // commission period settings code start
  var weeklyCommissionDay = '<?=$weeklyCommissionDay?>';
  $("#weeklyPeriodSel").val(weeklyCommissionDay);
  $(document).off("click", "#editCommSettingsBtn");
  $(document).on("click", "#editCommSettingsBtn", function() {
    editCommSettings();
  });

  $(document).off("click", "#saveCommSettingsBtn");
  $(document).on("click", "#saveCommSettingsBtn", function() {
    $day = $("#weeklyPeriodSel :selected").val();
    $payPeriod = $("#weeklyPeriodSel :selected").html();
      $action = "ajax_commission_periods_settings.php";
      $.ajax({
        url: $action,
        type: "POST",
        dataType: "json",
        data: {
          day: $day,
          payPeriod: $payPeriod,
          type: "weekly"
        },
        beforeSend: function() {
          $("#ajax_loader").show();
        },
        success: function(res) {
          $("#ajax_loader").hide();
          if (res.status == 'success') {
            setNotifySuccess(res.msg);
          }
        }
      });
    readOnlyCommSettings();
  });

});

// End Coverage Period settings code start


  $(document).off("click", "#editEndCoveSettingsBtn");
  $(document).on("click", "#editEndCoveSettingsBtn", function() {
    editEndCoveSettings();
  });

  $(document).off("click", "#saveEndCoveSettingsBtn");
  $(document).on("click", "#saveEndCoveSettingsBtn", function() {
    $endCoverage = $("#openEnrollEndCoverage").val();
    $(".error").html('');
    if($('#isOpenEnrollEndCoverage').is(":checked")){
      $isOpenEnrolllment = 'Y';
    }else{
      $isOpenEnrolllment = 'N';
    }

    $action = "ajax_end_coverage_period_settings.php";
      $.ajax({
        url: $action,
        type: "POST",
        dataType: "json",
        data: {
          endCoverage: $endCoverage,
          isOpenEnrolllment: $isOpenEnrolllment
        },
        beforeSend: function() {
          $("#ajax_loader").show();
        },
        success: function(res) {
          $("#ajax_loader").hide();
          if (res.status == 'success') {
            setNotifySuccess(res.msg);
            readOnlyEndCoveSettings();
          }else{
             $.each(res.errors,function($k,$v){
                $("#error-"+$k).html($v);
             });
          }
        }
      });
    
  });

  readOnlyEndCoveSettings = function() {
    $(".openEnrollmentInputs").prop("disabled", true);
    
    $("#saveEndCoveSettingsBtn").hide();
    $("#editEndCoveSettingsBtn").show();
  }
  editEndCoveSettings = function() {
    $(".openEnrollmentInputs").prop("disabled", false);
    $("#editEndCoveSettingsBtn").hide();
    $("#saveEndCoveSettingsBtn").show();
  }
// End Coverage Period settings code end

// load_reversal_reason_settings = function() {
//   $('#reversalDiv').hide();
//   $.ajax({
//     url: 'reversal_reasons_listing.php',
//     type: 'GET',
//     data: {
//       is_ajaxed: 1,
//     },
//     beforeSend: function() {
//       $("#ajax_loader").show();
//     },
//     success: function(res) {
//       $('#ajax_loader').hide();
//       $('#reversalDiv').html(res).show();

//       $(document).off("click", ".addReasonsLink");
//       $(document).on("click", ".addReasonsLink", function(e) {
//         e.preventDefault();
//         $link = $(this).attr('href');
//         $.colorbox({
//           href: $link,
//           iframe: true,
//           width: '550px',
//           height: '230px',
//           onClosed: function() {}
//         });
//       });
//     }
//   });
// }
load_failed_renewal_settings = function() {
  $('#reattemptDiv').hide();
  $.ajax({
    url: 'failed_renewal_settings.php',
    type: 'GET',
    data: {
      is_ajaxed: 1,
    },
    beforeSend: function() {
      $("#ajax_loader").show();
    },
    success: function(res) {
      $('#ajax_loader').hide();
      $('#reattemptDiv').html(res).show();
      common_select();
      fRefresh();
      $("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
    }
  });
}
readOnlyCommSettings = function() {
  $("#weeklyPeriodSel").prop("disabled", true);
  $('#weeklyPeriodSel').selectpicker('refresh');
  $("#saveCommSettingsBtn").hide();
  $("#editCommSettingsBtn").show();
}
editCommSettings = function() {
  $("#weeklyPeriodSel").prop("disabled", false);
  $('#weeklyPeriodSel').selectpicker('refresh');
  $("#editCommSettingsBtn").hide();
  $("#saveCommSettingsBtn").show();
}
load_scripts_settings = function() {
  $('#scriptsDiv').hide();
  $.ajax({
    url: 'scripts_listing.php',
    type: 'GET',
    data: {
      is_ajaxed: 1,
    },
    beforeSend: function() {
      $("#ajax_loader").show();
    },
    success: function(res) {
      $('#ajax_loader').hide();
      $('#scriptsDiv').html(res).show();
        common_select();
    }
  });
}
</script>