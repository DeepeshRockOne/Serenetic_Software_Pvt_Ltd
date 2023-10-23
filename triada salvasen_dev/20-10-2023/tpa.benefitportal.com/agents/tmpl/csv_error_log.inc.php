<div class="panel panel-default" style="min-height: 400px !important;">
    <div class="panel-heading">
        <h4 class="mn">Error Log - <span class="fw300"><?=$file_row['lead_tag']?> (<?=date('m/d/Y',strtotime($file_row['created_at']))?>)</span></h4>
    </div>
    <div class="panel-body ">
        <div class="table-responsive">
            <table class="<?= $table_class ?>">
                <thead>
                <tr class="data-head">
                    <th>Fail Reason</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($fetch_rows) > 0) {
                    foreach ($fetch_rows as $rows) { ?>
                        <tr>
                            <td><?= is_value_empty($rows['reason']) ?></td>
                            <td><?= is_value_empty($rows['fname']) ?></td>
                            <td><?= is_value_empty($rows['lname']) ?></td>
                            <td><?= is_value_empty($rows['cell_phone']) ?></td>
                            <td><?= is_value_empty($rows['email']) ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="79" class="text-center">No Error Log Found</td>
                    </tr>
                <?php } ?>
                </tbody>
                <?php if ($total_rows > 0) { ?>
                    <tfoot>
                    <tr>
                        <td colspan="79">
                            <?php /*echo str_replace('Showing','',$paginate->links_html);*/ ?>
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                    </tfoot>
                <?php } ?>
            </table>
            <?php if (count($fetch_rows) > 0) { ?>
            <div class="text-center">
                <a href="csv_error_log.php?id=<?= $agent_csv_id ?>&export=export_excel" class="red-link"><i class="fa fa-download"></i> Export</a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>

  $(document).ready(function(){
    common_select()
  });
      $(document).off('change', '.pagination_select');
    $(document).on('change', '.pagination_select', function(e) {
        e.preventDefault();
        $('panel-body').html('');
        var page_url = $(this).find('option:selected').attr('data-page_url');
        window.location.href=page_url
        common_select();
    });
  </script>