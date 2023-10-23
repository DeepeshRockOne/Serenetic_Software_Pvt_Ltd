<?php include "notify.inc.php";?>
<div class="panel panel-default panel-block add_fee_wrap theme-form">
  <form name="advFeeForm" method="Post" id="advFeeForm" class="theme-form">
    <input type="hidden" name="agentId" value="<?=checkIsset($agentId)?>">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="fs18 mn">Add Advance</h4>
    </div>
  </div>
  <div class="panel-body ">
      <p class="fs14 m-b-15"><strong class="fw500">Advance Information</strong></p>
      <div class="row">
        <div class="col-sm-6">
            <div class="form-group height_auto">
                <select name="products[]" id="products" multiple="multiple" class="se_multiple_select" >
                <?php if(!empty($productRes)){ ?>
                    <?php foreach ($productRes as $key=> $category) { ?>
                      <?php if(!empty($category)){ ?>
                  <optgroup label='<?= $key ?>'>
                    <?php foreach ($category as $pkey => $row) { ?>
                      <option value="<?= $row['id'] ?>" <?= (!empty($product_ids) && in_array($row['id'], $product_ids)) ? 'selected="selected"' : '' ?>>
                        <?= $row['name'] .' ('.$row['product_code'].')'?>    
                      </option>
                    <?php } ?>
                  </optgroup>
                    <?php } ?>
                  <?php } ?>
                <?php } ?>
              </select>
                <label>Products</label>
                <p class="error" id="error_products"></p>
            </div>
          </div>
        <div class="col-sm-6">
          <div class="form-group height_auto">
            <select class="form-control" name="advance_month" id="advance_month">
              <option value=""></option>
              <option value="0" <?=isset($advance_month) && $advance_month == 0 ? "selected = 'selected'" : ""?>>0</option>
              <?php for($i=1;$i<=12;$i=$i+0.5){ ?>
                <option value="<?=$i?>" <?=isset($advance_month) && $advance_month == $i ? "selected = 'selected'" : ""?>><?=$i?></option>
              <?php } ?>
            </select>
            <label>Advance Months</label>
            <p class="error" id="error_advance_month"></p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
              <input type="text" id="display_id" name="display_id" class="form-control" value="<?=$display_id?>">
              <label>Fee ID (Must be Unique)</label>
              <p class="error" id="error_display_id"></p>
            </div>
            
        </div>
        <div class="col-sm-6">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                  <div class="pr">
                  <input type="text" value="<?=$effective_date?>" name="effective_date" id="effective_date" class="form-control date_picker">
                  <label>Effective Date</label>
                </div>
                </div>
                <p class="error" id="error_effective_date"></p>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <div class="input-group"> 
                  <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                  <div class="pr">
                  <input type="text" value="<?=$termination_date?>" name="termination_date" id="termination_date" class="form-control date_picker">
                  <label>Termination Date</label>
                </div>
                </div>
                <p class="error" id="error_termination_date"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <p class="fs14 m-b-15"><strong class="fw500">Service Fee</strong></p>

      <p class="fs14 m-b-15">A service fee is applied to member orders based on the total amount of the order. The fee can be variable depending on order total (excluding any other fees).</p>

       
      <label class="">
        <input name="price_calculated_on" type="radio" class="price_calculated_on" value="FixedPrice" <?=$pricingModel == 'FixedPrice' ? 'Checked' : ""?> /> Fixed Amount
      </label>
      <div class="clearfix"></div>
      <label  class="">
        <input name="price_calculated_on" type="radio" class="price_calculated_on" value="VariableAmount" <?=$pricingModel == 'VariableAmount' ? 'Checked' : ""?> /> Variable Amount ($)
      </label>
      <p class="error" id="error_price_calculated_on"></p>
    

      <div class="variable_amount_wrapper m-t-20" style="<?=$pricingModel == 'VariableAmount' ? "display: block;" : "display: none;"?>">
        <?php if($pricing_range){ ?>
          <?php $counter = 1; ?>
          <?php foreach ($pricing_range as $k => $v) { ?>
            <div class="flex_row range">
              <?php if($counter > 1){ ?>
                  <div class="form-group height_auto">
                    <a href="javascript:void(0);" class="text-action delete_range fs16 text-gray fw500">X</a>
                  </div>
              <?php } ?>
                <div class="form-group ">
                  <input class="form-control priceControl" name="range_service_fee[<?=$v['matId']?>]" value="<?=$v['fee']?>" type="text">
                  <label>Fee ($0.00)</label>
                  <p class="error" id="error_range_service_fee_<?=$v['matId']?>"></p>
                </div>
                <div class="form-group height_auto text-center p-t-7">
                  |
                </div>
                <div class="form-group ">
                  <input class="form-control priceControl" name="order_min_total[<?=$v['matId']?>]" type="text" value="<?=$v['min_total']?>">
                  <label>Order Total Min ($0.00)</label>
                  <p class="error" id="error_order_min_total_<?=$v['matId']?>"></p>
                </div>
                <div class="form-group height_auto text-center p-t-7">
                  To
                </div>
                <div class="form-group ">
                  <input class="form-control priceControl" name="order_max_total[<?=$v['matId']?>]" type="text" value="<?=$v['max_total']?>">
                  <label>Order Total Max ($99.99)</label>
                  <p class="error" id="error_order_max_total_<?=$v['matId']?>"></p>
                </div>
            </div>
          <?php $counter++ ?>
          <?php } ?>
        <?php } ?>
      </div>
      <div class="text-right m-b-30 add_range_wrapper" style="<?=$pricingModel == 'VariableAmount' ? "display: block;" : "display: none;"?>">
          <a href="javascript:void(0);" class="btn btn-info add_range">+ Range</a>
      </div>
      <div class="row theme-form m-t-20 fixed_amount_wrapper" style="<?=$pricingModel == 'FixedPrice' ? "display: block;" : "display: none;"?>">
        <div class="col-sm-6">
          <div class="form-group height_auto">
              <input name="processing_fee" id="processing_fee" value="<?=$processing_fee?>" type="text" class="form-control priceControl" placeholder="Fixed Amount ($0.00)" />
              <label></label>
              <p class="error" id="error_processing_fee"></p>
            </div>
        </div>
      </div>
      
      <div class="clearfix m-b-10"></div>
      <div class="table-responsive">
          <p><strong class="fw500">Advance Rules</strong></p>
          <table cellpadding="0" cellspacing="0" width="100%" class="<?=$table_class?> m-b-5">
            <tbody>
              <tr class="lightblue_tr_bg"> 
                <td>Apply fee on new business?</td>
                <td class="w-110">
                  <label class="mn"><input type="radio" id="is_fee_on_new_business_N" name="is_fee_on_new_business" value="N" class="initial_purchase" <?=$is_fee_on_new_business == 'N' ? 'checked' : ""?>>No</label>
                </td>
                <td class="w-110">
                  <label class="mn"><input type="radio" id="is_fee_on_new_business_Y" value="Y" name="is_fee_on_new_business" class="initial_purchase" <?=$is_fee_on_new_business == 'Y' ? 'checked' : ""?>>Yes</label>
                </td>
                <p class="error" id="error_is_fee_on_new_business"></p>
              </tr> 
            </tbody>
          </table>
           <table cellpadding="0" cellspacing="0" width="100%" class="<?=$table_class?> m-b-5 table_br_gray">
            <tbody>
              <tr>
                <td colspan="2">Apply fee on renewals?</td>
                <td class="w-110">
                  <label class="mn"><input type="radio" id="is_fee_on_renewal_N" value="N" name="is_fee_on_renewal" class="is_fee_on_renewal" <?=$is_fee_on_renewal == 'N' ? 'checked' : ""?>>No</label>
                </td>
                <td class="w-110">
                  <label class="mn p-l-20"><input type="radio" name="is_fee_on_renewal" id="is_fee_on_renewal_Y" value="Y" class="is_fee_on_renewal" <?=$is_fee_on_renewal == 'Y' ? 'checked' : ""?>>Yes</label>
                </td>
                <p class="error" id="error_is_fee_on_renewal"></p>
              </tr>  
              <tr class="renewals_type_wrapper" style="<?=$is_fee_on_renewal == 'Y' ? "" : "display: none;"?>">
                  <td><span class="p-l-40">Set the number of renewals: </span></td>
                            <td class="w-130"><label class="mn">
                      <div class="radio"><span><input type="radio" name="renewal_type" class="renewal_type" id="renewal_type_continuous" value="Continuous" <?=$renewal_type == 'Continuous' ? 'checked' : ""?>></span></div>Continuous</label></td>
                  <td class="w-110"><label class="mn">
                      <div class="radio"><span><input type="radio" class="renewal_type" name="renewal_type" id="renewal_type_renewal" value="Renewals" <?=$renewal_type == 'Renewals' ? 'checked' : ""?>></span></div>Renewals</label></td>

                  
                  <td class="w-110">
                    <select class="form-control w-100 pull-right" <?=$renewal_type == 'Renewals' ? '' : "disabled='disabled'"?> name="number_of_renewals" id="number_of_renewals" tabindex="-98">
                      <?php for($i=1;$i<=24;$i++){ ?>
                        <option value="<?=$i?>" <?=$number_of_renewals == $i ? "selected='selected'" : ''?>><?=$i?></option>
                      <?php } ?>
                    </select>
                  </td>
                  <p class="error" id="error_renewal_type"></p>
                </tr>             
            </tbody>
          </table>
      </div>
      <div class="clearfix"></div>
      <div class="text-center m-t-20"><i>*If order has multiple products with varying advance commission fees, the largest fee will be applied based on all products included in order that have an advance commission set-up.</i></div>
      <div class="text-center m-t-30"> 
          <input type="hidden" name="ruleType" id="ruleType" value="<?= $ruleType ?>">
          <input type="hidden" name="advRuleId" id="advRuleId" value="<?= $advRuleId ?>">
          <input type="hidden" name="advFeeId" id="advFeeId" value="<?= $advFeeId ?>">
          <input type="hidden" name="advFeeIds" id="advFeeIds" value="<?= $advFeeIds ?>">
          <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
          <input type="button" name="add_fee" id="add_fee" class="btn btn-action" value="<?= (checkIsset($advFeeId)) ? 'Save Fee' : 'Add Fee' ?>"> 
          <a href="javascript:void(0);" onclick='parent.$.colorbox.close();' class="btn red-link">Cancel</a>
      </div>
  </div>
