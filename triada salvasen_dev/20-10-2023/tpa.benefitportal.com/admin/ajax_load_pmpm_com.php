<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();
$fee_id = $_POST['pmpm_id'];
$ids = $_POST['ids'];
$is_clone = $_POST['is_clone'];
$feeRow = array();
$incr="";
$temp_fees = "";


if(!empty($ids)){
  $incr.=" AND pcr.id in ($ids)";
}

if($fee_id && $is_clone == 'N'){

  $feeSql="SELECT pcr.id,pcr.commission_id,pcr.display_id,pcr.created_at,count(DISTINCT pm.id) as total_products,count(DISTINCT pcraa.agent_id) as total_agents,pcr.effective_date,pcr.termination_date,pcr.earned_on_new_business,pcr.earned_on_renewal,pcrpt.amount,pcr.status 
        FROM pmpm_commission_rule pcr
        JOIN pmpm_commission_rule_plan_type pcrpt on (pcr.id = pcrpt.rule_id AND pcrpt.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_product pcrap on (pcr.id = pcrap.rule_id AND pcrap.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_agent pcraa on (pcr.id = pcraa.rule_id AND pcraa.is_deleted = 'N')
        LEFT JOIN prd_main pm on(pm.id = pcrap.product_id AND pm.is_deleted = 'N')
        WHERE md5(pcr.commission_id) = :fee_id AND pcr.is_deleted='N' $incr group by pcr.id";
  $feeRow=$pdo->select($feeSql, array(':fee_id' => $fee_id));

}else{
  if($is_clone == "Y") {
    $fee_id = 0;
  }
  if($ids){
    $feeSql="SELECT pcr.id,pcr.commission_id,pcr.display_id,pcr.created_at,count(DISTINCT pm.id) as total_products,count(DISTINCT pcraa.agent_id) as total_agents,pcr.effective_date,pcr.termination_date,pcr.earned_on_new_business,pcr.earned_on_renewal,pcrpt.amount,pcr.status 
        FROM pmpm_commission_rule pcr
        JOIN pmpm_commission_rule_plan_type pcrpt on (pcr.id = pcrpt.rule_id AND pcrpt.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_product pcrap on (pcr.id = pcrap.rule_id AND pcrap.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_agent pcraa on (pcr.id = pcraa.rule_id AND pcraa.is_deleted = 'N')
        LEFT JOIN prd_main pm on(pm.id = pcrap.product_id AND pm.is_deleted = 'N')
        WHERE pcr.is_deleted='N' $incr group by pcr.id";
    $feeRow=$pdo->select($feeSql);

  }
}

ob_start();
?>
<div class="table-responsive">
    <table class="<?=$table_class?> ">
        <thead>
            <tr class="data-head">
                <th ><a href="javascript:void(0);" data-column="id">ID/Added Date</a>
                </th>

                <th class="text-center"><a href="javascript:void(0);" data-column="id">Products #</a>
                </th>

                <th class="text-center"><a href="javascript:void(0);" data-column="fname">Agents #</a>
                </th>
                
                <th class="text-center"><a href="javascript:void(0);" data-column="type">Earned New Business</a>
                </th>

                <th class="text-center"><a href="javascript:void(0);" data-column="type">Earned Renewals</a>
                </th>

                <th><a href="javascript:void(0);" data-column="type">Effective Date</a>
                </th>
                
                <th ><a href="javascript:void(0);" data-column="status">Termination Date</a>
                </th>

                <th class="text-center"><a href="javascript:void(0);" data-column="status">PMPM Amount</a>
                </th>
                <th ><a href="javascript:void(0);" data-column="status">Status</a>
                </th>
                <th width="130px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($feeRow){?>
                <?php foreach ($feeRow as $key => $value) { ?>
                   <tr>
                       <td><strong class="text-red"><?=$value['display_id']?></strong> <br> <?=date('m/d/Y',strtotime($value['created_at']))?></td>
                       <td class="text-center"><a href="pmpm_fee_prd_popup.php?id=<?=$value['id'];?>" class="popup text-action fw500"><?=$value['total_products']?></a></td>
                       <td class="text-center"><a href="pmpm_fee_agent_popup.php?id=<?=$value['id'];?>" class="popup text-action fw500"><?=$value['total_agents']?></a></td>
                       <td class="text-center"><?=$value['earned_on_new_business'] == 'Y' ? "Yes" : 'No'?></td>
                       <td class="text-center"><?=$value['earned_on_renewal'] == 'Y' ? "Yes" : "No" ?></td>
                       <td ><?=date('m/d/Y',strtotime($value['effective_date']))?></td>
                       <td><?=strtotime($value['termination_date']) ? date('m/d/Y',strtotime($value['termination_date'])) : "-"?></td>
                       <td class="text-center">$<?=$value['amount']?></td>
                       <td>
                       <select class="form-control fee_status" data-old_status="<?=$value['status']?>" id="fee_status_<?=$value['id']?>" name="status" data-id="<?=$value['id']?>">
                            <option value="Active" <?=$value['status'] == 'Active' ? "selected = 'selected'" : ""?>>Active</option>
                            <option value="Inactive" <?=$value['status'] == 'Inactive' ? "selected = 'selected'" : ""?>>Inactive</option>
                          </select>
                        </td>
                       <td class="icons " >
                            <a href="add_pmpm_fee.php?pmpm_id=<?= $value['commission_id'] ?>&pmpm_fee_id=<?= $value['id']?>&is_clone=Y" class="add_pmpm_fee" data-toggle="tooltip" title="Duplicate"><i class="fa fa-clone" aria-hidden="true"></i></a> 
                            <a href="add_pmpm_fee.php?pmpm_id=<?= $value['commission_id'] ?>&pmpm_fee_id=<?= $value['id'] ?>" class="add_pmpm_fee" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> 
                            <a href="javascript:void(0);" class="delete_vendor_fee" data-toggle="tooltip" title="Delete" data-id="<?= $value['id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                   </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="bg_light_bg text-left">
                    <a href="add_pmpm_fee.php?pmpm_id=<?= $fee_id ?>" class="add_pmpm_fee btn btn-info">Add Fee</a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<?php
$response['pmpm_fee_div']=ob_get_clean();
$response['status']="success";

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>