<div class="table-responsive">
    <table class="<?=$table_class?> table-small m-b-20">
       <thead>
            <tr class="bg_light_primary">
                <th>Payment Date</th>
                <th>Product</th>
                <th>Coverage Period</th>
                <th class="text-center">Cost</th>
                <th class="text-right">Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $total_amt = 0;
                $subscriptions_next_billing = array();
                $paymentApprovedCoverage = array();
                foreach ($coverage_subscriptions as $covKey => $coverage_period_row) {
                    foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                        if($tmp_ws_row['is_approved_payment'] == true){
                            $paymentApprovedCoverage[$tmp_ws_row['website_id']][] = $tmp_ws_row['start_coverage_period']; 
                        }
                    }
                }
                foreach ($coverage_subscriptions as $covKey => $coverage_period_row) {
                    if(in_array($covKey, $coverage_payments)){
                        foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                            $startDate = date("Y-m-d",strtotime("+1 day",strtotime($tmp_ws_row['end_coverage_period'])));
                            $product_dates = $enrollDate->getCoveragePeriod($startDate,$tmp_ws_row['member_payment_type']);
                            $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                            $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
                            
                            if(!empty($paymentApprovedCoverage[$tmp_ws_row['website_id']]) && in_array($startCoveragePeriod, $paymentApprovedCoverage[$tmp_ws_row['website_id']])) {
                                continue;
                            }
                            $pricing_change = get_renewals_new_price($tmp_ws_row['id'],false);
                            if($pricing_change && $pricing_change['pricing_changed'] == 'Y'){
                                $tmp_ws_row['price'] = $pricing_change['new_ws_row']['price'];
                            }
                            $subscriptions_next_billing[$tmp_ws_row['id']] = array(
                                'product_code' => $tmp_ws_row['product_code'],
                                'product_name' => $tmp_ws_row['product_name'],
                                'price' => $tmp_ws_row['price'],
                                'start_coverage_period' => $startCoveragePeriod,
                                'end_coverage_period' => $endCoveragePeriod,
                                'start_coverage_period_org' => $tmp_ws_row['start_coverage_period'],
                                'end_coverage_period_org' => $tmp_ws_row['end_coverage_period'],
                            );
                        }
                    }
                }
                $end_coverage_period_arr = array_column($subscriptions_next_billing,'end_coverage_period_org');
                $lowestCoverageDate = $enrollDate->getLowestCoverageDate($end_coverage_period_arr);

                
                foreach ($subscriptions_next_billing as $key => $snb) {
                    $next_billing_date = $enrollDate->getNextBillingDateFromCoverageList($end_coverage_period_arr,$snb['start_coverage_period_org'],$customer_id);

                    $next_billing_date = $enrollDate->getManualNextBillingDate($key,$snb['start_coverage_period_org'],$next_billing_date);

                    $subscriptions_next_billing[$key]['next_purchase_date'] = $next_billing_date;
                }

                $next_purchase_date_arr = array_column($subscriptions_next_billing, 'next_purchase_date');
                array_multisort($next_purchase_date_arr, SORT_ASC, $subscriptions_next_billing);

                foreach ($subscriptions_next_billing as $key => $snb) {
                    $total_amt += $snb['price'];
                    ?>
                    <tr>
                        <td><?=date("m/d/Y",strtotime($snb['next_purchase_date']))?></td>
                        <td><?=$snb['product_name']?></td>
                        <td class="centr_impot">
                            <?php
                                echo date("m/d/Y",strtotime($snb['start_coverage_period']));
                                echo " - ";
                                echo date("m/d/Y",strtotime($snb['end_coverage_period']));
                            ?>
                        </td>
                        <td class="text-right"><?=displayAmount($snb['price'],2)?></td>

                        <td class="text-right"><?=get_billing_label_by_billing_profile($default_cb_row['id']);?></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
    <table class="table table-small m-b-20 br-a">
       <tbody>
          <tr>
             <td class="text-right bg_light_gray"><span class="fw700">Total :</span> &nbsp;&nbsp; <?=displayAmount($total_amt,2)?></td>
          </tr>
       </tbody>
    </table>
</div>