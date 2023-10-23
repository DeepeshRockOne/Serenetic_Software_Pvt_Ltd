<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$REAL_IP_ADDRESS = get_real_ipaddress();
if (isset($_SESSION['admin']['id']) && !empty($_REQUEST['id'])) {
	$sql = "SELECT c.id,cs.agent_coded_level,company_id,business_name,fname,lname,access_type,feature_access,user_name,email,type,cell_phone,rep_id,sponsor_id,upline_sponsors,status, created_at,is_password_set,cs.displayDirectEnroll,cs.additionalAccess FROM customer c LEFT JOIN customer_settings cs ON (cs.customer_id=c.id) WHERE md5(c.id) = '" . $_REQUEST['id'] . "'";
	$sub_row = $pdo->selectOne($sql);
	if (!empty($sub_row)) {

		// if ($sub_row['type'] == "Affiliates") {
		// 	$_SESSION['member'] = null;
		// 	unset($_SESSION['member']);
		// 	$_SESSION['member'] = $sub_row;
		// 	$_SESSION['member']['admin_switch'] = 'yes';

		// 	setcookie('WebIMUser', $sub_row['fname'] . " " . $sub_row['lname'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
		// 	setcookie('WebIMCustId', $sub_row['id'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
		// 	setcookie('WebIMCustType', $sub_row['type'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
		// 	// redirect($HOST . "/affiliates/dashboard.php");
		// 	redirect($HOST . "/acenter/dashboard.php");
		// 	//redirect("https://myhealthpass.com/affiliates/dashboard.php");
		// } else if ($sub_row['type'] == "Ambassadors") {
		// 	$_SESSION['member'] = null;
		// 	unset($_SESSION['member']);
		// 	$_SESSION['member'] = $sub_row;
		// 	$_SESSION['member']['admin_switch'] = 'yes';

		// 	setcookie('WebIMUser', $sub_row['fname'] . " " . $sub_row['lname'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
		// 	setcookie('WebIMCustId', $sub_row['id'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
		// 	setcookie('WebIMCustType', $sub_row['type'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
		// 	// redirect($HOST . "/affiliates/dashboard.php");
		// 	redirect($HOST . "/acenter/dashboard.php");
		// 	//redirect("https://myhealthpass.com/affiliates/dashboard.php");
		// } else if ($sub_row['type'] == "Customer") {
		// 	redirect($CUSTOMER_HOST . '/index.php?switch=Y&id=' . md5($sub_row['id']));
		// 	if (!empty($_REQUEST['header'])) {
		// 		$_SESSION['customer'] = null;
		// 		unset($_SESSION['customer']);
		// 		$_SESSION['customer'] = $sub_row;
		// 		redirect($HOST . '/member/dashboard.php');
		// 	} else {
		// 		if ($sub_row['company_id'] == '2' || $sub_row['company_id'] == '1' || $sub_row['company_id'] == '4') {
		// 			$_SESSION['customer'] = null;
		// 			unset($_SESSION['customer']);
		// 			//$_SESSION['customer']['admin_switch'] = 'yes';
		// 			$companySql = "SELECT * FROM company WHERE id=:id";
		// 			$whr = array(":id" => $sub_row['company_id']);
		// 			$companyRows = $pdo->selectOne($companySql, $whr);
		// 			redirect($companyRows['site_url'] . '/customer/index.php?switch=Y&id=' . md5($sub_row['id']));
		// 		} else {
		// 			setNotifyError("Customer Not Found!");
		// 			redirect("customer_listing.php");
		// 		}
		// 	}
		// 	//redirect($HOST . "/affiliates/dashboard.php");
		// } else 
		if ($sub_row['type'] == "Agent") {
				$_SESSION['agents'] = null;
				unset($_SESSION['agent']);
				$_SESSION['agents'] = $sub_row;
				$_SESSION['agents']['is_sub_agent'] = 'N';
				$_SESSION['agents']['access'] = $sub_row['feature_access'] != "" ? explode(",",$sub_row['feature_access']) : array();
				$_SESSION['agents']['additionalAccess'] = $sub_row['additionalAccess'];
				$_SESSION['agents']['access_type'] = $sub_row['access_type'];
				$_SESSION['agents']['admin_switch'] = 'yes';
				$_SESSION['agents']['admin_switch'] = $sub_row['agent_coded_level'];

				/* No need for this as per current management
				if ($_SESSION['agents']['access_type'] == "" || $_SESSION['agents']['access_type'] == 'full_access') {
					$access_type_and_access = get_agent_access_type_and_access($sub_row['sponsor_id']);
					$_SESSION['agents']['access_type'] = $access_type_and_access['access_type'];
					$_SESSION['agents']['access'] = $access_type_and_access['access'];
				}*/
				
				$_SESSION['agents']['timezone'] = $_SESSION['admin']['timezone'];

				$cs_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon,cs.not_show_license_expired,cs.not_show_license_expiring,cs.not_show_eo_expired,cs.not_show_eo_expiring
		                        FROM customer c 
		                        JOIN customer_settings cs ON(cs.customer_id = c.id)
		                        WHERE c.id=:id";
				$cs_where = array(":id" => $_SESSION['agents']['id']);
				$cs_row = $pdo->selectOne($cs_sql, $cs_where);
				if(!empty($cs_row)) {
					$_SESSION['agents']['public_name'] = $cs_row['public_name'];
					$_SESSION['agents']['public_phone'] = $cs_row['public_phone'];
					$_SESSION['agents']['public_email'] = $cs_row['public_email'];
					$_SESSION['agents']['display_in_member'] = $cs_row['display_in_member'];
					$_SESSION['agents']['is_branding'] = $cs_row['is_branding'];
					$_SESSION['agents']['brand_icon'] = $cs_row['brand_icon'];
					$_SESSION['agents']['not_show_license_expired'] = $cs_row['not_show_license_expired'];
					$_SESSION['agents']['not_show_license_expiring'] = $cs_row['not_show_license_expiring'];
					$_SESSION['agents']['not_show_eo_expired'] = $cs_row['not_show_eo_expired'];
					$_SESSION['agents']['not_show_eo_expiring'] = $cs_row['not_show_eo_expiring'];
				}

				/*--- parent agent public data ---*/
				$spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
				        FROM customer c 
				        JOIN customer_settings cs ON(cs.customer_id = c.id)
				        WHERE c.id=:id";
				$spon_where = array(":id" => $_SESSION['agents']['sponsor_id']);
				$spon_row = $pdo->selectOne($spon_sql, $spon_where);
				if(!empty($spon_row)) {
					$_SESSION['agents']['sponsor_name'] = $spon_row['name'];
					$_SESSION['agents']['sponsor_rep_id'] = $spon_row['rep_id'];
					$_SESSION['agents']['sponsor_email'] = $spon_row['email'];
					$_SESSION['agents']['sponsor_cell_phone'] = $spon_row['cell_phone'];
					$_SESSION['agents']['sponsor_public_name'] = $spon_row['public_name'];
					$_SESSION['agents']['sponsor_public_phone'] = $spon_row['public_phone'];
					$_SESSION['agents']['sponsor_public_email'] = $spon_row['public_email'];
					$_SESSION['agents']['sponsor_display_in_member'] = $spon_row['display_in_member'];
					$_SESSION['agents']['sponsor_is_branding'] = $spon_row['is_branding'];
					$_SESSION['agents']['sponsor_brand_icon'] = $spon_row['brand_icon'];
					$_SESSION['agents']['agent_services_cell_phone'] = get_app_settings('agent_services_cell_phone');
					$_SESSION['agents']['agent_services_email'] = get_app_settings('agent_services_email');
				} else {
					$_SESSION['agents']['sponsor_display_in_member'] = 'Y';
					$_SESSION['agents']['agent_services_cell_phone'] = get_app_settings('agent_services_cell_phone');
					$_SESSION['agents']['agent_services_email'] = get_app_settings('agent_services_email');
				}
				/*---/parent agent public data ---*/

				$real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
				$updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address ,'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
				$where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
				$pdo->update("customer_settings", $updateStr, $where);

				$description = array();
				$description['ac_message'] =array(
					'ac_red_1'=>array(
						'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
						'title'=>$_SESSION['admin']['display_id'],
					),
					
					'ac_message_1' =>' Switch Login On Agent '.$sub_row['fname'].' '.$sub_row['lname'],
					'ac_red_2'=>array(
						'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($sub_row['id']),
						'title'=>"(".$sub_row['rep_id'].")",
					),
				);

				activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $sub_row['id'], 'Agent','Admin Switch Login', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

				redirect($AGENT_HOST . "/dashboard.php");

		} elseif ($sub_row['type'] == "Customer") {
				$_SESSION['customer'] = null;
				unset($_SESSION['customer']);
				// $_SESSION['customer'] = $sub_row;
				// $_SESSION['customer']['admin_switch'] = 'yes';
				// $_SESSION['customer']['timezone'] = $_SESSION['admin']['timezone'];
				
				$real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
				$updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address ,'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
				$where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
				$pdo->update("customer_settings", $updateStr, $where);

				/*-- Set Sponsor Data To Session --*/
				// $spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
				//                 FROM customer c 
				//                 JOIN customer_settings cs ON(cs.customer_id = c.id)
				//                 WHERE c.id=:id";
				// $spon_where = array(":id" => $_SESSION['customer']['sponsor_id']);
				// $spon_row = $pdo->selectOne($spon_sql, $spon_where);
				// if(!empty($spon_row)) {
				// 	$_SESSION['customer']['sponsor_name'] = $spon_row['name'];
				// 	$_SESSION['customer']['sponsor_rep_id'] = $spon_row['rep_id'];
				// 	$_SESSION['customer']['sponsor_email'] = $spon_row['email'];
				// 	$_SESSION['customer']['sponsor_cell_phone'] = $spon_row['cell_phone'];
				// 	$_SESSION['customer']['sponsor_public_name'] = $spon_row['public_name'];
				// 	$_SESSION['customer']['sponsor_public_phone'] = $spon_row['public_phone'];
				// 	$_SESSION['customer']['sponsor_public_email'] = $spon_row['public_email'];
				// 	$_SESSION['customer']['sponsor_display_in_member'] = $spon_row['display_in_member'];
				// 	$_SESSION['customer']['sponsor_is_branding'] = $spon_row['is_branding'];
    //       			$_SESSION['customer']['sponsor_brand_icon'] = $spon_row['brand_icon'];
				// 	$_SESSION['customer']['member_services_cell_phone'] = get_app_settings('member_services_cell_phone');
				// 	$_SESSION['customer']['member_services_email'] = get_app_settings('member_services_email');
				// }
				/*--/Set Sponsor Data To Session --*/

				// $description = array();
				// $description['ac_message'] =array(
				// 	'ac_red_1'=>array(
				// 		'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				// 		'title'=>$_SESSION['admin']['display_id'],
				// 	),
					
				// 	'ac_message_1' =>' Switch Login On Member '.$sub_row['fname'].' '.$sub_row['lname'],
				// 	'ac_red_2'=>array(
				// 		'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($sub_row['id']),
				// 		'title'=>"(".$sub_row['rep_id'].")",
				// 	),
				// );
				
				// activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $sub_row['id'], 'Customer','Admin Switch Login', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
				redirect($CUSTOMER_HOST . "/index.php?switch=Y&site=salvasen&id=" . md5($sub_row['id'])."&timezone=".$_SESSION['admin']['timezone']);

		} else if ($sub_row['type'] == "Group") {
			$_SESSION['groups'] = null;
			unset($_SESSION['groups']);



			$sql = "SELECT c.id,c.fname,c.lname,c.user_name,c.email,c.type,c.cell_phone,c.rep_id,c.sponsor_id,c.status, c.created_at,c.company_id,c.business_name, AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,c.access_type,c.feature_access,cs.tpa_for_billing
                FROM customer c
                JOIN customer_settings cs ON(cs.customer_id = c.id)
                WHERE md5(c.id) = '" . $_REQUEST['id'] . "'";
			$sub_row = $pdo->selectOne($sql);

			$real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
			$updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address ,'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
			$where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
			$pdo->update("customer_settings", $updateStr, $where);



			$_SESSION['groups'] = $sub_row;

			$_SESSION['groups']['timezone'] = $_SESSION['admin']['timezone'];
			$_SESSION['groups']['access'] = $sub_row['feature_access'] != "" ? explode(",",$sub_row['feature_access']) : array();
			$_SESSION['groups']['access_type'] = $sub_row['access_type'];
			$_SESSION['groups']['admin_switch'] = 'yes';

			$_SESSION['groups']['admin_switch'] = 'yes';
			$_SESSION['groups']['timezone'] = $_SESSION['admin']['timezone'];

			$cs_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
                        FROM customer c 
                        JOIN customer_settings cs ON(cs.customer_id = c.id)
                        WHERE c.id=:id";
     	 	$cs_where = array(":id" => $_SESSION['groups']['id']);
          	$cs_row = $pdo->selectOne($cs_sql, $cs_where);
          	if(!empty($cs_row)) {
	              $_SESSION['groups']['public_name'] = $cs_row['public_name'];
	              $_SESSION['groups']['public_phone'] = $cs_row['public_phone'];
	              $_SESSION['groups']['public_email'] = $cs_row['public_email'];
	              $_SESSION['groups']['display_in_member'] = $cs_row['display_in_member'];
	              $_SESSION['groups']['is_branding'] = $cs_row['is_branding'];
	              $_SESSION['groups']['brand_icon'] = $cs_row['brand_icon'];
          	}

			/*--- parent agent public data ---*/
				$spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
				        FROM customer c 
				        JOIN customer_settings cs ON(cs.customer_id = c.id)
				        WHERE c.id=:id";
				$spon_where = array(":id" => $_SESSION['groups']['sponsor_id']);
				$spon_row = $pdo->selectOne($spon_sql, $spon_where);
				if(!empty($spon_row)) {
					$_SESSION['groups']['sponsor_name'] = $spon_row['name'];
					$_SESSION['groups']['sponsor_rep_id'] = $spon_row['rep_id'];
					$_SESSION['groups']['sponsor_email'] = $spon_row['email'];
					$_SESSION['groups']['sponsor_cell_phone'] = $spon_row['cell_phone'];
					$_SESSION['groups']['sponsor_public_name'] = $spon_row['public_name'];
					$_SESSION['groups']['sponsor_public_phone'] = $spon_row['public_phone'];
					$_SESSION['groups']['sponsor_public_email'] = $spon_row['public_email'];
					$_SESSION['groups']['sponsor_display_in_member'] = $spon_row['display_in_member'];
					$_SESSION['groups']['sponsor_is_branding'] = $spon_row['is_branding'];
					$_SESSION['groups']['sponsor_brand_icon'] = $spon_row['brand_icon'];
					$_SESSION['groups']['group_services_cell_phone'] = get_app_settings('group_services_cell_phone');
					$_SESSION['groups']['group_services_email'] = get_app_settings('group_services_email');
					$_SESSION['groups']['enrollment_display_name'] = get_app_settings('enrollment_display_name');
				} else {
					$_SESSION['groups']['sponsor_display_in_member'] = 'Y';
					$_SESSION['groups']['group_services_cell_phone'] = get_app_settings('group_services_cell_phone');
					$_SESSION['groups']['group_services_email'] = get_app_settings('group_services_email');
					$_SESSION['groups']['enrollment_display_name'] = get_app_settings('enrollment_display_name');
				}
				/*---/parent agent public data ---*/

				$real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
				$updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address ,'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
				$where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
				$pdo->update("customer_settings", $updateStr, $where);

				$description = array();
				$description['ac_message'] =array(
					'ac_red_1'=>array(
						'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
						'title'=>$_SESSION['admin']['display_id'],
					),
					
					'ac_message_1' =>' Switch Login On Group '.$sub_row['business_name'],
					'ac_red_2'=>array(
						'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($sub_row['id']),
						'title'=>"(".$sub_row['rep_id'].")",
					),
				);

				activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $sub_row['id'], 'Group','Admin Switch Login', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
				redirect($HOST . "/groups/dashboard.php");
		} /*else if ($sub_row['type'] == "Organization") {
			$_SESSION['organization'] = null;
			unset($_SESSION['organization']);
			$_SESSION['organization'] = $sub_row;
			$_SESSION['organization']['admin_switch'] = 'yes';
			redirect($MYHEALTH_HOST . "/fundraiser");
		} else if ($sub_row['type'] == "Supporter") {
			$_SESSION['supporter'] = null;
			unset($_SESSION['supporter']);
			$_SESSION['supporter'] = $sub_row;
			$_SESSION['organization']['admin_switch'] = 'yes';
			redirect($MYHEALTH_HOST . "/teammember/dashboard.php");
		} else if ($sub_row['type'] == "Call Center" || $sub_row['type'] == "Fronter" || $sub_row['type'] == "Call Center Manager" || $sub_row['type'] == 'Representative') {
			// echo "string"; exit;
			$sql = "SELECT id,fname,lname,user_name,email,type,agent_coded_level,cell_phone,rep_id,call_center_access,sponsor_id,status, created_at,company_id,twilio_account_id, AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,company_id,access_type,feature_access,business_name
				FROM customer WHERE md5(id) = '" . $_REQUEST['id'] . "'";
			$sub_row = $pdo->selectOne($sql);

			$_SESSION['call_center'] = null;
			unset($_SESSION['call_center']);
			$_SESSION['call_center'] = $sub_row;
			$_SESSION['call_center']['access'] = (array) json_decode($sub_row['feature_access']);
			$_SESSION['call_center']['access_type'] = $sub_row['access_type'];
			$_SESSION['call_center']['admin_switch'] = 'yes';

			$getCustomerRes=$pdo->selectOne("SELECT * FROM customer_settings WHERE customer_id=:customer_id",array(":customer_id"=>$custRow['id']));
            if($getCustomerRes){
                 $_SESSION['call_center']['send_only_quick_text'] = $getCustomerRes['send_only_quick_text'];
            }
            //get call center primary function code start
                $getCallCenterRes=$pdo->selectOne("SELECT * FROM customer_settings WHERE customer_id=:customer_id",array(":customer_id"=>($_SESSION['call_center']['type']=="Call Center" ? $_SESSION['call_center']['id'] : $_SESSION['call_center']['sponsor_id'])));
                if($getCallCenterRes){
                 $_SESSION['call_center']['call_center_primary_function'] = $getCallCenterRes['call_center_primary_function'];
                }
            //get call center primary function code end
			if ($_SESSION['call_center']['access_type'] == "" || $_SESSION['call_center']['access_type'] == 'full_access') {
				$access_type_and_access = get_call_center_access_type_and_access($sub_row['sponsor_id']);
				$_SESSION['call_center']['access_type'] = $access_type_and_access['access_type'];
				$_SESSION['call_center']['access'] = $access_type_and_access['access'];
			}

			$updateStr = array("last_login" => 'msqlfunc_NOW()', 'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
			$where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
			$pdo->update("customer", $updateStr, $where);

			setcookie('WebIMUser', $sub_row['fname'] . " " . $sub_row['lname'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
			setcookie('WebIMCustId', $sub_row['id'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
			setcookie('WebIMCustType', $sub_row['type'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
			// echo $CALL_CENTER_HOST; exit;
			redirect($CALL_CENTER_HOST . "/dashboard.php");
		}*/
		 else {
			$_SESSION['customer'] = null;
			unset($_SESSION['customer']);
			redirect($CUSTOMER_HOST . "/index.php?switch=Y&site=salvasen&id=" . md5($sub_row['id'])."&timezone=".$_SESSION['admin']['timezone']);
		}
	}else{
		setNotifyError("No Record found!");
		redirect($ADMIN_HOST . "/agent_listing.php");
	}
}
?>
