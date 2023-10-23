<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
require_once dirname(__DIR__) . '/includes/notification_function.php';
require_once dirname(__DIR__) . '/includes/upload_paths.php';
require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
require_once dirname(__DIR__) . '/includes/member_setting.class.php';
require_once dirname(__DIR__) . '/includes/policy_setting.class.php';
require_once dirname(__DIR__) . '/includes/trigger.class.php';

// error_reporting(E_ALL);
$MemberEnrollment = new MemberEnrollment();
$function_list = new functionsList();
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();
$policySetting = new policySetting();
$TriggerMailSms = new TriggerMailSms();
require_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$today = date('Y-m-d');
// $today = "2020-04-30";
$requestOrderId = isset($_GET['order_id']) ? $_GET['order_id'] : '';

$incr = "";
$schParams=array();
$decline_log_id = "";

if(!empty($requestOrderId)){
	$incr.=" AND o.id = :requestOrderId";
	$schParams[":requestOrderId"]=$requestOrderId;
}

if(empty($requestOrderId)){
	/*---------- System script status code start -----------*/
	    $cronSql = "SELECT is_running,next_processed,last_processed FROM system_scripts WHERE script_code=:script_code";
	    $cronWhere = array(":script_code" => "post_date_order");
	    $cronRow = $pdo->selectOne($cronSql,$cronWhere);

	    if(!empty($cronRow)){
		    $cronWhere = array(
		                      "clause" => "script_code=:script_code", 
		                      "params" => array(
		                          ":script_code" => 'post_date_order'
		                      )
		                  );
		    $pdo->update('system_scripts',array("is_running" => "Y","status"=>"Running","last_processed"=>"msqlfunc_NOW()"),$cronWhere);
	    }
  	/*---------- System script status code ends -----------*/
}
$selsql = "SELECT c.id,w.plan_id,w.id as wid,o.created_at as originalOrderDate,o.id as order_id,
			o.display_id,c.sponsor_id,o.is_renewal,o.subscription_ids,w.admin_id
			  FROM website_subscriptions w
			  JOIN customer c on (c.id=w.customer_id)
			  JOIN orders o on (o.id=w.last_order_id)
			  WHERE c.status IN ('Active','Pending','Post Payment') AND 
			  (DATE(o.post_date)=date('$today') AND o.future_payment='Y' AND w.total_attempts=0) AND 
			  o.is_reinstate_order='N' AND
			  w.status in('Pending','Post Payment','Inactive') AND o.status in('Pending Payment','Payment Declined','Post Payment') $incr
  			GROUP BY o.id";
$AutoRows = $pdo->select($selsql,$schParams);

