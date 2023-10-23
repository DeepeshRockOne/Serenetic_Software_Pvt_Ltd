<?php 

	include_once __DIR__ . '/includes/connect.php';

	$json = file_get_contents('php://input');
	$resArr = json_decode($json, true);
	// pre_print($resArr,false);

	if(!empty($resArr)){
		$eventType = checkIsset($resArr["data"]["event_type"]);
		$payLoad = checkIsset($resArr["data"]["payload"],'arr');
		
		$messageId = checkIsset($payLoad["id"]);
		$toNumber = checkIsset($payLoad["to"][0]["phone_number"]);
		$errorArr = checkIsset($payLoad["errors"][0]);
		$fromNumber = checkIsset($payLoad["from"]["phone_number"]);
		$messageBody = checkIsset($payLoad["text"]);
		$status =  checkIsset($payLoad["to"][0]["status"]);

		if(!empty($eventType)){
			// Read incoming sms
			if($eventType == "message.received"){
				if(!empty($fromNumber)){
					$fromNumber = str_replace('+1', '', $fromNumber);
					$toNumber = str_replace('+1', '', $toNumber);
					$selMbrPhone = "SELECT id as userId,type as userType,cell_phone as phoneNo FROM customer WHERE cell_phone=:phone_no";
					$paramsMbrPhone = array(":phone_no" => $fromNumber);
					$resPhone = $pdo->selectOne($selMbrPhone,$paramsMbrPhone);		

					if(empty($resPhone)){
						$selLeadPhone = "SELECT id as userId,cell_phone as phoneNo FROM leads WHERE cell_phone=:phone_no";
						$paramsLeadPhone = array(":phone_no" => $fromNumber);
						$resPhone = $pdo->selectOne($selLeadPhone,$paramsLeadPhone);			
					}

					if(!empty($resPhone['userId']) && !empty($messageBody)){
						$userId = $resPhone['userId'];
						$userType = !empty($resPhone['userType']) ? $resPhone['userType'] : 'Leads';
						// SMS history 
							$historyParams = array(
										"user_id" => $userId,
										"user_type" => $userType,
										"from_number" => $fromNumber,
										"to_number" => $toNumber,
										"sms_content" => makeSafe($messageBody)
									);
							$pdo->insert("sms_history",$historyParams);

						// User request for Unsubscribe SMS
						foreach ($TELNYX_STOP_KEYWORDS as $key => $value) {
							if (preg_match("~\b".$value."\b~",$messageBody)) {
								$insParams = array(
									"type" => 'sms',
									"phone" => $fromNumber,
									"added_date" => 'msqlfunc_NOW()',
								);
								$resUnsubscribes = $pdo->selectOne("SELECT id,phone FROM unsubscribes WHERE phone=:phone AND is_deleted='N'",array(":phone" => $fromNumber));
								if(empty($resUnsubscribes['phone'])){
									$pdo->insert("unsubscribes",$insParams);
								}
							}
						}
						// User request for Resubscribe SMS
						foreach ($TELNYX_START_KEYWORDS as $key => $value) {
							if (preg_match("~\b".$value."\b~",$messageBody)) {
								$resUnsubscribes = $pdo->selectOne("SELECT id,phone FROM unsubscribes WHERE phone=:phone AND is_deleted='N'",array(":phone" => $fromNumber));
								if(!empty($resUnsubscribes['phone'])){
									$params = array('is_deleted' => 'Y',"removed_date"=>'msqlfunc_NOW()');
								    $where = array(
								        'clause' => 'id = :id ', 
								        'params' => array(':id' => $resUnsubscribes['id'])
								      );
								    $pdo->update("unsubscribes", $params, $where);

								}
							}
						}
					}
				}
			}else if(!empty($status)){
				// Receive status callback

				$selSmsLog = "SELECT id as logId,to_number FROM sms_log WHERE to_number=:toNumber AND message_id=:messageId";
				$params = array(":toNumber" => $toNumber,":messageId" => $messageId);
				$resSmsLog = $pdo->selectOne($selSmsLog,$params);
				
				if(!empty($resSmsLog)){
					$error_code = '';
					//Check is message delivered or not
					if(in_array($status,array('sending_failed','delivery_failed','delivery_unconfirmed'))) {
						
					    $error_code = checkIsset($errorArr["code"]);
					    $error_message = checkIsset($errorArr["title"]);
				    	
					    //Check is message not delivered then update status
					    if(!empty($error_code)) {

					    	$selSmsCode = "SELECT id FROM sms_error_codes WHERE service='Telnyx' AND code=:code";
							$codeParams = array(":code" => $error_code);
							$resSmsCode = $pdo->selectOne($selSmsCode,$codeParams);

					    	if(empty($resSmsCode["id"])) {
					    		$code_data = array(
					    			'service' => 'Telnyx',
									'code' => $error_code,
									'error_message' => $error_message,
				                );
							    $pdo->insert("sms_error_codes", $code_data);
					    	}

					    	$where = array(
								'clause' => 'id=:id ', 
								'params' => array(':id' => $resSmsLog['logId'])
							);
							$pdo->update("sms_log", array('status'=>'Fail'), $where);
					    }
						
					}

					$insLogDet = array(
						'service' => 'Telnyx',
						'log_id' => $resSmsLog['logId'],
						'status' => $status,
						'error_code' => $error_code,
		            );
				    $pdo->insert("sms_log_details", $insLogDet);
				}
				
			}
		}
	}
?>