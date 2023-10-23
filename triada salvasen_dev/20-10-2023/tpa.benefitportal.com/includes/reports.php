<?php

class Report {

  private $errors;
  private $pdo;

  public function __construct() {
    global $pdo;
  }

  public function update_rps_user_report_settings($user_id,$user_type,$report_id,$data = array())
	{
		global $pdo;
		$setting_data = array();
		$setting_data['extra'] = (!empty($data['extra'])?json_encode($data['extra']):'');
		if(isset($data['selected_columns'])) {
			$setting_data['selected_columns'] = $data['selected_columns'];
		}
		$setting_row = $pdo->selectOne("SELECT * FROM rps_user_report_settings WHERE user_id=:user_id AND user_type=:user_type AND report_id=:report_id",array(':user_id' => $user_id,':user_type' => $user_type,':report_id' => $report_id));
		if(!empty($setting_row)) {
			$where = array("clause" => "id=:id", "params" => array(":id" => $setting_row['id']));
	        $pdo->update("rps_user_report_settings",$setting_data, $where);
		} else {
			$setting_data['user_id'] = $user_id;
			$setting_data['user_type'] = $user_type;
			$setting_data['report_id'] = $report_id;
			$pdo->insert("rps_user_report_settings",$setting_data);
		}
		return true;
	}
	
	public function get_rps_user_report_settings($user_id,$user_type,$report_id)
	{
		global $pdo;
		$setting_row = $pdo->selectOne("SELECT * FROM rps_user_report_settings WHERE user_id=:user_id AND user_type=:user_type AND report_id=:report_id",array(':user_id' => $user_id,':user_type' => $user_type,':report_id' => $report_id));
		if(empty($setting_row)) {
			$setting_row = array();
		}
		return $setting_row;
	}

