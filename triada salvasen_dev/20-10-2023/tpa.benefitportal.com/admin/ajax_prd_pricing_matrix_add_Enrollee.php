<?php
include_once 'layout/start.inc.php';
$res = array();

$validate = new Validation();
$globalMatrixEnrolleeCriteriaArr = array();

$globalMatrixEnrolleeCriteriaArr['enrolleeType'] = $enrolleeType = !empty($_POST['enrolleeType']) ? $_POST['enrolleeType'] : '';
$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();
$globalMatrixEnrolleeCriteriaArr['price_control'] = $price_control_enrollee = !empty($_POST['price_control_enrollee']) ? $_POST['price_control_enrollee'] : array();
$globalMatrixEnrolleeCriteriaArr['matrixID'] = $matrixID = !empty($_POST['matrixID']) ? $_POST['matrixID'] : '';
$globalMatrixEnrolleeCriteriaArr['keyID'] = $keyID = !empty($_POST['keyID']) ? $_POST['keyID'] : '';
$globalMatrixEnrolleeCriteriaArr['enrolleeMatrix'] = $enrolleeMatrix = !empty($_POST['enrolleeMatrix']) ? $_POST['enrolleeMatrix'] : '';
$globalMatrixEnrolleeCriteriaArr['matrixPlanType'] = '';

$age_from = isset($_POST['age_from_Enrollee'])?$_POST['age_from_Enrollee']:"";
$age_to = isset($_POST['age_to_Enrollee'])?$_POST['age_to_Enrollee']:"";
$globalMatrixEnrolleeCriteriaArr[1] = (isset($age_from) && isset($age_to)) ? $age_from." To ".$age_to : '' ;
$globalMatrixEnrolleeCriteriaArr[2] = $state = !empty($_POST['state_Enrollee'])?$_POST['state_Enrollee']:"";
$globalMatrixEnrolleeCriteriaArr[4] = $gender = !empty($_POST['gender_Enrollee'])?$_POST['gender_Enrollee']:"";

$height_by = !empty($_POST['height_by_Enrollee'])?$_POST['height_by_Enrollee']:"";
$height_feet = !empty($_POST['height_feet_Enrollee'])?$_POST['height_feet_Enrollee']:0;
$height_inch = !empty($_POST['height_inch_Enrollee'])?$_POST['height_inch_Enrollee']:0;
$height_feet_to = !empty($_POST['height_feet_to_Enrollee'])?$_POST['height_feet_to_Enrollee']:0;
$height_inch_to = !empty($_POST['height_inch_to_Enrollee'])?$_POST['height_inch_to_Enrollee']:0;

$globalMatrixEnrolleeCriteriaArr[7] = $height_feet."Ft ".$height_inch."In".($height_by=="Range" ? " To ".$height_feet_to."Ft ".$height_inch_to."In" : '');
$weight_by = !empty($_POST['weight_by_Enrollee'])?$_POST['weight_by_Enrollee']:"";
$weight = !empty($_POST['weight_Enrollee'])?$_POST['weight_Enrollee']:0;
$weight_to = !empty($_POST['weight_to_Enrollee'])?$_POST['weight_to_Enrollee']:0;
$globalMatrixEnrolleeCriteriaArr[8] = $weight.($weight_by=="Range" ? " To ".$weight_to : '');

$globalMatrixEnrolleeCriteriaArr[5] = $smoking_status = !empty($_POST['smoking_status_Enrollee'])?$_POST['smoking_status_Enrollee']:"";
$globalMatrixEnrolleeCriteriaArr[6] = $tobacco_status = !empty($_POST['tobacco_status_Enrollee'])?$_POST['tobacco_status_Enrollee']:"";

$no_of_children_by = !empty($_POST['no_of_children_by_Enrollee'])?$_POST['no_of_children_by_Enrollee']:"";
$no_of_children = !empty($_POST['no_of_children_Enrollee'])?$_POST['no_of_children_Enrollee']:0;
$no_of_children_to = !empty($_POST['no_of_children_to_Enrollee'])?$_POST['no_of_children_to_Enrollee']:0;

