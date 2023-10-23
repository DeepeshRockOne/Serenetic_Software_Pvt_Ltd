<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$exStylesheets = array( 'thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js');

$template = "groups_interaction_add.inc.php";
include_once 'layout/iframe.layout.php';
?>