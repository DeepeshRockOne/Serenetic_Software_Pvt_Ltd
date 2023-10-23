<?php
include_once 'layout/start.inc.php';
$res = array();

$validate = new Validation();
$globalMatrixCriteriaArr = array();

$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();

$globalMatrixCriteriaArr['price_control'] = $price_control = !empty($_POST['price_control']) ? $_POST['price_control'] : array();

$globalMatrixCriteriaArr['matrixID'] = $matrixID = !empty($_POST['matrixID']) ? $_POST['matrixID'] : '';
$globalMatrixCriteriaArr['keyID'] = $keyID = !empty($_POST['keyID']) ? $_POST['keyID'] : '';

$globalMatrixCriteriaArr['matrixPlanType'] =  $matrixPlanType = !empty($_POST['matrixPlanType']) ? $_POST['matrixPlanType'] : '';
$globalMatrixCriteriaArr['enrolleeMatrix'] = '';

$age_from = !empty($_POST['age_from'])?$_POST['age_from']:0;
$age_to = !empty($_POST['age_to'])?$_POST['age_to']:0;
$globalMatrixCriteriaArr[1] = (isset($age_from) && isset($age_to)) ? $age_from." To ".$age_to : '' ;
$globalMatrixCriteriaArr[2] = $state = !empty($_POST['state'])?$_POST['state']:"";
$globalMatrixCriteriaArr[4] = $gender = !empty($_POST['gender'])?$_POST['gender']:"";

$height_by = !empty($_POST['height_by'])?$_POST['height_by']:"";
$height_feet = !empty($_POST['height_feet'])?$_POST['height_feet']:0;
$height_inch = !empty($_POST['height_inch'])?$_POST['height_inch']:0;
$height_feet_to = !empty($_POST['height_feet_to'])?$_POST['height_feet_to']:0;
$height_inch_to = !empty($_POST['height_inch_to'])?$_POST['height_inch_to']:0;
$globalMatrixCriteriaArr[7] = $height_feet."Ft ".$height_inch."In".($height_by=="Range" ? " To ".$height_feet_to."Ft ".$height_inch_to."In" : '');

$weight_by = !empty($_POST['weight_by'])?$_POST['weight_by']:"";
$weight = !empty($_POST['weight'])?$_POST['weight']:0;
$weight_to = !empty($_POST['weight_to'])?$_POST['weight_to']:0;
$globalMatrixCriteriaArr[8] = $weight.($weight_by=="Range" ? " To ".$weight_to : '');

$globalMatrixCriteriaArr[5] = $smoking_status = !empty($_POST['smoking_status'])?$_POST['smoking_status']:"";
$globalMatrixCriteriaArr[6] = $tobacco_status = !empty($_POST['tobacco_status'])?$_POST['tobacco_status']:"";

$no_of_children_by = isset($_POST['no_of_children_by'])?$_POST['no_of_children_by']:"";
$no_of_children = !empty($_POST['no_of_children'])?$_POST['no_of_children']:0;
$no_of_children_to = isset($_POST['no_of_children_to'])?$_POST['no_of_children_to']:0;
$globalMatrixCriteriaArr[9] = $no_of_children .($no_of_children_by=="Range" ? " To ".$no_of_children_to : '');

$globalMatrixCriteriaArr[3] = $zip = !empty($_POST['zip'])?$_POST['zip']:"";

$globalMatrixCriteriaArr[10] = $has_spouse = !empty($_POST['has_spouse'])?$_POST['has_spouse']:"";
$globalMatrixCriteriaArr[12] = $spouse_gender = !empty($_POST['spouse_gender'])?$_POST['spouse_gender']:"";
$spouse_age_from = !empty($_POST['spouse_age_from'])?$_POST['spouse_age_from']:0;
$spouse_age_to = !empty($_POST['spouse_age_to'])?$_POST['spouse_age_to']:0;
$globalMatrixCriteriaArr[11] = $spouse_age_from." To ".$spouse_age_to;

