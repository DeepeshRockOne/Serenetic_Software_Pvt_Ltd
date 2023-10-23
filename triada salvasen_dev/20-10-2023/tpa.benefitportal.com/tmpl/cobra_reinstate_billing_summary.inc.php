<table class="<?=$table_class?> table-small m-b-20">
    <thead class="bg_dark_primary">
        <tr>
            <th>Product</th>
            <th>Effective_date</th>
            <th>Paid Through Date</th>
            <th class="centr_impot">Product Price</th>
            <th class="" style="width: 125px;">COBRA Fee</th>
            <th class="text-right" style="width: 125px;">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $all_coverage_total = 0;
        foreach ($subscriptions_coverage_periods as $coverageKey =>$coverage_period_row) {
            if($coverage_period_row['is_approved_payment'] == true) {
                continue;
            }
            
            

            foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                $date_selection_options = get_tier_change_date_selection_options($tmp_ws_row['id']);
                $count = 0;
                foreach ($date_selection_options as $key => $value) {
                    $tmp_effective_date = strtotime($effective_date[$tmp_ws_row['product_id']]);
                    $tmp_paid_through_date = strtotime($paid_through_date[$tmp_ws_row['product_id']]);
                    if(strtotime($value['start_coverage_period']) >= $tmp_effective_date){
                        $coverage_periods_data[$tmp_ws_row['product_id']]['coverage'][$count]['start_coverage_period'] = $value['start_coverage_period'];
                        $coverage_periods_data[$tmp_ws_row['product_id']]['coverage'][$count]['end_coverage_period'] = $value['end_coverage_period'];
                        $coverage_periods_data[$tmp_ws_row['product_id']]['ws_id'] = $tmp_ws_row['id'];
                        $count++;
                    }
                    if(strtotime($value['end_coverage_period']) >= $tmp_paid_through_date){
                        break;
                    }    

                }
            }     
        }
        
            $get_cobra_service_fee = $pdo->selectOne("SELECT additional_surcharge FROM group_cobra_benefits WHERE group_use_cobra_benefit = 'Y' AND is_additional_surcharge = 'Y'");
            $cobra_service_fee = $get_cobra_service_fee['additional_surcharge'];
        ?>
            <?php 
                // pre_print($coverage_periods_data);
                foreach ($coverage_periods_data as $prd_id => $cp) {
                    foreach ($cp['coverage'] as $key => $value) { 
                        $get_price = $pdo->selectOne("SELECT price from prd_matrix where plan_type = :plan_type and product_id = :product_id",array(':plan_type' =>$prd_plan_type[$prd_id],':product_id' => $prd_id));
                        $price = $get_price['price'];
                        $total_amount = $price + ($price * $cobra_service_fee / 100);
                        $all_coverage_total += $total_amount; 
                        ?>
                          <tr>
                                <td><?=getname('prd_main',$prd_id,'name','id')?></td>
                                <td><?=date('m/d/Y',strtotime($effective_date[$prd_id]))?></td>
                                <td><?=date('m/d/Y',strtotime($paid_through_date[$prd_id]))?></td>
                                <td><?=displayAmount($price,2)?></td>
                                <td><?=$cobra_service_fee?>%</td>
                                <td class="text-right"><?=displayAmount($total_amount,2)?></td>
                            </tr>              
                    <?php }
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