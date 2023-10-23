<?php
include_once 'layout/start.inc.php';
 
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$res = array();

function csvToArraywithFieldsHeader($filename)
{
    $csv = array_map('str_getcsv', file($filename));
    $headers = $csv[0];
    unset($csv[0]);
    $rowsWithKeys = [];
    foreach ($csv as $row) {
        $newRow = [];
        $is_not_empty=array();

        foreach ($headers as $k => $key) {
            if (trim($key) != "") {
                $newRow[$key] = $row[$k];
            }
            //if csv has empty row than it will not increase counter and not inserted in array code start
                if(!empty($row[$k])){
                    array_push($is_not_empty,"true");
                }else{
                    array_push($is_not_empty,"false");
                }
            //if csv has empty row than it will not increase counter and not inserted in array code end
        }
        
        //if csv row has any 1 column not empty than it will increase counter 
        if(in_array("true", $is_not_empty)){
            $rowsWithKeys[] = $newRow;
        }
    }
    return $rowsWithKeys;
}

function csvToArraywithFields($filename) {
  $csv = array_map('str_getcsv', file($filename));
  $headers = $csv[0];
  unset($csv[0]);
  $rowsWithKeys = [];

  foreach ($csv as $row) {

    $newRow = [];
    foreach ($headers as $k => $key) {
      if (trim($key) != "") {
        $newRow[$key] = $row[$k];
      }
    }
    $rowsWithKeys[] = $newRow;
  }

  return $rowsWithKeys;
}

$validate = new Validation();
$saveCSVAs = $_POST['saveCSVAs'];
$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();
$price_control = !empty($_POST['price_control']) ? $_POST['price_control'] : array();
$coverage_options = !empty($_POST['coverage_options']) ? $_POST['coverage_options'] : array();

$coverageOptionName = array();
if(!empty($coverage_options)){
    foreach ($coverage_options as $key => $value) {
        array_push($coverageOptionName,$prdPlanTypeArray[$value]['title']);
    }
}

$stored_file_name = $_POST['stored_file_name'];

$csv_file = $_FILES['csv_file'];
$csv_fileName = $_FILES['csv_file']['name'];
$csvTmpName = $csv_file['tmp_name'];


$matrixPlanTypeCSV = !empty($_POST['matrixPlanTypeCSV']) ? $_POST['matrixPlanTypeCSV'] : '';
$age_fromCSV = !empty($_POST['age_fromCSV'])?$_POST['age_fromCSV']:"";
$age_toCSV = !empty($_POST['age_toCSV'])?$_POST['age_toCSV']:"";
$stateCSV = !empty($_POST['stateCSV'])?$_POST['stateCSV']:"";
$genderCSV = !empty($_POST['genderCSV'])?$_POST['genderCSV']:"";

$height_byCSV = !empty($_POST['height_byCSV'])?$_POST['height_byCSV']:"";
$height_feetCSV = !empty($_POST['height_feetCSV'])?$_POST['height_feetCSV']:0;
$height_inchCSV = !empty($_POST['height_inchCSV'])?$_POST['height_inchCSV']:0;
$height_feet_toCSV = !empty($_POST['height_feet_toCSV'])?$_POST['height_feet_toCSV']:0;
$height_inch_toCSV = !empty($_POST['height_inch_toCSV'])?$_POST['height_inch_toCSV']:0;

$weight_byCSV = !empty($_POST['weight_byCSV'])?$_POST['weight_byCSV']:"";
$weightCSV = !empty($_POST['weightCSV'])?$_POST['weightCSV']:0;
$weight_toCSV = !empty($_POST['weight_toCSV'])?$_POST['weight_toCSV']:0;


$smoking_statusCSV = !empty($_POST['smoking_statusCSV'])?$_POST['smoking_statusCSV']:"";
$tobacco_statusCSV = !empty($_POST['tobacco_statusCSV'])?$_POST['tobacco_statusCSV']:"";

$no_of_children_byCSV = isset($_POST['no_of_children_byCSV'])?$_POST['no_of_children_byCSV']:"";
$no_of_childrenCSV = isset($_POST['no_of_childrenCSV'])?$_POST['no_of_childrenCSV']:0;
$no_of_children_toCSV = isset($_POST['no_of_children_toCSV'])?$_POST['no_of_children_toCSV']:0;

