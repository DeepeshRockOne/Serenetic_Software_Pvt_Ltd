<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/reports.php';

$reports_class = new Report();

$report_id = isset($_GET['id']) ? $_GET['id'] : "";
$report_row = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)=:id",array(':id' => $report_id));
$filter_fields = array();

if($report_row['report_key'] == 'admin_export') { //Admin Export
	$fl_sql = "SELECT id,display_id,fname,lname FROM admin WHERE is_deleted = 'N' ORDER BY fname,lname";
	$fl_res = $pdo->select($fl_sql);

} else if($report_row['report_key'] == 'admin_history') { //Admin History
	$fl_sql = "SELECT id,display_id,fname,lname FROM admin WHERE is_deleted = 'N' ORDER BY fname,lname";
	$fl_res = $pdo->select($fl_sql);
	
} else if($report_row['report_key'] == 'agent_export') { //Agent Export OR Agent Summary
	$fields = $reports_class->getfields('agent_export');
	$fl_sql="SELECT c.id,if(cs.company_name is not null,cs.company_name,scs.company_name) as company_name,c.fname,c.lname,c.rep_id 
			FROM customer c 
			LEFT JOIN customer_settings cs ON(c.id=cs.customer_id)
			LEFT JOIN customer s ON(if(c.sponsor_id=0,c.id=s.id,c.sponsor_id=s.id) and s.is_deleted='N') 
			LEFT JOIN customer_settings scs ON(s.id=scs.customer_id)
			WHERE c.type='Agent' GROUP BY scs.company_name,c.id ORDER BY company_name";
	$fl_res = $pdo->selectGroup($fl_sql,array(),'company_name');
} else if($report_row['report_key'] == 'agent_history') { //Agent History
	$fl_sql="SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' ORDER BY fname,lname";
	$fl_res = $pdo->select($fl_sql,array());
} else if($report_row['report_key'] == 'agent_license') { //Agent License
	$fl_sql="SELECT c.id,if(cs.company_name is not null,cs.company_name,scs.company_name) as company_name,c.fname,c.lname,c.rep_id 
			FROM customer c 
			LEFT JOIN customer_settings cs ON(c.id=cs.customer_id)
			LEFT JOIN customer s ON(if(c.sponsor_id=0,c.id=s.id,c.sponsor_id=s.id) and s.is_deleted='N') 
			LEFT JOIN customer_settings scs ON(s.id=scs.customer_id)
			WHERE c.type='Agent' GROUP BY scs.company_name,c.id ORDER BY company_name";
	$fl_res = $pdo->selectGroup($fl_sql,array(),'company_name');
} else if($report_row['report_key'] == 'agent_merchant_assignment') { //Agent Merchant Assignment
	$fl_sql="SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' ORDER BY fname,lname";
	$fl_res = $pdo->select($fl_sql,array());
	$fl_merchant_sql = "SELECT id,name from payment_master where is_deleted='N'";
	$fl_merchant_res = $pdo->select($fl_merchant_sql,array());
} else if($report_row['report_key'] == 'agent_eo_coverage') { //Agent EO Coverage
	$fl_sql="SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' ORDER BY fname,lname";
	$fl_res = $pdo->select($fl_sql,array());
} else if($report_row['report_key'] == 'agent_interactions') { //Agent Interaction
	$fl_sql="SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' ORDER BY fname,lname";
	$fl_res = $pdo->select($fl_sql,array());
	$fl_interaction_sql = "SELECT type,id from interaction where user_type='Agent' and is_deleted='N' ORDER BY type ASC";
	$fl_interaction_res = $pdo->select($fl_interaction_sql,array());

} else if($report_row['report_key'] == 'payables_export') { //Agent Interaction
	$fields = $reports_class->getfields('payables_export');
	$fl_productRes = get_active_global_products_for_filter();
	$fl_payee_sql = "SELECT 
					IF(pd.payee_type='Agent',ag.rep_id,pf.display_id) as PAYEE_ID,
                    IF(pd.commission_id > 0,CONCAT(ag.fname,' ',ag.lname),pf.name) as PAYEE
					FROM payable py 
                    JOIN payable_details pd ON(pd.payable_id = py.id)
                    LEFT JOIN prd_fees pf ON(pf.id=pd.payee_id AND pf.is_deleted='N')
                    LEFT JOIN customer ag ON (ag.id = pd.payee_id AND pd.payee_type='Agent')
					WHERE 1 GROUP BY PAYEE_ID ORDER BY PAYEE ASC";
	$fl_payee_res = $pdo->select($fl_payee_sql);
} else if($report_row['report_key'] == 'list_bill_overview') { //Listbill Overview

	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());

} else if($report_row['report_key'] == 'admin_next_billing_date') { //Admin Area Next Billing Date

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());

} else if($report_row['report_key'] == 'admin_new_business_post_payments_org') {

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
	
} else if($report_row['report_key'] == 'admin_payment_outstanding_renewals') {

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());

} else if(in_array($report_row['report_key'],array('admin_payment_transaction_report','admin_payment_failed_payment_recapture_analytics','admin_payment_reversal_transactions','admin_payment_p2p_renewal_comparison'))) {
	
	$fields = $reports_class->getfields('admin_payment_transaction_report');
	$fl_productRes = get_active_global_products_for_filter();
	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
} else if(in_array($report_row['report_key'] ,array("payment_policy_overview","member_summary"))) { //Policy Overview And PCI/NonPCI Member Summary
	$fields = $reports_class->getfields('payment_policy_overview');
	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
	$fl_productRes = get_active_global_products_for_filter();

} else if(in_array($report_row['report_key'],array('member_verifications','member_paid_through'))) { //Member Verification And Member Policy Paid Through

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
	$fl_productRes = get_active_global_products_for_filter();

} else if($report_row['report_key'] == 'member_age_out') { //Member Age Out

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
	$fl_productRes = get_active_global_products_for_filter();

} else if($report_row['report_key'] == 'member_history') { //Member History

	// $fl_sql="SELECT c.id,c.fname,c.lname,c.rep_id 
	// 		FROM customer c WHERE c.type='Customer' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending') AND c.is_deleted='N' ORDER BY fname,lname";
	// $fl_res = $pdo->select($fl_sql,array());

} else if($report_row['report_key'] == 'member_interactions') { //Member Interaction

	// $fl_sql="SELECT c.id,c.fname,c.lname,c.rep_id 
	// 		FROM customer c WHERE c.type='customer' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending') AND c.is_deleted='N' ORDER BY fname,lname";
	// $fl_res = $pdo->select($fl_sql,array());
	$fl_interaction_sql = "SELECT type,id from interaction where user_type='member' and is_deleted='N' ORDER BY type ASC";
	$fl_interaction_res = $pdo->select($fl_interaction_sql,array());

} else if($report_row['report_key'] == 'member_product_cancellations'){//Member Product Cancellation report

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
	$fl_productRes = get_active_global_products_for_filter();

} else if($report_row['report_key'] == 'admin_member_persistency'){//Member Product Cancellation report

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
}else if($report_row['report_key'] == 'life_insurance_beneficiaries'){//Life Insurance Beneficiaries

	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
	$fl_productRes = get_active_global_products_for_filter();
	
}else if($report_row['report_key'] == 'advance_funding'){//Advance Funding

	// $fl_sql_comm = "SELECT cs.pay_period as payPeriod FROM commission cs WHERE cs.commission_duration='weekly' AND cs.is_deleted='N' GROUP BY cs.pay_period ORDER BY cs.pay_period DESC";
	// $fl_res_comm = $pdo->select($fl_sql_comm,array());
	
}else if($report_row['report_key'] == 'advance_collection'){//Advance Collection

	// $fl_sql_comm = "SELECT cs.pay_period as payPeriod FROM commission cs WHERE cs.commission_duration='monthly' AND cs.is_deleted='N' GROUP BY cs.pay_period ORDER BY cs.pay_period DESC";
	// $fl_res_comm = $pdo->select($fl_sql_comm,array());
	
}else if($report_row['report_key'] == 'commission_setup'){//Commission Setup
	$fields = $reports_class->getfields('commission_setup');
	$productSearchList=array();

	$productSearchList = get_active_global_products_for_filter(0,true,true);

	$resCommRules = $pdo->select("SELECT pc.title,cr.id,cr.rule_code,p.name
      FROM commission_rule cr 
	  JOIN prd_main p ON(p.id=cr.product_id and p.is_deleted='N' AND p.parent_product_id=0)
	  JOIN prd_category pc ON(pc.id=p.category_id and pc.is_deleted='N')
      WHERE cr.parent_rule_id=0 AND cr.is_deleted='N' 
      GROUP BY cr.id  
	  ORDER BY cr.id DESC");
}else if(in_array($report_row['report_key'],array('admin_agent_debit_balance','admin_agent_debit_ledger','debit_balance_overview'))){//Agent Debit Balance
	
	$resCommRules = $pdo->select("SELECT c.id as id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name
      FROM  customer c
	  JOIN customer_settings cs ON(cs.customer_id=c.id)
      WHERE cs.agent_coded_id!=1 AND c.is_deleted='N' AND c.type='agent'
	  ORDER BY c.id DESC");

}elseif($report_row['report_key'] == 'admin_product_persistency'){ //Product Persistency
	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
	$fl_productRes = get_active_global_products_for_filter();
}elseif($report_row['report_key'] == 'product_overview'){ //Product Overview
	$fields = $reports_class->getfields('product_overview');
	$fl_productRes = get_active_global_products_for_filter();
}elseif(in_array($report_row['report_key'],array('carrier_overview','membership_overview','vendor_overview'))){ //Product Carrier/Membership/Vendor Overview
	$label = $settingTypeIncr = '';
	$carrArr = array();
	if($report_row['report_key'] == 'carrier_overview'){
		$settingTypeIncr = " pf.setting_type='Carrier' ";
		$label = 'Carrier';
	}else if($report_row['report_key'] == 'membership_overview'){
		$settingTypeIncr = " pf.setting_type='membership' ";
		$label = 'Membership';
	}else if($report_row['report_key'] == 'vendor_overview'){
		$settingTypeIncr = " pf.setting_type='Vendor' ";
		$label = 'Vendor';
	}
	if(!empty($settingTypeIncr)){
		$selCarrier = "SELECT pf.id,pf.display_id,pf.name
		FROM prd_fees pf
		LEFT JOIN prd_assign_fees pa ON (pa.prd_fee_id=pf.id AND pa.is_deleted='N')
		LEFT JOIN prd_main p ON(pa.product_id = p.id AND p.carrier_id = pa.prd_fee_id AND p.is_deleted = 'N')
		WHERE $settingTypeIncr AND pf.is_deleted='N' 
		GROUP BY pf.id ";
		$carrArr = $pdo->select($selCarrier);
	}	
}else if($report_row['report_key'] == 'lead_summary'){
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
	
	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());

}else if($report_row['report_key'] == 'group_summary'){
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
}else if($report_row['report_key'] == 'group_interactions'){
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
	$fl_interaction_sql = "SELECT type,id from interaction where user_type='Group' and is_deleted='N' ORDER BY type ASC";
	$fl_interaction_res = $pdo->select($fl_interaction_sql,array());
}else if($report_row['report_key'] == 'group_history'){
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
}elseif($report_row['report_key'] == 'payables_reconciliation'){ //Product Persistency
	$fl_productRes = get_active_global_products_for_filter();
}else if($report_row['report_key'] == 'payment_nb_sales' || $report_row['report_key'] == 'payment_rb_sales'){//Payment new Business or Renewal Business Sale
	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());
}elseif($report_row['report_key'] == 'group_full_coverage'){
	$fl_productRes = get_active_global_products_for_filter(0,false,false,true);
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
}elseif($report_row['report_key'] == 'group_enroll_overview'){
	$fl_productRes = get_active_global_products_for_filter(0,false,false,true);
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
}elseif($report_row['report_key'] == 'group_member_age_out'){
	$fl_productRes = get_active_global_products_for_filter(0,false,false,true);
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
}elseif($report_row['report_key'] == 'group_change_product'){
	$fl_productRes = get_active_global_products_for_filter(0,false,false,true);
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
	FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
}else if($report_row['report_key'] == 'participants_summary'){
	$fl_sql_group = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Group' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_group = $pdo->select($fl_sql_group,array());
	
	$fl_sql_agent = "SELECT c.id,c.fname,c.lname,c.rep_id 
			FROM customer c WHERE c.type='Agent' AND c.is_deleted='N' ORDER BY fname,lname";
	$fl_res_agent = $pdo->select($fl_sql_agent,array());

}else if($report_row['report_key'] == 'eticket_overview'){
}else if($report_row['report_key'] == 'eticket_script'){
	$fl_sql_Eticket = "SELECT id,tracking_id from s_ticket ORDER BY id desc";
	$fl_res_Eticket = $pdo->select($fl_sql_Eticket,array());
}
$selected_columns = array();
$setting_selected_columns = array();
if(!empty($fields)) {
	$selected_columns = array_keys($fields);
	$setting_row = $reports_class->get_rps_user_report_settings($_SESSION['admin']['id'],'Admin',$report_row['id']);
	if(!empty($setting_row['selected_columns'])) {
		$setting_selected_columns = $selected_columns = explode(',',$setting_row['selected_columns']);
	}
}

