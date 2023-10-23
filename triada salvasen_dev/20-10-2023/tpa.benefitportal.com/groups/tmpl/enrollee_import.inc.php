<?php if(!empty($ENROLEE_IMPORT_ARR)){
   $columnCounter = 0;
   foreach($ENROLEE_IMPORT_ARR as $type => $fieldArr) {
?>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">
                    <strong><?=$fieldArr['label']?></strong>
                </div>
                <div class="pr">
                    <select name="<?=$fieldArr['field_name']?>" id="<?=$fieldArr['field_name']?>" class="form-control select_field" data-live-search="true">
                        <option value=""></option>
                        <?php foreach ($row as $key => $value) {
                                $selectedOption = "";
                                $optn_val=trim($value);
                                if ($fieldArr['label'] == trim($value) || ($fieldArr['file_label'] == trim($value))) {
                                    $selectedOption = 'selected="selected"';
                                } ?>
                            <option value="<?=trim($optn_val)?>" <?=$selectedOption?>><?=$value?></option>
                        <?php } ?>
                    </select>
                    <label class="label-wrap">Select CSV Column</label>
                </div>
            </div>
            <p class="error" id="error_<?=$fieldArr['field_name']?>"></p>
        </div>
    </div>
<?php } } ?>