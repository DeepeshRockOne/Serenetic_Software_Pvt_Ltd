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
$MemberEnrollment = new MemberEnrollment();
$function_list = new functionsList();
$enrollDate = new enrollmentDate();
$policySetting = new policySetting();
$memberSetting = new memberSetting();

$OS = "System";
$REQ_URL = "cron_scripts/post_date_reinstate_order.php";
$BROWSER = 'System';
$today = date('Y-m-d');
$requestOrderId = isset($_GET['order_id']) ? $_GET['order_id'] : '';

$incr = "";
$schParams=array(":today" => $today);
$decline_log_id = "";

if(!empty($requestOrderId)){
	$incr .=" AND o.id = :requestOrderId";
	$schParams[":requestOrderId"]=$requestOrderId;
}

$ord_sql = "SELECT o.*,obi.id as billing_id
			FROM orders o
			JOIN customer c ON(c.id = o.customer_id)
			JOIN order_billing_info obi ON(obi.order_id = o.id) 
			WHERE 
			c.status IN ('Active') AND 
			o.status IN ('Post Payment','Payment Declined') AND
			o.is_reinstate_order='Y' AND
			o.future_payment='Y' AND 
			((o.post_date=:today AND o.total_attempts=0) OR o.next_attempt_at=:today) $incr
			GROUP BY o.id ORDER BY o.id ASC";
$ord_res = $pdo->select($ord_sql,$schParams);
// pre_print($ord_res,false);

