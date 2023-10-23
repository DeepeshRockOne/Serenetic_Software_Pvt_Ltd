<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[1]['link'] = 'healthy_steps.php';
$breadcrumbes[2]['title'] = "Healthy Steps";
$breadcrumbes[2]['link'] = 'add_globalhealthy_steps.php';

$is_clone = (isset($_GET['is_clone']) && !empty($_GET['is_clone']) ? $_GET['is_clone'] : 'N');
$resource_res= array();

$product_id = checkIsset($_GET['product_id']);
$health_id = checkIsset($_GET['health_id']);
$product_ids = array();
$state_names = array();
$display_id = '';

if(!empty($product_id) && !empty($health_id)){

    $resource_res = $pdo->selectOne("SELECT pf.id as health_id,COUNT(paf.id) AS total_products,GROUP_CONCAT(paf.product_id) as product_ids,p.create_date,p.fee_renewal_type,p.is_fee_on_renewal,p.fee_renewal_count,pm.price,pm.commission_amount,pm.non_commission_amount,
                p.id,p.name,p.product_code,p.is_fee_on_commissionable,p.is_member_benefits,
                pm.pricing_effective_date,pm.pricing_termination_date,p.status,pmp.description,pmp.is_member_portal
                FROM prd_main p
                LEFT JOIN prd_matrix pm ON(pm.product_id=p.id AND pm.is_deleted='N')
                JOIN prd_assign_fees paf ON(paf.fee_id=p.id AND paf.is_deleted='N' )
                JOIN prd_fees pf ON(pf.id=paf.prd_fee_id AND pf.is_deleted='N')
                LEFT JOIN prd_member_portal_information pmp ON(pmp.product_id = p.id AND pmp.is_deleted='N')
                WHERE p.type='Fees' AND p.product_type='Healthy Step' AND p.record_type='Primary'
                AND md5(p.id)=:prd_id AND md5(pf.id) = :health_id
                AND p.is_deleted='N' ",array(":prd_id"=>$product_id ,":health_id"=>$health_id));

    if(empty($resource_res['health_id'])){
        setNotifyError(" Record deleted or record found!",true);
        redirect($ADMIN_HOST.'/healthy_steps.php');
        exit;
    }
    if(!empty($resource_res['product_ids'])){
        $product_ids = explode(',',$resource_res['product_ids']);
    }

    $display_id = checkIsset($resource_res['product_code']) !='' ? $resource_res['product_code'] : '';
}

$productRes = get_active_global_products_for_filter();

if(empty($resource_res) || $is_clone == 'Y'){
    include_once __DIR__ . '/../includes/function.class.php';
    $functionsList = new functionsList();
    $display_id=$functionsList->generateHealthyStepDisplayID();
}

$sqlHealthyStates = "SELECT state FROM healthy_steps_states where md5(prd_fee_id) = :id and is_deleted='N'";
$resHealthyStates = $pdo->select($sqlHealthyStates,array(":id"=>$health_id));

if(!empty($resHealthyStates)){
    foreach ($resHealthyStates as $key => $value) {
        array_push($state_names, $value['state']);
    }
}

$summernote = true;

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,'thirdparty/price_format/jquery.price_format.2.0.js', 'thirdparty/ckeditor/ckeditor.js');

$template = 'add_globalhealthy_steps.inc.php';
include_once 'layout/end.inc.php';
?>
