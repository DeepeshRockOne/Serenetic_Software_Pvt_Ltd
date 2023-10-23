<?php
/**
 * redirect to given url.
 *
 * @param string $url
 */
function redirect($url, $is_parent = false) {
	if ($is_parent) {
		echo "<script>window.parent.location='" . $url . "';</script>";
		exit;
	} else {
		if ($url == "CLOSE_COLORBOX") {
			echo "<script>window.parent.$.colorbox.close();</script>";
		} else {
			echo "<script>window.location='" . $url . "';</script>";
			exit;
		}
	}
}

/**
 * check file exists
 *
 * @global string $FILE_UPLOAD_HOST
 * @param string $file_path
 * @return boolean
 */

function remote_file_exists($file_path, $company_id = '3') {
	global $SITE_SETTINGS;

	$GLOBAL_DIR_HOST = $SITE_SETTINGS[$company_id]['HOST'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $GLOBAL_DIR_HOST . "/remote_scripts/check_file_exists.php");
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "file_path=" . urlencode($file_path));
	$data = curl_exec($ch);

	curl_close($ch);
	return (bool) $data;
}

/**
 * Upload file in server
 *
 * @global string $FILE_UPLOAD_HOST
 * @param string $file
 * @param string $path
 * @return boolean
 */
function remote_file_upload($path, $tmp_name, $new_image, $old_image = '') {
	global $FILE_UPLOAD_HOST;

	$handle = fopen($tmp_name, "r");
	$data = fread($handle, filesize($tmp_name));

	$params = 'path=' . urlencode($path) . '&tmp_name=' . base64_encode($data) . '&new_image=' . urlencode($new_image) . '&old_image_name=' . urlencode($old_image);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $FILE_UPLOAD_HOST . "/remote_scripts/upload.php");
	// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, "path=" . urlencode($path));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function remote_base64_file_upload($path, $tmp_name, $new_image, $old_image = '') {
	global $FILE_UPLOAD_HOST;

	$params = 'path=' . urlencode($path) . '&tmp_name=' . $tmp_name . '&new_image=' . urlencode($new_image) . '&old_image_name=' . urlencode($old_image);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $MSTAG_HOST . "/remote_scripts/upload_file_profile.php");
	// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, "path=" . urlencode($path));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function remote_base64_file_upload_new($path, $tmp_name, $new_image, $old_image = '', $company_id = 3) {
	global $FILE_UPLOAD_HOST, $BRIGHT_FILE_UPLOAD_HOST, $CYBERX_FILE_UPLOAD_HOST, $SENIOR_FILE_UPLOAD_HOST, $HOORAY_FILE_UPLOAD_HOST, $AGENTRA_FILE_UPLOAD_HOST, $MYHEALTH_FILE_UPLOAD_HOST;

	$params = 'path=' . urlencode($path) . '&tmp_name=' . $tmp_name . '&new_image=' . urlencode($new_image) . '&old_image_name=' . urlencode($old_image);

	$ch = curl_init();

	if ($company_id == 1) {
		curl_setopt($ch, CURLOPT_URL, $BRIGHT_FILE_UPLOAD_HOST . "/remote_scripts/upload_file_profile.php");
	} else if ($company_id == 2) {
		curl_setopt($ch, CURLOPT_URL, $MYHEALTH_FILE_UPLOAD_HOST . "/remote_scripts/upload_file_profile.php");
	} else if ($company_id == 3) {
		curl_setopt($ch, CURLOPT_URL, $CYBERX_FILE_UPLOAD_HOST . "/remote_scripts/upload_file_profile.php");
	} else if ($company_id == 4) {
		curl_setopt($ch, CURLOPT_URL, $SENIOR_FILE_UPLOAD_HOST . "/remote_scripts/upload_file_profile.php");
	} else if ($company_id == 5) {
		curl_setopt($ch, CURLOPT_URL, $HOORAY_FILE_UPLOAD_HOST . "/remote_scripts/upload_file_profile.php");
	} else if ($company_id == 6) {
		curl_setopt($ch, CURLOPT_URL, $AGENTRA_FILE_UPLOAD_HOST . "/remote_scripts/upload_file_profile.php");
	}

	// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, "path=" . urlencode($path));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$data = curl_exec($ch);
	//var_dump($data);
	curl_close($ch);
/*
echo "<pre>";
print_R($data);
echo "</pre>";
exit;*/
	return $data;

}

function saveImage($file_content, $folderName, $fileName, $remove_file_name = "") {
	$path = $folderName . $fileName;

	if (!is_dir($folderName)) {
		mkdir($folderName);
	}
	$img = str_replace('data:image/png;base64,', '', $file_content);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);

	$success = file_put_contents($path, $data);
	if ($success) {
		$res = "Success";
		if ($remove_file_name != '' && file_exists($folderName . $remove_file_name)) {
			unlink($folderName . $remove_file_name);
		}
	} else {
		$res = 'error';
	}
}

function upload_base64_image($tmp_name, $folder_name, $image_name) {
	$img = str_replace(array('data:image/png;base64,', 'data:image/jpeg;base64,'), array('', ''), $tmp_name);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$file = $folder_name . "/" . $image_name;
	$success = file_put_contents($file, $data);
}

function redirect404() {
	redirect('404.php');
}

function url_for($page) {
	//return '../'.$page;
	return $page;
}

function check_admin_login($check = false) {
	if ($check) {
		if (isset($_SESSION['admin_login'])) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * get specified field value by using table,field name and compare value
 *
 * @global PdoOpt $pdo
 * @param string $table
 * @param string $id
 * @param string $name
 * @param string $compvar
 * @return string
 */
function getname($table, $id, $name, $compvar = "id") {
	global $pdo;
	$getsql = "SELECT $name from $table where $compvar=:id";
	$params = array(
		':id' => $id,
	);
	$row = $pdo->selectOne($getsql, $params);
	return $row ? $row[$name] : '';
}
function getrepid($table, $id, $name, $compvar = "id") {
	global $pdo;
	$getsql = "SELECT $name from $table where $compvar=:id";
	$params = array(
		':id' => $id,
	);
	$row = $pdo->selectOne($getsql, $params);
	return $row ? $row[$name] : '';
}

function toTimestamp($milliseconds) {
	$seconds = $milliseconds / 1000;
	$remainder = round($seconds - ($seconds >> 0), 3) * 1000;

	return date('Y:m:d H:i:s.', $seconds) . $remainder;
}

/**
 * Create session.
 *
 * @param array $data
 */
function login($data) {
	//print_r($data);exit;

	$_SESSION['admin']['id'] = $data->id;
	$_SESSION['admin']['display_id'] = $data->display_id;
	$_SESSION['admin']['name'] = $data->fname . " " . $data->lname;
	$_SESSION['admin']['type'] = $data->type;
	$_SESSION['admin']['email'] = $data->email;
	$_SESSION['admin']['chat_password'] = $data->chat_password;
//    $_SESSION['admin']['webimstatus'] = 'on';
	$_SESSION['admin']['access'] = (array) explode(",", $data->feature_access);
}

function retrieveDate($date, $split_date = false) {
	global $FULL_DATE_FORMAT, $DATE_FORMAT, $TIME_FORMAT;
	if ($split_date) {
		return date($DATE_FORMAT, strtotime($date)) . "<br/>" . date($TIME_FORMAT, strtotime($date));
	} else {
		return date($FULL_DATE_FORMAT, strtotime($date));
	}
}

function storeDate($date) {
	return date('Y-m-d h:i:s', strtotime($date));
}

function get_display_id($table = 'admin') {
	global $pdo;
	$cust_id = rand(100000, 999999);
	if ($table == 'customer') {
		$sql = "SELECT count(display_id) as total FROM customer WHERE display_id =$cust_id";
	} else {
		$sql = "SELECT count(display_id) as total FROM admin WHERE display_id =$cust_id";
	}
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_display_id($table);
	} else {
		return $cust_id;
	}
}

function get_campaign_display_id() {
	global $pdo;
	$cust_id = rand(100000, 999999);
	$sql = "SELECT count(display_id) as total FROM auto_dialer_campaign WHERE display_id =$cust_id";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_campaign_display_id();
	} else {
		return $cust_id;
	}
}

function get_call_center_display_id() {
	global $pdo;
	$cust_id = rand(100000, 999999);
	$sql = "SELECT count(display_id) as total FROM customer WHERE display_id =$cust_id";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_call_center_display_id();
	} else {
		return $cust_id;
	}
}
function get_provider_display_id() {
	global $pdo;
	$cust_id = rand(100000, 999999);
	$sql = "SELECT count(display_id) as total FROM providers WHERE display_id =$cust_id";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_provider_display_id();
	} else {
		return $cust_id;
	}
}

function get_call_center_id() {
	global $pdo;
	$cust_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='K" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_call_center_id();
	} else {
		return "K" . $cust_id;
	}
}

function get_admin_id() {
	global $pdo;
	$cust_id = rand(10000, 99999);
	$sql = "SELECT display_id FROM admin WHERE display_id = 'A" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res) {
		return get_admin_id();
	} else {
		return "A" . $cust_id;
	}
}

function get_partner_id() {
	global $pdo;
	$cust_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='P" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_partner_id();
	} else {
		return "P" . $cust_id;
	}
}

function get_organization_id() {
	global $pdo;
	$cust_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='O" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_organization_id();
	} else {
		return "G" . $cust_id;
	}
}

function get_agent_id() {
	global $pdo;
	$cust_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='A" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_agent_id();
	} else {
		return "A" . $cust_id;
	}
}

function get_fronter_id() {
	global $pdo;
	$cust_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='F" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_fronter_id();
	} else {
		return "F" . $cust_id;
	}
}

function get_customer_id() {
	global $pdo;
	$cust_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='M" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_customer_id();
	} else {
		return "M" . $cust_id;
	}
}

function get_free_member_id() {
	global $pdo;
	$cust_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='F" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_free_member_id();
	} else {
		return "F" . $cust_id;
	}
}

function get_order_id() {
	global $pdo;
	$cust_id = rand(100000, 999999);
	$sql = "SELECT count(*) as total FROM orders WHERE display_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_order_id();
	} else {
		return $cust_id;
	}
}

function get_autoship_display_id() {
	global $pdo;
	$cust_id = rand(10000, 99999);
	$sql = "SELECT count(*) as total FROM autoship_profile WHERE display_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_autoship_display_id();
	} else {
		return $cust_id;
	}
}

function get_monthly_subscription_display_id() {
	global $pdo;
	$cust_id = rand(10000, 99999);
	$sql = "SELECT count(*) as total FROM monthly_subscriptions WHERE display_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_monthly_subscription_display_id();
	} else {
		return $cust_id;
	}
}

function hasActiveSubscription($rep_id) {
	global $pdo;
	$sql = "SELECT count(*) as total FROM monthly_subscriptions WHERE customer_id = $rep_id AND status = 'Active'";

	$row = $pdo->selectOne($sql);
	if ($row['total'] > 0) {
		return true;
	}
	return false;
}

function checkSubscription($customer_id, $product_id) {
	global $pdo;
	$sql = "SELECT count(*) as total FROM monthly_subscriptions WHERE customer_id = :customer_id AND product_id = :product_id AND status = 'Active'";
	$row = $pdo->selectOne($sql, array(':customer_id' => makeSafe($customer_id), ':product_id' => makeSafe($product_id)));
	if ($row['total'] > 0) {
		return true;
	}
	return false;
}

function checkAccessLevel($member, $section) {
	global $ACCESS_LEVEL;
	if ($member['type'] == "Partner Lite") {
		if (checkSubscription($member['id'], 4)) {
			if (array_key_exists($section, $ACCESS_LEVEL[$member['type']])) {
				return true;
			}
		}
		return false;
	} else if ($member['type'] == "Customer Life") {
		if (checkSubscription($member['id'], 5)) {
			if (array_key_exists($section, $ACCESS_LEVEL[$member['type']])) {
				return true;
			}
		}
		return false;
	} else if ($member['type'] == "Customer Vacation") {
		if (checkSubscription($member['id'], 6)) {
			if (array_key_exists($section, $ACCESS_LEVEL[$member['type']])) {
				return true;
			}
		}
		return false;
	} else if ($member['type'] == "Partner") {
		if (checkSubscription($member['id'], 2)) {
			return true;
		}
		return false;
	} else {
		return false;
	}
}

function getBrowser() {
	$browser = "";
	if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("MSIE"))) {
		$browser = "ie";
	} else if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("Presto"))) {
		$browser = "opera";
	} else if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("CHROME"))) {
		$browser = "chrome";
	} else if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("SAFARI"))) {
		$browser = "safari";
	} else if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("FIREFOX"))) {
		$browser = "firefox";
	} else {
		$browser = "other";
	}

	return $browser;
}

function startEndDate() {
	$params = array();
	$params['start_date'] = date('Y-m-d', time() + (1 - date('w')) * 24 * 3600);
	$params['end_date'] = date('Y-m-d', time() + (7 - date('w')) * 24 * 3600);
	return $params;
}

function dateDiff($from_date, $to_date, $format = 'day') {
	$dStart = new DateTime($from_date);
	$dEnd = new DateTime($to_date);
	$dDiff = $dStart->diff($dEnd);
	if ($format == 'day') {
		return $dDiff->days;
	} elseif ($format == 'month') {
		return $dDiff->format('%m') + 1;
	}
}

function getPointExpiry($date, $autoshipday) {
	$d1 = new DateTime($date);
	$day = $d1->format('d');
	if ($day <= 28 && $autoshipday <= 28) {
		$next_date = date("Y-m-d H:i:s", strtotime($date . " +1 month"));
	} else {
		$year = $d1->format('Y');
		$month = $d1->format('n');
		$hour = $d1->format('H');
		$minute = $d1->format('i');
		$second = $d1->format('s');
		if ($month == 12) {
			$year++;
		}

		$month = (++$month) % 12;
		if ($month == 0) {
			$month = 12;
		}

		$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		if ($autoshipday > $total_days) {
			$day = $total_days;
		} else {
			$day = $autoshipday;
		}

		$d2 = DateTime::createFromFormat('Y-m-d H:i:s', $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $second);
		$next_date = $d2->format('Y-m-d H:i:s');
	}

	$date = date('Y-m-d H:i:s', strtotime($date));
	$day_diff = dateDiff($date, $next_date, 'day');
	if ($day_diff < 30) {
		$add_day = 30 - $day_diff;
		$next_date = date('Y-m-d H:i:s', strtotime($next_date . ' + ' . $add_day . ' day'));
	}
	return $next_date;
}

function getOS($userAgent) {
	// Create list of operating systems with operating system name as array key
	$oses = array(
		'iPhone' => '(iPhone)',
		'Windows 3.11' => 'Win16',
		'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
		'Windows 98' => '(Windows 98)|(Win98)',
		'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
		'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
		'Windows 2003' => '(Windows NT 5.2)',
		'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
		'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
		'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
		'Windows ME' => 'Windows ME',
		'Open BSD' => 'OpenBSD',
		'Sun OS' => 'SunOS',
		'Linux' => '(Linux)|(X11)',
		'Safari' => '(Safari)',
		'Macintosh' => '(Mac_PowerPC)|(Macintosh)',
		'QNX' => 'QNX',
		'BeOS' => 'BeOS',
		'OS/2' => 'OS/2',
		'Blazer' => 'Blazer',
		'Palm' => 'Palm',
		'Handspring' => 'Handspring',
		'Nokia' => 'Nokia',
		'Kyocera' => 'Kyocera',
		'Samsung' => 'Samsung',
		'Motorola' => 'Motorola',
		'Smartphone' => 'Smartphone',
		'Windows CE' => 'Windows CE',
		'Blackberry' => 'Blackberry',
		'WAP' => 'WAP',
		'SonyEricsson' => 'SonyEricsson',
		'PlayStation Portable' => 'PlayStation Portable',
		'LG' => 'LG',
		'MMP' => 'MMP',
		'OPWV' => 'OPWV',
		'Symbian' => 'Symbian',
		'EPOC' => 'EPOC',
		'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
	);

	foreach ($oses as $os => $pattern) {
		// Loop through $oses array
		// Use regular expressions to check operating system type
		if (preg_match("#" . $pattern . "#", $userAgent)) { // Check if a value in $oses array matches current user agent.
			return $os; // Operating system was matched so return $oses key
		}
	}
	return 'Unknown'; // Cannot find operating system so return Unknown
}


function allow_mail($email) {
	$sendSql = "SELECT email FROM sendgrid_bademails where email='$email'";
	$sendRs = $pdo->selectOne($sendSql);
	if ($sendRs) {
		return "N";
	} else {
		$custSql = "SELECT email,allow_smtp FROM customer where email='$email'";
		$custRs = $pdo->selectOne($custSql);
		if ($custRs) {
			$Aarr = $custRs;
			$allow_smtp = stripslashes($Aarr["allow_smtp"]);
			return $allow_smtp;
		} else {
			return "Y";
		}
		//return "Y";
	}
}

/**
 *
 * @return type string
 * @author Ashwin Pitroda <ashwin@cyberxllc.com>
 */
function generate_event_id() {
	global $pdo;
	$event_id = rand(10000, 99999);
	$event_id = 'M-' . $event_id;
	$sql = "SELECT count(*) total FROM event WHERE event_display_id='" . $event_id . "'";
	$sel_res = $pdo->selectOne($sql);

	if ($sel_res['total'] > 0) {
		return generate_event_id();
	} else {
		return $event_id;
	}
}

function generate_event_register_id() {
	global $pdo;

	$event_id = 'ER-' . rand(100000, 999999);
	$sql = "SELECT id FROM event_register WHERE register_id = '" . $event_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res) {
		return generate_event_register_id();
	} else {
		return $event_id;
	}
}

/**
 * Generate tracking id for ticket.
 *
 */
