<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
$enrollDate = new enrollmentDate();
$validate = new Validation();

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "-1";
$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE md5(id)=:id",array(':id' => $ws_id));
if(empty($ws_row)) {
	setNotifyError("Please try again, Plan Not Found.");
	echo '<script type="text/javascript">parent.window.location.reload();</script>';
	exit();
}
$response = array();
if(isset($_POST['is_submit']) && $_POST['is_submit'] == 'Y'){
	$next_purchase_date = isset($_POST['next_purchase_date']) ? $_POST['next_purchase_date'] : "";
	$retain_rule = checkIsset($_POST['retain_rule']);

	if(!(strtotime($next_purchase_date) > 0)){
		$validate->setError("next_purchase_date","Please Select Next Billing Date");
	}else 
    $updateNBD = true;
    if(strtotime($next_purchase_date) == strtotime($ws_row['next_purchase_date'])){
        $updateNBD = false;
	}
	$validate->string(array('required' => true, 'field' => 'retain_rule', 'value' => $retain_rule), array('required' => 'Please select any option'));

	if($validate->isValid()){

        if($updateNBD){
            $upd_next_purchase_date_data = array(
            'next_purchase_date' => date('Y-m-d', strtotime($next_purchase_date)),
            'next_attempt_at' => NULL,
            'total_attempts' => 0,
            'next_purchase_date_changed' => "Y",
            'manual_next_purchase_date' => date('Y-m-d', strtotime($next_purchase_date)),
            'next_purchase_date_retain_rule' => $retain_rule
            );
            $upd_next_purchase_date_where = array(
                "clause" => "id=:id",
                "params" => array(":id" => $ws_row['id']));
            $pdo->update("website_subscriptions", $upd_next_purchase_date_data, $upd_next_purchase_date_where);

            $message = 'Next billing date changed from "' . $ws_row['next_purchase_date'] . '" to "' . $next_purchase_date . '"';
            $web_history_data = array(
                'customer_id' => $ws_row['customer_id'],
                'website_id' => $ws_row['id'],
                'product_id' => $ws_row['product_id'],
                'plan_id' => $ws_row['plan_id'],
                'order_id' => 0,
                'status' => 'Update',
                'message' => $message,
                'authorize_id' => '',
                'processed_at' => 'msqlfunc_NOW()',
                'created_at' => 'msqlfunc_NOW()',
            );
            $pdo->insert("website_subscriptions_history", $web_history_data);
        }else{
            $upd_next_purchase_date_data = array(
                'next_purchase_date_changed' => "Y",
                'manual_next_purchase_date' => date('Y-m-d', strtotime($next_purchase_date)),
                'next_purchase_date_retain_rule' => $retain_rule
            );
            $upd_next_purchase_date_where = array(
                "clause" => "id=:id",
                "params" => array(":id" => $ws_row['id'])
            );
            $pdo->update("website_subscriptions", $upd_next_purchase_date_data, $upd_next_purchase_date_where);
        }

        $customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
        $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
        $old_next_purchase_date = $ws_row['next_purchase_date'];

        if($retain_rule == "allRenewal"){
        	$retain_af = "Retain this date for all future renewals";
        }else{
			$retain_af = "Retain this date for one renewal";
        }

        if(!$updateNBD){
            $af_message = 'updated next billing date retain rule';
            $af_message2 = ' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Next billing date : '.displayDate($next_purchase_date) .'<br />'.$retain_af;
        }else{
            $af_message = 'changed next billing date';
            $af_message2 = ' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Next billing date changed from : '.displayDate($old_next_purchase_date).' to : '.displayDate($next_purchase_date) .'<br />'.$retain_af;
        }
        if($location == "admin") {
        	$af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=> $_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' => $af_message2,
            );
            activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
    	
    	} elseif($location == "agent") {
    		$af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                	'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' => $af_message2,
            );
            activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));

        } elseif($location == "group") {
    		$af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                	'title'=> $_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' => $af_message2,
            );
            activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));

        }
		$response['status'] = 'success';
        $response['next_purchase_date'] = date('m/d/Y',strtotime($next_purchase_date));
        $response['message'] ='The next purchase date has been updated.';
	}else{
		$errors = $validate->getErrors();
	    $response['errors'] = $errors;
	    $response['status'] = "fail";
	}
 	echo json_encode($response);
    exit();
}
if(strtotime('now') < strtotime($ws_row['end_coverage_period'])){
	$end_date = date('m/d/Y',strtotime($ws_row['end_coverage_period']));
} else {
	$end_date = date('m/d/Y',strtotime('now'));
}

$retain_rule = $ws_row["next_purchase_date_changed"] == "Y" ? $ws_row["next_purchase_date_retain_rule"] : "";

$template = 'edit_next_billing_date.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>