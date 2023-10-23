<?php 

	include_once dirname(__DIR__) . "/cron_scripts/connect.php";
	
	$from_number = str_replace($callingCode,$callingCodeReplace, $_POST['From']);
	$to_number = str_replace($callingCode,$callingCodeReplace, $_POST['To']);
	$body = $_POST['Body'];
	$upperBody = strtoupper(trim($_POST['Body']));

	if(!empty($from_number)){

		$selMbrPhone = "SELECT id as userId,type as userType,cell_phone as phoneNo FROM customer WHERE cell_phone=:phone_no";
		$paramsMbrPhone = array(":phone_no" => $from_number);
		$resPhone = $pdo->selectOne($selMbrPhone,$paramsMbrPhone);		

		if(empty($resPhone)){
			$selLeadPhone = "SELECT id as userId,cell_phone as phoneNo FROM leads WHERE cell_phone=:phone_no";
			$paramsLeadPhone = array(":phone_no" => $from_number);
			$resPhone = $pdo->selectOne($selLeadPhone,$paramsLeadPhone);			
		}

		if(!empty($resPhone['userId']) && !empty($upperBody)){
			$userId = $resPhone['userId'];
			$userType = !empty($resPhone['userType']) ? $resPhone['userType'] : 'Leads';
			// SMS history 
				$historyParams = array(
							"user_id" => $userId,
							"user_type" => $userType,
							"from_number" => $from_number,
							"to_number" => $to_number,
							"sms_content" => makeSafe($body)
						);
				$pdo->insert("sms_history",$historyParams);

			// User request for Unsubscribe SMS
			foreach ($STOP_KEYWORDS as $key => $value) {
				if (preg_match("~\b".$value."\b~",$upperBody)) {
					$insParams = array(
						"type" => 'sms',
						"phone" => $from_number,
						"added_date" => 'msqlfunc_NOW()',
					);
					$resUnsubscribes = $pdo->selectOne("SELECT id,phone FROM unsubscribes WHERE phone=:phone AND is_deleted='N'",array(":phone" => $from_number));
					if(empty($resUnsubscribes['phone'])){
						$pdo->insert("unsubscribes",$insParams);
					}
				}
			}
			// User request for Resubscribe SMS
			foreach ($START_KEYWORDS as $key => $value) {
				if (preg_match("~\b".$value."\b~",$upperBody)) {
					$resUnsubscribes = $pdo->selectOne("SELECT id,phone FROM unsubscribes WHERE phone=:phone AND is_deleted='N'",array(":phone" => $from_number));
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
?>