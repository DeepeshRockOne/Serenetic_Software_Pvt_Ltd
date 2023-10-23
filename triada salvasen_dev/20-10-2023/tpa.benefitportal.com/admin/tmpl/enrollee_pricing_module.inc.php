    <table class="<?=$table_class?> bg_odd_even_table text-nowrap">
        <input type="hidden" name="pricing_model" value="<?=checkIsset($pricing_model)?>">
        <thead>
        <tr>
        	<th>Fee</th>
            <th>Enrollee</th>
            <?php if(!empty($prdPricingQuestionRes)) { ?>
                <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                    <th class="<?= $value['id'] ?>PriceRowEnrollee" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>"><?= $value['display_label'] ?></th>
                <?php }?>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $mainKey => $rows) { ?>
                    <?php 
                    if(!empty($rows['matrixPlanType'])){
                        continue;
                    }
                    $key = ($rows['feeMatrixId'] > 0 ? ($rows['feeMatrixId'].'_'.$rows['id']) : ("0_".$rows['id']));
                    ?>
                    <input type="hidden" name="group_matrix[<?=$key?>]"  value="<?=$mainKey?>">
                    <tr>
                    	<td>
                            <div class="add_fee_addonwrap">
                              <div class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'add_fee_addon' : 'add_fee_addon_percentage' ?>">
                                <i class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'fa fa-usd' : 'fa fa-percent' ?> fee_calculated_type"></i>
                              </div>
                              <input type="text" name="plan[<?=$key?>]" class="form-control priceControl mw-125" placeholder="10.00" value="<?=checkIsset($rows['price'])?>">
                            </div>
                        </td>
                        <td><?= !empty($rows['enrolleeMatrix']) ? $rows['enrolleeMatrix'] . $rows['product_detail'] : '-' ?></td>
                        <?php if(!empty($prdPricingQuestionRes)) { ?>
                            <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                                <td class="<?= $value['id'] ?>PriceRowEnrollee" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>">
                                    <?= !empty($rows[$value['id']]['matrix_value']) ?  ($value['control_class']=='text_amount' ? '$' : '').$rows[$value['id']]['matrix_value'] : '-' ?></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="23" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<p class="error text-center" id="error_fee_plan_price"></p>        

