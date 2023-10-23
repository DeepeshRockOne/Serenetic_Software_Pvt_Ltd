<?php
include_once 'layout/start.inc.php';
 
$billing_profile=!empty($_POST['billing_profile']) ? $_POST['billing_profile'] : 0;
$available_payment=!empty($_POST['available_payment']) ? $_POST['available_payment'] : array();
$displayPaymentOption = !empty($available_payment) ? explode(',',$available_payment) : array();

$selProfile = "SELECT *,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number,AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full FROM customer_billing_profile WHERE id=:id";
$whrProfile = array(":id"=>$billing_profile);
$resProfile = $pdo->selectOne($selProfile, $whrProfile);

if(!empty($resProfile)){
  $company_id = $resProfile['company_id'];
  $payment_type = $resProfile['payment_mode'];
    
  $account_type = $resProfile['ach_account_type'];
  $bankname = $resProfile['bankname'];

  $bank_rounting_number = $resProfile['ach_routing_number'];
  $bank_account_number = $resProfile['ach_account_number'];
  $bank_number_confirm = $bank_account_number;

  $card_type = $resProfile['card_type'];
  $card_number = $resProfile['card_no_full'];
  $expiry_month = $resProfile['expiry_month'];
  $expiry_year = $resProfile['expiry_year'];

  $name_on_card = $resProfile['fname'];
  $cvv_no = $resProfile['cvv_no'];
  $bill_address = $resProfile['address'];
  $bill_address_2 = $resProfile['address2'];
  $bill_city = $resProfile['city'];
  $bill_state = $resProfile['state'];
  $bill_zip = $resProfile['zip'];
  
  $last_cc_ach_no = $resProfile['last_cc_ach_no'];

}


ob_start();
?>
    <div class="m-b-25">
       <!-- <p class="fw500">Select Payment Method</p> -->
       <div class="m-b-15" style="<?=!empty($displayPaymentOption) && in_array('ACH',$displayPaymentOption) ? '' : 'display:none'?>">
          <label class="mn"><input type="radio" name="payment_type" value="ACH" <?= empty($payment_type) || $payment_type =='ACH' ? 'checked' : '' ?>>ACH</label>
       </div>
       <div class="mn" style="<?=!empty($displayPaymentOption) && in_array('CC',$displayPaymentOption) ? '' : 'display:none'?>">
          <label class="mn"><input type="radio" name="payment_type" value="CC" <?= !empty($payment_type) && $payment_type =='CC' ? 'checked' : '' ?>>CC</label>
       </div>
       <p class="error" id="error_payment_type"></p>
    </div>
    <div id="achDiv" style="<?= !empty($displayPaymentOption) && in_array('ACH',$displayPaymentOption) && (empty($payment_type) || $payment_type =='ACH') ? '' : 'display: none' ?>">
       <h4 class="m-t-0">Bank Draft Information</h4>
       <div class="row theme-form">
          <div class="col-sm-12">
             <div class="m-b-25">
                <label class="radio-inline"><input type="radio" name="account_type" value="checking" <?= !empty($account_type) && $account_type =='checking' ? 'checked' : '' ?>>Checking</label>
                <label class="radio-inline"><input type="radio" name="account_type" value="saving" <?= !empty($account_type) && $account_type =='saving' ? 'checked' : '' ?>>Savings</label>
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
                <input type="text" name="bank_rounting_number" class="form-control" value="" oninput="isValidNumber(this)" maxlength='9'>
                
                <label>Bank Routing Number<em>*</em> <?= !empty($bank_rounting_number) ? "( *".substr($bank_rounting_number,-4).")" : '' ?> </label>
                <p class="error" id="error_bank_rounting_number"></p>
                <input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?= !empty($bank_rounting_number) ? $bank_rounting_number : '' ?>">
             </div>
          </div>
          <div class="col-sm-6">
             <div class="form-group">
                <input type="text" name="bank_account_number" class="form-control" value="" oninput="isValidNumber(this)" maxlength='17'>
                <label>Bank Account Number<em>*</em> <?= !empty($last_cc_ach_no) ? "( *".$last_cc_ach_no .")" : '' ?></label>
                <p class="error" id="error_bank_account_number"></p>
                <input type="hidden" name="entered_account_number" id="entered_account_number" value="<?= !empty($bank_account_number) ? $bank_account_number : '' ?>">
             </div>
          </div>
       </div>
    </div>
    <div id="CCDiv" style="<?= !empty($displayPaymentOption) && in_array('CC',$displayPaymentOption) && (!empty($payment_type) && $payment_type =='CC')  ? '' : 'display: none' ?>">
      <h4 class="m-t-0 m-b-20">Credit Card  Information</h4>
      <div class="row theme-form">
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="name_on_card" id="name_on_card" value="<?= !empty($name_on_card) ? $name_on_card : '' ?>" class="form-control">
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
                 <input name="card_number" id="card_number" type="text" class="form-control" required="" value="" maxlength="16" oninput="isValidNumber(this)">
                 <label>Card Number <?= !empty($last_cc_ach_no) ? "( *".$last_cc_ach_no.")" : '' ?></label>
                 <p class="error" id="error_card_number"></p>
                 <input type="hidden" name="full_card_number" id="full_card_number" value="<?= !empty($card_number) ? $card_number : '' ?>">
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
              <input type="text" name="cvv" id="cvv" oninput="isValidNumber(this)" minlength="3" maxlength="4" value="<?= !empty($cvv_no) ? $cvv_no : '' ?>" class="form-control" >
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
            <input type="text" class="form-control" name="bill_address_2" id="bill_address_2" value="<?= !empty($bill_address_2) ? $bill_address_2 : '' ?>" onkeypress="return block_special_char(event)" />
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
<?php   
$result = array();  
$result['html'] = ob_get_clean();
$result['status'] = "success"; 

  
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>