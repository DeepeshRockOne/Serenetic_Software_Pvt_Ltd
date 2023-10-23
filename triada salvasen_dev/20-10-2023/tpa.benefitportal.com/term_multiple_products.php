<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$validate = new Validation();
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$member_id = isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] : "";
$active_products = array();
if($member_id){
	$active_products = $pdo->select("SELECT IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,p.id as p_id,w.id as ws_id,w.website_id,w.plan_id 
		FROM website_subscriptions w
		JOIN prd_main p on(p.id = w.product_id)
		WHERE md5(w.customer_id)=:member_id and (w.termination_date IS NULL OR w.termination_date > NOW()) 
		GROUP BY w.id 
		ORDER BY p.name",array(':member_id' => $member_id));
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
				$extra_params = array();
	            $extra_params['location'] = "member_detail";
	            $extra_params['portal'] = $location;
	            $policySetting->setTerminationDate($key,$term_date[$key],$reason,$extra_params);
			}
		}
		$response['status'] = 'success';
        $response['message'] = 'Term date set successfully';
        $response['cobra_options'] = $cobra_options;
        setNotifySuccess("Term date set successfully");
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