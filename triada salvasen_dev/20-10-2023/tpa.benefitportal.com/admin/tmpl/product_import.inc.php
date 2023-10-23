<?php 
$PRODUCT_IMPORT_ARR = array(
	'Information' => array(
		array(
			'label' => 'Parent Product',
			'file_label' => 'PARENT_PRODUCT',
			'info' => 'Unique product ID if product is variation of global (Must be in system)',
			'field_name' => 'parent_product'
		),
		array(
			'label' => 'Product Name',
			'file_label' => 'PRODUCT_NAME',
			'info' => 'Display name of product',
			'field_name' => 'product_name'
		),
		array(
			'label' => 'Product ID',
			'file_label' => 'PRODUCT_ID',
			'info' => 'Unique identifier of product',
			'field_name' => 'product_code'
		),
		array(
			'label' => 'Application Method',
			'file_label' => 'ENROLLMENT_METHOD',
			'info' => 'Indicator on how this product may be enrolled (Direct Sale Product, Group Application, Admin Only Product, Add-On Only Product)',
			'field_name' => 'enrollment_method',
		),
		array(
			'label' => 'Company Offering Product',
			'file_label' => 'PRODUCT_COMPANY',
			'info' => 'Name of company offering product (Must be in system)',
			'field_name' => 'company_name',
		),
		array(
			'label' => 'Category',
			'file_label' => 'PRODUCT_CATEGORY',
			'info' => 'Name of class product falls underneath (Must be in system)',
			'field_name' => 'category_name',
		),
		array(
			'label' => 'Primary Carrier',
			'file_label' => 'PRODUCT_CARRIER',
			'info' => 'Primary Carrier of this product (Must be in system)',
			'field_name' => 'carrier_name',
		),
		array(
			'label' => 'Product Type',
			'file_label' => 'PRODUCT_TYPE',
			'info' => 'Identifier if product can be combined with other core products (Core or Ancillary)',
			'field_name' => 'product_type',
		),
		array(
			'label' => 'Life Product',
			'file_label' => 'LIFE_PRODUCT',
			'info' => 'Identifier if product is a life insurance product (Yes or No)',
			'field_name' => 'life_product',
		),
		array(
			'label' => 'Life Term',
			'file_label' => 'LIFE_TERM',
			'info' => 'Identifier if life insurance product is level term or annual term',
			'field_name' => 'life_term',
		),
		array(
			'label' => 'GI - Primary',
			'file_label' => 'GUARANTEE_ISSUE_PRIMARY',
			'info' => 'Guarantee issue, whole amount primary enrollee can select on Application without additional underwriting ($45,000)',
			'field_name' => 'primary_guarantee_issue',
		),
		array(
			'label' => 'GI - Spouse',
			'file_label' => 'GUARANTEE_ISSUE_SPOUSE',
			'info' => 'Guarantee issue, whole amount spouse enrollee can select on Application without additional underwriting ($45,000)',
			'field_name' => 'spouse_guarantee_issue',
		),
		array(
			'label' => 'GI - Child',
			'file_label' => 'GUARANTEE_ISSUE_CHILD',
			'info' => 'Guarantee issue, whole amount child enrollee can select on Application without additional underwriting ($45,000)',
			'field_name' => 'child_guarantee_issue',
		),
		array(
			'label' => 'Spouse Benefit',
			'file_label' => 'SPOUSE_GREATER_PRIMARY',
			'info' => 'Indicator if spouse benefit amount can be larger than primary enrollee (Yes or No)',
			'field_name' => 'spouse_benefit',
		),
		array(
			'label' => 'Group Code',
			'file_label' => 'GROUP_CODE',
			'info' => 'Product specific data field storage',
			'field_name' => 'group_code',
		),
		array(
			'label' => 'Plan Code 1',
			'file_label' => 'PLAN_CODE1',
			'info' => 'Product specific data field storage',
			'field_name' => 'plan_code_1',
		),
		array(
			'label' => 'Plan Code 2',
			'file_label' => 'PLAN_CODE2',
			'info' => 'Product specific data field storage',
			'field_name' => 'plan_code_2',
		),
		array(
			'label' => 'Plan Code 3',
			'file_label' => 'PLAN_CODE3',
			'info' => 'Product specific data field storage',
			'field_name' => 'plan_code_3',
		),
		array(
			'label' => 'Plan Code 4',
			'file_label' => 'PLAN_CODE4',
			'info' => 'Product specific data field storage',
			'field_name' => 'plan_code_4',
		),
	),
	'Rules' => array(
		array(
			'label' => 'Effective Date',
			'file_label' => 'EFFECTIVE_DATE',
			'info' => 'Indicator of first available effective date for enrollees (Next Day, First of Following Month, Select Day of Month)',
			'field_name' => 'effective_date'
		),
		array(
			'label' => 'Effective Day',
			'file_label' => 'EFFECTIVE_DAY',
			'info' => 'Effective day of month if effective date was set to "Select Day of Month"',
			'field_name' => 'effective_day'
		),
		array(
			'label' => 'Sold Until',
			'file_label' => 'SOLD_UNTIL',
			'info' => 'Final Application day of previous month to get this effective date (1-29 or Last Day)',
			'field_name' => 'sold_until'
		),
		array(
			'label' => 'Membership',
			'file_label' => 'MEMBERSHIP',
			'info' => 'Identifier of membership required for this product (Membership ID and must be in system)',
			'field_name' => 'membership'
		),
		array(
			'label' => 'No Sale States',
			'file_label' => 'NO_SALE_STATES',
			'info' => 'States where this product is not available for Applications (Two-digit abbreviations or full name and separate multiple states by comma)',
			'field_name' => 'no_sale_state'
		),
		array(
			'label' => 'No Sale Effective Date',
			'file_label' => 'NO_SALE_EFFECTIVE',
			'info' => 'Date state is not available for Applications (Must be future date)',
			'field_name' => 'no_sale_effective_date'
		),
		array(
			'label' => 'Specific Zip Codes',
			'file_label' => 'ZIP_CODE',
			'info' => 'Put “No” in each record for this column',
			'field_name' => 'specific_zip_codes'
		),
		array(
			'label' => 'Continue Plan',
			'file_label' => 'MOVE_COVERAGE_CONTINUE',
			'info' => 'Identifier if Plan should continue if member moves to no-sale state (Yes or No)',
			'field_name' => 'continue_coverage'
		),
		array(
			'label' => 'Plan Options',
			'file_label' => 'COVERAGE_OPTIONS',
			'info' => 'Plan tiers available for product',
			'field_name' => 'coverage_options'
		),
		array(
			'label' => 'Family Requirement',
			'file_label' => 'FAMILY_REQUIREMENT',
			'info' => 'Identifier of dependent assignments to meet family Plan option (Family requires one spouse and child, Family requires minimum of one dependent, Family requires minimum of two dependents)',
			'field_name' => 'family_requirement'
		),
		array(
			'label' => 'SubProducts',
			'file_label' => 'SUB_PRODUCTS',
			'info' => 'Unique identifiers of subproducts attached to main product (Must be in system)',
			'field_name' => 'sub_products'
		),
		array(
			'label' => 'Combo: Auto-Assign',
			'file_label' => 'COMBO_AUTO_ASSIGN',
			'info' => 'Unique identifiers of products that should be auto-assigned if product elected on Application (Product must be in system)',
			'field_name' => 'combo_auto_assign'
		),
		array(
			'label' => 'Combo: Packaged',
			'file_label' => 'COMBO_PKG',
			'info' => 'Unique identifiers of products where at least one (1) of these products must be active or selected to enroll in this product (Product must be in system)',
			'field_name' => 'combo_packaged'
		),
		array(
			'label' => 'Combo: Excludes',
			'file_label' => 'COMBO_EXCLUDES',
			'info' => 'Unique identifiers of products that are not allowed to be combined with this product (Product must be in system)',
			'field_name' => 'combo_excludes'
		),
		array(
			'label' => 'Combo: Required',
			'file_label' => 'COMBO_REQUIRED',
			'info' => 'Unique identifiers of products that are required to be combined with this product (Product must be in system)',
			'field_name' => 'combo_required'
		),
		array(
			'label' => 'Term to Effective',
			'file_label' => 'TERM_BACK_EFFECTIVE',
			'info' => 'Indicator if product can be termed back to effective to eliminate Plan from ever being active (Yes or No)',
			'field_name' => 'term_to_effective'
		),
		array(
			'label' => 'Auto-Term',
			'file_label' => 'AUTO_TERM',
			'info' => 'Indicator if product terms automatically after set period of time (Yes or No)',
			'field_name' => 'auto_term'
		),
		array(
			'label' => 'Auto-Term Time',
			'file_label' => 'AUTO_TERM_TIME',
			'info' => 'Indicator of time after effective date termination date should be set (1-365 and Days, Weeks, Months, Years, Plan Periods)',
			'field_name' => 'auto_term_time'
		),
		array(
			'label' => 'Reinstate Option',
			'file_label' => 'REINSTATE_OPTION',
			'info' => 'Indicator on restrictions to reinstate Plan after termination date (Not Available, Available without restrictions, Available within specific time frame)',
			'field_name' => 'reinstate_option'
		),
		array(
			'label' => 'Reinstate Timeframe',
			'file_label' => 'REINSTATE_TIMEFRAME',
			'info' => 'Indicator of timeframe if “available within specific time frame” option selected (1-24 and Days, Weeks, Months, Years)',
			'field_name' => 'reinstate_timeframe'
		),
		array(
			'label' => 'Reenroll Option',
			'file_label' => 'REENROLL_OPTION',
			'info' => 'Indicator on restrictions to reenroll a Plan in same product after termination date (Not available, Available without restrictions, Available after specific time frame)',
			'field_name' => 'reenroll_option'
		),
		array(
			'label' => 'Reenroll Timeframe',
			'file_label' => 'REENROLL_TIMEFRAME',
			'info' => 'Indicator of timeframe if “available after specific time frame” option selected (1-24 and Days, Weeks, Months, Years)',
			'field_name' => 'reenroll_timeframe'
		),
	),
	'Enrollment' => array(
		array(
			'label' => 'Primary Age',
			'file_label' => 'PRIMARY_AGE',
			'info' => 'Indicator of age range if primary enrollee has age restrictions: From Age - To Age (0-120)',
			'field_name' => 'primary_age'
		),
		array(
			'label' => 'Primary Auto Term',
			'file_label' => 'PRIMARY_AUTO_TERM',
			'info' => 'Indicator if upon reaching maximum age is primary enrollee automatically terminated at the end of current Plan period (Yes or No)',
			'field_name' => 'primary_auto_term'
		),
		array(
			'label' => 'Spouse Age',
			'file_label' => 'SPOUSE_AGE',
			'info' => 'Indicator of age range if spouse enrollee has age restrictions: From Age - To Age (0-120)',
			'field_name' => 'spouse_age'
		),
		array(
			'label' => 'Spouse Auto Term',
			'file_label' => 'SPOUSE_AUTO_TERM',
			'info' => 'Indicator if upon reaching maximum age is spouse enrollee automatically terminated at the end of current Plan period (Yes or No)',
			'field_name' => 'spouse_auto_term'
		),
		array(
			'label' => 'Child Age',
			'file_label' => 'CHILD_AGE',
			'info' => 'Indicator of age range if child enrollee has age restrictions: From Age - To Age (0-120)',
			'field_name' => 'child_age'
		),
		array(
			'label' => 'Child Auto Term',
			'file_label' => 'CHILD_AUTO_TERM',
			'info' => 'Indicator if upon reaching maximum age is child enrollee automatically terminated at the end of current Plan period (Yes or No)',
			'field_name' => 'child_auto_term'
		),
		array(
			'label' => 'Disability',
			'file_label' => 'DISABILITY_ALLOWANCE',
			'info' => 'Indicator if dependent with documented disability is allowed to go beyond age of restriction (Yes or No)',
			'field_name' => 'disability'
		),
		array(
			'label' => 'Disability Enrollee',
			'file_label' => 'DISABILITY_ALLOWANCE_ENROLLEE',
			'info' => 'Identifier of dependent type if disability beyond age of restriction is allowed (Spouse and/or Child - separate multiple by comma)',
			'field_name' => 'disability_enrollee'
		),
		array(
			'label' => 'SSN Rquired',
			'file_label' => 'SSN_REQUIRED',
			'info' => 'Identifier if Social Security Number of enrollee is required (Primary, Spouse, Child - separate multiple by comma)',
			'field_name' => 'ssn_required'
		),
		array(
			'label' => 'Primary Enrollee - Required',
			'file_label' => 'MEMBER_DETAILS_ASKED',
			'info' => 'Criteria required by primary member on Application (Separate multiple by comma)',
			'field_name' => 'primary_enrollee_required'
		),
		array(
			'label' => 'Spouse Enrollee - Required',
			'file_label' => 'SPOUSE_DETAILS_ASKED',
			'info' => 'Criteria required by spouse dependent on Application (Separate multiple by comma)',
			'field_name' => 'spouse_enrollee_required'
		),
		array(
			'label' => 'Child Enrollee - Required',
			'file_label' => 'CHILD_DETAILS_ASKED',
			'info' => 'Criteria required by child dependent on Application (Separate multiple by comma)',
			'field_name' => 'child_enrollee_required'
		),
		array(
			'label' => 'Beneficiary Required',
			'file_label' => 'BENEFICIARY_REQUIRED',
			'info' => 'Indicator if beneficiary required for this product (Yes or No)',
			'field_name' => 'beneficiary_required'
		),
		array(
			'label' => 'Principal Beneficiary',
			'file_label' => 'PRINCIPAL_BENEFICIARY_DETAILS',
			'info' => 'Criteria required of principal beneficiary on Application (Separate multiple by comma)',
			'field_name' => 'principal_beneficiary'
		),
		array(
			'label' => 'Contingent Beneficiary',
			'file_label' => 'CONTINGENT BENEFICIARY_DETAILS',
			'info' => 'Criteria required of contingent beneficiary on Application (Separate multiple by comma)',
			'field_name' => 'contingent_beneficiary'
		),
		array(
			'label' => 'Verification',
			'file_label' => 'VERIFICATION_METHOD',
			'info' => 'Member verification method(s) accepted for Application (eSign, Voice Verification, Email/SMS Verification - Separate multiple by comma)',
			'field_name' => 'verification'
		),
		array(
			'label' => 'License',
			'file_label' => 'LICENSE_REQUIRED',
			'info' => 'Identifier if license is required to enroll consumers in this Plan (Yes or No)',
			'field_name' => 'license'
		),
		array(
			'label' => 'License Type',
			'file_label' => 'LICENSE_TYPE',
			'info' => 'Identifier of license type required by agent if license required (Life, Health, General Lines)',
			'field_name' => 'license_type'
		),
		array(
			'label' => 'License Rules',
			'file_label' => 'LICENSE_RULES',
			'info' => 'Rules for this license to enroll consumers (Licensed Only, Licensed in Sale State, Licensed in Specific States Only, Licensed and Appointed)',
			'field_name' => 'license_rules'
		),
		array(
			'label' => 'Specific States Assigned',
			'file_label' => 'SPECIFIC_STATES_ASSIGN',
			'info' => 'State(s) accepted if “licensed in specific states only” selected for license rules (two-digit abbreviation or full name and separate multiple by comma)',
			'field_name' => 'specific_states_assigned'
		),
		array(
			'label' => 'Pre-Sale App',
			'file_label' => 'PRE_SALE_APPOINTMENTS',
			'info' => 'State(s) agent must be appointed in prior to selling this product (two-digit abbreviation or full name and separate multiple by comma)',
			'field_name' => 'pre_sale_app'
		),
		array(
			'label' => 'In-Time App',
			'file_label' => 'JIT_APPOINTMENTS',
			'info' => 'State(s) agent agent may be appointed after their initial sale occurs (two-digit abbreviation or full name and separate multiple by comma)',
			'field_name' => 'in_time_app'
		),
	),
	'Pricing' => array(
		array(
			'label' => 'Plan Type',
			'file_label' => 'SUBSCRIPTION_TYPE',
			'info' => 'Indicator of Plan type (Single or Recurring)',
			'field_name' => 'policy_type'
		),
		array(
			'label' => 'Recurring Type',
			'file_label' => 'RECURRING_TYPE',
			'info' => 'Indicator of recurring type (Annual or Monthly)',
			'field_name' => 'recurring_type'
		),
		array(
			'label' => 'Pricing Model',
			'file_label' => 'PRICING_MODEL',
			'info' => 'Type of pricing model (Fixed Pricing, Variable by Plan Tier, Variable By Enrollee)',
			'field_name' => 'pricing_model'
		),
		array(
			'label' => 'Plan Tier/Enrollee',
			'file_label' => 'BENEFIT_TIER_ENROLLEE',
			'info' => 'Indicator of plan tier for pricing (Member Only, Member + One, Member + Child(ren), Member + Spouse, Family, Primary Enrollee, Spouse Enrollee, Child Enrollee)',
			'field_name' => 'benefit_tier'
		),
		array(
			'label' => 'Retail Price',
			'file_label' => 'RETAIL_PRICE',
			'info' => 'Retail price of product for this plan tier/enrollee type ($13.00)',
			'field_name' => 'Retail Price'
		),
		array(
			'label' => 'Non-Comm Price',
			'file_label' => 'NON_COMM_PRICE',
			'info' => 'Non-commissionable price of products for this plan tier/enrollee type ($5.00)',
			'field_name' => 'non_comm_price'
		),
		array(
			'label' => 'Commissionable Price',
			'file_label' => 'COMMISSIONABLE_PRICE',
			'info' => 'Commissionable Price of product for this plan tier/enrollee type ($8.00)',
			'field_name' => 'commissionable_price'
		),
		array(
			'label' => 'Vary: Age',
			'file_label' => 'AGE',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Age number or From - To range)',
			'field_name' => 'vary_age'
		),
		array(
			'label' => 'Gender',
			'file_label' => 'GENDER',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Male or Female)',
			'field_name' => 'gender'
		),
		array(
			'label' => 'Height By',
			'file_label' => 'HEIGHT_BY',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Exactly, Less Than, Greater Than, Range)',
			'field_name' => 'height_by'
		),
		array(
			'label' => 'Height',
			'file_label' => 'HEIGHT',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Height in feet and inches - 6’3” or From - To range)',
			'field_name' => 'height'
		),
		array(
			'label' => 'Weight By',
			'file_label' => 'WEIGHT_BY',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Exactly, Less Than, Greater Than, Range)',
			'field_name' => 'weight_by'
		),
		array(
			'label' => 'Weight',
			'file_label' => 'WEIGHT',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Weight in pounds - 125 or From - To range)',
			'field_name' => 'weight'
		),
		array(
			'label' => 'Has Spouse',
			'file_label' => 'HAS_SPOUSE',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Yes or No)',
			'field_name' => 'has_spouse'
		),
		array(
			'label' => 'State',
			'file_label' => 'STATE',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Two-digit abbreviations or whole month - multiple separated by comma)',
			'field_name' => 'state'
		),
		array(
			'label' => 'Smoking',
			'file_label' => 'SMOKING',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Yes or No)',
			'field_name' => 'smoking'
		),
		array(
			'label' => 'Tobacco',
			'file_label' => 'TOBACCO',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Yes or No)',
			'field_name' => 'tobacco'
		),
		array(
			'label' => 'Benefit Amount',
			'file_label' => 'BENEFIT_AMOUNT',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Whole numbers - $45,000)',
			'field_name' => 'benefit_amount'
		),
		array(
			'label' => 'Zip Code',
			'file_label' => 'ZIP_CODE',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (5 digit only USA zip Codes)',
			'field_name' => 'zip_code'
		),
		array(
			'label' => '# Children',
			'file_label' => 'ID_OF_CHILDREN',
			'info' => 'Variable criteria used if pricing is variable by plan tier or variable by enrollee and uses this specific criteria (Whole number 0-15)',
			'field_name' => 'number_of_children'
		),
		array(
			'label' => 'Product Fee',
			'file_label' => 'PRODUCT_FEE',
			'info' => 'Indicator if product has charged fee (Yes or No)',
			'field_name' => 'product_fee'
		),
		array(
			'label' => 'Fee Name',
			'file_label' => 'FEE_NAME',
			'info' => 'Name of Fee',
			'field_name' => 'fee_name'
		),
		array(
			'label' => 'Fee ID',
			'file_label' => 'FEE_ID',
			'info' => 'Unique identifier of product fee',
			'field_name' => 'fee_id'
		),
		array(
			'label' => 'Fee Type',
			'file_label' => 'FEE_TYPE',
			'info' => '“Charged” in each record if fee exists',
			'field_name' => 'fee_type'
		),
		array(
			'label' => 'Fee Effective',
			'file_label' => 'FEE_EFFECTIVE',
			'info' => 'Date fee is active on this product for new Applications (MM/DD/YYYY)',
			'field_name' => 'fee_effective'
		),
		array(
			'label' => 'Fee Termination',
			'file_label' => 'FEE_TERMINATION',
			'info' => 'Date fee is inactive on this product for new Applications (MM/DD/YYYY)',
			'field_name' => 'fee_termination'
		),
		array(
			'label' => 'Fee NewBiz',
			'file_label' => 'FEE_NB',
			'info' => 'Indicator if fee is charged on new business (Yes or No)',
			'field_name' => 'fee_on_new_bussiness'
		),
		array(
			'label' => 'Fee Renewals',
			'file_label' => 'FEE_RENEWALS',
			'info' => 'Indicator if fee is charged on renewals (Yes or No)',
			'field_name' => 'fee_on_renewal'
		),
		array(
			'label' => '# Renewals',
			'file_label' => 'ID_RENEWALS',
			'info' => 'Indicator of number of renewals to charge fee (1-12 or Continuous)',
			'field_name' => 'number_of_renewal'
		),
		array(
			'label' => 'Vary By Tier',
			'file_label' => 'VARY_BY_TIER',
			'info' => 'Indicator if fee varies by plan tier (Yes or NO)',
			'field_name' => 'vary_by_tier'
		),
		array(
			'label' => 'Tier Amount',
			'file_label' => 'TIER_AMOUNT',
			'info' => 'Indicator of varying fee for this plan tier if varies by tier ($5.00 or 25%)',
			'field_name' => 'tier_amount'
		),
		array(
			'label' => 'Calculated By',
			'file_label' => 'CALCULATED_BY',
			'info' => 'Indicator of how fee is calculated (Fixed Price or Percentage)',
			'field_name' => 'calculated_by'
		),
		array(
			'label' => 'Percentage of',
			'file_label' => 'PERCENTAGE_OF',
			'info' => 'Indicator of percentage applied to if percentage based fee (Retail Price, Commissionable Price, Non-Commissionable Price)',
			'field_name' => 'percentage_of'
		),
		array(
			'label' => 'Fee Price/Percentage',
			'file_label' => 'FEE PRICE_PERCENTAGE',
			'info' => 'Indicatory of fee price if not vary by plan tier ($5.00 or 25%)',
			'field_name' => 'fee_price'
		),
	), 

);

