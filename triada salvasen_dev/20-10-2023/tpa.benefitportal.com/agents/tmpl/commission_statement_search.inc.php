<div class="table-responsive">
    <form id="frm_Search" action="commission_statement_search.php" method="GET">
        <table class="<?=$table_class?> com_tbl">
            <thead>
            <tr class="data-head">
                
                <th><a href="javascript:void(0);">Order #<br>Date</a></th>
                <th><a href="javascript:void(0);">Period</a></th>
                <th><a href="javascript:void(0);">Member</a></th>
                <th><a href="javascript:void(0);">Product</a></th>
                <th class="text-right"><a href="javascript:void(0);">Price</a></th>
                <th class="text-right"><a href="javascript:void(0);">Earned <br>Comm.</a></th>
                <th class="text-right"><a href="javascript:void(0);">Advanced <br>Comm.</a></th>
                <th class="text-right" ><a href="javascript:void(0);">PMPM</a></th>
                <th ><a href="javascript:void(0);">Order Type</a></th>
                <th><a href="javascript:void(0);">Order Status</a></th>
                <th class="text-right"><a href="javascript:void(0);">Reversed <br>Comm.</a></th>
                <th class="text-right"><a href="javascript:void(0);">Total <br>Comm.</a></th>

            </tr>
            </thead>
            <tbody>
            <?php 
                if ($total_rows > 0){
                    foreach ($fetch_rows as $rows) { 

                        if($rows['commission_duration']=="weekly") { 
                            $startPayPeriod=date('Y-m-d', strtotime('-6 days', strtotime($rows['pay_period'])));;
                            $endPayPeriod=date('m/d/Y', strtotime($rows['pay_period']));
                        }else{
                            $startPayPeriod=date('Y-m-01', strtotime($rows['pay_period']));;
                            $endPayPeriod=date('m/d/Y', strtotime($rows['pay_period']));;
                        }

                        $rowClass = '';
                        if(in_array($rows['orderStatus'],array("Chargeback","Void","Refund","Payment Returned"))){
                            $rowClass = "text-action";
                        }
            ?>
                        <tr class="<?=$rowClass?>">
                            <td><?=$rows['orderDispId']?> <br> <?=getCustomDate($rows['orderDate'])?></td>
                            <td><?php echo getCustomDate($startPayPeriod) . " - " . getCustomDate($endPayPeriod); ?></td>
                            <td><?php echo stripslashes($rows['memberName']) . "<br/>(" . $rows['memberRepId'] . ")"; ?></td>
                            <td><?= $rows['prdName']?></td>
                            <td><?= displayAmount($rows['prdPrice']) ?></td>
                            <td><?=dispCommAmt($rows['earnedComm'])?></td>
                            <td><?=dispCommAmt($rows['advanceComm'])?></td>
                            <td><?=dispCommAmt($rows['pmpmComm'])?></td>
                            <td><?=$rows['saleType']?></td>
                            <td><?=$rows['orderStatus']?></td>
                            <td><?=dispCommAmt($rows['reverseComm'])?></td>
                            <td><?=dispCommAmt($rows['totalComm'])?></td>
                        </tr>
                    <?php }?>
                <?php } else {?>
                    <tr>
                        <td colspan="12" class="text-center">No record(s) found</td>
                    </tr>
                <?php }?>
            </tbody>
            <?php 
            if ($total_rows > 0) {?>
                <tfoot>
                    <tr>
                        <td colspan="12">
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                </tfoot>
            <?php }?>
        </table>
    </form>
</div>

<script type="text/javascript">
    $(document).off('click', '#searchInStatementDivHtml ul.pagination li a');
    $(document).on('click', '#searchInStatementDivHtml ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#searchInStatementDivHtml').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#searchInStatementDivHtml').html(res).show();
                $('.detail_popup').colorbox({iframe:true, width: '1050px', height: '90%'});
                common_select();
            }
        });
    });
</script>