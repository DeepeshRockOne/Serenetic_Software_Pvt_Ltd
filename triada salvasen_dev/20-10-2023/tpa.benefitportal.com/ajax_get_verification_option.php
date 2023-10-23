<?php include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();
$response = array();

$sponsor_id = isset($_POST['sponsor_id'])?$_POST['sponsor_id']:"";
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";
$is_group_member = isset($_POST['is_group_member'])?$_POST['is_group_member']:"N";
$product_list = !empty($_POST['product_list'])? explode(",",$_POST['product_list']):array();
$product_matrix = isset($_POST['product_matrix'])?$_POST['product_matrix']:array();
$product_plan = isset($_POST['product_plan'])?$_POST['product_plan']:array();
$healthy_step_fee = isset($_POST['healthy_step_fee'])?$_POST['healthy_step_fee']:"";
$only_waive_products = isset($_POST['only_waive_products'])?$_POST['only_waive_products']:"N";
$billing_display = isset($_POST['billing_display'])?$_POST['billing_display']:"Y";
$primary_zip = isset($_POST['primary_zip'])?trim($_POST['primary_zip']):'';

$healthyStepFee = $MemberEnrollment->getHealthyStepFee($product_matrix,$sponsor_id,$primary_zip);
$healthy_step_fee_total = isset($healthyStepFee[$healthy_step_fee]) ? $healthyStepFee[$healthy_step_fee]['price'] : 0;

$group_product_list = array();
if($enrollmentLocation=='groupSide' || $is_group_member == "Y" || $billing_display == 'N'){

  $waive_checkbox = !empty($_POST['waive_checkbox']) ? $_POST['waive_checkbox'] : '';
  if(!empty($waive_checkbox)){

    $group_waive_product=isset($_POST['waive_products'])?$_POST['waive_products']:array();

    if(!empty($group_waive_product)){
      $group_product_list=$MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
      $product_list = array_merge($product_list,$group_product_list);
    }
  }
}

if(!empty($product_list)){
	if(in_array($enrollmentLocation,array("self_enrollment_site")) || $only_waive_products =='Y') {
		$verification_option = array('eSign');
	} else {
		$verification_option = $MemberEnrollment->get_enrollment_verification_option($product_list);
	}

	if(count($verification_option) > 0){
		ob_start();
		?>
		<div class="clearfix"></div>
      	<div class="m-b-30">
  			<?php foreach ($verification_option as $key => $row) { ?>
				<?php if($row=="eSign") { ?>
						<input type="hidden" name="signature_data" value="" id="hdn_signature_data">
	              		<div class="m-b-15"> 
	               			<label class="mn label-input">
	                		<input name="application_type" type="radio" value="member_signature" id="application_type_member_signature" class="application_type" <?= isset($is_direct_application) && $is_direct_application == 'Y' ? 'checked="checked"' : ''?> /> e-Signature
	               			</label>
	              		</div>
	             <?php }else if($row=="email_sms_verification"){ ?>
		              <div class="m-b-15">   
		                 <label class="mn label-input">
		                    <input name="application_type" type="radio" value="member" class="application_type"/>
		                    Email/Text Message Delivery To Primary Plan Holder
		                 </label> 
		              </div>
		           
	           	<?php }else if($row=="voice_verification") { ?>
					<div class="m-b-15"> 
				 		<label class="mn label-input">
					    	<input name="application_type" type="radio" value="voice_verification" class="application_type" /> Voicemail upload: Upload Voicemail to system
					 	</label>
					</div>
				<?php }else if($row=="upload_document") { ?>
	              		<div class="m-b-15">
		                	<label class="mn label-input">
		                  		<input name="application_type" type="radio" value="admin" class="application_type" /> Paper Application Verification 
		                	</label>
		              	</div>
				<?php } ?>         
			<?php } ?>
			<p class="error" id="error_application_type"></p>
		</div>
		<?php 
		$html = ob_get_clean();
		$response['html']=$html;

		ob_start();
		?>
		<div class="form-group m-b-20">
         	<div class="prd_div">
         		<?php if(!empty($product_list)) { ?>
         			<div class="table-responsive">
						<table class="<?=$table_class?>">
							<thead>
								<tr>
									<th>
									<input 	type="checkbox" 
											name="product_check_all" 
											class="product_check_all" 
											id="product_terms_check_all" >
									</th>
									<th class="text-left">Benefits</th>
									<th>Category</th>
									<th>Name</th>
									<th>Effective Date</th>
									<th class="text-center">Terms</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($product_list as $key => $product) { ?>
									<?php 
										$sqlProduct="SELECT pc.title,p.id,p.name FROM prd_main p
											JOIN prd_category pc on (pc.id=p.category_id)
											 where p.id=:id";
										$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$product));
									?>
								 <?php if(!in_array($product,$group_product_list)){ ?>
									<tr>
										<td class="text-left">
											<input 	type="checkbox" 
													name="product_check[<?=$product?>]" 
													id="product_check_<?=$product?>" 
													class="product_terms_check"
													value="<?=$product?>"><br>
											<span class="error" id="error_product_check_<?=$product?>"></span>
										</td>
										<td>
											<a href="javascript:void(0)"  data-product-id="<?=md5($product)?>" class="verification_terms"><i class="material-icons"> info </i></a>
										</td>
										<td><?=$resProduct['title']?></td> 
										<td><?=$resProduct['name']?></td>
										<td id="td_terms_products_<?= $product ?>"></td>
										<td class="icons text-center">
											<a href="javascript:void(0);" data-desc="product_id=<?=md5($product)?>" data-toggle="tooltip" data-placement="top" title="Terms & Conditions" class="prd_terms_popup"><i class="fa fa-file-text-o" aria-hidden="true"></i></a>
										</td>
									</tr>
								 <?php } ?>
								<?php } ?>
							</tbody>
						</table>
					</div>
         		<?php } ?>
			</div>
        </div>
        <?php 
		$product_checkbox_html = ob_get_clean();
		$response['product_checkbox_html']=$product_checkbox_html;

		$response['status']='success';
	}else{
		$response['status']='fail';
	}
}else{
	$response['status']='fail';
}


echo json_encode($response);
dbConnectionClose();
exit;
?>