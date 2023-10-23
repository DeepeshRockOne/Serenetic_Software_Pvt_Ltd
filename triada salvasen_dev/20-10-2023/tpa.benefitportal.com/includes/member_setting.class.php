<?php
include_once dirname(__DIR__) . "/includes/functions.php";

class memberSetting {
	
	public function get_status_by_payment($payment_approved="",$coveragePeriod="",$is_post_date=false,$current_member_status="",$extra=array()){
		global $pdo;
		$member_status = 'Pending';
		$policy_status = 'Pending';
		$dependent_status = 'Pending';
		$today = date('Y-m-d');

		if($payment_approved){
			$member_status = 'Active';
			$policy_status = 'Active';
			$dependent_status = 'Active';
		}

		if(!$payment_approved){
			$member_status = 'Inactive';
			$policy_status = 'Inactive';
			$dependent_status = 'Inactive';

			if(isset($extra['is_from_enrollment'])) {
				if(!empty($current_member_status)){
					if(in_array($current_member_status,array('Pending Validation'))){
						$member_status = 'Pending Validation';
					}
				}				
				$policy_status = 'Pending';
				$dependent_status = 'Pending';				
			}
		}

		if(!$payment_approved && !empty($coveragePeriod)){
			if(strtotime($coveragePeriod) <= strtotime($today)) {
				$member_status = 'Inactive';
				$policy_status = 'Inactive';
				$dependent_status = 'Inactive';
			}else{
				$policy_status = 'Active'; // On Hold Billing
				$member_status = 'Active';
				$dependent_status = 'Active';
			}
		}
		if($is_post_date){
			$member_status = 'Post Payment';
			$policy_status = 'Post Payment';
			$dependent_status = 'Post Payment';

			if(!empty($current_member_status)){
				if(in_array($current_member_status, array('Active','Inactive'))){
					$member_status = 'Active';
				}else{
					$member_status = "Post Payment";
				}
			}
		}
		if(isset($extra['attempt'])){
			$attemptSql = "SELECT * FROM prd_subscription_attempt
						   WHERE attempt=:attempt AND is_deleted='N'";
			$attemptParams = array(":attempt" => ($extra['attempt']));
			$attemptRow = $pdo->selectOne($attemptSql, $attemptParams);

			if($attemptRow){
				$member_status = 'Active';
				$policy_status = 'Active';
				$dependent_status = 'Active';
			}else{
				$member_status = 'Inactive';
				$policy_status = 'Inactive';
				$dependent_status = 'Inactive';
			}

			if(isset($extra['is_renewal'])){
				if($extra['is_renewal'] == 'Y'){
					$policy_status = 'Inactive';
					$dependent_status = 'Inactive';
				}else{
					$policy_status = 'Post Payment';
					$dependent_status = 'Post Payment';
				}
			}
		}
		if(isset($extra['is_term_products'])){
			$policy_status = 'Inactive';
			$dependent_status = 'Inactive';
		}
		if(isset($extra['is_cancel_post_date'])){
			$policy_status = 'Inactive';
			$dependent_status = 'Inactive';
		}

		return array('member_status' => $member_status,'policy_status' => $policy_status,'dependent_status' => $dependent_status);
	}
	public function get_status_by_order_status($order_status="",$term_date="",$effective_date="",$extra=array()){
		global $pdo;
		$member_status = 'Active';
		$policy_status = 'Active';
		$today = date('Y-m-d');

		if(in_array($order_status,array('Chargeback','Payment Return'))){
			$member_status = 'Inactive';
			$policy_status = 'Inactive';

			if($extra){
				if ($extra['is_renewal'] == 'N') {
                    $odrSel = "SELECT o.id FROM orders o 
                                JOIN order_details od ON(o.id=od.order_id AND od.is_deleted='N') 
                                WHERE o.status='Payment Approved' AND o.is_renewal='Y' AND 
                                od.product_id=:prdId AND od.plan_id=:planId AND o.customer_id=:custId";
                    $odrParams = array(":custId" => $extra['customer_id'], ":prdId" => $extra['product_id'], ":planId" => $extra['plan_id']);
                    $odrRes = $pdo->select($odrSel, $odrParams);
                    if (empty($odrRes)) {
                        $policy_status = 'Inactive';
                    }else{
                    	$policy_status = 'Active';
                    }
                }
                if(isset($extra['customer_id'])){
                	$odrSel = "SELECT o.id FROM orders o 
                                JOIN order_details od ON(o.id=od.order_id AND od.is_deleted='N') 
                                WHERE o.status='Payment Approved' AND o.customer_id=:custId";
                    $odrParams = array(":custId" => $extra['customer_id']);
                    $odrRes = $pdo->select($odrSel, $odrParams);

                    if (empty($odrRes)) {
                        $member_status = 'Inactive';
                    }else{
                    	$member_status = 'Active';
                    }
                }
			}

		}

		if(in_array($order_status,array('Void','Refund'))){
			if ((strtotime($term_date) <= strtotime(date('Y-m-d'))) || (strtotime($term_date) == strtotime($effective_date))) {
				$member_status = 'Inactive';
				$policy_status = 'Inactive';
			}
		}

		return array('member_status' => $member_status,'policy_status' => $policy_status);
	}