function generate_tracking_id() {
	global $pdo;
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	$randomString = '';
	for ($i = 0; $i < 10; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	$ntrack_id = "TKT-" . substr($randomString, 3, 3) . "-" . substr($randomString, 6, 4);
	$sql = "SELECT count(*) total FROM s_ticket WHERE tracking_id = '$ntrack_id'";
	$sel_res = $pdo->selectOne($sql);
	if ($sel_res['total'] > 0) {
		return generate_tracking_id();
	} else {
		return $ntrack_id;
	}
}

/**
 * Store mysql error in txt file.
 *
 * @param string $page
 * @param string $query
 *
 */
function mysqlErrLog($query) {
	$file = $_SERVER['DOCUMENT_ROOT'] . "/mysql_error.txt";
	$content = date('Y-m-d H:i:s') . "\t " . $_SERVER['SCRIPT_NAME'] . "\t " . $query . "\n";
	$fp = fopen($file, 'a+');
	fwrite($fp, $content);
	fclose($fp);
}

function makeSafe($string) {
	if (!is_array($string)) {
		$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
	return $string;
}

function curPageName() {
	return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
}

/**
 * Replaces all but the last for digits with x's in the given credit card number
 * @param int|string $cc The credit card number to mask
 * @param string $mask The chracter you want to place instead to number or string e.g x, * etc...
 * @return string The masked credit card number
 */
function MaskCreditCard($cc, $mask = 'X') {
	// Get the cc Length
	$cc_length = strlen($cc);
	// Replace all characters of credit card except the last four and dashes
	for ($i = 0; $i < $cc_length - 4; $i++) {
		if ($cc[$i] == '-') {
			continue;
		}
		$cc[$i] = $mask;
	}
	// Return the masked Credit Card #
	return $cc;
}

/**
 * It used to check whether mail is unsubscribe or not in unsubscribe table.
 *
 * @param type $email email address
 * @param type $user_type user main type
 * @return boolean
 * @author Sandeep Manvar <sandeep@cyberxllc.com>
 */
function is_unsubscribe($email, $user_type, $medium = "email") {
	global $pdo;
	$sql = "SELECT * FROM unsubscriber
					WHERE email = :email AND user_type = :user_type AND medium = :medium
				 ";
	$params = array(
		':email' => makeSafe($email),
		':user_type' => makeSafe($user_type),
		':medium' => makeSafe($medium),
	);

	$result = $pdo->select($sql, $params);

	if (count($result) > 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * Generate random chat password
 *
 * @param int $length
 * @return string
 * @author Ritesh Patadiya <ritesh@cyberxllc.com>
 */
function generate_chat_password($length = 10) {
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

/**
 * return a string of propoer amount format
 * e.g. $12.55
 *
 * @param decimal $number
 * @param int $decimal
 * @return string
 * @author Ritesh Patadiya <ritesh@cyberxllc.com>
 */
function displayAmount($number, $decimal = 2, $PRICE_TAG = '$') {

	if ($PRICE_TAG == 'USD' || $PRICE_TAG == 'CAD' || $PRICE_TAG == 'USA' || $PRICE_TAG == 'United States' || $PRICE_TAG == 'Canada') {
		$PRICE_TAG = '$';
	} else if ($PRICE_TAG == 'GBP' || $PRICE_TAG == 'UK' || $PRICE_TAG == 'United Kingdom' || $PRICE_TAG == '£') {
		$PRICE_TAG = '£';
	} else if ($PRICE_TAG == '&pound;') {
		$PRICE_TAG = '&pound;';
	} else if ($PRICE_TAG == '') {
		$PRICE_TAG = '$';
	} else {
		$PRICE_TAG = '$';
	}

	if ($number != "" && is_numeric($number)) {
		if ($number < 0) {
			return '-' . $PRICE_TAG . number_format(abs($number), $decimal, '.', ',');
		} else {
			return $PRICE_TAG . number_format(abs($number), $decimal, '.', ',');
		}
	} else {
		return $PRICE_TAG . number_format(0, $decimal, '.', ',');
	}
}

/**
 * return a string of propoer percentage format
 * e.g. 12.55%
 *
 * @param decimal $number
 * @param int $decimal
 * @return string
 * @author Mitul Bhalara <mitul@cyberxllc.com>
 */
function displaypercentage($number, $decimal = 2) {
	$number_array = explode(".", $number);
	$decimal_value = $number_array[1];
	if ($decimal_value > 0) {
		$decimal_value = rtrim($decimal_value, 0);
		$display_value = $number_array[0] . '.' . $decimal_value;
	} else {
		$display_value = $number_array[0];
	}

	return number_format($display_value, $decimal) . '%';
}

/**
 *
 * @param type $val
 * @param type $is_exit
 * @author punit ladani
 */
function pre_print($val, $is_exit = true) {
	echo "<pre>";
	print_r($val);
	echo "</pre>";
	if ($is_exit) {
		exit;
	}
}

/**
 *
 * @param type $content
 * @param type $chars
 * @return string
 * @author ashwin pitroda
 */
function wrap_content($content, $chars = 10) {

	if (strlen($content) > $chars) {
		$content = str_replace('&nbsp;', ' ', $content);
		$content = str_replace("\n", '', $content);
		$content = strip_tags(trim($content));
		$content = preg_replace('/\s+?(\S+)?$/', '', substr($content, 0, $chars));

		$content = trim($content) . '...';
	}
	return $content;
}

function format_telephone($phone_number) {
	$cleaned = preg_replace('/[^[:digit:]]/', '', $phone_number);
	preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
	return "({$matches[1]}){$matches[2]}-{$matches[3]}";
}

function get_user_data($user) {
	$user_detail = array();
	$user_detail['user_id'] = $user['id'];
	$user_detail['display_id'] = $user['display_id'];
	if ($user['type'] == 'Affiliates' || 'Customer') {
		$user_detail['full_name'] = $user['fname'] . ' ' . $user['lname'];
		$user_detail['user_type'] = $user['type'];
	} else {
		$user_detail['full_name'] = $user['name'];
		$user_detail['user_type'] = 'Admin';
	}

	return $user_detail;
}

function audit_log($action_by_data, $user_id, $user_type, $desc, $old_value = '', $new_value = '', $attribute = '', $post_id = '') {
	//pre_print(func_get_args());
	//print_r($old_value); die;
	global $pdo, $LOG_DB;

	$description = $desc;
	if (count($action_by_data) == 0) {
		$action_by_id = 0;
		$action_by_name = 'System';
		$action_by_type = 'System';
	} else {
		$action_by_id = $action_by_data['user_id'];
		$action_by_name = $action_by_data['full_name'];
		$action_by_type = $action_by_data['user_type'];
	}
	if (trim($description)) {
		$insert_data = array(
			'action_by_id' => makeSafe($action_by_id),
			'action_by_type' => makeSafe($action_by_type),
			'action_by_name' => makeSafe($action_by_name),
			'user_id' => makeSafe($user_id),
			'user_type' => makeSafe($user_type),
			'description' => makeSafe($description),
			'old_value' => makeSafe($old_value),
			'new_value' => makeSafe($new_value),
			'attribute' => makeSafe($attribute),
			'post_id' => makeSafe($post_id),
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'changed_at' => 'msqlfunc_NOW()',
		);

		$id = $pdo->insert("$LOG_DB.audit_log", $insert_data);
		return $id;
	}
}

function audit_log_new($action_by_data, $user_id, $user_type, $desc, $old_value = '', $new_value = '', $attribute = '') {
	//print_r($old_value); die;
	global $pdo, $LOG_DB;
	//echo count($new_value)."   ".count($old_value);exit;
	$description = $desc;
	if (count($action_by_data) == 0) {
		$action_by_id = 0;
		$action_by_name = 'System';
		$action_by_type = 'System';
	} else {
		$action_by_id = $action_by_data['user_id'];
		$action_by_name = $action_by_data['full_name'];
		$action_by_type = 'Admin';
	}
	if ((isset($old_value) && $old_value != '') || (isset($new_value) && $new_value != '')) {
		if ($old_value == '') {
			$old_value = array();
		}
		if ($new_value == '') {
			$new_value = array();
		}
		if (count($new_value) > 0 && count($old_value) == 0) {
			$diff_audit_value = array_diff_assoc($new_value, $old_value);
		}
		if (count($old_value) > 0 && count($new_value) == 0) {
			$diff_audit_value = array_diff_assoc($old_value, $new_value);
		}
		if (count($old_value) > 0 && count($new_value) > 0) {
			$diff_audit_value = array_diff_assoc($new_value, $old_value);
		}
		if (count($diff_audit_value) > 0) {
			foreach ($diff_audit_value as $key => $arr_value) {
				$old_updated_value[$key] = $old_value[$key];
			}
		} else {
			$old_value = array();
			$new_value = array();
		}
	}
	if (trim($description)) {
		$insert_data = array(
			'action_by_id' => makeSafe($action_by_id),
			'action_by_type' => makeSafe($action_by_type),
			'action_by_name' => makeSafe($action_by_name),
			'user_id' => makeSafe($user_id),
			'user_type' => makeSafe($user_type),
			'description' => makeSafe($description),
			'attribute' => makeSafe($attribute),
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'changed_at' => 'msqlfunc_NOW()',
		);
		if (count($old_value) > 0) {
			$old_value = json_encode($old_updated_value);
			$insert_data['old_value'] = makeSafe($old_value);
		}
		if (count($new_value) > 0) {
			$new_value = json_encode($diff_audit_value);
			$insert_data['new_value'] = makeSafe($new_value);
		}
		if (count($diff_audit_value) > 0) {
			$id = $pdo->insert("$LOG_DB.audit_log", $insert_data);
			return $id;
		}
	}
}

function get_time_format($time) {
	$h = floor($time / (60 * 60));
	$m = floor(($time % (60 * 60)) / 60);
	$s = ($time % 60);

	$h = strlen($h) >= 2 ? $h : "0" . $h;
	$m = strlen($m) >= 2 ? $m : "0" . $m;
	$s = strlen($s) >= 2 ? $s : "0" . $s;
	return $h . ":" . $m . ":" . $s;
}

/*
 * Generate Random Password
 *
 */

function generate_random_password($length = 10) {
	$capitals = range('A', 'Z');
	$smalls = range('a', 'z');
	$numbers = range('0', '9');
	//$additional_characters = array('_','.');
	$final_array = array_merge($capitals, $smalls, $numbers);

	$password = '';

	while ($length--) {
		$key = array_rand($final_array);
		$password .= $final_array[$key];
	}

	return $password;
}

/**
 *
 * @param string $value
 * @param integer $last_characters
 * @return string
 * @author Priya Varu>
 */
function secure_string_display_format($value, $last_characters) {
	if ($value != '' && $last_characters != '') {
		$display_string = substr($value, -$last_characters);
		$hide_string = substr($value, 0, -$last_characters);
		$hide_string = preg_replace('~[a-z0-9A-Z]~', '*', $hide_string);
		return $hide_string . $display_string;
	} else {
		return "";
	}
}

function credit_card_decline_log($customer_id, $cc_params, $res) {
	global $pdo;
	global $CREDIT_CARD_ENC_KEY;
	global $CC_DECLINE_EMAIL;
	$response = json_encode($res);
	$decline_text = $cc_params['err_text'];
	$insParams = array(
		'order_type' => $cc_params['order_type'],
		'customer_id' => $customer_id,
		'name_on_card' => ($cc_params['firstname'] . ' ' . $cc_params['lastname']),
		'country' => $cc_params['country'],
		'state' => $cc_params['state'],
		'city' => $cc_params['city'],
		'zip' => $cc_params['zip'],
		'phone' => (isset($cc_params['phone']) ? $cc_params['phone'] : ''),
		'email' => (isset($cc_params['email']) ? $cc_params['email'] : ''),
		'address' => ($cc_params['address1'] . ' ' . $cc_params['address2']),
		'cvv_no' => $cc_params['cvv'],
		'card_no' => substr($cc_params['ccnumber'], -4),
		'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $cc_params['ccnumber'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
		'card_type' => $cc_params['card_type'],
		'card_expiry' => $cc_params['ccexp'],
		'amount' => $cc_params['amount'],
		'ip_address' => (isset($cc_params['ipaddress']) ? $cc_params['ipaddress'] : ""),
		'decline_text' => makeSafe($decline_text),
		'response' => $response,
		'browser' => $cc_params['browser'],
		'os' => $cc_params['os'],
		'req_url' => $cc_params['req_url'],
		'created_at' => 'msqlfunc_NOW()',
	);
	$cc_decline_id = $pdo->insert('cc_decline_log', $insParams);

	$trigger_param = array(
		'name_on_card' => ($cc_params['firstname'] . ' ' . $cc_params['lastname']),
		'country' => $cc_params['country'],
		'state' => $cc_params['state'],
		'city' => $cc_params['city'],
		'zip' => $cc_params['zip'],
		'email' => (isset($cc_params['email']) ? $cc_params['email'] : ''),
		'phone' => (isset($cc_params['phone']) ? $cc_params['phone'] : ''),
		'address' => ($cc_params['address1'] . ' ' . $cc_params['address2']),
		'cvv_no' => $cc_params['cvv'],
		'card_no' => 'XXX' . substr($cc_params['ccnumber'], -4),
		'card_type' => $cc_params['card_type'],
		'card_expiry' => $cc_params['ccexp'],
		'amount' => $cc_params['amount'],
		'ip_address' => (isset($cc_params['ipaddress']) ? $cc_params['ipaddress'] : ""),
		'processor' => (isset($cc_params['processor']) ? $cc_params['processor'] : ""),
		'decline_text' => $decline_text,
		//'response' => (isset($cc_params['responsetext']) ? $cc_params['responsetext'] : ""),
	);
	if ($CC_DECLINE_EMAIL != "") {
		$subject = "MyHealthPass : Credit Card Declined";
		if (isset($cc_params['order_type'])) {
			$subject .= ", " . $cc_params['order_type'] . " Order";
		}

		$admin_trigger_param = array('trigger_title' => 'MyHealthPass : Credit Card Declined', 'location' => $cc_params['req_url']);
		trigger_mail_to_email($trigger_param, $CC_DECLINE_EMAIL, $subject, $admin_trigger_param);
	}
	return $cc_decline_id;
}

function getNextAutoshipDate($date, $autoshipday) {
	$d1 = new DateTime($date);
	$day = $d1->format('d');
	if ($day <= 28 && $autoshipday <= 28) {
		return date("Y-m-d", strtotime($date . " +1 month"));
	} else {
		$year = $d1->format('Y');
		$month = $d1->format('n');
		if ($month == 12) {
			$year++;
		}

		$month = (++$month) % 12;
		if ($month == 0) {
			$month = 12;
		}

		$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		if ($autoshipday > $total_days) {
			$day = $total_days;
		} else {
			$day = $autoshipday;
		}

		$d2 = DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day);
		return $d2->format('Y-m-d');
	}
}

function csv_display_data($str) {
	return html_entity_decode(stripslashes($str));
}

function isValidDate($date, $format = "Y-m-d") {
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function convertTimeZone($date, $from = "EST", $to = "EST") {
	if (!isset($date)) {
		$date = date("Y-m-d H:i:s");
	}

	$d = new DateTime($date, new DateTimeZone(strtoupper($from)));
	$d->setTimeZone(new DateTimeZone(strtoupper($to)));
	return $d->format('Y-m-d H:i:s');
}

function getRandomString($type = 'String', $length = 6) {

	if ($type == 'Digit') {
		$characters = '1234567890';
	} else {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	$string = '';
	for ($i = 0; $i < $length; $i++) {
		$string .= $characters[mt_rand(0, strlen($characters) - 1)];
	}
	return $string;
}

function getInvitionCode($type = 'String', $length = 6) {

	global $pdo;
	$str = getRandomString($type, $length);
	$checkSql = "select code from opp_invite_details where code=:code";
	$params = array(":code" => makeSafe($str));
	$checkRow = $pdo->selectOne($checkSql, $params);
	if (count($checkRow) > 0) {
		return getInvitionCode($type, $length);
	} else {
		$selSql = "select invitation_access_code from customer where (invitation_access_code=:code OR user_name=:code)";
		$selParams = array(':code' => $str);
		$codeRow = $pdo->selectOne($selSql, $selParams);
		if (count($codeRow) > 0) {
			return getInvitionCode($type, $length);
		} else {
			$selSql = "select invitation_access_code from admin where invitation_access_code=:code";
			$selParams = array(':code' => $str);
			$codeRow = $pdo->selectOne($selSql, $selParams);
			if (count($codeRow) > 0) {
				return getInvitionCode($type, $length);
			} else {
				return $str;
			}
		}
	}
}

function place_in_tree($sponsor_id, $parent_id, $place_user_id, $place_side, $placed_by) {
	//pre_print(func_get_args(),false);
	global $pdo;
	$place_side = strtolower($place_side);

	$iCheck = 0;
	if ($place_side == 'balance') {
		$sql = "SELECT * FROM binary_tree where user_id = " . $sponsor_id . " AND (left_node = 0 OR right_node = 0)";
		$row = $pdo->selectOne($sql);
		if (!$row) {
			$sql = "SELECT * FROM binary_tree where (upline_placements LIKE '%," . $sponsor_id . ",%') AND (left_node = 0 OR right_node = 0) AND user_id != " . $place_user_id . " ORDER BY level,place_side LIMIT 1";
			$row = $pdo->selectOne($sql);
		}
		if ($row) {
			if ($row['left_node'] == 0) {
				$parent_id = $row['user_id'];
				$place_side = "left";
			} else if ($row['right_node'] == 0) {
				$parent_id = $row['user_id'];
				$place_side = "right";
			}
		}
	}
	$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
	//echo $place_side;
	$parentRow = $pdo->selectOne($sql, array(':user_id' => $parent_id));
	//pre_print($parentRow, false);
	$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
	$placeRow = $pdo->selectOne($sql, array(':user_id' => $place_user_id));
	//pre_print($placeRow, false);

	if ($place_side == "left") {
		$iCheck = $parentRow['left_node'];
	} else if ($place_side == "right") {
		$iCheck = $parentRow['right_node'];
	}

	//echo $parent_id;
	//echo $iCheck;
	//exit;
	while (true) {
		if ($iCheck == 0) {
			//pre_print($parentRow,false);

			$update_params = array();
			$parent_update_params = array();
			if (strtolower($place_side) == "left") {
				$update_params['place_side'] = "Left";

				$parent_update_params['left_node'] = $placeRow['user_id'];
			} else if (strtolower($place_side) == "right") {
				$update_params['place_side'] = "Right";

				$parent_update_params['right_node'] = $placeRow['user_id'];
			}
			$parent_update_params['updated_at'] = 'msqlfunc_NOW()';
			$parent_update_where = array(
				'clause' => 'id=:id',
				'params' => array(':id' => makesafe($parentRow['id'])),
			);
			/* pre_print($parent_update_params,false);
            pre_print($parent_update_where,false); */
			//exit;
			$pdo->update('binary_tree', $parent_update_params, $parent_update_where);

			$level = count(explode(",", trim($parentRow['upline_placements'] . $parentRow['user_id'], ",")));

			$update_params['parent_node'] = $parentRow['user_id'];
			$update_params['upline_placements'] = (',' . ltrim($parentRow['upline_placements'], ',') . $parentRow['user_id'] . ',');
			$update_params['level'] = $level;
			$update_params['is_placed'] = 'Y';
			$update_params['updated_at'] = 'msqlfunc_NOW()';

			$update_where = array(
				'clause' => 'id=:id',
				'params' => array(':id' => makesafe($placeRow['id'])),
			);
			/* pre_print($update_params,false);
				            pre_print($update_where,false);
			*/
			$pdo->update('binary_tree', $update_params, $update_where);
			$ins_params = array(
				'user_id' => $placeRow['id'],
				'placed_under_id' => $parentRow['user_id'],
				'place_side' => ucfirst($place_side),
				'placed_by' => $placed_by['placed_by'],
				'placed_by_id' => $placed_by['placed_by_id'],
				'created_at' => 'msqlfunc_NOW()',
			);
			if (isset($_SERVER['REMOTE_ADDR'])) {
				$ins_params['ip_address'] = $_SERVER['REMOTE_ADDR'];
			}
			$pdo->insert("binary_tree_placement_history", $ins_params);
			update_down_placement($placeRow['user_id']);
			//echo "<br>{$placeRow['user_id']} Placed Under {$place_side} OF {$parentRow['user_id']}";
			/* $tp = array();
	            $tp['place_id'] = $placeRow['user_id'];
	            $tp['place_side'] = $place_side;
	            $tp['parent_id'] = $parentRow['user_id'];
*/
			break;
		} else {
			$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
			$parentRow = $pdo->selectOne($sql, array(':user_id' => $iCheck));
			if (strtolower($place_side) == "left") {
				$iCheck = $parentRow['left_node'];
			} else if (strtolower($place_side) == "right") {
				$iCheck = $parentRow['right_node'];
			}
			//pre_print($parentRow,false);
			//exit;
		}
	}
	//exit;
}

// this function will automatically place new enrolling partner in dual team based on setting of sponsor(Auto, Left,Right)

function place_in_tree_admin($sponsor_id, $parent_id, $place_user_id, $place_side, $placed_by) {
	//pre_print(func_get_args(),false);
	global $pdo;
	$place_side = strtolower($place_side);

	$iCheck = 0;

	$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
	//echo $place_side;
	$parentRow = $pdo->selectOne($sql, array(':user_id' => $parent_id));
	//pre_print($parentRow, false);
	$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
	$placeRow = $pdo->selectOne($sql, array(':user_id' => $place_user_id));
	//pre_print($placeRow, false);

	if ($place_side == "left") {
		$iCheck = $parentRow['left_node'];
	} else if ($place_side == "right") {
		$iCheck = $parentRow['right_node'];
	}

	//echo $parent_id;
	//echo $iCheck;
	//exit;
	while (true) {
		if ($iCheck == 0) {
			//pre_print($parentRow,false);

			$update_params = array();
			$parent_update_params = array();
			if (strtolower($place_side) == "left") {
				$update_params['place_side'] = "Left";

				$parent_update_params['left_node'] = $placeRow['user_id'];
			} else if (strtolower($place_side) == "right") {
				$update_params['place_side'] = "Right";

				$parent_update_params['right_node'] = $placeRow['user_id'];
			}
			$parent_update_params['updated_at'] = 'msqlfunc_NOW()';
			$parent_update_where = array(
				'clause' => 'id=:id',
				'params' => array(':id' => makesafe($parentRow['id'])),
			);
			/* pre_print($parent_update_params,false);
            pre_print($parent_update_where,false); */
			//exit;
			$pdo->update('binary_tree', $parent_update_params, $parent_update_where);

			$level = count(explode(",", trim($parentRow['upline_placements'] . $parentRow['user_id'], ",")));

			$update_params['parent_node'] = $parentRow['user_id'];
			$update_params['upline_placements'] = (',' . ltrim($parentRow['upline_placements'], ',') . $parentRow['user_id'] . ',');
			$update_params['level'] = $level;
			$update_params['is_placed'] = 'Y';
			$update_params['updated_at'] = 'msqlfunc_NOW()';

			$update_where = array(
				'clause' => 'id=:id',
				'params' => array(':id' => makesafe($placeRow['id'])),
			);
			/* pre_print($update_params,false);
				            pre_print($update_where,false);
			*/
			$pdo->update('binary_tree', $update_params, $update_where);
			$ins_params = array(
				'user_id' => $placeRow['id'],
				'placed_under_id' => $parentRow['user_id'],
				'place_side' => ucfirst($place_side),
				'placed_by' => $placed_by['placed_by'],
				'placed_by_id' => $placed_by['placed_by_id'],
				'created_at' => 'msqlfunc_NOW()',
			);
			if (isset($_SERVER['REMOTE_ADDR'])) {
				$ins_params['ip_address'] = $_SERVER['REMOTE_ADDR'];
			}
			$pdo->insert("binary_tree_placement_history", $ins_params);
			update_down_placement($placeRow['user_id']);
			//echo "<br>{$placeRow['user_id']} Placed Under {$place_side} OF {$parentRow['user_id']}";
			/* $tp = array();
	            $tp['place_id'] = $placeRow['user_id'];
	            $tp['place_side'] = $place_side;
	            $tp['parent_id'] = $parentRow['user_id'];
*/
			break;
		} else {
			$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
			$parentRow = $pdo->selectOne($sql, array(':user_id' => $iCheck));
			if (strtolower($place_side) == "left") {
				$iCheck = $parentRow['left_node'];
			} else if (strtolower($place_side) == "right") {
				$iCheck = $parentRow['right_node'];
			}
			//pre_print($parentRow,false);
			//exit;
		}
	}
	//exit;
}

/*
function place_in_tree($sponsor_id, $parent_id, $place_user_id, $place_side) {
//pre_print(func_get_args(), false);

global $pdo;
//$pdo->displayError();
$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
$parentRow = $pdo->selectOne($sql, array(':user_id' => $parent_id));
//pre_print($parentRow, false);
$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
$placeRow = $pdo->selectOne($sql, array(':user_id' => $place_user_id));
//pre_print($placeRow, false);
$iCheck = 0;
if ($place_side == "left") {
$iCheck = $parentRow['left_node'];
} else if ($place_side == "right") {
$iCheck = $parentRow['right_node'];
} else if ($place_side == "balance") {

}
//echo $iCheck;
//exit;
while (true) {
if ($iCheck == 0) {
$update_params = array();
$parent_update_params = array();
if ($place_side == "left") {
$update_params['place_side'] = "Left";

$parent_update_params['left_node'] = $placeRow['user_id'];
} else if ($place_side == "right") {
$update_params['place_side'] = "Right";

$parent_update_params['right_node'] = $placeRow['user_id'];
}
$parent_update_params['updated_at'] = 'msqlfunc_NOW()';
$parent_update_where = array(
'clause' => 'user_id=:user_id',
'params' => array(':user_id' => makesafe($parentRow['user_id']))
);
$pdo->update('binary_tree', $parent_update_params, $parent_update_where);

$update_params['parent_node'] = $parentRow['user_id'];
$update_params['upline_placements'] = (',' . ltrim($parentRow['upline_placements'],',') . $parentRow['user_id'] . ',');
$update_params['level'] = $parentRow['level'] + 1;
$update_params['is_placed'] = 'Y';
$update_params['updated_at'] = 'msqlfunc_NOW()';

$update_where = array(
'clause' => 'user_id=:user_id',
'params' => array(':user_id' => makesafe($placeRow['user_id']))
);
$pdo->update('binary_tree', $update_params, $update_where);

update_down_placement($placeRow['user_id']);
break;
} else {
$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
$parentRow = $pdo->selectOne($sql, array(':user_id' => $iCheck));
if ($place_side == "left") {
$iCheck = $parentRow['left_node'];
} else if ($place_side == "right") {
$iCheck = $parentRow['right_node'];
}
//    pre_print($parentRow,false);
}
}
//exit;
} */

function update_down_placement($node_id) {
	global $pdo;
	//echo "<br>" . $node_id;
	$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
	$row = $pdo->selectOne($sql, array(':user_id' => $node_id));
	//pre_print($row,false);
	if ($row) {
		if ($row['left_node'] > 0) {
			$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
			$down_row = $pdo->selectOne($sql, array(':user_id' => $row['left_node']));
			//echo "left" ; pre_print($down_row,false);
			$level = count(explode(",", trim($row['upline_placements'] . $row['user_id'], ",")));
			$update_params = array();
			$update_params['level'] = $level;
			$update_params['upline_placements'] = (',' . ltrim($row['upline_placements'], ',') . $row['user_id'] . ',');
			$update_where = array(
				'clause' => 'user_id=:user_id',
				'params' => array(':user_id' => makesafe($down_row['user_id'])),
			);
			$pdo->update('binary_tree', $update_params, $update_where);
			update_down_placement($row['left_node']);
		}
		if ($row['right_node'] > 0) {
			$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
			$down_row = $pdo->selectOne($sql, array(':user_id' => $row['right_node']));
			//echo "right" ; pre_print($down_row,false);
			$level = count(explode(",", trim($row['upline_placements'] . $row['user_id'], ",")));
			$update_params = array();
			$update_params['level'] = $level;
			$update_params['upline_placements'] = (',' . ltrim($row['upline_placements'], ',') . $row['user_id'] . ',');
			$update_where = array(
				'clause' => 'user_id=:user_id',
				'params' => array(':user_id' => makesafe($down_row['user_id'])),
			);
			$pdo->update('binary_tree', $update_params, $update_where);
			update_down_placement($row['right_node']);
		}
	}
	return;
}

function get_tree_users($node_id, $sponsor_id = 0) {
	global $pdo, $tree_counter, $HOST;
	$nodes = array();
	$sql = "SELECT c.id, CONCAT(c.fname,' ', c.lname) as member_name, c.rep_id, c.email, c.type, c.rank, c.status, c.sponsor_id, b.left_node, b.right_node, b.place_side, CONCAT(s.fname,' ',s.lname) as sponsor_name
    FROM binary_tree b
    JOIN customer c ON c.id = b.user_id
    LEFT JOIN customer s ON s.id = b.sponsor_id
    WHERE b.user_id = " . $node_id;
	$row = $pdo->selectOne($sql);

	if ($row) {
		$t = array();
		$t['id'] = $tree_counter++;
		$t['align'] = strtolower($row['place_side']);
		$t['name'] = "<div class='node_label'><img src='" . $HOST . "/member/images/user-icon/" . strtolower($row['type']) . ".png' /><span>{$row["rep_id"]}</span></div>";
		$t['data'] = array('Name' => stripslashes($row["member_name"]), 'ID' => $row["rep_id"], 'Email' => $row["email"], 'Type' => $row["type"], 'Status' => $row["status"], 'Rank' => $row["rank"], 'Sponsor' => $row["sponsor_name"]);
		$t['node'] = array();
		if ($row['left_node'] > 0) {
			$t['node'][] = get_tree_users($row['left_node'], $sponsor_id);
		} else {
			$t['node'][] = get_blank_node($row['id'], 'left', $tree_counter++, $sponsor_id);
		}

		if ($row['right_node'] > 0) {
			$t['node'][] = get_tree_users($row['right_node'], $sponsor_id);
		} else {
			$t['node'][] = get_blank_node($row['id'], 'right', $tree_counter++, $sponsor_id);
		}

		$t['children'] = $t['node'];
		unset($t['node']);
		$nodes = $t;
	}
	return $nodes;
}

function get_blank_node($parent_id, $place_side, $c, $sponsor_id) {
	global $HOST;
	$t = array();
	$t['id'] = $c;
	$t['name'] = "<div class='node_label'><a href='place_here.php?parent_id=" . md5($parent_id) . "&place_here=" . $place_side . "&sponsor_id=" . $sponsor_id . "' class='place_popup'><img src='" . $HOST . "/member/images/user-icon/open.png' /></a></div>";
	$t['data'] = array();
	$t['children'] = array();
	return $t;
}

function getLastNodeArray($node_id, $side, $tmp_data = array()) {
	global $pdo;
	$sql = "SELECT left_node, right_node FROM binary_tree WHERE is_placed = 'Y' AND user_id = " . $node_id;
	$row = $pdo->selectOne($sql);
	if ($side == "Left") {
		if ($row['left_node'] != 0) {
			$tmp_data[] = $row['left_node'];
			return getLastNodeArray($row['left_node'], $side, $tmp_data);
		} else {
			return $tmp_data;
		}
	} else if ($side == "Right") {
		if ($row['right_node'] != 0) {
			$tmp_data[] = $row['right_node'];
			return getLastNodeArray($row['right_node'], $side, $tmp_data);
		} else {
			return $tmp_data;
		}
	}
}

function getLastNodeId($node_id, $side) {
	global $pdo;
	$sql = "SELECT left_node, right_node FROM binary_tree WHERE is_placed = 'Y' AND user_id = " . $node_id;
	$row = $pdo->selectOne($sql);
	if ($side == "Left") {
		if ($row['left_node'] != 0) {
			return getLastNodeId($row['left_node'], $side);
		} else {
			return $node_id;
		}
	} else if ($side == "Right") {
		if ($row['right_node'] != 0) {
			return getLastNodeId($row['right_node'], $side);
		} else {
			return $node_id;
		}
	}
}

function get_enrollment_users($node_id, $jsonArr = array()) {
	global $pdo, $tree_counter, $HOST;
	$sql = "SELECT c.id, c.fname, c.lname, c.rep_id, c.type, c.rank, c.sponsor_id, b.is_placed
  JOIN binary_tree b ON b.user_id = c.id
  FROM customer c
  WHERE c.sponsor_id = " . $node_id;
	$rows = $pdo->select($sql);
	//pre_print($rows,false);
	if (count($rows)) {
		foreach ($rows as $key => $row) {
			$t = array();
			$t['id'] = $tree_counter++; //$row['id'];
			$t['name'] = "<div class='node_label'><img src='" . $HOST . "/member/images/user-icon/" . strtolower($row['type']) . ".png' /><span>{$row["rep_id"]}</span></div>";
			$t['data'] = array($row['type'] . '_ID' => $row["rep_id"], 'Name' => ($row["fname"] . " " . $row["lname"]), 'Rank' => $row["rank"]);
			$t['children'] = get_enrollment_users($row['id']);
			$jsonArr[] = $t;
		}
	}
	return $jsonArr;
}

function add_cron_job($params) {
	global $pdo, $LOG_DB;
	$ins_params = $params;
	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ins_params['ip_address'] = $_SERVER['REMOTE_ADDR'];
	}
	$ins_params['run_at'] = 'msqlfunc_NOW()';
	$pdo->insert($LOG_DB . ".cron_jobs", $ins_params);
}

function get_ck_editor_instance($selector, $value = "", $class_list = "") {
	echo PHP_EOL . '<textarea class="' . $class_list . '" id="' . $selector . '" name="' . $selector . '" rows="12">' . $value . '</textarea>' . PHP_EOL;
	echo '<script>' . PHP_EOL;
	echo "CKEDITOR.replace('" . $selector . "', {
       on: {
              instanceReady: function(evt) {
                $('.cke').addClass('admin-skin cke-hide-bottom');
              }
            },
      toolbar: [
        {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat']},
        {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote']},
      ]
    });";
	echo '</script>' . PHP_EOL;
}

function br2nl($string) {
	return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

/**
 * Get Shipping and Tax Value
 *
 * @global float $DEFAULT_VAT
 * @param array $params(
 *                'product_total'   //required Product Total
 *                'zip',            //required
 *                'state',          //required
 *              )
 *
 * @return array('tax' => 0, 'shipping' => 0)
 */
function getShippingTaxValue($params = array(), $type = "both") {

	global $DEFAULT_VAT, $DEFAULT_TAX_STATE; // IF site load is UK then required
	global $DEFAULT_SHIPPING, $DEFAULT_USA_SHIPPING, $DEFAULT_UK_SHIPPING, $DEFAULT_CANADA_SHIPPING, $D_SHIPPING;

	$tax_value = 0;
	$shipping_value = 0;

	if (count($params)) {

		//pre_print($params);

		if ($params['shipping_type'] == 'Shipping' || $params['shipping_type'] == 'Sponsor Pickup') {
			if (in_array($type, array("tax", "both"))) {
				$zipRow = getname("zip_code", $params['zip'], "combined_rate", "zip_code");
				if ($zipRow) {
					$taxes = $zipRow;
					$tax_value = ($params['product_total']) * ($taxes);
				} else {
					if ($params['state'] == "") {
						$params['state'] = getname("customer", $params['customer_id'], "state", "id");
						if ($params['state'] == "") {
							$params['state'] = $DEFAULT_TAX_STATE;
						}
					}
					//$taxes = getname("states_c", $params['state'], "tax", "name");
					//$tax_value = ($params['product_total']) * ($taxes / 100);
					// Temparary Tax
					$taxes = 0;
					$tax_value = ($params['product_total']) * ($taxes / 100);
				}
			}

			if (in_array($type, array("shipping", "both"))) {
				// Calculate total quantities
				$total_qty = 0;
				foreach ($params['product'] as $key => $value) {

					$qt = 1 * $value['qty'];
					if ($value['id'] == 17) {
						$qt = 7 * $value['qty'];
					} else if ($value['id'] == 18) {
						$qt = 19 * $value['qty'];
					}
					$total_qty += $qt;
				}

				if ($total_qty > 50) {
					$shipping_value = $DEFAULT_USA_SHIPPING[8]['charge'];
				} else {
					foreach ($DEFAULT_USA_SHIPPING as $shipping_charge_ar) {
						if ($total_qty >= $shipping_charge_ar['from'] && $total_qty <= $shipping_charge_ar['to']) {
							$shipping_value = $shipping_charge_ar['charge'];
							break;
						}
					}
				}
			}
		} else if ($params['shipping_type'] == 'Pickup') {
			if (in_array($type, array("tax", "both"))) {
				$state = getname("customer", $params['customer_id'], "state", "id");
				if ($state == "") {
					$state = $DEFAULT_TAX_STATE;
				}
				$taxes = getname("states_c", $state, "tax", "name");
				$tax_value = ($params['product_total']) * ($taxes / 100);
			}
		}
	}
	return array('tax' => number_format($tax_value, 2, ".", ","), 'shipping' => number_format($shipping_value, 2, ".", ","));
}

function validateCanadaZip($zip_code) {
	if (preg_match("/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/", $zip_code)) {
		return true;
	} else {
		return false;
	}
}

if (!function_exists('dump')) {

	function dump($var, $label = 'Dump', $exit = false, $echo = TRUE) {
		// Store dump in variable
		ob_start();
		var_dump($var);
		$output = ob_get_clean();

		// Add formatting
		$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
		$output = '<p style="background: #62A83D;
    color: #FFFFFF;
    border: 1px dotted #000;
    padding: 10px;
    margin: 10px 0;
    text-align: left;
    position: fixed;
    z-index: 99999;
    width: 100%;
    font-size: 17px;word-break: break-all; font-family: vardana courier">' . $label . ' => ' . $output . '</p>';

		// Output
		if ($echo == TRUE) {
			echo $output;
		} else {
			return $output;
		}
		if ($exit) {
			die();
		}
	}

}

// generate random code for business cards
function generate_rand_code() {
	global $pdo;

	$rand_code = rand(1000, 9999);
	$code_sql = "SELECT id FROM business_card WHERE code = '" . $rand_code . "'";
	$code_res = $pdo->selectOne($code_sql);
	if ($code_res) {
		return generate_rand_code();
	} else {
		return $rand_code;
	}
}

function remote_file_upload_biz_card($path, $tmp_name, $new_image, $old_image = '') {
	global $MSTAG_HOST;

	$params = 'path=' . urlencode($path) . '&tmp_name=' . ($tmp_name) . '&new_image=' . urlencode($new_image) . '&old_image_name=' . urlencode($old_image);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $HOST . "/scripts/upload_file_profile.php");
	// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, "path=" . urlencode($path));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function remote_move_uploaded_file($path, $tmp_image, $new_image, $company_id = 0, $old_image = "") {
	global $SITE_SETTINGS;
	$site_url = $SITE_SETTINGS[$company_id]['HOST'];
	if ($company_id == 0) {
		$company_id = 3;
	}
	if ($company_id == 3) {
		$response = array();
		$response['status'] = 'Fail';
		$response['message'] = 'Could not upload file';
		if (count($tmp_image) > 0) {
			if (move_uploaded_file($tmp_image['tmp_name'], dirname(__DIR__) . $path . $new_image)) {
				$response['status'] = 'Success';
				$response['message'] = 'File uploaded successfully';
			}
		}
		return $response;
	}

	$curl_handle = curl_init($site_url . "/remote_scripts/upload.php");
	curl_setopt($curl_handle, CURLOPT_POST, 1);
	$args['path'] = $path;
	$args['old_image'] = $old_image;
	$args['file'] = new CurlFile($tmp_image['tmp_name'], $tmp_image['type'], $new_image);
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $args);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);

	//execute the API Call
	$returned_data = curl_exec($curl_handle);

	if (curl_errno($curl_handle)) {
		echo $msg = curl_error($curl_handle);
		exit;
	}
	curl_close($curl_handle);
	return json_decode($returned_data, true);
}

function issetor(&$var, $default = false) {
	return isset($var) ? $var : $default;
}

/**
 * will return transaction id for orders
 */
function get_transaction_id() {
	global $pdo;
	$trans_id = rand(1000000, 9999999);
	$sql = "SELECT transaction_id FROM orders WHERE transaction_id =$trans_id";
	$res = $pdo->selectOne($sql);
	if ($res) {
		return get_transaction_id();
	} else {
		return $trans_id;
	}
}

function affect_admin_inventory($products, $type, $otherparam = array()) {
	global $pdo;

	foreach ($products as $key => $product) {
		$checkProduct = "SELECT * FROM product WHERE is_inventory_product = 'Y' AND id = :product_id";
		$checkProduct = $pdo->selectOne($checkProduct, array(':product_id' => makeSafe($product['id'])));

		if ($checkProduct) {
			foreach ($product['qty'] as $loc_key => $loc_qty) {
				$current_stock = 0;
				$variation_id = 0;
				if ($product['variation_id'] != '' && $product['variation_id'] > 0) {
					$variation_id = $product['variation_id'];
				}
				$checkLocation = "SELECT * FROM inventory_location_stock
            WHERE product_id = :product_id AND inv_location_id = :inv_location_id AND variation_id=:variation_id";
				$checkLocation = $pdo->selectOne($checkLocation, array(':product_id' => $product['id'], ':inv_location_id' => $loc_key, ":variation_id" => $variation_id));
				if ($checkLocation) {
					if ($type == 'In') {
						$current_stock = $checkLocation['stock'] + $loc_qty;
						$upd_params = array(
							'stock' => 'msqlfunc_stock + ' . $loc_qty,
							'updated_at' => 'msqlfunc_NOW()',
						);
					} else if ($type == 'Out') {
						$current_stock = $checkLocation['stock'] - $loc_qty;
						$upd_params = array(
							'stock' => 'msqlfunc_stock - ' . $loc_qty,
							'updated_at' => 'msqlfunc_NOW()',
						);
					}
					$upd_where = array(
						'clause' => 'id = :p_id',
						'params' => array(
							':p_id' => makeSafe($checkLocation['id']),
						),
					);
					$pdo->update("inventory_location_stock", $upd_params, $upd_where);
				} else {
					$current_stock = $loc_qty;
					$ins_params = array(
						'product_id' => $product['id'],
						'inv_location_id' => $loc_key,
						'variation_id' => $variation_id,
						'stock' => $loc_qty,
						'created_at' => 'msqlfunc_NOW()',
						'updated_at' => 'msqlfunc_NOW()',
					);
					$pdo->insert("inventory_location_stock", $ins_params);
				}
				$country = getname('inventory_location', $loc_key, "country", 'id');
				$inv_his = array(
					'product_id' => $product['id'],
					'type' => $type,
					'qty' => $loc_qty,
					'stock' => $current_stock,
					'inv_location_id' => $loc_key,
					'country' => $country,
					'created_at' => 'msqlfunc_NOW()',
				);
				if ($variation_id > 0) {
					$inv_his['variation_id'] = $variation_id;
				}
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$inv_his['ip_address'] = $_SERVER['REMOTE_ADDR'];
				}
				if (isset($otherparam['is_new'])) {
					$inv_his['is_new'] = $otherparam['is_new'];
				}
				if (isset($otherparam['admin_id'])) {
					$inv_his['admin_id'] = $otherparam['admin_id'];
				}
				if (isset($otherparam['note'])) {
					$inv_his['note'] = makeSafe($otherparam['note']);
				}
				$pdo->insert('inventory_history', $inv_his);
			}
		}
	}
}

function get_coupon_code($length = 5, $start_with = "") {
	global $pdo;
	$chars = "123456789ABCDEFGHJKMNPQRSTUVWXYZ";
	$coupon_code = "";
	$lstart = 0;
	if ($start_with != "") {
		$coupon_code = $start_with;
		$lstart = strlen($start_with);
	}
	for ($i = $lstart; $i < $length; $i++) {
		$coupon_code .= $chars[mt_rand(0, strlen($chars) - 1)];
	}
	$checkSql = "SELECT COUNT(id) as cnt FROM coupon WHERE coupon_code = :coupon_code";
	$checkRow = $pdo->selectOne($checkSql, array(':coupon_code' => makesafe($coupon_code)));
	if ($checkRow['cnt'] > 0) {
		get_coupon_code();
	} else {
		if (!empty($coupon_code)) {
			return $coupon_code;
		} else {
			get_coupon_code();
		}
	}
}

function apply_coupon($coupon_code, $customer_id, $order_details) {
	global $pdo;

	$couponSql = "select * from coupon where coupon_code=:coupon_code AND status='Active' AND is_delete='N'";
	$couponParams = array(":coupon_code" => makeSafe($coupon_code));
	$couponRow = $pdo->selectOne($couponSql, $couponParams);

	if (count($couponRow) > 0) {
		//checking coupon expiry
		if ($couponRow['user_type'] != 'All') {
			$user_type = '';
			if ($customer_id != "" && $customer_id > 0) {
				$user_type = getname("customer", $customer_id, "type", "id");
			}
			if ($user_type != $couponRow['user_type']) {
				$res['status'] = 'Fail';
				$res['message'] = 'You are not valid user to use this coupon code';
				return $res;
			}
		}
		if ($couponRow['coupon_location'] != 'All' && $couponRow['coupon_location'] != 'Mrej') {
			$res['status'] = 'Fail';
			$res['message'] = 'You can not use this coupon on current order';
			return $res;
		}
		if ($couponRow['id'] == 168) {
			foreach ($order_details as $key => $prd) {
				$order_total += $prd['qty'] * $prd['price'];
				if ($prd['qty'] != 2 || ($prd['product_id'] != 4 && $prd['product_id'] != 83)) {
					$res['status'] = 'Fail';
					$res['message'] = 'You can not use this coupon on current order';
					return $res;
				}
			}
		}

		if ($couponRow['usage_rule'] == 'Date' || $couponRow['usage_rule'] == 'Date_Usage') {
			if (strtotime(date('Y-m-d')) < strtotime($couponRow['from_date']) || strtotime(date('Y-m-d')) > strtotime($couponRow['to_date'])) {
				$res['status'] = 'Fail';
				$res['message'] = 'Coupon Code Expired';
				return $res;
			}
		}
		if ($couponRow['usage_rule'] == 'Usage' || $couponRow['usage_rule'] == 'Date_Usage') {

			if ($couponRow['usage_rule_type'] == 'Global') {
				if ($couponRow['max_usage'] <= $couponRow['total_used']) {
					$res['status'] = 'Fail  ';
					$res['message'] = 'Coupon Code Expired';
					return $res;
				}
			} elseif ($couponRow['usage_rule_type'] == 'Person') {
				if ($customer_id != "" && $customer_id > 0) {
					$total_personal_used = get_personal_coupon_used($couponRow['id'], $customer_id);
					if ($couponRow['max_usage'] <= $total_personal_used) {
						$res['status'] = 'Fail';
						$res['message'] = 'Coupon Code Expired';
						return $res;
					}
				} else {
					$res['status'] = 'Fail';
					$res['login_required'] = "Yes";
					$res['message'] = 'Please login to continue applying coupon';
					//$res['coupon_code'] = $coupon_code;
					return $res;
				}
			}
		}

		// Per Order Discount Code
		if ($couponRow['discount_on'] == 'per_order') {
			$is_future_discount = "No";
			if ($couponRow['set_condition'] == 'Yes') {
				// checking conditions
				$conditions = json_decode($couponRow['conditions'], true);
				//pre_print($conditions, false);
				if (count($conditions) > 0) {
					$order_total = 0;
					foreach ($order_details as $key => $prd) {
						$order_total += $prd['qty'] * $prd['price'];
					}
					foreach ($conditions as $cond) {
						if ($cond['type'] == 'Percentage') {
							$discount = ($order_total) * ($cond['value'] / 100);
						} else {
							$discount = $cond['value'];
						}

						$discount = $discount > ($order_total) ? ($order_total) : $discount;

						if ($cond['operator'] == 'BETWEEN') {
							if (($order_total) >= $cond['total_order'] && ($order_total) <= $cond['total_2_order']) {
								if ($cond['apply_to'] == 'CURRENT_PURCHASE') {
									$discount_details['discount_amount_type'] = $cond['type'];
									$discount_details['discount_amount'] = $cond['value'];
									$discount_details['total_discount'] = number_format($discount, 2, ".", "");
								} elseif ($cond['apply_to'] == 'FUTURE_PURCHASE') {
									$is_future_discount = "Yes";
									$discount_details['discount_on'] = $couponRow['discount_on'];
									$discount_details['discount_amount_type'] = $cond['type'];
									$discount_details['discount_amount'] = $cond['value'];
								} elseif ($cond['apply_to'] == 'SHIPPING') {
									$discount_details['is_on_shipping'] = "Yes";
									$discount_details['discount_amount_type'] = $cond['type'];
									$discount_details['discount_amount'] = $cond['value'];
								}
							}
						} elseif ($cond['operator'] == 'GREATER_THAN') {
							if (($order_total) > $cond['total_order']) {
								if ($cond['apply_to'] == 'CURRENT_PURCHASE') {
									$discount_details['discount_amount_type'] = $cond['type'];
									$discount_details['discount_amount'] = $cond['value'];
									$discount_details['total_discount'] = number_format($discount, 2, ".", "");
								} elseif ($cond['apply_to'] == 'FUTURE_PURCHASE') {
									$is_future_discount = "Yes";
									$discount_details['discount_on'] = $couponRow['discount_on'];
									$discount_details['discount_amount_type'] = $cond['type'];
									$discount_details['discount_amount'] = $cond['value'];
								} elseif ($cond['apply_to'] == 'SHIPPING') {
									$discount_details['is_on_shipping'] = "Yes";
									$discount_details['discount_amount_type'] = $cond['type'];
									$discount_details['discount_amount'] = $cond['value'];
								}
							}
						}
					}
					//calculating order discount on per product
					if (count($discount_details) > 0 && $is_future_discount != "Yes") {
						if ($discount_details['is_on_shipping'] == 'Yes') {
							// code pending for shipping
						} else {
							if ($discount_details['discount_amount_type'] == 'Percentage') {
								foreach ($order_details as $key => $prd) {
									$discount = ($prd['price'] * $prd['qty']) * ($discount_details['discount_amount'] / 100);
									$discount_details[$key]['discount_amount_type'] = $discount_details['discount_amount_type'];
									$discount_details[$key]['discount_amount'] = $discount_details['discount_amount'];
									$discount_details[$key]['total_discount'] = number_format($discount, 2, ".", "");
								}
							} elseif ($discount_details['discount_amount_type'] == 'Amount') {
								$product_dis_per = (100 * $discount_details['discount_amount']) / $order_total;
								foreach ($order_details as $key => $prd) {
									$discount = ($prd['price'] * $prd['qty']) * ($product_dis_per / 100);
									$discount_details[$key]['discount_amount_type'] = $discount_details['discount_amount_type'];
									$discount_details[$key]['discount_amount'] = $discount_details['discount_amount'];
									$discount_details[$key]['total_discount'] = number_format($discount, 2, ".", "");
								}
							}
						}
					}

					$res['status'] = "Success";
					$res['coupon_id'] = $couponRow['id'];
					$res['discount_type'] = "per_order";
					$res['is_future_discount'] = $is_future_discount;
					$res['discount_details'] = $discount_details;
				} else {
					$res['status'] = 'Fail';
					$res['message'] = 'Coupon Code Invalid';
					return $res;
				}
			} else {
				$order_total = 0;
				foreach ($order_details as $key => $prd) {
					$order_total += $prd['price'] * $prd['qty'];
				}
				if ($couponRow['amount_type'] == 'Percentage') {
					$discount = ($order_total) * ($couponRow['amount'] / 100);
				} elseif ($couponRow['amount_type'] == 'Amount') {
					if ($couponRow['amount'] > $order_total) {
						$couponRow['amount'] = $order_total;
					}
					$discount = ($couponRow['amount']);
				}

				$discount_details['discount_amount_type'] = $couponRow['amount_type'];
				$discount_details['discount_amount'] = $couponRow['amount'];
				$discount_details['total_discount'] = number_format($discount, 2, ".", "");

				//calculating order discount on per product
				if (count($discount_details) > 0) {
					if ($discount_details['discount_amount_type'] == 'Percentage') {
						foreach ($order_details as $key => $prd) {
							$discount = ($prd['price'] * $prd['qty']) * ($discount_details['discount_amount'] / 100);
							$discount_details[$key]['discount_amount_type'] = $discount_details['discount_amount_type'];
							$discount_details[$key]['discount_amount'] = $discount_details['discount_amount'];
							$discount_details[$key]['total_discount'] = number_format($discount, 2, ".", "");
						}
					} elseif ($discount_details['discount_amount_type'] == 'Amount') {
						$product_dis_per = (100 * $discount_details['discount_amount']) / $order_total;
						foreach ($order_details as $key => $prd) {
							$discount = ($prd['price'] * $prd['qty']) * ($product_dis_per / 100);
							$discount_details[$key]['discount_amount_type'] = $discount_details['discount_amount_type'];
							$discount_details[$key]['discount_amount'] = $discount_details['discount_amount'];
							$discount_details[$key]['total_discount'] = number_format($discount, 2, ".", "");
						}
					}
				}

				$res['status'] = "Success";
				$res['coupon_id'] = $couponRow['id'];
				$res['discount_type'] = "per_order";
				$res['discount_details'] = $discount_details;
			}
		} elseif ($couponRow['discount_on'] == 'per_product') {
			// Per Product Discount Code
			$is_future_discount = "No";
			$allowedProducts = json_decode($couponRow['discount_products'], true);
			$discount_details = array();
			if ($couponRow['set_condition'] == 'Yes') {
				// checking conditions
				$conditions = json_decode($couponRow['conditions'], true);
				//pre_print($conditions, false);
				if (count($conditions) > 0) {
					$prdFound = false;
					foreach ($order_details as $key => $prd) {
						if (in_array($key, $allowedProducts)) {
							$prdFound = true;
							foreach ($conditions as $cond) {
								if ($cond['type'] == 'Percentage') {
									$discount = ($prd['qty'] * $prd['price']) * ($cond['value'] / 100);
								} else {
									$discount = $cond['value'];
								}

								$discount = $discount > ($prd['qty'] * $prd['price']) ? ($prd['qty'] * $prd['price']) : $discount;

								if ($cond['operator'] == 'BETWEEN') {
									//echo "<br>".($prd['qty'] * $prd['price']) ."--". $cond['total_order'];
									if (($prd['qty'] * $prd['price']) >= $cond['total_order'] && ($prd['qty'] * $prd['price']) <= $cond['total_2_order']) {
										if ($cond['apply_to'] == 'CURRENT_PURCHASE') {
											$discount_details[$key]['product_id'] = $key;
											$discount_details[$key]['discount_amount_type'] = $cond['type'];
											$discount_details[$key]['discount_amount'] = $cond['value'];
											$discount_details[$key]['total_discount'] = number_format($discount, 2, ".", "");
										} elseif ($cond['apply_to'] == 'FUTURE_PURCHASE') {
											$is_future_discount = "Yes";
											$discount_details['discount_on'] = $couponRow['discount_on'];
											$discount_details['discount_amount_type'] = $cond['type'];
											$discount_details['discount_amount'] = $cond['value'];
										} elseif ($cond['apply_to'] == 'SHIPPING') {
											$discount_details[$key]['product_id'] = $key;
											$discount_details[$key]['is_on_shipping'] = "Yes";
											$discount_details[$key]['discount_amount_type'] = $cond['type'];
											$discount_details[$key]['discount_amount'] = $cond['value'];
										}
									} else {
										$prdFound = false;
									}
								} elseif ($cond['operator'] == 'GREATER_THAN') {
									if (($prd['qty'] * $prd['price']) > $cond['total_order']) {
										if ($cond['apply_to'] == 'CURRENT_PURCHASE') {
											$discount_details[$key]['product_id'] = $key;
											$discount_details[$key]['discount_amount_type'] = $cond['type'];
											$discount_details[$key]['discount_amount'] = $cond['value'];
											$discount_details[$key]['total_discount'] = number_format($discount, 2, ".", "");
										} elseif ($cond['apply_to'] == 'FUTURE_PURCHASE') {
											$is_future_discount = "Yes";
											$discount_details['discount_on'] = $couponRow['discount_on'];
											$discount_details['discount_amount_type'] = $cond['type'];
											$discount_details['discount_amount'] = $cond['value'];
										} elseif ($cond['apply_to'] == 'SHIPPING') {
											$discount_details[$key]['product_id'] = $key;
											$discount_details[$key]['is_on_shipping'] = "Yes";
											$discount_details[$key]['discount_amount_type'] = $cond['type'];
											$discount_details[$key]['discount_amount'] = $cond['value'];
										}
									} else {
										$prdFound = false;
									}
								}
							}
						}
					}

					if ($prdFound) {
						$res['status'] = "Success";
						$res['coupon_id'] = $couponRow['id'];
						$res['discount_type'] = "per_product";
						$res['is_future_discount'] = $is_future_discount;
						$res['discount_details'] = $discount_details;
					} else {
						$res['status'] = 'Fail';
						$res['message'] = 'Coupon Code Invalid';
					}
				} else {
					$res['status'] = 'Fail';
					$res['message'] = 'Coupon Code Invalid';
					return $res;
				}
			} else {
				$prdFound = false;
				foreach ($order_details as $key => $prd) {
					if (in_array($key, $allowedProducts)) {
						$prdFound = true;
						if ($couponRow['amount_type'] == 'Percentage') {
							$discount = ($prd['price'] * $prd['qty']) * ($couponRow['amount'] / 100);
						} elseif ($couponRow['amount_type'] == 'Amount') {
							if ($couponRow['amount'] > $prd['price']) {
								$couponRow['amount'] = $prd['price'];
							}
							$discount = ($couponRow['amount'] * $prd['qty']);
						}
						$discount_details[$key]['product_id'] = $key;
						$discount_details[$key]['discount_amount_type'] = $couponRow['amount_type'];
						$discount_details[$key]['discount_amount'] = $couponRow['amount'];
						$discount_details[$key]['total_discount'] = number_format($discount, 2, ".", "");
					}
				}
			}
			if ($prdFound) {
				$res['status'] = "Success";
				$res['coupon_id'] = $couponRow['id'];
				$res['discount_type'] = "per_product";
				$res['discount_details'] = $discount_details;
			} else {
				$res['status'] = 'Fail';
				$res['message'] = 'Coupon Code Invalid';
			}
		} elseif ($couponRow['discount_on'] == 'per_x_x') {
			// buy X get X Free
			$allowedProducts = array();
			$x_free_products = json_decode($couponRow['x_free_products'], true);
			//pre_print($x_free_products, false);
			foreach ($x_free_products as $key => $prd) {
				$allowedProducts[$key] = $key;
			}
			foreach ($order_details as $key => $prd) {
				if (in_array($key, $allowedProducts)) {
					if ($prd['qty'] >= $x_free_products[$key]['buy_qty']) {
						$free_qty = floor($prd['qty'] / $x_free_products[$key]['buy_qty']) * $x_free_products[$key]['free_qty'];
						$discount_details[$key]['product_id'] = $key;
						$discount_details[$key]['discount_amount_type'] = 'Product';
						$discount_details[$key]['buy_qty'] = $x_free_products[$key]['buy_qty'];
						$discount_details[$key]['free_qty'] = $x_free_products[$key]['free_qty'];
						$discount_details[$key]['total_discount'] = $free_qty;
					}
				}
			}
			//pre_print($discount_details);
			$res['status'] = "Success";
			$res['coupon_id'] = $couponRow['id'];
			$res['discount_type'] = "per_x_x";
			$res['discount_details'] = $discount_details;
		}
	} else {
		$res['status'] = 'Fail';
		$res['message'] = 'Invalid Coupon Code';
	}

	return $res;
}

function get_coupon_condition_text($coupon_code) {
	global $pdo;
	global $PRICE_TAG;
	if ($coupon_code != "") {
		$selSql = "select * from coupon where coupon_code=:coupon_code";
		$where = array(':coupon_code' => makeSafe($coupon_code));
		$couponRow = $pdo->selectOne($selSql, $where);
		//pre_print($couponRow);
		$conditions = array();
		$tAp = "";
		if ($couponRow['set_condition'] == 'Yes') {
			$conditions = json_decode($couponRow['conditions'], true);
			//pre_print($conditions);

			if (count($conditions) > 0) {

				foreach ($conditions as $sc) {
					$tAp .= "<p>";
					if ($sc['type'] == "Percentage") {
						$tAp .= $sc['value'] . "%";
					} elseif ($sc['type'] == "Amount") {
						$tAp .= $PRICE_TAG . $sc['value'];
					}

					$tAp .= " off on " . ucwords(str_replace('_', ' ', strtolower($sc['apply_to'])));

					$tAp .= " If order total is " . ($sc['operator'] == 'GREATER_THAN' ? '&gt;' : ($sc['operator'] == 'BETWEEN' ? '&lt;&gt;' : '')) . " " . $PRICE_TAG . $sc['total_order'];
					if ($sc['operator'] == 'BETWEEN') {
						$tAp .= " and " . $PRICE_TAG . $sc['total_2_order'] . "  ";
					}

					$tAp .= "</p>";
				}
			}
		} else {
			if ($couponRow['discount_on'] == 'per_order') {
				if ($couponRow['amount_type'] == 'Percentage') {
					$tAp .= "<p>";
					$tAp .= $couponRow['amount'] . "% off on order";
					$tAp .= "</p>";
				} else {
					$tAp .= "<p>";
					$tAp .= $PRICE_TAG . $couponRow['amount'] . " off on order";
					$tAp .= "</p>";
				}
			} elseif ($couponRow['discount_on'] == 'per_product') {
				$selected_products = json_decode($couponRow['discount_products'], true);
				if (count($selected_products) > 0) {
					foreach ($selected_products as $key => $prd) {
						if ($couponRow['amount_type'] == 'Percentage') {
							$tAp .= "<p>";
							$tAp .= $couponRow['amount'] . "% off on " . getname("product", $key, "title");
							$tAp .= "</p>";
						} else {
							$tAp .= "<p>";
							$tAp .= $PRICE_TAG . $couponRow['amount'] . " off on " . getname("product", $key, "title");
							$tAp .= "</p>";
						}
					}
				}
			} elseif ($couponRow['discount_on'] == 'per_x_x') {
				$x_free_products = json_decode($couponRow['x_free_products'], true);
				if (count($x_free_products) > 0) {
					foreach ($x_free_products as $key => $prd) {
						$tAp .= "<p>";
						$tAp .= "Buy " . $prd['buy_qty'] . " Get " . $prd['free_qty'] . " free on " . getname("product", $prd['product_id'], "title");
						$tAp .= "</p>";
					}
				}
			}
		}
	}
	return $tAp;
}

function get_personal_coupon_used($coupon_id, $customer_id) {
	global $pdo;
	if ($customer_id != "" && $customer_id > 0) {
		$checkRow = "select count(id)as total from orders where coupon_id>0 AND coupon_id=:coupon_id AND customer_id=:cid";
		$checkParams = array(":coupon_id" => makeSafe($coupon_id), ":cid" => makeSafe($customer_id));
		$checkRow = $pdo->selectOne($checkRow, $checkParams);
		return $checkRow['total'];
	}
}

function affect_order_inventory($products, $type, $sales_location, $otherparam = array()) {
	global $pdo;

	foreach ($products as $key => $product) {

		$checkProduct = "SELECT * FROM product WHERE is_inventory_product = 'Y' AND id = :product_id";
		$checkProduct = $pdo->selectOne($checkProduct, array(':product_id' => makeSafe($product['id'])));

		if ($checkProduct) {
			if ($checkProduct['type'] == 'Normal') {

				$variation_id = 0;
				if ($product['variation_id'] != '' && $product['variation_id'] > 0) {
					$variation_id = $product['variation_id'];
				}
				$current_stock = 0;
				$checkLocation = "SELECT * FROM product_inventory WHERE product_id = :product_id AND sales_location_id = :sales_location";
				$checkLocation = $pdo->selectOne($checkLocation, array(':product_id' => $product['id'], ':sales_location' => $sales_location));

				if ($checkLocation) {

					//pre_print($checkLocation);
					if ($type == 'In') {
						//$current_stock = $checkLocation['stock'] + $loc_qty;
						$upd_params = array(
							'stock' => 'msqlfunc_stock + ' . $product['qty'],
							'updated_at' => 'msqlfunc_NOW()',
						);
					} else if ($type == 'Out') {
						//$current_stock = $checkLocation['stock'] - $loc_qty;
						$upd_params = array(
							'stock' => 'msqlfunc_stock - ' . $product['qty'],
							'updated_at' => 'msqlfunc_NOW()',
						);
					}
					$upd_where = array(
						'clause' => 'product_id = :p_id AND inv_location_id=:inv_location AND variation_id=:variation_id',
						'params' => array(
							':p_id' => makeSafe($checkLocation['product_id']),
							':inv_location' => makeSafe($checkLocation['inv_location_id']),
							':variation_id' => makeSafe($variation_id),
						),
					);
					$pdo->update("inventory_location_stock", $upd_params, $upd_where);

					// inserting inventory history
					$stockSql = "select stock from inventory_location_stock where product_id=:p_id and inv_location_id=:inv_id and variation_id=:variation_id";
					$params = array(':p_id' => $checkLocation['product_id'], ':inv_id' => $checkLocation['inv_location_id'], ":variation_id" => $variation_id);
					$stockRow = $pdo->selectOne($stockSql, $params);
					$current_stock = $stockRow['stock'] != '' ? $stockRow['stock'] : 0;

					$country = getname('inventory_location', $checkLocation['inv_location_id'], "country", 'id');
					$inv_his = array(
						'product_id' => $product['id'],
						'variation_id' => $variation_id,
						'type' => $type,
						'qty' => $product['qty'],
						'stock' => $current_stock,
						'inv_location_id' => $checkLocation['inv_location_id'],
						'sales_location_id' => $checkLocation['sales_location_id'],
						'country' => $country,
						'created_at' => 'msqlfunc_NOW()',
					);
					if (isset($_SERVER['REMOTE_ADDR'])) {
						$inv_his['ip_address'] = $_SERVER['REMOTE_ADDR'];
					}
					if (isset($otherparam['is_new'])) {
						$inv_his['is_new'] = $otherparam['is_new'];
					}
					if (isset($otherparam['admin_id'])) {
						$inv_his['admin_id'] = $otherparam['admin_id'];
					}
					if (isset($otherparam['order_id'])) {
						$inv_his['order_id'] = $otherparam['order_id'];
					}
					if (isset($otherparam['note'])) {
						$inv_his['note'] = makeSafe($otherparam['note']);
					}
					$pdo->insert('inventory_history', $inv_his);
				}
			} elseif ($checkProduct['type'] == 'Kit') {
				$selkitSql = "select * from product_kit where kit_id=:kit_id";
				$params = array(':kit_id' => makeSafe($product['id']));
				$kitRows = $pdo->select($selkitSql, $params);
				if ($kitRows) {
					//kit code here is pending
					foreach ($kitRows as $kRow) {
						$checkLocation = "SELECT * FROM product_inventory WHERE product_id = :product_id AND sales_location_id = :sales_location";
						$checkLocation = $pdo->selectOne($checkLocation, array(':product_id' => $kRow['product_id'], ':sales_location' => $sales_location));
						if ($checkLocation) {
							if ($type == 'In') {
								//$current_stock = $checkLocation['stock'] + $loc_qty;
								$upd_params = array(
									'stock' => 'msqlfunc_stock + ' . ($product['qty'] * $kRow['qty']),
									'updated_at' => 'msqlfunc_NOW()',
								);
							} else if ($type == 'Out') {
								//$current_stock = $checkLocation['stock'] - $loc_qty;
								$upd_params = array(
									'stock' => 'msqlfunc_stock - ' . ($product['qty'] * $kRow['qty']),
									'updated_at' => 'msqlfunc_NOW()',
								);
							}
							$upd_where = array(
								'clause' => 'product_id = :p_id AND inv_location_id=:inv_location',
								'params' => array(
									':p_id' => makeSafe($checkLocation['product_id']),
									':inv_location' => makeSafe($checkLocation['inv_location_id']),
								),
							);
							$pdo->update("inventory_location_stock", $upd_params, $upd_where);

							// inserting inventory history
							$stockSql = "select stock from inventory_location_stock where product_id=:p_id and inv_location_id=:inv_id";
							$params = array(':p_id' => $checkLocation['product_id'], ':inv_id' => $checkLocation['inv_location_id']);
							$stockRow = $pdo->selectOne($stockSql, $params);
							$current_stock = $stockRow['stock'];

							$country = getname('inventory_location', $checkLocation['inv_location_id'], "country", 'id');
							$inv_his = array(
								'product_id' => $kRow['product_id'],
								'type' => $type,
								'qty' => ($product['qty'] * $kRow['qty']),
								'stock' => $current_stock,
								'inv_location_id' => $checkLocation['inv_location_id'],
								'sales_location_id' => $checkLocation['sales_location_id'],
								'country' => $country,
								'created_at' => 'msqlfunc_NOW()',
							);
							if (isset($_SERVER['REMOTE_ADDR'])) {
								$inv_his['ip_address'] = $_SERVER['REMOTE_ADDR'];
							}
							if (isset($otherparam['is_new'])) {
								$inv_his['is_new'] = $otherparam['is_new'];
							}
							if (isset($otherparam['admin_id'])) {
								$inv_his['admin_id'] = $otherparam['admin_id'];
							}
							if (isset($otherparam['order_id'])) {
								$inv_his['order_id'] = $otherparam['order_id'];
							}
							if (isset($otherparam['note'])) {
								$inv_his['note'] = makeSafe($otherparam['note']);
							}
							$pdo->insert('inventory_history', $inv_his);
						}
					}
				}
			}
		}
	}
}

function getInventorySalesLocation($location, $country) {
	global $pdo;
	$selSql = "select * from sales_locations";
	$salesRows = $pdo->select($selSql);
	if ($salesRows) {
		foreach ($salesRows as $row) {
			if (strtoupper($row['country']) == strtoupper($country)) {
				return $row['id'];
			}
		}
	}
}

function shippingAPI($Shipping_info) {

	global $pdo;

	// echo "<pre>";
	// print_r($Shipping_info);
	// exit;

	$devurl = "https://secure.shippingapis.com/ShippingAPI.dll";
	$puburl = " https://secure.shippingapis.com/ShippingAPI.dll";

	$service = "ExpressMailLabel";
	$userid = "605ELEVA6810";

	if ($Shipping_info['address2'] != "") {
		$xml = rawurlencode("<?xml version='1.0' encoding='UTF-8' ?>
		<ExpressMailLabelRequest USERID='605ELEVA6810'>
		    <Option />
		    <Revision>2</Revision>
		    <EMCAAccount />
		    <EMCAPassword />
		    <ImageParameters />
		    <FromFirstName>Elevacity</FromFirstName>
		    <FromLastName>LLC</FromLastName>
		    <FromFirm/>
		    <FromAddress1>3207 Skylane</FromAddress1>
		    <FromAddress2>Dr #110</FromAddress2>
		    <FromCity>Carrollton</FromCity>
		    <FromState>Texas</FromState>
		    <FromZip5>75006</FromZip5>
		    <FromZip4/>
		    <FromPhone>1234567890</FromPhone>
		    <ToFirstName>" . $Shipping_info['fname'] . "</ToFirstName>
		    <ToLastName>" . $Shipping_info['lname'] . "</ToLastName>
		    <ToFirm>XYZ Corporation</ToFirm>
		    <ToAddress1>" . $Shipping_info['address1'] . "</ToAddress1>
		    <ToAddress2>" . $Shipping_info['address2'] . "</ToAddress2>
		    <ToCity>" . $Shipping_info['city'] . "</ToCity>
		    <ToState>" . $Shipping_info['state'] . " </ToState>
		    <ToZip5>" . $Shipping_info['zip'] . "</ToZip5>
		    <ToZip4 />
		    <ToPhone />
		    <WeightInOunces>105</WeightInOunces>
		    <FlatRate/>
		    <SundayHolidayDelivery/>
		    <StandardizeAddress/>
		    <WaiverOfSignature/>
		    <NoHoliday/>
		    <NoWeekend/>
		    <SeparateReceiptPage/>
		    <POZipCode>20212</POZipCode>
		    <FacilityType>DDU</FacilityType>
		    <ImageType>NONE</ImageType>
		    <LabelDate>" . DATE('Y-m-d') . "</LabelDate>
		    <CustomerRefNo/>
		    <HoldForManifest/>
		    <CommercialPrice>false</CommercialPrice>
		    <InsuredAmount>25.00</InsuredAmount>
		    <Container>NONRECTANGULAR</Container>
		    <Size>LARGE</Size>
		    <Width>7</Width>
		    <Length>10.5</Length>
		    <Height>5</Height>
		    <Girth>10</Girth>
		</ExpressMailLabelRequest>
	    ");
	} else {
		$xml = rawurlencode("<?xml version='1.0' encoding='UTF-8' ?>
		<ExpressMailLabelRequest USERID='605ELEVA6810'>
		    <Option />
		    <Revision>2</Revision>
		    <EMCAAccount />
		    <EMCAPassword />
		    <ImageParameters />
		    <FromFirstName>Elevacity</FromFirstName>
		    <FromLastName>LLC</FromLastName>
		    <FromFirm/>
		    <FromAddress1>3207 Skylane</FromAddress1>
		    <FromAddress2>Dr #110</FromAddress2>
		    <FromCity>Carrollton</FromCity>
		    <FromState>Texas</FromState>
		    <FromZip5>75006</FromZip5>
		    <FromZip4/>
		    <FromPhone>1234567890</FromPhone>
		    <ToFirstName>" . $Shipping_info['fname'] . "</ToFirstName>
		    <ToLastName>" . $Shipping_info['lname'] . "</ToLastName>
		    <ToFirm>XYZ Corporation</ToFirm>
		    <ToAddress1></ToAddress1>
		    <ToAddress2>" . $Shipping_info['address1'] . "</ToAddress2>
		    <ToCity>" . $Shipping_info['city'] . "</ToCity>
		    <ToState>" . $Shipping_info['state'] . " </ToState>
		    <ToZip5>" . $Shipping_info['zip'] . "</ToZip5>
		    <ToZip4 />
		    <ToPhone />
		    <WeightInOunces>105</WeightInOunces>
		    <FlatRate/>
		    <SundayHolidayDelivery/>
		    <StandardizeAddress/>
		    <WaiverOfSignature/>
		    <NoHoliday/>
		    <NoWeekend/>
		    <SeparateReceiptPage/>
		    <POZipCode>20212</POZipCode>
		    <FacilityType>DDU</FacilityType>
		    <ImageType>PDF</ImageType>
		    <LabelDate>" . DATE('Y-m-d') . "</LabelDate>
		    <CustomerRefNo/>
		    <HoldForManifest/>
		    <CommercialPrice>false</CommercialPrice>
		    <InsuredAmount>25.00</InsuredAmount>
		    <Container>NONRECTANGULAR</Container>
		    <Size>LARGE</Size>
		    <Width>7</Width>
		    <Length>10.5</Length>
		    <Height>5</Height>
		    <Girth>10</Girth>
		</ExpressMailLabelRequest>
	    ");
	}

	$request = $devurl . "?API=" . $service . "&xml=" . $xml;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $request);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);

	$xml = simplexml_load_string($response);
	$json = json_encode($xml);
	$params = json_decode($json, TRUE);

	/* echo "<pre>";
		    print_r($params);
		    exit;
	*/
	if ($params['EMConfirmationNumber'] != "") {
		$upsp_api = "Y";
	} else {
		$upsp_api = "N";
	}

	$update = array(
		'tracking_number' => $params['EMConfirmationNumber'],
		'usps_response' => $json,
		'usps_api' => $upsp_api,
		'updated_at' => 'msqlfunc_NOW()',
	);
	$updateWhere = array(
		'clause' => 'id=:id',
		'params' => array(':id' => $Shipping_info['order_id']),
	);
	$pdo->update("orders", $update, $updateWhere);

	return $params;
}

function checkAddress($Shipping_info) {

	$devurl = "http://production.shippingapis.com/ShippingAPI.dll";
	$puburl = "http://production.shippingapis.com/ShippingAPI.dll";

	$service = "Verify";
	$userid = "605ELEVA6810";

	if ($Shipping_info['address2'] != "") {
		$xml = rawurlencode("<?xml version='1.0' encoding='UTF-8' ?>
		<AddressValidateRequest USERID='605ELEVA6810'>
			<Address>
				<Address1>" . $Shipping_info['address1'] . "</Address1>
				<Address2>" . $Shipping_info['address2'] . "</Address2>
				<City>" . $Shipping_info['city'] . "</City>
				<State>" . $Shipping_info['state'] . "</State>
				<Zip5>" . $Shipping_info['zip'] . "</Zip5>
				<Zip4></Zip4>
			</Address>
		</AddressValidateRequest>
	    ");
	} else {
		$xml = rawurlencode("<?xml version='1.0' encoding='UTF-8' ?>
		<AddressValidateRequest USERID='605ELEVA6810'>
			<Address>
				<Address1 />
				<Address2>" . $Shipping_info['address1'] . "</Address2>
				<City>" . $Shipping_info['city'] . "</City>
				<State>" . $Shipping_info['state'] . "</State>
				<Zip5>" . $Shipping_info['zip'] . "</Zip5>
				<Zip4></Zip4>
			</Address>
		</AddressValidateRequest>
	    ");
	}

	$request = $devurl . "?API=" . $service . "&xml=" . $xml;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $request);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);

	$xml = simplexml_load_string($response);
	$json = json_encode($xml);
	$params = json_decode($json, TRUE);

	return $params;
}

function get_week_pay_period($date = "") {
	//as we are running previous days commission we are selecting previous day
	if ($date == "") {
		$date = date('Y-m-d', strtotime("-1 day"));
	}
	//echo date('l', strtotime($date));
	if (date('l', strtotime($date)) == 'Tuesday') {
		$pay_period = $date;
	} else {
		$pay_period = date('Y-m-d', strtotime($date . " next Tuesday"));
	}
	//$pay_period = '2015-07-31';
	return $pay_period;
}

/*
 * For Creating User in Binary Tree
 */

function dual_team_last_node($sponsor_id, $place_side = 'Left') {
	global $pdo;
	$selSql = "select * from binary_tree where user_id=:user_id ";
	$params = array(":user_id" => $sponsor_id);
	$row = $pdo->selectOne($selSql, $params);
	$Response = array();
	if ($row) {
		if ($place_side == 'Left') {
			if ($row['left_node'] > 0) {
				return dual_team_last_node($row['left_node'], $place_side);
			} elseif ($row['left_node'] == 0) {
				$Response['partner_id'] = $row['user_id'];
				$Response['level'] = $row['level'];
				return $Response;
			}
		} elseif ($place_side == 'Right') {
			if ($row['right_node'] > 0) {
				return dual_team_last_node($row['right_node'], $place_side);
			} elseif ($row['right_node'] == 0) {
				$Response['partner_id'] = $row['user_id'];
				$Response['level'] = $row['level'];
				return $Response;
			}
		}
	}
}

function dual_team_auto_place($sponsor_id, $place_user_id, $placed_by) {
	//pre_print(func_get_args(),false);
	global $pdo;

	// checking if new user is already placed or not
	$selSql = "select * from binary_tree where user_id=:user_id AND is_placed='Y'";
	$params = array(":user_id" => $place_user_id);
	$checkRow = $pdo->selectOne($selSql, $params);
	if ($checkRow) {
		return false;
	}

	$settingSql = "select dual_team_setting,dual_team_alternate_side from customer where id=:id";
	$settingParam = array(":id" => $sponsor_id);
	$settingRow = $pdo->selectOne($settingSql, $settingParam);

	if (!$settingRow) {
		return false;
	} else {
		$setting = $settingRow['dual_team_setting'];
	}
	if ($setting != 'Left' && $setting != 'Right' && $setting != 'Auto' && $setting != 'Alternate') {
		$setting = 'Auto';
	}
	if ($setting == 'Alternate') {
		$setting = "Left";
		if ($settingRow['dual_team_alternate_side'] == 'Left') {
			$setting = "Right";
		}
	}
	$parent_id = 0;
	if ($setting == 'Auto') {
		$leftRes = dual_team_last_node($sponsor_id, 'Left');
		$rightRes = dual_team_last_node($sponsor_id, 'Right');
		//pre_print($leftRes,false);
		//pre_print($rightRes,false);
		if (count($leftRes) > 0 && count($rightRes) > 0) {
			if ($leftRes['level'] > $rightRes['level']) {
				$parent_id = $rightRes['partner_id'];
				$place_side = "Right";
			} else {
				$parent_id = $leftRes['partner_id'];
				$place_side = "Left";
			}
		}
	} elseif ($setting == 'Left') {
		$leftRes = dual_team_last_node($sponsor_id, 'Left');
		if (count($leftRes) > 0) {
			$parent_id = $leftRes['partner_id'];
			$place_side = "Left";
		}
	} elseif ($setting == 'Right') {
		$rightRes = dual_team_last_node($sponsor_id, 'Right');
		if (count($rightRes) > 0) {
			$parent_id = $rightRes['partner_id'];
			$place_side = "Right";
		}
	}
	$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
	$parentRow = $pdo->selectOne($sql, array(':user_id' => $parent_id));
	//pre_print($parentRow);
	//pre_print($parentRow, false);
	$sql = "SELECT * FROM binary_tree WHERE user_id = :user_id";
	$placeRow = $pdo->selectOne($sql, array(':user_id' => $place_user_id));
	//pre_print($placeRow, false);

	$update_params = array();
	$parent_update_params = array();
	if (strtolower($place_side) == "left") {
		$update_params['place_side'] = "Left";
		$parent_update_params['left_node'] = $placeRow['user_id'];
	} else if (strtolower($place_side) == "right") {
		$update_params['place_side'] = "Right";
		$parent_update_params['right_node'] = $placeRow['user_id'];
	}
	$parent_update_params['updated_at'] = 'msqlfunc_NOW()';
	$parent_update_where = array(
		'clause' => 'id=:id',
		'params' => array(':id' => makesafe($parentRow['id'])),
	);
	$pdo->update('binary_tree', $parent_update_params, $parent_update_where);

	$level = count(explode(",", trim($parentRow['upline_placements'] . $parentRow['user_id'], ",")));

	$update_params['parent_node'] = $parentRow['user_id'];
	$update_params['upline_placements'] = (',' . ltrim($parentRow['upline_placements'], ',') . $parentRow['user_id'] . ',');
	$update_params['level'] = $level;
	$update_params['is_placed'] = 'Y';
	$update_params['updated_at'] = 'msqlfunc_NOW()';

	$update_where = array(
		'clause' => 'id=:id',
		'params' => array(':id' => makesafe($placeRow['id'])),
	);
	$pdo->update('binary_tree', $update_params, $update_where);
	//pre_print($parentRow,false);
	$ins_params = array(
		'user_id' => $placeRow['user_id'],
		'placed_under_id' => $parentRow['user_id'],
		'place_side' => ucfirst($place_side),
		'placed_by' => $placed_by['placed_by'],
		'placed_by_id' => $placed_by['placed_by_id'],
		'created_at' => 'msqlfunc_NOW()',
	);
	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ins_params['ip_address'] = $_SERVER['REMOTE_ADDR'];
	}
	$pdo->insert("binary_tree_placement_history", $ins_params);

	//updating alternate dualteam setting option
	if ($settingRow['dual_team_setting'] == 'Alternate') {
		$update_params = array("dual_team_alternate_side" => $setting);
		$update_where = array(
			'clause' => 'id=:id',
			'params' => array(':id' => makesafe($sponsor_id)),
		);
		$pdo->update('customer', $update_params, $update_where);
	}
	update_down_placement($placeRow['user_id']);
}

function place_in_unilevel($partner_id, $parent_id, $place_id, $placed_by) {
	global $pdo;

	//selecting parent partner info.
	$parentSql = "select * from binary_tree where user_id=:parent_id ";
	$parentParams = array(":parent_id" => $parent_id);
	$parentRow = $pdo->selectOne($parentSql, $parentParams);

	if ($parentRow) {

		//selecting placing user info.
		$placeSql = "select * from binary_tree where user_id=:place_id and upline_unilevels like :partner_id";
		$placeParams = array(":place_id" => $place_id, ":partner_id" => "%," . $partner_id . ",%");
		$placeRow = $pdo->selectOne($placeSql, $placeParams);

		if ($placeRow) {
			//pre_print($parentRow,false);
			//pre_print($placeRow);
			//updating placement details for unilevels
			$upline_uni = $parentRow['upline_unilevels'] . $parentRow['user_id'] . ",";
			$uni_level = $parentRow['uni_level'] + 1;
			$uni_sponsor_id = $parentRow['user_id'];

			$updateArr = array(
				'uni_level' => $uni_level,
				'uni_sponsor_id' => $uni_sponsor_id,
				'unilevel_placed' => 'Y',
				'upline_unilevels' => $upline_uni,
				'updated_at' => 'msqlfunc_NOW()',
			);
			$where = array("clause" => "id=:id", "params" => array(":id" => $placeRow['id']));
			$pdo->update("binary_tree", $updateArr, $where);

			//inserting history of placement
			$insSql = array(
				'user_id' => $placeRow['user_id'],
				'placed_under_id' => $uni_sponsor_id,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'created_at' => 'msqlfunc_NOW()',
			);
			if ($placed_by['placed_by'] != '') {
				$insSql['placed_by'] = $placed_by['placed_by'];
			}
			if ($placed_by['placed_by_id'] != '') {
				$insSql['placed_by_id'] = $placed_by['placed_by_id'];
			}
			if ($placed_by['auto_placed'] != '') {
				$insSql['auto_placed'] = $placed_by['auto_placed'];
			}
			$pdo->insert("unilevel_placement_history", $insSql);

			//updating downline organization with new upline unilevel sponsors.
			$updateSql = "UPDATE binary_tree
        SET
          upline_unilevels=REPLACE(upline_unilevels,'" . ($placeRow['upline_unilevels'] . $placeRow['user_id'] . ",") . "','" . ($upline_uni . $placeRow['user_id'] . ",") . "'),
          uni_level=(uni_level-" . $placeRow['uni_level'] . ")+" . $uni_level . "
        WHERE upline_unilevels like('%," . $placeRow['user_id'] . ",%')";

			$stmt = $pdo->dbh->prepare($updateSql);
			$stmt->execute();
		}
	}
}

//Code End For Creating User

function get_website_id() {
	global $pdo;
	$web_id = "W" . rand(1000000, 9999999);
	$sql = "SELECT website_id FROM website_subscriptions WHERE website_id ='" . $web_id . "'";
	$res = $pdo->select($sql);
	if (count($res) > 0) {
		return get_website_id();
	} else {
		return $web_id;
	}
}

function dateDifference($date_1, $differenceFormat) {
	$datetime1 = date_create($date_1);
	$datetime2 = date_create('today');

	$interval = date_diff($datetime1, $datetime2);

	return $interval->format($differenceFormat);
}

function convertTimeZoneTwoCharacter($time = null, $from = "ET", $to = "ET") {
	$twoCharTimeZone = array("ET", "MT", "CT", "PT");
	$threeCharTimeZone = array("EST", "MST", "CST", "PST");
	$from = str_replace($twoCharTimeZone, $threeCharTimeZone, $from);
	$to = str_replace($twoCharTimeZone, $threeCharTimeZone, $to);
	if (!isset($time)) {
		$time = date("Y-m-d h:i");
	}
	$d = new DateTime($time, new DateTimeZone($from));
	$d->setTimeZone(new DateTimeZone($to));
	return $d->format('H:i');
}

function csvToArray($filename) {
	$rows = array();
	$headers = array();
	if (file_exists($filename) && is_readable($filename)) {
		$handle = fopen($filename, 'r');
		while (!feof($handle)) {
			$row = fgetcsv($handle, 10240, ';', '"');
			if (empty($headers)) {
				$headers = $row;
			} else if (is_array($row)) {
				array_splice($row, count($headers));
				//$rows[] = array_combine($headers, $row);
				$row[0] = preg_replace('!\s+!', ' ', $row[0]);

				$haystack = $row[0];
				$needle = ",";
				if (strpos($haystack, $needle) !== false) {
					$rows[] = explode(",", $row[0]);
				} else if (strpos($haystack, ";") !== false) {
					$rows[] = explode(";", $row[0]);
				} else {
					$rows[] = explode(" ", $row[0]);
				}
			}
		}
		fclose($handle);
	} else {
		throw new Exception($filename . ' doesn`t exist or is not readable.');
	}
	return $rows;
}

// FUNCTION FOR COPY CAMPAIGN TITLE
function campaign_title($copy_title) {
	Global $pdo;

	$title_Sql = "SELECT * FROM campaign WHERE title = '" . $copy_title . "'";
	$title_res = $pdo->selectOne($title_Sql);
	//pre_print($title_res);
	if (count($title_res) > 0) {

		if (preg_match('/copy/', $title_res['title'])) {
			$str_string = $title_res['title'];
			$length = strlen($str_string);
			$index = $length - 2;
			//echo $index;exit;
			$i = $title_res['title'][$index];

			$copy_title = substr($str_string, 0, -9);
			//echo $copy_title;exit;
			$i = $i + 1;
			$copy_title = $copy_title . ' (copy ' . $i . ')';
			//echo $copy_title;exit;
			$title_Sql = "SELECT * FROM campaign WHERE title = '" . $copy_title . "'";
			$title_res = $pdo->selectOne($title_Sql);
			if ($title_res) {
				return campaign_title($title_res['title']);
			} else {
				return $copy_title;
			}
		} else {
			$str_string = $title_res['title'] . " (copy 1)";

			$title_Sql = "SELECT * FROM campaign WHERE title = '" . $str_string . "'";
			$title_res = $pdo->selectOne($title_Sql);
			if ($title_res) {
				return campaign_title($title_res['title']);
			} else {
				return $str_string;
			}
		}
	}
}

function get_lead_id() {
	global $pdo;
	$lead_id = rand(1000000, 9999999);
	$sql = "SELECT count(lead_id) as total FROM leads WHERE lead_id =$lead_id";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_lead_id();
	} else {
		return $lead_id;
	}
}function get_imp_lead_id() {
	global $pdo;
	$lead_id = rand(100000, 999999);
	$sql = "SELECT count(display_id) as total FROM important_lead WHERE display_id =$lead_id";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_imp_lead_id();
	} else {
		return $lead_id;
	}
}
/**
 * [activity_feed description]
 * @param  integer $company_id    [activity feed copmany id]
 * @param  [type]  $sponsor_id    [reciver id which shows this feed]
 * @param  [type]  $sponsor_type  [receiver type]
 * @param  [type]  $entity_id     [destination id which detail you want to access]
 * @param  [type]  $entity_type   [destination type]
 * @param  [type]  $entity_action [feed heading]
 * @param  [type]  $fname         [description]
 * @param  [type]  $lname         [description]
 * @param  string  $description   [description]
 * @return [type]                 [description]
 */
