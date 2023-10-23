<?php
include_once 'layout/start.inc.php';

$response = array();
$src = $_POST['src'];
$fileName = basename($src);

$imagePath = $SUMMERNOTE_UPLOAD_DIR.$fileName;

if(!empty($src) && file_exists($imagePath)){
    if(unlink($imagePath))
    {
        $response["status"]= 'Succeess';
    }
}else{
    $response["status"]= 'Fail';
}


header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;

?>