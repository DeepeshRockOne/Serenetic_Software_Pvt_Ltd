<?php 
require('config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: image/svg+xml");
echo @file_get_contents( SB_URL . "/media/icons/support-board.svg#support-board");
?>