function activity_feed($company_id, $sponsor_id, $sponsor_type, $entity_id, $entity_type, $entity_action, $fname = '', $lname = '', $description = '') {
	global $pdo;
	$insert_params = array(
		'company_id' => $company_id,
		'user_id' => $sponsor_id,
		'user_type' => $sponsor_type,
		'entity_id' => $entity_id,
		'entity_type' => $entity_type,
		'entity_action' => $entity_action,
		'description' => $description,
		'note_admin_name' => $fname . "" . $lname,
		'ip_address' => $_SERVER['REMOTE_ADDR'],
		'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'changed_at' => 'msqlfunc_NOW()',
	);
	$activity_id = $pdo->insert("activity_feed", $insert_params);
	return $activity_id;
}

function imageToBase64($path) {
	if ($path != "" && file_exists($path)) {
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}
	return "";
}

function prdimageToBase64($path, $company_id = 3) {
	GLOBAL $SITE_SETTINGS;

	if ($path != "" && remote_file_exists($path, $company_id)) {
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$GLOBAL_DIR_HOST = $SITE_SETTINGS[$company_id]['HOST'];
		$path = $GLOBAL_DIR_HOST . $path;
		$data = file_get_contents($path);
		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}
	return "";
}
function _mime_content_type($file) {
	return mime_content_type($file);
}
function _mime_content_type_new($file) {
	$idx = explode('.', $file);
	$count_explode = count($idx);
	$idx = strtolower($idx[$count_explode - 1]);

	$mimet = array(
		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',

		// images
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',

		// archives
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',

		// audio/video
		'mp3' => 'audio/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',

		// adobe
		'pdf' => 'application/pdf',
		'psd' => 'image/vnd.adobe.photoshop',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',

		// ms office
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'docx' => 'application/msword',
		'xlsx' => 'application/vnd.ms-excel',
		'pptx' => 'application/vnd.ms-powerpoint',

		// open office
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);

	if (isset($mimet[$idx])) {
		return $mimet[$idx];
	} else {
		return 'application/octet-stream';
	}
}

