<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/trigger.class.php";
$validate = new Validation();
$TriggerMailSms = new TriggerMailSms();
$member_id = isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] : "";
$active_products = array();
if($member_id){
	$active_products = $pdo->select("SELECT IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,p.id as p_id,w.id as w_id,w.website_id,w.plan_id 
		FROM customer c
		JOIN website_subscriptions w on(w.customer_id = c.id)
		JOIN prd_main p on(p.id = w.product_id)
		WHERE md5(c.id) = :member_id and w.termination_date IS NULL GROUP BY p.id order by p.name",array(':member_id' => $member_id));
}

$customer_sql = "SELECT c.* FROM customer c WHERE md5(c.id)=:customer_id";
$customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $member_id));

if(isset($_POST['form_submit']) && $_POST['form_submit'] == 'Y'){
	$response = array();

	$term_date = isset($_POST['termination_date']) ? $_POST['termination_date'] : array();
	$checked_term_date = isset($_POST['chk']) ? $_POST['chk'] : array();
	$reason = $_POST['reason'] ? $_POST['reason'] : "";
	$cobra_options = isset($_POST['cobra_options']) ? $_POST['cobra_options'] : "";

	if(!$checked_term_date){
		$validate->setError('common',"Please select any product");
	}
	
	foreach ($checked_term_date as $key => $value) {
		$validate->string(array('required' => true, 'field' => 'product_' .$key , 'value' => $term_date[$key]), array('required' => 'Please select termination date'));
	}

	$validate->string(array('required' => true, 'field' => 'reason', 'value' => $reason), array('required' => 'Please select reason'));

	if ($validate->isValid()) {
		
		foreach($checked_term_date as $key => $value) {
			if(isset($term_date[$key])){

				$ws_row = $pdo->selectOne("SELECT id,customer_id,product_id,plan_id,prd_plan_type_id,termination_date,eligibility_date FROM website_subscriptions WHERE md5(customer_id) = :customer_id and plan_id = :plan_id",array(':customer_id' => $member_id,":plan_id" => $key));

				if(!empty($ws_row)){

					$req_where = array(
			            "clause"=>"customer_id=:customer_id AND product_id = :product_id AND plan_id = :plan_id",
			            "params"=>array(
			              ":customer_id"=>$ws_row['customer_id'],
			              ":product_id"=>$ws_row['product_id'],
			              ":plan_id"=>$ws_row['plan_id'],
			                )
			            );

	            	$req_data = array(
		                'termination_date' => $term_date[$key],
		                'status' => 'Inactive Member Request',
		                'term_date_set' => 'msqlfunc_NOW()',
		                'termination_reason' => $reason
		            );	
		            
		            if(strtotime('now') < strtotime($term_date[$key])){
		            	$req_data['status'] = 'Active';
		            }

		            $pdo->update("website_subscriptions",$req_data,$req_where);

		            $dependents = $pdo->select("SELECT id FROM customer_dependent WHERE customer_id = :customer_id AND product_id = :product_id AND product_plan_id = :product_plan_id",array(':customer_id' => $ws_row['customer_id'],':product_id' => $ws_row['product_id'],':product_plan_id' => $ws_row['plan_id']));

		            if($dependents){
		            	foreach ($dependents as $dependent) {
		            		$dep_where = array(
					            "clause"=>"id=:id",
					            "params"=>array(
					              ":id"=>$dependent['id']
					                )
					            );

			            	$dep_data = array(
				                'terminationDate' => $term_date[$key],
				                'status' => 'Termed',
				                'updated_at' => 'msqlfunc_NOW()',
				            );

				            $pdo->update("customer_dependent",$dep_data,$dep_where);	
		            	}
		            }

		            $insert_params = array(

		            	'customer_id' => $ws_row['customer_id'],
		            	'website_id' => $ws_row['id'],
		            	'product_id' => $ws_row['product_id'],
		            	'plan_id' => $ws_row['plan_id'],
		            	'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
		            	'status' => 'Termination',
		            	'message' => $reason,

		            );

		            $pdo->insert('website_subscriptions_history',$insert_params);

		            if((strtotime(date('Y-m-d', strtotime($term_date[$key]))) <= strtotime(date('Y-m-d'))) || strtotime(date("Y-m-d",strtotime($term_date[$key]))) == strtotime($ws_row['eligibility_date'])){
		            	if(empty($ws_row['termination_date'])){
		            		$products = array($ws_row["product_id"]=>date("Y-m-d"));
                        	$TriggerMailSms->trigger_action_mail('member_cancellation',$ws_row['customer_id'],'member','addedTerminationDate',$products);
		            	}
		            }else if(empty($ws_row['termination_date'])){
		            	$products = array($ws_row["product_id"]=>date("Y-m-d",strtotime($term_date[$key])));
                        $TriggerMailSms->trigger_action_mail('member_cancellation',$ws_row['customer_id'],'member','addedTerminationDate',$products);
		            }

		            $af_message = 'set termination date';

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
                        'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Termination date : '.displayDate($term_date[$key]).' <br/>Termination Reason : '. $reason,
                    );
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

		            /*$activityFeedDesc=array();

		            $activityFeedDesc['ac_message'] =array(
		              'ac_red_1'=>array(
		                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		                'title'=>$_SESSION['admin']['display_id']),
		              'ac_message_1' =>' set termination date ',
		              'ac_red_2'=>array(
		                'href'=>'',
		                'title'=>"",
		              ),
		            );

		            $activityFeedDesc['key_value']['desc_arr']['Product Name'] = getname('prd_main',$ws_row['product_id'],'name','id');
		            $activityFeedDesc['key_value']['desc_arr']['Termination Date'] = date('m/d/Y',strtotime($term_date[$key]));
		            $activityFeedDesc['key_value']['desc_arr']['Reason'] = $reason;

		            if(!empty($activityFeedDesc) && !empty($activityFeedDesc['key_value']['desc_arr'])){ 
		                activity_feed(3, $ws_row['customer_id'], 'Customer', $ws_row['customer_id'], 'customer','Admin set termination date', getname('customer',$ws_row['customer_id'],'fname','id'),getname('customer',$ws_row['customer_id'],'lname','id'),json_encode($activityFeedDesc));
		            }*/

		        }

		        $response['status'] = 'success';
		        $response['message'] = 'Term date set successfully';
		        $response['cobra_options'] = $cobra_options;
		        setNotifySuccess("Term date set successfully");

			}
		}
	}else{
		$response['status'] = "fail";
  		$response['errors'] = $validate->getErrors();
	}

	echo json_encode($response);
	exit();
}

$reasons = get_policy_termination_reasons();

$customer_id = $customer_row['id'];
$sponsor_id = $customer_row['sponsor_id'];
$sponsor_type = getname('customer',$sponsor_id,'type','id');
$allow_cobra_benefit = 'N';
if($sponsor_type == 'Group'){
    $check_cobra_benefits = $pdo->selectOne("SELECT group_use_cobra_benefit FROM group_cobra_benefits WHERE is_deleted = 'N'");
    if($check_cobra_benefits && $check_cobra_benefits['group_use_cobra_benefit'] == 'Y'){
        $allow_cobra_benefit = 'Y';
    }
}

$template = 'term_multiple_products.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>