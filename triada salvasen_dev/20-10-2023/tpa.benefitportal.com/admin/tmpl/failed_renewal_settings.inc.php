<div class="clearfix"></div>
<form name="attemptForm" id="attemptForm">
  <input type="hidden" name="attemptCnt" id="attemptCnt" value="<?=$attemptCnt?>">
  <div class="theme-form">
    <div class="row">
     <div id="loadAttemptDiv">
      <?php if(!empty($resAttempt)){
        foreach ($resAttempt as $key => $row) {
            $attempt = checkIsset($row['attempt']);
            $attemptFrequency = checkIsset($row['attempt_frequency']);
            $failTriggerId = checkIsset($row['fail_trigger_id']);
            $adminTicket = checkIsset($row['admin_ticket']);
      ?>
        <div class="col-sm-4 failedDiv" id="attemptDiv<?=$row['id']?>">
          <input type="hidden" name="attempt[]" value="<?=$row['id']?>">
          <div class="clearfix m-b-15">
            <span class="pull-left fw500 attemptTitle">Failed Attempt <?=$attempt?></span>
            <a href="javascript:void(0);" class="red-link pull-right removeDiv" data-id="<?=$row['id']?>">Remove</a>
          </div>
          <p>Number of day(s) to attempt after previous failed attempt:</p>
          <div class="form-group">
            <select class="form-control" name="attempt_frequency[<?=$row['id']?>]">
              <option></option>
              <?php
              for($i=1;$i<=20;$i++){
              ?>
              <option value="<?=$i?>" <?=($attemptFrequency == $i) ? "selected='selected'" : ''?>><?=$i?></option>
              <?php
              }
              ?>
            </select>
            <label>Days</label>
            <span class="error" id="error_attempt_frequency<?=$row['id']?>"></span>
          </div>
          <div class="clearfix"></div>
          <div class="form-group">
            <select class="form-control" name="fail_trigger_id[<?=$row['id']?>]" data-live-search="true">
              <option></option>
              <?php
              if(!empty($resTrigger)){
              foreach ($resTrigger as $trigger){
              ?>
              <option value="<?=$trigger['id']?>" <?=($failTriggerId == $trigger['id']) ? "selected='selected'" : ''?>><?=$trigger['display_id']?></option>
              <?php
              }
              }
              ?>
            </select>
            <label>Trigger Email ID for Failed Notification</label>
            <span class="error" id="error_fail_trigger_id<?=$row['id']?>"></span>
          </div>
          <div class="clearfix"></div>
          <label class="label-input"><input type="checkbox" class="adminTicket" name="admin_ticket[<?=$row['id']?>]" <?=$adminTicket=='Y' ? "checked='checked'" : ""?> >Notify support admin by creating a ticket </label>
          <div class="clearfix"></div>
          <hr>
        </div>
      <?php     
        }
      }
      ?>
     </div>
    </div>
  </div>
</form>

<div id="cloneAttemptDiv" style="display: none;">
  <div class="col-sm-4 failedDiv" id="attemptDiv~number~">
        <input type="hidden" name="attempt[]" value="~number~">
        <div class="clearfix m-b-15">
        <span class="pull-left fw500 attemptTitle">Failed Attempt ~title~</span>
        <a href="javascript:void(0);" class="red-link pull-right removeDiv" data-id="~number~">Remove</a>
        </div>
        <p>Number of day(s) to attempt after previous failed attempt:</p>
        <div class="form-group">
          <select class="add_control_~number~" name="attempt_frequency[~number~]">
            <option></option>
            <?php 
              for($i=1;$i<=10;$i++){
            ?>
            <option value="<?=$i?>"><?=$i?></option>
            <?php
              }
            ?>
          </select>
          <label>Days</label>
          <span class="error" id="error_attempt_frequency~number~"></span>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <select class="add_control_~number~" name="fail_trigger_id[~number~]"  id="triggerSel~number~" data-live-search="true">
              <option></option>
              <?php 
                if(!empty($resTrigger)){
                  foreach ($resTrigger as $trigger){
              ?>
                    <option value="<?=$trigger['id']?>"><?=$trigger['display_id']?></option>    
              <?php
                  }
                }
              ?>
            </select>
            <label>Trigger Email ID for Failed Notification</label>
            <span class="error" id="error_fail_trigger_id~number~"></span>
        </div>
        <div class="clearfix"></div>
        <label class="label-input"><input type="checkbox" class="adminTicket" name="admin_ticket[~number~]">Notify support admin by creating a ticket </label>
        <div class="clearfix"></div>
        <hr>
  </div>
</div>


<script type = "text/javascript">
  $(document).ready(function() {

    // do not allow update failed renewal settings
    $('#attemptForm').find(':input,:radio,:checkbox').prop("disabled", true);
    $('.removeDiv').hide();

    $(document).off("click", "#editAttemptBtn");
    $(document).on("click", "#editAttemptBtn", function() {
      // display add/save button
      $(".addSaveAttemptBtnCls").show();
      $(".editAttemptBtnCls").hide();

      // allow update failed renewal settings
      $('#attemptForm').find(':input,:radio,:checkbox').prop("disabled", false);
      $('.removeDiv').show();
      $('#attemptForm select').selectpicker('refresh');
    });


    $(document).off("click", "#addAttemptBtn");
    $(document).on("click", "#addAttemptBtn", function() {

      $existDiv = $(".failedDiv:visible").length;
      $existDiv = parseInt($existDiv) + 1;

      if ($existDiv <= 10) {
        $counter = $("#attemptCnt").val();
        $num = parseInt($counter) + 1;
        $("#attemptCnt").val($num);
        $num = "-" + $num;
        var html = $("#cloneAttemptDiv").html();
        html = html.replace(/~title~/g, $existDiv);
        html = html.replace(/~number~/g, $num);
        $("#loadAttemptDiv").append(html);

        $('#attemptForm .add_control_' + $num).addClass('form-control');
        $('#attemptForm .add_control_' + $num).selectpicker({
          container: 'body',
          style: 'btn-select',
          noneSelectedText: '',
          dropupAuto: false,
        });
        $("input[type='radio'], input[type='checkbox']").not('.js-switch').uniform();
      }
    });

    $(document).off("click", "#saveAttemptBtn");
    $(document).on("click", "#saveAttemptBtn", function() {
      saveAttemptSettings();
    });

    $(document).off("click", ".removeDiv");
    $(document).on("click", ".removeDiv", function() {
      var id = $(this).attr("data-id");
      $("#attemptDiv" + id).remove();
      $(".attemptTitle:visible").each(function(index, element) {
        index = index + 1;
        $(element).html("Failed Attempt " + index);
      });
    });
  });

  saveAttemptSettings = function() {
    $formId = $("#attemptForm");
    $action = "ajax_failed_renewal_settings.php";
    $('.error').html('');

    $.ajax({
      url: $action,
      type: "POST",
      dataType: "json",
      data: $formId.serialize(),
      beforeSend: function() {
        $("#ajax_loader").show();
      },
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == 'success') {
          setNotifySuccess(res.msg);
          $(".addSaveAttemptBtnCls").hide();
          $(".editAttemptBtnCls").show();
          parent.load_failed_renewal_settings();
        } else {
          var is_error = true;
          $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
            if (is_error) {
              var offset = $('#error_' + index).offset();
              if (typeof(offset) != "undefined") {
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 195;
                $('body,html').animate({
                  scrollTop: totalScroll
                }, 1200);
                is_error = false;
              }
            }
          });

          if (typeof(res.msg) != "undefined") {
            setNotifyError(res.msg);
          }
        }
      }
    });
  };
</script>