$spouse_height_feet = !empty($_POST['spouse_height_feet'])?$_POST['spouse_height_feet']:0;
$spouse_height_inch = !empty($_POST['spouse_height_inch'])?$_POST['spouse_height_inch']:0;
$globalMatrixCriteriaArr[15] = $spouse_height_feet."Ft ".$spouse_height_inch."In";
$spouse_weight = !empty($_POST['spouse_weight'])?$_POST['spouse_weight']:0;
$spouse_weight_type = !empty($_POST['spouse_weight_type'])?$_POST['spouse_weight_type']:"";
$globalMatrixCriteriaArr[16] = $spouse_weight." ".$spouse_weight_type;
$globalMatrixCriteriaArr[13] = $spouse_smoking_status = !empty($_POST['spouse_smoking_status'])?$_POST['spouse_smoking_status']:"";
$globalMatrixCriteriaArr[14] = $spouse_tobacco_status = !empty($_POST['spouse_tobacco_status'])?$_POST['spouse_tobacco_status']:"";

$globalMatrixCriteriaArr[17] = $benefit_amount = !empty($_POST['benefit_amount'])?$_POST['benefit_amount']:0;
$globalMatrixCriteriaArr[18] = $in_patient_benefit = !empty($_POST['in_patient_benefit'])?$_POST['in_patient_benefit']:0;
$globalMatrixCriteriaArr[19] = $out_patient_benefit = !empty($_POST['out_patient_benefit'])?$_POST['out_patient_benefit']:0;
$globalMatrixCriteriaArr[20] = $monthly_income = !empty($_POST['monthly_income'])?$_POST['monthly_income']:0;
// $globalMatrixCriteriaArr[21] = $benefit_percentage = !empty($_POST['benefit_percentage'])?$_POST['benefit_percentage']:0;


$pricing_matrix_group = !empty($_POST['pricing_matrix_group'])?$_POST['pricing_matrix_group']:array();
$globalMatrixCriteriaArr['matrix_group'] = !empty($pricing_matrix_group) ? $pricing_matrix_group : '';
$pricing_matrix_price = !empty($_POST['pricing_matrix_price'])?$_POST['pricing_matrix_price']:array();
if(!empty($globalMatrixCriteriaArr['matrix_group'])){
	foreach($globalMatrixCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixCriteriaArr['tmp_price_arr'][$pkey]['RetailPrice'] = checkIsset($pricing_matrix_price[$pkey]['Retail']);
		$globalMatrixCriteriaArr['tmp_price_arr'][$pkey]['NonCommissionablePrice'] = checkIsset($pricing_matrix_price[$pkey]['NonCommissionable']);
		$globalMatrixCriteriaArr['tmp_price_arr'][$pkey]['CommissionablePrice'] = checkIsset($pricing_matrix_price[$pkey]['Commissionable']);
	}
}
$pricing_matrix_effective_date = !empty($_POST['pricing_matrix_effective_date'])?$_POST['pricing_matrix_effective_date']:array();
if(!empty($globalMatrixCriteriaArr['matrix_group'])){
	foreach($globalMatrixCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixCriteriaArr['tmp_price_arr'][$pkey]['pricing_matrix_effective_date'] = checkIsset($pricing_matrix_effective_date[$pkey]);
	}
}
$pricing_matrix_termination_date = !empty($_POST['pricing_matrix_termination_date'])?$_POST['pricing_matrix_termination_date']:array();
if(!empty($globalMatrixCriteriaArr['matrix_group'])){
	foreach($globalMatrixCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixCriteriaArr['tmp_price_arr'][$pkey]['pricing_matrix_termination_date'] = checkIsset($pricing_matrix_termination_date[$pkey]);
	}
}
$newPricingMatrixOnRenewals = !empty($_POST['newPricingMatrixOnRenewals'])?$_POST['newPricingMatrixOnRenewals']:array();
if(!empty($globalMatrixCriteriaArr['matrix_group'])){
	foreach($globalMatrixCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixCriteriaArr['tmp_price_arr'][$pkey]['newPricingMatrixOnRenewals'] = checkIsset($newPricingMatrixOnRenewals[$pkey]);
	}
}

