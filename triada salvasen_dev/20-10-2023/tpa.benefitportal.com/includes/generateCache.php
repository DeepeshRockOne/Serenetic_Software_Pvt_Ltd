<?php
if($SITE_ENV=='Local'){
	if (!file_exists($CACHE_PATH_DIR)) {
	    mkdir($CACHE_PATH_DIR, 0777, true);
	    $keepFile = fopen($CACHE_PATH_DIR.'.keep', 'w');
		fclose($keepFile);
	}
}

if (file_exists($CACHE_PATH_DIR.$CACHE_FILE_NAME)) {
    $cacheMainArray = unserialize(file_get_contents($CACHE_PATH_DIR.$CACHE_FILE_NAME));

    $cacheMainArray['generateFrom']='Cache';
    $setrs = $cacheMainArray['setrs'];
    $emailer_settings = $cacheMainArray['emailer_settings'];
    $cache = $cacheMainArray['cache'];
    $allStateRes = $cacheMainArray['allStateRes'];
    $agentCodedRes = $cacheMainArray['agentCodedRes'];
    $prdPlanTypeArray = $cacheMainArray['prdPlanTypeArray'];
    $getStateNameByShortName = $cacheMainArray['getStateNameByShortName'];
    $allStateShortName = $cacheMainArray['allStateShortName'];
    $allStateResByName = $cacheMainArray['allStateResByName'];
    $prdQuestionRes = $cacheMainArray['prdQuestionRes'];
    $prdBeneficiaryQuestionRes = $cacheMainArray['prdBeneficiaryQuestionRes'];
    $prdPricingQuestionRes = $cacheMainArray['prdPricingQuestionRes'];
    //$cityListCache = $cacheMainArray['cityListCache'];
	$smsServiceProvider = $cacheMainArray['smsServiceProvider'];
    $zipCacheList = $cacheMainArray['zipCacheList'];
    $TwilioAccountSid = $cacheMainArray['TwilioAccountSid'];
    $TwilioAuthToken = $cacheMainArray['TwilioAuthToken'];
    $TwilioNumber = $cacheMainArray['TwilioNumber'];
	$TelnyxApiKey = $cacheMainArray['TwilioAuthToken'];
    $TelnyxNumber = $cacheMainArray['TwilioNumber'];
	$PlivoApiKey = $cacheMainArray['TwilioAccountSid'];
	$PlivoNumber = $cacheMainArray['TwilioNumber'];
	$PlivoAuthToken = $cacheMainArray['TwilioAuthToken'];
}else{
	$setsql = "SELECT * FROM emailer_setting";
	$setrs = $pdo->select($setsql);

	$emailer_settings = array();
	if(!empty($setrs)){
		foreach ($setrs as $key => $setrow) {
		  $emailer_settings[$setrow['company_id']][$setrow['field_name']] = stripslashes($setrow['field_value']);
		}
	}

	$cache=$pdo->selectOne("SELECT * FROM cache_management WHERE id=1");
	if($cache){
		$cache="?_v=".$cache["version"];
	}else{
		$cache="?_v=0";
	}

	$allStateSqlRes = $pdo->select("SELECT * FROM states_c WHERE country_id = 231 ORDER BY name ASC");
	$allStateShortName = array();
	$allStateRes = array();
	$allStateResByName = array();
	$getStateNameByShortName = array();
	foreach ($allStateSqlRes as $key => $value) {
		$getStateNameByShortName[$value['short_name']] = $value['name'];
		$allStateShortName[$value['name']] = $value['short_name'];
		$allStateRes[$value['id']] = $value;
		$allStateResByName[$value['name']] = $value;
	}
	
	$agentCodedResult = $pdo->select("SELECT * FROM agent_coded_level WHERE is_active='Y' ORDER BY id DESC");
	$agentCodedRes = array();
	if(!empty($agentCodedResult)){
		foreach ($agentCodedResult as $key => $value) {
			$agentCodedRes[$value['id']]=$value;
		}
	}

	$benefitTierRes=$pdo->select("SELECT * FROM prd_plan_type where is_active='Y' order by order_by ASC");
	$prdPlanTypeArray = array();

	if(!empty($benefitTierRes)){
		foreach ($benefitTierRes as $key => $value) {
			$prdPlanTypeArray[$value['id']]=$value;
		}
	}


	$prdQuestionSql="SELECT * FROM prd_enrollment_questions WHERE questionType='Default' AND is_deleted='N' order by order_by ASC";
	$prdQuestionRes=$pdo->select($prdQuestionSql);

	$prdBeneficiaryQuestionSql="SELECT * FROM prd_beneficiary_questions WHERE questionType='Default' AND is_deleted='N' order by order_by ASC";
	$prdBeneficiaryQuestionRes=$pdo->select($prdBeneficiaryQuestionSql);

	$prdPricingQuestionSql="SELECT * FROM prd_pricing_question WHERE is_deleted='N' order by order_by ASC";
	$resPrdPricingQuestionRes=$pdo->select($prdPricingQuestionSql);
	$prdPricingQuestionRes = array();
	if(!empty($resPrdPricingQuestionRes)){
		foreach ($resPrdPricingQuestionRes as $key => $value) {
			$prdPricingQuestionRes[$value['id']]=$value;
		}
	}

	/*$sql="SELECT id,city,state_name,zips FROM city_list";
	$res =$pdo->select($sql);

	$cityListCache = array();
	if(!empty($res)){
		foreach ($res as $key => $value) {
			$zips = str_replace(" ", ",", $value['zips']);
			$zipList = explode(",",$zips);

			if(!empty($zipList)){
				foreach ($zipList as $key => $zipCodes) {
					if(!array_key_exists($zipCodes, $cityListCache)){
						$cityListCache[$zipCodes] = $value;
					}
				}
			}
		}
	}
*/
	$sqlZipList= "SELECT z.* FROM zip_code z 
			JOIN states_c s ON (s.short_name = z.state_code)
			WHERE s.country_id = 231";
	$resZipList=$pdo->select($sqlZipList);

	$zipCacheList = array();
	if(!empty($resZipList)){
		foreach ($resZipList as $key => $zipValue) {
			$zipCacheList[$zipValue['zip_code']]=$zipValue;
		}
	}

	

	$TwilioAccountSid = "";
	$TwilioAuthToken = "";
	$TwilioNumber = "";

	$sqlTwilioNumber = "SELECT * FROM twilio_numbers WHERE is_active = 'Y' AND is_deleted='N'";
	$resTwilioNumber = $pdo->selectOne($sqlTwilioNumber);

	if(!empty($resTwilioNumber)){
		$smsServiceProvider = $resTwilioNumber['service'];
		$TwilioAccountSid = $resTwilioNumber['TwilioAccountSid'];
		$TwilioAuthToken=  $resTwilioNumber['TwilioAuthToken'];
		$TwilioNumber = $resTwilioNumber['TwilioNumber'];
		$TelnyxApiKey = $resTwilioNumber['TwilioAuthToken'];
    	$TelnyxNumber = $resTwilioNumber['TwilioNumber'];
		$PlivoApiKey = $resTwilioNumber['TwilioAccountSid'];
		$PlivoNumber = $resTwilioNumber['TwilioNumber'];
		$PlivoAuthToken = $resTwilioNumber['TwilioAuthToken'];
	}
	
	$cacheMainArray=array(
		'generateFrom'=>'Query',
		"setrs"=>$setrs,
		"emailer_settings"=>$emailer_settings,
		"cache"=>$cache,
		"allStateRes"=>$allStateRes,
		"getStateNameByShortName"=>$getStateNameByShortName,
		"allStateShortName"=>$allStateShortName,
		"allStateResByName"=>$allStateResByName,
		"agentCodedRes"=>$agentCodedRes,
		"prdPlanTypeArray"=>$prdPlanTypeArray,
		"prdQuestionRes"=>$prdQuestionRes,
		"prdBeneficiaryQuestionRes"=>$prdBeneficiaryQuestionRes,
		"prdPricingQuestionRes"=>$prdPricingQuestionRes,
		//"cityListCache"=>$cityListCache,
		"zipCacheList"=>$zipCacheList,
		"smsServiceProvider"=>$smsServiceProvider,
		"TwilioAccountSid"=>$TwilioAccountSid,
		"TwilioAuthToken"=>$TwilioAuthToken,
		"TwilioNumber"=>$TwilioNumber,
	);

	$cached = fopen($CACHE_PATH_DIR.$CACHE_FILE_NAME, 'w');
	fwrite($cached, serialize($cacheMainArray));
	fclose($cached);
}
?>