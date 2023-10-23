<form action="" id="regenerateFrm" name="regenerateFrm">
  <input type="hidden" name="action" id="action" value="">
   <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1">

<!-- Select Order Type Code Start -->
  <div class="panel panel-default panel-block advance_info_div">
    <div class="panel-body">
      <div class="phone-control-wrap ">
        <div class="phone-addon w-90">
          <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="70px">
        </div>
        <div class="phone-addon text-left">
          <p class="fs14"> Regenerating a commission can be done by agent, order, or product for varying date ranges.  Select the button below and follow the on-screen instructions to regenerate commission(s).</p>
          <div class="info_box_max_width theme-form roboto_font">
            <div class="phone-control-wrap">
              <div class="phone-addon">
                <div class="form-group">
                  <select class="form-control" id="select_type" name="select_type" onchange="selectOrderType($(this))">
                    <option></option>
                    <option value="specific_date_range">All Orders within a specific date range</option>
                    <option value="all_order_specific_product">All Orders with a specific product</option>
                    <option value="specific_order">Specific Order(s)</option>
                  </select>
                  <label>Select Order Type</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- Select Order Type Code Ends -->

<!-- Default Regenerate Div Code Start -->
  <div class="panel panel-default panel-block" style="min-height: 195px;" id="default_regenerate_div">
    <div class="panel-body">
      <div class="regenerate_commission_box">
        <div class="regenerate_commission_box_inner text-center">
          <h4 class="m-t-0 m-b-25 text-uppercase">Regenerate Commissions</h4>
          <p>Select the type of order you would like to regenerate from above</p>
        </div>
      </div>
    </div>
  </div>
<!-- Default Regenerate Div Code Ends -->

<!-- Regenerate Commission Code Start -->
<div class="panel panel-default panel-block" id="specific_commission_div">
  <div class="panel-body">
    <h4 class="m-t-0 m-b-25">Regenerate Commission</h4>
    <div class="row theme-form">

      <div class="clearfix">
      <!-- Specific Orders Code Start -->
        <div class="col-md-6 regenerate_commission_div" id="specific_order">
          <div class="form-group height_auto">
                <input class="listing_search" name="orders" id="orders" type="text"/>
                <label>Search Specific Order(s)</label>
                <p class="error error_specific_order"></p>
          </div>
        </div>
      <!-- Specific Orders Code Ends -->

      </div>

      <!-- Specific Products Code Start -->
        <div class="col-md-6 regenerate_commission_div" id="specific_product">
          <div class="form-group height_auto">
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
      <!-- Specific Products Code Ends -->

      <div class="clearfix"></div>

      <!-- Specific Date Range Code Start -->      
        <div class="col-md-6 regenerate_commission_div" id="specific_date_range">
          <p>Select Order Date</p>
          <div class="row">
            <div id="date_range" class="col-md-12">
              <div class="form-group">
                <select class="form-control" id="join_range" name="join_range">
                  <option value=""> </option>
                  <!-- <option value="Range">Range</option> -->
                  <option value="Exactly">Exactly</option>
                  <!-- <option value="Before">Before</option> -->
                  <!-- <option value="After">After</option> -->
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
      <!-- Specific Date Range Code Ends -->

      <div class="clearfix"></div>

      <!-- Select Commission Periods Code Start --> 
        <div class="col-sm-6 regenerate_commission_div" id="select_commission_period">
          <p>Select Regenerate Commission Period</p>
          <div class="form-group">
            <select class="form-control" name="period" id="period">
              <option data-hidden="true"></option>
              <option value="current_period">Current Period</option>
              <option value="period_earned">Period Earned</option>
            </select>
            <label>Select Regenerate Commission Period</label>
            <p class="error error_period"></p>
          </div>
        </div>
      <!-- Select Commission Periods Code Ends --> 
      
      <div class="clearfix"></div>

      <!-- Display Current Commission Periods Code Start -->
        <div class="col-md-4" id="current_commission_period">
          <table class="table m-b-20 br-a rounded">
            <tbody>
                <tr>
                  <td class="bg_light_gray"><strong>Weekly Period</strong></td>
                  <td class="text-right bg_light_gray"><span><?=getCustomDate($weeklyPeriodStart)?></span> - <span><?=getCustomDate($weeklyPeriodEnd)?></span>
                  </td>
                </tr>

                <tr>
                  <td class=""><strong>Monthly Period</strong></td>
                  <td class="text-right"><span><?=$monthlyCommPeriod?></span></td>
                </tr>
            </tbody>
          </table>
        </div>
      <!-- Display Current Commission Periods Code Ends -->

    </div>

    <div class="text-center">
      <div class="clearfix m-t-15">
        <a href="javascript:void(0);" class="btn btn-action" id="validateOrders">Validate Order(s)</a>
        <a href="javascript:void(0);" class="btn red-link" onclick="$('#specific_commission_div').hide();$('#default_regenerate_div').show();$('#select_type').val('').trigger('change');">Cancel</a>
      </div>
    </div>
  </div>
</div>
<!-- Regenerate Commission Code Ends -->

<div id="effOrdersDiv"></div>
</form>

<script type="text/javascript">

$(document).ready(function() {
  dropdown_pagination('effOrdersDiv'); 
  $('#specific_commission_div').hide();
  $('#current_commission_period').hide();
  
  $("#products").multipleSelect({
     selectAll: false
  });
  initSelectize('orders','CommissionOrderID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);

  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });

  $(document).off('change', '#join_range');
  $(document).on('change', '#join_range', function(e) {
    e.preventDefault();
    if($(this).val() == ''){
      $('.select_date_div').hide();
      $('#date_range').removeClass('col-sm-4').addClass('col-sm-12');
    }else{
      $('#date_range').removeClass('col-sm-12').addClass('col-sm-4');
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



  $(document).off("change",'#period');
  $(document).on("change",'#period',function(e){
    if($(this).val() == "current_period"){
      $("#current_commission_period").show();
    }else{
      $("#current_commission_period").hide();
    }
  });

  $(document).off("click",'#validateOrders');
  $(document).on("click",'#validateOrders',function(e){
    $("#action").val("validateOrder");
    regenerateCommission();
  });

  $(document).off('click',"#regenerateCommissionBtn");
  $(document).on('click',"#regenerateCommissionBtn",function(e){
    $("#action").val("regenerateCommission");
      regenerateCommission();
  });

});

function selectOrderType(element){
  var $selOdrType = element.val();

  $("#default_regenerate_div").hide();
  $(".regenerate_commission_div").hide();
  $("#specific_commission_div").show();

  if($selOdrType == 'specific_date_range'){
    $("#specific_date_range").show();
    $("#select_commission_period").show();
  }else if($selOdrType == 'all_order_specific_product'){
    $("#specific_product").show();
    $("#select_commission_period").show();
  }else if($selOdrType == 'specific_order'){
    $("#specific_order").show();
    $("#select_commission_period").show();
  }else{
    $("#default_regenerate_div").show();
    $("#specific_commission_div").hide();
  }
  return false;
}

getEffectedOrders = function() {
  $('#effOrdersDiv').hide();
  var params = $("#regenerateFrm").serialize();
  $.ajax({
    url: 'get_regenerate_commissions_orders.php',
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

regenerateCommission = function(){
  $.ajax({
    url: 'ajax_regenerate_commissions.php',
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
           window.location='regenerate_commissions.php';
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