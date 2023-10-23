<?php if (!empty($_GET["is_ajax"])) { ?>
  <div class="clearfix"></div>
  <div id="report_data">
    <div class="white-box p-b-0">
      <div class="clearfix tbl_filter">
        <div class="pull-left">
          <h4 class="m-t-7">Queue</h4>
        </div>
        <div class="pull-right">
          <div class="m-b-15">
            <a href="add_sms_broadcast.php" class="btn btn-action" >+ Broadcaster</a>
          </div>
        </div>
      </div>
       <iframe onload="$('#ajex_loader').show();" id="br_queue_iframe" src="<?= $HOST; ?>/admin/sms_broadcaster_detail_page.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%" scrolling="no"></iframe>
    </div>
    <div class="white-box p-b-0">
      <iframe onload="$('#ajex_loader').show();" id="br_history_iframe" src="<?= $HOST; ?>/admin/sms_broadcaster_history_page.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%" scrolling="no"></iframe>
    </div>
  </div>
  <div class="clearfix"></div>
<?php } else { ?>

  <div id="member_access">
    <div class="row">
      <div class="col-md-8">
        <form id="search_from" role="form" action="" name="search_from" enctype="multipart/form-data">
          <input type="hidden" name="is_ajax" id="is_ajax" value="1"/>
        </form>
      </div> 
    </div>
    <div class="outputData"></div>  
  </div>

    <script type="text/javascript">
    $(document).ready(function () {
      getSearchDetails();
      var counter = 0;
    });

    function getSearchDetails() {
      var params = $('#search_from').serialize();
      $.ajax({
        url: "sms_broadcaster.php",
        method: "GET",
        data: params,
        beforeSend: function () {
          $("#ajax_loader").show();
        },
        success: function (res) {
          $("#ajax_loader").hide();
          $("#member_access .outputData").html(res);
        }
      });
    }
  </script>
<?php } ?>

<script type="text/javascript">
  resizeIframe = function ($height, $frm_name) {
    $dropDownheight = 55;
    $totalHeight = $dropDownheight + $height;
    $("#"+$frm_name)[0].style.height = $totalHeight + 'px';
  };
  function parent_window_colorbox(params) {
    $.colorbox(params);
  }

  function delete_sms_broadcaster(id){
    swal({
        text: '<br>Delete Record: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm'
    }).then(function() {
      window.location = "sms_broadcaster.php?bro_id=" + id + "&action=delete";
    }, function(dismiss) {
    });
  }


  function change_status_sms_broadcaster(id, val){
    swal({
      text: '<br>Change Status: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
    }).then(function() {
      window.location = "sms_broadcaster.php?bro_id=" + id + "&status=" + val +"&action=status";
    }, function(dismiss) {
      window.location.reload();
    });
  }

  function redirect_page(url) {
    window.location.href = url;
  }
</script>