</form>
</div>
<div class="range_template" style="display: none;">
    <div class="flex_row range">
        <div class="form-group height_auto">
          <a href="javascript:void(0);" id="delete_range_~number~" class="fs16 delete_range text-gray fw500">X</a>
        </div>
        <div class="form-group ">
          <input class="form-control priceControl" name="range_service_fee[~number~]" type="text">
          <label>Fee ($0.00)</label>
          <p class="error" style="text-align: left !important;" id="error_range_service_fee_~number~"></p>
        </div>
        <div class="form-group height_auto text-center p-t-7">
          |
        </div>
        <div class="form-group ">
          <input class="form-control priceControl" name="order_min_total[~number~]" type="text">
          <label>Order Total Min ($0.00)</label>
          <p class="error" style="text-align: left !important;" id="error_order_min_total_~number~"></p>
        </div>
        <div class="form-group height_auto text-center p-t-7">
          To
        </div>
        <div class="form-group ">
          <input class="form-control priceControl" type="text" name="order_max_total[~number~]">
          <label>Order Total Max ($99.99)</label>
          <p class="error" style="text-align: left !important;" id="error_order_max_total_~number~"></p>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {

    $price_calculated_on = '<?= $price_calculated_on ?>';
    if($price_calculated_on == 'Percentage'){ 
      removePriceControl();
    }else{
      addPriceControl();
    }

    $(".add_range").on('click',function(){
      add_pricing_range();
    });

    $(document).off('click','.delete_range');
    $(document).on('click','.delete_range',function(){
      $(this).closest('.range').remove();
    });

    $("#products").multipleSelect({
    });

    $(document).on("click", "#add_fee", function() {
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_add_advance_fee.php',
        dataType: 'JSON',
        data: $("#advFeeForm").serialize(),
        type: 'POST',
        success: function(res) {
          $("#ajax_loader").hide();
          $('.error').html('');
          if (res.status == "success") {
            if (res.advFeeIds) {
              if (window.parent.$("#advFeeIds").val()) {
                window.parent.$("#advFeeIds").val(window.parent.$("#advFeeIds").val() + ',' + res.advFeeIds);
              } else {
                window.parent.$("#advFeeIds").val(res.advFeeIds);
              }
            }
            window.parent.load_advance_fee_div();
            window.parent.setNotifySuccess(res.message);
            window.parent.$.colorbox.close();
          } else {
            var is_error = true;
            $.each(res.errors, function(index, error) {
              $('#error_' + index).html(error);
              if (is_error) {
                var offset = $('#error_' + index).offset();
                if (typeof(offset) === "undefined") {
                  console.log("Not found : " + index);
                } else {
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
        }
      });
    });

    $("#effective_date").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true,
    }).on("changeDate", function(selected) {
      var minDate = new Date(selected.date.valueOf());
      $('#termination_date').datepicker('setStartDate', minDate);
    });

    $("#termination_date").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    });

    $(".price_calculated_on").on('change', function() {
      // $("#processing_fee").val('');
      if ($(this).val() == 'FixedPrice') {
        // $(".variable_amount_wrapper").html('');
        $('.variable_amount_wrapper').hide('slow');
        $('.add_range_wrapper').hide('slow');
        $(".fixed_amount_wrapper").show('hide');
      } else {
        $('.variable_amount_wrapper').show('slow');
        $('.add_range_wrapper').show('slow');
        $(".fixed_amount_wrapper").hide('hide');
        var counter = $('.range:visible').length;
        if(counter == 0){
          add_pricing_range();  
        }
      }
    });

    $(".is_fee_on_renewal").on('change', function() {
      if ($(this).val() == 'Y') {
        $('.renewals_type_wrapper').show('slow');
      } else {
        $('.renewals_type_wrapper').hide('slow');
      }
    });

    $(document).off("change", ".renewal_type");
    $(document).on("change", ".renewal_type", function(e) {
      e.preventDefault();
      if ($(this).val() == 'Renewals') {
        $('#number_of_renewals').prop('disabled', false);
      } else {
        $('#number_of_renewals').prop('disabled', true);
      }
      $('#number_of_renewals').selectpicker('refresh');
    });
});

addPriceControl = function() {
  $('.priceControl').priceFormat({
    prefix: '',
    suffix: '',
    centsSeparator: '.',
    thousandsSeparator: '',
    limit: false,
    centsLimit: 2,
  });
}

PercentagePriceControl = function() {
  $('.priceControl').priceFormat({
    prefix: '',
    suffix: '',
    thousandsSeparator: '',
    limit: false,
    centsLimit: 2,
  });
}

removePriceControl = function() {
  $('.priceControl').unpriceFormat();
}

add_pricing_range = function() {
  var counter = $('.range:visible').length + 1;
  counter = "-"+counter;
  var range_template = $(".range_template").html();
  range_template = range_template.replace(/~number~/g, counter);
  $('.variable_amount_wrapper').append(range_template);
  $('#delete_range_1').hide();
  addPriceControl();
}
</script>