$globalMatrixCriteriaArr['allowPricingUpdate'] = $allowPricingUpdate = !empty($_POST['allowPricingUpdate']) ? $_POST['allowPricingUpdate'] : false;
$globalMatrixCriteriaArr['pricingDataDisabled'] = $pricingDataDisabled = !empty($_POST['pricingDataDisabled']) ? $_POST['pricingDataDisabled'] : 'N';
if((!$allowPricingUpdate) || $pricingDataDisabled=='Y'){
	if(!$allowPricingUpdate){
		$globalMatrixCriteriaArr['price_control'] = $price_control = !empty($_POST['allow_price_control']) ? $_POST['allow_price_control'] : array();
	}
	$globalMatrixCriteriaArr['matrixPlanType'] = $matrixPlanType = !empty($_POST['allow_matrixPlanType']) ? $_POST['allow_matrixPlanType'] : '';

	$age_from = !empty($_POST['allow_age_from'])?$_POST['allow_age_from']:0;
	$age_to = !empty($_POST['allow_age_to'])?$_POST['allow_age_to']:0;
	$globalMatrixCriteriaArr[1] = (isset($age_from) && isset($age_to)) ? $age_from." To ".$age_to : '';
	$globalMatrixCriteriaArr[2] = $state = !empty($_POST['allow_state'])?$_POST['allow_state']:"";
	$globalMatrixCriteriaArr[4] = $gender = !empty($_POST['allow_gender'])?$_POST['allow_gender']:"";

	$height_by = !empty($_POST['allow_height_by'])?$_POST['allow_height_by']:"";
	$height_feet = !empty($_POST['allow_height_feet'])?$_POST['allow_height_feet']:0;
	$height_inch = !empty($_POST['allow_height_inch'])?$_POST['allow_height_inch']:0;
	$height_feet_to = !empty($_POST['allow_height_feet_to'])?$_POST['allow_height_feet_to']:0;
	$height_inch_to = !empty($_POST['allow_height_inch_to'])?$_POST['allow_height_inch_to']:0;
	$globalMatrixCriteriaArr[7] = $height_feet."Ft ".$height_inch."In".($height_by=="Range" ? " To ".$height_feet_to."Ft ".$height_inch_to."In" : '');

	$weight_by = !empty($_POST['allow_weight_by'])?$_POST['allow_weight_by']:"";
	$weight = !empty($_POST['allow_weight'])?$_POST['allow_weight']:0;
	$weight_to = !empty($_POST['allow_weight_to'])?$_POST['allow_weight_to']:0;
	$globalMatrixCriteriaArr[8] = $weight.($weight_by=="Range" ? " To ".$weight_to : '');


	$globalMatrixCriteriaArr[5] = $smoking_status = !empty($_POST['allow_smoking_status'])?$_POST['allow_smoking_status']:"";
	$globalMatrixCriteriaArr[6] = $tobacco_status = !empty($_POST['allow_tobacco_status'])?$_POST['allow_tobacco_status']:"";

	$no_of_children_by = isset($_POST['allow_no_of_children_by'])?$_POST['allow_no_of_children_by']:"";
	$no_of_children = isset($_POST['allow_no_of_children'])?$_POST['allow_no_of_children']:0;
	$no_of_children_to = isset($_POST['allow_no_of_children_to'])?$_POST['allow_no_of_children_to']:0;
	$globalMatrixCriteriaArr[9] = $no_of_children .($no_of_children_by=="Range" ? " To ".$no_of_children_to : '');

	$globalMatrixCriteriaArr[3] = $zip = !empty($_POST['allow_zip'])?$_POST['allow_zip']:"";

	$globalMatrixCriteriaArr[10] = $has_spouse = !empty($_POST['allow_has_spouse'])?$_POST['allow_has_spouse']:"";
	$globalMatrixCriteriaArr[12] = $spouse_gender = !empty($_POST['allow_spouse_gender'])?$_POST['allow_spouse_gender']:"";
	$spouse_age_from = !empty($_POST['allow_spouse_age_from'])?$_POST['allow_spouse_age_from']:0;
	$spouse_age_to = !empty($_POST['allow_spouse_age_to'])?$_POST['allow_spouse_age_to']:0;
	$globalMatrixCriteriaArr[11] = $spouse_age_from." To ".$spouse_age_to;

	$spouse_height_feet = !empty($_POST['allow_spouse_height_feet'])?$_POST['allow_spouse_height_feet']:0;
	$spouse_height_inch = !empty($_POST['allow_spouse_height_inch'])?$_POST['allow_spouse_height_inch']:0;
	$globalMatrixCriteriaArr[15] = $spouse_height_feet."Ft ".$spouse_height_inch."In";
	$spouse_weight = !empty($_POST['allow_spouse_weight'])?$_POST['allow_spouse_weight']:0;
	$spouse_weight_type = !empty($_POST['allow_spouse_weight_type'])?$_POST['allow_spouse_weight_type']:"";
	$globalMatrixCriteriaArr[16] = $spouse_weight." ".$spouse_weight_type;
	$globalMatrixCriteriaArr[13] = $spouse_smoking_status = !empty($_POST['allow_spouse_smoking_status'])?$_POST['allow_spouse_smoking_status']:"";
	$globalMatrixCriteriaArr[14] = $spouse_tobacco_status = !empty($_POST['allow_spouse_tobacco_status'])?$_POST['allow_spouse_tobacco_status']:"";

	$globalMatrixCriteriaArr[17] = $benefit_amount = !empty($_POST['allow_benefit_amount'])?$_POST['allow_benefit_amount']:0;
	$globalMatrixCriteriaArr[18] = $in_patient_benefit = !empty($_POST['allow_in_patient_benefit'])?$_POST['allow_in_patient_benefit']:0;
	$globalMatrixCriteriaArr[19] = $out_patient_benefit = !empty($_POST['allow_out_patient_benefit'])?$_POST['allow_out_patient_benefit']:0;
	$globalMatrixCriteriaArr[20] = $monthly_income = !empty($_POST['allow_monthly_income'])?$_POST['allow_monthly_income']:0;
	// $globalMatrixCriteriaArr[21] = $benefit_percentage = !empty($_POST['allow_benefit_percentage'])?$_POST['allow_benefit_percentage']:0;
}

