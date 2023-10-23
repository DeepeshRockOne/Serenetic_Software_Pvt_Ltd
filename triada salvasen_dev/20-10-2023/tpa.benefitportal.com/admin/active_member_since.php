<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
$enrollDate = new enrollmentDate();
$error = "";

$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "";
$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where md5(id)=:id",array(':id' => $ws_id));
$customer_id = $ws_row['customer_id'];
$product_id = $ws_row['product_id'];
$plan_id = $ws_row['plan_id'];

$response = array();

if(isset($_POST['is_submit']) && $_POST['is_submit'] == 'Y'){
	$active_date = isset($_POST['active_date']) ? $_POST['active_date'] : "";
	if(empty($active_date)){
		$error = "Please select active date";
        $response['error'] = $error;
        $response['status'] = 'fail';
	}else{

		if (!empty(strtotime($active_date)) && strtotime($active_date) != strtotime($ws_row['active_date'])) {

		        $upd_active_date_data = array(
		            'active_date' => date('Y-m-d', strtotime($active_date))
		        );
		        $upd_active_date_where = array(
		            "clause" => "id=:subscription_id",
		            "params" => array(":subscription_id" => $ws_row['id'])
		        );
		        $pdo->update("website_subscriptions", $upd_active_date_data, $upd_active_date_where);


		        $upd_active_date_data = array(
		            'active_since' => date('Y-m-d', strtotime($active_date))
		        );
		        $upd_active_date_where = array(
		            "clause" => "website_id=:id and is_deleted = 'N'",
		            "params" => array(":id" => $ws_row['id'])
		        );
		        $pdo->update("customer_dependent", $upd_active_date_data, $upd_active_date_where);

		        $customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
	            $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
	            $old_active_date = $ws_row['active_date'];

	            $af_message = 'changed active member since date';
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
	                'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Active member since date changed from : '.displayDate($old_active_date).' to : '.displayDate($active_date),
	            );
	            activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

		        // if(!empty(strtotime($ce_row['active_date']))){
		        //     $activity_feed_data = array(
		        //         'old_date' => date('m/d/Y', strtotime($ce_row['active_date'])),
		        //         'new_date' => date('m/d/Y', strtotime($active_date)),
		        //         'admin_name' => $_SESSION['admin']['name'],
		        //         'admin_id' => $_SESSION['admin']['id'],
		        //         'product_id' => $ws_row['product_id'],
		        //         'plan_id' => $ws_row['plan_id'],
		        //     );
		        //     activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'Active Member Since Date Update', '', '', json_encode($activity_feed_data));
		        // }

		       
		}
		$response['status'] = 'success';
        $response['active_date'] = date('m/d/Y',strtotime($active_date));
        $response['message'] ='The active date has been updated.';

		

	}
	echo json_encode($response);
    exit();
}


$template = 'active_member_since.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>