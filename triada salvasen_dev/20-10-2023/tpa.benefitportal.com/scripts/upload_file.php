<?php
$ForceDEV_Overwrite = 1;
include_once '../includes/connect.php';

$path = $_POST['path'];
$file_content= $_POST['file_content'];
$file_name = $_POST['file_name'];
$fullPath = $path . $file_name;
$remove_file_name = $_POST['remove_file_name'];

/*$fp = fopen("upload_file_data.txt", 'a+');
fwrite($fp, json_encode($_POST) . "\n\n");
fclose($fp);*/

if($file_content != ""){
  
  if($remove_file_name != ""){
    if(file_exists($path.$remove_file_name)){
      unlink($path.$remove_file_name);
    }
  }
  
  $content = base64_decode($file_content);
  if(file_put_contents($fullPath, $content)){
    echo "success";
  } else {
    echo "fail";
  }
}
?>