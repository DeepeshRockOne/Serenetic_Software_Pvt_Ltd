<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();
$fee_id = $_POST['fee_id'];
$ids = $_POST['ids'];
$is_clone = $_POST['is_clone'];
$feeRow = array();
$incr="";
$temp_fees = "";

if(isset($_SESSION['temp_fee_products'])){
  $temp_fees = implode(',', $_SESSION['temp_fee_products']);
  if($ids){
    $ids .= "," . $temp_fees;
  }else{
    $ids .= $temp_fees;
  }
}
if(!empty($ids)){
  $incr.=" AND paf.fee_id in ($ids)";
}

if($fee_id && $is_clone == 'N'){

  $feeSql="SELECT p.id,paf.fee_id,paf.id as f_id,p.name,p.product_code,count(DISTINCT pp.id) as total_products,pm.pricing_effective_date,pm.pricing_termination_date,pm.price,p.status 
        FROM prd_fees pf
        JOIN prd_assign_fees paf on (pf.id = paf.prd_fee_id AND paf.is_deleted = 'N')
        JOIN prd_main p on paf.fee_id=p.id
        JOIN prd_matrix pm on pm.product_id = p.id
        LEFT JOIN prd_main pp ON(paf.product_id = pp.id AND FIND_IN_SET(paf.prd_fee_id,pp.membership_ids) AND pp.is_deleted = 'N')
        WHERE pf.id = :fee_id AND pm.is_deleted='N' AND p.is_deleted='N' $incr group by p.id";
  $feeRow=$pdo->select($feeSql, array(':fee_id' => $fee_id));

}else{
  if($ids){
    $feeSql="SELECT p.id,p.name,paf.fee_id,paf.id as f_id,p.product_code,count(DISTINCT paf.product_id) as total_products,pm.pricing_effective_date,pm.pricing_termination_date,pm.price,p.status 
          FROM prd_assign_fees paf
          JOIN prd_main p on paf.fee_id=p.id
          JOIN prd_matrix pm on pm.product_id = p.id
          WHERE pm.is_deleted='N' AND p.is_deleted='N' AND paf.is_deleted = 'N' $incr group by p.id";
    $feeRow=$pdo->select($feeSql);

  }
}


ob_start();
?>
<div class="table-responsive">
    <table class="<?=$table_class?> ">
        <thead>
            <tr class="data-head">
                        <th width="250px"><a href="javascript:void(0);" data-column="id">Fee</a>
                        </th>

                        <th width="150px"><a href="javascript:void(0);" data-column="id">Fee ID</a>
                        </th>

                        <th class="text-center"><a href="javascript:void(0);" data-column="fname">Products</a>
                        </th>
                        
                        <th><a href="javascript:void(0);" data-column="type">Effective Date</a>
                        </th>

                        <th><a href="javascript:void(0);" data-column="type">Termination Date</a>
                        </th>

                        <th><a href="javascript:void(0);" data-column="type">Fee Price</a>
                        </th>
                        
                        <th width="15%"><a href="javascript:void(0);" data-column="status">Status</a>
                        </th>
                        
                        <th  width="100px" >Actions</th>
                    </tr>
        </thead>
        <tbody>
            <?php if($feeRow){?>
                <?php foreach ($feeRow as $key => $value) { ?>
                   <tr>
                       <td><?=$value['name']?></td>
                       <td><a href="add_membership_fee.php?fee_id=<?= $value['id'] ?>&membership_fee_id=<?= $fee_id ?>" class="add_vendor_fee"><strong class="text-red"><?=$value['product_code']?></strong></a></td>
                       <td class="text-center"><a href="membership_prd_popup.php?id=<?=$fee_id;?>&fee_id=<?=$value['fee_id']?>" class="fw500 add_vendor_fee"><strong class="text-red"><?=$value['total_products']?></strong></a></td>
                       <td><?=date('m/d/Y',strtotime($value['pricing_effective_date']))?></td>
                       <td><?=strtotime($value['pricing_termination_date']) ? date('m/d/Y',strtotime($value['pricing_termination_date'])) : "-"?></td>
                       <td>$<?=$value['price']?></td>
                       <td>
                        <div class="theme-form pr w-130">
                       <select name="fee_status_<?= $value['id'] ?>" class="form-control fee_status has-value" data-id="<?= md5($value['id']) ?>"  data-old_status="<?=$value['status']?>" id="fee_status_<?= $value['id'] ?>"> 
                           <option value="Active" <?=$value['status'] == 'Active' ? "selected = 'selected'" : ""?>>Active</option>
                           <option value="Inactive" <?=$value['status'] == 'Inactive' ? "selected = 'selected'" : ""?>>Inactive</option>
                       </select>
                       <label>Select</label>
                     </div>
                       </td>
                       <td class="icons text-center" width="125px">
                            <a href="add_membership_fee.php?fee_id=<?= $value['id'] ?>&membership_fee_id=<?= $fee_id?>&is_clone=Y" class="add_vendor_fee m-r-5"><i class="fa fa-clone" aria-hidden="true"></i></a> 
                            <a href="add_membership_fee.php?fee_id=<?= $value['id'] ?>&membership_fee_id=<?= $fee_id ?>" class="add_vendor_fee m-r-5"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> 
                            <a href="javascript:void(0);" class="m-r-5 delete_vendor_fee" data-id="<?= md5($value['id']); ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                   </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="bg_light_bg text-left">
                    <a href="add_membership_fee.php?membership_fee_id=<?= $fee_id ?>" class="add_vendor_fee btn btn-info">Add Fee</a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<?php
$response['membership_fee_div']=ob_get_clean();
$response['status']="success";

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>