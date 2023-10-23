<?php
include_once __DIR__ . '/includes/connect.php';

$response = array();

$bankname = checkIsset($_POST["bankname"]);
$bank_rounting_number = checkIsset($_POST['bank_rounting_number']);
$bank_account_number = checkIsset($_POST['bank_account_number']);
$bank_number_confirm = checkIsset($_POST['bank_number_confirm']);		
$bnk_account_type = checkIsset($_POST['bank_account_type']);
$effective_date = checkIsset($_POST['effective_date']);
$agent_id = $_POST['agent_id'];

$validate = new Validation();
$todayDate = date('m/d/Y');
$validate->string(array('required' => true, 'field' => 'bank_account_type', 'value' => $bnk_account_type), array('required' => 'Please Select Account Type'));

$validate->string(array('required' => true, 'field' => 'new_bank_name', 'value' => $bankname), array('required' => 'Bank name is required'));

$validate->digit(array('required' => true, 'field' => 'new_routing_number', 'value' => $bank_rounting_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
if (!$validate->getError("new_routing_number")) {
    if (checkRoutingNumber($bank_rounting_number) == false) {
        $validate->setError("new_routing_number", "Enter valid routing number");
    }
}

$validate->digit(array('required' => true, 'field' => 'new_account_number', 'value' => $bank_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));

$validate->digit(array('required' => true, 'field' => 'confirm_account_number', 'value' => $bank_number_confirm,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Confirm Account number is required', 'invalid' => "Enter valid Account number"));

if (!$validate->getError('confirm_account_number')) {
    if ($bank_number_confirm != $bank_account_number) {
        $validate->setError('confirm_account_number', "Enter same Account Number");
    }
}

$validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective Date is required'));
if ($effective_date != "") {
	if (validateDate($effective_date,'m/d/Y')) {
		if (strtotime($effective_date) < strtotime($todayDate)) {
			$validate->setError("effective_date", "Please Add Future Effective Date is required.");
		}
		$sel = "SELECT MAX(effective_date) as dates from direct_deposit_account WHERE customer_id=:customer_id";
		$whr = array(":customer_id" => $agent_id);
		$res = $pdo->selectOne($sel, $whr);
		if(!empty($res)){
			if(date('Y-m-d',strtotime($effective_date)) <= date($res['dates'])){
				$validate->setError("effective_date", "Please Add Future Effective Date is greater then ".date('m/d/Y',strtotime($res['dates'])));
			}
		}
	} else {
		$validate->setError("effective_date", "Valid Effective Date is required");
	}
}

if($validate->isValid())
{
	$selDirect = "SELECT id from direct_deposit_account WHERE customer_id=:customer_id order by id desc";
	$whrDirect = array(":customer_id" => $agent_id);
	$resDirect = $pdo->selectOne($selDirect, $whrDirect);

	$termination_date = '';
	$status = date('Y-m-d',strtotime($effective_date)) == date('Y-m-d') ? 'Active' : 'Inactive';

	$agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where id=:id",array(":id"=>$agent_id));
	$description = array();
	$description['ac_message'] = array(
	'ac_red_1'=>array(
		'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		'title'=>$_SESSION['admin']['display_id'],
	),
	'ac_message_1' =>'Added Direct Deposite Account In Agent'.$agent_name['name'].' (',
	'ac_red_2'=>array(
		'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
		'title'=> $agent_name['rep_id'],
	),
	'ac_message_2' =>')<br>',
	);
	if (!empty($resDirect)) {
		$termination_date =  date('Y-m-d', strtotime('-1 day', strtotime($effective_date)));
		$updateparams = array(
			'termination_date' => $termination_date,
			'updated_at' => 'msqlfunc_NOW()',
		);
		if($status=='Active'){
			$updateparams['status'] = 'Inactive';
		}
		$upd_where = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $resDirect['id'],
			),
		);
		$updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
		$update_data = $pdo->update('direct_deposit_account', $updateparams, $upd_where,true);

		$description['description']['termination_date'] ='Termination date updated To '.date('m/d/Y',strtotime($termination_date));
		if(!empty($update_data['status'])){
			$description['description']['status'] = 'Status updated from '.$update_data['status'].' To '.$status;
		}
	}

	$status = date('Y-m-d',strtotime($effective_date)) == date('Y-m-d') ? 'Active' : 'Inactive';
	$insparams = array(
		'customer_id' => $agent_id,
		'bank_name' => $bankname,
		'account_type' => $bnk_account_type,
		'routing_number' => $bank_rounting_number,
		'account_number' => $bank_account_number,
		'effective_date' => date('Y-m-d',strtotime($effective_date)),
		'status'		=> $status,
		'created_at' => 'msqlfunc_NOW()',
		'updated_at' => 'msqlfunc_NOW()',
	);
	$insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
	$pdo->insert('direct_deposit_account', $insparams);	

	$description['description']['new_account'] = 'New direct deposite account Added!';
	$desc = json_encode($description);
	activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $agent_name['id'], 'Agent', 'Direct Deposite Account',$_SESSION['admin']['name'],"",$desc);
	// setNotifySuccess("New direct deposite account Added!");
	$response['status'] = 'success';
}

if (count($validate->getErrors()) > 0) {
	$response['errors'] = $validate->getErrors();
}

header("Content-type:application/json");
echo json_encode($response);
dbConnectionClose();
exit;
?>