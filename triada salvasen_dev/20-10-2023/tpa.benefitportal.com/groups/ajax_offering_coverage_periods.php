<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);


$response = array();
$validate = new Validation();

$today_date=date('Y-m-d');

$group_coverage_period_id = isset($_POST['group_coverage_period_id'])?$_POST['group_coverage_period_id']:"";
$offering_id = isset($_POST['offering_id'])?$_POST['offering_id']:"";
$class_id = isset($_POST['class_id'])?$_POST['class_id']:"";
$group_id = isset($_POST["group_id"]) ? $_POST["group_id"] : 0;

$submit_type = isset($_POST['submit_type'])?$_POST['submit_type']:"";
$action = isset($_POST['action'])?$_POST['action']:"";
$step = isset($_POST['dataStep'])?$_POST['dataStep']:"";

$response['step']=$step;
$response['submit_type']=$submit_type;
$response['action']=$action;

//********** step1 varible intialization code start **********************
	$open_enrollment_start = !empty($_POST['open_enrollment_start']) ? $_POST['open_enrollment_start'] : '';
	$open_enrollment_end = !empty($_POST['open_enrollment_end']) ? $_POST['open_enrollment_end'] : '';
	$first_coverage_date = !empty($_POST['first_coverage_date']) ? $_POST['first_coverage_date'] : '';
	$waiting_restriction_on_open_enrollment = !empty($_POST['waiting_restriction_on_open_enrollment']) ? $_POST['waiting_restriction_on_open_enrollment'] : '';
	$allow_future_effective_date = !empty($_POST['allow_future_effective_date']) ? $_POST['allow_future_effective_date'] : '';
	$allowed_range = !empty($_POST['allowed_range']) ? $_POST['allowed_range'] : '';
//********** step1 varible intialization code start **********************

//********** step2 varible intialization code start **********************
	$products = !empty($_POST['products']) ? $_POST['products'] : array();
	$is_contribution = !empty($_POST['is_contribution']) ? $_POST['is_contribution'] : '';
	$display_contribution_on_enrollment = !empty($_POST['display_contribution_on_enrollment']) ? $_POST['display_contribution_on_enrollment'] : '';
//********** step2 varible intialization code start **********************

//********** step3 varible intialization code start **********************
	$contribution_type = isset($_POST['contribution_type'])?$_POST['contribution_type']:array();
	$contribution_value = isset($_POST['contribution_value'])?$_POST['contribution_value']:array();
	$currentContribution = array();

//********** step3 varible intialization code start **********************

//********* step1 validation code end   ********************
	if ($submit_type!='auto_save' && $step >= 1) {
		$validate->string(array('required' => true, 'field' => 'open_enrollment_start', 'value' => $open_enrollment_start), array('required' => 'Open Application Start Date is required'));
		$validate->string(array('required' => true, 'field' => 'open_enrollment_end', 'value' => $open_enrollment_end), array('required' => 'Open Application End Date is required'));
		$validate->string(array('required' => true, 'field' => 'first_coverage_date', 'value' => $first_coverage_date), array('required' => 'First Plan Date is required'));
		
		$validate->string(array('required' => true, 'field' => 'waiting_restriction_on_open_enrollment', 'value' => $waiting_restriction_on_open_enrollment), array('required' => 'Select any option'));
		$validate->string(array('required' => true, 'field' => 'allow_future_effective_date', 'value' => $allow_future_effective_date), array('required' => 'Select any option'));

		if($allow_future_effective_date =='Y'){
			$validate->string(array('required' => true, 'field' => 'allowed_range', 'value' => $allowed_range), array('required' => 'Select any option'));
		}
		if(!empty($open_enrollment_start)){
		    $check_open_enrollment_start=validateDate($open_enrollment_start,"m/d/Y");
		    if(!$check_open_enrollment_start){
		      $validate->setError("open_enrollment_start","Enter Valid Date");
		    }
	  	}
	  	if(!empty($open_enrollment_end)){
		    $check_open_enrollment_end=validateDate($open_enrollment_end,"m/d/Y");
		    if(!$check_open_enrollment_end){
		      $validate->setError("open_enrollment_end","Enter Valid Date");
		    }
	  	}
	  	if(!empty($first_coverage_date)){
		    $check_first_coverage_date=validateDate($first_coverage_date,"m/d/Y");
		    if(!$check_first_coverage_date){
		      $validate->setError("first_coverage_date","Enter Valid Date");
		    }
	  	}

	  	if(strtotime($open_enrollment_start) >= strtotime($open_enrollment_end)){
		    $validate->setError("open_enrollment_start","Enter Valid Date");
	  	}

	  	$sqlCoverage = "SELECT id,coverage_period_start,coverage_period_end FROM group_coverage_period WHERE id=:id";
	  	$resCoverage = $pdo->selectOne($sqlCoverage,array(":id"=>$group_coverage_period_id));


	  	if(!empty($resCoverage)){
	  		$coverage_period_start = date('m/d/Y',strtotime($resCoverage['coverage_period_start']));
	  		$coverage_period_end = date('m/d/Y',strtotime($resCoverage['coverage_period_end']));
	  		
	  		if((strtotime($first_coverage_date) >= strtotime($coverage_period_end)) || (strtotime($first_coverage_date) < strtotime($coverage_period_start))){
			    $validate->setError("first_coverage_date","Enter Valid Date ");
		  	}
	  	}

	  	

		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "settings_tab";
		}
	}
