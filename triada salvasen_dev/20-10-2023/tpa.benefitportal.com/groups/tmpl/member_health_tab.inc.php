<?php if($is_ajaxed){ ?>
<form id="health_details_form" name="health_details_form">
    <input type="hidden" name="is_update" id="is_update" value="1">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1">
    <input type="hidden" name="id" id="id" value="<?= $customer_id ?>">
    <div class="clearfix ">
        <p class="agp_md_title pull-left ">Health Details</p>
        <div class="pull-right">
            <a href="javascript:void(0);" id="btn_edit_health_details"><i class="fa fa-edit fa-lg"></i></a>
            <button type="button" class="btn btn-action btn_save_health_details" style="display: none;">Save</button>
        </div>
    </div>
    <div id="health_details_section" class="thumbnail theme-form">
        <div class="hline-title">
            <span class="text-action fw500"><?= $cust_row['fname'] . ' ' . $cust_row['lname'] ?> Information</span>
        </div>
        <div class="row enrollment_auto_row">
            <?php 
                $primary_benefit_arr = array('primary_benefit_amount','primary_in_patient_benefit','primary_out_patient_benefit','primary_monthly_income','primary_benefit_percentage');
                foreach ($primary_member_field as $key => $row) { ?>
                <?php
                $prd_question_id = $row['id'];
                $is_required = isset($prd_primary_member_field[$key]['required'])?$prd_primary_member_field[$key]['required']:'';
                $control_name = "primary_" . $row['label'];
                $label = $row['display_label'];
                $control_type = $row['control_type'];
                $class = $row['control_class'];
                $maxlength = $row['control_maxlength'];
                $control_attribute = $row['control_attribute'];
                $questionType = $row['questionType'];

                if (in_array($row['label'], array('fname', 'lname', 'SSN', 'phone', 'address1','address2', 'city', 'state', 'zip', 'email', 'birthdate', 'gender'))) {
                    continue;
                }
                $control_value = "";
                if (!empty(${$control_name . '_value'})) {
                    $control_value = ${$control_name . '_value'};
                }
                if ($row['label'] == "weight" || $row['label'] == "hours_per_week") {
                    if ($control_value == 0) {
                        $control_value = '';
                    }
                }
                ?>
                <?php if ($questionType == "Default" && !in_array($control_name,$primary_benefit_arr)) { ?>
                    <?php if ($control_type == 'text') { ?>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="form-group">
                                <input type="text" id="<?= $control_name ?>" maxlength="<?= $maxlength ?>"
                                       name="<?= $control_name ?>" value="<?= $control_value ?>"
                                       class="form-control primary_member_field <?= $class ?> <?= $control_value != '' ? "has-value" : "" ?>"
                                       required readonly disabled>
                                <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                            class="req-indicator">*</span><?php } ?></label>
                                <p class="error" id="error_<?= $control_name ?>"></p>
                            </div>
                        </div>
                    <?php } else if ($control_type == 'date_mask') { ?>
                        <?php
                        $dateValue = '';
                        if ($control_value != '') {
                            $dateValue = date('m/d/Y', strtotime($control_value));
                        }
                        ?>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="form-group">
                                <input type="text" id="<?= $control_name ?>" name="<?= $control_name ?>"
                                       value="<?= $dateValue ?>"
                                       class="form-control primary_member_field <?= $dateValue != "" ? "has-value" : "" ?> <?= $class ?>"
                                       required readonly disabled>
                                <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                            class="req-indicator">*</span><?php } ?></label>
                                <p class="error" id="error_<?= $control_name ?>"></p>
                            </div>
                        </div>
                    <?php } else if ($control_type == 'select') { ?>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="form-group">
                                <select id="<?= $control_name ?>" name="<?= $control_name ?>"
                                        class="selectpicker_input primary_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                        required data-live-search="true" disabled>

                                    <?php if (in_array($control_name, array('primary_height'))) { ?>
                                        <option value=""></option>
                                        <?php for ($i = 1; $i <= 8; $i++) { ?>
                                            <?php for ($j = 0; $j <= 11; $j++) { ?>
                                                <option value="<?= $i . '.' . $j ?>" <?php echo $control_value == $i . '.' . $j ? "selected='selected'" : '' ?>>
                                                    <?php
                                                    echo $i . ' Ft. ';
                                                    if ($j > 0) {
                                                        echo $j . ' In. ';
                                                    }
                                                    ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } else if (in_array($control_name, array('primary_weight'))) { ?>
                                        <option value=""></option>
                                        <?php for ($i = 1; $i <= 1000; $i++) { ?>
                                            <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
                                        <?php } ?>
                                    <?php } else if (in_array($control_name, array('primary_no_of_children'))) { ?>
                                        <option value=""></option>
                                        <?php for ($i = 1; $i <= 15; $i++) { ?>
                                            <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
                                        <?php } ?>
                                    <?php } else if (in_array($control_name, array('primary_pay_frequency'))) { ?>
                                        <option value=""></option>
                                        <option value="Annual" <?php echo $control_value == "Annual" ? "selected='selected'" : '' ?>>
                                            Annual
                                        </option>
                                        <option value="Monthly" <?php echo $control_value == "Monthly" ? "selected='selected'" : '' ?>>
                                            Monthly
                                        </option>
                                        <option value="Semi-Monthly" <?php echo $control_value == "Semi-Monthly" ? "selected='selected'" : '' ?>>
                                            Semi-Monthly
                                        </option>
                                        <option value="Semi-Weekly" <?php echo $control_value == "Semi-Weekly" ? "selected='selected'" : '' ?>>
                                            Semi-Weekly
                                        </option>
                                        <option value="Weekly" <?php echo $control_value == "Weekly" ? "selected='selected'" : '' ?>>
                                            Weekly
                                        </option>
                                        <option value="Hourly" <?php echo $control_value == "Hourly" ? "selected='selected'" : '' ?>>
                                            Hourly
                                        </option>
                                    <?php } ?>
                                </select>
                                <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                            class="req-indicator">*</span><?php } ?></label>
                                <p class="error" id="error_<?= $control_name ?>"></p>
                            </div>
                        </div>
                    <?php } else if ($control_type == 'radio') { ?>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="form-group">
                                <div class="btn-group colors btn-group-justified btn-group-disabled primary_smoking_edit" data-toggle="buttons">
                                    <?php if ($control_name == 'primary_smoking_status') { ?>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="Y"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                   readonly>
                                            Smokes
                                        </label>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="N"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                   readonly>
                                            Non Smokes
                                        </label>
                                    <?php } else if ($control_name == 'primary_tobacco_status') { ?>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="Y"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                   readonly>
                                            Tobacco
                                        </label>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="N"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                   readonly>
                                            Non Tobacco
                                        </label>

                                    <?php } else if ($control_name == 'primary_has_spouse') { ?>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="Y"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                   readonly>
                                            Spouse
                                        </label>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="N"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                   readonly>
                                            No Spouse
                                        </label>
                                    <?php } else if ($control_name == 'primary_employment_status') { ?>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="Y"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                   readonly>
                                            Employed
                                        </label>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="N"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                   readonly>
                                            Unemployed
                                        </label>
                                    <?php } else if ($control_name == 'primary_us_citizen') { ?>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="Y"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                   readonly>
                                            U.S. Citizen
                                        </label>
                                        <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                               disabled>
                                            <input type="checkbox" name="<?= $control_name ?>" value="N"
                                                   class="js-switch primary_member_field"
                                                   autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                   readonly>
                                            Not U.S. Citizen
                                        </label>
                                    <?php } ?>
                                </div>
                                <p class="error" id="error_<?= $control_name ?>"></p>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else {
                    $custom_name = str_replace($prd_question_id, "", $control_name);
                    $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N'";
                    $resAnswer = $pdo->select($sqlAnswer, array(":prd_question_id" => $prd_question_id));
                    ?>
                    <div class="clearfix"></div>
                    <?php if ($control_type == 'select' && !in_array($control_name,$primary_benefit_arr)) { ?>
                        <div class="col-sm-12 form-inline">
                            <div class="form-group  m-r-15">
                                <label><?= $label ?></label>
                            </div>
                            <div class="form-group  w-300 custom_question">
                                <select id="<?= $control_name ?>" name="<?= $custom_name ?>[<?= $prd_question_id ?>]"
                                        class="selectpicker_input primary_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                        required data-live-search="true" disabled>
                                    <option value=""></option>
                                    <?php if (!empty($resAnswer)) {
                                        foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                            <option value="<?= $ansValue['answer'] ?>" <?= ($control_value == $ansValue['answer'] ? 'selected=selected' : '') ?>
                                                    data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                            class="req-indicator">*</span><?php } ?></label>
                                <p class="error" id="error_<?= $control_name ?>"></p>
                            </div>
                        </div>
                    <?php } else if ($control_type == 'radio') { ?>
                        <div class="col-sm-12 form-inline">
                            <div class="form-group  m-r-15">
                                <label><?= $label ?></label>
                            </div>
                            <div class="form-group ">
                                <div class="btn-group colors  custom-question-btn btn-group-disabled question_ans_edit" data-toggle="buttons">
                                    <?php if (!empty($resAnswer)) {
                                        foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                            <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == $ansValue['answer'] ? 'active' : '') ?>"
                                                   disabled>
                                                <input type="checkbox" name="<?= $custom_name ?>[<?= $prd_question_id ?>]"
                                                       value="<?= $ansValue['answer'] ?>" readonly
                                                       data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"
                                                       class="js-switch primary_member_field"
                                                       autocomplete="false" <?= (!empty($control_value) && $control_value == $ansValue['answer'] ? 'checked' : '') ?> > <?= $ansValue['answer'] ?>
                                            </label>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <p class="error" id="error_<?= $control_name ?>"></p>
                            </div>
                        </div>
                    <?php } else if ($control_type == 'select_multiple') { ?>
                        <div class="col-sm-12 form-inline">
                            <div class="form-group  m-r-15">
                                <?= $label ?>
                            </div>
                            <div class="form-group  w-300 custom_question">
                                <select id="<?= $control_name ?>" name="<?= $custom_name ?>[<?= $prd_question_id ?>][]"
                                        class="se_multiple_select primary_multiple_select primary_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                        disabled multiple="multiple">
                                    <?php if (!empty($resAnswer)) {
                                        $tmp_control_value = explode(',', $control_value);
                                        foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                            <option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'], $tmp_control_value) ? 'selected=selected' : '') ?>
                                                    data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                            class="req-indicator">*</span><?php } ?></label>
                                <p class="error" id="error_<?= $control_name ?>"></p>
                            </div>
                        </div>
                    <?php } else if ($control_type == 'textarea') { ?> 
                            <div class="col-sm-12 form-inline">
                                <div class="form-group  m-r-15">
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                </div>
                                <div class="form-group  w-300 custom_question">
                                    <textarea name="<?= $custom_name ?>[<?= $prd_question_id ?>]" class="form-control <?= $control_value != '' ? "has-value" : "" ?>" 
                                    rows="3" maxlength="300" readonly="" disabled=""><?= $control_value ?></textarea>
                                    <p class="error" id="error_<?= $control_name ?>"></p>
                                </div>       
                            </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <?php if(!empty($prd_benefit_amount['Primary'])) { ?>
                <?php
                foreach ($prd_benefit_amount['Primary'] as $product_id => $benefit_amount_res) {
                 $productName = getname('prd_main', $product_id, 'name', 'id'); ?>
                 <input type="hidden" name="primary_product[<?=$product_id?>]" value="<?=$product_id?>">
                 <?php
                 foreach($benefit_amount_res as $benefitKey => $benefitValue){ ?>
                    <div class="col-sm-12"><p class="font-bold m-b-15"><?=ucwords(str_replace('_',' ',$benefitKey))?></p></div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <select name="primary_<?=$benefitKey?>[<?= $product_id ?>]" id="primary_<?=$benefitKey?>_<?= $product_id ?>" class="selectpicker_input form-control <?=!empty($primary_benefit_amount[$benefitKey][$product_id])?"has-value":""?>" disabled>
                                <option data-hidden="true"></option>
                                <?php 
                                    foreach ($benefitValue as $key => $benefit_amount) {
                                        ?>
                                        <option value="<?=$benefit_amount?>" <?=!empty($primary_benefit_amount[$benefitKey][$product_id]) && $primary_benefit_amount[$benefitKey][$product_id] == $benefit_amount?"selected=selected":"" ?>><?=$benefit_amount?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                            <label><?= $productName ?> <?=ucwords(str_replace('_',' ',$benefitKey))?></label>
                            <p class="error" id="error_<?=$benefitKey?>_<?= $product_id ?>"></p>
                        </div>
                    </div>
                    <?php
                 }
                }
                ?>
            <?php } ?>
        </div>

        <?php
        if (!empty($spouse_res)) {
            $spouse_benefit_arr = array('spouse_benefit_amount','spouse_in_patient_benefit','spouse_out_patient_benefit','spouse_monthly_income','spouse_benefit_percentage');
            foreach ($spouse_res as $spouse_key => $spouse_row) {
                $number = ($spouse_key + 1);
                ?>
                <div class="hline-title">
                    <input type="hidden" name="spouse_ids[<?=$number?>]" value=<?=$spouse_row['id']?>>
                    <span class="text-action fw500"><?= $spouse_row['fname'] . ' ' . $spouse_row['lname'] . ' (' . $spouse_row['display_id'] . ')'; ?> Information</span>
                </div>
                <div class="row enrollment_auto_row">
                    <?php foreach ($spouse_field as $key => $row) { ?>
                        <?php
                        $prd_question_id = $row['id'];
                        $is_required = isset($prd_spouse_field[$key]['required'])?$prd_spouse_field[$key]['required']:'';
                        $control_name = "spouse_" . $row['label'];
                        $label = $row['display_label'];
                        $control_type = $row['control_type'];
                        $class = $row['control_class'];
                        $maxlength = $row['control_maxlength'];
                        $control_attribute = $row['control_attribute'];
                        $questionType = $row['questionType'];

                        if (in_array($row['label'], array('fname', 'lname', 'SSN', 'address1','address2', 'city', 'state', 'zip', 'birthdate', 'gender'))) {
                            continue;
                        }

                        $tmp_control_name = get_column_name_by_control_name($row['label']);

                        $control_value = (isset($spouse_row[$tmp_control_name]) ? $spouse_row[$tmp_control_name] : '');
                        if ($row['label'] == "weight" || $row['label'] == "hours_per_week") {
                            if ($control_value == 0) {
                                $control_value = '';
                            }
                        }
                        if ($row['label'] == "height") {
                            $control_value = '';
                            if (!empty($spouse_row['height_feet'])) {
                                $control_value = $spouse_row['height_feet'];

                                if (!empty($spouse_row['height_inches'])) {
                                    $control_value .= '.' . $spouse_row['height_inches'];
                                } else {
                                    $control_value .= '.0';
                                }
                            }
                        }
                        ?>
                        <?php if ($questionType == "Default" && !in_array($control_name,$spouse_benefit_arr)) { ?>
                            <?php if ($control_type == 'text') { ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="<?= $control_name ?>_<?= $number ?>" maxlength="<?= $maxlength ?>"
                                               name="<?= $control_name ?>[<?= $number ?>]" value="<?= $control_value ?>"
                                               class="form-control spouse_member_field <?= $class ?> <?= $control_value != '' ? "has-value" : "" ?> <?= ($control_name == "spouse_email") ? "no_space" : ""; ?>"
                                               required readonly>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'date_mask') { ?>
                                <?php
                                $dateValue = '';
                                if ($control_value != '') {
                                    $dateValue = date('m/d/Y', strtotime($control_value));
                                }
                                ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="<?= $control_name ?>_<?= $number ?>"
                                               name="<?= $control_name ?>[<?= $number ?>]"
                                               value="<?= $dateValue ?>"
                                               class="form-control spouse_member_field <?= $dateValue != "" ? "has-value" : "" ?> <?= $class ?>"
                                               required readonly>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'select') { ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <select id="<?= $control_name ?>_<?= $number ?>" name="<?= $control_name ?>[<?= $number ?>]"
                                                class="selectpicker_input spouse_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                                required data-live-search="true" disabled data="<?= $control_value ?>">
                                            <?php if ($control_name == 'spouse_state') { ?>
                                                <?php foreach ($state_res as $key => $value) { ?>
                                                    <option data-state_id="<?= $value["id"]; ?>"
                                                            value="<?= $value["name"]; ?>" <?php echo $value["name"] == $control_value ? 'selected' : '' ?>><?php echo $value['name']; ?></option>
                                                <?php } ?>

                                            <?php } else if (in_array($control_name, array('spouse_height'))) { ?>
                                                <option value=""></option>
                                                <?php for ($i = 1; $i <= 8; $i++) { ?>
                                                    <?php for ($j = 0; $j <= 11; $j++) { ?>
                                                        <option value="<?= $i . '.' . $j ?>" <?php echo $control_value == $i . '.' . $j ? "selected='selected'" : '' ?>>
                                                            <?php
                                                            echo $i . ' Ft. ';
                                                            if ($j > 0) {
                                                                echo $j . ' In. ';
                                                            }
                                                            ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else if (in_array($control_name, array('spouse_weight'))) { ?>
                                                <option value=""></option>
                                                <?php for ($i = 1; $i <= 1000; $i++) { ?>
                                                    <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
                                                <?php } ?>
                                            <?php } else if (in_array($control_name, array('spouse_no_of_children'))) { ?>
                                                <option value=""></option>
                                                <?php for ($i = 1; $i <= 15; $i++) { ?>
                                                    <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
                                                <?php } ?>
                                            <?php } else if (in_array($control_name, array('spouse_pay_frequency'))) { ?>
                                                <option value=""></option>
                                                <option value="Annual" <?php echo $control_value == "Annual" ? "selected='selected'" : '' ?>>
                                                    Annual
                                                </option>
                                                <option value="Monthly" <?php echo $control_value == "Monthly" ? "selected='selected'" : '' ?>>
                                                    Monthly
                                                </option>
                                                <option value="Semi-Monthly" <?php echo $control_value == "Semi-Monthly" ? "selected='selected'" : '' ?>>
                                                    Semi-Monthly
                                                </option>
                                                <option value="Semi-Weekly" <?php echo $control_value == "Semi-Weekly" ? "selected='selected'" : '' ?>>
                                                    Semi-Weekly
                                                </option>
                                                <option value="Weekly" <?php echo $control_value == "Weekly" ? "selected='selected'" : '' ?>>
                                                    Weekly
                                                </option>
                                                <option value="Hourly" <?php echo $control_value == "Hourly" ? "selected='selected'" : '' ?>>
                                                    Hourly
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'radio') { ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <div class="btn-group colors btn-group-justified btn-group-disabled spouse_smoking_edit" data-toggle="buttons">
                                            <?php if ($control_name == 'spouse_smoking_status') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly>
                                                    Smokes
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly>
                                                    Non Smokes
                                                </label>
                                            <?php } else if ($control_name == 'spouse_tobacco_status') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly>
                                                    Tobacco
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly>
                                                    Non Tobacco
                                                </label>
                                            <?php } else if ($control_name == 'spouse_has_spouse') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly>
                                                    Spouse
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly>
                                                    No Spouse
                                                </label>
                                            <?php } else if ($control_name == 'spouse_employment_status') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly>
                                                    Employed
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly>
                                                    Unemployed
                                                </label>
                                            <?php } else if ($control_name == 'spouse_us_citizen') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly>
                                                    U.S. Citizen
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch spouse_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly>
                                                    Not U.S. Citizen
                                                </label>
                                            <?php } ?>
                                        </div>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else {
                            $custom_name = str_replace($prd_question_id, "", $control_name);
                            $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N'";
                            $resAnswer = $pdo->select($sqlAnswer, array(":prd_question_id" => $prd_question_id));
                            ?>
                            <div class="clearfix"></div>

                            <?php if ($control_type == 'select' && !in_array($control_name,$spouse_benefit_arr)) { ?>
                                <div class="col-sm-12 form-inline">
                                    <div class="form-group  m-r-15">
                                        <label><?= $label ?></label>
                                    </div>
                                    <div class="form-group  w-300 custom_question">
                                        <select id="<?= $control_name ?>_<?= $number ?>"
                                                name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]"
                                                class="selectpicker_input spouse_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                                required data-live-search="true" disabled>
                                            <option value=""></option>
                                            <?php if (!empty($resAnswer)) {
                                                foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                                    <option value="<?= $ansValue['answer'] ?>" <?= ($control_value == $ansValue['answer'] ? 'selected=selected' : '') ?>
                                                            data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'radio') { ?>
                                <div class="col-sm-12 form-inline">
                                    <div class="form-group  m-r-15">
                                        <label><?= $label ?></label>
                                    </div>
                                    <div class="form-group ">
                                        <div class="btn-group colors custom-question-btn btn-group-disabled question_ans_edit" data-toggle="buttons">
                                            <?php if (!empty($resAnswer)) {
                                                foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                                    <label class="btn btn-info btn_custom_radio  <?= (!empty($control_value) && $control_value == $ansValue['answer'] ? 'active' : '') ?>"
                                                           disabled>
                                                        <input type="checkbox"
                                                               name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]"
                                                               value="<?= $ansValue['answer'] ?>" readonly
                                                               data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"
                                                               class="js-switch spouse_member_field"
                                                               autocomplete="false" <?= (!empty($control_value) && $control_value == $ansValue['answer'] ? 'checked' : '') ?> > <?= $ansValue['answer'] ?>
                                                    </label>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'select_multiple') { ?>
                                <div class="col-sm-12 form-inline m-b-20">
                                    <div class="form-group  m-r-15">
                                        <label><?= $label ?></label>
                                    </div>
                                    <div class="form-group  w-300 custom_question">
                                        <select id="<?= $control_name ?>_<?= $number ?>"
                                                name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>][]"
                                                class="se_multiple_select spouse_multiple_select spouse_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                                disabled multiple="multiple">
                                            <?php if (!empty($resAnswer)) {
                                                $tmp_control_value = explode(',', $control_value);
                                                foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                                    <option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'], $tmp_control_value) ? 'selected=selected' : '') ?>
                                                            data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'textarea') {  ?> 
                                <div class="col-sm-12 form-inline">
                                    <div class="form-group  m-r-15">
                                            <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                    </div>
                                    <div class="form-group  w-300 custom_question">
                                        <textarea name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]" class="form-control <?= $control_value != '' ? "has-value" : "" ?>" 
                                                rows="3" maxlength="300" readonly="" disabled=""><?= $control_value ?></textarea>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>

                    <?php if(!empty($prd_benefit_amount['Spouse'])) {
                       foreach ($prd_benefit_amount['Spouse'] as $product_id => $benefit_amount_res) {
                        $productName = getname('prd_main', $product_id, 'name', 'id');
                        ?>
                        <input type="hidden" name="spouse_product[<?= $number ?>][<?=$product_id?>]" value="<?=$product_id?>">
                        <?php
                        foreach($benefit_amount_res as $benefitKey => $benefitValue){
                            ?>
                            <div class="col-sm-12"><p class="font-bold m-b-15"><?=ucwords(str_replace('_',' ',$benefitKey))?></p></div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <select name="spouse_<?=$benefitKey?>[<?= $number ?>][<?= $product_id ?>]" id="spouse_<?=$benefitKey?>_<?= $number ?>_<?= $product_id ?>" class="selectpicker_input form-control <?=!empty($spouse_row[$benefitKey][$product_id])?"has-value":""?>" disabled>
                                        <option data-hidden="true"></option>
                                        <?php 
                                            foreach ($benefitValue as $key => $benefit_amount) {
                                                ?>
                                                <option data-bamount="<?=$spouse_row[$benefitKey][$product_id]?>" value="<?=$benefit_amount?>" <?=!empty($spouse_row[$benefitKey][$product_id]) && $spouse_row[$benefitKey][$product_id] == $benefit_amount?"selected=selected":"" ?>><?=$benefit_amount?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                    <label><?= $productName ?> <?=ucwords(str_replace('_',' ',$benefitKey))?></label>
                                    <p class="error" id="error_spouse_<?=$benefitKey?>_<?= $number ?>_<?= $product_id ?>"></p>
                                </div>
                            </div>
                            <?php
                        }
                       }
                      ?>
                    <?php } ?>
                </div>
                <?php
            }
        }
        ?>

        <?php
        if (!empty($child_res)) {
            $child_benefit_arr = array('child_benefit_amount','child_in_patient_benefit','child_out_patient_benefit','child_monthly_income','child_benefit_percentage');
            foreach ($child_res as $child_key => $child_row) {
                $number = $child_key + 1;
                ?>
                <div class="hline-title">
                    <input type="hidden" name="child_ids[<?=$number?>]" value=<?=$child_row['id']?>>
                    <span class="text-action fw500"><?= $child_row['fname'] . ' ' . $child_row['lname'] . ' (' . $child_row['display_id'] . ')'; ?> Information</span>
                </div>
                <div class="row enrollment_auto_row">
                    <?php foreach ($child_field as $key => $row) { ?>
                        <?php

                        $prd_question_id = $row['id'];
                        $is_required = isset($prd_child_field[$key]['required'])?$prd_child_field[$key]['required']:'';
                        $control_name = "child_" . $row['label'];
                        $label = $row['display_label'];
                        $control_type = $row['control_type'];
                        $class = $row['control_class'];
                        $maxlength = $row['control_maxlength'];
                        $control_attribute = $row['control_attribute'];
                        $questionType = $row['questionType'];

                        if (in_array($row['label'], array('fname', 'lname', 'SSN', 'address1','address2', 'city', 'state', 'zip', 'birthdate', 'gender'))) {
                            continue;
                        }

                        $tmp_control_name = get_column_name_by_control_name($row['label']);
                        $control_value = (isset($child_row[$tmp_control_name]) ? $child_row[$tmp_control_name] : '');
                        if ($row['label'] == "weight" || $row['label'] == "hours_per_week") {
                            if ($control_value == 0) {
                                $control_value = '';
                            }
                        }
                        if ($row['label'] == "height") {
                            $control_value = '';
                            if (!empty($child_row['height_feet'])) {
                                $control_value = $child_row['height_feet'];

                                if (!empty($child_row['height_inches'])) {
                                    $control_value .= '.' . $child_row['height_inches'];
                                } else {
                                    $control_value .= '.0';
                                }
                            }
                        }
                        ?>
                        <?php if ($questionType == "Default" && !in_array($control_name,$child_benefit_arr)) { ?>
                            <?php if ($control_type == 'text') { ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="<?= $control_name ?>_<?= $number ?>"
                                               maxlength="<?= $maxlength ?>"
                                               name="<?= $control_name ?>[<?= $number ?>]" value="<?= $control_value ?>"
                                               class="form-control child_member_field <?= $class ?> <?= $control_value != '' ? "has-value" : "" ?> <?= ($control_name == "child_email") ? "no_space" : ""; ?>"
                                               required readonly
                                               data-id="<?= $number ?>">
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'date_mask') { ?>
                                <?php
                                $dateValue = '';
                                if ($control_value != '') {
                                    $dateValue = date('m/d/Y', strtotime($control_value));
                                }
                                ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="<?= $control_name ?>_<?= $number ?>"
                                               name="<?= $control_name ?>[<?= $number ?>]" value="<?= $dateValue ?>"
                                               class="form-control child_member_field <?= $dateValue != "" ? "has-value" : "" ?> <?= $class ?>"
                                               required readonly
                                               data-id="<?= $number ?>">
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'select') { ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <select id="<?= $control_name ?>_<?= $number ?>"
                                                name="<?= $control_name ?>[<?= $number ?>]"
                                                class="selectpicker_input child_select_<?= $number ?> child_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                                required data-live-search="true" data-id="<?= $number ?>" disabled>
                                            <?php if ($control_name == 'child_state') { ?>
                                                <?php foreach ($state_res as $key => $value) { ?>
                                                    <option data-state_id="<?= $value["id"]; ?>"
                                                            value="<?= $value["name"]; ?>" <?php echo $value["name"] == $control_value ? 'selected' : '' ?>><?php echo $value['name']; ?></option>
                                                <?php } ?>

                                            <?php } else if (in_array($control_name, array('child_height'))) { ?>
                                                <option value=""></option>
                                                <?php for ($i = 1; $i <= 8; $i++) { ?>
                                                    <?php for ($j = 0; $j <= 11; $j++) { ?>
                                                        <option value="<?= $i . '.' . $j ?>" <?php echo $control_value == $i . '.' . $j ? "selected='selected'" : '' ?>>
                                                            <?php
                                                            echo $i . ' Ft. ';
                                                            if ($j > 0) {
                                                                echo $j . ' In. ';
                                                            }
                                                            ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else if (in_array($control_name, array('child_weight'))) { ?>
                                                <option value=""></option>
                                                <?php for ($i = 1; $i <= 1000; $i++) { ?>
                                                    <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
                                                <?php } ?>
                                            <?php } else if (in_array($control_name, array('child_no_of_children'))) { ?>
                                                <option value=""></option>
                                                <?php for ($i = 1; $i <= 15; $i++) { ?>
                                                    <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
                                                <?php } ?>
                                            <?php } else if (in_array($control_name, array('child_pay_frequency'))) { ?>
                                                <option value=""></option>
                                                <option value="Annual" <?php echo $control_value == "Annual" ? "selected='selected'" : '' ?>>
                                                    Annual
                                                </option>
                                                <option value="Monthly" <?php echo $control_value == "Monthly" ? "selected='selected'" : '' ?>>
                                                    Monthly
                                                </option>
                                                <option value="Semi-Monthly" <?php echo $control_value == "Semi-Monthly" ? "selected='selected'" : '' ?>>
                                                    Semi-Monthly
                                                </option>
                                                <option value="Semi-Weekly" <?php echo $control_value == "Semi-Weekly" ? "selected='selected'" : '' ?>>
                                                    Semi-Weekly
                                                </option>
                                                <option value="Weekly" <?php echo $control_value == "Weekly" ? "selected='selected'" : '' ?>>
                                                    Weekly
                                                </option>
                                                <option value="Hourly" <?php echo $control_value == "Hourly" ? "selected='selected'" : '' ?>>
                                                    Hourly
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'radio') { ?>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <div class="btn-group colors btn-group-justified  btn-group-disabled child_smoking_edit" data-toggle="buttons">
                                            <?php if ($control_name == 'child_smoking_status') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Smokes
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Non Smokes
                                                </label>
                                            <?php } else if ($control_name == 'child_tobacco_status') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Tobacco
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Non Tobacco
                                                </label>

                                            <?php } else if ($control_name == 'child_has_spouse') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Spouse
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> No Spouse
                                                </label>
                                            <?php } else if ($control_name == 'child_employment_status') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Employed
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Unemployed
                                                </label>
                                            <?php } else if ($control_name == 'child_us_citizen') { ?>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'Y' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="Y"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'Y' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> U.S. Citizen
                                                </label>
                                                <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == 'N' ? 'active' : '') ?>"
                                                       disabled>
                                                    <input type="checkbox" name="<?= $control_name ?>[<?= $number ?>]"
                                                           value="N"
                                                           class="js-switch child_member_field"
                                                           autocomplete="false" <?= (!empty($control_value) && $control_value == 'N' ? 'checked' : '') ?>
                                                           readonly
                                                           data-id="<?= $number ?>"> Not U.S. Citizen
                                                </label>
                                            <?php } ?>
                                        </div>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else {
                            $custom_name = str_replace($prd_question_id, "", $control_name);
                            $sqlAnswer = "SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N'";
                            $resAnswer = $pdo->select($sqlAnswer, array(":prd_question_id" => $prd_question_id));
                            ?>
                            <div class="clearfix"></div>

                            <?php if ($control_type == 'select' && !in_array($control_name,$child_benefit_arr)) { ?>
                                <div class="col-sm-12 form-inline">
                                    <div class="form-group  m-r-15">
                                        <label><?= $label ?></label>
                                    </div>
                                    <div class="form-group  w-300 custom_question">
                                        <select id="<?= $control_name ?>_<?= $number ?>"
                                                name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]"
                                                class="selectpicker_input child_select_<?= $number ?> child_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                                required data-live-search="true" data-id="<?= $number ?>" disabled>
                                            <option value=""></option>
                                            <?php if (!empty($resAnswer)) {
                                                foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                                    <option value="<?= $ansValue['answer'] ?>" <?= ($control_value == $ansValue['answer'] ? 'selected=selected' : '') ?>
                                                            data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'radio') { ?>
                                <div class="col-sm-12 form-inline">
                                    <div class="form-group  m-r-15">
                                        <label><?= $label ?></label>
                                    </div>
                                    <div class="form-group ">
                                        <div class="btn-group colors custom-question-btn btn-group-disabled question_ans_edit" data-toggle="buttons">
                                            <?php if (!empty($resAnswer)) {
                                                foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                                    <label class="btn btn-info btn_custom_radio <?= (!empty($control_value) && $control_value == $ansValue['answer'] ? 'active' : '') ?>"
                                                           disabled>
                                                        <input type="checkbox"
                                                               name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]"
                                                               value="<?= $ansValue['answer'] ?>" readonly
                                                               data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"
                                                               class="js-switch child_member_field"
                                                               autocomplete="false" <?= (!empty($control_value) && $control_value == $ansValue['answer'] ? 'checked' : '') ?>
                                                               data-id="<?= $number ?>"> <?= $ansValue['answer'] ?>
                                                    </label>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'select_multiple') { ?>
                                <div class="col-sm-12 form-inline">
                                    <div class="form-group  m-r-15">
                                        <label><?= $label ?></label>
                                    </div>
                                    <div class="form-group  w-300 custom_question">
                                        <select id="<?= $control_name ?>_<?= $number ?>"
                                                name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>][]"
                                                class="se_multiple_select child_multiple_select child_member_field <?= $control_value != '' ? "has-value" : "" ?>"
                                                disabled multiple="multiple" data-id="<?= $number ?>">
                                            <?php if (!empty($resAnswer)) {
                                                $tmp_control_value = explode(',', $control_value);
                                                foreach ($resAnswer as $ansKey => $ansValue) { ?>
                                                    <option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'], $tmp_control_value) ? 'selected=selected' : '') ?>
                                                            data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>
                                </div>
                            <?php } else if ($control_type == 'textarea') { ?> 
                                <div class="col-sm-12 form-inline">
                                    <div class="form-group  m-r-15">
                                            <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                        class="req-indicator">*</span><?php } ?></label>
                                    </div>
                                    <div class="form-group  w-300 custom_question">
                                        <textarea name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]" class="form-control <?= $control_value != '' ? "has-value" : "" ?>"
                                                            rows="3" maxlength="300" readonly="" disabled=""><?= $control_value ?></textarea>
                                        <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                    </div>       
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <?php if(!empty($prd_benefit_amount['Child'])) { ?>
                    <?php
                        foreach ($prd_benefit_amount['Child'] as $product_id => $benefit_amount_res) {
                            $productName = getname('prd_main', $product_id, 'name', 'id');
                            ?>
                        <input type="hidden" name="child_product[<?= $number ?>][<?=$product_id?>]" value="<?=$product_id?>">
                        <?php
                        foreach($benefit_amount_res as $benefitKey => $benefitValue){
                            ?>
                            <div class="col-sm-12"><p class="font-bold m-b-15"><?=ucwords(str_replace('_',' ',$benefitKey))?></p></div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <select name="child_<?=$benefitKey?>[<?= $number ?>][<?= $product_id ?>]" id="child_<?=$benefitKey?>_<?= $number ?>_<?= $product_id ?>" class="selectpicker_input form-control <?=!empty($child_row[$benefitKey][$product_id])?"has-value":""?>" disabled>
                                        <option data-hidden="true"></option>
                                        <?php 
                                            foreach ($benefitValue as $key => $benefit_amount) {
                                                ?>
                                                <option value="<?=$benefit_amount?>" <?=!empty($child_row[$benefitKey][$product_id]) && $child_row[$benefitKey][$product_id] == $benefit_amount?"selected=selected":"" ?>><?=$benefit_amount?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                    <label><?= $productName ?> <?=ucwords(str_replace('_',' ',$benefitKey))?></label>
                                    <p class="error" id="error_child_<?=$benefitKey?>_<?= $number ?>_<?= $product_id ?>"></p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <?php } ?>
                </div>
                <?php
            }
        }
        ?>
        <div id="beneficiary_section">
            <h4 class="m-b-25">Beneficiary Information</h4>
            <div id="principal_beneficiary_div">
                <p class="font-bold m-b-15">Principal Beneficiary</p>
                <p class="m-b-25">I choose the person(s) named below to be the principal beneficiary(ies) of the Life
                    Insurance
                    benefits that may be payable at the time of my death. If any principal beneficiary(ies) is
                    disqualified or
                    dies before me, his/her percentage of this benefit will be paid to the remaining principal
                    beneficiary(ies).</p>
                <p class="m-b-25">*The percentage awarded between all principal beneficiary(ies) must add up to 100%</p>
                <div class="theme-form">
                    <div id="principal_beneficiary_field_div">
                        <?php
                        if (!empty($principal_ben_res)) {
                            foreach ($principal_ben_res as $principal_ben_key => $principal_ben_row) {
                                $number = $principal_ben_key + 1;
                                $principal_ben_row['queBeneficiaryFullName'] = $principal_ben_row['name'];
                                $principal_ben_row['queBeneficiaryAddress'] = $principal_ben_row['address'];
                                $principal_ben_row['queBeneficiaryPhone'] = format_telephone($principal_ben_row['cell_phone']);
                                $principal_ben_row['queBeneficiaryEmail'] = $principal_ben_row['email'];
                                $principal_ben_row['queBeneficiarySSN'] = $principal_ben_row['dssn'];
                                $principal_ben_row['queBeneficiaryRelationship'] = $principal_ben_row['relationship'];
                                $principal_ben_row['queBeneficiaryPercentage'] = $principal_ben_row['percentage'];
                                ?>
                                <div id="inner_principal_beneficiary_field_<?= $number ?>"
                                     class="inner_principal_beneficiary_field">
                                    <div class="clearfix m-b-25">
                                        <h5 class="mn pull-left">Principal Beneficiary <span
                                                    data-display_number="<?= $number ?>" data-id="<?= $number ?>"
                                                    id="principal_beneficiary_number_<?= $number ?>"
                                                    class="display_principal_beneficiary_number"><?= $number ?></span>
                                            <?php if ($number > 1) { ?>
                                                <button type="button" class="red-link removePrincipalBeneficiaryField"
                                                        data-id="<?= $number ?>" disabled>Remove
                                                </button>
                                            <?php } ?>
                                        </h5>
                                    </div>
                                    <div class="row enrollment_auto_row" style="display: none;">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input type="hidden" name="principal_beneficiary_id[<?= $number ?>]"
                                                        value="<?=$principal_ben_row['id']?>">
                                                <select id="principal_existing_dependent_<?= $number ?>"
                                                        name="principal_existing_dependent[<?= $number ?>]"
                                                        class="selectpicker_input has-value principal_beneficiary_select_<?= $number ?> principal_beneficiary_select"
                                                        data-id="<?= $number ?>" data-select-val="" disabled>
                                                    <option value=""></option>
                                                    <?php if (!empty($dep_res)) { ?>
                                                        <?php foreach ($dep_res as $key => $row) { ?>
                                                            <?php if (!empty($row['fname'])) { ?>
                                                                <option value="<?= $row['id'] ?>"
                                                                    <?= $row['fname'] . ' ' . $row['lname'] == $principal_ben_row['queBeneficiaryFullName'] ? "selected=selected" : "" ?>
                                                                        data-full-name="<?= $row['fname'] . ' ' . $row['lname'] ?>"
                                                                        data-type="<?= $row['type'] ?>"
                                                                        data-fname="<?= $row['fname'] ?>"
                                                                        data-lname="<?= $row['lname'] ?>"
                                                                        data-phone="<?= $row['phone'] ?>"
                                                                        data-email="<?= $row['email'] ?>"
                                                                        data-ssn="<?= $row['ssn'] ?>"
                                                                        data-address="<?= $row['address'] ?>"
                                                                        style="<?= !empty($principal_existing_dependent) && in_array($row['id'], $principal_existing_dependent) ? 'display:none' : '' ?>"><?= $row['fname'] . ' ' . $row['lname'] . ' (' . $row['type'] . ')' ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label>Select Existing Dependent</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row enrollment_auto_row">
                                        <?php foreach ($principal_beneficiary_field as $key => $row) { ?>
                                            <?php
                                            $prd_question_id = $row['id'];
                                            $is_required = $row['required'];
                                            $control_name = 'principal_' . $row['label'];
                                            $label = $row['display_label'];
                                            $control_type = $row['control_type'];
                                            $class = $row['control_class'];
                                            $maxlength = $row['control_maxlength'];
                                            $control_attribute = $row['control_attribute'];
                                            $questionType = $row['questionType'];
                                            if ($control_name == "principal_queBeneficiaryAllow3") {
                                                continue;
                                            }

                                            $control_value = isset($principal_ben_row[$row['label']]) ? $principal_ben_row[$row['label']] : '';
                                            ?>
                                            <?php if ($control_type == 'text') { ?>
                                                <div class="col-lg-3 col-md-6">
                                                    <?php if ($control_name == "principal_queBeneficiaryPercentage") { ?>
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="pr">
                                                                    <input type="text"
                                                                           id="<?= $control_name ?>_<?= $number ?>"
                                                                           maxlength="<?= $maxlength ?>"
                                                                           name="<?= $control_name ?>[<?= $number ?>]"
                                                                           value="<?= $control_value ?>"
                                                                           class="form-control has-value <?= $class ?>"
                                                                           required
                                                                           data-id="<?= $number ?>"
                                                                           onkeypress="return isNumber(event)" disabled>
                                                                    <label><?= $label ?><?php if ($is_required == 'Y') { ?>
                                                                            <span
                                                                                    class="req-indicator">*</span><?php } ?>
                                                                    </label>
                                                                </div>
                                                                <div class="input-group-addon"> %</div>
                                                            </div>
                                                            <p class="error"
                                                               id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="form-group">
                                                            <input type="text" id="<?= $control_name ?>_<?= $number ?>"
                                                                   maxlength="<?= $maxlength ?>"
                                                                   name="<?= $control_name ?>[<?= $number ?>]"
                                                                   value="<?= $control_value; ?>"
                                                                   class="form-control has-value <?= ($control_name == "principal_queBeneficiaryEmail") ? "no_space" : ""; ?> <?= $class ?>" required
                                                                   data-id="<?= $number ?>" disabled>
                                                            <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                                        class="req-indicator">*</span><?php } ?></label>
                                                            <p class="error"
                                                               id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            <?php } else if ($control_type == 'select') { ?>
                                                <div class="col-lg-3 col-md-6">
                                                    <div class="form-group">
                                                        <select id="<?= $control_name ?>_<?= $number ?>"
                                                                name="<?= $control_name ?>[<?= $number ?>]"
                                                                class="selectpicker_input has-value principal_beneficiary_select_<?= $number ?>"
                                                                required
                                                                data-live-search="true" data-id="<?= $number ?>"
                                                                disabled>
                                                            <option value="" hidden></option>
                                                            <?php if ($control_name == 'principal_queBeneficiaryRelationship') { ?>
                                                                <option value="Child" <?= ($control_value == "Child" ? 'selected=selected' : '') ?>>
                                                                    Child
                                                                </option>
                                                                <option value="Spouse" <?= ($control_value == "Spouse" ? 'selected=selected' : '') ?>>
                                                                    Spouse
                                                                </option>
                                                                <option value="Parent" <?= ($control_value == "Parent" ? 'selected=selected' : '') ?>>
                                                                    Parent
                                                                </option>
                                                                <option value="Grandparent" <?= ($control_value == "Grandparent" ? 'selected=selected' : '') ?>>
                                                                    Grandparent
                                                                </option>
                                                                <option value="Friend" <?= ($control_value == "Friend" ? 'selected=selected' : '') ?>>
                                                                    Friend
                                                                </option>
                                                                <option value="Other" <?= ($control_value == "Other" ? 'selected=selected' : '') ?>>
                                                                    Other
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                                    class="req-indicator">*</span><?php } ?></label>
                                                        <p class="error"
                                                           id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <hr>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-info" id="addPrincipalBeneficiaryField"
                        data-allow-upto="3" disabled>+ Principal Beneficiary
                </button>
                <p class="error" id="error_principal_beneficiary_general"></p>
                <hr>
            </div>
            <div id="contingent_beneficiary_div">
                <p class="font-bold m-b-15">Contingent Beneficiary</p>
                <p class="m-t-25">If all principal beneficiaries are disqualified or die before me, I choose the
                    person(s) named
                    below to be my contingent beneficiar(ies).</p>
                <p class="m-t-25">*The percentage awarded between all contingent beneficiary(ies) must add up to
                    100%</p>
                <div class="theme-form">
                    <div id="contingent_beneficiary_field_div">
                        <?php
                        if (!empty($contingent_ben_res)) {
                            foreach ($contingent_ben_res as $contingent_ben_key => $contingent_ben_row) {
                                $number = $contingent_ben_key + 1;
                                $contingent_ben_row['queBeneficiaryFullName'] = $contingent_ben_row['name'];
                                $contingent_ben_row['queBeneficiaryAddress'] = $contingent_ben_row['address'];
                                $contingent_ben_row['queBeneficiaryPhone'] = format_telephone($contingent_ben_row['cell_phone']);
                                $contingent_ben_row['queBeneficiaryEmail'] = $contingent_ben_row['email'];
                                $contingent_ben_row['queBeneficiarySSN'] = $contingent_ben_row['dssn'];
                                $contingent_ben_row['queBeneficiaryRelationship'] = $contingent_ben_row['relationship'];
                                $contingent_ben_row['queBeneficiaryPercentage'] = $contingent_ben_row['percentage'];
                                ?>
                                <div id="inner_contingent_beneficiary_field_<?= $number ?>"
                                     class="inner_contingent_beneficiary_field">
                                    <div class="clearfix m-b-25">
                                        <h5 class="mn pull-left">Contingent Beneficiary <span
                                                    data-display_number="<?= $number ?>" data-id="<?= $number ?>"
                                                    id="contingent_beneficiary_number_<?= $number ?>"
                                                    class="display_contingent_beneficiary_number"><?= $number ?></span>
                                            <?php if ($number > 1) { ?>
                                                <button type="button" class="red-link removeContingentBeneficiaryField"
                                                        data-id="<?= $number ?>" disabled>Remove
                                                </button>
                                            <?php } ?>
                                        </h5>
                                    </div>
                                    <div class="row enrollment_auto_row" style="display: none;">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input type="hidden" name="contingent_beneficiary_id[<?= $number ?>]"
                                                       id="contingent_beneficiary_id_<?= $number ?>" value="<?=$contingent_ben_row['id']?>">
                                                <select id="contingent_existing_dependent_<?= $number ?>"
                                                        name="contingent_existing_dependent[<?= $number ?>]"
                                                        class="selectpicker_input has-value contingent_beneficiary_select_<?= $number ?> contingent_beneficiary_select"
                                                        data-id="<?= $number ?>" data-select-val="" disabled>
                                                    <option value=""></option>
                                                    <?php if (!empty($dep_res)) { ?>
                                                        <?php foreach ($dep_res as $key => $row) { ?>
                                                            <?php if (!empty($row['fname'])) { ?>
                                                                <option value="<?= $row['id'] ?>"
                                                                    <?= $row['fname'] . ' ' . $row['lname'] == $contingent_ben_row['queBeneficiaryFullName'] ? "selected=selected" : "" ?>
                                                                        data-full-name="<?= $row['fname'] . ' ' . $row['lname'] ?>"
                                                                        data-type="<?= $row['type'] ?>"
                                                                        data-fname="<?= $row['fname'] ?>"
                                                                        data-lname="<?= $row['lname'] ?>"
                                                                        data-phone="<?= $row['phone'] ?>"
                                                                        data-email="<?= $row['email'] ?>"
                                                                        data-ssn="<?= $row['ssn'] ?>"
                                                                        data-address="<?= $row['address'] ?>"
                                                                        style="<?= !empty($contingent_existing_dependent) && in_array($row['id'], $contingent_existing_dependent) ? 'display: none' : '' ?>"><?= $row['fname'] . ' ' . $row['lname'] . ' (' . $row['type'] . ')' ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label>Select Existing Dependent</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row enrollment_auto_row">
                                        <?php foreach ($contingent_beneficiary_field as $key => $row) { ?>
                                            <?php
                                            $prd_question_id = $row['id'];
                                            $is_required = $row['required'];
                                            $control_name = 'contingent_' . $row['label'];
                                            $label = $row['display_label'];
                                            $control_type = $row['control_type'];
                                            $class = $row['control_class'];
                                            $maxlength = $row['control_maxlength'];
                                            $control_attribute = $row['control_attribute'];
                                            $questionType = $row['questionType'];
                                            if ($control_name == "contingent_queBeneficiaryAllow3") {
                                                continue;
                                            }

                                            $control_value = isset($contingent_ben_row[$row['label']]) ? $contingent_ben_row[$row['label']] : '';
                                            ?>
                                            <?php if ($control_type == 'text') { ?>
                                                <div class="col-lg-3 col-md-6">
                                                    <?php if ($control_name == "contingent_queBeneficiaryPercentage") { ?>
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="pr">
                                                                    <input type="text"
                                                                           id="<?= $control_name ?>_<?= $number ?>"
                                                                           maxlength="<?= $maxlength ?>"
                                                                           name="<?= $control_name ?>[<?= $number ?>]"
                                                                           value="<?= $control_value ?>"
                                                                           class="form-control has-value <?= $class ?>"
                                                                           required
                                                                           data-id="<?= $number ?>"
                                                                           onkeypress="return isNumber(event)" disabled>
                                                                    <label><?= $label ?><?php if ($is_required == 'Y') { ?>
                                                                            <span
                                                                                    class="req-indicator">*</span><?php } ?>
                                                                    </label>
                                                                </div>
                                                                <div class="input-group-addon"> %</div>
                                                            </div>
                                                            <p class="error"
                                                               id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="form-group">
                                                            <input type="text" id="<?= $control_name ?>_<?= $number ?>"
                                                                   maxlength="<?= $maxlength ?>"
                                                                   name="<?= $control_name ?>[<?= $number ?>]"
                                                                   value="<?= $control_value ?>"
                                                                   class="form-control has-value <?= ($control_name == "contingent_queBeneficiaryEmail") ? "no_space" : ""; ?> <?= $class ?>" required
                                                                   data-id="<?= $number ?>" disabled>
                                                            <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                                        class="req-indicator">*</span><?php } ?></label>
                                                            <p class="error"
                                                               id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            <?php } else if ($control_type == 'select') { ?>
                                                <div class="col-lg-3 col-md-6">
                                                    <div class="form-group">

                                                        <select id="<?= $control_name ?>_<?= $number ?>"
                                                                name="<?= $control_name ?>[<?= $number ?>]"
                                                                class="selectpicker_input has-value contingent_beneficiary_select_<?= $number ?>"
                                                                required
                                                                data-live-search="true" data-id="<?= $number ?>"
                                                                disabled>
                                                            <option value="" hidden></option>
                                                            <?php if ($control_name == 'contingent_queBeneficiaryRelationship') { ?>
                                                                <option value="Child" <?= ($control_value == "Child" ? 'selected=selected' : '') ?>>
                                                                    Child
                                                                </option>
                                                                <option value="Spouse" <?= ($control_value == "Spouse" ? 'selected=selected' : '') ?>>
                                                                    Spouse
                                                                </option>
                                                                <option value="Parent" <?= ($control_value == "Parent" ? 'selected=selected' : '') ?>>
                                                                    Parent
                                                                </option>
                                                                <option value="Grandparent" <?= ($control_value == "Grandparent" ? 'selected=selected' : '') ?>>
                                                                    Grandparent
                                                                </option>
                                                                <option value="Friend" <?= ($control_value == "Friend" ? 'selected=selected' : '') ?>>
                                                                    Friend
                                                                </option>
                                                                <option value="Other" <?= ($control_value == "Other" ? 'selected=selected' : '') ?>>
                                                                    Other
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                                    class="req-indicator">*</span><?php } ?></label>
                                                        <p class="error"
                                                           id="error_<?= $control_name ?>_<?= $number ?>"></p>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <hr>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-info m-t-25" id="addContingentBeneficiaryField"
                        data-allow-upto="3" disabled>+ Contingent Beneficiary
                </button>
                <p class="error" id="error_contingent_beneficiary_general"></p>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-action btn_save_health_details" style="display: none;">Save</button>
        </div>
    </div>
</form>
<div id="principal_beneficiary_fields" style="display: none;">
    <div id="inner_principal_beneficiary_field_~number~"
         class="inner_principal_beneficiary_field">
        <div class="clearfix m-b-25">
            <h5 class="mn pull-left">Principal Beneficiary <span
                        data-display_number="~display_number~" data-id="~number~"
                        id="principal_beneficiary_number_~number~"
                        class="display_principal_beneficiary_number">~display_number~</span>
                <button type="button" class="red-link removePrincipalBeneficiaryField"
                        data-id="~number~">Remove
                </button>
            </h5>
        </div>
        <div class="row enrollment_auto_row" style="display: none;">
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="hidden" name="principal_beneficiary_id[<?= $number ?>]"
                           id="principal_beneficiary_id_~number~" value="0">
                    <select id="principal_existing_dependent_~number~" name="principal_existing_dependent[~number~]"
                            class="principal_beneficiary_select_~number~ principal_beneficiary_select"
                            data-id="~number~" data-select-val="">
                        <option value=""></option>
                        <?php if (!empty($dep_res)) { ?>
                            <?php foreach ($dep_res as $key => $row) { ?>
                                <?php if (!empty($row['fname'])) { ?>
                                    <option value="<?= $row['id'] ?>"
                                            data-full-name="<?= $row['fname'] . ' ' . $row['lname'] ?>"
                                            data-type="<?= $row['type'] ?>" data-fname="<?= $row['fname'] ?>"
                                            data-lname="<?= $row['lname'] ?>" data-phone="<?= $row['phone'] ?>"
                                            data-email="<?= $row['email'] ?>" data-ssn="<?= $row['ssn'] ?>"
                                            data-address="<?= $row['address'] ?>"
                                            style="<?= !empty($principal_existing_dependent) && in_array($row['id'], $principal_existing_dependent) ? 'display:none' : '' ?>"><?= $row['fname'] . ' ' . $row['lname'] . ' (' . $row['type'] . ')' ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <label>Select Existing Dependent</label>
                </div>
            </div>
        </div>
        <div class="row enrollment_auto_row">
            <?php foreach ($principal_beneficiary_field as $key => $row) { ?>
                <?php
                $prd_question_id = $row['id'];
                $is_required = $row['required'];
                $control_name = 'principal_' . $row['label'];
                $label = $row['display_label'];
                $control_type = $row['control_type'];
                $class = $row['control_class'];
                $maxlength = $row['control_maxlength'];
                $control_attribute = $row['control_attribute'];
                $questionType = $row['questionType'];
                if ($control_name == "principal_queBeneficiaryAllow3") {
                    continue;
                }

                $control_value = '';
                ?>
                <?php if ($control_type == 'text') { ?>
                    <div class="col-lg-3 col-md-6">
                        <?php if ($control_name == "principal_queBeneficiaryPercentage") { ?>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="pr">
                                        <input type="text" id="<?= $control_name ?>_~number~"
                                               maxlength="<?= $maxlength ?>"
                                               name="<?= $control_name ?>[~number~]" value="<?= $control_value ?>"
                                               class="form-control <?= $class ?>" required
                                               data-id="~number~"
                                               onkeypress="return isNumber(event)">
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                    </div>
                                    <div class="input-group-addon"> %</div>
                                </div>
                                <p class="error" id="error_<?= $control_name ?>_~number~"></p>
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
                                <input type="text" id="<?= $control_name ?>_~number~"
                                       maxlength="<?= $maxlength ?>"
                                       name="<?= $control_name ?>[~number~]" value="<?= $control_value; ?>"
                                       class="form-control <?= ($control_name == "principal_queBeneficiaryEmail") ? "no_space" : "" ?> <?= $class ?>" required
                                       data-id="~number~">
                                <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                            class="req-indicator">*</span><?php } ?></label>
                                <p class="error" id="error_<?= $control_name ?>_~number~"></p>
                            </div>
                        <?php } ?>

                    </div>
                <?php } else if ($control_type == 'select') { ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <select id="<?= $control_name ?>_~number~"
                                    name="<?= $control_name ?>[~number~]"
                                    class="principal_beneficiary_select_~number~" required
                                    data-live-search="true" data-id="~number~">
                                <option value="" hidden></option>
                                <?php if ($control_name == 'principal_queBeneficiaryRelationship') { ?>
                                    <option value="Child" <?= ($control_value == "Child" ? 'selected=selected' : '') ?>>
                                        Child
                                    </option>
                                    <option value="Spouse" <?= ($control_value == "Spouse" ? 'selected=selected' : '') ?>>
                                        Spouse
                                    </option>
                                    <option value="Parent" <?= ($control_value == "Parent" ? 'selected=selected' : '') ?>>
                                        Parent
                                    </option>
                                    <option value="Grandparent" <?= ($control_value == "Grandparent" ? 'selected=selected' : '') ?>>
                                        Grandparent
                                    </option>
                                    <option value="Friend" <?= ($control_value == "Friend" ? 'selected=selected' : '') ?>>
                                        Friend
                                    </option>
                                    <option value="Other" <?= ($control_value == "Other" ? 'selected=selected' : '') ?>>
                                        Other
                                    </option>
                                <?php } ?>
                            </select>
                            <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                        class="req-indicator">*</span><?php } ?></label>
                            <p class="error" id="error_<?= $control_name ?>_~number~"></p>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <hr>
    </div>
</div>
<div id="contingent_beneficiary_fields" style="display: none;">
    <div id="inner_contingent_beneficiary_field_~number~"
         class="inner_contingent_beneficiary_field">
        <div class="clearfix m-b-25">
            <h5 class="mn pull-left">Contingent Beneficiary <span
                        data-display_number="~display_number~" data-id="~number~"
                        id="contingent_beneficiary_number_~number~"
                        class="display_contingent_beneficiary_number">~display_number~</span>
                <button type="button" class="red-link removeContingentBeneficiaryField"
                        data-id="~number~">Remove
                </button>
            </h5>
        </div>
        <div class="row enrollment_auto_row" style="display: none;">
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="hidden" name="contingent_beneficiary_id[~number~]"
                           id="contingent_beneficiary_id_~number~" value="0">
                    <select id="contingent_existing_dependent_~number~" name="contingent_existing_dependent[~number~]"
                            class="contingent_beneficiary_select_~number~ contingent_beneficiary_select"
                            data-id="~number~" data-select-val="">
                        <option value=""></option>
                        <?php if (!empty($dep_res)) { ?>
                            <?php foreach ($dep_res as $key => $row) { ?>
                                <?php if (!empty($row['fname'])) { ?>
                                    <option value="<?= $row['id'] ?>"
                                            data-full-name="<?= $row['fname'] . ' ' . $row['lname'] ?>"
                                            data-type="<?= $row['type'] ?>" data-fname="<?= $row['fname'] ?>"
                                            data-lname="<?= $row['lname'] ?>" data-phone="<?= $row['phone'] ?>"
                                            data-email="<?= $row['email'] ?>" data-ssn="<?= $row['ssn'] ?>"
                                            data-address="<?= $row['address'] ?>"
                                            style="<?= !empty($contingent_existing_dependent) && in_array($row['id'], $contingent_existing_dependent) ? 'display: none' : '' ?>"><?= $row['fname'] . ' ' . $row['lname'] . ' (' . $row['type'] . ')' ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <label>Select Existing Dependent</label>
                </div>
            </div>
        </div>
        <div class="row enrollment_auto_row">
            <?php foreach ($contingent_beneficiary_field as $key => $row) { ?>
                <?php
                $prd_question_id = $row['id'];
                $is_required = $row['required'];
                $control_name = 'contingent_' . $row['label'];
                $label = $row['display_label'];
                $control_type = $row['control_type'];
                $class = $row['control_class'];
                $maxlength = $row['control_maxlength'];
                $control_attribute = $row['control_attribute'];
                $questionType = $row['questionType'];
                if ($control_name == "contingent_queBeneficiaryAllow3") {
                    continue;
                }

                $control_value = '';
                ?>
                <?php if ($control_type == 'text') { ?>
                    <div class="col-lg-3 col-md-6">
                        <?php if ($control_name == "contingent_queBeneficiaryPercentage") { ?>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="pr">
                                        <input type="text" id="<?= $control_name ?>_~number~"
                                               maxlength="<?= $maxlength ?>"
                                               name="<?= $control_name ?>[~number~]" value=""
                                               class="form-control <?= $class ?>" required
                                               data-id="~number~"
                                               onkeypress="return isNumber(event)">
                                        <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                                    class="req-indicator">*</span><?php } ?></label>
                                    </div>
                                    <div class="input-group-addon"> %</div>
                                </div>
                                <p class="error" id="error_<?= $control_name ?>_~number~"></p>
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
                                <input type="text" id="<?= $control_name ?>_~number~"
                                       maxlength="<?= $maxlength ?>"
                                       name="<?= $control_name ?>[~number~]" value=""
                                       class="form-control <?= ($control_name == "contingent_queBeneficiaryEmail") ? "no_space" : ""; ?> <?= $class ?>" required
                                       data-id="~number~">
                                <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                            class="req-indicator">*</span><?php } ?></label>
                                <p class="error" id="error_<?= $control_name ?>_~number~"></p>
                            </div>
                        <?php } ?>

                    </div>
                <?php } else if ($control_type == 'select') { ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <select id="<?= $control_name ?>_~number~"
                                    name="<?= $control_name ?>[~number~]"
                                    class="contingent_beneficiary_select_~number~" required
                                    data-live-search="true" data-id="~number~">
                                <option value="" hidden></option>
                                <?php if ($control_name == 'contingent_queBeneficiaryRelationship') { ?>
                                    <option value="Child">Child</option>
                                    <option value="Spouse">Spouse</option>
                                    <option value="Parent">Parent</option>
                                    <option value="Grandparent">Grandparent</option>
                                    <option value="Friend">Friend</option>
                                    <option value="Other">Other</option>
                                <?php } ?>
                            </select>
                            <label><?= $label ?><?php if ($is_required == 'Y') { ?><span
                                        class="req-indicator">*</span><?php } ?></label>
                            <p class="error" id="error_<?= $control_name ?>_~number~"></p>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <hr>
    </div>
</div>
<script type="text/javascript">
    var se_multiple_select = $(".se_multiple_select");
    var que_spouse_assign_products = $("#que_spouse_assign_products");
    var que_child_assign_products = $("#que_child_assign_products");
    var principal_ben_cnt = <?=count($principal_ben_res);?>;
    var contingent_ben_cnt = <?=count($contingent_ben_res);?>;
    isNumber = function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    $(document).ready(function () {
      checkEmail();
        $(document).off('click', "#btn_edit_health_details");
        $(document).on('click', "#btn_edit_health_details", function () {
            $(".thumbnail").attr('style','background-color: #ffffff;');
            $("#ajax_loader").show();
            $(this).hide();
            $(".btn_save_health_details").show();
            $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").removeAttr('readonly');
            $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").removeAttr('disabled');
            $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").prop('readonly', false);
            $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").prop('disabled', false);
            $(".primary_smoking_edit").removeClass('btn-group-disabled');
            $(".spouse_smoking_edit").removeClass('btn-group-disabled');
            $(".child_smoking_edit").removeClass('btn-group-disabled');
            $(".question_ans_edit").removeClass('btn-group-disabled');
            se_multiple_select.multipleSelect('enable');
            que_spouse_assign_products.multipleSelect('enable');
            que_child_assign_products.multipleSelect('enable');
            $(".selectpicker_input").selectpicker('refresh');
            setTimeout(function(){
                $("#ajax_loader").hide();
            },1000);
        });

        $(document).off('click', ".btn_save_health_details");
        $(document).on('click', ".btn_save_health_details", function (e) {
            e.preventDefault();
            $(".btn_save_health_details").prop('disabled',true);
            $.ajax({
                url: "member_health_tab.php",
                type: 'POST',
                data: $("#health_details_form").serialize(),
                dataType: 'json',
                beforeSend: function (e) {
                    $("#ajax_loader").show();
                },
                success: function (res) {
                    $(".btn_save_health_details").prop('disabled',false);
                    
                    $(".error").html("");
                    if (res.status == 'success') {
                        $(".thumbnail").attr('style','');
                        $("#btn_edit_health_details").show();
                        $(".btn_save_health_details").hide();

                        $(".primary_smoking_edit").addClass('btn-group-disabled');
                        $(".spouse_smoking_edit").addClass('btn-group-disabled');
                        $(".child_smoking_edit").addClass('btn-group-disabled');
                        $(".question_ans_edit").addClass('btn-group-disabled');
                        $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").attr('readonly','readonly');
                        $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").attr('disabled','disabled');
                        $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").prop('readonly', true);
                        $("#health_details_section :input, #health_details_section :button, #health_details_section select, #health_details_section label").prop('disabled', true);
                        se_multiple_select.multipleSelect('disable');
                        que_spouse_assign_products.multipleSelect('disable');
                        que_child_assign_products.multipleSelect('disable');
                        $(".selectpicker_input").selectpicker('refresh');

                        $("#ajax_loader").hide();
                        setNotifySuccess("Health Details saved successfully.");
                    } else {
                        $("#ajax_loader").hide();
                        var is_error = true;
                        $.each(res.errors, function (index, error) {
                            if ($('#error_' + index).length > 0) {
                                $('#error_' + index).html(error).show();
                                if (is_error) {
                                    var offset = $('#error_' + index).offset();
                                    var offsetTop = offset.top;
                                    var totalScroll = offsetTop - 50;
                                    $('body,html').animate({scrollTop: totalScroll}, 1200);
                                    is_error = false;
                                }
                            }

                        });
                    }
                }
            });
        });

        $(document).on('click',"input:checkbox",function(){
            if($(this).is(":checked")) {
                var group = "input:checkbox[name='"+$(this).attr("name")+"']";
                $(group).prop("checked",false);
                $(this).prop("checked",true);    
            }            
        });

        $(document).off('click',".btn_custom_radio:not([disabled])");
        $(document).on('click',".btn_custom_radio:not([disabled])",function(){
            var element = $(this).find('input:checkbox');
            $("input:checkbox[name='"+element.attr("name")+"']:not([value='"+element.val()+"'])").prop("checked",false).closest('label').removeClass('active');
        });

        $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
        $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
        $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});

        $(".Salary_mask").priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
        });

        se_multiple_select.multipleSelect({
            width: '100%'
        });

        $(".selectpicker_input").addClass('form-control');
        $(".selectpicker_input").selectpicker({
            container: 'body',
            style: 'btn-select',
            noneSelectedText: '',
            dropupAuto: false,
        });

        /*---- Spouse Fields ----*/
        que_spouse_assign_products.multipleSelect({
            width: '100%',
            selectAll: false,
            onClick: function (e) {
                $value = e.value;
                $productPlan = $("#product_plan_" + $value).val();
                if ($productPlan == 5) {

                    $("select.child_dependent_multiple_select").each(function () {
                        $childID = $(this).attr('data-id');
                        if (e.checked) {
                            $("#que_child_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', true);
                        } else {
                            $("#que_child_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', false);
                        }
                        $("#que_child_assign_products_" + $childID).multipleSelect('refresh');
                    });

                    $("select.spouse_dependent_multiple_select").each(function () {
                        $childID = $(this).attr('data-id');
                        if (res.number != $childID) {
                            if (e.checked) {
                                $("#que_spouse_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', true);
                            } else {
                                $("#que_spouse_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', false);
                            }
                            $("#que_spouse_assign_products_" + $childID).multipleSelect('refresh');
                        }
                    });

                }

            }
        });

        /*---- Child Fields ----*/
        que_child_assign_products.multipleSelect({
            width: '100%',
            selectAll: false,
            onClick: function (e) {
                $value = e.value;
                $productPlan = $("#product_plan_" + $value).val();
                if ($productPlan == 5) {

                    $("select.child_dependent_multiple_select").each(function () {
                        $childID = $(this).attr('data-id');
                        if (res.number != $childID) {
                            if (e.checked) {
                                $("#que_child_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', true);
                            } else {
                                $("#que_child_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', false);
                            }
                            $("#que_child_assign_products_" + $childID).multipleSelect('refresh');
                        }
                    });

                    $("select.spouse_dependent_multiple_select").each(function () {
                        $childID = $(this).attr('data-id');
                        if (e.checked) {
                            $("#que_spouse_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', true);
                        } else {
                            $("#que_spouse_assign_products_" + $childID + " [value='" + $value + "']").prop('disabled', false);
                        }
                        $("#que_spouse_assign_products_" + $childID).multipleSelect('refresh');
                    });
                }
            }
        });

        /*---- Beneficiary Field ----*/
        $(document).off("click", "#addPrincipalBeneficiaryField");
        $(document).on("click", "#addPrincipalBeneficiaryField", function (e) {
            $allow_upto = $(this).attr('data-allow-upto');
            $count = $("#beneficiary_section .inner_principal_beneficiary_field").length;
            $display_number = $count + 1;
            if ($allow_upto == '' || $display_number <= $allow_upto) {
                principal_ben_cnt++;

                var tmp_html = $("#principal_beneficiary_fields").html();
                tmp_html = tmp_html.replace(/~number~/g, principal_ben_cnt);
                tmp_html = tmp_html.replace(/~display_number~/g, $display_number);
                $("#principal_beneficiary_field_div").append(tmp_html);

                $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
                $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
                $(".principal_beneficiary_select_" + principal_ben_cnt).addClass('form-control');
                $(".principal_beneficiary_select_" + principal_ben_cnt).selectpicker({
                    container: 'body',
                    style: 'btn-select',
                    noneSelectedText: '',
                    dropupAuto: false,
                });
            }
            checkEmail();
        });

        $(document).off("click", ".removePrincipalBeneficiaryField");
        $(document).on("click", ".removePrincipalBeneficiaryField", function () {
            $number = $(this).attr('data-id');

            var principal_beneficiary_id = $("#principal_beneficiary_id_" + $number).val();

            $removed_display_number = parseInt($("#principal_beneficiary_number_" + $number).attr('data-display_number'));
            $("#inner_principal_beneficiary_field_" + $number).remove();

            $('#beneficiary_section .display_principal_beneficiary_number').each(function () {
                $display_number = parseInt($(this).attr('data-display_number'));

                if ($display_number > $removed_display_number) {
                    $display_number = $display_number - 1;
                    $(this).attr('data-display_number', $display_number);
                    $(this).html($display_number);
                }
            });
        });

        $(document).off("click", "#addContingentBeneficiaryField");
        $(document).on("click", "#addContingentBeneficiaryField", function (e) {
            $allow_upto = $(this).attr('data-allow-upto');
            $count = $("#beneficiary_section .inner_contingent_beneficiary_field").length;
            $display_number = $count + 1;
            if ($allow_upto == '' || $display_number <= $allow_upto) {
                contingent_ben_cnt++;

                var tmp_html = $("#contingent_beneficiary_fields").html();
                tmp_html = tmp_html.replace(/~number~/g, principal_ben_cnt);
                tmp_html = tmp_html.replace(/~display_number~/g, $display_number);
                $("#contingent_beneficiary_field_div").append(tmp_html);

                $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
                $(".dob").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
                $(".SSN_mask").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
                $(".contingent_beneficiary_select_" + principal_ben_cnt).addClass('form-control');
                $(".contingent_beneficiary_select_" + principal_ben_cnt).selectpicker({
                    container: 'body',
                    style: 'btn-select',
                    noneSelectedText: '',
                    dropupAuto: false,
                });
            }
            checkEmail();
        });

        $(document).off("click", ".removeContingentBeneficiaryField");
        $(document).on("click", ".removeContingentBeneficiaryField", function () {
            $number = $(this).attr('data-id');

            var contingent_beneficiary_id = $("#contingent_beneficiary_id_" + $number).val();

            $removed_display_number = parseInt($("#contingent_beneficiary_number_" + $number).attr('data-display_number'));
            $("#inner_contingent_beneficiary_field_" + $number).remove();

            $('#beneficiary_section .display_contingent_beneficiary_number').each(function () {
                $display_number = parseInt($(this).attr('data-display_number'));

                if ($display_number > $removed_display_number) {
                    $display_number = $display_number - 1;
                    $(this).attr('data-display_number', $display_number);
                    $(this).html($display_number);
                }

            });
        });

        $(document).off("change", ".principal_beneficiary_select");
        $(document).on("change", ".principal_beneficiary_select", function (e) {
            e.stopPropagation();
            $number = $(this).attr('data-id');

            $depFname = $(this).find('option:selected').attr('data-fname');
            $depLname = $(this).find('option:selected').attr('data-lname');
            $depPhone = $(this).find('option:selected').attr('data-phone');
            $depEmail = $(this).find('option:selected').attr('data-email');
            $depSSN = $(this).find('option:selected').attr('data-ssn');
            $depType = $(this).find('option:selected').attr('data-type');
            $depAddress = $(this).find('option:selected').attr('data-address');
            $depFullName = $(this).find('option:selected').attr('data-full-name');
            $value = $(this).val();

            $("#principal_queBeneficiaryFullName_" + $number).val($depFullName);
            $("#principal_queBeneficiaryPhone_" + $number).val($depPhone);
            $("#principal_queBeneficiaryEmail_" + $number).val($depEmail);
            $("#principal_queBeneficiarySSN_" + $number).val($depSSN);
            $("#principal_queBeneficiaryAddress_" + $number).val($depAddress);
            $("#principal_queBeneficiaryRelationship_" + $number).val($depType);
            $("#principal_queBeneficiaryRelationship_" + $number).selectpicker('refresh');

            $selected = $(this).attr('data-select-val');

            if ($selected != '') {
                $(".principal_beneficiary_select option[value='" + $selected + "']").show();
                $(this).attr('data-select-val', '');
            }

            if ($value != '') {
                $(this).attr('data-select-val', $value);
                $(".principal_beneficiary_select option[value='" + $value + "']").hide();
                $("#principal_existing_dependent_" + $number + " option[value='" + $value + "']").show();
            }
            $(".principal_beneficiary_select:visible").selectpicker('refresh');
            fRefresh();
        });

        $(document).off("change", ".contingent_beneficiary_select");
        $(document).on("change", ".contingent_beneficiary_select", function (e) {
            e.stopPropagation();
            $number = $(this).attr('data-id');

            $depFname = $(this).find('option:selected').attr('data-fname');
            $depLname = $(this).find('option:selected').attr('data-lname');
            $depPhone = $(this).find('option:selected').attr('data-phone');
            $depEmail = $(this).find('option:selected').attr('data-email');
            $depSSN = $(this).find('option:selected').attr('data-ssn');
            $depType = $(this).find('option:selected').attr('data-type');
            $depAddress = $(this).find('option:selected').attr('data-address');
            $depFullName = $(this).find('option:selected').attr('data-full-name');
            $value = $(this).val();

            $("#contingent_queBeneficiaryFullName_" + $number).val($depFullName);
            $("#contingent_queBeneficiaryPhone_" + $number).val($depPhone);
            $("#contingent_queBeneficiaryEmail_" + $number).val($depEmail);
            $("#contingent_queBeneficiarySSN_" + $number).val($depSSN);
            $("#contingent_queBeneficiaryAddress_" + $number).val($depAddress);
            $("#contingent_queBeneficiaryRelationship_" + $number).val($depType);
            $("#contingent_queBeneficiaryRelationship_" + $number).selectpicker('refresh');

            $selected = $(this).attr('data-select-val');
            if ($selected != '') {
                $(".contingent_beneficiary_select option[value=" + $selected + "]").show();
                $(this).attr('data-select-val', '');
            }

            if ($value != '') {
                $(this).attr('data-select-val', $value);
                $(".contingent_beneficiary_select option[value=" + $value + "]").hide();
                $("#contingent_existing_dependent_" + $number + " option[value=" + $value + "]").show();
            }
            $(".contingent_beneficiary_select:visible").selectpicker('refresh');
            fRefresh();

        });
    });
</script>
<?php }else{ ?>

    <div id="request_access_div">
    <input type="hidden" name="id" id="id" value="<?=$_POST['id']?>">
    <div class="clearfix ">
        <p class="agp_md_title pull-left">Health Details</p>
        <!-- <div class="pull-right">
        <a href="javascript:void(0);"><i class="fa fa-edit fa-lg"></i></a>
        </div> -->
    </div>
    <div class="thumbnail theme-form">
        <!-- <div class="row">
        <div class="col-sm-4 text-center col-sm-offset-4">
        <i class="fa fa-lock fa-lg" aria-hidden="true"></i><br>
        <p class="mn">To view health details of this member, a unique 6-digit security code is required from the member. You may request this by clicking the button below.</p>
        </div>
        </div> -->
        <div class="text-center">
            <a id="request_access_popup"  class="btn btn-action">Access Details</a>
        </div>
    </div>
</div>
<div id="health_detail_div"></div>
<script type="text/javascript">
    $(document).ready(function(e){
        $("#health_detail_div").hide();
    });
    $(document).off('click','#request_access_popup');
    $(document).on('click','#request_access_popup',function(e){
        $.ajax({
            url:'member_health_tab.php',
            data : {is_ajaxed:1,id:$("#id").val()},
            type : 'post',
            dataType : 'html',
            beforeSend :function(e){
                $("#ajax_loader").show();
            },
            success :function(res){
                $("#ajax_loader").hide();
                $("#request_access_div").hide();
                $("#health_detail_div").html(res).show();
            }
        });
    });
</script>
<?php } ?>