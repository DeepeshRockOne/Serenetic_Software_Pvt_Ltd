<noscript>
    <div class="container text-center m-t-70">
      <h1 class="mn fw300">JavaScript is disabled</h1>
      <p>JavaScript is disabled on your browser. To start enrollment,<br/> Please enable JavaScript or upgrade to a JavaScript capable browser.</p>
    </div>
</noscript>
<div class="container text-center m-t-70" id="upgrade_browser_div" style="display: none;">
    <h1 class="mn fw300">Update your browser</h1>
    <p>This page does not support your version of browser. To start enrollment,<br/> Please update your browser.</p>
</div>

<style type="text/css">
<?php if(isset($_GET["iframe"])) {?> 
@media (min-width: 480px) {
 #enrollment_form .col-sm-6, #enrollment_form .col-lg-6, #enrollment_form .col-sm-3, #enrollment_form .col-sm-6 { width: 50%; float: left; }
}
<?php } ?>
</style>
<?php include "notify.inc.php"; ?>
<div id="enrollment_main_div" style="display: none;">
<?php if(in_array($enrollmentLocation,array("aae_site","self_enrollment_site")) || isset($display_header)) { ?>
<div class="quote_banner">
        <div class="container text-center">
          <h1 class="mn fw300">Member Application </h1>
        </div>
      </div>
<?php } else { ?>
<div class="agent_enroll_head">
    <?php if($enrollmentLocation == "member_portal") { ?>
      <div class="container text-center">
          <h1 class="fw300">Powering Change In Healthcare</h1>
      </div>
    <?php } else { ?>
      <div class="container">
          <h4>Instructions</h4>
          <p class="fs12"><p><?= isset($instruction_text) && !empty($instruction_text) ? $instruction_text : 'Welcome to your application engine! Follow the steps below to begin.' ?></p></p>
      </div>
    <?php } ?>
</div>
<?php } ?>
<div class="enroll_tab_wrapper">
  <div class="container">
    <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
      <li class="active"  data-tab="coverage_tab" id="li_coverage_detail"> <a data-toggle="tab" href="#coverage_detail" class="btn_step_heading enrollment_tabs" id="coverage_tab" data-step="1" data-is_validate="false">
             Plan Details 
        </a> 
      </li>
      <li class="<?=(!empty($found_state_id) || !empty($token)) ?'':'disabled'?>" data-tab="products_tab" id="li_product_detail"> <a data-toggle="tab" href="#products_detail" class="btn_step_heading enrollment_tabs" id="products_tab" data-step="2" data-is_validate="false">
            Products
        </a> 
      </li>
      <li class="<?=(!empty($found_state_id) || !empty($token)) ?'':'disabled'?>"  data-tab="details_tab" id="li_basic_detail"> 
        <a data-toggle="tab" href="#basic_detail" id="basic_detail_tab" class="btn_step_heading enrollment_tabs" data-step="3" data-is_validate="false">
            Application 
        </a> 
      </li>
      <li class="<?=(!empty($found_state_id) || !empty($token))?'':'disabled'?>"  data-tab="enroll_tab" id="li_payment_detail"> <a data-toggle="tab" href="#payment_detail" id="payment_detail_tab" class="btn_step_heading enrollment_tabs" data-step="4" data-is_validate="false">
            Payment
        </a> 
      </li>
      <li class="<?=(!empty($found_state_id) || !empty($token))?'':'disabled'?>" data-tab="enroll_tab" id="li_verification_detail"> <a data-toggle="tab" href="#verification_detail" id="verification_detail_tab" class="btn_step_heading enrollment_tabs" data-step="4" data-is_validate="false">
          Verification
      </a> 
      </li>
    </ul>
  </div>
   
  <div class="container">
    <div class="tab-subbar" id="TopSummaryBar" style="display: none;">
      <div class="row">
        <div class="col-sm-8">
      <div class="search_plan_left theme-form " id="filterByProductBar" style="display: none">
        <div class="form-group">
            <label class="m-r-10"><strong>Filter By Product</strong></label>
         </div>
         <div class="form-group">   
            <select name="product_filter" id="product_filter" class="se_multiple_select"  multiple="multiple">
            </select>
            <label>Select</label>
        </div>
      </div>
      </div>
      <div class="col-sm-4">
      <div class="enroll_right_cart dropdown" id="cartAmountBar" style="display: none">
      	<a href="javascript:void(0);" data-toggle="dropdown" class="dropdown-toggle">
              <span class="total_amount">$<span id="total_amount">0.00</span></span> 
              <span class="cart_icon" data-toggle="tooltip" title="Cart"><i class="material-icons">shopping_cart</i></span> 
              <span class="label label-rouded"><span id="cart_counter">0</span></span>
       	</a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="cart_body_scroll">
            <table width="100%" class="table mn" >
              <thead>
                <tr>
                  <td class="fs14 fw500">Added Plan</td>
                  <td class="fs14 fw500 contibution_price_td text-right">Member Rate</td>
                  <td class="fs14 fw500 contibution_price_td text-right">Group Rate</td>
                  <td class="fs14 fw500 contibution_price_td text-right">Full Rate</td>
                  <td>&nbsp;</td>
                </tr>
              </thead>
              <tbody id="addToCartTable">
              </tbody>
            </table>
          </div>
            <p class="line-title"></p>
            <table width="100%" class="table mn" >
              <tbody>
            	<tr>
                	<td>SubTotal(s) </td>
                    <td class="text-right">$<span id="cart_sub_total">0.00</span></td>
                    <td class="text-right contibution_price_td">$<span id="cart_sub_total_group_price">0.00</span></td>
                    <td class="text-right contibution_price_td">$<span id="total_cart_sub_total">0.00</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>Service fee(s) </td>
                    <td class="text-right">$<span id="cart_service_fee_total">0.00</span></td>
                    <td class="text-right contibution_price_td">$0.00</td>
                    <td class="text-right contibution_price_td">$<span id="total_cart_service_fee_total">0.00</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="fs14">
                  <td>Monthly Total</td>
                    <td class="text-right"><strong class="text-primary">$<span id="cart_monthly_total">0.00</span></strong></td>
                    <td class="text-right contibution_price_td"><strong class="text-primary">$<span id="cart_monthly_total_group_price">0.00</span></strong></td>
                    <td class="text-right contibution_price_td"><strong class="text-primary">$<span id="total_cart_monthly_total">0.00</span></strong></td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="cart_add_healthy_step_row">
                  <td colspan="2"><a href="javascript:void(0)" class="blue-link fw500" id="cart_display_healthy_step">+ Healthy Step</a></td>
                </tr>
                <tr class="cart_healthy_step_row">
                	<td id="cart_healthy_step_name"></td>
                    <td class="text-right">$<span id="cart_healthy_step_total">0.00</span></td>
                    <td class="text-right contibution_price_td">$0.00</td>
                    <td class="text-right contibution_price_td">$<span id="total_cart_healthy_step_total">0.00</span></td>
                    <td>&nbsp;</td>
                </tr>                
                <tr class="fs14">
                  <td>Today's Total</td>
                    <td class="text-right"><strong class="text-primary">$<span id="cart_total">0.00</span></strong></td>
                    <td class="text-right contibution_price_td"><strong class="text-primary">$<span id="cart_total_group_price">0.00</span></strong></td>
                    <td class="text-right contibution_price_td"><strong class="text-primary">$<span id="total_cart_total">0.00</span></strong></td>
                    <td>&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </div>
      </div>
            </div>
          </div>
    </div>
  </div>
