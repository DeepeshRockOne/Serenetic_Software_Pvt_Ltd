<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
$group_id = !empty($_GET['group_id']) ? $_GET['group_id'] : 0;
$billing_id = !empty($_GET['billing_id']) ? $_GET['billing_id'] : 0;

$sqlGroup = "SELECT id,fname,lname,rep_id,business_name FROM customer where md5(id)=:group_id";
$resGroup = $pdo->selectOne($sqlGroup,array(":group_id"=>$group_id));

$sqlCompany="SELECT id,name,ein,location FROM group_company WHERE md5(group_id)=:group_id and is_deleted='N'";
$resCompany=$pdo->select($sqlCompany,array(":group_id"=>$group_id));


$selProfile = "SELECT *,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number,AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full FROM customer_billing_profile WHERE md5(customer_id)=:customer_id AND md5(id)=:id";
$whrProfile = array(":customer_id" => $group_id,":id"=>$billing_id);
$resProfile = $pdo->selectOne($selProfile, $whrProfile);

if(!empty($resProfile)){
	$billing_id = $resProfile['id'];
	$company_id = $resProfile['company_id'];
	$payment_mode = $resProfile['payment_mode'];
	$is_valid_address= $resProfile['is_valid_address'];

}

$billingSql="SELECT cbp.id,cbp.created_at,cbp.fname,cbp.lname,cbp.payment_mode,cbp.last_cc_ach_no,
        if(cbp.payment_mode ='CC',cbp.card_type,'ACH') as card_type,cbp.is_default,gc.name as company_name,
        	AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as CC_NO,
        	AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ACH_NO
            FROM customer_billing_profile cbp
            LEFT JOIN group_company gc ON (gc.id = cbp.company_id and gc.is_deleted='N')
            where md5(cbp.customer_id)=:group_id and cbp.is_deleted='N' AND cbp.payment_mode !='Check' 
            order by cbp.created_at  DESC";
$billingResult=$pdo->select($billingSql,array(":group_id"=>$group_id));

$billingRes = array();
$tmpBillingRes = array();
if(!empty($billingResult)){
	foreach ($billingResult as $key => $value) {
		if($value['payment_mode'] == "CC"){
			if(!in_array($value['CC_NO'], $tmpBillingRes)){
				$billingRes[$key]=$value;
				array_push($tmpBillingRes, $value['CC_NO']);
			}
		}else{
			if(!in_array($value['ACH_NO'], $tmpBillingRes)){
				$billingRes[$key]=$value;
				array_push($tmpBillingRes, $value['ACH_NO']);
			}
		}
	}
}

$resPayOptions = functionsList::getGroupPayOptions($resGroup['id']);
$display_cc_charge = false;
$display_check_charge = false;
$cc_charge_amount = 0;
$check_charge_amount = 0;
$available_payment = array();
if(!empty($resPayOptions)){
	$is_cc=$resPayOptions['is_cc'];
	$is_check=$resPayOptions['is_check'];
	$is_ach=$resPayOptions['is_ach'];

	$cc_additional_charge = $resPayOptions['cc_additional_charge'];
	$cc_charge_type = $resPayOptions['cc_charge_type'];
	$cc_charge = $resPayOptions['cc_charge'];
	
	$check_additional_charge = $resPayOptions['check_additional_charge'];
	$check_charge = $resPayOptions['check_charge'];

	$remit_to_address = $resPayOptions['remit_to_address'];

	if($is_cc=='Y' && $cc_additional_charge =='Y'){
		$display_cc_charge = true;
		if($cc_charge_type =='Fixed'){
			$cc_charge_amount = '$'.$cc_charge;
		}else{
			$cc_charge_amount = $cc_charge.'%';
		}
	}

	if($is_check=='Y' && $check_additional_charge =='Y'){
		$display_check_charge = true;
		$check_charge_amount = '$'.$check_charge;
	}

	if($is_cc == 'Y'){
		array_push($available_payment,'CC');
	}
	if($is_ach == 'Y'){
		array_push($available_payment,'ACH');
	}
	if($is_check == 'Y'){
		array_push($available_payment,'Check');
	}
}

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$template = 'groups_add_billing.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