  /*
	Add same changes in Insurance-reports -> reports.php
  */
  public function getfields($report_key="") {
  	global $CREDIT_CARD_ENC_KEY;
  	$field_arr = array();
  	if(!$report_key){
  		return $field_arr;
  	}
  	if(in_array($report_key, array('payment_policy_overview','member_summary'))){
      $report_key = 'payment_policy_overview';
    }
    $field_arr[$report_key] = array();
    
  	$field_arr['payables_export'] = array(
  		'ADDED_DATE' => array(
  					'lable' => 'ADDED_DATE',
  					'field_name' => 'pd.created_at',
  					'field_table' => 'payable_details',
  					'is_default' => 'Y',
  				),
  		'TRANSACTION_DATE' => array(
  					'lable' => 'TRANSACTION_DATE',
  					'field_name' => 'DATE(t.created_at)',
  					'field_table' => 'transactions',
  					'is_default' => 'Y',
  				),
  		'ORDER_ID' => array(
  					'lable' => 'ORDER_ID',
  					'field_name' => 'o.display_id',
  					'field_table' => 'orders',
  					'is_default' => 'Y',
  				),
  		'POLICY_ID' => array(
  					'lable' => 'POLICY_ID',
  					'field_name' => 'ws.website_id',
  					'field_table' => 'website_subscriptions',
  					'is_default' => 'Y',
  				),
  		'PAYEE_TYPE' => array(
  					'lable' => 'PAYEE_TYPE',
  					'field_name' => "IF(pd.payee_type='Agent',pd.type,pd.payee_type)",
  					'field_table' => 'payable_details',
  					'is_default' => 'Y',
  				),
  		'PAYEE_ID' => array(
  					'lable' => 'PAYEE_ID',
  					'field_name' => "IF(pd.payee_type='Agent',ag.rep_id,pf.display_id)",
  					'field_table' => '',
  					'is_default' => 'Y',
  				),
  		'PAYEE' => array(
  					'lable' => 'PAYEE',
  					'field_name' => "IF(pd.commission_id > 0,CONCAT(ag.fname,' ',ag.lname),pf.name)",
  					'field_table' => '',
  					'is_default' => 'Y',
  				),
  		'PAYEE_ADDRESS' => array(
  					'lable' => 'PAYEE_ADDRESS',
  					'field_name' => "IF(pd.commission_id > 0,ag.address,pf.address)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PAYEE_ADDRESS2' => array(
  					'lable' => 'PAYEE_ADDRESS2',
  					'field_name' => "IF(pd.commission_id > 0,ag.address_2,pf.address2)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PAYEE_CITY' => array(
  					'lable' => 'PAYEE_CITY',
  					'field_name' => "IF(pd.commission_id > 0,ag.city,pf.city)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PAYEE_STATE' => array(
  					'lable' => 'PAYEE_STATE',
  					'field_name' => "IF(pd.commission_id > 0,ag.state,pf.state)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PAYEE_ZIP' => array(
  					'lable' => 'PAYEE_ZIP',
  					'field_name' => "IF(pd.commission_id > 0,ag.zip,pf.zipcode)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'FEE_NAME' => array(
  					'lable' => 'FEE_NAME',
  					'field_name' => "IF(pd.type='Vendor',fee_prd.name,pd.type)",
  					'field_table' => '',
  					'is_default' => 'Y',
  				),
  		'FEE_ID' => array(
  					'lable' => 'FEE_ID',
  					'field_name' => "IF(pd.type='Vendor',fee_prd.product_code,IF(pd.type='PMPM',pmpm.rule_code,cm_r.rule_code))",
  					'field_table' => '',
  					'is_default' => 'Y',
  				),
  		'MEMBER_ID' => array(
  					'lable' => 'MEMBER_ID',
  					'field_name' => 'c.rep_id',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'MEMBER_FIRSTNAME' => array(
  					'lable' => 'MEMBER_FIRSTNAME',
  					'field_name' => 'c.fname',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'MEMBER_LASTNAME' => array(
  					'lable' => 'MEMBER_LASTNAME',
  					'field_name' => 'c.lname',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'MEMBER_STATE' => array(
  					'lable' => 'MEMBER_STATE',
  					'field_name' => 'c.state',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'ENROLLING_AGENT_ID' => array(
  					'lable' => 'ENROLLING_AGENT_ID',
  					'field_name' => 'e_ag.rep_id',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'AGENCY_NAME' => array(
  					'lable' => 'AGENCY_NAME',
  					'field_name' => "IF(e_ag_s.account_type = 'Business',e_ag_s.company_name,'')",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'AGENT_NAME' => array(
  					'lable' => 'AGENT_NAME',
  					'field_name' => "CONCAT(e_ag.fname,' ',e_ag.lname)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'AGENT_LEVEL' => array(
  					'lable' => 'AGENT_LEVEL',
  					'field_name' => 'e_ag_s.agent_coded_level',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PRODUCT_CATEGORY' => array(
  					'lable' => 'PRODUCT_CATEGORY',
  					'field_name' => 'pmc.title',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PRODUCT_ID' => array(
  					'lable' => 'PRODUCT_ID',
  					'field_name' => 'pm.product_code',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PRODUCT_NAME' => array(
  					'lable' => 'PRODUCT_NAME',
  					'field_name' => 'pm.name',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'APPLY_NEW_BUS' => array(
  					'lable' => 'APPLY_NEW_BUS',
  					'field_name' => "IF(fee_prd.id IS NOT NULL,IF(fee_prd.initial_purchase = 'Y','Yes','No'),'')",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'APPLY_RENEWALS' => array(
  					'lable' => 'APPLY_RENEWALS',
  					'field_name' => "IF(fee_prd.id IS NOT NULL,IF(fee_prd.is_fee_on_renewal = 'Y','Yes','No'),'')",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'RENEWAL_COUNT' => array(
  					'lable' => 'RENEWAL_COUNT',
  					'field_name' => "IF(fee_prd.id IS NOT NULL,IF(fee_prd.is_fee_on_renewal = 'Y',IF(fee_prd.fee_renewal_type='Continuous','Continuous',fee_prd.fee_renewal_count),''),'')",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PROD_ADDED_DATE' => array(
  					'lable' => 'PROD_ADDED_DATE',
  					'field_name' => 'ws.created_at',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PROD_EFFECTIVE_DATE' => array(
  					'lable' => 'PROD_EFFECTIVE_DATE',
  					'field_name' => 'ws.eligibility_date',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PROD_TERM_DATE' => array(
  					'lable' => 'PROD_TERM_DATE',
  					'field_name' => 'ws.termination_date',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'TRANSACTION_ID' => array(
  					'lable' => 'TRANSACTION_ID',
  					'field_name' => 't.transaction_id',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'TRANSACTION_AMT' => array(
  					'lable' => 'TRANSACTION_AMT',
  					'field_name' => "IF(t.order_type='Credit',t.credit,t.debit)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'TRANSACTION_STATUS' => array(
  					'lable' => 'TRANSACTION_STATUS',
  					'field_name' => 't.transaction_status',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PAYMENT_TYPE' => array(
  					'lable' => 'PAYMENT_TYPE',
  					'field_name' => 't.payment_type',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'COVERAGE_PERIOD' => array(
  					'lable' => 'COVERAGE_PERIOD',
  					'field_name' => "CONCAT('P',od.renew_count)",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'COVERAGE_PERIOD_START' => array(
  					'lable' => 'COVERAGE_PERIOD_START',
  					'field_name' => "od.start_coverage_period",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'COVERAGE_PERIOD_END' => array(
  					'lable' => 'COVERAGE_PERIOD_END',
  					'field_name' => "od.end_coverage_period",
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'BENEFIT_TIER' => array(
  					'lable' => 'BENEFIT_TIER',
  					'field_name' => 'ppt.title',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'RETAIL_PRICE' => array(
  					'lable' => 'RETAIL_PRICE',
  					'field_name' => 'od.unit_price',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'COMMISSIONABLE_PRICE' => array(
  					'lable' => 'COMMISSIONABLE_PRICE',
  					'field_name' => 'px.commission_amount',
  					'field_table' => '',
  					'is_default' => 'N',
  				),
  		'PAYOUT' => array(
  					'lable' => 'PAYOUT',
  					'field_name' => "CASE
		                WHEN pd.commission_id > 0 THEN
		                	CASE 
		                		WHEN comm.is_advance='Y' THEN 
                                    CASE 
                                        WHEN comm.sub_type='Reverse' THEN (SELECT CONCAT(advance_month,' Months') FROM commission WHERE advance_reverse_id = comm.id LIMIT 1)
                                        ELSE CONCAT(comm.advance_month,' Months')
                                    END
		                		WHEN comm.is_pmpm_comm='Y' THEN CONCAT('$',ABS(comm.amount))
		                		WHEN comm.is_fee_comm='Y' THEN IF(comm.original_amount IS NOT NULL AND comm.original_amount != 0,CONCAT('$',ABS(comm.original_amount)),CONCAT(comm.percentage,'%'))
		                		ELSE IF(comm.original_amount IS NOT NULL AND comm.original_amount != 0,CONCAT('$',ABS(comm.original_amount)),CONCAT(comm.percentage,'%'))
		                	END
	                	WHEN fee_prd.id IS NOT NULL THEN
	                		CASE 
		                		WHEN fee_matrix.price_calculated_on = 'Percentage' THEN CONCAT(pd.payout,'%')
		                		ELSE CONCAT('$',pd.payout)
		                	END
		                ELSE CONCAT('$',pd.payout)
	                END",
  					'field_table' => '',
  					'is_default' => 'Y',
  				),
  		'CREDIT' => array(
  					'lable' => 'CREDIT',
  					'field_name' => 'pd.credit',
  					'field_table' => '',
  					'is_default' => 'Y',
  				),
  		'DEBIT' => array(
  					'lable' => 'DEBIT',
  					'field_name' => 'pd.debit',
  					'field_table' => '',
  					'is_default' => 'Y',
  				),
  		'COMPANY' => array(
            'lable' => 'COMPANY',
            'field_name' => 'e_ag_s.company',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
          ),

  	);
	$field_arr['admin_payment_transaction_report'] = array(
		'TRANSACTION_DATE' => array(
			'lable' => 'TRANSACTION_DATE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'MEMBER_ID' => array(
			'lable' => 'MEMBER_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'MEMBER_NAME' => array(
			'lable' => 'MEMBER_NAME',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
    'COMPANY' => array(
      'lable' => 'COMPANY',
      'field_name' => '',
      'field_table' => '',
      'is_default' => 'N',
      ),
		'CITY' => array(
			'lable' => 'CITY',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'STATE' => array(
			'lable' => 'STATE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'GENDER' => array(
			'lable' => 'GENDER',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'ORDER_ID' => array(
			'lable' => 'ORDER_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'ORIGINAL_TRANS_DATE' => array(
			'lable' => 'ORIGINAL_TRANS_DATE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'TRANSACTION_STATUS' => array(
			'lable' => 'TRANSACTION_STATUS',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'LATEST_ORDER_STATUS' => array(
			'lable' => 'LATEST_ORDER_STATUS',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'TRANSACTION_ID' => array(
			'lable' => 'TRANSACTION_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'TRANSACTION_RESPONSE' => array(
			'lable' => 'TRANSACTION_RESPONSE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'AUTHORIZATION_CODE' => array(
			'lable' => 'AUTHORIZATION_CODE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'PAYMENT_METHOD' => array(
			'lable' => 'PAYMENT_METHOD',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'LAST_FOUR' => array(
			'lable' => 'LAST_FOUR',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'SALE_TYPE' => array(
			'lable' => 'SALE_TYPE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'PROCESSOR' => array(
			'lable' => 'PROCESSOR',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'ADDED_DATE' => array(
			'lable' => 'ADDED_DATE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'POLICY_ID' => array(
			'lable' => 'POLICY_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'CARRIER_ID' => array(
			'lable' => 'CARRIER_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'CARRIER_NAME' => array(
			'lable' => 'CARRIER_NAME',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'CATEGORY_NAME' => array(
			'lable' => 'CATEGORY_NAME',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'PRODUCT_ID' => array(
			'lable' => 'PRODUCT_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'PRODUCT_NAME' => array(
			'lable' => 'PRODUCT_NAME',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'BENEFIT_TIER' => array(
			'lable' => 'BENEFIT_TIER',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'EFFECTIVE_DATE' => array(
			'lable' => 'EFFECTIVE_DATE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'TERMINATION_DATE' => array(
			'lable' => 'TERMINATION_DATE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'COVERAGE_START' => array(
			'lable' => 'COVERAGE_START',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'COVERAGE_END' => array(
			'lable' => 'COVERAGE_END',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'PRODUCT_FEE' => array(
			'lable' => 'PRODUCT_FEE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'HEALTHY_STEP' => array(
			'lable' => 'HEALTHY_STEP',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'SERVICE_FEE' => array(
			'lable' => 'SERVICE_FEE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'RETAIL_PRICE' => array(
			'lable' => 'RETAIL_PRICE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'TOTAL_PRICE' => array(
			'lable' => 'TOTAL_PRICE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'ORDER_TOTAL' => array(
			'lable' => 'ORDER_TOTAL',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'Y',
			),
		'DOB' => array(
			'lable' => 'DOB',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'LAST_4SSN' => array(
			'lable' => 'LAST_4SSN',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'ENROLLING_AGENT_ID' => array(
			'lable' => 'ENROLLING_AGENT_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'ENROLLING_AGENT' => array(
			'lable' => 'ENROLLING_AGENT',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'UPLINE_TREE_ID' => array(
			'lable' => 'UPLINE_TREE_ID',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'UPLINE_TREE_NAME' => array(
			'lable' => 'UPLINE_TREE_NAME',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'REVERSAL_REASON' => array(
			'lable' => 'REVERSAL_REASON',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'NON_COMM_PRICE' => array(
			'lable' => 'NON_COMM_PRICE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'COMMISSIONABLE_PRICE' => array(
			'lable' => 'COMMISSIONABLE_PRICE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'AGE' => array(
			'lable' => 'AGE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'GENDER' => array(
			'lable' => 'GENDER',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'HEIGHT' => array(
			'lable' => 'HEIGHT',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'WEIGHT' => array(
			'lable' => 'WEIGHT',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'HAS_SPOUSE' => array(
			'lable' => 'HAS_SPOUSE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'STATE' => array(
			'lable' => 'STATE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'SMOKING' => array(
			'lable' => 'SMOKING',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'TOBACCO' => array(
			'lable' => 'TOBACCO',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'BENEFIT_AMOUNT' => array(
			'lable' => 'BENEFIT_AMOUNT',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
            'IN_PATIENT_BENEFIT' => array(
                  'lable' => 'IN_PATIENT_BENEFIT',
                  'field_name' => '',
                  'field_table' => '',
                  'is_default' => 'N',
                  ),
            'OUT_PATIENT_BENEFIT' => array(
                  'lable' => 'OUT_PATIENT_BENEFIT',
                  'field_name' => '',
                  'field_table' => '',
                  'is_default' => 'N',
                  ),
            'MONTHLY_INCOME' => array(
                  'lable' => 'MONTHLY_INCOME',
                  'field_name' => '',
                  'field_table' => '',
                  'is_default' => 'N',
                  ),
		'ZIP_CODE' => array(
			'lable' => 'ZIP_CODE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'#_OF_CHILDREN' => array(
			'lable' => '#_OF_CHILDREN',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'GROUP_CODE' => array(
			'lable' => 'GROUP_CODE',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
		'PLAN_CODE1' => array(
			'lable' => 'PLAN_CODE1',
			'field_name' => '',
			'field_table' => '',
			'is_default' => 'N',
			),
	);
	$field_arr['payment_policy_overview'] = array(
      'ADDED_DATE' => array(
        'lable' => 'ADDED_DATE',
        'field_name' => 'w.created_at',
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'Y',
        
      ),
      'PRIMARY_MEMBER_ID' => array(
        'lable' => 'PRIMARY_MEMBER_ID',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'MEMBER_ID' => array(
        'lable' => 'MEMBER_ID',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'RELATION' => array(
        'lable' => 'RELATION',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'FIRST_NAME' => array(
        'lable' => 'FIRST_NAME',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'LAST_NAME' => array(
        'lable' => 'LAST_NAME',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'ADDRESS' => array(
        'lable' => 'ADDRESS',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'ADDRESS2' => array(
        'lable' => 'ADDRESS2',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'CITY' => array(
        'lable' => 'CITY',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'STATE' => array(
        'lable' => 'STATE',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'ZIP_CODE' => array(
        'lable' => 'ZIP_CODE',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'DOB' => array(
        'lable' => 'DOB',
        'field_name' => '',
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'GENDER' => array(
        'lable' => 'GENDER',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'SSN' => array(
        'lable' => 'SSN',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'EMAIL' => array(
				'lable' => 'EMAIL',
				'field_name' => '',
				'field_table' => '',
				'is_default' => 'N',
			),
  		'PHONE' => array(
				'lable' => 'PHONE',
				'field_name' => '',
				'field_table' => '',
				'is_default' => 'N',
			),
      'PRODUCT_ADDED_DATE' => array(
        'lable' => 'PRODUCT_ADDED_DATE',
        'field_name' => 'w.created_at',
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'POLICY_ID' => array(
        'lable' => 'POLICY_ID',
        'field_name' => 'w.website_id',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'PRODUCT_CATEGORY' => array(
        'lable' => 'PRODUCT_CATEGORY',
        'field_name' => 'pc.title',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'PRODUCT_CARRIER' => array(
        'lable' => 'PRODUCT_CARRIER',
        'field_name' => 'pf.name',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'PRODUCT_ID' => array(
        'lable' => 'PRODUCT_ID',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'PRODUCT_NAME' => array(
        'lable' => 'PRODUCT_NAME',
        'field_name' => 'prd.name',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'BENEFIT_TIER' => array(
        'lable' => 'BENEFIT_TIER',
        'field_name' => 'ppp.title',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'EFFECTIVE_DATE' => array(
        'lable' => 'EFFECTIVE_DATE',
        'field_name' => 'w.eligibility_date',
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'TERMINATION_DATE' => array(
        'lable' => 'TERMINATION_DATE',
        'field_name' => 'w.termination_date',
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'NEXT_BILLING_DATE' => array(
        'lable' => 'NEXT_BILLING_DATE',
        'field_name' => "IF(w.termination_date is null,w.next_purchase_date,'')",
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'COVERAGE_PERIODS' => array(
        'lable' => 'COVERAGE_PERIODS',
        'field_name' => "IF(
                date_format(date(w.eligibility_date),'%Y-%m-%d') = date_format(date(w.termination_date),'%Y-%m-%d')
                ,'P0',
                IF(
                    date(w.termination_date) < date(w.end_coverage_period),
                    CONCAT('P',w.renew_count-1),
                    IF(w.renew_count=0,CONCAT('P',w.renew_count+1),CONCAT('P',w.renew_count))
                )
            )",
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'DATE_TERMINATED' => array(
        'lable' => 'DATE_TERMINATED',
        'field_name' => 'w.term_date_set',
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'REASON' => array(
        'lable' => 'REASON',
        'field_name' => 'w.termination_reason',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'RETAIL_PRICE' => array(
        'lable' => 'RETAIL_PRICE',
        'field_name' => 'w.price',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'MEMBER_PRICE' => array(
        'lable' => 'MEMBER_PRICE',
        'field_name' => '(w.price-w.group_price)',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'GROUP_PRICE' => array(
        'lable' => 'GROUP_PRICE',
        'field_name' => 'w.group_price',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'P1_PAYMENT_DATE' => array(
        'lable' => 'P1_PAYMENT_DATE',
        'field_name' => "IF(t.created_at IS NOT NULL,t.created_at,IF(lb.status!='paid' AND lb.due_date IS NOT NULL,lb.due_date,''))",
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'Y',
      ),
      'ENROLLING_AGENT_ID' => array(
        'lable' => 'ENROLLING_AGENT_ID',
        'field_name' => 's.rep_id',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'ENROLLING_AGENT_NAME' => array(
        'lable' => 'ENROLLING_AGENT_NAME',
        'field_name' => "CONCAT(s.fname,' ',s.lname)",
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'TREE_ID' => array(
        'lable' => 'TREE_ID',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'TREE_NAME' => array(
        'lable' => 'TREE_NAME',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'HEIGHT' => array(
        'lable' => 'HEIGHT',
        'field_name' => "",
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'WEIGHT' => array(
        'lable' => 'WEIGHT',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'SMOKE' => array(
        'lable' => 'SMOKE',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'TOBACCO' => array(
        'lable' => 'TOBACCO',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'EMPLOYMENT_STATUS' => array(
        'lable' => 'EMPLOYMENT_STATUS',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'ANNUAL_SALARY' => array(
        'lable' => 'ANNUAL_SALARY',
        'field_name' => '',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'ENROLLEE_TYPE' => array(
        'lable' => 'ENROLLEE_TYPE',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'RELATIONSHIP_DATE' => array(
        'lable' => 'RELATIONSHIP_DATE',
        'field_name' => '',
        'field_type' => 'date',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'HOURS_WORKED_PER_WK' => array(
        'lable' => 'HOURS_WORKED_PER_WK',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'PAY_FREQUENCY' => array(
        'lable' => 'PAY_FREQUENCY',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'US_CITIZEN' => array(
        'lable' => 'US_CITIZEN',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'HAS_SPOUSE' => array(
        'lable' => 'HAS_SPOUSE',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'BENEFIT_AMOUNT' => array(
        'lable' => 'BENEFIT_AMOUNT',
        'field_name' => '',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'IN_PATIENT_BENEFIT' => array(
        'lable' => 'IN_PATIENT_BENEFIT',
        'field_name' => '',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'OUT_PATIENT_BENEFIT' => array(
        'lable' => 'OUT_PATIENT_BENEFIT',
        'field_name' => '',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'MONTHLY_INCOME' => array(
        'lable' => 'MONTHLY_INCOME',
        'field_name' => '',
        'field_type' => 'amount',
        'field_table' => '',
        'is_default' => 'N',
      ),
      'CUSTOM_QUESTIONS' => array(
        'lable' => 'CUSTOM_QUESTIONS',
        'field_name' => '',
        'field_type' => 'text',
        'field_table' => '',
        'is_default' => 'N',
      ),
    );

	//Agent Export OR Agent Summary
    $field_arr['agent_export'] = array(
      'ADDED_DATE'=> array(
	      'lable' => 'ADDED_DATE',
	      'is_default' => 'N',
	  	),
      'AGENT_ID'=> array(
            'lable' => 'AGENT_ID',
            'is_default' => 'Y',
        ),
      'ACCOUNT_TYPE'=> array(
            'lable' => 'ACCOUNT_TYPE',
            'is_default' => 'N',
        ),
      'AGENCY_LEGAL_NAME'=> array(
            'lable' => 'AGENCY_LEGAL_NAME',
            'is_default' => 'N',
        ),
      'AGENCY_ADDRESS'=> array(
            'lable' => 'AGENCY_ADDRESS',
            'is_default' => 'N',
        ),
      'AGENCY_ADDRESS_2'=> array(
            'lable' => 'AGENCY_ADDRESS_2',
            'is_default' => 'N',
        ),
      'AGENCY_CITY'=> array(
            'lable' => 'AGENCY_CITY',
            'is_default' => 'N',
        ),
      'AGENCY_STATE'=> array(
            'lable' => 'AGENCY_STATE',
            'is_default' => 'N',
        ),
      'AGENCY_ZIP_CODE'=> array(
            'lable' => 'AGENCY_ZIP_CODE',
            'is_default' => 'N',
        ),
      'AGENCY_EIN'=> array(
            'lable' => 'AGENCY_EIN',
            'is_default' => 'N',
        ),
      'PRINCIPAL_AGENT'=> array(
            'lable' => 'PRINCIPAL_AGENT',
            'is_default' => 'N',
        ),
      'AGENT_ADDRESS'=> array(
            'lable' => 'AGENT_ADDRESS',
            'is_default' => 'N',
        ),
      'AGENT_ADDRESS_2'=> array(
            'lable' => 'AGENT_ADDRESS_2',
            'is_default' => 'N',
        ),
      'AGENT_CITY'=> array(
            'lable' => 'AGENT_CITY',
            'is_default' => 'N',
        ),
      'AGENT_STATE'=> array(
            'lable' => 'AGENT_STATE',
            'is_default' => 'N',
        ),
      'AGENT_ZIP_CODE'=> array(
            'lable' => 'AGENT_ZIP_CODE',
            'is_default' => 'N',
        ),
      'AGENT_SSN'=> array(
            'lable' => 'AGENT_SSN',
            'is_default' => 'N',
        ),
      'AGENT_PHONE'=> array(
            'lable' => 'AGENT_PHONE',
            'is_default' => 'N',
        ),
      'AGENT_DOB'=> array(
            'lable' => 'AGENT_DOB',
            'is_default' => 'N',
        ),
      'AGENT_EMAIL'=> array(
            'lable' => 'AGENT_EMAIL',
            'is_default' => 'N',
        ),
      'AGENT_LEVEL'=> array(
            'lable' => 'AGENT_LEVEL',
            'is_default' => 'N',
        ),
      'AGENCY_TREE'=> array(
            'lable' => 'AGENCY_TREE',
            'is_default' => 'N',
        ),
      'PARENT_AGENT'=> array(
            'lable' => 'PARENT_AGENT',
            'is_default' => 'N',
        ),
      'AGENT_STATUS'=> array(
            'lable' => 'AGENT_STATUS',
            'is_default' => 'N',
        ),
      'AGENT_ALERTS'=> array(
            'lable' => 'AGENT_ALERTS',
            'is_default' => 'N',
            'is_custom' => 'Y',
        ),
      'DOWNLINE_AGENTS'=> array(
            'lable' => 'DOWNLINE_AGENTS',
            'is_default' => 'N',
        ),
      'AGENT_PRIMARY_MERCHANT_ACH'=> array(
            'lable' => 'AGENT_PRIMARY_MERCHANT_ACH',
            'is_default' => 'N',
        ),
      'AGENT_PRIMARY_MERCHANT_CC'=> array(
            'lable' => 'AGENT_PRIMARY_MERCHANT_CC',
            'is_default' => 'N',
        ),
      'AGENT_VARIATION_MERCHANTS'=> array(
            'lable' => 'AGENT_VARIATION_MERCHANTS',
            'is_default' => 'N',
        ),
      'W9_DOCUMENT'=> array(
            'lable' => 'W9_DOCUMENT',
            'is_default' => 'N',
        ),
      'NPN'=> array(
            'lable' => 'NPN',
            'is_default' => 'N',
        ),
      'E_O_AMOUNT'=> array(
            'lable' => 'E&O_AMOUNT',
            'is_default' => 'N',
        ),
      'E_O_DOCUMENT'=> array(
            'lable' => 'E&O_DOCUMENT',
            'is_default' => 'N',
        ),
      'E_O_EXPIRATION'=> array(
            'lable' => 'E&O_EXPIRATION',
            'is_default' => 'N',
        ),
      'LICENSE_STATE'=> array(
            'lable' => 'LICENSE_STATE',
            'field_table' => 'agent_license_l',
            'is_default' => 'N',
        ),
      'LICENSE_NUMBER'=> array(
            'lable' => 'LICENSE_NUMBER',
            'is_default' => 'N',
        ),
      'LICENSE_ACTIVE'=> array(
            'lable' => 'LICENSE_ACTIVE',
            'is_default' => 'N',
        ),
      'LICENSE_EXPIRATION'=> array(
            'lable' => 'LICENSE_EXPIRATION',
            'is_default' => 'N',
        ),
      'LICENSE_TYPE'=> array(
            'lable' => 'LICENSE_TYPE',
            'is_default' => 'N',
        ),
      'LINES_OF_AUTHORITY'=> array(
            'lable' => 'LINES_OF_AUTHORITY',
            'is_default' => 'N',
        ),
      'LICENSE_STATUS'=> array(
            'lable' => 'LICENSE_STATUS',
            'is_default' => 'N',
        ),
      'HIDE_DISPLAY'=> array(
            'lable' => 'HIDE_DISPLAY',
            'is_default' => 'N',
        ),
      'DISPLAY_NAME'=> array(
            'lable' => 'DISPLAY_NAME',
            'is_default' => 'N',
        ),
      'DISPLAY_PHONE'=> array(
            'lable' => 'DISPLAY_PHONE',
            'is_default' => 'N',
        ),
      'DISPLAY_EMAIL'=> array(
            'lable' => 'DISPLAY_EMAIL',
            'is_default' => 'N',
        ),
      'AGENT_USERNAME'=> array(
            'lable' => 'AGENT_USERNAME',
            'is_default' => 'N',
        ),
      'AGENT_CUSTOM_BRAND'=> array(
            'lable' => 'AGENT_CUSTOM_BRAND',
            'is_default' => 'N',
        ),
      'ACTIVE_DD_DATE'=> array(
            'lable' => 'ACTIVE_DD_DATE',
            'is_default' => 'N',
        ),
      'ACTIVE_DD_BANK'=> array(
            'lable' => 'ACTIVE_DD_BANK',
            'is_default' => 'N',
        ),
      'ACTIVE_ROUTING'=> array(
            'lable' => 'ACTIVE_ROUTING',
            'is_default' => 'N',
        ),
      'ACTIVE_ACCT'=> array(
            'lable' => 'ACTIVE_ACCT_#',
            'is_default' => 'N',
        ),
      'ACCOUNT_MANAGERS'=> array(
            'lable' => 'ACCOUNT_MANAGERS',
            'is_default' => 'N',
        ),
      'ACCESS_ENROLL_MEMBER'=> array(
            'lable' => 'ACCESS_ENROLL_MEMBER',
            'is_default' => 'N',

        ),
      'ACCESS_ENROLL_AGENT'=> array(
            'lable' => 'ACCESS_ENROLL_AGENT',
            'is_default' => 'N',

        ),
      'ACCESS_ENROLL_GROUP'=> array(
            'lable' => 'ACCESS_ENROLL_GROUP',
            'is_default' => 'N',

        ),
      'ACCESS_ENROLLMENT_WEBSITES'=> array(
            'lable' => 'ACCESS_ENROLLMENT_WEBSITES',
            'is_default' => 'N',

        ),
      'ACCESS_BOB_AGENTS'=> array(
            'lable' => 'ACCESS_BOB_AGENTS',
            'is_default' => 'N',

        ),
      'ACCESS_BOB_MEMBERS'=> array(
            'lable' => 'ACCESS_BOB_MEMBERS',
            'is_default' => 'N',

        ),
      'ACCESS_BOB_GROUPS'=> array(
            'lable' => 'ACCESS_BOB_GROUPS',
            'is_default' => 'N',

        ),
      'ACCESS_BOB_LEADS'=> array(
            'lable' => 'ACCESS_BOB_LEADS',
            'is_default' => 'N',

        ),
      'ACCESS_BOB_PENDING_AAE'=> array(
            'lable' => 'ACCESS_BOB_PENDING_AAE',
            'is_default' => 'N',

        ),
      'ACCESS_PRODUCTION_REPORTING'=> array(
            'lable' => 'ACCESS_PRODUCTION_REPORTING',
            'is_default' => 'N',

        ),
      'ACCESS_PRODUCTION_COMMISSIONS'=> array(
            'lable' => 'ACCESS_PRODUCTION_COMMISSIONS',
            'is_default' => 'N',

        ),
      'ACCESS_PRODUCTION_ORDERS'=> array(
            'lable' => 'ACCESS_PRODUCTION_ORDERS',
            'is_default' => 'N',

        ),
      'ACCESS_PRODUCTION_PRODUCTS'=> array(
            'lable' => 'ACCESS_PRODUCTION_PRODUCTS',
            'is_default' => 'N',

        ),
      'ACCESS_PRODUCTION_TRANSACTIONS'=> array(
            'lable' => 'ACCESS_PRODUCTION_TRANSACTIONS',
            'is_default' => 'N',

        ),
      'ACCESS_RESOURCES_EMAIL_BROADCASTER'=> array(
            'lable' => 'ACCESS_RESOURCES_EMAIL_BROADCASTER',
            'is_default' => 'N',

        ),
      'ACCESS_RESOURCES_TEXT_BROADCASTER'=> array(
            'lable' => 'ACCESS_RESOURCES_TEXT_BROADCASTER',
            'is_default' => 'N',

        ),
      'ACCESS_RESOURCES_TRAINING_MANUALS'=> array(
            'lable' => 'ACCESS_RESOURCES_TRAINING_MANUALS',
            'is_default' => 'N',

        ),
      'ACCESS_RESOURCES_API'=> array(
            'lable' => 'ACCESS_RESOURCES_API',
            'is_default' => 'N',

        ),
      'ACCESS_RESOURCES_SUPPORT'=> array(
            'lable' => 'ACCESS_RESOURCES_SUPPORT',
            'is_default' => 'N',

        ),
      'AGENT_LATEST_NEW_BUSINESS_SALE'=> array(
            'lable' => 'AGENT_LATEST_NEW_BUSINESS_SALE',
            'is_default' => 'N',
        ),
      'AGENT_DOWNLINE_LATEST_NEW_BUSINESS_SALE'=> array(
            'lable' => 'AGENT_DOWNLINE_LATEST_NEW_BUSINESS_SALE',
            'is_default' => 'N',
        ),
      'CARRIER_WRITING_NUMBER'=> array(
            'lable' => 'CARRIER_WRITING_NUMBER',
            'is_default' => 'N',
        ),
    );

	//Commission Setup
    $field_arr['commission_setup'] = array(
      	'ADDED_DATE'=> array(
			'lable' => 'ADDED_DATE',
			'is_default' => 'Y',
	  	),
      	'COMMISSION_ID'=> array(
			'lable' => 'COMMISSION_ID',
			'is_default' => 'Y',
	  	),
      	'GLOBAL_PRODUCT_ID'=> array(
			'lable' => 'GLOBAL_PRODUCT_ID',
			'is_default' => 'Y',
	  	),
      	'GLOBAL_PRODUCT_NAME'=> array(
			'lable' => 'GLOBAL_PRODUCT_NAME',
			'is_default' => 'Y',
	  	),
      	'ASSIGNED_AGENT'=> array(
			'lable' => 'ASSIGNED_AGENT',
			'is_default' => 'N',
	  	),
      	'COMMISSION_STATUS'=> array(
			'lable' => 'COMMISSION_STATUS',
			'is_default' => 'N',
	  	),
      	'COMMISSION_TYPE'=> array(
			'lable' => 'COMMISSION_TYPE',
			'is_default' => 'N',
	  	),
      	'NEW_BUSINESS_PAID'=> array(
			'lable' => 'NEW_BUSINESS_PAID',
			'is_default' => 'N',
	  	),
      	'RENEWALS_PAID'=> array(
			'lable' => 'RENEWALS_PAID',
			'is_default' => 'N',
	  	),
      	'COMMISSION_REVERSALS'=> array(
			'lable' => 'COMMISSION_REVERSALS',
			'is_default' => 'N',
	  	),
      	'REVERSALS_DAYS'=> array(
			'lable' => 'REVERSALS_DAYS',
			'is_default' => 'N',
	  	),
      	'BENEFIT_TIER'=> array(
			'lable' => 'BENEFIT_TIER',
			'is_default' => 'N',
	  	),
      	'RETAIL_PRICE'=> array(
			'lable' => 'RETAIL_PRICE',
			'is_default' => 'N',
	  	),
      	'NON_COMM_PRICE'=> array(
			'lable' => 'NON_COMM_PRICE',
			'is_default' => 'N',
	  	),
      	'COMM_PRICE'=> array(
			'lable' => 'COMM_PRICE',
			'is_default' => 'N',
	  	),
      	'COMMISSION_GROUPING_1'=> array(
			'lable' => 'COMMISSION GROUPING PERIOD 1',
			'is_default' => 'N',
	  	),
      	'COMMISSION_GROUPING_2'=> array(
			'lable' => 'COMMISSION GROUPING PERIOD 2',
			'is_default' => 'N',
	  	),
      	'COMMISSION_GROUPING_3'=> array(
			'lable' => 'COMMISSION GROUPING PERIOD 3',
			'is_default' => 'N',
	  	),
      	'COMMISSION_GROUPING_4'=> array(
			'lable' => 'COMMISSION GROUPING PERIOD 4',
			'is_default' => 'N',
	  	),
      	'COMMISSION_GROUPING_5'=> array(
			'lable' => 'COMMISSION GROUPING PERIOD 5',
			'is_default' => 'N',
	  	),
    );

	$field_arr['product_overview'] = array(
      'ADDED_DATE' => array(
            'lable' => 'ADDED_DATE',
            'field_name' => 'p.create_date',
            'field_type' => 'date',
            'field_table' => '',
            'is_default' => 'Y',

            ),
      'ADDED_BY' => array(
            'lable' => 'ADDED_BY',
            'field_name' => "CONCAT(a.fname,' ',a.lname,' (',a.display_id,')')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'Y',
            ),
      'PRODUCT_NAME' => array(
            'lable' => 'PRODUCT_NAME',
            'field_name' => 'p.name',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'Y',
            ),
      'PRODUCT_ID' => array(
            'lable' => 'PRODUCT_ID',
            'field_name' => 'p.product_code',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'Y',
            ),
      'ENROLLMENT_METHOD' => array(
            'lable' => 'ENROLLMENT_METHOD',
            'field_name' => 'p.product_type',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'PRODUCT_COMPANY' => array(
            'lable' => 'PRODUCT_COMPANY',
            'field_name' => 'cl.company_name',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'PRODUCT_CATEGORY' => array(
            'lable' => 'PRODUCT_CATEGORY',
            'field_name' => 'pc.title',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'PRODUCT_CARRIER' => array(
            'lable' => 'PRODUCT_CARRIER',
            'field_name' => "IF(pf.setting_type='Carrier',pf.name,'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'PRODUCT_TYPE' => array(
            'lable' => 'PRODUCT_TYPE',
            'field_name' => 'p.main_product_type',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'LIFE_PRODUCT' => array(
            'lable' => 'LIFE_PRODUCT',
            'field_name' => "IF(p.is_life_insurance_product='Y','Yes','No')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'LIFE_TERM' => array(
            'lable' => 'LIFE_TERM',
            'field_name' => 'p.life_term_type',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'GUARANTEE_ISSUE_PRIMARY' => array(
            'lable' => 'GUARANTEE_ISSUE_PRIMARY',
            'field_name' => 'p.primary_issue_amount',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'GUARANTEE_ISSUE_SPOUSE' => array(
            'lable' => 'GUARANTEE_ISSUE_SPOUSE',
            'field_name' => 'p.spouse_issue_amount',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'GUARANTEE_ISSUE_CHILD' => array(
            'lable' => 'GUARANTEE_ISSUE_CHILD',
            'field_name' => 'p.child_issue_amount',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'SPOUSE_GREATER_PRIMARY' => array(
            'lable' => 'SPOUSE_GREATER_PRIMARY',
            'field_name' => 'p.is_spouse_issue_amount_larger',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'EFFECTIVE_DATE' => array(
            'lable' => 'EFFECTIVE_DATE',
            'field_name' => 'p.direct_product',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'DATE_SELECTED' => array(
            'lable' => 'DATE_SELECTED',
            'field_name' => "IF(p.direct_product='Select Day Of Month',p.effective_day,'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'SOLD_UNTIL' => array(
            'lable' => 'SOLD_UNTIL',
            'field_name' => "IF(p.direct_product!='Next day',IF(p.sold_day>1,CONCAT(p.sold_day,' ','day prior to effective day'),CONCAT(p.sold_day,' ','days prior to effective day')),'-')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'MEMBERSHIP' => array(
            'lable' => 'MEMBERSHIP',
            'field_name' => "IF(p.membership_ids IS NOT NULL,(SELECT GROUP_CONCAT(DISTINCT(NAME)) FROM prd_fees WHERE FIND_IN_SET(id ,p.membership_ids) AND is_deleted='N'),'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'NO_SALE_STATES' => array(
            'lable' => 'NO_SALE_STATES',
            'field_name' => 'GROUP_CONCAT(DISTINCT(sc.short_name) ORDER BY sc.short_name ASC)',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'MOVE_COVERAGE_CONTINUE' => array(
            'lable' => 'MOVE_COVERAGE_CONTINUE',
            'field_name' => "IF(p.no_sale_state_coverage_continue='Y','Yes','No')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),  
      'FAMILY_REQUIREMENT' => array(
            'lable' => 'FAMILY_REQUIREMENT',
            'field_name' => 'p.family_plan_rule',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'SUB_PRODUCTS' => array(
            'lable' => 'SUB_PRODUCTS',
            'field_name' => "GROUP_CONCAT(DISTINCT(CONCAT(sp.product_name,' (',sp.product_code,')')))",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            ),
      'COMBO_AUTO_ASSIGN' => array(
            'lable' => 'COMBO_AUTO_ASSIGN',
            'field_name' => 'pc1.AutoAssignedCombo',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'COMBO_PKG' => array(
            'lable' => 'COMBO_PKG',
            'field_name' => 'pc1.PackagedCombo',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'COMBO_EXCLUDES' => array(
            'lable' => 'COMBO_EXCLUDES',
            'field_name' => 'pc1.ExcludesCombo',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'COMBO_REQUIRED' => array(
            'lable' => 'COMBO_REQUIRED',
            'field_name' => 'pc1.RequiredCombo',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'TERM_BACK_EFFECTIVE' => array(
            'lable' => 'TERM_BACK_EFFECTIVE',
            'field_name' => "IF(p.term_back_to_effective='Y','Yes','No')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'AUTO_TERM' => array(
            'lable' => 'AUTO_TERM',
            'field_name' => "IF(p.term_automatically='Y','Yes','No')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'REINSTATE_OPTION' => array(
            'lable' => 'REINSTATE_OPTION',
            'field_name' => 'p.reinstate_option',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'REINSTATE_TIMEFRAME' => array(
            'lable' => 'REINSTATE_TIMEFRAME',
            'field_name' => "CONCAT(p.reinstate_within,' ',p.reinstate_within_type)",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'REENROLL_OPTION' => array(
            'lable' => 'REENROLL_OPTION',
            'field_name' => 'p.reenroll_options',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'REENROLL_TIMEFRAME' => array(
            'lable' => 'REENROLL_TIMEFRAME',
            'field_name' => "CONCAT(p.reenroll_within,' ',p.reenroll_within_type)",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'PRIMARY_AGE' => array(
            'lable' => 'PRIMARY_AGE',
            'field_name' => "IF(p.is_primary_age_restrictions='Y',CONCAT(p.primary_age_restrictions_from,'-',p.primary_age_restrictions_to),'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'PRIMARY_AUTO_TERM' => array(
            'lable' => 'PRIMARY_AUTO_TERM',
            'field_name' => 'pc5.p_auto_term',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'SPOUSE_AGE' => array(
            'lable' => 'SPOUSE_AGE',
            'field_name' => "IF(p.is_spouse_age_restrictions='Y',CONCAT(p.spouse_age_restrictions_from,'-',p.spouse_age_restrictions_to),'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'SPOUSE_AUTO_TERM' => array(
            'lable' => 'SPOUSE_AUTO_TERM',
            'field_name' => 'pc5.s_auto_term',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'CHILD_AGE' => array(
            'lable' => 'CHILD_AGE',
            'field_name' => "IF(p.is_children_age_restrictions='Y',CONCAT(p.children_age_restrictions_from,'-',p.children_age_restrictions_to),'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'CHILD_AUTO_TERM' => array(
            'lable' => 'CHILD_AUTO_TERM',
            'field_name' => 'pc5.c_auto_term',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'SSN_REQUIRED' => array(
            'lable' => 'SSN_REQUIRED',
            'field_name' => "TRIM(TRAILING ',' FROM (CONCAT(COALESCE(CONCAT(pc2.SSN_Required,','),''),COALESCE(CONCAT(pc2.SSN_Required1,','),''),COALESCE(CONCAT(pc2.SSN_Required2,','),''))))",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'MEMBER_DETAILS_ASKED' => array(
            'lable' => 'MEMBER_DETAILS_ASKED',
            'field_name' => 'pc2.MemberAsked',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'SPOUSE_DETAILS_ASKED' => array(
            'lable' => 'SPOUSE_DETAILS_ASKED',
            'field_name' => 'pc2.SpouseAsked',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'CHILD_DETAILS_ASKED' => array(
            'lable' => 'CHILD_DETAILS_ASKED',
            'field_name' => 'pc2.ChildAsked',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'BENEFICIARY_REQUIRED' => array(
            'lable' => 'BENEFICIARY_REQUIRED',
            'field_name' => "IF(p.is_beneficiary_required='Y','Yes','No')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'PRINCIPAL_BENEFICIARY_DETAILS' => array(
            'lable' => 'PRINCIPAL_BENEFICIARY_DETAILS',
            'field_name' => "IF(p.is_beneficiary_required = 'Y',pc3.principal_ben_det,'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'CONTINGENT_BENEFICIARY_DETAILS' => array(
            'lable' => 'CONTINGENT_BENEFICIARY_DETAILS',
            'field_name' => "IF(p.is_beneficiary_required = 'Y',pc3.continget_ben_det,'')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'VERIFICATION_METHOD' => array(
            'lable' => 'VERIFICATION_METHOD',
            'field_name' => "GROUP_CONCAT(DISTINCT(pev.verification_type))",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'LICENSE_REQUIRED' => array(
            'lable' => 'LICENSE_REQUIRED',
            'field_name' => "IF(p.is_license_require='Y','Required','')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'LICENSE_TYPE' => array(
            'lable' => 'LICENSE_TYPE',
            'field_name' => 'p.license_type',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'LICENSE_RULES' => array(
            'lable' => 'LICENSE_RULES',
            'field_name' => 'p.license_rule',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'SUBSCRIPTION_TYPE' => array(
            'lable' => 'SUBSCRIPTION_TYPE',
            'field_name' => 'p.payment_type_subscription',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'PRICING_MODEL' => array(
            'lable' => 'PRICING_MODEL',
            'field_name' => 'p.pricing_model',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'BENEFIT_TIER' => array(
            'lable' => 'BENEFIT_TIER',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'RETAIL_PRICE' => array(
            'lable' => 'RETAIL_PRICE',
            'field_name' => 'pm.price',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'NON_COMM_PRICE' => array(
            'lable' => 'NON_COMM_PRICE',
            'field_name' => 'pm.non_commission_amount',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'COMMISSIONABLE_PRICE' => array(
            'lable' => 'COMMISSIONABLE_PRICE',
            'field_name' => 'pm.commission_amount',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'AGE' => array(
            'lable' => 'AGE',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'GENDER' => array(
            'lable' => 'GENDER',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'HEIGHT' => array(
            'lable' => 'HEIGHT',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'WEIGHT' => array(
            'lable' => 'WEIGHT',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'HAS_SPOUSE' => array(
            'lable' => 'HAS_SPOUSE',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'STATE' => array(
            'lable' => 'STATE',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'SMOKING' => array(
            'lable' => 'SMOKING',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'TOBACCO' => array(
            'lable' => 'TOBACCO',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'BENEFIT_AMOUNT' => array(
            'lable' => 'BENEFIT_AMOUNT',
            'field_name' => '',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'IN_PATIENT_BENEFIT' => array(
            'lable' => 'IN_PATIENT_BENEFIT',
            'field_name' => '',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'OUT_PATIENT_BENEFIT' => array(
            'lable' => 'OUT_PATIENT_BENEFIT',
            'field_name' => '',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'MONTHLY_INCOME' => array(
            'lable' => 'MONTHLY_INCOME',
            'field_name' => '',
            'field_type' => 'amount',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),      
      'ZIP_CODE' => array(
            'lable' => 'ZIP_CODE',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'NO_OF_CHILDREN' => array(
            'lable' => 'NO_OF_CHILDREN',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PRODUCT_FEES' => array(
            'lable' => 'PRODUCT_FEES',
            'field_name' => "IF(GROUP_CONCAT(distinct(prdFee.id)) IS NOT NULL,'Yes','')",
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'PARENT_PRODUCT' => array(
            'lable' => 'PARENT_PRODUCT',
            'field_name' => 'pv.product_code',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'STATUS' => array(
            'lable' => 'STATUS',
            'field_name' => 'p.status',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '1',
            ),
      'GROUP_CODE' => array(
            'lable' => 'GROUP_CODE',
            'field_name' => 'pc4.group_code',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '2',
            ),
      'PLAN_CODE1' => array(
            'lable' => 'PLAN_CODE1',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE2' => array(
            'lable' => 'PLAN_CODE2',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE3' => array(
            'lable' => 'PLAN_CODE3',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE4' => array(
            'lable' => 'PLAN_CODE4',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE5' => array(
            'lable' => 'PLAN_CODE5',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE6' => array(
            'lable' => 'PLAN_CODE6',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE7' => array(
            'lable' => 'PLAN_CODE7',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE8' => array(
            'lable' => 'PLAN_CODE8',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE9' => array(
            'lable' => 'PLAN_CODE9',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
      'PLAN_CODE10' => array(
            'lable' => 'PLAN_CODE10',
            'field_name' => '',
            'field_type' => 'text',
            'field_table' => '',
            'is_default' => 'N',
            'level' => '3',
            ),
    );
	return $field_arr[$report_key];
  }
}