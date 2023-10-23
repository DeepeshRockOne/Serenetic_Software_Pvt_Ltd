<?php
include_once __DIR__ . '/includes/connect.php';
$lead_quote_id = isset($_GET['id'])?$_GET['id']:'0';
$sent_via = isset($_GET['sent_via'])?$_GET['sent_via']:'Both';
$enrollmentLocation = isset($_GET['enrollmentLocation'])?$_GET['enrollmentLocation']:'';
$is_add_product = isset($_GET['is_add_product'])?$_GET['is_add_product']:'N';
$auto_refresh = isset($_GET['auto_refresh'])?$_GET['auto_refresh']:'1';
$site_user_name = isset($_GET['site_user_name'])?$_GET['site_user_name']:'';
$lead_quote_sql = "SELECT lq.id,lq.is_opened,lq.link_opened_at,l.lead_id as lead_rep_id,l.fname,l.lname,lq.order_ids as order_id,lq.customer_ids as customer_id
					FROM lead_quote_details lq
					JOIN leads l ON(l.id = lq.lead_id)
					WHERE lq.id=:id";
$lead_quote_row = $pdo->selectOne($lead_quote_sql, array(":id"=>$lead_quote_id));
$response = array();
if(empty($lead_quote_row)) {
	$response['status'] = 'fail';
	$response['message'] = 'Verification Not Found';
	echo json_encode($response);
	exit;
}

$customer_id = $lead_quote_row['customer_id'];
$link_opened_at = $lead_quote_row['link_opened_at'];

$customer_sql = "SELECT c.id,c.sponsor_id,s.type as sponsor_type,c.fname,c.lname,c.rep_id,c.email,c.is_password_set 
					FROM customer c 
					JOIN customer s ON(s.id = c.sponsor_id)  
					WHERE c.id=:id";
$customer_row = $pdo->selectOne($customer_sql,array(":id" => $customer_id));

$sponsor_billing_method = "individual";
if($customer_row['sponsor_type'] == "Group") {
	$sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
	$resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$customer_row['sponsor_id']));
	if(!empty($resBillingType)){
		$sponsor_billing_method = $resBillingType['billing_type'];
	}
}

$group_ord_row = array();
$tran_res = array();
$ord_row = array();
if(strtotime($link_opened_at)) {
	$ord_row = $pdo->selectOne("SELECT id,display_id,future_payment FROM orders WHERE id=:order_id",array(":order_id" => $lead_quote_row['order_id']));
	$ord_row['future_payment'] = "N";

	$tran_res = $pdo->select("SELECT id,transaction_type,transaction_response,transaction_status FROM `transactions` WHERE order_id=:order_id AND created_at > :created_at ORDER BY id ASC",array(":order_id" => $lead_quote_row['order_id'],':created_at' => $link_opened_at));
	if(count($tran_res) == 1) {
		if($tran_res[0]['transaction_status'] == "Post Payment") {
			$ord_row['future_payment'] = "Y";			
		}
	}
	if($sponsor_billing_method !== "individual") {
		$group_ord_row = $pdo->selectOne("SELECT id FROM group_orders WHERE id=:order_id AND status='Payment Approved'",array(":order_id" => $lead_quote_row['order_id']));
	}
}




$is_approved_tran = false;

$agent_sql = "SELECT c.*,cs.not_show_eo_expired,cs.display_in_member,cs.is_branding,cs.brand_icon  FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE c.id=:id AND c.is_deleted='N' AND c.type !='Customer' AND c.status IN('Active','Contracted')";
$agent_row = $pdo->selectOne($agent_sql,array(":id" => $customer_row['sponsor_id']));

if(!empty($agent_row['id'])) {
	$parent_agent_sql = "SELECT c.*,cs.not_show_eo_expired,cs.display_in_member,cs.is_branding,cs.brand_icon FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE c.id=:id";
	$parent_agent_row = $pdo->selectOne($parent_agent_sql,array(":id" => $agent_row['id']));	
}

if(empty($parent_agent_row)) { //For Root Agent
	$parent_agent_row = $agent_row;
}

$end_enrollment_url = "";
if($enrollmentLocation == "aae_site" && !empty($site_user_name)) {
	$end_enrollment_url = $AAE_WEBSITE_HOST . "/" . $site_user_name;
} elseif($enrollmentLocation == "self_enrollment_site") {
	$end_enrollment_url = $ENROLLMENT_WEBSITE_HOST . "/" . $site_user_name;
}

$update_enrollment_url = "";
$add_product_url = '';
if($is_add_product == '1'){
	$add_product_url = '&customer_id='.md5($customer_id);
}
if(in_array($enrollmentLocation,array("aae_site","groupSide")) && !empty($site_user_name) && !empty($lead_quote_id)) {
	$update_enrollment_url = $AAE_WEBSITE_HOST . "/" . $site_user_name ."/".md5($lead_quote_id)."/q";

} elseif($enrollmentLocation == "self_enrollment_site" && !empty($site_user_name) && !empty($lead_quote_id)) {
	$update_enrollment_url = $ENROLLMENT_WEBSITE_HOST . "/" . $site_user_name ."/".md5($lead_quote_id)."/q";

} elseif($enrollmentLocation == "agentSide" && !empty($lead_quote_id)) {
	$update_enrollment_url = $AGENT_HOST . "/member_enrollment.php?quote_id=".md5($lead_quote_id) . $add_product_url;

} elseif($enrollmentLocation == "groupSide" && !empty($lead_quote_id)) {
	$update_enrollment_url = $GROUP_HOST . "/member_enrollment.php?quote_id=".md5($lead_quote_id) . $add_product_url;

} elseif($enrollmentLocation == "adminSide" && !empty($lead_quote_id)) {
	$update_enrollment_url = $ADMIN_HOST . "/member_enrollment.php?quote_id=".md5($lead_quote_id).'&customer_id='.md5($customer_id);
}

