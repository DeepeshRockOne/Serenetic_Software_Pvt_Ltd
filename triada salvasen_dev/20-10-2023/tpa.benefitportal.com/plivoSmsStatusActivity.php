<?php 

include_once __DIR__ . '/includes/connect.php';

$service = 'Plivo';						

$content = file_get_contents('php://input');
$resArr = array();
parse_str($content,$resArr);
if(!empty($resArr)){
	$messageId = checkIsset($resArr['MessageUUID']);
	$toNumber = checkIsset($resArr['To']);
	$errorCode = checkIsset($resArr['ErrorCode']);
	$fromNumber = checkIsset($resArr['From']);
	$messageBody = checkIsset($resArr['Text']);
	$messageState = checkIsset($resArr['Status']);

	$toNumber = '+'.$toNumber;
	$fromNumber = '+'.$fromNumber;

	$messageDirection = 'outbound';
	$sqlTwilioNumber = "SELECT GROUP_CONCAT(DISTINCT TwilioNumber) as PlivoNumbers FROM twilio_numbers WHERE service='Plivo' AND is_deleted='N'";
	$resPlivoNumber = $pdo->selectOne($sqlTwilioNumber);
	
	if(!empty($resPlivoNumber['PlivoNumbers']) && in_array($toNumber,explode(',',$resPlivoNumber['PlivoNumbers']))){
		$messageDirection = 'inbound';
	}
	if(!empty($messageDirection)){
		// Read incoming sms
		if($messageDirection == "inbound"){
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
					foreach ($PLIVO_STOP_KEYWORDS as $key => $value) {
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
					foreach ($PLIVO_START_KEYWORDS as $key => $value) {
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
		}else{
			// Receive status callback

			$selSmsLog = "SELECT id as logId,to_number FROM sms_log WHERE to_number=:toNumber AND message_id=:messageId";
			$params = array(":toNumber" => $toNumber,":messageId" => $messageId);
			$resSmsLog = $pdo->selectOne($selSmsLog,$params);

			$status = $messageState;	
			
			if(!empty($resSmsLog)){
				$error_code = '';
				//Check is message delivered or not
				if(in_array($status,array('failed','undelivered','rejected'))) {
					
					$error_code = $errorCode;
					$error_message = checkIsset($errorArr["Text"]);
					
					//Check is message not delivered then update status
					if(!empty($error_code)) {

						$errorCodeWhere = array(
							":code" => $error_code,
							":service" => $service,
						);
						$errorCodes = $pdo->selectOne("SELECT id FROM sms_error_codes WHERE code=:code AND service=:service",$errorCodeWhere);
						if(empty($errorCodes['id'])) {
							$code_data = array(
								'service' => $service,
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
					'service' => $service,
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