<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(64);


$pmpm_id = (isset($_GET['pmpm_id']) ? $_GET['pmpm_id'] : 0);
$pmpm_fee_id = isset($_GET['pmpm_fee_id']) ? $_GET['pmpm_fee_id'] : "";
$is_clone = (isset($_GET['is_clone']) && $_GET['is_clone'] == 'Y') ? 'Y' : 'N';

$productSql="SELECT p.id,p.name,p.product_code,p.type,c.title 
            FROM prd_main p 
            LEFT JOIN prd_category c ON (c.id = p.category_id)
            LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
            WHERE p.type!='Fees' AND p.name != '' AND p.is_deleted='N' AND p.record_type='Primary' AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]')
            ORDER BY name ASC";
$productRes = $pdo->selectGroup($productSql,array(),'title');

$agents = $pdo->select("SELECT id,rep_id,CONCAT(fname,' ',lname) as agent_name,email FROM customer where type = 'agent'");

$agent_res = array();
$display_id = get_pmpm_comm_fee_id();
$is_included='N';
$is_earned_on_new_business='N';
$is_fee_on_renewal='N';
$fee_renewal_type='Continuous';
$is_benefit_tier='N';
$fee_type='Amount';
$percentage_type='Retail';
$priceArr = array();
$fee_renewal_count = 0;
$receiving_agents = array();
$product_ids = array();
$effective_date = "";
$termination_date = "";

if($pmpm_fee_id > 0){
	$fee_details = $pdo->selectOne("SELECT pcr.*,count(pcrap.product_id) as total_products,count(pcraa.agent_id) as total_agents,pcrpt.amount 
        FROM pmpm_commission_rule pcr
        JOIN pmpm_commission_rule_plan_type pcrpt on (pcr.id = pcrpt.rule_id AND pcrpt.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_product pcrap on (pcr.id = pcrap.rule_id AND pcrap.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_agent pcraa on (pcr.id = pcraa.rule_id AND pcraa.is_deleted = 'N')
        WHERE pcr.id = :fee_id AND pcr.is_deleted='N' group by pcr.id",array(':fee_id' => $pmpm_fee_id));

	if($fee_details){
		$effective_date = date('m/d/Y',strtotime($fee_details['effective_date']));
		$termination_date = $fee_details['termination_date'] ? date('m/d/Y',strtotime($fee_details['termination_date'])) : "";
		$display_id = $fee_details['display_id'];
		$is_earned_on_new_business=$fee_details['earned_on_new_business'];
		$is_fee_on_renewal=$fee_details['earned_on_renewal'];
		$fee_renewal_type=$fee_details['fee_renewal_type'];
		$is_benefit_tier=$fee_details['is_fee_by_benefit_tier'];
		$fee_type=$fee_details['amount_calculated_on'];
		$percentage_type=$fee_details['fee_per_calculate_on'];
		$fee_renewal_count=$fee_details['number_of_renewals'];
		if($is_clone == 'Y'){
			$display_id = get_pmpm_comm_fee_id();
		}

		$planArr = $pdo->select("SELECT plan_type,amount FROM pmpm_commission_rule_plan_type WHERE rule_id = :rule_id AND is_deleted = 'N'",array(':rule_id' => $fee_details['id']));
		if($planArr){
			if($is_benefit_tier == 'Y'){
				foreach ($planArr as $key => $value) {
					$priceArr[$value['plan_type']] = $value['amount'];
				}
			}else{
				$fee_price = $planArr[0]['amount'];
			}
		}

		$select_receiving_agents = $pdo->select("SELECT pcraa.agent_id,pcraa.include_loa,pcraa.include_downline,c.rep_id,c.email,CONCAT(c.fname,' ',c.lname) as agent_name 
			FROM pmpm_commission_rule_assign_agent pcraa 
			JOIN customer c on (pcraa.agent_id = c.id)
			WHERE pcraa.rule_id =:rule_id AND pcraa.is_deleted = 'N'",array(':rule_id' => $fee_details['id']));

		if($select_receiving_agents){
			foreach ($select_receiving_agents as $key => $value) {
				$receiving_agents[$value['agent_id']] = $value;
			}
		}

		$products = $pdo->selectOne("SELECT GROUP_CONCAT(',',product_id) as product_ids FROM pmpm_commission_rule_assign_product WHERE rule_id = :rule_id AND is_deleted = 'N'", array(':rule_id' => $fee_details['id']));
		if($products){
			$product_ids = explode(',', $products['product_ids']);
		}
	}
}


$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/bootstrap-datepicker-master/css/datepicker.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js', 'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js', 'thirdparty/price_format/jquery.price_format.2.0.js');

$template = 'add_pmpm_fee.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
