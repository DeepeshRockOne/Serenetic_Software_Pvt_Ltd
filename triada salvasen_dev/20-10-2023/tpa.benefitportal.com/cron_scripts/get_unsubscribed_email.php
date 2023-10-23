<?php 
	ini_set('memory_limit', '-1');
	ini_set('max_execution_time', '-1');
	include_once dirname(__DIR__) . "/cron_scripts/connect.php";

	// API Document https://sendgrid.com/docs/API_Reference/Web_API/unsubscribes.html
	$api_user = 'apikey';
	$api_key = $SENDGRID_API_KEY;
	$token = $SENDGRID_API_KEY;

	//****************************************** UNSUBSCRIBER LIST GET REQUEST  ********************************************
		$url = "https://api.sendgrid.com/api/unsubscribes.get.json?api_user=".$api_user."&api_key=".$api_key."&date=1&days=2"; 
		$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $url,
			    CURLOPT_HTTPHEADER => array('Content-Type: application/json','authorization: Bearer '.$token
			  	),
			));
		$resp = curl_exec($curl);
		curl_close($curl);
	//****************************************** UNSUBSCRIBER LIST GET REQUEST  ********************************************
		$unsubscriberArr = json_decode($resp,true);

		$insEmailsArr = array();
		$unsubscribedAlready = array();
		$count=0;
		
		if(!empty($unsubscriberArr)){

			$unsubscribe_email_arr = array_column($unsubscriberArr, 'email');
			$unsubscribe_email_ids = "'".implode("','", $unsubscribe_email_arr)."'";

			// checking if email is exist in our system
			$resMbrEmails = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(email)) as mbr_emails FROM customer WHERE email IN($unsubscribe_email_ids) AND is_deleted='N'");
			$resLeadEmails = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(email)) as lead_emails FROM leads WHERE email IN($unsubscribe_email_ids) AND is_deleted='N'");
			
			$mbrEmailsArr = (!empty($resMbrEmails) ? explode(",", $resMbrEmails['mbr_emails']) : array());
			$leadEmailsArr = (!empty($resLeadEmails) ? explode(",", $resLeadEmails['lead_emails']) : array());

			$insEmailsArr = array_merge($mbrEmailsArr,$leadEmailsArr);

			// checking if email already in unsubscribed table
				$foundEmailsIds = (!empty($insEmailsArr) ? "'".implode("','", $insEmailsArr)."'" : '');
				if(!empty($foundEmailsIds)){
					$resUnsubscribes = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(email)) as emails FROM unsubscribes WHERE email IN($foundEmailsIds) AND is_deleted='N'");
					$unsubscribedAlready = (!empty($resUnsubscribes['emails']) ? explode(",", $resUnsubscribes['emails']) : array());
				}
		}
		
	if(!empty($unsubscriberArr)){
		foreach ($unsubscriberArr as $row) {
			if(in_array($row['email'], $insEmailsArr) && !in_array($row['email'], $unsubscribedAlready)){
				$insParams = array(
							"type" => 'email',
							"email" => $row['email'],
							"added_date" => $row['created'],
						  );
				$pdo->insert("unsubscribes",$insParams);
				$count++;
			}
		}
	}
	
	echo "Total Records: ".count($unsubscriberArr)." Inserted Records: ".$count." Script Completed";
	dbConnectionClose();
?>