<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(17);
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Product List';
$breadcrumbes[1]['link'] = 'manage_product.php';
$breadcrumbes[2]['title'] = 'Add Product';
$page_title = "Add New Product";

$todayDate=date('Y-m-d');
$stateUncheckDate=date('m/d/Y');
$productActiveEffectiveDate=date('m/d/Y');

$summernote=true;
$parent_product_id=!empty($_GET['parentProduct']) ? $_GET['parentProduct'] : 0;
$product_id=!empty($_GET['product']) ? $_GET['product'] : 0;
$is_clone=!empty($_GET['is_clone']) ? $_GET['is_clone'] : 'N';
$record_type=!empty($_GET['type']) ? $_GET['type'] : '';
$manage_product_id = !empty($product_id) ? $product_id : $parent_product_id;

if(!empty($parent_product_id)){
	$record_type = "Variation";
}
$defaultPlanCode=array("GC","PC","PC");
$plan_code_counter=0;

$membership_ids = array();
$available_state = array();
$availableCheckAll = array();

$alreadyAddedStateName = array();

$specificState = array();
$specificStateCheckAll = array();

$specificZipCodeArr = array();

$preSaleState = array();
$preSaleCheckAll = array();

$justInTimeSaleState = array();
$justInTimeSaleCheckAll = array();

$resCheckMaxAgeTerm = array();
$questionList = array();
$FixedPriceArr=array();
$subProductList=array();
$productCombinationRulesArr = array();
$match_globals = array();

$dataStep = 0;
$checkStep = 1;
$pricingMatrixKey = '';
$productFees = '';
$initialBuild = false;
$pricing_model = '';

$excludeProduct = array();
$autoAssignProduct = array();
$requiredProduct = array();
$packagedProduct = array();

$enrolleeTypeArr = array("Primary","Spouse","Child");
$enrolleeType = array();

$riderInfoArr = array();
$tmpClonedMatrixParams = array();

