<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
$ListBill = new ListBill();

$validate = new Validation();

$group_id= !empty($_POST['regenerate_group_id']) ? $_POST['regenerate_group_id'] : '';
$list_bill_id= !empty($_POST['list_bill_id']) ? $_POST['list_bill_id'] : '';

$response = array();
$validate->string(array('required' => true, 'field' => 'regenerate_group_id', 'value' => $group_id), array('required' => 'Please Select Group'));
$validate->string(array('required' => true, 'field' => 'list_bill_id', 'value' => $list_bill_id), array('required' => 'Please Select List Bill'));

if ($validate->isValid()) {
	$sqlListBill = "SELECT lb.id,lb.list_bill_no,lb.company_id,lb.status,c.rep_id,lb.list_bill_date
					FROM list_bills lb 
					JOIN customer c ON (c.id=lb.customer_id)
					WHERE lb.id=:id";
	$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$list_bill_id));

	if(!empty($resListBill)){
		$extra = array();
  		$extra['type'] = 'manual';
  		$extra['today'] = $resListBill['list_bill_date'];
		$regenerate_company_id = $resListBill['company_id'];
		$list_bill_id_arr = $ListBill->generateListBill(true,$group_id,$list_bill_id,$regenerate_company_id,$extra);

		$response['list_bill_id_arr']=$list_bill_id_arr;

		if(!empty($list_bill_id_arr)){
			foreach ($list_bill_id_arr as $key => $value) {
				$sqlListBillNew = "SELECT id,list_bill_no,company_id,status FROM list_bills WHERE id=:id";
				$resListBillNew = $pdo->selectOne($sqlListBillNew,array(":id"=>$value));

				$ac_description['ac_message'] = array(
			        'ac_red_1'=>array(
			          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			          'title'=>$_SESSION['admin']['display_id'],
			        ),
			    	'ac_message_1' => "regenerated new list bill for",
			    );
			        
			    $ac_description['key_value']['desc_arr']['List Bill'] ="From ".$resListBill['list_bill_no'] ." To ".$resListBillNew['list_bill_no'];
			    
			    $ac_description['ac_message']['ac_red_2']=array(
					'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($group_id),
					'title'=>$resListBill['rep_id']
				); 
				
			  	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $group_id, 'Group', 'List Bill Regenerated','','',json_encode($ac_description));
			}
			
		  	setNotifySuccess('You Have Successfully Regenerate List Bill');
		}
	}
	
	$response['status'] = 'success';
    
		
	
}else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}

header('Content-type: application/json');
echo json_encode($response); 
dbConnectionClose();
exit;
?>
