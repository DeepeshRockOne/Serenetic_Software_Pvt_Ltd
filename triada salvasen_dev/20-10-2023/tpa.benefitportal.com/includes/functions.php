<?php

function pre_print($val, $is_exit = true) {
	echo "<pre>";
	print_r($val);
	echo "</pre>";
	if ($is_exit) {
		exit;
	}
}

function getname($table, $id, $name, $compvar = "id") {
	global $pdo;
	$getsql = "SELECT $name from $table where $compvar=:id";
	$params = array(
		':id' => $id,
	);
	$row = $pdo->selectOne($getsql, $params);
	return $row ? $row[$name] : '';
}

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
 
function retrieveDate($date, $split_date = false) {
	global $FULL_DATE_FORMAT, $DATE_FORMAT, $TIME_FORMAT;
	if ($split_date) {
		return date($DATE_FORMAT, strtotime($date)) . "<br/>" . date($TIME_FORMAT, strtotime($date));
	} else {
		return date($FULL_DATE_FORMAT, strtotime($date));
	}
}

function get_agent_display_id() {
	global $pdo;
	$cust_id = rand(100000, 999999);
	
	$sql = "SELECT count(display_id) as total FROM customer WHERE display_id =$cust_id";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_agent_display_id();
	} else {
		return $cust_id;
	}
}

function get_admin_id() {
	global $pdo;
	$cust_id = rand(1000, 9999);
	$sql = "SELECT display_id FROM admin WHERE display_id = 'AD" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res) {
		return get_admin_id();
	} else {
		return "AD" . $cust_id;
	}
}

function get_agent_id() {
	global $pdo;
	$cust_id = rand(10000, 99999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='A" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_agent_id();
	} else {
		return "A" . $cust_id;
	}
}

function get_agent_account_manager_id() {
	global $pdo;
	$cust_id = rand(100000, 999999);
	$sql = "SELECT count(*) as total FROM sub_agent WHERE account_manager_id ='AM" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_agent_account_manager_id();
	} else {
		return "AM" . $cust_id;
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

function getOS($userAgent) {
	$oses = array(
		'iPhone' => '(iPhone)',
		'Windows 3.11' => 'Win16',
		'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
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
		if (preg_match("#" . $pattern . "#", $userAgent)) { 
			return $os; 
		}
	}
	return 'Unknown';
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

function makeSafe($string) {
	if (!is_array($string)) {
		$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
	return $string;
}

function generate_chat_password($length = 10) {
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function displayAmount($number, $decimal = 2, $PRICE_TAG = '$') {
	$PRICE_TAG = '$';
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

function displayAmount2($number, $decimal = 2, $PRICE_TAG = '$') {
	$PRICE_TAG = '$';
	if ($number != "" && is_numeric($number)) {
		if ($number < 0) {
			return '<span class="text-action">(' . $PRICE_TAG . number_format(abs($number), $decimal, '.', ',').')</span>';
		} else {
			return $PRICE_TAG . number_format(abs($number), $decimal, '.', ',');
		}
	} else {
		return $PRICE_TAG . number_format(0, $decimal, '.', ',');
	}
}

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

function format_telephone($phone_number = '') {
	if(empty($phone_number)) {
		return '';
	}
	$phone_number = phoneReplaceMain($phone_number);
	if(strlen($phone_number) != 10) {
		return '';	
	}
	$cleaned = preg_replace('/[^[:digit:]]/', '', $phone_number);
	preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
	return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
}

function get_user_data($user) {
	$user_detail = array();
	$user_detail['user_id'] = $user['id'];
	$user_detail['display_id'] = !empty($user['display_id']) ? $user['display_id'] : $user['rep_id'] ;
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
	global $pdo, $LOG_DB;

	$REAL_IP_ADDRESS = get_real_ipaddress();
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
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
			'changed_at' => 'msqlfunc_NOW()',
		);

		$id = $pdo->insert("$LOG_DB.audit_log", $insert_data);
		return $id;
	}
}

function audit_log_new($action_by_data,$user_id,$user_type,$desc,$old_value = '',$new_value = '',$attribute = '') {
	global $pdo, $LOG_DB;
	 
	$REAL_IP_ADDRESS = get_real_ipaddress();
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
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
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

function isValidDate($date, $format = "Y-m-d") {
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function convertTimeZone($date, $from = "EST", $to = "EST") {
	if (empty($date)) {
		$date = date("Y-m-d H:i:s");
	}

	$d = new DateTime($date, new DateTimeZone(strtoupper($from)));
	$d->setTimeZone(new DateTimeZone(strtoupper($to)));
	return $d->format('Y-m-d H:i:s');
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

function dateDifference($date_1, $differenceFormat, $date_2 = 'today') {

	$from = new DateTime(date('Y-m-d', strtotime($date_1)));
	if ($date_2 == 'today') {
		$to = new DateTime('today');
	} else {
		$to = new DateTime(date('Y-m-d', strtotime($date_2)));
	}
	$d_age = $from->diff($to)->y;
	return $d_age;
}

function get_lead_id() {
	global $pdo;
	$lead_id = rand(1000000, 9999999);
	
	$sql = "SELECT count(*) as total FROM leads WHERE lead_id ='L" . $lead_id . "' OR lead_id ='" . $lead_id . "'";
    $res = $pdo->selectOne($sql);
    
    if(!empty($res['total'])) {
      return get_lead_id();
    } else {
      return "L" . $lead_id;
    }
}

function activity_feed($company_id, $sponsor_id, $sponsor_type, $entity_id, $entity_type, $entity_action, $fname = '', $lname = '', $description = '', $enrollment_url = '', $extra = '',$changed_at = '') {
	global $pdo;
	$REAL_IP_ADDRESS = get_real_ipaddress();
	$insert_params = array(
		'company_id' => $company_id,
		'user_id' => $sponsor_id,
		'user_type' => $sponsor_type,
		'entity_id' => $entity_id,
		'entity_type' => $entity_type,
		'entity_action' => $entity_action,
		'description' => $description,
		'note_admin_name' => $fname . "" . $lname,
		'extra' => $extra,
		'ip_address' => (!empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address']),
		'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'enrollment_url' => $enrollment_url,
		'changed_at' => 'msqlfunc_NOW()',
	);
	if(!empty($changed_at) && strtotime($changed_at) > 0) {
		$insert_params['changed_at'] = $changed_at;
	}
	$activity_id = $pdo->insert("activity_feed", $insert_params);
	return $activity_id;
}

function get_short_url($params) {
	global $pdo, $SHORT_URL_HOST;
	$code = get_short_url_code(5);
	$searchParam = array();
	$insParams = array("short_code" => $code, "type" => 'Redirect', "created_at" => "msqlfunc_NOW()");
	$seachIncr = '';
		
	if (!empty($params['agent_id'])) {
		$insParams['agent_id'] = $searchParam[':agent_id'] = $params['agent_id'];
		$seachIncr .= " AND agent_id = :agent_id";
	}
	if (!empty($params['lead_id'])) {
		$insParams['lead_id'] = $searchParam[':lead_id'] = $params['lead_id'];
		$seachIncr .= " AND lead_id = :lead_id";
	}
	if (!empty($params['dest_url'])) {
		$insParams['dest_url'] = $searchParam[':dest_url'] = $params['dest_url'];
		$seachIncr .= " AND dest_url = :dest_url";
	}
	if (!empty($params['type'])) {
		$insParams['type'] = $searchParam[':type'] = $params['type'];
		$seachIncr .= " AND type = :type";
	}
	if (!empty($params['customer_id'])) {
		$insParams['customer_id'] = $searchParam[':customer_id'] = $params['customer_id'];
		$seachIncr .= " AND customer_id = :customer_id";
	}

	$checkUrlSql = "SELECT short_code FROM short_url WHERE 1 $seachIncr";
	$checkUrlRes = $pdo->selectOne($checkUrlSql, $searchParam);

	if ($checkUrlRes) {
		$code = $checkUrlRes['short_code'];
	} else {
		$pdo->insert("short_url", $insParams);
	}
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

function getActivityFeedLimit() {
	$arr = array("limit" => 20, "nextLimit" => 10);
	return (object) $arr;
}

function get_updated_field($updatedFields, $databaseFields, $activity_id, $diffEmail = false, $type = '') {
	global $pdo;
	 
	$result = array_diff_assoc($updatedFields, $databaseFields);
	 
	if (isset($result['password'])) {
		$result['password'] = "";
	}
	if (isset($result['country_id'])) {
		$result['country_id'] = "";
	}
	if (isset($result['card_no_full'])) {
		$result['card_no_full'] = $result['card_no'];
	}
	if (isset($result['ach_account_number'])) {
		$result['Last four digit of account number'] = $result['activity_ach_account_number'];
		unset($result['activity_ach_account_number']);
		unset($result['ach_account_number']);
	}
	if (isset($result['ach_routing_number'])) {
		$result['Last four digit of routing number'] = $result['activity_ach_routing_number'];
		unset($result['activity_ach_routing_number']);
		unset($result['ach_routing_number']);
	}
	if (!empty($result['created_at'])) {
		unset($result['created_at']);
	}
	if (!empty($result['updated_at'])) {
		unset($result['updated_at']);
	}
	if (!empty($result['ip_address'])) {
		unset($result['ip_address']);
	}
	if (!empty($result["agent_id"])) {
		unset($result["agent_id"]);
	}
	if (!empty($result["id"])) {
		unset($result["id"]);
	}

	if ($diffEmail) {
		if (!empty($result['email'])) {
			unset($result['email']);
			$result['old_email'] = $databaseFields['email'];
			$result['new_email'] = $updatedFields['email'];
		}
	}
	 
	$updated_field = $result;

	if ($type == 'member') {
		$updated_field["member_id"] = $_SESSION["customer"]["id"];
	} else if ($type == 'agent') {
		$updated_field["agent_id"] = $_SESSION["agents"]["id"];
	} else if ($type == 'admin') {
		$updated_field["admin_id"] = $_SESSION["admin"]["id"];
	} else if ($type == 'group') {
		$updated_field["group_id"] = $_SESSION["groups"]["id"];
	}

	$updated_field = json_encode($updated_field);

	if (!empty($result)) {

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
	} else {
		$update_params = array(
			'is_deleted' => 'Y',
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

function getRelation($relation, $gender) {
	$relation_name = '';
	$relation = strtolower($relation);
	$gender = strtolower($gender);

	if (in_array($relation, array('child', 'spouse'))) {
		if ($relation == 'child') {
			if ($gender == 'male' || $gender == 'm') {
				$relation_name = 'Son';
			} else {
				$relation_name = 'Daughter';
			}
		} else {
			if ($gender == 'male' || $gender == 'm') {
				$relation_name = 'Husband';
			} else {
				$relation_name = 'Wife';
			}
		}
	} else {
		$relation_name = 'Other';
	}
	return $relation_name;
}

function getRevRelation($relation = "", $gender = "") {
	$relation_name = '';
	$relation = strtolower($relation);
	$gender = strtolower($gender);
	if (in_array($relation, array("son", "daughter"))) {
		return "Child";
	} else if (in_array($relation, array("husband", "wife"))) {
		return "Spouse";
	} else if (!empty($relation)) {
		return "Qualifying Relative";
	} else {
		return "";
	}
}

function getRelationOptions($relation = "", $selected_plans = array()) {
	$getRelation = getRevRelation($relation);
	if (!empty($selected_plans)) {
		if (in_array(4, $selected_plans)) {
			return "<option value='Child' " . ($getRelation == "Child" ? "selected" : "") . ">Child</option><option value='Spouse' " . ($getRelation == "Spouse" ? "selected" : "") . ">Spouse</option>";
		} elseif (in_array(2, $selected_plans) && in_array(3, $selected_plans)) {
			return "<option value='Child' " . ($getRelation == "Child" ? "selected" : "") . ">Child</option><option value='Spouse' " . ($getRelation == "Spouse" ? "selected" : "") . ">Spouse</option>";
		} elseif (in_array(2, $selected_plans)) {
			return "<option value='Child' " . ($getRelation == "Child" ? "selected" : "") . ">Child</option>";
		} elseif (in_array(3, $selected_plans)) {
			return "<option value='Spouse' " . ($getRelation == "Spouse" ? "selected" : "") . ">Spouse</option>";
		} else {
			return "<option value='Child' " . ($getRelation == "Child" ? "selected" : "") . ">Child</option><option value='Spouse' " . ($getRelation == "Spouse" ? "selected" : "") . ">Spouse</option>";
		}
	} else {
		return "<option value='Child' " . ($getRelation == "Child" ? "selected" : "") . ">Child</option><option value='Spouse' " . ($getRelation == "Spouse" ? "selected" : "") . ">Spouse</option>";
	}
}

function addAgentNotification($recipient_id, $template_id, $href = "#", $comment_id = 0, $force_display = 'N', $sender_id = 0, $extraData = "", $sender_type = "", $receipent_type = "") {
	global $pdo;

	$detail_id = $pdo->insert("users_notifications_details", array(
		"noti_template_id" => $template_id,
		"comment_id" => $comment_id,
		"href" => $href,
		"extra" => $extraData,
	)
	);
	if (!is_array($recipient_id)) {
		$recipient_id = array($recipient_id);
	}
	foreach ($recipient_id as $id) {
		$notification_array = array(
			"sender_id" => $sender_id,
			"recipient_id" => $id,
			"noti_detail_id" => $detail_id,
			"force_display" => $force_display,
			"created_at" => "msqlfunc_NOW()",
		);
		if (!empty($sender_type)) {
			$notification_array["sender_type"] = $sender_type;
		}
		if (!empty($receipent_type)) {
			$notification_array["recipient_type"] = $receipent_type;
		}
		$pdo->insert("users_notifications", $notification_array);
	}
}

function addGroupNotification($recipient_id, $template_id, $href = "#", $comment_id = 0, $force_display = 'N', $sender_id = 0, $extraData = "") {
	global $pdo;

	$detail_id = $pdo->insert("users_notifications_details", array(
		"noti_template_id" => $template_id,
		"comment_id" => $comment_id,
		"href" => $href,
		"extra" => $extraData,
	)
	);
	if (!is_array($recipient_id)) {
		$recipient_id = array($recipient_id);
	}
	foreach ($recipient_id as $id) {
		$notification_array = array(
			"sender_id" => $sender_id,
			"recipient_id" => $id,
			"noti_detail_id" => $detail_id,
			"force_display" => $force_display,
			"created_at" => "msqlfunc_NOW()",
		);
		$pdo->insert("users_notifications", $notification_array);
	}
}

function calculateAge($birthDate) {
	$bdate = date('Y-m-d', strtotime($birthDate));
	$today_date = date('Y-m-d');

	$bdate = date_create($bdate);
	$today_date = date_create($today_date);

	$dateDiff = date_diff($bdate, $today_date);
	$ageInYear = $dateDiff->format("%y");

	return $ageInYear;
}

function get_pay_period_weekly($date = "") {
	global $pdo;
	// if ($date == "") {
	// 	$date = date('Y-m-d');
	// }
	// $date = date("Y-m-d",strtotime($date));
	// if (date('l', strtotime($date)) == 'Sunday') {
	// 	$pay_period = $date;
	// } else {
	// 	$pay_period = date('Y-m-d', strtotime("next Sunday",strtotime($date)));
	// }
	//
	$weekly_commission = $pdo->selectOne("SELECT * FROM commission_periods_settings WHERE commission_type='weekly'");
	if(!empty($weekly_commission['id'])){
		// $start_date = date('m/d/Y',strtotime($weekly_commission['commission_day'].' -6 days'));
		$pay_period = date('Y-m-d',strtotime($weekly_commission['commission_day']));
	}else{
		$pay_period = date('Y-m-d', strtotime("next Sunday",strtotime($date)));
	}
	return $pay_period;
}

function get_pay_period_monthly($date = "") {
	if ($date == "") {
		$date = date('Y-m-d');
	}
	$date = date("Y-m-d",strtotime($date));
	$pay_period = date("Y-m-d", strtotime("last day of this month",strtotime($date)));
	return $pay_period;
}

function is_mec_product($product_id) {
	global $pdo;
	$is_mec_product = false;
	if (in_array($product_id, get_mec_product_ids())) {
		$is_mec_product = true;
	} else {
		$parent_product_id = getname('prd_main', $product_id, 'parent_product_id', 'id');
		if (in_array($parent_product_id, get_mec_product_ids())) {
			$is_mec_product = true;
		}
	}
	return $is_mec_product;
}

function order_contain_mec_product($order_id) {
	global $pdo;
	$contain_mec_product = false;
	$sql = "SELECT od.product_id 
			FROM orders o 
			JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N') 
			WHERE o.id=:id";
	$ord_prd_res = $pdo->select($sql, array("id" => $order_id));
	if (!empty($ord_prd_res)) {
		foreach ($ord_prd_res as $key => $product_row) {
			if ($contain_mec_product == false && is_mec_product($product_row['product_id']) == true) {
				$contain_mec_product = true;
				break;
			}
		}
	}
	return $contain_mec_product;
}
 
function get_enrollment_fee_prd_ids($type=''){
	global $pdo;
	$prd_res = $pdo->select("SELECT id FROM prd_main WHERE is_deleted='N' AND status='Active' AND product_type='Enrollment'");
	if($type == 'string') {
		$fee_products = "";
		if(!empty($prd_res)) {
			foreach ($prd_res as $key => $prd_row) {
				if($key > 0) {
					$fee_products .=",";
				}
				$fee_products .= $prd_row['id'];
			}
		}
	} else {
		$fee_products = array();
		if(!empty($prd_res)) {
			foreach ($prd_res as $key => $prd_row) {
				$fee_products[] = $prd_row['id'];
			}
		}
	}
	return $fee_products;
}

function get_enrollment_with_associate_fee_prd_ids($type='') {
	if($type == 'string'){
		$fee_products = "80,81";
	} else {
		$fee_products = array(80,81);
	}
	return $fee_products;
}

function get_transaction_display_id() {
    global $pdo;

    $display_id = rand(10000000,99999999);
    $sql = "SELECT count(*) as total FROM transactions WHERE display_id = :display_id";
    $res = $pdo->selectOne($sql, array(":display_id" => $display_id));
    if ($res['total'] > 0) {
        return get_transaction_display_id();
    } else {
        return $display_id;
    }
}
/*
function transaction_insert($orderId,$orderType='Credit',$transactionType='',$message='',$commissionId=0,$otherParams=array()){
    global $pdo,$CREDIT_CARD_ENC_KEY;
    $displayId=get_transaction_display_id();
    $transactionArray=array();
    $extraParams = array();

    $orderSql="SELECT o.id as orderId,o.customer_id as customerId,o.grand_total as grandTotal,o.status,o.payment_master_id as paymentMasterId FROM orders o WHERE o.id=:id";
    $orderRes=$pdo->selectOne($orderSql,array(":id"=>$orderId));
    
    if($orderRes){

        $customerId = $orderRes['customerId'];
        $grandTotal = $orderRes['grandTotal'];
    	$orderStatus = $orderRes['status'];

    	$reqUrl = !empty($otherParams['req_url'])  ? $otherParams['req_url'] : ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    	$transResponse = !empty($otherParams['transaction_response']) ? $otherParams['transaction_response'] : "";

        $insParams=array(
            'display_id'=>$displayId,
            'order_id'=>$orderId,
            'customer_id'=>$customerId,
            'order_type'=>$orderType,
            'transaction_status'=> $orderStatus,
            'transaction_type'=>$transactionType,
            'message'=>$message,
            'created_at'=>'msqlfunc_NOW()',
        );

         if(!empty($reqUrl)){
        	$insParams['req_url'] = $reqUrl;
		}

		if(!empty($transResponse)){
        	$insParams['transaction_response']=json_encode($transResponse);
        }

        if(!empty($orderRes['paymentMasterId'])){
        	$insParams['payment_master_id'] = $orderRes['paymentMasterId'];
        }

         if(!empty($otherParams['reason'])){
        	$insParams['reason'] = $otherParams['reason'];	
        }

        $billingSql = "SELECT ob.*,SUBSTRING(AES_DECRYPT(ob.ach_account_number,'".$CREDIT_CARD_ENC_KEY."'),-4) AS last_ach_acc_no 
        			FROM order_billing_info ob 
        			WHERE ob.order_id=:billing_id";
        $billingParam = array(":billing_id" => $orderRes['orderId']);
        $billingRes = $pdo->selectOne($billingSql,$billingParam);
       
        if(!empty($billingRes)){
			$insParams['billing_info']=json_encode($billingRes);
        }

        if(!empty($otherParams) && isset($otherParams['transaction_date'])){
        	$insParams['created_at'] = $otherParams['transaction_date'];
        }  
        if(!empty($otherParams) && !empty($otherParams['transaction_id'])){
        	$insParams['transaction_id'] = $otherParams['transaction_id'];
        }

        if($orderType=='Credit'){
        	if(!empty($otherParams) && isset($otherParams['credit_amount'])){
        		$insParams['credit']=$otherParams['credit_amount'];
        	}else{
            	$insParams['credit']=$grandTotal;
        	}
        }else{
        	if(in_array($transactionType, array('Refund Order','Chargeback','Payment Declined','Cancelled','Payment Returned','Void Order'))){
        		if(!empty($otherParams) && isset($otherParams['debit_amount'])){
        			$insParams['debit']=$otherParams['debit_amount'];
        		}else{
        			$insParams['debit']=$grandTotal;
        		}
        	}
        }

        $insId=$pdo->insert('transactions',$insParams);

        $transactionArray['id']=$insId;
        $transactionArray['display_id']=$displayId;

        if($transactionType == 'Refund Order' && !empty($otherParams['refunded_products'])){
        	$extraParams['refunded_products'] = $otherParams['refunded_products'];
        	$extraParams['refund_id'] = $otherParams['refund_id'];
        }
        sub_transaction_insert($insId,$extraParams);
    }

    if($commissionId>0){
    	$sqlCommission="SELECT * FROM commission_wallet_history WHERE id=:id";
    	$resCommission=$pdo->selectOne($sqlCommission,array(":id"=>$commissionId));

    	if($resCommission){
    		$customerId = $resCommission['customer_id'];
    		$amount = $resCommission['amount'];
    		$insParams=array(
	            'display_id'=>$displayId,
	            'order_id'=>0,
	            'customer_id'=>$customerId,
	            'order_type'=>$orderType,
	            'transaction_type'=>$transactionType,
	            'message'=>$message,
	            'commission_wallet_history_id'=>$commissionId,
	            'created_at'=>'msqlfunc_NOW()',
	        );
    		  if(!empty($otherParams) && isset($otherParams['transaction_id'])){
	        	$insParams['transaction_id']=$otherParams['transaction_id'];
	        }
	        if($orderType=='Credit'){
	        	if(!empty($otherParams) && isset($otherParams['credit_amount'])){
	        		$insParams['credit']=$otherParams['credit_amount'];
	        	}else{
	            	$insParams['credit']=$amount;
	        	}
	        }else{
        		if(!empty($otherParams) && isset($otherParams['debit_amount'])){
        			$insParams['debit']=$otherParams['debit_amount'];
        		}else{
        			$insParams['debit']=$amount;
        		}	        	
			}
			if(!empty($otherParams) && isset($otherParams['transaction_response'])){
	        	$insParams['transaction_response']=json_encode($otherParams['transaction_response']);
			}
	        if(!empty($otherParams) && isset($otherParams['transaction_date'])){
	        	$insParams['created_at']=$otherParams['transaction_date'];
			}
	        $insId=$pdo->insert('transactions',$insParams);

	        $transactionArray['id']=$insId;
	        $transactionArray['display_id']=$display_id;
    	}
    }
    return $transactionArray;
}*/
 
function subscription_is_paid_for_coverage_period($ws_id,$start_coverage_period) {
	global $pdo,$CREDIT_CARD_ENC_KEY;
	$is_paid = false;

	$ws_row = $pdo->selectOne("SELECT id,customer_id,plan_id FROM website_subscriptions WHERE id=:id",array(":id"=>$ws_id));

	$orders_sql = "SELECT o.id,o.display_id,o.status,o.transaction_id,o.payment_master_id,AES_DECRYPT(obi.card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as cc_no  
					FROM orders o 
					JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N') 
					JOIN order_billing_info obi ON(obi.order_id = o.id) 
					WHERE 
					od.start_coverage_period=:start_coverage_period AND
					(
						o.status IN('Payment Approved','Pending Settlement') OR 
						(o.status IN('Pending Payment','Post Payment') AND o.future_payment='Y')
					) AND 
					o.customer_id=:customer_id AND
					od.is_refund='N' AND
					od.website_id=:website_id ORDER BY obi.id DESC";
	$orders_where = array(
		":start_coverage_period"=>date("Y-m-d",strtotime($start_coverage_period)),
		":customer_id"=>$ws_row['customer_id'],
		":website_id"=>$ws_row['id'],
	);
	$order_row = $pdo->selectOne($orders_sql,$orders_where);
	$order_id = 0;
	$display_id = 0;
	$transaction_id = 0;
	$payment_master_id = 0;
	$is_post_date_order = false;
	$status = '';
	$cc_no = '';
	if(!empty($order_row)) {
		$is_paid = true;	
		$order_id = $order_row['id'];
		$display_id = $order_row['display_id'];
		$status = $order_row['status'];
		$cc_no = $order_row['cc_no'];
		$transaction_id = $order_row['transaction_id'];
		$payment_master_id = $order_row['payment_master_id'];

		if(in_array($order_row['status'],array('Pending Payment','Post Payment'))) {
			$is_post_date_order = true;
		}
	}
	return array("is_paid" => $is_paid,"order_id" => $order_id,"status" => $status,"cc_no" => $cc_no,"is_post_date_order" => $is_post_date_order,"display_id" => $display_id,"transaction_id" => $transaction_id,"payment_master_id" => $payment_master_id);
}
 
function valid_csv_cell_value($string='') {
	if(is_string($string)) {
	   $string = str_replace(' ', ' ', $string);
	   $string = str_replace(array('[\', \']'), '', $string);
	   $string = preg_replace('/\[.*\]/U', '', $string);
	   $string = str_replace("-"," ",$string);
	   $string = str_replace( ',', '',$string );
	   $string = str_replace( '.', '',$string );
	   $string = stripslashes($string);

	   $string = preg_replace('/[[:^print:]]/', '', $string); 
	   $string = preg_replace('/[^\x00-\x7F]+/', '', $string);
	   $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '', $string);
	   $string = htmlentities($string, ENT_COMPAT, 'utf-8');
	   $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	  $string = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
	  $string = htmlspecialchars_decode($string, ENT_QUOTES);
	  $string = preg_replace("/[^A-Za-z0-9?![:space:]]/","",$string);
	  $string = preg_replace('/[^A-Za-z0-9\s]/', '', $string);
	  return trim($string, '-');
	}
} 
 
function phoneReplaceMain($phone) {
	return str_replace(array("_", "-", " ", "(", ")"), array("", "", "", "", ""), $phone);
}

function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
 
//********************* Vendor Module Function Code Start ******************
	function get_vendor_id() {
		global $pdo;
		$vendor_id = rand(1000, 9999);
		$sql = "SELECT display_id FROM vendor WHERE display_id = 'VV" . $vendor_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_vendor_id();
		} else {
			return "VV" . $vendor_id;
		}
	}

	function get_vendor_fee_id() {
		global $pdo;
		$fee_id = rand(100000, 999999);
		$sql = "SELECT display_fee_id FROM vendor_fee WHERE display_fee_id = 'F" . $fee_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_vendor_fee_id();
		} else {
			return "F" . $fee_id;
		}
	}

	function delete_vendor_fee($fee_id,$is_benefit_tier){
		global $pdo;
		$feePlanSql = "SELECT id FROM vendor_fee_plan_pricing where fee_id = :fee_id AND is_deleted='N'";
	    $feePlanRow = $pdo->select($feePlanSql, array(":fee_id" => $fee_id));

	    if($is_benefit_tier=='Y'){
	    	$incr=' AND plan_id=0';
	    }else{
	    	$incr=' AND plan_id!=0';
	    }

	    if(!empty($feePlanRow)){
	       $plan_params = array(
	            'is_deleted' => 'Y',
	        );
	        $update_plan_where = array(
	            'clause' => 'fee_id = :fee_id '.$incr,
	            'params' => array(':fee_id' => $fee_id)
	        );
	        $fee_plan_price = $pdo->update("vendor_fee_plan_pricing",$plan_params, $update_plan_where);
	    }
	}
//********************* Vendor Module Function Code End   ******************

//********************* carrier Module Function Code Start ******************
	function get_carrier_id() {
		global $pdo;
		$carrier_id = rand(1000, 9999);
		$sql = "SELECT display_id FROM prd_fees WHERE display_id = 'CC" . $carrier_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_carrier_id();
		} else {
			return "CC" . $carrier_id;
		}
	}

	function get_carrier_fee_id() {
		global $pdo;
		$fee_id = rand(100000, 999999);
		$sql = "SELECT product_code FROM prd_main WHERE product_code = 'F" . $fee_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_carrier_fee_id();
		} else {
			return "F" . $fee_id;
		}
	}

	function get_membership_id() {
		global $pdo;
		$membership_id = rand(1000, 9999);
		$sql = "SELECT display_id FROM prd_fees WHERE display_id = 'MM" . $membership_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_membership_id();
		} else {
			return "MM" . $membership_id;
		}
	}

	function get_membership_fee_id() {
		global $pdo;
		$fee_id = rand(100000, 999999);
		$sql = "SELECT product_code FROM prd_main WHERE product_code = 'F" . $fee_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_carrier_fee_id();
		} else {
			return "F" . $fee_id;
		}
	}

	function get_pmpm_commission_id() {
		global $pdo;
		$pmpm_commission_id = rand(1000, 9999);
		$sql = "SELECT display_id FROM pmpm_commission_rule WHERE display_id = 'PM" . $pmpm_commission_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_pmpm_commission_id();
		} else {
			return "PM" . $pmpm_commission_id;
		}
	}

	function get_advance_comm_id() {
		global $pdo;
		$advance_commission_id = rand(100000, 999999);
		$sql = "SELECT display_id FROM prd_fees WHERE display_id = 'AV" . $advance_commission_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_advance_comm_id();
		} else {
			return "AV" . $advance_commission_id;
		}
	}

	function get_advance_comm_fee_id() {
		global $pdo;
		$advance_commission_id = rand(100000, 999999);
		$sql = "SELECT product_code FROM prd_main WHERE product_code = 'F" . $advance_commission_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_advance_comm_fee_id();
		} else {
			return "F" . $advance_commission_id;
		}
	}

	function get_product_fee_id() {
		global $pdo;
		$fee_id = rand(100000, 999999);
		$sql = "SELECT product_code FROM prd_main WHERE product_code = 'F" . $fee_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return get_product_fee_id();
		} else {
			return "F" . $fee_id;
		}
	}

//********************* carrier Module Function Code End   ******************

function get_real_ipaddress() {
  $my_ip_address = array();

  if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && (!isset($my_ip_address['ip_address']) || !isset($my_ip_address['original_ip_address']))) {
    $ip_address = $_SERVER['HTTP_CF_CONNECTING_IP'];

    if (strlen($ip_address) < 16) {
      if (!isset($my_ip_address['ip_address'])) {
        $my_ip_address['ip_address'] = $ip_address;
      }
      if (!isset($my_ip_address['original_ip_address'])) {
        $my_ip_address['original_ip_address'] = $ip_address;
      }
    } else {
      if (!isset($my_ip_address['original_ip_address'])) {
        $my_ip_address['original_ip_address'] = $ip_address;
      }
    }
  }

  if (isset($_SERVER['REMOTE_ADDR']) && (!isset($my_ip_address['ip_address']) || !isset($my_ip_address['original_ip_address']))) {
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (strlen($ip_address) < 16) {
      if (!isset($my_ip_address['ip_address'])) {
        $my_ip_address['ip_address'] = $ip_address;
      }
      if (!isset($my_ip_address['original_ip_address'])) {
        $my_ip_address['original_ip_address'] = $ip_address;
      }
    } else {
      if (!isset($my_ip_address['original_ip_address'])) {
        $my_ip_address['original_ip_address'] = $ip_address;
      }
    }
  }

  if (!isset($my_ip_address['ip_address']) || !isset($my_ip_address['original_ip_address'])) {
    if (!isset($my_ip_address['ip_address'])) {
      $my_ip_address['ip_address'] = '1.1.1.1';
    }
    if (!isset($my_ip_address['original_ip_address'])) {
      $my_ip_address['original_ip_address'] = '1.1.1.1';
    }
  }

  return $my_ip_address;
}
  
//timezone conversion start
	function convert_to_user_date($date,$format = 'Y-m-d H:i:s',$userTimeZone = 'America/Chicago',$serverTimeZone='UTC'){
		try {
			$dateTime = new DateTime ($date, new DateTimeZone($serverTimeZone));
			$dateTime->setTimezone(new DateTimeZone($userTimeZone));
			return $dateTime->format($format);
		} catch (Exception $e) {
			return '';
		}
	}

	function convert_to_server_date($date, $format='Y-m-d H:i:s', $userTimeZone='America/Chicago',$serverTimeZone = 'UTC'){
	    try {
	        $dateTime = new DateTime ($date, new DateTimeZone(date_default_timezone_get()));
	        $dateTime->setTimezone(new DateTimeZone($serverTimeZone));
	        return $dateTime->format($format);
	    } catch (Exception $e) {
	        return '';
	    }
	}
//timezone conversion end

function checkIsset(&$fieldName,$arr='') {
	if($arr==''){
		if (isset($fieldName)) {
			if(is_string($fieldName)) {
				return trim($fieldName);
			} else {
				return $fieldName;
			}
		} else {
			return "";    
		}
	}else{
		if (isset($fieldName)) {
			return $fieldName;
		} else {
			return array();
		}
	}
}

function splitName($name){
  $full_name=array();
  $names = explode(' ', $name);
  $lastname = $names[count($names) - 1];
  unset($names[count($names) - 1]);
  $firstname = join(' ', $names);
  $full_name['first_name']=$firstname;
  $full_name['last_name']=$lastname;
  return $full_name;
}

/**
 * [isValidUserName use to validate username]
 * @param  [type]  $user_name       [username]
 * @param  integer $agent_id        [agent_id if exists]
 * @param  integer $page_builder_id [page_builder id if exists]
 * @return boolean                  [its return true if valid username else return false on invalid]
 */
function isValidUserName($user_name, $agent_id = 0, $page_builder_id = 0) {
	global $pdo;
	if (empty($agent_id)) {
		$agent_id = 0;
	}
	if (empty($page_builder_id)) {
		$page_builder_id = 0;
	}

	$customerSql = "SELECT COUNT(id) as cnt FROM customer WHERE (user_name = :user_name) AND id!=:agent_id";
	$whr = array(':user_name' => makeSafe($user_name), ":agent_id" => $agent_id);
	$customerRow = $pdo->selectOne($customerSql, $whr);

	$pageBuilderSql = "SELECT count(id) as cnt FROM page_builder WHERE user_name = :user_name AND id!=:page_builder_id";
	$whr = array(':user_name' => makeSafe($user_name), ":page_builder_id" => $page_builder_id);
	$pageBuilderRow = $pdo->selectOne($pageBuilderSql, $whr);

	if (($customerRow["cnt"] + $pageBuilderRow["cnt"]) > 0) {
		return false;
	} else {
		return true;
	}

}

function isFutureDateMain($date, $format = 'Y-m-d') {
	$now = new DateTime();
	$user_date = DateTime::createFromFormat($format, $date);
	if ($user_date >= $now) {
		return true;
	} else {
		return false;
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
		$UserAccessSql = "SELECT id,feature_access,access_type,type,sponsor_id FROM customer WHERE id=:id AND status in ('Active', 'Contracted') AND is_deleted='N' AND type = 'Agent'";
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


function getProductCommissionJson($agent_id) {
	global $pdo;
	$getProducts = $pdo->select("SELECT
			c.agent_coded_level,pr.product_id,rule.commission_level_json,rule.commission_plan_level_json,cr.commission_rule_id,rule.amount_type,rule.commission_on,rule.duration_commission,rule.paid_by
		FROM agent_product_rule pr
			JOIN customer c ON (pr.agent_id=c.id)
			JOIN agent_commission_rule cr ON(
				pr.product_id=cr.product_id
				AND cr.id in(
				select max(id) from agent_commission_rule where agent_id=:agent_id and product_id=pr.product_id and is_deleted='N' and commission_rule_id!=0
				)
			)
			JOIN commission_rule rule ON(cr.commission_rule_id=rule.id)
		WHERE
		pr.agent_id=:agent_id
		AND cr.agent_id=:agent_id
		AND pr.status in('Pending Contracted','Contracted')
		AND cr.is_deleted='N'
		AND pr.is_deleted='N'", array(":agent_id" => $agent_id));
	// pre_print($getProducts);
	$finalJson = array();

	foreach ($getProducts as $getProduct) {
		$rangeLevelJson = array();
		$product_id = $getProduct["product_id"];
		$finalJson[$product_id] = $getProduct;
		$getCommissionRanges = $pdo->select("SELECT id,from_renewal,to_renewal,commission_on,commission_json from commission_rule_range WHERE commission_rule_id=:commission_rule_id AND is_deleted='N' order by from_renewal ASC", array(":commission_rule_id" => $getProduct["commission_rule_id"]));
		// pre_print($getCommissionRanges);
		if (count($getCommissionRanges) > 0) {
			foreach ($getCommissionRanges as $getCommissionRange) {
				$rangeLevelJson[$getCommissionRange["id"]]["product_id"] = $product_id;
				$rangeLevelJson[$getCommissionRange["id"]]["from_renewal"] = $getCommissionRange["from_renewal"];
				$rangeLevelJson[$getCommissionRange["id"]]["to_renewal"] = $getCommissionRange["to_renewal"];
				$rangeLevelJson[$getCommissionRange["id"]]["commission_on"] = $getCommissionRange["commission_on"];
				$rangeLevelJson[$getCommissionRange["id"]]["commission_json"] = $getCommissionRange["commission_json"];
			}
		}
		$finalJson[$product_id]["range"] = $rangeLevelJson;
	}
	return $finalJson;
}

// return '-' if date empty or 0000-00-00
function getCustomDate($date = '',$formate='m/d/Y')
{
	if(!empty($date) && $date!='0000-00-00' && $date!='0000-00-00 00:00:00'){
		return date($formate,strtotime($date));
	}else{
		return '-';
	}
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
function addMonth(\DateTime $date, $monthToAdd)
{
    $year = $date->format('Y');
    $month = $date->format('n');
    $day = $date->format('d');

    $year += floor($monthToAdd / 12);
    $monthToAdd = $monthToAdd % 12;
    $month += $monthToAdd;
    if ($month > 12) {
        $year ++;
        $month = $month % 12;
        if ($month === 0) {
            $month = 12;
        }
    }

    if (! checkdate($month, $day, $year)) {
        $newDate = \DateTime::createFromFormat('Y-n-j', $year . '-' . $month . '-1');
        $newDate->modify('last day of');
    } else {
        $newDate = \DateTime::createFromFormat('Y-n-d', $year . '-' . $month . '-' . $day);
    }
    $newDate->setTime($date->format('H'), $date->format('i'), $date->format('s'));

    return $newDate->format('Y-m-d');
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
	$decimal_value = 0;
	if(!empty($number_array[1]))
		$decimal_value = $number_array[1];
	if ($decimal_value > 0) {
		$decimal_value = rtrim($decimal_value, 0);
		$display_value = $number_array[0] . '.' . $decimal_value;
	} else {
		$display_value = $number_array[0];
	}

	return number_format($display_value, $decimal) . '%';
}
function note_custom_charecter($portal,$user_type,$x, $length, $name, $display_id, $id,$detail_page_url = 'javascript:void(0);')
{
	if($portal == "agent") {
		if($user_type == "lead") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		}else if($user_type == "particpants") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		} elseif($user_type == "customer") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }

		} elseif($user_type == "group") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		}
		
	} elseif($portal == "admin") {
		if($user_type == "lead") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		    
		}else if($user_type == "particpants") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		    
		}elseif($user_type == "customer") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		}
	} elseif($portal == "group") {
		if($user_type == "lead") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		    
		}elseif($user_type == "customer") {
			if (strlen($x) <= $length) {
		        return $x . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    } else {
		        $y = substr($x, 0, $length) . '...';
		        return $y . ' <span class="text-red">' . $name . ' - <a href="'.$detail_page_url.'"><small class="red-link">' . $display_id . '</small></a></span>';
		    }
		}
	}
    
}
function csvToArraywithFieldsMain($filename)
{
    $csv = array_map('str_getcsv', file($filename));
    $headers = $csv[0];
    unset($csv[0]);
    $rowsWithKeys = [];
    foreach ($csv as $row) {
        $newRow = [];
        $is_not_empty=array();
        $row = str_replace(',','', $row);
        if (count(array_filter(array_map('trim', $row))) == 0) {
        	continue;
        }
        
        foreach ($headers as $k => $key) {
            if (trim($key) != "") {
                $newRow[$key] = $row[$k];
            }
            //if csv has empty row than it will not increase counter and not inserted in array code start
                if(!empty($row[$k])){
                    array_push($is_not_empty,"true");
                }else{
                    array_push($is_not_empty,"false");
                }
            //if csv has empty row than it will not increase counter and not inserted in array code end
        }
        
        //if csv row has any 1 column not empty than it will increase counter 
        if(in_array("true", $is_not_empty)){
            $rowsWithKeys[] = $newRow;
        }
    }
    return $rowsWithKeys;
}
function get_lead_tags($agent_id = 0,$location='')
{
	global $pdo;
	$str = '';
	if(!empty($agent_id)) {
		$str .= ' AND sponsor_id = '.$agent_id;
	}
	if($location != 'Admin'){
		$str .=" AND opt_in_type NOT IN('Agent Assisted Enrollment','Agent/Group Invite','Group Enrollee') ";
	}
	$lead_tag_sql = "SELECT opt_in_type as tag,GROUP_CONCAT(DISTINCT sponsor_id) as sponsor_ids
				FROM leads
				WHERE opt_in_type != '' AND is_deleted='N' ".$str."
   				GROUP BY opt_in_type ORDER BY opt_in_type ASC";
	$lead_tag_res = $pdo->select($lead_tag_sql);
	if(!empty($lead_tag_res) && $location != 'Admin') {
		$tag = array();
		$tag[] = array('tag' => 'Agent Assisted Enrollment','sponsor_ids' => 'all');
		$tag[] = array('tag' => 'Agent/Group Invite','sponsor_ids' => 'all');
		$tag[] = array('tag' => 'Group Enrollee','sponsor_ids' => 'all');
		$tag = array_merge($tag,$lead_tag_res);
		return $tag;
	} else if($location == 'Admin'){
		
		$tag = array();
		$tag[] = array('tag' => 'Agent Assisted Enrollment','sponsor_ids' => 'all');
		$tag[] = array('tag' => 'Agent/Group Invite','sponsor_ids' => 'all');
		$tag[] = array('tag' => 'Group Enrollee','sponsor_ids' => 'all');
		$tag[] = array('tag' => 'Admin Assign','sponsor_ids' => 'all');
		$tag[] = array('tag' => 'Enrollment Page','sponsor_ids' => 'all');
		$admin_tags = array('Agent Assisted Enrollment','Agent/Group Invite','Group Enrollee','Admin Assign','Enrollment Page');
		if(!empty($lead_tag_res)){
			foreach($lead_tag_res as $db_tag){
				if(!in_array($db_tag['tag'],$admin_tags)){
					$tag[] = $db_tag;
				}
			}
		}
		return $tag;
	}else{
		return array();
	}
}
function get_left_time_by_seconds($secondsLeft) {

  $minuteInSeconds = 60;
  $hourInSeconds = $minuteInSeconds * 60;
  $dayInSeconds = $hourInSeconds * 24;

  $days = floor($secondsLeft / $dayInSeconds);
  $secondsLeft = $secondsLeft % $dayInSeconds;

  $hours = floor($secondsLeft / $hourInSeconds);
  $secondsLeft = $secondsLeft % $hourInSeconds;

  $minutes= floor($secondsLeft / $minuteInSeconds);

  $seconds = $secondsLeft % $minuteInSeconds;

  $timeComponents = array();

  if ($days > 0) {
    $timeComponents[] = $days . " day" . ($days > 1 ? "s" : "");
  }

  if ($hours > 0) {
    $timeComponents[] = $hours . " hour" . ($hours > 1 ? "s" : "");
  }

  if ($minutes > 0) {
    $timeComponents[] = $minutes . " minute" . ($minutes > 1 ? "s" : "");
  }

  if ($seconds > 0) {
    $timeComponents[] = $seconds . " second" . ($seconds > 1 ? "s" : "");
  }

  if (count($timeComponents) > 0) {
    $formattedTimeRemaining = implode(", ", $timeComponents);
    $formattedTimeRemaining = trim($formattedTimeRemaining);
    $formattedTimeRemaining .= ' left';
  } else {
    $formattedTimeRemaining = "";
  }

  return $formattedTimeRemaining;
}
function generate_eligibility_request($schedule_id){
    global $pdo;
    $today = date("Y-m-d");
    $allow_schedule = 'N';
    $allow_request = 'N';

    if(!empty($schedule_id)){

        $selScheduled = "SELECT * FROM eligibility_schedule WHERE is_deleted='N' AND id=:id";
        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));
        // pre_print($getScheduled);
        if(!empty($getScheduled) && is_array($getScheduled)){
            $schedule_type = $getScheduled['schedule_type'];
            $schedule_frequency = $getScheduled['schedule_frequency'];
            $schedule_end_type = $getScheduled['schedule_end_type'];
            $schedule_end_val = $getScheduled['schedule_end_val'];

            $process_cnt = $getScheduled['process_cnt'];
            $last_processed = $getScheduled['last_processed'];
            $last_process_date = '';
            $file_process_date = '';

            // check End Repeat code start
                if($schedule_end_type == "on_date"){
                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
                    if(strtotime($end_date) >= strtotime($today)){
                        $allow_schedule = 'Y';
                    }
                }else if($schedule_end_type == "no_of_times"){
                    if($process_cnt != '' && $schedule_end_val != ''){
                        if($process_cnt <= $schedule_end_val){
                             $allow_schedule = 'Y';    
                        }
                    }
                }else if($schedule_end_type == "never"){
                         $allow_schedule = 'Y';
                }
            // check End Repeat code ends

            // check if files scheduled today code start
                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
                        $last_process_date = date("Y-m-d",strtotime($last_processed));
                    }
                    

                    // check for daily file code start
                        if($schedule_type == "daily"){
                            if($last_process_date != ''){
                                $file_process_date = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
                            }else{
                                $file_process_date = date("Y-m-d");
                            }
                        }
                    // check for daily file code ends
                    
                    // check for weekly file code start
                        if($schedule_type == "weekly"){
                            $days_of_week = '';
                            $days_of_week = $getScheduled['days_of_week'];
                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();

                            if($last_process_date != ''){
                                $last_processedWeek = '';
                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
                                $start_week = strtotime("last monday",$last_processedWeek);
                                $start_week = date("Y-m-d",$start_week);
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                       
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $file_process_date = $check_week_date;
                                        }
                                    }
                                }
                            }else{
                                $start_week = date("Y-m-d",strtotime("last monday"));
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $file_process_date = $check_week_date;
                                        }
                                    }
                                }
                            }
                        }
                    // check for weekly file code ends
            
                    // check for monthly file code start
                        if($schedule_type == "monthly"){
                            $month_option = '';
                            $month_option = $getScheduled['month_option'];
                            if($month_option != '' && $month_option == "days_of_month"){
                                $days_of_month = '';
                                $days_of_month = $getScheduled['days_of_month'];
                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                     $file_process_date = date('Y-m-d');
                                }
                            }else if($month_option != '' && $month_option == "on_the_day"){
                                $day_type = '';
                                $selected_day = '';
                                $day_type = $getScheduled['day_type'];
                                $selected_day = $getScheduled['selected_day'];
                                if($day_type != '' && $selected_day != ''){
                                    $specific_day = $day_type.' '.$selected_day;
                                    $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
                                }
                            }
                        }
                    // check for monthly file code ends 
                    
                    // check for yearly file code start
                        if($schedule_type == "yearly"){
                            $months = '';
                            $months = $getScheduled['months'];
                            $months_arr = ($months != '') ? explode(",",$months) : array();
                            if(is_array($months_arr) && in_array(date('M'),$months_arr)){
                                $month_option = '';
                                $month_option = $getScheduled['month_option'];
                                if($month_option != '' && $month_option == "days_of_month"){
                                    $days_of_month = '';
                                    $days_of_month = $getScheduled['days_of_month'];
                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                    if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                         $file_process_date = date('Y-m-d');
                                    }
                                }else if($month_option != '' && $month_option == "on_the_day"){
                                    $day_type = '';
                                    $selected_day = '';
                                    $day_type = $getScheduled['day_type'];
                                    $selected_day = $getScheduled['selected_day'];
                                    if($day_type != '' && $selected_day != ''){
                                        $specific_day = $day_type.' '.$selected_day;
                                        $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
                                    }
                                }
                            }
                        }
                    // check for yearly file code ends 
                    
    
                    if(strtotime($file_process_date) == strtotime($today)){
                        $allow_request = "Y";
                    }
                }
            // check if files scheduled today code ends

            // pre_print($allow_request);
            
            // generate schedule request code start
                if($allow_request != '' && $allow_request == "Y"){
                    $date_time = date('Y-m-d H:i',strtotime($file_process_date.''.$getScheduled['time']));
                    $convert_date_time =  convertTimeZone($date_time, $getScheduled['timezone'], $to = "EST");

                    $file_name = '';
                    $file_name = getname("eligibility_files",$getScheduled['file_id'],"file_name","id");
                    $generate_via = '';
                    $generate_via = $getScheduled['generate_via'];
                    $ins_params = array(
                        "file_id" => $getScheduled['file_id'],
                        "file_name" => $file_name,
                        "file_type" => $getScheduled['file_type'],
                        "user_id" => $getScheduled['user_id'],
                        "user_type" => $getScheduled['user_type'],
                        "extra_params" => "",
                        "generate_via" => $generate_via,
                        "is_manual" => 'N',
                        "file_process_date" => $convert_date_time,
                        "status" => "Pending",
                        "created_at" => "msqlfunc_NOW()"
                    );
                    if($generate_via == "Email"){
                        $ins_params['email'] = $getScheduled['email'];
                        $ins_params['password'] = $getScheduled['password'];
                    }
                    $sel_schedule_req = "SELECT * FROM eligibility_requests WHERE is_manual='N' AND file_id=:file_id AND file_process_date=:process_date AND is_deleted='N'";
                    $sel_schedule_paramas = array(":file_id" => $getScheduled['file_id'],":process_date" =>date('Y-m-d H:i:s',strtotime($convert_date_time)));
                    $res_schedule = $pdo->select($sel_schedule_req,$sel_schedule_paramas);
                    if(empty($res_schedule)){
                    	$pdo->insert("eligibility_requests",$ins_params);
                    }
                } 
            // generate schedule request code ends
        }       
    }
}
function next_eligibility_schedule($schedule_id,$date=''){
    global $pdo;
    $today = date("Y-m-d");
    if($date != ''){
    	$today = date("Y-m-d",strtotime($date));
    }
		$nextDate = true;  
		$nextScheduleDate = ''; 
		$count = 1;
		while($nextDate && $count < 31) {
			$count++;
			$today = date('Y-m-d',strtotime($today . "+1 days"));
	
	 		if(!empty($schedule_id)){
		        $selScheduled = "SELECT * FROM eligibility_schedule WHERE is_deleted='N' AND id=:id";
		        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));
		        if(!empty($getScheduled) && is_array($getScheduled)){
		            $schedule_type = $getScheduled['schedule_type'];
		            $schedule_frequency = $getScheduled['schedule_frequency'];
		            $schedule_end_type = $getScheduled['schedule_end_type'];
		            $schedule_end_val = $getScheduled['schedule_end_val'];

		            $process_cnt = $getScheduled['process_cnt'];
		            $last_processed = $getScheduled['last_processed'];
		            $last_process_date = '';
		            $file_process_date = '';

		            // check End Repeat code start
		                if($schedule_end_type == "on_date"){
		                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
		                    if(strtotime($end_date) >= strtotime($today)){
		                        $allow_schedule = 'Y';
		                    }
		                }else if($schedule_end_type == "no_of_times"){
		                    if($process_cnt != '' && $schedule_end_val != ''){
		                        if($process_cnt <= $schedule_end_val){
		                             $allow_schedule = 'Y';    
		                        }
		                    }
		                }else if($schedule_end_type == "never"){
		                         $allow_schedule = 'Y';
		                }
		            // check End Repeat code ends

		            // check if files scheduled today code start
		                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
		                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
		                        $last_process_date = date("Y-m-d",strtotime($last_processed));
		                    }
		                    // check for daily file code start
		                        if($schedule_type == "daily"){
		                            if($last_process_date != ''){
		                                $file_process_date = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
		                            }else{
		                                $file_process_date = date("Y-m-d",strtotime($today));
		                            }
		                        }
		                    // check for daily file code ends
		                
		                    // check for weekly file code start
		                        if($schedule_type == "weekly"){
		                            $days_of_week = '';
		                            $days_of_week = $getScheduled['days_of_week'];
		                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();
		                            if($last_process_date != ''){
		                                $last_processedWeek = '';
		                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
		                                $start_week = strtotime("last monday",$last_processedWeek);
		                                $start_week = date("Y-m-d",$start_week);
		                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
		                                    foreach ($days_of_week_arr as $day) {
		                                        $check_week_date = '';
		                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
		                                       
		                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
		                                             $file_process_date = $check_week_date;
		                                        }
		                                    }
		                                }
		                            }else{
		                            	 	$last_processedWeek = strtotime("$today week");
		                                $start_week = date("Y-m-d",strtotime("Monday this week"));
		                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
		                                    foreach ($days_of_week_arr as $day) {
		                                        $check_week_date = '';
		                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
		                                        if($check_week_date != '' && (strtotime($today) <= strtotime($check_week_date))){
		                                             $file_process_date = $check_week_date;
		                                             break;
		                                        }
		                                    }
		                                }
		                            }
		                        }
		                    // check for weekly file code ends
		            
		                    // check for monthly file code start
		                        if($schedule_type == "monthly"){
		                            $month_option = '';
		                            $month_option = $getScheduled['month_option'];
		                            if($month_option != '' && $month_option == "days_of_month"){
		                                $days_of_month = '';
		                                $days_of_month = $getScheduled['days_of_month'];
		                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
		                                $month_day = date("d",strtotime($today));
		                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
		                                     $file_process_date = date('Y-m-d',strtotime($today));
		                                }
		                            }else if($month_option != '' && $month_option == "on_the_day"){
		                                $day_type = '';
		                                $selected_day = '';
		                                $day_type = $getScheduled['day_type'];
		                                $selected_day = $getScheduled['selected_day'];
		                                if($day_type != '' && $selected_day != ''){
		                                    $specific_day = $day_type.' '.$selected_day;
		                                    $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
		                                }
		                            }
		                        }
		                    // check for monthly file code ends 
		                    // check for yearly file code start
		                        if($schedule_type == "yearly"){
		                            $months = '';
		                            $months = $getScheduled['months'];
		                            $months_arr = ($months != '') ? explode(",",$months) : array();
		                              $get_month = date("M",strtotime($today));
		                            if(is_array($months_arr)){
		                                $month_option = '';
		                                $month_option = $getScheduled['month_option'];
		                                if($month_option != '' && $month_option == "days_of_month"){
		                                    $days_of_month = '';
		                                    $days_of_month = $getScheduled['days_of_month'];
		                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
		                                      $month_day = date("d",strtotime($today));
			                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
			                                     $file_process_date = date('Y-m-d',strtotime($today));
			                                }else{
			                                	foreach ($months_arr as $key => $value) {

			                                		$date = $days_of_month_arr[0] . " " .$value ." ". date("Y",strtotime('+ 1 year',strtotime($today)));
			                                		$file_process_date = date('Y-m-d',strtotime($date));

			                                		if(strtotime($today) <= strtotime($file_process_date)){
			                                			break;
			                                		}
			                                		
			                                	}
			                                }
		                                }else if($month_option != '' && $month_option == "on_the_day"){
		                                    $day_type = '';
		                                    $selected_day = '';
		                                    $day_type = $getScheduled['day_type'];
		                                    $selected_day = $getScheduled['selected_day'];
		                                    if($day_type != '' && $selected_day != ''){
		                                        $specific_day = $day_type.' '.$selected_day;
		                                        $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
		                                        $file_process_date = date('Y-m-d',strtotime('+ 1 year',strtotime($file_process_date)));
		                                    }
		                                }
		                            }
		                        }
		                    // check for yearly file code ends 
		    	
		                    if(strtotime($file_process_date) >= strtotime($today)){
		                        $nextDate = false;
		                      	$date_time = date('Y-m-d H:i',strtotime($file_process_date.''.$getScheduled['time']));
		                    		// $nextScheduleDate = convertTimeZone($date_time, $getScheduled['timezone'], $to = "EST");
		                        return $date_time;
		                    }
		                }
		            // check if files scheduled today code ends
		        }       
    		}
		} 
    return $nextScheduleDate;
}
function clean_csv_cell($value='')
{
	if(is_string($value)){
    $value = trim($value);
    $value = htmlspecialchars_decode($value);
    $value = str_replace(',',' ',$value);
    $value = str_replace("'",'',$value);
    $value = str_replace('.',' ',$value);
    $value = str_replace('\n',' ',$value);
    $value = preg_replace('/[^A-Za-z0-9\s]/', '', $value);
    return $value;
   }
}
function csv_agent_lead_import($csv_agent_lead_id)
{
    global $pdo,$CSV_DIR,$CREDIT_CARD_ENC_KEY;

	$REAL_IP_ADDRESS = get_real_ipaddress();
	include_once dirname(__DIR__) . "/includes/function.class.php";
	$functionClass = new functionsList();

    $file_where = array(":id" => $csv_agent_lead_id);
    $file_row = $pdo->selectOne("SELECT * FROM csv_agent_leads WHERE id=:id", $file_where);

    if (!empty($file_row)) {
        $agent_sql = "SELECT id,type,sponsor_id,rep_id,CONCAT(fname,' ',lname) as agent_name,business_name as group_name FROM customer where id=:id";
        $agent_row = $pdo->selectOne($agent_sql, array(":id" => $file_row['agent_id']));

        $admin_row = array();
        if(!empty($file_row['import_lead_admin_id'])) {
    		$admin_row = $pdo->selectOne('SELECT * FROM admin WHERE id=:id',array(":id" => $file_row['import_lead_admin_id']));
        }

        $csv_file = $CSV_DIR . $file_row['file_name'];
        $csv_file_rows = csvToArraywithFieldsMain($csv_file);

        $existing_lead_count = $file_row['existing_leads'];
        $new_lead_count = $file_row['import_leads'];
        $total_processed_count = 0;
        $mobile_leads = $file_row['mobile_leads'];
        $landline_leads = $file_row['landline_leads'];
        $voip_leads = $file_row['voip_leads'];
        $unknown_leads = $file_row['unknown_leads'];

        $last_inserted_lead_tag = '';

        foreach ($csv_file_rows as $value) {
        	$value = array_map('trim', $value);

			$a = array_map('trim', array_keys($value));
			$b = array_map('trim', $value);
			$value = array_combine($a, $b);

            $total_processed_count++;

            $company_name = $file_row['company_name_field'];
            $fname = $file_row['fname_field'];
            $lname = $file_row['lname_field'];
            $cell_phone = $file_row['cell_phone_field'];
            $email = $file_row['email_field'];
            $state = $file_row['state_field'];
            $state_tag = $file_row['state_tag_field'];
            $email2 = $file_row['email2_field'];
            $school_district = $file_row['school_district_field'];
            $send_date = $file_row['send_date_field'];
            $active_since = $file_row['active_since_field'];
            $address = $file_row['address_field'];
			$address2 = $file_row['address2_field'];

			$pre_tax_deductions_field = $file_row['pre_tax_deductions_field'];
			$post_tax_deductions_field = $file_row['post_tax_deductions_field'];
			$w4_filing_status_field = $file_row['w4_filing_status_field'];
			$w4_no_of_allowances_field = $file_row['w4_no_of_allowances_field'];
			$w4_two_jobs_field = $file_row['w4_two_jobs_field'];
			$w4_dependents_amount_field = $file_row['w4_dependents_amount_field'];
			$w4_4a_other_income_field = $file_row['w4_4a_other_income_field'];
			$w4_4b_deductions_field = $file_row['w4_4b_deductions_field'];
			$w4_additional_withholding_field = $file_row['w4_additional_withholding_field'];
			$state_filing_status_field = $file_row['state_filing_status_field'];
			$state_dependents_field = $file_row['state_dependents_field'];
			$state_additional_withholdings_field = $file_row['state_additional_withholdings_field'];

            $error_reporting_arr = array(
                'agent_csv_id' => $file_row['id'],
            );
            if (!empty($value[$company_name])) {
                $error_reporting_arr['company_name'] = $value[$company_name];
            }
            if (!empty($value[$fname])) {
                $error_reporting_arr['fname'] = $value[$fname];
            }
            if (!empty($value[$lname])) {
                $error_reporting_arr['lname'] = $value[$lname];
            }
            if (!empty($value[$cell_phone])) {
                $error_reporting_arr['cell_phone'] = $value[$cell_phone];
            }
            if (!empty($value[$email])) {
                $error_reporting_arr['email'] = $value[$email];
            }
            if (!empty($value[$state])) {
                $error_reporting_arr['state'] = $value[$state];
            }
            if (!empty($value[$state_tag])) {
                $error_reporting_arr['state_tag'] = $value[$state_tag];
            }
            if (!empty($value[$email2])) {
                $error_reporting_arr['email2'] = $value[$email2];
            }
            if (!empty($value[$address])) {
                $error_reporting_arr['address'] = $value[$address];
            }
            if (!empty($value[$school_district])) {
                $error_reporting_arr['school_district'] = $value[$school_district];
            }
            if (!empty($value[$send_date])) {
                $error_reporting_arr['send_date'] = date("Y-m-d", strtotime($value[$send_date]));
            }
            if (!empty($value[$active_since])) {
                $error_reporting_arr['active_since'] = date("Y-m-d", strtotime($value[$active_since]));
            }

            if($agent_row['type']=='Group'){
				$employee_typeArr = ['new','renew','existing'];
            	$enrollee_id = $file_row['enrollee_id_field'];
            	$annual_earnings = $file_row['annual_earnings_field'];
            	$employee_type = $file_row['employee_type_field'];
            	$hire_date = $file_row['hire_date_field'];
            	$city = $file_row['city_field'];
            	$zip = $file_row['zip_field'];
            	$gender = $file_row['gender_field'];
            	$dob = $file_row['dob_field'];
            	$ssn = $file_row['ssn_field'];
            	$class_name = $file_row['class_name_field'];
            	$coverage_period = $file_row['coverage_period_field'];

            	if (!empty($value[$enrollee_id])) {
                	$error_reporting_arr['enrollee_id'] = $value[$enrollee_id];
            	}
            	if (!empty($value[$annual_earnings])) {
                	$error_reporting_arr['annual_earnings'] = $value[$annual_earnings];
            	}
            	if (!empty($value[$employee_type])) {
                	$error_reporting_arr['employee_type'] = $value[$employee_type];
            	}
            	if (!empty($value[$hire_date])) {
                	$error_reporting_arr['hire_date'] = $value[$hire_date];
            	}
            	
            	if (!empty($value[$city])) {
                	$error_reporting_arr['city'] = $value[$city];
            	}
            	if (!empty($value[$gender])) {
                	$error_reporting_arr['gender'] = $value[$gender];
            	}
            	if (!empty($value[$dob])) {
                	$error_reporting_arr['dob'] = $value[$dob];
            	}
            	if (!empty($value[$ssn])) {
                	$error_reporting_arr['ssn'] = $value[$ssn];
            	}
            	if (!empty($value[$class_name])) {
                	$error_reporting_arr['class_name'] = $value[$class_name];
            	}
            	if (!empty($value[$zip])) {
                	$error_reporting_arr['zip'] = $value[$zip];
            	}
            }

			$is_error = false;

            if(empty($value[$fname])){
                $error_reporting_arr['reason'][] = "First name is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            }
            if(empty($value[$lname])){
                $error_reporting_arr['reason'][] = "Last name is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            }
            if(empty($value[$state])){
                $error_reporting_arr['reason'][] = "State is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            } 
            if(empty($value[$email])) {
                $error_reporting_arr['reason'][] = "Email is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
				$is_error = true;
            }else{
				if (!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', trim($value[$email]))) {
					$error_reporting_arr['reason'][] = "Valid Email is required";
					// $pdo->insert("agent_csv_log", $error_reporting_arr);
					$is_error = true;
				}
			}
            if(empty($value[$cell_phone])){
                $error_reporting_arr['reason'][] = "Phone Number is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            }else{
				$value[$cell_phone] = str_replace(array(" ", "(", ")", "-", "+1"), array("", "", "", "", ""), trim($value[$cell_phone]));
				$value[$cell_phone] = str_replace("+","",$value[$cell_phone]);
				if(!is_numeric($value[$cell_phone])){
                    $error_reporting_arr['reason'][] = "Invalid Phone Number.";
					$is_error = true;
                }else{
					if(strlen($value[$cell_phone]) > 10){
						$error_reporting_arr['reason'][] = "Phone number Maximum length 10 required";
						$is_error = true;
						// $pdo->insert("agent_csv_log", $error_reporting_arr);
					} else if(strlen($value[$cell_phone]) < 10){
						$error_reporting_arr['reason'][] = "Phone number Minimum length 10 required";
						$is_error = true;
						// $pdo->insert("agent_csv_log", $error_reporting_arr);
					}
				}
			}

			$existsGroupEmployee = 0;
            if($agent_row['type']=='Group'){

                if (empty($value[$enrollee_id])) {
                    $error_reporting_arr['reason'][] = "Enrollee ID is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }else if(!empty($value[$enrollee_id])){
					$checkEmpId_sql = "SELECT id, employee_id,status FROM leads WHERE employee_id = :employee_id AND is_deleted='N' AND sponsor_id=:sponsor";
					$whereEmpId = array(':employee_id' => makeSafe($value[$enrollee_id]),":sponsor"=>$file_row['agent_id']);
					$resultEmpId_res = $pdo->selectOne($checkEmpId_sql, $whereEmpId);
					if (count($resultEmpId_res)>0 && $resultEmpId_res['status'] != 'New') {
						$error_reporting_arr['reason'][] = "Enrollee ID converted to ".$resultEmpId_res['status'];
						$is_error = true;
					}
					if(count($resultEmpId_res)>0 && $resultEmpId_res['status'] == 'New'){
						$existsGroupEmployee = $resultEmpId_res['id'];
					}
				}
                if (empty($value[$annual_earnings])) {
                    $error_reporting_arr['reason'][] = "Annual Earning is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }else if(!empty($value[$annual_earnings]) && !is_numeric($value[$annual_earnings])){
					$error_reporting_arr['reason'][] = "Valid Annual Earning is required";
                    $is_error = true;
				}
                if (empty($value[$employee_type])) {
                    $error_reporting_arr['reason'][] = "Employee Type is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }else if(!in_array(strtolower($value[$employee_type]),$employee_typeArr)){
					$error_reporting_arr['reason'][] = "Invalid Employee Type, its required from ".implode('/',$employee_typeArr);
                    $is_error = true;
				}
                if (empty($value[$hire_date])) {
                    $error_reporting_arr['reason'][] = "Relationship Date is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }else if(!empty($value[$hire_date])){
					$seperator=(!strpos($value[$hire_date],'/'))?((strpos($value[$hire_date],'-'))?'-':false):'/';
					if($seperator != ''){
						list($mm, $dd, $yyyy) = explode($seperator, $value[$hire_date]);
						if (empty($mm) || empty($dd) || empty($yyyy) || !checkdate($mm, $dd, $yyyy)) {
							$error_reporting_arr['reason'][] = "Relationship Date is not valid";
							$is_error = true;
						}
					}else{
						$error_reporting_arr['reason'][] = "Relationship Date is not valid";
						$is_error = true;
					}
				}
                // if (empty($value[$city])) {
                //     $error_reporting_arr['reason'][] = "City is empty";
                //     // $pdo->insert("agent_csv_log", $error_reporting_arr);
                //     $is_error = true;
                // }
                if (empty($value[$gender])) {
                    $error_reporting_arr['reason'][] = "Gender is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }else if(!empty($value[$gender]) && !in_array(strtolower($value[$gender]),array('male','female'))){
					$error_reporting_arr['reason'][] = "Valid Gender is required from male/female";
                    $is_error = true;
				}
                if (empty($value[$dob])) {
                    $error_reporting_arr['reason'][] = "Birth Date is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }else if(!empty($value[$dob])){
					$seperator=(!strpos($value[$dob],'/'))?((strpos($value[$dob],'-'))?'-':false):'/';
					if($seperator != ''){
						list($mm, $dd, $yyyy) = explode($seperator, $value[$dob]);
						if (empty($mm) || empty($dd) || empty($yyyy) || !checkdate($mm, $dd, $yyyy)) {
							$error_reporting_arr['reason'][] = "Birth Date is not valid";
							$is_error = true;
						}
					}else{
						$error_reporting_arr['reason'][] = "Birth Date is not valid";
						$is_error = true;
					}
				}
                if (!empty($value[$ssn])) {
					$array = array_unique(str_split(str_replace('-', "", $value[$ssn])));
					$result = $array;
					if(count($result) === 1 ) {
						$error_reporting_arr['reason'][] = "Please enter valid SSN";
						$is_error = true;
					}else if(!is_numeric(str_replace('-', "", $value[$ssn]))|| doesStringContainChain(str_replace('-', "", $value[$ssn]),9) == true){
						$error_reporting_arr['reason'][] = "Please enter valid SSN";
						$is_error = true;
					}
                }
                if (empty($value[$class_name])) {
                    $error_reporting_arr['reason'][] = "Class Name is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                if (empty($value[$zip])) {
                    $error_reporting_arr['reason'][] = "Zip is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                if(!empty($value[$zip])){
                    $zipRes=$pdo->selectOne("SELECT id,state_code FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$value[$zip]));

                    if(empty($zipRes)){
                        $error_reporting_arr['reason'][] = "Zip is not valid";
                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                        $is_error = true;
                    }else{

						$city_response = $functionClass->uspsCityVerification($value[$zip]);
						if(!empty($city_response) && $city_response['status']=='success'){
							$value[$city] = ucwords(strtolower($city_response['city']));
						}

                      $stateRes=$pdo->selectOne("SELECT name,short_name FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$zipRes['state_code']));

                      if(empty($stateRes)){
                        $error_reporting_arr['reason'][] = "Zip is not valid";
                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                        $is_error = true;
                      }else{
                        if((!empty($value[$state]) && trim($stateRes['short_name']) != trim($value[$state]))){
                            $error_reporting_arr['reason'][] = "Zip is not valid";
                            // $pdo->insert("agent_csv_log", $error_reporting_arr);
                            $is_error = true;
                        }
                      }
                    }
                }
                if(empty($value[$address])){
	                $error_reporting_arr['reason'][] = "Address is empty";
	                $is_error = true;
	            }

				if(!empty($value[$pre_tax_deductions_field]) && !is_numeric($value[$pre_tax_deductions_field])){
					$error_reporting_arr['reason'][] = "Valid Pre Tax Deduction is required";
					$is_error = true;
				}
				if(!empty($value[$post_tax_deductions_field]) && !is_numeric($value[$post_tax_deductions_field])){
					$error_reporting_arr['reason'][] = "Valid Post Tax Deduction is required";
					$is_error = true;
				}
				if(!empty($value[$w4_filing_status_field]) && !in_array(strtolower($value[$w4_filing_status_field]),array('single','married'))){
					$error_reporting_arr['reason'][] = "Valid filing status single/married is required";
					$is_error = true;
				}
				if(!empty($value[$w4_no_of_allowances_field])){
					if(is_numeric($value[$w4_no_of_allowances_field]) && (floor($value[$w4_no_of_allowances_field]) != $value[$w4_no_of_allowances_field])){
						$error_reporting_arr['reason'][] = "Decimal value not acceptable for w4 no of allowances is required";
						$is_error = true;
					}else if(!is_numeric($value[$w4_no_of_allowances_field])){
						$error_reporting_arr['reason'][] = "Valid w4 no of allowances is required";
						$is_error = true;
					}else if($value[$w4_no_of_allowances_field] > 12){
						$error_reporting_arr['reason'][] = "Maximum 12 w4 no of allowances is allowed";
						$is_error = true;
					}
				}
				if(!empty($value[$w4_two_jobs_field]) && !in_array(strtolower($value[$w4_two_jobs_field]),array('yes','no'))){
					$error_reporting_arr['reason'][] = "Valid answer yes/no is required for w4 two jobs";
					$is_error = true;
				}
				if(!empty($value[$w4_dependents_amount_field])  && !is_numeric($value[$w4_dependents_amount_field])){
					$error_reporting_arr['reason'][] = "Valid dependents amount is required";
					$is_error = true;
				}
				if(!empty($value[$w4_4a_other_income_field])  && !is_numeric($value[$w4_4a_other_income_field])){
					$error_reporting_arr['reason'][] = "Valid w4 4a other income amount is required";
					$is_error = true;
				}
				if(!empty($value[$w4_4b_deductions_field])  && !is_numeric($value[$w4_4b_deductions_field])){
					$error_reporting_arr['reason'][] = "Valid 4b deductions is required";
					$is_error = true;
				}
				if(!empty($value[$w4_additional_withholding_field])  && !is_numeric($value[$w4_additional_withholding_field])){
					$error_reporting_arr['reason'][] = "Valid additional withholding is required";
					$is_error = true;
				}
				if(!empty($value[$state_filing_status_field]) && !in_array(strtolower($value[$state_filing_status_field]),array('single','married'))){
					$error_reporting_arr['reason'][] = "Valid state filing status single/married is required";
					$is_error = true;
				}
				if(!empty($value[$state_dependents_field])  && !is_numeric($value[$state_dependents_field])){
					$error_reporting_arr['reason'][] = "Valid state dependents is required";
					$is_error = true;
				}
				if(!empty($value[$state_additional_withholdings_field])  && !is_numeric($value[$state_additional_withholdings_field])){
					$error_reporting_arr['reason'][] = "Valid state additional withholdings is required";
					$is_error = true;
				}
            }

			if($is_error && !empty($error_reporting_arr['reason'])){
				$error_reporting_arr['reason'] = implode(',<br>',$error_reporting_arr['reason']);
				$pdo->insert("agent_csv_log", $error_reporting_arr);
			}

			if(!$is_error){
                if ($value[$email] != "" || $value[$cell_phone] != "") {

                    $exist = false;

                    $tmp_cell_phone = '';
                    if(isset($value[$cell_phone])) {
                    	$tmp_cell_phone = str_replace(array(" ", "(", ")", "-", "+1"), array("", "", "", "", ""), trim($value[$cell_phone]));	
                    }

                    if ($tmp_cell_phone != '') {
                        $tmp_cell_phone = substr($tmp_cell_phone, 0, 10);
                    }

                    $is_unsubscribed = false;

                    $tmp_email = '';
                    if(isset($value[$email])) {
                        $tmp_email = trim($value[$email]);    
                    }
                    if (!$exist && $tmp_email != '') {
                        $sel_unsub_leads = "SELECT id FROM leads WHERE (is_email_unsubscribe = 'Y' OR is_sms_unsubscribe = 'Y' OR status IN ('Request Do Not Contact','Do Not Contact')) AND email = :email AND is_deleted='N'";
                        $where_unsub_leads = array(":email" => $tmp_email);
                        $res_unsub_leads = $pdo->selectOne($sel_unsub_leads, $where_unsub_leads);
                        if (!empty($res_unsub_leads)) {
                            $is_unsubscribed = true;
                            if (!$exist) {
                                $error_reporting_arr['reason'][] = "Email on unsubscribed list";
                                $pdo->insert("agent_csv_log", $error_reporting_arr);
                            }
                            $exist = true;
                        }
                    }

                    if (!$exist && !$is_unsubscribed) {
                        if ($tmp_cell_phone != '') {
                            $sel_unsub_leads = "SELECT id FROM leads WHERE (is_email_unsubscribe = 'Y' OR is_sms_unsubscribe = 'Y' OR status IN ('Request Do Not Contact','Do Not Contact')) AND cell_phone = :cell_phone AND is_deleted='N'";
                            $where_unsub_leads = array(":cell_phone" => $tmp_cell_phone);
                            $res_unsub_leads = $pdo->selectOne($sel_unsub_leads, $where_unsub_leads);
                            if (!empty($res_unsub_leads)) {
                                $is_unsubscribed = true;
                                if (!$exist) {
                                    $error_reporting_arr['reason'][] = "Phone number on Do Not Call list";
                                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                }
                                $exist = true;
                            }
                        }
                    }
                    if($agent_row['type']=='Group'){
                    	$coverage_start_date = "";
		            	$coverage_end_date = "";
		            	if (!empty($value[$coverage_period])) {
		                	$error_reporting_arr['coverage_period'] = $value[$coverage_period];
							// Here we use string replace for replace Bigdash Character('–') to Smalldash Character('-')
							$period = str_replace("–","-",$value[$coverage_period]);
		                	$coverage_arr = array_map('trim', explode("-",$period));
		                	$coverage_start_date = isset($coverage_arr[0]) ? $coverage_arr[0] : '';
		                	$coverage_end_date = isset($coverage_arr[1]) ? $coverage_arr[1] : '';
		            	}
		            	$group_company_id=0;
						$group_classes_id=0;
						$group_coverage_id=0;

						$sqlClass="SELECT id FROM group_classes where class_name = :class_name and group_id=:group_id and is_deleted='N'";
						$resClass=$pdo->select($sqlClass,array(":class_name"=>$value[$class_name],":group_id"=>$file_row['agent_id']));

						if(empty($resClass)){
							$error_reporting_arr['reason'][] = "Group Class Not Found";
		                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
							$exist = true;
						}else if(count($resClass) > 1){
							$error_reporting_arr['reason'][] = "Multiple Group Class Found";
		                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
							$exist = true;
						}else{
							$group_classes_id = $resClass[0]['id'];
						}

						$sqlCompany="SELECT id FROM group_company where name = :company_name and group_id=:group_id";
						$resCompany=$pdo->select($sqlCompany,array(":company_name"=>$value[$company_name],":group_id"=>$file_row['agent_id']));

						if($agent_row['group_name'] == $value[$company_name]){
							$group_company_id = 0;
						}else if(empty($resCompany)){
							$error_reporting_arr['reason'][] = "Group Company Not Found";
		                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
							$exist = true;
						}else if(count($resCompany) > 1){
							$error_reporting_arr['reason'][] = "Multiple Group Company Found";
		                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
							$exist = true;
						}else{
							$group_company_id = $resCompany[0]['id'];
						}
						if(empty($coverage_start_date) || empty($coverage_end_date)){
							$error_reporting_arr['reason'][] = "Group Coverage Period Not Found";
		                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
							$exist = true;
						}else{
							$coverage_start_date = date('Y-m-d',strtotime(trim($coverage_start_date)));
							$coverage_end_date = date('Y-m-d',strtotime(trim($coverage_end_date)));

							$sqlCoverage="SELECT gc.id,gc.coverage_period_name FROM group_coverage_period gc 
								JOIN group_coverage_period_offering gco ON (gc.id = gco.group_coverage_period_id AND gco.is_deleted='N')
								WHERE gc.group_id=:group_id AND gco.class_id=:class_id AND gco.status='Active' AND gc.coverage_period_start =:start_date AND gc.coverage_period_end = :end_date group by gc.id";

							$resCoverage=$pdo->select($sqlCoverage,array(":class_id"=>$group_classes_id,":group_id"=>$file_row['agent_id'],":start_date"=>$coverage_start_date,":end_date"=>$coverage_end_date));

							if(empty($resCoverage)){
								$error_reporting_arr['reason'][] = "Group Coverage Not Found";
			                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
								$exist = true;
							}else if(count($resCoverage) > 1){
								$error_reporting_arr['reason'][] = "Multiple Group Coverage Found";
			                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
								$exist = true;
							}else{
								$group_coverage_id = $resCoverage[0]['id'];
							}
						}
						if($exist && !empty($error_reporting_arr['reason'])){
                            $error_reporting_arr['reason'] = implode(',<br>',$error_reporting_arr['reason']);
							$pdo->insert("agent_csv_log", $error_reporting_arr);
                        }
                    }
                    if (!$exist && !$is_unsubscribed) {
                        if ($tmp_email != '') {
                            $checkincr = "";
                            $selEmail = "SELECT id, email, cell_phone FROM leads WHERE sponsor_id=:sponsor AND email=:email AND lead_type=:lead_type AND is_deleted='N' AND id!=:id";
                            $whereEmail = array(":email" => $tmp_email,":sponsor" => $file_row['agent_id'],":lead_type" => $file_row['lead_type'],':id'=>$existsGroupEmployee);
                            $rowEmail = $pdo->selectOne($selEmail, $whereEmail);
                            if ($rowEmail && $file_row['lead_type'] != "Agent/Group") {
                                $error_reporting_arr['reason'][] = "Email Address attached to existing lead";
                                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                $exist = true;
                            } else {
                            	if($file_row['lead_type'] == "Member") {
                            		$cust_selEmail = "SELECT id FROM customer WHERE email=:email AND type IN('Customer')";
	                                $cust_whereEmail = array(':email' => makeSafe($tmp_email));
	                                $cust_rowEmail = $pdo->selectOne($cust_selEmail, $cust_whereEmail);
	                                if ($cust_rowEmail) {
	                                    $error_reporting_arr['reason'][] = "Email Address attached to existing Member";
	                                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
	                                    $exist = true;
	                                }
                            	} else {
                            		/*$cust_selEmail = "SELECT id FROM customer WHERE email=:email AND type IN('Agent','Group')";
	                                $cust_whereEmail = array(':email' => makeSafe($tmp_email));
	                                $cust_rowEmail = $pdo->selectOne($cust_selEmail, $cust_whereEmail);
	                                if ($cust_rowEmail) {
	                                    $error_reporting_arr['reason'][] = "Email Address attached to existing Agent/Group";
	                                    $pdo->insert("agent_csv_log", $error_reporting_arr);
	                                    $exist = true;
	                                }*/	
                            	}
                            }
                        } elseif ($tmp_cell_phone != '') {
                            /*$selCell = "SELECT id,email,cell_phone FROM leads WHERE sponsor_id=:sponsor AND cell_phone=:cell_phone AND lead_type=:lead_type AND is_deleted='N'";
                            $whereCell = array(":cell_phone" => $tmp_cell_phone, ":sponsor" => $file_row['agent_id'], ":lead_type" => $file_row['lead_type']);
                            $rowCell = $pdo->selectOne($selCell, $whereCell);
                            if ($rowCell) {
                                $error_reporting_arr['reason'][] = "Phone Number attached to existing lead";
                                $pdo->insert("agent_csv_log", $error_reporting_arr);
                                $exist = true;
                            }*/
                        }

						if($exist && !empty($error_reporting_arr['reason'])){
							$error_reporting_arr['reason'] = implode(',<br>',$error_reporting_arr['reason']);
							$pdo->insert("agent_csv_log", $error_reporting_arr);
						}

                        if (!$exist) {
                            $lead_disp_id = get_lead_id();
                            $lead_data = array(
                                "sponsor_id" => $file_row['agent_id'],
                                "lead_profession_type" => "Individual",
                                "lead_type" => $file_row['lead_type'],
                                'name'=>trim($value[$fname]).' '.trim($value[$lname]),
                                "fname" => trim($value[$fname]),
                                "lname" => trim($value[$lname]),
                                "cell_phone" => ($tmp_cell_phone ? $tmp_cell_phone : ''),
                                "opt_in_type" => $file_row['lead_tag'],
                                "generate_type" => "CSV",
                                "status" => "New",
                                "sms_scheduled" => 'N',
                                "ip_address" => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                "created_at" => "msqlfunc_NOW()",
                                "updated_at" => "msqlfunc_NOW()",
                            );

							if($existsGroupEmployee == 0){
								$lead_data['lead_id'] = $lead_disp_id;
							}

                            if (isset($value[$company_name]) && !empty(trim($value[$company_name]))) {
                                $lead_data['company_name'] = trim($value[$company_name]);
                            }

                            if (isset($value[$email]) && !empty(trim($value[$email]))) {
                                $lead_data['email'] = trim($value[$email]);
                            }
                            if (isset($value[$email2]) && !empty(trim($value[$email2]))) {
                                $lead_data['email2'] = trim($value[$email2]);
                            }
                            if (isset($value[$state]) && !empty(trim($value[$state]))) {
                            	$sqlState="SELECT name FROM states_c WHERE name=:state OR short_name =:state";
                            	$resState=$pdo->selectOne($sqlState,array(":state"=>$value[$state]));

                            	if(!empty($resState)){
                                	$lead_data['state'] = $resState['name'];
                            	}
                            }
                            if (isset($value[$address]) && !empty(trim($value[$address]))) {
                                $lead_data['address'] = trim($value[$address]);
                            }

							if (isset($value[$address2]) && !empty(trim($value[$address2]))) {
								$lead_data['address2'] = makeSafe($value[$address2]);
							}
                            if (isset($value[$state_tag]) && !empty(trim($value[$state_tag]))) {
                                $lead_data['state_tag'] = trim($value[$state_tag]);
                            }
                            if (isset($value[$school_district]) && !empty(trim($value[$school_district]))) {
                                $lead_data['school_district'] = trim($value[$school_district]);
                            }
                            if (!empty($value[$send_date])) {
                                $lead_data['send_date'] = date('Y-m-d', strtotime($value[$send_date]));
                            }
                            if (!empty($value[$active_since])) {
                                $lead_data['active_since'] = date('Y-m-d', strtotime($value[$active_since]));
                            }
                            if($agent_row['type']=='Group'){
                            	if (!empty($value[$enrollee_id])) {
	                                $lead_data['employee_id'] = trim($value[$enrollee_id]);
	                            }
	                            if (!empty($value[$annual_earnings])) {
	                                $lead_data['income'] = trim($value[$annual_earnings]);
	                            }
	                            if (!empty($value[$employee_type])) {
	                                $lead_data['employee_type'] = trim(ucfirst(strtolower($value[$employee_type])));
	                            }
	                            if (!empty($value[$hire_date])) {
		                            $lead_data['hire_date'] = date('Y-m-d', strtotime($value[$hire_date]));
		                        }
		                        
		                        if (!empty($value[$city])) {
		                            $lead_data['city'] = trim($value[$city]);
		                        }
		                        if (!empty($value[$city])) {
		                            $lead_data['zip'] = trim($value[$zip]);
		                        }
		                        if (!empty($value[$gender])) {
		                            $lead_data['gender'] = trim(ucfirst(strtolower($value[$gender])));
		                        }
		                        if (!empty($value[$dob])) {
		                            $lead_data['birth_date'] = date('Y-m-d', strtotime($value[$dob]));
		                        }
		                        if (!empty($value[$ssn])) {
		                        	$ssn_last_four_digit=substr($value[$ssn],-4,4);
		                        	$ssn="msqlfunc_AES_ENCRYPT('" . $value[$ssn] . "','" . $CREDIT_CARD_ENC_KEY . "')";
		                            $lead_data['is_ssn_itin'] = 'Y';
		                            $lead_data['ssn_itin_num'] = $ssn;
		                            $lead_data['last_four_ssn'] = $ssn_last_four_digit;
		                        }
		                        if(isset($group_company_id)){
				            		$lead_data['group_company_id']=$group_company_id;
		                        }
		                        if(!empty($group_classes_id)){
				            		$lead_data['group_classes_id']=$group_classes_id;
		                        }
		                        if(!empty($group_coverage_id)){
				            		$lead_data['group_coverage_id']=$group_coverage_id;
		                        }
								$tadDetails = [
									"pre_tax_deductions_field" => !empty($value[$pre_tax_deductions_field]) ? $value[$pre_tax_deductions_field] : 0,
									"post_tax_deductions_field" => !empty($value[$post_tax_deductions_field]) ? $value[$post_tax_deductions_field] : 0,
									"w4_filing_status_field" => !empty($value[$w4_filing_status_field]) ? $value[$w4_filing_status_field] : 'Single',
									"w4_no_of_allowances_field" => !empty($value[$w4_no_of_allowances_field]) ? $value[$w4_no_of_allowances_field] : 0,
									"w4_two_jobs_field" => !empty($value[$w4_two_jobs_field]) ? $value[$w4_two_jobs_field] : 'No',
									"w4_dependents_amount_field" => !empty($value[$w4_dependents_amount_field]) ? $value[$w4_dependents_amount_field] : 0,
									"w4_4a_other_income_field" => !empty($value[$w4_4a_other_income_field]) ? $value[$w4_4a_other_income_field] : 0,
									"w4_4b_deductions_field" => !empty($value[$w4_4b_deductions_field]) ? $value[$w4_4b_deductions_field] : 0,
									"w4_additional_withholding_field" => !empty($value[$w4_additional_withholding_field]) ? $value[$w4_additional_withholding_field] : 0,
									"state_filing_status_field" => !empty($value[$state_filing_status_field]) ? $value[$state_filing_status_field] : 'Single',
									"state_dependents_field" => !empty($value[$state_dependents_field]) ? $value[$state_dependents_field] : 0,
									"state_additional_withholdings_field" => !empty($value[$state_additional_withholdings_field]) ? $value[$state_additional_withholdings_field] : 0,
								];
								$lead_data = array_merge($lead_data,$tadDetails);
                            }

							$updatedData = [];
							if($existsGroupEmployee > 0){
								$ins_lead_id = $existsGroupEmployee;

								$csv_row_where = array(
									"clause" => "id=:id",
									"params" => array(":id" => $existsGroupEmployee)
								);
								$updatedData = $pdo->update('leads', $lead_data, $csv_row_where,true);

								if(!empty($group_coverage_id)){

									$csv_coverage_where = array(
										"clause" => "lead_id=:id",
										"params" => array(":id" => $ins_lead_id)
									);
									$pdo->update('leads_assign_coverage', array('group_coverage_period_id' => $group_coverage_id), $csv_coverage_where);
								}

							}else{
								$ins_lead_id = $pdo->insert("leads", $lead_data);

								if(!empty($group_coverage_id)){
									$pdo->insert("leads_assign_coverage", array('lead_id' => $ins_lead_id,'group_coverage_period_id' => $group_coverage_id));
								}
							}
                           
							//update activity feed
							$activity_description = [];
							if(!empty($updatedData)){
								$activity_description['description_customer'] = 'Lead information updated : <br>';
								foreach($updatedData as $key => $data){
									if(array_key_exists($key,$lead_data)){
										$activity_description['key_value']['desc_arr'][$key] = 	'updated from '.$data.' to '.$lead_data[$key];
										if($agent_row['rep_id'] == 'G56118' && $key=='income'){
											$activity_description['key_value']['desc_arr'][$key] = 'Salary updated';
										}
									}
								}
							}
							//update activity feed

							$message = $existsGroupEmployee > 0 ? ' updated' : ' added';
                            if(!empty($admin_row)) {
                            	$desc = array();
	                            $desc['ac_message'] = array(
	                                'ac_red_1' => array(
	                                    'href' => 'lead_details.php?id=' . md5($ins_lead_id),
	                                    'title' => $lead_disp_id,
	                                ),
	                                'ac_message_1' => $message.' by Admin ',
	                                'ac_red_2' => array(
	                                    'href' => 'admin_profile.php?id=' . md5($admin_row['id']),
	                                    'title' => $admin_row['display_id'],
	                                ),                                
	                                'ac_message_2' => ' To ',
	                                'ac_red_3' => array(
	                                    'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
	                                    'title' => $agent_row['rep_id'],
	                                ),                                
	                                'ac_message_3' => ' via upload using '.$file_row['lead_tag'],
	                            );
	                            $desc = json_encode($desc);

								//update activity feed
								if(!empty($updatedData) && $existsGroupEmployee > 0){
									activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Lead updated by Admin', trim($value[$fname]), trim($value[$lname]), json_encode($activity_description));
								}else if(empty($updatedData) && $existsGroupEmployee == 0){
									activity_feed(3,$ins_lead_id,'Lead',$ins_lead_id,'Lead','Lead added by Admin', trim($value[$fname]), trim($value[$lname]), $desc);
								}
                            }else if($agent_row['type']=='Group'){
                            	$desc = array();
	                            $desc['ac_message'] = array(
	                                'ac_red_1' => array(
	                                    'href' => 'lead_details.php?id=' . md5($ins_lead_id),
	                                    'title' => $lead_disp_id,
	                                ),
	                                'ac_message_1' => $message.' by Group ',
	                                'ac_red_2' => array(
	                                    'href' => 'groups_details.php?id=' . md5($agent_row['id']),
	                                    'title' => $agent_row['rep_id'],
	                                ),                                
	                                'ac_message_2' => ' via upload using '.$file_row['lead_tag'],
	                            );
	                            $desc = json_encode($desc);
								
								//update activity feed
								if(!empty($updatedData) && $existsGroupEmployee > 0){
									activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Group updated Enrollee', trim($value[$fname]), trim($value[$lname]), json_encode($activity_description));
								}else if(empty($updatedData) && $existsGroupEmployee == 0){
	                            	activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Group Created Enrollee', trim($value[$fname]), trim($value[$lname]), $desc);	
								}
                            } else {
                            	$desc = array();
	                            $desc['ac_message'] = array(
	                                'ac_red_1' => array(
	                                    'href' => 'lead_details.php?id=' . md5($ins_lead_id),
	                                    'title' => $lead_disp_id,
	                                ),
	                                'ac_message_1' => $message.' by Agent ',
	                                'ac_red_2' => array(
	                                    'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
	                                    'title' => $agent_row['rep_id'],
	                                ),                                
	                                'ac_message_2' => ' via upload using '.$file_row['lead_tag'],
	                            );
	                            $desc = json_encode($desc);
								
								//update activity feed
								if(!empty($updatedData) && $existsGroupEmployee > 0){
									activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Lead updated Agent', trim($value[$fname]), trim($value[$lname]), json_encode($activity_description));
								}else if(empty($updatedData) && $existsGroupEmployee == 0){
	                            	activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Lead added by Agent', trim($value[$fname]), trim($value[$lname]), $desc);	
								}
                            }
                            

                            $new_lead_count++;

                            //if New Tag is inserted then
                            if ($file_row['lead_tag'] != $last_inserted_lead_tag) {
                                $last_inserted_lead_tag = $file_row['lead_tag'];

                                $lead_tag_sql = "SELECT * FROM lead_tag_master WHERE lead_tag=:tag AND is_deleted='N'";
                                $lead_tag_row = $pdo->selectOne($lead_tag_sql, array(":tag" => $file_row['lead_tag']));

                                if (empty($lead_tag_row)) {

                                    $agent_tag_id = 0;

                                    /*$agent_master_sql = "SELECT * FROM agent_tag_master WHERE agent_tag=:tag AND is_deleted='N'";
                                    $agent_master_row = $pdo->selectOne($agent_master_sql, array(":tag" => $agent_row['agent_tag']));
                                    if ($agent_master_row) {
                                        $agent_tag_id = $agent_master_row['id'];
                                    }*/

                                    $tag_data = array(
                                        'lead_tag' => $file_row['lead_tag'],
                                        'agent_tag_id' => $agent_tag_id,
                                        'updated_at' => 'msqlfunc_NOW()',
                                        'created_at' => 'msqlfunc_NOW()'
                                    );
                                    $pdo->insert("lead_tag_master", $tag_data);
                                }
                            }
                        } else {
                            $existing_lead_count++;
                        }
                    }
                }
            }
        }

        $csv_row_upd_data = array(
            "status" => 'Processed',
            "is_running" => 'N',
            'total_processed' => $total_processed_count,
            'existing_leads' => $existing_lead_count,
            'import_leads' => $new_lead_count,
            'mobile_leads' => $mobile_leads,
            'landline_leads' => $landline_leads,
            'voip_leads' => $voip_leads,
            'unknown_leads' => $unknown_leads,
            'updated_at' => 'msqlfunc_NOW()',
        );
        $csv_row_where = array(
            "clause" => "id=:id",
            "params" => array(":id" => $file_row['id'])
        );
        $pdo->update('csv_agent_leads', $csv_row_upd_data, $csv_row_where);

        if(!empty($admin_row)) {
        	$desc = array();
	        $desc['ac_message'] = array(
	            'ac_red_1' => array(
	                'href' => 'admin_profile.php?id=' . md5($admin_row['id']),
	                'title' => $admin_row['display_id'],
	            ),
	            'ac_message_1' => ' CSV Leads Imported'
	        );
	        $desc['lead_type'] = "Lead Type : " . $file_row['lead_type'];
	        $desc['lead_tag'] = "Lead Tag : " . $file_row['lead_tag'];
	        $desc['total_leads'] = "Total Leads : " . $file_row['total_leads'];
	        $desc['new_leads'] = "Total Added Leads : " . $new_lead_count;
	        $desc['existing_leads'] = "Total Existing Leads : " . $existing_lead_count;
	        $desc = json_encode($desc);
	        activity_feed(3, $admin_row['id'], 'Admin', $file_row['id'],'csv_agent_leads','CSV Leads Imported', '', '', $desc);

	        $desc = array();
	        $desc['ac_message'] = array(
	            'ac_red_1' => array(
	                'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
	                'title' => $agent_row['rep_id'],
	            ),
	            'ac_message_1' => 'CSV Leads Imported'
	        );
	        $desc['lead_type'] = "Lead Type : " . $file_row['lead_type'];
	        $desc['lead_tag'] = "Lead Tag : " . $file_row['lead_tag'];
	        $desc['total_leads'] = "Total Leads : " . $file_row['total_leads'];
	        $desc['new_leads'] = "Total Added Leads : " . $new_lead_count;
	        $desc['existing_leads'] = "Total Existing Leads : " . $existing_lead_count;
	        $desc = json_encode($desc);
	        activity_feed(3, $agent_row['id'], $agent_row['type'], $file_row['id'],'csv_agent_leads','CSV Leads Imported', '', '', $desc);
        } else {
        	$desc = array();
	        $desc['ac_message'] = array(
	            'ac_red_1' => array(
	                'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
	                'title' => $agent_row['rep_id'],
	            ),
	            'ac_message_1' => 'CSV Leads Imported'
	        );
	        $desc['lead_type'] = "Lead Type : " . $file_row['lead_type'];
	        $desc['lead_tag'] = "Lead Tag : " . $file_row['lead_tag'];
	        $desc['total_leads'] = "Total Leads : " . $file_row['total_leads'];
	        $desc['new_leads'] = "Total Added Leads : " . $new_lead_count;
	        $desc['existing_leads'] = "Total Existing Leads : " . $existing_lead_count;
	        $desc = json_encode($desc);
	        activity_feed(3, $agent_row['id'], $agent_row['type'], $file_row['id'], 'csv_agent_leads', 'CSV Leads Imported', '', '', $desc);
        }
    }
    return true;
}
function get_control_name_by_column_name($column_name = '')
{
	$control_name = $column_name;
	if($column_name == "birth_date") {
		$control_name = "birthdate";

	} elseif($column_name == "tobacco_use") {
		$control_name = "tobacco_status";
	
	} elseif($column_name == "smoke_use") {
		$control_name = "smoking_status";

	} elseif($column_name == "zip_code") {
		$control_name = "zip";

	} elseif($column_name == "employmentStatus") {
		$control_name = "employment_status";
	}

	return $control_name;
}
function get_column_name_by_control_name($control_name = '')
{
	$column_name = $control_name;

	if($control_name == "birthdate") {
		$column_name = "birth_date";

	} elseif($control_name == "tobacco_status") {
		$column_name = "tobacco_use";
	
	} elseif($control_name == "smoking_status") {
		$column_name = "smoke_use";

	} elseif($control_name == "zip") {
		$column_name = "zip_code";
		
	} elseif($control_name == "employment_status") {
		$column_name = "employmentStatus";
		
	} elseif($control_name == "date_of_hire") {
		$column_name = "hire_date";
	}

	return $column_name;
}
function generate_billing_request($schedule_id){
    global $pdo;
    $today = date("Y-m-d");
    $allow_schedule = 'N';
    $allow_request = 'N';

    if(!empty($schedule_id)){

        $selScheduled = "SELECT * FROM billing_schedule WHERE is_deleted='N' AND id=:id";
        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));
        // pre_print($getScheduled);
        if(!empty($getScheduled) && is_array($getScheduled)){
            $schedule_type = $getScheduled['schedule_type'];
            $schedule_frequency = $getScheduled['schedule_frequency'];
            $schedule_end_type = $getScheduled['schedule_end_type'];
            $schedule_end_val = $getScheduled['schedule_end_val'];

            $process_cnt = $getScheduled['process_cnt'];
            $last_processed = $getScheduled['last_processed'];
            $last_process_date = '';
            $file_process_date = '';

            // check End Repeat code start
                if($schedule_end_type == "on_date"){
                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
                    if(strtotime($end_date) >= strtotime($today)){
                        $allow_schedule = 'Y';
                    }
                }else if($schedule_end_type == "no_of_times"){
                    if($process_cnt != '' && $schedule_end_val != ''){
                        if($process_cnt <= $schedule_end_val){
                             $allow_schedule = 'Y';    
                        }
                    }
                }else if($schedule_end_type == "never"){
                         $allow_schedule = 'Y';
                }
            // check End Repeat code ends

            // check if files scheduled today code start
                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
                        $last_process_date = date("Y-m-d",strtotime($last_processed));
                    }
                    

                    // check for daily file code start
                        if($schedule_type == "daily"){
                            if($last_process_date != ''){
                                $file_process_date = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
                            }else{
                                $file_process_date = date("Y-m-d");
                            }
                        }
                    // check for daily file code ends
                    
                    // check for weekly file code start
                        if($schedule_type == "weekly"){
                            $days_of_week = '';
                            $days_of_week = $getScheduled['days_of_week'];
                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();

                            if($last_process_date != ''){
                                $last_processedWeek = '';
                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
                                $start_week = strtotime("last monday",$last_processedWeek);
                                $start_week = date("Y-m-d",$start_week);
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                       
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $file_process_date = $check_week_date;
                                        }
                                    }
                                }
                            }else{
                                $start_week = date("Y-m-d",strtotime("last monday"));
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $file_process_date = $check_week_date;
                                        }
                                    }
                                }
                            }
                        }
                    // check for weekly file code ends
            
                    // check for monthly file code start
                        if($schedule_type == "monthly"){
                            $month_option = '';
                            $month_option = $getScheduled['month_option'];
                            if($month_option != '' && $month_option == "days_of_month"){
                                $days_of_month = '';
                                $days_of_month = $getScheduled['days_of_month'];
                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                     $file_process_date = date('Y-m-d');
                                }
                            }else if($month_option != '' && $month_option == "on_the_day"){
                                $day_type = '';
                                $selected_day = '';
                                $day_type = $getScheduled['day_type'];
                                $selected_day = $getScheduled['selected_day'];
                                if($day_type != '' && $selected_day != ''){
                                    $specific_day = $day_type.' '.$selected_day;
                                    $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
                                }
                            }
                        }
                    // check for monthly file code ends 
                    
                    // check for yearly file code start
                        if($schedule_type == "yearly"){
                            $months = '';
                            $months = $getScheduled['months'];
                            $months_arr = ($months != '') ? explode(",",$months) : array();
                            if(is_array($months_arr) && in_array(date('M'),$months_arr)){
                                $month_option = '';
                                $month_option = $getScheduled['month_option'];
                                if($month_option != '' && $month_option == "days_of_month"){
                                    $days_of_month = '';
                                    $days_of_month = $getScheduled['days_of_month'];
                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                    if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                         $file_process_date = date('Y-m-d');
                                    }
                                }else if($month_option != '' && $month_option == "on_the_day"){
                                    $day_type = '';
                                    $selected_day = '';
                                    $day_type = $getScheduled['day_type'];
                                    $selected_day = $getScheduled['selected_day'];
                                    if($day_type != '' && $selected_day != ''){
                                        $specific_day = $day_type.' '.$selected_day;
                                        $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
                                    }
                                }
                            }
                        }
                    // check for yearly file code ends 
                    
    
                    if(strtotime($file_process_date) == strtotime($today)){
                        $allow_request = "Y";
                    }
                }
            // check if files scheduled today code ends

            // pre_print($allow_request);
            
            // generate schedule request code start
                if($allow_request != '' && $allow_request == "Y"){
                    $date_time = date('Y-m-d H:i',strtotime($file_process_date.''.$getScheduled['time']));
                    $convert_date_time =  convertTimeZone($date_time, $getScheduled['timezone'], $to = "EST");

                    $file_name = '';
                    $file_name = getname("billing_files",$getScheduled['file_id'],"file_name","id");
                    $generate_via = '';
                    $generate_via = $getScheduled['generate_via'];
                    $ins_params = array(
                        "file_id" => $getScheduled['file_id'],
                        "file_name" => $file_name,
                        "file_type" => $getScheduled['file_type'],
                        "filter_options" => $getScheduled['filter_options'],
                        "user_id" => $getScheduled['user_id'],
                        "user_type" => $getScheduled['user_type'],
                        "extra_params" => "",
                        "generate_via" => $generate_via,
                        "is_manual" => 'N',
                        "file_process_date" => $convert_date_time,
                        "status" => "Pending",
                        "created_at" => "msqlfunc_NOW()"
                    );
                    if($generate_via == "Email"){
                        $ins_params['email'] = $getScheduled['email'];
                        $ins_params['password'] = $getScheduled['password'];
                    }
                    $sel_schedule_req = "SELECT * FROM billing_requests WHERE is_manual='N' AND file_id=:file_id AND file_process_date=:process_date AND is_deleted='N'";
                    $sel_schedule_paramas = array(":file_id" => $getScheduled['file_id'],":process_date" =>date('Y-m-d H:i:s',strtotime($convert_date_time)));
                    $res_schedule = $pdo->select($sel_schedule_req,$sel_schedule_paramas);
                    if(empty($res_schedule)){
                    	$pdo->insert("billing_requests",$ins_params);
                    }
                } 
            // generate schedule request code ends
        }       
    }
}
function next_billing_schedule($schedule_id,$date=''){
    global $pdo;
    $today = date("Y-m-d");
    if($date != ''){
    	$today = date("Y-m-d",strtotime($date));
    }
    $today_org = $today;
		$nextDate = true;  
		$nextScheduleDate = ''; 
		$count = 1;
		$allow_schedule = "N";

		if(!empty($schedule_id)){
	        $selScheduled = "SELECT * FROM billing_schedule WHERE is_deleted='N' AND id=:id";
	        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));
	        if(!empty($getScheduled) && is_array($getScheduled)){
	        	while($nextDate && $count <= 62) {
	        		$count++;
					$today = date('Y-m-d',strtotime($today . "+1 days"));
					$schedule_type = $getScheduled['schedule_type'];
		            $schedule_frequency = $getScheduled['schedule_frequency'];
		            $schedule_end_type = $getScheduled['schedule_end_type'];
		            $schedule_end_val = $getScheduled['schedule_end_val'];

		            $process_cnt = $getScheduled['process_cnt'];
		            $last_processed = $getScheduled['last_processed'];
		            $last_process_date = '';
		            $file_process_date = '';

		            // check End Repeat code start
		                if($schedule_end_type == "on_date"){
		                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
		                    if(strtotime($end_date) >= strtotime($today)){
		                        $allow_schedule = 'Y';
		                    }
		                }else if($schedule_end_type == "no_of_times"){
		                    if($process_cnt != '' && $schedule_end_val != ''){
		                        if($process_cnt <= $schedule_end_val){
		                             $allow_schedule = 'Y';    
		                        }
		                    }
		                }else if($schedule_end_type == "never"){
		                         $allow_schedule = 'Y';
		                }
		            // check End Repeat code ends


		            // check if files scheduled today code start
		                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
		                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
		                        $last_process_date = date("Y-m-d",strtotime($last_processed));
		                    }
		                    // check for daily file code start
		                        if($schedule_type == "daily"){
		                            if($last_process_date != ''){
		                                $file_process_date = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
		                            }else{
		                                $file_process_date = date("Y-m-d",strtotime($today));
		                            }
		                        }
		                    // check for daily file code ends
		                
		                    // check for weekly file code start
		                        if($schedule_type == "weekly"){
		                            $days_of_week = '';
		                            $days_of_week = $getScheduled['days_of_week'];
		                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();
		                            if($last_process_date != ''){
		                                $last_processedWeek = '';
		                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
		                                $start_week = strtotime("last monday",$last_processedWeek);
		                                $start_week = date("Y-m-d",$start_week);
		                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
		                                    foreach ($days_of_week_arr as $day) {
		                                        $check_week_date = '';
		                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
		                                       
		                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
		                                             $file_process_date = $check_week_date;
		                                        }
		                                    }
		                                }
		                            }else{
		                            	 	$last_processedWeek = strtotime("$today week");
		                                $start_week = date("Y-m-d",strtotime("Monday this week"));
		                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
		                                    foreach ($days_of_week_arr as $day) {
		                                        $check_week_date = '';
		                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
		                                        if($check_week_date != '' && (strtotime($today) <= strtotime($check_week_date))){
		                                             $file_process_date = $check_week_date;
		                                             break;
		                                        }
		                                    }
		                                }
		                            }
		                        }
		                    // check for weekly file code ends
		            
		                    // check for monthly file code start
		                        if($schedule_type == "monthly"){
		                            $month_option = '';
		                            $month_option = $getScheduled['month_option'];
		                            if($month_option != '' && $month_option == "days_of_month"){
		                                $days_of_month = '';
		                                $days_of_month = $getScheduled['days_of_month'];
		                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
		                                $month_day = date("d",strtotime($today));
		                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
		                                     $file_process_date = date('Y-m-d',strtotime($today));
		                                }
		                            }else if($month_option != '' && $month_option == "on_the_day"){
		                                $day_type = '';
		                                $selected_day = '';
		                                $day_type = $getScheduled['day_type'];
		                                $selected_day = $getScheduled['selected_day'];
		                                if($day_type != '' && $selected_day != ''){
		                                    $specific_day = $day_type.' '.$selected_day;
		                                    $file_process_date = date('Y-m-d',strtotime("$specific_day of this month",strtotime($today)));
		                                }
		                            }
		                        }
		                    // check for monthly file code ends 
		                    // check for yearly file code start

		                        if($schedule_type == "yearly"){
		                            $months = '';
		                            $months = $getScheduled['months'];
		                            $months_arr = ($months != '') ? explode(",",$months) : array();
		                              $get_month = date("M",strtotime($today));
		                            if(is_array($months_arr)){
		                                $month_option = '';
		                                $month_option = $getScheduled['month_option'];
		                                if($month_option != '' && $month_option == "days_of_month"){
		                                    $days_of_month = '';
		                                    $days_of_month = $getScheduled['days_of_month'];
		                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
		                                      $month_day = date("d",strtotime($today));
			                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
			                                     $file_process_date = date('Y-m-d',strtotime($today));

			                                }else{
			                                	foreach ($months_arr as $key => $value) {

			                                		$date = $days_of_month_arr[0] . " " .$value ." ". date("Y",strtotime('+ 1 year',strtotime($today)));
			                                		$file_process_date = date('Y-m-d',strtotime($date));

			                                		if(strtotime($today) <= strtotime($file_process_date)){

			                                			break;
			                                		}
			                                		
			                                	}
			                                }
		                                }else if($month_option != '' && $month_option == "on_the_day"){
		                                    $day_type = '';
		                                    $selected_day = '';
		                                    $day_type = $getScheduled['day_type'];
		                                    $selected_day = $getScheduled['selected_day'];
		                                    if($day_type != '' && $selected_day != ''){
		                                        $specific_day = $day_type.' '.$selected_day;
		                                        $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
		                                        $file_process_date = date('Y-m-d',strtotime('+ 1 year',strtotime($file_process_date)));
		                                    }
		                                }
		                            }
		                        }
		                    // check for yearly file code ends 
		                        /*echo $file_process_date;
		                        echo "<br/>";*/
		                    if(strtotime($file_process_date) >= strtotime($today_org)){
		                        $nextDate = false;
		                      	$date_time = date('Y-m-d H:i',strtotime($file_process_date.''.$getScheduled['time']));
		                    		// $nextScheduleDate = convertTimeZone($date_time, $getScheduled['timezone'], $to = "EST");
		                        return $date_time;
		                      	break;
		                    }
		                }
		            // check if files scheduled today code ends
	        	}
	        }
	    } 
    return $nextScheduleDate;
}

function custom_charecter($x, $length,$name='',$display_id='',$id='')
{
	if(!empty($name) && !empty($display_id) && !empty($id)){
		if(strlen($x)<=$length)
		{
			return $x.' <span class="text-red">'. $name. ' - <a href="admin_profile.php?id='.md5($id).'" target="_blank"><small class="red-link">'.$display_id .'</small></a></span>';
		}
		else
		{
			$y=substr($x,0,$length) . '...';
			return $y.' <span class="text-red">'. $name. ' - <a href="admin_profile.php?id='.md5($id).'" target="_blank"><small class="red-link">'.$display_id .'</small></a></span>';
		}
	}else{
		if(strlen($x)<=$length){
			return $x;
		}else{
			$y=substr($x,0,$length) .'...' ;
			return $y;
		}
	}
  
}
function get_active_agents_for_select()
{
	global $pdo;
	$agent_sql = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name 
				FROM customer 
				WHERE status IN('Active') AND type IN('Agent')";
	$agent_res = $pdo->select($agent_sql);
	return $agent_res;
}
function get_active_groups_for_select()
{
	global $pdo;
	$group_sql = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name 
				FROM customer 
				WHERE status IN('Active') AND type IN('Group')";
	$group_res = $pdo->select($group_sql);
	return $group_res;
}
function get_note_section_data($portal = 'admin',$user_id = 0,$user_type = '',$extra_params = array()) 
{
	global $pdo;
	$note_search_keyword = isset($extra_params['note_search_keyword']) ? $extra_params['note_search_keyword'] : '';

	 $note_incr = '';

    if ($note_search_keyword != '') {
        $note_incr = " AND  n.description like '%$note_search_keyword%' ";
    }

    if (preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/", $note_search_keyword)) {
        $note_search_keyword = date('Y-m-d', strtotime($note_search_keyword));
        $note_search_keyword = $tz->getDate($note_search_keyword);
        $note_search_keyword = date('Y-m-d', strtotime($note_search_keyword));
        $note_incr = " AND  n.created_at like '%$note_search_keyword%' ";
    }

    $notes_res = array();
    if($portal == 'admin') {
    	if($user_type == "lead") {
    		$note_sql = "SELECT af.id as ac_id,n.created_at,n.description,CONCAT(agent.fname,' ',agent.lname) as agent_name,n.agent_id,agent.rep_id agent_rep_id,CONCAT(admin.fname,' ',admin.lname) as admin_name,n.admin_id,admin.display_id admin_rep_id,n.id as note_id 
                    FROM note n 
                    LEFT JOIN customer agent ON(agent.id=n.agent_id AND agent.is_deleted='N') 
                    LEFT JOIN admin admin ON(admin.id=n.admin_id AND admin.is_deleted='N') 
                    LEFT JOIN activity_feed af ON(af.entity_id=n.id AND af.entity_type='note') 
                    WHERE md5(n.lead_id)=:lead_id AND n.is_deleted='N' $note_incr ORDER BY n.id DESC LIMIT 50";
			$notes = $pdo->select($note_sql, array(":lead_id" => $user_id));
			if(!empty($notes)) {
				foreach ($notes as $key => $note_row) {
					$added_by_name = '';
                    $added_by_rep_id = '';
                    $added_by_id = '';
                    $added_by_detail_page = 'javascript:void(0);';

                    if(!empty($note_row['agent_id'])) {
                        $added_by_name = $note_row['agent_name'];
                        $added_by_rep_id = $note_row['agent_rep_id'];
                        $added_by_id = $note_row['agent_id'];
                        $added_by_detail_page = 'agent_detail_v1.php?id='.md5($note_row['agent_id']);
                    }

                    if(!empty($note_row['admin_id'])) {
                        $added_by_name = $note_row['admin_name'];
                        $added_by_rep_id = $note_row['admin_rep_id'];
                        $added_by_id = $note_row['admin_id'];
                        $added_by_detail_page = 'admin_profile.php?id='.md5($note_row['admin_id']);
                    }

					$note_data = array();
					$note_data = $note_row;
					$note_data['added_by_name'] = $added_by_name;
					$note_data['added_by_rep_id'] = $added_by_rep_id;
					$note_data['added_by_id'] = $added_by_id;
					$note_data['added_by_detail_page'] = $added_by_detail_page;

					$notes_res[] = $note_data;
				}
			}
    	} elseif($user_type == "participants") {
    		$note_sql = "SELECT af.id as ac_id,n.created_at,n.description,CONCAT(agent.fname,' ',agent.lname) as agent_name,n.agent_id,agent.rep_id agent_rep_id,CONCAT(admin.fname,' ',admin.lname) as admin_name,n.admin_id,admin.display_id admin_rep_id,n.id as note_id 
                    FROM note n 
                    LEFT JOIN customer agent ON(agent.id=n.agent_id AND agent.is_deleted='N') 
                    LEFT JOIN admin admin ON(admin.id=n.admin_id AND admin.is_deleted='N') 
                    LEFT JOIN activity_feed af ON(af.entity_id=n.id AND af.entity_type='note') 
                    WHERE md5(n.participants_id)=:participants_id AND n.is_deleted='N' $note_incr ORDER BY n.id DESC LIMIT 50";
			$notes = $pdo->select($note_sql, array(":participants_id" => $user_id));
			if(!empty($notes)) {
				foreach ($notes as $key => $note_row) {
					$added_by_name = '';
                    $added_by_rep_id = '';
                    $added_by_id = '';
                    $added_by_detail_page = 'javascript:void(0);';

                    if(!empty($note_row['agent_id'])) {
                        $added_by_name = $note_row['agent_name'];
                        $added_by_rep_id = $note_row['agent_rep_id'];
                        $added_by_id = $note_row['agent_id'];
                        $added_by_detail_page = 'agent_detail_v1.php?id='.md5($note_row['agent_id']);
                    }

                    if(!empty($note_row['admin_id'])) {
                        $added_by_name = $note_row['admin_name'];
                        $added_by_rep_id = $note_row['admin_rep_id'];
                        $added_by_id = $note_row['admin_id'];
                        $added_by_detail_page = 'admin_profile.php?id='.md5($note_row['admin_id']);
                    }

					$note_data = array();
					$note_data = $note_row;
					$note_data['added_by_name'] = $added_by_name;
					$note_data['added_by_rep_id'] = $added_by_rep_id;
					$note_data['added_by_id'] = $added_by_id;
					$note_data['added_by_detail_page'] = $added_by_detail_page;

					$notes_res[] = $note_data;
				}
			}
    	} elseif($user_type == "customer") {
    		$note_sql = "SELECT af.id as ac_id,n.created_at,n.description,CONCAT(agent.fname,' ',agent.lname) as agent_name,n.agent_id,agent.rep_id agent_rep_id,CONCAT(admin.fname,' ',admin.lname) as admin_name,n.admin_id,admin.display_id admin_rep_id,n.id as note_id 
                    FROM note n 
                    LEFT JOIN customer agent ON(agent.id=n.agent_id AND agent.is_deleted='N') 
                    LEFT JOIN admin admin ON(admin.id=n.admin_id AND admin.is_deleted='N') 
                    LEFT JOIN activity_feed af ON(af.entity_id=n.id AND af.entity_type='note') 
                    WHERE md5(n.customer_id)=:customer_id AND n.is_deleted='N' $note_incr ORDER BY n.id DESC LIMIT 50";
			$notes = $pdo->select($note_sql, array(":customer_id" => $user_id));
			if(!empty($notes)) {
				foreach ($notes as $key => $note_row) {
					$added_by_name = '';
                    $added_by_rep_id = '';
                    $added_by_id = '';
                    $added_by_detail_page = 'javascript:void(0);';

                    if(!empty($note_row['agent_id'])) {
                        $added_by_name = $note_row['agent_name'];
                        $added_by_rep_id = $note_row['agent_rep_id'];
                        $added_by_id = $note_row['agent_id'];
                        $added_by_detail_page = 'agent_detail_v1.php?id='.md5($note_row['agent_id']);
                    }

                    if(!empty($note_row['admin_id'])) {
                        $added_by_name = $note_row['admin_name'];
                        $added_by_rep_id = $note_row['admin_rep_id'];
                        $added_by_id = $note_row['admin_id'];
                        $added_by_detail_page = 'admin_profile.php?id='.md5($note_row['admin_id']);
                    }

					$note_data = array();
					$note_data = $note_row;
					$note_data['added_by_name'] = $added_by_name;
					$note_data['added_by_rep_id'] = $added_by_rep_id;
					$note_data['added_by_id'] = $added_by_id;
					$note_data['added_by_detail_page'] = $added_by_detail_page;

					$notes_res[] = $note_data;
				}
			}
    	}
    } elseif ($portal == 'agent') {
    	if($user_type == "lead") {
    		$note_sql = "SELECT af.id as ac_id,n.created_at,n.description,CONCAT(agent.fname,' ',agent.lname) as agent_name,n.agent_id,agent.rep_id agent_rep_id,CONCAT(admin.fname,' ',admin.lname) as admin_name,n.admin_id,admin.display_id admin_rep_id,n.id as note_id 
                    FROM note n 
                    LEFT JOIN customer agent ON(agent.id=n.agent_id AND agent.is_deleted='N') 
                    LEFT JOIN admin admin ON(admin.id=n.admin_id AND admin.is_deleted='N') 
                    LEFT JOIN activity_feed af ON(af.entity_id=n.id AND af.entity_type='note') 
                    WHERE md5(n.lead_id)=:lead_id AND n.is_deleted='N' $note_incr ORDER BY n.id DESC LIMIT 50";
			$notes = $pdo->select($note_sql, array(":lead_id" => $user_id));
			if(!empty($notes)) {
				foreach ($notes as $key => $note_row) {
					$added_by_name = '';
                    $added_by_rep_id = '';
                    $added_by_id = '';
                    $added_by_detail_page = 'javascript:void(0);';

                    if(!empty($note_row['agent_id'])) {
                        $added_by_name = $note_row['agent_name'];
                        $added_by_rep_id = $note_row['agent_rep_id'];
                        $added_by_id = $note_row['agent_id'];
                        $added_by_detail_page = 'agent_detail_v1.php?id='.md5($note_row['agent_id']);
                    }

                    if(!empty($note_row['admin_id'])) {
                        $added_by_name = 'Admin: '.$note_row['admin_name'];
                        $added_by_rep_id = $note_row['admin_rep_id'];
                        $added_by_id = $note_row['admin_id'];
                        //$added_by_detail_page = 'admin_profile.php?id='.md5($note_row['admin_id']);
                    }

					$note_data = array();
					$note_data = $note_row;
					$note_data['added_by_name'] = $added_by_name;
					$note_data['added_by_rep_id'] = $added_by_rep_id;
					$note_data['added_by_id'] = $added_by_id;
					$note_data['added_by_detail_page'] = $added_by_detail_page;

					$notes_res[] = $note_data;
				}
			}
    	} elseif($user_type == "customer") {
    		$note_sql = "SELECT af.id as ac_id,n.created_at,n.description,CONCAT(agent.fname,' ',agent.lname) as agent_name,n.agent_id,agent.rep_id agent_rep_id,n.id as note_id 
                    FROM note n 
                    JOIN customer agent ON(agent.id=n.agent_id AND agent.is_deleted='N') 
                    LEFT JOIN activity_feed af ON(af.entity_id=n.id AND af.entity_type='note') 
                    WHERE md5(n.customer_id)=:customer_id AND n.is_deleted='N' $note_incr ORDER BY n.id DESC LIMIT 50";
			$notes = $pdo->select($note_sql, array(":customer_id" => $user_id));
			if(!empty($notes)) {
				foreach ($notes as $key => $note_row) {
					$added_by_name = '';
                    $added_by_rep_id = '';
                    $added_by_id = '';
                    $added_by_detail_page = 'javascript:void(0);';

                    if(!empty($note_row['agent_id'])) {
                        $added_by_name = $note_row['agent_name'];
                        $added_by_rep_id = $note_row['agent_rep_id'];
                        $added_by_id = $note_row['agent_id'];
                        $added_by_detail_page = 'agent_detail_v1.php?id='.md5($note_row['agent_id']);
                    }
                    
					$note_data = array();
					$note_data = $note_row;
					$note_data['added_by_name'] = $added_by_name;
					$note_data['added_by_rep_id'] = $added_by_rep_id;
					$note_data['added_by_id'] = $added_by_id;
					$note_data['added_by_detail_page'] = $added_by_detail_page;

					$notes_res[] = $note_data;
				}
			}

    	} elseif($user_type == "group") {

    		$note_sql = "SELECT af.id as ac_id,n.created_at,n.description,CONCAT(agent.fname,' ',agent.lname) as agent_name,n.agent_id,agent.rep_id agent_rep_id,n.id as note_id 
                    FROM note n 
                    JOIN customer agent ON(agent.id=n.agent_id AND agent.is_deleted='N') 
                    LEFT JOIN activity_feed af ON(af.entity_id=n.id AND af.entity_type='note') 
                    WHERE md5(n.customer_id)=:customer_id AND n.is_deleted='N' $note_incr ORDER BY n.id DESC LIMIT 50";
			$notes = $pdo->select($note_sql, array(":customer_id" => $user_id));
			if(!empty($notes)) {
				foreach ($notes as $key => $note_row) {
					$added_by_name = '';
                    $added_by_rep_id = '';
                    $added_by_id = '';
                    $added_by_detail_page = 'javascript:void(0);';

                    if(!empty($note_row['agent_id'])) {
                        $added_by_name = $note_row['agent_name'];
                        $added_by_rep_id = $note_row['agent_rep_id'];
                        $added_by_id = $note_row['agent_id'];
                        $added_by_detail_page = 'agent_detail_v1.php?id='.md5($note_row['agent_id']);
                    }
                    
					$note_data = array();
					$note_data = $note_row;
					$note_data['added_by_name'] = $added_by_name;
					$note_data['added_by_rep_id'] = $added_by_rep_id;
					$note_data['added_by_id'] = $added_by_id;
					$note_data['added_by_detail_page'] = $added_by_detail_page;

					$notes_res[] = $note_data;
				}
			}
    	}
    }
    return $notes_res;
}
function generate_fulfillment_request($schedule_id){
    global $pdo;
    $today = date("Y-m-d");
    $allow_schedule = 'N';
    $allow_request = 'N';

    if(!empty($schedule_id)){

        $selScheduled = "SELECT * FROM fulfillment_schedule WHERE is_deleted='N' AND id=:id";
        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));
        // pre_print($getScheduled);
        if(!empty($getScheduled) && is_array($getScheduled)){
            $schedule_type = $getScheduled['schedule_type'];
            $schedule_frequency = $getScheduled['schedule_frequency'];
            $schedule_end_type = $getScheduled['schedule_end_type'];
            $schedule_end_val = $getScheduled['schedule_end_val'];

            $process_cnt = $getScheduled['process_cnt'];
            $last_processed = $getScheduled['last_processed'];
            $last_process_date = '';
            $file_process_date = '';

            // check End Repeat code start
                if($schedule_end_type == "on_date"){
                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
                    if(strtotime($end_date) >= strtotime($today)){
                        $allow_schedule = 'Y';
                    }
                }else if($schedule_end_type == "no_of_times"){
                    if($process_cnt != '' && $schedule_end_val != ''){
                        if($process_cnt <= $schedule_end_val){
                             $allow_schedule = 'Y';    
                        }
                    }
                }else if($schedule_end_type == "never"){
                         $allow_schedule = 'Y';
                }
            // check End Repeat code ends

            // check if files scheduled today code start
                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
                        $last_process_date = date("Y-m-d",strtotime($last_processed));
                    }
                    

                    // check for daily file code start
                        if($schedule_type == "daily"){
                            if($last_process_date != ''){
                                $file_process_date = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
                            }else{
                                $file_process_date = date("Y-m-d");
                            }
                        }
                    // check for daily file code ends
                    
                    // check for weekly file code start
                        if($schedule_type == "weekly"){
                            $days_of_week = '';
                            $days_of_week = $getScheduled['days_of_week'];
                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();

                            if($last_process_date != ''){
                                $last_processedWeek = '';
                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
                                $start_week = strtotime("last monday",$last_processedWeek);
                                $start_week = date("Y-m-d",$start_week);
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                       
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $file_process_date = $check_week_date;
                                        }
                                    }
                                }
                            }else{
                                $start_week = date("Y-m-d",strtotime("last monday"));
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $file_process_date = $check_week_date;
                                        }
                                    }
                                }
                            }
                        }
                    // check for weekly file code ends
            
                    // check for monthly file code start
                        if($schedule_type == "monthly"){
                            $month_option = '';
                            $month_option = $getScheduled['month_option'];
                            if($month_option != '' && $month_option == "days_of_month"){
                                $days_of_month = '';
                                $days_of_month = $getScheduled['days_of_month'];
                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                     $file_process_date = date('Y-m-d');
                                }
                            }else if($month_option != '' && $month_option == "on_the_day"){
                                $day_type = '';
                                $selected_day = '';
                                $day_type = $getScheduled['day_type'];
                                $selected_day = $getScheduled['selected_day'];
                                if($day_type != '' && $selected_day != ''){
                                    $specific_day = $day_type.' '.$selected_day;
                                    $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
                                }
                            }
                        }
                    // check for monthly file code ends 
                    
                    // check for yearly file code start
                        if($schedule_type == "yearly"){
                            $months = '';
                            $months = $getScheduled['months'];
                            $months_arr = ($months != '') ? explode(",",$months) : array();
                            if(is_array($months_arr) && in_array(date('M'),$months_arr)){
                                $month_option = '';
                                $month_option = $getScheduled['month_option'];
                                if($month_option != '' && $month_option == "days_of_month"){
                                    $days_of_month = '';
                                    $days_of_month = $getScheduled['days_of_month'];
                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                    if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                         $file_process_date = date('Y-m-d');
                                    }
                                }else if($month_option != '' && $month_option == "on_the_day"){
                                    $day_type = '';
                                    $selected_day = '';
                                    $day_type = $getScheduled['day_type'];
                                    $selected_day = $getScheduled['selected_day'];
                                    if($day_type != '' && $selected_day != ''){
                                        $specific_day = $day_type.' '.$selected_day;
                                        $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
                                    }
                                }
                            }
                        }
                    // check for yearly file code ends 
                    
    
                    if(strtotime($file_process_date) == strtotime($today)){
                        $allow_request = "Y";
                    }
                }
            // check if files scheduled today code ends

            // pre_print($allow_request);
            
            // generate schedule request code start
                if($allow_request != '' && $allow_request == "Y"){
                    $date_time = date('Y-m-d H:i',strtotime($file_process_date.''.$getScheduled['time']));
                    $convert_date_time =  convertTimeZone($date_time, $getScheduled['timezone'], $to = "EST");

                    $file_name = '';
                    $file_name = getname("fulfillment_files",$getScheduled['file_id'],"file_name","id");
                    $generate_via = '';
                    $generate_via = $getScheduled['generate_via'];
                    $ins_params = array(
                        "file_id" => $getScheduled['file_id'],
                        "file_name" => $file_name,
                        "file_type" => $getScheduled['file_type'],
                        "user_id" => $getScheduled['user_id'],
                        "user_type" => $getScheduled['user_type'],
                        "extra_params" => "",
                        "generate_via" => $generate_via,
                        "is_manual" => 'N',
                        "file_process_date" => $convert_date_time,
                        "status" => "Pending",
                        "created_at" => "msqlfunc_NOW()"
                    );
                    if($generate_via == "Email"){
                        $ins_params['email'] = $getScheduled['email'];
                        $ins_params['password'] = $getScheduled['password'];
                    }
                    $sel_schedule_req = "SELECT * FROM fulfillment_requests WHERE is_manual='N' AND file_id=:file_id AND file_process_date=:process_date AND is_deleted='N'";
                    $sel_schedule_paramas = array(":file_id" => $getScheduled['file_id'],":process_date" =>date('Y-m-d H:i:s',strtotime($convert_date_time)));
                    $res_schedule = $pdo->select($sel_schedule_req,$sel_schedule_paramas);
                    if(empty($res_schedule)){
                    	$pdo->insert("fulfillment_requests",$ins_params);
                    }
                } 
            // generate schedule request code ends
        }       
    }
}
function next_fulfillment_schedule($schedule_id,$date=''){
    global $pdo;
    $today = date("Y-m-d");
    if($date != ''){
    	$today = date("Y-m-d",strtotime($date));
    }
		$nextDate = true;  
		$nextScheduleDate = ''; 
		$count = 1;
		while($nextDate && $count < 31) {
			$count++;
			$today = date('Y-m-d',strtotime($today . "+1 days"));
	
	 		if(!empty($schedule_id)){
		        $selScheduled = "SELECT * FROM fulfillment_schedule WHERE is_deleted='N' AND id=:id";
		        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));
		        if(!empty($getScheduled) && is_array($getScheduled)){
		            $schedule_type = $getScheduled['schedule_type'];
		            $schedule_frequency = $getScheduled['schedule_frequency'];
		            $schedule_end_type = $getScheduled['schedule_end_type'];
		            $schedule_end_val = $getScheduled['schedule_end_val'];

		            $process_cnt = $getScheduled['process_cnt'];
		            $last_processed = $getScheduled['last_processed'];
		            $last_process_date = '';
		            $file_process_date = '';

		            // check End Repeat code start
		                if($schedule_end_type == "on_date"){
		                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
		                    if(strtotime($end_date) >= strtotime($today)){
		                        $allow_schedule = 'Y';
		                    }
		                }else if($schedule_end_type == "no_of_times"){
		                    if($process_cnt != '' && $schedule_end_val != ''){
		                        if($process_cnt <= $schedule_end_val){
		                             $allow_schedule = 'Y';    
		                        }
		                    }
		                }else if($schedule_end_type == "never"){
		                         $allow_schedule = 'Y';
		                }
		            // check End Repeat code ends

		            // check if files scheduled today code start
		                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
		                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
		                        $last_process_date = date("Y-m-d",strtotime($last_processed));
		                    }
		                    // check for daily file code start
		                        if($schedule_type == "daily"){
		                            if($last_process_date != ''){
		                                $file_process_date = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
		                            }else{
		                                $file_process_date = date("Y-m-d",strtotime($today));
		                            }
		                        }
		                    // check for daily file code ends
		                
		                    // check for weekly file code start
		                        if($schedule_type == "weekly"){
		                            $days_of_week = '';
		                            $days_of_week = $getScheduled['days_of_week'];
		                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();
		                            if($last_process_date != ''){
		                                $last_processedWeek = '';
		                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
		                                $start_week = strtotime("last monday",$last_processedWeek);
		                                $start_week = date("Y-m-d",$start_week);
		                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
		                                    foreach ($days_of_week_arr as $day) {
		                                        $check_week_date = '';
		                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
		                                       
		                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
		                                             $file_process_date = $check_week_date;
		                                        }
		                                    }
		                                }
		                            }else{
		                            	 	$last_processedWeek = strtotime("$today week");
		                                $start_week = date("Y-m-d",strtotime("Monday this week"));
		                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
		                                    foreach ($days_of_week_arr as $day) {
		                                        $check_week_date = '';
		                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
		                                        if($check_week_date != '' && (strtotime($today) <= strtotime($check_week_date))){
		                                             $file_process_date = $check_week_date;
		                                             break;
		                                        }
		                                    }
		                                }
		                            }
		                        }
		                    // check for weekly file code ends
		            
		                    // check for monthly file code start
		                        if($schedule_type == "monthly"){
		                            $month_option = '';
		                            $month_option = $getScheduled['month_option'];
		                            if($month_option != '' && $month_option == "days_of_month"){
		                                $days_of_month = '';
		                                $days_of_month = $getScheduled['days_of_month'];
		                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
		                                $month_day = date("d",strtotime($today));
		                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
		                                     $file_process_date = date('Y-m-d',strtotime($today));
		                                }
		                            }else if($month_option != '' && $month_option == "on_the_day"){
		                                $day_type = '';
		                                $selected_day = '';
		                                $day_type = $getScheduled['day_type'];
		                                $selected_day = $getScheduled['selected_day'];
		                                if($day_type != '' && $selected_day != ''){
		                                    $specific_day = $day_type.' '.$selected_day;
		                                    $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
		                                }
		                            }
		                        }
		                    // check for monthly file code ends 
		                    // check for yearly file code start
		                        if($schedule_type == "yearly"){
		                            $months = '';
		                            $months = $getScheduled['months'];
		                            $months_arr = ($months != '') ? explode(",",$months) : array();
		                              $get_month = date("M",strtotime($today));
		                            if(is_array($months_arr)){
		                                $month_option = '';
		                                $month_option = $getScheduled['month_option'];
		                                if($month_option != '' && $month_option == "days_of_month"){
		                                    $days_of_month = '';
		                                    $days_of_month = $getScheduled['days_of_month'];
		                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
		                                      $month_day = date("d",strtotime($today));
			                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
			                                     $file_process_date = date('Y-m-d',strtotime($today));
			                                }else{
			                                	foreach ($months_arr as $key => $value) {

			                                		$date = $days_of_month_arr[0] . " " .$value ." ". date("Y",strtotime('+ 1 year',strtotime($today)));
			                                		$file_process_date = date('Y-m-d',strtotime($date));

			                                		if(strtotime($today) <= strtotime($file_process_date)){
			                                			break;
			                                		}
			                                		
			                                	}
			                                }
		                                }else if($month_option != '' && $month_option == "on_the_day"){
		                                    $day_type = '';
		                                    $selected_day = '';
		                                    $day_type = $getScheduled['day_type'];
		                                    $selected_day = $getScheduled['selected_day'];
		                                    if($day_type != '' && $selected_day != ''){
		                                        $specific_day = $day_type.' '.$selected_day;
		                                        $file_process_date = date('Y-m-d',strtotime("$specific_day of this month"));
		                                        $file_process_date = date('Y-m-d',strtotime('+ 1 year',strtotime($file_process_date)));
		                                    }
		                                }
		                            }
		                        }
		                    // check for yearly file code ends 
		    	
		                    if(strtotime($file_process_date) >= strtotime($today)){
		                        $nextDate = false;
		                      	$date_time = date('Y-m-d H:i',strtotime($file_process_date.''.$getScheduled['time']));
		                    		// $nextScheduleDate = convertTimeZone($date_time, $getScheduled['timezone'], $to = "EST");
		                        return $date_time;
		                    }
		                }
		            // check if files scheduled today code ends
		        }       
    		}
		} 
    return $nextScheduleDate;
}
function get_app_settings($setting_keys = array())
{	
	global $pdo;

	if(!empty($setting_keys) && is_array($setting_keys)) {
		$select = "SELECT * FROM `app_settings` WHERE setting_key IN('".implode("','",$setting_keys)."')";
		$res = $pdo->select($select);
		if(!empty($res)) {
			$app_settings = array();
			foreach ($res as $key => $row) {
				$app_settings[$row['setting_key']] = $row['setting_value'];
			}
			return $app_settings;
		} else {
			return array();
		}
	}

	if(!empty($setting_keys) && is_string($setting_keys)) {
		$select = "SELECT * FROM `app_settings` WHERE setting_key=:setting_key";
		$row = $pdo->selectOne($select,array(":setting_key" => $setting_keys));
		if(!empty($row)) {
			return $row['setting_value'];
		} else {
			return '';
		}
	}
	return '';
}
function subscribe_email($email,$extra_params)
{
	global $pdo,$SENDGRID_API_KEY;

	$email_row = $pdo->selectOne("SELECT id FROM unsubscribes WHERE type='email' AND is_deleted='N' AND email=:email",array(':email' => $email));

	if(!empty($email_row)) {
		/*$api_user = $sendGridUsername;
	  	$api_key = $sendGridPwd;
		$token = $SENDGRID_API_KEY;

		$data = "api_user=".$api_user."&api_key=".$api_key."&email=".$email;                  
		$url = 'https://api.sendgrid.com/api/unsubscribes.delete.json';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('authorization: Bearer '.$token
		  ));
		$resp = curl_exec($curl);
		curl_close($curl);*/

		$params = array('is_deleted'=>'Y',"removed_date"=>'msqlfunc_NOW()');
		$where = array(
			'clause' => 'id=:id ', 
			'params' => array(':id' => $email_row['id'])
		);
		$pdo->update("unsubscribes", $params, $where);

		if(isset($extra_params['user_id']) && isset($extra_params['user_type']) && isset($extra_params['profile_page']) && isset($extra_params['rep_id'])) {
			$desc = array();
			$desc['ac_message'] = array(
			  	'ac_red_1' => array(
				    'href' => $extra_params['profile_page'],
				    'title' => $extra_params['rep_id'],
			  	),
				'ac_message_1' =>' removed '.$email.' from unsubscribe list',
			);
			activity_feed(3,$extra_params['user_id'],$extra_params['user_type'],$email_row['id'],'unsubscribes','Removed from Unsubscribes','','',json_encode($desc));
		}
	}
	return true;
}
function subscribe_phone($phone,$extra_params)
{
	global $pdo,$SENDGRID_API_KEY;

	$phone_row = $pdo->selectOne("SELECT id FROM unsubscribes WHERE type='sms' AND is_deleted='N' AND phone=:phone",array(':phone' => $phone));

	if(!empty($phone_row)) {
		$params = array('is_deleted'=>'Y',"removed_date"=>'msqlfunc_NOW()');
		$where = array(
			'clause' => 'id=:id ', 
			'params' => array(':id' => $phone_row['id'])
		);
		$pdo->update("unsubscribes", $params, $where);

		if(isset($extra_params['user_id']) && isset($extra_params['user_type']) && isset($extra_params['profile_page']) && isset($extra_params['rep_id'])) {
			$desc = array();
			$desc['ac_message'] = array(
			  	'ac_red_1' => array(
				    'href' => $extra_params['profile_page'],
				    'title' => $extra_params['rep_id'],
			  	),
				'ac_message_1' =>' removed '.(format_telephone($phone)).' from unsubscribe list',
			);
			activity_feed(3,$extra_params['user_id'],$extra_params['user_type'],$phone_row['id'],'unsubscribes','Removed from Unsubscribes','','',json_encode($desc));	
		}
	}
	return true;
}
function unsubscribe_email($email,$extra_params)
{
	global $pdo;

	$row = $pdo->selectOne("SELECT id FROM unsubscribes WHERE type='email' AND is_deleted='N' AND email=:email",array(':email' => $email));

	if(!empty($row)) {
		$unsubscribe_id = $row['id'];
	} else {
		$data = array(
			"type" => 'email',
			"email" => $email,
			"added_date" => 'msqlfunc_NOW()',
	  	);
		$unsubscribe_id = $pdo->insert("unsubscribes",$data);
	}

	if(isset($extra_params['user_id']) && isset($extra_params['user_type']) && isset($extra_params['profile_page']) && isset($extra_params['rep_id'])) {
		$desc = array();
		$desc['ac_message'] = array(
		  	'ac_red_1' => array(
			    'href' => $extra_params['profile_page'],
			    'title' => $extra_params['rep_id'],
		  	),
			'ac_message_1' =>' added '.$email.' to unsubscribe list',
		);
		activity_feed(3,$extra_params['user_id'],$extra_params['user_type'],$unsubscribe_id,'unsubscribes','Added to Unsubscribes','','',json_encode($desc));	
	}
	return true;
}
function unsubscribe_phone($phone,$extra_params)
{
	global $pdo;

	$row = $pdo->selectOne("SELECT id FROM unsubscribes WHERE type='sms' AND is_deleted='N' AND phone=:phone",array(':phone' => $phone));

	if(!empty($row)) {
		$unsubscribe_id = $row['id'];
	} else {
		$data = array(
			"type" => 'sms',
			"phone" => $phone,
			"added_date" => 'msqlfunc_NOW()',
	  	);
		$unsubscribe_id = $pdo->insert("unsubscribes",$data);
	}

	if(isset($extra_params['user_id']) && isset($extra_params['user_type']) && isset($extra_params['profile_page']) && isset($extra_params['rep_id'])) {
		$desc = array();
		$desc['ac_message'] = array(
		  	'ac_red_1' => array(
			    'href' => $extra_params['profile_page'],
			    'title' => $extra_params['rep_id'],
		  	),
			'ac_message_1' =>' added '.(format_telephone($phone)).' to unsubscribe list',
		);
		activity_feed(3,$extra_params['user_id'],$extra_params['user_type'],$unsubscribe_id,'unsubscribes','Added to Unsubscribes','','',json_encode($desc));	
	}
	return true;
}
function get_pyament_methods($sponsor_id,$allow_inactive_assigned_agent = true)
{
	global $pdo;
	$is_cc_accepted = false;
	$is_ach_accepted = false;
	$acceptable_cc = array();

	$pm_res = array();
	$pm_sql = "SELECT pm.is_cc_accepted,pm.is_ach_accepted,pm.type,pmae.global_accept_ach_status,pm.is_assigned_to_all_product,if(pmae.status IS NOT NULL,pmae.status,pm.status) as status,pm.acceptable_cc
				FROM payment_master pm
				LEFT JOIN payment_master_assigned_agent pmae ON (pmae.payment_master_id = pm.id AND pmae.is_deleted='N' AND pmae.status!='Deleted')
				WHERE pm.is_deleted = 'N' AND pm.status IN ('Active') AND IF(pmae.agent_id is not null,pmae.agent_id=:agent_id,1) ORDER BY pm.order_by ASC";
	$pm_res = $pdo->select($pm_sql,array(":agent_id"=>$sponsor_id));
	if(!empty($pm_res)){
		foreach ($pm_res as $key => $value) {
			$status = !empty($value['global_accept_ach_status']) && $value['is_assigned_to_all_product'] == 'Y' ? $value['global_accept_ach_status'] : $value['status'] ;

			if($allow_inactive_assigned_agent == true) {
				if($value['is_cc_accepted']=='Y'){
					$is_cc_accepted = true;
				}
				if($value['is_ach_accepted']=='Y'){
					$is_ach_accepted = true;
				}
			} else {
				if($value['is_cc_accepted']=='Y' && $value['status'] == 'Active'){
					$is_cc_accepted = true;
				}

				if($value['is_ach_accepted']=='Y'){
					if($value['is_assigned_to_all_product'] == 'Y') {
						if($value['global_accept_ach_status']=='Active') {
							$is_ach_accepted = true;
						}
					} else {
						if($value['status'] =='Active' && $value['global_accept_ach_status'] == 'Active') {
							$is_ach_accepted = true;		
						}
					}
				}
			}
			if(!empty($value['acceptable_cc'])){
				$acceptable_cc[] = explode(',', $value['acceptable_cc']);
			}
		}
	}
	/*else{
		$payment_master_sql = "SELECT pm.is_cc_accepted,pm.is_ach_accepted,pm.type,pmae.global_accept_ach_status,pm.is_assigned_to_all_product,pm.status FROM payment_master pm
						LEFT JOIN payment_master_assigned_agent pmae ON (pmae.payment_master_id = pm.id AND pmae.is_deleted='N' AND pmae.status!='Deleted')
						WHERE pm.is_deleted = 'N' AND pm.is_assigned_to_all_agent = 'Y' AND pm.is_assigned_to_all_product = 'Y' AND pm.status IN ('Active') AND pm.type='Global' AND IF(pm.is_ach_accepted='Y',IF(pmae.global_accept_ach_status IS NOT NULL ,pmae.global_accept_ach_status='Active',pm.status='Active'),1) AND pm.is_default_for_ach='Y' AND pm.is_default_for_cc='Y'  ORDER BY pm.order_by ASC";
		$payment_master_res = $pdo->select($payment_master_sql);
		foreach ($payment_master_res as $key => $value) {

			$status = !empty($value['global_accept_ach_status']) ? $value['global_accept_ach_status'] == 'Active' : $value['status'] =='Active';

			if($value['is_cc_accepted']=='Y' && $value['is_assigned_to_all_product'] =='Y' && $status == 'Active'){
				$is_cc_accepted = true;
			}else if($value['is_ach_accepted'] =='Y' && $value['is_assigned_to_all_product'] == 'N'){
				$is_ach_accepted = true;
			}
		}
	}*/
	$temp_acceptable_cc = array();
	if($acceptable_cc){
		foreach ($acceptable_cc as $key => $value) {
			if(is_array($value)){
				foreach ($value as $k => $v) {
					array_push($temp_acceptable_cc,$v);
				}
			}
		}
	}
	return array(
		'is_cc_accepted' => $is_cc_accepted,
		'is_ach_accepted' => $is_ach_accepted,
		'acceptable_cc' => $temp_acceptable_cc ? array_unique($temp_acceptable_cc) : array(),
	);
}
function checkRoutingNumber($routing_number = 0) {
    if (!preg_match('/^[0-9]{9}$/', $routing_number)) {
      return false;
    }
    $checkSum = 0;
    for ($i = 0, $j = strlen($routing_number); $i < $j; $i += 3) {
		$checkSum += ($routing_number[$i] * 3);
		$checkSum += ($routing_number[$i + 1] * 7);
		$checkSum += ($routing_number[$i + 2]);
    }
    if ($checkSum != 0 and ($checkSum % 10) == 0) {
      	return true;
    } else {
      	return false;
    }
}
function save_base64_file_new($base64Code, $folderName, $saveFileName = "") {
	global $UPLOAD_DIR;
	$img = str_replace(array('data:image/png;base64,', 'data:image/jpeg;base64,'), array('', ''), $base64Code);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$uploadFolder = $folderName;
	// echo $uploadFolder;
	if (!is_dir($uploadFolder)) {
		mkdir($uploadFolder, 0777, true);
	}
	if ($saveFileName == "") {
		$saveFileName = md5(time() . rand(1, 100) . time()) . ".png";
	}
	$file = $uploadFolder . $saveFileName;
	$success = file_put_contents($file, $data);
	return $saveFileName;
}
function get_websites($agent_id)
{
	global $pdo;
	$sql = "SELECT pb.page_name,pb.user_name,md5(pb.id) as id
      FROM page_builder pb
      WHERE pb.is_deleted = 'N' AND pb.agent_id=:agent_id AND status='Active'
	  ORDER BY created_at DESC";
  	$where = array(":agent_id" => $agent_id);
  	$res = $pdo->select($sql,$where);
  	if(!empty($res)) {
  		return $res;
  	} else {
  		return array();
  	}
}
function getAllowedProcessedMain($order_id){
    global $pdo;
    $allowedProcessed = true;
	//check order is processedable?
    if($order_id > 0){
        $sql = "SELECT o.id,o.customer_id
            FROM order_details od
            JOIN orders o on(o.id=od.order_id)
            WHERE od.order_id = :order_id AND od.is_deleted='N'";
		$getAutoIncOrderId = $pdo->selectOne($sql, array(':order_id' => makeSafe($order_id)));

        if($getAutoIncOrderId){
            $sql = "SELECT od.product_id,o.id
                FROM order_details od
                JOIN orders o on(o.id=od.order_id)
                WHERE o.id > :order_id and o.status='Payment Approved' and o.customer_id=:customer_id AND od.is_deleted='N'";
            $checkFound = $pdo->select($sql, array(':order_id' =>$getAutoIncOrderId["id"],":customer_id"=>$getAutoIncOrderId["customer_id"]));
            if(count($checkFound)==0){
                //if no future approved order found then check fpr Pending Declined order comes from 
                $sql = "SELECT max(o.id) as id
                FROM order_details od 
                JOIN orders o on(o.id=od.order_id)
                WHERE o.id > :order_id and o.status='Payment Declined' and o.customer_id=:customer_id AND od.is_deleted='N'";
                $getLetestDeclinedOrder = $pdo->selectOne($sql, array(":customer_id"=>$getAutoIncOrderId["customer_id"],":order_id"=>$order_id));
                if($getLetestDeclinedOrder){
                    if(empty($getLetestDeclinedOrder["id"])){
                        $allowedProcessed = true;
                    }else{
                        $allowedProcessed = false;
                    }
                }
            }
        }
        
        if(count($checkFound)>0){
            $allowedProcessed=false;
        }
        // check term date on products
        $sql = "SELECT ws.termination_date
          FROM orders o
          JOIN website_subscriptions ws ON(FIND_IN_SET(ws.id,o.subscription_ids))
          WHERE o.id=:id AND ws.termination_date IS NOT NULL";
        $whr = array(":id" => $order_id);
        $res = $pdo->selectOne($sql,$whr);
        if(!empty($res)){
          $allowedProcessed = false;
		}
		
    } else {
        $allowedProcessed=false;
    }
    return $allowedProcessed;
}
function check_order_can_regenerate_or_not($order_id){
	global $pdo;

	$order_sql = "SELECT o.id,o.customer_id
					FROM orders o 
					JOIN customer c ON(o.customer_id = c.id)
					WHERE o.id=:id";
	$order_where = array(":id" => $order_id);
	$order_row = $pdo->selectOne($order_sql, $order_where);

	$od_sql = "SELECT od.id,w.id as ws_id,w.customer_id,ppt.title,pm.id,od.product_id,od.order_id
			FROM order_details od
			JOIN website_subscriptions w ON(od.plan_id=w.plan_id)
			JOIN prd_matrix pm ON(od.plan_id = pm.id)
			JOIN prd_plan_type ppt ON(ppt.id = pm.plan_type)
			WHERE od.order_id=:id AND w.customer_id=:customer_id AND od.is_deleted='N' GROUP BY od.id";
	$od_where = array(":id" => $order_row['id'], ":customer_id"=>$order_row['customer_id']);
	$od_res = $pdo->select($od_sql,$od_where);
	$ord_prd_count = count($od_res);
	$regenerate_order = true;
	$check_next_step = true;
	if(count($od_res) > 0){
		$not_allow_product= 0;
		foreach ($od_res as $key => $value) {
			$not_allow_product_regenerate =  order_active_product($value['product_id'],$value['order_id'],$order_row['customer_id']);
              if($not_allow_product_regenerate){
                    $not_allow_product++;
              }
		}
		if($ord_prd_count == $not_allow_product){
	        $regenerate_order = false;
	        $check_next_step = false;
	    }
	}
	return $regenerate_order;
}
function order_active_product($product_id,$order_id,$customer_id){
	global $pdo;
	$product_active = true;

	$sel_order = "SELECT od.id FROM orders o
	JOIN order_details od ON(o.id=od.order_id)
	WHERE o.customer_id=:cust_id AND o.id>:order_id AND od.product_id=:product_id AND o.status NOT IN ('Cancelled') AND od.is_deleted='N'";
	$where_order = array(":cust_id" => $customer_id,":order_id" => $order_id,":product_id" => $product_id); 

	$order_res = $pdo->select($sel_order,$where_order);

	if(empty($order_res)){
		 $product_active = false;
	}
	return $product_active;
}
function check_order_can_attempt_again_or_not($order_id) {
	global $pdo;

	$order_sql = "SELECT o.id,o.customer_id,o.is_renewal,o.is_list_bill_order
					FROM orders o 
					JOIN customer c ON(o.customer_id = c.id)
					WHERE o.id=:id";
	$order_where = array(":id" => $order_id);
	$order_row = $pdo->selectOne($order_sql, $order_where);
	if($order_row['is_list_bill_order'] == "Y") {
		return false;
	}

	$od_sql = "SELECT od.start_coverage_period,od.end_coverage_period,w.id as ws_id,w.eligibility_date,w.customer_id,ppt.title,w.status,w.next_purchase_date,w.termination_date
			FROM order_details od
			JOIN website_subscriptions w ON(w.id = od.website_id AND od.plan_id=w.plan_id)
			JOIN prd_matrix pm ON(od.plan_id = pm.id)
			JOIN prd_plan_type ppt ON(ppt.id = pm.plan_type)
			WHERE od.order_id=:id AND w.customer_id=:customer_id AND od.is_deleted='N' GROUP BY od.id";
	$od_where = array(":id" => $order_row['id'], ":customer_id"=>$order_row['customer_id']);
	$od_res = $pdo->select($od_sql,$od_where);
	$attempt_order = true;
	$check_next_step = true;
	if(count($od_res) > 0){
		$lowest_effective_date = date("Y-m-d", strtotime($od_res[0]['eligibility_date']));
		$lowest_end_coverge_date = date("Y-m-d", strtotime($od_res[0]['end_coverage_period']));
		foreach ($od_res as $key => $value) {
			if($order_row['is_renewal'] == 'N'){
	            if(!in_array($value['status'], array('Active','Pending Declined','Pending Payment'))){
					$attempt_order = false;
					$check_next_step = false;
	              	break;
	            }
	        } else {
	        	if(!in_array($value['status'], array('Pending Declined', 'Active','Terminated','Pending Payment'))){
					$attempt_order = false;
					$check_next_step = false;
	              	break;
	            }
	        }
            if(!empty($value['termination_date'])){
            	$attempt_order = false;
              	break;
            }
			if(strtotime($value['eligibility_date']) < strtotime($lowest_effective_date)){
				$lowest_effective_date = date("Y-m-d", strtotime($value['eligibility_date']));
			}
			if(strtotime($value['end_coverage_period']) < strtotime($lowest_end_coverge_date)){
				$lowest_end_coverge_date = date("Y-m-d", strtotime($value['end_coverage_period']));
			}
		}
		if($check_next_step){
			if($order_row['is_renewal'] == 'N'){
				$lowest_effective_date = date("Y-m-d", strtotime("-1 days",strtotime($lowest_effective_date)));
				if(strtotime(date("Y-m-d")) >= strtotime($lowest_effective_date)){
					$attempt_order = false;
				}
			} else {
				$lowest_end_coverge_date = date("Y-m-d", strtotime("-1 days",strtotime($lowest_end_coverge_date)));
				if(strtotime(date("Y-m-d")) >= strtotime($lowest_end_coverge_date)){
					$attempt_order = false;
				}
			}
		}
	}

	return $attempt_order;
}
function is_group_member($member_id) {
  $sponsor_id = getname('customer', $member_id, 'sponsor_id', "id");
  $sponsor_type = getname('customer', $sponsor_id, 'type', "id");
  if ($sponsor_type == 'Group') {
    return true;
  } else {
    return false;
  }
}
function get_date_picker_script($calender_type,$today = '') 
{
  $endDate = date("Y-m-d", strtotime("+75 days",strtotime($today)));
    if($calender_type == "monthly") {
      if(date('d',strtotime($today)) != 1) {
        $today = date('Y-m-d',strtotime('first day of next month',strtotime($today)));
      }
        $startDate = date('Y-m-d',strtotime($today));
        $startView = 1;
        $minViewMode = 1;
    } else {
        $startDate = date('Y-m-d',strtotime($today));
        $startView = 0;
        $minViewMode = 0;
    }
    return (object) array('startDate'=>$startDate,'endDate'=>$endDate,'startView'=>$startView,'minViewMode'=>$minViewMode);
}
function tier_change_charge($ws_id,$new_plan_id,$tier_change_date) {
  global $pdo;
  $response = array();
  $tier_change_date = date("Y-m-d",strtotime($tier_change_date));

  $ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id=:ws_id";
  $ws_row = $pdo->selectOne($ws_sql, array(":ws_id" => $ws_id));
  $old_plan_price = $ws_row['price'];

  $new_plan_sql = "SELECT price FROM prd_matrix WHERE id=:plan_id";
  $new_plan_row = $pdo->selectOne($new_plan_sql, array(":plan_id" => $new_plan_id));
  $new_plan_price = $new_plan_row['price'];
  
  $new_premium = 0;
  $premium_paid = 0;
  $transaction_amount = 0;

  $next_purchase_date = date("Y-m-d",strtotime($ws_row['next_purchase_date']));
  $effective_date = date("Y-m-d",strtotime($ws_row['eligibility_date']));
  $today = date('Y-m-d');
  $is_take_charge = false;
  
  
  if((strtotime($effective_date) > strtotime($today)) && (strtotime($tier_change_date) <= strtotime($effective_date))){
        $is_take_charge = true;  
  }else if((strtotime($tier_change_date) > strtotime($today)) && (strtotime($next_purchase_date) > strtotime($today))){
        
  }else if((strtotime($tier_change_date) <= strtotime($today))){
      $is_take_charge = true;
  }else{
      $is_take_charge = true;
  }

  if($is_take_charge) {
    $coverage_periods = subscription_coverage_periods_form_date($ws_id,$tier_change_date);
    if(!empty($coverage_periods)){
      foreach ($coverage_periods as $key => $coverage_period) {
        $tmp_res = subscription_is_paid_for_coverage_period($ws_id,$coverage_period['start_coverage_period']);
        //pre_print($tmp_res);
        if($tmp_res['is_paid'] == true) {
          $premium_paid += $old_plan_price; 
        }
        $new_premium += $new_plan_price;
      }
    }
    

    if($new_premium < $premium_paid) {
      $transaction_type = 'refund';
      $transaction_amount = $premium_paid - $new_premium; 

    } elseif ($premium_paid <= $new_premium) {
      $transaction_type = 'charge';
      $transaction_amount = $new_premium - $premium_paid; 
    } else {
      $transaction_amount = 0;
    }
  }

  

  if($transaction_amount > 0) {
    $billig_sql = "SELECT card_no,card_type FROM customer_billing_profile WHERE is_default = 'Y' AND customer_id=:customer_id";
    $billig_row = $pdo->selectOne($billig_sql,array('customer_id' => $ws_row['customer_id']));
    if(!empty($billig_row['card_no'])) {
      $response['customer_billing_info'] = $billig_row['card_type'].' *'.$billig_row['card_no'];
    } else {
      $response['customer_billing_info'] = 'no_billing_profile';
    }
  }

  $response['new_plan_price'] = displayAmount($new_plan_price);
  $response['old_plan_price'] = displayAmount($old_plan_price);
  $response['plan_price_diff'] = displayAmount(abs($new_plan_price - $old_plan_price));
  $response['plan_price_diff_org'] = abs($new_plan_price - $old_plan_price);
  $response['transaction_label'] = ($old_plan_price >= $new_plan_price?"Savings":"Increase");
  $response['new_premium'] = displayAmount($new_premium);
  $response['premium_paid'] = displayAmount($premium_paid);
  $response['transaction_amount'] = displayAmount($transaction_amount);
  $response['transaction_type'] = isset($transaction_type) ? $transaction_type : "";
  $response['coverage_periods'] = isset($coverage_periods) ? $coverage_periods : "";
  $response['is_take_charge'] = $is_take_charge;
  return $response;
}
function subscription_coverage_periods_form_date($ws_id,$date = "") {
	global $pdo;
	$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions w WHERE id=:id",array(":id"=>$ws_id));

	$coverage_periods = array();
	$subscription_coverage_periods = subscription_coverage_periods($ws_id);
	$date = date("Y-m-d",strtotime($date));
	$renew_count = '';
	foreach ($subscription_coverage_periods as $key => $scp) {
		if(strtotime($date) <= strtotime($scp['start_coverage_period']) && checkIsset($scp['coverage_period_type']) != 'next') {
		  	$renew_count = $scp['renew_count'];
		  	$coverage_periods[] = array(
			    'start_coverage_period' => $scp['start_coverage_period'],
			    'end_coverage_period' => $scp['end_coverage_period'],
			    'renew_count' => $scp['renew_count'],
		  	);
		}
	}   
  	return $coverage_periods;
}
function subscription_coverage_periods($ws_id,$effective_date="") {
	global $pdo;
	require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
	require_once dirname(__DIR__) . '/includes/list_bill.class.php';
	$listBillObj = new ListBill();
	$enrollDate = new enrollmentDate();
	$coverage_periods = array();
	$today = date("Y-m-d");

	$ws_row = get_main_subscription($ws_id);

	if(!empty($effective_date)){
		$ws_row['eligibility_date'] = $effective_date;
	}

	$eligibility_date = $ws_row['eligibility_date'];  

	/*---- If effective on future || Current coverage is initial coverage -------*/
	if($ws_row['payment_type']=='list_bill'){
		$member_payment_type=$listBillObj->get_pay_period_type($ws_row['id']);		
	}else{
		$member_payment_type = getname('prd_main',$ws_row['product_id'],'payment_type_subscription','id');
	}

	$product_dates=$enrollDate->getCoveragePeriod($eligibility_date,$member_payment_type);

	$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
	$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));


	if(strtotime($today) < strtotime($eligibility_date) || (strtotime($startCoveragePeriod) <= strtotime($today) && strtotime($today) <= strtotime($endCoveragePeriod))) {
		//Is Acive In Future or First Coverage Running
		$coverage_periods[$startCoveragePeriod] = array(
			'start_coverage_period' => $startCoveragePeriod,
			'end_coverage_period' => $endCoveragePeriod,
			'renew_count' => 1,
		);
		$tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));

		$product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

		$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
		$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

		// $coverage_periods[$startCoveragePeriod] = array(
		// 	'start_coverage_period' => $startCoveragePeriod,
		// 	'end_coverage_period' => $endCoveragePeriod,
		// 	'renew_count' => 2,
		// );  
	} else {
		$tmp_eligibility_date = $eligibility_date;
		$is_last_coverage_period = false;
		$renew_count = 1;

		while ($is_last_coverage_period == false) {
			$product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);
			$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
			$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

			$tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));

			if(strtotime($today) <= strtotime($startCoveragePeriod)) {
				$is_last_coverage_period = true;  
			}

			// Coverage start is same as effective date 
			if(strtotime($eligibility_date) == strtotime($startCoveragePeriod)) {
				$text = "Back to effective date (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
				$coverage_period_type = 'initial';

			// Coverage is over
			} elseif (strtotime($endCoveragePeriod) < strtotime($today)) {
				$text = "First of previous billing cycle (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
				$coverage_period_type = 'previous';
			  
			} elseif (strtotime($today) < strtotime($startCoveragePeriod)) {
				$text = "First of next billing cycle (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
				$coverage_period_type = 'next';

			} else {
				$text = "First of current billing cycle (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
				$coverage_period_type = 'current';
			}
			$coverage_periods[$startCoveragePeriod] = array(
				'start_coverage_period' => $startCoveragePeriod,
				'end_coverage_period' => $endCoveragePeriod,
				'coverage_period_type' => $coverage_period_type,
				'renew_count' => $renew_count,
			);
			$renew_count++;
		}
	}
	return $coverage_periods;
}
function coverage_periods_for_effective_date_change($ws_id,$effective_date="",$count=1) {
	global $pdo;
	require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
	require_once dirname(__DIR__) . '/includes/list_bill.class.php';
	$listBillObj = new ListBill();
	$enrollDate = new enrollmentDate();
	$coverage_periods = array();
	$today = date("Y-m-d");
	
	$ws_row = get_main_subscription($ws_id);

	if(!empty($effective_date)){
		$ws_row['eligibility_date'] = $effective_date;
	}

	$eligibility_date = $ws_row['eligibility_date'];

	/*---- If effective on future || Current coverage is initial coverage -------*/
	if($ws_row['payment_type']=='list_bill'){
		$member_payment_type=$listBillObj->get_pay_period_type($ws_row['id']);		
	}else{
		$member_payment_type = getname('prd_main',$ws_row['product_id'],'payment_type_subscription','id');
	}

	$product_dates=$enrollDate->getCoveragePeriod($eligibility_date,$member_payment_type);

	$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
	$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));


	$tmp_eligibility_date = $eligibility_date;
	$is_last_coverage_period = false;
	$renew_count = 1;

	while ($is_last_coverage_period == false) {
		$product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);
		$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
		$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

		$tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));

		if($count == $renew_count) {
			$is_last_coverage_period = true;  
		}

		// Coverage start is same as effective date 
		if(strtotime($eligibility_date) == strtotime($startCoveragePeriod)) {
			$text = "Back to effective date (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
			$coverage_period_type = 'initial';

		// Coverage is over
		} elseif (strtotime($endCoveragePeriod) < strtotime($today)) {
			$text = "First of previous billing cycle (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
			$coverage_period_type = 'previous';
		  
		} elseif (strtotime($today) < strtotime($startCoveragePeriod)) {
			$text = "First of next billing cycle (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
			$coverage_period_type = 'next';

		} else {
			$text = "First of current billing cycle (".(date("m/d/Y",strtotime($startCoveragePeriod))).")";
			$coverage_period_type = 'current';
		}
		$coverage_periods[$startCoveragePeriod] = array(
			'start_coverage_period' => $startCoveragePeriod,
			'end_coverage_period' => $endCoveragePeriod,
			'coverage_period_type' => $coverage_period_type,
			'renew_count' => $renew_count,
		);
		$renew_count++;
	}
	
	return $coverage_periods;
}
function get_main_subscription($ws_id) {
  	global $pdo;
  	$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions w WHERE id=:id",array(":id"=>$ws_id));
  	if(!empty($ws_row['parent_ws_id'])) {
    	$ws_row = get_main_subscription($ws_row['parent_ws_id']);
  	}
  	return $ws_row;
}
function cancel_order($order_id, $extra = array()) {
	global $pdo, $CREDIT_CARD_ENC_KEY,$SITE_ENV;
	$BROWSER = getBrowser();
	$OS = getOS($_SERVER['HTTP_USER_AGENT']);
	$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	$REAL_IP_ADDRESS = get_real_ipaddress();
	require_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
	include_once dirname(__DIR__) . "/includes/commission.class.php";
	include_once dirname(__DIR__) . "/includes/function.class.php";

	$commObj = new Commission();
	$functionClass = new functionsList();

	$order_status = "Refund";
	$STS = "Refund";
	$reverse_commission = 'Yes';

	$order_sql = "SELECT *,now() as currentTime FROM orders WHERE id=:id";
	$order_where = array(":id" => $order_id);
	$order_row = $pdo->selectOne($order_sql, $order_where);

	$refund_amount = $order_row['grand_total'];

	$refunded_amount_sql = "SELECT SUM(refund_amount) as total_refund_amount FROM return_orders 
	         WHERE refund_status= 'Success' AND order_id=:order_id GROUP BY order_id";
	$refunded_amount_row = $pdo->selectOne($refunded_amount_sql,array(":order_id"=>$order_row['id']));

  	if(!empty($refunded_amount_row['total_refund_amount'])) {
	    $refunded_amount = $refunded_amount_row['total_refund_amount']; 
	    $refund_amount = $refund_amount - $refunded_amount;
  	}

  	$upd_ord_status = false;
  	if($extra['is_partial_refund'] && $extra['is_partial_refund'] == 'Y'){
		$temp_prd_id = $extra['ws_row']['product_id'];
		$refundAdminFee = isset($extra['adminfeeRefundAmt']) ? $extra['adminfeeRefundAmt'] : 0;
		$order_row = $pdo->selectOne("SELECT o.*,pm.plan_type,od.unit_price FROM order_details od JOIN orders o ON (od.order_id = o.id) JOIN prd_matrix pm ON(pm.id = od.plan_id) WHERE o.id = :order_id AND od.product_id = :product_id AND od.plan_id = :plan_id AND od.is_deleted='N'",array(':order_id' => $order_id,':product_id' => $temp_prd_id,':plan_id' => $extra['ws_row']['plan_id']));
		$order_row['unit_price'] += $refundAdminFee;
		if($refund_amount == $order_row['unit_price']) {
			$upd_ord_status = true;
		}
		$refund_amount = $order_row['unit_price'];
  	}

	$customer_sql = "SELECT c.id,c.email,c.cell_phone,
	                IFNULL(sp.payment_master_id,0) AS payment_master_id,
	                IFNULL(sp.ach_master_id,0) AS ach_master_id
	                FROM customer c
	                LEFT JOIN customer sp ON sp.id=c.sponsor_id
	                WHERE c.id=:customer_id";
	$customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $order_row['customer_id']));

	$billing_sql = "SELECT *,
	              AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number,
	              AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number,
	              AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no
	              FROM order_billing_info WHERE order_id=:id";
	$billing_where = array(":id" => $order_row['id']);
	$billing_row = $pdo->selectOne($billing_sql, $billing_where);
	$refund_type = $billing_row['payment_mode'];

	//Refund API start
	$cc_params = array();
	$decline_log_id="";
	$cc_params['order_id'] = $order_row['display_id'];
	$cc_params['customer_id'] = getname('customer', $order_row['customer_id'], 'rep_id', 'id');
	$cc_params['amount'] = $refund_amount;
	$cc_params['transaction_id'] = $order_row['transaction_id'];

	$payment_master_id = $order_row['payment_master_id'];
	$checkProcessorStatus = $pdo->selectOne("SELECT id FROM payment_master WHERE status IN('Active','Inactive') AND id=:id AND is_deleted='N'",array(":id"=>$payment_master_id));
	if(empty($checkProcessorStatus)){
		return array("status" => false, "error" => "refund_failed", "message" => "Processor Closed");
	}
	
	if ($refund_type == "ACH") {
		// $payment_master_id = $customer_row['ach_master_id'];
		$cc_params['ach_account_type'] = $billing_row['ach_account_type'];
		$cc_params['ach_routing_number'] = $billing_row['ach_routing_number'];
		$cc_params['ach_account_number'] = $billing_row['ach_account_number'];
		$cc_params['name_on_account'] = $billing_row['fname'] . ' ' . $billing_row['lname'];
		$cc_params['bankname'] = $billing_row['bankname'];
	} else {
		// $payment_master_id = $customer_row['payment_master_id'];
		if ($SITE_ENV !='Live') {
			$cc_params['cc_no'] = "4111111111111114";
		}
		$cc_params['ccnumber'] = $billing_row['cc_no'];
		$cc_params['card_type'] = $billing_row['card_type'];
		$cc_params['ccexp'] = str_pad($billing_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($billing_row['expiry_year'], -2);
		$cc_params['cvv'] = $billing_row['cvv_no'];
	}

	$cc_params['description'] = !empty($extra['description'])?$extra['description']:"Order Refund for update benefit tier";
	$cc_params['firstname'] = $billing_row['fname'];
	$cc_params['lastname'] = $billing_row['lname'];
	$cc_params['address1'] = $billing_row['address'];
	$cc_params['city'] = $billing_row['city'];
	$cc_params['state'] = $billing_row['state'];
	$cc_params['zip'] = $billing_row['zip'];
	$cc_params['country'] = 'USA';
	$cc_params['phone'] = $customer_row['cell_phone'];
	$cc_params['email'] = $customer_row['email'];
	$cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
	$cc_params['processor'] = 'Authorize.net';
	$payment_processor = 'Authorize.net';

  	if($refund_amount == 0) {
        $payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
    } else {
	    if ($refund_type == "ACH") {
	      	$api = new CyberxPaymentAPI();
	      	$payment_res = $api->processRefundACH($cc_params,$payment_master_id);
	    } else {
			if($SITE_ENV !='Live') {
				$payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Manually Approved Order");
			} else {
				if ($cc_params['ccnumber'] == '4111111111111114') {
					$payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Manually Approved Order");
				} else {
					$api = new CyberxPaymentAPI();
					$payment_res = $api->processRefund($cc_params,$payment_master_id);
				}
			}
	    }    	
    }

	if ($payment_res['status'] == 'Success') {
		$refund_status = "Success";
		$is_refund = 'Y';
		$txn_id = $payment_res['transaction_id'];
		$payment_response = $payment_res;
	} else {
		$is_refund = 'N';
		$order_status="Void";
		$refund_status = "Failed";
		$cc_params['order_type'] = !empty($extra['description'])?$extra['description']:"Order Refund for update benefit tier";
		$cc_params['browser'] = $BROWSER;
		$cc_params['os'] = $OS;
		$cc_params['req_url'] = $REQ_URL;
		$cc_params['err_text'] = $payment_res['message'];
		$decline_log_id = $functionClass->credit_card_decline_log($order_row['customer_id'], $cc_params, $payment_res);
	}

  	if ($is_refund == 'Y') {
	    $return_order_data = array(
			'admin_id' => isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0',
			'order_id' => makeSafe($order_row['id']),
			'site_load' => makeSafe($order_row['site_load']),
			'return_type' => '',
			'refund_amount' => makeSafe($refund_amount),
			'inventory' => '',
			'order_comments' => !empty($extra['description'])?$extra['description']:"Order Refund for update policy",
			'refund' => makeSafe($is_refund),
			'refund_by' => makeSafe($refund_type),
			'refund_status' => makeSafe($refund_status),
			'auth_id' => makeSafe($txn_id),
			'payment_processor_res' => json_encode($payment_response),
			'auth_error' => '',
			'is_plan_cancel' => 'Y',
			'order_status' => $order_status,
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
			'created_at' => 'msqlfunc_NOW()',
			'updated_at' => 'msqlfunc_NOW()',
	    );

	    $return_order_id = $pdo->insert('return_orders', $return_order_data);
	  
	    if(($extra['is_partial_refund'] && $extra['is_partial_refund'] == 'N') || $upd_ord_status == true){
			$update_order_data = array(
				'status' => $order_status,
				'updated_at' => 'msqlfunc_NOW()',
			);

			$update_order_where = array("clause" => 'id=:id', 'params' => array(':id' => $order_row['id']));
			$pdo->update("orders", $update_order_data, $update_order_where);
	    }

	    $txn_id = $payment_res['transaction_id'];

	    if($order_status=="Void"){
	      	//************************ insert transaction code start ***********************
	        $other_params=array("debit_amount"=>$refund_amount,"transaction_id"=>$txn_id,'transaction_response'=>$payment_res,"reason" => "Order Void When Benefit Tier Update",'cc_decline_log_id'=>checkIsset($decline_log_id)); 
	        $transactionInsId=$functionClass->transaction_insert($order_row['id'],'Debit','Void Order','Transaction Void',0,$other_params);
	      	//************************ insert transaction code end ***********************
	    } else {
	      	//************************ insert transaction code start ***********************
	        $other_params=array("debit_amount"=>$refund_amount,"transaction_id"=>$txn_id,'transaction_response'=>$payment_res,"reason" => "Order Refund When Benefit Tier Update"); 
	        if(!empty($extra['is_partial_refund']) && $extra['is_partial_refund'] == 'Y'){
				$other_params['refunded_products'] = $extra['ws_row']['product_id'];
				$adminFeePrd = checkIsset($extra['adminFeePrd']);
				if(!empty($adminFeePrd)){
					$other_params['refunded_products'] .= ','.$adminFeePrd;
				}
	        }
	        $other_params['refund_id'] = $return_order_id;
	        $other_params['is_service_fee_refunded'] = 'N';
	        $transactionInsId=$functionClass->transaction_insert($order_row['id'],'Debit','Refund Order','Transaction Refund',0,$other_params);
	      	//************************ insert transaction code end ***********************
	    }
	    

	    $od_sql = "SELECT * FROM order_details WHERE order_id=:order_id AND is_deleted='N'";
	    $od_where = array(':order_id' => $order_row['id']);
	    $od_rows = $pdo->select($od_sql, $od_where);

	    if($extra['is_partial_refund'] && $extra['is_partial_refund'] == 'Y'){
	      	$temp_prd_id = $extra['ws_row']['product_id'];

			$adminFeePrd = checkIsset($extra['adminFeePrd']);
			if(!empty($adminFeePrd)){
			  $temp_prd_id .= ','.$adminFeePrd;
			  $od_rows = $pdo->select("SELECT od.* FROM order_details od JOIN orders o ON (od.order_id = o.id) WHERE o.id = :order_id AND od.product_id IN ($temp_prd_id) AND is_deleted='N' GROUP BY od.id",array(':order_id' => $order_row['id']));
			}else{
				$od_rows = $pdo->select("SELECT od.* FROM order_details od JOIN orders o ON (od.order_id = o.id) WHERE o.id = :order_id AND od.product_id = :product_id AND is_deleted='N' GROUP BY od.id",array(':order_id' => $order_row['id'],':product_id' => $temp_prd_id));
			}
	    }
	    
	    foreach ($od_rows as $key => $od_row) {
			$update_od_data = array(
				'is_refund' => "Y",
				'updated_at' => 'msqlfunc_NOW()',
			);
	      	$update_od_where = array("clause" => 'id=:id', 'params' => array(':id' => $od_row['id']));
	      	$pdo->update("order_details", $update_od_data, $update_od_where);

	      	$return_od_data = array(
		        'return_order_id' => $return_order_id,
		        'product_id' => $od_row['product_id'],
		        'product_type' => $od_row['product_type'],
		        'product_name' => $od_row['product_name'],
		        'unit_price' => $od_row['unit_price'],
		        'product_code' => $od_row['product_code'],
		        'qty' => $od_row['qty'],
		        'refund_amount' => $od_row['unit_price'],

	      	);
	      	$pdo->insert('return_order_details', $return_od_data);
	    }

	    //reversing commissions for this order
	    if ($reverse_commission == 'Yes') {
			$comm_extra_params = array();
			$comm_extra_params['note'] = ("Commission reversed on order cancel when ". (!empty($extra['description'])?$extra['description']:" update benefit tier"));
			$comm_extra_params['date'] = date("Y-m-d");
			$comm_extra_params['transaction_tbl_id'] = $transactionInsId['id'];

			if($extra['is_partial_refund'] && $extra['is_partial_refund'] == 'Y'){
				$order_detail_ids = array();
				foreach ($od_rows as $key => $od_row) {
					$order_detail_ids[] = $od_row['id'];
				}
				
				$comm_extra_params['plan_ids'] = array($od_row['plan_id']);
				$comm_extra_params['order_detail_id'] = $order_detail_ids;
			}
			$commObj->reverseOrderCommissions($order_row['id'],$comm_extra_params);  
	    }

	    //********* Payable Insert Code Start ********************
		$payable_params=array(
			'payable_type'=>'Reverse_Vendor',
			'type'=>'Vendor',
			'transaction_tbl_id' => $transactionInsId['id'],
		);
		if($extra['is_partial_refund'] && $extra['is_partial_refund'] == 'Y'){
			foreach ($od_rows as $key => $od_row) {
				$payable_params['order_detail_id'] = $od_row['id'];
				$payable = $functionClass->payable_insert($order_row['id'],$order_row['customer_id'],$od_row['product_id'],$od_row['plan_id'],$payable_params);	
			}
		} else {
			$payable = $functionClass->payable_insert($order_row['id'],0,0,0,$payable_params);
		}
		//********* Payable Insert Code End   ********************

	    /*----------------- List Bill Related Update ------------------*/
	    if ($order_row['type'] == 'List Bill') {
	      	$list_bill_where = array(":order_id" => $order_row['id']);

	      	if($extra['is_partial_refund'] && $extra['is_partial_refund'] == 'Y'){
				$tmp_incr = ' AND od.product_id = :product_id AND od.plan_id = :plan_id';
				$list_bill_where[':product_id'] = $extra['ws_row']['product_id'];
				$list_bill_where[':plan_id'] = $extra['ws_row']['plan_id'];
	      	}

	      	$list_bill_sql = "SELECT lb.list_bill_no,lb.received_amount as lb_received_amount,lb.due_amount as lb_due_amount,lbp.received_amount,lbp.other_charges,lbp.reference,lb.customer_id,od.list_bill_id,od.list_bill_payment_id
	                                    FROM list_bills lb
	                                    JOIN order_details od ON (od.list_bill_id = lb.id AND od.is_deleted='N')
	                                    JOIN list_bill_payments lbp ON (lbp.id = od.list_bill_payment_id)
	                                    WHERE od.order_id = :order_id $tmp_incr";
	      
	      	$list_bill_row = $pdo->selectOne($list_bill_sql, $list_bill_where);

	      	if (!empty($list_bill_row)) {
				/*------------ Make Entry In Account Summary ---------- */
				$transaction_amount = $list_bill_row['received_amount'] > $refund_amount ? $refund_amount : $list_bill_row['received_amount'];
				$account_summary_data = array(
					'customer_id' => $list_bill_row['customer_id'],
					'entity_id' => $list_bill_row['list_bill_payment_id'],
					'entity_type' => 'list_bill_payment',
					'transaction_date' => date('Y-m-d'),
					'transaction_type' => 'debit',
					'transaction_amount' => $transaction_amount,
					'transaction_action' => 'payment return',
					'transaction_name' => 'Payment Return',
					'transaction_desc' => ('Ref #' . $list_bill_row['reference'] . ' <br/>' . displayAmount($transaction_amount, 2) . ' for payment return of ' . $list_bill_row['list_bill_no']),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				);
				$pdo->insert('account_summary', $account_summary_data);
				/*------------/Make Entry In Account Summary ---------- */

	        	/*------------ Update List Bill Payment Status ---------------*/
		        $update_list_bill_payment_data = array();
		        $update_list_bill_payment_data['updated_at'] = "msqlfunc_NOW()";
		        if ($refund_amount >= $list_bill_row['received_amount']) {
	          		$update_list_bill_payment_data['status'] = 'Payment Return';
		        } else {
					$update_list_bill_payment_data['received_amount'] = $list_bill_row['received_amount'] - $refund_amount;
					$update_list_bill_payment_data['total_amount'] = $update_list_bill_payment_data['received_amount'] + $list_bill_row['other_charges'];
		        }
	        	$update_list_bill_payment_where = array("clause" => 'id=:id', 'params' => array(':id' => $list_bill_row['list_bill_payment_id']));
	        	$pdo->update("list_bill_payments", $update_list_bill_payment_data, $update_list_bill_payment_where);
	        	/*------------/Update List Bill Payment Status ---------------*/

	        	/*------------ Update List Bill Data ---------------*/
		        $update_list_bill_data = array(
					'received_amount' => $list_bill_row['lb_received_amount'] - $transaction_amount,
					'due_amount' => $list_bill_row['lb_due_amount'] + $transaction_amount,
					'updated_at' => 'msqlfunc_NOW()',
		        );
	        	$update_list_bill_where = array("clause" => 'id=:id', 'params' => array(':id' => $list_bill_row['list_bill_id']));
	        	$pdo->update("list_bills", $update_list_bill_data, $update_list_bill_where);
	        	/*------------/Update List Bill Data ---------------*/
	      	}
	    }
	    /*-----------------/List Bill Related Update ------------------*/
	    return array("status" => true, "message" => 'Order cancelled successfully');
  	} else {
		return array("status" => false, "error" => "refund_failed", "message" => $payment_res['message']);
  	}
}
function get_product_row($product_id){
    global $pdo;
    $pm_sql = "SELECT *,IF(payment_type='Recurring',payment_type_subscription,'One Time') as member_payment_type FROM prd_main WHERE id=:id";
    $pr_row = $pdo->selectOne($pm_sql, array(":id"=>$product_id));
    return $pr_row;
}
function get_termination_date_selection_options($ws_id) {
  global $pdo;

  require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';

  $enrollDate = new enrollmentDate();

  $options = array();
  $is_list_bill = false;
  $date = date("Y-m-d");
  $coverageOrder = array("1" => "P1","2" => "P2", "3" =>"P3","4"=>"P4","5"=>"P5","6"=>"P6","7"=>"P7","8"=>"P8","9"=>"P9","10" =>"P10","11" =>"P11","12" =>"P12","13" =>"P13","14" =>"P14","15" =>"P15");

  $ws_row = $pdo->selectOne("SELECT w.*,p.product_type as p_type,p.type as pm_type FROM website_subscriptions w JOIN prd_main p on(w.product_id = p.id)
  	WHERE w.id=:id",array(":id"=>$ws_id));
  $eligibility_date = $ws_row['eligibility_date'];
  $db_termination_date = $ws_row['termination_date'];
  $ws_end_coverage_date = $ws_row['end_coverage_period'];
  $ws_cus_id = $ws_row['customer_id'];
  $date = date("Y-m-d",strtotime($ws_end_coverage_date));

    //if sponser is grp so check billing type
	$check_sponser = $pdo->selectOne("SELECT cs.billing_type FROM customer s
	JOIN `customer_group_settings` cs ON (s.id = cs.customer_id)
	JOIN customer c ON (c.sponsor_id = s.id) WHERE s.type='Group' AND c.id = :cid",array(":cid"=>$ws_cus_id));
	if(!empty($check_sponser) && checkIsset($check_sponser['billing_type']) == 'list_bill'){
	$is_list_bill = true;
	}

  if($ws_row['p_type'] == 'Healthy Step'){
	  $termDates = get_available_term_date_for_healthy_step($ws_row['id']);
	  $db_termination_date = $termDates['end_termination_date'];
  }
  $last_coverage_period=false;
  //check last coverage period code start
    $sqlLastCoverageOrder = "SELECT count(id) totalCoverage,MAX(id) as order_id FROM orders where find_in_set(:ws_id,subscription_ids)";
    $resLastCoverageOrder = $pdo->selectOne($sqlLastCoverageOrder,array(":ws_id"=>$ws_id));

    if($resLastCoverageOrder && $resLastCoverageOrder['totalCoverage'] > 1){
      $orderDetailSql="SELECT start_coverage_period,end_coverage_period FROM order_details where order_id = :order_id AND is_deleted='N'";
      $orderDetailRes=$pdo->selectOne($orderDetailSql,array(":order_id"=>$resLastCoverageOrder['order_id']));

      if($orderDetailRes){
        $last_coverage_period=true;
        $start_of_last_coverage=$orderDetailRes['start_coverage_period'];
        $end_of_last_coverage=$orderDetailRes['end_coverage_period'];
      }
    }

  //check last coverage period code end
  /*---- If effective on future || Current coverage is initial coverage -------*/
  $member_payment_type_res = $pdo->selectOne("SELECT IF(payment_type='Recurring',payment_type_subscription,'One Time') as member_payment_type FROM prd_main where id = :id",array(":id" => $ws_row['product_id']));
  $member_payment_type = $member_payment_type_res['member_payment_type'];
  
  if($ws_row['pm_type'] == 'Fees'){
  	$allow_back_to_effective="Y";
  } else {
	$allow_back_to_effective=getname('prd_main',$ws_row['product_id'],'term_back_to_effective','id');
  }
  
  $product_dates = $enrollDate->getCoveragePeriod($eligibility_date,$member_payment_type);
  
  $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
  $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
  

    $tmp_eligibility_date = $eligibility_date;
    $product_dates=$enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

    $is_last_coverage_period = false;

    if(!empty($allow_back_to_effective) && $allow_back_to_effective == 'Y'){
	    $options[$startCoveragePeriod] = array(
	      "text" => "Back to effective date (".(date("m/d/Y",strtotime($eligibility_date))).")",
	      "value" => $eligibility_date,
	      'start_coverage_period' => $startCoveragePeriod,
	      'end_coverage_period' => $endCoveragePeriod,
	      'coverage_period_type' => 'effective'
	    );
	}
    $countCoverage = 1;

    while ($is_last_coverage_period == false) {
      $product_dates=$enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

      $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
      $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

      $tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));

      	if((!empty($db_termination_date) && strtotime($db_termination_date) > 0 && strtotime($endCoveragePeriod) < strtotime($db_termination_date))) {
      	} else {
			if(strtotime($date) < strtotime($startCoveragePeriod)) {
				$is_last_coverage_period = true;  
				$options[$endCoveragePeriod] = array(
					"text" => (!$is_list_bill) ? "End of ".$coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($endCoveragePeriod))).")" : "End of (".(date("m/d/Y",strtotime($endCoveragePeriod))).")",
					"value" => $endCoveragePeriod,
					'start_coverage_period' => $startCoveragePeriod,
					'end_coverage_period' => $endCoveragePeriod,
					'coverage_period_type' => 'next'
				);
				continue;
				exit;
			}
      	}
      
	  $text = (!$is_list_bill) ? "End of ".$coverageOrder[$countCoverage]. " (".(date("m/d/Y",strtotime($endCoveragePeriod))).")" : "End of (".(date("m/d/Y",strtotime($endCoveragePeriod))).")";
      $coverage_period_type = $coverageOrder[$countCoverage]. " coverage period";
      $termination_date = $endCoveragePeriod;
      $countCoverage++;
      
      $options[$endCoveragePeriod] = array(
        "text" => $text,
        "value" => $termination_date,
        'start_coverage_period' => $startCoveragePeriod,
        'end_coverage_period' => $endCoveragePeriod,
        'coverage_period_type' => $coverage_period_type,
      );
    }

    if(!empty($options)) {
      $tmp_options = array();
      foreach ($options as $key => $option) {
        $tmp_options[$option['value']] = $option;
      }
    }

  $tmp_options = array();
  foreach ($options as $key => $option) {
    $tmp_options[] = $option;
  }
  $options = $tmp_options;
  krsort($options);
  return $options;
}
function get_dependent_term_date_selection_options($dep_id,$ws_id) {
  global $pdo;

  require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';

  $enrollDate = new enrollmentDate();

  $options = array();
  $max_limit_date = date("Y-m-d");
  $coverageOrder = array("1" => "P1","2" => "P2", "3" =>"P3","4"=>"P4","5"=>"P5","6"=>"P6","7"=>"P7","8"=>"P8","9"=>"P9","10" =>"P10","11" =>"P11","12" =>"P12","13" =>"P13","14" =>"P14","15" =>"P15");

  $dep_row = $pdo->selectOne("SELECT * FROM customer_dependent WHERE id = :dep_id",array(':dep_id' => $dep_id));
  $dep_eligibility_date = $dep_row['eligibility_date'];

  $ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions w WHERE id=:id",array(":id"=>$ws_id));
  $eligibility_date = $ws_row['eligibility_date'];
  $eligibility_date = $dep_eligibility_date;
  $last_coverage_period=false;
  //check last coverage period code start
    $sqlLastCoverageOrder = "SELECT count(id) totalCoverage,MAX(id) as order_id FROM orders where find_in_set(:ws_id,subscription_ids)";
    $resLastCoverageOrder = $pdo->selectOne($sqlLastCoverageOrder,array(":ws_id"=>$ws_id));

    if($resLastCoverageOrder && $resLastCoverageOrder['totalCoverage'] > 1){
      $orderDetailSql="SELECT start_coverage_period,end_coverage_period FROM order_details where order_id = :order_id AND is_deleted='N'";
      $orderDetailRes=$pdo->selectOne($orderDetailSql,array(":order_id"=>$resLastCoverageOrder['order_id']));

      if($orderDetailRes){
        $last_coverage_period=true;
        $start_of_last_coverage=$orderDetailRes['start_coverage_period'];
        $end_of_last_coverage=$orderDetailRes['end_coverage_period'];
      }
    }

  //check last coverage period code end
  /*---- If effective on future || Current coverage is initial coverage -------*/
  $member_payment_type=getname('prd_main',$ws_row['product_id'],'payment_type_subscription','id');
  
  $product_dates = $enrollDate->getCoveragePeriod($eligibility_date,$member_payment_type);
  
  $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
  $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
  

    $tmp_eligibility_date = $eligibility_date;
    $product_dates=$enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

    $is_last_coverage_period = false;

    $options[$startCoveragePeriod] = array(
      "text" => "Back to effective date (".(date("m/d/Y",strtotime($eligibility_date))).")",
      "value" => $eligibility_date,
      'start_coverage_period' => $startCoveragePeriod,
      'end_coverage_period' => $endCoveragePeriod,
      'coverage_period_type' => 'effective'
    );
    $countCoverage = 1;

    while ($is_last_coverage_period == false) {
      $product_dates=$enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

      $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
      $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

      $tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));

      if(strtotime($max_limit_date) < strtotime($startCoveragePeriod)) {
        $is_last_coverage_period = true;  
        $options[$endCoveragePeriod] = array(
          "text" => "End of ".$coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($endCoveragePeriod))).")",
          "value" => $endCoveragePeriod,
          'start_coverage_period' => $startCoveragePeriod,
          'end_coverage_period' => $endCoveragePeriod,
          'coverage_period_type' => 'next'
        );
        continue;
        exit;
      }
      
      $text = "End of ".$coverageOrder[$countCoverage]. " (".(date("m/d/Y",strtotime($endCoveragePeriod))).")";
      $coverage_period_type = $coverageOrder[$countCoverage]. " coverage period";
      $termination_date = $endCoveragePeriod;
      $countCoverage++;
      
      $options[$endCoveragePeriod] = array(
        "text" => $text,
        "value" => $termination_date,
        'start_coverage_period' => $startCoveragePeriod,
        'end_coverage_period' => $endCoveragePeriod,
        'coverage_period_type' => $coverage_period_type,
      );
    }

  	krsort($options);
  	return $options;
}
function get_effective_date_selection_options($ws_id) {
  global $pdo;
  require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
  $enrollDate = new enrollmentDate();

  $options = array();
  $today_date = date("Y-m-d");
  $coverageOrder = array("1" => "P1","2" => "P2", "3" =>"P3","4"=>"P4","5"=>"P5","6"=>"P6","7"=>"P7","8"=>"P8","9"=>"P9","10" =>"P10","11" =>"P11","12" =>"P12","13" =>"P13","14" =>"P14","15" =>"P15");

  $ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions w WHERE id=:id",array(":id"=>$ws_id));
  $eligibility_date = $ws_row['eligibility_date'];

  	/*---- If effective on future || Current coverage is initial coverage -------*/
  	$member_payment_type = getname('prd_main',$ws_row['product_id'],'payment_type_subscription','id');

    $tmp_eligibility_date = $eligibility_date;
    $product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

    $is_last_coverage_period = false;
    $countCoverage = 1;

    $options[$startCoveragePeriod] = array(
      "text" =>  $coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($startCoveragePeriod))).")",
      "value" => $startCoveragePeriod,
      'start_coverage_period' => $startCoveragePeriod,
      'end_coverage_period' => $endCoveragePeriod,
      'coverage_period_type' => 'effective'
    );
    

    while ($is_last_coverage_period == false) {
		$product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

		$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
		$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

		$tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));

		if(strtotime(date("Y-m-d",strtotime('+2 months'))) < strtotime($startCoveragePeriod)) {
			$is_last_coverage_period = true;
			$options[$startCoveragePeriod] = array(
				"text" => $coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($startCoveragePeriod))).")",
				"value" => $startCoveragePeriod,
				'start_coverage_period' => $startCoveragePeriod,
				'end_coverage_period' => $endCoveragePeriod,
				'coverage_period_type' => 'next'
			);
			continue;
		} else {
			if(strtotime($startCoveragePeriod) != strtotime($eligibility_date)) {
				$options[$startCoveragePeriod] = array(
					"text" => $coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($startCoveragePeriod))).")",
					"value" => $startCoveragePeriod,
					'start_coverage_period' => $startCoveragePeriod,
					'end_coverage_period' => $endCoveragePeriod,
					'coverage_period_type' => 'next'
				);
			}
			$countCoverage++;
		}		
    }

  	ksort($options);
  	return $options;
}
function get_tier_change_date_selection_options($ws_id) {
  	global $pdo;
  	require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
  	$enrollDate = new enrollmentDate();

  	$options = array();
	$is_list_bill = false;
  	
  	$coverageOrder = array("1" => "P1","2" => "P2", "3" =>"P3","4"=>"P4","5"=>"P5","6"=>"P6","7"=>"P7","8"=>"P8","9"=>"P9","10" =>"P10","11" =>"P11","12" =>"P12","13" =>"P13","14" =>"P14","15" =>"P15");

  	$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions w WHERE id=:id",array(":id"=>$ws_id));
	$eligibility_date = $ws_row['eligibility_date'];
	$ws_end_coverage_date = $ws_row['end_coverage_period'];
	$ws_cus_id = $ws_row['customer_id'];

	$check_sponser = $pdo->selectOne("SELECT cs.billing_type FROM customer s
	    JOIN `customer_group_settings` cs ON (s.id = cs.customer_id)
	    JOIN customer c ON (c.sponsor_id = s.id) WHERE s.type='Group' AND c.id = :cid",array(":cid"=>$ws_cus_id));
    if(!empty($check_sponser) && checkIsset($check_sponser['billing_type']) == 'list_bill'){
		$is_list_bill = true;
	}	
	

	$today_date = date('Y-m-d');
	  
	$date1 = $eligibility_date;
	$date2 = $ws_end_coverage_date;
	$d1=new DateTime($date2); 
	$d2=new DateTime($date1);                                  
	$Months = $d2->diff($d1); 
	$totalMonths = (($Months->y) * 12) + ($Months->m);
	$totalMonths = $totalMonths+1;

  	/*---- If effective on future || Current coverage is initial coverage -------*/
  	$member_payment_type = getname('prd_main',$ws_row['product_id'],'payment_type_subscription','id');

    $tmp_eligibility_date = $eligibility_date;
    $product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

    $is_last_coverage_period = false;
    $countCoverage = 1;

    $options[$startCoveragePeriod] = array(
		"text" =>  (!$is_list_bill) ? $coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($startCoveragePeriod))).")" : date("m/d/Y",strtotime($startCoveragePeriod)),
		"value" => $startCoveragePeriod,
		'start_coverage_period' => $startCoveragePeriod,
		'end_coverage_period' => $endCoveragePeriod,
		'coverage_period_type' => 'effective'
    );
    

    while ($is_last_coverage_period == false) {
		$product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

		$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
		$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

		$tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));

		if(strtotime(date("Y-m-d",strtotime('+'.$totalMonths.' months'))) < strtotime($startCoveragePeriod)) {
			$is_last_coverage_period = true;
			$options[$startCoveragePeriod] = array(
				"text" => (!$is_list_bill) ? $coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($startCoveragePeriod))).")" : date("m/d/Y",strtotime($startCoveragePeriod)),
				"value" => $startCoveragePeriod,
				'start_coverage_period' => $startCoveragePeriod,
				'end_coverage_period' => $endCoveragePeriod,
				'coverage_period_type' => 'next'
			);
			continue;
		} else {
			if(strtotime($startCoveragePeriod) != strtotime($eligibility_date)) {
				$options[$startCoveragePeriod] = array(
					"text" => (!$is_list_bill) ? $coverageOrder[$countCoverage]." (".(date("m/d/Y",strtotime($startCoveragePeriod))).")" : date("m/d/Y",strtotime($startCoveragePeriod)),
					"value" => $startCoveragePeriod,
					'start_coverage_period' => $startCoveragePeriod,
					'end_coverage_period' => $endCoveragePeriod,
					'coverage_period_type' => 'next'
				);
			}
			$countCoverage++;
		}	
    }
  	krsort($options);
  	if(count($options) > 2) {
  		$tmp_options = array();
  		foreach ($options as $key => $value) {
			$tmp_options[$key] = $value;
			if(count($tmp_options) == 3) {
				//break;
			}
  		}
  		$options = $tmp_options;
  	}
  	ksort($options);
  	return $options;
}

function get_terminated_subscriptions($customer_id,$ws_id='')
{
  	global $pdo;
  	
	$incr = '';
  	$sch_params = array(":customer_id" => $customer_id);
  	if(!empty($ws_id)){
  		$incr = ' AND ws.id = :website_id';
  		$sch_params[':website_id'] = $ws_id;
  	}

	$terminated_subscriptions = array();
	$ws_sql = "SELECT ws.*,pm.name as product_name,ce.id as ce_id
			    FROM website_subscriptions ws
			    JOIN customer_enrollment ce on(ws.id = ce.website_id) 
			    JOIN prd_main pm ON(pm.id = ws.product_id AND pm.product_type NOT IN('Healthy Step','ServiceFee')) 
			    WHERE 
			    ws.status IN('Inactive','Active') AND (ws.termination_date is NOT NULL OR ws.termination_date != '') AND
			    ws.customer_id=:customer_id $incr";
	$ws_where = $sch_params;
	$ws_res = $pdo->select($ws_sql,$ws_where);

  	if(!empty($ws_res)) {
	    foreach ($ws_res as $key => $ws_row) {
	    	$new_ws_sql = "SELECT ws.id FROM customer_enrollment ce 
                JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                JOIN prd_main pm ON(pm.id = ws.product_id)
                WHERE (ws.termination_date IS NULL OR ws.termination_date!=ws.eligibility_date) AND ce.parent_coverage_id=:ce_id AND ce.process_status IN('Pending','Active')";
			$new_ws_row = $pdo->selectOne($new_ws_sql, array(":ce_id" => $ws_row['ce_id']));
			if(!empty($new_ws_row)) {
				continue;
			}

	      	$terminated_subscriptions[] = array(
		        'ws_id' => $ws_row['id'],
		        'website_id' => $ws_row['website_id'],
		        'product_id' => $ws_row['product_id'],
		        'product_name' => $ws_row['product_name'],
		        'product_code' => $ws_row['product_code'],
		        'plan_id' => $ws_row['plan_id'],
	      	);
	    }
  	}
  	return $terminated_subscriptions;
}
function get_terminated_subscriptions_for_cobra($customer_id,$is_reinstate = 'N')
{
  	global $pdo;
  	
	$terminated_subscriptions = array();
	
	$incr = "";
	if($is_reinstate == 'Y'){
		$incr = " AND ws.is_cobra_coverage = 'Y'";
	}
	$ws_sql = "SELECT ws.*,pm.name as product_name
			    FROM website_subscriptions ws
			    JOIN customer_enrollment ce on(ws.id = ce.website_id) 
			    JOIN prd_main pm ON(pm.id = ws.product_id AND pm.product_type NOT IN('Healthy Step','ServiceFee')) 
			    WHERE ws.status IN('Pending','Active','Inactive') AND (ws.termination_reason NOT IN('Benefit Tier Change','Policy Change','Benefit Amount Change','Cancelled Benefit Tier Change','Cancelled Policy Change','Cancelled Benefit Amount Change') OR ws.termination_reason IS NULL) AND ws.customer_id=:customer_id $incr";
	$ws_where = array(":customer_id" => $customer_id);
	$ws_res = $pdo->select($ws_sql,$ws_where);

  	if(!empty($ws_res)) {
	    foreach ($ws_res as $key => $ws_row) {      
	      	$terminated_subscriptions[] = array(
		        'ws_id' => $ws_row['id'],
		        'website_id' => $ws_row['website_id'],
		        'product_id' => $ws_row['product_id'],
		        'product_name' => $ws_row['product_name'],
		        'product_code' => $ws_row['product_code'],
		        'plan_id' => $ws_row['plan_id'],
	      	);
	    }
  	}
  	return $terminated_subscriptions;
}
function get_customer_enrollment_fee($customer_id) {
  global $pdo;
  $enroll_fee_prd_ids = get_enrollment_fee_prd_ids('string');
  if($enroll_fee_prd_ids){
    $fee_row = $pdo->selectOne("SELECT od.* FROM order_details od JOIN orders o ON(o.id = od.order_id) WHERE o.is_renewal='N' AND o.customer_id=:customer_id AND od.product_id IN(".$enroll_fee_prd_ids.") AND od.is_deleted='N' ORDER BY o.id ASC",array(":customer_id"=>$customer_id));
    return $fee_row;
  }
  return array();
}
function get_customer_billing_date($customer_id = 0,$ws_id=0) {
  global $pdo;
  $billing_date = '';
  $incr = " AND customer_id = :customer_id";
  $sch[':customer_id'] = $customer_id;

  if($ws_id){
    $incr .= " AND id = :id";
    $sch[":id"] = $ws_id;
  }
  if(!empty($customer_id)) {
    $ws_row = $pdo->selectOne("SELECT next_purchase_date FROM website_subscriptions WHERE status IN('Active','Pending', 'Post Payment') $incr ORDER BY next_purchase_date ASC",$sch);
    if(!empty($ws_row['next_purchase_date'])) {
      $billing_date = $ws_row['next_purchase_date'];    
    }
  }
  return $billing_date;
}
function get_coverage_periods_for_reinstate($customer_id,$ws_ids) {
    global $pdo;
    include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
    $enrollDate = new enrollmentDate();
    $today = date("Y-m-d");
    
    $coverage_periods = array();
    $paid_coverage_periods = array();
    foreach ($ws_ids as $key => $ws_id) {
        $ws_sql = "SELECT ws.*,p.name as product_name ,ppt.title as prd_plan_type_title
                    FROM website_subscriptions ws 
                    JOIN prd_main p ON(p.id = ws.product_id) 
                    LEFT JOIN prd_plan_type ppt ON(ppt.id = ws.prd_plan_type_id) 
                    WHERE ws.id=:id";
        $ws_row = $pdo->selectOne($ws_sql,array(":id"=>$ws_id));

        $billing_date = get_customer_billing_date($customer_id,$ws_id);

        if(strtotime($ws_row['eligibility_date']) == strtotime($ws_row['termination_date'])) {
            $coverage_start_from = $ws_row['eligibility_date'];
        } else {
            $coverage_start_from = date("Y-m-d",strtotime("+1 day",strtotime($ws_row['termination_date'])));
        }

      	// check all coverage periods from effecive date
        $coverage_start_from = $ws_row['eligibility_date'];
    	$subscription_coverage_periods = subscription_coverage_periods($ws_id);
    	$coverage_count = 1;
    	$paid_coverage_count = 1;
        foreach ($subscription_coverage_periods as $key2 => $scp) {
			if(strtotime($scp['start_coverage_period']) < strtotime($coverage_start_from)) {
				continue;
			}

          	$coverage_billing_date = coverage_billing_date($scp['start_coverage_period'],$billing_date);

          	//If future coverage
          	if(strtotime($today) < strtotime($scp['start_coverage_period'])) {
            	//Check this future coverage is not initial coverage
            	if(strtotime($ws_row['eligibility_date']) != strtotime($scp['start_coverage_period'])) {
              		//Billing date is not passout date('d',strtotime($billing_date))
              		if(strtotime(date('Y-m-d')) < strtotime($coverage_billing_date)) {
                		continue;
              		}
            	}
          	}

			$ws_row['next_purchase_date'] = coverage_billing_date(date("Y-m-d",strtotime("+1 day",strtotime($scp['end_coverage_period']))),$billing_date);
			$ws_row['start_coverage_period'] = $scp['start_coverage_period'];
			$ws_row['end_coverage_period'] = $scp['end_coverage_period'];
			$ws_row['renew_count'] = $scp['renew_count'];

			$ws_payment_status = subscriotion_has_approved_payment_this_coverage($ws_row['id'],$scp['start_coverage_period']);
			$ws_row['is_approved_payment'] = $ws_payment_status['success'];

			if($ws_payment_status['success'] == true) {
				$ws_row['order_id'] = $ws_payment_status['order_id'];
				$ws_row['transaction_id'] = $ws_payment_status['transaction_id'];
				$ws_row['payment_type'] = $ws_payment_status['payment_type'];
				$ws_row['is_post_date_order'] = $ws_payment_status['is_post_date_order'];

				if(!empty($paid_coverage_periods[$paid_coverage_count])) {
				  	$paid_coverage_periods[$paid_coverage_count]['ws_res'][] = $ws_row;

				  	if($scp['renew_count'] < $paid_coverage_periods[$paid_coverage_count]['renew_count']) {
				  		$paid_coverage_periods[$paid_coverage_count]['renew_count'] = $scp['renew_count'];
				  	}
				  	
				  	if(strtotime($scp['start_coverage_period']) < strtotime($paid_coverage_periods[$paid_coverage_count]['start_coverage_period'])) {
				  		$paid_coverage_periods[$paid_coverage_count]['start_coverage_period'] = $scp['start_coverage_period'];
				  		$paid_coverage_periods[$paid_coverage_count]['end_coverage_period'] = $scp['end_coverage_period'];
				  		$paid_coverage_periods[$paid_coverage_count]['coverage_billing_date'] = $coverage_billing_date;
				  	}
				} else {
					$paid_coverage_periods[$paid_coverage_count] = array(
						'start_coverage_period' => $scp['start_coverage_period'],
						'end_coverage_period' => $scp['end_coverage_period'],
						'coverage_billing_date' => $coverage_billing_date,
						'renew_count' => $scp['renew_count'],
						'ws_res' => array($ws_row),
					);
				}
				$paid_coverage_count++;
				continue;
			}

			if(!empty($coverage_periods[$coverage_count])) {
			  	$coverage_periods[$coverage_count]['ws_res'][] = $ws_row;

			  	if($scp['renew_count'] < $coverage_periods[$coverage_count]['renew_count']) {
			  		$coverage_periods[$coverage_count]['renew_count'] = $scp['renew_count'];
			  	}
			  	
			  	if(strtotime($scp['start_coverage_period']) < strtotime($coverage_periods[$coverage_count]['start_coverage_period'])) {
			  		$coverage_periods[$coverage_count]['start_coverage_period'] = $scp['start_coverage_period'];
			  		$coverage_periods[$coverage_count]['end_coverage_period'] = $scp['end_coverage_period'];
			  		$coverage_periods[$coverage_count]['coverage_billing_date'] = $coverage_billing_date;
			  	}
			} else {
				$coverage_periods[$coverage_count] = array(
					'start_coverage_period' => $scp['start_coverage_period'],
					'end_coverage_period' => $scp['end_coverage_period'],
					'coverage_billing_date' => $coverage_billing_date,
					'renew_count' => $scp['renew_count'],
					'ws_res' => array($ws_row),
				);
			}
			$coverage_count++;
        }
    }

    ksort($coverage_periods);
    ksort($paid_coverage_periods);

    foreach ($coverage_periods as $covKey => $coverage_period_row) {
    	$end_coverage_period_arr = array();
    	$effective_date_arr = array();
        foreach ($coverage_period_row['ws_res'] as $tmp_ws_row) {
            $end_coverage_period_arr[] = date('Y-m-d',strtotime('-1 days',strtotime($tmp_ws_row['start_coverage_period'])));
            $effective_date_arr[] = $tmp_ws_row['eligibility_date'];
        }
        $lowest_effective_date = $enrollDate->getLowestCoverageDate($effective_date_arr);
        if(strtotime($lowest_effective_date) <= strtotime($today) || $covKey > 1) {
	        $lowest_coverage_date = $enrollDate->getLowestCoverageDate($end_coverage_period_arr);
	      	$next_billing_date = date('Y-m-d',strtotime($lowest_coverage_date .'-4 day'));
	      	$coverage_periods[$covKey]['coverage_billing_date'] = $next_billing_date;

	      	if(strtotime($today) < strtotime($next_billing_date)) {
				unset($coverage_periods[$covKey]);
	      	}
        }
    }

    if(count($coverage_periods) == 0) {
    	$coverage_periods = $paid_coverage_periods;
    }

    foreach ($coverage_periods as $key => $coverage_period_row) {
		$is_approved_payment = true;
        foreach ($coverage_period_row['ws_res'] as $tmp_ws_row) {
            if($tmp_ws_row['is_approved_payment'] == false) {
                $is_approved_payment = false;
                break;
            }
        }
        $coverage_periods[$key]['is_approved_payment'] = $is_approved_payment;
    }
    return $coverage_periods;
}

function coverage_billing_date($start_coverage_period,$billing_date) {
	$start_coverage_period = date("Y-m-d",strtotime($start_coverage_period));
	$billing_date = date("Y-m-d",strtotime($billing_date));
	$tmp_billing_date = date("Y-m-d",strtotime('-1 days',strtotime($start_coverage_period)));
	while(date("d",strtotime($tmp_billing_date)) != date("d",strtotime($billing_date))) {
		$tmp_billing_date = date("Y-m-d",strtotime("-1 days",strtotime($tmp_billing_date)));
	}
	return $tmp_billing_date;
}
function get_subscription_renew_count($ws_id,$start_coverage_period) {
	$renew_count = 1;
	$start_coverage_period = date("Y-m-d",strtotime($start_coverage_period));
	$coverage_periods = subscription_coverage_periods($ws_id);
	foreach ($coverage_periods as $key => $cp) {
		if(strtotime($start_coverage_period) == strtotime($cp['start_coverage_period'])) {
		  	$renew_count = $cp['renew_count'];
		  	break;
		}
	}
  	return $renew_count;
}
function subscriotion_has_approved_payment_this_coverage($ws_id,$start_coverage_period) {
  global $pdo;

  	$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE id=:id",array(":id" => $ws_id));
	$incr= ($ws_row['payment_type']=='list_bill')?" JOIN list_bill_details lbd ON ( od.list_bill_detail_id=lbd.id AND lbd.is_reverse='N' AND lbd.transaction_type='charged') ":"";

  	$ord_sql = "SELECT o.id,o.transaction_id,o.payment_type,o.status FROM orders o
        JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
		$incr
        WHERE 
        (
			o.status IN('Payment Approved') OR 
			(o.status IN('Pending Payment','Post Payment') AND o.future_payment='Y')
		) AND 
        od.is_refund='N' AND  od.is_chargeback='N' AND od.is_payment_return='N' AND 
        od.website_id=:website_id AND
        od.start_coverage_period=:start_coverage_period";

	$ord_where = array(
		":website_id" => $ws_row['id'],
		":start_coverage_period" => $start_coverage_period,
	);
  	$ord_row = $pdo->selectOne($ord_sql,$ord_where);
	if(!empty($ord_row['id'])) {
		$res = array(
			'success' => true,
			'order_id' => $ord_row['id'],
			'transaction_id' => $ord_row['transaction_id'],
			'payment_type' => $ord_row['payment_type'],
			'is_post_date_order' => false,
		);
		if(in_array($ord_row['status'],array('Pending Payment','Post Payment'))) {
			$res['is_post_date_order'] = true;
		}
		return $res;
	} else {
		return array(
			'success' => false,
		);
	}
}
function get_billing_label_by_billing_profile($id,$cb_row = array()) {
  global $pdo,$CREDIT_CARD_ENC_KEY;
  if(empty($cb_row)) {
    $cb_sql = "SELECT *,
                AES_DECRYPT(card_no_full,'".$CREDIT_CARD_ENC_KEY."')as cc_no,
                AES_DECRYPT(ach_account_number,'".$CREDIT_CARD_ENC_KEY."')as ach_account_number,
                AES_DECRYPT(ach_routing_number,'".$CREDIT_CARD_ENC_KEY."')as ach_routing_number 
            FROM customer_billing_profile WHERE id=:id";
    $cb_where = array(":id" => $id);
    $cb_row = $pdo->selectOne($cb_sql, $cb_where);
  }

  $billing_label = '';

  if($cb_row['payment_mode'] == "ACH"){
    $billing_label = "ACH *".(substr($cb_row['ach_account_number'],-4));
  } else {
    if ($cb_row['card_type'] == 'Visa') {
          $card_type = 'VISA';
        
        } elseif ($cb_row['card_type'] == 'MasterCard') {
          $card_type = 'MC';
        
        } elseif ($cb_row['card_type'] == 'Discover') {
          $card_type = 'DISC';
        
        } elseif ($cb_row['card_type'] == 'American Express') {
          $card_type = 'AMEX';
        
        } else {
          $card_type = $cb_row['card_type'];
        }

        $billing_label = $card_type." *".$cb_row['card_no'];
  }
  return $billing_label;
}
function check_is_current_plan($ce_id) {
	global $pdo;
	$customer_enrollment_sql = "SELECT ce.new_plan_id,ce.parent_coverage_id,ws.status as plan_status FROM customer_enrollment ce JOIN website_subscriptions ws on(ws.id = ce.website_id) WHERE ce.id=:id";
	$ce_row = $pdo->selectOne($customer_enrollment_sql, array(":id" => $ce_id));
	if ($ce_row['plan_status'] == "Active") {
		return true;
	} elseif ($ce_row['plan_status'] == "Cancelled Update") {
		return false;
	} else {
		if ($ce_row['plan_status'] == "Pending") {
			$check_has_parent_plan_sql = "SELECT ce.id FROM customer_enrollment ce JOIN website_subscriptions w on(ce.website_id = w.id) WHERE w.status NOT IN('Cancelled Update') AND ce.id=:id";
			$check_has_parent_plan_row = $pdo->selectOne($check_has_parent_plan_sql, array(":id" => $ce_row['parent_coverage_id']));
			if (empty($check_has_parent_plan_row)) {
				return true;
			} else {
				return false;
			}
		} else {
			//Terminated // Check has child plan
			$check_has_sub_plan_sql = "SELECT ce.id FROM customer_enrollment ce JOIN website_subscriptions w on(w.id = ce.website_id) WHERE w.status NOT IN('Cancelled Update') AND w.plan_id=:plan_id AND ce.parent_coverage_id=:parent_coverage_id";
			$check_has_sub_plan_row = $pdo->selectOne($check_has_sub_plan_sql, array(":parent_coverage_id" => $ce_id, ":plan_id" => $ce_row['new_plan_id']));

			if (empty($check_has_sub_plan_row)) {
				return true;
			} else {
				return false;
			}
		}
	}
}
function getProductBillingDates($effective_date = "",$billing_date = "",$last_coverage_date="") {
	if ($effective_date == "") {
		$effective_date = date("Y-m-d");
	}

	if ($billing_date == "") {
		$billing_date = date("Y-m-d");
	}
	
	$effective_date = date("Y-m-d",strtotime($effective_date));

	$billing_date = date("Y-m-d",strtotime($billing_date));


	if($last_coverage_date!=""){
		$last_coverage_date = date("Y-m-d",strtotime($last_coverage_date));
		$date = new DateTime($last_coverage_date);
		$nextBillingDate = date("Y-m-", strtotime($date->format('Y-m-d'))) . date('d',strtotime($billing_date));
		if(strtotime($nextBillingDate)>strtotime($last_coverage_date)){
			$nextBillingDate=date("Y-m-d",strtotime("-1 Month",strtotime($nextBillingDate)));
		}
	}else{
		$date = new DateTime($billing_date);
		$nextBillingDate = $date->format('Y-m-d');
	}
	while((strtotime($nextBillingDate) < strtotime($effective_date))) {
		$date = new DateTime($nextBillingDate);
		$nextBillingDate = addMonth($date,'1');
	}

	while((strtotime($nextBillingDate) <= strtotime(date("Y-m-d")))) {
		$date = new DateTime($nextBillingDate);
		$nextBillingDate = addMonth($date,'1');
	}
	//$arr["nextBillingDate"] = $nextBillingDate;
	$arr["nextBillingDate"] = date("Y-m-d",strtotime("-1 days",strtotime($nextBillingDate)));
	$arr["eligibilityDate"] = $effective_date;
	return (object) $arr;
}
function get_product_effective_detail($product_id,$today = '',$is_from_group_portal = false)
{
	global $pdo;
	$tmp_company_ids = array();

	if($today == '' || strtotime($today) == 0) {
		$today = date('Y-m-d');
	}

	if($is_from_group_portal !== true && $is_from_group_portal !== false) {
		if(is_group_member($is_from_group_portal) == true) {
			$is_from_group_portal = true;
		} else {
			$is_from_group_portal = false;
		}
	}

	$parent_product_id = getname('prd_main', $product_id, 'parent_product_id', 'id');
	if($parent_product_id > 0) {
		$product_id = $parent_product_id;
	}
	
	$prd_row = $pdo->selectOne("SELECT product_code,direct_product,effective_day,sold_day FROM prd_main WHERE id=:id", array(":id" => $product_id));

	$effective_date = $prd_row['direct_product'];

	if($effective_date == 'Select Day Of Month'){
		$effective_from = $prd_row['effective_day'];
		if($effective_from == 'LastDayOfMonth'){
			$effective_from = date('t');
		}
		$calender_type = 'daily';
		$default_effective_from = new DateTime(date('Y-m-'.$effective_from));
		$default_effective_from = $default_effective_from->format('Y-m-d');
	}else if($effective_date == 'First Of Month'){
		$effective_from = 'next_month';
		$calender_type = 'monthly';
		$default_effective_from = new DateTime(date('Y-m-d'));
		$default_effective_from->modify('first day of next month');
		if(15 < date('d',strtotime($today))) {
			$effective_from = 'next_to_next_month';
			$default_effective_from->modify('first day of next month');
		}
		$default_effective_from = $default_effective_from->format('Y-m-d');
	}else{
		$effective_from = 'next_day';
		$calender_type = 'daily';
		$default_effective_from = new DateTime(date('Y-m-d'));
		$default_effective_from->modify('+1 day');
		$default_effective_from = $default_effective_from->format('Y-m-d');
	}

	if($is_from_group_portal == true) {
		$effective_from = 'next_month';
		$calender_type = 'monthly';
		$default_effective_from = new DateTime(date('Y-m-d'));
		$default_effective_from->modify('first day of next month');
		$default_effective_from = $default_effective_from->format('Y-m-d');
	}
	
	return (object) array('default_effective_from'=>$default_effective_from,'effective_from'=>$effective_from,'calender_type'=>$calender_type);
}
function get_end_coverage_period($order_id, $plan_id){
	global $pdo;

	$order_details_res = $pdo->selectOne("SELECT end_coverage_period FROM order_details WHERE order_id = :order_id AND plan_id = :plan_id AND is_deleted='N'", array(":order_id" => $order_id, ":plan_id" => $plan_id));
	
	if($order_details_res){
		return $order_details_res['end_coverage_period'];
	} else {
		return date('Y-m-d');
	}
}
function split_name($name) {
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
    return array($first_name, $last_name);
}
function check_is_card_exist($product_id){
	global $pdo;

	$id_card_sql = "SELECT sr.description
				FROM sub_resources sr 
				JOIN resources r ON(sr.res_id = r.id)
				JOIN res_products rp ON(rp.res_id=r.id)
				JOIN prd_main pm ON(pm.id=rp.product_id OR pm.parent_product_id=rp.product_id)
				WHERE 
				sr.is_deleted='N' AND
				r.is_deleted='N' AND 
				r.status='Active' AND 
				r.user_group='Member' AND
				pm.id=:product_id AND 
				r.effective_date <= :today_date AND 
				(r.termination_date = '0000-00-00' OR r.termination_date IS NULL OR r.termination_date >= :today_date) AND 
				r.type = 'id_card'
				GROUP BY sr.id
				ORDER BY sr.group_id";
	$id_card_where = array(
						":product_id" => $product_id,
						":today_date" => date('Y-m-d')
					);
	$id_card_row = $pdo->selectOne($id_card_sql,$id_card_where);

	if($id_card_row){
		return true;
	}
	return false;

}
function get_display_url($url = ""){
	if(empty($url)) {
		return "";
	}
	$bits = parse_url($url);
	$newHost = substr($bits["host"],0,4) !== "www."?"www.".$bits["host"]:$bits["host"];
	$url2 = $newHost.(isset($bits["port"])?":".$bits["port"]:"").$bits["path"].(!empty($bits["query"])?"?".$bits["query"]:"");;
	return $url2;
}
function getProductCoverageDates($effective_date = "",$member_payment_type="") {
	if ($effective_date == "") {
		$effective_date = date("Y-m-d");
	}

	$effective_date = date("Y-m-d",strtotime($effective_date));

	$date = new DateTime($effective_date);
	$startCoveragePeriod = $date->format('Y-m-d');
	if(strtotime($startCoveragePeriod) == strtotime($date->format('Y-m-01'))) {
		//$endCoveragePeriod =$date->format('Y-m-t');
		if($member_payment_type!=""){
			if($member_payment_type=='Monthly'){
				$endCoveragePeriod =$date->format('Y-m-t');
			}elseif($member_payment_type=='90 Days'){
				$endCoveragePeriod = addMonth($date,'3');
			}elseif($member_payment_type=='Annually'){
				$endCoveragePeriod = addMonth($date,'12');
			}else{
				$endCoveragePeriod =$date->format('Y-m-t');
			}
		}else{
			$endCoveragePeriod =$date->format('Y-m-t');
		}
	} else {
		//$endCoveragePeriod = addMonth($date->modify('-1 day'),'1');
		if($member_payment_type!=""){
			if($member_payment_type=='Monthly'){
				$endCoveragePeriod = addMonth($date->modify('-1 day'),'1');
			}elseif($member_payment_type=='90 Days'){
				$endCoveragePeriod = addMonth($date->modify('-1 day'),'3');
			}elseif($member_payment_type=='Annually'){
				$endCoveragePeriod = addMonth($date->modify('-1 day'),'12');
			}else{
				$endCoveragePeriod = addMonth($date->modify('-1 day'),'1');
			}
		}else{
			$endCoveragePeriod = addMonth($date->modify('-1 day'),'1');
		}
	}
	$arr["startCoveragePeriod"] = $startCoveragePeriod;
	$arr["endCoveragePeriod"] = $endCoveragePeriod;
	return (object) $arr;
}
function get_enrollee_detail($customer_id,$product_id = 0,$dep_profile_ids = array(),$other_params = array())
{
	global $pdo;
	$enrollee = array();
	$child_data = $spouse_data = $primary_data = array();

	$cust_sql = "SELECT cs.*,c.fname,c.gender,c.birth_date,c.zip
				FROM customer_settings cs
				JOIN customer c ON(c.id = cs.customer_id)
				WHERE cs.customer_id=:customer_id";
	$cust_row = $pdo->selectOne($cust_sql,array(":customer_id"=>$customer_id));
	if(!empty($cust_row)) {
		$height = '';
		if(!empty($cust_row['height_feet'])) {
			$height = $cust_row['height_feet'];
		}
		if(!empty($cust_row['height_inch'])) {
			if(!empty($height)) {
				$height .= ".".$cust_row['height_inch'];
			} else {
				$height = "0.".$cust_row['height_inch'];
			}
		}
		$benefit_amount = '';

		if(isset($other_params['primary_benefit_amount'])) {
			$benefit_amount = $other_params['primary_benefit_amount'];
		} else {
			if(!empty($product_id)) {
				$ba_sql ="SELECT amount 
							FROM customer_benefit_amount 
							WHERE 
							is_deleted='N' AND 
							customer_id=:customer_id AND 
							product_id=:product_id AND 
							type='Primary' ORDER BY id DESC";
				$ba_where = array(":customer_id"=>$customer_id,":product_id"=>$product_id);
				$ba_row = $pdo->selectOne($ba_sql,$ba_where);
			    if(!empty($ba_row) && $ba_row['amount'] > 0) {
			    	$benefit_amount = $ba_row['amount'];
			    }
			}
		}
 		$primary_data[1] = array(
			"fname" => $cust_row['fname'],
			"gender" => $cust_row['gender'],
			"birthdate" => $cust_row['birth_date'],
			"zip" => $cust_row['zip'],
			"smoking_status" => $cust_row['smoke_use'],
			"tobacco_status" => $cust_row['tobacco_use'],
			"height" => $height,
			"weight" => $cust_row['weight'],
			"no_of_children" => $cust_row['no_of_children'],
			"has_spouse" => $cust_row['has_spouse'],
			"benefit_amount" => $benefit_amount,
			"benefit_level" => $cust_row['benefit_level'],
			"employmentStatus" => $cust_row['employmentStatus'],
			"salary" => $cust_row['salary'],
			"hire_date" => $cust_row['hire_date'],
			"hours_per_week" => $cust_row['hours_per_week'],
			"pay_frequency" => $cust_row['pay_frequency'],
			"us_citizen" => $cust_row['us_citizen'],
		);
	}

	if(!empty($dep_profile_ids)) {
		$cd_sql = "SELECT cdp.* FROM customer_dependent_profile cdp WHERE id IN(".implode(',',$dep_profile_ids).")";
		$cd_res = $pdo->select($cd_sql);
		if(!empty($cd_res)) {
			$child_cnt = 1;
			foreach ($cd_res as $key => $cd_row) {
				$height = '';
				if(!empty($cd_row['height_feet'])) {
					$height = $cd_row['height_feet'];
				}
				if(!empty($cd_row['height_inches'])) {
					if(!empty($height)) {
						$height .= ".".$cd_row['height_inches'];
					} else {
						$height = "0.".$cd_row['height_inches'];
					}
				}

				$benefit_amount = '';
				if(!empty($other_params['dep_benefit_amount']) && isset($other_params['dep_benefit_amount'][$cd_row['id']])) {
					$benefit_amount = $other_params['dep_benefit_amount'][$cd_row['id']];
				} else {
					if(!empty($product_id)) {
						$ba_sql ="SELECT amount 
								FROM customer_benefit_amount 
								WHERE 
								is_deleted='N' AND 
								customer_dependent_profile_id=:customer_dependent_profile_id AND 
								product_id=:product_id
								ORDER BY id DESC";
						$ba_where = array(":customer_dependent_profile_id"=>$cd_row['id'],":product_id"=>$product_id);
						$ba_row = $pdo->selectOne($ba_sql,$ba_where);
					    if(!empty($ba_row) && $ba_row['amount'] > 0) {
					    	$benefit_amount = $ba_row['amount'];				    	
					    }
					}
				}

				if(in_array(strtolower($cd_row['relation']),array('son','daughter'))) {
					$child_data[$child_cnt] = array(
						"display_id" => $cd_row['display_id'],
						"fname" => $cd_row['fname'],
						"gender" => $cd_row['gender'],
						"birthdate" => $cd_row['birth_date'],
						"zip" => $cd_row['zip_code'],
						"smoking_status" => $cd_row['smoke_use'],
						"tobacco_status" => $cd_row['tobacco_use'],
						"height" => $height,
						"weight" => $cd_row['weight'],
						"no_of_children" => "",
						"has_spouse" => "",
						"benefit_amount" => $benefit_amount,
						"benefit_level" => $cd_row['benefit_level'],
						"employmentStatus" => $cd_row['employmentStatus'],
						"salary" => $cd_row['salary'],
						"hire_date" => $cd_row['hire_date'],
						"hours_per_week" => $cd_row['hours_per_week'],
						"pay_frequency" => $cd_row['pay_frequency'],
						"us_citizen" => $cd_row['us_citizen'],
					);
					$child_cnt++;
				} else {
					$spouse_data[1] = array(
						"display_id" => $cd_row['display_id'],
						"fname" => $cd_row['fname'],
						"gender" => $cd_row['gender'],
						"birthdate" => $cd_row['birth_date'],
						"zip" => $cd_row['zip_code'],
						"smoking_status" => $cd_row['smoke_use'],
						"tobacco_status" => $cd_row['tobacco_use'],
						"height" => $height,
						"weight" => $cd_row['weight'],
						"no_of_children" => "",
						"has_spouse" => "",
						"benefit_amount" => $benefit_amount,
						"benefit_level" => $cd_row['benefit_level'],
						"employmentStatus" => $cd_row['employmentStatus'],
						"salary" => $cd_row['salary'],
						"hire_date" => $cd_row['hire_date'],
						"hours_per_week" => $cd_row['hours_per_week'],
						"pay_frequency" => $cd_row['pay_frequency'],
						"us_citizen" => $cd_row['us_citizen'],
					);
				}
			}
		}
	}
	$enrollee = array();
	if(!empty($primary_data)) {
		$enrollee['Primary'] =  $primary_data;
	}
	if(!empty($spouse_data)) {
		$enrollee['Spouse'] =  $spouse_data;
	}
	if(!empty($child_data)) {
		$enrollee['Child'] =  $child_data;
	}
	return $enrollee;
}
function get_product_price_detail($customer_id,$product_id,$plan_type,$ws_id=0,$other_params = array(),$is_cobra='N')
{
	global $pdo;
	include_once __DIR__ . '/member_enrollment.class.php';
	$MemberEnrollment = new MemberEnrollment();
	$missing_pricing_criteria = array();
	$pricing_criteria_not_match = array();
	$plan_id = 0;
	$price = 0.0;
	$member_price = 0.0;
	$display_member_price = 0.0;
	$group_price = 0.0;
	$display_group_price = 0.0;
	$is_group_member = 'N';
	$contribution_type = '';
	$contribution_value = '';
	$groupCoverageContributionArr = array();
	$today_date = date('Y-m-d');
	$error_display = "";

	$shortTermProductDetails = $MemberEnrollment->shortTermDisabilityProductDetails($product_id);

	$is_short_term_disability_product = 'N';
	$monthly_benefit_allowed_db = "";
	$percentage_of_salary_db = "";
	$prd_matrix_id = 0;
	$annual_salary = 0;
	$accepted = 'N';
	$benefit_amount_percentage = "";

	if($customer_id){

		$sqlAmount="SELECT salary FROM customer_settings where customer_id=:customer_id";
		$resAmount = $pdo->selectOne($sqlAmount,array(":customer_id"=>$customer_id));

		$annual_salary = $resAmount['salary'];
	}

	if($shortTermProductDetails){
		$is_short_term_disability_product = $shortTermProductDetails['is_short_term_disablity_product'];
		$monthly_benefit_allowed_db = $shortTermProductDetails['monthly_benefit_allowed'];
		$percentage_of_salary_db = $shortTermProductDetails['percentage_of_salary'];
	}

	$cust_sql = "SELECT cs.class_id,cs.group_coverage_period_id,c.fname,c.gender,c.birth_date,c.zip,s.type as sponsor_type,c.sponsor_id
				FROM customer_settings cs
				JOIN customer c ON(c.id = cs.customer_id)
				JOIN customer s ON(s.id = c.sponsor_id)
				WHERE cs.customer_id=:customer_id";
	$cust_row = $pdo->selectOne($cust_sql,array(":customer_id"=>$customer_id));

	
	if($cust_row['sponsor_type'] == "Group" && $is_cobra == 'N') {
		$is_group_member = 'Y';	

		$sqlCoveragePeriod="SELECT gcc.*,gc.pay_period 
			FROM group_coverage_period_offering gco 
			JOIN group_classes gc ON (gc.id=gco.class_id and gc.is_deleted='N') 
			LEFT JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N')
			where gco.is_deleted='N' AND gco.status='Active' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id";
		$sqlCoveragePeriodWhere=array(':group_id'=>$cust_row['sponsor_id'],':class_id'=>$cust_row['class_id'],':group_coverage_period_id'=>$cust_row['group_coverage_period_id']);
		$resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);
		
		foreach ($resCovergaePeriod as $key => $value) {
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['type']=$value['type'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['contribution']=$value['con_value'];
			$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['pay_period']=$value['pay_period'];
			$groupCoverageContributionArr['pay_period']['pay_period']=$value['pay_period'];
		}
	}

	$prd_sql = "SELECT pricing_model FROM prd_main p WHERE p.id=:id";
	$prd_row = $pdo->selectOne($prd_sql,array(":id"=>$product_id));
	$orig_pricing_model = $prd_row['pricing_model'];
	if(!empty($prd_row)) {
		if($prd_row['pricing_model'] == "FixedPrice") {
			$plan_sql = "SELECT id,price 
						FROM prd_matrix 
						WHERE 
						is_deleted='N' AND 
						(pricing_effective_date <= :today_date AND (pricing_termination_date >= :today_date OR pricing_termination_date is null)) AND
						product_id=:product_id AND 
						plan_type=:plan_type";
			$plan_row = $pdo->selectOne($plan_sql,array(":product_id"=>$product_id,":plan_type"=>$plan_type,":today_date"=>$today_date));
			if(!empty($plan_row)) {
				$plan_id = $plan_row['id'];
				$prd_matrix_id = $plan_row['id'];
				$price = $plan_row['price'];
				$display_member_price = $plan_row['price'];

				if(isset($groupCoverageContributionArr) && $groupCoverageContributionArr){
					$tmp_contribution_value = isset($groupCoverageContributionArr[$product_id][$plan_id]) ? $groupCoverageContributionArr[$product_id][$plan_id] : null;
					if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
						$tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];
						$calculatedPrice = $MemberEnrollment->calculateGroupContributionPrice($price,$tmp_group_coverage_contribution,false);
						$member_price = $calculatedPrice['member_price'];
						$display_member_price = $calculatedPrice['display_member_price'];
						$group_price = $calculatedPrice['group_price'];
						$display_group_price = $calculatedPrice['display_group_price'];
						$contribution_type = $calculatedPrice['contribution_type'];
						$contribution_value = $calculatedPrice['contribution_value'];
					}
				}

				if($is_short_term_disability_product == 'Y' && isset($other_params['primary_benefit_amount'])){
					if($prd_matrix_id){
						$price = getname('prd_matrix',$prd_matrix_id,'price','id');
						if($price){
							$adjusted_percentage = $MemberEnrollment->calculateSTDPercentage($annual_salary,$other_params['primary_benefit_amount']);
							$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted,$percentage_of_salary_db);

							if($accepted == 'Y'){
								$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted,$percentage_of_salary_db);
							}

							$rate = $rate_details['rate'];
							$monthly_benefit = $rate_details['monthly_benefit'];

							
							if($rate_details['allowed_benefit_amount'] < $other_params['primary_benefit_amount']){
								$error_display .= " <br> Maximum benefit percentage is ".$percentage_of_salary_db."% of monthly salary for this product";
								$amount_limit_error = true;
								$plan_id = 0;
							}else if(($monthly_benefit_allowed_db < $other_params['primary_benefit_amount'])){
								$error_display .= " <br> Maximum benefit amount is ".displayAmount($monthly_benefit_allowed_db,2)." for this product";
								$amount_limit_error = true;
								$plan_id = 0;
							}

							if(empty($error_display)){
								$display_member_price = $rate;
								$price = $rate;
								$tmp_plan_id = $prd_matrix_id;
								$group_price = 0;
								$display_group_price = 0;
								$member_price = 0;
								$benefit_amount_percentage = $adjusted_percentage;
							}
						}
					}
				}
			}
		} else {
			$assignedQuestionValue = $MemberEnrollment->assignedQuestionValue($product_id);
			$benefitAmountSetting = $MemberEnrollment->benefitAmountSetting($product_id);
			$assignedQuestion = $MemberEnrollment->getPriceAssignedQuestion($product_id);
			$variableEnrolleeOptions = array();

			$pricing_model = $prd_row['pricing_model'];
			if($pricing_model == "VariablePrice"){
				$assignedQuestionValue=$MemberEnrollment->assignedQuestionValue($product_id,$plan_type);
			}
			if($pricing_model=="VariableEnrollee"){
				$variableEnrolleeOptions=$MemberEnrollment->variableEnrolleeOptions($product_id);
			}
			$dep_ids = array();
			if(!empty($other_params['dep_ids'])) {
				$dep_ids = $other_params['dep_ids'];
			}
			$enrollee = get_enrollee_detail($customer_id,$product_id,$dep_ids,$other_params);
			$productDetails  = array();
			$largestChild = array();
			if(!empty($enrollee)){
				foreach ($enrollee as $enrolleeType => $enrolleeArr) {

					$valid_rule_id = array();
					if(isset($assignedQuestionValue[$enrolleeType]['id'])){
						foreach ($assignedQuestionValue[$enrolleeType]['id'] as $key => $value) {
							if(!empty($enrolleeArr)){
								foreach ($enrolleeArr as $fieldKey => $fieldName) {
									$is_rule_valid=true;

									if(isset($fieldName["gender"])){
										$criteriaGender = $assignedQuestionValue[$enrolleeType]['gender'][$key];
										if($criteriaGender!='' && $fieldName["gender"] != $criteriaGender){
											$is_rule_valid = false;
											if(empty($fieldName["gender"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Gender";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Gender";
											}
										}
									}
									if(isset($fieldName["birthdate"])){
										$age_from_birthdate=calculateAge($fieldName["birthdate"]);
										$criteriaAgeFrom = $assignedQuestionValue[$enrolleeType]['age_from'][$key];
										$criteriaAgeTo = $assignedQuestionValue[$enrolleeType]['age_to'][$key];
										
										if($criteriaAgeFrom>=0 &&  $criteriaAgeTo>0 && ($criteriaAgeFrom > $age_from_birthdate || $criteriaAgeTo < $age_from_birthdate)){
											$is_rule_valid = false;
											if(empty($fieldName["birthdate"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Birthdate";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Birthdate";
											}
										}
										if($enrolleeType == 'Child'){
											if(empty($largestChild)){
												$largestChild['age'] = $age_from_birthdate;
												$largestChild['id'] = $fieldKey;
											}else{
												if($age_from_birthdate > $largestChild['age']){
													$largestChild['age'] = $age_from_birthdate;
													$largestChild['id'] = $fieldKey;
												}
											}
										}
									}else{
										if($enrolleeType == 'Child' && empty($largestChild)){
											$largestChild['age'] = 0;
											$largestChild['id'] = $fieldKey;
										}
										
									}
									if(isset($fieldName["zip"])){
										$criteriaZip = $assignedQuestionValue[$enrolleeType]['zipcode'][$key];
										if($criteriaZip != '' && $fieldName["zip"] != $criteriaZip){
											$is_rule_valid = false;

											if(empty($fieldName["zip"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Zip";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Zip";
											}
										}
										$criteriaStateName = $assignedQuestionValue[$enrolleeType]['state'][$key];
										if(!empty($criteriaStateName)){
											$getStateCode=$pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$fieldName["zip"]));
											$pricing_control_State = '';
											if($getStateCode){
												$pricing_control_State = getname("states_c",$getStateCode['state_code'],"name","short_name");
											}

											if($criteriaStateName != $pricing_control_State){
												$is_rule_valid = false;
											}
											$restricted_state_date = date('Y-m-d');

											$restrictedStateSql="SELECT GROUP_CONCAT(distinct product_id) as restrictedStateProduct FROM prd_no_sale_states WHERE state_name=:state AND is_deleted='N' AND effective_date <= :restricted_state_date AND (termination_date >= :restricted_state_date OR termination_date IS NULL) AND product_id = :product_id";
											$restrictedStateRes=$pdo->selectOne($restrictedStateSql,array(":state"=>$pricing_control_State,":restricted_state_date"=>$restricted_state_date,':product_id' => $product_id));
											
											if(!empty($restrictedStateRes['restrictedStateProduct'])){
												$restrictedStateArray = explode(",", $restrictedStateRes['restrictedStateProduct']);

												if(in_array($product_id,$restrictedStateArray)){
													$is_rule_valid = false;
												}
											}
										}
									}
									if(isset($fieldName["smoking_status"])){
										$criteriaSmoking = $assignedQuestionValue[$enrolleeType]['smoking_status'][$key];
										if($criteriaSmoking != '' && $fieldName["smoking_status"] != $criteriaSmoking){
											$is_rule_valid = false;
											
											if(empty($fieldName["smoking_status"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Smoking Status";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Smoking Status";
											}
										}
									}
									if(isset($fieldName["tobacco_status"])){
										$criteriaTobacco = $assignedQuestionValue[$enrolleeType]['tobacco_status'][$key];
										if($criteriaTobacco !='' && $fieldName["tobacco_status"] != $criteriaTobacco){
											$is_rule_valid = false;
											
											if(empty($fieldName["tobacco_status"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Tobacco Status";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Tobacco Status";
											}
										}
									}
									if(isset($fieldName["height"])){
										$height=$fieldName["height"];
										
										$heightBy=$assignedQuestionValue[$enrolleeType]['height_by'][$key];
										$criteriaHeight = $assignedQuestionValue[$enrolleeType]['height_feet'][$key].".".$assignedQuestionValue[$enrolleeType]['height_inch'][$key];
										$criteriaHeightTo = $assignedQuestionValue[$enrolleeType]['height_feet_to'][$key].".".$assignedQuestionValue[$enrolleeType]['height_inch_to'][$key];

										if($heightBy=="Exactly"){
											if($criteriaHeight!='' && $height != $criteriaHeight){
												$is_rule_valid = false;

												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}else if($heightBy=="Less Than"){
											if($criteriaHeight!='' && $height >= $criteriaHeight){
												$is_rule_valid = false;

												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}else if($heightBy=="Greater Than"){
											if($criteriaHeight!='' && $height <= $criteriaHeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}else if($heightBy=="Range"){
											if($criteriaHeight!='' && $criteriaHeightTo!='' && ($criteriaHeight > $height || $criteriaHeightTo < $height)){
												$is_rule_valid = false;
												
												if(empty($fieldName["height"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Height";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Height";
												}
											}
										}
									}
									if(isset($fieldName["weight"])){
										$weight=$fieldName["weight"];
										
										$weightBy=$assignedQuestionValue[$enrolleeType]['weight_by'][$key];
										$criteriaWeight = $assignedQuestionValue[$enrolleeType]['weight'][$key];
										$criteriaWeightTo = $assignedQuestionValue[$enrolleeType]['weight_to'][$key];

										if($weightBy=="Exactly"){
											if($criteriaWeight!='' && $weight != $criteriaWeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}else if($weightBy=="Less Than"){
											if($criteriaWeight!='' && $weight >= $criteriaWeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}else if($weightBy=="Greater Than"){
											if($criteriaWeight!='' && $weight <= $criteriaWeight){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}else if($weightBy=="Range"){
											if($criteriaWeight!='' && $criteriaWeightTo!='' && ($criteriaWeight > $weight || $criteriaWeightTo < $weight)){
												$is_rule_valid = false;
												
												if(empty($fieldName["weight"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Weight";
												}		 else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Weight";
												}
											}
										}
									}
									if(isset($fieldName["no_of_children"])){
										$no_of_children=$fieldName["no_of_children"];
										
										$noOfChildrenBy=$assignedQuestionValue[$enrolleeType]['no_of_children_by'][$key];
										$criteriaNoOfChildren = $assignedQuestionValue[$enrolleeType]['no_of_children'][$key];
										$criteriaNoOfChildrenTo = $assignedQuestionValue[$enrolleeType]['no_of_children_to'][$key];

										if($noOfChildrenBy=="Exactly"){
											if($criteriaNoOfChildren!='' && $no_of_children != $criteriaNoOfChildren){
												$is_rule_valid = false;

												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}else if($noOfChildrenBy=="Less Than"){
											if($criteriaNoOfChildren!='' && $no_of_children >= $criteriaNoOfChildren){
												$is_rule_valid = false;
												
												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}else if($noOfChildrenBy=="Greater Than"){
											if($criteriaNoOfChildren!='' && $no_of_children <= $criteriaNoOfChildren){
												$is_rule_valid = false;
												
												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}else if($noOfChildrenBy=="Range"){
											if($criteriaNoOfChildren!='' && $criteriaNoOfChildrenTo!='' && ($criteriaNoOfChildren > $no_of_children || $criteriaNoOfChildrenTo < $no_of_children)){
												$is_rule_valid = false;
												
												if(empty($fieldName["no_of_children"])) {
													$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "No of children";
												} else {
													$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." No of children";
												}
											}
										}
									}
									if(isset($fieldName["has_spouse"])){
										$criteriaHasSpouse = $assignedQuestionValue[$enrolleeType]['has_spouse'][$key];
										if($criteriaHasSpouse!='' && $fieldName["has_spouse"] != $criteriaHasSpouse){
											$is_rule_valid = false;
											
											if(empty($fieldName["has_spouse"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Has Spouse";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Has Spouse";
											}
										}
									}
									if(isset($fieldName["benefit_amount"]) && array_key_exists(17, $assignedQuestion[$enrolleeType])){
										$criteriaBenefit = $assignedQuestionValue[$enrolleeType]['benefit_amount'][$key];
										if($criteriaBenefit !='0.00' && $fieldName["benefit_amount"] != $criteriaBenefit){
											$is_rule_valid = false;
											
											if(!empty($criteriaBenefit) && $criteriaBenefit !='0.00' && empty($fieldName["benefit_amount"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Benefit Amount";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Benefit Amount";
											}
										}

										if(!empty($benefitAmountSetting) ){
											if(isset($enrollee['Primary']) && isset($enrollee['Spouse'])){
												if($benefitAmountSetting['is_spouse_issue_amount_larger']=='N' && $enrollee['Spouse']['1']['benefit_amount'] > $enrollee['Primary']['1']['benefit_amount']){
													$is_rule_valid = false;
													$error_display = 'Spouse issue amount can not be larger than primary';
												}
											}
											
											
											/*if($enrolleeType == 'Primary' && !empty($benefitAmountSetting['primary_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['primary_issue_amount']){

												$is_rule_valid = false;
												$error_display = 'Guarantee Issue amount for Primary is $'.$benefitAmountSetting['primary_issue_amount'].', please select this benefit level';
												
											}
											
											if($enrolleeType == 'Spouse' && !empty($benefitAmountSetting['spouse_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['spouse_issue_amount']){
												$is_rule_valid = false;
												$error_display = 'Guarantee Issue amount for Spouse is $'.$benefitAmountSetting['spouse_issue_amount'].', please select this benefit level';
												
											}
											if($enrolleeType == 'Child' && !empty($benefitAmountSetting['child_issue_amount']) && $fieldName["benefit_amount"] > $benefitAmountSetting['child_issue_amount']){
												$is_rule_valid = false;
												$error_display = 'Guarantee Issue amount for Child(ren) is $'.$benefitAmountSetting['child_issue_amount'].', please select this benefit level';
												
											}*/
										}
									}
									if(isset($fieldName["in_patient_benefit"]) && array_key_exists(18, $assignedQuestion[$enrolleeType])){
										$criteriaInPatientBenefit = $assignedQuestionValue[$enrolleeType]['in_patient_benefit'][$key];
										if($criteriaInPatientBenefit !='0.00' && $fieldName["in_patient_benefit"] != $criteriaInPatientBenefit){
											$is_rule_valid = false;
											if(!empty($criteriaInPatientBenefit) && $criteriaInPatientBenefit !='0.00' && empty($fieldName["in_patient_benefit"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "InPatient Benefit";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." InPatient Benefit";
											}
										}
									}
									if(isset($fieldName["out_patient_benefit"]) && array_key_exists(19, $assignedQuestion[$enrolleeType])){
										$criteriaOutPatientBenefit = $assignedQuestionValue[$enrolleeType]['out_patient_benefit'][$key];
										if($criteriaOutPatientBenefit !='0.00' && $fieldName["out_patient_benefit"] != $criteriaOutPatientBenefit){
											$is_rule_valid = false;
											if(!empty($criteriaOutPatientBenefit) && $criteriaOutPatientBenefit !='0.00' && empty($fieldName["out_patient_benefit"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "OutPatient Benefit";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." OutPatient Benefit";
											}
										}
									}
									if(isset($fieldName["monthly_income"]) && array_key_exists(20, $assignedQuestion[$enrolleeType])){
										$criteriaMonthlyIncome = $assignedQuestionValue[$enrolleeType]['monthly_income'][$key];
										if($criteriaMonthlyIncome !='0.00' && $fieldName["monthly_income"] != $criteriaMonthlyIncome){
											$is_rule_valid = false;
											if(!empty($criteriaMonthlyIncome) && $criteriaMonthlyIncome !='0.00' && empty($fieldName["monthly_income"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Monthly Income";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Monthly Income";
											}
										}
									}
									/*
									if(isset($fieldName["benefit_percentage"])){
										$criteriaBenefitPercentage = $assignedQuestionValue[$enrolleeType]['benefit_percentage'][$key];
										if($criteriaBenefitPercentage !='0.00' && $fieldName["benefit_percentage"] != $criteriaBenefitPercentage){
											$is_rule_valid = false;
											if(!empty($criteriaBenefitPercentage) && $criteriaBenefitPercentage !='0.00' && empty($fieldName["benefit_percentage"])) {
												$missing_pricing_criteria[$enrolleeType][$fieldKey][] = "Benefit Percentage";
											} else {
												$pricing_criteria_not_match[$enrolleeType][$fieldKey][] = $key." Benefit Percentage";
											}
										}
									}*/
									if($is_rule_valid){
										if(!empty($valid_rule_id[$fieldKey])){
											$prevID = $valid_rule_id[$fieldKey];
											$newID  = $key;

											if($assignedQuestionValue[$enrolleeType]['price'][$newID] > $assignedQuestionValue[$enrolleeType]['price'][$prevID]){
												$valid_rule_id[$fieldKey]=$key;
											}

										}else{
											$valid_rule_id[$fieldKey]=$key;
										}
									}
								}
							}
						}
					}
					
					if(!empty($valid_rule_id)){
						foreach ($valid_rule_id as $fieldKey => $value) {

							if($enrolleeType=='Child' && !empty($variableEnrolleeOptions) && $variableEnrolleeOptions['child_dependent_rate_calculation']=='Single Rate based on Eldest Child'){
								if(!empty($largestChild) && $fieldKey == $largestChild['id']){
									$productDetails[$enrolleeType][$fieldKey]['matrix_id']=$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value];
							
									if(isset($groupCoverageContributionArr) && !empty($groupCoverageContributionArr)){
										$tmp_contribution_value = isset($groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]]) ? $groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]] : null;
										if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
											$tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];
											$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($assignedQuestionValue[$enrolleeType]['price'][$value],$tmp_group_coverage_contribution,false);
											$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
											$productDetails[$enrolleeType][$fieldKey]['member_price']=$calculatedPrice['member_price'];
											$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$calculatedPrice['display_member_price'];
											$productDetails[$enrolleeType][$fieldKey]['group_price']=$calculatedPrice['group_price'];
											$productDetails[$enrolleeType][$fieldKey]['display_group_price']=$calculatedPrice['display_group_price'];
											$productDetails[$enrolleeType][$fieldKey]['contribution_type']=$calculatedPrice['contribution_type'];
											$productDetails[$enrolleeType][$fieldKey]['contribution_value']=$calculatedPrice['contribution_value'];
										} else {
											$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
											$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
											$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
											$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
											$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
											
										}
									} else {
										$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['member_price']=0;

									}
								}
							}else{
								$productDetails[$enrolleeType][$fieldKey]['matrix_id']=$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value];
								
								if(isset($groupCoverageContributionArr) && !empty($groupCoverageContributionArr)){
									$tmp_contribution_value = isset($groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]]) ? $groupCoverageContributionArr[$assignedQuestionValue[$enrolleeType]['product_id'][$value]][$assignedQuestionValue[$enrolleeType]['prd_matrix_id'][$value]] : null;
									if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
										$tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];
										$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($assignedQuestionValue[$enrolleeType]['price'][$value],$tmp_group_coverage_contribution,false);
										$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['member_price']=$calculatedPrice['member_price'];
										$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$calculatedPrice['display_member_price'];
										$productDetails[$enrolleeType][$fieldKey]['group_price']=$calculatedPrice['group_price'];
										$productDetails[$enrolleeType][$fieldKey]['display_group_price']=$calculatedPrice['display_group_price'];
										$productDetails[$enrolleeType][$fieldKey]['contribution_type']=$calculatedPrice['contribution_type'];
										$productDetails[$enrolleeType][$fieldKey]['contribution_value']=$calculatedPrice['contribution_value'];
									}else{
										$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
										$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
										$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
										
									}
								}else{
									$productDetails[$enrolleeType][$fieldKey]['price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
									$productDetails[$enrolleeType][$fieldKey]['display_member_price']=$assignedQuestionValue[$enrolleeType]['price'][$value];
									$productDetails[$enrolleeType][$fieldKey]['group_price']=0;
									$productDetails[$enrolleeType][$fieldKey]['display_group_price']=0;
									$productDetails[$enrolleeType][$fieldKey]['member_price']=0;
									if($is_short_term_disability_product == 'Y' && isset($other_params['primary_benefit_amount'])){
										$price = $assignedQuestionValue[$enrolleeType]['price'][$value];
										if($price){
											$adjusted_percentage = $MemberEnrollment->calculateSTDPercentage($annual_salary,$other_params['primary_benefit_amount']);
											$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted,$percentage_of_salary_db);

											if($accepted == 'Y'){
												$rate_details = $MemberEnrollment->calculateSTDRate($price,$annual_salary,$adjusted_percentage,$accepted);
											}

											$rate = $rate_details['rate'];
											$monthly_benefit = $rate_details['monthly_benefit'];
											$benefit_amount_percentage = $adjusted_percentage;
											$productDetails['Primary'][1]['display_member_price'] = $rate;
											$productDetails['Primary'][1]['price'] = $rate;
											$productDetails['Primary'][1]['matrix_id'] = $prd_matrix_id;
											$productDetails['Primary'][1]['group_price']=0;
											$productDetails['Primary'][1]['display_group_price']=0;
											$productDetails['Primary'][1]['member_price']=0;
											$productDetails['Primary'][1]['monthly_benefit']=$monthly_benefit;

											if($rate_details['allowed_benefit_amount'] < $other_params['primary_benefit_amount']){
												$error_display .= " <br> Maximum benefit percentage is ".$percentage_of_salary_db."% of monthly salary for this product";
												$amount_limit_error = true;
												$plan_id = 0;
											}else if(($monthly_benefit_allowed_db < $other_params['primary_benefit_amount']) && $accepted == 'N'){
												$error_display .= " <br> Maximum benefit amount is ".displayAmount($monthly_benefit_allowed_db,2)." for this product";
												$amount_limit_error = true;
												$amount_limit_error_text = 'The maximum monthly benefit is $' . $monthly_benefit_allowed_db .'. To accept maximum amount click button below.' ;
											}
										}
										
									}
								}
							}
							
						}
						
					}
					
				}
			}

			if(!empty($enrollee)){
				foreach ($enrollee as $enrolleeType => $enrolleeArr) {
					if(!isset($productDetails[$enrolleeType])){
						$productDetails[$enrolleeType] = array();
					}
				}
			}
			if(!empty($productDetails) && empty($error_display)){
				$tmp_plan_id = '';
				$price = 0;
				$group_price = 0;
				$member_price = 0;
				$display_group_price = 0;
				$display_member_price = 0;
				foreach ($productDetails as $key1 => $value1) {
					if(!empty($value1)) {
						foreach ($value1 as $key2 => $value2) {
							if(!empty($tmp_plan_id)) {
								$tmp_plan_id .= ',';
							}
							$tmp_plan_id .= $value2['matrix_id'];
							$price += $value2['price'];
							$group_price += $value2['group_price'];
							$member_price += $value2['member_price'];
							$display_group_price += $value2['display_group_price'];
							$display_member_price += $value2['display_member_price'];

							if(!empty($value2['contribution_type']) && !empty($value2['contribution_value'])) {
								$contribution_type = $value2['contribution_type'];
								$contribution_value = $value2['contribution_value'];
							}
						}
					}
				}
				$plan_id = $tmp_plan_id;
			}

			//Remove duplicate errors
			if(!empty($missing_pricing_criteria)) {
				$org_missing_pricing_criteria = $missing_pricing_criteria;
				foreach ($missing_pricing_criteria as $key1 => $value1) {
					foreach ($value1 as $key2 => $value2) {
						$missing_pricing_criteria[$key1][$key2] = array_unique($missing_pricing_criteria[$key1][$key2]);
						if(isset($enrollee[$key1][$key2]['display_id'])) {
							$tmp_display_id = $enrollee[$key1][$key2]['display_id'];
							$missing_pricing_criteria[$key1][$tmp_display_id] = $missing_pricing_criteria[$key1][$key2];
							unset($missing_pricing_criteria[$key1][$key2]);
						}
					}
				}
			}
		}

	}

	return array(
		'customer_id' => $customer_id,
		'product_id' => $product_id,
		'prd_plan_type_id' => $plan_type,
		'ws_id' => $ws_id,
		'other_params' => $other_params,
		'productDetails' => !empty($productDetails)?$productDetails:array(),
		'enrollee' => !empty($enrollee)?$enrollee:array(),
		'error_display' => !empty($error_display)?$error_display:'',
		'plan_id' => $plan_id,
		'price' => $price,
		'member_price' => $member_price,
		'group_price' => $group_price,
		'display_member_price' => $display_member_price,
		'display_group_price' => $display_group_price,
		'contribution_type' => $contribution_type,
		'contribution_value' => $contribution_value,
		'missing_pricing_criteria' => $missing_pricing_criteria,
		'org_missing_pricing_criteria' => isset($org_missing_pricing_criteria)?$org_missing_pricing_criteria:'',
		'pricing_criteria_not_match' => isset($pricing_criteria_not_match)?$pricing_criteria_not_match:'',
		'valid_rule_id' => isset($valid_rule_id)?$valid_rule_id:'',
		'pricing_model' => $prd_row['pricing_model'],
		'benefit_amount_percentage' => $benefit_amount_percentage,
	);
	
}

function get_product_benefit_tiers($product_id = 0)
{
	global $pdo;

	if(empty($product_id)) {
		return array();
	}


	$sqlAssigned="SELECT * FROM prd_coverage_options WHERE is_deleted='N' AND product_id=:product_id";
    $resAssgined=$pdo->select($sqlAssigned,array(":product_id"=> $product_id));
    $prd_plan_type_ids = array();
    if(!empty($resAssgined)){
        foreach ($resAssgined as $key => $value) {
            $prd_plan_type_ids[] = $value['prd_plan_type_id'];
        }
    }
    if(!empty($prd_plan_type_ids)) {
        $productPlan = $pdo->select("SELECT ppt.* FROM prd_plan_type ppt WHERE ppt.id IN(".implode(',',$prd_plan_type_ids).") ORDER BY order_by ASC");
    } else {
        $productPlan = $pdo->select("SELECT ppt.* FROM prd_plan_type ppt JOIN prd_matrix pm ON (pm.plan_type = ppt.id) WHERE  pm.product_id= :product_id AND is_deleted = 'N' GROUP BY ppt.id ORDER BY order_by ASC",array(':product_id' => $product_id));  
    }
    return $productPlan;
}
function displayDate($date = '')
{
	$re_date = '-';
	if(abs(strtotime($date)) > 0) {
		$re_date = date("m/d/Y",strtotime($date));
	}
	return $re_date;
}
function get_product_id_by_plan_id($plan_id) {
	global $pdo;
	$product_id = 0;
	$prd_sql = "SELECT id FROM prd_main WHERE id IN (SELECT product_id FROM prd_matrix WHERE id=:id)";
	$prd_row = $pdo->selectOne($prd_sql,array(":id"=>$plan_id));
	if(!empty($prd_row['id'])) {
		$product_id = $prd_row['id'];
	}
	return $product_id;
}

function get_group_display_id() {
	global $pdo;
	$cust_id = rand(100000, 999999);
	
	$sql = "SELECT count(display_id) as total FROM customer WHERE display_id =$cust_id";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_group_display_id();
	} else {
		return $cust_id;
	}
}

function get_group_id() {
	global $pdo;
	$cust_id = rand(10000, 99999);
	$sql = "SELECT count(*) as total FROM customer WHERE rep_id ='G" . $cust_id . "' OR rep_id ='" . $cust_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res['total'] > 0) {
		return get_group_id();
	} else {
		return "G" . $cust_id;
	}
}
function get_payables_new($payable_id){
	global $pdo;
	$sql="SELECT 
		pd.created_at AS ADDED_DATE,
		CASE
            WHEN pd.payee_type='Agent' THEN pd.type
            ELSE pd.payee_type
        END AS PAYEE_TYPE,
        CASE
            WHEN pd.payee_type='Agent' THEN ag.rep_id
            ELSE pf.display_id
        END AS PAYEE_ID,
        CASE
            WHEN pd.commission_id > 0 THEN CONCAT(ag.fname,' ',ag.lname)
            ELSE pf.name
        END AS PAYEE,
        fee_prd.product_code as FEE_CODE,
        fee_prd.name as FEE_NAME,
        pm.product_code AS PRODUCT_ID,
        pm.name AS PRODUCT_NAME,
        CASE
            WHEN pd.commission_id > 0 THEN
                CASE 
                    WHEN comm.is_advance='Y' THEN 
                        CASE 
                            WHEN comm.sub_type='Reverse' THEN (SELECT CONCAT(advance_month,' Months') FROM commission WHERE advance_reverse_id = comm.id LIMIT 1)
                            ELSE CONCAT(comm.advance_month,' Months')
                        END                                    
                    WHEN comm.is_pmpm_comm='Y' THEN CONCAT('$',ABS(comm.amount))
                    WHEN comm.is_fee_comm='Y' THEN IF(comm.original_amount IS NOT NULL AND comm.original_amount != 0,CONCAT('$',ABS(comm.original_amount)),CONCAT(comm.percentage,'%'))
                    ELSE IF(comm.original_amount IS NOT NULL AND comm.original_amount != 0,CONCAT('$',ABS(comm.original_amount)),CONCAT(comm.percentage,'%'))
                END
            WHEN fee_prd.id IS NOT NULL THEN
                CASE 
                    WHEN fee_matrix.price_calculated_on = 'Percentage' THEN CONCAT(pd.payout,'%')
                    ELSE CONCAT('$',pd.payout)
                END
            ELSE CONCAT('$',pd.payout)
        END AS PAYOUT,
        pd.credit AS CREDIT,
        pd.debit AS DEBIT
        FROM payable py 
        JOIN payable_details pd ON(pd.payable_id = py.id AND pd.is_deleted='N')
        JOIN order_details od ON(od.order_id=py.order_id AND od.id=py.order_detail_id AND od.is_deleted='N')
        JOIN prd_main pm ON(pm.id = py.product_id)
        LEFT JOIN prd_assign_fees paf ON(paf.fee_id=pd.fee_price_id AND paf.product_id=py.product_id AND paf.is_deleted='N')
        LEFT JOIN prd_fees pf ON(pf.id=paf.prd_fee_id OR (IF(paf.id IS NULL,pf.id=pd.payee_id,pf.id=paf.prd_fee_id) AND pf.is_deleted='N'))
        LEFT JOIN prd_main fee_prd ON (fee_prd.id = paf.fee_id)
        LEFT JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=fee_prd.id AND fee_matrix.is_deleted='N')
        LEFT JOIN customer ag ON (ag.id = pd.payee_id AND pd.payee_type='Agent')
        LEFT JOIN commission comm ON(comm.id=pd.commission_id AND comm.is_deleted='N')
		WHERE pd.payable_id=:payable_id 
		GROUP BY pd.id 
		ORDER BY pd.id DESC,pd.payee_type ASC";
	$res=$pdo->select($sql,array(":payable_id"=>$payable_id));
	return $res;
}

function get_payables($payable_id){
	global $pdo;
	//We are not use this get_payables_new
	$sql="SELECT py.id,o.id as orderId,p.name as product_name,p.product_code,o.transaction_id,pd.type,o.display_id,pd.created_at,IF(pd.payee_type = 'Agent',pd.type,pd.payee_type) as payee_type,fee_matrix.price_calculated_on as fee_method,pd.credit,pd.debit,o.grand_total,fee_matrix.price as amount,
	IF(pd.payee_type='Agent',CONCAT(c.fname,' ',c.lname),if(fee.name is not null ,fee.name,'Fee not found or Rule changed')) as feeName,
	IF(pd.payee_type='Agent',c.rep_id,fee.product_code) as feeCode,IF(pf.name is not null,pf.name,if(pd.type = 'Vendor','',pd.type)) as bndlName,if(pd.commission_id !=0 ,cr.rule_code,pf.display_id) as bndlCode,pd.payout
				FROM payable py 
				JOIN payable_details pd ON(pd.payable_id=py.id)
				LEFT JOIN prd_assign_fees paf ON(paf.fee_id=pd.fee_price_id AND paf.product_id=py.product_id and paf.is_deleted='N')
				LEFT JOIN prd_fees pf ON(( pf.id=paf.prd_fee_id OR (if(paf.id is null,pf.id=pd.payee_id,pf.id=paf.prd_fee_id)) AND pf.is_deleted='N'))
				LEFT JOIN customer c ON (c.id=pd.payee_id AND pd.payee_type='Agent')
				LEFT JOIN prd_main fee ON(fee.id=paf.fee_id and fee.is_deleted='N')
				LEFT JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=fee.id and fee_matrix.is_deleted='N')
				JOIN orders o ON(o.id=py.order_id)
				LEFT JOIN prd_main p ON(p.id=py.product_id and p.is_deleted='N')
				LEFT JOIN commission cm ON(cm.id=pd.commission_id and pd.commission_id!=0 and cm.is_deleted='N')
				LEFT JOIN commission_rule cr ON(cr.id=cm.rule_id and cr.is_deleted='N')
				WHERE pd.payable_id=:payable_id GROUP BY pd.id order by pd.created_at desc,pd.payee_type ASC";
	$res=$pdo->select($sql,array(":payable_id"=>$payable_id));
	return $res;
} 
function save_customer_dependent_profile_benefit_amount($cd_profile_id,$product_id,$benefit_amount = array())
{
	global $pdo;
	$dep_sql = "SELECT * FROM customer_dependent_profile WHERE id=:id";
    $dep_row = $pdo->selectOne($dep_sql, array(":id"=>$cd_profile_id));
    if(!empty($dep_row) && !empty($benefit_amount)) {
    	$benefit_amount_data = array(
			'customer_id' => $dep_row['customer_id'],
			'customer_dependent_profile_id' => $dep_row['id'],
			'product_id' =>$product_id,
			'type'=> (in_array(strtolower($dep_row['relation']),array("husband","wife"))?"Spouse":"Child"),
			'amount'=>!empty($benefit_amount['benefit_amount']) ? $benefit_amount['benefit_amount'] : 0,
			'in_patient_benefit'=>!empty($benefit_amount['in_patient_benefit']) ? $benefit_amount['in_patient_benefit'] : 0,
			'out_patient_benefit'=>!empty($benefit_amount['out_patient_benefit']) ? $benefit_amount['out_patient_benefit'] : 0,
			'monthly_income'=>!empty($benefit_amount['monthly_income']) ? $benefit_amount['monthly_income'] : 0,
			'benefit_percentage'=>!empty($benefit_amount['benefit_percentage']) ? $benefit_amount['benefit_percentage'] : 0,
		);
		$amount_sql = "SELECT id FROM customer_benefit_amount WHERE is_deleted='N' AND customer_dependent_profile_id=:customer_dependent_profile_id AND product_id=:product_id";
		$amount_row = $pdo->selectOne($amount_sql,array(":customer_dependent_profile_id"=>$dep_row['id'],":product_id"=>$product_id));

		if(!empty($amount_row)){
			$benefit_amount_id = $amount_row['id'];
			$amount_where = array("clause" => "id=:id", "params" => array(":id" => $amount_row['id']));
			$pdo->update("customer_benefit_amount", $benefit_amount_data,$amount_where);
		} else {
			$benefit_amount_id = $pdo->insert("customer_benefit_amount", $benefit_amount_data);
		}
		return $benefit_amount_id;
    }
    return 0;
}
function get_admin_dashboard($admin_id){
	global $pdo;

	$dashboard = "";
	$access_level = $pdo->selectOne("SELECT al.dashboard FROM access_level al JOIN admin a on(a.type = al.name) WHERE a.id = :id",array(':id' => $admin_id));
	if($access_level){
		$dashboard = $access_level['dashboard'];
	}
	return $dashboard;
}
//Check agent license expired or not
function checkLicenseExpiredOrNot($agent_id){
	global $pdo;
	$agent_license = $pdo->selectOne("SELECT MIN(license_exp_date) as expire_date FROM agent_license WHERE agent_id=:agent_id AND is_deleted='N'",array(":agent_id"=>$agent_id));
	$todate = date('Y-m-d');
	$expireArr = array();
	if(!empty($agent_license['expire_date'])){
		// Check If a license is expired (expiration date in past). 
		if(strtotime($agent_license['expire_date']) < strtotime($todate)){
			$expireArr['expired'] = 'expired';
		}else{
			// Check 30 days prior to a license expiring.
			$diff = date_diff(date_create($todate),date_create($agent_license['expire_date']));
			$days = $diff->format("%R%a");
			if($days <= 30){
				$expireArr['expired'] = 'expire_in_30_days';
				$expireArr['days'] = $diff->format("%a");
			}
		}
	}

	return $expireArr;
}
//Check agent EO expired or not
function checkEOExpiredOrNot($agent_id){
	global $pdo;
	$selE_o_expiration = "SELECT e_o_expiration FROM agent_document WHERE agent_id=:agent_id";
	$whrEO = array(":agent_id" => $agent_id);
	$resE_o_expiration = $pdo->selectOne($selE_o_expiration, $whrEO);

	$todate = date('Y-m-d');
	$expireArr = array();
	if(!empty($resE_o_expiration['e_o_expiration'])){
		// Check If a E&O is expired (expiration date in past). 
		if(strtotime($resE_o_expiration['e_o_expiration']) < strtotime($todate)){
			$expireArr['expired'] = 'expired';
		}
		/*else{
			// Check 30 days prior to a license expiring.
			$diff = date_diff(date_create($todate),date_create($resE_o_expiration['e_o_expiration']));
			$days = $diff->format("%R%a");
			if($days <= 30){
				$expireArr['expired'] = 'expire_in_30_days';
				$expireArr['days'] = $diff->format("%a");
			}
		}*/
	}

	return $expireArr;
}
function display_policy($ws_id = 0,$extra_params = array())
{
	global $pdo;
	$str = '';
	$sql = "SELECT ws.id,ws.benefit_amount,ws.website_id,ws.product_id,p.name as product_name,p.product_code,ppt.title as prd_plan_type
			FROM website_subscriptions ws
			JOIN prd_main p ON(p.id = ws.product_id)
			LEFT JOIN prd_plan_type ppt ON(ppt.id = ws.prd_plan_type_id)
			WHERE ws.id = :id";
	$row = $pdo->selectOne($sql,array(":id" => $ws_id));
	if(!empty($row)) {
		//$str .= ' <b>Policy : </b> <span class="text-red">'.$row['website_id'].'</span>';	
		//$str .= $row['product_name'].' (<span class="text-red">'.$row['product_code'].'</span>)';
		$str .= $row['product_name'].' (<a href="policy_details.php?ws_id='.md5($row['id']).'" target="_blank" class="red-link">'.$row['website_id'].'</a>)';
		
		if(!empty($row['prd_plan_type'])) {
			$str .= ' <b>Coverage : </b> '.$row['prd_plan_type'];	
		}
		
		if(isset($extra_params['display_benefit_amount'])) {
			$policy_benefit_amount = $row['benefit_amount'];
		    $dep_benefit_amount = $pdo->select("SELECT relation,benefit_amount FROM customer_dependent WHERE website_id=:website_id AND is_deleted = 'N'", array(':website_id' => $row['id']));
		    if ($dep_benefit_amount) {
		        foreach ($dep_benefit_amount as $amount) {
		            $policy_benefit_amount = $policy_benefit_amount + $amount['benefit_amount'];
		        }
		    }
		    $str .= ' <b>Benefit Amount : </b> '.displayAmount($policy_benefit_amount);
		}
	}
	return $str;
}
function get_ws_family_member_count($website_id = 0)
{
	global $pdo;
	$family_member = 0;
	$row = $pdo->selectOne("SELECT COUNT(id) as family_member FROM customer_dependent WHERE website_id=:website_id",array(":website_id" => $website_id));
	if(!empty($row)) {
		$family_member = $row['family_member'];
	}
	return $family_member;
}
function getOrderProducts($customer_id,$todate){
	global $pdo;
	$productArr = array();
/*
	//Get Website Subscriptions product plan and product wise
	$ordersProduct = $pdo->select("SELECT plan_id,product_id FROM website_subscriptions WHERE customer_id=:customer_id AND STATUS IN('Active','Suspended','On Hold Failed Billing') AND DATE(next_purchase_date)=:todate OR
	( next_attempt_at = :todate AND total_attempts>0 )",array(":customer_id"=>$customer_id,":todate"=>$todate));
*/
	
	//For new service Fee Applied to new selected coverage period if changed
	
	$ordersProduct = $pdo->select("SELECT plan_id,product_id,status FROM website_subscriptions WHERE customer_id=:customer_id AND STATUS IN('Active','Pending','Inactive') AND DATE(next_purchase_date)=:todate OR
	( next_attempt_at = :todate AND total_attempts>0 ) AND termination_date IS NULL HAVING if(status='Pending',product_id IN(product_id),1)",array(":customer_id"=>$customer_id,":todate"=>$todate));

	if(!empty($ordersProduct)){
			foreach($ordersProduct as $product){
				$productArr['plan'][$product['plan_id']] = $product['product_id'];
			}
			foreach($ordersProduct as $product){
				$productArr['product'][$product['product_id']] = $product['plan_id'];
			}		
	}
	return $productArr;
}
function get_sponsor_detail_for_mail($member_id, $sponsor_id = 0) {
	global $pdo;
	$response = array();

	if (empty($sponsor_id)) {
		$member_sql = "SELECT sponsor_id FROM customer WHERE id=:id AND is_deleted='N'";
		$member_where = array(':id' => $member_id);
		$member_row = $pdo->selectOne($member_sql, $member_where);
		if (!empty($member_row['sponsor_id'])) {
			$sponsor_id = $member_row['sponsor_id'];
		}
	}

	if (!empty($sponsor_id)) {
		$sponsor_sql = "SELECT id,public_name,public_email,public_phone,rep_id FROM customer WHERE id=:id AND is_deleted='N'";
		$sponsor_where = array(':id' => $sponsor_id);
		$sponsor_row = $pdo->selectOne($sponsor_sql, $sponsor_where);
		if (!empty($sponsor_row)) {
			if ($sponsor_row['id'] != '') {
				if (($sponsor_row['public_name'] != '') || ($sponsor_row['public_email'] != '') || ($sponsor_row['public_phone'] != '')) {
					$response['agent_name'] = $sponsor_row['public_name'];
					$response['agent_email'] = $sponsor_row['public_email'];
					$response['agent_phone'] = $sponsor_row['public_phone'];
					$response['agent_id'] = $sponsor_row['id'];
					$response['rep_id'] = $sponsor_row['rep_id'];
					$response['is_public_info'] = '';
				} else {
					$response['is_public_info'] = 'display:none';
				}
			} else {
				$response['is_public_info'] = 'display:none';
			}
		}
	} else {
		$response['is_public_info'] = 'display:none';
	}
	return $response;
}
function get_weekly_pay_period_main($date = "") {
	global $pdo;
	$weekDayRes = $pdo->selectOne("SELECT commission_day FROM commission_periods_settings WHERE commission_type='weekly'");
	$commDay = !empty($weekDayRes["commission_day"]) ? $weekDayRes["commission_day"] : "Sunday";
	$date = !empty($date) ? date("Y-m-d",strtotime($date)) : date('Y-m-d');
	if (date('l', strtotime($date)) == $commDay) {
		$payPeriod = $date;
	} else {
		$payPeriod = date('Y-m-d', strtotime("next $commDay",strtotime($date)));
	}
	return $payPeriod;
}
function get_monthly_pay_period_main($date = "") {
	$date = !empty($date) ? date("Y-m-d",strtotime($date)) : date('Y-m-d');
	$payPeriod = date("Y-m-d", strtotime("last day of this month",strtotime($date)));
	return $payPeriod;
}
function get_pay_period_commission_totals($agent_id = 0,$date = '')
{
	global $pdo;
	include_once dirname(__DIR__) . "/includes/commission.class.php";
	$commObj = new Commission();

	$weekly_comm = 0;
	$monthly_comm = 0;

	if(strtotime($date) > 0) {
		$tmp_today = date('Y-m-d',strtotime($date));
	} else {
		$tmp_today = date('Y-m-d');
	}

	$weekly_pay_period = $commObj->getWeeklyPayPeriod($tmp_today);
	$monthly_pay_period = $commObj->getMonthlyPayPeriod($tmp_today);
	
	$weekly_comm_sql = "SELECT SUM(cs.amount) as gross_total
	                     FROM commission cs
	                     JOIN customer c ON(c.id=cs.customer_id)
	                     WHERE cs.commission_duration='weekly' AND cs.customer_id =:id  AND cs.status IN ('Approved','Pending') AND cs.amount != 0 AND cs.is_deleted = 'N' AND date(cs.pay_period)=:pay_period GROUP BY cs.pay_period";
	$weekly_comm_res = $pdo->selectOne($weekly_comm_sql, array(":pay_period" => $weekly_pay_period,':id'=>$agent_id));
	if(!empty($weekly_comm_res)) {
		$weekly_comm = $weekly_comm_res['gross_total'];
	}

	$monthly_comm_sql = "SELECT SUM(cs.amount) as gross_total
	             FROM commission cs
	             JOIN customer c ON(c.id=cs.customer_id)
	             WHERE cs.commission_duration='monthly' AND cs.customer_id = :id AND cs.status IN ('Approved','Pending') AND cs.amount != 0 AND cs.is_deleted = 'N' AND date(cs.pay_period)=:pay_period GROUP BY cs.pay_period";
	$monthly_comm_res = $pdo->selectOne($monthly_comm_sql, array(":pay_period" => $monthly_pay_period,':id'=>$agent_id));
	if(!empty($monthly_comm_res)) {
		$monthly_comm = $monthly_comm_res['gross_total'];
	}

	return array(
		'weekly' => $weekly_comm,
		'monthly' => $monthly_comm,
	);
}
function get_pay_period_range($date = '',$duration = 'weekly')
{
	global $pdo;
	$date = strtotime($date) > 0 ? date("Y-m-d",strtotime($date)) : date('Y-m-d');

	if($duration == "weekly") {
		$weekDayRes = $pdo->selectOne("SELECT commission_day FROM commission_periods_settings WHERE commission_type='weekly'");
		$commDay = !empty($weekDayRes["commission_day"]) ? $weekDayRes["commission_day"] : "Sunday";
		if (date('l', strtotime($date)) == $commDay) {
			$end_pay_period = $date;
		} else {
			$end_pay_period = date('Y-m-d', strtotime("next $commDay",strtotime($date)));
		}
		$start_pay_period = date('Y-m-d', strtotime("-6 days",strtotime($end_pay_period)));
		return array('start' => $start_pay_period,'end' => $end_pay_period);
	} else {
		$start_pay_period = date("Y-m-d", strtotime("first day of this month",strtotime($date)));
		$end_pay_period = date("Y-m-d", strtotime("last day of this month",strtotime($date)));
		return array('start' => $start_pay_period,'end' => $end_pay_period);
	}
}
function get_pay_period_range_text($date = '',$duration = 'weekly')
{
	$pay_period_range = get_pay_period_range($date,$duration);
	return date('m/d/Y',strtotime($pay_period_range['start'])).' - '.date('m/d/Y',strtotime($pay_period_range['end']));
}
function get_declined_reason_from_tran_response($response = '',$show_error_code = true,$reason='') 
{	
	$declined_reason = '';
	if(!empty($response)) {
		$response_arr = json_decode($response,true);

		if(isset($response_arr['API_response']) && isset($response_arr['API_response']['error_message'])) {
			if($show_error_code == true && isset($response_arr['API_response']['error_code'])) {
				$declined_reason = $response_arr['API_response']['error_code'].' - ';
			}
			$declined_reason .= $response_arr['API_response']['error_message'];

		} elseif(empty($declined_reason) && isset($response_arr['error_message'])) {
			if($show_error_code == true && isset($response_arr['API_response']['error_code'])) {
				$declined_reason = $response_arr['API_response']['error_code'].' - ';
			}
			$declined_reason  .= $response_arr['error_message'];

		} elseif(empty($declined_reason) && isset($response_arr['message'])) {
			if($show_error_code == true && isset($response_arr['API_response']['error_code'])) {
				$declined_reason = $response_arr['API_response']['error_code'].' - ';
			}
			$declined_reason  .= $response_arr['message'];
		}
	}
	if(empty($declined_reason)) {
		if(!empty($reason)) {
			$declined_reason = $reason;
		} else {
		$declined_reason = 'Transaction Declined';
	}
	}
	return $declined_reason;
}
function str_replace_deep($search, $replace, $subject)
{
    if (is_array($subject))
    {
        foreach($subject as &$oneSubject)
            $oneSubject = str_replace_deep($search, $replace, $oneSubject);
        unset($oneSubject);
        return $subject;
    } else {
        return str_replace($search, $replace, $subject);
    }
}
function generateOTP() {
	$otp = rand(100000, 999999);
	return $otp;
}
function agent_has_member_access($member_id)
{	
	$AccessTime = 0;
	if(isset($_SESSION["login_time_stamp"][$member_id])){
		$AccessTime = time()-$_SESSION["login_time_stamp"][$member_id];
	}
	if($AccessTime > 1800){
		unset($_SESSION['member_access'][$member_id]);
		unset($_SESSION["login_time_stamp"][$member_id]);
	}
	$has_full_access = false;
	if(isset($_SESSION['member_access'][$member_id]) && $_SESSION['member_access'][$member_id] == true){
	    $has_full_access = true;
	}
	if(isset($_SESSION['agents']) && isset($_SESSION['agents']['passcode']) && $_SESSION['agents']['passcode'] == "Y") {
		$has_full_access = true;	
	}
	return $has_full_access;
}
function get_member_display_status($status = '')
{
	$display_status = $status;
	if(in_array($status,array("Active"))) {
        // $display_status = 'Active';
    }
    if(in_array($status,array("Pending","Post Payment"))) {
        $display_status = 'Pending';
    }
    if(in_array($status,array("Inactive"))) {
        // $display_status = 'Inactive';
    }
    if(in_array($status,array("Hold"))) {
        $display_status = 'Hold';
    }
	return $display_status;
}
function get_member_status_class($status = '')
{
	$display_status = get_member_display_status($status);
	$status_class="";
	if($display_status == "Hold" || $display_status == "Pending"){
	  	$status_class = 'Abandoned';

	} else if($display_status == "Inactive"){
	   $status_class = 'Unqualified';
	}
	return $status_class;
}
function get_order_total_commission($order_id)
{
	global $pdo;
	$total_commission = '';
	$comm_sql = "SELECT SUM(cs.amount) as total_commission 
						FROM commission cs 
						WHERE 
						cs.amount != 0 AND 
						cs.is_deleted = 'N' AND 
						cs.order_id=:order_id 
						GROUP BY cs.order_id";
	$comm_row = $pdo->selectOne($comm_sql,array(":order_id" => $order_id));
	if(!empty($comm_row)) {
		$total_commission = $comm_row['total_commission'];
	}
	return $total_commission;
}
function dispCommAmt($amount,$class=''){
	$commAmt = 0;
	if($amount < 0){
		$amount = abs($amount);
		$commAmt = "<span class='text-danger fw500'>(".displayAmount($amount).")</span>";
	}else{
		$commAmt = "<span class='".$class."'>".displayAmount($amount)."</span>";
	}
	return $commAmt;
}
function alert_admin_license_add_update($agent_id = '')
{
	global $pdo,$ADMIN_HOST;
	$trigger_row = $pdo->selectOne("SELECT * FROM triggers WHERE display_id='T956'");
	if(!empty($trigger_row) && !empty($trigger_row['to_email_specific'])) {
		$agent_row = $pdo->selectOne('SELECT * FROM customer WHERE md5(id)=:id',array(":id" => $agent_id));

		$mail_data = array();
        $mail_data['fname'] = $agent_row['fname'];
        $mail_data['lname'] = $agent_row['lname'];
        $mail_data['AgentID'] = $agent_row['rep_id'];
        $mail_data['Email'] = $agent_row['email'];
        $mail_data['Phone'] = format_telephone($agent_row['cell_phone']);

        $mail_data['link'] = $ADMIN_HOST;

        $smart_tags = get_user_smart_tags($agent_row['id'],'agent');
                
        if($smart_tags){
            $mail_data = array_merge($mail_data,$smart_tags);
        }

        if(!empty($trigger_row['cc_email_specific'])) {
        	$mail_data['EMAILER_SETTING']['cc_email'] = $trigger_row['cc_email_specific'];
        }
        if(!empty($trigger_row['bcc_email_specific'])) {
        	$mail_data['EMAILER_SETTING']['bcc_email'] = $trigger_row['bcc_email_specific'];
        }

        trigger_mail($trigger_row['id'],$mail_data,$trigger_row['to_email_specific']);
	}
}
function get_admin_feature_access_options()
{
	global $pdo;
	$sql_feature = "SELECT id, title, IF(parent_id = 0, id, parent_id) as parent_id 
                    FROM feature_access where is_deleted='N' AND order_by > 0 
                    ORDER BY order_by";
    $res_feature = $pdo->select($sql_feature);
    $features_arr = array();
    if (!empty($res_feature)) {
        foreach ($res_feature as $feature) {
            if (!in_array($feature['id'], array(4, 16, 20, 52, 51, 30, 31, 32, 41, 55, 47, 52, 21, 22, 23, 24, 43, 59, 61, 41, 57, 38, 39, 40, 48, 18, 19, 50, 56, 29,85))) {

            	$feature["title"] = ($feature["title"] == 'Eligibility Files' ? 'Eligibility' : $feature["title"]);
            	$feature["title"] = ($feature["title"] == 'Types' ? 'Categories' : $feature["title"]);
            	$feature["title"] = ($feature["title"] == 'Pending Eligibility' ? 'Generator' : $feature["title"]);
            	$feature["title"] = ($feature["title"] == 'Eligibility History' ? 'History' : $feature["title"]);

                if (!isset($features_arr[$feature['parent_id']])) {
                    $features_arr[$feature['parent_id']] = $feature;
                    $features_arr[$feature['parent_id']]['child'] = array();
                } else {
                    $features_arr[$feature['parent_id']]['child'][] = $feature;
                }
            }
        }
    }
    return $features_arr;
}
function add_export_request_api($file_type,$user_id,$user_type,$title,$export_location,$query_string,$query_params = array(),$extra = array(),$report_key = "",$report_path="") {
	global $pdo,$REPORT_DB,$ADMIN_HOST,$SITE_ENV,$AWS_REPORTING_URL;
	if($report_path == ""){
		if($report_key!=""){
			$report_path = $AWS_REPORTING_URL[$report_key];
		}
	}
	$data = array(
		'file_type' => $file_type,
		'user_id' => $user_id,
		'user_type' => $user_type,
		'title' => $title,
		'export_location' => $export_location,
		'query_string' => $query_string,
		'query_params' => (!empty($query_params)?json_encode($query_params):''),
		'extra' => (!empty($extra)?json_encode($extra):''),
		'req_url' => (isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:'system'),
		'status' => 'Pending',
		'is_manual' => 'Y',
		'report_path' => $report_path,
	);
	if(!empty($report_key)){
		$reportID = getname("$REPORT_DB.rps_reports",$report_key,'id','report_key');
		if(!empty($reportID)){
			$data['report_id'] = $reportID;
		}
	}
	$row = array();
	$entity_id = $entity_type = $entity_link = $user_link = '';
	if(in_array($user_type, array('Admin','admin','admins'))){
		$row = $pdo->selectOne("SELECT id,fname,lname,display_id as rep_id from admin where id=:id",array(":id"=>$user_id));
		$user_link = $ADMIN_HOST.'/admin_profile.php?id='.md5($user_id);
	}else{
		$row = $pdo->selectOne("SELECT id,fname,lname,rep_id from customer where id=:id",array(":id"=>$user_id));
		if(in_array($user_type,array('customer','customers','Customer'))){
			$user_link = $ADMIN_HOST.'/members_details.php?id='.md5($user_id);
		}elseif(in_array($user_type,array('groups','group','Group'))){
			$user_link = $ADMIN_HOST.'/groups_details.php?id='.md5($user_id);
		}elseif(in_array($user_type,array('agent' ,'agents','Agent'))){
			$user_link = $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($user_id);
		}
	}
	$ac_message_1 = !empty($extra['activit_feed']['entity_id']) ? '  Created '.$title.' Export Request For' : '  Created '.$title.' Export Request ';
	$desc = array();
    $desc['ac_message'] =array(
        'ac_red_1'=>array(
            'href'=>$user_link,
            'title'=>$row['rep_id'],
        ),
		'ac_message_1' =>$ac_message_1,
	);
	
	if(isset($extra['activit_feed']['entity_id']) && !empty($extra['activit_feed']['entity_id'])){
		$entity_id = $extra['activit_feed']['entity_id'];
		$erow = array();
		if(in_array($extra['activit_feed']['entity_type'], array('Admin','admin','admins'))){
			$erow = $pdo->selectOne("SELECT id,fname,lname,display_id as rep_id from admin where id=:id",array(":id"=>$entity_id));
			$entity_link = $ADMIN_HOST.'/admin_profile.php?id='.md5($entity_id);
			$entity_type = $extra['activit_feed']['entity_type'];
		}else{
			$erow = $pdo->selectOne("SELECT id,fname,lname,rep_id from customer where id=:id",array(":id"=>$entity_id));
			if(in_array($extra['activit_feed']['entity_type'],array('customer','customer','Customer'))){
				$entity_link = $ADMIN_HOST.'/members_details.php?id='.md5($entity_id);
				$entity_type = $extra['activit_feed']['entity_type'];
			}elseif(in_array($extra['activit_feed']['entity_type'],array('Agent','agent','agents'))){
				$entity_link = $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($entity_id);
				$entity_type = $extra['activit_feed']['entity_type'];
			}elseif(in_array($extra['activit_feed']['entity_type'],array('groups','group','Group'))){
				$entity_link = $ADMIN_HOST.'/groups_details.php?id='.md5($entity_id);
				$entity_type = $extra['activit_feed']['entity_type'];
			}
		}
		$desc['ac_message']['ac_red_2'] = array(
				'href'=>$entity_link,
				'title'=>$erow['rep_id'],
		);
	}
	if(!empty($extra['report_id'])) {
		$data['report_id'] = $extra['report_id'];
	}
	$job_id = $pdo->insert("$REPORT_DB.export_requests",$data);
	if($entity_id == ''){
		$entity_id = $job_id;
		$entity_type = $title;
	}
    $desc = json_encode($desc);
	activity_feed(3,$user_id, $user_type , $entity_id, $entity_type, 'Report Export Request',$row['fname'].$row['lname'],"",$desc);

	if(!empty($report_key)) {
		include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
		
		$reportDownloadURL = $AWS_REPORTING_URL[$report_key]."&job_id=".$job_id;
		$ch = curl_init($reportDownloadURL);
		if($SITE_ENV == "Local"){
			$ch = curl_init($reportDownloadURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			$api_response = curl_exec($ch);
			curl_close($ch);
		} else {
			$ch = curl_init($reportDownloadURL);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_exec($ch);
			curl_close($ch);
		}
	}
	return $job_id;
}
function get_agent_feature_access_options()
{
	global $pdo;
	$features_arr = array();
	$featureAccessSql="SELECT id, title, IF(parent_id = 0, id, parent_id) as parent_id 
    FROM agent_feature_access 
    ORDER BY parent_id, id";
    
    $featureAccessRes=$pdo->select($featureAccessSql);
    if (!empty($featureAccessRes)) {
        foreach ($featureAccessRes as $feature) {
            if (!isset($features_arr[$feature['parent_id']])) {
                $features_arr[$feature['parent_id']] = $feature;
                $features_arr[$feature['parent_id']]['child'] = array();
            } else {
                $features_arr[$feature['parent_id']]['child'][] = $feature;
            }
        }
    }
    return $features_arr;
}
function generate_report_request($schedule_id,$generate_past_req = true){
    global $pdo,$REPORT_DB,$SITE_ENV,$AWS_REPORTING_URL;
    include dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
    $today = date("Y-m-d");
    $allow_schedule = 'N';
    $allow_request = 'N';

    if(!empty($schedule_id)){
        $selScheduled = "SELECT * FROM $REPORT_DB.rps_reports_schedule WHERE is_deleted='N' AND id=:id";
        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));

        if(!empty($getScheduled) && is_array($getScheduled)){
            $schedule_type = $getScheduled['schedule_type'];
            $schedule_frequency = $getScheduled['schedule_frequency'];
            $schedule_end_type = $getScheduled['schedule_end_type'];
            $schedule_end_val = $getScheduled['schedule_end_val'];

            $process_cnt = $getScheduled['process_cnt'];
            $last_processed = $getScheduled['last_processed'];
            $last_process_date = '';
            $process_datetime = '';

            // check End Repeat code start
                if($schedule_end_type == "on_date"){
                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
                    if(strtotime($end_date) >= strtotime($today)){
                        $allow_schedule = 'Y';
                    }
                } else if($schedule_end_type == "no_of_times"){
                    if($process_cnt != '' && $schedule_end_val != ''){
                        if($process_cnt <= $schedule_end_val){
                             $allow_schedule = 'Y';    
                        }
                    }
                } else if($schedule_end_type == "never"){
                         $allow_schedule = 'Y';
                }
            // check End Repeat code ends

            // check if report scheduled today code start
                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
                        $last_process_date = date("Y-m-d",strtotime($last_processed));
                    }

                    // check for daily report code start
                        if($schedule_type == "daily"){
                            if($last_process_date != ''){
                                $process_datetime = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
                                if(strtotime($process_datetime) < strtotime($today)) {
                                	$process_datetime = date("Y-m-d");	
                                }
                            }else{
                                $process_datetime = date("Y-m-d");
                            }
                        }
                    // check for daily report code ends
                    
                    // check for weekly report code start
                        if($schedule_type == "weekly"){
                            $days_of_week = '';
                            $days_of_week = $getScheduled['days_of_week'];
                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();

                            if($last_process_date != ''){
                                $last_processedWeek = '';
                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
                                $start_week = strtotime("last monday",$last_processedWeek);
                                $start_week = date("Y-m-d",$start_week);
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                       
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $process_datetime = $check_week_date;
                                        }
                                    }
                                }
                            } else {
                                $start_week = date("Y-m-d",strtotime("last monday"));
                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
                                    foreach ($days_of_week_arr as $day) {
                                        $check_week_date = '';
                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
                                             $process_datetime = $check_week_date;
                                        }
                                    }
                                }
                            }
                        }
                    // check for weekly report code ends
            
                    // check for monthly report code start
                        if($schedule_type == "monthly"){
                            $month_option = '';
                            $month_option = $getScheduled['month_option'];
                            if($month_option != '' && $month_option == "days_of_month"){
                                $days_of_month = '';
                                $days_of_month = $getScheduled['days_of_month'];
                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                     $process_datetime = date('Y-m-d');
                                }
                            }else if($month_option != '' && $month_option == "on_the_day"){
                                $day_type = '';
                                $selected_day = '';
                                $day_type = $getScheduled['day_type'];
                                $selected_day = $getScheduled['selected_day'];
                                if($day_type != '' && $selected_day != ''){
                                    $specific_day = $day_type.' '.$selected_day;
                                    $process_datetime = date('Y-m-d',strtotime("$specific_day of this month"));
                                }
                            }
                        }
                    // check for monthly report code ends 
                    
                    // check for yearly report code start
                        if($schedule_type == "yearly"){
                            $months = '';
                            $months = $getScheduled['months'];
                            $months_arr = ($months != '') ? explode(",",$months) : array();
                            if(is_array($months_arr) && in_array(date('M'),$months_arr)){
                                $month_option = '';
                                $month_option = $getScheduled['month_option'];
                                if($month_option != '' && $month_option == "days_of_month"){
                                    $days_of_month = '';
                                    $days_of_month = $getScheduled['days_of_month'];
                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
                                    if(is_array($days_of_month_arr) && in_array(date('d'),$days_of_month_arr)){
                                         $process_datetime = date('Y-m-d');
                                    }
                                }else if($month_option != '' && $month_option == "on_the_day"){
                                    $day_type = '';
                                    $selected_day = '';
                                    $day_type = $getScheduled['day_type'];
                                    $selected_day = $getScheduled['selected_day'];
                                    if($day_type != '' && $selected_day != ''){
                                        $specific_day = $day_type.' '.$selected_day;
                                        $process_datetime = date('Y-m-d',strtotime("$specific_day of this month"));
                                    }
                                }
                            }
                        }
                    // check for yearly report code ends 

                    if($generate_past_req == true) {
	                    if(strtotime($process_datetime) == strtotime($today)){
	                        $allow_request = "Y";
	                    }
                    } else {
                    	$date_time = date('Y-m-d H:i',strtotime($process_datetime.''.$getScheduled['time']));
                    	if(strtotime($date_time) >= strtotime(date('Y-m-d H:i'))){
	                        $allow_request = "Y";
	                    }
                    }
                }
            // check if reports scheduled today code ends
            
            // generate schedule request code start
                if($allow_request != '' && $allow_request == "Y"){
                    $date_time = date('Y-m-d H:i',strtotime($process_datetime.''.$getScheduled['time']));
                    //$convert_date_time =  convertTimeZone($date_time, $getScheduled['timezone'], $to = "EST");
                    $convert_date_time =  $date_time;

                    $report_sql = "SELECT * FROM $REPORT_DB.rps_reports WHERE id=:id"; 
    				$report_row = $pdo->selectOne($report_sql,array(":id" => $getScheduled['report_id']));

                    $generate_via = $getScheduled['generate_via'];
                    $report_path = "";
                    $report_key = $report_row['report_key'];
                    if(!empty($report_key)){
                    	$report_path = isset($AWS_REPORTING_URL[$report_key]) ? $AWS_REPORTING_URL[$report_key] : "";
                    }
                    $ins_params = array(
                    	'file_type' => $report_row['file_type'],
                    	"user_id" => $getScheduled['user_id'],
                        "user_type" => $getScheduled['user_type'],
                        "schedule_id" => $getScheduled['id'],
                        "report_id" => $getScheduled['report_id'],
                        "title" => $report_row['export_file_report_name'],
                        "export_location" => 'set_report_schedule',
                        "query_string" => '',
                        "query_params" => '',
                        "filter_options" => isset($getScheduled['filter_options'])?$getScheduled['filter_options']:'',
                        "extra" => isset($getScheduled['extra'])?$getScheduled['extra']:'',
                        "req_url" => 'report_schedule_cron',
                        "generate_via" => $generate_via,
                        "is_manual" => 'N',
                        "process_datetime" => $convert_date_time,
                        "status" => "Pending",
                        "report_path" => $report_path,
                    );
                    if(strtotime($convert_date_time) < strtotime(date('Y-m-d H:i'))) {
                    	$ins_params['created_at'] = $convert_date_time;
                    }
                    if($generate_via == "Email"){
                        $ins_params['email'] = $getScheduled['email'];
                        $ins_params['password'] = $getScheduled['password'];
                    }
                    $sel_schedule_req = "SELECT * FROM $REPORT_DB.export_requests WHERE is_manual='N' AND schedule_id=:schedule_id AND process_datetime=:process_date AND is_deleted='N'";
                    $sel_schedule_paramas = array(":schedule_id" => $getScheduled['id'],":process_date" =>date('Y-m-d H:i:s',strtotime($convert_date_time)));
                    $res_schedule = $pdo->select($sel_schedule_req,$sel_schedule_paramas);
                    if(empty($res_schedule)){
                    	$pdo->insert("$REPORT_DB.export_requests",$ins_params);
                    }
                } 
            // generate schedule request code ends
        }       
    }
}
function next_rps_reports_schedule($schedule_id,$date=''){
    global $pdo,$REPORT_DB;
    $today = date("Y-m-d");
    if($date != ''){
    	$today = date("Y-m-d",strtotime($date));
    }
	$nextDate = true;  
	$nextScheduleDate = ''; 
	$count = 1;
	while($nextDate && $count < 31) {
		$count++;
		$today = date('Y-m-d',strtotime($today . "+1 days"));

 		if(!empty($schedule_id)){
	        $selScheduled = "SELECT * FROM $REPORT_DB.rps_reports_schedule WHERE is_deleted='N' AND id=:id";
	        $getScheduled = $pdo->selectOne($selScheduled,array(":id" => $schedule_id));
	        if(!empty($getScheduled) && is_array($getScheduled)){
	            $schedule_type = $getScheduled['schedule_type'];
	            $schedule_frequency = $getScheduled['schedule_frequency'];
	            $schedule_end_type = $getScheduled['schedule_end_type'];
	            $schedule_end_val = $getScheduled['schedule_end_val'];

	            $process_cnt = $getScheduled['process_cnt'];
	            $last_processed = $getScheduled['last_processed'];
	            $last_process_date = '';
	            $process_datetime = '';

	            // check End Repeat code start
	                if($schedule_end_type == "on_date"){
	                    $end_date = date('Y-m-d',strtotime($schedule_end_val));
	                    if(strtotime($end_date) >= strtotime($today)){
	                        $allow_schedule = 'Y';
	                    }
	                }else if($schedule_end_type == "no_of_times"){
	                    if($process_cnt != '' && $schedule_end_val != ''){
	                        if($process_cnt <= $schedule_end_val){
                             	$allow_schedule = 'Y';    
	                        }
	                    }
	                }else if($schedule_end_type == "never"){
                     	$allow_schedule = 'Y';
	                }
	            // check End Repeat code ends

	            // check if reports scheduled today code start
	                if(!empty($allow_schedule) && $allow_schedule == 'Y'){
	                    if($last_processed != "" && $last_processed != "0000-00-00" && $last_processed != "1970-01-01"){
	                        $last_process_date = date("Y-m-d",strtotime($last_processed));
	                    }
	                    // check for daily report code start
	                        if($schedule_type == "daily"){
	                            if($last_process_date != ''){
	                                $process_datetime = date("Y-m-d",strtotime("$last_process_date +$schedule_frequency days"));
	                            }else{
	                                $process_datetime = date("Y-m-d",strtotime($today));
	                            }
	                        }
	                    // check for daily report code ends
	                
	                    // check for weekly report code start
	                        if($schedule_type == "weekly"){
	                            $days_of_week = '';
	                            $days_of_week = $getScheduled['days_of_week'];
	                            $days_of_week_arr = ($days_of_week != '') ? explode(",",$days_of_week) : array();
	                            if($last_process_date != ''){
	                                $last_processedWeek = '';
	                                $last_processedWeek = strtotime("$last_process_date +$schedule_frequency week");
	                                $start_week = strtotime("last monday",$last_processedWeek);
	                                $start_week = date("Y-m-d",$start_week);
	                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
	                                    foreach ($days_of_week_arr as $day) {
	                                        $check_week_date = '';
	                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
	                                       
	                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
	                                             $process_datetime = $check_week_date;
	                                        }
	                                    }
	                                }
	                            }else{
                        	 		$last_processedWeek = strtotime("$today week");
	                                $start_week = date("Y-m-d",strtotime("last monday $last_processedWeek"));
	                                if(!empty($days_of_week_arr) && is_array($days_of_week_arr)){
	                                    foreach ($days_of_week_arr as $day) {
	                                        $check_week_date = '';
	                                        $check_week_date = date("Y-m-d",strtotime("$start_week this $day"));
	                                        if($check_week_date != '' && (strtotime($today) == strtotime($check_week_date))){
	                                             $process_datetime = $check_week_date;
	                                        }
	                                    }
	                                }
	                            }
	                        }
	                    // check for weekly report code ends
	            
	                    // check for monthly report code start
	                        if($schedule_type == "monthly"){
	                            $month_option = '';
	                            $month_option = $getScheduled['month_option'];
	                            if($month_option != '' && $month_option == "days_of_month"){
	                                $days_of_month = '';
	                                $days_of_month = $getScheduled['days_of_month'];
	                                $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
	                                $month_day = date("d",strtotime($today));
	                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
	                                     $process_datetime = date('Y-m-d',strtotime($today));
	                                }
	                            }else if($month_option != '' && $month_option == "on_the_day"){
	                                $day_type = '';
	                                $selected_day = '';
	                                $day_type = $getScheduled['day_type'];
	                                $selected_day = $getScheduled['selected_day'];
	                                if($day_type != '' && $selected_day != ''){
	                                    $specific_day = $day_type.' '.$selected_day;
	                                    $process_datetime = date('Y-m-d',strtotime("$specific_day of this month"));
	                                }
	                            }
	                        }
	                    // check for monthly report code ends 

	                    // check for yearly report code start
	                        if($schedule_type == "yearly"){
	                            $months = '';
	                            $months = $getScheduled['months'];
	                            $months_arr = ($months != '') ? explode(",",$months) : array();
	                              $get_month = date("M",strtotime($today));
	                            if(is_array($months_arr)){
	                                $month_option = '';
	                                $month_option = $getScheduled['month_option'];
	                                if($month_option != '' && $month_option == "days_of_month"){
	                                    $days_of_month = '';
	                                    $days_of_month = $getScheduled['days_of_month'];
	                                    $days_of_month_arr = ($days_of_month != '') ? explode(",",$days_of_month) : array();
	                                      $month_day = date("d",strtotime($today));
		                                if(is_array($days_of_month_arr) && in_array($month_day,$days_of_month_arr)){
		                                     $process_datetime = date('Y-m-d',strtotime($today));
		                                }else{
		                                	foreach ($months_arr as $key => $value) {

		                                		$date = $days_of_month_arr[0] . " " .$value ." ". date("Y",strtotime('+ 1 year',strtotime($today)));
		                                		$process_datetime = date('Y-m-d',strtotime($date));

		                                		if(strtotime($today) <= strtotime($process_datetime)){
		                                			break;
		                                		}
		                                		
		                                	}
		                                }
	                                }else if($month_option != '' && $month_option == "on_the_day"){
	                                    $day_type = '';
	                                    $selected_day = '';
	                                    $day_type = $getScheduled['day_type'];
	                                    $selected_day = $getScheduled['selected_day'];
	                                    if($day_type != '' && $selected_day != ''){
	                                        $specific_day = $day_type.' '.$selected_day;
	                                        $process_datetime = date('Y-m-d',strtotime("$specific_day of this month"));
	                                        $process_datetime = date('Y-m-d',strtotime('+ 1 year',strtotime($process_datetime)));
	                                    }
	                                }
	                            }
	                        }
	                    // check for yearly report code ends 
	    	
	                    if(strtotime($process_datetime) >= strtotime($today)){
	                        $nextDate = false;
	                      	$date_time = date('Y-m-d H:i',strtotime($process_datetime.''.$getScheduled['time']));
	                        return $date_time;
	                    }
	                }
	            // check if reports scheduled today code ends
	        }       
		}
	} 
    return $nextScheduleDate;
}
function get_report_filter_fields($report_key = "",$location = "")
{
	$res = array();
	if(in_array($report_key ,array("admin_export","agent_export","agent_license","agent_merchant_assignment","agent_eo_coverage","member_verifications","member_age_out","life_insurance_beneficiaries"))) {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Added Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
		
	} elseif($report_key == "agent_quick_sales_summary") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Transaction Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif($report_key == "agent_new_business_post_payments") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Post-Payment Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif($report_key == "agent_declines_summary") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Transaction Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif($report_key == "agent_p2p_comparison") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Transaction Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif($report_key == "admin_member_persistency" || $report_key == "agent_member_persistency" || $report_key ==  "agent_product_persistency") {
		if($location == "schedule_popup") {
			$res['added_or_effective_date'] = array(
				'name' => 'added_or_effective_date',
				'label' => '',
				'is_reuired' => true,
				'input_type' => 'radio',
				'field_key' => 'added_or_effective_date',
				'error_msg' => 'Please select an option',
			);
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif($report_key == "agent_debit_balance" || $report_key == "agent_debit_ledger") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'as_of_date'
			);
		} else {

		}
	} elseif($report_key == "daily_order_summary") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'as_of_date_prior'
			);
		} else {

		}
	} elseif($report_key == "admin_payment_p2p_renewal_comparison") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Transaction Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'as_of_date_prior_month'
			);
		} else {

		}
	} elseif($report_key == "admin_next_billing_date") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Next Billing Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif($report_key == "admin_new_business_post_payments_org") {
		if($location == "schedule_popup") {
			$res['added_or_post_payment_date'] = array(
				'name' => 'added_or_post_payment_date',
				'label' => '',
				'is_reuired' => true,
				'input_type' => 'radio',
				'field_key' => 'added_or_post_payment_date',
				'error_msg' => 'Please select an option',
			);
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif($report_key == "admin_payment_outstanding_renewals") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Next Billing Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	} elseif(in_array($report_key,array('admin_payment_transaction_report','admin_payment_failed_payment_recapture_analytics','admin_payment_reversal_transactions'))) {
		if($location == "schedule_popup") {
			$res['transaction_or_effective_date'] = array(
				'name' => 'transaction_or_effective_date',
				'label' => '',
				'is_reuired' => true,
				'input_type' => 'radio',
				'field_key' => 'transaction_or_effective_date',
				'error_msg' => 'Please select an option',
			);
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	}else if($report_key == "member_summary") {
		if($location == "schedule_popup") {
			$res['report_type'] = array(
				'name' => 'report_type',
				'label' => 'Report Type',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'report_type'
			);
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Added Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
		
	}else if(in_array($report_key,array("agent_interactions","member_interactions"))) {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Added Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
			$res['user_type'] = array(
				'name' => 'user_type',
				'input_type' => 'hidden',
				'is_reuired' => false,
				'field_key' => 'user_type'
			);
		} else {

		}
		
	}else if(in_array($report_key,array("member_product_cancellations"))) {
		if($location == "schedule_popup") {
			$res['termination_or_terminated_date'] = array(
				'name' => 'termination_or_terminated_date',
				'label' => '',
				'is_reuired' => true,
				'input_type' => 'radio',
				'field_key' => 'termination_or_terminated_date',
				'error_msg' => 'Please select an option',
			);
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
		} else {

		}
	}elseif($report_key == "payment_nb_sales" || $report_key == "payment_rb_sales") {
		if($location == "schedule_popup") {
			$res['added_date'] = array(
				'name' => 'added_date',
				'label' => 'Date',
				'is_reuired' => true,
				'input_type' => 'select',
				'field_key' => 'added_date'
			);
			$res['sales_report_type'] = array(
				'name' => 'report_type',
				'input_type' => 'hidden',
				'is_reuired' => false,
				'field_key' => 'sales_report_type'
			);
		} else {

		}
	}
	return $res;
}
function get_graded_comm_id() {
	global $pdo;
	$graded_commission_id = rand(1000, 9999);
	$sql = "SELECT display_id FROM prd_fees WHERE display_id = 'GR" . $graded_commission_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res) {
		return get_graded_comm_id();
	} else {
		return "GR" . $graded_commission_id;
	}
}

function get_graded_comm_fee_id() {
	global $pdo;
	$graded_commission_id = rand(100000, 999999);
	$sql = "SELECT product_code FROM prd_main WHERE product_code = 'F" . $graded_commission_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res) {
		return get_graded_comm_fee_id();
	} else {
		return "F" . $graded_commission_id;
	}
}
function key_to_display_value($key = '')
{
	return ucwords(str_replace('_',' ',$key));
}
function send_e_ticket_mail_to_assigne($ticket_id = 0)
{
	global $pdo,$ADMIN_HOST;
	$ticket_row = $pdo->selectOne("SELECT * from s_ticket where id=:id",array(":id"=>$ticket_id));
	if(!empty($ticket_row) && !empty($ticket_row['assigned_admin_id'])) {
		$admin_row = $pdo->selectOne('SELECT * FROM admin WHERE id=:id',array(":id"=>$ticket_row['assigned_admin_id']));
		if(!empty($admin_row)) {
			$trigger_id = 5;
			$mail_data = array();
	        $mail_data['fname'] = $admin_row['fname'];
	        $mail_data['link'] = $ADMIN_HOST;
	        $mail_data['TKT_ID'] = $ticket_row['tracking_id'];

	        $smart_tags = get_user_smart_tags($admin_row['id'],'admin');
                
            if($smart_tags){
                $mail_data = array_merge($mail_data,$smart_tags);
            }
	        
	        trigger_mail($trigger_id,$mail_data,$admin_row['email']);		
		}
	}
}
//$user_id = MD5
function get_user_data_for_af($user_id,$user_type)
{
	global $pdo,$ADMIN_HOST;

	if($user_type == 'Admin') {
        $user_row = $pdo->selectOne("SELECT id,fname,lname,display_id as rep_id FROM admin WHERE md5(id)=:id",array(":id"=>$user_id));
        $profile_page_url = $ADMIN_HOST.'/admin_profile.php?id='.$user_id;
		$timezone = isset($_SESSION['admin']['timezone'])?$_SESSION['admin']['timezone']:'UTC';

    } else {
        $user_row = $pdo->selectOne("SELECT id,fname,lname,rep_id FROM customer WHERE md5(id)=:id",array(":id"=>$user_id));

        if($user_type == 'Customer'){
            $profile_page_url = $ADMIN_HOST.'/members_details.php?id='.$user_id;
            $timezone = isset($_SESSION['customer']['timezone'])?$_SESSION['customer']['timezone']:'UTC';

        } elseif ($user_type == 'Group'){
            $profile_page_url = $ADMIN_HOST.'/groups_details.php?id='.$user_id;
            $timezone = isset($_SESSION['groups']['timezone'])?$_SESSION['groups']['timezone']:'UTC';

        } elseif ($user_type == 'Agent'){
            $profile_page_url = $ADMIN_HOST.'/agent_detail_v1.php?id='.$user_id;
            $timezone = isset($_SESSION['agents']['timezone'])?$_SESSION['agents']['timezone']:'UTC';
        }
    }

    if(!empty($user_row)) {
    	return array(
    		'user_id' => $user_row['id'],
    		'user_type' => $user_type,
    		'fname' => $user_row['fname'],
    		'lname' => $user_row['lname'],
    		'rep_id' => $user_row['rep_id'],
    		'profile_page_url' => $profile_page_url,
    		'timezone' => $timezone,
    	);
    } else {
    	return array();
    }
}
function get_pmpm_comm_fee_id() {
	global $pdo;
	$pmpm_commission_id = rand(100000, 999999);
	$sql = "SELECT product_code FROM prd_main WHERE product_code = 'F" . $pmpm_commission_id . "'";
	$res = $pdo->selectOne($sql);
	if ($res) {
		return get_pmpm_comm_fee_id();
	} else {
		return "F" . $pmpm_commission_id;
	}
}
function get_user_smart_tags($user_id='',$user_type='',$product_id='',$ws_id=''){
	global $pdo,$ADMIN_HOST,$AGENT_HOST,$GROUP_HOST,$CUSTOMER_HOST,$CREDIT_CARD_ENC_KEY,$HOST;
	$response = array();
	if(!empty($user_type)){
		if(strtolower($user_type) == 'admin'){

			$response['fname'] = '';
			$response['lname'] = '';
			$response['AdminName'] = '';
			$response['AdminID'] = '';
			$response['AdminLevel'] = '';
			$response['AdminEmail'] = '';
			$response['AdminPhone'] = '';
			$response['AdminStatus'] = '';
			$response['AdminPortal'] = '';

			if(!empty($user_id)){
				$admin_info = $pdo->selectOne("SELECT id,display_id,fname,lname,type,email,phone,status FROM admin WHERE id = :id",array(":id" => $user_id));
				if($admin_info){
					$response['fname'] = $admin_info['fname'];
					$response['lname'] = $admin_info['lname'];
					$response['AdminName'] = $admin_info['fname'] . ' '. $admin_info['lname'];
					$response['AdminID'] = $admin_info['display_id'];
					$response['AdminLevel'] = $admin_info['type'];
					$response['AdminEmail'] = $admin_info['email'];
					$response['AdminPhone'] = format_telephone($admin_info['phone']);
					$response['AdminStatus'] = $admin_info['status'];
					$response['AdminPortal'] = "<a href='".$ADMIN_HOST."'>$ADMIN_HOST</a>";
				}
			}

		}else if(strtolower($user_type) == 'agent'){

			$response['fname'] = '';
			$response['lname'] = '';
			$response['AgentName'] = '';
			$response['AgentID'] = '';
			$response['AgentLevel'] = '';
			$response['AgencyName'] = '';
			$response['AgentEmail'] = '';
			$response['AgentPhone'] = '';
			$response['AgentStatus'] = '';
			$response['AgentUsername'] = '';
			$response['AgentEOAmount'] = '';
			$response['AgentEOExpiration'] = '';
			$response['AgentPublicDisplay'] = '';
			$response['ParentAgentName'] = '';
			$response['ParentAgentID'] = '';
			$response['TreeAgentName'] = '';
			$response['TreeAgentID'] = '';
			$response['AgentPortal'] = '';
			$response['AgentActiveProducts'] = '';
			$response['AgentActiveLicense'] = '';
			$response['AgentInactiveLicense'] = '';
			$response['AgentServicesInfo'] = '';
			$response['Date'] = '';
			$response['Name'] = '';
			$response['AgentType'] = '';
			$response['Address'] = '';
			$response['Day'] = '';
			$response['Month'] = '';
			$response['AGENT_ASSOCIATED_COMPANY'] = '';

			if(!empty($user_id)){
				
				$agent_info = $pdo->selectOne("SELECT c.id,c.rep_id,c.fname,c.lname,c.type,c.email,c.cell_phone as phone,c.status,cs.agent_coded_level,cs.company_name,c.business_name,c.user_name,c.public_name,c.public_email,c.public_phone,CONCAT(s.fname,' ',s.lname) as parent_agent_name,s.rep_id as parent_agent_rep_id,cs.account_type,cs.company_city,cs.company_state,cs.company_address,cs.company_address_2,cs.company_zip,c.state,c.city,c.zip,c.address,c.address_2,cs.company FROM customer c 
					JOIN customer_settings cs on(c.id = cs.customer_id)
					JOIN customer s on(s.id = c.sponsor_id) 
					WHERE c.id = :id AND c.type = 'agent'",array(":id" => $user_id));

				if($agent_info){
					$response['fname'] = $agent_info['fname'];
					$response['lname'] = $agent_info['lname'];
					$response['AgentName'] = $agent_info['fname'] . ' '. $agent_info['lname'];
					$response['AgentID'] = $agent_info['rep_id'];
					$response['AgentLevel'] = $agent_info['agent_coded_level'];
					$response['AgencyName'] = $agent_info['company_name'];
					$response['AgentEmail'] = $agent_info['email'];
					$response['AgentPhone'] = format_telephone($agent_info['phone']);
					$response['AgentStatus'] = $agent_info['status'];
					$response['AgentUsername'] = $agent_info['user_name'];
					$response['AgentEOAmount'] = displayAmount(getname('agent_document', $agent_info['id'], 'e_o_amount','agent_id'),2);
					$response['AgentEOExpiration'] = date('m/d/Y',strtotime(getname('agent_document', $agent_info['id'], 'e_o_expiration','agent_id')));
					$response['AgentPublicDisplay'] = $agent_info['public_name'] . ', '. $agent_info['public_email'] . ', ' . format_telephone($agent_info['public_phone']);
					$response['ParentAgentName'] = $agent_info['parent_agent_name'];
					$response['ParentAgentID'] = $agent_info['parent_agent_rep_id'];

					$highest_upline_sponsor = get_highest_upline_agent_detail($agent_info['rep_id']);

					$response['TreeAgentName'] = $highest_upline_sponsor['name'];
					$response['TreeAgentID'] = $highest_upline_sponsor['rep_id'];
					$response['AgentPortal'] = "<a href='".$AGENT_HOST."'>$AGENT_HOST</a>";

					$response['Date'] = date('m/d/Y');
					$response['Day'] = date('j');
					$response['Month'] = date('F');

					$response['AGENT_ASSOCIATED_COMPANY'] = !empty($agent_info['company']) ? $agent_info['company'] : '';

					if($agent_info['account_type'] == 'Business'){
						$response['Name'] = $agent_info['company_name'];
						$response['AgentType'] = 'Agency';

						$response['Address'] = $agent_info['company_address'] . ' '. $agent_info['company_address_2'] .', '. $agent_info['company_city'] . ', ' . $agent_info['company_state'] . ', ' . $agent_info['company_zip'];
					}else{
						$response['Name'] = $agent_info['fname'] . ' '. $agent_info['lname'];
						$response['AgentType'] = 'Agent';

						$response['Address'] = $agent_info['address'] . ' '. $agent_info['address_2'] .', '. $agent_info['city'] . ', ' . $agent_info['state'] . ', ' . $agent_info['zip'];
					}

					$agent_active_products = array();
					$agent_product_incr = '';
					if(!empty($product_id)){
						$agent_product_incr = " AND apr.product_id IN( $product_id )";
					}
					$agent_products = $pdo->select("SELECT p.name,apr.status from prd_main p JOIN agent_product_rule apr on(p.id = apr.product_id) WHERE apr.agent_id = :agent_id AND apr.status = 'Contracted' $agent_product_incr AND p.type !='Fees' AND apr.is_deleted='N'",array(":agent_id" => $agent_info['id']));
					if($agent_products){
						foreach ($agent_products as $key => $value) {
							array_push($agent_active_products, $value['name']);
						}
					}

					$response['AgentActiveProducts'] = $agent_active_products ? implode(', ', $agent_active_products) : '';

					$agent_active_license = array();
					$agent_inactive_license = array();
					$agent_licenses = $pdo->select("SELECT selling_licensed_state,DATE_FORMAT(license_active_date,'%m/%d/%Y') as license_active_date,DATE_FORMAT(license_exp_date,'%m/%d/%Y') as license_exp_date,license_type,license_auth,license_status from agent_license WHERE agent_id = :agent_id AND is_deleted='N' order by selling_licensed_state",array(":agent_id" => $agent_info['id']));
					if($agent_licenses){
						foreach ($agent_licenses as $key => $value) {
							if($value['license_status'] == 'Active'){
								unset($value['license_status']);
								$agent_active_license[] =  $value;
							}else{
								unset($value['license_status']);
								$agent_inactive_license[] =  $value;
							}
						}
					}

					$response['AgentActiveLicense'] = $agent_active_license ? build_table($agent_active_license) : "";
					// pre_print($agent_inactive_license);
					$response['AgentInactiveLicense'] = $agent_inactive_license ? build_table($agent_inactive_license) : "";

					$setting_keys = array(
					          'agent_services_email',
					          'agent_services_cell_phone',
					        );

					$app_setting_res = get_app_settings($setting_keys);

					$response['AgentServicesInfo'] = "Agent Services - <a href=mailto:" . $app_setting_res['agent_services_email'] . ">" . $app_setting_res['agent_services_email'] . "</a> - <a href='tel:".$app_setting_res['agent_services_cell_phone']."'>" . format_telephone($app_setting_res['agent_services_cell_phone']) . "</a>";



				}
			}

		}else if(strtolower($user_type) == 'group'){

			$response['GroupName'] = '';
			$response['GroupID'] = '';
			$response['GroupContactName'] = '';
			$response['GroupContactEmail'] = '';
			$response['GroupContactPhone'] = '';
			$response['GroupStatus'] = '';
			$response['GroupUsername'] = '';
			$response['GroupPublicDisplay'] = '';
			$response['ParentAgentName'] = '';
			$response['ParentAgentID'] = '';
			$response['TreeAgentName'] = '';
			$response['TreeAgentID'] = '';
			$response['GroupPortal'] = '';
			$response['GroupActiveProducts'] = '';
			$response['GroupServicesInfo'] = '';

			if(!empty($user_id)){
				
				$group_info = $pdo->selectOne("SELECT c.id,c.rep_id,c.fname,c.lname,c.type,c.email,c.cell_phone as phone,c.status,cs.agent_coded_level,cs.company_name,c.business_name,c.user_name,c.public_name,c.public_email,c.public_phone,CONCAT(s.fname,' ',s.lname) as parent_agent_name,s.rep_id as parent_agent_rep_id FROM customer c 
					JOIN customer_settings cs on(c.id = cs.customer_id)
					JOIN customer s on(s.id = c.sponsor_id) 
					WHERE c.id = :id",array(":id" => $user_id));

				if($group_info){
					$response['GroupName'] = $group_info['business_name'];
					$response['GroupID'] = $group_info['rep_id'];
					$response['GroupContactName'] = $group_info['fname'] .' '.$group_info['lname'];
					$response['GroupContactEmail'] = $group_info['email'];
					$response['GroupContactPhone'] = format_telephone($group_info['phone']);
					$response['GroupStatus'] = $group_info['status'];
					$response['GroupUsername'] = $group_info['user_name'];
					$response['GroupPublicDisplay'] = $group_info['public_name'] . ', '. $group_info['public_email'] . ', ' . format_telephone($group_info['public_phone']);
					$response['ParentAgentName'] = $group_info['parent_agent_name'];
					$response['ParentAgentID'] = $group_info['parent_agent_rep_id'];

					$highest_upline_sponsor = get_highest_upline_agent_detail($group_info['rep_id']);

					$response['TreeAgentName'] = $highest_upline_sponsor['name'];
					$response['TreeAgentID'] = $highest_upline_sponsor['rep_id'];
					$response['GroupPortal'] = "<a href='".$GROUP_HOST."'>$GROUP_HOST</a>";

					$group_active_products = array();

					$group_products = $pdo->select("SELECT p.name,apr.status from prd_main p JOIN agent_product_rule apr on(p.id = apr.product_id) WHERE apr.agent_id = :agent_id AND apr.status = 'Contracted'",array(":agent_id" => $group_info['id']));
					if($group_products){
						foreach ($group_products as $key => $value) {
							array_push($group_active_products, $value['name']);
						}
					}

					$response['GroupActiveProducts'] = $group_active_products ? implode(', ', $group_active_products) : '';

					$setting_keys = array(
					          'group_services_email',
					          'group_services_cell_phone',
							  'enrollment_display_name',
					        );
					$app_setting_res = get_app_settings($setting_keys);

					$response['GroupServicesInfo'] = $app_setting_res['enrollment_display_name'] . " - <a href=mailto:" . $app_setting_res['group_services_email'] . ">" . $app_setting_res['group_services_email'] . "</a> - <a href='tel:".$app_setting_res['group_services_cell_phone']."'>" . format_telephone($app_setting_res['group_services_cell_phone']) . "</a>";



				}
			}

		}else if(strtolower($user_type) == 'member'){

			$response['fname'] = '';
			$response['lname'] = '';
			$response['MemberName'] = '';
			$response['MemberID'] = '';
			$response['MemberEmail'] = '';
			$response['MemberPhone'] = '';
			$response['MemberCity'] = '';
			$response['MemberState'] = '';
			$response['MemberZipCode'] = '';
			$response['MemberStatus'] = '';
			$response['MemberAddress'] = '';
			$response['ParentAgentName'] = '';
			$response['ParentAgentID'] = '';
			$response['TreeAgentName'] = '';
			$response['TreeAgentID'] = '';
			$response['MemberPortal'] = '';
			$response['MemberActiveProducts'] = '';
			$response['MemberFullActiveProducts'] = '';
			$response['MemberServicesInfo'] = '';
			$response['ProductName'] = '';
			$response['ProductDetails'] = '';
			$response['RetailPrice'] = '';
			$response['HealthyStepFee'] = '';
			$response['ServiceFee'] = '';
			$response['BenefitTier'] = '';
			$response['EffectiveDate'] = '';
			$response['EffectiveDate+OneYear'] = '';
			$response['DependentName'] = '';
			$response['GroupCode'] = '';
			$response['PlanCode1'] = '';
			$response['PlanCode2'] = '';
			$response['PlanCode3'] = '';
			$response['PlanCode4'] = '';
			$response['PlanCode5'] = '';
			$response['PlanCode6'] = '';
			$response['PlanCode7'] = '';
			$response['PlanCode8'] = '';
			$response['PlanCode9'] = '';
			$response['PlanCode10'] = '';
			$response['PlanCode11'] = '';
			$response['PlanCode12'] = '';
			$response['PlanCode13'] = '';
			$response['PlanCode14'] = '';
			$response['PlanCode15'] = '';
			$response['PlanCode16'] = '';
			$response['PlanCode17'] = '';
			$response['PlanCode18'] = '';
			$response['PlanCode19'] = '';
			$response['PlanCode20'] = '';
			$response['EnrollingAgentDisplayName'] = '';
			$response['EnrollingAgentDisplayEmail'] = '';
			$response['EnrollingAgentDisplayPhone'] = '';
			$response['AgentName'] = '';
			$response['AgentID'] = '';
			$response['AgentEmail'] = '';
			$response['AgentPhone'] = '';
			$response['TempPassword'] = '';
			$response['Date'] = date('m/d/Y');
			$response['NextBillingDate'] = '';
			$response['NextBillAmount'] = '';
			$response['AGENT_ASSOCIATED_COMPANY'] = '';
			$response['PolicyID'] = '';

			if(!empty($user_id)){
				
				$member_info = $pdo->selectOne("SELECT c.id,c.rep_id,c.fname,c.lname,c.type,c.email,c.city,c.state,c.zip,c.cell_phone as phone,c.status,cs.agent_coded_level,cs.company_name,c.business_name,c.user_name,s.public_name,s.public_email,s.public_phone,CONCAT(s.fname,' ',s.lname) as parent_agent_name,s.rep_id as parent_agent_rep_id,CONCAT(c.address,' ',c.address_2) as full_address,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as TempPassword,DATE(cs.signature_date) as signatureDate,scs.company,scs.display_in_member as parentAgentSetting,s.cell_phone as parent_agent_phone,s.email as parent_agent_email
					FROM customer c
					JOIN customer_settings cs on(c.id = cs.customer_id)
					JOIN customer s on(s.id = c.sponsor_id) 
					JOIN customer_settings scs on(s.id = scs.customer_id)
					WHERE c.id = :id",array(":id" => $user_id));

				if($member_info){
					$response['fname'] = $member_info['fname'];
					$response['lname'] = $member_info['lname'];
					$response['MemberName'] = $member_info['fname'] . ' ' . $member_info['lname'];
					$response['MemberID'] = $member_info['rep_id'];
					$response['MemberEmail'] = $member_info['email'];
					$response['MemberPhone'] = format_telephone($member_info['phone']);
					$response['MemberCity'] = $member_info['city'];
					$response['MemberState'] = $member_info['state'];
					$response['MemberZipCode'] = $member_info['zip'];
					$response['MemberStatus'] = $member_info['status'];
					$response['MemberAddress'] = $member_info['full_address'];
					$response['ParentAgentName'] = $member_info['parent_agent_name'];
					$response['ParentAgentID'] = $member_info['parent_agent_rep_id'];
					$response['EnrollingAgentDisplayName'] = $member_info['public_name'];
					$response['EnrollingAgentDisplayEmail'] = $member_info['public_email'];
					$response['EnrollingAgentDisplayPhone'] = $member_info['public_phone'];

					$response['AgentID'] = $member_info['parent_agent_rep_id'];

					if($member_info['parentAgentSetting'] == 'Y'){
						$response['AgentName'] = $member_info['parent_agent_name'];
						$response['AgentEmail'] = $member_info['parent_agent_email'];
						$response['AgentPhone'] = format_telephone($member_info['parent_agent_phone']);
					}else{
						$response['AgentName'] = $member_info['public_name'];
						$response['AgentEmail'] = $member_info['public_email'];
						$response['AgentPhone'] = format_telephone($member_info['public_phone']);
					}
					
					$response['AGENT_ASSOCIATED_COMPANY'] = !empty($member_info['company']) ? $member_info['company'] : '';

					$highest_upline_sponsor = get_highest_upline_agent_detail($member_info['rep_id']);

					$response['TreeAgentName'] = $highest_upline_sponsor['name'];
					$response['TreeAgentID'] = $highest_upline_sponsor['rep_id'];
					$response['MemberPortal'] = "<a href='".$CUSTOMER_HOST."'>$CUSTOMER_HOST</a>";
					$response['TempPassword'] = $member_info['TempPassword'];
					$response['Date'] = !empty($member_info['signatureDate']) ? displayDate($member_info['signatureDate']) : date('m/d/Y');

					$member_active_products = array();
					$member_active_full_products = array();
					$nextBillDate = '';
					$nextBillingAmt = 0;

					$member_products_sql = "SELECT DATE_FORMAT(w.created_at,'%m/%d/%Y') as added_date,w.website_id as 'Policy #',p.name,ppt.title as benefit_tier,DATE_FORMAT(w.eligibility_date,'%m/%d/%Y') as effective_date,DATE_FORMAT(w.termination_date,'%m/%d/%Y') as termination_date,DATE_FORMAT(w.next_purchase_date,'%m/%d/%Y') as next_billing_date, w.price, w.status 
																FROM website_subscriptions w 
																JOIN prd_main p on(p.id = w.product_id)
																JOIN prd_matrix pm on(pm.id = w.plan_id) 
																JOIN prd_plan_type ppt on(ppt.id = pm.plan_type) 
																WHERE w.customer_id = :customer_id AND w.status = 'Active'";
					$member_products = $pdo->select($member_products_sql,[':customer_id' => $member_info['id']]);

					if($member_products){
						foreach ($member_products as $key => $value) {
							array_push($member_active_products, $value['name']);
							$member_active_full_products[$key] = $value;
							$nextBillingAmt += $value['price'];
							$nextBillDate = $value['next_billing_date'];
						}
					}

					$response['MemberActiveProducts'] = $member_active_products ? implode(', ', $member_active_products) : '';

					$response['MemberFullActiveProducts'] = $member_active_full_products ? build_table($member_active_full_products) : "";
					$response['NextBillingDate'] = date('m/d/Y',strtotime($nextBillDate));
					$response['NextBillAmount'] = displayAmount($nextBillingAmt,2);

					$setting_keys = array(
					          'member_services_email',
					          'member_services_cell_phone',
					        );
					$app_setting_res = get_app_settings($setting_keys);

					$response['MemberServicesInfo'] = "Member Services - <a href=mailto:" . $app_setting_res['member_services_email'] . ">" . $app_setting_res['member_services_email'] . "</a> - <a href='tel:".$app_setting_res['member_services_cell_phone']."'>" . format_telephone($app_setting_res['member_services_cell_phone']) . "</a>";

					if(!empty($product_id)){
						if(!empty($ws_id)) {
							$product_info = $pdo->selectOne("SELECT w.id as ws_id,w.created_at,w.website_id,p.name,ppt.title,DATE_FORMAT(w.eligibility_date,'%m/%d/%Y') as eligibility_date,DATE_FORMAT(w.termination_date,'%m/%d/%Y') as termination_date,DATE_FORMAT(w.next_purchase_date,'%m/%d/%Y') as next_purchase_date, w.price, w.status,w.prd_plan_type_id 
								from website_subscriptions w 
								JOIN prd_main p on(p.id = w.product_id)
								JOIN prd_plan_type ppt on(ppt.id = w.prd_plan_type_id) 
								WHERE w.id=:id",array(':id' => $ws_id));

							if($product_info){
								$response['ProductName'] = $product_info['name'];
								$response['ProductDetails'] = 'Product : ' . $product_info['name'] . ', Benefit Tier/Enrollee : ' . $product_info['title'] . ', Retail Price : ' . displayAmount($product_info['price'],2) . ', Effective Date : ' . $product_info['eligibility_date'];
								$response['RetailPrice'] = displayAmount($product_info['price'],2);
								$response['NextBillingDate'] = date('m/d/Y',strtotime($product_info['next_purchase_date']));
								$response['NextBillAmount'] = displayAmount($product_info['price'],2);
								$response['PolicyID'] = $product_info['website_id'];

								$check_fees = $pdo->select("SELECT p.product_code,p.product_type
									FROM website_subscriptions w 
									JOIN website_subscriptions ws on(w.product_id = ws.fee_applied_for_product)
									JOIN prd_main p on(ws.product_id = p.id)
									WHERE w.id=:id AND p.product_type in('Healthy Step','ServiceFee')",array(':id' => $ws_id));
								if($check_fees){
									foreach ($check_fees as $key => $value) {
										if($value['product_type'] = 'Healthy Step'){
											$response['HealthyStepFee'] = $value['product_code'];
										}else if($value['product_type'] = 'ServiceFee'){
											$response['ServiceFee'] = $value['product_code'];
										}
									}
								}

								$plan_codes = $pdo->select("SELECT code_no,plan_code_value FROM prd_plan_code WHERE product_id = :product_id AND is_deleted = 'N' order by code_no",array(':product_id' => $product_id));
								if($plan_codes){
									foreach ($plan_codes as $key => $plan_code) {
										if($plan_code['code_no'] == 'GC'){
											$response['GroupCode'] = $plan_code['plan_code_value'];
										}else{
											$response['PlanCode' . $key] = $plan_code['plan_code_value'];
										}
									}
								}
								$response['BenefitTier'] = $product_info['title'];
								$response['EffectiveDate'] = $product_info['eligibility_date'];
								$response['EffectiveDate+OneYear'] = date('m/d/Y', strtotime('+1 year', strtotime($product_info['eligibility_date'])) );

								if($product_info['prd_plan_type_id'] > 1){
									$dependents = $pdo->select("SELECT CONCAT(cp.fname,' ',cp.lname) as name from customer_dependent cd JOIN customer_dependent_profile cp on(cp.id = cd.cd_profile_id) WHERE cd.website_id = :website_id AND cd.is_deleted = 'N'",array(':website_id' => $product_info['ws_id']));
									if($dependents){
										$i = 0;
										$count = count($dependents);
										$dependents_str = "";
										foreach ($dependents as $key => $dependent) {
											$i++;
											$dependents_str .= $dependent['name'];
											if($i < $count){
												$dependents_str .= ', ';
											}
										}
										$response['DependentName'] = $dependents_str;
									}
								}

							}
						} else {

							$product_info = $pdo->selectOne("SELECT w.id as ws_id,w.created_at,w.website_id,p.name,ppt.title,DATE_FORMAT(w.eligibility_date,'%m/%d/%Y') as eligibility_date,DATE_FORMAT(w.termination_date,'%m/%d/%Y') as termination_date,DATE_FORMAT(w.next_purchase_date,'%m/%d/%Y') as next_purchase_date, w.price, w.status,w.prd_plan_type_id 
								from website_subscriptions w 
								JOIN prd_main p on(p.id = w.product_id)
								JOIN prd_plan_type ppt on(ppt.id = w.prd_plan_type_id) 
								WHERE w.customer_id = :customer_id AND w.product_id = :product_id",array(":customer_id" => $member_info['id'],':product_id' => $product_id));

							if($product_info){
								$response['ProductName'] = $product_info['name'];
								$response['ProductDetails'] = 'Product : ' . $product_info['name'] . ', Benefit Tier/Enrollee : ' . $product_info['title'] . ', Retail Price : ' . displayAmount($product_info['price'],2) . ', Effective Date : ' . $product_info['eligibility_date'];
								$response['RetailPrice'] = displayAmount($product_info['price'],2);
								$response['NextBillingDate'] = date('m/d/Y',strtotime($product_info['next_purchase_date']));
								$response['NextBillAmount'] = displayAmount($product_info['price'],2);
								$response['PolicyID'] = $product_info['website_id'];

								$check_fees = $pdo->select("SELECT p.product_code,p.product_type
									FROM website_subscriptions w 
									JOIN website_subscriptions ws on(w.product_id = ws.fee_applied_for_product)
									JOIN prd_main p on(ws.product_id = p.id)
									WHERE w.customer_id = :customer_id AND w.product_id = :product_id AND p.product_type in('Healthy Step','ServiceFee')",array(':customer_id' => $user_id,':product_id' => $product_id));
								if($check_fees){
									foreach ($check_fees as $key => $value) {
										if($value['product_type'] = 'Healthy Step'){
											$response['HealthyStepFee'] = $value['product_code'];
										}else if($value['product_type'] = 'ServiceFee'){
											$response['ServiceFee'] = $value['product_code'];
										}
									}
								}

								$plan_codes = $pdo->select("SELECT code_no,plan_code_value FROM prd_plan_code WHERE product_id = :product_id AND is_deleted = 'N' order by code_no",array(':product_id' => $product_id));
								if($plan_codes){
									foreach ($plan_codes as $key => $plan_code) {
										if($plan_code['code_no'] == 'GC'){
											$response['GroupCode'] = $plan_code['plan_code_value'];
										}else{
											$response['PlanCode' . $key] = $plan_code['plan_code_value'];
										}
									}
								}
								$response['BenefitTier'] = $product_info['title'];
								$response['EffectiveDate'] = $product_info['eligibility_date'];
								$response['EffectiveDate+OneYear'] = date('m/d/Y', strtotime('+1 year', strtotime($product_info['eligibility_date'])) );

								if($product_info['prd_plan_type_id'] > 1){
									$dependents = $pdo->select("SELECT CONCAT(cp.fname,' ',cp.lname) as name from customer_dependent cd JOIN customer_dependent_profile cp on(cp.id = cd.cd_profile_id) WHERE cd.website_id = :website_id AND cd.is_deleted = 'N'",array(':website_id' => $product_info['ws_id']));
									if($dependents){
										$i = 0;
										$count = count($dependents);
										$dependents_str = "";
										foreach ($dependents as $key => $dependent) {
											$i++;
											$dependents_str .= $dependent['name'];
											if($i < $count){
												$dependents_str .= ', ';
											}
										}
										$response['DependentName'] = $dependents_str;
									}
								}

							}
						}
					}

				}
			}

		}else if(strtolower($user_type) == 'lead'){

			$response['fname'] = '';
			$response['lname'] = '';
			$response['LeadName'] = '';
			$response['LeadCompanyName'] = '';
			$response['LeadID'] = '';
			$response['LeadEmail'] = '';
			$response['LeadPhone'] = '';
			$response['LeadState'] = '';
			$response['LeadStatus'] = '';
			$response['LeadTag'] = '';
			$response['GroupEnrollment'] = '';

			if(!empty($user_id)){

				$leadSel = "SELECT l.id,l.fname,l.lname,CONCAT(l.fname,' ',l.lname) as full_name,l.company_name,l.lead_id,l.email,l.cell_phone,l.state,l.status,l.opt_in_type,l.employee_id,l.sponsor_id,c.user_name 
					FROM leads l
					LEFT JOIN customer c on(c.id = l.sponsor_id AND c.is_deleted='N')
					WHERE l.id = :id";
				$leadParam = array(":id" => $user_id);
				$lead_info = $pdo->selectOne($leadSel,$leadParam);
				if($lead_info){
					$response['fname'] = $lead_info['fname'];
					$response['lname'] = $lead_info['lname'];
					$response['LeadName'] = $lead_info['full_name'];
					$response['LeadCompanyName'] = $lead_info['company_name'];
					$response['LeadID'] = $lead_info['lead_id'];
					$response['LeadEmail'] = $lead_info['email'];
					$response['LeadPhone'] = format_telephone($lead_info['cell_phone']);
					$response['LeadState'] = $lead_info['state'];
					$response['LeadStatus'] = $lead_info['status'];
					$response['LeadTag'] = $lead_info['opt_in_type'];

					$groupEnrolleeIdLink = $HOST."/group_enroll/".$lead_info['user_name']."/".$lead_info['employee_id'];
					$response['GroupEnrollment'] = "<a href='" . $groupEnrolleeIdLink . "' target='_blank'>$groupEnrolleeIdLink</a>";
				}
			}

		}else if(strtolower($user_type) == 'product'){

			$response['ProductName'] = '';
			$response['ProductDetails'] = '';
			$response['HealthyStepFee'] = '';
			$response['ServiceFee'] = '';
			$response['ProductFee'] = '';
			$response['RetailPrice'] = '';
			$response['Date'] = date('m/d/Y');

			if(!empty($user_id)){

				$product_info = $pdo->selectOne("SELECT * FROM prd_main WHERE id = :id",array(":id" => $user_id));
				if($product_info){
					$response['ProductName'] = $product_info['name'];
					$response['ProductDetails'] = "";
					$response['HealthyStepFee'] = "";
					$response['ServiceFee'] = "";
					$response['ProductFee'] = "";
					$response['RetailPrice'] = "";
				}
			}

		}
	}
	return $response;
}
function get_highest_upline_agent_detail($member_id) {
	global $pdo;    
    $upline_sponsors = getname('customer', $member_id, 'upline_sponsors','rep_id');
    $upline_sponsors = trim($upline_sponsors,',');
    $upline_sponsors_arr = explode(',',$upline_sponsors);
    $res = array('rep_id' => '', 'name' => '');
    if(!empty($upline_sponsors_arr)) {
      $hu_agent_id = 0;
      if($upline_sponsors_arr[0] == 1 && !empty($upline_sponsors_arr[1])) {
        $hu_agent_id = $upline_sponsors_arr[1];
      } else {
        $hu_agent_id = $upline_sponsors_arr[0];
      }
    }
    if(!empty($hu_agent_id)) {
      $res = $pdo->selectOne("SELECT rep_id,IF(business_name != '',business_name,CONCAT(fname,' ',lname)) as name FROM customer WHERE id='$hu_agent_id'");
    }
    return $res;
}
function build_table($array){
    // start table
    $html = '<table>';
    // header row
    $html .= '<tr>';
    $heading_array = array('selling_licensed_state' => 'State','license_active_date' => 'Active','license_exp_date' => 'Expiration','license_type' => 'Type','license_auth' => 'LOA');
    $value_array = array('Personal' => 'Agent','Business' => 'Agency','general_lines' => 'General Lines');
    foreach($array[0] as $key=>$value){
    		$key = isset($heading_array[$key]) ? $heading_array[$key] : ucfirst(str_replace('_', " ", $key));
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
    $html .= '</tr>';

    // data rows
    foreach( $array as $key=>$value){
        $html .= '<tr>';
        foreach($value as $key2=>$value2){
            $html .= '<td>' . (isset($value_array[$value2]) ? htmlspecialchars($value_array[$value2]) : htmlspecialchars($value2)) . '</td>';
        }
        $html .= '</tr>';
    }

    // finish table and return it

    $html .= '</table>';
    return $html;
}
function doesStringContainChain($str, $n_chained_expected){
    $chained = 1;

    for($i=1; $i<strlen($str); $i++)
    {
        if($str[$i] == ($str[$i-1] + 1) || ($str[$i] - ($str[$i-1] + 1)) == -10)
        {
            $chained++;
            if($chained >= $n_chained_expected)
                return true;
        }else{
            $chained = 1;
        }
    }
    return false;
}
function get_active_global_products_for_filter($agent_id = 0, $includeAll = false, $includeHealthyStep = false, $isGroup = false,$isContractedProducts = false, $isAllassignedproduct = true)
{
	global $pdo;
	$incr = " AND p.status IN('Active','Suspended') ";//OP29-650 updates include suspended Products
	$fieldIncr = ',c.title';
	$extraIncr = " AND p.type!='Fees'";
	$tableJoin = 'JOIN prd_category c ON (c.id = p.category_id)';
	if($includeAll){
		$incr = "";
	}
	if($includeHealthyStep){
		$fieldIncr = ",IF(p.product_type='Healthy Step','Healthy Step',IF(c.title!='',c.title,'No Category')) as title ";
		$extraIncr = " AND (p.type!='Fees' OR (p.product_type='Healthy Step' AND p.record_type = 'Primary'))";
		$tableJoin = 'LEFT JOIN prd_category c ON (c.id = p.category_id)';
	}
	if($isGroup){
		$incr .= " AND p.product_type='Group Enrollment' ";
		if($isContractedProducts){
			$incr .= " AND ap.status='Contracted' ";
		}
	}
	if(!empty($agent_id)) {
		if(!$isGroup){
			if($isAllassignedproduct){
				$incr .= " AND ap.status='Contracted'";
			}
			$incr .= "AND p.parent_product_id=0 ";
		}
		$fl_productSql="SELECT p.id,p.name,p.product_code,p.type $fieldIncr
						FROM prd_main p 
						JOIN agent_product_rule ap ON ap.product_id=p.id
						$tableJoin
						WHERE  ap.agent_id=:agent_id $extraIncr  AND p.is_deleted='N' $incr
						GROUP BY p.id ORDER BY p.name ASC";
		$fl_productRes = $pdo->selectGroup($fl_productSql,array(":agent_id"=>$agent_id),'title');
	} else {
		if(!$isGroup){
			$incr .= " AND p.parent_product_id=0 ";
		}
		$fl_productSql="SELECT p.id,p.name,p.product_code,p.type $fieldIncr
						FROM prd_main p 
						$tableJoin
						WHERE p.is_deleted='N' $incr $extraIncr
						GROUP BY p.id ORDER BY p.name ASC";
		$fl_productRes = $pdo->selectGroup($fl_productSql,array(),'title');
	}
	if(empty($fl_productRes)) {
		$fl_productRes = array();
	}
	return $fl_productRes;
}
function get_policy_display_status($status = '')
{
	$status_res = $status;
	if(in_array($status, array("Active","On Hold Failed Billing"))) {
		$status_res = "Active";

	} else if(in_array($status, array("Inactive Member Request","Inactive Failed Billing"))) {
		$status_res = "Inactive";

	} else if(in_array($status, array("Pending Payment","Post Payment"))) {
		$status_res = "Pending";
	}
	return $status_res;
}
function get_policy_db_status($status = '')
{
	if(is_array($status)) {
		$status_res = array();
		foreach ($status as $tmp_status) {
			$tmp_res = get_policy_db_status($tmp_status);
			if(!empty($tmp_res)) {
				$status_res = array_merge($status_res,$tmp_res);	
			}		
		}
		return $status_res;
	} else {
		if(strtolower($status) == "active") {
			return array("Active");
		
		} elseif (strtolower($status) == "inactive") {
			return array("Inactive");

		} elseif (strtolower($status) == "pending") {
			return array("Pending","Post Payment");
		}
		return array();
	}
}
function get_policy_termination_reasons()
{
	global $pdo;
	$reasons = $pdo->select("SELECT name,is_qualifies_for_cobra from termination_reason where is_deleted = 'N' ORDER BY name");
	if(empty($reasons)) {
		$reasons = array();
	}
	return $reasons;
}
function display_billing_type($billing_type = '')
{
	$re_billing_type = '';
	if($billing_type == "individual") {
		$re_billing_type = 'Individual';

	} elseif($billing_type == "list_bill") {
		$re_billing_type = 'List Bill';
	
	} elseif($billing_type == "TPA") {
		$re_billing_type = 'TPA Bill';
	}
	return $re_billing_type;
}

function getNBOrderDetails($customerId,$productId,$website_id=0){
	global $pdo;
	$res = array("orderId"=> 0,"orderDate" => "");
	$incrOdr = '';
	$sch_paramsOdr = array();
	$incr = '';
	$sch_params = array();

	$sch_paramsOdr = array(
      ":customerId" => $customerId,
      ":productId" => $productId
  );
	if($website_id > 0){
		$sch_paramsOdr[':website_id'] = $website_id;
		$incrOdr .= "AND ws.id =:website_id";
	}
	$selOdr = "SELECT o.id as odrId,o.created_at as odrDate
			FROM orders o
			JOIN order_details od ON(o.id=od.order_id AND od.is_deleted='N') 
			JOIN website_subscriptions ws ON(ws.id=od.website_id)
			WHERE ws.customer_id=:customerId AND od.product_id=:productId $incrOdr 
			AND (o.is_renewal='N' OR (o.is_renewal='L' AND od.is_renewal='N')) ORDER BY o.id DESC";
	$resOdr = $pdo->selectOne($selOdr,$sch_paramsOdr);
	
	if(!empty($resOdr)){
		 return array(
			"orderId"=> $resOdr["odrId"],
			"orderDate" => $resOdr["odrDate"]
		);
	}else{
		$sch_params = array(
        ":customer_id" => $customerId,
        ":product_id" => $productId
    );
		if($website_id > 0){
			$sch_params[':website_id'] = $website_id;
			$incr .= "AND w.id =:website_id";
		}
		$ws_sel = "SELECT w.id,w.product_id,w.parent_ws_id
			FROM website_subscriptions w
			WHERE w.customer_id =:customer_id AND w.product_id = :product_id $incr
			ORDER BY w.id DESC";
		$ws_res = $pdo->selectOne($ws_sel,$sch_params);
		
		if(!empty($ws_res) && !empty($ws_res['product_id']) && !empty($ws_res['parent_ws_id'])){
			$productRes = getOldProductNBOrderDetails($customerId,$ws_res['product_id'],$ws_res['parent_ws_id']);
					return getNBOrderDetails($customerId,$productRes['product_id'],$productRes['website_id']);

		}else{
			return array("orderId"=> 0,"orderDate" => "");
		}
	}
}
function getGlobalFeeProductRule($product_id,$matrix_id){
	global $pdo;
	$res = array();
	  
	  $productSql = "SELECT p.parent_product_id,
	  				p.pricing_model as variationPricingModel,
	  				gp.id as globalProductId,
	  				gp.pricing_model as globalPricingModel,
	  				pm.plan_type as prdPlanType,
	  				GROUP_CONCAT(CONCAT('''', pm.enrollee_type, '''' )) as enrolleeType,
	  				-- GROUP_CONCAT(pm.enrollee_type) as enrolleeType,
	  				pmc.*
	  				FROM prd_main p
	  				JOIN prd_matrix pm ON(p.id=pm.product_id)
	  				LEFT JOIN prd_matrix_criteria pmc ON(pm.product_id=pmc.product_id AND pm.id=pmc.prd_matrix_id)
	  				LEFT JOIN prd_main gp ON(p.parent_product_id=gp.id)
	  				WHERE p.id=:product_id AND pm.id IN ($matrix_id)";
	  $productParmas = array(":product_id" => $product_id);
	  $productRes = $pdo->selectOne($productSql,$productParmas);

	  if(!empty($productRes) && checkIsset($productRes["globalProductId"]) > 0){
	  	$globalProductId = $productRes["globalProductId"];

	  	if(checkIsset($productRes["variationPricingModel"]) == checkIsset($productRes["globalPricingModel"])){
	  		if(checkIsset($productRes["variationPricingModel"]) == "FixedPrice"){

	  			$selGlobal = "SELECT p.id as product_id,pm.id as matrix_id
	  								FROM prd_main p
	  								JOIN prd_matrix pm ON(p.id=pm.product_id AND pm.is_deleted='N')
	  								WHERE p.is_deleted='N' AND p.id=:product_id AND pm.plan_type=:planType";
	  			$params = array(":product_id" => $globalProductId,":planType" => $productRes["prdPlanType"]);
	  			$resGlobal = $pdo->selectOne($selGlobal,$params);
	  			if(!empty($resGlobal)){
	  				$res["product_id"] = $resGlobal["product_id"];
	  				$res["matrix_id"] = $resGlobal["matrix_id"];
	  			}
	  		}else{
	  			$incr = "";
	  			$sch_params = array();

	  			$incr .= " AND pmc.age_from = :age_from AND pmc.age_to = :age_to 
	  					   AND pmc.state= :state AND pmc.zipcode =:zipcode AND pmc.gender = :gender
	  					   AND pmc.smoking_status = :smoking_status AND pmc.tobacco_status = :tobacco_status";

	  			$incr .= " AND pmc.height_by = :height_by AND pmc.height_feet = :height_feet 
	  					   AND pmc.height_inch = :height_inch AND pmc.height_feet_to = :height_feet_to
	  					   AND pmc.height_inch_to = :height_inch_to AND pmc.weight_by = :weight_by 
	  					   AND pmc.weight = :weight AND pmc.weight_to = :weight_to";

	  			$incr .= " AND pmc.no_of_children_by = :no_of_children_by AND pmc.no_of_children = :no_of_children 
	  					   AND pmc.no_of_children_to = :no_of_children_to AND pmc.has_spouse = :has_spouse
	  					   AND pmc.spouse_age_from = :spouse_age_from AND pmc.spouse_age_to = :spouse_age_to
	  					   AND pmc.spouse_gender = :spouse_gender AND pmc.spouse_smoking_status = :spouse_smoking_status
	  					   AND pmc.spouse_tobacco_status = :spouse_tobacco_status 
	  					   AND pmc.spouse_height_feet = :spouse_height_feet 
	  					   AND pmc.spouse_height_inch = :spouse_height_inch 
	  					   AND pmc.spouse_weight = :spouse_weight
	  					   AND pmc.spouse_weight_type = :spouse_weight_type";

	  			$sch_params[":age_from"] = $productRes["age_from"];
	  			$sch_params[":age_to"] = $productRes["age_to"];
	  			$sch_params[":state"] = $productRes["state"];
	  			$sch_params[":zipcode"] = $productRes["zipcode"];
	  			$sch_params[":gender"] = $productRes["gender"];
	  			$sch_params[":smoking_status"] = $productRes["smoking_status"];
	  			$sch_params[":tobacco_status"] = $productRes["tobacco_status"];

	  			$sch_params[":height_by"] = $productRes["height_by"];
	  			$sch_params[":height_feet"] = $productRes["height_feet"];
	  			$sch_params[":height_inch"] = $productRes["height_inch"];
	  			$sch_params[":height_feet_to"] = $productRes["height_feet_to"];
	  			$sch_params[":height_inch_to"] = $productRes["height_inch_to"];
	  			$sch_params[":weight_by"] = $productRes["weight_by"];
	  			$sch_params[":weight"] = $productRes["weight"];
	  			$sch_params[":weight_to"] = $productRes["weight_to"];

	  			$sch_params[":no_of_children_by"] = $productRes["no_of_children_by"];
	  			$sch_params[":no_of_children"] = $productRes["no_of_children"];
	  			$sch_params[":no_of_children_to"] = $productRes["no_of_children_to"];
	  			$sch_params[":has_spouse"] = $productRes["has_spouse"];
	  			$sch_params[":spouse_age_from"] = $productRes["spouse_age_from"];
	  			$sch_params[":spouse_age_to"] = $productRes["spouse_age_to"];
	  			$sch_params[":spouse_gender"] = $productRes["spouse_gender"];
	  			$sch_params[":spouse_smoking_status"] = $productRes["spouse_smoking_status"];
	  			$sch_params[":spouse_tobacco_status"] = $productRes["spouse_tobacco_status"];
	  			$sch_params[":spouse_height_feet"] = $productRes["spouse_height_feet"];
	  			$sch_params[":spouse_height_inch"] = $productRes["spouse_height_inch"];
	  			$sch_params[":spouse_weight"] = $productRes["spouse_weight"];
	  			$sch_params[":spouse_weight_type"] = $productRes["spouse_weight_type"];

	  			if(checkIsset($productRes["variationPricingModel"]) == "VariablePrice"){
	  				$incr .= " AND p.id=:product_id AND pm.plan_type=:planType";
	  				$sch_params[":product_id"] = $globalProductId;
	  				$sch_params[":planType"] = $productRes["prdPlanType"];

		  			$selGlobal = "SELECT p.id as product_id,pm.id as matrix_id
		  								FROM prd_main p
		  								JOIN prd_matrix pm ON(p.id=pm.product_id AND pm.is_deleted='N')
		  								JOIN prd_matrix_criteria pmc ON(pm.product_id=pmc.product_id AND pm.id=pmc.prd_matrix_id AND pmc.is_deleted='N')
		  								WHERE p.is_deleted='N' $incr";
		  			
		  			$resGlobal = $pdo->selectOne($selGlobal,$sch_params);
		  			if(!empty($resGlobal)){
		  				$res["product_id"] = $resGlobal["product_id"];
		  				$res["matrix_id"] = $resGlobal["matrix_id"];
		  			}
		  		}else if(checkIsset($productRes["variationPricingModel"]) == "VariableEnrollee"){
		  			$incr .= " AND p.id=:product_id AND pm.enrollee_type IN(".$productRes["enrolleeType"].")";
	  				$sch_params[":product_id"] = $globalProductId;

		  			$selGlobal = "SELECT p.id as product_id,GROUP_CONCAT(pm.id) as matrix_ids
		  								FROM prd_main p
		  								JOIN prd_matrix pm ON(p.id=pm.product_id AND pm.is_deleted='N')
		  								JOIN prd_matrix_criteria pmc ON(pm.product_id=pmc.product_id AND pm.id=pmc.prd_matrix_id AND pmc.is_deleted='N')
		  								WHERE p.is_deleted='N' $incr";
		  			
		  			$resGlobal = $pdo->selectOne($selGlobal,$sch_params);
		  	
		  			if(!empty($resGlobal)){
		  				$res["product_id"] = $resGlobal["product_id"];
		  				$res["matrix_id"] = $resGlobal["matrix_ids"];
		  			}
		  		}
	  		}
	  	}else{
	  		return $res;
	  	}
	  }
	return $res;
}
function get_sale_type_by_is_renewal($is_renewal) {
	$sale_type = '';
	if($is_renewal == "N") {
		$sale_type = "New Business";

	} else if($is_renewal == "Y") {
		$sale_type = "Renewal";
	
	} else if($is_renewal == "L") {
		$sale_type = "List Bill";
	}
	return $sale_type;
}
function get_order_summary_table($order_id,$memberId='')
{
	global $pdo,$resOrder,$tblClass,$orderStatus,$subTotal,$grandTotal,$stepFeePrice,$stepFeeRefund,$serviceFeePrice,$serviceFeeRefund,$CobraServiceFee,$CobraServiceFeeRefund,$prdPlanTypeArray;

	if(!empty($order_id)){
		$detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,od.prd_plan_type_id as planTitle,od.unit_price as price,od.is_refund,c.rep_id
        FROM order_details od
        JOIN prd_main pm ON(pm.id=od.product_id)
        LEFT JOIN website_subscriptions ws ON(ws.id = od.website_id)
        LEFT JOIN customer c ON(c.id = ws.customer_id)
        WHERE od.order_id = :odrId AND od.is_deleted='N'
        ORDER BY od.product_name ASC";
		$detRes = $pdo->select($detSql, array(':odrId' => makeSafe($order_id)));
	}else if(!empty($memberId)){
		$detSql = "SELECT pm.type,pm.product_type,pm.name as product_name,ws.start_coverage_period,ws.end_coverage_period,ws.price as price,'' as is_refund,c.rep_id,ws.prd_plan_type_id as planTitle
        FROM customer c
		JOIN website_subscriptions ws ON(ws.customer_id = c.id)
        JOIN prd_main pm ON(pm.id=ws.product_id)
        WHERE c.id = :memberId
        ORDER BY pm.name ASC";
		$detRes = $pdo->select($detSql, array(':memberId' => makeSafe($memberId)));
		$subTotal = $grand_total = 0;
	}
	

	ob_start();
	include_once dirname(__DIR__) . "/tmpl/order_summary_table.inc.php";
	$html_code = ob_get_clean();
	return $html_code;
}
function get_tran_summary_table($transId)
{
	global $pdo,$resTrans,$tblClass,$transStatus,$subTotal,$transTotal,$stepFeePrice,$serviceFeePrice;

	if($resTrans['is_list_bill_order'] == "Y") {
	    $detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,IF(od.is_refund = 'Y',(od.unit_price * -1),od.unit_price) as price,od.is_refund,c.rep_id
	            FROM transactions t
	            JOIN order_details od ON(t.order_id=od.order_id)
	            LEFT JOIN website_subscriptions ws ON(ws.id = od.website_id)
        		LEFT JOIN customer c ON(c.id = ws.customer_id)
	            JOIN prd_main pm ON(pm.id=od.product_id)
	            LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
	            WHERE t.id = :transId
	            GROUP BY od.list_bill_detail_id
	            ORDER BY od.product_name ASC";
	    $detRes = $pdo->select($detSql, array(':transId' => makeSafe($transId)));
	} else {
	    $detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,od.is_refund,od.unit_price as price
	            FROM transactions t
	            JOIN sub_transactions st ON(st.transaction_id=t.id)
	            JOIN order_details od ON(st.order_detail_id = od.id and t.order_id=od.order_id AND od.product_id=st.product_id)
	            JOIN prd_main pm ON(pm.id=od.product_id)
	            LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
	            WHERE t.id = :transId
	            ORDER BY od.product_name ASC";
	    $detRes = $pdo->select($detSql, array(':transId' => makeSafe($transId)));
	}

	ob_start();
	include_once dirname(__DIR__) . "/tmpl/tran_summary_table.inc.php";
	$html_code = ob_get_clean();
	return $html_code;
}
function get_agent_coded_level($agent_coded_id='',$array = ''){
	global $pdo;
	$level = '';
	
	if($agent_coded_id!='' && $array == ''){
		$agentRes = $pdo->selectOne("SELECT level from agent_coded_level where id=:id and is_active='Y'",array(":id"=>$agent_coded_id));
		$level = $agentRes['level'];
	}else{
		$level = $pdo->select("SELECT * from agent_coded_level where  is_active='Y' order by id desc");
	}
	return $level;
}
function get_agent_coded_level_heading($agent_coded_id){
	global $pdo;
	$level = '';
	$agentRes = $pdo->selectOne("SELECT level_heading from agent_coded_level where id=:id and is_active='Y'",array(":id"=>$agent_coded_id));
	if(!empty($agentRes['level_heading'])){
		$level = $agentRes['level_heading'];
	}
	return $level;
}
function get_cobra_service_fee($price = ""){
	global $pdo;
	$cobra_service_fee = "";
	$total_amount = "";
	$get_cobra_service_fee = $pdo->selectOne("SELECT additional_surcharge FROM group_cobra_benefits WHERE group_use_cobra_benefit = 'Y' AND is_additional_surcharge = 'Y'");
	if($get_cobra_service_fee){
		$cobra_service_fee_percentage = $get_cobra_service_fee['additional_surcharge'];
		if($price){
			$total_amount = $price + ($price * $cobra_service_fee_percentage / 100);
			$cobra_service_fee = ($price * $cobra_service_fee_percentage / 100);
		}
	}
	return array('fee' => floatval($cobra_service_fee),'total' => $total_amount);
}
function get_cobra_service_fee_product(){
	global $pdo;

	$sql = "SELECT p.id as product_id,p.product_code,p.name as product_name,p.product_type,pm.price,pm.id as plan_id,pm.plan_type FROM prd_main p JOIN prd_matrix pm on(pm.product_id = p.id) WHERE p.is_deleted='N' AND p.product_type='CobraServiceFee' ORDER BY p.id DESC";
	$res = $pdo->selectOne($sql);
	if (!empty($res)) {
	  return $res;
	} else {
	  $prd_data = array(
	      'record_type' => 'Primary',
	      'product_code' => 'CS84584',
	      'name' => 'Cobra Service Fee',
	      'type' => 'Fees',
	      'fee_type' => 'Changed',
	      'product_type' => 'CobraServiceFee',
	  );
	  $product_id = $pdo->insert('prd_main',$prd_data);

	  $get_cobra_service_fee = $pdo->selectOne("SELECT additional_surcharge FROM group_cobra_benefits WHERE group_use_cobra_benefit = 'Y' AND is_additional_surcharge = 'Y'");

	  $price = 0.00;
	  if($get_cobra_service_fee){
		$price = $get_cobra_service_fee['additional_surcharge'];
	  }

	  $matrix_data = array(
	      'product_id' => $product_id,
	      'price' => $price,
	      'plan_type' => 1,
	  );
	  $matrix_id = $pdo->insert('prd_matrix',$matrix_data);

	  return get_cobra_service_fee_product();
	}
}
function add_commission_request($operation,$request_params = array(),$extra_params = array()) {
	global $pdo,$SITE_ENV,$AWS_REPORTING_URL;
	require_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
	$request_id = 0;
	if(isset($AWS_REPORTING_URL[$operation])) {
		$request_data = array(
			'operation' => $operation,
			'request_params' => (!empty($request_params)?json_encode($request_params):''),
			'status' => 'Pending',
			'extra_params' => (!empty($extra_params)?json_encode($extra_params):''),
			'req_url' => (isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:'system'),
		);
		$request_id = $pdo->insert("commission_requests",$request_data);
		$request_url = $AWS_REPORTING_URL[$operation]."&request_id=".$request_id;
		$ch = curl_init($request_url);
		curl_setopt($ch, CURLOPT_TIMEOUT,1);//Timeout set to 1 Sec
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_exec($ch);
		curl_close($ch);
	}
	return $request_id;
}

function displayAgentPortalDescriptionInfo($product_id = 0,$is_encryption = 'N'){
	global $pdo;

	include_once dirname(__DIR__) . "/includes/function.class.php";
	$functionsList = new functionsList();

	$incr = " AND p.id=:id";
	$sch_params = array(":id"=>$product_id);

	if($is_encryption=='Y'){
			$incr = " AND md5(p.id)=:id";
	}
	$sql = "SELECT p.id,pd.agent_info,p.direct_product,p.effective_day,p.effective_day2 FROM prd_main p
	        LEFT JOIN prd_descriptions pd ON (p.id = pd.product_id)
	        WHERE 1 $incr";
	$res = $pdo->selectOne($sql,$sch_params);
	$agent_info = array();
	$effective_date_html = "-";
	$available_state_html = "-";
	$required_product_html = "-";
	$excluded_product_html = "-";
	if(!empty($res)){
		$agent_info = !empty($res['agent_info']) ? explode(",", $res['agent_info']) : array();
		$effective_day = !empty($res['effective_day']) ? $functionsList->addOrdinalNumberSuffix($res['effective_day']).',' : '';
		$effective_day_2 = !empty($res['effective_day2']) ? $functionsList->addOrdinalNumberSuffix($res['effective_day2']) .' of the month' : '';

		$effective_date_html = $res['direct_product'] == "Select Day Of Month" ? $effective_day . $effective_day_2 : $res['direct_product'];


		$sqlState = "SELECT sc.short_name AS state_name 
			FROM prd_available_state pas
			JOIN states_c sc ON (sc.id = pas.state_id)
			WHERE pas.product_id=:product_id AND pas.is_deleted='N'";
		$resState = $pdo->select($sqlState,array(":product_id"=>$res['id']));

		$availableStateArr = array();
		if(!empty($resState)){
			foreach ($resState as $key => $value) {
				if(!in_array($value['state_name'],$availableStateArr)){
					array_push($availableStateArr,$value['state_name']);
				}
			}
		}

		$available_state_html = !empty($availableStateArr) ? implode(", ", $availableStateArr) : '-';

		$sqlCombination = "SELECT pcr.combination_type,p.name,p.product_code FROM prd_combination_rule pcr
			JOIN prd_main p ON (p.id = pcr.combination_product_id AND pcr.is_deleted='N')
			WHERE  pcr.product_id = :product_id AND pcr.combination_type IN ('Excludes','Required')";
		$resCombination = $pdo->select($sqlCombination,array(":product_id"=>$res['id']));

        $excludeArray = array();
        $requiredArray = array();
		if(!empty($resCombination)){
			foreach ($resCombination as $key => $value) {
					$tmpVal = $value['name'] .' ('.$value['product_code'].')';

				if($value['combination_type']=="Excludes"){
					array_push($excludeArray, $tmpVal);
				}else if($value['combination_type']=="Required"){
					array_push($requiredArray, $tmpVal);
				}
			}
			$required_product_html = implode(", ", $requiredArray);
			$excluded_product_html = implode(", ", $excludeArray);
		}
	}
	$html = "";
	if(!empty($agent_info)){
		ob_start(); ?>
		<div id="agentInfoHeading" class="br-b">
	          <p style="<?= !empty($agent_info) && in_array("Effective Date",$agent_info) ? '' : 'display: none';?>">Effective Date: <span> <?= $effective_date_html ?></span></p>
	          <p style="<?= !empty($agent_info) && in_array("Available State",$agent_info) ? '' : 'display: none';?>">Available State: <span><?= $available_state_html ?></span></p>
	          <p style="<?= !empty($agent_info) && in_array("Product Required",$agent_info) ? '' : 'display: none';?>">Product Required: <span><?= $required_product_html ?></span></p>
	          <p style="<?= !empty($agent_info) && in_array("Product Excluded",$agent_info) ? '' : 'display: none';?> ">Products Excluded: <span><?= $excluded_product_html ?></span></p>
	  	</div>
  		<?php $html = ob_get_clean();
  	}
  	return $html;
}

function displayNextBillingDate($customer_id,$website_id = 0,$is_encrypted='N'){
	global $pdo;
	$nextBillingDate = array(
		"next_billing_date"=>'',
		"products"=>0,
		"grandTotal"=>0.00,
		"type"=>'',
		"ws_id"=>array(),
	);
	$incr = " ws.customer_id=:customer_id";
	if($is_encrypted=='Y'){
		$incr = " md5(ws.customer_id)=:customer_id";
	}
	$paramsNextInfo[':customer_id'] = $customer_id;
	if(!empty($website_id)){
		$incr.=" AND ws.id=:website_id";
		$paramsNextInfo[':website_id'] = $website_id;
	}
	/*$sqlNextInfo = "SELECT res.processing_date,res.products,res.grandTotal,res.type
		FROM(
		  SELECT MIN(ws.next_purchase_date) AS processing_date,
			COUNT(ws.product_id) AS products,
			SUM(ws.price) AS grandTotal,'npd' AS TYPE
			FROM website_subscriptions ws
			JOIN order_details od on(od.website_id = ws.id AND od.is_deleted='N')
			JOIN prd_main p ON(p.id=ws.product_id AND p.is_deleted='N' AND p.product_type NOT IN('Healthy Step')) 
			WHERE $incr GROUP BY ws.next_purchase_date 
		  UNION 
			SELECT MIN(ws.next_attempt_at) AS processing_date,
			COUNT(ws.product_id) AS products,
			SUM(ws.price) AS grandTotal,'nat' AS TYPE
			FROM website_subscriptions ws
			JOIN order_details od on(od.website_id = ws.id AND od.is_deleted='N')
			JOIN prd_main p ON(p.id=ws.product_id AND p.is_deleted='N' AND p.product_type NOT IN('Healthy Step')) 
			WHERE $incr AND ws.next_attempt_at IS NOT NULL GROUP BY ws.next_attempt_at 
		) AS res";
	*/
	$sqlNextInfo = "SELECT res.*
		FROM(
			SELECT ws.id as id,ws.next_purchase_date AS processing_date,ws.status,ws.termination_date,
			ws.product_id,ws.plan_id,
			ws.price AS grandTotal,'npd' AS TYPE,ce.id AS ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,ws.last_order_id,ws.start_coverage_period,ws.end_coverage_period,p.payment_type_subscription as member_payment_type
			FROM website_subscriptions ws
			JOIN prd_main p ON(p.id=ws.product_id AND p.is_deleted='N' AND p.product_type NOT IN('Healthy Step','ServiceFee')) 
			JOIN customer_enrollment ce ON (ce.website_id=ws.id)
			WHERE $incr AND ws.status !='Inactive'
		UNION 
			SELECT ws.id as id,ws.next_attempt_at AS processing_date,ws.status,ws.termination_date,
			ws.product_id,ws.plan_id,
			ws.price AS grandTotal,'npd' AS TYPE,ce.id AS ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,ws.last_order_id,ws.start_coverage_period,ws.end_coverage_period,p.payment_type_subscription as member_payment_type
			FROM website_subscriptions ws
			JOIN prd_main p ON(p.id=ws.product_id AND p.is_deleted='N' AND p.product_type NOT IN('Healthy Step','ServiceFee'))
			JOIN customer_enrollment ce ON (ce.website_id=ws.id)
			WHERE $incr AND ws.next_attempt_at IS NOT NULL AND ws.status !='Inactive'
		) AS res;
	";
	$next_purchase_info = $pdo->select($sqlNextInfo,$paramsNextInfo);
	
	if(!empty($next_purchase_info)){
		$minNextPurchaseDate = '';
		foreach ($next_purchase_info as $key => $value) {
			if(empty($minNextPurchaseDate)){
				$minNextPurchaseDate = $value['processing_date'];
			}else{
				if(strtotime($value['processing_date']) < strtotime($minNextPurchaseDate)){
					$minNextPurchaseDate = $value['processing_date'];
				}
			}
		}
		foreach ($next_purchase_info as $key => $value) {
			/* old code
			$processing_date = !empty($nextBillingDate['next_billing_date']) ? date("m/d/Y",strtotime($nextBillingDate['next_billing_date'])) : '';
			$tmpDate = date("m/d/Y",strtotime($value['processing_date']));

			if(empty($processing_date) || strtotime($tmpDate) < strtotime($processing_date)){
				$nextBillingDate["next_billing_date"]=$tmpDate;
				$nextBillingDate["products"]=$value['products'];
				$nextBillingDate["grandTotal"]=$value['grandTotal'];
				$nextBillingDate["type"]=$value['type'];
			}old code
			*/
			if(strtotime($minNextPurchaseDate) != strtotime($value['processing_date'])){
				continue;
			}
			$tmpDate = date("m/d/Y",strtotime($value['processing_date']));
			if(empty($value['last_order_id'])){
				$startCoveragePeriod = date('Y-m-d',strtotime($value['start_coverage_period']));
				$endCoveragePeriod = date('Y-m-d',strtotime($value['end_coverage_period']));
			} else {
				require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
				$enrollDate = new enrollmentDate();
				$endCoveragePeriod = date('Y-m-d',strtotime($value['end_coverage_period']));
				$startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));
				$product_dates=$enrollDate->getCoveragePeriod($startDate,$value['member_payment_type']);

				$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
				$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
			}

			$tire_change_date = $value['tier_change_date'];
			if($value['process_status'] != 'Pending'){
				if(empty($nextBillingDate["next_billing_date"]) || strtotime($tmpDate) < strtotime($nextBillingDate["next_billing_date"])){
					$nextBillingDate["next_billing_date"] = $tmpDate;
				}
				$nextBillingDate["ws_id"][]=$value['id'];				
				$nextBillingDate["products"] += 1;
				$nextBillingDate["grandTotal"] += $value['grandTotal'];
				// $nextBillingDate["type"] = $value['type'];
			} else if (!empty($value['termination_date']) && strtotime($value['termination_date']) > 0 && strtotime($value['termination_date']) >= strtotime($endCoveragePeriod)) {
				//If Termination date is future date And Tier change Date in future
				$termination_date = date('Y-m-d',strtotime($value['termination_date']));
				if (strtotime($termination_date) <= strtotime($tire_change_date) || empty($tire_change_date)) {
					if(empty($nextBillingDate["next_billing_date"]) || strtotime($tmpDate) < strtotime($nextBillingDate["next_billing_date"])){
						$nextBillingDate["next_billing_date"] = $tmpDate;
					}
					$nextBillingDate["ws_id"][]=$value['id'];				
					$nextBillingDate["products"] += 1;
					$nextBillingDate["grandTotal"] += $value['grandTotal'];
				}
			} else if ($value['process_status'] == 'Pending' && !empty($value['new_plan_id']) && !empty($value['tier_change_date'])) {
					
					if (strtotime($startCoveragePeriod) <= strtotime($tire_change_date) && strtotime($tire_change_date) <= strtotime($endCoveragePeriod)) {
	
						$new_ws_sql = "SELECT ce.id as ce_id,ws.price as grandTotal
										FROM customer_enrollment ce 
										JOIN website_subscriptions ws ON(ws.id = ce.website_id)
										WHERE
										ce.parent_coverage_id=:parent_coverage_id AND 
										ws.status='Pending' AND 
										ce.process_status='Pending'";
						$new_ws_row = $pdo->selectOne($new_ws_sql, array(":parent_coverage_id" => $value['ce_id']));
						if (!empty($new_ws_row['ce_id'])) { 
	
							if(empty($nextBillingDate["next_billing_date"]) ||  strtotime($tmpDate) < strtotime($nextBillingDate["next_billing_date"])){
								$nextBillingDate["next_billing_date"] = $tmpDate;
							}
							$nextBillingDate["ws_id"][]=$value['id'];				
							$nextBillingDate["products"] += 1;
							$nextBillingDate["grandTotal"] += $new_ws_row['grandTotal'];
						}
					}
			}
		}
	}
	return $nextBillingDate;
}

function displayPolicyNextBillingDate($ws_id,$next_purchase_date,$termination_date,$end_coverage_period,$sponsor_id,$extra_params = array())
{
	include_once dirname(__DIR__) . "/includes/list_bill.class.php";
	global $pdo;
		$listBillObj = new ListBill();

	$NextBillingDate = "-";
	$sponsor_billing_method = (isset($extra_params['sponsor_billing_method'])?$extra_params['sponsor_billing_method']:'individual');

	if($ws_id){
		$product_type = $pdo->selectOne("SELECT p.product_type FROM prd_main p JOIN website_subscriptions w on(w.product_id = p.id) AND w.id = :website_id AND p.product_type IN('Healthy Step','ServiceFee')",array(':website_id' => $ws_id));
		if(!empty($product_type)){
			return $NextBillingDate;
		}
	}

	if(strtotime($termination_date) > 0) {
		if(strtotime($termination_date) <= strtotime($end_coverage_period) || strtotime($termination_date) <= strtotime(date("Y-m-d"))) {
	    	$NextBillingDate = '-';
		} else {
			if($sponsor_billing_method == "list_bill") {
				$listBillGenerateRes = $listBillObj->get_next_list_bill_generate_date($sponsor_id,$ws_id);
				$NextBillingDate = $listBillGenerateRes["listBillDate"];
			} else {
	      		$NextBillingDate = displayDate($next_purchase_date);
			}
	  	}
	} else {
      	if($sponsor_billing_method == "list_bill") {
			$listBillGenerateRes = $listBillObj->get_next_list_bill_generate_date($sponsor_id,$ws_id);
			$NextBillingDate = $listBillGenerateRes["listBillDate"];
		} else {
      		$NextBillingDate = displayDate($next_purchase_date);
		}
  	}
  	return $NextBillingDate;
}
function check_agent_license_validation($agent_id,$validate,$hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_exp,$edit,$ajax='')
{	
	global $pdo;
    $dbLic = $pdo->selectOne("SELECT count(id) as lic from agent_license where agent_id=:agent_id AND is_deleted='N'",array(":agent_id"=>$agent_id));
	$totalLicense = !empty($dbLic['lic']) ? $dbLic['lic'] : 0;
	foreach ($hdn_license as $lekey => $lexpiry) {
		if($license_not_exp[$lekey] == 'N' && !empty($license_not_exp[$lekey])){
			if(!isset($license_expiry[$lekey]) || empty($license_expiry[$lekey])){
				$validate->setError("license_expiry_" . $lekey, "License Expiry date is required");
			}else{
				if (validateDate($license_expiry[$lekey],'m/d/Y')) {
					if (!isFutureDateMain($license_expiry[$lekey],'m/d/Y')) {
						// $validate->setError("license_expiry_" . $lekey, "Please Add Future License Date is required");
					}
				} else {
                    $validate->setError("license_expiry_" . $lekey, "Valid License Date is required");
                } 
			}
		}
        
        $validate->string(array('required' => true, 'field' => 'license_active_date_' . $lekey, 'value' => $license_active[$lekey]), array('required' => 'License Active date is required'));

		if(!empty($license_active[$lekey]) && strtotime($license_active[$lekey]) < 0){
			$validate->setError("license_active_date_" . $lekey, "Select Valid License Active date");
		}

        $validate->string(array('required' => true, 'field' => 'license_number_' . $lekey, 'value' => $license_number[$lekey]), array('required' => 'Valid License Number is required'));

		$license_type_1 = !empty($license_type[$lekey]) ? $license_type[$lekey] : "";
        $validate->string(array('required' => true, 'field' => 'license_type_' . $lekey, 'value' => $license_type_1), array('required' => 'License Type is required'));
        
        $l_auth = !empty($license_auth[$lekey]) ? $license_auth[$lekey] : "";
        $validate->string(array('required' => true, 'field' => 'licsense_authority_' . $lekey, 'value' => $l_auth), array('required' => 'License Of Authority is required'));

		$l_state = !empty($license_state[$lekey]) ? $license_state[$lekey] : "";
		if(!isset($l_state) || empty($l_state)){
			$validate->setError("license_state_" . $lekey, "Select License State");
		}else{
            if(!empty($l_state) && !empty($l_auth) && !empty($license_type_1)){
                $schagParam = array(
                    ":agent_id"=>$agent_id,
                    ":selling_licensed_state"=>$l_state,
                    ":license_auth"=>$l_auth,
                    ":license_type"=>$license_type_1,
				);
				$lincr = '';
                if(!empty($edit[$lekey])){
                    $lincr .= ' AND id!=:id ';
                    $schagParam[':id'] = $edit[$lekey];
				}
                $exist_license = $pdo->selectOne("SELECT count(id) as ids from agent_license WHERE agent_id=:agent_id AND ((selling_licensed_state=:selling_licensed_state AND license_auth=:license_auth AND license_type=:license_type) OR
				(new_selling_licensed_state=:selling_licensed_state AND new_license_auth=:license_auth AND new_license_type=:license_type)) AND is_deleted='N' $lincr ",$schagParam);
                if(!empty($exist_license['ids']) && $exist_license['ids'] > 1){
                    $validate->setError("license_state_" . $lekey, "Please select different License State.");
                }else if(!empty($edit[$lekey]) && $exist_license['ids'] > 0){
					$validate->setError("license_state_" . $lekey, "Please select different License State.");
				}
            }
			$validate->string(array('required' => true, 'field' => 'license_state_' . $lekey, 'value' => $l_state), array('required' => 'License state is required'));
		}
	}
}
function get_sponsor_billing_method($sponsor_id = 0,$customer_id = 0){
	global $pdo;
	$sponsor_billing_method = "individual";
	if(empty($sponsor_id) && !empty($customer_id)) {
		$sponsor_id = getname('customer', $customer_id, 'sponsor_id','id');
	}
	if(!empty($sponsor_id)) {
		$sqlBillingType = "SELECT billing_type FROM customer_group_settings WHERE customer_id=:customer_id";
	    $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id" => $sponsor_id));
	    if(!empty($resBillingType) && !empty($resBillingType['billing_type'])){
	        $sponsor_billing_method = $resBillingType['billing_type'];
	    }
	}
	return $sponsor_billing_method;
}
function get_renewals_new_price($ws_id,$insert=true){
	global $pdo;
	include_once __DIR__ . '/function.class.php';
	$function_list = new functionsList();
	$today = date("Y-m-d");
	$response = array('pricing_changed' => 'N');
	
	$old_ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE id=:id",array(":id"=>$ws_id));

	$sponsor_sql = "SELECT s.id,s.type,s.upline_sponsors,s.level,s.payment_master_id,s.ach_master_id,s.fname,s.lname,s.user_name,s.rep_id,s.sponsor_id,cs.group_coverage_period_id,cs.class_id
	FROM customer c
	JOIN customer s ON(s.id = c.sponsor_id)
	JOIN customer_settings cs ON(cs.customer_id = c.id) 
	WHERE s.type!='Customer' AND c.id = :id ";
	$sponsor_row = $pdo->selectOne($sponsor_sql, array(':id' => $old_ws_row['customer_id']));


	$productsSql="SELECT p.id as p_product_id,p.name as product_name,p.product_code,
		pm.id as matrix_id,pm.plan_type,pm.price,p.company_id
		FROM prd_main p
		JOIN prd_matrix pm on (p.id = pm.product_id AND pm.is_deleted='N')
		WHERE p.id=:product_id AND pm.plan_type = :plan_type AND pm.is_new_price_on_renewal = 'Y' AND p.status='Active' AND p.is_deleted='N' AND pm.is_deleted='N'AND (pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date is null))";

	$productsRes=$pdo->selectOne($productsSql,array(":today_date" => $today,":product_id" => $old_ws_row['product_id'],":plan_type" => $old_ws_row['prd_plan_type_id']));

	if($productsRes){
		if($productsRes['matrix_id'] != $old_ws_row['plan_id']){
			$response['pricing_changed'] = 'Y';

			$product_matrix = array($productsRes['p_product_id'] => $productsRes['matrix_id']);
			$product_plan = array($productsRes['p_product_id'] => $productsRes['plan_type']);
			$product_price = array($productsRes['p_product_id'] => $productsRes['price']);
			
			$new_eligibility_date = date('Y-m-d',strtotime($old_ws_row['end_coverage_period'] .'+1 day'));

			$new_ws_row = array(
				'website_id' => $function_list->get_website_id(),
				'customer_id' => $old_ws_row['customer_id'],
				'eligibility_date' => $new_eligibility_date,
				'product_id' => $old_ws_row['product_id'],
				'plan_id' => $productsRes['matrix_id'],
				'price' => $productsRes['price'],
				'prd_plan_type_id' => $productsRes['plan_type'],
				'status' => "Active",
				'fee_applied_for_product' => $old_ws_row['fee_applied_for_product'],
				'product_type' => $old_ws_row['product_type'],
				'last_order_id' => $old_ws_row['last_order_id'],
				'renew_count' => $old_ws_row['renew_count'],
				'parent_ws_id' => $old_ws_row['id'],
				'product_code' => $old_ws_row['product_code'],
				'qty' => $old_ws_row['qty'],
				'payment_type' => $old_ws_row['payment_type'],
				'issued_state' => $old_ws_row['issued_state'],
				'last_purchase_date' => $old_ws_row['last_purchase_date'],
				'start_coverage_period' => $old_ws_row['start_coverage_period'],
				'end_coverage_period' => $old_ws_row['end_coverage_period'],
				'next_purchase_date' => $old_ws_row['next_purchase_date'],
				'active_date' => strtotime($old_ws_row['active_date'])>0?$old_ws_row['active_date']:$old_ws_row['created_at'],
				'application_type' => $old_ws_row['application_type'],
				'is_cobra_coverage' => $old_ws_row['is_cobra_coverage'],
				'is_listbill_refund_charge' => $old_ws_row['is_listbill_refund_charge'],
				'created_at' => 'msqlfunc_NOW()',
				'purchase_date' => 'msqlfunc_NOW()',
			);

			$group_product_price = get_group_member_pricing($product_price,$product_matrix,$product_plan,$sponsor_row);

			if($group_product_price){
				$new_ws_row['group_price'] = $group_product_price[$productsRes['p_product_id']]['group_price'];
				$new_ws_row['member_price'] = $group_product_price[$productsRes['p_product_id']]['member_price'];
				$new_ws_row['contribution_type'] = isset($group_product_price[$productsRes['p_product_id']]['contribution_type']) ? $group_product_price[$productsRes['p_product_id']]['contribution_type'] : "";
				$new_ws_row['contribution_value'] = isset($group_product_price[$productsRes['p_product_id']]['contribution_value']) ? $group_product_price[$productsRes['p_product_id']]['contribution_value'] : "";
				$new_ws_row['price'] = $group_product_price[$productsRes['p_product_id']]['price'];
			}

			if($insert){

				$check_ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE customer_id=:customer_id AND product_id = :product_id AND plan_id=:plan_id",array(':customer_id' => $old_ws_row['customer_id'],':product_id' => $productsRes['p_product_id'],':plan_id' => $productsRes['matrix_id']));
				if(empty($check_ws_row)){
					$new_ws_id = $pdo->insert("website_subscriptions",$new_ws_row);
					$new_ws_row['id'] = $new_ws_id;

					$customer_rows = $pdo->selectOne("SELECT id,fname,lname,rep_id,sponsor_id from customer where id=:id",array(":id"=>$new_ws_row['customer_id']));
					$sponsor_rows = $pdo->selectOne("SELECT id,fname,lname,rep_id,type from customer where id=:id",array(":id"=>$customer_rows['sponsor_id']));
					$price_ac_descriptions = array();

					$price_ac_descriptions['description'] = 'Policy Effective date and Price changed';
					$price_ac_descriptions['old_policy_id'] = 'Old Policy: '.$old_ws_row['website_id'];
					$price_ac_descriptions['old_policy_termination'] = 'Old Policy Termination Date: '.date('m/d/Y',strtotime($old_ws_row['end_coverage_period']));
					$price_ac_descriptions['old_price'] = 'Old Price: '.displayAmount($old_ws_row['price']);
					$price_ac_descriptions['new_policy_id'] = 'New Policy: '.$new_ws_row['website_id'];
					$price_ac_descriptions['effective_date'] = 'New Effective Date: '.date('m/d/Y',strtotime($new_eligibility_date));
					$price_ac_descriptions['new_price'] = 'New Price: '.displayAmount($new_ws_row['price']);

					activity_feed(3, $sponsor_rows['id'], $sponsor_rows['type'], $customer_rows['id'], 'customer', 'Policy Price Changed', $productsRes['product_name'], "", json_encode($price_ac_descriptions));
				}else{
					$new_ws_row = $check_ws_row;
				}
			}else{
				$response['new_ws_row'] = $new_ws_row;
				return $response;
			}

			$response['new_ws_row'] = $new_ws_row;

			$update_old_ws_data = array(
                "termination_date" => $old_ws_row['end_coverage_period'],
                "term_date_set" => "msqlfunc_NOW()",
                "termination_reason" => "Policy Change",
            );
            $update_old_ws_where = array("clause" => "id=:id", "params" => array(":id" => $old_ws_row['id']));
            $pdo->update("website_subscriptions", $update_old_ws_data, $update_old_ws_where);

			$sub_products = $function_list->get_sub_product($old_ws_row['product_id']);

			$new_ce_row = array(
                'website_id' => $new_ws_row['id'],
                'company_id' => $productsRes['company_id'],
                'sub_product' =>$sub_products,
                'sponsor_id' => $sponsor_row['id'],
                'upline_sponsors' => $sponsor_row['upline_sponsors'] . $sponsor_row['id'] . ",",
                'level' => $sponsor_row['level']+1,
            );

            $check_ce_row = $pdo->selectOne("SELECT * FROM customer_enrollment WHERE website_id=:website_id",array(':website_id' => $new_ws_row['id']));
			if(empty($check_ce_row)){
            	$customer_enrollment_id = $pdo->insert("customer_enrollment", $new_ce_row);
			}else{
				$new_ce_row = $check_ce_row;
			}


            $old_dependents = $pdo->select("SELECT * FROM customer_dependent WHERE website_id=:website_id AND is_deleted = 'N'",array(':website_id'=>$old_ws_row['id']));

            if($old_dependents){
            	foreach ($old_dependents as $key => $dependent) {
            		$update_old_cd_data = array(
		                "terminationDate" => $old_ws_row['end_coverage_period'],
		                "status" => "Inactive",
		            );
		            $update_old_cd_where = array("clause" => "id=:id", "params" => array(":id" => $dependent['id']));
		            $pdo->update("customer_dependent", $update_old_cd_data, $update_old_cd_where);

		            $new_cd_data = $dependent;
		            unset($new_cd_data['id']);
					$new_cd_data['website_id'] = $new_ws_row['id'];		            
					$new_cd_data['display_id'] = $function_list->get_dependant_display_id();		            
					$new_cd_data['product_plan_id'] = $new_ws_row['plan_id'];		            
					$new_cd_data['terminationDate'] = NULL;
					$new_cd_data['eligibility_date'] = $new_eligibility_date;
					$new_cd_data['active_since'] = strtotime($dependent['active_since'])>0?$dependent['active_since']:$dependent['created_at'];

					$check_dependents = $pdo->selectOne("SELECT * FROM customer_dependent WHERE website_id=:website_id AND cd_profile_id = :profile_id AND is_deleted = 'N'",array(':website_id'=>$new_ws_row['id'],':profile_id' => $dependent['cd_profile_id']));
					if(empty($check_dependents)){
						$new_cd_id = $pdo->insert('customer_dependent',$new_cd_data);
					}
            	}
            }


		}
	}
	return $response;

}
function get_group_member_pricing($product_price,$product_matrix,$product_plan,$sponsor_row){
	global $pdo;
	include_once __DIR__ . '/member_enrollment.class.php';
	$MemberEnrollment = new MemberEnrollment();
	$product_details = array();

	if($sponsor_row['type'] == "Group") {

		$group_coverage_period_id = !empty($sponsor_row['group_coverage_period_id']) ? $sponsor_row['group_coverage_period_id'] : '';
		$enrolle_class = !empty($sponsor_row['class_id']) ? $sponsor_row['class_id'] : '';

		$sqlCoveragePeriod="SELECT gcc.*,gc.pay_period, gco.display_contribution_on_enrollment 
			FROM group_coverage_period_offering gco 
			JOIN group_classes gc ON (gc.id=gco.class_id and gc.is_deleted='N')
			LEFT JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N') 
			where gco.is_deleted='N' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id";
		$sqlCoveragePeriodWhere=array(':group_id'=>$sponsor_row['id'],':class_id'=>$enrolle_class,':group_coverage_period_id'=>$group_coverage_period_id);
		$resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);

		
		if($resCovergaePeriod){
			foreach ($resCovergaePeriod as $key => $value) {
				$display_contribution=$value['display_contribution_on_enrollment'];
				$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['type']=$value['type'];
				$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['contribution']=$value['con_value'];
				$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['pay_period']=$value['pay_period'];
				$groupCoverageContributionArr['pay_period']['pay_period']=$value['pay_period'];
			}

			$product_details = $MemberEnrollment->getProductDetails($product_price,$product_matrix,$product_plan,$groupCoverageContributionArr);
		}


	}
	return $product_details;

}

function is_ach_voidable($orderId){
	global $pdo;
	if(empty($orderId)){
		return false;
	}

	$order_row = $pdo->selectOne("SELECT id,created_at FROM orders WHERE status='Pending Settlement' AND id=:id",array(':id' => $orderId));

	if($order_row){
		$current_date = date('Y-m-d H:i:s');
		$order_date = date('Y-m-d H:i:s',strtotime($order_row['created_at']));
		$day = (date('d',strtotime($order_date)));
		$fix_date = date("Y-m-".$day. " 15:00:00");
		
		if((strtotime($order_date) > strtotime($fix_date))){
			$fix_date = date('Y-m-d H:i:s',strtotime('+1 day',strtotime($fix_date)));
		}

		if((strtotime($order_date) < strtotime($fix_date)) && (strtotime($current_date) < strtotime($fix_date))){
			return true;
		}
		
	}
	return false;
}

function getEmailSMSTable($email,$cell_phone,$has_full_access = true){
	global $pdo,$HOST,$table_class;
	$trigger_log_sql = "SELECT res.email_log_id,res.sms_log_id,res.trigger_id,res.to_email,res.to_number,res.title,res.display_id,res.created_at,res.type,res.status as details_status
    FROM (
        SELECT e.id AS email_log_id,'' AS sms_log_id,t.id AS trigger_id,e.to_email,'' as to_number,if(t.title != '',t.title,e.name) as title,t.display_id,e.created_at,t.type,eld.status
        FROM email_log e
        LEFT JOIN email_log_details eld ON eld.id = (SELECT id FROM email_log_details WHERE log_id = e.id ORDER BY id DESC LIMIT 1)
        LEFT JOIN triggers t ON(t.id = e.trigger_id)
        WHERE (e.to_email = :email OR e.user_email = :email) GROUP BY e.id
    UNION 
        SELECT '' AS email_log_id,s.id AS sms_log_id,t.id AS trigger_id,'' AS to_email,s.to_number,if(t.title != '',t.title,s.name) as title,t.display_id,s.created_at,t.type,sld.status
        FROM sms_log s
        LEFT JOIN sms_log_details sld ON sld.id = (SELECT id FROM sms_log_details WHERE log_id = s.id ORDER BY id DESC LIMIT 1)
        LEFT JOIN triggers t ON(t.id = s.trigger_id)
        WHERE (s.to_number = :cell_phone1 OR s.to_number = :cell_phone2 OR s.user_phone = :cell_phone1 OR s.user_phone = :cell_phone2) GROUP BY s.id
      ) AS res order BY res.created_at desc";
  $trigger_log_res = $pdo->select($trigger_log_sql,array(':email' => $email,":cell_phone1" => '+1'.$cell_phone,":cell_phone2" => '+91'.$cell_phone));

  $html = "";
  ob_start();
  ?>
  <table class="<?=$table_class?>" data-mobile-responsive="true" id="communication_table">
    <thead>
      <tr>
        <th>ID/Sent Date</th>
        <th>Name</th>
        <th class="text-center">Message</th>
        <!-- <th>Type</th> -->
        <th class="text-center">Status</th>
        <th class="text-center">Action</th>
      </tr>
    </thead>  
    <tbody>
  <?php
  if($trigger_log_res){
    foreach ($trigger_log_res as $key => $log) { ?>
      <tr>
        <td><?=displayDate($log['created_at'])?>
        <br><a href="javascript:void(0);" class="fw500 text-action"><?=!empty($log['display_id']) ? $log['display_id'] : '' ?></a></td>    
        <td><?=!empty($log['title']) ? $log['title'] : '' ?></td>
        <?php if(!empty($log['email_log_id'])){ ?>
        <td class="icons text-center">
          <?php if($has_full_access){ ?>
          <a href="<?=$HOST?>/send_email_content.php?log_id=<?=md5($log['email_log_id'])?>" class="emailer_content" data-toggle="tooltip" data-placement="top" title="" data-original-title="Email Content">
          <i class="fa fa-eye "></i>
          </a>
          <?php }else {echo '-'; } ?>
        </td>
        <?php } ?>
        <?php if(!empty($log['sms_log_id'])){ ?>
        <td class="icons text-center">
          <?php if($has_full_access){ ?>
          <a href="<?=$HOST?>/send_sms_content.php?log_id=<?=md5($log['sms_log_id'])?>" class="emailer_content" data-toggle="tooltip" data-placement="top" title="" data-original-title="SMS Content">
          <i class="fa fa-eye "></i>
          </a>
          <?php }else {echo '-'; } ?>
        </td>
        <?php } ?>    
        <!-- <td><?=$log['type']?></td> -->

        <?php if(!empty($log['details_status'])){
          if(in_array($log['details_status'], array('bounce','deferred','dropped'))){
            $log['status'] = 'fail';
          }else{
            $log['status'] = 'success';
          }
        }else{
          $log['status'] = $log['details_status'];
        }?> 
        <td class="icons text-center">
			<?php 
			$url="";
			if(!empty($log['to_email'])){  
				$url = $HOST.'/send_email_activity.php?log_id='.md5($log['email_log_id']).'&email='.$log['to_email'];
			}else{  
				$url = $HOST.'/send_sms_activity.php?log_id='.md5($log['sms_log_id']).'&phone='.$log['to_number'];
			 }
			?>
			<a href="<?=$url?>" class="emailer_content" data-toggle="tooltip" ata-placement="top" title="<?= ($log['status']=='fail')?'Fail':'Success' ?>" aria-hidden="true" >
				<?php if($log['status']=='fail'){ ?>
				<div class="text-action"><i class="fa fa-exclamation-circle fa-lg"></i></div>
				<?php }else{ ?>
				<div class="text-success"><i class="fa fa-check-circle fa-lg"></i></div>
				<?php } ?>
			</a>
        </td>
        <td class="icons text-center">
          	<?php if(!empty($log['trigger_id'])){ ?>
	          	<?php if(!empty($log['email_log_id'])){ ?>
	            	<a href="javascript:void(0);" class="send_communication" id="send_email" onclick="sendEmailSMS($(this),'Email')" data-toggle="tooltip" ata-placement="top" title="" aria-hidden="true" data-original-title="Resend Email" data-id ="<?=$log['trigger_id']?>"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>
	          	<?php } ?>
	          	<?php if(!empty($log['sms_log_id'])){ ?>
	            	<a href="javascript:void(0);" class="send_communication" id="send_sms" onclick="sendEmailSMS($(this),'SMS')" data-toggle="tooltip" ata-placement="top" title="" aria-hidden="true" data-original-title="Resend SMS" data-id ="<?=$log['trigger_id']?>"><i class="fa fa-mobile fa-lg" style="font-size: 20px;" aria-hidden="true"></i></a>
	          	<?php } ?>
          	<?php } else { ?>
          		<?php echo "-"; ?>
          	<?php } ?>
        </td>
      </tr>
    <?php }
  }else{ ?>
    <tr >
      <td colspan="5">
       No record(s) found
      </td>
    </tr>
  <?php } ?>
  </tbody>
  </table>
  <?php 
  $html = ob_get_clean();
  echo $html;
  exit();
}
function allowMakePayment($order_id){
	global $pdo;

	$allow_make_payment = false;

	$order_res = $pdo->selectOne("SELECT o.id FROM orders o 
		JOIN order_details od on(o.id = od.order_id AND od.is_deleted='N')
		JOIN website_subscriptions w on(w.id = od.website_id)
		WHERE o.id = :order_id
		AND o.is_renewal = 'Y'
		AND o.status = 'Payment Declined' 
		AND w.termination_date IS NULL 
		AND od.product_type != 'Fees'",array(":order_id" => $order_id));

	if($order_res){
		$allow_make_payment = true;
	}

	return $allow_make_payment;
}

function get_global_user_setting($user_type,$columns = array()){
	global $pdo;
	$global_setting = array();
	if(!empty($user_type)){
		$columns = !empty($columns) ? implode(',',array_merge(array('id'),$columns)) : "*";
		$settingRow = $pdo->selectOne("SELECT $columns from global_user_manage where LOWER(user_type)=:user_type",array(":user_type"=>strtolower($user_type)));
		if(!empty($settingRow['id']) && !empty($settingRow)){
			return $settingRow;
		}
	}
	return $global_setting;
}

function save2FAuserSetting($user_type,$params,$extra = array()){
	global $pdo;
	$updateArr = array();
	if(!empty($user_type)){
		$user_type = strtolower($user_type);
		$settingRow = $pdo->selectOne("SELECT * from global_user_manage where LOWER(user_type)=:user_type",array(":user_type"=>$user_type));
		if(!empty($settingRow['id']) && !empty($settingRow)){
			unset($params['user_type']);
			$warr = array(
				"clause" => "user_type=:user_type",
				"params" => array(":user_type"=>$user_type)
			);
			$updateArr = $pdo->update("global_user_manage",$params,$warr,true);
			$updateArr['entity_id'] = $settingRow['id'];
		}else{
			$inserted_id = $pdo->insert("global_user_manage",$params);
			$params['entity_id'] = $inserted_id;
			$updateArr = $params;
		}

		if(!empty($updateArr) && !empty($extra)){

			$ac_message_1 = !empty($extra['ac_message_1']) ? $extra['ac_message_1'] : '';
			$new_update_details = !empty($extra['new_update_details']) ? $extra['new_update_details'] : array();
			$user_id = !empty($extra['user_id']) ? $extra['user_id'] : '';
			$link = !empty($extra['link']) ? $extra['link'] : '';
			$display_id = !empty($extra['display_id']) ? $extra['display_id'] : '';
			$entity_action = !empty($extra['entity_action']) ? $extra['entity_action'] : '';
			$user_fname = !empty($extra['user_fname']) ? $extra['user_fname'] : '';
			$user_lname = !empty($extra['user_lname']) ? $extra['user_lname'] : '';

			$res['status'] = 'success';
			$flg = "false";
			$description = array();
			$description['ac_message'] = array(
				'ac_red_1'=>array(
					'href'=>$link,
					'title'=>$display_id,
				),
			'ac_message_1' =>$ac_message_1,
			);
			foreach($updateArr as $key2 => $val){
				if($key2 == 'entity_id' || $key2 == 'user_type' ){
					continue;
				}
				if(array_key_exists($key2,$new_update_details)){
					if(in_array($val,array('Y','N'))){
						$val = ($val == 'Y' ? "selected" : "unselected");
					}
					$tmp_key2 = str_replace('_',' ',$key2);
					if(in_array($key2,array('is_2fa'))){
						$tmp_key2 = "Two-Factor Authentication (2FA)";
					}
					if(in_array($key2,array('is_ip_restriction'))){
						$tmp_key2 = "IP Address Restriction";
					}
					$description['key_value']['desc_arr'][$tmp_key2] = ' Updated From '.$val." To ".$new_update_details[$key2].".<br>";
					$flg = "true";
				}else{
					$description['description2'][] = ucwords(str_replace('_',' ',$val));
					$flg = "true";
				}
			}
			if($flg == "true"){
				$desc=json_encode($description);
				activity_feed(3, $user_id, $user_type, $updateArr['entity_id'], 'global_user_manage', $entity_action,$user_fname,$user_lname,$desc);
			}
			return 'success';
		}else{
			return '';
		}

		return $updateArr;
	}
}

/**
 * Generate Common User Interface for Two Factor Authentication
 */
function generate2FactorAuthenticationUI($row=array(),$classArr = array()){
		$twoFAUI = '';
		$class  =  $offClass = $addOnClass = '';
		if(empty($classArr['main_class'])){
			$class = 'col-sm-6';
		}else{
			$class = $classArr['main_class'];
		}

		if(empty($classArr['offsetClass'])){
			$offClass = 'col-sm-5 col-sm-offset-1  m-b-25';
		}else{
			$offClass = $classArr['offsetClass'];
		}

		if(empty($classArr['addOnClass'])){
			$addOnClass = 'phone-addon text-left w-160 p-t-7';
		}else{
			$addOnClass = $classArr['addOnClass'];
		}
		
		$twoFAUI .=  '
		<div class="'.$class.' theme-form">
			<div class="phone-control-wrap m-b-25">
				<div class="phone-addon text-left">
					<strong>Two-Factor Authentication (2FA):</strong><br>
					Two-factor authentication is an extra layer of security on login designed to ensure that user is the only person who can access their account, even if someone knows their password.
				</div>
				<div class="phone-addon w-90">
					<div class="custom-switch">
						<label class="smart-switch">
							<input type="checkbox" class="js-switch" name="is_2fa" id="is_2fa" '. (checkIsset($row['is_2fa'])=='Y' ? 'checked' : '' ).' value="Y" />
							<div class="smart-slider round"></div>
						</label>
					</div>
				</div>
			</div>
			<div class="2fa_div m-t-25 user_authentication" style="'.(checkIsset($row['is_2fa'])=='Y' ? '' : 'display: none;') .'">
				<div class="phone-control-wrap">
					<div class="'.$addOnClass.'">
						<input type="radio" name="send_via" id="send_via_email" value="email" '.(checkIsset($row['send_otp_via'])=='email' ? 'checked' : '').'>Via Email
					</div>
					<div class="phone-addon">
						<div class="form-group">
							<input type="text" name="via_email" value="'.checkIsset($row['via_email']).'" class="form-control valid_phone no_space"  id="via_email">
							<label for="via_email">Email Address </label>
							<div id="via_email_err" class="mid"><span></span></div>
							<p class="error text-left"><span id="error_via_email"></span></p>
						</div>
					</div>
				</div>
				<div class="phone-control-wrap">
					<div class="'.$addOnClass.'">
						<input type="radio" name="send_via" id="send_via_mobile" value="sms" '.(checkIsset($row['send_otp_via'])=='sms' ? 'checked' : '' ).'>Via Text Message
					</div>
					<div class="phone-addon">
						<div class="form-group">
							<input type="text" name="via_mobile" value="'.checkIsset($row['via_sms']).'" class="form-control valid_phone"  id="via_mobile">
							<label for="via_mobile">Phone Number </label>
							<div id="via_mobile_err" class="mid"><span></span></div>
							<p class="error text-left"><span id="error_via_mobile"></span></p>
						</div>
					</div>
				</div>
				<p class="error"><span id="error_send_via"></span></p>
			</div>
			<div class="phone-control-wrap">
				<div class="phone-addon text-left">
					<strong>IP Address Restriction:</strong><br>
					IP restrictions allow user to specify which IP addresses have access to sign in to their account. We recommend using IP restrictions if user desires to access account when they are in office, mobile, etc.
				</div>
				<div class="phone-addon w-90">
					<div class="custom-switch">
						<label class="smart-switch">
							<input type="checkbox" class="js-switch" name="is_ip_restriction" id="is_ip_restriction" '.(checkIsset($row['is_ip_restriction'])=='Y' ? 'checked' : '' ).' value="Y" />
							<div class="smart-slider round"></div>
						</label>
					</div>
				</div>
			</div>
		</div>';
			$allowed_ip_res = array();
			if(checkIsset($row['is_ip_restriction']) == 'Y' && !empty($row['allowed_ip'])) {
				$allowed_ip_res = explode(',',$row['allowed_ip']);
			}
		$twoFAUI.='
		<div class="clearfix"></div>
		<div class="ip_address_div m-t-25 theme-form" style="'.($row['is_ip_restriction']=='Y' ? '' : 'display: none;').'">
			<div class="'.$offClass.'">
				<div id="ip_address_row_div">';
					if(!empty($allowed_ip_res)) {
							foreach ($allowed_ip_res as $key => $allowed_ip) { 
			$twoFAUI.='
					<div class="ip_address_row" id="ip_address_row_'.$key.'" data-id="'.$key.'">
						<div class="phone-control-wrap">
							<div class="phone-addon">
								<div class="form-group">
									<input type="text" name="allowed_ip_res['.$key.']" class="form-control ip_input" value="'.$allowed_ip.'">
									<label>IP Address</label>
									<p class="error text-left"><span id="error_ip_address_'.$key.'"></span></p>
								</div>
							</div>';
							if($key > 0) {
							$twoFAUI.='
							<div class="phone-addon">
								<div class="form-group">
									<a href="javascript:void(0);" class="text-light-gray fw700 remove_ip_address"  data-id="'.$key.'">X</a>
								</div>
							</div>';
							} 
				$twoFAUI.='
						</div>
					</div> ';
					} } else { 
						$twoFAUI.='
						<div class="ip_address_row" id="ip_address_row_0" data-id="0">
							<div class="form-group">
								<input type="text" name="allowed_ip_res[0]" class="form-control ip_input"  value="'.checkIsset($allowed_ip[0]).'">
								<label>IP Address</label>
								<p class="error"><span id="error_ip_address_0"></span></p>
							</div>
						</div>';
					 } 
				$twoFAUI.='
				</div>
				<div class="clearfix"></div>
				<div class="add_ip_address_row text-right">
					<button id="add_ip_address" type="button" class="btn btn-action">+ IP Address</button>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>';

		return $twoFAUI;
}
/**
 * Generate Common User Interface for Dynamic IP Address
 */
function generateIPAddressUI(){
	$IPUI = '';
	$IPUI .='
	<div id="dynamic_ip_address_div" style="display: none">
		<div class="ip_address_row" id="ip_address_row_~number~" data-id="~number~">
			<div class="phone-control-wrap">
			<div class="phone-addon">
				<div class="form-group">
					<input type="text" name="allowed_ip_res[~number~]" class="form-control ip_input" >
					<label>IP Address</label>
					<p class="error text-left"><span id="error_ip_address_~number~"></span></p>
				</div>
			</div>
			<div class="phone-addon">
				<div class="form-group">
				<a href="javascript:void(0);" class="text-light-gray remove_ip_address"  data-id="~number~">X</a>
			</div>
			</div>
		</div>
		</div>
	</div>';
	return $IPUI;
}
/**
 * Generate Common Javascript Code For 2FA And IP Address
 */
function generate2FactorAuthenticationJS(){
	$twoFAjs = '
		$(function(){
			$("#send_via_mobile").uniform();
			$("#send_via_email").uniform();
		});
		$(document).off("click", ".remove_ip_address");
		$(document).on("click", ".remove_ip_address", function(e){
			e.preventDefault();
			$add_counter = parseInt($("#ip_group_count").val()) - 1;
			if($add_counter <= 10){
				$("#add_ip_address").show();
			}
			$("#ip_group_count").val($add_counter);
			$("#ip_address_row_"+$(this).attr("data-id")).remove();
		});

		$(document).off("change", "#is_ip_restriction");
		$(document).on("change", "#is_ip_restriction", function () {
			if($(this).is(":checked")) {
				$(".ip_address_div").show();
			} else {
				$(".ip_address_div").hide();
			}
		});

		$(document).off("change", "#is_2fa");
		$(document).on("change", "#is_2fa", function () {
			if($(this).is(":checked")) {
				$(".2fa_div").show();
			} else {
				$(".2fa_div").hide();
			}
		});

		$(document).off("click", "#add_ip_address");
		$(document).on("click", "#add_ip_address", function(e){
			e.preventDefault();
			$add_counter = parseInt($("#ip_group_count").val()) + 1;
			if($add_counter >= 10){
				$(this).hide();
			}
			$("#ip_group_count").val($add_counter);
			loadIPAddressDiv();
		});

		loadIPAddressDiv = function(){
			$count = $("#account_detail .ip_address_row").length;
			$ip_display_counter = parseInt($("#ip_display_counter").val());
			$number = $count+1;
			if($ip_display_counter > $count){
				$number = $ip_display_counter + 1;
			}
			$neg_number = $number * -1;
			html = $("#dynamic_ip_address_div").html();
			$("#ip_address_row_div").append(html.replace(/~number~/g, $neg_number));
			$("#ip_display_counter").val($number);
		}
		
	';
	return $twoFAjs;
}
/**
 * Return Agency If account Type is Business(Agency), return Agent Downline If Agent Id And from 'Agent' is Passed in Arguments, if not found Anything Then it return Empty Select Dropdown
 */
function getAgencySelect($dropDownName,$agent_id='',$from='Admin'){
	global $pdo;

	$from = strtolower($from);
	$sch_params = $agencyArr = array();
	$selectControl = $incr = '';
	if($from == 'agent' && $agent_id!='') {
		$incr.=' AND (c.id=:id or (c.upline_sponsors LIKE :downline)) ';
		$sch_params[":id"] = $agent_id;
		$sch_params[":downline"] = '%,'.$agent_id.',%';
	}

	$agency_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,c.rep_id,cs.company_name as agencyNameDis
					FROM customer c
					JOIN customer_settings cs ON(cs.customer_id=c.id AND cs.account_type='Business' AND cs.company_name!='')
					WHERE c.type='Agent' AND c.is_deleted = 'N ' $incr ORDER BY agencyNameDis ASC";
	$agencyArr = $pdo->select($agency_sql,$sch_params);

	$selectControl .= '
			<div class="col-sm-6">
				<div class="form-group ">
						<select class="se_multiple_select listing_search" name="'.$dropDownName.'[]" id="'.$dropDownName.'" multiple="multiple">';
			if($from == 'Agent') {
							if(!empty($agencyArr)){ 
								foreach($agencyArr as $value){
									$selectControl .= '
							<option value="'.($value['id']).'">'.($value['rep_id']).' - '.($value['fname']).' '.($value['lname']) .'</option>';
								}
							}
							
			}else{
							if(!empty($agencyArr)){ 
								foreach($agencyArr as $value){
									$selectControl .= '
							<option value="'.($value['id']).'">'.($value['rep_id']).' - '.($value['agencyNameDis']) .'</option>';
								}
							}
			}
			$selectControl .= '
						</select>
						<label>Agency</label>
				</div>
			</div>
		';
	return $selectControl;
}
/**
 * Merge CSV Code for Local,Dev And Stag Environment Only.
 */
function generateMergeCSVJS(){

	$spinHtml = '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Merging Files"><i class="fa fa-spinner fa-spin"></i></a>';
	$toolTip = '$('."'".'[data-toggle="tooltip"]'."'".').tooltip()';
	$generateMergeCSVJS = '
		$(document).off("click",".merge_csv");
		$(document).on("click",".merge_csv",function(){
			var id = $(this).data("id");
			$(".mergeDiv"+id).html('."'".$spinHtml."'".');
			'.$toolTip.'
			mergeRequest(id);
		});

		mergeRequest = function(id){
			$("#ajax_loader").show();
			$.ajax({
				url: "report_export.php",
				dataType: "JSON",
				type: "POST",
				data: {
					id: id,
					action: "mergeCsv"
				},
				success: function(res) {
					$("#ajax_loader").hide();
					if (res.status == "success") {
						setNotifySuccess(res.message);
						setTimeout(function(){ window.location.reload(); },1000);
					}else{
						setNotifySuccess("Something went wrong!");
					}
				}
			});
		}
	';
	return $generateMergeCSVJS;
}
function check_base64_image($base64) {
	if(empty($base64)){
		return false;
	}
	$signature_file_name = rand(111,999).time().'.png';
  $img = getimagesizefromstring(base64_decode($base64));
  if (!$img) {
      return false;
  }
  if ($img[0] > 0 && $img[1] > 0 && $img['mime']) {
      return true;
  }
  return false;
}
function add_billing_request($operation,$billing_id) {
	global $pdo,$SITE_ENV,$AWS_REPORTING_URL;
	require_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
	if(isset($AWS_REPORTING_URL[$operation])) {
		$req_where = array(
	        "clause"=>"id=:id",
	        "params"=>array(
	          ":id"=>$billing_id,
	            )
	        );
	    $req_data = array(
	        'report_path' => $AWS_REPORTING_URL[$operation],
	    );
	    $pdo->update("billing_requests",$req_data,$req_where);

		$request_url = $AWS_REPORTING_URL[$operation]."&job_id=".$billing_id;
		
		if($SITE_ENV == "Local"){
			$ch = curl_init($request_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			$api_response = curl_exec($ch);
			curl_close($ch);

			$responseArray['api_response'] = $api_response;
		}else{
			$ch = curl_init($request_url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_exec($ch);
			// $api_response = curl_exec($ch);
			curl_close($ch);
		}
	}
	return $billing_id;
}
function add_eligibility_request($operation,$eligibility_id) {
	global $pdo,$SITE_ENV,$AWS_REPORTING_URL;
	require_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
	if(isset($AWS_REPORTING_URL[$operation])) {
		$req_where = array(
	        "clause"=>"id=:id",
	        "params"=>array(
	          ":id"=>$eligibility_id,
	            )
	        );
	    $req_data = array(
	        'report_path' => $AWS_REPORTING_URL[$operation],
	    );
	    $pdo->update("eligibility_requests",$req_data,$req_where);

		$request_url = $AWS_REPORTING_URL[$operation]."&job_id=".$eligibility_id;
		
		if($SITE_ENV == "Local"){
			$ch = curl_init($request_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			$api_response = curl_exec($ch);
			curl_close($ch);

			$responseArray['api_response'] = $api_response;
		}else{
			$ch = curl_init($request_url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_exec($ch);
			// $api_response = curl_exec($ch);
			curl_close($ch);
		}
	}
	return $eligibility_id;
}
function add_fulfillment_request($operation,$fulfillment_id) {
	global $pdo,$SITE_ENV,$AWS_REPORTING_URL;
	require_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
	if(isset($AWS_REPORTING_URL[$operation])) {
		$req_where = array(
	        "clause"=>"id=:id",
	        "params"=>array(
	          ":id"=>$fulfillment_id,
	            )
	        );
	    $req_data = array(
	        'report_path' => $AWS_REPORTING_URL[$operation],
	    );
	    $pdo->update("fulfillment_requests",$req_data,$req_where);

		$request_url = $AWS_REPORTING_URL[$operation]."&job_id=".$fulfillment_id;
		
		if($SITE_ENV == "Local"){
			$ch = curl_init($request_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			$api_response = curl_exec($ch);
			curl_close($ch);

			$responseArray['api_response'] = $api_response;
		}else{
			$ch = curl_init($request_url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_exec($ch);
			// $api_response = curl_exec($ch);
			curl_close($ch);
		}
	}
	return $fulfillment_id;
}
function get_available_term_date_for_healthy_step($ws_id){
	global $pdo;
	require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
	$enrollDate = new enrollmentDate();
	$terminationDates = array();

	if($ws_id){
		$prd_details = $pdo->selectOne("SELECT p.*,w.eligibility_date,w.start_coverage_period,w.end_coverage_period FROM website_subscriptions w JOIN prd_main p on(p.id = w.product_id) WHERE w.id = :id",array(":id" => $ws_id));
		if($prd_details){
			if($prd_details['is_member_benefits'] == "Y" && $prd_details['is_fee_on_renewal'] == "Y" && $prd_details['fee_renewal_type'] == "Renewals" && $prd_details['fee_renewal_count'] > 0) {
				$tmp_fee_renewal_count = $prd_details['fee_renewal_count'];
				$tmp_start_coverage_date = $prd_details['start_coverage_period'];
				$tmp_termination_date = $prd_details['end_coverage_period'];
				while ($tmp_fee_renewal_count > 0) {
					$product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$prd_details['payment_type_subscription']);
					$tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
					$tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
					$tmp_fee_renewal_count--;
				}
				$terminationDates['start_termination_date'] = $prd_details['eligibility_date'];
				$terminationDates['end_termination_date'] = $tmp_termination_date;
			}
		}
	}
	return $terminationDates;
}
function lead_tracking($lead_id=0,$customer_id=0,$params=array()){
	global $pdo;

	$lead_param = array();

	if($lead_id > 0){
		$lead_param['lead_id'] = $lead_id;
	}
	if($customer_id > 0){
		$lead_param['customer_id'] = $customer_id;	
	}
	if($params['status']){
		$lead_param['status'] = $params['status'];	
	}
	if($params['description']){
		$lead_param['description'] = $params['description'];	
	}

	if($lead_param){
		$pdo->insert('lead_tracking',$lead_param);
	}
}
/**
 * ***** Set Group Member's Policy Termination Date *****
 * int $groupId Unencrypted
 */
function updateGroupMemberPolicy(int $groupId, string $terminationDate, string $reason, array $productId = array(),bool $generateListBill = false,$location = 'Admin',$sponsor_billing_method){
	global $pdo;

	$id = $groupId;
	$productIds = !empty($productId) ? $productId : array();
	$reason = $reason;
	$billing_method = $sponsor_billing_method;
	$incr = "";
	if(!empty($productIds)){
		$incr = " AND ws.product_id IN(".implode(',',$productIds).")";
	}

	$selGroupMember = "SELECT ws.eligibility_date,ws.id as ws_id,ws.website_id,ws.termination_date,ws.end_coverage_period
			FROM customer c 
			JOIN customer s ON(s.id=c.sponsor_id AND s.type='Group')
			JOIN website_subscriptions ws ON(ws.customer_id=c.id AND (ws.termination_date IS NULL OR DATE(ws.termination_date) > CURDATE()) AND IF(ws.termination_date IS NOT NULL AND ws.termination_date !='', DATE(ws.termination_date) != DATE(ws.eligibility_date),1))
			WHERE s.id=:sponsor_id AND c.type='customer' $incr ";
	$groupMembers = $pdo->select($selGroupMember,array(":sponsor_id"=>$id));
	if(!empty($groupMembers)){
		include_once dirname(__DIR__) .'/includes/policy_setting.class.php';
		$policySetting = new policySetting();
		
		$termination_date = '';
		if($terminationDate == 'end_of_month'){
			$termination_date = '';
		}

		foreach($groupMembers as $member){
			
			if(($billing_method == 'TPA' || $billing_method == 'list_bill') && $terminationDate == 'end_of_month'){
				if(date('d',strtotime($member['eligibility_date']))==01){
					$termination_date = !empty($member['termination_date']) ? $member['termination_date'] : date("Y-m-t");
				}else{
					$termination_date = !empty($member['termination_date']) ? $member['termination_date'] : $member['end_coverage_period'];
				}
			}

			if($terminationDate == 'back_to_initial_start_period'){
				$termination_date = $member['eligibility_date'];
			}

			$extra_params = array();
			$extra_params['location'] = "change_product_status";
			$extra_params['portal'] = $location;
			$policySetting->setTerminationDate($member['ws_id'],$termination_date,$reason,$extra_params);
		}
	}

	//Generate list Bill
		if($generateListBill){
			ini_set('memory_limit', '-1');
			ini_set('max_execution_time',0);
			require_once dirname(__DIR__) . '/includes/list_bill.class.php';
			$ListBill = new ListBill();
			$extra = array();
			$extra['term_regenerate'] = 'Y';
			$list_bill_id_arr = $ListBill->generateListBill(false,$id,'', '', $extra);
			$sendEmailSummary = array();
			if(!empty($list_bill_id_arr)){
				$trigger_param = array(
					'Inseted List bill Id' => implode(",", $list_bill_id_arr)
				);
				$sendEmailSummary[] = $trigger_param;
			}

			$DEFAULT_LIST_BILL_EMAIL = array('karan@cyberxllc.com');
			trigger_mail_to_email($sendEmailSummary, $DEFAULT_LIST_BILL_EMAIL, "Generate List Bill ID", array(), 2);
		}
	//Generate list Bill
}
/**
 * Insert Group Company Billing Profile
 */
function insertGroupCompanyBillingProfile($group_id, $company_id){
	global $pdo;
	$REAL_IP_ADDRESS = get_real_ipaddress();
	$group_bill_sql = "SELECT id FROM customer_billing_profile where customer_id=:customer_id and company_id = :company_id";
	$group_bill_row = $pdo->selectOne($group_bill_sql, array(":customer_id" => $group_id,":company_id"=>$company_id));
	
	$clone_bill_sql = "SELECT * FROM customer_billing_profile where customer_id=:customer_id AND is_default='Y' AND is_deleted='N'  ORDER BY company_id";
	$clone_bill_row = $pdo->selectOne($clone_bill_sql, array(":customer_id" => $group_id));

	if(!empty($clone_bill_row)){
		$bill_params = array(
			'company_id'=>$company_id,
			'customer_id'=>$group_id,
			"fname" => $clone_bill_row['fname'],
			"lname" => $clone_bill_row['lname'],
			"email" => $clone_bill_row['email'],
			"phone" => $clone_bill_row['phone'],
			'country_id' => '231',
			'country' => "United States",
			'listbill_enroll' => $clone_bill_row['listbill_enroll'],
			'is_default' => 'N',
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
		);

		$payment_type = $bill_params['payment_mode'] = $clone_bill_row['payment_mode'];
			
		if ($payment_type == "ACH") {
			$bill_params['fname'] = $clone_bill_row['fname'];
			$bill_params['ach_account_number'] = $clone_bill_row['ach_account_number'];
			$bill_params['ach_routing_number'] = $clone_bill_row['ach_routing_number'];
			$bill_params['ach_account_type'] = $clone_bill_row['ach_account_type'];
			$bill_params['bankname'] = $clone_bill_row['bankname'];
			$bill_params['last_cc_ach_no'] = $clone_bill_row['last_cc_ach_no'];
		} else if ($payment_type == "CC") {
			$bill_params['fname'] = $clone_bill_row['fname'];
			
			$bill_params['state'] = $clone_bill_row['state'];
			$bill_params['city'] = $clone_bill_row['city'];
			$bill_params['zip'] = $clone_bill_row['zip'];
			$bill_params['address'] = $clone_bill_row['address'];
			$bill_params['address2'] = $clone_bill_row['address2'];
			$bill_params['is_valid_address'] = $clone_bill_row['is_valid_address'];
			$bill_params['cvv_no'] = $clone_bill_row['cvv_no'];

			$bill_params['card_no'] = $clone_bill_row['card_no'];
			$bill_params['card_no_full'] = $clone_bill_row['card_no_full'];
			$bill_params['card_type'] = $clone_bill_row['card_type'];
			$bill_params['expiry_month'] = $clone_bill_row['expiry_month'];
			$bill_params['expiry_year'] = $clone_bill_row['expiry_year'];
			$bill_params['last_cc_ach_no'] = $clone_bill_row['last_cc_ach_no'];
		}
		
		if (!empty($group_bill_row)) {
			$pdo->update("customer_billing_profile", $bill_params, array("clause" => "id=:id", "params" => array(":id" => $group_bill_row['id'])));
		}else{
			$pdo->insert("customer_billing_profile", $bill_params);
			return true;
		}
	}else{
		return false;
	}
}
function dispReversalAmt($amount,$class=''){
	$revAmt = 0;
	if(abs($amount) > 0){
		$amount = abs($amount);
		$revAmt = "<span class='text-red'>(".displayAmount($amount).")</span>";
	}else{
		$revAmt = "<span class='".$class."'>".displayAmount($amount)."</span>";
	}
	return $revAmt;
}
function getAllUsers($type = ''){
    global $pdo;
	if($type == 'Admin' || $type ==''){
    $admins_list = $pdo->select("SELECT id,display_id,CONCAT(fname,' ',lname) as name FROM admin WHERE status != 'Pending' AND is_deleted = 'N'");
	}
	if($type == 'Agent' || $type ==''){
    $agents = $pdo->select("SELECT id,rep_id,CONCAT(fname,' ',lname) as name FROM customer WHERE type = 'Agent' AND status IN('Active') AND is_deleted = 'N'");
	}
	if($type == 'Group' || $type ==''){
    $groups = $pdo->select("SELECT id,rep_id,CONCAT(fname,' ',lname) as name FROM customer WHERE type = 'Group' AND status IN('Active') AND is_deleted = 'N'");
	}
	if($type == 'Member' || $type ==''){
    $customers = $pdo->select("SELECT id,rep_id,CONCAT(fname,' ',lname) as name FROM customer WHERE type = 'Customer' AND status IN('Active','Inactive') AND is_deleted = 'N'");
	}
	if($type == 'Lead' || $type ==''){
    $leads = $pdo->select("SELECT id,lead_id,CONCAT(fname,' ',lname) as name FROM leads WHERE status NOT IN('Converted') AND is_deleted = 'N'");
	}
    $all_users = array();

    if(isset($admins_list)){
        foreach ($admins_list as $key => $value) {
            $all_users['Admins'][] = array('id' => $value['id'],'label' => $value['display_id'] . ' - ' . $value['name']);
        }
    }
    if(isset($agents)){
        foreach ($agents as $key => $value) {
            $all_users['Agents'][] = array('id' => $value['id'],'label' => $value['rep_id'] . ' - ' . $value['name']);
        }
    }
    if(isset($groups)){
        foreach ($groups as $key => $value) {
            $all_users['Groups'][] = array('id' => $value['id'],'label' => $value['rep_id'] . ' - ' . $value['name']);
        }
    }
    if(isset($customers)){
        foreach ($customers as $key => $value) {
            $all_users['Members'][] = array('id' => $value['id'],'label' => $value['rep_id'] . ' - ' . $value['name']);
        }
    }
    if(isset($leads)){
        foreach ($leads as $key => $value) {
            $all_users['Leads'][] = array('id' => $value['id'],'label' => $value['lead_id'] . ' - ' . $value['name']);
        }
    }

    return $all_users;
}
function get_downline_agents($sponsor_id,$str_return = false,$include_self = true)
{
	global $pdo;
	$agents = array();
	if(!empty($sponsor_id)) {
			$agents_row = $pdo->selectOne("SELECT group_concat(DISTINCT c.id) as ids FROM customer c WHERE type='Agent' AND upline_sponsors LIKE '%,$sponsor_id,%' AND is_deleted='N'");
			if(!empty($agents_row['ids'])) {
				$agents =	explode(",",$agents_row['ids']);
			}
	}
	if($include_self) {
			$agents[] = $sponsor_id;
	}
	if($str_return)	 {
			$agents_return = implode(',',$agents);
	} else {
			$agents_return = $agents;
	}
	return $agents_return;
}
function get_direct_loa_agents($sponsor_id,$str_return = false,$include_self = true)
{
		global $pdo;
		$agents = array();
		if(!empty($sponsor_id)) {
				$agents_row = $pdo->selectOne("SELECT group_concat(c.id) as ids FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE type='Agent' AND agent_coded_level='LOA' AND sponsor_id=:sponsor_id AND is_deleted='N'",array(':sponsor_id' => $sponsor_id));
				if(!empty($agents_row['ids'])) {
					$agents =	explode(",",$agents_row['ids']);
				}
		}
		if($include_self) {
				$agents[] = $sponsor_id;
		}
		if($str_return)	 {
				$agents_return = implode(',',$agents);
		} else {
				$agents_return = $agents;
		}
		return $agents_return;
}

function agent_has_menu_feature_access($access,$agent_id){
		global $pdo;
	    $flag = false;
	    
	    $sel_sql = "SELECT c.feature_access FROM customer c  WHERE c.id=:agent_id";
	    $params = array(":agent_id" => $agent_id);
	    $agent_row = $pdo->selectOne($sel_sql, $params);
	    $featureArr  = !empty($agent_row['feature_access']) ? explode(',',$agent_row['feature_access']) : array();
	    if (is_array($access)) {
	        if (isset($featureArr) && $featureArr != "") {
	            if (count(array_intersect($access, array_values($featureArr)))) {
	                $flag = true;
	            }
	        } else {
	            $flag = false;
	        }
	    } else {
	        if (isset($featureArr) && $featureArr  != "") {
	            if (in_array($access, array_values($featureArr))) {
	                $flag = true;
	            }elseif(is_array($featureArr) && in_array($access, $featureArr)){
	                $flag = true;
	            }
	        } else {
	            $flag = false;
	        }
	    }
	    return $flag;
	}

function displayNumber($number){
	if ($number != "" && is_numeric($number)) {
		if ($number < 0) {
			return '-' . number_format($number);
		} else {
			return number_format($number);
		}
	} else {
		return number_format(0);
	}
}
function getOldProductNBOrderDetails($customerId,$productId,$parentCoverageId){
	global $pdo;

	$ws_res = $pdo->selectOne("SELECT w.id,w.product_id,w.parent_ws_id
		FROM website_subscriptions w
		WHERE w.id = :parent_ws_id",array(":parent_ws_id" => $parentCoverageId));

	if(!empty($ws_res['product_id']) && $ws_res['parent_ws_id'] > 0){
		return getOldProductNBOrderDetails($customerId,$ws_res['product_id'],$ws_res['parent_ws_id']);
	}else{
		return array('product_id' => $ws_res['product_id'],'website_id'=>$ws_res['id']);
	}
}

function getRenewalDetails($customer_id = 0,$extra_params = array()){
	global $pdo;
	include_once dirname(__DIR__) .'/includes/member_enrollment.class.php';
	include_once dirname(__DIR__) .'/includes/enrollment_dates.class.php';
	$MemberEnrollment = new MemberEnrollment();
	$enrollDate = new enrollmentDate();

  $grandTotal = $subTotal = 0;
  $lastFailOrderId = 0;
  $isAttemptOrder = false;

  $plan_ids_arr = array();
  $productWiseInformation = array();
  $renewalCountsArr = array();

  $selSql = "SELECT w.id,w.last_order_id,w.start_coverage_period,w.end_coverage_period,w.price as subs_price,p.id as prd_matrix_id,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.payment_type_subscription as member_payment_type,w.product_id,w.prd_plan_type_id,w.website_id,w.plan_id,w.customer_id,w.total_attempts,w.price
            FROM website_subscriptions w
            JOIN customer c on (c.id=w.customer_id)
            JOIN prd_main pm ON (pm.id=w.product_id)
            JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
            JOIN customer_enrollment ce ON (ce.website_id=w.id)
            WHERE c.status IN('Active') AND c.type='Customer' AND DATE(w.next_purchase_date)=:next_purchase_date AND w.total_attempts=0
            AND w.status in('Active') AND c.id=:customer_id
            AND pm.type!='Fees'
            GROUP BY w.id";
  $selParams = array(":customer_id" =>$customer_id,':next_purchase_date' => date('Y-m-d',strtotime($extra_params['next_purchase_date'])));
  $ProfileRows = $pdo->select($selSql, $selParams);

  if ($ProfileRows) {
    foreach ($ProfileRows as $key => $row){

      $member_payment_type = $row['member_payment_type'];

      //Check if benifit tire change Or renewal fail on next attempt
      if(empty($row['last_order_id'])){
        $startCoveragePeriod = date('Y-m-d',strtotime($row['start_coverage_period']));
        $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
      } else {
        $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
        $startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));
        $product_dates=$enrollDate->getCoveragePeriod($startDate,$member_payment_type);

        $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
        $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

        $selectOrder = $pdo->selectOne("SELECT od.start_coverage_period,od.end_coverage_period FROM order_details od JOIN orders o on(o.id = od.order_id) WHERE o.id = :order_id AND o.status in('Void','Refund','Cancelled','Chargeback') AND od.website_id = :website_id",array(":order_id" => $row['last_order_id'],':website_id' => $row['id']));

        if($selectOrder){
          if(strtotime($selectOrder['start_coverage_period']) > strtotime($today)){
            $startCoveragePeriod = $selectOrder['start_coverage_period'];
            $endCoveragePeriod = $selectOrder['end_coverage_period'];
          }
        }

      }

      //checking for plan change in current or next month
      if ($row['process_status'] == 'Pending' && !empty($row['new_plan_id']) && !empty($row['tier_change_date'])) {
          
        $tire_change_date = $row['tier_change_date'];

        if (strtotime($startCoveragePeriod) <= strtotime($tire_change_date) && strtotime($tire_change_date) <= strtotime($endCoveragePeriod)) {

          $new_ws_sql = "SELECT ce.id as ce_id,ws.* 
                          FROM customer_enrollment ce 
                          JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                          WHERE
                          ce.parent_coverage_id=:parent_coverage_id AND 
                          ws.status='Pending' AND 
                          ce.process_status='Pending'";
          $new_ws_row = $pdo->selectOne($new_ws_sql, array(":parent_coverage_id" => $row['ce_id']));
          if(!empty($new_ws_row)) {
            $ProfileRows[$key]['id'] = $new_ws_row['id'];
            $ProfileRows[$key]['website_id'] = $new_ws_row['website_id'];
            $ProfileRows[$key]['product_id'] = $new_ws_row['product_id'];
            $ProfileRows[$key]['prd_plan_type_id'] = $new_ws_row['prd_plan_type_id'];
            $ProfileRows[$key]['plan_id'] = $new_ws_row['plan_id'];
            $ProfileRows[$key]['prd_matrix_id'] = $new_ws_row['plan_id'];
            $ProfileRows[$key]['subs_price'] = $new_ws_row['price'];
            $ProfileRows[$key]['price'] = $new_ws_row['price'];
            $ProfileRows[$key]['old_ws_id'] = $row['id'];
          }
        }
      }

      // Check Termination Date set for subscription
      if (!empty($row['termination_date']) && strtotime($row['termination_date']) > 0) {

        //checking for Termination Date
        /*------ Check New Plan Created Or Not ---------*/
        $new_ws_sql = "SELECT ce.id as ce_id 
                      FROM customer_enrollment ce 
                      JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                      WHERE
                      ws.plan_id=:plan_id AND
                      ce.parent_coverage_id=:parent_coverage_id AND 
                      ws.status='Pending' AND 
                      ce.process_status='Pending'";
        $new_ws_row = $pdo->selectOne($new_ws_sql, array(":plan_id" => $row['new_plan_id'],":parent_coverage_id" => $row['ce_id']));
        if (empty($new_ws_row['ce_id'])) { // Not Created New Plan
            $term_date = $row['termination_date'];                        
            if (strtotime($term_date) < strtotime($startCoveragePeriod)) {
                unset($ProfileRows[$key]);
                $terminatedPolicy++;
                continue;
            }
        }
        /*------ Check New Plan Created Or Not ---------*/
      }

      $pricing_change = get_renewals_new_price($row['id']);
      if($pricing_change['pricing_changed'] == 'Y'){
        $new_ws_data = $pricing_change['new_ws_row'];

        $ProfileRows[$key]['id'] = $new_ws_data['id'];
        $ProfileRows[$key]['product_id'] = $new_ws_data['product_id'];
        $ProfileRows[$key]['plan_id'] = $new_ws_data['plan_id'];
        $ProfileRows[$key]['prd_matrix_id'] = $new_ws_data['plan_id'];
        $ProfileRows[$key]['subs_price'] = $new_ws_data['price'];
        $ProfileRows[$key]['price'] = $new_ws_data['price'];
      }

      $index = $ProfileRows[$key]["product_id"] . "-" . $ProfileRows[$key]["prd_matrix_id"];
      
      if (!isset($productWiseInformation[$index])) {
        $productWiseInformation[$index] = array();
        $productWiseInformation[$index]['qty'] = 1;
      }else{
        $productWiseInformation[$index]['qty'] = $productWiseInformation[$index]['qty'] + 1;
      }

      $renewalCountsArr[$ProfileRows[$key]["product_id"]] = ($ProfileRows[$key]["renew_count"] + 1);

      $prdPrice = $ProfileRows[$key]['price'];
      $subsPrice = $ProfileRows[$key]['subs_price'];
      $subTotal += $prdPrice;
                      
      $productWiseInformation[$index]["subTotal"] = $prdPrice;
      $productWiseInformation[$index]["grandTotal"] = $subsPrice;
     
      $site_load = 'USA';
      $price_tag = "$";

      $plan_ids_arr[$ProfileRows[$key]['product_id']] = $ProfileRows[$key]['plan_id'];
    }
  }

  if(empty($productWiseInformation)){
  	return false;
  }
  // selecting service fee code start
    $serviceFeePrice = 0;
    $serviceFee = $MemberEnrollment->getRenewalServiceFee($plan_ids_arr,$extra_params['id'],$extra_params["sponsor_id"],$subTotal,'Members',"N","Y",$renewalCountsArr);

    if(!empty($serviceFee)){
      $serviceFeeRow = $serviceFee[0];
      
      if(!empty($serviceFeeRow)){
        $serviceFeePrice = $serviceFee["total"];
      }
    }
  // selecting service fee code ends

  // selecting membership fee code start
    $membershipFeePrice = 0;
    $membershipFee = $MemberEnrollment->getRenewalMembershipFee($plan_ids_arr,$extra_params['id'],$extra_params["zip_code"],"N","Y",$renewalCountsArr);

    if(!empty($membershipFee)){
        $membershipFeePrice = $membershipFee['total'];
        unset($membershipFee['total']);
    }
  // selecting membership fee code ends

  // selecting vendor/carrier/product fee code start
    $linkedFeeTotal = 0;
    
    $linkedFee = $MemberEnrollment->getRenewalLinkedFee($plan_ids_arr,$extra_params['id'],$extra_params["sponsor_id"],"N","Y",$renewalCountsArr);
    if(!empty($linkedFee)){
	    $linkedFeeTotal = $linkedFee['total'];
	    unset($linkedFee['total']);
  	}
  // selecting vendor/carrier/product fee code ends

  $product_total = $subTotal;
        
  $subTotal = $product_total + $linkedFeeTotal + $membershipFeePrice;
  $grand_total = $subTotal + $serviceFeePrice;

  $grandTotal = number_format($grand_total, 2, ".", "");

  $returnArr = [
  	'ProfileRows' => !empty($ProfileRows) ? $ProfileRows : [],
  	'productWiseInformation' => !empty($productWiseInformation) ? $productWiseInformation : [],
  	'plan_ids_arr' => !empty($plan_ids_arr) ? $plan_ids_arr : [],
  	'subsPrice' => !empty($subsPrice) ? $subsPrice : 0,
  	'subTotal' => !empty($subTotal) ? $subTotal : 0,
  	'serviceFeeRow' => !empty($serviceFeeRow) ? $serviceFeeRow : [],
  	'serviceFeePrice' => !empty($serviceFeePrice) ? $serviceFeePrice : 0,
  	'membershipFee' => !empty($membershipFee) ? $membershipFee : [],
  	'membershipFeePrice' => !empty($membershipFeePrice) ? $membershipFeePrice : 0,
  	'linkedFee' => !empty($linkedFee) ? $linkedFee : [],
  	'linkedFeeTotal' => !empty($linkedFeeTotal) ? $linkedFeeTotal : 0,
  	'grand_total' => !empty($grandTotal) ? $grandTotal : 0,
  	'next_purchase_date' => !empty($extra_params['next_purchase_date']) ? date('m/d/Y',strtotime($extra_params['next_purchase_date'])) : '',
  ];

  return $returnArr;
}

function checkPaymentButtonDisplay($group_id = 0){
	global $pdo;

	$check_variation_display_sql = "SELECT id,is_check,is_cc,is_ach FROM group_pay_options WHERE group_id=:group_id AND rule_type = 'Variation' AND is_deleted = 'N'";
	$check_variation_display_res = $pdo->selectOne($check_variation_display_sql,[':group_id' => $group_id]);

	if(!empty($check_variation_display_res)){
		if($check_variation_display_res['is_check'] == 'Y' && $check_variation_display_res['is_cc'] == 'N' && $check_variation_display_res['is_ach'] == 'N'){
			return false;
		} else {
			return true;
		}
	} else {
		$check_global_display_sql = "SELECT id,is_check,is_cc,is_ach FROM group_pay_options WHERE rule_type = 'Global' AND is_deleted = 'N'";
		$check_global_display_res = $pdo->selectOne($check_global_display_sql);
		if(!empty($check_global_display_res)){
			if($check_global_display_res['is_check'] == 'Y' && $check_global_display_res['is_cc'] == 'N' && $check_global_display_res['is_ach'] == 'N'){
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
}
function getstate($table, $id, $name, $compvar = "id", $compvar2 = "name") {
	global $pdo;
	$getsql = "SELECT $name from $table where $compvar=:id or $compvar2=:id";
	$params = array(
		':id' => $id,
	);

	$row = $pdo->selectOne($getsql, $params);
	return $row ? $row[$name] : '';
}
function checkNumberSet(&$fieldName) {
	if (isset($fieldName)) {
		return $fieldName;
	} else {
		return 0;    
	}
}
function set_communication_schedule($trigger_id,$action,$user_id,$user_type,$specific = '',$products =array(),$extra=array()){
	global $pdo;

	$schedule_id = 0;
	if(empty($trigger_id)){
		return $schedule_id;
	}

	$trigger_res = $pdo->selectOne("SELECT trigger_delay_type,numbers_to_delay,time_units,delay_until_date FROM triggers WHERE id = :id AND is_deleted='N' AND status='Active'",array(":id" => $trigger_id));

	if(empty($trigger_res)){
		return $schedule_id;
	}	
	
	$current_datetime = date('Y-m-d H:i:s');
	$trigger_delay_type = isset($trigger_res['trigger_delay_type']) ? $trigger_res['trigger_delay_type'] : '';
	$delay_for = isset($trigger_res['numbers_to_delay']) ? $trigger_res['numbers_to_delay'] : '';
	$time_unit = isset($trigger_res['time_units']) ? $trigger_res['time_units'] : '';
	$delay_until_date = isset($trigger_res['delay_until_date']) ? date('Y-m-d',strtotime($trigger_res['delay_until_date'])) : '';
	$schedule_date = '';


	if(!empty($trigger_delay_type)){
		if($trigger_delay_type == 'Relative' && !empty($delay_for) && !empty($time_unit)){
			$schedule_date = date('Y-m-d H:i:s',strtotime($current_datetime ."+$delay_for $time_unit"));
		}else if($trigger_delay_type == 'Exact Date' && !empty($delay_until_date) && strtotime($current_datetime) < strtotime($delay_until_date)){
			$schedule_date = date('Y-m-d H:i:s',strtotime($delay_until_date));
		}
		if($schedule_date){

			$insert_params = array(
				'action' => $action,
				'user_id' => $user_id,
				'user_type' => $user_type,
				'specifically' => $specific,
				'products' => json_encode($products),
				'extra' => json_encode($extra),
				'schedule_date' => $schedule_date,
				'updated_at' => 'msqlfunc_NOW()',
				'created_at' => 'msqlfunc_NOW()'
			);

			$schedule_id = $pdo->insert('communication_delay_requests',$insert_params);
			
		}
	}
	
	return $schedule_id;
}

function fetch_public_holidays($curDate){

	$Year=date('Y',strtotime($curDate));
	$publicHolidays=array();
    $curDate = date("Y-m-d", strtotime("-1 months"));
	//Provide Holidays from one month minus than current month to next year current month (if cur month-year = Oct 2022, provide holidays from sep 2022 - oct 2023 )

	//New Year Date
	$publicHolidays[]=( strtotime(date($Year.'-01')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-01-01',strtotime('+ 1 year',strtotime($curDate))) : date($Year.'-01-01'); 
	//Martin Luther King, Jr. Day
	$publicHolidays[]=( strtotime(date($Year.'-01')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-m-d',strtotime("third monday of january ".date('Y',strtotime('+ 1 year',strtotime($curDate))) )) : date($Year.'-m-d',strtotime("third monday of january ".$Year));
	//President’s Day (George Washington’s Birthday)
	$publicHolidays[]=( strtotime(date($Year.'-02')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-m-d',strtotime("third monday of february ".date('Y',strtotime('+ 1 year',strtotime($curDate))) )) : date($Year.'-m-d',strtotime("third monday of february ".$Year)); 
	//Memorial Day
	$publicHolidays[]=( strtotime(date($Year.'-05')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-m-d',strtotime("last monday of may ".date('Y',strtotime('+ 1 year',strtotime($curDate))) )) : date($Year.'-m-d',strtotime("last monday of may ".$Year)); 
	//Juneteenth
	$publicHolidays[]=( strtotime(date($Year.'-06')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-06-19',strtotime('+ 1 year',strtotime($curDate))) : date($Year.'-06-19'); 
	//Independence Day	
	$publicHolidays[]=( strtotime(date($Year.'-07')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-07-04',strtotime('+ 1 year',strtotime($curDate))) : date($Year.'-07-04'); 
	//Labor Day
	$publicHolidays[]=( strtotime(date($Year.'-09')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-m-d',strtotime("first monday of september ".date('Y',strtotime('+ 1 year',strtotime($curDate))) )) : date($Year.'-m-d',strtotime("first monday of september ".$Year)); 
	//Indigenous Peoples’ Day (also observed as Columbus Day)
	$publicHolidays[]=( strtotime(date($Year.'-10')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-m-d',strtotime("second monday of october ".date('Y',strtotime('+ 1 year',strtotime($curDate))) )) : date($Year.'-m-d',strtotime("second monday of october ".$Year)); 
	//Veterans Day
	$publicHolidays[]=( strtotime(date($Year.'-11')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-11-11',strtotime('+ 1 year',strtotime($curDate))) : date($Year.'-11-11'); 
	//Thanksgiving Day
	$publicHolidays[]=( strtotime(date($Year.'-11')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-m-d',strtotime("fourth Thursday of november ".date('Y',strtotime('+ 1 year',strtotime($curDate))) )) : date($Year.'-m-d',strtotime("fourth Thursday of november ".$Year)); 
	//Christmas Day
	$publicHolidays[]=( strtotime(date($Year.'-12')) < strtotime(date('Y-m',strtotime($curDate))) ) ? date('Y-12-25',strtotime('+ 1 year',strtotime($curDate))) : date($Year.'-12-25'); 


	return $publicHolidays;
}

function compareDate($date1, $date2){
    return strtotime($date1) - strtotime($date2);
}

function get_working_buisness_day()
{
	$day = strtolower(date('D', strtotime('+1 day')));
	$date = date('ymd', strtotime('+1 day'));
	if ($day == 'sat') {
		 $date = date('ymd', strtotime('+3 day'));
	} else if ($day == 'sun') {
		 $date = date('ymd', strtotime('+1 day'));
	}
	
	$date = date('ymd', strtotime('+2 day'));
	return $date;
}

//Check Credit card number using luhn algorithm
function is_valid_luhn($number,$type = '') {
	global $SITE_ENV;
	if ($number == '4111111111111114' || $number == '4111111111111113') {
        return 1;
	}
    settype($number, 'string');
    $sumTable = array(
      array(0,1,2,3,4,5,6,7,8,9),
      array(0,2,4,6,8,1,3,5,7,9));
    $sum = 0;
    $flip = 0;
    for ($i = strlen($number) - 1; $i >= 0; $i--) {
      $sum += $sumTable[$flip++ & 0x1][$number[$i]];
    }
    $is_valid = ($sum % 10 == 0) ? 'true' : 'false';
	
	if($is_valid == 'true'){
        if(!empty($number) && !empty($type)){
             $check_card_type = cc_type_pair($number,$type);
			 return $check_card_type;
		}else{
			return true;
		}
	}else{
        return false;
	}
	
}
function dbConnectionClose(){
	global $pdo;
	$pdo->closeConnection();
	$pdo=null;
}
function cc_type_pair($cc, $type) {
	if(isset($cc) && isset($type)) {
		$cards = array(
			'VISA' => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            'MASTERCARD' =>  "/^5[1-5][0-9]{14}$/",
            'DISCOVER' =>  "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
            'AMEX' => "/^3[47][0-9]{13}$/",
		);
		$type = strtoupper($type);
		if(isset($cards[$type]) && preg_match($cards[$type], $cc)){
			return true;
		}else{
			return false;
		}
	}
	return false;
}

function cvv_type_pair($cvv, $card_type) {
	$result = true;
	if(!empty($cvv) && !empty($card_type) && ($cvv != '000' && $cvv != '0000')){
		$cards = array(
			'VISA' => 3,
			'MASTERCARD' => 3,
			'DISCOVER' => 3,
			'AMEX' => 4
	    );

		$type = strtoupper($card_type);
		if(isset($cards[$type]) && strlen($cvv) == $cards[$type]){
			$result = true;
		}else{
			$result = false;
		}
	}else{
		$result = false;
	}
	return $result;
}

//this function using get amdin fee if product type type is variable by enrolle and variable by pricing
function getAdminFee($wsCusId,$wsId,$planTypeId,$isOldFee = false,$other_params){

    global $pdo;
    require_once dirname(__DIR__) . '/includes/benefit_tier_change_function.php';
    require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
	require_once dirname(__DIR__) . '/includes/member_setting.class.php';
	require_once dirname(__DIR__) . '/includes/function.class.php';
	$memberSetting = new memberSetting();
	$functionClass = new functionsList();
    $MemberEnrollment = new MemberEnrollment();

	$today = date('Y-m-d');
    $is_new_order = $is_renewal = 'N';
	$new_ws_data = $fee_plan_id = [];
	$action = $other_params['action'];
	$sumFeePrice = 0;
	$fee_prd_id = $fee_prd_name = '';

	$planIds = $other_params['mainPrdPlanId'];
    $mainPrdId = $other_params['mainPrdId'];
    $product_matrix[$mainPrdId] = $planIds;
	$sponsor_id = $other_params['sponser_id'];
    $renewCount = $other_params['renew_count'];
	$change_date = $other_params['change_date'];
    if($renewCount == 0){
    	$is_new_order = 'Y';
    }else{
    	$is_renewal = 'Y';
    }

	$nbOrder = getNBOrderDetails($wsCusId,$mainPrdId);
    $nbOrderDate = !empty($nbOrder["orderDate"]) ? date("Y-m-d",strtotime($nbOrder["orderDate"])) : $today;

	// Admin linked fee
	$adminLinkedFee = $MemberEnrollment->getLinkedFee($product_matrix,$sponsor_id,$is_new_order,$is_renewal,$renewCount,$nbOrderDate,'Y');
	unset($adminLinkedFee["total"]);
	unset($adminLinkedFee['total_single']);
	unset($adminLinkedFee['total_annually']);
	if(!empty($adminLinkedFee)){
		foreach ($adminLinkedFee as $fee) {
			$sumFeePrice += $fee['price'];
			array_push($fee_plan_id, $fee['matrix_id']);
			$fee_prd_id = $fee['product_id'];
			$is_benefit_tier = $fee['is_benefit_tier'];
			$fee_prd_name = $fee['product_name'];
		}
		$matrix_id = implode(",",$fee_plan_id);

		$planTypeId = ($is_benefit_tier == 'Y') ? $planTypeId : 0;
    }

	$member_setting = $memberSetting->get_status_by_change_benefit_tier();
	$new_ws_status = $member_setting['member_status'];
	$new_ce_process_status = "Pending";
	$old_ce_process_status = "Pending";

	//Inserting Code new fee

	if($isOldFee){
		$ws_sql = "SELECT id,customer_id,prd_plan_type_id,product_id,price,eligibility_date,status FROM website_subscriptions WHERE id=:id";
		$ws_row = $pdo->selectOne($ws_sql, array(":id" => $wsId));
		$old_prd_row = get_product_row($ws_row['product_id']);
		$old_plan_row = array(
			'product_id' => $ws_row['product_id'],
			'plan_type_title' => getPlanName("",$ws_row['prd_plan_type_id']),
			'product_name' => $old_prd_row['name'],
		);
	}

	$newPlanWs = (!$isOldFee) ? $wsId : $other_params['plan_new_ws_id'];
	if(!empty($newPlanWs) && !empty($adminLinkedFee) ){
		$newPlanSql = "SELECT * FROM website_subscriptions WHERE id=:id";
		$newPlanRes = $pdo->selectOne($newPlanSql, array(":id" => $newPlanWs));

		$new_prd_row = get_product_row($fee_prd_id);
		$new_plan_row = array(
			'id' => $matrix_id,
			'plan_id' => $matrix_id,
			'product_id' => $fee_prd_id,
			'plan_type' => $planTypeId,
			'prd_plan_type_id' => $planTypeId,
			'price' => $sumFeePrice,
			'member_price' => 0,
			'group_price' => 0,
			'display_member_price' => 0,
			'display_group_price' => 0,
			'contribution_type' => '',
			'contribution_value' => 0,
			'product_code' => $new_prd_row['product_code'],
			'product_name' => $new_prd_row['name'],
			'product_type' => $new_prd_row['type'],
			'plan_type_title' => getPlanName("",$planTypeId),
		);

		if(!empty($newPlanRes)){

			$new_ws_data = array(
				"customer_id" => $newPlanRes['customer_id'],
				"website_id" => $functionClass->get_website_id(),
				"product_id" => $new_plan_row['product_id'],
				"fee_applied_for_product" => $mainPrdId,
				"product_type" => $new_plan_row['product_type'],
				"plan_id" => $new_plan_row['plan_id'],
				"prd_plan_type_id" => $new_plan_row['prd_plan_type_id'],
				"product_code" => $new_plan_row['product_code'],
				"qty" => 1,
				"price" => $new_plan_row['price'],
				"member_price" => $new_plan_row['member_price'],
				"group_price" => $new_plan_row['group_price'],
				"contribution_type" => $new_plan_row['contribution_type'],
				"contribution_value" => $new_plan_row['contribution_value'],
				"next_purchase_date" => $newPlanRes['next_purchase_date'],
				"eligibility_date" => $newPlanRes['eligibility_date'],
				"start_coverage_period" => $newPlanRes['start_coverage_period'],
				"end_coverage_period" => $newPlanRes['end_coverage_period'],
				"last_order_id" => 0,
				"total_attempts" => 0,
				"next_attempt_at" => NULL,
				"renew_count" => $newPlanRes['renew_count'],
				"termination_date" => NULL,
				"term_date_set" => NULL,
				"status" => $newPlanRes['status'],
				"parent_ws_id" => ($isOldFee) ? $ws_row['id'] : '',
				"is_onetime" => 'N',
				"benefit_amount" => '0.00',
				"site_load" => $newPlanRes['site_load'],
				"payment_type" => $newPlanRes['payment_type'],
				"application_type" => $newPlanRes['application_type'],
				"active_date" => strtotime($newPlanRes['active_date'])>0?$newPlanRes['active_date']:$newPlanRes['created_at'],
				"policy_change_reason" => $action,
				"next_purchase_date_changed" => $newPlanRes['next_purchase_date_changed'],
				"manual_next_purchase_date" => $newPlanRes['manual_next_purchase_date'],
				"next_purchase_date_retain_rule" => $newPlanRes['next_purchase_date_retain_rule'],
				'annual_salary' => NULL,
				'monthly_benefit_percentage' => NULL,
				'last_purchase_date' => 'msqlfunc_NOW()',
				'purchase_date' => 'msqlfunc_NOW()',
				"updated_at" => "msqlfunc_NOW()",
				"created_at" => "msqlfunc_NOW()",
			);

			if (!empty($newPlanRes['issued_state'])) {
				$new_ws_data['issued_state'] = $newPlanRes['issued_state'];
			}
			if(strtotime($new_ws_data['next_purchase_date']) <= strtotime(date('Y-m-d'))) {
				$new_ws_data['next_purchase_date'] = date('Y-m-d',strtotime('+1 day'));
			}
			$new_ws_id = $pdo->insert("website_subscriptions", $new_ws_data);
			
			if($action == "policy_change") {
				$description = ($isOldFee) ? ("Policy changed from " . $old_plan_row['product_name'] . " to ." . $new_plan_row['product_name']) : ("Add New Policy " . $new_plan_row['product_name']);
			}elseif($action == "benefit_tier_change"){
				$description = ($isOldFee) ? ("Benefit Tier changed from " . $old_plan_row['plan_type_title'] . " to " . $new_plan_row['plan_type_title']) : ("Add New Policy " . $new_plan_row['product_name']);
			}

			$web_history_data = array(
				'customer_id' => $newPlanRes['customer_id'],
				'fee_applied_for_product' => $mainPrdId,
				'website_id' => $new_ws_id,
				'product_id' => $new_plan_row['product_id'],
				'plan_id' => $new_plan_row['plan_id'],
				'prd_plan_type_id' => $new_plan_row['prd_plan_type_id'],
				'order_id' => 0,
				'status' => 'Update',
				'message' => $description,
				'admin_id' => checkIsset($_SESSION['admin']['id']),
				'authorize_id' => '',
				'processed_at' => 'msqlfunc_NOW()',
				'created_at' => 'msqlfunc_NOW()',
			);
			$pdo->insert("website_subscriptions_history", $web_history_data);
		}
	}

	//Old Plan Update start

	$eligibility_date = date("Y-m-d", strtotime($change_date));

	if($isOldFee){
	
		$old_ce_process_status = "Pending";

		if(strtotime($ws_row['eligibility_date']) == strtotime($eligibility_date)) {
			$termination_date = $ws_row['eligibility_date'];
		} else{
			$termination_date = date('Y-m-d',strtotime('-1 day',strtotime($eligibility_date)));
		}
	
		$member_setting = $memberSetting->get_status_by_change_benefit_tier($ws_row['eligibility_date'],$change_date,$ws_row['status'],$termination_date);
	
		$is_proceed_imidiate = false;
		if ((strtotime($ws_row['eligibility_date']) == strtotime($change_date)) || strtotime($change_date) <= strtotime($today)) {
			$is_proceed_imidiate = true;
	
			$old_ws_status = $member_setting['old_policy_status'];
			$old_ce_process_status = "Active";
		}

		$old_ce_sql = "SELECT ce.*,w.customer_id 
		FROM customer_enrollment ce 
		JOIN website_subscriptions w on(w.id = ce.website_id) 
		WHERE ce.website_id=:website_id";
		$old_ce_res = $pdo->select($old_ce_sql, array(":website_id" => $ws_row['id']));

		foreach ($old_ce_res as $key => $old_ce_row) {

			if(!empty($adminLinkedFee)){
				$sub_products = $functionClass->get_sub_product($new_plan_row['product_id']);
				$new_ce_data = array(
					"company_id" => $old_ce_row['company_id'],
					'sub_product' => $sub_products,
					"sponsor_id" => $old_ce_row['sponsor_id'],
					"upline_sponsors" => $old_ce_row['upline_sponsors'],
					"level" => $old_ce_row['level'],
					"website_id" => $new_ws_id,
					"process_status" => $new_ce_process_status,
					"tier_change_date" => $eligibility_date,
					"has_old_coverage" => $old_ce_row['has_old_coverage'],
					"old_coverage_file" => $old_ce_row['old_coverage_file'],
					"parent_coverage_id" => $old_ce_row['id'],
				);
				$pdo->insert("customer_enrollment", $new_ce_data);
		    }

			//updating current plan to set term date etc
		
			$old_cd_data = array(
				'terminationDate' => $termination_date,
			);
			if(strtotime($termination_date) <= strtotime($today)) {
				$old_cd_data['status'] = $member_setting['dependent_status'];
			}
			$old_cd_where = array("clause" => "website_id=:id", "params" => array(":id" => $old_ce_row['website_id']));
			$pdo->update("customer_dependent", $old_cd_data, $old_cd_where);

			$old_ce_data = array(
				"process_status" => $old_ce_process_status,
				"new_plan_id" => checkIsset($new_plan_row['id']),
				"tier_change_date" => $eligibility_date,
			);
			$old_ce_where = array("clause" => "id=:id", "params" => array(":id" => $old_ce_row['id']));
			$pdo->update("customer_enrollment", $old_ce_data, $old_ce_where);
		}

		$termination_reason = $action;

		//updating current plan to set term date etc
		$old_ws_data = array(
			'termination_date' => $termination_date,
			'term_date_set' => date('Y-m-d'),
			'termination_reason' => $termination_reason,
			"policy_change_reason" => $action ,
			"updated_at" => "msqlfunc_NOW()",
		);
		if ($is_proceed_imidiate == true) {
			$old_ws_data['status'] = $old_ws_status;
		}
		$old_ws_where = array("clause" => "id=:id", "params" => array(":id" => $ws_row['id']));
		$pdo->update("website_subscriptions", $old_ws_data, $old_ws_where);

		$tmp_ws_data = array(
			'fee_applied_for_product' => $mainPrdId,
			"updated_at" => "msqlfunc_NOW()",
		);

		$tmp_ws_where = array("clause" => "fee_applied_for_product=:fee_applied_for_product AND customer_id=:customer_id", 
		"params" => array(":fee_applied_for_product" => $old_plan_row['product_id'],":customer_id" => $ws_row['customer_id']));
		$pdo->update("website_subscriptions", $tmp_ws_data, $tmp_ws_where);
	  // Old Plan Update End
	}else{
		if(!empty($adminLinkedFee)){
			$old_ce_sql = "SELECT ce.*,w.customer_id
			FROM customer_enrollment ce 
			JOIN website_subscriptions w on(w.id = ce.website_id) 
			WHERE ce.website_id=:website_id";
		    $old_ce_res = $pdo->selectOne($old_ce_sql, array(":website_id" => $newPlanWs));
			$sub_products = $functionClass->get_sub_product($new_plan_row['product_id']);
			$new_ce_data = array(
				"company_id" => $old_ce_res['company_id'],
				'sub_product' => $sub_products,
				"sponsor_id" => $old_ce_res['sponsor_id'],
				"upline_sponsors" => $old_ce_res['upline_sponsors'],
				"level" => $old_ce_res['level'],
				"website_id" => $new_ws_id,
				"process_status" => $new_ce_process_status,
				"tier_change_date" => $eligibility_date,
				"has_old_coverage" => NULL,
				"old_coverage_file" => NULL,
				"parent_coverage_id" => 0,
			);
			$pdo->insert("customer_enrollment", $new_ce_data);
		}
	}	
	return array(
		'feePrdId' => !empty($adminLinkedFee) ? $new_plan_row['product_id'] : "",
		'feePrdName' => !empty($adminLinkedFee) ? $fee_prd_name : "",
		'feePrdCode' => !empty($adminLinkedFee) ? $new_plan_row['product_code'] : "",
		'feeWebid' => !empty($adminLinkedFee) ? $new_ws_id : "",
		'fee_price' => !empty($adminLinkedFee) ?  $new_plan_row['price'] : 0,
		'feePlanId' => !empty($adminLinkedFee) ? $new_plan_row['plan_id'] : "",
		'amdinFeeRefAmt' => ($isOldFee) ? $ws_row['price'] : 0,
		'fee_applied_for_product' => $mainPrdId,
		'old_prd_id' => ($isOldFee) ? $ws_row['product_id'] : "",
	);
}

/**
 * Merge Common variable for bundle product and product
 */
function get_post_filtered_data(){
	global $_POST;
	$electedBundle = checkIsset($_POST['elected_bundle']);
	$fromStep = checkIsset($_POST['fromStep']);
	if(!empty($electedBundle) && $fromStep == 'bundleRecommandation'){

		$tmp_removed_product = checkIsset($_POST['removed_product'][$electedBundle],'arr');
		$tmp_bundle_product_matrix = checkIsset($_POST['bundle_product_matrix'][$electedBundle],'arr');
		$tmp_bundle_product_price = checkIsset($_POST['bundle_product_price'][$electedBundle],'arr');
		$tmp_bundle_display_product_price = checkIsset($_POST['bundle_display_product_price'][$electedBundle],'arr');
		$tmp_bundle_product_category = checkIsset($_POST['bundle_product_category'][$electedBundle],'arr');
		$tmp_bundle_product_benefit_tier = checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle],'arr');
		$tmp_bundle_product_benefit_tier = checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle],'arr');

		$_POST['removed_product'] = isset($_POST['removed_product']) ? array_replace_recursive($_POST['removed_product'],$tmp_removed_product) : $tmp_removed_product ;
		$_POST['product_matrix'] = isset($_POST['product_matrix']) ? array_replace_recursive($_POST['product_matrix'],$tmp_bundle_product_matrix) : $tmp_bundle_product_matrix ;
		$_POST['product_price'] = isset($_POST['product_price']) ? array_replace_recursive($_POST['product_price'],$tmp_bundle_product_price) : $tmp_bundle_product_price ;
		$_POST['display_product_price'] = isset($_POST['display_product_price']) ? array_replace_recursive($_POST['display_product_price'],$tmp_bundle_display_product_price) : $tmp_bundle_display_product_price ;
		$_POST['product_category'] = isset($_POST['product_category']) ? array_replace_recursive($_POST['product_category'],$tmp_bundle_product_category) : $tmp_bundle_product_category ;
		$_POST['product_benefit_tier'] = isset($_POST['product_benefit_tier']) ? array_replace_recursive($_POST['product_benefit_tier'],$tmp_bundle_product_benefit_tier) : $tmp_bundle_product_benefit_tier ;
		$_POST['product_plan'] = isset($_POST['product_plan']) ? array_replace_recursive($_POST['product_plan'],$tmp_bundle_product_benefit_tier) : $tmp_bundle_product_benefit_tier ;

		if(!empty($_POST['removed_product'])){
			$_POST['product_benefit_tier'] = array_diff_key($_POST['product_benefit_tier'],$_POST['removed_product']);
			$_POST['product_matrix'] = array_diff_key($_POST['product_matrix'],$_POST['removed_product']);
			$_POST['product_price'] = array_diff_key($_POST['product_price'],$_POST['removed_product']);
			$_POST['display_product_price'] = array_diff_key($_POST['display_product_price'],$_POST['removed_product']);
			$_POST['product_plan'] = array_diff_key($_POST['product_plan'],$_POST['removed_product']);
		}
	} else {
		$bundle_product_matrix = checkIsset($_POST['bundle_product_matrix'][$electedBundle],'arr');
		$bundle_product_price = checkIsset($_POST['bundle_product_price'][$electedBundle],'arr');
		$bundle_display_product_price = checkIsset($_POST['bundle_display_product_price'][$electedBundle],'arr');
		$bundle_product_category = checkIsset($_POST['bundle_product_category'][$electedBundle],'arr');
		$bundle_product_benefit_tier = checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle],'arr');
		$_POST['removed_product'] = checkIsset($_POST['removed_product'][$electedBundle],'arr');

		if(!empty($_POST['removed_product'])){
			$_POST['product_benefit_tier'] = isset($_POST['product_benefit_tier']) ? array_replace_recursive($_POST['product_benefit_tier'],array_diff_key($bundle_product_benefit_tier,$_POST['removed_product'])) : array_diff_key($bundle_product_benefit_tier,$_POST['removed_product']);
			$_POST['product_matrix'] = isset($_POST['product_matrix']) ? array_replace_recursive($_POST['product_matrix'],array_diff_key($bundle_product_matrix,$_POST['removed_product'])) : array_diff_key($bundle_product_matrix,$_POST['removed_product']);
			$_POST['product_price'] = isset($_POST['product_price']) ? array_replace_recursive($_POST['product_price'],array_diff_key($bundle_product_price,$_POST['removed_product'])) : array_diff_key($bundle_product_price,$_POST['removed_product']);
			$_POST['display_product_price'] = isset($_POST['display_product_price']) ? array_replace_recursive($_POST['display_product_price'],array_diff_key($bundle_display_product_price, $_POST['removed_product'])) : array_diff_key($bundle_display_product_price, $_POST['removed_product']);
			// $_POST['product_plan'] = array_replace_recursive($_POST['product_plan'],array_diff_key($_POST['product_plan'],$_POST['removed_product']));
		}else{
			$_POST['product_benefit_tier'] = isset($_POST['product_benefit_tier']) ? array_replace_recursive($_POST['product_benefit_tier'],$bundle_product_benefit_tier) : $bundle_product_benefit_tier;
			$_POST['product_matrix'] = isset($_POST['product_matrix']) ? array_replace_recursive($_POST['product_matrix'],$bundle_product_matrix) : $bundle_product_matrix;
			$_POST['product_price'] = isset($_POST['product_price']) ? array_replace_recursive($_POST['product_price'],$bundle_product_price) : $bundle_product_price;
			$_POST['display_product_price'] = isset($_POST['display_product_price']) ? array_replace_recursive($_POST['display_product_price'],$bundle_display_product_price) : $bundle_display_product_price;
		}

		$_POST['product_plan'] = $_POST['product_benefit_tier'];
	}
	return $_POST;
}

// To Remove Special Character in Searching
function cleanSearchKeyword($searchKeyword) {
	if ($searchKeyword != '') {	
		$searchKeyword = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $searchKeyword);  
	}
	return $searchKeyword;
}

/**
 * Used function for salary Encrypt and decrypt for Group Member/lead from Admin & Group Area
 * @param $user_type is admin/group
 * @param $entity_type is lead/customer
 */
function salaryEncryptDecrypt($user_type,$entity_type = 'lead'){
	global $pdo, $SALARY_PASSWORD, $ADMIN_HOST;
	$salaryRes = ['error' => ''];

	if(!empty($_POST['showing_pass']) && $_POST['showing_pass'] != $SALARY_PASSWORD){
        $salaryRes['error'] = 'Invalid password';
    }else if(!empty($_POST['change_salary']) && !empty($_POST['type']) && !empty($_POST['income'])){
        if($_POST['type'] == 'encrypt'){
            $salaryRes['enc_income'] = base64_encode($_POST['income']);
        }else {
            $salaryRes['dec_income'] = base64_decode($_POST['income']);
            $lead_id = $_POST['id'];
            $res_enity =$pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,lead_id as rep_id,income,customer_id from leads where md5(id)=:id",array(":id"=>$lead_id));
            if($res_enity['income'] != ''){
				$entity_action = ucfirst($user_type).' Read Lead Salary';
				$ac_red_2_href = $ADMIN_HOST.'/lead_details.php?id='.$lead_id;
				$ac_message_1 = ' Read Lead Salary for '.$res_enity['name'].'(';
				if($entity_type == 'customer'){
					$res_enityCustomer =$pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,rep_id from customer where id=:id",array(":id"=>$res_enity['customer_id']));
					$entity_action = 'Admin Read Member Salary';
					$ac_red_2_href = $ADMIN_HOST.'/members_details.php?id='.md5($res_enityCustomer['id']);
					$ac_message_1 = ' Read Member Salary for '.$res_enityCustomer['name'].'(';
					$res_enity['id'] = $res_enityCustomer['id'];
					$res_enity['rep_id'] = $res_enityCustomer['rep_id'];
				}
				if(strtolower($user_type) == 'admin'){
					$description['ac_message'] = array(
						'ac_red_1'=>array(
							'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
							'title'=>$_SESSION['admin']['display_id'],
						),
						'ac_message_1' =>$ac_message_1,
						'ac_red_2'=>array(
							'href'=> $ac_red_2_href,
							'title'=> $res_enity['rep_id'],
						),
						'ac_message_2'=>')',
					);
					$desc=json_encode($description);
					activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $res_enity['id'], $entity_type, $entity_action,$_SESSION['admin']['name'],"",$desc);
				}else if(strtolower($user_type) == 'group'){
					$description['ac_message'] = array(
						'ac_red_1'=>array(
							'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
							'title'=>$_SESSION['groups']['rep_id'],
						),
						'ac_message_1' =>$ac_message_1,
						'ac_red_2'=>array(
							'href'=> $ac_red_2_href,
							'title'=> $res_enity['rep_id'],
						),
						'ac_message_2'=>')',
					);
					$desc=json_encode($description);
					activity_feed(3,$_SESSION['groups']['id'], 'Group' , $res_enity['id'], $entity_type, $entity_action,($_SESSION['groups']['fname'].$_SESSION['groups']['lname']),"",$desc);
				}
            }
        }
    }
	return $salaryRes;
}
function addDashtoBlankField($fieldValue){
	return !empty($fieldValue) ? $fieldValue : '-';
}
?>
