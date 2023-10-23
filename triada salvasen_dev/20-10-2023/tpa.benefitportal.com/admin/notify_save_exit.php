<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$product_id= $_GET['id'];

// $exStylesheets = array('thirdparty/summernote-master/dist/summernote.css');
$exJs = array('thirdparty/ckeditor/ckeditor.js');


$template = 'notify_save_exit.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