$globalMatrixEnrolleeCriteriaArr[9] = $no_of_children .($no_of_children_by=="Range" ? " To ".$no_of_children_to : '');
$globalMatrixEnrolleeCriteriaArr[3] = $zip = !empty($_POST['zip_Enrollee'])?$_POST['zip_Enrollee']:"";

$globalMatrixEnrolleeCriteriaArr[10] = $has_spouse = !empty($_POST['has_spouse_Enrollee'])?$_POST['has_spouse_Enrollee']:"";
$globalMatrixEnrolleeCriteriaArr[12] = $spouse_gender = !empty($_POST['spouse_gender_Enrollee'])?$_POST['spouse_gender_Enrollee']:"";
$spouse_age_from = !empty($_POST['spouse_age_from_Enrollee'])?$_POST['spouse_age_from_Enrollee']:'';
$spouse_age_to = !empty($_POST['spouse_age_to_Enrollee'])?$_POST['spouse_age_to_Enrollee']:'';
$globalMatrixEnrolleeCriteriaArr[11] = $spouse_age_from." To ".$spouse_age_to;
$spouse_height_feet = !empty($_POST['spouse_height_feet_Enrollee'])?$_POST['spouse_height_feet_Enrollee']:0;
$spouse_height_inch = !empty($_POST['spouse_height_inch_Enrollee'])?$_POST['spouse_height_inch_Enrollee']:0;
$globalMatrixEnrolleeCriteriaArr[15] = $spouse_height_feet."Ft ".$spouse_height_inch."In";
$spouse_weight = !empty($_POST['spouse_weight_Enrollee'])?$_POST['spouse_weight_Enrollee']:0;
$spouse_weight_type = !empty($_POST['spouse_weight_type_Enrollee'])?$_POST['spouse_weight_type_Enrollee']:"";
$globalMatrixEnrolleeCriteriaArr[16] = $spouse_weight." ".$spouse_weight_type;
$globalMatrixEnrolleeCriteriaArr[13] = $spouse_smoking_status = !empty($_POST['spouse_smoking_status_Enrollee'])?$_POST['spouse_smoking_status_Enrollee']:"";
$globalMatrixEnrolleeCriteriaArr[14] = $spouse_tobacco_status = !empty($_POST['spouse_tobacco_status_Enrollee'])?$_POST['spouse_tobacco_status_Enrollee']:"";

$globalMatrixEnrolleeCriteriaArr[17] = $benefit_amount = !empty($_POST['benefit_amount_Enrollee'])?$_POST['benefit_amount_Enrollee']:0;
$globalMatrixEnrolleeCriteriaArr[18] = $in_patient_benefit = !empty($_POST['in_patient_benefit_Enrollee'])?$_POST['in_patient_benefit_Enrollee']:0;
$globalMatrixEnrolleeCriteriaArr[19] = $out_patient_benefit = !empty($_POST['out_patient_benefit_Enrollee'])?$_POST['out_patient_benefit_Enrollee']:0;
$globalMatrixEnrolleeCriteriaArr[20] = $monthly_income = !empty($_POST['monthly_income_Enrollee'])?$_POST['monthly_income_Enrollee']:0;
// $globalMatrixEnrolleeCriteriaArr[21] = $benefit_percentage = !empty($_POST['benefit_percentage_Enrollee'])?$_POST['benefit_percentage_Enrollee']:0;