if(!empty($product_id) || !empty($parent_product_id)){
	$sqlProduct="SELECT id,company_id,name,product_code,category_id,carrier_id,type,product_type,dataStep,parent_product_id,record_type,status,
	direct_product,effective_day,effective_day2,sold_day,membership_ids,is_specific_zipcode,no_sale_state_coverage_continue,family_plan_rule,termination_rule,term_back_to_effective,term_automatically,term_automatically_within,
	term_automatically_within_type,reinstate_option,reinstate_within,reinstate_within_type,reenroll_options,reenroll_within,reenroll_within_type,is_primary_age_restrictions,primary_age_restrictions_from,
	primary_age_restrictions_to,is_spouse_age_restrictions,spouse_age_restrictions_from,spouse_age_restrictions_to,is_children_age_restrictions,children_age_restrictions_from,children_age_restrictions_to,
	maxAgeAutoTermed,allowedBeyoundAge,is_beneficiary_required,is_license_require,license_type,license_rule,
	payment_type,payment_type_subscription,pricing_model,main_product_type,is_life_insurance_product,life_term_type,guarantee_issue_amount_type,primary_issue_amount,spouse_issue_amount,child_issue_amount,is_spouse_issue_amount_larger,is_short_term_disablity_product,monthly_benefit_allowed,percentage_of_salary,deduction,is_gap_plus_product,annual_hrm_payment,is_require_out_of_pocket_maximum,is_benefit_amount_limit,minimum_benefit_amount,maximum_benefit_amount,is_set_default_out_of_pocket_maximum,default_out_of_pocket_maximum,gap_home_savings_recommend_text,gap_custom_recommendation_text,joinder_agreement_require
	FROM prd_main WHERE md5(id)=:id AND is_deleted='N'";
	$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$manage_product_id));

	if(!empty($resProduct)){
		$dataStep = $resProduct['dataStep'];
		$checkStep = $dataStep + 1;

		//********** step1 varible intialization code start **********************
			$product_name = $resProduct['name'];
			$product_code = $resProduct['product_code'];
			$product_type = $resProduct['product_type'];
			$company_id = $resProduct['company_id'];
			$category_id = $resProduct['category_id'];
			$carrier_id = $resProduct['carrier_id'];

			$sqlProductCode="SELECT id,code_no,plan_code_value FROM prd_plan_code where md5(product_id)=:product_id and is_deleted='N' ORDER BY code_no ASC";
			$resProductCode=$pdo->select($sqlProductCode,array(":product_id"=>$manage_product_id));

			$sqlMatchGlobal= "SELECT group_concat(match_globals) as match_globals from prd_match_globals WHERE is_deleted='N' AND md5(product_id) = :product_id";
			$resMatchGlobal= $pdo->selectOne($sqlMatchGlobal,array(":product_id"=>$manage_product_id));
			
			$match_globals = !empty($resMatchGlobal['match_globals']) ? explode(",",$resMatchGlobal['match_globals']): array();			

			$sqlDepartment="SELECT id,name,description FROM prd_member_portal_information where md5(product_id) = :product_id AND is_deleted='N'";
			$resDepartment=$pdo->select($sqlDepartment,array(":product_id"=>$manage_product_id));

			if(empty($resDepartment)){
				$initialBuild = true;
			}

			$sqlDesc = "SELECT enrollment_desc,agent_portal,agent_info,limitations_exclusions FROM prd_descriptions where md5(product_id) = :product_id";
			$resDesc = $pdo->selectOne($sqlDesc,array(":product_id"=>$manage_product_id));

			if(!empty($resDesc)){
				$enrollmentPage = $resDesc['enrollment_desc'];
				$agent_portal = $resDesc['agent_portal'];
				$agent_info = !empty($resDesc['agent_info']) ? explode(",", $resDesc['agent_info']) : '';
				$limitations_exclusions = $resDesc['limitations_exclusions'];

			}

			$main_product_type = $resProduct['main_product_type'];
			$is_life_insurance_product = $resProduct['is_life_insurance_product'];
			$life_term_type = $resProduct['life_term_type'];
			$guarantee_issue_amount_type = !empty($resProduct['guarantee_issue_amount_type']) ? explode(",", $resProduct['guarantee_issue_amount_type']) : array();
			$primary_issue_amount = $resProduct['primary_issue_amount'];
			$spouse_issue_amount = $resProduct['spouse_issue_amount'];
			$child_issue_amount = $resProduct['child_issue_amount'];
			$is_spouse_issue_amount_larger = $resProduct['is_spouse_issue_amount_larger'];
			$is_short_term_disablity_product = $resProduct['is_short_term_disablity_product'];
			$monthly_benefit_allowed = $resProduct['monthly_benefit_allowed'];
			$percentage_of_salary = $resProduct['percentage_of_salary'];
			$deduction = $resProduct['deduction'];
			$is_gap_plus_product = $resProduct['is_gap_plus_product'];
			$annual_hrm_payment = (!empty($resProduct['annual_hrm_payment'])?json_decode($resProduct['annual_hrm_payment'],true):array());
			$is_require_out_of_pocket_maximum = $resProduct['is_require_out_of_pocket_maximum'];
			$is_benefit_amount_limit = $resProduct['is_benefit_amount_limit'];
			$minimum_benefit_amount = $resProduct['minimum_benefit_amount'];
			$maximum_benefit_amount = $resProduct['maximum_benefit_amount'];
			$is_set_default_out_of_pocket_maximum = $resProduct['is_set_default_out_of_pocket_maximum'];
			$default_out_of_pocket_maximum = $resProduct['default_out_of_pocket_maximum'];
			$gap_home_savings_recommend_text = $resProduct['gap_home_savings_recommend_text'];
			if($gap_home_savings_recommend_text == "custom_recommendation") {
				$gap_custom_recommendation_text = $resProduct['gap_custom_recommendation_text'];
			} else {
				$gap_custom_recommendation_text = "";
			}
		//********** step1 varible intialization code end   **********************
		
		//********** step2 varible intialization code start **********************
			$direct_product = $resProduct['direct_product'];
			$effective_day = $resProduct['effective_day'];
			$effective_day2 = $resProduct['effective_day2'];
			$sold_day = $resProduct['sold_day'];

			$membership_ids = !empty($resProduct['membership_ids']) ? explode(",",$resProduct['membership_ids']) : array();

			$is_membership_require ='N';
			if(!empty($membership_ids)){
				$is_membership_require = 'Y';
			}

			$sqlAvailState="SELECT group_concat(pas.state_name) as available_state 
			FROM prd_available_state pas
			WHERE md5(pas.product_id) = :product_id AND pas.is_deleted='N'";
			$resAvailState = $pdo->selectOne($sqlAvailState,array(":product_id"=>$manage_product_id));



			if(!empty($resAvailState['available_state'])){
				$available_state = !empty($resAvailState['available_state']) ? explode(",", $resAvailState['available_state']) : array(); 
			}

			
			$sqlAvailableNoSaleState="SELECT pn.*
							FROM prd_no_sale_states pn 
							WHERE md5(pn.product_id) = :product_id AND pn.is_deleted='N'";
			$resAvailableNoSaleState=$pdo->select($sqlAvailableNoSaleState,array(":product_id"=>$manage_product_id));

			$resAvailableNoSaleStateArray=array();
			$displayNoSaleState=array();
			if(!empty($resAvailableNoSaleState)){
				foreach ($resAvailableNoSaleState as $key => $value) {
			 		$today_date = date('Y-m-d');
                	$effectiveDate = !empty($value['effective_date']) ? date('Y-m-d',strtotime($value['effective_date'])) : '';
	                $terminationDate = !empty($value['termination_date']) ? date('Y-m-d',strtotime($value['termination_date'])) : '';
	                $termReadonly = "";
	                $effectiveReadonly = "";
	                if(!empty($terminationDate) && (
	                    strtotime($terminationDate) < strtotime($effectiveDate) ||
	                    strtotime($terminationDate) < strtotime($today_date))
	                ){
	                    array_push($available_state, $value['state_name']);
	                }else{
	                	if (($key = array_search($value['state_name'], $available_state)) !== false) {
						    unset($available_state[$key]);
						}
	                }
					$resAvailableNoSaleStateArray[$value['state_id']][]=$value;
					
					if(!in_array($value['state_name'], $available_state)){
						array_push($displayNoSaleState, $value['state_name']);
					}
				}
			}
			$resAvailableNoSaleState=$resAvailableNoSaleStateArray;
			
			
			

			$is_specific_zipcode = $resProduct['is_specific_zipcode'];

			$sqlSpecificZipCode="SELECT psz.*
							FROM prd_specific_zipcode psz 
							WHERE md5(psz.product_id) = :product_id AND psz.is_deleted='N'";
			$resSpecificZipCode=$pdo->select($sqlSpecificZipCode,array(":product_id"=>$manage_product_id));

			
			if(!empty($resSpecificZipCode)){
			    foreach ($resSpecificZipCode as $key => $row) {
			        if (!array_key_exists($row['state_name'], $specificZipCodeArr)) {
			            $specificZipCodeArr[$row['state_name']] = array();
			        }
			        array_push($specificZipCodeArr[$row['state_name']],$row['zipcode']);
			    }
				
			}
			
			$no_sale_state_coverage_continue = $resProduct['no_sale_state_coverage_continue'];

			$sqlCoverage="SELECT pco.prd_plan_type_id
							FROM prd_coverage_options pco 
							WHERE md5(pco.product_id) = :product_id AND pco.is_deleted='N'";
			$resCoverage=$pdo->select($sqlCoverage,array(":product_id"=>$manage_product_id));

			$coverage_options = array();
			if(!empty($resCoverage)){
			    foreach ($resCoverage as $key => $row) {
			        array_push($coverage_options,$row['prd_plan_type_id']);
			    }
				
			}

			$family_plan_rule = $resProduct['family_plan_rule'];

			$prdSubProductsSql="SELECT group_concat(sub_product_id) as sub_product_id FROM prd_sub_products where is_deleted='N' AND md5(product_id) = :product_id";
			$prdSubProducts=$pdo->selectOne($prdSubProductsSql,array(":product_id"=>$manage_product_id));

			if(!empty($prdSubProducts['sub_product_id'])){
				$subProductList = explode(",", $prdSubProducts['sub_product_id']);
			}
			
			$sqlCombination="SELECT combination_type,combination_product_id from prd_combination_rule where is_deleted='N' AND md5(product_id) = :product_id";
			$resCombination=$pdo->select($sqlCombination,array(":product_id"=>$manage_product_id));

			
			if(!empty($resCombination)){
				foreach ($resCombination as $key => $value) {
					if($value['combination_type']=='Excludes'){
						array_push($excludeProduct, $value['combination_product_id']);
					}
					if($value['combination_type']=='Required'){
						array_push($requiredProduct, $value['combination_product_id']);
					}
					if($value['combination_type']=='Auto Assign'){
						array_push($autoAssignProduct, $value['combination_product_id']);
					}
					if($value['combination_type']=='Packaged'){
						array_push($packagedProduct, $value['combination_product_id']);
					}
					
				}
			}

			$productCombinationRulesArr= array_merge($excludeProduct,$autoAssignProduct,$requiredProduct,$packagedProduct);

			$termination_rule = $resProduct['termination_rule'];
			
			$term_back_to_effective = $resProduct['term_back_to_effective'];
			
			$term_automatically = $resProduct['term_automatically'];
			$term_automatically_within = $resProduct['term_automatically_within'];
			$term_automatically_within_type = $resProduct['term_automatically_within_type'];

			$reinstate_option = $resProduct['reinstate_option'];
			$reinstate_within = $resProduct['reinstate_within'];
			$reinstate_within_type = $resProduct['reinstate_within_type'];

			$reenroll_options = $resProduct['reenroll_options'];
			$reenroll_within = $resProduct['reenroll_within'];
			$reenroll_within_type = $resProduct['reenroll_within_type'];
		//********** step2 varible intialization code end   **********************
		
		//********** step3 varible intialization code start **********************
			$is_primary_age_restrictions = $resProduct['is_primary_age_restrictions'];
			$primary_age_restrictions_from = $resProduct['primary_age_restrictions_from'];
			$primary_age_restrictions_to = $resProduct['primary_age_restrictions_to'];

			$is_spouse_age_restrictions = $resProduct['is_spouse_age_restrictions'];
			$spouse_age_restrictions_from = $resProduct['spouse_age_restrictions_from'];
			$spouse_age_restrictions_to = $resProduct['spouse_age_restrictions_to'];

			$is_children_age_restrictions = $resProduct['is_children_age_restrictions'];
			$children_age_restrictions_from = $resProduct['children_age_restrictions_from'];
			$children_age_restrictions_to = $resProduct['children_age_restrictions_to'];

			$maxAgeAutoTermed = $resProduct['maxAgeAutoTermed'];
			$allowedBeyoundAge = $resProduct['allowedBeyoundAge'];

			$sqlCheckMaxAgeTerm="SELECT * from prd_max_age_terminaion where is_deleted='N' AND md5(product_id) = :product_id";
			$resCheckMaxAgeTerm=$pdo->selectGroup($sqlCheckMaxAgeTerm,array(":product_id"=>$manage_product_id),'member_type');

			$sqlCheckBeyondAge="SELECT * from prd_beyond_age_disablity where is_deleted='N' AND md5(product_id) = :product_id";
			$resCheckBeyondAge=$pdo->selectGroup($sqlCheckBeyondAge,array(":product_id"=>$manage_product_id),'member_type');


			$sqlQuestion="SELECT * from prd_enrollment_questions_assigned where is_deleted='N' AND md5(product_id) = :product_id";
			$resQuestion=$pdo->select($sqlQuestion,array(
				":product_id"=>$manage_product_id,
			));

			$questionList = array();
			foreach ($resQuestion as $key => $value) {
				$questionList[$value['prd_question_id']]=$value;
			}
			
			$is_beneficiary_required = $resProduct['is_beneficiary_required'];

			$sqlBeneficiaryQuestion="SELECT * from prd_beneficiary_questions_assigned where is_deleted='N' AND md5(product_id) = :product_id";
			$resBeneficiaryQuestion=$pdo->select($sqlBeneficiaryQuestion,array(
				":product_id"=>$manage_product_id,
			));

			$beneficiaryQuestionList = array();
			foreach ($resBeneficiaryQuestion as $key => $value) {
				$beneficiaryQuestionList[$value['prd_beneficiary_question_id']]=$value;
			}

			$sqlCheckVerification="SELECT id,verification_type from prd_enrollment_verification where is_deleted='N' AND md5(product_id) = :product_id";
			$resCheckVerification=$pdo->select($sqlCheckVerification,array(
				":product_id"=>$manage_product_id,
			));

			$enrollment_verification = array();
			foreach ($resCheckVerification as $key => $value) {
				array_push($enrollment_verification,$value['verification_type']);
			}
			
			$sqlCheckTerms="SELECT terms_condition FROM prd_terms_condition where is_deleted='N' AND md5(product_id) = :product_id";
			$resCheckTerms=$pdo->selectOne($sqlCheckTerms,array(":product_id"=>$manage_product_id));
			
			$joinder_agreement_require = $resProduct['joinder_agreement_require'];
			$sqlJoinderAgreement="SELECT joinder_agreement FROM prd_agreements where is_deleted='N' AND md5(product_id) = :product_id";
			$resJoinderAgreement=$pdo->selectOne($sqlJoinderAgreement,array(":product_id"=>$manage_product_id));


			$is_license_require = $resProduct['is_license_require'];
			$license_type = !empty($resProduct['license_type']) ? explode(",",$resProduct['license_type']) : array();
			$license_rule = $resProduct['license_rule'];
			
			$sqlCheckState="SELECT * from prd_license_state where is_deleted='N' AND md5(product_id) = :product_id";
			$resCheckState=$pdo->select($sqlCheckState,array(":product_id"=>$manage_product_id));

			$licenseStateArray = array();
			foreach ($resCheckState as $key => $row) {
				if (!array_key_exists($row['sale_type'], $licenseStateArray)) {
			            $licenseStateArray[$row['sale_type']] = array();
		        }
		        array_push($licenseStateArray[$row['sale_type']],$row['state_name']);
				
			}
		//********** step3 varible intialization code end   **********************
		
		//********** step4 varible intialization code start **********************
			$member_payment = $resProduct['payment_type'];
			$member_payment_type = $resProduct['payment_type_subscription'];
			$pricing_model = $resProduct['pricing_model'];



			$sqlVarEnrollee="SELECT * from prd_variable_by_enrollee where is_deleted='N' AND md5(product_id)=:product_id";
			$resVarEnrollee=$pdo->selectOne($sqlVarEnrollee,array(":product_id"=>$manage_product_id));

			if(!empty($resVarEnrollee)){
				$childRateCalculateType=$resVarEnrollee['child_dependent_rate_calculation'];
				$singleRateChildrenAllowed=$resVarEnrollee['allowed_child'];
				$enrollee_primary_age=$resVarEnrollee['is_primary_eldest'];
				$rider_for_enrollee=$resVarEnrollee['is_rider_for_enrolles'];
				$enrolleeRiderType=!empty($resVarEnrollee['offer_rider_for']) ? explode(",",$resVarEnrollee['offer_rider_for']) : array();
				$enrolleeRiderProduct=$resVarEnrollee['rider_product'];
				$enrolleeRiderProductQue=$resVarEnrollee['rider_question'];
			}


			$sqlRiderInfo="SELECT * from prd_rider_information where is_deleted='N' AND md5(product_id)=:product_id";
			$resRiderInfo=$pdo->select($sqlRiderInfo,array(":product_id"=>$manage_product_id));
			

			if(!empty($resRiderInfo)){
				foreach ($resRiderInfo as $key => $value) {
					$riderInfoArr[$value['rider_type']][$value['id']]['id']=$value['id'];
					$riderInfoArr[$value['rider_type']][$value['id']]['rider_rate']=$value['rider_rate'];
					$riderInfoArr[$value['rider_type']][$value['id']]['rider_product_id']=$value['rider_product_id'];
					$riderInfoArr[$value['rider_type']][$value['id']]['rider_type']=$value['rider_type'];
				}
			}



			$sqlCheckPricingQue="SELECT assign_type,prd_pricing_question_id from prd_pricing_question_assigned where is_deleted='N' AND md5(product_id)=:product_id";
			$resCheckPricingQue=$pdo->select($sqlCheckPricingQue,array(":product_id"=>$manage_product_id));
			$price_control = array();
			$price_control_enrollee = array();
			$price_controlCSVMatrix = array();
			$enrolleeType = array();

			if(!empty($resCheckPricingQue)){
				foreach ($resCheckPricingQue as $key => $value) {
					if($pricing_model=="VariablePrice"){
						array_push($price_control, $value['prd_pricing_question_id']);
					}else{
						if(!array_key_exists($value['assign_type'], $price_control_enrollee)){
							$price_control_enrollee[$value['assign_type']]=array();
						}
						array_push($price_control_enrollee[$value['assign_type']], $value['prd_pricing_question_id']);

						
					}
					if(!in_array($value['assign_type'],$enrolleeType)){
						array_push($enrolleeType, $value['assign_type']);
					}

					if(!in_array($value['prd_pricing_question_id'], $price_controlCSVMatrix)){
						array_push($price_controlCSVMatrix, $value['prd_pricing_question_id']);

					}
				}
			}

			if($pricing_model=="FixedPrice"){

				$priceSql = "SELECT pm.id,pm.plan_type,pm.price,pm.non_commission_amount,pm.commission_amount,pm.payment_type,pm.pricing_model,pm.pricing_effective_date,pm.pricing_termination_date,pm.is_new_price_on_renewal,pm.matrix_group
				         FROM prd_matrix pm
				         WHERE pm.is_deleted='N' and md5(pm.product_id)=:product_id ORDER BY id ASC";
				$priceRes = $pdo->select($priceSql,array(":product_id"=>$manage_product_id));

				if(!empty($priceRes)){
					foreach ($priceRes as $key => $value) {
						$tmpDate1 = !empty($value['pricing_effective_date']) ? date($DATE_FORMAT,strtotime($value['pricing_effective_date'])) : '';
						$tmpDate2 = !empty($value['pricing_termination_date']) ? date($DATE_FORMAT,strtotime($value['pricing_termination_date'])) : '';
						
						$FixedPriceArr[$value['matrix_group']]['pricing_effective_date'] = $tmpDate1;
						$FixedPriceArr[$value['matrix_group']]['pricing_termination_date'] = $tmpDate2;
						$FixedPriceArr[$value['matrix_group']]['is_new_price_on_renewal'] = $value['is_new_price_on_renewal'];
						$FixedPriceArr[$value['matrix_group']][$value['plan_type']] = $value;

						$tmpClonedMatrixParams[$value['matrix_group']] = $value['matrix_group'];
					}

				}
				if((empty($product_id) && !empty($parent_product_id)) || $is_clone=='Y'){
					$tmpKey='';

					if(!empty($FixedPriceArr)){
						foreach ($FixedPriceArr as $key => $value) {
							if(empty($tmpKey)){
								$tmpKey = $key;
							}else{
								$dt1 = $FixedPriceArr[$tmpKey]['pricing_effective_date'];
								$dt2 = $value['pricing_effective_date'];

								if(strtotime($dt1) < strtotime($dt2)){
									unset($FixedPriceArr[$tmpKey]);
									$tmpKey = $key;
								}
							}
						}
					}
				
					$FixedPriceArr[$tmpKey]['pricing_effective_date'] = date($DATE_FORMAT,strtotime('+1 day'));
					$FixedPriceArr[$tmpKey]['pricing_termination_date'] = '';
				}
			}else{
				$priceSql = "SELECT pm.product_id,pm.enrollee_type,pm.plan_type,pm.price,pm.non_commission_amount,pm.commission_amount,pm.payment_type,pm.pricing_model,pm.pricing_effective_date,pm.pricing_termination_date,pm.is_new_price_on_renewal,pm.matrix_group,pmc.*
				         FROM prd_matrix pm
				         JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.is_deleted='N')
				         WHERE pm.is_deleted='N' and md5(pm.product_id)=:product_id";
				$priceRes = $pdo->select($priceSql,array(":product_id"=>$manage_product_id));
				
				$clonedMatrix = "-".rand(1,1000);
				if(!empty($priceRes)){
					foreach ($priceRes as $key => $rows) {
						if($manage_product_id == $product_id && $is_clone=='N'){
							$matrix_group = $rows['matrix_group'];
						}else{
							$matrix_group = $clonedMatrix.$rows['matrix_group'];
						}
						$tmpClonedMatrixParams[$rows['matrix_group']] = $matrix_group;
						
						$tmpDate1 = !empty($rows['pricing_effective_date']) ? date($DATE_FORMAT,strtotime($rows['pricing_effective_date'])) : '';
						$tmpDate2 = !empty($rows['pricing_termination_date']) ? date($DATE_FORMAT,strtotime($rows['pricing_termination_date'])) : '';
						
						if(!isset($priceResArr[$matrix_group])){
							$rows['is_new_price_on_renewal'] = '';
						}
						$priceResArr[$matrix_group][$rows['prd_matrix_id']] = array(
							'keyID'=>$matrix_group,
							'matrix_group'=>$matrix_group,
							'id'=>$rows['prd_matrix_id'],
							'matrixPlanType'=>$rows['plan_type'],
							'enrolleeMatrix'=>$rows['enrollee_type'],
							'1'=>array("matrix_value"=>(isset($rows['age_from']) && isset($rows['age_to'])) ?$rows['age_from']." To ".$rows['age_to'] : '',
										"age_from"=>$rows['age_from'],
										"age_to"=>$rows['age_to']
									),
							'2'=>array("matrix_value"=>$rows['state']),
							'3'=>array("matrix_value"=>$rows['zipcode']),
							'4'=>array("matrix_value"=>$rows['gender']),
							'5'=>array("matrix_value"=>$rows['smoking_status']),
							'6'=>array("matrix_value"=>$rows['tobacco_status']),
							'7'=>array("matrix_value"=>$rows['height_feet']."Ft ".$rows['height_inch']."In" .($rows['height_by']=="Range" ? " To ".$rows['height_feet_to']."Ft ".$rows['height_inch_to']."In" : ''),
										"height_by"=>$rows['height_by'],
										"height_feet"=>$rows['height_feet'],
										"height_inch"=>$rows['height_inch'],
										"height_feet_to"=>$rows['height_feet_to'],
										"height_inch_to"=>$rows['height_inch_to'],
									),
							'8'=>array("matrix_value"=>$rows['weight'].($rows['weight_by']=="Range" ? " To ".$rows['weight_to'] : ''),
										"weight_by"=>$rows['weight_by'],
										"weight"=>$rows['weight'],
										"weight_to"=>$rows['weight_to'],
									),
							'9'=>array(
								"matrix_value"=>$rows['no_of_children'] .($rows['no_of_children_by']=="Range" ? " To ".$rows['no_of_children_to'] : ''),
								"no_of_children_by"=>$rows['no_of_children_by'],
								"no_of_children"=>$rows['no_of_children'],
								"no_of_children_to"=>$rows['no_of_children_to'],
							),
							'10'=>array("matrix_value"=>$rows['has_spouse']),
							'11'=>array("matrix_value"=>$rows['spouse_age_from']." To ".$rows['spouse_age_to'],
										"spouse_age_from"=>$rows['spouse_age_from'],
										"spouse_age_to"=>$rows['spouse_age_to']
									),
							'12'=>array("matrix_value"=>$rows['spouse_gender']),
							'13'=>array("matrix_value"=>$rows['spouse_smoking_status']),
							'14'=>array("matrix_value"=>$rows['spouse_tobacco_status']),
							'15'=>array("matrix_value"=>$rows['spouse_height_feet']."Ft ".$rows['spouse_height_inch']."In",
										"spouse_height_feet"=>$rows['spouse_height_feet'],
										"spouse_height_inch"=>$rows['spouse_height_inch']
									),
							'16'=>array("matrix_value"=>$rows['spouse_weight']." ".$rows['spouse_weight_type'],
										"spouse_weight"=>$rows['spouse_weight'],
										"spouse_weight_type"=>$rows['spouse_weight_type']
									),
							'17'=>array("matrix_value"=>$rows['benefit_amount']),
							'18'=>array("matrix_value"=>$rows['in_patient_benefit']),
							'19'=>array("matrix_value"=>$rows['out_patient_benefit']),
							'20'=>array("matrix_value"=>$rows['monthly_income']),
							// '21'=>array("matrix_value"=>$rows['benefit_percentage']),
							'RetailPrice'=>$rows['price'],
							'NonCommissionablePrice'=>$rows['non_commission_amount'],
							'CommissionablePrice'=>$rows['commission_amount'],
							'pricing_matrix_effective_date'=>$tmpDate1,
							'pricing_matrix_termination_date'=>$tmpDate2,
							'newPricingMatrixOnRenewals'=>!empty($rows['is_new_price_on_renewal']) ? $rows['is_new_price_on_renewal'] : '',
						);
					}

					if((empty($product_id) && !empty($parent_product_id)) || $is_clone=='Y'){
						

						if(!empty($priceResArr)){
							foreach ($priceResArr as $matrixGroup => $matrixGroupRow) {
								$tmpKey1='';
								$tmpKey2='';
								foreach ($matrixGroupRow as $key => $value) {
									if(empty($tmpKey2)){
										$tmpKey1 = $matrixGroup;
										$tmpKey2 = $key;
									}else{
										$dt1 = $priceResArr[$tmpKey1][$tmpKey2]['pricing_matrix_effective_date'];
										$dt2 = $value['pricing_matrix_effective_date'];

										if(strtotime($dt1) < strtotime($dt2)){
											unset($priceResArr[$tmpKey1][$tmpKey2]);
											$tmpKey1 = $matrixGroup;
											$tmpKey2 = $key;
										}
									}
								}
							}
						}
						
						foreach ($priceResArr as $matrixGroup => $matrixGroupRow) {
								foreach ($matrixGroupRow as $key => $value) {
									$priceResArr[$matrixGroup][$key]['pricing_matrix_effective_date'] = date($DATE_FORMAT,strtotime('+1 day'));
									$priceResArr[$matrixGroup][$key]['pricing_matrix_termination_date'] = '';
									$priceResArr[$matrixGroup][$key]['newPricingMatrixOnRenewals'] = '';
								}
						}
						
					}
					$pricingMatrixKey = json_encode($priceResArr);
				}
			}
			
			$productFeeSql="SELECT pf.id as fee_id,pf.name,pf.product_code,pf.product_type,pf.fee_type,pf.initial_purchase,pf.is_fee_on_renewal,pf.fee_renewal_type,pf.fee_renewal_count,pf.payment_type,pf.pricing_model,
				pm.pricing_effective_date,pm.pricing_termination_date,pf.status,pf.is_benefit_tier,
				pm.price,pm.plan_type,pm.price_calculated_on,pm.price_calculated_type,pm.id as matrixID,pm.product_id,prdMat.matrix_group 
				FROM prd_assign_fees paf
				JOIN prd_main pf ON (paf.fee_id = pf.id AND pf.product_type IN('Product','AdminFee') AND pf.is_deleted='N')
				JOIN prd_matrix pm ON (pf.id = pm.product_id AND pm.is_deleted='N')
				LEFT JOIN prd_fee_pricing_model pfm ON(paf.fee_id=pfm.fee_product_id AND pm.id=pfm.prd_matrix_fee_id AND pfm.is_deleted='N')
				LEFT JOIN prd_matrix prdMat ON(prdMat.id=pfm.prd_matrix_id AND prdMat.is_deleted='N')
				WHERE md5(paf.product_id) = :product_id AND paf.is_deleted='N'";
			$productFeeRes=$pdo->select($productFeeSql,array(":product_id"=>$manage_product_id));
		
			$productFeeArr = array();
			$clonedFeeID="-".rand(1,1000);
			if(!empty($productFeeRes)){
				foreach ($productFeeRes as $key => $rows) {
					if($manage_product_id == $product_id && $is_clone=='N'){
						$fee_id = $rows['fee_id'];
					}else{
						$fee_id = $clonedFeeID.$rows['fee_id'];
						$rows['product_code'] = get_product_fee_id();
					}
					if(!isset($productFeeArr[$fee_id])){
						$productFeeArr[$fee_id]=array(
							'keyID'=>$fee_id,
							'id'=>$fee_id,
							'name' => $rows['name'],
						    'product_code' => $rows['product_code'],
						    'product_type' => $rows['product_type'],
						    'type' => 'Fees',
					    	'fee_type' => $rows['fee_type'],
						    'pricing_effective_date'=>!empty($rows['pricing_effective_date']) ? date($DATE_FORMAT,strtotime($rows['pricing_effective_date'])) : '',
							'pricing_termination_date'=>!empty($rows['pricing_termination_date']) ? date($DATE_FORMAT,strtotime($rows['pricing_termination_date'])) : '',
						    'initial_purchase' => $rows['initial_purchase'],
						    'is_fee_on_renewal' => $rows['is_fee_on_renewal'],
						    'fee_renewal_type' => $rows['fee_renewal_type'],
						    'fee_renewal_count' => $rows['fee_renewal_count'],
						    'is_benefit_tier' => $rows['is_benefit_tier'],
						    'pricing_model' => $rows['pricing_model'],
						    'status' => $rows['status'],
						    'total_products'=>1,
						    'prd_price'=>'',
						);
					}
					if (!array_key_exists('price', $productFeeArr[$fee_id])) {
			            $productFeeArr[$fee_id]['price'] = array();
			        }
			        if (!array_key_exists($rows['matrixID'], $productFeeArr[$fee_id]['price'])) {
			            $productFeeArr[$fee_id]['price'][$rows['matrixID']] = array();
			        }
			        
					if(empty($productFeeArr[$fee_id]['prd_price']) || $rows['price'] < $productFeeArr[$fee_id]['prd_price']){
			          $productFeeArr[$fee_id]['prd_price'] = $rows['price'];
			          $productFeeArr[$fee_id]['price_calculated_on'] = $rows['price_calculated_on'];
			        }
					$plan_params = array(
			          'product_id' => $rows['product_id'],
			          'plan_type' => $rows['plan_type'],  
			          'price_calculated_on' => $rows['price_calculated_on'],  
			          'price_calculated_type' => $rows['price_calculated_type'],  
			          'price' => $rows['price'],
			          'is_deleted'=> 'N',
			          'matrix_group'=> checkNumberset($tmpClonedMatrixParams[$rows['matrix_group']]),
			        );
					$productFeeArr[$fee_id]['price'][$rows['matrixID']] = $plan_params;
				}
				$productFees =json_encode($productFeeArr);
			}

		//********** step4 varible intialization code end   **********************
		
	}else{
		redirect($ADMIN_HOST.'/manage_product.php');
	}
}else{
	$initialBuild = true;
}


