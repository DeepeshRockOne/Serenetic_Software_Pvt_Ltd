<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
require_once __DIR__ . '/includes/member_setting.class.php';
$functionsList = new functionsList();
$memberSetting = new memberSetting();
$odrRes = array();
$validate = new Validation();
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$res = array();
$orderId = $_REQUEST['orderId'];

if(!empty($orderId)){
$odrSql = "SELECT id,customer_id,subscription_ids,display_id,post_date,status,transaction_id,is_reinstate_order,is_renewal FROM orders WHERE md5(id)=:id AND future_payment='Y' AND status='Post Payment'";
$odrRes = $pdo->selectOne($odrSql, array(":id" => $orderId));
}

if(empty($odrRes['subscription_ids'])) {
	setNotifyError("Post Payment Order not found");
	echo "<script>parent.window.location.reload();</script>";
	exit();
}

$odrId = !empty($odrRes["id"]) ? $odrRes["id"] : 0;
$odrDispId = checkIsset($odrRes["display_id"]);
$postDate = !empty($odrRes['post_date']) ? date("m/d/Y",strtotime($odrRes['post_date'])) : "";

$subscriptionIds = checkIsset($odrRes['subscription_ids']);

$odSql = "SELECT id,start_coverage_period 
			FROM order_details 
			WHERE order_id=:order_id AND is_deleted='N'
			ORDER BY start_coverage_period ASC";
$odRes = $pdo->selectOne($odSql,array(":order_id" => $odrId));

$start_coverage_period = checkIsset($odRes['start_coverage_period']);

$today = date("Y-m-d");
$endDate = date("m/d/Y",strtotime("- 1 days",strtotime($start_coverage_period)));

if(isset($_GET["post_date"]) && !empty($odrRes)){
	$postDate = $_REQUEST['post_date'];

	/*---------- Post Date Validation ---------*/
	$validate->string(array('required' => true, 'field' => 'post_date', 'value' => $postDate), array('required' => 'Select Post date'));
	if (empty($validate->getError('post_date'))) {
		if (strtotime($postDate) >= strtotime($start_coverage_period)) {
			$validate->setError('post_date', 'Post date must be less than' . date('m/d/Y', strtotime($start_coverage_period)));
		}
		if (strtotime($postDate) < strtotime(date("Y-m-d"))) {
			$validate->setError('post_date', 'Post date must future date');
		}
	}
	/*---------- Post Date Validation ---------*/
	
	if ($validate->isValid()) {
		$activityFeedDesc =  array();
		if($location == "admin") {
            $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' changed post date on '
            );

        } elseif($location == "agent") {
            $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' =>' changed post date on '
            );
        } elseif($location == "group") {
            $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=> $_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' =>' changed post date on '
            );
        }
		$selOldOdr = "SELECT status,future_payment,post_date FROM orders WHERE md5(id)=:odrId";
		$parmasOdr = array(":odrId"=>$orderId);
		$resOldOdr = $pdo->selectOne($selOldOdr,$parmasOdr);
		
		$updOdrParams = array(
			'status'=>'Post Payment',
			'future_payment'=>'Y',
			'total_attempts'=>0,
		  	'post_date' => date("Y-m-d",strtotime($postDate))
		);
		$updOdrWhere = array(
			"clause"=>"md5(id)=:id",
		    "params"=>array(
		    	":id"=>$orderId,
		    )
	  	);
		$pdo->update("orders",$updOdrParams,$updOdrWhere);

		$odrCheckDiff=array_diff_assoc($resOldOdr, $updOdrParams);
	    if(!empty($odrCheckDiff)){ 
	      foreach ($odrCheckDiff as $key1 => $value1) {
	        $activityFeedDesc['key_value']['desc_arr'][$key1]='From '. (!empty($resOldOdr[$key1]) ? $resOldOdr[$key1] : 'blank').' To '.$updOdrParams[$key1] ." on Order ".$odrDispId; 
	      }
	    }

	    $mbrRes = array();
		$mbrSel = "SELECT id,status,rep_id FROM customer WHERE id=:custId";
		$mbrParams = array(":custId" => $odrRes['customer_id']);
		$mbrRes = $pdo->selectOne($mbrSel,$mbrParams);

		$member_setting = $memberSetting->get_status_by_payment('','',true,$mbrRes["status"]);

		if($odrRes['status'] == 'Payment Declined'){
			$other_params=array("transaction_id"=>$odrRes['transaction_id']);
			$transId = $functionsList->transaction_insert($odrId,'Credit','Post Payment','Post Transaction',0,$other_params);
		}

		$wsSql = "SELECT id,website_id,customer_id,product_id,plan_id,fee_applied_for_product,next_attempt_at,status FROM website_subscriptions WHERE id IN(".$subscriptionIds.") ORDER BY eligibility_date ASC";
		$wsRes = $pdo->select($wsSql);
		
		if(!empty($wsRes)){
			foreach ($wsRes as $key => $wsRow) {
				$wsUpdParams = array(
					'next_attempt_at' => NULL,
					'total_attempts' => 0
				);
				$wsUpdWhere = array(
					"clause"=>"id=:id",
				    "params"=>array(
				    	":id"=>$wsRow['id'],
				    )
			  	);
				$pdo->update("website_subscriptions",$wsUpdParams,$wsUpdWhere);
			}
		}

		if(!empty($activityFeedDesc)) {
			if($location == "admin") {
				activity_feed(3, $_SESSION['admin']['id'], 'Admin', $mbrRes['id'], 'customer','Admin updated Order Post Date', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
			
			} elseif($location == "agent") {
				activity_feed(3, $_SESSION['agents']['id'], 'Agent', $mbrRes['id'], 'customer','Agent updated Order Post Date','','',json_encode($activityFeedDesc));
			} elseif($location == "group") {
				activity_feed(3, $_SESSION['groups']['id'], 'Group', $mbrRes['id'], 'customer','Group updated Order Post Date','','',json_encode($activityFeedDesc));
			}
		}
		$res["msg"] = "Post Date Changed successfully";
		$res["status"] = "success";

		if(strtotime(date('Y-m-d',strtotime($postDate))) <= strtotime(date('Y-m-d'))) {
			$test = $functionsList->generatePostOrder($odrId,$odrRes['is_reinstate_order']);
			if(!empty($test)){
				$res["msg"] = "Post Date Order Attempted";
				$res["status"] = "success_attempt";
				setNotifySuccess($res['msg']);
			}
		}		
	} else {
		$errors = $validate->getErrors();
	    $res['status'] = 'fail';
	    $res['errors'] = $errors;
	}
	echo json_encode($res);
	exit;
}
$template = 'edit_order_post_date.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>