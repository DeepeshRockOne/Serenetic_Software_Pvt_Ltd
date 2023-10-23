<div class="panel panel-default">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">Debit Balance - <span class="fw300"><?=$agentName?></span></h4>
      </div>
   </div>
   <div class="panel-body">
      <p>Debit Balance</p>
      <div class="bg_light_gray p-15 text-center fw500">
         <?=dispCommAmt($debitBalance)?>
      </div>
      <div class="clearfix m-t-25 text-center">
         <a href="javascript:void(0);" class="btn btn-action" id="btn_export">Export</a>
         <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function() {
      $(document).off('click', '#btn_export');
      $(document).on('click', '#btn_export', function(e) {
          parent.confirm_export_data(function() {
              $('#ajax_loader').show();
              var params = {'action':'export_debit_balance'};
              $.ajax({
                  url: 'commissions_debit_balance.php',
                  type: 'GET',
                  data: params,
                  dataType: 'json',
                  success: function(res) {
                      $('#ajax_loader').hide();
                      $("#export").val('');
                      if(res.status == "success") {
                          confirm_view_export_request(true,'agent');
                      } else {
                          setNotifyError(res.message);
                      }
                  }
              });
          });
      });
   });
</script>