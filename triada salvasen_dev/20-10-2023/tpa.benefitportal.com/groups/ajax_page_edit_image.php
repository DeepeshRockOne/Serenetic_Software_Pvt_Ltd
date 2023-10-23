<?php
include_once __DIR__ . '/layout/start.inc.php';
$validate = new validation();

$BROWSER = getBrowser();
$OS = getOs($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

$div_step_error = "";
$response = array();
extract($_POST);
$action = isset($_POST['action'])?$_POST['action']:'';
$cover_image = isset($_POST['cover_image'])?$_POST['cover_image']:array('name' => '');
if ($action == "delete") {
	$check = $pdo->selectOne("SELECT * FROM page_builder_images WHERE page_builder_id=:page_builder_id AND id=:id",array(":page_builder_id"=>$page_builder_id,":id"=>$image_id));
	if ($check) {
		if(!empty($check["image_name"]) && file_exists($PAGE_COVER_DIR.DIRECTORY_SEPARATOR.$check["image_name"])){
			unlink($PAGE_COVER_DIR.DIRECTORY_SEPARATOR.$check["image_name"]);
		}
		$updatePageBuilderWhere = array(
			'clause' => 'id=:id',
			'params' => array(
				':id' => $image_id,
			),
		);
		$pdo->update('page_builder_images', array("is_deleted"=>"Y"), $updatePageBuilderWhere);
		$response['status'] = 'success';
	} else {
		$response['status'] = 'fail';		
	}
} else {
	$validate->string(array('required' => true, 'field' => 'cover_image', 'value' => $cover_image['name']), array('required' => 'Please upload Cover Image'));

	if (!$validate->getError('cover_image')) {
		if (!in_array($cover_image['type'], array("image/gif", "image/jpeg", "image/jpg", "image/png"))) {
			$validate->setError('cover_image', 'Only .jpg, .png, .gif file format allow');
		}

		if (!$validate->getError('cover_image')) {
			$MAX_SIZE = 1024 * 1024 * 4;
			if ($cover_image['size'] > $MAX_SIZE) {
				$validate->setError("cover_image", "Cover Image size limit upto 4MB");
			}
		}
	}

	if ($validate->isValid()) {
		if ($page_builder_id > 0) {
			$updatePageBuilder = array();
			$new_cover_image = $cover_image['name'];
			if ($cover_image['name'] != "" && $cover_image['tmp_name'] != "") {
				$new_cover_image = md5(time() . rand(1, 10000)) . ".png";
				save_base64_file_new($cover_image['tmp_name'], $PAGE_COVER_DIR, $new_cover_image);
				$updatePageBuilder["cover_image"] = $new_cover_image;
			}
			$image_id = $pdo->insert("page_builder_images", array(
				"page_builder_id" => $page_builder_id,
				"image_name" => $new_cover_image,
				"updated_at" => "msqlfunc_NOW()",
				"created_at" => "msqlfunc_NOW()",
			));
			$response["cover_image"] = $new_cover_image;
			$response['page_builder_id'] = $page_builder_id;
			$response['image_id'] = $image_id;
			$response['status'] = 'success';
		}
	}
}
if (count($validate->getErrors()) > 0) {
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
	$response['div_step_error'] = $div_step_error;
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
?>
