<?php 
$WRITING_NUMBER_ARR = array(
    array(
        'label' => 'Agent Id',
        'file_label' => 'AGENT_ID',
        'info' => 'Agent Id',
        'field_name' => 'agent_id'
    ),
    array(
        'label' => 'E&O Amount',
        'file_label' => 'E_O_AMOUNT',
        'info' => 'E&O Amount',
        'field_name' => 'e_o_amount'
    ),
    array(
        'label' => 'E&O Expiration',
        'file_label' => 'E_O_EXPIRATION',
        'info' => 'E&O Expiration',
        'field_name' => 'e_o_expiration'
    ),
);
echo '<div class="row">';
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