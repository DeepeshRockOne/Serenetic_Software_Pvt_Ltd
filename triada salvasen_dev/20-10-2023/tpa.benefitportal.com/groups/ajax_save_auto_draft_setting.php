<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$response = array();
$group_id = $_REQUEST['group_id'];
$company_id = !empty($_REQUEST['company_id'])?$_REQUEST['company_id']:0;
$company_name = $_REQUEST['company_name'];
$payment_mode = isset($_REQUEST['payment_mode'])?$_REQUEST['payment_mode']:'';
$billing_profile = isset($_REQUEST['billing_profile'])?$_REQUEST['billing_profile']:'';

$REAL_IP_ADDRESS = get_real_ipaddress();
$validate->string(array('required' => true, 'field' => 'payment_mode', 'value' => $payment_mode), array('required' => 'Please select Payment Mode'));
if (!$validate->getError("payment_mode")) {
	if($payment_mode != "Check") {
		$validate->string(array('required' => true, 'field' => 'billing_profile', 'value' => $billing_profile), array('required' => 'Please select Payment Method'));
	}
}

if ($validate->isValid()) {
	if($payment_mode == "Check" && empty($billing_profile)) {
		$cb_data = array(
			'customer_id' => $group_id,
			'company_id' => $company_id,
			'payment_mode' => 'Check',
			'country_id' => '231',
	        'country' => "United States",
	        'listbill_enroll' => 'Y',
	        'is_default' => 'N',
	        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
		);
		$billing_profile = $pdo->insert('customer_billing_profile',$cb_data);
	}

	$upd_data = array(
        'is_auto_draft_set' => 'N',
        'billing_id' => $billing_profile,
        'auto_draft_date' => NULL,
        'updated_at' => 'msqlfunc_NOW()'
    );

	if(!empty($company_id)) {
        $update_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $company_id
            )
        );
        $pdo->update("group_company", $upd_data, $update_where);
	} else {
        $update_where = array(
            'clause' => 'customer_id=:id',
            'params' => array(
                ':id' => $group_id
            )
        );
        $pdo->update("customer_group_settings", $upd_data, $update_where);
	}

    $response['msg'] = "Payment Method Saved Successfully";
    $response['status'] = 'success';
    
} else {
    $errors = $validate->getErrors();
    $response['status'] = 'fail';
    $response['errors'] = $errors;
}
echo json_encode($response);
dbConnectionClose();
exit;

