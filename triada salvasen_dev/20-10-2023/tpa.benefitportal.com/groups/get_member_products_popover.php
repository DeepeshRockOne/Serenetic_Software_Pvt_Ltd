<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET['id'];
$resProducts = array();
if (!empty($id)) {
    $incr = "";
    $sch_params = array();

    $incr .= " AND md5(c.id) = :id";
    $sch_params[':id'] = $id;

    $sel_sql = "SELECT CONCAT(s.fname,' ',s.lname) as agent_name, s.rep_id as agent_id,IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,p.product_code,w.eligibility_date,w.termination_date,w.website_id,w.next_purchase_date,ppt.title as benefit_tier,w.price,DATE(w.created_at) as added_date,ce.fulfillment_date,p.id as p_id,pm.id as matrix_id,w.status,ce.tier_change_date,ce.process_status,c.id as customer_id,w.id as ws_id,
            (
              SELECT o.future_payment FROM orders o
              JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
              WHERE
                  o.customer_id = w.customer_id AND
                  od.product_id = w.product_id AND o.status NOT IN('Pending Validation')
              ORDER BY o.id ASC
              LIMIT 1
            ) as is_active_in_future
            FROM customer c
            JOIN website_subscriptions w ON (w.customer_id=c.id)
            JOIN customer_enrollment ce ON (ce.website_id=w.id)
            JOIN customer s ON (s.id=ce.sponsor_id)
            JOIN prd_main p ON (p.id=w.product_id)
            JOIN prd_matrix pm ON (pm.product_id=w.product_id AND w.plan_id = pm.id)
            LEFT JOIN prd_plan_type ppt ON (ppt.id = w.prd_plan_type_id)
            WHERE p.type!='Fees' AND c.type='Customer' $incr GROUP BY w.id ORDER BY p.type DESC,p.name";
    $fetch_rows = $pdo->select($sel_sql,$sch_params);

    /*---------- Product Sorting Process -------------*/
    $product_names = $pending_products = $active_pending_products = $active_products = $terminated_products = array();
    foreach ($fetch_rows as $key => $rows) {
        if (in_array($rows['status'], array('Inactive'))) {
            $terminated_products[] = $rows;
        } elseif (!empty($rows['termination_date']) && strtotime($rows['termination_date']) > 0 && strtotime($rows['termination_date']) <= strtotime(date('Y-m-d'))) {
            $terminated_products[] = $rows;
        } elseif (!empty($rows['eligibility_date']) && strtotime($rows['eligibility_date']) > 0 && strtotime($rows['eligibility_date']) > strtotime(date('Y-m-d'))) {
            $pending_products[] = $rows;
        } elseif ($rows['is_active_in_future'] == 'Y') {
            $pending_products[] = $rows;
        } elseif (!empty($rows['termination_date']) && strtotime($rows['termination_date']) > 0 && strtotime($rows['termination_date']) > strtotime(date('Y-m-d'))) {
            $active_pending_products[] = $rows;
        } elseif ($rows['process_status'] == 'Pending' && !empty($rows['tier_change_date']) && strtotime($rows['tier_change_date']) > strtotime(date('Y-m-d'))) {
            $active_pending_products[] = $rows;
        } else {
            $active_products[] = $rows;
        }

        $product_names[] = $rows['name'];
    }

    $fetch_rows = array_merge($pending_products, $active_pending_products, $active_products, $terminated_products);
    array_multisort($product_names, SORT_ASC, $product_names);
}
?>

<div class="table-responsive">
    <table class="<?= $table_class ?> fs12">
        <thead>
            <tr>
                <th>Added Date<br>Plan ID</th>
                <th>Product Name<br>Enrolling Agent</th>
                <th>Plan</th>
                <th>Effective Date</th>
                <th>Termination<br>Date</th>
                <th>Next Billing Date</th>
                <th>Fulfillment <br>Date</th>
                <th>Total Premium</th>
            </tr>
        </thead>
        <tbody>
           <?php if($fetch_rows){ ?>
              <?php foreach ($fetch_rows as $k => $row) { ?>
           <tr>
              <?php 
                    $data_color = 'success'; 
                    if (in_array($row['status'], array('Inactive')) || (!empty($row['termination_date']) && strtotime($row['termination_date']) > 0 && strtotime($row['termination_date']) <= strtotime(date('Y-m-d')))) {
                        $data_color = "danger";

                    } else if (!empty($row['eligibility_date']) && strtotime($row['eligibility_date']) > strtotime(date('Y-m-d'))) {
                        $data_color = "warning";
                    
                    } else if (!empty($row['termination_date']) && strtotime($row['termination_date']) > 0) {
                        $data_color = "success-warning";
                    
                    } else if ($row['process_status'] == 'Pending' && !empty($row['tier_change_date']) && strtotime($row['tier_change_date']) > strtotime(date('Y-m-d'))) {
                        $data_color = "success-warning";
                    }
              ?>
              <td>
                  <?=displayDate($row['added_date']);?><br>
                  <a href="javascript:void(0);" class="fw500 text-action"><?=$row['website_id']?></a>
             </td>
             <td>
                  <a class="label label-rounded label-<?=$data_color?> product_name" data-toggle="" data-target="#prd_details_expanded" data-ws_id="<?=md5($row['ws_id'])?>" data-color="<?=$data_color?>"><?=$row['name']?></a>
                    <p class="mn fw500 text-action"><?=$row['agent_id']?> - <?=$row['agent_name']?></p>
             </td>
             <td><?=$row['benefit_tier']?></td>
             <td class="effective_td_<?=$row['p_id']?>"><?=date('m/d/Y',strtotime($row['eligibility_date']))?></td>
             <td><?=displayDate($row['termination_date']);?></td>
             <td>
                  <?php 
                  if(strtotime($row['termination_date']) > 0) {
                      echo '-';
                  } else {
                      echo displayDate($row['next_purchase_date']);
                  } 
                  ?>
             </td>
             <td><?=displayDate($row['fulfillment_date']);?></td>
             <td>$<?=$row['price']?></td>
              </tr>
              <?php } ?>
           <?php } ?>
        </tbody>
    </table>
</div>