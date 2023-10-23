<script type="text/javascript">
  window.onload = function () {
  	document.querySelector('#matrix_table').addEventListener('scroll', function () {
  	  var scrollTop = this.scrollTop;
  	  this.querySelector('thead').style.transform = 'translateY(' + scrollTop + 'px)';
  	});
  }
</script>
<div class="white-box pricing_matrix_wrap">
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <h4 class="fees_bar_name mn">Pricing Matrix for <?php echo isset($resProduct['name'])?$resProduct['name']:""; ?></h4>
    </div>
    <div class="panel-body">
      <div class="cust_tab_ui">
        <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
          <li class="active" data-tab="manual_pricing"> <a data-toggle="tab" href="#manual_pricing" class="btn_step_heading" data-step="1" data-is_validate="false">
            <div class="column-step ">
              <div class="step-title">MANUAL PRICING MATRIX</div>
            </div>
          </a> </li>
          <li data-tab="matrix_csv"> <a data-toggle="tab" href="#matrix_csv" class="btn_step_heading" data-step="2" data-is_validate="false">
            <div class="column-step">
              <div class="step-title">PRICING MATRIX CSV</div>
            </div>
          </a> </li>
        </ul>
      </div>
      <div class="tab-content">
        <div id="manual_pricing" class="tab-pane fade in active ">
          <form id="frm_prd_matrix" class="form_wrap fs-13" name="frm_prd_matrix" method="POST">
            <input type="hidden" name="matrix_id" value="<?= $matrix_id ?>">
            <input type="hidden" name="product_id" id="product_id" value="<?= $product_id ?>">
            <h5 class="h5_title ">Set Price Criteria
            <i class="fa fa-info-circle text-light-black fs16"  data-toggle="tooltip" data-placement="right" title="Set demographic rules per Plan type based on data selected on previous page. "></i>
            </h5>
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Plan Tier <i class="text-light-gray">(Required)</i></label>
                  <select name="plan_type" class="form-control" id="plan_type">
                    <option value="" >--Select--</option>
                    <?php if(!empty($planTypeRows)){ ?>
                    <?php foreach ($planTypeRows as $prow) { ?>
                    <option value="<?= $prow['id']; ?>" <?php echo isset($plan_type) && $plan_type == $prow['id'] ? "selected='selected'" : '' ?>><?php echo ($prow['title']=="Member") ? 'Member Only' : $prow['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <p class="error" id="error_plan_type"></p>
                </div>
              </div>
            </div>
            <div class="row">
              <?php if(in_array("Age", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Age (Date of Birth) <i class="text-light-gray">(Required)</i></label>
                  <div class="phone-control-wrap">
                    <div class="phone-addon">
                      <select name="age_from" class="form-control ">
                        <?php for($i=1;$i<=100;$i++){ ?>
                        <option value="<?= $i ?>" <?= (isset($age_from) && $i==$age_from) ? 'selected=selected' : '' ?>>
                          <?= $i ?>
                        </option>
                        <?php } ?>
                      </select>
                      <p class="error" id="error_age_from"></p>
                    </div>
                    <div class="phone-addon text-center"> to </div>
                    <div class="phone-addon ">
                      <select name="age_to" class="form-control ">
                        <?php for($i=1;$i<=100;$i++){ ?>
                        <option value="<?= $i ?>" <?= (isset($age_to) && $i==$age_to) ? 'selected=selected' : '' ?>>
                          <?= $i ?>
                        </option>
                        <?php } ?>
                      </select>
                      <p class="error" id="error_age_to"></p>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("State", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>State <i class="text-light-gray">(Required)</i></label>
                  <select name="state" class="form-control" id="state">
                    <option value="" >Select State</option>
                    <?php if(!empty($statesRows)){ ?>
                    <?php foreach ($statesRows as $srow) { ?>
                    <option value="<?= $srow['name']; ?>" <?php echo isset($state) && $state == $srow['name'] ? "selected='selected'" : '' ?>><?php echo $srow['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <p class="error" id="error_state"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Zip Code", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Zip Code <i class="text-light-gray">(Required)</i></label>
                  <input type="text" id="zip" class="form-control number_only" name="zip" value='<?php echo isset($zip) && $zip ?>' />
                  <p class="error" id="error_zip"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Gender", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Legal Sex/Gender <i class="text-light-gray">(Required)</i></label>
                  <select name="gender" class="form-control" id="gender">
                    <option value="" >Select Gender</option>
                    <option value="Male" <?php echo isset($gender) && $gender == 'Male' ? "selected='selected'" : '' ?>>Male</option>
                    <option value="Female" <?php echo isset($gender) && $gender == 'Female' ? "selected='selected'" : '' ?>>Female</option>
                  </select>
                  <p class="error" id="error_gender"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Smoke", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Smoking <i class="text-light-gray">(Required)</i></label>
                  <select name="smoking_status" class="form-control" id="smoking_status">
                    <option value=""></option>
                    <option value="Yes" <?php echo isset($smoking_status) && $smoking_status == 'Yes' ? "selected='selected'" : '' ?>>Yes</option>
                    <option value="No" <?php echo isset($smoking_status) && $smoking_status == 'No' ? "selected='selected'" : '' ?>>No</option>
                  </select>
                  <p class="error" id="error_smoking_status"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Tobacco Use", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Tobacco Use <i class="text-light-gray">(Required)</i></label>
                  <select name="tobacco_status" class="form-control" id="tobacco_status">
                    <option value="" ></option>
                    <option value="Yes" <?php echo isset($tobacco_status) &&  $tobacco_status == 'Yes' ? "selected='selected'" : '' ?>>Yes</option>
                    <option value="No" <?php echo isset($tobacco_status) && $tobacco_status == 'No' ? "selected='selected'" : '' ?>>No</option>
                  </select>
                  <p class="error" id="error_tobacco_status"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Height", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="row">
                  <div class="col-sm-6 col-xs-6 col-md-6">
                    <div class="form-group">
                      <label>Height Feet <i class="text-light-gray">(Required)</i></label>
                      <select name="height_feet" class="form-control" id="height_feet">
                        <option value="" >Height Feet</option>
                        <?php for($i=1; $i<=8;$i++){?>
                        <option value="<?=$i?>" <?php echo isset($height_feet) && $height_feet == $i ? "selected='selected'" : '' ?>>
                          <?= $i.' Ft. ';  ?>
                        </option>
                        <?php }?>
                      </select>
                      <p class="error" id="error_height_feet"></p>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xs-6 col-md-6">
                    <div class="form-group">
                      <label>Height Inch <i class="text-light-gray">(Required)</i></label>
                      <select name="height_inch" class="form-control" id="height_inch">
                        <option value="" >Height Inch</option>
                        <?php for($j=0; $j<=11;$j++){?>
                        <option value="<?=$j?>" <?= isset($height_inch) && $height_inch == $j ? "selected='selected'" : '' ?>>
                          <?= $j.' In. ';?>
                        </option>
                        <?php }?>
                      </select>
                      <p class="error" id="error_height_inch"></p>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Weight", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Weight <i class="text-light-gray">(Required)</i></label>
                  <input type="text" id="weight" class="form-control number_only" name="weight" value='<?php echo isset($weight)?$weight:""; ?>' />
                  <p class="error" id="error_weight"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Number Of Children", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Number Of Children <i class="text-light-gray">(Required)</i></label>
                  <select name="no_of_children" class="form-control" id="no_of_children">
                    <option value="" >Select No of Children</option>
                    <?php for ($i=0;$i<=15;$i++) { ?>
                    <option value="<?= $i; ?>" <?php echo isset($no_of_children) && $no_of_children == $i ? "selected='selected'" : '' ?>>
                      <?= $i ?>
                    </option>
                    <?php } ?>
                  </select>
                  <p class="error" id="error_no_of_children"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Has Spouse", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Has Spouse <i class="text-light-gray">(Required)</i></label>
                  <select name="has_spouse" class="form-control" id="has_spouse">
                    <option value="" >Has Spouse</option>
                    <option value="Yes" <?php echo isset($has_spouse) && $has_spouse == 'Yes' ? "selected='selected'" : '' ?>>Yes</option>
                    <option value="No" <?php echo isset($has_spouse) && $has_spouse == 'No' ? "selected='selected'" : '' ?>>No</option>
                  </select>
                  <p class="error" id="error_has_spouse"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Spouse Age", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Spouse Age <i class="text-light-gray">(Required)</i></label>
                  <div class="phone-control-wrap">
                    <div class="phone-addon">
                      <select name="spouse_age_from" class="form-control ">
                        <?php for($i=1;$i<=100;$i++){ ?>
                        <option value="<?= $i ?>" <?= (isset($spouse_age_from) && $i==$spouse_age_from) ? 'selected=selected' : '' ?>>
                          <?= $i ?>
                        </option>
                        <?php } ?>
                      </select>
                      <p class="error" id="error_spouse_age_from"></p>
                    </div>
                    <div class="phone-addon text-center"> to </div>
                    <div class="phone-addon ">
                      <select name="spouse_age_to" class="form-control ">
                        <?php for($i=1;$i<=100;$i++){ ?>
                        <option value="<?= $i ?>" <?= (isset($spouse_age_to) && $i==$spouse_age_to) ? 'selected=selected' : '' ?>>
                          <?= $i ?>
                        </option>
                        <?php } ?>
                      </select>
                      <p class="error" id="error_spouse_age_to"></p>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Spouse Gender", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Spouse Gender <i class="text-light-gray">(Required)</i></label>
                  <select name="spouse_gender" class="form-control" id="spouse_gender">
                    <option value="" >Select Gender</option>
                    <option value="Male" <?php echo isset($spouse_gender) && $spouse_gender == 'Male' ? "selected='selected'" : '' ?>>Male</option>
                    <option value="Female" <?php echo isset($spouse_gender) && $spouse_gender == 'Female' ? "selected='selected'" : '' ?>>Female</option>
                  </select>
                  <p class="error" id="error_spouse_gender"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Spouse Smoke", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Spouse Smoking <i class="text-light-gray">(Required)</i></label>
                  <select name="spouse_smoking_status" class="form-control" id="spouse_smoking_status">
                    <option value="" ></option>
                    <option value="Yes" <?php echo isset($spouse_smoking_status) && $spouse_smoking_status == 'Yes' ? "selected='selected'" : '' ?>>Yes</option>
                    <option value="No" <?php echo isset($spouse_smoking_status) && $spouse_smoking_status == 'No' ? "selected='selected'" : '' ?>>No</option>
                  </select>
                  <p class="error" id="error_spouse_smoking_status"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Spouse Tobacco Use", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Spouse Tobacco Use <i class="text-light-gray">(Required)</i></label>
                  <select name="spouse_tobacco_status" class="form-control" id="spouse_tobacco_status">
                    <option value="" ></option>
                    <option value="Yes" <?php echo isset($spouse_smoking_status) && $spouse_tobacco_status == 'Yes' ? "selected='selected'" : '' ?>>Yes</option>
                    <option value="No" <?php echo isset($spouse_smoking_status) && $spouse_tobacco_status == 'No' ? "selected='selected'" : '' ?>>No</option>
                  </select>
                  <p class="error" id="error_spouse_tobacco_status"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Spouse Height", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="row">
                  <div class="col-sm-6 col-xs-6 col-md-6">
                    <div class="form-group height_auto">
                      <label>Spouse Height Feet <i class="text-light-gray">(Required)</i></label>
                      <select name="spouse_height_feet" class="form-control" id="spouse_height_feet">
                        <option value="" >Height Feet</option>
                        <?php for($i=1; $i<=8;$i++){?>
                        <option value="<?=$i?>" <?php echo isset($spouse_height_feet) && $spouse_height_feet == $i ? "selected='selected'" : '' ?>>
                          <?= $i.' Ft. ';  ?>
                        </option>
                        <?php }?>
                      </select>
                      <p class="error" id="error_spouse_height_feet"></p>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xs-6 col-md-6">
                    <div class="form-group">
                      <label>Spouse Height Inch <i class="text-light-gray">(Required)</i></label>
                      <select name="spouse_height_inch" class="form-control" id="spouse_height_inch">
                        <option value="" >Height Inch</option>
                        <?php for($j=0; $j<=11;$j++){?>
                        <option value="<?=$j?>" <?= isset($spouse_height_inch) && $spouse_height_inch == $j ? "selected='selected'" : '' ?>>
                          <?= $j.' In. ';?>
                        </option>
                        <?php }?>
                      </select>
                      <p class="error" id="error_spouse_height_inch"></p>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
              <?php if(in_array("Spouse Weight", $PrCtrl)){?>
              <div class="col-sm-4">
                <div class="form-group">
                  <label>Spouse Weight <i class="text-light-gray">(Required)</i></label>
                  <input type="text" id="spouse_weight" class="form-control number_only" name="spouse_weight" value='<?php echo isset($spouse_weight)?$spouse_weight:"" ?>' />
                  <p class="error" id="error_spouse_weight"></p>
                </div>
              </div>
              <?php } ?>
              <div class="clearfix"></div>
            </div>
            <h5 class="h5_title top_space">Plan Tier Pricing
            <i class="fa fa-info-circle text-light-black fs16"  data-toggle="tooltip" data-placement="right" title="Set pricing rules per Plan type based on data set above."></i>
            </h5>
            
            <div id="benefit_tier_pricing_main_div">
              <?php
              $prdMatrixSql="SELECT * FROM prd_matrix where id=:id AND is_deleted='N'";
              $prdMatrixRes=$pdo->selectOne($prdMatrixSql,array(":id"=>$matrix_id));
              $matrixCount=1;
              if(!empty($prdMatrixRes)){ ?>
              <div id="pricing_div_list_main_<?= $prdMatrixRes['matrix_group'] ?>" class="pricing_matrix_count m-t-15" data-matrix_group="<?= $prdMatrixRes['matrix_group'] ?>">
                <div id="pricing_div_list_<?= $prdMatrixRes['matrix_group'] ?>" class="pricing_div_list">
                  <div class="row">
                    <div class="col-sm-6 col-md-4">
                      <div class="form-group">
                        <label>Price <i class="text-light-gray">(Required)</i></label>
                        <div class="input-group">
                          <span class="input-group-addon">$</span>
                          <input name="pricing_matrix[<?= $prdMatrixRes['matrix_group'] ?>][Sale]" id="pricing_matrix_Sale_<?= $prdMatrixRes['matrix_group'] ?>" data-matrix_group="<?= $prdMatrixRes['matrix_group'] ?>"  type="text" class="form-control priceControl sale_pricing_matrix" placeholder="$000.00" onkeypress="return isNumber(event)" value="<?= $prdMatrixRes['price'] ?>" <?= (!empty($prdMatrixRes['pricing_termination_date']) && strtotime(date('Y-m-d',strtotime($prdMatrixRes['pricing_termination_date']))) < strtotime($todayDate)) ? 'readonly' : '' ?>>
                        </div>
                        <p class="error" id="error_pricing_matrix_<?= $prdMatrixRes['matrix_group'] ?>"></p>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-group">
                        <label>Non Commissionable Amount  <i class="text-light-gray">(Required)</i></label>
                        <div class="input-group">
                          <span class="input-group-addon">$</span>
                          <input name="pricing_matrix[<?= $prdMatrixRes['matrix_group'] ?>][NonCommissionable]" id="pricing_matrix_NonCommissionable_<?= $prdMatrixRes['matrix_group'] ?>" data-matrix_group="<?= $prdMatrixRes['matrix_group'] ?>"  type="text" class="form-control priceControl non_commissionable_pricing_matrix" placeholder="$000.00" onkeypress="return isNumber(event)" value="<?= $prdMatrixRes['non_commission_amount'] ?>" <?= (!empty($prdMatrixRes['pricing_termination_date']) && strtotime(date('Y-m-d',strtotime($prdMatrixRes['pricing_termination_date']))) < strtotime($todayDate)) ? 'readonly' : '' ?>>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-group">
                        <label>Commissionable Price  <i class="text-light-gray">(Required)</i></label>
                        <div class="input-group">
                          <span class="input-group-addon">$</span>
                          <input name="pricing_matrix[<?= $prdMatrixRes['matrix_group'] ?>][Commissionable]" id="pricing_matrix_Commissionable_<?= $prdMatrixRes['matrix_group'] ?>" data-matrix_group="<?= $prdMatrixRes['matrix_group'] ?>"  type="text" class="form-control priceControl commissionable_pricing_matrix" placeholder="$000.00" onkeypress="return isNumber(event)" value="<?= $prdMatrixRes['commission_amount'] ?>" readonly="">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div id="pricing_date_div_<?= $prdMatrixRes['matrix_group'] ?>" class="m-t-20">
                  <div class="row">
                    <div class="col-sm-6 col-md-3">
                      <div class="form-group">
                        <label>Pricing Effective Date <i class="text-light-gray">(Required)</i></label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          <input name="pricing_effective_date[<?= $prdMatrixRes['matrix_group'] ?>]" id="pricing_effective_date_<?= $prdMatrixRes['matrix_group'] ?>" type="text" class="form-control pricing_date pricing_effective_date"  placeholder="MM/DD/YYYY" value="<?= !empty($prdMatrixRes['pricing_effective_date']) ? date('m/d/Y',strtotime($prdMatrixRes['pricing_effective_date'])) : date('m/d/Y',strtotime($productActiveEffectiveDate)) ?>" data-matrix_group="<?= $prdMatrixRes['matrix_group'] ?>" readonly="">
                        </div>
                        <p class="error" id="error_pricing_effective_date_<?= $prdMatrixRes['matrix_group'] ?>"></p>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="form-group">
                        <label>Pricing Termination Date</label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          <input name="pricing_termination_date[<?= $prdMatrixRes['matrix_group'] ?>]" id="pricing_termination_date_<?= $prdMatrixRes['matrix_group'] ?>" type="text" class="form-control pricing_date pricing_termination_date"  placeholder="MM/DD/YYYY"  value="<?= !empty($prdMatrixRes['pricing_termination_date']) ? date('m/d/Y',strtotime($prdMatrixRes['pricing_termination_date'])) : '' ?>" data-matrix_group="<?= $prdMatrixRes['matrix_group'] ?>" <?= (!empty($prdMatrixRes['pricing_termination_date']) && strtotime(date('Y-m-d',strtotime($prdMatrixRes['pricing_termination_date']))) < strtotime($todayDate)) ? 'readonly' : '' ?>>
                        </div>
                        <p class="error" id="error_pricing_termination_date_<?= $prdMatrixRes['matrix_group'] ?>"></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
            </div>
            <div class="clearfix"></div>
            <div id="setNewPricingDiv" style="<?= !empty($prdMatrixRes['pricing_termination_date']) ? '' : 'display: none' ?>">
              <div class="form-group">
                <a href="javascript:void(0);" id="addNewPricing" class="btn btn-default">Set New Pricing</a>
              </div>
            </div>
            <div class="form-group m-t-20">
              <button type="button" name="save" id="save" class="btn btn-icon btn-info">
              <?= ($matrix_id==0) ? 'Add' : 'Update' ?>
              </button>
              <button type="button" name="cancel" id="cancel" onclick="window.parent.$.colorbox.close()" class="btn btn-icon btn-default">Cancel</button>
            </div>
          </form>
          <div class="clearfix"></div>
          
          <?php if($matrix_id==0) { ?>
          <div class="tbl-header" id="matrix_table">
            <table class="<?=$table_class?> tbl_preform"  width="100%" >
              <thead>
                <tr>
                  <th>Plan Tier</th>
                  <?php if(!empty($PrCtrl)){ ?>
                    <?php foreach ($PrCtrl as $key => $value) { ?>
                      <th><?= $value ?></th>
                    <?php } ?>
                  <?php } ?>
                  <th>Price</th>
                  <th>Non Commissionable <br />Price</th>
                  <th>Commissionable <br />Price</th>
                  <th class="text-right">Effective Date</th>
                  <th class="text-right">Termination Date</th>
                  <th class="text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($matrixRows)) { ?>
                <?php foreach ($matrixRows as $row) { ?>
                  <?php
                    $benefit_tier=getname('prd_plan_type',$row['plan_type'],'title','id');
                    if($benefit_tier=="Member"){
                      $benefit_tier = "Member Only";
                    }
                  ?>
                <tr>
                  <td><?= $benefit_tier ?></td>
                  <?php if(!empty($PrCtrl)){ ?>
                    <?php foreach ($PrCtrl as $key => $value) { ?>
                      <?php if(("Age" == $value)){
                      $pricrControl = $row['age_from'].'-'.$row['age_to'];
                      }if(("State" == $value)){
                        $pricrControl = $row['state'];
                      }if(("Zip Code" == $value)){
                        $pricrControl = $row['zip'];
                      }if(("Gender" == $value)){
                        $pricrControl = $row['gender'];
                      }if(("Smoke" == $value)){
                        $pricrControl = $row['smoking_status'];
                      }if(("Tobacco Use" == $value)){
                        $pricrControl = $row['tobacco_status'];
                      }if(("Height" == $value)){
                        $pricrControl = $row['height_feet'].' Ft. '.$row['height_inch'] .' In.';
                      }if(("Weight" == $value)){
                        $pricrControl = $row['weight'];
                      }if(("Number Of Children" == $value)){
                        $pricrControl = $row['no_of_children'];
                      }if(("Has Spouse" == $value)){
                        $pricrControl = $row['has_spouse'];
                      }if(("Spouse Age" == $value)){
                        $pricrControl = $row['spouse_age_from'].'-'.$row['spouse_age_to'];
                      }if(("Spouse Gender" == $value)){
                        $pricrControl = $row['spouse_gender'];
                      }if(("Spouse Smoke" == $value)){
                        $pricrControl = $row['spouse_smoking_status'];
                      }if(("Spouse Tobacco Use" == $value)){
                        $pricrControl = $row['spouse_tobacco_status'];
                      }if(("Spouse Height" == $value)){
                        $pricrControl = $row['spouse_height_feet'].' Ft. '.$row['spouse_height_inch'] .' In.';
                      }if(("Spouse Weight" == $value)){
                        $pricrControl = $row['spouse_weight'];
                      } ?>
                      <td><?= $pricrControl ?></td>
                    <?php } ?>
                  <?php } ?>
                  <td><?php echo displayAmount($row['price'],2,"USA"); ?></td>
                  <td><?php echo displayAmount($row['non_commission_amount'],2,"USA"); ?></td>
                  <td><?php echo displayAmount($row['commission_amount'],2,"USA"); ?></td>
                  <td class="text-right"><?php echo (!empty($row['pricing_effective_date'])) ? date('m/d/Y',strtotime($row['pricing_effective_date'])) : '-' ?></td>
                  <td class="text-right"><?php echo (!empty($row['pricing_termination_date'])) ? date('m/d/Y',strtotime($row['pricing_termination_date'])) : '-' ?></td>
                  <td class="icons text-right" ><a href="pricing_matrix.php?product_id=<?= $product_id ?>&id=<?= $row['id'] ?>"  class="edit_popup" title="Edit Product Price" data-toggle="tooltip" ><i class="fa fa-edit"></i></a> <a href="javascript:void(0)" data-product="<?= $product_id ?>" data-matrix="<?= $row['id'] ?>" title="Delete Product Price" data-toggle="tooltip" class="deletePriceMatrix"><i class="fa fa-trash"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td colspan="7" class="text-center">No Records Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <div class="col-sm-12 m-t-15">
            <button type="button"  class="btn btn-icon btn-info close_popup">
            Close
            </button>
          </div>
          <?php } ?>
        </div>
        <div id="matrix_csv" class="tab-pane fade ">
          <form id="frm_prd_matrix_csv"  name="frm_prd_matrix_csv" method="POST">
            <h5 class="h5_title">Upload Pricing Matrix CSV </h5>
            <input type="hidden" name="save_as" id="save_as" value="">
            <input type="hidden" name="is_csv_uploaded" id="is_csv_uploaded" value="N">
            <input type="hidden" name="product_id" id="product_id" value="<?= $product_id ?>">
            <div class="form-group m-b-15">
              <div class="col-sm-6">
                <div class="custom_drag_control">
                  <span class="btn btn-info btn-sm">Select File</span>
                  <input type="file" class="gui-file" id="csv_file" name="csv_file">
                  <input type="text" class="gui-input" placeholder="Drag or Select File">
                  <span class="error error_preview" id="error_csv_file"></span>
                  <span class="error error_preview" id="error_csv_fileUpload"></span>
                </div>
              </div>
              <button class="btn btn-info importCSV" type="button" name="uploadCSV " id="uploadCSV" value="uploadCSV" style="display: none">Upload CSV</button>
              <a href="<?= $PRICE_MATRIX_CSV_WEB ?>Pricing_Matrix.csv" download class="btn btn-default">Download Template</a>
            </div>
            <div id="inline_content" style="display: none;">
              <h5 class="h5_title m-t-20">Set Matrix</h5>
              <div class="row">
                <div class="col-sm-6">
                  <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <thead>
                      <tr>
                        <th class="text-center">Pricing Column</th>
                        <th class="text-center">Select CSV Column</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td colspan="2">
                          <div class="form-group pri_matrix_addon">
                            <div class="input-group w-100">
                              <span class="input-group-addon">Plan Tier</span>
                              <select id="plan_type_csv" name="plan_type" class="select_field form-control">
                                <option value="">Select Fields</option>
                              </select>
                              <span class="error error_preview" id="error_plan_type"></span>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <?php $field_count=2; ?>
                <?php if(!empty($field)) { ?>
                  <?php foreach ($field as $key => $row) { ?>
                  <?php
                  $input_field=$row['input_field'];
                  $display_name=$row['display_field'];
                  $prc_cntrl = $row['price_control_field'];
                  ?>
                    <?php if(in_array($prc_cntrl, $PrCtrl)) { ?>
                      <div class="col-sm-6">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                          <?php if($field_count==1 || $field_count == 2) { ?>
                          <thead>
                            <tr>
                              <th class="text-center">Pricing Column</th>
                              <th class="text-center">Select CSV Column</th>
                            </tr>
                          </thead>
                          <?php } ?>
                          <tbody>
                            <tr>
                              <td colspan="2">
                                <div class="form-group pri_matrix_addon">
                                  <div class="input-group w-100">
                                    <span class="input-group-addon"><?= $display_name ?></span>
                                    <select id="<?= $input_field ?>" name="<?= $input_field ?>" class="select_field form-control">
                                      <option value="">Select Fields</option>
                                    </select>
                                    <span class="error error_preview" id="error_<?= $input_field ?>"></span>
                                  </div>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    <?php $field_count++; ?>
                    <?php } ?>
                  <?php } ?>
                <?php } ?>
                <div class="col-sm-6">
                  <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <?php if($field_count==1 || $field_count == 2) { ?>
                    <thead>
                      <tr>
                        <th class="text-center">Pricing Column</th>
                        <th class="text-center">Select CSV Column</th>
                      </tr>
                    </thead>
                    <?php } ?>
                    <tbody>
                      <tr>
                        <td colspan="2">
                          <div class="form-group pri_matrix_addon">
                            <div class="input-group w-100">
                              <span class="input-group-addon">Price</span>
                              <select id="price_csv" name="price" class="select_field form-control">
                                <option value="">Select Fields</option>
                              </select>
                              <span class="error error_preview" id="error_price"></span>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-sm-6">
                  <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tbody>
                      <tr>
                        <td colspan="2">
                          <div class="form-group pri_matrix_addon">
                            <div class="input-group w-100">
                              <span class="input-group-addon">Non Commissionable Amount</span>
                              <select id="non_commissionable_csv" name="non_commissionable" class="select_field form-control">
                                <option value="">Select Fields</option>
                              </select>
                              <span class="error error_preview" id="error_non_commissionable"></span>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                    <a href="javascript:void(0);" class="btn btn-info" id="view_matrix">View Matrix</a>
                  </div>
                </div>
              </div>
            </div>
            <div id="matrix_div" style="display: none;">
              <div id="matrix_div_html">
              </div>
              
              <div class="col-sm-12 m-t-15">
                <button class="btn btn-info importCSV" type="button" name="save" id="save" value="Save">Save</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div id="pricing_div_list_main_dynamic_div" style="display: none">
    <div id="pricing_div_list_main_~matrix_group~" class="pricing_matrix_count m-t-20" data-matrix_group="~matrix_group~">
      <div id="copy_previous_div_~matrix_group~">
        <h5 class="h5_title top_space">Set New Pricing <a href="javascript:void(0)" class="copy_previous_data" data-matrix_group="~matrix_group~" data-toggle="tooltip" title="Copy">
          <i class="fa fa-copy"></i>
        </a>
        <a href="javascript:void(0);" class="remove_pricing_div_list_main pull-right" data-removeId="~matrix_group~" data-id=''><i class="fa fa-times"></i></a>
        </h5>
      </div>
      <div id="pricing_div_list_~matrix_group~" class="pricing_div_list">
        
      </div>
      <div class="clearfix"></div>
      <div id="pricing_date_div_~matrix_group~" class="m-t-20">
        <div class="row">
          <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label>Pricing Effective Date <i class="text-light-gray">(Required)</i></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input name="pricing_effective_date[~matrix_group~]" id="pricing_effective_date_~matrix_group~" type="text" class="form-control pricing_date pricing_effective_date"  placeholder="00 / 00 / 0000" value="" data-matrix_group="~matrix_group~" readonly="">
              </div>
              <p class="error" id="error_pricing_effective_date_~matrix_group~"></p>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label>Pricing Termination Date</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input name="pricing_termination_date[~matrix_group~]" id="pricing_termination_date_~matrix_group~" type="text" class="form-control pricing_date pricing_termination_date"  placeholder="00 / 00 / 0000"  value="" data-matrix_group="~matrix_group~">
              </div>
              <p class="error" id="error_pricing_termination_date_~matrix_group~"></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="pricing_dynamic_div" style="display: none">
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <label>Price <i class="text-light-gray">(Required)</i></label>
          <div class="input-group"> <span class="input-group-addon">$</span>
          <input type="text" name="pricing_matrix[~matrix_group~][Sale]" id="pricing_matrix_Sale_~matrix_group~" class="form-control number_only priceControl sale_pricing_matrix" value="" data-matrix_group="~matrix_group~" placeholder="$000.00" onkeypress="return isNumber(event)"/>
          
        </div>
        <p class="error" id="error_pricing_matrix_~matrix_group~"></p>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <label>Non Commissionable Amount <i class="text-light-gray">(Required)</i></label>
        <div class="input-group"> <span class="input-group-addon">$</span>
        <input type="text" name="pricing_matrix[~matrix_group~][NonCommissionable]" id="pricing_matrix_NonCommissionable_~matrix_group~" class="form-control number_only priceControl non_commissionable_pricing_matrix" value="" data-matrix_group="~matrix_group~" placeholder="$000.00" onkeypress="return isNumber(event)"/>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="form-group">
      <label>Commissionable Price <i class="text-light-gray">(Required)</i></label>
      <div class="input-group"> <span class="input-group-addon">$</span>
      <input type="text" name="pricing_matrix[~matrix_group~][Commissionable]" id="pricing_matrix_Commissionable_~matrix_group~" class="form-control number_only priceControl commissionable_pricing_matrix" value="" readonly="" data-matrix_group="~matrix_group~" placeholder="$000.00" onkeypress="return isNumber(event)"/>
    </div>
  </div>
</div>
</div>
</div>

<script type="text/javascript">


  $(document).ready(function() {

    $(".pricing_date").mask("99/99/9999");
    $matrix_id = '<?= $matrix_id ?>';
    $matrix_group_count = '<?= $matrix_group_count ?>';
    if ($matrix_id == 0) {
      addBenefitTierPricingDiv();
    }
    $(".number_only").keypress(function(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && charCode != 46 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      return true;
    });
    $('.priceControl').priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
    $("#csv_file").change(function() {
      var filename = $('#csv_file').val();
      if (filename != '') {
        $('.select_field').html('<option value="">Select Fields</option>');
        $("#is_csv_uploaded").val('N');
        $("#uploadCSV").show();
      }
    });
  });

  $(document).on("change", "#plan_type", function() {
    $val = $(this).val();

    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_get_pricing_effective_date.php',
      dataType: 'JSON',
      type: 'POST',
      data: {
        plan: $val,
        product_id: $("#product_id").val()
      },
      success: function(res) {
        $("#ajax_loader").hide();
        $(".pricing_effective_date:visible").first().val(res.effectiveDate);
      }
    })
  });

  $(document).on('click', ".deletePriceMatrix", function() {
    $product_id = $(this).attr('data-product');
    $matrix_id = $(this).attr('data-matrix');
    swal({
      text: 'Delete Price: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_delete_matrix_price.php',
        dataType: 'JSON',
        data: {
          id: $matrix_id
        },
        type: 'POST',
        success: function(res) {
          window.location.href = "pricing_matrix.php?product_id=" + $product_id;
        }
      });

    }, function(dismiss) {
      window.location.href = "pricing_matrix.php?product_id=" + $product_id;
    });
  });

  $(document).on("click", ".close_popup", function() {
    window.parent.$.colorbox.close();
  });

  $(document).on("click", "#addNewPricing", function() {
    addBenefitTierPricingDiv();
  });

  $(document).on("click", ".remove_pricing_div_list_main", function() {
    $removeId = $(this).attr('data-removeId');
    $id = $(this).attr('data-id');

    if ($id != '') {
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_prd_remove_pricing_group.php',
        dataType: 'JSON',
        type: 'POST',
        data: {
          id: $id,
          product_id: $("#product_id").val()
        },
        success: function(res) {
          if (res.status == 'success') {
            $("#ajax_loader").hide();
            setNotifySuccess('Pricing Removed Successfully');
          }
        }
      })
    }
    $prevTermDate = $("#pricing_div_list_main_" + $removeId).prev('.pricing_matrix_count').find('.pricing_termination_date').val();
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_prd_pricing_effective_date.php',
      dataType: 'JSON',
      data: {
        date: $prevTermDate
      },
      type: 'POST',
      success: function(res) {
        $nextTermDate = $("#pricing_div_list_main_" + $removeId).next('.pricing_matrix_count').find('.pricing_effective_date').val(res.date);

        $("#pricing_div_list_main_" + $removeId).remove();
        checkPricingTermDate();
        $("#ajax_loader").hide();
      }
    });
  });
  $(document).on("click", ".copy_previous_data", function() {
    $matrix_group = $(this).attr('data-matrix_group');

    $prev_matrixGroup = $("#pricing_div_list_main_" + $matrix_group).prev('.pricing_matrix_count').attr('data-matrix_group');

    $prevSalePrice = $("#price_" + $prev_matrixGroup).val();
    $prevNonCommissionablePrice = $("#non_commissionable_" + $prev_matrixGroup).val();
    $prevCommissionablePrice = $("#commissionable_" + $prev_matrixGroup).val();

    $("#price_" + $matrix_group).val($prevSalePrice);
    $("#non_commissionable_" + $matrix_group).val($prevNonCommissionablePrice);
    $("#commissionable_" + $matrix_group).val($prevCommissionablePrice);



    $prevTermDate = $("#pricing_div_list_main_" + $matrix_group).prev('.pricing_matrix_count').find('.pricing_termination_date').val();;
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_prd_pricing_effective_date.php',
      dataType: 'JSON',
      data: {
        date: $prevTermDate
      },
      type: 'POST',
      success: function(res) {
        $nextTermDate = $("#pricing_div_list_main_" + $matrix_group).find('.pricing_effective_date').val(res.date);
        $("#pricing_div_list_main_" + $matrix_group).find('.pricing_termination_date').val($prevTermDate);
        checkPricingTermDate();
        $("#ajax_loader").hide();
      }
    });
  });

  $(document).on("blur", ".pricing_termination_date", function() {
    $val = $(this).val();
    $matrix_group = $(this).attr('data-matrix_group');
    $('.error').html('');
    if ($(this).mask("99/99/9999").val().length > 0) {
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_prd_pricing_effective_date.php',
        dataType: 'JSON',
        data: {
          date: $val
        },
        type: 'POST',
        success: function(res) {
          $nextTermDate = $("#pricing_div_list_main_" + $matrix_group).next('.pricing_matrix_count').find('.pricing_effective_date').val(res.date);
          $("#ajax_loader").hide();
        }
      });
    } else {
      $nextTermDate = $("#pricing_div_list_main_" + $matrix_group).next('.pricing_matrix_count').find('.pricing_effective_date').val('');
    }
    checkPricingTermDate();
  });

  $(document).on("blur", ".sale_pricing_matrix", function() {
    $matrix_group = $(this).attr('data-matrix_group');

    $non_commissionable_price = $("#pricing_matrix_NonCommissionable_" + $matrix_group).val();
    $price = $("#pricing_matrix_Sale_" + $matrix_group).val();

    $non_commissionable_price = $non_commissionable_price.replace(",", "");
    $price = $price.replace(",", "");

    $non_commissionable_price = parseFloat($non_commissionable_price);
    $price = parseFloat($price);
    $commissionable_price = ($price - $non_commissionable_price).toFixed(2);

    if ($commissionable_price < 0) {
      swal({
        text: "Error: Please Enter Valid Price",
      }).then(function() {

      });
      $("#pricing_matrix_Commissionable_" + $matrix_group).val('0.00');
    } else {
      $("#pricing_matrix_Commissionable_" + $matrix_group).val($commissionable_price);
    }

    $("input[name='pricing_matrix[" + $matrix_group + "][Sale]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
    $("input[name='pricing_matrix[" + $matrix_group + "][Commissionable]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
    $("input[name='pricing_matrix[" + $matrix_group + "][NonCommissionable]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
  });

  $(document).on("blur", ".non_commissionable_pricing_matrix", function() {
    $matrix_group = $(this).attr('data-matrix_group');

    $non_commissionable_price = $("#pricing_matrix_NonCommissionable_" + $matrix_group).val();
    $price = $("#pricing_matrix_Sale_" + $matrix_group).val();

    $non_commissionable_price = $non_commissionable_price.replace(",", "");
    $price = $price.replace(",", "");

    $non_commissionable_price = parseFloat($non_commissionable_price);
    $price = parseFloat($price);
    $commissionable_price = ($price - $non_commissionable_price).toFixed(2);

    if ($commissionable_price < 0) {
      swal({
        text: "Error: Please Enter Valid Price",
      }).then(function() {

      });
      $("#pricing_matrix_Commissionable_" + $matrix_group).val('0.00');
    } else {
      $("#pricing_matrix_Commissionable_" + $matrix_group).val($commissionable_price);
    }
    $("input[name='pricing_matrix[" + $matrix_group + "][Sale]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
    $("input[name='pricing_matrix[" + $matrix_group + "][Commissionable]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
    $("input[name='pricing_matrix[" + $matrix_group + "][NonCommissionable]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
  });

  addBenefitTierPricingDiv = function() {
    $matrix_group_count = parseInt($matrix_group_count) + 1;
    $matrix_group = $matrix_group_count;
    $main_html = $("#pricing_div_list_main_dynamic_div").html();
    $main_html = $main_html.replace(/~matrix_group~/g, $matrix_group);
    $("#benefit_tier_pricing_main_div").append($main_html);
    $(".pricing_date").mask("99/99/9999");

    if ($("#frm_prd_matrix .pricing_matrix_count").length == 1) {
      $("#pricing_div_list_" + $matrix_group + " .remove_pricing_div_list_main").remove();
      $("#pricing_effective_date_" + $matrix_group).val('<?= $productActiveEffectiveDate ?>');
      $("#copy_previous_div_" + $matrix_group).remove();
    } else {
      $prevTermDate = $("#pricing_div_list_main_" + $matrix_group).prev('.pricing_matrix_count').find('.pricing_termination_date').val();
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_prd_pricing_effective_date.php',
        dataType: 'JSON',
        data: {
          date: $prevTermDate
        },
        type: 'POST',
        success: function(res) {
          $("#pricing_effective_date_" + $matrix_group).val(res.date);
          $("#ajax_loader").hide();
        }
      });
    }

    checkPricingTermDate();
    addpricing_matrix($matrix_group);

  }
  checkPricingTermDate = function() {
    $last_pricing_term_date_html = $("#frm_prd_matrix .pricing_termination_date").last();
    $last_pricing_term_date = $("#frm_prd_matrix .pricing_termination_date").last().val();
    $("#setNewPricingDiv").hide();

    if ($last_pricing_term_date_html.mask("99/99/9999").val().length > 0) {
      $.ajax({
        url: 'ajax_prd_pricing_effective_date.php',
        dataType: 'JSON',
        data: {
          date: $last_pricing_term_date
        },
        type: 'POST',
        success: function(res) {
          if (res.data_type != 'Past Date') {
            $("#setNewPricingDiv").show();
          }
          $("#ajax_loader").hide();
        }
      });

    }
  }

  addpricing_matrix = function($matrix_group) {
    html = $('#pricing_dynamic_div').html();
    html = html.replace(/~matrix_group~/g, $matrix_group);

    $('#pricing_div_list_' + $matrix_group).append(html);

    $("input[name='pricing_matrix[" + $matrix_group + "][Sale]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
    $("input[name='pricing_matrix[" + $matrix_group + "][Commissionable]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });
    $("input[name='pricing_matrix[" + $matrix_group + "][NonCommissionable]'").priceFormat({
      prefix: '',
      suffix: '',
      centsSeparator: '.',
      thousandsSeparator: ',',
      limit: false,
      centsLimit: 2,
    });

  }

  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && charCode != 46 && charCode != 47 && (charCode < 48 || charCode > 57)) {
      return false;
    }
    return true;
  }

  $(document).on("click", "#save", function() {
    $("#frm_prd_matrix").submit();
  });

  $('#frm_prd_matrix').ajaxForm({
    type: "POST",
    dataType: 'JSON',
    beforeSubmit: function(arr, $form, options) {
      $("#ajax_loader").show();

    },
    url: 'ajax_prd_pricing_add.php',
    success: function(res) {

      if (res.status == 'fail') {
        var is_error = true;
        $.each(res.errors, function(index, error) {
          $('#error_' + index).html(error).show();
          if (is_error) {
            var offset = $('#error_' + index).offset();
            var offsetTop = offset.top;
            var totalScroll = offsetTop - 50;
            $('body,html').animate({
              scrollTop: totalScroll
            }, 1200);
            is_error = false;
          }
        });
      } else if (res.status == 'success') {
        window.location.href = "pricing_matrix.php?product_id=" + res.product_id;
      }
      $("#ajax_loader").hide();
    }
  });
  //********************* CSV CODE Start ***************************
  $(document).on('click', '.importCSV', function(e) {
    $("span.error").html('');
    if ($("#is_csv_uploaded").val() == 'N') {
      e.preventDefault();
      var save_as = $(this).val();
      if (save_as != '') {
        $("#save_as").val(save_as);

        $ajaxUrl = 'ajax_prd_pricing_import.php';

        $("#frm_prd_matrix_csv").attr('action', $ajaxUrl);
        $("#ajax_loader").show();
        $("#frm_prd_matrix_csv").submit();
      }

    } else {
      $("#save_as").val('');
      var $ajaxUrl = 'ajax_prd_pricing_import.php';
      $("#frm_prd_matrix_csv").attr('action', $ajaxUrl);
      $("#ajax_loader").show();
      $("#frm_prd_matrix_csv").submit();
    }
  });

  $('#frm_prd_matrix_csv').ajaxForm({
    type: "POST",
    dataType: 'JSON',
    success: function(res) {
      if (res.csv_data) {
        $("#inline_content").show('slow');
        $("#uploadCSV").hide('slow');
        $.each(res.csv_data, function(key, val) {
          $('.select_field').append('<option value="' + val + '">' + val + '</option>');
        });
        $("#is_csv_uploaded").val('Y');
      }
      if (res.status == 'fail') {
        var is_error = true;
        $.each(res.errors, function(index, error) {
          $('#frm_prd_matrix_csv #error_' + index).html(error).show();
          if (is_error) {
            var offset = $('#error_' + index).offset();
            var offsetTop = offset.top;
            var totalScroll = offsetTop - 50;
            $('body,html').animate({
              scrollTop: totalScroll
            }, 1200);
            is_error = false;
          }
        });
      } else if (res.status == 'success') {
        setNotifySuccess('Pricing Matrix CSV added successfully.');
        window.location.reload();
      }
      $("#ajax_loader").hide();
    }
  });

  $(document).on("click", "#view_matrix", function() {
    $("span.error").html('');
    $("#matrix_div").show();
    $("#matrix_div_html").html('');
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_prd_pricing_csv_matrix_load.php',
      data: $("#frm_prd_matrix_csv").serialize(),
      dataType: 'JSON',
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        $("#matrix_div_html").html(res.html);

      }
    });
  });
  //********************* CSV CODE End   ***************************
</script>