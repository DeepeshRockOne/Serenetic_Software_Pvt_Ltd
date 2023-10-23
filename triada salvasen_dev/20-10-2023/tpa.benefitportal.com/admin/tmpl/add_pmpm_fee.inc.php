<?php include "notify.inc.php";?>
<div class="add_level_panelwrap add_fee_wrap">
  <form name="pmpm_fee_frm" method="Post" id="pmpm_fee_frm" class="theme-form ">
    <!-- flag used for load pricing module -->
    <input type="hidden" name="isPmPmComm" value="Y">
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <h4 class="mn fw500">Add PMPM</h4>
    </div>
    <div class="panel-body theme-form">
      <p class="fw500">PMPMP Information</p>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
              <select name="products[]" id="products" multiple="multiple" class="se_multiple_select" >
                <?php if(!empty($productRes)){ ?>
                    <?php foreach ($productRes as $key=> $category) { ?>
                      <?php if(!empty($category)){ ?>
                <optgroup label='<?= $key ?>'>
                  <?php foreach ($category as $pkey => $row) { ?>
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
                <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i> </span>
                  <div class="pr">
                    <input type="text" class="form-control date_picker" name="effective_date" id="effective_date" value="<?=$effective_date?>"/>
                    <label>Effective Date</label>
                  </div>
                </div>
                <p class="error" id="error_effective_date"></p>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i> </span>
                  <div class="pr">
                    <input type="text" class="form-control date_picker" name="termination_date" id="termination_date" value="<?=$termination_date?>"/>
                    <label>Termination Date</label>
                    <p class="error" id="error_termination_date"></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
          <div class="form-group">
            <input name="display_id" id="display_id" type="text" class="form-control" value="<?=$display_id?>" />
            <label>PMPM ID</label>
            <p class="error" id="error_display_id"></p>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
              <select class="se_multiple_select" multiple="multiple" name="receiving_agents[]" id="receiving_agents">
                <?php foreach ($agents as $k => $v) { ?>
                  <option value="<?=$v['id']?>" data-id="<?=$v['id']?>" data-rep_id="<?=$v['rep_id']?>" data-name="<?=$v['agent_name']?>" data-email="<?=$v['email']?>" <?=in_array($v['id'],array_keys($receiving_agents)) ? 'selected = "selected"' : ""?>><?=$v['rep_id'] . " - " . $v['agent_name']?></option>
                <?php } ?>
              </select>
              <label>Enrolling Agent(s)</label>
              <p class="error" id="error_receiving_agents"></p>
            </div>
        </div>
      </div>
      <div class="table-responsive" id="agents_selection_wraper">
        <table class="table pm_table text-left">
          <thead>
            <tr>
              <th >Agent ID </th>
              <th >Agent Name </th>
              <th >Agent Email </th>
              <th class="text-center">Include LOA Only? </th>
              <th class="text-center">Include Downline? </th>
              <th> </th>
            </tr>
          </thead>
          <tbody id="agents_section">
            <?php 
              if(!empty($receiving_agents)) {
                  foreach ($receiving_agents as $key => $agent_row) {
                      ?>
                    <tr class="agent_row_<?=$agent_row['agent_id']?>">
                      <td class="text-left rep_id"><?=$agent_row['rep_id']?></td>
                      <td class="text-left name"><?=$agent_row['agent_name']?></td>
                      <td class="text-left email"><?=$agent_row['email']?></td>
                      <td class="text-center"><input type="checkbox" name="include_loa_checked_status[]" value="<?=$agent_row['agent_id']?>" <?=$agent_row['include_loa'] == 'Y' ? "checked":""?>></td>
                      <td class="text-center"><input type="checkbox" name="downline_checked_status[]" value="<?=$agent_row['agent_id']?>" <?=$agent_row['include_downline'] == 'Y' ? "checked":""?>></td>
                      <td class="icons text-right">
                      <a href="javascript:void(0);" class="btn_remove_agent" data-agent_id="<?=$agent_row['agent_id']?>"> <i class="fa fa-trash" aria-hidden="true"> </i> </a></td>
                    </tr>
            <?php
                  }
              }
            ?>
          </tbody>
        </table>
      </div>
      <p class="fs14 m-t-20"><strong class="fw500">Commission Structure</strong></p>
      <div class="table-responsive">
        <table class="<?=$table_class?> m-b-5">
          <tbody>
            <tr class="lightblue_tr_bg">
              <td>Earned on new business?</td>
              <td class="w-110"><label class="mn">
                  <input type="radio" class="is_earned_on_new_business" name="is_earned_on_new_business" id="is_earned_on_new_business_N" value="N" <?= (!empty($is_earned_on_new_business) && $is_earned_on_new_business == 'N') ? 'checked' : '' ?>/>
                  No</label></td>
              <td class="w-110"><label class="mn">
                  <input type="radio" class="is_earned_on_new_business" name="is_earned_on_new_business" id="is_earned_on_new_business_Y" value="Y" <?= (!empty($is_earned_on_new_business) && $is_earned_on_new_business == 'Y') ? 'checked' : '' ?>/>
                  Yes</label></td>
              <p class="error" id="error_is_earned_on_new_business"></p>
            </tr>
          </tbody>
        </table>
        <table class="<?=$table_class?> m-b-5 table_br_gray">
          <tbody>
            <tr>
              <td colspan="2">Earned on renewals?</td>
              <td class="w-110 "><label class="mn">
                  <input type="radio" class="is_fee_on_renewal" name="is_fee_on_renewal" id="is_fee_on_renewal_N" value="N" <?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'N') ? 'checked' : '' ?>/>
                  No</label></td>
              <td class="w-110 "><label class="mn">
                  <input type="radio" class="is_fee_on_renewal" name="is_fee_on_renewal" id="is_fee_on_renewal_Y" value="Y"  <?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'Y') ? 'checked' : '' ?>/>
                  Yes</label></td>
              <p class="error" id="error_is_fee_on_renewal"></p>
            </tr>
              </tr>
            
            <tr id="renewal_numbers_row" style="display:<?= (!empty($is_fee_on_renewal) && $is_fee_on_renewal == 'Y') ? '' : 'none' ?>" >
              <td ><span class="p-l-40">Set the number of renewals: </span></td>
              <td class="w-130"><label class="mn">
                  <input type="radio" class="fee_renewal_type" name="fee_renewal_type" id="fee_renewal_type_Continuous" value="Continuous" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Continuous') ? 'checked' : '' ?>/>
                  Continuous</label>
              </td>
              <td class="w-110"><label class="mn">
                  <input type="radio" class="fee_renewal_type" name="fee_renewal_type" id="fee_renewal_type_Renewals" value="Renewals" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Renewals') ? 'checked' : '' ?> />
                  Renewals</label>
              </td>
              <td class="w-110">
                <select class="form-control max-w75" name="fee_renewal_count" id="fee_renewal_count" <?= (!empty($fee_renewal_type) && $fee_renewal_type == 'Renewals') ? '' : 'disabled' ?>>
                  <?php for($i=0;$i<=12;$i++) { ?>
                    <option value="<?= $i ?>" <?= (!empty($fee_renewal_count) && $i == $fee_renewal_count) ? 'selected=selected' : '' ?>><?= $i ?></option>
                 <?php } ?>
                </select>
              </td>

            </tr>
          </tbody>
        </table>
        <table class="<?=$table_class?> m-b-5 table_br_gray">
          <tbody>
            <tr class="lightblue_tr_bg">
              <td>Amount vary by plan tier?</td>
              <td class="w-110"><label class="mn">
                  <input type="radio" name="is_benefit_tier" id="is_benefit_tier_N" class="is_benefit_tier" value="N" <?= (!empty($is_benefit_tier) && $is_benefit_tier == 'N') ? 'checked' : '' ?>/>
                  No</label></td>
              <td class="w-110"><label class="mn">
                  <input type="radio" name="is_benefit_tier" id="is_benefit_tier_Y" class="is_benefit_tier" value="Y" <?= (!empty($is_benefit_tier) && $is_benefit_tier == 'Y') ? 'checked' : '' ?> />
                  Yes</label></td>
            </tr>
            <tr>
                <td colspan="3" id="pricing_model_div" class="pn">
                   <p class="error" id="error_pricing_model"></p>
                </td>
            </tr>
          </tbody>
        </table>
        <table class="<?=$table_class?> m-b-5 table_br_gray">
          <tbody>
            <tr>
              <td colspan="2">How is the amount calculated?</td>
              <td class="w-110 "><label class="mn">
                  <input type="radio" name="fee_type" id="fee_type_no" value="Amount" class="fee_type" <?= (!empty($fee_type) && $fee_type == 'Amount') ? 'checked' : '' ?>/>
                  Fixed Price</label></td>
              <td class="w-110 "><label class="mn">
                  <input type="radio" name="fee_type" id="fee_type_yes" value="Percentage" class="fee_type" <?= (!empty($fee_type) && $fee_type == 'Percentage') ? 'checked' : '' ?>/>
                  Percentage</label></td>
            </tr>
              </tr>
            
            <tr id="fee_per_calculate_on_section" style="<?=isset($fee_type) && $fee_type=="Percentage"?"":"display: none;"?>">
              <td >How is the % calculated?</td>
              <td>
                  <label class="mn">
                    <input type="radio" name="percentage_type" id="percentage_type_Retail" value="RetailPrice" class="percentage_type" <?= (!empty($percentage_type) && $percentage_type == 'RetailPrice') ? 'checked' : '' ?>/>Retail Price
                  </label>
                </td>
                <td>
                  <label class="mn">
                    <input type="radio" name="percentage_type" id="percentage_type_Commissionable" value="CommissionableAmount" class="percentage_type" <?= (!empty($percentage_type) && $percentage_type == 'CommissionableAmount') ? 'checked' : '' ?> />Commissionable Price
                  </label>
                </td>
                <td>
                  <label class="mn">
                    <input type="radio" name="percentage_type" id="percentage_type_NonCommissionable" value="NonCommissionableAmount" class="percentage_type" <?= (!empty($percentage_type) && $percentage_type == 'NonCommissionableAmount') ? 'checked' : '' ?> />Non-commissionable Price
                  </label>
                </td>
            </tr>
            <tr id="fee_price_row" style="<?= (!empty($is_benefit_tier) && $is_benefit_tier=='N')?'' : 'display:none'; ?>" >
                <td colspan="3">
                  <div class="col-sm-4">
                    <label>PMPM Amount</label>
                    <div class="add_fee_addonwrap">
                      <div class="<?= (!empty($fee_type) && $fee_type=='Amount') ? 'add_fee_addon' : 'add_fee_addon_percentage' ?>">
                        <i class="<?= (!empty($fee_type) && $fee_type=='Amount') ? 'fa fa-usd' : 'fa fa-percent' ?>  fee_calculated_type"></i>
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
      
      <div class="text-center m-t-30"> 
          <input type="hidden" name="pmpm_id" id="pmpm_id" value="<?= $pmpm_id ?>">
          <input type="hidden" name="pmpm_fee_id" id="pmpm_fee_id" value="<?= $pmpm_fee_id ?>">
          <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
          <input type="button" name="add_fee" id="add_fee" class="btn btn-action" value="<?= (checkIsset($fee_id)) ? 'Save Fee' : 'Add Fee' ?>"> 
          <a href="javascript:void(0);" onclick='parent.$.colorbox.close();' class="btn red-link">Cancel</a>
      </div>
  </div>
</form>
</div>
<div id="agent_row_template" style="display: none;">
    <table>
        <tbody>
            <tr>
                <td class="rep_id text-left"></td>
                <td class="name text-left"></td>
                <td class="email text-left"></td>
                <td class="text-center">
                    <input type="checkbox" name="include_loa_checked_status[]" value="">
                </td>
                <td class="text-center">
                    <input type="checkbox" name="downline_checked_status[]" value="">
                </td>
                <td class="icons text-right">
                    <a href="javascript:void(0);" class="btn_remove_agent" data-agent_id="0">
                        <i class="fa fa-trash fa-lg" aria-hidden="true">
                        </i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
$(document).ready(function() {
  hide_show_agent_template();
  setFeeAmountControl();

  $(document).on("click", ".btn_remove_agent", function() {
    var agent_id = $(this).data('agent_id');
    $('.agent_row_' + agent_id).hide();
    $("#receiving_agents option[value='" + agent_id + "']").prop("selected", false);
    $('#receiving_agents').multipleSelect("refresh");
    hide_show_agent_template();
  });

  $(document).on("click", "#add_fee", function() {
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_add_pmpm_fee.php',
      dataType: 'JSON',
      data: $("#pmpm_fee_frm").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        $('.error').html('');
        if (res.status == "success") {
          if (res.pmpm_fee_id) {
            if (window.parent.$("#ids").val()) {
              window.parent.$("#ids").val(window.parent.$("#ids").val() + ',' + res.pmpm_fee_id);
            } else {
              window.parent.$("#ids").val(res.pmpm_fee_id);
            }
          }
          window.parent.load_pmpm_fee_div();
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

  $("#products").multipleSelect({
    onClick: function(e) {
      var val = $("input[name='is_benefit_tier']:checked").val();
      loadPricingModuleHtml(val);
    },
    onTagRemove: function(e) {
      var val = $("input[name='is_benefit_tier']:checked").val();
      loadPricingModuleHtml(val);
    }
  });

  $is_benefit_tier = '<?= $is_benefit_tier ?>';
  if ($is_benefit_tier == 'Y') {
    loadPricingModuleHtml($is_benefit_tier);
  }

  $("#receiving_agents").multipleSelect({
    filter: true,
    selectAll: false,
    onClick: function(e) {
      //console.log(e);
      var is_checked = e.selected;
      var agent_id = e.value;
      if (is_checked == true) {
        var option_obj = $("#receiving_agents option[value='" + agent_id + "']");
        var agent_row_template = $("#agent_row_template").find("tr").clone();
        agent_row_template.addClass('agent_row_' + agent_id);
        agent_row_template.find('.rep_id').html(option_obj.attr('data-rep_id'));
        agent_row_template.find('.name').html(option_obj.attr('data-name'));
        agent_row_template.find('.email').html(option_obj.attr('data-email'));
        agent_row_template.find('.btn_remove_agent').attr('data-agent_id', agent_id);
        $("#agents_section").append(agent_row_template);
        $(".agent_row_" + agent_id).find("input[type='checkbox']").attr('value', agent_id).uniform();
      } else {
        $(".agent_row_" + agent_id).remove();
      }
      hide_show_agent_template();
    }
  });
  $(".date_picker").datepicker({
    // orientation: 'auto top'
  });
});

function hide_show_agent_template() {
  if ($("#receiving_agents option:selected").length > 0) {
    $('#agents_selection_wraper').show();
  } else {
    $('#agents_selection_wraper').hide();
  }
};
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

$(document).off("change", ".fee_type");
$(document).on("change", ".fee_type", function(e) {
  e.preventDefault();
  $val = $(this).val();
  if ($val == "Percentage") {
    $("#percentage_type_row").show();
    $(".fee_calculated_type").removeClass('fa-usd').addClass('fa-percent');
    $(".fee_calculated_type").parent('div').addClass('add_fee_addon_percentage');
    $(".fee_calculated_type").parent('div').removeClass('add_fee_addon');

    $(".priceControl").val("");
    $(".priceControl").attr("placeholder", "0");
    $("#fee_per_calculate_on_section").show();
    removePriceControl();
    PercentagePriceControl();
  } else {
    $("#percentage_type_row").hide();
    $(".fee_calculated_type").removeClass('fa-percent').addClass('fa-usd');
    $(".fee_calculated_type").parent('div').removeClass('add_fee_addon_percentage');
    $(".fee_calculated_type").parent('div').addClass('add_fee_addon');

    $(".priceControl").val("");
    $(".priceControl").attr("placeholder", "10.00");
    $("#fee_per_calculate_on_section").hide();
    removePriceControl();
    addPriceControl();
  }
});
loadPricingModuleHtml = function($val) {
  if ($val == "Y") {
    $("#fee_price_row").hide();
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_load_fee_pricing_model.php',
      data: $("#pmpm_fee_frm").serialize(),
      type: 'POST',
      success: function(res) {
        $('#pricing_model_div').html(res).show();
        $("#ajax_loader").hide();
        setFeeAmountControl();
      }
    });
  } else {
    $('#pricing_model_div').html('').hide();
    $("#fee_price_row").show();
  }
};
setFeeAmountControl = function() {
  $fee_type = '<?= $fee_type ?>';
  if ($fee_type == 'Percentage') {

    $(".priceControl").attr("placeholder", "0");
    removePriceControl();
    PercentagePriceControl();
  } else {
    $(".priceControl").attr("placeholder", "10.00");
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
    limit: false,
    centsLimit: 2,
  });
}
removePriceControl = function() {
  $('.priceControl').unpriceFormat();
}
</script>