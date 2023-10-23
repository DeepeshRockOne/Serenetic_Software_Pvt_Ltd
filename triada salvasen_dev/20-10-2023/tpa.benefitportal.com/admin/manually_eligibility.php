<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_id = $_GET['id'];


$products = '';
$memberincr = '';
$agentincr = '';



if(!empty($file_id)){
    $sql_file = "SELECT * FROM eligibility_files WHERE id=:file_id";
    $resFile = $pdo->selectOne($sql_file,array(":file_id" => $file_id));
    $FTP = $resFile['ftp_name'];
    $products = $resFile['products'];
    $file_name = $resFile['file_name'];
    $file_key = $resFile['file_key'];
    $sql_processedFile = "SELECT * FROM eligibility_requests WHERE is_deleted='N' AND status='Processed' AND file_id=:file_id LIMIT 3";
    $res_processedFile = $pdo->select($sql_processedFile,array(":file_id" => $file_id));
    
}

// if (isset($products) && !empty($products)) {
//     $memberincr .= " AND (w.product_id IN (" .$products. "))";
//     $agentincr .= " AND (ap.product_id IN (" .$products. "))";
// }

// // specific member only

// // member wise
// $resMember = $Rpdo->select("SELECT c.*
//         FROM customer c
//         JOIN customer as s on(s.id= c.sponsor_id)
//         JOIN website_subscriptions w ON(w.customer_id = c.id)
//         WHERE c.id>0 AND c.type='Customer' AND c.is_deleted = 'N' AND c.status IN ('Active','Terminated','Inactive Member Request','Inactive Failed Billing','On Hold Failed Billing') $memberincr
//          GROUP BY c.id");

// // agent wise
// $resAgent = $Rpdo->select("SELECT c.*
//         FROM customer c
//         JOIN customer as s on(s.id= c.sponsor_id)
//         JOIN agent_product_rule ap ON(ap.agent_id=c.id)
//         WHERE c.id>0 AND c.type='Agent' AND (c.sub_type='' OR c.sub_type is null) AND c.is_deleted = 'N' $agentincr
//          GROUP BY c.id");








$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "manually_eligibility.inc.php";
include_once 'layout/iframe.layout.php';
?>