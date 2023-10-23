<div class="m-b-25"> 
    <p>Set Enrollee</p>
    <?php if(!($allowPricingUpdate) && !empty($enrolleeType) && in_array('Primary',$enrolleeType) && 
      in_array('Spouse',$enrolleeType) && in_array('Child',$enrolleeType)){ ?>
      	<input type="hidden" name="allow_enrolleeType[All]" value="All">
  	<?php } ?>
  	<?php if(!($allowPricingUpdate) && !empty($enrolleeType) && in_array('Primary',$enrolleeType)){ ?>
      	<input type="hidden" name="allow_enrolleeType[Primary]" value="Primary">
  	<?php } ?>
  	<?php if(!($allowPricingUpdate) && !empty($enrolleeType) && in_array('Spouse',$enrolleeType)){ ?>
      	<input type="hidden" name="allow_enrolleeType[Spouse]" value="Spouse">
  	<?php } ?>
  	<?php if(!($allowPricingUpdate) && !empty($enrolleeType) && in_array('Child',$enrolleeType)){ ?>
      	<input type="hidden" name="allow_enrolleeType[Child]" value="Child">
  	<?php } ?>
  	<div class="checkbox-question">
	    <div class="checkbox-inline">
	      <label><input id="enrolleeTypeAll" name="enrolleeType[All]" type="checkbox" value="All" <?= !empty($enrolleeType) && 
	      in_array('Primary',$enrolleeType) && 
	      in_array('Spouse',$enrolleeType) && 
	      in_array('Child',$enrolleeType) ? 'checked' : '' ?> <?=  (!($allowPricingUpdate) ? 'disabled' : '') ?>/> All</label>
	    </div>
	    <div class="checkbox-inline">
	      <label><input class="enrolleeType" name="enrolleeType[Primary]" type="checkbox" value="Primary" <?= !empty($enrolleeType) && 
	      in_array('Primary',$enrolleeType) ? 'checked' : '' ?> <?=  (!($allowPricingUpdate) ? 'disabled' : '') ?>/> Primary</label>
	    </div>
	    <div class="checkbox-inline">
	      <label><input class="enrolleeType" name="enrolleeType[Spouse]" type="checkbox" value="Spouse" <?= !empty($enrolleeType) && 
	      in_array('Spouse',$enrolleeType) ? 'checked' : '' ?> <?=  (!($allowPricingUpdate) ? 'disabled' : '') ?>/> Spouse</label>
	    </div>
	    <div class="checkbox-inline">
	      <label><input class="enrolleeType" name="enrolleeType[Child]" type="checkbox" value="Child" <?= !empty($enrolleeType) && 
	      in_array('Child',$enrolleeType) ? 'checked' : '' ?> <?=  (!($allowPricingUpdate) ? 'disabled' : '') ?>/> Child</label>
	    </div>
	  </div>  
    <p class="error" id="error_enrolleeType"></p>