// pre_print($AutoRows,false);
if (count($AutoRows) > 0) {
	$sendEmailSummary = array();
	foreach ($AutoRows as $autorow) {
		$ws_ids = $autorow['subscription_ids'];
		$allow_process = true;
		$decline_txt = $decline_type = "";

		$totalTax = $totalShipping = $grandTotal = $subTotal = 0;
		$free_qty = 0;
		$discount = 0;
		$service_fee = 0;
		$totalOrderDiscount = 0;
		$productWiseInformation = array();
		$order_billing_id = '';
		$terminated_products = array();
		$ship_product = array();

		$ws_res = array();
		if(!empty($ws_ids)) {
			$sql_ws = "SELECT w.*,p.price,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,o.display_id,p.plan_type as prd_matrix_type,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date, o.id as ord_id,ppt.title as plan_name,IF(pm.name = '' AND pm.product_type = 'ServiceFee','Service Fee',pm.name) AS product_name,pm.product_type,o.is_renewal
	            FROM website_subscriptions w
	            JOIN customer c on c.id=w.customer_id
	            JOIN prd_main pm ON pm.id=w.product_id
	            JOIN prd_matrix p ON p.product_id=w.product_id AND p.id=w.plan_id
	            JOIN customer_enrollment ce ON ce.website_id=w.id
	            JOIN orders o on (o.id=w.last_order_id)
	            LEFT JOIN prd_plan_type ppt ON ppt.id = p.plan_type
	            WHERE c.status IN('Active','Pending','Post Payment') AND
	            w.status in('Inactive','Pending','Post Payment') AND o.status in('Pending Payment','Payment Declined','Post Payment') AND 
	            c.id=:customer_id AND o.id=:order_id AND w.id IN ($ws_ids) GROUP BY w.id";
			$where_ws = array(":customer_id" => $autorow['id'],":order_id" => $autorow['order_id']);
			$ws_res = $pdo->select($sql_ws,$where_ws);
		}

		//create individuak products array for this customer, if multiple subscription for one user then
		$is_selected_service_fee_product = false;
		$prd_ids = array();
		$healthy_step_fee_products = 0;
		$service_fee_products = 0;
		$is_main_product = false;
		$send_email_productId = array();
        // pre_print($ws_res);

		if (count($ws_res) > 0) {
			$productArray = array();
			foreach ($ws_res as $key => $ws_row) {
				$productArray[] = $ws_res[$key]['product_id'];
				// Check Termination Date set for subscription
				if (strtotime($ws_row['termination_date']) > 0) {
					if(strtotime($ws_row['eligibility_date']) == strtotime($ws_row['termination_date'])) {
						$terminated_products[] = $ws_row['product_id'];
						unset($ws_res[$key]);
                    	continue;	
					}
                    
                }
                // Check Termination Date set for subscription
                array_push($prd_ids, $ws_row['prd_matrix_id']);

                if($ws_row['prd_type'] == 'Normal'){
                	$is_main_product = true;
                }

				if($ws_row['product_type'] == 'Healthy Step'){
                    $healthy_step_fee_products = $ws_row['subs_price'];
                }

                if($ws_row['product_type'] == 'ServiceFee'){
                    $service_fee_products = $ws_row['subs_price'];
                    $is_selected_service_fee_product = true;
                }

                if($ws_row['fail_order_id'] > 0){
                    $lastFailOrderId =  $ws_row['fail_order_id'];  
                }
				
				$index = $ws_row["product_id"] . "-" . $ws_row["prd_matrix_id"];
				if (!isset($productWiseInformation[$index])) {
					$productWiseInformation[$index] = array();
				}
				
				$ws_row['qty'] = 1;
				// pre_print($ws_row['subs_price'],false);
				$prdPrice = $ws_row['price'] * $ws_row['qty'];
				$subsPrice = $ws_row['subs_price'] * $ws_row['qty'];
				$subTotal += $subsPrice;
				$productWiseInformation[$index]["subTotal"] = $prdPrice;
				$productWiseInformation[$index]["grandTotal"] = $subsPrice;
				$site_load = 'USA';
				$price_tag = "$";
			}

			//********* Product send email code start ********************
			$mi_patria_product_res = $pdo->selectOne("SELECT setting_value FROM app_settings WHERE setting_key='mi_patria_products'");
			$mi_patria_product_id = !empty($mi_patria_product_res['setting_value']) ? explode(',',$mi_patria_product_res['setting_value']) : '';
			$send_email_productId = array_diff($productArray,$mi_patria_product_id);
			//********* Product send email code end ********************
		}

		// pre_print($productWiseInformation);
		if (count($productWiseInformation) == 0) {
            continue;
            // skipping customer record when subscriptions are not available to process
        }
        // skipping customer if only fees in order.
        if(!$is_main_product){
        	continue;
        }

		//selecting customer details
		$custSql = "SELECT c.*,sp.id as sponsor_id,sp.type as sponsor_type,sp.email as sponsor_email,l.id as lead_id,l.lead_id as lead_display_id
                  FROM customer c
                  JOIN leads l ON (l.customer_id=c.id)
                  LEFT JOIN customer sp ON sp.id=c.sponsor_id
                  where c.id=:id AND c.type='Customer' AND c.status IN('Active','Pending','Post Payment','Inactive')";
		$custParams = array(":id" => $autorow['id']);
		$customer_rows = $pdo->selectOne($custSql, $custParams);
		if (!$customer_rows) {
			continue;
			exit;
		}


		//Sponsor Detail
		$sponsor_sql = "SELECT id,fname,lname,type,upline_sponsors,level,payment_master_id,ach_master_id, user_name,email,sponsor_id 
				FROM customer WHERE  type!='Customer' AND id = :id ";
		$sponsor_row = $pdo->selectOne($sponsor_sql, array(':id' => $autorow['sponsor_id']));

		//selecting billing profile
		$billSql = "SELECT *,
				      AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number,
				      AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number,
				      AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no
				      FROM customer_billing_profile WHERE is_default = 'Y' AND customer_id=:cust_id";
		$params = array(":cust_id" => $autorow['id']);
		$billRow = $pdo->selectOne($billSql, $params);
		// pre_print($billRow);

		if (count($billRow) == 0) {
			$allow_process = false;
			$decline_type = 'System';
			$decline_txt = "Billing profile missing";
		}
		//allowed only CC,ACH payment here
		if (!in_array($billRow["payment_mode"], array("CC", "ACH"))) {
			continue;
			exit;
		}
		/*----------*/

		$monthly_premium = $subTotal;
		/*----------*/
		
		$order_id = $autorow["order_id"];

		if($service_fee_products != 0){
			$subTotal = $subTotal - $service_fee_products;
		}
		if($healthy_step_fee_products != 0){
			$subTotal = $subTotal - $healthy_step_fee_products;
		}
		
		// $subTotal += $healthy_step_fee_products;
		$product_total = $subTotal;
		// $subTotal += $service_fee;

		//calculating shipping and Tax
		$grandTotal = number_format($subTotal + $service_fee_products + $healthy_step_fee_products, 2, ".", "");
		// echo "<hr><br>sub:" . $subTotal;
		// echo "<br>Discount:" . $discount;
		// echo "<br>Tax:" . $totalTax;
		// echo "<br>Shipping:" . $totalShipping;
		// echo "<br>Grand:" . $grandTotal;

		//$product_total = $subTotal;
		$sub_total = $subTotal;
		$grand_total = $grandTotal;

		// pre_print($grandTotal);
		//create array to take charge from api
		$order_display_id = $autorow["display_id"];

		if ($sponsor_row['type'] == 'Group') {
			$payment_master_id = $function_list->get_agent_merchant_detail($prd_ids, $sponsor_row['sponsor_id'], $billRow["payment_mode"], array('is_renewal' => $autorow['is_renewal'], 'customer_id' => $autorow['id']));
		} else {
			$payment_master_id = $function_list->get_agent_merchant_detail($prd_ids, $sponsor_row['id'], $billRow["payment_mode"], array('is_renewal' => $autorow['is_renewal'], 'customer_id' => $autorow['id']));
		}
        $payment_processor= getname('payment_master',$payment_master_id,'processor_id');

		$payment_approved = false;
		$txn_id = 0;
		$cc_params = array();
		$cc_params['order_id'] = $order_display_id;
		$cc_params['amount'] = $grandTotal;

		if ($billRow["payment_mode"] == "ACH") {
			$cc_params['ach_account_type'] = $billRow['ach_account_type'];
			$cc_params['ach_routing_number'] = $billRow['ach_routing_number'];
			$cc_params['ach_account_number'] = $billRow['ach_account_number'];
			$cc_params['name_on_account'] = $billRow['fname'] . ' ' . $billRow['lname'];
			$cc_params['bankname'] = $billRow['bankname'];
		} else {
			$cc_params['ccnumber'] = $billRow['cc_no'];
			$cc_params['card_type'] = $billRow['card_type'];
			$cc_params['ccexp'] = str_pad($billRow['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($billRow['expiry_year'], -2);
		}

		$cc_params['description'] = "Customer Post Payment";
		$cc_params['firstname'] = $billRow['fname'];
		$cc_params['lastname'] = $billRow['lname'];
		$cc_params['address1'] = $billRow['address'];
		$cc_params['city'] = $billRow['city'];
		$cc_params['state'] = $billRow['state'];
		$cc_params['zip'] = $billRow['zip'];
		$cc_params['country'] = 'USA';
		$cc_params['phone'] = $billRow['phone'];
		$cc_params['email'] = $customer_rows['email'];
		$cc_params['processor'] = $payment_processor;

		//pre_print($ws_res,false);
		// pre_print($cc_params);
		if($grandTotal == 0) {
			$payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
		}else {
			if ($billRow["payment_mode"] == "ACH") {
				$api = new CyberxPaymentAPI();
				$payment_res = $api->processPaymentACH($cc_params,$payment_master_id);
			} else {
				if($cc_params['ccnumber'] == "4111111111111114") {
					$payment_res = array('status'=>'Success','transaction_id'=>0);					
				} else {					
					$api = new CyberxPaymentAPI();
					$payment_res = $api->processPayment($cc_params,$payment_master_id);
				}
				
			}
		}

		// pre_print($payment_res, false);
		// pre_print($cc_params, false);

		if ($payment_res['status'] == 'Success') {
			$payment_approved = true;
			$txn_id = $payment_res['transaction_id'];
		} else {
			$decline_txt = $payment_res['message'];
			$payment_error = $payment_res['message'];
			$decline_type = 'Payment Processor';
			$allow_process = false;
			$payment_approved = false;
			$cc_params['order_type'] = 'Subscription';
			$cc_params['browser'] = $BROWSER;
			$cc_params['os'] = $OS;
			$cc_params['req_url'] = $REQ_URL;
			$cc_params['err_text'] = $decline_txt;
			$decline_log_id = $function_list->credit_card_decline_log($autorow['id'], $cc_params, $payment_res);
		}

		$bill_data = array(
			'customer_id' => $autorow['id'],
			'customer_billing_id' => $billRow['id'],
			'fname' => makeSafe($billRow['fname']),
			'lname' => makeSafe($billRow['lname']),
			'email' => makeSafe($customer_rows['email']),
			'country_id' => '231',
			'country' => 'United States',
			'state' => makeSafe($billRow['state']),
			'city' => makeSafe($billRow['city']),
			'zip' => makeSafe($billRow['zip']),
			'address' => makeSafe($billRow['address']),
			'payment_mode' => $billRow['payment_mode'],
		);

		if($billRow['payment_mode'] == "ACH") {
			$bill_data = array_merge($bill_data,array(
				'ach_account_type' => $billRow['ach_account_type'],
				'bankname' => $billRow['bankname'],
				'last_cc_ach_no' => makeSafe(substr($billRow['ach_account_number'], -4)),
				'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $billRow['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
				'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $billRow['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
			));
		} elseif($billRow['payment_mode'] == "CC") {

			$bill_data = array_merge($bill_data,array(
				'card_type' => makeSafe($billRow['card_type']),
				'expiry_month' => makeSafe($billRow['expiry_month']),
				'expiry_year' => makeSafe($billRow['expiry_year']),
				'card_no' => makeSafe(substr($billRow['cc_no'], -4)),
				'last_cc_ach_no' => makeSafe(substr($billRow['cc_no'], -4)),
				'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $billRow['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
			));
		}
		$bill_where = array("clause" => "order_id=:id", "params" => array(":id" => $autorow['order_id']));
		$pdo->update("order_billing_info",$bill_data,$bill_where);

		//if payment done
		$member_setting = $memberSetting->get_status_by_payment($payment_approved);
		if ($allow_process) {
			// checking if payment processed
			if ($payment_approved) {
				//payment received now update Database
				$orderParams = array(
					'product_total' => $product_total,
					'sub_total' => $sub_total,
					'grand_total' => $grand_total,
				);
				$orderParams['future_payment'] = 'N';
				$orderParams['original_order_date'] = $autorow["originalOrderDate"];
				$orderParams['created_at'] = "msqlfunc_NOW()";
				$orderParams['transaction_id'] = $txn_id;
				$orderParams['payment_processor_res'] = json_encode($payment_res);
				$orderParams['status'] = ($billRow['payment_mode'] == 'ACH') ? 'Pending Settlement' : 'Payment Approved';
				$orderParams['payment_type'] = ($billRow['payment_mode'] == "ACH"?"ACH":"CC");
				$orderParams['payment_master_id'] = $payment_master_id;
				
				if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
                    $orderParams['review_require'] = 'Y';
                }
				$order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
				$pdo->update("orders", $orderParams, $order_where);
				$txn_id = $payment_res['transaction_id'];
				

				
				// if($billRow['payment_mode'] != "ACH"){
					
				// }else{
				// 	$transactionInsId =transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params);	
				// }
                          
				$cust_where = array(
					"clause" => "id=:customer_id",
					"params" => array(
						":customer_id" => $customer_rows['id'],
					),
				);
				$pdo->update("customer", array('status' => $member_setting['member_status'], 'updated_at' => 'msqlfunc_NOW()'), $cust_where);	
					
				/*---------- Convert Lead ---------*/
				$lead_where = array(
					"clause" => "customer_id=:customer_id",
					"params" => array(
						":customer_id" => $customer_rows['id'],
					),
				);
				$pdo->update("leads", array('status' => 'Converted', 'updated_at' => 'msqlfunc_NOW()'), $lead_where);

				$update_lead_param = array(
		            'customer_id' => $customer_rows['id'],
		            'email' => $customer_rows['email'],
		            'cell_phone' => $customer_rows['cell_phone']
		        );
        		$function_list->update_leads_and_details($update_lead_param);

        		if(!empty($ws_res) && $autorow['is_renewal'] == 'N') {
        			$coverage_dates = array();
					foreach ($ws_res as $ws_row) {
						$coverage_dates[$ws_row['product_id']] = $ws_row['eligibility_date'];
					}
					$customerPassword = getname("customer",$customer_rows['id'],"password",'id');
					if(empty($customerPassword)){
						$temporaryPassword = generate_chat_password(10);
						$updateCustomerPasswordParams = ['password' => "msqlfunc_AES_ENCRYPT('" . $temporaryPassword . "','" . $CREDIT_CARD_ENC_KEY . "')"];
						$updateCustomerPasswordWhere = ['clause' => 'id=:id','params' => [':id' => $customer_rows['id']]];
						$pdo->update('customer',$updateCustomerPasswordParams,$updateCustomerPasswordWhere);
					}
    				$TriggerMailSms->trigger_action_mail('member_enrollment',$customer_rows['id'],'member','addedEffectiveDate',$coverage_dates);
        		}

        		$MemberEnrollment->unqualified_leads_with_duplicate_email($customer_rows['email'],$customer_rows['id']);
				/*-----------/Convert Lead --------*/


				// generate dpg agreement on order is approved
				$function_list->checkOrderDpgAgreement($order_id);
				
				// generate joinder agreement when order is approved
                $function_list->checkJoinderAgreement($order_id);


				$triggerId = 39;
				$productDetail = "";
				$successProducts = array();
				$history_id = 0;
				if ($ws_res) {
					foreach ($ws_res as $key => $ws_row) {
						$prdIndex = $ws_row['product_id'] . "-" . $ws_row["prd_matrix_id"];
						$prdSql = "SELECT * FROM prd_main WHERE id=:id";
						$productRow = $pdo->selectOne($prdSql, array(":id" => $ws_row['product_id']));

						$productDetail .= $productRow['name'] . " ";
						$successProducts[] = $productRow['name'];
						
						$updWhere = array(
							"clause" => "website_id=:subscription_id",
							"params" => array(
								":subscription_id" => $ws_row['id'],
							),
						);
						$updateCE = array(
							'process_status' => "Active",
						);
						$pdo->update("customer_enrollment", $updateCE, $updWhere);

						//updating autoship product
						$updateArr = array(
							'last_order_id' => $order_id,
							'total_attempts' => 0,
							'next_attempt_at' => NULL,
							'last_purchase_date' => 'msqlfunc_NOW()',
							'status' => $member_setting['policy_status'],
							'updated_at' => 'msqlfunc_NOW()',
						);

						if($autorow['is_renewal'] == 'Y') {
							$updateArr['renew_count'] = 'msqlfunc_renew_count + 1';
						}
						$updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $ws_row['id']));

						$pdo->update(
							"customer_dependent",
							array(
								"status" => $member_setting['member_status'],
								"updated_at" => "msqlfunc_NOW()",
							),
							array(
								"clause" => "customer_id=:customer_id AND product_plan_id=:product_plan_id",
								"params" => array(
									":customer_id" => $autorow['id'],
									":product_plan_id" => $ws_row['plan_id']
								),
							)
						);

						// inserting in autoship history
						$insHistorySql = array(
							'customer_id' => $autorow['id'],
							'website_id' => $ws_row['id'],
							'product_id' => $ws_row['product_id'],
							'plan_id' => $ws_row['plan_id'],
							'order_id' => $order_id,
							'status' => 'Success',
							'message' => 'Initial Setup Successful',
							'authorize_id' => makeSafe($txn_id),
							'created_at' => 'msqlfunc_NOW()',
							'processed_at' => 'msqlfunc_NOW()',
						);
						$history_id = $pdo->insert("website_subscriptions_history", $insHistorySql);

						//sending notifications to chris and mike //For Special People
						$trigger_param = array(
							'order_status' => 'Payment Approved',
							'name' => ($customer_rows['fname'] . ' ' . $customer_rows['lname'] . ' (' . $customer_rows['rep_id'] . ')'),
							'order_id' => makeSafe("#" . $order_display_id),
							'Transaction_ID' => $txn_id,
							'amount_charged' => ($price_tag . number_format($productWiseInformation[$prdIndex]["grandTotal"], 2, ".", ",")),
							'payment_type' => (!empty($billRow["payment_mode"])?$billRow["payment_mode"]:"-"),
							'product_name' => $productRow['name'],
							'decline_type' => '-',
							'reason' => 'Success',
							'Attempt' => makeSafe($ws_row['total_attempts'] + 1),
						);
						$sendEmailSummary[] = $trigger_param;
						$pdo->update("website_subscriptions", $updateArr, $updateWhere);
					}

					if($autorow['is_renewal'] == 'Y') {
						$enrollDate->updateNextBillingDateByOrder($order_id);
					}
				}
				// echo "<br>Post Payment Successfully Processed";
				$email_params = array();
				$email_params['fname'] = $customer_rows['fname'];
				$email_params['lname'] = $customer_rows['lname'];
				$email_params['Email'] = $customer_rows['email'];
				$email_params['Phone'] = $customer_rows['cell_phone'];
				$email_params['link'] = $HOST . "/member";
				$email_params['Agent'] = $sponsor_row['fname'] . " " .$sponsor_row['lname'];
				$email_params['order_id'] = "#" . $order_display_id;
				$email_params['order_date'] = date("m/d/Y");
				$email_params['MemberID'] = $customer_rows['rep_id'];

				if(!empty($sponsor_row['sponsor_id'])){
					$parent_agent_detail = $function_list->get_sponsor_detail_for_mail($autorow['id'], $sponsor_row['sponsor_id']);
					$email_params['ParentAgent'] = $parent_agent_detail['agent_name'];
				}

				//********* Confirm summary code start ********************
				$summary = "";
				$summary .= '<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center" style="margin-bottom:15px; text-align:left; font-size:14px; margin-top:10px" >
	            <thead>
	                <tr style="background-color:#f1f1f1; text-align:left;">
	                    <th width="5%">No.</th>
	                    <th width="50%">Description</th>
	                    <th width="10%">Qty</th>
	                    <th width="">Unit Price</th>
	                    <th width="12%" style="text-align:right">Total</th>
	                </tr>
	            </thead>
	            <tbody>';
				$i = 1;

				$tmp_sub_total = 0;
				foreach ($ws_res as $key => $product) {
					if(in_array($product['product_type'], array('Healthy Step','ServiceFee'))){
						continue;
					}
					$summary_price = 0;
					$summary_price = $product['subs_price'];
					$tmp_sub_total += $product['subs_price'];

					$plan_name = isset($product['plan_name'])?$product['plan_name']:"";
					$product_name = $product['product_name'];
					if($product['prd_type']=='Fees'){
						$plan_name = $product['product_type'].' Fee';

					}
					$count = $i;

					$summary .= '<tr>
		                <td>' . $count . '</td>
		                <td>' . $product_name . ' (' . $plan_name . ')' . '</td>
		                <td>' . $product['qty'] . '</td>
		                <td>' . displayAmount($summary_price, 2, 'USA') . '</td>
		                <td style="text-align:right">' . displayAmount($summary_price * $product['qty'], 2, 'USA') .'</td>
	            	</tr>';

					$i++;

				}


				$summary .= '</tbody> </table>
	            <table cellspacing="0" cellpadding="5" border="0" style="float:right; width:290px; font-size:14px;">
	            <tr>
	                <td>Sub Total : </td>
	                <td style="text-align:right">' . displayAmount($tmp_sub_total, 2, "USA") . '</td>
	            </tr>';
				if ($service_fee_products > 0) {
					$summary .= '<tr>
	                    <td>Service Fee</td>
	                    <td align="right">' . displayAmount($service_fee_products, 2, 'USA') . ' </td>
	                </tr>';
				}
				if ($healthy_step_fee_products > 0) {
					$summary .= '<tr>
	                    <td>Healthy Step </td>
	                    <td align="right">' . displayAmount($healthy_step_fee_products, 2, 'USA') . ' </td>
	                </tr>';
				}
				$summary .= '<tr style="background-color:#f1f1f1; font-size: 16px;">
	                <td><strong>Grand Total</strong></td>
	                <td style="text-align:right"><strong>' . displayAmount($grand_total, 2, "USA") . '</strong></td>
	            </tr>
	            </table>
	            <div style="clear:both"></div>';
				//********* Confirm summary code end ********************

				$email_params['order_summary'] = $summary;

				if ($billRow['payment_mode'] == "CC") {
					$cd_number = $billRow['last_cc_ach_no'];
					$email_params['billing_detail'] = "Billed to: ".$billRow['card_type']." *" . substr($cd_number, -4);
				} else {
					$r_number = !empty($billRow['ach_routing_number']) ? $billRow['ach_routing_number'] : 0;
					$email_params['billing_detail'] = "Billed to: ACH *" . substr($r_number, -4);
				}

				//sending email to customer to notify monthly supply purchase
				$email_params['product_name'] = implode(",", $successProducts);
				$email_params['order_id'] = "#" . $order_display_id;

				$email_params['grand_total'] = ($price_tag . number_format($grandTotal, 2, ".", ",")) . ' (Including Tax)';

				$email_params['fname'] = $customer_rows['fname'];
				$email_params['lname'] = $customer_rows['lname'];
				$email_params['USER_IDENTITY'] = array('rep_id' => $customer_rows['id'], 'cust_type' => $customer_rows['type'], 'location' => 'cron_scripts/post_date_order.php');
				$email_params['subscription_type'] = "Post Date Payment";
				$email_params['reason'] = $decline_txt;
				$email_params['login_link'] = '<a href="' . $CUSTOMER_HOST . '">Your Account</a>';
				$email_params['link'] = '<a href="' . $CUSTOMER_HOST . '">Login</a>';
				$email_params['billing_short_url'] = get_short_url(array(
					'dest_url' => $HOST . '/order_billing/' . md5($order_id),
					'type' => 'Redirect',
					'customer_id' => $customer_rows['id'],
				));
				$agent_detail = $function_list->get_sponsor_detail_for_mail($customer_rows['id'], $customer_rows['sponsor_id']);
				if (!empty($agent_detail)) {
					$email_params['agent_name'] = $agent_detail['agent_name'];
					$email_params['agent_email'] = $agent_detail['agent_email'];
					$email_params['agent_phone'] = $agent_detail['agent_phone'];
					$email_params['agent_id'] = $agent_detail['agent_id'];
					$email_params['is_public_info'] = $agent_detail['is_public_info'];
				} else {
					$email_params['is_public_info'] = 'display:none';
				}

				$smart_tags = get_user_smart_tags($customer_rows['id'],'member');
                
                if($smart_tags){
                    $email_params = array_merge($email_params,$smart_tags);
                }
                if(!empty($send_email_productId)){
					trigger_mail($triggerId, $email_params, $customer_rows['email'], "");
				}
				$trigger_id = 38;

				$mail_data = array();
				$mail_data['fname'] = $customer_rows['fname'];
				$mail_data['lname'] = $customer_rows['lname'];
				$mail_data['Email'] = $customer_rows['email'];
				$mail_data['Phone'] = $customer_rows['cell_phone'];
				$mail_data['MemberID'] = $customer_rows['rep_id'];
				$mail_data['Agent'] = $agent_detail['agent_name'];
				if (!empty($agent_detail)) {
					$mail_data['agent_name'] = $agent_detail['agent_name'];
					$mail_data['agent_email'] = $agent_detail['agent_email'];
					$mail_data['agent_phone'] = $agent_detail['agent_phone'];
					$mail_data['agent_id'] = $agent_detail['agent_id'];
					$mail_data['is_public_info'] = $agent_detail['is_public_info'];
				} else {
					$email_params['is_public_info'] = 'display:none';
				}
        
		        if($smart_tags){
		            $mail_data = array_merge($mail_data,$smart_tags);
		        }
		        if(!empty($send_email_productId)){
					trigger_mail($trigger_id, $mail_data, $customer_rows['email']);

					$mail_content = $pdo->selectOne("SELECT id,email_content,display_id from triggers where id=:id",array(":id"=>$trigger_id));
					$email_activity = array();
					if(!empty($mail_data) && !empty($mail_content['id'])){
						$email_cn = $mail_content['email_content'];
						foreach ($mail_data as $placeholder => $value) {
							$email_cn = str_replace("[[" . $placeholder . "]]", $value, $email_cn);
						}

						$email_activity["ac_description_link"] = array(
							'Trigger :' => array(
								'href'=>'#javascript:void(0)',
								'class'=>'descriptionPopup',
								'title'=>$mail_content['display_id'],
								'data-desc'=>htmlspecialchars($email_cn),
								'data-encode'=>'no'
							),
							'<br>Email :' => array(
								'href' => '#javascript:void(0)',
								'title' => $customer_rows['email'],
							)
						);
					}
				
					activity_feed(3, $customer_rows['id'], "Customer", $trigger_id, 'triggers', 'Welcome email delivered', $customer_rows['fname'],$customer_rows['lname'],json_encode($email_activity));
					$MemberEnrollment->send_temporary_password_mail($customer_rows['id']);
				}
				
				if($history_id > 0) {

					$ac_descriptions['ac_message'] =array(
                        'ac_message_1' =>'  Successful Post Payment, on Order '.$order_display_id.' <br/>',
                    );
                    activity_feed(3, $customer_rows['sponsor_id'], $customer_rows['sponsor_type'], $customer_rows['id'], 'customer', 'Successful Post Payment', $productRow['name'], "", json_encode($ac_descriptions));

                    activity_feed(3,$customer_rows['lead_id'],'Lead',$customer_rows['lead_id'],'leads','Successful Post Payment',"","",json_encode($ac_descriptions));
				}

				//check any ticket for this customer is generated then resoved and create activity feed
				$checkTicketExists = $pdo->select("SELECT * FROM s_ticket WHERE user_id=:user_id AND subject =:subject",
					array(
						'user_id' => $customer_rows['id'],
						'subject' => "Failed Post Date Payment",
					)
				);
				if (count($checkTicketExists) > 0) {
					foreach ($checkTicketExists as $chkTicket) {
						$update_params = array(
							'status' => "Resolved",
						);
						$update_where = array(
							'clause' => 'id = :id',
							'params' => array(
								':id' => $chkTicket["id"],
							),
						);
						$pdo->update("s_ticket", $update_params, $update_where);

						$ac1_descriptions['ac_message'] =array(
                            'ac_red_1'=>array(
                            //   'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
                              'title'=>$chkTicket['tracking_id'],
                            ),
                            'ac_message_1' =>'  E-Ticket Resolved',
                        );

                        activity_feed(3, $customer_rows['id'], $customer_rows['type'], $chkTicket["id"], 's_ticket', 'E-Ticket Resolved', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac1_descriptions));

						// activity_feed(3, $customer_rows['id'], $customer_rows['type'], $chkTicket["id"], 's_ticket', 'E-Ticket Resolved', $customer_rows['fname'], $customer_rows['lname'], $triggerId);
					}

				}
			}
			// allow process over
		}

		//If payment failed then Inserting Fail Status to history
		$failedProducts = array();
		$attemptForTicketSend = $fail_trigger_id = 0;
		$payment_failed_triggers = array();
		if (!$allow_process && $decline_type != "") {

			if (count($ws_res)) {
				foreach ($ws_res as $key => $ws_row) {
					$prdIndex = $ws_row['product_id'] . "-" . $ws_row["prd_matrix_id"];
					$prdSql = "SELECT * FROM prd_main WHERE id=:id";
					$productRow = $pdo->selectOne($prdSql, array(":id" => $ws_row['product_id']));

					$insHistorySql = array(
						'customer_id' => $autorow['id'],
						'website_id' => $ws_row['id'],
						'product_id' => $ws_row['product_id'],
						'plan_id' => $ws_row['plan_id'],
						'order_id' => $order_id,
						'status' => 'Fail',
						'message' => $decline_txt,
						'attempt' => ($ws_row['total_attempts'] + 1),
						'created_at' => 'msqlfunc_NOW()',
						'processed_at' => 'msqlfunc_NOW()',
					);
					// echo "history fail<pre>";
					$history_id = $pdo->insert("website_subscriptions_history", $insHistorySql);

					//updating autoship product
					$updateArr = array(
						'fail_order_id' => $order_id,
						'updated_at' => 'msqlfunc_NOW()',
					);

					if($autorow['is_renewal'] == 'Y'){
						$updateArr['total_attempts'] = 'msqlfunc_total_attempts + 1';
					}

					$extra = array('attempt' => $ws_row['total_attempts'] + 1);
					$member_setting = $memberSetting->get_status_by_payment($payment_approved,"","","",$extra);

					$attemptSql = "SELECT * FROM prd_subscription_attempt
						                				   WHERE attempt=:attempt AND is_deleted='N'";
					$attemptParams = array(":attempt" => ($ws_row['total_attempts'] + 1));
					$attemptRow = $pdo->selectOne($attemptSql, $attemptParams);
					

					$attemptForTicketSend = $ws_row['total_attempts'] + 1;
					$failedProducts[] = $productRow["name"];
					
					if ($attemptRow) {
						$atmpt = $attemptRow['attempt'];
						$fail_trigger_id = $attemptRow['fail_trigger_id'];
					}
					if ($attemptRow && $autorow['is_renewal'] == 'Y') {
						$updateArr['next_attempt_at'] = date('Y-m-d', strtotime($today . " +" . $attemptRow['attempt_frequency'] . " " . $attemptRow['attempt_frequency_type']));
						$updateArr['status'] = $member_setting['policy_status'];
					} else {
						if($autorow['is_renewal'] == 'Y' || $autorow['admin_id'] > 0) {
							$termination_date=$enrollDate->getTerminationDate($ws_row['id']);
							$extra_params = array();
							$extra_params['location'] = "post_date_order";
							$termination_reason = "Failed Billing";
							$policySetting->setTerminationDate($ws_row['id'],$termination_date,$termination_reason,$extra_params);
						}
					}
					$updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $ws_row['id']));

					// echo "Update next billing<pre>";
					// print_r($updateArr);
					// print_r($updateWhere);
					// echo "</pre>";

					$pdo->update("website_subscriptions", $updateArr, $updateWhere);

					
					$productDetail = $productRow['name'] . " ";

					$trigger_param = array(
						'order_status' => 'Failed',
						'name' => ($customer_rows['fname'] . ' ' . $customer_rows['lname'] . ' (' . $customer_rows['rep_id'] . ')'),
						'order_id' => makeSafe("#" . $order_display_id),
						'Transaction_ID' => (isset($txn_id) && $txn_id != "" && $txn_id > 0) ? $txn_id : "-",
						'amount_charged' => ($price_tag . number_format($productWiseInformation[$prdIndex]["grandTotal"], 2, ".", ",")),
						'payment_type' =>  (!empty($billRow["payment_mode"])?$billRow["payment_mode"]:"-"),
						'product_name' => makeSafe($productDetail),
						'decline_type' => makeSafe($decline_type),
						'reason' => makeSafe($decline_txt),
						'Attempt' => makeSafe($ws_row['total_attempts'] + 1),
					);
					$sendEmailSummary[] = $trigger_param;
				}

				$updateOrderArr = array(
					'transaction_id' => $payment_res['transaction_id'],
					'product_total' => $product_total,
					'sub_total' => $sub_total,
					'grand_total' => $grand_total,
					'payment_processor_res' => !empty($payment_res)?json_encode($payment_res):'',
					'status' => 'Payment Declined',
					'updated_at' => 'msqlfunc_NOW()',
				);
				$updateOrderArr['payment_type'] = ($billRow['payment_mode'] == "ACH"?"ACH":"CC");
				$updateOrderArr['payment_master_id'] = $payment_master_id;
				
				$orderUpdateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $order_id));

				$pdo->update("orders", $updateOrderArr, $orderUpdateWhere);
				$txn_id = $payment_res['transaction_id'];
				
				// pre_print("Updated order id => ".$ws_row['ord_id'], false);

				//for sending mail and generate actity feed
				// sending email to notify that monthly supply was failed to process
				$email_params['fname'] = $customer_rows['fname'];
				$email_params['lname'] = $customer_rows['lname'];
				$email_params['subscription_type'] = "Post Date Payment";
				$email_params['USER_IDENTITY'] = array('rep_id' => $customer_rows['id'], 'cust_type' => $customer_rows['type'], 'location' => 'cron_scripts/post_date_order.php');
				$email_params['reason'] = $decline_txt;
				$email_params['login_link'] = '<a href="' . $CUSTOMER_HOST . '">Your Account</a>';
				$email_params['link'] = '<a href="' . $CUSTOMER_HOST . '">Login</a>';
				$email_params['billing_short_url'] = get_short_url(array(
					'dest_url' => $HOST . '/order_billing/' . md5($order_id),
					'type' => 'Redirect',
					'customer_id' => $customer_rows['id'],
				));
				$agent_detail = get_sponsor_detail_for_mail($customer_rows['id'], $customer_rows['sponsor_id']);
				if (!empty($agent_detail)) {
					$email_params['agent_name'] = $agent_detail['agent_name'];
					$email_params['agent_email'] = $agent_detail['agent_email'];
					$email_params['agent_phone'] = $agent_detail['agent_phone'];
					$email_params['agent_id'] = $agent_detail['agent_id'];
					$email_params['is_public_info'] = $agent_detail['is_public_info'];
				} else {
					$email_params['is_public_info'] = 'display:none';
				}

				if ($fail_trigger_id) {
					if(empty($payment_failed_triggers) || !in_array($fail_trigger_id,$payment_failed_triggers)){
						trigger_mail($fail_trigger_id, $email_params, $customer_rows['email'], "");

						$phone = $customer_rows['cell_phone'];
						if ($phone != "") {
							$calling_code = "+1";
							$tophone = $calling_code . $phone;
							trigger_sms($fail_trigger_id, $tophone, $email_params, "");
							//activity feed for sms
							// activity_feed(3, $ws_row['cust_id'], $ws_row['cust_type'], $history_id, 'website_subscriptions_history', 'Failed Post Date Payment(SMS sent)', implode(",", $failedProducts), "", $fail_trigger_id);

							$ac_descriptions['ac_message'] =array(
	                            'ac_red_1'=>array(
	                              'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($ws_row['cust_id']),
	                              'title'=>$customer_rows['rep_id'],
	                            ),
	                            'ac_message_1' =>'  Failed Post Date Payment(SMS sent) ',
	                        );


	                        activity_feed(3, $ws_row['cust_id'], $ws_row['cust_type'], $history_id, 'website_subscriptions_history', 'Failed Post Date Payment(SMS sent)', implode(",", $failedProducts), "",json_encode($ac_descriptions));
						}

						array_push($payment_failed_triggers, $fail_trigger_id);
					}
					

                    $ac_descriptions['ac_message'] =array(
                        'ac_message_1' =>'  Failed Payment on Order '.$order_display_id.' <br/>',
                        'ac_message_2' =>' due to '. checkIsset($payment_error),
                    );

                    activity_feed(3, $ws_row['cust_id'], $ws_row['cust_type'], $history_id, 'website_subscriptions_history', 'Failed Payment', implode(",", $failedProducts), "",json_encode($ac_descriptions));

                    activity_feed(3,$customer_rows['lead_id'],'Lead',$customer_rows['lead_id'],'leads','Failed Payment', implode(",", $failedProducts), "",json_encode($ac_descriptions));

					

				} else {
					// activity_feed(3, $ws_row['cust_id'], $ws_row['cust_type'], $history_id, 'website_subscriptions_history', 'Inactive Failed Billing', $productRow['name'], "", 120);

					/*$ac_descriptions['ac_message'] =array(
                        'ac_red_1'=>array(
                          'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($ws_row['cust_id']),
                          'title'=>$customer_rows['rep_id'],
                        ),
                        'ac_message_1' =>' Inactive Failed Billing ',
                    );


                    activity_feed(3, $ws_row['cust_id'], $ws_row['cust_type'], $history_id, 'website_subscriptions_history', 'Inactive Failed Billing', $productRow['name'], "",json_encode($ac_descriptions));

					$email_params = array();
					$email_params['fname'] = $customer_rows['fname'];
					$email_params['Date'] = "";
					$email_params['USER_IDENTITY'] = array('rep_id' => $customer_rows['id'], 'cust_type' => $customer_rows['type'], 'location' => 'cron_scripts/post_date_order.php');*/
				}

				//for eticket entry
				if ($attemptForTicketSend > 1) {
					//Generating e-tickets
					$tkt_customer_id = $customer_rows['id'];
					$customer_email = $customer_rows['email'];
					$tkt_user_type = $customer_rows['type'];
					$tracking_id = $function_list->generateEticketTrackingId();

					$message1 = "<h4>Failed Post Date Payment</h4><br>
				        			 <p>Name of Member : " . $customer_rows['fname'] . ' ' . $customer_rows['lname'] . "</p></br>
				        			 <p>Member ID: " . $customer_rows['rep_id'] . "</p></br>
				        			 <p>Product Type : " . implode(",", $failedProducts) . "</p></br>
									 <p>Email : " . $customer_rows['email'] . "</p></br>
									 <p>Phone : " . $customer_rows['cell_phone'] . "</p></br>
									 <p>Failed Billing Reason : " . $decline_txt . "</p></br>
									 ";

					$tkt_insert_params = array(
						'user_id' => $tkt_customer_id,
						'tracking_id' => $tracking_id,
						'user_type' => $tkt_user_type,
						'subject' => "Failed Post Date Payment",
						'created_at' => 'msqlfunc_NOW()',
						'last_replied' => 'msqlfunc_NOW()',
					);
					$ticket_id = $pdo->insert("s_ticket", $tkt_insert_params);

					$insert_params_qry4 = array(
						'ticket_id' => $ticket_id,
						'user_id' => $tkt_customer_id,
						'user_type' => $tkt_user_type,
						'message' => $message1,
						'is_read' => 'N',
						'created_at' => 'msqlfunc_NOW()',
					);
					$tkt_ins_id = $pdo->insert("s_ticket_message", $insert_params_qry4);
					// activity_feed(3, $tkt_customer_id, $tkt_user_type, $ticket_id, 's_ticket', 'E-Ticket Opened', $customer_rows['fname'], $customer_rows['lname'], $fail_trigger_id);

					$ac_descriptions_ti['ac_message'] =array(
	                    'ac_red_1'=>array(
	                      'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
	                      'title'=>$customer_rows['rep_id'],
	                    ),
	                    'ac_message_1' =>' E-Ticket Opened',
	                );

	                activity_feed(3, $tkt_customer_id, $tkt_user_type, $tkt_customer_id, $tkt_user_type, 'E-Ticket Opened', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac_descriptions_ti));
				}
			}
		}

		$extra = array('is_term_products' => true);
		$member_setting = $memberSetting->get_status_by_payment($payment_approved,"","","",$extra);

		if(!empty($terminated_products)) {
			$terminated_products_res = $pdo->select("SELECT * FROM order_details WHERE order_id=:order_id AND is_deleted='N' AND product_id IN ('".implode("','",$terminated_products)."')",array(":order_id"=>$order_id));
			if(!empty($terminated_products_res)) {
				foreach ($terminated_products_res as $key => $terminated_products_row) {
					$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE customer_id=:customer_id AND plan_id=:plan_id", array(":customer_id" => $autorow['id'],":plan_id" => $terminated_products_row["plan_id"]));
					
					$updateArr = array(
						"status" => $member_setting['policy_status'],
						"updated_at" => 'msqlfunc_NOW()',
					);
					$updWhere = array(
						"clause" => "id=:id",
						"params" =>  array(
							":id" => $ws_row['id'],
						),
					);
					$pdo->update("website_subscriptions", $updateArr, $updWhere);

					$updWhere = array(
						"clause" => "website_id=:subscription_id",
						"params" =>  array(
							":subscription_id" => $ws_row['id'],
						),
					);
					$updateCE = array(
						'process_status' => "Active",
					);
					$pdo->update("customer_enrollment", $updateCE, $updWhere);

					$pdo->update(
						"customer_dependent",
						array(
							"status" => $member_setting['policy_status'],
							"updated_at" => "msqlfunc_NOW()",
						),
						array(
							"clause" => "website_id=:website_id",
							"params" =>  array(
								":website_id" => $ws_row['id'],
							),
						)
					);
					$od_where = array(
						"clause" => "id=:id",
						"params" => array(
							":id" => $terminated_products_row['id'],
						),
					);
					$pdo->update("order_details", array('is_deleted' => 'Y'), $od_where);
				}
			}
		}

		if ($allow_process && $payment_approved) {
			$other_params=array(
					"transaction_id"=> $payment_res['transaction_id'],
					"req_url" => "cron_scripts/post_date_order.php",
					'transaction_response'=> $payment_res,
				);

			if($billRow['payment_mode'] == "ACH"){
		        $transactionInsId = $function_list->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params);
	        } else {            
				if($autorow['is_renewal'] == 'N') {
					$transactionInsId = $function_list->transaction_insert($order_id,'Credit','New Order','Transaction Approved','',$other_params);
				} else {
					$transactionInsId = $function_list->transaction_insert($order_id,'Credit','Renewal Order','Renewal Transaction','',$other_params);	
				}

				$payable_params=array(
	                'payable_type'=>'Vendor',
	                'type'=>'Vendor',
	                'transaction_tbl_id' => $transactionInsId['id'],
	            );
	            $function_list->payable_insert($order_id,0,0,0,$payable_params);
        	}
		}else if(!$allow_process && $decline_type != ""){
			$other_params = array(
				"transaction_id"=> $payment_res['transaction_id'],
				"req_url" => "cron_scripts/post_date_order.php",
				'transaction_response'=>$payment_res,
				"reason" => checkIsset($payment_error),
				"cc_decline_log_id" => checkIsset($decline_log_id)
			);
    		$transactionInsId = $function_list->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined','',$other_params);
		}
	}

	if (count($sendEmailSummary) && empty($requestOrderId) && $SITE_ENV=='Live') {
		$DEFAULT_ORDER_EMAIL = array("dharmesh@cyberxllc.com","karan.shukla@serenetic.in");
		trigger_mail_to_email($sendEmailSummary, $DEFAULT_ORDER_EMAIL, $SITE_NAME . " : Post Date Payment Order", array(), 2);
	}
}

/*--------- System script status code start ----------*/
if(!empty($cronRow) && empty($requestOrderId)){

	$cronSql = "SELECT last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "post_date_order");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);
    if(date("H",strtotime($cronRow['last_processed'])) > 16) {
    	$next_processed = date("Y-m-d 01:15:00",strtotime("+1 day", strtotime($cronRow['last_processed'])));
    } else {
    	$next_processed = date("Y-m-d 16:15:00",strtotime($cronRow['last_processed']));
    }
    $cronUpdParams = array("is_running" => "N","status"=>"Active","next_processed" => $next_processed);
    $cronWhere = array(
                "clause" => "script_code=:script_code", 
                "params" => array(
                    ":script_code" => 'post_date_order'
                )
            );
    $pdo->update('system_scripts',$cronUpdParams,$cronWhere);
}
/*---------- System script status code ends -----------*/

// echo "<br>Process Complete";
dbConnectionClose();
?>