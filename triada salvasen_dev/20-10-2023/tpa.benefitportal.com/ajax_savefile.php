<?php
include_once 'includes/connect.php';
if ($_POST["action"]) {
	extract($_POST);
	if ($action == "save") {
		$data["code"] = 200;
		$data["msg"] = -1;
		$data["imagePath"] = $TMP_WEB . save_base64_file_new($imageCode, $TMP_DIR);
	} else {
		$data["code"] = 300;
		$data["msg"] = -1;
	}
}
echo json_encode($data);
exit;
?>