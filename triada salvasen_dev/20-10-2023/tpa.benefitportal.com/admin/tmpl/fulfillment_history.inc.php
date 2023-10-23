<?php if ($is_ajaxed) { ?>
  <div class="panel panel-default panel-block">
  <div class="panel-body">
    <h4 class="m-t-0">Fulfillment History </h4>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>File Name</th>
            <th class="text-center">Processed Files</th>
            <th  width="40%">Last Processed</th>
            <th width="70px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($total_rows > 0) {
            foreach ($fetch_rows as $rows) { ?>
          <tr>
            <td><?=$rows['file_name']?></td>
            <td class="text-center"><a href="fulfillment_processed_file.php?id=<?=$rows['id']?>" class="fw500 text-action"><?=$rows['total_files']?></a></td>
            <td><?=strtotime($rows['last_processed']) > 0 ? date('m/d/Y h:i A',strtotime($rows['last_processed'])) . " EST" : "-" ?></td>
            <td class="icons text-center">
              <a href="fulfillment_processed_file.php?id=<?=$rows['id']?>" data-toggle="tooltip" data-trigger="hover" title="Download"><i class="fa fa-list"></i></a>
            </td>
          </tr>
          <?php } ?>
          <?php }else{ ?>
            <tr><td colspan="5">No Records.</td></tr>
          <?php } ?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                    <td colspan="9">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php } ?>
      </table>
    </div>
  </div>
</div>
<?php }else{ ?>

<div class="panel panel-default panel-block panel-title-block advance_info_div">
  <div class="panel-body ">
      <div class="phone-control-wrap ">
        <div class="phone-addon w-90">
          <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="70px">
        </div>
        <div class="phone-addon text-left lato_font">All manual files, once requested, can be found on the Export Requests page - click the button below:
          <div class="clearfix m-t-15">
            <a href="fulfillment_export_requests.php" class="btn btn-info">Export Requests</a>
          </div>
        </div>
    </div>
  </div>
</div>

<form id="frm_search" action="fulfillment_history.php" method="GET" class="sform">
  <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
  <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
  <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
  <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
</form>
<div class="panel panel-default panel-block">
  <div class="list-group">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_data" class=""> </div>
  </div>
</div>  

<script type="text/javascript">
$(document).ready(function(){
  dropdown_pagination('ajax_data')
  ajax_submit();
  $(document).on("click", ".search_btn", function(e) {
    e.preventDefault();
    $(this).hide();
    $("#search_div").css('display', 'inline-block');
  });
  $(document).on("click", ".search_close_btn", function(e) {
    e.preventDefault();
    $("#search_div").hide();
    $(".search_btn").show();
  });
$(document).off('click', '#ajax_data ul.pagination li a');
    $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
      e.preventDefault();
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          common_select();
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
});

});
function ajax_submit() {
  $('#ajax_loader').show();
  $('#ajax_data').hide();
  $('#is_ajaxed').val('1');
  var params = $('#frm_search').serialize();
  $.ajax({
    url: $('#frm_search').attr('action'),
    type: 'GET',
    data: params,
    success: function(res) {
      $('#is_ajaxed').val('');
      $('#ajax_loader').hide();
      $('#ajax_data').html(res).show();
      common_select();
      $('[data-toggle="tooltip"]').tooltip();
    }
  });
  return false;
}
</script>
<?php } ?>