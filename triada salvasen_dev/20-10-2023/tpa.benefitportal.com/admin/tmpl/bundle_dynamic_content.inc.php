<!-- Bundle Template -->

<table style="display: none">
   <tbody id="bundle_template" style="display: none">

      <tr id="textBundleTr~i~" class="textBundleTr" data-id="~i~" style="display:none">
         <td><label id="bundle_label_text~i~"></label>
            <p class="error" id="error_checkBundle_~i~"></p>
         </td>
         <td>
            <a href="javascript:void(0);" class="fw500 text-center"><label class="bundle_product_text" id="bundle_product_text~i~"></label></a>
         </td>
         <td><label id="bundle_effective_date_text~i~"></label></td>
         <td><label id="bundle_termination_date_text~i~"></label></td>
         <td><label id="bundle_recommendation_reason_text~i~"></label></td>
         <td class="icons editBundleLabelTr" colspan="1">
            <a href="javascript:void(0);" data-id="~i~" id="edit_Bundle_~i~" class="edit_Bundle"><i data-toggle="tooltip" title="Edit" class="fa fa-edit fa-lg tooltip~i~"></i>
            </a>
            <a href="javascript:void(0);" data-id="~i~" id="remove_Bundle_~i~" class="remove_Bundle"><i data-toggle="tooltip" title="Delete" class="fa fa-trash fa-lg tooltip~i~"></i></a>
         </td>
      </tr>
      <tr id="inputBundleTr~i~" class="inputBundleTr">
         <td>
            <div class="phone-control-wrap">
               <div class="phone-addon fw500 fs20 w-30 dynamicId" id="dynamicId_~i~">~i~</div>
               <div class="phone-addon">
                  <div class="form-group">
                     <input type="text" name="bundle_label[~i~]" id="bundle_label_~i~" class="form-control bundle_label" value="">
                     <label>Bundle Label</label>
                     <p class="error text-left error_bundle_~i~" id="error_bundle_label_~i~"></p>
                  </div>                
               </div>
            </div>
         </td>
         <td>
            <div class="form-group">      
               <select name="bundle_product[~i~][]" multiple="multiple" data-id="~i~" id="bundle_product_~i~" class="bundle_product" data-container="body">
               </select>
               <label>Products</label>
               <p class="error error_bundle_~i~" id="error_bundle_product_~i~"></p>
            </div>
         </td>
         <td>
            <div class="form-group">
               <input type="text" name="bundle_effective_date[~i~]" id="bundle_effective_date_~i~" class="form-control date_picker" data-change_text="bundle_effective_date_text~i~" />
               <label>Effective Date</label>
               <p class="error error_bundle_~i~" id="error_bundle_effective_date_~i~"></p>
            </div>
         </td>
         <td>
            <div class="form-group">
               <input type="text" name="bundle_termination_date[~i~]" id="bundle_termination_date_~i~" class="form-control date_picker" data-change_text="bundle_termination_date_text~i~" />
               <label>Termination Date</label>
               <p class="error error_bundle_~i~" id="error_bundle_termination_date_~i~"></p>
            </div>
         </td>
         <td>
            <div class="form-group">
               <input type="text" name="recommendation_reason[~i~]" id="recommendation_reason_~i~" class="form-control recommendation_reason">
               <label>Recommendation Reason</label>
               <p class="error error_bundle_~i~" id="error_recommendation_reason_~i~"></p>
            </div>
         </td>
         <td class="icons w-30 bundlePortion editBundleLabelTr" data-id="~i~" id="editBundleLabelTr~i~">
            <!-- <a href="javascript:void(0);"><i data-toggle="tooltip" idclass="fa fa-trash fa-lg tooltip~i~" title="Delete"></i></a> -->
         </td>
      </tr>
   </tbody>
</table>
<!-- Bundle Template -->

