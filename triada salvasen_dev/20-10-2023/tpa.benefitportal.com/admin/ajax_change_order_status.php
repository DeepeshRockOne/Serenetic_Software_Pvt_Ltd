<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/policy_setting.class.php';
require_once dirname(__DIR__) . '/includes/member_setting.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
$policySetting = new policySetting();
$functionsList = new functionsList();
$commObj = new Commission();
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();

$res = array();
$validate = new Validation();
$adminId = $_SESSION['admin']['id'];

$orderId = $_REQUEST['orderId'];
$oldOrderStatus = checkIsset($_REQUEST['oldStatus']);
$newOrderStatus = checkIsset($_REQUEST['newStatus']);

$reason = checkIsset($_REQUEST['reason']);

if($validate->isValid()){
	
	$selOrder = "SELECT id,customer_id,display_id,is_renewal,status,subscription_ids FROM orders WHERE md5(id) = :odrId";
    $resOrder = $pdo->selectOne($selOrder, array(':odrId' => $orderId));

    if(!empty($resOrder)){
    	$activityFeedDesc = array();
    	$userData = get_user_data($_SESSION['admin']);

    	$orderId = $resOrder["id"];
    	$oldOrderStatus = $resOrder["status"];
    	$customerId = $resOrder["customer_id"];

    	$odrUpdParams['status'] = makeSafe($newOrderStatus);

    	
    	
        // update order details table for refund code start
	    	if(in_array($oldOrderStatus,array("Refund","Void")) && $newOrderStatus=='Payment Approved'){
	          $updOdrDetParams = array(
	            'is_refund' => "N",
	          );
	          $updOdrDetWhere = array("clause" => 'order_id=:id', 'params' => array(':id'=>$orderId));
	          $pdo->update("order_details", $updOdrDetParams, $updOdrDetWhere);
	        }

	        
	    // update order details table for refund code end
        
        // update order code start
	        $orderReason = "Admin manually updated Order status";
	       	$odrUpdParams['order_comments'] = $orderReason;

	        if($newOrderStatus == 'Payment Approved'){
	            $odrUpdParams['created_at'] = "msqlfunc_NOW()";
	        }       

	        $odrUpdWhere = array(
	            'clause' => 'id = :id',
	            'params' => array(
	                ':id' => makeSafe($orderId)
	            )
	        );
        	$pdo->update("orders", $odrUpdParams, $odrUpdWhere);
        	
        	$oldOdr = array("status" => $oldOrderStatus);
        	$newOdr = array("status" => $newOrderStatus);
        	$odrCheckDiff=array_diff_assoc($oldOdr, $newOdr);
		    if(!empty($odrCheckDiff)){ 
		      foreach ($odrCheckDiff as $key1 => $value1) {
		        $activityFeedDesc['key_value']['desc_arr'][$key1]='From '. (!empty($oldOdr[$key1]) ? $oldOdr[$key1] : 'blank').' To '.$newOdr[$key1] ." on Order ".checkIsset($resOrder["display_id"]); 
		      }
		    }

		    audit_log($userData, $resOrder['customer_id'], "customer", 'Order Status Changed from ' . $oldOrderStatus . ' to '.$newOrderStatus,$resOrder['id']);
        // update order code ends

		// transaction insert code start
		    $transInsId = array();
		    $transParams = array("reason" => checkIsset($orderReason));
    	  	if($newOrderStatus != $oldOrderStatus) {
            	if($newOrderStatus=='Payment Approved'){
                    if($resOrder['is_renewal'] == 'Y'){
                       $transInsId=$functionsList->transaction_insert($orderId,'Credit','Renewal Order','Transaction Approved',0,$transParams);
                    } else {
                       $transInsId=$functionsList->transaction_insert($orderId,'Credit','New Order','Transaction Approved',0,$transParams);
                    }
                }else if ($newOrderStatus=="Chargeback"){
                	
                    $transInsId=$functionsList->transaction_insert($orderId,'Debit','Chargeback','Transaction Chargeback',0,$transParams);
                }else if ($newOrderStatus=="Refund"){
                    $transInsId=$functionsList->transaction_insert($orderId,'Debit','Refund Order','Transaction Refund',0,$transParams);
                }else if ($newOrderStatus=="Void"){
                    $transInsId=$functionsList->transaction_insert($orderId,'Debit','Void Order','Transaction Void',0,$transParams);
                }else if ($newOrderStatus=="Payment Declined"){
                    $transInsId=$functionsList->transaction_insert($orderId,'Debit','Payment Declined','Transaction Declined',0,$transParams);
                }else if ($newOrderStatus=="Payment Returned"){
                    $transInsId=$functionsList->transaction_insert($orderId,'Debit','Payment Returned','Transaction Returned',0,$transParams);
                }else if ($newOrderStatus=="Cancelled"){
                    $transInsId=$functionsList->transaction_insert($orderId,'Debit','Cancelled','Transaction Cancelled',0,$transParams);
                }
            }
    	// transaction insert code ends


        if(in_array($newOrderStatus,array("Refund","Void"))){
       	 	$updOdrDetParams = array(
              'is_refund' => "Y",
            );
            $updOdrDetWhere = array("clause" => 'order_id=:id', 'params' => array(':id'=>$orderId));
            $pdo->update("order_details", $updOdrDetParams, $updOdrDetWhere);
		}
		
		if(in_array($newOrderStatus, array('Chargeback','Refund','Void','Payment Returned','Payment Declined'))){
            $payable_params=array(
                'payable_type'=>'Reverse_Vendor',
				'type'=>'Vendor',
				'transaction_tbl_id' => $transInsId['id'],
            );
            $payable = $functionsList->payable_insert($orderId,0,0,0,$payable_params);
        }

        // Adjust order commission code start
	       	if(in_array($newOrderStatus, array("Payment Declined","Cancelled","Void","Refund"))){
	        	$extraParams["note"] = "Commission reversed when Order status changed to ".$newOrderStatus;
	        	$extraParams["transaction_tbl_id"] = $transInsId['id'];
	        	$commObj->reverseOrderCommissions($orderId,$extraParams);

	       	}else if ($newOrderStatus == 'Payment Returned') {
	            $extraParams['note'] = $reason;
	            $extraParams["transaction_tbl_id"] = $transInsId['id'];
	            $commObj->reverseOrderCommissions($orderId,$extraParams);
	            
	        } elseif ($newOrderStatus == 'Chargeback') {
	            $extraParams['note'] = $chargeBackNote;
	            $extraParams["transaction_tbl_id"] = $transInsId['id'];
	            $commObj->reverseOrderCommissions($orderId,$extraParams);
	        } 
        // Adjust order commission code ends
	    // Member status and policy status 
	       	$member_setting = $memberSetting->get_status_by_order_status($newOrderStatus);    
	    // Chargeback status code start
	        if ($newOrderStatus == 'Chargeback') {
	        	$resMbr = array();
				$selMbr = "SELECT id,rep_id,status FROM customer WHERE is_deleted='N' AND id=:mbrId";
				$resMbr = $pdo->selectOne($selMbr,array(":mbrId" =>$customerId));

	            $custUpdParams = array(
	                'status' => $member_setting['member_status'],
	            );

	            $custUpdWhere = array(
	                'clause' => 'id = :id',
	                'params' => array(
	                    ':id' => makeSafe($customerId)
	                )
	            );
            	$pdo->update("customer", $custUpdParams, $custUpdWhere);

            	audit_log($userData, $resMbr['id'], "customer", 'Member Status Changed from ' . $resMbr['status'] . ' to '.$custUpdParams["status"],$resMbr['id']);

            	$oldValArr = $resMbr;
				unset($oldValArr["id"]);
				unset($oldValArr["rep_id"]);
				$mbrCheckDiff=array_diff_assoc($oldValArr, $custUpdParams);
		
			    if(!empty($mbrCheckDiff)){ 
			      foreach ($mbrCheckDiff as $key1 => $value1) {
			        $activityFeedDesc['key_value']['desc_arr']["Member Status"]='From '. (!empty($oldValArr[$key1]) ? $oldValArr[$key1] : 'blank').' To '.$custUpdParams[$key1] ." on Member ".checkIsset($resMbr["rep_id"]); 
			      }
			    }


            	$orderSubscriptionIds = $resOrder['subscription_ids'];

	            $wsSql = "SELECT ws.id,ws.status,ws.website_id,ws.customer_id,ws.product_id,ws.plan_id,ws.fee_applied_for_product,ws.eligibility_date FROM website_subscriptions ws WHERE ws.id IN ($orderSubscriptionIds)";
	            $wsRes = $pdo->select($wsSql);

	            if (count($wsRes) > 0) {
	                foreach ($wsRes as $key => $wsRow) {
	                	$extra_params = array();
			            $extra_params['location'] = "change_order_status";
			            $extra_params['activity_feed_flag'] = "change_order_status";
			            $termination_reason = "Chargeback";
			            $policySetting->setTerminationDate($wsRow['id'],$wsRow['eligibility_date'],$termination_reason,$extra_params);

	                    $oldVaArray = array("status"=> $wsRow["status"]);
						$newVaArray = array("status"=> $member_setting['policy_status']);
			            
			            $subCheckDiff = array_diff_assoc($oldVaArray, $newVaArray);
			            if(!empty($subCheckDiff)){ 
					      foreach ($subCheckDiff as $key1 => $value1) {
					        $activityFeedDesc['key_value']['desc_arr']["Plan Status"]='From '. (!empty($oldVaArray[$key1]) ? $oldVaArray[$key1] : 'blank').' To '.$newVaArray[$key1] ." on Subscription ".checkIsset($wsRow["website_id"]); 
					      }
					    }
	                }
	            }
        	}
	    // Chargeback status code ends

       	// Payment Returned Code Start
        	if ($newOrderStatus == 'Payment Returned') {
	            $orderSubscriptionIds = $resOrder['subscription_ids'];
	            $wsSql = "SELECT ws.id FROM website_subscriptions ws WHERE ws.id IN ($orderSubscriptionIds)";
	            $wsRes = $pdo->select($wsSql);
            	if (count($wsRes) > 0) {
                	foreach ($wsRes as $key => $wsRow) {
                		$termination_date = $enrollDate->getTerminationDate($wsRow['id']);

                        $extra_params = array();
                        $extra_params['location'] = "ach_pending_settlement_transactions";
                        $extra_params['message'] = "Subscription Plan Terminated (Payment Returned)";
                        $extra_params['activity_feed_flag'] = "change_order_status";
                        $termination_reason = "Payment Returned";
                        $policySetting->setTerminationDate($wsRow['id'],$termination_date,$termination_reason,$extra_params);
	                }
	            }
        	}
        // Payment Returned Code End

        // Payment Approved Code Start
        	if($newOrderStatus == 'Payment Approved'){

				$payable_params=array(
					'payable_type'=>'Vendor',
					'type'=>'Vendor',
					'transaction_tbl_id' => $transInsId['id'],
				);
				$payable = $functionsList->payable_insert($orderId,0,0,0,$payable_params);

        		$resMbr = array();
        		$selMbr = "SELECT id,rep_id,status FROM customer WHERE is_deleted='N' AND id=:mbrId";
        		$resMbr = $pdo->selectOne($selMbr,array(":mbrId" =>$customerId));

	            $custUpdParams = array(
	                'status' => $member_setting['member_status'],
	            );

	            $custUpdWhere = array(
	                'clause' => 'id = :id',
	                'params' => array(
	                    ':id' => makeSafe($customerId)
	                )
	            );
	            $pdo->update("customer", $custUpdParams, $custUpdWhere);

	            $updOdrDetParams = array(
	              'is_refund' => "N",
	              'is_chargeback' => "N",
	              'is_payment_return' => "N",
	            );
	            $updOdrDetWhere = array("clause" => 'order_id=:id', 'params' => array(':id'=>$orderId));
	            $pdo->update("order_details", $updOdrDetParams, $updOdrDetWhere);

	            $oldValArr = $resMbr;
				unset($oldValArr["id"]);
				unset($oldValArr["rep_id"]);
				$mbrCheckDiff=array_diff_assoc($oldValArr, $custUpdParams);
		
			    if(!empty($mbrCheckDiff)){ 
			      foreach ($mbrCheckDiff as $key1 => $value1) {
			        $activityFeedDesc['key_value']['desc_arr']["Member Status"]='From '. (!empty($oldValArr[$key1]) ? $oldValArr[$key1] : 'blank').' To '.$custUpdParams[$key1] ." on Member ".checkIsset($resMbr["rep_id"]); 
			      }
			    }

			    audit_log($userData, $resMbr['id'], "customer", 'Member Status Changed from ' . $resMbr['status'] . ' to '.$custUpdParams["status"],$resMbr['id']);

            	$orderSubscriptionIds = $resOrder['subscription_ids'];

	            $wsSql = "SELECT ws.id,ws.website_id,ws.customer_id,ws.product_id,ws.plan_id,ws.fee_applied_for_product,ws.status,ws.termination_date,ws.term_date_set,p.product_type,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count,ws.eligibility_date
	            			FROM website_subscriptions ws 
	            			JOIN prd_main p ON (p.id=ws.product_id)
	            			WHERE ws.id IN ($orderSubscriptionIds)";
	            $wsRes = $pdo->select($wsSql);

	            if (count($wsRes) > 0) {
                	foreach ($wsRes as $key => $wsRow) {
                		$extra_params = array();
					    $extra_params['location'] = "member_detail";
					    $extra_params['portal'] = 'admin';
					    $policySetting->removeTerminationDate($wsRow['id'],$extra_params);

                		$wsUpdParams = array();
                   		
                   		/*------ Set Termination Date for Healthy Step ------*/
						if($wsRow['product_type'] == "Healthy Step") {
							if($wsRow['is_member_benefits'] == "Y" && $wsRow['is_fee_on_renewal'] == "Y" && $wsRow['fee_renewal_type'] == "Renewals" && $wsRow['fee_renewal_count'] > 0) {

								$member_payment_type=getname('prd_main',$wsRow['product_id'],'member_payment_type','id');
								$product_dates = $enrollDate->getCoveragePeriod($wsRow['eligibility_date'],$member_payment_type);
								$tmp_fee_renewal_count = $wsRow['fee_renewal_count'];
								$tmp_start_coverage_date = $product_dates['startCoveragePeriod'];
								$tmp_termination_date = $product_dates['endCoveragePeriod'];
								while ($tmp_fee_renewal_count > 0) {
									$product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
									$tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
									$tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
									$tmp_fee_renewal_count--;
								}
								$wsUpdParams['termination_date'] = $tmp_termination_date;
								$wsUpdParams['term_date_set'] = date('Y-m-d');
								$wsUpdParams['termination_reason'] = 'Policy Change';
							}
						}
						/*------/Set Termination Date for Healthy Step ------*/

                    	if(!empty($wsUpdParams)) {
	                    	$wsUpdWhere = array(
		                        'clause' => 'id = :id',
		                        'params' => array(
		                            ':id' => makeSafe($wsRow['id'])
		                        )
		                    );
		                    
	                    	$updParamsKey =  implode(",", array_keys($wsUpdParams));
						    $oldWsSql = "SELECT id,$updParamsKey FROM website_subscriptions WHERE id=:subId";
						    $oldWsRes = $pdo->selectOne($oldWsSql, array(":subId" =>$wsRow['id']));

		                    $pdo->update("website_subscriptions", $wsUpdParams, $wsUpdWhere);

		                    $oldVaArray = $oldWsRes;
					        $newVaArray = $wsUpdParams;
					        unset($oldVaArray['id']);
	        				$subCheckDiff=array_diff_assoc($newVaArray, $oldVaArray);

	        				if(!empty($subCheckDiff)){ 
						      foreach ($subCheckDiff as $key1 => $value1) {
						        $activityFeedDesc['key_value']['desc_arr'][$key1]='From '. (!empty($oldVaArray[$key1]) ? $oldVaArray[$key1] : 'blank').' To '.$newVaArray[$key1] ." on Subscription ".checkIsset($wsRow["website_id"]); 
						      }
						    }
                    	}
                    }

                    //update next purchase date code start
	                $enrollDate->updateNextBillingDateByOrder($orderId);
	                //update next purchase date code end
            	}
        	}
        // Payment Approved Code Ends

       	if(!empty($activityFeedDesc)){
       		$activityFeedDesc1 = array();
       		$activityFeedDesc1['ac_message'] =array(
			    'ac_red_1'=>array(
			      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			      'title'=>$_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' =>' updated Order '.$resOrder["display_id"],
		  	);
		  	$activityFeedDesc1 = array_merge($activityFeedDesc1,$activityFeedDesc);
		    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $customerId, 'customer','Admin updated Order Status', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc1));
		}
       

        $res['status'] = 'success';
        $res['msg'] = 'Order status updated successfully...';
    }else{
	    $res['status'] = 'error';
	    $res['msg'] = 'Something went wrong';
    }

}else{
    $errors = $validate->getErrors();
    $res['status'] = 'fail';
    $res['errors'] = $errors;
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>