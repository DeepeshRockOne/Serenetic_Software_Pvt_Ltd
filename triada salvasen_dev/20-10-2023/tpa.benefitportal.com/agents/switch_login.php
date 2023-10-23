<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$REAL_IP_ADDRESS = get_real_ipaddress();
if (isset($_SESSION['agents']['id']) && !empty($_REQUEST['id'])) {
	$sql = "SELECT c.id,cs.agent_coded_level,company_id,business_name,fname,lname,access_type,feature_access,user_name,email,type,cell_phone,rep_id,sponsor_id,upline_sponsors,status, created_at FROM customer c LEFT JOIN customer_settings cs ON (cs.customer_id=c.id) WHERE md5(c.id) = '" . $_REQUEST['id'] . "'";
	$sub_row = $pdo->selectOne($sql);
	if (!empty($sub_row)) {
		if ($sub_row['type'] == "Customer") {
				$_SESSION['customer'] = null;
				unset($_SESSION['customer']);
				$_SESSION['customer'] = $sub_row;
				$_SESSION['customer']['admin_switch'] = 'yes';
				$_SESSION['customer']['timezone'] = $_SESSION['agents']['timezone'];
				
				$real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
				$updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address ,'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
				$where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
				$pdo->update("customer_settings", $updateStr, $where);

				/*-- Set Sponsor Data To Session --*/
				$spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
				                FROM customer c 
				                JOIN customer_settings cs ON(cs.customer_id = c.id)
				                WHERE c.id=:id";
				$spon_where = array(":id" => $_SESSION['customer']['sponsor_id']);
				$spon_row = $pdo->selectOne($spon_sql, $spon_where);
				if(!empty($spon_row)) {
					$_SESSION['customer']['sponsor_name'] = $spon_row['name'];
					$_SESSION['customer']['sponsor_rep_id'] = $spon_row['rep_id'];
					$_SESSION['customer']['sponsor_email'] = $spon_row['email'];
					$_SESSION['customer']['sponsor_cell_phone'] = $spon_row['cell_phone'];
					$_SESSION['customer']['sponsor_public_name'] = $spon_row['public_name'];
					$_SESSION['customer']['sponsor_public_phone'] = $spon_row['public_phone'];
					$_SESSION['customer']['sponsor_public_email'] = $spon_row['public_email'];
					$_SESSION['customer']['sponsor_display_in_member'] = $spon_row['display_in_member'];
					$_SESSION['customer']['sponsor_is_branding'] = $spon_row['is_branding'];
          			$_SESSION['customer']['sponsor_brand_icon'] = $spon_row['brand_icon'];
					$_SESSION['customer']['member_services_cell_phone'] = get_app_settings('member_services_cell_phone');
					$_SESSION['customer']['member_services_email'] = get_app_settings('member_services_email');
				}
				/*--/Set Sponsor Data To Session --*/

				$description = array();
				$description['ac_message'] =array(
					'ac_red_1'=>array(
						'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
						'title' => $_SESSION['agents']['rep_id'],
					),
					
					'ac_message_1' =>' Switch Login On Member '.$sub_row['fname'].' '.$sub_row['lname'],
					'ac_red_2'=>array(
						'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($sub_row['id']),
						'title'=>"(".$sub_row['rep_id'].")",
					),
				);
				
				activity_feed(3, $_SESSION['agents']['id'], 'Agent',  $sub_row['id'], 'Customer','Agent Switch Login',"","",json_encode($description));
				redirect($HOST . "/member/dashboard.php");

		} else if ($sub_row['type'] == "Group") {
			$_SESSION['groups'] = null;
			unset($_SESSION['groups']);
			$sql = "SELECT c.id,c.fname,c.lname,c.user_name,c.email,c.type,c.cell_phone,c.rep_id,c.sponsor_id,c.status, c.created_at,c.company_id,c.business_name, AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,c.access_type,c.feature_access,cs.tpa_for_billing,s.sponsor_id as s_sponsor_id,scs.agent_coded_level as sponsor_coded_level
                FROM customer c
                JOIN customer_settings cs ON(cs.customer_id = c.id)
				JOIN customer s ON (s.id=c.sponsor_id)
				JOIN customer_settings scs ON(scs.customer_id=s.id)
                WHERE md5(c.id) = '" . $_REQUEST['id'] . "'";
			$sub_row = $pdo->selectOne($sql);

			if($_SESSION['agents']['id'] == $sub_row['sponsor_id'] || (!empty($sub_row['sponsor_coded_level']) && $sub_row['sponsor_coded_level'] == 'LOA' && $_SESSION['agents']['id'] == $sub_row['s_sponsor_id'])) {
			}else {
				setNotifyError("you dont have access to this page");        
				echo "<script>window.parent.location = 'groups_listing.php'; window.location = 'groups_listing.php'; </script>";
				exit;
			}
			
			$real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
			$updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address ,'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
			$where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
			$pdo->update("customer_settings", $updateStr, $where);

			$_SESSION['groups'] = $sub_row;
			$_SESSION['groups']['access'] = $sub_row['feature_access'] != "" ? explode(",",$sub_row['feature_access']) : array();
			$_SESSION['groups']['access_type'] = $sub_row['access_type'];
			$_SESSION['groups']['admin_switch'] = 'yes';
			$_SESSION['groups']['timezone'] = $_SESSION['agents']['timezone'];
			$description = array();
			$description['ac_message'] =array(
				'ac_red_1'=>array(
					'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
					'title' => $_SESSION['agents']['rep_id'],
				),				
				'ac_message_1' =>' Switch Login On Group '.$sub_row['business_name'],
				'ac_red_2'=>array(
					'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($sub_row['id']),
					'title'=>"(".$sub_row['rep_id'].")",
				),
			);
			activity_feed(3, $_SESSION['agents']['id'], 'Agent',  $sub_row['id'], 'Group','Agent Switch Login',"","",json_encode($description));
			redirect($HOST . "/groups/dashboard.php");
		} else {
			$_SESSION['customer'] = null;
			unset($_SESSION['customer']);
			$_SESSION['customer'] = $sub_row;
			$_SESSION['customer']['admin_switch'] = 'yes';
			redirect($HOST . "/member/dashboard.php");
		}
	}else{
		setNotifyError("No Record found!");
		redirect($ADMIN_HOST . "/agent_listing.php");
	}
}
?>