<!-- Bundle Create Question -->
<div >
   <table>
      <tbody id="quetion_template_tr" style="display: none">
         <tr id="textQuestionTr~i~" class="textQuestionTr" style="display:none" data-LabelID="">
            <td><label id="question_label_text~i~"><label></td>
            <td><label id="question_answer_type_text~i~"><label></td>
            <td class="text-center">
               <a href="javascript:void(0)" id = "view_bundle_table_~i~" data-toggle="tooltip" title="View" data-container="body" class="fw500 text-action view_bundle_table tooltip~i~"><span id="question_assigned_bundle_text~i~"></span></a>
            </td>
            <td class="icons editQuestionIcons" style="width: 100px;">
               <a href="javascript:void(0)" data-toggle="tooltip" title="Clone" data-container="body" class="CloneQuestion tooltip~i~" id="question_clone_~i~" data-id="~i~"><i class="fa fa-clone fa-lg"></i></a>
               <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" data-container="body" class="EditQuestion tooltip~i~" id="question_edit_~i~" data-id="~i~" ><i class="fa fa-pencil fa-lg"></i></a>
               <a href="javascript:void(0)"   class="QuestionRemove" data-container="body"  data-id="~i~" id="question_remove_~i~"><i data-toggle="tooltip" title="Delete" class="fa fa-trash fa-lg tooltip~i~"></i></a>
            </td>
         </tr>
      </tbody>
   </table> 
   <div id="quetion_template" style="display: none">
      <div id="inputQuetionsTr~i~" class="p-15 inputQuetionsTr questionPortion" data-id="~i~">
         <div class="row">
            <div class="col-sm-1">
            </div>
            <div class="col-sm-11">
               <div class="row">
                  <div class="col-sm-3">
                     <div class="phone-control-wrap">
                        <div class="phone-addon fw700 fs18 w-30 dynamicQuestionId">
                            <div class="m-b-20" id="dynamicQuestionId_~i~">~i~</div>
                        </div>
                        <div class="phone-addon text-left">
                           <div class="form-group">
                              <select class="questionType" name="control_type[~i~]" id="control_type_~i~" data-id="~i~">
                                 <option></option>
                                 <option value="radio" <?= !empty($control_type) && $control_type == "radio" ? 'selected' : '' ?>>Yes/No</option>
                                 <option value="select_multiple" <?= !empty($control_type) && $control_type == "select_multiple" ? 'selected' : '' ?>>Multiple Choice (1+ option)</option>
                                 <option value="select" <?= !empty($control_type) && $control_type == "select" ? 'selected' : '' ?>>Multiple Choice (1 option)</option>
                                 <option value="checkbox" <?= !empty($control_type) && $control_type == "textarea" ? 'selected' : '' ?>>Check Answer</option>
                              </select>
                              <label>Question Type</label>
                              <p class="error" id="error_control_type_~i~"></p>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-8">
                     <div class="form-group">
                        <input type="text" class="form-control" name="ask_question[~i~]" id="ask_question_~i~">
                        <label>Ask Question</label>
                        <p class="error" id="error_ask_question_~i~"></p>
                     </div>
                  </div>
                  <div class="col-sm-1">
                     <div class="m-t-7">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div id="answer_parent_div"  class = "m-l-30" style="<?= !empty($control_type) && in_array($control_type, array('radio', 'select_multiple', 'select')) ? '' : 'display: none' ?>">
            <input type="hidden" class="form-control" name="answer_counter" id="answers_counter_~i~" value="0">
            <div id="main_answer_div">
            </div>
         </div>
         <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-11">
               <div class="clearfix">
                  <a href="javascript:void(0);" style="display:none" class="btn btn-info add_answer_btn tooltip~i~" id= "add_answer_btn_~i~" data-toggle="tooltip" title="+ Answer" data-container="body" data-id="~i~">+ Answer</a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="answer_dynamic_div" style="display: none;">
   <div class="answer_div" id="addAnwserTab_~i~" data-id="~i~">
      <div class="col-sm-1"></div>
      <div class="col-sm-11">
         <div class="row">
            <div class="col-sm-3">
               <h5 class="answer_heading" style="~header_display~">Answers</h5>
               <div class="phone-control-wrap">
                  <div class="phone-addon fs24 v-align-top w-30 delete_answer_btn"  id="delete_answer_btn_~i~" style="~icon_display~" data-id="~i~" role="button">X</div>
                  <div class="phone-addon text-left">
                     <div class="form-group">
                        <input type="text" class="form-control" id="question_answer_~i~" name="question_answer[~j~][~i~]">
                        <label>Answer</label>
                        <p class="error" id="error_question_answer_~i~"></p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm-3">
               <h5 class="answer_heading" style="~header_display~">Assign to Bundle(s)</h5>
               <div class="form-group">
                  <select id="display_bundle_~i~" class="display_bundle" multiple="multiple" data-id="~i~" name="display_bundle[~i~][]" data-container="body">
                  </select>
                  <label>Select</label>
                  <p class="error" id="error_display_bundle_~i~"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <h5 class="answer_heading" style="~header_display~">Priority Order</h5>
               <a href="javascript:void(0);"  id ="set_priority_~i~" class="btn red-link set_priority" data-id="~i~" data-enabled="0">Set Priority Order</a>
               <h5 id="moveable_div" style="display: none;">
                  <a href="javascript:void(0);" class="fa fa-info-circle fa-lg tooltip~i~" aria-hidden="true" data-toggle="tooltip" data-original-title="Drag records up and down using the action icon to determine the priority order for this answer. The bundle in slot #1 will have more priority." data-container="body"></a> Priority Order of Bundle(s)
               </h5>
               <div class="clearfix"></div>
               <a href="javascript:void(0);" style="display:none" id = "editPriority_~i~" class="editPriority" data-id="~i~"><i class="fa fa-pencil fs24"></i></a>
               <div class="moveable bundles_priority_div" data-id="~i~" style="display:none">
                  <div id="bundlePriority_~i~">
                  </div>
                  <div class="clearfix text-right m-t-15 save_answer_div">
                     <!--  <a href="javascript:void(0);" class="btn btn-info" data-toggle="tooltip" title="Save" data-container="body">Save</a> -->
                     <a href="javascript:void(0)" id ="savePriority_~i~"  class="btn btn-info savePriority tooltip~i~" data-toggle="tooltip" title="save" data-container="body" data-id="~i~">save</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="setPriorityDiv" style="display: none;">
   <div class="~category_block~ bundlePriority display_bundle_div" id="display_bundle_div_~i~" data-id="~i~">
      <input type="hidden" name="bundleorder[~AnsweID~][~i~]"  class="bundle_order" id="bundleorder_~AnsweID~" value="~OrderValue~">
      <div class="div_table">
         <div class="table_row br-t br-b">
            <div class="table_cell">
               <div class="bg_dark_primary p-10 text-center text-white bundleCountDiv category_order" id="bundleCountDiv_~i~" name="~bundleCount~" data-display-number="~bundleCount~">~bundleCount~</div>
            </div>
            <div class="table_cell">
               <div class="p-10 w-30 text-center">
                  <div class="moveable_controller text-nowrap tooltip~i~" data-toggle="tooltip" data-placement="top" title="Move">
                     <i class="fa fa-ellipsis-v fa-lg"></i>
                     <i class="fa fa-ellipsis-v fa-lg"></i>
                  </div>
               </div>
            </div>
            <div class="table_cell" name="~bundleText~" id="bundleTextDiv_~i~">
               ~bundleText~
            </div>
         </div>
      </div>
   </div>
