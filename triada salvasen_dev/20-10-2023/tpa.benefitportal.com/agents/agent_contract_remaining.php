<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[2]['title'] = "Application";
$breadcrumbes[2]['class'] = "Active";
$page_title = "Application";
$agent_res = array();

$firstTabOpen = "active in";
if (isset($_SESSION["agents"]['rep_id'])) {
	$code = $_SESSION["agents"]['rep_id'];
	
	$checkLink_query = "SELECT c.id as id ,email,cell_phone,rep_id,sponsor_id,public_name,public_email,public_phone,
						user_name,display_in_member,is_branding,brand_icon,status,account_type,company_name,company_address,company_address_2,company_city,company_state,company_zip,w9_pdf,address,address_2,fname,tax_id,lname,
						city,state,zip,birth_date,type,npn,is_contract_approved,TIMESTAMPDIFF(HOUR,invite_at,now()) as difference,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn,reject_text,c.updated_at 
						FROM customer c 
						LEFT JOIN customer_settings cs on(cs.customer_id=c.id) 
						WHERE rep_id=:invite_key AND (status in ('Pending Approval','Pending Contract','Pending Documentation'))";
	$Linkwhere = array(':invite_key' => $code);
	$agent_res = $pdo->selectOne($checkLink_query, $Linkwhere);
	$contract_business_image=!empty($agent_res["brand_icon"])?$agent_res["brand_icon"]:"";
	
	if (!$agent_res) {
		setNotifyError('Sorry! Agent contract not found');
		redirect($AGENT_HOST.'/index.php');
	} 
	$tz = new UserTimeZone('m/d/Y @ g:i A T',$_SESSION['agents']['timezone']);
	
} 
// $profile_id=$agent_res["agent_coded_profile"];
$selADoc = "SELECT id,selling_licensed_state,license_active_date,license_num,license_exp_date,license_not_expire,license_type,license_auth FROM agent_license WHERE agent_id=:agent_id AND is_deleted='N'";
$whrADoc = array(":agent_id" => $agent_res['id']);
$resADoc = $pdo->select($selADoc, $whrADoc);

// if(empty($resADoc)){$resADoc[]=1;}	
if(!empty($resADoc)){
	$selectedState = array();
	foreach ($resADoc as $st) {
		$selectedState[]=$st['selling_licensed_state'];
	}
}
$selDoc = "SELECT e_o_coverage,by_parent,by_parent,e_o_amount,e_o_expiration,e_o_document,process_commission FROM agent_document WHERE agent_id=:agent_id";
$whrDoc = array(":agent_id" => $agent_res['id']);
$resDoc = $pdo->selectOne($selDoc, $whrDoc);

$selDirect = "SELECT account_type,bank_name,routing_number,account_number FROM direct_deposit_account WHERE customer_id=:agent_id";
$whrDirect = array(":agent_id" => $agent_res['id']);
$resDirect = $pdo->selectOne($selDirect, $whrDoc);


if ($agent_res) {
	$AgentType = 'Agent';
	$fname = $agent_res['fname'];
	$lname = $agent_res['lname'];
	$email = $agent_res['email'];
	$phone = $agent_res['cell_phone'];
	$rep_id = $agent_res['rep_id'];
	$hdn_cust_id = $agent_res['id'];
	$sponsor_id = $agent_res['sponsor_id'];
	$sponsor_detail = $pdo->selectOne("SELECT business_name,fname,lname,cell_phone,email from customer WHERE id=:id", array(":id" => $sponsor_id));
	$business_name = (!empty($agent_res['business_name'])?$agent_res['business_name']:($agent_res['fname'].' '.$agent_res['lname']));
	$_SESSION['AGENT_INFO']['type'] = $AgentType;
	$_SESSION['AGENT_INFO']['rep_id'] = $rep_id;
	$_SESSION['AGENT_INFO']['fname'] = $fname;
	$_SESSION['AGENT_INFO']['lname'] = $lname;
	$_SESSION['AGENT_INFO']['email'] = $email;
	$_SESSION['AGENT_INFO']['cell_phone'] = $agent_res['cell_phone'];
	$_SESSION['AGENT_INFO']['hdn_cust_id'] = $hdn_cust_id;
	$_SESSION['AGENT_INFO']['sponsor_id'] = $sponsor_id;
} else {
	redirect($AGENT_HOST.'/index.php');
}
$rejection_text_new = $rejection_text = checkIsset($agent_res["reject_text"]);

