<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Enrollees';
$breadcrumbes[1]['link'] = 'group_enrollees.php';
$breadcrumbes[2]['title'] = '+ Enrollee';

group_has_access(4);

$group_id = $_SESSION['groups']['id'];

$lead_tag_res = get_lead_tags($group_id);


$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$exJs = array('thirdparty/ajax_form/jquery.form.js'.$cache);
$template = 'group_add_csv_enrollee.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>