</div>
<div id="enrolleeTypeMainDiv">
	<?php if(!empty($enrolleeTypeArr)) { ?>
		<?php foreach ($enrolleeTypeArr as $key => $enrolleeTypeVal) { ?>
			<div class="m-b-25" id="enrolleeTypeInnerMainDiv<?= $enrolleeTypeVal ?>" style="<?= in_array($enrolleeTypeVal,$enrolleeType) ? '' :'display: none' ?>">  
			    <h4 class="m-b-15"><?= $enrolleeTypeVal ?> Enrollee</h4>
			    <p class="m-b-15"><strong>Select which criteria you need for pricing:</strong></p>
			    <div class="clearfix">
			      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
			        <?php if(!empty($prdPricingQuestionRes)) { ?>
			          <?php $rowCount=0; ?>
			          <?php foreach ($prdPricingQuestionRes as $priceKey => $priceRow) { ?>
			            <?php 
			              $label = $priceRow['label'];
			              $displayLabel = $priceRow['display_label'];
			              $controlType = $priceRow['type'];
			              if(in_array($enrolleeTypeVal,array("Spouse","Child")) && in_array($displayLabel,array('Has Spouse','Number Of Children'))){
			              	continue;
			              }
			            ?>
			            <?php if($rowCount!=0 && $rowCount%3 == 0){ ?>
			      </div>
			      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
			        <?php } ?>
			        <div class="list_label" style="width:100%">
			        	<?php if(!($allowPricingUpdate) && (!empty($price_control_enrollee[$enrolleeTypeVal]) && in_array($priceRow['id'], $price_control_enrollee[$enrolleeTypeVal])) ){ ?>
						      <input type="hidden" name="allow_price_control_enrollee[<?= $enrolleeTypeVal ?>][]" value="<?= $priceRow['id'] ?>">
						<?php } ?>
			          <label><input name="price_control_enrollee[<?= $enrolleeTypeVal ?>][]" class="price_control_matrix_enrollee <?= $controlType=="Spouse" && $displayLabel != 'Has Spouse' ? 'spouseControl'  : ''?> <?= ( $displayLabel=='Has Spouse') ?'HasSpouse' :'' ?>"  type="checkbox" value="<?= $priceRow['id'] ?>" <?= $controlType=="Spouse" && $displayLabel != 'Has Spouse' && (!empty($price_control_enrollee[$enrolleeTypeVal]) && !in_array('Has Spouse', $price_control_enrollee[$enrolleeTypeVal])) ? 'disabled=disabled'  : ''?> data-label="<?= $displayLabel ?>" <?=(!empty($price_control_enrollee[$enrolleeTypeVal]) && in_array($priceRow['id'], $price_control_enrollee[$enrolleeTypeVal])) ? "checked" : ''?> data-enrollee-type="<?= $enrolleeTypeVal ?>" <?=  (!($allowPricingUpdate) ? 'disabled' : '') ?>><?= $displayLabel ?></label>
			        </div>
			        <?php $rowCount++; ?>
			        <?php } ?>
			        <?php } ?>
			      </div>
			    </div>
			    <p class="error" id="error_price_control_enrollee_<?= $enrolleeTypeVal ?>"></p>
		  	</div>
		<?php } ?>
	<?php } ?>
	<p class="error" id="error_price_control_enrollee"></p>
</div>
<?php if($allowPricingUpdate) { ?>
	<div class="form-group height_auto" id="btn_set_pricing_matrix_Enrollee">
	  <a href="javascript:void(0);"  class="btn btn-primary"> + Create Matrix</a>
	</div>
<?php } ?>
<div id="create_pricing_matrix_div_Enrollee" style="display: none">
  <?php include ('prd_pricing_matrix_add_Enrollee.inc.php'); ?>
</div>
<div id="pricingMatrixIframeDivEnrollee"></div>

<div class="m-b-25" id="child_added_div" style="<?= !empty($enrolleeType) && 
      in_array('Child',$enrolleeType) ? '' : 'display: none'; ?>">
	<h4 class="m-b-15 m-t-15">Child Added</h4>
	<p><i>How are the child dependent(s) rates calculated?</i></p>
	<div class="radio-v">
		<label><input type="radio" value="Individually Rated" name="childRateCalculateType" <?= !empty($childRateCalculateType) && 
      $childRateCalculateType == "Individually Rated" ? 'checked' : '' ?>/> Individually Rated</label>
	</div>
	<div class="radio-v">
		<label class="label-input"><input type="radio" value="Single Rate based on Eldest Child" name="childRateCalculateType" <?= !empty($childRateCalculateType) && 
      $childRateCalculateType == "Single Rate based on Eldest Child" ? 'checked' : '' ?>/> Single Rate based on Eldest Child</label>
	</div>
	<p class="error" id="error_childRateCalculateType"></p>

	<div class="m-t-25" id="singleRateBaseChildDiv" style="<?= !empty($childRateCalculateType) && 
      $childRateCalculateType == "Single Rate based on Eldest Child" ? '' : 'display: none'; ?>">
      	<div class="row">
	        <div class="col-sm-6">
	      		<select name="singleRateChildrenAllowed" name="singleRateChildrenAllowed" class="form-control" id="singleRateChildrenAllowed" data-live-search="true">
	      			<option value="" hidden selected="selected"></option>
				        <?php for ($i=1;$i<=10;$i++) { ?>
				            <option value="<?= $i; ?>" <?php echo isset($singleRateChildrenAllowed) && $singleRateChildrenAllowed == $i ? "selected" : '' ?>>
				                <?= $i ?>
				            </option>
				        <?php } ?>
			        <option value="Unlimited" <?php echo isset($singleRateChildrenAllowed) && $singleRateChildrenAllowed == 'Unlimited' ? "selected" : '' ?>>Unlimited</option>
	      		</select>
	      		<label># of Children Allowed</label>
	      		<p class="error" id="error_singleRateChildrenAllowed"></p>
	      	</div>
    	</div>
	</div>
