<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();
$sch_params = array();
$incr=""; 
$carrier_id = checkIsset($_POST['carrier_id']); 
$carrier_fee_id = checkIsset($_POST['carrier_fee_id']);

if(!empty($carrier_fee_id) && !empty($carrier_id)){
  $sch_params[":carrier_id"] = makeSafe($carrier_id);
  $incr.=" AND ( pm.id in (".$carrier_fee_id.") OR md5(pm.prd_fee_id)=:carrier_id )";
}else if(!empty($carrier_fee_id) && empty($carrier_id)){
  $incr.=" AND pm.id in (".$carrier_fee_id.")";
}else if(empty($carrier_fee_id) && !empty($carrier_id)){
  $sch_params[":carrier_id"] = makeSafe($carrier_id);
  $incr.=" AND md5(pm.prd_fee_id)=:carrier_id";
}

if(!empty($carrier_fee_id) || !empty($carrier_id)){
  $feeSql="SELECT md5(pm.id) as id,pm.fee_type,pm.is_benefit_tier,pm.product_code,pm.name,pm.status,pmx.pricing_effective_date,pmx.pricing_termination_date,pmx.price as prd_price,pmx.price_calculated_on,pm.prd_fee_id,count(DISTINCT p.id) as total_products 
          FROM prd_main pm
          JOIN prd_matrix pmx on (pmx.product_id=pm.id)
          JOIN prd_assign_fees pa on (pa.fee_id=pm.id)
          LEFT JOIN prd_main p ON(pa.product_id = p.id AND p.carrier_id = pa.prd_fee_id AND p.is_deleted = 'N')
          WHERE pm.is_deleted = 'N' AND pmx.is_deleted = 'N' AND pa.is_deleted = 'N'". $incr ." 
          GROUP BY pa.fee_id
          ORDER BY pm.id";
  $feeRow=$pdo->select($feeSql,$sch_params); 
}

ob_start();
?>
<div class="table-responsive">
  <table class="<?=$table_class?>">
    <thead>
      <tr>
        <th>Fee Name</th>
        <th>Fee ID</th>
        <th>Type</th>
        <th class="text-center">Products</th>
        <th>Effective Date</th>
        <th>Termination Date</th>
        <th class="text-center">Fee Price</th>
        <th width="10%">Status</th>
        <th class="text-center" width="130px">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($feeRow)){ ?>
        <?php foreach ($feeRow as $fee) { ?>
           
          <tr id="row_<?= $fee['id']; ?>">
              <td><?= $fee['name'] ?> </td>
              <td><a href="add_carrier_fee.php?fee_id=<?= $fee['id'] ?>&carrier_id=<?= $carrier_id ?>&carrier_fee_id=<?= $carrier_fee_id ?>" class="add_carrier_fee"><strong class="text-red"><?= $fee['product_code'] ?></strong></a></td>
              <td><?= $fee['fee_type'] ?></td>
              <td  class="text-center">
                <a href="view_fee_products_details.php?id=<?= $fee['id'] ?>&name=<?= $fee['name'] ?>&display_id=<?= $fee['product_code'] ?>&count=<?= $fee['total_products'] ?>&carrier_id=<?=$carrier_id?>" class="fw600 text-red carrie_productsr_details"><?php echo $fee['total_products']; ?></a>
              </td>
              <td><?= !empty($fee['pricing_effective_date']) ? date($DATE_FORMAT,strtotime($fee['pricing_effective_date'])) : '-'; ?></td>
              <td><?= !empty($fee['pricing_termination_date']) ? date($DATE_FORMAT,strtotime($fee['pricing_termination_date'])) : '-'; ?></td>
              <td class="text-center">
                <?php 
                  $prd_price = '$'.$fee['prd_price'];
                  if($fee['price_calculated_on'] == 'Percentage'){
                    $prd_price=(floor($fee['prd_price']*100)/100).'%';
                  } 
                ?>
                <?php if($fee['is_benefit_tier']=='Y'){ ?>
                  <a href="view_plan_price.php?id=<?= $fee['id'] ?>&name=<?= $fee['name'] ?>&display_id=<?= $fee['product_code'] ?>" class="fw600 text-red carrie_productsr_details"><strong><?= $prd_price; ?> +</strong></a>
                <?php }else{ ?>
                  <strong><?= $prd_price; ?></strong>
                <?php } ?>
              </td>
              <td>
                <div class="theme-form pr w-130">
                <select name="fee_status" class="form-control fee_status has-value" data-id="<?= $fee['id'] ?>" data-old_status="<?=$fee['status']?>" id="fee_status_<?= $fee['id'] ?>">
                  <option value="Active" <?=$fee['status']=="Active" ? 'selected' : '' ?> >Active </option>
                  <option value="Inactive" <?=$fee['status']=="Inactive" ? 'selected' : '' ?>>Inactive</option>
                </select>
                <label>Select</label>
              </div>
              </td>
              <td class="icons text-right">
                <a href="add_carrier_fee.php?fee_id=<?= $fee['id'] ?>&carrier_id=<?= $carrier_id ?>&is_clone=Y&carrier_fee_id=<?= $carrier_fee_id ?>" class="add_carrier_fee"><i class="fa fa-clone" aria-hidden="true"></i></a>
                <a href="add_carrier_fee.php?fee_id=<?= $fee['id'] ?>&carrier_id=<?= $carrier_id ?>&carrier_fee_id=<?= $carrier_fee_id ?>" class="add_carrier_fee"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                <a href="javascript:void(0);" class="delete_fee" data-id="<?= $fee['id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
              </td>
          </tr>
        <?php } ?>
      <?php } ?>
    </tbody>
    <tfoot>
    <tr>
      <td colspan="9" class="bg_light_bg text-left">
        <a href="add_carrier_fee.php?carrier_id=<?=$carrier_id?>&carrier_fee_id=<?= $carrier_fee_id ?>" class="add_carrier_fee btn btn-info">Add Fee</a>
      </td>
    </tr>
    </tfoot>
  </table>
</div>
<?php
$response['carrier_fee_div']=ob_get_clean();
$response['status']="success";

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>