function get_group_display_id() {
	global $pdo;
	$code = "G" . rand(1000000, 9999999);
	$sql = "SELECT id FROM customer WHERE type='Group' AND rep_id =:rep_id";
	$res = $pdo->select($sql, array(":rep_id" => $code));
	if (count($res) > 0) {
		return get_group_display_id();
	} else {
		return $code;
	}
}
function get_dependant_display_id() {
	global $pdo;
	$dependat_id = rand(1000000, 9999999);
	$sql = "SELECT count(*) as total FROM customer_dependent WHERE display_id ='D" . $dependat_id . "' OR display_id ='" . $dependat_id . "'";
	$res = $pdo->selectOne($sql);

	if ($res['total'] > 0) {
		return get_dependant_display_id();
	} else {
		return "D" . $dependat_id;
	}
}

// Report Schedule Code Start

function daily_schedule($report_id) {
	global $pdo;
	$res = $pdo->select("SELECT r.id as rec_id,r.name,r.recepient_email,r.report_id as rep_id,rs.schedule_type,rs.time,rs.con_date_time as con_time,rs.id as sche_id,rs.timezone
              FROM `recepient` as r
              JOIN recepient_schedule as rs ON(r.id = rs.recepient_id)
              WHERE r.report_id=:id AND r.is_deleted='N' AND rs.is_deleted='N' AND rs.schedule_type='Daily' AND rs.con_date_time<=CURTIME() AND rs.con_date_time >= CURTIME() - INTERVAL 10 MINUTE", array(":id" => $report_id));
	return $res;
}

function monday_friday_schedule($report_id) {
	global $pdo;
	$current_day = date("N");
	$days_to_friday = 5 - $current_day;
	$days_from_monday = $current_day - 1;
	$monday = date("Y-m-d", strtotime("- {$days_from_monday} Days"));
	$friday = date("Y-m-d", strtotime("+ {$days_to_friday} Days"));

	$res = $pdo->select("SELECT r.id as rec_id,r.name,r.recepient_email,r.report_id as rep_id,rs.schedule_type,rs.time,rs.con_date_time as con_time,rs.id as sche_id,rs.timezone
              FROM `recepient` as r
              JOIN recepient_schedule as rs ON(r.id = rs.recepient_id)
              WHERE r.report_id=:id AND r.is_deleted='N' AND rs.is_deleted='N' AND rs.schedule_type='Mon-Friday' AND
              NOW()  >= '" . $monday . "' AND NOW() <= '" . $friday . "' AND rs.con_date_time<=CURTIME() AND rs.con_date_time >= CURTIME() - INTERVAL 10 MINUTE", array(":id" => $report_id));
	return $res;
}

function weekly_schedule($report_id) {
	global $pdo;
	$res = $pdo->select("SELECT r.id as rec_id,r.name,r.recepient_email,r.report_id as rep_id,rs.schedule_type,rs.time,rs.con_date_time as con_time,rs.id as sche_id,rs.timezone
              FROM `recepient` as r
              JOIN recepient_schedule as rs ON(r.id = rs.recepient_id)
              WHERE r.report_id=:id AND  rs.day_of_week = (WEEKDAY(CURDATE())+1) AND r.is_deleted='N' AND rs.is_deleted='N' AND rs.schedule_type='Weekly' AND  rs.con_date_time<=CURTIME() AND rs.con_date_time >= CURTIME() - INTERVAL 10 MINUTE", array(":id" => $report_id));
	return $res;
}
function monthly_schedule($report_id) {
	global $pdo;
	$res = $pdo->select("SELECT r.id as rec_id,r.name,r.recepient_email,r.report_id as rep_id,rs.schedule_type,rs.time,rs.con_date_time as con_time,rs.id as sche_id,rs.timezone
              FROM `recepient` as r
              JOIN recepient_schedule as rs ON(r.id = rs.recepient_id)
              WHERE r.report_id=:id AND r.is_deleted='N' AND rs.is_deleted='N' AND rs.schedule_type='Monthly' AND rs.day_of_month=DAYOFMONTH(NOW()) AND rs.con_date_time<=CURTIME() AND rs.con_date_time >= CURTIME() - INTERVAL 10 MINUTE", array(":id" => $report_id));
	return $res;
}
// Report Schedule Code End
//-----------------------------------------------------------------------------
// For Sales Report Functions Start

function all_sales_today($date = '') {
	global $pdo;

	if ($date != "") {

		$today_incr = " AND DATE(created_at) >= '" . date("Y-m-d", strtotime($date)) . "' AND DATE(created_at) <='" . date("Y-m-d", strtotime($date)) . "'";
		$pre_day_incr = " AND DATE(created_at) = '" . date('Y-m-d', strtotime('-1 day', strtotime($date))) . "'";
	} else {
		$today_incr = " AND DATE(created_at) >= '" . DATE('Y-m-d') . "' AND DATE(created_at) <='" . DATE('Y-m-d') . "'";
		$pre_day_incr = " AND DATE(created_at) = CURDATE()-1";
	}

	$day_sql_orders = "SELECT sum(grand_total) as today_sales
          FROM orders
          WHERE id>0 AND status='Payment Approved'" . $today_incr;
	$day_res_orders = $pdo->selectOne($day_sql_orders);

	// QUERY FOR Yeasterda'S SALES

	$pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_sales
          FROM orders
          WHERE id>0 AND status='Payment Approved'" . $pre_day_incr;
	$pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

	// Calculate today percentage
	$today_per_value = $day_res_orders['today_sales'] - $pre_day_res_orders['yesterday_sales'];

	if ($pre_day_res_orders['yesterday_sales'] > 0) {
		$today_per = ($today_per_value / $pre_day_res_orders['yesterday_sales']) * 100;
	} else {
		// $today_per = $day_res_orders['today_sales'] * 100;
		$today_per = $day_res_orders['today_sales'];
	}
	$res['today_sales'] = $day_res_orders['today_sales'];
	$res['yesterday_sales'] = $pre_day_res_orders['yesterday_sales'];
	$res['today_per'] = $today_per;

	/*if($time!=""){
		    $current_time = $time;
		    $current_date = DATE('Y-m-d'). ' ' . $current_time;
		    $yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date." -24 HOUR"));
		    $yesterday_date  = DATE('Y-m-d',(strtotime ('-1 day',strtotime($current_date)))). ' ' . $current_time; ;
		    $previousday_day = DATE('Y-m-d',(strtotime ('-1 day',strtotime($yesterday_date)))). ' ' . $current_time;

		    $day_sql_orders = "SELECT sum(grand_total) as today_sales
		    FROM orders
		    WHERE id>0 AND status='Payment Approved' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "'";
		    $day_res_orders = $pdo->selectOne($day_sql_orders);

		    // QUERY FOR Yeasterda'S SALES
		    $pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_sales
		    FROM orders
		    WHERE id>0 AND status='Payment Approved' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($yesterday_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($previousday_day) . "'";
		    $pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

	*/

	/*$day_sql_orders = "SELECT sum(grand_total) as today_sales
		    FROM orders
		    WHERE id>0 AND status='Payment Approved' AND DATE(created_at) >= '" . date('Y-m-d') . "' AND DATE(created_at) <='" . date('Y-m-d') . "'";
		    $day_res_orders = $pdo->selectOne($day_sql_orders);

		    // QUERY FOR Yeasterda'S SALES
		    $pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_sales "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' AND DATE(created_at) = CURDATE()-1";
	*/

	// }

	return $res;
}

function all_sales_week() {
	global $pdo;

	$dayofweek = date('w', strtotime(date('m/d/Y')));
	if ($dayofweek == 7) {
		$dayofweek = 1;
	} else {
		$dayofweek = $dayofweek + 1;
	}

	$fromdate = date("m/d/Y", strtotime("-$dayofweek days"));
	$todate = date('m/d/Y');

	$from_pre_dayofweek = $dayofweek + 8;
	$to_pre_dayofweek = $dayofweek + 1;

	$pre_fromdate = date("m/d/Y", strtotime("-$from_pre_dayofweek days"));
	$pre_todate = date("m/d/Y", strtotime("-$to_pre_dayofweek days"));

	$week_sql_orders = "SELECT sum(grand_total) as week_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$week_res_orders = $pdo->selectOne($week_sql_orders);

	$pre_week_sql_orders = "SELECT sum(grand_total) as pre_week_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_todate)) . "'";
	$pre_week_res_orders = $pdo->selectOne($pre_week_sql_orders);

	$week_per_value = $week_res_orders['week_sales'] - $pre_week_res_orders['pre_week_sales'];

	if ($pre_week_res_orders['pre_week_sales'] > 0) {
		$week_per = ($week_per_value / $pre_week_res_orders['pre_week_sales']) * 100;
	} else {
		//$week_per = $week_res_orders['week_sales'] * 100;
		$week_per = $week_res_orders['week_sales'];
	}

	$res['week_sales'] = $week_res_orders['week_sales'];
	$res['pre_week_sales'] = $pre_week_res_orders['pre_week_sales'];
	$res['week_per'] = $week_per;
	return $res;
}

function all_sales_month() {
	global $pdo;

	$fromdate = date('m/01/Y');
	$todate = date('m/d/Y');

	$pre_month_fromdate = date('m/01/Y', strtotime('previous month'));
	$pre_month_todate = date('m/t/Y', strtotime('previous month'));

	$month_sql_orders = "SELECT sum(grand_total) as month_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$month_res_orders = $pdo->selectOne($month_sql_orders);

	$pre_month_sql_orders = "SELECT sum(grand_total) as last_month_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_month_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_month_todate)) . "'";
	$pre_month_res_orders = $pdo->selectOne($pre_month_sql_orders);

	$month_per_value = $month_res_orders['month_sales'] - $pre_month_res_orders['last_month_sales'];

	if ($pre_month_res_orders['last_month_sales'] > 0) {
		$month_per = ($month_per_value / $pre_month_res_orders['last_month_sales']) * 100;
	} else {
		// $month_per = $month_res_orders['month_sales'] * 100;
		$month_per = $month_res_orders['month_sales'];
	}
	$res['month_sales'] = $month_res_orders['month_sales'];
	$res['last_month_sales'] = $pre_month_res_orders['last_month_sales'];
	$res['month_per'] = $month_per;
	return $res;
}

function all_sales_quater() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');
	$previous_quater = 0;
	if ($current_month >= 1 && $current_month <= 3) {
		$previous_quater = 0;
		$start_date = date('m/d/Y', strtotime('1-January-' . $current_year)); // timestamp or 1-Januray 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('31-March-' . $current_year)); // timestamp or 1-April 12:00:00 AM means end of 31 March
	} else if ($current_month >= 4 && $current_month <= 6) {
		$previous_quater = 1;
		$start_date = date('m/d/Y', strtotime('1-April-' . $current_year)); // timestamp or 1-April 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('30-June-' . $current_year)); // timestamp or 1-July 12:00:00 AM means end of 30 June
	} else if ($current_month >= 7 && $current_month <= 9) {
		$previous_quater = 2;
		$start_date = date('m/d/Y', strtotime('1-July-' . $current_year)); // timestamp or 1-July 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('31-September-' . $current_year)); // timestamp or 1-October 12:00:00 AM means end of 30 September
	} else if ($current_month >= 10 && $current_month <= 12) {
		$previous_quater = 3;
		$start_date = date('m/d/Y', strtotime('1-October-' . $current_year)); // timestamp or 1-October 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('31-December-' . ($current_year))); // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
	}
	$quater_sql_orders = "SELECT sum(grand_total) as quater_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($end_date)) . "'";
	$quater_res_orders = $pdo->selectOne($quater_sql_orders);

	if ($previous_quater == 0) {
		$pre_start_date = date('m/d/Y', strtotime('1-October-' . $current_year . '- 1 year')); // timestamp or 1-Januray 12:00:00 AM
		$pre_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year')); // timestamp or 1-April 12:00:00 AM means end of 31 March
	} else if ($previous_quater == 1) {
		$pre_start_date = date('m/d/Y', strtotime('1-January-' . $current_year)); // timestamp or 1-April 12:00:00 AM
		$pre_end_date = date('m/d/Y', strtotime('31-March-' . $current_year)); // timestamp or 1-July 12:00:00 AM means end of 30 June
	} else if ($previous_quater == 2) {
		$pre_start_date = date('m/d/Y', strtotime('1-April-' . $current_year)); // timestamp or 1-July 12:00:00 AM
		$pre_end_date = date('m/d/Y', strtotime('30-June-' . $current_year)); // timestamp or 1-October 12:00:00 AM means end of 30 September
	} else if ($previous_quater == 3) {
		$pre_start_date = date('m/d/Y', strtotime('1-July-' . $current_year)); // timestamp or 1-October 12:00:00 AM
		$pre_end_date = date('m/d/Y', strtotime('31-September-' . ($current_year))); // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
	}

	$pre_quater_sql_orders = "SELECT sum(grand_total) as pre_quater_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_end_date)) . "'";
	$pre_quater_res_orders = $pdo->selectOne($pre_quater_sql_orders);

	$quater_per_value = $quater_res_orders['quater_sales'] - $pre_quater_res_orders['pre_quater_sales'];

	if ($pre_quater_res_orders['pre_quater_sales'] > 0) {
		$quater_per = ($quater_per_value / $pre_quater_res_orders['pre_quater_sales']) * 100;
	} else {
		//$quater_per = $quater_res_orders['quater_sales'] * 100;
		$quater_per = $quater_res_orders['quater_sales'];
	}

	$res['quater_sales'] = $quater_res_orders['quater_sales'];
	$res['pre_quater_sales'] = $pre_quater_res_orders['pre_quater_sales'];
	$res['quater_per'] = $quater_per;
	return $res;
}

function all_sales_year() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');

	$year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
	$year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year));

	$pre_year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year . '- 1 year'));
	$pre_year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));

	$year_sql_orders = "SELECT sum(grand_total) as year_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($year_end_date)) . "'";
	$year_res_orders = $pdo->selectOne($year_sql_orders);

	$pre_year_sql_orders = "SELECT sum(grand_total) as last_year_sales "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved' AND status in('Payment Approved') AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_year_end_date)) . "'";
	$pre_year_res_orders = $pdo->selectOne($pre_year_sql_orders);

	$year_per_value = $year_res_orders['year_sales'] - $pre_year_res_orders['last_year_sales'];

	if ($pre_year_res_orders['last_year_sales'] > 0) {
		$year_per = ($year_per_value / $pre_year_res_orders['last_year_sales']) * 100;
	} else {
		//$year_per = $year_res_orders['year_sales'] * 100;
		$year_per = $year_res_orders['year_sales'];
	}

	$res['year_sales'] = $year_res_orders['year_sales'];
	$res['last_year_sales'] = $pre_year_res_orders['last_year_sales'];
	$res['year_per'] = $year_per;
	return $res;
}

// For Sales Report Functions End
//-----------------------------------------------------------------------------

// For Number Of agent Reports Functions Start

function numbers_of_agents_today($time = '') {
	global $pdo;

	if ($time != "") {

		$current_time = $time;
		$current_date = DATE('Y-m-d') . ' ' . $current_time;
		$yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date . " -24 HOUR"));
		$yesterday_date = DATE('Y-m-d', (strtotime('-1 day', strtotime($current_date)))) . ' ' . $current_time;
		$previousday_day = DATE('Y-m-d', (strtotime('-1 day', strtotime($yesterday_date)))) . ' ' . $current_time;

		$day_sql_agent = "SELECT COUNT(c.id) as today_agents
               FROM customer c
               LEFT JOIN customer as s on(s.id = c.sponsor_id)
               WHERE c.status='Active' AND c.type='Agent'
               AND c.is_deleted = 'N' AND c.id>0 AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "'";
		$day_res_agents = $pdo->selectOne($day_sql_agent);

		// QUERY FOR Yeasterda'S SALES
		$pre_day_sql_agents = "SELECT COUNT(c.id) as yesterday_agents
                 FROM customer c
                 LEFT JOIN customer as s on(s.id = c.sponsor_id)
                 WHERE c.status='Active' AND c.type='Agent'
                 AND c.is_deleted = 'N' AND c.id>0 AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($yesterday_date) . "' AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') >='" . date($previousday_day) . "'";
		$pre_day_res_agents = $pdo->selectOne($pre_day_sql_agents);

	} else {

		$day_sql_agent = "SELECT COUNT(c.id) as today_agents
               FROM customer c
               LEFT JOIN customer as s on(s.id = c.sponsor_id)
               WHERE c.status='Active' AND c.type='Agent'
               AND c.is_deleted = 'N' AND c.id>0 AND DATE(c.created_at) >= " . date('Y-m-d') . " AND DATE(c.created_at) <=" . date('Y-m-d');

		$day_res_agents = $pdo->selectOne($day_sql_agent);

		$pre_day_sql_agents = "SELECT COUNT(c.id) as yesterday_agents
                 FROM customer c
                 LEFT JOIN customer as s on(s.id = c.sponsor_id)
                 WHERE c.status='Active' AND c.type='Agent'
                 AND c.is_deleted = 'N' AND c.id>0 AND DATE(c.created_at) = CURDATE()-1";

		$pre_day_res_agents = $pdo->selectOne($pre_day_sql_agents);
	}

	$today_per_value = $day_res_agents['today_agents'] - $pre_day_res_agents['yesterday_agents'];
	// pre_print($today_per_value);

	if ($pre_day_res_agents['yesterday_agents'] > 0) {
		$today_per = ($today_per_value / $pre_day_res_agents['yesterday_agents']) * 100;
	} else {
		$today_per = $day_res_agents['today_agents'];
	}

	$res['today_agents'] = $day_res_agents['today_agents'];
	$res['yesterday_agents'] = $pre_day_res_agents['yesterday_agents'];
	$res['today_per'] = $today_per;
	return $res;
}
function numbers_of_agents_week() {
	global $pdo;

	$dayofweek = date('w', strtotime(date('m/d/Y')));
	if ($dayofweek == 7) {
		$dayofweek = 1;
	} else {
		$dayofweek = $dayofweek + 1;
	}

	$fromdate = date("m/d/Y", strtotime("-$dayofweek days"));
	$todate = date('m/d/Y');

	$from_pre_dayofweek = $dayofweek + 8;
	$to_pre_dayofweek = $dayofweek + 1;

	$pre_fromdate = date("m/d/Y", strtotime("-$from_pre_dayofweek days"));
	$pre_todate = date("m/d/Y", strtotime("-$to_pre_dayofweek days"));

	$week_sql_agents = "SELECT COUNT(c.id) as week_agents
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.status='Active' AND c.type='Agent' AND c.id>0 AND c.is_deleted = 'N' AND DATE(c.created_at) >= " . date('Y-m-d', strtotime($fromdate)) . " AND DATE(c.created_at) <=" . date('Y-m-d', strtotime($todate));

	$week_res_agents = $pdo->selectOne($week_sql_agents);

	$pre_week_sql_agents = "SELECT COUNT(c.id) as pre_week_agents
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.id>0 AND c.status='Active' AND c.type='Agent' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_fromdate)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_todate)) . "'";
	$pre_week_res_agents = $pdo->selectOne($pre_week_sql_agents);

	$week_per_value = $week_res_agents['week_agents'] - $pre_week_res_agents['pre_week_agents'];

	if ($pre_week_res_agents['pre_week_agents'] > 0) {
		$week_per = ($week_per_value / $pre_week_res_agents['pre_week_agents']) * 100;
	} else {
		$week_per = $week_res_agents['week_agents'];
	}
	$res['week_agents'] = $week_res_agents['week_agents'];
	$res['pre_week_agents'] = $pre_week_res_agents['pre_week_agents'];
	$res['week_per'] = $week_per;
	return $res;
}
function numbers_of_agents_month() {
	global $pdo;

	$fromdate = date('m/01/Y');
	$todate = date('m/d/Y');

	$pre_month_fromdate = date('m/01/Y', strtotime('previous month'));
	$pre_month_todate = date('m/t/Y', strtotime('previous month'));

	$month_sql_agents = "SELECT COUNT(c.id) as month_agents
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.id>0 AND c.status='Active' AND c.type='Agent' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$month_res_agents = $pdo->selectOne($month_sql_agents);

	$pre_month_sql_agents = "SELECT COUNT(c.id) as pre_month_agents
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.id>0 AND c.status='Active' AND c.type='Agent' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_month_fromdate)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_month_todate)) . "'";

	$pre_month_res_agents = $pdo->selectOne($pre_month_sql_agents);
	$month_per_value = $month_res_agents['month_agents'] - $pre_month_res_agents['pre_month_agents'];

	if ($pre_month_res_agents['pre_month_agents'] > 0) {
		$month_per = ($month_per_value / $pre_month_res_agents['pre_month_agents']) * 100;
	} else {
		$month_per = $month_res_agents['month_agents'];
	}

	$res['month_agents'] = $month_res_agents['month_agents'];
	$res['pre_month_agents'] = $pre_month_res_agents['pre_month_agents'];
	$res['month_per'] = $month_per;

	return $res;
}
function numbers_of_agents_quater() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');
	$previous_quater = 0;
	if ($current_month >= 1 && $current_month <= 3) {
		$previous_quater = 0;
		$start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($current_month >= 4 && $current_month <= 6) {
		$previous_quater = 1;
		$start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($current_month >= 7 && $current_month <= 9) {
		$previous_quater = 2;
		$start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-September-' . $current_year));
	} else if ($current_month >= 10 && $current_month <= 12) {
		$previous_quater = 3;
		$start_date = date('m/d/Y', strtotime('1-October-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-December-' . ($current_year)));
	}
	$quater_sql_agents = "SELECT COUNT(c.id) as quater_agents
            FROM customer c
            LEFT JOIN customer as s on(s.id = c.sponsor_id)
            WHERE c.id>0 AND c.status='Active' AND c.type='Agent' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($end_date)) . "'";
	$quater_res_agents = $pdo->selectOne($quater_sql_agents);

	if ($previous_quater == 0) {
		$pre_start_date = date('m/d/Y', strtotime('1-October-' . $current_year . '- 1 year'));
		$pre_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));
	} else if ($previous_quater == 1) {
		$pre_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($previous_quater == 2) {
		$pre_start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($previous_quater == 3) {
		$pre_start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-September-' . ($current_year)));
	}
	$pre_quater_sql_agents = "SELECT COUNT(c.id) as pre_quater_agents
          FROM customer c
          LEFT JOIN customer as s on(s.id = c.sponsor_id)
          WHERE c.id>0 AND c.status='Active' AND c.type='Agent' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_end_date)) . "'";
	$pre_quater_res_agents = $pdo->selectOne($pre_quater_sql_agents);

	$quater_per_value = $quater_res_agents['quater_agents'] - $pre_quater_res_agents['pre_quater_agents'];

	if ($pre_quater_res_agents['pre_quater_agents'] > 0) {
		$quater_per = ($quater_per_value / $pre_quater_res_agents['pre_quater_agents']) * 100;
	} else {
		$quater_per = $quater_res_agents['quater_agents'];
	}

	$res['quater_agents'] = $quater_res_agents['quater_agents'];
	$res['pre_quater_agents'] = $pre_quater_res_agents['pre_quater_agents'];
	$res['quater_per'] = $quater_per;
	return $res;
}
function numbers_of_agents_year() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');
	$year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
	$year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year));

	$pre_year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year . '- 1 year'));
	$pre_year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));
	$year_sql_agents = "SELECT COUNT(c.id) as year_agents
          FROM customer c
          LEFT JOIN customer as s on(s.id = c.sponsor_id)
          WHERE c.id>0 AND c.status='Active' AND c.type='Agent' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($year_start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($year_end_date)) . "'";
	$year_res_agents = $pdo->selectOne($year_sql_agents);

	$pre_year_sql_agents = "SELECT COUNT(c.id) as pre_year_agents
          FROM customer c
          LEFT JOIN customer as s on(s.id = c.sponsor_id)
          WHERE c.id>0 AND c.status='Active' AND c.type='Agent' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_year_start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_year_end_date)) . "'";
	$pre_year_res_agents = $pdo->selectOne($pre_year_sql_agents);

	$year_per_value = $year_res_agents['year_agents'] - $pre_year_res_agents['pre_year_agents'];

	if ($pre_year_res_agents['pre_year_agents'] > 0) {
		$year_per = ($year_per_value / $pre_year_res_agents['pre_year_agents']) * 100;
	} else {
		$year_per = $year_res_agents['year_agents'];
	}

	$res['year_agents'] = $year_res_agents['year_agents'];
	$res['pre_year_agents'] = $pre_year_res_agents['pre_year_agents'];
	$res['year_per'] = $year_per;
	return $res;
}
// For Number Of Agent Reports Functions End
//-----------------------------------------------------------------------------

