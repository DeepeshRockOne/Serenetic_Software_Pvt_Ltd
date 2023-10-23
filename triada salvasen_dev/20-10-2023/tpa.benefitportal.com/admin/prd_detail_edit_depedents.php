<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$dependent_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
$name = "";
$display_id = "";
$coverage_periods = array();
$effective_date = "";
$termination_date = "";
$added_date = "";
$active_since_date = "";

$dependent = $pdo->selectOne("SELECT * FROM customer_dependent WHERE id=:id",array(':id' => $dependent_id));
$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE id=:website_id",array(':website_id' => $dependent['website_id']));

$name = $dependent['fname'] . " " . $dependent['lname'];
$display_id = $dependent['display_id'];
$effective_date = date('m/d/Y',strtotime($dependent['eligibility_date']));
$ws_eligibility = date('m/d/Y',strtotime($ws_row['eligibility_date']));
$termination_date = $dependent['terminationDate'];
$added_date = $dependent['hire_date'] ? date('m/d/Y',strtotime($dependent['hire_date'])) : date('m/d/Y',strtotime($dependent['created_at']));
$active_since_date = $dependent['active_since'] ? date('m/d/Y',strtotime($dependent['active_since'])) : $added_date;
$coverage_periods = get_dependent_term_date_selection_options($dependent['id'],$ws_row['id']);

if(!empty($_POST)){
	$effective_date = isset($_POST['effective_date']) ? $_POST['effective_date'] : ""; 
	$termination_date = isset($_POST['termination_date']) ? $_POST['termination_date'] : ""; 
	$active_date = isset($_POST['active_since_date']) ? $_POST['active_since_date'] : ""; 
	$added_date = isset($_POST['added_date']) ? $_POST['added_date'] : ""; 

	$validate = new Validation();
	$validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Please select effective date'));
	$validate->string(array('required' => true, 'field' => 'added_date', 'value' => $added_date), array('required' => 'Please select added date'));

	if ($validate->isValid()) {

		$updParam = array(
			'eligibility_date' => date('Y-m-d',strtotime($effective_date)),
			'updated_at' => 'msqlfunc_NOW()',
		);

		if(strtotime($termination_date) > 0){
			$updParam['terminationDate'] = date('Y-m-d',strtotime($termination_date));
			if(strtotime('now') > strtotime($updParam['terminationDate'])){
				$updParam['status'] = 'Termed';
			} else {
				$updParam['status'] = 'Active';
			}
		} else {
			$updParam['terminationDate'] = NULL;
			$updParam['status'] = 'Active';
		}

		if($active_date){
			$updParam['active_since'] = date('Y-m-d',strtotime($active_date));
		}

		if($added_date){
			$updParam['hire_date'] = date('Y-m-d',strtotime($added_date));
		}

		$updWhere=array(
			'clause'=>'id=:id',
			'params'=>array(":id"=>$dependent_id)
		);
		$pdo->update('customer_dependent',$updParam,$updWhere);

		/*--- Activity Feed ----*/
		$updated_text = '';
		if(strtotime($updParam['eligibility_date']) != strtotime($dependent['eligibility_date'])) {
			$updated_text .= '<br/> Effective date changed from : '.displayDate($dependent['eligibility_date']).' to : '.displayDate($updParam['eligibility_date']);
		}

		if(strtotime($updParam['terminationDate']) != strtotime($dependent['terminationDate'])) {
			if(strtotime($updParam['terminationDate']) > 0 && strtotime($dependent['terminationDate']) > 0) {
				$updated_text .= '<br/> Termination date changed from : '.displayDate($dependent['terminationDate']).' to : '.displayDate($updParam['terminationDate']);	

			} else if(strtotime($updParam['terminationDate']) > 0 && strtotime($dependent['terminationDate']) == 0) {
				$updated_text .= '<br/> Termination date set : '.displayDate($updParam['terminationDate']);	
			
			} else if(strtotime($updParam['terminationDate']) == 0 && strtotime($dependent['terminationDate']) > 0) {
				$updated_text .= '<br/> Termination date removed ';	
			}
			
		}

		if(strtotime($updParam['active_since']) != strtotime($dependent['active_since'])) {
			$updated_text .= '<br/> Active member since date changed from : '.displayDate($dependent['active_since']).' to : '.displayDate($updParam['active_since']);
		}

		if(strtotime($updParam['hire_date']) != strtotime($dependent['hire_date'])) {
			$updated_text .= '<br/> Added date changed from : '.displayDate($dependent['hire_date']).' to : '.displayDate($updParam['hire_date']);
		}

		if(!empty($updated_text)) {
			$customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
	        $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
	        
	        $af_message = 'updated plan dependent';
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
	            'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Dependent : '.$dependent['fname'].' '.$dependent['lname'].' ('.$dependent['display_id'].') '.$updated_text,
	        );
	        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
		}	
        /*--- Activity Feed ----*/

		$response['status'] = "success";
		setNotifySuccess("Dependent Updated Successfully");
	} else {
	  	$response['status'] = "fail";
	  	$response['errors'] = $validate->getErrors();
	}
	header('Content-Type: application/json');
	echo json_encode($response);
	exit;
}
$template = 'prd_detail_edit_depedents.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>