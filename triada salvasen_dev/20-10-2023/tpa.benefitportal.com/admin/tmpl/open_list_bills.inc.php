<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr>
          <th >Added Date</th>
          <th>List Bill #</th>
          <th>Group Name/ID</th>
          <th>Company Name</th>
          <th>Status</th>
          <th>Balance</th>
          <th class="text-center">Amendments</th>
          <th class="text-left" width="130px">Actions</th>
       </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= date('m/d/Y',strtotime($rows['list_bill_date'])); ?></td>
                        <td><a href="view_listbill_statement.php?list_bill=<?= $rows['secured'] ?>" class="text-action" target="_blank">
                          <strong><?= $rows['list_bill_no'] ?></strong></a></td>
                        <td><?= $rows['business_name'] ?><br><a href="javascript:void(0);" class="text-action"><strong><?= $rows['rep_id'] ?></strong></a></td>
                        <td><?= !empty($rows['group_company_name']) ? $rows['group_company_name']: ' - ' ?></td>
                        <td>Open</td>
                        <td><strong><?= displayAmount2($rows['grand_total'],2) ?></strong></td>
                        <td class="text-center"><?= displayAmount(abs($rows['amendment'])) ?></td>
                        <td>
                             <div class="theme-form pr w-130">
                                <select class="form-control listbill_action">
                                  <option value=""></option>
                                  <option value="View" data-href="view_listbill_statement.php?list_bill=<?= $rows['secured'] ?>" data-id="<?= $rows['secured']?>">View</option>
                                  <option value="Amend" data-href="add_amendment_listbill.php?list_bill=<?= $rows['secured'] ?>" data-id="<?= $rows['secured']?>">Amend</option>
                                  <?php if($rows['due_amount'] > 0) { ?>
                                    <option value="Pay" data-href="<?=$HOST?>/pay_bill.php?location=admin&list_bill_id=<?= $rows['secured']?>" data-id="<?= $rows['secured']?>">Pay</option>
                                  <?php } ?>
                                  <option value="void_select" data-id="<?= $rows['secured']?>" data-list-bill-no="<?= $rows['list_bill_no'] ?>" data-group-name="<?= $rows['business_name'] ?>">Void</option>
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
    $(document).off('click', '#open_list_bills_div ul.pagination li a');
    $(document).on('click', '#open_list_bills_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#open_list_bills_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#open_list_bills_div').html(res).show();
                common_select();
            }
        });
    });
</script>