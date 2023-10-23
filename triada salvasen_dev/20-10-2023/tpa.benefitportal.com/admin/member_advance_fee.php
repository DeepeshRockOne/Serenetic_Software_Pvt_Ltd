<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(62);
$advRuleId = !empty($_GET['advRuleId']) ? $_GET['advRuleId'] : 0;
$advFeeId = !empty($_GET['advFeeId']) ? $_GET['advFeeId'] : 0;
$advFeeIds = !empty($_GET['advFeeIds']) ? $_GET['advFeeIds'] : '';
$ruleType = !empty($_GET['ruleType']) ? $_GET['ruleType'] : '';
$product_ids = array();

$agentId = isset($_GET['agentId']) ? $_GET['agentId'] : 0;
$is_clone = (isset($_GET['is_clone']) && $_GET['is_clone'] == 'Y') ? 'Y' : 'N';

$prdIncr = "";
$prdParams = array();

if(checkIsset($ruleType) == "Global"){
  $prdIncr .= " AND p.record_type='Primary'";
}

if(!empty($agentId)){
	$productSql="SELECT p.id,p.name,p.product_code,p.type,c.title,p.record_type
	            FROM prd_main p
	            LEFT JOIN prd_category c ON (c.id = p.category_id)
              LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
	            WHERE p.type!='Fees' AND p.name != '' AND p.is_deleted='N' AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]') $prdIncr
              GROUP BY p.id ORDER BY p.name ASC";
	$productRes = $pdo->selectGroup($productSql,$prdParams,'title');
}else{
	$productSql="SELECT p.id,p.name,p.product_code,p.type,c.title 
	            FROM prd_main p
	            LEFT JOIN prd_category c ON (c.id = p.category_id)
              LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
	            WHERE p.type!='Fees' AND p.name != '' AND p.is_deleted='N' AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]') $prdIncr
              GROUP BY p.id ORDER BY name ASC";
	$productRes = $pdo->selectGroup($productSql,$prdParams,'title');
}

if(!empty($advFeeId)){
    $feeSql = "SELECT pm.id,pm.name,pm.product_code,pmt.price_calculated_on,pmt.price_calculated_type,pm.initial_purchase,pm.is_benefit_tier,pm.is_fee_on_renewal,pm.fee_renewal_type,pm.fee_renewal_count,pmt.pricing_effective_date,pmt.pricing_termination_date,pm.advance_month,pmt.price,pm.pricing_model,pm.id as advFeeId
      FROM prd_main pm
      JOIN prd_matrix pmt ON(pm.id=pmt.product_id)
      WHERE pm.is_deleted = 'N' AND md5(pm.id) = :fee_id ORDER BY pm.id";
    $feeRow = $pdo->selectOne($feeSql, array(":fee_id" => $advFeeId));
}
   
if(!empty($feeRow)){
    $processing_fee = 0;

    $advance_month = $feeRow['advance_month'];
    $display_id = $feeRow['product_code'];
    $pricingModel = $feeRow['pricing_model'];
    $price_calculated_on = $feeRow['price_calculated_on'];
    $price_calculated_type = $feeRow['price_calculated_type'];
    $effective_date = !empty($feeRow['pricing_effective_date']) ? date('m/d/Y',strtotime($feeRow['pricing_effective_date'])) : '';
    $termination_date = !empty($feeRow['pricing_termination_date']) ? date('m/d/Y',strtotime($feeRow['pricing_termination_date'])) : '';
    
    $is_fee_on_new_business = $feeRow['initial_purchase'];
    $is_benefit_tier = $feeRow['is_benefit_tier'];
    $is_fee_on_renewal = $feeRow['is_fee_on_renewal'];
    $renewal_type = $feeRow['fee_renewal_type'];
    $number_of_renewals = $feeRow['fee_renewal_count'];
    $processing_fee = !empty($feeRow['price']) ? $feeRow['price'] : 0;

    if($is_clone == 'Y'){
        $display_id = get_advance_comm_fee_id();
    }else{
      $assignSql = "SELECT group_concat(product_id) as product_ids 
                  FROM prd_assign_fees
                  WHERE is_deleted='N' AND md5(fee_id)=:fee_id ";
      $assignRow = $pdo->selectOne($assignSql, array(":fee_id" => $advFeeId));
      $fee_product_list = $assignRow['product_ids'];
      $product_ids = !empty($fee_product_list) ? explode(",", $fee_product_list) : array();

        //************* Activity Code Start *************
          $description['ac_message'] =array(
          'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Read Advance Commission Rule ',
          'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/add_agent_advance_global_rule.php?advanceId='.$advRuleId,
          'title'=> $display_id,
          ),
          );
          activity_feed(3, $_SESSION['admin']['id'], 'Admin', $feeRow['id'], 'prd_main',"Admin Read Advance Fee", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        //************* Activity Code End *************
    }

    $pricing_range = $pdo->select("SELECT pmt.id as matId,pmt.price as fee,pmc.min_total,pmc.max_total FROM prd_matrix pmt JOIN prd_matrix_criteria pmc ON(pmt.id=pmc.prd_matrix_id AND pmc.is_deleted='N')  WHERE pmc.product_id = :prdId AND pmt.is_deleted = 'N'",array(':prdId' => $feeRow['advFeeId']));
}else{
  $advance_month = NULL;
	$display_id = get_advance_comm_fee_id();
	$effective_date = "";
	$termination_date = "";
	$price_calculated_on = "Amount";
	$processing_fee = "";
	$is_fee_on_new_business = 'N';
	$is_fee_on_renewal = 'N';
	$renewal_type = 'Continuous';
	$number_of_renewals = 0;
	$price_calculated_type = 'Amount';
	$pricing_range = array();
  $pricingModel = '';
}

