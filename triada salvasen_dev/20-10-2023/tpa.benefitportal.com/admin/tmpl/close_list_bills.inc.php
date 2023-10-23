<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr>
          <th>Payment Date</th>
          <th>List Bill # </th>
          <th>Group Name/ID</th>
          <th>Company Name</th>
          <th>Payment Method <br/> Transaction #</th>
          <th>Amount</th>
          <th >Status</th>
          <th class="text-left" width="130px">Actions</th>
       </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= (!empty($rows['payment_date'])) ? date('m/d/Y',strtotime($rows['payment_date'])) : ' - '; ?></td>
                        <td><a href="view_listbill_statement.php?list_bill=<?= $rows['secured'] ?>" data-id="<?= $rows['secured']?>" target="_blank" class="text-action"><strong><?= $rows['list_bill_no'] ?></strong></a></td>
                        <td><?= $rows['business_name'] ?><br><a href="javascript:void(0);" class="text-action"><strong><?= $rows['rep_id'] ?></strong></a></td>
                        <td><?= !empty($rows['group_company_name']) ? $rows['group_company_name']: ' - ' ?></td>
                        <td><?= !empty($rows['payment_type']) ? $rows['payment_type'].' *'.$rows['last_cc_ach_no'] : ' - ' ?><br/><?= !empty($rows['transaction_id']) ? $rows['transaction_id']: '' ?></td>
                        <td><strong><?= displayAmount2($rows['grand_total'],2) ?></strong></td>
                        <td><?= ucfirst($rows['status']) ?></td>
                        <td>
                             <div class="theme-form pr w-130">
                                <select class="form-control listbill_action">
                                  <option value=""></option>
                                  <option value="View" data-href="view_listbill_statement.php?list_bill=<?= $rows['secured'] ?>" data-id="<?= $rows['secured']?>">View</option>
                                  <?php if(!empty($rows['order_id'])) { ?>
                                    <option value="Receipt" data-href="payment_receipt.php?order_id=<?= md5($rows['order_id']) ?>" data-id="<?= $rows['secured']?>">Receipt</option>
                                  <?php } ?>
                                  <?php /*<option value="download" data-href="<?=$HOST?>/view_listbill_statement.php?list_bill=<?= $rows['secured']?>&action_type=export_excel" data-id="<?= $rows['secured']?>">Download CSV</option> */ ?>
                                  <option value="download" data-href="<?=$HOST?>/view_listbill_statement.php?list_bill=<?= $rows['secured']?>&action_type=pdf" data-id="<?= $rows['secured']?>">Download</option>
                                </select>
                                <label>Select</label>
                             </div>
                        </td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="8" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
        <?php 
        if ($total_rows > 0) {?>
            <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php }?>
    </table>
</div>

<script type="text/javascript">
    $(document).off('click', '#close_list_bills_div ul.pagination li a');
    $(document).on('click', '#close_list_bills_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#close_list_bills_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#close_list_bills_div').html(res).show();
                common_select();
            }
        });
    });
</script>