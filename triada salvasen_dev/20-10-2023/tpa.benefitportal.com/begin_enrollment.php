<?php
include_once __DIR__ . '/includes/connect.php';

$exStylesheets = array('groups/css/prd_preview.css'.$cache);
$username = $_GET['username'];
$sponsorId = $_GET['sponsorId'];
$template = 'begin_enrollment.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>