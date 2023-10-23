<?php if ($is_ajaxed) { ?>
    <?php if ($total_rows > 0) { ?>
        <div class="clearfix m-b-20">
            <div class="pull-right">
                <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                    <div class="form-group mn">
                        <label for="user_type">Records Per Page </label>
                    </div>
                    <div class="form-group mn">
                        <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);pendingHRMPaymentData();">
                            <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="table-responsive hrm_summary">
        <table class="<?= $table_class ?>">
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Payment Duration</th>
                    <th>Total</th>
                    <th>Pay Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) {
                    foreach ($fetch_rows as $rows) { 
                        $currentPayPeriod = displayDate($rows['start_coverage_date']) .' - '. displayDate($rows['end_coverage_date']);
                        ?>
                        <tr>
                            <td><?= $currentPayPeriod ?></td>
                            <td><?= $rows['hrm_payment_duration'] ?></td>
                            <td><?= dispCommAmt($rows['totalAmount']) ?></td>
                            <td><?= displayDate($rows['pay_date']) ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                    <tr>
                        <td colspan="6"><?php echo $paginate->links_html; ?></td>
                    </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
    <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
    <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
<?php } ?>


<script type="text/javascript">
    $(document).ready(function() {

    });
</script>