$validate->string(array('required' => true, 'field' => 'matrixPlanType', 'value' => $matrixPlanType), array('required' => 'Plan Tier is required'));



if(empty($price_control)){
	$validate->setError("price_control","Pricing Criteria is required");
}





if(in_array("1", $price_control)){
	$validate->string(array('required' => true, 'field' => 'age', 'value' => $age_from), array('required' => 'Age is Required'));
	$validate->string(array('required' => true, 'field' => 'age', 'value' => $age_to), array('required' => 'Age is Required'));

	if (!$validate->getError('age')) {
		if($age_to < $age_from){
			$validate->setError("age","Select Valid Age");
		}
	}
}else{
	$age_from = "";
	$age_to = "";
}

if(in_array("2", $price_control)){
	$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'Select State'));
}else{
	$state = "";
}

if(in_array("3", $price_control)){
	$validate->string(array('required' => true, 'field' => 'zip', 'value' => $zip), array('required' => 'Zipcode is required'));
}else{
	$zip = "";
}

if(in_array("4", $price_control)){
	$validate->string(array('required' => true, 'field' => 'gender', 'value' => $gender), array('required' => 'Select Gender'));
}else{
	$gender = "";
}
if(in_array("5", $price_control)){
	$validate->string(array('required' => true, 'field' => 'smoking_status', 'value' => $smoking_status), array('required' => 'Select Smoking Status'));
}else{
	$smoking_status = "";
}

if(in_array("6", $price_control)){
	$validate->string(array('required' => true, 'field' => 'tobacco_status', 'value' => $tobacco_status), array('required' => 'Select Tobacco Status'));
}else{
	$tobacco_status = "";
}

if(in_array("7", $price_control)){
	$validate->string(array('required' => true, 'field' => 'height', 'value' => $height_by), array('required' => 'Height By is required'));
	$validate->string(array('required' => true, 'field' => 'height', 'value' => $height_feet), array('required' => 'Height Feet is required'));
	$validate->string(array('required' => true, 'field' => 'height', 'value' => $height_inch), array('required' => 'Height Inch is required'));

	if($height_by=="Range"){
		$validate->string(array('required' => true, 'field' => 'height', 'value' => $height_feet_to), array('required' => 'To Height Feet is required'));
		$validate->string(array('required' => true, 'field' => 'height', 'value' => $height_inch_to), array('required' => 'To Height Inch is required =>'.$height_inch_to));
	}
}else{
	$height_by = "";
	$height_feet = 0;
	$height_inch = 0;
	$height_feet_to = 0;
	$height_inch_to = 0;
}