$columnCounter=0;
foreach ($PRODUCT_IMPORT_ARR as $label => $fields) { ?>
	<div class="line_title">
		<h3><span><?=$label?></span></h3>
	</div>
	<div class="row">
	<?php foreach($fields as $field) { ?>
         <div class="col-sm-12 col-md-6">
            <div class="form-group height_auto">
               <div class="input-group resources_addon">
                  <div class="input-group-addon">
                     <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$field['info']?>"></i> <?=$field['label']?>
                  </div>
                  <div class="pr">
                     <select class="form-control select" name="<?=$field['field_name']?>" data-live-search="true">
                           <option data-hidden="true"></option>
                          <?php foreach ($row as $key => $value) {
                              $selectedOption = "";
                              $optn_val=$value;
                              if ($field['label'] == trim($value) || ($field['file_label'] == trim($value))) {
                                 $selectedOption = 'selected="selected"';
                              } else if ($columnCounter == $key) {
                                 $optn_val='None';
                                 $value='';
                                 $selectedOption = '';
                              } 
                           ?>
                           <option value="<?=$optn_val?>" <?=$selectedOption?>><?=$value?></option>
                        <?php } ?>
                     </select>
                     <label class="label-wrap">Select CSV Column</label>
                  </div>
               </div>
               <p class="error" id="err_<?=$field['field_name']?>"></p>
            </div>
         </div>
      <?php } ?>
	</div>
<?php } 

?>