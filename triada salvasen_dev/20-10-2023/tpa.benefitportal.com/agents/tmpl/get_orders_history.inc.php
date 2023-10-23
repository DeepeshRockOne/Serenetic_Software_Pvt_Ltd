<?php if($is_order_ajaxed) { ?>
<table class="<?=$table_class?>">
    <thead>
        <th>ID/Added Date</th>
        <th>Enrolling Agent/ID</th>
        <th>Sale Type/Transaction ID</th>
        <?php /*<th>Payment</th> */ ?>
        <th>Status</th>
        <th>Total</th>
        <th>Actions</th>
    </thead>
    <tbody>
    <?php if ($total_rows > 0) {?>
        <?php foreach ($fetch_rows as $rows) {
            
            $attempt_order = $checkOrderChargeOptions = $regenerate_order = $allowedProcessed = $is_regenerate = $allowVoid = false;

            if(in_array($rows['status'],array("Pending Settlement"))){
                $allowVoid = is_ach_voidable($rows["id"]);
            }
            if(in_array($rows['status'], array("Cancelled", "Payment Declined","Refund", "Void","Payment Returned"))){
                $allowedProcessed = getAllowedProcessedMain($rows["id"]);
            }
            if(in_array($rows['status'], array("Refund", "Void","Payment Returned"))){
                $is_regenerate = true;
                $checkOrderChargeOptions = $enrollDate->checkOrderChargeOptions($rows['id'],$is_regenerate);
                $regenerate_order = check_order_can_regenerate_or_not($rows['id']);
            }
            if(in_array($rows['status'], array("Cancelled", "Payment Declined","Payment Returned"))){
                $attempt_order = check_order_can_attempt_again_or_not($rows['id']);
            }            
            ?>
            <tr>
                <td>
                    <a href="javascript:void(0);" class="fw500 text-action" onclick="Receipt('<?=$rows['order_id']?>')"><?=$rows['display_id']?></a><br><?=getCUstomDate($rows['created_at'])?>
                </td>
                <td>
                    <a href="javascript:void(0);" class="fw500 text-action"><?=$rows['rep_id']?></a><br><?=$rows['fname'].' '.$rows['lname']?>
                </td>
                <td>
                    <?=$rows['is_renewal'] == 'Y' ? 'Renewal' : 'New Business'?><br>
                    <a href="javascript:void(0);" class="fw500 text-action"><?=$rows['transaction_id']?></a>
                </td>
                <?php /*<td><?=$rows['order_payment_mode'] == 'ACH' ? 'ACH' : $rows['order_card_type']?> *<?=$rows['card_no']?></td>*/ ?>
                <td><?=$rows['status']?></td>
                <td><?=displayAmount($rows['grand_total'])?></td>
                <td class="w-200">
                    <div class="theme-form pr">
                    <select class="form-control order_action" data-id="<?=$rows['order_id']?>" id="order_action_<?=$rows['order_id']?>">
                        <option data-hidden="true"></option>
                        <option value="Receipt">Receipt</option>
                        <?php if($has_full_access == true && $reversal_access) { ?>
                            <?php if($rows['status'] == "Payment Approved"){ ?>
                              <option value="reverseOrder">Reverse Order</option>
                            <?php } ?>
                            <?php if($rows['status'] == "Pending Settlement" && $allowVoid){ ?>
                              <option value="reverseOrder">Reverse Order</option>
                            <?php } ?>
                            <?php if($is_regenerate && $checkOrderChargeOptions && $allowedProcessed && $regenerate_order){ ?>
                                <option value="Regenerate">Regenerate Order</option>
                            <?php } ?>
                            <?php if($rows['status'] == "Post Payment"){ ?>
                            <option value="Postdate">Edit Post Date</option>
                            <?php } ?>
                        <?php } ?>
                        <?php if($attempt_order && $allowedProcessed){ ?>
                            <option value="Reprocess">Reprocess Order</option>
                        <?php } ?>
                    </select>
                    <label>Select</label>
                    </div>
                </td>
            </tr>
    <?php } }else echo "<tr><td colspan='7'>No record Found!</td></tr>"; ?>
    </tbody>
    <?php if ($total_rows > 0) { ?>
    <tfoot>
        <tr>
            <td colspan="7">
                <?php echo $paginate->links_html; ?>
            </td>
        </tr>
    </tfoot>
    <?php } ?>
</table>
<?php }else{?>
<div>
    <form action="get_orders_history.php" name="orders_history_form" id="orders_history_form">
        <input type="hidden" name="customer_id" id="customer_id" value="<?=!empty($customer_id) ? $customer_id : '' ?>"/>
        <input type="hidden" name="is_order_ajaxed" id="is_order_ajaxed" value="1"/>
        <input type="hidden" name="pages" id="pages" value="<?=!empty($per_page) ? $per_page : '' ?>"/>
    </form>

    <div class="table-responsive" id="get_order_history_data">
    </div>
</div>
<script scr="text/javascript">
    $(document).ready(function(e){
        get_orders_history();
        dropdown_pagination('get_order_history_data');
    });

    $(document).off('click', '#get_order_history_data ul.pagination li a');
    $(document).on('click', '#get_order_history_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#get_order_history_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#get_order_history_data').html(res).show();
                common_select();
            }
        });
    });

    function get_orders_history(){
        var params = $('#orders_history_form').serialize();
        $.ajax({
            url: $('#orders_history_form').attr('action'),
            type: 'GET',
            data: params,
            beforeSend : function(e){
                $('#ajax_loader').show();
                $('#get_order_history_data').hide();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#get_order_history_data').html(res).show();
                common_select();
            }
        });
        return false;
    }
</script>
<?php } ?>