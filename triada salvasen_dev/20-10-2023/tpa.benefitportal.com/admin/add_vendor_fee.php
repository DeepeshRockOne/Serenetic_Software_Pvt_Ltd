<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(65);

$vendor_id = checkIsset($_GET['vendor_id']);
$vendor_fee_id = checkIsset($_GET['vendor_fee_id']);

$fee_id = checkIsset($_GET['fee_id']);
$is_clone = (isset($_GET['is_clone']) && $_GET['is_clone'] == 'Y') ? 'Y' : 'N';

if (!empty($_GET['fee_id'])) {
    $fee_id = $_GET['fee_id'];

    $feeSql = "SELECT pm.id,pm.name,pm.product_code,pm.fee_type,pm.initial_purchase,pm.is_benefit_tier,pm.is_fee_on_renewal,pm.fee_renewal_type,pm.fee_renewal_count,pmt.pricing_effective_date,pmt.pricing_termination_date
            FROM prd_main pm
            JOIN prd_matrix pmt ON(pm.id=pmt.product_id)
            WHERE pm.is_deleted = 'N' AND md5(pm.id) = :fee_id ORDER BY pm.id";
    $feeRow = $pdo->selectOne($feeSql, array(":fee_id" => $fee_id));

    if(!empty($feeRow)){

        $fee_name = $feeRow['name'];
        $display_fee_id = $feeRow['product_code'];
        $fee_type = $feeRow['fee_type'];
        $effective_date = !empty($feeRow['pricing_effective_date']) ? date('m/d/Y',strtotime($feeRow['pricing_effective_date'])) : '';
        $termination_date = !empty($feeRow['pricing_termination_date']) ? date('m/d/Y',strtotime($feeRow['pricing_termination_date'])) : '';
        
        $initial_purchase = $feeRow['initial_purchase'];
        $is_benefit_tier = $feeRow['is_benefit_tier'];
        $is_fee_on_renewal = $feeRow['is_fee_on_renewal'];
        $fee_renewal_type = $feeRow['fee_renewal_type'];
        $fee_renewal_count = $feeRow['fee_renewal_count'];

        if($is_clone == 'Y'){
            $display_fee_id = get_vendor_fee_id();
            $fee_name = "";
        }else{
            //************* Activity Code Start *************
                $description['ac_message'] =array(
                    'ac_red_1'=>array(
                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                      'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' Read Vendor Fee ',
                    'ac_red_2'=>array(
                      //'href'=> '',
                      'title'=> $display_fee_id,
                    ),
                );  

                activity_feed(3, $_SESSION['admin']['id'], 'Admin', $feeRow['id'], 'vendor',"Admin Read Vendor Fee", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
            //************* Activity Code End *************
        }

        $assignSql = "SELECT group_concat(product_id) as product_ids 
                      FROM prd_assign_fees 
                      WHERE is_deleted='N' AND md5(fee_id)=:fee_id ";
        $assignRow = $pdo->selectOne($assignSql, array(":fee_id" => $fee_id));
        $fee_product_list = $assignRow['product_ids'];
        $product_ids = !empty($fee_product_list) ? explode(",", $fee_product_list) : array();
        
        $feePlanSql = "SELECT id,product_id,plan_type,price_calculated_on,
                        price_calculated_type,price,is_deleted 
                        FROM  prd_matrix 
                        WHERE is_deleted='N' AND md5(product_id) = :product_id ";
        $feePlanRow = $pdo->select($feePlanSql, array(":product_id" => $fee_id));

        $priceArr = array();
        if(!empty($feePlanRow)){
            foreach ($feePlanRow as $key => $value) {
                $fee_method=$value['price_calculated_on'];
                $percentage_type=$value['price_calculated_type'];

                if($value['plan_type']>0){ 
                    $priceArr[$value['plan_type']]=$value['price'];
                }else{
                    $fee_price=$value['price'];
                }
            }
        }
    }
}else{
    $display_fee_id = get_vendor_fee_id();
    $is_included='N';
    $initial_purchase='N';
    $is_fee_on_renewal='N';
    $fee_renewal_type='Continuous';
    $is_benefit_tier='N';
    $fee_method='FixedPrice';
    $percentage_type='Retail';
    $fee_type = 'Display Only';
}

// $productSql="SELECT p.id,p.name,p.product_code,p.type,c.title 
//             FROM prd_main p 
//             LEFT JOIN prd_category c ON (c.id = p.category_id)
//             WHERE p.type!='Fees' AND p.parent_product_id=0 AND p.is_deleted='N' and p.status='Active' ORDER BY name ASC";
// $productRes = $pdo->selectGroup($productSql,array(),'title');

$productRes = get_active_global_products_for_filter(0,false,true);

 
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array(
    'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
    'thirdparty/price_format/jquery.price_format.2.0.js'
);

$layout = 'iframe.layout.php';
$template = 'add_vendor_fee.inc.php';
include_once 'layout/end.inc.php';
?>