	public function get_status_by_term_date($customer_id=0,$ws_id=0,$termination_date="",$is_term_date_removed=false){
		global $pdo;
		$today = date('Y-m-d');
		$policy_status = "Active";
		$member_status = "Active";

		if($ws_id > 0){
			$get_ws_row = $pdo->selectOne("SELECT termination_date,eligibility_date FROM website_subscriptions where id = :id",array(":id" => $ws_id));
			if(!empty($get_ws_row['termination_date']) && $get_ws_row['termination_date'] != '0000-00-00'){
				$term_date = $get_ws_row['termination_date'];
				if(strtotime($term_date) <= strtotime($today)){
					$policy_status = 'Inactive';
				}
			}
			if(!empty($termination_date)){
				if((strtotime($termination_date) <= strtotime($today)) || strtotime($termination_date) == strtotime($get_ws_row['eligibility_date'])){
					$policy_status = 'Inactive';
				}
			}
		}

		if($customer_id > 0){
			$get_ws_rows = $pdo->select("SELECT termination_date FROM website_subscriptions where customer_id = :id",array(":id" => $customer_id));
			$is_future_term_date = false;
			foreach ($get_ws_rows as $key => $value) {
				if(!empty($value['termination_date']) && $value['termination_date'] != '0000-00-00'){
					$termination_date = $value['termination_date'];
					if(strtotime($termination_date) >= strtotime($today)){
						$is_future_term_date = true;
					}
				}else{
					$is_future_term_date = true;
				}
			}
			if($is_future_term_date){
				$member_status = "Active";
			}else{
				$member_status = "Inactive";
			}
		}

		if($is_term_date_removed){
			$policy_status = "Active";
		}
		
		return array('member_status' => $member_status,'policy_status' => $policy_status);
	}

	public function get_status_by_change_benefit_tier($effective_date="",$change_date="",$ws_status="",$termination_date="",$extra=array()){
		global $pdo;
		$today = date('Y-m-d');
		$policy_status = "Pending";
		$member_status = "Pending";
		$dependent_status = "Pending";
		$old_policy_status = "Active";
		$old_dependent_status = "Active";

		if(!empty($effective_date) && !empty($change_date)){
			if ((strtotime($effective_date) == strtotime($change_date)) || strtotime($change_date) <= strtotime($today)) {
				$policy_status = "Active";
				$member_status = "Active";
				$dependent_status = "Active";
				if(!empty($ws_status) && ($ws_status == 'Pending Payment' || $ws_status == 'Post Payment')){
					$policy_status = 'Post Payment';
					$dependent_status = "Post Payment";
				}
				$old_policy_status = "Inactive";
				$old_dependent_status = "Inactive";
			}else{
				$member_status = "Active";
			}
		}
		if(!empty($termination_date)){
			if(strtotime($termination_date) <= strtotime($today)) {
                $dependent_status = "Inactive";
            }
		}

		if(!empty($extra) && isset($extra['is_cancel_benefit_tier'])){
			if($extra['is_cancel_benefit_tier']){
				$policy_status = "Inactive";
				$dependent_status = "Inactive";
				$old_policy_status = "Active";
				$old_dependent_status = "Active";
			}
		}

		return array('member_status' => $member_status,'policy_status' => $policy_status,'old_policy_status' => $old_policy_status,'dependent_status' => $dependent_status,'old_dependent_status' => $old_dependent_status);
	}
}