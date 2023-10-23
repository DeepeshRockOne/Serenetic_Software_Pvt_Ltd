<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
has_access(6);

$id = $_GET['id']; 
$select = "SELECT business_name,display_id,sponsor_id,cell_phone,rep_id,fname,lname,email,type,status,HOUR(TIMEDIFF(NOW(), invite_at)) as invite_time_diff FROM customer WHERE md5(id)=:id AND type='Group'";
$param = array(":id" => $id);
$data = $pdo->selectOne($select, $param);

if ($data) {
	if (!in_array($data['status'], array('Active', 'Contracted'))) {
		$fname = $data['fname'];
		$lname = $data['lname'];
		$access_level = $data['type'];
		$email = trim($data['email']);
		$display_id = $data['display_id'];
		$rep_id = $data['rep_id'];
		$groupName = $data['business_name'];
		 
		if($data['invite_time_diff'] > 168){
			$link = 'Application link has expried';
		}else{
			$link = $HOST . '/group_contract/' . md5($rep_id);
		}
	} else {
		setNotifySuccess('Registration process for this user has been completed.');
		echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
		exit;
	}
} else {
	setNotifySuccess('User not found.');
	echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
	exit;
}

if (isset($_POST['save'])) {
	$email = checkIsset($_POST['email']);
	$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Invalid Email'));

	if (!$validate->getError('email')) {
		$sql = "SELECT id,email FROM customer WHERE email = :email AND md5(id)!=:id AND type='Group'";
		$whr = array(
			':email' => makeSafe($email), ':id' => $_GET['id']);
		$row = $pdo->selectOne($sql, $whr);
		if (count($row) > 0) {
			if ($row['id'] > 0) {
				$validate->setError('email', 'This email is already associated with another Group account');
			}
		}
	}

	if ($validate->isValid()) {
		$admin_type = strtolower(str_replace(" ", "", $access_level));
		// Group - Welcome Invite
		$trigger_id = 61;
		if (($data["status"] == "Pending Account Set Up") || ($data["status"] == "Invited")) {
			$link = $HOST . '/group_contract/' . md5($rep_id);
		} else if ($data["status"] == "Group Abandon") {
			$link = $HOST . '/group_contract/' . $rep_id;
		}

		// trigger SmartTag replace
		$resEnrollAgent = array();
		if(!empty($data['sponsor_id'])){
			$selEnrollAgent = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name,email,cell_phone FROM customer WHERE id=:id AND type='Agent'";
			$paramsEnrollAgent = array(":id" => $data['sponsor_id']);
			$resEnrollAgent = $pdo->selectOne($selEnrollAgent,$paramsEnrollAgent);
		}

		$link = "<a href='" . $link . "' target='_blank'>" . $link . "</a>";
		$params = array();
		$params['fname'] = $fname;
		$params['lname'] = $lname;
		$params['link'] = $link;
		$params['invite_link'] = $link;
		$params['link'] = $link;
		$params['Email'] = $email;
		$params['Phone'] = $data['cell_phone'];
		$params['Group'] = $fname.' '.$lname;
		$params['ParentAgent'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';

		$params['GroupName'] = $groupName;
		$params['EnrollingAgentDisplayName'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';
		$params['EnrollingAgentDisplayEmail'] = !empty($resEnrollAgent['email']) ? $resEnrollAgent['email'] : '';
		$params['EnrollingAgentDisplayPhone'] = !empty($resEnrollAgent['cell_phone']) ? $resEnrollAgent['cell_phone'] : '';

		$params['USER_IDENTITY'] = array('rep_id' => $rep_id, 'cust_type' => 'Group', 'location' => ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

		$smart_tags = get_user_smart_tags($id,'group');
                
	    if($smart_tags){
	        $params = array_merge($params,$smart_tags);
	    }

		$update_params = array(
			'email' => makeSafe($email),
			'invite_at' => 'msqlfunc_NOW()',
			'updated_at' => 'msqlfunc_NOW()',
		);
		$update_where = array(
			'clause' => 'md5(id) = :id',
			'params' => array(
				':id' => makeSafe($id),
			),
		);
		$update_status = $pdo->update('customer', $update_params, $update_where);
		if ($SITE_ENV=='Local') {
			$email = "karan@cyberxllc.com";
		}
		try {
			trigger_mail($trigger_id, $params, $email);
		} catch (Exception $e) {
			echo $e;
			exit;
		}
		setNotifySuccess('You have successfully resent invitation to ' . $fname . ' ' . $lname . ' at ' . $email);
		redirect('groups_listing.php', true);
	}
}

$exJs = array('thirdparty/clipboard/clipboard.min.js');

$errors = $validate->getErrors();
$layout = "iframe.layout.php";
$template = "reinvite_group.inc.php";
include_once 'layout/end.inc.php';
?>
