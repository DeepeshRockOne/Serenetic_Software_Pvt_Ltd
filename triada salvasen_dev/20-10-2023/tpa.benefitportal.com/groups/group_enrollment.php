<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/function.class.php';

$group_row = array();
if (isset($_SESSION["groups"]['id'])) {
	$group_id = $_SESSION["groups"]['id'];
	$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['groups']['timezone']);
	
	$group_sql = "SELECT c.business_name,c.address,c.address_2,c.city,c.state,c.zip,c.business_phone,c.business_email,c.fname,c.lname,c.cell_phone,c.email,c.public_name,c.public_email,c.public_phone,c.user_name,cs.display_in_member,cs.tpa_for_billing,cs.is_valid_address,cs.brand_icon,c.sponsor_id,c.updated_at
		FROM customer c
		LEFT JOIN customer_settings cs on(cs.customer_id=c.id) 
		WHERE c.id=:id AND (c.status in ('Pending Documentation'))";
	$group_where = array(':id' => $group_id);
	$group_row = $pdo->selectOne($group_sql, $group_where);

	$selADoc = "SELECT * FROM customer_group_settings WHERE customer_id=:customer_id";
	$whrADoc = array(":customer_id" => $group_id);
	$resADoc = $pdo->selectOne($selADoc, $whrADoc);



	$selProfile = "SELECT *,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number,AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full FROM customer_billing_profile WHERE customer_id=:customer_id";
	$whrProfile = array(":customer_id" => $group_id);
	$resProfile = $pdo->selectOne($selProfile, $whrProfile);
	
	if (!$group_row) {
		setNotifyError('Sorry! Group contract not found');
		redirect($GROUP_HOST . '/dashboard.php');
	}
}
if(!empty($group_row)){	
	$group_full_name = $group_row['fname'] . " " . $group_row['lname'];
	$is_valid_address = $group_row['is_valid_address'];
	$tpa_for_billing = $group_row['tpa_for_billing'];
	
	$sponsor_id = $group_row['sponsor_id'];

	$sqlSponsor = "SELECT fname,lname,cell_phone,email from customer where id=:sponsor_id";
	$resSponsor = $pdo->selectOne($sqlSponsor,array(":sponsor_id"=>$sponsor_id));

	$sponsor_full_name = $resSponsor['fname']." ".$resSponsor['lname'];

	$currentImage='';
	$contract_business_image=!empty($group_row["brand_icon"])?$group_row["brand_icon"]:"";

	$last_saved = $tz->getDate($group_row['updated_at']);

    if (file_exists($GROUPS_BRAND_ICON_DIR . $contract_business_image) && $contract_business_image != "") {
      $currentImage=$GROUPS_BRAND_ICON_WEB . $contract_business_image;
    }
    
	//********** step1 varible intialization code start **********************
		$display_in_member =  $group_row['display_in_member'];
		
		$group_name =  $group_row['business_name'];
		$business_address = $group_row['address'];
		$business_address_2 = $group_row['address_2'];
		$city = $group_row['city'];
		$state = $group_row['state'];
		$zipcode = $group_row['zip'];
		$business_phone = $group_row['business_phone'];
		$business_email = $group_row['business_email'];
		if(!empty($resADoc)){
			$no_of_employee = $resADoc['group_size'];
			$years_in_business = $resADoc['group_in_year'];
			$ein = $resADoc['ein'];
			$nature_of_business = $resADoc['business_nature'];
			$sic_code = $resADoc['sic_code'];
		}
		


		$sicCodeSql = 'SELECT * FROM `group_sic_code` WHERE business_id = :id';
		$where = array(':id' => makeSafe($nature_of_business));
		$sicCodeRes = $pdo->select($sicCodeSql,$where);

		$fname = $group_row['fname'];
		$lname = $group_row['lname'];
		$email = $group_row['email'];
		$phone = $group_row['cell_phone'];
		$username = $group_row['user_name'];
		

		$public_name = $group_row['public_name'];
		$public_phone = $group_row['public_phone'];
		$public_email = $group_row['public_email'];
		$found_state_id = 0;
	//********** step1 varible intialization code start **********************

	//********** step2 varible intialization code start **********************
		if(!empty($resADoc)){
			$automated_communication = !empty($resADoc['automated_communication']) ? explode(",",$resADoc['automated_communication']) : array();
			$group_company = $resADoc['employer_company_common_owner'];
			$billing_broken = $resADoc['invoice_broken_locations'];
			
			$billing_type = $resADoc['billing_type'];
		}
		if(!empty($resProfile)){
			$is_valid_billing_address = $resProfile['is_valid_address'];
			$payment_type = $resProfile['payment_mode'];
		
			$account_type = $resProfile['ach_account_type'];
			$bankname = $resProfile['bankname'];

			$bank_rounting_number = $resProfile['ach_routing_number'];
			$bank_account_number = $resProfile['ach_account_number'];
			$bank_number_confirm = $bank_account_number;

			$card_type = $resProfile['card_type'];
			$card_number = $resProfile['card_no_full'];
			$expiry_month = $resProfile['expiry_month'];
			$expiry_year = $resProfile['expiry_year'];

			$name_on_card = $resProfile['fname'];
	        $cvv_no = $resProfile['cvv_no'];
	        $bill_address = $resProfile['address'];
	        $bill_address_2 = $resProfile['address2'];
	        $bill_city = $resProfile['city'];
	        $bill_state = $resProfile['state'];
	        $bill_zip = $resProfile['zip'];
		}
		
        

	//********** step2 varible intialization code start **********************
	
	//********** step3 varible intialization code start **********************
		$res_t =$pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Group',":status"=>'Active')); 

		if(!empty($res_t)){
			$group_terms = $res_t['terms'];
			$group_terms_id = $res_t['id'];
		}
	//********** step3 varible intialization code start **********************
} else {
	redirect($GROUP_HOST . '/dashboard.php');
}