if(in_array("8", $price_control)){
	$validate->string(array('required' => true, 'field' => 'weight', 'value' => $weight_by), array('required' => 'Weight By is required'));
	$validate->string(array('required' => true, 'field' => 'weight', 'value' => $weight), array('required' => 'Weight is required'));
	if($weight_by=="Range"){
		$validate->string(array('required' => true, 'field' => 'weight', 'value' => $weight_to), array('required' => 'To Weight is required'));
	}
}else{
	$weight_by = "";
	$weight = 0;
	$weight_to = 0;
}

if(in_array("9", $price_control)){
	$validate->string(array('required' => true, 'field' => 'no_of_children', 'value' => $no_of_children_by), array('required' => 'Children By is required'));
	$validate->string(array('required' => true, 'field' => 'no_of_children', 'value' => $no_of_children), array('required' => 'Select No of Children'));
	if($no_of_children_by=="Range"){
		$validate->string(array('required' => true, 'field' => 'no_of_children', 'value' => $no_of_children_to), array('required' => 'Select To No of Children'));
		
	}
}else{
	$no_of_children_by = "";
	$no_of_children = 0;
	$no_of_children_to = 0;
}

if(in_array("10", $price_control)){
	$validate->string(array('required' => true, 'field' => 'has_spouse', 'value' => $has_spouse), array('required' => 'Select Any Option'));
}else{
	$has_spouse = "";
}