$pricing_matrix_group_Enrollee = !empty($_POST['pricing_matrix_group_Enrollee'])?$_POST['pricing_matrix_group_Enrollee']:array();
$globalMatrixEnrolleeCriteriaArr['matrix_group'] = !empty($pricing_matrix_group_Enrollee) ? $pricing_matrix_group_Enrollee : '';
$pricing_matrix_price = !empty($_POST['pricing_matrix_price_Enrollee'])?$_POST['pricing_matrix_price_Enrollee']:array();
if(!empty($globalMatrixEnrolleeCriteriaArr['matrix_group'])){
	foreach($globalMatrixEnrolleeCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixEnrolleeCriteriaArr['tmp_price_arr'][$pkey]['RetailPrice'] = checkIsset($pricing_matrix_price[$pkey]['Retail']);
		$globalMatrixEnrolleeCriteriaArr['tmp_price_arr'][$pkey]['NonCommissionablePrice'] = checkIsset($pricing_matrix_price[$pkey]['NonCommissionable']);
		$globalMatrixEnrolleeCriteriaArr['tmp_price_arr'][$pkey]['CommissionablePrice'] = checkIsset($pricing_matrix_price[$pkey]['Commissionable']);
	}
}
$pricing_matrix_effective_date = !empty($_POST['pricing_matrix_effective_date_Enrollee'])?$_POST['pricing_matrix_effective_date_Enrollee']:array();
if(!empty($globalMatrixEnrolleeCriteriaArr['matrix_group'])){
	foreach($globalMatrixEnrolleeCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixEnrolleeCriteriaArr['tmp_price_arr'][$pkey]['pricing_matrix_effective_date'] = checkIsset($pricing_matrix_effective_date[$pkey]);
	}
}
$pricing_matrix_termination_date = !empty($_POST['pricing_matrix_termination_date_Enrollee'])?$_POST['pricing_matrix_termination_date_Enrollee']:array();
if(!empty($globalMatrixEnrolleeCriteriaArr['matrix_group'])){
	foreach($globalMatrixEnrolleeCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixEnrolleeCriteriaArr['tmp_price_arr'][$pkey]['pricing_matrix_termination_date'] = checkIsset($pricing_matrix_termination_date[$pkey]);
	}
}
$newPricingMatrixOnRenewals = !empty($_POST['newPricingMatrixOnRenewals_Enrollee'])?$_POST['newPricingMatrixOnRenewals_Enrollee']:array();
if(!empty($globalMatrixEnrolleeCriteriaArr['matrix_group'])){
	foreach($globalMatrixEnrolleeCriteriaArr['matrix_group'] as $pkey => $pval){
		$globalMatrixEnrolleeCriteriaArr['tmp_price_arr'][$pkey]['newPricingMatrixOnRenewals'] = checkIsset($newPricingMatrixOnRenewals[$pkey]);
	}
}
$globalMatrixEnrolleeCriteriaArr['allowPricingUpdate'] = $allowPricingUpdate = !empty($_POST['allowPricingUpdate']) ? $_POST['allowPricingUpdate'] : false;
$globalMatrixEnrolleeCriteriaArr['pricingDataDisabled'] = $pricingDataDisabled_Enrollee = !empty($_POST['pricingDataDisabled_Enrollee']) ? $_POST['pricingDataDisabled_Enrollee'] : 'N';
if((!$allowPricingUpdate) || $pricingDataDisabled_Enrollee=='Y'){

	if(!$allowPricingUpdate){
		$globalMatrixEnrolleeCriteriaArr['enrolleeType'] = $enrolleeType = !empty($_POST['allow_enrolleeType']) ? $_POST['allow_enrolleeType'] : '';

		$globalMatrixEnrolleeCriteriaArr['price_control'] = $price_control_enrollee = !empty($_POST['allow_price_control_enrollee']) ? $_POST['allow_price_control_enrollee'] : array();
	}


	$globalMatrixEnrolleeCriteriaArr['enrolleeMatrix'] = $enrolleeMatrix = !empty($_POST['allow_enrolleeMatrix']) ? $_POST['allow_enrolleeMatrix'] : '';

	$age_from = isset($_POST['allow_age_from_Enrollee'])?$_POST['allow_age_from_Enrollee']:'';
	$age_to = isset($_POST['allow_age_to_Enrollee'])?$_POST['allow_age_to_Enrollee']:'';
	$globalMatrixEnrolleeCriteriaArr[1] = (isset($age_from) && isset($age_to)) ? $age_from." To ".$age_to : '';
	$globalMatrixEnrolleeCriteriaArr[2] = $state = !empty($_POST['allow_state_Enrollee'])?$_POST['allow_state_Enrollee']:"";
	$globalMatrixEnrolleeCriteriaArr[4] = $gender = !empty($_POST['allow_gender_Enrollee'])?$_POST['allow_gender_Enrollee']:"";

	$height_by = !empty($_POST['allow_height_by_Enrollee'])?$_POST['allow_height_by_Enrollee']:"";
	$height_feet = !empty($_POST['allow_height_feet_Enrollee'])?$_POST['allow_height_feet_Enrollee']:0;
	$height_inch = !empty($_POST['allow_height_inch_Enrollee'])?$_POST['allow_height_inch_Enrollee']:0;
	$height_feet_to = !empty($_POST['allow_height_feet_to_Enrollee'])?$_POST['allow_height_feet_to_Enrollee']:0;
	$height_inch_to = !empty($_POST['allow_height_inch_to_Enrollee'])?$_POST['allow_height_inch_to_Enrollee']:0;
	$globalMatrixEnrolleeCriteriaArr[7] = $height_feet."Ft ".$height_inch."In".($height_by=="Range" ? " To ".$height_feet_to."Ft ".$height_inch_to."In" : '');

	$weight_by = !empty($_POST['allow_weight_by_Enrollee'])?$_POST['allow_weight_by_Enrollee']:"";
	$weight = !empty($_POST['allow_weight_Enrollee'])?$_POST['allow_weight_Enrollee']:0;
	$weight_to = !empty($_POST['allow_weight_to_Enrollee'])?$_POST['allow_weight_to_Enrollee']:0;
	$globalMatrixEnrolleeCriteriaArr[8] = $weight.($weight_by=="Range" ? " To ".$weight_to : '');

	$globalMatrixEnrolleeCriteriaArr[5] = $smoking_status = !empty($_POST['allow_smoking_status_Enrollee'])?$_POST['allow_smoking_status_Enrollee']:"";
	$globalMatrixEnrolleeCriteriaArr[6] = $tobacco_status = !empty($_POST['allow_tobacco_status_Enrollee'])?$_POST['allow_tobacco_status_Enrollee']:"";

	$no_of_children_by = !empty($_POST['allow_no_of_children_by_Enrollee'])?$_POST['allow_no_of_children_by_Enrollee']:"";
	$no_of_children = !empty($_POST['allow_no_of_children_Enrollee'])?$_POST['allow_no_of_children_Enrollee']:0;
	$no_of_children_to = !empty($_POST['allow_no_of_children_to_Enrollee'])?$_POST['allow_no_of_children_to_Enrollee']:0;
	$globalMatrixEnrolleeCriteriaArr[9] = $no_of_children .($no_of_children_by=="Range" ? " To ".$no_of_children_to : '');
	$globalMatrixEnrolleeCriteriaArr[3] = $zip = !empty($_POST['allow_zip_Enrollee'])?$_POST['allow_zip_Enrollee']:"";

	$globalMatrixEnrolleeCriteriaArr[10] = $has_spouse = !empty($_POST['allow_has_spouse_Enrollee'])?$_POST['allow_has_spouse_Enrollee']:"";
	$globalMatrixEnrolleeCriteriaArr[12] = $spouse_gender = !empty($_POST['allow_spouse_gender_Enrollee'])?$_POST['allow_spouse_gender_Enrollee']:"";
	$spouse_age_from = !empty($_POST['allow_spouse_age_from_Enrollee'])?$_POST['allow_spouse_age_from_Enrollee']:'';
	$spouse_age_to = !empty($_POST['allow_spouse_age_to_Enrollee'])?$_POST['allow_spouse_age_to_Enrollee']:'';
	$globalMatrixEnrolleeCriteriaArr[11] = $spouse_age_from." To ".$spouse_age_to;

	$spouse_height_feet = !empty($_POST['allow_spouse_height_feet_Enrollee'])?$_POST['allow_spouse_height_feet_Enrollee']:0;
	$spouse_height_inch = !empty($_POST['allow_spouse_height_inch_Enrollee'])?$_POST['allow_spouse_height_inch_Enrollee']:0;
	$globalMatrixEnrolleeCriteriaArr[15] = $spouse_height_feet."Ft ".$spouse_height_inch."In";
	$spouse_weight = !empty($_POST['allow_spouse_weight_Enrollee'])?$_POST['allow_spouse_weight_Enrollee']:0;
	$spouse_weight_type = !empty($_POST['allow_spouse_weight_type_Enrollee'])?$_POST['allow_spouse_weight_type_Enrollee']:"";
	$globalMatrixEnrolleeCriteriaArr[16] = $spouse_weight." ".$spouse_weight_type;
	$globalMatrixEnrolleeCriteriaArr[13] = $spouse_smoking_status = !empty($_POST['allow_spouse_smoking_status_Enrollee'])?$_POST['allow_spouse_smoking_status_Enrollee']:"";
	$globalMatrixEnrolleeCriteriaArr[14] = $spouse_tobacco_status = !empty($_POST['allow_spouse_tobacco_status_Enrollee'])?$_POST['allow_spouse_tobacco_status_Enrollee']:"";

	$globalMatrixEnrolleeCriteriaArr[17] = $benefit_amount = !empty($_POST['allow_benefit_amount_Enrollee'])?$_POST['allow_benefit_amount_Enrollee']:0;
	$globalMatrixEnrolleeCriteriaArr[18] = $in_patient_benefit = !empty($_POST['allow_in_patient_benefit_Enrollee'])?$_POST['allow_in_patient_benefit_Enrollee']:0;
	$globalMatrixEnrolleeCriteriaArr[19] = $out_patient_benefit = !empty($_POST['allow_out_patient_benefit_Enrollee'])?$_POST['allow_out_patient_benefit_Enrollee']:0;
	$globalMatrixEnrolleeCriteriaArr[20] = $monthly_income = !empty($_POST['allow_monthly_income_Enrollee'])?$_POST['allow_monthly_income_Enrollee']:0;
	// $globalMatrixEnrolleeCriteriaArr[21] = $benefit_percentage = !empty($_POST['allow_benefit_percentage_Enrollee'])?$_POST['allow_benefit_percentage_Enrollee']:0;
}

