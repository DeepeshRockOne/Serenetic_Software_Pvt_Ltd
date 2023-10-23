<?php
include_once __DIR__ . '/includes/connect.php';
$agent_id = $_SESSION["agents"]["id"];
$agent_id = md5($agent_id);

$license_sql = "SELECT * FROM agent_license WHERE is_rejected='Y' AND md5(agent_id)=:agent_id AND is_deleted='N'";
$license_res = $pdo->select($license_sql,array(":agent_id"=>$agent_id));
if (!empty($license_res)) {
	foreach ($license_res as $key => $license_row) {
		$upd_where = array(
	        'clause' => 'id = :id',
	        'params' => array(
	            ':id' => $license_row['id'],
	        ),
	    );
	    if(!empty($license_row['license_num'])) {
	        $upd_data = array(
	            'new_request' => 'N',
	            'is_rejected' => 'N',
	            'new_license_status' => '',
	            'new_selling_licensed_state' => '',
	            'new_license_num' => '',
	            'new_license_active_date'=> NULL,
	            'new_license_not_expire' => '',
	            'new_license_exp_date' => NULL,
	            'new_license_type' => '',
	            'new_license_auth' => '',
	            'updated_at' => 'msqlfunc_NOW()',
	        );
	        $pdo->update('agent_license',$upd_data,$upd_where);   

	        $desc = array();
	        $desc['ac_message'] = array(
	            'ac_red_1'=>array(
	                'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
	                'title'=> $_SESSION['agents']['rep_id'],
	            ),
	            'ac_message_1' =>' Updated Profile <br/>',
	            'ac_message_2' =>' Cancelled License Update for State : '.$license_row['selling_licensed_state'].'.'
	        );
	        $desc=json_encode($desc);
	        activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Agent Cancelled License Update',"","",$desc);
	    } else {
	        $pdo->update('agent_license', array("is_deleted" => 'Y', 'updated_at' => 'msqlfunc_NOW()', 'license_removal_date'=>'msqlfunc_NOW()','license_status'=>'Inactive'), $upd_where);       

	        $desc = array();
	        $desc['ac_message'] = array(
	            'ac_red_1'=>array(
	                'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
	                'title'=> $_SESSION['agents']['rep_id'],
	            ),
	            'ac_message_1' =>' Updated Profile <br/>',
	            'ac_message_2' =>' License Deleted for State : '.$license_row['selling_licensed_state'].'.'
	        );
	        $desc=json_encode($desc);
	        activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Agent License Deleted',"","",$desc);                    
	    }
	}
}
$updateParams = array(
	'license_reject_status'=>'N',
	'license_reject_text' => '',
);
$update_where = array(
	'clause' => 'md5(customer_id) = :id',
	'params' => array(
		':id' => $agent_id,
	),
);
$pdo->update('customer_settings',$updateParams,$update_where);
echo json_encode(array("status" => "success"));
dbConnectionClose();
?>
