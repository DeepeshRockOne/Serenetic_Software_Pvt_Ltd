<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/files.class.php';
$FilesClass = new FilesClass();
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);

if(isset($_GET['run_query']) && $_GET['run_query'] == 'yes') {
    $sql = "SELECT id,products FROM billing_files WHERE 1";
    $res = $pdo->select($sql);
    foreach ($res as $row) {
        $products = explode(',',$row['products']);
        $FilesClass->updateBillingFilePrd($row['id'],$products);
    }
}
echo "Completed";