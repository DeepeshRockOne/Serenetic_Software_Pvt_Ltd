<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "edit_etickets_grops.inc.php";
include_once 'layout/iframe.layout.php';
?>