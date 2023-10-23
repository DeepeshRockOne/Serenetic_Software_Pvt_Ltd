<?php
include_once 'includes/connect.php';
include_once __DIR__ . '/../includes/function.class.php';

$response = array();
$validate = new Validation();
$activity_update = false;
$payment_master_id = $_POST['payment_master_id'];
$assinged_to_agent = !empty($_POST['assinged_to_agent']) ? $_POST['assinged_to_agent'] : '';
$agents = !empty($_POST['agents']) ? $_POST['agents'] : array();
$agents_downline = !empty($_POST['agents_downline']) ? $_POST['agents_downline'] : array();
$agents_loa = !empty($_POST['agents_loa']) ? $_POST['agents_loa'] : array();
$assinged_to_product = !empty($_POST['assinged_to_product']) ? $_POST['assinged_to_product'] : '';
$products = !empty($_POST['products']) ? $_POST['products'] : array();
$products_variation = !empty($_POST['products_variation']) ? $_POST['products_variation'] : array();
$validate->string(array('required' => true, 'field' => 'assinged_to_agent', 'value' => $assinged_to_agent), array('required' => 'Please select one option'));
if(!empty($assinged_to_agent) && $assinged_to_agent == 'selected' && empty($agents)){
	$validate->setError('agents','Please select one option');
}

$response['status'] = 'fail';