if(!empty($ord_res)){
	foreach ($ord_res as $order_row) {
		$allow_process = true;
		$decline_txt = $decline_type = "";
		$grandTotal = $subTotal = 0;
		$productWiseInformation = array();
		$terminated_products = array();

		$is_selected_service_fee_product = false;
		$healthy_step_fee_products = 0;
		$service_fee_products = 0;
	

		$order_id = $order_row['id'];
		$customer_id = $order_row['customer_id'];
		$ws_ids = $order_row['subscription_ids'];
		$ws_res = array();

		$prd_ids = array();
		if(!empty($ws_ids)) {
			$ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id IN ($ws_ids)";
			$ws_res = $pdo->select($ws_sql);
			foreach ($ws_res as $key => $ws_row) {

				// Check Termination Date set for subscription
				if (!empty($ws_row['termination_date']) && strtotime($ws_row['termination_date']) > 0) {

					$order_details = $pdo->selectOne("SELECT id,start_coverage_period,end_coverage_period FROM order_details WHERE order_id=:order_id AND product_id=:product_id AND is_deleted='N'", array(':order_id' => $order_id,":product_id" => $ws_row['product_id']));

					if(empty($order_details['start_coverage_period']) || strtotime($ws_row['termination_date']) < strtotime($order_details['start_coverage_period'])) {
						$terminated_products[] = $ws_row['product_id'];
						unset($ws_res[$key]);
                    	continue;	
					}
                    
                }

				array_push($prd_ids, $ws_row['plan_id']);

				if($ws_row['product_type'] == 'Healthy Step'){
                    $healthy_step_fee_products = $ws_row['price'];
                }

                if($ws_row['product_type'] == 'ServiceFee'){
                    $service_fee_products = $ws_row['price'];
                    $is_selected_service_fee_product = true;
                }

                $index = $ws_row["product_id"] . "-" . $ws_row["prd_matrix_id"];
				if (!isset($productWiseInformation[$index])) {
					$productWiseInformation[$index] = array();
				}

				$ws_row['qty'] = 1;
				// pre_print($ws_row['subs_price'],false);
				$prdPrice = $ws_row['price'] * $ws_row['qty'];
				$subsPrice = $ws_row['price'] * $ws_row['qty'];
				$subTotal += $subsPrice;
				$productWiseInformation[$index]["subTotal"] = $prdPrice;
				$productWiseInformation[$index]["grandTotal"] = $subsPrice;
				$site_load = 'USA';
				$price_tag = "$";
			}
		}

		// pre_print($productWiseInformation);
		if (count($productWiseInformation) == 0) {
            continue;
            // skipping customer record when subscriptions are not available to process
        }
		
		$cust_sql = "SELECT id,rep_id,sponsor_id,fname,lname,email,cell_phone,status FROM customer c WHERE c.id=:customer_id";
		$customer_rows = $pdo->selectOne($cust_sql, array(":customer_id" => $customer_id));
		if(empty($customer_rows["id"])){
			continue;
			exit;
		}

		//Sponsor Detail
		$sponsor_sql = "SELECT IF(type='Group',sponsor_id,id) as sponsorPaymentId 
				FROM customer WHERE  type!='Customer' AND id = :id ";
		$sponsor_row = $pdo->selectOne($sponsor_sql, array(':id' => $customer_rows['sponsor_id']));

		$sponsor_id = $customer_rows['sponsor_id'];

		//selecting billing profile
		$bill_sql = "SELECT cb.*,
				      AES_DECRYPT(cb.ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number,
				      AES_DECRYPT(cb.ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number,
				      AES_DECRYPT(cb.card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no
				      FROM customer_billing_profile cb
				      JOIN order_billing_info ob ON(cb.id=ob.customer_billing_id AND cb.customer_id=ob.customer_id)
				      WHERE ob.id=:id";
		$bill_where = array(":id" => $order_row['billing_id']);
		$bill_row = $pdo->selectOne($bill_sql, $bill_where);
		// pre_print($bill_row);

		if (empty($bill_row)) {
			$allow_process = false;
			$decline_type = 'System';
			$decline_txt = "Billing profile missing";
		}
		//allowed only CC,ACH payment here
		if (!in_array($bill_row["payment_mode"], array("CC", "ACH"))) {
			continue;
			exit;
		}
		/*----------*/
		$monthly_premium = $subTotal;
		if($service_fee_products != 0){
			$subTotal = $subTotal - $service_fee_products;
		}
		if($healthy_step_fee_products != 0){
			$subTotal = $subTotal - $healthy_step_fee_products;
		}
		$product_total = $subTotal;
		
		$grandTotal = number_format($subTotal + $service_fee_products + $healthy_step_fee_products, 2, ".", "");
		
		$sub_total = $subTotal;
		$grand_total = $grandTotal;

	    $payment_master_id = $function_list->get_agent_merchant_detail($prd_ids, $sponsor_row['sponsorPaymentId'], $bill_row["payment_mode"],array('is_renewal'=>$order_row['is_renewal'],'customer_id'=>$customer_id));
	    $payment_processor = getname('payment_master',$payment_master_id,'processor_id');

	    $paymentApproved = false;
	    $txn_id = 0;
	    $cc_params = array();
		$cc_params['order_id'] = $order_row['display_id'];
		$cc_params['amount'] = $grand_total;
		
		if ($bill_row["payment_mode"] == "ACH") {
			$cc_params['ach_account_type'] = $bill_row['ach_account_type'];
			$cc_params['ach_routing_number'] = $bill_row['ach_routing_number'];
			$cc_params['ach_account_number'] = $bill_row['ach_account_number'];
			$cc_params['name_on_account'] = $bill_row['fname']. ' ' . $bill_row['lname'];
			$cc_params['bankname'] = $bill_row['bankname'];
		} else {
			$cc_params['ccnumber'] = $bill_row['cc_no'];
			$cc_params['card_type'] = $bill_row['card_type'];
			$cc_params['ccexp'] = str_pad($bill_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($bill_row['expiry_year'], -2);
		}
		
		$cc_params['description'] = "Customer Post Payment Order";
		$cc_params['firstname'] = $bill_row['fname'];
		$cc_params['lastname'] = $bill_row['lname'];
		$cc_params['address1'] = $bill_row['address'];
		$cc_params['city'] = $bill_row["city"];
		$cc_params['state'] = $bill_row["state"];
		$cc_params['zip'] = $bill_row["zip"];
		$cc_params['country'] = 'USA';
		$cc_params['phone'] = $customer_rows['cell_phone'];
		$cc_params['email'] = $customer_rows['email'];
		$cc_params['processor'] = $payment_processor;

		if($grand_total == 0) {
			$payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
		} else {
			if ($bill_row["payment_mode"] == "ACH") {
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

		if ($payment_res['status'] == 'Success') {
			$paymentApproved = true;
			$txn_id = $payment_res['transaction_id'];
		} else {
			$txn_id = $payment_res['transaction_id'];
			$decline_txt = $payment_res['message'];
			$payment_error = $payment_res['message'];
			$decline_type = 'Payment Processor';
			$allow_process = false;
			$paymentApproved = false;
			$cc_params['order_type'] = 'Subscription';
			$cc_params['browser'] = $BROWSER;
			$cc_params['os'] = $OS;
			$cc_params['req_url'] = $REQ_URL;
			$cc_params['err_text'] = $decline_txt;
			$decline_log_id = $function_list->credit_card_decline_log($customer_id, $cc_params, $payment_res);
		}

		$bill_data = array(
			'customer_id' => $customer_id,
			'customer_billing_id' => $bill_row['id'],
			'fname' => makeSafe($bill_row['fname']),
			'lname' => makeSafe($bill_row['lname']),
			'email' => makeSafe($customer_rows['email']),
			'country_id' => '231',
			'country' => 'United States',
			'state' => makeSafe($bill_row['state']),
			'city' => makeSafe($bill_row['city']),
			'zip' => makeSafe($bill_row['zip']),
			'address' => makeSafe($bill_row['address']),
			'payment_mode' => $bill_row['payment_mode'],
		);

		if($bill_row['payment_mode'] == "ACH") {
			$bill_data = array_merge($bill_data,array(
				'ach_account_type' => $bill_row['ach_account_type'],
				'bankname' => $bill_row['bankname'],
				'last_cc_ach_no' => makeSafe(substr($bill_row['ach_account_number'], -4)),
				'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $bill_row['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
				'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $bill_row['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
			));
		} elseif($bill_row['payment_mode'] == "CC") {

			$bill_data = array_merge($bill_data,array(
				'card_type' => makeSafe($bill_row['card_type']),
				'expiry_month' => makeSafe($bill_row['expiry_month']),
				'expiry_year' => makeSafe($bill_row['expiry_year']),
				'card_no' => makeSafe(substr($bill_row['cc_no'], -4)),
				'last_cc_ach_no' => makeSafe(substr($bill_row['cc_no'], -4)),
				'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $bill_row['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
			));
		}
		$bill_where = array("clause" => "order_id=:id", "params" => array(":id" => $order_id));
		$pdo->update("order_billing_info",$bill_data,$bill_where);

		//if payment done
		$member_setting = $memberSetting->get_status_by_payment($paymentApproved);
		if ($allow_process) {
			// checking if payment processed
			if ($paymentApproved) {
				//payment received now update Database
				$order_params = array(
					'future_payment' => 'N',
					'original_order_date' => $order_row["originalOrderDate"],
					'created_at' => "msqlfunc_NOW()",
					'transaction_id' => $txn_id,
					'payment_processor_res' => json_encode($payment_res),
					'status' => ($bill_row['payment_mode'] == 'ACH') ? 'Pending Settlement' : 'Payment Approved',
					'payment_type' => ($bill_row['payment_mode'] == 'ACH' ? "ACH":"CC"),
					'payment_master_id' => $payment_master_id,
					'product_total' => $product_total,
					'sub_total' => $sub_total,
					'grand_total' => $grand_total,
				);

				if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
                    $order_params['review_require'] = 'Y';
                }
				$order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
				$pdo->update("orders", $order_params, $order_where);

				$other_params=array(
					"transaction_id"=> $txn_id,
					"req_url" => "cron_scripts/post_date_reinstate_order.php",
					'transaction_response'=> $payment_res,
				);

	        	$cust_where = array(
					"clause" => "id=:customer_id",
					"params" => array(
						":customer_id" => $customer_rows['id'],
					),
				);
				$pdo->update("customer", array('status' => $member_setting['member_status'], 'updated_at' => 'msqlfunc_NOW()'), $cust_where);	
			
				$history_id = 0;
				if(!empty($ws_res)){
					foreach ($ws_res as $ws_row) {

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

						if($order_row['is_renewal'] == 'Y') {
							$updateArr['renew_count'] = 'msqlfunc_renew_count + 1';
						}
						$updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $ws_row['id']));
						$pdo->update("website_subscriptions", $updateArr, $updateWhere);

						$updDepArr = array(
									"status" => $member_setting['member_status'],
									"updated_at" => "msqlfunc_NOW()",
								);
						$updDepWhere = array(
								"clause" => "customer_id=:customer_id AND product_plan_id=:product_plan_id",
								"params" => array(
									":customer_id" => $customer_id,
									":product_plan_id" => $ws_row['plan_id']
								),
							);

						$pdo->update("customer_dependent",$updDepArr,$updDepWhere);

						$ws_history_data = array(
							'customer_id' => $customer_id,
							'website_id' => $ws_row['id'],
							'product_id' => $ws_row['product_id'],
							'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
							'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
							'plan_id' => $ws_row['plan_id'],
							'order_id' => $order_id,
							'status' => ($paymentApproved == true?'Success':'Fail'),
							'message' => ($order_row['is_renewal'] == 'Y'?'Renewed Successfully':'Initial Setup Successful'),
							'authorize_id' => makeSafe($txn_id),
							'created_at' => 'msqlfunc_NOW()',
							'processed_at' => 'msqlfunc_NOW()',
						);
						$history_id = $pdo->insert("website_subscriptions_history", $ws_history_data);

					}

					if($order_row['is_renewal'] == 'Y') {
						$enrollDate->updateNextBillingDateByOrder($order_id);
					}
				}

				/*-- ACtivity Feed --*/
				$ac_descriptions = array();
				$ac_descriptions['ac_message'] =array(
		            'ac_red_1'=>array(
		              'href'=> 'members_details.php?id='.md5($customer_id),
		              'title'=> $customer_rows['rep_id'],
		            ),
		            'ac_message_1' =>'  Successful Post Payment Order ID ',
		            'ac_red_2'=>array(
						'href'=> 'all_orders.php?id='.md5($order_id),
						'title'=>$order_row['display_id'],
					),
		        );
		        activity_feed(3,$customer_id, 'customer',$customer_id, 'customer','Successful Post Payment',"","",json_encode($ac_descriptions));
		        /*--/ACtivity Feed --*/

		        //check any ticket for this customer is generated then resoved and create activity feed
				$checkTicketExists = $pdo->select("SELECT id,tracking_id FROM s_ticket WHERE user_id=:user_id AND subject=:subject AND status !='Resolved'",
					array(
						':user_id' => $customer_rows['id'],
						':subject' => "Failed Post Date Payment",
					)
				);
				if (!empty($checkTicketExists)) {
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

						$ac1_descriptions = array();
						$ac1_descriptions['ac_message'] =array(
		                    'ac_red_1'=>array(
		                      'title'=>$chkTicket['tracking_id'],
		                    ),
		                    'ac_message_1' =>'  E-Ticket Resolved',
		                );
		                activity_feed(3, $customer_id, 'Customer', $chkTicket["id"], 's_ticket', 'E-Ticket Resolved', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac1_descriptions));
					}
				}
				
			}
		}

		if (!$allow_process && $decline_type != "") {
			
			$order_params = array(
				'future_payment' => "Y",
				'updated_at' => "msqlfunc_NOW()",
				'transaction_id' => $txn_id,
				'payment_processor_res' => json_encode($payment_res),
				'status' => 'Payment Declined',
				'payment_type' => ($bill_row['payment_mode'] == 'ACH' ? "ACH":"CC"),
				'payment_master_id' => $payment_master_id,
				'total_attempts' => 'msqlfunc_total_attempts + 1',
				'updated_at' => 'msqlfunc_NOW()',
				'product_total' => $product_total,
				'sub_total' => $sub_total,
				'grand_total' => $grand_total,
			);
			
    		/*-- ACtivity Feed --*/
				$ac_descriptions = array();
				$ac_descriptions['ac_message'] =array(
		            'ac_red_1'=>array(
		              'href'=> 'members_details.php?id='.md5($customer_id),
		              'title'=> $customer_rows['rep_id'],
		            ),
		            'ac_message_1' =>'  Failed Post Payment OrderID ',
		            'ac_red_2'=>array(
						'href'=> 'all_orders.php?id='.md5($order_id),
						'title'=>$order_row['display_id'],
					),
		        );
		        activity_feed(3,$customer_id, 'Customer',$customer_id, 'customer','Failed Post Payment',"","",json_encode($ac_descriptions));
			/*--/ACtivity Feed --*/

			$attempt_sql = "SELECT * FROM prd_subscription_attempt WHERE attempt=:attempt AND is_deleted='N'";
        	$attempt_where = array(":attempt" =>($order_row['total_attempts'] + 1));
        	$attempt_row = $pdo->selectOne($attempt_sql, $attempt_where);

        	if(!empty($attempt_row)) {
        		$fail_trigger_id = $attempt_row['fail_trigger_id'];

	        	if($order_row['is_renewal'] == "Y") {
	        		$order_params['next_attempt_at'] = date('Y-m-d', strtotime("+" . $attempt_row['attempt_frequency'] . " " . $attempt_row['attempt_frequency_type']));
	        	}

				if (!empty($fail_trigger_id)) {
		        	$email_params = array();
		        	$email_params['fname'] = $customer_rows['fname'];
					$email_params['lname'] = $customer_rows['lname'];
					$email_params['subscription_type'] = "Post Date Payment";
					$email_params['USER_IDENTITY'] = array('rep_id' => $customer_rows['id'], 'cust_type' => $customer_rows['type'], 'location' => 'cron_scripts/post_date_reinstate_order.php');
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
		        	trigger_mail($fail_trigger_id, $email_params, $customer_rows['email'], "");
				}
	        } else {
	        	/*------- Stop All Future billing and terminate order's subscriptions ------*/
	        	if($order_row['is_renewal'] == "Y") {
	        		$order_params['future_payment'] = "N";

	        		foreach ($ws_res as $ws_row) {
						$termination_date=$enrollDate->getTerminationDate($ws_row['id']);  
						$extra_params = array();
						$extra_params['location'] = "post_date_reinstate_order";
						$termination_reason = "Failed Billing";
						$policySetting->setTerminationDate($ws_row['id'],$termination_date,$termination_reason,$extra_params);
					}
	        	}


	        	$message1 = "<h4>Failed Post Date Payment</h4><br>
		        			 <p>Name of Member : " . $customer_rows['fname'] . ' ' . $customer_rows['lname'] . "</p></br>
		        			 <p>Member ID: " . $customer_rows['rep_id'] . "</p></br>
							 <p>Email : " . $customer_rows['email'] . "</p></br>
							 <p>Phone : " . $customer_rows['cell_phone'] . "</p></br>
		        			 <p>Order ID: " . $order_row['display_id'] . "</p></br>
							 <p>Failed Billing Reason : " . $decline_txt . "</p></br>
							 ";
				$sessionArr = array('System'=>'System');
                $function_list->createNewTicket($sessionArr,17,"Failed Post Date Payment",0,$message1,$customer_id,'Customer','',array());

                $ac_descriptions_ti['ac_message'] =array(
		            'ac_red_1'=>array(
		              'href'=> 'members_details.php?id='.md5($customer_rows['id']),
		              'title'=>$customer_rows['rep_id'],
		            ),
		            'ac_message_1' =>' E-Ticket Opened',
	        	);
	        	activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'E-Ticket Opened', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac_descriptions_ti));
	        }

        	$order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
			$pdo->update("orders", $order_params, $order_where);


			if(!empty($ws_res)){
				foreach ($ws_res as $ws_row) {
					$insHistorySql = array(
						'customer_id' => $customer_id,
						'website_id' => $ws_row['id'],
						'product_id' => $ws_row['product_id'],
						'plan_id' => $ws_row['plan_id'],
						'order_id' => $order_id,
						'status' => 'Fail',
						'message' => $decline_txt,
						'attempt' => ($order_row['total_attempts'] + 1),
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

					$extra = array('attempt' => $order_row['total_attempts'] + 1);
					$member_setting = $memberSetting->get_status_by_payment($paymentApproved,"","","",$extra);
					
					if (!empty($attempt_row) && $order_row['is_renewal'] == 'Y') {
						$updateArr['status'] = $member_setting['policy_status'];
					} 
					$updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $ws_row['id']));
					$pdo->update("website_subscriptions", $updateArr, $updateWhere);
				}
			}
		}

		$extra = array('is_term_products' => true);
		$member_setting = $memberSetting->get_status_by_payment($paymentApproved,"","","",$extra);

		if(!empty($terminated_products)) {
			$terminated_products_res = $pdo->select("SELECT * FROM order_details WHERE order_id=:order_id AND is_deleted='N' AND product_id IN ('".implode("','",$terminated_products)."')",array(":order_id"=>$order_id));
			if(!empty($terminated_products_res)) {
				foreach ($terminated_products_res as $key => $terminated_products_row) {
					$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE customer_id=:customer_id AND plan_id=:plan_id", array(":customer_id" => $customer_id,":plan_id" => $terminated_products_row["plan_id"]));
					
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

		if ($allow_process && $paymentApproved) {
			$other_params=array(
					"transaction_id"=> $txn_id,
					"req_url" => "cron_scripts/post_date_reinstate_order.php",
					'transaction_response'=> $payment_res,
				);

			if($bill_row["payment_mode"] == "ACH"){
		        $transactionInsId = $function_list->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params);
	        } else {            
				if($order_row['is_renewal'] == 'N') {
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
				"transaction_id"=> $txn_id,
				"req_url" => "cron_scripts/post_date_reinstate_order.php",
				'transaction_response'=>$payment_res,
				"reason" => checkIsset($payment_error),
				"cc_decline_log_id" => checkIsset($decline_log_id)
			);
    		$transactionInsId = $function_list->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined','',$other_params);
		}

	}
}
dbConnectionClose();
// echo "Completed";