//For Number Of Affiliates Functions Start

function numbers_of_affiliates_today($time = '') {
	global $pdo;

	if ($time != "") {
		$current_time = $time;
		$current_date = DATE('Y-m-d') . ' ' . $current_time;
		$yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date . " -24 HOUR"));
		$yesterday_date = DATE('Y-m-d', (strtotime('-1 day', strtotime($current_date)))) . ' ' . $current_time;
		$previousday_day = DATE('Y-m-d', (strtotime('-1 day', strtotime($yesterday_date)))) . ' ' . $current_time;

		// QUERY FOR TODAY'S SALES
		$day_sql_affiliates = "SELECT COUNT(c.id) as total_affiliates
                 FROM customer c
                 LEFT JOIN customer as s on(s.id = c.sponsor_id)
                 WHERE c.status='Active' AND c.type='Affiliates'
                 AND c.is_deleted = 'N' AND c.id>0 AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "'";

		$day_res_affiliates = $pdo->selectOne($day_sql_affiliates);

		// QUERY FOR Yeasterda'S SALES
		$pre_day_sql_affiliates = "SELECT COUNT(c.id) as yesterday_affiliates
                 FROM customer c
                 LEFT JOIN customer as s on(s.id = c.sponsor_id)
                 WHERE c.status='Active' AND c.type='Affiliates'
                 AND c.is_deleted = 'N' AND c.id>0 AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($yesterday_date) . "' AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') >='" . date($previousday_day) . "'";

		$pre_day_res_affiliates = $pdo->selectOne($pre_day_sql_affiliates);

	} else {

		// QUERY FOR TODAY'S SALES
		$day_sql_affiliates = "SELECT COUNT(c.id) as total_affiliates
                 FROM customer c
                 LEFT JOIN customer as s on(s.id = c.sponsor_id)
                 WHERE c.status='Active' AND c.type='Affiliates'
                 AND c.is_deleted = 'N' AND c.id>0 AND DATE(c.created_at) >= " . date('Y-m-d') . " AND DATE(c.created_at) <=" . date('Y-m-d');

		$day_res_affiliates = $pdo->selectOne($day_sql_affiliates);

		// QUERY FOR Yeasterda'S SALES
		$pre_day_sql_affiliates = "SELECT COUNT(c.id) as yesterday_affiliates
                 FROM customer c
                 LEFT JOIN customer as s on(s.id = c.sponsor_id)
                 WHERE c.status='Active' AND c.type='Affiliates'
                 AND c.is_deleted = 'N' AND c.id>0 AND DATE(c.created_at) = CURDATE()-1";

		$pre_day_res_affiliates = $pdo->selectOne($pre_day_sql_affiliates);

	}

	// Calculate today percentage
	$today_per_value = $day_res_affiliates['total_affiliates'] - $pre_day_res_affiliates['yesterday_affiliates'];

	if ($pre_day_res_affiliates['yesterday_affiliates'] > 0) {
		$today_per = ($today_per_value / $pre_day_res_affiliates['yesterday_affiliates']) * 100;
	} else {
		$today_per = $day_res_affiliates['total_affiliates'];
	}

	$res['today_affiliates'] = $day_res_affiliates['today_affiliates'];
	$res['yesterday_affiliates'] = $pre_day_res_affiliates['yesterday_affiliates'];
	$res['today_per'] = $today_per;
	return $res;
}
function numbers_of_affiliates_week() {
	global $pdo;

	// QUERY FOR WEEK'S SALES
	$dayofweek = date('w', strtotime(date('m/d/Y')));
	if ($dayofweek == 7) {
		$dayofweek = 1;
	} else {
		$dayofweek = $dayofweek + 1;
	}

	$fromdate = date("m/d/Y", strtotime("-$dayofweek days"));
	$todate = date('m/d/Y');

	$from_pre_dayofweek = $dayofweek + 8;
	$to_pre_dayofweek = $dayofweek + 1;

	$pre_fromdate = date("m/d/Y", strtotime("-$from_pre_dayofweek days"));
	$pre_todate = date("m/d/Y", strtotime("-$to_pre_dayofweek days"));

	$week_sql_affiliates = "SELECT COUNT(c.id) as week_affiliates
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.status='Active' AND c.type='Affiliates' AND c.id>0 AND c.is_deleted = 'N' AND DATE(c.created_at) >= " . date('Y-m-d', strtotime($fromdate)) . " AND DATE(c.created_at) <=" . date('Y-m-d', strtotime($todate));

	$week_res_affiliates = $pdo->selectOne($week_sql_affiliates);

	// QUERY FOR LAST WEEK'S SALES
	$pre_week_sql_affiliates = "SELECT COUNT(c.id) as last_week_affiliates
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.id>0 AND c.status='Active' AND c.type='Affiliates' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_fromdate)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_todate)) . "'";
	$pre_week_res_affiliates = $pdo->selectOne($pre_week_sql_affiliates);

	// Calculate percentage
	$week_per_value = $week_res_affiliates['week_affiliates'] - $pre_week_res_affiliates['last_week_affiliates'];

	if ($pre_week_res_affiliates['last_week_affiliates'] > 0) {
		$week_per = ($week_per_value / $pre_week_res_affiliates['last_week_affiliates']) * 100;
	} else {
		$week_per = $week_res_affiliates['week_affiliates'];
	}

	$res['week_affiliates'] = $week_res_affiliates['week_affiliates'];
	$res['last_week_affiliates'] = $pre_week_res_affiliates['last_week_affiliates'];
	$res['week_per'] = $week_per;
	return $res;
}
function numbers_of_affiliates_month() {
	global $pdo;

	// QUERY FOR MONTH'S SALES
	$fromdate = date('m/01/Y');
	$todate = date('m/d/Y');

	$pre_month_fromdate = date('m/01/Y', strtotime('previous month'));
	$pre_month_todate = date('m/t/Y', strtotime('previous month'));

	$month_sql_affiliates = "SELECT COUNT(c.id) as month_affiliates
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.id>0 AND c.status='Active' AND c.type='Affiliates' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$month_res_affiliates = $pdo->selectOne($month_sql_affiliates);

	// QUERY FOR LAST MONTH'S SALES
	$pre_month_sql_affiliates = "SELECT COUNT(c.id) as last_month_affiliates
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.id>0 AND c.status='Active' AND c.type='Affiliates' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_month_fromdate)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_month_todate)) . "'";

	$pre_month_res_affiliates = $pdo->selectOne($pre_month_sql_affiliates);

	$month_per_value = $month_res_affiliates['month_affiliates'] - $pre_month_res_affiliates['last_month_affiliates'];

	if ($pre_month_res_affiliates['last_month_affiliates'] > 0) {
		$month_per = ($month_per_value / $pre_month_res_affiliates['last_month_affiliates']) * 100;
	} else {
		$month_per = $month_res_affiliates['month_affiliates'];
	}
	$res['month_affiliates'] = $month_res_affiliates['month_affiliates'];
	$res['last_month_affiliates'] = $pre_month_res_affiliates['last_month_affiliates'];
	$res['month_per'] = $month_per;
	return $res;
}
function numbers_of_affiliates_quater() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');

	$previous_quater = 0;
	if ($current_month >= 1 && $current_month <= 3) {
		$previous_quater = 0;
		$start_date = date('m/d/Y', strtotime('1-January-' . $current_year)); // timestamp or 1-Januray 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('31-March-' . $current_year)); // timestamp or 1-April 12:00:00 AM means end of 31 March
	} else if ($current_month >= 4 && $current_month <= 6) {
		$previous_quater = 1;
		$start_date = date('m/d/Y', strtotime('1-April-' . $current_year)); // timestamp or 1-April 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('30-June-' . $current_year)); // timestamp or 1-July 12:00:00 AM means end of 30 June
	} else if ($current_month >= 7 && $current_month <= 9) {
		$previous_quater = 2;
		$start_date = date('m/d/Y', strtotime('1-July-' . $current_year)); // timestamp or 1-July 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('31-September-' . $current_year)); // timestamp or 1-October 12:00:00 AM means end of 30 September
	} else if ($current_month >= 10 && $current_month <= 12) {
		$previous_quater = 3;
		$start_date = date('m/d/Y', strtotime('1-October-' . $current_year)); // timestamp or 1-October 12:00:00 AM
		$end_date = date('m/d/Y', strtotime('31-December-' . ($current_year))); // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
	}

	//QUERY FOR QUATER SALES
	$quater_sql_affiliates = "SELECT COUNT(c.id) as quater_affiliates
          FROM customer c
          LEFT JOIN customer as s on(s.id = c.sponsor_id)
          WHERE c.id>0 AND c.status='Active' AND c.type='Affiliates' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($end_date)) . "'";
	$quater_res_affiliates = $pdo->selectOne($quater_sql_affiliates);

	if ($previous_quater == 0) {
		$pre_start_date = date('m/d/Y', strtotime('1-October-' . $current_year . '- 1 year'));
		$pre_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));
	} else if ($previous_quater == 1) {
		$pre_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($previous_quater == 2) {
		$pre_start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($previous_quater == 3) {
		$pre_start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-September-' . ($current_year)));
	}

	//QUERY FOR PRE QUATER SALES
	$pre_quater_sql_affiliates = "SELECT COUNT(c.id) as pre_quater_affiliates
          FROM customer c
          LEFT JOIN customer as s on(s.id = c.sponsor_id)
          WHERE c.id>0 AND c.status='Active' AND c.type='Affiliates' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_end_date)) . "'";
	$pre_quater_res_affiliates = $pdo->selectOne($pre_quater_sql_affiliates);

	$quater_per_value = $quater_res_affiliates['quater_affiliates'] - $pre_quater_res_affiliates['pre_quater_affiliates'];

	if ($pre_quater_res_affiliates['pre_quater_affiliates'] > 0) {
		$quater_per = ($quater_per_value / $pre_quater_res_affiliates['pre_quater_affiliates']) * 100;
	} else {
		$quater_per = $quater_res_affiliates['quater_affiliates'];
	}

	$res['quater_affiliates'] = $quater_res_affiliates['quater_affiliates'];
	$res['pre_quater_affiliates'] = $pre_quater_res_affiliates['pre_quater_affiliates'];
	$res['quater_per'] = $quater_per;
	return $res;
}
function numbers_of_affiliates_year() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');

	$year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
	$year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year));

	$pre_year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year . '- 1 year'));
	$pre_year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));

	$year_sql_affiliates = "SELECT COUNT(c.id) as year_affiliates
        FROM customer c
        LEFT JOIN customer as s on(s.id = c.sponsor_id)
        WHERE c.id>0 AND c.status='Active' AND c.type='Affiliates' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($year_start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($year_end_date)) . "'";
	$year_res_affiliates = $pdo->selectOne($year_sql_affiliates);

	$pre_year_sql_affiliates = "SELECT COUNT(c.id) as last_year_affiliates
          FROM customer c
          LEFT JOIN customer as s on(s.id = c.sponsor_id)
          WHERE c.id>0 AND c.status='Active' AND c.type='Affiliates' AND c.is_deleted = 'N' AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($pre_year_start_date)) . "' AND DATE(c.created_at) <='" . date('Y-m-d', strtotime($pre_year_end_date)) . "'";
	$pre_year_res_affiliates = $pdo->selectOne($pre_year_sql_affiliates);

	$year_per_value = $year_res_affiliates['year_affiliates'] - $pre_year_res_affiliates['last_year_affiliates'];

	if ($pre_year_res_affiliates['last_year_affiliates'] > 0) {
		$year_per = ($year_per_value / $pre_year_res_affiliates['last_year_affiliates']) * 100;
	} else {
		$year_per = $year_res_affiliates['year_affiliates'];
	}

	$res['year_affiliates'] = $year_res_affiliates['year_affiliates'];
	$res['last_year_affiliates'] = $pre_year_res_affiliates['last_year_affiliates'];
	$res['year_per'] = $year_per;
	return $res;
}

//For Number Of Affiliates Functions End

// For All Member Report Functions Start

function all_members_today($time = '') {
	global $pdo;

	if ($time != "") {
		$current_time = $time;
		$current_date = DATE('Y-m-d') . ' ' . $current_time;
		$yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date . " -24 HOUR"));
		$yesterday_date = DATE('Y-m-d', (strtotime('-1 day', strtotime($current_date)))) . ' ' . $current_time;
		$previousday_day = DATE('Y-m-d', (strtotime('-1 day', strtotime($yesterday_date)))) . ' ' . $current_time;

		// QUERY FOR TODAY'S ENROLLS
		$day_sql_enrolls = "SELECT COUNT(id) as today_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "'";
		$day_res_enrolls = $pdo->selectOne($day_sql_enrolls);

		// QUERY  FOR Yeasterday'S ENROLLS
		$pre_day_sql_enrolls = "SELECT COUNT(id) as yesterday_enrolls
                        FROM customer
                        WHERE type='Customer' AND status='Active' AND id>0 AND is_deleted = 'N' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($yesterday_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($previousday_day) . "'";
		$pre_day_res_enrolls = $pdo->selectOne($pre_day_sql_enrolls);

	} else {

		// QUERY FOR TODAY'S ENROLLS
		$day_sql_enrolls = "SELECT COUNT(id) as today_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d') . "' AND DATE(created_at) <='" . date('Y-m-d') . "'";
		$day_res_enrolls = $pdo->selectOne($day_sql_enrolls);

		// QUERY  FOR Yeasterday'S ENROLLS
		$pre_day_sql_enrolls = "SELECT COUNT(id) as yesterday_enrolls
                        FROM customer
                        WHERE type='Customer' AND status='Active' AND id>0 AND is_deleted = 'N' AND DATE(created_at) = CURDATE()-1";
		$pre_day_res_enrolls = $pdo->selectOne($pre_day_sql_enrolls);
	}
	// Calculate percentage
	$today_per_value = $day_res_enrolls['today_enrolls'] - $pre_day_res_enrolls['yesterday_enrolls'];
	if ($pre_day_res_enrolls['yesterday_enrolls'] > 0) {
		$today_per = ($today_per_value / $pre_day_res_enrolls['yesterday_enrolls']) * 100;
	} else {
		$today_per = $day_res_enrolls['today_enrolls'] * 100;
	}

	$res['today_enrolls'] = $day_res_enrolls['today_enrolls'];
	$res['yesterday_enrolls'] = $pre_day_res_enrolls['yesterday_enrolls'];
	$res['today_per'] = $today_per;
	// pre_print($res);
	return $res;
}
function all_members_week() {
	global $pdo;

	$dayofweek = date('w', strtotime(date('m/d/Y')));
	if ($dayofweek == 7) {
		$dayofweek = 1;
	} else {
		$dayofweek = $dayofweek + 1;
	}

	$fromdate = date("m/d/Y", strtotime("-$dayofweek days"));
	$todate = date('m/d/Y');
	$from_pre_dayofweek = $dayofweek + 8;
	$to_pre_dayofweek = $dayofweek + 1;
	$pre_fromdate = date("m/d/Y", strtotime("-$from_pre_dayofweek days"));
	$pre_todate = date("m/d/Y", strtotime("-$to_pre_dayofweek days"));

	// QUERY FOR WEEK'S ENROLLS
	$week_sql_enrolls = "SELECT count(id) as week_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$week_res_enrolls = $pdo->selectOne($week_sql_enrolls);

	// QUERY FOR LAST WEEK'S ENROLLS
	$pre_week_sql_enrolls = "SELECT COUNT(id) as pre_week_enrolls
                          FROM customer
                          WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_todate)) . "'";
	$pre_week_res_enrolls = $pdo->selectOne($pre_week_sql_enrolls);

	$week_per_value = $week_res_enrolls['week_enrolls'] - $pre_week_res_enrolls['pre_week_enrolls'];

	if ($pre_week_res_enrolls['pre_week_enrolls'] > 0) {
		$week_per = ($week_per_value / $pre_week_res_enrolls['pre_week_enrolls']) * 100;
	} else {
		$week_per = $week_res_enrolls['week_enrolls'] * 100;
	}
	$res['week_enrolls'] = $week_res_enrolls['week_enrolls'];
	$res['pre_week_enrolls'] = $pre_week_res_enrolls['pre_week_enrolls'];
	$res['week_per'] = $week_per;
	return $res;
}
function all_members_month() {
	global $pdo;

	$fromdate = date('m/01/Y');
	$todate = date('m/d/Y');
	$pre_month_fromdate = date('m/01/Y', strtotime('previous month'));
	$pre_month_todate = date('m/t/Y', strtotime('previous month'));

	// QUERY FOR MONTH'S Enrolls
	$month_sql_enrolls = "SELECT count(id) as month_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active'  AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$month_res_enrolls = $pdo->selectOne($month_sql_enrolls);

	// QUERY FOR PRE MONTH'S Enrolls
	$pre_month_sql_enrolls = "SELECT count(id) as pre_month_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_month_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_month_todate)) . "'";
	$pre_month_res_enrolls = $pdo->selectOne($pre_month_sql_enrolls);

	$month_per_value = $month_res_enrolls['month_enrolls'] - $pre_month_res_enrolls['pre_month_enrolls'];

	if ($pre_month_res_enrolls['pre_month_enrolls'] > 0) {
		$month_per = ($month_per_value / $pre_month_res_enrolls['pre_month_enrolls']) * 100;
	} else {
		$month_per = $month_res_enrolls['month_enrolls'] * 100;
	}

	$res['month_enrolls'] = $month_res_enrolls['month_enrolls'];
	$res['pre_month_enrolls'] = $pre_month_res_enrolls['pre_month_enrolls'];
	$res['month_per'] = $month_per;
	return $res;
}
function all_members_quater() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');
	$previous_quater = 0;

	if ($current_month >= 1 && $current_month <= 3) {
		$previous_quater = 0;
		$start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($current_month >= 4 && $current_month <= 6) {
		$previous_quater = 1;
		$start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($current_month >= 7 && $current_month <= 9) {
		$previous_quater = 2;
		$start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-September-' . $current_year));
	} else if ($current_month >= 10 && $current_month <= 12) {
		$previous_quater = 3;
		$start_date = date('m/d/Y', strtotime('1-October-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-December-' . ($current_year)));
	}

	// QUERY FOR QUATER'S Enrolls
	$quater_sql_enrolls = "SELECT count(id) as quater_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($end_date)) . "'";
	$quater_res_enrolls = $pdo->selectOne($quater_sql_enrolls);

	if ($previous_quater == 0) {
		$pre_start_date = date('m/d/Y', strtotime('1-October-' . $current_year . '- 1 year'));
		$pre_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));
	} else if ($previous_quater == 1) {
		$pre_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($previous_quater == 2) {
		$pre_start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($previous_quater == 3) {
		$pre_start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-September-' . ($current_year)));
	}

	// QUERY FOR PRE QUATER'S Enrolls
	$pre_quater_sql_enrolls = "SELECT count(id) as pre_quater_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_end_date)) . "'";
	$pre_quater_res_enrolls = $pdo->selectOne($pre_quater_sql_enrolls);

	$quater_per_value = $quater_res_enrolls['quater_enrolls'] - $pre_quater_res_enrolls['pre_quater_enrolls'];

	if ($pre_quater_res_enrolls['pre_quater_enrolls'] > 0) {
		$quater_per = ($quater_per_value / $pre_quater_res_enrolls['pre_quater_enrolls']) * 100;
	} else {
		$quater_per = $quater_res_enrolls['quater_enrolls'] * 100;
	}
	$res['quater_enrolls'] = $quater_res_enrolls['quater_enrolls'];
	$res['pre_quater_enrolls'] = $pre_quater_res_enrolls['pre_quater_enrolls'];
	$res['quater_per'] = $quater_per;
	return $res;
}
function all_members_year() {
	global $pdo;

	$current_month = date('m');
	$current_year = date('Y');
	$year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
	$year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year));
	$pre_year_start_date = date('m/d/Y', strtotime('1-January-' . ($current_year . '- 1 year')));
	$pre_year_end_date = date('m/d/Y', strtotime('31-December-' . ($current_year . '- 1 year')));

	// QUERY FOR YEAR'S Enrolls
	$year_sql_enrolls = "SELECT count(id) as year_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($year_end_date)) . "'";
	$year_res_enrolls = $pdo->selectOne($year_sql_enrolls);

	// QUERY FOR PRE YEAR'S Enrolls
	$pre_year_sql_enrolls = "SELECT count(id) as pre_year_enrolls
                      FROM customer
                      WHERE type='Customer' AND status='Active' AND is_deleted = 'N' AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_year_end_date)) . "'";
	$pre_year_res_enrolls = $pdo->selectOne($pre_year_sql_enrolls);

	$year_per_value = $year_res_enrolls['year_enrolls'] - $pre_year_res_enrolls['pre_year_enrolls'];
	if ($pre_year_res_enrolls['pre_year_enrolls'] > 0) {
		$year_per = ($year_per_value / $pre_year_res_enrolls['pre_year_enrolls']) * 100;
	} else {
		$year_per = $year_res_enrolls['year_enrolls'] * 100;
	}
	$res['year_enrolls'] = $year_res_enrolls['year_enrolls'];
	$res['pre_year_enrolls'] = $pre_year_res_enrolls['pre_year_enrolls'];
	$res['year_per'] = $year_per;
	return $res;
}

// For All Member Report Functions End

// For New Business Report Functions Start

function new_business_today($date = '') {
	global $pdo;

	$incr_type = " AND (type=',Enrollment,' OR type=',Customer Order,' OR type=',Customer Enrollment,') AND is_renewal='N'";

	if ($date != "") {

		$today_incr = " AND DATE(created_at) >= '" . date("Y-m-d", strtotime($date)) . "' AND DATE(created_at) <='" . date("Y-m-d", strtotime($date)) . "'";
		$pre_day_incr = " AND DATE(created_at) = '" . date('Y-m-d', strtotime('-1 day', strtotime($date))) . "'";
	} else {
		$today_incr = " AND DATE(created_at) >= '" . DATE('Y-m-d') . "' AND DATE(created_at) <='" . DATE('Y-m-d') . "'";
		$pre_day_incr = " AND DATE(created_at) = CURDATE()-1";
	}

	$day_sql_orders = "SELECT sum(grand_total) as today_new_business "
		. "FROM orders "
		. "WHERE id>0 AND status='Payment Approved' " . $incr_type . $today_incr;
	$day_res_orders = $pdo->selectOne($day_sql_orders);

	// QUERY FOR Yeasterda'S
	$pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_new_business "
		. "FROM orders "
		. "WHERE id>0 AND status='Payment Approved' " . $incr_type . $pre_day_incr;
	$pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

	// Calculate today percentage
	$today_per_value = $day_res_orders['today_new_business'] - $pre_day_res_orders['yesterday_new_business'];

	if ($pre_day_res_orders['yesterday_new_business'] > 0) {
		$today_per = ($today_per_value / $pre_day_res_orders['yesterday_new_business']) * 100;
	} else {
		$today_per = $day_res_orders['today_new_business'];
	}

	$res['today_new_business'] = $day_res_orders['today_new_business'];
	$res['yesterday_new_business'] = $pre_day_res_orders['yesterday_new_business'];
	$res['today_per'] = $today_per;

	/*if($time!=""){

		    $current_time = $time;
		    $current_date = DATE('Y-m-d'). ' ' . $current_time;
		    $yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date." -24 HOUR"));
		    $yesterday_date  = DATE('Y-m-d',(strtotime ('-1 day',strtotime($current_date)))). ' ' . $current_time; ;
		    $previousday_day = DATE('Y-m-d',(strtotime ('-1 day',strtotime($yesterday_date)))). ' ' . $current_time;

		    $day_sql_orders = "SELECT sum(grand_total) as today_new_business "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . " AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "'";
		    $day_res_orders = $pdo->selectOne($day_sql_orders);

		    // QUERY FOR Yeasterda'S
		    $pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_new_business "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . "AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($yesterday_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($previousday_day) . "'";
		    $pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

		    }else{

		    $day_sql_orders = "SELECT sum(grand_total) as today_new_business "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d') . "' AND DATE(created_at) <='" . date('Y-m-d') . "'";
		    $day_res_orders = $pdo->selectOne($day_sql_orders);

		    // QUERY FOR Yeasterda'S
		    $pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_new_business "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . " AND DATE(created_at) = CURDATE()-1";
		    $pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

	*/

	return $res;
}
function new_business_week() {
	global $pdo;

	$dayofweek = date('w', strtotime(date('m/d/Y')));
	if ($dayofweek == 7) {
		$dayofweek = 1;
	} else {
		$dayofweek = $dayofweek + 1;
	}

	$fromdate = date("m/d/Y", strtotime("-$dayofweek days"));
	$todate = date('m/d/Y');

	$from_pre_dayofweek = $dayofweek + 8;
	$to_pre_dayofweek = $dayofweek + 1;

	$pre_fromdate = date("m/d/Y", strtotime("-$from_pre_dayofweek days"));
	$pre_todate = date("m/d/Y", strtotime("-$to_pre_dayofweek days"));

	$incr_type = " AND (type=',Enrollment,' OR type=',Customer Order,' OR type=',Customer Enrollment,') AND is_renewal='N'";

	$week_sql_orders = "SELECT sum(grand_total) as week_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$week_res_orders = $pdo->selectOne($week_sql_orders);

	$pre_week_sql_orders = "SELECT sum(grand_total) as pre_week_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_todate)) . "'";
	$pre_week_res_orders = $pdo->selectOne($pre_week_sql_orders);

	$week_per_value = $week_res_orders['week_new_business'] - $pre_week_res_orders['pre_week_new_business'];

	if ($pre_week_res_orders['pre_week_new_business'] > 0) {
		$week_per = ($week_per_value / $pre_week_res_orders['pre_week_new_business']) * 100;
	} else {
		$week_per = $week_res_orders['week_new_business'];
	}

	$res['week_new_business'] = $week_res_orders['week_new_business'];
	$res['pre_week_new_business'] = $pre_week_res_orders['pre_week_new_business'];
	$res['week_per'] = $week_per;
	return $res;
}
function new_business_month() {
	global $pdo;

	$fromdate = date('m/01/Y');
	$todate = date('m/d/Y');

	$pre_month_fromdate = date('m/01/Y', strtotime('previous month'));
	$pre_month_todate = date('m/t/Y', strtotime('previous month'));

	$incr_type = " AND (type=',Enrollment,' OR type=',Customer Order,' OR type=',Customer Enrollment,') AND is_renewal='N'";

	$month_sql_orders = "SELECT sum(grand_total) as month_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$month_res_orders = $pdo->selectOne($month_sql_orders);

	$pre_month_sql_orders = "SELECT sum(grand_total) as pre_month_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_month_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_month_todate)) . "'";
	$pre_month_res_orders = $pdo->selectOne($pre_month_sql_orders);

	$month_per_value = $month_res_orders['month_new_business'] - $pre_month_res_orders['pre_month_new_business'];

	if ($pre_month_res_orders['pre_month_new_business'] > 0) {
		$month_per = ($month_per_value / $pre_month_res_orders['pre_month_new_business']) * 100;
	} else {
		$month_per = $month_res_orders['month_new_business'];
	}
	$res['month_new_business'] = $month_res_orders['month_new_business'];
	$res['pre_month_new_business'] = $pre_month_res_orders['pre_month_new_business'];
	$res['month_per'] = $month_per;
	return $res;
}
function new_business_quater() {
	global $pdo;
	$current_month = date('m');
	$current_year = date('Y');
	$previous_quater = 0;

	$incr_type = " AND (type=',Enrollment,' OR type=',Customer Order,' OR type=',Customer Enrollment,') AND is_renewal='N'";

	if ($current_month >= 1 && $current_month <= 3) {
		$previous_quater = 0;
		$start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($current_month >= 4 && $current_month <= 6) {
		$previous_quater = 1;
		$start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($current_month >= 7 && $current_month <= 9) {
		$previous_quater = 2;
		$start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-September-' . $current_year));
	} else if ($current_month >= 10 && $current_month <= 12) {
		$previous_quater = 3;
		$start_date = date('m/d/Y', strtotime('1-October-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-December-' . ($current_year)));
	}

	$quater_sql_orders = "SELECT sum(grand_total) as quater_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($end_date)) . "'";
	$quater_res_orders = $pdo->selectOne($quater_sql_orders);

	if ($previous_quater == 0) {
		$pre_start_date = date('m/d/Y', strtotime('1-October-' . $current_year . '- 1 year'));
		$pre_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));
	} else if ($previous_quater == 1) {
		$pre_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($previous_quater == 2) {
		$pre_start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($previous_quater == 3) {
		$pre_start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-September-' . ($current_year)));
	}

	$pre_quater_sql_orders = "SELECT sum(grand_total) as pre_quater_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_end_date)) . "'";
	$pre_quater_res_orders = $pdo->selectOne($pre_quater_sql_orders);

	$quater_per_value = $quater_res_orders['quater_new_business'] - $pre_quater_res_orders['pre_quater_new_business'];

	if ($pre_quater_res_orders['pre_quater_new_business'] > 0) {
		$quater_per = ($quater_per_value / $pre_quater_res_orders['pre_quater_new_business']) * 100;
	} else {
		$quater_per = $quater_res_orders['quater_new_business'];
	}

	$res['quater_new_business'] = $quater_res_orders['quater_new_business'];
	$res['pre_quater_new_business'] = $pre_quater_res_orders['pre_quater_new_business'];
	$res['quater_per'] = $quater_per;
	return $res;
}
function new_business_year() {
	global $pdo;
	$current_month = date('m');
	$current_year = date('Y');

	$year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
	$year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year));

	$pre_year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year . '- 1 year'));
	$pre_year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));

	$incr_type = " AND (type=',Enrollment,' OR type=',Customer Order,' OR type=',Customer Enrollment,') AND is_renewal='N'";

	$year_sql_orders = "SELECT sum(grand_total) as year_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($year_end_date)) . "'";
	$year_res_orders = $pdo->selectOne($year_sql_orders);

	$pre_year_sql_orders = "SELECT sum(grand_total) as pre_year_new_business "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_year_end_date)) . "'";
	$pre_year_res_orders = $pdo->selectOne($pre_year_sql_orders);

	$year_per_value = $year_res_orders['year_new_business'] - $pre_year_res_orders['pre_year_new_business'];

	if ($pre_year_res_orders['pre_year_new_business'] > 0) {
		$year_per = ($year_per_value / $pre_year_res_orders['pre_year_new_business']) * 100;
	} else {
		$year_per = $year_res_orders['year_new_business'];
	}

	$res['year_new_business'] = $year_res_orders['year_new_business'];
	$res['pre_year_new_business'] = $pre_year_res_orders['pre_year_new_business'];
	$res['year_per'] = $year_per;
	return $res;
}

// For New Business Report Functions End

// For Renewals Report Functions Start

