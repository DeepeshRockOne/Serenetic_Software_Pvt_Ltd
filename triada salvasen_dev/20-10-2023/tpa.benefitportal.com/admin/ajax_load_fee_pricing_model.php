<?php
include_once 'layout/start.inc.php';

	$res =array();
	$products = checkIsset($_POST['products'],'arr');
	$fee_method = checkIsset($_POST['fee_method']);
	$isPmPmComm = checkIsset($_POST['isPmPmComm']);
	$fee_id = !empty($_POST['fee_id']) ? $_POST['fee_id'] : 0;

	if($isPmPmComm == "Y"){
		$pmpm_fee_id = !empty($_POST['pmpm_fee_id']) ? $_POST['pmpm_fee_id'] : 0;
		$fee_method = checkIsset($_POST['fee_type']);
	}

	if(!empty($products)){

		$prdIds = is_array($products) ? implode(",", $products) : $products;
		$getPriceModel = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(pricing_model)) as priceModel FROM prd_main WHERE id IN(".$prdIds.")");
		$pricingModelArr = !empty($getPriceModel['priceModel']) ? explode(",", $getPriceModel['priceModel']) : array();

		// updated to PMPM set on fixedPricing
		if($isPmPmComm == "Y"){
			$pricingModelArr = array();
			$pricingModelArr[0] = "FixedPrice";
		}
	
		if(!empty($pricingModelArr)){
			if(count($pricingModelArr) > 1){
				echo "<p class='error'>Select product with same pricing model</p.";
			}else{
				$pricing_model = $pricingModelArr[0];

				if($pricing_model=="FixedPrice"){
					$getProductPlans = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(plan_type)) as prdPlan FROM prd_matrix WHERE product_id IN(".$prdIds.") ORDER BY plan_type ASC");
					$prdPlansArr = !empty($getProductPlans['prdPlan']) ? explode(",", $getProductPlans['prdPlan']) : array();

					if(empty($prdPlansArr) && $isPmPmComm == "Y"){
						$getProductPlans = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(id)) as prdPlan FROM prd_plan_type ORDER BY id ASC");
						$prdPlansArr = !empty($getProductPlans['prdPlan']) ? explode(",", $getProductPlans['prdPlan']) : array();
					}


			        $priceArr = array();
			        if(!empty($fee_id)){
						$feePlanSql = "SELECT id,product_id,plan_type,price_calculated_on,
	                        price_calculated_type,price,is_deleted 
	                        FROM  prd_matrix 
	                        WHERE is_deleted='N' AND md5(product_id) = :product_id";
				        $feePlanRow = $pdo->select($feePlanSql, array(":product_id" => $fee_id));

				        if(!empty($feePlanRow)){
				            foreach ($feePlanRow as $key => $value) {
				            	$fee_method=$value['price_calculated_on'];
				                $percentage_type=$value['price_calculated_type'];
				                if($value['plan_type']>0){ 
			                        $priceArr[$value['plan_type']]=$value['price'];
				                }else{
			                        $fee_price=$value['price'];
				                }
				            }
				        }
			        }
			        if(!empty($pmpm_fee_id)){
						$feePlanSql = "SELECT pcr.id,pcr.amount_calculated_on as price_calculated_on,pcr.fee_per_calculate_on as price_calculated_type,pt.plan_type,pt.amount as price
	                        FROM  pmpm_commission_rule pcr
	                        JOIN pmpm_commission_rule_plan_type pt ON(pcr.id=pt.rule_id AND pt.is_deleted='N')
	                        WHERE pcr.is_deleted='N' AND pcr.id = :fee_id GROUP BY pt.plan_type ORDER BY pt.plan_type";
				        $feePlanRow = $pdo->select($feePlanSql, array(":fee_id" => $pmpm_fee_id));
				        if(!empty($feePlanRow)){
				            foreach ($feePlanRow as $key => $value) {
				            	$fee_method=$value['price_calculated_on'];
				                $percentage_type=$value['price_calculated_type'];

				                if($value['plan_type']>0){ 
			                        $priceArr[$value['plan_type']]=$value['price'];
				                }else{
			                        $fee_price=$value['price'];
				                }
				            }
				        }
			        }
			        
					include_once "tmpl/fixed_pricing_module.inc.php";
				}else{

					if($isPmPmComm != "Y"){
						$feeIncr = '';
						$feeParams = array();

						if(isset($fee_id)){
							$feeIncr .= " AND md5(pfm.fee_product_id) = :fee_id";
							$feeParams[':fee_id'] = $fee_id;
						}

						$priceSql = "SELECT pm.enrollee_type,pm.plan_type,pm.price,pm.non_commission_amount,pm.commission_amount,pm.payment_type,pm.pricing_model,pm.pricing_effective_date,pm.pricing_termination_date,pm.is_new_price_on_renewal,pm.matrix_group,prdMat.price,prdMat.id as feeMatrixId,p.name as product_name,p.product_code,pmc.*
						FROM prd_matrix pm
						JOIN prd_main p ON (p.id=pm.product_id)
						LEFT JOIN prd_plan_type ppt ON(pm.plan_type=ppt.id)
						JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.is_deleted='N')
						LEFT JOIN prd_fee_pricing_model pfm ON(pm.id=pfm.prd_matrix_id AND pfm.is_deleted='N' $feeIncr)
						LEFT JOIN prd_matrix prdMat ON(prdMat.id=pfm.prd_matrix_fee_id AND prdMat.is_deleted='N')
						WHERE pm.is_deleted='N' and pm.product_id IN(".$prdIds.") GROUP BY pm.matrix_group ORDER BY ppt.order_by,pmc.age_from,pmc.tobacco_status";
						$priceRes = $pdo->select($priceSql,$feeParams);

					}else{
						$pmpmIncr = '';
						$pmpmParams = array();

						if(isset($pmpm_fee_id)){
							$pmpmIncr .= " AND pt.rule_id = :rule_id";
							$pmpmParams[':rule_id'] = $pmpm_fee_id;
						}

						$priceSql = "SELECT pm.enrollee_type,pm.plan_type,pm.price,pm.non_commission_amount,pm.commission_amount,pm.payment_type,pm.pricing_model,pm.pricing_effective_date,pm.pricing_termination_date,pm.is_new_price_on_renewal,pm.matrix_group,pt.amount as price,pt.id as feeMatrixId,p.name as product_name,p.product_code,pmc.*
						FROM prd_matrix pm
						JOIN prd_main p ON (p.id=pm.product_id)
						LEFT JOIN prd_plan_type ppt ON(pm.plan_type=ppt.id)
						JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.is_deleted='N')
						LEFT JOIN pmpm_commission_rule_plan_type pt ON(pm.id=pt.prd_matrix_id AND pt.is_deleted='N' $pmpmIncr)
						WHERE pm.is_deleted='N' and pm.product_id IN(".$prdIds.") ORDER BY ppt.order_by,pmc.age_from,pmc.tobacco_status";
						$priceRes = $pdo->select($priceSql,$pmpmParams);
					}
				
					if(!empty($priceRes)){
						foreach ($priceRes as $key => $rows) {
								$matrix_group = $rows['matrix_group'];
								$priceResArr[$matrix_group] = array(
									'id'=>$rows['prd_matrix_id'],
									'feeMatrixId'=>$rows['feeMatrixId'],
									'product_detail'=>' ('.$rows['product_name'].') ('.$rows['product_code'].')',
									//'product_detail'=>'',
									'price'=>$rows['price'],
									'matrixPlanType'=>$rows['plan_type'],
									'enrolleeMatrix'=>$rows['enrollee_type'],
									'1'=>array("matrix_value"=>(isset($rows['age_from']) && isset($rows['age_to'])) ?$rows['age_from']." To ".$rows['age_to'] : '',
												"age_from"=>$rows['age_from'],
												"age_to"=>$rows['age_to']
											),
									'2'=>array("matrix_value"=>$rows['state']),
									'3'=>array("matrix_value"=>$rows['zipcode']),
									'4'=>array("matrix_value"=>$rows['gender']),
									'5'=>array("matrix_value"=>$rows['smoking_status']),
									'6'=>array("matrix_value"=>$rows['tobacco_status']),
									'7'=>array("matrix_value"=>$rows['height_feet']."Ft ".$rows['height_inch']."In" .($rows['height_by']=="Range" ? " To ".$rows['height_feet_to']."Ft ".$rows['height_inch_to']."In" : ''),
												"height_by"=>$rows['height_by'],
												"height_feet"=>$rows['height_feet'],
												"height_inch"=>$rows['height_inch'],
												"height_feet_to"=>$rows['height_feet_to'],
												"height_inch_to"=>$rows['height_inch_to'],
											),
									'8'=>array("matrix_value"=>$rows['weight'].($rows['weight_by']=="Range" ? " To ".$rows['weight_to'] : ''),
												"weight_by"=>$rows['weight_by'],
												"weight"=>$rows['weight'],
												"weight_to"=>$rows['weight_to'],
											),
									'9'=>array(
										"matrix_value"=>$rows['no_of_children'] .($rows['no_of_children_by']=="Range" ? " To ".$rows['no_of_children_to'] : ''),
										"no_of_children_by"=>$rows['no_of_children_by'],
										"no_of_children"=>$rows['no_of_children'],
										"no_of_children_to"=>$rows['no_of_children_to'],
									),
									'10'=>array("matrix_value"=>$rows['has_spouse']),
									'11'=>array("matrix_value"=>$rows['spouse_age_from']." To ".$rows['spouse_age_to'],
												"spouse_age_from"=>$rows['spouse_age_from'],
												"spouse_age_to"=>$rows['spouse_age_to']
											),
									'12'=>array("matrix_value"=>$rows['spouse_gender']),
									'13'=>array("matrix_value"=>$rows['spouse_smoking_status']),
									'14'=>array("matrix_value"=>$rows['spouse_tobacco_status']),
									'15'=>array("matrix_value"=>$rows['spouse_height_feet']."Ft ".$rows['spouse_height_inch']."In",
												"spouse_height_feet"=>$rows['spouse_height_feet'],
												"spouse_height_inch"=>$rows['spouse_height_inch']
											),
									'16'=>array("matrix_value"=>$rows['spouse_weight']." ".$rows['spouse_weight_type'],
												"spouse_weight"=>$rows['spouse_weight'],
												"spouse_weight_type"=>$rows['spouse_weight_type']
											),
									'17'=>array("matrix_value"=>$rows['benefit_amount']),
								);
						}


						$sqlCheckPricingQue="SELECT assign_type,prd_pricing_question_id from prd_pricing_question_assigned WHERE is_deleted='N' AND product_id IN(".$prdIds.")";
						$resCheckPricingQue = $pdo->select($sqlCheckPricingQue);
					
						$price_control = array();
						$price_control_enrollee = array();
						$banded_rates_array = array();
						$enrolleeType = array();

						if(!empty($resCheckPricingQue)){
							foreach ($resCheckPricingQue as $key => $value) {
								if($pricing_model=="VariablePrice"){
									array_push($price_control, $value['prd_pricing_question_id']);
								}else{
									if(!array_key_exists($value['assign_type'], $price_control_enrollee)){
										$price_control_enrollee[$value['assign_type']]=array();
									}
									array_push($price_control_enrollee[$value['assign_type']], $value['prd_pricing_question_id']);

									if(!array_key_exists($value['prd_pricing_question_id'], $banded_rates_array)){
										$banded_rates_array[$value['prd_pricing_question_id']]=array();
									}
									array_push($banded_rates_array[$value['prd_pricing_question_id']], $value['assign_type']);
								}
								if(!in_array($value['assign_type'],$enrolleeType)){
									array_push($enrolleeType, $value['assign_type']);
								}
							}
						}

						if(!empty($price_control_enrollee)){
							foreach ($price_control_enrollee as $keyArr => $valueArr) {
								foreach ($valueArr as $key => $value) {
									array_push($price_control,$value);
								}
							}	
						}

						$fetch_rows = $priceResArr;
					
						$total_rows = count($fetch_rows);

						if($pricing_model == "VariablePrice"){
							include_once "tmpl/benefit_tier_pricing_module.inc.php";
						}else if($pricing_model == "VariableEnrollee"){
							include_once "tmpl/enrollee_pricing_module.inc.php";
						}
					}else{
						echo "<p class='error text-center' id='error_pricing_model'>Pricing matrix not found for selected product</p.";			
					}
				}
			}
		}else{
			echo "<p class='error text-center' id='error_pricing_model'>Pricing model not found for selected product</p.";
		}
	}else{
		echo "<p class='error text-center' id='error_pricing_model'>Please select product</p.";
	}
exit;
?>