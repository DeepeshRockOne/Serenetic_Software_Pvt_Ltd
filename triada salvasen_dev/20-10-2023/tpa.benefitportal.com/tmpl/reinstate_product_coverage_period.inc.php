<?php
$tmp_index = $coverageKey;
$coverage_total = 0.00;
?>
<div id="single_coverage" class="m-b-40">
    <div class="table-responsive">
        <table class="<?= $table_class ?> table-small m-b-20">
            <caption class="bg_dark_primary text-white p-l-10">
                <?php
                echo "P".$coverage_period_row['renew_count'].": ";
                echo date("m/d/Y", strtotime($coverage_period_row['start_coverage_period']));
                echo " - ";
                echo date("m/d/Y", strtotime($coverage_period_row['end_coverage_period']));
                ?>
            </caption>
            <thead>
            <tr class="bg_light_primary">
                <th style="width: 350px;">Product</th>
                <th >Coverage</th>
                <th class="text-right">Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                if ($tmp_ws_row['is_approved_payment'] == true) {
                    continue;
                }
                $pricing_change = get_renewals_new_price($tmp_ws_row['id'],false);
                if($pricing_change && $pricing_change['pricing_changed'] == 'Y'){
                    $tmp_ws_row['price'] = $pricing_change['new_ws_row']['price'];
                }
                $coverage_total += $tmp_ws_row['price'];
                ?>
                <tr>
                    <td><?= $tmp_ws_row['product_name'] ?></td>
                    <td class=""><?= $tmp_ws_row['prd_plan_type_title'] ?></td>
                    <td class="text-right"><?= displayAmount($tmp_ws_row['price'], 2) ?></td>
                </tr>
                <?php
            }
            ?>
            </tr>
            </tbody>
        </table>
        <table class="table table-small m-b-20 br-a">
            <tbody>
            <tr>
                <td colspan="2" class="bg_light_gray">
                    <div class="row">
                        <?php 
                        if (!empty($linked_Fee)) {
                            $already_Fee = array();
                            foreach ($linked_Fee as $key => $value) {
                                $fee_ws_row = $pdo->selectOne("SELECT id,price from website_subscriptions where customer_id = :customer_id and product_id = :product_id", array(':product_id' => $value['product_id'], ':customer_id' => $customer_id));
                                if (!empty($fee_ws_row) && !in_array($fee_ws_row['id'],$already_Fee)) {
                                    $already_Fee[] = $fee_ws_row['id'];
                                    $coverage_total += $fee_ws_row['price'];
                                    ?>
                                    <div class="col-xs-6">
                                        <div class="checkbox checkbox-custom mn">
                                            <input type="checkbox" checked="checked" name="fees[<?= $tmp_index ?>][]"
                                                   value="<?= $fee_ws_row['id'] ?>" class="chk_fee"
                                                   data-fee="<?= $fee_ws_row['price'] ?>" data-coverage="<?=$coverage_cnt?>"
                                                   id="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                            <label for="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                                <?= $value['product_name'] ?> Fee (<?= isset($fee_ws_row['price']) ? displayAmount($fee_ws_row['price']) : "" ?>)</label>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }

                        if (!empty($membership_Fee)) {
                            $already_Fee = array();
                            foreach ($membership_Fee as $key => $value) {

                                $fee_ws_row = $pdo->selectOne("SELECT id,price from website_subscriptions where customer_id = :customer_id and product_id = :product_id", array(':product_id' => $value['product_id'], ':customer_id' => $customer_id));
                                if (!empty($fee_ws_row) && !in_array($fee_ws_row['id'],$already_Fee)) {
                                    $already_Fee[] = $fee_ws_row['id'];
                                    $coverage_total += $fee_ws_row['price'];
                                    ?>
                                    <div class="col-xs-6">
                                        <div class="checkbox checkbox-custom mn">
                                            <input type="checkbox" checked="checked" name="fees[<?= $tmp_index ?>][]"
                                                   value="<?= $fee_ws_row['id'] ?>" class="chk_fee"
                                                   data-fee="<?= $fee_ws_row['price'] ?>" data-coverage="<?=$coverage_cnt?>"
                                                   id="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                            <label for="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                                <?= $value['product_name'] ?> Fee (<?= isset($fee_ws_row['price']) ? displayAmount($fee_ws_row['price']) : "" ?>)</label>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }

                        if ($is_new_order == 'Y' && !empty($healthyStepFee)) {
                            $already_Fee = array();
                            $fee_ws_row = $pdo->selectOne("SELECT id,price from website_subscriptions where customer_id = :customer_id and product_id = :product_id", array(':product_id' => $healthyStepFee['product_id'], ':customer_id' => $customer_id));
                            if (!empty($fee_ws_row) && !in_array($fee_ws_row['id'],$already_Fee)) {
                                $already_Fee[] = $fee_ws_row['id'];
                                $coverage_total += $fee_ws_row['price'];
                                ?>
                                <div class="col-xs-6">
                                    <div class="checkbox checkbox-custom mn">
                                        <input type="checkbox" checked="checked" name="healthystepfee[<?= $tmp_index ?>][]"
                                               value="<?= $fee_ws_row['id'] ?>" class="chk_fee"
                                               data-fee="<?= $fee_ws_row['price'] ?>" data-coverage="<?=$coverage_cnt?>"
                                               id="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                        <label for="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                            <?= $healthyStepFee['product_type'] ?> Fee (<?= isset($fee_ws_row['price']) ? displayAmount($fee_ws_row['price']) : "" ?>)</label>
                                    </div>
                                </div>
                                <?php
                            }
                        }

                        if (!empty($serviceFee)) {
                            $already_Fee = array();
                            foreach ($serviceFee as $key => $value) {
                                $fee_ws_row = $pdo->selectOne("SELECT id,price from website_subscriptions where customer_id = :customer_id and product_id = :product_id", array(':product_id' => $value['product_id'], ':customer_id' => $customer_id));
                                if (!empty($fee_ws_row) && !in_array($fee_ws_row['id'],$already_Fee)) {
                                    $already_Fee[] = $fee_ws_row['id'];
                                    $coverage_total += $fee_ws_row['price'];
                                    ?>
                                    <div class="col-xs-6">
                                        <div class="checkbox checkbox-custom mn">
                                            <input type="checkbox" checked="checked" name="servicefee[<?= $tmp_index ?>][]"
                                                   value="<?= $fee_ws_row['id'] ?>" class="chk_fee"
                                                   data-fee="<?= $fee_ws_row['price'] ?>" data-coverage="<?=$coverage_cnt?>"
                                                   id="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                            <label for="service_fees_res_<?=$coverage_cnt?>_<?= $fee_ws_row['id'] ?>">
                                                <?= $value['product_type'] ?> Fee (<?= isset($fee_ws_row['price']) ? displayAmount($fee_ws_row['price']) : "" ?>)</label>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </td>
                <td class="text-right bg_light_gray">
                    <span class="fw700">Total :</span> &nbsp;&nbsp;
                    <span class="coverage_total_amount_<?=$coverage_cnt?>" data-coverage_total_amount="<?=$coverage_total?>"><?= displayAmount($coverage_total)?></span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row theme-form">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <div class="pr">
                        <input type="text" name="coverage_periods[<?= $tmp_index ?>][payment_date]"
                               class="form-control payment_date" placeholder="DD / MM / YYYY"
                               aria-describedby="basic-addon1"
                               value="<?= date("m/d/Y") ?>" <?= $coverage_cnt == 1 ? "readonly" : "" ?>>
                        <label>Payment Date (MM/DD/YYYY)</label>
                        <p class="error"><span id="error_payment_date_<?= $tmp_index ?>"></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <select class="form-control billing_profile"
                        name="coverage_periods[<?= $tmp_index ?>][billing_profile]" data-size="5">
                    <option data-hidden="true"></option>
                    <?php if ($is_list_bill_enroll == "Y") { ?>
                        <option value="List Bill" selected="selected">List Bill</option>
                    <?php }else if (count($cb_res) > 0) { ?>
                        <?php foreach ($cb_res as $key => $cb_row) { ?>
                            <option value="<?= $cb_row['id'] ?>">
                                <?php
                                if ($cb_row['payment_mode'] == "ACH") {
                                    echo "ACH *" . (substr($cb_row['ach_account_number'], -4));
                                } else {
                                    if ($cb_row['card_type'] == 'Visa') {
                                        $card_type = 'VISA';

                                    } elseif ($cb_row['card_type'] == 'MasterCard') {
                                        $card_type = 'MC';

                                    } elseif ($cb_row['card_type'] == 'Discover') {
                                        $card_type = 'DISC';

                                    } elseif ($cb_row['card_type'] == 'American Express') {
                                        $card_type = 'AMEX';

                                    } else {
                                        $card_type = $cb_row['card_type'];
                                    }

                                    echo $card_type . " *" . $cb_row['card_no'];
                                }
                                ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <label>Payment Method</label>
                <p class="error"><span id="error_billing_profile_<?= $tmp_index ?>"></span></p>
            </div>
        </div>
    </div>
</div>