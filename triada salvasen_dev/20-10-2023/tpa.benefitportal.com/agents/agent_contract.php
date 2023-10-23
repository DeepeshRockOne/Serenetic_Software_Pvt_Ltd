<?php
include_once (__DIR__) . '/includes/connect.php';
$open_sponsor_detail = array();
if (isset($_GET['code'])) {
	$code = $_GET['code'];
	
	$checkLink_query = "SELECT c.id,c.fname,c.lname,c.email,c.cell_phone,c.rep_id,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,sponsor_id FROM customer c
	JOIN customer_settings cs ON (cs.customer_id = c.id)
	WHERE md5(c.rep_id)=:invite_key AND (c.status='Invited' OR cs.recontract_status='Pending') AND c.type='Agent'";
	$Linkwhere = array(':invite_key' => $code);
	$agent_res = $pdo->selectOne($checkLink_query, $Linkwhere);
     
	if (!$agent_res) {
		setNotifyError('Sorry! Agent contract not found');
		redirect($AGENT_HOST);
	} elseif ($agent_res['difference'] > 168) {
		setNotifyError('Agent Contract link has expired');
		
		redirect($AGENT_HOST.'/index.php?link=expired&key='.$code);
	}
	
}else if(!empty($_GET["level"])&&!empty($_GET["username"])){
	$level=$_GET["level"];
	$username=$_GET["username"];
	$getLevel=$pdo->selectOne("SELECT id,profile_id,level from agent_coded_level WHERE md5(level_unique)=:level and is_active='Y'",array(":level"=>$level));
	if($getLevel){
		$profile_id = $getLevel["profile_id"];
		$level = $getLevel["level"];
		$level_id = $getLevel["id"];
	}else{
		setNotifyError('Invalid Link');
		redirect($AGENT_HOST);
	}
	//check sponsor is valid agent 
	$open_sponsor_detail=$pdo->selectOne("SELECT c.id,cs.agent_coded_id,c.level from customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE user_name=:user_name AND type='Agent' AND status in ('Active','Contracted')",array(":user_name"=>$username));
	if(!$open_sponsor_detail){
		setNotifyError('Invalid Link');
		redirect($AGENT_HOST);
	}else{
		//only alllowed to enroll IMO3 user which sponsor id 1(democity)
		if($open_sponsor_detail["id"]!=1 && $level=="IMO3"){
			setNotifyError('Invalid Link');
			redirect($AGENT_HOST);
		}else{
			//now check always allowed level enroll on down level
			if($open_sponsor_detail["id"] != 1 && $level_id > $open_sponsor_detail["agent_coded_id"]){
				setNotifyError('Invalid Link');
				redirect($AGENT_HOST);
			}
		}
	}
	//when open enrollment then comes here
}else {
	setNotifyError('Invalid Link');
	redirect($AGENT_HOST);
}

$enrollType="";
if (!empty($agent_res)) {
	$enrollType="invite";
	$AgentType = 'Agent';
	$fname = $agent_res['fname'];
	$lname = $agent_res['lname'];
	$email = $agent_res['email'];
	$phone = $agent_res['cell_phone'];
	$rep_id = $agent_res['rep_id'];
	$hdn_cust_id = $agent_res['id'];
	$sponsor_id = $agent_res['sponsor_id'];
	$sponsor_detail = $pdo->selectOne("SELECT business_name,fname,lname from customer WHERE id=:id", array(":id" => $sponsor_id));
	$_SESSION['AGENT_INFO']['type'] = $AgentType;
	$_SESSION['AGENT_INFO']['rep_id'] = $rep_id;
	$_SESSION['AGENT_INFO']['fname'] = $fname;
	$_SESSION['AGENT_INFO']['lname'] = $lname;
	$_SESSION['AGENT_INFO']['email'] = $email;
	$_SESSION['AGENT_INFO']['cell_phone'] = $agent_res['cell_phone'];
	$_SESSION['AGENT_INFO']['hdn_cust_id'] = $hdn_cust_id;
	$_SESSION['AGENT_INFO']['sponsor_id'] = $sponsor_id;
} else if($open_sponsor_detail){
	$enrollType="self";
	$AgentType = 'Agent';
	$fname = "";
	$lname = "";
	$email = "";
	$phone = "";
	$rep_id = "";
	$hdn_cust_id = 0;
	$sponsor_id = $open_sponsor_detail['id'];
	$sponsor_detail = $pdo->selectOne("SELECT business_name,fname,lname,allow_to_sell from customer WHERE id=:id", array(":id" => $sponsor_id));
	$_SESSION['AGENT_INFO']['type'] = $AgentType;
	$_SESSION['AGENT_INFO']['level'] = $level;
	$_SESSION['AGENT_INFO']['level_id'] = $level_id;
	$_SESSION['AGENT_INFO']['rep_id'] = $rep_id;
	$_SESSION['AGENT_INFO']['fname'] = $fname;
	$_SESSION['AGENT_INFO']['lname'] = $lname;
	$_SESSION['AGENT_INFO']['email'] = $email;
	$_SESSION['AGENT_INFO']['cell_phone'] = $phone;
	$_SESSION['AGENT_INFO']['hdn_cust_id'] = $hdn_cust_id;
	$_SESSION['AGENT_INFO']['sponsor_id'] = $sponsor_id;
	$_SESSION['AGENT_INFO']['allow_to_sell'] = $sponsor_detail['allow_to_sell'];
}else {
	redirect($AGENT_HOST);
}

$exJs = array('js/password_validation.js'.$cache,
'thirdparty/masked_inputs/jquery.inputmask.bundle.js');
$template = 'agent_contract.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>