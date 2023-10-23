<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commissions";
$breadcrumbes[1]['link'] = 'pmpm_commission.php';
$breadcrumbes[2]['title'] = "PMPMs";
$breadcrumbes[2]['link'] = 'add_pmpm_commission.php';
$page_title = "PMPMs";

$response = array();
$pmpm_id = isset($_GET['id']) ? $_GET['id'] : 0;
$is_clone = isset($_GET['is_clone']) ? $_GET['is_clone'] : 'N';
$pmpm_fee_ids = "";
$incr = "";
$scr = array();
if(!empty($pmpm_id)){
	$incr .= " AND md5(id) = :id";
	$scr[':id'] = $pmpm_id;
}
if(!empty($pmpm_id) && $is_clone == 'N'){
	$agents = $pdo->select("SELECT id,rep_id,CONCAT(fname,' ',lname) as agent_name FROM customer where type = 'agent' AND id in(select agent_id FROM pmpm_commission WHERE is_deleted='N' $incr)",$scr);
}else{
	$agents = $pdo->select("SELECT id,rep_id,CONCAT(fname,' ',lname) as agent_name FROM customer where type = 'agent' AND id not in(select agent_id FROM pmpm_commission WHERE is_deleted='N')");	
}

$status = "";
$rule_code = "";
$receiving_agents = array();
$delete_id = isset($_POST['delete_id']) ? $_POST['delete_id'] : 0;

