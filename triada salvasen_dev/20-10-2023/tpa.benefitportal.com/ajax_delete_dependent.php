<?php
include_once __DIR__ . '/includes/connect.php';

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$id = isset($_POST['id']) ? $_POST['id'] : "";
$response = array();
if($id){
	$dependent = $pdo->selectOne("SELECT * from customer_dependent where id = :id and is_deleted = 'N'",array(':id' => $id));

	if($dependent){

		$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where md5(id)=:id",array(':id' => md5($dependent['website_id'])));

		if($ws_row){

			if($ws_row['prd_plan_type_id'] == 2){
				$existing_dependent = $pdo->selectOne("SELECT * from customer_dependent where id != :id and relation in('Son','Daughter') and website_id=:website_id and is_deleted = 'N'",array(':id' => $id,':website_id' => $dependent['website_id']));
			}else if($ws_row['prd_plan_type_id'] == 3){
				$existing_dependent = $pdo->selectOne("SELECT * from customer_dependent where id != :id and relation in('Wife','Husband') and website_id=:website_id and is_deleted = 'N'",array(':id' => $id,':website_id' => $dependent['website_id']));	
			}else if($ws_row['prd_plan_type_id'] == 4){
				$existing_dependent = $pdo->select("SELECT id,relation from customer_dependent where id != :id and website_id=:website_id and is_deleted = 'N'",array(':id' => $id,':website_id' => $dependent['website_id']));	
				if(!empty($existing_dependent)){
					//task - CRM-620 update
					$prdInfo = $pdo->selectOne("SELECT family_plan_rule from prd_main where id=:id",array(":id" =>$ws_row['product_id']));
					if(!empty($prdInfo)){
						$spouseDep = 0;
						$chilDep = 0;
						$message = '';
						foreach($existing_dependent as $dep){
							if(in_array(strtolower($dep['relation']),array('wife','husband'))){
								$spouseDep++;
							}else if(in_array(strtolower($dep['relation']),array('son','daughter'))){
								$chilDep++;
							}
						}
						$family_plan_rule =  $prdInfo['family_plan_rule'];
						if($family_plan_rule=="Spouse And Child"){
							if($chilDep == 0 || $spouseDep == 0){
								$existing_dependent = array();
								$message = 'Family plan requires one spouse and child';
							}
						}else if($family_plan_rule=="Minimum One Dependent"){
							if($chilDep == 0 && $spouseDep == 0){
								$existing_dependent = array();
								$message = 'Family plan requires minimum of one dependent';
							}
	
						}else if($family_plan_rule=="Minimum Two Dependent"){
							if(($chilDep + $spouseDep) < 2){
								$existing_dependent = array();
								$message = 'Family plan requires minimum of two dependents';
							}
						}
					}else {
						$spouseDep = $pdo->selectOne("SELECT * from customer_dependent where id = :id and website_id=:website_id and relation in('Wife','Husband') and is_deleted = 'N'",array(':id' => $id,':website_id' => $dependent['website_id']));
						if(!empty($spouseDep)){
							$existing_dependent = array();
						}
					}
				}
			}else if($ws_row['prd_plan_type_id'] == 5){
				$existing_dependent = $pdo->selectOne("SELECT * from customer_dependent where id != :id and website_id=:website_id and is_deleted = 'N'",array(':id' => $id,':website_id' => $dependent['website_id']));	
			}

			if($existing_dependent){

				$updParam = array('is_deleted'=>'Y');
				$updWhere = array(
				'clause' => 'id = :id',
				'params' => array(':id' => $id)
				);
				$pdo->update('customer_dependent', $updParam, $updWhere);

				/*--- Activity Feed ----*/
				$customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
	            $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
	            
	            $af_message = 'removed dependent from plan';
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
		                'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Dependent : '.$dependent['fname'].' '.$dependent['lname'].' ('.$dependent['display_id'].')',
		            );
		            activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
	            } elseif($location == "agent") {
	            	$af_desc = array();
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
		                'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Dependent : '.$dependent['fname'].' '.$dependent['lname'].' ('.$dependent['display_id'].')',
		            );
		            activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));	
	            } elseif($location == "group") {
	            	$af_desc = array();
		            $af_desc['ac_message'] =array(
		                'ac_red_1'=>array(
		                    'href' => $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
	            			'title' => $_SESSION['agents']['rep_id'],
		                ),
		                'ac_message_1' => $af_message.' on ',
		                'ac_red_2'=>array(
		                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
		                    'title'=>$customer_row['rep_id'],
		                ),
		                'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Dependent : '.$dependent['fname'].' '.$dependent['lname'].' ('.$dependent['display_id'].')',
		            );
		            activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));	
	            }
	            /*--- Activity Feed ----*/

				$response['status'] = 'success';
				$response['allow'] = 'Y';
				$response['message'] = 'Dependent deleted';
				$response['id'] = $id;

			}else{
				$response['status'] = 'fail';
				$response['allow'] = 'N';
				$response['message'] = !empty($message) ? $message :  'You must need to change benefit tier to delete dependent';
			}

		}

	}
	
}else{
	$response['status'] = 'fail';
    $response['allow'] = 'N';
    $response['message'] = 'not_found';
}
echo json_encode($response);
dbConnectionClose();
exit();

?>