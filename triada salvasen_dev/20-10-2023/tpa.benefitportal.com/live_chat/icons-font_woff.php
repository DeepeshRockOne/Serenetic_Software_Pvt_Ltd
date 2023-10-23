<?php 
require('config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/font-woff");
echo @file_get_contents( SB_URL . "/media/icons/support-board.woff");
?>