<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
$listBillObj = new ListBill();
$function_list = new functionsList();

$today = !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d');
//please use date in y-m-d format for a testing
$other_params = array();
$variationGrpId = '';

// start variation setting to payment of list bill 
$listBillsSql="SELECT lb.id as list_bill_id,lb.list_bill_pay_date as class_pay_date,lbo.auto_set_payment_received as is_check,lbo.auto_set_payment_received_inside_sys as is_system,lbo.auto_payment_days,IF(lb.company_id > 0,gc.billing_id,cgs.billing_id) as billing_id,cbp.payment_mode
               FROM list_bills lb 
               JOIN list_bill_options lbo ON(lb.customer_id=lbo.group_id AND (lbo.auto_set_payment_received='Y' OR lbo.auto_set_payment_received_inside_sys='Y') AND lbo.rule_type='Variation' AND lbo.is_deleted='N') 
               JOIN customer_group_settings cgs ON(cgs.customer_id = lb.customer_id)
               LEFT JOIN group_company gc ON(gc.id = lb.company_id)
               LEFT JOIN customer_billing_profile cbp ON(IF(lb.company_id > 0,cbp.id = gc.billing_id, cbp.id = cgs.billing_id) AND cbp.payment_mode != 'Check')
               WHERE lb.status IN ('open') AND lb.is_deleted='N' AND lb.list_bill_date <= :list_bill_date ";
$listBillWhere = array(":list_bill_date" => "$today");
$listBillRes = $pdo->select($listBillsSql,$listBillWhere);

if(!empty($listBillRes)){
    foreach ($listBillRes as $list_bill_row) {
        $prior_day = $list_bill_row['auto_payment_days'];
        $grp_pay_date = $list_bill_row['class_pay_date'];
        $listBillPaydate = $function_list->getWorkingPriorDay($grp_pay_date,$prior_day);
        
        if($listBillPaydate == $today){

            $location = "auto_payment_list_bill_cron";
            $other_params['payment_date']=$today;

            if($list_bill_row['is_check'] == 'Y'){
                $other_params['check_number']=9999;
                $pay_lb_res = $listBillObj->pay_list_bill($list_bill_row['list_bill_id'],'record_check_payment',$location,$other_params);  // list bill pay to third party (check)

            }else if($list_bill_row['is_system'] == 'Y' && !empty($list_bill_row['billing_id']) && $list_bill_row['billing_id'] != '' && !empty($list_bill_row['payment_mode']) && $list_bill_row['payment_mode'] != 'Check'){
                $pay_lb_res = $listBillObj->pay_list_bill($list_bill_row['list_bill_id'],$list_bill_row['billing_id'],$location,$other_params); //list bill pay for inside payment
            }
        }
    }
} 
// end variation setting to payment of list bill 



// start global setting to inside payment of list bill
$globalGrpsql = "SELECT auto_payment_days FROM list_bill_options WHERE rule_type = 'global' AND is_deleted='N' ";
$globalGrpRes = $pdo->selectOne($globalGrpsql);

if(!empty($globalGrpRes)){
    $auto_payment_days = $globalGrpRes['auto_payment_days'];

    $VariationGrpsql = "SELECT GROUP_CONCAT(DISTINCT group_id) AS grp_id FROM list_bill_options WHERE rule_type = 'Variation' AND is_deleted='N'";
    $VariationGrpRes = $pdo->selectOne($VariationGrpsql);
    
    if(!empty($VariationGrpRes['grp_id'])){
        $variation_ids = $VariationGrpRes['grp_id'];
        $variationGrpId = "AND lb.customer_id NOT IN ($variation_ids)";
    }  

    $listBillsSql = "SELECT lb.id as list_bill_id,lb.list_bill_pay_date as class_pay_date,IF(lb.company_id > 0,gc.billing_id,cgs.billing_id) as billing_id 
                    FROM list_bills lb
                    JOIN customer_group_settings cgs ON(cgs.customer_id = lb.customer_id)
                    LEFT JOIN group_company gc ON(gc.id = lb.company_id)
                    JOIN customer_billing_profile cbp ON(IF(lb.company_id > 0,cbp.id = gc.billing_id, cbp.id = cgs.billing_id) AND cbp.payment_mode != 'Check')
                    WHERE lb.status IN ('open') AND lb.is_deleted='N' $variationGrpId
                    AND lb.list_bill_date <= :list_bill_date ";
    $listBillWhere = array(":list_bill_date" => "$today");
    $listBillRes = $pdo->select($listBillsSql,$listBillWhere);
    
    if(!empty($listBillRes)){
        foreach ($listBillRes as $list_bill_row) {
            $grp_pay_date = $list_bill_row['class_pay_date'];
            $listBillPaydate = $function_list->getWorkingPriorDay($grp_pay_date,$auto_payment_days);

            if($listBillPaydate == $today && !empty($list_bill_row['billing_id']) && $list_bill_row['billing_id'] != ''){
                $location = "auto_payment_list_bill_cron";
                $other_params['payment_date']=$today;
                $pay_lb_res = $listBillObj->pay_list_bill($list_bill_row['list_bill_id'],$list_bill_row['billing_id'],$location,$other_params); //list bill pay for inside payment
            }
        }
    }
} 
// end global setting to inside payment of list bill


echo "<br>Process Complete";
dbConnectionClose();
?>