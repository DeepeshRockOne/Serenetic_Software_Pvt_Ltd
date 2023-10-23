<?php
include_once 'layout/start.inc.php';
 
$response=array();
$validate = new Validation();

$matrix_id=isset($_POST['matrix_id']) ? $_POST['matrix_id'] : 0;
$product_id=isset($_POST['product_id'])? $_POST['product_id']: 0;
$response['product_id']=$product_id;
$sqlProduct="SELECT * FROM prd_main WHERE id=:product_id";
$params=array(":product_id"=>$product_id);
$resProduct=$pdo->selectOne($sqlProduct,$params);

$coverage_options_list=0;

if($resProduct){
	if($resProduct['price_control']!=""){
	  $PrCtrl= json_decode($resProduct['price_control'],true);
	}
	$coverage_options_list = $resProduct['coverage_options'];
	$coverage_options = !empty($coverage_options_list) ? explode(",", $coverage_options_list) : array();
	if(empty($coverage_options_list)){
		$coverage_options_list=0;
	}
}

$plan_type = isset($_POST['plan_type'])?$_POST['plan_type']:"";

$age_from = isset($_POST['age_from'])?$_POST['age_from']:"";
$age_to = isset($_POST['age_to'])?$_POST['age_to']:"";
$state = isset($_POST['state'])?$_POST['state']:"";
$zip = isset($_POST['zip'])?$_POST['zip']:"";
$gender = isset($_POST['gender'])?$_POST['gender']:"";

$smoking_status = isset($_POST['smoking_status'])?$_POST['smoking_status']:"";
$tobacco_status = isset($_POST['tobacco_status'])?$_POST['tobacco_status']:"";
$height_feet = isset($_POST['height_feet'])?$_POST['height_feet']:"";
$height_inch = isset($_POST['height_inch'])?$_POST['height_inch']:"";
$weight = isset($_POST['weight'])?$_POST['weight']:"";

$no_of_children = isset($_POST['no_of_children'])?$_POST['no_of_children']:"";
$has_spouse = isset($_POST['has_spouse'])?$_POST['has_spouse']:"";
$spouse_age_from = isset($_POST['spouse_age_from'])?$_POST['spouse_age_from']:"";
$spouse_age_to = isset($_POST['spouse_age_to'])?$_POST['spouse_age_to']:"";
$spouse_gender = isset($_POST['spouse_gender'])?$_POST['spouse_gender']:"";

$spouse_smoking_status = isset($_POST['spouse_smoking_status'])?$_POST['spouse_smoking_status']:"";
$spouse_tobacco_status = isset($_POST['spouse_tobacco_status'])?$_POST['spouse_tobacco_status']:"";
$spouse_height_feet = isset($_POST['spouse_height_feet'])?$_POST['spouse_height_feet']:"";
$spouse_height_inch = isset($_POST['spouse_height_inch'])?$_POST['spouse_height_inch']:"";
$spouse_weight = isset($_POST['spouse_weight'])?$_POST['spouse_weight']:"";

$pricing_matrix = isset($_POST['pricing_matrix'])?$_POST['pricing_matrix']:array();

$pricing_effective_date = isset($_POST['pricing_effective_date'])?$_POST['pricing_effective_date']:array();
$pricing_termination_date = isset($_POST['pricing_termination_date'])?$_POST['pricing_termination_date']:array();

$validate->string(array('required' => true, 'field' => 'plan_type', 'value' => $plan_type), array('required' => 'Plan Type is required'));