</div>
<?php
  if(!isset($display_default_billing)) {
      $display_default_billing = 'N';
  }
  if(!isset($is_group_member)) {
    $is_group_member = 'N';
    if($enrollmentLocation == "groupSide") {
        $is_group_member = 'Y';
    }
  }
  if(!isset($sponsor_billing_method)) {
    $sponsor_billing_method = 'individual';
    if($is_group_member == 'Y' && !empty($group_billing_method)) {
        $sponsor_billing_method = $group_billing_method;
    }
  }
?>
<div class="quoting_engine_new aa_enrolls" id="main-contant">
  <form action="<?=$HOST?>/ajax_member_enrollment.php" role="form" method="post" name="enrollment_form" id="enrollment_form" autocomplete="false" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="group_billing_method" value="<?=isset($group_billing_method) ? $group_billing_method : ""?>">
    <input type="hidden" name="is_add_product" id="is_add_product" value="<?=isset($is_add_product)?1:0;?>">
    <input type="hidden" name="already_puchase_product" id="already_puchase_product" value="">
    <input type="hidden" name="enrollmentLocation" id="enrollmentLocation" value="<?= $enrollmentLocation ?>">
    <input type="hidden" name="is_group_member" id="is_group_member" value="<?= $is_group_member ?>">
    <input type="hidden" name="site_user_name" id="site_user_name" value="<?= isset($user_name)?$user_name:'' ?>">
    <input type="hidden" name="pb_id" id="pb_id" value="<?= isset($pb_id)?$pb_id:'0';?>">
    <input type="hidden" name="sponsor_id" id="sponsor_id" value="<?= $sponsor_id ?>">
    <input type="hidden" name="lead_id" id="lead_id" value="<?= isset($lead_id)?$lead_id:0?>">
    <input type="hidden" name="customer_id" id="customer_id" value="<?= isset($customer_id)?$customer_id:0?>">
    <input type="hidden" name="md5_customer_id" id="md5_customer_id" value="">
    <input type="hidden" name="md5_order_id" id="md5_order_id" value="">
    <input type="hidden" name="payment_type" id="payment_type" value="">
    <input type="hidden" name="added_product" id="added_product" value="0">
    <input type="hidden" name="dataStep" id="dataStep" value="0">
    <input type="hidden" name="action" id="action" value="">
    <input type="hidden" name="last_selected_product" id="last_selected_product" value="0">
    <input type="hidden" name="last_billing_profile_id" id="last_billing_profile_id" value="0">
    <input type="hidden" name="order_id" id="order_id" value="<?php echo isset($order_id) && $order_id>0?$order_id:""; ?>">
    <input type="hidden" name="submit_type" id="submit_type" value="">
    <input type="hidden" name="healthy_step_fee" id="healthy_step_fee" value="<?= isset($quote_healthy_step_fee) ? $quote_healthy_step_fee : 0 ?>"/>
    <input type="hidden" name="enrollment_type" value="<?= $enrollment_type ?>"/>
    <input type="hidden" name="lead_quote_detail_id" id="lead_quote_detail_id" value="<?= isset($lead_quote_detail_id)?$lead_quote_detail_id:"" ?>">
    <input type="hidden" name="lead_quote_plan_ids" id="lead_quote_plan_ids" value="<?= isset($lead_quote_plan_ids)?$lead_quote_plan_ids:"" ?>">
    <input type="hidden" name="product_list" id="product_list" value="">
    <input type="hidden" name="enrolleeElementsVal" id="enrolleeElementsVal" value="">
    <input type="hidden" name="dependent_array" id="dependent_array" value="">
    <input type="hidden" name="billing_display" id="billing_display" value="Y">
    <input type="hidden" name="only_waive_products" id="only_waive_products" value="N">
    <div class="tab-content">
      <!-- Stap 1 Start -->
      <div id="coverage_detail" class="tab-pane fade active in">
      	<div class="container">
        	<?php include ('enrollment_coverage_tab.inc.php'); ?>
         </div>
      </div>
      <!-- Stap 1 Start -->
      <!-- Stap 2 Start -->
      <div id="products_detail" class="tab-pane fade">
      	<div class="container">
        	<?php include ('enrollment_products_tab.inc.php'); ?>
         </div>
      </div>
      <!-- Step 2 End-->
      <!-- Stap 3 Start -->
      <div id="basic_detail" class="tab-pane fade">
      	<div class="container">
        	<?php include ('enrollment_basic_detail_tab.inc.php'); ?>
         </div>
      </div>
      <!-- Step 3 End-->
      <!-- Stap 4 Start -->
      <div id="payment_detail" class="tab-pane fade " data-step="4">
        	<?php include ('enrollment_payment_detail_tab.inc.php'); ?>          
      </div>
      <!-- Step 4 End--> 
      <!-- Stap 5 Start -->
      <div id="verification_detail" class="tab-pane fade" data-step="4">
        <?php include ('enrollment_verification_tab.inc.php'); ?>
      </div>
      <!-- Stap 5 End -->
    </div>

    <div style="display: none">
      <div id="billing_address_popup">
        <div class="panel panel-default panel-block panel-shadowless mn">
          <div class="panel-heading br-b">
            <h4>Billing Address - <span class="fw300"> Edit</span></h4>
          </div>
          <div class="panel-body "> 
             <div class="row theme-form">
              <div class="col-sm-6">
               <div class="form-group">
                <input type="text" name="bill_name" id="bill_name" value="" class="form-control" *>
                <label>Name*</label>
               </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-sm-6">
               <div class="form-group">
                <input type="text" name="bill_address" id="bill_address" value=""
                 class="required form-control tblur" *>
                <label>Address*</label>
                <span class="error" id="error_bill_address"></span></div>
              </div>
              <div class="col-sm-6">
               <div class="form-group">
                <input type="text" name="bill_address2" id="bill_address2" value=""
                 class="required form-control tblur" onkeypress="return block_special_char(event)" *>
                <label>Address 2 (suite, apt)</label>
                <span class="error" id="error_bill_address2"></span></div>
              </div>
              <div class="col-sm-4">
               <div class="form-group">
                <input type="text" name="bill_city" id="bill_city"
                 value="<?= !empty($billing_data['city']) ? $billing_data['city'] : '' ?>"
                 class="required form-control tblur" *>
                <label>City*</label>
                <span class="error" id="error_bill_city"></span></div>
              </div>
              <div class="col-sm-4">
               <div class="form-group">
                <select id="bill_state" name="bill_state" class="tblur form-control popup-dropdown">
                  <option value=""></option>
                  <?php if (count($allStateRes) > 0) { ?>
                   <?php foreach ($allStateRes as $key => $value) { ?>
                    <option  value="<?= $value["name"]; ?>"><?php echo $value['name']; ?></option>
                   <?php } ?>
                  <?php } ?>
                </select>
                <label>State*</label>
                <span class="error" id="error_bill_state"></span></div>
              </div>
              <div class="col-sm-4">
               <div class="form-group ">
                <input type="text" name="bill_zip" id="bill_zip"
                 value="<?= !empty($billing_data['zip']) ? $billing_data['zip'] : '' ?>"
                 class="required form-control tblur"
                 maxlength="<?php echo $bill_country == 231 ? '5' : '7'; ?>" *>
                <label>Zip/Postal Code*</label>
                <span class="error" id="error_bill_zip"></span></div>
              </div>
              <div class="col-sm-12 text-center">
                 <input id="billing_save" type="button" class="btn btn-action" value="Save" />
              </div>
             </div> 
          </div>
          
        </div>
      </div>
    </div>
  </form>
