<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(28);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Builder';
$breadcrumbes[1]['link'] = 'commission_builder.php';
$breadcrumbes[2]['title'] = '+ Commission Rule';

$parentCommission=!empty($_GET['parentCommission']) ? $_GET['parentCommission'] : 0;
$commission=!empty($_GET['commission']) ? $_GET['commission'] : 0;
$is_clone=!empty($_GET['is_clone']) ? $_GET['is_clone'] : 'N';
$commission_rule_id = !empty($commission) ? $commission : $parentCommission;
$product_id = 0;
$productPlansArr = array();
$main_commission_array=array();

if(!empty($commission) || !empty($parentCommission)){
	
	if(!empty($parentCommission)){
		$variation_sql = "SELECT p.name,p.product_code
                FROM commission_rule cr 
                JOIN prd_main p ON (cr.product_id=p.id)  
                WHERE cr.is_deleted='N' AND md5(cr.id)=:id";
	   $variation_where=array(":id"=>$parentCommission);
	   $variation_res=$pdo->selectOne($variation_sql,$variation_where);
	}

	$sqlCommissionRule="SELECT id,product_id,rule_code,status,commission_on,stop_commission_after,new_business_commission_duration,renewal_commission_duration,commission_duration,commission_reversals,reverse_days,commission_json,calculate_by FROM commission_rule WHERE md5(id)=:id AND is_deleted='N'";
	$resCommissionRule=$pdo->selectOne($sqlCommissionRule,array(":id"=>$commission_rule_id));

	if(!empty($resCommissionRule)){
		$commission_rule_id= $resCommissionRule['id'];
		$product_id = $resCommissionRule['product_id'];
		$display_id = $resCommissionRule['rule_code'];
		$commission_status = $resCommissionRule['status'];
		$commission_type = $resCommissionRule['commission_on'];
		$stop_commission_after = $resCommissionRule['stop_commission_after'];
		$new_business_commission_duration = $resCommissionRule['new_business_commission_duration'];
		$renewal_commission_duration = $resCommissionRule['renewal_commission_duration'];
		$commission_reversals = $resCommissionRule['commission_reversals'];
		$reverse_days = $resCommissionRule['reverse_days'];
		
		$calculate_by = $resCommissionRule['calculate_by'];
		$commission_duration = $resCommissionRule['commission_duration'];
		$commission_array = json_decode($resCommissionRule['commission_json'],true);

		if(!empty($product_id)){
			$resPlans = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(plan_type)) as planIds FROM prd_matrix WHERE is_deleted='N' AND product_id=:product_id",array(":product_id" => $product_id));
			$productPlansArr = !empty($resPlans["planIds"]) ? explode(",", $resPlans["planIds"]) : array();
		}
		
		$i=1;
		$main_commission_array[$i]['commission_calculate_by']=$calculate_by;
		$main_commission_array[$i]['commission_duration']=$commission_duration;
		$main_commission_array[$i]['commission_price']=$commission_array;
		

		if($commission_duration=="Change Commission"){
			$sqlChangeCommission = "SELECT id,from_renewal,calculate_by,commission_duration,commission_json FROM commission_rule_range where commission_rule_id = :id AND is_deleted='N'";
			$resChangeCommission = $pdo->select($sqlChangeCommission,array(":id"=>$commission_rule_id));

			if(!empty($resChangeCommission)){
				$i++;
				foreach ($resChangeCommission as $changeKey => $changeValue) {
					$main_commission_array[$i]['range_id']='R'.$changeValue['id'];
					$main_commission_array[$i]['from_renewal']=$changeValue['from_renewal'];
					$main_commission_array[$i]['commission_calculate_by']=$changeValue['calculate_by'];
					$main_commission_array[$i]['commission_duration']=$changeValue['commission_duration'];
					$main_commission_array[$i]['commission_price']=json_decode($changeValue['commission_json'],true);
					$i++;
				}
			}
		}

		if(checkIsset($is_clone) == 'Y'){
			$sqlProducts = "SELECT p.id,p.name,p.product_code,p.record_type,pc.title as company_name,p.product_type,p.type,p.parent_product_id
		  FROM prd_main p
		  LEFT JOIN prd_category pc ON (pc.id=p.category_id)
		  LEFT JOIN commission_rule cr ON (p.id = cr.product_id AND cr.is_deleted='N')
		  LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
		  WHERE p.is_deleted='N' AND cr.id is null AND p.name !='' AND p.record_type = 'Primary' AND (p.type!='Fees' OR p.product_type='Healthy Step') AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]')
		  GROUP BY p.id order by p.name";
		}else{
			if($parentCommission){
				$sqlProducts = "SELECT p.id,p.name,p.product_code,p.record_type,pc.title AS company_name,p.product_type,p.type,p.parent_product_id
				FROM commission_rule cr
				JOIN prd_main p ON((p.id = cr.product_id OR p.parent_product_id = cr.product_id) AND cr.is_deleted='N')
				LEFT JOIN prd_category pc ON (pc.id=p.category_id)
				LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
			    WHERE cr.id=$commission_rule_id AND (p.type!='Fees' OR p.product_type='Healthy Step') AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]')";
			}else{

				$sqlProducts = "SELECT p.id,p.name,p.product_code,p.record_type,pc.title as company_name,p.product_type,p.type,p.parent_product_id
				  FROM prd_main p
				  LEFT JOIN prd_category pc ON (pc.id=p.category_id)
				  JOIN commission_rule cr ON (p.id = cr.product_id AND cr.is_deleted='N')
				  LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
				  WHERE cr.id=$commission_rule_id AND (p.type!='Fees' OR p.product_type='Healthy Step') AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]')";
			}
		}

		$description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Read Commission ',
			'ac_red_2'=>array(
					'href'=>$ADMIN_HOST.'/add_commission_rule.php?commission='.md5($commission_rule_id),
					'title'=>$display_id,
			),
		); 

		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $commission_rule_id, 'commission','Read Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}else{
		redirect($ADMIN_HOST.'/commission_builder.php');
	}
}else{

	$sqlProducts = "SELECT p.id,p.name,p.product_code,p.record_type,pc.title as company_name,p.product_type,p.type,p.parent_product_id,GROUP_CONCAT(DISTINCT(pm.plan_type)) as planTypes,p.status
	  FROM prd_main p
	  LEFT JOIN prd_matrix pm ON(p.id=pm.product_id AND pm.is_deleted='N')
	  LEFT JOIN prd_category pc ON (pc.id=p.category_id)
	  LEFT JOIN commission_rule cr ON (p.id = cr.product_id AND cr.is_deleted='N')
	  LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
	  WHERE p.is_deleted='N' AND cr.id is null AND p.name !='' AND p.record_type = 'Primary' AND (p.type!='Fees' OR p.product_type='Healthy Step') AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]')
	  GROUP BY p.id order by p.name";
}

