<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(7);

$group_id = $_SESSION['groups']['id'];
$coverage_id = !empty($_GET['coverage']) ? $_GET['coverage'] : '';
$offering_id = !empty($_GET['offering']) ? $_GET['offering'] : '';
$is_clone = !empty($_GET['clone']) ? $_GET['clone'] : 'N';

$sqlOffering = "SELECT go.*,
				gc.class_name,gc.existing_member_eligible_coverage,gc.new_member_eligible_coverage,
				gc.renewed_member_eligible_coverage,gc.pay_period 
				FROM group_coverage_period_offering go 
				JOIN group_classes gc ON (gc.id = go.class_id)
				where go.is_deleted='N' AND md5(go.id)=:id";
$resOffering = $pdo->selectOne($sqlOffering,array(":id"=>$offering_id));

$sqlCoverage = "SELECT * FROM group_coverage_period where is_deleted='N' AND md5(id)=:id";
$resCoverage = $pdo->selectOne($sqlCoverage,array(":id"=>$coverage_id));
if(!empty($resCoverage)){
	$group_coverage_period_id = $resCoverage['id'];
}

$class_id = 0;
$incr = "";
$sch_params = array(":group_id"=>$group_id);

$display_contribution = false;
$disabled_tabs = true;

if(!empty($resOffering)){
	$disabled_tabs = false;
	
	$tmp_offering_id = $resOffering['id'];
	$offering_id = $resOffering['id'];
	$class_id = $resOffering['class_id'];
	
	$class_name = $resOffering['class_name'];
	
	$existing_member_eligible_coverage = $resOffering['existing_member_eligible_coverage'];
	$new_member_eligible_coverage = $resOffering['new_member_eligible_coverage'];
	$renewed_member_eligible_coverage = $resOffering['renewed_member_eligible_coverage'];
	
	$pay_period = $resOffering['pay_period'];
	
	$cl_existing_member = $existing_member_eligible_coverage;
	$cl_new_member = $new_member_eligible_coverage;
	$cl_renewed_member = $renewed_member_eligible_coverage;
	if($existing_member_eligible_coverage != "Immediately"){
		$cl_existing_member = "After ".$existing_member_eligible_coverage." days ";
	}
	if($new_member_eligible_coverage != "Immediately"){
		$cl_new_member = "After ".$new_member_eligible_coverage." days ";
	}
	if($renewed_member_eligible_coverage != "Immediately"){
		$cl_renewed_member = "After ".$renewed_member_eligible_coverage." days ";
	}
	
	$open_enrollment_start = date('m/d/Y',strtotime($resOffering['open_enrollment_start']));
	$open_enrollment_end = date('m/d/Y',strtotime($resOffering['open_enrollment_end']));
	$first_coverage_date = !empty($resOffering['first_coverage_date']) ? date('m/d/Y',strtotime($resOffering['first_coverage_date'])) : '';
	$waiting_restriction_on_open_enrollment = $resOffering['waiting_restriction_on_open_enrollment'];
	$allow_future_effective_date = $resOffering['allow_future_effective_date'];
	$allowed_range = $resOffering['allowed_range'];
	
	$products = $resOffering['products'];
	$products_arr = explode(",", $products);
	$is_contribution = $resOffering['is_contribution'];
	$display_contribution_on_enrollment = $resOffering['display_contribution_on_enrollment'];

	

	if($is_clone == 'Y'){
		$offering_id = 0;
		$class_id = 0;
	}
	$display_contribution = true;
}

$sqlGroupOffering = "SELECT group_concat(go.class_id) as class_ids FROM group_coverage_period_offering go
	WHERE go.is_deleted='N' AND md5(go.group_coverage_period_id) = :id";
$resGroupOffering = $pdo->selectOne($sqlGroupOffering,array(":id"=>$coverage_id));

if(!empty($resGroupOffering) && !empty($resGroupOffering['class_ids'])){
	$class_id_arr = explode(",", $resGroupOffering['class_ids']);

	if(!empty($class_id)){
		if (($key = array_search($class_id, $class_id_arr)) !== false) {
		    unset($class_id_arr[$key]);
		}
	}

	if(!empty($class_id_arr)) { 
		$class_id_list = implode(",", $class_id_arr);
		$incr .=" AND (gc.id not in ($class_id_list))";
	}
}

$sqlClass="SELECT gc.* FROM group_classes gc WHERE gc.is_deleted='N' and gc.group_id =:group_id $incr";
$resClass=$pdo->select($sqlClass,$sch_params);

$company_arr = array('Global Products' => array());
$productSql = "SELECT p.id,p.product_code,p.name as prdName,p.type,p.parent_product_id,pc.title as company_name 
                FROM prd_main p
                JOIN agent_product_rule rp ON (rp.product_id=p.id AND rp.is_deleted='N' AND rp.status='Contracted' AND rp.agent_id=:agent_id)
                LEFT JOIN prd_category pc ON (pc.id = p.category_id)
                WHERE p.is_deleted='N' AND p.status='Active' AND p.type!='Fees' AND p.product_type ='Group Enrollment'
                ORDER BY company_name,p.name ASC";
$productRes = $pdo->select($productSql, array(":agent_id" => $group_id));


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


$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css' . $cache,
	'thirdparty/bootstrap-datepicker-master/css/datepicker.css'.$cache,
);
$exJs = array(
	'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js' . $cache,
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js'.$cache,
	'thirdparty/ajax_form/jquery.form.js',
);


$template = 'offering_coverage_periods.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
