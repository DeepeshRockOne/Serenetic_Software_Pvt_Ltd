<div class="panel panel-default" style="min-height: 400px !important;">
    <div class="panel-heading">
        <h4 class="mn">Error Log - <span class="fw300"><?=$file_row['import_type']?> (<?=date('m/d/Y',strtotime($file_row['created_at']))?>)</span></h4>
    </div>
    <div class="panel-body ">
        <div class="table-responsive">
            <table class="<?= $table_class ?>">
                <thead>
                <tr class="data-head">
                    <?php if(!empty($fetch_rows) && $total_rows > 0) {?>
                        <?php $csv_field_arr = json_decode($fetch_rows[0]['csv_columns'],true);
                            $totalField = count($csv_field_arr); ?>
                            <th style="min-width: 210px;">Fail Reason</th>
                            <?php foreach($csv_field_arr as $key => $value){ ?>
                            <?php if(!in_array($key, array('products' ,'dependents'))) { ?>
                                <th><?= $key ?></th>
                        <?php }}
                        ?>
                    <?php }else echo "No Record Found!"; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($fetch_rows) > 0) {
                    foreach ($fetch_rows as $rows) { ?>
                        <tr>
                                <td><?=$rows['reason']?></td>
                            <?php $csv_value_arr = json_decode($rows['csv_columns'],true); 
                                foreach($csv_value_arr as $key => $value){ ?>
                                    <?php if(!in_array($key, array('products' ,'dependents'))) { ?>
                                        <td width="auto"><?= $value ?></td>
                                    <?php } ?>
                            <?php }
                            ?>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="<?=$totalField?>" class="text-center">No Error Log Found</td>
                    </tr>
                <?php } ?>
                </tbody>
                <?php if ($total_rows > 0) { ?>
                    <tfoot>
                    <tr>
                        <td colspan="<?=$totalField?>">
                            <?php /*echo str_replace('Showing','',$paginate->links_html);*/ ?>
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                    </tfoot>
                <?php } ?>
            </table>
            <?php if (count($fetch_rows) > 0) { ?>
            <div class="text-center">
                <a href="csv_error_log_member.php?id=<?= $file_id ?>&export=export_excel" class="red-link"><i class="fa fa-download"></i> Export</a>
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