<?php 
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();

$response = array();
$validate = new Validation();

$enrollmentLocation = !empty($_POST['enrollmentLocation']) ? $_POST['enrollmentLocation'] : '';
$is_group_member = !empty($_POST['is_group_member']) ? $_POST['is_group_member'] : 'N';
$is_add_product = !empty($_POST['is_add_product']) ? $_POST['is_add_product'] : '';
$pb_id = !empty($_POST['pb_id']) ? $_POST['pb_id'] : 0;
$check_open_enrollment = isset($_POST['check_open_enrollment']) ? false : true;

$today_date=date('Y-m-d');
$sponsor_id = $_POST['sponsor_id'];
$primary_fname = !empty(trim($_POST['primary_fname'])) ? trim($_POST['primary_fname']) : '';
$primary_zip = !empty(trim($_POST['primary_zip'])) ? trim($_POST['primary_zip']) : '';
$primary_gender = !empty(trim($_POST['primary_gender'])) ? trim($_POST['primary_gender']) : '';
$primary_birthdate = !empty($_POST['primary_birthdate']) ? $_POST['primary_birthdate'] : '';
$primary_email = !empty(trim($_POST['primary_email'])) ? trim($_POST['primary_email']) : '';
$customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : 0;
$lead_id = !empty($_POST['lead_id']) ? $_POST['lead_id'] : 0;

$spouse_dependent = isset($_POST['spouse_fname']) ? $_POST['spouse_fname'] : NULL;
$child_dependent = isset($_POST['tmp_child_fname']) ? $_POST['tmp_child_fname'] : array();

$already_puchase_product=array();
$inactive_products = array();
$restricted_products = array();
$rule_error_array = array();
$combination_products = array();

$found_state_id = 0;
$groupCoverageContributionArr=array();

if($enrollmentLocation=='groupSide' || $is_group_member == 'Y'){
	$coverage_period = !empty($_POST['coverage_period']) ? $_POST['coverage_period'] : '';
	$enrolle_class = !empty($_POST['hdn_enrolle_class']) ? $_POST['hdn_enrolle_class'] : '';
	$relationship_to_group = !empty($_POST['hdn_relationship_to_group']) ? $_POST['hdn_relationship_to_group'] : '';
	$relationship_date = !empty($_POST['relationship_date']) ? $_POST['relationship_date'] : '';

	$validate->string(array('required' => true, 'field' => 'coverage_period', 'value' => $coverage_period), array('required' => 'Coverage Period is required'));
	$validate->string(array('required' => true, 'field' => 'enrolle_class', 'value' => $enrolle_class), array('required' => 'Enrollee Class is required'));
	$validate->string(array('required' => true, 'field' => 'relationship_to_group', 'value' => $relationship_to_group), array('required' => 'Relationship to group is required'));
	$validate->string(array('required' => true, 'field' => 'relationship_date', 'value' => $relationship_date), array('required' => 'Relationship Date is required'));
	

	$sqlCoveragePeriod="SELECT gcc.*,gc.pay_period FROM group_coverage_period_offering gco 
		JOIN group_classes gc ON gc.id=gco.class_id 
		JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N') 
		where gco.is_deleted='N' AND gco.status='Active' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id AND CURDATE()>=gco.open_enrollment_start AND CURDATE()<=gco.open_enrollment_end";
	$sqlCoveragePeriodWhere=array(':group_id'=>$sponsor_id,':class_id'=>$enrolle_class,':group_coverage_period_id'=>$coverage_period);
	$resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);


	$sqlAssignCoverage="SELECT * FROM leads_assign_coverage where lead_id=:lead_id AND group_coverage_period_id = :coverage_period AND is_deleted='N'";
	$resAssignCoverage=$pdo->select($sqlAssignCoverage,array(":lead_id"=>$lead_id,":coverage_period"=>$coverage_period));

	$sqlCoverage = "SELECT id FROM group_coverage_period_offering where is_deleted='N' AND status='Active' AND group_id=:group_id AND group_coverage_period_id =:coverage_period AND class_id=:class_id";
	$sqlCoverageWhere=array(':group_id'=>$sponsor_id,':class_id'=>$enrolle_class,':coverage_period'=>$coverage_period);
	$resCoverage = $pdo->select($sqlCoverage,$sqlCoverageWhere);

	if(empty($resCoverage)){
		$validate->setError("enrolle_class","No Class Found For This Coverage");
	}

	if(empty($resCovergaePeriod) && empty($resAssignCoverage) && $check_open_enrollment){
		$validate->setError("coverage_period","Open Enrollment is closed");
	}else{
		$sqlCoveragePeriod="SELECT gcc.*,gc.pay_period 
			FROM group_coverage_period_offering gco 
			JOIN group_classes gc ON (gc.id=gco.class_id and gc.is_deleted='N') 
			LEFT JOIN group_coverage_period_contributions gcc on(gcc.group_coverage_period_offering_id=gco.id AND gcc.is_deleted='N')
			where gco.is_deleted='N' AND gco.status='Active' AND gco.group_coverage_period_id=:group_coverage_period_id AND gco.group_id=:group_id AND gco.class_id=:class_id";
		$sqlCoveragePeriodWhere=array(':group_id'=>$sponsor_id,':class_id'=>$enrolle_class,':group_coverage_period_id'=>$coverage_period);
		$resCovergaePeriod=$pdo->select($sqlCoveragePeriod,$sqlCoveragePeriodWhere);
		if(!empty($resCovergaePeriod)) {
			foreach ($resCovergaePeriod as $key => $value) {
				$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['type']=$value['type'];
				$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['contribution']=$value['con_value'];
				$groupCoverageContributionArr[$value['product_id']][$value['plan_id']]['pay_period']=$value['pay_period'];
				$groupCoverageContributionArr['pay_period']['pay_period']=$value['pay_period'];
			}
		}
	}
}