if($pmpm_id){
	$pmpm_res = $pdo->selectOne("SELECT pc.id,pc.rule_code,c.rep_id,GROUP_CONCAT(pc.agent_id) as receiving_agents,
								GROUP_CONCAT(pcr.id) as ids,pc.status 
								 FROM pmpm_commission pc
								 LEFT JOIN pmpm_commission_rule pcr ON(pcr.commission_id = pc.id AND pcr.is_deleted = 'N')
								 LEFT JOIN customer c ON (c.id = pc.agent_id)
								 WHERE md5(pc.id) = :id AND pc.is_deleted = 'N'",array(':id' => $pmpm_id));
	if($pmpm_res){
		$receiving_agents = explode(',',$pmpm_res['receiving_agents']);
		$status = $pmpm_res['status'];
		$rule_code = $pmpm_res['rule_code'];
		$pmpm_fee_ids = $pmpm_res['ids'];
		if($is_clone == 'Y'){
			$receiving_agents = array();
			$rule_code=get_pmpm_commission_id();
		}

		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Read PMPM Commission ',
	      'ac_red_2'=>array(
	          'href'=>$ADMIN_HOST.'/add_pmpm_commission.php?id='. $pmpm_id,
	          'title'=>$pmpm_res['rep_id'],
	      ),
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $pmpm_res['id'], 'pmpm_commission','Read PMPM Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

	    if($is_clone == "Y" && !empty($pmpm_fee_ids)){
	    	$feeIds = array();
			$pmpm_fee_ids_arr = explode(",",$pmpm_fee_ids);
			foreach ($pmpm_fee_ids_arr as $k => $v) {
		      	$clone_rules = $pdo->selectOne("SELECT * FROM pmpm_commission_rule WHERE id=:rule_id and is_deleted = 'N'",array(':rule_id' => $v));
		      	if($clone_rules){
		      		$tmp_pmpm_id = 0;
			      	$insert_params = $clone_rules;
					unset($insert_params['id']);
					$insert_params['created_at'] = 'msqlfunc_NOW()';
					$insert_params['updated_at'] = 'msqlfunc_NOW()';
					$insert_params['display_id'] = get_pmpm_comm_fee_id();
					$insert_params['commission_id'] = $tmp_pmpm_id;        
					$tmp_rule_id = $pdo->insert('pmpm_commission_rule',$insert_params);
					array_push($feeIds,$tmp_rule_id);

					$rule_plans = $pdo->select("SELECT * FROM pmpm_commission_rule_plan_type WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));
					if($rule_plans){
						foreach ($rule_plans as $key => $value) {
							$insert_params = $value;
							unset($insert_params['id']);
							$insert_params['created_at'] = 'msqlfunc_NOW()';
							$insert_params['updated_at'] = 'msqlfunc_NOW()';
							$insert_params['commission_id'] = $tmp_pmpm_id;
							$insert_params['rule_id'] = $tmp_rule_id;
							$pdo->insert('pmpm_commission_rule_plan_type',$insert_params);
						}
					}

					$rule_products = $pdo->select("SELECT * FROM pmpm_commission_rule_assign_product WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));
					if($rule_products){
						foreach ($rule_products as $key => $value) {
							$insert_params = $value;
							unset($insert_params['id']);
							$insert_params['created_at'] = 'msqlfunc_NOW()';
							$insert_params['updated_at'] = 'msqlfunc_NOW()';
							$insert_params['commission_id'] = $tmp_pmpm_id;
							$insert_params['rule_id'] = $tmp_rule_id;
							$pdo->insert('pmpm_commission_rule_assign_product',$insert_params);
						}
					}

					$rule_agents = $pdo->select("SELECT * FROM pmpm_commission_rule_assign_agent WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));
					if($rule_agents){
						foreach ($rule_agents as $key => $value) {
							$insert_params = $value;
							unset($insert_params['id']);
							$insert_params['created_at'] = 'msqlfunc_NOW()';
							$insert_params['updated_at'] = 'msqlfunc_NOW()';
							$insert_params['commission_id'] = $tmp_pmpm_id;
							$insert_params['rule_id'] = $tmp_rule_id;
							$pdo->insert('pmpm_commission_rule_assign_agent',$insert_params);
						}
					}
		      	}
		  	}
		  	$pmpm_fee_ids = implode(",",$feeIds);
	    }
	}
}else{
	$rule_code=get_pmpm_commission_id();
}

if(isset($_POST['action']) && $_POST['action'] == 'delete' && $delete_id > 0){

	$data = $pdo->selectOne("SELECT id,display_id FROM pmpm_commission_rule WHERE id = :id",array(":id" => $delete_id));
	if($data){
		$updateParams = array('is_deleted' => 'Y');
		$update_where = array(
		'clause' => 'id=:id',
		'params' => array(
		  ":id" => $delete_id,
		)
		);
		$update_status = $pdo->update('pmpm_commission_rule', $updateParams, $update_where);

		$update_where = array(
		'clause' => 'rule_id=:id',
		'params' => array(
		  ":id" => $delete_id,
		)
		);
		$update_status = $pdo->update('pmpm_commission_rule_plan_type', $updateParams, $update_where);
		$update_status = $pdo->update('pmpm_commission_rule_assign_product', $updateParams, $update_where);
		$update_status = $pdo->update('pmpm_commission_rule_assign_agent', $updateParams, $update_where);

		$response['status'] = "success";
		$response['message'] = 'Fee deleted successfully';

		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Deleted PMPM Commission Fee',
	      'ac_red_2'=>array(
	          'title'=>$data['display_id'],
	      ),
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $delete_id, 'pmpm_commission','Deleted PMPM Commission Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

	}else{
		$response['status'] = "fail";
		$response['message'] = 'Fee Not Found';
	}
	echo json_encode($response);
	exit();
}
if (isset($_GET['status']) && isset($_GET['feeId'])) {
	$feeId = $_GET['feeId'];
	$status = $_GET['status'];
	$data = $pdo->selectOne("SELECT id,display_id FROM pmpm_commission_rule WHERE id = :id",array(":id" => $feeId));
	if($data){
		$updateParams = array('status' => $status);
		$update_where = array(
		'clause' => 'id=:id',
		'params' => array(
		  ":id" => $feeId,
		)
		);
		$update_status = $pdo->update('pmpm_commission_rule', $updateParams, $update_where);

		$update_where = array(
		'clause' => 'rule_id=:id',
		'params' => array(
			  ":id" => $feeId,
			)
		);
		

		$response['status'] = "success";
		$response['message'] = 'Status changed successfully';

		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Changed PMPM Commission Fee Status',
	      'ac_red_2'=>array(
	          'title'=>$data['display_id'],
	      ),
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $feeId, 'pmpm_commission','Changed PMPM Commission Fee Status', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

	}else{
		$response['status'] = "fail";
		$response['message'] = 'Fee Not Found';
	}
	echo json_encode($response);
	exit();
}

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array(
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'add_pmpm_commission.inc.php';
include_once 'layout/end.inc.php';
?>