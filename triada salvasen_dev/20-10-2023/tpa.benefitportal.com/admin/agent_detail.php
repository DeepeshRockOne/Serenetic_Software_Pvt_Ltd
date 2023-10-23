<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
//error_reporting(E_ALL);
has_access(5);
redirect('agent_detail_v1.php');
/* notification code start */
if (isset($_REQUEST["noti_id"])) {
	openAdminNotification($_REQUEST["noti_id"],$_REQUEST['id']);
}
 
/* notification code end */
$tmp_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$referer = basename($tmp_referer);
$query = parse_url($referer,PHP_URL_QUERY);
parse_str($query, $params);
$test = isset($params['activeTab']) ? $params['activeTab'] : '';

$activeTab = ($test == "" ? "profile" : $test);

if (parse_url($referer, PHP_URL_PATH) != "adminSafecode.php") {
	unset($_SESSION['admin']['safe_code']);
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$agent_id = $id;
$customer_id = $id;
$note_id = isset($_GET['note_id']) ? $_GET['note_id'] : '';

$select_note = "SELECT * FROM note WHERE id = :id";
$note_res = $pdo->selectOne($select_note, array('id' => $note_id));

$select_user = "SELECT c.*,c.email as cust_email,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as password,s.fname as s_fname,s.lname as s_lname,s.rep_id as s_rep_id
                FROM `customer` c
                LEFT JOIN customer s on(s.id = c.sponsor_id)
                WHERE c.id= :id AND c.type IN ('Agent')";
$where = array(':id' => makeSafe($id));
$row = $pdo->selectOne($select_user, $where);

$selADoc = "SELECT * FROM agent_document WHERE agent_id=:agent_id";
$whrADoc = array(":agent_id" => $customer_id);
$resADoc = $pdo->selectOne($selADoc, $whrADoc);

if (!$row) {
	setNotifyError('Customer does not exist');
	redirect("agent_listing.php");
}
$type = $row['type'];

$parent_page = "agent_listing.php";
$parent_title = "Agents";

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = $parent_title;
$breadcrumbes[1]['link'] = $parent_page;
$breadcrumbes[2]['title'] = stripslashes($row['fname'] . " " . $row['lname']);

$validate = new Validation();

//For Acoount Address
$country_id = "231";

$country_qur = "SELECT * FROM `country` WHERE country_id IN('" . $country_id . "') ORDER BY country_id ";
$countries_temp = $pdo->select($country_qur);

foreach ($countries_temp AS $key => $data) {
	$countries[$data['country_id']] = $data;
	$countries_name[$data['country_id']] = $data;
}

if ($row['country_id'] > 0) {
	$acc_add_state = "SELECT * FROM `states_c` WHERE country_id= :country_id";
	$swhere_state = array(':country_id' => makeSafe($row['country_id']));
	$add_temp_state = $pdo->select($acc_add_state, $swhere_state);

	foreach ($add_temp_state AS $add_key => $add_value) {
		$add_states[$add_value['id']] = $add_value;
	}
}

$selSql = "SELECT *, AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as definedPassword FROM `customer` WHERE id= :id";
$params = array(":id" => makeSafe($id));
$custRow = $pdo->selectOne($selSql, $params);

$password = $custRow['definedPassword'];

$strQuery = "SELECT ad.*,c.*
          FROM customer ad
          JOIN country c ON(ad.country_id = c.country_id) WHERE id= :id";
$sch_params = array(':id' => makeSafe($id));
$rs = $pdo->selectOne($strQuery, $sch_params);
$to = isset($to) ? $to : '';
$to = "+" . $to;
$internal_id = $row['id'];
$display_id = $row['display_id'];
$rep_id = $row['rep_id'];
$user_type = $row['type'];
$sponsor_name = getname("customer", $row['sponsor_id'], "CONCAT(fname, ' ', lname)", "id");
$email = stripslashes($row['cust_email']);
$fname = stripslashes($row['fname']);
$lname = stripslashes($row['lname']);
$city = stripslashes($row['city']);
$zip_code = stripslashes($row['zip']);
$web_alias = stripslashes($row['user_name']);
$b_name = stripslashes($row['business_name']);
$company_address_2 = stripslashes($row['company_address_2']);
$company_address = stripslashes($row['company_address']);
$company_city = stripslashes($row['company_city']);
$company_state = stripslashes($row['company_state']);
$company_zip = stripslashes($row['company_zip']);
$business_taxid = $row['fid'];
$agent_coded_level = $row['agent_coded_level'];

$state = $row['state'];
$address_siteapt = $row['address_2'];
$dl_num = $row['dl_number'];
$dl_state = $row['dl_state'];

$eo_amt = $resADoc['e_o_amount'];
$eo_exp = $resADoc['e_o_expiration'];
$eo_doc = $resADoc['e_o_document'];
$eo_doc_web = $AGENT_DOC_WEB . $eo_doc;

if (!empty($row['birth_date']) && $row['birth_date'] != '00-00-0000') {
	$bdate = date('m-d-Y', strtotime(stripslashes($row['birth_date'])));
}

$password = $row['password'];
$phone = $row['cell_phone'];
$mophone1 = substr($phone, 0, 3);
$mophone2 = substr($phone, 3, 3);
$mophone3 = substr($phone, 6, 4);
$mobile_num = format_telephone($phone);
$bphone = stripslashes($row['business_phone']);
$bophone1 = substr($bphone, 0, 3);
$bophone2 = substr($bphone, 3, 3);
$bophone3 = substr($bphone, 6, 4);
$ssn = stripslashes($row['ssn']);
$rank = stripslashes($row['rank']);
$address = $row['address'];
$status = $row['status'];
$r_country_id = $row['country_id'];
$company_id = $row['company_id'];
$created_at = $row['created_at'];
$setup_type = $row['setup_type'];
$parent = $row['s_rep_id'] . '-' . $row['s_fname'] . ' ' . $row['s_lname'];
$fid = $row['fid'];
$npn = $row['npn'];
$fax = $row['fax'];
$product_set = $row['product_set'];
$payment_grouping = $row['payment_grouping'];
$eo_coverage = $row['eo_coverage'];
$flag = $row['flag'];
$tracking_status = $row['tracking_status'];
$tracking_stage = $row['tracking_stage'];
$contract = $row['contract'];
$website = $row['website'];
$domain_name = $row['domain_name'];
$secure_domain = $row['secure_domain'];
$allow_marketing = explode(',', $row['allow_marketing']);
$is_social_link = $row['is_social_link'];
$public_name = $row["public_name"];
$public_phone = $row["public_phone"];
$public_email = $row["public_email"];
$is_social_link = $row["is_social_link"];
$facebook_page = $row['fb_link'];
$linkedin_page = $row['linkedin_link'];
$twitter_page = $row['twitter_link'];
$g_link = isset($row['g_link']) ? $row['g_link'] : '';
$c_acc_type = $row['account_type'];

$round_robin_res = $pdo->selectOne("SELECT * FROM lead_round_robin WHERE agent_id=:agent_id AND is_deleted='N' ", array(":agent_id" => $id));
$is_round_robin = isset($round_robin_res['id']) ? $round_robin_res['id'] : '';

$accSql = "select * from direct_deposit_account where customer_id=:cust_id";
$params = array(':cust_id' => makeSafe($id));
$accRow = $pdo->selectOne($accSql, $params);

$licenseDetailsResult = $pdo->select("SELECT * from agent_license where license_num!='' and agent_id=:agent_id and is_deleted='N' order by selling_licensed_state", array(":agent_id" => $id));
$LicensedStates = array();
if(!empty($licenseDetailsResult)){
	foreach ($licenseDetailsResult as $key => $license_row) {
		$LicensedStates[$license_row['id']] = $license_row['selling_licensed_state'];
	}
}
$r_country_id = 231;
$nonlicenseDetailsResult = $pdo->select("SELECT * FROM `states_c` WHERE country_id in($r_country_id) and name not in(SELECT new_selling_licensed_state FROM agent_license WHERE license_num='' AND agent_id=:agent_id AND is_deleted='N' UNION 
	SELECT new_selling_licensed_state FROM agent_license WHERE new_request='Y' AND agent_id=:agent_id AND is_deleted='N' UNION 
	SELECT selling_licensed_state FROM agent_license WHERE license_num!='' AND agent_id=:agent_id AND is_deleted='N')", array(":agent_id" => $id));
$nonLicenseStateArray=array();
if(!empty($nonlicenseDetailsResult)){
	foreach ($nonlicenseDetailsResult as $license_row) {
		$nonLicenseStateArray[$license_row['id']] = $license_row['name'];
	}
}

$nonafslicLicense=$pdo->select("SELECT * FROM writing_number WHERE agent_id=:agent_id AND is_deleted='N'",array(":agent_id" => $id));
$nonafslicLicenseStateArray=array();
if(!empty($nonafslicLicense)){
	foreach ($nonafslicLicense as $license_row) {
		$nonafslicLicenseStateArray[$license_row['id']] = explode(',',$license_row['license_state']);
	}
}
/* echo "<pre>";
print_r($accRow);
echo "</pre>";
exit('here'); */

if ($accRow) {
	$d_account_name = $accRow['account_name'];
	$d_bank_name = $accRow['bank_name'];
	$d_bank_branch = $accRow['bank_branch'];
	$d_bank_address = $accRow['bank_address'];
	$d_bic_code = $accRow['bic_code'];
	$d_v_account_number = $accRow['account_number'];
	$d_account_number = $accRow['account_number'];
	$d_account_number_show = $accRow['account_number'];
	$d_country = $accRow['country'];
	$d_state = $accRow['state'];
	$d_city = $accRow['city'];
	$d_zip_code = $accRow['zip'];
	$d_account_type = $accRow['account_type'];
	$d_routing_number = $accRow['routing_number'];
	$d_v_routing_number = $accRow['routing_number'];
	$d_ins_number = $accRow['institution_number'];

}

/*if ($r_country_id == 230) {
$query_country = 'SELECT * FROM `country` WHERE country_id = 230';
} elseif ($r_country_id == 40) {
$query_country = 'SELECT * FROM `country` WHERE country_id = 38';
} else {
$query_country = 'SELECT * FROM `country`';
}*/



$query_country = "SELECT * FROM `country` WHERE country_id = '" . $r_country_id . "' ";

$d_countries = array();
$temp = $pdo->select($query_country);
foreach ($temp AS $key => $value) {
	$d_countries[$value['country_id']] = $value;
}

if ($r_country_id != '') {
	$select_state = "SELECT * FROM `states_c` WHERE country_id in($r_country_id) order by name ASC";
	$temp_state = $pdo->select($select_state);
	foreach ($temp_state AS $key => $value) {
		$d_states[$value['name']] = $value;
	}
}

//For Account Address
$acc_fname = stripslashes($row['fname']);
$acc_lname = stripslashes($row['lname']);
$acc_country_id = $row['country_id'];
$acc_country = $row['country_name'];
$acc_address1 = isset($row['address']) ? $row['address'] : '';
$acc_address2 = isset($row['address2']) ? $row['address2'] : '';
$acc_city = $row['city'];
$acc_state = $row['state'];
$acc_zip = $row['zip'];

if ($acc_country_id > 0) {
	$acc_add_state = "SELECT * FROM `states_c` WHERE country_id= :country_id";
	$swhere_state = array(':country_id' => makeSafe($acc_country_id));
	$add_temp_state = $pdo->select($acc_add_state, $swhere_state);
	foreach ($add_temp_state AS $add_key => $add_value) {
		$add_states[$add_value['id']] = $add_value;
	}
}

$billing_query = "SELECT a.*,b.country_id, AES_DECRYPT(a.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn FROM `customer_billing_profile` as a LEFT JOIN country as b ON  a.country_id=b.country_id WHERE a.customer_id = :customer_id AND a.is_default='Y' and is_deleted='N'";
$billing_where = array(':customer_id' => makeSafe($id));
$bd = $pdo->selectOne($billing_query, $billing_where);
// echo $bd['id']; //exit;

$b_states = array();
$billing_status = 0;
// echo $bcountry_id = $bd['country_id']; exit;
//echo 'aa'.$bd['customer_id'];
if (isset($bd['customer_id'])) {
//echo "isset";
	$billing_status = 1;
	$bcountry_id = $bd['country_id'];
	if ($bcountry_id > 0) {
		$bill_state = "SELECT * FROM `states_c` WHERE country_id= :country_id";
		$bwhere_state = array(':country_id' => makeSafe($bcountry_id));
		$btemp_state = $pdo->select($bill_state, $bwhere_state);
		foreach ($btemp_state AS $bkey => $bvalue) {
			$b_states[$bvalue['id']] = $bvalue;
		}
	}
}

//$b_creditcard = MaskCreditCard($b_creditcard);

$shipping_query = "SELECT a.*,b.country_id FROM `customer_shipping_profile` as a LEFT JOIN country as b ON  a.country=b.country_id WHERE a.customer_id = :customer_id";
$shipping_where = array(':customer_id' => makeSafe($id));
$shipping_details = $pdo->selectOne($shipping_query, $shipping_where);

$s_states = array();
$shipping_status = FALSE;
if (isset($shipping_details['customer_id'])) {
	$shipping_status = TRUE;
	$scountry_id = $shipping_details['country_id'];
	if ($scountry_id > 0) {
		$ship_state = "SELECT * FROM `states_c` WHERE country_id= :country_id";
		$swhere_state = array(':country_id' => makeSafe($scountry_id));
		$stemp_state = $pdo->select($ship_state, $swhere_state);
		foreach ($stemp_state AS $skey => $svalue) {
			$s_states[$svalue['id']] = $svalue;
		}
	}

	$s_fname = $shipping_details['fname'];
	$s_lname = $shipping_details['lname'];
	$s_countryid = $shipping_details['country_id'];
	$s_state = $shipping_details['state'];
	$s_city = $shipping_details['city'];
	$s_zip = $shipping_details['zip'];
	$s_address1 = $shipping_details['add1'];
	$s_address2 = $shipping_details['add2'];
}

// Alert Display code
$incr = isset($incr) ? $incr : '';
$alert_query = "SELECT * FROM note WHERE user_type='{$type}' AND customer_id = :customer_id AND type ='Alert' $incr ORDER BY created_at desc";
$alert_params = array(
	':customer_id' => makeSafe($id),
);
$alert_Sql = $pdo->select($alert_query, $alert_params);
// End Alert Code
$errors = $validate->getErrors();

$member_id = $_GET['id'];
$o_fname = getname('customer', $member_id, 'fname', 'id');
$o_lname = getname('customer', $member_id, 'lname', 'id');
$o_email = getname('customer', $member_id, 'email', 'id');
$states = $pdo->select("SELECT * FROM states_c WHERE country_id=231 order by name ASC");

if(in_array('admin',$_SESSION))
{
    activity_feed($company_id,  $_SESSION['admin']['id'],'admin',$row['id'], $row['type'], 'Viewed Member Account', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], 'Viewed Member Account');
}