// Nature of business code start
$sel_Business = "SELECT * FROM group_nature_business WHERE id > 0 AND is_deleted='N'";
$res_Business = $pdo->select($sel_Business);

// Nature of business code ends

//Group Pay Options
$payOptions = functionsList::getGroupPayOptions($_SESSION["groups"]['id']);
$is_cc_available = !empty($payOptions) && $payOptions['is_cc'] == 'Y' ? true : false;
$is_ach_available = !empty($payOptions) && $payOptions['is_ach'] == 'Y' ? true : false;
$is_check_available = !empty($payOptions) && $payOptions['is_check'] == 'Y' ? true : false;
$available_payment = array();
if($is_cc_available){
	array_push($available_payment,'CC');
}
if($is_ach_available){
	array_push($available_payment,'ACH');
}
if($is_check_available){
	array_push($available_payment,'Check');
}
//Group Pay Options
$exStylesheets = array('thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css',
	'thirdparty/multiple-select-master/multiple-select.css',
	'thirdparty/jquery_ui/custome_theme/css/jquery-ui-1.9.2.custom.css',
	'thirdparty/colorbox/colorbox.css',
	'thirdparty/signature_pad-master/example/css/signature-pad.css',
	'thirdparty/dropzone/css/basic.css'
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
	'thirdparty/ajax_form/jquery.form.js',
	'thirdparty/jquery_autotab/jquery.autotab-1.1b.js',
	'thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.min.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
	'thirdparty/colorbox/jquery.colorbox.js',
	'thirdparty/signature_pad-master/example/js/signature_pad.js',
	'thirdparty/bower_components/moment/moment.js',
	'js/scrollfix.js',
	'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,
	'thirdparty/dropzone/dropzone.min.js',
	'thirdparty/MaskedPassword/password_validation.js', 
	'thirdparty/iPhonePassword/js/jQuery.dPassword.js'
);

$template = 'group_enrollment.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