function generateDateRange($label = '',$id=''){
	if($label == '') {
		$label = 'Added Date';
	}
	$id_label = '';
	if($id !=''){
		$id_label = $id.'_';
	}
echo '<div id="date_range" class="col-xs-4">
	       <div class="form-group height_auto pn">
	          <select class="form-control" id="'.$id_label.'join_range" name="'.$id_label.'join_range">
        			<option></option>
		            <option value="Exactly">Exactly</option>
		            <option value="Before">Before</option>
		            <option value="After">After</option>
		            <option value="Range">Range</option>
	          </select>
	          <label>'.$label.'</label>
	          <p class="error"><span class="error_'.$id_label.'join_range"></span></p>
	       </div>
	    </div>
	    <div class="select_date_div col-xs-8" >
	       	<div class="form-group height_auto pn">
				<div id="'.$id_label.'all_join">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input type="text" name="'.$id_label.'added_date" id="'.$id_label.'added_date" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
						</div>
					</div>
					<p class="error text-left"><span class="error_'.$id_label.'added_date"></span></p>
				</div>

				<div  id="'.$id_label.'range_join" style="display:none;">
				 	<div class="phone-control-wrap">
					    <div class="phone-addon">
					       <label class="mn">From</label>
					    </div>
					    <div class="phone-addon">
					       <div class="input-group">
					          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					          <div class="pr">
					             <input type="text" name="'.$id_label.'fromdate" id="'.$id_label.'fromdate" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
					          </div>
					       </div>
					    	<p class="error text-left"><span class="error_'.$id_label.'fromdate"></span></p>
					    </div>

					    <div class="phone-addon">
					       <label class="mn">To</label>
					    </div>
					    <div class="phone-addon">
					       <div class="input-group">
					          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					          <div class="pr">
					             <input type="text" name="'.$id_label.'todate" id="'.$id_label.'todate" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
					          </div>
					       </div>
					    	<p class="error text-left"><span class="error_'.$id_label.'todate"></span></p>
					    </div>
				 	</div>
				</div>
	       	</div>
	    </div>';
}
$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'.$cache,
	'thirdparty/lou-multi-select/css/multi-select.css'.$cache

);
$exJs = array(
	'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
	'thirdparty/lou-multi-select/js/jquery.multi-select.js'.$cache,
);
if($report_row['report_key'] == 'member_interactions'){
	$exStylesheets[] = 'thirdparty/selectize.js/css/selectize.default.css'.$cache;
	$exJs[] = 'thirdparty/selectize.js/js/standalone/selectize.js'.$cache;
}
if($report_row['report_key'] == 'member_history'){
 	$exStylesheets[] = 'thirdparty/selectize.js/css/selectize.default.css'.$cache;
 	$exJs[] = 'thirdparty/selectize.js/js/standalone/selectize.js'.$cache;
}

$template = "generate_report.inc.php";
include_once 'layout/iframe.layout.php';
?>