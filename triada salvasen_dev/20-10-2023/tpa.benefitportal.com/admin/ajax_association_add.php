<?php
	include_once 'layout/start.inc.php';
	
	$response=array();
	$validate = new Validation();

	$fee_id = $_POST['fee_id'];
	$product_type = 'Association';
	$record_type = 'Primary';

	//********** step1 varible intialization code start **********************
		$product_name = isset($_POST['product_name'])?$_POST['product_name']:"";
		$product_code = isset($_POST['product_code'])?$_POST['product_name']:"";
		
		$is_fee_to_association = isset($_POST['is_fee_to_association'])?$_POST['is_fee_to_association']:"";
		$is_association_fee_included = isset($_POST['is_association_fee_included'])?$_POST['is_association_fee_included']:"";

		$is_fee_on_renewal  = isset($_POST['is_fee_on_renewal'])?$_POST['is_fee_on_renewal']:"";
		$fee_renewal_type  = isset($_POST['fee_renewal_type'])?$_POST['fee_renewal_type']:"";
		$fee_renewal_count  = isset($_POST['fee_renewal_count'])?$_POST['fee_renewal_count']:"";

		$is_fee_on_commissionable  = isset($_POST['is_fee_on_commissionable'])?$_POST['is_fee_on_commissionable']:"";
		
		$price  = isset($_POST['price'])?$_POST['price']:"";
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

		$is_assign_by_state = isset($_POST['is_assign_by_state'])?$_POST['is_assign_by_state']:"";
		$association_product = isset($_POST['association_product'])?$_POST['association_product']:array();
		$association_state_array = isset($_POST['association_state'])?$_POST['association_state']:array();
		
		$association_state = array();
		if(count($association_product) > 0){
			foreach ($association_product as $key => $productArr) {
				foreach ($productArr as $key1 => $product_id) {
					if (empty($association_state[$product_id])) {
						$stateArray =(!empty($association_state_array[$key])) ? $association_state_array[$key] : array();
	        		}else{
	        			$prevSelectedState=explode(",", $association_state[$product_id]);
	        			$currentSelectedState=(!empty($association_state_array[$key])) ? $association_state_array[$key] : array();
	        			$stateArray =array_unique(array_merge($prevSelectedState,$currentSelectedState));
	        			
	        		}
	        		asort($stateArray);
	        		$stateString=(!empty($stateArray)) ? implode(",", $stateArray) : '';
	        		$association_state[$product_id] = $stateString;
				}
			}
		}
	//********** step1 varible intialization code end   **********************

	


	//********* step1 validation code start ********************
		
			$validate->string(array('required' => true, 'field' => 'product_name', 'value' => $product_name), array('required' => 'Please Add Association Name'));
			if ($product_name != "" && !$validate->getError('product_name')) {
				$sqlProduct="SELECT id FROM prd_main where id!=:id and name = :product_name and is_deleted='N'";
				$resProduct=$pdo->select($sqlProduct,array(":id"=>$fee_id,":product_name"=>$product_name));

				if(!empty($resProduct)){
					$validate->setError("product_name","Association Name Already Exist");
				}
			}

			$validate->string(array('required' => true, 'field' => 'product_code', 'value' => $product_code), array('required' => 'Please Add Association Id'));
			if ($product_code != "" && !$validate->getError('product_code')) {

				$sqlProduct="SELECT id FROM prd_main where id!=:id and product_code = :product_code and is_deleted='N'";
				$resProduct=$pdo->select($sqlProduct,array(":id"=>$fee_id,":product_code"=>$product_code));

				if(!empty($resProduct)){
					$validate->setError("product_code","Association ID Already Exist");
				}
			}

			$validate->string(array('required' => true, 'field' => 'is_fee_to_association', 'value' => $is_fee_to_association), array('required' => 'Please Select Any Option'));

			if($is_fee_to_association=='Y'){
				$validate->string(array('required' => true, 'field' => 'is_association_fee_included', 'value' => $is_association_fee_included), array('required' => 'Please Select Any Option'));
				
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
					$validate->string(array('required' => true, 'field' => 'non_commissionable_price', 'value' => $non_commissionable_price), array('required' => 'Please Enter Amount'));

					$validate->string(array('required' => true, 'field' => 'commissionable_price', 'value' => $commissionable_price), array('required' => 'Please Enter Amount'));

					if($price < $non_commissionable_price){
					  	$validate->setError("price","Enter Valid Price");
					}
				}else{
					$commissionable_price = 0;
				}
				if(count($product_array) <=0){
					$validate->setError("product","Please Select Product");
				}
			}			
	//********* step1 validation code end   ********************
	

	if ($validate->isValid()) {

			$insParams=array(
				'type'=>'Fees',
				'name'=>$product_name,
				'product_code'=>$product_code,
				'product_type'=>$product_type,
				'is_fee_to_association'=>$is_fee_to_association,
				'is_association_fee_included'=>'N',
				'is_fee_on_renewal'=>'N',
				'fee_renewal_type'=>'',
				'fee_renewal_count'=>0,
				'opt_rate_engine'=>'FixedPrice',
				'is_assign_by_state'=>isset($is_assign_by_state)?$is_assign_by_state:'N',
				'is_fee_on_commissionable'=>'N',
				'admin_id' => $_SESSION['admin']['id'],
			);
			if($is_fee_to_association=='Y'){
				$insParams['is_association_fee_included']=$is_association_fee_included;
				$insParams['is_fee_on_renewal']=$is_fee_on_renewal;
				$insParams['is_fee_on_commissionable']=$is_fee_on_commissionable;

				if($is_fee_on_renewal=='Y'){
					$insParams['fee_renewal_type']=$fee_renewal_type;
					$insParams['fee_renewal_count']=$fee_renewal_count;
					$insParams['opt_product_sell_type']='Subscription';
				}else{
					$insParams['opt_product_sell_type']='OneTime';
				}
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
			
			//****  association fees states id code start *************
				$updParams=array(
					"is_deleted"=>'Y',
					'updated_at'=>'msqlfunc_NOW()',
				);
				$updWhere=array(
					'clause'=>'association_fee_id=:id',
					'params'=>array(":id"=>$fee_id)
				);

				$pdo->update("association_assign_by_state",$updParams,$updWhere);
				if(!empty($association_state) && $is_assign_by_state == 'Y'){
					foreach ($association_state as $product_id => $statesList) {
							
						$sqlAssociationState="SELECT id FROM association_assign_by_state 
											WHERE association_fee_id=:fee_id AND product_id=:product_id";
						$resAssociationState=$pdo->selectOne($sqlAssociationState,array(":fee_id"=>$fee_id,":product_id"=>$product_id));
							
						$states = '';
						if(!empty($statesList)){
							$states = $statesList;
						}

						if(!empty($states)){

							if($resAssociationState){
								$updParams=array(
									"states"=>$states,
									"is_deleted"=>'N',
								);
								$updWhere=array(
									'clause'=>'id=:id',
									'params'=>array(":id"=>$resAssociationState['id'])
								);
								$pdo->update("association_assign_by_state",$updParams,$updWhere);
							}else{
								$insParams=array(
									'association_fee_id'=>$fee_id,
									'product_id'=>$product_id,
									"states"=>$states,
									"is_deleted"=>'N',
									'created_at'=>'msqlfunc_NOW()',
								);

								$pdo->insert("association_assign_by_state",$insParams);
							}
						}else{
							if($resAssociationState){
								$updParams=array(
									"is_deleted"=>'Y',
									"states"=>'',
								);
								$updWhere=array(
									'clause'=>'id=:id',
									'params'=>array(":id"=>$resAssociationState['id'])
								);
								$pdo->update("association_assign_by_state",$updParams,$updWhere);
							}
						}
					}
				}
			//****  association fees states id code end   *************

			//****  association fees id code start *************
				$checkAssociationFee="SELECT id,association_ids FROM prd_main where is_deleted='N' AND FIND_IN_SET(:fee_id,association_ids)";
				$resAssociationFee=$pdo->select($checkAssociationFee,array(":fee_id"=>$fee_id));

				if(!empty($resAssociationFee)){
					foreach ($resAssociationFee as $key => $product) {
						if(!in_array($product['id'],$product_array) || $is_fee_to_association=='N'){
							$association_ids_list=explode(",", $product['association_ids']);
							if (($key = array_search($fee_id, $association_ids_list)) !== false) {
							    unset($association_ids_list[$key]);
							}
							$association_ids = implode(",",$association_ids_list);

							$updParams=array(
								"association_ids"=>trim($association_ids,","),
							);
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(":id"=>$product['id'])
							);

							$pdo->update("prd_main",$updParams,$updWhere);
						}
					}
				}
				if($is_fee_to_association=='Y'){
					if(!empty($product_array)){
						foreach ($product_array as $key => $product) {
							$sqlProduct="SELECT id,association_ids FROM prd_main where is_deleted='N' AND id=:id";
							$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$product));

							if($resProduct){
								$association_ids_list=explode(",",$resProduct['association_ids']);
								if(!in_array($fee_id,$association_ids_list)){
									array_push($association_ids_list, $fee_id);

									$association_ids = implode(",", $association_ids_list);


									$updParams=array(
										"association_ids"=>trim($association_ids,","),
										'is_association_require'=>'Y',
									);
									$updWhere=array(
										'clause'=>'id=:id',
										'params'=>array(":id"=>$product)
									);
									$pdo->update("prd_main",$updParams,$updWhere);
								}

							}
						}
					}
				}
			//****  association fees id code end   *************

			$sqlMatrix="SELECT id FROM prd_matrix where product_id=:fee_id AND is_deleted='N'";
			$whereMatrix=array(":fee_id"=>$fee_id);
			$resMatrix=$pdo->selectOne($sqlMatrix,$whereMatrix);
			if($is_fee_to_association=='Y'){
				if($resMatrix){
					$matrixUpdParam=array(
						'price'=>$price,
						'non_commission_amount'=>$non_commissionable_price,
						'commission_amount'=>$commissionable_price,
					);
					$matrixUpdWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$resMatrix['id'])
					);
					$pdo->update("prd_matrix",$matrixUpdParam,$matrixUpdWhere);
				}else{
					$matrixIns = array(
						"product_id" => $fee_id,
						"price" => $price,
						'non_commission_amount'=>$non_commissionable_price,
						"commission_amount"=>$commissionable_price,
						"create_date" => 'msqlfunc_NOW()',
					);
					$pdo->insert("prd_matrix", $matrixIns);
				}
			}else{
				if($resMatrix){
					$matrixUpdParam=array(
						'is_deleted'=>'Y',
					);
					$matrixUpdWhere=array(
						'clause'=>'id=:id',
						'params'=>array(":id"=>$resMatrix['id'])
					);
					$pdo->update("prd_matrix",$matrixUpdParam,$matrixUpdWhere);
				}
			}
			
		
		$response['fee_id']=$fee_id;
		$response['status']="success";
	} else {
		$response['status'] = "fail";
		$response['errors'] = $validate->getErrors();
	}
	header('Content-Type: application/json');
	echo json_encode($response);
	dbConnectionClose();
	exit;
?>