<?php if($is_ajaxed) { ?>
<div class="table-responsive">
    <table class="<?=$table_class?>">
        <thead>
        <tr>
            <th>Date Range</th>
            <th>Account Type</th>
            <th>Entity Name</th>
            <!-- <th>Status</th> -->
            <th>Routing Number</th>
            <th>Account Number</th>
            <th width="70px">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if($totalRecords > 0 ) {
                foreach($fetchRecords as $rows) {
            ?>
        <tr>
            <td><?=getCustomDate($rows['effective_date'])?> - <?=getCustomDate($rows['termination_date']) != '-' ? getCustomDate($rows['termination_date']) : 'Present' ?></td>
            <td><?=ucfirst($rows['account_type'])?></td>
            <td><?=$rows['bank_name']?></td>
            <!-- <td><?=$rows['status']?></td> -->
            <td><?=$rows['routing_number']?></td>
            <td>*<?=substr($rows['account_number'],-4)?></td>
            <td class="icons text-right"><a href="javascript:void(0)" data-href="direct_deposit_account.php?id=<?=$rows['id']?>&agent_id=<?=$agent_id?>" class="direct_deposit_account"><i class="fa fa-eye "></i></a></td>
        </tr>
        <?php } }else{ ?>
            <tr>
                <td colspan="6"> No Rows Found!</td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
            <tr>
            <?php if($totalRecords > 0 && !empty($fetchRecords)) { ?>
                <td colspan="6">
                <?php echo $paginate_records->links_html; ?>
                </td>
            <?php } ?>
            </tr>
        </tfoot>
    </table>
</div>
<?php } ?>