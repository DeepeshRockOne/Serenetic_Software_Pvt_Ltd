<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/function.class.php';

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$ws_id = isset($_POST['ws_id']) ? $_POST['ws_id'] : "";
$reason = isset($_POST['reason']) ? $_POST['reason'] : "";
$response = array();
$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where id = :id",array(':id' => $ws_id));
$customer_row = $pdo->selectOne("SELECT id,rep_id FROM customer where id = :id",array(':id' => $ws_row['customer_id']));

if($ws_row && $reason){

    $upd_term_date_data = array('termination_reason' => $reason);
    $upd_term_date_where = array(
        "clause" => "id=:id",
        "params" => array(":id" => $ws_row['id'])
    );

    $pdo->update("website_subscriptions", $upd_term_date_data, $upd_term_date_where);

    $af_message = ' changed termination reason';
    $af_desc = array();
    if($location == "admin") {
        
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
            'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Termination reason changed from '.$ws_row['termination_reason'].' to '.$reason,
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

    } elseif($location == "agent") {

        $af_desc['ac_message'] =array(
            'ac_red_1'=>array(
                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' => $af_message.' on ',
            'ac_red_2'=>array(
                'href'=> 'members_details.php?id='.md5($customer_row['id']),
                'title'=>$customer_row['rep_id'],
            ),
            'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Termination reason changed from '.$ws_row['termination_reason'].' to '.$reason,
        );
        activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
    }

    // setNotifySuccess("Termination date removed successfully");
    $response['status'] = "success";
    $response['message'] = "Reason updated successfully";
    
}else{
    $response['status'] = "fail";
    $response['message'] = "Something went wrong";
}
echo json_encode($response);
dbConnectionClose();
exit();
?>