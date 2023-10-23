<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/includes/connect.php"; 

$sql = "SELECT c.id,cs.ip_address
		FROM customer_billing_profile cb
		JOIN customer_billing_profile cb1 ON (
		cb.customer_id = cb1.customer_id AND cb.payment_mode = cb1.payment_mode AND cb.last_cc_ach_no = cb1.last_cc_ach_no AND cb.id != cb1.id
		AND cb.cvv_no = cb1.cvv_no AND cb.card_type = cb1.card_type AND cb.expiry_month = cb1.expiry_month AND cb.expiry_year = cb1.expiry_year
		)
		JOIN customer c ON(c.id = cb.customer_id)
		JOIN customer_settings cs ON(c.id = cs.customer_id)
		WHERE cb.is_deleted='N' AND cb1.is_deleted='N' AND cb.payment_mode = 'CC' AND c.type = 'Customer'
		GROUP BY cb.customer_id";
$res = $pdo->select($sql);
$ccArr = $achArr = [];
if(!empty($res)){
	foreach ($res as $key => $value) {
		
		$order_billing_sql = "SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full FROM order_billing_info WHERE customer_id=:customer_id AND payment_mode=:payment_mode ORDER BY created_at DESC";
		$order_billing_res = $pdo->select($order_billing_sql,[':customer_id' => $value['id'],':payment_mode' => 'CC']);

		if(!empty($order_billing_res)){
			foreach ($order_billing_res as $k1 => $v1) {
				$c_billing_sql = "SELECT group_concat(id) as ids FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND card_type=:card_type AND expiry_month=:expiry_month AND expiry_year=:expiry_year AND is_deleted = 'N'";
				$c_billing_res = $pdo->selectOne($c_billing_sql,[':customer_id' => $value['id'],':payment_mode' => $v1['payment_mode'],':last_cc_ach_no' => $v1['last_cc_ach_no'],':card_type' => $v1['card_type'],':expiry_month' => $v1['expiry_month'],':expiry_year' => $v1['expiry_year']]);

				if(!empty($c_billing_res) && !empty($c_billing_res['ids'])){
					$upd_cust_params = ['is_deleted' => 'Y','updated_at' => 'msqlfunc_NOW()'];
					$upd_cust_where = ['clause' => 'customer_id=:customer_id AND id IN('.$c_billing_res["ids"].')','params' => [':customer_id' => $value['id']]];
					$pdo->update('customer_billing_profile',$upd_cust_params,$upd_cust_where);
				}
			}
		}

		if(!empty($order_billing_res)){
			foreach ($order_billing_res as $k1 => $v1) {
				$isDefaultCheck = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' AND is_default = 'Y'", array(':customer_id' => $value['id']));

				$billing_sql = "SELECT * FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND card_type=:card_type AND expiry_month=:expiry_month AND expiry_year=:expiry_year AND is_deleted = 'N'";
				$billing_res = $pdo->selectOne($billing_sql,[':customer_id' => $value['id'],':payment_mode' => $v1['payment_mode'],':last_cc_ach_no' => $v1['last_cc_ach_no'],':card_type' => $v1['card_type'],':expiry_month' => $v1['expiry_month'],':expiry_year' => $v1['expiry_year']]);

				if(empty($billing_res)){
					$billParams = array(
						'customer_id' => !empty($v1['customer_id']) ? $v1['customer_id'] : 0,
						'fname' => !empty($v1['fname']) ? makeSafe($v1['fname']) : '',
						'lname' => '',
						'email' => !empty($v1['email']) ? makeSafe($v1['email']) : '',
						'country_id' => 231,
						'country' => 'United States',
						'state' => !empty($v1['state']) ? makeSafe($v1['state']) : '',
						'city' => !empty($v1['city']) ? makeSafe($v1['city']) : '',
						'zip' => !empty($v1['zip']) ? makeSafe($v1['zip']) : '',
						'address' => !empty($v1['address']) ? makeSafe($v1['address']) : '',
						'address2' => !empty($v1['address2']) ? makeSafe($v1['address2']) : '',
						'cvv_no' => !empty($v1['cvv_no']) ? makeSafe($v1['cvv_no']) : '',
						'card_no' => !empty($v1['card_no']) ? makeSafe($v1['card_no']) : '',
						'last_cc_ach_no' => !empty($v1['last_cc_ach_no']) ? makeSafe($v1['last_cc_ach_no']) : '',
						'card_no_full' => !empty($v1['card_no_full']) ? "msqlfunc_AES_ENCRYPT('" . $v1['card_no_full'] . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
						'card_type' => !empty($v1['card_type']) ? makeSafe($v1['card_type']) : '',
						'expiry_month' => !empty($v1['expiry_month']) ? makeSafe($v1['expiry_month']) : '',
						'expiry_year' => !empty($v1['expiry_year']) ? makeSafe($v1['expiry_year']) : '',
						'created_at' => !empty($v1['created_at']) ? $v1['created_at'] : 'msqlfunc_NOW()',
						'updated_at' => 'msqlfunc_NOW()',
						'payment_mode' => !empty($v1['payment_mode']) ? $v1['payment_mode'] : '',
						'ip_address' => !empty($value['ip_address']) ? $value['ip_address'] : '',
					);
					if(!in_array($v1['customer_id'], $ccArr)){
						array_push($ccArr, $v1['customer_id']);
					}
					if(empty($isDefaultCheck)){
						$billParams['is_default'] = 'Y';
					}
					$customer_billing_id = $pdo->insert("customer_billing_profile", $billParams);
				} else {
					$customer_billing_id = $billing_res['id'];
				}

				$upd_ob_params = ['customer_billing_id' => $customer_billing_id,'updated_at' => 'msqlfunc_NOW()'];
				$upd_ob_where = ['clause' => 'id=:id','params' => [':id' => $v1['id']]];
				$pdo->update('order_billing_info',$upd_ob_params,$upd_ob_where);
			}
		}

		$order_billing_sql1 = "SELECT *,AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as account_number,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as routing_number FROM order_billing_info WHERE customer_id=:customer_id AND payment_mode=:payment_mode  ORDER BY created_at DESC";
		$order_billing_res1 = $pdo->select($order_billing_sql1,[':customer_id' => $value['id'],':payment_mode' => 'ACH']);

		if(!empty($order_billing_res1)){
			foreach ($order_billing_res1 as $k2 => $v2) {
				$c_billing_sql1 = "SELECT group_concat(id) as ids FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND bankname=:bankname AND ach_account_type=:ach_account_type AND is_deleted = 'N'";
				$c_billing_res1 = $pdo->selectOne($c_billing_sql1,[':customer_id' => $value['id'],':payment_mode' => $v2['payment_mode'],':last_cc_ach_no' => $v2['last_cc_ach_no'],':bankname' => $v2['bankname'],':ach_account_type' => $v2['ach_account_type']]);

				if(!empty($c_billing_res1) && !empty($c_billing_res1['ids'])){
					$upd_cust_params1 = ['is_deleted' => 'Y','updated_at' => 'msqlfunc_NOW()'];
					$upd_cust_where1 = ['clause' => 'customer_id=:customer_id AND id IN('.$c_billing_res1["ids"].')','params' => [':customer_id' => $value['id']]];
					$pdo->update('customer_billing_profile',$upd_cust_params1,$upd_cust_where1);
				}
			}
		}

		if(!empty($order_billing_res1)){
			foreach ($order_billing_res1 as $k2 => $v2) {
				$isDefaultCheck1 = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' AND is_default = 'Y'", array(':customer_id' => $value['id']));

				$billing_sql1 = "SELECT * FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND bankname=:bankname AND ach_account_type=:ach_account_type AND is_deleted = 'N'";
				$billing_res1 = $pdo->selectOne($billing_sql1,[':customer_id' => $value['id'],':payment_mode' => $v2['payment_mode'],':last_cc_ach_no' => $v2['last_cc_ach_no'],':bankname' => $v2['bankname'],':ach_account_type' => $v2['ach_account_type']]);

				if(empty($billing_res1)){
					$billParams1 = array(
						'customer_id' => !empty($v2['customer_id']) ? $v2['customer_id'] : 0,
						'fname' => !empty($v2['fname']) ? makeSafe($v2['fname']) : '',
						'lname' => '',
						'email' => !empty($v2['email']) ? makeSafe($v2['email']) : '',
						'country_id' => 231,
						'country' => 'United States',
						'state' => !empty($v2['state']) ? makeSafe($v2['state']) : '',
						'city' => !empty($v2['city']) ? makeSafe($v2['city']) : '',
						'zip' => !empty($v2['zip']) ? makeSafe($v2['zip']) : '',
						'address' => !empty($v2['address']) ? makeSafe($v2['address']) : '',
						'address2' => !empty($v2['address2']) ? makeSafe($v2['address2']) : '',
						'created_at' => !empty($v2['created_at']) ? $v2['created_at'] : 'msqlfunc_NOW()',
						'payment_mode' => !empty($v2['payment_mode']) ? $v2['payment_mode'] : '',
						'ach_account_type' => !empty($v2['ach_account_type']) ? $v2['ach_account_type'] : '',
						'bankname' => !empty($v2['bankname']) ? $v2['bankname'] : '',
						'last_cc_ach_no' => !empty($v2['last_cc_ach_no']) ? makeSafe($v2['last_cc_ach_no']) : '',
						'ach_account_number' => !empty($v2['account_number']) ? "msqlfunc_AES_ENCRYPT('" . $v2['account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
						'ach_routing_number' => !empty($v2['routing_number']) ? "msqlfunc_AES_ENCRYPT('" . $v2['routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
						'updated_at' => 'msqlfunc_NOW()',
						'ip_address' => !empty($value['ip_address']) ? $value['ip_address'] : '',
					);
					if(!in_array($v2['customer_id'], $achArr)){
						array_push($achArr, $v2['customer_id']);
					}
					if(empty($isDefaultCheck1)){
						$billParams1['is_default'] = 'Y';
					}
					$customer_billing_id1 = $pdo->insert("customer_billing_profile", $billParams1);
				} else {
					$customer_billing_id1 = $billing_res1['id'];
				}

				$upd_ob_params1 = ['customer_billing_id' => $customer_billing_id1,'updated_at' => 'msqlfunc_NOW()'];
				$upd_ob_where1 = ['clause' => 'id=:id','params' => [':id' => $v2['id']]];
				$pdo->update('order_billing_info',$upd_ob_params1,$upd_ob_where1);
			}
		}
	}
}

$sql1 = "SELECT c.id,cs.ip_address
		FROM customer_billing_profile cb
		JOIN customer_billing_profile cb1 ON (
		cb.customer_id = cb1.customer_id AND cb.payment_mode = cb1.payment_mode AND cb.last_cc_ach_no = cb1.last_cc_ach_no AND cb.id != cb1.id
		AND cb.bankname = cb1.bankname AND cb.ach_account_type = cb1.ach_account_type
		)
		JOIN customer c ON(c.id = cb.customer_id)
		JOIN customer_settings cs ON(c.id = cs.customer_id)
		WHERE cb.is_deleted='N' AND cb1.is_deleted='N' AND cb.payment_mode = 'ACH' AND c.type = 'Customer'
		GROUP BY cb.customer_id";
$res1 = $pdo->select($sql1);

if(!empty($res1)){
	foreach ($res1 as $key => $value) {
		
		$order_billing_sql = "SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full FROM order_billing_info WHERE customer_id=:customer_id AND payment_mode=:payment_mode ORDER BY created_at DESC";
		$order_billing_res = $pdo->select($order_billing_sql,[':customer_id' => $value['id'],':payment_mode' => 'CC']);

		if(!empty($order_billing_res)){
			foreach ($order_billing_res as $k1 => $v1) {
				$c_billing_sql = "SELECT group_concat(id) as ids FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND card_type=:card_type AND expiry_month=:expiry_month AND expiry_year=:expiry_year AND is_deleted = 'N'";
				$c_billing_res = $pdo->selectOne($c_billing_sql,[':customer_id' => $value['id'],':payment_mode' => $v1['payment_mode'],':last_cc_ach_no' => $v1['last_cc_ach_no'],':card_type' => $v1['card_type'],':expiry_month' => $v1['expiry_month'],':expiry_year' => $v1['expiry_year']]);

				if(!empty($c_billing_res) && !empty($c_billing_res['ids'])){
					$upd_cust_params = ['is_deleted' => 'Y','updated_at' => 'msqlfunc_NOW()'];
					$upd_cust_where = ['clause' => 'customer_id=:customer_id AND id IN('.$c_billing_res["ids"].')','params' => [':customer_id' => $value['id']]];
					$pdo->update('customer_billing_profile',$upd_cust_params,$upd_cust_where);
				}
			}
		}

		if(!empty($order_billing_res)){
			foreach ($order_billing_res as $k1 => $v1) {
				$isDefaultCheck = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' AND is_default = 'Y'", array(':customer_id' => $value['id']));

				$billing_sql = "SELECT * FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND card_type=:card_type AND expiry_month=:expiry_month AND expiry_year=:expiry_year AND is_deleted = 'N'";
				$billing_res = $pdo->selectOne($billing_sql,[':customer_id' => $value['id'],':payment_mode' => $v1['payment_mode'],':last_cc_ach_no' => $v1['last_cc_ach_no'],':card_type' => $v1['card_type'],':expiry_month' => $v1['expiry_month'],':expiry_year' => $v1['expiry_year']]);

				if(empty($billing_res)){
					$billParams = array(
						'customer_id' => !empty($v1['customer_id']) ? $v1['customer_id'] : 0,
						'fname' => !empty($v1['fname']) ? makeSafe($v1['fname']) : '',
						'lname' => '',
						'email' => !empty($v1['email']) ? makeSafe($v1['email']) : '',
						'country_id' => 231,
						'country' => 'United States',
						'state' => !empty($v1['state']) ? makeSafe($v1['state']) : '',
						'city' => !empty($v1['city']) ? makeSafe($v1['city']) : '',
						'zip' => !empty($v1['zip']) ? makeSafe($v1['zip']) : '',
						'address' => !empty($v1['address']) ? makeSafe($v1['address']) : '',
						'address2' => !empty($v1['address2']) ? makeSafe($v1['address2']) : '',
						'cvv_no' => !empty($v1['cvv_no']) ? makeSafe($v1['cvv_no']) : '',
						'card_no' => !empty($v1['card_no']) ? makeSafe($v1['card_no']) : '',
						'last_cc_ach_no' => !empty($v1['last_cc_ach_no']) ? makeSafe($v1['last_cc_ach_no']) : '',
						'card_no_full' => !empty($v1['card_no_full']) ? "msqlfunc_AES_ENCRYPT('" . $v1['card_no_full'] . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
						'card_type' => !empty($v1['card_type']) ? makeSafe($v1['card_type']) : '',
						'expiry_month' => !empty($v1['expiry_month']) ? makeSafe($v1['expiry_month']) : '',
						'expiry_year' => !empty($v1['expiry_year']) ? makeSafe($v1['expiry_year']) : '',
						'created_at' => !empty($v1['created_at']) ? $v1['created_at'] : 'msqlfunc_NOW()',
						'updated_at' => 'msqlfunc_NOW()',
						'payment_mode' => !empty($v1['payment_mode']) ? $v1['payment_mode'] : '',
						'ip_address' => !empty($value['ip_address']) ? $value['ip_address'] : '',
					);
					if(!in_array($v1['customer_id'], $ccArr)){
						array_push($ccArr, $v1['customer_id']);
					}
					if(empty($isDefaultCheck)){
						$billParams['is_default'] = 'Y';
					}
					$customer_billing_id = $pdo->insert("customer_billing_profile", $billParams);
				} else {
					$customer_billing_id = $billing_res['id'];
				}

				$upd_ob_params = ['customer_billing_id' => $customer_billing_id,'updated_at' => 'msqlfunc_NOW()'];
				$upd_ob_where = ['clause' => 'id=:id','params' => [':id' => $v1['id']]];
				$pdo->update('order_billing_info',$upd_ob_params,$upd_ob_where);
			}
		}

		$order_billing_sql1 = "SELECT *,AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as account_number,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as routing_number FROM order_billing_info WHERE customer_id=:customer_id AND payment_mode=:payment_mode  ORDER BY created_at DESC";
		$order_billing_res1 = $pdo->select($order_billing_sql1,[':customer_id' => $value['id'],':payment_mode' => 'ACH']);

		if(!empty($order_billing_res1)){
			foreach ($order_billing_res1 as $k2 => $v2) {
				$c_billing_sql1 = "SELECT group_concat(id) as ids FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND bankname=:bankname AND ach_account_type=:ach_account_type AND is_deleted = 'N'";
				$c_billing_res1 = $pdo->selectOne($c_billing_sql1,[':customer_id' => $value['id'],':payment_mode' => $v2['payment_mode'],':last_cc_ach_no' => $v2['last_cc_ach_no'],':bankname' => $v2['bankname'],':ach_account_type' => $v2['ach_account_type']]);

				if(!empty($c_billing_res1) && !empty($c_billing_res1['ids'])){
					$upd_cust_params1 = ['is_deleted' => 'Y','updated_at' => 'msqlfunc_NOW()'];
					$upd_cust_where1 = ['clause' => 'customer_id=:customer_id AND id IN('.$c_billing_res1["ids"].')','params' => [':customer_id' => $value['id']]];
					$pdo->update('customer_billing_profile',$upd_cust_params1,$upd_cust_where1);
				}
			}
		}

		if(!empty($order_billing_res1)){
			foreach ($order_billing_res1 as $k2 => $v2) {
				$isDefaultCheck1 = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' AND is_default = 'Y'", array(':customer_id' => $value['id']));

				$billing_sql1 = "SELECT * FROM customer_billing_profile WHERE customer_id=:customer_id AND payment_mode=:payment_mode AND last_cc_ach_no=:last_cc_ach_no AND bankname=:bankname AND ach_account_type=:ach_account_type AND is_deleted = 'N'";
				$billing_res1 = $pdo->selectOne($billing_sql1,[':customer_id' => $value['id'],':payment_mode' => $v2['payment_mode'],':last_cc_ach_no' => $v2['last_cc_ach_no'],':bankname' => $v2['bankname'],':ach_account_type' => $v2['ach_account_type']]);

				if(empty($billing_res1)){
					$billParams1 = array(
						'customer_id' => !empty($v2['customer_id']) ? $v2['customer_id'] : 0,
						'fname' => !empty($v2['fname']) ? makeSafe($v2['fname']) : '',
						'lname' => '',
						'email' => !empty($v2['email']) ? makeSafe($v2['email']) : '',
						'country_id' => 231,
						'country' => 'United States',
						'state' => !empty($v2['state']) ? makeSafe($v2['state']) : '',
						'city' => !empty($v2['city']) ? makeSafe($v2['city']) : '',
						'zip' => !empty($v2['zip']) ? makeSafe($v2['zip']) : '',
						'address' => !empty($v2['address']) ? makeSafe($v2['address']) : '',
						'address2' => !empty($v2['address2']) ? makeSafe($v2['address2']) : '',
						'created_at' => !empty($v2['created_at']) ? $v2['created_at'] : 'msqlfunc_NOW()',
						'payment_mode' => !empty($v2['payment_mode']) ? $v2['payment_mode'] : '',
						'ach_account_type' => !empty($v2['ach_account_type']) ? $v2['ach_account_type'] : '',
						'bankname' => !empty($v2['bankname']) ? $v2['bankname'] : '',
						'last_cc_ach_no' => !empty($v2['last_cc_ach_no']) ? makeSafe($v2['last_cc_ach_no']) : '',
						'ach_account_number' => !empty($v2['account_number']) ? "msqlfunc_AES_ENCRYPT('" . $v2['account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
						'ach_routing_number' => !empty($v2['routing_number']) ? "msqlfunc_AES_ENCRYPT('" . $v2['routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
						'updated_at' => 'msqlfunc_NOW()',
						'ip_address' => !empty($value['ip_address']) ? $value['ip_address'] : '',
					);
					if(!in_array($v2['customer_id'], $achArr)){
						array_push($achArr, $v2['customer_id']);
					}
					if(empty($isDefaultCheck1)){
						$billParams1['is_default'] = 'Y';
					}
					$customer_billing_id1 = $pdo->insert("customer_billing_profile", $billParams1);
				} else {
					$customer_billing_id1 = $billing_res1['id'];
				}

				$upd_ob_params1 = ['customer_billing_id' => $customer_billing_id1,'updated_at' => 'msqlfunc_NOW()'];
				$upd_ob_where1 = ['clause' => 'id=:id','params' => [':id' => $v2['id']]];
				$pdo->update('order_billing_info',$upd_ob_params1,$upd_ob_where1);
			}
		}
	}
}
echo "==========ccArr==========";
pre_print($ccArr,false);
echo "==========achArr==========";
pre_print($achArr,false);
echo "<br>Completed";
exit;
dbConnectionClose();
?>