if(in_array("11", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_age', 'value' => $spouse_age_from), array('required' => 'Spouse Age is Required'));
	$validate->string(array('required' => true, 'field' => 'spouse_age', 'value' => $spouse_age_to), array('required' => 'Spouse Age is Required'));
	if (!$validate->getError('spouse_age')) {
		if($spouse_age_to < $spouse_age_from){
			$validate->setError("spouse_age","Select Valid Age");
		}
	}
}else{
	$spouse_age_from = "";
	$spouse_age_to = "";
}

if(in_array("12", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_gender', 'value' => $spouse_gender), array('required' => 'Select Gender'));
}else{
	$spouse_gender = "";
}

if(in_array("13", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_smoking_status', 'value' => $spouse_smoking_status), array('required' => 'Select Smoking Status'));
}else{
	$spouse_smoking_status = "";
}

if(in_array("14", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_tobacco_status', 'value' => $spouse_tobacco_status), array('required' => 'Select Tobacco Status'));
}else{
	$spouse_tobacco_status = "";
}

if(in_array("15", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_height', 'value' => $spouse_height_feet), array('required' => 'Height is required'));
	$validate->string(array('required' => true, 'field' => 'spouse_height', 'value' => $spouse_height_inch), array('required' => 'Height is required'));
}else{
	$spouse_height_feet = 0;
	$spouse_height_inch = 0;
}

if(in_array("16", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_weight', 'value' => $spouse_weight), array('required' => 'Weight is required'));
	$validate->string(array('required' => true, 'field' => 'spouse_weight', 'value' => $spouse_weight_type), array('required' => 'Weight is required'));
}else{
	$spouse_weight = 0;
	$spouse_weight_type = '';
}

if(in_array("17", $price_control)){
	$validate->string(array('required' => true, 'field' => 'benefit_amount', 'value' => $benefit_amount), array('required' => 'Benefit Amount is required'));
	if($benefit_amount == "0.00"){
		$validate->setError("benefit_amount","Benefit Amount is required");
	}
}else{
	$benefit_amount = 0;
}

if(in_array("18", $price_control)){
	$validate->string(array('required' => true, 'field' => 'in_patient_benefit', 'value' => $in_patient_benefit), array('required' => 'InPatient Benefit Amount is required'));
	if($in_patient_benefit == "0.00"){
		$validate->setError("in_patient_benefit","InPatient Benefit Amount is required");
	}
}else{
	$in_patient_benefit = 0;
}

if(in_array("19", $price_control)){
	$validate->string(array('required' => true, 'field' => 'out_patient_benefit', 'value' => $out_patient_benefit), array('required' => 'OutPatient Benefit Amount is required'));
	if($out_patient_benefit == "0.00"){
		$validate->setError("out_patient_benefit","OutPatient Benefit Amount is required");
	}
}else{
	$out_patient_benefit = 0;
}

if(in_array("20", $price_control)){
	$validate->string(array('required' => true, 'field' => 'monthly_income', 'value' => $monthly_income), array('required' => 'Monthly Income is required'));
	if($monthly_income == "0.00"){
		$validate->setError("monthly_income","Monthly Income is required");
	}
}else{
	$monthly_income = 0;
}

// if(in_array("21", $price_control)){
// 	$validate->string(array('required' => true, 'field' => 'benefit_percentage', 'value' => $benefit_percentage), array('required' => 'Benefit Percentage is required'));
// 	if($benefit_percentage == "" || empty($benefit_percentage)){
// 		$validate->setError("benefit_percentage","Benefit Percentage is required");
// 	}
// }else{
// 	$benefit_percentage = "";
// }

$i=1;
$maxCount=count($pricing_matrix_price);
foreach ($pricing_matrix_price as $matrix_group => $matrix_array) {
	
	$validate->string(array('required' => true, 'field' => 'pricing_matrix_price_'.$matrix_group, 'value' => $pricing_matrix_price[$matrix_group]['Retail']), array('required' => 'Please Add Price'));
	$validate->string(array('required' => true, 'field' => 'pricing_matrix_price_'.$matrix_group, 'value' => $pricing_matrix_price[$matrix_group]['NonCommissionable']), array('required' => 'Please Add Price'));
	$validate->string(array('required' => true, 'field' => 'pricing_matrix_price_'.$matrix_group, 'value' => $pricing_matrix_price[$matrix_group]['Commissionable']), array('required' => 'Please Add Price'));

	if(str_replace(",","",$pricing_matrix_price[$matrix_group]['Retail']) <  str_replace(",","",$pricing_matrix_price[$matrix_group]['NonCommissionable'])){
		$validate->setError("pricing_matrix_price_".$matrix_group,"Enter Valid Price");
	}

	$validate->string(array('required' => true, 'field' => 'pricing_matrix_effective_date_'.$matrix_group, 'value' => $pricing_matrix_effective_date[$matrix_group]), array('required' => 'Add Effective Date'));
	

	if(!empty($pricing_matrix_effective_date[$matrix_group]) && !empty($pricing_matrix_termination_date[$matrix_group])){
		$effectiveDate=date('Y-m-d',strtotime($pricing_matrix_effective_date[$matrix_group]));
		$terminationDate=date('Y-m-d',strtotime($pricing_matrix_termination_date[$matrix_group]));
		$todayDate=date('Y-m-d');

		if(strtotime($effectiveDate) >= strtotime($terminationDate)){
			$validate->setError("pricing_matrix_termination_date_".$matrix_group,"Enter Valid Date");
		}
		if(strtotime($terminationDate) < strtotime($todayDate)){
			$validate->setError("pricing_matrix_termination_date_".$matrix_group,"Enter Valid Date");
		}
		if (!$validate->getError('pricing_matrix_termination_date_'.$matrix_group)) {
			list($mm, $dd, $yyyy) = explode('/', $pricing_matrix_termination_date[$matrix_group]);
			
			if (!checkdate($mm, $dd, $yyyy)) {
				$validate->setError("pricing_matrix_termination_date_".$matrix_group,"Enter Valid Date");
			}
		}
		if (!$validate->getError('pricing_matrix_effective_date_'.$matrix_group)) {
			list($mm, $dd, $yyyy) = explode('/', $pricing_matrix_effective_date[$matrix_group]);
			
			if (!checkdate($mm, $dd, $yyyy)) {
				$validate->setError("pricing_matrix_effective_date_".$matrix_group,"Enter Valid Date");
			}
		}
	}
	if($i > 1 && $i==$maxCount){
		if(empty($newPricingMatrixOnRenewals[$matrix_group])){
			$validate->setError('newPricingMatrixOnRenewals_'.$matrix_group,"Select Any Option");
		}
	}
	$i++;

}
$insertMatrix = true;
if($validate->isValid() && !empty($pricingMatrixKey)) {
	include_once dirname(__DIR__) .'/includes/function.class.php';
	$insertMatrix = functionsList::prdMatrixValidation($pricingMatrixKey,$globalMatrixCriteriaArr,'matrixPlanType');
}
if(!$insertMatrix){
	$validate->setError('global_matrix_arr_exits',"Please change value, Criteria Already Exists.");
}
if ($validate->isValid()) {
	foreach ($pricing_matrix_price as $matrix_group => $matrix_array) {
		$pricingMatrixKey[$pricing_matrix_group[$matrix_group]][$matrix_group]=
			array(
				'keyID'=>$pricing_matrix_group[$matrix_group],
				'matrix_group'=>$pricing_matrix_group[$matrix_group],
				'id'=>0,
				'matrixPlanType'=>$matrixPlanType,
				'enrolleeMatrix'=>'',
				'1'=>array("matrix_value"=>$age_from." To ".$age_to,
							"age_from"=>$age_from,
							"age_to"=>$age_to
						),
				'2'=>array("matrix_value"=>$state),
				'3'=>array("matrix_value"=>$zip),
				'4'=>array("matrix_value"=>$gender),
				'5'=>array("matrix_value"=>$smoking_status),
				'6'=>array("matrix_value"=>$tobacco_status),
				'7'=>array("matrix_value"=>$height_feet."Ft ".$height_inch."In".($height_by=="Range" ? " To ".$height_feet_to."Ft ".$height_inch_to."In" : ''),
							"height_by"=>$height_by,
							"height_feet"=>$height_feet,
							"height_inch"=>$height_inch,
							"height_feet_to"=>$height_feet_to,
							"height_inch_to"=>$height_inch_to,
						),
				'8'=>array("matrix_value"=>$weight.($weight_by=="Range" ? " To ".$weight_to : ''),
							"weight_by"=>$weight_by,
							"weight"=>$weight,
							"weight_to"=>$weight_to,
						),
				'9'=>array("matrix_value"=>$no_of_children .($no_of_children_by=="Range" ? " To ".$no_of_children_to : ''),
					"no_of_children_by"=>$no_of_children_by,
					"no_of_children"=>$no_of_children,
					"no_of_children_to"=>$no_of_children_to,
				),
				'10'=>array("matrix_value"=>$has_spouse),
				'11'=>array("matrix_value"=>$spouse_age_from." To ".$spouse_age_to,
							"spouse_age_from"=>$spouse_age_from,
							"spouse_age_to"=>$spouse_age_to
						),
				'12'=>array("matrix_value"=>$spouse_gender),
				'13'=>array("matrix_value"=>$spouse_smoking_status),
				'14'=>array("matrix_value"=>$spouse_tobacco_status),
				'15'=>array("matrix_value"=>$spouse_height_feet."Ft ".$spouse_height_inch."In",
							"spouse_height_feet"=>$spouse_height_feet,
							"spouse_height_inch"=>$spouse_height_inch
						),
				'16'=>array("matrix_value"=>$spouse_weight." ".$spouse_weight_type,
							"spouse_weight"=>$spouse_weight,
							"spouse_weight_type"=>$spouse_weight_type
						),
				'17'=>array("matrix_value"=>str_replace(",","", $benefit_amount)),
				'18'=>array("matrix_value"=>str_replace(",","", $in_patient_benefit)),
				'19'=>array("matrix_value"=>str_replace(",","", $out_patient_benefit)),
				'20'=>array("matrix_value"=>str_replace(",","", $monthly_income)),
				// '21'=>array("matrix_value"=>str_replace(",","", $benefit_percentage)),
				'RetailPrice'=>$pricing_matrix_price[$matrix_group]['Retail'],
				'NonCommissionablePrice'=>$pricing_matrix_price[$matrix_group]['NonCommissionable'],
				'CommissionablePrice'=>$pricing_matrix_price[$matrix_group]['Commissionable'],
				'pricing_matrix_effective_date'=>$pricing_matrix_effective_date[$matrix_group],
				'pricing_matrix_termination_date'=>$pricing_matrix_termination_date[$matrix_group],
				'newPricingMatrixOnRenewals'=>!empty($newPricingMatrixOnRenewals[$matrix_group]) ? $newPricingMatrixOnRenewals[$matrix_group] : '',
			);
	}
	$res['pricingMatrixKey'] = json_encode($pricingMatrixKey);
	$res['status'] = "success";
	$res['msg']="Pricing Added To Queue";
	
} else {
	$res['status'] = "fail";
	$res['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>