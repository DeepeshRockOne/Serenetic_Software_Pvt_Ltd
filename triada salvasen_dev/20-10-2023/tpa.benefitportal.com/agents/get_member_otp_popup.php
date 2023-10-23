<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$member_id = $_REQUEST['member_id'];
$member_sql = "SELECT * FROM customer WHERE md5(id)=:id";
$member_res = $pdo->selectOne($member_sql,array(":id"=>$member_id));

if(isset($_POST["save"])){
	$otp=isset($_POST['otp'])?$_POST['otp']:'';	
	$validate->string(array("required" => true, "field" => "otp", "value" => $otp), array("required" => "Please enter Code"));
	if (!$validate->getError('otp')) {
		if($otp == $_SESSION['member_otp']["otp"]){
			$_SESSION["login_time_stamp"][$member_id] = time();
			$_SESSION['member_access'][$member_id]="true";
			$_SESSION['member_otp']['status']="Succcess";
			setNotifySuccess("Full access granted successfully.");
			redirect("members_details.php?id=".$member_id,true);
		} else {
			$validate->setError("otp","Please enter valid Code");
			$_SESSION['member_otp']['status']="Fail";
		}
	}
}
$errors = $validate->getErrors();	
$template = "get_member_otp_popup.inc.php";
include_once 'layout/iframe.layout.php';
?>