if(in_array("Age", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'age_from', 'value' => $age_from), array('required' => 'Select Age From'));
	$validate->string(array('required' => true, 'field' => 'age_to', 'value' => $age_to), array('required' => 'Select Age To'));
}if(in_array("State", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'Select State'));
}if(in_array("Zip Code", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'zip', 'value' => $zip), array('required' => 'Zipcode is required'));
}if(in_array("Gender", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'gender', 'value' => $gender), array('required' => 'Select Gender'));
}if(in_array("Smoke", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'smoking_status', 'value' => $smoking_status), array('required' => 'Select Smoke'));
}if(in_array("Tobacco Use", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'tobacco_status', 'value' => $tobacco_status), array('required' => 'Select Tobacco Use'));
}if(in_array("Height", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'height_feet', 'value' => $height_feet), array('required' => 'Select Height Feet'));
	$validate->string(array('required' => true, 'field' => 'height_inch', 'value' => $height_inch), array('required' => 'Select Height Inch'));
}if(in_array("Weight", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'weight', 'value' => $weight), array('required' => 'Weight is required'));
}if(in_array("Number Of Children", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'no_of_children', 'value' => $no_of_children), array('required' => 'Select No of Children'));
}if(in_array("Has Spouse", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'has_spouse', 'value' => $has_spouse), array('required' => 'Select Any Option'));
}if(in_array("Spouse Age", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'spouse_age_from', 'value' => $spouse_age_from), array('required' => 'Select Spouse Age From'));
	$validate->string(array('required' => true, 'field' => 'spouse_age_to', 'value' => $spouse_age_to), array('required' => 'Select Spouse Age To'));
}if(in_array("Spouse Gender", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'spouse_gender', 'value' => $spouse_gender), array('required' => 'Select Gender'));
}if(in_array("Spouse Smoke", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'spouse_smoking_status', 'value' => $spouse_smoking_status), array('required' => 'Select Smoke'));
}if(in_array("Spouse Tobacco Use", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'spouse_tobacco_status', 'value' => $spouse_tobacco_status), array('required' => 'Select Tobacco Use'));
}if(in_array("Spouse Height", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'spouse_height_feet', 'value' => $spouse_height_feet), array('required' => 'Select Height Feet'));
	$validate->string(array('required' => true, 'field' => 'spouse_height_inch', 'value' => $spouse_height_inch), array('required' => 'Select Height Inch'));
}if(in_array("Spouse Weight", $PrCtrl)){
	$validate->string(array('required' => true, 'field' => 'spouse_weight', 'value' => $spouse_weight), array('required' => 'Weight is required'));
}

