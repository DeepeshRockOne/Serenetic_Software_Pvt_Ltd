<?php
	include_once 'layout/start.inc.php';
	include_once __DIR__ . '/../includes/function.class.php';
	$functionsList = new functionsList();
	 
	$response=array();
	$validate = new Validation();
	
	$connection_id = isset($_POST['connection_id'])?$_POST['connection_id']:"";
	$connection_category = isset($_POST['connection_category'])?$_POST['connection_category']:array();
	$upgrade_option = isset($_POST['upgrade_option'])?$_POST['upgrade_option']:array();
	$upgrade_within = isset($_POST['upgrade_within'])?$_POST['upgrade_within']:array();
	$upgrade_within_type = isset($_POST['upgrade_within_type'])?$_POST['upgrade_within_type']:array();
	$is_allow_upgrade_life_event = isset($_POST['is_allow_upgrade_life_event'])?$_POST['is_allow_upgrade_life_event']:array();
	$upgrade_life_event_options = isset($_POST['upgrade_life_event_options'])?$_POST['upgrade_life_event_options']:array();
	$downgrade_option = isset($_POST['downgrade_option'])?$_POST['downgrade_option']:array();
	$downgrade_within = isset($_POST['downgrade_within'])?$_POST['downgrade_within']:array();
	$downgrade_within_type = isset($_POST['downgrade_within_type'])?$_POST['downgrade_within_type']:array();
	$is_allow_downgrade_life_event = isset($_POST['is_allow_downgrade_life_event'])?$_POST['is_allow_downgrade_life_event']:array();
	$downgrade_life_event_options = isset($_POST['downgrade_life_event_options'])?$_POST['downgrade_life_event_options']:array();
	$connection_products = isset($_POST['connection_products'])?$_POST['connection_products']:array();
	$connected_product_arr = array();
	if(!empty($connection_products)){
		foreach ($connection_products as $catKey => $catProducts) {

			$validate->string(array('required' => true, 'field' => 'upgrade_option_'.$catKey, 'value' => $upgrade_option[$catKey]), array('required' => 'Please select option'));
			if($upgrade_option[$catKey] == "Available Within Specific Time Frame") {
				if(empty($upgrade_within[$catKey])){
					$validate->setError("upgrade_within_".$catKey,"Please Select Valid ".$upgrade_within_type[$catKey]);
				}
			}
			if(isset($is_allow_upgrade_life_event[$catKey]) && $is_allow_upgrade_life_event[$catKey] == "Y") {
				if(empty($upgrade_life_event_options[$catKey])) {
					$validate->setError('upgrade_life_event_options_'.$catKey,'Please select option');
				}
			}

			$validate->string(array('required' => true, 'field' => 'downgrade_option_'.$catKey, 'value' => $downgrade_option[$catKey]), array('required' => 'Please select option'));
			if($downgrade_option[$catKey] == "Available Within Specific Time Frame") {
				if(empty($downgrade_within[$catKey])){
					$validate->setError("downgrade_within_".$catKey,"Please Select Valid ".$downgrade_within_type[$catKey]);
				}
			}
			if(isset($is_allow_downgrade_life_event[$catKey]) && $is_allow_downgrade_life_event[$catKey] == "Y") {
				if(empty($downgrade_life_event_options[$catKey])) {
					$validate->setError('downgrade_life_event_options_'.$catKey,'Please select option');
				}
			}

			foreach ($catProducts as $key => $value) {
				$validate->string(array('required' => true, 'field' => 'connection_products_'.$catKey, 'value' => $value), array('required' => 'Please Select Product'));
				$connected_product_arr[$connection_id[$catKey].'_'.$connection_category[$catKey].'_'.$value]=$value;
			}
		}
	}


	
	if ($validate->isValid()) {
		if(!empty($connection_products)){
			$connectionResponse = array();
			foreach ($connection_products as $catKey => $catProducts) {
				$prd_conn_data = array(
					'category_id' => $connection_category[$catKey],
					'upgrade_option' => $upgrade_option[$catKey],
					'downgrade_option' => $downgrade_option[$catKey],
				);
				if($upgrade_option[$catKey] == "Available Within Specific Time Frame") {
					$prd_conn_data['upgrade_within'] = $upgrade_within[$catKey];
					$prd_conn_data['upgrade_within_type'] = $upgrade_within_type[$catKey];
				} else {
					$prd_conn_data['upgrade_within'] = '';
					$prd_conn_data['upgrade_within_type'] = '';
				}
				if(isset($is_allow_upgrade_life_event[$catKey]) && $is_allow_upgrade_life_event[$catKey] == "Y") {
					$prd_conn_data['is_allow_upgrade_life_event'] = 'Y';
					$prd_conn_data['upgrade_life_event_options'] = json_encode($upgrade_life_event_options[$catKey]);	
				} else {
					$prd_conn_data['is_allow_upgrade_life_event'] = 'N';
					$prd_conn_data['upgrade_life_event_options'] = '';
				}

				if($downgrade_option[$catKey] == "Available Within Specific Time Frame") {
					$prd_conn_data['downgrade_within'] = $downgrade_within[$catKey];
					$prd_conn_data['downgrade_within_type'] = $downgrade_within_type[$catKey];
				} else {
					$prd_conn_data['downgrade_within'] = '';
					$prd_conn_data['downgrade_within_type'] = '';
				}
				if(isset($is_allow_downgrade_life_event[$catKey]) && $is_allow_downgrade_life_event[$catKey] == "Y") {
					$prd_conn_data['is_allow_downgrade_life_event'] = 'Y';
					$prd_conn_data['downgrade_life_event_options'] = json_encode($downgrade_life_event_options[$catKey]);	
				} else {
					$prd_conn_data['is_allow_downgrade_life_event'] = 'N';
					$prd_conn_data['downgrade_life_event_options'] = '';
				}
				
				$connection_id=$connection_id[$catKey];
				if(empty($connection_id)){
					$connection_id=$pdo->insert('prd_connections',$prd_conn_data);
				} else {
					$update_where = array(
						'clause' => 'id=:id',
						'params' => array(
							':id' => $connection_id,
						),
					);
					$pdo->update('prd_connections', $prd_conn_data, $update_where);
				}
				$category_id=$connection_category[$catKey];


				$sqlTblConnection="SELECT id,category_id,product_id,connection_id FROM prd_connected_products WHERE is_deleted='N' AND connection_id=:connection_id";
				$resTblConnection=$pdo->select($sqlTblConnection,array(":connection_id"=>$connection_id));
				$tbl_connected_product_arr = array();

				if(!empty($resTblConnection)){
					foreach ($resTblConnection as $key => $value) {
						$tbl_connected_product_arr[$value['connection_id'].'_'.$value['category_id'].'_'.$value['product_id']]=$value['id'];
					}
				}
				$connectionResult=array_diff_key($tbl_connected_product_arr,$connected_product_arr);

				if(!empty($connectionResult)){
					foreach ($connectionResult as $key => $value) {
						$keyDiff = explode("_", $key);

						$sqlPrdMain = "SELECT id,name,product_code FROM prd_main where id=:id";
						$resPrdMain = $pdo->selectOne($sqlPrdMain,array(":id"=>$keyDiff[2]));
						$actProductInfo = "";

						if(!empty($resPrdMain)){
							$actProductInfo = $resPrdMain['name'].'('.$resPrdMain['product_code'] .')';
						}
						$categoryName = getname('prd_category',$keyDiff[1],'title','id');

						$updParams=array(
							'is_deleted'=>'Y'
						);
						$updWhere=array(
							'clause'=>'connection_id=:connection_id AND category_id=:category_id AND product_id=:product_id',
							'params'=>array(
								":connection_id"=>$keyDiff[0],
								":category_id"=>$keyDiff[1],
								":product_id"=>$keyDiff[2],
							)
						);
						$pdo->update("prd_connected_products",$updParams,$updWhere);

						$actFeed=$functionsList->generalActivityFeed('','','',$categoryName,$value,'prd_connected_products','Admin Removed Connected Product','Removed Connected Product '.$actProductInfo.' For');
					}

				}


				foreach ($catProducts as $key => $value) {
					$sqlPrdMain = "SELECT id,name,product_code FROM prd_main where id=:id";
					$resPrdMain = $pdo->selectOne($sqlPrdMain,array(":id"=>$value));
					$actProductInfo = "";

					if(!empty($resPrdMain)){
						$actProductInfo = $resPrdMain['name'].'('.$resPrdMain['product_code'] .')';
					}

					$insParams=array(
						'product_id'=>$value,
					);

					$sqlConnection="SELECT id FROM prd_connected_products WHERE is_deleted='N' AND product_id=:product_id AND category_id=:category_id AND connection_id=:connection_id";
					$resConnection=$pdo->selectOne($sqlConnection,array(":product_id"=>$value,":category_id"=>$category_id,":connection_id"=>$connection_id));

					if(empty($resConnection)){
						$insParams['connection_id'] = $connection_id;
						$insParams['category_id'] = $category_id;
						
						$product_connec_id=$pdo->insert('prd_connected_products',$insParams);
						$categoryName = getname('prd_category',$category_id,'title','id');

						$actFeed=$functionsList->generalActivityFeed('','','',$categoryName,$product_connec_id,'prd_connected_products','Admin Added Connected Product','Added Connected Product '.$actProductInfo.' For');
					}
				}

				$connectionResponse[$catKey] = $connection_id;


				//update order by code start
					$order_by = 1;

					$sqlOrdCon="SELECT id,product_id FROM prd_connected_products WHERE is_deleted='N' AND category_id=:category_id  order by id asc";
					$resOrdCon=$pdo->select($sqlOrdCon,array(":category_id"=>$category_id));
					
					$orderByProduct = array();
					if(!empty($resOrdCon)){
						foreach ($resOrdCon as $ordKey => $ordValue) {
							$updParams=array(
								'order_by'=>$order_by
							);
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$ordValue['id'],
								)
							);
							$pdo->update("prd_connected_products",$updParams,$updWhere);

							$ordPrdID = $ordValue['product_id'];
							$updParams=array(
								'order_by'=>$order_by
							);
							$updWhere=array(
								'clause'=>'id=:id',
								'params'=>array(
									":id"=>$ordPrdID,
								)
							);
							$pdo->update("prd_main",$updParams,$updWhere);
							array_push($orderByProduct, $ordPrdID);
							$order_by++;
						}

						$productList = 0;
						if(!empty($orderByProduct)){
							$productList = implode(",", $orderByProduct);
						}

						$sqlOrdConProduct = "SELECT id as product_id FROM prd_main where category_id = :category_id AND id not in ($productList) AND is_deleted='N'";
						$resOrdConProduct = $pdo->select($sqlOrdConProduct,array(":category_id"=>$category_id));

						if(!empty($resOrdConProduct)){
							foreach ($resOrdConProduct as $ordKey => $ordValue) {
								$ordPrdID = $ordValue['product_id'];
								$updParams=array(
									'order_by'=>$order_by
								);
								$updWhere=array(
									'clause'=>'id=:id',
									'params'=>array(
										":id"=>$ordPrdID,
									)
								);
								$pdo->update("prd_main",$updParams,$updWhere);
								$order_by++;
							}
						}

					}

				//update order by code end

			}
			$response['connection']=$connectionResponse;
			$response['status']="success";
		}	
	} else {
		$response['status'] = "fail";
		$response['errors'] = $validate->getErrors();
	}
	header('Content-Type: application/json');
	echo json_encode($response);
	dbConnectionClose();
	exit;
?>