$validate->string(array('required' => true, 'field' => 'enrolleeMatrix', 'value' => $enrolleeMatrix), array('required' => 'Enrollee is required'));



$price_control = array();

if(!empty($price_control_enrollee[$enrolleeMatrix])){
	foreach ($price_control_enrollee[$enrolleeMatrix] as $keyArr => $value) {
		array_push($price_control,$value);
	}	
}
if(!empty($enrolleeType)){
	foreach ($enrolleeType as $key => $value) {
		if($value != 'All' && empty($price_control_enrollee[$value])){
			$validate->setError("price_control_enrollee_".$value,"Pricing Criteria is required");
		}
	}
}else{
	$validate->setError("enrolleeType","Select Enrollee");
}
	
if(in_array("1", $price_control)){
	$validate->string(array('required' => true, 'field' => 'age_Enrollee', 'value' => $age_from), array('required' => 'Age is Required'));
	$validate->string(array('required' => true, 'field' => 'age_Enrollee', 'value' => $age_to), array('required' => 'Age is Required'));

	if (!$validate->getError('age_Enrollee')) {
		if($age_to < $age_from){
			$validate->setError("age_Enrollee","Select Valid Age");
		}
	}
}else{
	$age_from = "";
	$age_to = "";
}

if(in_array("2", $price_control)){
	$validate->string(array('required' => true, 'field' => 'state_Enrollee', 'value' => $state), array('required' => 'Select State'));
}else{
	$state = "";
}

