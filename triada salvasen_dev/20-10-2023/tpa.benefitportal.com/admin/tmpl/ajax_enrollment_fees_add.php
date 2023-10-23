<?php
	include_once 'layout/start.inc.php';
	 
	$response=array();
	$validate = new Validation();


	$fee_id = isset($_POST['fee_id'])?$_POST['fee_id']:"";
	$product_type = 'Enrollment Fee';
	$record_type = 'Primary';

	//********** step1 varible intialization code start **********************
		$product_name = isset($_POST['product_name'])?$_POST['product_name']:"";
		$product_code = isset($_POST['product_code'])?$_POST['product_code']:"";
		
		$is_fee_on_renewal  = isset($_POST['is_fee_on_renewal'])?$_POST['is_fee_on_renewal']:"";
		$fee_renewal_type  = isset($_POST['fee_renewal_type'])?$_POST['fee_renewal_type']:"";
		$fee_renewal_count  = isset($_POST['fee_renewal_count'])?$_POST['fee_renewal_count']:"";

		$is_fee_on_commissionable  = isset($_POST['is_fee_on_commissionable'])?$_POST['is_fee_on_commissionable']:"";
		
		$price  = $_POST['price'];
		$non_commissionable_price  = isset($_POST['non_commissionable_price'])?$_POST['non_commissionable_price']:"";
		$commissionable_price  = isset($_POST['commissionable_price'])?$_POST['commissionable_price']:"";

		if($is_fee_on_commissionable=='N'){
			$non_commissionable_price = 0;
			$commissionable_price  = 0;
		}

		$product_array = empty($_POST['product']) ? array() : $_POST['product'];
		$product = '';
		if(count($product_array) > 0){
			$product = implode(",", $product_array);
		}		
	//********** step1 varible intialization code end   **********************

	


	//********* step1 validation code start ********************
		
			$validate->string(array('required' => true, 'field' => 'product_name', 'value' => $product_name), array('required' => 'Please Add Fee Name'));
			if ($product_name != "" && !$validate->getError('product_name')) {
				$sqlProduct="SELECT id FROM prd_main where id!=:id and name = :product_name and is_deleted='N'";
				$resProduct=$pdo->select($sqlProduct,array(":id"=>$fee_id,":product_name"=>$product_name));

				if(!empty($resProduct)){
					$validate->setError("product_name","Fee Name Already Exist");
				}
			}

			$validate->string(array('required' => true, 'field' => 'product_code', 'value' => $product_code), array('required' => 'Please Add Fee Id'));
			if ($product_code != "" && !$validate->getError('product_code')) {

				$sqlProduct="SELECT id FROM prd_main where id!=:id and product_code = :product_code and is_deleted='N'";
				$resProduct=$pdo->select($sqlProduct,array(":id"=>$fee_id,":product_code"=>$product_code));

				if(!empty($resProduct)){
					$validate->setError("product_code","Fee ID Already Exist");
				}
			}

			$validate->string(array('required' => true, 'field' => 'is_fee_on_renewal', 'value' => $is_fee_on_renewal), array('required' => 'Please Select Any Option'));

			if($is_fee_on_renewal=='Y'){
				$validate->string(array('required' => true, 'field' => 'fee_renewal_type', 'value' => $fee_renewal_type), array('required' => 'Please Select Any Option'));

				if($fee_renewal_type=='Set Renewals'){
					$validate->string(array('required' => true, 'field' => 'fee_renewal_count', 'value' => $fee_renewal_count), array('required' => 'Please Select Number Of Renewals'));
				}
				
			}
			$validate->string(array('required' => true, 'field' => 'is_fee_on_commissionable', 'value' => $is_fee_on_commissionable), array('required' => 'Please Select Any Option'));
			
			$validate->string(array('required' => true, 'field' => 'price', 'value' => $price), array('required' => 'Please Enter Fee Amount'));

			if($is_fee_on_commissionable=='Y'){
				$validate->string(array('required' => true, 'field' => 'non_commissionable_price', 'value' => $non_commissionable_price), array('required' => 'Please Enter Non Commissionable Amount'));
				$validate->string(array('required' => true, 'field' => 'commissionable_price', 'value' => $commissionable_price), array('required' => 'Please Enter Commissionable Amount'));
				if($price < $non_commissionable_price){
					  	$validate->setError("price","Enter Valid Price");
				}
			}

			if(count($product_array) <=0){
				$validate->setError("product","Please Select Product");
			}
	//********* step1 validation code end   ********************
	

	if ($validate->isValid()) {

			$insParams=array(
				'type'=>'Fees',
				'name'=>$product_name,
				'product_code'=>$product_code,
				'product_type'=>$product_type,
				'is_fee_on_renewal'=>$is_fee_on_renewal,
				'fee_renewal_type'=>'',
				'fee_renewal_count'=>0,
				'opt_rate_engine'=>'FixedPrice',
				'initial_charge_type'=>'OnCheckout',
				'is_fee_on_commissionable'=>$is_fee_on_commissionable,
				'admin_id' => $_SESSION['admin']['id'],
			);
			if($is_fee_on_renewal=='Y'){
				$insParams['fee_renewal_type']=$fee_renewal_type;
				$insParams['fee_renewal_count']=$fee_renewal_count;
				$insParams['opt_product_sell_type']='Subscription';
			}else{
				$insParams['opt_product_sell_type']='OneTime';
			}
			if(empty($fee_id)){
				$insParams['create_date'] = 'msqlfunc_NOW()';
				$insParams['record_type'] = $record_type;
				$insParams['status'] = 'Active';
				$fee_id=$pdo->insert('prd_main',$insParams);
			}else{
				$updWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$fee_id)
				);
				$pdo->update("prd_main",$insParams,$updWhere);
			}
			
			//****  enrollment fees id code start *************
				$checkEnrollmentFee="SELECT id,enrollment_fee_ids FROM prd_main where is_deleted='N' AND FIND_IN_SET(:fee_id,enrollment_fee_ids)";
				$resEnrollmentFee=$pdo->select($checkEnrollmentFee,array(":fee_id"=>$fee_id));

				if(!empty($resEnrollmentFee)){
					foreach ($resEnrollmentFee as $key => $product) {
						if(!in_array($product['id'],$product_array)){
							$enrollment_fee_ids_list=explode(",", $product['enrollment_fee_ids']);
							if (($key = array_search($fee_id, $enrollment_fee_ids_list)) !== false) {
							    unset($enrollment_fee_ids_list[$key]);
							}
							$enrollment_fee_ids = implode(",",$enrollment_fee_ids_list);
							$updParams=array("enrollment_fee_ids"=>trim($enrollment_fee_ids,","));
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(":id"=>$product['id'])
							);
							$pdo->update("prd_main",$updParams,$updWhere);
						}
					}
				}

				if(!empty($product_array)){
					foreach ($product_array as $key => $product) {
						$sqlProduct="SELECT id,enrollment_fee_ids FROM prd_main where is_deleted='N' AND id=:id";
						$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$product));

						if($resProduct){
							$enrollment_fee_ids_list=explode(",",$resProduct['enrollment_fee_ids']);
							if(!in_array($fee_id,$enrollment_fee_ids_list)){
								array_push($enrollment_fee_ids_list, $fee_id);

								$enrollment_fee_ids = implode(",", $enrollment_fee_ids_list);
								$updParams=array("enrollment_fee_ids"=>trim($enrollment_fee_ids,","),'is_enrollment_fee'=>'Y');
								$updWhere=array(
									'clause'=>'id=:id',
									'params'=>array(":id"=>$product)
								);
								$pdo->update("prd_main",$updParams,$updWhere);
							}

						}
					}
				}
			//****  enrollment fees id code end   *************

			$sqlMatrix="SELECT id FROM prd_matrix where product_id=:fee_id AND is_deleted='N'";
			$whereMatrix=array(":fee_id"=>$fee_id);
			$resMatrix=$pdo->selectOne($sqlMatrix,$whereMatrix);
			
			if($resMatrix){
				$matrixUpdParam=array(
					'opt_rate_engine'=>'FixedPrice',
					'price'=>$price,
					'non_commission_amount'=>$non_commissionable_price,
					'commission_amount'=>$commissionable_price,
				);
				if($is_fee_on_renewal=='Y'){
					$matrixUpdParam['opt_product_sell_type']='Subscription';
				}else{
					$matrixUpdParam['opt_product_sell_type']='OneTime';
				}
				$matrixUpdWhere=array(
					'clause'=>'id=:id',
					'params'=>array(":id"=>$resMatrix['id'])
				);
				$pdo->update("prd_matrix",$matrixUpdParam,$matrixUpdWhere);
			}else{
				$matrixIns = array(
					'opt_rate_engine'=>'FixedPrice',
					"product_id" => $fee_id,
					"price" => $price,
					'non_commission_amount'=>$non_commissionable_price,
					"commission_amount"=>$commissionable_price,
					"create_date" => 'msqlfunc_NOW()',
				);
				if($is_fee_on_renewal=='Y'){
					$matrixIns['opt_product_sell_type']='Subscription';
				}else{
					$matrixIns['opt_product_sell_type']='OneTime';
				}
				$pdo->insert("prd_matrix", $matrixIns);
			}
		
		$response['fee_id']=$fee_id;
		$response['status']="success";
	} else {
		$response['status'] = "fail";
		$response['errors'] = $validate->getErrors();
	}
	header('Content-Type: application/json');
	echo json_encode($response);
	exit;
?>