//********* step1 validation code end   ********************

//********* step2 validation code end   ********************
	if ($submit_type!='auto_save' && $step >= 2) {
		if(empty($products)){
			$validate->setError("products","Please Select Product");
		}
		$validate->string(array('required' => true, 'field' => 'is_contribution', 'value' => $is_contribution), array('required' => 'Select any option'));

		if($is_contribution == 'Y'){
			$validate->string(array('required' => true, 'field' => 'display_contribution_on_enrollment', 'value' => $display_contribution_on_enrollment), array('required' => 'Select any option'));
		}else{
			$is_rule_set =false;
			if(!empty($products)){
				foreach ($products as $key => $value) {
					$sqlCheckRule="SELECT gcr.id FROM group_contribution_rule gcr
					JOIN group_contribution_setting gcs ON (gcr.id = gcs.group_contribution_rule_id AND gcs.is_deleted='N')
					WHERE gcr.is_deleted='N' AND FIND_IN_SET(:products,gcs.products) AND gcr.rule_type='Global'";
					$resCheckRule=$pdo->select($sqlCheckRule,array(":products"=>$value));

					if(!empty($resCheckRule)){
						$is_rule_set = true;
					}

					$sqlCheckRule="SELECT gcr.id FROM group_contribution_rule gcr
					JOIN group_contribution_setting gcs ON (gcr.id = gcs.group_contribution_rule_id AND gcs.is_deleted='N')
					WHERE gcr.is_deleted='N' AND FIND_IN_SET(:products,gcs.products) AND gcr.rule_type='Variation' AND gcr.group_id=:group_id";
					$resCheckRule=$pdo->select($sqlCheckRule,array(":products"=>$value,":group_id"=>$group_id));

					if(!empty($resCheckRule)){
						$is_rule_set = true;
					}
				}
			}

			if($is_rule_set){
				$validate->setError("is_contribution","Minimum Contribution is set");
			}
		}

		
		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "products_tab";
		}
	}
//********* step2 validation code end   ********************