if(in_array("3", $price_control)){
	$validate->string(array('required' => true, 'field' => 'zip_Enrollee', 'value' => $zip), array('required' => 'Zipcode is required'));
}else{
	$zip = "";
}

if(in_array("4", $price_control)){
	$validate->string(array('required' => true, 'field' => 'gender_Enrollee', 'value' => $gender), array('required' => 'Select Gender'));
}else{
	$gender = "";
}
if(in_array("5", $price_control)){
	$validate->string(array('required' => true, 'field' => 'smoking_status_Enrollee', 'value' => $smoking_status), array('required' => 'Select Smoking Status'));
}else{
	$smoking_status = "";
}

if(in_array("6", $price_control)){
	$validate->string(array('required' => true, 'field' => 'tobacco_status_Enrollee', 'value' => $tobacco_status), array('required' => 'Select Tobacco Status'));
}else{
	$tobacco_status = "";
}

if(in_array("7", $price_control)){
	$validate->string(array('required' => true, 'field' => 'height_Enrollee', 'value' => $height_by), array('required' => 'Height By is required'));
	$validate->string(array('required' => true, 'field' => 'height_Enrollee', 'value' => $height_feet), array('required' => 'Height Feet is required'));
	$validate->string(array('required' => true, 'field' => 'height_Enrollee', 'value' => $height_inch), array('required' => 'Height Inch is required'));

	if($height_by=="Range"){
		$validate->string(array('required' => true, 'field' => 'height_Enrollee', 'value' => $height_feet_to), array('required' => 'To Height Feet is required'));
		$validate->string(array('required' => true, 'field' => 'height_Enrollee', 'value' => $height_inch_to), array('required' => 'To Height Inch is required =>'.$height_inch_to));
	}
}else{
	$height_by = "";
	$height_feet = 0;
	$height_inch = 0;
	$height_feet_to = 0;
	$height_inch_to = 0;
}

