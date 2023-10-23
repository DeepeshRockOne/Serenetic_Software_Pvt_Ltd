<div class="row">
<?php
    if($is_main_products){
?>
    <div class="col-md-9">
        <div class="row m-b-30">
            <div class="col-sm-6">
                <h2 class="mn">Recommended Benefits</h2>
            </div>
            <div class="col-sm-6">
                <p class="text-right m-t-7">To elect individual plans, use <span data-toggle="tooltip" data-container="body" data-trigger="hover" title="SELF GUIDING BENEFITS" data-placement="bottom"><a href="javascript:void(0);" class="form_submit" data-step="3">Self Guiding Benefits</a></span></p>
            </div>
        </div>
        <?php 
         $bundledata = [];
         $productPrice = [];
         foreach ($recommendedapiResponse['data'] as $key => $value) {
                  $bundledata[$value['bundleLabel']] = $key;
         }
        foreach($recommendedapiResponse['data'] as $key => $value){ ?>
        <div class="recom-bundle-block bundleblock"  id="changecolor<?= $key ?>">
            <div class="bundle-block-head">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                     <?php if($value['mostRecommended'] == 'Y'){ ?>
                        <a href="javascript:void(0);" class="btn-buddle text-white badge-recommend">Most Recommended </a>
                     <?php } ?>   
                        <div class="m-b-0 m-t-10 p-l-15 text-white text-uppercase fs18 d-inline"><?= $value['bundleLabel'] ?></div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="text-right">
                        <span class="dropdown bundle-compare">
                                 <a href="javascript:void(0);" class="dropdown-toggle btn-buddle text-white " data-toggle="dropdown"> <i class="fa fa-exchange p-r-10" aria-hidden="true"></i> Compare Bundle </a>
                                 <ul  class="dropdown-menu">
                                    <div class="list-scroll">
                                     <?php foreach($bundledata as $bundlekey => $bundlevalue) {?>
                                        <li ><span><input type="checkbox" class="compareBundle" data-id="<?=$value['bundleLabel']?>" id="bundle<?=$bundlekey . $bundlevalue?>" value="<?= $bundlevalue ?>" name="bundleID[]"><label for="bundle<?=$bundlekey . $bundlevalue?>">&nbsp;</label></span><?= $bundlekey ?></li>
                                     <?php } ?>
                                     </div>
                                   <div class="text-center p-b-10 p-t-10">
                                       <a href="javascript:void(0);" data-toggle="tooltip" data-container="body" data-trigger="hover" id="comparebundle" title="Compare Bundle" data-placement="bottom" data-bunleid="<?=$value['bundleLabel']?>" class="btn-buddle btn btn-info compare_bundle"> Bundle </a>
                                    </div>
                                 </ul>
                        </span>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-container="body" data-trigger="hover" title="ELECT BUNDLE" data-placement="bottom" class="btn-buddle bg-white text-black electbundle" id="electbundle<?= $key ?>" data-id="<?= $key ?>" data-bundle-label="<?= $value['bundleLabel'] ?>"><i class="" id="icon<?= $key ?>" aria-hidden="true"></i><span  id="elect_bundle_text<?=$key?>">ELECT BUNDLE</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bundle-block-body">
                <div class="row">
                  <?php
                  $benifit = 0;
                  $matrix =[];
                   foreach($value['Products'] as $k => $v){ ?>
                    <div class="col-lg-4 col-md-6 col-sm-6" id="removeproduct<?= $value['bundleID'].'_'.$k ?>">
                        <div class="plan-bundle">
                            <div class="plan-bundle-remove">
                                <div class="checkbox-v">
                                    <label class="font-bold" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Remove from Bundle" data-placement="bottom"><input type="checkbox" data-bundleID = "<?=$value['bundleID'];?>" data-prdId = "<?=$k?>" data-pricing_model="<?=$v['pricing_model']?>" class="removeplan removep<?= $value['bundleID'].'_'.$k ?> remove_<?=$k?>" data-productid ="<?= $value['bundleID'].'_'.$k ?>" value="<?=$k?>" name="removed_product[<?=$value['bundleID']?>][<?=$k?>]">Remove Plan</label>
                                </div>
                            </div>
                            <div class="bundle-remove-holder" id="removedbundle<?=$value['bundleID'].'_'.$k?>" style="display:none">
                                <table cellspacing="0" cellpadding="0" width="100%" height="100%" border="0">
                                    <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <h4 class="mn font-bold">
                                                    Removed
                                                    <div class="d-block"> From Bundle</div>
                                                </h4>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="plan-bundle-title d-clearfix">
                                <div class="pull-left">
                                    <h5 class="mn font-bold" id="product_name_<?=$k?>"><?= $v['product_name'] ?></h5>
                                </div>
                                <div class="pull-right"><a href="<?= $HOST?>/group_enroll_planinfo.php?productId=<?= $v['product_code'] ?>" class="group_enroll_planinfo"><i class="fa fa-eye fs18"></i></a></div>
                            </div>
                            <div class="plan-bundle-banner" style="background-image:url(<?= $HOST ?>/images/prd_preview/thumbnail/hugging_girl_in_wheelchair.jpg);">
                            </div>
                            <div class="p-15">
                                <p class="m-b-20"><?= $value['recommandationReason'] ?></p>
                                <div class="form-group height-auto">
                                <input type="hidden" name="bundle_product_price[<?=$key?>][<?=$k?>]" id="bundle_product_price_<?=$key?><?=$k?>" value="0.00" data-excluded-product-for=""  data-is-required-for="" data-required-product="" data-packaged-product-for="~packaged_products~" class="hidden_product_price" data-product-id="<?=$k?>">
                                <input type="hidden" name="bundle_display_product_price[<?=$key?>][<?=$k?>]" id="bundle_display_product_price_<?=$key?><?=$k?>" value="0.00" class="hidden_product_price" data-product-id="<?=$k?>">
                                <input type="hidden" name="bundle_product_matrix[<?=$key?>][<?=$k?>]" id="bundle_product_matrix_<?=$key?><?=$k?>" value="0" data-product-id="<?=$k?>">
                                <input type="hidden" name="bundle_product_category[<?=$key?>][<?=$k?>]" id="bundle_product_category_<?=$key?><?=$k?>" value="" class="hidden_product_category" data-product-id="<?=$k?>">
                                <input type="hidden" name="" class="percentage_of_salary_<?=$k?>" value="<?=checkIsset($v['percentage_of_salary'])?>">
                                    <input type="hidden" name="" class="monthly_benefit_allowed_<?=$k?>" value="<?=checkIsset($v['monthly_benefit_allowed'])?>">
                                <input type="hidden" name="benefit_tierName[<?=$key?>][<?=$k?>]" value="" id="benefit_tierName_<?=$key?>_<?=$k?>">
                                <input type="hidden" name="temp_bundle_pricing_model[<?=$key?>][<?=$k?>]" value="<?=$v['pricing_model']?>" id="temp_bundle_pricing_model_<?=$key?>_<?=$k?>">
                                <?php if($v['pricing_model'] != 'FixedPrice'){ ?> 
                                    <input type="hidden" name="bundle_product_is_calculated[<?=$key?>][<?=$k?>]" value="N" id="bundle_product_is_calculated_<?=$key?>_<?=$k?>">
                                <?php } ?>
                                    <select class="form-control benefit-tier prd_benefit_tier prd_benefit_tier<?= $key ?>" data-pricing_model ='<?=$v['pricing_model']?>' id="bundle_product_benefit_tier_<?= $key ?><?= $k ?>" data-product-id="<?= $k ?>" data-bundlId ='<?= $key ?>' name = 'bundle_product_benefit_tier[<?=$key?>][<?=$k?>]'>
                                        <!-- <option data-hidden="true"></option> -->
                                        <?php $matrix = $v['pricing_model']=='VariableEnrollee' ? $v['Enrollee_Matrix'] : $v['Matrix'];
                                        if($v['pricing_model']=='VariableEnrollee'){
                                        ?>
                                        <option data-hidden="true"></option>
                                      <?php } ?>
                                        <?php foreach($matrix as $planId => $Matrixvalue) { ?>
                                        
                                        <option data-deduction="<?=$v['deduction']?>" data-plan_name="<?= $Matrixvalue['plan_name'] ?>" value="<?= $planId ?>"  data-price='<?= $Matrixvalue['product_price'] ?>' data-prd-matrix-id="<?=$Matrixvalue['matrix_id']?>" data-display-price="<?= $Matrixvalue['display_member_price'] ?>" <?= !empty($_POST['bundle_product_benefit_tier'][$key][$k]) && $_POST['bundle_product_benefit_tier'][$key][$k] == $planId ? 'selected' : '' ?> ><?= $Matrixvalue['plan_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <label>Benefit Tier</label>
                                </div>
                            </div>
                            <?php 
                                foreach ($matrix as $prdK => $prdV) {
                                  // $productPrice[] = $Matrixvalue['product_price'];
                                    if($v['pricing_model'] =='VariablePrice'){
                                        if($prdV['plan_name'] == 'Member Only'){
                                            $productPrices = '<sub>Starting at </sub>'.displayAmount($prdV['display_member_price']);
                                            $productPrice[] = $prdV['display_member_price'];
                                        }else{
                                            $productPrices = '<sub>Starting at </sub>'.displayAmount($prdV['display_member_price']);
                                            $productPrice[] = $prdV['display_member_price'];
                                        }
                                    }elseif ($v['pricing_model']=='VariableEnrollee') {
                                        $productPrice[] = $prdV['display_member_price'];
                                        $productPrices = '<sub>Starting at </sub>'.displayAmount($prdV['display_member_price']);;
                                    }else {
                                        $productPrice[] = $prdV['display_member_price'];
                                        $productPrices = displayAmount($prdV['display_member_price']);

                                    } break; 
                            }?>
                            <div class="plan-bundle-price d-clearfix">
                                <div id="prices<?= $key.$k ?>" class="pull-left">
                                    <?= $productPrices;?> <?php echo $v['pricing_model'] == 'FixedPrice' ? '<sub>/ pay period</sub>' : '' ?>
                                </div>
                                <?php if($v['pricing_model'] != 'FixedPrice') { ?>
                                <div class="text-right">
                                    <a href="javascript:void(0)" data-href="<?=$HOST?>/exact_estimate.php" class="exact_estimate calculatePlanSelf" data-toggle="tooltip" data-trigger="hover" data-product-id="<?= $k ?>"  data-pricing-model="<?= $v['pricing_model'] ?>"  id="bundlecalculatePlanSelf_<?=$k?>" data-recommanded-tab = '1' data-placement="bottom" title="GET EXACT RATE" data-bundleid ='<?= $key ?>'>
                                        <span class="material-icons-outlined">playlist_add_check</span>
                                    </a>
                                    <div style="display: none;">
                                        <div class="bundleCalculate_<?=$k?> calclate_rate_popup_<?=$k?>"></div>  
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="m-b-15 clearfix selecte<?= $key ?>" style="display:none">
                            <a href="javascript:void(0);" class="btn btn-lg btn-success btn-block">Selected</a>
                        </div>
                        </div>
                    <?php
                  $benifit++;
                } ?>  
                </div>
            </div>
            <p id="bundleSelectError<?= $key ?>" class="error"></p>
            <div class="bundle-block-foot">
                <div class="font-bold text-white text-right">
                    <span class="p-r-10">Bundle Total</span><span bablic-exclude>$</span><span id="bundleTotal_<?= $key ?>"><?= str_replace('$','',displayAmount(array_sum($productPrice))) ?>/ pay period</span>
                </div>
            </div>
            <div class="b-t m-b-30 m-t-30"></div>
        </div>
        <?php unset($productPrice); }?>
    </div>
    <div class="col-md-3">
        <?php
            $page_location = "bundle_page_";
        ?>
        <?php include('bundle_sidebar.inc.php'); ?>
    </div>
</div>
<?php }else{ ?>
    <div class="row m-b-30">
        <div class="col-sm-6">
            <h3 class="mn">No products found for Bundles</h2>
        </div>
    </div>
<?php } ?>
    </div>
<div class="clearfix">
    <button type="button" class="btn btn-action form_submit" data-step="3">Continue</button>
    <button class="enrollmentLeftmenuItem" id="questionTabBack"><a href="javascript:void(0);" class="btn red-link" data-step="2">Back</a></button>
</div>