//********* step3 validation code end   ********************
	if ($submit_type!='auto_save' && $step >= 3) {
		$panel_error = array();
		$tmpProductArr = array();

		if($is_contribution == 'Y'){
			if(empty($contribution_type)){
				$validate->setError("general_contribution_error","Please Select Contribution");
			}

			if(empty($contribution_value)){
				$validate->setError("general_contribution_error","Please Add Contribution");
			}else{
				$tmp_is_set = false;

				foreach ($contribution_value as $product_key => $value) {
					if(!empty($contribution_type[$product_key])){
						$tmp_is_set = true;
					}

					if(empty($contribution_type[$product_key]) && $tmp_is_set){
						unset($contribution_value[$product_key]);
					}
				}
				foreach ($contribution_value as $product_key => $value) {
					array_push($tmpProductArr, $product_key);
					if(empty($contribution_type[$product_key])){
						$validate->setError("contribution_type_".$product_key,"Select Contribution By");
						if(!in_array($product_key, $panel_error)){
							array_push($panel_error, $product_key);
						}
					}
					foreach ($value as $matrix_key => $contribution) {
						$validate->string(array('required' => true, 'field' => 'contribution_value_'.$product_key.'_'.$matrix_key, 'value' => $contribution), array('required' => 'Enter Contribution'));

						if(!$validate->getError('contribution_value_'.$product_key.'_'.$matrix_key) && !$validate->getError('contribution_type_'.$product_key)){
							$tmpAmount = $contribution;
							$tmpType = $contribution_type[$product_key];

							$sqlCheckRule="SELECT gcr.id,gcs.contribution_type,gcs.contribution,gcs.percentage_calculate_by FROM group_contribution_rule gcr
							JOIN group_contribution_setting gcs ON (gcr.id = gcs.group_contribution_rule_id AND gcs.is_deleted='N')
							WHERE gcr.is_deleted='N' AND FIND_IN_SET(:products,gcs.products) AND gcr.rule_type='Variation' AND gcr.group_id=:group_id";
							$resCheckRule=$pdo->select($sqlCheckRule,array(":products"=>$product_key,":group_id"=>$group_id));

							if(!empty($resCheckRule)){
								foreach ($resCheckRule as $tmpKey => $tmpValue) {
									if(($tmpValue['contribution_type']=='Percentage' && $tmpType == 'Percentage') || ($tmpValue['contribution_type']=='Fixed' && $tmpType == 'Amount')){
										if($tmpAmount < $tmpValue['contribution']){
											$validate->setError('contribution_value_'.$product_key.'_'.$matrix_key,' minimum contribution : '.round($tmpValue['contribution'],2));
										}
									}else{
										if($tmpValue['percentage_calculate_by'] == 'member_only_tier_apply_to_all'){
											$sqlPrice="SELECT price FROM prd_matrix where id=:id AND plan_type=1";
											$resPrice=$pdo->selectOne($sqlPrice,array(":id"=>$matrix_key));
											if(empty($resPrice)){
												$sqlPrice="SELECT pm.product_id,pm.pricing_model,pm.price FROM prd_matrix pm JOIN (SELECT id,product_id,matrix_group from prd_matrix where id=:id) AS res ON(res.product_id=pm.product_id AND res.matrix_group=pm.matrix_group AND IF(pm.plan_type=1,pm.plan_type=1,1))";
												$resPrice=$pdo->selectOne($sqlPrice,array(":id"=>$matrix_key));
												if(!empty($resPrice['pricing_model']) && in_array($resPrice['pricing_model'],array('VariableEnrollee','VariablePrice')) ){
													$tmpResMCplan = $pdo->selectOne("SELECT * from prd_matrix_criteria where prd_matrix_id=:matrix_id AND is_deleted='N'",array(":matrix_id"=>$matrix_key));
													if(!empty($tmpResMCplan['id'])){
														unset($tmpResMCplan['id']);
														unset($tmpResMCplan['created_at']);
														unset($tmpResMCplan['updated_at']);
														unset($tmpResMCplan['is_deleted']);
														unset($tmpResMCplan['prd_matrix_id']);
														$tmp_mincr = $tmp_incr = '';
														$tmp_sch_param = array();
														foreach($tmpResMCplan as $tkey=> $tval){
															$tmp_incr.=" AND $tkey=:$tkey";
															$tmp_sch_param[":".$tkey] = $tval;
														}
														if($resPrice['pricing_model'] == 'VariableEnrollee'){
															$tmp_mincr =" AND enrollee_type='Primary'";
														}else{
															$tmp_mincr =" AND plan_type=1";
														}
														$tmpResMCMember = $pdo->selectOne("SELECT price FROM prd_matrix where id IN(SELECT prd_matrix_id from prd_matrix_criteria where is_deleted='N' $tmp_incr) and is_deleted='N' $tmp_mincr ",$tmp_sch_param);
														if(!empty($tmpResMCMember['price'])){
															$resPrice = $tmpResMCMember;
														}
													}
												}
											}
										}else{
											$sqlPrice="SELECT price FROM prd_matrix where id=:id";
											$resPrice=$pdo->selectOne($sqlPrice,array(":id"=>$matrix_key));
										}

										if(!empty($resPrice)){
											if($tmpValue['contribution_type']=='Percentage'){
												$tmpFixed = $tmpValue['contribution'] * $resPrice['price'] / 100;
												$tmpFixed2 = $tmpAmount;
											}else if($tmpType == 'Percentage'){
												$tmpFixed2 = $tmpAmount * $resPrice['price'] / 100;
												$tmpFixed = $tmpValue['contribution'];
											}

											if($tmpFixed2 < $tmpFixed){
												$validate->setError('contribution_value_'.$product_key.'_'.$matrix_key,'minimum contribution : $'.round($tmpFixed,2));
											}
										}
									}
								}
							}else{
								$sqlCheckRule="SELECT gcr.id,gcs.contribution_type,gcs.contribution,gcs.percentage_calculate_by FROM group_contribution_rule gcr
								JOIN group_contribution_setting gcs ON (gcr.id = gcs.group_contribution_rule_id AND gcs.is_deleted='N')
								WHERE gcr.is_deleted='N' AND FIND_IN_SET(:products,gcs.products) AND gcr.rule_type='Global'";
								$resCheckRule=$pdo->select($sqlCheckRule,array(":products"=>$product_key));

								if(!empty($resCheckRule)){
									foreach ($resCheckRule as $tmpKey => $tmpValue) {
										if(($tmpValue['contribution_type']=='Percentage' && $tmpType == 'Percentage') || ($tmpValue['contribution_type']=='Fixed' && $tmpType == 'Amount')){
											if($tmpAmount < $tmpValue['contribution']){
												$validate->setError('contribution_value_'.$product_key.'_'.$matrix_key,'minimum contribution : '.round($tmpValue['contribution'],2));
											}
										}else{
											if($tmpValue['percentage_calculate_by'] == 'member_only_tier_apply_to_all'){
												$sqlPrice="SELECT price FROM prd_matrix where id=:id AND plan_type=1";
												$resPrice=$pdo->selectOne($sqlPrice,array(":id"=>$matrix_key));
												if(empty($resPrice)){
													$sqlPrice="SELECT pm.product_id,pm.pricing_model,pm.price FROM prd_matrix pm JOIN (SELECT id,product_id,matrix_group from prd_matrix where id=:id) AS res ON(res.product_id=pm.product_id AND res.matrix_group=pm.matrix_group AND IF(pm.plan_type=1,pm.plan_type=1,1))";
													$resPrice=$pdo->selectOne($sqlPrice,array(":id"=>$matrix_key));
													if(!empty($resPrice['pricing_model']) && in_array($resPrice['pricing_model'],array('VariableEnrollee','VariablePrice')) ){
														$tmpResMCplan = $pdo->selectOne("SELECT * from prd_matrix_criteria where prd_matrix_id=:matrix_id AND is_deleted='N'",array(":matrix_id"=>$matrix_key));
														if(!empty($tmpResMCplan['id'])){
															unset($tmpResMCplan['id']);
															unset($tmpResMCplan['created_at']);
															unset($tmpResMCplan['updated_at']);
															unset($tmpResMCplan['is_deleted']);
															unset($tmpResMCplan['prd_matrix_id']);
															$tmp_mincr = $tmp_incr = '';
															$tmp_sch_param = array();
															foreach($tmpResMCplan as $tkey=> $tval){
																$tmp_incr.=" AND $tkey=:$tkey";
																$tmp_sch_param[":".$tkey] = $tval;
															}
															if($resPrice['pricing_model'] == 'VariableEnrollee'){
																$tmp_mincr =" AND enrollee_type='Primary'";
															}else{
																$tmp_mincr =" AND plan_type=1";
															}
															$tmpResMCMember = $pdo->selectOne("SELECT price FROM prd_matrix where id IN(SELECT prd_matrix_id from prd_matrix_criteria where is_deleted='N' $tmp_incr) and is_deleted='N' $tmp_mincr ",$tmp_sch_param);
															if(!empty($tmpResMCMember['price'])){
																$resPrice = $tmpResMCMember;
															}
														}
													}
												}
											}else{
												$sqlPrice="SELECT price FROM prd_matrix where id=:id";
												$resPrice=$pdo->selectOne($sqlPrice,array(":id"=>$matrix_key));
											}
											
											if(!empty($resPrice)){
												if($tmpValue['contribution_type']=='Percentage'){
													$tmpFixed = $tmpValue['contribution'] * $resPrice['price'] / 100;
													$tmpFixed2 = $tmpAmount;
												}else if($tmpType == 'Percentage'){
													$tmpFixed2 = $tmpAmount * $resPrice['price'] / 100;
													$tmpFixed = $tmpValue['contribution'];
												}

												if($tmpFixed2 < $tmpFixed){
													$validate->setError('contribution_value_'.$product_key.'_'.$matrix_key,'minimum contribution : $'.round($tmpFixed,2));
												}
											}
										}
									}
								}
							}

						}

						if($validate->getError('contribution_value_'.$product_key.'_'.$matrix_key)){
							if(!in_array($product_key, $panel_error)){
								array_push($panel_error, $product_key);
							}
						}
						$currentContribution[$product_key."_".$matrix_key]=0;
					}
				}
			}
		}
		$response['panel_error'] = $panel_error;
		$response['success_panel'] = array_diff($tmpProductArr,$panel_error);
		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "contributions_tab";
		}
	}
