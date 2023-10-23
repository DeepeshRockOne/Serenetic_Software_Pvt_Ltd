<div class="white-box">
    <div class="clearfix tbl_filter">
        <div class="pull-left">
            <h4 class="">Monthly Commissions</h4>
        </div>
       <?php if ($total_rows > 0) { ?>
        <div class="pull-right">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn height_auto">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn height_auto">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);monthlyCommissionStatement();">
                <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
              </select>
            </div>
          </div>
        </div>
    <?php } ?>
    </div>
    <div class="table-responsive">
        <table class="<?=$table_class?> text-nowrap">
            <thead> 
                <tr>
                  <th width="12%">Period</th>
                  <th class="text-center" width="12%">Payees</th>
                  <th>Earned</th>
                  <th>Advanced</th>
                  <th>PMPM</th>
                  <th>Reversals</th>
                  <th>Fees</th>
                  <th>Adjustments</th>
                  <th width="7%">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) {

                    foreach ($fetch_rows as $rows) { 
                        $startPayPeriod=date('m/01/Y', strtotime($rows['payPeriod']));
                        $endPayPeriod=date('m/d/Y', strtotime($rows['payPeriod']));
                        ?>
                        <tr>
                            <td class="text-nowrap"><a href="monthly_commissions_status.php?pay_period=<?=$rows['payPeriod']?><?php if(!empty($agent_id)){ ?>&agent_id=<?=$agent_id?><?php } ?>" class="fw500 text-action"><?= $startPayPeriod.' - '.$endPayPeriod; ?></a></td>
                            <td class="text-center"><?= $rows['totalPayee']; ?></td>
                            <td><?=dispCommAmt($rows['earnedComm'])?></td>
                            <td><?=dispCommAmt($rows['advanceComm'])?></td>
                            <td><?=dispCommAmt($rows['pmpmComm'])?></td>
                            <td><?=dispCommAmt($rows['reverseComm'])?></td>
                            <td><?=dispCommAmt($rows['feeComm'])?></td>
                            <td><?=dispCommAmt($rows['adjustComm'])?></td>
                            <td><?=dispCommAmt($rows['totalComm'])?></td>
                        </tr>
                    <?php }?>
                <?php } else {?>
                    <tr>
                        <td colspan="9" class="text-center">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php 
            if ($total_rows > 0) {?>
                <tfoot>
                    <tr>
                        <td colspan="9">
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                </tfoot>
            <?php }?>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).off('click', '#monthlyCommissionDiv ul.pagination li a');
    $(document).on('click', '#monthlyCommissionDiv ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#monthlyCommissionDiv').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#monthlyCommissionDiv').html(res).show();
                common_select();
            }
        });
    });
</script>