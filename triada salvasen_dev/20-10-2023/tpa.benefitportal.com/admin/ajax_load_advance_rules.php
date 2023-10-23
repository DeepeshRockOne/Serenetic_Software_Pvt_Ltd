<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();
$feeRow = array();
$sch_params = array();
$incr="";

$advRuleId = checkIsset($_POST['advRuleId']); 
$advFeeIds = checkIsset($_POST['advFeeIds']);
$chargedTo = isset($_POST['chargedTo']) ? $_POST['chargedTo'] : 'Agents';
$ruleType = isset($_POST['ruleType']) ? $_POST['ruleType'] : 'Variation';
$agentId = isset($_POST['agentId']) ? $_POST['agentId'] : '';
$agentId = !empty($agentId) ? md5($agentId) : '';
$is_clone = !empty($_POST['is_clone']) ? $_POST['is_clone'] : 'N';

$popupFile = "agent_advance_fee.php";
if($chargedTo == 'Members'){
  $popupFile = "member_advance_fee.php";
}

if($is_clone == "Y"){
  $incr.=" AND pm.id in (".$advFeeIds.")";
}else{
  if(!empty($advFeeIds) && !empty($advRuleId)){

    $incr.=" AND ( pm.id in (".$advFeeIds.") OR md5(pm.prd_fee_id)=:advRuleId )";
    $sch_params[":advRuleId"] = makeSafe($advRuleId);

  }else if(!empty($advFeeIds) && empty($advRuleId)){

    $incr.=" AND pm.id in (".$advFeeIds.")";

  }else if(empty($advFeeIds) && !empty($advRuleId)){

    $sch_params[":advRuleId"] = makeSafe($advRuleId);
    $incr.=" AND md5(pm.prd_fee_id)=:advRuleId";

  }
}

if(!empty($advFeeIds)){
  $feeSql="SELECT md5(pm.id) as advFeeId,pm.product_code,pm.create_date as addedDate,count(DISTINCT pa.id) as total_products,pm.advance_month,pmx.price as prd_price,pmx.price_calculated_on,pmx.pricing_effective_date,pmx.pricing_termination_date,pf.charged_to,md5(pf.id) as advRuleId,pm.pricing_model
          FROM prd_main pm
          JOIN prd_matrix pmx on (pmx.product_id=pm.id)
          JOIN prd_assign_fees pa on (pa.fee_id=pm.id )
          LEFT JOIN prd_fees pf ON(pa.prd_fee_id=pf.id)
          WHERE pm.is_deleted = 'N' AND pmx.is_deleted = 'N' AND pa.is_deleted = 'N' ". $incr ." 
          GROUP BY pa.fee_id
          ORDER BY pm.id";
  $feeRow=$pdo->select($feeSql,$sch_params);  
}
ob_start();
?>
<div class="table-responsive">
  <table class="<?=$table_class?> ">
    <thead>
      <tr class="data-head">
        <th ><a href="javascript:void(0);" data-column="id">ID/Added Date</a></th>
        <th class="text-center"><a href="javascript:void(0);" data-column="id">Products #</a></th>
        <th class="text-center"><a href="javascript:void(0);" data-column="fname">Advance Months</a></th>
        <th class="text-center"><a href="javascript:void(0);" data-column="type">Service Fee(s)</a></th>
        <th><a href="javascript:void(0);" data-column="type">Effective Date</a></th>
        <th><a href="javascript:void(0);" data-column="status">Termination Date</a></th>
        <th width="130px">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($feeRow){?>
      <?php foreach ($feeRow as $key => $fee) { ?>
        <tr>
            <td><a href="<?=$popupFile?>?advFeeId=<?= $fee['advFeeId'] ?>&advRuleId=<?= $fee['advRuleId'] ?>&advFeeIds=<?= $advFeeIds ?>&agentId=<?=$agentId?>&ruleType=<?=$ruleType?>" class="addAdvanceFee"><span class="text-red"><strong><?=$fee['product_code']?></strong></span></a><br><?=date('m/d/Y',strtotime($fee['addedDate']))?></td>
            <td class="text-center"><a href="advance_commission_product.php?advFeeId=<?=$fee['advFeeId']?>" class="advPrdPopup text-red"><strong><?=$fee['total_products']?></strong></a></td>
            <td class="text-center"><?=$fee['advance_month']?></td>

           <?php if($fee['charged_to'] == 'Agents'){ ?>
              <td class="text-center"><?=$fee['price_calculated_on'] == 'Percentage' ? $fee['prd_price'] . '%' : '$' . $fee['prd_price']?></td>
            <?php }else{ 
              if($fee['pricing_model'] == "FixedPrice"){
            ?>
              <td class="text-center"><?='$' . $fee['prd_price']?></td>
            <?php 
              } else{ 
                $selPricingRange = "SELECT pmt.price,pmc.min_total,pmc.max_total 
                                    FROM prd_matrix pmt 
                                    JOIN prd_matrix_criteria pmc ON(pmt.id=pmc.prd_matrix_id AND pmc.is_deleted='N')
                                    WHERE md5(pmt.product_id) = :advFeeId AND pmt.is_deleted='N'";

                $resPricingRange = $pdo->select($selPricingRange,array(':advFeeId' => $fee['advFeeId']));
            ?>
              <td class="icons text-center">
                <a href="javascript:void(0)" data-toggle="popover"
                data-html="true"
                data-placement="top"
                data-content="
                <table class='<?=$table_class?>'>
                  <thead>
                    <tr>
                      <th>Fee</th>
                      <th>From</th>
                      <th>To</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if($resPricingRange){ ?>
                    <?php foreach($resPricingRange as $range){ ?>
                    <tr>
                      <td>$<?=$range['price']?></td>
                      <td>$<?=$range['min_total']?></td>
                      <td>$<?=$range['max_total']?></td>
                    </tr>
                    <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>">
                <i class="fa fa-eye "></i>
                </a>
              </td>
            <?php } 
                } 
            ?>
            <td><?= !empty($fee['pricing_effective_date']) ? date($DATE_FORMAT,strtotime($fee['pricing_effective_date'])) : '-'; ?></td>
            <td><?= !empty($fee['pricing_termination_date']) ? date($DATE_FORMAT,strtotime($fee['pricing_termination_date'])) : '-'; ?></td>
            <td class="icons">
              <a href="<?=$popupFile?>?advFeeId=<?=$fee['advFeeId']?>&advRuleId=<?= $fee['advRuleId'] ?>&is_clone=Y&advFeeIds=<?=$advFeeIds?>&agentId=<?=$agentId?>&ruleType=<?=$ruleType?>" class="addAdvanceFee"><i class="fa fa-clone" aria-hidden="true"></i></a>
              <a href="<?=$popupFile?>?advFeeId=<?= $fee['advFeeId']?>&advRuleId=<?= $fee['advRuleId'] ?>&advFeeIds=<?= $advFeeIds ?>&agentId=<?=$agentId?>&ruleType=<?=$ruleType?>" class="addAdvanceFee"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
              <a href="javascript:void(0);" class="delAdvanceFee" data-id="<?= $fee['advFeeId']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
            </td>
        </tr>
          <?php } ?>
        <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="10" class="bg_light_bg text-left">
        <a href="<?=$popupFile?>?advFeeIds=<?=$advFeeIds?>&agentId=<?=$agentId?>&ruleType=<?=$ruleType?>&advRuleId=<?= $advRuleId ?>" class="addAdvanceFee btn btn-info">+ Advance</a>
        </td>
      </tr>
    </tfoot>
</table>
</div>
<?php
$response['advanceFeeDiv']=ob_get_clean();
$response['status']="success";

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>