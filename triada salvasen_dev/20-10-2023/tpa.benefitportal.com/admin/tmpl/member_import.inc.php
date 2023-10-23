<?php if(!empty($MEMBER_IMPORT_ARR)){
   $columnCounter = 0;
   foreach($MEMBER_IMPORT_ARR as $type => $fieldArr) {
?>
   <div class="line_title">
      <h3><span><?=$type?></span></h3>
   </div>
   <div class="row">
      <?php foreach($fieldArr as $field) { ?>
         <div class="col-sm-12 col-md-6">
            <div class="form-group height_auto">
               <div class="input-group resources_addon">
                  <div class="input-group-addon">
                     <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$field['info']?>"></i> <?=$field['label']?>
                  </div>
                  <div class="pr">
                     <select class="form-control select" name="<?=$field['field_name']?>" data-live-search="true">
                           <option data-hidden="true"></option>
                          <?php foreach ($row as $key => $value) {
                              $selectedOption = "";
                              $optn_val=$value;
                              if ($field['label'] == trim($value) || ($field['file_label'] == trim($value))) {
                                 $selectedOption = 'selected="selected"';
                              } else if ($columnCounter == $key) {
                                 $optn_val='None';
                                 $value='';
                                 $selectedOption = '';
                              } 
                           ?>
                           <option value="<?=$optn_val?>" <?=$selectedOption?>><?=$value?></option>
                        <?php } ?>
                     </select>
                     <label class="label-wrap">Select CSV Column</label>
                  </div>
               </div>
               <p class="error" id="err_<?=$field['field_name']?>"></p>
            </div>
         </div>
      <?php } ?>
      <?php if($type == 'PRODUCT'){
         $custom_questions = $pdo->select("SELECT id,display_label from prd_enrollment_questions where questionType = 'Custom' and is_deleted = 'N' order by order_by"); 
         $selQuestion = [];
         if(!empty($custom_questions)){
            foreach ($custom_questions as $que) { ?>
               <div class="col-sm-12 col-md-6">
                  <div class="form-group height_auto">
                     <div class="input-group resources_addon">
                        <div class="input-group-addon">
                           <?=$que['display_label']?><!--  <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="Percentage of benefit amount to beneficiary"></i> -->
                        </div>
                        <div class="pr">
                           <select class="form-control select" name="custom_question_<?=$que['id']?>" data-live-search="true">
                              <option data-hidden="true"></option>
                                 <?php  
                                 $selected = false;
                                 foreach ($row as $key => $value1) {
                                    $optn_val=$value1;
                                    $selVal = '';
                                    $selectedOption = "";
                                    if ((in_array(trim($value1),$customUestionArr['label']) || in_array(trim($value1),$customUestionArr['file_label'])) && $selected == false && !in_array(trim($value1),$selQuestion)) {
                                       $selVal = $value1;
                                       $selected = true;
                                       $selectedOption = 'selected="selected"';
                                       $selQuestion[] = trim($value1);
                                    }else if ($columnCounter == $key) {
                                       $value1=' ';
                                       $optn_val='None';
                                       $selectedOption = '';
                                    } ?>
                                 <option value="<?=$optn_val?>" <?=$selectedOption?> data-sel="<?=$selVal?>"><?=$value1?></option>
                              <?php } ?>
                           </select>
                           <label class="label-wrap">Select CSV Column</label>
                        </div>
                     </div>
                     <p class="error" id="err_custom_question_<?=$que['id']?>"></p>
                  </div>
               </div>
      <?php } } } ?>
   </div>
<?php } } ?>
