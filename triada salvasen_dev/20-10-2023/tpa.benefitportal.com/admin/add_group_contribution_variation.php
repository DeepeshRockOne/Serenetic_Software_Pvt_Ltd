<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$rule_id=!empty($_GET['id']) ? $_GET['id'] : 0;
$is_clone=!empty($_GET['clone']) ? $_GET['clone'] : 'N';

$sqlGroupContribution = "SELECT * FROM group_contribution_rule where rule_type='Variation' and is_deleted='N' AND id=:id";
$resGroupContribution = $pdo->selectOne($sqlGroupContribution,array(":id"=>$rule_id));

$incr="";
$sch_params = array();
$all_products = array();
$is_added_products = "false";
if(!empty($resGroupContribution)){
	$group_id=$resGroupContribution['group_id'];
	$minimum_group_contribution=$resGroupContribution['minimum_group_contribution'];

	$sqlGroupContributionSetting = "SELECT * FROM group_contribution_setting where is_deleted='N' AND group_contribution_rule_id=:rule_id";
	$resGroupContributionSetting = $pdo->select($sqlGroupContributionSetting,array(":rule_id"=>$rule_id));
	if(!empty($resGroupContributionSetting)){
		$is_added_products = "true";
		foreach ($resGroupContributionSetting as $key => $value) {
			$productArr = !empty($value['products']) ? explode(",", $value['products']) : array();
			$all_products = array_merge($all_products,$productArr);
		}
	}

	if($is_clone == 'Y'){
		$rule_id = 0;
		$group_id = 0;
		$sqlGroupContribution = "SELECT group_concat(group_id) as group_id FROM group_contribution_rule where rule_type='Variation' and is_deleted='N'";
		$resGroupContribution = $pdo->selectOne($sqlGroupContribution);

		if(!empty($resGroupContribution) && !empty($resGroupContribution['group_id'])){
			$incr .= " AND id NOT IN (".$resGroupContribution['group_id'].")";
		}
	}else{
		$incr .= " AND id=:id";
		$sch_params[':id']=$group_id;
	}
	
}else{
	$sqlGroupContribution = "SELECT group_concat(group_id) as group_id FROM group_contribution_rule where rule_type='Variation' and is_deleted='N'";
	$resGroupContribution = $pdo->selectOne($sqlGroupContribution);

	if(!empty($resGroupContribution) && !empty($resGroupContribution['group_id'])){
		$incr .= " AND id NOT IN (".$resGroupContribution['group_id'].")";
	}
}

$sqlGroup = "SELECT id,fname,lname,rep_id,business_name FROM customer where type='Group' AND status='Active' $incr";
$resGroup = $pdo->select($sqlGroup,$sch_params);

$company_arr = array('Global Products' => array());
$sqlProducts="SELECT p.id,p.name,p.product_code,pc.title as company_name  FROM prd_main p 
JOIN prd_category pc ON (pc.id = p.category_id)
WHERE p.is_deleted='N' AND p.type!='Fees' AND p.product_type ='Group Enrollment'
ORDER BY pc.title,p.name ASC";
$productRes = $pdo->select($sqlProducts);

if ($productRes){
    foreach($productRes as $key => $row) {
        if($row['company_name'] != ""){
            $company_arr[$row['company_name']][] = $row;
        }else{
            $company_arr['Global Products'][] = $row;                
        }

        if (empty($company_arr['Global Products'])) {
            unset($company_arr['Global Products']);
        }

        if (empty($row['company_name'])) {
            unset($row['company_name']);
        }
    }
}
ksort($company_arr);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "add_group_contribution_variation.inc.php";
include_once 'layout/iframe.layout.php';
?>