if(in_array("8", $price_control)){
	$validate->string(array('required' => true, 'field' => 'weight_Enrollee', 'value' => $weight_by), array('required' => 'Weight By is required'));
	$validate->string(array('required' => true, 'field' => 'weight_Enrollee', 'value' => $weight), array('required' => 'Weight is required'));
	if($weight_by=="Range"){
		$validate->string(array('required' => true, 'field' => 'weight_Enrollee', 'value' => $weight_to), array('required' => 'To Weight is required'));
	}
}else{
	$weight_by = "";
	$weight = 0;
	$weight_to = 0;
}

if(in_array("9", $price_control)){
	$validate->string(array('required' => true, 'field' => 'no_of_children_Enrollee', 'value' => $no_of_children_by), array('required' => 'Children By is required'));
	$validate->string(array('required' => true, 'field' => 'no_of_children_Enrollee', 'value' => $no_of_children), array('required' => 'Select No of Children'));
	if($no_of_children_by=="Range"){
		$validate->string(array('required' => true, 'field' => 'no_of_children_Enrollee', 'value' => $no_of_children_to), array('required' => 'Select To No of Children'));
		
	}
}else{
	$no_of_children_by = "";
	$no_of_children = 0;
	$no_of_children_to = 0;
}

if(in_array("10", $price_control)){
	$validate->string(array('required' => true, 'field' => 'has_spouse_Enrollee', 'value' => $has_spouse), array('required' => 'Select Any Option'));
}else{
	$has_spouse = "";
}

