<?php 
	include_once __DIR__ . '/includes/connect.php'; 
	$result = array();	
	$id = $_POST['id'];

	$sqlListBill = "SELECT lb.id,lb.list_bill_no,lb.customer_id,lb.items_total,lb.grand_total,lb.due_amount,lb.amendment FROM list_bills lb WHERE md5(id)=:id";
	$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$id));

	$updateParams=array(
		'status'=> 'void',
		'cancel_date' => date('Y-m-d'),
	);
	$updateWhere=array(
		'clause'=>'md5(id)=:id',
		'params'=>array(
			":id"=>$id,
		)
	);
	$pdo->update("list_bills",$updateParams,$updateWhere);

	$ac_description['ac_message'] = array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' => "Void List Bill ".$resListBill['list_bill_no'],
    );

	activity_feed(3, $_SESSION['admin']['id'],'Admin',$resListBill['id'] , 'list_bills', 'Void List Bill', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($ac_description));
	
	$result['status'] = "success"; 
	$result['msg'] = "List Bill Void Successfully"; 
  
	header('Content-type: application/json');
	echo json_encode($result); 
	dbConnectionClose();
	exit;
?>