if(empty($product_id) || $is_clone =='Y'){
	$product_id = 0;
	$product_code=$functionsList->generateProductCode();
	$i=0;
	if(!empty($resProductCode)){
		foreach ($resProductCode as $key => $tmpRow) {
			$resProductCode[$key]['id'] = $i;
			$i++;
		}
	}
	
}

if(!empty($product_id)){
	$actFeed=$functionsList->prdActtivityFeed($resProduct['id'],$resProduct['parent_product_id'],$product_code,'Read Product','Viewed Product');	
}


$company_sql = "SELECT id,company_name FROM prd_company WHERE is_deleted='N' order by company_name ASC";
$company_res = $pdo->select($company_sql);

$sql = "SELECT * FROM prd_category where status='Active' AND is_deleted='N' order by title ASC";
$categories = $pdo->select($sql);

$prdFeesSql = "SELECT setting_type,id,name,display_id FROM prd_fees where status='Active' AND is_deleted='N' AND setting_type in ('Carrier','membership')";
$prdFeesRes = $pdo->select($prdFeesSql,array());

$prdFeeArray = array();
if(!empty($prdFeesRes)){
	foreach ($prdFeesRes as $key => $value) {
		$prdFeeArray[$value['setting_type']][$value['id']]['id']=$value['id'];
		$prdFeeArray[$value['setting_type']][$value['id']]['name']=$value['name'];
		$prdFeeArray[$value['setting_type']][$value['id']]['product_code']=$value['display_id'];
		
	}
}