if(in_array("11", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_age_Enrollee', 'value' => $spouse_age_from), array('required' => 'Spouse Age is Required'));
	$validate->string(array('required' => true, 'field' => 'spouse_age_Enrollee', 'value' => $spouse_age_to), array('required' => 'Spouse Age is Required'));
	if (!$validate->getError('spouse_age_Enrollee')) {
		if($spouse_age_to < $spouse_age_from){
			$validate->setError("spouse_age_Enrollee","Select Valid Age");
		}
	}
}else{
	$spouse_age_from = "";
	$spouse_age_to = "";
}

if(in_array("12", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_gender_Enrollee', 'value' => $spouse_gender), array('required' => 'Select Gender'));
}else{
	$spouse_gender = "";
}

if(in_array("13", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_smoking_status_Enrollee', 'value' => $spouse_smoking_status), array('required' => 'Select Smoking Status'));
}else{
	$spouse_smoking_status = "";
}

if(in_array("14", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_tobacco_status_Enrollee', 'value' => $spouse_tobacco_status), array('required' => 'Select Tobacco Status'));
}else{
	$spouse_tobacco_status = "";
}

if(in_array("15", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_height_Enrollee', 'value' => $spouse_height_feet), array('required' => 'Height is required'));
	$validate->string(array('required' => true, 'field' => 'spouse_height_Enrollee', 'value' => $spouse_height_inch), array('required' => 'Height is required'));
}else{
	$spouse_height_feet = 0;
	$spouse_height_inch = 0;
}

if(in_array("16", $price_control)){
	$validate->string(array('required' => true, 'field' => 'spouse_weight_Enrollee', 'value' => $spouse_weight), array('required' => 'Weight is required'));
	$validate->string(array('required' => true, 'field' => 'spouse_weight_Enrollee', 'value' => $spouse_weight_type), array('required' => 'Weight is required'));
}else{
	$spouse_weight = 0;
	$spouse_weight_type = '';
}

if(in_array("17", $price_control)){
	$validate->string(array('required' => true, 'field' => 'benefit_amount_Enrollee', 'value' => $benefit_amount), array('required' => 'Benefit Amount is required'));
}else{
	$benefit_amount = 0;
}

if(in_array("18", $price_control)){
	$validate->string(array('required' => true, 'field' => 'in_patient_benefit_Enrollee', 'value' => $in_patient_benefit), array('required' => 'In Patient Benefit Amount is required'));
}else{
	$in_patient_benefit = 0;
}

if(in_array("19", $price_control)){
	$validate->string(array('required' => true, 'field' => 'out_patient_benefit_Enrollee', 'value' => $out_patient_benefit), array('required' => 'In Patient Benefit Amount is required'));
}else{
	$out_patient_benefit = 0;
}

if(in_array("20", $price_control)){
	$validate->string(array('required' => true, 'field' => 'monthly_income_Enrollee', 'value' => $monthly_income), array('required' => 'Monthly income is required'));
}else{
	$monthly_income = 0;
}

// if(in_array("21", $price_control)){
// 	$validate->string(array('required' => true, 'field' => 'benefit_percentage_Enrollee', 'value' => $benefit_percentage), array('required' => 'Benefit Percentage is required'));
// }else{
// 	$benefit_percentage = 0;
// }

$i=1;
$maxCount=count($pricing_matrix_price);
foreach ($pricing_matrix_price as $matrix_group => $matrix_array) {
	
	$tmpName="Enrollee_";
	
	$validate->string(array('required' => true, 'field' => 'pricing_matrix_price_'.$tmpName.$matrix_group, 'value' => $pricing_matrix_price[$matrix_group]['Retail']), array('required' => 'Please Add Price'));
	$validate->string(array('required' => true, 'field' => 'pricing_matrix_price_'.$tmpName.$matrix_group, 'value' => $pricing_matrix_price[$matrix_group]['NonCommissionable']), array('required' => 'Please Add Price'));
	$validate->string(array('required' => true, 'field' => 'pricing_matrix_price_'.$tmpName.$matrix_group, 'value' => $pricing_matrix_price[$matrix_group]['Commissionable']), array('required' => 'Please Add Price'));

	if(str_replace(",","",$pricing_matrix_price[$matrix_group]['Retail']) <  str_replace(",","",$pricing_matrix_price[$matrix_group]['NonCommissionable'])){
		$validate->setError("pricing_matrix_price_".$tmpName.$matrix_group,"Enter Valid Price");
	}

	$validate->string(array('required' => true, 'field' => 'pricing_matrix_effective_date_'.$tmpName.$matrix_group, 'value' => $pricing_matrix_effective_date[$matrix_group]), array('required' => 'Add Effective Date'));
	

	if(!empty($pricing_matrix_effective_date[$matrix_group]) && !empty($pricing_matrix_termination_date[$matrix_group])){
		$effectiveDate=date('Y-m-d',strtotime($pricing_matrix_effective_date[$matrix_group]));
		$terminationDate=date('Y-m-d',strtotime($pricing_matrix_termination_date[$matrix_group]));
		$todayDate=date('Y-m-d');

		if(strtotime($effectiveDate) >= strtotime($terminationDate)){
			$validate->setError("pricing_matrix_termination_date_".$tmpName.$matrix_group,"Enter Valid Date");
		}
		if(strtotime($terminationDate) < strtotime($todayDate)){
			$validate->setError("pricing_matrix_termination_date_".$tmpName.$matrix_group,"Enter Valid Date");
		}
		if (!$validate->getError('pricing_matrix_termination_date_'.$tmpName.$matrix_group)) {
			list($mm, $dd, $yyyy) = explode('/', $pricing_matrix_termination_date[$matrix_group]);
			
			if (!checkdate($mm, $dd, $yyyy)) {
				$validate->setError("pricing_matrix_termination_date_".$tmpName.$matrix_group,"Enter Valid Date");
			}
		}
		if (!$validate->getError('pricing_matrix_effective_date_'.$tmpName.$matrix_group)) {
			list($mm, $dd, $yyyy) = explode('/', $pricing_matrix_effective_date[$matrix_group]);
			
			if (!checkdate($mm, $dd, $yyyy)) {
				$validate->setError("pricing_matrix_effective_date_".$tmpName.$matrix_group,"Enter Valid Date");
			}
		}
	}
	if($i > 1 && $i==$maxCount){
		if(empty($newPricingMatrixOnRenewals[$matrix_group])){
			$validate->setError('newPricingMatrixOnRenewals_'.$tmpName.$matrix_group,"Select Any Option");
		}
	}
	$i++;

}
$insertMatrix = true;
if($validate->isValid() && !empty($pricingMatrixKey)) {
	include_once dirname(__DIR__) .'/includes/function.class.php';
	$insertMatrix = functionsList::prdMatrixValidation($pricingMatrixKey,$globalMatrixEnrolleeCriteriaArr,'enrolleeMatrix');
}

if(!$insertMatrix){
	$validate->setError('global_matrix_arr_exits_enrollee',"Please change value, Criteria Already Exists.");
}
if ($validate->isValid()) {
	foreach ($pricing_matrix_price as $matrix_group => $matrix_array) {
		$pricingMatrixKey[$pricing_matrix_group_Enrollee[$matrix_group]][$matrix_group]=
			array(
				'keyID'=>$pricing_matrix_group_Enrollee[$matrix_group],
				'matrix_group'=>$pricing_matrix_group_Enrollee[$matrix_group],
				'id'=>0,
				'matrixPlanType'=>0,
				'enrolleeMatrix'=>$enrolleeMatrix,
				'1'=>array("matrix_value"=>(isset($age_from) && isset($age_to)) ? $age_from." To ".$age_to : '',
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
				'17'=>array("matrix_value"=>str_replace(",","",$benefit_amount)),
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