
<h2 class="m-t-0 m-b-30">Enrollee question</h2>
<?php
if(!empty($customquestionapiResponse)){
   $serialno = 1;
   foreach ($customquestionapiResponse['data'] as $key => $row) {
      $type = $row['Type'];
      $control_name = $key;
      $label = $row['Question'];
      $answers = $row['Answer'];
      if(in_array($type,array('radio','select'))){
         $already_exist_answer = !empty($_POST['bundleQuestion'][$control_name]) ? $_POST['bundleQuestion'][$control_name] : '';
      }else{
         $already_exist_answer = !empty($_POST['bundleQuestion'][$control_name]) ? $_POST['bundleQuestion'][$control_name] : array();
      }

      ?>
      <?php if($type =='radio'){ ?>
         <div class="single-quiz-block <?= !empty($already_exist_answer) ? 'completed' : '' ?>" id="<?= $control_name ?>">
            <div class="quiz-block-head">
               <span class="serial-no"><?= $serialno ?></span>
               <?= $label ?>
            </div>
            <div class="quiz-block-body">
               <div class="row">
                  <div class="col-lg-4 col-md-6">
                  <?php foreach($answers as $k => $v){ ?>
                     <label class="radio-inline" onchange= "questionchangecolor('<?= $control_name ?>','radio')"><input type="radio" name="<?= "bundleQuestion[$control_name]" ?>" value="<?= $k ?>" class="bundleQuestion" <?= $already_exist_answer == $k ? 'checked' : '' ?>><?= $v['Label'] ?></label>
                  <?php } ?>
                  </div>
               </div>
            </div>
         </div>
         <?php $serialno++;
      } else if($type =='checkbox'){ ?>
         <div id="checkbox<?=$control_name ?>">
            <div class="single-quiz-block <?= !empty($already_exist_answer) ? 'completed' : '' ?>" id="<?= $control_name ?>">
               <div class="quiz-block-head">
                  <span class="serial-no"><?= $serialno ?></span>
                  <?= $label ?>
               </div>
               <div class="quiz-block-body">
                  <div class="row">
                     <div class="col-lg-4 col-md-6">
                        <?php foreach($answers as $k => $v){ ?>
                           <div class="checkbox-v">
                              <label>&nbsp;<input type="checkbox" name="bundleQuestion[<?= $control_name ?>][]"  onchange= "questionchangecolor('<?= $control_name ?>','checkbox')" value="<?= $k ?>" class="bundleQuestion" <?= in_array($k,$already_exist_answer) ? 'checked' : '' ?> ><?= $v['Label'] ?></label>
                           </div>
                           <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php $serialno++;
      } else if($type =='select'){ ?>
         <div class="single-quiz-block <?= !empty($already_exist_answer) ? 'completed' : '' ?>" id="<?= $control_name ?>">
            <div class="quiz-block-head">
               <span class="serial-no"><?= $serialno ?></span>
               <?= $label ?>
            </div>
            <div class="quiz-block-body">
               <div class="row">
                  <div class="col-lg-4 col-md-6">
                     <div class="form-group">
                        <select class="form-control" name="bundleQuestion[<?= $control_name ?>]" onchange ="questionchangecolor('<?= $control_name  ?>','select')" id="select<?= $control_name ?>">
                        <?php /*<select class="form-control bundleSelect" name="bundleQuestion[<?= $control_name ?>]" onchange ="questionchangecolor('<?= $control_name  ?>','select')" id="select<?= $control_name ?>" data-question="<?=$control_name?>">*/ ?>
                        <option></option>
                        <?php foreach($answers as $k => $v){ ?>   
                           <option data-hidden="true"></option>
                           <option value="<?=$k?>" <?= $already_exist_answer == $k ? 'selected' : '' ?>><?= $v['Label'] ?></option>
                        <?php } ?>   
                        </select>
                        <label>Select Answer</label>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php $serialno++;
      } else if($type =='select_multiple'){ ?>
         <div class="single-quiz-block <?= !empty($already_exist_answer) ? 'completed' : '' ?>" id="<?= $control_name ?>">
            <div class="quiz-block-head">
               <span class="serial-no"><?= $serialno ?></span>
               <?= $label ?>
            </div>
            <div class="quiz-block-body">
               <div class="row">
                  <div class="col-lg-4 col-md-6">
                     <div class="form-group">
                        <select class="form-control" name="bundleQuestion[<?= $control_name ?>][]"  onchange ="questionchangecolor('<?= $control_name  ?>','multiselect')"  id="multiselect<?= $control_name ?>" required multiple="multiple">
                        <?php /*<select class="form-control multipleBundleSelect" name="bundleQuestion[<?= $control_name ?>][]"  onchange ="questionchangecolor('<?= $control_name  ?>','multiselect')"  id="multiselect<?= $control_name ?>" required multiple="multiple" data-question="<?=$control_name?>"> */?>
                        <?php foreach($answers as $k => $v){ ?>   
                           <option data-hidden="true"></option>
                           <option value="<?=$k?>" <?= in_array($k,$already_exist_answer) ? 'selected' : '' ?>><?= $v['Label'] ?></option>
                        <?php } ?>   
                        </select>
                        <label>Select Answer</label>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      <?php $serialno++;}
   }
}
?>
<hr class="m-t-20">
<div class="clearfix">
   <!-- <a href="javascript:void(0);" class="btn btn-action form_submit" data-step="2">Continue</a> -->
   <button type="button" class="btn btn-action form_submit" data-step="2">Continue</button>
   <button class="enrollmentLeftmenuItem" id="coverageDetailTabBack"><a href="javascript:void(0);" class="btn red-link" data-step="1">Back</a></button>
</div>
