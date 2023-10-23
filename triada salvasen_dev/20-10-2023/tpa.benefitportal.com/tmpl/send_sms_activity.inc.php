<?php if ($popup_is_ajaxed) { ?>
<div class="table-responsive">
  <table class="<?=$table_class?>">
    <thead>
      <tr>
        <th>Activity Date</th>
        <th>Status</th>
        <th>Reason</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) { ?>
        <?php foreach ($fetch_rows as $rows) { ?>
          <tr>
            <td><?php echo date('m/d/Y', strtotime($rows['created_at'])); ?></td>
            <td><?php echo ucwords($rows['status']) ?></td>
            <td><?= ((!empty($rows['code']) && !empty($rows["error_message"])) ? 'Error Code ('.$rows["code"].') '.$rows["error_message"] : '-');?></td>
          </tr>
        <?php } ?>
      <?php } else { ?>
        <tr>
          <td colspan="4">No record(s) found</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<div class="text-center">
  <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
</div>
<?php } else{ ?>
<div class="panel panel-default panel-block">
  <form id="popup_frm_search" action="send_sms_activity.php" method="GET">
    <div class="panel-heading">
      <div class="panel-title">
        <h4 class="mn">
           <strong class="fw500">SMS Activity - <span class="fw300"><?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $phone) ?></span></strong>
        </h4>
      </div>
    </div>
    <div class="panel-footer clearfix" style="display: none;"> 
      <input type="hidden" name="popup_is_ajaxed" id="popup_is_ajaxed" value="1" />
      <input type="hidden" name="log_id" id="id" value="<?= $log_id ?>" />
      <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
      <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
    </div>
  </form>
</div>
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_data"> </div>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function() { 
      popup_ajax_submit();
    });


    function popup_ajax_submit() {
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#popup_is_ajaxed').val('1');
      var params = $('#popup_frm_search').serialize();
      $.ajax({
        url: $('#popup_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          common_select();
        }
      });
      return false;
    }
</script>
<?php }?>