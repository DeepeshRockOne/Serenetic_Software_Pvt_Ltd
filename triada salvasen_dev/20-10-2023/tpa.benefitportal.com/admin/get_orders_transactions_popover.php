<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET['id'];
if (!empty($id)) {
    $incr = "";
    $sch_params = array();

    $incr .= " AND o.id = :id";
    $sch_params[':id'] = $id;

    $order_info = $pdo->selectOne("SELECT display_id,created_at,payment_type from orders where id =:id",array(":id" => $id));

    $sel_sql = "SELECT t.created_at,t.payment_type,t.transaction_status as transStatus from transactions t JOIN orders o on(t.order_id = o.id and t.is_deleted = 'N') where 1 $incr order by t.id desc limit 5";
    $fetch_rows = $pdo->select($sel_sql,$sch_params);
}
?>
<h4 class="m-t-0">Transactions Details</h4>
    <table cellpadding="0" cellspacing="0" width="100%" border="0">
      <tbody>
        <tr>
          <td><strong>Order Date : </strong><?=displayDate($order_info['created_at'])?>&nbsp;&nbsp;&nbsp;</td>
          <td class="text-right"><strong>Payment Type :</strong> <?=$order_info['payment_type'] ?></td>
        </tr>
      </tbody>
    </table>
<div class="table-responsive m-t-10">
    <table class="<?= $table_class ?> fs12">
        <thead>
            <tr>
                <th>Date</th>
                <th width="50%">Status</th>
            </tr>
        </thead>
        <tbody>
           <?php if($fetch_rows){ ?>
              <?php foreach ($fetch_rows as $k => $row) { ?>
              <tr>
                <td><?=displayDate($row['created_at']);?></td>
                <td><?=$row['transStatus']?></td>
              </tr>
              <?php } ?>
           <?php } ?>
        </tbody>
    </table>
    <div class="clearfix">
    <?php if(has_access(60)){?>
         <a href="javascript:void(0);" class="commission_popup red-link pull-left" data-id="<?=$id?>">View Commissions</a>
    <?php } ?>
      <a class="pull-right blue-link" target="_blank" href="payment_transaction.php?order_id=<?=$order_info['display_id']?>&is_from_all_orders=Y">View All</a>
    </div>
</div>