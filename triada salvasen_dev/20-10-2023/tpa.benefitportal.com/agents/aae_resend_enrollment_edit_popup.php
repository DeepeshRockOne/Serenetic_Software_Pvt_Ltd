<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$function_list = new functionsList();

$lead_quote_id = isset($_REQUEST['lead_quote_id']) ? $_REQUEST['lead_quote_id'] : "";
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$is_resend = isset($_POST['is_resend']) ? $_POST['is_resend'] : "";

if($lead_quote_id){
	$lead_info = $pdo->selectOne("SELECT l.id,l.email,l.fname,l.lname,l.cell_phone,l.customer_id,l.sponsor_id,lqd.order_ids From leads l JOIN lead_quote_details lqd on(lqd.lead_id = l.id) WHERE md5(lqd.id) = :id",array(":id" => $lead_quote_id));
}

if($is_resend == 'Yes'){
	$response = array();
	$lead_info = $pdo->selectOne("SELECT l.id,l.email,l.fname,l.lname,l.cell_phone,l.customer_id,l.sponsor_id,lqd.order_ids From leads l JOIN lead_quote_details lqd on(lqd.lead_id = l.id) WHERE md5(lqd.id) = :id",array(":id" => $lead_quote_id));

	if($lead_info){

		$sponsor_row = $pdo->selectOne("SELECT * From customer WHERE id = :id",array(":id" => $lead_info['sponsor_id']));

		$token_val = md5('TOKEN'.$lead_info['order_ids']);
		$url_link = $HOST . '/quote/enroll_varification/'. $token_val;
		$url_params = array(
			'dest_url' => $url_link,
			'agent_id' => $lead_info['sponsor_id'],
			'customer_id' => $lead_info['customer_id'],
		);
		$link = get_short_url($url_params);

		$rep_id = getname('customer',$lead_info['customer_id'],'rep_id','id');
 
		$mail_data = array();
		$mail_data['fname'] = $lead_info['fname'];
		$mail_data['lname'] = $lead_info['lname'];
		$mail_data['Email'] = $lead_info['email'];
		$mail_data['Phone'] = $lead_info['cell_phone'];
		$mail_data['MemberID'] = $rep_id;
		$agent_detail = $function_list->get_sponsor_detail_for_mail($lead_info['customer_id'], $lead_info['sponsor_id']);
		$mail_data['Agent'] = $agent_detail['agent_name'];
		if(!empty($sponsor_row['sponsor_id'])){
			$parent_agent_detail = $function_list->get_sponsor_detail_for_mail($lead_info['customer_id'], $sponsor_row['sponsor_id']);
			$mail_data['ParentAgent'] = $parent_agent_detail['agent_name'];
		}

		if (!empty($agent_detail)) {
			$mail_data['agent_name'] = $agent_detail['agent_name'];
			$mail_data['agent_email'] = $agent_detail['agent_email'];
			$mail_data['agent_phone'] = format_telephone($agent_detail['agent_phone']);
			$mail_data['agent_id'] = $agent_detail['agent_id'];
			$mail_data['is_public_info'] = $agent_detail['is_public_info'];
		} else {
			$mail_data['is_public_info'] = 'display:none';
		}

		$mail_data['link'] = $link;
		$mail_data['USER_IDENTITY'] = array('rep_id' => $rep_id, 'cust_type' => 'Agent', 'location' => $REQ_URL);
		if ($SITE_ENV == 'Local') {
			$primary_email = 'karan@cyberxllc.com';
		}

		$smart_tags = get_user_smart_tags($lead_info['id'],'lead');
                
	    if($smart_tags){
	        $mail_data = array_merge($mail_data,$smart_tags);
	    }
		$mail_sent_status = trigger_mail(84, $mail_data, $lead_info['email'], array(), 3);

		if($mail_sent_status == "success"){
			$response['mail_status'] = 'success';

			$description['ac_message'] = array(
			    'ac_red_1' => array(
			        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
			        'title' => $_SESSION['agents']['rep_id'],
			    ),
			    'ac_message_1' => ' Resend Application'
			);
			$desc = json_encode($description);
			activity_feed(3, $_SESSION['agents']['id'], 'Agent', $lead_info['id'], 'Lead', 'Agent Resend Application.', $lead_info['fname'], $lead_info['lname'], $desc);


		}else{
			$response['mail_status'] = 'fail';
		}
		$response['status'] = "success";
	}else{
		$response['status'] = "fail";
	}

	echo json_encode($response);
	exit();
}


$template = 'aae_resend_enrollment_edit_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>