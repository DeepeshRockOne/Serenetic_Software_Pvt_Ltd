<?php
// include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once (__DIR__) . '/includes/connect.php';


$res_t =$pdo->selectOne('SELECT id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Admin',":status"=>'Active')); 

$exStylesheets = array('thirdparty/colorbox/colorbox.css');

$exJs = array('thirdparty/colorbox/jquery.colorbox.js');

$template = 'admin_terms.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