$final_approval_text = "display:none";
if (in_array($agent_res["status"], array("Pending Approval", "Pending Documentation"))) {
	$firstTabOpen = "in active";
	$secondTabComplete = "disabled";
}
if (in_array($agent_res["status"], array("Pending Approval", "Pending Documentation")) && $agent_res["user_name"] != "") {
	$secondTabOpen = "in active";
	$firstTabOpen = "";
	$secondTabComplete = "";
}
if (in_array($agent_res["status"], array("Pending Approval")) && !in_array($agent_res["is_contract_approved"], array("Pending Resubmission"))) {
	$firstTabOpen = $secondTabOpen = "";
	$firstTabComplete = $secondTabComplete = "disabled";
	$final_approval_text = "";
}
$lastTabDisabled = "disabled";
$forceFullyAllow = false;
$checkThirdStp = '';
if (in_array($agent_res["status"], array("Pending Contract"))) {
	$lastTabDisabled = "";
	$secondTabOpen = "";
	$thirdTabOpen = "in active";
	$checkThirdStp = 'Pending Contract';
	$final_approval_text = "display:none";
	$firstTabOpen = $secondTabOpen = "";
		$firstTabComplete = $secondTabComplete = "disabled";
		$is_contract_approved = $agent_res["is_contract_approved"];
		$forceFullyAllow = true;
		$thirdTabOpen = "in active";
} 
if (in_array($agent_res["status"], array("Pending Approval")) && !in_array($agent_res["is_contract_approved"], array("Pending Resubmission"))) {
	$lastTabDisabled = "";
	$final_approval_text = "";
	$thirdTabOpen = "in active";
	$checkThirdStp = 'Pending Approval';
}

$terms = $pdo->selectOne("SELECT * from terms WHERE status='Active' AND type='Agent'");

$day = date('d');
$month = date('F');
$year = date('Y');

$terms = str_replace(array(/*"[[products]]",*/"[[day]]","[[month]]","[[year]]"/*,"[[business_taxid]]","[[business_address]]"*/,"[[business_name]]"), array(/*$productTable,*/$day,$month,$year/*,$business_taxid,$business_address*/,$business_name), $terms["terms"]);


if(isset($agent_res['id'])){
	$smart_tags = get_user_smart_tags($agent_res['id'],'Agent');
	if($smart_tags){
	  foreach ($smart_tags as $key => $value) {
	    $terms = str_replace("[[" . $key . "]]", $value, $terms);
	  }
	}
}


$exStylesheets = array('
	thirdparty/signature_pad-master/example/css/signature-pad.css', 
	'thirdparty/colorbox/colorbox.css',
	'thirdparty/cropper/dist/cropper.css',
	'thirdparty/dropzone/css/basic.css');

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
	'thirdparty/signature_pad-master/example/js/signature_pad.js', 
	'thirdparty/moment/moment.js', 
	'thirdparty/MaskedPassword/password_validation.js', 
	'thirdparty/iPhonePassword/js/jQuery.dPassword.js', 
	'thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/cropper/dist/cropper.js',
	'thirdparty/dropzone/dropzone.min.js',
	'thirdparty/ajax_form/jquery.form.min.js',
	'thirdparty/jQuery-SSN-Field-Masking-master/js/jquery.maskedinput.min.js',
	'thirdparty/jQuery-SSN-Field-Masking-master/js/jquery.maskssn.js'
);

$template = "agent_contract_remaining_v1.inc.php";
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
