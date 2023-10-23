<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
include_once dirname(__DIR__) . "/includes/function.class.php";
include_once dirname(__DIR__) . "/includes/enrollment_dates.class.php";
include_once dirname(__DIR__) . "/includes/policy_setting.class.php";
include_once dirname(__DIR__) . "/includes/member_setting.class.php";
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$validate = new Validation();
$functionClass = new functionsList();
$commObj = new Commission();
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();
$policySetting = new policySetting();
$response = array();

$REAL_IP_ADDRESS = get_real_ipaddress();
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : "";
$order_sql = "SELECT o.*,c.fname,c.lname,c.rep_id,ob.card_type,ob.card_no,now() as currentTime 
					FROM orders o 
					JOIN customer c ON(o.customer_id = c.id) 
					JOIN order_billing_info ob ON(o.id = ob.order_id) 
					WHERE o.id=:id AND o.status IN('Payment Approved','Pending Settlement')";
$order_where = array(":id" => $order_id);
$order_row = $pdo->selectOne($order_sql, $order_where);

if(!$order_row){
	$response['status'] = 'fail_attempt';
	$response['failed_message'] = "Order Billing information not found";
	echo json_encode($response);
	exit();
}

$od_sql = "SELECT od.*,w.id as ws_id,w.eligibility_date,w.customer_id,w.status,w.next_purchase_date
			FROM order_details od
			JOIN website_subscriptions w on(w.id=od.website_id)
			WHERE od.order_id=:order_id AND od.is_deleted='N' GROUP BY od.id";
$od_where = array(":order_id" => $order_row['id']);
$od_res = $pdo->select($od_sql,$od_where);


$non_refunded_products = array();
foreach ($od_res as $key => $od_row) {
	if($od_row['is_refund'] == "N") {
		$non_refunded_products[] = $od_row;
	}
}


$select_product = array();

$reversal_type = isset($_POST["reversal_type"]) ? $_POST["reversal_type"] : "";
$refund_reason = isset($_POST["refund_reason"]) ? $_POST["refund_reason"] : "";
$is_partial_refund = false;

$validate->string(array('required' => true, 'field' => 'refund_reason', 'value' => $refund_reason), array('required' => 'Reason is required'));
// $void_transaction = !empty($_POST["void_transaction"]) ? $_POST["void_transaction"] : "No";
$void_regenerate_order = "No";

if($reversal_type == "Void"){
	$refund_type = $order_row['payment_type'];
	$refund_amount = $order_row['grand_total'];
	$future_billing = "N";
	$inactive_member = isset($_POST['inactive_member_void']) && $_POST['inactive_member_void'] == 'Y' ? "Yes" : "No";
	$reverse_commission = "Yes";
	$note = $refund_reason;
	$term_product = array();
	$select_product = isset($_POST['void_term_chk']) ? $_POST['void_term_chk'] : array();

	$term_product = isset($_POST['void_termination_date']) ? $_POST['void_termination_date'] : array();

	if($inactive_member == 'Yes'){
		if(empty($select_product)){
			$validate->setError('void_prd_common',"Please select any product");
		}

		$future_billing = "Y";

		foreach ($select_product as $key => $value) {
			$validate->string(array('required' => true, 'field' => 'void_termination_date_' .$key , 'value' => $term_product[$key]), array('required' => 'Please select termination date'));
		}
	}

	foreach ($od_res as $key => $value) {
		$select_product[$value['plan_id']] = $value;
		if($inactive_member != 'Yes'){
			$term_product[$value['plan_id']] = $value['eligibility_date'];
		}
	}
	

} else if($reversal_type == "Refund") {

	$refund_type = $order_row['payment_type'];
	$refund_amount = isset($_POST['refund_amount']) ? $_POST['refund_amount'] : "";
	// $future_billing = isset($_POST['future_billing']) && $_POST['future_billing'] == 'Y' ? "Yes" : "No";
	$future_billing = "N";
	$inactive_member = isset($_POST['inactive_member']) && $_POST['inactive_member'] == 'Y' ? "Yes" : "No";
	$reverse_commission = isset($_POST['reverse_commission']) && $_POST['reverse_commission'] == 'Y' ? "Yes" : "No";

	$select_product = isset($_POST['term_chk']) ? $_POST['term_chk'] : array();

	$term_product = isset($_POST['termination_date']) ? $_POST['termination_date'] : array();

	$validate->string(array('required' => true, 'field' => 'refund_amount', 'value' => $refund_amount), array('required' => 'Amount is required'));
	$refund_by_check = isset($_POST['chk_refund_by_check']) && $_POST['chk_refund_by_check'] == 'Y' ? "Yes" : "No";
	$check_id = isset($_POST['check_id']) ? $_POST['check_id'] : "";

	if($refund_by_check == 'Yes'){
		$validate->string(array('required' => true, 'field' => 'check_id', 'value' => $check_id), array('required' => 'Check ID is required'));
		$refund_type = 'Cheque';
	}

	if(empty($select_product)){
		$validate->setError('prd_common',"Please select any product");
	}
	if($inactive_member == 'Yes'){
		foreach ($select_product as $key => $value) {
			$validate->string(array('required' => true, 'field' => 'termination_date_' .$key , 'value' => $term_product[$key]), array('required' => 'Please select termination date'));

			$future_billing = 'Y';
		}
	}

	
	if (count($non_refunded_products) != count($select_product)) {
		$is_partial_refund = true;
	}

}

