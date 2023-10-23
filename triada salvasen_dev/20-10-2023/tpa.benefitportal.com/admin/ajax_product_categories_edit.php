<?php
include_once 'layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
$res = array();
$validate = new Validation();

$category_id = checkIsset($_POST['category_id']);
$category_name= checkIsset($_POST['category_name']);
$short_description= checkIsset($_POST['short_description']);
$old_category_image= checkIsset($_POST['old_category_image']);
$category_image= !empty($_FILES['category_image']) ? $_FILES['category_image'] : '';
$image_name = $old_category_image;



$validate->string(array('required' => true, 'field' => 'category_name', 'value' => $category_name), array('required' => 'Please Enter Category Name'));
$validate->string(array('required' => true, 'field' => 'short_description', 'value' => $short_description), array('required' => 'Please Enter Category Description'));


if ($category_name != "" && !$validate->getError('category_name')) {
	
	if(!empty($category_id)){
		$sqlCategory="SELECT id FROM prd_category WHERE is_deleted='N' AND title = :title and md5(id)!=:id";
		$resCategory=$pdo->select($sqlCategory,array(":title"=>$category_name,":id"=>$category_id));
	}else{
		$sqlCategory="SELECT id FROM prd_category WHERE is_deleted='N' AND title = :title";
		$resCategory=$pdo->select($sqlCategory,array(":title"=>$category_name));
	}

	if(!empty($resCategory)){
		$validate->setError("category_name","Category Already Exist");
	}
}
if(empty($category_image["name"]) && empty($old_category_image)){
	$validate->setError('category_image', 'Please Add Category Image');
}
if (!empty($category_image["name"])) {
	if (!in_array($category_image['type'], array("image/jpeg", "image/jpg", "image/png"))) {
		$validate->setError('category_image', 'Only .jpg, .png, .jpeg file format allow');
	}
}


if ($validate->isValid()) {

	$checksqlCategory="SELECT id,title,short_description,category_image FROM prd_category WHERE is_deleted='N' AND md5(id)=:id";
	$checkresCategory=$pdo->selectOne($checksqlCategory,array(":id"=>$category_id));
	if (!empty($category_image["name"])) {
		$image_name = round(microtime(true)).'_'.str_replace(" ", "", $category_image['name']);
		
		$image_tmp_name = $category_image['tmp_name'];
		if (!empty($old_category_image)) {
			if (file_exists($CATEGORY_IMAGE_DIR . $old_category_image)) {
				unlink($CATEGORY_IMAGE_DIR . $old_category_image);
			}
		}
		move_uploaded_file($image_tmp_name, $CATEGORY_IMAGE_DIR . $image_name);
	}
	if(!empty($checkresCategory)){
		$updateName = array(
			"title" => $category_name,
			"short_description" => $short_description,
		);
		if(!empty($image_name)){
			$updateName['category_image']=$image_name;
		}
		$updateWhere = array(
			"clause" => "id=:id",
			"params" => array(":id" => $checkresCategory['id']),
		);

		//************* Activity Code Start *************
			$oldVaArray = $checkresCategory;
			$NewVaArray = $updateName;
			unset($oldVaArray['id']);

			$activity=array_diff_assoc($oldVaArray,$NewVaArray);
			
            $tmp = array();
            $tmp2 = array();
			 
			if(!empty($activity)){
				if(array_key_exists('short_description',$activity)){
	                $tmp['display_desc']=base64_encode($activity['short_description']);
	                $tmp2['display_desc']=base64_encode($NewVaArray['short_description']);
              	}
	            
	            if(array_key_exists('title',$activity)){
	                $tmp['Category Name'] = $activity['title'];
	                $tmp2['Category Name'] = $NewVaArray['title'];
	            }
				
				$actFeed=$functionsList->generalActivityFeed($tmp,$tmp2,'',$category_name,$checkresCategory['id'],'prd_category','Admin Updated Product Category','Updated Product Category');
			}
		//************* Activity Code End   *************

		$pdo->update("prd_category", $updateName, $updateWhere);
		$res['msg']='Category Name Added Successfully';
	}else{
		$ins_params = array(
			'title' => $category_name,
			'short_description' => $short_description,
			'status' => 'Active',
			'admin_id' => $_SESSION['admin']['id'],
			'create_date' => 'msqlfunc_NOW()'
	    );
	    if(!empty($image_name)){
			$ins_params['category_image']=$image_name;
		}
	    $category_id = $pdo->insert("prd_category", $ins_params);
	    $res['new_category_id']=$category_id;
    	$res['new_category_title']=$category_name;
		$res['msg']='Category Name Updated Successfully';

		$description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Created Product Category ',
			'ac_red_2'=>array(
				//'href'=> '',
				'title'=>$category_name,
			),
		); 
		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $category_id, 'prd_category','Admin Created Product Category', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}
	
	$res["status"] = "success";
}else{
	$errors = $validate->getErrors();
	$res["errors"] = $errors;
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>