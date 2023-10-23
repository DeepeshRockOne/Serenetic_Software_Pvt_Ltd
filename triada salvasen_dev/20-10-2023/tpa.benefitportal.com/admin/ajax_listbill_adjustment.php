<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();

$group_id= !empty($_POST['group_id']) ? $_POST['group_id'] : '';
$list_bill_id= !empty($_POST['adjustment_list_bill_id']) ? $_POST['adjustment_list_bill_id'] : '';
$adjustment_type= !empty($_POST['adjustment_type']) ? $_POST['adjustment_type'] : '';
$adjustment_amount= !empty($_POST['adjustment_amount']) ? $_POST['adjustment_amount'] : '';
$adjustment_note= !empty($_POST['adjustment_note']) ? $_POST['adjustment_note'] : '';

$response = array();
$validate->string(array('required' => true, 'field' => 'group_id', 'value' => $group_id), array('required' => 'Please Select Group'));
$validate->string(array('required' => true, 'field' => 'adjustment_list_bill_id', 'value' => $list_bill_id), array('required' => 'Please Select List Bill'));
$validate->string(array('required' => true, 'field' => 'adjustment_type', 'value' => $adjustment_type), array('required' => 'Please Select Transaction Type'));
$validate->string(array('required' => true, 'field' => 'adjustment_amount', 'value' => $adjustment_amount), array('required' => 'Please Enter Amount'));

if ($validate->isValid()) {
	$adjustment_amount = str_replace(',','',$adjustment_amount);
	$amount = ($adjustment_type == 'Credit') ? $adjustment_amount : -1 * $adjustment_amount;

	$sqlListBill = "SELECT lb.id,lb.grand_total,lb.due_amount,lb.list_bill_no,lb.adjustment,c.rep_id
				FROM list_bills lb 
				JOIN customer c ON (c.id=lb.customer_id)
				where lb.customer_id=:group_id AND lb.status='open' AND lb.id=:id";
	$resListBill = $pdo->selectOne($sqlListBill,array(":group_id"=>$group_id,":id"=>$list_bill_id));

	if(!empty($resListBill)){
		
			$adjustment = $resListBill['adjustment'] + $amount;
			$grand_total = $resListBill['grand_total'] + $amount;
			$due_amount = $resListBill['due_amount'] + $amount;

			$list_bill_update_params = array(
	            "adjustment" => $adjustment,
	            "grand_total" => $grand_total,
	            "due_amount" => $due_amount,
	        );
	        if(!empty($adjustment_note)){
	        	$list_bill_update_params['notes'] = $adjustment_note;
	        }
	        
	        $list_bill_update_where = array(
	            'clause' => 'id = :id',
	            'params' => array(
	                ':id' =>$list_bill_id
	            )
	        );
	        $pdo->update('list_bills', $list_bill_update_params ,$list_bill_update_where);

	        $adjustmentParams = array(
	        	'group_id'=>$group_id,
	        	'list_bill_id'=>$list_bill_id,
	        	'adjustment'=>$amount,
	        	'adjustment_type'=>$adjustment_type,
	        	'adjustment_note'=>$adjustment_note,
	        );
	        $pdo->insert('list_bill_adjustments',$adjustmentParams);

	        $ac_description['ac_message'] = array(
	            'ac_red_1'=>array(
	              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	              'title'=>$_SESSION['admin']['display_id'],
	            ),
            	'ac_message_1' => " created Adjustment ",
            );
	            
            $ac_description['key_value']['desc_arr']['List Bill'] = $resListBill['list_bill_no'];
            $ac_description['key_value']['desc_arr']['Adjustment'] = displayAmount($amount,2);
            $ac_description['key_value']['desc_arr']['Adjustment Type'] = $adjustment_type;

            $ac_description['ac_message']['ac_red_2']=array(
				'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($group_id),
				'title'=>$resListBill['rep_id']
			); 
            
            $ac_description['ac_message']['ac_red_3']=array(
				'href'=>$ADMIN_HOST.'/view_listbill_statement.php?list_bill='.md5($list_bill_id),
				'title'=>$resListBill['list_bill_no']
			); 

			$ac_description['ac_description_link']=array(
				'Adjustment '=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'Note','data-desc'=>checkIsset($adjustment_note),'data-encode'=>'no'),
			);

		  	activity_feed(3,$_SESSION['admin']['id'], 'Admin', $group_id, 'Group', 'created Adjustment','','',json_encode($ac_description));
			$response['status'] = 'success';
		    setNotifySuccess('You have successfully applied adjustment');
		
	}
}else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}

header('Content-type: application/json');
echo json_encode($response);
dbConnectionClose(); 
exit;
?>
