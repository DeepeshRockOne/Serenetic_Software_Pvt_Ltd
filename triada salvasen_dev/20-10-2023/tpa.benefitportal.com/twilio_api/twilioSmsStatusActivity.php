<?php 
	include_once dirname(__DIR__) . "/cron_scripts/connect.php";

	$fromNo = $_POST['From'];
	$toNo = $_POST['To'];

	$msgId = $_REQUEST['MessageSid'];
	$status = $_REQUEST['MessageStatus'];

	if(!empty($msgId) && !empty($toNo)){

		$selSmsLog = "SELECT id as logId,to_number FROM sms_log WHERE to_number=:toNo AND message_id=:msgId";
		$params = array(":toNo" => $toNo,":msgId" => $msgId);
		$resSmsLog = $pdo->selectOne($selSmsLog,$params);			
		
		if(!empty($resSmsLog)){
			
			$error_code = '';
			//Check is message delivered or not
			if(in_array($status,array('failed','undelivered'))) {
				$client = new Twilio\Rest\Client($TwilioAccountSid, $TwilioAuthToken);
				try {
				    $message = $client->messages($msgId)->fetch();
				    $error_code = $message->errorCode;
			    	
				    //Check is message not delivered then update status
				    if(!empty($error_code)) {
				    	$error_message = $message->errorMessage;

				    	$selSmsCode = "SELECT id FROM sms_error_codes WHERE service='Twilio' AND code=:code";
						$codeParams = array(":code" => $error_code);
						$resSmsCode = $pdo->selectOne($selSmsCode,$codeParams);
						if(empty($resSmsCode["id"])) {				    		
				    		$code_data = array(
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
				} catch (Exception $ex) {

				}
			}

			$insLogDet = array(
				'service' => 'Twilio',
				'log_id' => $resSmsLog['logId'],
				'status' => $status,
				'error_code' => $error_code,
            );
		    $pdo->insert("sms_log_details", $insLogDet);
		}
	}

?>