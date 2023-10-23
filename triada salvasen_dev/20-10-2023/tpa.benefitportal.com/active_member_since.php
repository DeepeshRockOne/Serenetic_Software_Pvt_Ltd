<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
$enrollDate = new enrollmentDate();
$error = "";

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
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
		                'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Active member since date changed from : '.displayDate($old_active_date).' to : '.displayDate($active_date),
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
		                'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Active member since date changed from : '.displayDate($old_active_date).' to : '.displayDate($active_date),
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
		                'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Active member since date changed from : '.displayDate($old_active_date).' to : '.displayDate($active_date),
		            );
		            activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
	            }		       
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