</div>

<div class="mn">
	<h4 class="m-b-15">Primary Age</h4>
	<p><i>ls primary user required to be eldest person in coverage?</i></p>
	<div class="radio-v">
		<label><input type="radio" value="Y" name="enrollee_primary_age" <?= !empty($enrollee_primary_age) && $enrollee_primary_age == "Y" ? 'checked' : '' ?>  /> Yes</label>
	</div>
	<div class="radio-v">
		<label><input type="radio" value="N" name="enrollee_primary_age" <?= !empty($enrollee_primary_age) && $enrollee_primary_age == "N" ? 'checked' : '' ?>  /> No</label>
	</div>
	<p class="error" id="error_enrollee_primary_age"></p>
</div>


<div class="form-group height_auto" style="display: none">
	<h4 class="m-b-15">Rider</h4>
	<p><i>Does this product offer a rider for enrollees?</i></p>
	<div class="radio-v">
		<label><input type="radio" value="Y" name="rider_for_enrollee" <?= !empty($rider_for_enrollee) && $rider_for_enrollee == "Y" ? 'checked' : '' ?> /> Yes</label>
	</div>
	<div class="radio-v">
		<label><input type="radio" value="N" name="rider_for_enrollee" <?= !empty($rider_for_enrollee) && $rider_for_enrollee == "N" ? 'checked' : '' ?> /> No</label>
	</div>
	<p class="error" id="error_rider_for_enrollee"></p>
</div>

