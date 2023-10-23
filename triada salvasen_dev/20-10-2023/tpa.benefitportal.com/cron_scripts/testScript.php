<?php

	include_once dirname(__DIR__) . "/cron_scripts/connect.php";

	$msg = "Message Recent Activity Test";
	$toPhone = "+919712028991";

	$smsId = 0;
    
    $url="https://smarteapp.com/twilio_api/twilioSmsStatusActivity.php";

    $client = new Twilio\Rest\Client($TwilioAccountSid, $TwilioAuthToken);
    try {
    	$msgResponse = $client->messages->create(
            $toPhone,
            array(
              'from' => $TwilioNumber,
              'body' => $msg,
               "statusCallback" => $url,
            )
        );
        $smsStatus = 'success';
        $smsId = $msgResponse->sid;
    } catch (Exception $ex) {
        $smsStatus = 'fail';
    }


    $insLog = array(
	    'message_id' => $smsId,
	    'from_number' => $TwilioNumber,
	    'to_number' => $toPhone,
	    'message' => $msg,
	    'status' => $smsStatus,
	);
	$pdo->insert("sms_log", $insLog);

    echo $smsStatus;
    echo "Completed";


?>

   

		
