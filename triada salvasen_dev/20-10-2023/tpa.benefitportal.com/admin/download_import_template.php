<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_name = $_GET['file_name'];
$fullPath = $CSV_DIR . $_GET['file_name'];
$realPath = $CSV_DIR . $_GET['file_name'];

if(!file_exists($realPath)){
  redirect('404.php');
}

$fsize = filesize($fullPath);
$path_parts = pathinfo($fullPath);
$ext = strtolower($path_parts["extension"]);


// Determine Content Type
switch ($ext) {
  case "pdf": $ctype = "application/pdf";
    break;
  case "exe": $ctype = "application/octet-stream";
    break;
  case "zip": $ctype = "application/zip";
    break;
  case "doc": $ctype = "application/msword";
    break;
  case "xls": $ctype = "application/vnd.ms-excel";
    break;
  case "ppt": $ctype = "application/vnd.ms-powerpoint";
    break;
  case "gif": $ctype = "image/gif";
    break;
  case "png": $ctype = "image/png";
    break;
  case "jpeg":
  case "jpg": $ctype = "image/jpg";
    break;
  default: $ctype = "application/force-download";
}

header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false); // required for certain browsers
header("Content-Type: $ctype");
header("Content-Disposition: attachment; filename=\"" . $file_name . "\";");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . $fsize);
readfile($fullPath);
?>

