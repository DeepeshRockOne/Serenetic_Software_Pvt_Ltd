<?php
include_once 'layout/start.inc.php';
$res = array();

$validate = new Validation();
$commission_array = array();

$change_commission_option = array();



$parent_rule_id = 0;
$parentCommissionRuleID = !empty($_POST['parentCommission']) ? $_POST['parentCommission'] : 0;
$commissionRuleID = !empty($_POST['commission']) ? $_POST['commission'] : 0;
$product = !empty($_POST['product']) ? $_POST['product'] : array();
$display_id = !empty($_POST['display_id']) ? $_POST['display_id'] : '';
$commission_status = !empty($_POST['commission_status']) ? $_POST['commission_status'] : '';
$commission_type = !empty($_POST['commission_type']) ? $_POST['commission_type'] : '';
$is_clone = !empty($_POST['is_clone']) ? $_POST['is_clone'] : 'N';


$change_commission_rate_after = !empty($_POST['change_commission_rate_after']) ? $_POST['change_commission_rate_after'] : array();
$commission_calculate_by = !empty($_POST['commission_calculate_by']) ? $_POST['commission_calculate_by'] : array();
$commission_duration = !empty($_POST['commission_duration']) ? $_POST['commission_duration'] : array();
$initialCommissionDuration = '';
$initialCalculateBy = '';
$commission_price = !empty($_POST['commission_price']) ? $_POST['commission_price'] : array();

$stop_commission_after = !empty($_POST['stop_commission_after']) ? $_POST['stop_commission_after'] : '';
$new_business_commission_duration = !empty($_POST['new_business_commission_duration']) ? $_POST['new_business_commission_duration'] : '';
$renewal_commission_duration = !empty($_POST['renewal_commission_duration']) ? $_POST['renewal_commission_duration'] : '';
$commission_reversals = !empty($_POST['commission_reversals']) ? $_POST['commission_reversals'] : '';
$reverse_days = !empty($_POST['reverse_days']) ? $_POST['reverse_days'] : ''; 

if(!empty($product)){
	$pro = explode(",", $product);
	$product_id = $pro[0];
	$product_type = $pro[1];
	$parent_product_id = $pro[2];
}

if(empty($product)){
	$validate->setError('product','Please Select Product');
}

$validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Please Enter Commission ID'));
if (!$validate->getError('display_id')) {
	$schParams=array(":rule_code"=>$display_id);
	$incr='';

	if(!empty($commissionRuleID) && $is_clone == 'N'){
		$incr.=" AND md5(id) != :id";
		$schParams[':id']=$commissionRuleID;
	}
	$sqlCommissionRule="SELECT id FROM commission_rule where rule_code=:rule_code AND is_deleted='N' $incr";
	$resCommissionRule=$pdo->selectOne($sqlCommissionRule,$schParams);

	if(!empty($resCommissionRule)){
		$validate->setError('display_id',"Commission ID Already Exists");
	}
}

$validate->string(array('required' => true, 'field' => 'commission_status', 'value' => $commission_status), array('required' => 'Please Select Status'));
$validate->string(array('required' => true, 'field' => 'commission_type', 'value' => $commission_type), array('required' => 'Please Select Commission Type'));

