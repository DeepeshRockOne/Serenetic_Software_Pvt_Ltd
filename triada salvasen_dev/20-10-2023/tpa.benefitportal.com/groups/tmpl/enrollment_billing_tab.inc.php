<h4 class="m-t-0 m-b-20">Operational Questions</h4>
   <div class="form-group height_auto">
      <p class="fw500 m-b-20">Select which type of users you would like to receive automated communications from the system when an action occurs</p>
      <div class="row">
         <div class="col-md-2 col-sm-4">
            <p>Group Adminstration</p>
            <div class="custom-switch">
               <label class="smart-switch">
                  <input type="checkbox" name="automated_communication[]" class="js-switch"  value="Group" <?= !empty($automated_communication) && in_array("Group",$automated_communication) ? 'checked' : '' ?> />
                  <div class="smart-slider round"></div>
               </label>
            </div>
         </div>
         <div class="col-md-2 col-sm-4">
            <p>Members</p>
            <div class="custom-switch">
               <label class="smart-switch">
                  <input type="checkbox" name="automated_communication[]" class="js-switch" value="Members" <?= !empty($automated_communication) && in_array("Members",$automated_communication) ? 'checked' : '' ?>/>
                  <div class="smart-slider round"></div>
               </label>
            </div>
         </div>
         <div class="col-md-2 col-sm-4">
            <p>Enrollees</p>
            <div class="custom-switch">
               <label class="smart-switch">
                  <input type="checkbox" name="automated_communication[]" class="js-switch" value="Enrollees" <?= !empty($automated_communication) && in_array("Enrollees",$automated_communication) ? 'checked' : '' ?>/>
                  <div class="smart-slider round"></div>
               </label>
            </div>
         </div>
         <p class="error" id="error_automated_communication"></p>
      </div>
   </div>
   <div class="form-group height_auto">
      <p class="fw500">Does your group have additional locations and/or companies under common ownership?</p>
      <div class="m-b-15">
         <label class="mn"><input type="radio" name="group_company" value="Y" <?= !empty($group_company) && $group_company == 'Y' ? 'checked' : '' ?>>Yes</label>
      </div>
      <div class="mn">
         <label class="mn"><input type="radio" name="group_company" value="N" <?= !empty($group_company) && $group_company == 'N' ? 'checked' : '' ?>>No</label>
      </div>
      <p class="error" id="error_group_company"></p>
   </div>
   <div id="group_company_div" style="<?= !empty($group_company) && $group_company == 'Y' ? '' : 'display:none' ?>">
      <div class="table-responsive m-b-10">
         <table class="<?=$table_class?>">
            <thead>
               <tr>
                  <th>Location/Company</th>
                  <th>EIN/FEIN</th>
                  <th>Location Code</th>
                  <th width="130px">Actions</th>
               </tr>
            </thead>
            <tbody id="groupCompanyDate">
                    
            </tbody>
         </table>
      </div>
      <p class="error" id="error_company_count"></p>
      <div class="form-group height_auto">
         <a href="javascript:void(0);" class="btn btn-info" id="add_group_company">+ Location/Company</a>
      </div>
      <div class="form-group height_auto">
         <p class="fw500">Should the billing be broken out by individual locations/companies?</p>
         <div class="m-b-15">
            <label class="mn"><input type="radio" name="billing_broken" value="Y" <?= !empty($billing_broken) && $billing_broken == 'Y' ? 'checked' : '' ?>>Yes</label>
         </div>
         <div class="mn">
            <label class="mn"><input type="radio" name="billing_broken" value="N" <?= !empty($billing_broken) && $billing_broken == 'N' ? 'checked' : '' ?>>No</label>
         </div>
         <p class="error" id="error_billing_broken"></p>
      </div>
   </div>

   <h4 class="m-t-0 m-b-20">Group Billing Preference <i class="fa fa-info-circle text-info" aria-hidden="true"></i></h4>
   <div class="row theme-form">
      <div class="col-sm-6">
         <div class="form-group">
            <select class="form-control" name="billing_type" id="billing_type">
               <option data-hidden="true"></option>
               <option value="individual" <?= !empty($billing_type) && $billing_type == 'individual' ? 'selected' : '' ?> <?= $tpa_for_billing =='Y' ? 'disabled' : '' ?>>Individual</option>
               <option value="list_bill" <?= !empty($billing_type) && $billing_type == 'list_bill' ? 'selected' : '' ?> <?= $tpa_for_billing =='Y' ? 'disabled' : '' ?>>List Bill</option>
               <!-- <option value="TPA"  <?= ((!empty($billing_type) && $billing_type == 'TPA') || ($tpa_for_billing =='Y')) ? 'selected' : '' ?><?= $tpa_for_billing == 'N' ? 'disabled' : '' ?>>TPA (Admin Only)</option> -->
            </select>
            <label>Select Group Billing</label>
            <p class="error" id="error_billing_type"></p>
         </div>
      </div>
   </div>
   <div id="list_bill_div" style="<?= !empty($billing_type) && $billing_type == 'list_bill' ? '' : 'display: none' ?>">
      <input type="hidden" name="available_payment" value="<?=!empty($available_payment) ? implode(',',$available_payment) : '' ?>">
      <div class="form-group height_auto">
         <p class="fw500">Select Payment Method</p>
         <div class="m-b-15" style="<?= $is_ach_available ? '' : 'display: none' ?>">
            <label class="mn"><input type="radio" name="payment_type" value="ACH" <?= empty($payment_type) || $payment_type =='ACH' ? 'checked' : '' ?>>ACH</label>
         </div>
         <div class="m-b-15" style="<?= $is_cc_available ? '' : 'display: none' ?>">
            <label class="mn"><input type="radio" name="payment_type" value="CC" <?= !empty($payment_type) && $payment_type =='CC' ? 'checked' : '' ?>>CC</label>
         </div>
         <div class="mn" style="<?= $is_check_available ? '' : 'display: none' ?>">
            <label class="mn"><input type="radio" name="payment_type" value="Check" <?= !empty($payment_type) && $payment_type =='Check' ? 'checked' : '' ?>>Check</label>
         </div>
         <p class="error" id="error_payment_type"></p>
      </div>
      <div id="achDiv" style="<?= $is_ach_available && (empty($payment_type) || $payment_type =='ACH') ? '' : 'display: none' ?>">
         <h4 class="m-t-0 m-b-20">Bank Draft Information</h4>
         <div class="row theme-form">
            <div class="col-sm-12">
               <div class="form-group height_auto m-b-20">
                  <label class="radio-inline"><input type="radio" name="account_type" value="checking" <?= !empty($account_type) && $account_type =='checking' ? 'checked' : '' ?>>Checking</label>
                  <label class="radio-inline"><input type="radio" name="account_type" value="savings" <?= !empty($account_type) && $account_type =='savings' ? 'checked' : '' ?>>Savings</label>
                  <p class="error" id="error_account_type"></p>
               </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="ach_name" value="<?=!empty($name_on_card) ? $name_on_card : ''?>" class="form-control">
                <label>Full Name<em>*</em></label>
                <p class="error" id="error_ach_name"></p>
              </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="bankname" class="form-control" value="<?= !empty($bankname) ? $bankname : '' ?>">
                  <label>Bank Name<em>*</em></label>
                  <p class="error" id="error_bankname"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="bank_rounting_number" class="form-control" value="<?= !empty($bank_rounting_number) ? $bank_rounting_number : '' ?>" maxlength='9' oninput="isValidNumber(this)">
                  <label>Bank Routing Number<em>*</em></label>
                  <p class="error" id="error_bank_rounting_number"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="bank_account_number" class="form-control" value="<?= !empty($bank_account_number) ? $bank_account_number : '' ?>" maxlength='17' oninput="isValidNumber(this)">
                  <label>Bank Account Number</label>
                  <p class="error" id="error_bank_account_number"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="bank_number_confirm" class="form-control" value="<?= !empty($bank_account_number) ? $bank_account_number : '' ?>" maxlength='17' oninput="isValidNumber(this)">
                  <label>Confirm Account Number</label>
                  <p class="error" id="error_bank_number_confirm"></p>
               </div>
            </div>
         </div>
      </div>
      <div id="CCDiv" style="<?= $is_cc_available && ( !empty($payment_type) && $payment_type =='CC')  ? '' : 'display: none' ?>">
        <h4 class="m-t-0 m-b-20">Credit Card  Information</h4>
        <div class="row theme-form">
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="name_on_card" value="<?= !empty($name_on_card) ? $name_on_card : '' ?>" class="form-control">
                <label>Full Name<em>*</em></label>
                <p class="error" id="error_name_on_card"></p>
              </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                   <select name="card_type" id="card_type" class="form-control" required="" data-error="Card Type is required">
                     <option value=""></option>
                     <option value="Visa" <?= !empty($card_type) && $card_type=="Visa"?'selected':''?>>
                       Visa
                     </option>
                     <option value="MasterCard" <?= !empty($card_type) && $card_type=="MasterCard"?'selected':''?>>
                       MasterCard
                     </option>
                     <option value="Discover" <?= !empty($card_type) && $card_type=="Discover"?'selected':''?>>
                       Discover
                     </option>
                     <option value="Amex" <?= !empty($card_type) && $card_type=="Amex"?'selected':''?>>
                       American Express
                     </option>
                   </select>
                   <label>Card Type<em>*</em></label>
                   <p class="error" id="error_card_type"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                   <input name="card_number" id="card_number" type="text" class="form-control" required="" value="<?=!empty($card_number) ? $card_number : ''?>" oninput="isValidNumber(this)" maxlength="16">
                   <label>Card Number<em>*</em></label>
                   <p class="error" id="error_card_number"></p>
               </div>
             </div>
            <div class="col-sm-6">
              <div class="form-group">
                  <?php
                    $date = '';
                    if(!empty($expiry_month) && !empty($expiry_year)){
                      $date = date('m/y',strtotime($expiry_month."/01/".$expiry_year));
                    }
                  ?>
                  <input type="text" name="expiration" id="expiration" value="<?=$date?>" class="form-control">
                  <label>Expiration Date<em>*</em></label>
                  <p class="error" id="error_expiration"></p>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group height_auto m-b-15">
                <input type="text" name="cvv" id="cvv" oninput="isValidNumber(this)" minlength="3" maxlength="4" value="<?= !empty($cvv_no) ? $cvv_no :'' ?>" class="form-control" >
                <label>CVV*</label>
                <p class="error" id="error_cvv"></p>
              </div>
            </div>
        </div>
        <h4 class="m-t-0 m-b-20">Billing Address</h4>
        <div class="row theme-form">
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="bill_address" id="bill_address" class="form-control" value="<?= !empty($bill_address) ? $bill_address : '' ?>" placeholder="">
              <label>Address<em>*</em></label>
              <p class="error" id="error_bill_address"></p>
              <input type="hidden" name="old_bill_address" value="<?= !empty($bill_address) ? $bill_address : '' ?>">
            </div>
          </div>
          <div class="col-sm-6">
             <div class="form-group">
                <input type="text" name="bill_address_2" id="bill_address_2" class="form-control" value="<?= !empty($bill_address_2) ? $bill_address_2 : '' ?>" onkeypress="return block_special_char(event)" placeholder="">
                <label>Address 2 (suite, apt)</label>
                <p class="error" id="error_bill_address_2"></p>
             </div>
        </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="bill_city" class="form-control" id="bill_city" value="<?= !empty($bill_city) ? $bill_city : '' ?>">
              <label>City<em>*</em></label>
              <p class="error" id="error_bill_city"></p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <select class="form-control" name="bill_state" id="bill_state">
                <option data-hidden="true"></option>
                <!-- <option>Alabama</option> -->
                <?php if(!empty($allStateRes)){
                  foreach($allStateRes as $states){ ?>
                    <option value="<?=$states['name']?>" <?=!empty($bill_state) &&  $bill_state == $states['name'] ? 'selected="selected"': ''?>><?=$states['name']?></option>
                <?php } } ?>
              </select>
              <label>State<em>*</em></label>
              <p class="error" id="error_bill_state"></p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="bill_zip" maxlength="5" id="bill_zip" class="form-control" oninput="isValidNumber(this)" value="<?= !empty($bill_zip) ? $bill_zip : '' ?>">
              <label>Zip/Postal Code<em>*</em></label>
              <p class="error" id="error_bill_zip"></p>
              <input type="hidden" name="old_bill_zip" value="<?= !empty($bill_zip) ? $bill_zip : '' ?>">
            </div>
          </div>
        </div>
      </div>
   </div>
   <div class="text-right">
      <a href="javascript:void(0);" class="btn btn-action next_tab_button" data-step="2">Next</a>
      <a href="javascript:void(0);" class="btn red-link cancel_tab_button">Cancel</a>
   </div>
   <div class="text-right m-t-20">
     <span><small>Last Saved Timestamp : <?=$last_saved?></small></span>
   </div>