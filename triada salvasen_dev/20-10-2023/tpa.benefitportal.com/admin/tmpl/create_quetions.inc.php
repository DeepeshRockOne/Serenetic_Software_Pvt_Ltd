<div class="bundle_wrap p-b-20 p-t-20" style="<?=!empty($groupIds) ? '' : 'display: none'?>">
   <div class="create_bundle">
      <div class="bundle_head bg_dark_danger p-15">
         <div class="row">
            <div class="col-sm-6">
                <h4 class="m-t-7 m-b-0 text-white">Step 2 - Create Questions(s)</h4>
            </div>
            <div class="col-sm-6" >
                <div class="text-right">
                    <a href="javascript:void(0);" id="edit_bundle_step_2" onclick="edit_bundle_step(2)" style="<?=!empty($GetBundleQuestionDetails) ? '': 'display: none';?>" class="btn btn-default">Edit</a>
                </div>
            </div>
         </div>
      </div>
       <div class="bundle_body" id="bundle_question_body">
          <div class="table-responsive">
             <table class="<?=$table_class?> b-b" id="questionTable">
                <thead>
                   <tr class="QuestionHeaderTr" style="<?=!empty($GetBundleQuestionDetails) ? '' : 'display: none;'?>">
                      <th>Question</th>
                      <th>Answer Type</th>
                      <th class="text-center">Assigned Bundle(s)</th>
                      <th width="100px" class = "editQuestionIcons" style="<?=empty($GetBundleQuestionDetails) ? '' :'display: none';?>"></th>
                   </tr>
                </thead>
                <tbody id="questionTableBody">
                  <?php $questionLabelID = 1;
                   if(!empty($GetBundleQuestionDetails)){ 
                      foreach ($GetBundleQuestionDetails as $key => $data) {?>
                          <tr id="textQuestionTr<?=$data['question_id'];?>" class="textQuestionTr" data-LabelID=<?= $questionLabelID ?>>
                              <td><label id="question_label_text<?=$data['question_id'];?>"><label><?=$data['questions'];?>
                              </td>
                              <td>
                                  <?php
                                  $control_type = '';
                                  if($data['control_type'] == "radio"){
                                    $control_type = "Yes/No";
                                  }elseif ($data['control_type'] == "select") {
                                     $control_type = "Multiple Choice (1 option)";
                                  }elseif ($data['control_type'] == "select_multiple") {
                                     $control_type = "Multiple Choice (1+ option)";
                                  }elseif ($data['control_type'] == "checkbox") { 
                                     $control_type = "Check Answer";
                                  }
                                  ?>
                                  <label id="question_answer_type_text<?=$data['question_id'];?>"><?=$control_type;?><label>
                              </td>
                             <td class="text-center">
                                 <a href="view_bundle_question_answer_details.php?id=<?=md5($data['question_id']);?>" id='view_bundle_table_<?=$data['question_id'];?>' data-toggle="tooltip" title="View" data-container="body" class="fw500 text-action tooltip<?=$data['question_id'];?> view_bundle_table"><span id="question_assigned_bundle_text<?=$data['question_id'];?>"><?=$data['TotalAssignedBundle'];?></span></a>     
                             </td>
                             <td class="icons editQuestionIcons" style="display: none">
                                 <a href="javascript:void(0)" data-toggle="tooltip" title="Clone" data-container="body" class="CloneQuestion"  id="question_clone_<?=$data['question_id'];?>" data-id="<?=$data['question_id'];?>"><i class="fa fa-clone fa-lg"></i></a>
                                 <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" data-container="body" class="EditQuestion" id="question_edit_<?=$data['question_id'];?>" data-id="<?=$data['question_id'];?>"><i class="fa fa-pencil fa-lg"></i></a>
                                <a href="javascript:void(0)"  class="QuestionRemove" data-container="body"  data-id="<?=$data['question_id'];?>" id="question_remove_<?=$data['question_id'];?>"><i data-toggle="tooltip" title="Delete" class="fa fa-trash fa-lg tooltip<?=$data['question_id'];?>"></i>
                                </a>
                             </td>
                          </tr>
                      <?php $questionLabelID++; }  ?>
                   <?php } ?>
                </tbody>
             </table>
          </div> 
          <div id="editBundleQuestions"></div>  
       </div>
       <div class="bundle_footer p-15 b-t" id="step_2_footer" style="<?=empty($GetBundleQuestionDetails)?'':'display: none';?>">
          <div class="row">
            <div class="col-sm-6">
              <button class="btn btn-info" data-toggle="tooltip" title="+ Question" type="button" id="addQuestionButton" onclick="addQuestion()" >+ Question</button>
                <a href="javascript:void(0)" class="btn btn-info saveQuestionButton" data-toggle="tooltip" title="save" style="display:none" onclick="saveQuestions(2)" data-container="body">save</a>
                <a href="javascript:void(0)" class="btn blue-link saveQuestionButton" id="cancelCreateQuestionButton"  data-id="" onclick="cancelQuestion()" data-toggle="tooltip" title="cancel" style="display:none" data-container="body">cancel</a>
            </div>
            <div class="col-sm-6">
              <div class="text-right">
                 <a href="javascript:void(0)" class="btn btn-action disabled" role="button" onclick="goto_step(3)" 
                 id="go_to_third_step" data-toggle="tooltip" title="Next Step" data-container="body" disabled="disabled">Next Step</a>
              </div>
            </div>
          </div>
      </div>
   </div>
</div>
<input type="hidden" name="question_table_counter" id="question_table_counter" value="0">
<input type="hidden" name="bundle_priority_counter" id="bundle_priority_counter" value="0">
<input type="hidden" name="question_id" id="question_id" value="0">

<div class="clearfix"></div>
<!-- <script type="text/javascript">
  $(".bundle_wrap").show();

</script> -->