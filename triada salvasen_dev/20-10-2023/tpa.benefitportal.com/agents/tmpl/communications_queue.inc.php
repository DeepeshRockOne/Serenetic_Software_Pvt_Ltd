
<?php if (!empty($_GET["is_ajax"])) { ?>
    <div class="clearfix"></div>
        <div class="container m-t-30">
            <div class="panel panel-default">
                <div class="panel-body p-b-0">
                   <div class="clearfix tbl_filter">
                     <div class="pull-left">
                        <h4 class="m-t-0">Drafts</h4>
                     </div>
                   </div>
                    <iframe onload="$('#ajex_loader').show();" id="br_queue_iframe" src="<?= $AGENT_HOST; ?>/draft_broadcast_agent.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%" scrolling="no"></iframe>
                </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-body p-b-0">
                <iframe onload="$('#ajex_loader').show();" id="br_history_iframe" src="<?= $AGENT_HOST; ?>/all_broadcast_agent.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%" scrolling="no"></iframe>
              </div>
            </div>
        </div>
    <div class="clearfix"></div>
<?php } else { ?>
          
    <div id="agent_access">
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
        url: "communications_queue.php",
        method: "GET",
        data: params,
        beforeSend: function () {
          $("#ajax_loader").show();
        },
        success: function (res) {
          $("#ajax_loader").hide();
          $("#agent_access .outputData").html(res);
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

  function delete_broadcaster(id){
    swal({
      text: 'Delete Record: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm'
    }).then(function() {
      window.location = "communications_queue.php?bro_id=" + id + "&action=delete";
    }, function(dismiss) {
    });
  }


  function change_status_broadcaster(id, val){
    swal({
      text: "Change Status: Are you sure?",
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      window.location = "communications_queue.php?bro_id=" + id + "&status=" + val +"&action=status";
    }, function(dismiss) {
      window.location.reload();
    });
  }

  function redirect_page(url) {
    window.location.href = url;
  }
</script>