</div>

<div class="bundlLabelDynamicDiv" style="display:none">
   <li class="bundle_label">
      <div class="form-group">
         <input type="text" class="form-control" name="">
         <label>Row Label</label>
      </div>
   </li>
   <li class="">
      <p class="lable_text"></p>
      <span>
         <div class="dropdown">
            <a href="javascript:void(0);" class="text-action" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-h" aria-hidden="true" data-toggle="tooltip" title="Action"></i></a>
            <ul class="dropdown-menu dropdown-menu-right">
               <li><a href="#">Edit</a></li>
               <li><a href="#">Remove</a></li>
            </ul>
         </div>
      </span>
   </li>
</div>

<table class="" style="display:none;">
   <thead>
      <tr class="compareRecommendDynamicTh">
         <th data-id="~i~">
            <label class="mn"><input type="" class="top_recommended_bundle" name="top_recommended" value="~i~" id="top_recommended_~i~"> Top Recommended</label>
            <!-- <p class="error error_top_recommended"></p> -->

         </th>
      </tr>
      <tr class="compareBundleHeadingDynamicTh">
         <th data-id="~i~" id ="comparision_bundle_~i~" class="comparision_bundle">
            ~text~
         </th>
      </tr>
   </thead>
   <tbody>
      <tr class="compareBundleInputDynamicTd">
         <td class="compare_bundle_input" data-column-id="~j~">
            <div class="form-group">
               <input type="text" id="bundle_recom_input_~i~_~j~" class="form-control bundle_recom_input" name="compare_bundle[~j~][~i~]">
               <label>Input</label>
               
            </div>
         </td>
      <tr>
      <tr class="compareBundleTextDynamicTd">
         <td class="compare_bundle_text" data-column-id="~j~">
         </td>
      </tr>
   </tbody>
</table>

<table class="" style="display:none;">
   <tbody class="compareBundleDynamicTrSet">
   <tr class="bundle_label_set compare_bundle_inputs" id="compare_bundle_inputs_~i~">
   </tr> 
   <tr class="compare_bundle_texts" id="compare_bundle_texts_~i~" style="display:none">
   </tr>
   </tbody>
</table>

<div class="compareLableSetDynamicDiv" style="display:none;">
   <li class="bundle_label_set compare_label_inputs" id="compare_label_inputs_~i~" data-id="~i~">
      <div class="form-group">
         <input type="text" class="form-control" name="compare_row_label[~i~]" id="compare_row_label_~i~">
         <label>Row Label</label>
         <!-- <p class="error" id = "error_compare_row_label_~i~"></p> -->
      </div>
   </li>
   <li class="compare_label_text" id="compare_label_text_~i~" style="display:none">
      <p id="compare_label_~i~" class="d-inline mn"></p>
      <span>
         <div class="dropdown">
            <a href="javascript:void(0);" class="text-action" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-h" aria-hidden="true" data-toggle="tooltip" title="Action"></i></a>
            <ul class="dropdown-menu dropdown-menu-right">
               <li class="edit_compare_row" id="edit_compare_row_~i~" data-id="~i~"><a href="javascript:void(0);">Edit</a></li>
               <li class="del_compare_row" id="del_compare_row_~i~" data-id="~i~"><a href="javascript:void(0);">Remove</a></li>
            </ul>
         </div>
      </span>
   </li>
</div>


