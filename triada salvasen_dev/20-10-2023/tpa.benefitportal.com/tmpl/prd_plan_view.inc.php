<div class="panel">
    <div class="panel-heading">
        <h4 class="panel-title"><?= $categoryName ?> Details</h4>
    </div>
    <div class="plan-block">
        <div class="plan-left-block vtabs customvtab">
            <ul class="nav tabs-vertical">
                <?php if(!empty($productDetails)){ $i = 1; ?>
                    <?php foreach ($productDetails as $key => $value) { ?>
                        <li class="tab <?php echo $i == 1 ? 'active' : '' ?>">
                            <a data-toggle="tab" href="#rp_cat_<?=$value['product_id']?>" aria-expanded="true" class="btn_load_category_report" data-category_name="<?=$value['category_name']?>"> <?=$value['product_name']?> </a>
                        </li>
                    <?php $i++; } ?>
                <?php } ?>
            </ul>
        </div>
        <div class="plan-right-block">
            <div id="plan-info-scroll" >
                <div class="tab-content mn">
                    <?php if(!empty($productDetails)) { $i = 1; ?>
                        <?php foreach ($productDetails as $key1 => $value1) { ?>
                            <div id="rp_cat_<?=$value1['product_id']?>" class="tab-pane plan-info-scroll <?php echo $i == 1 ? 'active' : '' ?>">
                                <div class="tab-body" style="max-height:500px;">
                                    <div class="panel-body b-b">
                                        <div class="clearfix" style="display:<?=empty($value1['effectiveDate']) ? 'none' : '' ?>">
                                            <h5 class="fw700">Effective Date</h5>
                                            <p class="mn"><?=$value1['effectiveDate']?></p>
                                        </div>
                                        <div class="m-t-30" style="display:<?=empty($value1['availableState']) ? 'none' : '' ?>">
                                            <h5 class="m-t-0 fw700">Available States</h5>
                                            <p class="mn"><?=$value1['availableState']?></p>
                                        </div>
                                        <div class="m-t-30" style="display:<?=empty($value1['requiredProduct']) ? 'none' : '' ?>">
                                            <h5 class="m-t-0 fw700">Products Required</h5>
                                            <p class="mn"><?=$value1['requiredProduct']?></p>
                                        </div>
                                        <div class="m-t-30" style="display:<?=empty($value1['excludedProduct']) ? 'none' : '' ?>">
                                            <h5 class="m-t-0 fw700">Products Excluded</h5>
                                            <p class="mn"><?=$value1['excludedProduct']?></p>
                                        </div>
                                    </div>
                                <div class="panel-body">
                                    <div class="clearfix">
                                        <h5 class="fw700"><?=$value1['product_description']?></h5>
                                    </div>
                                </div>
                                </div>
                            </div>
                        <?php $i++; } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function (){
    $(".tab-body").mCustomScrollbar({theme:"minimal-dark"});
});
 
</script>