<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$customer_id = $_GET['customer_id'];
$orderId = $_GET['orderId'];
$location_from = checkIsset($_GET['location_from']);

$orderRow = $pdo->selectOne("SELECT o.id,o.display_id,o.is_renewal,o.customer_id,c.fname,c.lname,c.rep_id FROM orders as o JOIN customer as c ON (c.id = o.customer_id) WHERE md5(o.id) = :id AND md5(c.id)=:cid", array(":id" => $orderId,":cid"=>$customer_id));

if(empty($orderRow['id'])){
    echo "<script>
    parent.setNotifyError('Order not found');
    parent.$.colorbox.close();</script>";
	exit();
}

$od_sql = "SELECT p.name,p.product_code,od.start_coverage_period,od.end_coverage_period,od.order_id,w.id as ws_id,w.eligibility_date,w.customer_id,w.status,w.next_purchase_date,od.unit_price,p.product_type,p.type,od.product_id
     FROM order_details od
     JOIN website_subscriptions w ON(od.plan_id=w.plan_id)
     JOIN prd_matrix pm ON(od.plan_id = pm.id)
     JOIN prd_main p ON(p.id=od.product_id and p.is_deleted='N')
     WHERE od.order_id=:id AND w.customer_id=:customer_id AND od.is_deleted='N' GROUP BY od.id";
$od_where = array(":id" => $orderRow['id'], ":customer_id"=>$orderRow['customer_id']);
$od_res = $pdo->select($od_sql,$od_where);

if(empty($od_res)){
    echo "<script>
    parent.setNotifyError('Order not found');
    parent.$.colorbox.close();</script>";
	exit();
}
$attempt_order = true;
$to_date = '';
$from_date = date("m/d/Y");
if(count($od_res) > 0){
     $lowest_effective_date = date("Y-m-d", strtotime($od_res[0]['eligibility_date']));
     $lowest_end_coverge_date = date("Y-m-d", strtotime($od_res[0]['end_coverage_period']));
     foreach ($od_res as $key => $value) {
          if(strtotime($value['eligibility_date']) < strtotime($lowest_effective_date)){
               $lowest_effective_date = date("Y-m-d", strtotime($value['eligibility_date']));
          }
          if(strtotime($value['end_coverage_period']) < strtotime($lowest_end_coverge_date)){
               $lowest_end_coverge_date = date("Y-m-d", strtotime($value['end_coverage_period']));
          }
     }
    if($orderRow['is_renewal'] == 'N'){
        $lowest_effective_date = date("Y-m-d", strtotime("-1 days",strtotime($lowest_effective_date)));
        if(strtotime(date("Y-m-d")) >= strtotime($lowest_effective_date)){
             $attempt_order = false;
        } else {
             $to_date = date("m/d/Y", strtotime($lowest_effective_date));
        }
    } else {
        $lowest_end_coverge_date = date("Y-m-d", strtotime("-1 days",strtotime($lowest_end_coverge_date)));
        if(strtotime(date("Y-m-d")) >= strtotime($lowest_end_coverge_date)){
             $attempt_order = false;
        } else {
             $to_date = date("m/d/Y", strtotime($lowest_end_coverge_date));
        }
   }
}

$template = 'reprocess_order.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>