<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$productMain_id=1;

$product_id = checkIsset($_GET['product_id']);
$fee_id = checkIsset($_GET['fee_id']);
$is_clone = (isset($_GET['is_clone']) && $_GET['is_clone'] == 'Y') ? 'Y' : 'N';
$groupEnrollmentPrd = isset($_GET['groupEnrollmentPrd']) ? $_GET['groupEnrollmentPrd'] : 'N';
$data = !empty($_GET['data']) ? json_decode($_GET['data'],true) : array();

if (!empty($_GET['fee_id']) && empty($data)) {
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
        
        $initial_purchase = checkIsset($feeRow['initial_purchase']);
        $is_benefit_tier = $feeRow['is_benefit_tier'];
        $is_fee_on_renewal = checkIsset($feeRow['is_fee_on_renewal']);
        $fee_renewal_type = checkIsset($feeRow['fee_renewal_type']);
        $fee_renewal_count = checkIsset($feeRow['fee_renewal_count']);

        if($is_clone == 'Y'){
            $display_fee_id = get_product_fee_id();
            $fee_name = "";
        }

        $assignSql = "SELECT group_concat(product_id) as product_ids 
                      FROM prd_assign_fees 
                      WHERE is_deleted='N' AND md5(fee_id)=:fee_id ";
        $assignRow = $pdo->selectOne($assignSql, array(":fee_id" => $fee_id));
        $fee_product_list = $assignRow['product_ids'];
        $product_ids = !empty($fee_product_list) ? explode(",", $fee_product_list) : array();
        
        $feePlanSql = "SELECT id,product_id,plan_type,opt_product_sell_type,price_calculated_on,
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
    if(!empty($data)){

        $fee_name = $data['name'];
        $display_fee_id = $data['product_code'];
        $fee_type = $data['fee_type'];
        $effective_date = !empty($data['pricing_effective_date']) ? date('m/d/Y',strtotime($data['pricing_effective_date'])) : '';
        $termination_date = !empty($data['pricing_termination_date']) ? date('m/d/Y',strtotime($data['pricing_termination_date'])) : '';
        
        $initial_purchase = checkIsset($data['initial_purchase']);
        $is_benefit_tier = $data['is_benefit_tier'];
        $is_fee_on_renewal = checkIsset($data['is_fee_on_renewal']);
        $fee_renewal_type = checkIsset($data['fee_renewal_type']);
        $fee_renewal_count = checkIsset($data['fee_renewal_count']);
        $pricing_model = $data['pricing_model'];
       
        if($is_clone == 'Y'){
            $display_fee_id = get_product_fee_id();
            $fee_name = "";
        }

        $feePlanRow = $data['price'];
        $priceArr = array();

         if(!empty($feePlanRow)){
            foreach ($feePlanRow as $key => $value) {
                if(!empty($value)){
                $fee_method=$value['price_calculated_on'];
                $percentage_type=$value['price_calculated_type'];

                if($is_benefit_tier == "Y" && $pricing_model != "FixedPrice"){
                        $priceArr[$value['matrix_group']]=$value['price'];
                }else{
                    if(!empty($value['plan_type'])){ 
                        $priceArr[$value['plan_type']]=$value['price'];
                    }else{
                        $fee_price=$value['price'];
                    }
                }
            }
            }
        }
        $priceArrEnc = json_encode($priceArr);
    }else{
        $display_fee_id = get_product_fee_id();
        $is_included='N';
        $initial_purchase='N';
        $is_fee_on_renewal='N';
        $fee_renewal_type='Continuous';
        $is_benefit_tier='N';
        $fee_method='FixedPrice';
        $percentage_type='Retail';
        $fee_type='Charged';
    }
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,'thirdparty/price_format/jquery.price_format.2.0.js');

$template = 'add_product_fee.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
