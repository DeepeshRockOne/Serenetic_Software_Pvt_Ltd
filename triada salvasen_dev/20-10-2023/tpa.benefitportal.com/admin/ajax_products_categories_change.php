<?php
include_once 'layout/start.inc.php';
$res = array();
$offering_company = checkIsset($_POST['offering_company']);

if (!empty($offering_company)) {
	foreach ($offering_company as $product_id => $category_id) {

		$checksqlProduct="SELECT id,category_id,product_code FROM prd_main WHERE is_deleted='N' AND id=:id";
		$checkresProduct=$pdo->selectOne($checksqlProduct,array(":id"=>$product_id));

		if(!empty($checkresProduct)){
			$updateParams = array("category_id" => $category_id);
			$updateWhere = array(
				"clause" => "id=:id",
				"params" => array(":id" => $product_id),
			);
			$pdo->update("prd_main", $updateParams, $updateWhere);

			//************* Activity Code Start *************
				$oldVaArray = $checkresProduct;
				$NewVaArray = $updateParams;
				unset($oldVaArray['id']);

				$checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
				 
				if(!empty($checkDiff)){
					$activityFeedDesc['ac_message'] =array(
						'ac_red_1'=>array(
							'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
							'title'=>$_SESSION['admin']['display_id']),
						'ac_message_1' =>' Updated Product ',
						'ac_red_2'=>array(
							//'href'=> '',
							'title'=>$checkresProduct['product_code'],
						),
					); 
					
					if(!empty($checkDiff)){
						foreach ($checkDiff as $key1 => $value1) {
							if($key1=='category_id'){
								$oldVaArray[$key1]=getname('prd_category',$oldVaArray[$key1],'title','id');
								$NewVaArray[$key1]=getname('prd_category',$NewVaArray[$key1],'title','id');
							}
							$activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
						} 
					}
					
					activity_feed(3, $_SESSION['admin']['id'], 'Admin', $checkresProduct['id'], 'product','Admin Updated Product', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
				}
			//************* Activity Code End   *************

		}
	}	
	setNotifySuccess("Company Updated Successfully");
	$res["status"] = "success";
}else{
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>