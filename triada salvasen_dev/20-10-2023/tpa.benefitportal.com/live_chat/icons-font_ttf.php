<?php 
require('config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/x-font-ttf");
echo @file_get_contents( SB_URL . "/media/icons/support-board.ttf");
?>