if ($validate->isValid()) {
	if(!empty($payment_master_id)){
		$merchant_res = $pdo->selectOne("SELECT * FROM payment_master WHERE is_deleted = 'N' AND id = :id",array(":id" => $payment_master_id));

		$insert_param = array('updated_at' => 'msqlfunc_NOW()');
		
		if($assinged_to_agent == 'all') {
			$insert_param['is_assigned_to_all_agent'] = 'Y';
			$insert_param['assigned_agent'] = NULL;
			$insert_param['agents_downline'] = NULL;
		} else {
			$insert_param['is_assigned_to_all_agent'] = 'N';
			$agents_downline_arr = array();
			if(!empty($agents_downline) && count($agents_downline) > 0){
				foreach ($agents_downline as $key => $value) {
					if(!in_array($key, $agents_downline_arr)){
						array_push($agents_downline_arr, $key);
						$downline_res = $pdo->select("SELECT id FROM customer WHERE upline_sponsors LIKE :upline_sponsors AND is_deleted = 'N' AND type = 'Agent'", array(':upline_sponsors' => "%," . $key . ",%"));
						if(count($downline_res) > 0){
							foreach ($downline_res as $index => $ele) {
								if(!in_array($ele['id'], $agents)){
									array_push($agents, $ele['id']);
								}
							}
						}
					}
				}
				if(!empty($agents_downline_arr) && count($agents_downline_arr) > 0){
					$insert_param['agents_downline'] = implode(",", $agents_downline_arr);
				}
			} else {
				$insert_param['agents_downline'] = NULL;
			}

			$agents_loa_arr = array();
			if(!empty($agents_loa) && count($agents_loa) > 0){
				foreach ($agents_loa as $key => $value) {
					if(!in_array($key, $agents_loa_arr)){
						array_push($agents_loa_arr, $key);
						$loa_res = $pdo->select("SELECT c.id FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE upline_sponsors LIKE :upline_sponsors AND agent_coded_level=:type AND is_deleted = 'N' AND type = 'Agent'", array(':upline_sponsors' => "%," . $key . ",%",':type'=>'LOA'));
						if(count($loa_res) > 0){
							foreach ($loa_res as $index => $ele) {
								if(!in_array($ele['id'], $agents)){
									array_push($agents, $ele['id']);
								}
							}
						}
					}
				}
				if(!empty($agents_loa_arr) && count($agents_loa_arr) > 0){
					$insert_param['agents_loa'] = implode(",", $agents_loa_arr);
				}
			} else {
				$insert_param['agents_loa'] = NULL;
			}

			if(!empty($agents) && count($agents) > 0){
				$insert_param['assigned_agent'] = implode(",", $agents);
			}
		}
		
		if($assinged_to_product == 'all') {
			$insert_param['is_assigned_to_all_product'] = 'Y';
			$insert_param['assigned_product'] = NULL;
			$insert_param['products_variation'] = NULL;
		} else {
			$insert_param['is_assigned_to_all_product'] = 'N';
			if(!empty($products) && count($products) > 0){
				$insert_param['assigned_product'] = implode(",", $products);
			}
			$product_variation_arr = array();
			if(!empty($products_variation) && count($products_variation) > 0){
				foreach ($products_variation as $key => $value) {
					if(!in_array($key, $product_variation_arr)){
						array_push($product_variation_arr, $key);
					}
				}
				if(!empty($product_variation_arr) && count($product_variation_arr) > 0){
					$insert_param['products_variation'] = implode(",", $product_variation_arr);
				}
			}
		}

		$upd_where = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $payment_master_id,
			),
		);
		$payment_master_update = $pdo->update('payment_master', $insert_param, $upd_where,true);
		if(!empty($payment_master_update)){

			$activity_description['ac_message'] =array(
				'ac_red_1'=>array(
				  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				  'title'=>$_SESSION['admin']['display_id'],
				),
				'ac_message_1' =>'  Updated Merchant Processor ',
				'ac_red_2'=>array(
				  'href'=> $ADMIN_HOST.'/add_merchant_processor.php?type='.$merchant_res['type'].'&id='.md5($payment_master_id),
				  'title'=> $merchant_res['name'],
				)
			  );
			foreach($payment_master_update as $key => $value){
				if(!empty($value) && !in_array($key,array('live_details','gateway_id','processor_id','order_by'))){
					$agent_value = $product_value = '';
					if(in_array($key,array('assigned_agent','agents_downline','agents_loa')) && !empty($value) && $value!=''){
							$value1 = '';
							if(!empty($insert_param[$key]))
								$value1 = implode(',',array_diff(explode(',',$value),explode(',',$insert_param[$key])));
							else
								$value1 = $value;

							if(!empty($value1)){
								$agents11 = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in($value1)");
								if(!empty($agents11['name']))
									$agent_value = $agents11['name'];
							}
					}elseif(in_array($key,array('assigned_product','products_variation')) && !empty($value)  && $value!=''){
						$value1 = '';
						if(!empty($insert_param[$key]))
							$value1 = implode(',',array_diff(explode(',',$value),explode(',',$insert_param[$key])));
						else
							$value1 = $value;
						if(!empty($value1)){
							$products11 = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name,' (',product_code,')')) as name from prd_main where id in($value1)");
							if(!empty($products11['name']))
								$product_value = $products11['name'];
						}
					}

					$agent_value_old = $product_value_old = '';
					if(in_array($key,array('assigned_agent','agents_downline','agents_loa')) && !empty($insert_param[$key]) && $insert_param[$key]!=''){

						if(!empty($value))
							$insert_param[$key] = implode(',',array_diff(explode(',',$insert_param[$key]),explode(',',$value)));
						if(!empty($insert_param[$key])){
							$agents11 = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in($insert_param[$key])");
							if(!empty($agents11['name']))
								$agent_value_old = $agents11['name'];
						}
						
					}elseif(in_array($key,array('assigned_product','products_variation')) && !empty($insert_param[$key])  && $insert_param[$key]!=''){
						if(!empty($value))
							$insert_param[$key] = implode(',',array_diff(explode(',',$insert_param[$key]),explode(',',$value)));
						if(!empty($insert_param[$key])){
							$products11 = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name,' (',product_code,')')) as name from prd_main where id in($insert_param[$key])");
							if(!empty($products11['name']))
								$product_value_old = $products11['name'];
						}
						
					}
					
					if(in_array($key,array('assigned_agent','agents_downline','agents_loa')) && !empty($value)  && $value!=''){
						$value = $agent_value;
					}elseif(in_array($key,array('assigned_product','products_variation')) && !empty($value)  && $value!=''){
						$value = $product_value;
					}

					if(in_array($key,array('assigned_agent','agents_downline','agents_loa')) && !empty($insert_param[$key])){
						$insert_param[$key] = $agent_value_old;
					}elseif(in_array($key,array('assigned_product','products_variation')) && !empty($insert_param[$key])){
						$insert_param[$key] = $product_value_old;
					}
					
					if(in_array($key,array('assigned_agent','agents_downline','agents_loa','assigned_product','products_variation'))){
						if(in_array($key,array('assigned_agent','agents_downline','agents_loa')) && !empty($insert_param[$key])){
							$activity_description['key_value']['desc_arr'][$key.'_selected'] = $insert_param[$key] .' Selected.';
							$activity_update = true;
						}
						if(in_array($key,array('assigned_agent','agents_downline','agents_loa')) && !empty($value) && $value!=''){
							$activity_description['key_value']['desc_arr'][$key.'_unselected'] = $value .' Unselected.';
							$activity_update = true;
						}
						if(in_array($key,array('assigned_product','products_variation')) && !empty($value)  && $value!=''){
							$activity_description['key_value']['desc_arr'][$key.'_unselected'] = $value.' Unselected';
							$activity_update = true;
						}
						if(in_array($key,array('assigned_product','products_variation')) && !empty($insert_param[$key])  && $insert_param[$key]!=''){
							$activity_description['key_value']['desc_arr'][$key.'_selected'] = $insert_param[$key].' Selected';
							$activity_update = true;
						}
					}else{
						$activity_description['key_value']['desc_arr'][$key] = 'updated from '.$value.' to '.$insert_param[$key];
						$activity_update = true;
					}
				}
			}
		}
		$databaseFields = $merchant_res;
		if(!empty($databaseFields['live_details'])){
			$databaseFields_live_details_arr = json_decode($databaseFields['live_details'], true);
			if(!empty($databaseFields_live_details_arr)){
				foreach ($databaseFields_live_details_arr as $key => $value) {
					$databaseFields["live_".$key."_details"] = $value;
				}
				unset($databaseFields['live_details']);
			}
		}
		if(!empty($databaseFields['sandbox_details'])){
			$databaseFields_sandbox_details_arr = json_decode($databaseFields['sandbox_details'], true);
			if(!empty($databaseFields_sandbox_details_arr)){
				foreach ($databaseFields_sandbox_details_arr as $key => $value) {
					$databaseFields["sandbox_".$key."_details"] = $value;
				}
				unset($databaseFields['sandbox_details']);
			}
		}
		$function_list = new functionsList();
        // Database detials end
        $function_list->get_updated_payment_field($payment_master_id, 'N', $insert_param, $databaseFields);
		$response['status'] = 'success';
		if($activity_update){
			activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $_SESSION['admin']['id'], 'Admin', 'Merchant Processor Updated',  $_SESSION['admin']['fname'],  $_SESSION['admin']['lname'], json_encode($activity_description));
		}

	}
}

header('Content-type: application/json');
$errors = $validate->getErrors();
$response['errors'] = $errors;
echo json_encode($response);
dbConnectionClose();
exit;
?>