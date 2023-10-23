<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$customer_id = !empty($_POST['id']) ? $_POST['id'] : '';
$location = 'group';

$selACH="SELECT id,fname,lname,bankname,ach_account_type,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number,AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number FROM customer_billing_profile WHERE is_direct_deposit_account='Y' AND md5(customer_id)=:customer_id";
$paramsACH = array(":customer_id"=>$customer_id);
$resACH = $pdo->selectOne($selACH,$paramsACH);

$achId = !empty($resACH['id']) ? $resACH['id'] : '';
$achfname = !empty($resACH['fname']) ? $resACH['fname'] : '';
$achlname = !empty($resACH['lname']) ? $resACH['lname'] : '';
$achBankname = !empty($resACH['bankname']) ? $resACH['bankname'] : '';
$achAccountType = !empty($resACH['ach_account_type']) ? $resACH['ach_account_type'] : '';
$achAccountNumber = !empty($resACH['ach_account_number']) ? $resACH['ach_account_number'] : '';
$achRoutingNumber = !empty($resACH['ach_routing_number']) ? $resACH['ach_routing_number'] : '';

include_once 'tmpl/member_hrm_tab.inc.php';
?>