$i=1;
if(!empty($commission_calculate_by)){
	$previousKey = null;
	foreach ($commission_calculate_by as $mainKey => $mainValue) {
		if($i>1){
			$validate->string(array('required' => true, 'field' => 'change_commission_rate_after_'.abs($mainKey), 'value' => $change_commission_rate_after[$mainKey]), array('required' => 'Please Select Commission Rate'));
			if (!$validate->getError('change_commission_rate_after_'.abs($mainKey)) && !empty($previousKey)) {
				if ($change_commission_rate_after[$mainKey] <= $change_commission_rate_after[$previousKey]) {
						$validate->setError('change_commission_rate_after_'.abs($mainKey), 'Please Enter Valid Change Commission Rate ');
				}
			}
			if (!$validate->getError('change_commission_rate_after_'.abs($mainKey))){
				$change_commission_option[$change_commission_rate_after[$mainKey]]['commission_id'] = str_replace("R","",$mainKey);
				$change_commission_option[$change_commission_rate_after[$mainKey]]['commission_duration'] =$commission_duration[$mainKey];
				$change_commission_option[$change_commission_rate_after[$mainKey]]['commission_calculate_by'] =$commission_calculate_by[$mainKey];

			}
			$previousKey = $mainKey;
		}else{
			$initialCommissionDuration = $commission_duration[$mainKey];
			$initialCalculateBy = $commission_calculate_by[$mainKey];
		}
		

		$validate->string(array('required' => true, 'field' => 'commission_calculate_by_'.abs($mainKey), 'value' => $commission_calculate_by[$mainKey]), array('required' => 'Please Select Calculate By'));

		$validate->string(array('required' => true, 'field' => 'commission_duration_'.abs($mainKey), 'value' => $commission_duration[$mainKey]), array('required' => 'Please Select Commission Duration'));
		if (!$validate->getError('commission_duration_'.abs($mainKey))) {
			if($commission_duration[$mainKey]=="commission_stop"){
				$validate->string(array('required' => true, 'field' => 'stop_commission_after', 'value' => $stop_commission_after), array('required' => 'Please Enter Stop Commission Rate'));
			
				if (!$validate->getError('stop_commission_after')) {
					if($stop_commission_after <= $change_commission_rate_after[$mainKey]){
						$validate->setError('stop_commission_after','Enter Valid Stop Commission Range');
					}
				}
			}
		}
		$i++;	
	}
}

if(!empty($commission_price)){
	if (!$validate->getError('commission_type')) {
		if($commission_type=="Agent Level"){
			foreach ($commission_price as $priceKey => $priceLevel) {
				$priceError=array();
				$tempArray = array();
				foreach ($priceLevel as $levelKey => $levelValue) {
					if($levelValue==""){
						array_push($priceError,true);
					}else{
						if(empty($change_commission_rate_after[$priceKey])){
							$commission_array[$levelKey]['amount']=$levelValue;
							$commission_array[$levelKey]['amount_type']=$commission_calculate_by[$priceKey];
						}else{
							$tempArray[$levelKey]['amount']=$levelValue;
							$tempArray[$levelKey]['amount_type']=$commission_calculate_by[$priceKey];
						}
						
					}
				}
				if(!empty($tempArray)){
					$change_commission_option[$change_commission_rate_after[$priceKey]]['commission_price'] = $tempArray;
				}
				if(in_array(true, $priceError)){
					$validate->setError('commission_price_'.abs($priceKey),'Please Enter Commission Amount');
				}
				
			}
			
		}else if($commission_type=="Plan"){
			foreach ($commission_price as $priceKey => $priceLevel) {
				$tempArray = array();
				foreach ($priceLevel as $planKey => $planValue) {
					$priceError=array();
					foreach ($planValue as $levelKey => $levelValue) {
						if($levelValue==""){
							array_push($priceError,true);
						}else{
							if(empty($change_commission_rate_after[$priceKey])){
								$commission_array[$planKey][$levelKey]['amount']=$levelValue;
								$commission_array[$planKey][$levelKey]['amount_type']=$commission_calculate_by[$priceKey];
							}else{
								
								$tempArray[$planKey][$levelKey]['amount']=$levelValue;
								$tempArray[$planKey][$levelKey]['amount_type']=$commission_calculate_by[$priceKey];
								
							}
						}
					}
					
					if(in_array(true, $priceError)){
						$validate->setError('commission_price_'.abs($priceKey).'_'.abs($planKey),'Please Enter Commission Amount');
					}
				}
				if(!empty($tempArray)){
					$change_commission_option[$change_commission_rate_after[$priceKey]]['commission_price'] = $tempArray;
				}
			}
		}
	}	
}

$validate->string(array('required' => true, 'field' => 'new_business_commission_duration', 'value' => $new_business_commission_duration), array('required' => 'Select any option'));
$validate->string(array('required' => true, 'field' => 'renewal_commission_duration', 'value' => $renewal_commission_duration), array('required' => 'Select any option'));
if(empty($commission_reversals)){
	$validate->setError('commission_reversals','Select any option');
}else if($commission_reversals  == 'not_reverse_after'){
	$validate->string(array('required' => true, 'field' => 'reverse_days', 'value' => $reverse_days), array('required' => 'Please select option'));
}

