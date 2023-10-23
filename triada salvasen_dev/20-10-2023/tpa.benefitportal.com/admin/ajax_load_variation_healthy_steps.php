<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';

$response = array();

$sch_params = array();
$incr=""; 
$fee_ids = checkIsset($_GET['fee_ids']);
$added_fee_ids = checkIsset($_GET['added_fee_ids']);
$fee_id = checkIsset($_GET['fee_id']);
$agent_id = checkIsset($_GET['agent_id']);
$delete_fee_id = checkIsset($_GET['delete_id']);

if(!empty($delete_fee_id)){
    $fee_ids = explode(',',$fee_ids);
    $fee_ids = array_flip($fee_ids);
    unset($fee_ids[$delete_fee_id]);
    $fee_ids = array_flip($fee_ids);
    $fee_ids = implode(',',$fee_ids);
}
$tblincr = "LEFT JOIN agent_product_rule apr ON(apr.product_id=parent.id and apr.is_deleted='N' and apr.agent_id=:agent_id)";

if(!empty($fee_id)){
    $tblincr = "JOIN agent_product_rule apr ON(apr.product_id=parent.id and apr.is_deleted='N' and apr.agent_id=:agent_id)";
}
$totalRecords = '';
$fetchRecords = array();
if(!empty($fee_ids) && !empty($agent_id)){

    if(!empty($agent_id)){
        $sch_params[':agent_id'] = $agent_id;
        $incr.='AND apr.agent_id=:agent_id ';
    }
    $has_querystring =false;
    if (count($sch_params) > 0) {
        $has_querystring = true;
    }
    
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
        $has_querystring = true;
        $per_page = $_GET['pages'];
    }
    
    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
    $options = array(
        'results_per_page' =>50,
        'url' => 'ajax_load_variation_healthy_steps.php?'. $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );
    
    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);
        try {

    $selRecords ="SELECT p.id as id,pf.id as health_id,p.fee_renewal_type,GROUP_CONCAT(DISTINCT (paf.product_id)) as products,
    p.fee_renewal_count,p.create_date,p.name,parent.product_code as variation_product_code,p.product_code,p.is_fee_on_commissionable,p.is_member_benefits,
    pm.pricing_effective_date,pm.pricing_termination_date,pmp.description,pm.price as prd_price ,pm.commission_amount,pm.non_commission_amount,pmp.is_member_portal,apr.status as product_status
    FROM prd_main p
    LEFT JOIN prd_matrix pm ON(pm.product_id=p.id and pm.is_deleted='N' )
    LEFT JOIN prd_main parent ON(parent.parent_product_id=p.id and parent.is_deleted='N')
    $tblincr
    LEFT JOIN prd_assign_fees paf ON(paf.fee_id=p.id  and paf.is_deleted='N')
    LEFT JOIN prd_fees pf ON( paf.prd_fee_id = pf.id AND pf.is_deleted='N')
    LEFT JOIN prd_member_portal_information pmp ON(pmp.product_id=p.id AND pmp.is_deleted='N')
    WHERE p.type='Fees' AND p.product_type='Healthy Step' AND p.record_type='Primary' and p.is_deleted='N' and p.id in(".$fee_ids.") ".(!empty($added_fee_ids)?(" AND (p.id NOT IN(".$added_fee_ids.") OR apr.id IS NOT NULL)"):"")." GROUP BY p.id";

        $paginate_records = new pagination($page, $selRecords, $options);
        if ($paginate_records->success == true) {
            $fetchRecords = $paginate_records->resultset->fetchAll();
            $totalRecords = count($fetchRecords);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}
ob_start();
?>
<div class="table-responsive" id="variation_data">
    <table class="<?=$table_class?>">
    <thead>
        <tr>
        <th width="20%">ID/Added Date</th>
        <th width="15%">Name</th>
        <th class="text-center" >Fee Price</th>
        <th width="25%">Status</th>
        <th width="70px">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if($totalRecords > 0){  $fee_ids =array(); ?>
            <?php foreach($fetchRecords as $row){ 
                array_push($fee_ids,$row['id']);
            }
                $fee_ids = implode(',',$fee_ids);
            ?>
            <input type="hidden" name="available_fees" id="available_fees" value="<?=$fee_ids?>">
            <?php foreach($fetchRecords as $row){ ?>
        <tr id="row_id_<?=$row['id']?>">

        <input type="hidden" name="termination_date[<?=$row['id']?>]" id="termination_date_<?=$row['id']?>" value="<?=!empty($row['pricing_termination_date']) && getCustomDate($row['pricing_termination_date']) !='-' ? getCustomDate($row['pricing_termination_date']) : '' ?>" >
        <input type="hidden" name="non_commissionable_amount[<?=$row['id']?>]" id="non_commissionable_amount_<?=$row['id']?>" value="<?=checkIsset($row['non_commission_amount'])?>">
        <input type="hidden" name="is_fee_commissionable[<?=$row['id']?>]" id="is_fee_commissionable_<?=$row['id']?>" value="<?=$row['is_fee_on_commissionable']?>">
        <input type="hidden" name="is_member_benefits[<?=$row['id']?>]" id="is_member_benefits_<?=$row['id']?>" value="<?=$row['is_member_benefits']?>">
        <input type="hidden" name="is_member_portal[<?=$row['id']?>]" id="is_member_portal_<?=$row['id']?>" value="<?=$row['is_member_portal']?>">
        <input type="hidden" name="description[<?=$row['id']?>]" id="description_<?=$row['id']?>" value="<?=htmlspecialchars($row['description'])?>">
        <input type="hidden" name="benifit_period[<?=$row['id']?>]" id="benifit_period_<?=$row['id']?>" value="<?=$row['fee_renewal_type']?>">
        <input type="hidden" name="month[<?=$row['id']?>]" id="month_<?=$row['id']?>" value="<?=$row['fee_renewal_count']?>">
        <input type="hidden" name="selected_products[<?=$row['id']?>]" id="selected_products_<?=$row['id']?>" value="<?=$row['products']?>">
        <input type="hidden" name="effective_date[<?=$row['id']?>]" id="effective_date_<?=$row['id']?>" value="<?=!empty($row['pricing_effective_date']) && getCustomDate($row['pricing_effective_date']) !='-' ? getCustomDate($row['pricing_effective_date']) : '' ?>">
        <input type="hidden" name="commissionable_amount[<?=$row['id']?>]" id="commissionable_amount_<?=$row['id']?>" value="<?=$row['commission_amount']?>">
        <input type="hidden" name="fee_price[<?=$row['id']?>]" id="fee_price_<?=$row['id']?>" value="<?=$row['prd_price']?>">
        <input type="hidden" name="healthy_steps[<?=$row['id']?>]" id="healthy_steps_<?=$row['id']?>" value="<?=$row['id']?>">
        <td><a href="javascript:void(0);" class="fw500 text-action"><?=!empty($row['variation_product_code'])?$row['variation_product_code']:'-'?></a><br><?=getCUstomDate($row['create_date'])?></td>
        <td><?=$row['name']?><br/><a href="javascript:void(0);" class="fw500 text-action"><?=$row['product_code']?></a></td>
        <td class="text-center">$<?=$row['prd_price']?></td>
        <td>
            <div class="theme-form pr w-200">
                <select class="form-control sel_status" name="product_status[<?=$row['id']?>]">
                    <option data-hidden="true"></option>
                    <?php if($row['product_status'] == ''){ ?>
                        <option value='Contracted' selected="selected">Active</option>
                    <?php }else{ ?>
                        <option value='Contracted' <?=$row['product_status'] == 'Contracted' ? 'selected="selected"' : '' ?>>Active</option>
                    <?php } ?>
                    
                    <option value='Pending Approval' <?=$row['product_status'] == 'Pending Approval' ? 'selected="selected"' : '' ?>>Inactive</option>
                </select>
                <label>Select</label>
            </div>
        </td>
        <td class="icons">
            <a href="javascript:void(0);" onclick="window.parent.delete_fee(<?=$row['id']?>)"><i class="fa fa-trash"></i></a>
        </td>
        </tr>
        <?php } ?>
        <?php } else echo '<tr><td colspan="5">No rows found!</td></tr>'; ?>
        
        <tr>
        <td colspan="5"><a href="javascript:void(0);" data-href="healthy_steps_popup.php?fee_ids=<?=$fee_ids?>" class="btn btn-info healthy_steps_popup" id="healthy_steps_popup">+ Assign</a></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
        <?php if($totalRecords > 0 && !empty($fetchRecords)) { ?>
            <td colspan="6">
            <?php echo $paginate_records->links_html; ?>
            </td>
        <?php } ?>
        </tr>
    </tfoot>
    </table>
</div>
<script type="text/javascript">
$(document).ready(function() {
$(".healthy_steps_popup").colorbox({
    iframe: false,
    width: '800px',
    height: '450px'
  });
common_select();
});
</script>
<?php
$response['varition_fee_div']=ob_get_clean();
$response['status']="success";

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>