$assigned_products = array();

if($ruleType == 'Global'){
  $globalIncr = "";
  $globalParams = array();
  if(!empty($product_ids)){
    $globalIncr .= " AND pa.product_id NOT IN(".implode(',',$product_ids).")";
  }
  $selGlobPrd = "SELECT GROUP_CONCAT(DISTINCT(pa.product_id)) as productIds
                  FROM prd_fees pf 
                  JOIN prd_assign_fees pa ON(pf.id=pa.prd_fee_id AND pa.is_deleted='N')
                  JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.pricing_termination_date IS NULL)
                  WHERE pf.setting_type='ServiceFee' AND pf.rule_type='Global' AND pf.is_deleted='N' $globalIncr";
  $resGlobPrd = $pdo->selectOne($selGlobPrd,$globalParams);
  $assigned_products = !empty($resGlobPrd['productIds']) ? explode(",", $resGlobPrd['productIds']) : array();
}else{

  $varyIncr = "";
  $varySchParams = array();
  if(!empty($product_ids)){
    $varyIncr .= " AND pa.product_id NOT IN(".implode(',',$product_ids).")";
  }
  if(!empty($agentId)){
    $varyIncr .= " AND md5(pf.agent_id) = :agentId"; 
    $varySchParams[":agentId"] = $agentId;
  }
  $selVaryPrd = "SELECT GROUP_CONCAT(DISTINCT(pa.product_id)) as productIds
                  FROM prd_fees pf 
                  JOIN prd_assign_fees pa ON(pf.id=pa.prd_fee_id AND pa.is_deleted='N')
                  JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.pricing_termination_date IS NULL)
                  WHERE pf.setting_type='ServiceFee' AND pf.rule_type='Variation' AND pf.is_deleted='N' $varyIncr";
  $resVaryPrd = $pdo->selectOne($selVaryPrd,$varySchParams);
  $assigned_products = !empty($resVaryPrd['productIds']) ? explode(",", $resVaryPrd['productIds']) : array();
}

$resRulePrd = array();
if(!empty($advFeeIds)){
  $incr = "";
  if(!empty($product_ids)){
    $incr .= " AND paf.product_id NOT IN(".implode(',',$product_ids).")";
  }
  $selRulePrd = "SELECT GROUP_CONCAT(DISTINCT(paf.product_id)) AS productIds
      FROM prd_assign_fees paf
      JOIN prd_matrix pm ON(paf.fee_id=pm.product_id AND pm.pricing_termination_date IS NULL) 
      WHERE paf.is_deleted='N' AND pm.is_deleted='N' AND paf.fee_id IN(".$advFeeIds.") $incr";
  $resRulePrd = $pdo->selectOne($selRulePrd);
}

if(!empty($resRulePrd["productIds"])){
  $asgProducts = explode(",", $resRulePrd["productIds"]);
  $assigned_products = array_unique (array_merge ($assigned_products, $asgProducts));
}


$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache, 'thirdparty/bootstrap-datepicker-master/css/datepicker.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js','thirdparty/price_format/jquery.price_format.2.0.js');

$template = "member_advance_fee.inc.php";
include_once 'layout/iframe.layout.php';
?>