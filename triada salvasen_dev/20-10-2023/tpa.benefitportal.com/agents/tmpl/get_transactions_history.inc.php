<?php if($is_transaction_ajaxed) { ?>
<div class="table-responsive">
    <table class="<?=$table_class?>">
    <thead>
        <th>ID/Added Date</th>
        <th>Enrolling Agent/ID</th>
        <th>Sale Type/Transaction ID</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Merchant</th>
        <th>Total</th>
        <th width="90px">Actions</th>
    </thead>
    <tbody>
    <?php if ($total_rows > 0) {?>
        <?php foreach ($fetch_rows as $rows) { 
            $billing_info=!empty($rows['billing_info'])?json_decode($rows['billing_info'],true):array();
            $amtTxtClass = "";
              if(in_array($rows['transaction_status'],array("Post Payment","Pending Settlement"))){
                $amtTxtClass = "text-warning";
              }else if(in_array($rows['transaction_status'],array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){
                $amtTxtClass = "text-action";
              }
            ?>
        <tr>
            <td>
                <a href="javascript:void(0);" data-href="transaction_receipt.php?transId=<?=$rows['t_id']?>" class="fw500 text-action transReceipt"><?=$rows['display_id']?></a><br><?=getCUstomDate($rows['created_at'])?>
            </td>
            <td>
                <a href="javascript:void(0);" class="fw500 text-action"><?=$rows['rep_id']?></a><br><?=$rows['fname'].' '.$rows['lname']?>
            </td>
            <td>
                <?=$rows['is_renewal'] == 'Y' ? 'Renewal' : 'New Business'?><br>
                <a href="javascript:void(0);" class="fw500 text-action"><?=$rows['transaction_id']?></a>
            </td>
            <td>
            <?php
            if(count($billing_info)>0){
                echo $billing_info['payment_mode'] == 'ACH' ? "ACH *".$billing_info['last_cc_ach_no'] : $billing_info['card_type']." *".$billing_info['last_cc_ach_no'];
            }else{
                echo "-";
            }
            ?>
            </td>
            <td class="<?=$amtTxtClass?>"><?=$rows['transaction_status']?></td>
            <td><?=$rows['merchant_name']?></td>
            <td class="<?=$amtTxtClass?>">
                <?php 
                  if(in_array($rows['transaction_status'], array("Refund","Void","Chargeback","Payment Returned"))){
                    echo "(".displayAmount($rows["transTotal"],2).")";
                  }else{
                    echo displayAmount($rows["transTotal"],2);
                  }
                ?>
            </td>
            <td class="icons">
                <a href="javascript:void(0);" data-href="transaction_receipt.php?transId=<?=$rows['t_id']?>" class="transReceipt" data-toggle="tooltip" data-placement="top" title="Send Receipt"><i class="fa fa-file-text"></i></a>
            </td>
        </tr>
    <?php } }else echo "<tr><td colspan='8'>No record Found!</td></tr>"; ?>
    </tbody>
    <?php if ($total_rows > 0) { ?>
    <tfoot>
        <tr>
            <td colspan="8">
                <?php echo $paginate->links_html; ?>
            </td>
        </tr>
    </tfoot>
    <?php } ?>
    </table>
</div>
<?php }else{?>
<div>
    <form action="get_transactions_history.php" name="transactions_history_form" id="transactions_history_form">
        <input type="hidden" name="customer_id" id="customer_id" value="<?=!empty($customer_id) ? $customer_id : '' ?>"/>
        <input type="hidden" name="is_transaction_ajaxed" id="is_transaction_ajaxed" value="1"/>
        <input type="hidden" name="pages" id="pages" value="<?=!empty($per_page) ? $per_page : '' ?>"/>
    </form>
    <div class="table-responsive" id="get_transaction_history_data">
    </div>
</div>
<script scr="text/javascript">
    $(document).ready(function(e){
        get_transactions_history();
        dropdown_pagination('get_transaction_history_data')
    });

    $(document).off('click', '#get_transaction_history_data ul.pagination li a');
    $(document).on('click', '#get_transaction_history_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#get_transaction_history_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#get_transaction_history_data').html(res).show();
                common_select();
            }
        });
    });

    function get_transactions_history(){
        var params = $('#transactions_history_form').serialize();
        $.ajax({
            url: $('#transactions_history_form').attr('action'),
            type: 'GET',
            data: params,
            beforeSend : function(e){
                $('#ajax_loader').show();
                $('#get_transaction_history_data').hide();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#get_transaction_history_data').html(res).show();
                common_select();
            }
        });
        return false;
    }
</script>
<?php } ?>