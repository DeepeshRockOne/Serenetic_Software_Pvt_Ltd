<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$category_array = array(
	'AD&D' => 'AD&D Coverage',
	'Dental' => 'Dental Coverage',
	'Discount Services' => 'Discount Services',
	'Prescription Services' => 'Prescription Services',
	'Prescription Card' => 'Prescription Card',
	'Telemedicine' => 'Telemedicine',
	'Vision' => 'Vision Coverage',
);

$company_array = array(
    'NAPP'=>'NAPP'
);

$sql="SELECT c.company_name,c.site_url,c.short_name,pc.title as category_name,pc.short_description,pc.category_image,pc.status as category_status,pf.name as carrier_name,pf.display_id as carrier_code,pf.contact_fname,pf.contact_lname,pf.phone,pf.email,pf.status as carrier_status,pf.use_appointments,p.* 
		FROM prd_main p 
		JOIN prd_company c on (c.id=p.company_id)
		JOIN prd_category pc ON (pc.id = p.category_id)
		JOIN prd_fees pf ON (pf.id = p.carrier_id AND pf.setting_type='Carrier')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
		
		if(empty($resPrd)){
			$sqlCategory="SELECT id FROM prd_category WHERE is_deleted='N' AND title = :title";
      		$resCategory=$pdo->selectOne($sqlCategory,array(":title"=>$value['category_name']));
			  
      		$sqlCompany="SELECT id FROM prd_company WHERE is_deleted='N' AND company_name = :company_name";
      		$resCompany=$pdo->selectOne($sqlCompany,array(":company_name"=>$value['company_name']));
			
			if(!empty($value['carrier_name'])){
				$selectCarrier = "SELECT id FROM prd_fees WHERE setting_type='Carrier' AND name=:name AND is_deleted='N' ";
				$where = array(":name"=>$value['carrier_name']);
				$resultCarrier = $pdo->selectOne($selectCarrier,$where);
			}

			if(!empty($value['membership_ids'])){
				$selectMembership = "SELECT name FROM prd_fees WHERE setting_type='Membership' AND id IN(".$value['membership_ids'].") AND is_deleted='N' ";
				$resultMembership = $OtherPdo->select($selectMembership);
				$membership_ids = array();
				if(!empty($resultMembership)){
					foreach($resultMembership as $membership){
						$resMFees = $pdo->selectOne("SELECT id from prd_fees where name=:name and is_deleted='N'",array(":name"=>$membership['name']));
						if(!empty($resMFees['id'])){
							$membership_ids[] = $resMFees['id'];
						}
					}					
				}
			}
			  
      		if(!empty($resCategory['id']) && !empty($resCompany['id']) && !empty($resultCarrier['id'])){

      			$insParams=array(
					'category_id'=>$resCategory['id'],
					'company_id'=>$resCompany['id'],
					'prd_fee_id' => 0,
					'record_type' => $value['record_type'],
					'admin_id' => 1,
					'association_id' => 0,
					'carrier_id'=>$resultCarrier['id'],
					'main_product_type'=>$value['main_product_type'],
					'is_life_insurance_product'=>$value['is_life_insurance_product'],
					'life_term_type'=>$value['life_term_type'],
					'guarantee_issue_amount_type'=>$value['guarantee_issue_amount_type'],
					'primary_issue_amount'=>$value['primary_issue_amount'],
					'spouse_issue_amount'=>$value['spouse_issue_amount'],
					'child_issue_amount'=>$value['child_issue_amount'],
					'is_spouse_issue_amount_larger'=>$value['is_spouse_issue_amount_larger'],
					'membership_ids'=>!empty($membership_ids) ? implode(',',$membership_ids) : '',
					'parent_product_id' => 0,
					'product_code'=>$value['product_code'],
					'name'=>$value['name'],
					'variation_desc'=>$value['variation_desc'],
					'type'=>$value['type'],
					'product_type'=>$value['product_type'],
					'description' => $value['description'],
					'long_desc' => $value['long_desc'],
					'is_clone_long_desc' => $value['is_clone_long_desc'],
					'member_portal' => $value['member_portal'],
					'is_clone_member_portal' => $value['is_clone_member_portal'],
					'agent_portal' => $value['agent_portal'],
					'is_clone_agent_portal' => $value['is_clone_agent_portal'],
					'limitations_exclusions' => $value['limitations_exclusions'],
					'is_clone_limitations_exclusions' => $value['is_clone_limitations_exclusions'],
					'direct_product'=>$value['direct_product'],
					'effective_day'=>$value['effective_day'],
					'effective_day2' => $value['effective_day2'],
					'sold_day'=>$value['sold_day'],
					'group_product' => $value['group_product'],
					'group_effective_day' => $value['group_effective_day'],
					'group_sold_day' => $value['group_sold_day'],
					'is_association_require' => $value['is_association_require'],
					'association_ids' => $value['association_ids'],
					'is_assign_by_state' => $value['is_assign_by_state'],
					'is_state_restriction' => $value['is_state_restriction'],
					'is_carrier_state' => $value['is_carrier_state'],
					'carrier_state' => $value['carrier_state'],
					'house_state' => $value['house_state'],
					'member_move_policy_termed' => $value['member_move_policy_termed'],
					'is_zipcode_restricted' => $value['is_zipcode_restricted'],
					'is_specific_zipcode'=>$value['is_specific_zipcode'],
					'no_sale_state_coverage_continue'=>$value['no_sale_state_coverage_continue'],
					'restricted_zipcodes'=>$value['restricted_zipcodes'],
					'restricted_state_zipcode_list'=>$value['restricted_state_zipcode_list'],
					'coverage_options' => $value['coverage_options'],
					'family_plan_rule'=>$value['family_plan_rule'],
					'is_primary_age_restrictions'=>$value['is_primary_age_restrictions'],
					'primary_age_restrictions_from'=>$value['primary_age_restrictions_from'],
					'primary_age_restrictions_to'=>$value['primary_age_restrictions_to'],
					'is_children_age_restrictions'=>$value['is_children_age_restrictions'],
					'children_age_restrictions_from'=>$value['children_age_restrictions_from'],
					'children_age_restrictions_to'=>$value['children_age_restrictions_to'],
					'is_spouse_age_restrictions'=>$value['is_spouse_age_restrictions'],
					'spouse_age_restrictions_from'=>$value['spouse_age_restrictions_from'],
					'spouse_age_restrictions_to'=>$value['spouse_age_restrictions_to'],
					'maxAgeAutoTermed'=>$value['maxAgeAutoTermed'],
					'is_beneficiary_required' => $value['is_beneficiary_required'],
					'allowedBeyoundAge'=>$value['allowedBeyoundAge'],
					'child_product'=>$value['child_product'],
					'termination_rule'=>$value['termination_rule'],
					'term_back_to_effective'=>$value['term_back_to_effective'],
					'term_automatically'=>$value['term_automatically'],
					'term_automatically_within'=>$value['term_automatically_within'],
					'term_automatically_within_type'=>$value['term_automatically_within_type'],
					'reinstate_option'=>$value['reinstate_option'],
					'reinstate_within'=>$value['reinstate_within'],
					'reinstate_within_type'=>$value['reinstate_within_type'],
					'reenroll_options'=>$value['reenroll_options'],
					'reenroll_within'=>$value['reenroll_within'],
					'reenroll_within_type'=>$value['reenroll_within_type'],
					'member_details_asked' => $value['member_details_asked'],
					'member_details_required' => $value['member_details_required'],
					'spouse_details_asked' => $value['spouse_details_asked'],
					'spouse_details_required' => $value['spouse_details_required'],
					'dependent_details_asked' => $value['dependent_details_asked'],
					'dependent_details_required' => $value['dependent_details_required'],
					'enrollment_verification' => $value['enrollment_verification'],
					'verification_doc' => $value['verification_doc'],
					'is_eSignTermsCondition' => $value['is_eSignTermsCondition'],
					'eSignTermsCondition_doc' => $value['eSignTermsCondition_doc'],
					'eSignTermsCondition_desc' => $value['eSignTermsCondition_desc'],
					'eSignTermsCondition_desc' => $value['eSignTermsCondition_desc'],
					'is_agent_requirement' => $value['is_agent_requirement'],
					'agent_requirement' => $value['agent_requirement'],
					'is_license_require'=>$value['is_license_require'],
					'license_type'=>$value['license_type'],
					'license_rule'=>$value['license_rule'],
					'initial_purchase' => $value['initial_purchase'],
					'is_benefit_tier' => $value['is_benefit_tier'],
					'is_fee_on_renewal' => $value['is_fee_on_renewal'],
					'fee_renewal_type' => $value['fee_renewal_type'],
					'fee_renewal_count' => $value['fee_renewal_count'],
					'is_fee_on_commissionable' => $value['is_fee_on_commissionable'],
					'is_member_benefits' => $value['is_member_benefits'],
					'advance_month' => $value['advance_month'],
					'status' => $value['status'],
					'is_add_on_product' => $value['is_add_on_product'],
					'payment_type'=>$value['payment_type'],
					'pricing_model'=>$value['pricing_model'],
					'payment_type_subscription'=>$value['payment_type_subscription'],
					'dataStep' => $value['dataStep'],
					'order_by' => $value['order_by'],
					'is_writing_number' => $value['is_writing_number'],
					'is_writing_number_utilized' => $value['is_writing_number_utilized'],
					'licensed_in_specific_state' => $value['licensed_in_specific_state'],
					'member_payment' => $value['member_payment'],
					'member_payment_type' => $value['member_payment_type'],
					'custom_day' => $value['custom_day'],
					'is_enrollment_fee' => $value['is_enrollment_fee'],
					'enrollment_fee_ids' => $value['enrollment_fee_ids'],
					'is_admin_fee' => $value['is_admin_fee'],
					'admin_fee_ids' => $value['admin_fee_ids'],
					'is_service_fee' => $value['is_service_fee'],
					'service_fee_ids' => $value['service_fee_ids'],
					'is_product_commissionable' => $value['is_product_commissionable'],
					'is_commissionable_by_tier' => $value['is_commissionable_by_tier'],
					'is_fee_to_association' => $value['is_fee_to_association'],
					'is_association_fee_included' => $value['is_association_fee_included'],
					'opt_states_restricted' => $value['opt_states_restricted'],
					'restricted_products' => $value['restricted_products'],
					'required_product' => $value['required_product'],
					'auto_assign_product' => $value['auto_assign_product'],
					'auto_assign_no_delete_product' => $value['auto_assign_no_delete_product'],
					'packaged_product' => $value['packaged_product'],
					'is_age_restrictions' => $value['is_age_restrictions'],
					'from_age' => $value['from_age'],
					'age_to' => $value['age_to'],
					'child_from_age' => $value['child_from_age'],
					'child_age_to' => $value['child_age_to'],
					'opt_commission_prd' => $value['opt_commission_prd'],
					'opt_agent_licensed' => $value['opt_agent_licensed'],
					'opt_product_sell_type' => $value['opt_product_sell_type'],
					'opt_rate_engine' => $value['opt_rate_engine'],
					'pricing_effective_date' => $value['pricing_effective_date'],
					'pricing_termination_date' => $value['pricing_termination_date'],
					'initial_charge_type' => $value['initial_charge_type'],
					'allow_sell_to' => $value['allow_sell_to'],
					'is_allow_family_plan_with_single_dependent' => $value['is_allow_family_plan_with_single_dependent'],
					'is_allow_family_plan_with_two_dependent' => $value['is_allow_family_plan_with_two_dependent'],
				);
				$product_id=$pdo->insert('prd_main',$insParams);
      		}

		}
		
	}
}
echo "import_products->Completed";
dbConnectionClose();
exit;
?>