$zipCSV = !empty($_POST['zipCSV'])?$_POST['zipCSV']:"";

$has_spouseCSV = !empty($_POST['has_spouseCSV'])?$_POST['has_spouseCSV']:"";
$spouse_genderCSV = !empty($_POST['spouse_genderCSV'])?$_POST['spouse_genderCSV']:"";
$spouse_age_fromCSV = !empty($_POST['spouse_age_fromCSV'])?$_POST['spouse_age_fromCSV']:"";
$spouse_age_toCSV = !empty($_POST['spouse_age_toCSV'])?$_POST['spouse_age_toCSV']:"";

$spouse_height_feetCSV = !empty($_POST['spouse_height_feetCSV'])?$_POST['spouse_height_feetCSV']:"";
$spouse_height_inchCSV = !empty($_POST['spouse_height_inchCSV'])?$_POST['spouse_height_inchCSV']:"";
$spouse_weightCSV = !empty($_POST['spouse_weightCSV'])?$_POST['spouse_weightCSV']:"";
$spouse_weight_typeCSV = !empty($_POST['spouse_weight_typeCSV'])?$_POST['spouse_weight_typeCSV']:"";
$spouse_smoking_statusCSV = !empty($_POST['spouse_smoking_statusCSV'])?$_POST['spouse_smoking_statusCSV']:"";
$spouse_tobacco_statusCSV = !empty($_POST['spouse_tobacco_statusCSV'])?$_POST['spouse_tobacco_statusCSV']:"";

$pricing_matrix_priceCSVRetail = !empty($_POST['pricing_matrix_priceCSVRetail']) ? $_POST['pricing_matrix_priceCSVRetail'] : '';
$pricing_matrix_priceCSVNonCommissionable = !empty($_POST['pricing_matrix_priceCSVNonCommissionable']) ? $_POST['pricing_matrix_priceCSVNonCommissionable'] : '';
$pricing_matrix_priceCSVCommissionable = !empty($_POST['pricing_matrix_priceCSVCommissionable']) ? $_POST['pricing_matrix_priceCSVCommissionable'] : '';

$pricing_matrix_effective_dateCSV = !empty($_POST['pricing_matrix_effective_dateCSV'])?$_POST['pricing_matrix_effective_dateCSV']:'';
$pricing_matrix_termination_dateCSV = !empty($_POST['pricing_matrix_termination_dateCSV'])?$_POST['pricing_matrix_termination_dateCSV']:'';
$newPricingMatrixOnRenewalsCSV = !empty($_POST['newPricingMatrixOnRenewalsCSV'])?$_POST['newPricingMatrixOnRenewalsCSV']:'';

$benefit_amountCSV = !empty($_POST['benefit_amountCSV'])?$_POST['benefit_amountCSV']:'';
$in_patient_benefitCSV = !empty($_POST['in_patient_benefitCSV'])?$_POST['in_patient_benefitCSV']:'';
$out_patient_benefitCSV = !empty($_POST['out_patient_benefitCSV'])?$_POST['out_patient_benefitCSV']:'';
$monthly_incomeCSV = !empty($_POST['monthly_incomeCSV'])?$_POST['monthly_incomeCSV']:'';
// $benefit_percentageCSV = !empty($_POST['benefit_percentageCSV'])?$_POST['benefit_percentageCSV']:'';


$status = 'Pending Contract';

