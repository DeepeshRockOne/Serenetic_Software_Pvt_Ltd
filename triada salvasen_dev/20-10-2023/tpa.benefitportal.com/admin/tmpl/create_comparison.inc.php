<div class="row comparison_toggle" style="<?=!empty($GetBundleQuestionDetails) ? ' ' :'display:none'; ?>">
      <div class="col-sm-12 text-center p-20">
         <a href="javascript:void(0);" id = "skip_compare_btn" class="btn red-link skip_compare_btn" data-toggle="tooltip" data-placement="bottom" title="Skip" style="<?=($SkipComparison == "Y" || !empty($getAllCompareData))  ? 'display:none' :''; ?>">Skip Comparison</a>
         <a href="javascript:void(0);" id= "add_compare_btn" class="btn red-link add_compare_btn" data-toggle="tooltip" data-placement="bottom" title="Add" style="<?= !empty($SkipComparison) && (empty($getAllCompareData) || ($SkipComparison == "N" && empty($getAllCompareData))) ? '' :'display:none'; ?>">Add Comparison of Bundle</a>
      </div>
</div>
<div class="comparison_wrap" style="<?=!empty($GetBundleQuestionDetails) && (!empty($getAllCompareData) || empty($SkipComparison))? '' :'display:none'; ?>">
   <div class="compare_bundle">
      <div class="row">
         <div class="col-sm-3">
            <div class="bundle_left">
               <div class="bundle_left_head text-center">
                  <h4 class="mn fs16 text-white">Step 3 - Compare Bundle To Bundle</h4>
               </div>
               <div class="p-20 bg-white text-center">Add a row below.</div>

               <div class="bundle_left_body">
                  <ul class="list-unstyled" id="compare_rows_label_div">
                     <?php if(!empty($getAllCompareData)){
                        foreach ($getAllCompareData as $key => $value) {?>
                          <li class="bundle_label_set compare_label_inputs" id="compare_label_inputs_<?=$value['bundle_compare_id']?>"   data-id="<?=$value['bundle_compare_id']?>" style="display:none">
                               <div class="form-group">
                                  <input type="text" class="form-control" name="compare_row_label[<?=$value['bundle_compare_id']?>]" id="compare_row_label_<?=$value['bundle_compare_id']?>" value= "<?=$value['comparison_lable']?>">
                                  <label>Row Label</label>
                                  <p class="error" id = "error_compare_row_label_<?=$value['bundle_compare_id']?>"></p>
                               </div>
                          </li>
                          <li class="compare_label_text" id="compare_label_text_<?=$value['bundle_compare_id']?>" >
                            <p id="compare_label_<?=$value['bundle_compare_id']?>" class="d-inline mn"><?=$value['comparison_lable']?></p>
                            <span>
                               <div class="dropdown">
                                  <a href="javascript:void(0);" class="text-action" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-h" aria-hidden="true" data-toggle="tooltip" title="Action"></i></a>
                                  <ul class="dropdown-menu dropdown-menu-right">
                                     <li class="edit_compare_row" id="edit_compare_row_<?=$value['bundle_compare_id']?>" data-id="<?=$value['bundle_compare_id']?>"><a href="javascript:void(0);">Edit</a></li>
                                     <li class="del_compare_row" id="del_compare_row_<?=$value['bundle_compare_id']?>" data-id="<?=$value['bundle_compare_id']?>"><a href="javascript:void(0);">Remove</a></li>
                                  </ul>
                               </div>
                            </span>
                          </li>
                        <?php }?>
                     <?php }?>
                  </ul>
               </div>
               <div class="bundle_left_footer text-center">
                  <a href="javascript:void(0);"  id = "add_row" class="btn btn-action add_row" data-toggle="tooltip" title="+ Row" role="button" >+ Row</a>
                  <a href="javascript:void(0);" id = "save_row" class="btn btn-action save_row row_btn" data-toggle="tooltip" title="Save" role="button" style="display:none">Save</a>
                  <a href="javascript:void(0);" id="cancel_row" class="btn btn-action cancel_row row_btn" data-toggle="tooltip" title="Cancel" role="button" style="display:none">Cancel</a>
               </div>
            </div>
          <p class="error" id="error_compare_row_label"></p>
         </div>
         <div class="col-sm-9">
            <div class="bundle_right">
               <div class="table-responsive">
                  <table class="table">
                        
                           <thead>  
                              <tr id="comparision_recommended_div">
                                <?php if(!empty($BundleDetailsArr)){
                                 foreach ($BundleDetailsArr as $key => $value) {
                                        $isBundleChecked = $selectedBundle == $value['id'] ? 'checked' :'';
                                        ?>
                                       <th data-id="<?=$value['id']?>">
                                          <label class="mn"><input type="radio" class = "top_recommended_bundle" name="top_recommended" value="<?=$value['id']?>" <?=$isBundleChecked?> id="top_recommended_<?=$bundleInformId?>"> Top Recommended</label>
                                       </th>   
                                  <?php  } } ?>
                              </tr>
                              <tr class="bundle_right_subhead" id="comparision_bundles_div">
                                   <?php if(!empty($BundleDetailsArr)){
                                    foreach ($BundleDetailsArr as $key => $value) { 
                                        $addClass = $value['id'] == $selectedBundle ? 'bundle-selected':'';
                                      ?>
                                     <th data-id="<?=$value['id']?>"  id ="comparision_bundle_<?=$value['id']?>" class="comparision_bundle <?=$addClass;?>">
                                     <?= $value['bundle_label'];?>
                                      </th>
                                   <?php } } ?>
                              </tr>
                           </thead>
                         <tbody id="comparision_table_body">
                            <?php if(!empty($getAllCompareData)){
                                  $jsonArray = []; 
                                foreach ($getAllCompareData as $v) {?>
                                  <tr class="bundle_label_set compare_bundle_inputs" id="compare_bundle_inputs_<?=$v['bundle_compare_id']?>" style="display: none;">
                                    <?php 
                                     $BundleData = json_decode($v['bundle_comparison_lable'],true);
                                     foreach ($bundleIdsArr as  $V1) {
                                        $arrayDiff = array_key_exists($V1, $BundleData);
                                        if(empty($arrayDiff)){
                                            $jsonArray[$V1] = "";
                                        }else{
                                             $jsonArray =  $BundleData;
                                        }
                                     }
                                     foreach ($jsonArray as $key => $bunleval) {?>
                                        <td class="compare_bundle_input" data-column-id="<?=$key?>" >
                                           <div class="form-group">
                                              <input type="text" value="<?=$bunleval?>" id="bundle_recom_input_<?=$v['bundle_compare_id']?>_<?=$key?>" class="form-control bundle_recom_input" name="compare_bundle[<?=$key?>][<?=$v['bundle_compare_id']?>]">
                                              <label>Input</label>
                                           </div>
                                        </td>
                                     <?php } ?>
                                  </tr>
                                  <tr class="compare_bundle_texts" id="compare_bundle_texts_<?=$v['bundle_compare_id']?>">
                                     <?php  foreach ($jsonArray as $key => $bunleval) {?>
                                     <td class="compare_bundle_text" data-column-id="<?=$key?>"><?=$bunleval?>
                                     </td>
                                     <?php }?>
                                  </tr>
                               <?php } ?>
                            <?php } ?>
                         </tbody>
                  </table>
               </div>
            </div>
              <ul class="compare_bundle_error">
                <li class="error" id = "error_top_recommended" style="display: none;"></li>
                <li class="error" id="error_bundle_compare_row" style="display: none;"></li>
              </ul>            
         </div>
      </div>
   </div>
   
</div>
<!-- <hr / class="m-t-30 m-b-30">
   <div class="clearfix text-center">
      <a href="javascript:void(0);" class="btn btn-action save_recommendation" id="save_recommendation">Save Recommendation</a>
      <a href="javascript:void(0);" class="btn red-link  cancel_recommendation" id="cancel_recommendation">Cancel</a>
   </div> -->
<input type="hidden" name="comparison_row_counter" id="comparison_row_counter" value="<?=(isset($BundleCount)) ? $BundleCount : 0?>">
