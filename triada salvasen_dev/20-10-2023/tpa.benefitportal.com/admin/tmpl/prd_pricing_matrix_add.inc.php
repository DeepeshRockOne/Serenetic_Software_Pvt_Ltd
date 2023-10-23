<div class="inner_box m-b-30">
    <h5 class="h5_title">Pricing Matrix</h5>
    <ul class="nav nav-tabs tabs customtab" role="tablist" id="pricingTypeTab">
        <li role="presentation" class="active">
            <a href="#manual_matrix" id="manual_matrixLI" aria-controls="manual_matrix " role="tab" data-toggle="tab">Manual Matrix Pricing</a>
        </li>
        <li role="presentation">
            <a href="#csv_matrix_tab" id="csv_matrix_tabLI" aria-controls="csv_matrix_tab" role="tab" data-toggle="tab">CSV Matrix Pricing</a>
        </li>
    </ul>
    <div class="tab-content m-t-20">
        <div role="tabpanel" class="tab-pane active" id="manual_matrix">
            <h5 class="h5_title m-b-10 pull-left">Set Price Criteria</h5> 
            <?php if($allowPricingUpdate) { ?> 
                <a href="javascript:void(0)" class="btn btn-skyblue pull-right" id="clearPricingMatrix">Clear</a>
            <?php } ?>
            
            <input type="hidden" class="clearPricing" name="matrixGroup" id="matrixGroup" value="">
            <input type="hidden" name="allow_matrixPlanType" id="allow_matrixPlanType" value="">
            <input type="hidden" name="allow_age_from" id="allow_age_from" value="">
            <input type="hidden" name="allow_age_to" id="allow_age_to" value="">
            <input type="hidden" name="allow_state" id="allow_state" value="">
            <input type="hidden" name="allow_zip" id="allow_zip" value="">
            <input type="hidden" name="allow_gender" id="allow_gender" value="">
            <input type="hidden" name="allow_smoking_status" id="allow_smoking_status" value="">
            <input type="hidden" name="allow_tobacco_status" id="allow_tobacco_status" value="">
            <input type="hidden" name="allow_height_by" id="allow_height_by" value="">
            <input type="hidden" name="allow_height_feet" id="allow_height_feet" value="">
            <input type="hidden" name="allow_height_inch" id="allow_height_inch" value="">
            <input type="hidden" name="allow_height_feet_to" id="allow_height_feet_to" value="">
            <input type="hidden" name="allow_height_inch_to" id="allow_height_inch_to" value="">
            <input type="hidden" name="allow_weight_by" id="allow_weight_by" value="">
            <input type="hidden" name="allow_weight" id="allow_weight" value="">
            <input type="hidden" name="allow_weight_to" id="allow_weight_to" value="">
            <input type="hidden" name="allow_no_of_children_by" id="allow_no_of_children_by" value="">
            <input type="hidden" name="allow_no_of_children" id="allow_no_of_children" value="">
            <input type="hidden" name="allow_no_of_children_to" id="allow_no_of_children_to" value="">
            <input type="hidden" name="allow_has_spouse" id="allow_has_spouse" value="">
            <input type="hidden" name="allow_spouse_age_from" id="allow_spouse_age_from" value="">
            <input type="hidden" name="allow_spouse_age_to" id="allow_spouse_age_to" value="">
            <input type="hidden" name="allow_spouse_gender" id="allow_spouse_gender" value="">
            <input type="hidden" name="allow_spouse_smoking_status" id="allow_spouse_smoking_status" value="">
            <input type="hidden" name="allow_spouse_tobacco_status" id="allow_spouse_tobacco_status" value="">
            <input type="hidden" name="allow_spouse_height_feet" id="allow_spouse_height_feet" value="">
            <input type="hidden" name="allow_spouse_height_inch" id="allow_spouse_height_inch" value="">
            <input type="hidden" name="allow_spouse_weight" id="allow_spouse_weight" value="">
            <input type="hidden" name="allow_spouse_weight_type" id="allow_spouse_weight_type" value="">
            <input type="hidden" name="allow_benefit_amount" id="allow_benefit_amount" value="">
            <input type="hidden" name="allow_in_patient_benefit" id="allow_in_patient_benefit" value="">
            <input type="hidden" name="allow_out_patient_benefit" id="allow_out_patient_benefit" value="">
            <input type="hidden" name="allow_monthly_income" id="allow_monthly_income" value="">
            <!-- <input type="hidden" name="allow_benefit_percentage" id="allow_benefit_percentage" value=""> -->
             <input type="hidden" name="pricingDataDisabled" id="pricingDataDisabled" value="N">
            <div class="clearfix"></div>
            <div class="row">
              	<div class="col-sm-6 col-md-4">
                	<div class="form-group">
                    	<select name="matrixPlanType" id="matrixPlanType" class="form-control clearPricing" >
                       		<option value="" hidden selected="selected"></option>
                                <?php if(!empty($prdPlanTypeArray)) {?>
                                    <?php foreach ($prdPlanTypeArray as $key => $tier) { ?>
                                        <option value="<?= $tier['id'] ?>" <?= !empty($coverage_options) && in_array($tier['id'],$coverage_options) ? '' : 'disabled' ?> > <?= $tier['title'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                        </select>
                        <label>Plan Tier</label>
                        <p class="error" id="error_matrixPlanType"></p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6 col-md-4 1PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(1,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Age</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                        	<div class="phone-addon pr">
                                <select name="age_from" id="age_from" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=120;$i++){ ?>
                                    <option value="<?= $i ?>" <?= (isset($age_from) && $i==$age_from) ? 'selected=selected' : '' ?> >
                                      <?= $i ?>
                                    </option>
                                    <?php } ?>
                                </select>
                                <label>Age</label>
                                
                            </div>
                            <div class="phone-addon w-42">
                            	To
                            </div>
                            <div class="phone-addon pr">
                            	<select name="age_to" id="age_to" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected" ></option>
                                    <?php for($i=0;$i<=120;$i++){ ?>
                                        <option value="<?= $i ?>" <?= (isset($age_to) && $i==$age_to) ? 'selected=selected' : '' ?> >
                                          <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label>Age</label>                        	
                                
                            </div>
                        </div>
                        <p class="error" id="error_age"></p>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3 2PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(2,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>State</strong></p>
                	<div class="form-group">
                        <select name="state" class="form-control clearPricing" id="state" data-live-search="true" >
                            <option value="" hidden selected="selected" ></option>
                            <?php if(!empty($allStateRes)){ ?>
                                <?php foreach ($allStateRes as $srow) { ?>
                                <option value="<?= $srow['name']; ?>" <?php echo isset($state) && $state == $srow['name'] ? "selected" : '' ?> ><?php echo $srow['name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <label>State</label>
                        <p class="error" id="error_state"></p>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3 4PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(4,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Gender</strong></p>
                	<div class="form-group">
                        <select name="gender" class="form-control clearPricing" id="gender" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Male" <?php echo isset($gender) && $gender == 'Male' ? "selected" : '' ?> >Male</option>
                            <option value="Female" <?php echo isset($gender) && $gender == 'Female' ? "selected" : '' ?> >Female</option>
                        </select>
                        <label>Gender</label>
                        <p class="error" id="error_gender"></p>
                    </div>
                </div>
                <div class="col-sm-12  7PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Height</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="height_by" id="height_by" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    
                                    <option value="Exactly" <?= !empty($height_by) && $height_by == "Exactly" ? "selected" : "" ?> >Exactly</option>
                                    <option value="Less Than" <?= !empty($height_by) && $height_by == "Less Than" ? "selected" : "" ?> >Less Than</option>
                                    <option value="Greater Than" <?= !empty($height_by) && $height_by == "Greater Than" ? "selected" : "" ?> >Greater Than</option>
                                    <option value="Range" <?= !empty($height_by) && $height_by == "Range" ? "selected" : "" ?> >Range</option>
                                </select>
                                <label>Height By</label>
                            </div>
                            <div class="phone-addon pr w-55 height_range_div" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                From
                            </div>
                        	<div class="phone-addon pr">
                                <select name="height_feet" class="form-control clearPricing" id="height_feet" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=1; $i<=8;$i++){?>
                                    <option value="<?=$i?>" <?php echo isset($height_feet) && $height_feet == $i ? "selected" : '' ?> >
                                      <?= $i.' Ft. ';  ?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Feet</label>
                               
                            </div>
                            <div class="phone-addon pr">
                            	<select name="height_inch" class="form-control clearPricing" id="height_inch" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($j=0; $j<=11;$j++){?>
                                    <option value="<?=$j?>" <?= isset($height_inch) && $height_inch == $j ? "selected" : '' ?> >
                                      <?= $j.' In. ';?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Inch</label>
                                
                            </div>
                            <div class="phone-addon pr w-42 height_range_div" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                To
                            </div>
                            <div class="phone-addon pr height_range_div" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                <select name="height_feet_to" class="form-control clearPricing" id="height_feet_to" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=1; $i<=8;$i++){?>
                                    <option value="<?=$i?>" <?php echo isset($height_feet_to) && $height_feet_to == $i ? "selected" : '' ?> >
                                      <?= $i.' Ft. ';  ?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Feet</label>
                               
                            </div>
                            <div class="phone-addon pr height_range_div" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                <select name="height_inch_to" class="form-control clearPricing" id="height_inch_to" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($j=0; $j<=11;$j++){?>
                                    <option value="<?=$j?>" <?= isset($height_inch_to) && $height_inch_to == $j ? "selected" : '' ?> >
                                      <?= $j.' In. ';?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Inch</label>
                                
                            </div>
                            
                        </div>
                         <p class="error" id="error_height"></p>
                    </div>
                </div>
                <div class="col-sm-12  8PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Weight</strong></p>
                	<div class="form-group">
                        <div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="weight_by" id="weight_by" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    
                                    <option value="Exactly" <?= !empty($weight_by) && $weight_by == "Exactly" ? "selected" : "" ?> >Exactly</option>
                                    <option value="Less Than" <?= !empty($weight_by) && $weight_by == "Less Than" ? "selected" : "" ?> >Less Than</option>
                                    <option value="Greater Than" <?= !empty($weight_by) && $weight_by == "Greater Than" ? "selected" : "" ?> >Greater Than</option>
                                    <option value="Range" <?= !empty($weight_by) && $weight_by == "Range" ? "selected" : "" ?> >Range</option>
                                </select>
                                <label>Weight by</label>
                            </div>

                            <div class="phone-addon pr w-55 weight_range_div" style="<?= !empty($weight_by) && $weight_by == "Range" ? "" : "display: none" ?>">
                                From
                            </div>
                            <div class="phone-addon pr">
                                <select name="weight" id="weight" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=1000;$i++) { ?>
                                        <option value="<?= $i ?>" <?= !empty($weight) && $weight == $i ? "selected" : "" ?> ><?= $i ?></option>
                                    <?php } ?>
                                </select>
                                <label>Weight</label>
                            </div>
                            <div class="phone-addon pr w-42 weight_range_div" style="<?= !empty($weight_by) && $weight_by == "Range" ? "" : "display: none" ?>">
                                To
                            </div>
                            <div class="phone-addon pr weight_range_div" style="<?= !empty($weight_by) && $weight_by == "Range" ? "" : "display: none" ?>">
                                <select name="weight_to" id="weight_to" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=1000;$i++) { ?>
                                        <option value="<?= $i ?>" <?= !empty($weight_to) && $weight_to == $i ? "selected" : "" ?> ><?= $i ?></option>
                                    <?php } ?>
                                </select>
                                <label>Weight</label>
                            </div>
                            
                            
                            
                        </div>
                        <p class="error" id="error_weight"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2 5PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(5,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Smoking Status</strong></p>
                	<div class="form-group">
                        <select name="smoking_status" class="form-control clearPricing" id="smoking_status" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($smoking_status) && $smoking_status == 'Y' ? "selected" : '' ?>>Yes</option>
                            <option value="N" <?php echo isset($smoking_status) && $smoking_status == 'N' ? "selected" : '' ?>>No</option>
                        </select>
                        <label>Smoking Status</label>
                        <p class="error" id="error_smoking_status"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 6PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(6,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Tobacco Status</strong></p>
                	<div class="form-group">
                        <select name="tobacco_status" class="form-control clearPricing" id="tobacco_status" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($tobacco_status) &&  $tobacco_status == 'Y' ? "selected" : '' ?> >Yes</option>
                            <option value="N" <?php echo isset($tobacco_status) && $tobacco_status == 'N' ? "selected" : '' ?> >No</option>
                        </select>
                        <label>Tobacco Status</label>
                        <p class="error" id="error_tobacco_status"></p>
                    </div>
                </div>
                <div class="col-sm-12 9PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Number of Children</strong></p>
                	<div class="form-group">
                        <div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="no_of_children_by" id="no_of_children_by" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    
                                    <option value="Exactly" <?= !empty($no_of_children_by) && $no_of_children_by == "Exactly" ? "selected" : "" ?> >Exactly</option>
                                    <option value="Less Than" <?= !empty($no_of_children_by) && $no_of_children_by == "Less Than" ? "selected" : "" ?> >Less Than</option>
                                    <option value="Greater Than" <?= !empty($no_of_children_by) && $no_of_children_by == "Greater Than" ? "selected" : "" ?> >Greater Than</option>
                                    <option value="Range" <?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "selected" : "" ?> >Range</option>
                                </select>
                                <label>Children by</label>
                            </div>
                            <div class="phone-addon pr w-55 no_of_children_by_range_div" style="<?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "" : "display: none" ?>">
                                From
                            </div>
                            <div class="phone-addon pr">
                                <select name="no_of_children" class="form-control clearPricing" id="no_of_children" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for ($i=0;$i<=15;$i++) { ?>
                                        <option value="<?= $i; ?>" <?php echo isset($no_of_children) && $no_of_children == $i ? "selected" : '' ?> >
                                            <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label># of Children</label>
                            </div>
                            <div class="phone-addon pr w-42 no_of_children_by_range_div" style="<?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "" : "display: none" ?>">
                                To
                            </div>
                            <div class="phone-addon pr no_of_children_by_range_div" style="<?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "" : "display: none" ?>">
                                <select name="no_of_children_to" class="form-control clearPricing" id="no_of_children_to" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for ($i=0;$i<=15;$i++) { ?>
                                        <option value="<?= $i; ?>" <?php echo isset($no_of_children_to) && $no_of_children_to == $i ? "selected" : '' ?> >
                                            <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label># of Children</label>
                            </div>
                        </div>

                        <p class="error" id="error_no_of_children"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2 3PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(3,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Zip Code</strong></p>
                	<div class="form-group">
                        <input type="text" id="zip" class="form-control clearPricing" name="zip" value='<?php echo isset($zip) && $zip ?>' />
                        <label>Zip Code</label>
                        <p class="error" id="error_zip"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 10PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(10,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Has Spouse</strong></p>
                	<div class="form-group">
                        <select name="has_spouse" class="form-control clearPricing" id="has_spouse" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($has_spouse) && $has_spouse == 'Y' ? "selected" : '' ?> >Yes</option>
                            <option value="N" <?php echo isset($has_spouse) && $has_spouse == 'N' ? "selected" : '' ?> >No</option>
                        </select>
                        <label>Has Spouse</label>
                        <p class="error" id="error_has_spouse"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDiv 12PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(12,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Gender</strong></p>
                	<div class="form-group">
                        <select name="spouse_gender" class="form-control clearPricing" id="spouse_gender" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Male" <?php echo isset($spouse_gender) && $spouse_gender == 'Male' ? "selected" : '' ?> >Male</option>
                            <option value="Female" <?php echo isset($spouse_gender) && $spouse_gender == 'Female' ? "selected" : '' ?> >Female</option>
                        </select>
                        <label>Spouse Gender</label>
                        <p class="error" id="error_spouse_gender"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDiv 11PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(11,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouser Age</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                        	<div class="phone-addon pr">
                                <select name="spouse_age_from" id="spouse_age_from" class="form-control clearPricing" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=120;$i++){ ?>
                                        <option value="<?= $i ?>" <?= (isset($spouse_age_from) && $i==$spouse_age_from) ? 'selected' : '' ?> >
                                        <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label>Age</label>
                                
                            </div>
                            <div class="phone-addon w-42">
                            	To
                            </div>
                            <div class="phone-addon pr">
                            	<select name="spouse_age_to" id="spouse_age_to" class="form-control clearPricing" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=120;$i++){ ?>
                                        <option value="<?= $i ?>" <?= (isset($spouse_age_to) && $i==$spouse_age_to) ? 'selected' : '' ?> >
                                          <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label>Age</label>
                                
                            </div>
                        </div>
                        <p class="error" id="error_spouse_age"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDiv 15PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(15,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouser Height</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                        	<div class="phone-addon pr">
                                <select name="spouse_height_feet" class="form-control clearPricing" id="spouse_height_feet" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=1; $i<=8;$i++){?>
                                        <option value="<?=$i?>" <?php echo isset($spouse_height_feet) && $spouse_height_feet == $i ? "selected" : '' ?> >
                                          <?= $i.' Ft. ';  ?>
                                        </option>
                                    <?php }?>
                                  </select>
                                  <label>Feet</label>
                                  
                            </div>
                            <div class="phone-addon pr">
                            	<select name="spouse_height_inch" class="form-control clearPricing" id="spouse_height_inch" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($j=0; $j<=11;$j++){?>
                                        <option value="<?=$j?>" <?= isset($spouse_height_inch) && $spouse_height_inch == $j ? "selected='selected'" : '' ?> >
                                          <?= $j.' In. ';?>
                                        </option>
                                    <?php }?>
                                </select>
                                <label>Inch</label>
                                
                            </div>
                        </div>
                        <p class="error" id="error_spouse_height"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2 spouseControlPriceDiv 16PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(16,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Weight</strong></p>
                    <div class="form-group">
                        <div class="phone-control-wrap fixed_size">
                          <div class="phone-addon pr">
                                <select name="spouse_weight" id="spouse_weight" class="form-control clearPricing" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=200;$i++) { ?>
                                        <option value="<?= $i ?>" <?= !empty($spouse_weight) && $spouse_weight == $i ? "selected" : "" ?> ><?= $i ?></option>
                                    <?php } ?>
                                </select>
                                
                                
                            </div>
                            <div class="phone-addon pr">
                                <select name="spouse_weight_type" id="spouse_weight_type" class="form-control clearPricing" >
                                    <option value="" hidden selected="selected"></option>
                                    <option value="lbs" <?= !empty($spouse_weight_type) && $spouse_weight_type =="lbs" ? 'selected' : '' ?> >lbs</option>
                                    <option value="kg" <?= !empty($spouse_weight_type) && $spouse_weight_type =="kg" ? 'selected' : '' ?> >Kg</option>
                                    <option value="pound" <?= !empty($spouse_weight_type) && $spouse_weight_type =="pound" ? 'selected' : '' ?> >Pound</option>
                                </select> 
                                  
                                                
                            </div>
                        </div>
                        <p class="error" id="error_spouse_weight"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDiv 13PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(13,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Smoking Status</strong></p>
                	<div class="form-group">
                        <select name="spouse_smoking_status" class="form-control clearPricing" id="spouse_smoking_status" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($spouse_smoking_status) && $spouse_smoking_status == 'Y' ? "selected" : '' ?> >Yes</option>
                            <option value="N" <?php echo isset($spouse_smoking_status) && $spouse_smoking_status == 'N' ? "selected" : '' ?> >No</option>
                        </select>
                        <label>Smoking Status</label>
                        <p class="error" id="error_spouse_smoking_status"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDiv 14PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(14,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Tobacco Status</strong></p>
                	<div class="form-group">
                        <select name="spouse_tobacco_status" class="form-control clearPricing" id="spouse_tobacco_status" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($spouse_smoking_status) && $spouse_tobacco_status == 'Y' ? "selected" : '' ?> >Yes</option>
                            <option value="N" <?php echo isset($spouse_smoking_status) && $spouse_tobacco_status == 'N' ? "selected" : '' ?> >No</option>
                          </select>
                          <label>Tobacco Status</label>
                          <p class="error" id="error_spouse_tobacco_status"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 17PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(17,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Benefit Amount</strong></p>
                    <div class="form-group">
                        <input type="text" id="benefit_amount" class="form-control formatPricing clearPricing" name="benefit_amount" value='<?php echo isset($benefit_amount) && $benefit_amount ?>' />
                        <label>Benefit Amount</label>
                        <p class="error" id="error_benefit_amount"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 18PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(18,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>InPatient Benefit</strong></p>
                    <div class="form-group">
                        <input type="text" id="in_patient_benefit" class="form-control formatPricing clearPricing" name="in_patient_benefit" value='<?php echo isset($in_patient_benefit) && $in_patient_benefit ?>' />
                        <label>InPatient Benefit</label>
                        <p class="error" id="error_in_patient_benefit"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 19PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(19,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>OutPatient Benefit</strong></p>
                    <div class="form-group">
                        <input type="text" id="out_patient_benefit" class="form-control formatPricing clearPricing" name="out_patient_benefit" value='<?php echo isset($out_patient_benefit) && $out_patient_benefit ?>' />
                        <label>OutPatient Benefit</label>
                        <p class="error" id="error_out_patient_benefit"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 20PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(20,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Monthly Income</strong></p>
                    <div class="form-group">
                        <input type="text" id="monthly_income" class="form-control formatPricing clearPricing" name="monthly_income" value='<?php echo isset($monthly_income) && $monthly_income ?>' />
                        <label>Monthly Income</label>
                        <p class="error" id="error_monthly_income"></p>
                    </div>
                </div>
                <?php /*
                <div class="col-sm-4  21PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(21,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Benefit Percentage</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="benefit_percentage" id="benefit_percentage" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=1; $i<=100;$i++){?>
                                    <option value="<?=$i?>" <?php echo isset($benefit_percentage) && $benefit_percentage == $i ? "selected" : '' ?> ><?=$i?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Benefit Percentage</label>
                            </div>
                        </div>
                         <p class="error" id="error_benefit_percentage"></p>
                    </div>
                </div>*/?>
            </div>
            <div id="pricing_matrix_pricing_main_div"></div>
          
            <div class="text-right">
                <p class="error" id="error_global_matrix_arr_exits"></p>
              	<a href="javascript:void(0);" class="btn btn-primary m-r-10" ID="addPricingMatrix">ADD</a>
                <a href="javascript:void(0);" class="red-link" id="cancelPricingFixed">Cancel</a>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="csv_matrix_tab">
            <div class="csv_matrix_tab">
                <h5 class="h5_title">Upload Pricing Matrix by CSV</h5>
                <input type="hidden" name="stored_file_name" id="stored_file_name" value="">
                <input type="hidden" name="is_csv_uploaded" id="is_csv_uploaded" value="N">
                <input type="hidden" name="saveCSVAs" id="saveCSVAs" value="">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="custom_drag_control"> 
                            <span class="btn btn-action" style="border-radius:0px;">Upload CSV</span>
                            <input type="file" class="gui-file" id="csv_file" name="csv_file">
                            <input type="text" class="gui-input" placeholder="">
                            <span class="error error_preview" id="error_csv_file"></span>
                            <span class="error error_preview" id="error_csv_fileUpload"></span>
                        </div>
                    </div>
                    <div class="col-sm-8 text-right">
                        <div class="d-none visible-xs m-t-15"></div>
                        <a href="<?= $PRICE_MATRIX_CSV_WEB ?>Pricing_Matrix.csv" download class="btn btn-info btn-outline">Download Template</a>
                    </div>
                </div>
                <hr>
                <div id="inline_content" style="display: none;">
                    <div class="col-sm-6">
                       <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Plan Tier</strong></span>
                                <div class="pr">
                                    <select name="matrixPlanTypeCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_matrixPlanTypeCSV"></p>
                       </div>
                    </div>
                    <div class="col-sm-6 1PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(1,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Age From</strong></span>
                                <div class="pr">
                                    <select name="age_fromCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_age_fromCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 1PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(1,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Age To</strong></span>
                                <div class="pr">
                                    <select name="age_toCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_age_toCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 2PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(2,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>State</strong></span>
                                <div class="pr">
                                    <select name="stateCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_stateCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 4PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(4,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Gender</strong></span>
                                <div class="pr">
                                    <select name="genderCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_genderCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Height BY</strong></span>
                                <div class="pr">
                                    <select name="height_byCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_byCSV"></p>
                        </div>
                    </div>
                    <div class="col-sm-6 7PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Height Feet</strong></span>
                                <div class="pr">
                                    <select name="height_feetCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_feetCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Height Inch</strong></span>
                                <div class="pr">
                                    <select name="height_inchCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_inchCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To Height Feet</strong></span>
                                <div class="pr">
                                    <select name="height_feet_toCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_feet_toCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To Height Inch</strong></span>
                                <div class="pr">
                                    <select name="height_inch_toCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_inch_toCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 8PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Weight By</strong></span>
                                <div class="pr">
                                    <select name="weight_byCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_weight_byCSV"></p>
                        </div>
                    </div>
                    <div class="col-sm-6 8PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Weight</strong></span>
                                <div class="pr">
                                    <select name="weightCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_weightCSV"></p>
                        </div>
                    </div>
                    <div class="col-sm-6 8PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To Weight</strong></span>
                                <div class="pr">
                                    <select name="weight_toCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_weight_toCSV"></p>
                        </div>
                    </div>

                    

                    <div class="col-sm-6 5PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(5,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Smoking Status</strong></span>
                                <div class="pr">
                                    <select name="smoking_statusCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_smoking_statusCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 6PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(6,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Tobacco Status</strong></span>
                                <div class="pr">
                                    <select name="tobacco_statusCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_tobacco_statusCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 9PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Children By</strong></span>
                                <div class="pr">
                                    <select name="no_of_children_byCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_no_of_children_byCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 9PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>No Of Children</strong></span>
                                <div class="pr">
                                    <select name="no_of_childrenCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_no_of_childrenCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 9PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To No Of Children</strong></span>
                                <div class="pr">
                                    <select name="no_of_children_toCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_no_of_children_toCSV"></p>
                        </div>
                    </div>


                    <div class="col-sm-6 3PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(3,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Zip Code</strong></span>
                                <div class="pr">
                                    <select name="zipCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_zipCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 10PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(10,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Has Spouse</strong></span>
                                <div class="pr">
                                    <select name="has_spouseCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_has_spouseCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 12PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(12,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Gender</strong></span>
                                <div class="pr">
                                    <select name="spouse_genderCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_genderCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 11PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(11,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Age From</strong></span>
                                <div class="pr">
                                    <select name="spouse_age_fromCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_age_fromCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 11PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(11,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Age To</strong></span>
                                <div class="pr">
                                    <select name="spouse_age_toCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_age_toCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 15PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(15,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Height Feet</strong></span>
                                <div class="pr">
                                    <select name="spouse_height_feetCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_height_feetCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 15PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(15,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Height Inch</strong></span>
                                <div class="pr">
                                    <select name="spouse_height_inchCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_height_inchCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 16PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(16,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Weight</strong></span>
                                <div class="pr">
                                    <select name="spouse_weightCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_weightCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 16PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(16,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Weight Type</strong></span>
                                <div class="pr">
                                    <select name="spouse_weight_typeCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_weight_typeCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 13PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(13,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Smoking Status</strong></span>
                                <div class="pr">
                                    <select name="spouse_smoking_statusCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_smoking_statusCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDiv 14PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(14,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Tobacco Status</strong></span>
                                <div class="pr">
                                    <select name="spouse_tobacco_statusCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_tobacco_statusCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 17PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(17,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Benefit Amount</strong></span>
                                <div class="pr">
                                    <select name="benefit_amountCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_benefit_amountCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 18PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(18,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>InPatient Benefit</strong></span>
                                <div class="pr">
                                    <select name="in_patient_benefitCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_in_patient_benefitCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 19PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(19,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>OutPatient Benefit</strong></span>
                                <div class="pr">
                                    <select name="out_patient_benefitCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_out_patient_benefitCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 20PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(20,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Monthly Income</strong></span>
                                <div class="pr">
                                    <select name="monthly_incomeCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_monthly_incomeCSV"></p>
                        </div>
                    </div>
                    <?php /*
                    <div class="col-sm-6 21PriceDiv" style="<?= !empty($price_controlCSVMatrix) && in_array(21,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Benefit Percentage</strong></span>
                                <div class="pr">
                                    <select name="benefit_percentageCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_benefit_percentageCSV"></p>
                        </div>
                    </div>
                    */?>
                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Retail Price</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_priceCSVRetail" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_priceCSVRetail"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Non-Commissionable</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_priceCSVNonCommissionable" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_priceCSVNonCommissionable"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Commissionable</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_priceCSVCommissionable" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_priceCSVCommissionable"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Effective Date</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_effective_dateCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_effective_dateCSV"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Termination Date</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_termination_dateCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_termination_dateCSV"></p>
                        </div>
                    </div>
                                    
                    <div class="m-t-15 text-right">
                        <button class="btn btn-info before_export" type="button" name="ImportCSV" id="ImportCSV" value="ImportCSV">Import</button>
                        <button class="btn btn-info while_exportBTN" type="button" name="CancelCSV" id="CancelCSV" value="CancelCSV" style="display: none">Cancel CSV</button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="min-h150">
                    </div>
                        <div class="matrix_progress_wrap">
                            <div class="after_export phone-control-wrap" style="display: none">
                                <div id="csvError" class="phone-addon">
                                    <div class="progress_left_tag">
                                    <div class="phone-addon w-110">
                                    <i class="fa fa-times-circle text-action fs24" aria-hidden="true"></i> &nbsp;&nbsp; 
                                    <strong class="text-action">Error :</strong>
                                    </div>
                                    <ul id="CSVErrorList" class="phone-addon"></ul>
                                    </div>
                                </div>
                                <div class="phone-addon w-70 pn">
                                    <a href="javascript:void(0);" class="red-link" id="cancelPricingMatrix">Close</a>
                                </div>
                            </div>
                            <div class="while_export" style="display:none">
                                <div class="loading-progress"></div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
