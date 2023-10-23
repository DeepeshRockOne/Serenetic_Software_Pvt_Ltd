
<?php if($pricingModel == "FixedPrice"){ ?>
    <table class="<?=$table_class?> mn bg_odd_even_table" id="benefit_tier_table">
      <thead>
        <tr>
          <th>Fee</th>
          <th>Plan Tier</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($prdPlanTypeArray)) {  
          ?>
        <?php foreach ($prdPlanTypeArray as $plan) {
            if(in_array($plan['id'],$prdPlansArr)){
         ?>
        <tr>
          <td>
            <div class="add_fee_addonwrap">
              <div class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'add_fee_addon' : 'add_fee_addon_percentage' ?>">
                <i class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'fa fa-usd' : 'fa fa-percent' ?> fee_calculated_type"></i>
              </div>
              <input type="text" name="plan[<?= $plan['id'] ?>]" class="form-control priceControl" value="<?= !empty($priceArr[$plan['id']]) ? $priceArr[$plan['id']] : '' ?>" placeholder="10.00" >
            </div>
          </td>
          <td> <?= $plan['title'] ?> </td>
        </tr>
        <?php }
            }
          }
        ?>
      </tbody>
    </table>
    <p class="error" id="error_fee_plan_price"></p>
<?php } else if($pricingModel == "VariablePrice"){ ?>
    <table class="<?=$table_class?> bg_odd_even_table text-nowrap">
        <thead>
        <tr>
            <th>Fee</th>
            <th>Plan Tier</th>
            <?php if(!empty($prdPricingQuestionRes)) {  ?>
                <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                    <th class="<?= $value['id'] ?>PriceRow" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>"><?= $value['display_label'] ?></th>
                <?php }?>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $matrix_group => $matrixGroupRows) { 
                foreach ($matrixGroupRows as $mainKey => $rows) { ?>
                    <?php 
                    if(!empty($rows['enrolleeMatrix'])){
                        continue;
                    }
                    ?>
                    <tr>
                        <td>
                            <div class="add_fee_addonwrap">
                              <div class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'add_fee_addon' : 'add_fee_addon_percentage' ?>">
                                <i class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'fa fa-usd' : 'fa fa-percent' ?> fee_calculated_type"></i>
                              </div>
                              <input type="text" name="plan[<?=$rows['matrix_group']?>]" class="form-control priceControl mw-125" placeholder="10.00" value="<?=checkIsset($priceArr[$rows['matrix_group']])?>">
                            </div>
                        </td>
                        <td><?= !empty($rows['matrixPlanType']) ? $prdPlanTypeArray[$rows['matrixPlanType']]['title'] : '-' ?></td>
                        <?php if(!empty($prdPricingQuestionRes)) { ?>
                            <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                                <td class="<?= $value['id'] ?>PriceRow" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>">
                                    <?= $value['control_class']=='text_amount' ? '$' : '' ?><?= !empty($rows[$value['id']]['matrix_value']) ? trim($rows[$value['id']]['matrix_value']) : '-' ?></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php }?>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="23" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <p class="error" id="error_fee_plan_price"></p>
<?php } else if($pricingModel == "VariableEnrollee"){ ?>
    <table class="<?=$table_class?> bg_odd_even_table text-nowrap">
        <thead>
        <tr>
            <th>Fee</th>
            <th>Enrollee</th>
            <?php if(!empty($prdPricingQuestionRes)) {  ?>
                <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                    <th class="<?= $value['id'] ?>PriceRowEnrollee" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>"><?= $value['display_label'] ?></th>
                <?php }?>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $matrix_group => $matrixGroupRows) { 
                foreach ($matrixGroupRows as $mainKey => $rows) { ?>
                    <?php 
                    if(!empty($rows['matrixPlanType'])){
                        continue;
                    }
                    ?>
                    <tr>
                        <td>
                            <div class="add_fee_addonwrap">
                              <div class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'add_fee_addon' : 'add_fee_addon_percentage' ?>">
                                <i class="<?= (!empty($fee_method) && ($fee_method == 'FixedPrice' || $fee_method == 'Amount')) ? 'fa fa-usd' : 'fa fa-percent' ?> fee_calculated_type"></i>
                              </div>
                              <input type="text" name="plan[<?=$rows['matrix_group']?>]" class="form-control priceControl mw-125" placeholder="10.00"  value="<?=checkIsset($priceArr[$rows['matrix_group']])?>">
                            </div>
                        </td>
                        <td><?= !empty($rows['enrolleeMatrix']) ? $rows['enrolleeMatrix'] : '-' ?></td>
                        <?php if(!empty($prdPricingQuestionRes)) { ?>
                            <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                                <td class="<?= $value['id'] ?>PriceRowEnrollee" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>">
                                    <?= !empty($rows[$value['id']]['matrix_value']) ?  ($value['control_class']=='text_amount' ? '$' : '').$rows[$value['id']]['matrix_value'] : '-' ?></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php }?>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="23" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <p class="error" id="error_fee_plan_price"></p>
<?php } ?>