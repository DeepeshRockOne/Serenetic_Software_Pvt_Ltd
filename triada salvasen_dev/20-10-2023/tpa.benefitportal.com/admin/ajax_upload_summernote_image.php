<?php
include_once 'layout/start.inc.php';
$response = array();

if ($_FILES['image']['name']) {
    if (!$_FILES['image']['error']) {
            $name = md5(rand(100, 200)).date("mdYHis");
            $ext = explode('.', $_FILES['image']['name']);
            $filename = $name . '.' . $ext[1];
            $destination = $SUMMERNOTE_UPLOAD_DIR . $filename;
            $location = $_FILES["image"]["tmp_name"];
            move_uploaded_file($location, $destination);
            $response = $SUMMERNOTE_WEB . $filename;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;

?>