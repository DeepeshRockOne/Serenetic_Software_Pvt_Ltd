<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

$res = array();
$multipleAgentExport = false;
$commission_duration = checkIsset($_REQUEST['commission_duration']);
$agentIds = (!empty($_REQUEST['agentIds']) && is_array($_REQUEST['agentIds'])) ? implode(",",$_REQUEST['agentIds']) : $_REQUEST['agentIds'];

$pay_period = checkIsset($_REQUEST['pay_period']);
$status = checkIsset($_REQUEST['status']);
$agentsArr = !empty($agentIds) ? explode(",",$agentIds) : array();
$incr = "";

if(empty($agentsArr)) {
	$res["status"] = "fail";
	$res["message"] = "Commissions not found";
	echo json_encode($res);
	exit;
}

if(count($agentsArr) > 1){
	$multipleAgentExport = true;
}


$statusIncr = "";
if(!empty($status)){
	if($status == 'Pending'){
		$statusIncr .= " AND cs.status IN('Pending')";
	}else if($status == 'Approved'){
		$statusIncr .= " AND cs.status IN('Approved')";
	}else if($status == 'Cancelled'){
		$statusIncr .= " AND cs.status IN('Cancelled')";
	}else if($status == 'CompleteDecline'){
		$statusIncr .= " AND cs.status IN('Cancelled','Approved')";
	}
}

if(!empty($agentIds) && !$multipleAgentExport){
 	$agentRes = $pdo->selectOne("SELECT c.id as agentId,c.rep_id as agentDispId,cs.company_name as agencyName,CONCAT(c.fname,' ',c.lname) as agentName FROM customer c JOIN customer_settings cs ON(c.id=cs.customer_id) WHERE c.id IN(".$agentIds.")");

    //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' exported commissions in '. getCustomDate($pay_period).' for',
           'ac_red_2'=>array(
              'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agentRes['agentId']),
              'title'=> $agentRes['agentDispId'],
            ),
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$agentRes['agentId'], 'Agent',"Exported Commissions", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************
}else{
	   //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' exported commissions in '. getCustomDate($pay_period).' for all/multiple agents',
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'Agent',"Exported Commissions", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************
}
	
$incr .= " AND cs.customer_id IN(".$agentIds.")";

if(!empty($commission_duration)){
	$incr .= " AND cs.commission_duration =:commission_duration";
	$sch_params[':commission_duration'] = $commission_duration;
}

if (!empty($pay_period)) {
    $sch_params[':pay_period'] = date('Y-m-d', strtotime($pay_period));
    $incr .= " AND cs.pay_period = :pay_period";
}

	$extraParams = array();
	$extraParams["agentsArr"] = $agentsArr;
	$extraParams["commission_duration"] = $commission_duration;
	$extraParams["statusIncr"] = $statusIncr;
	$extraParams["pay_period"] = $pay_period;

	$job_id = add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Commission Report","commission_export",$incr,$sch_params,$extraParams,'commission_export');
	$reportDownloadURL = $AWS_REPORTING_URL['commission_export']."&job_id=".$job_id;

	$ch = curl_init($reportDownloadURL);
	curl_setopt($ch, CURLOPT_TIMEOUT,1);//Timeout set to 1 Sec
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_POST, false);
	$apiResponse = curl_exec($ch);
	curl_close($ch);

	echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL));
	dbConnectionClose(); 
	exit;
?>