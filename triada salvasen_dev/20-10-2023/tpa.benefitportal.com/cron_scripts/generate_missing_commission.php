<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/includes/connect.php"; 
$ord_sql = "SELECT GROUP_CONCAT(o.id) as order_ids
          FROM orders o
          WHERE 
          o.is_comm_generated IN('N','R') AND
          o.created_at >= (NOW() - INTERVAL 2 DAY) AND 
          o.status IN('Payment Approved','Completed','Pending Settlement')";
$ord_row = $pdo->selectOne($ord_sql);
if(!empty($ord_row['order_ids'])) {
    $order_ids = explode(',',$ord_row['order_ids']);
    add_commission_request('generate_commissions',array('order_ids' => $order_ids));
}
echo "<br>Completed";
dbConnectionClose();
?>
