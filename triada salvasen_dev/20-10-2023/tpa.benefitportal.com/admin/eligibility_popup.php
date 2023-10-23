<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "eligibility_popup.inc.php";
include_once 'layout/iframe.layout.php';
?>