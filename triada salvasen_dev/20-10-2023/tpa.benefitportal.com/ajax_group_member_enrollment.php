<?php
	ini_set('memory_limit', '-1');
	ini_set('max_execution_time', '-1');
	include_once __DIR__ . '/includes/connect.php';
	include_once __DIR__ .'/includes/Api.class.php';
	include_once __DIR__ .'/includes/apiUrlKey.php';

	$ajaxApiCall = new Api();
	$apiResponse = '';
	// $_POST['step'] = 3;
	/*
	$fileData = [];
	if(!empty($_FILES)){
		foreach($_FILES as $key => $file){
			if(in_array($key,array('coverage_child_verification_doc','child_verification_doc','spouse_verification_doc','coverage_spouse_verification_doc'))){
				foreach($file as $k => $valueArr){
					foreach($valueArr as $i => $keyName){
						if($k =='tmp_name'){
							$_POST[$key][$i]['file_content'] = base64_encode(file_get_contents($keyName));
							$_POST[$key][$i][$k] = $keyName;
						}else{
							$_POST[$key][$i][$k] = $keyName;
						}
					}
				}
			}
			// else{
			// 	$_POST[$key][$i] = $file;
			// 	$_POST[$key][$i]['file_content'] = base64_encode(file_get_contents($file['tmp_name']));
			// }
		}
	}
	*/
	// pre_print($_POST);

	$_POST['enrolleeClass'] = checkIsset($_POST['hdn_enrolle_class']);
	$_POST['relationshipOfGroup'] = checkIsset($_POST['hdn_relationship_to_group']);
	$_POST['relationship_date'] = checkIsset($_POST['relationshipDate']);

	if ($_POST['api_key'] == "getProducts") { // to get self guiding benefits product
		$response = array();
		$_POST['userName'] = $_POST['groupId'];
		$takeHomePayDisplay = 'N';
		$prdJsonArr = $ajaxApiCall->ajaxApiCall($_POST,true);
		ob_start();
		$finalArray = array();
		$categoryArray = array();
		$combination_products = array();
		$enrolleeClassData = [];

		if (!empty($prdJsonArr) && $prdJsonArr["status"] ==  "Success") {
			$prdData = checkIsset($prdJsonArr['data']);
			if (!empty($prdData)) {
				$combination_products[] = checkIsset($prdData['combination_products']);
				$response['is_add_product'] = $prdData['is_add_product'];
				$response['addOnDisplay'] = $prdData['addOnDisplay'];
				
				$prdDetailArr = checkIsset($prdData['Products']);

				if (!empty($prdDetailArr)) {
					foreach ($prdDetailArr as $key => $value) {
						$categoryId = checkIsset($value["category_id"]);
						$categoryName = checkIsset($value["category_name"]);
						$orderBy = checkIsset($value["order_by"]);

						$categoryArray[$categoryName]["categoryId"] = $categoryId;
						$categoryArray[$categoryName]["categoryName"] = $categoryName;
						$categoryArray[$categoryName]["orderBy"] = $orderBy;

						$finalArray[$categoryId]["category_id"] = $categoryId;
						$finalArray[$categoryId]["category_name"] = $categoryName;
						$finalArray[$categoryId]["category_image"] = checkIsset($value["category_image"]);

						$prdArr = array();
						$prdId = checkIsset($value["product_id"]);
						$prdCode = checkIsset($value["product_code"]);
						$prdArr["productId"] = $prdId;
						$prdArr["productName"] = checkIsset($value["product_name"]);
						$prdArr["pricingModel"] = checkIsset($value["pricing_model"]);
						$prdArr["companyName"] = checkIsset($value["company_name"]);
						$prdArr["percentage_of_salary"] = checkIsset($value["percentage_of_salary"]);
						$prdArr["monthly_benefit_allowed"] = checkIsset($value["monthly_benefit_allowed"]);
						$prdArr["is_add_on_product"] = checkIsset($value["is_add_on_product"]);
						$prdArr["deduction"] = checkIsset($value["deduction"]);
						$prdArr["category_id"] = checkIsset($value["category_id"]);


						if($value['pricing_model'] == 'VariableEnrollee'){
							$prdMatrix = checkIsset($value['Enrollee_Matrix']);
						}else{
							$prdMatrix = checkIsset($value['Matrix']);
						}
						$prdTempMatrix = array();

						if (!empty($prdMatrix)) {
							foreach ($prdMatrix as $matrixKey => $value) {
								$prdTempMatrix[$matrixKey]["matrixId"] =  $value['matrix_id'];
								$prdTempMatrix[$matrixKey]["productPrice"] =  $value['product_price'];
								$prdTempMatrix[$matrixKey]["displayProductPrice"] =  $value['display_member_price'];
								$prdTempMatrix[$matrixKey]["planId"] =  $value['plan_id'];
								$prdTempMatrix[$matrixKey]["planName"] =  $value['plan_name'];
							}
						}

						$prdArr["matrix"] = $prdTempMatrix;
						$reqArr = array(
							'api_key' => 'productData',
							'productID' => $prdCode
						);

						$productDescJson = $ajaxApiCall->ajaxApiCall($reqArr);
						$productDescArr = json_decode($productDescJson, true);
						$prdArr["effectiveDate"] = checkIsset($productDescArr['data']["Effective Date"]);
						$prdArr["availableState"] = checkIsset($productDescArr['data']["Available State"]);
						$prdArr["requiredProduct"] = checkIsset($productDescArr['data']["Required Product"]);
						$prdArr["excludedProduct"] = checkIsset($productDescArr['data']["Excluded Product"]);
						$prdArr["productDescription"] = checkIsset($productDescArr['data']["Product Description"]);

						$finalArray[$categoryId]["productArr"][$prdId] = $prdArr;
						$finalArray[$categoryId]["product_list_count"] = !empty($finalArray[$categoryId]["product_list_count"])? ($finalArray[$categoryId]["product_list_count"] + 1):1;
					}
				}
				$takeHomePayDisplay = $prdData["takeHomePayDisplay"];
				$enrolleeClassData = !empty($prdData["getEnrolleeClass"][0]) ? $prdData["getEnrolleeClass"][0] : array();
			}
		}
		$combination_array = array();
		if(!empty($combination_products)){
			foreach($combination_products as $cKey => $cValue){
				if(!empty($cValue)){
					foreach($cValue as $pKey => $ruleArray ){
						$combination_array[$pKey] = $ruleArray;	
					}
				}
			}
		}
		// pre_print($productArr);
		// $columns = array_column($categoryArray, 'categoryName');
        // array_multisort($columns, SORT_ASC, $categoryArray);

		include('tmpl/self_guiding_benefits.inc.php');
		$response["htmlData"] = ob_get_contents();
		$response["already_puchase_product"] = !empty($prdJsonArr['data']['already_puchase_product']) ? $prdJsonArr['data']['already_puchase_product'] : array();
		ob_clean();

		$electedBundle = checkIsset($_POST['elected_bundle']);
		$_POST['product_matrix'] = checkIsset($_POST['bundle_product_matrix'][$electedBundle],'arr');
		$_POST['product_price'] = checkIsset($_POST['bundle_product_price'][$electedBundle]);
		$_POST['display_product_price'] = checkIsset($_POST['bundle_display_product_price'][$electedBundle]);
		$_POST['product_category'] = checkIsset($_POST['bundle_product_category'][$electedBundle]);
		$_POST['product_benefit_tier'] = checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle],'arr');
		$_POST['product_plan'] = checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle],'arr');
		
		$response["is_main_products"] = isset($prdJsonArr['data']['is_main_products']) ? $prdJsonArr['data']['is_main_products'] : true;;
		$response["categoryData"] = $categoryArray;
		$response["takeHomePayDisplay"] = $takeHomePayDisplay;
		$response["status"] = "Success";
		$apiResponse = json_encode($response);
	} else if($_POST['step'] == 2){
		$responseData = $ajaxApiCall->ajaxApiCall($_POST,true);
		if(!empty($responseData['status']) && $responseData['status'] == 'Success'){
			$apiResponse = [];
			if(!empty($responseData['data']['status']) && $responseData['data']['status'] == 'fail'){
				$apiResponse = json_encode($responseData['data'],true);
			}else{
				$groupId = !empty($_POST['groupId']) ? $_POST['groupId'] : "";
				if(!empty($groupId)){
					$data = [
						'userName' => $groupId,
						'api_key' => 'bundleQuestionsAnswers'
					];
					$ajaxApiCall = new Api();
					$customquestionapiResponse = $ajaxApiCall->ajaxApiCall($data,true);
					ob_start();
					include_once 'tmpl/group_member_customquestion.inc.php';
					$customquestionhtml = ob_get_clean();
					$apiResponse['html'] = $customquestionhtml;
					$apiResponse['status'] = 'Success';
					$apiResponse['step'] = $_POST['step'];
					$apiResponse = json_encode($apiResponse);
				}
			}
		}else{
			$apiResponse = json_encode($responseData,true);
		}
	} else if($_POST['step'] == 3){
		$groupId = !empty( $_POST['groupId']) ? $_POST['groupId'] : "";
		$_POST['userName'] = $groupId;
		if(!empty($groupId)){
			if($_POST['api_key'] == 'productAddToCart'){
				$_POST['api_key'] = 'cartTotalCalculate';
				$electedBundle = checkIsset($_POST['elected_bundle']);
				$_POST['product_matrix'] = checkIsset($_POST['bundle_product_matrix'][$electedBundle],'arr');
				$_POST['product_price'] = $_POST['bundle_product_price'][$electedBundle];
				$_POST['display_product_price'] = $_POST['bundle_display_product_price'][$electedBundle];
				$_POST['product_category'] = $_POST['bundle_product_category'][$electedBundle];
				$_POST['product_plan'] = checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle],'arr');
				$_POST['removed_product'] = checkIsset($_POST['removed_product'][$electedBundle],'arr');
				$_POST['product_benefit_tier'] = checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle],'arr');
				$_POST['product_benefit_tier'] = array_diff_key($_POST['product_benefit_tier'],$_POST['removed_product']);
				$_POST['product_benefit_tier'] = array_diff_key($_POST['product_benefit_tier'],$_POST['removed_product']);
				// pre_print($_POST);
				$apiResponse = ['status'=>'success'];
				foreach($_POST['product_benefit_tier'] as $prdId => $plan){
					$tempProductsArr = array_keys($_POST['product_benefit_tier']);
					if(!empty($_POST['removed_product'])){
						$tempProductsArr = array_diff($tempProductsArr,$_POST['removed_product']);
						$_POST['product_benefit_tier'] = array_diff_key($_POST['product_benefit_tier'],$_POST['removed_product']);
						$_POST['product_matrix'] = array_diff_key($_POST['product_matrix'],$_POST['removed_product']);
						$_POST['product_price'] = array_diff_key($_POST['product_price'],$_POST['removed_product']);
						$_POST['display_product_price'] = array_diff_key($_POST['display_product_price'],$_POST['removed_product']);
						$_POST['product_plan'] = array_diff_key($_POST['product_plan'],$_POST['removed_product']);
					}
					$_POST['added_product'] = count($tempProductsArr) > 1  ? implode(',',$tempProductsArr) : (in_array($prdId,$tempProductsArr) ? '' : implode(',',$tempProductsArr));
					$_POST['addding_product'] = $prdId;
					$responseArr = $ajaxApiCall->ajaxApiCall($_POST,true);

					$totalAddedProduct = !empty($_POST['product_list']) ? array_merge(explode(',',$_POST['product_list']),$tempProductsArr) : $tempProductsArr;

					$totalAddedProduct = !empty($_POST['already_puchase_product']) ? array_merge(explode(',',$_POST['already_puchase_product']),$totalAddedProduct) : $totalAddedProduct;
					if(!empty($responseArr['combination_products']['Packaged']) && (empty(array_intersect(explode(',',$responseArr['combination_products']['Packaged']['product_id']),$totalAddedProduct)))){
						$apiResponse['status'] = 'Fail';
						$apiResponse['message'] = $responseArr['premium_products'][$prdId]['product_name']." products is excluded until you add at at least one of the following ".$responseArr['combination_products']['Packaged']['product_name'];
					}else if(!empty($responseArr['combination_products']['Excludes']) ){

						$excludedProductsIds = !empty($responseArr['combination_products']['Excludes']['product_id']) ? explode(',',$responseArr['combination_products']['Excludes']['product_id']) : array();
						$excludedProductsNames = !empty($responseArr['combination_products']['Excludes']['product_name']) ? explode(',',$responseArr['combination_products']['Excludes']['product_name']) : array();
						$excludedProductsNamesArr = [];
						if(!empty($excludedProductsIds)){
							foreach($excludedProductsIds as $arrayKey => $exproductid){
								$excludedProductsNamesArr[$exproductid] = $excludedProductsNames[$arrayKey];
							}
						}

						$excludeProductExists = array_intersect($tempProductsArr,$excludedProductsIds);

						if(count($excludeProductExists) > 0){
							$ProductString = implode(',',array_intersect_key($excludedProductsNamesArr,array_flip($excludeProductExists)));
							if(checkIsset($responseArr['premium_products'][$prdId]['product_name']) != $ProductString){
								$apiResponse['status'] = 'Fail';
								$apiResponse['message'] = "You need to remove this products: ".$ProductString." Because its excluded for product ".checkIsset($responseArr['premium_products'][$prdId]['product_name']) .' or multiple core products';
								break;
							}							
						}
					}else if(!empty($responseArr['status']) && $responseArr['status'] == 'Error'){
						$apiResponse['status'] = 'Fail';
						$apiResponse['message'] = $responseArr['message'];
					}
				}
				$apiResponse = json_encode($apiResponse,true);
			}else{
				$_POST['api_key'] = 'bundleDetails';
				$formData = $_POST;
				$recommendedAPIRes = $ajaxApiCall->ajaxApiCall($formData ,true);
				$recommendResponseArr = !empty($recommendedAPIRes["data"]) ? $recommendedAPIRes["data"] : array();

				$takeHomePayDisplay = !empty($recommendResponseArr["takeHomePayDisplay"]) ? $recommendResponseArr["takeHomePayDisplay"] : "N";
				$recommendedapiResponse['data'] = !empty($recommendResponseArr["recommendationResponse"]) ? $recommendResponseArr["recommendationResponse"] : array();
				$enrolleeClassData = !empty($recommendResponseArr["getEnrolleeClass"][0]) ? $recommendResponseArr["getEnrolleeClass"][0] : array();

				$is_main_products = empty($recommendedapiResponse['data']) ? false : (isset($recommendedapiResponse['data']['is_main_products']) ? $recommendedapiResponse['data']['is_main_products'] : true);
				if(!empty($recommendedapiResponse['status']) && $recommendedapiResponse['status'] == 'fail'){
					$is_main_products = false;
					ob_start();
					include_once 'tmpl/group_member_enrollment_recommended.inc.php';
					$html = ob_get_clean();
					$apiResponse = [];
					$apiResponse['is_main_products'] = false;
					$apiResponse['html'] = $html;
					$apiResponse['status'] = 'Success';
					$apiResponse['step'] = $_POST['step'];
					$apiResponse['already_puchase_product'] = !empty($recommendResponseArr['already_puchase_product']) ? $recommendResponseArr['already_puchase_product'] : array();
					$apiResponse = json_encode($apiResponse);
				}else if(!empty($recommendedapiResponse)){
					ob_start();
					include_once 'tmpl/group_member_enrollment_recommended.inc.php';
					$html = ob_get_clean();
					$apiResponse = [];
					$apiResponse['is_main_products'] = isset($recommendedapiResponse['data']['is_main_products']) ? $recommendedapiResponse['data']['is_main_products'] : true;
					$apiResponse['html'] = $html;
					$apiResponse['status'] = 'Success';
					$apiResponse['step'] = $_POST['step'];
					$apiResponse['already_puchase_product'] = !empty($recommendResponseArr['already_puchase_product']) ? $recommendResponseArr['already_puchase_product'] : array();
					$apiResponse["takeHomePayDisplay"] = $takeHomePayDisplay;
					$apiResponse = json_encode($apiResponse);
				}
			}
		}
	} else if($_POST['api_key'] == "bundleComparision"){
			$_POST['userName'] = $_POST['groupId'];
			$_POST['api_key'] = 'bundleComparision';
			$bundleDate = $_POST;
			$ComparisionApiResponse = $ajaxApiCall->ajaxApiCall($bundleDate ,true);
			ob_start();
					include_once 'tmpl/compare_bundle.inc.php';
			$comparehtml = ob_get_clean();
			$apiResponse = [];
			$apiResponse['html'] =  $comparehtml;
			$apiResponse['status'] = 'Success';
			$apiResponse['step'] = $_POST['step'];
			$apiResponse = json_encode($apiResponse);
	} else if($_POST['api_key'] == "calculateRateQuestionsDetails"){
			$apiResponse = [];
			$_POST['userName'] = $_POST['groupId'];
			$_POST['api_key'] = 'calculateRateQuestionsDetails';
			
			$calculateRateQuestionsApiResponse = $ajaxApiCall->ajaxApiCall($_POST ,true);
			// pre_print($calculateRateQuestionsApiResponse);
				$apiResponse['step'] = $_POST['step'];
				$apiResponse['data'] = $calculateRateQuestionsApiResponse['data'];
				$apiResponse = json_encode($apiResponse);
	} else if($_POST['api_key'] == "productAddToCart") {
		// pre_print($_POST);
			$_POST['userName'] = $_POST['groupId'];
			$_POST['api_key'] = 'productAddToCart';
			$_POST = get_post_filtered_data();
			$productcartdata = $_POST;
			$productcartApiResponse = $ajaxApiCall->ajaxApiCall($productcartdata);
			$apiResponse = $productcartApiResponse;
	} else if($_POST['api_key'] == "getPrimaryMemberField") {
		$groupData = $_POST;
		$groupData['api_key'] = 'groupCompany';
		$groupData['groupCompany'] = $_POST['groupId'];
		$groupCompany = $ajaxApiCall->ajaxApiCall($groupData,true);
		$productcartdata = $_POST;
		$additionalInfo = $ajaxApiCall->ajaxApiCall($productcartdata,true);
		ob_start();
			include_once 'tmpl/group_member_enrollment_primary_detail.inc.php';
		$primary_member_field = ob_get_clean();
		$primaryResponse['html'] = $primary_member_field;
		$primaryResponse['status'] = 'success';
		$apiResponse = json_encode($primaryResponse,true);
	} else {
		$_POST['userName'] = $_POST['groupId'];
		// $_POST['api_key'] = "enrollmentSubmit";
		$_POST = get_post_filtered_data();
		$apiResponse = $ajaxApiCall->ajaxApiCall($_POST);
	}

	header('Content-type:application/json');
	echo $apiResponse;
	exit();
?>