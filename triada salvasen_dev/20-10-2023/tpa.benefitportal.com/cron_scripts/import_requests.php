<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
include_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
include_once 'import_function.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
// error_reporting(E_ALL);

$MemberEnrollment = new MemberEnrollment();
$enrollDate = new enrollmentDate();
$function_list = new functionsList();
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$today_date=date('Y-m-d');

$check_already_running = $pdo->selectOne("SELECT * FROM import_requests WHERE status='Pending' AND is_running='Y' AND is_deleted='N'");
if (!empty($check_already_running)) {
    exit("Already Running");
}

$csv_sql = "SELECT * FROM import_requests WHERE status='Pending' AND is_running='N' AND is_deleted='N' LIMIT 0,3";
$csv_res = $pdo->select($csv_sql);

if(empty($csv_res)) {
    exit("No import request found");
}
if (count($csv_res) > 0) {
	foreach ($csv_res as $file_row) {
		$csv_where = array(
            "clause" => "id=:id",
            "params" => array(
                ":id" => $file_row['id']
            )
        );
        $pdo->update('import_requests',array('is_running' => 'Y'), $csv_where);

        $admin_row = array();
        if(!empty($file_row['admin_id'])) {
            $admin_row = $pdo->selectOne('SELECT * FROM admin WHERE id=:id',array(":id" => $file_row['admin_id']));
        }

        $csv_file = $CSV_DIR . $file_row['file_name'];

        $csv_file_rows = csvToArraywithFields($csv_file);

        // $total_processed_count = 0;
        // $total_failed_count = 0;
		// $total_counts = count($csv_file_rows);

        if(count($csv_file_rows) > 0){

        	$module_type = $file_row['module_type'];
			$import_type = $file_row['import_type'];
            $fields = json_decode($file_row['csv_columns'],true);
            unset($fields['save_as']);
            unset($fields['module_name']);
            unset($fields['import_action']);

            if($module_type == 'members' && $import_type == 'add_members'){

				$yesNoArr = [
					'Yes' => 'Y',
					'Y' => 'Y',
					'No' => 'N',
					'N' => 'N'
				];
				$relationArray = [
					'P' => 'Primary',
					'S' => 'Spouse',
					'C' => 'Child',
					'Primary' => 'Primary',
					'Spouse' => 'Spouse',
					'Child' => 'Child',
				];
				$benefitTierArray = [
					'EE' => 'Member Only',
					'ES' => 'Member + Spouse',
					'EC' => 'Member + Child(ren)',
					'EF' => 'Family',
				];
				$genderArr = [
					'M' => 'Male',
					'F' => 'Female',
					'Male'=> 'Male',
					'Female'=>'Female',
				];
				$member_arr = array();
				$beneficiaryArr = array();
				$policyIdArrMember = $policyIdArr = [];
				$total_records = 0;
				$total_processed_records = 0;
				$total_failed_records = 0;
				$custom_questions = $pdo->select("SELECT id,display_label,label from prd_enrollment_questions where questionType = 'Custom' and is_deleted = 'N' order by order_by"); 
	            // pre_print($csv_file_rows);
				$existsProduct = $existsMember = $existsAgent = [];
				$global_is_error = false;
				$globalProd = $checkEmail = [];
				$memberCount=0;
	            foreach ($csv_file_rows as $value) {
					
					$total_records++;
					$error_reporting_arr = [];
					$a = array_map('trim', array_keys($value));
					$b = array_map('trim', $value);
					$value = array_combine($a, $b);
					
					$value['RELATION'] = !empty($relationArray[ucfirst(strtolower($value['RELATION']))]) ? $relationArray[ucfirst(strtolower($value['RELATION']))] : '';

					$value['BENEFIT_TIER'] = !empty($benefitTierArray[$value['BENEFIT_TIER']]) ? $benefitTierArray[$value['BENEFIT_TIER']] : '';
					$value['GENDER'] = !empty($genderArr[ucfirst(strtolower($value['GENDER']))]) ? $genderArr[ucfirst(strtolower($value['GENDER']))] : '';
					$value['PRIMARY_ID'] = !empty($value['HOUSEHOLD_ID']) ? $value['HOUSEHOLD_ID'] : '';
					$errorArr = array('file_id' => $file_row['id'],'csv_columns' => json_encode($value),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()');
					if( empty($value['RELATION']) || !in_array(strtolower($value['RELATION']),array('primary','spouse','child'))){
						$total_failed_records++;
						$error_reporting_arr[] = "Valid Relation is required from P/Primary or S/Spouse or C/Child.";
						$errorArr['reason'] = implode(' or<br>',$error_reporting_arr);
						$pdo->insert("import_csv_log", $errorArr);
						$is_error = true;
						$total_failed_records++;
						if(!$global_is_error){
							$global_is_error = true;
						}
						continue;
					}

					$is_error = false;
					$sponsor_row = array();
					$enrollingmemberid=(empty($value['ENROLLING_AGENT_GROUP_ID']))?(empty($value['ENROLLING_GROUP_ID']))?'':$value['ENROLLING_GROUP_ID']:$value['ENROLLING_AGENT_GROUP_ID'];
					if(empty($enrollingmemberid)){
						$error_reporting_arr[] = "Agent ID/Group ID is empty";
						$is_error = true;
					}else{
						if(empty($existsAgent) || !in_array($enrollingmemberid,$existsAgent)){
							$sponsor_row = $pdo->selectOne("SELECT id,rep_id,type FROM customer WHERE rep_id = :rep_id",array(":rep_id" => $enrollingmemberid));
							$existsAgent[] = $enrollingmemberid;
							if(empty($sponsor_row['id'])){
								$is_error = true;
								$error_reporting_arr[] = "Agent ID/Group ID is not found.";
								$errorArr['reason'] = implode(' or<br>',$error_reporting_arr);
								$pdo->insert("import_csv_log", $errorArr);
								$total_failed_records++;
								if(!$global_is_error){
									$global_is_error = true;
								}
								continue;
							}
						}
					}

					// if($value['RELATION']){
					// 	$error_reporting_arr[] = "relation is required.";
					// 	$errorArr['reason'] = implode(' or<br>',$error_reporting_arr);
					// 	// $pdo->insert("import_csv_log", $errorArr);
					// 	$total_failed_records++;
					// 	$is_error = true;
					// 	if(!$global_is_error){
					// 		$global_is_error = true;
					// 	}
					// 	// continue;
					// } 
					
					// else if(!empty($value['RELATION']) && $value['RELATION'] != 'Primary'){
					// 	$error_reporting_arr[] = "Valid realtion (Primary) is required";
					// 	$errorArr['reason'] = implode(' or<br>',$error_reporting_arr);
					// 	$pdo->insert("import_csv_log", $errorArr);
					// 	$total_failed_records++;
					// 	$is_error = true;
					// 	continue;
					// }
					
					if(empty($value['FIRST_NAME'])){
						$error_reporting_arr[] = "First Name is empty";
						$is_error = true;
					}else{
						// if(!ctype_alpha($value['FIRST_NAME'])){
						// 	$error_reporting_arr[] = "First Name should be valid text.";
						// 	$is_error = true;
						// }
					}
					if(empty($value['LAST_NAME'])){
						$error_reporting_arr[] = "Last Name is empty";
						$is_error = true;
					}else{
						// if(!ctype_alpha($value['LAST_NAME'])){
						// 	$error_reporting_arr[] = "Last Name should be valid text.";
						// 	$is_error = true;
						// }
					}
					if(empty($value['PHONE_NUMBER']) && $value['RELATION'] == 'Primary'){
						$error_reporting_arr[] = "Phone Number is empty";
						$is_error = true;
					}else if(!empty($value['PHONE_NUMBER'])){

						$formatedPhone = phoneReplaceMain($value['PHONE_NUMBER']);
						preg_match('/(\d{3})(\d{3})(\d{4})/', $formatedPhone, $matches);
						if(strlen($formatedPhone) > 10){
							$error_reporting_arr[] = "Invalid Phone Number (10 Digit Required)";
							$is_error = true;
						}else if("{$matches[1]}-{$matches[2]}-{$matches[3]}" != $value['PHONE_NUMBER'] && "({$matches[1]}) {$matches[2]}-{$matches[3]}" != $value['PHONE_NUMBER']){
							$error_reporting_arr[] = "Phone number should be required XXX-XXX-XXXX OR (XXX) XXX-XXXX format and having only numbers";
							$is_error = true;
						}

						// if(!$is_error && $value['RELATION'] == 'Primary' && (empty($existsMember) || !in_array($value['PRIMARY_ID'],$existsMember))){
						// 	$selectPhone = "SELECT id,cell_phone FROM customer WHERE cell_phone=:cell_phone AND type='Customer' AND is_deleted='N'";
						// 	$existsMember[] = $value['PRIMARY_ID'];
						// 	$resultPhone = $pdo->selectOne($selectPhone, array(':cell_phone' => phoneReplaceMain($value['PHONE_NUMBER'])));
						// 	if (!empty($resultPhone)) {
						// 		$error_reporting_arr[] = "Phone Number is Already Exist";
						// 		$is_error = true;
						// 	}
						// }
					}
					if( empty($value['EMAIL_ADDRESS']) && $value['RELATION'] == 'Primary' ){
						$error_reporting_arr[] = "Email is empty";
						$is_error = true;
					}else if(!empty($value['EMAIL_ADDRESS'])){
						if(!filter_var($value['EMAIL_ADDRESS'], FILTER_VALIDATE_EMAIL)){
							$error_reporting_arr[] = "Email is Not Valid";
							$is_error = true;
						}

						if((empty($checkEmail) || !in_array($value['PRIMARY_ID'],$checkEmail))){
							$checkEmail[] = $value['PRIMARY_ID'];
							$selectEmail = "SELECT id,email FROM customer WHERE email=:email AND type='Customer' AND is_deleted='N'";
							$resultEmail = $pdo->selectOne($selectEmail, array(':email' => $value['EMAIL_ADDRESS']));

							if($resultEmail){
								$error_reporting_arr[] = "Email Already associate with other account";
								$is_error = true;
							}
						}
					}
					if(empty($value['ADDRESS']) && $value['RELATION'] == 'Primary' ){
						$error_reporting_arr[] = "Address is empty";
						$is_error = true;
					}
					if(empty($value['CITY']) && $value['RELATION'] == 'Primary'){
						$error_reporting_arr[] = "City is empty";
						$is_error = true;
					}
					if(empty($value['STATE']) && $value['RELATION'] == 'Primary'){
						$error_reporting_arr[] = "State is empty";
						$is_error = true;
					}
					if(empty($value['ZIP_CODE']) && $value['RELATION'] == 'Primary'){
						$error_reporting_arr[] = "Zip Code is empty";
						$is_error = true;
					}
					if(empty($value['DOB'])){
						$error_reporting_arr[] = "Birth date is empty";
						$is_error = true;
					}else{
						$seperator=(!strpos($value['DOB'],'/'))?(strpos($value['DOB'],'-'))?'-':false:'/';
						list($mm, $dd, $yyyy) = explode($seperator, $value['DOB']);
						if (!checkdate($mm, $dd, $yyyy)) {
							$error_reporting_arr[] = "Birth date is not valid";
							$is_error = true;
						}
					}
					if(empty($value['GENDER'])){
						$error_reporting_arr[] = "Gender should be M/Male or F/Female";
						$is_error = true;
					}
					// else if(!empty($value['GENDER'])){
					// 	$error_reporting_arr[] = "Gender should be M or F";
					// 	$is_error = true;
					// }

					
					if(!empty($sponsor_row['type']) && $sponsor_row['type'] == 'Agent' && $value['RELATION'] == 'Primary'){
						if(empty($value['BILLING_TYPE'])){
							$error_reporting_arr[] = "Billing Type is empty";
							$is_error = true;
						}
					}

					if(!empty($sponsor_row['type']) && $sponsor_row['type'] == 'Group' && $value['RELATION'] == 'Primary'){
						if(empty($value['CLASS'])){
							$error_reporting_arr[] = "Class is empty";
							$is_error = true;
						}

						if(empty($value['ENROLLEE_TYPE']) || !in_array(strtolower($value['ENROLLEE_TYPE']),array('new','renew','existing'))){
							$error_reporting_arr[] = "Enrollee Type is empty or required from New or Renew or Existing";
							$is_error = true;
						}

						if(empty($value['RELATIONSHIP_DATE'])){
							$error_reporting_arr[] = "Relationship Date is empty";
							$is_error = true;
						}else{
							$seperator=(!strpos($value['RELATIONSHIP_DATE'],'/'))?(strpos($value['RELATIONSHIP_DATE'],'-'))?'-':false:'/';
							list($mm, $dd, $yyyy) = explode($seperator, $value['RELATIONSHIP_DATE']);
							if (!checkdate($mm, $dd, $yyyy)) {
								$error_reporting_arr[] = "Relationship Date is not valid";
								$is_error = true;
							}
						}

						if(empty($value['GROUP_NAME'])){
							$error_reporting_arr[] = "Group Name is empty";
							$is_error = true;
						}

						

					}

					if(empty($value['PRODUCT_CATEGORY'])){
						$error_reporting_arr[] = "Product Category is empty";
						$is_error = true;
					}else if(!empty($value['PRODUCT_CATEGORY'])){
						$sqlCategory = "SELECT pc.id,pc.title FROM prd_main p JOIN prd_category pc ON(pc.id=p.category_id AND pc.is_deleted='N') WHERE p.is_deleted='N' AND p.product_code=:product_code  AND pc.title=:title";
						$category1 = $pdo->selectOne($sqlCategory,array(":product_code"=>$value['PRODUCT_ID'],':title'=>$value['PRODUCT_CATEGORY']));
						if(empty($category1['id'])){
							$error_reporting_arr[] = "Product Category is not found in system or not valid for product ".$value['PRODUCT_ID'];
							$is_error = true;
						}
					}

					if(empty($value['PRODUCT_ID'])){
						$error_reporting_arr[] = "Product ID is empty";
						// $pdo->insert("import_csv_log", $error_reporting_arr);
						$is_error = true;
					}else if(!empty($value['PRODUCT_ID'])){
						$prod = [];
						if(empty($existsProduct) || !in_array($value['PRODUCT_ID'],$existsProduct)){
							$prod = $pdo->selectOne("SELECT id,is_short_term_disablity_product,monthly_benefit_allowed,percentage_of_salary from prd_main where is_deleted='N' and product_code=:product_code",array(":product_code"=>$value['PRODUCT_ID']));
							$globalProd[$value['PRODUCT_ID']] = $prod;
						}
						if(empty($prod['id']) || in_array($value['PRODUCT_ID'],$existsProduct)){
							$error_reporting_arr[] = "Product ID is not valid";
							$is_error = true;
							$existsProduct[] = $value['PRODUCT_ID'];
						}
					}
					// else if(!empty($value['PRODUCT_ID']) && !ctype_alnum($value['PRODUCT_ID'])){
					// 	$error_reporting_arr[] = "Product Id should be Alphanumeric charecters.";
					// 	$is_error = true;
					// }

					if(empty($value['BENEFIT_TIER'])){
						$error_reporting_arr[] = "Benefit Tier is empty or Valid Benefit Tier is from EE or ES or EC or EF";
						$is_error = true;
					}

					if(empty($value['EFFECTIVE_DATE'])){
						$error_reporting_arr[] = "Effective Date is empty";
						$is_error = true;
					}else if(!empty($value['EFFECTIVE_DATE'])){
						$seperator=(!strpos($value['EFFECTIVE_DATE'],'/'))?(strpos($value['EFFECTIVE_DATE'],'-'))?'-':false:'/';
						list($mm, $dd, $yyyy) = explode($seperator, $value['EFFECTIVE_DATE']);

						if (!checkdate($mm, $dd, $yyyy)) {
							$error_reporting_arr[] = "Valid Effective Date is required";
							$is_error = true;
						}
					}

					if(!empty($value['ACTIVE_MEMBER_SINCE_DATE'])){
						list($mm, $dd, $yyyy) = explode('/', $value['ACTIVE_MEMBER_SINCE_DATE']);
						if (!checkdate($mm, $dd, $yyyy)) {
							$error_reporting_arr[] = "Valid active member since Date is required";
							$is_error = true;
						}
					}

					if(!empty($value['HEIGHT']) && (!is_numeric($value['HEIGHT']) || !strpos($value['HEIGHT'], '.')) ){
						$error_reporting_arr[] = "Valid HEIGHT value should be in decimal values";
						$is_error = true;
					}

					if(!empty($value['WEIGHT']) && (!is_numeric($value['WEIGHT']) || strpos($value['WEIGHT'], '.')) ){
						$error_reporting_arr[] = "Valid WEIGHT value should be whole number";
						$is_error = true;
					}
					
					if(!empty($value['SMOKE']) && empty($yesNoArr[$value['SMOKE']])){
						$error_reporting_arr[] = "Valid Smoke value should be Yes or Y or No or N";
						$is_error = true;
					}

					if(!empty($value['TOBACCO']) && empty($yesNoArr[$value['TOBACCO']])){
						$error_reporting_arr[] = "Valid Tobacco value should be Yes or Y or No or N";
						$is_error = true;
					}

					if(!empty($value['EMPLOYMENT_STATUS']) && !in_array($value['EMPLOYMENT_STATUS'],array('PT','FT'))){
						$error_reporting_arr[] = "Valid Employment value should be FT or PT";
						$is_error = true;
					}

					if(!empty($value['HOURS_WORKED_PER_WK']) && !ctype_digit($value['HOURS_WORKED_PER_WK']) ){
						$error_reporting_arr[] = "Hours worked per week should be numeric ie. 30 or 40 etc";
						$is_error = true;
					}

					if(!empty($value['PAY_FREQUENCY']) && !in_array($value['PAY_FREQUENCY'],array('W','BW','SM','M'))){
						$error_reporting_arr[] = "Valid Pay Frequency value should be W or BW or SM or M";
						$is_error = true;
					}

					if(!empty($value['US_CITIZEN']) && empty($yesNoArr[$value['US_CITIZEN']])){
						$error_reporting_arr[] = "Valid US Citizen value should be Yes or Y or No or N";
						$is_error = true;
					}

					if(!empty($value['HAS_SPOUSE']) && empty($yesNoArr[$value['HAS_SPOUSE']])){
						$error_reporting_arr[] = "Valid Has Spouse value should be Yes or Y or No or N";
						$is_error = true;
					}

					if(!empty($value['NUMBER_OF_CHILDREN']) && !ctype_digit($value['NUMBER_OF_CHILDREN']) ){
						$error_reporting_arr[] = "Number or Children should be numeric ie. 1 or 2 etc";
						$is_error = true;
					}

					if(!empty($value['NUMBER_OF_CHILDREN']) && !ctype_digit($value['NUMBER_OF_CHILDREN']) ){
						$error_reporting_arr[] = "Number or Children should be numeric ie. 1 or 2 etc";
						$is_error = true;
					}

					
					if(!empty($globalProd[$value['PRODUCT_ID']]) && $globalProd[$value['PRODUCT_ID']]['is_short_term_disablity_product'] == 'Y'){
						if(empty($value['MONTHLY_BENEFIT_AMOUNT'])){
							$error_reporting_arr[] = "Monthly Benefit amount is required for product ".$value['PRODUCT_ID'];
							$is_error = true;
						}
						// $value['MONTHLY_BENEFIT_AMOUNT'] = str_replace(array('$',','),array('',''),$value['MONTHLY_BENEFIT_AMOUNT']);
						if(!empty($value['MONTHLY_BENEFIT_AMOUNT']) && !ctype_digit($value['MONTHLY_BENEFIT_AMOUNT'])){
							$error_reporting_arr[] = "Monthly Benefit should only in numbers without currency Format";
							$is_error = true;
						}

						if(empty($value['ANNUAL_SALARY'])){
							$error_reporting_arr[] = "Annual salary is required for product ".$value['PRODUCT_ID'];
							$is_error = true;
						}else if(!empty($value['ANNUAL_SALARY']) && !ctype_digit($value['ANNUAL_SALARY'])){
							$error_reporting_arr[] = "Annual salary should be numeric ie. 10000 or 20000 etc";
							$is_error = true;
						}
					}

					// $value['BENEFIT_AMOUNT'] = str_replace(array('$',','),array('',''),$value['BENEFIT_AMOUNT']);
					if(!empty($value['BENEFIT_AMOUNT']) && !ctype_digit($value['BENEFIT_AMOUNT']) ){
						$error_reporting_arr[] = "Benefit amount should only in numbers without currency Format";
						$is_error = true;
					}

					// $value['IN_PATIENT_BENEFIT'] = str_replace(array('$',','),array('',''),$value['IN_PATIENT_BENEFIT']);
					if(!empty($value['IN_PATIENT_BENEFIT']) && !ctype_digit($value['IN_PATIENT_BENEFIT'])){
						$error_reporting_arr[] = "IN Patient Benefit amount should only in numbers without currency Format";
						$is_error = true;
					}

					// $value['OUT_PATIENT_BENEFIT'] = str_replace(array('$',','),array('',''),$value['OUT_PATIENT_BENEFIT']);
					if(!empty($value['OUT_PATIENT_BENEFIT']) && !ctype_digit($value['OUT_PATIENT_BENEFIT'])){
						$error_reporting_arr[] = "Out Patient Benefit amount should only in numbers without currency Format";
						$is_error = true;
					}

					// $value['MONTHLY_INCOME'] = str_replace(array('$',','),array('',''),$value['MONTHLY_INCOME']);
					if(!empty($value['MONTHLY_INCOME']) && !ctype_digit($value['MONTHLY_INCOME'])){
						$error_reporting_arr[] = "Monthly Income amount should only in numbers without currency Format";
						$is_error = true;
					}

					if(!empty($value['BENEFICIARY_TYPE']) && !in_array($value['BENEFICIARY_TYPE'],array('Principal','Contingent'))){
						$error_reporting_arr[] = "Beneficiary Type is not valid";
						$is_error = true;
					}
					if(!empty($value['BENEFICIARY_NAME']) && !ctype_alpha(str_replace(' ', '', $value['BENEFICIARY_NAME']))){
						$error_reporting_arr[] = "Beneficiary Name should be valid text";
						$is_error = true;
					}
					// if(!empty($beneficiary_address)){
					// 	$error_reporting_arr[] = "Beneficiary Name should be valid text";
					// 	$is_error = true;
					// }
					if(!empty($value['BENEFICIARY_EMAIL']) && !filter_var($value['BENEFICIARY_EMAIL'], FILTER_VALIDATE_EMAIL)){
						$error_reporting_arr[] = "Beneficiary Email is Not Valid";
						$is_error = true;
					}
					if(!empty($value['BENEFICIARY_PHONE'])){
						$formatedPhone1 = phoneReplaceMain($value['BENEFICIARY_PHONE']);
						preg_match('/(\d{3})(\d{3})(\d{4})/', $formatedPhone1, $matchesB);
						if(strlen($formatedPhone1) > 10){
							$error_reporting_arr[] = "Invalid Beneficiary Phone Number (10 Digit Required)";
							$is_error = true;
						}else if("{$matchesB[1]}-{$matchesB[2]}-{$matchesB[3]}" != $value['BENEFICIARY_PHONE'] && "({$matchesB[1]}) {$matchesB[2]}-{$matchesB[3]}" != $value['BENEFICIARY_PHONE']){
							$error_reporting_arr[] = "Beneficiary Phone number should be required XXX-XXX-XXXX OR (XXX) XXX-XXXX format and having only numbers";
							$is_error = true;
						}
					}
					if(!empty($value['BENEFICIARY_SSN'])){
						preg_match('/(\d{3})(\d{2})(\d{4})/', phoneReplaceMain($value['BENEFICIARY_SSN']), $matches1);
						if("{$matches1[1]}-{$matches1[2]}-{$matches1[3]}" != $value['BENEFICIARY_SSN'] && "({$matches1[1]}) {$matches1[2]}-{$matches1[3]}" != $value['BENEFICIARY_SSN']){
							$error_reporting_arr[] = "Beneficiary SSN should be required XXX-XX-XXXX OR (XXX) XX-XXXX format and having only numbers";
							$is_error = true;
						}
					}
					if(!empty($value['BENEFICIARY_RELATION']) && !in_array(strtolower($value['BENEFICIARY_RELATION']),array('child', 'spouse', 'parent', 'grandparent', 'friend', 'other'))){
						$error_reporting_arr[] = "Beneficiary Relation should be Child, spouse, parent, grandparent, friend, other";
						$is_error = true;
					}
					if(!empty($value['BENEFICIARY_PERCENTAGE'])){
						if(!preg_match('/^\d+(?:\.\d+)?%$/',$value['BENEFICIARY_PERCENTAGE'])){
							$error_reporting_arr[] = "Beneficiary Percentage is not valid.";
							$is_error = true;
						}
						if(str_replace('%','',$value['BENEFICIARY_PERCENTAGE']) > 100 || str_replace('%','',$value['BENEFICIARY_PERCENTAGE']) < 0){
							$error_reporting_arr[] = "Beneficiary Percentage value between 0% to 100%.";
							$is_error = true;
						}
					}

					if($is_error && !empty($error_reporting_arr)){
						$errorArr['reason'] = implode(' or<br>',$error_reporting_arr);
						$pdo->insert("import_csv_log", $errorArr);
						$total_failed_records++;
						$is_error = true;
						if(!$global_is_error){
							$global_is_error = true;
						}
						continue;
					}
					if(!isset($member_arr[$memberCount][$value['PRIMARY_ID']])){
		        		if($value['RELATION'] != 'Primary'){
		        			$member_arr[$memberCount][$value['PRIMARY_ID']]['primary']['dependents'][] = $value;
		        		}else{
							$primaryprd=$value['PRODUCT_ID'];
							++$memberCount;
							$member_arr[$memberCount][$value['PRIMARY_ID']]['primary'] = $value;
		        			$member_arr[$memberCount][$value['PRIMARY_ID']]['primary']['products'][] = $value;
							/*
							if(!empty($value['POLICY_ID']) && !empty($value['PRODUCT_ID'])  && (empty($policyIdArrMember[$value['PRIMARY_ID']][$value['PRODUCT_ID']]) || $value['POLICY_ID']!=$policyIdArrMember[$value['PRIMARY_ID']][$value['PRODUCT_ID']])){
								$policyIdArrMember[$value['PRIMARY_ID']][$value['PRODUCT_ID']] = $value['POLICY_ID'];
								$policyIdArr[$value['PRIMARY_ID']][] = $value['POLICY_ID'];
							}
							*/

							if(!empty($value['BENEFICIARY_NAME'])){
								$beneficiaryArr[$memberCount][$value['PRIMARY_ID']]['beneficiary'][] = $value;
							}
							
		        		}
		        	}else{
		        		if($value['RELATION'] != 'Primary'){
		        			$member_arr[$memberCount][$value['PRIMARY_ID']]['primary']['dependents'][] = $value;
		        		}else{
							if(empty($primaryprd) || $primaryprd==$value['PRODUCT_ID']){
								++$memberCount;
								$member_arr[$memberCount][$value['PRIMARY_ID']]['primary'] = $value;	
							}
							$member_arr[$memberCount][$value['PRIMARY_ID']]['primary']['products'][] = $value;

							/*
							if(!empty($value['POLICY_ID']) && !empty($value['PRODUCT_ID']) && (empty($policyIdArrMember[$value['PRIMARY_ID']][$value['PRODUCT_ID']]) || $value['POLICY_ID']!=$policyIdArrMember[$value['PRIMARY_ID']][$value['PRODUCT_ID']])){
								$policyIdArrMember[$value['PRIMARY_ID']][$value['PRODUCT_ID']] = $value['POLICY_ID'];
								$policyIdArr[$value['PRIMARY_ID']][] = $value['POLICY_ID'];
							}
							*/

							if(!empty($value['BENEFICIARY_NAME'])){
								$beneficiaryArr[$memberCount][$value['PRIMARY_ID']]['beneficiary'][] = $value;
							}
				            
		        		}
		        	}
					if($is_error && !$global_is_error){
						$global_is_error = true;
					}
		        }

				$error_reporting_arr = [];
				$policyError = false;
				$id_key=1;
				/*
				$tempPolicyIdArr = $policyIdArrMember;
				if(!empty($policyIdArr) && !empty($policyIdArrMember)){
					foreach ($policyIdArrMember as $member_id => $valueArr) {
						if(!empty($valueArr)){
							foreach($valueArr as $plan => $policy_id){
								$tempPolicyIdArr = $policyIdArrMember;
								unset($tempPolicyIdArr[$member_id][$plan]);
								foreach($tempPolicyIdArr as $mid => $v){
									if(in_array($policy_id,$v)){
										$error_reporting_arr = array('file_id' => $file_row['id'],'csv_columns' => json_encode($member_arr[$member_id]['primary']),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()','reason'=>'Duplicate Policy Id '.$policy_id.', in member '.$member_id.' on selected Import file');
										$pdo->insert("import_csv_log", $error_reporting_arr);
										$policyError = true;
										$total_failed_records++;
										break;
									}
								}
							}
						}
						
					}
				}
				*/

		        if($member_arr && !$policyError){
					foreach($member_arr as $member_arr_val){
						foreach ($member_arr_val as $member_id => $value) {
							foreach ($value as $key => $member) {
								$is_error = false;
								$error_reporting_arr = [];
								// pre_print($member);
								$errorArr = array('file_id' => $file_row['id'],'reason' => '','csv_columns' => json_encode($member),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()');
								// pre_print($error_reporting_arr);
								
								$userType = $file_row['user_type'];
								$userId = $file_row['user_id'];
								$agent_id=(empty($member['ENROLLING_AGENT_GROUP_ID']))?(empty($member['ENROLLING_GROUP_ID']))?'':$member['ENROLLING_GROUP_ID']:$member['ENROLLING_AGENT_GROUP_ID'];
								$relation = $member['RELATION'];
								$member_id = $member['PRIMARY_ID'];
								$fname = $member['FIRST_NAME'];
								$lname = $member['LAST_NAME'];
								$phone = $member['PHONE_NUMBER'];
								$email = $member['EMAIL_ADDRESS'];
								$address = $member['ADDRESS'];
								$address_2 = $member['ADDRESS_2'];
								$city = $member['CITY'];
								$state = $member['STATE'] ? getstate('states_c',$member['STATE'],'name','short_name','name') : "";
								$zip = $member['ZIP_CODE'];
								$birth_date = $member['DOB'];
								$gender = $member['GENDER'];
								$ssn = $member['SSN'];
								$billing_type = $member['BILLING_TYPE'];
								$bankname = $member['BANK_NAME'];
								$ach_account_type = $member['ACH_ACCOUNT_TYPE'];
								$ach_routing_number = $member['ACH_ROUTING'];
								$ach_account_number = $member['ACH_ACCOUNT_NUMBER'];
								$card_type = $member['CC_TYPE'];
								$cc_number = $member['CC_NUMBER'];
								$cvv = $member['CC_CVV'];
								$cc_expiry = $member['CC_EXPIRATION'];
								$billing_name = $member['BILLING_NAME'];
								$billing_address = $member['BILLING_ADDRESS'];
								$billing_city = $member['BILLING_CITY'];
								$billing_state = $member['BILLING_STATE'] ? getstate('states_c',$member['BILLING_STATE'],'name','short_name','name') : "";
								$billing_zip = $member['BILLING_ZIPCODE'];
								// $policy_id = $member['POLICY_ID'];
								// $product_added_date = $member['PRODUCT_ADDED_DATE'];
								$product_id = $member['PRODUCT_ID'];
								$benefit_tier = $member['BENEFIT_TIER'];
								$effective_date = $member['EFFECTIVE_DATE'];
								// $termination_date = $member['TERMINATION_DATE'];
								// $next_billing_date = $member['NEXT_BILLING_DATE'];
								$active_member_since = $member['ACTIVE_MEMBER_SINCE_DATE'];
								$height = $member['HEIGHT'];
								$weight = $member['WEIGHT'];
								$smoke = !empty($yesNoArr[$member['SMOKE']]) ? $yesNoArr[$member['SMOKE']] : '';
								$tobacco = !empty($yesNoArr[$member['TOBACCO']]) ? $yesNoArr[$member['TOBACCO']] : '';
								$employed = $member['EMPLOYMENT_STATUS'];
								$annual_salary = '';
								$enrolle_class = $member['CLASS'];
								$coverage_period = $member['PLAN_PERIOD_ID'];
								$enrollee = ucfirst($member['ENROLLEE_TYPE']);
								$relationship_date = $member['RELATIONSHIP_DATE'];
								$weekly_hours = $member['HOURS_WORKED_PER_WK'];
								$pay_frequency = $member['PAY_FREQUENCY'];
								$us_citizen = !empty($yesNoArr[$member['US_CITIZEN']]) ? $yesNoArr[$member['US_CITIZEN']] : '';
								$has_spouse = !empty($yesNoArr[$member['HAS_SPOUSE']]) ? $yesNoArr[$member['HAS_SPOUSE']] : '';
								$no_of_children = $member['NUMBER_OF_CHILDREN'];
								$benefit_amount = $member['BENEFIT_AMOUNT'];
								$monthly_benefit_amount = $member['MONTHLY_BENEFIT_AMOUNT'];
								$in_patient_benefit = $member['IN_PATIENT_BENEFIT'];
								$out_patient_benefit = $member['OUT_PATIENT_BENEFIT'];
								$monthly_income = $member['MONTHLY_INCOME'];
								$benefit_percentage = $member['BENEFIT_PERCENTAGE'];
								// $beneficiary_type = $member['BENEFICIARY_TYPE'];
								// $beneficiary_name = $member['BENEFICIARY_NAME'];
								// $beneficiary_address = $member['BENEFICIARY_ADDRESS'];
								// $beneficiary_email = $member['BENEFICIARY_EMAIL'];
								// $beneficiary_phone = $member['BENEFICIARY_PHONE'];
								// $beneficiary_ssn = $member['BENEFICIARY_SSN'];
								// $beneficiary_relation = $member['BENEFICIARY_RELATION'];
								// $beneficiary_percentage = $member['BENEFICIARY_PERCENTAGE'];
								$group_company = $member['GROUP_NAME'];

								$sponsor_row = $pdo->selectOne("SELECT id,rep_id,type FROM customer WHERE rep_id = :rep_id",array(":rep_id" => $agent_id));

								if(empty($member['PRIMARY_ID'])){
									$error_reporting_arr[] = "Account ID is empty";
									$is_error = true;
								}else{
									if(strlen($member['PRIMARY_ID']) > 10){
										$error_reporting_arr[] = "Account ID length should be less then or equal 10";
										$is_error = true;
									}else{
										if(!empty($fname) && !empty($lname) && !empty($birth_date) && !empty($gender) && !empty($address)){
											$where_policy_holder = array(
												":fname" => $fname,
												":lname" => $lname,
												":birth_date" => date('Y-m-d',strtotime($birth_date)),
												":gender" => $gender,
												":address_1" => $address,
											);
											$selSql = "SELECT id FROM customer WHERE fname=:fname AND lname=:lname AND birth_date=:birth_date AND gender=:gender AND address=:address_1 AND is_deleted='N' AND type = 'Customer' AND status NOT IN('Customer Abandon','Pending Quote','Pending Validation')";
											$customer_row = $pdo->selectOne($selSql,$where_policy_holder);
											if(!empty($customer_row['id'])){
												$error_reporting_arr[] = "Member Already Exist";
												$is_error = true;
											}
										}
									}
								}

								/*
								if(empty($policy_id)){
									$error_reporting_arr[] = "Policy ID is empty";
									// $pdo->insert("import_csv_log", $error_reporting_arr);
									$is_error = true;
								}else{
									$selSql = "SELECT id FROM website_subscriptions WHERE website_id = :rep_id";
									$sub_row = $pdo->selectOne($selSql,array(":rep_id" => $policy_id));
									if(!empty($sub_row['id'])){
										$error_reporting_arr[] = "Policy Id Already Exist";
										$is_error = true;
									}
								}
								*/
								// if(empty($active_member_since)){
								// 	$error_reporting_arr[] = "Active Member Since Date is empty";
								//     // $pdo->insert("import_csv_log", $error_reporting_arr);
								//     $is_error = true;
								// }
								
								if(strtolower($userType) == 'group' && strtolower($sponsor_row['type']) != 'group'){
									$error_reporting_arr[] = "Group is not valid.";
									$is_error = true;
								}else if(strtolower($userType) == 'group' && strtolower($sponsor_row['type']) == 'group'){
									if($userId != $sponsor_row['id']){
										$error_reporting_arr[] = "Imported File Group and User Group should be same.";
										$is_error = true;
									}
								}

								$waiveCoverageTitle = $waiveCoverage = $tmpWaiveCoverage = [];
								if(!empty($sponsor_row['type']) && $sponsor_row['type'] == 'Group'){
									if(empty($enrolle_class)){
										$error_reporting_arr[] = "Class is required for group member";
										$is_error = true;
									}else{
										$classAr = $pdo->selectOne("SELECT id from group_classes where class_name=:className and group_id=:group_id and is_deleted='N'",array(":className"=>$enrolle_class,':group_id'=>$sponsor_row['id']));
										if(empty($classAr)){
											$error_reporting_arr[] = "Invalid Class for selected group";
											$is_error = true;
										}else{
											$enrolle_class = !empty($classAr['id']) ? $classAr['id'] : 0;
										}
									}
								
									if(empty($coverage_period)){
										$error_reporting_arr[] = "Plan Period ID required for group member";
										$is_error = true;
									}else{
										$coverageAr = $pdo->selectOne("SELECT id from group_coverage_period where display_id=:display_id and group_id=:group_id and is_deleted='N'",array(":display_id"=>$coverage_period,':group_id'=>$sponsor_row['id']));
										if(empty($coverageAr)){
											$error_reporting_arr[] = "Invalid Plan Period ID for selected group.";
											$is_error = true;
										}else{
											$group_coverage_period_id = !empty($coverageAr['id']) ? $coverageAr['id'] : 0;
										}
									}

									if(!empty($group_company)){
										$groupCompany = $pdo->selectOne("SELECT id FROM group_company where name=:name AND group_id=:group_id and is_deleted='N'",array(":name"=>$group_company,':group_id'=>$sponsor_row['id']));
										if(empty($groupCompany['id'])){
											$groupCompany1 = $pdo->selectOne("SELECT id FROM customer WHERE business_name=:name and is_deleted='N' and id=:group_id",array(":name"=>$group_company,':group_id'=>$sponsor_row['id']));
											if(empty($groupCompany1['id'])){
												$error_reporting_arr[] = "Group Company is not valid for this group.";
												// $pdo->insert("import_csv_log", $error_reporting_arr);
												$is_error = true;
											}
										}else{
											$group_company_id = $groupCompany['id'];
										}
									}
								}

								if($error_reporting_arr){
									$errorArr['reason'] = implode(' or<br>', $error_reporting_arr);
									$pdo->insert("import_csv_log", $errorArr);
									$total_failed_records++;
									continue;
								}

								if(!$is_error){

									$params = array(
										'sponsor_id' => $sponsor_row['id'],
										'sponsor_type' => $sponsor_row['type'],
										'primary_fname' => $fname,
										'enrollmentLocation' => $sponsor_row['type'] == 'Agent' ? 'agentSide' : 'groupSide',
										'primary_zip' => $zip,
										'primary_gender' => $gender,
										'primary_birthdate' => $birth_date,
										'primary_email' => $email,
										'customer_id' => (!empty($customer_row['id']) ? $customer_row['id'] : 0),

									);

									$get_available_products = get_available_products($params);

									$member_products = $member['products'];
									$productErrors = $get_available_products['rule_error'];
									$available_product = $get_available_products['product_list'];
									$waive_products = [];
									$product_matrix = array();
									$product_price = array();
									$product_plan = array();
									$product_category = [];
									$all_category = [];
									$coverage_date = array();
									// $website_ids = array();
									// $termination_dates = array();
									$product_code_id_wise = $product_id_code_wise = array();
									$addProductError = [];
									$monthly_benefit_amount = $benefit_amount = $in_patient_benefit = $out_patient_benefit = $monthly_income = [];
									// $exists_policy = [];
									$effective_date = [];
									$product_list = [];
									$coreProductArr = [];
									if($member_products){

										$tempProduct = '';
										foreach ($member_products as $product) {

											/*
											if(empty($exists_policy) || !in_array($product['POLICY_ID'],$exists_policy)){
												$exists_policy[] = $product['POLICY_ID'];
											}else if(!empty($exists_policy) && in_array($product['POLICY_ID'],$exists_policy) && $tempProduct != $product['PRODUCT_ID']){
												$addProductError[] = 'Duplicate Policy Id '.$product['POLICY_ID'].', in member '.$member_id.' on selected Import file';
												continue;
											}
											*/
											$tempProduct =  $product['PRODUCT_ID'];

											$product_info = $pdo->selectOne("SELECT id,category_id,main_product_type,name FROM prd_main WHERE product_code = :product_code AND status = 'Active' AND is_deleted = 'N'",array(':product_code' =>$product['PRODUCT_ID']));
											
											if($product_info){

												$product_matrix[$product_id] = 0;
												$product_id = $product_info['id'];
												$product_category[] = $product_info['category_id'];
												$product_list[] = $product_id;
												$prd_matrix_info = $pdo->selectOne("SELECT pm.id as matrix_id,pm.product_id,pm.price,pm.plan_type FROM prd_matrix pm JOIN prd_plan_type ppt on(ppt.id = pm.plan_type AND ppt.is_active='Y') WHERE pm.product_id = :product_id AND ppt.title = :title AND pm.is_deleted='N'",array(":product_id" => $product_id,':title' => $product['BENEFIT_TIER']));
												if($prd_matrix_info){
													$plan_type = $prd_matrix_info['plan_type'];
													$product_plan[$product_id] = $plan_type;
													if(empty($available_product[$product_id]['Matrix'][$plan_type])){
														$addProductError[] = 'Product '.$product['PRODUCT_ID'].' is not available for this Agent ID/Group ID';
														$addProductError[] = $productErrors[$prd_matrix_info['matrix_id']];
														continue;
													}
												}else{
													$plansAr = $pdo->selectOne("SELECT id from prd_plan_type where is_active='Y' AND title=:title",array(":title"=>$product['BENEFIT_TIER']));
													$enroleeProduct = $pdo->selectOne("SELECT p.id from prd_main p JOIN prd_matrix pm ON(pm.product_id=p.id AND pm.is_deleted='N' AND pm.pricing_model='VariableEnrollee') JOIN prd_matrix_criteria pmc ON(pmc.product_id=p.id and pmc.prd_matrix_id=pm.id AND pmc.is_deleted='N') where p.id=:id AND p.is_deleted='N'",array(":id"=>$product_id));
													if(!empty($plansAr) && !empty($enroleeProduct)){
														if(empty($available_product[$product_id]['Enrollee_Matrix'][$plansAr['id']])){
															$addProductError[] = 'Product '.$product['PRODUCT_ID'].' is not available for this Agent ID/Group ID';
															$addProductError[] = $productErrors[$prd_matrix_info['matrix_id']];
															continue;
														}
														$product_plan[$product_id] = $plansAr['id'];
													}else{
														$addProductError[] = 'Product '.$product['PRODUCT_ID'].' benefit tier '.$product['BENEFIT_TIER'].' not exists.'.implode(' or<br>',array_values(array_filter($productErrors)));
														continue;
													}
												}
												// $product_plan[$product_id] = 1;//Default Plan if coverage is waived
												if(!empty($waiveCoverage['waive_coverage'][$product_info['category_id']])){
													$waive_products[$product_info['category_id']][] = $product_id;
													continue;
												}
												if($product_info['main_product_type'] == 'Core Product'){
													$coreProductArr[$product_info['id']] = $product_info['name'].'('.$product['PRODUCT_ID'].')';
												}

												if(!empty($product['EFFECTIVE_DATE']) && strtotime($product['EFFECTIVE_DATE']) > 0){
													$effective_date[$product_id] = $product['EFFECTIVE_DATE'];
												}

												if(!empty($product['ACTIVE_MEMBER_SINCE_DATE']) && strtotime($product['ACTIVE_MEMBER_SINCE_DATE']) > 0){
													$effective_date[$product_id] = $product['ACTIVE_MEMBER_SINCE_DATE'];
												}

												if(empty($monthly_benefit_amount[$product_id])){
													$monthly_benefit_amount[$product_id] = $product['MONTHLY_BENEFIT_AMOUNT'];
												}
												if(empty($benefit_amount[$product_id])){
													$benefit_amount[$product_id] = $product['BENEFIT_AMOUNT'];
												}
												if(empty($in_patient_benefit[$product_id])){
													$in_patient_benefit[$product_id] = $product['IN_PATIENT_BENEFIT'];
												}
												if(empty($out_patient_benefit[$product_id])){
													$out_patient_benefit[$product_id] = $product['OUT_PATIENT_BENEFIT'];
												}
												if(empty($monthly_income[$product_id])){
													$monthly_income[$product_id] = $product['MONTHLY_INCOME'];
												}
												if(empty($annual_salary)){
													$annual_salary = $product['ANNUAL_SALARY'];
												}
												if(empty($product_id_code_wise[$product['PRODUCT_ID']])){
													$product_id_code_wise[$product['PRODUCT_ID']] = $product_id;
												}
												$product_code_id_wise[$product_id] = $product['PRODUCT_ID'];
												// $website_ids[$product_id] = $product['POLICY_ID'];
												// $termination_dates[$product_id] = $product['TERMINATION_DATE'];

											}
										}	

										    // start waived product category
										    if(!empty($sponsor_row['type']) && $sponsor_row['type'] == 'Group'){
										    	if(!empty($available_product)){
										    		$existsCategory = [];
										    		$i=0;
										    		foreach ($available_product as $product_id => $prdArr) {
										    			if(!in_array($prdArr['category_id'],$existsCategory)){
										    				$existsCategory[] = $prdArr['category_id'];
										    				$all_category[$i]['name'] = $prdArr['category_name'];
										    				$all_category[$i]['id'] = $prdArr['category_id'];
										    				$i++;
										    			}
										    		}
										    	}
									            
										    	if(!empty($product_category) && !empty($all_category)){
										    		foreach($all_category as $category){
										    			if(!in_array($category['id'],$product_category)){
										    				$waiveCoverage['waive_coverage'][$category['id']] = $category['id'];
										    				$waiveCoverage['waive_coverage_reason'][$category['id']] = 'Other';
										    				$waiveCoverage['waive_coverage_other_reason'][$category['id']] = '';
										    			}
										    		}
										    	}
										    }
											//end start waived product category
									}
									// pre_print($member_products);
									if($member_products){
										$__temp_products = array();
										foreach ($member_products as $__key => $__value) {
											if(!empty($__value['SSN'])){
												$ssn = $__value['SSN'];
											}
											if(!empty($__value['PRODUCT_ID'])){
												array_push($__temp_products, $__value['PRODUCT_ID']);
											}
										}
										if(!empty($__temp_products)){
											$ssn_req_res = $pdo->select("SELECT peqa.product_id,peqa.is_member_required,p.product_code 
											FROM prd_enrollment_questions_assigned peqa
											JOIN prd_enrollment_questions peq ON(peq.id = peqa.prd_question_id)
											JOIN prd_main p on(p.id = peqa.product_id)
											WHERE p.product_code IN('".implode("','", $__temp_products)."') AND peqa.is_member_required = 'Y' AND peq.label = 'SSN' AND peqa.is_deleted = 'N' AND peq.is_deleted = 'N'");
											if(!empty($ssn_req_res)){
												$req_ssn_products = array_column($ssn_req_res, 'product_code');
												if(empty($ssn)){
													$addProductError[] = "SSN is required for product ".implode(',', $req_ssn_products);
												}else if(!empty($ssn)){
													preg_match('/(\d{3})(\d{2})(\d{4})/', phoneReplaceMain($ssn), $matchesP);
													if("{$matchesP[1]}-{$matchesP[2]}-{$matchesP[3]}" != $ssn && "({$matchesP[1]}) {$matchesP[2]}-{$matchesP[3]}" != $ssn){
														$addProductError[] = "SSN should be required XXX-XX-XXXX or (XXX) XX-XXXX format and having only numbers";
													}
												}
											}
										}
									}
                                    
									if(!empty($coreProductArr) && count($coreProductArr) > 1){
										$addProductError[] = "A member may have only one (1) active Core Product at a time from this products : ".implode(', ',$coreProductArr);
									}
									if(!empty($addProductError)){
										$errorArr['reason'] = implode(' or<br>', $addProductError);
										$pdo->insert("import_csv_log", $errorArr);
										$total_failed_records++;
										continue;
									}
									if(empty($available_product)){
										$errorArr['reason'] = "Products Not Exist or pricing criteria not exists.";
										$pdo->insert("import_csv_log", $errorArr);
										$total_failed_records++;
										continue;
									}

									// $product_list = array_keys($product_matrix);
									$is_group_member = $params['sponsor_type'] == 'Group' ? 'Y' : 'N';

									$extra = array();
									if($is_group_member == 'Y'){
										$extra['is_group_member']=$is_group_member;
										$extra['enrolle_class']=$enrolle_class;
										$extra['coverage_period']=$group_coverage_period_id;
										$extra['relationship_date']=$relationship_date;
										$extra['relationship_to_group']=$enrollee;
									}

									$summaryList =$MemberEnrollment->get_coverage_period($product_list,$params['sponsor_id'],$extra);
									foreach ($product_list as $value) {
										$coverage_date[$value] = $summaryList[$value]['coverage_date'];
									}
									// pre_print($coverage_date);

									$params = array(
										'admin_id' => $file_row['admin_id'],
										'sponsor_id' => $sponsor_row['id'],
										'customer_id' =>(!empty($customer_row['id']) ? $customer_row['id'] : 0),
										'rep_id' => $member_id,
										'primary_fname' => $fname,
										'primary_lname' => $lname,
										'primary_SSN' =>$ssn,
										'primary_phone' =>$phone,
										'primary_address' =>$address,
										'primary_city' =>$city,
										'primary_state' =>$state,
										'primary_zip' => $zip,
										'primary_gender' => $gender,
										'primary_birthdate' => $birth_date,
										'primary_email' => $email,
										// 'primary_benefit_amount' => $benefit_amount,
										// 'primary_in_patient_benefit' => $in_patient_benefit,
										// 'primary_out_patient_benefit' => $out_patient_benefit,
										// 'primary_monthly_income' => $monthly_income,
										'primary_benefit_percentage' => $benefit_percentage,
										'spouse_benefit_amount' => array(),
										'spouse_in_patient_benefit' => array(),
										'spouse_out_patient_benefit' => array(),
										'spouse_monthly_income' => array(),
										'spouse_benefit_percentage' => array(),
										'child_benefit_amount' => array(),
										'child_in_patient_benefit' => array(),
										'child_out_patient_benefit' => array(),
										'child_monthly_income' => array(),
										'child_benefit_percentage' => array(),
										'is_principal_beneficiary' => '',
										'is_contingent_beneficiary' => '',
										'principal_queBeneficiaryFullName' => array(),
										'contingent_queBeneficiaryFullName' => array(),
										'payment_mode' => in_array(strtolower($billing_type),array("credit card","cc")) ? 'CC' : 'ACH',
										'bill_address' => $billing_address,
										'bill_city' => $billing_city,
										'bill_state' => $billing_state,
										'bill_zip' => $billing_zip,
										'name_on_card' => $billing_name,
										'card_number' => $cc_number,
										'card_type' => $card_type,
										'expiration' => $cc_expiry,
										'cvv_no' => $cvv,
										'full_card_number' => $cc_number,
										'ach_bill_fname' => $billing_name,
										'ach_bill_lname' => '',
										'bankname' => $bankname,
										'ach_account_type' => $ach_account_type,
										'routing_number' => $ach_routing_number,
										'account_number' => $ach_account_number,
										'confirm_account_number' => $ach_account_number,
										'entered_routing_number' => $ach_routing_number,
										'entered_account_number' => $ach_account_number,
										// 'product_matrix' => $product_matrix,
										'product_plan' => $product_plan,
										// 'product_price' => $product_price,
										'coverage_date' => $coverage_date,
										'product_id_code_wise' => $product_id_code_wise,
										'group_company_id' => $group_company_id,
										'effective_date' => $effective_date

									);

									$params['enrolle_class'] = $enrolle_class;
									$params['class_id'] = $enrolle_class;
									$params['waive_coverage'] = $waiveCoverage['waive_coverage'];
									$params['waive_coverage_reason'] = $waiveCoverage['waive_coverage_reason'];
									$params['waive_coverage_other_reason'] = $waiveCoverage['waive_coverage_other_reason'];
									$params['waive_products'] = array_map('array_unique',$waive_products);
									$params['coverage_period'] = $group_coverage_period_id;
									$params['group_coverage_period_id'] = $group_coverage_period_id;
									$params['relationship_date'] = $relationship_date;
									$params['relationship_to_group'] = $enrollee;
									$params['primary_address1'] = $member['ADDRESS'];
									$params['primary_address2'] = $member['ADDRESS_2'];
									$params['primary_height'] = $member['HEIGHT'];
									$params['primary_weight'] = $member['WEIGHT'];
									$params['primary_smoking_status'] = !empty($yesNoArr[$member['SMOKE']]) ? $yesNoArr[$member['SMOKE']] : '';
									$params['primary_tobacco_status'] = !empty($yesNoArr[$member['TOBACCO']]) ? $yesNoArr[$member['TOBACCO']] : '';
									$params['primary_benefit_level'] = "";
									$params['primary_employment_status'] = $member['EMPLOYMENT_STATUS'];
									$params['primary_salary'] = $member['ANNUAL_SALARY'];
									$params['primary_date_of_hire'] = "";
									$params['primary_hours_per_week'] = $member['HOURS_WORKED_PER_WK'];
									$params['primary_pay_frequency'] = $member['PAY_FREQUENCY'];
									$params['primary_us_citizen'] = !empty($yesNoArr[$member['US_CITIZEN']]) ? $yesNoArr[$member['US_CITIZEN']] : '';
									$params['primary_relation'] = $member['BENEFICIARY_RELATION'];
									$params['primary_no_of_children'] = $member['NUMBER_OF_CHILDREN'];
									$params['primary_has_spouse'] = !empty($yesNoArr[$member['HAS_SPOUSE']]) ? $yesNoArr[$member['HAS_SPOUSE']] : '';
									$params['primary_monthly_benefit_amount'] = $monthly_benefit_amount;
									$params['primary_benefit_amount'] = $benefit_amount;
									$params['primary_in_patient_benefit'] = $in_patient_benefit;
									$params['primary_out_patient_benefit'] = $out_patient_benefit;
									$params['primary_monthly_income'] = $monthly_income;
									$params['primary_annual_salary'] = $annual_salary;
									$params['primary_benefit_percentage'] = $member['BENEFIT_PERCENTAGE'];
									
									if(!empty($custom_questions)){
										foreach($custom_questions as $key => $question){
											$params['primary_'.$question['label']] = $member['CUSTOM_QUESTION_'.($key+1)];
											$params['primary_queCustom'][$question['id']] = $member['CUSTOM_QUESTION_'.($key+1)];
										}
									}

									$member_dependents = checkIsset($member['dependents'],'arr');
									$member_benificiary = $beneficiaryArr[$id_key][$member_id]['beneficiary'];

									$pk = array_map(function($p){
										return str_replace('primary_','',$p);
									}, array_keys($params));
									$pv = array_values($params);
									$primary_profile =  array_combine($pk, $pv);
									$primary_profile['sponsor_type'] = $sponsor_row['type'];
									$dep_profile = array();
									$spouse_fname = array();
									$child_fname = array();
									$spouse_assign_products = array();
									$child_assign_products = array();
									$spouse_products_list = array();
									$child_products_list = array();

									if($member_dependents){
										foreach ($member_dependents as $k => $dependent) {
											$dep_prd_code = $dependent['PRODUCT_ID'];
											if($dependent['RELATION'] == 'Spouse'){
												$spouse_fname[$k] = $dependent['FIRST_NAME'];

												$dep_profile[$dep_prd_code][$k]['fname'] = $dependent['FIRST_NAME'];
												$dep_profile[$dep_prd_code][$k]['lname'] = $dependent['LAST_NAME'];
												$dep_profile[$dep_prd_code][$k]['birthdate'] = $dependent['DOB'];
												$dep_profile[$dep_prd_code][$k]['gender'] = $dependent['GENDER'];
												$dep_profile[$dep_prd_code][$k]['email'] = $dependent['EMAIL_ADDRESS'];
												$dep_profile[$dep_prd_code][$k]['phone'] = $dependent['PHONE_NUMBER'];
												$dep_profile[$dep_prd_code][$k]['SSN'] = $dependent['SSN'];
												$dep_profile[$dep_prd_code][$k]['address1'] = $dependent['ADDRESS'];
												$dep_profile[$dep_prd_code][$k]['address2'] = '';
												$dep_profile[$dep_prd_code][$k]['city'] = $dependent['CITY'];
												$dep_profile[$dep_prd_code][$k]['state'] = $dependent['STATE'] ? getstate('states_c',$dependent['STATE'],'name','short_name','name') : "";
												$dep_profile[$dep_prd_code][$k]['zip'] = $dependent['ZIP_CODE'];
												$dep_profile[$dep_prd_code][$k]['height'] = $dependent['HEIGHT'];
												$dep_profile[$dep_prd_code][$k]['weight'] = $dependent['WEIGHT'];
												$dep_profile[$dep_prd_code][$k]['smoking_status'] = !empty($yesNoArr[$dependent['SMOKE']]) ? $yesNoArr[$dependent['SMOKE']] : '';
												$dep_profile[$dep_prd_code][$k]['tobacco_status'] = !empty($yesNoArr[$dependent['TOBACCO']]) ? $yesNoArr[$dependent['TOBACCO']] : '';
												$dep_profile[$dep_prd_code][$k]['benefit_level'] = "";
												$dep_profile[$dep_prd_code][$k]['employment_status'] = $dependent['EMPLOYMENT_STATUS'];
												$dep_profile[$dep_prd_code][$k]['salary'] = $dependent['ANNUAL_SALARY'];
												$dep_profile[$dep_prd_code][$k]['date_of_hire'] = "";
												$dep_profile[$dep_prd_code][$k]['hours_per_week'] = $dependent['HOURS_WORKED_PER_WK'];
												$dep_profile[$dep_prd_code][$k]['pay_frequency'] = $dependent['PAY_FREQUENCY'];
												$dep_profile[$dep_prd_code][$k]['us_citizen'] = !empty($yesNoArr[$dependent['US_CITIZEN']]) ? $yesNoArr[$dependent['US_CITIZEN']] : '';
												$dep_profile[$dep_prd_code][$k]['relation'] = $dependent['RELATION'];
												$dep_profile[$dep_prd_code][$k]['no_of_children'] = $dependent['NUMBER_OF_CHILDREN'];
												$dep_profile[$dep_prd_code][$k]['has_spouse'] = !empty($yesNoArr[$dependent['HAS_SPOUSE']]) ? $yesNoArr[$dependent['HAS_SPOUSE']] : '';
												$dep_profile[$dep_prd_code][$k]['benefit_amount'] = $dependent['BENEFIT_AMOUNT'];
												$dep_profile[$dep_prd_code][$k]['in_patient_benefit'] = $dependent['IN_PATIENT_BENEFIT'];
												$dep_profile[$dep_prd_code][$k]['out_patient_benefit'] = $dependent['OUT_PATIENT_BENEFIT'];
												$dep_profile[$dep_prd_code][$k]['monthly_income'] = $dependent['MONTHLY_INCOME'];
												$dep_profile[$dep_prd_code][$k]['benefit_percentage'] = $dependent['BENEFIT_PERCENTAGE'];

												$params['spouse_fname'][$k] = $dependent['FIRST_NAME'];
												$params['spouse_lname'][$k] = $dependent['LAST_NAME'];
												$params['spouse_birthdate'][$k] = $dependent['DOB'];
												$params['spouse_gender'][$k] = $dependent['GENDER'];
												$params['spouse_email'][$k] = $dependent['EMAIL_ADDRESS'];
												$params['spouse_phone'][$k] = $dependent['PHONE_NUMBER'];
												$params['spouse_SSN'][$k] = $dependent['SSN'];
												$params['spouse_address1'][$k] = $dependent['ADDRESS'];
												$params['spouse_address2'][$k] = '';
												$params['spouse_city'][$k] = $dependent['CITY'];
												$params['spouse_state'][$k] = $dependent['STATE'] ? getstate('states_c',$dependent['STATE'],'name','short_name','name') : "";
												$params['spouse_zip'][$k] = $dependent['ZIP_CODE'];
												$params['spouse_height'][$k] = $dependent['HEIGHT'];
												$params['spouse_weight'][$k] = $dependent['WEIGHT'];
												$params['spouse_smoking_status'][$k] = !empty($yesNoArr[$dependent['SMOKE']]) ? $yesNoArr[$dependent['SMOKE']] : '';
												$params['spouse_tobacco_status'][$k] = !empty($yesNoArr[$dependent['TOBACCO']]) ? $yesNoArr[$dependent['TOBACCO']] : '';
												$params['spouse_benefit_level'][$k] = "";
												$params['spouse_employment_status'][$k] = $dependent['EMPLOYMENT_STATUS'];
												$params['spouse_salary'][$k] = $dependent['ANNUAL_SALARY'];
												$params['spouse_date_of_hire'][$k] = "";
												$params['spouse_hours_per_week'][$k] = $dependent['HOURS_WORKED_PER_WK'];
												$params['spouse_pay_frequency'][$k] = $dependent['PAY_FREQUENCY'];
												$params['spouse_us_citizen'][$k] = !empty($yesNoArr[$dependent['US_CITIZEN']]) ? $yesNoArr[$dependent['US_CITIZEN']] : '';
												$params['spouse_relation'][$k] = $dependent['RELATION'];
												$params['spouse_no_of_children'][$k] = $dependent['NUMBER_OF_CHILDREN'];
												$params['spouse_has_spouse'][$k] = !empty($yesNoArr[$dependent['HAS_SPOUSE']]) ? $yesNoArr[$dependent['HAS_SPOUSE']] : '';
												$params['spouse_benefit_amount'][$k] = $dependent['BENEFIT_AMOUNT'];
												$params['spouse_in_patient_benefit'][$k] = $dependent['IN_PATIENT_BENEFIT'];
												$params['spouse_out_patient_benefit'][$k] = $dependent['OUT_PATIENT_BENEFIT'];
												$params['spouse_monthly_income'][$k] = $dependent['MONTHLY_INCOME'];
												$params['spouse_benefit_percentage'][$k] = $dependent['BENEFIT_PERCENTAGE'];
												
												if(!empty($custom_questions)){
													foreach($custom_questions as $key => $question){
														$params['spouse_'.$question['label']][$k] = $dependent['CUSTOM_QUESTION_'.($key+1)];
														$params['spouse_queCustom'][$k][$question['id']] = $dependent['CUSTOM_QUESTION_'.($key+1)];
													}
												}

												$product_info = $pdo->selectOne("SELECT id FROM prd_main WHERE product_code = :product_code AND status = 'Active' AND is_deleted = 'N'",array(':product_code' =>$dependent['PRODUCT_ID']));
												if($product_info){
													$spouse_assign_products[$k][] = $product_info['id'];
													array_push($spouse_products_list, $product_info['id']);
												}

											}else if($dependent['RELATION'] == 'Child'){
												$child_fname[$k] = $dependent['FIRST_NAME'];

												$dep_profile[$dep_prd_code][$k]['fname'] = $dependent['FIRST_NAME'];
												$dep_profile[$dep_prd_code][$k]['lname'] = $dependent['LAST_NAME'];
												$dep_profile[$dep_prd_code][$k]['birthdate'] = $dependent['DOB'];
												$dep_profile[$dep_prd_code][$k]['gender'] = $dependent['GENDER'];
												$dep_profile[$dep_prd_code][$k]['email'] = $dependent['EMAIL_ADDRESS'];
												$dep_profile[$dep_prd_code][$k]['phone'] = $dependent['PHONE_NUMBER'];
												$dep_profile[$dep_prd_code][$k]['SSN'] = $dependent['SSN'];
												$dep_profile[$dep_prd_code][$k]['address1'] = $dependent['ADDRESS'];
												$dep_profile[$dep_prd_code][$k]['address2'] = '';
												$dep_profile[$dep_prd_code][$k]['city'] = $dependent['CITY'];
												$dep_profile[$dep_prd_code][$k]['state'] = $dependent['STATE'] ? getstate('states_c',$dependent['STATE'],'name','short_name','name') : "";
												$dep_profile[$dep_prd_code][$k]['zip'] = $dependent['ZIP_CODE'];
												$dep_profile[$dep_prd_code][$k]['height'] = $dependent['HEIGHT'];
												$dep_profile[$dep_prd_code][$k]['weight'] = $dependent['WEIGHT'];
												$dep_profile[$dep_prd_code][$k]['smoking_status'] = !empty($yesNoArr[$dependent['SMOKE']]) ? $yesNoArr[$dependent['SMOKE']] : '';
												$dep_profile[$dep_prd_code][$k]['tobacco_status'] = !empty($yesNoArr[$dependent['TOBACCO']]) ? $yesNoArr[$dependent['TOBACCO']] : '';
												$dep_profile[$dep_prd_code][$k]['benefit_level'] = "";
												$dep_profile[$dep_prd_code][$k]['employment_status'] = $dependent['EMPLOYMENT_STATUS'];
												$dep_profile[$dep_prd_code][$k]['salary'] = $dependent['ANNUAL_SALARY'];
												$dep_profile[$dep_prd_code][$k]['date_of_hire'] = "";
												$dep_profile[$dep_prd_code][$k]['hours_per_week'] = $dependent['HOURS_WORKED_PER_WK'];
												$dep_profile[$dep_prd_code][$k]['pay_frequency'] = $dependent['PAY_FREQUENCY'];
												$dep_profile[$dep_prd_code][$k]['us_citizen'] =  !empty($yesNoArr[$dependent['US_CITIZEN']]) ? $yesNoArr[$dependent['US_CITIZEN']] : '';
												$dep_profile[$dep_prd_code][$k]['relation'] = $dependent['RELATION'];
												$dep_profile[$dep_prd_code][$k]['no_of_children'] = $dependent['NUMBER_OF_CHILDREN'];
												$dep_profile[$dep_prd_code][$k]['has_spouse'] = !empty($yesNoArr[$dependent['HAS_SPOUSE']]) ? $yesNoArr[$dependent['HAS_SPOUSE']] : '';
												$dep_profile[$dep_prd_code][$k]['benefit_amount'] = $dependent['BENEFIT_AMOUNT'];
												$dep_profile[$dep_prd_code][$k]['in_patient_benefit'] = $dependent['IN_PATIENT_BENEFIT'];
												$dep_profile[$dep_prd_code][$k]['out_patient_benefit'] = $dependent['OUT_PATIENT_BENEFIT'];
												$dep_profile[$dep_prd_code][$k]['monthly_income'] = $dependent['MONTHLY_INCOME'];
												$dep_profile[$dep_prd_code][$k]['benefit_percentage'] = $dependent['BENEFIT_PERCENTAGE'];

												$params['child_fname'][$k] = $dependent['FIRST_NAME'];
												$params['child_lname'][$k] = $dependent['LAST_NAME'];
												$params['child_birthdate'][$k] = $dependent['DOB'];
												$params['child_gender'][$k] = $dependent['GENDER'];
												$params['child_email'][$k] = $dependent['EMAIL_ADDRESS'];
												$params['child_phone'][$k] = $dependent['PHONE_NUMBER'];
												$params['child_SSN'][$k] = $dependent['SSN'];
												$params['child_address1'][$k] = $dependent['ADDRESS'];
												$params['child_address2'][$k] = '';
												$params['child_city'][$k] = $dependent['CITY'];
												$params['child_state'][$k] = $dependent['STATE'] ? getstate('states_c',$dependent['STATE'],'name','short_name','name') : "";
												$params['child_zip'][$k] = $dependent['ZIP_CODE'];
												$params['child_height'][$k] = $dependent['HEIGHT'];
												$params['child_weight'][$k] = $dependent['WEIGHT'];
												$params['child_smoking_status'][$k] = !empty($yesNoArr[$dependent['SMOKE']]) ? $yesNoArr[$dependent['SMOKE']] : '';
												$params['child_tobacco_status'][$k] =  !empty($yesNoArr[$dependent['TOBACCO']]) ? $yesNoArr[$dependent['TOBACCO']] : '';
												$params['child_benefit_level'][$k] = "";
												$params['child_employment_status'][$k] = $dependent['EMPLOYMENT_STATUS'];
												$params['child_salary'][$k] = $dependent['ANNUAL_SALARY'];
												$params['child_date_of_hire'][$k] = "";
												$params['child_hours_per_week'][$k] = $dependent['HOURS_WORKED_PER_WK'];
												$params['child_pay_frequency'][$k] = $dependent['PAY_FREQUENCY'];
												$params['child_us_citizen'][$k] = !empty($yesNoArr[$dependent['US_CITIZEN']]) ? $yesNoArr[$dependent['US_CITIZEN']] : '';
												$params['child_relation'][$k] = $dependent['RELATION'];
												$params['child_no_of_children'][$k] = $dependent['NUMBER_OF_CHILDREN'];
												$params['child_has_spouse'][$k] = !empty($yesNoArr[$dependent['HAS_SPOUSE']]) ? $yesNoArr[$dependent['HAS_SPOUSE']] : '';
												$params['child_benefit_amount'][$k] = $dependent['BENEFIT_AMOUNT'];
												$params['child_in_patient_benefit'][$k] = $dependent['IN_PATIENT_BENEFIT'];
												$params['child_out_patient_benefit'][$k] = $dependent['OUT_PATIENT_BENEFIT'];
												$params['child_monthly_income'][$k] = $dependent['MONTHLY_INCOME'];
												$params['child_benefit_percentage'][$k] = $dependent['BENEFIT_PERCENTAGE'];
												
												if(!empty($custom_questions)){
													foreach($custom_questions as $key => $question){
														$params['child_'.$question['label']][$k] = $dependent['CUSTOM_QUESTION_'.($key+1)];
														$params['child_queCustom'][$k][$question['id']] = $dependent['CUSTOM_QUESTION_'.($key+1)];
													}
												}
												$product_info = $pdo->selectOne("SELECT id FROM prd_main WHERE product_code = :product_code AND status = 'Active' AND is_deleted = 'N'",array(':product_code' =>$dependent['PRODUCT_ID']));
												if($product_info){
													$child_assign_products[$k][] = $product_info['id'];
													array_push($child_products_list, $product_info['id']);
												}

											}
										}
									}
									$beneficiaryProductsWise = array();
									if(!empty($member_benificiary)){
										foreach ($member_benificiary as $k => $beneficiary) {
											if(empty($product_id_code_wise[$beneficiary['PRODUCT_ID']])){
												continue;
											}
											$beneficiaryProductsWise[] = $beneficiary['BENEFICIARY_TYPE'];
											if(strtolower($beneficiary['BENEFICIARY_TYPE']) == 'principal' && !empty($beneficiary['BENEFICIARY_NAME'])){
												$params['is_principal_beneficiary'] = 'displayed';
												$params['principal_product'][] = $product_id_code_wise[$beneficiary['PRODUCT_ID']];
												$params['principal_queBeneficiaryFullName'][] = $beneficiary['BENEFICIARY_NAME'];
												$params['principal_queBeneficiaryAddress'][] = $beneficiary['BENEFICIARY_ADDRESS'];
												$params['principal_queBeneficiaryPhone'][] = $beneficiary['BENEFICIARY_PHONE'];
												$params['principal_queBeneficiaryEmail'][] = $beneficiary['BENEFICIARY_EMAIL'];
												$params['principal_queBeneficiarySSN'][] = $beneficiary['BENEFICIARY_SSN'];
												$params['principal_queBeneficiaryRelationship'][] = $beneficiary['BENEFICIARY_RELATION'];
												$params['principal_queBeneficiaryPercentage'][] = $beneficiary['BENEFICIARY_PERCENTAGE'];
												
											}else if(strtolower($beneficiary['BENEFICIARY_TYPE']) == 'contingent' && !empty($beneficiary['BENEFICIARY_NAME'])){
												$params['is_contingent_beneficiary'] = 'displayed';
												$params['contingent_product'][] = $product_id_code_wise[$beneficiary['PRODUCT_ID']];
												$params['contingent_queBeneficiaryFullName'][] = $beneficiary['BENEFICIARY_NAME'];
												$params['contingent_queBeneficiaryAddress'][] = $beneficiary['BENEFICIARY_ADDRESS'];
												$params['contingent_queBeneficiaryPhone'][] = $beneficiary['BENEFICIARY_PHONE'];
												$params['contingent_queBeneficiaryEmail'][] = $beneficiary['BENEFICIARY_EMAIL'];
												$params['contingent_queBeneficiarySSN'][] = $beneficiary['BENEFICIARY_SSN'];
												$params['contingent_queBeneficiaryRelationship'][] = $beneficiary['BENEFICIARY_RELATION'];
												$params['contingent_queBeneficiaryPercentage'][] = $beneficiary['BENEFICIARY_PERCENTAGE'];
											}
										}
									}

									$params['spouse_fname'] = $spouse_fname;
									$params['child_fname'] = $child_fname;
									$params['spouse_assign_products'] = $spouse_assign_products;
									$params['child_assign_products'] = $child_assign_products;
									$params['spouse_products_list'] = implode(',',$spouse_products_list);
									$params['child_products_list'] = implode(',',$child_products_list);
									// $params['website_ids'] = $website_ids;
									// $params['termination_dates'] = $termination_dates;
									
									$price_details = [];
									$product_price = [];
									$otherError = [];
									$primary_monthly_salary_percentage = [];
									$beneficiaryAdded = false;
									foreach($product_code_id_wise as $product_id => $product_code){
										if(!$beneficiaryAdded){
											$beneficiary = $pdo->selectOne("SELECT id,product_code,name,is_beneficiary_required from prd_main WHERE id=:id AND is_deleted='N' and is_beneficiary_required='Y'",array(':id'=>$product_id));
											if(!empty($beneficiary['id'])){
												if(empty($beneficiaryProductsWise)){
													$otherError[] = 'Beneficiary is required';
												}else{
													$beneficiaryAdded = true;
												}
											}
										}
										$other_params = [
											'dep_profiles' => $dep_profile[$product_code],
											'primary_benefit_amount' => $monthly_benefit_amount[$product_id],
											'annual_salary' => $annual_salary,
										];
										$price_details[$product_id] = product_price_detail($primary_profile,$product_id,$product_plan[$product_id],0,$other_params);
									}
									
									if(!empty($price_details)){
										foreach($price_details as $product_id => $productArr){
											if(!empty($productArr['missing_pricing_criteria'])){
												foreach($productArr['missing_pricing_criteria'] as $relation => $errors){
													if(!empty($errors)){
														foreach($errors as $error){
															$otherError[] = $relation .' '. implode(' or<br>', $error). ' is required or not valid for product '.$product_code_id_wise[$product_id];
														}
													}
												}
											}

											if(empty($productArr['valid_rule_id']) && !empty($productArr['pricing_criteria_not_match'])){
												foreach($productArr['pricing_criteria_not_match'] as $relation1 => $errors){
													if(!empty($errors)){
														foreach($errors as $error){
															$otherError[] = $relation1 .' '. implode(' or<br>', $error). ' is required or not valid for product '.$product_code_id_wise[$product_id];
														}
													}
												}
											}
											if(!empty($productArr['error_display'])){
												$otherError[] = $productArr['error_display'] .' '. $product_code_id_wise[$product_id];
											}
											
											if(!empty($productArr['price'])){
												$product_price[$product_id] = $productArr['price'];
												$product_matrix[$product_id] = $productArr['plan_id'];
											}
											if(empty($primary_monthly_salary_percentage[$product_id])){
												$primary_monthly_salary_percentage[$product_id] = $productArr['benefit_amount_percentage'];
											}
										}
									}

									if(!empty($otherError)){
										$errorArr['reason'] = implode(' or<br>', $otherError);
										$pdo->insert("import_csv_log", $errorArr);
										$total_failed_records++;
										continue;
									}
									$params['primary_monthly_salary_percentage'] = $primary_monthly_salary_percentage;
									$params['product_price'] = $product_price;
									$params['product_matrix'] = $product_matrix;
									$result = member_enrollment($params,$errorArr);
									// pre_print($result);
									if($result['status'] == 'account_approved'){
										$total_processed_records++;
										if(count($member_dependents) > 0){
											$total_processed_records = $total_processed_records + count($member_dependents);
										}
									}else{
										$total_failed_records++;
										if(count($member_dependents) > 0){
											$total_failed_records = $total_failed_records + count($member_dependents);
										}
									}
								}
								++$id_key;
							}

						}
					}
		        }	

			}else if($module_type == 'agents' && $import_type == 'add_license'){
				$agent_license_arr = array();

				$total_records = 0;
				$total_processed_records = 0;
				$total_failed_records = 0;

				foreach ($csv_file_rows as $key => $value) {
					$agent_license_arr[] = $value;
				}

				$states = $pdo->select("SELECT name FROM states_c WHERE country_id=:id",array(":id"=>231));
				if(!empty($states)){
					foreach($states as $sname){
						$stateArr[] = $sname['name'];
					}
				}
				// pre_print($stateArr);
				if($agent_license_arr){
					foreach ($agent_license_arr as $agent_id => $value) {
						$error_reporting_arr = array('file_id' => $file_row['id'],'reason' => '','csv_columns' => json_encode($value),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()');
						
						$is_error = false;

						$agent_id_field = $fields['agent_id'];
						$license_expiry_field = $fields["license_expiration"];
						$license_number_field = $fields['license_number'];
						$license_active_field = $fields["license_active"];
						$license_state_field = $fields['license_state'];
						$license_type_field = $fields['license_type'];
						$license_auth_field = $fields['license_of_authority'];

						$agentArr = $pdo->selectOne("SELECT id from customer where rep_id=:rep_id and is_deleted='N'",array(":rep_id"=>$value[$agent_id_field]));

						// pre_print($agentArr);
						if(!empty($agentArr['id'])){

							$agent_id = $agentArr['id'];
							$license_expiry = $value[$license_expiry_field];
							$license_number = $value[$license_number_field];
							$license_active = $value[$license_active_field];
							$license_state = $value[$license_state_field];
							$license_type = $value[$license_type_field];
							$license_auth = $value[$license_auth_field];

							if(!empty($license_auth) && in_array($license_auth,array("general_lines","General Lines","General Lines (Both)","Health","health","Life","life"))){
								if(in_array($license_auth,array('General Lines','General Lines (Both)'))){
									$license_auth = 'general_lines';
								}else{
									$license_auth = $license_auth;
								}
							}

							if(!empty($license_type) && in_array($license_type,array("Agent","Agency","Personal","Business"))){
								if($license_type == 'Agent' || $license_type == 'Personal'){
									$license_type = 'Personal';
								}else if($license_type == 'Agency' || $license_type == 'Business'){
									$license_type = 'Business';
								}
							}

							if(!empty($license_state) && !empty($license_auth) && !empty($license_type)){
								$sch_param = array(
									":agent_id"=>$agent_id,
									":selling_licensed_state"=> $license_state,
									":license_auth"=> $license_auth,
									":license_type"=> $license_type,
								);
	
								$exist_license = $pdo->selectOne("SELECT id from agent_license WHERE agent_id=:agent_id AND selling_licensed_state=:selling_licensed_state AND license_auth=:license_auth AND license_type=:license_type AND is_deleted='N'",$sch_param);
	
								if(!empty($exist_license['id'])){
									$error_reporting_arr['reason'] = "License already exists.";
									$pdo->insert("import_csv_log", $error_reporting_arr);
									$is_error = true;
								}
							}
							
							if(empty($license_expiry)){
								$error_reporting_arr['reason'] = "License expiration date is empty";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}

							if(empty($license_number)){
								$error_reporting_arr['reason'] = "License Number is empty";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}

							if(empty($license_active)){
								$error_reporting_arr['reason'] = "License Active date is empty";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}

							if(empty($license_state)){
								$error_reporting_arr['reason'] = "License state is empty";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}

							if(!empty($license_state) && !in_array($license_state,$stateArr)){
								$error_reporting_arr['reason'] = "License state is not valid.";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}

							if(empty($license_type)){
								$error_reporting_arr['reason'] = "License Type is empty";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}else if(!empty($license_type)  && !in_array($license_type,array("Agent","Agency","Personal","Business"))){
								$error_reporting_arr['reason'] = "License Type is not valid.";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}

							if(empty($license_auth)){
								$error_reporting_arr['reason'] = "License Auth is empty";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}else if(!empty($license_auth) && !in_array($license_auth,array("general_lines","General Lines","General Lines (Both)","Health","health","Life","life"))){
								$error_reporting_arr['reason'] = "Inavalid License Auth.";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}


							if(!$is_error){
								$insertParams = array(
									"agent_id" => $agent_id,
									'selling_licensed_state' => $license_state, 
									'license_num' => $license_number,
									'license_active_date' => date('Y-m-d', strtotime($license_active)),
									'license_type' => $license_type,
									'license_not_expire' => 'N',
									'license_exp_date' => date('Y-m-d', strtotime($license_expiry)),
									'license_auth' => $license_auth,
									'license_added_date' => 'msqlfunc_NOW()',
									'updated_at' => 'msqlfunc_NOW()',
								);
								$pdo->insert('agent_license',$insertParams);
								$total_processed_records++;
							}else{
								$total_failed_records++;
							}
						}else{
							$error_reporting_arr['reason'] = "Agent not found";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$total_failed_records++;
						}
						$total_records++;
					}
				}
			}else if($module_type == 'agents' && $import_type == 'add_appointment'){
				if($csv_file_rows){

					$states = $pdo->select("SELECT name FROM states_c WHERE country_id=:id",array(":id"=>231));
					if(!empty($states)){
						foreach($states as $sname){
							$stateArr[] = $sname['name'];
						}
					}
					// pre_print($csv_file_rows);
					foreach ($csv_file_rows as $key => $value) {

						$error_reporting_arr = array('file_id' => $file_row['id'],'reason' => '','csv_columns' => json_encode($value),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()');

						if(empty($value['AGENT_ID'])){
							$error_reporting_arr['reason'] = "Agent Id is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}

						if(empty($value['CARRIER_ID'])){
							$error_reporting_arr['reason'] = "Carrier Id is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}

						if(empty($value['WRITING_NUMBER'])){
							$error_reporting_arr['reason'] = "Writing Number is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}

						if(empty($value['WRITING_STATE'])){
							$error_reporting_arr['reason'] = "Writing State is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}

						if(!$is_error){
						
							$agentArr = $pdo->selectOne("SELECT id from customer where rep_id=:rep_id and is_deleted='N'",array(":rep_id"=>$value['AGENT_ID']));
							$is_error = false;
							if(!empty($agentArr['id'])){

								$agent_id = $agentArr['id'];

								$carrArr = $pdo->selectOne("SELECT id from prd_fees where display_id=:display_id and is_deleted='N'",array(":display_id"=>$value['CARRIER_ID']));

								if(!empty($carrArr['id'])){

									$carrier_id = $carrArr['id'];

									$sql = "SELECT pf.name,pf.id,pls.sale_type,GROUP_CONCAT(distinct(pls.state_name)) as states 
									FROM agent_product_rule apr
									LEFT JOIN prd_main p on (p.id = apr.product_id)
									LEFT JOIN prd_fees pf on (pf.setting_type = 'Carrier' AND pf.status='Active' AND pf.id=p.carrier_id AND pf.is_deleted = 'N')
									LEFT JOIN prd_assign_fees pa ON (pa.product_id=p.id and pa.prd_fee_id=pf.id AND pa.is_deleted='N')
									LEFT JOIN prd_license_state pls ON(pls.product_id=p.id AND pls.is_deleted='N' )
									WHERE apr.agent_id = :agent_id AND p.is_deleted='N' AND (pf.use_appointments ='Y' OR pls.license_rule='Licensed and Appointed') AND apr.is_deleted = 'N' AND pf.id=:carrier_id group by pf.id";
									$carrier = $pdo->selectOne($sql,array(':agent_id' => $agent_id,":carrier_id"=>$carrier_id));

									if($carrier['id']){

										if(!empty($carrier['states']) && in_array($carrier['sale_type'],array('Just-In-Time','Pre-Sale'))){  
											$states = explode(',',$carrier['states']);
											if(!in_array($value['WRITING_STATE'],$states)){
												$error_reporting_arr['reason'] = "Writing state not Available for product.";
												$pdo->insert("import_csv_log", $error_reporting_arr);
												$total_failed_records++;
												$is_error = true;
											}
										}

										if(!$is_error && !in_array($value['WRITING_STATE'],$stateArr)){
											$error_reporting_arr['reason'] = "Writing state is not valid.";
											$pdo->insert("import_csv_log", $error_reporting_arr);
											$total_failed_records++;
											$is_error = true;
										}

										if(!$is_error){
											$db_value = $pdo->selectOne("SELECT ws.id as id FROM agent_writing_number wn LEFT JOIN agent_writing_states ws ON(ws.writing_id = wn.id AND ws.is_deleted='N') WHERE agent_id=:agent_id AND carrier_id=:c_id and wn.is_deleted='N' AND ws.state=:state ",array(":agent_id"=>$agent_id,':c_id'=>$carrier_id,":state"=>$value['WRITING_STATE']));
											if(!empty($db_value['id'])){
												$error_reporting_arr['reason'] = "Writing state Already Exist.";
												$pdo->insert("import_csv_log", $error_reporting_arr);
												$total_failed_records++;
											}else{
												$wrId = $pdo->selectOne("SELECT id FROM agent_writing_number WHERE agent_id=:agent_id AND carrier_id=:c_id and is_deleted='N'",array(":agent_id"=>$agent_id,':c_id'=>$carrier_id));
												if($wrId['id']){
													$insert_param = array(
														"writing_id" => $wrId['id'],
														"state" => $value['WRITING_STATE']
													);
													$pdo->insert('agent_writing_states',$insert_param);
												}else{
													
													$insert_param = array(
														"agent_id" => $agent_id,
														"carrier_id" => $carrier_id,
														"writing_number" => $value['WRITING_NUMBER'],
													);
													$ins_id = $pdo->insert('agent_writing_number',$insert_param);

													$insert_st_param = array(
														"writing_id" => $ins_id,
														"state" => $value['WRITING_STATE']
													);
													$pdo->insert('agent_writing_states',$insert_st_param);
													$total_processed_records++;
												}
											}
										}

									}else{
										$error_reporting_arr['reason'] = "Carrier product not found for Agent.";
										$pdo->insert("import_csv_log", $error_reporting_arr);
										$total_failed_records++;
									}

								}else{
									$error_reporting_arr['reason'] = "Carrier not found";
									$pdo->insert("import_csv_log", $error_reporting_arr);
									$total_failed_records++;
								}

							}else{
								$error_reporting_arr['reason'] = "Agent not found";
								$pdo->insert("import_csv_log", $error_reporting_arr);
								$total_failed_records++;
							}
						}else{
							$total_failed_records++;
						}
						$total_records++;
					}
				}
			}else if($module_type == 'agents' && $import_type == 'add_direct_deposit'){
				if($csv_file_rows){
					foreach($csv_file_rows as $value){
						
						$validate = new Validation();

						$error_reporting_arr = array('file_id' => $file_row['id'],'reason' => '','csv_columns' => json_encode($value),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()');
						$is_error = false;

						if(empty($value['AGENT_ID'])){
							$error_reporting_arr['reason'] = "Agent Id is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}
						if(empty($value['EFFECTIVE_DATE'])){
							$error_reporting_arr['reason'] = "Effective Date is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}
						if(empty($value['ACCOUNT_TYPE'])){
							$error_reporting_arr['reason'] = "Account Type is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}
						if(empty($value['BANK_NAME'])){
							$error_reporting_arr['reason'] = "Bank Name is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}
						if(empty($value['BANK_ROUTING_NUMBER'])){
							$error_reporting_arr['reason'] = "Bank Routing Number is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}
						if(empty($value['BANK_ACCOUNT_NUMBER'])){
							$error_reporting_arr['reason'] = "Bank Acount Number is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}
						if(empty($value['CONFIRM_BANK_ACCOUNT_NUMBER'])){
							$error_reporting_arr['reason'] = "Confirm Bank Acount Number is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}

						$agentArr = $pdo->selectOne("SELECT id from customer where rep_id=:rep_id and is_deleted='N'",array(":rep_id"=>$value['AGENT_ID']));
						
						if(empty($agentArr['id'])){
							$error_reporting_arr['reason'] = "Agent Not Found.";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}

						if(!$is_error){

							$agent_id = $agentArr['id'];

							if(checkRoutingNumber($value['BANK_ROUTING_NUMBER']) == false) {
								$error_reporting_arr['reason'] = "Valid Routing Number is required.";
								$pdo->insert("import_csv_log", $error_reporting_arr);
								$is_error = true;
							}

							$validate->digit(array('required' => true, 'field' => 'BANK_ACCOUNT_NUMBER', 'value' => $value['BANK_ACCOUNT_NUMBER'],'min'=>5,'max'=>17), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));

							$validate->digit(array('required' => true, 'field' => 'CONFIRM_BANK_ACCOUNT_NUMBER', 'value' => $value['CONFIRM_BANK_ACCOUNT_NUMBER'],'min'=>5,'max'=>17), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));

							if (!$validate->getError('CONFIRM_BANK_ACCOUNT_NUMBER')) {
								if ($value['BANK_ACCOUNT_NUMBER'] != $value['CONFIRM_BANK_ACCOUNT_NUMBER']) {
									$validate->setError('CONFIRM_BANK_ACCOUNT_NUMBER', "Enter same Account Number");
								}
							}

							if ($value['EFFECTIVE_DATE'] != "") {
								if (validateDate($value['EFFECTIVE_DATE'],'m/d/Y')) {
									if (!isFutureDateMain($value['EFFECTIVE_DATE'],'m/d/Y')) {
										$validate->setError("EFFECTIVE_DATE", "Please Add Future Effective Date is required.");
									}
									$sel = "SELECT MAX(effective_date) as dates from direct_deposit_account WHERE customer_id=:customer_id";
									$whr = array(":customer_id" => $agent_id);
									$res = $pdo->selectOne($sel, $whr);
									if(!empty($res)){
										if(date('Y-m-d',strtotime($value['EFFECTIVE_DATE'])) <= date($res['dates'])){
											$validate->setError("EFFECTIVE_DATE", "Please Add Future Effective Date is greater then ".date('m/d/Y',strtotime($res['dates'])));
										}
									}
								} else {
									$validate->setError("EFFECTIVE_DATE", "Valid Effective Date is required");
								}
							}

							if(!in_array(strtolower($value['ACCOUNT_TYPE']),array('saving','checking'))){
								$error_reporting_arr['reason'] = "Invalid Account Type.";
								$pdo->insert("import_csv_log", $error_reporting_arr);
								$is_error = true;
							}
							
							if (count($validate->getErrors()) > 0) {
								$errorArr = $validate->getErrors();
								foreach($errorArr as $error){
									$error_reporting_arr['reason'] = $error;
									$pdo->insert("import_csv_log", $error_reporting_arr);
								}
								$is_error = true;
							}

							if($validate->isValid() && !$is_error)
							{

								$status = date('Y-m-d',strtotime($value['EFFECTIVE_DATE'])) == date('Y-m-d') ? 'Active' : 'Inactive';

								$selDirect = "SELECT id from direct_deposit_account WHERE customer_id=:customer_id order by id desc";
								$whrDirect = array(":customer_id" => $agent_id);
								$resDirect = $pdo->selectOne($selDirect, $whrDirect);

								if (!empty($resDirect['id'])) {
									$termination_date =  date('Y-m-d', strtotime('-1 day', strtotime($value['EFFECTIVE_DATE'])));
									$updateparams = array(
										'termination_date' => $termination_date,
										'updated_at' => 'msqlfunc_NOW()',
									);
									if($status=='Active'){
										$updateparams['status'] = 'Inactive';
									}
									$upd_where = array(
										'clause' => 'id = :id',
										'params' => array(
											':id' => $resDirect['id'],
										),
									);
									$updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
									$update_data = $pdo->update('direct_deposit_account', $updateparams, $upd_where,true);
								}
								
								$insparams = array(
									'customer_id' => $agent_id,
									'bank_name' => $value['BANK_NAME'],
									'account_type' => $value['ACCOUNT_TYPE'],
									'routing_number' => $value['BANK_ROUTING_NUMBER'],
									'account_number' => $value['BANK_ACCOUNT_NUMBER'],
									'effective_date' => date('Y-m-d',strtotime($value['EFFECTIVE_DATE'])),
									'termination_date' => '',
									'status'		=> $status,
									'created_at' => 'msqlfunc_NOW()',
									'updated_at' => 'msqlfunc_NOW()',
								);
								$insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
								$pdo->insert('direct_deposit_account', $insparams);	
								$total_processed_records++;
							}else{
								$total_failed_records++;
							}
						}else{
							$total_failed_records++;
						}
						$total_records++;
						unset($validate);
					}
				}
			}else if($module_type == 'agents' && $import_type == 'add_agents'){
				// pre_print($fields);
				$total_records = 0;
				$total_processed_records = 0;
				$total_failed_records = 0;

				if(!empty($csv_file_rows)){
					$parent_agent_ids = array();
					$allStateShortName = array();
					$allStateFullName = array();
				   	$allStateSqlRes = $pdo->select("SELECT id,name,short_name FROM states_c WHERE country_id = 231 ORDER BY name ASC");
				   	
					foreach ($allStateSqlRes as $key => $value) {
						$allStateShortName[$value['name']] = $value['short_name'];
						$allStateFullName[$value['name']] = $value['name'];
					}
				
					$agentCodedResult = $pdo->select("SELECT * FROM agent_coded_level WHERE is_active='Y' ORDER BY id DESC");
					$tmpAgentCodedRes = array();
					if(!empty($agentCodedResult)){
						foreach ($agentCodedResult as $key => $value) {
							$tmpAgentCodedRes[$value['level_heading']]=$value;
						}
					}

					$parent_agent_id_field = trim($fields['parent_agent_id']);
					$agent_id_field = trim($fields['agent_id']);
					$agent_level_field = trim($fields['agent_level']);
					$account_type_field = trim($fields['account_type']);
					$agency_legalname_field = trim($fields['company_name']);
					$agency_address_field = trim($fields['company_address']);
					$agency_address2_field = trim($fields['company_address_2']);
					$agency_city_field = trim($fields['company_city']);
					$agency_state_field = trim($fields['company_state']);
					$agency_zipcode_field = trim($fields['company_zip']);
					$agency_ein_field = trim($fields['tax_id']);
					$principal_agent_fn_field = trim($fields['fname']);
					$principal_agent_ln_field = trim($fields['lname']);
					$agent_address_field = trim($fields['address']);
					$agent_city_field = trim($fields['city']);
					$agent_state_field = trim($fields['state']);
					$agent_zipcode_field = trim($fields['zip']);
					$agent_ssn_field = trim($fields['ssn']);
					$agent_dob_field = trim($fields['birth_date']);
					$agent_phone_field = trim($fields['cell_phone']);
					$agent_email_field = trim($fields['email']);
					$agent_status_field = trim($fields['status']);
					$agent_primary_merchant_ach_field = trim($fields['payment_master_id']);
					$agent_primary_merchant_cc_field = trim($fields['ach_master_id']);
					$npn_field = trim($fields['npn']);
					$e_o_amount_field = trim($fields['e_o_amount']);
					$e_o_expiration_field = trim($fields['e_o_expiration']);
					$hide_display_field = trim($fields['display_in_member']);
					$display_name_field = trim($fields['public_name']);
					$display_phone_field = trim($fields['public_phone']);
					$display_email_field = trim($fields['public_email']);
					$agent_username_field = trim($fields['username']);
					$agent_custombrand_field = trim($fields['is_branding']);
					$access_enroll_member_field = trim($fields['access_enroll_member']);
					$access_enroll_agent_field = trim($fields['access_enroll_agent']);
					$access_enroll_groups_field = trim($fields['access_enroll_groups']);
					$access_aae_enrollment_website_field = trim($fields['access_aae_enrollment_website']);
					$access_self_enrollment_website_field = trim($fields['access_self_enrollment_Website']);
					$access_bob_agents_field = trim($fields['access_bob_agents']);
					$access_bob_members_field = trim($fields['access_bob_members']);
					$access_bob_groups_field = trim($fields['access_bob_groups']);
					$access_bob_leads_field = trim($fields['access_bob_leads']);
					$access_bob_pending_aae_field = trim($fields['access_bob_pending_aae']);
					$access_prod_reporting_field = trim($fields['access_prod_reporting']);
					$access_prod_commissions_field = trim($fields['access_prod_commissions']);
					$access_prod_orders_field = trim($fields['access_prod_orders']);
					$access_prod_products_field = trim($fields['access_prod_products']);
					$access_prod_transactions_field = trim($fields['access_prod_transactions']);
					$access_resources_email_broadcaster_field = trim($fields['access_resources_email_broadcaster']);
					$access_resources_sms_broadcaster_field = trim($fields['access_resources_sms_broadcaster']);
					$access_resources_communication_queue_field = trim($fields['access_resources_communication_queue']);
					$access_resources_training_field = trim($fields['access_resources_training']);
					$access_resources_support_field = trim($fields['access_resources_support']);
					
					foreach ($csv_file_rows as $key => $agent) {
						$error_reporting_arr = array('file_id' => $file_row['id'],'reason' => '','csv_columns' => json_encode($agent),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()');
						$total_records++;
						// variable initialization code start
							$parent_agent_id = trim($agent[$parent_agent_id_field]);
							$agent_rep_id = trim($agent[$agent_id_field]);
							$agent_level = trim($agent[$agent_level_field]);
							$account_type = trim($agent[$account_type_field]);
							
							$agency_legalname = trim($agent[$agency_legalname_field]);
							$agency_address = trim($agent[$agency_address_field]);
							$agency_address2 = trim($agent[$agency_address2_field]);
							$agency_city = trim($agent[$agency_city_field]);
							$agency_state = trim($agent[$agency_state_field]);
							$agency_zipcode = trim(phoneReplaceMain($agent[$agency_zipcode_field]));
							$agency_ein = trim(phoneReplaceMain($agent[$agency_ein_field]));
							
							$principal_agent_fn = trim($agent[$principal_agent_fn_field]);
							$principal_agent_ln = trim($agent[$principal_agent_ln_field]);
							$agent_address = trim($agent[$agent_address_field]);
							$agent_city = trim($agent[$agent_city_field]);
							$agent_state = trim($agent[$agent_state_field]);
							$agent_zipcode = trim(phoneReplaceMain($agent[$agent_zipcode_field]));

							$agent_ssn = trim(phoneReplaceMain($agent[$agent_ssn_field]));
							$agent_dob = trim($agent[$agent_dob_field]);
							$agent_phone = trim(phoneReplaceMain($agent[$agent_phone_field]));
							$agent_email = trim($agent[$agent_email_field]);
							$agent_status = trim($agent[$agent_status_field]);
							
							$agent_primary_merchant_ach = trim($agent[$agent_primary_merchant_ach_field]);
							$agent_primary_merchant_cc = trim($agent[$agent_primary_merchant_cc_field]);
							
							$npn = trim(phoneReplaceMain($agent[$npn_field]));
							$e_o_amount = str_replace(array("$", ","), array("", ""), trim($agent[$e_o_amount_field]));
							$e_o_expiration = trim($agent[$e_o_expiration_field]);
							
							$hide_display = trim($agent[$hide_display_field]);
							$hide_display = strtolower($hide_display) == "yes" ? "Y" : "N";

							$display_name = trim($agent[$display_name_field]);
							$display_phone = trim(phoneReplaceMain($agent[$display_phone_field]));
							$display_email = trim($agent[$display_email_field]);
							
							$agent_username = trim($agent[$agent_username_field]);
							$agent_custombrand = trim($agent[$agent_custombrand_field]);
							$agent_custombrand = strtolower($agent_custombrand) == "yes" ? "Y" : "N";
							
							$access_enroll_member = trim($agent[$access_enroll_member_field]);
							$access_enroll_agent = trim($agent[$access_enroll_agent_field]);
							$access_enroll_groups = trim($agent[$access_enroll_groups_field]);
							$access_aae_enrollment_website = trim($agent[$access_aae_enrollment_website_field]);
							$access_self_enrollment_website = trim($agent[$access_self_enrollment_website]);
							
							$access_bob_agents = trim($agent[$access_bob_agents_field]);
							$access_bob_members = trim($agent[$access_bob_members_field]);
							$access_bob_groups = trim($agent[$access_bob_groups_field]);
							$access_bob_leads = trim($agent[$access_bob_leads_field]);
							$access_bob_pending_aae = trim($agent[$access_bob_pending_aae_field]);
							
							$access_prod_reporting = trim($agent[$access_prod_reporting_field]);
							$access_prod_commissions = trim($agent[$access_prod_commissions_field]);
							$access_prod_orders = trim($agent[$access_prod_orders_field]);
							$access_prod_products = trim($agent[$access_prod_products_field]);
							$access_prod_transactions = trim($agent[$access_prod_transactions_field]);
							
							$access_resources_email_broadcaster = trim($agent[$access_resources_email_broadcaster_field]);
							$access_resources_sms_broadcaster = trim($agent[$access_resources_sms_broadcaster_field]);
							$access_resources_communication_queue = trim($agent[$access_resources_communication_queue]);
							$access_resources_training = trim($agent[$access_resources_training_field]);
							$access_resources_support = trim($agent[$access_resources_support_field]);
						// variable initialization code ends
						
						$is_error = false;
						$sponsor_row = array();
						if(empty($parent_agent_id)){
							$error_reporting_arr['reason'] = "Parent Agent ID is empty";
			                $pdo->insert("import_csv_log", $error_reporting_arr);
			                $is_error = true;
						}else{
							$sponsor_row = $pdo->selectOne("SELECT id,upline_sponsors,level FROM customer WHERE rep_id = :rep_id",array(":rep_id" => $parent_agent_id));
							// if(empty($sponsor_row)){
							// 	$error_reporting_arr['reason'] = "Parent Agent not found";
				            //     $pdo->insert("import_csv_log", $error_reporting_arr);
				            //     $is_error = true;
							// }
							// $parent_agent_ids[$key] = $parent_agent_id;
						}
						if(empty($agent_rep_id)){
							$error_reporting_arr['reason'] = "Agent ID is empty";
			                $pdo->insert("import_csv_log", $error_reporting_arr);
			                $is_error = true;
						}else{
							$agent_row = $pdo->selectOne("SELECT id FROM customer WHERE rep_id = :rep_id and is_deleted = 'N'",array(":rep_id" => $agent_rep_id));
							if(!empty($agent_row)){
								$error_reporting_arr['reason'] = "Agent ID already exist";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}
						}

						if(empty($agent_level)){
							$error_reporting_arr['reason'] = "Agent Level is empty";
			                $pdo->insert("import_csv_log", $error_reporting_arr);
			                $is_error = true;
						}

						/*---------------- Agent Primary Info Code Start ---------------------*/
							if(empty($principal_agent_fn)){
								$error_reporting_arr['reason'] = "Agent FirstName is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($principal_agent_ln)){
								$error_reporting_arr['reason'] = "Agent LastName is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agent_address)){
								$error_reporting_arr['reason'] = "Agent Address is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agent_city)){
								$error_reporting_arr['reason'] = "Agent City is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agent_state)){
								$error_reporting_arr['reason'] = "Agent State is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agent_zipcode)){
								$error_reporting_arr['reason'] = "Agent Zipcode is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agent_ssn)){
								$error_reporting_arr['reason'] = "Agent SSN is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agent_dob)){
								$error_reporting_arr['reason'] = "Agent DOB is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}else{
								$agent_dob = date('m/d/Y',strtotime($agent_dob));
								list($mm, $dd, $yyyy) = explode('/', $agent_dob);
								if (!checkdate($mm, $dd, $yyyy)) {
									$error_reporting_arr['reason'] = "Valid Agent Date of Birth is required";
				                	$pdo->insert("import_csv_log", $error_reporting_arr);
				                	$is_error = true;
								}else{
									$age_y = dateDifference($agent_dob, '%y');
									if ($age_y < 18) {
										$error_reporting_arr['reason'] = "You must be 18 years of age";
					                	$pdo->insert("import_csv_log", $error_reporting_arr);
					                	$is_error = true;
									} else if ($age_y > 90) {
										$error_reporting_arr['reason'] = "You must be younger then 90 years of age";
					                	$pdo->insert("import_csv_log", $error_reporting_arr);
					                	$is_error = true;
									}
								}
							}

							if(empty($agent_phone)){
								$error_reporting_arr['reason'] = "Agent Phone is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agent_email)){
								$error_reporting_arr['reason'] = "Agent Email is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}else{
								/*$selectEmail = "SELECT id, email, type FROM customer WHERE email = :email AND type IN('Agent','Group') AND is_deleted='N'";
								$whereEmail = array(':email' => $agent_email);
								$resEmail = $pdo->selectOne($selectEmail, $whereEmail);
								if (!empty($resEmail)){
									if($resEmail['type'] == "Agent") {
										$error_reporting_arr['reason'] = "This email is already associated with another agent account";
					                	$pdo->insert("import_csv_log", $error_reporting_arr);
					                	$is_error = true;
									} else {
										$error_reporting_arr['reason'] = "This email is already associated with another group account";
					                	$pdo->insert("import_csv_log", $error_reporting_arr);
					                	$is_error = true;
									}
								}*/
							}

							// if(empty($agent_status)){
							// 	$error_reporting_arr['reason'] = "Agent Status is empty";
			    //             	$pdo->insert("import_csv_log", $error_reporting_arr);
			    //             	$is_error = true;
							// }
						/*---------------- Agent Primary Info Code Ends ---------------------*/


						/*---------------- Agent Public info Code Start -------------------*/
							if($hide_display != "Y"){
								if(empty($display_name)){
									$error_reporting_arr['reason'] = "Display Name is empty";
				                	$pdo->insert("import_csv_log", $error_reporting_arr);
				                	$is_error = true;
								}
								if(empty($display_phone)){
									$error_reporting_arr['reason'] = "Display Phone is empty";
				                	$pdo->insert("import_csv_log", $error_reporting_arr);
				                	$is_error = true;
								}
								if(empty($display_email)){
									$error_reporting_arr['reason'] = "Display Email is empty";
				                	$pdo->insert("import_csv_log", $error_reporting_arr);
				                	$is_error = true;
								}
							}

							if(empty($agent_username)){
								$error_reporting_arr['reason'] = "Agent Username is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}else if(!isValidUserName($agent_username)){
								$error_reporting_arr['reason'] = "Username already exist";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}
						/*---------------- Agent Public info Code Ends --------------------*/
						
						if(empty($account_type)){
								$error_reporting_arr['reason'] = "Account Type is empty";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
						}

						if(strtolower($account_type) == "agency"){
							if(empty($agency_legalname)){
								$error_reporting_arr['reason'] = "Agency Name is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agency_address)){
								$error_reporting_arr['reason'] = "Agency Address is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agency_city)){
								$error_reporting_arr['reason'] = "Agency City is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agency_state)){
								$error_reporting_arr['reason'] = "Agency State is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agency_zipcode)){
								$error_reporting_arr['reason'] = "Agency Zipcode is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}

							if(empty($agency_ein)){
								$error_reporting_arr['reason'] = "Agency EIN is empty";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}
						}

						if(empty($npn)){
							$error_reporting_arr['reason'] = "NPN Number is empty";
			                $pdo->insert("import_csv_log", $error_reporting_arr);
			                $is_error = true;
						}
						if(empty($e_o_amount)){
							$error_reporting_arr['reason'] = "E&O Amount is empty";
			                $pdo->insert("import_csv_log", $error_reporting_arr);
			                $is_error = true;
						}

						if(empty($e_o_expiration)){
							$error_reporting_arr['reason'] = "E&O Expiration Date is empty";
			                $pdo->insert("import_csv_log", $error_reporting_arr);
			                $is_error = true;
						}else{
							$e_o_expiration = date('m/d/Y',strtotime($e_o_expiration));
							if (validateDate($e_o_expiration,'m/d/Y')) {
								// if (!isFutureDateMain($e_o_expiration,'m/d/Y')) {
								// 	$error_reporting_arr['reason'] = "Future Expiration Date is required";
					   //              $pdo->insert("import_csv_log", $error_reporting_arr);
					   //              $is_error = true;
								// }
							} else {
								$error_reporting_arr['reason'] = "Valid Expiration Date is required";
				                $pdo->insert("import_csv_log", $error_reporting_arr);
				                $is_error = true;
							}
						}

						$agentAchMerchantId = 0;
						$agentCcMerchantId = 0;

						
						if(!empty($agent_primary_merchant_ach)){
							$selProcessor = "SELECT id FROM payment_master WHERE merchant_id=:merchantId AND is_deleted='N'";
							$whereParams = array(":merchantId" => $agent_primary_merchant_ach);
							$resProcessor = $pdo->selectOne($selProcessor,$whereParams);
							if(empty($resProcessor["id"])){
								$error_reporting_arr['reason'] = "Agent Primary ACH Merchant not found";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}else{
								$agentAchMerchantId = $resProcessor["id"];
							}
						}
						
						
						if(!empty($agent_primary_merchant_cc)){
							$selProcessor = "SELECT id FROM payment_master WHERE merchant_id=:merchantId AND is_deleted='N'";
							$whereParams = array(":merchantId" => $agent_primary_merchant_cc);
							$resProcessor = $pdo->selectOne($selProcessor,$whereParams);
							if(empty($resProcessor["id"])){
								$error_reporting_arr['reason'] = "Agent Primary CC Merchant not found";
			                	$pdo->insert("import_csv_log", $error_reporting_arr);
			                	$is_error = true;
							}else{
								$agentCcMerchantId = $resProcessor["id"];
							}
						}

						/*-------------- Feature access code start -----------------------*/
						$accessArr = array();

						if(strtolower($access_enroll_member) == "write"){
							array_push($accessArr, 2);
							array_push($accessArr, 1);
						}
						if(strtolower($access_enroll_agent) == "write"){
							array_push($accessArr, 3);
							array_push($accessArr, 1);
						}
						if(strtolower($access_enroll_groups) == "write"){
							array_push($accessArr, 4);
							array_push($accessArr, 1);
						}

						if(strtolower($access_aae_enrollment_website) == "write"){
							array_push($accessArr, 24);
							array_push($accessArr, 5);
						}
						if(strtolower($access_self_enrollment_website) == "write"){
							array_push($accessArr, 25);
							array_push($accessArr, 5);
						}

						if(strtolower($access_bob_agents) == "write"){
							array_push($accessArr, 7);
							array_push($accessArr, 6);
						}
						if(strtolower($access_bob_members) == "write"){
							array_push($accessArr, 8);
							array_push($accessArr, 6);
						}
						if(strtolower($access_bob_groups) == "write"){
							array_push($accessArr, 9);
							array_push($accessArr, 6);
						}
						if(strtolower($access_bob_leads) == "write"){
							array_push($accessArr, 10);
							array_push($accessArr, 6);
						}
						if(strtolower($access_bob_pending_aae) == "write"){
							array_push($accessArr, 11);
							array_push($accessArr, 6);
						}
						
						if(strtolower($access_prod_commissions) == "write"){
							array_push($accessArr, 13);
							array_push($accessArr, 12);
						}
						if(strtolower($access_prod_products) == "write"){
							array_push($accessArr, 14);
							array_push($accessArr, 12);
						}
						if(strtolower($access_prod_orders) == "write"){
							array_push($accessArr, 15);
							array_push($accessArr, 12);
						}
						if(strtolower($access_prod_reporting) == "write"){
							array_push($accessArr, 16);
							array_push($accessArr, 12);
						}
						if(strtolower($access_prod_transactions) == "write"){
							array_push($accessArr, 17);
							array_push($accessArr, 12);
						}


						if(strtolower($access_resources_email_broadcaster) == "write"){
							array_push($accessArr, 19);
							array_push($accessArr, 18);
						}
						if(strtolower($access_resources_sms_broadcaster) == "write"){
							array_push($accessArr, 20);
							array_push($accessArr, 18);
						}
						if(strtolower($access_resources_communication_queue) == "write"){
							array_push($accessArr, 21);
							array_push($accessArr, 18);
						}
						if(strtolower($access_resources_training) == "write"){
							array_push($accessArr, 22);
							array_push($accessArr, 18);
						}
						if(strtolower($access_resources_support) == "write"){
							array_push($accessArr, 23);
							array_push($accessArr, 18);
						}

						$accessArr = array_unique($accessArr);
						/*-------------- Feature access code ends -----------------------*/
						
						/*---------- Insert Agent Data Code Start --------------*/
						if(!$is_error){
							$password = $principal_agent_fn.''.date("Y",strtotime($agent_dob));

							$sponsor_id = $sponsor_row["id"];
							$upline_sponsors = $sponsor_row["upline_sponsors"] . $sponsor_id .',';
							$level = $sponsor_row["level"] + 1;

							$account_type = strtolower($account_type) == "agency" ? "Business" : "Personal";
							$custParams = array(
								'company_id' => 3,
								'display_id' => get_display_id(),
								'rep_id' => $agent_rep_id,
								'password' => "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')",

								'fname' => $principal_agent_fn,
								'lname' => $principal_agent_ln,
								'cell_phone' => $agent_phone,
								'email' => $agent_email,
								'birth_date' => date("Y-m-d",strtotime($agent_dob)),

								'address' => $agent_address,
								'country_id' => '231',
								'country_name' => "United States",
								'city' => $agent_city,
								'state' => $allStateFullName[$agent_state],
								'zip' => $agent_zipcode,
								
								'public_name' => $display_name,
								'public_email' => $display_email,
								'public_phone' => $display_phone,
								'user_name' => $agent_username,

								'payment_master_id' => $agentCcMerchantId,
								'ach_master_id' => $agentAchMerchantId,

								"sponsor_id" => $sponsor_id ? $sponsor_id : 0,
								'upline_sponsors' => $upline_sponsors ? $upline_sponsors : "",
								'level' => $level,

								'type' => 'Agent',
								'status' => $agent_status ? $agent_status : "Active",
								'access_type' => "full_access",
								'feature_access' => implode(",", $accessArr),
								'joined_date' => 'msqlfunc_NOW()',
								'invite_at' => 'msqlfunc_NOW()',
								'created_at' => 'msqlfunc_NOW()',
							);

					   		if ($agent_ssn != "") {
								$custParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . $agent_ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
								$custParams['last_four_ssn'] = substr($agent_ssn, -4);
							}

							if ($account_type == "Business") {
					   			$custParams["business_name"] = $agency_legalname;
							}

							if(!empty($e_o_expiration)){
								$custParams["eo_coverage"] = "Y";
							}

							$agentId = $pdo->insert("customer",$custParams);

							if(empty($sponsor_id)){
								$parent_agent_ids[$agentId] = array('parent_agent_rep_id' => $parent_agent_id,'error_reporting' => $error_reporting_arr);
							}

							$desc = array();
				            $desc['ac_message'] = array(
				                'ac_red_1' => array(
				                    'href' => 'agent_detail_v1.php?id=' . md5($agentId) ,
				                    'title' => $custParams['rep_id'],
				                ) ,
				                'ac_message_1' => ' added by Import ',
				            );
				            activity_feed(3, $agentId, 'Agent', $agentId, 'Agent', 'Agent added by Import', $custParams["fname"], $custParams["lname"], json_encode($desc));

				            $agent_coded_id =  $tmpAgentCodedRes[$agent_level]['id'];
				            $agent_coded_level =  $tmpAgentCodedRes[$agent_level]['level'];

				            $customerSettings = array(
								'customer_id' => $agentId,
								'npn' => $npn,
								'display_in_member' => $hide_display,
								'is_branding' => $agent_custombrand,
								'agent_coded_id' => $agent_coded_id,
								'agent_coded_level' => $agent_coded_level,
								'agent_coded_profile'=>1,
								'account_type' => $account_type,
							);

							if ($account_type == "Business") {
					   			$customerSettings["company_name"] = $agency_legalname;
					   			$customerSettings["company_address"] = $agency_address;
					   			$customerSettings["company_address_2"] = $agency_address2;
					   			$customerSettings["company_country_id"] = makesafe('231');
					   			$customerSettings["company_country_name"] = makeSafe("United States");
					   			$customerSettings["company_state"] = $allStateFullName[$agency_state];
					   			$customerSettings["company_city"] = $agency_city;
					   			$customerSettings["company_zip"] = $agency_zipcode;
					   			$customerSettings["tax_id"] = $agency_ein;
							}
							$agentSettingsId = $pdo->insert("customer_settings",$customerSettings);

							if(!empty($e_o_expiration)){
								$insDocument = array(
									'agent_id' => $agentId,
									'e_o_coverage' => "Y",
									'e_o_amount' => $e_o_amount,
									'e_o_expiration' => date('Y-m-d', strtotime($e_o_expiration)),
									'created_at' => 'msqlfunc_NOW()',
								);
								$agentDocId = $pdo->insert('agent_document', $insDocument);
							}

							//Assign Merchant Processor start
								$processor = $pdo->selectOne("SELECT GROUP_CONCAT(id) as ids from payment_master where is_assigned_to_all_agent='Y' AND is_deleted='N'");
								if(!empty($processor['ids'])){
									$processorArr = explode(',',$processor['ids']);
									if(!empty($processorArr)){
										foreach($processorArr as $id){
											$pdo->insert('payment_master_assigned_agent',array("agent_id"=>$agentId,"payment_master_id"=>$id));
										}
									}
								}
								if(!empty($sponsor_id)){
									$typeIncr = '';		
									if($agent_coded_level == 'LOA'){
										$typeIncr= " AND (include_downline='Y' OR loa_only='Y')";
									}else{
										$typeIncr= " AND (include_downline='Y')";
									}
									$processorRes = $pdo->selectOne("SELECT GROUP_CONCAT(res.payment_master_id) as ids from payment_master p JOIN (
										SELECT GROUP_CONCAT(distinct(payment_master_id)) as payment_master_id from payment_master_assigned_agent WHERE agent_id=:sponsor_id AND is_deleted='N' ".$typeIncr.") res ON (p.id IN(res.payment_master_id)) AND p.is_deleted='N'",array(":sponsor_id"=>$sponsor_id));
									if(!empty($processorRes['ids'])){
										$processorArrRes = explode(',',$processorRes['ids']);
										if(!empty($processorArrRes)){
											foreach($processorArrRes as $id){
												$prRes = $pdo->selectOne("SELECT id from payment_master_assigned_agent where payment_master_id=:payment_master_id AND agent_id=:agent_id AND is_deleted='N'",array("agent_id"=>$agent_id,"payment_master_id"=>$id));
												if(empty($prRes['id'])){
													$pdo->insert('payment_master_assigned_agent',array("agent_id"=>$agent_id,"payment_master_id"=>$id));
												}
											}
										}
									}
								}
							//Assign Merchant Processor end

							$total_processed_records++;
						}else{
							$total_failed_records++;
						}
					/*---------- Insert Agent Data Code Ends --------------*/
					
					}

					if($parent_agent_ids){
						foreach ($parent_agent_ids as $agent_id => $parent_agent) {
							$check_parent_agent = $pdo->selectOne("SELECT id,upline_sponsors FROM customer WHERE rep_id = :rep_id AND type = 'Agent' and is_deleted = 'N'",array(':rep_id' => $parent_agent['parent_agent_rep_id']));

							if($check_parent_agent){
								$update_params = array(
						            'sponsor_id' => $check_parent_agent['id'],
						            'upline_sponsors' => $check_parent_agent["upline_sponsors"] . $check_parent_agent['id'] .','
						        );
								
						        $update_where = array(
						            'clause' => 'id = :id',
						            'params' => array(
						                ':id' => makeSafe($agent_id)
						            )
						        );
							}else{

								$pdo->delete('DELETE FROM customer WHERE id = :id',array(":id" => $agent_id));
								$pdo->delete('DELETE FROM customer_settings WHERE customer_id = :id',array(":id" => $agent_id));
								$pdo->delete('DELETE FROM agent_document WHERE agent_id = :id',array(":id" => $agent_id));

								$parent_agent['error_reporting']['reason'] = "Parent Agent not found";
				                $pdo->insert("import_csv_log", $parent_agent['error_reporting']);

								$total_processed_records--;
								$total_failed_records++;
							}
						}
					}

				}
			}else if($module_type == 'agents' && $import_type == 'add_e_o_coverage'){
				$total_records = 0;
				$total_processed_records = 0;
				$total_failed_records = 0;
				if($csv_file_rows){
					foreach($csv_file_rows as $value){
						$total_records++;
						$e_o_amount = 0;
						$e_o_expiration = "";

						$error_reporting_arr = array('file_id' => $file_row['id'],'reason' => '','csv_columns' => json_encode($value),'updated_at' => 'msqlfunc_NOW()','created_at' => 'msqlfunc_NOW()');
						$is_error = false;

						if(empty($value['AGENT_ID'])){
							$error_reporting_arr['reason'] = "Agent Id is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}else{
							$agentArr = $pdo->selectOne("SELECT id from customer where rep_id=:rep_id and is_deleted='N'",array(":rep_id"=>$value['AGENT_ID']));
							if(empty($agentArr)){
								$error_reporting_arr['reason'] = "Agent Not Found";
								$pdo->insert("import_csv_log", $error_reporting_arr);
								$is_error = true;
							}
						}
						if(empty($value['E_O_AMOUNT'])){
							$error_reporting_arr['reason'] = "E&O Amount is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}else{
							$e_o_amount = str_replace(array('$',','), array('',''), $value['E_O_AMOUNT']);
							
							if(!is_numeric($e_o_amount)){
								$error_reporting_arr['reason'] = "E&O Amount is invalid";
								$pdo->insert("import_csv_log", $error_reporting_arr);
								$is_error = true;
							}
						}
						if(empty($value['E_O_EXPIRATION'])){
							$error_reporting_arr['reason'] = "E&O Expiration is empty";
							$pdo->insert("import_csv_log", $error_reporting_arr);
							$is_error = true;
						}else{
							$date = $value['E_O_EXPIRATION'];
							$format = 'd/m/Y';
							$d = DateTime::createFromFormat($format, $date);
							if($d && $d->format($format) !== $date){
								$error_reporting_arr['reason'] = "E&O Expiration is invalid";
								$pdo->insert("import_csv_log", $error_reporting_arr);
								$is_error = true;
							}
							$date = DateTime::createFromFormat('d/m/Y', $date)->format('Y-m-d');
						}

						if(!$is_error){
							
							$agent_document = $pdo->selectOne("SELECT agent_id,e_o_amount,e_o_expiration FROM agent_document WHERE agent_id = :agent_id",array(':agent_id' => $agentArr['id']));

							if(empty($agent_document)){
								$insDocument = array(
									'agent_id' => $agentArr['id'],
									'e_o_coverage' => "Y",
									'e_o_amount' => $e_o_amount,
									'e_o_expiration' => $date,
									'created_at' => 'msqlfunc_NOW()',
								);
								$agentDocId = $pdo->insert('agent_document', $insDocument);
							}else{
								$update_params = array(
						            'e_o_coverage' => "Y",
									'e_o_amount' => $e_o_amount,
									'e_o_expiration' => $date,
									'updated_at' => 'msqlfunc_NOW()',
						        );
								
						        $update_where = array(
						            'clause' => 'agent_id = :id',
						            'params' => array(
						                ':id' => makeSafe($agentArr['id'])
						            )
						        );

						        $pdo->update('agent_document',$update_params,$update_where);
							}
							$total_processed_records++;
						}else{
							$total_failed_records++;
						}
					}
				}
			}

			$csv_where = array(
	            "clause" => "id=:id",
	            "params" => array(
	                ":id" => $file_row['id']
	            )
	        );
	        $pdo->update('import_requests',array('is_running' => 'N','status'=>'complete','total_records' =>$total_records,'total_processed_records' => $total_processed_records,'total_failed_records' =>$total_failed_records,'updated_at' => 'msqlfunc_NOW()'), $csv_where);

	    }
	}	
} else {
    echo "All Processed";
}
dbConnectionClose();
function csvToArraywithFields($filename)
{   
    if(file_exists($filename)) {
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
    } else {
        $rowsWithKeys = array();        
    }
    return $rowsWithKeys;
}

?>