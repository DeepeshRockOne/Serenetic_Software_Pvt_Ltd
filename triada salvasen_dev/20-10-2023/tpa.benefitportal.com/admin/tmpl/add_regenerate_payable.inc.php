<form action="" id="regenerateFrm" name="regenerateFrm">
<input type="hidden" name="start_date" value="<?=$start_date?>">
<input type="hidden" name="end_date" value="<?=$end_date?>">
<input type="hidden" name="action" id="action" value="">
<input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1">
<div class="panel panel-default panel-block">
  <div class="panel-body advance_info_div">
    <div class="phone-control-wrap">
      <div class="phone-addon w-90 v-align-top">
        <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="85px">
      </div>
      <div class="phone-addon text-left">
        <div class="info_box_max_width theme-form">
          <p>Regenerating a payable can be done by agent, order, or product for varying date ranges.  Select the button below and follow the on-screen instructions to regenerate payable(s).</p>
          <div class="phone-control-wrap">
            <div class="phone-addon">
              <div class="form-group">
                <select class="form-control" id="select_type" name="select_type" onchange="selectOrderType($(this))">
                  <option value=""></option>
                  <option value="specific_date_range">All Orders within a specific date range</option>
                  <option value="all_order_specific_product">All Orders with a specific product</option>
                  <option value="specific_order">Specific Order(s)</option>
                </select>
                <label>Select Order Type</label>
              </div>
            </div>
            <!-- <div class="phone-addon w-80">
              <div class="form-group height_auto m-b-15">
                <a href="javascript:void(0);" class="btn btn-action btn-block" id="select_payable">Submit</a>
              </div>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="panel panel-default panel-block" style="min-height: 195px;" id="default_regenerate_div">
  <div class="panel-body">
    <div class="regenerate_commission_box">
      <div class="regenerate_commission_box_inner text-center">
        <h4 class="m-t-0 m-b-25 text-uppercase">Regenerate Payable</h4>
        <p>Select the type of order you would like to regenerate from above</p>
      </div>
    </div>
  </div>
</div>

<div class="panel panel-default" id="specific_payable_div">
    <div class="panel-body">
      <h4 class="m-t-0 m-b-20">Regenerate Payable</h4>
      <div class="row theme-form">
        <div class="col-lg-7 col-md-8 regenerate_payable_div" id="specific_order">
          <div class="form-group height_auto">
                <input class="listing_search" name="orders" id="orders" type="text" />
                <label>Search Specific Order(s)</label>
                <p class="error error_specific_order"></p>
          </div>
        </div>
        <div class="clearfix">
        </div>
        <div class="col-lg-7 col-md-8 regenerate_payable_div" id="product_div">
          <div class="form-group ">
              <select class="se_multiple_select" name="products[]"  id="products" multiple="multiple" >
                <?php if(!empty($productRes)){ ?>
                    <?php foreach ($productRes as $key=> $category) { ?>
                      <?php if(!empty($category)){ ?>
                  <optgroup label='<?= $key ?>'>
                    <?php foreach ($category as $pkey => $row) { ?>
                      <option value="<?= $row['id'] ?>" <?= (!empty($products) && in_array($row['id'], $products)) ? 'selected="selected"' : '' ?> <?= (!empty($assigned_products) && in_array($row['id'], $assigned_products)) ? 'disabled="disabled"' : '' ?> >
                        <?= $row['name'] .' ('.$row['product_code'].')'?>    
                      </option>
                    <?php } ?>
                  </optgroup>
                    <?php } ?>
                  <?php } ?>
                <?php } ?>
              </select>
              <label>Products</label>
              <p class="error error_products"></p>
          </div>
        </div>
        <div class="clearfix">
        </div>  
        <div class="col-lg-7 col-md-8 regenerate_payable_div" id="date_range_div">
          <p>Select Order Date</p>
          <div class="row">
            <div id="date_range" class="col-md-12">
              <div class="form-group">
                <select class="form-control" id="join_range" name="join_range">
                  <option value=""> </option>
                  <option value="Range">Range</option>
                  <option value="Exactly">Exactly</option>
                  <option value="Before">Before</option>
                  <option value="After">After</option>
                </select>
                <label>Select</label>
                <p class="error error_join_range"></p>
              </div>
            </div>
            <div class="select_date_div col-md-9" style="display:none">
              <div class="form-group">
                <div id="all_join" class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <div class="pr">
                    <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                    <label>MM / DD / YYYY</label>
                  </div>
                </div>
                <div  id="range_join" style="display:none;">
                  <div class="phone-control-wrap">
                    <div class="phone-addon">
                      <label class="mn">From</label>
                    </div>
                    <div class="phone-addon">
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <div class="pr">
                          <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                          <label>MM / DD / YYYY</label>
                        </div>
                      </div>
                    </div>
                    <div class="phone-addon">
                      <label class="mn">To</label>
                    </div>
                    <div class="phone-addon">
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <div class="pr">
                          <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                          <label>MM / DD / YYYY</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <p class="error error_added_date"></p>
                <p class="error error_from_to"></p>
              </div>
            </div>
            
          </div>
        </div>
        <div class="clearfix">
        </div>
        <div class="col-lg-7 col-md-8 regenerate_payable_div" id="payable_period">
          <p>Select Regenerate Payables Period</p>
          <div class="form-group">
            <select class="form-control" name="period" id="period">
              <option data-hidden="true"></option>
              <option value="current_period">Current Period</option>
              <option value="period_earned">Period Earned</option>
            </select>
            <label>Select Regenerate Payables Period</label>
            <p class="error error_period"></p>
          </div>
        </div>  
        <div class="clearfix">
        </div>
        <div class="col-lg-7 col-md-8 hidden" id="current_period_div">
          <table class="table m-b-20 br-a rounded">
            <tbody>
                <tr>
                  <td class="bg_light_gray"><strong>New Business Period</strong></td>
                  <td class="text-right bg_light_gray"><span><?=getCustomDate($start_date)?></span> - <span><?=getCustomDate($end_date)?></span>
                  </td>
                </tr>

                <tr>
                  <td class=""><strong>Renewal Period</strong></td>
                  <td class="text-right"><span><?=date('M Y')?></span></td>
                </tr>
            </tbody>
          </table>
        </div>
              
      </div>
      <div class="text-center">
        <div class="clearfix m-t-15">
          <!-- <a href="javascript:void(0);" class="btn btn-action" id="regenerate_payable_btn">Regenerate</a> -->
          <a href="javascript:void(0);" class="btn btn-action" id="validateOrders">Validate Order(s)</a>
          <a href="javascript:void(0);" class="btn red-link" onclick="$('#specific_payable_div').hide();$('#default_regenerate_div').show();$('#select_type').val('').trigger('change');">Cancel</a>
        </div>
      </div>
    </div>
