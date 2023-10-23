<table class="<?=$table_class?> table-small m-b-20">
    <thead>
        <tr class="bg_dark_primary">
            <th>Payment Date</th>
            <th>Coverage Period</th>
            <th class="centr_impot">Cost</th>
            <th class="text-right" style="width: 125px;">Payment Type</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $all_coverage_total = 0;
        foreach ($subscriptions_coverage_periods as $coverageKey =>$coverage_period_row) {
            if($coverage_period_row['is_approved_payment'] == true) {
                continue;
            }

            $coverage_total = 0;
            $tmp_index = $coverageKey;
            if (isset($sel_fees[$tmp_index])) {
                foreach ($sel_fees[$tmp_index] as $v) {
                     $tmp_ws_sql = "SELECT ws.price
                                FROM website_subscriptions ws 
                                WHERE ws.id=:id";
                    $tmp_ws_row = $pdo->selectOne($tmp_ws_sql, array(":id" => $v));
                    $coverage_total += $tmp_ws_row['price'];
                }
            }
            if (isset($sel_healthystepfee[$tmp_index])) {
                foreach ($sel_healthystepfee[$tmp_index] as $v) {
                    $tmp_ws_sql = "SELECT ws.price
                                FROM website_subscriptions ws
                                WHERE ws.id=:id";
                    $tmp_ws_row = $pdo->selectOne($tmp_ws_sql, array(":id" => $v));
                    $coverage_total += $tmp_ws_row['price'];
                }
            }
            if (isset($sel_servicefee[$tmp_index])) {
                foreach ($sel_servicefee[$tmp_index] as $v) {
                    $tmp_ws_sql = "SELECT ws.price
                                FROM website_subscriptions ws
                                WHERE ws.id=:id";
                    $tmp_ws_row = $pdo->selectOne($tmp_ws_sql, array(":id" => $v));
                    $coverage_total += $tmp_ws_row['price'];
                }
            }
        ?>
            <tr>
                <td>
                    <?=date('m/d/Y',strtotime($coverage_periods_data[$tmp_index]['payment_date']))?>
                </td>
                <td>
                    <?php
                        if($is_list_bill_enroll != "Y" ){
                        echo "P".$coverage_period_row['renew_count'].": ";
                        }
                        echo date("m/d/Y",strtotime($coverage_period_row['start_coverage_period']));
                        echo " - ";
                        echo date("m/d/Y",strtotime($coverage_period_row['end_coverage_period']));
                    ?>
                </td>
                <td class="centr_impot">
                    <?php
                    foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                        if($tmp_ws_row['is_approved_payment'] == true) {
                            continue;
                        }
                        $coverage_total += $tmp_ws_row['price'];
                    }
                    $all_coverage_total += $coverage_total;
                    echo displayAmount($coverage_total,2);
                    ?>
                </td>
                <td class="text-right">
                    <?=$is_list_bill_enroll == "Y" ? "List Bill" : get_billing_label_by_billing_profile($coverage_periods_data[$tmp_index]['billing_profile'])?></td>
            </tr>
        <?php 
            }
        ?>
    </tbody>
</table>
<table class="table table-small m-b-20 br-a">
   <tbody>
      <tr>
         <td class="text-right bg_light_gray"><span class="fw700">Total :</span> &nbsp;&nbsp; <?=displayAmount($all_coverage_total,2);?></td>
      </tr>
   </tbody>
</table>