function renewals_today($date = '') {
	global $pdo;

	// QUERY FOR TODAY'S Renewals
	$incr_type = " AND (type=',Renewals,' OR type=',Subscription Order,' OR type=',Website Subscriptions,') AND is_renewal='Y'";

	if ($date != "") {

		$today_incr = " AND DATE(created_at) >= '" . date("Y-m-d", strtotime($date)) . "' AND DATE(created_at) <='" . date("Y-m-d", strtotime($date)) . "'";
		$pre_day_incr = " AND DATE(created_at) = '" . date('Y-m-d', strtotime('-1 day', strtotime($date))) . "'";
	} else {
		$today_incr = " AND DATE(created_at) >= '" . DATE('Y-m-d') . "' AND DATE(created_at) <='" . DATE('Y-m-d') . "'";
		$pre_day_incr = " AND DATE(created_at) = CURDATE()-1";
	}

	$day_sql_orders = "SELECT sum(grand_total) as today_renewals "
		. "FROM orders "
		. "WHERE id>0 AND status='Payment Approved' " . $incr_type . $today_incr;
	$day_res_orders = $pdo->selectOne($day_sql_orders);

	// QUERY FOR Yeasterda'S Renewals
	$pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_renewals "
		. "FROM orders "
		. "WHERE id>0 AND status='Payment Approved' " . $incr_type . $pre_day_incr;
	$pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

	// Calculate today percentage
	$today_per_value = $day_res_orders['today_renewals'] - $pre_day_res_orders['yesterday_renewals'];

	if ($pre_day_res_orders['yesterday_renewals'] > 0) {
		$today_per = ($today_per_value / $pre_day_res_orders['yesterday_renewals']) * 100;
	} else {
		$today_per = $day_res_orders['today_renewals'];
	}
	$res['today_renewals'] = $day_res_orders['today_renewals'];
	$res['yesterday_renewals'] = $pre_day_res_orders['yesterday_renewals'];
	$res['today_per'] = $today_per;

	/*if($time!=""){

		    $current_time = $time;
		    $current_date = DATE('Y-m-d'). ' ' . $current_time;
		    $yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date." -24 HOUR"));
		    $yesterday_date  = DATE('Y-m-d',(strtotime ('-1 day',strtotime($current_date)))). ' ' . $current_time; ;
		    $previousday_day = DATE('Y-m-d',(strtotime ('-1 day',strtotime($yesterday_date)))). ' ' . $current_time;

		    $day_sql_orders = "SELECT sum(grand_total) as today_renewals "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . " AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "'";
		    $day_res_orders = $pdo->selectOne($day_sql_orders);

		    // QUERY FOR Yeasterda'S Renewals
		    $pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_renewals "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . " AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($yesterday_date) . "' AND STR_TO_DATE(created_at, '%Y-%m-%d %H:%i:%s') >='" . date($previousday_day) . "'";
		    $pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

		    }else{

		    $day_sql_orders = "SELECT sum(grand_total) as today_renewals "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d') . "' AND DATE(created_at) <='" . date('Y-m-d') . "'";
		    $day_res_orders = $pdo->selectOne($day_sql_orders);

		    // QUERY FOR Yeasterda'S Renewals
		    $pre_day_sql_orders = "SELECT sum(grand_total) as yesterday_renewals "
		    . "FROM orders "
		    . "WHERE id>0 AND status='Payment Approved' " . $incr_type . " AND DATE(created_at) = CURDATE()-1";
		    $pre_day_res_orders = $pdo->selectOne($pre_day_sql_orders);

	*/

	return $res;
}

function renewals_week() {
	global $pdo;

	$dayofweek = date('w', strtotime(date('m/d/Y')));
	if ($dayofweek == 7) {
		$dayofweek = 1;
	} else {
		$dayofweek = $dayofweek + 1;
	}

	$fromdate = date("m/d/Y", strtotime("-$dayofweek days"));
	$todate = date('m/d/Y');
	$from_pre_dayofweek = $dayofweek + 8;
	$to_pre_dayofweek = $dayofweek + 1;
	$pre_fromdate = date("m/d/Y", strtotime("-$from_pre_dayofweek days"));
	$pre_todate = date("m/d/Y", strtotime("-$to_pre_dayofweek days"));

	$incr_type = " AND (type=',Renewals,' OR type=',Subscription Order,' OR type=',Website Subscriptions,') AND is_renewal='Y'";

	// QUERY FOR WEEK'S Renewals
	$week_sql_orders = "SELECT sum(grand_total) as week_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$week_res_orders = $pdo->selectOne($week_sql_orders);
	// pre_print($week_res_orders);
	// QUERY FOR PRE WEEK'S Renewals
	$pre_week_sql_orders = "SELECT sum(grand_total) as pre_week_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_todate)) . "'";
	$pre_week_res_orders = $pdo->selectOne($pre_week_sql_orders);
	$week_per_value = $week_res_orders['week_renewals'] - $pre_week_res_orders['pre_week_renewals'];

	if ($pre_week_res_orders['pre_week_renewals'] > 0) {
		$week_per = ($week_per_value / $pre_week_res_orders['pre_week_renewals']) * 100;
	} else {
		$week_per = $week_res_orders['week_renewals'];
	}

	$res['week_renewals'] = $week_res_orders['week_renewals'];
	$res['pre_week_renewals'] = $pre_week_res_orders['pre_week_renewals'];
	$res['week_per'] = $week_per;
	return $res;
}
function renewals_month() {
	global $pdo;

	$fromdate = date('m/01/Y');
	$todate = date('m/d/Y');

	$pre_month_fromdate = date('m/01/Y', strtotime('previous month'));
	$pre_month_todate = date('m/t/Y', strtotime('previous month'));

	$incr_type = " AND (type=',Renewals,' OR type=',Subscription Order,' OR type=',Website Subscriptions,') AND is_renewal='Y'";

	// QUERY FOR MONTH'S Renewals
	$month_sql_orders = "SELECT sum(grand_total) as month_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($todate)) . "'";
	$month_res_orders = $pdo->selectOne($month_sql_orders);

	// QUERY FOR PRE MONTH'S Renewals
	$pre_month_sql_orders = "SELECT sum(grand_total) as pre_month_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_month_fromdate)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_month_todate)) . "'";
	$pre_month_res_orders = $pdo->selectOne($pre_month_sql_orders);

	$month_per_value = $month_res_orders['month_renewals'] - $pre_month_res_orders['pre_month_renewals'];

	if ($pre_month_res_orders['pre_month_renewals'] > 0) {
		$month_per = ($month_per_value / $pre_month_res_orders['pre_month_renewals']) * 100;
	} else {
		$month_per = $month_res_orders['month_renewals'];
	}

	$res['month_renewals'] = $month_res_orders['month_renewals'];
	$res['pre_month_renewals'] = $pre_month_res_orders['pre_month_renewals'];
	$res['month_per'] = $month_per;
	return $res;
}
function renewals_quater() {
	global $pdo;
	$current_month = date('m');
	$current_year = date('Y');
	$previous_quater = 0;

	$incr_type = " AND (type=',Renewals,' OR type=',Subscription Order,' OR type=',Website Subscriptions,') AND is_renewal='Y'";

	if ($current_month >= 1 && $current_month <= 3) {
		$previous_quater = 0;
		$start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($current_month >= 4 && $current_month <= 6) {
		$previous_quater = 1;
		$start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($current_month >= 7 && $current_month <= 9) {
		$previous_quater = 2;
		$start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-September-' . $current_year));
	} else if ($current_month >= 10 && $current_month <= 12) {
		$previous_quater = 3;
		$start_date = date('m/d/Y', strtotime('1-October-' . $current_year));
		$end_date = date('m/d/Y', strtotime('31-December-' . ($current_year)));
	}

	// QUERY FOR Quater'S Renewals
	$quater_sql_orders = "SELECT sum(grand_total) as quater_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($end_date)) . "'";
	$quater_res_orders = $pdo->selectOne($quater_sql_orders);

	if ($previous_quater == 0) {
		$pre_start_date = date('m/d/Y', strtotime('1-October-' . $current_year . '- 1 year'));
		$pre_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));
	} else if ($previous_quater == 1) {
		$pre_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-March-' . $current_year));
	} else if ($previous_quater == 2) {
		$pre_start_date = date('m/d/Y', strtotime('1-April-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('30-June-' . $current_year));
	} else if ($previous_quater == 3) {
		$pre_start_date = date('m/d/Y', strtotime('1-July-' . $current_year));
		$pre_end_date = date('m/d/Y', strtotime('31-September-' . ($current_year)));
	}
	// QUERY FOR PRE Quater'S Renewals
	$pre_quater_sql_orders = "SELECT sum(grand_total) as pre_quater_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_end_date)) . "'";
	$pre_quater_res_orders = $pdo->selectOne($pre_quater_sql_orders);

	$quater_per_value = $quater_res_orders['quater_renewals'] - $pre_quater_res_orders['pre_quater_renewals'];

	if ($pre_quater_res_orders['pre_quater_renewals'] > 0) {
		$quater_per = ($quater_per_value / $pre_quater_res_orders['pre_quater_renewals']) * 100;
	} else {
		$quater_per = $quater_res_orders['quater_renewals'];
	}

	$res['quater_renewals'] = $quater_res_orders['quater_renewals'];
	$res['pre_quater_renewals'] = $pre_quater_res_orders['pre_quater_renewals'];
	$res['quater_per'] = $quater_per;
	return $res;
}
function renewals_year() {
	global $pdo;
	$current_month = date('m');
	$current_year = date('Y');

	$year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year));
	$year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year));

	$pre_year_start_date = date('m/d/Y', strtotime('1-January-' . $current_year . '- 1 year'));
	$pre_year_end_date = date('m/d/Y', strtotime('31-December-' . $current_year . '- 1 year'));

	$incr_type = " AND (type=',Renewals,' OR type=',Subscription Order,' OR type=',Website Subscriptions,') AND is_renewal='Y'";

	$year_sql_orders = "SELECT sum(grand_total) as year_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($year_end_date)) . "'";
	$year_res_orders = $pdo->selectOne($year_sql_orders);

	$pre_year_sql_orders = "SELECT sum(grand_total) as pre_year_renewals "
	. "FROM orders "
	. "WHERE id>0 AND status='Payment Approved'" . $incr_type . " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($pre_year_start_date)) . "' AND DATE(created_at) <='" . date('Y-m-d', strtotime($pre_year_end_date)) . "'";
	$pre_year_res_orders = $pdo->selectOne($pre_year_sql_orders);

	$year_per_value = $year_res_orders['year_renewals'] - $pre_year_res_orders['pre_year_renewals'];

	if ($pre_year_res_orders['pre_year_renewals'] > 0) {
		$year_per = ($year_per_value / $pre_year_res_orders['pre_year_renewals']) * 100;
	} else {
		$year_per = $year_res_orders['year_renewals'];
	}

	$res['year_renewals'] = $year_res_orders['year_renewals'];
	$res['pre_year_renewals'] = $pre_year_res_orders['pre_year_renewals'];
	$res['year_per'] = $year_per;
	return $res;
}

// For Renewals Report Functions End

// For Report HTML content for Send Emails Start

