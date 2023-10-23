<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(5);
$agent_id = isset($_GET['agent_id']) ? $_GET['agent_id'] : 0;

$userTypeRes = $pdo->selectOne("SELECT c.type,cs.agent_coded_level,cs.agent_coded_profile 
								from customer c 
								JOIN customer_settings cs on(c.id = cs.customer_id)
								WHERE md5(c.id)=:id", array(":id" => $agent_id));
$userType = 'Agent';
$profile_id=0;
$agent_coded_level = "";
if ($userTypeRes) {
	$userType = $userTypeRes["type"];
	$agent_coded_level = $userTypeRes["agent_coded_level"];
	$profile_id=$userTypeRes["agent_coded_profile"];
}

/*$sql_product = "SELECT * FROM prd_main where is_deleted='N' AND status='Active' AND id not in(select product_id from agent_product_rule where is_deleted='N' and agent_id=:agent_id)";
$where_product = array(":agent_id"=>$agent_id);
$ProductRows = $pdo->select($sql_product, $where_product);*/

$productSql = "SELECT p.*,c.company_name FROM prd_main p
            	LEFT JOIN company c ON (c.id = p.company_id)
            	where p.is_deleted='N' AND p.status='Active' and 
                p.id not in(select product_id from agent_product_rule where is_deleted='N' and md5(agent_id)=:agent_id) 
                ORDER BY p.name ASC";
$productRes = $pdo->select($productSql, array(":agent_id" => $agent_id));

$company_arr = array('Global Products' => array(), 'Group Only Products' => array(), 'Variations' => array());
// pre_print($company_arr,false);
if ($productRes) {
	foreach ($productRes as $key => $row) {
		unset($row["description"]);
		unset($row["long_desc"]);
		
		if ($row["allow_sell_to"] == "Agent" || $row["allow_sell_to"] == "All") {
			$company_arr['Global Products'][] = $row;
		} else if ($row["allow_sell_to"] == "Group") {
			$company_arr['Group Only Products'][] = $row;
		}
		
		if (empty($company_arr['Global Products'])) {
			unset($company_arr['Global Products']);
		}

		if (empty($company_arr['Group Only Products'])) {
			unset($company_arr['Group Only Products']);
		}

		if (empty($company_arr['Variations'])) {
			unset($company_arr['Variations']);
		}
		if (empty($company_arr['Kits'])) {
			unset($company_arr['Kits']);
		}
		
	}
}
ksort($company_arr);
$sql_commission = "SELECT * FROM commission_rule where (product_id=:product_id || parent_product_id=:product_id) AND  is_deleted='N'";
$where_commission = array(":product_id" => $parent_product_id);
$RuleRows = $pdo->select($sql_commission, $where_commission);

$validate = new Validation();

if (isset($_POST['save'])) {

	$variation_product_select = $_POST['variation_product_select'];
	$variation_commission_select = $_POST['variation_commission_select'];

	if (empty($variation_product_select)) {
		$validate->setError("variation_product_select", "Please select at least one product");
	}
	if(empty($variation_commission_select)){
		$validate->setError("variation_commission_select", "Please select at least one commission rule");	
	}

	if ($validate->isValid()) {
		$insert_product_rule = array(
				'agent_id' => $agent_id,
				'product_id' => $variation_product_select,
				'admin_id' => isset($_SESSION["admin"]["id"]) ? $_SESSION["admin"]["id"] : 0,
				'status' => 'Pending Approval',
				'created_at' => 'msqlfunc_NOW()',
			);

		$insert_product_rule['product_id'] = $variation_product_select;
		$insert_product_rule['parent_product_id'] = getParentProductId($variation_product_select);

		assignCommissionRuleToAgent($agent_id, $insert_product_rule['product_id'], $variation_commission_select);

		$ap_rule_id = $pdo->insert("agent_product_rule", $insert_product_rule);
		setNotifySuccess('Success, you successfully created a new product rule.');
		redirect("agent_detail.php?id=".$agent_id,true);
	}
}

$errors = $validate->getErrors();



$exStylesheets = array("thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js');

$template = 'product_rules_add.inc.php';
include_once 'layout/iframe.layout.php';
?>