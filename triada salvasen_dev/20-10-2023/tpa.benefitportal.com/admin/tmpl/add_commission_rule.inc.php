<?php if(!empty($parentCommission)){ ?>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18 fw500 mn"><?= empty($commission) ? 'New' : 'Edit' ?> Commission Variation <span class="fw300">(<?= $variation_res['name'] ?>)</span> <a href="variation_detail.php?commission=<?= $parentCommission ?>" class="btn red-link pull-right">Back</a></p>
    </div>
  </div>
</div>
<?php } ?>
<form action="" role="form" method="post" class="uform" name="commission_rule_form" id="commission_rule_form" enctype="multipart/form-data">
  <input type="hidden" name="parentCommission" id="parentCommission" value="<?= $parentCommission ?>">
  <input type="hidden" name="commission" id="commission" value="<?= $commission ?>">
  <input type="hidden" name="is_clone" id="is_clone" value="<?= $is_clone ?>">
  <div class="panel panel-default panel-block theme-form">
    <div class="panel-body ">
      <h4 class="pull-left m-t-0 fw500">Commission Details</h4>
      <div class="clearfix"></div>
      <p class="m-t-20">Select the product you would like to pay a commission on:</p>
      
      <div class="row m-t-15 ">
        <div class="col-lg-4">
          <div class="form-group">
            <select name="product" id="product" class="form-control">
              <option data-hidden="true"></option>
              <?php if(!empty($company_arr)){ ?>
                <?php foreach ($company_arr as $companyName=>$companyProduct) { ?>
                  <optgroup label='<?= $companyName ?>'>
                    <?php foreach ($companyProduct as $key => $product) { ?>
                      <option value="<?= $product['id'] . ',' . $product['record_type'] .','.$product['parent_product_id']; ?>" <?= $product['id'] == $product_id ? 'selected' : '' ?> data-planTypes="<?=checkIsset($product['planTypes'])?>">
                        <?php echo $product['name'] . "  (" . $product['product_code'] . ")" . " "; ?> 
                      </option>
                    <?php } ?>
                  </optgroup>
                <?php } ?>
              <?php } ?>
            </select>
            <label>Select Product</label>
            <p class="error"><span id="error_product"></span></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <input type="text" class="form-control" name="display_id" id="display_id" value="<?=!empty($display_id) ? $display_id : '' ?>">
            <label>Commission ID (Must be unique)</label>
            <p class="error"><span id="error_display_id"></span></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <select class="form-control" name="commission_status" id="commission_status">
              <option hidden selected="selected"></option>
              <option value="Active" <?= !empty($commission_status) && $commission_status == "Active" ? 'selected' : '' ?>>Active</option>
              <option value="Inactive" <?= !empty($commission_status) && $commission_status == "Inactive" ? 'selected' : '' ?>>Inactive</option>
            </select>
            <label>Status</label>
            <p class="error"><span id="error_commission_status"></span></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <select class="form-control" name="commission_type" id="commission_type">
              <option hidden selected="selected"></option>
              <option value="Agent Level" <?= !empty($commission_type) && $commission_type=="Agent Level" ? 'selected' : '' ?>>Agent Level</option>
              <option value="Plan" <?= !empty($commission_type) && $commission_type=="Plan" ? 'selected' : '' ?>>Plan Tier</option>
            </select>
            <label>Set Commission Type</label>
            <p class="error"><span id="error_commission_type"></span></p>
          </div>
        </div>
      </div>
      
      <div id="main_commission_rule_div">
        <?php if(!empty($main_commission_array)){ ?>
          <?php foreach ($main_commission_array as $mainKey => $mainValue) { ?>
            <?php 
              $nameKey=!empty($mainValue['range_id']) ? $mainValue['range_id'] : '-'.$mainKey;
            ?>
            <div class="inner_commission_div" id="inner_commission_div_<?= $mainKey ?>" data-id="<?= $mainKey ?>">
              <h4 class="m-b-20 fw500">Commission <?= $mainKey ?>
                <a href="javascript:void(0);" data-toggle="tooltip"  data-template='<div class="tooltip left_text_tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>' data-placement="top" data-trigger="click" 
                    title="Set the total commission amount for each level,the system will calculate the override based on enrolling agent. The highest commission rate should be applied to box one and show in descending order for additional commission boxes if using Percentage based commission."> 
                    <i class="fa fa-info-circle" aria-hidden="true"></i> 
                </a>
              </h4>
              <div class="row">
                <div class="col-lg-4 change_commission_rate_after_div_option" style="<?= $mainKey > 1 ? '' : 'display: none' ?>" id="change_commission_rate_after_div_option_<?= $mainKey ?>" data-id="<?= $mainKey ?>">
                  <div class="form-group">
                    <select class="form-control change_commission_rate_after" id="change_commission_rate_after_<?= $mainKey ?>" name="change_commission_rate_after[<?= $nameKey ?>]" data-id="<?= $mainKey ?>">
                      <option hidden selected="selected"></option>
                      <?php for($i=1;$i<=24;$i++){?>
                        <option value="<?= $i ?>" <?= !empty($mainValue['from_renewal']) && $mainValue['from_renewal'] == $i ? 'selected' : '' ?>>P<?=$i?></option>
                      <?php } ?>
                    </select>
                    <label>Change Commission Rate After</label>
                    <p class="error"><span id="error_change_commission_rate_after_<?= $nameKey ?>"></span></p>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <select id="commission_calculate_by_<?= $mainKey ?>" name="commission_calculate_by[<?= $nameKey ?>]" class=" commission_calculate_by form-control" data-id="<?= $mainKey ?>">
                      <option hidden selected="selected"></option>
                      <option value="Percentage"  <?= !empty($mainValue['commission_calculate_by']) && $mainValue['commission_calculate_by'] == 'Percentage' ? 'selected' : '' ?>>Percentage of sale</option>
                      <option value="Amount" <?= !empty($mainValue['commission_calculate_by']) && $mainValue['commission_calculate_by'] == 'Amount' ? 'selected' : '' ?>>Flat Rate of sale</option>
                    </select>
                    <label>Commission Calculated By</label>
                    <p class="error"><span id="error_commission_calculate_by_<?= $nameKey ?>"></span></p>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <select id="commission_duration_<?= $mainKey ?>" name="commission_duration[<?= $nameKey ?>]" class="  commission_duration form-control" data-id="<?= $mainKey ?>">
                      <option hidden selected="selected"></option>
                      <option value="Indefinitely" <?= !empty($mainValue['commission_duration']) && $mainValue['commission_duration'] == 'Indefinitely' ? 'selected' : '' ?>>Maintain commissions indefinitely </option>
                      <option value="Change Commission" <?= !empty($mainValue['commission_duration']) && $mainValue['commission_duration'] == 'Change Commission' ? 'selected' : '' ?>>Adjust commissions after a period of time</option>
                      <option value="Stop Paying" <?= !empty($mainValue['commission_duration']) && $mainValue['commission_duration'] == 'Stop Paying' ? 'selected' : '' ?>>Stop commissions after a period of time</option>
                    </select>
                    <label>Commission Duration</label>
                    <p class="error"><span id="error_commission_duration_<?= $nameKey ?>"></span></p>
                  </div>
                </div>
              </div>
              <div id="main_commission_price_div_<?= $mainKey ?>" data-id="<?= $mainKey ?>" class="main_commission_price_div">
                <?php if($commission_type=="Agent Level") {?>
                  <div class="inner_commission_product_div" id="inner_commission_product_div_<?= $mainKey ?>" data-id="<?= $mainKey ?>">
                    <div class="commission_add_box commission_add_box_blue_<?= $mainKey ?>">
                      <div class="phone-control-wrap ">
                        <div class="phone-addon">
                          <div class="flex_row flex_equal_width">
                            <?php if(!empty($agentCodedRes)) { ?> 
                              <?php foreach ($agentCodedRes as $keyLevel => $levelValue) { ?>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <div class="input-group-addon addon_<?= $mainKey ?> addon_amount_<?= $mainKey ?>" style="<?= $mainValue['commission_price'][$levelValue['level']]['amount_type']=="Amount" ? '' : 'display: none'; ?>"> $ </div>
                                      <div class="pr">
                                        <input name="commission_price[<?= $nameKey ?>][<?= $levelValue['level'] ?>]" class="form-control" type="text" onkeypress="return isNumberOnly(event)" value="<?= $mainValue['commission_price'][$levelValue['level']]['amount'] ?>" <?= $levelValue['level'] == "LOA" ? 'readonly' : '' ?>/>
                                        <label><?= $levelValue['level_heading'] ?></label>
                                      </div>
                                      <div class="input-group-addon addon_<?= $mainKey ?> addon_percentage_<?= $mainKey ?>" style="<?= $mainValue['commission_price'][$levelValue['level']]['amount_type']=="Percentage" ? '' : 'display: none'; ?>"> % </div>
                                    </div>
                                  </div>
                              <?php } ?>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="error"><span id="error_commission_price_<?= $nameKey ?>"></span></p>
                  </div>
                <?php }else{ ?>
                  <div class="inner_commission_plan_div" id="inner_commission_plan_div_<?= $mainKey ?>" data-id="<?= $mainKey ?>">
                    <?php if(!empty($prdPlanTypeArray)) { ?>
                      <?php foreach ($prdPlanTypeArray as $keyTier => $tierValue) { ?>
                        <?php if(empty($productPlansArr) || in_array($tierValue['id'], $productPlansArr)){ ?>
                        <div class="commission_add_box commission_add_box_blue_<?= $mainKey ?>">
                          <div class="phone-control-wrap ">
                            <div class="phone-addon com_title w-110"><?= $tierValue['title'] ?></div>
                            <div class="phone-addon">
                              <div class="flex_row flex_equal_width">
                                <?php if(!empty($agentCodedRes)) { ?> 
                                  <?php foreach ($agentCodedRes as $keyLevel => $levelValue) { ?>
                                      <div class="form-group">
                                        <div class="input-group">
                                          <div class="input-group-addon addon_<?= $mainKey ?> addon_amount_<?= $mainKey ?>" style="<?= $mainValue['commission_price'][$tierValue['id']][$levelValue['level']]['amount_type']=="Amount" ? '' : 'display: none'; ?>"> $ </div>
                                          <div class="pr">
                                            <input name="commission_price[<?= $nameKey ?>][<?= $tierValue['id'] ?>][<?= $levelValue['level'] ?>]" class="form-control commission_price_input commission_price_input_<?= $tierValue['id'] ?>" type="text" onkeypress="return isNumberOnly(event)" value="<?= $mainValue['commission_price'][$tierValue['id']][$levelValue['level']]['amount'] ?>" <?= $levelValue['level'] == "LOA" ? 'readonly' : '' ?>/>
                                            <label><?= $levelValue['level_heading'] ?></label>
                                          </div>
                                          <div class="input-group-addon addon_<?= $mainKey ?> addon_percentage_<?= $mainKey ?>" style="<?= $mainValue['commission_price'][$tierValue['id']][$levelValue['level']]['amount_type']=="Percentage" ? '' : 'display: none'; ?>"> % </div>
                                        </div>
                                      </div>
                                  <?php } ?>
                                <?php } ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <p class="error"><span id="error_commission_price_<?= $nameKey ?>_<?= $tierValue['id'] ?>"></span></p>
                      <?php } ?>
                      <?php } ?>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>
            </div>
            
          <?php } ?>

        <?php } ?>
      </div>

      <div class="clearfix"></div>
      
      <div id="stop_commission_div" style="<?= !empty($mainValue['commission_duration']) && $mainValue['commission_duration'] == 'Stop Paying' ? '' : 'display: none' ?>">
        <p class="m-b-20"></p>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
                <select class="form-control" id="stop_commission_after" name="stop_commission_after">
                  <option hidden selected="selected"></option>
                  <?php for($i=1;$i<=60;$i++){?>
                    <option value="<?= $i ?>" <?= !empty($stop_commission_after) && $stop_commission_after == $i ? 'selected' : '' ?>>P<?=$i?></option>
                  <?php } ?>
                </select>
                <label>Stop Commission</label>
                <p class="error"><span id="error_stop_commission_after"></span></p>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <hr>
      <div class="form-group height_auto">
        <label>When will this commission be paid for New Business?</label>
        <div class="clearfix"></div>
        <label class="radio m-t-0">
          <input  type="radio" name="new_business_commission_duration" id="new_business_commission_duration_weekly" value="weekly" <?= !empty($new_business_commission_duration) && $new_business_commission_duration == "weekly" ? 'checked' : '' ?>>  Weekly</label>
        <div class="clearfix"></div>
        <label class="radio m-t-0">
          <input type="radio" name="new_business_commission_duration" id="new_business_commission_duration_monthly" value="monthly" <?= !empty($new_business_commission_duration) && $new_business_commission_duration == "monthly" ? 'checked' : '' ?>> Monthly</label>
        <div class="clearfix"></div>
        <p class="error"><span id="error_new_business_commission_duration"></span></p>
      </div>
      <div class="clearfix"></div>
      <div class="form-group height_auto">
        <label>When will this commission be paid for Renewal?</label>
        <div class="clearfix"></div>
        <label class="radio m-t-0">
          <input  type="radio" name="renewal_commission_duration"  id="renewal_commission_duration_weekly" value="weekly" <?= !empty($renewal_commission_duration) && $renewal_commission_duration == "weekly" ? 'checked' : '' ?>> Weekly</label>
        <div class="clearfix"></div>
       
        <label class="radio m-t-0">
          <input  type="radio" name="renewal_commission_duration" id="renewal_commission_duration_monthly" value="monthly" <?= !empty($renewal_commission_duration) && $renewal_commission_duration == "monthly" ? 'checked' : '' ?>> Monthly</label>
        <div class="clearfix"></div>
        <p class="error"><span id="error_renewal_commission_duration"></span></p>
      </div>
      <div class="clearfix"></div>
      <div class="form-group height_auto">
        <label>Commission Reversals: How would you like system to handle reversal of commissions for New Business with this commission rule?</label>
        <div class="clearfix"></div>
        <label class="radio m-t-0">
          <input type="radio" name="commission_reversals" id="system_default" value="system_default" <?= !empty($commission_reversals) && $commission_reversals == 'system_default' ? 'checked' : '' ?>> System Default</label>
        <div class="clearfix"></div>
        <label class="radio m-t-0 label-input">
          <input type="radio" name="commission_reversals" id="not_reverse_after" value="not_reverse_after" <?= !empty($commission_reversals) && $commission_reversals == 'not_reverse_after' ? 'checked' : '' ?>> Do not reverse commissions if reversal is after set period of time</label>
        <div class="clearfix"></div>
        <p class="error"><span id="error_commission_reversals"></span></p>
      </div>
      <div class="form-group height_auto reverse_days_select" style="<?= !empty($commission_reversals) && $commission_reversals == 'not_reverse_after' ? '' : 'display: none' ?>">
        <div class="theme-form">
          <div>
            Do not reverse commissions if reversal is
            <select name="reverse_days" id="reverse_days" class="form-control max-w75">
              <option hidden selected="selected"></option>
                <?php for ($day=1; $day < 61; $day++) { ?> 
                  <option value="<?= $day ?>" <?=!empty($reverse_days) && $reverse_days == $day ? 'selected' : '' ?>> <?=$day?> </option>
                <?php } ?>
            </select>
            Days after Payment Approved status of New Business.
          </div>
          <p class="error"><span id="error_reverse_days"></span></p>
        </div>
      </div>
    </div>
    <div class="panel-footer text-center">
      <hr class="m-t-0"  />
      <div class="col-md-12">
        <button class="btn btn-action" type="button" name="save_as_active" id="save_as_active">Save </button>
        <button id="btn_cancel" name="btn_cancel" class="btn red-link cancel_btn" type="button">Cancel</button>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</form>

<!-- ************* Dynamic Code Generation Code Start ************* -->
  <div id="commission_rule_dynamic_div" style="display: none">
    <div class="inner_commission_div" id="inner_commission_div_~number~" data-id="~number~">
      <h4 class="m-b-20 fw500">Commission ~number~
        <a href="javascript:void(0);" data-toggle="tooltip"  data-template='<div class="tooltip left_text_tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>' data-placement="top" data-trigger="click" 
            title="Set the total commission amount for each level,the system will calculate the override based on enrolling agent. The highest commission rate should be applied to box one and show in descending order for additional commission boxes if using Percentage based commission."> 
            <i class="fa fa-info-circle" aria-hidden="true"></i> 
        </a>
      </h4>
      <div class="row">
        <div class="col-lg-4 change_commission_rate_after_div_option" style="display: none" id="change_commission_rate_after_div_option_~number~" data-id="~number~">
          <div class="form-group">
            <select class="add_control_~number~ change_commission_rate_after" id="change_commission_rate_after_~number~" name="change_commission_rate_after[-~number~]" data-id="~number~">
              <option hidden selected="selected"></option>
              <?php for($i=1;$i<=24;$i++){?>
                <option value="<?= $i ?>">P<?=$i?></option>
              <?php } ?>
            </select>
            <label>Change Commission Rate After</label>
            <p class="error"><span id="error_change_commission_rate_after_~number~"></span></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <select id="commission_calculate_by_~number~" name="commission_calculate_by[-~number~]" class=" commission_calculate_by add_control_~number~" data-id="~number~">
              <option hidden selected="selected"></option>
              <option value="Percentage" >Percentage of sale</option>
              <option value="Amount">Flat Rate of sale</option>
            </select>
            <label>Commission Calculated By</label>
            <p class="error"><span id="error_commission_calculate_by_~number~"></span></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <select id="commission_duration_~number~" name="commission_duration[-~number~]" class="  commission_duration add_control_~number~" data-id="~number~">
              <option hidden selected="selected"></option>
              <option value="Indefinitely">Maintain commissions indefinitely </option>
              <option value="Change Commission">Adjust commissions after a period of time</option>
              <option value="Stop Paying">Stop commissions after a period of time</option>
            </select>
            <label>Commission Duration</label>
            <p class="error"><span id="error_commission_duration_~number~"></span></p>
          </div>
        </div>
      </div>
      <div id="main_commission_price_div_~number~" data-id="~number~" class="main_commission_price_div">
      </div>
    </div>
  </div>

  <div id="commission_product_dynamic_div" style="display: none">
    <div class="inner_commission_product_div" id="inner_commission_product_div_~number~" data-id="~number~">
      <div class="commission_add_box commission_add_box_blue_~number~">
        <div class="phone-control-wrap ">
          <div class="phone-addon">
            <div class="flex_row flex_equal_width">
              <?php if(!empty($agentCodedRes)) { ?> 
                <?php foreach ($agentCodedRes as $keyLevel => $levelValue) { ?>
                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-addon addon_~number~ addon_amount_~number~" style="display: none"> $ </div>
                        <div class="pr">
                          <input name="commission_price[-~number~][<?= $levelValue['level'] ?>]" class="form-control" type="text" onkeypress="return isNumberOnly(event)" value="<?= $levelValue['level'] == "LOA" ? 0 : '' ?>" <?= $levelValue['level'] == "LOA" ? 'readonly' : '' ?>/>
                          <label><?= $levelValue['level_heading'] ?></label>
                        </div>
                        <div class="input-group-addon addon_~number~ addon_percentage_~number~" style="display: none"> % </div>
                      </div>
                    </div>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <p class="error"><span id="error_commission_price_~number~"></span></p>
    </div>
  </div>

  <div id="commission_plan_dynamic_div" style="display: none">
    <div class="inner_commission_plan_div" id="inner_commission_plan_div_~number~" data-id="~number~">
      <?php if(!empty($prdPlanTypeArray)) { ?>
        <?php foreach ($prdPlanTypeArray as $keyTier => $tierValue) { ?>
           <?php if(empty($productPlansArr) || in_array($tierValue['id'],$productPlansArr)){ ?>
          <div class="commission_add_box benefitTier priceTier_<?=$tierValue['id']?> commission_add_box_blue_~number~">
            <div class="phone-control-wrap ">
              <div class="phone-addon com_title w-110"><?= $tierValue['title'] ?></div>
              <div class="phone-addon">
                <div class="flex_row flex_equal_width">
                  <?php if(!empty($agentCodedRes)) { ?> 
                    <?php foreach ($agentCodedRes as $keyLevel => $levelValue) { ?>
                        <div class="form-group">
                          <div class="input-group">
                            <div class="input-group-addon addon_~number~ addon_amount_~number~" style="display: none"> $ </div>
                            <div class="pr">
                              <input name="commission_price[-~number~][<?= $tierValue['id'] ?>][<?= $levelValue['level'] ?>]" class="form-control commission_price_input commission_price_input_<?= $tierValue['id'] ?>" type="text" onkeypress="return isNumberOnly(event)" value="<?= $levelValue['level'] == "LOA" ? 0 : '' ?>" <?= $levelValue['level'] == "LOA" ? 'readonly' : '' ?>/>
                              <label><?= $levelValue['level_heading'] ?></label>
                            </div>
                            <div class="input-group-addon addon_~number~ addon_percentage_~number~" style="display: none"> % </div>
                          </div>
                        </div>
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <p class="error"><span id="error_commission_price_~number~_<?= $tierValue['id'] ?>"></span></p>
        <?php } ?>
        <?php } ?>
      <?php } ?>
    </div>
  </div>
<!-- ************* Dynamic Code Generation Code End   ************* -->
<script type="text/javascript">
    $commission_rule_id='<?= $commission_rule_id ?>';
    $(document).ready(function(){
      // $("#product").multipleSelect({
     
      // });

      if($commission_rule_id==0){
        load_commission_rule('');
      }else{
        load_stop_commission_after('onLoad');
      }
    });

    $(document).on("change","#product",function(e){
        e.stopPropagation();
        hideShowBenefitTier();
    });

    $(document).on("change","#commission_type",function(e){
      e.stopPropagation();
      $("#main_commission_rule_div").html('');
      load_commission_rule('');
      $("#stop_commission_div").hide();
      load_commission_price('');
    });

    $(document).on("change","#commission_rule_form .commission_calculate_by",function(e){
      e.stopPropagation();
      $number = $(this).attr('data-id');
      load_commission_price_addon($number);
    });

    $(document).on("change","#commission_rule_form .commission_duration",function(e){
      e.stopPropagation();
      $val = $(this).val();
      $number = parseInt($(this).attr('data-id'));
      $("#stop_commission_div").hide();
      if($val=="Change Commission"){
        load_commission_rule($val);
      }else{
        
        $(".inner_commission_div").filter(function(){
            if($(this).attr('data-id') > $number){
              $("#inner_commission_div_"+($(this).attr('data-id'))).remove();
            }
              
        });
        if($val=="Stop Paying"){
          $("#stop_commission_div").show();
          load_stop_commission_after();
        }
            
      }
      
    });

    $(document).on("change","#commission_rule_form .change_commission_rate_after",function(e){
      e.stopPropagation();
      load_stop_commission_after();
    });


    load_commission_rule = function($val){
      $count = $("#commission_rule_form .inner_commission_div").length;
      $number=$count+1;

      html = $('#commission_rule_dynamic_div').html();
      $('#main_commission_rule_div').append(html.replace(/~number~/g, $number));

      $('#commission_rule_form .add_control_'+$number).addClass('form-control');
      $('#commission_rule_form .add_control_'+$number).selectpicker({ 
        container: 'body', 
        style:'btn-select',
        noneSelectedText: '',
        dropupAuto:false,
      });
      if($val=="Change Commission"){
        $('#commission_rule_form #change_commission_rate_after_div_option_'+$number).show();
        load_commission_price($number);
      }
    }

    load_commission_price = function($number){
      $val=$("#commission_type").val();
      
      if($val){
        $div_id="commission_product_dynamic_div";
        if($val=='Plan'){
          $div_id="commission_plan_dynamic_div";
        }
        
        if($number){
          html = $('#'+$div_id).html();
          $('#main_commission_price_div_'+$number).html(html.replace(/~number~/g, $number));
          if($number > 1){
            $('#commission_rule_form .commission_add_box_blue_'+$number).addClass('blue');
          }
          load_commission_price_addon($number);
        }else{
          $('#commission_rule_form .main_commission_price_div').each(function(){
            $number = parseInt($(this).attr('data-id'));
            html = $('#'+$div_id).html();
            $('#main_commission_price_div_'+$number).html(html.replace(/~number~/g, $number));
            if($number > 1){
              $('#commission_rule_form .commission_add_box_blue_'+$number).addClass('blue');
            }
            load_commission_price_addon($number);
          });
        }
      }
    }

    load_commission_price_addon = function($number){
      $val=$("#commission_calculate_by_"+$number).val();
      $(".addon_"+$number).hide();

      if($val=="Percentage"){
        $(".addon_percentage_"+$number).show();
      }
      if($val=="Amount"){
        $(".addon_amount_"+$number).show();
      }
    }

    load_stop_commission_after = function($type){
      $val = parseInt($("#commission_rule_form .change_commission_rate_after").last().val());
      
      $('#stop_commission_after option').each(function() {
        $currentOption = parseInt($(this).val());
        if ($currentOption <= $val) {
          $(this).hide();
        }else{
          $(this).show();
        }

      });
      if($type!='onLoad'){
        $('#stop_commission_after').selectpicker('refresh');
      }
    }

    isNumberOnly = function(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode != 8 && charCode != 46 && charCode != 47 && charCode != 0 && (charCode < 48 || charCode > 57)) {
          return false;
      }
      return true;
    }

    $(document).on("click","#save_as_active",function(){
      $('.error span').html('');
      $('#ajax_loader').show();
      $.ajax({
          url:"<?= $ADMIN_HOST ?>/ajax_add_commission_rule.php",
          data: $("#commission_rule_form").serialize(),
          method: 'POST',
          dataType: 'json',
          success: function(res) {
            $('#ajax_loader').hide();
            if (res.status == 'success') {
              if(res.parentCommissionRuleID!=0){
                  window.location.href="variation_detail.php?commission="+res.parentCommissionRuleID;
              }else{
                  window.location.href="commission_builder.php";
              }
            } else if (res.status == 'fail') {
              var is_error = true;
              $.each(res.errors, function (index, error) {
                  $('#error_' + index).html(error);
                  if (is_error) {
                      var offset = $('#error_' + index).offset();
                      if(typeof(offset) === "undefined"){
                          console.log("Not found : "+index);
                      }else{
                          var offsetTop = offset.top;
                          var totalScroll = offsetTop - 195;
                          $('body,html').animate({
                              scrollTop: totalScroll
                          }, 1200);
                          is_error = false;
                      }
                  }
              });
            }
            return false;
          }
      });
    });
    $(document).on("click","#btn_cancel",function(){
      window.location.href="<?= $ADMIN_HOST ?>/commission_builder.php";
    });


    hideShowBenefitTier = function(){
      $planTypes = $("#product :selected").attr('data-planTypes');
      if (typeof($planTypes) === "undefined" || $planTypes == null || $planTypes == ''){
        $(".benefitTier").show();
      }else{
        var plansArr =  $planTypes.split(",");
        $(".benefitTier").hide();
        $(".commission_price_input").attr('disabled',true);
        $.each(plansArr,function(i,v){
          $(".priceTier_"+v).show();
          $(".commission_price_input_"+v).attr('disabled',false);
        });
      }
    }

    $(document).on('click','#not_reverse_after',function(){
      $('.reverse_days_select').show();
    });
    $(document).on('click','#system_default',function(){
      $('.reverse_days_select').hide();
    });
</script>