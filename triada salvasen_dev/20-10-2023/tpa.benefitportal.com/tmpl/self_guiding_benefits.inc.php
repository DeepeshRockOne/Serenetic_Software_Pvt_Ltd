<?php if(!empty($finalArray)){ ?>
<div class="group_member_enroll">
    <div class="theme-form">
        <div id="self_guiding_benefits">
            <h2 class="m-t-0 m-b-30">Self Guiding Benefits</h2>
            <div class="row">
                <div class="col-md-9">
                    <div class="tab-content mn">
                        <?php $prevCategoryId = null; ?>
                        <?php foreach ($finalArray as $key => $categoryRow) {
                            $categoryId = checkIsset($categoryRow['category_id']);
                            $categoryName = checkIsset($categoryRow['category_name']);
                            $productListCount = checkNumberSet($categoryRow['product_list_count']);
                            $productArr = checkIsset($categoryRow['productArr'], 'arr');
                            $nextCategoryId = checkIsset(next($finalArray)['category_id']);
                        ?>
                            <div role="tabpanel" class="tab-pane category_class" id="categoryDiv<?= $key ?>">
                                <input type="hidden" name="category_id" id="category_id" value="<?= $categoryId ?>">
                                <input type="hidden" id="categoryKey" value="<?= $key ?>">
                                <input type="hidden" id="category_step" value="<?= $key ?>">
                                <input type="hidden" name="all_category[]" id="all_category" value="<?= $categoryId ?>">
                                <div class="">
                                    <div class="guide-hearo">
                                        <div class="bg_dark_primary p-15 fs24 fw700 text-white"> <?= $categoryName ?> </div>
                                        <div class="guide-hearo-thumb m-b-30" style="background-image:url(<?= $HOST ?>/images/prd_preview/thumbnail/hugging_girl_in_wheelchair.jpg);"> </div>
                                        <p>Displaying <?= $productListCount . ' ' . $categoryName ?></p>
                                        <hr>
                                    </div>
                                    <?php foreach ($productArr as $pkey => $productRow) {
                                        $productId = checkIsset($productRow['productId']);
                                        $productName = checkIsset($productRow['productName']);
                                        $companyName = checkIsset($productRow['companyName']);
                                        $effectiveDate = !empty($productRow['effectiveDate']) ? $productRow['effectiveDate'] : '';
                                        $deduction = !empty($productRow['deduction']) ? $productRow['deduction'] : '';
                                        $availableState = checkIsset($productRow['availableState']);
                                        $requiredProduct = checkIsset($productRow['requiredProduct']);
                                        $excludedProduct = checkIsset($productRow['excludedProduct']);
                                        $productDescription = checkIsset($productRow['productDescription']);
                                        $productMatrix = checkIsset($productRow['matrix'], 'arr');
                                        $pricingModel = checkIsset($productRow['pricingModel']);
                                        $is_add_on_product = checkIsset($productRow['is_add_on_product']);
                                        $category_id = checkIsset($productRow['category_id']);

                                        $packaged_flag = false;
                                        $packaged_product_ids = "";
                                        $packaged_product_name = "";
                                        if(!empty($combination_array[$productId])){
                                            if(!empty($combination_array[$productId]['Packaged'])){
                                                $packaged_product_ids = $combination_array[$productId]['Packaged']['product_id'];
                                                $packaged_product_name = $combination_array[$productId]['Packaged']['product_name'];
                                                $packaged_flag = true;
                                            }
                                        }
                                    ?>
                                        <input type="hidden" name="product_id[]" value="<?= $productId ?>">
                                        <input type="hidden" name="tmp_pricing_model[]" class="pricing_model_<?= $productId ?>" value="<?= $pricingModel ?>">
                                        <input type="hidden" name="product_price[<?=$productId?>]" id="product_price_<?=$productId?>" value="0.00" data-excluded-product-for=""  data-is-required-for="" data-required-product="" data-packaged-product-for="<?= $packaged_product_ids ?>" class="hidden_product_price" data-product-id="<?=$productId?>">
                                        <input type="hidden" name="display_product_price[<?=$productId?>]" id="display_product_price_<?=$productId?>" value="0.00" class="hidden_product_price" data-product-id="<?=$productId?>">
                                        <input type="hidden" name="product_matrix[<?=$productId?>]" id="product_matrix_<?=$productId?>" value="0" data-product-id="<?=$productId?>"><input type="hidden" name="product_category[<?=$productId?>]" id="product_category_<?=$productId?>" value="<?=$category_id?>" data-product-id="<?=$productId?>">
                                        <input type="hidden" name="waive_products[<?=$categoryId?>][]" class="waive_products_<?=$categoryId?>" value="<?=$productId?>">
                                        <input type="hidden" name="" class="percentage_of_salary_<?=$productId?>" value="<?=checkIsset($productRow['percentage_of_salary'])?>">
                                        <input type="hidden" name="" class="monthly_benefit_allowed_<?=$productId?>" value="<?=checkIsset($productRow['monthly_benefit_allowed'])?>">

                                        <div class="single-guide-block <?=!empty($packaged_flag) ? 'packaged_body' : '' ?>" id="product_list_<?= $productId ?>">
                                            <div class="guide-block-head">
                                                <div class="row">
                                                    <div class="col-md-5 col-sm-5">
                                                        <h2 class="mn font-bold" id="product_name_<?=$productId?>"><?= $productName ?></h2>
                                                        <p><?= $companyName ?></p>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4">
                                                        <div id="excluded_content_<?=$productId?>" style="display: none">
                                                            <div class="plan-center">
                                                                <h2 class="font-bold m-t-0">Excluded</h2>
                                                                <p class="mn">This product is excluded because you added <span id="excluded_content_product_name_<?=$productId?>"></span>.</p>
                                                            </div>
                                                        </div>
                                                    </div>                
                                                    <?php if(!empty($packaged_flag)){
                                                     ?>
                                                        <div class="col-md-4 col-sm-4">
                                                            <div id="packaged_content_<?=$productId?>" >
                                                                <div class="plan-center">
                                                                    <h2 class="font-bold m-t-0">Packaged</h2>
                                                                    <p class="mn">This product is excluded until you add at at least one of the following <span id="packaged_content_product_name_<?=$productId?>"> <?= $packaged_product_name ?></span>.</p>
                                                                </div>
                                                              </div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-md-3 col-sm-3">
                                                        <div class="text-right">
                                                            <span id="span_product_view_detail_<?= $productId ?>" data-toggle="tooltip" data-trigger="hover" data-container="body" title="View Details" data-placement="bottom">
                                                                <a class="btn red-link productViewDetail" id="product_view_detail_<?= $productId ?>" data-product-id="<?= $productId ?>" role="button" data-toggle="collapse" href="#plandetails<?= $productId ?>" aria-expanded="false" aria-controls="plandetails<?= $productId ?>"><i class="fa fa-eye fa-lg p-r-5"></i> View Details</a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse" id="plandetails<?= $productId ?>">
                                                <div class="p-15 bg_dark_primary text-white text-center">
                                                    <h4 class="mn">Details</h4>
                                                </div>
                                                <div class="guide-block-body ">
                                                    <div class="panel-body b-b">
                                                        <div class="clearfix" style="display:<?= empty($effectiveDate) ? 'none' : '' ?>">
                                                            <h5 class="fw700">Effective Date</h5>
                                                            <p class="mn"><?= $effectiveDate ?></p>
                                                        </div>
                                                        <div class="m-t-30" style="display:<?= empty($availableState) ? 'none' : '' ?>">
                                                            <h5 class="m-t-0 fw700">Available States</h5>
                                                            <p class="mn"><?= $availableState ?></p>
                                                        </div>
                                                        <div class="m-t-30" style="display:<?= empty($requiredProduct) ? 'none' : '' ?>">
                                                            <h5 class="m-t-0 fw700">Products Required</h5>
                                                            <p class="mn"><?= $requiredProduct ?></p>
                                                        </div>
                                                        <div class="m-t-30" style="display:<?= empty($excludedProduct) ? 'none' : '' ?>">
                                                            <h5 class="m-t-0 fw700">Products Excluded</h5>
                                                            <p class="mn"><?= $excludedProduct ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="clearfix">
                                                            <h5 class="fw700"><?= $productDescription ?></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="guide-block-foot bg_dark_primary">
                                                <div class="d-flex">
                                                    <div>
                                                        <h2 class="mn text-white font-bold "><span class = "product_price_label" id="product_price_label_<?= $productId ?>"></span></h2>
                                                    </div>
                                                    <div>
                                                        <div class="phone-control-wrap">
                                                            <div class="phone-addon text-left">
                                                                <div class="pr">
                                                                    <input type="hidden" class="selected_product<?= $key ?>" name="selected_product_id[<?= $productId ?>]" id="selected_product_id_<?= $productId ?>" value="">
                                                                    <select class="form-control benefittier_type" name="product_benefit_tier[<?= $productId ?>]" id="product_benefit_tier_<?= $productId ?>" data-product-id="<?= $productId ?>" data-category-id="<?=$categoryId?>" >
                                                                        <?php if (!empty($productMatrix)) { ?>
                                                                            <?php
                                                                            foreach ($productMatrix as $mkey => $matrix) {
                                                                                $planName = checkIsset($matrix['planName']);
                                                                                $planId = checkIsset($matrix['planId']);
                                                                                $matrixId = checkIsset($matrix['matrixId']);
                                                                                $productPrice = checkIsset($matrix['productPrice']);$displayProductPrice = checkIsset($matrix['displayProductPrice']);
                                                                            ?>
                                                                                <option data-deduction="<?=$deduction?>" data-plan_name="<?= $planName ?>" value="<?= $planId ?>" data-prd-matrix-id='<?= $matrixId ?>' data-price='<?= $productPrice ?>' data-display-price='<?=$displayProductPrice?>'><?= $planName ?> </option>
                                                                            <?php } ?>
                                                                        <?php } ?>
                                                                    </select>
                                                                    <label>Benefit Tier</label>
                                                                    <span class="error" id="error_product_benefit_tier_<?= $productId ?>"></span>
                                                                </div>
                                                            </div>
                                                            <?php if($pricingModel == "FixedPrice"){ ?>
                                                            <div class="phone-addon w-110"><a href="javascript:void(0);" class="btn-buddle btn-block bg-white text-black addPlanself" id="addPlanself_<?= $productId ?>" data-product-id="<?= $productId ?>" data-pricing-model="<?= $pricingModel ?>" data-is-add-on-product="<?=$is_add_on_product?>" data-category-id="<?=$category_id?>">Add Plan</a></div>
                                                            <?php }else{ ?>
                                                            <div class="phone-addon w-110"><a href="javascript:void(0);" data-href="<?=$HOST?>/exact_estimate.php" class="btn-buddle btn-block bg-white text-black calculatePlanSelf" id="calculatePlanSelf_<?= $productId ?>" data-product-id="<?= $productId ?>" data-pricing-model="<?= $pricingModel ?>" data-is-add-on-product="<?=$is_add_on_product?>" data-category-id="<?=$category_id?>">Calculate Rate</a></div>
                                                            <div style="display: none;">
                                                                <div class="selfGuidingCalculate_<?=$productId?> calclate_rate_popup_<?=$productId?>"></div>  
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($pricingModel == "VariablePrice"){ ?>
                                            <div class="calculate_question_class" id="calulate_question_<?=$productId?>">
                                            </div>
                                        <?php } ?>
                                        <div class="b-t m-b-40 m-t-40"></div>
                                    <?php } ?>
                                </div>
                                <div class="clearfix">
                                <?php if(!empty($nextCategoryId)) { ?>
                                    <a href="#categoryDiv<?= $nextCategoryId ?>" class="btn btn-action selfGuidingBenefitsHref" id="category<?= $nextCategoryId ?>" role="tab" data-toggle="tab" aria-controls="categoryDiv<?= $nextCategoryId ?>" data-next-category-id="<?= $nextCategoryId ?>" data-category-id="<?=$categoryId?>" data-from="self-guiding" data-step="4" data-category-step="<?= $nextCategoryId ?>">Continue</a>
                                <?php }else{ ?>
                                    <button type="button" class="btn btn-action form_submit" data-step="4" data-category-id="<?=$categoryId?>" data-from="self-guiding" data-category-steps="<?= $key ?>">Continue</button>
                                <?php } ?>
                                <?php if($prevCategoryId) { ?>
                                    <a href="#categoryDiv<?= $prevCategoryId ?>" class="btn red-link" id="category<?= $prevCategoryId ?>" role="tab" data-toggle="tab">Back</a>
                                <?php }else if(!$prevCategoryId) { ?>    
                                    <button class="enrollmentLeftmenuItem" id="recommendationsTabBack"><a href="javascript:void(0);" class="btn red-link" data-step="3">Back</a></button>
                                <?php }
                                $prevCategoryId = $key;
                                ?>
                                </div>
                            </div>
                        <?php } ?>
                     </div>
                </div>
                <div class="col-md-3">
                    <?php
                        $page_location = "self_guiding_benefits_page_";
                    ?>
                    <?php include('bundle_sidebar.inc.php'); ?>
                </div>
            </div>
            <p class="error" id="error_product_cart"></p>
        </div>
    </div>
</div>
<?php } ?>