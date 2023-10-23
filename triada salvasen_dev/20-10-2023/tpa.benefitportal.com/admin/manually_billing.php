<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_id = isset($_GET['id']) ? $_GET['id'] : "";

$memberincr = '';
$agentincr = '';



if(!empty($file_id)){
    $sql_file = "SELECT * FROM billing_files WHERE id=:file_id";
    $resFile = $pdo->selectOne($sql_file,array(":file_id" => $file_id));
    $FTP = $resFile['ftp_name'];
    $file_name = $resFile['file_name'];
    $sql_processedFile = "SELECT * FROM billing_requests WHERE is_deleted='N' AND status='Processed' AND file_id=:file_id LIMIT 3";
    $res_processedFile = $pdo->select($sql_processedFile,array(":file_id" => $file_id));
    
}


$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "manually_billing.inc.php";
include_once 'layout/iframe.layout.php';
?>