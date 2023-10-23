<div class="table-responsive">
    <table class="<?=$table_class?> table-small m-b-20">
        <thead>
            <tr class="bg_light_primary">
                <th>Payment Date</th>
                <th>Coverage Period</th>
                <th class="text-center">Cost</th>
                <th class="text-right">Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                foreach ($coverage_payments as $key => $covPeriod) {
                    $coverageTotal = 0;
                    $startCoveragePeriod = $coverage_subscriptions[$covPeriod]["start_coverage_period"];
                    $endCoveragePeriod = $coverage_subscriptions[$covPeriod]["end_coverage_period"];
                    $tmp_index = strtotime($startCoveragePeriod);
            ?>
                <tr>
                    <td>
                        <?=date('m/d/Y',strtotime($coverage_periods_data[$tmp_index]['payment_date']))?>
                    </td>
                    <td><?="P".$covPeriod?></td>
                    <td class="text-center">
                        <?php
                            foreach ($coverage_subscriptions[$covPeriod]['ws_res'] as $wsPlan) {
                              if($wsPlan['is_approved_payment'] == true) {
                                continue;
                              }

                                $pricing_change = get_renewals_new_price($wsPlan['id'],false);
                                if($pricing_change && $pricing_change['pricing_changed'] == 'Y'){
                                    $wsPlan['price'] = $pricing_change['new_ws_row']['price'];
                                }
                              $coverageTotal += $wsPlan['price'];
                            }

                            if(isset($coverage_periods_data[$tmp_index]['service_fee'])) {
                                $coverageTotal += $coverage_subscriptions[$covPeriod]['coverage_service_fee']['total'];
                            }

                            if(isset($coverage_periods_data[$tmp_index]['enrollment_fee'])) {
                                $coverageTotal += $stepFeeRow['price'];    
                            }
                            echo displayAmount($coverageTotal,2);
                        ?>
                    </td>
                    <td class="text-right"><?=get_billing_label_by_billing_profile($coverage_periods_data[$tmp_index]['billing_profile'])?></td>
                </tr>
            <?php 
                    $total += $coverageTotal;
                }
            ?>
        </tbody>
    </table>
    <table class="table table-small m-b-20 br-a">
       <tbody>
          <tr>
             <td class="text-right bg_light_gray"><span class="fw700">Total :</span> &nbsp;&nbsp; <?=displayAmount($total,2)?></td>
          </tr>
       </tbody>
    </table>
</div>