<?php
include_once __DIR__ . '/includes/connect.php';

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$id = isset($_POST['ce_id']) ? $_POST['ce_id'] : "";
$response = array();
if($id){
	$ce_row = $pdo->selectOne("SELECT ce.id,ce.old_coverage_file,ce.website_id,c.rep_id,c.id as customerId 
			From customer_enrollment ce 
			JOIN website_subscriptions ws ON(ws.id=ce.website_id)
			JOIN customer c ON(c.id=ws.customer_id)
			where ce.id = :id",array(':id' => $id));
	
	if($ce_row){
		$updParam = array('old_coverage_file' => '');
		$updWhere = array(
		'clause' => 'id = :id',
		'params' => array(':id' => $id)
		);
		$pdo->update('customer_enrollment', $updParam, $updWhere);

		$af_message = ' removed Proof of Prior Coverage';
	    $af_desc = array();
	    if($location == "admin") {
	        $af_desc['ac_message'] =array(
	            'ac_red_1'=>array(
	                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
	                'title'=> $_SESSION['admin']['display_id'],
	            ),
	            'ac_message_1' => $af_message.' on ',
	            'ac_red_2'=>array(
	                'href'=> 'members_details.php?id='.md5($ce_row['customerId']),
	                'title'=>$ce_row['rep_id'],
	            ),
	            'ac_message_2' =>' <br/> Plan : '.display_policy($ce_row['website_id']),
	        );
	        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ce_row['customerId'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

	    } elseif($location == "agent") {
	        
	        $af_desc['ac_message'] =array(
	            'ac_red_1'=>array(
	                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
	                'title' => $_SESSION['agents']['rep_id'],
	            ),
	            'ac_message_1' => $af_message.' on ',
	            'ac_red_2'=>array(
	                'href'=> 'members_details.php?id='.md5($ce_row['customerId']),
	                'title'=>$ce_row['rep_id'],
	            ),
	            'ac_message_2' =>' <br/> Plan : '.display_policy($ce_row['website_id']),
	        );
	        activity_feed(3, $_SESSION['agents']['id'], 'Agent',$ce_row['customerId'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
	    }

		$response['status'] = 'success';
		$response['message'] = 'Document deleted';
		$response['id'] = $id;
	}else{
		$response['status'] = 'fail';
		$response['message'] = 'File not Found';
	}
	
}else{
	$response['status'] = 'fail';
	$response['message'] = 'File not Found';
}
echo json_encode($response);
dbConnectionClose();
exit();

?>