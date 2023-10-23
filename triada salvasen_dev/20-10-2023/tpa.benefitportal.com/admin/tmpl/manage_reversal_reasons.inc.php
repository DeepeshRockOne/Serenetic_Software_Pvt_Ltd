<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="fs18 mn"><?=!empty($id) ? "Edit" : "+"?> Reversal Reasons</h4>
    </div>
  </div>
  <div class="panel-body">
    <form class="theme-form" id="reasonsForm">
      <input type="hidden" class="form-control" name="id" value="<?=!empty($id) ? $id : ''?>">
      <div class="row">
        <div class="form-group height_auto mn">
          <input type="text" class="form-control" name="type" value="<?=!empty($type) ? $type : ''?>">
          <label>Type</label>
          <p class="error" id="error_type"></p>
        </div>
      </div>
      <div class="clearfix m-t-20 text-center">
        <button class="btn btn-action" type="button" id="saveReasons">Save</button>
        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
      </div>
    </form>
  </div>
</div>


<script type="text/javascript">
$(document).on('keypress',function(e) {
    if(e.which == 13) {
      $("#saveReasons").trigger('click');      
    }
});  
$(document).off("click", "#saveReasons");
$(document).on("click", "#saveReasons", function() {

  $formId = $("#reasonsForm");
  $action = "ajax_manage_reversal_reasons.php";
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
        parent.$.colorbox.close();
        window.parent.setNotifySuccess(res.msg);
        parent.load_reversal_reason_settings();
      } else {
        $.each(res.errors, function(index, error) {
          $('#error_' + index).html(error);
          var offset = $('#error_' + index).offset();
          if (typeof(offset) === "undefined") {
            console.log("Not found : " + index);
          } else {
            var offsetTop = offset.top;
            var totalScroll = offsetTop - 195;
            $('body,html').animate({
              scrollTop: totalScroll
            }, 1200);
          }
        });
      }
    }
  });
});
</script>
