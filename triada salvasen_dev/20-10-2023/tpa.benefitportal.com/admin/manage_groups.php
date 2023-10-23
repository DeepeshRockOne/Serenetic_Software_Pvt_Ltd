<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/Api.class.php';

has_access(6);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "User Groups";
$breadcrumbes[2]['title'] = "Groups";
$breadcrumbes[2]['link'] = 'groups_listing.php';
$breadcrumbes[3]['title'] = "Manage Groups";
$breadcrumbes[3]['link'] = 'manage_groups.php';

$ajaxApiCall = new Api();

$all_products = array();
$is_added_products = "false";
//******************** Pay Options Code Start **********************
	$sqlPayOptions = "SELECT * FROM group_pay_options where rule_type='Global' and is_deleted='N'";
	$resPayOptions = $pdo->selectOne($sqlPayOptions);

	if(!empty($resPayOptions)){
		$is_cc=$resPayOptions['is_cc'];
		$is_check=$resPayOptions['is_check'];
		$is_ach=$resPayOptions['is_ach'];

		$cc_additional_charge = $resPayOptions['cc_additional_charge'];
		$cc_charge_type = $resPayOptions['cc_charge_type'];
		$cc_charge = $resPayOptions['cc_charge'];
		
		$check_additional_charge = $resPayOptions['check_additional_charge'];
		$check_charge = $resPayOptions['check_charge'];

		$remit_to_address = $resPayOptions['remit_to_address'];
	}
//******************** Pay Options Code End   **********************

//******************** Cobra Benefit Code start **********************
	$sqlCobraBenefit = "SELECT * FROM group_cobra_benefits where is_deleted ='N'";
	$resCobraBenefit = $pdo->selectOne($sqlCobraBenefit);

	if(!empty($resCobraBenefit)){
		$group_use_cobra_benefit = $resCobraBenefit['group_use_cobra_benefit'];
		$is_additional_surcharge = $resCobraBenefit['is_additional_surcharge'];
		$additional_surcharge = $resCobraBenefit['additional_surcharge'];
	}
//******************** Cobra Benefit Code End   **********************

//******************** Contribution Code Start **********************
	$sqlGroupContribution = "SELECT * FROM group_contribution_rule where is_deleted='N' AND rule_type='Global'";
	$resGroupContribution = $pdo->selectOne($sqlGroupContribution);

	if(!empty($resGroupContribution)){
		$group_contribution_rule_id = $resGroupContribution['id'];
		$minimum_group_contribution = $resGroupContribution['minimum_group_contribution'];


		$sqlGroupContributionSetting = "SELECT * FROM group_contribution_setting where is_deleted='N' AND group_contribution_rule_id=:group_contribution_rule_id";
		$resGroupContributionSetting = $pdo->select($sqlGroupContributionSetting,array(":group_contribution_rule_id"=>$group_contribution_rule_id));

		if(!empty($resGroupContributionSetting)){
			$is_added_products = "true";
			foreach ($resGroupContributionSetting as $key => $value) {
				$productArr = !empty($value['products']) ? explode(",", $value['products']) : array();
				$all_products = array_merge($all_products,$productArr);
			}
		}
	}
//******************** Contribution Code End   **********************	

//******************** Agreement Code Start **********************	
	$res_t =$pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Group',":status"=>'Active')); 

	if(!empty($res_t)){
		$group_terms = $res_t['terms'];
		$group_terms_id = $res_t['id'];
	}
//******************** Agreement Code End   **********************	

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

$is_ajaxed = isset($_POST['is_ajaxed']) ? $_POST['is_ajaxed'] : '';
if($is_ajaxed){
	if(isset($_POST['api_key'])){
	    $apiResponse = $ajaxApiCall->ajaxApiCall($_POST,true);
	    $fetch_rows = !empty($apiResponse['data']) ? $apiResponse['data']['data']: array();
	    $total_rows = count($fetch_rows);
	    if($total_rows > 0){
			$paginate = $ajaxApiCall->paginate($apiResponse['data'],'manage_groups.php');
			$paginageLinks = $paginate ['links'];
			$per_page = $paginate ['per_page'];
		}
	}
	include_once 'tmpl/group_enrollment_settings.inc.php';
	exit;
}

$data = array('api_key' => 'globalCartSettings');
$globalCartSetting = $ajaxApiCall->ajaxApiCall($data,true);
$pay_calc = !empty($globalCartSetting['data']['take_home_pay_calc']) ? $globalCartSetting['data']['take_home_pay_calc'] : "";
$effective_date = !empty($globalCartSetting['data']['effective_date']) ? $globalCartSetting['data']['effective_date'] : "";
$termination_date = !empty($globalCartSetting['data']['termination_date']) ? $globalCartSetting['data']['termination_date'] : "";
$cart_type = !empty($globalCartSetting['data']['cart_type']) ? $globalCartSetting['data']['cart_type'] : "";
$global_id = !empty($globalCartSetting['data']['id']) ? $globalCartSetting['data']['id'] : '';

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$summernote = "Y";
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 'thirdparty/ckeditor/ckeditor.js');

$template = 'manage_groups.inc.php';
include_once 'layout/end.inc.php';
?>
