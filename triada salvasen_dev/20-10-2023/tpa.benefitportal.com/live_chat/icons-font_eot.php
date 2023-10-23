<?php 
require('config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/vnd.ms-fontobject");
echo @file_get_contents( SB_URL . "/media/icons/support-board.eot");
?>