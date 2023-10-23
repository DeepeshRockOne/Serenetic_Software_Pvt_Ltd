<?php
include_once __DIR__ . '/layout/start.inc.php';
$validate = new validation();
$BROWSER = getBrowser();
$OS = getOs($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$response = array();
$div_step_error = "";
$page_builder_id = isset($_POST['page_builder_id'])?$_POST['page_builder_id']:'';

$pb_sql = "SELECT * from page_builder WHERE id=:page_builder_id and is_deleted='N'";
$pb_where = array(':page_builder_id' => $page_builder_id);
$pb_row = $pdo->selectOne($pb_sql,$pb_where);

$data_action = isset($_POST['data_action'])?$_POST['data_action']:'';
$step = isset($_POST['step'])?$_POST['step']:'';
$cover_image = isset($_POST['pb_img'])?$_POST['pb_img']:'';
$header_content = isset($_POST['header_content'])?$_POST['header_content']:'';
$header_subcontent = isset($_POST['header_subcontent'])?$_POST['header_subcontent']:'';
$logo = isset($_FILES["logo"])?$_FILES["logo"]:array();
$category_ids = isset($_POST['category_ids'])?implode(",", $_POST['category_ids']):'';
$product_ids = isset($_POST['product_ids'])?implode(",", $_POST['product_ids']):'';
$class_ids = isset($_POST['class_ids'])?implode(",", $_POST['class_ids']):'';
$page_name = isset($_POST['page_name'])?$_POST['page_name']:'';
$contact_us_emails = isset($_POST['contact_us_emails'])?rtrim(trim($_POST['contact_us_emails']),";"):'';
$user_name = isset($_POST['user_name'])?$_POST['user_name']:'';
$contact_us_phone_number = isset($_POST['contact_us_phone_number'])?phoneReplaceMain($_POST['contact_us_phone_number']):'';

if(!empty($pb_row)) {
	if (!in_array($data_action, array("preview", "draft"))) {
		if ($step >= 1) {
			$validate->string(array('required' => true, 'field' => 'cover_image', 'value' => $cover_image), array('required' => 'Please Add/Select Cover Image'));
			$validate->string(array('required' => true, 'field' => 'header_content', 'value' => $header_content), array('required' => 'Header content is required'));
			$validate->string(array('required' => true, 'field' => 'header_subcontent', 'value' => $header_subcontent), array('required' => 'Header content is required'));
			
			if (!empty($logo) && $logo['error'] > 0) {
				$validate->setError('logo', 'Logo is required');
			} else if (!empty($logo["name"])) {
				if (!in_array($logo['type'], array("image/jpeg", "image/jpg", "image/png"))) {
					$validate->setError('logo', 'Only .jpg, .png, .jpeg file format allow');
				}
			}

			if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
				$div_step_error = "top_fold_panel";
			}
		}

		if ($step >= 2) {
			if (empty($category_ids)) {
				$validate->setError('category_ids', 'Please select at least one category.');
			}
			if (!$validate->getError('category_ids') && empty($product_ids)) {
				$validate->setError('product_ids', 'Please select at least one product.');
			}
			if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
				$div_step_error = "products_panel";
			}
		}

		if ($step >= 3) {
			if (empty($class_ids)) {
				$validate->setError('class_ids', 'Please select at least one Class.');
			}
			$validate->string(array('required' => true, 'field' => 'page_name', 'value' => $page_name), array('required' => 'Site name is required'));

			$validate->string(array('required' => true, 'field' => 'contact_us_emails', 'value' => $contact_us_emails), array('required' => 'At least one email required'));
			if (!empty($contact_us_emails)) {
				$contact_us_email_arr = explode(';', $contact_us_emails);
				foreach ($contact_us_email_arr as $tmp_email) {
					if (!empty($tmp_email)) {
						$validate->email(array('required' => true, 'field' => 'contact_us_emails', 'value' => $tmp_email), array('required' => 'Email is required', 'invalid' => 'Please add all valid email'));
					}
				}
			}

			$validate->string(array('required' => true, 'field' => 'user_name', 'value' => $user_name), array('required' => 'Username is required'));
			if ($user_name != "" && !$validate->getError('user_name')) {
				if (strlen($user_name) < 4 || strlen($user_name) > 20) {
					$validate->setError('user_name', 'User name must be 4-20 characters');
				} else if (!preg_match('/^[a-zA-Z0-9_-]{4,20}$/', $user_name)) {
					$validate->setError('user_name', 'Valid user name is required');
				}
				if (!isset($page_builder_id)) {
					$page_builder_id = 0;
				}
				if (!isValidUserName($user_name, 0, $page_builder_id)) {
					$validate->setError('user_name', 'Username is already exists');
				}
			}

			$validate->digit(array('required' => true, 'field' => 'contact_us_phone_number', 'value' => $contact_us_phone_number, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

			if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
				$div_step_error = "contact_us_panel";
			}
		}
	}

	if ($validate->isValid()) {
		$af_new_data = array(
			'header_content' => makeSafe($header_content),
			'header_subcontent' => makeSafe($header_subcontent),
			'category_ids' => $category_ids,
			'product_ids' => $product_ids,
			'class_ids' => $class_ids,
			'page_name' => $page_name,
			'contact_us_emails' => $contact_us_emails,
			'user_name' => $user_name,
			'contact_us_phone_number' => $contact_us_phone_number,
			'page_name' => $page_name,
			
		);

		$pb_upd_data = array(
			'header_content' => makeSafe($header_content),
			'header_subcontent' => makeSafe($header_subcontent),
			'category_ids' => $category_ids,
			'product_ids' => $product_ids,
			'class_ids' => $class_ids,
			'page_name' => $page_name,
			'contact_us_emails' => $contact_us_emails,
			'user_name' => $user_name,
			'contact_us_phone_number' => $contact_us_phone_number,
			'is_social' => "N",
			'updated_at' => 'msqlfunc_NOW()',
			'page_name' => $page_name,
			
		);

		if (!empty($cover_image)) {
			$pb_upd_data["cover_image"] = $cover_image;
		}

		if (!empty($logo["name"])) {
			$logo_name = explode(".", $logo['name']);
			$logo_extension = end($logo_name);
			$logo_tmp_name = $logo['tmp_name'];
			$new_logo_name = 'logo_' . round(microtime(true)) . '.' . $logo_extension;
			if (!empty($pb_row["logo"])) {
				if (file_exists($PAGE_LOGO_DIR . $pb_row["logo"])) {
					unlink($PAGE_LOGO_DIR . $pb_row["logo"]);
				}
			}
			move_uploaded_file($logo_tmp_name, $PAGE_LOGO_DIR . $new_logo_name);
			$pb_upd_data["logo"] = $new_logo_name;
			$response["logo"] = $PAGE_LOGO_WEB . $new_logo_name;
		}
		

		if ($data_action == "update") {
			$pb_upd_data["status"] = "Active";
		} else if ($data_action == "draft") {
			$pb_upd_data["status"] = "Draft";
		}

		//$pb_upd_data = array_filter($pb_upd_data,"strlen"); //removes null and blank array fields from array

		$pb_upd_where = array(
			'clause' => 'id=:id',
			'params' => array(
				':id' => $page_builder_id,
			),
		);
		$pdo->update('page_builder', $pb_upd_data, $pb_upd_where);
		$af_upd_data = $pb_row;

		
		$response['status'] = 'success';
		if ($data_action == "update") {
			$response['msg'] = 'Your Website is now published, <a target="_BLANK" href="'.$DEFAULT_SITE_URL.'/"' . $user_name . '">Click Here</a> to access you page.';
			$response['msg'] = 'Site Published';

		} else if ($data_action == "next") {
			
		} else {
			$response['msg'] = 'Your Website is saved as draft successfully';
		}

		if(in_array($data_action,array("update","draft"))) {
			$af_sql = "SELECT id from activity_feed WHERE entity_type='page_builder' AND entity_id=:entity_id AND entity_action='Website Created'";
			$af_where = array(':entity_id' => $page_builder_id);
			$af_row = $pdo->selectOne($af_sql,$af_where);
			if(empty($af_row)) {
				$desc = array();
	            $desc['ac_message'] = array(
	                'ac_red_1' => array(
	                    'href' => 'groups_details.php?id=' . md5($_SESSION['groups']['id']),
	                    'title' => $_SESSION['groups']['rep_id'],
	                ),
	                'ac_message_1' => ' created Website ',
	                'ac_red_2' => array(
	                    'href' => 'page_builder.php?id='.md5($pb_row['id']),
	                    'title' => $pb_row['page_name'],
	                ),
	            );
	            $desc = json_encode($desc);
	            activity_feed(3, $_SESSION['groups']['id'], 'Group', $pb_row['id'], 'page_builder', 'Website Created', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
			} else {

				$desc = array();
	            $desc['ac_message'] = array(
	                'ac_red_1' => array(
	                    'href' => 'groups_details.php?id=' . md5($_SESSION['groups']['id']),
	                    'title' => $_SESSION['groups']['rep_id'],
	                ),
	                'ac_message_1' => ' updated Website ',
	                'ac_red_2' => array(
	                    'href' => $GROUP_HOST.'/page_builder.php?id='.md5($pb_row['id']),
	                    'title' => $pb_row['page_name'],
	                ),
	            );
	            if(!empty($af_upd_data)){
		            foreach($af_upd_data as $key => $value){
		            	if(isset($af_new_data[$key]) && $af_new_data[$key] != $value) {
		            		if($key == 'contact_us_phone_number'){
			                    $value = format_telephone($value);
			                    $af_new_data[$key] = format_telephone($af_new_data[$key]);
							}elseif($key == 'class_ids'){
								$new_class_name['class_name'] = $old_class_name['class_name'] = '';
								if(!empty($value)){
									$old_class_name = $pdo->selectOne("SELECT GROUP_CONCAT(class_name) as class_name from group_classes where id IN(".$value.") AND is_deleted='N' AND group_id=:group_id ",array(":group_id"=>$_SESSION['groups']['id']));
								}
								if(!empty($af_new_data[$key])){
									$new_class_name = $pdo->selectOne("SELECT GROUP_CONCAT(class_name) as class_name from group_classes where id IN(".$af_new_data[$key].") AND is_deleted='N' AND group_id=:group_id ",array(":group_id"=>$_SESSION['groups']['id']));
								}
								
			                    $value = $old_class_name['class_name'];
			                    $af_new_data[$key] = $new_class_name['class_name'];
			                }
			                $tmp_key = ucwords(str_replace('_',' ',$key));
			                $tmp_desc = ' From '.$value.' to '.$af_new_data[$key];
			                if($key == "logo" || $key == "cover_image") {
			                	$tmp_desc =  " updated";
			                }
			                $desc['key_value']['desc_arr'][$tmp_key] = $tmp_desc; 
		            	}
		            }
		        }
	            $desc = json_encode($desc);
	            $response['desc'] = $desc;
	            activity_feed(3, $_SESSION['groups']['id'], 'Group', $pb_row['id'], 'page_builder', 'Website Updated', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
			}
		}
	}
} else {
	$response['status'] = 'error';
	$response['msg'] = 'Oops.. No page found to update';
}


if (count($validate->getErrors()) > 0) {
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
	$response['div_step_error'] = $div_step_error;
}

$response['page_builder_id'] = $page_builder_id;
$response["data_action"] = $data_action;
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
?>
