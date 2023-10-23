<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
$functionsList = new functionsList();
$response = array();

$order_id = checkIsset($_POST['order_id']);
$location = isset($_POST['location'])?$_POST['location']:'admin';


if($order_id){
	$order_res = $pdo->selectOne("SELECT id,status,display_id,is_renewal,future_payment,post_date,customer_id,subscription_ids,is_reinstate_order FROM orders WHERE md5(id)=:id AND post_date IS NOT NULL AND future_payment = 'Y' AND status in('Post Payment')",array(":id" => $order_id));

	if(empty($order_res)){
		$response['status']='not_found';
		$response['message']="Order Not Found";

		if(isset($_GET['lead_detail_page'])) {
			setNotifySuccess("Order Not Found");
		}
		echo json_encode($response);
		exit();
	}

	$mbrRes = $pdo->selectOne("SELECT id,rep_id,status FROM customer where id=:id",array(":id"=>$order_res['customer_id']));

	$subscriptionIds = checkIsset($order_res['subscription_ids']);
	if(!empty($subscriptionIds)){
		$wsSql = "SELECT id,eligibility_date,customer_id,product_id,plan_id,fee_applied_for_product,last_order_id 
				FROM website_subscriptions 
				WHERE id IN(".$subscriptionIds.") 
				ORDER BY eligibility_date ASC";
		$wsRes = $pdo->select($wsSql);
	}

	$updOdrParams = array(
		'status'=>'Cancelled',
		'future_payment'=>'N',
	  	'post_date' => NULL
	);
	$updOdrWhere = array(
		"clause"=>"id=:id",
	    "params"=>array(
	    	":id"=>$order_res['id'],
	    )
  	);
	$pdo->update("orders",$updOdrParams,$updOdrWhere);

	$lq_data = array(
		'status'=>'Disabled',
	);
	$lq_where = array(
		"clause"=>"order_ids=:order_ids",
	    "params"=>array(
	    	":order_ids"=>$order_res['id'],
	    )
  	);
	$pdo->update("lead_quote_details",$lq_data,$lq_where);

	$lead_description = '';
	if($order_res['is_renewal'] == "N") {
		if(!empty($mbrRes)){
			if(!in_array($mbrRes["status"],array("Active","Inactive","Pending"))){
				$mbrUpdParams = array(
					'status' => 'Pending Validation'
				);
				$mbrWhere = array("clause" => "id=:id","params" => array(":id" => $mbrRes['id']));
				$pdo->update("customer", $mbrUpdParams, $mbrWhere);

				//EL8-989 updates -> update lead status to Abandoned 
					$l_data = array(
						'status'=>'Abandoned',
					);
					$l_where = array(
						"clause"=>"customer_id=:customer_id",
					    "params"=>array(
					    	":customer_id"=>$mbrRes['id'],
					    )
				  	);
					$leadStatus = $pdo->update("leads",$l_data,$l_where,true);
					$lead = $pdo->selectOne("SELECT lead_id from leads where customer_id=:id",array(":id"=>$mbrRes['id']));
					$lead_description = !empty($leadStatus['status']) ? 'Lead '.checkIsset($lead["lead_id"]).' Staus updated from '.$leadStatus['status'].' to Abandoned' : '';
				//EL8-989 updates -> update lead status to Abandoned 
			}
		}

		foreach ($wsRes as $wsRow) {
			$wsUpdParams = array(
				'status'=>'Inactive',
				'termination_date'=>$wsRow['eligibility_date'],
				'term_date_set'=>'msqlfunc_NOW()',
				'next_attempt_at' => NULL,
				'total_attempts' => 0,
			);
			$wsUpdWhere = array(
				"clause"=>"id=:id",
			    "params"=>array(
			    	":id"=>$wsRow['id'],
			    )
		  	);
			$pdo->update("website_subscriptions",$wsUpdParams,$wsUpdWhere);

			$ws_history = array(
                'customer_id' => $wsRow['customer_id'],
                'website_id' => $wsRow['id'],
                'product_id' => $wsRow['product_id'],
                'plan_id' => $wsRow['plan_id'],
                'fee_applied_for_product' => $wsRow['fee_applied_for_product'],
                'order_id' => $wsRow['last_order_id'],
                'status' => 'Inactive', 
                'message' => 'Cancelled Order Post Date', 
                'admin_id' => (isset($_SESSION['admin']['id'])?$_SESSION['admin']['id']:''),
                'processed_at' => 'msqlfunc_NOW()'
            );
            $pdo->insert("website_subscriptions_history", $ws_history);

			$cdUpdParams = array(
				'status'=>'Inactive',
				'terminationDate'=>$wsRow['eligibility_date'],
			);
			$cdUpdWhere = array(
				"clause"=>"website_id=:website_id",
			    "params"=>array(
			    	":website_id"=>$wsRow['id'],
			    )
		  	);
			$pdo->update("customer_dependent",$cdUpdParams,$cdUpdWhere);
		}
	}

	$transInsId = array();
    $transParams = array("reason" => "Cancelled Order Post Date");
	$transInsId=$functionsList->transaction_insert($order_res['id'],'Debit','Cancelled','Transaction Cancelled',0,$transParams);

	$activityFeedDesc = array();
	$oldOdr = array("status" => $order_res['status']);
	$newOdr = array("status" => 'Cancelled');
	$odrCheckDiff=array_diff_assoc($oldOdr, $newOdr);
    if(!empty($odrCheckDiff)){ 
      	foreach ($odrCheckDiff as $key1 => $value1) {
        	$activityFeedDesc['key_value']['desc_arr'][$key1]='From '. (!empty($oldOdr[$key1]) ? $oldOdr[$key1] : 'blank').' To '.$newOdr[$key1] ." on Order ".checkIsset($order_res["display_id"]); 
      	}
    }
	
	if(!empty($activityFeedDesc)){
		$activityFeedDesc1 = array();

		if($order_res['is_renewal'] == "N") {
			$entity_id = $mbrRes['id'];
			$entity_type = 'customer';
			if(!empty($_POST['lead_id']) && !in_array($mbrRes["status"],array("Active","Inactive","Pending"))){
				$entity_id = $_POST['lead_id'];
				$entity_type = 'Lead';
			}
		} else {
			$entity_id = $mbrRes['id'];
			$entity_type = 'customer';
		}

		if($location == "admin") {
			$activityFeedDesc1['ac_message'] =array(
			    'ac_red_1'=>array(
			      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			      'title'=>$_SESSION['admin']['display_id'],
			    ),
		  	);
		  	$activityFeedDesc1['description_status'] = $lead_description;
		  	$activityFeedDesc1 = array_merge($activityFeedDesc1,$activityFeedDesc);
			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $entity_id, $entity_type,'Admin cancelled Order Post Date', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc1));
		
		} elseif($location == "agent") {
			$activityFeedDesc1['ac_message'] =array(
			    'ac_red_1'=>array(
			      	'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
			    ),
		  	);
		  	$activityFeedDesc1['description_status'] = $lead_description;
		  	$activityFeedDesc1 = array_merge($activityFeedDesc1,$activityFeedDesc);

			activity_feed(3, $_SESSION['agents']['id'], 'Agent', $entity_id, $entity_type,'Agent cancelled Order Post Date','','',json_encode($activityFeedDesc1));
		} elseif($location == "group") {
			$activityFeedDesc1['ac_message'] =array(
			    'ac_red_1'=>array(
			      	'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=> $_SESSION['groups']['rep_id'],
			    ),
		  	);
		  	$activityFeedDesc1['description_status'] = $lead_description;
		  	$activityFeedDesc1 = array_merge($activityFeedDesc1,$activityFeedDesc);

			activity_feed(3, $_SESSION['groups']['id'], 'Group', $entity_id, $entity_type,'Group cancelled Order Post Date','','',json_encode($activityFeedDesc1));
		}
	}
	$response['status'] = 'success';
	$response['message'] = "Post date cancelled successfully";

	if(isset($_GET['lead_detail_page'])) {
		setNotifySuccess("Post date cancelled successfully");
	}
}else{
	$response['status']='not_found';
	$response['message']="Order Not Found";
}
echo json_encode($response);
dbConnectionClose();
exit();