if(empty($available_state)){
	$available_state = array_keys($allStateShortName);
}


$subProductSql="SELECT sp.id,sp.product_code,sp.product_name,pf.name 
					FROM sub_products sp 
					JOIN prd_fees pf ON (pf.id=sp.carrier_id AND pf.setting_type='Carrier')
					where sp.status='Active' AND sp.is_deleted='N' order by sp.id asc";
$subProductsArray=$pdo->selectGroup($subProductSql,array(),'name');


$temp_incr = "";
$temp_params = array();
if(!empty($product_id)){
	$temp_incr .= " AND md5(p.id) != :product_id";
	$temp_params[':product_id'] = $product_id;
}
$productSql = "SELECT p.id,p.name,p.product_code,pc.title as category_name
	  FROM prd_main p
	  JOIN prd_category pc ON (pc.id=p.category_id)
	  WHERE p.is_deleted='N' AND p.name !='' AND p.type!='Fees' AND p.parent_product_id = 0 $temp_incr
	  GROUP BY p.id order by p.name ASC";
$productArray=$pdo->selectGroup($productSql,$temp_params,'category_name');


$productSql = "SELECT p.id,p.name,p.product_code,pc.title as category_name
	  FROM prd_main p
	  JOIN prd_category pc ON (pc.id=p.category_id)
	  WHERE p.is_deleted='N' AND p.name !='' AND p.type!='Fees'AND p.pricing_model='FixedPrice' AND p.parent_product_id = 0 $temp_incr
	  GROUP BY p.id order by p.name ASC";
