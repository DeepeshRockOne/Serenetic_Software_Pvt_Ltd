<?php if ($is_ajaxed) { ?>
    <div class="clearfix m-b-15">
        <div class="pull-left">
            <label class="mn"><i class="fa fa-square fa-lg text-warning"></i>&nbsp; Pending </label>
            <label class="mn"><i class="fa fa-square fa-lg text-action m-l-10"></i>&nbsp; Cancelled </label> 
        </div>
        <div class="pull-right">
            <strong>Next Import : </strong> <i class="text-light-gray"><?=$next_import_time;?></i>
        </div>
    </div>
    <div class="table-responsive">
        <table class="<?= $table_class ?>">
            <thead>
            <tr class="data-head">
                <th>Added Date</th>
                <th>Tag</th>
                <th>Total Leads</th>
                <th>Imported Leads</th>
                <th>Error(s)</th>
                <th width="90px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) {
                        $tmp_class = '';
                        if($rows['status'] == 'Pending') {
                            $tmp_class = 'text-warning';
                        } elseif($rows['status'] == 'Cancel') {
                            $tmp_class = 'text-action';                            
                        }
                    ?>
                    <tr class="<?=$tmp_class;?>">
                        <td><?= $tz->getDate($rows['created_at'], 'n/j/Y @ g:i a') ?></td>
                        <td><?= $rows['lead_tag'] ?></td>
                        <td><?= $rows['total_leads'] ?></td>
                        <td><?= $rows['import_leads'] ?></td>
                        <td><?= $rows['existing_leads'] ?></td>
                        <td class="icons text-right">
                            <?php if ($rows['status'] == 'Pending') { ?>                            
                            <a href="javascript:void(0);" data-id="<?= md5($rows['id']) ?>" class="btn_cancel_import" data-toggle="tooltip" title="Cancel Import"><i class="fa fa-times-circle-o"></i></a>
                            <?php } ?>

                            <?php if ($rows['is_report_send'] == 'Y') { ?>
                                <a href="csv_error_log.php?id=<?= md5($rows['id']) ?>" class="color_box_popup cboxElement" data-toggle="tooltip" title="View Error Log"><i class="fa fa-exclamation-triangle"></i></a>
                            <?php } ?>

                            <?php if ($rows['status'] != 'Pending') { ?>                            
                                <a href="javascript:void(0);" data-id="<?= md5($rows['id']) ?>" class="btn_delete_import" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.color_box_popup').colorbox({iframe: true, width: '950px', height: '500px'});
    });
</script>