if ($validate->isValid()) {
		if(!empty($parentCommissionRuleID)){
			$sqlCommissionRule="SELECT id FROM commission_rule where md5(id)=:id";
			$resCommissionRule=$pdo->selectOne($sqlCommissionRule,array(":id"=>$parentCommissionRuleID));

			if(!empty($resCommissionRule)){
				$parent_rule_id = $resCommissionRule['id'];
			}
		}
		$ins_params = array(
			'product_id' => $product_id,
			'parent_product_id' => $parent_product_id,
			'product_type' => $product_type,
			'parent_rule_id' => $parent_rule_id,
			'commission_on' => $commission_type,
			'commission_duration' => $initialCommissionDuration,
			'calculate_by' => $initialCalculateBy,
			'new_business_commission_duration' => $new_business_commission_duration,
			'renewal_commission_duration' => $renewal_commission_duration,
			'status' => $commission_status,
			'rule_code' => $display_id,
			'commission_json'=>json_encode($commission_array,JSON_PRETTY_PRINT),
			
		);
		if(!empty($stop_commission_after)){
			$ins_params['stop_commission_after']=$stop_commission_after;
		}

		if($commission_reversals == 'system_default'){
			$ins_params['commission_reversals']=$commission_reversals;
			$ins_params['reverse_days']= 0;
		}else if($commission_reversals == 'not_reverse_after'){
			$ins_params['commission_reversals']=$commission_reversals;
			$ins_params['reverse_days']=$reverse_days;
		}
		
		$oldValKey=array_keys($ins_params);
		$oldVal = implode(",", $oldValKey);
		
		$sqlCommissionRule="SELECT id,$oldVal FROM commission_rule where md5(id)=:id";
		$resCommissionRule=$pdo->selectOne($sqlCommissionRule,array(":id"=>$commissionRuleID));
		if(!empty($resCommissionRule) && $is_clone == 'N'){
			$commission_rule_id = $resCommissionRule['id'];
			//************* Activity Code Start *************
				$oldVaArray = $resCommissionRule;
				$NewVaArray = $ins_params;
				unset($oldVaArray['id']);

				$checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
				if(!empty($checkDiff)){
					$activityFeedDesc['ac_message'] =array(
						'ac_red_1'=>array(
							'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
							'title'=>$_SESSION['admin']['display_id']),
						'ac_message_1' =>' Updated Commission ',
					); 
					
					$extraJson = array();
					foreach ($checkDiff as $key1 => $value1) {
						$activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
					}
					unset($activityFeedDesc['key_value']['desc_arr']['commission_json']);
					if(!empty($checkDiff['commission_json'])){
						$activityFeedDesc['ac_commission_link']=array(
							'From'=>array('href'=>'#javascript:void(0)','class'=>'commissionJson','title'=>'Commission'),
							'To'=>array('href'=>'#javascript:void(0)','class'=>'commissionJson','title'=>'Commission'),
						);
						$extraJson = array(
							"From"=>$oldVaArray['commission_json'],
							"FromType"=>$oldVaArray['commission_on'],
							"To"=>$NewVaArray['commission_json'],
							"ToType"=>$NewVaArray['commission_on'],
						);
						
					}
					$activityFeedDesc['ac_message']['ac_red_2']=array(
						'href'=>$ADMIN_HOST.'/add_commission_rule.php?commission='.md5($commission_rule_id),
						'title'=>$display_id
					); 
					
					activity_feed(3, $_SESSION['admin']['id'], 'Admin', $commission_rule_id, 'commission','Admin Updated Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc),'',json_encode($extraJson));
				}
			//************* Activity Code End   *************
			
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $commission_rule_id,
				),
			);
			$pdo->update('commission_rule', $ins_params, $upd_where);
			$res['msg'] = "Commission rule updated Successfully";

			$ins_rangeparams=array("is_deleted"=>'Y');
			$upd_where = array(
				'clause' => 'commission_rule_id = :commission_rule_id',
				'params' => array(
					':commission_rule_id' => $commission_rule_id,
				),
			);
			$pdo->update('commission_rule_range', $ins_rangeparams, $upd_where);

		}else{
			$commission_rule_id = $pdo->insert("commission_rule", $ins_params);
			$res['msg'] = "Commission rule added Successfully";

			$description['ac_message'] =array(
				'ac_red_1'=>array(
					'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
					'title'=>$_SESSION['admin']['display_id'],
				),
				'ac_message_1' =>' Created Commission ',
				'ac_red_2'=>array(
						'href'=>$ADMIN_HOST.'/add_commission_rule.php?commission='.md5($commission_rule_id),
						'title'=>$display_id,
				),
			); 
			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $commission_rule_id, 'commission','Admin Created Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
		}

		if ($initialCommissionDuration == "Change Commission" || $initialCommissionDuration == "Stop Paying") {
			$changeKeys = array_keys($change_commission_option);
			foreach(array_keys($changeKeys) AS $k ){
				$From = $changeKeys[$k];
				$To = 999;
				
				if(isset($changeKeys[$k+1])){
					$To = $changeKeys[$k+1];
				}

				if($change_commission_option[$changeKeys[$k]]['commission_duration']=="Stop Paying"){
					$To = $stop_commission_after;
				}
				
				$ins_rangeparams = array(
					'is_deleted'=>'N',
					'commission_rule_id' => $commission_rule_id,
					'from_renewal' => $From,
					'to_renewal' => $To,
					'commission_duration' => $change_commission_option[$changeKeys[$k]]['commission_duration'],
					'calculate_by' => $change_commission_option[$changeKeys[$k]]['commission_calculate_by'],
					'commission_on' => $commission_type,
					'commission_json' => json_encode($change_commission_option[$changeKeys[$k]]['commission_price'],JSON_PRETTY_PRINT),
				);

				$oldValKey=array_keys($ins_rangeparams);
				$oldVal = implode(",", $oldValKey);

				$sqlCommissionRuleRange="SELECT id,$oldVal FROM commission_rule_range where id=:id AND commission_rule_id =:commission_rule_id";
				$resCommissionRuleRange=$pdo->selectOne($sqlCommissionRuleRange,array(":commission_rule_id"=>$commission_rule_id,":id"=>$change_commission_option[$changeKeys[$k]]['commission_id']));

				if(!empty($resCommissionRuleRange) && $is_clone == 'N'){
					//************* Activity Code Start *************
						$oldVaArray = $resCommissionRuleRange;
						$newVaArray = $ins_rangeparams;
						
						unset($newVaArray['is_deleted']);
						unset($newVaArray['to_renewal']);
						unset($oldVaArray['id']);
						unset($oldVaArray['is_deleted']);
						unset($oldVaArray['to_renewal']);
						
						$checkDiff=array_diff_assoc($newVaArray, $oldVaArray);
						if(!empty($checkDiff)){
							$activityFeedDesc['ac_message'] =array(
								'ac_red_1'=>array(
									'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
									'title'=>$_SESSION['admin']['display_id']),
								'ac_message_1' =>' Updated Commission ',
							); 
							
							$extraJson = array();
							foreach ($checkDiff as $key1 => $value1) {
								$activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$newVaArray[$key1];
							}
							unset($activityFeedDesc['key_value']['desc_arr']['commission_json']);
							if(!empty($checkDiff['commission_json'])){
								$activityFeedDesc['ac_commission_link']=array(
									'From'=>array('href'=>'#javascript:void(0)','class'=>'commissionJson','title'=>'Commission'),
									'To'=>array('href'=>'#javascript:void(0)','class'=>'commissionJson','title'=>'Commission'),
								);
								$extraJson = array(
									"From"=>$oldVaArray['commission_json'],
									"FromType"=>$oldVaArray['commission_on'],
									"To"=>$newVaArray['commission_json'],
									"ToType"=>$newVaArray['commission_on'],
								);
								
							}
							$activityFeedDesc['ac_message']['ac_red_2']=array(
								'href'=>$ADMIN_HOST.'/add_commission_rule.php?commission='.md5($commission_rule_id),
								'title'=>$display_id
							); 
							
							activity_feed(3, $_SESSION['admin']['id'], 'Admin', $commission_rule_id, 'commission','Admin Updated Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc),'',json_encode($extraJson));
						}
					//************* Activity Code End   *************

					$upd_where = array(
						'clause' => 'id = :id',
						'params' => array(
							':id' => $resCommissionRuleRange['id'],
						),
					);
					$pdo->update('commission_rule_range', $ins_rangeparams, $upd_where);
				}else{
					$range_id = $pdo->insert("commission_rule_range", $ins_rangeparams);
				}

			}
		}


		$res['status'] = "success";
		$res['parentCommissionRuleID'] = $parentCommissionRuleID;
		setNotifySuccess($res['msg']);
	
} else {
	$res['status'] = "fail";
	$res['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>