</div>
<?php  //jquery code file; 
include_once __DIR__ . '/member_enrollment_jquery.inc.php'; 
include_once __DIR__ . '/member_enrollment_clone_data_file.inc.php'; 
include_once __DIR__ . '/product_descriptions_clone_file.inc.php'; 
?>
</div>
<script type="text/javascript"> 
navigator.sayswho = ( function () {
    var ua = navigator.userAgent, tem,
        M = ua.match( /(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i ) || [];
    if ( /trident/i.test( M[1] ) ) {
        tem = /\brv[ :]+(\d+)/g.exec( ua ) || [];
        return 'IE ' + ( tem[1] || '' );
    }
    if ( M[1] === 'Chrome' ) {
        tem = ua.match( /\b(OPR|Edge)\/(\d+)/ );
        if ( tem != null ) return tem.slice( 1 ).join( ' ' ).replace( 'OPR', 'Opera' );
    }
    M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
    if ( ( tem = ua.match( /version\/(\d+)/i ) ) != null ) M.splice( 1, 1, tem[1] );
    return M.join( ' ' );
} )();
//document.getElementById('printVer').innerHTML=navigator.sayswho
var browser_str = navigator.sayswho;
var browser_name = browser_str.substring(0,browser_str.indexOf(" "));
var browser_version = browser_str.substring(browser_str.indexOf(" "));
browser_version = browser_version.trim();
browser_version = parseInt(browser_version);
/*
We are use jQuery v1.12.4
Browser Support in jQuery 1.12 and Beyond
https://blog.jquery.com/2014/04/02/browser-support-in-jquery-1-12-and-beyond/
https://en.wikipedia.org/wiki/List_of_web_browsers use 2015 start version
*/
if (
  (browser_name == "Chrome" && browser_version < 40) || 
  (browser_name == "Firefox" && browser_version < 35) || 
  (browser_name == "Safari" && browser_version < 10) || 
  (browser_name == "IE" && browser_version < 11) || 
  (browser_name == "Opera" && browser_version < 27) || 
  (browser_name == "Edge" && browser_version < 14)
) {
    console.log("browser : " + browser_name);
    console.log("version : " + browser_version);
    document.getElementById("upgrade_browser_div").style.display="block";
} else {
    document.getElementById("enrollment_main_div").style.display="block";
}
// if ($(window).width() >= 768) {
//     $('.bottom_btn_wrap ').scrollFix({
//         side: 'bottom'
//     });
//     $('#TopSummaryBar').scrollFix({
//         side: 'top'
//     });
// }
var exit_by_system = false;
$(window).bind('beforeunload', function(){
    if(exit_by_system === false) {
        return 'Are you sure you want leave?';
    }
});
</script>