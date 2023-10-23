<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Dashboard';

$group_id = $_SESSION['groups']['id'];
$sqlGroup = "SELECT c.business_name,cs.display_group_welcome FROM customer c 
			JOIN customer_settings cs ON (c.id = cs.customer_id)
			WHERE c.id=:group_id";
$resGroup = $pdo->selectOne($sqlGroup,array(":group_id"=>$group_id));
$group_name = '';
$display_group_welcome = 'N';

if(!empty($resGroup)){
	$group_name = $resGroup['business_name'];
	$display_group_welcome = $resGroup['display_group_welcome'];
}

//********************** Enrollment Website Code Start ********************** 
	$website_sql= "SELECT pb.id,pb.page_name,pb.user_name FROM page_builder pb WHERE pb.is_deleted = 'N' AND pb.status='Active' AND pb.agent_id=:group_id";
	$website_res= $pdo->select($website_sql,array(":group_id"=>$group_id));

//********************** Enrollment Website Code End   ********************** 

//********************** Enrollee Code Start **********************
	$enrollee_sql = "SELECT l.id,l.lead_id,l.fname,l.lname,l.created_at,gcp.coverage_period_name,md5(l.id) as secured_id FROM leads l 
					LEFT JOIN group_coverage_period gcp ON (l.group_coverage_id = gcp.id)
					WHERE l.is_deleted = 'N' AND l.status != 'Converted' AND l.sponsor_id=:group_id";
	$enrollee_res = $pdo->select($enrollee_sql,array(":group_id"=>$group_id));
//********************** Enrollee Code End   **********************

//********************** Billing Code Start **********************
	$billing_type = '';
	$groupSettingSql = "SELECT billing_type FROM customer_group_settings WHERE customer_id=:group_id";
	$groupSettingRes = $pdo->selectOne($groupSettingSql,array(":group_id"=>$group_id));

	if(!empty($groupSettingRes)){
		$billing_type = $groupSettingRes['billing_type'];
	}
	$current_amount = 0;
	$due_date = '';
	if($billing_type == 'list_bill'){
		$sqlListBill = "SELECT SUM(due_amount) as current_amount,due_date FROM list_bills WHERE status='open' AND is_deleted ='N' AND customer_id=:group_id";
		$resListBill = $pdo->selectOne($sqlListBill,array(":group_id"=>$group_id));

		if(!empty($resListBill)){
			$current_amount = !empty($resListBill['current_amount']) ? $resListBill['current_amount'] : 0;
			$due_date = !empty($resListBill['due_date']) ? date('m/d/Y',strtotime($resListBill['due_date'])) : '-';
		}
	}
//********************** Billing Code End   **********************

//********************** Coverage Code Start **********************
	$sqlCoverage = "SELECT id,coverage_period_name,coverage_period_start,coverage_period_end FROM group_coverage_period WHERE is_deleted='N' AND status='Active' AND group_id=:group_id ORDER BY coverage_period_name DESC";
	$resCoverage = $pdo->select($sqlCoverage,array(":group_id"=>$group_id));
	$default_coverage = '';
	if(!empty($resCoverage)){
		foreach ($resCoverage as $key => $value) {
			$today_date = date('Y-m-d');

			if(strtotime($today_date) > strtotime(date('Y-m-d',strtotime($value['coverage_period_start']))) && strtotime($today_date) <= strtotime(date('Y-m-d',strtotime($value['coverage_period_end'])))){
				$default_coverage = $value['id'];
			}
		}
	}

//********************** Coverage Code End   **********************	

$exStylesheets = array('thirdparty/bootstrap-tables/css/bootstrap-table.min.css');
$exJs = array('thirdparty/bootstrap-tables/js/bootstrap-table.min.js', 'thirdparty/simscroll/jquery.slimscroll.min.js');

$template = 'dashboard.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
