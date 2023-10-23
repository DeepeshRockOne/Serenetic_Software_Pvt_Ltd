<div class="coverageRow">
  <?php
    $tmp_index = strtotime($coverageRow['start_coverage_period']);
    $coverageTotal = 0.00;
  ?>
   <div class="m-b-40 coverage_<?=$coverageKey?>" style="display: none;">
      <div class="table-responsive">
         <table class="<?=$table_class?> table-small m-b-20">
            <caption class="bg_dark_primary text-white p-l-10">
                <?php
                    echo "P".$coverageKey.":";
                ?>
            </caption>
            <thead>
               <tr class="bg_light_primary">
                  <th>Product</th>
                  <th>Benefit Tier</th>
                  <th>Coverage Period</th>
                  <th class="text-right">Total</th>
               </tr>
            </thead>
            <tbody>
              <?php
                foreach ($coverageRow['ws_res'] as $wsPlan) {
                  if($wsPlan['is_approved_payment'] == true) {
                    continue;
                  }
                  $pricing_change = get_renewals_new_price($wsPlan['id'],false);
                  if($pricing_change && $pricing_change['pricing_changed'] == 'Y'){
                      $wsPlan['price'] = $pricing_change['new_ws_row']['price'];
                  }
                  $coverageTotal += $wsPlan['price'];
              ?>
                <tr>
                  <td><?=$wsPlan['product_name']?></td>
                  <td><?=$wsPlan['planTitle']?></td>
                  <td><?=date("m/d/Y",strtotime($wsPlan['start_coverage_period']))." - ".date("m/d/Y",strtotime($wsPlan['end_coverage_period']))?></td>
                  <td class="text-right"><?=displayAmount($wsPlan['price'],2)?></td>
                </tr>
              <?php
                }
              ?>
            </tbody>
         </table>
         
         <table class="table table-small m-b-20 br-a">
            <tbody>
              <tr>
                <?php if(!empty($coverageRow['coverage_service_fee']['total'])){ ?>
                <td class="bg_light_gray">
                  <div class="checkbox checkbox-custom mn">
                    <?php 
                      $coverageTotal += $coverageRow['coverage_service_fee']['total'];
                    ?>
                     <input type="checkbox" class="chk_service_fee js-switch" name="coverage_periods[<?=$tmp_index?>][service_fee]" data-service_fee="<?=$coverageRow['coverage_service_fee']['total']?>" data-coverage="<?=$tmp_index?>" value="Y" id="service_fees_res_<?=$tmp_index?>" checked="checked">
                     <label for="service_fees_res_<?=$tmp_index?>">Service Fee (<?=displayAmount($coverageRow['coverage_service_fee']['total'])?>)</label>
                   </div>
                </td>
                <?php } ?>
                <?php if($coverageKey == 1 && !empty($stepFeeRow)) { 
                    $coverageTotal += $stepFeeRow['price'];
                  ?>
                  <td class="bg_light_gray">
                    <div class="checkbox checkbox-custom mn">
                     <input type="checkbox" class="chk_enrollment_fee js-switch" name="coverage_periods[<?=$tmp_index?>][enrollment_fee]" value="Y" data-enrollment_fee="<?=$stepFeeRow['price']?>" id="enroll_fees_res_<?=$tmp_index?>" checked="checked" data-coverage="<?=$tmp_index?>">
                     <label for="enroll_fees_res_<?=$tmp_index?>">Enrollment Fee (<?=displayAmount($stepFeeRow['price'])?>)</label>
                   </div>
                  </td>
                <?php } ?>
                <td class="text-right bg_light_gray"><span class="fw700">Total :</span> &nbsp;&nbsp; <span class="total_amount_<?=$tmp_index?>"><?=displayAmount($coverageTotal,2)?></span></td>
             </tr>
            </tbody>
         </table>
      </div>
      
      <input type="hidden" name="coverage_periods[<?=$tmp_index?>][coverage_number]" value="<?=$coverageKey?>">
      <div class="row theme-form">
         <div class="col-sm-6">
            <div class="form-group">
               <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <div class="pr">
                     <input type="text" name="coverage_periods[<?=$tmp_index?>][payment_date]" class="form-control payment_date" aria-describedby="basic-addon1" value="<?=date("m/d/Y")?>" data-max_payment_date="<?=date("m/d/Y",strtotime($coverageRow['max_payment_date']))?>">
                     <label>Payment Date (MM/DD/YYYY)</label>
                  </div>
               </div>
              <p class="error"><span id="error_payment_date_<?=$tmp_index?>"></span></p>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <select class="form-control billing_profile" name="coverage_periods[<?=$tmp_index?>][billing_profile]" data-size="5">
                  <option data-hidden="true"></option>
                  <?php if(count($custBillRes) > 0) { ?>
                  <?php foreach ($custBillRes as $key => $billRow) { ?>
                  <option value="<?=$billRow['id']?>">
                  <?php 
                          if($billRow['payment_mode'] == "ACH"){
                              echo "ACH *".(substr($billRow['ach_account_number'],-4));
                          } else {
                              if ($billRow['card_type'] == 'Visa') {
                                $card_type = 'VISA';
                              
                              } elseif ($billRow['card_type'] == 'MasterCard') {
                                $card_type = 'MC';
                              
                              } elseif ($billRow['card_type'] == 'Discover') {
                                $card_type = 'DISC';
                              
                              } elseif ($billRow['card_type'] == 'American Express') {
                                $card_type = 'AMEX';
                              
                              } else {
                                $card_type = $billRow['card_type'];
                              }

                              echo $card_type." *".$billRow['card_no'];
                          }
                      ?>
                  </option>
                    <?php } ?>
                  <?php } ?>
                  <!-- <option value="new_payment_method">Add Payment Method</option> -->
               </select>
               <label>Payment Method</label>
               <p class="error"><span id="error_billing_profile_<?=$tmp_index?>"></span></p>
            </div>
         </div>
      </div>
   </div>
</div>