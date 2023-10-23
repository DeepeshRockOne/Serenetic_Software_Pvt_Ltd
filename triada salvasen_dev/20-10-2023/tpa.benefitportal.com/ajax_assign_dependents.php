<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
$validate = new Validation();
$functionClass = new functionsList();
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$ws_id = isset($_POST['ws_id']) ? $_POST['ws_id'] : "";
$ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE md5(ws.id)=:id";
$ws_row = $pdo->selectOne($ws_sql,array(":id" => $ws_id));

$dependent_id = isset($_POST['assigned_dependents']) ? $_POST['assigned_dependents'] : "-1";
$effective_date = isset($_POST['effective_date']) ? $_POST['effective_date'] : "";

$dep_data = $pdo->selectOne("SELECT * FROM customer_dependent_profile WHERE id=:id",array(':id' => $dependent_id));
if(empty($dep_data)) {
	$dependent_id = '';
}

$validate->string(array('required' => true, 'field' => 'assign_dependents', 'value' => $dependent_id), array('required' => 'Please select dependent'));
$validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Please select effective date'));

if ($validate->isValid()) {
	$dep_data['cd_profile_id'] = $dep_data['id'];
	$dep_data['website_id'] = $ws_row['id'];
	$dep_data['order_id'] = $ws_row['last_order_id'];
	$dep_data['product_id'] = $ws_row['product_id'];
	$dep_data['product_plan_id'] = $ws_row['plan_id'];
	$dep_data['prd_plan_type_id'] = $ws_row['prd_plan_type_id'];
	$dep_data['status'] = $ws_row['status'];
	$dep_data['eligibility_date'] = date('Y-m-d',strtotime($effective_date));
	$dep_data['terminationDate'] = NULL;
	$dep_data['active_since'] = strtotime($ws_row['active_date'])>0?$ws_row['active_date']:$ws_row['created_at'];
	$dep_data['hire_date'] = NULL;
	$dep_data['updated_at'] = 'msqlfunc_NOW()';
	$dep_data['created_at'] = 'msqlfunc_NOW()';
	unset($dep_data['id']);
	unset($dep_data['is_disabled']);
	$pdo->insert("customer_dependent",$dep_data);

	$customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
    $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
    $old_eligibility_date = $ws_row['eligibility_date'];
    
    $af_message = 'assigned dependent to plan';
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
	        'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Dependent : '.$dep_data['fname'].' '.$dep_data['lname'].' ('.$dep_data['display_id'].') <br/> Effective From : '.displayDate($effective_date),
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
	        'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Dependent : '.$dep_data['fname'].' '.$dep_data['lname'].' ('.$dep_data['display_id'].') <br/> Effective From : '.displayDate($effective_date),
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
	        'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Dependent : '.$dep_data['fname'].' '.$dep_data['lname'].' ('.$dep_data['display_id'].') <br/> Effective From : '.displayDate($effective_date),
	    );
	    activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
    }
	$response['status'] = "success";
	setNotifySuccess("Dependent added successfully");
} else {
  	$response['status'] = "fail";
  	$response['errors'] = $validate->getErrors();  
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();

exit;


?>