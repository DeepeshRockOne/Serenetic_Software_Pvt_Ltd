<?php
    // $coverage_total += $tmp_ws_row['price'];
$productPlan = $pdo->select("SELECT ppt.* FROM prd_plan_type ppt JOIN prd_matrix pm ON (pm.plan_type = ppt.id) WHERE pm.product_id = :product_id AND is_deleted = 'N' GROUP BY ppt.id ORDER BY order_by ASC", array(':product_id' => $tmp_prd_id));
$date_selection_options = get_tier_change_date_selection_options($tmp_cv_data['ws_id']);
// pre_print($date_selection_options);
?>
<div class="single_coverage" class="m-b-40">
    <h5 class="text-action"><?= getname('prd_main',$tmp_prd_id,'name','id') . " (" .getname('website_subscriptions',$tmp_cv_data['ws_id'],'website_id','id'). ")"?></h5>
    <div class="row theme-form">
        <div class="col-sm-12">
            <div class="form-group">
                <select class="form-control benefit_tier_change_drpdwn"
                        data-product_id="<?= $tmp_prd_id ?>"
                        data-customer_id="<?= getname('website_subscriptions',$tmp_cv_data['ws_id'],'customer_id','id') ?>"
                        data-web_id="<?= $tmp_cv_data['ws_id'] ?>"
                        name= "prd_plan_type[<?= $tmp_prd_id?>]" id="prd_plan_type<?= $tmp_prd_id?>">
                    <option value=""></option>
                    <?php foreach ($productPlan as $row) { ?>
                        <option value="<?php echo $row["id"]; ?>"><?php echo $row["title"]; ?></option>
                    <?php } ?>
                </select>
                <label>Benefit Tier</label>
                <p class="error"><span id="err_prd_plan_type_<?= $tmp_prd_id?>"></span></p>
            </div>
        </div>
        <div class="col-sm-12 dependents_div_<?=$tmp_prd_id ?>" style="display: none;">
            <div class="form-group">
                <select class="dependents se_multiple_select" name="dependents[<?=$tmp_prd_id ?>][]" id="dependents_<?=$tmp_prd_id ?>" multiple="multiple" data-customer_id="<?= getname('website_subscriptions',$tmp_cv_data['ws_id'],'customer_id','id') ?>" data-product_id="<?= $tmp_prd_id ?>" data-web_id="<?= $tmp_cv_data['ws_id'] ?>">
                    <!-- <option></option> -->
                </select>
                <label>Select Dependents</label>
                <p class="error"><span id="err_dependents_<?= $tmp_prd_id?>"></span></p>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <select class="form-control" name="effective_date[<?=$tmp_prd_id ?>]" id="effective_date">
                    <option></option>
                <?php foreach ($date_selection_options as $coverage) { ?>
                        <option value="<?=$coverage['value'];?>"><?=$coverage['text'];?></option>
                <?php } ?>
                </select>
                <label>Effective Date</label>
                <p class="error"><span id="err_effective_date_<?= $tmp_prd_id?>"></span></p>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <select class="form-control" name="paid_through_date[<?=$tmp_prd_id ?>]">
                    <option></option>
                    <?php foreach ($date_selection_options as $coverage) { ?>
                        <option value="<?=$coverage['end_coverage_period'];?>"><?=date('m/d/Y',strtotime($coverage['end_coverage_period']));?></option>
                    <?php } ?>
                </select>
                <label>Paid-Through Date</label>
                <p class="error"><span id="err_paid_through_date_<?= $tmp_prd_id?>"></span></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <p>Full Premium Amount: <span class="fw800" id="full_premium_amount_<?=$tmp_prd_id ?>">$0.00</span></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <p>COBRA Product Cost: <br> <span class="text-success fw800" id="cobra_product_cost_<?=$tmp_prd_id ?>">$0.00</span></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <p>COBRA Service Fee: <span id="cobra_service_fee_<?=$tmp_prd_id ?>">0.00%</span></p>
            </div>
        </div>
    </div>
</div>