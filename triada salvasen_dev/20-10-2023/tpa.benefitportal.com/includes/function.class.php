<?php
include_once dirname(__DIR__) . "/includes/notification_function.php";

require dirname(__DIR__) . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class functionsList{
	public function getCommissionRuleId($product_id,$sponsor_id='',$parent = 1){
	    global $pdo;
	    $res = array();
	    $incr = "";
	    $ruleId = 0;

	    $sch_params = array();
	    $sch_params[':product_id'] = $product_id;
	    

	    if(!empty($sponsor_id)){
	      $agentCommRuleSql = "SELECT c.id FROM commission_rule c
	                      JOIN agent_commission_rule r ON (r.commission_rule_id=c.id)
	                      WHERE r.agent_id=:sponsor_id AND r.product_id=:product_id 
	                      AND c.status='Active' AND c.is_deleted='N' 
	                      AND r.is_deleted='N'
	                      ORDER BY r.id DESC LIMIT 1";
	      $sch_params[':sponsor_id'] = $sponsor_id;
	      $res = $pdo->selectOne($agentCommRuleSql,$sch_params);
	    }

	    if(empty($res)){
	    	unset($sch_params[':sponsor_id']);
	      	if($parent == 0){
	          $incr = " AND parent_product_id=:parent_product_id";
	          $sch_params[":parent_product_id"] = 0;
	      	}

	      
	      	$sql = "SELECT id FROM commission_rule 
	      	WHERE product_id=:product_id AND parent_rule_id=0 AND is_deleted='N' AND status='Active' $incr";
	      	$res = $pdo->selectOne($sql, $sch_params);
	    }
	    
	    if(!empty($res)){
	      $ruleId = $res['id'];
	    }else if($sponsor_id == 1){
	      $parentProductId = $this->getParentProductId($product_id);
	      if ($parentProductId > 0) {
	        $ruleId = $this->getCommissionRuleId($parentProductId,$sponsor_id,0);
	      } else {
	        $ruleId = 0;
	      }
	    }
	    return $ruleId;
  	}

  	public function getParentProductId($product_id){
  		global $pdo;
		$getParent_sql = "SELECT parent_product_id FROM prd_main where id=:id";
		$whereParent_sql = array(":id" => $product_id);
		$res_Parent = $pdo->selectOne($getParent_sql, $whereParent_sql);
		$parentProductId = $res_Parent['parent_product_id'];
		return $parentProductId;
  	}

  	public function addCustomerSettings($params,$customer_id){
  		global $pdo;

  		$checkRes=$pdo->selectOne("SELECT id FROM customer_settings WHERE customer_id=:id",array(":id"=>$customer_id));

  		if(!empty($checkRes)){
  			$id=$checkRes['id'];
  			$upd_where = array(
				'clause' => 'customer_id = :id',
				'params' => array(
					':id' => $customer_id,
				),
			);
			$pdo->update('customer_settings', $params, $upd_where);
  		}else{
  			$params['customer_id']=$customer_id;
  			$id=$pdo->insert('customer_settings',$params);
  		}
  		return $id;
  	}

  	public function assignCommissionRuleToAgent($agentId, $productId, $commissionRuleIds = array()) {
		global $pdo;
		
		if (!is_array($commissionRuleIds)) {
			$commissionRuleIds = array($commissionRuleIds);		
		} else {
			$commissionRuleIds = $commissionRuleIds;
		}
		
		foreach ($commissionRuleIds as $commissionRuleId) {
			//check rule is exists for user or not
			if(!empty($commissionRuleId)){
				$checkCommissionExists = $pdo->selectOne("SELECT id,is_deleted FROM agent_commission_rule WHERE agent_id=:agent_id AND product_id=:product_id AND commission_rule_id=:commission_rule_id AND is_deleted='N'", array(":product_id" => $productId, ":agent_id" => $agentId, ":commission_rule_id" => $commissionRuleId));
				if (!$checkCommissionExists) {
					$commission_rule_params = array(
						"agent_id" => $agentId,
						"product_id" => $productId,
						"commission_rule_id" => $commissionRuleId,
						"created_at" => "msqlfunc_NOW()",
						"admin_id" => isset($_SESSION["admin"]["id"]) ? $_SESSION["admin"]["id"] : 0,
					);
					$pdo->insert("agent_commission_rule", $commission_rule_params);
				}
			}
			
		}
		//remove rule which is not exists when we add
		$getAllRules = $pdo->select("SELECT commission_rule_id,id FROM agent_commission_rule WHERE agent_id=:agent_id AND product_id=:product_id", array(":product_id" => $productId, ":agent_id" => $agentId));
		if (count($getAllRules) > 0) {
			foreach ($getAllRules as $rule) {
				//check commission id is exists on our else remove it
				if (!in_array($rule["commission_rule_id"], $commissionRuleIds)) {
					$updateSql = array("is_deleted" => 'Y');
					$updateWhere = array("clause" => "id=:id", "params" => array(":id" => $rule['id']));
					$pdo->update("agent_commission_rule", $updateSql, $updateWhere);
				}
			}
		}
	}

	public function generateCommissionDisplayID() {
		global $pdo;
		$rule_code = rand(100000, 999999);
		
		$sql = "SELECT count(id) as total FROM commission_rule WHERE rule_code ='C" . $rule_code . "' OR rule_code ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateCommissionDisplayID();
		} else {
			return 'C'.$rule_code;
		}
	}


	public function generateProviderDisplayID() {
		global $pdo;
		$rule_code = rand(1000, 9999);
		
		$sql = "SELECT count(id) as total FROM providers WHERE display_id ='PP" . $rule_code . "' OR display_id ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateProviderDisplayID();
		} else {
			return 'PP'.$rule_code;
		}
	}

	public function generateBroadcasterDisplayID() {
		global $pdo;
		$rule_code = rand(1000, 9999);
		
		$sql = "SELECT count(id) as total FROM broadcaster WHERE display_id ='B" . $rule_code . "' OR display_id ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateBroadcasterDisplayID();
		} else {
			return 'B'.$rule_code;
		}
	}
	public function generateResourceDisplayID() {
		global $pdo;
		$rule_code = rand(1000, 9999);
		
		$sql = "SELECT count(id) as total FROM resources WHERE display_id ='RR" . $rule_code . "' OR display_id ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateResourceDisplayID();
		} else {
			return 'RR'.$rule_code;
		}
	}
	
	public function generateProductCode() {
		global $pdo;
		$product_code = rand(100000, 999999);
		
		$sql = "SELECT count(id) as total FROM prd_main WHERE product_code ='P" . $product_code . "' OR product_code ='" . $product_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateProductCode();
		} else {
			return 'P'.$product_code;
		}
	}

	public function addOrdinalNumberSuffix($num) {
	    if (!in_array(($num % 100),array(11,12,13))){
	      switch ($num % 10) {
	        // Handle 1st, 2nd, 3rd
	        case 1:  return $num.'st';
	        case 2:  return $num.'nd';
	        case 3:  return $num.'rd';
	      }
	    }
    	return $num.'th';
	}

	public function generateMatrixGroup() {
		global $pdo;
		$sql = "SELECT matrix_group FROM prd_matrix where matrix_group > 0 order by id desc";
		$res = $pdo->selectOne($sql);
		if (!empty($res)) {
			return $res['matrix_group']+1;
		} else {
			return 1;
		}
	}

	public function prdMatchGlobal($product_id,$parent_product_id,$record_type,$step){
		global $pdo,$allStateRes,$prdPlanTypeArray;
		if($record_type=="Variation"){
			$variation_id = $product_id;
			$product_id = $parent_product_id;
		}
		$sqlProduct="SELECT id,company_id,name,product_code,category_id,carrier_id,type,product_type,dataStep,parent_product_id,record_type,status,direct_product,effective_day,sold_day,membership_ids,is_specific_zipcode,no_sale_state_coverage_continue,family_plan_rule,
			termination_rule,term_back_to_effective,term_automatically,term_automatically_within,
			term_automatically_within_type,reinstate_option,reinstate_within,reinstate_within_type,reenroll_options,reenroll_within,reenroll_within_type,is_primary_age_restrictions,primary_age_restrictions_from,primary_age_restrictions_to,is_spouse_age_restrictions,spouse_age_restrictions_from,spouse_age_restrictions_to,is_children_age_restrictions,children_age_restrictions_from,children_age_restrictions_to,maxAgeAutoTermed,allowedBeyoundAge,is_beneficiary_required,
			is_license_require,license_type,license_rule,payment_type,payment_type_subscription,
			pricing_model,is_add_on_product,is_life_insurance_product,life_term_type,guarantee_issue_amount_type,primary_issue_amount,spouse_issue_amount,child_issue_amount,is_spouse_issue_amount_larger,is_short_term_disablity_product,monthly_benefit_allowed,percentage_of_salary,joinder_agreement_require
		FROM prd_main WHERE id=:id AND is_deleted='N'";
		$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$product_id));

		if(!empty($resProduct)){
			$is_add_on_product = !empty($resProduct['is_add_on_product']) ? $resProduct['is_add_on_product'] : 'N';
			$product_type = !empty($resProduct['product_type']) ? $resProduct['product_type'] : '';
			$company_id = !empty($resProduct['company_id']) ? $resProduct['company_id'] : 0;
			$category_id = !empty($resProduct['category_id']) ? $resProduct['category_id'] : 0;
			$carrier_id = !empty($resProduct['carrier_id']) ? $resProduct['carrier_id'] : 0;
			$is_life_insurance_product = !empty($resProduct['is_life_insurance_product'])?$resProduct['is_life_insurance_product']:"";	
			$life_term_type = !empty($resProduct['life_term_type'])?$resProduct['life_term_type']:"";	
			$guarantee_issue_amount_type = !empty($resProduct['guarantee_issue_amount_type'])?$resProduct['guarantee_issue_amount_type']:"";	
			$primary_issue_amount = !empty($resProduct['primary_issue_amount'])?str_replace(',','',$resProduct['primary_issue_amount']):"";	
			$spouse_issue_amount = !empty($resProduct['spouse_issue_amount'])?str_replace(',','',$resProduct['spouse_issue_amount']):"";	
			$child_issue_amount = !empty($resProduct['child_issue_amount'])?str_replace(',','',$resProduct['child_issue_amount']):"";	
			$is_spouse_issue_amount_larger = !empty($resProduct['is_spouse_issue_amount_larger'])?$resProduct['is_spouse_issue_amount_larger']:"";	
			$is_short_term_disablity_product = !empty($resProduct['is_short_term_disablity_product']) ? $resProduct['is_short_term_disablity_product'] : "";
			$monthly_benefit_allowed = !empty($resProduct['monthly_benefit_allowed']) ? str_replace(',','',$resProduct['monthly_benefit_allowed']) : "";
			$percentage_of_salary = !empty($resProduct['percentage_of_salary']) ? str_replace(',','',$resProduct['percentage_of_salary']) : "";
			
			$direct_product = !empty($resProduct['direct_product']) ? $resProduct['direct_product'] : '';
			$effective_day = !empty($resProduct['effective_day']) ? $resProduct['effective_day'] : 0;
			$sold_day = !empty($resProduct['sold_day']) ? $resProduct['sold_day'] : 0;

			$membership_ids = !empty($resProduct['membership_ids']) ? $resProduct['membership_ids'] : '';
			$is_specific_zipcode = !empty($resProduct['is_specific_zipcode']) ? $resProduct['is_specific_zipcode'] : 'N';
			$no_sale_state_coverage_continue = !empty($resProduct['no_sale_state_coverage_continue']) ? $resProduct['no_sale_state_coverage_continue'] : 'N';
			
			$family_plan_rule = !empty($resProduct['family_plan_rule']) ? $resProduct['family_plan_rule'] : 'N';
			
			$termination_rule = !empty($resProduct['termination_rule']) ? $resProduct['termination_rule'] : '';
			
			$term_back_to_effective = !empty($resProduct['term_back_to_effective']) ? $resProduct['term_back_to_effective'] : '';
			
			$term_automatically = !empty($resProduct['term_automatically']) ? $resProduct['term_automatically'] : '';
			$term_automatically_within = !empty($resProduct['term_automatically_within']) ? $resProduct['term_automatically_within'] : 0;
			$term_automatically_within_type = !empty($resProduct['term_automatically_within_type']) ? $resProduct['term_automatically_within_type'] : 'Days';
			
			$reinstate_option = !empty($resProduct['reinstate_option']) ? $resProduct['reinstate_option'] : '';
			$reinstate_within = !empty($resProduct['reinstate_within']) ? $resProduct['reinstate_within'] : 0;
			$reinstate_within_type = !empty($resProduct['reinstate_within_type']) ? $resProduct['reinstate_within_type'] : 'Days';
			
			$reenroll_options = !empty($resProduct['reenroll_options']) ? $resProduct['reenroll_options'] : '';
			$reenroll_within = !empty($resProduct['reenroll_within']) ? $resProduct['reenroll_within'] : 0;
			$reenroll_within_type = !empty($resProduct['reenroll_within_type']) ? $resProduct['reenroll_within_type'] : 'Days';

			$is_primary_age_restrictions = !empty($resProduct['is_primary_age_restrictions']) ? $resProduct['is_primary_age_restrictions'] : 'N';
			$primary_age_restrictions_from = !empty($resProduct['primary_age_restrictions_from']) ? $resProduct['primary_age_restrictions_from'] : 0;
			$primary_age_restrictions_to = !empty($resProduct['primary_age_restrictions_to']) ? $resProduct['primary_age_restrictions_to'] : 0;

			$is_spouse_age_restrictions = !empty($resProduct['is_spouse_age_restrictions']) ? $resProduct['is_spouse_age_restrictions'] : 'N';
			$spouse_age_restrictions_from = !empty($resProduct['spouse_age_restrictions_from']) ? $resProduct['spouse_age_restrictions_from'] : 0;
			$spouse_age_restrictions_to = !empty($resProduct['spouse_age_restrictions_to']) ? $resProduct['spouse_age_restrictions_to'] : 0;
			
			$is_children_age_restrictions = !empty($resProduct['is_children_age_restrictions']) ? $resProduct['is_children_age_restrictions'] : 'N';
			$children_age_restrictions_from = !empty($resProduct['children_age_restrictions_from']) ? $resProduct['children_age_restrictions_from'] : 0;
			$children_age_restrictions_to = !empty($resProduct['children_age_restrictions_to']) ? $resProduct['children_age_restrictions_to'] : 0;
			
			$maxAgeAutoTermed = !empty($resProduct['maxAgeAutoTermed']) ? $resProduct['maxAgeAutoTermed'] : 'N';
			$allowedBeyoundAge = !empty($resProduct['allowedBeyoundAge']) ? $resProduct['allowedBeyoundAge'] : 'N';
			
			$is_beneficiary_required = !empty($resProduct['is_beneficiary_required']) ? $resProduct['is_beneficiary_required'] : 'N';
			$is_license_require = !empty($resProduct['is_license_require']) ? $resProduct['is_license_require'] : '';
			$license_type = !empty($resProduct['license_type']) ? $resProduct['license_type'] : '';
			$license_rule = !empty($resProduct['license_rule']) ? $resProduct['license_rule'] : '';
			
			$payment_type = !empty($resProduct['payment_type']) ? $resProduct['payment_type'] : '';
			$payment_type_subscription = !empty($resProduct['payment_type_subscription']) ? $resProduct['payment_type_subscription'] : '';
			$pricing_model = !empty($resProduct['pricing_model']) ? $resProduct['pricing_model'] : '';
			
			$joinder_agreement_require = !empty($resProduct['joinder_agreement_require']) ? $resProduct['joinder_agreement_require'] : '';

		}

		$sqlProductCode="SELECT id,code_no,plan_code_value FROM prd_plan_code where product_id=:product_id and is_deleted='N' ORDER BY id ASC";
		$resProductCode=$pdo->select($sqlProductCode,array(":product_id"=>$product_id));


		$planCodeArray = array();
		if(!empty($resProductCode)){
			foreach ($resProductCode as $key => $value) {
				$planCodeArray[$value['code_no']."_".$value['plan_code_value']] = $value['id'];
			}
		}

		$sqlCheckMemberPortal="SELECT id,name,description FROM prd_member_portal_information where product_id=:product_id and is_deleted='N' ORDER BY id ASC";
		$resCheckMemberPortal=$pdo->select($sqlCheckMemberPortal,array(":product_id"=>$product_id));

		$memberPortalArray = array();
		if(!empty($resCheckMemberPortal)){
			foreach ($resCheckMemberPortal as $key => $value) {
				$memberPortalArray[$value['name']] = $value['description'];
			}
		}

		$sqlCheckState="SELECT state_id,state_name from prd_available_state where is_deleted='N' AND product_id = :product_id";
		$resCheckState=$pdo->select($sqlCheckState,array(":product_id"=>$product_id));

		$availStateArray = array();
		if(!empty($resCheckState)){
			foreach ($resCheckState as $key => $value) {
				$availStateArray[$value['state_id']] = $value['state_name'];
			}
		}

		$sqlCheckNoSaleState="SELECT id,state_name,state_id,DATE_FORMAT(effective_date,'%Y%m%d') AS effective_date,DATE_FORMAT(termination_date,'%Y%m%d') AS termination_date from prd_no_sale_states where is_deleted='N' AND product_id = :product_id";
		$resCheckNoSaleState=$pdo->select($sqlCheckNoSaleState,array(":product_id"=>$product_id));

		$noSaleStateArray = array();
		if(!empty($resCheckNoSaleState)){
			foreach ($resCheckNoSaleState as $key => $value) {
				$noSaleStateArray[$value['state_id']."_".$value['effective_date']."_".$value['termination_date']] = $value['id'];
			}
		}

		$sqlCheckSpecificZipCode="SELECT state_name,state_id,zipcode from prd_specific_zipcode where is_deleted='N' AND product_id = :product_id";
		$resCheckSpecificZipCode=$pdo->select($sqlCheckSpecificZipCode,array(":product_id"=>$product_id));
		
		$specificZipCodeArray = array();
		if(!empty($resCheckSpecificZipCode)){
			foreach ($resCheckSpecificZipCode as $key => $value) {
				$specificZipCodeArray[$value['state_id']."_".$value['zipcode']] = $value['state_name'];
			}
		}

		$sqlCheckCoverage="SELECT prd_plan_type_id from prd_coverage_options where is_deleted='N' AND product_id = :product_id";
		$resCheckCoverage=$pdo->select($sqlCheckCoverage,array(":product_id"=>$product_id));

		$coverageOptionArray = array();
		if(!empty($resCheckCoverage)){
			foreach ($resCheckCoverage as $key => $value) {
				array_push($coverageOptionArray, $value['prd_plan_type_id']);
			}
		}

		$sqlCheckSubProducts="SELECT sub_product_id from prd_sub_products where is_deleted='N' AND product_id = :product_id";
		$resCheckSubProducts=$pdo->select($sqlCheckSubProducts,array(":product_id"=>$product_id));

		$subProductsArray = array();
		if(!empty($resCheckSubProducts)){
			foreach ($resCheckSubProducts as $key => $value) {
				array_push($subProductsArray, $value['sub_product_id']);
			}
		}

		$sqlCheckCombination="SELECT id,combination_type,combination_product_id from prd_combination_rule where is_deleted='N' AND product_id = :product_id";
		$resCheckCombination=$pdo->select($sqlCheckCombination,array(":product_id"=>$product_id));

		$checkCombinationArray = array();
		if(!empty($resCheckCombination)){
			foreach ($resCheckCombination as $key => $value) {
				$checkCombinationArray[$value['combination_type']."_".$value['combination_product_id']] = $value['id'];
			}
		}

		$sqlCheckMaxAgeTerm="SELECT id,member_type,terminate_within,terminate_within_type,
		terminate_range,terminate_trigger from prd_max_age_terminaion where is_deleted='N' AND product_id = :product_id";
		$resCheckMaxAgeTerm=$pdo->select($sqlCheckMaxAgeTerm,array(":product_id"=>$product_id));

		$maxAgeTermArray = array();
		if(!empty($resCheckMaxAgeTerm)){
			foreach ($resCheckMaxAgeTerm as $key => $value) {
				$maxAgeTermArray[$value['member_type']."_".$value['terminate_within']."_".$value['terminate_within_type']."_".$value['terminate_range']."_".$value['terminate_trigger']] = $value['id'];
			}
		}

		$sqlCheckBeyondAge="SELECT id,member_type from prd_beyond_age_disablity where is_deleted='N' AND product_id = :product_id";
		$resCheckBeyondAge=$pdo->select($sqlCheckBeyondAge,array(":product_id"=>$product_id));

		$maxBeyondAgeArray = array();
		if(!empty($resCheckBeyondAge)){
			foreach ($resCheckBeyondAge as $key => $value) {
				$maxBeyondAgeArray[$value['member_type']] = $value['id'];
			}
		}

		$sqlQuestion="SELECT prd_question_id,is_member_asked,is_member_required, is_spouse_asked,is_spouse_required,is_child_asked,is_child_required from prd_enrollment_questions_assigned where is_deleted='N' AND product_id = :product_id";
		$resQuestion=$pdo->select($sqlQuestion,array(":product_id"=>$product_id));

		$prdQustionArray = array();
		$prdMainQuestionList = array();
		if(!empty($resQuestion)){
			foreach ($resQuestion as $key => $value) {
				array_push($prdQustionArray, $value['prd_question_id']);
				$prdMainQuestionList[$value['prd_question_id']]['is_member_asked'] = $value['is_member_asked'];
				$prdMainQuestionList[$value['prd_question_id']]['is_member_required'] = $value['is_member_required'];
				$prdMainQuestionList[$value['prd_question_id']]['is_spouse_asked'] = $value['is_spouse_asked'];
				$prdMainQuestionList[$value['prd_question_id']]['is_spouse_required'] = $value['is_spouse_required'];
				$prdMainQuestionList[$value['prd_question_id']]['is_child_asked'] = $value['is_child_asked'];
				$prdMainQuestionList[$value['prd_question_id']]['is_child_required'] = $value['is_child_required'];
			}
		}

		$sqlBQuestion="SELECT prd_beneficiary_question_id,is_principal_beneficiary_asked,is_principal_beneficiary_required, is_contingent_beneficiary_asked,is_contingent_beneficiary_required from prd_beneficiary_questions_assigned where is_deleted='N' AND product_id = :product_id";
		$resBQuestion=$pdo->select($sqlBQuestion,array(":product_id"=>$product_id));

		$prdBQustionArray = array();
		$prdBMainQuestionList = array();
		if(!empty($resBQuestion)){
			foreach ($resBQuestion as $key => $value) {
				array_push($prdBQustionArray, $value['prd_beneficiary_question_id']);
				$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_principal_beneficiary_asked'] = $value['is_principal_beneficiary_asked'];
				$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_principal_beneficiary_required'] = $value['is_principal_beneficiary_required'];
				$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_contingent_beneficiary_asked'] = $value['is_contingent_beneficiary_asked'];
				$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_contingent_beneficiary_required'] = $value['is_contingent_beneficiary_required'];
			}
		}

		$sqlCheckVerification="SELECT verification_type from prd_enrollment_verification where is_deleted='N' AND product_id = :product_id";
		$resCheckVerification=$pdo->select($sqlCheckVerification,array(":product_id"=>$product_id));

		$verificationArray = array();
		if(!empty($resCheckVerification)){
			foreach ($resCheckVerification as $key => $value) {
				array_push($verificationArray, $value['verification_type']);
			}
		}

		$sqlCheckTerms="SELECT id,terms_condition FROM prd_terms_condition where product_id=:product_id AND is_deleted='N'";
		$resCheckTerms=$pdo->selectOne($sqlCheckTerms,array(":product_id"=>$product_id));

		$sqlCheckAgreement="SELECT id,joinder_agreement FROM prd_agreements where product_id=:product_id AND is_deleted='N'";
		$resCheckAgreement=$pdo->selectOne($sqlCheckAgreement,array(":product_id"=>$product_id));

		$sqlLicenseState="SELECT * from prd_license_state where is_deleted='N' AND product_id = :product_id";
		$resLicenseState=$pdo->select($sqlLicenseState,array(":product_id"=>$product_id));

		$prdLicenseState = array();
		$prdLicenseStateList = array();
		if(!empty($resLicenseState)){
			foreach ($resLicenseState as $key => $value) {
				$prdLicenseState[$value['sale_type']."_".$value['state_id']] = $value['id'];
				$prdLicenseStateList[$value['sale_type']."_".$value['state_id']] = $value;
			}
		}

		$sqlPrdValidation="SELECT id,errorJson FROM prd_product_builder_validation where product_id=:product_id";
		$resPrdValidation=$pdo->selectOne($sqlPrdValidation,array(":product_id"=>$product_id));
		//******************************************************

		$sqlDesc = "SELECT * FROM prd_descriptions where product_id = :product_id";
		$resDesc = $pdo->selectOne($sqlDesc,array(":product_id"=>$product_id));

		$variationIds = array();
		$variationProductCode = array();
		if($record_type=="Variation"){
			$sqlVariation="SELECT id,product_code FROM prd_main where id = :product_id AND is_deleted='N'";
			$resVariation=$pdo->selectOne($sqlVariation,array(":product_id"=>$variation_id));

			array_push($variationIds, $variation_id);
			$variationProductCode[$resVariation['id']] = $resVariation['product_code'];
		}else{
			$sqlVariation="SELECT id,product_code FROM prd_main where parent_product_id = :product_id AND is_deleted='N'";
			$resVariation=$pdo->select($sqlVariation,array(":product_id"=>$product_id));

			if(!empty($resVariation)){
				foreach ($resVariation as $key => $value) {
					array_push($variationIds, $value['id']);
					$variationProductCode[$value['id']] = $value['product_code'];
				}
			}
		}
		$matchGlobal = array();
		$matchIncr="";
		if(!empty($variationIds)){
			$variationList = implode(",", $variationIds);
			$matchIncr =" AND product_id in ($variationList)";
		}
		$sqlMatchGlobal= "SELECT id,product_id,match_globals from prd_match_globals WHERE is_deleted='N' $matchIncr";
		$resMatchGlobal= $pdo->select($sqlMatchGlobal,array(":product_id"=>$product_id));

		if(!empty($resMatchGlobal)){
			foreach ($resMatchGlobal as $key => $value) {
				$matchGlobal[$value['product_id']] = $value['match_globals'];
			}
		}

		

		if(!empty($variationIds)){
			foreach ($variationIds as $variationKey => $variation_id) {
				$matchGlobalArr= isset($matchGlobal[$variation_id]) ? explode(",", $matchGlobal[$variation_id]) : array();

				if($step == 1){
					if(in_array("Settings",$matchGlobalArr)){
						$insParams=array(
							'company_id'=>$company_id,
							'category_id'=>$category_id,
							'carrier_id'=>$carrier_id,
							'type'=>$resProduct['type'],
							'product_type'=>$product_type,
							'is_life_insurance_product'=>$is_life_insurance_product,
							'is_short_term_disablity_product'=>$is_short_term_disablity_product,
							'life_term_type'=>$life_term_type,
							'guarantee_issue_amount_type'=>$guarantee_issue_amount_type,
							'primary_issue_amount'=>$primary_issue_amount,
							'spouse_issue_amount'=>$spouse_issue_amount,
							'is_spouse_issue_amount_larger'=>$is_spouse_issue_amount_larger,
							'child_issue_amount'=>$child_issue_amount,
							'monthly_benefit_allowed' =>$monthly_benefit_allowed,
							'percentage_of_salary' =>$percentage_of_salary,
							'is_add_on_product'=>$is_add_on_product,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Settings',$variationProductCode[$variation_id]);
						}


						$sqlProductCode="SELECT id,code_no,plan_code_value FROM prd_plan_code where product_id=:product_id and is_deleted='N' ORDER BY id ASC";
						$resProductCode=$pdo->select($sqlProductCode,array(":product_id"=>$variation_id));

						$childPlanCodeArray = array();
						if(!empty($resProductCode)){
							foreach ($resProductCode as $key => $value) {
								$childPlanCodeArray[$value['code_no']."_".$value['plan_code_value']] = $value['id'];
							}
						}

						$planCodeResult=array_diff_key($planCodeArray,$childPlanCodeArray);
						
						if(!empty($planCodeResult)){
							foreach ($planCodeResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$code_no = $keyDiff[0];
								$plan_code_value = $keyDiff[1];

								$ins_params = array(
									"product_id"=>$variation_id,
									"code_no"=>$code_no,
									"plan_code_value"=>$plan_code_value,
							    );
							    $pdo->insert("prd_plan_code", $ins_params);

							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Plan Code '.$plan_code_value,'Admin Updated Product','Settings');
							}
						}

						$planCodeResult=array_diff_key($childPlanCodeArray,$planCodeArray);

						if(!empty($planCodeResult)){
							foreach ($planCodeResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$code_no = $keyDiff[0];
								$plan_code_value = $keyDiff[1];

								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'product_id=:product_id AND code_no=:code_no AND plan_code_value=:plan_code_value AND is_deleted="N"',
									'params'=>array(
										":product_id"=>$variation_id,
										":code_no"=>$code_no,
										":plan_code_value"=>$plan_code_value,
									)
								);
								$pdo->update("prd_plan_code",$updParams,$updWhr);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Plan Code '.$plan_code_value,'Admin Updated Product','Settings');
							}
						}
					}
					if(in_array("MemberPortalInformation",$matchGlobalArr)){
						$sqlCheckMemberPortal="SELECT id,name,description FROM prd_member_portal_information where product_id=:product_id and is_deleted='N' ORDER BY id ASC";
						$resCheckMemberPortal=$pdo->select($sqlCheckMemberPortal,array(":product_id"=>$variation_id));

						$childMemberPortalArray = array();
						if(!empty($resCheckMemberPortal)){
							foreach ($resCheckMemberPortal as $key => $value) {
								$childMemberPortalArray[$value['name']] = $value['description'];
							}
						}

						$memberPortalResult=array_diff_key($memberPortalArray,$childMemberPortalArray);
						if(!empty($memberPortalResult)){
							foreach ($memberPortalResult as $key => $value) {
								$ins_params = array(
									"product_id"=>$variation_id,
									"name"=>$key,
									"description"=>$value,
							    );
							    $pdo->insert("prd_member_portal_information", $ins_params);

							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Member Portal Section '.$key,'Admin Updated Product','Member Portal Information');
							}
						}
						

						$memberPortalResult=array_diff_key($childMemberPortalArray,$memberPortalArray);
						if(!empty($memberPortalResult)){
							foreach ($memberPortalResult as $key => $value) {

								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'product_id=:product_id AND name=:name AND is_deleted="N"',
									'params'=>array(
										":product_id"=>$variation_id,
										":name"=>$key,
									)
								);
								$pdo->update("prd_member_portal_information",$updParams,$updWhr);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Member Portal Section '.$key,'Admin Updated Product','Member Portal Information');
							}
						}

						$sqlCheckMemberPortal="SELECT id,name,description FROM prd_member_portal_information where product_id=:product_id and is_deleted='N' ORDER BY id ASC";
						$resCheckMemberPortal=$pdo->select($sqlCheckMemberPortal,array(":product_id"=>$variation_id));

						if(!empty($resCheckMemberPortal)){
							foreach ($resCheckMemberPortal as $key => $value) {
								$oldVaArray = $value;
								if(isset($memberPortalArray[$value['name']])){
									$updParams=array(
										'description'=>$memberPortalArray[$value['name']],
									);
									$updWhr=array(
										'clause'=>'id=:id',
										'params'=>array(
											":id"=>$value['id'],
										)
									);
									$pdo->update("prd_member_portal_information",$updParams,$updWhr);
									$NewVaArray = $updParams;
									unset($oldVaArray['id']);
									$activity=array_diff_assoc($oldVaArray,$NewVaArray);

									if(!empty($activity)){
										if(array_key_exists('description',$activity)){
											$sectionName="Member Portal Information";

											$tmp = array();
											$tmp2 = array();
											$tmp['description']=base64_encode($activity['description']);
											$tmp2['description']=base64_encode($NewVaArray['description']);
											
											
											$this->prdUpdActtivityFeed($variation_id,$product_id,$tmp,$tmp2,$sectionName,$variationProductCode[$variation_id],true);
										}
									}
								}
							}
						}
					}

					if(in_array("EnrollmentPage",$matchGlobalArr) || in_array("AgentEnrollmentInformation",$matchGlobalArr) || in_array("LimitationAndExclusions",$matchGlobalArr)){

						$childSqlDesc = "SELECT id,enrollment_desc,agent_portal,agent_info,limitations_exclusions FROM prd_descriptions where product_id = :product_id";
						$childResDesc = $pdo->selectOne($childSqlDesc,array(":product_id"=>$variation_id));
						$oldVaArray = $childResDesc;
						$descParams=array(
							'agent_info'=>!empty($resDesc['agent_info']) ? $resDesc['agent_info'] : '',
						);
						if(in_array("EnrollmentPage",$matchGlobalArr)){
							$descParams['enrollment_desc'] = !empty($resDesc['enrollment_desc']) ? $resDesc['enrollment_desc'] : '';
						}
						if(in_array("AgentEnrollmentInformation",$matchGlobalArr)){
							$descParams['agent_portal'] = !empty($resDesc['agent_portal']) ? $resDesc['agent_portal'] : '';
						}
						if(in_array("LimitationAndExclusions",$matchGlobalArr)){
							$descParams['limitations_exclusions'] = !empty($resDesc['limitations_exclusions']) ? $resDesc['limitations_exclusions'] : '';
						}
						if(!empty($childResDesc)){
							$descWhere=array(
								'clause'=>'id=:id',
								'params'=>array(":id"=>$childResDesc['id'])
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
								$tmp2['enrollment_desc']=isset($descParams['enrollment_desc']) ? base64_encode($descParams['enrollment_desc']) : "";

								$this->prdUpdActtivityFeed($variation_id,$product_id,$tmp,$tmp2,$sectionName,$variationProductCode[$variation_id],true);
							}
							if(array_key_exists('limitations_exclusions',$activity)){
								$sectionName="Limitations and Exclusions";
								$tmp = array();
								$tmp2 = array();
								$tmp['limitations_exclusions']=base64_encode($activity['limitations_exclusions']);
								$tmp2['limitations_exclusions']= isset($descParams['limitations_exclusions']) ? base64_encode($descParams['limitations_exclusions']) : "";

								$this->prdUpdActtivityFeed($variation_id,$product_id,$tmp,$tmp2,$sectionName,$variationProductCode[$variation_id],true);
							}
							if(array_key_exists('agent_portal',$activity)){
								$sectionName="Agent Application Information";

								$tmp = array();
								$tmp2 = array();
								$tmp['agent_portal']=base64_encode($activity['agent_portal']);
								$tmp2['agent_portal']=isset($descParams['agent_portal']) ? base64_encode($descParams['agent_portal']) : "";
								
								$this->prdUpdActtivityFeed($variation_id,$product_id,$tmp,$tmp2,$sectionName,$variationProductCode[$variation_id],true);
							}
							if(array_key_exists('agent_info',$activity)){
								$sectionName="Agent Application Information";

								$tmp = array();
								$tmp2 = array();
								$tmp['agent_info']=$activity['agent_info'];
								$tmp2['agent_info']=$descParams['agent_info'];
								
								$this->prdUpdActtivityFeed($variation_id,$product_id,$tmp,$tmp2,$sectionName,$variationProductCode[$variation_id]);
							}														
						}
						}else{
							$descParams['product_id']=$variation_id;
							$pdo->insert("prd_descriptions",$descParams);
						}

					}
				}
				if($step == 2){
					if(in_array("EffecttiveDate",$matchGlobalArr)){
						$insParams=array(
							'direct_product'=>$direct_product,
							'effective_day'=>$effective_day,
							'sold_day'=>$sold_day,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Effective Date',$variationProductCode[$variation_id]);

						}

					}
					if(in_array("MembershipRequirement",$matchGlobalArr)){
						$insParams=array(
							'membership_ids'=>$membership_ids,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Membership Requirements',$variationProductCode[$variation_id]);
						}
					}
					if(in_array("Availability",$matchGlobalArr)){
						$insParams=array(
							'is_specific_zipcode'=>$is_specific_zipcode,
							'no_sale_state_coverage_continue'=>$no_sale_state_coverage_continue,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Availability',$variationProductCode[$variation_id]);
						}

						$sqlCheckState="SELECT state_id,state_name from prd_available_state where is_deleted='N' AND product_id = :product_id";
						$resCheckState=$pdo->select($sqlCheckState,array(":product_id"=>$variation_id));

						$childAvailStateArray = array();
						if(!empty($resCheckState)){
							foreach ($resCheckState as $key => $value) {
								$childAvailStateArray[$value['state_id']] = $value['state_name'];
							}
						}

						$stateResult=array_diff_key($availStateArray,$childAvailStateArray);
						
						if(!empty($stateResult)){
							foreach ($stateResult as $key => $value) {
								$ins_params = array(
									"product_id"=>$variation_id,
									"state_id"=>$key,
									"state_name"=>$value,
							    );
							    $pdo->insert("prd_available_state", $ins_params);
							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Available State '.$value,'Admin Updated Product','Availability');
							}
						}

						$stateResult=array_diff_key($childAvailStateArray,$availStateArray);
						if(!empty($stateResult)){
							foreach ($stateResult as $key => $value) {
								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'product_id=:product_id AND state_id=:state_id AND is_deleted="N"',
									'params'=>array(
										":product_id"=>$variation_id,
										":state_id"=>$key,
									)
								);
								$pdo->update("prd_available_state",$updParams,$updWhr);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Available State '.$value,'Admin Updated Product','Availability');
							}
						}

						$sqlCheckNoSaleState="SELECT id,state_name,state_id,DATE_FORMAT(effective_date,'%Y%m%d') AS effective_date,DATE_FORMAT(termination_date,'%Y%m%d') AS termination_date from prd_no_sale_states where is_deleted='N' AND product_id = :product_id";
						$resCheckNoSaleState=$pdo->select($sqlCheckNoSaleState,array(":product_id"=>$variation_id));

						$childNoSaleStateArray = array();
						if(!empty($resCheckNoSaleState)){
							foreach ($resCheckNoSaleState as $key => $value) {
								$childNoSaleStateArray[$value['state_id']."_".$value['effective_date']."_".$value['termination_date']] = $value['id'];
							}
						}

						$noSaleStateResult=array_diff_key($noSaleStateArray,$childNoSaleStateArray);
						
						if(!empty($noSaleStateResult)){
							foreach ($noSaleStateResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$state_id = $keyDiff[0];
								$state_name=$allStateRes[$state_id]['name'];
								$effective_date = !empty($keyDiff[1]) ? date('Y-m-d',strtotime($keyDiff[1])) : NULL;
								$termination_date = !empty($keyDiff[2]) ? date('Y-m-d',strtotime($keyDiff[2])) : NULL;
								
								$ins_params = array(
									"product_id"=>$variation_id,
									"state_id"=>$state_id,
									"effective_date"=>$effective_date,
									"termination_date"=>$termination_date,
									"state_name"=>$state_name,
							    );
							    $pdo->insert("prd_no_sale_states", $ins_params);

							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added No Sale State '.$state_name,'Admin Updated Product','Availability');
							}
						}

						$noSaleStateResult=array_diff_key($childNoSaleStateArray,$noSaleStateArray);

						if(!empty($noSaleStateResult)){
							foreach ($noSaleStateResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$state_id = $keyDiff[0];
								$state_name=$allStateRes[$state_id]['name'];

								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$value,
									)
								);
								$pdo->update("prd_no_sale_states",$updParams,$updWhr);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed No Sale State '.$state_name,'Admin Updated Product','Availability');
							}
						}

						$sqlCheckSpecificZipCode="SELECT state_name,state_id,zipcode from prd_specific_zipcode where is_deleted='N' AND product_id = :product_id";
						$resCheckSpecificZipCode=$pdo->select($sqlCheckSpecificZipCode,array(":product_id"=>$variation_id));
						
						$childSpecificZipCodeArray = array();
						if(!empty($resCheckSpecificZipCode)){
							foreach ($resCheckSpecificZipCode as $key => $value) {
								$childSpecificZipCodeArray[$value['state_id']."_".$value['zipcode']] = $value['state_name'];
							}
						}

						$specificZipCodeResult=array_diff_key($specificZipCodeArray,$childSpecificZipCodeArray);
						
						if(!empty($specificZipCodeResult)){
							foreach ($specificZipCodeResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$state_id = $keyDiff[0];
								$zipcode = $keyDiff[1];

								$ins_params = array(
									"product_id"=>$variation_id,
									"state_id"=>$state_id,
									"zipcode"=>$zipcode,
									"state_name"=>$value,
							    );
							    $pdo->insert("prd_specific_zipcode", $ins_params);

							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Specific ZipCode '.$zipcode,'Admin Updated Product','Availability');
							}
						}

						$specificZipCodeResult=array_diff_key($childSpecificZipCodeArray,$specificZipCodeArray);

						if(!empty($specificZipCodeResult)){
							foreach ($specificZipCodeResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$state_id = $keyDiff[0];
								$zipcode = $keyDiff[1];

								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'product_id=:product_id AND state_id=:state_id AND zipcode = :zipcode AND is_deleted="N"',
									'params'=>array(
										":product_id"=>$variation_id,
										":state_id"=>$state_id,
										":zipcode"=>$zipcode,
									)
								);
								$pdo->update("prd_specific_zipcode",$updParams,$updWhr);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Specific ZipCode '.$zipcode,'Admin Updated Product','Availability');
							}
						}
					}
					if(in_array("CoverageOptions",$matchGlobalArr)){
						$insParams=array(
							'family_plan_rule'=>$family_plan_rule,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Coverage Options',$variationProductCode[$variation_id]);
						}
						
						$sqlCheckCoverage="SELECT prd_plan_type_id from prd_coverage_options where is_deleted='N' AND product_id = :product_id";
						$resCheckCoverage=$pdo->select($sqlCheckCoverage,array(":product_id"=>$variation_id));

						$childCoverageOptionArray = array();
						if(!empty($resCheckCoverage)){
							foreach ($resCheckCoverage as $key => $value) {
								array_push($childCoverageOptionArray, $value['prd_plan_type_id']);
							}
						}

						$coverageOptionResult=array_diff($coverageOptionArray,$childCoverageOptionArray);
						if(!empty($coverageOptionResult)){
							foreach ($coverageOptionResult as $key => $value) {
								$insParams = array(
					            	"product_id" => $variation_id,
					            	"prd_plan_type_id" => $value,
				          		);
				          		$prd_coverage_options = $pdo->insert('prd_coverage_options',$insParams);
				          		$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Coverage Options '.$prdPlanTypeArray[$value]['title'],'Admin Updated Product','Coverage Options');
							}
						}

						$coverageOptionResult=array_diff($childCoverageOptionArray,$coverageOptionArray);
						if(!empty($coverageOptionResult)){
							foreach ($coverageOptionResult as $key => $value) {
								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'is_deleted="N" AND product_id = :product_id and prd_plan_type_id=:prd_plan_type_id',
									'params'=>array(
										":product_id"=>$variation_id,
										":prd_plan_type_id"=>$value,
									)
								);
								$pdo->update("prd_coverage_options",$updParams,$updWhr);
								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Coverage Options '.$prdPlanTypeArray[$value]['title'],'Admin Updated Product','Coverage Options');
							}
						}
					}
					if(in_array("SubProducts",$matchGlobalArr)){
						$sqlCheckSubProducts="SELECT sub_product_id from prd_sub_products where is_deleted='N' AND product_id = :product_id";
						$resCheckSubProducts=$pdo->select($sqlCheckSubProducts,array(":product_id"=>$variation_id));

						$childSubProductsArray = array();
						if(!empty($resCheckSubProducts)){
							foreach ($resCheckSubProducts as $key => $value) {
								array_push($childSubProductsArray, $value['sub_product_id']);
							}
						}

						$subProductsResult=array_diff($subProductsArray,$childSubProductsArray);
						if(!empty($subProductsResult)){
							foreach ($subProductsResult as $key => $value) {
								$insParams = array(
					            	"product_id" => $variation_id,
					            	"sub_product_id" => $value,
				          		);
				          		$pdo->insert('prd_sub_products',$insParams);
				          		$resP=$pdo->selectOne("SELECT CONCAT(product_name,'(',product_code,')') as sub_products FROM sub_products where id=:id",array(":id"=>$value));
				          		$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Sub Product '.$resP['sub_products'],'Admin Updated Product','Sub Products');
							}
						}

						$subProductsResult=array_diff($childSubProductsArray,$subProductsArray);

						if(!empty($subProductsResult)){
							foreach ($subProductsResult as $key => $value) {
								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'is_deleted="N" AND product_id = :product_id and sub_product_id=:sub_product_id',
									'params'=>array(
										":product_id"=>$variation_id,
										":sub_product_id"=>$value,
									)
								);
								$pdo->update("prd_sub_products",$updParams,$updWhr);
								$resP=$pdo->selectOne("SELECT CONCAT(product_name,'(',product_code,')') as sub_products FROM sub_products where id=:id",array(":id"=>$value));
				          		$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Sub Product '.$resP['sub_products'],'Admin Updated Product','Sub Products');
							}
						}
					}
					if(in_array("ProductCombinationRules",$matchGlobalArr)){
						$sqlCheckCombination="SELECT id,combination_type,combination_product_id from prd_combination_rule where is_deleted='N' AND product_id = :product_id";
						$resCheckCombination=$pdo->select($sqlCheckCombination,array(":product_id"=>$variation_id));

						$childCheckCombinationArray = array();
						if(!empty($resCheckCombination)){
							foreach ($resCheckCombination as $key => $value) {
								$childCheckCombinationArray[$value['combination_type']."_".$value['combination_product_id']] = $value['id'];
							}
						}

						$combinationResult=array_diff_key($checkCombinationArray,$childCheckCombinationArray);
						
						if(!empty($combinationResult)){
							foreach ($combinationResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$combination_type = $keyDiff[0];
								$combination_product_id = $keyDiff[1];
								
								$ins_params = array(
									"product_id"=>$variation_id,
									"combination_type"=>$combination_type,
									"combination_product_id"=>$combination_product_id,
							    );
							    $pdo->insert("prd_combination_rule", $ins_params);

							    $resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$combination_product_id));

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added '.$combination_type.' Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');
							}
						}

						$combinationResult=array_diff_key($childCheckCombinationArray,$checkCombinationArray);

						if(!empty($combinationResult)){
							foreach ($combinationResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$combination_type = $keyDiff[0];
								$combination_product_id = $keyDiff[1];

								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$value,
									)
								);
								$pdo->update("prd_combination_rule",$updParams,$updWhr);

								$resP=$pdo->selectOne("SELECT CONCAT(name,' (',product_code,')') as comb_prd FROM prd_main where id=:id",array(":id"=>$combination_product_id));

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed '.$combination_type.' Product '.$resP['comb_prd'],'Admin Updated Product','Product Combination Rules');	
							}
						}
					}
					if(in_array("TerminationRules",$matchGlobalArr)){
						$insParams=array(
							'termination_rule'=>$termination_rule,
							'term_back_to_effective'=>$term_back_to_effective,
							'term_automatically'=>$term_automatically,
							'term_automatically_within'=>$term_automatically_within,
							'term_automatically_within_type'=>$term_automatically_within_type,
							'reinstate_option'=>$reinstate_option,
							'reinstate_within'=>$reinstate_within,
							'reinstate_within_type'=>$reinstate_within_type,
							'reenroll_options'=>$reenroll_options,
							'reenroll_within'=>$reenroll_within,
							'reenroll_within_type'=>$reenroll_within_type,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Termination Rules',$variationProductCode[$variation_id]);
						}
					}
				}
				if($step == 3){
					if(in_array("AgeRestrictions",$matchGlobalArr)){
						$insParams=array(
							'is_primary_age_restrictions'=>$is_primary_age_restrictions,
							'primary_age_restrictions_from'=>$primary_age_restrictions_from,
							'primary_age_restrictions_to'=>$primary_age_restrictions_to,
							'is_spouse_age_restrictions'=>$is_spouse_age_restrictions,
							'spouse_age_restrictions_from'=>$spouse_age_restrictions_from,
							'spouse_age_restrictions_to'=>$spouse_age_restrictions_to,
							'is_children_age_restrictions'=>$is_children_age_restrictions,
							'children_age_restrictions_from'=>$children_age_restrictions_from,
							'children_age_restrictions_to'=>$children_age_restrictions_to,
							'maxAgeAutoTermed'=>$maxAgeAutoTermed,
							'allowedBeyoundAge'=>$allowedBeyoundAge,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);
						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Age Restrictions',$variationProductCode[$variation_id]);
						}

						$sqlCheckMaxAgeTerm="SELECT id,member_type,terminate_within,terminate_within_type,
						terminate_range,terminate_trigger from prd_max_age_terminaion where is_deleted='N' AND product_id = :product_id";
						$resCheckMaxAgeTerm=$pdo->select($sqlCheckMaxAgeTerm,array(":product_id"=>$variation_id));

						$childMaxAgeTermArray = array();
						if(!empty($resCheckMaxAgeTerm)){
							foreach ($resCheckMaxAgeTerm as $key => $value) {
								$childMaxAgeTermArray[$value['member_type']."_".$value['terminate_within']."_".$value['terminate_within_type']."_".$value['terminate_range']."_".$value['terminate_trigger']] = $value['id'];
							}
						}

						$maxAgeTermResult=array_diff_key($maxAgeTermArray,$childMaxAgeTermArray);
						
						if(!empty($maxAgeTermResult)){
							foreach ($maxAgeTermResult as $key => $value) {
								$keyDiff = explode("_", $key);
								
								$member_type = $keyDiff[0];
								$terminate_within = $keyDiff[1];
								$terminate_within_type = $keyDiff[2];
								$terminate_range = $keyDiff[3];
								$terminate_trigger = $keyDiff[4];
								
								$ins_params = array(
									"product_id"=>$variation_id,
									"member_type"=>$member_type,
									"terminate_within"=>$terminate_within,
									"terminate_within_type"=>$terminate_within_type,
									"terminate_range"=>$terminate_range,
									"terminate_trigger"=>$terminate_trigger,
							    );
							    $pdo->insert("prd_max_age_terminaion", $ins_params);

							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added '.$member_type.' Auto Termed Setting ','Admin Updated Product','Age Restrictions');
							}
						}

						$maxAgeTermResult=array_diff_key($childMaxAgeTermArray,$maxAgeTermArray);

						if(!empty($maxAgeTermResult)){
							foreach ($maxAgeTermResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$member_type = $keyDiff[0];

								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$value,
									)
								);
								$pdo->update("prd_max_age_terminaion",$updParams,$updWhr);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed '.$member_type.' Auto Termed Setting ','Admin Updated Product','Age Restrictions');
							}
						}

						$sqlCheckBeyondAge="SELECT id,member_type from prd_beyond_age_disablity where is_deleted='N' AND product_id = :product_id";
						$resCheckBeyondAge=$pdo->select($sqlCheckBeyondAge,array(":product_id"=>$variation_id));

						$childMaxBeyondAgeArray = array();
						if(!empty($resCheckBeyondAge)){
							foreach ($resCheckBeyondAge as $key => $value) {
								$childMaxBeyondAgeArray[$value['member_type']] = $value['id'];
							}
						}

						$beyondAgeResult=array_diff_key($maxBeyondAgeArray,$childMaxBeyondAgeArray);
						
						if(!empty($beyondAgeResult)){
							foreach ($beyondAgeResult as $key => $value) {
								$keyDiff = explode("_", $key);
								
								$member_type = $keyDiff[0];
								//$terminate_within = $keyDiff[1];
								//$terminate_within_type = $keyDiff[2];
								//$terminate_range = $keyDiff[3];
								//$terminate_trigger = $keyDiff[4];
								
								$ins_params = array(
									"product_id"=>$variation_id,
									"member_type"=>$member_type,
									//"terminate_within"=>$terminate_within,
									//"terminate_within_type"=>$terminate_within_type,
									//"terminate_range"=>$terminate_range,
									//"terminate_trigger"=>$terminate_trigger,
							    );
							    $pdo->insert("prd_beyond_age_disablity", $ins_params);

							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added '.$member_type.' Documented Disability ','Admin Updated Product','Age Restrictions');
							}
						}

						$beyondAgeResult=array_diff_key($childMaxBeyondAgeArray,$maxBeyondAgeArray);
						
						if(!empty($beyondAgeResult)){
							foreach ($beyondAgeResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$member_type = $keyDiff[0];
								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$value,
									)
								);
								$pdo->update("prd_beyond_age_disablity",$updParams,$updWhr);
								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed '.$member_type.' Documented Disability ','Admin Updated Product','Age Restrictions');
							}
						}

					}
					if(in_array("MemberEnrollmentInformation",$matchGlobalArr)){
						$sqlQuestion="SELECT prd_question_id,is_member_asked,is_member_required, is_spouse_asked,is_spouse_required,is_child_asked,is_child_required from prd_enrollment_questions_assigned where is_deleted='N' AND product_id = :product_id";
						$resQuestion=$pdo->select($sqlQuestion,array(":product_id"=>$variation_id));

						$sqlPricingQue="SELECT 
						GROUP_CONCAT(ppq.prd_enrollment_questions_id) AS prd_questions_id 
							FROM prd_pricing_question ppq
							JOIN prd_pricing_question_assigned ppqa ON (ppq.id = ppqa.prd_pricing_question_id)
							WHERE ppqa.product_id=:product_id AND prd_enrollment_questions_id != 0 AND ppq.is_deleted='N' AND ppqa.is_deleted='N'";
						$resPricingQue=$pdo->selectOne($sqlPricingQue,array(":product_id"=>$variation_id));

						$pricingQueArr = array();
						if(!empty($resPricingQue) && !empty($resPricingQue['prd_questions_id'])){
							$pricingQueArr = explode(",", $resPricingQue['prd_questions_id']);
						}

						$childPrdQustionArray = array();
						if(!empty($resQuestion)){
							foreach ($resQuestion as $key => $value) {
								array_push($childPrdQustionArray, $value['prd_question_id']);
							}
						}



						$prdQuestionResult=array_diff($prdQustionArray,$childPrdQustionArray);
						if(!empty($prdQuestionResult)){
							foreach ($prdQuestionResult as $key => $value) {
								if(!empty($value) && !in_array($value, $pricingQueArr)){
									$insParams = array(
						            	"product_id" => $variation_id,
						            	"prd_question_id" => $value,
					          		);
					          		$pdo->insert('prd_enrollment_questions_assigned',$insParams);
				          		}
							}
						}

						$prdQuestionResult=array_diff($childPrdQustionArray,$prdQustionArray);
						
						if(!empty($prdQuestionResult)){
							foreach ($prdQuestionResult as $key => $value) {
								if(!empty($value) && !in_array($value, $pricingQueArr)){
									$updParams=array(
										'is_member_asked'=>'N',
										'is_member_required'=>'N',
										'is_spouse_asked'=>'N',
										'is_spouse_required'=>'N',
										'is_child_asked'=>'N',
										'is_child_required'=>'N',
									);
									$updWhr=array(
										'clause'=>'is_deleted="N" AND product_id = :product_id and prd_question_id=:prd_question_id',
										'params'=>array(
											":product_id"=>$variation_id,
											":prd_question_id"=>$value,
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updParams,$updWhr,true);

									if(!empty($activity)){
										$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$value));

										$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$updParams,'Member Enrollment Information',$variationProductCode[$variation_id],false,$resQ['display_label']);
									}
								}
								
							}
						}

						$sqlQuestion="SELECT id,prd_question_id,is_member_asked,is_member_required, is_spouse_asked,is_spouse_required,is_child_asked,is_child_required from prd_enrollment_questions_assigned where is_deleted='N' AND product_id = :product_id";
						$resQuestion=$pdo->select($sqlQuestion,array(":product_id"=>$variation_id));
						if(!empty($resQuestion)){
							foreach ($resQuestion as $key => $value) {
								if(isset($prdMainQuestionList[$value['prd_question_id']]) && !empty($value['prd_question_id']) && !in_array($value['prd_question_id'], $pricingQueArr)){
									$updParams=array(
										'is_member_asked'=>$prdMainQuestionList[$value['prd_question_id']]['is_member_asked'],
										'is_member_required'=>$prdMainQuestionList[$value['prd_question_id']]['is_member_required'],
										'is_spouse_asked'=>$prdMainQuestionList[$value['prd_question_id']]['is_spouse_asked'],
										'is_spouse_required'=>$prdMainQuestionList[$value['prd_question_id']]['is_spouse_required'],
										'is_child_asked'=>$prdMainQuestionList[$value['prd_question_id']]['is_child_asked'],
										'is_child_required'=>$prdMainQuestionList[$value['prd_question_id']]['is_child_required'],
									);
									$updWhr=array(
										'clause'=>'id=:id',
										'params'=>array(
											":id"=>$value['id'],
										)
									);
									$activity = $pdo->update("prd_enrollment_questions_assigned",$updParams,$updWhr,true);
									if(!empty($activity)){
										$resQ=$pdo->selectOne("SELECT display_label FROM prd_enrollment_questions where id=:id",array(":id"=>$value['prd_question_id']));

										$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$updParams,'Member Enrollment Information',$variationProductCode[$variation_id],false,$resQ['display_label']);
									}
								}
							}
						}
					}
					if(in_array("BeneficiaryInformation",$matchGlobalArr)){
						$insParams=array(
							'is_beneficiary_required'=>$is_beneficiary_required,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);
						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Beneficiary Information',$variationProductCode[$variation_id]);
						}

						$sqlBQuestion="SELECT prd_beneficiary_question_id,is_principal_beneficiary_asked,is_principal_beneficiary_required, is_contingent_beneficiary_asked,is_contingent_beneficiary_required from prd_beneficiary_questions_assigned where is_deleted='N' AND product_id = :product_id";
						$resBQuestion=$pdo->select($sqlBQuestion,array(":product_id"=>$variation_id));

						$childPrdBQustionArray = array();
						if(!empty($resBQuestion)){
							foreach ($resBQuestion as $key => $value) {
								array_push($childPrdBQustionArray, $value['prd_beneficiary_question_id']);
							}
						}

						$prdBQuestionResult=array_diff($prdBQustionArray,$childPrdBQustionArray);
						if(!empty($prdBQuestionResult)){
							foreach ($prdBQuestionResult as $key => $value) {
								$insParams = array(
					            	"product_id" => $variation_id,
					            	"prd_beneficiary_question_id" => $value,
				          		);
				          		$pdo->insert('prd_beneficiary_questions_assigned',$insParams);
							}
						}

						$prdBQuestionResult=array_diff($childPrdBQustionArray,$prdBQustionArray);
						
						if(!empty($prdBQuestionResult)){
							foreach ($prdBQuestionResult as $key => $value) {
								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'is_deleted="N" AND product_id = :product_id and prd_beneficiary_question_id=:prd_beneficiary_question_id',
									'params'=>array(
										":product_id"=>$variation_id,
										":prd_beneficiary_question_id"=>$value,
									)
								);
								$pdo->update("prd_beneficiary_questions_assigned",$updParams,$updWhr);
								$resQ=$pdo->selectOne("SELECT display_label FROM prd_beneficiary_questions where id=:id",array(":id"=>$value));
								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed '.$resQ['display_label'],'Admin Updated Product','Beneficiary Information');
							}
						}

						$sqlQuestion="SELECT id,prd_beneficiary_question_id,is_principal_beneficiary_asked,is_principal_beneficiary_required, is_contingent_beneficiary_asked,is_contingent_beneficiary_required from prd_beneficiary_questions_assigned where is_deleted='N' AND product_id = :product_id";
						$resQuestion=$pdo->select($sqlQuestion,array(":product_id"=>$variation_id));
						if(!empty($resQuestion)){
							foreach ($resQuestion as $key => $value) {
								if(isset($prdBMainQuestionList[$value['prd_beneficiary_question_id']])){
									$updParams=array(
										'is_principal_beneficiary_asked'=>$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_principal_beneficiary_asked'],
										'is_principal_beneficiary_required'=>$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_principal_beneficiary_required'],
										'is_contingent_beneficiary_asked'=>$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_contingent_beneficiary_asked'],
										'is_contingent_beneficiary_required'=>$prdBMainQuestionList[$value['prd_beneficiary_question_id']]['is_contingent_beneficiary_required'],
									);
									$updWhr=array(
										'clause'=>'id=:id',
										'params'=>array(
											":id"=>$value['id'],
										)
									);
									$activity = $pdo->update("prd_beneficiary_questions_assigned",$updParams,$updWhr,true);
									if(!empty($activity)){
										$resQ=$pdo->selectOne("SELECT display_label FROM prd_beneficiary_questions where id=:id",array(":id"=>$value['prd_beneficiary_question_id']));
		          						
										$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$updParams,'Beneficiary Information',$variationProductCode[$variation_id],false,$resQ['display_label']);
									}
								}
							}
						}
					}
					if(in_array("EnrollmentVerification",$matchGlobalArr)){
						$sqlCheckVerification="SELECT verification_type from prd_enrollment_verification where is_deleted='N' AND product_id = :product_id";
						$resCheckVerification=$pdo->select($sqlCheckVerification,array(":product_id"=>$variation_id));

						$childVerificationArray = array();
						if(!empty($resCheckVerification)){
							foreach ($resCheckVerification as $key => $value) {
								array_push($childVerificationArray, $value['verification_type']);
							}
						}

						$verificationResult=array_diff($verificationArray,$childVerificationArray);
						if(!empty($verificationResult)){
							foreach ($verificationResult as $key => $value) {
								$insSubParams=array(
									'product_id'=>$variation_id,
									'verification_type'=>$value,
								);
								$pdo->insert('prd_enrollment_verification',$insSubParams);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Verification Option '.str_replace("_", " ", $value),'Admin Updated Product','Application Verification');	
							}
						}

						$verificationResult=array_diff($childVerificationArray,$verificationArray);
						if(!empty($verificationResult)){
							foreach ($verificationResult as $key => $value) {
								$updVerificationParams=array(
									'is_deleted'=>'Y'
								);
								$updVerificationWhere=array(
									'clause'=>'product_id=:product_id and verification_type = :verification_type AND is_deleted="N"',
									'params'=>array(
										":verification_type"=>$value,
										":product_id"=>$variation_id
									)
								);
								$pdo->update("prd_enrollment_verification",$updVerificationParams,$updVerificationWhere);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Verification Option '.str_replace("_", " ", $value),'Admin Updated Product','Application Verification');					
							}
						}

						$sqlChildCheckTerms="SELECT id,terms_condition FROM prd_terms_condition where product_id=:product_id AND is_deleted='N'";
						$resChildCheckTerms=$pdo->selectOne($sqlChildCheckTerms,array(":product_id"=>$variation_id));

						if(empty($resCheckTerms) && !empty($resChildCheckTerms)){
							$updParams=array(
									'is_deleted'=>'Y'
								);
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$resChildCheckTerms['id'],
								)
							);
							$pdo->update("prd_terms_condition",$updParams,$updWhere);

							$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Terms And Condition','Admin Updated Product','Application Verification');	
						}
						if(!empty($resCheckTerms) && empty($resChildCheckTerms)){
							$updParams=array(
								'product_id'=>$variation_id,
								'terms_condition'=>$resCheckTerms['terms_condition']
							);
							$pdo->insert("prd_terms_condition",$updParams);

							$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Terms And Condition','Admin Updated Product','Application Verification');
						}
						if(!empty($resCheckTerms) && !empty($resChildCheckTerms)){
							$updParams=array(
								'terms_condition'=>$resCheckTerms['terms_condition']
							);
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$resChildCheckTerms['id'],
								)
							);
							$pdo->update("prd_terms_condition",$updParams,$updWhere);
							
							
							$tmp = array();
							$tmp2 = array();
							$tmp['terms_condition']=base64_encode($resChildCheckTerms['terms_condition']);
							$tmp2['terms_condition']=base64_encode($resCheckTerms['terms_condition']);

							$this->prdUpdActtivityFeed($variation_id,$product_id,$tmp,$tmp2,'Application Verification',$variationProductCode[$variation_id],true);
							
						}

						$updParams=array(
							'joinder_agreement_require'=>$joinder_agreement_require,
						);

						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$updParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$updParams,'Application Verification',$variationProductCode[$variation_id]);
						}

						$sqlChildCheckAgreement="SELECT id,joinder_agreement FROM prd_agreements where product_id=:product_id AND is_deleted='N'";
						$resChildCheckAgreement=$pdo->selectOne($sqlChildCheckAgreement,array(":product_id"=>$variation_id));

						if(empty($resCheckAgreement) && !empty($resChildCheckAgreement)){
							$updParams=array(
									'is_deleted'=>'Y'
								);
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$resChildCheckAgreement['id'],
								)
							);
							$pdo->update("prd_agreements",$updParams,$updWhere);

							$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed Joinder Agreement','Admin Updated Product','Application Verification');	
						}
						if(!empty($resCheckAgreement) && empty($resChildCheckAgreement)){
							$updParams=array(
								'product_id'=>$variation_id,
								'joinder_agreement'=>$resCheckAgreement['joinder_agreement']
							);
							$pdo->insert("prd_agreements",$updParams);

							$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added Joinder Agreement','Admin Updated Product','Application Verification');
						}
						if(!empty($resCheckAgreement) && !empty($resChildCheckAgreement)){
							$updParams=array(
								'joinder_agreement'=>$resCheckAgreement['joinder_agreement']
							);
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$resChildCheckAgreement['id'],
								)
							);
							$pdo->update("prd_agreements",$updParams,$updWhere);
							
							
							$tmp = array();
							$tmp2 = array();
							$tmp['joinder_agreement']=base64_encode($resChildCheckAgreement['joinder_agreement']);
							$tmp2['joinder_agreement']=base64_encode($resCheckAgreement['joinder_agreement']);

							$this->prdUpdActtivityFeed($variation_id,$product_id,$tmp,$tmp2,'Application Verification',$variationProductCode[$variation_id],true);
							
						}

					}
					if(in_array("AgentRequirements",$matchGlobalArr)){
						$insParams=array(
							'is_license_require'=>$is_license_require,
							'license_type'=>$license_type,
							'license_rule'=>$license_rule,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity =$pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Agent Requirements',$variationProductCode[$variation_id]);
						}

						$sqlLicenseState="SELECT * from prd_license_state where is_deleted='N' AND product_id = :product_id";
						$resLicenseState=$pdo->select($sqlLicenseState,array(":product_id"=>$variation_id));

						$childPrdLicenseState = array();
						if(!empty($resLicenseState)){
							foreach ($resLicenseState as $key => $value) {
								$childPrdLicenseState[$value['sale_type']."_".$value['state_id']] = $value['id'];
							}
						}

						$licenseStateResult=array_diff_key($prdLicenseState,$childPrdLicenseState);
						if(!empty($licenseStateResult)){
							foreach ($licenseStateResult as $key => $value) {
								$ins_params = array(
									"product_id"=>$variation_id,
									"license_rule"=>$prdLicenseStateList[$key]['license_rule'],
									"sale_type"=>$prdLicenseStateList[$key]['sale_type'],
									"state_id"=>$prdLicenseStateList[$key]['state_id'],
									"state_name"=>$prdLicenseStateList[$key]['state_name'],
							    );
							    $pdo->insert("prd_license_state", $ins_params);

							    $this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Added '.$prdLicenseStateList[$key]['sale_type'].' State '.$prdLicenseStateList[$key]['state_name'],'Admin Updated Product','Agent Requirements');
							}
						}

						$licenseStateResult=array_diff_key($childPrdLicenseState,$prdLicenseState);
						if(!empty($licenseStateResult)){
							foreach ($licenseStateResult as $key => $value) {
								$keyDiff = explode("_", $key);
								$sale_type = $keyDiff[0];
								$state_id = $keyDiff[1];
								$state_name=$allStateRes[$state_id]['name'];
								$updParams=array(
									'is_deleted'=>'Y'
								);
								$updWhr=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$value,
									)
								);
								$pdo->update("prd_license_state",$updParams,$updWhr);

								$this->prdActtivityFeed($variation_id,$product_id,$variationProductCode[$variation_id],'Removed '.$sale_type.' State '.$state_name,'Admin Updated Product','Agent Requirements');
							}
						}
					}
				}
				if($step == 4){
					if(in_array("MemberPaymentSubscriptionType",$matchGlobalArr)){
						$insParams=array(
							'payment_type'=>$payment_type,
							'payment_type_subscription'=>$payment_type_subscription,
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity=$pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Member Payment Subscription Type',$variationProductCode[$variation_id]);
						}
					}
					
				}

				if(in_array("Settings",$matchGlobalArr) &&
				in_array("EnrollmentPage",$matchGlobalArr) &&
				in_array("MemberPortalInformation",$matchGlobalArr) &&
				in_array("LimitationAndExclusions",$matchGlobalArr) &&
				in_array("EffecttiveDate",$matchGlobalArr) &&
				in_array("MembershipRequirement",$matchGlobalArr) &&
				in_array("Availability",$matchGlobalArr) &&
				in_array("CoverageOptions",$matchGlobalArr) &&
				in_array("SubProducts",$matchGlobalArr) &&
				in_array("ProductCombinationRules",$matchGlobalArr) &&
				in_array("TerminationRules",$matchGlobalArr) &&
				in_array("AgeRestrictions",$matchGlobalArr) &&
				in_array("MemberEnrollmentInformation",$matchGlobalArr) &&
				in_array("BeneficiaryInformation",$matchGlobalArr) &&
				in_array("EnrollmentVerification",$matchGlobalArr) &&
				in_array("AgentRequirements",$matchGlobalArr) &&
				in_array("MemberPaymentSubscriptionType",$matchGlobalArr)){
					
					$sqlChildPrdValidation="SELECT id FROM prd_product_builder_validation where product_id=:product_id";
					$resChildPrdValidation=$pdo->selectOne($sqlChildPrdValidation,array(":product_id"=>$variation_id));

					if(!empty($resPrdValidation) && empty($resChildPrdValidation)){
						$updParams=array(
							'product_id'=>$variation_id,
							'errorJson'=>$resPrdValidation['errorJson']
						);
						$pdo->insert("prd_product_builder_validation",$updParams);
					}
					if(!empty($resPrdValidation) && !empty($resChildPrdValidation)){
						$updParams=array(
							'errorJson'=>$resPrdValidation['errorJson']
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(
								":id"=>$resChildPrdValidation['id'],
							)
						);
						$pdo->update("prd_product_builder_validation",$updParams,$updWhere);
					}

					$errorJson = json_decode($resPrdValidation['errorJson'],true);

					if(!empty($errorJson)){
						$insParams=array(
							'status'=>'Pending',
						);
						$updWhere=array(
							'clause'=>'id=:id',
							'params'=>array(":id"=>$variation_id)
						);
						$activity = $pdo->update("prd_main",$insParams,$updWhere,true);

						if(!empty($activity)){
							$this->prdUpdActtivityFeed($variation_id,$product_id,$activity,$insParams,'Status',$variationProductCode[$variation_id]);
						}
					}
				}

			}
		}
		
	}

	public function prdActtivityFeed($product_id,$parent_product_id,$product_code,$message,$title,$sectionName=''){
		global $pdo,$ADMIN_HOST;
		$extraLink='';
		if(!empty($parent_product_id)){
			$extraLink='&parentProduct='.md5($parent_product_id);
		}

		$description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>$message,
			'ac_red_2'=>array(
					'href'=>$ADMIN_HOST.'/product_builder.php?product='.md5($product_id).$extraLink,
					'title'=>$product_code,
			),
		); 
		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $product_id, 'product',$title, $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'','prd_'.$sectionName);	
	}
	public function prdUpdActtivityFeed($product_id,$parent_product_id,$oldVal,$newVal,$sectionName,$product_code,$is_description=false,$extra_message=''){
		global $pdo,$ADMIN_HOST;

		$extraLink='';
		if(!empty($parent_product_id)){
			$extraLink='&parentProduct='.md5($parent_product_id);
		}
		$activityFeedDesc['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Updated '.$sectionName.' '.$extra_message.' on product',
		); 

		
		foreach ($oldVal as $actKey => $actValue) {
			if(empty($actValue)){
				$actValue="-";
			}
			if(empty($newVal[$actKey])){
				$newVal[$actKey]="-";
			}
			if($actKey=='company_id'){
				if(!empty($actValue) && $actValue != "-"){
					$actValue = getname('prd_company',$actValue,'company_name','id');
				}else{
					$actValue = "-";
				}
				if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
					$newVal[$actKey] = getname('prd_company',$newVal[$actKey],'company_name','id');
				}else{
					$newVal[$actKey] = "-";
				}
			}else if($actKey=="category_id"){
				if(!empty($actValue) && $actValue != "-"){
					$actValue = getname('prd_category',$actValue,'title','id');
				}else{
					$actValue = "-";
				}
				if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
					$newVal[$actKey] = getname('prd_category',$newVal[$actKey],'title','id');
				}else{
					$newVal[$actKey] = "-";
				}
			}else if($actKey=="carrier_id"){
				if(!empty($actValue) && $actValue != "-"){
					$actValue = getname('prd_fees',$actValue,'name','id');
				}else{
					$actValue = "-";
				}
				if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
					$newVal[$actKey] = getname('prd_fees',$newVal[$actKey],'name','id');
				}else{
					$newVal[$actKey] = "-";
				}
			}else if($actKey=="admin_id"){
				if(!empty($actValue) && $actValue != "-"){
					$actValue = getname('admin',$actValue,'display_id','id');
				}else{
					$actValue = "-";
				}
				if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
					$newVal[$actKey] = getname('admin',$newVal[$actKey],'display_id','id');
				}else{
					$newVal[$actKey] = "-";
				}
			}else if($actKey=="rider_product" || $actKey == "rider_product_id"){
				if(!empty($actValue) && $actValue != "-"){
					$actValue = getname('prd_main',$actValue,'product_code','id');
				}else{
					$actValue = "-";
				}
				if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
					$newVal[$actKey] = getname('prd_main',$newVal[$actKey],'product_code','id');
				}else{
					$newVal[$actKey] = "-";
				}
			}else if($actKey=="membership_ids"){

				if(!empty($actValue) && $actValue != "-"){
					$sql1=$pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(NAME,'(',display_id,')')) AS membership FROM prd_fees where id in ($actValue)");
					$actValue = !empty($sql1['membership']) ? $sql1['membership'] : '-';
				}else{
					$actValue = "-";
				}
				if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
					$sql2=$pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(NAME,'(',display_id,')')) AS membership FROM prd_fees where id in ($newVal[$actKey])");
					$newVal[$actKey] = !empty($sql2['membership']) ? $sql2['membership'] : '-';
				}else{
					$newVal[$actKey] = "-";
				}

			}
			if($actValue=='N'){
				$actValue = 'No';
			}
			if($actValue=='Y'){
				$actValue = 'Yes';
			}
			if($newVal[$actKey] == 'N'){
				$newVal[$actKey] = 'No';
			}
			if($newVal[$actKey] == 'Y'){
				$newVal[$actKey] = 'Yes';
			}
			if($is_description){
				$activityFeedDesc['ac_description_link']=array(
					'From'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Description','data-desc'=>$actValue),
					'To'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Description','data-desc'=>$newVal[$actKey]),
				); 
			}else{
				$activity_text = " From ".$actValue." To ".$newVal[$actKey];
				$activityFeedDesc['key_value']['desc_arr'][str_replace('_',' ',$actKey)] = $activity_text;

			}
		}
		
		$activityFeedDesc['ac_message']['ac_red_2']=array(
			'href'=>$ADMIN_HOST.'/product_builder.php?product='.md5($product_id).$extraLink,
			'title'=>$product_code,
		); 

		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $product_id, 'product','Admin Updated Product', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc),'','prd_'.$sectionName);
	}

	public function generalActivityFeed($oldVal,$newVal,$link,$displayId,$tblId,$tblName,$heading,$message,$extra_params=array()){
		global $pdo,$ADMIN_HOST;

		$activityFeedDesc['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>$message,
		); 

		$descCnt = 1;

		if(!empty($oldVal)){
			foreach ($oldVal as $actKey => $actValue) {
				if(empty($actValue)){
					$actValue="Blank";
				}
				if(empty($newVal[$actKey])){
					$newVal[$actKey]="Blank";
				}
				if($actKey=='company_id'){
					if(!empty($actValue) && $actValue != "-"){
						$actValue = getname('company',$actValue,'company_name','id');
					}else{
						$actValue = "-";
					}
					if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
						$newVal[$actKey] = getname('company',$newVal[$actKey],'company_name','id');
					}else{
						$newVal[$actKey] = "-";
					}
				}
				if($actValue=='N'){
					$actValue = 'No';
				}
				if($actValue=='Y'){
					$actValue = 'Yes';
				}
				if($newVal[$actKey] == 'N'){
					$newVal[$actKey] = 'No';
				}
				if($newVal[$actKey] == 'Y'){
					$newVal[$actKey] = 'Yes';
				}

				if($actKey == 'display_desc_1'){
					$title = "Email Content";
				}else if($actKey == 'display_desc_2'){
					$title = "SMS Content";
				}else if($actKey == 'display_desc_3'){
					$title = "Image";
				}else{
					$title = "Content";
				}

				if(preg_match('/display_desc_\d/',$actKey) || $actKey == 'display_desc'){
					$activityFeedDesc['ac_description_link_'.$descCnt]=array(
						'From'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>$title,'data-desc'=>$actValue),
						'To'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>$title,'data-desc'=>$newVal[$actKey]),
					); 
					$descCnt++;
				}else{
					$activity_text = " From ".$actValue." To ".$newVal[$actKey];
					$activityFeedDesc['key_value']['desc_arr'][$actKey] = $activity_text;

				}
			}
		}

		$activityFeedDesc['ac_message']['ac_red_2']=array(
			'title'=>$displayId,
		); 
		if(!empty($link)){
			$activityFeedDesc['ac_message']['ac_red_2']['href']=$link;
		}

		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $tblId, $tblName,$heading, $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
	}

	public function generalActivityFeedAgent($oldVal,$newVal,$link,$displayId,$tblId,$tblName,$heading,$message,$extra_params=array()){
		global $pdo,$ADMIN_HOST;

		$activityFeedDesc['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
				'title'=>$_SESSION['agents']['rep_id'],
			),
			'ac_message_1' =>$message,
		); 

		$descCnt = 1;

		if(!empty($oldVal)){
			foreach ($oldVal as $actKey => $actValue) {
				if(empty($actValue)){
					$actValue="Blank";
				}
				if(empty($newVal[$actKey])){
					$newVal[$actKey]="Blank";
				}
				if($actKey=='company_id'){
					if(!empty($actValue) && $actValue != "-"){
						$actValue = getname('company',$actValue,'company_name','id');
					}else{
						$actValue = "-";
					}
					if(!empty($newVal[$actKey]) && $newVal[$actKey] != "-"){
						$newVal[$actKey] = getname('company',$newVal[$actKey],'company_name','id');
					}else{
						$newVal[$actKey] = "-";
					}
				}
				if($actValue=='N'){
					$actValue = 'No';
				}
				if($actValue=='Y'){
					$actValue = 'Yes';
				}
				if($newVal[$actKey] == 'N'){
					$newVal[$actKey] = 'No';
				}
				if($newVal[$actKey] == 'Y'){
					$newVal[$actKey] = 'Yes';
				}

				if($actKey == 'display_desc_1'){
					$title = "Email Content";
				}else if($actKey == 'display_desc_2'){
					$title = "SMS Content";
				}else if($actKey == 'display_desc_3'){
					$title = "Image";
				}else{
					$title = "Content";
				}

				if(preg_match('/display_desc_\d/',$actKey) || $actKey == 'display_desc'){
					$activityFeedDesc['ac_description_link_'.$descCnt]=array(
						'From'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>$title,'data-desc'=>$actValue),
						'To'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>$title,'data-desc'=>$newVal[$actKey]),
					); 
					$descCnt++;
				}else{
					$activity_text = " From ".$actValue." To ".$newVal[$actKey];
					$activityFeedDesc['key_value']['desc_arr'][$actKey] = $activity_text;

				}
			}
		}

		$activityFeedDesc['ac_message']['ac_red_2']=array(
			'title'=>$displayId,
		); 
		if(!empty($link)){
			$activityFeedDesc['ac_message']['ac_red_2']['href']=$link;
		}

		activity_feed(3,$_SESSION['agents']['id'],'Agent',$tblId,$tblName,$heading,"","",json_encode($activityFeedDesc));
	}


	public function get_agent_level_range($agent_id){
		global $pdo;
		$agent_level_params = array();
		$agent_level_incr = "";

		$agentSql="SELECT c.id,c.sponsor_id,cs.agent_coded_id,cs.agent_coded_level FROM customer c LEFT JOIN customer_settings cs ON (cs.customer_id=c.id) WHERE md5(c.id)=:id";
		$agentRes=$pdo->selectOne($agentSql,array(":id"=>$agent_id));
		$downlineSql="SELECT cs.agent_coded_id FROM customer c JOIN customer_settings cs ON(cs.customer_id=c.id)  WHERE md5(c.sponsor_id)=:id AND c.type = 'Agent' AND c.is_deleted='N' ORDER BY cs.agent_coded_id DESC";
		$downlineRes=$pdo->selectOne($downlineSql,array(":id"=>$agent_id));
		if($downlineRes){
			$downline_level=$downlineRes['agent_coded_id'];
			$agent_level_incr .= " AND id >= :downline_level";

			$agent_level_params[':downline_level'] = $downline_level;
		}
		$uplineSql = "SELECT cs.agent_coded_id FROM customer c JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE c.type = 'Agent' AND c.id=:id";
		$uplineRes = $pdo->selectOne($uplineSql,array(":id"=>$agentRes['sponsor_id']));
		if($uplineRes){
			$upline_level=$uplineRes['agent_coded_id'];
			if($agentRes['sponsor_id'] == 1){
				$agent_level_incr .= " AND id < :upline_level";
			}else{
				$agent_level_incr .= " AND id <= :upline_level";
			}
			$agent_level_params[':upline_level'] = $upline_level;
		}
		$your_level=$agentRes['agent_coded_id'];
		$agent_level_params[':profile_id'] = '1';
	
		$agentRangeSql="SELECT * FROM agent_coded_level WHERE profile_id = :profile_id $agent_level_incr AND is_active='Y' order by id desc";
		$agentRangeRes=$pdo->select($agentRangeSql,$agent_level_params);
		return $agentRangeRes;
	}

	public function generateConnectionID() {
		global $pdo;
		$sql = "SELECT connection_id FROM prd_connected_products where connection_id > 0 order by id desc";
		$res = $pdo->selectOne($sql);
		if (!empty($res)) {
			return $res['connection_id']+1;
		} else {
			return 1;
		}
	}

	public function updateAgentDownline($agentId){
  		global $pdo,$ADMIN_HOST;

  		// select updated agent
  		$selUpdAgent = "SELECT id,level,sponsor_id,upline_sponsors,rep_id FROM customer WHERE type='Agent' AND id=:agentId";
      	$resUpdAgent = $pdo->selectOne($selUpdAgent,array(":agentId" => $agentId));

      	// get downline agents
  		$downlineSql = "SELECT a.id,a.level,a.sponsor_id,a.upline_sponsors,a.rep_id as agentDispId,s.rep_id as sponsorDispId
  						FROM customer a
  						JOIN customer s ON(a.sponsor_id=s.id AND s.type='Agent')
  						WHERE a.type='Agent' AND a.sponsor_id=:id AND a.is_deleted='N'";
  		$downlineRes = $pdo->select($downlineSql, array(":id" => $agentId));

  		if(!empty($downlineRes) && !empty($resUpdAgent['id'])){

      		$agentLevel = $resUpdAgent['level'] + 1;
      		$agentUplineSponsor = $resUpdAgent['upline_sponsors'] . $resUpdAgent['id'] . ',';

  			foreach ($downlineRes as $downlineAgent){
  				// Update agent level and sponsors
  				$updParams = array(
			        "level" => $agentLevel,
			        "upline_sponsors" => $agentUplineSponsor,
			    );
		      	$updWhere = array(
			        'clause' => 'id = :id',
			        'params' => array(':id' => $downlineAgent['id']),
		      	);
			    $pdo->update('customer', $updParams, $updWhere);

			    // Link Agent to Their Agency
			    $agencyId = $this->getAgencyId($downlineAgent['id']);
				$customer_settings = array("agency_id" => $agencyId);
				$this->addCustomerSettings($customer_settings,$downlineAgent['id']);

				// update Agent downline groups and group members
				$this->updateDownlineGroup($downlineAgent['id']);

				// update Agent downline members
        		$this->updateDownlineMember($downlineAgent['id']);

				// Update downline agents of Agent
			    $this->updateAgentDownline($downlineAgent['id']);
  			}
  		}
	}

	function get_advance_commission_rules_agents($agent_id, $product_id = array()) {
		global $pdo;
		$rules_ids = array();
		return $rules_ids;
		// $agent_coded_level = getname("customer_settings", $agent_id, "agent_coded_level", "customer_id");
		// $sponsor_id = getname("customer", $agent_id, "sponsor_id", "id");
		// if($agent_coded_level == "LOA") {
		// 	$agent_id = $sponsor_id;
		// }
		// 	$rule_id = 0;
	
		// 	/*-------- Global Rule ------------*/
		// 	$global_rule_sql = "SELECT id,charged_to FROM advance_commission ac WHERE ac.is_deleted='N' AND ac.agent_id=0 AND ac.type='Global'";
		// 	$global_rule_row = $pdo->select($global_rule_sql);
		// 	$i=0;
		// 	if(!empty($global_rule_row)) {
		// 		foreach ($global_rule_row as $rule) {
		// 			$rules_ids[$i]['id'] = $rule['id'];
		// 			$rules_ids[$i]['charged_to'] = $rule['charged_to'];
		// 			$i++;
		// 		}
		// 	}

		// 	/*-------- Personal Rule ------------*/
		// 	$spec_rule = "SELECT id,charged_to FROM advance_commission ac WHERE ac.is_deleted='N' AND ac.agent_id=:agent_id AND ac.type='Variation'";
		// 	$spec_rule_row = $pdo->select($spec_rule,array(":agent_id"=>$agent_id));
			
		// 	if(!empty($spec_rule_row)) {
		// 		foreach ($spec_rule_row as $rule) {
		// 			$rules_ids[$i]['id'] = $rule['id'];
		// 			$rules_ids[$i]['charged_to'] = $rule['charged_to'];
		// 			$i++;
		// 		}
		// 	}
		// 	return $rules_ids;
	
	}

	public function generateHealthyStepDisplayID() {
		global $pdo;
		$rule_code = rand(100000, 999999);
		
		$sql = "SELECT count(id) as total FROM prd_main WHERE product_code ='H" . $rule_code . "' OR product_code ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateHealthyStepDisplayID();
		} else {
			return 'H'.$rule_code;
		}
	}

	public function generateHealthyStepVariationDisplayID($fees=false) {
		global $pdo;
		$rule_code = rand(100000, 999999);
		
		$tbl = 'prd_main';
		$incr = 'product_code';
		if($fees){
			$tbl = 'prd_fees';
			$incr = 'display_id';
		}
		$sql = "SELECT count(id) as total FROM $tbl WHERE $incr ='HV" . $rule_code . "' OR $incr ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateHealthyStepVariationDisplayID($fees);
		} else {
			return 'HV'.$rule_code;
		}
	}

	public function generateHealthyStepDisplayIDFees() {
		global $pdo;
		$rule_code = rand(100000, 999999);
		
		$sql = "SELECT count(id) as total FROM prd_fees WHERE display_id ='H" . $rule_code . "' OR display_id ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateHealthyStepDisplayIDFees();
		} else {
			return 'H'.$rule_code;
		}
	}

	public function generateHealthyStepFeesName($fees = false) {
		global $pdo;
		$rule_code = rand(100000, 999999);
		
		$tbl = 'prd_main';
		if($fees){
			$tbl = 'prd_fees';
		}

		$sql = "SELECT count(id) as total FROM $tbl WHERE name ='Healthy step variation " . $rule_code . "' OR name ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateHealthyStepFeesName($fees);
		} else {
			return 'Healthy step variation '.$rule_code;
		}
	}

	public function get_agent_merchant_detail($matrix_id, $agent_id, $payment_mode,$extra_params =array()){
		global $pdo;
		$incr = '';
		$prd_incr = '';
		$having_incr = '';
		$agent_incr = '';
		$table_incr = '';
		$agent_table_incr = '';
		$processor = $sch_params = array();
		$payment_response = 0;
		if(!empty($matrix_id)){
			if(!empty($extra_params['is_renewal'])) {
				if($extra_params['is_renewal'] === true) {
					$extra_params['is_renewal'] = "Y";
				}
				if($extra_params['is_renewal'] === false) {
					$extra_params['is_renewal'] = "N";
				}
			}
			if(!empty($extra_params['customer_id']) && !empty($extra_params['is_renewal']) && ($extra_params['is_renewal'] == 'Y' || $extra_params['is_renewal'] == 'L')){
				$sql = "SELECT p.id
						FROM transactions t
						JOIN payment_master p ON(p.id = t.payment_master_id AND p.is_deleted = 'N')
						WHERE 
						t.is_deleted = 'N' AND
						p.status IN ('Active','Inactive') AND
						((t.payment_type='CC' AND p.is_cc_accepted='Y') OR (t.payment_type='ACH' AND p.is_ach_accepted='Y')) AND
						t.transaction_status IN('Payment Approved','Pending Settlement') AND
						t.customer_id=:customer_id AND
						t.payment_type=:payment_type
						ORDER BY t.id DESC
						LIMIT 1";
				$where = array(
					':customer_id' => $extra_params['customer_id'],
					':payment_type' => $payment_mode
				);
				$row = $pdo->selectOne($sql,$where);
				if(!empty($row['id'])) {
					return $row['id'];
				}
			}

			if(is_array($matrix_id)){
				$matrix_id = implode(',', $matrix_id);
			}
			$product_detail_res = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(IF(p.parent_product_id > 0,p.parent_product_id,p.id))) as product_id,COUNT(DISTINCT(p.id)) as product_counts FROM  prd_matrix as pm JOIN prd_main as p ON (p.id = pm.product_id) WHERE pm.id IN (" . $matrix_id . ") and p.type!='Fees' ORDER BY p.id ASC ");
			
			if($payment_mode == 'CC'){
				$incr .= " AND p.is_cc_accepted = 'Y'";
			}else{
				$incr .= " AND p.is_ach_accepted = 'Y'";
			}
			
			// if(!empty($extra_params['is_renewal']) && ($extra_params['is_renewal'] == 'Y' || $extra_params['is_renewal'] == 'L')){
			// 	$incr .= " AND p.status IN ('Active','Inactive')";
			// }else{
				$incr .= " AND p.status IN ('Active')";
			// }
			
			if(!empty($product_detail_res['product_id'])){
				$prd_incr = " AND pap.product_id IN(".$product_detail_res['product_id'].")";
				$having_incr = " HAVING COUNT(pap.product_id)=".$product_detail_res['product_counts'];
				$table_incr .= " LEFT JOIN payment_master_assigned_product pap ON(pap.payment_master_id = p.id and pap.is_deleted='N') ";
			}	
			
			if(!empty($agent_id)){
				$table_incr .= " JOIN payment_master_assigned_agent pmaa ON(pmaa.payment_master_id = p.id and pmaa.is_deleted='N' AND pmaa.agent_id = :agent_id) ";

				$sch_params[':agent_id'] = $agent_id;

				if($payment_mode == 'CC'){
					if(isset($extra_params['allow_inactive']) && $extra_params['allow_inactive'] == true) {
						$agent_incr .= " AND pmaa.status IN('Active','Inactive')";
					} else {
						$agent_incr .= " AND pmaa.status='Active'";
					}
				}else{
					if(isset($extra_params['allow_inactive']) && $extra_params['allow_inactive'] == true) {
						$agent_incr .= " AND ( 
							IF(
								p.is_assigned_to_all_product='Y',
								pmaa.global_accept_ach_status IN('Active','Inactive'),
								pmaa.global_accept_ach_status IN('Active','Inactive') AND pmaa.status IN('Active','Inactive')
							)
						) ";
					} else {
						$agent_incr .= " AND ( 
							IF(
								p.is_assigned_to_all_product='Y',
								pmaa.global_accept_ach_status='Active',
								pmaa.global_accept_ach_status='Active' AND pmaa.status='Active'
							)
						) ";
					}
				}
			}
			// Limited product and Limited Agent start
			$payment_master_res = array();			
			$payment_master_sql = "SELECT p.id, p.order_by, p.type 
				FROM payment_master p
				$table_incr 
				WHERE p.is_assigned_to_all_agent = 'N' AND p.is_assigned_to_all_product = 'N' AND type='Variation' 
				AND p.is_deleted = 'N' 
				$prd_incr $agent_incr $incr
				$having_incr
				ORDER BY order_by ASC";
			$payment_master_res = $pdo->selectOne($payment_master_sql,$sch_params);

			if(!empty($payment_master_res)){
				$payment_response = $payment_master_res['id'];
			}else{
				// Limited product and All Agent start
				$payment_master_res = array();
				$payment_master_sql = "SELECT p.id, p.order_by, p.type 
				FROM payment_master p 
				$table_incr
				WHERE p.is_assigned_to_all_agent = 'Y' AND type='Variation' 
				AND p.is_assigned_to_all_product = 'N' AND p.is_deleted = 'N'
				$prd_incr $agent_incr $incr
				$having_incr
				ORDER BY order_by ASC";				
				$payment_master_res = $pdo->selectOne($payment_master_sql,$sch_params);
				if(!empty($payment_master_res)){
					$payment_response = $payment_master_res['id'];
				}else{
					// All product and Limited Agent start
					unset($sch_params[":payment_id"]);
					$payment_master_res = array();
					$payment_master_sql = "SELECT p.id, p.order_by, type 
					FROM payment_master p 
					$table_incr
					WHERE p.is_assigned_to_all_agent = 'N' AND p.is_assigned_to_all_product = 'Y' 
					AND p.is_deleted = 'N' AND p.type='Variation' 
					 $agent_incr $incr ORDER BY order_by ASC";
					$payment_master_res = $pdo->selectOne($payment_master_sql,$sch_params);
					if(!empty($payment_master_res)){
						$payment_response = $payment_master_res['id'];
					}else{
						// All product and All Agent start (variations)
						$payment_master_res = array();
						$payment_master_sql = "SELECT p.id, p.order_by, p.type 
						FROM payment_master p 
						$table_incr
						WHERE p.is_assigned_to_all_agent = 'Y' AND p.is_assigned_to_all_product = 'Y' 
						AND p.is_deleted = 'N' AND p.type='Variation' 
						 $agent_incr $incr ORDER BY order_by ASC";
						$payment_master_res = $pdo->selectOne($payment_master_sql,$sch_params);

						if(!empty($payment_master_res)){
							$payment_response = $payment_master_res['id'];
						}else{
							// get default processor start (Active) (OP29-325)
							$payment_master_res = array();

							$incr = "";
							if($payment_mode == 'CC'){
								$incr .= " AND p.is_cc_accepted = 'Y'";
							}else{
								$incr .= " AND p.is_ach_accepted = 'Y'";
							}             
							$incr .= " AND p.status IN ('Active','Inactive')";

							$default_incr = '';
							if($payment_mode == 'CC'){
								$default_incr .= " AND p.is_default_for_cc = 'Y'";
							} else {
								$default_incr .= " AND p.is_default_for_ach = 'Y'";
							}
							$payment_master_sql = "SELECT p.id, p.order_by, p.type 
							FROM payment_master p
							$table_incr
							WHERE p.is_assigned_to_all_agent = 'Y' AND p.is_assigned_to_all_product = 'Y' 
							AND p.is_deleted = 'N' AND p.type='Global' 
							 $default_incr $agent_incr $incr ORDER BY order_by ASC";
							$payment_master_res = $pdo->selectOne($payment_master_sql,$sch_params);
							if(!empty($payment_master_res)){
								$payment_response = $payment_master_res['id'];
							}else{
								//  global standby 1 if active (OP29-325)
								$payment_master_res = array();
								$payment_master_sql = "SELECT p.id, p.order_by, p.type 
								FROM payment_master p
								$table_incr
								WHERE p.is_assigned_to_all_agent = 'Y' AND p.is_assigned_to_all_product = 'Y' 
								AND p.is_default_for_cc = 'N' AND p.is_default_for_ach = 'N'
								AND p.is_deleted = 'N' AND p.type='Global'  
								 $agent_incr $incr ORDER BY order_by ASC";
								$payment_master_res = $pdo->selectOne($payment_master_sql,$sch_params);

								if(!empty($payment_master_res)){
									$payment_response = $payment_master_res['id'];
								}else{
									//  global standby of others (OP29-325)
									$sincr = "";
									$sincr .= " AND p.status IN ('Active','Inactive')";
									if($payment_mode == 'CC'){
										$sincr .= " AND p.is_cc_accepted = 'Y'";
									}else{
										$sincr .= " AND p.is_ach_accepted = 'Y'";
									}

									$payment_master_res = array();
									$payment_master_sql = "SELECT p.id, p.order_by, p.type 
									FROM payment_master p
									$table_incr
									WHERE p.is_assigned_to_all_agent = 'Y' AND p.is_assigned_to_all_product = 'Y' 
									AND p.is_default_for_cc = 'N' AND p.is_default_for_ach = 'N'
									AND p.is_deleted = 'N' AND p.type='Global'  
									 $agent_incr $sincr ORDER BY order_by ASC";
									$payment_master_res = $pdo->selectOne($payment_master_sql,$sch_params);

									if(!empty($payment_master_res)){
										$payment_response = $payment_master_res['id'];
									}
								}
							}
						}
					}
				}
			}	
		}	
		if($payment_response == 0 && !isset($extra_params['allow_inactive'])) {
			$extra_params['allow_inactive'] = true;
			$payment_response = $this->get_agent_merchant_detail($matrix_id,$agent_id,$payment_mode,$extra_params);
		}
		return $payment_response;
	}

	public function get_website_id() {
		global $pdo;
		$web_id = "W" . rand(1000000, 9999999);
		$sql = "SELECT website_id FROM website_subscriptions WHERE website_id ='" . $web_id . "'";
		$res = $pdo->select($sql);
		if (count($res) > 0) {
			return $this->get_website_id();
		} else {
			return $web_id;
		}
	}

	public function get_sub_product($product_id){
		global $pdo;
	
		$sqlSubProduct="SELECT group_concat(sub_product_id) as sub_products FROM prd_sub_products where is_deleted='N' AND product_id = :product_id";
		$resSubProduct=$pdo->selectOne($sqlSubProduct,array(":product_id"=>$product_id));
	
		$sub_products = '';
		if($resSubProduct){
			if(!empty($resSubProduct['sub_products'])){
				$sub_products = $resSubProduct['sub_products'];
			}
		}
		return $sub_products;
	}

	//  insert member terms agreement for new product plan code start
	public function insert_member_terms($customer_id,$order_id,$extra = array()){
        global $pdo,$S3_KEY,$S3_SECRET,$S3_REGION,$S3_BUCKET_NAME;
		$dependents_details = array();
		$product_ids = array();
  		$agreement_id = 0;
  		$ws_ids = array();
		$REAL_IP_ADDRESS = get_real_ipaddress();
  		$website_subscription_ids = !empty($extra['websiteSubscriptionArr']) ? $extra['websiteSubscriptionArr'] : array();
		//  Customer Details Code Start
	 
		$cust_sql = "SELECT c.id,fname,lname,email,type,country_id,birth_date,country_name,cell_phone,gender,address,address_2,city,state,zip,ip_address,rep_id,display_id,sponsor_id,level,upline_sponsors,status,cs.signature_file,created_at FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE  c.id=:customer_id";
		$customer_res = $pdo->selectOne($cust_sql,array(":customer_id" => $customer_id));  
		$customerInfo = array(
				'id' => $customer_res['id'],
				'fname' => $customer_res['fname'],
				'lname' => $customer_res['lname'],
				'email' => $customer_res['email'],
				'type' => $customer_res['type'],
				'country_id' => $customer_res['country_id'],
				'birth_date'=>$customer_res['birth_date'],
				'country_name' => $customer_res['country_name'],
				'cell_phone' => $customer_res['cell_phone'],
				'gender' =>$customer_res['gender'],
				'address' => $customer_res['address'],
				'address_2' => $customer_res['address_2'],
				'city' => $customer_res['city'],
				'state' => $customer_res['state'],
				'zip' => $customer_res['zip'],
				'ip_address' => $customer_res['ip_address'],
				'rep_id' => $customer_res['rep_id'],
				'display_id' => $customer_res['display_id'],
				'sponsor_id' => $customer_res['sponsor_id'],
				'level' => $customer_res['level'],
				'upline_sponsors' => $customer_res['upline_sponsors'],
				'status' => $customer_res['status'],
				"signature_file" => $customer_res['signature_file'],
				"created_at" => $customer_res['created_at'],
			);
  
		 //  Customer Details Code Ends
		 
		 //member terms agreement fetch 
         $member_terms_res = $pdo->selectOne("SELECT id FROM `member_terms` WHERE is_default='Y'");
		 $member_terms_id = $member_terms_res['id'];
		 
		 // Terms and Conditions Code start 
		 $website_id = $prd_plan_type_id = 0;
  		if(!empty($extra['website_id']) && !empty($extra['action']) && $extra['action'] == "policy_updated") {
  			$ws_row = $pdo->selectOne("SELECT id,customer_id,product_id,plan_id,prd_plan_type_id FROM website_subscriptions WHERE id=:id",array(":id"=>$extra['website_id']));
  			$product_ids[] =$ws_row['product_id'];
  			$prd_plan_type_id = $ws_row['prd_plan_type_id'];
  			$website_id = $ws_row['id'];

  			$product_id = implode(',',$product_ids);
			$terms_conditions_content = $this->get_terms_conditions_content($product_id,$customer_id);

			// Dependent code start
			$dependent_sql = "SELECT cd.*,pm.name as product_name, pm.product_code as product_code
			  FROM customer_dependent as cd
			  LEFT JOIN prd_main as pm ON(pm.id = cd.product_id)
			  WHERE cd.website_id = :website_id GROUP BY cd.display_id";
	  
		  	$dependet_where = array(":website_id" => $extra['website_id']);
	  
		   	$dependent_res = $pdo->select($dependent_sql,$dependet_where);  

			if(count($dependent_res) > 0){
				foreach($dependent_res as $dependents){
					$dependent_params = array(
						'id' => $dependents['id'],
						'display_id' => $dependents['display_id'],
						'customer_id' => $dependents['customer_id'],
						'relation' => $dependents['relation'],
						'fname' => $dependents['fname'],
						'mname' => $dependents['mname'],
						'lname' => $dependents['lname'],
						'email' => $dependents['email'],
						'phone' => $dependents['phone'],
						'birth_date' => date('Y-m-d', strtotime($dependents['birth_date'])),
						'gender' => $dependents['gender'],
						);
					$dependents_details[] = $dependent_params;
				} 
			}
			// Dependent code ends

			// Customer Billing Details Code Start
			$customer_billing_sql = "SELECT * FROM customer_billing_profile WHERE customer_id=:c_id and is_deleted='N'";
			$customer_billing_res = $pdo->selectOne($customer_billing_sql,array(":c_id" => $customer_id)); 
			// Customer Billing Details Code Ends

			$primary_details = array();
			$primary_details = $customerInfo;
				  
			$billing_details = array();
			$billing_details = $customer_billing_res;
	  		$file_name = "";
	  		
	        if($terms_conditions_content){
	            $s3Client = new S3Client([
	                'version' => 'latest',
	                'region'  => $S3_REGION,
	                'credentials'=>array(
	                    'key'=> $S3_KEY,
	                    'secret'=> $S3_SECRET
	                )
	            ]);
	            $s3Client->registerStreamWrapper();

	            $file_name = $customer_res['fname'] . $customer_res['lname'] . date('mdY') . time() . '.txt';
	            $file_name = str_replace(" ", "", $file_name);

	            $result = $s3Client->putObject(array(
	                'Bucket' => $S3_BUCKET_NAME,
	                'Key'    => $file_name,
	                'Body'   => $terms_conditions_content
	            ));

	        }

	        $terms_params = array(
				'customer_id' => $customer_id,
				'order_id' => $order_id,
				'primary_details' => json_encode($primary_details),
				'dependent_details' => json_encode($dependents_details),
				'billing_details' => json_encode($billing_details),
				'agreement' => '',
				'member_terms_id' => $member_terms_id,
                'date_of_signature'=> 'msqlfunc_NOW()',
				'agreement_file' => $file_name,
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'created_at' => 'msqlfunc_NOW()',
				'signature_img' => '',
				'extra' => json_encode($extra),
			);

			$agreement_id = $pdo->insert('member_terms_agreement',$terms_params);

			$ws_where = array("clause" => "id=:id", "params" => array(":id" => $extra['website_id']));
			$pdo->update("website_subscriptions",array('agreement_id' => $agreement_id), $ws_where);

			$ws_ids[] = $extra['website_id'];
  		} else {
			$order_details_res = $pdo->select("SELECT od.product_id
			  FROM order_details as od
			  LEFT JOIN website_subscriptions as ws ON(ws.id=od.website_id  AND  ws.customer_id=:cus_id)
			  WHERE od.order_id = :id AND od.is_deleted='N' GROUP BY od.id", array(":id" => $order_id, ":cus_id" => $customer_id));
			if(empty($order_details_res) && !empty($website_subscription_ids)){
				$order_details_res = $pdo->select("SELECT product_id FROM website_subscriptions WHERE id IN(".implode(',',$website_subscription_ids).")");
			}
			foreach($order_details_res as $order){
				$product_ids[] = $order['product_id'];
			}
			
			$product_id = implode(',',$product_ids);
			$terms_conditions_content = $this->get_terms_conditions_content($product_id,$customer_id);
			// Terms and Conditions Code Ends
	  
			// Dependent code start
			$dependent_sql = "SELECT cd.*,pm.name as product_name, pm.product_code as product_code
			  FROM order_details as od
			  LEFT JOIN prd_main as pm ON(pm.id = od.product_id AND od.product_type='Normal')
			  JOIN customer_dependent as cd ON (od.website_id=cd.website_id)
			  WHERE od.order_id = :id AND od.is_deleted='N' GROUP BY cd.display_id";
	  
		  	$dependet_where = array(":id" => $order_id);
	  
		   	$dependent_res = $pdo->select($dependent_sql,$dependet_where);  

			if(count($dependent_res) > 0){
				foreach($dependent_res as $dependents){
					$dependent_params = array(
						'id' => $dependents['id'],
						'display_id' => $dependents['display_id'],
						'customer_id' => $dependents['customer_id'],
						'relation' => $dependents['relation'],
						'fname' => $dependents['fname'],
						'mname' => $dependents['mname'],
						'lname' => $dependents['lname'],
						'email' => $dependents['email'],
						'phone' => $dependents['phone'],
						'birth_date' => date('Y-m-d', strtotime($dependents['birth_date'])),
						'gender' => $dependents['gender'],
						);
					$dependents_details[] = $dependent_params;
				} 
			}
			// Dependent code ends
			
			// Customer Billing Details Code Start
			$customer_billing_sql = "SELECT * FROM customer_billing_profile WHERE customer_id=:c_id and is_deleted='N'";
			$customer_billing_res = $pdo->selectOne($customer_billing_sql,array(":c_id" => $customer_id)); 
			// Customer Billing Details Code Ends

			$primary_details = array();
			$primary_details = $customerInfo;
				  
			$billing_details = array();
			$billing_details = $customer_billing_res;
	  		$file_name = "";
	  		
	        if($terms_conditions_content){
	            $s3Client = new S3Client([
	                'version' => 'latest',
	                'region'  => $S3_REGION,
	                'credentials'=>array(
	                    'key'=> $S3_KEY,
	                    'secret'=> $S3_SECRET
	                )
	            ]);
	            $s3Client->registerStreamWrapper();

	            $file_name = $customer_res['fname'] . $customer_res['lname'] . date('mdY') . time() . '.txt';
	            $file_name = str_replace(" ", "", $file_name);

	            $result = $s3Client->putObject(array(
	                'Bucket' => $S3_BUCKET_NAME,
	                'Key'    => $file_name,
	                'Body'   => $terms_conditions_content
	            ));

	        }	

			$terms_params = array(
				'customer_id' => $customer_id,
				'order_id' => $order_id,
				'primary_details' => json_encode($primary_details),
				'dependent_details' => json_encode($dependents_details),
				'billing_details' => json_encode($billing_details),
				'agreement' => '',
				'member_terms_id' => $member_terms_id,
                'date_of_signature'=> 'msqlfunc_NOW()',
				'agreement_file' => $file_name,
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'created_at' => 'msqlfunc_NOW()',
			);
	  
			if(!empty($extra['action'])){
			 	if(in_array($extra['action'],array("email_verification","member_signature"))){
					$terms_params['signature_img'] = $customer_res['signature_file'];
			 	}else if(!in_array($extra['action'],array("email_verification","member_signature"))){
			 		$terms_params['extra'] = json_encode($extra);
			 	}
			}
			$agreement_id = $pdo->insert('member_terms_agreement',$terms_params);

			$res_subscription_ids = $pdo->selectOne("SELECT subscription_ids FROM orders where id=:id",array(":id"=>$order_id));
			$subscription_ids = !empty($res_subscription_ids['subscription_ids']) ? explode(",", $res_subscription_ids['subscription_ids']) : $website_subscription_ids;
			if(!empty($subscription_ids)){
				foreach($subscription_ids as $ws_id) {
					$ws_where = array("clause" => "id=:id", "params" => array(":id" => $ws_id));
					$pdo->update("website_subscriptions",array('agreement_id' => $agreement_id), $ws_where);
					$ws_ids[] = $ws_id;
				}	
			}
  		}

  		if(!empty($ws_ids) && !empty($extra['add_activity_feed'])) {
  			$this->add_activity_feed_for_member_terms($ws_ids,$customer_id,$agreement_id);
  		}
  		if(!empty($product_id)){
  			$this->insert_member_terms_custom_question($agreement_id,$customer_id,$product_id,$prd_plan_type_id,$website_id);
  		}
  		return $agreement_id;
	}

	public function update_member_terms($customer_id,$policy_id,$agreement_id,$extra = array()){
		global $pdo;
		$terms_row = $pdo->selectOne("SELECT id,extra FROM member_terms_agreement WHERE id=:agreement_id",array(":agreement_id" => $agreement_id));
		if(!empty($terms_row)) {
			if(!empty($terms_row['extra'])) {
				$extra2 = json_decode($terms_row['extra'],true);
			} else {
				$extra2 = array();
			}
			$extra2['activity_added'] = true;
			if(!empty($extra2['activity_ids'])) {
				$extra2['activity_ids'] .= ",".$extra['activity_id'];
			} else {
				$extra2['activity_ids'] = $extra['activity_id'];
			}
			$upd_data = array();
			$upd_data['extra'] = json_encode($extra2,true);
			$terms_where = array("clause" => "id=:id", "params" => array(":id" => $agreement_id));
			$pdo->update('member_terms_agreement',$upd_data,$terms_where);
		}
	}

	public function add_activity_feed_for_member_terms($ws_ids,$customer_id,$agreement_id){
		global $pdo;
		$ws_ids = implode(',',$ws_ids);
		$ws_res =  $pdo->select("SELECT id,website_id FROM website_subscriptions WHERE id IN(".$ws_ids.")");
		if(!empty($ws_res)) {
			foreach($ws_res as $ws_row) {
	  			$amendmentSchParam = array(':customer_id'=>$customer_id);
				$entityArr = array(
					'changed benefit tier',
					'changed plan',
					'changed policy',
					'changed benefit amount',

					'cancelled benefit tier change',
					'cancelled plan change',
					'cancelled policy change',
					'cancelled benefit amount change',
					
					'updated future benefit tier change',
					'updated future plan change',
					'updated future policy change',
					'updated future benefit amount change',

					'changed effective date',
				);
				$amendmentIncr = " AND description LIKE '%".$ws_row['website_id']."%'";
				$amendmentIncr .= ' AND (';
				foreach($entityArr as $entiy){
					if($entiy == end($entityArr)){
						$amendmentIncr .= " entity_action LIKE '%".$entiy."%' )";
					}else{
						$amendmentIncr .= " entity_action LIKE '%".$entiy."%' OR ";
					}
				}
				$ammendmentActivitySql = "SELECT GROUP_CONCAT(id) as activity_ids FROM activity_feed WHERE ((entity_id=:customer_id AND entity_type='Customer') OR (user_id=:customer_id AND user_type='Customer')) $amendmentIncr ORDER BY id DESC";
				$ammendmentActivityRow = $pdo->selectOne($ammendmentActivitySql,$amendmentSchParam);
				if(!empty($ammendmentActivityRow['activity_ids'])) {
					$this->update_member_terms($customer_id,$ws_row['id'],$agreement_id,array('activity_id' => $ammendmentActivityRow['activity_ids']));
					return $ammendmentActivityRow['activity_ids'];
				}
			}
		}
	}

	/**
	 * Add member terms custom questions
	 * */
	public function insert_member_terms_custom_question($agreement_id,$customer_id,$product_id,$prd_plan_type_id = 0,$website_id=0){
		global $pdo;
		//Customer custom questions EL8-1391
		$enrollee_type = '';
		if($prd_plan_type_id==1){
	      $enrollee_type .= " AND cc.enrollee_type IN('primary')";
	    }else if($prd_plan_type_id==2){
	      $enrollee_type .= " AND cc.enrollee_type IN('primary','child')";
	    }else if($prd_plan_type_id==3){
	      $enrollee_type .= " AND cc.enrollee_type IN('primary','spouse')";
	    }else if($prd_plan_type_id==5){
	      $dep_relation = $pdo->selectOne("SELECT relation FROM customer_dependent WHERE website_id=:id",array(":id"=>$website_id));
	      if(!empty($dep_relation['relation'])){
	        if(in_array(strtolower($dep_relation['relation']),array('wife','husband'))){
	          $enrollee_type .= " AND cc.enrollee_type IN('primary','spouse')";
	        }else{
	          $enrollee_type .= " AND cc.enrollee_type IN('primary','child')";
	        }
	      }
	    }

		$customerCustomQuesSql = "SELECT cc.id,display_label,cc.answer,cc.enrollee_type,cc.question_id,cc.dependent_id
		FROM customer_custom_questions cc
		JOIN prd_enrollment_questions peq ON(peq.id=cc.question_id AND questionType='Custom')
		JOIN prd_enrollment_questions_assigned peqa ON(peqa.prd_question_id=peq.id AND peqa.is_member_agreement='Y')
		WHERE cc.is_deleted='N' AND ( IF(cc.enrollee_type='primary',peqa.is_member_asked='Y',0) 
		OR IF(cc.enrollee_type='spouse',peqa.is_spouse_asked='Y',0) 
		OR IF(cc.enrollee_type='child',peqa.is_child_asked='Y',0)) AND peqa.product_id IN(".$product_id.") $enrollee_type AND cc.customer_id=:customer_id GROUP BY cc.customer_id,cc.dependent_id,cc.question_id,enrollee_type ORDER BY FIELD(enrollee_type,'primary','spouse','child'),dependent_id,peq.id ASC";
		$customerCustomQueRes = $pdo->select($customerCustomQuesSql,array(":customer_id" => $customer_id));
		if(!empty($customerCustomQueRes)){
			foreach($customerCustomQueRes as $question){
				$custom_question_param = array(
					'agreement_id' => $agreement_id,
					'customer_id' => $customer_id,
					'dependent_id' => $question['dependent_id'],
					'question_id' => $question['question_id'],
					'enrollee_type' => $question['enrollee_type'],
					'display_label' => $question['display_label'],
					'answer' => $question['answer'],
				);
				$pdo->insert('member_agreement_custom_question',$custom_question_param);
			}
		}
	}

	public function get_terms_conditions_content($product_ids,$customer_id=0){
		global $pdo;
		$product_ids_arr = explode(',',$product_ids);
		$enrollment_fee_amount=0;
		$terms_html="";


		if(count($product_ids_arr) > 0) {
			foreach ($product_ids_arr as $key => $product_id) {
				$prd_sql = "SELECT p.name,prd_term.terms_condition as eSignTermsCondition_desc,pd.limitations_exclusions
							FROM prd_main p 
							JOIN prd_terms_condition prd_term ON(prd_term.product_id = p.id AND prd_term.is_deleted='N')
							LEFT JOIN prd_descriptions pd ON(pd.product_id= p.id)
							WHERE p.id=:id AND p.is_deleted='N';
							";
				$prd_row = $pdo->selectOne($prd_sql, array(":id" =>$product_id));
				if(!empty($prd_row)){
					$terms_html.=$prd_row['name'];
					$terms_html.="<br/>";
					$terms_html.=$prd_row['limitations_exclusions'];
					$terms_html.="<br/>";
					$terms_html.=$prd_row['eSignTermsCondition_desc'];
					$terms_html.="<br/>";

					$smart_tags = get_user_smart_tags($customer_id,'member',$product_id);

			        if(!empty($smart_tags)){
			            foreach ($smart_tags as $tmpKey => $tmpValue) {
			            	$terms_html=str_replace("[[".$tmpKey."]]",$tmpValue,$terms_html);
			            }
			        }
				}
			}
		}
		return $terms_html;
		/*ob_start();
		include dirname(__DIR__) . '/tmpl/main_enroll_terms.inc.php';
		$content = ob_get_clean();
		return $content;*/
	}

	public function insert_dpg_agreements($customer_id,$order_id,$extra = array()) {
		global $pdo;
		$dpg_products=array();
	  
		$is_dpg_product=false;
		$generate_agreement=true;
			
		$REAL_IP_ADDRESS = get_real_ipaddress();
		//  Customer Details Code Start
		  $cust_sql = "SELECT c.*,cs.signature_file FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE c.id=:customer_id";
		  $customer_res = $pdo->selectOne($cust_sql,array(":customer_id" => $customer_id));  
		//  Customer Details Code Ends

		// Dependent code start
		  $dependent_sql = "SELECT cd.*,pm.name as product_name, pm.product_code as product_code, ppt.title as plan_name
		  FROM order_details as od
		  LEFT JOIN prd_main as pm ON(pm.id = od.product_id AND od.product_type='Normal')
		  LEFT JOIN prd_matrix as px ON(px.id = od.plan_id)
		  LEFT JOIN prd_plan_type as ppt ON(px.plan_type = ppt.id)
		  JOIN website_subscriptions as ws ON(ws.product_id=pm.id AND ws.plan_id=px.id AND ws.customer_id=:cus_id)
		  JOIN customer_dependent as cd ON (ws.id=cd.website_id)
		  WHERE od.order_id = :id AND od.is_deleted='N' GROUP BY cd.display_id";
	  
		  $dependet_where = array(":id" => $order_id, ":cus_id" => $customer_id);
	  
		  $dependent_res = $pdo->select($dependent_sql,$dependet_where);  
		// Dependent code ends

		// Customer Billing Details Code Start
		  $customer_billing_sql = "SELECT * FROM customer_billing_profile WHERE customer_id=:c_id and is_deleted='N'";
		  $customer_billing_res = $pdo->selectOne($customer_billing_sql,array(":c_id" => $customer_id)); 
		// Customer Billing Details Code Ends
	  
		//order detail code start
		  $sqlOrder="SELECT p.id as product_id,p.parent_product_id from orders o
					JOIN order_details od on (o.id=od.order_id AND od.is_deleted='N')
					JOIN prd_main p ON (p.id=od.product_id)
					where o.id=:order_id";
		  $resOrder=$pdo->select($sqlOrder,array(":order_id"=>$order_id));
	  
		  $sqlDpg="SELECT * FROM dpg_agreements_products where status='Active' order by id DESC";
		  $resDpg=$pdo->selectOne($sqlDpg);
	  
		  if(!empty($resOrder) && !empty($resDpg)){
			
			$dpg_products=explode(",", $resDpg['product_ids']);
	  
			foreach ($resOrder as $key => $value) {
			  if(in_array('all', $dpg_products) || in_array($value['product_id'], $dpg_products) || in_array($value['parent_product_id'], $dpg_products)){
				  $is_dpg_product=true;
			  }
			}
		  }
		//order detail code end
		  
		$primary_details = array();
		$primary_details = $customer_res;
		
		$dependents_details = array();
		$dependents_details = $dependent_res;
			  
		$billing_details = array();
		$billing_details = $customer_billing_res;
	  
		$terms_params = array(
			'customer_id' => $customer_id,
			'order_id' => $order_id,
			'primary_details' => json_encode($primary_details),
			'dependent_details' => json_encode($dependents_details),
			'billing_details' => json_encode($billing_details),
			'created_at' => 'msqlfunc_NOW()',
		);
	  
		$terms_params['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
		if(!empty($extra) && checkIsset($extra['entity_type']) != ""){
		   $terms_params['extra'] = json_encode($extra);
		}
	  
		$order_sql = "SELECT * FROM orders WHERE id=:order_id 
						AND status IN('Payment Approved','Post Payment','Pending Settlement')";
		$order_params = array(":order_id" => $order_id);
		$order_res = $pdo->selectOne($order_sql,$order_params);
	  
		if(!$order_res){
			$generate_agreement = false;  	
		}else{
			$generate_agreement = true;
			if($order_res['status'] == "Payment Approved"){
				$terms_params['is_generated'] = 'Y';
			}
		}
	  
	  
		$dpg_agreement_sql = "SELECT * FROM dpg_agreements WHERE customer_id=:customer_id AND is_generated='Y'";
		$dpg_agreement_params = array(":customer_id" => $customer_id);
		$dpg_agreements_exist = $pdo->select($dpg_agreement_sql,$dpg_agreement_params);

		if($dpg_agreements_exist){
		  $generate_agreement = false;  	
		}
			
		if($is_dpg_product && $generate_agreement && !empty($customer_res['signature_file'])){
			$terms_params['signature_img'] = $customer_res['signature_file'];
		  $terms_params['signature_date'] = $customer_res['signature_date'];
		  $pdo->insert('dpg_agreements',$terms_params);
		}
	}
	public function insert_joinder_agreements($customer_id,$order_id,$application_type) {
		global $pdo,$S3_KEY,$S3_SECRET,$S3_REGION,$S3_BUCKET_NAME;

		$order_sql = "SELECT id,status FROM orders WHERE id=:order_id 
						AND status IN('Payment Approved','Post Payment','Pending Settlement')";
		$order_params = array(":order_id" => $order_id);
		$order_res = $pdo->selectOne($order_sql,$order_params);
		

		if(!empty($order_res["id"])){
			$REAL_IP_ADDRESS = get_real_ipaddress();
			
			$sqlOrder="SELECT p.id as productId,pa.joinder_agreement,od.website_id,
				c.id as customer_id,c.fname,c.lname,cs.signature_file,cs.signature_date
				FROM orders o
				JOIN customer c ON(o.customer_id=c.id)
				LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
				JOIN order_details od ON (o.id=od.order_id AND od.is_deleted='N')
				JOIN prd_main p ON (p.id=od.product_id AND p.joinder_agreement_require='Y')
				JOIN prd_agreements pa ON(p.id=pa.product_id AND pa.is_deleted='N')
				where o.id=:order_id AND c.id=:customer_id GROUP BY p.id";
			$resOrder=$pdo->select($sqlOrder,array(":order_id"=>$order_id,":customer_id" => $customer_id));
			
			if(!empty($resOrder)){
				foreach($resOrder as $odrRow){

					$agreement_params = array(
						'customer_id' => $customer_id,
						'order_id' => $order_id,
						'website_id' => $odrRow["website_id"],
						'product_id' => $odrRow["productId"],
						'application_type' => $application_type,
						'ip_address' => (!empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address']),
					);

					if($order_res['status'] == "Payment Approved"){
						$agreement_params['is_generated'] = 'Y';
					}

					if(!empty($odrRow['signature_file'])){
						$agreement_params['signature_img'] = $odrRow['signature_file'];
					  	$agreement_params['signature_date'] = $odrRow['signature_date'];
					}

					$joinderAgreement = $odrRow['joinder_agreement'];

					$smart_tags = get_user_smart_tags($customer_id,'member',$odrRow['productId']);
		
		            if(!empty($smart_tags)){
		            	foreach ($smart_tags as $key => $value) {
		            		$joinderAgreement = str_replace("[[" . $key . "]]", $value, $joinderAgreement);
		            	}
		            }

					$s3Client = new S3Client([
		                'version' => 'latest',
		                'region'  => $S3_REGION,
		                'credentials'=>array(
		                    'key'=> $S3_KEY,
		                    'secret'=> $S3_SECRET
		                )
		            ]);
	            	$s3Client->registerStreamWrapper();

		            $file_name = $odrRow['fname'] . $odrRow['lname'] . $odrRow['website_id'] . date('mdY') . time() . '.txt';
		            $file_name = str_replace(" ", "", $file_name);

		            $result = $s3Client->putObject(array(
		                'Bucket' => $S3_BUCKET_NAME,
		                'Key'    => '/joinder_agreement'.$file_name,
		                'Body'   => $joinderAgreement
		            ));

		            $agreement_params['agreement_file'] = $file_name;

					$checkAgreement = "SELECT id,is_generated FROM joinder_agreements WHERE customer_id=:customer_id AND product_id=:product_id AND is_deleted='N'";
					$checkParams = array(":customer_id" => $customer_id,":product_id" => $odrRow["productId"]);
					$resAgreement = $pdo->selectOne($checkAgreement,$checkParams);

					if(empty($resAgreement)){
						$pdo->insert('joinder_agreements',$agreement_params);
					}else if($resAgreement["is_generated"] == "N"){
	       				$updWhere=array(
	       					'clause'=>'id=:id',
	       					'params'=>array(":id"=>$resAgreement['id'])
	       				);
	       				$pdo->update('joinder_agreements',$agreement_params,$updWhere);
					}
				}
			}
		}
	}
	//********************* Payable Module Function Code Start ******************
	public function get_payable_id() {
		global $pdo;
		$payable_id = rand(1000000, 9999999);
		$sql = "SELECT display_id FROM payable WHERE display_id = 'PA" . $payable_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res) {
			return $this->get_payable_id();
		} else {
			return "PA" . $payable_id;
		}
	}
	/*
	* NOTE : In $other_params Must Pass order_detail_id
	* 
	*
	*/
	public function payable_insert($order_id,$customer_id=0,$product_id=0,$matrix_id=0,$other_params=array()){
		global $pdo;
	    if(empty($product_id) && !empty($other_params['transaction_tbl_id'])){
	    	$sqlPayable="SELECT o.customer_id,od.product_id,od.plan_id,od.id as order_detail_id FROM orders o
                    JOIN order_details od on (o.id=od.order_id AND od.is_deleted='N')
                    where o.id=:order_id ORDER BY od.id ASC";
			$resPayable = $pdo->select($sqlPayable,array(":order_id"=>$order_id));
            if(!empty($resPayable)){
                foreach ($resPayable as $keyPayable => $rowPayable) {
					$is_reverse = $pay_period = '';
					if(!empty($other_params['pay_period'])){
						$pay_period = $other_params['pay_period'];
					}
					if($other_params['payable_type'] == 'Reverse_Vendor'){
						$is_reverse = 'Y';
					}
					$payable_arr = $this->check_payable_can_regenerate_or_not($other_params['transaction_tbl_id'],$order_id,$rowPayable['order_detail_id'],$pay_period,$is_reverse,$other_params);
                }
                return array();
            }
	    }

		$payable_display_id=$this->get_payable_id();
	    $resRevVendor = $payableArray=array();
	    $order_detail_id = isset($other_params['order_detail_id'])?$other_params['order_detail_id']:0;
	    $payable_id=0;
	    $payee_id=0;
	    $payee_type='';
	    $commission_id=0;
	    // $commission_monthly_id=0;
	    $fee_price_id=0;
	    $plan_id=0;
	    $payable_detail_status='';
	    $credit=0;
	    $debit=0;
	    $renew_count=0;
	    $is_vendor=false;
	    $is_reverse_vendor=false;
	    $is_reverse='N';
		$reverse_id=0;
		$payout=0;

		if(!empty($order_detail_id)) {
			$order_sql = "SELECT o.id as order_id,od.id as order_detail_id,od.product_id,od.plan_id,od.prd_plan_type_id,IF(o.is_list_bill_order='N',o.is_renewal,IF(od.renew_count=1,'N','Y')) as is_renewal,o.grand_total,od.renew_count,ws.customer_id,o.is_list_bill_order,od.website_id,od.is_refund
						FROM orders o 
						JOIN order_details od ON (o.id=od.order_id AND od.is_deleted='N')
						JOIN website_subscriptions ws ON (ws.id=od.website_id)
						WHERE 
						od.id=:order_detail_id";
			$order_where = array(":order_detail_id" => $order_detail_id);
			$orderRes = $pdo->selectOne($order_sql,$order_where);
		} else {
			$order_sql = "SELECT o.id as order_id,od.id as order_detail_id,od.product_id,od.plan_id,od.prd_plan_type_id,IF(o.is_list_bill_order='N',o.is_renewal,IF(od.renew_count=1,'N','Y')) as is_renewal,o.grand_total,od.renew_count,ws.customer_id,o.is_list_bill_order,od.website_id,od.is_refund
				FROM orders o 
				JOIN order_details od ON (o.id=od.order_id AND od.is_deleted='N')
				JOIN website_subscriptions ws ON (ws.id=od.website_id)
				WHERE od.order_id=:order_id AND od.product_id=:product_id";
			$orderRes = $pdo->selectOne($order_sql,array(":order_id"=>$order_id,":product_id"=>$product_id));
		}

	    if(!empty($orderRes) && !empty($other_params)){
	    	$plan_id=$orderRes['prd_plan_type_id'];
	    	/*if($orderRes['is_list_bill_order'] == "Y") {
	    		if($orderRes['is_refund'] == "Y") {
	    			
	    		}
	    	}*/

	    	$customer_id = $orderRes['customer_id'];
	    	$product_id = $orderRes['product_id'];
	    	$matrix_id = $orderRes['plan_id'];

	    	if(!empty($other_params['pay_period'])){
				$payPeriod = date("Y-m-d",strtotime($other_params['pay_period']));
			}else{
				if($orderRes['is_renewal'] != 'Y'){
					$payPeriod = get_pay_period_weekly();
				}else{
					$payPeriod = get_pay_period_monthly();
				}
			}

	       	$payDate = !empty($other_params['payDate']) ? date("Y-m-d H:i:s",strtotime($other_params['payDate'])) : date("Y-m-d H:i:s");

	       	$renew_count = ($orderRes['renew_count'] - 1);

	       	$payableSql="SELECT id FROM payable where order_id=:order_id AND order_detail_id=:order_detail_id AND product_id = :product_id AND is_deleted='N' AND status='Active'";
	       	$payableWhr=array(
	       		':order_id'=>$order_id,
				':order_detail_id'=>$order_detail_id,
				':product_id'=>$product_id,
	       	);
			$payableRes=$pdo->selectOne($payableSql,$payableWhr);
	       	if(!empty($payableRes)){
	       		$payable_id=$payableRes['id'];
	       	} else {
	       		$period_count = $orderRes['renew_count'];

	       		$insParams=array(
		            'display_id'=>$payable_display_id,
		            'order_id'=>$order_id,
		            'order_detail_id'=>$order_detail_id,
		            'customer_id'=>$customer_id,
		            'product_id'=>$product_id,
		            'plan_id'=>$plan_id,
		            'matrix_id'=>$matrix_id,
		            'status'=>'Active',
		            'period_count'=>$period_count,
		            'created_at'=>$payDate,
		        );
	       		$payable_id = $pdo->insert('payable',$insParams);
	       	}

	       	$insParams = array(
		        'payable_id'=>$payable_id,
				'commission_id'=>0,
				'fee_price_id'=>0,
				'matrix_id'=>0,
		        'payee_id'=>0,
				'credit'=>0,
				'debit'=>0,
		        'created_at'=>$payDate,
			);	

	       	if(!empty($other_params['payable_type'])){
	       		if(in_array($other_params['payable_type'],array("Commission","Advance_Commission","PMPM_Commission","Fee_Commission"))){
	       			$sqlComm="SELECT c.id,c.amount,if(cr.calculate_by = 'Percentage',c.percentage,c.amount) as payout,c.customer_id,c.sub_type,c.is_fee_comm FROM commission c LEFT JOIN commission_rule cr ON(c.rule_id=cr.id and cr.is_deleted='N') where c.id=:id and c.commission_duration='weekly'";
	       			$resComm=$pdo->selectOne($sqlComm,array(":id"=>$other_params['commission_id']));

	       			if(!empty($resComm)){
	       				$commission_id = $resComm['id'];
	       				$payee_id = $resComm['customer_id'];
	       				$payee_type = getName('customer',$payee_id,'type','id');
						$payout = $resComm['payout'];
	       				if($resComm['sub_type']=="Reverse"){
	       					$payable_detail_status="Reverse_".$other_params['payable_type'];
	       					$is_reverse='Y';
	       					if($resComm['is_fee_comm'] == "Y") {
	       						$credit = $resComm['amount'];
	       					} else {
	       						$debit = $resComm['amount'];
	       					}
	       				}else{
	       					$payable_detail_status="Generate_".$other_params['payable_type'];
	       					if($resComm['is_fee_comm'] == "Y") {
	       						$debit = $resComm['amount'];
	       					} else {
	       						$credit = $resComm['amount'];
	       					}
	       				}
	       			}

	       		} else if(in_array($other_params['payable_type'],array("Commission_Monthly","Advance_Commission_Monthly","PMPM_Commission_Monthly","Fee_Commission_Monthly"))){
	       			$sqlComm="SELECT c.id,c.amount,if(cr.calculate_by = 'Percentage',c.percentage,c.amount) as payout,c.customer_id,c.sub_type,c.is_fee_comm FROM commission c LEFT JOIN commission_rule cr ON(c.rule_id=cr.id and cr.is_deleted='N') where c.id=:id and c.commission_duration='monthly'";
	       			$resComm=$pdo->selectOne($sqlComm,array(":id"=>$other_params['commission_id']));
	       			if(!empty($resComm)){
	       				$commission_id = $resComm['id'];
	       				$payee_id = $resComm['customer_id'];
	       				$payee_type = getName('customer',$payee_id,'type','id');
						$payout = $resComm['payout'];

	       				if($resComm['sub_type']=="Reverse"){
	       					$payable_detail_status="Reverse_".$other_params['payable_type'];
	       					$is_reverse='Y';
	       					if($resComm['is_fee_comm'] == "Y") {
	       						$credit = $resComm['amount'];
	       					} else {
	       						$debit = $resComm['amount'];
	       					}
	       				}else{
	       					$payable_detail_status="Generate_".$other_params['payable_type'];
	       					if($resComm['is_fee_comm'] == "Y") {
	       						$debit = $resComm['amount'];
	       					} else {
	       						$credit = $resComm['amount'];
	       					}
	       				}

	       			}

	       		} else if($other_params['payable_type']=="Vendor") {

					$sql_where1 = array(":order_detail_id"=>$order_detail_id,':order_id'=>$order_id);
					$inner_incr = '';

	       			if($orderRes['is_renewal']=='Y'){
	                  	$inner_incr.=" AND p_fee.is_fee_on_renewal='Y' AND (p_fee.fee_renewal_type='Continuous' OR p_fee.fee_renewal_count <= $renew_count)";
	                } else {
	                  	$inner_incr.=" AND p_fee.initial_purchase='Y'";
					}
					
					if(!empty($other_params['plan_ids'])){
						if($other_params['pricing_model'] != 'FixedPrice'){
							$inner_incr .= "AND IF(p_fee.is_benefit_tier='N',1,fee_matrix.matrix_group=p_matrix.matrix_group)";
							if($other_params['pricing_model'] == 'VariableEnrollee'){
							    $inner_incr .= "AND p_matrix.id IN(".$other_params['prd_plan_id'].")";	
							}
						}else{
							$inner_incr.= "AND IF(p_fee.is_benefit_tier='N',1,p_matrix.plan_type IN(".$other_params['plan_ids'].")
							AND fee_matrix.plan_type IN(".$other_params['plan_ids']."))";
						}				
					}

					if(!empty($other_params['fee_price_id'])){
						$sqlrevVendor="SELECT pd.* 
										FROM payable p
										JOIN payable_details pd ON (p.id=pd.payable_id)
										WHERE 
										p.order_id=:order_id AND 
										p.order_detail_id=:order_detail_id AND 
										pd.status IN('Generate_Vendor','Generate_Membership','Generate_Carrier') AND 
										is_reverse='N' AND 
										pd.fee_price_id=:fee_price_id";
						$whrrevVendor = array(
							':order_id'=>$order_id,
							':order_detail_id'=>$order_detail_id,
							':fee_price_id'=>$other_params['fee_price_id'],
						);
						
						$resRevVendor = $pdo->select($sqlrevVendor,$whrrevVendor);

						$sql_where1[':fee_price_id'] = $other_params['fee_price_id'];
						$inner_incr.=" AND paf.fee_id =:fee_price_id ";
					}
					$NBOrderResult = getNBOrderDetails($orderRes['customer_id'],$product_id);
                    if(!empty($NBOrderResult) && !empty($NBOrderResult['orderDate'])){
                    	$sql_where1[':order_date'] = date('Y-m-d',strtotime($NBOrderResult['orderDate']));
                    } else {
                    	$sql_where1[':order_date'] = date('Y-m-d');
                    }
					if($other_params['pricing_model'] != 'VariableEnrollee'){
					$sqlVendor = "SELECT pf.id as vendor_id,paf.fee_id as fee_price_id,p_fee.name AS fee_name ,fee_matrix.price_calculated_on as fee_method,pf.setting_type as payable_type,fee_matrix.price as amount,p_matrix.price as retail_price,fee_matrix.id as fee_matrix_id,p_fee.is_benefit_tier,fee_matrix.price_calculated_type,p_matrix.commission_amount,p_matrix.non_commission_amount
					FROM order_details od 
					JOIN prd_main od_pm ON(od_pm.id = od.product_id)
					JOIN prd_matrix p_matrix ON(p_matrix.id = od.plan_id AND p_matrix.is_deleted='N')
					JOIN prd_assign_fees paf ON((paf.product_id=od_pm.id OR paf.product_id=od_pm.parent_product_id) AND paf.is_deleted='N')
					JOIN prd_fees pf ON(pf.id=paf.prd_fee_id AND pf.is_deleted='N')
					JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=paf.fee_id AND fee_matrix.is_deleted='N')
					JOIN prd_main p_fee ON(p_fee.id=fee_matrix.product_id AND p_fee.is_deleted='N')
					LEFT JOIN prd_fee_pricing_model model ON(
						CASE WHEN p_fee.is_benefit_tier='Y' 
						THEN 
							model.product_id=od.product_id AND 
							model.prd_matrix_id=od.plan_id AND 
							model.fee_product_id=p_fee.id AND 
							model.prd_matrix_fee_id=fee_matrix.id
						ELSE
							model.product_id=od.product_id AND 
							model.prd_matrix_id=od.plan_id 
						END
						AND model.is_deleted='N'  
						)
					WHERE od.order_id=:order_id AND od.is_deleted='N' AND od.id=:order_detail_id AND pf.setting_type IN('Vendor','Membership','Carrier') AND p_fee.status='Active' 
					AND DATE(fee_matrix.pricing_effective_date) <= :order_date  AND (fee_matrix.pricing_termination_date IS NULL OR DATE(fee_matrix.pricing_termination_date)>= :order_date) $inner_incr GROUP BY p_fee.id,od.product_id
					";
					}else{
					$sqlVendor = "SELECT pf.id AS vendor_id,paf.fee_id AS fee_price_id,p_fee.name AS fee_name ,fee_matrix.price_calculated_on AS fee_method,pf.setting_type AS payable_type,fee_matrix.price AS amount,p_matrix.price AS retail_price,fee_matrix.id AS fee_matrix_id,p_fee.is_benefit_tier,fee_matrix.price_calculated_type,p_matrix.commission_amount,p_matrix.non_commission_amount
					FROM prd_matrix p_matrix 
					JOIN prd_main od_pm ON(od_pm.id = p_matrix.product_id)
					JOIN order_details od ON(od_pm.id = od.product_id)
					JOIN prd_assign_fees paf ON((paf.product_id=od_pm.id OR paf.product_id=od_pm.parent_product_id) AND paf.is_deleted='N')
					JOIN prd_fees pf ON(pf.id=paf.prd_fee_id AND pf.is_deleted='N')
					JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=paf.fee_id AND fee_matrix.is_deleted='N')
					JOIN prd_main p_fee ON(p_fee.id=fee_matrix.product_id AND p_fee.is_deleted='N')
					LEFT JOIN prd_fee_pricing_model model ON(
						CASE WHEN p_fee.is_benefit_tier='Y' 
						THEN 
							model.product_id=od_pm.id AND 
							model.prd_matrix_id=p_matrix.id AND 
							model.fee_product_id=p_fee.id AND 
							model.prd_matrix_fee_id=fee_matrix.id
						ELSE
							model.product_id=od_pm.id AND 
							model.prd_matrix_id=p_matrix.id 
						END
						AND model.is_deleted='N'  
						)
					WHERE od.order_id=:order_id AND od.is_deleted='N' AND od.id=:order_detail_id AND pf.setting_type IN('Vendor','Membership','Carrier') AND p_fee.status='Active' AND DATE(fee_matrix.pricing_effective_date) <= :order_date
					AND (fee_matrix.pricing_termination_date IS NULL OR DATE(fee_matrix.pricing_termination_date)> :order_date) AND 
					p_fee.initial_purchase='Y' $inner_incr GROUP BY fee_matrix.id";
					}
					$resVendor = $pdo->select($sqlVendor,$sql_where1);
					$is_vendor = true;	   

	       		} else if($other_params['payable_type'] == "Reverse_Vendor") {
	       			$tmp_incr = " AND pd.status IN('Generate_Vendor','Generate_Membership','Generate_Carrier')";
	       			if($orderRes['is_list_bill_order'] == "Y") {
	       				$tmp_incr = " AND pd.status IN('Generate_Vendor','Generate_Membership','Generate_Carrier','Reverse_Vendor','Reverse_Membership','Reverse_Carrier')";
	       			}
	       			if(!empty($other_params) && !empty($other_params['payable_details_id'])){
                    	$tmp_incr .= " AND pd.id = ".$other_params['payable_details_id'];
                    }
	       			$sqlVendor="SELECT pd.* FROM payable p
	       						JOIN payable_details pd ON (p.id=pd.payable_id)
	       						WHERE p.order_id=:order_id AND p.order_detail_id=:order_detail_id AND is_reverse='N' $tmp_incr";
	       			$whrVendor = array(
	       				':order_id'=>$order_id,
						':order_detail_id'=>$order_detail_id,
				   	);
					$resVendor = $pdo->select($sqlVendor,$whrVendor);
	       			$is_reverse_vendor = true;
	       		}
	       	}

		    if(!empty($commission_id)){
		    	$insParams['commission_id']=$commission_id;
		    }
		    
		    if(!empty($fee_price_id)){
		    	$insParams['fee_price_id']=$fee_price_id;
		    }
		    if(!empty($matrix_id)){
		    	$insParams['matrix_id']=$matrix_id;
		    }

		    if(!empty($payee_id)){
		    	$insParams['payee_id']=$payee_id;
		    }
		    if(!empty($payee_type)){
		    	$insParams['payee_type']=$payee_type;
		    }
		    if(!empty($other_params['type'])){
		    	$insParams['type']=$other_params['type'];
		    }
		    if(!empty($payable_detail_status)){
		    	$insParams['status']=$payable_detail_status;
		    }
		    if(!empty($credit)){
	    		$insParams['credit']=$credit;
		    }
		    if(!empty($debit)){
	    		$insParams['debit']=$debit;
		    }
		    if(!empty($is_reverse)){
	    		$insParams['is_reverse']=$is_reverse;
			}
			if(!empty($other_params['transaction_tbl_id'])){
		    	$insParams['transaction_tbl_id'] = $other_params['transaction_tbl_id'];
			}
			if(!empty($payout)){
				$insParams['payout'] = $payout;
			}
			
			$insParams['pay_period'] = $payPeriod;			
		    if(!$is_vendor && !$is_reverse_vendor) {
				$payable_details_id = $pdo->insert('payable_details',$insParams);

				if($is_reverse=='Y'){
					$sqlReverse="SELECT pd.id FROM payable p
						JOIN payable_details pd ON (p.id=pd.payable_id)
						WHERE p.order_id=:order_id AND p.customer_id=:customer_id AND p.order_detail_id=:order_detail_id AND pd.payee_id = :payee_id AND pd.is_reverse='N' and pd.type=:type";
					$whrReverse=array(
						":order_id"=>$order_id,
						":customer_id"=>$customer_id,
						":order_detail_id"=>$order_detail_id,
						":payee_id"=>$payee_id,
						":type"=>$other_params['type'],
					);
					$resReverse=$pdo->select($sqlReverse,$whrReverse);
					if(!empty($resReverse)){
						foreach ($resReverse as $keyRev => $rowRev) {
							$updParam=array("is_reverse"=>'Y','reverse_id'=>$payable_details_id);
		       				$updWhere=array(
		       					'clause'=>'id=:id',
		       					'params'=>array(":id"=>$rowRev['id'])
		       				);
		       				$pdo->update('payable_details',$updParam,$updWhere);
						}
					}
					
				}
		    }else if ($is_reverse_vendor){
		    	if(!empty($resVendor)){
		    		foreach ($resVendor as $key => $vendor_value) {
		    			$vendor_ins_params=$insParams;
						$vendor_ins_params['fee_price_id'] = $vendor_value['fee_price_id'];
						$vendor_ins_params['matrix_id'] = $vendor_value['matrix_id'];
	       				$vendor_ins_params['payee_id'] = $vendor_value['payee_id'];
						$vendor_ins_params['payee_type'] = $vendor_value['payee_type'];
						$vendor_ins_params['payout'] = $vendor_value['payout'];
	       				$vendor_ins_params['status'] = 'Reverse_'.$vendor_value['payee_type'];
                        $vendor_ins_params['debit'] = $vendor_value['credit'] * -1;
                        $vendor_ins_params['is_reverse'] ='Y';
	                    
	                    if($orderRes['is_list_bill_order'] == "Y" && $orderRes['is_refund'] == "Y") {
			    			$vendor_ins_params['status'] = $vendor_value['payee_type'];
                    		$vendor_ins_params['credit'] = $vendor_value['debit'] * -1;
                    		$vendor_ins_params['debit'] = 0;
				    	}

	       				$payable_details_id = $pdo->insert('payable_details',$vendor_ins_params);

	       				$updParam = array("is_reverse"=>'Y','reverse_id'=>$payable_details_id);
	       				$updWhere = array(
	       					'clause' => 'id=:id',
	       					'params' => array(":id"=>$vendor_value['id'])
	       				);
	       				$pdo->update('payable_details',$updParam,$updWhere);
		    		}
		    	}
		    }else if ($is_vendor){
				if(!empty($resRevVendor) && !empty($resVendor)){
					foreach ($resRevVendor as $key => $vendor_value) {
		    			$vendor_ins_params = $insParams;
		    			$vendor_ins_params['fee_price_id'] = $vendor_value['fee_price_id'];
	       				$vendor_ins_params['payee_id'] = $vendor_value['payee_id'];
						$vendor_ins_params['payee_type'] = $vendor_value['payee_type'];
						$vendor_ins_params['payout'] = $vendor_value['payout'];
	       				$vendor_ins_params['status'] = 'Reverse_'.$vendor_value['payee_type'];
                        $vendor_ins_params['debit'] = $vendor_value['credit'] * -1;
                        $vendor_ins_params['is_reverse'] ='Y';
	                    
	                    if($orderRes['is_list_bill_order'] == "Y" && $orderRes['is_refund'] == "Y") {
			    			$vendor_ins_params['status'] = $vendor_value['payee_type'];
                    		$vendor_ins_params['credit'] = $vendor_value['debit'] * -1;
                    		$vendor_ins_params['debit'] = 0;
				    	}

	       				$payable_details_id = $pdo->insert('payable_details',$vendor_ins_params);

	       				$updParam = array("is_reverse"=>'Y','reverse_id'=>$payable_details_id);
	       				$updWhere = array(
	       					'clause' => 'id=:id',
	       					'params' => array(":id"=>$vendor_value['id'])
	       				);
	       				$pdo->update('payable_details',$updParam,$updWhere);
		    		}
				}
		    	if(!empty($resVendor)){
		    		foreach ($resVendor as $key => $vendor_value) {
		    			$vendor_ins_params = $insParams;
		    			$vendor_ins_params['fee_price_id'] = $vendor_value['fee_price_id'];
	       				$vendor_ins_params['payee_id'] = $vendor_value['vendor_id'];
						$vendor_ins_params['payee_type'] = $vendor_value['payable_type'];
						$vendor_ins_params['payout'] = $vendor_value['amount'];
	       				$vendor_ins_params['status']="Generate_".$vendor_value['payable_type'];

	       				if($vendor_value['fee_method'] == "Percentage"){
							if($vendor_value['price_calculated_type']=="Retail"){
								$calclulatedPrice = ($vendor_value['retail_price'] * $vendor_value['amount'])/100;
							  }else if($vendor_value['price_calculated_type']=="Commissionable"){
								$calclulatedPrice = ($vendor_value['commission_amount'] * $vendor_value['amount'])/100;
							  }else if($vendor_value['price_calculated_type']=="NonCommissionable"){
								$calclulatedPrice = ($vendor_value['non_commission_amount'] * $vendor_value['amount'])/100;
							  }
							  $vendor_ins_params['credit'] = $calclulatedPrice;
	                    } else {
							$vendor_ins_params['credit'] = $vendor_value['amount'];
						}

						if($orderRes['is_list_bill_order'] == "Y" && $orderRes['is_refund'] == "Y") {
	       					$vendor_ins_params['status'] = 'Reverse_'.$vendor_value['payable_type'];
                    		$vendor_ins_params['debit'] = $vendor_ins_params['credit'];
                    		$vendor_ins_params['credit'] = 0;
				    	}

	       				$payable_details_id=$pdo->insert('payable_details',$vendor_ins_params);
		    		}
		    	}
		    } else {
		    	return $payableArray;
		    }
	        $payableArray['display_id'] = $payable_display_id;
			$payableArray['payable_details_id'] = isset($payable_details_id) && $payable_details_id>0?$payable_details_id:0;
			
	    }
	    return $payableArray;
	}
	//********************* Payable Module Function Code Start ******************

	public function update_leads_and_details($param) {
		global $pdo;
		if (!empty($param) && !empty($param['customer_id'])) {
			$customer_id = $param['customer_id'];
			if (!empty($customer_id)) {
				$lead_quote_detail_where = array(
					"clause" => "customer_ids=:id AND status NOT IN ('Completed')",
					"params" => array(
						":id" => $customer_id,
					),
				);
				$pdo->update("lead_quote_details", array('status' => 'Disabled', 'updated_at' => 'msqlfunc_NOW()'), $lead_quote_detail_where);
	
				$leads_where = array(
					"clause" => "customer_id=:id",
					"params" => array(
						":id" => $customer_id,
					),
				);
				$pdo->update("leads", array('status' => 'Converted', 'updated_at' => 'msqlfunc_NOW()'), $leads_where);
			}
		}
	}

	public function get_sponsor_detail_for_mail($member_id, $sponsor_id = 0) {
		global $pdo;
		$response = array();
	
		if (empty($sponsor_id)) {
			$member_sql = "SELECT sponsor_id FROM customer WHERE id=:id AND is_deleted='N'";
			$member_where = array(':id' => $member_id);
			$member_row = $pdo->selectOne($member_sql, $member_where);
			if (!empty($member_row['sponsor_id'])) {
				$sponsor_id = $member_row['sponsor_id'];
			}
		}
	
		if (!empty($sponsor_id)) {
			$sponsor_sql = "SELECT id,public_name,public_email,public_phone FROM customer WHERE id=:id AND is_deleted='N'";
			$sponsor_where = array(':id' => $sponsor_id);
			$sponsor_row = $pdo->selectOne($sponsor_sql, $sponsor_where);
			if (!empty($sponsor_row)) {
				if ($sponsor_row['id'] != '') {
					if (($sponsor_row['public_name'] != '') || ($sponsor_row['public_email'] != '') || ($sponsor_row['public_phone'] != '')) {
						$response['agent_name'] = $sponsor_row['public_name'];
						$response['agent_email'] = $sponsor_row['public_email'];
						$response['agent_phone'] = $sponsor_row['public_phone'];
						$response['agent_id'] = $sponsor_row['id'];
						$response['is_public_info'] = '';
					} else {
						$response['is_public_info'] = 'display:none';
					}
				} else {
					$response['is_public_info'] = 'display:none';
				}
			}
		} else {
			$response['is_public_info'] = 'display:none';
		}
		return $response;
	}

	public function credit_card_decline_log($customer_id, $cc_params, $res) {
		global $pdo;
		global $CREDIT_CARD_ENC_KEY;
		global $CC_DECLINE_EMAIL;
		global $DEFAULT_SITE_NAME;
		$response = json_encode($res);
		$decline_text = $cc_params['err_text'];
		if(!isset($cc_params['lastname'])) {
			$cc_params['lastname'] = '';
		}
		$insParams = array(
			'order_type' => $cc_params['order_type'],
			'customer_id' => $customer_id,
			'name_on_card' => ($cc_params['firstname'] . ' ' . $cc_params['lastname']),
			'country' => $cc_params['country'],
			'state' => $cc_params['state'],
			'city' => $cc_params['city'],
			'zip' => $cc_params['zip'],
			'phone' => (isset($cc_params['phone']) ? $cc_params['phone'] : ''),
			'email' => (isset($cc_params['email']) ? $cc_params['email'] : ''),
			'address' => ((isset($cc_params['address1'])?$cc_params['address1']:'') . ' ' . (isset($cc_params['address2'])?$cc_params['address2']:'')),
			'cvv_no' => checkIsset($cc_params['cvv']),
			'card_no' => checkIsset($cc_params['ccnumber'])!='' ? substr($cc_params['ccnumber'], -4) : '',
			'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $cc_params['ccnumber'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
			'card_type' => checkIsset($cc_params['card_type']),
			'card_expiry' => checkIsset($cc_params['ccexp']),
			'amount' => $cc_params['amount'],
			'ip_address' => (isset($cc_params['ip_address']) ? $cc_params['ip_address'] : ""),
			'decline_text' => makeSafe($decline_text),
			'response' => $response,
			'browser' => $cc_params['browser'],
			'os' => $cc_params['os'],
			'req_url' => $cc_params['req_url'],
			'created_at' => 'msqlfunc_NOW()',
		);
		// pre_print($insParams);
		$cc_decline_id = $pdo->insert('cc_decline_log', $insParams);
	
		$trigger_param = array(
			'name_on_card' => ($cc_params['firstname'] . ' ' . $cc_params['lastname']),
			'country' => $cc_params['country'],
			'state' => $cc_params['state'],
			'city' => $cc_params['city'],
			'zip' => $cc_params['zip'],
			'email' => (isset($cc_params['email']) ? $cc_params['email'] : ''),
			'phone' => (isset($cc_params['phone']) ? $cc_params['phone'] : ''),
			'address' => ($cc_params['address1']),
			'cvv_no' => checkIsset($cc_params['cvv']),
			'card_no' => checkIsset($cc_params['ccnumber'])!='' ? 'XXX' . substr($cc_params['ccnumber'], -4) : '',
			'card_type' => checkIsset($cc_params['card_type']),
			'card_expiry' => checkIsset($cc_params['ccexp']),
			'amount' => $cc_params['amount'],
			'ip_address' => (isset($cc_params['ip_address']) ? $cc_params['ip_address'] : ""),
			'processor' => (isset($cc_params['processor']) ? $cc_params['processor'] : ""),
			'decline_text' => $decline_text,
			//'response' => (isset($cc_params['responsetext']) ? $cc_params['responsetext'] : ""),
		);
		if ($CC_DECLINE_EMAIL != "") {
			$subject = $DEFAULT_SITE_NAME." : Credit Card Declined";
			if (isset($cc_params['order_type'])) {
				$subject .= ", " . $cc_params['order_type'] . " Order";
			}
	
			$admin_trigger_param = array('trigger_title' => $DEFAULT_SITE_NAME.' : Credit Card Declined', 'location' => $cc_params['req_url']);
			//trigger_mail_to_email($trigger_param, $CC_DECLINE_EMAIL, $subject, $admin_trigger_param);
		}
		return $cc_decline_id;
	}
	/************************Master Dependent Table Start*******************************/
	public function insert_dependent($arr, $plan_id,$is_add_product=0,$cd_profile_id=0) {

		global $pdo;
		//CRM-610 updates - duplicate dependent issue 
		if($cd_profile_id > 0){
			$sql_q = "SELECT * FROM customer_dependent_profile WHERE id=:id";
			$where = array(
				':id' => $cd_profile_id,
			);
			$sql = $pdo->selectOne($sql_q, $where);
		}else{
			$sql_q = "SELECT * FROM customer_dependent_profile WHERE fname = :fname AND lname = :lname AND relation = :relation AND birth_date = :birth_date AND gender = :gender AND customer_id = :customer_id";
			$where = array(
				':fname' => $arr["fname"],
				':lname' => $arr["lname"],
				':relation' => $arr['relation'],
				':birth_date' => $arr["birth_date"],
				':gender' => $arr['gender'],
				':customer_id' => $arr['customer_id'],
			);
			$sql = $pdo->selectOne($sql_q, $where);
		}

		if (in_array($arr['relation'], array('child', 'spouse'))) {
			$arr['relation'] = getRelation($arr['relation'], $arr['gender']);
		}

		$cd_id = 0;
		if ($sql) {
			$cd_id = $sql['id'];
			$dependant_display_id = $sql['display_id'];

			$cdp_data = array(
				'email' => (isset($arr['email'])?$arr['email']:""),
				'phone' => (isset($arr['phone'])?$arr['phone']:""),
				'fname' => (isset($arr['fname'])?$arr['fname']:""),
				'lname' => (isset($arr['lname'])?$arr['lname']:""),
				'mname' => (isset($arr['mname'])?$arr['mname']:""),
				'address' => (isset($arr['address'])?$arr['address']:""),
				'city' => (isset($arr['city'])?$arr['city']:""),
				'state' => (isset($arr['state'])?$arr['state']:""),
				'zip_code' => (isset($arr['zip_code'])?$arr['zip_code']:""),
				'salary' => (isset($arr['salary'])?$arr['salary']:""),
				'employmentStatus' => (isset($arr['employmentStatus'])?$arr['employmentStatus']:""),
				'tobacco_use' => (isset($arr['tobacco_use'])?$arr['tobacco_use']:""),
				'smoke_use' => (isset($arr['smoke_use'])?$arr['smoke_use']:""),
				'height_feet' => (isset($arr['height_feet'])?$arr['height_feet']:""),
				'height_inches' => (isset($arr['height_inches'])?$arr['height_inches']:""),
				'weight' => (isset($arr['weight'])?$arr['weight']:""),
				'ssn' => (isset($arr['ssn'])?$arr['ssn']:""),
				'last_four_ssn' => (isset($arr['last_four_ssn'])?$arr['last_four_ssn']:""),
				'benefit_level' => (isset($arr['benefit_level'])?$arr['benefit_level']:""),
				'hours_per_week' => (isset($arr['hours_per_week'])?$arr['hours_per_week']:NULL),
				'pay_frequency' => (isset($arr['pay_frequency'])?$arr['pay_frequency']:""),
				'us_citizen' => (isset($arr['us_citizen'])?$arr['us_citizen']:""),
				'hire_date' => (isset($arr['hire_date'])?$arr['hire_date']:NULL),
			);
			$cdp_where = array("clause" => "id=:cd_id", "params" => array(":cd_id" => $cd_id));
			$pdo->update("customer_dependent_profile",$cdp_data,$cdp_where);

			if(!empty($arr['product_id']) && (!empty($arr['benefit_amount']) || !empty($arr['in_patient_benefit']) || !empty($arr['out_patient_benefit']) || !empty($arr['monthly_income']) || !empty($arr['benefit_percentage']) )) {
				$dep_benefit_param = array(
					"benefit_amount" => checkIsset($arr['benefit_amount']),
					"in_patient_benefit" => checkIsset($arr['in_patient_benefit']),
					"out_patient_benefit" => checkIsset($arr['out_patient_benefit']),
					"monthly_income" => checkIsset($arr['monthly_income']),
					"benefit_percentage" => checkIsset($arr['benefit_percentage']),
				);
				save_customer_dependent_profile_benefit_amount($cd_id,$arr['product_id'],$dep_benefit_param);	
			}
		} else {
			$dependant_display_id = $this->get_dependant_display_id();
			$customer_dependent_profile_param = array(
				'customer_id' => $arr['customer_id'],
				'display_id' => $dependant_display_id,
				'relation' => $arr["relation"],
				'fname' => $arr["fname"],
				'lname' => $arr["lname"],
				'birth_date' => $arr["birth_date"],
				'gender' => $arr["gender"],
				'email' => (isset($arr['email'])?$arr['email']:""),
				'phone' => (isset($arr['phone'])?$arr['phone']:""),
				'mname' => (isset($arr['mname'])?$arr['mname']:""),
				'address' => (isset($arr['address'])?$arr['address']:""),
				'city' => (isset($arr['city'])?$arr['city']:""),
				'state' => (isset($arr['state'])?$arr['state']:""),
				'zip_code' => (isset($arr['zip_code'])?$arr['zip_code']:""),
				'salary' => (isset($arr['salary'])?$arr['salary']:""),
				'employmentStatus' => (isset($arr['employmentStatus'])?$arr['employmentStatus']:""),
				'tobacco_use' => (isset($arr['tobacco_use'])?$arr['tobacco_use']:""),
				'smoke_use' => (isset($arr['smoke_use'])?$arr['smoke_use']:""),
				'height_feet' => (isset($arr['height_feet'])?$arr['height_feet']:""),
				'height_inches' => (isset($arr['height_inches'])?$arr['height_inches']:""),
				'weight' => (isset($arr['weight'])?$arr['weight']:""),
				'ssn' => (isset($arr['ssn'])?$arr['ssn']:""),
				'last_four_ssn' => (isset($arr['last_four_ssn'])?$arr['last_four_ssn']:""),
				'benefit_level' => (isset($arr['benefit_level'])?$arr['benefit_level']:""),
				'hours_per_week' => (isset($arr['hours_per_week'])?$arr['hours_per_week']:""),
				'pay_frequency' => (isset($arr['pay_frequency'])?$arr['pay_frequency']:""),
				'us_citizen' => (isset($arr['us_citizen'])?$arr['us_citizen']:""),
				'hire_date' => (isset($arr['hire_date'])?$arr['hire_date']:NULL),
			);
			$cd_id = $pdo->insert('customer_dependent_profile', $customer_dependent_profile_param);

			if(!empty($arr['product_id']) && (!empty($arr['benefit_amount']) || !empty($arr['in_patient_benefit']) || !empty($arr['out_patient_benefit']) || !empty($arr['monthly_income']) || !empty($arr['benefit_percentage']))) {
				$dep_benefit_param = array(
					"benefit_amount" => checkIsset($arr['benefit_amount']),
					"in_patient_benefit" => checkIsset($arr['in_patient_benefit']),
					"out_patient_benefit" => checkIsset($arr['out_patient_benefit']),
					"monthly_income" => checkIsset($arr['monthly_income']),
					"benefit_percentage" => checkIsset($arr['benefit_percentage']),
				);
				save_customer_dependent_profile_benefit_amount($cd_id,$arr['product_id'],$dep_benefit_param);	
			}
		}

		if ($cd_id > 0) {
			// pre_print($dependant_display_id);
			$incr = "";
			// if($is_add_product == 1){
				$incr = " AND terminationDate IS NULL";
			// }
			if(isset($arr["website_id"]) && $arr["website_id"] > 0) {
				$checkDep = "SELECT id FROM customer_dependent WHERE website_id=:website_id AND cd_profile_id=:cd_profile_id";
				$checkDepParams = array(":website_id" => $arr['website_id'], ":cd_profile_id" => $cd_id);
				$checkDepRow = $pdo->selectOne($checkDep, $checkDepParams);
			} else {				
				$checkDep = "SELECT id FROM customer_dependent WHERE customer_id=:customer_id AND product_id=:product_id AND product_plan_id=:product_plan_id AND fname=:fname AND lname=:lname AND is_deleted='N' AND relation = :relation $incr";
				$checkDepParams = array(":customer_id" => $arr['customer_id'], ":product_id" => $arr['product_id'], ":product_plan_id" => $plan_id, ":fname" => $arr["fname"], ":lname" => $arr["lname"], ":relation" => $arr['relation']);
				$checkDepRow = $pdo->selectOne($checkDep, $checkDepParams);
			}

			$dep_id = 0;
			if ($checkDepRow) {
				$dep_id = $checkDepRow['id'];
			}

			if ($dep_id > 0) {
				$dependent_where = array("clause" => "id=:id", "params" => array(":id" => $dep_id));
				$pdo->update("customer_dependent", $arr, $dependent_where);
			} else {
				$arr['cd_profile_id'] = $cd_id;
				$arr['display_id'] = $dependant_display_id;
				$arr['created_at'] = 'msqlfunc_NOW()';
				$dep_id = $pdo->insert('customer_dependent', $arr);
			}
		}
		return $dep_id;
	}
	public function get_dependant_display_id() {
		global $pdo;
		$dependat_id = rand(100000, 999999);
		$sql = "SELECT count(*) as total FROM customer_dependent WHERE display_id ='D2" . $dependat_id . "' OR display_id ='" . $dependat_id . "'";
		$res = $pdo->selectOne($sql);
	
		if ($res['total'] > 0) {
			return $this->get_dependant_display_id();
		} else {
			return "D2" . $dependat_id;
		}
	}

	/************************Master Dependent Table End*******************************/
	public function get_order_id() {
		global $pdo;
		$cust_id = rand(1000000, 9999999);
		$sql = "SELECT count(*) as total FROM orders WHERE display_id ='" . $cust_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->get_order_id();
		} else {
			return $cust_id;
		}
	}
	public function addQuoteNotificationMain($agent_id, $quote_noti_id=1, $lead_id, $quote_id, $total_policy, $total_policy_price, $url = "#") {
		global $pdo;

		$noti_param = array(
			'agent_id' => $agent_id,
			'quote_noti_id' => $quote_noti_id,
			'lead_id' => $lead_id,
			'quote_id' => $quote_id,
			'total_policy' => $total_policy,
			'total_policy_price' => $total_policy_price,
			'url' => '{HOST}/leads_profile.php?leads=' . $lead_id,
			'updated_at' => 'msqlfunc_NOW()',
		);

		$details_res = $pdo->selectOne("SELECT id FROM quote_notification_details WHERE agent_id = :agent_id AND quote_id = :quote_id AND lead_id = :lead_id AND quote_noti_id = :quote_noti_id", array(":agent_id"=>$agent_id, "quote_id" =>$quote_id, ":lead_id" => $lead_id, ":quote_noti_id" => $quote_noti_id));
		if(!empty($details_res)){
			$where = array(
				"clause" => "id=:id",
				"params" => array(
					":id" => $details_res['id'],
				),
			);
			$pdo->update("quote_notification_details", $noti_param, $where);
		} else {
			$noti_param['created_at'] = 'msqlfunc_NOW()';
			$insert_id = $pdo->insert("quote_notification_details", $noti_param);
		}
	}
	public function get_transaction_display_id() {
		global $pdo;
	
		$display_id = rand(10000000,99999999);
		$sql = "SELECT count(*) as total FROM transactions WHERE display_id = :display_id";
		$res = $pdo->selectOne($sql, array(":display_id" => $display_id));
		if ($res['total'] > 0) {
			return $this->get_transaction_display_id();
		} else {
			return $display_id;
		}
	}

	public function transaction_insert($orderId,$orderType='Credit',$transactionType='',$message='',$commissionId=0,$otherParams=array()){
		global $pdo,$CREDIT_CARD_ENC_KEY;
		$displayId=$this->get_transaction_display_id();
		$transactionArray=array();
		$extraParams = array();

		$orderSql="SELECT o.id as orderId,o.customer_id as customerId,o.grand_total as grandTotal,o.status,o.payment_master_id as paymentMasterId,o.payment_type as paymentType,o.is_list_bill_order,o.is_renewal,o.new_business_total,o.renewal_total,o.new_business_members,o.renewal_members FROM orders o WHERE o.id=:id";
		$orderRes=$pdo->selectOne($orderSql,array(":id"=>$orderId));
		
		if($orderRes){
	
			$customerId = $orderRes['customerId'];
			$grandTotal = $orderRes['grandTotal'];
			
			$transactionStatus = "";
			if(in_array($transactionType, array("New Order","Renewal Order","List Bill Order"))){
				$transactionStatus = "Payment Approved";
			}else if(in_array($transactionType, array("Refund Order"))){
				$transactionStatus = "Refund";
			}else if(in_array($transactionType, array("Void Order"))){
				$transactionStatus = "Void";
			}else if(in_array($transactionType, array("Post Payment","Chargeback","Payment Declined","Payment Returned"))){
				$transactionStatus = $transactionType;
			}else{
				$transactionStatus = $orderRes["status"];
			}

			$reqUrl = !empty($otherParams['req_url'])  ? $otherParams['req_url'] : ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    		$transResponse = !empty($otherParams['transaction_response']) ? $otherParams['transaction_response'] : "";
    		$ccDeclineId = !empty($otherParams['cc_decline_log_id']) ? $otherParams['cc_decline_log_id'] : "";
	
			$insParams=array(
				'display_id'=>$displayId,
				'order_id'=>$orderId,
				'customer_id'=>$customerId,
				'order_type'=>$orderType,
				'transaction_status'=> $transactionStatus,
				'transaction_type'=>$transactionType,
				'payment_type'=>$orderRes["paymentType"],
				'is_list_bill_order'=>$orderRes["is_list_bill_order"],
				'message'=>$message,
				'cc_decline_log_id'=>$ccDeclineId,
				'created_at'=>'msqlfunc_NOW()',
			);
	
			 if(!empty($reqUrl)){
				$insParams['req_url'] = $reqUrl;
			}
	
			if(!empty($transResponse)){
				$insParams['transaction_response']=json_encode($transResponse);
			}
	
			if(!empty($orderRes['paymentMasterId'])){
				$insParams['payment_master_id'] = $orderRes['paymentMasterId'];
			}

			if(!empty($otherParams['reason'])){
        		$insParams['reason'] = $otherParams['reason'];	
        	}
	
			$billingSql = "SELECT ob.*,SUBSTRING(AES_DECRYPT(ob.ach_account_number,'".$CREDIT_CARD_ENC_KEY."'),-4) AS last_ach_acc_no 
						FROM order_billing_info ob 
						WHERE ob.order_id=:order_id order by ob.id desc";
			$billingParam = array(":order_id" => $orderRes['orderId']);
			$billingRes = $pdo->selectOne($billingSql,$billingParam);
		   
			if(!empty($billingRes)){
				$insParams['billing_info']=json_encode($billingRes);
			}
	
			if(!empty($otherParams) && isset($otherParams['transaction_date'])){
				$insParams['created_at'] = $otherParams['transaction_date'];
			}  
			if(!empty($otherParams) && !empty($otherParams['transaction_id'])){
				$insParams['transaction_id'] = $otherParams['transaction_id'];
			}
	
			if($orderType=='Credit'){
				if(!empty($otherParams) && isset($otherParams['credit_amount'])){
					$insParams['credit']=$otherParams['credit_amount'];
				}else{
					$insParams['credit']=$grandTotal;
				}
			}else{
				if(in_array($transactionType, array('Refund Order','Chargeback','Payment Declined','Cancelled','Payment Returned','Void Order'))){
					if(!empty($otherParams) && isset($otherParams['debit_amount'])){
						$insParams['debit']=$otherParams['debit_amount'];
					}else{
						$insParams['debit']=$grandTotal;
					}
				}
			}

			if($orderRes['is_list_bill_order'] == "Y"){
				if(!empty($otherParams) && isset($otherParams['new_business_total'])){
					$insParams['new_business_total'] = $otherParams['new_business_total'];
				} else {
					$insParams['new_business_total'] = $orderRes['new_business_total'];
				}
				
				if(!empty($otherParams) && isset($otherParams['renewal_total'])){
					$insParams['renewal_total'] = $otherParams['renewal_total'];
				} else {
					$insParams['renewal_total'] = $orderRes['renewal_total'];
				}
				
				if(!empty($otherParams) && isset($otherParams['new_business_members'])){
					$insParams['new_business_members'] = $otherParams['new_business_members'];
				} else {
					$insParams['new_business_members'] = $orderRes['new_business_members'];
				}
				
				if(!empty($otherParams) && isset($otherParams['renewal_members'])){
					$insParams['renewal_members'] = $otherParams['renewal_members'];
				} else {
					$insParams['renewal_members'] = $orderRes['renewal_members'];
				}
				
			} else {
				if($orderRes['is_renewal'] == "Y") {
					$insParams['renewal_members'] = 1;
					if(isset($insParams['credit'])) {
						$insParams['renewal_total'] = $insParams['credit'];
					} else {
						$insParams['renewal_total'] = $insParams['debit'];
					}

					$insParams['new_business_total'] = 0;
					$insParams['new_business_members'] = 0;

				} else if($orderRes['is_renewal'] == "N") {
					$insParams['new_business_members'] = 1;
					if(isset($insParams['credit'])) {
						$insParams['new_business_total'] = $insParams['credit'];
					} else {
						$insParams['new_business_total'] = $insParams['debit'];
					}
					$insParams['renewal_members'] = 0;
					$insParams['renewal_total'] = 0;
				}
			}
	
			$insId=$pdo->insert('transactions',$insParams);
	
			$transactionArray['id']=$insId;
			$transactionArray['display_id']=$displayId;
	
			if($transactionType == 'Refund Order' && !empty($otherParams['refunded_products'])){
				$extraParams['refunded_products'] = $otherParams['refunded_products'];
				$extraParams['refund_id'] = $otherParams['refund_id'];
			}
			$this->sub_transaction_insert($insId,$extraParams);

			if(!empty($transResponse)){
				$this->transaction_response_insert($insId,$transResponse);
			}

			/*---- Generate Commission ----*/
			if(in_array($transactionStatus,array("Payment Approved","Pending Settlement")) && !isset($otherParams['not_generate_commission'])) {
				add_commission_request('generate_commissions',array('order_ids' => array($orderId)));
			}
			/*----/Generate Commission ----*/

			// /*---- Insert HRM Payment*/
			// if(in_array($transactionStatus,array("Payment Approved","Pending Settlement")) && !isset($otherParams['not_generate_commission'])) {
			// 	$this->add_hrm_payments($orderId);
			// }
			// /*---- /Insert HRM Payment*/

			$this->send_Email_Threshold_and_Notification($orderRes['paymentMasterId']);
		}
	
		if($commissionId>0){
			$sqlCommission="SELECT * FROM commission_wallet_history WHERE id=:id";
			$resCommission=$pdo->selectOne($sqlCommission,array(":id"=>$commissionId));
	
			if($resCommission){
				$agentId = $resCommission['agent_id'];
				$amount = $resCommission['amount'];
				$insParams=array(
					'display_id'=>$displayId,
					'order_id'=>0,
					'customer_id'=>$agentId,
					'order_type'=>$orderType,
					'transaction_type'=>$transactionType,
					'message'=>$message,
					'commission_wallet_history_id'=>$commissionId,
					'created_at'=>'msqlfunc_NOW()',
				);
				  if(!empty($otherParams) && isset($otherParams['transaction_id'])){
					$insParams['transaction_id']=$otherParams['transaction_id'];
				}
				if($orderType=='Credit'){
					if(!empty($otherParams) && isset($otherParams['credit_amount'])){
						$insParams['credit']=$otherParams['credit_amount'];
					}else{
						$insParams['credit']=$amount;
					}
				}else{
					if(!empty($otherParams) && isset($otherParams['debit_amount'])){
						$insParams['debit']=$otherParams['debit_amount'];
					}else{
						$insParams['debit']=$amount;
					}	        	
				}
				if(!empty($otherParams) && isset($otherParams['transaction_response'])){
					$insParams['transaction_response']=json_encode($otherParams['transaction_response']);
				}
				if(!empty($otherParams) && isset($otherParams['transaction_date'])){
					$insParams['created_at']=$otherParams['transaction_date'];
				}
				$insId=$pdo->insert('transactions',$insParams);
	
				if(!empty($transResponse)){
					$this->transaction_response_insert($insId,$transResponse,$otherParams);
				}

				$transactionArray['id']=$insId;
				$transactionArray['display_id']=$displayId;
			}
		}
		return $transactionArray;
	}

	public function sub_transaction_insert($transactionId,$otherParams=array()){
		global $pdo;
		$incr = '';
		
		$transSql="SELECT id,customer_id,order_id,transaction_type FROM transactions WHERE id=:id";
		$transRes=$pdo->selectOne($transSql,array(":id"=>$transactionId));
		
		if(!empty($transRes)){
			$customerId = $transRes['customer_id'];
			$orderId = $transRes['order_id'];
			$transTblId = $transRes['id'];
	
			if($transRes['transaction_type'] == 'Refund Order' && !empty($otherParams['refunded_products'])){
					$refunded_products = (is_array($otherParams['refunded_products']) ? implode(",", $otherParams['refunded_products']) : $otherParams['refunded_products']);
					$incr .= " AND od.product_id IN($refunded_products)";
			}else{
					$incr .= " AND od.is_refund='N'";
			} 
	
			$odrSql = "SELECT o.id as odrId,od.product_id,od.id as order_detail_id
								FROM orders o
								JOIN order_details od ON(o.id=od.order_id AND od.is_deleted='N')
								WHERE o.id=:ord_id $incr GROUP BY od.id";
			   $odrParams = array(":ord_id"=>$orderId);
			   $odrRes = $pdo->select($odrSql,$odrParams);
			   if(!empty($odrRes)){
				   foreach($odrRes as $order){
					   $insParams = array(
								   "transaction_id" => $transTblId,
								   "order_id" => $orderId,
								   "order_detail_id" => $order['order_detail_id'],
								   "product_id" => $order['product_id'],
								   "created_at" => 'msqlfunc_NOW()'
								   );
	
					   if(!empty($otherParams['refund_id'])){
						   $insParams['refund_id'] = $otherParams['refund_id'];
					   }
					   $pdo->insert("sub_transactions",$insParams);
				   }
			   }
		}   
	}

	public function transaction_response_insert($insId,$transaction_response,$other_params=array()){
		global $pdo;
		if(!empty($transaction_response)){
			$transaction_response_param = array(
				'status' =>checkIsset($transaction_response['status']),
				'transaction_tbl_id' => $insId,
				'transaction_response' => json_encode($transaction_response),
			);

			if(!empty($other_params) && !empty($other_params['transaction_id'])){
				$transaction_response_param['transaction_id']=$other_params['transaction_id'];
			}

			if(isset($transaction_response['API_response']['description'])){
				$transaction_response_param['description']=$transaction_response['API_response']['description'];
			}

			if(isset($transaction_response['API_response']['error_message']) && empty($transaction_response['API_response']['description'])){
				$transaction_response_param['description']=$transaction_response['API_response']['error_message'];
			}

			if(isset($transaction_response['message']) && empty($transaction_response['API_response']['description']) && empty($transaction_response['API_response']['description'])){
				$transaction_response_param['description']=$transaction_response['message'];
			}

			if(isset($transaction_response['API_response']['auth_code'])){
				$transaction_response_param['auth_code']=$transaction_response['API_response']['auth_code'];
			}

			if(isset($transaction_response['API_response']['authcode']) && empty($transaction_response['API_response']['auth_code'])){
				$transaction_response_param['auth_code']=$transaction_response['API_response']['authcode'];
			}				
			$pdo->insert("transaction_response",$transaction_response_param);
		}
	}

	public function uspsAddressVerification($address2,$address1,$city,$state,$zip){
		global $USPS_USER_ID,$allStateShortName;
		
		if(!empty($state) && strlen($state) > 2){
			$state = $allStateShortName[$state];
		}
		
		$xml_data = "<AddressValidateRequest USERID='$USPS_USER_ID'>" .
		"<Revision>1</Revision>".
		"<Address ID='0'>" .
		"<Address1>$address1</Address1>" .
		"<Address2>$address2</Address2>".
		"<City>$city</City>" .
		"<State>$state</State>" .
		"<Zip5>$zip</Zip5>" .
		"<Zip4></Zip4>" .
		"</Address>" .
		"</AddressValidateRequest>";


		$url = "http://production.shippingapis.com/ShippingAPI.dll?API=Verify";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
		            'XML=' . $xml_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		$output = curl_exec($ch);
		echo curl_error($ch);
		curl_close($ch);

		$array_data = json_decode(json_encode(simplexml_load_string($output)), true);

		$response = array();
		$response['status']='fail';
		$response['error_message']='No Address Found';
		if(!empty($array_data['Address'])){
			if(!empty($array_data['Address']['Error'])){
				$response['status']='fail';
				$response['error_message']=$array_data['Address']['Error']['Description'];
			}else{
				$response['status']='success';
				$response['address']=$array_data['Address']['Address2'];
				$response['address2']=!empty($array_data['Address']['Address1']) && $array_data['Address']['Address1'] != "#" ? $array_data['Address']['Address1'] : '';
				$response['city']=$array_data['Address']['City'];
				$response['state']=$array_data['Address']['State'];
				$response['zip']=$array_data['Address']['Zip5'];
				unset($response['error_message']);
			}
			
		}
		return $response;
	}

	public function uspsCityVerification($zip){
		global $USPS_USER_ID;
		$xml_data = "<CityStateLookupRequest USERID='$USPS_USER_ID'>" .
			"<ZipCode>" .
			"<Zip5>$zip</Zip5>" .
			"</ZipCode>" .
			"</CityStateLookupRequest>";

			$url = "http://production.shippingapis.com/ShippingAPI.dll?API=CityStateLookup";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
			            'XML=' . $xml_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
			$output = curl_exec($ch);
			echo curl_error($ch);
			curl_close($ch);

			$array_data = json_decode(json_encode(simplexml_load_string($output)), true);
			$response = array();
			$response['status']='fail';
			$response['error_message']='';
			if(!empty($array_data['ZipCode'])){
				if(!empty($array_data['ZipCode']['Error'])){
					$response['status']='fail';
					$response['error_message']=$array_data['ZipCode']['Error']['Description'];
				}else{
					$response['status']='success';
					$response['city']=$array_data['ZipCode']['City'];
					$response['state']=$array_data['ZipCode']['State'];
					$response['zip']=$array_data['ZipCode']['Zip5'];
					unset($response['error_message']);
				}
				
			}
			return $response;
	}
	public function get_updated_payment_field($payment_processor_id, $is_new_record, $updatedFields, $databaseFields) {
		global $pdo;
		$REAL_IP_ADDRESS = get_real_ipaddress();
		$result = array_diff_assoc($updatedFields, $databaseFields);
		if (!empty($result['created_at'])) {
			unset($result['created_at']);
		}
		if (!empty($result['updated_at'])) {
			unset($result['updated_at']);
		}
		if (!empty($result['ip_address'])) {
			unset($result['ip_address']);
		}
		if (!empty($result["id"])) {
			unset($result["id"]);
		}
	
		$updated_field = $result;
		$updated_field = json_encode($updated_field);
	
		$update_param = array(
			'payment_processor_id' => $payment_processor_id,
			'admin_id' => $_SESSION['admin']['id'],
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
			'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'updated_at' => 'msqlfunc_NOW()',
			'created_at' => 'msqlfunc_NOW()',
			'is_record_inserted' => $is_new_record,
		);
		if($is_new_record == 'Y'){
			$update_param['insert_param'] = json_encode($updatedFields);
		} else {
			$update_param['old_param'] = json_encode($databaseFields);
			$update_param['new_param'] = json_encode($updatedFields);
			$update_param['only_updated_value'] = json_encode($updated_field);
		}
		$pdo->insert("payment_master_log", $update_param);
	}
	 /*   * ***********************************************
		* email and notification code Start
		* *********************************************** */
	private function send_Email_Threshold_and_Notification($payment_master_id) {
		global $pdo,$SITE_ENV;

		$total_amount =  $total_approved = $total_chargeback = $total_refund_void  = 0.00;
		$approved_parcentage = $refund_void_percentage = $chargeback_percentage = 0 ;

		$sales_threshold_value = $refund_threshold_value = $chargeback_threshold_value = $monthly_threshold_sale = 0;

		$payment_master = $pdo->selectOne("SELECT * FROM payment_master where id=:id and is_deleted='N'",array(":id"=>$payment_master_id));

		if(!empty($payment_master['monthly_threshold_sale'])){
		$monthly_threshold_sale = $payment_master['monthly_threshold_sale'];
		$sales_threshold_value = $payment_master['sales_threshold_value'];
		$refund_threshold_value = $payment_master['refund_threshold_value'];
		$chargeback_threshold_value = $payment_master['chargeback_threshold_value'];
		}
		$approved_order_total = $pdo->selectOne("SELECT count(DISTINCT(order_id)) as total_count, sum(credit) as total_credit, sum(debit) as total_debit FROM transactions as t WHERE payment_master_id = :payment_master_id AND transaction_type IN ('New Order','Renewal Order','List Bill Order') AND Date(created_at) >= :from_date AND Date(created_at) <= :to_date", array(":payment_master_id" => $payment_master_id,":from_date" => date("Y-m-01"),":to_date" => date("Y-m-t")));
		if(!empty($approved_order_total['total_credit'] > 0)){
		$total_approved =  abs($approved_order_total['total_credit'] - $approved_order_total['total_debit']);
		}
		$chargeback_order_total = $pdo->selectOne("SELECT count(DISTINCT(order_id)) as total_count, sum(credit) as total_credit, sum(debit) as total_debit FROM transactions as t WHERE payment_master_id = :payment_master_id AND transaction_type IN ('Chargeback') AND Date(created_at) >= :from_date AND Date(created_at) <= :to_date", array(":payment_master_id" => $payment_master_id,":from_date" => date("Y-m-01"),":to_date" => date("Y-m-t")));
		if(!empty($chargeback_order_total['total_credit'] > 0)){
		$total_chargeback = abs($chargeback_order_total['total_credit'] - $chargeback_order_total['total_debit']);
		}


		$refund_void_order_total = $pdo->selectOne("SELECT count(DISTINCT(order_id)) as total_count, sum(credit) as total_credit, sum(debit) as total_debit FROM transactions as t WHERE payment_master_id = :payment_master_id AND transaction_type IN ('Refund Order') AND Date(created_at) >= :from_date AND Date(created_at) <= :to_date", array(":payment_master_id" => $payment_master_id,":from_date" => date("Y-m-01"),":to_date" => date("Y-m-t")));
		if(!empty($refund_void_order_total['total_credit'] > 0)){
		$total_refund_void = abs($refund_void_order_total['total_credit'] - $refund_void_order_total['total_debit']);
		}

		if(($total_approved > 0  || $total_refund_void > 0 || $total_chargeback > 0)  && !empty($monthly_threshold_sale) && ($monthly_threshold_sale > 0)){

		$approved_parcentage = ($total_approved * 100) / $monthly_threshold_sale;
		$refund_void_percentage = ($total_refund_void * 100) / $monthly_threshold_sale;
		$chargeback_percentage = ($total_chargeback * 100) / $monthly_threshold_sale;
		}

		$mail_data['fname'] = '';
		$mail_data['lname'] = '';
		$mail_data['Email'] = '';
		$mail_data['Phone'] = '';
		$mail_data['Agent'] = '';
		$mail_data['Parent Agent'] = '';
		$mail_data['MemberID'] = '';
		$mail_data['ActiveProducts'] = '';

		if($approved_parcentage > 0 && $approved_parcentage >= $sales_threshold_value && $payment_master['is_sales_threshold'] =='Y'){
		$trigger_id = 85 ;//sales threshold trigger id

		$sales_email = $pdo->selectOne("SELECT to_email_specific,cc_email_specific,bcc_email_specific,to_email_user,to_email_user,bcc_email_user,to_phone_specific FROM triggers WHERE id=:id and is_deleted='N' and status='Active'",array(":id"=>$trigger_id));

		$salesEmail[] = !empty($sales_email['to_email_specific']) ? $sales_email['to_email_specific'] : '';
		$salesEmail[] = !empty($sales_email['cc_email_specific']) ? $sales_email['cc_email_specific'] : '';
		$salesEmail[] = !empty($sales_email['bcc_email_specific']) ? $sales_email['bcc_email_specific'] : '';
		$salesEmail[] = !empty($sales_email['to_email_user']) ? $sales_email['to_email_user'] : '';
		$salesEmail[] = !empty($sales_email['to_email_user']) ? $sales_email['to_email_user'] : '';
		$salesEmail[] = !empty($sales_email['bcc_email_user']) ? $sales_email['bcc_email_user'] : '';
		$salesPhone = !empty($sales_email['to_phone_specific']) ? $sales_email['to_phone_specific'] : '';
		
		addAdminNotification(0,10,"{HOST}/add_merchant_processor.php?type=".$payment_master['type']."&id=" . md5($payment_master_id),0,'Y');
		if(!empty($salesEmail))
			trigger_mail($trigger_id, $mail_data, $salesEmail, array(), 3);
		if(!empty($salesPhone))
			trigger_sms($trigger_id, $salesPhone ,$mail_data, array(), 3);
		}

		if($refund_void_percentage > 0 && $refund_void_percentage >= $refund_threshold_value && $payment_master['is_refund_threshold'] =='Y'){
		$trigger_id = 86 ;//refund threshold trigger id

		$refund_email = $pdo->selectOne("SELECT to_email_specific,cc_email_specific,bcc_email_specific,to_email_user,to_email_user,bcc_email_user,to_phone_specific FROM triggers WHERE id=:id and is_deleted='N' and status='Active'",array(":id"=>$trigger_id));

		$refundEmail[] = !empty($refund_email['to_email_specific']) ? $refund_email['to_email_specific'] : '';
		$refundEmail[] = !empty($refund_email['cc_email_specific']) ? $refund_email['cc_email_specific'] : '';
		$refundEmail[] = !empty($refund_email['bcc_email_specific']) ? $refund_email['bcc_email_specific'] : '';
		$refundEmail[] = !empty($refund_email['to_email_user']) ? $refund_email['to_email_user'] : '';
		$refundEmail[] = !empty($refund_email['to_email_user']) ? $refund_email['to_email_user'] : '';
		$refundEmail[] = !empty($refund_email['bcc_email_user']) ? $refund_email['bcc_email_user'] : '';
		$refundPhone = !empty($refund_email['to_phone_specific']) ? $refund_email['to_phone_specific'] : '';

		addAdminNotification(0,11,"{HOST}/add_merchant_processor.php?type=".$payment_master['type']."&id=" . md5($payment_master_id),0,'Y');
		if(!empty($refundEmail))
			trigger_mail($trigger_id, $mail_data, $refundEmail, array(), 3);
		if(!empty($refundPhone))
			trigger_sms($trigger_id, $refundPhone ,$mail_data, array(), 3);
		}

		if($chargeback_percentage > 0 && $chargeback_percentage >= $chargeback_threshold_value && $payment_master['is_chargeback_threshold'] =='Y'){
		$trigger_id = 87 ;//chargeback threshold trigger id

		$chargeback_email = $pdo->selectOne("SELECT to_email_specific,cc_email_specific,bcc_email_specific,to_email_user,to_email_user,bcc_email_user,to_phone_specific FROM triggers WHERE id=:id and is_deleted='N' and status='Active'",array(":id"=>$trigger_id));

		$chargebackEmail[] = !empty($chargeback_email['to_email_specific']) ? $chargeback_email['to_email_specific'] : '';
		$chargebackEmail[] = !empty($chargeback_email['cc_email_specific']) ? $chargeback_email['cc_email_specific'] : '';
		$chargebackEmail[] = !empty($chargeback_email['bcc_email_specific']) ? $chargeback_email['bcc_email_specific'] : '';
		$chargebackEmail[] = !empty($chargeback_email['to_email_user']) ? $chargeback_email['to_email_user'] : '';
		$chargebackEmail[] = !empty($chargeback_email['to_email_user']) ? $chargeback_email['to_email_user'] : '';
		$chargebackEmail[] = !empty($chargeback_email['bcc_email_user']) ? $chargeback_email['bcc_email_user'] : '';
		$chargebackPhone = !empty($chargeback_email['to_phone_specific']) ? $chargeback_email['to_phone_specific'] : '';

		addAdminNotification(0,12,"{HOST}/add_merchant_processor.php?type=".$payment_master['type']."&id=" . md5($payment_master_id),0,'Y');
		if(!empty($chargebackEmail))
			trigger_mail($trigger_id, $mail_data, $chargebackEmail, array(), 3);
		if(!empty($chargebackPhone))
			trigger_sms($trigger_id, $chargebackPhone ,$mail_data, array(), 3);
		}

	}
	/*   * ***********************************************
	* email and notification code End
	* *********************************************** */

	public function generateEticketTrackingId() {
		global $pdo;
		$rule_code = "E-".rand(100000, 999999);
		
		$sql = "SELECT count(id) as total FROM s_ticket WHERE tracking_id = '" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateEticketTrackingId();
		} else {
			return $rule_code;
		}
	}

	public function createNewTicket($sessionArr = array() , $categoryId , $subject = "" , $assigne_admins = 0 ,  $ticketDesc = "", $userId = 0 , $userType = "" ,$ip_address = '' , $docFile = array(),$type = "reply",$website_id=0) {
		global $pdo,$ETICKET_DOCUMENT_DIR,$ADMIN_HOST,$AGENT_HOST,$CUSTOMER_HOST,$GROUP_HOST;
		$returnArr = array();
		$s_ticket_insParam = array(
			'group_id' => $categoryId,
			'tracking_id' => $this->generateEticketTrackingId(),
			'user_id' => $userId,
			'website_id' =>$website_id,
			'user_type' => $userType,
			'subject' => $subject,
			'is_assigned' => $assigne_admins!=0 ? 'Y' : 'N',
			'assigned_admin_id' => $assigne_admins,
			'ip_address' => $ip_address,
		);
		$s_ticket_insId = $pdo->insert('s_ticket',$s_ticket_insParam);
		$returnArr['ticket_id'] = $s_ticket_insId;
		$s_ticket_msg_insParam = array(
			'ticket_id' => $s_ticket_insId,
			'user_id' => $userId,
			'user_type' => $userType,
			'type' => $type,
			'message' => $ticketDesc,
			'ip_address' => $ip_address,
		);
	
		$s_ticket_message_insId = $pdo->insert('s_ticket_message',$s_ticket_msg_insParam);
		$returnArr['s_ticket_message_id'] = $s_ticket_message_insId;
		if(!empty($docFile) && !empty($docFile['name'])){
			$s_ticket_msg_fileinsParam = array(
				'ticket_id' => $s_ticket_insId,
				'message_id' => $s_ticket_message_insId,
				'file' => date('mdYhisa').$docFile['name'],
				'file_name' =>$docFile['name'],
			);
			$pdo->insert('s_ticket_message_files',$s_ticket_msg_fileinsParam);
			$name = basename($s_ticket_msg_fileinsParam['file']);
			move_uploaded_file($docFile['tmp_name'], $ETICKET_DOCUMENT_DIR.$name);
		}

		$user_id = $user_type = $rep_id = $user_url = '';
		if(!empty($sessionArr['admin'])){
			$user_url = $ADMIN_HOST.'/admin_profile.php?id='.md5($sessionArr['admin']['id']);
			$rep_id = $sessionArr['admin']['display_id'];
			$user_id = $sessionArr['admin']['id'];
			$user_type = 'Admin';
		}elseif(!empty($sessionArr['agents'])){
			$user_url = $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($sessionArr['agents']['id']);
			$rep_id = $sessionArr['agents']['rep_id'];
			$user_id = $sessionArr['agents']['id'];
			$user_type = 'Agent';
		}elseif(!empty($sessionArr['customer'])){
			$user_url = $ADMIN_HOST.'/members_details.php?id='.md5($sessionArr['customer']['id']);
			$rep_id = $sessionArr['customer']['rep_id'];
			$user_id = $sessionArr['customer']['id'];
			$user_type = 'Customer';
		}elseif(!empty($sessionArr['group'])){
			$user_url = $ADMIN_HOST.'/groups_details.php?id='.md5($sessionArr['group']['id']);
			$rep_id = $sessionArr['group']['rep_id'];
			$user_id = $sessionArr['group']['id'];
			$user_type = 'Group';
		}elseif(!empty($sessionArr['System'])){
			$user_url = '';
			$rep_id = 'System';
			$user_id = 0;
			$user_type = 'System';
		}

		$userArr = array();
		$entity_type  = $entity_url = '';
		$entity_id = $userId;
		$description['ac_message'] = array(
			'ac_red_1'=>array(
				'href'=>$user_url,
				'title'=>$rep_id,
			),
			'ac_message_1' =>' Created E-Ticket ',
			'ac_red_2'=>array(
				'title'=> $s_ticket_insParam['tracking_id'],
			)
		);

		if(!empty($sessionArr['admin'])){
			if($userType == 'Admin'){
				$userArr = $pdo->selectOne("SELECT fname,lname,display_id as rep_id,id from admin where is_deleted='N' and id=:id",array(":id"=>$entity_id));
				$entity_url = $ADMIN_HOST.'/admin_profile.php?id='.md5($entity_id);
				$entity_type = 'Admin';
			}else{
				$userArr = $pdo->selectOne("SELECT fname,lname,rep_id,id from customer where is_deleted='N' and id=:id and type=:type",array(":id"=>$entity_id,":type"=>$userType));
	
				if($userType == 'Agent'){
					$entity_type = 'Agent';
					$entity_url = $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($entity_id);
				}elseif($userType == 'customer'){
					$entity_type = 'customer';
					$entity_url = $ADMIN_HOST.'/members_details.php?id='.md5($entity_id);
				}elseif($userType == 'Group'){
					$entity_type = 'Group';
					$entity_url = $ADMIN_HOST.'/groups_details.php?id='.md5($entity_id);
				}
			}
			

			if($entity_type == ''){
				$entity_type = $userType;
			}
			$description['ac_message']['ac_message_2'] = ' For '.$userType.' '.$userArr['fname'].' '.$userArr['lname'].' (';
			$description['ac_message']['ac_red_3'] = array('href'=>$entity_url,'title'=> $userArr['rep_id']);
			$description['ac_message']['ac_message_3'] = ').';
		}

		activity_feed(3,$user_id, $user_type , $entity_id ,$entity_type , 'E-Ticket Created',"","",json_encode($description));
		return $returnArr;
	}

	public function create_reorder($order_id, $customer_id, $cust_billing_id, $payment_date, $is_eligibility_date_update, $params,$order_display_id = '',$other_params = array()){

		global $pdo,$CREDIT_CARD_ENC_KEY,$ADMIN_HOST;

		$order_type = "Post Payment";
		$sponsor_id = getname("customer", $customer_id, 'sponsor_id', 'id');
		$sponsor_sql = "SELECT id,type,upline_sponsors,level,payment_master_id,ach_master_id FROM customer WHERE type!='Customer' AND id = :sponsor_id ";
		$sponsor_row = $pdo->selectOne($sponsor_sql, array(':sponsor_id' => $sponsor_id));
		$is_selected_service_fee_product = false;

		$service_fee_products = $pdo->selectOne("SELECT GROUP_CONCAT(p.id) as ids,sum(price) as service_fee from prd_main p JOIN prd_matrix pm ON(pm.product_id=p.id and pm.is_deleted='N')  WHERE pm.id IN(".implode(',',array_keys($params)).") AND p.is_deleted='N' AND type='Fees' AND product_type='ServiceFee'");

		$healthy_step_fee_products = $pdo->selectOne("SELECT GROUP_CONCAT(p.id) as ids,sum(price) as healthy_fee from prd_main p JOIN prd_matrix pm ON(pm.product_id=p.id and pm.is_deleted='N')  WHERE pm.id IN(".implode(',',array_keys($params)).") AND p.is_deleted='N' AND type='Fees' AND product_type='Healthy Step'");

		$selected_products = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(p.name,' Price : ',price) SEPARATOR '<br>') as products from prd_main p JOIN prd_matrix pm ON(pm.product_id=p.id and pm.is_deleted='N')  WHERE pm.id IN(".implode(',',array_keys($params)).") AND p.is_deleted='N' AND product_type!='ServiceFee'");
		
		require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
		require_once dirname(__DIR__) . '/includes/member_setting.class.php';
	
		$enrollDate = new enrollmentDate();
		$memberSetting = new memberSetting();
	
		$BROWSER = getBrowser();
		$OS = getOS($_SERVER['HTTP_USER_AGENT']);
		$billing_desc = '';
	
		$REAL_IP_ADDRESS = get_real_ipaddress();
		if($payment_date != ''){
			$payment_date = date("Y-m-d", strtotime($payment_date));
		}
	
		if(strtotime(date('Y-m-d',strtotime($payment_date))) == strtotime(date('Y-m-d'))) {
			$order_type = "Attempt Order";
		}

		// Insert Into Orders
			$old_orders_res = $pdo->selectOne("SELECT * FROM orders WHERE id = :id", array(":id" => $order_id));
			$order_details_count = $pdo->selectOne("SELECT count(id) as prd_count FROM order_details WHERE order_id = :order_id AND is_deleted='N'", array(":order_id" => $order_id));
		
			$customer_billing_res = $pdo->selectOne("SELECT *,AES_DECRYPT(ach_account_number,'".$CREDIT_CARD_ENC_KEY."') as decrypt_ach_account_number FROM customer_billing_profile WHERE id = :id AND customer_id = :customer_id and is_deleted='N'", array(":id" => $cust_billing_id, ":customer_id" => $customer_id));

			$payment_master_id = $sponsor_row['payment_master_id'];
			$payment_master_id = $this->get_agent_merchant_detail(array_keys($params),$sponsor_id,$customer_billing_res['payment_mode'],array('is_renewal' => $old_orders_res['is_renewal'],'customer_id' => $customer_id));
			
			if(!empty($payment_master_id)){
				$payment_processor = getname('payment_master',$payment_master_id,'processor_id');
			}

			$order_param = array(
				'display_id' => !empty($order_display_id) ? $order_display_id : $this->get_order_id(),
				'customer_id' => $customer_id,
				'company_id' => $old_orders_res['company_id'],
				'user_type' => $old_orders_res['user_type'],
				'admin_id' => (isset($_SESSION['admin']['id'])?$_SESSION['admin']['id']:0),
				'is_added_by_admin' => "Y",
				'status' => "Post Payment",
				'type' => $old_orders_res['type'],
				'payment_type' => $customer_billing_res['payment_mode'],
				'payment_processor' => $payment_processor,
				'payment_master_id' => $payment_master_id,
				'is_renewal' => $old_orders_res['is_renewal'],
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'browser' => $BROWSER,
				'os' => $OS,
				'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
				'post_date' => $payment_date,
				'future_payment' => 'Y',
				'is_regenerated_order' => 'Y',
				'order_id_from_regenerated' => $old_orders_res['id'],
				'transaction_id' => 0,
			);
			$new_order_id = $pdo->insert('orders', $order_param);
		// Insert Into Orders
		
		// Insert Into Order billing profile
			$order_billing_info_params = array(
				'order_id' => $new_order_id,
				'customer_id' => $customer_id,
				'fname' => $customer_billing_res['fname'],
				'lname' => $customer_billing_res['lname'],
				'email' => $customer_billing_res['email'],
				'country' => $customer_billing_res['country'],
				'country_id' => $customer_billing_res['country_id'],
				'state' => $customer_billing_res['state'],
				'city' => $customer_billing_res['city'],
				'zip' => $customer_billing_res['zip'],
				'phone' => $customer_billing_res['phone'],
				'address' => $customer_billing_res['address'],
				'payment_mode' => $customer_billing_res['payment_mode'],
				'customer_billing_id' => $customer_billing_res['id'],
				'last_cc_ach_no' => $customer_billing_res['last_cc_ach_no'],
				'created_at' => 'msqlfunc_NOW()',
			);

			if($customer_billing_res['payment_mode'] == 'CC'){
				$order_billing_info_params['card_no'] = $customer_billing_res['card_no'];
				$order_billing_info_params['card_no_full'] = $customer_billing_res['card_no_full'];
				$order_billing_info_params['card_type'] = $customer_billing_res['card_type'];
				$order_billing_info_params['expiry_month'] = $customer_billing_res['expiry_month'];
				$order_billing_info_params['expiry_year'] = $customer_billing_res['expiry_year'];
				$billing_desc = $customer_billing_res['payment_mode'] . " (" . $customer_billing_res['card_type']. " *".$customer_billing_res['card_no'].")"; 
				
				$payment_master_id = $sponsor_row['payment_master_id'];
			} else {
				$order_billing_info_params['bankname'] = $customer_billing_res['bankname'];
				$order_billing_info_params['ach_account_number'] = $customer_billing_res['ach_account_number'];
				$order_billing_info_params['ach_routing_number'] = $customer_billing_res['ach_routing_number'];
				$order_billing_info_params['ach_account_type'] = $customer_billing_res['ach_account_type'];
				$billing_desc = $customer_billing_res['payment_mode'] . ' ( *'. substr($customer_billing_res['decrypt_ach_account_number'], -4,4) .")"; 
				$payment_master_id = $sponsor_row['ach_master_id'];
			}
		
			$order_billing_id = $pdo->insert("order_billing_info", $order_billing_info_params);
		// Insert Into Order billing profile
	
		$coverage_dates = array();
		$sub_arr = array();
		$product_ids= array();
		$is_mec_enrollment_fee  = true;
		$is_bid_enrollment_fee  = true;
		$ord_count = 0;
		$member_setting = $memberSetting->get_status_by_payment("","",true);
		if(count($params) > 0){
			if($is_eligibility_date_update == 'N'){
				foreach ($params as $key => $value) {
					if($value != ''){
						$coverage_dates[$key] = $value;
					}
				}
				$lowest_coverage_date = $enrollDate->getLowestCoverageDate($coverage_dates);
			}
			$product_total = 0;
			foreach ($params as $key => $value) {
				if($value != ''){
					$ord_count++;
					$ws_row = $pdo->selectOne("SELECT id, product_id,fee_applied_for_product,plan_id,prd_plan_type_id FROM website_subscriptions WHERE customer_id = :customer_id AND plan_id = :plan_id", array(':customer_id' => $customer_id, ':plan_id' => $key));
					if($ws_row){

						array_push($sub_arr, $ws_row['id']);
						array_push($product_ids, $ws_row['product_id']);
						// Order Details 
							$old_order_details_res = $pdo->selectOne("SELECT * FROM order_details WHERE order_id = :order_id AND plan_id = :plan_id AND is_deleted='N'", array(":order_id" => $order_id, ':plan_id' => $key));
						// Order Details 
	
						$product_total = $product_total + $old_order_details_res['unit_price'];
	
						// customer dependent details
							$customer_dependent_res = $pdo->select("SELECT * FROM customer_dependent WHERE order_id = :order_id AND product_plan_id = :plan_id AND customer_id = :customer_id", array(":order_id" => $order_id, ':plan_id' => $key, ':customer_id' => $customer_id));
		
							if(count($customer_dependent_res) > 0){
								foreach ($customer_dependent_res as $index => $ele) {
									$update_cust_dep = array(
										"status" => $member_setting['member_status'],
										"order_id" => $new_order_id,
										'updated_at' => 'msqlfunc_NOW()',
									);
									
									$update_cust_dep_where = array("clause" => " product_plan_id=:product_plan_id AND customer_id=:customer_id AND id=:id", 'params' => array(':product_plan_id' => $key,':customer_id'=>$customer_id,":id" => $ele['id']));
		
									$pdo->update("customer_dependent", $update_cust_dep, $update_cust_dep_where);
								}
		
							}
						// customer dependent details
	
						// Website subscription update param
							$update_web_data = array(
								"status" => $member_setting['policy_status'],
								"total_attempts" => 0,
								"last_order_id" => $new_order_id,
								'payment_type' => $customer_billing_res['payment_mode'],
								'updated_at' => 'msqlfunc_NOW()',
							);
		
							$update_web_where = array("clause" => "id=:subscription_id AND customer_id=:customer_id", 'params' => array(':subscription_id' => $ws_row['id'],':customer_id'=>$customer_id));
							$pdo->update("website_subscriptions", $update_web_data, $update_web_where);
						// Website subscription update param
	
						// Order details insert param
							$insert_order_details = array(
								'order_id' => $new_order_id,
								'website_id' => $ws_row['id'],
								'product_id' => $old_order_details_res['product_id'],
								'fee_applied_for_product' => $old_order_details_res['fee_applied_for_product'],
								'plan_id' => $old_order_details_res['plan_id'],
								'prd_plan_type_id' =>  $old_order_details_res['prd_plan_type_id'],
								'product_type' => $old_order_details_res['product_type'],
								'product_name' => $old_order_details_res['product_name'],
								'unit_price' => $old_order_details_res['unit_price'],
								'product_code' => $old_order_details_res['product_code'],
								'qty' => 1,
								'start_coverage_period' => $old_order_details_res['start_coverage_period'],
								'end_coverage_period' => $old_order_details_res['end_coverage_period'],
								'family_member' => $old_order_details_res['family_member'],
								'admin_id' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0),
								'renew_count' => 'msqlfunc_renew_count + 1',
							);
						// Order details insert param
						if($is_eligibility_date_update == 'N'){
							$member_payment_type = getname('prd_main',$old_order_details_res['product_id'],'member_payment_type','id');
	
							$product_dates=$enrollDate->getCoveragePeriod($coverage_dates[$key],$member_payment_type);
	
							$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
							$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
							$eligibility_date = date('Y-m-d',strtotime($product_dates['eligibility_date']));
	
							// website subscription
								$update_web_data['eligibility_date'] = $eligibility_date;
								$update_web_data['start_coverage_period'] = $startCoveragePeriod;
								$update_web_data['end_coverage_period'] = $endCoveragePeriod;
							// website subscription
	
							// order details param
								$insert_order_details['start_coverage_period'] = $startCoveragePeriod;
								$insert_order_details['end_coverage_period'] = $endCoveragePeriod;
							// order details param
						}
	
						// update website subscription
							$update_web_where = array("clause" => "id=:subscription_id AND customer_id=:customer_id", 'params' => array(':subscription_id' => $ws_row['id'],':customer_id'=>$customer_id));
							$pdo->update("website_subscriptions", $update_web_data, $update_web_where);
						// update website subscription
						if($order_type != '' && $order_type == "Post Payment"){
								// insert into website_subscriptions_history 
								$web_history_data = array(
									'customer_id' => $customer_id,
									'website_id' => $ws_row['id'],
									'admin_id' => (isset($_SESSION['admin']['id'])?$_SESSION['admin']['id']:0),
									'product_id' => $ws_row['product_id'],
									'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
									'plan_id' => $ws_row['plan_id'],
									'prd_plan_type_id' =>  $ws_row['prd_plan_type_id'],
									'order_id' => $order_id,
									'status' => 'Setup',
									'message' => 'Initial Setup Successful With Post Date' . date("m/d/Y", strtotime($payment_date)),
									'processed_at' => 'msqlfunc_NOW()',
									'created_at' => 'msqlfunc_NOW()',
								);
								$pdo->insert("website_subscriptions_history", $web_history_data);
						}
						// insert into order details
						$pdo->insert("order_details", $insert_order_details);
						// insert into order details
					}
				}
			}
		}
		$fee_product_total = ($service_fee_products['service_fee']!=0 ? $service_fee_products['service_fee'] : 0)  + 				($healthy_step_fee_products['healthy_fee']!=0 ? $healthy_step_fee_products['healthy_fee'] : 0) ;
	
		// Update orders start
			$sub_total = $product_total - $fee_product_total ;
			$grand_total = $product_total ;
			$order_update_param = array(
				'product_total' => $sub_total,
				'sub_total' => $sub_total,
				'grand_total' => $grand_total,
				'subscription_ids' => implode(',', $sub_arr)
			);
			$order_where = array("clause" => "id=:id AND customer_id=:customer_id", 'params' => array(':id' => $new_order_id,':customer_id'=>$customer_id));
			$pdo->update("orders", $order_update_param, $order_where);
		// Update orders end
	
		//update next purchase date code start     
			$enrollDate->updateNextBillingDateByOrder($new_order_id);
		//update next purchase date code end
	
		if($order_type != '' && $order_type == "Post Payment"){
			$transactionInsId = $this->transaction_insert($new_order_id,'Credit','Pending','Post Transaction');
	
			$update_customer_where = array(
				'clause' => 'id=:id',
				'params' => array(
					':id' => $customer_id,
				),
			);
		
			$current_member_status = getname('customer',$customer_id,'status','id');

			$member_setting = $memberSetting->get_status_by_payment("","",true,$current_member_status);

			$update_customer_data = array('status' => $member_setting['member_status'], 'updated_at' => 'msqlfunc_NOW()');
			$pdo->update('customer', $update_customer_data, $update_customer_where);
		}

		$post_payment = true;
		if(strtotime(date('Y-m-d',strtotime($payment_date))) == strtotime(date('Y-m-d'))) {
			$extra_params = !empty($cust_billing_id) ? array("billing_id"=>$cust_billing_id) : array();
			$order_billing_res = $this->take_order_billing($new_order_id,$extra_params);
			if($order_billing_res['status'] == true) {
				$response['status'] = 'payment_success';
				setNotifySuccess($order_billing_res['message']);
			} else {
				$response['status'] = $order_billing_res['error_status'];
				setNotifyError($order_billing_res['message']);
			}
			$post_payment = false;
		}
		$other_params['location'] = isset($other_params['location'])?$other_params['location']:'admin';

		$activity_feed_data = array();
		if($post_payment){
			$activity_feed_data['ac_description_post_paymnet'] = 'Post payment on : '.getCustomDate($payment_date);
		}

		if(!empty($selected_products['products'])) {
			$activity_feed_data['ac_description_products']  = "Products : <br>".$selected_products['products'];
		}

		$customer_sql = "SELECT fname,lname,rep_id,id FROM customer  WHERE id=:customer_id";
		$customer_where = array(":customer_id" => $customer_id);
		$customer_row = $pdo->selectOne($customer_sql, $customer_where);
		
		if($other_params['location'] == "admin") {
			$activity_feed_data['ac_message'] = array(
				'ac_red_1'=>array(
					'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
					'title'=>$_SESSION['admin']['display_id'],
				),
				'ac_message_1' =>' Regenerate order ',
				'ac_red_2'=>array(
					// 'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$order_param['display_id'],
				),
				'ac_message_2' =>' for Member ',
				'ac_red_3'=>array(
					'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$customer_row['rep_id'],
				),
			);
			activity_feed(3, $_SESSION['admin']['id'], "Admin", $customer_id, 'Customer', 'Regenerate Order', $customer_row['fname'], $customer_row['lname'],json_encode($activity_feed_data));

		} elseif($other_params['location'] == "agent") {
			$activity_feed_data['ac_message'] = array(
				'ac_red_1'=>array(
					'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
				),
				'ac_message_1' =>' Regenerate order ',
				'ac_red_2'=>array(
					// 'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$order_param['display_id'],
				),
				'ac_message_2' =>' for Member ',
				'ac_red_3'=>array(
					'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$customer_row['rep_id'],
				),
			);
			activity_feed(3, $_SESSION['agents']['id'], "Agent", $customer_id, 'Customer', 'Regenerate Order', $customer_row['fname'], $customer_row['lname'],json_encode($activity_feed_data));
		}elseif($other_params['location'] == "group") {
			$activity_feed_data['ac_message'] = array(
				'ac_red_1'=>array(
					'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=> $_SESSION['groups']['rep_id'],
				),
				'ac_message_1' =>' Regenerate order ',
				'ac_red_2'=>array(
					// 'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$order_param['display_id'],
				),
				'ac_message_2' =>' for Member ',
				'ac_red_3'=>array(
					'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$customer_row['rep_id'],
				),
			);
			activity_feed(3, $_SESSION['groups']['id'], "Group", $customer_id, 'Customer', 'Regenerate Order', $customer_row['fname'], $customer_row['lname'],json_encode($activity_feed_data));
		}	
	}

	public function take_order_billing($order_id,$extra_params = array()) {
		global $pdo, $CREDIT_CARD_ENC_KEY,$SITE_ENV,$ADMIN_HOST;
		
		$response = array();
		$BROWSER = getBrowser();
		$OS = getOS($_SERVER['HTTP_USER_AGENT']);
		$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		require_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
		require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
		require_once dirname(__DIR__) . '/includes/member_setting.class.php';
		require_once dirname(__DIR__) . '/includes/policy_setting.class.php';
	
		$enrollDate = new enrollmentDate();
		$memberSetting = new memberSetting();
		$policySetting = new policySetting();

		$REAL_IP_ADDRESS = get_real_ipaddress();
		$BROWSER = empty($BROWSER) ? 'System' : $BROWSER;
		$OS = empty($OS) ? 'System' : $OS;
		$REQ_URL = empty($REQ_URL) ? 'System' : $REQ_URL;
		$today = date('Y-m-d');
		$order_sql = "SELECT o.id,customer_id,subscription_ids,is_renewal,grand_total,sub_total,display_id FROM orders o WHERE o.id=:order_id";
		$order_where = array(":order_id" => $order_id);
		$order_row = $pdo->selectOne($order_sql, $order_where);
		
		if (empty($order_row) || empty($order_row['subscription_ids'])) {
			$response['status'] = false;
			$response['error_status'] = "order_not_found";
			$response['message'] = "Oops, Order not found!!!";
			return $response;
		}
	
		$customer_id = $order_row['customer_id'];
		$customer_sql = "SELECT fname,lname,id,rep_id,email,sponsor_id,cell_phone,type,status FROM customer  WHERE id=:customer_id";
		$customer_where = array(":customer_id" => $customer_id);
		$customer_row = $pdo->selectOne($customer_sql, $customer_where);
		
		$sponsor_id = $customer_row['sponsor_id'];
	
		$sponsor_sql = "SELECT id,type FROM customer WHERE type!='Customer' AND id = :sponsor_id ";
		$sponsor_row = $pdo->selectOne($sponsor_sql, array(':sponsor_id' => $sponsor_id));
	
		$billing_id = !empty($extra_params['billing_id']) ? $extra_params['billing_id'] : '';
		$bill_incr = '';
		$sch_bill_incr = array();
		if(!empty($billing_id)){
			$bill_incr = " AND id=:id AND customer_id=:customer_id ";
			$sch_bill_incr = array(":id"=>$billing_id,":customer_id"=>$customer_id);
		}else{
			$bill_incr = " AND is_default='Y' AND customer_id=:customer_id ";
			$sch_bill_incr = array(":customer_id"=>$customer_id);
		}
		$cb_sql = "SELECT *,
					AES_DECRYPT(card_no_full,'$CREDIT_CARD_ENC_KEY')as cc_no,
					AES_DECRYPT(ach_account_number,'$CREDIT_CARD_ENC_KEY')as ach_account_number,
					AES_DECRYPT(ach_routing_number,'$CREDIT_CARD_ENC_KEY')as ach_routing_number 
				FROM customer_billing_profile WHERE  1 $bill_incr and is_deleted='N'";
		$cb_row = $pdo->selectOne($cb_sql, $sch_bill_incr);
		if(empty($cb_row) || !in_array($cb_row['payment_mode'],array("CC","ACH"))) {
			$response['status'] = false;
			$response['error_status'] = "order_not_found";
			$response['message'] = "Oops, Billing detail not found!!!";
			return $response;
		}
	
		$order_ws_ids = $order_row['subscription_ids'];
		$order_ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id IN ($order_ws_ids)";
		$order_ws_result = $pdo->select($order_ws_sql);
		
		if (empty($order_ws_result)) {
			$response['status'] = false;
			$response['error_status'] = "order_not_found";
			$response['message'] = "Oops, Subscriptions not found!!!";
			return $response;
		}
	
		$coverage_dates = array();
		$PlanIdArr = array();
		foreach ($order_ws_result as $key => $order_ws_row) {
			$coverage_dates[$order_ws_row['product_id']] = $order_ws_row['eligibility_date'];
			if(!in_array($order_ws_row['plan_id'], $PlanIdArr)){
				array_push($PlanIdArr, $order_ws_row['plan_id']);
			}
		}
		
		$sale_type_params = array();
		$decline_log_id="";
		$sale_type_params['is_renewal'] = $order_row['is_renewal'];
		$sale_type_params['customer_id'] = $customer_id;
		$payment_master_id = $this->get_agent_merchant_detail($PlanIdArr, $sponsor_row['id'], $cb_row['payment_mode'],$sale_type_params);
		if(!empty($payment_master_id)){
			$payment_processor = getname('payment_master',$payment_master_id,'processor_id');
		}
		$cc_params = array();
		$cc_params['order_id'] = $order_row['display_id'];
		$cc_params['amount'] = $order_row['grand_total'];
		$cc_params['firstname'] = $cb_row['fname'];
		$cc_params['lastname'] = $cb_row['lname'];
		$cc_params['address1'] = $cb_row['address'];
		$cc_params['city'] = $cb_row["city"];
		$cc_params['state'] = $cb_row["state"];
		$cc_params['zip'] = $cb_row["zip"];
		$cc_params['country'] = 'USA';
		$cc_params['description'] = "Attempt Order Again";
		$cc_params['email'] = $customer_row['email'];
		$cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
		$cc_params['processor'] = $payment_processor;
	
		if($order_row['grand_total'] == 0) {
			$payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
		} else {
			//temporary payment
				$cb_row['payment_mode'] = 'CC';
				$cb_row['cc_no'] = "4111111111111114";
			//temporary payment
			if ($cb_row['payment_mode'] == "ACH") {
				$cc_params['ach_account_type'] = $cb_row['ach_account_type'];
				$cc_params['ach_routing_number'] = $cb_row['ach_routing_number'];
				$cc_params['ach_account_number'] = $cb_row['ach_account_number'];
				$cc_params['name_on_account'] = $cb_row['fname']. ' ' . $cb_row['lname'];
				$cc_params['bankname'] = $cb_row['bankname'];
				$api = new CyberxPaymentAPI();
				$payment_res = $api->processPaymentACH($cc_params,$payment_master_id);
	
			} elseif ($cb_row['payment_mode'] == "CC") {
				if ($SITE_ENV=='Local') {
					$cb_row['cc_no'] = "4111111111111114";
				}
				$cc_params['ccnumber'] = $cb_row['cc_no'];
				$cc_params['card_type'] = $cb_row['card_type'];
				$cc_params['ccexp'] = str_pad($cb_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($cb_row['expiry_year'], -2);
				if ($cb_row['cc_no'] == '4111111111111114') {
					$paymentApproved = true;
					$txn_id = 0;
					$payment_res = array("status"=>"Success",'transaction_id'=>0);
				} else {
					$api = new CyberxPaymentAPI();
					$payment_res = $api->processPayment($cc_params,$payment_master_id);
				}
			}
		}
		//$payment_res = array("status"=>"Fail",'transaction_id'=>0);
		/*$txn_id = 0;
		$payment_res = array("status"=>"Success",'transaction_id'=>0);*/
		
	
		if ($payment_res['status'] == 'Success') {
			$paymentApproved = true;
			$txn_id = $payment_res['transaction_id'];
		} else {
			$paymentApproved = false;
			$payment_error = checkIsset($payment_res['message']);
			$cc_params['order_type'] = 'Attempt Order Again';
			$cc_params['browser'] = $BROWSER;
			$cc_params['os'] = $OS;
			$cc_params['req_url'] = $REQ_URL;
			$cc_params['err_text'] = $payment_error;
			$decline_log_id = $this->credit_card_decline_log($customer_id, $cc_params, checkIsset($payment_res,'arr'));
		}
	
		$bill_data = array(
			'customer_id' => $customer_id,
			'fname' => makeSafe($cb_row['fname']),
			'lname' => makeSafe($cb_row['lname']),
			'email' => makeSafe($customer_row['email']),
			'country_id' => '231',
			'country' => 'United States',
			'state' => makeSafe($cb_row['state']),
			'city' => makeSafe($cb_row['city']),
			'zip' => makeSafe($cb_row['zip']),
			'address' => makeSafe($cb_row['address']),
			'payment_mode' => $cb_row['payment_mode'],
		);
	
		if($cb_row['payment_mode'] == "ACH") {
			$bill_data = array_merge($bill_data,array(
				'ach_account_type' => $cb_row['ach_account_type'],
				'bankname' => $cb_row['bankname'],
				'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $cb_row['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
				'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $cb_row['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
			));
		} elseif($cb_row['payment_mode'] == "CC") {
	
			$bill_data = array_merge($bill_data,array(
				'card_type' => makeSafe($cb_row['card_type']),
				'expiry_month' => makeSafe($cb_row['expiry_month']),
				'expiry_year' => makeSafe($cb_row['expiry_year']),
				'card_no' => makeSafe(substr($cb_row['cc_no'], -4)),
				'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $cb_row['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
			));
		}
		$billing_id = getname('order_billing_info',$order_row['id'],'id','order_id');
		$bill_where = array("clause" => "id=:id", "params" => array(":id" => $billing_id));
		$pdo->update("order_billing_info",$bill_data,$bill_where);
	
		/*----- code for order --------*/
			$order_params = array(
				'transaction_id' => makeSafe($txn_id),
				'payment_type' => ($cb_row['payment_mode'] == "ACH"?"ACH":"CC"),
				'status' => ($paymentApproved)? ( $cb_row['payment_mode'] == "ACH" ? "Pending Settlement" : "Payment Approved"):"Payment Declined",
				'payment_processor_res' => json_encode($payment_res),
				'future_payment' => "N",
				'browser' => $BROWSER,
				'os' => $OS,
				'req_url' => $REQ_URL,
			);
			if($cb_row['payment_mode'] == "ACH") {
				$order_params['payment_master_id'] = $payment_master_id;
			} else {
				$order_params['payment_master_id'] = $payment_master_id;
			}
			if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
				$order_params['review_require'] = 'Y';
			}
			$order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
			$pdo->update("orders", $order_params, $order_where);
			$txn_id = $payment_res['transaction_id'];
			$member_setting = $memberSetting->get_status_by_payment($paymentApproved);
			if ($paymentApproved) {
				//************************ insert transaction code start *********************
				if($cb_row['payment_mode'] != "ACH") {
					if($order_row['is_renewal'] == 'N') {
						$this->checkOrderDpgAgreement($order_row['id']);
						// generate joinder agreement when order is approved
                		$this->checkJoinderAgreement($order_row['id']);

						$transactionInsId = $this->transaction_insert($order_id,'Credit','New Order','Transaction Approved','',array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res));
					} else {
						$transactionInsId = $this->transaction_insert($order_id,'Credit','Renewal Order','Renewal Transaction','',array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res));	
					}
				}else{
					$transactionInsId = $this->transaction_insert($order_id,'Credit','Pending','Settlement Transaction','',array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res));	
				}
				//************************ insert transaction code end ***********************
				//************************** payable code start **********************//
					$payable_params=array(
						'payable_type'=>'Vendor',
						'type'=>'Vendor',
						'transaction_tbl_id' =>$transactionInsId['id']
					);
					$this->payable_insert($order_id,0,0,0,$payable_params);
				//************************** payable code start **********************//
			}else{
				//************************ insert transaction code start ***********************
					$transactionInsId = $this->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined','',array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res,"reason" => checkIsset($payment_error),'cc_decline_log_id'=>checkIsset($decline_log_id)));
				//************************ insert transaction code end ***********************
			}
		/*----- code for order --------*/
		if ($paymentApproved) {
	
			foreach ($order_ws_result as $order_ws_row) {	
				$wsId=$order_ws_row['id'];	
				/*----------- Fetch Product Row -----------*/
				$prd_sql = "SELECT name FROM prd_main WHERE id=:id";
				$prd_row = $pdo->selectOne($prd_sql, array(":id" => $order_ws_row['product_id']));				
	
				/*-------- Update Website Subscriptions -------*/
					$update_ws_data = array(
						'last_order_id' => $order_id,
						'fail_order_id' => 0,
						'total_attempts' => 0,
						'next_attempt_at' => NULL,
						'last_purchase_date' => 'msqlfunc_NOW()',
					);
					$update_ws_where = array("clause" => "id=:id", "params" => array(":id" => $wsId));
					$pdo->update("website_subscriptions", $update_ws_data, $update_ws_where);
				/*-------- Update Website Subscriptions -------*/

				$extra_params = array();
				$extra_params['location'] = "take_order_billing";
				$extra_params['member_setting'] = $member_setting;
				$extra_params['message'] = ($order_row['is_renewal'] == 'Y'?'Renewed Successfully':'Initial Setup Successful (Attempt Again)');
				$extra_params['transaction_id'] = $txn_id;
				$policySetting->removeTerminationDate($wsId,$extra_params);
			}

			$enrollDate->updateNextBillingDateByOrder($order_id);

			if($order_row['is_renewal'] == 'N') {
				
				$lead_where = array(
					"clause" => "customer_id=:customer_id",
					"params" => array(
						":customer_id" => $order_row['customer_id'],
					),
				);

				$pdo->update("leads", array('status' => 'Converted', 'updated_at' => 'msqlfunc_NOW()'), $lead_where);
	
				$update_lead_param = array(
					'customer_id' => $customer_row['id'],
					'email' => $customer_row['email'],
					'cell_phone' => $customer_row['cell_phone']
				);
				$this->update_leads_and_details($update_lead_param);
	
				$af_data = array(
					'order_id' => $order_id,
					'order_display_id' => $order_row['display_id'],
					'customer_id' => $customer_id,
					'admin_id' => (!empty($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0),
				);
	
				if ($cb_row['payment_mode'] == "CC") {
					$af_data['billing_detail'] = $cb_row['card_type']." *" . substr($cb_row['cc_no'], -4);
				} else {
					$af_data['billing_detail'] = "ACH *" . substr($cb_row['ach_account_number'], -4);
				}
	
				// activity_feed(3, $sponsor_row['id'],$sponsor_row['type'],$order_row['id'], 'orders', 'Successful Payment', $customer_row['fname'], $customer_row['lname'], json_encode($af_data));
	
				// activity_feed(3, $customer_row['id'],$customer_row['type'],$order_row['id'], 'orders', 'Successful Payment', $customer_row['fname'], $customer_row['lname'], json_encode($af_data));
			}
	
			/*------ Update Customer -----*/
			$update_customer_where = array(
				'clause' => 'id=:id',
				'params' => array(
					':id' => $customer_id,
				),
			);
		
			$update_customer_data = array('status' => $member_setting['member_status']);
			$pdo->update('customer', $update_customer_data, $update_customer_where);

			$activity_feed_data = array();
			$customer_sql = "SELECT id,type,rep_id,fname,lname FROM customer  WHERE id=:customer_id";
			$customer_where = array(":customer_id" => $customer_id);
			$customer_row = $pdo->selectOne($customer_sql, $customer_where);

			$activity_feed_data['ac_message'] = array(
				'ac_message_1' =>' Order Payment successfully Order : ',
				'ac_red_2'=>array(
					// 'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$order_row['display_id'],
				),
				'ac_message_2' =>' for Member ',
				'ac_red_3'=>array(
					'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$customer_row['rep_id'],
				),
			);

			if ($cb_row['payment_mode'] == "CC") {
				$activity_feed_data['billing_description'] =  "Billing Information : ".$cb_row['card_type']." *" . substr($cb_row['cc_no'], -4);
			} else {
				$activity_feed_data['billing_description'] = "Billing Information : "."ACH *" . substr($cb_row['ach_account_number'], -4);
			}

			activity_feed(3, $customer_row['id'],$customer_row['type'],$order_row['id'], 'orders', 'Successful Payment', $customer_row['fname'], $customer_row['lname'], json_encode($activity_feed_data));
	
			$response['status'] = true;
			$response['message'] = "Order Attempted Successfully";
			return $response;
		} else {
			foreach ($order_ws_result as $order_ws_row) {
				$wsId=$order_ws_row['id'];	
				$ws_history_data = array(
					'customer_id' => $customer_id,
					'website_id' => $wsId,
					'product_id' => $order_ws_row['product_id'],
					'fee_applied_for_product' => $order_ws_row['fee_applied_for_product'],
					'prd_plan_type_id' =>  $order_ws_row['prd_plan_type_id'],
					'plan_id' => $order_ws_row['plan_id'],
					'order_id' => $order_id,
					'status' => 'Fail',
					'message' => $payment_error,
					'authorize_id' => makeSafe($txn_id),
					'admin_id' => !empty($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0,
					'created_at' => 'msqlfunc_NOW()',
					'note' => 'menual attempt',
				);
				$pdo->insert("website_subscriptions_history", $ws_history_data);
				$update_ws_data = array(
					'total_attempts' => 'msqlfunc_total_attempts + 1',
					'updated_at' => 'msqlfunc_NOW()',
				);
	
				$attempt_sql = "SELECT * FROM prd_subscription_attempt WHERE attempt=:attempt AND is_deleted='N'";
				$attempt_where = array(":attempt" =>($order_ws_row['total_attempts'] + 1));
				$attempt_row = $pdo->selectOne($attempt_sql, $attempt_where);

				$extra = array('attempt' => $order_ws_row['total_attempts'] + 1,'is_renewal' => $order_row['is_renewal']);

				$member_setting = $memberSetting->get_status_by_payment("","","","",$extra);

				if ($attempt_row) {
					$atmpt = $attempt_row['attempt'];
					$fail_trigger_id = $attempt_row['fail_trigger_id'];
	
					// $update_ws_data['next_attempt_at'] = date('Y-m-d', strtotime("+" . $attempt_row['attempt_frequency'] . " " . $attempt_row['attempt_frequency_type']));

					$update_ws_data['next_attempt_at'] = date('Y-m-d',strtotime("+1 day",strtotime(date('Y-m-d'))));
					
					$update_ws_data['status'] = $member_setting['policy_status'];
					// if($order_row['is_renewal'] == "Y") {
					// 	$update_ws_data['status'] = 'On Hold Failed Billing';
					// } else {
					// 	$update_ws_data['status'] = 'Post Payment';
					// }

					$customrt_updateArr['status'] = $member_setting['member_status'];
					$customer_updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $order_ws_row['customer_id']));
					$pdo->update("customer", $customrt_updateArr, $customer_updateWhere);
				} else {
					
					$termination_date=$enrollDate->getTerminationDate($wsId);
					
					$extra_params = array();
					$extra_params['location'] = "take_order_billing";
					$termination_reason = "Failed Billing";
					$policySetting->setTerminationDate($wsId,$termination_date,$termination_reason,$extra_params);
				}
	
				$update_ws_where = array("clause" => 'id=:id', 'params' => array(":id" => $wsId));
				$pdo->update("website_subscriptions", $update_ws_data, $update_ws_where);
	
			}
			/*-------- Payment Failed Activity Feed -----------*/
			$pf_act_data = array(
				'order_id' => $order_id,
				'order_display_id' => $order_row['display_id'],
				// 'order_billing_id' => $order_billing_id,
				'reason' => $payment_error ? $payment_error : 'Error in processing payment',
				'billing_info' => ($cb_row['payment_mode'] == "CC"?$cb_row['card_type']." *" . substr($cb_row['cc_no'], -4):"ACH *" . substr($cb_row['ach_routing_number'], -4)),
			);
			// $pf_act_data = json_encode($pf_act_data);
			// activity_feed(3,$order_row['customer_id'], "Customer", $order_id, 'orders', 'Billing Failed', $fname, $lname,$pf_act_data);

			$activity_feed_data['ac_message'] = array(
				'ac_message_1' =>' Order Payment Fail Order : ',
				'ac_red_2'=>array(
					// 'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$order_row['display_id'],
				),
				'ac_message_2' =>' for Member ',
				'ac_red_3'=>array(
					'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>$customer_row['rep_id'],
				),
			);

			if(!empty($customrt_updateArr['status'])){
				$activity_feed_data['customer_status'] = "Member status : ".$customrt_updateArr['status'];
			}
			$activity_feed_data['description_error'] = "Error : ". $pf_act_data['reason'];
			$activity_feed_data['billing_description'] = "Billing Information : ".$pf_act_data['billing_info'];

			activity_feed(3, $customer_row['id'],$customer_row['type'],$order_row['id'], 'orders', 'Billing Failed', $customer_row['fname'], $customer_row['lname'], json_encode($activity_feed_data));

			/*--------/Payment Failed Activity Feed -----------*/
			$response['status'] = false;
			$response['error_status'] = "payment_fail";
			$response['message'] = ($payment_error ? $payment_error : 'Error in processing payment');
			return $response;
		}
	}

	public function checkOrderDpgAgreement($order_id){
		global $pdo;
	
		if($order_id){
			$order_sql = "SELECT customer_id FROM orders WHERE id=:order_id AND status='Payment Approved' AND is_renewal='N'";
			$order_param = array(":order_id" => $order_id);
			$order_res = $pdo->selectOne($order_sql,$order_param);
	
			if(!empty($order_res)){
				$agreementSql = "SELECT id,is_generated FROM dpg_agreements WHERE customer_id=:customer_id ORDER BY is_generated DESC";
				$agreementParams = array(":customer_id" => $order_res['customer_id']);
				$agreementRes = $pdo->select($agreementSql,$agreementParams);
	
				$has_dpg_document = false;
				if(!empty($agreementRes)){
					foreach ($agreementRes as $key => $value) {
						if($value['is_generated'] == 'Y'){
							$has_dpg_document = true;
							break;
						}
					}
				}
	
				if(!empty($agreementRes)) {
					if(!$has_dpg_document){
						$agreement_sql = "SELECT id FROM dpg_agreements WHERE order_id = :order_id";
						$agreement_param = array(":order_id" => $order_id);
						$agreement_res = $pdo->selectOne($agreement_sql,$agreement_param);
	
						if(!empty($agreement_res['id'])){
							$update_param = array(
								"is_generated" => "Y",
							);
							$update_where = array(
								'clause' => 'id = :id',
								'params' => array(
									':id' => $agreement_res['id'],
								),
							);
							$pdo->update('dpg_agreements', $update_param, $update_where);
						}
					}
				} else {
					$this->insert_dpg_agreements($order_res['customer_id'],$order_id,array());
				}
			}	
		}
	}

	public function checkJoinderAgreement($order_id){
		global $pdo;
	
		if(!empty($order_id)){

			$odrSql = "SELECT id,customer_id FROM orders WHERE id=:order_id AND status='Payment Approved' AND is_renewal='N'";
			$odrParams = array(":order_id" => $order_id);
			$odrRes = $pdo->selectOne($odrSql,$odrParams);
	
			if(!empty($odrRes)){
				$sqlOrder="SELECT o.customer_id,p.id as productId,ws.application_type
					FROM orders o
					JOIN order_details od on (o.id=od.order_id AND od.is_deleted='N')
					JOIN website_subscriptions ws ON(ws.id=od.website_id)
					JOIN prd_main p ON (p.id=od.product_id AND p.joinder_agreement_require='Y')
					JOIN prd_agreements pa ON(p.id=pa.product_id AND pa.is_deleted='N')
					where o.id=:order_id GROUP BY p.id";
				$resOrder=$pdo->select($sqlOrder,array(":order_id"=>$order_id));
				
				if(!empty($resOrder)){
					foreach($resOrder as $odrRow){
						$selAgreement = "SELECT id,is_generated FROM joinder_agreements WHERE customer_id=:customer_id AND product_id=:product_id AND is_deleted='N'";
						$agreementParams = array(":customer_id" => $odrRow["customer_id"],":product_id" => $odrRow["productId"]);
						$resAgreement = $pdo->selectOne($selAgreement,$agreementParams);
	
						if(!empty($resAgreement)){
							if($resAgreement["is_generated"] == "N"){
								$update_param = array(
									"is_generated" => "Y",
								);
								$update_where = array(
									'clause' => 'id = :id',
									'params' => array(
										':id' => $resAgreement['id'],
									),
								);
								$pdo->update('joinder_agreements', $update_param, $update_where);
							}
						}else{
							$this->insert_joinder_agreements($odrRow["customer_id"],$order_id,$odrRow["application_type"]);
							break;
						}
					}
				}
			}
		}
	}
	public function check_payable_can_regenerate_or_not($transaction_tbl_id,$orderId,$order_detail_id,$payPeriod='current',$is_reverse='',$payable_params){
		global $pdo;
		include_once dirname(__DIR__) . "/includes/commission.class.php";
		$commObj = new Commission();
		$insert_payable = false;
		$plan_array = array();

		$py_incr = $payabelpayPeriod = '';

		$order_sql = "SELECT o.id as order_id,od.id as order_detail_id,od.product_id,od.plan_id,od.prd_plan_type_id AS plan_ids,IF(o.is_list_bill_order='N',o.is_renewal,IF(od.renew_count=1,'N','Y')) as is_renewal,o.grand_total,ws.customer_id,o.is_list_bill_order,od.website_id,od.is_refund,od.renew_count,o.created_at as odrDate,p.pricing_model as pricing_model
					FROM orders o 
					JOIN order_details od ON (o.id=od.order_id AND od.is_deleted='N')
					JOIN website_subscriptions ws ON (ws.id=od.website_id)
					JOIN prd_main p on(p.id = ws.product_id)
					WHERE 
					od.id=:order_detail_id 
					AND (od.product_type != 'Fees' OR p.product_type = 'Healthy Step')
					";
		$order_where = array(":order_detail_id" => $order_detail_id);
		$order_res = $pdo->selectOne($order_sql,$order_where);
		$payable_params['order_detail_id'] = $order_detail_id;

		if(!empty($order_res['order_id'])){
			$payable_params['pricing_model'] = $order_res['pricing_model'];
			$payable_params['prd_plan_id'] = $order_res['plan_id'];
			$productId = $order_res['product_id'];
			$matrixId = $order_res['plan_id'];

			if ($payPeriod == 'period_earned') {
				$transRes = array();
				if(!empty($transaction_tbl_id)){
					$transSql = "SELECT created_at as transDate FROM transactions WHERE id=:id";
					$transRes = $pdo->selectOne($transSql,array(":id" => $transaction_tbl_id));
				}
                $payDate = !empty($transRes["transDate"]) ?  date('Y-m-d H:i:s', strtotime($transRes["transDate"])) : date('Y-m-d H:i:s', strtotime($order_res["odrDate"]));
            } else {
                $payDate = date("Y-m-d H:i:s");
            }
            $regPayPeriod = "";
            if($order_res['is_renewal'] == 'N'){
            	$regPayPeriod = $commObj->getWeeklyPayPeriod($payDate);
            }else{
            	$regPayPeriod = $commObj->getMonthlyPayPeriod($payDate);
            }

			if($payPeriod != 'period_earned'){
				if($order_res['is_renewal'] == 'N') {
					$payabelpayPeriod = get_pay_period_weekly();
				} else {
					$payabelpayPeriod = get_pay_period_monthly();
				}
				$py_incr .=" AND pd.pay_period=:pay_period ";
			}
			
			if($is_reverse == 'Y'){
				$py_incr .=" AND pd.status IN('Reverse_Vendor','Reverse_membership','Reverse_Carrier') AND pd.is_reverse='Y' ";
			}else{
				$py_incr .=" AND pd.status IN('Generate_Vendor','Generate_membership','Generate_Carrier') AND (pd.is_reverse='N' OR pd.is_reverse='Y') ";
			}
			
			$where = array(
				":order_id" => $orderId,
				":order_detail_id" => $order_detail_id,
				":transaction_tbl_id" => $transaction_tbl_id
			);
			if(!empty($payabelpayPeriod)){
				$where[":pay_period"] = $payabelpayPeriod;
			}
			if(!empty($productId)){
				$py_incr .=" AND p.product_id=:product_id";
				$where[":product_id"] = $productId;
			}

			$sel_payable = "SELECT pd.* FROM payable p JOIN payable_details pd ON(p.id=pd.payable_id) WHERE p.order_id=:order_id AND p.order_detail_id=:order_detail_id AND pd.transaction_tbl_id=:transaction_tbl_id $py_incr ";
			$resPayable = $pdo->select($sel_payable,$where);

			$oldPayableArr = [];
            if(!empty($resPayable)){
	            foreach ($resPayable as $payableValue) {
                    $oldPayableArr[$payableValue['payee_type']][$payableValue['fee_price_id']] = $payableValue;
	            }
        	}

			//Check IN Database no record found for payable Then Insert
			if(empty($resPayable)){
				$payable_params['plan_ids'] = $order_res['plan_ids'];
				$payable_params['order_detail_id'] = $order_detail_id;
				$payable_params['pay_period'] = $regPayPeriod;
				$payable_params['payDate'] = $payDate;
				$this->payable_insert($orderId,$order_res['customer_id'],$productId,$matrixId,$payable_params);
				$insert_payable = true;

			} else {
				// Found Record IN database then check for Membership Fee, Carrier Fee or Vendor Fee Is match or not
					$payableArr = array();
					foreach($resPayable as $value){
						$payableArr[$value['payee_type']][$value['fee_price_id']][$value['transaction_tbl_id']]['credit'] = $value['credit'];
						$payableArr[$value['payee_type']][$value['fee_price_id']][$value['transaction_tbl_id']]['fee_price_id'] = $value['fee_price_id'];
					}
					$renew_count = ($order_res['renew_count'] - 1);
					$inner_incr = '';

					if($order_res['is_renewal'] == 'Y'){
						$inner_incr.=" AND p_fee.is_fee_on_renewal='Y' AND (p_fee.fee_renewal_type='Continuous' OR p_fee.fee_renewal_count <= $renew_count)";
					} else {
						$inner_incr.=" AND p_fee.initial_purchase='Y'";
					}
					
					if(!empty($order_res['plan_ids'])){
						$inner_incr.= "AND IF(p_fee.is_benefit_tier='N',1,p_matrix.plan_type IN(".$order_res['plan_ids'].") AND fee_matrix.plan_type IN(".$order_res['plan_ids']."))";
					}
					$NBOrderres = getNBOrderDetails($order_res['customer_id'],$productId);
					$sqlVendor = "SELECT pf.id as vendor_id,paf.fee_id as fee_price_id,p_fee.name AS fee_name ,fee_matrix.price_calculated_on as fee_method,pf.setting_type as payable_type,fee_matrix.price as amount,p_matrix.price as retail_price,fee_matrix.id as fee_matrix_id,p_fee.is_benefit_tier,fee_matrix.price_calculated_type,p_matrix.commission_amount,p_matrix.non_commission_amount
					FROM order_details od 
					JOIN prd_matrix p_matrix ON(p_matrix.id = od.plan_id AND p_matrix.is_deleted='N')
					-- join prd_main p ON(p.id=od.product_id)
					JOIN prd_assign_fees paf ON(paf.product_id=od.product_id AND paf.is_deleted='N')
					JOIN prd_fees pf ON(pf.id=paf.prd_fee_id AND pf.is_deleted='N')
					JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=paf.fee_id AND fee_matrix.is_deleted='N')
					JOIN prd_main p_fee ON(p_fee.id=fee_matrix.product_id AND p_fee.is_deleted='N')
					LEFT JOIN prd_fee_pricing_model model ON(
						CASE WHEN p_fee.is_benefit_tier='Y' 
						THEN 
							model.product_id=od.product_id AND 
							model.prd_matrix_id=od.plan_id AND 
							model.fee_product_id=p_fee.id AND 
							model.prd_matrix_fee_id=fee_matrix.id
						ELSE
							model.product_id=od.product_id AND 
							model.prd_matrix_id=od.plan_id 
						END
						AND model.is_deleted='N'  
						)
					WHERE od.order_id=:order_id AND od.is_deleted='N' AND od.id=:order_detail_id AND pf.setting_type IN('Vendor','Membership','Carrier') AND p_fee.status='Active' 
					AND DATE(fee_matrix.pricing_effective_date) <= :order_date AND (fee_matrix.pricing_termination_date IS NULL OR DATE(fee_matrix.pricing_termination_date)> :order_date) $inner_incr GROUP BY p_fee.id,od.product_id";
			
					$resVendor = $pdo->select($sqlVendor,array(":order_detail_id"=>$order_detail_id,":order_id"=>$orderId, ":order_date" => (!empty($NBOrderres['orderDate']) ? date('Y-m-d',strtotime($NBOrderres['orderDate'])) : date('Y-m-d'))));
					$fee_array = array();
					if(!empty($resVendor)){
						foreach($resVendor as $value){
							if(isset($oldPayableArr[$value['payable_type']][$value['fee_price_id']])){
                    			unset($oldPayableArr[$value['payable_type']][$value['fee_price_id']]);
                    		}
							if($value['fee_method'] == "Percentage"){
								if($value['price_calculated_type']=="Retail"){
									$calclulatedPrice = ($value['retail_price'] * $value['amount'])/100;
								  }else if($value['price_calculated_type']=="Commissionable"){
									$calclulatedPrice = ($value['commission_amount'] * $value['amount'])/100;
								  }else if($value['price_calculated_type']=="NonCommissionable"){
									$calclulatedPrice = ($value['non_commission_amount'] * $value['amount'])/100;
								  }
								  $vendor_fee = $calclulatedPrice;
							}else{
								$vendor_fee = $value['amount'];
							}
							$old_Price = checkIsset($payableArr[$value['payable_type']][$value['fee_price_id']][$transaction_tbl_id]['credit']);
							$fee_price_id = checkIsset($payableArr[$value['payable_type']][$value['fee_price_id']][$transaction_tbl_id]['fee_price_id']);

							if(!empty($old_Price) && number_format($vendor_fee,2) !=  $old_Price || $value['fee_price_id'] != $fee_price_id ){
								$payable_params['fee_price'] = $old_Price;
								$payable_params['fee_price_id'] = $value['fee_price_id'];
								$payable_params['plan_ids'] = $order_res['plan_ids'];
								$payable_params['order_detail_id'] = $order_detail_id;
								$payable_params['pay_period'] = $regPayPeriod;
								$payable_params['payDate'] = $payDate;
								$this->payable_insert($orderId,$order_res['customer_id'],$productId,$matrixId,$payable_params);
								$insert_payable = true;
							}
						}
					}
					if(!empty($oldPayableArr)){
	                    foreach ($oldPayableArr as $revPayable) {
	                    	foreach ($revPayable as $k => $v) {
		                    	$payable_params['payable_type'] = 'Reverse_Vendor';
		                    	$payable_params['fee_price'] = $v['credit'];
		                        $payable_params['fee_price_id'] = $v['fee_price_id'];
		                        $payable_params['plan_ids'] = $order_res['plan_ids'];
		                        $payable_params['order_detail_id'] = $order_detail_id;
		                        $payable_params['pay_period'] = $regPayPeriod;
		                        $payable_params['payDate'] = $payDate;
		                        $payable_params['payable_details_id'] = $v['id'];
		                    	$this->payable_insert($orderId, $order_res['customer_id'], $productId, $matrixId, $payable_params, $regenerate_params);
	                    	}
	                    }
	                }
				// Found Record IN database then check for Membership Fee, Carrier Fee or Vendor Fee Is match or not
			}
		}
		return $insert_payable;
	}
	public function getHealthyStepFeePrdIds($type=''){
		global $pdo;
		$feePrdRes = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(id)) as feeIds FROM prd_main WHERE is_deleted='N' AND product_type='Healthy Step'");

		if($type == 'string'){
			$feeProducts = !empty($feePrdRes["feeIds"]) ? $feePrdRes["feeIds"] : "";
		}else{
			$feeProducts = !empty($feePrdRes["feeIds"]) ? explode(",", $feePrdRes["feeIds"]) : array();
		}
		return $feeProducts;
	}

	public function getCoveragePeriodsForPayments($customer_id,$ws_ids) {
	    global $pdo;
	    require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
		$MemberEnrollment = new MemberEnrollment();
	   
	    $today = date("Y-m-d");
	    $sponsorId = getname('customer', $customer_id, 'sponsor_id', 'id');
	    $coverage_periods = array();
	    foreach ($ws_ids as $key => $ws_id) {
	        $ws_sql = "SELECT ws.*,pm.price as plan_price,p.name as product_name,ppt.title as planTitle,IF(p.payment_type='Recurring',p.payment_type_subscription,'One Time') as member_payment_type
	                    FROM website_subscriptions ws 
	                    JOIN prd_main p ON(p.id = ws.product_id) 
	                    JOIN prd_matrix pm ON (pm.id=ws.plan_id)
	                    LEFT JOIN prd_plan_type ppt ON (ws.prd_plan_type_id=ppt.id)
	                    WHERE ws.id=:id
	                    GROUP BY ws.id";
	        $ws_row = $pdo->selectOne($ws_sql,array(":id"=>$ws_id));

	        $billing_date = $this->getCustomerBillingDate($customer_id,$ws_id);
	      
	       
	       // check all coverage periods from effecive date
	         $eligibility_date = $ws_row['eligibility_date'];
	         $subscription_coverage_periods = $this->subscriptionCoveragePeriods($ws_id);
	        foreach ($subscription_coverage_periods as $key => $scp) {
	          if(strtotime($scp['start_coverage_period']) < strtotime($eligibility_date)) {
	            continue;
	          }

	          $coverage_billing_date = coverage_billing_date($scp['start_coverage_period'],$billing_date);
	       	
	          $ws_row['next_purchase_date'] = coverage_billing_date(date("Y-m-d",strtotime("+1 day",strtotime($scp['end_coverage_period']))),$billing_date);

	          $ws_row['start_coverage_period'] = $scp['start_coverage_period'];
	          $ws_row['end_coverage_period'] = $scp['end_coverage_period'];
	          $ws_row['renew_count'] = $scp['renew_count'];
	          $ws_payment_status = subscriotion_has_approved_payment_this_coverage($ws_row['id'],$scp['start_coverage_period']);
	          $ws_row['is_approved_payment'] = $ws_payment_status['success'];
	          if($ws_payment_status['success'] == true) {
	            $ws_row['order_id'] = $ws_payment_status['order_id'];
	            $ws_row['transaction_id'] = $ws_payment_status['transaction_id'];
	            $ws_row['payment_type'] = $ws_payment_status['payment_type'];
	            $ws_row['is_post_date_order'] = $ws_payment_status['is_post_date_order'];
	          }

	          if(!empty($coverage_periods[$scp['renew_count']])) {
	              $coverage_periods[$scp['renew_count']]['ws_res'][] = $ws_row;
	              if(strtotime($scp['start_coverage_period']) < strtotime($coverage_periods[$scp['renew_count']]['start_coverage_period'])) {
	            	$coverage_periods[$scp['renew_count']]['start_coverage_period'] = $scp['start_coverage_period'];
	            	$coverage_periods[$scp['renew_count']]['end_coverage_period'] = $scp['end_coverage_period'];
	            	$coverage_periods[$scp['renew_count']]['coverage_billing_date'] = $coverage_billing_date;
	            	$coverage_periods[$scp['renew_count']]['max_payment_date'] = date('Y-m-d',strtotime('-1 day',strtotime($scp['start_coverage_period'])));
	              }
	          } else {
	            $coverage_periods[$scp['renew_count']] = array(
	              'start_coverage_period' => $scp['start_coverage_period'],
	              'end_coverage_period' => $scp['end_coverage_period'],
	              'coverage_billing_date' => $coverage_billing_date,
	              'max_payment_date' => date('Y-m-d',strtotime('-1 day',strtotime($scp['start_coverage_period']))),
	              'renew_count' => $scp['renew_count'],
	              'ws_res' => array($ws_row),
	            );
	          }

	          	if(strtotime($coverage_periods[$scp['renew_count']]['max_payment_date']) < strtotime(date('Y-m-d'))) {
	            	$coverage_periods[$scp['renew_count']]['max_payment_date'] = date('Y-m-d');
	            }
	        }
	    }
	    ksort($coverage_periods);
	    foreach ($coverage_periods as $key => $coverage_period_row) {
	      $is_approved_payment = true;
	      $product_matrix = array();
	      $subTotal = 0.00;
	      $is_new_order = "N";
	      $is_renewal='N';
	      $renew_count = 0;
	      $renewalCountsArr = array();

	        foreach ($coverage_period_row['ws_res'] as $tmp_ws_row) {
	            if($tmp_ws_row['is_approved_payment'] == false) {
	                $is_approved_payment = false;
	                $product_matrix[$tmp_ws_row['product_id']] = $tmp_ws_row['plan_id'];
	                $subTotal += $tmp_ws_row['plan_price'];

	                if($coverage_period_row["renew_count"] > 1){
						$is_renewal='Y';
						$renew_count = $coverage_period_row["renew_count"]-1;
				      }else{
				      	$is_new_order = "Y";
				      }
	                $renewalCountsArr[$tmp_ws_row['product_id']] = $renew_count;
	            }
	        }
	    
	        $coverage_periods[$key]['is_approved_payment'] = $is_approved_payment;
	        
	        if($is_approved_payment == true) {
	          $coverage_periods[$key]['coverage_service_fee'] = 0.0;
	        } else {
	        	$serviceFee = $MemberEnrollment->getRenewalServiceFee($product_matrix,$customer_id,$sponsorId,$subTotal,'Members',$is_new_order,$is_renewal,$renewalCountsArr);
	        	$coverage_periods[$key]['coverage_service_fee'] = $serviceFee;
	        }
	    }
	    return $coverage_periods;
	}
	public function getCustomerBillingDate($customerId = 0,$wsId=0) {
	    global $pdo;
		$billingDate = '';
		$incr = "";
		$schParams = array();

		if(!empty($customerId)){
			$incr .= " AND customer_id = :customerId";
			$schParams[':customerId'] = $customerId;
		}

		if(!empty($wsId)){
			$incr .= " AND id = :wsId";
			$schParams[':wsId'] = $wsId;
		}

		if(!empty($customerId) || !empty($wsId)){
			$wsSql = "SELECT next_purchase_date 
					FROM website_subscriptions 
					WHERE status IN('Active','Inactive','Pending', 'Post Payment') $incr 
					ORDER BY next_purchase_date ASC";
			$wsRes = $pdo->selectOne($wsSql,$schParams);
			if(!empty($wsRes["next_purchase_date"])){
				$billingDate = $wsRes["next_purchase_date"];
			}
		}
		return $billingDate;
	}
	public function subscriptionCoveragePeriods($ws_id,$getFutureCov = 'Y',$extraParams = array()) {
  		global $pdo;
  		require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';

		$enrollDate = new enrollmentDate();
	   	$coverage_periods = array();
	   	$today = date("Y-m-d");

  		$ws_row = $this->getMainSubscription($ws_id);

  		if(!empty($ws_row['termination_date']) && strtotime($ws_row['termination_date']) == strtotime($ws_row['eligibility_date'])){
  			$ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions w WHERE id=:id",array(":id"=>$ws_id));
  		}
  		
  		$eligibility_date = $ws_row['eligibility_date'];
	    $member_payment_type_res = $pdo->selectOne("SELECT IF(payment_type='Recurring',payment_type_subscription,'One Time') as member_payment_type FROM prd_main where id = :id",array(":id" => $ws_row['product_id']));
	    $member_payment_type = $member_payment_type_res['member_payment_type'];
  		
	    $tmp_eligibility_date = $eligibility_date;
	    $is_last_coverage_period = false;
	    $renew_count = 1;
	    $futureCoverage = 0;
	    while ($is_last_coverage_period == false) {
	      $product_dates=$enrollDate->getCoveragePeriod($tmp_eligibility_date,$member_payment_type);

	      $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
	      $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

	      $tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));
	      // if getFutureCov = 'N' then it will not return future coverage
	      if($getFutureCov == 'N'){
	      	$nextBillingDate = !empty($extraParams["nextBillingDate"]) ? date("Y-m-d",strtotime($extraParams["nextBillingDate"])) : "";
	      	if(!empty($nextBillingDate) && strtotime($nextBillingDate) > strtotime($today)){
	      		if(strtotime($nextBillingDate) <= strtotime($startCoveragePeriod)){
	      			$is_last_coverage_period = true;
		      		continue;
	      		}
	      	}else if(strtotime($today) <= strtotime($startCoveragePeriod)) {
	      		$is_last_coverage_period = true;
	      		continue;
		    }
	      }else{
		      if(strtotime($today) <= strtotime($startCoveragePeriod)) {
		      	$futureCoverage++;
		      }

		      if($futureCoverage >= 3){
		      	$is_last_coverage_period = true;  
		      }
	      }

	      $coverage_periods[$startCoveragePeriod] = array(
	        'start_coverage_period' => $startCoveragePeriod,
	        'end_coverage_period' => $endCoveragePeriod,
	        'renew_count' => $renew_count,
	      );
	      $renew_count++;
	    }
  		return $coverage_periods;
	}
	public function getMainSubscription($wsId) {
	  global $pdo;
	  $wsRow = $pdo->selectOne("SELECT * FROM website_subscriptions w WHERE id=:id",array(":id"=>$wsId));
	  if(!empty($wsRow['parent_ws_id'])) {
	    $wsRow = $this->getMainSubscription($wsRow['parent_ws_id']);
	  }
	  return $wsRow;
	}
	public function getMemberHealthyStepFee($customerId) {
  		global $pdo;
  		$stepFeeRow = array();
  		if(!empty($customerId)){
  			$stepFeeSql = "SELECT ws.*,pm.price as plan_price,p.name as product_name,p.product_type 
  						FROM website_subscriptions ws
						JOIN prd_main p ON(ws.product_id=p.id)
						JOIN prd_matrix pm ON (pm.id=ws.plan_id AND pm.is_deleted='N')
						WHERE ws.customer_id=:customerId AND p.product_type='Healthy Step'";
			$stepFeeRow = $pdo->selectOne($stepFeeSql,array(":customerId"=>$customerId));
  		}
  		return $stepFeeRow;
  	}

  	public function addCustomerGroupSettings($params,$customer_id){
  		global $pdo;

  		$checkRes=$pdo->selectOne("SELECT id FROM customer_group_settings WHERE customer_id=:id",array(":id"=>$customer_id));

  		if(!empty($checkRes)){
  			$id=$checkRes['id'];
  			$upd_where = array(
				'clause' => 'customer_id = :id',
				'params' => array(
					':id' => $customer_id,
				),
			);
			$pdo->update('customer_group_settings', $params, $upd_where);
  		}else{
  			$params['customer_id']=$customer_id;
  			$id=$pdo->insert('customer_group_settings',$params);
  		}
  		return $id;
  	}
  	public function generateCoverageCode() {
		global $pdo;
		$display_id = rand(1000, 9999);
		
		$sql = "SELECT count(id) as total FROM group_coverage_period WHERE display_id ='CP" . $display_id . "' OR display_id ='" . $display_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateCoverageCode();
		} else {
			return 'CP'.$display_id;
		}
	}
	public function generatePostOrder($order_id,$is_reinstate_order = 'N') {
		global $pdo,$HOST;
		$request_id = 0;
		
		if($is_reinstate_order == "Y") {
			$request_url = $HOST.'/cron_scripts/post_date_reinstate_order.php?order_id='.$order_id;
		} else {
			$request_url = $HOST.'/cron_scripts/post_date_order.php?order_id='.$order_id;
		}
		$ch = curl_init($request_url);
		#curl_setopt($ch, CURLOPT_TIMEOUT,1);//Timeout set to 1 Sec
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	public function generateRenewalOrder($customer_id,$failed_order_id="") {
		global $pdo,$HOST;

		$request_id = 0;
		$incr = "";
		if($failed_order_id){
			$incr .= "&failed_order_id=$failed_order_id";
		}

		$request_url = $HOST.'/cron_scripts/monthly_subscription_order.php?customer_id='.$customer_id.$incr;
		
		$ch = curl_init($request_url);
		#curl_setopt($ch, CURLOPT_TIMEOUT,1);//Timeout set to 1 Sec
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}


	public function generateMassUpdateDisplayID() {
		global $pdo;
		$display_id = rand(100000, 999999);
		
		$sql = "SELECT count(id) as total FROM mass_updates WHERE display_id ='MU" . $display_id . "' OR display_id ='" . $display_id . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateMassUpdateDisplayID();
		} else {
			return 'MU'.$display_id;
		}
	}

	public function productPriceCriteriaCheck($matrixCriteria,$addedCriteria){

		$response = array();
		$response['matrix_group'] = $addedCriteria[0]['keyID'];
		if(!empty($matrixCriteria)){
			foreach ($matrixCriteria as $matrix_group => $matrixGroupRow) {
				foreach ($matrixGroupRow as $key => $value) {
					$matrixPlanType = $value['matrixPlanType'];
					$addedmatrixPlanType = $addedCriteria[0]['matrixPlanType'];

					$enrolleeMatrix = $value['enrolleeMatrix'];
					$addedenrolleeMatrix = $addedCriteria[0]['enrolleeMatrix'];

					$age_from = $value[1]['age_from'];
					$addedage_from = $addedCriteria[0][1]['age_from'];
					
					$age_to = $value[1]['age_to'];
					$addedage_to = $addedCriteria[0][1]['age_to'];

					$state = $value[2]['matrix_value'];
					$addedstate = $addedCriteria[0][2]['matrix_value'];

					$zip = $value[3]['matrix_value'];
					$addedzip = $addedCriteria[0][3]['matrix_value'];

					$gender = $value[4]['matrix_value'];
					$addedgender = $addedCriteria[0][4]['matrix_value'];

					$smoking_status = $value[5]['matrix_value'];
					$addedsmoking_status = $addedCriteria[0][5]['matrix_value'];

					$tobacco_status = $value[6]['matrix_value'];
					$addedtobacco_status = $addedCriteria[0][6]['matrix_value'];

					$height_by = $value[7]['height_by'];
					$addedheight_by = $addedCriteria[0][7]['height_by'];

					$height_feet = $value[7]['height_feet'];
					$addedheight_feet = $addedCriteria[0][7]['height_feet'];

					$height_inch = $value[7]['height_inch'];
					$addedheight_inch = $addedCriteria[0][7]['height_inch'];

					$height_feet_to = $value[7]['height_feet_to'];
					$addedheight_feet_to = $addedCriteria[0][7]['height_feet_to'];

					$height_inch_to = $value[7]['height_inch_to'];
					$addedheight_inch_to = $addedCriteria[0][7]['height_inch_to'];

					$weight_by = $value[8]['weight_by'];
					$addedweight_by = $addedCriteria[0][8]['weight_by'];

					$weight = $value[8]['weight'];
					$addedweight = $addedCriteria[0][8]['weight'];

					$weight_to = $value[8]['weight_to'];
					$addedweight_to = $addedCriteria[0][8]['weight_to'];

					$no_of_children_by = $value[9]['no_of_children_by'];
					$addedno_of_children_by = $addedCriteria[0][9]['no_of_children_by'];

					$no_of_children = $value[9]['no_of_children'];
					$addedno_of_children = $addedCriteria[0][9]['no_of_children'];

					$no_of_children_to = $value[9]['no_of_children_to'];
					$addedno_of_children_to = $addedCriteria[0][9]['no_of_children_to'];

					$has_spouse = $value[10]['matrix_value'];
					$addedhas_spouse = $addedCriteria[0][10]['matrix_value'];

					$spouse_age_from = $value[11]['spouse_age_from'];
					$addedspouse_age_from = $addedCriteria[0][11]['spouse_age_from'];
					
					$spouse_age_to = $value[11]['spouse_age_to'];
					$addedspouse_age_to = $addedCriteria[0][11]['spouse_age_to'];

					$spouse_gender = $value[12]['matrix_value'];
					$addedspouse_gender = $addedCriteria[0][12]['matrix_value'];

					$spouse_smoking_status = $value[13]['matrix_value'];
					$addedspouse_smoking_status = $addedCriteria[0][13]['matrix_value'];

					$spouse_tobacco_status = $value[14]['matrix_value'];
					$addedspouse_tobacco_status = $addedCriteria[0][14]['matrix_value'];

					$spouse_height_feet = $value[15]['spouse_height_feet'];
					$addedspouse_height_feet = $addedCriteria[0][15]['spouse_height_feet'];

					$spouse_height_inch = $value[15]['spouse_height_inch'];
					$addedspouse_height_inch = $addedCriteria[0][15]['spouse_height_inch'];

					$spouse_weight = $value[16]['spouse_weight'];
					$addedspouse_weight = $addedCriteria[0][16]['spouse_weight'];

					$spouse_weight_type = $value[16]['spouse_weight_type'];
					$addedspouse_weight_type = $addedCriteria[0][16]['spouse_weight_type'];

					$benefit_amount = $value[17]['matrix_value'];
					$addedbenefit_amount = $addedCriteria[0][17]['matrix_value'];



					if( 
						($matrixPlanType ==	$addedmatrixPlanType) &&
						($enrolleeMatrix == $addedenrolleeMatrix) &&
						($age_from == $addedage_from) && 
						($age_to == $addedage_to) &&
						($state == $addedstate) &&
						($zip == $addedzip) &&
						($gender ==	$addedgender) &&
						($smoking_status == $addedsmoking_status) &&
						($tobacco_status == $addedtobacco_status) &&
						($height_by == $addedheight_by) &&
						($height_feet == $addedheight_feet) &&
						($height_inch == $addedheight_inch) &&
						($height_feet_to == $addedheight_feet_to) &&
						($height_inch_to == $addedheight_inch_to) &&
						($weight_by == $addedweight_by) &&
						($weight == $addedweight) &&
						($weight_to == $addedweight_to) &&
						($no_of_children_by == $addedno_of_children_by) &&
						($no_of_children == $addedno_of_children) &&
						($no_of_children_to == $addedno_of_children_to) &&
						($has_spouse == $addedhas_spouse) &&
						($spouse_age_from == $addedspouse_age_from) &&
						($spouse_age_to == $addedspouse_age_to) &&
						($spouse_gender == $addedspouse_gender) &&
						($spouse_smoking_status == $addedspouse_smoking_status) &&
						($spouse_tobacco_status == $addedspouse_tobacco_status) &&
						($spouse_height_feet == $addedspouse_height_feet) &&
						($spouse_height_inch == $addedspouse_height_inch) &&
						($spouse_weight == $addedspouse_weight) &&
						($spouse_weight_type == $addedspouse_weight_type) &&
						($benefit_amount == $addedbenefit_amount)
					){
						$response['matrix_group'] = $matrix_group;
					}
				}
			}
		}
		return $response;
	}

	public function getDirectLoaAgents($agent_id)
	{
		global $pdo;
		$agent_sql = "SELECT GROUP_CONCAT(DISTINCT c.id) as agent_ids 
						FROM customer c 
						JOIN customer_settings cs ON(cs.customer_id=c.id) 
						WHERE 
						cs.agent_coded_level='LOA' AND 
						c.sponsor_id=:sponsor_id AND 
						c.type='Agent' AND 
						c.is_deleted='N'";
		$agent_where = array(":sponsor_id" => $agent_id);
		$agent_row = $pdo->selectOne($agent_sql,$agent_where);
		$agent_res = (!empty($agent_row['agent_ids'])?explode(",",$agent_row['agent_ids']):array());
   		return $agent_res;
	}
	/**
	 * Generate Merchant Processor Display ID
	 */
	public function generateMerchantProcessorDisplayID() {
		global $pdo;
		$rule_code = rand(100000, 999999);
		
		$sql = "SELECT count(id) as total FROM payment_master WHERE processor_code ='MP" . $rule_code . "' OR processor_code ='" . $rule_code . "'";
		$res = $pdo->selectOne($sql);
		if ($res['total'] > 0) {
			return $this->generateMerchantProcessorDisplayID();
		} else {
			return 'MP'.$rule_code;
		}
	}

	/**
	 * setDefaultBillingProfile
	 */
	public function setDefaultBillingProfile($customer_id,$billing_profile_id) {
		global $pdo;

		$pdo->update("customer_billing_profile", array("is_default" => "N"), array("clause" => "customer_id=:customer_id", "params" => array(":customer_id" => $customer_id)));

		$pdo->update("customer_billing_profile", array("is_default" => "Y"), array("clause" => "id=:id", "params" => array(":id" => $billing_profile_id)));
		
		return true;
	}

	public function getLifeEvents()
	{
		return array(
			"Age Out" => "Age Out",
			"Citizenship" => "Citizenship",
			"Death of plan participant" => "Death of plan participant",
			"Divorce" => "Divorce",
			"Employment Change" => "Employment Change",
			"Having/Adopting Child" => "Having/Adopting Child",
			"Marriage" => "Marriage",
			"Moving" => "Moving",
		);
	}

	public function getLifeEventLabelByKey($key = "")
	{
		$LifeEvents = $this->getLifeEvents();
		return (isset($LifeEvents[$key])?$LifeEvents[$key]:"");
	}
	/**
	 * getNonPaymentCoverage
	 */
	public function getPaymentFailedCoverages($customerId,$wsId,$extraParams = array()){
		global $pdo,$ADMIN_HOST;
		$res = array();
		$res["failCoverage"] = 'N';

	 	$wsSql = "SELECT ws.id,ws.website_id as websiteId,ws.end_coverage_period,
	 				p.name as prdName,p.product_code as prdCode,
	 				c.id as mbrId,c.rep_id as mbrDispId,GROUP_CONCAT(c.fname,' ',c.lname) as mbrName,
	 				c.fname as mbrFname,c.lname as mbrLname,ws.next_purchase_date as nextBillingDate,ws.eligibility_date as effectiveDate
	                    FROM website_subscriptions ws
	                    JOIN customer c ON(c.id=ws.customer_id)
	                    JOIN prd_main p ON(p.id=ws.product_id) 
	                    WHERE md5(ws.id)=:wsId AND md5(ws.customer_id)=:customerId
	                    GROUP BY ws.id";
	    $wsParams = array(":wsId" => $wsId,":customerId" => $customerId);
	    $wsRow = $pdo->selectOne($wsSql,$wsParams);

	    if(!empty($wsRow)){

	    	$policyCoveragePeriods = $this->subscriptionCoveragePeriods($wsRow["id"],'N',array("nextBillingDate" => $wsRow["nextBillingDate"]));
	    	// pre_print($policyCoveragePeriods);
	    	$failedCoverageArr = array();
	    	$effectiveDate = $wsRow["effectiveDate"];
	    	$policyEndCoverage = $wsRow["end_coverage_period"];
	    	if(!empty($policyCoveragePeriods)){
		    	foreach ($policyCoveragePeriods as $covRow) {
	    			$startCoveragePeriod = $covRow["start_coverage_period"];
	    			$endCoveragePeriod = $covRow["end_coverage_period"];
	    			
	    			// startCoveragePeriod start from effective date and check upto end coverage period, current coverage should not be check may be coverage payment in failed renewal settings.

	    			if($startCoveragePeriod >= $effectiveDate && $startCoveragePeriod < $policyEndCoverage){
		    			$odrSql = "SELECT o.id 
		    				FROM orders o
					        JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
					        WHERE o.status IN('Payment Approved') 
					        AND od.is_refund='N' AND  od.is_chargeback='N' AND od.is_payment_return='N' 
					        AND od.website_id=:website_id AND od.start_coverage_period=:start_coverage_period";
						$odrWhere = array(
							":website_id" => $wsRow["id"],
							":start_coverage_period" => $startCoveragePeriod,
						);
					  	$odrRes = $pdo->selectOne($odrSql,$odrWhere);
					  	
					  	if(empty($odrRes["id"])){
					  		$failOdrSql = "SELECT o.id as odrId,o.display_id as odrDispId,
					  		o.status as odrStatus,od.id as odrDetailId,
					  		od.start_coverage_period,od.end_coverage_period,
					  		p.name as prdName,p.product_code as prdCode,
					  		od.is_refund,od.is_chargeback,od.is_payment_return
			    				FROM orders o
						        JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
						        JOIN prd_main p ON(p.id=od.product_id)
						        WHERE (o.status NOT IN('Payment Approved') OR od.is_refund='N' OR od.is_chargeback='N' OR od.is_payment_return='N')
						        AND od.website_id=:website_id AND od.start_coverage_period=:start_coverage_period
						        ORDER BY o.id,od.id DESC";
							$failOdrWhere = array(
								":website_id" => $wsRow["id"],
								":start_coverage_period" => $startCoveragePeriod,
							);
						  	$failOdrRes = $pdo->selectOne($failOdrSql,$failOdrWhere);

						  	if(!empty($failOdrRes)){
						  		$odrStatus = $failOdrRes["odrStatus"];
						  		if($odrStatus=='Void'){
									$odrStatus = "Void";
								}else if($failOdrRes["is_refund"] == "Y" || $odrStatus=='Refund'){
								    $odrStatus = "Refund";
						  		}else if($failOdrRes["is_chargeback"] == "Y"){
						  			$odrStatus = "Chargeback";
						  		}else if($failOdrRes["is_payment_return"] == "Y"){
						  			$odrStatus = "Payment Returned";
						  		}
						  		
						  		if(!empty($extraParams["checkFailCov"]) && $extraParams["checkFailCov"] == 'Y'){
						  			$res["failCoverage"] = 'Y';
						  			return $res;
						  			exit;
						  		}else if(!empty($extraParams["getFailCov"]) && $extraParams["getFailCov"] == 'Y'){
									$failedCoverageArr[] = array(
										"odrId" => $failOdrRes["odrId"],
										"odrDispId" => $failOdrRes["odrDispId"],
										"odrDetailId" => $failOdrRes["odrDetailId"],
										"prdName" => $failOdrRes["prdName"],
										"prdCode" => $failOdrRes["prdCode"],
										"odrStatus" => $odrStatus,
										"start_coverage_period" => $failOdrRes["start_coverage_period"],
										"end_coverage_period" => $failOdrRes["end_coverage_period"],
									);
						  		}else{
						  			$extCovSql = "SELECT id FROM payment_failed_coverages WHERE customer_id=:customer_id AND order_id=:order_id AND order_detail_id=:order_detail_id AND is_deleted='N' AND is_paid='N'";
							  		$extCovParams = array(
						  				":customer_id" => $wsRow["mbrId"],
						  				":order_id" => $failOdrRes["odrId"],
						  				":order_detail_id" => $failOdrRes["odrDetailId"],
									);
							  		$extCovRes = $pdo->selectOne($extCovSql,$extCovParams);
							  		if(empty($extCovRes["id"])){
							  			$failedCoverageArr[] = array(
							  				"odrId" => $failOdrRes["odrId"],
											"odrDispId" => $failOdrRes["odrDispId"],
											"odrDetailId" => $failOdrRes["odrDetailId"],
											"prdName" => $failOdrRes["prdName"],
											"prdCode" => $failOdrRes["prdCode"],
											"odrStatus" => $odrStatus,
											"start_coverage_period" => $failOdrRes["start_coverage_period"],
											"end_coverage_period" => $failOdrRes["end_coverage_period"],
										);
							  		}
						  		}
						  	}
					  	}
					}
		    	}

		    	if(!empty($extraParams["getFailCov"]) && $extraParams["getFailCov"] == 'Y'){
		    		$res["coverageData"] = $failedCoverageArr;
		    		return $res;
		    		exit;
		    	}else if(!empty($failedCoverageArr)){
	    			$message = "<p><strong>Missing Coverage Payment</strong></p><p>Member below has an active policy but is missing an approved payment for the coverage period listed below.  Please visit member account to resolve issue.</p><br/><p>Member: " . $wsRow['mbrName'] . " (". $wsRow['mbrDispId'] .")</p><p>Policy ID: " . $wsRow['websiteId'] . "</p><p><p>Product: " . $wsRow['prdName'] . " (". $wsRow['prdCode'] .")</p>";

	    			foreach ($failedCoverageArr as $row) {
	    				$message .= "<p>Order ID: ". $row["odrDispId"]."</p>";
	    				$message .= "<p>Coverage Period: ". displayDate($row["start_coverage_period"]) ." - ".displayDate($row["end_coverage_period"])."</p>";
	    			}
	    			$link = $ADMIN_HOST."/payment_failed_coverages.php";
					$message .= "<br/><p>You can see all missed coverage payments on: <a href='".$link."'>".$link."</a></p>";
	    			$sessionArr = array('System'=>'System');
	                $res = $this->createNewTicket($sessionArr,17,"Missing Coverage Payment",0,$message,$wsRow["mbrId"],'Customer','',array(),'notes',$wsRow["id"]);

	                $ac_descriptions_ti['ac_message'] =array(
                        'ac_red_1'=>array(
                          'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($wsRow["mbrId"]),
                          'title'=>$wsRow['mbrDispId'],
                        ),
                        'ac_message_1' =>' E-Ticket Opened For Policy '.$wsRow['websiteId']
                    );
                    activity_feed(3, $wsRow["mbrId"], 'Customer', $wsRow["mbrId"], 'Customer', 'E-Ticket Opened', $wsRow['mbrFname'], $wsRow['mbrLname'], json_encode($ac_descriptions_ti));

	                foreach ($failedCoverageArr as $row) {
	                	$insParams = array(
	                				"customer_id" => $wsRow["mbrId"],
	                				"website_id" => $wsRow["id"],
	                				"order_id" => $row["odrId"],
	                				"order_detail_id" => $row["odrDetailId"],
	                				"ticket_id" => $res["ticket_id"],
	                			);
	                	$pdo->insert("payment_failed_coverages",$insParams);
	    			}
	    		}
	    	}
	    }
	    return $res;
	}
	/**
	 * get Group Payment Options
	 */
	public static function getGroupPayOptions($group_id = 0){
		global $pdo;
		$pay_options = array();
		$pay_options = $pdo->selectOne("SELECT * FROM group_pay_options 
										WHERE is_deleted='N' AND group_id=:group_id AND rule_type='Variation' AND group_id!=0",array(":group_id"=>$group_id));
		if(empty($pay_options)) {
			$pay_options = $pdo->selectOne("SELECT * FROM group_pay_options 
										WHERE is_deleted='N' AND rule_type='Global'");
		}
		return $pay_options;
	}
	/**
	 * product matrix validation code
	 */
	public static function prdMatrixValidation($pricingMatrixKey,$globalMatrixEnrolleeCriteriaArr,$from){
		$i=0;
		$tmppricing = $tmppricingMatrixKey = array();
		$fromKey = $from;
		foreach($pricingMatrixKey as $key => $matrixArr){
			$tmppricingMatrixKey[$key] = $matrixArr;
			foreach($matrixArr as $k => $fieldArr){
				foreach($fieldArr as $fieldKey => $fieldValArr){
					if(in_array($fieldKey,array('RetailPrice','NonCommissionablePrice','CommissionablePrice','pricing_matrix_effective_date','pricing_matrix_termination_date','newPricingMatrixOnRenewals'))){
						$tmppricingMatrixKey[$key][$k]['tmp_price_arr'][$fieldKey] = $fieldValArr;
						$tmppricing[$key][$k]['tmp_price_arr'][$fieldKey] = $fieldValArr;
						
					}else if((is_numeric($fieldKey) && $fieldKey > 0 )|| $fieldKey == $fromKey){
						if($fieldKey != $fromKey){
							$fieldValArr = str_replace(',','',$fieldValArr['matrix_value']);
							$fieldValArr = trim($fieldValArr);
							if(str_replace(' ','',$fieldValArr) == 'To'){
								$fieldValArr = '0 To 0';
							}
						}
						$tmppricingMatrixKey[$key][$k]['tmp_key_arr'][$fieldKey] = (($fieldKey == $fromKey) ? $fieldValArr : $fieldValArr);
					}
				}
			}
		}
		$tmpglobalMatrixEnrolleeCriteriaArr = array();
		foreach($globalMatrixEnrolleeCriteriaArr as $key =>$gval){
			if((is_numeric($key) && $key > 0 ) || $key == $fromKey){
				$gval = str_replace(',','',$gval);
				$gval = trim($gval);
				if(str_replace(' ','',$gval) == 'To'){
					$gval = '0 To 0';
				}
				
				$tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'][$key] = $gval;
			}else{
				$tmpglobalMatrixEnrolleeCriteriaArr[$key] = $gval;
			}
		}
		$insertMatrix = false;
		foreach($tmppricingMatrixKey as $key => $matrixArr){
			$i++;
			if(empty($globalMatrixEnrolleeCriteriaArr['matrixID']) || ($key != $globalMatrixEnrolleeCriteriaArr['matrixID'])){
				foreach($matrixArr as $k => $fieldArr){
					$showError = false;
					foreach($fieldArr as $fieldKey => $fieldValArr){
						
						if($fieldKey == "tmp_key_arr"){
							if($tmpglobalMatrixEnrolleeCriteriaArr[$fieldKey][$fromKey] == $fieldValArr[$fromKey]){
								if(array_diff_assoc($fieldValArr,$tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'])){
									if($showError){
										// pre_print($fieldValArr,false);
										// pre_print($tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'],false);
										// pre_print(array_diff_assoc($tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'],$fieldValArr),false);
										pre_print(array_diff_assoc($fieldValArr,$tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr']),false);
										echo "Array : ".$i;
										echo "<br><br>";
									}
									$insertMatrix = true;
									continue;
								}else{
									return false;
								}
							}else if($tmpglobalMatrixEnrolleeCriteriaArr[$fieldKey][$fromKey] != $fieldValArr[$fromKey]){
								if(array_diff_assoc($fieldValArr,$tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'])){
									if($showError){
										// pre_print($fieldValArr,false);
										// pre_print($tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'],false);
										// pre_print(array_diff_assoc($tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'],$fieldValArr),false);
										pre_print(array_diff_assoc($fieldValArr,$tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr']),false);
										echo "Array : ".$i;
										echo "<br><br>";
									}
									$insertMatrix = true;
									continue;
								}else{
									return false;
								}
							}else{
								if(!array_diff_assoc($fieldValArr,$tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr'])){
									if($showError){
										pre_print(array_diff_assoc($fieldValArr,$tmpglobalMatrixEnrolleeCriteriaArr['tmp_key_arr']),false);
										echo "Array : ".$i;
										echo "<br><br>";
									}
									return false;
								}
							}
						}
					}
				}
			}else{
				$insertMatrix = true;
			}
		}
		return $insertMatrix;
	}

	public static function compareArray($old_array,$new_array){
		$is_same = false;
		if(count($new_array) > 0){
			foreach ($new_array as $key => $value) {
				if(is_array($value)){
					if(!array_diff_assoc($old_array,$value)){
						$is_same = true;
						continue;
					}
				}
			}
		}
		return $is_same;
	}
	/**
	 * addCommunicationRequest
	 */
	public static function addCommunicationRequest($to_user_id,$to_user_type,$trigger_id,$commun_data){
		global $pdo;
		$req_url = (isset($_SERVER["HTTP_HOST"])?($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]):'');
		if(isset($commun_data['req_url'])) {
			$req_url = $commun_data['req_url'];
		}
		$insert_data = array(
			"to_user_id" => $to_user_id,
			"to_user_type" => $to_user_type,
			"trigger_id" => $trigger_id,
			"type" => (isset($commun_data['type'])?$commun_data['type']:''),
			"to_phone" => (isset($commun_data['to_phone'])?$commun_data['to_phone']:''),
			"sms_params" => (!empty($commun_data['sms_params'])?json_encode($commun_data['sms_params']):''),
			"to_email" => (isset($commun_data['to_email'])?$commun_data['to_email']:''),
			"email_params" => (!empty($commun_data['email_params'])?json_encode($commun_data['email_params']):''),
			"status" => "Pending",
			"extra_params" => (!empty($commun_data['extra_params'])?json_encode($commun_data['extra_params']):''),
			"req_url" => $req_url,
		);
		$pdo->insert("communication_requests",$insert_data);
		return true;
	}
	
	public static function hasExternalJsCss($desc){
		$res = false;
		if(preg_match('/<link\s+(?:[^>]*?\s+)?href="([^"]*)"/',$desc)) {
			$res = true;
		}
		if(preg_match("/<script.*>/im",$desc)) {
			$res = true;
		}
		return $res;
	}
	public function getProductJoinderAgreement($customer_id,$product_id){
		global $pdo;
		$resAgreement = array();
		if(!empty($customer_id) && !empty($product_id)){
			$selAgreement = "SELECT id FROM joinder_agreements WHERE customer_id=:customer_id AND product_id=:product_id AND is_generated='Y' AND is_deleted='N'";
			$agreementParams = array(":customer_id" => $customer_id,":product_id" => $product_id);
			$resAgreement = $pdo->selectOne($selAgreement,$agreementParams);
		}
		return $resAgreement;
	}

	public function getSignatureFromS3Bucket($signature){
		global $S3_KEY,$S3_REGION,$S3_SECRET,$S3_BUCKET_NAME,$SIGNATURE_FILE_PATH;

		if(empty($signature)){
			return '';
		} else {
			$s3ClientObject = new S3Client([
	          	'version' => 'latest',
	          	'region'  => $S3_REGION,
	          	'credentials'=>array(
	              	'key'=> $S3_KEY,
	              	'secret'=> $S3_SECRET
	          	)
	      	]);
			$s3ClientObject->registerStreamWrapper();

			$result = $s3ClientObject->getObject(array(
			    'Bucket' => $S3_BUCKET_NAME,
			    'Key'    => $SIGNATURE_FILE_PATH.$signature 
		  	));

		  	$signature_data = !empty($result) && !empty($result['@metadata']) && !empty($result['@metadata']['effectiveUri']) ? $result['@metadata']['effectiveUri'] : '';
		  	if(!empty($signature_data)){
		  		return $signature_data;
		  	} else {
		  		return '';
		  	}
	  	}
	}
	public function getAgencyId($agent_id){
		global $pdo;
		$agencyId = 0;

		$selAgent = "SELECT c.id,c.sponsor_id,c.upline_sponsors
    			FROM customer c
    			JOIN customer_settings cs ON(c.id=cs.customer_id AND cs.account_type!='Business')
    			WHERE c.type='Agent' AND c.is_deleted='N' AND c.id=:agent_id";
		$resAgent = $pdo->selectOne($selAgent,array(":agent_id" => $agent_id));

		if(!empty($resAgent)){
			$upline_sponsors = !empty($resAgent["upline_sponsors"]) ? explode(",", $resAgent["upline_sponsors"]) : array();
			$upline_sponsors_arr = !empty($upline_sponsors) ? array_reverse(array_filter($upline_sponsors)) : array();
			
			if(!empty($upline_sponsors_arr)){
				foreach ($upline_sponsors_arr as $uplineAgentId) {

					$selAgent = "SELECT c.id,c.sponsor_id,c.upline_sponsors
			    			FROM customer c
			    			JOIN customer_settings cs ON(c.id=cs.customer_id AND cs.account_type='Business')
			    			WHERE c.type='Agent' AND c.is_deleted='N' AND c.id=:agent_id";
					$resAgent = $pdo->selectOne($selAgent,array(":agent_id" => $uplineAgentId));

					if(!empty($resAgent["id"])){
						$agencyId = $resAgent["id"];
						break;
					}
				}
			}
		}
		return $agencyId;
	}
	public function updateDownlineGroup($agentId){
		global $pdo;
		if(!empty($agentId)){
		$selAgent = "SELECT id,level,sponsor_id,upline_sponsors,rep_id FROM customer WHERE is_deleted='N' AND type='Agent' AND id=:agentId";
      	$resAgent = $pdo->selectOne($selAgent,array(":agentId" => $agentId));

      	if(!empty($resAgent["id"])){
      		$upline_sponsors = $resAgent["upline_sponsors"] . $resAgent['id'] . ',';
			$level = $resAgent["level"] + 1;

      		$selGroup = "SELECT id FROM customer WHERE is_deleted='N' AND type='Group' AND sponsor_id=:id";
			$resGroup = $pdo->select($selGroup, array(":id" => $resAgent["id"]));
			
			if(!empty($resGroup)){
				foreach ($resGroup as $groupRow) {
				// update Group upline details code start
					$updGroupParams = array(
			          "level" => $level,
			          "upline_sponsors" => $upline_sponsors,
			        );
			        $updGroupWhere = array(
			          'clause' => "id = :id",
			          'params' => array(':id' => $groupRow['id']),
			        );
			        $pdo->update('customer', $updGroupParams, $updGroupWhere);
		        // update Group upline details code end

		        // Update downline members underneath group code start
			        $selCust = "SELECT id FROM customer WHERE is_deleted='N' AND type='Customer' AND sponsor_id=:id";
					$resCust = $pdo->selectOne($selCust, array(":id" => $groupRow["id"]));

					$mbr_upline_sponsors = $upline_sponsors . $groupRow['id'] . ',';
					$mbr_level = $level + 1;

					if(!empty($resCust['id'])){
				        $updCustParams = array(
				          "level" => $mbr_level,
				          "upline_sponsors" => $mbr_upline_sponsors,
				        );
				        $updCustWhere = array(
				          'clause' => "is_deleted='N' AND type='Customer' AND sponsor_id = :id",
				          'params' => array(':id' => $groupRow['id']),
				        );
				        $pdo->update('customer', $updCustParams, $updCustWhere);


				        $selCustEnroll = "SELECT id FROM customer_enrollment WHERE sponsor_id=:id";
					    $resCustEnroll = $pdo->selectOne($selCustEnroll, array(":id" => $groupRow["id"]));

					    if(!empty($resCustEnroll['id'])){
					        $updEnrollParams = array(
					          "level" => $mbr_level,
					          "upline_sponsors" => $mbr_upline_sponsors,
					        );
					        $updEnrollWhere = array(
					          'clause' => 'sponsor_id = :id',
					          'params' => array(':id' => $groupRow["id"]),
					        );
					        $pdo->update('customer_enrollment', $updEnrollParams, $updEnrollWhere);
					    }
				    }
				// Update downline members underneath group code ends
				}
			}
		}
		}
	}
	public function updateDownlineMember($agentId){
		global $pdo;
		if(!empty($agentId)){
		$selAgent = "SELECT id,level,sponsor_id,upline_sponsors,rep_id FROM customer WHERE is_deleted='N' AND type='Agent' AND id=:agentId";
      	$resAgent = $pdo->selectOne($selAgent,array(":agentId" => $agentId));

      	if(!empty($resAgent["id"])){
      		$selCust = "SELECT id FROM customer WHERE is_deleted='N' AND type='Customer' AND sponsor_id=:id";
			$resCust = $pdo->selectOne($selCust, array(":id" => $resAgent["id"]));

			$upline_sponsors = $resAgent["upline_sponsors"] . $resAgent['id'] . ',';
			$level = $resAgent["level"] + 1;

			if(!empty($resCust['id'])){
		        $updCustParams = array(
		          "level" => $level,
		          "upline_sponsors" => $upline_sponsors,
		        );
		        $updCustWhere = array(
		          'clause' => "is_deleted='N' AND type='Customer' AND sponsor_id = :id",
		          'params' => array(':id' => $resAgent['id']),
		        );
		        $pdo->update('customer', $updCustParams, $updCustWhere);


		        $selCustEnroll = "SELECT id FROM customer_enrollment WHERE sponsor_id=:id";
			    $resCustEnroll = $pdo->selectOne($selCustEnroll, array(":id" => $resAgent["id"]));

			    if(!empty($resCustEnroll['id'])){
			        $updEnrollParams = array(
			          "level" => $level,
			          "upline_sponsors" => $upline_sponsors,
			        );
			        $updEnrollWhere = array(
			          'clause' => 'sponsor_id = :id',
			          'params' => array(':id' => $resAgent["id"]),
			        );
			        $pdo->update('customer_enrollment', $updEnrollParams, $updEnrollWhere);
			    }
		    }
      	}
      	}
	}
	public function updateGroupMembers($groupId){
		global $pdo;
		if(!empty($groupId)){

      		$selGroup = "SELECT id,level,sponsor_id,upline_sponsors,rep_id FROM customer WHERE is_deleted='N' AND type='Group' AND id=:id";
			$resGroup = $pdo->selectOne($selGroup, array(":id" => $groupId));
			
			if(!empty($resGroup["id"])){
	        // Update downline members underneath group code start
		        $selCust = "SELECT id FROM customer WHERE is_deleted='N' AND type='Customer' AND sponsor_id=:id";
				$resCust = $pdo->selectOne($selCust, array(":id" => $resGroup["id"]));

				$mbr_upline_sponsors = $resGroup['upline_sponsors'] . $resGroup['id'] . ',';
				$mbr_level = $resGroup['level'] + 1;

				if(!empty($resCust['id'])){
			        $updCustParams = array(
			          "level" => $mbr_level,
			          "upline_sponsors" => $mbr_upline_sponsors,
			        );
			        $updCustWhere = array(
			          'clause' => "is_deleted='N' AND type='Customer' AND sponsor_id = :id",
			          'params' => array(':id' => $resGroup['id']),
			        );
			        $pdo->update('customer', $updCustParams, $updCustWhere);


			        $selCustEnroll = "SELECT id FROM customer_enrollment WHERE sponsor_id=:id";
				    $resCustEnroll = $pdo->selectOne($selCustEnroll, array(":id" => $resGroup["id"]));

				    if(!empty($resCustEnroll['id'])){
				        $updEnrollParams = array(
				          "level" => $mbr_level,
				          "upline_sponsors" => $mbr_upline_sponsors,
				        );
				        $updEnrollWhere = array(
				          'clause' => 'sponsor_id = :id',
				          'params' => array(':id' => $resGroup["id"]),
				        );
				        $pdo->update('customer_enrollment', $updEnrollParams, $updEnrollWhere);
				    }
			    }
			// Update downline members underneath group code ends
			}
		}
	}

	public function getExpireCreditCardProfiles($expireMonth,$expireYear){
		global $pdo;

		$selProfile = "SELECT c.id AS mbrId,c.email AS mbrEmail,c.rep_id,
			COUNT(IF(ws.status IN('Active') AND ws.termination_date,ws.id,'')) AS activePolicy 
			FROM customer_billing_profile cb
			JOIN customer c ON (c.is_deleted='N' AND c.type='Customer' AND c.status='Active' AND cb.customer_id=c.id)
			JOIN website_subscriptions ws ON (ws.status='Active' AND ws.product_type!='Fees' AND (ws.termination_date IS NULL OR ws.termination_date > CURDATE()) AND ws.customer_id=c.id)
			WHERE cb.payment_mode='CC' AND cb.is_deleted='N' AND cb.is_default='Y'
			AND cb.expiry_month=:expireMonth AND cb.expiry_year=:expireYear
			GROUP BY c.id HAVING activePolicy > 0";
		$paramsProfile = array(":expireMonth" => $expireMonth,":expireYear" => $expireYear);
		$resProfile = $pdo->select($selProfile,$paramsProfile);
		
		$triggerRow = $pdo->selectOne("SELECT id,display_id FROM triggers WHERE display_id='T847' AND is_deleted='N'");
		
		if(!empty($resProfile) && !empty($triggerRow["id"])){
			foreach($resProfile as $row){
				//SEND MAIL CODE START
				$triggerId = $triggerRow["id"];
				$email_params = get_user_smart_tags($row['mbrId'],'member'); 
				$mailStatus = trigger_mail($triggerId, $email_params, $row['mbrEmail']);
				//SEND MAIL CODE END
			}
		}
	}

	/** save agent contract in s3 bucket. (task => EL8-1170) **/
	public function saveAgentContract($agentId = 0,$signature = '',$location = ''){
		global $pdo,$AGENT_AGREEMENT_CONTRACT_FILE_PATH,$S3_REGION,$S3_KEY,$S3_SECRET,$HOST,$S3_BUCKET_NAME,$UPLOAD_DIR,$SIGNATURE_FILE_PATH;

		$upload_file_url = $UPLOAD_DIR.'temp';

		if (!file_exists($upload_file_url)) {
		    mkdir($upload_file_url, 0777, true);
		}	

		$temp_file_uploads = $upload_file_url . '/';

		$signature_data = $pdf_html_code = '';

		if(!empty($agentId)){
			$agentInfo = $pdo->selectOne("SELECT c.id,c.rep_id,c.fname,c.lname,c.cell_phone,c.email,cs.ip_address,c.joined_date,cs.signature_file FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
		    WHERE c.is_deleted='N' AND type='Agent' AND c.id=:id
		    ",array(":id"=>$agentId));

		    $res_t =$pdo->selectOne('SELECT terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Agent',":status"=>'Active')); 
		    if(!empty($agentInfo['id'])){
		    	$joinedDate = !empty($agentInfo['joined_date']) && strtotime($agentInfo['joined_date']) > 0 ? $agentInfo['joined_date'] : date('Y-m-d');
		        $terms = trim($res_t['terms']);
		        $smart_tags = get_user_smart_tags($agentInfo['id'],'Agent');
			    if($smart_tags){
			      foreach ($smart_tags as $key => $value) {
			      	if(!empty($location) && $location == 'temp_script' && $key == 'Date'){
			      		$terms = str_replace("[[" . $key . "]]", date('m/d/Y',strtotime($joinedDate)), $terms);
			      	} else if(!empty($location) && $location == 'temp_script' && $key == 'Day'){
			      		$terms = str_replace("[[" . $key . "]]", date('j',strtotime($joinedDate)), $terms);
			      	} else if(!empty($location) && $location == 'temp_script' && $key == 'Month'){
			      		$terms = str_replace("[[" . $key . "]]", date('F',strtotime($joinedDate)), $terms);
			      	} else {
			        	$terms = str_replace("[[" . $key . "]]", $value, $terms);
			    	}
			      }
			    }

			    if(!empty($signature)){
			    	$doesFileExist = $this->doesFileExistFromS3Bucket($SIGNATURE_FILE_PATH,$signature);

			    	if(!empty($doesFileExist)){
			        	$signature_data = $this->getSignatureFromS3Bucket($signature);
			    	}
			    }

			    $s3Client = new S3Client([
			        'version' => 'latest',
			        'region'  => $S3_REGION,
			        'credentials'=>array(
			            'key'=> $S3_KEY,
			            'secret'=> $S3_SECRET
			        )
			    ]);

			    ob_start();
			  	include __DIR__ . '/../tmpl/agent_agreement.inc.php';
			  	$pdf_html_code = ob_get_clean();

			  	require_once __DIR__ . '/../libs/mpdf/vendor/autoload.php';
			  	
			  	$filename = "AgentAgreement_".$agentInfo['rep_id']."_".strtotime("now"). ".pdf";
			  	$file_path = $temp_file_uploads . $filename;
			  	
			  	$mpdf = new \Mpdf\Mpdf();
			  	$arrContextOptions=array(
				    "ssl"=>array(
				        "verify_peer"=>false,
				        "verify_peer_name"=>false,
				    ),
				);
			  	$stylesheet = file_get_contents($HOST.'/css/mpdf_common_style.css', false, stream_context_create($arrContextOptions));
			  	$mpdf->WriteHTML($stylesheet,1);
			  	$mpdf->WriteHTML($pdf_html_code,2);
			  	$mpdf->use_kwt = true;
			  	$mpdf->shrink_tables_to_fit = 1;
			  	$mpdf->Output($file_path,"F");

			  	try{
				  	$result = $s3Client->putObject(array(
		                'Bucket' => $S3_BUCKET_NAME,
		                'Key' => $AGENT_AGREEMENT_CONTRACT_FILE_PATH.$filename,
		                'SourceFile' => $file_path,
		                'ACL' => 'public-read'
		            ));
			  	} catch (Exception $e){
			  		echo $e->getMessage();
			  		exit;
			  	}
			  	unlink($file_path);
	            return $filename;
		    } else {
				return '';
		    }
		}
	}

	/** File Get from AWS S3 Bucket **/
	public function getAwsS3Bucket($file_path,$file_name=''){

		global $S3_REGION,$S3_KEY,$S3_SECRET,$S3_BUCKET_NAME;
		$s3Client = new S3Client([
			'version' => 'latest',
			'region'  => $S3_REGION,
			'credentials'=>array(
				'key'=> $S3_KEY,
				'secret'=> $S3_SECRET
			)
		]);

		$result = $s3Client->getObject([
			'Bucket' => $S3_BUCKET_NAME,
			'Key'    => $file_path.$file_name,
		]);

		return $result;
	}

	/** Check in s3 bucket file exist or Not **/
	public function doesFileExistFromS3Bucket($path,$file_name){
		global $S3_BUCKET_NAME,$S3_KEY,$S3_SECRET,$S3_REGION;
		$s3Client = new S3Client([
			'version' => 'latest',
			'region'  => $S3_REGION,
			'credentials'=>array(
				'key'=> $S3_KEY,
				'secret'=> $S3_SECRET
			)
		]);
		$response = $s3Client->doesObjectExist($S3_BUCKET_NAME,$path.$file_name);
		return $response;
	}

	/** save admin contract in s3 bucket. (task => EL8-1219) **/
	public function saveAdminContract($adminId = 0){
		global $pdo,$ADMIN_AGREEMENT_CONTRACT_FILE_PATH,$S3_REGION,$S3_KEY,$S3_SECRET,$HOST,$S3_BUCKET_NAME,$UPLOAD_DIR;

		$upload_file_url = $UPLOAD_DIR.'temp';

		if (!file_exists($upload_file_url)) {
		    mkdir($upload_file_url, 0777, true);
		}	

		$temp_file_uploads = $upload_file_url . '/';

		$pdf_html_code = '';

		if(!empty($adminId)){
			$admin_sql = "SELECT a.*,af.ip_address 
						FROM admin a 
						LEFT JOIN activity_feed af ON(af.user_id = a.id AND af.user_type = 'Admin' AND af.entity_action='Admin Invite Accepted') 
						WHERE a.id=:id";
			$admin_row =$pdo->selectOne($admin_sql,array(':id' => $adminId));

		    $res_t =$pdo->selectOne("SELECT md5(id) as id,type,terms FROM terms WHERE type='Admin' and status='Active'"); 
		    if(!empty($res_t['terms']) && !empty($admin_row['id'])){
		   
		        $terms = trim($res_t['terms']);

			    $s3Client = new S3Client([
			        'version' => 'latest',
			        'region'  => $S3_REGION,
			        'credentials'=>array(
			            'key'=> $S3_KEY,
			            'secret'=> $S3_SECRET
			        )
			    ]);

			    ob_start();
			  	include __DIR__ . '/../tmpl/admin_agreement.inc.php';
			  	$pdf_html_code = ob_get_clean();

			  	require_once __DIR__ . '/../libs/mpdf/vendor/autoload.php';
			  	
			  	$filename = "AdminAgreement_".$admin_row['display_id']."_".strtotime("now"). ".pdf";
			  	$file_path = $temp_file_uploads . $filename;
			  	
			  	$mpdf = new \Mpdf\Mpdf();
			  	$arrContextOptions=array(
				    "ssl"=>array(
				        "verify_peer"=>false,
				        "verify_peer_name"=>false,
				    ),
				);
			  	$stylesheet = file_get_contents($HOST.'/css/mpdf_common_style.css', false, stream_context_create($arrContextOptions));
			  	$mpdf->WriteHTML($stylesheet,1);
			  	$mpdf->WriteHTML($pdf_html_code,2);
			  	$mpdf->use_kwt = true;
			  	$mpdf->shrink_tables_to_fit = 1;
			  	$mpdf->Output($file_path,"F");

			  	try{
				  	$result = $s3Client->putObject(array(
		                'Bucket' => $S3_BUCKET_NAME,
		                'Key' => $ADMIN_AGREEMENT_CONTRACT_FILE_PATH.$filename,
		                'SourceFile' => $file_path,
		                'ACL' => 'public-read'
		            ));
			  	} catch (Exception $e){
			  		echo $e->getMessage();
			  		exit;
			  	}
			  	unlink($file_path);
	            return $filename;
		    } else {
				return '';
		    }
		}
	}

	/** save group contract in s3 bucket. (task => EL8-1220) **/
	public function saveGroupContract($groupId = 0,$signature = '',$location = ''){
		global $pdo,$GROUP_AGREEMENT_CONTRACT_FILE_PATH,$S3_REGION,$S3_KEY,$S3_SECRET,$HOST,$S3_BUCKET_NAME,$UPLOAD_DIR,$SIGNATURE_FILE_PATH;

		$upload_file_url = $UPLOAD_DIR.'temp';

		if (!file_exists($upload_file_url)) {
		    mkdir($upload_file_url, 0777, true);
		}	

		$temp_file_uploads = $upload_file_url . '/';

		$signature_data = $pdf_html_code = '';

		if(!empty($groupId)){
			$groupInfo = $pdo->selectOne("SELECT c.id,c.rep_id,c.fname,c.lname,c.cell_phone,c.email,cs.ip_address,c.joined_date,cs.signature_file FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
		    WHERE c.is_deleted='N' AND type='Group' AND c.id=:id
		    ",array(":id"=>$groupId));

		    $res_t =$pdo->selectOne('SELECT terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Group',":status"=>'Active')); 
		    if(!empty($groupInfo['id'])){
		    	$joinedDate = !empty($groupInfo['joined_date']) && strtotime($groupInfo['joined_date']) > 0 ? $groupInfo['joined_date'] : date('Y-m-d');
		        $terms = trim($res_t['terms']);

			    if(!empty($signature)){
			    	$doesFileExist = $this->doesFileExistFromS3Bucket($SIGNATURE_FILE_PATH,$signature);

			    	if(!empty($doesFileExist)){
			        	$signature_data = $this->getSignatureFromS3Bucket($signature);
			    	}
			    }

			    $s3Client = new S3Client([
			        'version' => 'latest',
			        'region'  => $S3_REGION,
			        'credentials'=>array(
			            'key'=> $S3_KEY,
			            'secret'=> $S3_SECRET
			        )
			    ]);

			    ob_start();
			  	include __DIR__ . '/../tmpl/group_agreement.inc.php';
			  	$pdf_html_code = ob_get_clean();

			  	require_once __DIR__ . '/../libs/mpdf/vendor/autoload.php';
			  	
			  	$filename = "GroupAgreement_".$groupInfo['rep_id']."_".strtotime("now"). ".pdf";
			  	$file_path = $temp_file_uploads . $filename;
			  	
			  	$mpdf = new \Mpdf\Mpdf();
			  	$arrContextOptions=array(
				    "ssl"=>array(
				        "verify_peer"=>false,
				        "verify_peer_name"=>false,
				    ),
				);
			  	$stylesheet = file_get_contents($HOST.'/css/mpdf_common_style.css', false, stream_context_create($arrContextOptions));
			  	$mpdf->WriteHTML($stylesheet,1);
			  	$mpdf->WriteHTML($pdf_html_code,2);
			  	$mpdf->use_kwt = true;
			  	$mpdf->shrink_tables_to_fit = 1;
			  	$mpdf->Output($file_path,"F");

			  	try{
				  	$result = $s3Client->putObject(array(
		                'Bucket' => $S3_BUCKET_NAME,
		                'Key' => $GROUP_AGREEMENT_CONTRACT_FILE_PATH.$filename,
		                'SourceFile' => $file_path,
		                'ACL' => 'public-read'
		            ));
			  	} catch (Exception $e){
			  		echo $e->getMessage();
			  		exit;
			  	}
			  	unlink($file_path);
	            return $filename;
		    } else {
				return '';
		    }
		}
	}

	/**
	 * Its Used for generate HRM Payment
	 * @param string $orderIds as List Bill id if its not paid else its an order Id
	 * @param string $flag is as status of List Bill
	 * @param date $payDate is a group class paydate
	 */
	public function add_hrm_payments($orderIds = '', $flag = '', $payDateArr)
	{
		global $pdo;
		include_once dirname(__DIR__) . "/includes/hrm_payment.class.php";
		$hrmObj = new HRMPayment();
		$resPayDateArr = [];
		if (!empty($orderIds) && $flag != 'open') {
			$orderSql = "SELECT o.id,ws.customer_id,ws.plan_id,IF(o.is_renewal='L',od.is_renewal,o.is_renewal) as is_renewal,
					lbd.product_id,lbd.ws_id as website_id,c.is_compliant,
					c.sponsor_id,p.is_gap_plus_product AS gap,p.annual_hrm_payment,
					s.type AS sponsor_type,GROUP_CONCAT(DISTINCT(gcp.paydate)) AS payDate,
					p.name,p.product_code,lbd.prd_plan_type_id AS planType,
					od.id AS order_detail_id,gc.pay_period,cs.class_id,lb.id as list_bill_id,IF(gcp.paydate >= lbd.start_coverage_date AND gcp.paydate <=lbd.end_coverage_date,lbd.id,0) AS list_bill_detail_id
					FROM list_bills lb
					JOIN list_bill_details lbd ON (lbd.list_bill_id=lb.id)
					JOIN website_subscriptions ws ON (ws.id=lbd.ws_id)
					JOIN customer c ON (c.id=ws.customer_id AND lbd.customer_id=c.id)
					JOIN customer s ON (s.id=c.sponsor_id)
					JOIN prd_main p ON (p.id=lbd.product_id AND p.is_deleted='N' AND p.type!='Fees')
					JOIN customer_settings cs ON(cs.customer_id = c.id)
					JOIN group_classes gc ON(gc.id=cs.class_id AND gc.is_deleted='N')
					JOIN group_classes_paydates gcp ON(gcp.class_id = gc.id AND gcp.is_deleted='N')
					JOIN orders o ON (o.id=lb.order_id)
					LEFT JOIN order_details od ON (od.order_id=o.id AND od.is_deleted='N' AND od.is_refund='N' AND lbd.id=od.list_bill_detail_id)
					WHERE lb.is_deleted='N' AND o.id IN('" . $orderIds . "') AND c.type IN ('Customer','Group')
					AND gcp.paydate IN("."'".implode("','", $payDateArr)."'".") AND o.status IN('Payment Approved','Completed','Pending Settlement')
					AND p.is_gap_plus_product = 'Y' GROUP BY lbd.id,gcp.paydate,lbd.product_id,c.id HAVING list_bill_detail_id>0";
			$orderRes = $pdo->select($orderSql);
			if (!empty($orderRes)) {

				//Generate NACHA File Start
					if($flag == 'Payment Approved'){
						$groupId = getname('orders',$orderIds,'customer_id');
						$resPayDateArr = array_column($orderRes,'payDate');
					}
				//Generate NACHA File End
				foreach ($orderRes as $order) {
					$weeklyPayPeriod = $hrmObj->getWeeklyPayPeriod($order['payDate']);
					$planType = $order['planType'];
					$selTransactions = "SELECT id FROM transactions WHERE order_id=:odrID AND transaction_status IN('Payment Approved','Pending Settlement') ORDER BY id DESC";
					$resTransactions = $pdo->selectOne($selTransactions, array(":odrID" => $order["id"]));
					$order["transaction_tbl_id"] = 0;
					if (!empty($resTransactions["id"])) {
						$order["transaction_tbl_id"] = $resTransactions["id"];
					}
					if (!empty($order['annual_hrm_payment'])) {
						$amount = json_decode($order['annual_hrm_payment']);
						$hrmAmount = $amount->$planType;
						if($order['pay_period'] == "Monthly"){
							$hrmAmount = round(($hrmAmount / 12),2);
						} else if($order['pay_period'] == "Semi-Monthly"){
							$hrmAmount = round(($hrmAmount / 24),2);
						} else if($order['pay_period'] == "Weekly"){
							$hrmAmount = round(($hrmAmount / 52),2);
						} else if($order['pay_period'] == "Bi-Weekly"){
							$hrmAmount = round(($hrmAmount / 26),2);
						}
						$groupId = $order['sponsor_id'];
						$payer_id = $order['customer_id'];

						$hrmSql = "SELECT id FROM hrm_payment WHERE group_id=:group_id AND pay_period=:pay_period AND website_id=:website_id AND payer_id=:payer_id AND is_deleted='N'";
						$params = array(":group_id" => $groupId, ":pay_period" => $weeklyPayPeriod, ":website_id" => $order['website_id'],":payer_id"=>$payer_id);
						$hrmRes = $pdo->selectOne($hrmSql, $params);

						if(empty($hrmRes)){
							$insHrmSql = array(
								"group_id" => $groupId,
								"website_id" => $order['website_id'],
								"product_id" => $order['product_id'],
								"plan_id" => $order['plan_id'],
								"prd_plan_type_id" => $order['planType'],
								"payer_id" => $payer_id,
								"payer_type" => "Customer",
								"hrm_unit_price" => $hrmAmount,
								"list_bill_id" => $order['list_bill_id'],
								"list_bill_detail_id" => $order['list_bill_detail_id'],
								"amount" => $hrmAmount,
								"order_id" => $order['id'],
								"order_detail_id" => $order['order_detail_id'],
								"transaction_id" => $order['transaction_tbl_id'],
								"status" => $order['is_compliant'] == 'N' ? 'NonCompliant' : ($flag == 'Payment Approved' ? 'Completed' : 'Pending'),
								"pay_date" => $order['payDate'],
								"created_at" => "msqlfunc_NOW()",
							);

							$insHrmSql["sub_type"] = $order['is_renewal'] == "Y" ? 'Renewals' : 'New';
							$insHrmSql["balance_type"] = "addCredit";
							$insHrmSql["pay_period"] = $weeklyPayPeriod;
							$insHrmSql["hrm_payment_duration"] = "weekly";
							$weeklyHRMPaymentId = $pdo->insert("hrm_payment", $insHrmSql);
							$hrmObj->memberHRMPayment("addCredit", "weekly", $groupId, $payer_id, $weeklyPayPeriod, $hrmAmount, $weeklyHRMPaymentId,$order['payDate'],$flag);
						}
					}
				}
			}
		}
		if(!empty($orderIds) && $flag == 'open'){
			$orderSql = "SELECT lb.id,ws.customer_id,ws.plan_id,
		 			lbd.product_id,lbd.ws_id as website_id,c.is_compliant,
		 			c.sponsor_id,p.is_gap_plus_product AS gap,p.annual_hrm_payment,
		 			s.type AS sponsor_type,GROUP_CONCAT(DISTINCT(gcp.paydate)) AS payDate,
		 			p.name,p.product_code,lbd.prd_plan_type_id AS planType,gc.pay_period,cs.class_id,lb.id as list_bill_id,IF(gcp.paydate >= lbd.start_coverage_date AND gcp.paydate <=lbd.end_coverage_date,lbd.id,0) AS list_bill_detail_id
		 			FROM list_bills lb
		 			JOIN list_bill_details lbd ON (lbd.list_bill_id=lb.id)
		 			JOIN website_subscriptions ws ON (ws.id=lbd.ws_id)
		 			JOIN customer c ON (c.id=ws.customer_id)
		 			JOIN customer s ON (s.id=c.sponsor_id)
		 			JOIN prd_main p ON (p.id=lbd.product_id AND p.is_deleted='N' AND p.type!='Fees')
		 			JOIN customer_settings cs ON(cs.customer_id = c.id)
		 			JOIN group_classes gc ON(gc.id=cs.class_id AND gc.is_deleted='N')
		 			JOIN group_classes_paydates gcp ON(gcp.class_id = gc.id AND gcp.is_deleted='N')
		 			WHERE lb.is_deleted='N' AND lb.id IN('" . $orderIds . "') AND c.type IN ('Customer','Group')
		 			AND gcp.paydate IN("."'".implode("','", $payDateArr)."'".")
		 			AND p.is_gap_plus_product = 'Y' GROUP BY lbd.id,gcp.paydate,lbd.product_id,c.id HAVING list_bill_detail_id>0";
		 	$orderRes = $pdo->select($orderSql);
			if (!empty($orderRes)) {
				foreach ($orderRes as $order) {
					$weeklyPayPeriod = $hrmObj->getWeeklyPayPeriod($order['payDate']);
					$planType = $order['planType'];
					$order["transaction_tbl_id"] = 0;
					if (!empty($order['annual_hrm_payment'])) {
						$amount = json_decode($order['annual_hrm_payment']);
						$hrmAmount = $amount->$planType;
						if($order['pay_period'] == "Monthly"){
							$hrmAmount = round(($hrmAmount / 12),2);
						} else if($order['pay_period'] == "Semi-Monthly"){
							$hrmAmount = round(($hrmAmount / 24),2);
						} else if($order['pay_period'] == "Weekly"){
							$hrmAmount = round(($hrmAmount / 52),2);
						} else if($order['pay_period'] == "Bi-Weekly"){
							$hrmAmount = round(($hrmAmount / 26),2);
						}
						$groupId = $order['sponsor_id'];
						$payer_id = $order['customer_id'];

						$hrmSql = "SELECT id FROM hrm_payment WHERE group_id=:group_id AND pay_period=:pay_period AND website_id=:website_id AND payer_id=:payer_id AND is_deleted='N'";
						$params = array(":group_id" => $groupId, ":pay_period" => $weeklyPayPeriod, ":website_id" => $order['website_id'],":payer_id"=>$payer_id);
						$hrmRes = $pdo->selectOne($hrmSql, $params);
						if(empty($hrmRes)){
							$insHrmSql = array(
								"group_id" => $groupId,
								"website_id" => $order['website_id'],
								"product_id" => $order['product_id'],
								"plan_id" => $order['plan_id'],
								"prd_plan_type_id" => $order['planType'],
								"payer_id" => $payer_id,
								"payer_type" => "Customer",
								"hrm_unit_price" => $hrmAmount,
								"amount" => $hrmAmount,
								"list_bill_id" => $order['list_bill_id'],
								"list_bill_detail_id" => $order['list_bill_detail_id'],
								"order_id" => '',
								"order_detail_id" => '',
								"transaction_id" => '',
								"status" => $order['is_compliant'] == 'N' ? 'NonCompliant' : 'Pending',
								"pay_date" => $order['payDate'],
								"created_at" => "msqlfunc_NOW()",
							);
							$insHrmSql["sub_type"] = 'New';
							$insHrmSql["balance_type"] = "addCredit";
							$insHrmSql["pay_period"] = $weeklyPayPeriod;
							$insHrmSql["hrm_payment_duration"] = "weekly";
							$weeklyHRMPaymentId = $pdo->insert("hrm_payment", $insHrmSql);
							$hrmObj->memberHRMPayment("addCredit", "weekly", $groupId, $payer_id, $weeklyPayPeriod, $hrmAmount, $weeklyHRMPaymentId,$order['payDate'],$flag);
						}
					}
				}
			}
		}
		return $resPayDateArr;
	}

	function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        if(($d && $d->format($format) === $date)) {
           return $date;
	    }else{
			$date = new DateTime($date);
            $date->modify('last day of previous month');
            return $date->format('Y-m-d');
	    }
    }
	
	//Start check the Holiday date list
	public function checkHoliday($date=""){
		$check_date = 'true';
		$nonWorkingDays = array();
		$year = date('Y', strtotime($date));

        $date1 = date('Y-m-d', strtotime("january $year third monday"));  //Martin Luther King, Jr. Day
        $date2 = date('Y-m-d', strtotime("february $year third monday")); //presidents day
        $date3 = date('Y-m-d', strtotime("last monday of may $year")); // memorial day
        $date4 = date('Y-m-d', strtotime("september $year first monday"));  //labor day
        $date5 = date('Y-m-d', strtotime("october $year second monday")); // Columbus Day
        $date6 = date('Y-m-d',strtotime("november $year fourth thursday")); // thanks giving
		
        array_push($nonWorkingDays, $date1, $date2, $date3, $date4, $date5, $date6);
        
		if((date('l', strtotime($date)) == 'Saturday') || date('l', strtotime($date)) == 'Sunday'){
			$check_date = 'false';
		}else{
		  $receivedDate = date('d M', strtotime($date));
		  $holiday = array(
			'01 Jan' => 'New Year Day',
			'19 Jun' => 'Juneteenth',
			'04 Jul' => 'Independence Day',
			'11 Nov' => 'Veterans Day',
			'25 Dec' => 'Christmas Day'
		  );
	  
		  foreach($holiday as $key => $value){
			if($receivedDate == $key){
				$check_date = 'false';
			}
		  }
          
		  foreach($nonWorkingDays as $val){
			if($date == $val){
				$check_date = 'false';
			}
		  }
		}
		
		if($check_date == 'false'){
			$date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
			return $this->checkHoliday($date);
		}else{
            return $date;
		}
	}
	//end check the Holiday date list

	public function generate_ach_betch_file($rows,$pay_date)
	{
		global $pdo;
		//pre_print($rows);
		$checkBatch = "SELECT count(id)as total from nacha_file_export ";
		$checkBatchRow = $pdo->selectOne($checkBatch);
		$totalBatch = $checkBatchRow['total'] + 150;

		$content = "";
		if (count($rows) > 0) {
				/* ----------- USA FILE FORMAT START --------------- */
				// TRANSMISSION DATE 24:29
				$transdate = date('ymd');

				// TRANSMISSION TIME 30:33
				$transtime = date('Hi');

				//IMMEDIATE ORIGIN  (41:50)
				//$company_id = "609089829"; // LIVE
				$company_id = "7825371810"; // LIVE

				// IMMEDIATE DESTINATION (4:13)
				$immediate_destination = '071921891';

				//IMMEDIATE DESTINATION NAME
				$immediate_destination_name='PNC BANK';
				$disp_destination_name = $immediate_destination_name . str_repeat(" ", (23 - strlen($immediate_destination_name)));

				// IMMEDIATE ORIGIN NAME (64:86)
				$company_name = "TRIADA HEALTH";
				$disp_company_name = $company_name . str_repeat(" ", (23 - strlen($company_name)));


				// FILE HEADER RECORD (1)	
				$content .= "101 " . $immediate_destination . $company_id . $transdate . $transtime . "A094101".$disp_destination_name. $disp_company_name . "        \r\n";

				// COMPANY ENTRY DESCRIPTION (54:63)
				$comp_entry_desc = "CLAIM PYMT";

				// COMPANY DESCRIPTIVE DATE 64:69
				$comp_dist_date = get_working_buisness_day();

				// EFFECTIVE ENTRY DATE 64:69
				$elect_entry_date=date('ymd', strtotime($pay_date ."-1 day"));
				// $elect_entry_date = get_working_buisness_day();

				// ORIGINATING DFI ID (80:87)
				$orginate_dfi_id = "07192189";

				// COMPANY NAME * (5:20)
				$disp_company_name = $company_name . str_repeat(" ", (16 - strlen($company_name)));
				$batchNumber = sprintf('%07d', $totalBatch);

				//BATCH HEADER RECORD (5)
				$content .= "5220" . $disp_company_name ."                    ". $company_id . "PPD" . $comp_entry_desc . $transdate .$elect_entry_date ."   " . "1".$orginate_dfi_id . "0000001" . "\r\n";

				$i = 1;
				$total = 0;
				$entry_hash = 0;
				$total_amount = 0;
				foreach ($rows as $value) {
					$incrment_id = sprintf('%06d', $i);

					//$bank_number = $value['routing_number'];
					$bank_number = sprintf('%09d', $value['routing_number']);
					$entry_hash = $entry_hash + substr($bank_number, 0, 8);

					// DFI ACCOUNT NUMBER (13:29)
					$account_number = $value['account_number'];
					$dist_account_number = $account_number . str_repeat(" ", (17 - strlen($account_number)));
					$acc_code = '22';
					if($value['account_type'] == 'savings'){
						$acc_code = '32';
					}

					// DOLLAR AMOUNT (30:39)
					$amount = str_replace('.', '', number_format($value['total_amount'], 2, ".", ""));
					$disp_amount = sprintf('%010d', $amount);
					$total_amount = $total_amount + $value['total_amount'];

					// INDIVIDUAL ID (40:54)
					$rep_id = $value['rep_id'];
					$disp_rep_id = $rep_id . str_repeat(" ", (15 - strlen($rep_id)));

					// INDIVIDUAL NAME (55:76)
					$account_name = html_entity_decode($value['account_name']);
					$account_name = preg_replace('/[^A-Za-z0-9 ,]/', '', $account_name);

					$account_name = strlen($account_name) > 22 ? substr($account_name, 0, 22) : $account_name;
					$disp_account_name = $account_name . str_repeat(" ", (22 - strlen($account_name)));

					// TRACE NUMBER  
					$trace_number = "071921890" . $incrment_id;
					$disp_trace_number = $trace_number . str_repeat(" ", (15 - strlen($trace_number)));

					$order_display_id = sprintf('%08d', $value['order_display_id']);;

					$content .= "6". $acc_code . $bank_number . $dist_account_number . $disp_amount . "B" .$order_display_id ."      ". $disp_account_name . "  " ."0".$disp_trace_number . "\r\n";
					$i++;
					$total++;
				}

				// BATCH CONTROL RECORD
				$dist_total_rec = sprintf('%06d', $total);

				// TOTAL CREDIT AMOUNT
				$disp_total_amount = sprintf('%012d', str_replace('.', '', number_format($total_amount, 2, ".", "")));

				// ENTITY HASH
	
				$disp_entity_hash = sprintf('%010d', $entry_hash);

				//RESERVED
				$reserved_space = str_repeat(" ", 25);
				$batch_count = sprintf('%07d', 1);

				$content .= "8220" . $dist_total_rec . $disp_entity_hash . sprintf('%012d', 0) . $disp_total_amount . $company_id . $reserved_space . $orginate_dfi_id . $batch_count . "\r\n";

				// FILE CONTROL RECORD (9)
				//BATCH COUNT
				

				//BLOCK COUNT
				
				// $content .= str_repeat("9", $total + 4);
				$total_lines = 2 + $total + 2 + 1;
				$temp_total_lines = 2 + $total + 2 + 1;

				for ($i=0; $i <= 10; $i++) { 
					$last_digit = substr($temp_total_lines, -1);
					if($last_digit != 1){
						$temp_total_lines = $temp_total_lines + 1;
					}else{
						break;
					}    
				}

				$repeatation = $temp_total_lines - $total_lines;


				$block_count = sprintf('%06d', (($temp_total_lines - 1) / 10));

				// ENTRY/ ADDENDA COUNT (14:21)
				$dist_total_rec = sprintf('%08d', $total);

				//RESERVED
				$reserved_space = str_repeat(" ", 39);
				
				$batch_count = sprintf('%06d', 1);

				$content .= "9" . $batch_count . $block_count . $dist_total_rec . $disp_entity_hash . sprintf('%012d', 0) . $disp_total_amount . $reserved_space."\r\n";

				// if($repeatation > 0){
				// 	for ($i=0; $i<=$repeatation ; $i++) {
				// 		if($i < $repeatation){
				// 			$content .= str_repeat("9", 94) . "\r\n";        
				// 		}else{
				// 			$content .= str_repeat("9", 94);
				// 		} 
				// 	}
				// }else{
				// 	$content .= str_repeat("9", 94);
				// }
				/* ----------- USA FILE FORMAT END --------------- */
		} 
		return $content;
	}

	public function generate_control_total_file($rows,$pay_date)
	{
		global $pdo;
		$control_content = "";
		if (count($rows) > 0) {
				/* ----------- USA FILE FORMAT START --------------- */
				$customer_number = "0000773238"; // LIVE

				$job_name = "PEPPCG14";

				$total = 0;
				$credit_amount = 0;
				$debit_amount = 0;
				foreach ($rows as $value) {
					$credit_number = $value['credit_number'];
					$disp_credit_number = sprintf('%08d', $value['credit_number']);

					// DOLLAR AMOUNT
					$cred_amount = str_replace('.', '', number_format($value['credit_amount'], 2, ".", ""));
					$disp_credit_amount = sprintf('%012d', $cred_amount);
					$credit_amount = $credit_amount + $value['credit_amount'];

					$debit_number = $value['debit_number'];
					$disp_debit_number = sprintf('%08d', $value['debit_number']);

					// DOLLAR AMOUNT
					$deb_amount = str_replace('.', '', number_format($value['debit_amount'], 2, ".", ""));
					$disp_debit_amount = sprintf('%012d', $deb_amount);
					$debit_amount = $debit_amount + $value['debit_amount'];

					$items_count = $credit_number + $debit_number;
					$disp_item_count = sprintf('%08d', $items_count);

					$control_content .= $customer_number . $disp_credit_number . $disp_credit_amount . $disp_debit_number .$disp_debit_amount. $disp_item_count . $job_name . "\r\n";
				}
				/* ----------- USA FILE FORMAT END --------------- */
		} 
		return $control_content;
	}

	/**
	 * @param $hrmGenerateDate as date of 2 day before paydate
	 * Get Next 10 paydate from database
	 * check for every paydate,
	 * if all paydate has same generate day then generate hrm payment for that paydate
	 */
	public function getPayDateForHrmPayment($hrmGenerateDate, $groupId = ''){
		global $pdo;

		$incr = '';
		$currentDate = date('Y-m-d',strtotime($hrmGenerateDate));
		$nextTenDate = date('Y-m-d',strtotime($hrmGenerateDate.'+10 day'));
		$schParam = [
			":currentDate" => $currentDate,
			":nextTenDate" => $nextTenDate,
		];
		if(!empty($groupId)){
			$incr = ' AND group_id=:group_id';
			$schParam[':group_id'] = getname('customer',$groupId,'id','rep_id');
		}
		$paydateSql = "SELECT DISTINCT paydate as paydate FROM group_classes_paydates WHERE is_deleted='N' $incr AND DATE(paydate) >= :currentDate AND DATE(paydate) <= :nextTenDate ORDER BY paydate ASC";
		$payDatesArr = $pdo->select($paydateSql,$schParam);
		$generateHrmPaydateArr = [];
		if(!empty($payDatesArr)){
			foreach($payDatesArr as $dateArr){
				$businessDay = $this->getWorkingBuisnessDay($dateArr['paydate']);
				if($businessDay == date('Y-m-d',strtotime($hrmGenerateDate))){
					$generateHrmPaydateArr[] = $dateArr['paydate'];
				}
			}
		}
		return $generateHrmPaydateArr;
	}

	public function getWorkingBuisnessDay($cDate = '')
	{
		$date = $this->getDateWithoutFestiveHoliday($cDate);
		$date = date('Y-m-d', strtotime($date . '-2 day')); // 04/07,06/17
		$date = $this->getDateWithoutHoliday($date); // 04/07
		return $date;
	}

	public function getDateWithoutHoliday($date = ''){
		$days = date('D', strtotime($date));
		$holidayArr = fetch_public_holidays($date);
		if((!in_array($days,array('Sat','Sun'))) && (in_array($date,$holidayArr))){
			$date = date('Y-m-d', strtotime($date . '-1 day'));
		}
		$day = date('D', strtotime($date)); 
		if ($day == 'Sat') {
			$date = date('Y-m-d', strtotime($date . '-2 day'));
			$date = $this->getDateWithoutHoliday($date);
		} else if ($day == 'Sun') {
			$date = date('Y-m-d', strtotime($date . '-2 day'));
			$date = $this->getDateWithoutHoliday($date);
		}
		return $date;
	}

	public function getDateWithoutFestiveHoliday($date = ''){
		$days = date('D', strtotime($date));
		$holidayArr = fetch_public_holidays($date);
		if((!in_array($days,array('Sat','Sun'))) && (in_array($date,$holidayArr))){
			$date = date('Y-m-d', strtotime($date . '-1 day'));
		}
		$previousDate = date('Y-m-d', strtotime($date . '-1 day'));
		$holidayArrs = fetch_public_holidays($previousDate);
		if(!empty($holidayArrs) && in_array($previousDate,$holidayArrs)){
			$date = date('Y-m-d', strtotime($date . '-1 day'));
		}
		$day = date('D', strtotime($date));
		if ($day == 'Sun') {
			$date = date('Y-m-d', strtotime($date . '-1 day'));
			$date = $this->getDateWithoutFestiveHoliday($date);
		}
		return $date;
	}

	public function getPayPeriods($payPeriod,$payDate)
	{
		$startPayPeriod = date('Y-m-d', strtotime('-6 days', strtotime($payPeriod)));;
		$endPayPeriod = date('Y-m-d', strtotime($payPeriod));
		if (($startPayPeriod <= $payDate) && ($endPayPeriod >= $payDate)) {
			$startPayPeriod = date('m/d/Y', strtotime('-6 days', strtotime($payPeriod)));;
			$endPayPeriod = date('m/d/Y', strtotime($payPeriod));
		} else {
			$dayNumber = date("w", strtotime($payDate));
			if ($dayNumber == "1") {
				$startPayPeriod = date('m/d/Y', strtotime($payDate));
				$endPayPeriod = date('m/d/Y', strtotime('+6 days', strtotime($payDate)));
			} else {
				$startPayPeriod = date("m/d/Y", strtotime("previous monday", strtotime($payDate)));
				$endPayPeriod = date('m/d/Y', strtotime('+6 days', strtotime($startPayPeriod)));
			}
		}
		$currentPayPeriod = $startPayPeriod .' - '. $endPayPeriod;
		return $currentPayPeriod;
	}

	/**
	 * Generate NACHA File
	 * @param $groupId
	 * @param $pay_date
	 * @param $hrm_payment_duration 
	 */
	public function generateNachaFile($groupId,$pay_date,$hrm_payment_duration = 'weekly', $memberIds = '',$debug = false){
		global $UPLOAD_DIR;
		require_once dirname(__DIR__) . '/libs/php_sftp_libs/Net/SFTP.php';
		set_include_path(dirname(__DIR__) . '/libs/php_sftp_libs/');

        global $CREDIT_CARD_ENC_KEY, $S3_REGION, $S3_KEY, $S3_SECRET, $S3_BUCKET_NAME, $NACHA_FILES_PATH, $NACHA_FILES_SFTP_PATH, $ADMIN_HOST, $pdo, $SITE_ENV;
        $response = array();
        $response['status'] = 'fail';
		
		$incr = '';
		if(!empty($memberIds)){
			$incr .= " AND hrmp.payer_id IN (" . $memberIds . ") ";
		}
        //Generate NACHA File Start
            $selHRMPayment = "SELECT hrmp.id,hrmp.amount as total_amount,CONCAT(a.fname,' ',a.lname) AS account_name,a.rep_id,hrmp.pay_date,a.sponsor_id AS groupId,
            AES_DECRYPT(cbp.ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as account_number,
            AES_DECRYPT(cbp.ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as routing_number,
            cbp.ach_account_type as account_type,o.display_id as order_display_id,a.id as customer_id,
			IF(hrmp.balance_type='addCredit',COUNT(hrmp.balance_type),0) AS credit_number,
			IF(hrmp.balance_type='addCredit',SUM(hrmp.amount),0) AS credit_amount,
			IF(hrmp.balance_type='revCredit',COUNT(hrmp.balance_type),0) AS debit_number,
			IF(hrmp.balance_type='revCredit',SUM(hrmp.amount),0) AS debit_amount
            FROM hrm_payment hrmp
            JOIN customer a ON(hrmp.payer_id=a.id AND a.type='Customer')
            JOIN orders o ON (hrmp.order_id=o.id)
            JOIN customer_billing_profile cbp ON (a.id=cbp.customer_id AND cbp.is_direct_deposit_account='Y')
            WHERE hrmp.is_deleted='N' AND hrmp.status='Completed' AND hrmp.group_id=:group_id  AND hrmp.pay_date=:pay_date AND hrmp.hrm_payment_duration = :hrm_payment_duration AND a.id NOT IN(SELECT customer_id FROM nacha_file_members WHERE is_deleted='N' AND group_id=:group_id AND hrm_payment_duration = :hrm_payment_duration AND pay_date=:pay_date) ".$incr." GROUP BY hrmp.payer_id";
            $sch_params = array(
            ':pay_date' => $pay_date,
            ':hrm_payment_duration' => $hrm_payment_duration,
            ':group_id' => $groupId
            );
			
            $resHRMPaymentRecords = $pdo->select($selHRMPayment, $sch_params);
			if($debug){
				pre_print($selHRMPayment,false);
				pre_print($sch_params,false);
				pre_print($resHRMPaymentRecords,false);
			}
            $content = $this->generate_ach_betch_file($resHRMPaymentRecords,$pay_date);
			$control_content = $this->generate_control_total_file($resHRMPaymentRecords,$pay_date);
            if (!empty($content) && !empty($control_content)) {
                $file_name = str_replace('.', '', microtime(true)) . '_' . 'NACHA';
				$control_file_name = str_replace('.', '', microtime(true)). '_' . 'CONTROL_FILE';
				$file_type = 'txt';
				/*
				//Create Temp File FOR NACHA
					$tmpfname = tempnam($UPLOAD_DIR, 'NAC'.date('s'));
					//This removes the file
					$handle = fopen($tmpfname, "w");
					fwrite($handle, $content);
					fclose($handle);
				//Create Temp File FOR NACHA

				//Create Temp File FOR CONTROL
					$controltmpfname = tempnam($UPLOAD_DIR, 'NAC'.date('s'));
					//This removes the file
					$controlhandle = fopen($controltmpfname, "w");
					fwrite($controlhandle, $control_content);
					fclose($controlhandle);
				//Create Temp File FOR CONTROL
                //Upload file to SFTP
                    $ftp_server = "efx-uat.pnc.com";
                    $ftp_username = "trihealth0t";
                    $ftp_userpass = "c3!5aexe";

					$uploadFile = $file_name.'.'.$file_type;
					$controluploadFile = $control_file_name.'.'.$file_type;
					if($SITE_ENV != 'Local'){
	                    $sftp = new Net_SFTP($ftp_server);
	                    if ($sftp->login($ftp_username, $ftp_userpass)) {
	                      $sftp->put('/inbound/'.$uploadFile,$tmpfname,NET_SFTP_LOCAL_FILE);
						  $sftp->put('/inbound/'.$controluploadFile,$controltmpfname,NET_SFTP_LOCAL_FILE);
	                    }
                    }
                //Upload file to SFTP

				//remove temp file
				unlink($tmpfname);
				unlink($controltmpfname);
				*/
                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region'  => $S3_REGION,
                    'credentials' => array(
                        'key' => $S3_KEY,
                        'secret' => $S3_SECRET
                    )
                ]);

                $result = $s3Client->putObject(array(
                    'Bucket' => $S3_BUCKET_NAME,
                    'Key'    => $NACHA_FILES_PATH . $file_name . '.' . $file_type,
                    'Body' => $content,
                ));

				$controlresult = $s3Client->putObject(array(
                    'Bucket' => $S3_BUCKET_NAME,
                    'Key'    => $NACHA_FILES_PATH . $control_file_name . '.' . $file_type,
                    'Body' => $control_content,
                ));

                $result = $s3Client->putObject(array(
                    'Bucket' => $S3_BUCKET_NAME,
                    'Key'    => $NACHA_FILES_SFTP_PATH . $file_name . '.' . $file_type,
                    'Body' => $content,
                ));

				$controlresult = $s3Client->putObject(array(
                    'Bucket' => $S3_BUCKET_NAME,
                    'Key'    => $NACHA_FILES_SFTP_PATH . $control_file_name . '.' . $file_type,
                    'Body' => $control_content,
                ));

                $code = $result['@metadata']['statusCode'];
				$controlcode = $controlresult['@metadata']['statusCode'];

				$adminId = !empty($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0;
				if ($code === 200 && $controlcode === 200) {
                    $REAL_IP_ADDRESS = get_real_ipaddress();
                    $insSql = array(
                        'admin_id' => $adminId,
                        'group_id' => $groupId,
                        'pay_date' => $pay_date,
                        'file_name' => $file_name,
						'control_file_name' => $control_file_name,
                        'file_type' => $file_type,
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? makesafe($REAL_IP_ADDRESS['original_ip_address']) : makeSafe($REAL_IP_ADDRESS['ip_address']),
                    );
					$nachaBatchId = $pdo->insert("nacha_file_export", $insSql);

                    // Activity Feed Code Start
                    foreach ($resHRMPaymentRecords as $grpRes) {

						$insParam = [
							'nacha_batch_id' => $nachaBatchId,
							'group_id' => $groupId,
							'customer_id' => $grpRes['customer_id'],
							'pay_date' => $pay_date,
							'hrm_payment_duration' => $hrm_payment_duration,
						];
						$pdo->insert('nacha_file_members',$insParam);

                        $description['ac_message'] = array(
                            'ac_red_1' => array(
                                'href' => !empty($adminId) ? $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']) : "System",
                                'title' => !empty($adminId) ? $_SESSION['admin']['display_id'] : 'System',
                            ),
                            'ac_message_1' => ' Generated NACHA File for ',
                            'ac_red_2' => array(
                                'href' => $ADMIN_HOST . '/groups_details.php.php?id=' . md5($grpRes['groupId']),
                                'title' => $grpRes['rep_id'],
                            ),
                        );
                        activity_feed(3, 5, 'System', $grpRes['groupId'], 'Group', "Generated NACHA File", (!empty($adminId) ? $_SESSION['admin']['fname'] : 'System'), !empty($adminId) ? $_SESSION['admin']['lname'] : 'System', json_encode($description));
                    }
                    // Activity Feed Code Ends  
                    $response['status'] = 'success';
                    $response['message'] = "NACHA File Generated successfully";
                } else {
                    $response['status'] = 'fail';
                    $response['message'] = "Something Went Wrong";
                }
            }
        //Generate NACHA File End
        return $response;
    }

	public function getWorkingPriorDay($paydate = '',$prior_day = '')
	{
		$holidayDates = fetch_public_holidays($paydate);
		$d = new DateTime($paydate);
        $t = $d->getTimestamp();
		for($i=0; $i<$prior_day; $i++){
			// add 1 day to timestamp
			$addDay = 86400;
			// get what day it is next day
			$nextDay = date('w', ($t-$addDay));
			// if it's Saturday or Sunday get $i-1
			if($nextDay == 0 || $nextDay == 6) {
				$i--;
			}elseif(in_array(date('Y-m-d',$t-$addDay),$holidayDates)){
				$i--;
			}
			// modify timestamp, add 1 day
			$t = $t-$addDay;
		}
		$d->setTimestamp($t);
		return $d->format('Y-m-d');
	}

	public function getWorkingnextDay($paydate = '',$next_day = '')
	{
		$holidayDates = fetch_public_holidays($paydate);
		$d = new DateTime($paydate);
        $t = $d->getTimestamp();
		for($i=0; $i<$next_day; $i++){
			// add 1 day to timestamp
			$addDay = 86400;
			// get what day it is next day
			$nextDay = date('w', ($t+$addDay));
			// if it's Saturday or Sunday get $i-1
			if($nextDay == 0 || $nextDay == 6) {
				$i--;
			}elseif(in_array(date('Y-m-d',$t+$addDay),$holidayDates)){
				$i--;
			}
			// modify timestamp, add 1 day
			$t = $t+$addDay;
		}
		$d->setTimestamp($t);
		return $d->format('Y-m-d');
	}

}
?>