<?php
	ini_set('memory_limit', '-1');
	ini_set('max_execution_time', 30000);
	include_once 'layout/start.inc.php';
	include_once __DIR__ . '/../includes/function.class.php';
	require_once dirname(__DIR__) . '/includes/redisCache.class.php';
	$redisCache = new redisCache();
	$functionsList = new functionsList();
	 
	$response=array();
	$validate = new Validation();
	$step1Validation = new Validation();
	$step2Validation = new Validation();
	$step3Validation = new Validation();
	$step4Validation = new Validation();

	$exit_with_error="false";
	$step = $_POST['dataStep'];
	$prdStep = 0;

	$div_step_error = "";
	$product_id = !empty($_POST['product_id']) ? $_POST['product_id']  : 0;
	$is_clone = !empty($_POST['is_clone']) ? $_POST['is_clone'] : 'N';
	$parent_product_id = !empty($_POST['parent_product_id']) ? $_POST['parent_product_id'] : 0;
	$matchGlobal = !empty($_POST['matchGlobal']) ? $_POST['matchGlobal'] : array();
	$matchGlobalList = !empty($matchGlobal) ? implode(",", $matchGlobal) : '';
	if(empty($parent_product_id)){
		$record_type = 'Primary';
		$matchGlobal = array();
	}else{
		$record_type = 'Variation';
	}
	
	$manage_product_id = !empty($_POST['manage_product_id']) ? $_POST['manage_product_id'] : 0;

	
	$submit_type = $_POST['submit_type'];
	$groupEnrollmentPrd = $_POST['groupEnrollmentPrd'];

	$response['step']=$step;
	$response['submit_type']=$submit_type;

	$departmentArray= array();
	
	$sqlPrdStep="SELECT dataStep,id,parent_product_id,record_type,product_code FROM prd_main where md5(id)=:id";
	if(!empty($parent_product_id) && empty($product_id)){
		$resPrdStep=$pdo->selectOne($sqlPrdStep,array(":id"=>$parent_product_id));

		$parent_product_id = $resPrdStep['id'];
		$product_id = 0;
		
	}else{
		$resPrdStep=$pdo->selectOne($sqlPrdStep,array(":id"=>$manage_product_id));

		if($resPrdStep){
			$product_id = $resPrdStep['id'];
			$parent_product_id = $resPrdStep['parent_product_id'];

		}
	}
		

	if($is_clone == 'Y'){
		$product_id = 0;
		$step = $resPrdStep['dataStep'];
	}
	if($resPrdStep){
		$prdStep = $resPrdStep['dataStep'];
		$response['dataStep']=$prdStep;
	}
	$prdAssignQuestion = array();
	$allowPricingUpdate = !empty($_POST['allowPricingUpdate']) ? $_POST['allowPricingUpdate'] : false;
	//********** step1 varible intialization code start **********************
		$product_name = isset($_POST['product_name'])?$_POST['product_name']:"";
		$product_code = isset($_POST['product_code'])?$_POST['product_code']:"";
		
		$product_type  = isset($_POST['product_type'])?$_POST['product_type']:"";
		
		$company_id  = isset($_POST['company_id'])?$_POST['company_id']:"";
		$company_name  = isset($_POST['company_name'])?$_POST['company_name']:"";
		
		$category_id  = isset($_POST['category_id'])? $_POST['category_id']:'';
		$category_name  = isset($_POST['category_name'])?$_POST['category_name']:"";
		
		$product_carrier  = isset($_POST['product_carrier'])?$_POST['product_carrier']:"";
		
		$product_plan_code = isset($_POST['product_plan_code'])?$_POST['product_plan_code']:array();
		$enrollmentPage = isset($_POST['enrollmentPage'])?$_POST['enrollmentPage']:'';

		$department_name = isset($_POST['department_name'])?$_POST['department_name']:array();
		$department_desc = isset($_POST['department_desc'])?$_POST['department_desc']:array();
		
		$agent_portal = isset($_POST['agent_portal'])?$_POST['agent_portal']:"";
		$agentInfoProductBox = !empty($_POST['agentInfoProductBox'])? implode(",",$_POST['agentInfoProductBox']):"";

		$limitations_exclusions = isset($_POST['limitations_exclusions'])?$_POST['limitations_exclusions']:"";	
		
		$main_product_type = isset($_POST['main_product_type'])?$_POST['main_product_type']:"";	
		$is_life_insurance_product = isset($_POST['is_life_insurance_product'])?$_POST['is_life_insurance_product']:"";	
		$life_term_type = isset($_POST['life_term_type'])?$_POST['life_term_type']:"";	
		$guarantee_issue_amount_type = isset($_POST['guarantee_issue_amount_type'])?$_POST['guarantee_issue_amount_type']:array();	
		$primary_issue_amount = isset($_POST['primary_issue_amount'])?str_replace(',','',$_POST['primary_issue_amount']):"";	
		$spouse_issue_amount = isset($_POST['spouse_issue_amount'])?str_replace(',','',$_POST['spouse_issue_amount']):"";	
		$child_issue_amount = isset($_POST['child_issue_amount'])?str_replace(',','',$_POST['child_issue_amount']):"";	
		$is_spouse_issue_amount_larger = isset($_POST['is_spouse_issue_amount_larger'])?$_POST['is_spouse_issue_amount_larger']:"";	
		$is_short_term_disablity_product = isset($_POST['is_short_term_disablity_product']) ? $_POST['is_short_term_disablity_product'] : "";
		$monthly_benefit_allowed = isset($_POST['monthly_benefit_allowed']) ? str_replace(',','',$_POST['monthly_benefit_allowed']) : "";
		$percentage_of_salary = isset($_POST['percentage_of_salary']) ? str_replace(',','',$_POST['percentage_of_salary']) : "";

		$deduction = isset($_POST['deduction']) ? $_POST['deduction'] : "";
		$is_gap_plus_product = isset($_POST['is_gap_plus_product']) ? $_POST['is_gap_plus_product'] : "";
		$annual_hrm_payment = (!empty($_POST['annual_hrm_payment'])?$_POST['annual_hrm_payment']:array());
		if(!empty($prdPlanTypeArray)) {
			foreach ($prdPlanTypeArray as $key => $tier) {
				$annual_hrm_payment[$tier['id']] = (isset($annual_hrm_payment[$tier['id']]) ? str_replace(',','',$annual_hrm_payment[$tier['id']]) : "");					
			}
		}
		$is_require_out_of_pocket_maximum = isset($_POST['is_require_out_of_pocket_maximum']) ? $_POST['is_require_out_of_pocket_maximum'] : "";
		$is_benefit_amount_limit = isset($_POST['is_benefit_amount_limit']) ? $_POST['is_benefit_amount_limit'] : "";
		$minimum_benefit_amount = isset($_POST['minimum_benefit_amount']) ? $_POST['minimum_benefit_amount'] : "";
		$maximum_benefit_amount = isset($_POST['maximum_benefit_amount']) ? $_POST['maximum_benefit_amount'] : "";
		$is_set_default_out_of_pocket_maximum = isset($_POST['is_set_default_out_of_pocket_maximum']) ? $_POST['is_set_default_out_of_pocket_maximum'] : "";
		$default_out_of_pocket_maximum = isset($_POST['default_out_of_pocket_maximum']) ? $_POST['default_out_of_pocket_maximum'] : "";
		$gap_home_savings_recommend_text = isset($_POST['gap_home_savings_recommend_text']) ? $_POST['gap_home_savings_recommend_text'] : "";
		$gap_custom_recommendation_text = isset($_POST['gap_custom_recommendation_text']) ? $_POST['gap_custom_recommendation_text'] : "";
	//********** step1 varible intialization code end   **********************

	//********** step2 varible intialization code start **********************
		$direct_product = isset($_POST['direct_product'])?$_POST['direct_product']:"";
		$effective_day = isset($_POST['effective_day'])?$_POST['effective_day']:"";
		$effective_day2 = isset($_POST['effective_day2'])?$_POST['effective_day2']:"";
		$sold_day = isset($_POST['sold_day'])?$_POST['sold_day']:"";

		$is_membership_require = isset($_POST['is_membership_require'])?$_POST['is_membership_require']:"";
		$membership_ids_check_array = array();
		if($is_membership_require=='Y'){
			$membership_ids_check_array = empty($_POST['membership_ids']) ? array() : $_POST['membership_ids'];
		}
		
		
		
		
		if($product_type=="Association"){
			$is_membership_require='N';
		}
		
		

		$available_state_array = empty($_POST['available_state']) ? array() : $_POST['available_state'];


		$available_state = '';
		if(count($available_state_array) > 0){
			$available_state = implode(",", $available_state_array);
		}

		$available_no_sale_state = isset($_POST['available_no_sale_state'])?$_POST['available_no_sale_state']:array();

		$is_specific_zipcode = isset($_POST['is_specific_zipcode'])?$_POST['is_specific_zipcode']:"";
		
		$available_zipcode_list = array();
		$zipcode_allow_only_state = array();
		$available_state_zipcode = array();
		if($is_specific_zipcode=='Y'){
			$zipcode_allow_only_state = isset($_POST['zipcode_allow_only_state'])?$_POST['zipcode_allow_only_state']:array();
			$available_state_zipcode = isset($_POST['available_state_zipcode'])?$_POST['available_state_zipcode']:array();

		}

		$no_sale_state_coverage_continue = isset($_POST['no_sale_state_coverage_continue'])?$_POST['no_sale_state_coverage_continue']:"";

		$coverage_options_array = empty($_POST['coverage_options']) ? array() : $_POST['coverage_options'];
		if(!$allowPricingUpdate){
			$coverage_options_array = empty($_POST['allow_coverage_options']) ? array() : $_POST['allow_coverage_options'];	
		}

		$family_plan_rule = isset($_POST['family_plan_rule'])?$_POST['family_plan_rule']:"";

		$sub_product_array = empty($_POST['sub_product']) ? array() : $_POST['sub_product'];

		$excludeProduct_array = empty($_POST['excludeProduct']) ? array() : $_POST['excludeProduct'];
		$requiredProduct_array = empty($_POST['requiredProduct']) ? array() : $_POST['requiredProduct'];
		$autoAssignProduct_array = empty($_POST['autoAssignProduct']) ? array() : $_POST['autoAssignProduct'];
		$packagedProduct_array = empty($_POST['packagedProduct']) ? array() : $_POST['packagedProduct'];
		
		if(!empty($excludeProduct_array)){
			$exclude_variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $excludeProduct_array)."') OR parent_product_id in('".implode("','", $excludeProduct_array)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
			if($exclude_variations){
				$excludeProduct_array = explode(',', $exclude_variations['product_ids']);
			}
		}

		if(!empty($requiredProduct_array)){
			$required_variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $requiredProduct_array)."') OR parent_product_id in('".implode("','", $requiredProduct_array)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
			if($required_variations){
				$requiredProduct_array = explode(',', $required_variations['product_ids']);
			}
		}


		if(!empty($autoAssignProduct_array)){
			$sql = "SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $autoAssignProduct_array)."') OR parent_product_id in('".implode("','", $autoAssignProduct_array)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'";
			$autoassign_variations = $pdo->selectOne($sql);
			if($autoassign_variations){
				$autoAssignProduct_array = explode(',', $autoassign_variations['product_ids']);
			}
		}

		if(!empty($packagedProduct_array)){
			$pakaged_variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $packagedProduct_array)."') OR parent_product_id in('".implode("','", $packagedProduct_array)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
			if($pakaged_variations){
				$packagedProduct_array = explode(',', $pakaged_variations['product_ids']);
			}
		}
		
		$termination_rule = isset($_POST['termination_rule'])?$_POST['termination_rule']:"";
		
		$term_back_to_effective = isset($_POST['term_back_to_effective'])?$_POST['term_back_to_effective']:"";

		$term_automatically = isset($_POST['term_automatically'])?$_POST['term_automatically']:"";
		$term_automatically_within = isset($_POST['term_automatically_within'])?$_POST['term_automatically_within']:"";
		$term_automatically_within_type = isset($_POST['term_automatically_within_type'])?$_POST['term_automatically_within_type']:"";
		
		$reinstate_option = isset($_POST['reinstate_option'])?$_POST['reinstate_option']:"";
		$reinstate_within = isset($_POST['reinstate_within'])?$_POST['reinstate_within']:"";
		$reinstate_within_type = isset($_POST['reinstate_within_type'])?$_POST['reinstate_within_type']:"";
		
		$reenroll_options = isset($_POST['reenroll_options'])?$_POST['reenroll_options']:"";
		$reenroll_within = isset($_POST['reenroll_within'])?$_POST['reenroll_within']:"";
		$reenroll_within_type = isset($_POST['reenroll_within_type'])?$_POST['reenroll_within_type']:"";
	//********** step2 varible intialization code end   **********************
	
	//********** step3 varible intialization code start **********************
		$is_primary_age_restrictions = isset($_POST['is_primary_age_restrictions'])?$_POST['is_primary_age_restrictions']:"";
		$primary_age_restrictions_from = isset($_POST['primary_age_restrictions_from'])?$_POST['primary_age_restrictions_from']:"";
		$primary_age_restrictions_to = isset($_POST['primary_age_restrictions_to'])?$_POST['primary_age_restrictions_to']:"";

		$is_spouse_age_restrictions = isset($_POST['is_spouse_age_restrictions'])?$_POST['is_spouse_age_restrictions']:"";
		$spouse_age_restrictions_from = isset($_POST['spouse_age_restrictions_from'])?$_POST['spouse_age_restrictions_from']:"";
		$spouse_age_restrictions_to = isset($_POST['spouse_age_restrictions_to'])?$_POST['spouse_age_restrictions_to']:"";
		
		$is_children_age_restrictions = isset($_POST['is_children_age_restrictions'])?$_POST['is_children_age_restrictions']:"";
		$children_age_restrictions_from = isset($_POST['children_age_restrictions_from'])?$_POST['children_age_restrictions_from']:"";
		$children_age_restrictions_to = isset($_POST['children_age_restrictions_to'])?$_POST['children_age_restrictions_to']:"";

		$maxAgeAutoTermed = !empty($_POST['maxAgeAutoTermed']) ? $_POST['maxAgeAutoTermed'] : '';
		
		$autoTermArray = array();
		$autoTermedMemberType = !empty($_POST['autoTermedMemberType']) ? $_POST['autoTermedMemberType'] : array();
		$autoTermMemberSettingWithin = !empty($_POST['autoTermMemberSettingWithin']) ? $_POST['autoTermMemberSettingWithin'] : array();
		$autoTermMemberSettingWithinType = !empty($_POST['autoTermMemberSettingWithinType']) ? $_POST['autoTermMemberSettingWithinType'] : array();
		$autoTermMemberSettingRange = !empty($_POST['autoTermMemberSettingRange']) ? $_POST['autoTermMemberSettingRange'] : array();
		$autoTermMemberSettingWithinTrigger = !empty($_POST['autoTermMemberSettingWithinTrigger']) ? $_POST['autoTermMemberSettingWithinTrigger'] : array();


		$allowedBeyoundAge = !empty($_POST['allowedBeyoundAge']) ? $_POST['allowedBeyoundAge'] : '';
		$allowedBeyoundAgeType = array();
		if($allowedBeyoundAge=='Y'){
			$allowedBeyoundAgeType = !empty($_POST['allowedBeyoundAgeType']) ? $_POST['allowedBeyoundAgeType'] : array();
		}

		$memberQuestion = !empty($_POST['memberQuestion']) ? $_POST['memberQuestion'] : array();
		$spouseQuestion = !empty($_POST['spouseQuestion']) ? $_POST['spouseQuestion'] : array();
		$childQuestion = !empty($_POST['childQuestion']) ? $_POST['childQuestion'] : array();
		
		$agreementCustomQuestion = !empty($_POST['agreementCustomQuestion']) ? $_POST['agreementCustomQuestion'] : array();
		$memberCustomQuestion = !empty($_POST['memberCustomQuestion']) ? $_POST['memberCustomQuestion'] : array();
		$spouseCustomQuestion = !empty($_POST['spouseCustomQuestion']) ? $_POST['spouseCustomQuestion'] : array();
		$childCustomQuestion = !empty($_POST['childCustomQuestion']) ? $_POST['childCustomQuestion'] : array();
		
		
		
		
		$is_beneficiary_required = !empty($_POST['is_beneficiary_required']) ? $_POST['is_beneficiary_required'] : '';
		$principalBeneficiary = !empty($_POST['principalBeneficiary']) ? $_POST['principalBeneficiary'] : array();
		$contingentBeneficiary = !empty($_POST['contingentBeneficiary']) ? $_POST['contingentBeneficiary'] : array();
		
		
		$enrollment_verification_array = empty($_POST['enrollment_verification']) ? array() : $_POST['enrollment_verification'];
		$enrollment_verification ='';
		if(!empty($enrollment_verification_array)){
			if($product_type == "Group Enrollment"){
				if(!empty($enrollment_verification_array)){
					foreach($enrollment_verification_array as $vfKey => $verification){
						if($verification == 'voice_verification'){
							unset($enrollment_verification_array[$vfKey]);
						}
					}
				}
			}
			$enrollment_verification = implode(",", $enrollment_verification_array);
		}
		
		$termsConditionData = isset($_POST['termsConditionData'])?$_POST['termsConditionData']:'';
		$joinder_agreement_require = !empty($_POST['joinder_agreement_require']) ? $_POST['joinder_agreement_require'] : 'N';
		$joinder_agreement = !empty($_POST['joinder_agreement']) ? $_POST['joinder_agreement'] : '';				

		$is_license_require = !empty($_POST['is_license_require']) ? $_POST['is_license_require'] : '';

		if($product_type == "Group Enrollment"){
			$is_license_require='N';
		}

		$license_type = isset($_POST['license_type'])?$_POST['license_type']:array();

		$license_rule = isset($_POST['license_rule'])?$_POST['license_rule']:"";
		
		$specificStateArray = array();
		if($license_rule == "Licensed in Specific States Only"){
			$specificStateArray = !empty($_POST['specificState'])?$_POST['specificState']:array();
			$specificState = '';
			if(!empty($specificStateArray)){
				$specificState = implode(",", $specificStateArray);
			}
		}
		$preSaleStateArray = array();
		$justInTimeSaleStateArray = array();
		if($license_rule == "Licensed and Appointed"){
			$preSaleStateArray = !empty($_POST['preSaleState'])?$_POST['preSaleState']:array();
			$preSaleState = '';
			if(!empty($preSaleStateArray)){
				$preSaleState = implode(",", $preSaleStateArray);
			}
			$justInTimeSaleStateArray = !empty($_POST['justInTimeSaleState'])?$_POST['justInTimeSaleState']:array();
			$justInTimeSaleState = '';
			if(!empty($justInTimeSaleStateArray)){
				$justInTimeSaleState = implode(",", $justInTimeSaleStateArray);
			}

			
		}
		
	//********** step3 varible intialization code end   **********************
	
	//********** step4 varible intialization code start **********************
		$pricing_model = $member_payment = $member_payment_type = '';
		if($step >= 4 || $prdStep >= 4){
			$member_payment = isset($_POST['member_payment'])?$_POST['member_payment']:"";
			$member_payment_type = isset($_POST['member_payment_type'])?$_POST['member_payment_type']:"";
			$pricing_model = isset($_POST['pricing_model'])?$_POST['pricing_model']:"";

			if(!$allowPricingUpdate){
				$pricing_model = !empty($_POST['allow_pricing_model']) ? $_POST['allow_pricing_model'] : array();	
			}
		}

		$pricing_fixed_price = array();
		$price_control_array = array();
		$price_control_enrollee = array();
		$price_control_enrollee_array = array();
		$pricingMatrixKey = array();
		$riderArr = array();
		$riderDetailArr = array();

		
		if($pricing_model=="FixedPrice"){
			$pricing_fixed_price = !empty($_POST['pricing_fixed_price']) ? $_POST['pricing_fixed_price'] : array();
			$pricing_effective_date = !empty($_POST['pricing_effective_date']) ? $_POST['pricing_effective_date'] : array();
			$pricing_termination_date = !empty($_POST['pricing_termination_date']) ? $_POST['pricing_termination_date'] : array();
			$newPricingOnRenewals = !empty($_POST['newPricingOnRenewals']) ? $_POST['newPricingOnRenewals'] : array();
		}else if($pricing_model == "VariablePrice"){
			$price_control_enrollee = array();
			$price_control_array = empty($_POST['price_control']) ? array() : $_POST['price_control'];
			$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();

			if(!$allowPricingUpdate){
				$price_control_array = empty($_POST['allow_price_control']) ? array() : $_POST['allow_price_control'];
			}

			if(!empty($pricingMatrixKey)){
				foreach ($pricingMatrixKey as $matrixGroup => $matrixGroupRow) {
					foreach ($matrixGroupRow as $key => $value) {
						if(!empty($value['enrolleeMatrix'])){
							unset($pricingMatrixKey[$matrixGroup][$key]);
						}
					}
				}
			}
		}else if($pricing_model == "VariableEnrollee"){
			$price_control_array = array();
			$enrolleeType = empty($_POST['enrolleeType']) ? array() : $_POST['enrolleeType'];
			$price_control_enrollee = empty($_POST['price_control_enrollee']) ? array() : $_POST['price_control_enrollee'];
			$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();

			if(!$allowPricingUpdate){
				$enrolleeType = empty($_POST['allow_enrolleeType']) ? array() : $_POST['allow_enrolleeType'];
				$price_control_enrollee = empty($_POST['allow_price_control_enrollee']) ? array() : $_POST['allow_price_control_enrollee'];
			}
			if(!empty($price_control_enrollee)){
				foreach ($price_control_enrollee as $key => $valueArr) {
					if(!empty($valueArr)){
						foreach ($valueArr as $keyInnr => $value) {
							$price_control_enrollee_array[$key."_".$value]=$value;
						}
					}
				}
			}
			
			if(!empty($pricingMatrixKey)){
				foreach ($pricingMatrixKey as $matrixGroup => $matrixGroupRow) {
					foreach ($matrixGroupRow as $key => $value) {
						if(!empty($value['matrixPlanType'])){
							unset($pricingMatrixKey[$matrixGroup][$key]);
						}
					}
				}
			}
			$childRateCalculateType = isset($_POST['childRateCalculateType'])?$_POST['childRateCalculateType']:"";
			$singleRateChildrenAllowed = isset($_POST['singleRateChildrenAllowed'])?$_POST['singleRateChildrenAllowed']:"";
			
			$enrollee_primary_age = isset($_POST['enrollee_primary_age'])?$_POST['enrollee_primary_age']:"";
			
			$rider_for_enrollee = isset($_POST['rider_for_enrollee'])?$_POST['rider_for_enrollee']:"";
			
			$riderProduct = isset($_POST['riderProduct'])?$_POST['riderProduct']:array();
			$riderRate = isset($_POST['riderRate'])?$_POST['riderRate']:array();			
		}
		$productFees = !empty($_POST['productFees']) ? json_decode($_POST['productFees'],true) : array();
		$feePrdMatrixIdArr = array();
		
	//********** step4 varible intialization code end   **********************


	//********* step1 validation code start ********************
		//if($step >= 1 || $prdStep >=1){
			$step1Validation->string(array('required' => true, 'field' => 'product_name', 'value' => $product_name), array('required' => 'Please Add Product Name'));
			if(!$step1Validation->getError('product_name')){
				if(!preg_match('/^[a-zA-Z0-9 ._\-\/$+]*$/i', $product_name)){
					$step1Validation->setError('product_name','Special characters not allowed');
				}
			}
			
			$step1Validation->string(array('required' => true, 'field' => 'product_code', 'value' => $product_code), array('required' => 'Please Add Product Id'));
			if(!$step1Validation->getError('product_code')){
				if(!preg_match('/^[a-zA-Z0-9 ._\-\/$]*$/i', $product_code)){
					$step1Validation->setError('product_code','Special characters not allowed');
				}
			}
			
			if ($product_code != "" && !$step1Validation->getError('product_code')) {
				$prdINCR = '';
				$prdSCHParams = array(":product_code"=>$product_code);
				if(!empty($product_id)){
					$prdINCR = ' AND id!=:id';
					$prdSCHParams[":id"] = $product_id;
				}
				$sqlProduct="SELECT id FROM prd_main where product_code = :product_code and is_deleted='N' $prdINCR";
				$resProduct=$pdo->selectOne($sqlProduct,$prdSCHParams);

				if(!empty($resProduct)){
					$step1Validation->setError("product_code","Product ID Already Exist".$resProduct['id']);
				}
			}
			
			if($company_id=="new_company"){
				$step1Validation->string(array('required' => true, 'field' => 'company_name', 'value' => $company_name), array('required' => 'Please Add New Company Name'));
				
				if (!empty($company_name) && !$step1Validation->getError('company_name')) {
					$sqlCompany="SELECT id FROM prd_company where company_name = :company_name AND is_deleted='N'";
					$resCompany=$pdo->select($sqlCompany,array(":company_name"=>$company_name));

					if(!empty($resCompany)){
						$step1Validation->setError("company_name","Company Name Already Exist");
					}
				}

			}else{
				$step1Validation->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Please Select Company'));
			}

			if($category_id=="new_category"){
				$step1Validation->string(array('required' => true, 'field' => 'category_name', 'value' => $category_name), array('required' => 'Please Add New Category Name'));
				
				if ($category_name != "" && !$step1Validation->getError('category_name')) {
					$sqlCategory="SELECT id FROM prd_category where title = :title AND is_deleted='N'";
					$resCategory=$pdo->select($sqlCategory,array(":title"=>$category_name));

					if(!empty($resCategory)){
						$step1Validation->setError("category_name","Category Already Exist");
					}
				}

			}else{
				$step1Validation->string(array('required' => true, 'field' => 'category_id', 'value' => $category_id), array('required' => 'Please Select Category'));
			}

			
			$step1Validation->string(array('required' => true, 'field' => 'product_carrier', 'value' => $product_carrier), array('required' => 'Please Select Primary Carrier'));
			

			$step1Validation->string(array('required' => true, 'field' => 'product_type', 'value' => $product_type), array('required' => 'Please Select Application Method'));
			
			$step1Validation->string(array('required' => true, 'field' => 'agent_portal', 'value' => $agent_portal), array('required' => 'Please Enter Description'));

			$step1Validation->string(array('required' => true, 'field' => 'main_product_type', 'value' => $main_product_type), array('required' => 'Please Select Product Type'));
			
			$step1Validation->string(array('required' => true, 'field' => 'is_life_insurance_product', 'value' => $is_life_insurance_product), array('required' => 'Please Select is life insurance product'));

			$step1Validation->string(array('required' => true, 'field' => 'is_short_term_disablity_product', 'value' => $is_short_term_disablity_product), array('required' => 'Please Select is short term disability product'));

			if($is_life_insurance_product == 'Y'){
				$step1Validation->string(array('required' => true, 'field' => 'life_term_type', 'value' => $life_term_type), array('required' => 'Please Select Life Term'));

				if(empty($guarantee_issue_amount_type)){
					//$step1Validation->setError("guarantee_issue_amount_type","Please Select guarantee issue amount");
				}else{
					/*if(in_array("Primary", $guarantee_issue_amount_type)){
						$step1Validation->string(array('required' => true, 'field' => 'primary_issue_amount', 'value' => $primary_issue_amount), array('required' => 'Please Select Primary Issue Amount'));
					}
					if(in_array("Spouse", $guarantee_issue_amount_type)){
						$step1Validation->string(array('required' => true, 'field' => 'spouse_issue_amount', 'value' => $spouse_issue_amount), array('required' => 'Please Select Spouse Issue Amount'));
						$step1Validation->string(array('required' => true, 'field' => 'is_spouse_issue_amount_larger', 'value' => $is_spouse_issue_amount_larger), array('required' => 'Please Select Spouse Issue Amount Larger than Primary'));
					}
					if(in_array("Child", $guarantee_issue_amount_type)){
						$step1Validation->string(array('required' => true, 'field' => 'child_issue_amount', 'value' => $child_issue_amount), array('required' => 'Please Select Child Issue Amount'));
					}*/
				}			
			}

			if($is_short_term_disablity_product == 'Y'){
				if(empty($monthly_benefit_allowed) || $monthly_benefit_allowed == "0" || $monthly_benefit_allowed == "0.00"){
					$step1Validation->setError("monthly_benefit_allowed","Please enter Monthly benefit allowed greater than 0.");
				}
				if(empty($percentage_of_salary) || $percentage_of_salary == "0" || $percentage_of_salary == "0.00"){
					$step1Validation->setError("percentage_of_salary","Please enter Percentage of salary greater than 0.");
				}else if(!empty($percentage_of_salary) && $percentage_of_salary > 100){
					$step1Validation->setError("percentage_of_salary","Please enter Percentage of salary not greater than 100.");
				}
			}

			if($product_type == "Group Enrollment"){
				$step1Validation->string(array('required' => true, 'field' => 'is_gap_plus_product', 'value' => $is_gap_plus_product), array('required' => 'Please Select is Gap+ product'));

				$step1Validation->string(array('required' => true, 'field' => 'deduction', 'value' => $deduction), array('required' => 'Please Select Deductions'));

				if($is_gap_plus_product == 'Y'){
					if(!empty($prdPlanTypeArray)) {
						foreach ($prdPlanTypeArray as $key => $tier) {
							$hrm_payment = $annual_hrm_payment[$tier['id']];
							if(empty($hrm_payment) || $hrm_payment == "0" || $hrm_payment == "0.00"){
								$step1Validation->setError("annual_hrm_payment_".$tier['id'],"Please enter amount allowed greater than 0.");
							}					
						}
					}
					$step1Validation->string(array('required' => true, 'field' => 'is_require_out_of_pocket_maximum', 'value' => $is_require_out_of_pocket_maximum), array('required' => 'Please select option'));
					$step1Validation->string(array('required' => true, 'field' => 'is_benefit_amount_limit', 'value' => $is_benefit_amount_limit), array('required' => 'Please select option'));
					if($is_benefit_amount_limit == "Y") {
						$step1Validation->string(array('required' => true, 'field' => 'minimum_benefit_amount', 'value' => $minimum_benefit_amount), array('required' => 'Please select amount'));
						$step1Validation->string(array('required' => true, 'field' => 'maximum_benefit_amount', 'value' => $maximum_benefit_amount), array('required' => 'Please select amount'));
						if($maximum_benefit_amount < $minimum_benefit_amount) {
							$step1Validation->setError("maximum_benefit_amount","Amount must be greater than Minimum Available Amount");
						}
					}
					$step1Validation->string(array('required' => true, 'field' => 'is_set_default_out_of_pocket_maximum', 'value' => $is_set_default_out_of_pocket_maximum), array('required' => 'Please select option'));
					
					$step1Validation->string(array('required' => true, 'field' => 'default_out_of_pocket_maximum', 'value' => $default_out_of_pocket_maximum), array('required' => 'Please select option'));
					$step1Validation->string(array('required' => true, 'field' => 'gap_home_savings_recommend_text', 'value' => $gap_home_savings_recommend_text), array('required' => 'Please select option'));
					if($gap_home_savings_recommend_text == "custom_recommendation") {
						$step1Validation->string(array('required' => true, 'field' => 'gap_custom_recommendation_text', 'value' => $gap_custom_recommendation_text), array('required' => 'Please enter Recommendation Text'));
					}
				}
			}

			if (preg_match('/^(<div><br><\/div>$)/',$agent_portal)){
			 $step1Validation->setError("agent_portal","Please Enter Description");
			}
			if(!$step1Validation->getError('enrollmentPage')) {
				if($functionsList->hasExternalJsCss($enrollmentPage)) {
					$step1Validation->setError('enrollmentPage',"Please remove external JavaScript/CSS from HTML");
				}
			}
			if(!$step1Validation->getError('agent_portal')) {
				if($functionsList->hasExternalJsCss($agent_portal)) {
					$step1Validation->setError('agent_portal',"Please remove external JavaScript/CSS from HTML");
				}
			}
			if(!$step1Validation->getError('limitations_exclusions')) {
				if($functionsList->hasExternalJsCss($limitations_exclusions)) {
					$step1Validation->setError('limitations_exclusions',"Please remove external JavaScript/CSS from HTML");
				}
			}
			if(!empty($department_name) && count($department_name) > 0){
				foreach ($department_name as $key => $value) {
					if(!$step1Validation->getError('department_desc_'.$key)) {
						if($functionsList->hasExternalJsCss($department_desc[$key])) {
							$step1Validation->setError('department_desc_'.$key,"Please remove external JavaScript/CSS from HTML");
						}
					}
				}
			}
		//}
	//********* step1 validation code end   ********************
	
	//********* step2 validation code start ********************
		//if($step >= 2 || $prdStep >= 2){
			
			$step2Validation->string(array('required' => true, 'field' => 'direct_product', 'value' => $direct_product), array('required' => 'Select Effective Date Option'));
			
			if($direct_product=="Select Day Of Month"){
				$step2Validation->string(array('required' => true, 'field' => 'effective_day', 'value' => $effective_day), array('required' => 'Select Day of Month'));
				$step2Validation->string(array('required' => true, 'field' => 'sold_day', 'value' => $sold_day), array('required' => 'Select Day Of Month To Sold'));
			}
			if($direct_product=="First Of Month"){
				$step2Validation->string(array('required' => true, 'field' => 'sold_day', 'value' => $sold_day), array('required' => 'Select Day Of Month To Sold'));
			}
			
			
			
			$step2Validation->string(array('required' => true, 'field' => 'is_membership_require', 'value' => $is_membership_require), array('required' => 'Select Membership Requirement'));
			
			if($is_membership_require=='Y' && count($membership_ids_check_array) <=0){
				$step2Validation->setError("membership_ids","Please Select Membership");
			}
			
			
			if(!empty($available_no_sale_state)){
				foreach ($available_no_sale_state as $keyr => $valuer) {
					foreach ($valuer as $key => $value) {

					$step2Validation->string(array('required' => true, 'field' => 'available_no_sale_state_effective_date_'.$keyr.'_'.$key, 'value' => $value['effective_date']), array('required' => 'Enter Effective Date'));
					
					if(!empty($value['effective_date']) && !empty($value['termination_date'])){
						$noSaleEffectiveDate=date('Y-m-d',strtotime($value['effective_date']));
						$noSaleTerminationDate=date('Y-m-d',strtotime($value['termination_date']));
						if(strtotime($noSaleEffectiveDate) >= strtotime($noSaleTerminationDate)){
							$step2Validation->setError("available_no_sale_state_effective_date_".$keyr.'_'.$key,"Enter Valid Date");
						}
						$checkEffectiveDate=validateDate($value['effective_date'],"m/d/Y");
						$checkTermDate=validateDate($value['termination_date'],"m/d/Y");

						if(!$checkEffectiveDate){
							$step2Validation->setError("available_no_sale_state_effective_date_".$keyr.'_'.$key,"Enter Valid Date");
						}

						if(!$checkTermDate){
							$step2Validation->setError("available_no_sale_state_termination_date_".$keyr.'_'.$key,"Enter Valid Date");
						}

					}
				  }
				}
			}

			$step2Validation->string(array('required' => true, 'field' => 'is_specific_zipcode', 'value' => $is_specific_zipcode), array('required' => 'Select specific zip code'));

			if($is_specific_zipcode =='Y'){
				if(!empty($zipcode_allow_only_state)){
					foreach ($zipcode_allow_only_state as $key => $row) {
						if(empty($available_state_zipcode[$row])){
							$step2Validation->setError("available_state_zipcode_".str_replace(" ","_", $row),"Please Add Specific ZipCode");
						}
						$zipcodeList = explode(",", $available_state_zipcode[$row]);
						if(!empty($zipcodeList)){
							foreach ($zipcodeList as $key => $value) {
								$available_zipcode_list[$row."_".$value] = $value;
								
							}
						}
						
					}
				}else{
					$step2Validation->setError("zipcode_allow_only_state","Please Select State");
				}
			}
  			
			$step2Validation->string(array('required' => true, 'field' => 'no_sale_state_coverage_continue', 'value' => $no_sale_state_coverage_continue), array('required' => 'select continue plan on move to no sale state'));

			if(count($coverage_options_array) <= 0){
				$step2Validation->setError("coverage_options","Please Select Plan Options");
			}
			
			if(in_array(4, $coverage_options_array)){
				$step2Validation->string(array('required' => true, 'field' => 'family_plan_rule', 'value' => $family_plan_rule), array('required' => 'Select Any Option'));
			}


			if(count($excludeProduct_array) > 0 && count($autoAssignProduct_array) > 0){
				if (array_intersect($excludeProduct_array, $autoAssignProduct_array)) {
					$step2Validation->setError("excludeProduct","Same Product can not AutoAssign and Exluded");
				}
			}
			if(count($excludeProduct_array) > 0 && count($requiredProduct_array) > 0){
				if (array_intersect($excludeProduct_array, $requiredProduct_array)) {
					$step2Validation->setError("excludeProduct","Same Product can not Required and Exluded");
				}
			}
			if(count($excludeProduct_array) > 0 && count($packagedProduct_array) > 0){
				if (array_intersect($excludeProduct_array, $packagedProduct_array)) {
					$step2Validation->setError("excludeProduct","Same Product can not Packaged and Exluded");
				}
			}

			if(count($autoAssignProduct_array) > 0 && count($requiredProduct_array) > 0){
				if (array_intersect($autoAssignProduct_array, $requiredProduct_array)) {
					$step2Validation->setError("autoAssignProduct","Same Product can not Required and AutoAssign");
				}
			}
			if(count($autoAssignProduct_array) > 0 && count($packagedProduct_array) > 0){
				if (array_intersect($autoAssignProduct_array, $packagedProduct_array)) {
					$step2Validation->setError("autoAssignProduct","Same Product can not Packaged and AutoAssign");
				}
			}
			if(count($requiredProduct_array) > 0 && count($packagedProduct_array) > 0){
				if (array_intersect($requiredProduct_array, $packagedProduct_array)) {
					$step2Validation->setError("requiredProduct","Same Product can not Packaged and Required");
				}
			}

			$step2Validation->string(array('required' => true, 'field' => 'termination_rule', 'value' => $termination_rule), array('required' => 'Select Termination Rule'));
			
			$step2Validation->string(array('required' => true, 'field' => 'term_back_to_effective', 'value' => $term_back_to_effective), array('required' => 'Select Can Product Termed back to effective'));
			$step2Validation->string(array('required' => true, 'field' => 'term_automatically', 'value' => $term_automatically), array('required' => 'Select Product Termed Automatically after period of time'));
			if($term_automatically=='Y'){
				$step2Validation->string(array('required' => true, 'field' => 'term_automatically_within', 'value' => $term_automatically_within), array('required' => 'Select '.$term_automatically_within_type.' Of Last Payment'));
				if(empty($term_automatically_within)){
					$step2Validation->setError("term_automatically_within","Please Select Valid ".$term_automatically_within_type);
				}
			}

			$step2Validation->string(array('required' => true, 'field' => 'reinstate_option', 'value' => $reinstate_option), array('required' => 'Select Reinstate Option'));
			if($reinstate_option=='Available Within Specific Time Frame'){
				$step2Validation->string(array('required' => true, 'field' => 'reinstate_within', 'value' => $reinstate_within), array('required' => 'Select '.$reinstate_within_type.' Of Last Payment'));
				if(empty($reinstate_within)){
					$step2Validation->setError("reinstate_within","Please Select Valid ".$reinstate_within_type);
				}
			}


			$step2Validation->string(array('required' => true, 'field' => 'reenroll_options', 'value' => $reenroll_options), array('required' => 'Select Reenroll Option'));
			if($reenroll_options=='Available After Specific Time Frame'){
				$step2Validation->string(array('required' => true, 'field' => 'reenroll_within', 'value' => $reenroll_within), array('required' => 'Select '.$reenroll_within_type.' Of Last Payment'));
				if(empty($reenroll_within)){
					$step2Validation->setError("reenroll_within","Please Select Valid ".$reenroll_within_type);
				}
			}
		//}
	//********* step2 validation code end   ********************
	
	//********* step3 validation code start ********************
		//if($step >= 3 || $prdStep >= 3){

			$step3Validation->string(array('required' => true, 'field' => 'is_primary_age_restrictions', 'value' => $is_primary_age_restrictions), array('required' => 'Select Primary Age Restriction'));

			if ($is_primary_age_restrictions == "Y") {	
				$step3Validation->string(array('required' => true, 'field' => 'primary_age_restrictions', 'value' => $primary_age_restrictions_from), array('required' => 'Select Age Restriction'));
				$step3Validation->string(array('required' => true, 'field' => 'primary_age_restrictions', 'value' => $primary_age_restrictions_to), array('required' => 'Select Age Restriction'));

				if($primary_age_restrictions_to <= $primary_age_restrictions_from){
					$step3Validation->setError("primary_age_restrictions","Please Select Valid Age Restriction");
				}
			}

			$step3Validation->string(array('required' => true, 'field' => 'is_spouse_age_restrictions', 'value' => $is_spouse_age_restrictions), array('required' => 'Select Spouse Age Restriction'));

			if ($is_spouse_age_restrictions == "Y") {	
				$step3Validation->string(array('required' => true, 'field' => 'spouse_age_restrictions', 'value' => $spouse_age_restrictions_from), array('required' => 'Select Age Restriction'));
				$step3Validation->string(array('required' => true, 'field' => 'spouse_age_restrictions', 'value' => $spouse_age_restrictions_to), array('required' => 'Select Age Restriction'));

				if($spouse_age_restrictions_to <= $spouse_age_restrictions_from){
					$step3Validation->setError("spouse_age_restrictions","Please Select Valid Age Restriction");
				}
			}
			
			$step3Validation->string(array('required' => true, 'field' => 'is_children_age_restrictions', 'value' => $is_children_age_restrictions), array('required' => 'Select Child Age Restriction'));

			if ($is_children_age_restrictions == "Y") {	
				$step3Validation->string(array('required' => true, 'field' => 'children_age_restrictions', 'value' => $children_age_restrictions_from), array('required' => 'Select Age Restriction'));
				$step3Validation->string(array('required' => true, 'field' => 'children_age_restrictions', 'value' => $children_age_restrictions_to), array('required' => 'Select Age Restriction'));

				if($children_age_restrictions_to <= $children_age_restrictions_from){
					$step3Validation->setError("children_age_restrictions","Please Select Valid Age Restriction");
				}
			}
			
			
			if($is_primary_age_restrictions == "Y" || 	$is_spouse_age_restrictions == "Y" || $is_children_age_restrictions == "Y"){
				$step3Validation->string(array('required' => true, 'field' => 'maxAgeAutoTermed', 'value' => $maxAgeAutoTermed), array('required' => 'Select Any Option'));
				$step3Validation->string(array('required' => true, 'field' => 'allowedBeyoundAge', 'value' => $allowedBeyoundAge), array('required' => 'Select Any Option'));

				if($maxAgeAutoTermed =='Y'){

					if(empty($autoTermedMemberType)){
						$step3Validation->setError("autoTermedMemberType","Select Member");
					}else{
						
						if(!empty($autoTermMemberSettingWithin)){
							foreach ($autoTermMemberSettingWithin as $mainKey => $mainValue) {
								foreach ($mainValue as $innerKey => $innerValue) {
									
									$step3Validation->string(array('required' => true, 'field' => 'autoTermMemberSettingWithin_'.$innerKey.'_'.$mainKey, 'value' => $autoTermMemberSettingWithin[$mainKey][$innerKey]), array('required' => 'Select Any Option'));
									$step3Validation->string(array('required' => true, 'field' => 'autoTermMemberSettingWithinType_'.$innerKey.'_'.$mainKey, 'value' => $autoTermMemberSettingWithinType[$mainKey][$innerKey]), array('required' => 'Select Any Option'));
									$step3Validation->string(array('required' => true, 'field' => 'autoTermMemberSettingRange_'.$innerKey.'_'.$mainKey, 'value' => $autoTermMemberSettingRange[$mainKey][$innerKey]), array('required' => 'Select Any Option'));
									
									$step3Validation->string(array('required' => true, 'field' => 'autoTermMemberSettingWithinTrigger_'.$innerKey.'_'.$mainKey, 'value' => $autoTermMemberSettingWithinTrigger[$mainKey][$innerKey]), array('required' => 'Select Trigger'));

									if(isset($autoTermedMemberType[$innerKey])){
										$autoTermArray[$innerKey.'_'.$mainKey]['id']=$mainKey;
										$autoTermArray[$innerKey.'_'.$mainKey]['member_type']=$innerKey;
										$autoTermArray[$innerKey.'_'.$mainKey]['terminate_within']=$autoTermMemberSettingWithin[$mainKey][$innerKey];
										$autoTermArray[$innerKey.'_'.$mainKey]['terminate_within_type']=$autoTermMemberSettingWithinType[$mainKey][$innerKey];
										$autoTermArray[$innerKey.'_'.$mainKey]['terminate_range']=$autoTermMemberSettingRange[$mainKey][$innerKey];
										$autoTermArray[$innerKey.'_'.$mainKey]['terminate_trigger']=$autoTermMemberSettingWithinTrigger[$mainKey][$innerKey];
									}
									
								}
							}
						}
					}
				}
				if($allowedBeyoundAge =='Y'){
					if(empty($allowedBeyoundAgeType)){
						$step3Validation->setError("allowedBeyoundAgeType","Select Member");
					}
				}
			}else{
				$autoTermArray = array();
				$allowedBeyoundAgeType = array();
			}


			if(!empty($memberQuestion)){
				foreach ($memberQuestion as $mainKey => $mainValue) {
					if((!empty($mainValue['required']) && empty($mainValue['asked']))){
						$step3Validation->setError("memberQuestion","Asked is required");	
					}
				}
			}else{
				$step3Validation->setError('memberQuestion','Select Member Details Question');
			}
			if(!empty($spouseQuestion)){
				foreach ($spouseQuestion as $mainKey => $mainValue) {
					if((!empty($mainValue['required']) && empty($mainValue['asked']))){
						$step3Validation->setError("spouseQuestion","Asked is required");	
					}
				}
			}else{
				$step3Validation->setError('spouseQuestion','Select Spouse Details Question');
			}
			if(!empty($childQuestion)){
				foreach ($childQuestion as $mainKey => $mainValue) {
					if((!empty($mainValue['required']) && empty($mainValue['asked']))){
						$step3Validation->setError("childQuestion","Asked is required");	
					}
				}
			}else{
				$step3Validation->setError('childQuestion','Select Child Details Question');
			}

			$is_member_custom_questions_empty = $is_spouse_custom_question_empty = $is_child_custom_question_empty  = false;
			$check_custom_question_not_empty_array = array();
			if(!empty($memberCustomQuestion)){
				foreach ($memberCustomQuestion as $mainKey => $mainValue) {
					if((!empty($mainValue['required']) && empty($mainValue['asked']))){
						$step3Validation->setError("customQuestion","Asked is required");	
					}
					if(empty($check_custom_question_not_empty_array[$mainKey])){
						$check_custom_question_not_empty_array[$mainKey] = true;
					}
				}
			}else{
				$is_member_custom_questions_empty = true;
			}
			if(!empty($spouseCustomQuestion)){
				foreach ($spouseCustomQuestion as $mainKey => $mainValue) {
					if((!empty($mainValue['required']) && empty($mainValue['asked']))){
						$step3Validation->setError("customQuestion","Asked is required");	
					}
					if(empty($check_custom_question_not_empty_array[$mainKey])){
						$check_custom_question_not_empty_array[$mainKey] = true;
					}
				}
			}else{
				$is_spouse_custom_question_empty = true;
			}
			if(!empty($childCustomQuestion)){
				foreach ($childCustomQuestion as $mainKey => $mainValue) {
					if((!empty($mainValue['required']) && empty($mainValue['asked']))){
						$step3Validation->setError("customQuestion","Asked is required");	
					}
					if(empty($check_custom_question_not_empty_array[$mainKey])){
						$check_custom_question_not_empty_array[$mainKey] = true;
					}
				}
			}else{
				$is_child_custom_question_empty = true;
			}
			
			if(!empty($agreementCustomQuestion) ){
				if($is_member_custom_questions_empty && $is_spouse_custom_question_empty && $is_child_custom_question_empty){
					$step3Validation->setError("customQuestion","Please select Required or Asked from any of Member, Spouse or Child/Dependent.");
					
				}else{
					foreach ($agreementCustomQuestion as $mainKey => $mainValue) {
						if(!empty($mainValue['agreement']) && !isset($check_custom_question_not_empty_array[$mainKey])){
							$step3Validation->setError("customQuestion","Please select Required or Asked from any of Member, Spouse or Child/Dependent.");
						}
					}
				}
			}
			
			$step3Validation->string(array('required' => true, 'field' => 'is_beneficiary_required', 'value' => $is_beneficiary_required), array('required' => 'Select beneficiary option'));

			if($is_beneficiary_required=='Y'){
				if(!empty($principalBeneficiary)){
					foreach ($principalBeneficiary as $mainKey => $mainValue) {
						if((!empty($mainValue['required']) && empty($mainValue['asked']))){
							$step3Validation->setError("principalBeneficiary","Asked is required");	
						}
					}
				}else{
					$step3Validation->setError('principalBeneficiary','Select principal beneficiary');
				}
				if(!empty($contingentBeneficiary)){
					foreach ($contingentBeneficiary as $mainKey => $mainValue) {
						if((!empty($mainValue['required']) && empty($mainValue['asked']))){
							$step3Validation->setError("contingentBeneficiary","Asked is required");	
						}
					}
				}else{
					$step3Validation->setError('contingentBeneficiary','Select contingent beneficiary');
				}
			}
			
			$step3Validation->string(array('required' => true, 'field' => 'enrollment_verification', 'value' => $enrollment_verification), array('required' => 'Select Application Verification Option'));
			
			$step3Validation->string(array('required' => true, 'field' => 'termsCondition', 'value' => $termsConditionData), array('required' => 'Terms & Condition is required'));

			if (preg_match('/^(<div><br><\/div>$)/',$termsConditionData))
			{
			 $step3Validation->setError("termsCondition","Terms & Condition is required");
			}
			if(!$step3Validation->getError('termsConditionData')) {
				if($functionsList->hasExternalJsCss($termsConditionData)) {
					$step3Validation->setError('termsConditionData',"Please remove external JavaScript/CSS from HTML");
				}
			}

			if($joinder_agreement_require == "Y"){
				$step3Validation->string(array('required' => true, 'field' => 'joinder_agreement', 'value' => $joinder_agreement), array('required' => 'Joinder Agreement is required'));
				if (preg_match('/^(<div><br><\/div>$)/',$joinder_agreement))
				{
				 $step3Validation->setError("joinder_agreement","Joinder Agreement is required");
				}
				if(!$step3Validation->getError('joinder_agreement')) {
					if($functionsList->hasExternalJsCss($joinder_agreement)) {
						$step3Validation->setError('joinder_agreement',"Please remove external JavaScript/CSS from HTML");
					}
				}
			}
				
			$step3Validation->string(array('required' => true, 'field' => 'is_license_require', 'value' => $is_license_require), array('required' => 'Select License Requirement'));

			if($is_license_require=='Y'){
				 
				if(empty($license_type)){
					$step3Validation->setError('license_type','License Type is required');
				}
				

				$step3Validation->string(array('required' => true, 'field' => 'license_rule', 'value' => $license_rule), array('required' => 'Select Any Option'));

				if($license_rule == "Licensed in Specific States Only"){
					if(empty($specificStateArray)){
						$step3Validation->setError("specificState","Select Licensed in Specific State");
					}
				}else if($license_rule == "Licensed and Appointed"){
					if(empty($preSaleStateArray) && empty($justInTimeSaleStateArray)){
						$step3Validation->setError("justInTimeSaleState","Select Licensed and Appointed State");
					}
				}
			}				
		//}
	//********* step3 validation code end   ********************
	
	//********* step4 validation code start ********************
		//if($step >= 4 || $prdStep >= 4){
			$step4Validation->string(array('required' => true, 'field' => 'member_payment', 'value' => $member_payment), array('required' => 'Select Payment Option'));
			if($member_payment=="Recurring"){
				$step4Validation->string(array('required' => true, 'field' => 'member_payment_type', 'value' => $member_payment_type), array('required' => 'Select Any Option'));	
			}

			$step4Validation->string(array('required' => true, 'field' => 'pricing_model', 'value' => $pricing_model), array('required' => 'Select Pricing Model'));

			$count=1;

			if($pricing_model=="FixedPrice"){
				if(!empty($pricing_fixed_price)){
					$maxCount=count($pricing_fixed_price);
					foreach ($pricing_fixed_price as $matrix_group => $matrix_group_array) {
						foreach ($matrix_group_array as $matrix => $matrix_array) {
							if(in_array($matrix, $coverage_options_array)){
								$step4Validation->string(array('required' => true, 'field' => 'pricing_fixed_price_'.$matrix_group.'_'.$matrix, 'value' => $pricing_fixed_price[$matrix_group][$matrix]['Retail']), array('required' => 'Please Add Price'));
								$step4Validation->string(array('required' => true, 'field' => 'pricing_fixed_price_'.$matrix_group.'_'.$matrix, 'value' => $pricing_fixed_price[$matrix_group][$matrix]['NonCommissionable']), array('required' => 'Please Add Price'));
								$step4Validation->string(array('required' => true, 'field' => 'pricing_fixed_price_'.$matrix_group.'_'.$matrix, 'value' => $pricing_fixed_price[$matrix_group][$matrix]['Commissionable']), array('required' => 'Please Add Price'));

								if(str_replace(",","",$pricing_fixed_price[$matrix_group][$matrix]['Retail']) <  str_replace(",","",$pricing_fixed_price[$matrix_group][$matrix]['NonCommissionable'])){
									$step4Validation->setError("pricing_fixed_price_".$matrix_group."_".$matrix,"Enter Valid Price");
								}
							}
						}
						$step4Validation->string(array('required' => true, 'field' => 'pricing_effective_date_'.$matrix_group, 'value' => $pricing_effective_date[$matrix_group]), array('required' => 'Add Effective Date'));
						

						if(!empty($pricing_effective_date[$matrix_group]) && !empty($pricing_termination_date[$matrix_group])){
							
							$effectiveDate=date('Y-m-d',strtotime($pricing_effective_date[$matrix_group]));
							$terminationDate=date('Y-m-d',strtotime($pricing_termination_date[$matrix_group]));
							$todayDate=date('Y-m-d');
							if(strtotime($effectiveDate) >= strtotime($terminationDate)){
								$step4Validation->setError("pricing_effective_date_".$matrix_group,"Enter Valid Date");
							}
							if(strtotime($terminationDate) <= strtotime($todayDate)){
								 
							}

							$checkEffectiveDate=validateDate($pricing_effective_date[$matrix_group],"m/d/Y");
							$checkTermDate=validateDate($pricing_termination_date[$matrix_group],"m/d/Y");
							if(!$checkEffectiveDate){
								$step4Validation->setError("pricing_effective_date_".$matrix_group,"Enter Valid Date");
							}
							if(!$checkTermDate){
								$step4Validation->setError("pricing_termination_date_".$matrix_group,"Enter Valid Date");
							}
						}
						
						if($count==$maxCount && $count > 1){

							if(empty($newPricingOnRenewals[$matrix_group])){
								$step4Validation->setError('newPricingOnRenewals_'.$matrix_group,"Select New Pricing Applied On Renewals");
							}
						}
						$count++;

						
					}
				}
			}else if($pricing_model == "VariablePrice"){
				if(empty($price_control_array)){
					$step4Validation->setError("price_control","Please Select Pricing Criteria");
				}
				if(empty($pricingMatrixKey)){
					$step4Validation->setError("price_control","Please Add Price");
				}
			}else if($pricing_model=="VariableEnrollee"){
				if(!empty($enrolleeType)){
					foreach ($enrolleeType as $key => $value) {
						if($value != 'All' && empty($price_control_enrollee[$value])){
							$step4Validation->setError("price_control_enrollee_".$value,"Pricing Criteria is required");
						}
					}
				}else{
					$step4Validation->setError("enrolleeType","Select Enrollee");
				}

				if(empty($pricingMatrixKey)){
					$step4Validation->setError("price_control_enrollee","Please Add Price");
				}

				if(!empty($enrolleeType) && in_array('Child',$enrolleeType)){
					$step4Validation->string(array('required' => true, 'field' => 'childRateCalculateType', 'value' => $childRateCalculateType), array('required' => 'Select child dependent rates calculations'));

					if($childRateCalculateType == "Single Rate based on Eldest Child"){
						$step4Validation->string(array('required' => true, 'field' => 'singleRateChildrenAllowed', 'value' => $singleRateChildrenAllowed), array('required' => 'Select # of children allowed'));
					}
				}

				$step4Validation->string(array('required' => true, 'field' => 'enrollee_primary_age', 'value' => $enrollee_primary_age), array('required' => 'Select primary user required to be eldest person in plan'));
				
				//$step4Validation->string(array('required' => true, 'field' => 'rider_for_enrollee', 'value' => $rider_for_enrollee), array('required' => 'Select rider for enrollees'));

				/*if($rider_for_enrollee=='Y'){

					if(empty($riderProduct)){
						$step4Validation->setError("rider_general","Please Select Rider Product");
					}else{
						foreach ($riderProduct as $key => $riderType) {
							foreach ($riderType as $innerKey => $value) {
								$tmp_rider_rate = "";
								if(empty($riderRate[$key][$innerKey])){
									$step4Validation->setError('riderRate_'.$key.'_'.$innerKey,"Select rider rate");
								}else{
									$tmp_rider_rate = $riderRate[$key][$innerKey];
								}
								$step4Validation->string(array('required' => true, 'field' => 'riderProduct_'.$key.'_'.$innerKey, 'value' => $value), array('required' => 'Select rider product'));
								
								$riderArr[$key."_".$innerKey]=$value;
								$riderDetailArr[$key]['rider_type'] = $innerKey;
								$riderDetailArr[$key]['rider_product_id'] = $value;
								$riderDetailArr[$key]['rider_rate'] = $tmp_rider_rate;


							}
							
						}
						
					}
				}*/
			}

			
			//allow one admin fee only when application type is group of admin fee validation
			if($product_type == "Group Enrollment" && count($productFees) > 1){
                $step4Validation->setError("allow_amdin_fee","Only one admin fee is allowed");
			}
		//}
	//********* step4 validation code end   ********************

	$allValidation = array_merge($step1Validation->getErrors(),$step2Validation->getErrors(),$step3Validation->getErrors(),$step4Validation->getErrors());

	if(!empty($product_id)){
		$errParams=array(
			'errorJson'=>json_encode($allValidation),
		);
		$errWhere=array(
			'clause'=>"product_id=:product_id",
			'params'=>array(
				":product_id"=>$product_id,

			)
		);
		$pdo->update("prd_product_builder_validation",$errParams,$errWhere);
	}
	
	$stepValidation = array();
	if($step >= 1 || $prdStep >=1){
		$stepValidation = array_merge($step1Validation->getErrors(),$stepValidation);
		if (count($step1Validation->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "information_tab";
		}
	}
	if($step >= 2 || $prdStep >= 2){
		$stepValidation = array_merge($step2Validation->getErrors(),$stepValidation);
		if (count($step2Validation->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "rules_tab";
		}
	}
	if($step >= 3 || $prdStep >= 3){
		$stepValidation = array_merge($step3Validation->getErrors(),$stepValidation);
		if (count($step3Validation->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "enrollment_tab";
		}
	}
	if($step >= 4 || $prdStep >= 4){
		$stepValidation = array_merge($step4Validation->getErrors(),$stepValidation);
		if (count($step4Validation->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "pricing_tab";
		}
	}
	if(!empty($stepValidation)){
		foreach ($stepValidation as $key => $value) {
			$validate->setError($key,$value);
		}
	}
	if(($submit_type=='continue' || $submit_type=='save')){
		$exit_with_error="true";
		if ($validate->isValid()){
			$exit_with_error="false";
		}
	}
	
	if ($validate->isValid() || $submit_type=='continue' || $submit_type=='save') {
		$response['exit_with_error']=$exit_with_error;
		
		if($step >= 1 || $prdStep >= 1){
			if($company_id=="new_company" && !empty($company_name)){
				$ins_params = array(
			          'company_name' => $company_name,
			          'site_url' => '',
			          'short_name' => str_replace(" ","_", $company_name)
			    );
			    $company_id = $pdo->insert("prd_company", $ins_params);
			    $response['new_company_id']=$company_id;
			    $response['new_company_name']=$company_name;
			}
			if($category_id=="new_category" && !empty($category_name)){
				$ins_params = array(
			          'title' => $category_name,
			          'status' => 'Active',
			          'admin_id' => $_SESSION['admin']['id'],
			    );
			    $category_id = $pdo->insert("prd_category", $ins_params);
			    $response['new_category_id']=$category_id;
			    $response['new_category_title']=$category_name;
			}

			$insParams=array(
				'company_id'=>$company_id,
				'name'=>$product_name,
				'product_code'=>$product_code,
				'category_id'=>$category_id,
				'carrier_id'=>$product_carrier,
				'type'=>'Normal',
				'product_type'=>$product_type,
				'main_product_type'=>$main_product_type,
				'is_life_insurance_product'=>$is_life_insurance_product,
				'is_short_term_disablity_product'=>$is_short_term_disablity_product,
				'is_gap_plus_product'=>($product_type == "Group Enrollment") ? $is_gap_plus_product : 'N',
				'deduction'=>($product_type == "Group Enrollment") ? $deduction : 'pre_tax',
				'life_term_type'=>'',
				'guarantee_issue_amount_type'=>'',
				'primary_issue_amount'=>0,
				'spouse_issue_amount'=>0,
				'is_spouse_issue_amount_larger'=>'N',
				'child_issue_amount'=>0,
				'monthly_benefit_allowed' =>0,
				'percentage_of_salary' => 0,
				'admin_id' => $_SESSION['admin']['id'],
			);
			if($product_type=='Add On Only Product'){
				$insParams['is_add_on_product'] = 'Y';
			}else{
				$insParams['is_add_on_product'] = 'N';
			}

			if($is_life_insurance_product == 'Y'){
				$insParams['life_term_type'] = $life_term_type;
				$insParams['guarantee_issue_amount_type'] = implode(",", $guarantee_issue_amount_type);
				

				if(in_array("Primary", $guarantee_issue_amount_type)){
					$insParams['primary_issue_amount'] = $primary_issue_amount;
				}
				if(in_array("Spouse", $guarantee_issue_amount_type)){
					$insParams['spouse_issue_amount'] = $spouse_issue_amount;
					$insParams['is_spouse_issue_amount_larger'] = $is_spouse_issue_amount_larger;
				}
				if(in_array("Child", $guarantee_issue_amount_type)){
					$insParams['child_issue_amount'] = $child_issue_amount;
				}
			}
			if($is_short_term_disablity_product == 'Y'){
				$insParams['monthly_benefit_allowed'] = $monthly_benefit_allowed;
				$insParams['percentage_of_salary'] = $percentage_of_salary;
			}
			if($product_type == "Group Enrollment" && $is_gap_plus_product == 'Y'){
				$insParams['annual_hrm_payment'] = (!empty($annual_hrm_payment)?json_encode($annual_hrm_payment):'');
				$insParams['is_require_out_of_pocket_maximum'] = $is_require_out_of_pocket_maximum;
				$insParams['is_benefit_amount_limit'] = $is_benefit_amount_limit;
				$insParams['minimum_benefit_amount'] = $minimum_benefit_amount;
				$insParams['maximum_benefit_amount'] = $maximum_benefit_amount;
				$insParams['is_set_default_out_of_pocket_maximum'] = $is_set_default_out_of_pocket_maximum;
				$insParams['default_out_of_pocket_maximum'] = $default_out_of_pocket_maximum;
				$insParams['gap_home_savings_recommend_text'] = $gap_home_savings_recommend_text;

				if($gap_home_savings_recommend_text == "most_expensive") {
					$insParams['gap_custom_recommendation_text'] = "Most expensive product available with savings";

				} else if($gap_home_savings_recommend_text == "least_expensive") {
					$insParams['gap_custom_recommendation_text'] = "Least expensive product available with savings";

				} else {
					$insParams['gap_custom_recommendation_text'] = $gap_custom_recommendation_text;
				}
			}
			
			if(empty($product_id)){
				$order_by = 1;
				$sqlPrdOrd = "SELECT order_by FROM prd_main where category_id=:category_id and is_deleted='N' order by order_by DESC";
				$resPrdOrd=$pdo->selectOne($sqlPrdOrd,array(":category_id"=>$category_id));

				if(!empty($resPrdOrd)){
					$order_by = $resPrdOrd['order_by'] + 1;
				}
				$insParams['dataStep'] = $step;
				$insParams['parent_product_id'] = $parent_product_id;
				$insParams['record_type'] = $record_type;
				$insParams['status'] = 'Pending';
				$insParams['order_by'] = $order_by;
				$product_id=$pdo->insert('prd_main',$insParams);

				$errParams=array(
					'errorJson'=>json_encode($allValidation),
					'product_id'=>$product_id,
				);
				$pdo->insert("prd_product_builder_validation",$errParams);	

				$extraLink='';
				if(!empty($parent_product_id)){
					$extraLink='&parentProduct='.$parent_product_id;
				}
				
				$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Created Product','Admin Created Product');	
			}else{
				if(!empty($resPrdStep)){
					if($resPrdStep['dataStep'] <  $step){
						$insParams['dataStep'] = $step;
					}
				}
				
				$updWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$product_id)
				);
				$activity= $pdo->update("prd_main",$insParams,$updWhere,true);


				if(!empty($activity)){
					$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$insParams,'Settings',$product_code);							
				}
			}
			
			//*************** Plan Codes Code Start ***************
				if(($record_type!='Primary' && !in_array("Settings",$matchGlobal)) || $record_type=='Primary'){
					$sqlCheckPlanCode="SELECT id,plan_code_value from prd_plan_code where is_deleted='N' AND product_id = :product_id";
					$resCheckPlanCode=$pdo->select($sqlCheckPlanCode,array(":product_id"=>$product_id));

					$planCodeArray = array();
					if(!empty($resCheckPlanCode)){
						foreach ($resCheckPlanCode as $key => $value) {
							$planCodeArray[$value['id']] = $value['plan_code_value'];
						}
					}
					
					$planCodeResult=array_diff_key($planCodeArray,$product_plan_code);
					
					if(!empty($planCodeResult)){
						foreach ($planCodeResult as $key => $value) {
							if($key > 0){
								$updatePlanCodeParams=array(
									'is_deleted'=>'Y'
								);
								$updatePlanCodeWhere=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$key,
									)
								);
								$pdo->update("prd_plan_code",$updatePlanCodeParams,$updatePlanCodeWhere);

								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Plan Code '.$value,'Admin Updated Product','Settings');
							}
						}
					}

					if(!empty($product_plan_code)){
						$prdPlanCodeArray=array();
						foreach ($product_plan_code as $key => $value) {

							$sqlPlanCode="SELECT id,code_no,plan_code_value FROM prd_plan_code where product_id=:product_id AND id=:id AND is_deleted='N'";
							$resPlanCode=$pdo->selectOne($sqlPlanCode,array(":product_id"=>$product_id,":id"=>$key));

							if(!empty($resPlanCode)){
								$updPlanParams=array(
									"plan_code_value"=>$value,
								);
								$updPlanWhere=array(
									'clause'=>'id=:id',
									'params'=>array(":id"=>$key)
								);
								$activity = $pdo->update("prd_plan_code",$updPlanParams,$updPlanWhere,true);

								if(!empty($activity)){

									$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updPlanParams,'Settings',$product_code);
								}
								

							}else{
								
								$ins_params = array(
									"product_id"=>$product_id,
									"plan_code_value"=>$value,
							    );
							    if(abs($key)==0){
									$ins_params["code_no"]='GC';
							    }else{
							    	$ins_params["code_no"]='PC';
							    }
							    $prdPlanCodeId = $pdo->insert("prd_plan_code", $ins_params);
							    $prdPlanCodeArray[$key]=$prdPlanCodeId;
							    $actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Plan Code '.$value,' Admin Updated Product','Settings');
								
							}
						}
						$response['prdPlanCodeArray']=$prdPlanCodeArray;
					}
				}
			//*************** Plan Codes Code End   ***************

			//*************** Member Portal Info Code Start ***************
				if(($record_type!='Primary' && !in_array("MemberPortalInformation",$matchGlobal)) || $record_type=='Primary'){
					$sqlCheckMemberPortal="SELECT id,name from prd_member_portal_information where is_deleted='N' AND product_id = :product_id";
					$resCheckMemberPortal=$pdo->select($sqlCheckMemberPortal,array(":product_id"=>$product_id));

					$memberPortalArray = array();
					if(!empty($resCheckMemberPortal)){
						foreach ($resCheckMemberPortal as $key => $value) {
							$memberPortalArray[$value['id']] = $value['name'];
						}
					}
					
					$memberPortalResult=array_diff_key($memberPortalArray,$department_name);
					
					if(!empty($memberPortalResult)){
						foreach ($memberPortalResult as $key => $value) {
							if($key > 0){
								$updateMemberPortalParams=array(
									'is_deleted'=>'Y'
								);
								$updateMemberPortalWhere=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$key,
									)
								);
								$pdo->update("prd_member_portal_information",$updateMemberPortalParams,$updateMemberPortalWhere);
								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Member Portal Section '.$value,'Admin Updated Product','Member Portal Information');
							}
						}
					}
					if(!empty($department_name) && count($department_name) > 0){
						foreach ($department_name as $key => $value) {

							$sqlDept="SELECT id,name,description FROM prd_member_portal_information where product_id=:product_id AND id=:id AND is_deleted='N'";
							$resDept=$pdo->selectOne($sqlDept,array(":product_id"=>$product_id,":id"=>$key));
							$oldVaArray = $resDept;
							if(!empty($resDept)){
								$updPortalParams=array(
									"name"=>$value,
									"description"=>$department_desc[$key],
								);
								$updPortalWhere=array(
									'clause'=>'id=:id',
									'params'=>array(":id"=>$key)
								);
								$pdo->update("prd_member_portal_information",$updPortalParams,$updPortalWhere);
								$NewVaArray = $updPortalParams;
								unset($oldVaArray['id']);

								$activity=array_diff_assoc($oldVaArray,$NewVaArray);
								if(!empty($activity)){
									if(array_key_exists('description',$activity)){
										$sectionName="Member Portal Information";

										$tmp = array();
										$tmp2 = array();
										$tmp['description']=base64_encode($activity['description']);
										$tmp2['description']=base64_encode($updPortalParams['description']);
										
										
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code,true);
									}
									if(array_key_exists('name',$activity)){
										$sectionName="Member Portal Information";

										$tmp = array();
										$tmp2 = array();
										$tmp['Section Name']=$activity['name'];
										$tmp2['Section Name']=$updPortalParams['name'];
										
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code);
									}

								}
							

							}else{
								if(!empty($value) || !empty($department_desc[$key])){
									$ins_params = array(
										"product_id"=>$product_id,
								    );
								    if(!empty($value)){
								    	$ins_params['name']=$value;
								    }
								    if(!empty($department_desc[$key])){
										$ins_params["description"]=$department_desc[$key];
								    }
								    $depId = $pdo->insert("prd_member_portal_information", $ins_params);

								    $actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Member Portal Section '.$value,'Admin Updated Product','Member Portal Information');

								    $departmentArray[$key]=$depId;
								}
							}
						}
						$response['departmentArray']=$departmentArray;
					}
				}
			//*************** Member Portal Info Code End   ***************
			
			//*************** Description Code Start ***************
				if(($record_type!='Primary' && (!in_array("EnrollmentPage",$matchGlobal) || !in_array("AgentEnrollmentInformation",$matchGlobal) || !in_array("LimitationAndExclusions",$matchGlobal))) || $record_type=='Primary'){
					$sqlDesc = "SELECT id,enrollment_desc,agent_portal,agent_info,limitations_exclusions FROM prd_descriptions where product_id = :product_id";
					$resDesc = $pdo->selectOne($sqlDesc,array(":product_id"=>$product_id));

					$oldVaArray = $resDesc;
					$descParams=array(
						'enrollment_desc'=>$enrollmentPage,
						'agent_portal'=>$agent_portal,
						'agent_info'=>$agentInfoProductBox,
						'limitations_exclusions'=>$limitations_exclusions,
					);
					if(!empty($resDesc)){
						$descWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$resDesc['id'])
						);
						$pdo->update("prd_descriptions",$descParams,$descWhere);

						
						$NewVaArray = $descParams;
						unset($oldVaArray['id']);

						$activity=array_diff_assoc($oldVaArray,$NewVaArray);

						if(!empty($activity)){
							$is_description = false;

							if(array_key_exists('enrollment_desc',$activity)){

								$sectionName="Enrollment Page";
								$tmp = array();
								$tmp2 = array();
								$tmp['enrollment_desc']=base64_encode($activity['enrollment_desc']);
								$tmp2['enrollment_desc']=base64_encode($descParams['enrollment_desc']);

								$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code,true);
							}
							if(array_key_exists('limitations_exclusions',$activity)){
								$sectionName="Limitations and Exclusions";
								$tmp = array();
								$tmp2 = array();
								$tmp['limitations_exclusions']=base64_encode($activity['limitations_exclusions']);
								$tmp2['limitations_exclusions']=base64_encode($descParams['limitations_exclusions']);

								$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code,true);
							}
							if(array_key_exists('agent_portal',$activity)){
								$sectionName="Agent Application Information";

								$tmp = array();
								$tmp2 = array();
								$tmp['agent_portal']=base64_encode($activity['agent_portal']);
								$tmp2['agent_portal']=base64_encode($descParams['agent_portal']);
								
								$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code,true);
							}
							if(array_key_exists('agent_info',$activity)){
								$sectionName="Agent Application Information";

								$tmp = array();
								$tmp2 = array();
								$tmp['agent_info']=$activity['agent_info'];
								$tmp2['agent_info']=$descParams['agent_info'];
								
								$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code);
							}														
						}
					}else{
						$descParams['product_id']=$product_id;
						$pdo->insert("prd_descriptions",$descParams);
						
					}
				}
			//*************** Description Code End   ***************

			if($record_type=='Variation'){

				$sqlMatchGlobal= "SELECT id from prd_match_globals WHERE product_id=:product_id AND is_deleted='N'";
				$resMatchGlobal= $pdo->selectOne($sqlMatchGlobal,array(":product_id"=>$product_id));

				$insMatchGlobal = array("match_globals"=>$matchGlobalList);
				if(!empty($resMatchGlobal)){
					$matchGlobalWhr=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$resMatchGlobal['id'])
					);
					$activity =$pdo->update("prd_match_globals",$insMatchGlobal,$matchGlobalWhr,true);
					if(!empty($activity)){
						$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$insMatchGlobal,'Match Globals',$product_code);
					}
				}else{
					$insMatchGlobal["product_id"]=$product_id;
					$pdo->insert("prd_match_globals",$insMatchGlobal);

					$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Match Globals'.$matchGlobalList,'Admin Updated Product');	
				}
			}

			$prdMatchGlobal=$functionsList->prdMatchGlobal($product_id,$parent_product_id,$record_type,1);
		}
		if($step >=2 || $prdStep >= 2){
			if(($record_type!='Primary' && !in_array("EffecttiveDate",$matchGlobal)) || $record_type=='Primary'){
				$updParams=array(
					'direct_product'=>$direct_product,
					'effective_day'=>0,
					'sold_day'=>0,
				);
				if($direct_product=="Select Day Of Month"){
					$updParams['effective_day']=$effective_day;
					$updParams['effective_day2']= (!empty($effective_day2) ? $effective_day2 : 0);
					$updParams['sold_day']=$sold_day;
				}

				if($direct_product=="First Of Month"){
					$updParams['sold_day']=$sold_day;
				}
				$updWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$product_id)
				);
				
				$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
				if(!empty($activity)){
					$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Effective Date',$product_code);
				}
			}
			if(($record_type!='Primary' && !in_array("MembershipRequirement",$matchGlobal)) || $record_type=='Primary'){
				$updParams=array(
					'membership_ids'=>'',
				);
				if($is_membership_require=="Y" && !empty($membership_ids_check_array)){
					$updParams['membership_ids']=implode(",", $membership_ids_check_array);
				}
				$updWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$product_id)
				);
				$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
				if(!empty($activity)){
					$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Membership Requirements',$product_code);
				}
			}
			
			
			if(($record_type!='Primary' && !in_array("Availability",$matchGlobal)) || $record_type=='Primary'){
				$updParams=array(
					'is_specific_zipcode'=>$is_specific_zipcode,
					'no_sale_state_coverage_continue'=>$no_sale_state_coverage_continue,
				);
				if($is_membership_require=="Y" && !empty($membership_ids_check_array)){
					$updParams['membership_ids']=implode(",", $membership_ids_check_array);
				}
				$updWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$product_id)
				);
				$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
				if(!empty($activity)){
					$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Availability',$product_code);
				}

				//************* Available State Code START ****************
					$sqlCheckState="SELECT state_name from prd_available_state where is_deleted='N' AND product_id = :product_id";
					$resCheckState=$pdo->select($sqlCheckState,array(":product_id"=>$product_id));
					
					$availStateArray = array();
					
					if(!empty($resCheckState)){
						foreach ($resCheckState as $key => $value) {
							array_push($availStateArray, $value['state_name']);
						}
					}

					$stateResult=array_diff($availStateArray,$available_state_array);
					if(!empty($stateResult)){
						foreach ($stateResult as $key => $value) {
							$updStateParams=array(
								'is_deleted'=>'Y'
							);
							$updStateWhere=array(
								'clause'=>'is_deleted="N" AND product_id = :product_id AND state_name=:state_name',
								'params'=>array(
									":product_id"=>$product_id,
									":state_name"=>$value
								)
							);
							$pdo->update("prd_available_state",$updStateParams,$updStateWhere);
							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Available State '.$value,'Admin Updated Product','Availability');
						}
					}

					$stateResult=array_diff($available_state_array,$availStateArray);

					if(!empty($stateResult)){
						foreach ($stateResult as $key => $value) {
							$state_id=$allStateResByName[$value]['id'];
							$insStateParams = array(
					            "product_id" => $product_id,
					            "state_id" => $state_id,
					            "state_name" => $value,
				          	);
				          	$prd_available_state = $pdo->insert('prd_available_state',$insStateParams);
				          	$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Available State '.$value,'Admin Updated Product','Availability');
						}
					}
				//************* Available State Code end   ****************

				//************* No Sale State Management Code start ****************
					//*************** Available No Sale State Delete Code start *********************
						$availableNoStateArray =array();
						if(!empty($available_no_sale_state)){
							foreach ($available_no_sale_state as $key => $value) {
								$state_name=$allStateRes[$key]['name'];
								if(in_array($state_name, $available_state_array)){
									unset($available_no_sale_state[$key]);
								}else{
									array_push($availableNoStateArray, $key);
								}
							}
						}

						$sqlCheckNoSaleState="SELECT state_id from prd_no_sale_states where is_deleted='N' AND product_id = :product_id";
						$resCheckNoSaleState=$pdo->select($sqlCheckNoSaleState,array(":product_id"=>$product_id));

						$noSaleStateArray = array();
						if(!empty($resCheckNoSaleState)){
							foreach ($resCheckNoSaleState as $key => $value) {
								array_push($noSaleStateArray, $value['state_id']);
							}
						}
						
						$noSaleStateResult=array_diff($noSaleStateArray,$availableNoStateArray);

						if(!empty($noSaleStateResult)){
							foreach ($noSaleStateResult as $key => $value) {
								$updateStateParams=array(
									'is_deleted'=>'Y'
								);
								$updateStateWhere=array(
									'clause'=>'state_id=:state_id AND product_id =:product_id',
									'params'=>array(
										":product_id"=>$product_id,
										":state_id"=>$value,
									)
								);
								$pdo->update("prd_no_sale_states",$updateStateParams,$updateStateWhere);
								$state_name=$allStateRes[$value]['name'];
								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed No Sale State '.$state_name,'Admin Updated Product','Availability');
								
							}
						}
					//*************** Available No Sale State Delete Code end   *********************

					//*********** Available No Sale State Insert/Update Code start *************
						if(!empty($available_no_sale_state)){
							foreach ($available_no_sale_state as $keyc => $valuec) {
								foreach ($valuec as $key => $value) {

									$effective_date_str=(!empty($value['effective_date'])) ? date('Y-m-d',strtotime($value['effective_date'])) : NULL;
									$termination_date_str=(!empty($value['termination_date'])) ? date('Y-m-d',strtotime($value['termination_date'])) : NULL;
									
									$sqlCheckNoSaleState="SELECT id,termination_date FROM prd_no_sale_states 
									where id = :id AND product_id=:product_id AND is_deleted='N'";
									$resCheckNoSaleState=$pdo->selectOne($sqlCheckNoSaleState,array(
										':id'=>$key,":product_id"=>$product_id,
									));

									if(!empty($resCheckNoSaleState)){
										$updStateParams = array(
											'effective_date'=>(!empty($value['effective_date'])) ? date('Y-m-d',strtotime($value['effective_date'])) : NULL,
											'termination_date'=>(!empty($value['termination_date'])) ? date('Y-m-d',strtotime($value['termination_date'])) : NULL,
										);
										$updStateWhere=array(
											'clause'=>"id=:id",
											'params'=>array(":id"=>$resCheckNoSaleState['id'])
										);
										
										$activity = $pdo->update("prd_no_sale_states",$updStateParams,$updStateWhere,true);
										if(!empty($activity)){
											if(empty($resCheckNoSaleState['termination_date']) && empty($updStateParams['termination_date'])){
												unset($activity['termination_date']);
											}
											if(!empty($activity)){
												$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updStateParams,'Availability',$product_code);

											}
										}
									}else{
										$state_name=$allStateRes[$keyc]['name'];
										$insertStateParams=array(
											'product_id'=>$product_id,
											'state_id'=>$keyc,
											'state_name'=>$state_name,
											'is_deleted'=>'N',
											'effective_date'=>(!empty($value['effective_date'])) ? date('Y-m-d',strtotime($value['effective_date'])) : NULL,
											'termination_date'=>(!empty($value['termination_date'])) ? date('Y-m-d',strtotime($value['termination_date'])) : NULL,
										);
										$noSaleStateId=$pdo->insert("prd_no_sale_states",$insertStateParams);
										$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added No Sale State '.$state_name,'Admin Updated Product','Availability');
										$prdNoSaleStateArray[$keyc][$key]=$noSaleStateId;
									}
							  	}
							}
							if(!empty($prdNoSaleStateArray)){
								$response['prdNoSaleStateArray']=$prdNoSaleStateArray;
							}
						}
					//*********** Available No Sale State Insert/Update Code end   *************
				//************* No Sale State Management Code end   ****************
			
				//*************Specific Zip Code Start ****************
					$sqlCheckSpecificZipCode="SELECT state_name,zipcode from prd_specific_zipcode where is_deleted='N' AND product_id = :product_id";
					$resCheckSpecificZipCode=$pdo->select($sqlCheckSpecificZipCode,array(":product_id"=>$product_id));
					
					$specificZipCodeArray = array();
					if(!empty($resCheckSpecificZipCode)){
						foreach ($resCheckSpecificZipCode as $key => $value) {
							$specificZipCodeArray[$value['state_name']."_".$value['zipcode']]=$value['zipcode'];
						}
					}
					
					$specificZipCodeResult=array_diff_key($specificZipCodeArray,$available_zipcode_list);
					
					if(!empty($specificZipCodeResult)){
						foreach ($specificZipCodeResult as $key => $value) {
							$keyDiff = explode("_", $key);
							$state_name = $keyDiff[0];
							$zipcode = $keyDiff[1];

							$updateZipParams=array(
								'is_deleted'=>'Y'
							);
							$updateZipWhere=array(
								'clause'=>'state_name=:state_name AND zipcode=:zipcode AND product_id =:product_id',
								'params'=>array(
									":product_id"=>$product_id,
									":zipcode"=>$zipcode,
									":state_name"=>$state_name,
								)
							);
							$pdo->update("prd_specific_zipcode",$updateZipParams,$updateZipWhere);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Specific ZipCode '.$value,'Admin Updated Product','Availability');
						}
					}
					
					$specificZipCodeResult=array_diff_key($available_zipcode_list,$specificZipCodeArray);

					if(!empty($specificZipCodeResult)){
						foreach ($specificZipCodeResult as $key => $value) {
							$keyDiff = explode("_", $key);
							$state_name = $keyDiff[0];
							$zipcode = $keyDiff[1];
							$state_id=$allStateResByName[$state_name]['id'];
							$ins_params = array(
								"product_id"=>$product_id,
								"state_id"=>$state_id,
								"zipcode"=>$zipcode,
								"state_name"=>$state_name,
						    );
						    $pdo->insert("prd_specific_zipcode", $ins_params);

						    $actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Specific ZipCode '.$zipcode,'Admin Updated Product','Availability');
						}
					}
				//*************Specific Zip Code end   ****************
			}
			
			//************* Coverage Options Code Start ****************
				if(($record_type!='Primary' && !in_array("CoverageOptions",$matchGlobal)) || $record_type=='Primary'){
					$updParams=array(
						'family_plan_rule'=>'',
					);
					if(in_array(4, $coverage_options_array)){
						$updParams['family_plan_rule']=$family_plan_rule;
					}
					$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$product_id)
					);
					
					$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
					if(!empty($activity)){
						$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Coverage Options',$product_code);
					}

					$sqlCheckCoverage="SELECT prd_plan_type_id from prd_coverage_options where is_deleted='N' AND product_id = :product_id";
					$resCheckCoverage=$pdo->select($sqlCheckCoverage,array(":product_id"=>$product_id));

					$coverageOptionArray = array();
					if(!empty($resCheckCoverage)){
						foreach ($resCheckCoverage as $key => $value) {
							array_push($coverageOptionArray, $value['prd_plan_type_id']);
						}
					}

					$coverageOptionResult=array_diff($coverageOptionArray,$coverage_options_array);

					if(!empty($coverageOptionResult)){
						foreach ($coverageOptionResult as $key => $value) {
							$updCoverageParams=array(
								'is_deleted'=>'Y'
							);
							$updCoverageWhere=array(
								'clause'=>'is_deleted="N" AND product_id = :product_id and prd_plan_type_id=:prd_plan_type_id',
								'params'=>array(
									":product_id"=>$product_id,
									":prd_plan_type_id"=>$value,
								)
							);
							$pdo->update("prd_coverage_options",$updCoverageParams,$updCoverageWhere);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Plan Options '.$prdPlanTypeArray[$value]['title'],'Admin Updated Product','Plan Options');
						}
					}

					$coverageOptionResult=array_diff($coverage_options_array,$coverageOptionArray);
					if(!empty($coverageOptionResult)){
						foreach ($coverageOptionResult as $key => $value) {
							$insCoverageParams = array(
				            	"product_id" => $product_id,
				            	"prd_plan_type_id" => $value,
			          		);
			          		$prd_coverage_options = $pdo->insert('prd_coverage_options',$insCoverageParams);

			          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Plan Options '.$prdPlanTypeArray[$value]['title'],'Admin Updated Product','Plan Options');
						}
					}
				}
			//************* Coverage Options Code end   ****************

			//************* Sub product Management Code start ****************
				if(($record_type!='Primary' && !in_array("SubProducts",$matchGlobal)) || $record_type=='Primary'){

					$sqlCheckSubProducts="SELECT sub_product_id from prd_sub_products where is_deleted='N' AND product_id = :product_id";
					$resCheckSubProducts=$pdo->select($sqlCheckSubProducts,array(":product_id"=>$product_id));

					$subProductsArray = array();
					if(!empty($resCheckSubProducts)){
						foreach ($resCheckSubProducts as $key => $value) {
							array_push($subProductsArray, $value['sub_product_id']);
						}
					}

					$subProductsResult=array_diff($subProductsArray,$sub_product_array);

					if(!empty($subProductsResult)){
						foreach ($subProductsResult as $key => $value) {
							$updSubProductsParams=array(
								'is_deleted'=>'Y'
							);
							$updSubProductsWhere=array(
								'clause'=>'product_id=:product_id and sub_product_id = :sub_product_id AND is_deleted="N"',
								'params'=>array(
									":sub_product_id"=>$value,
									":product_id"=>$product_id
								)
							);
							$pdo->update("prd_sub_products",$updSubProductsParams,$updSubProductsWhere);

							$resP=$pdo->selectOne("SELECT CONCAT(product_name,'(',product_code,')') as sub_products FROM sub_products where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Sub Product '.$resP['sub_products'],'Admin Updated Product','Sub Products');
						}
					}

					$subProductsResult=array_diff($sub_product_array,$subProductsArray);
					if(!empty($subProductsResult)){
						foreach ($subProductsResult as $key => $value) {
							$insSubParams=array(
								'product_id'=>$product_id,
								'sub_product_id'=>$value,
							);
							$pdo->insert('prd_sub_products',$insSubParams);		
							$resP=$pdo->selectOne("SELECT CONCAT(product_name,'(',product_code,')') as sub_products FROM sub_products where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Sub Product '.$resP['sub_products'],'Admin Updated Product','Sub Products');				
						}
					}
				}
			//************* Sub product Management Code end   ****************	

				
			//************* Product Combination Rule Code Start ****************
				if(($record_type!='Primary' && !in_array("ProductCombinationRules",$matchGlobal)) || $record_type=='Primary'){
					$sqlCheckCombination="SELECT combination_type,combination_product_id from prd_combination_rule where is_deleted='N' AND product_id = :product_id";
					$resCheckCombination=$pdo->select($sqlCheckCombination,array(":product_id"=>$product_id));

					$excludeProductArray = array();
					$autoAssignProductArray = array();
					$requiredProductArray = array();
					$packagedProductArray = array();
					if(!empty($resCheckCombination)){
						foreach ($resCheckCombination as $key => $value) {
							if($value['combination_type']=='Excludes'){
								array_push($excludeProductArray, $value['combination_product_id']);
							}
							if($value['combination_type']=='Required'){
								array_push($requiredProductArray, $value['combination_product_id']);
							}
							if($value['combination_type']=='Auto Assign'){
								array_push($autoAssignProductArray, $value['combination_product_id']);
							}
							if($value['combination_type']=='Packaged'){
								array_push($packagedProductArray, $value['combination_product_id']);
							}
							
						}
					}

					$excludeResult=array_diff($excludeProductArray,$excludeProduct_array);
					if(!empty($excludeResult)){
						foreach ($excludeResult as $key => $value) {
							$updCombinationParams=array(
								'is_deleted'=>'Y'
							);
							$updCombinationWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id and combination_product_id=:combination_product_id AND combination_type='Excludes'",
								'params'=>array(
									":product_id"=>$product_id,
									":combination_product_id"=>$value,
								)
							);
							$pdo->update("prd_combination_rule",$updCombinationParams,$updCombinationWhere);

							$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Excludes Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');	
						}
					}
					$excludeResult=array_diff($excludeProduct_array,$excludeProductArray);
					if(!empty($excludeResult)){
						foreach ($excludeResult as $key => $value) {
							$insCombinationParams = array(
				            	"product_id" => $product_id,
				            	"combination_product_id" => $value,
				            	"combination_type" => 'Excludes',
			          		);
			          		$prd_coverage_options = $pdo->insert('prd_combination_rule',$insCombinationParams);

			          		$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Excludes Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');
						}
					}

					$requiredResult=array_diff($requiredProductArray,$requiredProduct_array);
					if(!empty($requiredResult)){
						foreach ($requiredResult as $key => $value) {
							$updCombinationParams=array(
								'is_deleted'=>'Y'
							);
							$updCombinationWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id and combination_product_id=:combination_product_id AND combination_type='Required'",
								'params'=>array(
									":product_id"=>$product_id,
									":combination_product_id"=>$value,
								)
							);
							$pdo->update("prd_combination_rule",$updCombinationParams,$updCombinationWhere);
							$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Required Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');
						}
					}
					$requiredResult=array_diff($requiredProduct_array,$requiredProductArray);
					if(!empty($requiredResult)){
						foreach ($requiredResult as $key => $value) {
							$insCombinationParams = array(
				            	"product_id" => $product_id,
				            	"combination_product_id" => $value,
				            	"combination_type" => 'Required',
			          		);
			          		$prd_coverage_options = $pdo->insert('prd_combination_rule',$insCombinationParams);
			          		$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Required Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');						
						}
					}

					$autoAssignResult=array_diff($autoAssignProductArray,$autoAssignProduct_array);
					if(!empty($autoAssignResult)){
						foreach ($autoAssignResult as $key => $value) {
							$updCombinationParams=array(
								'is_deleted'=>'Y'
							);
							$updCombinationWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id and combination_product_id=:combination_product_id AND combination_type='Auto Assign'",
								'params'=>array(
									":product_id"=>$product_id,
									":combination_product_id"=>$value,
								)
							);
							$pdo->update("prd_combination_rule",$updCombinationParams,$updCombinationWhere);
							$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Auto Assign Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');	
						}
					}
					$autoAssignResult=array_diff($autoAssignProduct_array,$autoAssignProductArray);
					if(!empty($autoAssignResult)){
						foreach ($autoAssignResult as $key => $value) {
							$insCombinationParams = array(
				            	"product_id" => $product_id,
				            	"combination_product_id" => $value,
				            	"combination_type" => 'Auto Assign',
			          		);
			          		$prd_coverage_options = $pdo->insert('prd_combination_rule',$insCombinationParams);	
			          		$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Auto Assign Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');					
						}
					}

					$packagedResult=array_diff($packagedProductArray,$packagedProduct_array);
					if(!empty($packagedResult)){
						foreach ($packagedResult as $key => $value) {
							$updCombinationParams=array(
								'is_deleted'=>'Y'
							);
							$updCombinationWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id and combination_product_id=:combination_product_id AND combination_type='Packaged'",
								'params'=>array(
									":product_id"=>$product_id,
									":combination_product_id"=>$value,
								)
							);
							$pdo->update("prd_combination_rule",$updCombinationParams,$updCombinationWhere);
							$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Packaged Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');	
						}
					}
					$packagedResult=array_diff($packagedProduct_array,$packagedProductArray);
					if(!empty($packagedResult)){
						foreach ($packagedResult as $key => $value) {
							$insCombinationParams = array(
				            	"product_id" => $product_id,
				            	"combination_product_id" => $value,
				            	"combination_type" => 'Packaged',
			          		);
			          		$prd_coverage_options = $pdo->insert('prd_combination_rule',$insCombinationParams);	
			          		$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$value));

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Packaged Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');					
						}
					}
				}
			//************* Product Combination Rule Code end   ****************
			
			if(($record_type!='Primary' && !in_array("TerminationRules",$matchGlobal)) || $record_type=='Primary'){
				$updParams=array(
					'termination_rule'=>$termination_rule,
					'term_back_to_effective'=>$term_back_to_effective,
					'term_automatically'=>$term_automatically,
					'term_automatically_within'=>'0',
					'term_automatically_within_type'=>'Days',
					'reinstate_option'=>$reinstate_option,
					'reinstate_within'=>0,
					'reinstate_within_type'=>'Days',
					'reenroll_options'=>$reenroll_options,
					'reenroll_within'=>0,
					'reenroll_within_type'=>'Days',
				);
				if($term_automatically=='Y'){
					$updParams['term_automatically_within']=$term_automatically_within;
					$updParams['term_automatically_within_type']=$term_automatically_within_type;
				}
				if($reinstate_option=='Available Within Specific Time Frame'){
					$updParams['reinstate_within']=$reinstate_within;
					$updParams['reinstate_within_type']=$reinstate_within_type;
				}
				if($reenroll_options=='Available After Specific Time Frame'){
					$updParams['reenroll_within']=$reenroll_within;
					$updParams['reenroll_within_type']=$reenroll_within_type;
				}
				$updWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$product_id)
				);
				$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
				if(!empty($activity)){
					$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Termination Rules',$product_code);
				}
			}

			$prdMatchGlobal=$functionsList->prdMatchGlobal($product_id,$parent_product_id,$record_type,2);
		}
		if($step >=3 || $prdStep >= 3){	
			//*************** Age Restrictions Code Start ***************
				if(($record_type!='Primary' && !in_array("AgeRestrictions",$matchGlobal)) || $record_type=='Primary'){

					$updParams=array(
						'is_primary_age_restrictions'=>$is_primary_age_restrictions,
						'primary_age_restrictions_from'=>0,
						'primary_age_restrictions_to'=>0,
						'is_children_age_restrictions'=>$is_children_age_restrictions,
						'children_age_restrictions_from'=>0,
						'children_age_restrictions_to'=>0,
						'is_spouse_age_restrictions'=>$is_spouse_age_restrictions,
						'spouse_age_restrictions_from'=>0,
						'spouse_age_restrictions_to'=>0,
						'maxAgeAutoTermed'=>'N',
						'allowedBeyoundAge'=>'N',
					);
					if($is_primary_age_restrictions == 'Y'){
						$updParams['primary_age_restrictions_from']=$primary_age_restrictions_from;	
						$updParams['primary_age_restrictions_to']=$primary_age_restrictions_to;	
					}
					if($is_children_age_restrictions == 'Y'){
						$updParams['children_age_restrictions_from']=$children_age_restrictions_from;	
						$updParams['children_age_restrictions_to']=$children_age_restrictions_to;
					}	
					if($is_spouse_age_restrictions == 'Y'){
						$updParams['spouse_age_restrictions_from']=$spouse_age_restrictions_from;	
						$updParams['spouse_age_restrictions_to']=$spouse_age_restrictions_to;	
					}
					if($is_primary_age_restrictions == "Y" || 	$is_spouse_age_restrictions == "Y" || $is_children_age_restrictions == "Y"){
						$updParams['maxAgeAutoTermed']=$maxAgeAutoTermed;	
						$updParams['allowedBeyoundAge']=$allowedBeyoundAge;	
					}
					$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$product_id)
					);
					$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
					if(!empty($activity)){
						$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Age Restrictions',$product_code);
					}

					$sqlCheckMaxAgeTerm="SELECT id,member_type from prd_max_age_terminaion where is_deleted='N' AND product_id = :product_id";
					$resCheckMaxAgeTerm=$pdo->select($sqlCheckMaxAgeTerm,array(":product_id"=>$product_id));

					$maxAgeTermArray = array();
					if(!empty($resCheckMaxAgeTerm)){
						foreach ($resCheckMaxAgeTerm as $key => $value) {
							$maxAgeTermArray[$value['member_type']."_".$value['id']] = $value['id'];
						}
					}
					
					$maxAgeTermResult=array_diff_key($maxAgeTermArray,$autoTermArray);
					

					if(!empty($maxAgeTermResult)){
						foreach($maxAgeTermResult as $key => $value) {
							if($value > 0){
								$keyDiff = explode("_", $key);
								$member_type = $keyDiff[0];

								$updateMaxAgeTermParams=array(
									'is_deleted'=>'Y'
								);
								$updateMaxAgeTermWhere=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$value,
									)
								);
								$pdo->update("prd_max_age_terminaion",$updateMaxAgeTermParams,$updateMaxAgeTermWhere);

								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed '.$member_type.' Auto Termed Setting','Admin Updated Product','Age Restrictions');
							}
						}
					}


					$sqlCheckBeyondAge="SELECT id,member_type from prd_beyond_age_disablity where is_deleted='N' AND product_id = :product_id";
					$resCheckBeyondAge=$pdo->select($sqlCheckBeyondAge,array(":product_id"=>$product_id));

					$maxBeyondAgeArray = array();
					if(!empty($resCheckBeyondAge)){
						foreach ($resCheckBeyondAge as $key => $value) {
							$maxBeyondAgeArray[$value['member_type']] = $value['member_type'];
						}
					}
					
					$beyondAgeResult=array_diff($maxBeyondAgeArray,$allowedBeyoundAgeType);
					
					if(!empty($beyondAgeResult)){
						foreach($beyondAgeResult as $key => $value) {
							$updateBeyondAgeParams=array(
								'is_deleted'=>'Y'
							);
							$updateBeyondAgeWhere=array(
								'clause'=>'product_id=:product_id AND member_type=:member_type AND is_deleted="N"',
								'params'=>array(
									":product_id"=>$product_id,
									":member_type"=>$value
								)
							);
							$pdo->update("prd_beyond_age_disablity",$updateBeyondAgeParams,$updateBeyondAgeWhere);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed '.$value.' Documented Disability','Admin Updated Product','Age Restrictions');
						}
					}
					if($is_primary_age_restrictions == "Y" || 	$is_spouse_age_restrictions == "Y" || $is_children_age_restrictions == "Y"){
						
						if($maxAgeAutoTermed == 'Y'){
							if(!empty($autoTermArray)){
								foreach ($autoTermArray as $key => $value) {

									$sqlPlanCode="SELECT id FROM prd_max_age_terminaion where product_id=:product_id AND id=:id AND is_deleted='N'";
									$resPlanCode=$pdo->selectOne($sqlPlanCode,array(":product_id"=>$product_id,":id"=>$value['id']));

									if(!empty($resPlanCode)){
										$updMaxAgeParams=array(
											"member_type"=>$value['member_type'],
											"terminate_within"=>$value['terminate_within'],
											"terminate_within_type"=>$value['terminate_within_type'],
											"terminate_range"=>$value['terminate_range'],
											"terminate_trigger"=>$value['terminate_trigger'],
										);
										$updMaxAgeWhere=array(
											'clause'=>'id=:id',
											'params'=>array(":id"=>$value['id'])
										);
										$activity = $pdo->update("prd_max_age_terminaion",$updMaxAgeParams,$updMaxAgeWhere,true);
										if(!empty($activity)){
											$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Age Restrictions',$product_code);
										}

									}else{
										
										$insMaxAgeParams = array(
											"product_id"=>$product_id,
											"member_type"=>$value['member_type'],
											"terminate_within"=>$value['terminate_within'],
											"terminate_within_type"=>$value['terminate_within_type'],
											"terminate_range"=>$value['terminate_range'],
											"terminate_trigger"=>$value['terminate_trigger'],
									    );
									    
									    $MaxAgeId = $pdo->insert("prd_max_age_terminaion", $insMaxAgeParams);

									    $actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$value['member_type'].' Auto Termed Setting','Admin Updated Product','Age Restrictions');

									    $prdMaxAgeArr[$value['member_type']][$value['id']]=$MaxAgeId;
									}
								}
								if(!empty($prdMaxAgeArr)){
									$response['prdMaxAgeArr']=$prdMaxAgeArr;
								}
							}
						}


						$beyondAgeResult=array_diff($allowedBeyoundAgeType,$maxBeyondAgeArray);
						if(!empty($beyondAgeResult)){
							foreach($beyondAgeResult as $key => $value) {
								$insBeyondAgeParams = array(
									"product_id"=>$product_id,
									"member_type"=>$value,
							    );
							    
							    $beyondAgeId = $pdo->insert("prd_beyond_age_disablity", $insBeyondAgeParams);
							    $actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$value.' Documented Disability','Admin Updated Product','Age Restrictions');
							}
						}
					}
				}
			//*************** Age Restrictions Code End   ***************
			
			//*************** Member Enrollment Information Code Start ***************
				if(($record_type!='Primary' && !in_array("MemberEnrollmentInformation",$matchGlobal)) || $record_type=='Primary'){

					$sqlQuestion="SELECT group_concat(prd_question_id) as prd_question_id from prd_enrollment_questions_assigned where is_deleted='N' AND product_id = :product_id";
					$resQuestion=$pdo->selectOne($sqlQuestion,array(":product_id"=>$product_id));

					$sqlQuestionDel="SELECT peq.questionType,peq.id as prd_question_id FROM prd_enrollment_questions peq
						JOIN prd_enrollment_questions_assigned peqa ON (peq.id = peqa.prd_question_id AND peqa.is_deleted='N')
						WHERE peq.is_deleted='N' AND peqa.product_id=:product_id";
					$resQuestionDel=$pdo->select($sqlQuestionDel,array(":product_id"=>$product_id));
					
					if(!empty($resQuestion['prd_question_id'])){
						$prdAssignQuestion = explode(",", $resQuestion['prd_question_id']);
					}

					$defQuestionDel=array();
					$customQuestionDel=array();
					if(!empty($resQuestionDel)){
						foreach ($resQuestionDel as $key => $value) {
							if($value['questionType']=='Custom'){
								$customQuestionDel[$value['prd_question_id']]=$value['prd_question_id'];
							}else{
								$defQuestionDel[$value['prd_question_id']]=$value['prd_question_id'];
							}
						}
					}
					$delMemberQue=array_diff_key($defQuestionDel, $memberQuestion);
					if(!empty($delMemberQue)){
						foreach ($delMemberQue as $key => $value) {
							$updQuesParams=array(
									'is_member_asked'=> 'N',
									'is_member_required'=> 'N'
							);
							$updQuesWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
								'params'=>array(
									":product_id"=>$product_id,
									":prd_question_id"=>$key,
								)
							);
							$pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere);
						}
					}
					if(!empty($memberQuestion)){
						foreach ($memberQuestion as $mainKey => $Qvalue) {
							if(!empty($mainKey)){
								if(!empty($prdAssignQuestion) && in_array($mainKey, $prdAssignQuestion)){
									$updQuesParams=array(
										'is_member_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_member_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_question_id"=>$mainKey,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);
									if(!empty($activity)){
										$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_question_id" => $mainKey,
						            	'is_member_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_member_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);

					          		$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

					          		$actText="Member Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');

					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');

					          		array_push($prdAssignQuestion, $mainKey);
								}
							}
						}
					}

					$delSpouseQue=array_diff_key($defQuestionDel, $spouseQuestion);
					if(!empty($delSpouseQue)){
						foreach ($delSpouseQue as $key => $value) {
							$updQuesParams=array(
									'is_spouse_asked'=> 'N',
									'is_spouse_required'=> 'N'
							);
							$updQuesWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
								'params'=>array(
									":product_id"=>$product_id,
									":prd_question_id"=>$key,
								)
							);
							$pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere);
						}
					}
					if(!empty($spouseQuestion)){
						foreach ($spouseQuestion as $mainKey => $Qvalue) {
							if(!empty($mainKey)){
								if(!empty($prdAssignQuestion) && in_array($mainKey, $prdAssignQuestion)){
									$updQuesParams=array(
										'is_spouse_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_spouse_required'=>!empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_question_id"=>$mainKey,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);
									if(!empty($activity)){
										$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_question_id" => $mainKey,
						            	'is_spouse_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_spouse_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);
					          		$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

					          		$actText="Spouse Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');

					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');
					          		array_push($prdAssignQuestion, $mainKey);
								}
							}
						}
					}

					$delChildQue=array_diff_key($defQuestionDel, $childQuestion);
					if(!empty($delChildQue)){
						foreach ($delChildQue as $key => $value) {
							$updQuesParams=array(
									'is_child_asked'=> 'N',
									'is_child_required'=> 'N'
							);
							$updQuesWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
								'params'=>array(
									":product_id"=>$product_id,
									":prd_question_id"=>$key,
								)
							);
							$pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere);
						}
					}
					if(!empty($childQuestion)){
						foreach ($childQuestion as $mainKey => $Qvalue) {
							if(!empty($mainKey)){
								if(!empty($prdAssignQuestion) && in_array($mainKey, $prdAssignQuestion)){
									$updQuesParams=array(
										'is_child_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_child_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_question_id"=>$mainKey,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);
									if(!empty($activity)){
										$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_question_id" => $mainKey,
						            	'is_child_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_child_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);
					          		$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

					          		$actText="Child Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');

					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');
					          		array_push($prdAssignQuestion, $mainKey);
								}
							}
						}
					}

					$delMemberAgreementCustomQue=array_diff_key($customQuestionDel, $agreementCustomQuestion);
					if(!empty($delMemberAgreementCustomQue)){
						foreach ($delMemberAgreementCustomQue as $key => $value) {
							$updQuesParams=array(
									'is_member_agreement'=> 'N',
							);
							$updQuesWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
								'params'=>array(
									":product_id"=>$product_id,
									":prd_question_id"=>$key,
								)
							);
							$pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere);
						}
					}
					if(!empty($agreementCustomQuestion)){
						foreach ($agreementCustomQuestion as $mainKey => $Qvalue) {
							if(!empty($mainKey)){
								$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

				          		$actText="Member Details ".$resQ['display_label']." Agreement : ".(!empty($Qvalue['agreement']) ? 'Yes' : 'No');

								if(!empty($prdAssignQuestion) && in_array($mainKey, $prdAssignQuestion)){
									$updQuesParams=array(
										'is_member_agreement'=> !empty($Qvalue['agreement']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_question_id"=>$mainKey,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);
									if(!empty($activity)){
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_question_id" => $mainKey,
						            	'is_member_agreement'=> !empty($Qvalue['agreement']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);
					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');
					          		array_push($prdAssignQuestion, $mainKey);
								}
							}
						}
					}

					$delMemberCustomQue=array_diff_key($customQuestionDel, $memberCustomQuestion);
					if(!empty($delMemberCustomQue)){
						foreach ($delMemberCustomQue as $key => $value) {
							$updQuesParams=array(
									'is_member_asked'=> 'N',
									'is_member_required'=> 'N'
							);
							$updQuesWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
								'params'=>array(
									":product_id"=>$product_id,
									":prd_question_id"=>$key,
								)
							);
							$pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere);
						}
					}
					if(!empty($memberCustomQuestion)){
						foreach ($memberCustomQuestion as $mainKey => $Qvalue) {
							if(!empty($mainKey)){
								$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

				          		$actText="Member Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');

								if(!empty($prdAssignQuestion) && in_array($mainKey, $prdAssignQuestion)){
									$updQuesParams=array(
										'is_member_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_member_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_question_id"=>$mainKey,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);
									if(!empty($activity)){
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_question_id" => $mainKey,
						            	'is_member_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_member_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);
					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');
					          		array_push($prdAssignQuestion, $mainKey);
								}
							}
						}
					}

					$delSpouseCustomQue=array_diff_key($customQuestionDel, $spouseCustomQuestion);
					if(!empty($delSpouseCustomQue)){
						foreach ($delSpouseCustomQue as $key => $value) {
							$updQuesParams=array(
									'is_spouse_asked'=> 'N',
									'is_spouse_required'=> 'N'
							);
							$updQuesWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
								'params'=>array(
									":product_id"=>$product_id,
									":prd_question_id"=>$key,
								)
							);
							$pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere);
						}
					}
					if(!empty($spouseCustomQuestion)){
						foreach ($spouseCustomQuestion as $mainKey => $Qvalue) {
							if(!empty($mainKey)){
								$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

				          		$actText="Spouse Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');
								if(!empty($prdAssignQuestion) && in_array($mainKey, $prdAssignQuestion)){
									$updQuesParams=array(
										'is_spouse_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_spouse_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_question_id"=>$mainKey,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);
									if(!empty($activity)){
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ['display_label']);
									}

								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_question_id" => $mainKey,
						            	'is_spouse_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_spouse_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);
					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');
					          		array_push($prdAssignQuestion, $mainKey);
								}
							}
						}
					}

					$delChildCustomQue=array_diff_key($customQuestionDel, $childCustomQuestion);
					if(!empty($delChildCustomQue)){
						foreach ($delChildCustomQue as $key => $value) {
							$updQuesParams=array(
									'is_child_asked'=> 'N',
									'is_child_required'=> 'N'
							);
							$updQuesWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
								'params'=>array(
									":product_id"=>$product_id,
									":prd_question_id"=>$key,
								)
							);
							$pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere);
						}
					}
					if(!empty($childCustomQuestion)){
						foreach ($childCustomQuestion as $mainKey => $Qvalue) {
							if(!empty($mainKey)){
								$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$mainKey));

				          		$actText="Child Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');
								if(!empty($prdAssignQuestion) && in_array($mainKey, $prdAssignQuestion)){
									$updQuesParams=array(
										'is_child_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_child_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_question_id = :prd_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_question_id"=>$mainKey,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);
									if(!empty($activity)){
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_question_id" => $mainKey,
						            	'is_child_asked'=>!empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_child_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);
					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');
					          		array_push($prdAssignQuestion, $mainKey);
								}
							}
						}
					}
				}
			//*************** Member Enrollment Information Code End   ***************
			
			//*************** Beneficiary Information Code Start ***************
				if(($record_type!='Primary' && !in_array("BeneficiaryInformation",$matchGlobal)) || $record_type=='Primary'){

					$updParams=array(
						'is_beneficiary_required'=>$is_beneficiary_required,
					);
					$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$product_id)
					);
					$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
					if(!empty($activity)){
						$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Beneficiary Information',$product_code);
					}

					$sqlBQuestion="SELECT group_concat(prd_beneficiary_question_id) as prd_beneficiary_question_id from prd_beneficiary_questions_assigned where is_deleted='N' AND product_id = :product_id";
					$resBQuestion=$pdo->selectOne($sqlBQuestion,array(":product_id"=>$product_id));

					$sqlBQuestionDel="SELECT peq.questionType,peq.id as prd_beneficiary_question_id FROM prd_beneficiary_questions peq
						JOIN prd_beneficiary_questions_assigned peqa ON (peq.id = peqa.prd_beneficiary_question_id AND peqa.is_deleted='N')
						WHERE peq.is_deleted='N' AND peqa.product_id=:product_id";
					$resBQuestionDel=$pdo->select($sqlBQuestionDel,array(":product_id"=>$product_id));

					$prdBAssignQuestion = array();
					if(!empty($resBQuestion['prd_beneficiary_question_id'])){
						$prdBAssignQuestion = explode(",", $resBQuestion['prd_beneficiary_question_id']);
					}
					$defBQuestionDel=array();
					$customBQuestionDel=array();
					if(!empty($resBQuestionDel)){
						foreach ($resBQuestionDel as $key => $value) {
							if($value['questionType']=='Custom'){
								$customBQuestionDel[$value['prd_beneficiary_question_id']]=$value['prd_beneficiary_question_id'];
							}else{
								$defBQuestionDel[$value['prd_beneficiary_question_id']]=$value['prd_beneficiary_question_id'];
							}
						}
					}
					if($is_beneficiary_required=='Y'){
						$delPrinciQue=array_diff_key($defBQuestionDel, $principalBeneficiary);
						if(!empty($delPrinciQue)){
							foreach ($delPrinciQue as $key => $value) {
								$updQuesParams=array(
										'is_principal_beneficiary_asked'=> 'N',
										'is_principal_beneficiary_required'=> 'N'
								);
								$updQuesWhere=array(
									'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_beneficiary_question_id = :prd_beneficiary_question_id",
									'params'=>array(
										":product_id"=>$product_id,
										":prd_beneficiary_question_id"=>$key,
									)
								);
								$activity =$pdo->update("prd_beneficiary_questions_assigned",$updQuesParams,$updQuesWhere,true);

								if(!empty($activity)){
									$resQ=$pdo->selectOne("SELECT display_label FROM prd_beneficiary_questions where id=:id",array(":id"=>$key));

									$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Beneficiary Information',$product_code,false,$resQ['display_label']);
								}
							}
						}
						if(!empty($principalBeneficiary)){
							foreach ($principalBeneficiary as $mainKey => $Qvalue) {
								$resQ=$pdo->selectOne("SELECT display_label FROM prd_beneficiary_questions where id=:id",array(":id"=>$mainKey));
								$actText="Principal Beneficiary Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');

								if(!empty($prdBAssignQuestion) && in_array($mainKey, $prdBAssignQuestion)){
									$updQuesParams=array(
										'is_principal_beneficiary_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_principal_beneficiary_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_beneficiary_question_id = :prd_beneficiary_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_beneficiary_question_id"=>$mainKey,
										)
									);
									$activity =$pdo->update("prd_beneficiary_questions_assigned",$updQuesParams,$updQuesWhere,true);

									if(!empty($activity)){
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Beneficiary Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_beneficiary_question_id" => $mainKey,
						            	'is_principal_beneficiary_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_principal_beneficiary_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_beneficiary_questions_assigned',$insQuesParams);
					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Beneficiary Information');
					          		array_push($prdBAssignQuestion, $mainKey);
								}
							}
						}

						$delBenefQue=array_diff_key($defBQuestionDel, $contingentBeneficiary);
						if(!empty($delBenefQue)){
							foreach ($delBenefQue as $key => $value) {
								$updQuesParams=array(
										'is_contingent_beneficiary_asked'=> 'N',
										'is_contingent_beneficiary_required'=> 'N'
								);
								$updQuesWhere=array(
									'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_beneficiary_question_id = :prd_beneficiary_question_id",
									'params'=>array(
										":product_id"=>$product_id,
										":prd_beneficiary_question_id"=>$key,
									)
								);
								$activity =$pdo->update("prd_beneficiary_questions_assigned",$updQuesParams,$updQuesWhere,true);

								if(!empty($activity)){
									$resQ=$pdo->selectOne("SELECT display_label FROM prd_beneficiary_questions where id=:id",array(":id"=>$key));

									$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Beneficiary Information',$product_code,false,$resQ['display_label']);
								}
							}
						}
						if(!empty($contingentBeneficiary)){
							foreach ($contingentBeneficiary as $mainKey => $Qvalue) {
								$resQ=$pdo->selectOne("SELECT display_label FROM prd_beneficiary_questions where id=:id",array(":id"=>$mainKey));
								$actText="Contingent Beneficiary Details ".$resQ['display_label']." Asked : ".(!empty($Qvalue['asked']) ? 'Yes' : 'No')." & Required : ".(!empty($Qvalue['required']) ? 'Yes' : 'No');
								if(!empty($prdBAssignQuestion) && in_array($mainKey, $prdBAssignQuestion)){
									$updQuesParams=array(
										'is_contingent_beneficiary_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_contingent_beneficiary_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
									);
									$updQuesWhere=array(
										'clause'=>"is_deleted='N' AND product_id = :product_id AND prd_beneficiary_question_id = :prd_beneficiary_question_id",
										'params'=>array(
											":product_id"=>$product_id,
											":prd_beneficiary_question_id"=>$mainKey,
										)
									);
									$activity =$pdo->update("prd_beneficiary_questions_assigned",$updQuesParams,$updQuesWhere,true);

									if(!empty($activity)){
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Beneficiary Information',$product_code,false,$resQ['display_label']);
									}
								}else{
									$insQuesParams = array(
						            	"product_id" => $product_id,
						            	"prd_beneficiary_question_id" => $mainKey,
						            	'is_contingent_beneficiary_asked'=> !empty($Qvalue['asked']) ? 'Y' : 'N',
										'is_contingent_beneficiary_required'=> !empty($Qvalue['required']) ? 'Y' : 'N',
					          		);
					          		$insQue = $pdo->insert('prd_beneficiary_questions_assigned',$insQuesParams);
					          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Beneficiary Information');
					          		array_push($prdBAssignQuestion, $mainKey);
								}
							}
						}
					}else{
						$updQuesParams=array(
							'is_principal_beneficiary_asked'=> 'N',
							'is_principal_beneficiary_required'=> 'N',
							'is_contingent_beneficiary_asked'=> 'N',
							'is_contingent_beneficiary_required'=> 'N'
						);
						$updQuesWhere=array(
							'clause'=>"product_id=:product_id",
							'params'=>array(
								":product_id"=>$product_id,
							)
						);
						$pdo->update("prd_beneficiary_questions_assigned",$updQuesParams,$updQuesWhere);

						$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Assigned Beneficiary Questions','Admin Updated Product','Beneficiary Information');
					}
				}
			//*************** Beneficiary Information Code End   ***************
			
			//*************** Enrollment Verification Code Start ***************
				if(($record_type!='Primary' && !in_array("EnrollmentVerification",$matchGlobal)) || $record_type=='Primary'){
					$sqlCheckVerification="SELECT verification_type from prd_enrollment_verification where is_deleted='N' AND product_id = :product_id";
					$resCheckVerification=$pdo->select($sqlCheckVerification,array(":product_id"=>$product_id));

					$verificationArray = array();
					if(!empty($resCheckVerification)){
						foreach ($resCheckVerification as $key => $value) {
							array_push($verificationArray, $value['verification_type']);
						}
					}

					$verificationResult=array_diff($verificationArray,$enrollment_verification_array);
					if(!empty($verificationResult)){
						foreach ($verificationResult as $key => $value) {
							$updVerificationParams=array(
								'is_deleted'=>'Y'
							);
							$updVerificationWhere=array(
								'clause'=>'product_id=:product_id and verification_type = :verification_type AND is_deleted="N"',
								'params'=>array(
									":verification_type"=>$value,
									":product_id"=>$product_id
								)
							);
							$pdo->update("prd_enrollment_verification",$updVerificationParams,$updVerificationWhere);	

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Verification Option '.str_replace("_", " ", $value),'Admin Updated Product','Application Verification');				
						}
					}

					$verificationResult=array_diff($enrollment_verification_array,$verificationArray);
					if(!empty($verificationResult)){
						foreach ($verificationResult as $key => $value) {
							$insSubParams=array(
								'product_id'=>$product_id,
								'verification_type'=>$value,
							);
							$pdo->insert('prd_enrollment_verification',$insSubParams);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Verification Option '.str_replace("_", " ", $value),'Admin Updated Product','Application Verification');
						}
					}

					$sqlCheckTerms="SELECT id,terms_condition FROM prd_terms_condition where product_id=:product_id AND is_deleted='N'";
					$resCheckTerms=$pdo->selectOne($sqlCheckTerms,array(":product_id"=>$product_id));
					$oldVaArray = $resCheckTerms;
					if(!empty($resCheckTerms)){
						$updTermsParams=array(
							'terms_condition'=>$termsConditionData
						);
						$updTermsWhere=array(
							'clause'=>'id=:id',
							'params'=>array(
								":id"=>$resCheckTerms['id'],
							)
						);
						$pdo->update("prd_terms_condition",$updTermsParams,$updTermsWhere);

						$NewVaArray = $updTermsParams;
						unset($oldVaArray['id']);

						$activity=array_diff_assoc($oldVaArray,$NewVaArray);
						if(!empty($activity)){
							$sectionName="Application Verification";
							$tmp = array();
							$tmp2 = array();
							$tmp['terms_condition']=base64_encode($activity['terms_condition']);
							$tmp2['terms_condition']=base64_encode($updTermsParams['terms_condition']);

							$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code,true);
						}
					}else{
						$insTermsParams=array(
							'product_id'=>$product_id,
							'terms_condition'=>$termsConditionData,
						);
						$pdo->insert('prd_terms_condition',$insTermsParams);

						$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Terms And Condition','Admin Updated Product','Anrollment Verification');
					}

					$updParams=array(
						'joinder_agreement_require'=>$joinder_agreement_require,
					);
					$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$product_id)
					);
					$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
					if(!empty($activity)){
						$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Application Verification',$product_code);
					}

					if($joinder_agreement_require == "Y"){
						$sqlAgreement="SELECT id,joinder_agreement FROM prd_agreements where product_id=:product_id AND is_deleted='N'";
						$resCheckAgreement=$pdo->selectOne($sqlAgreement,array(":product_id"=>$product_id));
						$oldVaArray = $resCheckAgreement;
						if(!empty($resCheckAgreement)){
							$updAgreementParams=array(
								'joinder_agreement'=>$joinder_agreement
							);
							$updAgreementWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$resCheckAgreement['id'],
								)
							);
							$pdo->update("prd_agreements",$updAgreementParams,$updAgreementWhere);

							$NewVaArray = $updAgreementParams;
							unset($oldVaArray['id']);

							$activity=array_diff_assoc($oldVaArray,$NewVaArray);
							if(!empty($activity)){
								$sectionName="Application Verification";
								$tmp = array();
								$tmp2 = array();
								$tmp['joinder_agreement']=base64_encode($activity['joinder_agreement']);
								$tmp2['joinder_agreement']=base64_encode($updAgreementParams['joinder_agreement']);

								$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$tmp,$tmp2,$sectionName,$product_code,true);
							}
						}else{
							$insAgreementParams=array(
								'product_id'=>$product_id,
								'joinder_agreement'=>$joinder_agreement,
							);
							$pdo->insert('prd_agreements',$insAgreementParams);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Joinder Agreement','Admin Updated Product','Application Verification');
						}
					}else{
						$sqlAgreement="SELECT id,joinder_agreement FROM prd_agreements where product_id=:product_id AND is_deleted='N'";
						$resCheckAgreement=$pdo->selectOne($sqlAgreement,array(":product_id"=>$product_id));

						if(!empty($resCheckAgreement)){
							$updAgreementParams=array(
								'is_deleted'=> 'Y'
							);
							$updAgreementWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$resCheckAgreement['id'],
								)
							);
							$pdo->update("prd_agreements",$updAgreementParams,$updAgreementWhere);
							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Joinder Agreement','Admin Updated Product','Application Verification');
						}
					}
					
					$response['termsConditionData'] = true;
				}
			//*************** Enrollment Verification Code End   ***************
			
			//*************** Agent Requirements Code Start ***************
				if(($record_type!='Primary' && !in_array("AgentRequirements",$matchGlobal)) || $record_type=='Primary'){
					$updParams=array(
						'is_license_require'=>$is_license_require,
						'license_type'=>'',
						'license_rule'=>'',
					);
					if($is_license_require=='Y'){
						$updParams['license_type']=implode(",", $license_type); 
						$updParams['license_rule']=$license_rule; 
					}
					$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$product_id)
					);
					$activity = $pdo->update("prd_main",$updParams,$updWhere,true);

					if(!empty($activity)){
						$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Agent Requirements',$product_code);
					}


					$sqlCheckState="SELECT state_name,sale_type from prd_license_state where is_deleted='N' AND product_id = :product_id";
					$resCheckState=$pdo->select($sqlCheckState,array(":product_id"=>$product_id));
					
					$checkspecificStateArray = array();
					$checkpreSaleStateArray = array();
					$checkjustInTimeStateArray = array();
					
					if(!empty($resCheckState)){
						foreach ($resCheckState as $key => $value) {
							if($value['sale_type']=='Specific'){
								array_push($checkspecificStateArray, $value['state_name']);
							}
							if($value['sale_type']=='Pre-Sale'){
								array_push($checkpreSaleStateArray, $value['state_name']);
							}
							if($value['sale_type']=='Just-In-Time'){
								array_push($checkjustInTimeStateArray, $value['state_name']);
							}
						}
					}
					
					$specificStateResult=array_diff($checkspecificStateArray,$specificStateArray);
					if(!empty($specificStateResult)){
						foreach ($specificStateResult as $key => $value) {
							$updStateParams=array(
								'is_deleted'=>'Y'
							);
							$updStateWhere=array(
								'clause'=>'is_deleted="N" AND product_id = :product_id AND state_name=:state_name AND sale_type="Specific"',
								'params'=>array(
									":product_id"=>$product_id,
									":state_name"=>$value
								)
							);
							$pdo->update("prd_license_state",$updStateParams,$updStateWhere);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Specific State '.$value,'Admin Updated Product','Agent Requirements');				
						}
					}
					$specificStateResult=array_diff($specificStateArray,$checkspecificStateArray);
					if(!empty($specificStateResult)){
						foreach ($specificStateResult as $key => $value) {
							if($license_rule == "Licensed in Specific States Only"){
								$state_id=$allStateResByName[$value]['id'];
								$insStateParams = array(
						            "product_id" => $product_id,
						            "state_id" => $state_id,
						            "state_name" => $value,
						            "license_rule" => $license_rule,
						            "sale_type" => 'Specific',
					          	);
					          	$prd_available_state = $pdo->insert('prd_license_state',$insStateParams);
					          	$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Specific State '.$value,'Admin Updated Product','Agent Requirements');
				          	}
						}
					}

					$preSaleStateResult=array_diff($checkpreSaleStateArray,$preSaleStateArray);
					if(!empty($preSaleStateResult)){
						foreach ($preSaleStateResult as $key => $value) {
							$updStateParams=array(
								'is_deleted'=>'Y'
							);
							$updStateWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND state_name=:state_name AND sale_type='Pre-Sale'",
								'params'=>array(
									":product_id"=>$product_id,
									":state_name"=>$value
								)
							);
							$pdo->update("prd_license_state",$updStateParams,$updStateWhere);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Pre-Sale State '.$value,'Admin Updated Product','Agent Requirements');	


						}
					}
					$preSaleStateResult=array_diff($preSaleStateArray,$checkpreSaleStateArray);
					if(!empty($preSaleStateResult)){
						foreach ($preSaleStateResult as $key => $value) {
							if($license_rule == "Licensed and Appointed"){
								$state_id=$allStateResByName[$value]['id'];
								$insStateParams = array(
						            "product_id" => $product_id,
						            "state_id" => $state_id,
						            "state_name" => $value,
						            "license_rule" => $license_rule,
						            "sale_type" => 'Pre-Sale',
					          	);
					          	$prd_available_state = $pdo->insert('prd_license_state',$insStateParams);
					          	$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Pre-Sale State '.$value,'Admin Updated Product','Agent Requirements');
				          	}
						}
					}

					$justInTimeStateResult=array_diff($checkjustInTimeStateArray,$justInTimeSaleStateArray);
					if(!empty($justInTimeStateResult)){
						foreach ($justInTimeStateResult as $key => $value) {
							$updStateParams=array(
								'is_deleted'=>'Y'
							);
							$updStateWhere=array(
								'clause'=>"is_deleted='N' AND product_id = :product_id AND state_name=:state_name AND sale_type='Just-In-Time'",
								'params'=>array(
									":product_id"=>$product_id,
									":state_name"=>$value
								)
							);
							$pdo->update("prd_license_state",$updStateParams,$updStateWhere);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Just-In-Time State '.$value,'Admin Updated Product','Agent Requirements');
						}
					}
					$justInTimeStateResult=array_diff($justInTimeSaleStateArray,$checkjustInTimeStateArray);
					if(!empty($justInTimeStateResult)){
						foreach ($justInTimeStateResult as $key => $value) {
							if($license_rule == "Licensed and Appointed"){
								$state_id=$allStateResByName[$value]['id'];
								$insStateParams = array(
						            "product_id" => $product_id,
						            "state_id" => $state_id,
						            "state_name" => $value,
						            "license_rule" => $license_rule,
						            "sale_type" => 'Just-In-Time',
					          	);
					          	$prd_available_state = $pdo->insert('prd_license_state',$insStateParams);
					          	$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Just-In-Time State '.$value,'Admin Updated Product','Agent Requirements');
				          	}
						}
					}
				}
			//*************** Agent Requirements Code End   ***************
			$prdMatchGlobal=$functionsList->prdMatchGlobal($product_id,$parent_product_id,$record_type,3);
		}
		if($step >=4 || $prdStep >= 4){
			if(($record_type!='Primary' && !in_array("MemberPaymentSubscriptionType",$matchGlobal)) || $record_type=='Primary'){
				$updParams=array(
					'payment_type'=>$member_payment,
					'payment_type_subscription'=>'',
				);
				if($member_payment=="Recurring"){
					$updParams['payment_type_subscription']=$member_payment_type;
				}
				$updWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$product_id)
				);
				$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
				if(!empty($activity)){
					$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Member Payment Subscription Type',$product_code);
				}
			}
			
			 
			if($exit_with_error=="false"){
				//$updParams['status']='Active';
			}else{
				$updParams=array('status'=>'Pending');
				$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$product_id)
				);
				$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
				if(!empty($activity)){
					$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Status',$product_code);
				}
			}
			
			//*************** Pricing Code Start ***************
				if(($record_type!='Primary' && !in_array("PricingBox",$matchGlobal)) || $record_type=='Primary' || 1){
					$updParams=array(
						'pricing_model'=>$pricing_model,
					);
					$updWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$product_id)
					);
					$activity = $pdo->update("prd_main",$updParams,$updWhere,true);
					if(!empty($activity)){
						$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updParams,'Pricing',$product_code);
					}


					//****** assigned question remove code start ******
						$sqlCheckPricingQue="SELECT prd_pricing_question_id from prd_pricing_question_assigned where is_deleted='N' AND product_id = :product_id AND assign_type is null";
						$resCheckPricingQue=$pdo->select($sqlCheckPricingQue,array(":product_id"=>$product_id));
						$pricingQuestionArray = array();
							
						if(!empty($resCheckPricingQue)){
							foreach ($resCheckPricingQue as $key => $value) {
								array_push($pricingQuestionArray, $value['prd_pricing_question_id']);
							}
						}

						$pricingQueResult=array_diff($pricingQuestionArray,$price_control_array);
						
						if(!empty($pricingQueResult)){
							foreach ($pricingQueResult as $key => $value) {
								
								$updPricingQueParams=array(
									'is_deleted'=>'Y'
								);
								$updPricingQueWhere=array(
									'clause'=>"is_deleted='N' AND product_id = :product_id and prd_pricing_question_id=:prd_pricing_question_id",
									'params'=>array(
										":product_id"=>$product_id,
										":prd_pricing_question_id"=>$value,

									)
								);
								$pdo->update("prd_pricing_question_assigned",$updPricingQueParams,$updPricingQueWhere);

								$resQ=$pdo->selectOne("SELECT display_label FROM prd_pricing_question where id=:id",array(":id"=>$value));

								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Pricing Criteria '.$resQ['display_label'],'Admin Updated Product','Pricing');
								
							}
						}


						$sqlCheckPricingQue="SELECT prd_pricing_question_id,assign_type from prd_pricing_question_assigned where is_deleted='N' AND product_id = :product_id AND assign_type is not null";
						$resCheckPricingQue=$pdo->select($sqlCheckPricingQue,array(":product_id"=>$product_id));
						$pricingQuestionArray = array();
							
						if(!empty($resCheckPricingQue)){
							foreach ($resCheckPricingQue as $key => $value) {
								$pricingQuestionArray[$value['assign_type'].'_'.$value['prd_pricing_question_id']] = $value['prd_pricing_question_id'];
							}
						}

						$pricingQueResult=array_diff_key($pricingQuestionArray,$price_control_enrollee_array);
						
						if(!empty($pricingQueResult)){
							foreach ($pricingQueResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$assign_type = $keyDiff[0];
								$prd_pricing_question_id = $keyDiff[1];

								$updPricingQueParams=array(
									'is_deleted'=>'Y'
								);
								$updPricingQueWhere=array(
									'clause'=>"is_deleted='N' AND product_id = :product_id and assign_type =:assign_type AND prd_pricing_question_id=:prd_pricing_question_id",
									'params'=>array(
										":product_id"=>$product_id,
										":assign_type"=>$assign_type,
										":prd_pricing_question_id"=>$prd_pricing_question_id,

									)
								);
								$pdo->update("prd_pricing_question_assigned",$updPricingQueParams,$updPricingQueWhere);

								$resQ=$pdo->selectOne("SELECT display_label FROM prd_pricing_question where id=:id",array(":id"=>$value));

								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Pricing Criteria '.$resQ['display_label'],'Admin Updated Product','Pricing');
								
							}
						}
					//****** assigned question remove code end   ******

					//****** price delete code start ******
						$productPricing =array();
						if(!empty($pricing_fixed_price)){
							foreach ($pricing_fixed_price as $matrix_group => $value) {
								foreach ($value as $plan_type => $value1) {
									if(in_array($plan_type, $coverage_options_array)){
										array_push($productPricing, $matrix_group."_".$plan_type);
									}
								}
								
							}
						}

						$sqlCheckPricing="SELECT id,matrix_group,plan_type from prd_matrix where is_deleted='N' AND product_id = :product_id AND pricing_model='FixedPrice'";
						$resCheckPricing=$pdo->select($sqlCheckPricing,array(":product_id"=>$product_id));

						$productPricingArray = array();
						if(!empty($resCheckPricing)){
							foreach ($resCheckPricing as $key => $value) {
								array_push($productPricingArray, $value['matrix_group']."_".$value['plan_type']);
							}
						}
						
						$pricingResult=array_diff($productPricingArray,$productPricing);

						if(!empty($pricingResult)){
							foreach ($pricingResult as $key => $value) {
								$resArr = explode("_", $value);
								$matrix_group = $resArr[0];
								$plan_type = $resArr[1];
								$updateStateParams=array(
									'is_deleted'=>'Y'
								);
								$updateStateWhere=array(
									'clause'=>'matrix_group=:matrix_group AND plan_type =:plan_type AND product_id =:product_id',
									'params'=>array(
										":product_id"=>$product_id,
										":matrix_group"=>$matrix_group,
										":plan_type"=>$plan_type,
									)
								);
								$pdo->update("prd_matrix",$updateStateParams,$updateStateWhere);

								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Price For '.$prdPlanTypeArray[$plan_type]['title'],'Admin Updated Product','Pricing');
							}
						}


						$productVariablePricing =array();
						$productVariableEnrolleePricing =array();
						if(!empty($pricingMatrixKey)){
							foreach ($pricingMatrixKey as $matrix_group => $matrixGroupRow) {
								foreach ($matrixGroupRow as $matrixGroupKey => $value) {
									if($pricing_model == "VariablePrice"){
										if(in_array($value['matrixPlanType'], $coverage_options_array) && empty($value['enrolleeMatrix'])){
											array_push($productVariablePricing, $matrix_group."_".$value['matrixPlanType']);
										}else{
											unset($pricingMatrixKey[$matrix_group]);
										}								
									}else if($pricing_model=="VariableEnrollee"){
										if(empty($value['matrixPlanType'])){
											array_push($productVariableEnrolleePricing, $matrix_group."_".$value['enrolleeMatrix']);
										}else{
											unset($pricingMatrixKey[$matrix_group]);
										}
									}
								}
							}
						}

						//***************************
							$sqlCheckPricing="SELECT id,matrix_group,plan_type from prd_matrix where is_deleted='N' AND product_id = :product_id AND pricing_model='VariablePrice'";
							$resCheckPricing=$pdo->select($sqlCheckPricing,array(":product_id"=>$product_id));
							$productVariablePricingArray = array();
							if(!empty($resCheckPricing)){
								foreach ($resCheckPricing as $key => $value) {
									array_push($productVariablePricingArray, $value['matrix_group']."_".$value['plan_type']);
								}
							}
							$variablePricingResult=array_diff($productVariablePricingArray,$productVariablePricing);
							if(!empty($variablePricingResult)){
								foreach ($variablePricingResult as $key => $value) {
									$resArr = explode("_", $value);
									$matrix_group = $resArr[0];
									$plan_type = $resArr[1];
									

									$updateStateParams=array(
										'is_deleted'=>'Y'
									);
									
									$updateStateWhere=array(
										'clause'=>'matrix_group=:matrix_group AND plan_type =:plan_type AND product_id =:product_id',
										'params'=>array(
											":product_id"=>$product_id,
											":matrix_group"=>$matrix_group,
											":plan_type"=>$plan_type,
										)
									);
									$removeFor = $prdPlanTypeArray[$plan_type]['title'];
									
									
									$pdo->update("prd_matrix",$updateStateParams,$updateStateWhere);

									$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Price For '.$removeFor,'Admin Updated Product','Pricing');

									$sqlMatrixDel="SELECT id FROM prd_matrix WHERE matrix_group=:matrix_group AND plan_type =:plan_type AND product_id =:product_id";
									$resMatrixDel=$pdo->selectOne($sqlMatrixDel,array(
										":product_id"=>$product_id,
										":matrix_group"=>$matrix_group,
										":plan_type"=>$plan_type,
									));
									$updateStateParams=array(
										'is_deleted'=>'Y'
									);
									$updateStateWhere=array(
										'clause'=>'prd_matrix_id=:prd_matrix_id',
										'params'=>array(
											":prd_matrix_id"=>$resMatrixDel['id'],
										)
									);
									$pdo->update("prd_matrix_criteria",$updateStateParams,$updateStateWhere);
									
								}
							}
						//***************************

						//***************************
							$sqlCheckPricing="SELECT id,matrix_group,enrollee_type from prd_matrix where is_deleted='N' AND product_id = :product_id AND pricing_model='VariableEnrollee'";
							$resCheckPricing=$pdo->select($sqlCheckPricing,array(":product_id"=>$product_id));
							$productVariableEnrolleePricingArray = array();
							if(!empty($resCheckPricing)){
								foreach ($resCheckPricing as $key => $value) {
									array_push($productVariableEnrolleePricingArray, $value['matrix_group']."_".$value['enrollee_type']);
								}
							}
							$variablePricingResult=array_diff($productVariableEnrolleePricingArray,$productVariableEnrolleePricing);
							if(!empty($variablePricingResult)){
								foreach ($variablePricingResult as $key => $value) {
									$resArr = explode("_", $value);
									$matrix_group = $resArr[0];
									$enrollee_type = $resArr[1];
									

									$updateStateParams=array(
										'is_deleted'=>'Y'
									);
									
									$updateStateWhere=array(
										'clause'=>'matrix_group=:matrix_group AND enrollee_type =:enrollee_type AND product_id =:product_id',
										'params'=>array(
											":product_id"=>$product_id,
											":matrix_group"=>$matrix_group,
											":enrollee_type"=>$enrollee_type,
										)
									);
									$removeFor = $enrollee_type;
									$pdo->update("prd_matrix",$updateStateParams,$updateStateWhere);

									$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Price For '.$removeFor,'Admin Updated Product','Pricing');

									$sqlMatrixDel="SELECT id FROM prd_matrix WHERE matrix_group=:matrix_group AND enrollee_type =:enrollee_type AND product_id =:product_id";
									$resMatrixDel=$pdo->selectOne($sqlMatrixDel,array(
										":product_id"=>$product_id,
										":matrix_group"=>$matrix_group,
										":enrollee_type"=>$enrollee_type,
									));
									$updateStateParams=array(
										'is_deleted'=>'Y'
									);
									$updateStateWhere=array(
										'clause'=>'prd_matrix_id=:prd_matrix_id',
										'params'=>array(
											":prd_matrix_id"=>$resMatrixDel['id'],
										)
									);
									$pdo->update("prd_matrix_criteria",$updateStateParams,$updateStateWhere);
									
								}
							}
						//***************************
					
					//****** price delete code end   ******

					$sqlQuestion="SELECT group_concat(prd_question_id) as prd_question_id from prd_enrollment_questions_assigned where is_deleted='N' AND product_id = :product_id";
					$resQuestion=$pdo->selectOne($sqlQuestion,array(":product_id"=>$product_id));
					
					//****** price insert update code start   ******
						if($pricing_model=="FixedPrice"){
							if(!empty($pricing_fixed_price)){
								foreach ($pricing_fixed_price as $matrix_group => $matrix_group_array) {
									if($matrix_group<0){
										$newMatrixGroup=$functionsList->generateMatrixGroup();
									}else{
										$newMatrixGroup = $matrix_group;
									}

									foreach ($matrix_group_array as $matrix => $matrix_array) {
										$NonCommissionable = str_replace(",","",$matrix_array['NonCommissionable']);
										$Commissionable = str_replace(",","",$matrix_array['Commissionable']);
										$Sale = str_replace(",","",$matrix_array['Retail']);
										$plan_type = $matrix;
										

										$sqlMatrix="SELECT id FROM prd_matrix where product_id=:product_id AND plan_type=:plan_type AND is_deleted='N' AND matrix_group=:matrix_group AND pricing_model='FixedPrice'";
										$whereMatrix=array(":product_id"=>$product_id,":plan_type"=>$plan_type,":matrix_group"=>$matrix_group);
										$resMatrix=$pdo->selectOne($sqlMatrix,$whereMatrix);
										
										
										if(!empty($resMatrix)){
											$matrixUpdParam=array(
												'price'=>$Sale,
												'non_commission_amount'=>$NonCommissionable,
												'commission_amount'=>$Commissionable,
												'payment_type'=>$member_payment,
												'pricing_model'=>$pricing_model,
												'pricing_effective_date'=>(!empty($pricing_effective_date[$matrix_group])) ? date('Y-m-d',strtotime($pricing_effective_date[$matrix_group])) : NULL,
												'pricing_termination_date'=>(!empty($pricing_termination_date[$matrix_group])) ? date('Y-m-d',strtotime($pricing_termination_date[$matrix_group])) : NULL,
												'is_new_price_on_renewal' => !empty($newPricingOnRenewals[$matrix_group]) ? $newPricingOnRenewals[$matrix_group] : 'N',
											);
											$matrixUpdWhere=array(
												'clause'=>'id=:id',
												'params'=>array(":id"=>$resMatrix['id'])
											);
											$activity = $pdo->update("prd_matrix",$matrixUpdParam,$matrixUpdWhere,true);

											if(!empty($activity)){
												if(empty($resMatrix['pricing_termination_date']) && empty($matrixUpdParam['pricing_termination_date'])){
													unset($activity['pricing_termination_date']);
												}
												if(!empty($activity)){
													$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixUpdParam,'Pricing',$product_code,'','For '.$prdPlanTypeArray[$plan_type]['title']);;
												}
											}
										}else{
											if(in_array($plan_type, $coverage_options_array)){
												$matrixIns = array(
													"product_id" => $product_id,
													"plan_type" => $plan_type,
													"price" => $Sale,
													'non_commission_amount'=>$NonCommissionable,
													"commission_amount"=>$Commissionable,
													"payment_type" => $member_payment,
													"pricing_model" => $pricing_model,
													'pricing_effective_date'=>(!empty($pricing_effective_date[$matrix_group])) ? date('Y-m-d',strtotime($pricing_effective_date[$matrix_group])) : NULL,
													'pricing_termination_date'=>(!empty($pricing_termination_date[$matrix_group])) ? date('Y-m-d',strtotime($pricing_termination_date[$matrix_group])) : NULL,
													'is_new_price_on_renewal' => !empty($newPricingOnRenewals[$matrix_group]) ? $newPricingOnRenewals[$matrix_group] : 'N',
													'matrix_group'=>$newMatrixGroup,
												);
												$prdMatID=$pdo->insert("prd_matrix", $matrixIns);
												$prdFixedPriceRes[$matrix_group][$plan_type]=$newMatrixGroup;

												$keys=array_keys($matrixIns);
												$activity=array_fill_keys($keys,'');

												unset($activity['product_id']);
												unset($matrixIns['product_id']);
												unset($activity['matrix_group']);
												unset($matrixIns['matrix_group']);
												
												$matrixIns['plan_type']=$prdPlanTypeArray[$plan_type]['title'];
										
												$activity=array_diff_assoc($activity,$matrixIns);
												
												$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixIns,'Pricing',$product_code);
												
											}
										}
									}
								}
								if(!empty($prdFixedPriceRes)){
									$response['prdFixedPriceRes']=$prdFixedPriceRes;
								}
							}
						}else{
							if($pricing_model == "VariablePrice"){
								if(!empty($price_control_array)){
									foreach ($price_control_array as $key => $value) {
										$sqlCheckPricingQue="SELECT id from prd_pricing_question_assigned where is_deleted='N' AND product_id = :product_id and prd_pricing_question_id=:prd_pricing_question_id AND assign_type is null";
										$resCheckPricingQue=$pdo->selectOne($sqlCheckPricingQue,array(
											":product_id"=>$product_id,
											":prd_pricing_question_id"=>$value,
										));
										$resQ=$pdo->selectOne("SELECT display_label,prd_enrollment_questions_id FROM prd_pricing_question where id=:id",array(":id"=>$value));
										if(empty($resCheckPricingQue)){
											$insPricingQueParams = array(
								            	"product_id" => $product_id,
								            	"prd_pricing_question_id" => $value,
							          		);
							          		$prd_coverage_options = $pdo->insert('prd_pricing_question_assigned',$insPricingQueParams);			      
											$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Pricing Criteria '.$resQ['display_label'],'Admin Updated Product','Pricing');
										}

										if(!empty($resQ['prd_enrollment_questions_id'])){
											$sqlCheckPricingQue="SELECT id from prd_enrollment_questions_assigned
											where is_deleted='N' AND product_id = :product_id and prd_question_id=:prd_question_id";
											$resCheckPricingQue=$pdo->selectOne($sqlCheckPricingQue,array(
												":product_id"=>$product_id,
												":prd_question_id"=>$resQ['prd_enrollment_questions_id'],
											));

											if(!empty($resCheckPricingQue)){
												$updQuesParams=array(
													'is_member_asked'=> 'Y',
													'is_member_required'=> 'Y',
												);
												$updQuesWhere=array(
													'clause'=>"id=:id",
													'params'=>array(
														":id"=>$resCheckPricingQue['id'],
													)
												);
												$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);

												if(!empty($activity)){
													$resQ1=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$resQ['prd_enrollment_questions_id']));

													$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Enrollment Information',$product_code,false,$resQ1['display_label']);
												}
											}else{
												$insQuesParams = array(
									            	"product_id" => $product_id,
									            	"prd_question_id" => $resQ['prd_enrollment_questions_id'],
									            	'is_member_asked'=> 'Y',
													'is_member_required'=> 'Y',
								          		);
								          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);

								          		$resQ1=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$resQ['prd_enrollment_questions_id']));

								          		$actText="Member Details ".$resQ1['display_label']." Asked : Yes & Required : Yes";

								          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');

								          		array_push($prdAssignQuestion, $resQ['prd_enrollment_questions_id']);
											}
										}
										
									}
								}
							}else {
								if(!empty($price_control_enrollee)){
									foreach ($price_control_enrollee as $key => $valueArr) {
										if(!empty($valueArr)){
											foreach ($valueArr as $keyInnr => $value) {
												$sqlCheckPricingQue="SELECT id from prd_pricing_question_assigned where is_deleted='N' AND product_id = :product_id and prd_pricing_question_id=:prd_pricing_question_id AND assign_type=:assign_type";
												$resCheckPricingQue=$pdo->selectOne($sqlCheckPricingQue,array(
													":product_id"=>$product_id,
													":prd_pricing_question_id"=>$value,
													":assign_type"=>$key,
												));

												$resQ=$pdo->selectOne("SELECT display_label,prd_enrollment_questions_id FROM prd_pricing_question where id=:id",array(":id"=>$value));
												if(empty($resCheckPricingQue)){
													$insPricingQueParams = array(
										            	"product_id" => $product_id,
										            	"prd_pricing_question_id" => $value,
										            	"assign_type" => $key,
									          		);
									          		$prd_coverage_options = $pdo->insert('prd_pricing_question_assigned',$insPricingQueParams);			      
													$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Pricing Criteria '.$resQ['display_label'],'Admin Updated Product','Pricing');
													if(!empty($resQ['prd_enrollment_questions_id'])){
														$sqlCheckPricingQue="SELECT id from prd_enrollment_questions_assigned
														where is_deleted='N' AND product_id = :product_id and prd_question_id=:prd_question_id";
														$resCheckPricingQue=$pdo->selectOne($sqlCheckPricingQue,array(
															":product_id"=>$product_id,
															":prd_question_id"=>$resQ['prd_enrollment_questions_id'],
														));

														if(!empty($resCheckPricingQue)){
															if($key=="Primary"){
																$updQuesParams=array(
																	'is_member_asked'=> 'Y',
																	'is_member_required'=> 'Y',
																);
															}else if($key == "Spouse"){
																$updQuesParams=array(
																	'is_spouse_asked'=> 'Y',
																	'is_spouse_required'=> 'Y',
																);
															}else if($key == "Child"){
																$updQuesParams=array(
																	'is_child_asked'=> 'Y',
																	'is_child_required'=> 'Y',
																);
															}
															$updQuesWhere=array(
																'clause'=>"id=:id",
																'params'=>array(
																	":id"=>$resCheckPricingQue['id'],
																)
															);
															$activity = $pdo->update("prd_enrollment_questions_assigned",$updQuesParams,$updQuesWhere,true);

															if(!empty($activity)){
																$resQ1=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$resQ['prd_enrollment_questions_id']));

																$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updQuesParams,'Member Application Information',$product_code,false,$resQ1['display_label']);
															}
														}else{
															$insQuesParams = array(
												            	"product_id" => $product_id,
												            	"prd_question_id" => $resQ['prd_enrollment_questions_id'],
											          		);
											          		if($key=="Primary"){
																$insQuesParams['is_member_asked']='Y';
																$insQuesParams['is_member_required']='Y';
															}else if($key == "Spouse"){
																$insQuesParams['is_spouse_asked']='Y';
																$insQuesParams['is_spouse_required']='Y';
															}else if($key == "Child"){
																$insQuesParams['is_child_asked']='Y';
																$insQuesParams['is_child_required']='Y';
															}
											          		$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);

											          		$resQ1=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$resQ['prd_enrollment_questions_id']));

											          		$actText="Member Details ".$resQ1['display_label']." Asked : Yes & Required : Yes";

											          		$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added '.$actText,'Admin Updated Product','Member Application Information');

											          		array_push($prdAssignQuestion, $resQ['prd_enrollment_questions_id']);
														}
													}
												}
											}
										}
									}
								}

								$sqlVarEnr="SELECT id,child_dependent_rate_calculation,allowed_child,is_primary_eldest,is_rider_for_enrolles,offer_rider_for,rider_product,rider_question from prd_variable_by_enrollee where is_deleted='N' AND product_id = :product_id";
								$resVarEnr=$pdo->selectOne($sqlVarEnr,array(":product_id"=>$product_id));
								$oldVaArray = $resVarEnr;

								$updVarEnrollParams=array(
									'child_dependent_rate_calculation'=>'',
									'allowed_child'=>'',
									'is_primary_eldest'=>$enrollee_primary_age,
									'is_rider_for_enrolles'=>$rider_for_enrollee,
									'offer_rider_for'=>'',
									'rider_product'=>'',
									'rider_question'=>'',
								);
								if(!empty($enrolleeType) && in_array('Child',$enrolleeType)){
									
									$updVarEnrollParams['child_dependent_rate_calculation']=!empty($childRateCalculateType) ? $childRateCalculateType : '';

									if($childRateCalculateType=="Single Rate based on Eldest Child"){
										$updVarEnrollParams['allowed_child']=!empty($singleRateChildrenAllowed) ? $singleRateChildrenAllowed : '';
									}
								}
								
								
								if(!empty($resVarEnr)){
									$updVarEnrollWhr=array(
										'clause'=>'id=:id',
										'params'=>array(
											":id"=>$resVarEnr['id'],
										)
									);
									$pdo->update("prd_variable_by_enrollee",$updVarEnrollParams,$updVarEnrollWhr);

									$NewVaArray = $updVarEnrollParams;
									unset($oldVaArray['id']);

									$activity=array_diff_assoc($oldVaArray,$NewVaArray);

									if(!empty($activity)){
										$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$NewVaArray,'Pricing',$product_code);
									}
								}else{
									$updVarEnrollParams['product_id']=$product_id;
									$pdo->insert('prd_variable_by_enrollee',$updVarEnrollParams);

									$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Banded Setting','Admin Updated Product','Pricing');
								}
							}

							if(!empty($pricingMatrixKey)){
								foreach ($pricingMatrixKey as $matrix_group => $matrix_groupRow) {
									if($matrix_group<0){
										$newMatrixGroup=$functionsList->generateMatrixGroup();
									}else{
										$newMatrixGroup = $matrix_group;
									}
									foreach ($matrix_groupRow as $matrix_group_key => $matrix_array) {

										$NonCommissionable = str_replace(",","",$matrix_array['NonCommissionablePrice']);
										$Commissionable = str_replace(",","",$matrix_array['CommissionablePrice']);
										$Sale = str_replace(",","",$matrix_array['RetailPrice']);
										$enrollee_type = $matrix_array['enrolleeMatrix'];
										$plan_type = $matrix_array['matrixPlanType'];
										$effectiveDate = $matrix_array['pricing_matrix_effective_date'];
										$terminateDate = $matrix_array['pricing_matrix_termination_date'];
										$is_new_price_on_renewal = $matrix_array['newPricingMatrixOnRenewals'];

										if($pricing_model == "VariablePrice"){
											$sqlMatrix="SELECT id,pricing_termination_date FROM prd_matrix where id=:matrix_group_key AND product_id=:product_id AND plan_type=:plan_type AND is_deleted='N' AND matrix_group=:matrix_group AND pricing_model='VariablePrice'";
											$whereMatrix=array(":matrix_group_key"=>$matrix_group_key,":product_id"=>$product_id,":plan_type"=>$plan_type,":matrix_group"=>$matrix_group);
											$resMatrix=$pdo->selectOne($sqlMatrix,$whereMatrix);

											$updatedFor = $prdPlanTypeArray[$plan_type]['title'];
										}else{
											$sqlMatrix="SELECT id,pricing_termination_date FROM prd_matrix where id=:matrix_group_key AND product_id=:product_id AND enrollee_type=:enrollee_type AND is_deleted='N' AND matrix_group=:matrix_group AND pricing_model='VariableEnrollee'";
											$whereMatrix=array(":matrix_group_key"=>$matrix_group_key,":product_id"=>$product_id,":enrollee_type"=>$enrollee_type,":matrix_group"=>$matrix_group);
											$resMatrix=$pdo->selectOne($sqlMatrix,$whereMatrix);

											$updatedFor = $enrollee_type;
										}
											
										if(!empty($resMatrix)){
											$matrixUpdParam=array(
												'price'=>$Sale,
												'non_commission_amount'=>$NonCommissionable,
												'commission_amount'=>$Commissionable,
												'payment_type'=>$member_payment,
												'pricing_model'=>$pricing_model,
												'pricing_effective_date'=>(!empty($effectiveDate)) ? date('Y-m-d',strtotime($effectiveDate)) : NULL,
												'pricing_termination_date'=>(!empty($terminateDate)) ? date('Y-m-d',strtotime($terminateDate)) : NULL,
												'is_new_price_on_renewal' => !empty($is_new_price_on_renewal) ? $is_new_price_on_renewal : 'N',
											);
											$matrixUpdWhere=array(
												'clause'=>'id=:id',
												'params'=>array(":id"=>$resMatrix['id'])
											);
											$activity =$pdo->update("prd_matrix",$matrixUpdParam,$matrixUpdWhere,true);
											if(!empty($activity)){
												if(empty($resMatrix['pricing_termination_date']) && empty($matrixUpdParam['pricing_termination_date'])){
													unset($activity['pricing_termination_date']);
												}
												if(!empty($activity)){
													$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixUpdParam,'Pricing',$product_code,'','For '.$updatedFor);
												}
											}
											$matrixID=$resMatrix['id'];
										}else{
											$matrixIns = array(
												"product_id" => $product_id,
												"plan_type" => $plan_type,
												"enrollee_type" => $enrollee_type,
												"price" => $Sale,
												'non_commission_amount'=>$NonCommissionable,
												"commission_amount"=>$Commissionable,
												"payment_type" => $member_payment,
												"pricing_model" => $pricing_model,
												'pricing_effective_date'=>(!empty($effectiveDate)) ? date('Y-m-d',strtotime($effectiveDate)) : NULL,
												'pricing_termination_date'=>(!empty($terminateDate)) ? date('Y-m-d',strtotime($terminateDate)) : NULL,
												'is_new_price_on_renewal' => !empty($is_new_price_on_renewal) ? $is_new_price_on_renewal : 'N',
												'matrix_group'=>$newMatrixGroup,
											);
											$matrixID=$pdo->insert("prd_matrix", $matrixIns);

											$keys=array_keys($matrixIns);
											$activity=array_fill_keys($keys,'');
											unset($activity['product_id']);
											unset($matrixIns['product_id']);
											unset($activity['matrix_group']);
											unset($matrixIns['matrix_group']);
											$matrixIns['plan_type']= !empty($plan_type) ? $prdPlanTypeArray[$plan_type]['title'] : '';
									
											$activity=array_diff_assoc($activity,$matrixIns);
											$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixIns,'Pricing',$product_code);
											

											$tmpArr=$pricingMatrixKey[$matrix_group][$matrix_group_key];
											unset($pricingMatrixKey[$matrix_group][$matrix_group_key]);
											$pricingMatrixKey[$newMatrixGroup][$matrixID]=$tmpArr;
											$pricingMatrixKey[$newMatrixGroup][$matrixID]['keyID']=$newMatrixGroup;
											$pricingMatrixKey[$newMatrixGroup][$matrixID]['matrix_group']=$newMatrixGroup;
										}

										$sqlMatrixCriteria="SELECT id FROM prd_matrix_criteria where prd_matrix_id=:prd_matrix_id AND is_deleted='N'";
										$whereMatrixCriteria=array(":prd_matrix_id"=>$matrixID);
										$resMatrixCriteria=$pdo->selectOne($sqlMatrixCriteria,$whereMatrixCriteria);

										$matrixUpdParam=array(
											'age_from'=>$matrix_array[1]['age_from'],
											'age_to'=>$matrix_array[1]['age_to'],
											'state'=>$matrix_array[2]['matrix_value'],
											'zipcode'=>$matrix_array[3]['matrix_value'],
											'gender'=>$matrix_array[4]['matrix_value'],
											'smoking_status'=>$matrix_array[5]['matrix_value'],
											'tobacco_status'=>$matrix_array[6]['matrix_value'],
											'height_by'=>$matrix_array[7]['height_by'],
											'height_feet'=>$matrix_array[7]['height_feet'],
											'height_inch'=>$matrix_array[7]['height_inch'],
											'height_feet_to'=>$matrix_array[7]['height_feet_to'],
											'height_inch_to'=>$matrix_array[7]['height_inch_to'],
											'weight_by'=>$matrix_array[8]['weight_by'],
											'weight'=>$matrix_array[8]['weight'],
											'weight_to'=>$matrix_array[8]['weight_to'],
											'no_of_children_by'=>$matrix_array[9]['no_of_children_by'],
											'no_of_children'=>$matrix_array[9]['no_of_children'],
											'no_of_children_to'=>$matrix_array[9]['no_of_children_to'],
											'has_spouse'=>$matrix_array[10]['matrix_value'],
											'spouse_age_from'=>$matrix_array[11]['spouse_age_from'],
											'spouse_age_to'=>$matrix_array[11]['spouse_age_to'],
											'spouse_gender'=>$matrix_array[12]['matrix_value'],
											'spouse_smoking_status'=>$matrix_array[13]['matrix_value'],
											'spouse_tobacco_status'=>$matrix_array[14]['matrix_value'],
											'spouse_height_feet'=>$matrix_array[15]['spouse_height_feet'],
											'spouse_height_inch'=>$matrix_array[15]['spouse_height_inch'],
											'spouse_weight'=>$matrix_array[16]['spouse_weight'],
											'spouse_weight_type'=>$matrix_array[16]['spouse_weight_type'],
											'benefit_amount'=>$matrix_array[17]['matrix_value'],
											'in_patient_benefit'=>$matrix_array[18]['matrix_value'],
											'out_patient_benefit'=>$matrix_array[19]['matrix_value'],
											'monthly_income'=>$matrix_array[20]['matrix_value'],
											// 'benefit_percentage'=>$matrix_array[21]['matrix_value'],
										);

										if(!empty($resMatrixCriteria)){
											
											$matrixUpdWhere=array(
												'clause'=>'id=:id',
												'params'=>array(":id"=>$resMatrixCriteria['id'])
											);
											$activity = $pdo->update("prd_matrix_criteria",$matrixUpdParam,$matrixUpdWhere,true);

											if(!empty($activity)){
												$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixUpdParam,'Pricing',$product_code);
											}

											
										}else{

											$matrixUpdParam['product_id']=$product_id;
											$matrixUpdParam['prd_matrix_id']=$matrixID;

											$pdo->insert("prd_matrix_criteria",$matrixUpdParam);

											$keys=array_keys($matrixUpdParam);
											$activity=array_fill_keys($keys,'');
											unset($matrixUpdParam['product_id']);
											unset($activity['product_id']);
											unset($matrixUpdParam['prd_matrix_id']);
											unset($activity['prd_matrix_id']);
											
											$activity=array_diff_assoc($activity,$matrixUpdParam);
											
											$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixUpdParam,'Pricing',$product_code);
										}

										$feePrdMatrixIdArr[$matrix_group] = $matrixID;
									}
								}
							}
							$response['pricingMatrixKey']=json_encode($pricingMatrixKey);
						}
					//****** price insert update code end   ******

				}

				
			//*************** Pricing Code End   ***************
			
			//*************** Rider Code Start ***************

				if($pricing_model=="VariableEnrollee" && $rider_for_enrollee=='Y'){
					$sqlCheckRider="SELECT id,rider_type from prd_rider_information where is_deleted='N' AND product_id = :product_id";
					$resCheckRider=$pdo->select($sqlCheckRider,array(":product_id"=>$product_id));
					
					$riderDBArr = array();
					if(!empty($resCheckRider)){
						foreach ($resCheckRider as $key => $value) {
							$riderDBArr[$value['id']."_".$value['rider_type']]=$value['id'];
						}
					}
					
					$riderResult=array_diff_key($riderDBArr,$riderArr);

					if(!empty($riderResult)){
						foreach ($riderResult as $key => $value) {
							$keyDiff = explode("_", $key);
							$dbID = $keyDiff[0];
							$dbRiderType = $keyDiff[1];

							$updateZipParams=array(
								'is_deleted'=>'Y'
							);
							$updateZipWhere=array(
								'clause'=>'id=:id AND product_id =:product_id',
								'params'=>array(
									":product_id"=>$product_id,
									":id"=>$dbID,
								)
							);
							$pdo->update("prd_rider_information",$updateZipParams,$updateZipWhere);

							$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Rider Enrollee '.$dbRiderType,'Admin Updated Product','Pricing');
						}
					}
					
					if(!empty($riderDetailArr)){
						$riderInformationArr=array();
						foreach ($riderDetailArr as $key => $value) {

							$sqlRider="SELECT id,rider_rate,rider_product_id,rider_type FROM prd_rider_information where product_id=:product_id AND id=:id AND is_deleted='N'";
							$resRider=$pdo->selectOne($sqlRider,array(":product_id"=>$product_id,":id"=>$key));

							if(!empty($resRider)){
								$updRiderParams=array(
									"rider_rate"=>$value['rider_rate'],
									"rider_product_id"=>$value['rider_product_id'],
									"rider_type"=>$value['rider_type'],
								);
								$updRiderWhere=array(
									'clause'=>'id=:id',
									'params'=>array(":id"=>$key)
								);
								$activity = $pdo->update("prd_rider_information",$updRiderParams,$updRiderWhere,true);

								if(!empty($activity)){

									$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$updRiderParams,'Pricing',$product_code);
								}
							}else{
								
								$ins_params = array(
									"product_id"=>$product_id,
									"rider_rate"=>$value['rider_rate'],
									"rider_product_id"=>$value['rider_product_id'],
									"rider_type"=>$value['rider_type'],
							    );
							    $prd_rider_id = $pdo->insert("prd_rider_information", $ins_params);
							    $riderInformationArr[$key][$value['rider_type']]=$prd_rider_id;
							    $actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Added Rider For '.$value['rider_type'],' Admin Updated Product','Pricing');
								
							}
						}
						$response['riderInformationArr']=$riderInformationArr;
					}
				}else{
					$updateZipParams=array(
						'is_deleted'=>'Y'
					);
					$updateZipWhere=array(
						'clause'=>'product_id =:product_id',
						'params'=>array(
							":product_id"=>$product_id,
						)
					);
					$pdo->update("prd_rider_information",$updateZipParams,$updateZipWhere);

					$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,'Removed Rider','Admin Updated Product','Pricing');
				}

			//*************** Rider Code End   ***************

			//*************** Product Fee Code Start ***************
				if(($record_type!='Primary' && !in_array("ProductFeeBox",$matchGlobal)) || $record_type=='Primary' || 1){
					
					if($product_type == "Group Enrollment") {
						$product_fee_id = getname('prd_fees','AdminFee','id','setting_type');
						if(empty($product_fee_id)){
							$tmpFee = array(
								'display_id'=>'AAF891423',
								'setting_type'=>'AdminFee',
								'name'=>'Admin Fee',
								'status'=>'Active',
							);
							$product_fee_id =$pdo->insert('prd_fees',$tmpFee);
						}
						$product_fee_type = "Admin Fee";
					} else {
						$product_fee_id = getname('prd_fees','Product','id','setting_type');
						if(empty($product_fee_id)){
							$tmpFee = array(
								'display_id'=>'PAF891423',
								'setting_type'=>'Product',
								'name'=>'Product Fee',
								'status'=>'Active',
							);
							$product_fee_id =$pdo->insert('prd_fees',$tmpFee);
						}
						$product_fee_type = "Product Fee";
					}

					//*********** Delete Product Fees ***********
						$sqlPrdFees="SELECT fee_id from prd_assign_fees where is_deleted='N' AND product_id = :product_id AND prd_fee_id = :prd_fee_id";
						$resPrdFees=$pdo->select($sqlPrdFees,array(":product_id"=>$product_id,":prd_fee_id"=>$product_fee_id));

						$prdFeeArray = array();
						if(!empty($resPrdFees)){
							foreach ($resPrdFees as $key => $value) {
								$prdFeeArray[$value['fee_id']] = $value['fee_id'];
							}
						}
						
						$prdFeeResult=array_diff_key($prdFeeArray,$productFees);

						if(!empty($prdFeeResult)){
							foreach ($prdFeeResult as $key => $value) {
								$insParams = array("is_deleted"=>'Y');
								$updWhere=array(
							        'clause'=>'id=:id',
							        'params'=>array(":id"=>$value)
							    );
							    $pdo->update("prd_main",$insParams,$updWhere);

							    $insParams = array("is_deleted"=>'Y');
								$updWhere=array(
							        'clause'=>'product_id=:product_id',
							        'params'=>array(":product_id"=>$value)
							    );
							    $pdo->update("prd_matrix",$insParams,$updWhere);


							    $insParams = array("is_deleted"=>'Y');
								$updWhere=array(
							        'clause'=>'fee_id=:fee_id',
							        'params'=>array(":fee_id"=>$value)
							    );
							    $pdo->update("prd_assign_fees",$insParams,$updWhere);

							    $insParams = array("is_deleted"=>'Y');
							    $updWhere=array(
							        'clause'=>'fee_product_id=:fee_id',
							        'params'=>array(":fee_id"=>$value)
							    );
							    $pdo->update("prd_fee_pricing_model",$insParams,$updWhere);

							    $removedFeeID=getname('prd_main',$value,'product_code','id');
							    $tmp_label = $product_fee_type.' Removed '.$removedFeeID;
							    $actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,$tmp_label,'Admin Updated Product',$product_fee_type);
							}
						}
					//*********** Delete Product Fees ***********

					if(!empty($productFees)){
						foreach ($productFees as $key => $value) {

							$pricingModel = checkIsset($value['pricing_model']);
							$isBenefitTier = checkIsset($value['is_benefit_tier']);
							$isFeeOnRenewal = checkIsset($value['is_fee_on_renewal']);
							$feePrice = checkIsset($value['price']);
							$terminateDate = checkIsset($value['pricing_termination_date']);
							$effectiveDate = checkIsset($value['pricing_effective_date']);
							
							$insParams=array(
							    'type'=>'Fees',
							    'name'=>$value['name'],
							    'product_code'=>$value['product_code'],
							    'product_type'=> ($product_type == "Group Enrollment") ? 'AdminFee' : $value['product_type'],
							    'fee_type'=>$value['fee_type'],
							    'is_benefit_tier'=>$isBenefitTier,
							    'payment_type'=>$value['product_type'] == 'AdminFee' ? 'Recurring' : 'Single',
							    'pricing_model'=>$pricingModel,
							    'admin_id' => $_SESSION['admin']['id'],
							);

							if($groupEnrollmentPrd == 'N'){
								$insParams['initial_purchase']=$value['initial_purchase'];
								$insParams['is_fee_on_renewal']=$isFeeOnRenewal;
								$insParams['fee_renewal_type']=$value['fee_renewal_type'];
								$insParams['fee_renewal_count']= $value['fee_renewal_count'];
								if($value['is_fee_on_renewal']=='Y'){
									$insParams['payment_type']='Recurring';
								}
							}

							if($key > 0){
								$fee_id = $key;
								$updWhere=array(
							        'clause'=>'id=:id',
							        'params'=>array(":id"=>$fee_id)
							    );
							    $activity = $pdo->update("prd_main",$insParams,$updWhere,true);

							    if(!empty($activity)){
							    	$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$insParams,$product_fee_type,$product_code);
							    }
							}else {
								$insParams['prd_fee_id']=$product_fee_id;
								$insParams['status']='Active';
								$fee_id = $pdo->insert("prd_main", $insParams);

								$tmp_label = $product_fee_type.' Added '.$value['product_code'];
								$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,$tmp_label,'Admin Updated Product',$product_fee_type);

								$tmpArr=$productFees[$key];
								unset($productFees[$key]);
								$productFees[$fee_id]=$tmpArr;
								$productFees[$fee_id]['keyID']=$fee_id;
							}

							$feePrice = $value['price'];
							$terminateDate = checkIsset($value['pricing_termination_date']);
							$effectiveDate = checkIsset($value['pricing_effective_date']);

							$sqlCheckFeePricing="SELECT id from prd_matrix where is_deleted='N' AND product_id = :product_id";
							$resCheckFeePricing=$pdo->select($sqlCheckFeePricing,array(":product_id"=>$fee_id));
						

							$productFeePricingArray = array();
							if(!empty($resCheckFeePricing)){
								foreach ($resCheckFeePricing as $key => $value) {
									array_push($productFeePricingArray, $value['id']);
								}
							}

							$feeMatrixIdArr = array();

							if(!empty($feePrice)){
								if($isBenefitTier == "Y"){
									if($pricingModel == "FixedPrice"){
										foreach ($feePrice as $feePriceKey => $feePriceValue) {
											$sqlFeeMatrix="SELECT id FROM prd_matrix where plan_type=:plan_type AND is_deleted='N' AND product_id=:product_id";
										  	$whereFeeMatrix=array(":plan_type"=>$feePriceValue['plan_type'],":product_id"=>$fee_id);
										  	$resFeeMatrix=$pdo->selectOne($sqlFeeMatrix,$whereFeeMatrix);
									  		
									  		if(!empty($resFeeMatrix)){
									  			$feeMatId = $resFeeMatrix['id'];

									    		$matrixUpdParam=array(
									      			'price'=> $feePriceValue['price'],
									      			'price_calculated_on'=>$feePriceValue['price_calculated_on'],
									      			'price_calculated_type'=>$feePriceValue['price_calculated_type'],
									      			'plan_type'=>$feePriceValue['plan_type'],
									      			"pricing_effective_date"=>(!empty($effectiveDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($effectiveDate)) : NULL,
										      		"pricing_termination_date"=>(!empty($terminateDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($terminateDate)) : NULL,
									    		);
									    		$matrixUpdWhere=array(
									      			'clause'=>'id=:id',
									      			'params'=>array(":id"=>$resFeeMatrix['id'])
									    		);
									    		$activity =$pdo->update("prd_matrix",$matrixUpdParam,$matrixUpdWhere,true);
												if(!empty($activity)){
													if(empty($resFeeMatrix['pricing_termination_date']) && empty($matrixUpdParam['pricing_termination_date'])){
														unset($activity['pricing_termination_date']);
													}
													if(!empty($activity)){
														$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixUpdParam,$product_fee_type,$product_code);
													}
												}
									  		}else{
									    		$matrixIns = array(
									    			'pricing_model'=>$insParams['pricing_model'],
									    			'payment_type'=>$insParams['payment_type'],
										    		"product_id" => $fee_id,
										     		'price'=> $feePriceValue['price'],
								      				'price_calculated_on'=>$feePriceValue['price_calculated_on'],
								      				'price_calculated_type'=>$feePriceValue['price_calculated_type'],
								      				'plan_type'=>$feePriceValue['plan_type'],
													"pricing_effective_date"=>(!empty($effectiveDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($effectiveDate)) : NULL,
										      		"pricing_termination_date"=>(!empty($terminateDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($terminateDate)) : NULL,
									   	 		);
									    		$feeMatId = $pdo->insert("prd_matrix", $matrixIns);

									    		$keys=array_keys($matrixIns);
												$activity=array_fill_keys($keys,'');
												unset($activity['product_id']);
												unset($matrixIns['product_id']);
												unset($matrixIns['plan_type']);
												
										
												$activity=array_diff_assoc($activity,$matrixIns);
												$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$matrixIns,$product_fee_type,$product_code);
									  		}
									  		
								  			$selPrd = "SELECT p.id as prdId,pm.id as prdMatId,pm.plan_type FROM prd_main p JOIN prd_matrix pm ON(p.id=pm.product_id AND pm.is_deleted='N') WHERE p.id IN(".$product_id.") AND pm.plan_type=:planType";
											$resPrd = $pdo->select($selPrd,array(":planType" => $feePriceValue['plan_type']));

											if(!empty($resPrd)){
									            foreach ($resPrd as $key => $value) {
									             $assignSql = "SELECT id FROM prd_fee_pricing_model WHERE prd_matrix_id=:prd_matrix_id AND prd_matrix_fee_id=:fee_id AND is_deleted='N'";
									              $assignRow = $pdo->selectOne($assignSql, array(":prd_matrix_id" => $value['prdMatId'],":fee_id" => $feeMatId));

									              if(empty($assignRow['id'])){
									                $insert_params = array(
									                  'product_id' => $value['prdId'],
									                  'prd_matrix_id' => $value['prdMatId'],
									                  'fee_product_id' =>  $fee_id,
									                  'prd_matrix_fee_id' => $feeMatId,
									                );
									                $pdo->insert("prd_fee_pricing_model", $insert_params);
									              }
									            }
									        }
									  		
									  		array_push($feeMatrixIdArr, $feeMatId);
										}
									}else{
										foreach ($feePrice as $feePriceKey => $feePriceValue) {
											$prdMatrixId = $feePrdMatrixIdArr[$feePriceValue['matrix_group']];
											$resPrds = array();
								            $resFeePrd = array();

								            if(!empty($prdMatrixId)){
									            $selPrds = "SELECT p.id as prdId,pm.id as matId,pm.plan_type FROM prd_main p JOIN prd_matrix pm ON(p.id=pm.product_id) WHERE pm.id=:matId AND pm.is_deleted='N'";
									            $resPrds = $pdo->selectOne($selPrds,array(":matId" =>$prdMatrixId));

									            $selFeePrd = "SELECT pfm.fee_product_id as prdId,pfm.prd_matrix_fee_id as matId FROM prd_fee_pricing_model pfm WHERE pfm.prd_matrix_id=:matrixId AND pfm.fee_product_id=:feeId AND pfm.is_deleted='N'";
				 								$resFeePrd = $pdo->selectOne($selFeePrd,array(":matrixId" =>$prdMatrixId,":feeId" =>$fee_id));
								          	}

								          	if(!empty($resPrds)){
										        if(!empty($resFeePrd['prdId'])){
										            $feeMatId = $resFeePrd['matId'];

											        $plan_params = array(
											            'price'=> $feePriceValue['price'],
										      			'price_calculated_on'=>$feePriceValue['price_calculated_on'],
										      			'price_calculated_type'=>$feePriceValue['price_calculated_type'],
														"pricing_effective_date"=>(!empty($effectiveDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($effectiveDate)) : NULL,
											      		"pricing_termination_date"=>(!empty($terminateDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($terminateDate)) : NULL,
											            "is_deleted"=> 'N'
											        );

										            $plan_params['payment_type'] = ($isFeeOnRenewal == "Y" ? "Recurring" : "Single");
										            $plan_params['pricing_model'] = $pricingModel;
										            $update_plan_where = array(
										              'clause' => 'id = :id',
										              'params' => array(
										                ':id' => $feeMatId,
										              )
										            );

										          $activity = $pdo->update("prd_matrix",$plan_params, $update_plan_where,true);
										          	unset($activity['pricing_termination_date']);
										          	if(!empty($activity)){
														$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$plan_params,$product_fee_type,$product_code);
													}
										        }else{
										            $plan_params = array(
										            	'product_id' => $fee_id,
											            'plan_type' => checkIsset($resPrds['plan_type']), 
											            'price'=> $feePriceValue['price'],
										      			'price_calculated_on'=>$feePriceValue['price_calculated_on'],
										      			'price_calculated_type'=>$feePriceValue['price_calculated_type'],
														"pricing_effective_date"=>(!empty($effectiveDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($effectiveDate)) : NULL,
											      		"pricing_termination_date"=>(!empty($terminateDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($terminateDate)) : NULL,
											            "is_deleted"=> 'N'
										            );

										            $plan_params['payment_type'] = ($isFeeOnRenewal == "Y" ? "Recurring" : "Single");
										            $plan_params['pricing_model'] = $pricingModel;
										            $feeMatId = $pdo->insert("prd_matrix", $plan_params);

										            $keys=array_keys($plan_params);
													$activity=array_fill_keys($keys,'');
													unset($activity['product_id']);
													unset($activity['is_deleted']);
													unset($activity['pricing_termination_date']);
													unset($plan_params['product_id']);
													unset($plan_params['plan_type']);
												
										
													$activity=array_diff_assoc($activity,$plan_params);
													$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$plan_params,$product_fee_type,$product_code);

										        }

										        $assignSql = "SELECT id FROM prd_fee_pricing_model where prd_matrix_id=:prd_matrix_id AND prd_matrix_fee_id=:fee_id AND is_deleted='N'";
									          	$assignRow = $pdo->selectOne($assignSql, array(":prd_matrix_id" => $resPrds['matId'],":fee_id" => $feeMatId));
									            
									          	if(empty($assignRow)){
										            $insert_params = array(
										              'product_id' => $resPrds['prdId'],
										              'prd_matrix_id' => $resPrds['matId'],
										              'fee_product_id' =>  $fee_id,
										              'prd_matrix_fee_id' => $feeMatId,
										            );
									            	$pdo->insert("prd_fee_pricing_model", $insert_params);
									          	}
									            array_push($feeMatrixIdArr, $feeMatId);	
									        }
										}
									}
								}else{
									$feePlanSql = "SELECT id,product_id FROM prd_matrix WHERE product_id=:product_id and plan_type = :plan_type AND is_deleted='N'";
									$feePlanRow = $pdo->selectOne($feePlanSql, array(":product_id" => $fee_id,":plan_type" => 0));
								
									foreach ($feePrice as $feePriceKey => $feePriceValue) {
										$plan_params = array(
									      'product_id' => $fee_id,
									      'plan_type' => 0,  
									      'price_calculated_on' => $feePriceValue['price_calculated_on'],  
									      'price_calculated_type' => $feePriceValue['price_calculated_type'],  
									      'pricing_effective_date'=>(!empty($effectiveDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($effectiveDate)) : NULL,
										  'pricing_termination_date'=>(!empty($terminateDate) && ($product_type != "Group Enrollment")) ? date('Y-m-d',strtotime($terminateDate)) : NULL,
									      'price' => $feePriceValue['price'],
									      'is_deleted'=>'N'
									    );

									    $plan_params['payment_type'] = ($isFeeOnRenewal == "Y" ? "Recurring" : "Single");
									    $plan_params['pricing_model'] = "FixedPrice";
									    if(!empty($feePlanRow)){
									    	$feeMatId = $feePlanRow['id'];
									      	$update_plan_where = array(
										        'clause' => 'id = :id and product_id = :product_id',
										        'params' => array(
										            ':id' => $feePlanRow['id'],
									            	':product_id' => $feePlanRow['product_id']
									        	)
									      	);
									      $activity = $pdo->update("prd_matrix",$plan_params, $update_plan_where,true);
										    if(!empty($activity)){
												if(empty($plan_params['pricing_termination_date'])){
													unset($activity['pricing_termination_date']);
												}
												if(!empty($activity)){
													$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$plan_params,$product_fee_type,$product_code);
												}
											}
									    }else{
									      	$feeMatId = $pdo->insert("prd_matrix", $plan_params);
									      	$keys=array_keys($plan_params);
											$activity=array_fill_keys($keys,'');
											unset($activity['product_id']);
											unset($activity['is_deleted']);
											unset($activity['pricing_termination_date']);
											unset($plan_params['product_id']);
											unset($plan_params['plan_type']);
										
								
											$activity=array_diff_assoc($activity,$plan_params);
											$actFeed=$functionsList->prdUpdActtivityFeed($product_id,$parent_product_id,$activity,$plan_params,$product_fee_type,$product_code);

									    }
									    array_push($feeMatrixIdArr, $feeMatId);
									}
								}
							}
							
							$feePricingResult=array_diff($productFeePricingArray,$feeMatrixIdArr);

							if(!empty($feePricingResult)){
								foreach ($feePricingResult as $key => $value) {
									$updateStateParams=array(
										'is_deleted'=>'Y'
									);
									$updateStateWhere=array(
										'clause'=>'id=:id AND product_id =:product_id',
										'params'=>array(
											":product_id"=>$fee_id,
											":id"=>$value,
										)
									);
									$pdo->update("prd_matrix",$updateStateParams,$updateStateWhere);
									$tmp_label = 'Removed '.$product_fee_type.' Price';
									$actFeed=$functionsList->prdActtivityFeed($product_id,$parent_product_id,$product_code,$tmp_label,'Admin Updated Product',$product_fee_type);
								}
							}
							if($isBenefitTier != 'Y'){
						       $plan_fee_params = array(
						          'is_deleted' => 'Y',
						        );
						        $update_plan_fee_where = array(
						          'clause' => 'is_deleted="N" AND fee_product_id = :product_id',
						          'params' => array(':product_id' => $fee_id)
						        );
						        $pdo->update("prd_fee_pricing_model",$plan_fee_params, $update_plan_fee_where);
						    }else{
							    $feePricingSql = "SELECT id FROM prd_fee_pricing_model WHERE fee_product_id=:product_id AND is_deleted='N'";
							    $feePricingRes = $pdo->selectOne($feePricingSql, array(":product_id" => $fee_id));
								$fee_incr = '';
						      	if(!empty($feePricingRes) && !empty($feeMatrixIdArr)){
							        $fee_incr =' AND prd_matrix_fee_id NOT IN('.implode(",", $feeMatrixIdArr).')';
							        
						      
							        $plan_fee_params = array(
							          'is_deleted' => 'Y',
							        );
							        $update_plan_fee_where = array(
							          'clause' => 'is_deleted="N" AND fee_product_id = :product_id '.$fee_incr,
							          'params' => array(':product_id' => $fee_id)
							        );
						        	$pdo->update("prd_fee_pricing_model",$plan_fee_params, $update_plan_fee_where);
						      	}
						    }
							
							$sqlAssignFee="SELECT id from prd_assign_fees where product_id = :product_id AND fee_id=:fee_id AND is_deleted='N'";
							$resAssignFee=$pdo->selectOne($sqlAssignFee,array(":product_id"=>$product_id,":fee_id"=>$fee_id));

							if(empty($resAssignFee)){
								$insFeeParams = array(
						            "product_id" => $product_id,
						            "fee_id" => $fee_id,
						            "prd_fee_id" => !empty($product_fee_id) ? $product_fee_id : 0,
					          	);
					          	$prd_assign_fees = $pdo->insert('prd_assign_fees',$insFeeParams);
							}
						}
					}
					$response['productFees']=json_encode($productFees);
				}
		//*************** Product Fee Code End   ***************
			
			$prdMatchGlobal=$functionsList->prdMatchGlobal($product_id,$parent_product_id,$record_type,4);
		}

		//*************** Cache Code Start   ***************
		if($SITE_ENV == 'Local'){
			$product_detail_info = displayAgentPortalDescriptionInfo($product_id,'N');
			$productCacheMainArray[$product_id]['name'] = $product_name;
			$productCacheMainArray[$product_id]['description'] = $product_detail_info."</br>".$agent_portal;
			
			$prd_cached = fopen($CACHE_PATH_DIR.$PRODUCT_CACHE_FILE_NAME, 'w');
			fwrite($prd_cached, json_encode($productCacheMainArray));
			fclose($prd_cached);
		}else{
			$redisCache->getOrGenerateCache('All','Product','add');
		}
		//*************** Cache Code End   ***************
		
		setNotifySuccess('Product Added Successfully');

		$response['product_id']=md5($product_id);
		$response['parent_product_id']=$record_type=='Variation' ? md5($parent_product_id) : 0;
		$response['manage_product_id']=md5($product_id);
		$response['is_clone']='N';
		$response['status']="success";
	} else {
		$response['status'] = "fail";
		$response['errors'] = $validate->getErrors();
		$response['div_step_error'] = $div_step_error;
	}
	header('Content-Type: application/json');
	echo json_encode($response);
	dbConnectionClose();
	exit;
?>