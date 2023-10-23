<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(53);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Memberships';
$page_title = "Memberships";
$effective_date='';
$is_back= !empty($_GET['is_back']) ? $_GET['is_back'] : false;
$state_res = $pdo->select("SELECT name FROM states_c WHERE country_id = 231");
$product_ids = array();

$is_clone = (isset($_GET['is_clone']) && $_GET['is_clone'] == 'Y') ? 'Y' : 'N';
$fee_id = "";
$membership_id = isset($_GET['membership_fee_id']) ? $_GET['membership_fee_id'] : 0;
$incr = "";

if($membership_id > 0){
    $incr .= " AND pf.id = $membership_id";
}

if (!empty($_GET['fee_id'])) {
	$fee_id = $_GET['fee_id'];

    $feeSql = "SELECT p.name,p.product_code,p.fee_type,GROUP_CONCAT(paf.product_id) as product_ids,p.is_assign_by_state,pm.pricing_effective_date as effective_date,pm.pricing_termination_date as termination_date,p.is_fee_on_renewal,p.initial_purchase,p.fee_renewal_type,p.fee_renewal_count,p.is_fee_on_commissionable,pm.price,pm.non_commission_amount,pm.commission_amount,GROUP_CONCAT(DISTINCT states) as states,GROUP_CONCAT(abs.product_id) as assoc_diff_products 
               FROM prd_fees pf
               JOIN prd_assign_fees paf on(paf.prd_fee_id = pf.id)
               JOIN prd_main p on (p.id = paf.fee_id)
               JOIN prd_matrix pm on pm.product_id = p.id
               LEFT JOIN association_assign_by_state abs on(abs.association_fee_id = paf.fee_id AND abs.is_deleted = 'N')
               WHERE p.id = :fee_id AND paf.is_deleted = 'N'";
    $feeRow = $pdo->selectOne($feeSql, array(":fee_id" => $fee_id));
    if(!empty($feeRow)){
        $fee_name = $feeRow['name'];
        $display_fee_id = $feeRow['product_code'];
        $fee_type = $feeRow['fee_type'];
        $fee_product_list = $feeRow['product_ids'];
        $product_ids = !empty($fee_product_list) ? explode(",", $fee_product_list) : array();


        $effective_date = !empty($feeRow['effective_date']) ? date('m/d/Y',strtotime($feeRow['effective_date'])) : '';
        $termination_date = !empty($feeRow['termination_date']) ? date('m/d/Y',strtotime($feeRow['termination_date'])) : '';
        $initial_purchase = $feeRow['initial_purchase'];
        $is_fee_on_renewal = $feeRow['is_fee_on_renewal'];
        $fee_renewal_type = $feeRow['fee_renewal_type'];
        $fee_renewal_count = $feeRow['fee_renewal_count'];
        $price=$feeRow['price'];
        $nc_amount=$feeRow['non_commission_amount'];
        $c_amount=$feeRow['commission_amount'];
        $is_fee_commissionable = $feeRow['is_fee_on_commissionable'];
        if($is_fee_commissionable == 'N'){
            $retail_price = $feeRow['price'];
        }else{
            $retail_price = "";
        }
        $differ_by_state = $feeRow['is_assign_by_state'];
        $assign_states = $feeRow['states'] ? explode(',',$feeRow['states']) : array();
        $assoc_diff_product = $feeRow['assoc_diff_products'] ? explode(',',$feeRow['assoc_diff_products']) : array();
        if($is_clone == 'Y'){
            $display_fee_id = get_membership_fee_id();
            $fee_name = "";
        }
    }
    
}else{
    $display_fee_id = get_membership_fee_id();
    $is_included='N';
    $initial_purchase='N';
    $is_fee_on_renewal='N';
    $fee_renewal_type='Continuous';
    $is_benefit_tier='N';
    $fee_method='Fixed Price';
    $percentage_type='Retail';
    $is_fee_commissionable='N';
    $differ_by_state='N';
    $price="";
    $nc_amount="";
    $c_amount="";
    $retail_price="";
    $assign_states = array();
    $assoc_diff_product = array();
}

$company_arr=array();
if (!empty($membership_id)) {

    $productSql="SELECT p.*,c.title FROM prd_main p 
                LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
                LEFT JOIN prd_category c ON (c.id = p.category_id)
                where p.type!='Fees'AND p.parent_product_id=0 AND p.is_deleted='N' AND FIND_IN_SET(:membership_id,p.membership_ids) AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]') ORDER BY name ASC";
    $productRes = $pdo->select($productSql,array(":membership_id" => $membership_id));

    if($productRes){
        foreach ($productRes as $key => $row) {
        	if($row['type'] == 'Kit'){
            		$row['title']= 'Product Kits';
            }
            if (!isset($company_arr[$row['title']])) {
                    $company_arr[$row['title']] = array();
            }
            array_push($company_arr[$row['title']], $row);
        }
    }
}

$associationStateRes = array();
$fee_planRows = $pdo->select("SELECT id as plan_id,title as plan_name FROM prd_plan_type ORDER BY id");


$exStylesheets = array('thirdparty/bootstrap-datepicker-master/css/datepicker.css', 'thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js',
    'thirdparty/price_format/jquery.price_format.2.0.js',
'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$layout = 'iframe.layout.php';
$template = 'add_membership_fee.inc.php';
include_once 'layout/end.inc.php';
?>