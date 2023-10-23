<?php
include_once __DIR__ . '/includes/connect.php'; 

$exStylesheets = array('thirdparty/summernote-master/dist/summernote.css');
$exJs = array('thirdparty/summernote-master/dist/popper.js', 'thirdparty/summernote-master/dist/summernote.js');


$template = 'email_share_website.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>