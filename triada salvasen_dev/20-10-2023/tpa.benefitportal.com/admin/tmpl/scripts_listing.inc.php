<div class="white-box">
  <div class="clearfix m-b-15 tbl_filter">
    <div class="pull-left">
        <h4 class="m-t-7">Scripts</h4>
    </div>
      <form class="theme-form" id="scriptForm" action="scripts_listing.php">
        <input type="hidden" id="is_ajaxed" name="is_ajaxed" value="">
     <div class="pull-right">
    <?php if ($total_rows > 0) {?>
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                <div class="form-group height_auto mn">
                    <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group height_auto mn">
                    <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);scriptListing();">
                        <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                </div>
            </div>
              <?php } ?>
        </div>

  </div>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr>
          <th>Script Type</th>
          <th>Last Processed</th>
          <th>Next Processed</th>
          <th width="100px">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= $rows['script_type']; ?></td>
                        <td><?= !empty($rows['last_processed']) ? date("m/d/Y h:i A",strtotime($rows['last_processed'])).' CST ' : '-'?></td>
                        <td><?= !empty($rows['next_processed']) ? date("m/d/Y h:i A",strtotime($rows['next_processed'])).' CST ' : '-'?></td>
                        <td><?= $rows['status']; ?></td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="4" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
      </tbody>
      <?php if ($total_rows > 0) {?>
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
</div>

<script type="text/javascript">
$(document).ready(function() {
    $(document).off('click', '#scriptsDiv ul.pagination li a');
    $(document).on('click', '#scriptsDiv ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#scriptsDiv').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#scriptsDiv').html(res).show();
                common_select();
            }
        });
    });

    scriptListing = function() {
        $('#ajax_loader').show();
        $('#is_ajaxed').val('1');
        var params = $('#scriptForm').serialize();
        $.ajax({
            url: $('#scriptForm').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#scriptsDiv').html(res).show();
                common_select();
            }
        });
        return false;
    }
});
</script>