//$validate->string(array('required' => true, 'field' => 'primary_fname', 'value' => $primary_fname), array('required' => 'First Name is required'));
$validate->string(array('required' => true, 'field' => 'primary_zip', 'value' => $primary_zip), array('required' => 'Zip Code is required'));
$validate->string(array('required' => true, 'field' => 'primary_gender', 'value' => $primary_gender), array('required' => 'Gender is required'));
$validate->string(array('required' => true, 'field' => 'primary_birthdate', 'value' => $primary_birthdate), array('required' => 'DOB is required'));
$validate->email(array('required' => true, 'field' => 'primary_email', 'value' => $primary_email), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));

if(!$validate->getError('primary_birthdate')){
	if(!empty($primary_birthdate) && strtotime($primary_birthdate) >= strtotime($today_date)){
		$validate->setError("primary_birthdate","Please Enter Valid Birthdate");
	}
}

if(!$validate->getError('primary_zip')){
	$zipRes=$pdo->selectOne("SELECT id,state_code FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$primary_zip));

	if(empty($zipRes)){
		$validate->setError('primary_zip', 'Zip code is not valid');
	}else{
		$response['zip'] = $primary_zip;

		$stateRes=$pdo->selectOne("SELECT id FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$zipRes['state_code']));

		if(empty($stateRes)){
			$validate->setError('primary_zip', 'Zip code is not valid');
		}else{
			$found_state_id = $stateRes['id'];
		}
	}
}

if(!$validate->getError('primary_email')){
	$extra_params = array();
	$extra_params['location'] = $enrollmentLocation;
	$extra_params['site_user_name'] = (isset($_POST['site_user_name'])?$_POST['site_user_name']:'');
	$extra_params['is_add_product'] = $is_add_product;
	$email_error = $MemberEnrollment->validate_existing_email($primary_email,$sponsor_id,$customer_id,$lead_id,$extra_params);
	if($email_error['status'] == "fail") {
		$validate->setError("primary_email",$email_error['message']);
		$response['existing_email'] = $primary_email;
		$response['existing_status'] = $email_error['existing_status'];
		$response['enrollment_url'] = $email_error['enrollment_url'];
	}
}

if (!$validate->getError('primary_birthdate')) {
	list($mm, $dd, $yyyy) = explode('/', $primary_birthdate);

	if (!checkdate($mm, $dd, $yyyy)) {
		$validate->setError('primary_birthdate', 'Valid Date of Birth is required');
	}
}

if($validate->isValid()){

	$incr ="";
	$mat_incr ="";

	$sch_params = array();
	$mat_sch_params = array();

	$mat_incr.= " AND (pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date is null))";
	$mat_sch_params[":today_date"]=$today_date;

	$incr.= " AND (pm.pricing_effective_date <= :today_date AND (pm.pricing_termination_date >= :today_date OR pm.pricing_termination_date is null))";
	$sch_params[":today_date"]=$today_date;

	$incr.=" AND (apr.agent_id=:agent_id OR p.product_type='Admin Only Product')";
	$sch_params[":agent_id"]=$sponsor_id;

	if(!empty($customer_id)){

		//************** Already Purchase Product will  not display **************
			$already_puchase_product = $MemberEnrollment->getAlreadyPurchasedProducts($customer_id);
		//************** Already Purchase Product will  not display **************
		

		//************** Purchased Core Product Other Core Product will  not display **************
			$PurchasedCoreProductsData = $MemberEnrollment->getAlreadyPurchasedCoreProducts($customer_id);
			$purchaseCoreProducts = $PurchasedCoreProductsData['purchaseCoreProducts'];
		//************** Purchased Core Product Other Core Product will  not display **************


		//******** Already Purchase Products Restricted Product will  not display ********	
			if(!empty($already_puchase_product)){
				foreach ($already_puchase_product as $key => $productRow) {
					
					$sqlProduct="SELECT combination_product_id as restricted_products FROM prd_combination_rule WHERE product_id=:product_id AND combination_type='Excludes' AND is_deleted='N'";
					$resProduct=$pdo->select($sqlProduct,array(":product_id"=>$productRow));
					if(!empty($resProduct)){
						foreach ($resProduct as $key => $row) {
							array_push($restricted_products,$row['restricted_products']);
						}							
					}
				}
			}
		//******** Already Purchase Products Restricted Product will  not display ********	
		
		if(count($already_puchase_product) > 0){
			$list_already_puchase_product = implode(",", $already_puchase_product);
			$incr.=" AND p.id not in ($list_already_puchase_product)";
			$response['already_puchase_product']=$already_puchase_product;
		}

		if(count($restricted_products) > 0){
			$list_restricted_products = implode(",", $restricted_products);
			$incr.=" AND p.id not in ($list_restricted_products)";
			$response['restricted_products']=$restricted_products;
		}

		if(!empty($purchaseCoreProducts)){
			$incr.= " AND p.main_product_type!='Core Product' ";
			$response['core_products']=true;
		}

		$addOnDisplay = $MemberEnrollment->addOnDisplay($customer_id);
	}

	if(in_array($enrollmentLocation, array('agentSide','aae_site'))){
		$incr.=" AND p.product_type in ('Direct Sale Product','Add On Only Product')";
	}else if($enrollmentLocation == "groupSide"){
		$incr.=" AND p.product_type in ('Group Enrollment','Add On Only Product')";
	
	} else if($enrollmentLocation == "adminSide") {
		if($is_group_member == 'Y') {
			$incr.=" AND p.product_type in ('Admin Only Product','Group Enrollment','Add On Only Product')";
		} else {
			$incr.=" AND p.product_type in ('Admin Only Product','Direct Sale Product','Add On Only Product')";
		}
	}

	if(!empty($pb_id)) {
		$pb_sql = "SELECT pg.product_ids
            FROM page_builder pg 
            LEFT JOIN page_builder_images pi ON (pi.id = pg.cover_image) 
            WHERE pg.is_deleted='N' AND pg.status='Active' AND pg.id=:id";
		$pb_row = $pdo->selectOne($pb_sql,array(":id"=>$pb_id));

		if(!empty($pb_row['product_ids'])) {
			$incr .= " AND p.id IN (".$pb_row['product_ids'].")";		
		}
	}

	$productsSql="SELECT p.id as p_product_id,p.name as product_name,p.company_id as product_company,p.parent_product_id as primary_product_id,p.product_code,p.is_license_require,p.is_primary_age_restrictions,p.primary_age_restrictions_from,p.primary_age_restrictions_to,p.is_specific_zipcode,
		p.type as product_type,p.is_add_on_product,p.license_type,p.license_rule,p.reenroll_options,p.reenroll_within,p.reenroll_within_type,p.family_plan_rule,
		if(p.is_add_on_product='Y','Add-On Only',pc.title) as category_name,
		if(p.is_add_on_product='Y','addOnCategory',p.category_id) as product_category_id,
		pf.name as carrier_name,p.is_short_term_disablity_product,p.monthly_benefit_allowed,p.percentage_of_salary,IF(p.payment_type='Recurring',p.payment_type_subscription,'One Time') as member_payment_type
		FROM prd_main p
		JOIN prd_matrix pm ON(pm.product_id = p.id)
		LEFT JOIN agent_product_rule apr ON (p.id=apr.product_id AND apr.status ='Contracted' AND apr.is_deleted='N')
		JOIN prd_category pc ON (pc.id=p.category_id)
		JOIN prd_fees pf ON (pf.id = p.carrier_id)
		WHERE p.status='Active' AND p.type!='Fees' AND p.is_deleted='N' $incr 
		GROUP BY p.id
		ORDER BY category_name,p.order_by ASC";
	$productsRes=$pdo->select($productsSql,$sch_params);

	$licenseFullExpired=$MemberEnrollment->isFullLicenseExpired($sponsor_id);
	
	$product_list = array();
	$is_main_products = false;
	if(!empty($productsRes)){
		$p_product_ids = array_column($productsRes,'p_product_id');
		$p_product_ids_str = implode(',',$p_product_ids);

		$getStateCode=$pdo->selectOne("SELECT state_code from zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$primary_zip));
		if($getStateCode){
			$pricing_control_State = getname("states_c",$getStateCode['state_code'],"name","short_name");

			$sqlSetting="SELECT aws.state FROM agent_writing_number awn
					JOIN agent_writing_states aws ON (awn.id = aws.writing_id AND aws.is_deleted='N')
					WHERE awn.is_deleted='N' and awn.agent_id=:agent_id AND aws.state=:state_name";
			$resSetting=$pdo->select($sqlSetting,array(":agent_id"=>$sponsor_id,":state_name"=>$pricing_control_State));
		}

		$tmpProductsRes = array();
		$assignedQuestionRes = array();
		$getProductLicenseRes = array();
		$restricted_state_date = date('Y-m-d');
		$restrictedStateResByPrd = array();
		$resAgentStateByPrd = array();
		$zipCodeResByPrd = array();
		$tmpCombinationProductsByPrd = array();
		$enrolleeCoverageByPrd = array();
		
		$productsMatSql="SELECT pm.id as matrix_id,pm.product_id as p_product_id,pm.plan_type,pm.enrollee_type,pm.pricing_model,pm.pricing_effective_date,pm.pricing_termination_date,pm.price,pm.commission_amount,pm.non_commission_amount,pmc.age_from,pmc.age_to,pmc.state,pmc.zipcode,pmc.gender
					FROM prd_matrix pm
					LEFT JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.is_deleted='N')
					WHERE pm.is_deleted='N' AND pm.product_id IN(".$p_product_ids_str.") $mat_incr";
		$productsMatRes = $pdo->select($productsMatSql,$mat_sch_params);
		$productsMatResByPrd = array();
		if(!empty($productsMatRes)) {
			foreach ($productsMatRes as $productsMatRow) {
				if(isset($productsMatResByPrd[$productsMatRow['p_product_id']])) {
					$productsMatResByPrd[$productsMatRow['p_product_id']][] = $productsMatRow;
				} else {
					$productsMatResByPrd[$productsMatRow['p_product_id']] = array($productsMatRow);
				}
			}
		}

		if(!empty($pricing_control_State)) {
			$restrictedStateSql="SELECT GROUP_CONCAT(distinct product_id) as restrictedStateProduct,product_id FROM prd_no_sale_states WHERE state_name=:state AND is_deleted='N' AND effective_date <= :restricted_state_date AND (termination_date >= :restricted_state_date OR termination_date IS NULL) AND product_id IN(".$p_product_ids_str.") GROUP BY product_id";
			$restrictedStateRes=$pdo->select($restrictedStateSql,array(":state"=>$pricing_control_State,":restricted_state_date"=>$restricted_state_date));
			if(!empty($restrictedStateRes)) {
				foreach ($restrictedStateRes as $restrictedStateRow) {
					$restrictedStateResByPrd[$restrictedStateRow['product_id']] = $restrictedStateRow;
				}
			}

			$sqlAgentState="SELECT id,product_id FROM agent_assign_state where agent_id=:sponsor_id AND product_id IN(".$p_product_ids_str.") AND state=:state AND is_deleted='N' GROUP BY product_id";
			$resAgentState=$pdo->select($sqlAgentState,array(":state"=>$pricing_control_State,":sponsor_id"=>$sponsor_id));
			if(!empty($resAgentState)) {
				foreach ($resAgentState as $AgentStateRow) {
					$resAgentStateByPrd[$AgentStateRow['product_id']] = $AgentStateRow;
				}
			}
		}

		$zipCodeSql="SELECT id,product_id FROM prd_specific_zipcode WHERE zipcode=:zipcode AND product_id IN(".$p_product_ids_str.") AND is_deleted='N' GROUP BY product_id";
		$zipCodeRes=$pdo->select($zipCodeSql,array(":zipcode"=>$primary_zip));
		if(!empty($zipCodeRes)) {
			foreach ($zipCodeRes as $key => $zipCodeRow) {
				$zipCodeResByPrd[$zipCodeRow['product_id']]=$zipCodeRow;
			}
		}

		foreach ($productsRes as $key => $product_row) {
			if(!empty($productsMatResByPrd[$product_row['p_product_id']])) {
				foreach ($productsMatResByPrd[$product_row['p_product_id']]  as $productsMatRow) {
					$tmpProductsRes[] = array_merge($product_row,$productsMatRow);
				}
			}
		}
		$productsRes = $tmpProductsRes;

		$age_from_birthdate=calculateAge($primary_birthdate);

		$getActiveLicensedStatesRes = array();
		$getActiveLicensedStatesRes['All'] = $MemberEnrollment->getActiveLicensedStates($sponsor_id);
		$getActiveLicensedStatesRes['Health_Life'] = $MemberEnrollment->getActiveLicensedStates($sponsor_id,'Health,Life');
		$getActiveLicensedStatesRes['Life'] = $MemberEnrollment->getActiveLicensedStates($sponsor_id,'Life');
		$getActiveLicensedStatesRes['Health'] = $MemberEnrollment->getActiveLicensedStates($sponsor_id,'Health');

		foreach ($productsRes as $key => $product_row) {
			
			$is_rule_valid = true;
			$rule_error ="";

			//$assignedQuestion=$MemberEnrollment->getPriceAssignedQuestion($product_row['p_product_id']);
			if(!isset($assignedQuestionRes[$product_row['p_product_id']])) {
				$assignedQuestionRes[$product_row['p_product_id']]=$MemberEnrollment->getPriceAssignedQuestion($product_row['p_product_id']);
			}
			$assignedQuestion=$assignedQuestionRes[$product_row['p_product_id']];

			$checkExtraQuestion = array();
			if(!empty($assignedQuestion) && !empty($assignedQuestion['Primary'])){
				$keys=array_keys($assignedQuestion['Primary']);
				$checkExtraQuestion=array_fill_keys($keys,'');
				unset($checkExtraQuestion[1]);
				unset($checkExtraQuestion[2]);
				unset($checkExtraQuestion[3]);
				unset($checkExtraQuestion[4]);
			}
			
			
			//**************************** License Rule Code Start **********************
				if($product_row['is_license_require']=='Y' && ($enrollmentLocation != "groupSide" && $is_group_member == 'N')){
					//$getActiveLicensedStates=$MemberEnrollment->getActiveLicensedStates($sponsor_id,$product_row['license_type']);
					if(!empty($product_row['license_type'])) {
						$license_type_arr = explode(",", $product_row['license_type']);
						if (in_array("Health", $license_type_arr) && in_array("Life", $license_type_arr)) {
							$getActiveLicensedStates=$getActiveLicensedStatesRes['Health_Life'];	
						
						} else if (in_array("Health", $license_type_arr)) {
							$getActiveLicensedStates=$getActiveLicensedStatesRes['Health'];	

						} else if (in_array("Life", $license_type_arr)) {
							$getActiveLicensedStates=$getActiveLicensedStatesRes['Life'];	
						}
					} else {
						$getActiveLicensedStates=$getActiveLicensedStatesRes['All'];
					}
					
					
					if($product_row['license_rule']=='Licensed Only'){
						if(empty($getActiveLicensedStates)){
							$is_rule_valid = false;
							$rule_error = "Licensed Only Error";
						}
					}else if($product_row['license_rule']=='Licensed in Sale State'){
						if(!empty($pricing_control_State)){
							if(!in_array($pricing_control_State, $getActiveLicensedStates)){
								$is_rule_valid = false;	
								$rule_error = "Licensed in Sale State Error";
							}
						}else{
							$is_rule_valid = false;	
							$rule_error = "License Error";
						}
					}else if($product_row['license_rule']=='Licensed and Appointed'){
						if(!empty($pricing_control_State)){
							if(!in_array($pricing_control_State, $getActiveLicensedStates)){
								$is_rule_valid = false;	
								$rule_error = "Licensed and Appointed Error";
							}
						}else{
							$is_rule_valid = false;	
							$rule_error = "License Error";
						}

						//$getProductLicense=$MemberEnrollment->getProductLicensedStates($product_row['p_product_id'],$product_row['license_rule']);
						if(!isset($getProductLicenseRes[$product_row['p_product_id']])) {
							$getProductLicenseRes[$product_row['p_product_id']] = $MemberEnrollment->getProductLicensedStates($product_row['p_product_id'],$product_row['license_rule']);
						}
						$getProductLicense=$getProductLicenseRes[$product_row['p_product_id']];
						
						$licensed_in_PreSale =!(empty($getProductLicense)) ? $getProductLicense['Pre-Sale'] : array();

						if(!empty($licensed_in_PreSale) && in_array($pricing_control_State, $licensed_in_PreSale)){
							if(empty($resSetting)){
								$is_rule_valid = false;
								$rule_error = "is_writing_number Error";
							}
						}
					}else if($product_row['license_rule']=='Licensed in Specific States Only'){

						//$getProductLicense=$MemberEnrollment->getProductLicensedStates($product_row['p_product_id'],$product_row['license_rule']);
						if(!isset($getProductLicenseRes[$product_row['p_product_id']])) {
							$getProductLicenseRes[$product_row['p_product_id']] = $MemberEnrollment->getProductLicensedStates($product_row['p_product_id'],$product_row['license_rule']);
						}
						$getProductLicense=$getProductLicenseRes[$product_row['p_product_id']];

						$licensed_in_specific_state =!(empty($getProductLicense)) ? $getProductLicense['Specific'] : array();

						$isLicenseInSpecific=array();
						if(!empty($licensed_in_specific_state)){
							foreach ($licensed_in_specific_state as $lKey => $lValue) {
								if(in_array($lValue,$getActiveLicensedStates)){
									array_push($isLicenseInSpecific,"true");
								}else{
									array_push($isLicenseInSpecific,"false");
								}
							}
						}else{
							array_push($isLicenseInSpecific,"false");
						}

						if(!in_array("true", $isLicenseInSpecific)){
							$is_rule_valid = false;	
							$rule_error = "Licensed in Specific State Error";
						}
					}
				}
			//**************************** License Rule Code End   **********************

			//**************************** Age Rule Code Start **********************
				if(!empty($primary_birthdate)){
					if(in_array($product_row['pricing_model'],array('VariableEnrollee','VariablePrice')) && !empty($assignedQuestion['Primary']) && array_key_exists(1, $assignedQuestion['Primary'])) {
						if($product_row['pricing_model'] == "VariablePrice" && $product_row['age_from']>=0 &&  $product_row['age_to']>0 && ($product_row['age_from'] > $age_from_birthdate || $product_row['age_to'] < $age_from_birthdate)){
							$is_rule_valid = false;
							$rule_error = "Age Error";
						}
						if($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary"  && $product_row['age_from']>=0 &&  $product_row['age_to']>0 && ($product_row['age_from'] > $age_from_birthdate || $product_row['age_to'] < $age_from_birthdate)){
							$is_rule_valid = false;
							$rule_error = "Age Error";
						}
					}
					if($product_row['is_primary_age_restrictions']=='Y' && ($product_row['primary_age_restrictions_from'] > $age_from_birthdate || $product_row['primary_age_restrictions_to'] < $age_from_birthdate)){
						$is_rule_valid = false;
						$rule_error = "is_primary_age_restrictions Error";
					}
				}
			//**************************** Age Rule Code End   **********************

			//**************************** state Rule Code Start **********************
				if(!empty($pricing_control_State)){
					if(in_array($product_row['pricing_model'],array('VariableEnrollee','VariablePrice')) && !empty($assignedQuestion['Primary']) && array_key_exists(2, $assignedQuestion['Primary'])) {
						if($product_row['pricing_model'] == "VariablePrice" && $product_row['state']!="" && $product_row['state'] != $pricing_control_State){
							$is_rule_valid = false;
							$rule_error = "state Error";
						}
						if($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary"  && $product_row['state']!="" && $product_row['state'] != $pricing_control_State){
							$is_rule_valid = false;
							$rule_error = "state Error";
						}
					}

					$restrictedStateRes = (isset($restrictedStateResByPrd[$product_row['p_product_id']])?$restrictedStateResByPrd[$product_row['p_product_id']]:array());
					if(!empty($restrictedStateRes['restrictedStateProduct'])){

						$restrictedStateArray = explode(",", $restrictedStateRes['restrictedStateProduct']);

						if(in_array($product_row['p_product_id'],$restrictedStateArray)){
							$is_rule_valid = false;
							$rule_error = "restricted state Error";
						}
					}

					$resAgentState = (isset($resAgentStateByPrd[$product_row['p_product_id']])?$resAgentStateByPrd[$product_row['p_product_id']]:array());
					if(!empty($resAgentState)){
						$is_rule_valid = false;
						$rule_error = "restricted agent state Error";
					}
				}
			//**************************** state Rule Code End   **********************
			
			//**************************** zip code Rule Code Start **********************
				if(!empty($primary_zip)){
					if(in_array($product_row['pricing_model'],array('VariableEnrollee','VariablePrice')) && !empty($assignedQuestion['Primary']) && array_key_exists(3, $assignedQuestion['Primary'])) {

						if($product_row['pricing_model'] == "VariablePrice" && $product_row['zipcode'] !="" && $product_row['zipcode'] != $primary_zip){
							$is_rule_valid = false;
							$rule_error = "Zip Error";
						}
						if($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary" && $product_row['zipcode'] !="" && $product_row['zipcode'] != $primary_zip){
							$is_rule_valid = false;
							$rule_error = "Zip Error";
						}
					}

					if($product_row['is_specific_zipcode']=='Y'){
						$zipCodeRes = (isset($zipCodeResByPrd[$product_row['p_product_id']])?$zipCodeResByPrd[$product_row['p_product_id']]:array());
						if(empty($zipCodeRes)){
							$is_rule_valid = false;
							$rule_error = "Specific Zip Error";							
						}	
					}
				}
			//**************************** zip code Rule Code End   **********************
			
			//**************************** Gender Rule Code start **********************
				if(!empty($primary_gender)){
					if(in_array($product_row['pricing_model'],array('VariableEnrollee','VariablePrice')) && !empty($assignedQuestion['Primary']) && array_key_exists(4, $assignedQuestion['Primary'])) {
						if($product_row['pricing_model'] == "VariablePrice" && $product_row['gender'] !="" && $product_row['gender'] != $primary_gender){
							$is_rule_valid = false;
							$rule_error = "Gender Error";
						}
						if($product_row['pricing_model'] == "VariableEnrollee" && $product_row['enrollee_type'] == "Primary" && $product_row['gender'] !="" && $product_row['gender'] != $primary_gender){
							$is_rule_valid = false;
							$rule_error = "Gender Error";
						}
					}
				}
			//**************************** Gender Rule Code End   **********************

			$response['is_rule_valid']=$is_rule_valid;
			$rule_error_array[$product_row['matrix_id']] = $rule_error;
			
			if($is_rule_valid){
				$defaultPlan = '';
				$family_plan_rule = $product_row['family_plan_rule'];
				if(!isset($spouse_dependent) && empty($child_dependent)){
				 	$defaultPlan=1;
				}else if(isset($spouse_dependent) && empty($child_dependent)){
					$defaultPlan=3;
					if($product_row['plan_type']==5 && $product_list[" ".$product_row['p_product_id']]['default_plan_id']!=4){
						$defaultPlan=5;
					}
					if($family_plan_rule=="Minimum One Dependent"){
						$defaultPlan=4;
					}
				}else if(!isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) == 1){
					$defaultPlan=2;
					if($product_row['plan_type']==5 && $product_list[" ".$product_row['p_product_id']]['default_plan_id']!=4){
						$defaultPlan=5;
					}
					if($family_plan_rule=="Minimum One Dependent"){
						$defaultPlan=4;
					}
				}else if(isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) == 1){
					$defaultPlan=4;
				}else if(isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) >= 2){
					$defaultPlan=4;					
				}else if(!isset($spouse_dependent) && !empty($child_dependent) && count($child_dependent) >= 2){
					$defaultPlan=2;
					if($family_plan_rule=="Minimum Two Dependent" || $family_plan_rule == "Minimum One Dependent"){
						$defaultPlan=4;
					}
				}
				//$tmpCombinationProducts = $MemberEnrollment->getCombinationProducts($product_row['p_product_id'],$sponsor_id);
				if(!isset($tmpCombinationProductsByPrd[$product_row['p_product_id']])) {
					$tmpCombinationProductsByPrd[$product_row['p_product_id']] = $MemberEnrollment->getCombinationProducts($product_row['p_product_id'],$sponsor_id);
				}
				$tmpCombinationProducts = $tmpCombinationProductsByPrd[$product_row['p_product_id']];

				if(isset($tmpCombinationProducts[$product_row['p_product_id']]['Packaged'])){
					$packaged_prd_not_purchased = true;
					if(count($already_puchase_product) > 0) {
						$tmp_packaged_prd_ids = explode(',',$tmpCombinationProducts[$product_row['p_product_id']]['Packaged']['product_id']);
						foreach ($tmp_packaged_prd_ids as $packaged_prd_id) {
							if(in_array($packaged_prd_id,$already_puchase_product))	 {
								$packaged_prd_not_purchased = false;
								break;
							}
						}
					}

					if($packaged_prd_not_purchased == true) {
						$combination_products[$product_row['p_product_id']]['Packaged'] = $tmpCombinationProducts[$product_row['p_product_id']]['Packaged'];
					}
				}
				
				$product_code = $product_row['product_code'];
				$category_id = $product_row['product_category_id'];
				$company_id = $product_row['product_company'];
				$category_name = $product_row['category_name'];
				$product_id = $product_row['p_product_id'];
				$product_type = $product_row['product_type'];
				$product_name = $product_row['product_name'];
				$parent_product_id = $product_row['primary_product_id'];
				$matrix_id = $product_row['matrix_id'];
				$product_price = $product_row['price'];
				$plan_id = $product_row['plan_type'];
				$enrollee_type = !empty($product_row['enrollee_type']) ? $product_row['enrollee_type'] : '';
				$carrier_name = $product_row['carrier_name'];
				$pricing_model = $product_row['pricing_model'];
				$is_short_term_disablity_product = $product_row['is_short_term_disablity_product'];
				$monthly_benefit_allowed = $product_row['monthly_benefit_allowed'];
				$percentage_of_salary = $product_row['percentage_of_salary'];
				$member_payment_type = $product_row['member_payment_type'];
				if(empty($checkExtraQuestion) && $pricing_model=="VariablePrice"){
					$pricing_model = "FixedPrice";
				}

				if($pricing_model == "FixedPrice" && $is_short_term_disablity_product == 'Y'){
					$pricing_model = "VariablePrice";
				}
				
				$plan_name = !empty($plan_id) ? $prdPlanTypeArray[$plan_id]['title'] : '';
				$is_default_plan = 'N';
				$priceType=!empty($plan_id) ? $plan_id : '';
				
				if($plan_id==$defaultPlan){
					$is_default_plan = 'Y';
				}
				$is_add_on_product = isset($product_row['is_add_on_product'])?$product_row['is_add_on_product']:"";

				$is_main_products = true;

				$product_list[" ".$product_id]['category_id']=$category_id;
				$product_list[" ".$product_id]['category_name']=$category_name;
				$product_list[" ".$product_id]['default_plan_id']=$defaultPlan;
				$product_list[" ".$product_id]['product_id']=$product_id;
				$product_list[" ".$product_id]['product_name']=$product_name;
				$product_list[" ".$product_id]['product_code']=$product_code;
				$product_list[" ".$product_id]['parent_product_id']=$product_name;
				$product_list[" ".$product_id]['company_id']=$company_id;
				$product_list[" ".$product_id]['product_type']=$product_type;
				$product_list[" ".$product_id]['is_add_on_product']=$is_add_on_product;

				$product_list[" ".$product_id]['carrier_name']=$carrier_name;
				$product_list[" ".$product_id]['pricing_model']=$pricing_model;
				$product_list[" ".$product_id]['is_short_term_disablity_product']=$is_short_term_disablity_product;
				$product_list[" ".$product_id]['monthly_benefit_allowed']=$monthly_benefit_allowed;
				$product_list[" ".$product_id]['percentage_of_salary']=$percentage_of_salary;
				$product_list[" ".$product_id]['member_payment_type']=$member_payment_type;

				if(!empty($enrollee_type)){
					if(!isset($product_list[" ".$product_id]['Enrollee_Matrix'])){
						
						//$enrolleeCoverage=$MemberEnrollment->getProductCoverageOptions($product_row['p_product_id']);
						if(!isset($enrolleeCoverageByPrd[$product_row['p_product_id']])) {
							$enrolleeCoverageByPrd[$product_row['p_product_id']] = $MemberEnrollment->getProductCoverageOptions($product_row['p_product_id']);
						}
						$enrolleeCoverage=$enrolleeCoverageByPrd[$product_row['p_product_id']];

						if(!empty($enrolleeCoverage)){
							foreach ($enrolleeCoverage as $ecKey => $ecValue) {
								$product_list[" ".$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['matrix_id']=$ecValue['plan_id'];
								$product_list[" ".$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['product_price']='0.00';
								$product_list[" ".$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['display_member_price']='0.00';
								$product_list[" ".$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['plan_id']=$ecValue['plan_id'];
								$product_list[" ".$product_id]['Enrollee_Matrix'][$ecValue['plan_id']]['plan_name']=$ecValue['plan_name'];
							}
						}
					}
				}
				if(!empty($plan_id) && (!isset($product_list[" ".$product_id]['Matrix'][$plan_id]['product_price']) || $product_list[" ".$product_id]['Matrix'][$plan_id]['product_price'] < $product_price)){
					$product_list[" ".$product_id]['Matrix'][$plan_id]['matrix_id']=$matrix_id;
					if(isset($groupCoverageContributionArr) && $groupCoverageContributionArr){
						$tmp_contribution_value = isset($groupCoverageContributionArr[$product_id][$matrix_id]) ? $groupCoverageContributionArr[$product_id][$matrix_id] : null;
						if(isset($tmp_contribution_value) || !empty($groupCoverageContributionArr['pay_period'])){
							$tmp_group_coverage_contribution = !empty($tmp_contribution_value) ? $tmp_contribution_value : $groupCoverageContributionArr['pay_period'];
							$calculatedPrice=$MemberEnrollment->calculateGroupContributionPrice($product_price,$tmp_group_coverage_contribution,false);
							$product_list[" ".$product_id]['Matrix'][$plan_id]['product_price']=$product_price;
							$product_list[" ".$product_id]['Matrix'][$plan_id]['member_price']=$calculatedPrice['member_price'];
							$product_list[" ".$product_id]['Matrix'][$plan_id]['display_member_price']=$calculatedPrice['display_member_price'];
							$product_list[" ".$product_id]['Matrix'][$plan_id]['group_price']=$calculatedPrice['group_price'];
							$product_list[" ".$product_id]['Matrix'][$plan_id]['display_group_price']=$calculatedPrice['display_group_price'];
						}else{
							$product_list[" ".$product_id]['Matrix'][$plan_id]['product_price']=$product_price;
							$product_list[" ".$product_id]['Matrix'][$plan_id]['display_member_price']=$product_price;
							$product_list[" ".$product_id]['Matrix'][$plan_id]['member_price']=0;
							$product_list[" ".$product_id]['Matrix'][$plan_id]['group_price']=0;
							$product_list[" ".$product_id]['Matrix'][$plan_id]['display_group_price']=0;
						}
					}else{
						$product_list[" ".$product_id]['Matrix'][$plan_id]['product_price']=$product_price;
						$product_list[" ".$product_id]['Matrix'][$plan_id]['display_member_price']=$product_price;
						$product_list[" ".$product_id]['Matrix'][$plan_id]['member_price']=0;
						$product_list[" ".$product_id]['Matrix'][$plan_id]['group_price']=0;
						$product_list[" ".$product_id]['Matrix'][$plan_id]['display_group_price']=0;
					}
					$product_list[" ".$product_id]['Matrix'][$plan_id]['plan_id']=$plan_id;
					$product_list[" ".$product_id]['Matrix'][$plan_id]['plan_name']=$plan_name;
				}
			}
		}
	}
	$response['rule_error']=$rule_error_array;

	if(!empty($product_list)){
		$response['product_list'] = $product_list;
		$response['product_list_count'] = count($product_list);
	}
	if(!empty($combination_products)){
		$response['combination_products'] = $combination_products;
	}
	$response['primary_email'] = $primary_email;
	$response['primary_state'] = $found_state_id;
	$response['is_main_products'] = $is_main_products;
	$response['is_add_product'] = $is_add_product;
	$response['status'] = 'success';
	$response['purchaseCoreProducts'] = !empty($purchaseCoreProducts)?"true":"false";
	$response['addOnDisplay'] = !empty($addOnDisplay)?$addOnDisplay:"true";
} else {
	$response['status'] = 'fail';
	$errors = $validate->getErrors();
	$response['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;