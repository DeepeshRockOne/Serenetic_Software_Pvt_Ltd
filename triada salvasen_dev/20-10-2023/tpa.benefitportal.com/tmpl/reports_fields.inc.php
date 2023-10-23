<?php
$field_value = (isset($filter_options[$field_data['name']])?$filter_options[$field_data['name']]:'');
?>
<?php if($field_data['field_key'] == "added_or_effective_date") { ?>
    <div class="col-xs-6 m-b-20">
        <div class="input-question">
            <div class="radio-inline">
                <label class="mn">
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="added_date" <?=$field_value == "added_date"?"checked":""?>> Added Date
                </label>
            </div>
            <div class="radio-inline">
                <label class="mn"> 
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="effective_date" <?=$field_value == "effective_date"?"checked":""?>> Effective Date
                </label>
            </div>    
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } elseif($field_data['field_key'] == "added_or_post_payment_date") { ?>
    <div class="col-xs-6 m-b-20">
        <div class="input-question">
            <div class="radio-inline">
                <label>
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="added_date" <?=$field_value == "added_date"?"checked":""?>> Added Date
                </label>
            </div>
            <div class="radio-inline">
                <label class="mn">
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="post_payment_date" <?=$field_value == "post_payment_date"?"checked":""?>> Post-Payment Date
                </label>
            </div>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } elseif($field_data['field_key'] == "transaction_or_effective_date") { ?>
    <div class="col-xs-6 m-b-20">
        <div class="input-question">
            <div class="radio-inline">
                <label>
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="transaction_date" <?=$field_value == "transaction_date"?"checked":""?>> Transaction Date
                </label>
            </div>
            <div class="radio-inline">
                <label class="mn">
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="effective_date" <?=$field_value == "effective_date"?"checked":""?>> Effective Date
                </label>
            </div>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } elseif($field_data['field_key'] == "termination_or_terminated_date") { ?>
    <div class="col-xs-6 m-b-20">
        <div class="input-question">
            <div class="radio-inline">
                <label>
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="termination_date" <?=$field_value == "termination_date"?"checked":""?>> Termination Date
                </label>
            </div>
            <div class="input-question">
                <label class="mn">
                    <input type="radio" name="filter_<?=$field_data['name']?>" value="date_terminated" <?=$field_value == "date_terminated"?"checked":""?>> Terminated Date
                </label>
            </div>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php }  elseif($field_data['field_key'] == "added_date") { ?>
    <div class="col-xs-6">
        <div class="form-group ">
            <select class="filter_fields form-control" name="filter_<?=$field_data['name']?>" id="filter_<?=$field_data['name']?>">
                <option data-hidden="true"></option>
                <option value="current_day" <?=$field_value == "current_day"?"selected":""?>>Current Day</option>
                <option value="current_Week" <?=$field_value == "current_Week"?"selected":""?>>Current Week</option>
                <option value="current_month" <?=$field_value == "current_month"?"selected":""?>>Current month</option>
                <option value="prior_day" <?=$field_value == "prior_day"?"selected":""?>>Prior Day</option>
                <option value="prior_week" <?=$field_value == "prior_week"?"selected":""?>>Prior Week</option>
                <option value="prior_month" <?=$field_value == "prior_month"?"selected":""?>>Prior Month</option>
                <option value="as_of_today" <?=$field_value == "as_of_today"?"selected":""?>>As of Today</option>
            </select>
            <label><?=$field_data['label']?></label>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } elseif($field_data['field_key'] == "as_of_date") { ?>
    <div class="col-xs-6">
        <div class="form-group ">
            <select class="filter_fields form-control" name="filter_<?=$field_data['name']?>" id="filter_<?=$field_data['name']?>">
                <option value="as_of_today" selected>As of Today</option>
            </select>
            <label><?=$field_data['label']?></label>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } elseif($field_data['field_key'] == "as_of_date_prior") { ?>
    <div class="col-xs-6">
        <div class="form-group ">
            <select class="filter_fields form-control" name="filter_<?=$field_data['name']?>" id="filter_<?=$field_data['name']?>">
                <option data-hidden="true"></option>
                <option value="prior_day" <?=$field_value == "prior_day"?"selected":""?>>Prior Day</option>
                <option value="prior_week" <?=$field_value == "prior_week"?"selected":""?>>Prior Week</option>
                <option value="prior_month" <?=$field_value == "prior_month"?"selected":""?>>Prior Month</option>
                <option value="as_of_today" <?=$field_value == "as_of_today"?"selected":""?>>As of Today</option>
            </select>
            <label><?=$field_data['label']?></label>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } elseif($field_data['field_key'] == "as_of_date_prior_month") { ?>
    <div class="col-xs-6">
        <div class="form-group ">
            <select class="filter_fields form-control" name="filter_<?=$field_data['name']?>" id="filter_<?=$field_data['name']?>">
                <option value="prior_month">Prior Month</option>
            </select>
            <label><?=$field_data['label']?></label>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } elseif($field_data['field_key'] == "report_type") { ?>
    <div class="col-xs-6">
        <div class="form-group">
            <select class="filter_fields form-control" name="filter_<?=$field_data['name']?>" id="filter_<?=$field_data['name']?>">
                <option value="" hidden="true" selected></option>
                <option value="masked" selected="selected">Masked</option>
                <option value="unmasked">Unmasked</option>
            </select>
            <label><?=$field_data['label']?></label>
            <p class="error"><span id="error_filter_<?=$field_data['name']?>"></span></p>
        </div>
    </div>
<?php } elseif($field_data['field_key'] == "user_type") {
        $user_type = '';
        if($report_row['report_key'] == 'agent_interactions'){
            $user_type = 'agent' ;
        }else if($report_row['report_key'] == 'member_interactions'){
            $user_type = 'customer' ;
        }
    ?>
        <input type="hidden" name="filter_<?=$field_data['field_key']?>" value="<?=$user_type?>">
<?php } elseif($field_data['field_key'] == "sales_report_type") {
        $report_type = '';
        if($report_row['report_key'] == 'payment_nb_sales'){
            $report_type = 'new_business' ;
        }else if($report_row['report_key'] == 'payment_rb_sales'){
            $report_type = 'renewal' ;
        }
    ?>
        <input type="hidden" name="filter_<?=$field_data['name']?>" value="<?=$report_type?>">
<?php } ?>