</div>
<div id="effOrdersDiv"></div>
</form>
<script type="text/javascript">
$(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
  e.preventDefault();
  $('.select_date_div').hide();
  if($(this).val() == ''){
    $('#date_range').removeClass('col-md-3').addClass('col-md-12');
  }else{
    $('#date_range').removeClass('col-md-12').addClass('col-md-3');
    $('.select_date_div').show();
    if ($(this).val() == 'Range') {
      $('#range_join').show();
      $('#all_join').hide();
    } else {
      $('#range_join').hide();
      $('#all_join').show();
    }
  }
});
$(document).ready(function() {
  dropdown_pagination('effOrdersDiv')

  $('#specific_payable_div').hide();
  $("#products").multipleSelect({});
  initSelectize('orders','PayableOrderID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
  
  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });
});
$(document).off("click",'#validateOrders');
$(document).on("click",'#validateOrders',function(e){
  $("#action").val("validateOrder");
  regeneratePayable();
});

$(document).off('click',"#regenerate_payable_btn");
$(document).on('click',"#regenerate_payable_btn",function(e){
  $("#action").val("regenerateCommission");
  regeneratePayable();
  // $.ajax({
  //   url: 'ajax_regenerate_payables.php',
  //   type: 'POST',
  //   data: $("#regenerateFrm").serialize(),
  //   beforeSend : function(){
  //     $(".error").hide();
  //     $('#ajax_loader').show();
  //   },
  //   success: function(res) {
  //     $('#ajax_loader').hide();
  //     $(".error").hide();
  //     if(res.status == 'success'){
  //       location.redirect = 'refresh';
  //       window.location = 'regenerate_payable.php';
  //     }else if(res.status == 'fail'){
  //       window.location = 'regenerate_payable.php';
  //     }else{
  //       $.each(res.errors, function(key, value) {
  //         $('.error_' + key).html(value).show();
  //       });
  //     }
  //   }
  // });
  //   return false;
});

$(document).off("change",'#period');
$(document).on("change",'#period',function(e){
  if($(this).val() == "current_period"){
    $("#current_period_div").removeClass('hidden');
  }else{
    $("#current_period_div").addClass('hidden');
  }
});

function selectOrderType(element){
  var $payable = element.val();

  $("#default_regenerate_div").hide();
  $(".regenerate_payable_div").hide();
  $("#specific_payable_div").show();

  if($payable == 'specific_date_range'){
    $("#date_range_div").show();
    $("#payable_period").show();
  }else if($payable == 'all_order_specific_product'){
    // $("#date_range_div").show();
    $("#product_div").show();
    $("#payable_period").show();
  }else if($payable == 'specific_order'){
    $("#specific_order").show();
    // $("#product_div").show();
    $("#payable_period").show();
  }else{
    $("#default_regenerate_div").show();
    $("#specific_payable_div").hide();
  }
  return false;
}

getEffectedOrders = function() {
  $('#effOrdersDiv').hide();
  var params = $("#regenerateFrm").serialize();
  $.ajax({
    url: 'get_regenerate_payable_orders.php',
    type: 'GET',
    data: params,
    beforeSend:function(){
      $("#ajax_loader").show();
    },
    success: function(res) {
      $('#ajax_loader').hide();
      $('#effOrdersDiv').html(res).show();
      common_select();
    }
  });
}

regeneratePayable = function(){
  $.ajax({
    url: 'ajax_regenerate_payables.php',
    type: 'POST',
    data: $("#regenerateFrm").serialize(),
    dataType:'JSON',
    beforeSend : function(){
      $(".error").hide();
      $('#ajax_loader').show();
    },
    success: function(res) {
      $('#ajax_loader').hide();
      $(".error").hide();
      if(res.status == 'validateOrder'){
        $("#action").val("");
        getEffectedOrders();
      }else if(res.status == 'success'){
        setNotifySuccess(res.msg);
        setTimeout(function(){
           window.location='regenerate_payable.php';
        }, 1000);
      }else if(res.status == 'fail'){
        setNotifyError(res.msg);
      }else{
        $.each(res.errors, function(key, value) {
          $('.error_' + key).html(value).show();
        });
      }
    }
  });
}
</script>