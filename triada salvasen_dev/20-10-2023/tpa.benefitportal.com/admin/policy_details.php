<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
$MemberEnrollment = new MemberEnrollment();
$function_list = new functionsList();

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[2]['title'] = 'Plan Detail';
$breadcrumbes[2]['link'] = 'policy_details.php';

$ws_id = isset($_GET['ws_id']) ? $_GET['ws_id'] : "";

if(empty($ws_id)){
	redirect('payment_policies.php');
}

$ws_row = $pdo->selectOne("SELECT w.*,IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,CONCAT(c.fname,' ',c.lname) as member_name,c.rep_id,ppt.title as benefit_tier,w.end_coverage_period,w.start_coverage_period,p.product_type as p_type,c.sponsor_id,p.main_product_type
 						   FROM website_subscriptions w 
 						   join prd_main p on(p.id = w.product_id)
 						   JOIN customer c on(c.id = w.customer_id)
 						   JOIN prd_matrix pm ON(pm.id=w.plan_id OR FIND_IN_SET(pm.id,w.plan_id)) 
 						   left JOIN prd_plan_type ppt ON (w.prd_plan_type_id = ppt.id)
 						   where md5(w.id) = :id",array(':id' => $ws_id));
$allow_cancel_termination = true;
$max_termination_date = '';
if($ws_row['main_product_type'] == "Core Product" && !empty($ws_row['termination_date'])) {
    $core_prd_termination_data = $MemberEnrollment->check_allow_cancel_core_prd_termination($ws_row['id'],$ws_row['customer_id'],$ws_row['termination_date']);
    $allow_cancel_termination = $core_prd_termination_data['allow_cancel_termination'];
    $max_termination_date = $core_prd_termination_data['max_termination_date'];
}

$earliest_effective_date = '';
if($ws_row['main_product_type'] == "Core Product") {
    $earliest_effective_date = $MemberEnrollment->get_core_prd_earliest_effective_date($ws_row['id']);
}

$effective_date = strtotime($ws_row['eligibility_date']) > 0 ? displayDate($ws_row['eligibility_date']) : "";
$termination_date = strtotime($ws_row['termination_date']) > 0 ? displayDate($ws_row['termination_date']) : "";
$date_terminated = strtotime($ws_row['term_date_set']) > 0 ? displayDate($ws_row['term_date_set']) : "";

$is_allow_next_billing = true;
if($termination_date && strtotime($termination_date) <= strtotime(date('m/d/Y'))){
	$is_allow_next_billing = false;
	$ws_row['next_purchase_date'] = "";
}
$effective_date_enable = true;
if($termination_date && $ws_row['p_type'] != 'Healthy Step'){
	$effective_date_enable = false;
}

$next_purchase_date = strtotime($ws_row['next_purchase_date']) > 0 ? displayDate($ws_row['next_purchase_date']) : "";

$dependents = $pdo->select("SELECT cd.*,CONCAT(cdp.fname,' ',cdp.lname) as dependent_name 
                  FROM customer_dependent cd
                  JOIN customer_dependent_profile cdp on(cdp.id = cd.cd_profile_id)
                  WHERE cd.website_id=:website_id AND cd.is_deleted = 'N' GROUP BY cdp.id", array(":website_id" => $ws_row['id']));

$coverage_periods = $pdo->select("SELECT od.id,od.start_coverage_period,od.end_coverage_period,od.renew_count,o.display_id,o.status,o.created_at 
	FROM order_details od
	JOIN orders o on(o.id = od.order_id)
	WHERE od.website_id = :id AND o.status NOT IN('Pending Validation') AND od.is_deleted='N' GROUP BY od.id
	",array(':id' => $ws_row['id']));

$renew_count_limit = 1;
if(!empty($coverage_periods)) {
  foreach ($coverage_periods as $coverage_period) {
      if($coverage_period['renew_count'] >= $renew_count_limit) {
          $renew_count_limit = $coverage_period['renew_count'];
      }
  }
}

$last_coverage = 0;

$term_reasons = $pdo->select("SELECT name FROM termination_reason WHERE is_deleted ='N' ORDER BY name ASC");

$MemberEnrollment = new MemberEnrollment();
$extra = array();
$product_id = $ws_row['product_id'];
$coverage_period_data = $MemberEnrollment->get_coverage_period(array($product_id),$ws_row["sponsor_id"],$extra);
$coverage_period_data = (isset($coverage_period_data[$product_id])?$coverage_period_data[$product_id]:array());

$disabledDates = !empty($coverage_period_data['datesDisabled']) ? $coverage_period_data['datesDisabled'] : array();
$disableDays = array();
if(!empty($disabledDates)){
    foreach ($disabledDates as $value) {
        $day = date("d",strtotime($value));
        if(!in_array($day, $disableDays)){
            array_push($disableDays, $day);
        }
    }
}

$is_list_bill = false;
$check_sponser = $pdo->selectOne("SELECT cs.billing_type FROM customer s
	    JOIN `customer_group_settings` cs ON (s.id = cs.customer_id)
	    WHERE s.type='Group' AND s.id = :sponser_id",array(":sponser_id"=>$ws_row["sponsor_id"]));	
if(!empty($check_sponser) && checkIsset($check_sponser['billing_type']) == 'list_bill'){
	$is_list_bill = true;
}

// check if joinder agreement generated for policy
$resAgreement = $function_list->getProductJoinderAgreement($ws_row["customer_id"],$ws_row["product_id"]);

$page_title = "Plan Detail";
$template = 'policy_details.inc.php';
include_once 'layout/end.inc.php';
?>