<div class="m-t-20" id="rider_for_enrollee_div" style="<?= !empty($rider_for_enrollee) && $rider_for_enrollee == "Y" ? 'display: none' : 'display: none' ?>">
	<div class="row">
		<div class="col-lg-4 col-md-6 col-sm-6">
		   <h4 class="m-t-0">Primary</h4>
		   <div id="primary_rider_div">
		   	<?php $riderCount = 1; ?>
		   	<?php if(!empty($riderInfoArr)&& !empty($riderInfoArr['Primary'])) { ?>
		   		<?php foreach ($riderInfoArr['Primary'] as $key => $value) { ?>
		   			<div class="row rider_div" id="rider_div_<?=$value['id']?>_<?= $value['rider_type'] ?>" data-id="<?=$value['id']?>" data-type="<?= $value['rider_type'] ?>">
						<div class="col-sm-6">
						   <div class="row">
						      <div class="col-sm-2" style="<?= $riderCount > 1 ? '' : 'display: none' ?>" id="remove_rider_div_<?=$value['id']?>_<?= $value['rider_type'] ?>">
						         <a href="javascript:void(0);" id="remove_rider_<?=$value['id']?>_<?= $value['rider_type'] ?>" class="text-light-gray fs16 remove_rider" data-id="<?=$value['id']?>" data-type="<?= $value['rider_type'] ?>">X</a>
						      </div>
						      <div class="col-sm-10">
						         <div class="form-group height_auto">
						            <select class="form-control" id="riderProduct_<?=$value['id']?>_<?= $value['rider_type'] ?>" name="riderProduct[<?=$value['id']?>][<?= $value['rider_type'] ?>]">
						               <option value="" hidden selected="selected"></option>
						                <?php if(!empty($productAddOnArray)){ ?>
						                  <?php foreach ($productAddOnArray as $categoryName => $productRow) { ?>
						                      <optgroup label='<?= $categoryName; ?>'>
						                        <?php foreach ($productRow as $key1 => $row1) { ?>
						                          <option value="<?= $row1['id'] ?>" <?= $row1['id'] == $value['rider_product_id'] ?'selected' : '' ?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
						                        <?php } ?>
						                      </optgroup>
						                  <?php } ?>
						                <?php } ?>
						            </select>
						            <label>Select Product</label>
						            <p class="error" id="error_riderProduct_<?=$value['id']?>_<?= $value['rider_type'] ?>"></p>
						         </div>
						      </div>
						   </div>
						</div>
						<div class="col-sm-6">
						   <div class="radio-v">
						      <label><input type="radio" value="Seperate Rate" name="riderRate[<?=$value['id']?>][<?= $value['rider_type'] ?>]"  <?= $value['rider_rate'] == "Seperate Rate" ? 'checked' : '' ?>> Separates Rates</label>
						   </div>
						   <div class="radio-v">
						      <label><input type="radio" value="Combined Rate" name="riderRate[<?=$value['id']?>][<?= $value['rider_type'] ?>]"  <?= $value['rider_rate'] == "Combined Rate" ? 'checked' : '' ?>> Combined Rates</label>
						   </div>
						   <p class="error" id="error_riderRate_<?=$value['id']?>_<?= $value['rider_type'] ?>"></p>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-12">
						  <div class="br-t m-b-10 p-t-10">
						</div>
						</div>
					</div>
					<?php $riderCount++; ?>
		   		<?php } ?>
		   	<?php } ?>
		   </div>
		   <div class="text-right">
		      <a href="javascript:void(0);" class="red-link add_rider" data-riderType="Primary">+ Rider</a>
		   </div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6">
		   <h4 class="m-t-0">Spouse</h4>
		   <div id="spouse_rider_div">
		   	<?php $riderCount = 1; ?>
		   	<?php if(!empty($riderInfoArr)&& !empty($riderInfoArr['Spouse'])) { ?>
		   		<?php foreach ($riderInfoArr['Spouse'] as $key => $value) { ?>
		   			<div class="row rider_div" id="rider_div_<?=$value['id']?>_<?= $value['rider_type'] ?>" data-id="<?=$value['id']?>" data-type="<?= $value['rider_type'] ?>">
						<div class="col-sm-6">
						   <div class="row">
						      <div class="col-sm-2" style="<?= $riderCount > 1 ? '' : 'display: none' ?>" id="remove_rider_div_<?=$value['id']?>_<?= $value['rider_type'] ?>">
						         <a href="javascript:void(0);" id="remove_rider_<?=$value['id']?>_<?= $value['rider_type'] ?>" class="text-light-gray fs16 remove_rider" data-id="<?=$value['id']?>" data-type="<?= $value['rider_type'] ?>">X</a>
						      </div>
						      <div class="col-sm-10">
						         <div class="form-group height_auto">
						            <select class="form-control" id="riderProduct_<?=$value['id']?>_<?= $value['rider_type'] ?>" name="riderProduct[<?=$value['id']?>][<?= $value['rider_type'] ?>]">
						               <option value="" hidden selected="selected"></option>
						                <?php if(!empty($productAddOnArray)){ ?>
						                  <?php foreach ($productAddOnArray as $categoryName => $productRow) { ?>
						                      <optgroup label='<?= $categoryName; ?>'>
						                        <?php foreach ($productRow as $key1 => $row1) { ?>
						                          <option value="<?= $row1['id'] ?>" <?= $row1['id'] == $value['rider_product_id'] ?'selected' : '' ?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
						                        <?php } ?>
						                      </optgroup>
						                  <?php } ?>
						                <?php } ?>
						            </select>
						            <label>Select Product</label>
						            <p class="error" id="error_riderProduct_<?=$value['id']?>_<?= $value['rider_type'] ?>"></p>
						         </div>
						      </div>
						   </div>
						</div>
						<div class="col-sm-6">
						   <div class="radio-v">
						      <label><input type="radio" value="Seperate Rate" name="riderRate[<?=$value['id']?>][<?= $value['rider_type'] ?>]"  <?= $value['rider_rate'] == "Seperate Rate" ? 'checked' : '' ?>> Separates Rates</label>
						   </div>
						   <div class="radio-v">
						      <label><input type="radio" value="Combined Rate" name="riderRate[<?=$value['id']?>][<?= $value['rider_type'] ?>]"  <?= $value['rider_rate'] == "Combined Rate" ? 'checked' : '' ?>> Combined Rates</label>
						   </div>
						   <p class="error" id="error_riderRate_<?=$value['id']?>_<?= $value['rider_type'] ?>"></p>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-12">
						  <div class="br-t m-b-10 p-t-10">
						</div>
						</div>
					</div>
					<?php $riderCount++; ?>
		   		<?php } ?>
		   	<?php } ?>
		   </div>
		   <div class="text-right">
		      <a href="javascript:void(0);" class="red-link add_rider" data-riderType="Spouse">+ Rider</a>
		   </div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6">
		   <h4 class="m-t-0">Child</h4>
		   <div id="child_rider_div">
		   	<?php $riderCount = 1; ?>
		   	<?php if(!empty($riderInfoArr)&& !empty($riderInfoArr['Child'])) { ?>
		   		<?php foreach ($riderInfoArr['Child'] as $key => $value) { ?>
		   			<div class="row rider_div" id="rider_div_<?=$value['id']?>_<?= $value['rider_type'] ?>" data-id="<?=$value['id']?>" data-type="<?= $value['rider_type'] ?>">
						<div class="col-sm-6">
						   <div class="row">
						      <div class="col-sm-2" style="<?= $riderCount > 1 ? '' : 'display: none' ?>" id="remove_rider_div_<?=$value['id']?>_<?= $value['rider_type'] ?>">
						         <a href="javascript:void(0);" id="remove_rider_<?=$value['id']?>_<?= $value['rider_type'] ?>" class="text-light-gray fs16 remove_rider" data-id="<?=$value['id']?>" data-type="<?= $value['rider_type'] ?>">X</a>
						      </div>
						      <div class="col-sm-10">
						         <div class="form-group height_auto">
						            <select class="form-control" id="riderProduct_<?=$value['id']?>_<?= $value['rider_type'] ?>" name="riderProduct[<?=$value['id']?>][<?= $value['rider_type'] ?>]">
						               <option value="" hidden selected="selected"></option>
						                <?php if(!empty($productAddOnArray)){ ?>
						                  <?php foreach ($productAddOnArray as $categoryName => $productRow) { ?>
						                      <optgroup label='<?= $categoryName; ?>'>
						                        <?php foreach ($productRow as $key1 => $row1) { ?>
						                          <option value="<?= $row1['id'] ?>" <?= $row1['id'] == $value['rider_product_id'] ?'selected' : '' ?>><?= $row1['name'] .' ('.$row1['product_code'].')' ?></option>
						                        <?php } ?>
						                      </optgroup>
						                  <?php } ?>
						                <?php } ?>
						            </select>
						            <label>Select Product</label>
						            <p class="error" id="error_riderProduct_<?=$value['id']?>_<?= $value['rider_type'] ?>"></p>
						         </div>
						      </div>
						   </div>
						</div>
						<div class="col-sm-6">
						   <div class="radio-v">
						      <label><input type="radio" value="Seperate Rate" name="riderRate[<?=$value['id']?>][<?= $value['rider_type'] ?>]"  <?= $value['rider_rate'] == "Seperate Rate" ? 'checked' : '' ?>> Separates Rates</label>
						   </div>
						   <div class="radio-v">
						      <label><input type="radio" value="Combined Rate" name="riderRate[<?=$value['id']?>][<?= $value['rider_type'] ?>]"  <?= $value['rider_rate'] == "Combined Rate" ? 'checked' : '' ?>> Combined Rates</label>
						   </div>
						   <p class="error" id="error_riderRate_<?=$value['id']?>_<?= $value['rider_type'] ?>"></p>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-12">
						  <div class="br-t m-b-10 p-t-10">
						</div>
						</div>
					</div>
					<?php $riderCount++; ?>
		   		<?php } ?>
		   	<?php } ?>
		   </div>
		   <div class="text-right">
		      <a href="javascript:void(0);" class="red-link add_rider" data-riderType="Child">+ Rider</a>
		   </div>
		</div>
		<p class="error" id="error_rider_general"></p>
	</div>
</div>