$resProducts = $pdo->select($sqlProducts);

/*$healthyProducts = $pdo->select("SELECT id,name,product_code,fee_type as type,'Op29' as company_name from prd_main where parent_product_id=0 and product_type='Healthy Step' and record_type='Primary' and is_deleted='N'");
$resProducts = array_merge_recursive($resProducts,$healthyProducts);*/

if(empty($commission) || $is_clone == 'Y'){
	include_once __DIR__ . '/../includes/function.class.php';
	$functionsList = new functionsList();
	$display_id=$functionsList->generateCommissionDisplayID();
	$product_id = 0;
}

$company_arr=array();
if($resProducts){
    foreach ($resProducts as $key => $row) {
    	if($row['type'] == 'Kit'){
        		$row['company_name']= 'Product Kits';
		}
		if($row['type'] == 'Healthy Step'){
			$row['company_name']= 'Healthy Step';
		}
        if (!isset($company_arr[$row['company_name']])) {
                $company_arr[$row['company_name']] = array();
        }
        array_push($company_arr[$row['company_name']], $row);
    }
}

// $company_arr = array('Membership' => array(),'Global Products' => array(),'Variation' => array());

// if(!empty($resProducts)){
//     foreach ($resProducts as $key => $row) {
//         // if (isset($row['type']) && $row['type'] == 'Fees') {
//         // 	$row['company_name'] = $row['product_type'];
//         // }
//         if(isset($row['type']) && $row['type'] == 'Fees' && $row['product_type'] != 'Membership'){
//         	continue;
//         }
//         if(isset($row['type']) && $row['type'] == 'Fees'){
//         	if (!array_key_exists($row['company_name'], $company_arr['Membership'])) {
//             	$company_arr['Membership'][$row['company_name']] = array();
//         	}
//         	array_push($company_arr['Membership'][$row['company_name']],$row);
//         }
//         if(isset($row['record_type']) && $row['record_type'] == 'Primary'){
//         	if (!array_key_exists($row['company_name'], $company_arr['Global Products'])) {
//             	$company_arr['Global Products'][$row['company_name']] = array();
//         	}
//         	array_push($company_arr['Global Products'][$row['company_name']],$row);
//         }
//         if(isset($row['record_type']) && $row['record_type'] == 'Variation'){
//         	if (!array_key_exists($row['company_name'], $company_arr['Variation'])) {
//             	$company_arr['Variation'][$row['company_name']] = array();
//         	}
//         	array_push($company_arr['Variation'][$row['company_name']],$row);
//         }
//         // array_push($company_arr['Global Products'][$row['company_name']],$row);
//     }
	
// }

// pre_print($company_arr);

$exStylesheets = array("thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js');


$template = "add_commission_rule.inc.php";
include_once 'layout/end.inc.php';
?>