<?php
include_once __DIR__ . '/includes/connect.php'; 

$SITE_FAVICON_TEXT = "View Registred Records";
$title = "View Registred Records";

//view register records
$data = array();

$query = "SELECT * FROM registration";

$result = $pdo->select($query);

if (count($result) > 0) {
    $data = $result;
}

$template = 'view_reg_records.inc.php';
$layout = 'main.layout.php';

include_once 'layout/end.inc.php';
