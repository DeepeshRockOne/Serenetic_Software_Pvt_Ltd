<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$year = date('Y');
$sch_params = array();
$labelArr = array();
$Y_KeyArr = array();
$CompanyLagend = array();
$Line_Color_Arr = array();

$labelArr[] = 'Gross Premiums';
$labelArr[] = 'New Business';
$labelArr[] = 'Renewals';
$labelArr[] = 'Refunds/ChargeBacks';
$labelArr[] = 'Void';

$Y_KeyArr[] = 'TotalAmount';
$Y_KeyArr[] = 'NewAmount';
$Y_KeyArr[] = 'RenewalAmount';
$Y_KeyArr[] = 'ChargebackRefundAmount';
$Y_KeyArr[] = 'VoidAmount';

$Line_Color_Arr[] = '#5a708a';
$Line_Color_Arr[] = '#74a7e5';
$Line_Color_Arr[] = '#5fb89c';
$Line_Color_Arr[] = '#5d5d5d';
$Line_Color_Arr[] = '#bd4360';

$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
$chartData = array();
$months_arr = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");

for ($i = 0; $i < 12; $i++) {
  $chartData[$i]["period"] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));     
  foreach ($Y_KeyArr as $key) {      
    $chartData[$i][$key] = 0;
  }
}

$fee_products = get_enrollment_with_associate_fee_prd_ids("string");
$enroll_products = get_enrollment_fee_prd_ids('string');

$orderSql = "SELECT 
            SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'Renewal Order'),t.credit,0))as total,
            SUM(IF(t.transaction_type = 'Renewal Order',t.credit,0))as renewal_total,
            SUM(IF(t.transaction_type = 'New Order',t.credit,0))as new_total,
            SUM(IF(t.transaction_type = 'Void Order',t.debit,0)) AS void_total,
            SUM(IF(t.transaction_type = 'Chargeback' OR t.transaction_type = 'Refund Order',t.debit,0)) AS chargeback_refund_total,
            DATE_FORMAT(t.created_at,'%m')as o_month,YEAR(t.created_at)as o_year,DATE_FORMAT(t.created_at,'%d')as o_day            
            FROM transactions t
            LEFT JOIN 
            (SELECT o.id
                    FROM orders o 
                    JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N') 
                    JOIN customer c ON (c.id=o.customer_id) 
                    WHERE o.payment_type IN('CC','ACH') GROUP BY o.id 
            ) as ord ON ord.id = t.order_id 
            WHERE t.id > 0 AND t.transaction_type NOT IN ('Pending') GROUP BY YEAR(t.created_at),MONTH(t.created_at)";

$orderRows = $pdo->select($orderSql, $sch_params);
if ($orderRows) {
    foreach ($orderRows as $order) {
        foreach ($chartData as $key => $cdata) {
            if ($cdata['period'] == $order['o_year'] . '-' . $order['o_month']) {
                $chartData[$key]['TotalAmount'] = $chartData[$key]['TotalAmount'] + $order['total'];
                $chartData[$key]['NewAmount'] = $chartData[$key]['NewAmount'] + $order['new_total'];
                $chartData[$key]['RenewalAmount'] = $chartData[$key]['RenewalAmount'] + $order['renewal_total'];
                $chartData[$key]['ChargebackRefundAmount'] = $chartData[$key]['ChargebackRefundAmount'] + $order['chargeback_refund_total'];
                $chartData[$key]['VoidAmount'] = $chartData[$key]['VoidAmount'] + $order['void_total'];
            }
        }
    }
}
$resData = array(
  "data" => $chartData,
  "labels" => $labelArr,
  "y_key" => $Y_KeyArr,
  "line_color" => $Line_Color_Arr,
  "company_lagend" => $CompanyLagend,
  "period_len" => count($chartData),
);

// pre_print($resData);

header("Content-type: application/json;");
echo json_encode($resData);
dbConnectionClose();
exit;
?>