ob_start();
?>
	<!-- ---------- -->
	<ul class="verify_process">
		<li><i class="material-icons text-success">check_circle</i><?=strtolower($sent_via) == "both"?"Email/Text":ucfirst($sent_via)?> Message Verification Delivered</li>
		<?php if($lead_quote_row['is_opened'] == "N") { ?>
			<li><i class="fa fa-spinner fa-pulse"></i><span class="sr-only">Loading...</span>Application Link Opened</li>
		<?php } else { ?>
			<li><i class="material-icons text-success">check_circle</i>Application Link Opened</li>
			<?php if($sponsor_billing_method != "individual") { ?>
				<?php if(count($group_ord_row) == 0) { ?>
					<li><i class="fa fa-spinner fa-pulse"></i><span class="sr-only">Loading...</span>Pending Review and Payment</li>
				<?php } else { 
					$is_approved_tran = true;
					?>
					<li><i class="material-icons text-success">check_circle</i>Pending Review and Payment</li>
					<li><i class="material-icons text-success">check_circle</i>Application Verification Completed</li>
				<?php } ?>
			<?php } else { ?>
				<?php if(count($tran_res) == 0) { ?>
					<li><i class="fa fa-spinner fa-pulse"></i><span class="sr-only">Loading...</span>Pending Review and Payment</li>
				<?php } else { ?>
					<li><i class="material-icons text-success">check_circle</i>Pending Review and Payment</li>

					<?php if($ord_row['future_payment'] == "Y") { $is_approved_tran = true; ?>
						<li><i class="material-icons text-success">check_circle</i>Post Payment Set</li>

					<?php } else { ?>
						<li><i class="material-icons text-success">check_circle</i>Payment Attempted</li>
						<?php
							foreach ($tran_res as $key => $tran_row) {
								if($tran_row['transaction_type'] == "New Order") { //CC
									echo '<li class="sub_status text-success"><i class="material-icons">check_circle</i>Payment Approved</li>';
									$is_approved_tran = true;
									?>
									<?php if(in_array($enrollmentLocation,array("agentSide","aae_site","self_enrollment_site"))) { ?>
										<li><i class="material-icons text-success">check_circle</i>Application Completed - <a href="javascript:void(0);" class="btn red-link pn odrReceipt" data-odrId="<?=md5($ord_row["id"])?>"><?=$ord_row['display_id']?></a>
											<?php if($customer_row['is_password_set'] == "N") { ?> 
												<strong class=""><?=$customer_row['rep_id']?> - <?=$customer_row['fname']?> <?=$customer_row['lname']?> <br/>
												<p class="m-l-30">A temporary password will be sent to <?=$customer_row['email']?>. Please encourage the member to check their spam/junk mail.</p></strong>
											<?php } ?> 
										</li>
									<?php } else { ?>
										<li><i class="material-icons text-success">check_circle</i>Application Completed!
											<?php if($customer_row['is_password_set'] == "N") { ?> 
												<strong class=""><?=$customer_row['rep_id']?> - <?=$customer_row['fname']?> <?=$customer_row['lname']?> <br/>
												<p class="m-l-30">A temporary password will be sent to <?=$customer_row['email']?>. Please encourage the member to check your spam/junk folder.</p></strong>
											<?php } ?> 
										</li>
									<?php } ?>
									<?php

								} elseif($tran_row['transaction_type'] == "Pending") { //ACH Pending Settlement
									echo '<li class="sub_status text-warning"><i class="material-icons text-success">check_circle</i>Payment Approved</li>';
									$is_approved_tran = true;

								} elseif($tran_row['transaction_type'] == "Post Payment") { //ACH Pending Settlement
									echo '<li class="sub_status text-warning"><i class="material-icons text-success">check_circle</i>Post Payment Set</li>';
									$is_approved_tran = true;

								} else {
									$declined_reason = get_declined_reason_from_tran_response($tran_row['transaction_response']);
									echo '<li class="sub_status text-action"><i class="material-icons">cancel</i><strong>Payment Declined </strong> (Reason : '.$declined_reason.')</li>';
								}

								if($is_approved_tran == true) {
									break;
									//Stop foreach after approved trans found
								}
							}
						?>
					<?php } ?>						
				<?php } ?>
			<?php } ?>
		<?php } ?>
	</ul>
		<div class="p-30">
			<p>Kindly make sure lead is not making changes to the application at the same time and ask them to close or reload their verification link if you wish to update application </p>
		</div>
		<div class="text-center m-b-30">
			<?php if($is_approved_tran == false) { ?>
				<a href="javascript:void(0);" data-href="<?=$update_enrollment_url?>" class="btn btn-info" id="btn_update_enrollment">Update Application</a>
			<?php } ?>	
			<a href="javascript:void(0);" data-href="<?=$end_enrollment_url?>" class="btn blue-link" id="btn_end_enrollment">End Application</a>
		</div>
	<!-- ---------- -->
<?php
$response['html'] = ob_get_clean();

$response['status'] = 'success';
echo json_encode($response);
dbConnectionClose();
exit;

?>