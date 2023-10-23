<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_id = $_GET['id'];


$products = '';
$memberincr = '';
$agentincr = '';
$total_records = "";


if(!empty($file_id)){
    $sel_sql = "SELECT ff.*
		FROM fulfillment_files ff
		where ff.is_deleted = 'N' AND ff.id = :file_id GROUP by ff.id order by ff.file_name";

    $resFile = $pdo->selectOne($sel_sql,array(":file_id" => $file_id));

    $total_records = $pdo->selectOne("SELECT count(DISTINCT ce.id) as total_records 
                  FROM fulfillment_files ff
                  JOIN website_subscriptions w on(FIND_IN_SET(w.product_id,ff.products) AND w.status not in('Pending Declined','Pending Payment'))
                  JOIN customer_enrollment ce on(ce.website_id = w.id AND ce.is_fulfillment = 'N')
                  JOIN customer c on(c.id = w.customer_id AND c.status NOT IN('Pendind Validation','Customer Abandon','Pending'))
                  where ff.id = :id AND ff.is_deleted = 'N' GROUP by ff.id order by ff.file_name",array(':id' => $file_id));


    $FTP = $resFile['ftp_name'];
    $products = $resFile['products'];
    $file_name = $resFile['file_name'];
    $total_records = isset($total_records['total_records']) ? $total_records['total_records'] : 0;
}



$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "manually_fulfillment.inc.php";
include_once 'layout/iframe.layout.php';
?>