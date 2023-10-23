<div class="add_level_panelwrap add_fee_wrap">
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <h4 class="mn fw500"><?= (!empty($fee_id) && $is_clone == 'N') ? 'Edit Fee' : 'Add Fee' ?></h4>
    </div>
    <form name="vendor_fee_frm" method="Post" id="vendor_fee_frm" class="theme-form ">
      <div class="panel-body">
        <p class="fw500">Fee Information</p>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <input type="text" id="fee_name" name="fee_name" class="form-control" value="<?= checkIsset($fee_name); ?>"/>
              <label>Fee Name</label>
              <p class="error" id="error_fee_name"></p>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <input type="text" id="display_fee_id" name="display_fee_id" class="form-control" value="<?= checkIsset($display_fee_id); ?>"/>
                <label>Vendor ID (Must Be Unique,ex.V1234567)</label>
              <p class="error" id="error_display_fee_id"></p>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <select class="form-control has-value" name="fee_type" id="fee_type">
                <option value="Charged" <?= (checkIsset($fee_type) && $fee_type=='Charged') ? 'selected' : '' ?>>Charged</option>
                <option value="Display Only" <?= (checkIsset($fee_type) && $fee_type=='Display Only') ? 'selected' : '' ?>>Display Only</option>
              </select>
              <label>Fee Type</label>
              <p class="error" id="error_fee_type"></p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
                <select name="products[]" id="products" multiple="multiple" class="se_multiple_select <?= !empty($product_ids)?'has-value':'' ?>">
                  <?php if(!empty($productRes)){ ?>
                    <?php foreach ($productRes as $key=> $category) { ?>
                      <?php if(!empty($category)){ ?>
                        <optgroup label='<?= $key ?>'>
                            <?php    foreach ($category as $pkey => $row) { ?>
                              <option value="<?= $row['id'] ?>" <?= (!empty($product_ids) && in_array($row['id'], $product_ids)) ? 'selected="selected"' : '' ?> >
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
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <div class="input-group"> 
                    <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i> </span>
                    <div class="pr">
                    <input type="text" class="form-control" name="effective_date" id="effective_date"  value="<?= checkIsset($effective_date); ?>"/>
                    <label>Effective Date</label>
                  </div>
                  </div>
                  <p class="error" id="error_effective_date"></p>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i> </span>
                    <div class="pr">
                      <input type="text" class="form-control" name="termination_date" id="termination_date"  value="<?= checkIsset($termination_date); ?>"/>
                      <label>Termination Date</label>
                    </div>
                  </div>
                  <p class="error" id="error_termination_date"></p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <p class="fs14"><strong class="fw500">Fee Structure</strong></p>
        <div class="table-responsive">
          <table class="<?=$table_class?> m-b-5">
            <tbody>
              <tr class="lightblue_tr_bg">
                <td>Is this fee charged on initial purchase?</td>
                <td class="w-110">
                  <label class="mn">
                    <input type="radio" name="initial_purchase" id="initial_purchase_N" value="N" class="initial_purchase" <?= (!empty($initial_purchase) && $initial_purchase == 'N') ? 'checked' : '' ?> />No
                  </label>
                </td>
                <td class="w-110">
                  <label class="mn">
                    <input type="radio" name="initial_purchase" id="initial_purchase_Y" value="Y" class="initial_purchase" <?= (!empty($initial_purchase) && $initial_purchase == 'Y') ? 'checked' : '' ?> />Yes
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
         </div>
         <div class="table-responsive">
          <table class="<?=$table_class?> m-b-5 table_br_gray">
            <tbody>
              <tr>
                <td colspan="2">Does this fee apply on renewals?</td>
                <td class="w-110 ">
                  <label class="mn">
                    <input type="radio" name="is_fee_on_renewal" id="is_fee_on_renewal_N" value="N" class="is_fee_on_renewal" <?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'N') ? 'checked' : '' ?> />No
                  </label>
                </td>
                <td class="w-110 ">
                  <label class="mn">
                    <input type="radio" name="is_fee_on_renewal" id="is_fee_on_renewal_Y" value="Y" class="is_fee_on_renewal" <?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'Y') ? 'checked' : '' ?>  />Yes
                  </label>
                </td>
              </tr>
              <tr id="renewal_numbers_row" style="display:<?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'Y') ? '' : 'none' ?>" >
                <td><span class="p-l-40">Set the number of renewals: </span></td>
                <td class="w-130">
                  <label class="mn">
                    <input type="radio" name="fee_renewal_type" id="fee_renewal_type_Continuous" value="Continuous" class="fee_renewal_type" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Continuous') ? 'checked' : '' ?>/>Continuous
                  </label>
                </td>
                <td class="w-110">
                  <label class="mn">
                    <input type="radio" name="fee_renewal_type" id="fee_renewal_type_Renewals" value="Renewals" class="fee_renewal_type" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Renewals') ? 'checked' : '' ?> />Renewals
                  </label>
                </td>
                <td class="w-110" id="fee_renewal_type_div">
                  <select class="form-control max-w75 pull-right" name="fee_renewal_count" id="fee_renewal_count" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Renewals') ? '' : 'disabled' ?>>
                    <?php for($i=0;$i<=12;$i++) { ?>
                      <option value="<?= $i ?>" <?= (!empty($fee_renewal_count) && $i == $fee_renewal_count) ? 'selected=selected' : '' ?>><?= $i ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
         </div> 
         <div class="table-responsive">
          <table class="<?=$table_class?> m-b-5 table_br_gray">
            <tbody>
              <tr class="lightblue_tr_bg">
                <td>Vary by pricing model?</td>
                <td class="w-110">
                  <label class="mn">
                    <input type="radio" name="is_benefit_tier" id="is_benefit_tier_N" value="N" class="is_benefit_tier"  <?= (!empty($is_benefit_tier) && $is_benefit_tier == 'N') ? 'checked' : '' ?>/>No
                  </label>
                </td>
                <td class="w-110">
                  <label class="mn">
                    <input type="radio" name="is_benefit_tier" id="is_benefit_tier_Y" value="Y" class="is_benefit_tier" <?= (!empty($is_benefit_tier) && $is_benefit_tier == 'Y') ? 'checked' : '' ?>/>Yes
                  </label>
                </td>
              </tr>
               <tr>
                <td colspan="3" id="pricing_model_div" class="pn">
                   <p class="error" id="error_pricing_model"></p>
                </td>
            </tr>
            </tbody>
          </table>  
         </div>
         <div class="table-responsive">
          <table class="<?=$table_class?> m-b-5 table_br_gray">
            <tbody>
              <tr>
                <td colspan="2">How is the fee calculated?</td>
                <td class="w-110 ">
                  <label class="mn">
                    <input type="radio" name="fee_method" value="FixedPrice" id="fee_method_no" class="fee_method" <?= (!empty($fee_method) && $fee_method == 'FixedPrice') ? 'checked' : '' ?> />Fixed Price
                  </label>
                </td>
                <td class="w-110 ">
                  <label class="mn">
                    <input type="radio" name="fee_method" value="Percentage" id="fee_method_yes" class="fee_method" <?= (!empty($fee_method) && $fee_method == 'Percentage') ? 'checked' : '' ?>/>Percentage
                  </label>
                </td>
              </tr>
              <tr id="percentage_type_row" style="<?= (!empty($fee_method) && $fee_method == 'Percentage') ? '' : 'display:none' ?>" >
                <td>How is the % calculated?</td>
                <td>
                  <label class="mn">
                    <input type="radio" name="percentage_type" id="percentage_type_Retail" value="Retail" class="percentage_type" <?= (!empty($percentage_type) && $percentage_type == 'Retail') ? 'checked' : '' ?>/>Retail Price
                  </label>
                </td>
                <td>
                  <label class="mn">
                    <input type="radio" name="percentage_type" id="percentage_type_Commissionable" value="Commissionable" class="percentage_type" <?= (!empty($percentage_type) && $percentage_type == 'Commissionable') ? 'checked' : '' ?> />Commissionable Price
                  </label>
                </td>
                <td>
                  <label class="mn">
                    <input type="radio" name="percentage_type" id="percentage_type_NonCommissionable" value="NonCommissionable" class="percentage_type" <?= (!empty($percentage_type) && $percentage_type == 'NonCommissionable') ? 'checked' : '' ?> />Non-commissionable Price
                  </label>
                </td>
              </tr>
              <tr id="fee_price_row" style="<?= (!empty($is_benefit_tier) && $is_benefit_tier=='N')?'' : 'display:none'; ?>" >
                <td colspan="3">
                  <div class="col-sm-4">
                    <label>Fee Price</label>
                    <div class="add_fee_addonwrap">
                      <div class="<?= (!empty($fee_method) && $fee_method=='FixedPrice') ? 'add_fee_addon' : 'add_fee_addon_percentage' ?>">
                        <i class="<?= (!empty($fee_method) && $fee_method=='FixedPrice') ? 'fa fa-usd' : 'fa fa-percent' ?>  fee_calculated_type"></i>
                      </div>
                      <input type="text" id="fee_price" name="fee_price" class="form-control priceControl" placeholder="10.00" value="<?= checkIsset($fee_price); ?>" >
                      <p class="error" id="error_fee_price"></p>
                    </div>
                  </div>
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
         </div>
        <div class="text-center m-t-20">
          <input type="hidden" name="vendor_id" id="vendor_id" value="<?= $vendor_id ?>">
          <input type="hidden" name="fee_id" id="fee_id" value="<?= ($is_clone=='Y')?'':$fee_id; ?>">
          <input type="hidden" name="vendor_fee_id" id="vendor_fee_id" value="<?= $vendor_fee_id ?>">
          <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
          <input type="button" name="add_fee" id="add_fee" class="btn btn-action" value="<?= (checkIsset($fee_id))? 'Save Fee' : 'Add Fee' ?>"> 
          <a href="javascript:void(0);" onclick='parent.$.colorbox.close();' class="btn red-link">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  
  $(document).ready(function() {

    setFeeAmountControl();
    $("#products").multipleSelect({
      onClick:function(e){
        var val = $("input[name='is_benefit_tier']:checked").val();
        loadPricingModuleHtml(val);      
      },
      onTagRemove:function(e){
          var val = $("input[name='is_benefit_tier']:checked").val();
        loadPricingModuleHtml(val);
      }
    });

     $is_benefit_tier = '<?= $is_benefit_tier ?>';

     if($is_benefit_tier == 'Y'){
       loadPricingModuleHtml($is_benefit_tier);
     }

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

  });

  $(document).off("change", ".fee_renewal_type");
  $(document).on("change", ".fee_renewal_type", function(e) {
    e.preventDefault();
    $val = $(this).val();
    if ($val == "Renewals") {
      $("#fee_renewal_count").prop("disabled", false);
    } else {
      $("#fee_renewal_count").val(0);
      $("#fee_renewal_count").prop("disabled", true);
    }
    $('#fee_renewal_count').selectpicker('refresh');
  });

  $(document).off("change", ".is_fee_on_renewal");
  $(document).on("change", ".is_fee_on_renewal", function(e) {
    e.preventDefault();
    $val = $(this).val();
    $("#renewal_numbers_row").hide();
    if ($val == "Y") {
      $("#renewal_numbers_row").show();
    }
  });

  $(document).off("click", ".is_benefit_tier");
  $(document).on("click", ".is_benefit_tier", function() {
    $val = $(this).val();
    loadPricingModuleHtml($val);
  });



  $(document).off("change", ".fee_method");
  $(document).on("change", ".fee_method", function(e) {
    $val = $(this).val();
    if ($val == "Percentage"){
      $("#percentage_type_row").show();
      $(".fee_calculated_type").removeClass('fa-usd').addClass('fa-percent');
      $(".fee_calculated_type").parent('div').addClass('add_fee_addon_percentage');
      $(".fee_calculated_type").parent('div').removeClass('add_fee_addon');

      $(".priceControl").val("");
      $(".priceControl").attr("placeholder", "0");
      removePriceControl();
      PercentagePriceControl();
    } else {
      $("#percentage_type_row").hide();
      $(".fee_calculated_type").removeClass('fa-percent').addClass('fa-usd');
      $(".fee_calculated_type").parent('div').removeClass('add_fee_addon_percentage');
      $(".fee_calculated_type").parent('div').addClass('add_fee_addon');

      $(".priceControl").val("");
      $(".priceControl").attr("placeholder", "10.00");
      removePriceControl();
      addPriceControl();
    }
  });

  $(document).on("click", "#add_fee", function() {
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_add_vendor_fee.php',
      dataType: 'JSON',
      data: $("#vendor_fee_frm").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        $('.error').html('');
        if (res.status == "success") { ;
          window.parent.$("#vendor_fee_id").val(res.vendor_fee_id);
          window.parent.load_vendor_fee_div();
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

  loadPricingModuleHtml = function($val){
    if ($val == "Y"){
      $("#fee_price_row").hide();
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_load_fee_pricing_model.php',
        data: $("#vendor_fee_frm").serialize(),
        type: 'POST',
        success: function(res) {
          $('#pricing_model_div').html(res).show();
          $("#ajax_loader").hide();
            setFeeAmountControl();
        }
      });
    }else{
       $('#pricing_model_div').html('').hide();
       $("#fee_price_row").show();
    } 
  }; 


   setFeeAmountControl = function(){
    $fee_method = '<?= $fee_method ?>';
    if($fee_method == 'Percentage'){
      
      $(".priceControl").attr("placeholder", "0");
      removePriceControl();
      PercentagePriceControl();
    }else{
      $(".priceControl").attr("placeholder","10.00");
      addPriceControl();
    }
  };

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
      limit: 5,
      centsLimit: 2,
    });
  }

  removePriceControl = function() {
    $('.priceControl').unpriceFormat();
  }
 
</script>