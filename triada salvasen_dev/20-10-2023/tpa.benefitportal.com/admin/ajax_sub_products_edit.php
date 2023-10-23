<?php
include_once 'layout/start.inc.php';
$res = array();
$validate = new Validation();

$sub_id = checkIsset($_POST['sub_id']); 
$carrier_id = checkIsset($_POST['carrier_id']);
$product_code = checkIsset($_POST['product_code']);
$product_name = checkIsset($_POST['product_name']);
$status = checkIsset($_POST['status']);


$validate->string(array('required' => true, 'field' => 'carrier_id', 'value' => $carrier_id), array('required' => 'Please select carrier'));
$validate->string(array('required' => true, 'field' => 'product_code', 'value' => $product_code), array('required' => 'Product Code is required'));
$validate->string(array('required' => true, 'field' => 'product_name', 'value' => $product_name), array('required' => 'Product Name is required'));
$validate->string(array('required' => true, 'field' => 'status', 'value' => $status), array('required' => 'Please select status'));

if ($validate->isValid()) {

	$checksql="SELECT id,carrier_id,product_code,product_name,status FROM sub_products WHERE is_deleted='N' AND md5(id)=:id";
	$checkres=$pdo->selectOne($checksql,array(":id"=>$sub_id));

	$update_params = array(
      	'carrier_id' => makeSafe($carrier_id),
        'product_code' => makeSafe($product_code),
        'product_name' => makeSafe($product_name),
        'status' => makeSafe($status),
  	);

	if(!empty($checkres)){
      	$update_where = array(
          	'clause' => 'id = :id',
          	'params' => array(
              ':id' => makeSafe($checkres['id'])
          	)
      	);

      	//************* Activity Code Start *************
			$oldVaArray = $checkres;
			$NewVaArray = $update_params;
			unset($oldVaArray['id']);

			$checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
			 
			if(!empty($checkDiff)){
				$activityFeedDesc['ac_message'] =array(
					'ac_red_1'=>array(
						'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
						'title'=>$_SESSION['admin']['display_id']),
					'ac_message_1' =>' Updated Sub Product ',
					'ac_red_2'=>array(
						//'href'=> '',
						'title'=>$product_code,
					),
				); 
				
				if(!empty($checkDiff)){
					foreach ($checkDiff as $key1 => $value1) {
						if($key1=='carrier_id'){
							$sql = "SELECT id,name,display_id FROM prd_fees WHERE is_deleted='N' AND id=:f_id ";
							$rows_data = $pdo->selectOne($sql,array(':f_id'=>$oldVaArray[$key1]));
 							$oldVaArray[$key1] = $rows_data['name'].' ('.$rows_data['display_id'].')';

							$sql = "SELECT id,name,display_id FROM prd_fees WHERE is_deleted='N' AND id=:f_id ";
							$rows_data = $pdo->selectOne($sql,array(':f_id'=>$NewVaArray[$key1]));
							$NewVaArray[$key1] = $rows_data['name'].' ('.$rows_data['display_id'].')';
						}
						$activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
					} 
				}
				
				activity_feed(3, $_SESSION['admin']['id'], 'Admin', $checkres['id'], 'sub_product','Admin Updated Sub Product', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
			}
		//************* Activity Code End   *************

		$pdo->update("sub_products", $update_params, $update_where);
		$res['msg']='Sub Product Updated Successfully';
	}else{
		 
	    $sub_id = $pdo->insert("sub_products", $update_params);
	    $res['msg']='Sub Product Added Successfully';

	    $description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Created Sub Product ',
			'ac_red_2'=>array(
				//'href'=> '',
				'title'=>$product_code,
			),
		); 
		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $sub_id, 'sub_product','Admin Created Sub Product', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}

	$res["status"] = "success";
}else{
	$errors = $validate->getErrors();
	$res["errors"] = $errors;
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>