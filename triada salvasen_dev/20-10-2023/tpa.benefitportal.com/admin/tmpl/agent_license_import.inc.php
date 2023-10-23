<?php 
$WRITING_NUMBER_ARR = array(
   array(
       'label' => 'Agent Id',
       'file_label' => 'AGENT_ID',
       'info' => 'Agent Id',
       'field_name' => 'agent_id'
   ),
   array(
       'label' => 'License State',
       'file_label' => 'LICENSE_STATE',
       'info' => 'License State',
       'field_name' => 'license_state'
   ),
   array(
       'label' => 'License Number',
       'file_label' => 'LICENSE_NUMBER',
       'info' => 'License Number',
       'field_name' => 'license_number'
   ),
   array(
      'label' => 'License Active',
      'file_label' => 'LICENSE_ACTIVE',
      'info' => 'License Active',
      'field_name' => 'license_active'
   ),
   array(
      'label' => 'License Expiration',
      'file_label' => 'LICENSE_EXPIRATION',
      'info' => 'License Expiration',
      'field_name' => 'license_expiration'
   ),
   array(
      'label' => 'License Type',
      'file_label' => 'LICENSE_TYPE',
      'info' => 'License Type',
      'field_name' => 'license_type'
   ),
   array(
      'label' => 'Lines Of Authority',
      'file_label' => 'LINES_OF_AUTHORITY',
      'info' => 'Lines Of Authority',
      'field_name' => 'lines_of_authority'
   ),
);
?>

<div class="line_title">
 <h3><span>Member</span></h3>
</div>
<div class="row">
<?php $columnCounter = 0;
foreach($WRITING_NUMBER_ARR as $field) {?>
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
</div>