$productArray2=$pdo->selectGroup($productSql,$temp_params,'category_name');



$triggerSql="SELECT id,concat(id,' - ',title) as display_id FROM triggers where is_deleted='N'";
$triggerRes=$pdo->select($triggerSql);

$productAddOnSql = "SELECT p.id,p.name,p.product_code,pc.title as category_name
	  FROM prd_main p
	  JOIN prd_category pc ON (pc.id=p.category_id)
	  WHERE p.is_deleted='N' AND p.name !='' AND p.product_type= 'Add On Only Product'
	  GROUP BY p.id order by p.name ASC";
$productAddOnArray=$pdo->selectGroup($productAddOnSql,array(),'category_name');

$allowPricingUpdate = true;

if(($is_clone !='Y' && !empty($product_id)) || !empty($parent_product_id)){
	$sqlCheckVariation = "SELECT id FROM prd_main where md5(parent_product_id) = :id AND is_deleted='N'";
	$resCheckVerification = $pdo->selectOne($sqlCheckVariation,array(":id"=>$manage_product_id));

	$sqlFeeAssigned = "SELECT id FROM prd_assign_fees WHERE md5(product_id)=:id AND is_deleted='N'";
	$resFeeAssigned = $pdo->selectOne($sqlFeeAssigned,array(":id"=>$manage_product_id));

	if(!empty($resCheckVerification) || !empty($resFeeAssigned) || !empty($parent_product_id)){
		$allowPricingUpdate = false;
	}
}
$allowGapPlusUpdate = false;
$gapPlusReadonly = "";
if(($is_clone !='Y' && !empty($product_id)) || !empty($parent_product_id)){
	$sqlCheckActive = "SELECT p.id 
						FROM prd_main p
						JOIN website_subscriptions w ON(w.product_id = p.id) 
						WHERE md5(p.id) = :id AND p.is_deleted='N' AND p.status='Active'";
	$resCheckActive = $pdo->selectOne($sqlCheckActive,array(":id"=>$manage_product_id));
	if(!empty($resCheckActive)){
		$allowGapPlusUpdate = true;
		$gapPlusReadonly = "readonly";
	}
}




$exStylesheets = array(
	'thirdparty/dropzone/css/basic.css',
	'thirdparty/multiple-select-master/multiple-select.css',
	'thirdparty/bootstrap-tagsinput-master/bootstrap-tagsinput.css',
	'thirdparty/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css',
	'thirdparty/select2/css/select2.css',
	'thirdparty/summernote-master/dist/summernote.css'
);
$exJs = array(
	"thirdparty/ajax_form/jquery.form.js",
	'thirdparty/multiple-select-master/jquery.multiple.select.js',
	'thirdparty/bootstrap-tagsinput-master/bootstrap-tagsinput.js',
	'thirdparty/bootstrap-switch/js/bootstrap-switch.min.js',
	'thirdparty/price_format/jquery.price_format.2.0.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/select2/js/select2.full.min.js',
	"thirdparty/ajaxProgressTimer/js/jquery.progresstimer.js",
	"thirdparty/jquery-match-height/js/jquery.matchHeight.js",
	"thirdparty/ckeditor/ckeditor.js",
	'thirdparty/summernote-master/dist/popper.js', 
	'thirdparty/summernote-master/dist/summernote.js'
);

$template = "product_builder.inc.php";
include_once 'layout/end.inc.php';
?>