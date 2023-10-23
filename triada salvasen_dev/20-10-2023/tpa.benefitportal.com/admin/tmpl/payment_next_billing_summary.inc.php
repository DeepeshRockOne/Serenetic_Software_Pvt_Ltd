<div class="table-responsive">
    <table class="<?=$table_class?> table-small m-b-20">
       <thead>
            <tr class="bg_light_primary">
                <th>Payment Date</th>
                <th>Product</th>
                <th>Plan Period</th>
                <th class="text-center">Cost</th>
                <th class="text-right">Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $totalAmt = 0;
                $subscriptions_next_billing = array();
                $startCoveragePeriod = '';
                $endCoveragePeriod = '';
                foreach ($coverage_subscriptions as $covKey => $covRow) {
                    foreach ($covRow["ws_res"] as $tmp_ws_row) {
                        if($tmp_ws_row["is_approved_payment"] == true){
                            continue;
                        }

                        if(!in_array($covKey, $coverage_payments)){
                            $startCoveragePeriod = $tmp_ws_row["start_coverage_period"];
                            $endCoveragePeriod = $tmp_ws_row["end_coverage_period"];
                        }else{  
                            $startCoveragePeriod = $tmp_ws_row["start_coverage_period"];
                            $endCoveragePeriod = $tmp_ws_row["end_coverage_period"];

                            $startDate = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));
                            $product_dates=$enrollDate->getCoveragePeriod($startDate);
                            $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                            $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
                        }

                        $subscriptions_next_billing[$tmp_ws_row['id']] = array(
                            'product_code' => $tmp_ws_row['product_code'],
                            'product_name' => $tmp_ws_row['product_name'],
                            'price' => $tmp_ws_row['price'],
                            'start_coverage_period' => $startCoveragePeriod,
                            'end_coverage_period' => $endCoveragePeriod,
                            'next_purchase_date' => $tmp_ws_row['end_coverage_period'],
                        );
                    }
                    if($covRow["is_approved_payment"] != true){
                        if(!in_array($covKey, $coverage_payments)){
                            break;
                        }
                    }
                }
        
                $min_end_coverage_date = '';
                foreach ($subscriptions_next_billing as $key => $snb) {
                    if(empty($min_end_coverage_date)){
                        $min_end_coverage_date = $snb['next_purchase_date'];
                    }else{
                        if(strtotime($snb['next_purchase_date']) < strtotime($min_end_coverage_date)){
                            $min_end_coverage_date = $snb['next_purchase_date'];
                        }
                    }

                    $next_billing_date = date('Y-m-d',strtotime($min_end_coverage_date .'-1 day'));    
                      
                    if(strtotime($next_billing_date) < strtotime('now')){

                      $year = date('Y',strtotime($snb['start_coverage_period']));
                      $month = date('m',strtotime($snb['start_coverage_period']));
                      $day = date('d',strtotime($next_billing_date));
                      
                      $next_billing_date = date('Y-m-d',strtotime($year.'-'.$month.'-'.$day));
                      
                    }
                    $subscriptions_next_billing[$key]['next_purchase_date'] = $next_billing_date;
                }

                foreach ($subscriptions_next_billing as $key => $snb) {
                    ?>
                    <tr>
                        <td><?=date("m/d/Y",strtotime($snb['next_purchase_date']))?></td>
                        <td><?=$snb["product_name"]?></td>
                        <td>
                            <?php
                                echo date("m/d/Y",strtotime($snb['start_coverage_period']));
                                echo " - ";
                                echo date("m/d/Y",strtotime($snb['end_coverage_period']));
                            ?>
                        </td>
                        <td class="text-center"><?=displayAmount($snb['price'],2)?></td>
                        <td class="text-right"><?=get_billing_label_by_billing_profile($default_cb_row['id'])?></td>
                    </tr>
                <?php
                        $totalAmt += $snb['price'];
                    }
                ?>
        </tbody>
    </table>
    <table class="table table-small m-b-20 br-a">
       <tbody>
          <tr>
             <td class="text-right bg_light_gray"><span class="fw700">Total :</span> &nbsp;&nbsp; <?=displayAmount($totalAmt,2)?></td>
          </tr>
       </tbody>
    </table>
</div>