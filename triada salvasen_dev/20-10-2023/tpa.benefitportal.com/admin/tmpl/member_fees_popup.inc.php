<?php if ($popup_is_ajaxed) { ?>
<div class="table-responsive">
  <table class="<?=$table_class?>">
    <thead>
      <tr>
        <th>Member ID</th>
        <th>Member Name</th>
        <th class="text-center">Active Members Per ID</th>
        <th width="100px">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) { ?>
        <?php foreach ($fetch_rows as $rows) { ?>
          <tr>
            <td><?=$value['rep_id']?></td>
            <td><?=$value['customer_name']?></td>
            <td class="text-center"><?=$value['members_per_id']?></td>
            <td><?=$value['status']?></td>
          </tr>
        <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="4" class="text-center">No record(s) found</td>
      </tr>
      <?php } ?>
    </tbody>
    <?php if ($total_rows > 0) { ?>
      <tfoot>
        <tr>
          <td colspan="4">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php }?>
  </table>
</div>
<div class="text-center m-t-20">
  <a href="javascript:void(0);" name="export" id="export" class="btn btn-action">Export</a>
  <a href="javascript:void(0);" class="btn red-link m-l-15" onclick='parent.$.colorbox.close(); return false;'>Close</a>
</div>
<?php } else{ ?>
<div class="panel panel-default panel-block">
  
</div>
<div class="panel panel-default panel-block popup-height">
  <form id="popup_frm_search" action="member_fees_popup.php" method="GET">
    <div class="panel-heading">
      <div class="panel-title">
        <p class="fs18">
           <strong class="fw500"><?=$name . ' ('.$display_id .') - '?>
          </strong> <span class="fw300">(<?= $total_rows ?>) Products</span> 
        </p>
      </div>
    </div>
    <div class="panel-footer clearfix" style="display: none;">
      <input type="hidden" name="export" id="export" value=""/>
      <input type="hidden" name="popup_is_ajaxed" id="popup_is_ajaxed" value="1" />
      <input type="hidden" name="id" id="id" value="<?= $id ?>" />
      <input type="hidden" name="is_export" id="is_export" value="" />
      <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
      <input type="hidden" name="page" id="nav_page" value="" />
      <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
      <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
    </div>
  
  <div class="panel-body">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_data"> </div>
  </div>
  </form>
</div>
<script type="text/javascript">
  $(document).ready(function() {

    popup_ajax_submit();
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
      }
    });
  });
  function popup_ajax_submit() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#popup_is_ajaxed').val('1');
    $("#export").val('');
    var params = $('#popup_frm_search').serialize();
    var cpage = $('#nav_page').val();
    $.ajax({
      url: $('#popup_frm_search').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        common_select();
        var get_height = $('.popup-height').outerHeight() + 20;
    if(get_height < 450){
    console.log(get_height);
      parent.$.colorbox.resize({
        height: get_height+'px'
      });
}
      }
    });
    return false;
  }

  $(document).on('click', '#export', function() {
    swal({
      text: 'Export to Excel All Vendors: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      $('#is_ajaxed').val('0');
      $('#is_export').val('1');
      $('#frm_search').submit();
    }, function(dismiss) {
      window.location.reload();
    })
  });

</script>
<?php }?>