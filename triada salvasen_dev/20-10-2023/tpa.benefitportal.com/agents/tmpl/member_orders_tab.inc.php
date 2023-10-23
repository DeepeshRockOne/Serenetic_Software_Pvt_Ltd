<div class="clearfix order_tab_info">
   <div class="pull-left">
      <ul class="nav nav-tabs tabs  customtab nav-noscroll" role="tablist">
         <li role="presentation" class="active">
            <a href="#orders_history" aria-controls="orders_history" role="tab" data-toggle="tab">Orders</a>
         </li>
         <li role="presentation">
            <a href="#transaction_history" aria-controls="transaction_history" role="tab" data-toggle="tab">Transactions</a>
         </li>
      </ul>
   </div>
   <div class="pull-right text-right">
      <span class="m-b-0 p-t-7 m-r-10"><strong>Next Billing:</strong>   <span class="text-success m-l-10"> <?=getCustomDate($next_purchase_info['next_billing_date'])?>  |  <?=$next_purchase_info['products']?> Products  |  <?=displayAmount($next_purchase_info['grandTotal'])?></span></span>
      <?php if($has_full_access == true) { ?>
      <a href="<?=$HOST?>/make_payment.php?location=agent&id=<?=$customer_id?>" class="btn btn-success make_payment">Make Payment</a>
      <?php } ?>
   </div>
</div>
<div class="tab-content">
   <div role="tabpanel" class="tab-pane active" id="orders_history">
   </div>
   <div role="tabpanel" class="tab-pane" id="transaction_history">
   </div>
</div>
<hr>
<script type="text/javascript">
$(document).ready(function() {
   get_order_transaction_history('get_orders_history.php','orders_history');
   get_order_transaction_history('get_transactions_history.php','transaction_history');
   $(".make_payment").colorbox({iframe: true, width: '768px', height: '530px'});

});
$(document).off('click', '.view_order_receipt');
$(document).on('click', '.view_order_receipt', function(e) {
  e.stopImmediatePropagation();
  $.colorbox({
      href: $(this).data('href'),
      iframe: true,
      width: '1020px',
      height: '600px',
      fastIframe: false,
    });
});

$(document).off('click', '.transReceipt');
$(document).on('click', '.transReceipt', function(e) {
  e.stopImmediatePropagation();
  $.colorbox({
      href: $(this).data('href'),
      iframe: true,
      width: '1020px',
      height: '600px',
      fastIframe: false,
    });
});



$(document).off('change', '.order_action');
$(document).on('change', '.order_action', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var this_val = $(this).val();
    if (this_val == 'Receipt') {
      Receipt(id);
    } else if (this_val == 'Reprocess') {
      Reprocess(id);
    } else if (this_val == 'Regenerate') {
      Regenerate(id);
    } else if (this_val == 'Postdate') {
      Postdate(id);
    }else if (this_val == 'reverseOrder') {
       $href= "add_payment_reversal.php?location=agent&orderId="+id;
        window.open($href,"_blank");
    } 

});

/* order receipt dropdown option start */
function Receipt(id) {
    $.colorbox({
      href: 'order_receipt.php?orderId='+id,
      iframe: true,
      width: '1020px',
      height: '630px',
      fastIframe: false,
      onClosed:function(){
        $("#order_action_"+id).val('').change();
      }
    });
  }
/* order receipt dropdown option end */

/* Reprocess Order dropdown option start */
function Reprocess(id) {
    $.colorbox({
      href: '<?=$HOST?>/reprocess_order.php?location=agent&location_from=memberDetail&customer_id=<?=$customer_id?>&orderId='+id,
      iframe: true,
      width: '1020px',
      height: '600px',
      fastIframe: false,
      onClosed:function(){
        $("#order_action_"+id).val('').change();
      }
    });
  }
/* Reprocess Order dropdown option end */

/* Regenerate Order dropdown option start */
function Regenerate(id) {
    $.colorbox({
      href: '<?=$HOST?>/regenerate_order.php?location=agent&location_from=memberDetail&customer_id=<?=$customer_id?>&orderId='+id,
      iframe: true,
      width: '1020px',
      height: '600px',
      fastIframe: false,
      onClosed:function(){
        $("#order_action_"+id).val('').change();
      }
    });
  }
/* Regenerate Order dropdown option end */

/* PostDate dropdown option start */
function Postdate(id) {
    $.colorbox({
      href: '<?=$HOST?>/edit_order_post_date.php?location=agent&orderId='+id,
      iframe: true,
      width: '400px',
      height: '420px',
      fastIframe: false,
      onClosed:function(){
        $("#order_action_"+id).val('').change();
      }
    });
  }
/* PostDate dropdown option end */

function get_order_transaction_history(report_url,report_div){
      $.ajax({
          url: report_url,
          type: 'GET',
          data : {id:'<?=$customer_id?>'},
          beforeSend : function(e){
            $('#ajax_loader').show();
          },
          success: function (res) {
            $('#ajax_loader').hide();
            $('#'+report_div).html(res);
          }
      });
  }
</script>