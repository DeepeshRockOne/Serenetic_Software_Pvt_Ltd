<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Groups';
$breadcrumbes[1]['link'] = 'javascript:void(0);';
$breadcrumbes[2]['title'] = 'Leads';
$breadcrumbes[2]['link'] = 'lead_listing.php';
$breadcrumbes[3]['title'] = '+ Lead(s)';
$breadcrumbes[3]['link'] = 'javascript:void(0);';

$lead_tag_res = get_lead_tags();
$agent_res = get_active_agents_for_select();
$group_res = get_active_groups_for_select();

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'.$cache
);
$exJs = array(
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
	'thirdparty/ajax_form/jquery.form.js'.$cache,
	'thirdparty/vue-js/vue.min.js'
);
$template = 'add_leads.inc.php';
include_once 'layout/end.inc.php';
?>