<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr>
            <tr>
            <th>Fee</th>
            <th>Fee ID</th>
            <th>Type</th>                
            <th class="text-center">Products</th>
            <?php if($groupEnrollmentPrd == 'N'){ ?>
            <th>Effective Date</th>
            <th>Termination Date</th>
            <?php } ?>
            <th class="text-center">Fee Price</th>
            <th width="15%">Status</th>
            <th width="130px">Actions</th>
            </tr>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $mainKey => $fee) { ?>
                    <tr id="row_<?= $fee['id']; ?>">
                      <td><?= $fee['name'] ?> </td>
                      <td><strong class="text-red"><?= $fee['product_code'] ?></strong></td>
                      <td><?= $fee['fee_type'] ?></td>
                      <td  class="text-center">
                        <?php echo $fee['total_products']; ?>
                      </td>
                      <?php if($groupEnrollmentPrd == 'N'){ ?>
                        <td><?= !empty($fee['pricing_effective_date']) ? date($DATE_FORMAT,strtotime($fee['pricing_effective_date'])) : '-'; ?></td>
                        <td><?= !empty($fee['pricing_termination_date']) ? date($DATE_FORMAT,strtotime($fee['pricing_termination_date'])) : '-'; ?></td>
                      <?php } ?>
                      <td class="text-center">
                        <?php 
                          $prd_price = '$'.$fee['prd_price'];
                          if($fee['price_calculated_on'] == 'Percentage'){
                            $prd_price=(floor($fee['prd_price']*100)/100).'%';
                          } 
                        ?>
                        <?php if($fee['is_benefit_tier']=='Y'){ ?>
                          <a href="javascript:void(0)" class="fw600 text-red productFeePrice" data-row-id="<?= $fee['keyID'] ?>"><strong><?= $prd_price; ?> +</strong></a>
                        <?php }else{ ?>
                          <strong><?= $prd_price; ?></strong>
                        <?php } ?>
                      </td>
                      <td>
                        <div class="theme-form pr w-200">
                        <select name="fee_status" class="form-control fee_status" data-old_status="<?=$fee['status']?>" id="fee_status_<?= $fee['keyID'] ?>" data-row-id="<?= $fee['keyID'] ?>">
                          <option value="Active" <?=$fee['status']=="Active" ? 'selected' : '' ?> >Active </option>
                          <option value="Inactive" <?=$fee['status']=="Inactive" ? 'selected' : '' ?>>Inactive</option>
                        </select>
                      </div>
                      </td>
                      <td class="icons">
                      <?php if($groupEnrollmentPrd == 'N'){ ?>
                        <a href="javascript:void(0)" data-toggle="tooltip" title="Clone" class="productFeeClone" data-click-type="Clone" data-row-id="<?= $fee['keyID'] ?>">
                                <i class="fa fa-clone"></i>
                        </a>
                        <?php } ?>
                        <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" class="productFeeEdit" data-click-type="Edit" data-row-id="<?= $fee['keyID'] ?>">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="productFeeDelete" data-row-id="<?= $fee['keyID'] ?>">
                            <i class="fa fa-trash"></i>
                        </a>
                      </td>
                  </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="23" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
            <!-- Need to remove Add Fee button once a single admin fee is added -->
            <tr style="<?= ($groupEnrollmentPrd == 'Y' && !empty($total_rows)) ? 'display:none' : '' ?>">
                <td colspan="9" class="bg_light_gray">
                    <a href="javascript:void(0)" class="btn btn-primary productFeeAdd">Add Fee</a>
                </td>           
            </tr>
        </tbody>
        
    </table>
</div>