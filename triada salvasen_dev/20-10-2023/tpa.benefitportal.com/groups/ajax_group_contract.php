<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$validate = new Validation();


$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : 0;

$group_name = !empty($_POST['group_name']) ? $_POST['group_name'] : '';
$fname = !empty($_POST['fname']) ? $_POST['fname'] : '';
$lname = !empty($_POST['lname']) ? $_POST['lname'] : '';
$email = checkIsset($_POST['email']);
$cell_phone = !empty($_POST['cell_phone']) ? phoneReplaceMain($_POST['cell_phone']) : '';
$password = !empty($_POST['password']) ? $_POST['password'] : '';
$c_password = !empty($_POST['c_password']) ? $_POST['c_password'] : '';


$validate->string(array('required' => true, 'field' => 'group_name', 'value' => $group_name), array('required' => 'Group name is required'));
$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First name is required'));
$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last name is required'));

$validate->digit(array('required' => true, 'field' => 'phone', 'value' => $cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));


$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

if ($email != "") {
	$selectEmail = "SELECT email FROM customer WHERE type='Group' AND email = :email AND id!=:id AND is_deleted='N'";
	$where_select_email = array(':email' => $email, ":id" => $group_id);
	$resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
	if ($resultEmail) {
		$validate->setError("email", "This email is already associated with another group account. <a href='".$GROUP_HOST."'>Click Here</a> to login");
	}
}

$validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
$validate->string(array('required' => true, 'field' => 'c_password', 'value' => $c_password), array('required' => 'Confirm Password is required'));
//for strong password
if (!$validate->getError('password')) {
	if (strlen($password) < 8 || strlen($password) > 20) {
		$validate->setError('password', 'Password must be 8-20 characters');
	} else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
		 || !preg_match('`[0-9]`', $password)) {
		// at least one digit
		$validate->setError('password', 'Valid Password is required');
	} else if (!ctype_alnum($password)) {
		$validate->setError('password', 'Special character not allowed');
	} else if (preg_match('`[?/$\*+]`', $password)) {
		$validate->setError('password', 'Password not valid');
	} else if (preg_match('`[,"]`', $password)) {
		$validate->setError('password', 'Password not valid');
	} else if (preg_match("[']", $password)) {
		$validate->setError('password', 'Password not valid');
	}
}
if (!$validate->getError('c_password') && !$validate->getError('password')) {
	if ($password != $c_password) {
		$validate->setError('c_password', 'Both Password must be same');
	}
}


