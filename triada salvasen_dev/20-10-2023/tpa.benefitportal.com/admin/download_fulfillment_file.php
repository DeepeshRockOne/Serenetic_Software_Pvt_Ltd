<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$history_id = $_GET['id'];
$file_name = $_GET['file_name'];
$csv_file_name = "";
$data = $pdo->selectOne("SELECT file_name from fulfillment_history where id = :id",array(':id' => $history_id));

if($data){
  $csv_file_name = $data['file_name'];

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' downloaded fulfillment file ',
    'ac_red_2'=>array(
      'title'=> $file_name,
    ),
  ); 
  activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'fulfillment_files','Admin downloaded fulfillment file', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));


}
$fullPath = $FULFILLMENT_FILES_DIR . $csv_file_name;
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
header("Content-Disposition: attachment; filename=\"" . $csv_file_name . "\";");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . $fsize);
readfile($fullPath);
?>

