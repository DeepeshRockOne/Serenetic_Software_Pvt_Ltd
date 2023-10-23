<?php 
include_once __DIR__ . '/includes/connect.php';
echo "<pre>";
print_r($_SESSION);
echo date("Y-m-d H:i:s");
echo "</pre>";

pre_print(date_default_timezone_get());