// All Lead Report Function (Reportr ID = 10)
function all_leads_data($daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;

	$lead_res = $pdo->select("SELECT CONCAT(c.fname,' ',c.lname) as c_name,count(l.id) as total_leads
            FROM customer c
            LEFT JOIN leads l ON(c.id = l.sponsor_id AND l.opt_in_type != 'Converted')
            WHERE c.is_deleted='N' AND c.status='Active' GROUP BY c.id");

	$all_leads_array = array('c_name', 'leads');
	$report_th_data = array('Name', 'Total Leads');

	if (count($lead_res) > 0) {
		foreach ($lead_res as $res) {
			$name = $res['c_name'] != "" ? $res['c_name'] : "";
			$total_leads = $res['total_leads'] != "" ? $res['total_leads'] : 0;

			$trigger_param[] = array(
				'name' => $name,
				'total_leads' => $total_leads,
			);
		}
		$sendEmailSummary = $trigger_param;
		$report_link = $HOST . "/agentra-global-admin/all_leads_report.php";
		if ($daily_schedule_res != "") {
			$report_heading = "All Leads Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
			$subject = "Agentra Global Admin : All Leads Daily Report";
		} else if ($monToFri_schedule_res != "") {
			$report_heading = "All Leads Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
			$subject = "Agentra Global Admin : All Leads Monday - Friday Report";
		} else if ($weekly_schedule_res != "") {
			$report_heading = "All Leads Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
			$subject = "Agentra Global Admin : All Leads Weekly Report";
		} else if ($monthly_schedule_res != "") {
			$report_heading = "All Leads Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
			$subject = "Agentra Global Admin : All Leads Monthly Report";
		}
		if (is_array($sendEmailSummary)) {
			foreach ($sendEmailSummary as $placeholder => $value) {
				if (is_array($value)) {
					if (!$hasCopied) {

						$table_head .= "\r\n" . "<tr><td colspan='4' bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:10px 0;'>";
						$table_head .= "\r\n" . $report_heading . "</td></tr><tr><td colspan='4'> </td></tr>";

						$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:20px 0;'>";
						foreach ($report_th_data as $th) {
							$table_data .= "\r\n" . "<th style='background-color:2c94d1;color:#ffffff'> " . $th . "</th>";
						}
						$table_head .= "\r\n" . "</tr>";
						$hasCopied = true;
					}
					$table_data .= "\r\n" . "<tr> ";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> " . $value['name'] . "</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> " . $value['total_leads'] . "</td>";
				} else {
					$str_key = ucwords(str_replace('_', ' ', $placeholder));
					$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
				}

			}
			if (!empty($table_head)) {
				$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
				$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
				$table_data .= "\r\n" . "</td></tr> ";
				$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
			}
		} else {
			$message['html'] = $sendEmailSummary;
		}
		$message['subject'] = $subject;
		return $message;
	}
}

// Affiliate Lead Report Function (Reportr ID = 11)
function affiliate_leads_data($daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;
	$lead_res = $pdo->select("SELECT CONCAT(c.fname,' ',c.lname) as affiliate_name,count(l.id) as total_leads
        FROM customer c
        LEFT JOIN leads l ON(c.id = l.sponsor_id AND l.opt_in_type != 'Converted')
        WHERE c.type='Affiliates' AND c.is_deleted='N' AND c.status='Active' GROUP BY c.id");
	$all_leads_array = array('affiliate_name', 'leads');
	$report_th_data = array('Name', 'Total Leads');

	if (count($lead_res) > 0) {
		foreach ($lead_res as $res) {
			$name = $res['affiliate_name'] != "" ? $res['affiliate_name'] : "";
			$total_leads = $res['total_leads'] != "" ? $res['total_leads'] : 0;

			$trigger_param[] = array(
				'name' => $name,
				'total_leads' => $total_leads,
			);
		}
		$sendEmailSummary = $trigger_param;
		$report_link = $HOST . "/agentra-global-admin/affiliate_leads_report.php";
		if ($daily_schedule_res != "") {
			$report_heading = "Affiliate Leads Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Affiliate Leads Daily Report";
		} else if ($monToFri_schedule_res != "") {
			$report_heading = "Affiliate Leads Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Affiliate Leads Monday - Friday Report";
		} else if ($weekly_schedule_res != "") {
			$report_heading = "Affiliate Leads Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Affiliate Leads Weekly Report";
		} else if ($monthly_schedule_res != "") {
			$report_heading = "Affiliate Leads Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Affiliate Leads Monthly Report";
		}
		if (is_array($sendEmailSummary)) {
			foreach ($sendEmailSummary as $placeholder => $value) {
				if (is_array($value)) {
					if (!$hasCopied) {

						$table_head .= "\r\n" . "<tr><td colspan='4' bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:10px 0;'>";
						$table_head .= "\r\n" . $report_heading . "</td></tr><tr><td colspan='4'> </td></tr>";

						$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:20px 0;'>";
						foreach ($report_th_data as $th) {
							$table_data .= "\r\n" . "<th style='background-color:2c94d1;color:#ffffff'> " . $th . "</th>";
						}
						$table_head .= "\r\n" . "</tr>";
						$hasCopied = true;
					}
					$table_data .= "\r\n" . "<tr> ";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> " . $value['name'] . "</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> " . $value['total_leads'] . "</td>";
				} else {
					$str_key = ucwords(str_replace('_', ' ', $placeholder));
					$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
				}

			}
			if (!empty($table_head)) {
				$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
				$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
				$table_data .= "\r\n" . "</td></tr> ";
				$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
			}
		} else {
			$message['html'] = $sendEmailSummary;
		}
		$message['subject'] = $subject;
		return $message;
	}
}

// Agent Lead Report Function (Reportr ID = 12)
function agent_leads_data($daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;
	$lead_res = $pdo->select("SELECT CONCAT(c.fname,' ',c.lname) as agent_name,count(l.id) as total_leads
        FROM customer c
        LEFT JOIN leads l ON(c.id = l.sponsor_id AND l.opt_in_type != 'Converted')
        WHERE c.type='Agent' AND c.is_deleted='N' AND c.status='Active' GROUP BY c.id");
	$all_leads_array = array('agent_name', 'leads');
	$report_th_data = array('Name', 'Total Leads');

	if (count($lead_res) > 0) {
		foreach ($lead_res as $res) {
			$name = $res['agent_name'] != "" ? $res['agent_name'] : "";
			$total_leads = $res['total_leads'] != "" ? $res['total_leads'] : 0;

			$trigger_param[] = array(
				'name' => $name,
				'total_leads' => $total_leads,
			);
		}
		$sendEmailSummary = $trigger_param;
		$report_link = $HOST . "/agentra-global-admin/agent_leads_report.php";
		if ($daily_schedule_res != "") {
			$report_heading = "Agent Leads Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Agent Leads Daily Report";
		} else if ($monToFri_schedule_res != "") {
			$report_heading = "Agent Leads Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Agent Leads Monday - Friday Report";
		} else if ($weekly_schedule_res != "") {
			$report_heading = "Agent Leads Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Agent Leads Weekly Report";
		} else if ($monthly_schedule_res != "") {
			$report_heading = "Agent Leads Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
			$subject = "Agentra Global Admin : Agent Leads Monthly Report";
		}
		if (is_array($sendEmailSummary)) {
			foreach ($sendEmailSummary as $placeholder => $value) {
				if (is_array($value)) {
					if (!$hasCopied) {

						$table_head .= "\r\n" . "<tr><td colspan='4' bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:10px 0;'>";
						$table_head .= "\r\n" . $report_heading . "</td></tr><tr><td colspan='4'> </td></tr>";

						$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:20px 0;'>";
						foreach ($report_th_data as $th) {
							$table_data .= "\r\n" . "<th style='background-color:2c94d1;color:#ffffff'> " . $th . "</th>";
						}
						$table_head .= "\r\n" . "</tr>";
						$hasCopied = true;
					}
					$table_data .= "\r\n" . "<tr> ";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> " . $value['name'] . "</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> " . $value['total_leads'] . "</td>";
				} else {
					$str_key = ucwords(str_replace('_', ' ', $placeholder));
					$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
				}

			}
			if (!empty($table_head)) {
				$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
				$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
				$table_data .= "\r\n" . "</td></tr> ";
				$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
			}
		} else {
			$message['html'] = $sendEmailSummary;
		}
		$message['subject'] = $subject;
		return $message;
	}
}

// New Business Report Function (Reportr ID = 13)
function new_business_reports_data($time = '', $daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;

	$UP_ICON = $HOST . "/images/up_arrow.png";
	$DOWN_ICON = $HOST . "/images/down_arrow.png";

	// TODAY'S New Business
	$today_new_business_res = new_business_today($time);

	// WEEK'S New Business
	$week_new_business_res = new_business_week($time);

	// MONTH'S New Business
	$month_new_business_res = new_business_month($time);

	// Quater New Business
	$quater_new_business_res = new_business_quater($time);

	// Year New Business
	$year_new_business_res = new_business_year($time);

	$all_report_array = array('Sales', 'This', 'Previous');
	$sales = array("Today Sales", "This Week Sales", "This Month Sales", "This Quater Sales", "This Year Sales");
	$this_sales = array(
		$today_new_business_res['today_new_business'] != "" ? $today_new_business_res['today_new_business'] : 0,
		$week_new_business_res['week_new_business'] != "" ? $week_new_business_res['week_new_business'] : 0,
		$month_new_business_res['month_new_business'] != "" ? $month_new_business_res['month_new_business'] : 0,
		$quater_new_business_res['quater_new_business'] != "" ? $quater_new_business_res['quater_new_business'] : 0,
		$year_new_business_res['year_new_business'] != "" ? $year_new_business_res['year_new_business'] : 0,
	);

	$pre_sales = array(
		$today_new_business_res['yesterday_new_business'] != "" ? $today_new_business_res['yesterday_new_business'] : 0,
		$week_new_business_res['pre_week_new_business'] != "" ? $week_new_business_res['pre_week_new_business'] : 0,
		$month_new_business_res['pre_month_new_business'] != "" ? $month_new_business_res['pre_month_new_business'] : 0,
		$quater_new_business_res['pre_quater_new_business'] != "" ? $quater_new_business_res['pre_quater_new_business'] : 0,
		$year_new_business_res['pre_year_new_business'] != "" ? $year_new_business_res['pre_year_new_business'] : 0,
	);

	$sales_per = array(
		round($today_new_business_res['today_per'], 2),
		round($week_new_business_res['week_per'], 2),
		round($month_new_business_res['month_per'], 2),
		round($quater_new_business_res['quater_per'], 2),
		round($year_new_business_res['year_per'], 2),
	);

	$pre_text = array(
		"Yesterday's Sales",
		"Previous Week Sales",
		"Previous Month Sales",
		"Previous Quater Sales",
		"Previous Fiscal Year Sales",
	);
	foreach ($sales as $key => $row) {
		foreach ($all_report_array as $res) {
			if ($res == "Sales") {
				$sales_arr = $row;
			} else if ($res == "This") {
				$this_arr = $this_sales[$key];
			} else if ($res == "Previous") {
				if ($sales_per[$key] == 0) {
					$icon = "";
				} else if ($sales_per[$key] > 0) {
					$icon = "<img src='" . $UP_ICON . "' align='texttop' width='20' height='20' border='0' />";
				} else if ($sales_per[$key] < 0) {
					$icon = "<img src='" . $DOWN_ICON . "' align='texttop' width='20' height='20' border='0' />";
				}
				$last_arr['icon'] = $icon;
				$last_arr['sales'] = $pre_sales[$key];
				$last_arr['text'] = $pre_text[$key];
				$last_arr['Percentage'] = $sales_per[$key] . "%";
			}
		}
		$trigger_param[] = array(
			'Sales' => $sales_arr,
			'This' => $this_arr,
			'Prev' => $last_arr,
		);
	}
	$sendEmailSummary = $trigger_param;
	// pre_print($sendEmailSummary);
	$report_link = $HOST . "/agentra-global-admin/new_business_report.php";
	if ($daily_schedule_res != "") {
		$report_heading = "New Business Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
		$subject = "Agentra Global Admin : New Business Daily Report";
	} else if ($monToFri_schedule_res != "") {
		$report_heading = "New Business Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
		$subject = "Agentra Global Admin : New Business Monday - Friday Report";
	} else if ($weekly_schedule_res != "") {
		$report_heading = "New Business Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
		$subject = "Agentra Global Admin : New Business Weekly Report";
	} else if ($monthly_schedule_res != "") {
		$report_heading = "New Business Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
		$subject = "Agentra Global Admin : New Business Monthly Report";
	}
	if (is_array($sendEmailSummary)) {
		foreach ($sendEmailSummary as $placeholder => $value) {
			if (is_array($value)) {
				if (!$hasCopied) {
					$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; color:#ffffff; padding:20px 0;'><td colspan='3'>";
					$table_head .= "\r\n" . $report_heading . "</td></tr>";
					$hasCopied = true;
				}
				$table_data .= "\r\n" . "<tr> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>" . $value['Sales'] . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:24px;  color:#000000;' width='30%' align='left'>" . displayAmount($value['This'], 2) . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='40%' align='right'>" . $value['Prev']['icon'] . $value['Prev']['Percentage'] . "<br />" .
				"<span style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#666666; line-height:28px;'>" . $value['Prev']['text'] . ' : ' . displayAmount($value['Prev']['sales'], 2) . "</span>" . "</td> ";
				$table_data .= "\r\n" . "</tr> ";
			} else {
				$str_key = ucwords(str_replace('_', ' ', $placeholder));
				$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
			}

		}
		if (!empty($table_head)) {
			$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
			$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
			$table_data .= "\r\n" . "</td></tr> ";
			$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
		}
	} else {
		$message['html'] = $sendEmailSummary;
	}
	$message['subject'] = $subject;
	return $message;
}

// Renewals Report Function (Reportr ID = 14)
function renewals_reports_data($time = "", $daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;

	$UP_ICON = $HOST . "/images/up_arrow.png";
	$DOWN_ICON = $HOST . "/images/down_arrow.png";

	// TODAY'S Renewals
	$today_renewals_res = renewals_today($time);

	// WEEK'S Renewals
	$week_renewals_res = renewals_week($time);

	// MONTH'S Renewals
	$month_renewals_res = renewals_month($time);

	// Quater Renewals
	$quater_renewals_res = renewals_quater($time);

	// Year Renewals
	$year_renewals_res = renewals_year($time);
	// pre_print($year_renewals_res);

	$all_report_array = array('Sales', 'This', 'Previous');
	$sales = array("Today Sales", "This Week Sales", "This Month Sales", "This Quater Sales", "This Year Sales");
	$this_sales = array(
		$today_renewals_res['today_renewals'] != "" ? $today_renewals_res['today_renewals'] : 0,
		$week_renewals_res['week_renewals'] != "" ? $week_renewals_res['week_renewals'] : 0,
		$month_renewals_res['month_renewals'] != "" ? $month_renewals_res['month_renewals'] : 0,
		$quater_renewals_res['quater_renewals'] != "" ? $quater_renewals_res['quater_renewals'] : 0,
		$year_renewals_res['year_renewals'] != "" ? $year_renewals_res['year_renewals'] : 0,
	);

	$pre_sales = array(
		$today_renewals_res['yesterday_renewals'] != "" ? $today_renewals_res['yesterday_renewals'] : 0,
		$week_renewals_res['pre_week_renewals'] != "" ? $week_renewals_res['pre_week_renewals'] : 0,
		$month_renewals_res['pre_month_renewals'] != "" ? $month_renewals_res['pre_month_renewals'] : 0,
		$quater_renewals_res['pre_quater_renewals'] != "" ? $quater_renewals_res['pre_quater_renewals'] : 0,
		$year_renewals_res['pre_year_renewals'] != "" ? $year_renewals_res['pre_year_renewals'] : 0,
	);
	$sales_per = array(
		round($today_renewals_res['today_per'], 2),
		round($week_renewals_res['week_per'], 2),
		round($month_renewals_res['month_per'], 2),
		round($quater_renewals_res['quater_per'], 2),
		round($year_renewals_res['year_per'], 2),
	);

	$pre_text = array(
		"Yesterday's Sales",
		"Previous Week Sales",
		"Previous Month Sales",
		"Previous Quater Sales",
		"Previous Fiscal Year Sales",
	);

	foreach ($sales as $key => $row) {
		foreach ($all_report_array as $res) {
			if ($res == "Sales") {
				$sales_arr = $row;
			} else if ($res == "This") {
				$this_arr = $this_sales[$key];
			} else if ($res == "Previous") {
				if ($sales_per[$key] == 0) {
					$icon = "";
				} else if ($sales_per[$key] > 0) {
					$icon = "<img src='" . $UP_ICON . "' align='texttop' width='20' height='20' border='0' />";
				} else if ($sales_per[$key] < 0) {
					$icon = "<img src='" . $DOWN_ICON . "' align='texttop' width='20' height='20' border='0' />";
				}
				$last_arr['icon'] = $icon;
				$last_arr['sales'] = $pre_sales[$key];
				$last_arr['text'] = $pre_text[$key];
				$last_arr['Percentage'] = $sales_per[$key] . "%";
			}
		}
		$trigger_param[] = array(
			'Sales' => $sales_arr,
			'This' => $this_arr,
			'Prev' => $last_arr,
		);
	}
	$sendEmailSummary = $trigger_param;
	$report_link = $HOST . "/agentra-global-admin/renewals_report.php";
	if ($daily_schedule_res != "") {
		$report_heading = "Renewals Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
		$subject = "Agentra Global Admin : Renewals Daily Report";
	} else if ($monToFri_schedule_res != "") {
		$report_heading = "Renewals Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
		$subject = "Agentra Global Admin : Renewals Monday - Friday Report";
	} else if ($weekly_schedule_res != "") {
		$report_heading = "Renewals Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
		$subject = "Agentra Global Admin : Renewals Weekly Report";
	} else if ($monthly_schedule_res != "") {
		$report_heading = "Renewals Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
		$subject = "Agentra Global Admin : Renewals Monthly Report";
	}
	if (is_array($sendEmailSummary)) {
		foreach ($sendEmailSummary as $placeholder => $value) {
			if (is_array($value)) {
				if (!$hasCopied) {
					$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; color:#ffffff; padding:20px 0;'><td colspan='3'>";
					$table_head .= "\r\n" . $report_heading . "</td></tr>";
					$hasCopied = true;
				}
				$table_data .= "\r\n" . "<tr> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>" . $value['Sales'] . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:24px;  color:#000000;' width='30%' align='left'>" . displayAmount($value['This'], 2) . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='40%' align='right'>" . $value['Prev']['icon'] . $value['Prev']['Percentage'] . "<br />" .
				"<span style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#666666; line-height:28px;'>" . $value['Prev']['text'] . ' : ' . displayAmount($value['Prev']['sales'], 2) . "</span>" . "</td> ";
				$table_data .= "\r\n" . "</tr> ";
			} else {
				$str_key = ucwords(str_replace('_', ' ', $placeholder));
				$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
			}

		}
		if (!empty($table_head)) {
			$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
			$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
			$table_data .= "\r\n" . "</td></tr> ";
			$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
		}
	} else {
		$message['html'] = $sendEmailSummary;
	}
	$message['subject'] = $subject;
	return $message;
}
// For Report HTML content for Send Emails End

// For Business Summary Report Start

//For Today Enroll Total Members Start
function enroll_members_today($date = '') {
	global $pdo;

	if ($date != "") {
		/* $today_incr = " AND DATE(created_at) >= '" . date("Y-m-d", strtotime($date)) . "' AND DATE(created_at) <='" . date("Y-m-d", strtotime($date)) . "'";*/

		$today_incr = " AND DATE(created_at) = '" . date("Y-m-d", strtotime($date)) . "'";

		$pre_day_incr = " AND DATE(created_at) = '" . date('Y-m-d', strtotime('-1 day', strtotime($date))) . "'";
	} else {
		/*$today_incr = " AND DATE(created_at) >= '" . DATE('Y-m-d') . "' AND DATE(created_at) <='" . DATE('Y-m-d') . "'";*/
		$today_incr = " AND DATE(created_at) = '" . date("Y-m-d", strtotime(date('Y-m-d'))) . "'";
		$pre_day_incr = " AND DATE(created_at) = CURDATE()-1";
	}

	// QUERY FOR TODAY'S ENROLLS
	$day_sql_enrolls = "SELECT COUNT(id) as today_enrolls
                      FROM customer
                      WHERE type='Customer' AND is_deleted = 'N' AND status ='Active' " . $today_incr;
	$day_res_enrolls = $pdo->selectOne($day_sql_enrolls);

	// QUERY  FOR Yeasterday'S ENROLLS
	$pre_day_sql_enrolls = "SELECT COUNT(id) as yesterday_enrolls
                        FROM customer
                        WHERE type='Customer' AND id>0 AND is_deleted = 'N' AND status ='Active' " . $pre_day_incr;
	$pre_day_res_enrolls = $pdo->selectOne($pre_day_sql_enrolls);

	// Calculate percentage
	$today_per_value = $day_res_enrolls['today_enrolls'] - $pre_day_res_enrolls['yesterday_enrolls'];
	if ($pre_day_res_enrolls['yesterday_enrolls'] > 0) {
		$today_per = ($today_per_value / $pre_day_res_enrolls['yesterday_enrolls']) * 100;
	} else {
		$today_per = $day_res_enrolls['today_enrolls'];
	}

	$res['today_enrolls'] = $day_res_enrolls['today_enrolls'];
	$res['yesterday_enrolls'] = $pre_day_res_enrolls['yesterday_enrolls'];
	$res['today_per'] = $today_per;
	// pre_print($res);
	return $res;
}
//For Today Enroll Total Members End

//For Top 5 New Business Progress Sales Today Start
function top_five_product_sales_today($date = '') {
	global $pdo;

	if ($date != "") {
		$today_incr = " AND date(o.created_at) >= '" . date('Y-m-d', strtotime($date)) . "' AND DATE(o.created_at) <= '" . date('Y-m-d', strtotime($date)) . "'";
	} else {
		$today_incr = " AND date(o.created_at) >= '" . date('Y-m-d') . "' AND DATE(o.created_at) <= '" . date('Y-m-d') . "'";
	}

	$product_sql = "SELECT p.parent_product_id,p.id as product_id,p.name as product_name,ord.total_sales,ord.total_sold ,p.product_code as product_code,
      IF(p.parent_product_id>0, p.parent_product_id, p.id) as gp_id
                  FROM prd_main p
                  JOIN
                    (
                      SELECT od.product_id,sum(od.unit_price*od.qty) as total_sales,count(od.id) as total_sold
                      FROM order_details od
                      JOIN orders as o ON(o.id=od.order_id " . $today_incr . ")
                      WHERE o.status='Payment Approved' AND od.is_deleted='N'
                      GROUP BY od.product_id
                    ) as ord ON ord.product_id=p.id
                  WHERE p.is_deleted='N' AND status='Active' AND ord.total_sold!=0
                  GROUP BY  gp_id ORDER BY total_sales DESC LIMIT 5";

	$product_res = $pdo->select($product_sql);

	$totalSales = array();

	foreach ($product_res as $key => $rows) {
		if ($rows['parent_product_id'] != 0) {

			$totalSales[$rows['parent_product_id']]['p_name'] = $rows['product_name'];
			$totalSales[$rows['parent_product_id']]['total'] = $totalSales[$rows['parent_product_id']]['total'] + $rows['total_sales'];
		} else {
			$totalSales[$rows['product_id']]['p_name'] = $rows['product_name'];
			$totalSales[$rows['product_id']]['total'] = $totalSales[$rows['product_id']]['total'] + $rows['total_sales'];
		}
	}

	/*if($time!=""){

		    $current_time = $time;
		    $current_date = DATE('Y-m-d'). ' ' . $current_time;
		    $yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date." -24 HOUR"));
		    $yesterday_date  = DATE('Y-m-d',(strtotime ('-1 day',strtotime($current_date)))). ' ' . $current_time; ;
		    $previousday_day = DATE('Y-m-d',(strtotime ('-1 day',strtotime($yesterday_date)))). ' ' . $current_time;

		    $product_sql = "SELECT p.parent_product_id,p.id as product_id,p.name as product_name,ord.total_sales,ord.total_sold ,p.product_code as product_code
		    FROM prd_main p
		    JOIN
		    (
		    SELECT od.product_id,sum(od.unit_price*od.qty) as total_sales,count(od.id) as total_sold
		    FROM order_details od
		    JOIN orders as o ON(o.id=od.order_id AND STR_TO_DATE(o.created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(o.created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "')
		    WHERE o.status='Payment Approved'
		    GROUP BY od.product_id
		    ) as ord ON ord.product_id=p.id
		    WHERE p.is_deleted='N' AND status='Active' AND ord.total_sold!=0 GROUP BY p.id ORDER BY total_sales DESC LIMIT 5";
		    $product_res = $pdo->select($product_sql);
	*/

	return $totalSales;
}

//For Top 5 New Business Progress Sales Today End

//For Top 5 Referring Agent Today Start
function top_five_referring_agent_today($date = '') {
	global $pdo;

	if ($date != "") {
		$today_incr = " AND DATE(c.created_at) >= '" . date('Y-m-d', strtotime($date)) . "' AND DATE(c.created_at) <= '" . date('Y-m-d', strtotime($date)) . "' ";
	} else {
		$today_incr = " AND DATE(c.created_at) >= '" . date('Y-m-d') . "' AND DATE(c.created_at) <= '" . date('Y-m-d') . "' ";
	}

	$order_sql = "SELECT count(c.id) as total,s.id as agent_id, CONCAT(s.fname,' ',s.lname) as agent_name,s.business_name,sum(o.grand_total) as total_sales
                FROM customer as c
                JOIN customer as s ON(s.id = c.sponsor_id)
                JOIN orders as o ON(o.customer_id=c.id)
                WHERE c.type='Customer' AND s.type='Agent' AND c.status='Active' AND o.status='Payment Approved' " . $today_incr . "GROUP BY s.id Order BY total DESC LIMIT 5 ";
	$order_res = $pdo->select($order_sql);

	$today_sales = array();
	if (count($order_res) > 0) {
		foreach ($order_res as $val) {
			$name = $val['business_name'] != "" ? $val['business_name'] : $val['agent_name'];
			$today_sales[$val['agent_id']]['agent_name'] .= $name;
			$today_sales[$val['agent_id']]['total'] .= $val['total'];
			$today_sales[$val['agent_id']]['total_sales'] .= $val['total_sales'];
		}
	}
	/*if($time!=''){
		    $current_time = $time;
		    $current_date = DATE('Y-m-d'). ' ' . $current_time;
		    $yesterday_date = DATE("Y-m-d H:m:s", strtotime($current_date." -24 HOUR"));
		    $yesterday_date  = DATE('Y-m-d',(strtotime ('-1 day',strtotime($current_date)))). ' ' . $current_time; ;
		    $previousday_day = DATE('Y-m-d',(strtotime ('-1 day',strtotime($yesterday_date)))). ' ' . $current_time;

		    $order_sql ="SELECT count(c.id) as total, CONCAT(s.fname,' ',s.lname) as agent_name,s.business_name
		    FROM customer as c
		    JOIN customer as s ON(s.id = c.sponsor_id)
		    WHERE c.type='Customer' AND s.type='Agent' AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') <= '" . date($current_date) . "' AND STR_TO_DATE(c.created_at, '%Y-%m-%d %H:%i:%s') >='" . date($yesterday_date) . "' AND c.status='Active' GROUP BY s.id Order BY total DESC LIMIT 5 ";
		    $order_res = $pdo->select($order_sql);

	*/

	return $today_sales;
}
//For Top 5 Referring Agent Today End

function bussiness_summary_reports_data($time, $daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;

	$UP_ICON = $HOST . "/images/up_arrow.png";
	$DOWN_ICON = $HOST . "/images/down_arrow.png";

	$today_sales_res = all_sales_today($time);
	$new_business_sales_res = new_business_today();
	$renewal_sales_res = renewals_today();
	$new_member_today_res = enroll_members_today();
	// $total_active_member_res = all_members_today();
	$top_five_business_sales = top_five_product_sales_today($time);
	$top_five_referring_agent_today = top_five_referring_agent_today($time);

	$all_report_array = array('Sales', 'This', 'Previous');
	$sales = array("Today Sales", "New Business Sales", "Renewal Sales", "New Members Today");
	// $sales = array("Today Sales","New Business Sales","Renewal Sales", "New Members Today","Total Active Member");

	$this_sales = array(
		$today_sales_res['today_sales'] != "" ? '$' . $today_sales_res['today_sales'] : '$0',
		$new_business_sales_res['today_new_business'] != "" ? '$' . $new_business_sales_res['today_new_business'] : '$0',
		$renewal_sales_res['today_renewals'] != "" ? '$' . $renewal_sales_res['today_renewals'] : '$0',
		$new_member_today_res['today_enrolls'] != "" ? $new_member_today_res['today_enrolls'] : 0,
		// $total_active_member_res['today_enrolls']!="" ? $total_active_member_res['today_enrolls'] : 0
	);

	$pre_sales = array(
		$today_sales_res['yesterday_sales'] != "" ? '$' . $today_sales_res['yesterday_sales'] : '$0',
		$new_business_sales_res['yesterday_new_business'] != "" ? '$' . $new_business_sales_res['yesterday_new_business'] : '$0',
		$renewal_sales_res['yesterday_renewals'] != "" ? '$' . $renewal_sales_res['yesterday_renewals'] : '$0',
		$new_member_today_res['yesterday_enrolls'] != "" ? $new_member_today_res['yesterday_enrolls'] : 0,
		// $total_active_member_res['yesterday_enrolls']!="" ? $total_active_member_res['yesterday_enrolls'] :0
	);
	$sales_per = array(
		round($today_sales_res['today_per'], 2),
		round($new_business_sales_res['today_per'], 2),
		round($renewal_sales_res['today_per'], 2),
		round($new_member_today_res['today_per'], 2),
		// round($total_active_member_res['today_per'],2)
	);

	$pre_text = array(
		"Yesterday's Total Sales",
		"Yesterday's New Business Sales",
		"Yesterday's Renewal Sales",
		"Yesterday's New Members",
		// "Yesterday's Active Members"
	);

	foreach ($sales as $key => $row) {
		foreach ($all_report_array as $res) {
			if ($res == "Sales") {
				$sales_arr = $row;
			} else if ($res == "This") {
				$this_arr = $this_sales[$key];
			} else if ($res == "Previous") {
				if ($sales_per[$key] == 0) {
					$icon = "";
				} else if ($sales_per[$key] > 0) {
					$icon = "<img src='" . $UP_ICON . "' align='texttop' width='20' height='20' border='0' />";
				} else if ($sales_per[$key] < 0) {
					$icon = "<img src='" . $DOWN_ICON . "' align='texttop' width='20' height='20' border='0' />";
				}
				$last_arr['icon'] = $icon;
				$last_arr['sales'] = $pre_sales[$key];
				$last_arr['text'] = $pre_text[$key];
				$last_arr['Percentage'] = $sales_per[$key] . "%";
			}
		}
		$trigger_param[] = array(
			'Sales' => $sales_arr,
			'This' => $this_arr,
			'Prev' => $last_arr,
		);

	}
	$sendEmailSummary = $trigger_param;

	$report_link = $HOST . "/agentra-global-admin/business_summary_reports.php";

	if ($daily_schedule_res != "") {
		$report_heading = "Daily Summary Report <small>@ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'] . " " . date('m/d/y') . "</small>";
		//  $subject = "Business Summary Daily Report";
		$subject = "Daily Summary Report";
	} else if ($monToFri_schedule_res != "") {
		$report_heading = "Daily Summary Report Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
		$subject = "Business Summary Monday - Friday Report";
	} else if ($weekly_schedule_res != "") {
		$report_heading = "Daily Summary Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
		$subject = "Business Summary Weekly Report";
	} else if ($monthly_schedule_res != "") {
		$report_heading = "Daily Summary Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
		$subject = "Business Summary Monthly Report";
	}
	if (is_array($sendEmailSummary)) {
		foreach ($sendEmailSummary as $placeholder => $value) {
			if (is_array($value)) {
				if (!$hasCopied) {
					$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; color:#ffffff; padding:20px 0;'><td colspan='3'>";
					$table_head .= "\r\n" . $report_heading . "</td></tr>";
					$hasCopied = true;
				}
				$table_data .= "\r\n" . "<tr> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>" . $value['Sales'] . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:24px;  color:#000000;' width='30%' align='left'>" . $value['This'] . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='40%' align='right'> " . $value['Prev']['icon'] . $value['Prev']['Percentage'] . "<br />" .
					"<span style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#666666; line-height:28px;'>" . $value['Prev']['text'] . ' : ' . $value['Prev']['sales'] . "</span>" . "</td> ";
				$table_data .= "\r\n" . "</tr> ";
			} else {
				$str_key = ucwords(str_replace('_', ' ', $placeholder));
				$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
			}

		}
		$table_data .= "\r\n <tr><td colspan='3'>";

		// if(count($top_five_business_sales)>0 || count($top_five_referring_agent_today)>0){

		$table_data .= "\r\n <table width='49%' style='float:left;' cellpadding='10'>";
		$table_data .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#ffffff; padding:20px 0;'><td colspan='2'>";
		$table_data .= "\r\n Top 5 New Business Products Sales Today </td></tr>";
		if (count($top_five_business_sales) > 0) {
			foreach ($top_five_business_sales as $res) {
				$table_data .= "\r\n" . "<tr> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>" . $res['p_name'] . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>" . displayAmount($res['total'], 2) . "</td> ";

				$table_data .= "\r\n" . "</tr>";
			}
		} else {
			$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>No Records Found</td> ";
		}
		$table_data .= "\r\n </table>";

		$table_data .= "\r\n <table width='49%'  style=' float:left;' cellpadding='10'>";
		$table_data .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#ffffff; padding:20px 0;'><td colspan='2'>";

		$table_data .= "\r\n Top 5 Referring Agents Today </td></tr>";
		$table_data .= "\r\n <tr>&nbsp;</tr>";
		if (count($top_five_referring_agent_today) > 0) {
			foreach ($top_five_referring_agent_today as $res) {
				$table_data .= "\r\n" . "<tr> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>" . $res['agent_name'] . "</td> ";
				$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>" . displayAmount($res['total_sales'], 2) . "</td> ";

				$table_data .= "\r\n" . "</tr>";
			}
		} else {
			$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='30%' align='left'>No Records Found</td> ";
		}
		$table_data .= "\r\n </table>";
		// }

		$table_data .= "\r\n </td></tr>";
		if (!empty($table_head)) {
			$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
			$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
			$table_data .= "\r\n" . "</td></tr> ";
			$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
		}
	} else {
		$message['html'] = $sendEmailSummary;
	}
	$message['subject'] = $subject;
	return $message;

}

// For Business Summary Report End
// -----------------------------------------------------------------------------------

// Report Schedule For agent code start

function agent_daily_schedule($report_id) {
	global $pdo;
	$res = $pdo->select("SELECT a.*, a.id as schedule_id, CONCAT(c.fname,' ',c.lname), c.email as agent_name
              FROM agent_report_schedule as a
              JOIN customer as c ON (c.id = a.agent_id)
              WHERE a.report_id=:id AND a.is_deleted='N' AND c.is_deleted='N'
              AND a.schedule_type='Daily' AND c.type = 'Agent' AND a.con_date_time<=CURTIME() AND a.con_date_time >= CURTIME() - INTERVAL 10 MINUTE",
		array(":id" => $report_id));
	return $res;
}

function agent_monday_friday_schedule($report_id) {
	global $pdo;
	$current_day = date("N");
	$days_to_friday = 5 - $current_day;
	$days_from_monday = $current_day - 1;
	$monday = date("Y-m-d", strtotime("- {$days_from_monday} Days"));
	$friday = date("Y-m-d", strtotime("+ {$days_to_friday} Days"));

	$res = $pdo->select("SELECT a.*, a.id as schedule_id, CONCAT(c.fname,' ',c.lname) as agent_name, c.email
              FROM agent_report_schedule as a
              JOIN customer as c ON(c.id = a.agent_id)
              WHERE a.report_id=:id AND a.is_deleted='N' AND c.is_deleted='N' AND a.schedule_type='Mon-Friday' AND
              CURDATE()  >= '" . $monday . "' AND CURDATE() <= '" . $friday . "' AND a.con_date_time<=CURTIME() AND a.con_date_time >= CURTIME() - INTERVAL 10 MINUTE", array(":id" => $report_id));
	return $res;
}

function agent_weekly_schedule($report_id) {
	global $pdo;
	$res = $pdo->select("SELECT a.*, a.id as schedule_id, CONCAT(c.fname,' ',c.lname) as agent_name, c.email
              FROM agent_report_schedule as a
              JOIN customer as c ON(c.id = a.agent_id)
              WHERE a.report_id=:id AND  a.day_of_week = (WEEKDAY(CURDATE())+1) AND a.is_deleted='N' AND c.is_deleted='N' AND a.schedule_type='Weekly' AND  a.con_date_time<=CURTIME() AND a.con_date_time >= CURTIME() - INTERVAL 10 MINUTE", array(":id" => $report_id));
	return $res;
}

function agent_monthly_schedule($report_id) {
	global $pdo;
	$res = $pdo->select("SELECT a.*, a.id as schedule_id, CONCAT(c.fname,' ',c.lname) as agent_name, c.email
              FROM agent_report_schedule as a
              JOIN customer as c ON(c.id = a.agent_id)
              WHERE a.report_id=:id AND a.is_deleted='N' AND c.is_deleted='N' AND a.schedule_type='Monthly' AND a.day_of_month=DAYOFMONTH(NOW()) AND a.con_date_time<=CURTIME() AND a.con_date_time >= CURTIME() - INTERVAL 10 MINUTE", array(":id" => $report_id));
	return $res;
}

// Report Schedule For agent code end
// ------------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------
//  For Report HTML content For send emails Agent side Start

// Agent Personal Sales Report Function (Reportr ID = 15)
function agent_personal_sales_data($agent_id, $daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;

	$UP_ICON = $HOST . "/images/up_arrow.png";
	$DOWN_ICON = $HOST . "/images/down_arrow.png";

	$sel = "SELECT GROUP_CONCAT(product_id) as pid FROM agent_product_rule WHERE agent_id=:agent_id";
	$wr = array(":agent_id" => $agent_id);
	$res = $pdo->selectOne($sel, $wr);

	if ($res['pid'] == "") {
		$res['pid'] = '0';
	}

	if ($res['pid'] != "") {
		$incr = " AND p.id IN(" . $res['pid'] . ")";
	}

	$order_sql = "SELECT CONCAT(c.fname,' ',c.lname) as agent_name,cm.total_commission,sum(IF(p.id IS NOT NULL,od.unit_price*od.qty,0)) as total_sales,c.id as agent_id
          FROM customer c
          LEFT JOIN customer c2 ON (c.id=c2.sponsor_id)
          LEFT JOIN orders o ON(c2.id = o.customer_id)
          LEFT JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
          LEFT JOIN prd_main p ON(p.id = od.product_id)
          LEFT JOIN
            (
              SELECT SUM(amount) as total_commission,customer_id
                FROM commission WHERE status!='Pending' AND is_deleted='N' GROUP BY customer_id
            ) AS cm ON (cm.customer_id = c.id)

          WHERE (c.type='Agent' AND c2.type='Customer') AND c.is_deleted='N' AND o.status NOT IN('Returned','Cancelled','Pending Quote','Pending Quotes') AND c.id=:id  $incr GROUP BY c.id";
	$order_where = array(":id" => $agent_id);
	$lead_res = $pdo->select($order_sql, $order_where);

	$all_leads_array = array('agent_name', 'agents');
	$report_th_data = array('Agent Name', 'Growth', 'Total Sales');

	$today_sales = array();
	if (count($lead_res) > 0) {
		foreach ($lead_res as $val) {
			$today_sales[$val['agent_id']]['id'] .= $val['agent_id'];
			$today_sales[$val['agent_id']]['today_sales'] .= $val['total_sales'];
		}
	}

	if (count($lead_res) > 0) {
		foreach ($lead_res as $key => $res) {
			if (isset($today_sales[$res['agent_id']])) {
				$total_sales = $today_sales[$res['agent_id']]['today_sales'];
				$sales_diff = $today_sales[$res['agent_id']]['today_sales'];
				if ($sales_diff != 0 && $total_sales != 0 && $total_sales != '') {
					$sales_per = ($sales_diff) / $total_sales * 100;
				} else {
					$sales_per = 0;
				}
			} else {
				$sales_per = 0;
			}

			$name = $res['agent_name'] != "" ? $res['agent_name'] : "";
			$sales_per = $sales_per != "" ? $sales_per : 0;
			$total_sales = $res['total_sales'] != "" ? $res['total_sales'] : 0;
			$icon = $sales_per >= 0 ? $UP_ICON : $DOWN_ICON;

			$trigger_param[] = array(
				'name' => $name,
				'sales_per' => $sales_per,
				'total_sales' => $total_sales,
				'icon' => $icon,
			);
		}
		$sendEmailSummary = $trigger_param;
		$report_link = $HOST . "/agents/report_personal_sales.php";
		if ($daily_schedule_res != "") {
			$report_heading = "Agent Personal Sales Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
			$subject = "Agents : Agent Personal Sales Daily Report";
		} else if ($monToFri_schedule_res != "") {
			$report_heading = "Agent Personal Sales Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
			$subject = "Agents : Agent Personal Sales Monday - Friday Report";
		} else if ($weekly_schedule_res != "") {
			$report_heading = "Agent Personal Sales Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
			$subject = "Agents : Agent Personal Sales Weekly Report";
		} else if ($monthly_schedule_res != "") {
			$report_heading = "Agent Personal Sales Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
			$subject = "Agents : Agent Personal Sales Monthly Report";
		}
		if (is_array($sendEmailSummary)) {
			foreach ($sendEmailSummary as $placeholder => $value) {
				if (is_array($value)) {
					if (!$hasCopied) {

						$table_head .= "\r\n" . "<tr><td colspan='4' bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:10px 0;'>";
						$table_head .= "\r\n" . $report_heading . "</td></tr><tr><td colspan='4'> </td></tr>";

						$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:20px 0;'>";
						foreach ($report_th_data as $th) {
							$table_data .= "\r\n" . "<th style='background-color:2c94d1;color:#ffffff'> " . $th . "</th>";
						}
						$table_head .= "\r\n" . "</tr>";
						$hasCopied = true;
					}
					$table_data .= "\r\n" . "<tr> ";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> " . $value['name'] . "</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> <i class='fa fa-level-down text-danger text-semibold'> <img src='" . $value['icon'] . "'align='texttop' width='20' height='20' border='0' /> " . round($value['sales_per']) . "%</i></td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> " . displayAmount($value['total_sales'], 2) . "</td>";
				} else {
					$str_key = ucwords(str_replace('_', ' ', $placeholder));
					$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
				}

			}
			if (!empty($table_head)) {
				$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
				$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
				$table_data .= "\r\n" . "</td></tr> ";
				$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
			}
		} else {
			$message['html'] = $sendEmailSummary;
		}
		$message['subject'] = $subject;
		// print_r($message);
		return $message;
	}
}

// Agent Personal Sales Per Product Report Function (Reportr ID = 16)
function agent_personal_data($agent_id, $daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;

	$UP_ICON = $HOST . "/images/up_arrow.png";
	$DOWN_ICON = $HOST . "/images/down_arrow.png";

	$sel = "SELECT GROUP_CONCAT(product_id) as pid FROM agent_product_rule WHERE agent_id=:agent_id";
	$wr = array(":agent_id" => $agent_id);
	$res = $pdo->selectOne($sel, $wr);

	if ($res['pid'] == "") {
		$res['pid'] = '0';
	}

	if ($res['pid'] != "") {
		$incr = " AND p.id IN(" . $res['pid'] . ")";
	}

	$product_sql = "SELECT p.parent_product_id,p.id as product_id,p.name as product_name,ord.total_sales,ord.total_sold ,p.product_code as product_code
                FROM prd_main p
                LEFT JOIN
                  (
                    SELECT od.product_id,sum(od.unit_price*od.qty) as total_sales,count(od.id) as total_sold
                    FROM order_details od
                    JOIN orders as o ON(o.id=od.order_id)
                    LEFT JOIN customer c ON (c.id=o.customer_id)
                    WHERE c.sponsor_id ={$agent_id} AND c.type='Customer' AND o.status NOT IN ('Pending Quote','Pending Quotes') AND od.is_deleted='N'
                    GROUP BY od.product_id
                  ) as ord ON (ord.product_id=p.id)
                WHERE p.is_deleted='N' $incr GROUP BY p.id ORDER BY total_sales DESC";

	$lead_res = $pdo->select($product_sql);

	$totalSales = array();

	foreach ($lead_res as $key => $rows) {
		if ($rows['parent_product_id'] != 0) {
			$totalSales[$rows['parent_product_id']] = $totalSales[$rows['parent_product_id']] + $rows['total_sales'];
		} else {
			$totalSales[$rows['product_id']] = $totalSales[$rows['product_id']] + $rows['total_sales'];
		}
	}

	$today_sales_sql = "SELECT od.product_id, sum(od.unit_price) as today_sales "
	. "FROM orders o "
	. "LEFT JOIN order_details od ON(o.id = od.order_id) "
	. "WHERE DATE(o.created_at) >= '" . date('Y-m-d') . "' AND DATE(o.created_at) <='" . date('Y-m-d') . "' AND o.status NOT IN ('Pending Quote','Pending Quotes') GROUP BY od.product_id";
	$today_sales_res = $pdo->select($today_sales_sql);

	$today_sales = array();
	if (count($today_sales_res) > 0) {
		foreach ($today_sales_res as $val) {
			$today_sales[$val['product_id']]['id'] .= $val['product_id'];
			$today_sales[$val['product_id']]['today_sales'] .= $val['today_sales'];
		}
	}

	$fromdate = date("m/d/Y", strtotime("-1 days"));
	$todate = date('m/d/Y', strtotime("-1 days"));
	$prev_sales_sql = "SELECT od.product_id, sum(od.unit_price) as prev_sales "
	. "FROM orders o "
	. "LEFT JOIN order_details od ON(o.id = od.order_id) "
	. "WHERE DATE(o.created_at) >= '" . date('Y-m-d', strtotime($fromdate)) . "' AND DATE(o.created_at) <='" . date('Y-m-d', strtotime($todate)) . "' AND o.status NOT IN ('Pending Quote','Pending Quotes') GROUP BY od.product_id";
	$prev_sales_res = $pdo->select($prev_sales_sql);

	$prev_sales = array();
	if (count($prev_sales_res) > 0) {
		foreach ($prev_sales_res as $val) {
			$prev_sales[$val['product_id']]['id'] .= $val['product_id'];
			$prev_sales[$val['product_id']]['prev_sales'] .= $val['prev_sales'];
		}
	}

	$all_leads_array = array('agent_name', 'Agents');
	$report_th_data = array('Product', 'Sales', 'total_sales');

	if (count($lead_res) > 0) {
		foreach ($lead_res as $key => $res) {
			if (isset($today_sales[$res['product_id']]) || isset($prev_sales[$res['product_id']])) {
				if ($today_sales[$res['product_id']]['today_sales'] > $prev_sales[$res['product_id']]['prev_sales']) {
					$total_sales = $today_sales[$res['product_id']]['today_sales'];
				} else if ($prev_sales[$res['product_id']]['prev_sales'] > $today_sales[$res['product_id']]['today_sales']) {
					$total_sales = $prev_sales[$res['product_id']]['prev_sales'];
				}
				$sales_diff = $today_sales[$res['product_id']]['today_sales'] - $prev_sales[$res['product_id']]['prev_sales'];
				if ($sales_diff != 0 && $total_sales != 0 && $total_sales != '') {
					$sales_per = (($sales_diff) / $total_sales) * 100;
				} else {
					$sales_per = 0;
				}
			} else {
				$sales_per = 0;
			}

			$name = $res['product_name'] != "" ? $res['product_name'] : "";
			$sales_per = $sales_per != "" ? $sales_per : 0;
			$total_sales = $res['total_sales'] != "" ? $res['total_sales'] : 0;
			$icon = $sales_per >= 0 ? $UP_ICON : $DOWN_ICON;

			$trigger_param[] = array(
				'name' => $name,
				'sales_per' => $sales_per,
				'total_sales' => $total_sales,
				'icon' => $icon,
			);
		}

		$sendEmailSummary = $trigger_param;
		$report_link = $HOST . "/agents/report_sales_per_product.php";
		if ($daily_schedule_res != "") {
			$report_heading = "Agent Sales Per Product Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Product Daily Report";
		} else if ($monToFri_schedule_res != "") {
			$report_heading = "Agent Sales Per Product Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Product Monday - Friday Report";
		} else if ($weekly_schedule_res != "") {
			$report_heading = "Agent Sales Per Product Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Product Weekly Report";
		} else if ($monthly_schedule_res != "") {
			$report_heading = "Agent Sales Per Product Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Product Monthly Report";
		}
		if (is_array($sendEmailSummary)) {
			foreach ($sendEmailSummary as $placeholder => $value) {
				if (is_array($value)) {
					if (!$hasCopied) {

						$table_head .= "\r\n" . "<tr><td colspan='4' bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:10px 0;'>";
						$table_head .= "\r\n" . $report_heading . "</td></tr><tr><td colspan='4'> </td></tr>";

						$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:20px 0;'>";
						foreach ($report_th_data as $th) {
							$table_data .= "\r\n" . "<th style='background-color:2c94d1;color:#ffffff'> " . $th . "</th>";
						}
						$table_head .= "\r\n" . "</tr>";
						$hasCopied = true;
					}
					$table_data .= "\r\n" . "<tr> ";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> " . $value['name'] . "</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> <img src='" . $value['icon'] . "'align='texttop' width='20' height='20' border='0' /> " . round($value['sales_per']) . "%</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> " . displayAmount($value['total_sales'], 2) . "</td>";
				} else {
					$str_key = ucwords(str_replace('_', ' ', $placeholder));
					$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
				}

			}
			if (!empty($table_head)) {
				$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
				$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
				$table_data .= "\r\n" . "</td></tr> ";
				$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
			}
		} else {
			$message['html'] = $sendEmailSummary;
		}
		$message['subject'] = $subject;
		// pre_print($message);
		return $message;
	}
}

// Agent Organization Sales Report Function (Reportr ID = 17)
function agent_organization_sales_data($agent_id, $daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {
	global $pdo, $HOST;

	$UP_ICON = $HOST . "/images/up_arrow.png";
	$DOWN_ICON = $HOST . "/images/down_arrow.png";

	$sel = "SELECT GROUP_CONCAT(product_id) as pid FROM agent_product_rule WHERE agent_id=:agent_id";
	$wr = array(":agent_id" => $agent_id);
	$res = $pdo->selectOne($sel, $wr);

	if ($res['pid'] == "") {
		$res['pid'] = '0';
	}

	if ($res['pid'] != "") {
		$incr = " AND p.id IN(" . $res['pid'] . ")";
	}

	// QUERY FOR AGENT SALES
	$order_sql = "SELECT CONCAT(c.fname,' ',c.lname) as agent_name,cm.total_commission,sum(IF(p.id IS NOT NULL,od.unit_price*od.qty,0)) as total_sales,c.id as agent_id
          FROM customer c
          LEFT JOIN orders o ON(c.id = o.customer_id)
          LEFT JOIN order_details od ON(od.order_id = o.id)
          LEFT JOIN prd_main p ON(p.id = od.product_id)
          LEFT JOIN
            (
              SELECT SUM(amount) as total_commission,customer_id
                FROM commission WHERE status!='Pending' AND is_deleted='N'
                GROUP BY customer_id
            ) AS cm ON (cm.customer_id = c.id)

          WHERE c.type='Customer'  AND c.is_deleted='N' AND o.status NOT IN('Returned','Cancelled','Pending Quote','Pending Quotes') AND  c.upline_sponsors LIKE :upline_sponsors  $incr  GROUP BY c.id";
	$whr = array(':upline_sponsors' => "%," . $agent_id . ",%");
	$lead_res = $pdo->select($order_sql, $whr);

	$all_leads_array = array('agent_name', 'leads');
	$report_th_data = array('Agent Name', 'Growth', 'Total Sales');

	$today_sales = array();
	if (count($lead_res) > 0) {
		foreach ($lead_res as $val) {
			$today_sales[$val['agent_id']]['id'] .= $val['agent_id'];
			$today_sales[$val['agent_id']]['today_sales'] .= $val['total_sales'];
		}
	}

	// foreach ($lead_res as $key => $rows) {
	// }

	if (count($lead_res) > 0) {
		foreach ($lead_res as $key => $res) {
			if (isset($today_sales[$res['agent_id']])) {
				$total_sales = $today_sales[$res['agent_id']]['today_sales'];
				$sales_diff = $today_sales[$res['agent_id']]['today_sales'];
				if ($sales_diff != 0 && $total_sales != 0 && $total_sales != '') {
					$sales_per = ($sales_diff) / $total_sales * 100;
				} else {
					$sales_per = 0;
				}
			} else {
				$sales_per = 0;
			}

			$name = $res['agent_name'] != "" ? $res['agent_name'] : "";
			$sales_per = $sales_per != "" ? $sales_per : 0;
			$total_sales = $res['total_sales'] != "" ? $res['total_sales'] : 0;
			$icon = $sales_per >= 0 ? $UP_ICON : $DOWN_ICON;

			$trigger_param[] = array(
				'name' => $name,
				'sales_per' => $sales_per,
				'total_sales' => $total_sales,
				'icon' => $icon,
			);
		}
		$sendEmailSummary = $trigger_param;
		$report_link = $HOST . "/agents/report_organization_sales.php";
		if ($daily_schedule_res != "") {
			$report_heading = "Agent Organization Sales Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
			$subject = "Agents : Agent Organization Sales Daily Report";
		} else if ($monToFri_schedule_res != "") {
			$report_heading = "Agent Organization Sales Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
			$subject = "Agents : Agent Organization Sales Monday - Friday Report";
		} else if ($weekly_schedule_res != "") {
			$report_heading = "Agent Organization Sales Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
			$subject = "Agents : Agent Organization Sales Weekly Report";
		} else if ($monthly_schedule_res != "") {
			$report_heading = "Agent Organization Sales Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
			$subject = "Agents : Agent Organization Sales Monthly Report";
		}
		if (is_array($sendEmailSummary)) {
			foreach ($sendEmailSummary as $placeholder => $value) {
				if (is_array($value)) {
					if (!$hasCopied) {

						$table_head .= "\r\n" . "<tr><td colspan='4' bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:10px 0;'>";
						$table_head .= "\r\n" . $report_heading . "</td></tr><tr><td colspan='4'> </td></tr>";

						$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:20px 0;'>";
						foreach ($report_th_data as $th) {
							$table_data .= "\r\n" . "<th style='background-color:2c94d1;color:#ffffff'> " . $th . "</th>";
						}
						$table_head .= "\r\n" . "</tr>";
						$hasCopied = true;
					}
					$table_data .= "\r\n" . "<tr> ";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> " . $value['name'] . "</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> <img src='" . $value['icon'] . "'align='texttop' width='20' height='20' border='0' /> " . round($value['sales_per']) . "%</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> " . displayAmount($value['total_sales'], 2) . "</td>";
				} else {
					$str_key = ucwords(str_replace('_', ' ', $placeholder));
					$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
				}

			}
			if (!empty($table_head)) {
				$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
				$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
				$table_data .= "\r\n" . "</td></tr> ";
				$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
			}
		} else {
			$message['html'] = $sendEmailSummary;
		}
		$message['subject'] = $subject;
		// print_r($message);
		return $message;
	}
}

// Agent Sales Per Agent Report Function (Reportr ID = 18)
function agent_sales_per_agent_reports_data($agent_id, $daily_schedule_res = "", $monToFri_schedule_res = "", $weekly_schedule_res = "", $monthly_schedule_res = "") {

	global $pdo, $HOST;

	$UP_ICON = $HOST . "/images/up_arrow.png";
	$DOWN_ICON = $HOST . "/images/down_arrow.png";

	$sel = "SELECT GROUP_CONCAT(product_id) as pid FROM agent_product_rule WHERE agent_id=:agent_id";
	$wr = array(":agent_id" => $agent_id);
	$res = $pdo->selectOne($sel, $wr);

	if ($res['pid'] == "") {
		$res['pid'] = '0';
	}

	if ($res['pid'] != "") {
		$incr = " AND p.id IN(" . $res['pid'] . ")";
	}

	$order_sql = "SELECT CONCAT(cus.fname,' ',cus.lname) as agent_name,cm.total_commission,sum(od.unit_price*od.qty) as total_sales,c.id as agent_id,s.id as c_id
          FROM customer c
          LEFT JOIN customer  s ON(c.id=s.sponsor_id AND s.type='Agent')
          LEFT JOIN customer as cus ON(s.id=cus.sponsor_id AND cus.type='Customer')
          LEFT JOIN orders o ON(cus.id = o.customer_id)
          LEFT JOIN order_details od ON(od.order_id = o.id)
          LEFT JOIN prd_main p ON(p.id = od.product_id $incr)
          LEFT JOIN
            (
              SELECT SUM(amount) as total_commission,customer_id
                FROM commission WHERE status!='Pending' AND is_deleted='N'
                GROUP BY customer_id
            ) AS cm ON (cm.customer_id = s.id)
          WHERE c.type='Agent' AND c.is_deleted='N' AND o.status NOT IN ('Pending Quote','Pending Quotes') AND c.id='" . $agent_id . "'  GROUP BY s.id";
	$lead_res = $pdo->select($order_sql);

	$all_leads_array = array('agent_name', 'Agents');
	$report_th_data = array('Agent Name', 'Growth', 'Total Sales');
	// pre_print($lead_res);

	$today_sales = array();
	if (count($lead_res) > 0) {
		foreach ($lead_res as $val) {
			$today_sales[$val['agent_id']]['id'] .= $val['agent_id'];
			$today_sales[$val['agent_id']]['today_sales'] .= $val['total_sales'];
		}
	}

	// foreach ($lead_res as $key => $rows) {
	// }

	if (count($lead_res) > 0) {
		foreach ($lead_res as $key => $res) {
			if (isset($today_sales[$res['agent_id']])) {
				$total_sales = $today_sales[$res['agent_id']]['today_sales'];
				$sales_diff = $today_sales[$res['agent_id']]['today_sales'];
				if ($sales_diff != 0 && $total_sales != 0 && $total_sales != '') {
					$sales_per = ($sales_diff) / $total_sales * 100;
				} else {
					$sales_per = 0;
				}
			} else {
				$sales_per = 0;
			}
			$name = $res['agent_name'] != "" ? $res['agent_name'] : "";
			$sales_per = $sales_per != "" ? $sales_per : 0;
			$total_sales = $res['total_sales'] != "" ? $res['total_sales'] : 0;
			$icon = $sales_per >= 0 ? $UP_ICON : $DOWN_ICON;

			$trigger_param[] = array(
				'name' => $name,
				'sales_per' => $sales_per,
				'total_sales' => $total_sales,
				'icon' => $icon,
			);
		}
		$sendEmailSummary = $trigger_param;
		$report_link = $HOST . "/agents/report_sale_per_agent.php";
		if ($daily_schedule_res != "") {
			$report_heading = "Sales Per Agent Snapshot Daily Report @ " . date("h:i A", strtotime($daily_schedule_res['time'])) . " " . $daily_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Agent Sales Daily Report";
		} else if ($monToFri_schedule_res != "") {
			$report_heading = "Sales Per Agent Snapshot Monday - Friday Report @ " . date("h:i A", strtotime($monToFri_schedule_res['time'])) . " " . $monToFri_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Agent Sales Monday - Friday Report";
		} else if ($weekly_schedule_res != "") {
			$report_heading = "Sales Per Agent Snapshot Weekly Report @ " . date("h:i A", strtotime($weekly_schedule_res['time'])) . " " . $weekly_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Agent Sales Weekly Report";
		} else if ($monthly_schedule_res != "") {
			$report_heading = "Sales Per Agent Snapshot Monthly Report @ " . date("h:i A", strtotime($monthly_schedule_res['time'])) . " " . $monthly_schedule_res['timezone'];
			$subject = "Agents : Agent Sales Per Agent Sales Monthly Report";
		}
		if (is_array($sendEmailSummary)) {
			foreach ($sendEmailSummary as $placeholder => $value) {
				if (is_array($value)) {
					if (!$hasCopied) {

						$table_head .= "\r\n" . "<tr><td colspan='4' bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:10px 0;'>";
						$table_head .= "\r\n" . $report_heading . "</td></tr><tr><td colspan='4'> </td></tr>";

						$table_head .= "\r\n" . "<tr bgcolor='#2c94d1' valign='middle' align='center' style='font-family:Arial, Helvetica, sans-serif; font-size:26px; font-weight:bold; color:#ffffff; padding:20px 0;'>";
						foreach ($report_th_data as $th) {
							$table_data .= "\r\n" . "<th style='background-color:2c94d1;color:#ffffff'> " . $th . "</th>";
						}
						$table_head .= "\r\n" . "</tr>";
						$hasCopied = true;
					}
					$table_data .= "\r\n" . "<tr> ";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='25%' align='left'> " . $value['name'] . "</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> <img src='" . $value['icon'] . "'align='texttop' width='20' height='20' border='0' /> " . round($value['sales_per']) . "%</td>";
					$table_data .= "\r\n" . "<td style='font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#000000;' width='35%' align='center'> " . displayAmount($value['total_sales'], 2) . "</td>";
				} else {
					$str_key = ucwords(str_replace('_', ' ', $placeholder));
					$message .= "\r\n" . "<p><strong>" . $str_key . "</strong> : " . $value . "</p> ";
				}

			}
			if (!empty($table_head)) {
				$table_data .= "\r\n" . "<tr> <td colspan='4' style='text-align: center; padding-top:50px;'>";
				$table_data .= "\r\n" . "<a href='" . $report_link . "' style='font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:bold; color:#ffffff; padding:15px 30px; background-color:#2c94d1; text-decoration:none; border-radius:7px; text-shadow:3px 2px 2px rgba(0,0,0,0.20);' target='_blank'>Click For Real Time Detailed View</a> </td>";
				$table_data .= "\r\n" . "</td></tr> ";
				$message['html'] .= "\r\n" . '<table border=0 cellspacing=0 cellpadding=10 width="100%" style=" font-size:13px;  border-collapse: collapse;" > ' . "\r\n" . $table_head . "\r\n" . $table_data . "\r\n" . ' </table>';
			}
		} else {
			$message['html'] = $sendEmailSummary;
		}
		$message['subject'] = $subject;
		// echo "testin";
		// print_r($message);
		return $message;
	}
}

//  For Report HTML content For send emails Agent side End
// -----------------------------------------------------------------------------------

/// This function send_sms_agent_lead will be only used on Text messages sent from agent portal to leads
function send_sms_agent_lead($agent_id, $lead_id, $to_phone, $params, $trigger_id, $message = "") {
	GLOBAL $pdo, $TWILIO_NUMBER, $LOG_DB;

	$agent_sql = "SELECT c.*,t.phone_number as agent_twilio_number
    FROM customer c
    JOIN agent_twilio_number t ON t.customer_id=c.id
    WHERE c.id=:agent_id";
	$where_agent_id = array(':agent_id' => $agent_id);
	$agentRes = $pdo->selectOne($agent_sql, $where_agent_id);

	if ($agentRes) {
		$twilio_number = $agentRes['agent_twilio_number'];
		$params['agent_name'] = $agentRes['fname'] . ' ' . $agentRes['lname'];
		$params['agent_phone'] = $twilio_number;
	} else {
		return $msg_status = "Fail";
	}

	$leadSql = "select * from leads where id=:id";
	$leadRow = $pdo->selectOne($leadSql, array(":id" => $lead_id));
	if ($leadRow) {
		$params['lead_fname'] = $leadRow['fname'];
		$params['lead_lname'] = $leadRow['lname'];
	}

	if (in_array($agentRes['agent_tag'], array('Care First Insurance Group', 'Ideal Health Benefits'))) {
		$account_type = "Care First Insurance Group";
	} else {
		$account_type = 'Hooray Health Dialer';
	}

	//selecting message content
	if ($trigger_id > 0) {
		$trigger_sql = "SELECT * FROM triggers WHERE type in('SMS','Both') AND id=:id and status='Active'";
		$t_row = $pdo->selectOne($trigger_sql, array(":id" => $trigger_id));
		if ($t_row) {
			$message = $t_row['sms_content'];
			foreach ($params as $placeholder => $value) {
				if ($placeholder == 'USER_IDENTITY') {
					continue;
				}
				$message = str_replace("[[" . $placeholder . "]]", $value, $message);
			}
		}
	}

	$smscheckSql = "SELECT * FROM leads where id=:id and is_deleted='N' and is_sms_unsubscribe='N'";
	$smsCheckRes = $pdo->selectOne($smscheckSql, array(":id" => $lead_id));
	//$smsCount = count($smsCheckRes);

	// put your Twilio API credentials here
	$accountSid = $TWILIO_NUMBER[$account_type]['sid'];
	$authToken = $TWILIO_NUMBER[$account_type]['token'];

	//echo $accountSid." - ".$authToken." - ".$appSid." - ".$twilio_number;exit;
	if ($accountSid != '' && $authToken != '' && $twilio_number != '' && $to_phone != '' && $message != '' && $smsCheckRes) {
		if ($_SERVER['HTTP_HOST'] == '192.168.1.30') {
			$to_phone = "9662629760";
		}
		if ($to_phone == "9662629760") {
			$to_phone = "+919662629760";
		} else {
			$to_phone = "+1" . $to_phone;
		}
		$client = new Twilio\Rest\Client($accountSid, $authToken);
		try {
			$client->messages->create($to_phone, array('from' => $twilio_number, 'body' => $message));
			$msg_status = "Success";
		} catch (Exception $ex) {
			$msg_status = "Fail";
		}
		$ins_log_param = array(
			'rep_id' => $agent_id,
			'trigger_id' => $trigger_id,
			'cust_type' => $agentRes['type'],
			'email' => $to_phone,
			'status' => "Success",
			'location' => $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"],
			'created_at' => 'msqlfunc_NOW()',
		);
		$pdo->insert("$LOG_DB.trigger_log", $ins_log_param);
		$ins_his_params = array(
			'agent_id' => $agent_id,
			'lead_id' => $lead_id,
			'status' => 'Text Sent',
			'action_type' => 'Dialer',
			'description' => $message,
			'created_at' => 'msqlfunc_NOW()',
		);
		$dialer_id = $pdo->insert("auto_dialer_history", $ins_his_params);
		if ($params['track_lead_link'] == 'Y') {
			$TrackSql = array(
				"lead_id" => $lead_id,
				"action" => 'Text Sent',
				"dialer_history_id" => $dialer_id,
				"created_at" => 'msqlfunc_NOW()',
			);
			$pdo->insert("lead_link_tracking", $TrackSql);
		}
	} else {
		$msg_status = 'Fail';
	}
	return $msg_status;
}

/// This function send_sms_agent_lead will be only used on Text messages sent from agent portal to leads
function send_sms_cyberx_lead($lead_id, $from_phone, $to_phone, $params, $trigger_id, $message = "") {
	GLOBAL $pdo, $TWILIO_NUMBER, $LOG_DB;

	/*$agent_sql = "SELECT c.*,t.phone_number as agent_twilio_number
		    FROM customer c
		    JOIN agent_twilio_number t ON t.customer_id=c.id
		    WHERE c.id=:agent_id";
		    $where_agent_id = array(':agent_id' => $agent_id);
		    $agentRes = $pdo->selectOne($agent_sql, $where_agent_id);

		    if ($agentRes) {
		    $twilio_number = $agentRes['agent_twilio_number'];
		    $params['agent_name'] = $agentRes['fname'] . ' ' . $agentRes['lname'];
		    } else {
		    return $msg_status = "Fail";
	*/

	$leadSql = "select * from leads where id=:id";
	$leadRow = $pdo->selectOne($leadSql, array(":id" => $lead_id));
	if ($leadRow) {
		$params['lead_fname'] = $leadRow['fname'];
		$params['lead_lname'] = $leadRow['lname'];
	}

	//selecting message content
	if ($trigger_id > 0) {
		$trigger_sql = "SELECT * FROM triggers WHERE  id=:id and status='Active'";
		$t_row = $pdo->selectOne($trigger_sql, array(":id" => $trigger_id));
		if ($t_row) {
			$message = $t_row['sms_content'];
			foreach ($params as $placeholder => $value) {
				if ($placeholder == 'USER_IDENTITY') {
					continue;
				}
				$message = str_replace("[[" . $placeholder . "]]", $value, $message);
			}
		}
	}

	// put your Twilio API credentials here
	$accountSid = $TWILIO_NUMBER['Hooray Health Dialer']['sid'];
	$authToken = $TWILIO_NUMBER['Hooray Health Dialer']['token'];
	//echo $accountSid." - ".$authToken." - ".$appSid." - ".$twilio_number;exit;
	if ($accountSid != '' && $authToken != '' && $from_phone != '' && $to_phone != '' && $message != '') {
		if ($_SERVER['HTTP_HOST'] == '192.168.1.30') {
			$to_phone = "9662629760";
		}
		if ($to_phone == "9662629760") {
			$to_phone = "+919662629760";
		} else {
			$to_phone = "+1" . $to_phone;
		}

		$client = new Twilio\Rest\Client($accountSid, $authToken);
		try {
			$client->messages->create($to_phone, array('from' => $from_phone, 'body' => $message));
			$msg_status = "Success";
		} catch (Exception $ex) {
			$msg_status = "Fail";
		}

		$ins_his_params = array(
			'lead_id' => $lead_id,
			'status' => 'Text Sent',
			'action_type' => 'Dialer',
			'description' => $message,
			'created_at' => 'msqlfunc_NOW()',
		);
		$pdo->insert("auto_dialer_history", $ins_his_params);
	} else {
		$msg_status = 'Fail';
	}

	return $msg_status;
}
function get_short_url($params) {
	global $pdo, $SHORT_URL_HOST;
	$code = get_short_url_code(4);
	$insParams = array("short_code" => $code, "type" => 'Redirect', "created_at" => "msqlfunc_NOW()");
	if (!empty($params['agent_id'])) {
		$insParams['agent_id'] = $params['agent_id'];
	}
	if (!empty($params['lead_id'])) {
		$insParams['lead_id'] = $params['lead_id'];
	}
	if (!empty($params['dest_url'])) {
		$insParams['dest_url'] = $params['dest_url'];
	}
	if (!empty($params['type'])) {
		$insParams['type'] = $params['type'];
	}
	if (!empty($params['customer_id'])) {
		$insParams['customer_id'] = $params['customer_id'];
	}
	$pdo->insert("short_url", $insParams);
	return $SHORT_URL_HOST . '/' . $code;
}
function get_short_url_code($length) {
	global $pdo;
	$code = "";
	$characters = 'abcdefghijklmnopqrstuvwxyz1234567890';
	for ($i = 0; $i < $length; $i++) {
		$code .= $characters[rand(0, strlen($characters) - 1)];
	}
	$checkSql = "SELECT id FROM short_url WHERE short_code=:code";
	$checkRow = $pdo->selectOne($checkSql, array(":code" => $code));
	if ($checkRow) {
		return get_short_url_code($length);
	}
	return $code;
}
function getRoundRobinFronter() {
	global $pdo;

	//selecting last agent whom lead is assigned with round robin
	$lastAgentSql = "SELECT c.id,c.rep_id
    FROM lead_api_request l
    JOIN lead_round_robin rr ON rr.agent_id=l.sponsor_id
    JOIN customer c ON c.id=rr.agent_id
    WHERE l.sponsor_id>0 AND rr.is_deleted='N'
    $lead_incr  $agent_incr
    ORDER BY l.id DESC LIMIT 1";

	$lastAgent = $pdo->selectOne($lastAgentSql);

	if ($lastAgent) {
		$selectSql = "SELECT c.id,c.rep_id
    FROM lead_round_robin rr
    JOIN customer c ON c.id=rr.agent_id
    WHERE c.twilio_call_active='N' AND c.is_login='Y' AND rr.is_deleted='N' AND rr.id>:id
    $agent_incr
    ORDER BY rr.id ASC LIMIT 1";
		$row = $pdo->selectOne($selectSql, array(":id" => $lastAgent['id']));
		if ($row) {
			return $row['rep_id'];
		}
	}
	//selecting first agent for round robin
	$selectSql = "SELECT c.id,c.rep_id
    FROM lead_round_robin rr
    JOIN customer c ON c.id=rr.agent_id
    WHERE rr.is_deleted='N' AND c.twilio_call_active='N' AND c.is_login='Y' $agent_incr
    ORDER BY rr.id ASC LIMIT 1";
	$row = $pdo->selectOne($selectSql);

	if ($row) {
		return $row['rep_id'];
	}
	return '0';
}
function getCallCenterId($data) {
	global $pdo;

	if ($data['agent_tag'] != "") {
		$callCenters = json_decode($data['agent_tag'], true);

		//got total of percentage assign to call center
		$totalPer = 0;
		foreach ($callCenters as $t) {
			$totalPer += $t;
		}
		$totalAssignedLead = array();
		//convert that percentage into base of 100
		foreach ($callCenters as $k => $t) {
			$callCenters[$k] = (100 * $t) / $totalPer;
			$totalAssignedLead[$k] = 0;
		}

		$leadQuerySql = $pdo->select("SELECT sponsor_id,count(id) as cnt from leads WHERE opt_in_type=:opt_in_type GROUP BY sponsor_id", array(":opt_in_type" => $data["lead_tag"]));

		//update lead count by callcenter on live lead data
		$totalLeadRecordCount = 0;
		if (count($leadQuerySql) > 0) {
			foreach ($leadQuerySql as $lead) {
				$totalAssignedLead[$lead["sponsor_id"]] = $lead["cnt"];
				//count total lead
				$totalLeadRecordCount += $lead["cnt"];
			}
		}
		$findCallCenterWiseCurrentLeadCountPer = array();
		//find sponsor to assign lead logic
		if (($totalLeadRecordCount == 0 && count($callCenters) > 0) || count($callCenters) == 1) {
			//if no any lead and total all lead count is also zero then send first call center id
			foreach ($callCenters as $k => $t) {
				return $k;
			}
		} else {
			//got percentage of all call center individually
			foreach ($totalAssignedLead as $key => $value) {
				$findCallCenterWiseCurrentLeadCountPer[$key] = (100 * $value) / $totalLeadRecordCount;
			}
		}
		// pre_print($findCallCenterWiseCurrentLeadCountPer);
		foreach ($findCallCenterWiseCurrentLeadCountPer as $key => $value) {
			if ($value < $callCenters[$key]) {
				return $key;
			}
			continue;
		}

		//finally else part to return default call center
		foreach ($callCenters as $k => $t) {
			return $k;
		}
	}

	return '0';
}
function getActivityFeedLimit() {
	$arr = array("limit" => 20, "nextLimit" => 10);
	return (object) $arr;
}
function getImpDate($date = "", $companyId = 0, $interval = 30, $intervalType = "Day") {
	if ($date == "") {
		$date = date("Y-m-d");
	}
	$arr = array("eligibilityDate" => "", "nextBillingDate" => "");
	if ($companyId == 1) {
		$day = date("d", strtotime($date));

		$nextMonthDate = new DateTime($date);
		$nextMonthDate->modify('first day of next month');
		$nextMonthFormattedDate = $nextMonthDate->format('Y-m-d');
		if ($day < 25) {
			$arr["eligibilityDate"] = $nextMonthFormattedDate;
		} else {
			$nextd = new DateTime($nextMonthFormattedDate);
			$nextd->modify('first day of next month');
			$arr["eligibilityDate"] = $nextd->format('Y-m-d');
		}
		$arr["nextBillingDate"] = date("Y-m-", strtotime($arr["eligibilityDate"])) . "25";
	} else {
		$afterTwoDayDate = new DateTime($date);
		$afterTwoDayDate->modify('+2 day');

		$nextBillingDate = new DateTime($date);
		$nextBillingDate->modify("+$interval $intervalType");
		$arr["eligibilityDate"] = $afterTwoDayDate->format('Y-m-d');
		$arr["nextBillingDate"] = $nextBillingDate->format('Y-m-d');
	}
	return (object) $arr;
}
function getFutureBillingDate($date = "", $companyId = 0, $interval = 30, $intervalType = "Day") {
	if ($date == "") {
		$date = date("Y-m-d");
	}
	$nextDate = "";
	if ($companyId == 1) {
		$nextBillingDate = new DateTime($date);
		$nextBillingDate->modify("+1 MONTH");
		$nextDate = $nextBillingDate->format('Y-m-d');
	} else {
		$nextBillingDate = new DateTime($date);
		$nextBillingDate->modify("+$interval $intervalType");
		$nextDate = $nextBillingDate->format('Y-m-d');
	}

	if (strtotime($nextDate) < time()) {
		return getFutureBillingDate($nextDate, $companyId, $interval, $intervalType);
	} else {
		return $nextDate;
	}
}
function getBIDImpDate($date = "") {
	if ($date == "") {
		$date = date("Y-m-d");
	}
	$arr = array("eligibilityDate" => "", "nextBillingDate" => "");
	$day = date("d", strtotime($date));

	$nextMonthDate = new DateTime($date);
	$nextMonthDate->modify('first day of next month');
	$nextMonthFormattedDate = $nextMonthDate->format('Y-m-d');
	if ($day < 25) {
		$arr["eligibilityDate"] = $nextMonthFormattedDate;
	} else {
		$nextd = new DateTime($nextMonthFormattedDate);
		$nextd->modify('first day of next month');
		$arr["eligibilityDate"] = $nextd->format('Y-m-d');
	}
	$arr["nextBillingDate"] = date("Y-m-", strtotime($arr["eligibilityDate"])) . "25";
	return (object) $arr;
}
function assignCommissionRuleToAgent($agentId, $productId, $commissionRuleIds = array()) {
	global $pdo;
	if (!is_array($commissionRuleIds)) {
		$commissionRuleIds = array($commissionRuleIds);
	} else {
		//clean array if any blank value then just removes
		$commissionRuleIds = cleanArray($commissionRuleIds);
	}
	// pre_print($commissionRuleIds);
	foreach ($commissionRuleIds as $commissionRuleId) {
		//check rule is exists for user or not
		$checkCommissionExists = $pdo->selectOne("SELECT id,is_deleted FROM agent_commission_rule WHERE agent_id=:agent_id AND product_id=:product_id AND commission_rule_id=:commission_rule_id", array(":product_id" => $productId, ":agent_id" => $agentId, ":commission_rule_id" => $commissionRuleId));
		if (!$checkCommissionExists) {
			$pdo->insert("agent_commission_rule", array(
				"agent_id" => $agentId,
				"product_id" => $productId,
				"commission_rule_id" => $commissionRuleId,
				"updated_at" => "msqlfunc_NOW()",
				"created_at" => "msqlfunc_NOW()",
			)
			);
		} else {
			if ($checkCommissionExists["is_deleted"] == "Y") {
				$updateSql = array("is_deleted" => 'N');
				$updateWhere = array("clause" => "id=:id", "params" => array(":id" => $checkCommissionExists['id']));
				$pdo->update("agent_commission_rule", $updateSql, $updateWhere);
			}
		}
	}
	//remove rule which is not exists when we add
	$getAllRules = $pdo->select("SELECT * FROM agent_commission_rule WHERE agent_id=:agent_id AND product_id=:product_id", array(":product_id" => $productId, ":agent_id" => $agentId));
	if (count($getAllRules) > 0) {
		foreach ($getAllRules as $rule) {
			//check commission id is exists on our else remove it
			if (!in_array($rule["commission_rule_id"], $commissionRuleIds)) {
				$updateSql = array("is_deleted" => 'Y');
				$updateWhere = array("clause" => "id=:id", "params" => array(":id" => $rule['id']));
				$pdo->update("agent_commission_rule", $updateSql, $updateWhere);
			}
		}
	}
}
function cleanArray($ar) {
	if (!is_array($ar)) {
		return array();
	} else {
		foreach ($ar as $k => $a) {
			if (trim($a) == "") {
				unset($ar[$k]);
			}
		}
	}
	return $ar;
}
if (!function_exists('get_call_center_access_features')) {
	/**
	 * @param $user_id
	 * @param array $res_feature
	 * @return array
	 */
	function get_call_center_access_features($user_id, $res_feature = array()) {
		global $pdo;
		$features_arr = array();

		if (empty($res_feature)) {
			$sql_feature = "SELECT id, title, IF(parent_id = 0, id, parent_id) as parent_id FROM call_center_feature_access ORDER BY parent_id, id";
			$res_feature = $pdo->select($sql_feature);
		}

		if (count($res_feature) > 0) {
			$UserAccessSql = "SELECT feature_access,access_type,type,sponsor_id FROM customer WHERE id=:id AND status='Active' AND is_deleted='N' AND (type IN('Call Center','Call Center Manager') OR (type IN('Agent','Fronter','Call Center Manager'))";
			$UserAccessWhere = array(":id" => makeSafe($user_id));
			$UserAccessRow = $pdo->selectOne($UserAccessSql, $UserAccessWhere);
			if (empty($UserAccessRow)) {
				foreach ($res_feature as $feature) {
					if (!isset($features_arr[$feature['parent_id']])) {
						$features_arr[$feature['parent_id']] = $feature;
						$features_arr[$feature['parent_id']]['child'] = array();
					} else {
						$features_arr[$feature['parent_id']]['child'][] = $feature;
					}
				}
			} else {
				$UserAccessType = $UserAccessRow['access_type'];
				if ($UserAccessType == 'limited') {
					$ParentFeatureAccess = $UserAccessRow['feature_access'] != "" ? (array) json_decode($UserAccessRow['feature_access']) : array();
					foreach ($res_feature as $feature) {
						if (in_array($feature['id'], $ParentFeatureAccess)) {
							if (!isset($features_arr[$feature['parent_id']])) {
								$features_arr[$feature['parent_id']] = $feature;
								$features_arr[$feature['parent_id']]['child'] = array();
							} else {
								$features_arr[$feature['parent_id']]['child'][] = $feature;
							}
						}
					}
				} elseif ($UserAccessType == 'full_access') {
					$features_arr = get_call_center_access_features($UserAccessRow['sponsor_id'], $res_feature);
				} else {
					foreach ($res_feature as $feature) {
						if (!isset($features_arr[$feature['parent_id']])) {
							$features_arr[$feature['parent_id']] = $feature;
							$features_arr[$feature['parent_id']]['child'] = array();
						} else {
							$features_arr[$feature['parent_id']]['child'][] = $feature;
						}
					}
				}
			}
		}
		return $features_arr;
	}
}

if (!function_exists('get_agent_access_features')) {
	/**
	 * @param $user_id
	 * @param array $res_feature
	 * @return array
	 */
	function get_agent_access_features($user_id, $res_feature = array()) {
		global $pdo;
		$features_arr = array();

		if (empty($res_feature)) {
			$sql_feature = "SELECT id, title, IF(parent_id = 0, id, parent_id) as parent_id FROM agent_feature_access ORDER BY parent_id, id";
			$res_feature = $pdo->select($sql_feature);
		}

		if (count($res_feature) > 0) {
			$UserAccessSql = "SELECT feature_access,access_type,type,sponsor_id FROM customer WHERE id=:id AND status='Active' AND is_deleted='N' AND type = 'Agent'";
			$UserAccessWhere = array(":id" => makeSafe($user_id));
			$UserAccessRow = $pdo->selectOne($UserAccessSql, $UserAccessWhere);
			if (empty($UserAccessRow)) {
				foreach ($res_feature as $feature) {
					if (!isset($features_arr[$feature['parent_id']])) {
						$features_arr[$feature['parent_id']] = $feature;
						$features_arr[$feature['parent_id']]['child'] = array();
					} else {
						$features_arr[$feature['parent_id']]['child'][] = $feature;
					}
				}
			} else {
				$UserAccessType = $UserAccessRow['access_type'];
				if ($UserAccessType == 'limited') {
					$ParentFeatureAccess = $UserAccessRow['feature_access'] != "" ? (array) json_decode($UserAccessRow['feature_access']) : array();
					foreach ($res_feature as $feature) {
						if (in_array($feature['id'], $ParentFeatureAccess)) {
							if (!isset($features_arr[$feature['parent_id']])) {
								$features_arr[$feature['parent_id']] = $feature;
								$features_arr[$feature['parent_id']]['child'] = array();
							} else {
								$features_arr[$feature['parent_id']]['child'][] = $feature;
							}
						}
					}
				} elseif ($UserAccessType == 'full_access') {
					$features_arr = get_agent_access_features($UserAccessRow['sponsor_id'], $res_feature);
				} else {
					foreach ($res_feature as $feature) {
						if (!isset($features_arr[$feature['parent_id']])) {
							$features_arr[$feature['parent_id']] = $feature;
							$features_arr[$feature['parent_id']]['child'] = array();
						} else {
							$features_arr[$feature['parent_id']]['child'][] = $feature;
						}
					}
				}
			}
		}
		return $features_arr;
	}
}

if (!function_exists('get_call_center_access_type_and_access')) {
	/**
	 * @param $call_center_id
	 * @return array
	 */
	function get_call_center_access_type_and_access($call_center_id) {
		global $pdo;
		$response = array(
			'access_type' => 'full_access',
			'access' => array(),
		);
		$UserAccessSql = "SELECT id,feature_access,access_type,type,sponsor_id FROM customer WHERE id=:id AND status='Active' AND is_deleted='N' AND (type IN('Call Center','Call Center Manager') OR (type IN('Agent','Fronter','Call Center Manager') )";
		$UserAccessWhere = array(":id" => makeSafe($call_center_id));
		$UserAccessRow = $pdo->selectOne($UserAccessSql, $UserAccessWhere);
		if (!empty($UserAccessRow)) {
			$UserAccessType = $UserAccessRow['access_type'];
			if ($UserAccessType == 'limited') {
				$response = array(
					'access_type' => 'limited',
					'access' => $UserAccessRow['feature_access'] != "" ? (array) json_decode($UserAccessRow['feature_access']) : array(),
				);
			} elseif ($UserAccessType == 'full_access') {
				$response = get_call_center_access_type_and_access($UserAccessRow['sponsor_id']);
			}
		}
		return $response;
	}
}

if (!function_exists('get_agent_access_type_and_access')) {
	/**
	 * @param $agent_id
	 * @return array
	 */
	function get_agent_access_type_and_access($agent_id) {
		global $pdo;
		$response = array(
			'access_type' => 'full_access',
			'access' => array(),
		);
		$UserAccessSql = "SELECT id,feature_access,access_type,type,sponsor_id FROM customer WHERE id=:id AND status='Active' AND is_deleted='N' AND type = 'Agent'";
		$UserAccessWhere = array(":id" => makeSafe($agent_id));
		$UserAccessRow = $pdo->selectOne($UserAccessSql, $UserAccessWhere);
		if (!empty($UserAccessRow)) {
			$UserAccessType = $UserAccessRow['access_type'];
			if ($UserAccessType == 'limited') {
				$response = array(
					'access_type' => 'limited',
					'access' => $UserAccessRow['feature_access'] != "" ? (array) json_decode($UserAccessRow['feature_access']) : array(),
				);
			} elseif ($UserAccessType == 'full_access') {
				$response = get_agent_access_type_and_access($UserAccessRow['sponsor_id']);
			}
		}
		return $response;
	}
}

function get_invoice_id($table = 'invoices') {
	global $pdo;
	$inv_id = rand(100000, 999999);
	$invoice_id = "INV-" . $inv_id;
	$sql = "SELECT count(invoice_no) as total FROM invoices WHERE invoice_no='" . $invoice_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_invoice_id($table);
	} else {
		return $invoice_id;
	}
}
function get_list_bill_id($table = 'list_bills') {
	global $pdo;
	$list_bill_id = rand(100000, 999999);
    $list_bill_id = "LIST-" . $list_bill_id;
	$sql = "SELECT count(list_bill_no) as total FROM list_bills WHERE list_bill_no='" . $list_bill_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_list_bill_id($table);
	} else {
		return $list_bill_id;
	}
}

function get_updated_field($updat_arr, $query_arr, $activity_id) {
	global $pdo;
	$result = array_diff($updat_arr, $query_arr);
	if (isset($result['password'])) {
		$result['password'] = "";
	}
	if (isset($result['card_no_full'])) {
		$result['card_no_full'] = $result['card_no'];
	}
	$updated_field = json_encode($result);

	if (count($result) > 0) {
		$update_params = array(
			'extra' => $updated_field,
		);
		$update_where = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => makeSafe($activity_id),
			),
		);
		$pdo->update('activity_feed', $update_params, $update_where);
	}

}

function getAge($date) {
	return intval(date('Y', time() - strtotime($date))) - 1970;
}

/**
 * @return array
 */
function get_bid_plan_labels()
{
    return array(
        '8'=>'MAC1500',
        '7'=>'MAC3000',
        '21'=>'MAC5000',
        '9'=>'Bright Idea Dental Vision',
    );
}
function is_allow_change_list_bill_adjustment()
{
	$response = true;
	/*if ($_SESSION['admin']['type'] == 'Super Admin') {
		$response = true;
	} else {
		$response = false;
	}*/
	return $response;
}function getCommissionOnAgentTerms($codedId){
	global $pdo;
	$getCodedLevel=$pdo->select("SELECT level FROM agent_coded_level WHERE id<=:id ORDER BY id desc",array(":id"=>$codedId));
	$level="";
	foreach($getCodedLevel as $l){
		$level.=$l["level"].", ";
	}
	$level=rtrim($level,", ");
	return $level;
}
?>