//********* step3 validation code end   ********************


if ($validate->isValid()) {
	if ($step >= 1) {
	  	$response['status']="success";
  	}
  	if($step >=2){
		$response['status']="success";
  	}
  	if ($submit_type!='auto_save' && $submit_type!= 'next_panel' && $step == 3) {
		$contribution_arr = $coverage_period_arr = $activity_feed_offering_period = array();
		$insertActivity = false;
		$params = array(
			'group_id'=>$group_id,
			'group_coverage_period_id'=>$group_coverage_period_id,
			'class_id'=>$class_id,
			'open_enrollment_start'=>date('Y-m-d',strtotime($open_enrollment_start)),
			'open_enrollment_end'=>date('Y-m-d',strtotime($open_enrollment_end)),
			'first_coverage_date'=>date('Y-m-d',strtotime($first_coverage_date)),
			'waiting_restriction_on_open_enrollment'=>$waiting_restriction_on_open_enrollment,
			'allow_future_effective_date'=>$allow_future_effective_date,
			'allowed_range'=>0,
			'products'=>implode(",", $products),
			'is_contribution'=>$is_contribution,
			'display_contribution_on_enrollment'=>'N',
		);
		if($allow_future_effective_date == 'Y'){
			$params['allowed_range']=$allowed_range;
		}
		if($is_contribution == 'Y'){
			$params['display_contribution_on_enrollment']=$display_contribution_on_enrollment;
		}

		if(!empty($offering_id)){
			$upd_where = array(
		        'clause' => 'id = :id',
		        'params' => array(
		          ':id' => $offering_id,
		        ),
			  );
			$coverage_period_arr = $params;
			$activity_feed_offering_period['coverage_period'] = $pdo->update('group_coverage_period_offering',$params,$upd_where,true);
		}else{
			$params['status']='Inactive';
			$coverage_period_arr = $params;
			$offering_id = $pdo->insert("group_coverage_period_offering",$params);
			$insertActivity = true;
			// $group_coverage_period_id = $offering_id;
		}

		$sqlCheck = "SELECT * FROM group_coverage_period_contributions WHERE group_coverage_period_offering_id=:id AND is_deleted='N' AND group_coverage_period_id=:coverage_id AND group_id=:group_id";
		$whrCheck = array(
			":group_id"=>$group_id,
			":id"=>$offering_id,
			":coverage_id"=>$group_coverage_period_id,
		);
		$resCheck = $pdo->select($sqlCheck,$whrCheck);

		$dbContribution = array();

		if(!empty($resCheck)){
			foreach ($resCheck as $key => $value) {
				$dbContribution[$value['product_id']."_".$value['plan_id']]=$value['id'];
			}
		}

		$dbContributionResult=array_diff_key($dbContribution,$currentContribution);

		if(!empty($dbContributionResult)){
			foreach ($dbContributionResult as $key => $value) {
				$keyDiff = explode("_", $key);
				$tmpProductID = $keyDiff[0];
				$tmpPlanID = $keyDiff[1];

				$updateZipParams=array(
					'is_deleted'=>'Y'
				);
				$updateZipWhere=array(
					'clause'=>'group_coverage_period_offering_id=:id AND group_coverage_period_id=:coverage_id AND product_id=:product_id AND plan_id=:plan_id AND group_id=:group_id',
					'params'=>array(
						":group_id"=>$group_id,
						":id"=>$offering_id,
						":coverage_id"=>$group_coverage_period_id,
						":product_id"=>$tmpProductID,
						":plan_id"=>$tmpPlanID,
					)
				);
				$pdo->update("group_coverage_period_contributions",$updateZipParams,$updateZipWhere);
			}
		}

		foreach ($contribution_value as $product_key => $value) {
			foreach ($value as $matrix_key => $contribution) {
				$sqlCheck = "SELECT id FROM group_coverage_period_contributions WHERE group_coverage_period_offering_id=:id AND is_deleted='N' AND group_coverage_period_id=:coverage_id AND product_id=:product_id AND plan_id=:plan_id AND group_id=:group_id";
				$whrCheck = array(
					":group_id"=>$group_id,
					":id"=>$offering_id,
					":coverage_id"=>$group_coverage_period_id,
					":product_id"=>$product_key,
					":plan_id"=>$matrix_key,
				);
				$resCheck = $pdo->selectOne($sqlCheck,$whrCheck);
				$params = array(
					'group_id'=>$group_id,
					'group_coverage_period_id'=>$group_coverage_period_id,
					'group_coverage_period_offering_id'=>$offering_id,
					'class_id'=>$class_id,
					'product_id'=>$product_key,
					'plan_id'=>$matrix_key,
					'type'=>($is_contribution == 'Y' ? $contribution_type[$product_key] : 'Percentage'),
					'con_value'=>($is_contribution == 'Y' ? $contribution : 0),
				);
				if(!empty($resCheck)){
					$upd_where = array(
				        'clause' => 'id = :id',
				        'params' => array(
				          ':id' => $resCheck['id'],
				        ),
					  );
					$contribution_arr[$product_key][$matrix_key] = $params;
			      	$activity_feed_offering_period['conribution'][$product_key][$matrix_key] = $pdo->update('group_coverage_period_contributions',$params,$upd_where,true);
				}else{
					$contribution_id = $pdo->insert("group_coverage_period_contributions",$params);
				}
			}
		}

		$description_arr = array();
		$entity_action ='';
		$inesrtActivity = false;
		$customKey = array(
			"con_value" => 'Contribution Value ',
			"type" => 'Type '
		);
		
		$description_arr['ac_message'] =array(
			'ac_red_1'=>array(
			  'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
			  'title'=>$_SESSION['groups']['rep_id'],
			)
		);
		$coverage_display_id = getname('group_coverage_period',$group_coverage_period_id,'display_id','id');
		if(!empty($activity_feed_offering_period)){
			$description_arr['ac_message']['ac_message_1']=' updated offering Period ';
			$description_arr['ac_message']['ac_red_2']['title']= date('m/d/Y',strtotime($coverage_period_arr['open_enrollment_start'])) . ' - '.date('m/d/Y',strtotime($coverage_period_arr['open_enrollment_end']));
			$description_arr['ac_message']['ac_red_3']['href'] = $GROUP_HOST.'/add_coverage_periods.php?coverage='.md5($group_coverage_period_id);
			$description_arr['ac_message']['ac_red_3']['title'] = $coverage_display_id;
			$entity_action = 'Group Update Offering Plan Period';
			
			foreach($activity_feed_offering_period as $type => $arr){
				if($type == 'coverage_period'){
					if(!empty($arr) && is_array($arr)){
						foreach($arr as $key2 => $val){
							if(array_key_exists($key2,$coverage_period_arr) && !in_array($key2,array('products','class_id'))){
								$description_arr['key_value']['desc_arr'][$key2] = ' Updated From '.$val." To ".$coverage_period_arr[$key2].".<br>";
								$inesrtActivity = true;
								// pre_print("Ok",false);
							} else if($key2 == 'products'){
								$deleted_product = array_unique(array_diff(explode(',',$val),$products));
								$inserted_product = array_unique(array_diff($products,explode(',',$val)));
								if(!empty($inserted_product[0])){
									$insProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(product_code,' ',name)  SEPARATOR ' ,') as products from prd_main where ID IN(".implode(',',$inserted_product).")");
									if(!empty($insProd['products'])){
										$description_arr['inserted_product_desc'] = "Inserted Products : <br>".$insProd['products'];
										$inesrtActivity = true;
									}
								}
								if(!empty($deleted_product[0])){
									$delProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(product_code,' ',name) SEPARATOR ' ,') as products from prd_main where ID IN(".implode(',',$deleted_product).")");
									if(!empty($delProd['products'])){
										$description_arr['deleted_product_desc'] = "Deleted Products : <br>".$delProd['products'];
										$inesrtActivity = true;
									}
								}
							}elseif($key2 == 'class_id'){
								$vincr = '';
								if(!empty($val) && $coverage_period_arr[$key2]){
									$vincr = $val.','.$coverage_period_arr[$key2];
								}
								$value_f = 'Blank';
								$value_t = 'Blank';
								if($vincr!=''){
									$valueArr = $pdo->select("SELECT class_name,id from group_classes WHERE id IN(".$vincr.") LIMIT 2");
									foreach($valueArr as $vl){
										if($vl['id'] == $val){
											$value_f = $vl['class_name'];
										}else if($vl['id'] == $coverage_period_arr['class_id']){
											$value_t = $vl['class_name'];
										}
									}
								}
								$description_arr['customer_settings_desc_class'] = ' Class updated from '.$value_f.' to '.$value_t;
								$inesrtActivity = true;
							}else{
								$description_arr['description2'][] = ucwords(str_replace('_',' ',$val));
								$inesrtActivity = true;
							}
						}
					}
				}else if($type == 'conribution'){
					$product = $exist_pro =  array();
					$i=1;
					foreach($arr as $product_id => $matrix_key){
						if(!empty($matrix_key)){
							if(!in_array($product_id,$exist_pro)){
								$product[$product_id] = $pdo->selectOne("SELECT product_code,name,id from prd_main where id=:id AND is_deleted='N'",array(":id"=>$product_id));
								array_push($exist_pro,$product_id);
							}
							if(!empty($product[$product_id]['id'])){
								$existPlan = array();
								$plan_id = '';
								
								foreach($matrix_key as $key => $value){
									
									if(!in_array($key,$existPlan)){
										$plan_id = getname('prd_matrix',$key,'plan_type','id');
										array_push($existPlan,$key);
									}
									if(!empty($value)){
										foreach($value as $k => $v){
											if($k == 'class_id'){
												continue;
											}
											if(!empty($product[$product_id]['id']) && $i==1){
												$description_arr['key_value']['desc_arr']['Products'] = '<br>';
											}
											if(!empty($product[$product_id]['id'])){
												$description_arr['key_value']['desc_arr']['<b>'.$product[$product_id]['name'].'('.$product[$product_id]['product_code'].')</b>'] = ' <br>';
											}
											$ck = isset($customKey[$k]) ? $customKey[$k] : $k;
											$description_arr['key_value']['desc_arr'][$prdPlanTypeArray[$plan_id]['title'].' '.$ck.'_'.$i] = ' From '.$v.' to '.$contribution_arr[$product_id][$key][$k];
											$inesrtActivity = true;
											$i++;
										}
									}
								}
								
							}
						}

					}
				}
			}
		}else if($insertActivity){
			$description_arr['ac_message']['ac_message_1']=' Created offering Period ';
			$description_arr['ac_message']['ac_red_2']['title']= date('m/d/Y',strtotime($coverage_period_arr['open_enrollment_start'])) . ' - '.date('m/d/Y',strtotime($coverage_period_arr['open_enrollment_end']));
			$description_arr['ac_message']['ac_red_3']['href'] = $GROUP_HOST.'/add_coverage_periods.php?coverage='.md5($group_coverage_period_id);
			$description_arr['ac_message']['ac_red_3']['title'] = $coverage_display_id;
			$entity_action = 'Group Created Offering Plan Period';
			$inesrtActivity = true;
		}

		if($inesrtActivity){
			$desc=json_encode($description_arr);
			activity_feed(3,$_SESSION['groups']['id'], 'Group' , $group_coverage_period_id, 'group_coverage_period', $entity_action,($_SESSION['groups']['fname'].' '.$_SESSION['groups']['lname']),"",$desc);
		}
		if($submit_type == 'continue'){
			$response['status'] = 'offering_added';
		}else{
			$response['status'] = 'success';
		}
  		
  	}
}else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
	$response['div_step_error'] = $div_step_error;
}

header('Content-type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>