<div class="inner_box m-b-30">
    <h5 class="h5_title">Pricing Matrix</h5>
    <ul class="nav nav-tabs tabs customtab" role="tablist" id="pricingTypeTabEnrollee">
        <li role="presentation" class="active">
            <a href="#manual_matrix_Enrollee" id="manual_matrixLI_Enrollee" aria-controls="manual_matrix_Enrollee " role="tab" data-toggle="tab">Manual Matrix Pricing</a>
        </li>
        <li role="presentation">
            <a href="#csv_matrix_tab_Enrollee" id="csv_matrix_tabLI_Enrollee" aria-controls="csv_matrix_tab_Enrollee" role="tab" data-toggle="tab">CSV Matrix Pricing</a>
        </li>
    </ul>
    <div class="tab-content m-t-20">
        <div role="tabpanel" class="tab-pane active" id="manual_matrix_Enrollee">
            <h5 class="h5_title m-b-10 pull-left">Set Price Criteria</h5>  
            <?php if($allowPricingUpdate) { ?>
                <a href="javascript:void(0)" class="btn btn-skyblue pull-right" id="clearPricingMatrix">Clear</a>
            <?php } ?>
            <input type="hidden" class="clearPricing" name="matrixGroupEnrollee" id="matrixGroupEnrollee" value="">
            <input type="hidden" name="allow_enrolleeMatrix" id="allow_enrolleeMatrix" value="">
            <input type="hidden" name="allow_age_from_Enrollee" id="allow_age_from_Enrollee" value="">
            <input type="hidden" name="allow_age_to_Enrollee" id="allow_age_to_Enrollee" value="">
            <input type="hidden" name="allow_state_Enrollee" id="allow_state_Enrollee" value="">
            <input type="hidden" name="allow_zip_Enrollee" id="allow_zip_Enrollee" value="">
            <input type="hidden" name="allow_gender_Enrollee" id="allow_gender_Enrollee" value="">
            <input type="hidden" name="allow_smoking_status_Enrollee" id="allow_smoking_status_Enrollee" value="">
            <input type="hidden" name="allow_tobacco_status_Enrollee" id="allow_tobacco_status_Enrollee" value="">
            <input type="hidden" name="allow_height_by_Enrollee" id="allow_height_by_Enrollee" value="">
            <input type="hidden" name="allow_height_feet_Enrollee" id="allow_height_feet_Enrollee" value="">
            <input type="hidden" name="allow_height_inch_Enrollee" id="allow_height_inch_Enrollee" value="">
            <input type="hidden" name="allow_height_feet_to_Enrollee" id="allow_height_feet_to_Enrollee" value="">
            <input type="hidden" name="allow_height_inch_to_Enrollee" id="allow_height_inch_to_Enrollee" value="">
            <input type="hidden" name="allow_weight_by_Enrollee" id="allow_weight_by_Enrollee" value="">
            <input type="hidden" name="allow_weight_Enrollee" id="allow_weight_Enrollee" value="">
            <input type="hidden" name="allow_weight_to_Enrollee" id="allow_weight_to_Enrollee" value="">
            <input type="hidden" name="allow_no_of_children_by_Enrollee" id="allow_no_of_children_by_Enrollee" value="">
            <input type="hidden" name="allow_no_of_children_Enrollee" id="allow_no_of_children_Enrollee" value="">
            <input type="hidden" name="allow_no_of_children_to_Enrollee" id="allow_no_of_children_to_Enrollee" value="">
            <input type="hidden" name="allow_has_spouse_Enrollee" id="allow_has_spouse_Enrollee" value="">
            <input type="hidden" name="allow_spouse_age_from_Enrollee" id="allow_spouse_age_from_Enrollee" value="">
            <input type="hidden" name="allow_spouse_age_to_Enrollee" id="allow_spouse_age_to_Enrollee" value="">
            <input type="hidden" name="allow_spouse_gender_Enrollee" id="allow_spouse_gender_Enrollee" value="">
            <input type="hidden" name="allow_spouse_smoking_status_Enrollee" id="allow_spouse_smoking_status_Enrollee" value="">
            <input type="hidden" name="allow_spouse_tobacco_status_Enrollee" id="allow_spouse_tobacco_status_Enrollee" value="">
            <input type="hidden" name="allow_spouse_height_feet_Enrollee" id="allow_spouse_height_feet_Enrollee" value="">
            <input type="hidden" name="allow_spouse_height_inch_Enrollee" id="allow_spouse_height_inch_Enrollee" value="">
            <input type="hidden" name="allow_spouse_weight_Enrollee" id="allow_spouse_weight_Enrollee" value="">
            <input type="hidden" name="allow_spouse_weight_type_Enrollee" id="allow_spouse_weight_type_Enrollee" value="">
            <input type="hidden" name="allow_benefit_amount_Enrollee" id="allow_benefit_amount_Enrollee" value="">
            <input type="hidden" name="allow_in_patient_benefit_Enrollee" id="allow_in_patient_benefit_Enrollee" value="">
            <input type="hidden" name="allow_out_patient_benefit_Enrollee" id="allow_out_patient_benefit_Enrollee" value="">
            <input type="hidden" name="allow_monthly_income_Enrollee" id="allow_monthly_income_Enrollee" value="">
            <!-- <input type="hidden" name="allow_benefit_percentage_Enrollee" id="allow_benefit_percentage_Enrollee" value=""> -->
            <input type="hidden" name="pricingDataDisabled_Enrollee" id="pricingDataDisabled_Enrollee" value="N">
            <div class="clearfix"></div>
            <div class="row">
              	<div class="col-sm-6 col-md-4">
                	<div class="form-group">
                    	<select name="enrolleeMatrix" id="enrolleeMatrix" class="form-control clearPricing" >
                       		<option value="" hidden selected="selected"></option>
                                
                            <option value="Primary" <?= !empty($enrolleeType) && in_array('Primary',$enrolleeType) ? '' : 'disabled' ?>>Primary</option>
                            <option value="Spouse" <?= !empty($enrolleeType) && in_array('Spouse',$enrolleeType) ? '' : 'disabled' ?>>Spouse</option>
                            <option value="Child" <?= !empty($enrolleeType) && in_array('Child',$enrolleeType) ? '' : 'disabled' ?>>Child</option>
                                   
                        </select>
                        <label>Enrollee</label>
                        <p class="error" id="error_enrolleeMatrix"></p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6 col-md-4 1PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(1,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Age</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                        	<div class="phone-addon pr">
                                <select name="age_from_Enrollee" id="age_from_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=120;$i++){ ?>
                                    <option value="<?= $i ?>" <?= (isset($age_from) && $i==$age_from) ? 'selected=selected' : '' ?>>
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
                            	<select name="age_to_Enrollee" id="age_to_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected" ></option>
                                    <?php for($i=0;$i<=120;$i++){ ?>
                                        <option value="<?= $i ?>" <?= (isset($age_to) && $i==$age_to) ? 'selected=selected' : '' ?>>
                                          <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label>Age</label>                        	
                                
                            </div>
                        </div>
                        <p class="error" id="error_age_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3 2PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(2,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>State</strong></p>
                	<div class="form-group">
                        <select name="state_Enrollee" class="form-control clearPricing" id="state_Enrollee" data-live-search="true" >
                            <option value="" hidden selected="selected" ></option>
                            <?php if(!empty($allStateRes)){ ?>
                                <?php foreach ($allStateRes as $srow) { ?>
                                <option value="<?= $srow['name']; ?>" <?php echo isset($state) && $state == $srow['name'] ? "selected" : '' ?>><?php echo $srow['name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <label>State</label>
                        <p class="error" id="error_state_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-3 col-md-3 4PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(4,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Gender</strong></p>
                	<div class="form-group">
                        <select name="gender_Enrollee" class="form-control clearPricing" id="gender_Enrollee" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Male" <?php echo isset($gender) && $gender == 'Male' ? "selected" : '' ?> >Male</option>
                            <option value="Female" <?php echo isset($gender) && $gender == 'Female' ? "selected" : '' ?>>Female</option>
                        </select>
                        <label>Gender</label>
                        <p class="error" id="error_gender_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-12  7PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Height</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="height_by_Enrollee" id="height_by_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    
                                    <option value="Exactly" <?= !empty($height_by) && $height_by == "Exactly" ? "selected" : "" ?> >Exactly</option>
                                    <option value="Less Than" <?= !empty($height_by) && $height_by == "Less Than" ? "selected" : "" ?> >Less Than</option>
                                    <option value="Greater Than" <?= !empty($height_by) && $height_by == "Greater Than" ? "selected" : "" ?> >Greater Than</option>
                                    <option value="Range" <?= !empty($height_by) && $height_by == "Range" ? "selected" : "" ?> >Range</option>
                                </select>
                                <label>Height By</label>
                            </div>
                            <div class="phone-addon pr w-55 height_range_divEnrollee" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                From
                            </div>
                        	<div class="phone-addon pr">
                                <select name="height_feet_Enrollee" class="form-control clearPricing" id="height_feet_Enrollee" data-live-search="true" >
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
                            	<select name="height_inch_Enrollee" class="form-control clearPricing" id="height_inch_Enrollee" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($j=0; $j<=11;$j++){?>
                                    <option value="<?=$j?>" <?= isset($height_inch) && $height_inch == $j ? "selected" : '' ?> >
                                      <?= $j.' In. ';?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Inch</label>
                                
                            </div>
                            <div class="phone-addon pr w-42 height_range_divEnrollee" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                To
                            </div>
                            <div class="phone-addon pr height_range_divEnrollee" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                <select name="height_feet_to_Enrollee" class="form-control clearPricing" id="height_feet_to_Enrollee" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=1; $i<=8;$i++){?>
                                    <option value="<?=$i?>" <?php echo isset($height_feet_to) && $height_feet_to == $i ? "selected" : '' ?> >
                                      <?= $i.' Ft. ';  ?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Feet</label>
                               
                            </div>
                            <div class="phone-addon pr height_range_divEnrollee" style="<?= !empty($height_by) && $height_by == "Range" ? "" : "display: none" ?>">
                                <select name="height_inch_to" class="form-control clearPricing" id="height_inch_to_Enrollee" data-live-search="true" >
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
                         <p class="error" id="error_height_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-12  8PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Weight</strong></p>
                	<div class="form-group">
                        <div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="weight_by_Enrollee" id="weight_by_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    
                                    <option value="Exactly" <?= !empty($weight_by) && $weight_by == "Exactly" ? "selected" : "" ?>  >Exactly</option>
                                    <option value="Less Than" <?= !empty($weight_by) && $weight_by == "Less Than" ? "selected" : "" ?>  >Less Than</option>
                                    <option value="Greater Than" <?= !empty($weight_by) && $weight_by == "Greater Than" ? "selected" : "" ?>  >Greater Than</option>
                                    <option value="Range" <?= !empty($weight_by) && $weight_by == "Range" ? "selected" : "" ?>  >Range</option>
                                </select>
                                <label>Weight by</label>
                            </div>

                            <div class="phone-addon pr w-55 weight_range_divEnrollee" style="<?= !empty($weight_by) && $weight_by == "Range" ? "" : "display: none" ?>">
                                From
                            </div>
                            <div class="phone-addon pr">
                                <select name="weight_Enrollee" id="weight_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=1000;$i++) { ?>
                                        <option value="<?= $i ?>" <?= !empty($weight) && $weight == $i ? "selected" : "" ?> ><?= $i ?></option>
                                    <?php } ?>
                                </select>
                                <label>Weight</label>
                            </div>
                            <div class="phone-addon pr w-42 weight_range_divEnrollee" style="<?= !empty($weight_by) && $weight_by == "Range" ? "" : "display: none" ?>">
                                To
                            </div>
                            <div class="phone-addon pr weight_range_divEnrollee" style="<?= !empty($weight_by) && $weight_by == "Range" ? "" : "display: none" ?>">
                                <select name="weight_to_Enrollee" id="weight_to_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=1000;$i++) { ?>
                                        <option value="<?= $i ?>" <?= !empty($weight_to) && $weight_to == $i ? "selected" : "" ?> ><?= $i ?></option>
                                    <?php } ?>
                                </select>
                                <label>Weight</label>
                            </div>
                            
                            
                            
                        </div>
                        <p class="error" id="error_weight_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2 5PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(5,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Smoking Status</strong></p>
                	<div class="form-group">
                        <select name="smoking_status_Enrollee" class="form-control clearPricing" id="smoking_status_Enrollee" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($smoking_status) && $smoking_status == 'Y' ? "selected" : '' ?>>Yes</option>
                            <option value="N" <?php echo isset($smoking_status) && $smoking_status == 'N' ? "selected" : '' ?>>No</option>
                        </select>
                        <label>Smoking Status</label>
                        <p class="error" id="error_smoking_status_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 6PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(6,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Tobacco Status</strong></p>
                	<div class="form-group">
                        <select name="tobacco_status_Enrollee" class="form-control clearPricing" id="tobacco_status_Enrollee" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($tobacco_status) &&  $tobacco_status == 'Y' ? "selected" : '' ?> >Yes</option>
                            <option value="N" <?php echo isset($tobacco_status) && $tobacco_status == 'N' ? "selected" : '' ?> >No</option>
                        </select>
                        <label>Tobacco Status</label>
                        <p class="error" id="error_tobacco_status_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-12 9PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Number of Children</strong></p>
                	<div class="form-group">
                        <div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="no_of_children_by_Enrollee" id="no_of_children_by_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    
                                    <option value="Exactly" <?= !empty($no_of_children_by) && $no_of_children_by == "Exactly" ? "selected" : "" ?> >Exactly</option>
                                    <option value="Less Than" <?= !empty($no_of_children_by) && $no_of_children_by == "Less Than" ? "selected" : "" ?> >Less Than</option>
                                    <option value="Greater Than" <?= !empty($no_of_children_by) && $no_of_children_by == "Greater Than" ? "selected" : "" ?> >Greater Than</option>
                                    <option value="Range" <?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "selected" : "" ?> >Range</option>
                                </select>
                                <label>Children by</label>
                            </div>
                            <div class="phone-addon pr w-55 no_of_children_by_range_divEnrollee" style="<?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "" : "display: none" ?>">
                                From
                            </div>
                            <div class="phone-addon pr">
                                <select name="no_of_children_Enrollee" class="form-control clearPricing" id="no_of_children_Enrollee" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for ($i=0;$i<=15;$i++) { ?>
                                        <option value="<?= $i; ?>" <?php echo isset($no_of_children) && $no_of_children == $i ? "selected" : '' ?> >
                                            <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label># of Children</label>
                            </div>
                            <div class="phone-addon pr w-42 no_of_children_by_range_divEnrollee" style="<?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "" : "display: none" ?>">
                                To
                            </div>
                            <div class="phone-addon pr no_of_children_by_range_divEnrollee" style="<?= !empty($no_of_children_by) && $no_of_children_by == "Range" ? "" : "display: none" ?>">
                                <select name="no_of_children_to_Enrollee" class="form-control clearPricing" id="no_of_children_to_Enrollee" data-live-search="true" >
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

                        <p class="error" id="error_no_of_children_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2 3PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(3,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Zip Code</strong></p>
                	<div class="form-group">
                        <input type="text" id="zip_Enrollee" class="form-control clearPricing" name="zip_Enrollee" value='<?php echo isset($zip) && $zip ?>'  />
                        <label>Zip Code</label>
                        <p class="error" id="error_zip_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 10PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(10,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Has Spouse</strong></p>
                	<div class="form-group">
                        <select name="has_spouse_Enrollee" class="form-control clearPricing" id="has_spouse_Enrollee" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($has_spouse) && $has_spouse == 'Y' ? "selected" : '' ?>  >Yes</option>
                            <option value="N" <?php echo isset($has_spouse) && $has_spouse == 'N' ? "selected" : '' ?>  >No</option>
                        </select>
                        <label>Has Spouse</label>
                        <p class="error" id="error_has_spouse_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDivEnrollee 12PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(12,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Gender</strong></p>
                	<div class="form-group">
                        <select name="spouse_gender_Enrollee" class="form-control clearPricing" id="spouse_gender_Enrollee" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Male" <?php echo isset($spouse_gender) && $spouse_gender == 'Male' ? "selected" : '' ?> >Male</option>
                            <option value="Female" <?php echo isset($spouse_gender) && $spouse_gender == 'Female' ? "selected" : '' ?> >Female</option>
                        </select>
                        <label>Spouse Gender</label>
                        <p class="error" id="error_spouse_gender_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDivEnrollee 11PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(11,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouser Age</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                        	<div class="phone-addon pr">
                                <select name="spouse_age_from_Enrollee" id="spouse_age_from_Enrollee" class="form-control clearPricing" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=120;$i++){ ?>
                                        <option value="<?= $i ?>" <?= (isset($spouse_age_from) && $i==$spouse_age_from) ? 'selected' : '' ?>  >
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
                            	<select name="spouse_age_to_Enrollee" id="spouse_age_to_Enrollee" class="form-control clearPricing" >
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
                        <p class="error" id="error_spouse_age_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDivEnrollee 15PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(15,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouser Height</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                        	<div class="phone-addon pr">
                                <select name="spouse_height_feet_Enrollee" class="form-control clearPricing" id="spouse_height_feet_Enrollee" >
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
                            	<select name="spouse_height_inch_Enrollee" class="form-control clearPricing" id="spouse_height_inch_Enrollee" >
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
                        <p class="error" id="error_spouse_height_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2 spouseControlPriceDivEnrollee 16PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(16,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Weight</strong></p>
                    <div class="form-group">
                        <div class="phone-control-wrap fixed_size">
                          <div class="phone-addon pr">
                                <select name="spouse_weight_Enrollee" id="spouse_weight_Enrollee" class="form-control clearPricing" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=0;$i<=200;$i++) { ?>
                                        <option value="<?= $i ?>" <?= !empty($spouse_weight) && $spouse_weight == $i ? "selected" : "" ?> ><?= $i ?></option>
                                    <?php } ?>
                                </select>
                                
                                
                            </div>
                            <div class="phone-addon pr">
                                <select name="spouse_weight_type_Enrollee" id="spouse_weight_type_Enrollee" class="form-control clearPricing" >
                                    <option value="" hidden selected="selected"></option>
                                    <option value="lbs" <?= !empty($spouse_weight_type) && $spouse_weight_type =="lbs" ? 'selected' : '' ?> >lbs</option>
                                    <option value="kg" <?= !empty($spouse_weight_type) && $spouse_weight_type =="kg" ? 'selected' : '' ?> >Kg</option>
                                    <option value="pound" <?= !empty($spouse_weight_type) && $spouse_weight_type =="pound" ? 'selected' : '' ?> >Pound</option>
                                </select> 
                                  
                                                
                            </div>
                        </div>
                        <p class="error" id="error_spouse_weight_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDivEnrollee 13PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(13,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Smoking Status</strong></p>
                	<div class="form-group">
                        <select name="spouse_smoking_status_Enrollee" class="form-control clearPricing" id="spouse_smoking_status_Enrollee" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($spouse_smoking_status) && $spouse_smoking_status == 'Y' ? "selected" : '' ?> >Yes</option>
                            <option value="N" <?php echo isset($spouse_smoking_status) && $spouse_smoking_status == 'N' ? "selected" : '' ?> >No</option>
                        </select>
                        <label>Smoking Status</label>
                        <p class="error" id="error_spouse_smoking_status_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 spouseControlPriceDivEnrollee 14PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(14,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Spouse Tobacco Status</strong></p>
                	<div class="form-group">
                        <select name="spouse_tobacco_status_Enrollee" class="form-control clearPricing" id="spouse_tobacco_status_Enrollee" >
                            <option value="" hidden selected="selected"></option>
                            <option value="Y" <?php echo isset($spouse_smoking_status) && $spouse_tobacco_status == 'Y' ? "selected" : '' ?> >Yes</option>
                            <option value="N" <?php echo isset($spouse_smoking_status) && $spouse_tobacco_status == 'N' ? "selected" : '' ?> >No</option>
                          </select>
                          <label>Tobacco Status</label>
                          <p class="error" id="error_spouse_tobacco_status_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 17PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(17,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Benefit Amount</strong></p>
                    <div class="form-group">
                        <input type="text" id="benefit_amount_Enrollee" class="form-control formatPricing clearPricing" name="benefit_amount_Enrollee" value='<?php echo isset($benefit_amount) && $benefit_amount ?>'  />
                        <label>Benefit Amount</label>
                        <p class="error" id="error_benefit_amount_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 18PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(18,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>InPatient Benefit</strong></p>
                    <div class="form-group">
                        <input type="text" id="in_patient_benefit_Enrollee" class="form-control formatPricing clearPricing" name="in_patient_benefit_Enrollee" value='<?php echo isset($in_patient_benefit) && $in_patient_benefit ?>' />
                        <label>InPatient Benefit</label>
                        <p class="error" id="error_in_patient_benefit_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 19PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(19,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>OutPatient Benefit</strong></p>
                    <div class="form-group">
                        <input type="text" id="out_patient_benefit_Enrollee" class="form-control formatPricing clearPricing" name="out_patient_benefit_Enrollee" value='<?php echo isset($out_patient_benefit) && $out_patient_benefit ?>' />
                        <label>OutPatient Benefit</label>
                        <p class="error" id="error_out_patient_benefit_Enrollee"></p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 20PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(20,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Monthly Income</strong></p>
                    <div class="form-group">
                        <input type="text" id="monthly_income_Enrollee" class="form-control formatPricing clearPricing" name="monthly_income_Enrollee" value='<?php echo isset($monthly_income) && $monthly_income ?>' />
                        <label>Monthly Income</label>
                        <p class="error" id="error_monthly_income_Enrollee"></p>
                    </div>
                </div>
                <?php /*
                <div class="col-sm-4  21PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(21,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                	<p><strong>Benefit Percentage</strong></p>
                	<div class="form-group">
                    	<div class="phone-control-wrap fixed_size">
                            <div class="phone-addon pr">
                                <select name="benefit_percentage_Enrollee" id="benefit_percentage_Enrollee" class="form-control clearPricing" data-live-search="true" >
                                    <option value="" hidden selected="selected"></option>
                                    <?php for($i=1; $i<=100;$i++){?>
                                    <option value="<?=$i?>" <?php echo isset($benefit_percentage) && $benefit_percentage == $i ? "selected" : '' ?> ><?=$i?>
                                    </option>
                                    <?php }?>
                                </select>
                                <label>Benefit Percentage</label>
                            </div>
                        </div>
                         <p class="error" id="error_benefit_percentage_Enrollee"></p>
                    </div>
                </div>
                */?>
            </div>
            <div id="pricing_matrix_pricing_main_div_Enrollee"></div>
          
            <div class="text-right">
                <p class="error" id="error_global_matrix_arr_exits_enrollee"></p>
              	<a href="javascript:void(0);" class="btn btn-primary m-r-10" ID="addPricingMatrixEnrollee">ADD</a>
                <a href="javascript:void(0);" class="red-link" id="cancelPricingFixedEnrollee">Cancel</a>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="csv_matrix_tab_Enrollee">
            <div class="csv_matrix_tab_Enrollee">
                <h5 class="h5_title">Upload Pricing Matrix by CSV</h5>
                <input type="hidden" name="stored_file_name_Enrollee" id="stored_file_name_Enrollee" value="">
                <input type="hidden" name="is_csv_uploaded_Enrollee" id="is_csv_uploaded_Enrollee" value="N">
                <input type="hidden" name="saveCSVAs_Enrollee" id="saveCSVAs_Enrollee" value="">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="custom_drag_control"> 
                            <span class="btn btn-action" style="border-radius:0px;">Upload CSV</span>
                            <input type="file" class="gui-file" id="csv_file_Enrollee" name="csv_file_Enrollee">
                            <input type="text" class="gui-input" placeholder="">
                            <span class="error error_preview" id="error_csv_file_Enrollee"></span>
                            <span class="error error_preview" id="error_csv_fileUpload_Enrollee"></span>
                        </div>
                    </div>
                    <div class="col-sm-8 text-right">
                        <div class="d-none visible-xs m-t-15"></div>
                        <a href="<?= $PRICE_MATRIX_CSV_WEB ?>Pricing_Matrix.csv" download class="btn btn-info btn-outline">Download Template</a>
                    </div>
                </div>
                <hr>
                <div id="inline_content_Enrollee" style="display: none;">
                    <div class="col-sm-6">
                       <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Enrollee</strong></span>
                                <div class="pr">
                                    <select name="enrolleeMatrixCSV" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_enrolleeMatrixCSV"></p>
                       </div>
                    </div>

                    <div class="col-sm-6 1PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(1,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Age From</strong></span>
                                <div class="pr">
                                    <select name="age_fromCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_age_fromCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 1PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(1,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Age To</strong></span>
                                <div class="pr">
                                    <select name="age_toCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_age_toCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 2PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(2,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>State</strong></span>
                                <div class="pr">
                                    <select name="stateCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_stateCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 4PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(4,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Gender</strong></span>
                                <div class="pr">
                                    <select name="genderCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_genderCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Height BY</strong></span>
                                <div class="pr">
                                    <select name="height_byCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_byCSV_Enrollee"></p>
                        </div>
                    </div>
                    <div class="col-sm-6 7PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Height Feet</strong></span>
                                <div class="pr">
                                    <select name="height_feetCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_feetCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Height Inch</strong></span>
                                <div class="pr">
                                    <select name="height_inchCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_inchCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To Height Feet</strong></span>
                                <div class="pr">
                                    <select name="height_feet_toCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_feet_toCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 7PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(7,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To Height Inch</strong></span>
                                <div class="pr">
                                    <select name="height_inch_toCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_height_inch_toCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 8PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Weight By</strong></span>
                                <div class="pr">
                                    <select name="weight_byCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_weight_byCSV_Enrollee"></p>
                        </div>
                    </div>
                    <div class="col-sm-6 8PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Weight</strong></span>
                                <div class="pr">
                                    <select name="weightCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_weightCSV_Enrollee"></p>
                        </div>
                    </div>
                    <div class="col-sm-6 8PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(8,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To Weight</strong></span>
                                <div class="pr">
                                    <select name="weight_toCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_weight_toCSV_Enrollee"></p>
                        </div>
                    </div>

                    

                    <div class="col-sm-6 5PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(5,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Smoking Status</strong></span>
                                <div class="pr">
                                    <select name="smoking_statusCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_smoking_statusCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 6PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(6,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Tobacco Status</strong></span>
                                <div class="pr">
                                    <select name="tobacco_statusCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_tobacco_statusCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 9PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Children By</strong></span>
                                <div class="pr">
                                    <select name="no_of_children_byCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_no_of_children_byCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 9PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>No Of Children</strong></span>
                                <div class="pr">
                                    <select name="no_of_childrenCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_no_of_childrenCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 9PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(9,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>To No Of Children</strong></span>
                                <div class="pr">
                                    <select name="no_of_children_toCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_no_of_children_toCSV_Enrollee"></p>
                        </div>
                    </div>


                    <div class="col-sm-6 3PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(3,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Zip Code</strong></span>
                                <div class="pr">
                                    <select name="zipCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_zipCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 10PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(10,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Has Spouse</strong></span>
                                <div class="pr">
                                    <select name="has_spouseCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_has_spouseCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 12PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(12,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Gender</strong></span>
                                <div class="pr">
                                    <select name="spouse_genderCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_genderCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 11PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(11,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Age From</strong></span>
                                <div class="pr">
                                    <select name="spouse_age_fromCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_age_fromCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 11PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(11,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Age To</strong></span>
                                <div class="pr">
                                    <select name="spouse_age_toCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_age_toCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 15PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(15,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Height Feet</strong></span>
                                <div class="pr">
                                    <select name="spouse_height_feetCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_height_feetCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 15PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(15,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Height Inch</strong></span>
                                <div class="pr">
                                    <select name="spouse_height_inchCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_height_inchCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 16PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(16,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Weight</strong></span>
                                <div class="pr">
                                    <select name="spouse_weightCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_weightCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 16PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(16,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Weight Type</strong></span>
                                <div class="pr">
                                    <select name="spouse_weight_typeCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_weight_typeCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 13PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(13,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Smoking Status</strong></span>
                                <div class="pr">
                                    <select name="spouse_smoking_statusCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_smoking_statusCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 spouseControlPriceDivEnrollee 14PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(14,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Spouse Tobacco Status</strong></span>
                                <div class="pr">
                                    <select name="spouse_tobacco_statusCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_spouse_tobacco_statusCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 17PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(17,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Benefit Amount</strong></span>
                                <div class="pr">
                                    <select name="benefit_amountCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_benefit_amountCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 18PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(18,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>InPatient Benefit</strong></span>
                                <div class="pr">
                                    <select name="in_patient_benefitCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_in_patient_benefitCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 19PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(19,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>OutPatient Benefit</strong></span>
                                <div class="pr">
                                    <select name="out_patient_benefitCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_out_patient_benefitCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6 20PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(20,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Monthly Income</strong></span>
                                <div class="pr">
                                    <select name="monthly_incomeCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_monthly_incomeCSV_Enrollee"></p>
                        </div>
                    </div>
                    <?php /*
                    <div class="col-sm-6 21PriceDivEnrollee" style="<?= !empty($price_controlCSVMatrix) && in_array(21,$price_controlCSVMatrix) ? '' : 'display: none' ?>">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Benefit Percentage</strong></span>
                                <div class="pr">
                                    <select name="benefit_percentageCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_benefit_percentageCSV_Enrollee"></p>
                        </div>
                    </div>
                    */?>
                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Retail Price</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_priceCSVRetail_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_priceCSVRetail_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Non-Commissionable</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_priceCSVNonCommissionable_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_priceCSVNonCommissionable_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Commissionable</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_priceCSVCommissionable_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_priceCSVCommissionable_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Effective Date</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_effective_dateCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_effective_dateCSV_Enrollee"></p>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <div class="input-group">
                                <span class="input-group-addon"><strong>Termination Date</strong></span>
                                <div class="pr">
                                    <select name="pricing_matrix_termination_dateCSV_Enrollee" class="select_field" data-live-search="true">
                                        <option value="" hidden selected="selected"></option>
                                    </select>
                                    <label>Select CSV Column</label>
                                </div>
                            </div>
                            <p class="error" id="error_pricing_matrix_termination_dateCSV_Enrollee"></p>
                        </div>
                    </div>
                                    
                    <div class="m-t-15 text-right">
                        <button class="btn btn-info before_exportEnrollee" type="button" name="ImportCSV_Enrollee" id="ImportCSV_Enrollee" value="ImportCSV">Import</button>
                        <button class="btn btn-info while_exportBTNEnrollee" type="button" name="CancelCSV_Enrollee" id="CancelCSV_Enrollee" value="CancelCSV" style="display: none">Cancel CSV</button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="min-h150">
                    </div>
                        <div class="matrix_progress_wrap">
                            <div class="after_exportEnrollee phone-control-wrap" style="display: none">
                                <div id="csvError_Enrollee" class="phone-addon">
                                    <div class="progress_left_tag">
                                    <div class="phone-addon w-110">
                                    <i class="fa fa-times-circle text-action fs24" aria-hidden="true"></i> &nbsp;&nbsp; 
                                    <strong class="text-action">Error :</strong>
                                    </div>
                                    <ul id="CSVErrorList_Enrollee" class="phone-addon"></ul>
                                    </div>
                                </div>
                                <div class="phone-addon w-70 pn">
                                    <a href="javascript:void(0);" class="red-link" id="cancelPricingMatrixEnrollee">Close</a>
                                </div>
                            </div>
                            <div class="while_exportEnrollee" style="display:none">
                                <div class="loading-progress_Enrollee"></div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
