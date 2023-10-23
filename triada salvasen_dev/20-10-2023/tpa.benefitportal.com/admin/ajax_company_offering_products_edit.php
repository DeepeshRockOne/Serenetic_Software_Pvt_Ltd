<?php
include_once 'layout/start.inc.php';
$res = array();
$validate = new Validation();

$company_id = checkIsset($_POST['company_id']);
$company_name = checkIsset($_POST['company_name']);

$validate->string(array('required' => true, 'field' => 'company_name', 'value' => $company_name), array('required' => 'Please Enter Company Name'));

if ($company_name != "" && !$validate->getError('company_name')) {

	if(!empty($company_id)){
		$sqlCategory="SELECT id FROM prd_company WHERE is_deleted='N' AND company_name = :company_name and md5(id)!=:id";
		$resCategory=$pdo->select($sqlCategory,array(":company_name"=>$company_name,":id"=>$company_id));
	}else{
		$sqlCategory="SELECT id FROM prd_company WHERE is_deleted='N' AND company_name = :company_name";
		$resCategory=$pdo->select($sqlCategory,array(":company_name"=>$company_name));
	}

	if(!empty($resCategory)){
		$validate->setError("company_name","Company Name Already Exist");
	}
}

if ($validate->isValid()) {

	$checksqlCompany="SELECT id,company_name,short_name FROM prd_company WHERE is_deleted='N' AND md5(id)=:id";
	$checkresCompany=$pdo->selectOne($checksqlCompany,array(":id"=>$company_id));

	if(!empty($checkresCompany)){
		$updateName = array(
			"company_name" => $company_name,
			'short_name' => str_replace(" ","_", $company_name)
		);
		$updateWhere = array(
			"clause" => "id=:id",
			"params" => array(":id" => $checkresCompany['id']),
		);

		//************* Activity Code Start *************
			$oldVaArray = $checkresCompany;
			$NewVaArray = $updateName;
			unset($oldVaArray['id']);

			$checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
			 
			if(!empty($checkDiff)){
				$activityFeedDesc['ac_message'] =array(
					'ac_red_1'=>array(
						'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
						'title'=>$_SESSION['admin']['display_id']),
					'ac_message_1' =>' Updated Companies Offering Product ',
					'ac_red_2'=>array(
						//'href'=> '',
						'title'=>$company_name,
					),
				); 
				
				if(!empty($checkDiff)){
					foreach ($checkDiff as $key1 => $value1) {
						$activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
					} 
				}
				
				activity_feed(3, $_SESSION['admin']['id'], 'Admin', $checkresCompany['id'], 'companies_offering_products','Admin Updated Companies Offering Products', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
			}
		//************* Activity Code End   *************

		$pdo->update("prd_company", $updateName, $updateWhere);
		$res['msg']='Company Name Updated Successfully';

	}else{
		$ins_params = array(
			'company_name' => $company_name,
			'site_url' => '',
			'short_name' => str_replace(" ","_", $company_name)
	    );
	    $company_id = $pdo->insert("prd_company", $ins_params);
	    $res['msg']='Company Name Added Successfully';
	    $res['new_company_id']=$company_id;
    	$res['new_company_name']=$company_name;

	    $description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Created Companies Offering Product ',
			'ac_red_2'=>array(
				//'href'=> '',
				'title'=>$company_name,
			),
		); 
		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $company_id, 'companies_offering_products','Admin Created Companies Offering Products', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
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