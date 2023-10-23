<?php 
$WRITING_NUMBER_ARR = array(
    array(
        'label' => 'Agent Id',
        'file_label' => 'AGENT_ID',
        'info' => 'Agent Id',
        'field_name' => 'agent_id'
    ),
    array(
        'label' => 'Effective Date',
        'file_label' => 'ACTIVE_DD_DATE',
        'info' => 'Effective Date(MM/DD/YYY)',
        'field_name' => 'effective_date'
    ),
    array(
        'label' => 'Account Type',
        'file_label' => 'ACCOUNT_TYPE',
        'info' => 'Account Type(checking or saving)',
        'field_name' => 'account_type'
    ),
    array(
        'label' => 'Bank Name',
        'file_label' => 'ACTIVE_DD_BANK',
        'info' => 'Bank Name',
        'field_name' => 'bank_name'
    ),
    array(
        'label' => 'Bank Routing Number',
        'file_label' => 'ACTIVE_ROUTING',
        'info' => 'Bank Routing Number',
        'field_name' => 'bank_routing_number'
    ),
    array(
        'label' => 'Bank Account Number',
        'file_label' => 'ACTIVE_ACCT_ID',
        'info' => 'Bank Account Number',
        'field_name' => 'bank_account_number'
    ),
    array(
        'label' => 'Confirm Bank Account Number',
        'file_label' => 'CONFIRM_BANK_ACCOUNT_NUMBER',
        'info' => 'Confirm Bank Account Number',
        'field_name' => 'confirm_bank_account_number'
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