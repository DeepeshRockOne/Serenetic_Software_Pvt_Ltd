<?php include_once dirname(__FILE__) . '/layout/start.inc.php';
error_reporting(E_ALL);
$response = array();

$products = !empty($_POST['products']) ? $_POST['products'] : array();
$is_contribution = !empty($_POST['is_contribution']) ? $_POST['is_contribution'] : '';
$tmp_offering_id = !empty($_POST['tmp_offering_id']) ? $_POST['tmp_offering_id'] : '';


$contributionTypeArray = array();
$contributionArray = array();
$added_id = array();

$sqlContribution = "SELECT * FROM group_coverage_period_contributions WHERE is_deleted='N' AND group_coverage_period_offering_id =:offering_id";
$resContribution = $pdo->select($sqlContribution,array(":offering_id"=>$tmp_offering_id));

if(!empty($resContribution)){
	foreach ($resContribution as $key => $value) {
		$contributionTypeArray[$value['product_id']]=$value['type'];
		$contributionArray[$value['product_id']][$value['plan_id']]=$value['con_value'];
		if(!in_array($value['product_id'], $added_id)){
			array_push($added_id, $value['product_id']);
		}
	}
}

if(!empty($products)){
	$product_list = implode(",", $products);
	$sqlProducts="SELECT id,name,product_code,pricing_model FROM prd_main where id in ($product_list)";
	$resProducts=$pdo->select($sqlProducts);
	$productCount = 1;
	$totalProducts = count($resProducts);
	?>
		<?php ob_start(); ?>
		<div class="is_contribution_Y_div" <?=$is_contribution == 'Y' ? '' : 'style="display:none"'?>>
		<p class="fw500">Set group contributions for each product offered (not required on all products):</p>
		<div class="coverage_panel_wrap">
		   <div class="panel-group" id="accordion">
				<?php foreach ($resProducts as $key => $value) { ?>
					<?php $pricing_model = $value['pricing_model']; ?>
			      	<?php /*<div class="panel panel-default completed cyan-panel"> */ ?>
			      	<div class="panel panel-default <?= !empty($contributionTypeArray) && isset($contributionTypeArray[$value['id']]) ? 'completed cyan-panel' : '' ?>" id="main_panel_id_<?=$value['id'] ?>">
			         	<div class="panel-heading">
				            <h4 class="panel-title">
				               <a data-toggle="collapse" data-parent="#accordion" href="#panel_id_<?=$value['id'] ?>" class="panel_expand" data-id="<?= $value['id'] ?>">
				               <?= $value['name'] ?></a>
				            </h4>
		         		</div>
			         	<div id="panel_id_<?=$value['id'] ?>" class="panel-collapse collapse product_panel" data-id="<?= $value['id'] ?>">
				            <div class="panel-body">
				               <div class="m-b-15">
				                  <label class="radio-inline">Set Contribution(s) by:</label>
				                  <label class="radio-inline"><input type="radio" name="contribution_type[<?= $value['id'] ?>]" value="Percentage" data-id="<?= $value['id'] ?>" class="contribution_type" <?= (empty($contributionTypeArray) || (isset($contributionTypeArray[$value['id']]) && $contributionTypeArray[$value['id']] == "Percentage"))  ? 'checked' : '' ?>> Percentage</label>
				                  <label class="radio-inline"><input type="radio" name="contribution_type[<?= $value['id'] ?>]" value="Amount" data-id="<?= $value['id'] ?>" class="contribution_type" <?= !empty($contributionTypeArray) && isset($contributionTypeArray[$value['id']]) && $contributionTypeArray[$value['id']] == "Amount"  ? 'checked' : '' ?>> Fixed Amount</label> 
				                  <p class="error" id="error_contribution_type_<?= $value['id'] ?>"></p>
				               </div>
				               <?php 
				               $priceResArr = array();
								if($pricing_model=="FixedPrice"){
									$sqlPrd="SELECT p.id AS prdID,p.name AS prdName,p.product_code AS prdCode,
						               	pm.id AS matrix_id,pm.matrix_group,pm.pricing_model,pm.enrollee_type,pm.plan_type,
						               	pm.price
						               	FROM prd_main p 
						               	JOIN prd_matrix pm ON (p.id=pm.product_id AND pm.is_deleted='N')
						               	WHERE p.id = :product_id";
						            $resPrd=$pdo->select($sqlPrd,array(":product_id"=>$value['id']));
						            if(!empty($resPrd)){
										foreach ($resPrd as $innKey => $rows) {
												$matrix_group = $rows['matrix_id'];
												$priceResArr[$matrix_group] = array(
													'id'=>$rows['matrix_id'],
													'feeMatrixId'=>0,
													'price'=>$rows['price'],
													'matrixPlanType'=>$rows['plan_type'],
													'enrolleeMatrix'=>$rows['enrollee_type'],
												);
										}
									}
								}else{
									$sqlPrd="SELECT p.id AS prdID,p.name AS prdName,p.product_code AS prdCode,
						               	pm.id AS matrix_id,pm.matrix_group,pm.pricing_model,pm.enrollee_type,pm.plan_type,
						               	pm.price,pmc.* 
						               	FROM prd_main p 
						               	JOIN prd_matrix pm ON (p.id=pm.product_id AND pm.is_deleted='N')
						               	LEFT JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.is_deleted='N')
						               	WHERE p.id = :product_id";
				               		$resPrd=$pdo->select($sqlPrd,array(":product_id"=>$value['id']));
				               		if(!empty($resPrd)){
										foreach ($resPrd as $innKey => $rows) {
												$matrix_group = $rows['matrix_group'];
												$priceResArr[$matrix_group] = array(
													'id'=>$rows['matrix_id'],
													'feeMatrixId'=>0,
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
													'18'=>array("matrix_value"=>$rows['in_patient_benefit']),
													'19'=>array("matrix_value"=>$rows['out_patient_benefit']),
													'20'=>array("matrix_value"=>$rows['monthly_income']),
													'21'=>array("matrix_value"=>$rows['benefit_percentage']),
												);
										}
									}
								}
								
								$sqlCheckPricingQue="SELECT assign_type,prd_pricing_question_id from prd_pricing_question_assigned WHERE is_deleted='N' AND product_id =:product_id";
								$resCheckPricingQue = $pdo->select($sqlCheckPricingQue,array(":product_id"=>$value['id']));
								$price_control = array();
								$price_control_enrollee = array();
								$enrolleeType = array();
								$banded_rates_array = array();

								if(!empty($resCheckPricingQue)){
									foreach ($resCheckPricingQue as $innKey => $rows) {
										if($pricing_model=="VariablePrice"){
											array_push($price_control, $rows['prd_pricing_question_id']);
										}else{
											if(!array_key_exists($rows['assign_type'], $price_control_enrollee)){
												$price_control_enrollee[$rows['assign_type']]=array();
											}
											array_push($price_control_enrollee[$rows['assign_type']], $rows['prd_pricing_question_id']);

											if(!array_key_exists($rows['prd_pricing_question_id'], $banded_rates_array)){
												$banded_rates_array[$rows['prd_pricing_question_id']]=array();
											}
											array_push($banded_rates_array[$rows['prd_pricing_question_id']], $rows['assign_type']);
										}
										if(!in_array($rows['assign_type'],$enrolleeType)){
											array_push($enrolleeType, $rows['assign_type']);
										}
									}
								}

								if(!empty($price_control_enrollee)){
									foreach ($price_control_enrollee as $keyArr => $valueArr) {
										foreach ($valueArr as $innKey => $rows) {
											array_push($price_control,$rows);
										}
									}	
								}
				               	?>
				               <div class="table-responsive">
				                  <table class="<?=$table_class?>">
				                     <thead>
				                        <tr>
				                        	<?php if($pricing_model == "VariableEnrollee"){ ?>
				                        		<th>Enrollee</th>
				                        	<?php }else{ ?>
				                        		<th>Benefit Tier</th>
				                        	<?php } ?>
				                           
				                           	<?php if(!empty($prdPricingQuestionRes)) { ?>
								                <?php foreach ($prdPricingQuestionRes as $innKey => $rows) { ?>
								                    <th class="<?= $rows['id'] ?>PriceRow" style="<?= !empty($price_control) && in_array($rows['id'], $price_control) ? '' : 'display: none' ?>"><?= $rows['display_label'] ?></th>
								                <?php }?>
								            <?php } ?>
				                           <th class="min-w185">Contribution Option</th>
				                           <th>Group Cost</th>
				                           <th>Member Cost</th>
				                           <th>Total</th>
				                        </tr>
				                     </thead>
				                     <tbody>
				                     	<?php if(!empty($priceResArr)) { ?>
				                     		<?php foreach ($priceResArr as $innKey => $rows) { ?>
				                     		<tr>
				                     			<?php if($pricing_model == "VariableEnrollee"){ ?>
				                     				<td><?= !empty($rows['enrolleeMatrix']) ? $rows['enrolleeMatrix'] : '-' ?></td>
			                     				<?php }else{ ?>
			                     					<td><?= !empty($rows['matrixPlanType']) ? $prdPlanTypeArray[$rows['matrixPlanType']]['title'] : '-' ?></td>
			                     				<?php } ?>

					                           	<?php if($pricing_model!="FixedPrice"){ ?>
					                        		<?php if(!empty($prdPricingQuestionRes)) { ?>
							                            <?php foreach ($prdPricingQuestionRes as $innKey => $innValue) { ?>
							                                <td class="<?= $innValue['id'] ?>PriceRowEnrollee" style="<?= !empty($price_control) && in_array($innValue['id'], $price_control) ? '' : 'display: none' ?>">
							                                    <?= !empty($rows[$innValue['id']]['matrix_value']) ?  ($innValue['control_class']=='text_amount' ? '$' : '').$rows[$innValue['id']]['matrix_value'] : '-' ?><?= $innValue['control_class']=='text_percentage' ? '%' : '' ?></td>
							                            <?php } ?>
							                        <?php } ?>
					                        	<?php } ?>
					                           <td>
					                              <div class="theme-form">
					                              	<div class="form-group height_auto mn">
					                                    <div class="input-group">
					                                      <div class="input-group-addon Amount_<?= $value['id'] ?>" style="<?= !empty($contributionTypeArray) && isset($contributionTypeArray[$value['id']]) && $contributionTypeArray[$value['id']] == "Amount"  ? '' : 'display: none' ?>"> $ </div>
					                                      <div class="pr">
					                                        <input type="text" class="form-control contribution_value contribution_value_<?= $value['id'] ?>" name="contribution_value[<?= $value['id'] ?>][<?= $rows['id'] ?>]" placeholder="0" data-price="<?= $rows['price'] ?>" id="contribution_value_<?= $value['id'] ?>_<?= $rows['id'] ?>" data-id="<?= $value['id'] ?>" data-matrix-id="<?= $rows['id'] ?>" onkeypress="return isNumberOnly(event)" value="<?= !empty($contributionArray) && isset($contributionArray[$value['id']][$rows['id']]) ? $contributionArray[$value['id']][$rows['id']] : '0' ?>">
					                                      </div>
					                                      <div class="input-group-addon Percentage_<?= $value['id'] ?>" style="<?= !empty($contributionTypeArray) && isset($contributionTypeArray[$value['id']]) && $contributionTypeArray[$value['id']] == "Percentage"  ? '' : 'display: none' ?>"> % </div>
					                                    </div>
					                                    <p class="error" id="error_contribution_value_<?= $value['id'] ?>_<?= $rows['id'] ?>"></p>
					                                  </div>
					                              </div>
					                           </td>
					                           <td>$<span id="group_cost_<?= $value['id'] ?>_<?= $rows['id'] ?>">0.00</span>/month</td>
					                           <td>$<span id="member_cost_<?= $value['id'] ?>_<?= $rows['id'] ?>">0.00</span>/month</td>
					                           <td>$<span id="total_cost_<?= $value['id'] ?>_<?= $rows['id'] ?>">0.00</span>/month</td>
					                        </tr>
					                    	<?php } ?>
			                     		<?php }else{ ?>
			                     		<?php } ?>
				                     </tbody>
				                  </table>
				               </div>
				               <div class="clearfix m-t-15">
				               	<?php if($productCount > 1) { ?>
				                  <div class="pull-left m-t-5">
				                     <a href="javascript:void(0);" class="btn red-link back_product" id="back_product_<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" >Back</a>
				                  </div>
				                <?php } ?>
				                <?php if($productCount < $totalProducts) { ?>
				                  <div class="pull-right">
				                     <a href="javascript:void(0);" class="btn btn-info next_product" id="next_product_<?= $value['id'] ?>" data-id="<?= $value['id'] ?>">Next Product</a>
				                  </div>
				                <?php } ?>
				               </div>
				            </div>
			         	</div>
			      	</div>
			      	<?php $productCount++; ?>
				<?php } ?>
			</div>
		</div>
		<p class="error" id="error_general_contribution_error"></p>
		</div>
		<p class="fw500" <?=$is_contribution == 'Y' ? 'style="display:none"' : ''?> >Group Contribution not selected</p>
		<?php 
		$html = ob_get_clean();
		$response['html']=$html;
		$response['status']='success';
		$response['added_id']=$added_id;
}else{
	$response['status']='fail';
}


echo json_encode($response);
dbConnectionClose();
exit;
?>