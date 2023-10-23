<div class="panel panel-default">
    <div class="panel-body">
        <form action="" id="bundle_recommendation">
            <input type="hidden" name="bundleID" id="bundleID" value="0">
            <input type="hidden" id="editBundleID" name="editBundleID" value="0">
            <input type="hidden" name="QuestionID" id="QuestionID" value="0">
            <input type="hidden" name="editQuestionID" id="editQuestionID" value="0">
            <input type="hidden" name="comparisonRowID" id="comparisonRowID" value="0">
            <input type="hidden" name="editcomparisonRowID" id="editcomparisonRowID" value="0">
            <input type="hidden" name="TopRecommanded" id="TopRecommanded" value="0">
            <input type="hidden" name="skipCompairions" id="skipCompairions" value="N">
            <input type="hidden" name="editskipCompairionsId" id="editskipCompairionsId" value="<?=!empty($bundleInformId)? $bundleInformId:'0'?>">
            <input type="hidden" name="totalBundleForGroup" id="totalBundleForGroup" value="0">
            <div style='display: none;' id="groupProducts">        
                   <?php  if(!empty($fetch_rows['productData'])){
                            foreach ($fetch_rows['productData'] as $key => $company) { ?>
                                <optgroup label="<?=$key?>">
                                    <?php
                                        foreach ($company as $pkey => $value) { ?>
                                            <?php 
                                                $productIds = !empty($rows['product_ids']) ? explode(',',$rows['product_ids']) : 0;
                                                if(is_array($productIds)){
                                                    $selectedProductIds = in_array($value['id'],$productIds) ? 'selected' : '';
                                                } else {
                                                    $selectedProductIds = $value['id'] == $productIds ? 'selected' : '';
                                                }
                                            ?>
                                            <option value ="<?=$value['id'];?>"<?= $selectedProductIds ?>><?= $value['name'].' '.'('.$value['product_code'].')'?></option>
                                       <?php } ?>  
                                </optgroup>
                            <?php } ?>
                    <?php } ?>
            </div>
            <div class="theme-form">
                <!-- Bundle Step 1 -->
                    <?php include_once 'create_bundle.inc.php'; ?>
                <!-- Bundle Step 1 -->

                <div id="step_1_hr" style="display:none"><hr></div>

                <!-- Bundle Step 2 -->
                    <?php include_once 'create_quetions.inc.php'; ?>
                <!-- Bundle Step 2 -->

                <div id="step_2_hr" style="display:none"><hr></div>

                <!-- Bundle Step 3 -->
                <?php include_once 'create_comparison.inc.php'; ?>
                <!-- Bundle Step 3 -->
                <hr / class="m-t-30 m-b-30">
                   <div class="clearfix text-center">
                      <a href="javascript:void(0);" class="btn btn-action save_recommendation" id="save_recommendation" style="<?=!empty($groupIds) && !empty($GetBundleQuestionDetails) ? '' :'display: none';?>">Save Recommendation</a>
                      <a href="javascript:void(0);" class="btn red-link  cancel_recommendation" id="cancel_recommendation" style="<?=!empty($groupIds) ? '' :'display: none';?>">Cancel</a>
                   </div>
            </div>

        </form>
    </div>
</div>
<div class="panel panel-default panel-block">
  <div class="list-group">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
  </div>
</div>
<?php include('bundle_dynamic_content.inc.php'); ?>
<?php include('add_update_bundle_jquery.inc.php'); ?>