foreach ($pricing_matrix as $matrix_group => $matrix_array) {
		$validate->string(array('required' => true, 'field' => 'pricing_matrix_'.$matrix_group, 'value' => $pricing_matrix[$matrix_group]['Sale']), array('required' => 'Please Add Price'));
		$validate->string(array('required' => true, 'field' => 'pricing_matrix_'.$matrix_group, 'value' => $pricing_matrix[$matrix_group]['NonCommissionable']), array('required' => 'Please Add Price'));
		$validate->string(array('required' => true, 'field' => 'pricing_matrix_'.$matrix_group, 'value' => $pricing_matrix[$matrix_group]['Commissionable']), array('required' => 'Please Add Price'));

		if(str_replace(",","",$pricing_matrix[$matrix_group]['Sale']) <  str_replace(",","",$pricing_matrix[$matrix_group]['NonCommissionable'])){
			$validate->setError("pricing_matrix_".$matrix_group,"Enter Valid Price".$pricing_matrix[$matrix_group]['NonCommissionable']);
		}
	
	$validate->string(array('required' => true, 'field' => 'pricing_effective_date_'.$matrix_group, 'value' => $pricing_effective_date[$matrix_group]), array('required' => 'Add Effective Date'));
	 
	if(!empty($pricing_effective_date[$matrix_group]) && !empty($pricing_termination_date[$matrix_group])){
		$effectiveDate=date('Y-m-d',strtotime($pricing_effective_date[$matrix_group]));
		$terminationDate=date('Y-m-d',strtotime($pricing_termination_date[$matrix_group]));
		$todayDate=date('Y-m-d');
		if(strtotime($effectiveDate) >= strtotime($terminationDate)){
			$validate->setError("pricing_effective_date_".$matrix_group,"Enter Valid Date");
		}
		if(strtotime($terminationDate) <= strtotime($todayDate)){
			//$validate->setError("pricing_termination_date_".$matrix_group,"Enter Valid Date");
		}
	}

}
if ($validate->isValid()) {
    if(count($pricing_matrix) > 0){
		foreach ($pricing_matrix as $matrix_group => $matrix_array) {
				$non_commissionable = str_replace(",","",$pricing_matrix[$matrix_group]['NonCommissionable']);
				$commissionable = str_replace(",","",$pricing_matrix[$matrix_group]['Commissionable']);
				$price = str_replace(",","",$pricing_matrix[$matrix_group]['Sale']);

				$sqlMatrix="SELECT id FROM prd_matrix where product_id=:product_id AND is_deleted='N' AND matrix_group=:matrix_group AND id=:id";
				$whereMatrix=array(":product_id"=>$product_id,":matrix_group"=>$matrix_group,":id"=>$matrix_id);
				$resMatrix=$pdo->selectOne($sqlMatrix,$whereMatrix);
				
				$ins_params = array(
					'product_id' => ($product_id),
					'opt_product_sell_type' => ($resProduct['opt_product_sell_type']),
					'opt_rate_engine' => ($resProduct['opt_rate_engine']),
					'price' => $price,
					'non_commission_amount'=>$non_commissionable,
					'commission_amount'=>$commissionable,
					'plan_type' => $plan_type,
					'age_from' => 0,
					'age_to' => 0,
					'state' => '',
					'zip' => '',
					'gender' => '',
					'smoking_status' => '',
					'tobacco_status' => '',
					'height_feet' => '',
					'height_inch' => '',
					'weight' => '',
					'no_of_children' => '',
					'has_spouse' => '',
					'spouse_age_from' => 0,
					'spouse_age_to' => 0,
					'spouse_gender' => '',
					'spouse_smoking_status' => '',
					'spouse_tobacco_status' => '',
					'spouse_height_feet' => '',
					'spouse_height_inch' => '',
					'spouse_weight' => '',
					'matrix_group'=>$matrix_group,
					'pricing_effective_date'=>(!empty($pricing_effective_date[$matrix_group])) ? date('Y-m-d',strtotime($pricing_effective_date[$matrix_group])) : NULL,
					'pricing_termination_date'=>(!empty($pricing_termination_date[$matrix_group])) ? date('Y-m-d',strtotime($pricing_termination_date[$matrix_group])) : NULL,
					'update_date' => 'msqlfunc_NOW()',
					'create_date' => 'msqlfunc_NOW()',
				);
				if(in_array("Age", $PrCtrl)){
					$ins_params['age_from']=$age_from;
					$ins_params['age_to']=$age_to;
				}if(in_array("State", $PrCtrl)){
					$ins_params['state']=$state;
				}if(in_array("Zip Code", $PrCtrl)){
					$ins_params['zip']=$zip;
				}if(in_array("Gender", $PrCtrl)){
					$ins_params['gender']=$gender;
				}if(in_array("Smoke", $PrCtrl)){
					$ins_params['smoking_status']=$smoking_status;
				}if(in_array("Tobacco Use", $PrCtrl)){
					$ins_params['tobacco_status']=$tobacco_status;
				}if(in_array("Height", $PrCtrl)){
					$ins_params['height_feet']=$height_feet;
					$ins_params['height_inch']=$height_inch;
				}if(in_array("Weight", $PrCtrl)){
					$ins_params['weight']=$weight;
				}if(in_array("Number Of Children", $PrCtrl)){
					$ins_params['no_of_children']=$no_of_children;
				}if(in_array("Has Spouse", $PrCtrl)){
					$ins_params['has_spouse']=$has_spouse;
				}if(in_array("Spouse Age", $PrCtrl)){
					$ins_params['spouse_age_from']=$spouse_age_from;
					$ins_params['spouse_age_to']=$spouse_age_to;
				}if(in_array("Spouse Gender", $PrCtrl)){
					$ins_params['spouse_gender']=$spouse_gender;
				}if(in_array("Spouse Smoke", $PrCtrl)){
					$ins_params['spouse_smoking_status']=$spouse_smoking_status;
				}if(in_array("Spouse Tobacco Use", $PrCtrl)){
					$ins_params['spouse_tobacco_status']=$spouse_tobacco_status;
				}if(in_array("Spouse Height", $PrCtrl)){
					$ins_params['spouse_height_feet']=$spouse_height_feet;
					$ins_params['spouse_height_inch']=$spouse_height_inch;
				}if(in_array("Spouse Weight", $PrCtrl)){
					$ins_params['spouse_weight']=$spouse_weight;
				}  
				if($resMatrix){
					$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$resMatrix['id']),
					);
					$pdo->update("prd_matrix", $ins_params,$updWhere);
					setNotifySuccess('Product Price Matrix Updated Successfully.');
				}else{
					$pdo->insert("prd_matrix", $ins_params);
					setNotifySuccess('Product Price Matrix Added Successfully.');
				}
			
		}
		$response['status']="success";
	}	
}else {
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>