<div id="deleteMatrixBtnDiv" style="display: none;">
    <div class="clearfix">
        <div class="pull-right m-b-10">
          <a href="javascript:void(0);" id="deleteMatrixBtn" class="btn btn-info">DELETE</a>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr>
            <th>Plan Tier</th>
            <?php if(!empty($prdPricingQuestionRes)) {  ?>
                <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                    <th class="<?= $value['id'] ?>PriceRow" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>"><?= $value['display_label'] ?></th>
                <?php }?>
            <?php } ?>
            <th>Retail Price</th>
            <th>Non-Comm</th>
            <th>Comm</th>
            <th>Effective</th>
            <th>Termination</th>
            <th width="130px">Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $matrixGroup => $matrixGroupRows) {
                    $isApplied = false;
                    foreach ($matrixGroupRows as $mainKey => $rows) { ?>
                    <?php 
                    if(!empty($rows['enrolleeMatrix'])){
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?= !empty($rows['matrixPlanType']) ? $prdPlanTypeArray[$rows['matrixPlanType']]['title'] : '-' ?></td>
                        <?php if(!empty($prdPricingQuestionRes)) { ?>
                            <?php foreach ($prdPricingQuestionRes as $key => $value) { ?>
                                <td class="<?= $value['id'] ?>PriceRow" style="<?= !empty($price_control) && in_array($value['id'], $price_control) ? '' : 'display: none' ?>">
                                    <?= $value['control_class']=='text_amount' ? '$' : '' ?><?= !empty($rows[$value['id']]['matrix_value']) ? $rows[$value['id']]['matrix_value'] : '-' ?><?= $value['control_class']=='text_percentage' ? '%' : '' ?></td>
                            <?php } ?>
                        <?php } ?>
                        <td><?= !empty($rows['RetailPrice']) ? "$".$rows['RetailPrice'] : '-' ?></td>
                        <td><?= !empty($rows['NonCommissionablePrice']) ? "$".$rows['NonCommissionablePrice'] : '-' ?></td>
                        <td><?= !empty($rows['CommissionablePrice']) ? "$".$rows['CommissionablePrice'] : '-' ?></td>
                        <td><?= !empty($rows['pricing_matrix_effective_date']) ? date($DATE_FORMAT,strtotime($rows['pricing_matrix_effective_date'])) : '-' ?></td>
                        <td><?= !empty($rows['pricing_matrix_termination_date']) ? date($DATE_FORMAT,strtotime($rows['pricing_matrix_termination_date'])) : '-' ?></td>
                        <td class="icons">
                            <?php if($allowPricingUpdate && !$isApplied) { ?>
                                <a href="javascript:void(0)" data-toggle="tooltip" title="Clone" data-trigger="hover" data-container="#pricingMatrixIframeDiv" class="matrixPricingClone" data-click-type="Clone" data-row-id="<?= $rows['keyID'] ?>">
                                    <i class="fa fa-clone"></i>
                                </a>
                            <?php } ?>
                            
                            <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="matrixPricingEdit" data-click-type="Edit" data-trigger="hover" data-container="#pricingMatrixIframeDiv" data-row-id="<?= $rows['keyID'] ?>">
                                <i class="fa fa-edit"></i>
                            </a>
                            
                            <?php if($allowPricingUpdate && !$isApplied) { ?>
                                <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-trigger="hover" data-container="#pricingMatrixIframeDiv" class="matrixPricingDelete" data-row-id="<?=  $rows['keyID'] ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php } ?>
                            <?php if($allowPricingUpdate && !$isApplied) { ?>
                                <input type="checkbox" name="pricingMatrixRowDel[]" class="js-switch deletePricingMatrixBox" value="<?=$rows['keyID']?>"/>
                            <?php } ?>
                        </td> 
                    </tr>
                    <?php $isApplied = true; ?>
                    <?php }?>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="23" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
        
    </table>
</div>