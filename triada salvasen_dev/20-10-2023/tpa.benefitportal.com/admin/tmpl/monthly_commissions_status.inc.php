<?php if (!empty($_GET["is_ajax"])) { ?>
  <div class="clearfix"></div>
    <div class="white-box">
       <iframe onload="$('#ajex_loader').show();" id="comm_pending_iframe" src="<?= $HOST; ?>/admin/monthly_pending_commission.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%" scrolling="no"></iframe>
    </div>
    <div class="white-box">
      <iframe onload="$('#ajex_loader').show();" id="comm_completed_iframe" src="<?= $HOST; ?>/admin/monthly_completed_commission.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%" scrolling="no"></iframe>
    </div>
  <div class="clearfix"></div>
<?php } else { ?>
  <div id="commData">
    <div class="row">
      <div class="col-md-8">
        <form id="commForm" role="form" action="" name="commForm" enctype="multipart/form-data">
          <input type="hidden" name="is_ajax" id="is_ajax" value="1"/>
          <input type="hidden" name="pay_period" value="<?=$pay_period?>"/>
          <?php if(!empty($agent_id)){ ?>
            <input type="hidden" name="agent_id" value="<?=$agent_id?>"/>
          <?php } ?>
        </form>
      </div> 
    </div>
    <div class="outputData"></div>  
  </div>
  
  <script type="text/javascript">
    $(document).ready(function () {
      getCommissionDetails();
      var counter = 0;
    });

    function getCommissionDetails() {
      var params = $('#commForm').serialize();
      $.ajax({
        url: "monthly_commissions_status.php",
        method: "GET",
        data: params,
        beforeSend: function () {
          $("#ajax_loader").show();
        },
        success: function (res) {
          $("#ajax_loader").hide();
          $("#commData .outputData").html(res);
        }
      });
    }
  </script>
<?php } ?>



<script type="text/javascript">
  resizeIframe = function ($height, $frm_name) {
    $dropDownheight = 70;
    $totalHeight = $dropDownheight + $height;
    $("#"+$frm_name)[0].style.height = $totalHeight + 'px';
  };
  function parent_window_colorbox(params) {
    $.colorbox(params);
  }
  function redirect_page(url) {
    window.location.href = url;
  }
</script>