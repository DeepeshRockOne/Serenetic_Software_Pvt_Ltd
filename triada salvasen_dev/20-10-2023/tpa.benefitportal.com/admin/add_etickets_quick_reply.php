<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$quickId = checkIsset($_GET['id']);
$categoryRec = array();
$view = checkIsset($_GET['type']);
$panelHeading = '+ Quick Reply';
if(!empty($quickId)){
    $categoryRec = $pdo->selectOne("SELECT id,title,description FROM s_ticket_quick_reply where is_deleted='N' and md5(id)=:id",array(":id"=>$quickId));
    $panelHeading = !empty($view) ? 'View ' : 'Edit '; //'Quick Reply - <span class="fw300">'.$categoryRec['title'].'</span>';
    $panelHeading .= 'Quick Reply - <span class="fw300">'.$categoryRec['title'].'</span>';
}

// $exStylesheets = array('thirdparty/summernote-master/dist/summernote.css'.$cache);
$exJs = array('thirdparty/ckeditor/ckeditor.js');

$template = "add_etickets_quick_reply.inc.php";
include_once 'layout/iframe.layout.php';
?>