if ($saveCSVAs == 'uploadCSV') {
    if ($csv_file['name'] != '') {
        $allowed_ext = array('csv');
        
        $Ext=array_reverse(explode(".", $csv_fileName));
        $file_ext = strtolower($Ext[0]);
        $allowed_extensions = '*.csv';
        $allowed_file_size = '10485760';
        $size_in_mb = "10";
        $vmFileSize = $csv_file['size'];

        if (!in_array($file_ext, $allowed_ext)) {
            $validate->setError('csv_file', "Only " . $allowed_extensions . " file format allowed");
        } else if ($vmFileSize > $allowed_file_size) {
            $validate->setError('csv_file', "Maximum " . $size_in_mb . " MB file size allowed");
        }
    } else {
        $validate->setError("csv_file", "Upload Pricing Matrix CSV File");
    }
} else {

    if ($csv_file['name'] != '') {
        
        $allowed_ext = array('csv');
        $Ext=array_reverse(explode(".", $csv_fileName));
        $file_ext = strtolower($Ext[0]);
        $allowed_extensions = '*.csv';
        $allowed_file_size = '10485760';
        $size_in_mb = "10";
        $vmFileSize = $csv_file['size'];


        if (!in_array($file_ext, $allowed_ext)) {
            $validate->setError('csv_file', "Only " . $allowed_extensions . " file format allowed");
        } else if ($vmFileSize > $allowed_file_size) {
            $validate->setError('csv_file', "Maximum " . $size_in_mb . " MB file size allowed");
        }
        
        $validate->string(array('required' => true, 'field' => 'matrixPlanTypeCSV', 'value' => $matrixPlanTypeCSV), array('required' => 'Plan Tier is required'));

        if(empty($price_control)){
            $validate->setError("price_control","Pricing Criteria is required");
        }

        if(in_array("1", $price_control)){
            $validate->string(array('required' => true, 'field' => 'age_fromCSV', 'value' => $age_fromCSV), array('required' => 'Age From is required'));
            $validate->string(array('required' => true, 'field' => 'age_toCSV', 'value' => $age_toCSV), array('required' => 'Age To is required'));
        }if(in_array("2", $price_control)){
            $validate->string(array('required' => true, 'field' => 'stateCSV', 'value' => $stateCSV), array('required' => 'State is required'));
        }if(in_array("3", $price_control)){
            $validate->string(array('required' => true, 'field' => 'zipCSV', 'value' => $zipCSV), array('required' => 'Zipcode is required'));
        }if(in_array("4", $price_control)){
            $validate->string(array('required' => true, 'field' => 'genderCSV', 'value' => $genderCSV), array('required' => 'Gender is required'));
        }if(in_array("5", $price_control)){
            $validate->string(array('required' => true, 'field' => 'smoking_statusCSV', 'value' => $smoking_statusCSV), array('required' => 'Smoking Status is required'));
        }if(in_array("6", $price_control)){
            $validate->string(array('required' => true, 'field' => 'tobacco_statusCSV', 'value' => $tobacco_statusCSV), array('required' => 'Tobacco Status is required'));
        }if(in_array("7", $price_control)){
            $validate->string(array('required' => true, 'field' => 'height_byCSV', 'value' => $height_byCSV), array('required' => 'Height By is required'));
            $validate->string(array('required' => true, 'field' => 'height_feetCSV', 'value' => $height_feetCSV), array('required' => 'Height Feet is required'));
            $validate->string(array('required' => true, 'field' => 'height_inchCSV', 'value' => $height_inchCSV), array('required' => 'Height Inch is required'));
        }if(in_array("8", $price_control)){
            $validate->string(array('required' => true, 'field' => 'weight_byCSV', 'value' => $weight_byCSV), array('required' => 'Weight By is required'));
            $validate->string(array('required' => true, 'field' => 'weightCSV', 'value' => $weightCSV), array('required' => 'Weight is required'));
        }if(in_array("9", $price_control)){
            $validate->string(array('required' => true, 'field' => 'no_of_children_byCSV', 'value' => $no_of_children_byCSV), array('required' => 'Children By is required'));
            $validate->string(array('required' => true, 'field' => 'no_of_childrenCSV', 'value' => $no_of_childrenCSV), array('required' => 'Select No of Children'));
        }if(in_array("10", $price_control)){
            $validate->string(array('required' => true, 'field' => 'has_spouseCSV', 'value' => $has_spouseCSV), array('required' => 'Select Has Spouse'));
        }if(in_array("11", $price_control)){
            $validate->string(array('required' => true, 'field' => 'spouse_age_fromCSV', 'value' => $spouse_age_fromCSV), array('required' => 'Spouse Age From is Required'));
            $validate->string(array('required' => true, 'field' => 'spouse_age_toCSV', 'value' => $spouse_age_toCSV), array('required' => 'Spouse Age To is Required'));
        }if(in_array("12", $price_control)){
            $validate->string(array('required' => true, 'field' => 'spouse_genderCSV', 'value' => $spouse_genderCSV), array('required' => 'Gender is required'));
        }if(in_array("13", $price_control)){
            $validate->string(array('required' => true, 'field' => 'spouse_smoking_statusCSV', 'value' => $spouse_smoking_statusCSV), array('required' => 'Spouse Smoking Status is required'));
        }if(in_array("14", $price_control)){
            $validate->string(array('required' => true, 'field' => 'spouse_tobacco_statusCSV', 'value' => $spouse_tobacco_statusCSV), array('required' => 'Spouse Tobacco Status is required'));
        }if(in_array("15", $price_control)){
            $validate->string(array('required' => true, 'field' => 'spouse_height_feetCSV', 'value' => $spouse_height_feetCSV), array('required' => 'Spouse Height Feet is required'));
            $validate->string(array('required' => true, 'field' => 'spouse_height_inchCSV', 'value' => $spouse_height_inchCSV), array('required' => 'Spouse Height Inch is required'));
        }if(in_array("16", $price_control)){
            $validate->string(array('required' => true, 'field' => 'spouse_weightCSV', 'value' => $spouse_weightCSV), array('required' => 'Spouse Weight is required'));
            $validate->string(array('required' => true, 'field' => 'spouse_weight_typeCSV', 'value' => $spouse_weight_typeCSV), array('required' => 'Spouse Weight Type is required'));
        }
        if(in_array("17", $price_control)){
            $validate->string(array('required' => true, 'field' => 'benefit_amountCSV', 'value' => $benefit_amountCSV), array('required' => 'Benefit Amount is required'));
        }
        if(in_array("18", $price_control)){
            $validate->string(array('required' => true, 'field' => 'in_patient_benefitCSV', 'value' => $in_patient_benefitCSV), array('required' => 'InPatient Benefit is required'));
        }
        if(in_array("19", $price_control)){
            $validate->string(array('required' => true, 'field' => 'out_patient_benefitCSV', 'value' => $out_patient_benefitCSV), array('required' => 'OutPatient Benefit is required'));
        }
        if(in_array("20", $price_control)){
            $validate->string(array('required' => true, 'field' => 'monthly_incomeCSV', 'value' => $monthly_incomeCSV), array('required' => 'Monthly Income is required'));
        }
        // if(in_array("21", $price_control)){
        //     $validate->string(array('required' => true, 'field' => 'benefit_percentageCSV', 'value' => $benefit_percentageCSV), array('required' => 'Benefit Percentage is required'));
        // }


        
        $validate->string(array('required' => true, 'field' => 'pricing_matrix_priceCSVRetail', 'value' => $pricing_matrix_priceCSVRetail), array('required' => 'Retail price is required'));
        $validate->string(array('required' => true, 'field' => 'pricing_matrix_priceCSVNonCommissionable', 'value' => $pricing_matrix_priceCSVNonCommissionable), array('required' => ' Non-Commissionable price is required'));
        $validate->string(array('required' => true, 'field' => 'pricing_matrix_priceCSVCommissionable', 'value' => $pricing_matrix_priceCSVCommissionable), array('required' => 'Commissionable price is required'));
        
        $validate->string(array('required' => true, 'field' => 'pricing_matrix_effective_dateCSV', 'value' => $pricing_matrix_effective_dateCSV), array('required' => 'Effective date is required'));
        
        
        
        $csv_file = $PRICE_MATRIX_CSV_DIR . $stored_file_name;
        $field_row = csvToArraywithFields($csv_file);
        
            if(!empty($field_row)){
                foreach ($field_row as $value) {
                    if (!$validate->getError('matrixPlanTypeCSV')){
                        
                        if(!in_array($value[$matrixPlanTypeCSV], $coverageOptionName)) {
                            $validate->setError("matrixPlanTypeCSV","Plan Not Found");
                        }
                    }

                    if (!$validate->getError('age_fromCSV') && !$validate->getError('age_toCSV')){
                        if(isset($value[$age_fromCSV]) && isset($value[$age_toCSV])){
                            if($value[$age_fromCSV] >= $value[$age_toCSV]) {
                                $validate->setError("age_fromCSV","Valid Age Is Required");
                            }
                        }
                    }

                    if (!$validate->getError('height_byCSV')){
                        if(isset($value[$height_byCSV])=="Range"){
                            $validate->string(array('required' => true, 'field' => 'height_feet_toCSV', 'value' => $height_feet_toCSV), array('required' => 'Height Feet is required'));
                            $validate->string(array('required' => true, 'field' => 'height_inch_toCSV', 'value' => $height_inch_toCSV), array('required' => 'Height Inch is required'));
                        }
                    }
                    if (!$validate->getError('weight_byCSV')){
                        if(isset($value[$weight_byCSV])=="Range"){
                            $validate->string(array('required' => true, 'field' => 'weight_toCSV', 'value' => $weight_toCSV), array('required' => 'Weight is required'));
                        }
                    }

                    if (!$validate->getError('no_of_children_byCSV')){
                        if(isset($value[$no_of_children_byCSV])=="Range"){
                            $validate->string(array('required' => true, 'field' => 'no_of_children_toCSV', 'value' => $no_of_children_toCSV), array('required' => 'Select No of Children'));
                        }
                    }

                    if (!$validate->getError('spouse_age_fromCSV') && !$validate->getError('spouse_age_toCSV')){
                        
                        if(isset($value[$spouse_age_fromCSV]) && isset($value[$spouse_age_toCSV])){
                            if($value[$spouse_age_fromCSV] >= $value[$spouse_age_toCSV]) {
                                $validate->setError("spouse_age_fromCSV","Valid Spouse Age Is Required");
                            }
                        }
                    }
                    if (!$validate->getError('pricing_matrix_priceCSVRetail') && !$validate->getError('pricing_matrix_priceCSVNonCommissionable')) {
                        if($value[$pricing_matrix_priceCSVRetail] < $value[$pricing_matrix_priceCSVNonCommissionable]){
                            $validate->setError("pricing_matrix_priceCSVRetail","Enter Valid Price");
                        }
                    }
                    if (!$validate->getError('pricing_matrix_effective_dateCSV')) {

                        if(!empty($value[$pricing_matrix_effective_dateCSV]) && !empty($value[$pricing_matrix_termination_dateCSV])){
                            $effectiveDate=date('Y-m-d',strtotime($value[$pricing_matrix_effective_dateCSV]));
                            $terminationDate=date('Y-m-d',strtotime($value[$pricing_matrix_termination_dateCSV]));
                            $todayDate=date('Y-m-d');

                            if(strtotime($effectiveDate) >= strtotime($terminationDate)){
                                $validate->setError("pricing_matrix_termination_dateCSV","Termination date not valid");
                            }
                            if(strtotime($terminationDate) < strtotime($todayDate)){
                                $validate->setError("pricing_matrix_termination_dateCSV","Termination date not valid");
                            }
                            if (!$validate->getError('pricing_matrix_termination_dateCSV')) {
                                list($mm, $dd, $yyyy) = explode('/', $value[$pricing_matrix_termination_dateCSV]);
                                
                                if (!checkdate($mm, $dd, $yyyy)) {
                                    $validate->setError("pricing_matrix_termination_dateCSV","Termination date not valid");
                                }
                            }
                            if (!$validate->getError('pricing_matrix_effective_dateCSV')) {
                                list($mm, $dd, $yyyy) = explode('/', $value[$pricing_matrix_effective_dateCSV]);
                                
                                if (!checkdate($mm, $dd, $yyyy)) {
                                    $validate->setError("pricing_matrix_effective_dateCSV","Effective date not valid");
                                }
                            }
                        }
                    }
                }
            }  
        
        
        if ($_POST['is_csv_uploaded'] == 'N') {
            $validate->setError('csv_fileUpload', "Please click on upload csv button and select file fileds");
        }
    } else {
        $validate->setError("csv_file", "Upload Pricing Matrix CSV File");
    }

}

