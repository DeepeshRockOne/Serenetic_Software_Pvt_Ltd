<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Reporting";
$breadcrumbes[1]['link'] = 'system_resources.php';

$setting_keys = array(
					'default_email_from',
					'default_from_name',
					'sms_twilio_number',
					'agent_services_cell_phone',
					'agent_services_email',
					'member_services_cell_phone',
					'member_services_email',
					'group_services_cell_phone',
					'group_services_email',
					'immediate_destination',
					'immediate_destination_name',
					'immediate_origin',
					'immediate_origin_name',
					'company_entry_description',
					'originating_dfi_id',
					'enrollment_display_name',
				);
$app_setting_res = get_app_settings($setting_keys);
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array(
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js',
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
	'thirdparty/ajax_form/jquery.form.js'.$cache,
);
$template = 'system_resources.inc.php';
include_once 'layout/end.inc.php';
?>