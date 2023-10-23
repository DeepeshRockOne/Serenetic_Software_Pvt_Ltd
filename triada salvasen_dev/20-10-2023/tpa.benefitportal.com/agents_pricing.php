<?php
include_once __DIR__ . '/includes/connect.php';
$product_id = $_GET['product_id'];
$tbl_incr ='';
$incr='';
$order_by_incr=' FIELD(pm.plan_type,1,3,2,5,4)';
$from = checkIsset($_REQUEST['from']);

$prd_main = $pdo->selectOne("SELECT name,pricing_model from prd_main where id=:id",array(":id"=>$product_id));

if($prd_main['pricing_model'] !='VariableEnrollee'){
    $sqlCheckPricingQue="SELECT prd_pricing_question_id from prd_pricing_question_assigned where is_deleted='N' AND product_id=:product_id";
    $resCheckPricingQue=$pdo->select($sqlCheckPricingQue,array(":product_id"=>$product_id));
    $price_control = array();

    if(!empty($resCheckPricingQue)){
        foreach ($resCheckPricingQue as $key => $value) {
            array_push($price_control, $value['prd_pricing_question_id']);
        }
    }

    if(!empty($price_control)){
        $tbl_incr .= ' LEFT JOIN prd_matrix_criteria pmc ON(pmc.prd_matrix_id = pm.id and pmc.is_deleted="N") ';
        $incr .= " pmc.*, ";
        $order_by_incr .= " ,pmc.age_from,pmc.tobacco_status";
    }
    $prd_sql = "SELECT $incr pm.price,pm.plan_type from prd_matrix pm $tbl_incr where pm.product_id = :product_id  
    AND CURDATE() >= DATE(pm.pricing_effective_date) AND (IF(pm.pricing_termination_date!='',CURDATE() < DATE(pm.pricing_termination_date),1)) 
    AND pm.is_deleted='N' order by $order_by_incr";
    $products = $pdo->select($prd_sql,array(":product_id"=>$product_id));
}

$template = 'agents_pricing.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>