if ($validate->isValid()) {

    if ($saveCSVAs != 'uploadCSV') {
        
        $field_row = csvToArraywithFieldsHeader($csvTmpName);
      
        //Pricing Matrix Uploaded From File Code Start
            $csv_file = $PRICE_MATRIX_CSV_DIR . $stored_file_name;
            $field_row = csvToArraywithFields($csv_file);
            $i=99;
            if(count($field_row) > 0){
                foreach ($field_row as $value) {
                    $matrix_group ="-".$i.rand(1000,2000);
                    $plan_type = $value[$matrixPlanTypeCSV];
                    if(strtolower($plan_type)=='member' || strtolower($plan_type)=='member only'){
                        $plan_type=1;
                    }else if(strtolower($plan_type)=='member + child(ren)' || strtolower($plan_type)=='member + children'){
                        $plan_type=2;
                    }else if(strtolower($plan_type)=='member + spouse'){
                        $plan_type=3;
                    }else if(strtolower($plan_type)=='family'){
                        $plan_type=4;
                    }else if(strtolower($plan_type)=='member + one'){
                        $plan_type=5;
                    }


                    $age_from = !empty($value[$age_fromCSV]) ? $value[$age_fromCSV] : 0;
                    $age_to = !empty($value[$age_toCSV]) ? $value[$age_toCSV] : 0;
                    $state = !empty($value[$stateCSV]) ? $value[$stateCSV] : '';
                    $zip = !empty($value[$zipCSV]) ? $value[$zipCSV] : '';
                    $gender = !empty($value[$genderCSV]) ? $value[$genderCSV] : '';

                    $smoking_status = !empty($value[$smoking_statusCSV]) ? strtoupper($value[$smoking_statusCSV]) : '';
                    $tobacco_status = !empty($value[$tobacco_statusCSV]) ? strtoupper($value[$tobacco_statusCSV]) : '';
                    $height_by = !empty($value[$height_byCSV]) ? $value[$height_byCSV] : '';
                    $height_feet = !empty($value[$height_feetCSV]) ? $value[$height_feetCSV] : '';
                    $height_inch = !empty($value[$height_inchCSV]) ? $value[$height_inchCSV] : '';
                    $height_feet_to = !empty($value[$height_feet_toCSV]) ? $value[$height_feet_toCSV] : '';
                    $height_inch_to = !empty($value[$height_inch_toCSV]) ? $value[$height_inch_toCSV] : '';
                    $weight_by = !empty($value[$weight_byCSV]) ? $value[$weight_byCSV] : '';
                    $weight = !empty($value[$weightCSV]) ? $value[$weightCSV] : '';
                    $weight_to = !empty($value[$weight_toCSV]) ? $value[$weight_toCSV] : '';
                    

                    $no_of_children_by = !empty($value[$no_of_children_byCSV]) ? $value[$no_of_children_byCSV] : '';
                    $no_of_children = !empty($value[$no_of_childrenCSV]) ? $value[$no_of_childrenCSV] : 0;
                    $no_of_children_to = !empty($value[$no_of_children_toCSV]) ? $value[$no_of_children_toCSV] : 0;

                    $has_spouse = !empty($value[$has_spouseCSV]) ? strtoupper($value[$has_spouseCSV]) : '';
                    $spouse_age_from = !empty($value[$spouse_age_fromCSV]) ? $value[$spouse_age_fromCSV] : 0;
                    $spouse_age_to = !empty($value[$spouse_age_toCSV]) ? $value[$spouse_age_toCSV] : 0;
                    $spouse_gender = !empty($value[$spouse_genderCSV]) ? $value[$spouse_genderCSV] : '';

                    $spouse_smoking_status = !empty($value[$spouse_smoking_statusCSV]) ? strtoupper($value[$spouse_smoking_statusCSV]) : '';
                    $spouse_tobacco_status = !empty($value[$spouse_tobacco_statusCSV]) ? strtoupper($value[$spouse_tobacco_statusCSV]) : '';
                    $spouse_height_feet = !empty($value[$spouse_height_feetCSV]) ? $value[$spouse_height_feetCSV] : '';
                    $spouse_height_inch = !empty($value[$spouse_height_inchCSV]) ? $value[$spouse_height_inchCSV] : '';
                    $spouse_weight = !empty($value[$spouse_weightCSV]) ? $value[$spouse_weightCSV] : '';
                    $spouse_weight_type = !empty($value[$spouse_weight_typeCSV]) ? strtolower($value[$spouse_weight_typeCSV]) : '';
                    
                    $benefit_amount = !empty($value[$benefit_amountCSV]) ? str_replace(array("$"),array(" "),$value[$benefit_amountCSV]) : '';
                    $in_patient_benefit = !empty($value[$in_patient_benefitCSV]) ? str_replace(array("$"),array(" "),$value[$in_patient_benefitCSV]) : '';
                    $out_patient_benefit = !empty($value[$out_patient_benefitCSV]) ? str_replace(array("$"),array(" "),$value[$out_patient_benefitCSV]) : '';
                    $monthly_income = !empty($value[$monthly_incomeCSV]) ? str_replace(array("$"),array(" "),$value[$monthly_incomeCSV]) : '';
                    // $benefit_percentage = !empty($value[$benefit_percentageCSV]) ? str_replace(array("$","%"),array(" "," "),$value[$benefit_percentageCSV]) : '';
                    
                    
                    $pricing_matrix_effective_date = !empty($value[$pricing_matrix_effective_dateCSV]) ? date($DATE_FORMAT,strtotime($value[$pricing_matrix_effective_dateCSV])) : '';
                    $pricing_matrix_termination_date = !empty($value[$pricing_matrix_termination_dateCSV]) ? date($DATE_FORMAT,strtotime($value[$pricing_matrix_termination_dateCSV])) : '';

                    $price = str_replace("$"," ",$value[$pricing_matrix_priceCSVRetail]);
                    $non_commissionable = str_replace("$"," ",$value[$pricing_matrix_priceCSVNonCommissionable]);
                    $commissionable = $price - $non_commissionable; 

                    $pricingMatrixKey[$matrix_group][$matrix_group]=
                        array(
                            'keyID'=>$matrix_group,
                            'id'=>0,
                            'matrixPlanType'=>$plan_type,
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
                            '17'=>array("matrix_value"=>$benefit_amount),
                            '18'=>array("matrix_value"=>$in_patient_benefit),
							'19'=>array("matrix_value"=>$out_patient_benefit),
							'20'=>array("matrix_value"=>$monthly_income),
							// '21'=>array("matrix_value"=>$benefit_percentage),
                            'RetailPrice'=>number_format($price,2,'.',''),
                            'NonCommissionablePrice'=>number_format($non_commissionable,2,'.',''),
                            'CommissionablePrice'=>number_format($commissionable,2,'.',''),
                            'pricing_matrix_effective_date'=>$pricing_matrix_effective_date,
                            'pricing_matrix_termination_date'=>$pricing_matrix_termination_date,
                            'newPricingMatrixOnRenewals'=> '',
                            'csv_file_name'=> $stored_file_name,
                        );
                        $i++;
                }
            }        
        
        $res['pricingMatrixKey'] = json_encode($pricingMatrixKey);
        $res['msg'] = "Pricing Matrix added Successfully";

        $res['status'] = "success";
    } else {
        //pre_print($_POST,FALSE);
        if (($handle = fopen($csvTmpName, "r")) !== FALSE) {
            if (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c = 0; $c < $num; $c++) {
                    $data[$c] . "<br />\n";

                }
            }

            fclose($handle);
        }

        $row = $data;
        $res['csv_data'] = $row;

        $name = basename($csv_fileName);
        move_uploaded_file($csvTmpName, $PRICE_MATRIX_CSV_DIR . '/' . time() . $name);
        $stored_file_name = time() . $name;

    }
} else {
    $res['status'] = "fail";
    $res['errors'] = $validate->getErrors();
}
$res['saveCSVAs']= $saveCSVAs;
$res['formType']= 'CSV';
$res['stored_file_name']= $stored_file_name;

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
