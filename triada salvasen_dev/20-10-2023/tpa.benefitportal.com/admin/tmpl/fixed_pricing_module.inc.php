
<table class="<?=$table_class?> mn bg_odd_even_table" id="benefit_tier_table">
  <input type="hidden" name="pricing_model" value="<?=checkIsset($pricing_model)?>">
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
          <input type="text" name="plan[<?= $plan['id'] ?>]" class="form-control priceControl" value="<?= !empty($priceArr) ? $priceArr[$plan['id']] : '' ?>" placeholder="10.00" >
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