if ($validate->isValid()) {
	
	$custSql = "SELECT c.rep_id,s.email as sponsor_email,s.fname as sponsor_fname,c.sponsor_id 
		FROM customer c
		JOIN customer_settings cs ON (c.id=cs.customer_id)
		JOIN customer s ON (s.id=c.sponsor_id)
		WHERE (c.status in('Group Abandon','Invited') OR cs.recontract_status='Pending') AND c.id=:id";
	$custWhr = array(':id' => $group_id);
	$custRes = $pdo->selectOne($custSql, $custWhr);

	if (!empty($custRes)) {
		$sponsor_id = $custRes['sponsor_id'];
		$sponsor_email = $custRes['sponsor_email'];
		$sponsor_name = $custRes['sponsor_fname'];
		$groupRepID = $custRes["rep_id"];


		//************* Customer Update Code Start *************
			$params = array(
				'fname' => makesafe($fname),
				'lname' => makesafe($lname),
				'email' => $email,
				'cell_phone' => makesafe($cell_phone),
				'joined_date' => 'msqlfunc_NOW()',
				'password' => "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')",
				'status' => 'Pending Documentation',
			);
		
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $group_id,
				),
			);
			$pdo->update('customer', $params, $upd_where);
		//************* Customer Update Code End   *************

		//************* Lead Update Code Start *************
			$leads_update_params = array(
				'fname'=>$fname,
				'lname'=>$lname,
				'email'=>$email,
				'cell_phone' => makeSafe($cell_phone),
				'status' => "Converted",
			);
			$leads_update_where = array(
				'clause' => 'customer_id = :customer_id',
				'params' => array(
					':customer_id' => $group_id,
				),
			);
			$pdo->update("leads", $leads_update_params, $leads_update_where);
		//************* Lead Update Code End   *************

		//************* Invite Group Send Mail Code Start *************
			$rep_params = array();
			$rep_params['fname'] = $fname;
			$rep_params['lname'] = $lname;
			$rep_params['name'] = $fname . ' ' . $lname;
			$rep_params['email'] = $email;
			$rep_params['username'] = $fname . ' ' . $lname;
			$rep_params['groupName'] = $group_name;

			$rep_params['link'] = $HOST;
			$rep_params['invite_link'] = $HOST;
			$rep_params['userid'] = $groupRepID;
			$rep_params['phone'] = $cell_phone;
			$rep_params['login'] = $email;

			$rep_params['USER_IDENTITY'] = array('rep_id' => $groupRepID, 'cust_type' => 'Group', 'location' => $REQ_URL);

			$smart_tags = get_user_smart_tags($group_id,'group');
                
            if($smart_tags){
                $rep_params = array_merge($rep_params,$smart_tags);
            }
			// Group - Welcome Email
			$trigger_id = 62;
			trigger_mail($trigger_id, $rep_params, $email);
		//************* Invite Group Send Mail Code End   *************

		//************* Group Sponsor Send Mail Code Start *************
			$rep_params['fname'] = $sponsor_name;
			$rep_params['GroupName'] = $group_name;
			// Group - Onboarding Complete
			/*$trigger_id2 = 98;
			if (!empty($sponsor_email)) {
				trigger_mail($trigger_id2, $rep_params, $sponsor_email);
			}*/
		//************* Group Sponsor Send Mail Code End   *************

		//************* Audit Log Code Start *************
			$user_data = array();
			$user_data['id'] = $group_id;
			$user_data['user_id'] = $group_id;
			$user_data['display_id'] = $groupRepID;
			$user_data['group_ID'] = $groupRepID;
			$user_data['fname'] = makesafe($fname);
			$user_data['lname'] = makesafe($lname);
			$user_data['full_name'] = $fname.' '.$lname;
			$user_data['user_type'] = 'Group';
			$user_data['type'] = 'Group';
			$audit_log_id = audit_log($user_data, $group_id, "Group", "Group Account Created", '', '', 'Group account created');
		//************* Audit Log Code End   *************

		//************* Parent Agent Notification Code Start *************
			addGroupNotification($sponsor_id, 18, "{GROUP}/groups_listing.php?1=1", 0, 'N', $group_id,$fname.' '.$lname);
		//************* Parent Agent Notification Code End   *************

		//************* Notification Email To Chris Code Start *************
			$trigger_id = 97;
			if($SITE_ENV == 'Live'){
				//Group - Invite Accepted
				$NOTIFICATION_EMAIL = array("cpearson@cyberxllc.com");
				$rep_params = array();
				$rep_params["fname"] = $fname;
				$rep_params["lname"] = $lname;
				$rep_params["group_ID"] = $groupRepID;
				$rep_params["GroupName"] = $group_name;
				$rep_params['link'] = $ADMIN_HOST . "/groups_details.php?id=" .md5($group_id);

				$smart_tags = get_user_smart_tags($group_id,'group');
                
                if($smart_tags){
                    $rep_params = array_merge($rep_params,$smart_tags);
                }

				trigger_mail($trigger_id, $rep_params, $NOTIFICATION_EMAIL);
			}
		//************* Notification Email To Chris Code End   *************

		//************* Activity Feed Generate Code Start *************
			$email_link_agent = "trigger_detail_popup.php?trigger_id=".$trigger_id."&type=email";
			$email_link_admin = "trigger_detail_popup.php?trigger_id=".$trigger_id."&type=email";
			
			/*$description['ac_link'] = array(
				'email_agent'=>['text'=>'Email sent to parent agent: ','label'=>$trigger_id2,'href'=>$email_link_agent,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
				'email_admin'=>['text'=>'Email sent to admin: ','label'=>$trigger_id,'href'=>$email_link_admin,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>'']
			);*/
			$description['ac_link'] = array(
				'email_admin'=>['text'=>'Email sent to admin: ','label'=>$trigger_id,'href'=>$email_link_admin,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>'']
			);
			activity_feed(3, $sponsor_id, 'Agent', $group_id, 'customer', 'Group Invite Accepted', $fname, $lname,json_encode($description));
			activity_feed(3, $group_id, 'Group', $group_id, 'customer', 'Group Invite Accepted', $fname, $lname,json_encode($description));
		//************* Activity Feed Generate Code End   *************

		$response['status'] = 'account_approved';
		$response['group_id'] = $group_id;
	} else {
		$response['status'] = 'no_group_found';
	}
	
}else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}


header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>