foreach ($select_product as $k => $v) {
	$select_product[$k] = get_product_id_by_plan_id($k);
}

$refund_error_message = "";

if(in_array($reversal_type, array('Chargeback','Payment Return'))){

	if ($validate->isValid()) {

		if ($reversal_type=="Chargeback"){
	        $transactionInsId= $functionClass->transaction_insert($order_row['id'],'Debit','Chargeback','Transaction Chargeback',0,array('reason' => $refund_reason));

	        foreach ($od_res as $order) {
	        	$update_od_data = array(
					'is_chargeback' => "Y",
					'updated_at' => 'msqlfunc_NOW()',
				);
				$update_od_where = array("clause" => 'id=:id', 'params' => array(':id'=>$order['id']));
				$pdo->update("order_details", $update_od_data, $update_od_where);
	        }

	        

	    }else if ($reversal_type=="Payment Return"){
	        $transactionInsId=$functionClass->transaction_insert($order_row['id'],'Debit','Payment Returned','Transaction Returned',0,array('reason' => $refund_reason));

	        foreach ($od_res as $order) {
	        	$update_od_data = array(
					'is_payment_return' => "Y",
					'updated_at' => 'msqlfunc_NOW()',
				);
				$update_od_where = array("clause" => 'id=:id', 'params' => array(':id'=>$order['id']));
				$pdo->update("order_details", $update_od_data, $update_od_where);
	        }
	    }

	    $update_params['order_comments'] = makeSafe($refund_reason);
	    $update_params['status'] = ($reversal_type == "Payment Return" ? "Payment Returned" : $reversal_type);

	    $update_where = array(
	        'clause' => 'id = :id',
	        'params' => array(
	            ':id' => makeSafe($order_row['id'])
	        )
	    );


	    $pdo->update("orders", $update_params, $update_where);

	    $extra = array('customer_id' => $order_row['customer_id'],'is_renewal' => 'Y');

	    $member_status = $memberSetting->get_status_by_order_status($reversal_type,"","",$extra);

	    $extra_params['note'] = $refund_reason;
	    $extra_params['transaction_tbl_id'] = $transactionInsId['id'];
	    $commObj->reverseOrderCommissions($order_row['id'],$extra_params);

	    $payable_params=array(
	        'payable_type'=>'Reverse_Vendor',
			'type'=>'Vendor',
			'transaction_tbl_id' => $transactionInsId['id'],
	    );

	    $payable= $functionClass->payable_insert($order_row['id'],0,0,0,$payable_params);

	    $insert_params = array(
			'admin_id' => (!empty($_SESSION['admin']['id'])?$_SESSION['admin']['id']:''),
			'order_id' => $order_row['id'],
			'site_load' => $order_row['site_load'],
			'return_type' => "Full",
			'refund_amount' => $order_row['grand_total'],
			'order_comments' => $refund_reason,
			'refund' => 'N',
			'refund_by' => 'Admin',
			'refund_status' => 'success',
			'order_status' => $reversal_type,
			'auth_id' => 0,
			'payment_processor_res' => "",
			'auth_error' => "",
			'is_plan_cancel' => 'Y',
			'transaction_id' => $transactionInsId['id'],
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
			'created_at' => 'msqlfunc_NOW()',
			'updated_at' => 'msqlfunc_NOW()',
		);
		$return_order_id = $pdo->insert('return_orders', $insert_params);

		foreach ($od_res as $order) {
			$update_ws_data = array(
                'termination_reason' => $refund_reason,
                'updated_at' => 'msqlfunc_NOW()',
            );
            $update_ws_where = array("clause" => 'id=:id', 'params' => array(':id' => $order['ws_id']));
            $pdo->update("website_subscriptions", $update_ws_data, $update_ws_where);
		}

	    if ($reversal_type == 'Chargeback') {
	        $customer_update_params = array(
	            'status' => $member_status['member_status'],
	            'updated_at' => 'msqlfunc_NOW()'
	        );

	        $customer_update_where = array(
	            'clause' => 'id = :id',
	            'params' => array(
	                ':id' => makeSafe($order_row['customer_id'])
	            )
	        );
	        $old_status = getname('customer', $order_row['customer_id'], 'status', 'id');
	        $pdo->update("customer", $customer_update_params, $customer_update_where);

	        $order_ws_ids = $order_row['subscription_ids'];
	        $wquery = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id IN ($order_ws_ids)";
	        $wrow = $pdo->select($wquery);

	        if (count($wrow) > 0) {
	            foreach ($wrow as $key => $value) {

	            	$term_product = true;
	                
	                if($term_product){
	                    $termination_date=$enrollDate->getTerminationDate($value['id']);

	                    $extra_params = array();
			            $extra_params['location'] = "return_order";
			            $extra_params['activity_feed_flag'] = "change_order_status";
			            $termination_reason = "Chargeback";
			            $policySetting->setTerminationDate($value['id'],$termination_date,$termination_reason,$extra_params);
	                }
	            }
	        }

	        $description['ac_message'] = array(
			    'ac_red_1' => array(
			      'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
			      'title' => $_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' => ' Chargebacked ',
			    'ac_red_2' => array(
			      'href' => $ADMIN_HOST . '/order_receipt.php?orderId=' . md5($order_row['id']),
			      'title' => $order_row['display_id'],
			    )
			  );

	        $desc = json_encode($description);
  			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $order_row['customer_id'], 'Customer', 'Chargebacked Order', $_SESSION['admin']['name'], "", $desc);
	        // $activity_arr = array('admin_id'=>$_SESSION['admin']['id'],'old_status' => $old_status, 'new_status' => 'Inactive Member Chargeback');

	        // activity_feed(3, $order_row['customer_id'], 'Customer', $order_row['customer_id'], 'Customer', 'Member Status Changed', $_SESSION['admin']['name'], $_SESSION['admin']['lname'],json_encode($activity_arr));
	    
	    }else if ($reversal_type == 'Payment Return') {

	        //  Update Customer table status
	        $customer_update_params = array(
	            'status' => $member_status['member_status'],
	            'updated_at' => 'msqlfunc_NOW()'
	        );

	        $customer_update_where = array(
	            'clause' => 'id = :id',
	            'params' => array(
	                ':id' => makeSafe($order_row['customer_id'])
	            )
	        );
	        $old_status = getname('customer', $order_row['customer_id'], 'status', 'id');
	        $pdo->update("customer", $customer_update_params, $customer_update_where);
	        //  Update Customer table status

	        $order_ws_ids = $order_row['subscription_ids'];
	        $wquery = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id IN ($order_ws_ids)";
	        $wrow = $pdo->select($wquery);

	        if (count($wrow) > 0) {
	            foreach ($wrow as $key => $value) {
	                $term_product = true;
	                
	                if($term_product){
	                    $termination_date=$enrollDate->getTerminationDate($value['id']);

	                    $extra_params = array();
			            $extra_params['location'] = "return_order";
			            $extra_params['activity_feed_flag'] = "change_order_status";
			            $termination_reason = "Payment Return";
			            $policySetting->setTerminationDate($value['id'],$termination_date,$termination_reason,$extra_params);
	                }
	            }
	        }

	        $description['ac_message'] = array(
			    'ac_red_1' => array(
			      'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
			      'title' => $_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' => ' Payment Returned ',
			    'ac_red_2' => array(
			      'href' => $ADMIN_HOST . '/order_receipt.php?orderId=' . md5($order_row['id']),
			      'title' => $order_row['display_id'],
			    )
			  );

	        $desc = json_encode($description);
  			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $order_row['customer_id'], 'Customer', 'Payment Returned', $_SESSION['admin']['name'], "", $desc);
	    }

	    $response['status'] = 'success';
	    $response['msg'] = 'Request Proceed Successfully';
	    setNotifySuccess('Request Proceed Successfully');

    } else {
		$response['errors'] = $validate->getErrors();	
	}

}else if(in_array($reversal_type, array('Void','Refund'))){
	if ($validate->isValid()) {

		if($refund_type != 'Cheque'){
			$checkProcessorStatus = $pdo->selectOne("SELECT id FROM payment_master WHERE status IN('Active','Inactive') AND id=:id AND is_deleted='N'",array(":id"=>$order_row['payment_master_id']));
			if(!$checkProcessorStatus){
				$response['status'] = 'processor_inactive';
				echo json_encode($response);
				exit();
			}
		}
		
		$product_ids = "";
		$plan_ids = "";

		$cust_sql = "SELECT * FROM customer WHERE id=:cust_id";
		$cust_row = $pdo->selectOne($cust_sql, array(':cust_id' => $order_row['customer_id']));

		$spon_sql = "SELECT * FROM customer WHERE id=:sponsor_id";
		$spons_row = $pdo->selectOne($spon_sql, array(':sponsor_id' => $cust_row['sponsor_id']));

		if (in_array($refund_type, array("CC", "ACH"))) {
			$bill_sql = "SELECT *,
	                    AES_DECRYPT(ach_routing_number,'".$CREDIT_CARD_ENC_KEY."')as ach_routing_number,
	                    AES_DECRYPT(ach_account_number,'".$CREDIT_CARD_ENC_KEY."')as ach_account_number,
	                    AES_DECRYPT(card_no_full,'".$CREDIT_CARD_ENC_KEY."')as cc_no
	                    FROM order_billing_info WHERE order_id=:id ORDER BY id DESC";
			$bill_row = $pdo->selectOne($bill_sql, array(":id" => $order_row['id']));

			
			$cc_params = array();
			$cc_params['order_id'] = $order_row['display_id'];
			$cc_params['customer_id'] = $cust_row['rep_id'];
			$cc_params['amount'] = $refund_amount;
			$cc_params['transaction_id'] = $order_row['transaction_id'];
			$cc_params['description'] = "Order Refund";
			$cc_params['firstname'] = $bill_row['fname'];
			$cc_params['lastname'] = $bill_row['lname'];
			$cc_params['address1'] = $bill_row['address'];
			$cc_params['city'] = $bill_row['city'];
			$cc_params['state'] = $bill_row['state'];
			$cc_params['zip'] = $bill_row['zip'];
			$cc_params['country'] = 'USA';
			$cc_params['phone'] = $bill_row['phone'];
			$cc_params['email'] = $bill_row['email'];
			$cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
			$cc_params['processor'] = 'Authorize.net';
			$payment_processor = 'Authorize.net';
			
			if ($reversal_type == "Void") {
				if($bill_row['cc_no'] == "4111111111111114") {
					$payment_res['status'] = 'Success';
					$payment_res['transaction_id'] = 0;
				} else {
					if($refund_amount == 0) {
						$payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
					} else {
						$api = new CyberxPaymentAPI();
						if($bill_row['payment_mode'] == "CC") {
							$payment_res = $api->processVoid($cc_params,$order_row['payment_master_id']);
						} else {
							$payment_res = $api->processVoidACH($cc_params,$order_row['payment_master_id']);
						}
					}
				}
			} else {
				if ($refund_type == 'CC') {
					$cc_params['ccnumber'] = $bill_row['cc_no'];
					$cc_params['card_type'] = $bill_row['card_type'];
					$cc_params['ccexp'] = str_pad($bill_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($bill_row['expiry_year'], -2);
					$cc_params['cvv'] = $bill_row['cvv_no'];

					if($refund_amount == 0) {
						$payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
					} else {
						if ($cc_params['ccnumber'] == '4111111111111114') {
							$payment_res['status'] = 'Success';
							$payment_res['transaction_id'] = 0;
						} else {
							$api = new CyberxPaymentAPI();
							$payment_res = $api->processRefund($cc_params,$order_row['payment_master_id']);
						}
					}

				} else {
					$cc_params['ach_account_type'] = $bill_row['ach_account_type'];
					$cc_params['ach_routing_number'] = $bill_row['ach_routing_number'];
					$cc_params['ach_account_number'] = $bill_row['ach_account_number'];
					$cc_params['name_on_account'] = $bill_row['fname'] . ' ' . $bill_row['lname'];
					$cc_params['bankname'] = $bill_row['bankname'];

					if($refund_amount == 0) {
						$payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
					} else {
						$api = new CyberxPaymentAPI();
						$payment_res = $api->processRefundACH($cc_params,$order_row['payment_master_id']);
					}
				}
			}
			

			if ($payment_res['status'] == 'Success') {
				$refund_status = "Success";
				$is_refund = 'Y';
				$txn_id = $payment_res['transaction_id'];
				$payment_response = $payment_res;
			} else {
				$is_refund = 'N';
				$refund_status = "Failed";
				$cc_params['order_type'] = 'Order Refund';
				$cc_params['browser'] = $BROWSER;
				$cc_params['os'] = $OS;
				$cc_params['req_url'] = $REQ_URL;
				$cc_params['err_text'] = $payment_res['message'];
				$refund_error = $payment_res['message'];
				$refund_error_message = $refund_error;
				$txn_id = $payment_res['transaction_id'];
				$functionClass->credit_card_decline_log($order_row['customer_id'], $cc_params, $payment_res);
			}
		} else {
			$is_refund = "Y";
			$refund_status = "Success";
			$txn_id = $check_id;

			$payment_res['status'] = 'Success';
			$payment_res['transaction_id'] = $check_id;
		}

		if ($is_refund == 'Y') {

			$insert_params = array(
				'admin_id' => (!empty($_SESSION['admin']['id'])?$_SESSION['admin']['id']:''),
				'order_id' => $order_row['id'],
				'site_load' => $order_row['site_load'],
				'return_type' => $is_partial_refund ? 'Partial' : "Full",
				'refund_amount' => $refund_amount,
				'order_comments' => $refund_reason,
				'refund' => $is_refund,
				'refund_by' => $refund_type,
				'refund_status' => $refund_status,
				'order_status' => $reversal_type=='Refund' ? 'Refund' : 'Void',
				'auth_id' => $txn_id,
				'payment_processor_res' => $payment_res ? json_encode($payment_res) : "" ,
				'auth_error' => (!empty($refund_error)?$refund_error:''),
				'is_plan_cancel' => $future_billing,
				'transaction_id' => 0,
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'created_at' => 'msqlfunc_NOW()',
				'updated_at' => 'msqlfunc_NOW()',
			);
			$return_order_id = $pdo->insert('return_orders', $insert_params);

			//************************ insert transaction code start ***********************
			$other_params=array("debit_amount"=>$refund_amount,"transaction_id"=>$txn_id,'transaction_response'=>$payment_res);
			$other_params['refunded_products'] = $select_product;
			$other_params['refund_id'] = $return_order_id;
			$other_params['reason'] = $refund_reason;
			
			if($reversal_type=='Refund'){
				$transactionInsId = $functionClass->transaction_insert($order_row['id'],'Debit','Refund Order','Transaction Refund',0,$other_params);
			}else{
				$transactionInsId = $functionClass->transaction_insert($order_row['id'],'Debit','Void Order','Transaction Void',0,$other_params);
			}
			//************************ insert transaction code end ***********************
			
			$update_ret_ord_data = array(
				'transaction_id' => $transactionInsId['id'],
				'updated_at' => 'msqlfunc_NOW()',
			);
			$update_ret_ord_where = array("clause" => 'id=:id', 'params' => array(':id' => $return_order_id));
			$pdo->update("return_orders", $update_ret_ord_data, $update_ret_ord_where);

			if ($is_partial_refund == false) {
				$update_ord_data = array(
					'status' => $reversal_type,
					'updated_at' => 'msqlfunc_NOW()',
				);
				$update_ord_where = array("clause" => 'id=:id', 'params' => array(':id' => $order_row['id']));
				$pdo->update("orders", $update_ord_data, $update_ord_where);
				
				
			}

			$is_any_product_terminated = false;
			$reversed_plan_ids = array();
			$order_detail_ids = array();
			foreach ($select_product as $plan_id => $prd_id) {
				$tmp_od_sql = "SELECT * FROM order_details WHERE order_id=:id AND plan_id=:plan_id AND is_deleted='N'";
				$tmp_od_where = array(':id' => $order_row['id'], ":plan_id" => $plan_id);
				$tmp_od_row = $pdo->selectOne($tmp_od_sql, $tmp_od_where);
				$reversed_plan_ids[] = $plan_id;
				$order_detail_ids[] = $tmp_od_row['id'];
				$return_od_data = array(
					'return_order_id' => $return_order_id,
					'product_id' => $tmp_od_row['product_id'],
					'product_type' => $tmp_od_row['product_type'],
					'product_name' => $tmp_od_row['product_name'],
					'unit_price' => $tmp_od_row['unit_price'],
					'product_code' => $tmp_od_row['product_code'],
					'qty' => $tmp_od_row['qty'],
					'refund_amount' => $tmp_od_row['unit_price'],

				);
				$pdo->insert('return_order_details', $return_od_data);

				$update_od_data = array(
					'is_refund' => "Y",
					'updated_at' => 'msqlfunc_NOW()',
				);
				$update_od_where = array("clause" => 'id=:id', 'params' => array(':id'=>$tmp_od_row['id']));
				$pdo->update("order_details", $update_od_data, $update_od_where);


				//********* Payable Insert Code Start ********************
					$payable_params=array(
						'payable_type'=>'Reverse_Vendor',
						'type'=>'Vendor',
						'transaction_tbl_id' => $transactionInsId['id'],
						'order_detail_id' => $tmp_od_row['id'],
					);
					$payable=$functionClass->payable_insert($order_row['id'],$order_row['customer_id'],$tmp_od_row['product_id'],$plan_id,$payable_params);
				//********* Payable Insert Code End   ********************  


				/*------ Terminate Subscription ----------*/

				$ws_sql = "SELECT * FROM website_subscriptions WHERE id=:id";
				$ws_where = array(":id"=>$tmp_od_row['website_id']);
				$ws_row = $pdo->selectOne($ws_sql,$ws_where);

				if(!empty($ws_row)) {
					// Terminate subscription
					if ($inactive_member == 'Yes') {
						if($term_product[$plan_id] != ''){
							$termination_date = date("Y-m-d",strtotime($term_product[$plan_id]));
							$extra_params = array();
			            	$extra_params['location'] = "return_order";
			            	$extra_params['activity_feed_flag'] = "return_order";
			            	$termination_reason = $refund_reason;
			            	$policySetting->setTerminationDate($ws_row['id'],$termination_date,$termination_reason,$extra_params);

							$is_any_product_terminated = true;
						}
					}
				}
			}

			if ($reverse_commission == 'Yes') {
				$extra_params = array();
				$extra_params['note'] = $refund_reason;
				$extra_params['date'] = date("Y-m-d");
				if ($is_partial_refund == true) {
					$extra_params['plan_ids'] = $reversed_plan_ids;
					$extra_params['order_detail_id'] = $order_detail_ids;
				}
				 $extra_params['transaction_tbl_id'] = $transactionInsId['id'];
				$commObj->reverseOrderCommissions($order_row['id'],$extra_params);
			}

			if($is_any_product_terminated == true) {
				// send_policy_cancellation_mail_to_sponsor($cust_row['id']);
			}

			// $af_data = array(
			// 	'refund_type' => $refund_type,
			// 	'refund_amount' => $refund_amount,
			// 	'future_billing' => $future_billing,
			// 	'inactive_member' => $inactive_member,
			// 	'reverse_commission' => $reverse_commission,
			// 	'note' => $refund_reason,
			// );

			if($reversal_type=='Refund'){
				$refundType = ($refund_type == 'Cheque' ? 'Check' : 'Original Payment');
				if($is_partial_refund){
					$description['ac_message'] = array(
					    'ac_red_1' => array(
					      'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
					      'title' => $_SESSION['admin']['display_id'],
					    ),
					    'ac_message_1' => ' partially refunded ',
					    'ac_red_2' => array(
					      'href' => $ADMIN_HOST . '/order_receipt.php?orderId=' . md5($order_row['id']),
					      'title' => $order_row['display_id'],
					    ),
					    'ac_message_2' => ' <br/>Reverse commissions: '.$reverse_commission,
					  );

					$description['descriptions'][] = 'Refund By : '.$refundType;
					$description['descriptions'][] = 'Transaction ID : '.$txn_id;
			        $desc = json_encode($description);
		  			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $order_row['customer_id'], 'Customer', 'Partial Refund', $_SESSION['admin']['name'], "", $desc);
				}else{
					$description['ac_message'] = array(
					    'ac_red_1' => array(
					      'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
					      'title' => $_SESSION['admin']['display_id'],
					    ),
					    'ac_message_1' => ' fully refunded ',
					    'ac_red_2' => array(
					      'href' => $ADMIN_HOST . '/order_receipt.php?orderId=' . md5($order_row['id']),
					      'title' => $order_row['display_id'],
					    ),
					    'ac_message_2' => ' <br/>Reverse commissions: '.$reverse_commission,
					  );

					$description['descriptions'][] = 'Refund By : '.$refundType;
					$description['descriptions'][] = 'Transaction ID : '.$txn_id;
			        $desc = json_encode($description);
		  			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $order_row['customer_id'], 'Customer', 'Full Refund', $_SESSION['admin']['name'], "", $desc);
				}

				// activity_feed($cust_row['company_id'], $cust_row['id'], $cust_row['type'], $order_row['id'], 'orders', 'Order Refund','', '', json_encode($af_data));
			}else{
				$description['ac_message'] = array(
				    'ac_red_1' => array(
				      'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
				      'title' => $_SESSION['admin']['display_id'],
				    ),
				    'ac_message_1' => ' voided ',
				    'ac_red_2' => array(
				      'href' => $ADMIN_HOST . '/order_receipt.php?orderId=' . md5($order_row['id']),
				      'title' => $order_row['display_id'],
				    )
				  );

		        $desc = json_encode($description);
	  			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $order_row['customer_id'], 'Customer', 'Void Order', $_SESSION['admin']['name'], "", $desc);
			}

			/*----------------- List Bill Related Update ------------------*/
			if ($order_row['type'] == 'List Bill') {
				$list_bill_sql = "SELECT lb.list_bill_no,lb.received_amount as lb_received_amount,lb.due_amount as lb_due_amount,lbp.received_amount,lbp.other_charges,lbp.reference,lb.customer_id,od.list_bill_id,od.list_bill_payment_id
										FROM list_bills lb
										JOIN order_details od ON (od.list_bill_id = lb.id AND od.is_deleted='N')
										JOIN list_bill_payments lbp ON (lbp.id = od.list_bill_payment_id)
										WHERE od.order_id = :order_id";
				$list_bill_where = array(":order_id" => $order_row['id']);
				$list_bill_row = $pdo->selectOne($list_bill_sql, $list_bill_where);

				if (!empty($list_bill_row)) {
					/*------------ Make Entry In Account Summary ---------- */
					$transaction_amount = $list_bill_row['received_amount'] > $refund_amount ? $refund_amount : $list_bill_row['received_amount'];
					// $account_summary_data = array(
					// 	'customer_id' => $list_bill_row['customer_id'],
					// 	'entity_id' => $list_bill_row['list_bill_payment_id'],
					// 	'entity_type' => 'list_bill_payment',
					// 	'transaction_date' => date('Y-m-d'),
					// 	'transaction_type' => 'debit',
					// 	'transaction_amount' => $transaction_amount,
					// 	'transaction_action' => 'payment return',
					// 	'transaction_name' => 'Payment Return',
					// 	'transaction_desc' => 'Ref #' . $list_bill_row['reference'] . ' <br/>' . displayAmount($transaction_amount, 2) . ' for payment return of ' . $list_bill_row['list_bill_no'],
					// 	'created_at' => date('Y-m-d H:i:s'),
					// 	'updated_at' => date('Y-m-d H:i:s'),
					// );
					// $pdo->insert('account_summary', $account_summary_data);
					/*------------/Make Entry In Account Summary ---------- */

					/*------------ Update List Bill Payment Status ---------------*/
					$update_list_bill_payment_data = array();
					$update_list_bill_payment_data['updated_at'] = "msqlfunc_NOW()";
					if ($refund_amount >= $list_bill_row['received_amount']) {
						$update_list_bill_payment_data['status'] = 'Payment Return';
					} else {
						$update_list_bill_payment_data['received_amount'] = $list_bill_row['received_amount'] - $refund_amount;
						$update_list_bill_payment_data['total_amount'] = $update_list_bill_payment_data['received_amount'] + $list_bill_row['other_charges'];
					}
					$update_list_bill_payment_where = array("clause" => 'id=:id', 'params' => array(':id' => $list_bill_row['list_bill_payment_id']));
					$pdo->update("list_bill_payments", $update_list_bill_payment_data, $update_list_bill_payment_where);
					/*------------/Update List Bill Payment Status ---------------*/

					/*------------ Update List Bill Data ---------------*/
					$update_list_bill_data = array(
						'received_amount' => $list_bill_row['lb_received_amount'] - $transaction_amount,
						'due_amount' => $list_bill_row['lb_due_amount'] + $transaction_amount,
						'updated_at' => 'msqlfunc_NOW()',
					);
					$update_list_bill_where = array("clause" => 'id=:id', 'params' => array(':id' => $list_bill_row['list_bill_id']));
					$pdo->update("list_bills", $update_list_bill_data, $update_list_bill_where);
					/*------------/Update List Bill Data ---------------*/
				}
			}
			/*-----------------/List Bill Related Update ------------------*/
			// Regenerate Orders changes end

			$response['status'] = 'success';
			if ($reversal_type == "Void") {
				setNotifySuccess("Order Voided Successfully");
			} else {
				setNotifySuccess("Order Refund Successfully");
			}
		} else {
			if ($reversal_type != "Void") {
	 		}

			$response['status'] = 'fail_attempt';
			if(!empty($refund_error_message)) {
				setNotifyError($refund_error_message);
				$response['failed_message'] = $refund_error_message;
			} else {
				if ($reversal_type == "Void") {
					$response['failed_message'] = "Order voiding Failed, try with refund";
					// setNotifyError("Order voiding Failed, try with refund");
				} else {
					// setNotifyError("Order Refund Failed");
					$response['failed_message'] = "Order Refund Failed";
				}
			}
			
		}
	} else {
		$response['errors'] = $validate->getErrors();	
	}
}
echo json_encode($response);
dbConnectionClose();
exit();