<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/Api.class.php';

$ajaxApiCall = new Api();
$groupIds = !empty($_POST['groupId']) ? $_POST['groupId'] : '';

$params = array(
    'id' =>$groupIds,
    'paginate' => true,
    'api_key' =>'editBundleRecommendationList',
    'page' => !empty($_POST['page']) ? $_POST['page'] : 1
);
$bundleRes = $ajaxApiCall->ajaxApiCall($params,true);
$fetch_rows = !empty($bundleRes['data']) ? $bundleRes['data']: array();
$total_rows = count($fetch_rows);
$data = !empty($fetch_rows['editRecordDetails']) ? $fetch_rows['editRecordDetails'] : [];
$paginateLink = $ajaxApiCall->paginate($data,'create_bundle.php');

$currentPageTotalBundleForGroup = 1;
if($data['current_page'] != 1){
    $totalRecords = !empty($data['per_page']) && !empty($data['current_page']) ? ($data['per_page'] * $data['current_page']) : 0;
    $currentPageTotalBundleForGroup = !empty($data['per_page']) && !empty($data['current_page']) ? ((($data['current_page']-1) * $data['per_page']) + 1) : 1;
}
$totalBundleForGroup = !empty($data['total']) ? $data['total'] : 0;

$data = $data['data'];
$BundleCount = count($data);

$htmlData = '';
ob_start();
if(!empty($data)){
    $i = $currentPageTotalBundleForGroup;
    foreach ($data as $key => $rows) {
            $array = explode(',', $rows['product_ids']);
            $product_count = count($array); 
        ?>
        <tr id="textBundleTr<?=$rows['id'];?>" class="textBundleTr" data-id="<?= $rows['id']; ?>">
            <td><label id="bundle_label_text<?= $rows['id']; ?>"><?= $rows['bundle_label'];?></label>
                 <p class="error" id="error_checkBundle_<?= $rows['id']; ?>"></p>
            </td>
            <td>
             <a href="javascript:void(0);" class="fw500 text-center"><label class="bundle_product_text" id="bundle_product_text<?= $rows['id']; ?>"><?= $product_count;?></label></a>
             <p class="error" id="error_bundleProduct_<?= $rows['id']; ?>"></p>
            </td>
            <td><label id="bundle_effective_date_text<?= $rows['id']; ?>"><?=displayDate($rows['effective_date'])?></label>
            </td>
            <td><label id="bundle_termination_date_text<?= $rows['id']; ?>"><?=!empty($rows['termination_date']) ?displayDate($rows['termination_date']) :''?></label></td>
            <td><label id="bundle_recommendation_reason_text<?= $rows['id']; ?>"><?=$rows['recommendation_reason']?></label></td>
            <td class="icons editBundleLabelTr" colspan="1">
                <a href="javascript:void(0);" id="<?= $rows['id']; ?>" data-id="<?= $rows['id']; ?>" class = "edit_Bundle"><i data-toggle="tooltip" title="Edit" class="fa fa-edit fa-lg tooltip<?= $rows['id']; ?>"></i>
                </a>
                
                <a href="javascript:void(0);" class="remove_Bundle" id="<?= $rows['id']; ?>" data-id="<?= $rows['id']; ?>"><i data-toggle="tooltip" title="Delete" class="fa fa-trash fa-lg tooltip<?= $rows['id']; ?>" ></i></a>
            </td>
        </tr>
        <tr id="inputBundleTr<?= $rows['id']; ?>"  style="display: none;" class="inputBundleTr">
            <td>
                <div class="phone-control-wrap">
                    <div class="phone-addon fw500 fs20 w-30 dynamicId" id="dynamicId_<?= $rows['id']; ?>"><?= $i; ?></div>
                    <div class="phone-addon">
                        <div class="form-group">
                            <input type="text" name="bundle_label[<?= $rows['id']; ?>]" id="bundle_label_<?= $rows['id']; ?>" class="form-control bundle_label" value="<?= $rows['bundle_label'];?>">
                            <label>Bundle Label</label>
                            <p class="error error_bundle_<?= $rows['id']; ?> text-left" id="error_bundle_label_<?= $rows['id']; ?>"></p>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select name="bundle_product[<?= $rows['id']; ?>][]" multiple="multiple" data-id="<?= $rows['id']; ?>"  id="bundle_product_<?= $rows['id']; ?>" class="bundle_product" data-container="body">
                    <?php if(!empty($fetch_rows['productData'])){
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
                                            <option value ="<?=$value['id']?>"<?= $selectedProductIds ?>><?= $value['name'].' '.'('.$value['product_code'].')'?></option>
                                       <?php } ?>  
                                </optgroup>
                            <?php } ?>
                    <?php }  ?>
                    </select>
                    <label>Products</label>
                    <p class="error error_bundle_<?= $rows['id']; ?>" id="error_bundle_product_<?= $rows['id']; ?>"></p>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="bundle_effective_date[<?= $rows['id']; ?>]" id="bundle_effective_date_<?= $rows['id']; ?>" class="form-control date_picker" data-change_text="bundle_effective_date_text<?= $rows['id']; ?>" value= "<?= displayDate($rows['effective_date']);?>" />
                    <label>Effective Date</label>
                    <p class="error error_bundle_<?= $rows['id']; ?>" id="error_bundle_effective_date_<?= $rows['id']; ?>"></p>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" name="bundle_termination_date[<?= $rows['id']; ?>]" id="bundle_termination_date_<?= $rows['id']; ?>" class="form-control date_picker" data-change_text="bundle_termination_date_text<?= $rows['id']; ?>" value ="<?=!empty($rows['termination_date']) ? displayDate($rows['termination_date']) :''?>" />
                    <label>Termination Date</label>
                    <p class="error error_bundle_<?= $rows['id']; ?>" id="error_bundle_termination_date_<?= $rows['id']; ?>"></p>
                </div>
            </td>
            <td>
                <div class="form-group">
                  <input type="text" name="recommendation_reason[<?= $rows['id']; ?>]" id="recommendation_reason_<?= $rows['id']; ?>" class="form-control recommendation_reason" value = "<?= $rows['recommendation_reason'];?>">
                  <label>Recommendation Reason</label>
                    <p class="error error_bundle_<?= $rows['id']; ?>" id="error_recommendation_reason_<?= $rows['id']; ?>"></p>
                </div>
            </td>
            <td class="icons w-30 bundlePortion editBundleLabelTr" data-id="<?= $rows['id']; ?>">
            </td>
        </tr>
    <?php $i++; } ?>

    <input type="hidden" name="pages" id="per_pages" value="<?=$paginateLink['per_page'];?>" />
<?php }
$htmlData = ob_get_clean();
$res = ['htmlData'=>$htmlData,'paginateLink'=>$paginateLink['links'],'totalBundleForGroup'=>$totalBundleForGroup];
header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>