$exStylesheets = array('thirdparty/colorbox/colorbox.css', 'thirdparty/dropzone/css/basic.css', 'thirdparty/switchery/dist/switchery.min.css', 'css/sweatalert/css/sweatalert.css', 'thirdparty/cropper/dist/cropper.css', 'thirdparty/multiple-select-master/multiple-select.css');
$exJs = array(
	'agents/js/jquery.peity.min.js',
	'agents/js/jquery.peity.init.js',
	'thirdparty/jquery.bxslider/jquery.bxslider.js',
	'thirdparty/clipboard/clipboard.min.js',
	'thirdparty/simscroll/jquery.slimscroll.min.js',
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
	'thirdparty/jquery_autotab/jquery.autotab-1.1b.js',
	'thirdparty/ajax_form/jquery.form.js',
	'thirdparty/Birthdate/moment.min.js',
	//'thirdparty/colorbox/jquery.colorbox.js',
	'thirdparty/dropzone/dropzone.min.js',
	'thirdparty/iPhonePassword/js/jQuery.dPassword.js',
	'thirdparty/MaskedPassword/password_validation.js',
	'thirdparty/switchery/dist/switchery.min.js',
	'thirdparty/cropper/dist/cropper.js',
	'thirdparty/multiple-select-master/jquery.multiple.select.js',
	'thirdparty/jquery-sparkline/jquery.sparkline.min.js',
	'thirdparty/price_format/jquery.price_format.2.0.js',
);
$page_title = "Agent's Details";
$template = 'agent_detail.inc.php';
include_once 'layout/end.inc.php';
?>