<div class="bg_light_gray">
   <h4 class="p-15 mn">Take Home Pay Calculator</h4>
   <div class="row p-15">
      <div class="col-sm-4">
         <div class="panel gap_panel panel-default">
            <div class="panel-body theme-form">
               <form method="POST" id="home_pay_calculator">
                  <!-- PARENT FORM FIELD -->
                  <input type="hidden" name="zipcode" value="35071">
                  <?php foreach ($prdArr as $prdId => $prdData) { ?>
                     <?php foreach ($prdData as $key => $val) { ?>
                        <?php if ($key == 'annual_hrm_payment') { ?>
                           <?php foreach ($val as $hrmKey => $hrmVal) { ?>
                              <input type="hidden" name="product[<?= $prdId ?>][<?= $key ?>][<?= $hrmKey ?>]" value="<?= $hrmVal ?>">
                           <?php } ?>
                        <?php } else { ?>
                           <input type="hidden" name="product[<?= $prdId ?>][<?= $key ?>]" value="<?= $val ?>">
                        <?php } ?>
                     <?php } ?>
                  <?php } ?>
                  <!-- PARENT FORM FIELD -->
                  <p class="fw500">Payroll Type</p>
                  <div class="clearfix m-b-25">
                     <label class="radio-inline">
                        <input type="radio" class='gap_payroll_type' name="gap_payroll_type" value="Hourly">Hourly
                     </label>
                     <label class="radio-inline">
                        <input type="radio" class='gap_payroll_type' name="gap_payroll_type" value="Salary">Salary
                     </label>
                     <p class="error" id="error_gap_payroll_type"></p>
                  </div>
                  <div class="row" id="payroll_type_salary_div" style="display:none;"></div>
                  <div class="payroll_type_hourly_div" id="payroll_type_hourly_div" style="display:none;"></div>
                  <div class="payroll_type_hourly_div" style="display:none;">
                     <a href="javascript:void(0)" class="red-link pull-right" id="add_hourly_rate">+ Add Rate</a>
                  </div>
                  <p class="fw500">Marital Status</p>
                  <div class="clearfix m-b-25">
                     <label class="radio-inline">
                        <input type="radio" name="gap_marital_status" value="single">Single
                     </label>
                     <label class="radio-inline">
                        <input type="radio" name="gap_marital_status" value="married">Married
                     </label>
                     <p class="error" id="error_gap_marital_status"></p>
                  </div>
                  <p class="fw500">Pay Frequency</p>
                  <div class="form-group">
                     <select class="form-control" name="gap_pay_frequency" id="gap_pay_frequency">
                        <option value=""></option>
                        <!-- <option value="DAILY">Daily</option> -->
                        <option value="WEEKLY">Weekly</option>
                        <option value="BI_WEEKLY">Bi-Weekly</option>
                        <option value="SEMI_MONTHLY">Semi-Monthly</option>
                        <option value="MONTHLY">Monthly</option>
                        <!-- <option value="QUARTERLY">Quarterly</option>
                        <option value="SEMI_ANNUAL">Semi-Annually</option>
                        <option value="ANNUAL">Annually</option> -->
                     </select>
                     <label>Select</label>
                     <p class="error" id="error_gap_pay_frequency"></p>

                  </div>
                  <p class="fw500">Default Allowances</p>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <select class="form-control" name="gap_default_allowances_federal">
                              <?php
                              for ($i = 0; $i < 13; $i++) {
                                 echo "<option value='" . $i . "' " . ($i == 1 ? 'selected' : '') . ">" . $i . "</option>";
                              }
                              ?>
                           </select>
                           <label>Federal</label>
                           <p class="error" id="error_gap_default_allowances_federal"></p>
                        </div>
                     </div>
                     <!-- <div class="col-sm-4">
                        <div class="form-group">
                           <input type="tetx" class="form-control" name="">
                           <label>State</label>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <input type="tetx" class="form-control" name="">
                           <label>Local</label>
                        </div>
                     </div> -->
                  </div>
                  <div class="gap_tax_deduct">
                     <div class="gap_text_warp">
                        <div class="gap_text_head">
                           Pre-Tax Deductions
                           <a href="javascript:void(0);" id="pre_tax_deduct_click" class="pre_tax_deduct_click gap_add pull-right">+</a>
                           <div class="gap_text_body">
                              <div class="table-responsive">
                                 <input type="hidden" name="pre_tax_deductions" id="input_pre_tax_deductions">
                                 <table cellspacing="0" cellpadding="0" width="100%" border="0">
                                    <tbody id="pre_tax_deductions_tbody">
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="gap_text_warp">
                        <div class="gap_text_head">
                           Post-Tax Deductions
                           <a href="javascript:void(0);" id="post_tax_deduct_click" class="post_tax_deduct_click gap_add pull-right">+</a>
                           <div class="gap_text_body">
                              <div class="table-responsive">
                                 <input type="hidden" name="post_tax_deductions" id="input_post_tax_deductions">
                                 <table cellspacing="0" cellpadding="0" width="100%" border="0">
                                    <tbody id="post_tax_deductions_tbody">
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="m-t-30 text-center">
                     <a href="javascript:void(0);" class="btn btn-action" id="calculate_coverage">Calculate Coverage</a>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <div class="col-sm-8">
         <div class="panel panel-default">
            <div class="p-15 bg_dark_primary">
               <h4 class="mn text-white">Calculations</h4>
            </div>
            <div class="home-pay-calc">
               <div class="table-responsive">
                  <table class="table fs12 gap_table text-center">
                     <thead>
                        <tr>
                           <th></th>
                           <th class="font-bold">Without Triada</th>
                           <th class="bg-success text-white font-bold">With Triada</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td class="text-left font-bold">Your Gross Income</strong></td>
                           <td class="text-danger" id="without_triada_gross_income">$0.00</strong></td>
                           <td class="text-danger" id="with_triada_gross_income">$0.00</strong></td>
                        </tr>
                        <tr>
                           <td class="text-left">
                                    <a href="#estimated_taxes_pay" data-toggle="collapse" class="collapsed font-bold">
                                       Estimated Payroll Taxes <span class="caret"></span>
                                    </a>
                             </strong></td>
                           <td class="text-danger" id="without_estimated_payroll_taxes_total">$0.00</strong></td>
                           <td class="text-danger" id="with_estimated_payroll_taxes_total">$0.00</strong></td>
                        </tr>
                        <tr>
                           <td class="pn" colspan="3" style="border: none;">
                              <div id="estimated_taxes_pay" class="collapse estimated_taxes_div">
                                 <table width="100%" border="0">
                                    <tbody>
                                       <tr>
                                          <td class="text-left">Federal Taxes</strong></td>
                                          <td class="text-danger" id="without_gap_federal_taxes">$0.00</strong></td>
                                          <td class="text-danger" id="with_gap_federal_taxes">$0.00</strong></td>
                                       </tr>
                                       <tr>
                                          <td class="text-left">State Taxes</td>
                                          <td class="text-danger" id="without_gap_state_taxes">$0.00</td>
                                          <td class="text-danger" id="with_gap_state_taxes">$0.00</td>
                                       </tr>
                                       <tr>
                                          <td class="text-left">FICA</td>
                                          <td class="text-danger" id="without_gap_fica">$0.00</td>
                                          <td class="text-danger" id="with_gap_fica">$0.00</td>
                                       </tr>
                                       <tr>
                                          <td class="text-left">Medicare</td>
                                          <td class="text-danger" id="without_gap_medicare">$0.00</td>
                                          <td class="text-danger" id="with_gap_medicare">$0.00</td>
                                       </tr>
                                       <tr>
                                          <td class="text-left">Local Taxes</td>
                                          <td class="text-danger" id="without_gap_local_taxes">$0.00</td>
                                          <td class="text-danger" id="with_gap_local_taxes">$0.00</td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </td>
                        </tr>
                        <tr>
                           <td class="text-left">
                              <p class="font-bold">PreTax Deductions</p>
                              <span id="pre_tax_deductions_line_items_names" class="font-normal"></span>
                           </td>
                           <td>
                              <p>&nbsp;</p>
                              <span id="withoutgap_pre_tax_deductions_line_items_totals"></span>
                           </td>
                           <td>
                              <p>&nbsp;</p>
                              <span id="pre_tax_deductions_line_items_totals"></span>
                           </td>
                        </tr>
                        <tr>
                           <td class="text-left">
                              <p class="font-bold">PostTax Deductions</p>
                              <span id="post_tax_deductions_line_items_names"></span>
                           </td>
                           <td class="text-danger">
                              <p>&nbsp;</p>
                              <span id="withoutgap_post_tax_deductions_line_items_totals"></span>
                           </td>
                           <td class="text-danger">
                              <p>&nbsp;</p>
                              <span id="post_tax_deductions_line_items_totals"></span>
                           </td>
                        </tr>
                        <tr class="bg_light_success">
                           <td class="text-left font-bold">Claim Payment</td>
                           <td class="fs14" id="without_gap_claim_payment">$0.00</td>
                           <td class="text-success fs14" id="with_gap_claim_payment">$0.00</td>
                        </tr>
                        <tr>
                           <td class="text-left p-30 font-bold">Take Home</td>
                           <td class="fs14" id="without_gap_take_home">$0.00</td>
                           <td class="text-white fs14 bg-success gapTakeHomeAmt" id="with_gap_take_home">$0.00</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               
               <div class="text-right p-20">
                  <button class="btn btn-info" onclick="parent.$.colorbox.close(); return false;" data-toggle="tooltip" data-trigger="hover" title="Close" data-placement="bottom">Close</button>
               </div>
            </div>

         </div>
      </div>
   </div>
</div>

<!-- salary input template start -->
<div id="salary_input_div" style="display:none;">
   <div class="col-sm-12">
      <div class="form-group">
         <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
            <div class="pr">
               <input type="text" class="form-control" name="gap_payroll_type_salary~temp~" id="gap_payroll_type_salary~temp~">
               <label>Annual Salary</label>
            </div>
         </div>
         <p class="error~temp~" id="error_gap_payroll_type_salary~temp~"></p>
      </div>
   </div>
</div>
<!-- salary input template end -->

<!-- hourly rate template start-->
<div id="hourly_rates_template" style="display:none;">
   <div class="row hourly_rates_row~temp~" id="hourly_rates_row_~index~" data-index="~index~">
      <div class="col-md-6">
         <div class="form-group height_auto">
            <div class="input-group">
               <span class="input-group-addon"><i class="fa fa-usd"></i></span>
               <div class="pr">
                  <input type="text" class="form-control gap_payroll_type_hourly_wage" name="gap_payroll_type_hourly_wage[~index~]" id="gap_payroll_type_hourly_wage_~index~">
                  <label>Hourly Wage</label>
               </div>
            </div>
            <p class="error" id="error_gap_payroll_type_hourly_wage_~index~"></p>
         </div>
      </div>
      <div class="col-md-6">
         <div class="phone-control-wrap">
            <div class="phone-addon">
               <div class="form-group height_auto">
                  <input type="text" class="form-control gap_payroll_type_hours" name="gap_payroll_type_hours[~index~]" id="gap_payroll_type_hours_~index~">
                  <label>Hours</label>
                  <p class="error text-left" id="error_gap_payroll_type_hours_~index~"></p>
               </div>
            </div>
            <div class="phone-addon" id="remove_hourly_~index~" style="display:none;">
               <div class="form-group p-t-7">
                  <a href="javascript:void(0);" class="text-action remove_hourly_rate" data-index="~index~">X</a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- hourly rate template end-->

<!-- pre tax popup start -->
<div id="pre_tax_deduct_content" style="display: none">
   <div class="theme-form tax_deduct_table tbl-header">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
         <thead>
            <tr>
               <th>Deduction Name</th>
               <th>Calculation Method</th>
               <th>Deduction Amount</th>
               <th class="text-right fs24"><a class="text-black font-bold close_deduction_popover" href="javascript:void(0);" data-tax_type="pre_tax">X</a></th>
            </tr>
         </thead>
         <tbody>
            <tr class="pre_tax_deduction_row~temp~" style="display: none;" data-index="-1"></tr>
         </tbody>
      </table>
   </div>
   <div class="tax_deduct_table">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
         <tbody>
            <tr>
               <td colspan="3"><strong>Total</strong></td>
               <td class="text-right"><strong id="pre_tax_deductions_total~temp~"></strong></td>
            </tr>
            <tr>
               <td colspan="3">
                  <a href="javascript:void(0);" class="gap_add">+</a><a href="javascript:void(0);" class="btn primary-link add_deduction" data-tax_type="pre_tax">Add Deduction</a>
               </td>
               <td>
                  <a href="javascript:void(0);" data-tax_type="pre_tax" class="btn btn-action-o deductions_done">Done</a>
               </td>
            </tr>
         </tbody>
      </table>
   </div>
</div>

<table style="display: none">
   <tbody id="pre_tax_deduction_row_template">
      <tr class="pre_tax_deduction_row~temp~ pre_tax_deduction_row_~index~" data-index="~index~">
         <td>
            <div class="pr">
               <input type="text" class="form-control" name="pre_tax_deduction_name_[~index~]" id="pre_tax_deduction_name_~index~" maxlength="15">
               <label>Deduction Name</label>
               <p class="error" id="error_pre_tax_deduction_name_~index~"></p>
            </div>
         </td>
         <td>
            <div class="pr">
               <select name="pre_tax_deduction_method[~index~]" id="pre_tax_deduction_method_~index~" class="deduction_method_select" data-tax_type="pre_tax" data-index="~index~" data-none-selected-text="" data-dropup-auto="false">
                  <option value=""></option>
                  <option value="fixed_amount">$ Fixed Amount</option>
                  <option value="gross_pay">% Gross Pay</option>
               </select>
               <label>Method</label>
               <p class="error" id="error_pre_tax_deduction_method_~index~"></p>
            </div>
         </td>
         <td>
            <div class="pr">
               <input type="text" class="form-control deduction_amount_input" name="pre_tax_deduction_amount[~index~]" id="pre_tax_deduction_amount_~index~" data-tax_type="pre_tax" data-index="~index~">
               <p class="error" id="error_pre_tax_deduction_amount_~index~"></p>
            </div>
         </td>
         <td class="text-right"><strong class="row_total_amount" id="pre_tax_deduction_row_total_~index~">$0.00</strong> <a href="javascript:void(0);" class="deduction_remove_row" data-tax_type="pre_tax" data-index="~index~">X</a></td>
      </tr>
   </tbody>
</table>
<!-- pre tax popup end -->

<!-- post tax popup start -->
<div id="post_tax_deduct_content" style="display: none">
   <div class="theme-form tax_deduct_table tbl-header">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
         <thead>
            <tr>
               <th>Deduction Name</th>
               <th>Calculation Method</th>
               <th>Deduction Amount</th>
               <th class="text-right fs24"><a class="text-black font-bold close_deduction_popover" href="javascript:void(0);" data-tax_type="post_tax">X</a></th>
            </tr>
         </thead>
         <tbody>
            <tr class="post_tax_deduction_row~temp~" style="display: none;" data-index="-1">
            </tr>
         </tbody>
      </table>
   </div>
   <div class="tax_deduct_table">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
         <tbody>
            <tr>
               <td colspan="3"><strong>Total</strong></td>
               <td class="text-right"><strong id="post_tax_deductions_total~temp~"></strong></td>
            </tr>
            <tr>
               <td colspan="3">
                  <a href="javascript:void(0);" class="gap_add">+</a><a href="javascript:void(0);" class="btn primary-link add_deduction" data-tax_type="post_tax">Add Deduction</a>
               </td>
               <td><a href="javascript:void(0);" data-tax_type="post_tax" class="btn btn-action-o deductions_done">Done</a></td>
            </tr>
         </tbody>
      </table>
   </div>
</div>

<table style="display: none">
   <tbody id="post_tax_deduction_row_template">
      <tr class="post_tax_deduction_row~temp~ post_tax_deduction_row_~index~" data-index="~index~">
         <td>
            <div class="pr">
               <input type="text" class="form-control" name="post_tax_deduction_name_[~index~]" id="post_tax_deduction_name_~index~" maxlength="15">
               <label>Deduction Name</label>
               <p class="error" id="error_post_tax_deduction_name_~index~"></p>
            </div>
         </td>
         <td>
            <div class="pr">
               <select name="post_tax_deduction_method[~index~]" id="post_tax_deduction_method_~index~" class="deduction_method_select" data-tax_type="post_tax" data-index="~index~" data-none-selected-text="" data-dropup-auto="false">
                  <option value=""></option>
                  <option value="fixed_amount">$ Fixed Amount</option>
                  <option value="gross_pay">% Gross Pay</option>
               </select>
               <label>Method</label>
               <p class="error" id="error_post_tax_deduction_method_~index~"></p>
            </div>
         </td>
         <td>
            <div class="pr">
               <input type="text" class="form-control deduction_amount_input" name="post_tax_deduction_amount[~index~]" id="post_tax_deduction_amount_~index~" data-tax_type="post_tax" data-index="~index~">
               <p class="error" id="error_post_tax_deduction_amount_~index~"></p>
            </div>
         </td>
         <td class="text-right"><strong class="row_total_amount" id="post_tax_deduction_row_total_~index~">$0.00</strong> <a href="javascript:void(0);" class="deduction_remove_row" data-tax_type="post_tax" data-index="~index~">X</a></td>
      </tr>
   </tbody>
</table>
<!-- post tax popup end -->

<!-- tax table extra -->
<table style="display:none;">
   <tbody id="productRow">
      <tr>
         <td class="text-right">~prductName~</td>
         <td class="text-danger" id="~withoutProductNameId~">~withoutProductValue~</td>
         <td class="text-danger" id="~withProductNameId~">~withProductValue~</td>
      </tr>
   </tbody>
</table>
<!-- tax table extra -->

<script type="text/javascript">
   var pre_tax_deductions = [];
   var post_tax_deductions = [];
   $hourDivCnt = 0;

   /* TO DISPLAY INPUT ACCORDING TO PAYROLL TYPE */
   $(document).off("change", ".gap_payroll_type");
   $(document).on("change", ".gap_payroll_type", function() {
      $val = $(this).val();
      if ($val == "Hourly") {
         $('#payroll_type_salary_div').html("");
         addHourlyDiv($hourDivCnt);
         $('.payroll_type_hourly_div').show();
         $('#payroll_type_salary_div').hide();
      }
      if ($val == "Salary") {
         $('#payroll_type_hourly_div').html("");
         $hourDivCnt = 0;
         addSalaryDiv();
         $('#payroll_type_salary_div').show();
         $('.payroll_type_hourly_div').hide();
         $('#gap_payroll_type_salary').priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: '',
            limit: false,
            centsLimit: 2,
         });
      }
   });

   /* TO ADD ADDITIONAL INPUT FOR HOURLY PAYROLL TYPE */
   $(document).off("click", "#add_hourly_rate");
   $(document).on("click", "#add_hourly_rate", function(e) {
      e.preventDefault();
      if ($hourDivCnt > 0) {
         var $is_error = false;
         var $row_index1 = $hourDivCnt - 1;

         $('#payroll_type_hourly_div .error').html('');

         $.each($('#payroll_type_hourly_div .gap_payroll_type_hourly_wage'),function(){
            var hourly_wage_id = $(this).attr('id');
            var hourly_wage_value = $(this).val();
            if(hourly_wage_value == "" || hourly_wage_value == "0.00"){
               $("#error_"+hourly_wage_id).html('Please enter Hourly Wage');
               $is_error = true;
            }
         });

         $.each($('#payroll_type_hourly_div .gap_payroll_type_hours'),function(){
            var hourly_id = $(this).attr('id');
            var hourly_value = $(this).val();
            if(hourly_value == "" || hourly_value == "0"){
               $("#error_"+hourly_id).html('Please enter Hours');
               $is_error = true;
            }
            
         });

         if ($is_error) {
            return false;
         }
      }
      addHourlyDiv($hourDivCnt);
   });

   /* TO REMOVE ADDITIONAL INPUT FOR HOURLY PAYROLL TYPE */
   $(document).off("click", ".remove_hourly_rate");
   $(document).on("click", ".remove_hourly_rate", function(e) {
      e.preventDefault();
      $row_index = $(this).data('index');
      $("#hourly_rates_row_" + $row_index).remove();

   });

   addSalaryDiv = function() {
      $html = $('#salary_input_div').html();
      $html = $html.replace(/~temp~/g, '');
      $('#payroll_type_salary_div').html($html);

      $('#gap_payroll_type_salary').priceFormat({
         prefix: '',
         suffix: '',
         centsSeparator: '.',
         thousandsSeparator: ',',
         limit: false,
         centsLimit: 0,
      });
   }

   addHourlyDiv = function($index) {
      $row_html = $("#hourly_rates_template").html();
      $row_html = $row_html.replace(/~index~/g, $index);
      $row_html = $row_html.replace(/~temp~/g, '');
      $("#payroll_type_hourly_div").append($row_html);
      if ($hourDivCnt > 0) {
         $('#remove_hourly_' + $index).show();
      }
      $('#gap_payroll_type_hourly_wage_' + $index).priceFormat({
         prefix: '',
         suffix: '',
         centsSeparator: '.',
         thousandsSeparator: ',',
         limit: false,
         centsLimit: 2,
      });
      $('#gap_payroll_type_hours_' + $index).priceFormat({
         prefix: '',
         suffix: '',
         centsSeparator: '.',
         thousandsSeparator: ',',
         limit: 2,
         centsLimit: 0,
      });
      $hourDivCnt++;
   }

   $('#pre_tax_deduct_click').popover({
      html: true,
      container: 'body',
      trigger: 'click',
      title: '&nbsp;',
      template: '<div class="popover gaptax_deduct_popover"><div class="gaptax_popover_head"></div><div class="popover-content"></div></div>',
      placement: 'left',
      content: function() {
         $html =  $('#pre_tax_deduct_content').html();
         $html =  $html.replace(/~temp~/g,'');
         return $html;
      }
   }).on('shown.bs.popover', function() {
      if (pre_tax_deductions.length > 0 && pre_tax_deductions['1'].length > 0) {
         var deduction_data = pre_tax_deductions['1'];
         var is_row_added = false;
         $.each(deduction_data, function($k, $v) {
            if (typeof($v) !== "undefined") {
               add_deduction_row($k, 'pre_tax', $v);
               is_row_added = true;
            }
         });
         if (is_row_added == false) {
            add_deduction_row(0, 'pre_tax');
         } else {
            calculate_tax_deduction_total('pre_tax');
         }
      } else {
         add_deduction_row(0, 'pre_tax');
      }
   });

   $('#post_tax_deduct_click').popover({
      html: true,
      container: 'body',
      trigger: 'click',
      title: '&nbsp;',
      template: '<div class="popover gaptax_deduct_popover"><div class="gaptax_popover_head"></div><div class="popover-content"></div></div>',
      placement: 'left',
      content: function() {
         $html =  $('#post_tax_deduct_content').html();
         $html =  $html.replace(/~temp~/g,'');
         return $html;
      }
   }).on('shown.bs.popover', function() {
      if (post_tax_deductions.length > 0 && post_tax_deductions['2'].length > 0) {
         var deduction_data = post_tax_deductions['2'];
         var is_row_added = false;
         $.each(deduction_data, function($k, $v) {
            if (typeof($v) !== "undefined") {
               add_deduction_row($k, 'post_tax', $v);
               is_row_added = true;
            }
         });
         if (is_row_added == false) {
            add_deduction_row(0, 'post_tax');
         } else {
            calculate_tax_deduction_total('post_tax');
         }
      } else {
         add_deduction_row(0, 'post_tax');
      }
   });
   
   $(document).off("click", "#post_tax_deduct_click");
   $("#post_tax_deduct_click").on('shown.bs.popover', function(){
       $('.gaptax_deduct_popover .tbl-header').slimScroll({
         height: '100%',
         width: '100%',
         alwaysVisible: true
      });
      document.querySelector('.gaptax_deduct_popover .tbl-header').addEventListener('scroll', function () {
         var scrollTop = this.scrollTop;
         this.querySelector('thead').style.transform = 'translateY(' + scrollTop + 'px)';
      });
   });
   $(document).off("click", "#pre_tax_deduct_click");
   $("#pre_tax_deduct_click").on('shown.bs.popover', function(){
       $('.gaptax_deduct_popover .tbl-header').slimScroll({
         height: '100%',
         width: '100%',
         alwaysVisible: true
      });
      document.querySelector('.gaptax_deduct_popover .tbl-header').addEventListener('scroll', function () {
         var scrollTop = this.scrollTop;
         this.querySelector('thead').style.transform = 'translateY(' + scrollTop + 'px)';
      });
   });

   /* TO REMOVE TAX ROW FROM FORM */
   $(document).off("click", ".deduction_remove_row2");
   $(document).on("click", ".deduction_remove_row2", function(e) {
      e.preventDefault();
      var $row_index = $(this).data('index');
      var $tax_type = $(this).data('tax_type');
      if ($tax_type == "pre_tax") {
         var deduction_data = pre_tax_deductions['1'];
         var deduction_data2 = [];
         $.each(deduction_data, function($k, $v) {
            if ($row_index != $k) {
               deduction_data2[$k] = $v;
            }
         });
         pre_tax_deductions['1'] = deduction_data2;
         $("#pre_tax_deduction_row2_" + $row_index).remove();
         if (deduction_data2.length > 0) {
            $("#input_pre_tax_deductions").val(JSON.stringify(deduction_data2));
         } else {
            $("#input_pre_tax_deductions").val('');
         }
      }
      if ($tax_type == "post_tax") {
         var deduction_data = post_tax_deductions['2'];
         var deduction_data2 = [];
         $.each(deduction_data, function($k, $v) {
            if ($row_index != $k) {
               deduction_data2[$k] = $v;
            }
         });
         post_tax_deductions['2'] = deduction_data2;
         $("#post_tax_deduction_row2_" + $row_index).remove();
         if (deduction_data2.length > 0) {
            $("#input_post_tax_deductions").val(JSON.stringify(deduction_data2));
         } else {
            $("#input_post_tax_deductions").val('');
         }
      }
   });

   /* TAX POPUP CODE START */
   /* TO CLOSE DEDUCTION POPOVER START */
   $(document).off('click', '.close_deduction_popover');
   $(document).on('click', '.close_deduction_popover', function(e) {
      e.preventDefault();
      var $tax_type = $(this).data('tax_type');
      $("." + $tax_type + "_deduct_click").trigger('click');
   });

   /* TO REMOVE DEDUCTION ROW START */
   $(document).off("click", ".deduction_remove_row");
   $(document).on("click", ".deduction_remove_row", function(e) {
      e.preventDefault();
      var $row_index = $(this).data('index');
      var $tax_type = $(this).data('tax_type');
      $("." + $tax_type + "_deduction_row_" + $row_index).remove();
      calculate_tax_deduction_total($tax_type);
   });

   /* TO ADD ADDITION TAX ROW*/
   $(document).off("click", ".add_deduction");
   $(document).on("click", ".add_deduction", function(e) {
      e.preventDefault();
      $('.tax_deduct_table .error').html('');
      var $tax_type = $(this).data('tax_type');
      var $row_index = 1 + $("tr." + $tax_type + "_deduction_row:last").data("index");
      if ($row_index > 0) {
         var $is_error = false;
         var $row_index1 = $row_index - 1;
         if ($("#" + $tax_type + "_deduction_name_" + $row_index1).val() == "") {
            $("#error_" + $tax_type + "_deduction_name_" + $row_index1).html('Deduction Name is required');
            $is_error = true;
         }
         if ($("#" + $tax_type + "_deduction_method_" + $row_index1).val() == "") {
            $("#error_" + $tax_type + "_deduction_method_" + $row_index1).html('Method is required');
            $is_error = true;
         }
         if ($("#" + $tax_type + "_deduction_amount_" + $row_index1).val() == "" || $("#" + $tax_type + "_deduction_amount_" + $row_index1).val() == 0) {
            $("#error_" + $tax_type + "_deduction_amount_" + $row_index1).html('Amount is required');
            $is_error = true;
         }
         if ($is_error) {
            return false;
         }
      }
      add_deduction_row($row_index, $tax_type);
   });

   /* ON SELECT THE TAX DEDUCTION METHOD */
   $(document).off("change", ".deduction_method_select");
   $(document).on("change", ".deduction_method_select", function() {
      var $tax_type = $(this).data('tax_type');
      var $row_index = $(this).data('index');
      var $deduction_method = $(this).val();
      $("#" + $tax_type + "_deduction_amount_" + $row_index).unpriceFormat();
      if ($deduction_method == "fixed_amount") {
         $("#" + $tax_type + "_deduction_amount_" + $row_index).priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
         });
      }
      if ($deduction_method == "gross_pay") {
         $("#" + $tax_type + "_deduction_amount_" + $row_index).priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: 4,
            centsLimit: 2,
         });
      }
   });

   /* ON INPUT OF TAX AMOUNT/PERCENTAGE */
   $(document).off("keyup", ".deduction_amount_input");
   $(document).on("keyup", ".deduction_amount_input", function(e) {
      var $row_index = $(this).data('index');
      var $tax_type = $(this).data('tax_type');
      calculate_tax_deduction_row($tax_type, $row_index);
   });

   /* POPUP FROM DONE */
   $(document).off("click", ".deductions_done");
   $(document).on("click", ".deductions_done", function(e) {
      e.preventDefault();
      var $tax_type = $(this).data('tax_type');
      var $row_index1 = $("tr." + $tax_type + "_deduction_row:last").data("index");
      $('.deduct_table .error').html('');
      if ($row_index1 >= 0) {
         var $is_error = false;
         if ($("#" + $tax_type + "_deduction_name_" + $row_index1).val() == "") {
            $("#error_" + $tax_type + "_deduction_name_" + $row_index1).html('Deduction Name is required');
            $is_error = true;
         }
         if ($("#" + $tax_type + "_deduction_method_" + $row_index1).val() == "") {
            $("#error_" + $tax_type + "_deduction_method_" + $row_index1).html('Method is required');
            $is_error = true;
         }
         if ($("#" + $tax_type + "_deduction_amount_" + $row_index1).val() == "" || $("#" + $tax_type + "_deduction_amount_" + $row_index1).val() == 0) {
            $("#error_" + $tax_type + "_deduction_amount_" + $row_index1).html('Amount is required');
            $is_error = true;
         }
         if ($is_error) {
            return false;
         }
      }

      display_tax_deduction($tax_type);
      $("." + $tax_type + "_deduct_click").trigger('click');
   });

   /* FUNCTION TO ADD ADDITIONAL ROW IN TAX POPUP */
   add_deduction_row = function($row_index, $tax_type, $row_data) {
      var $row_html = $("#" + $tax_type + "_deduction_row_template").html();
      $row_html = $row_html.replace(/~index~/g, $row_index);
      $row_html = $row_html.replace(/~temp~/g, '');
      $("tr." + $tax_type + "_deduction_row:last").after($row_html);

      if (typeof($row_data) !== "undefined") {
         $("#" + $tax_type + "_deduction_name_" + $row_index).val($row_data.deduction_name);
         $("#" + $tax_type + "_deduction_method_" + $row_index).val($row_data.deduction_method);
         $("#" + $tax_type + "_deduction_amount_" + $row_index).val($row_data.deduction_amount);
         $("#" + $tax_type + "_deduction_row_total_" + $row_index).html($row_data.deduction_row_total);

         $("#" + $tax_type + "_deduction_name_" + $row_index).addClass('has-value');
         $("#" + $tax_type + "_deduction_method_" + $row_index).addClass('has-value');
         $("#" + $tax_type + "_deduction_amount_" + $row_index).unpriceFormat();
         $("#" + $tax_type + "_deduction_amount_" + $row_index).priceFormat({
            prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: false,
            centsLimit: 2,
         });
      }

      $("#" + $tax_type + "_deduction_method_" + $row_index).addClass('form-control');
      $("#" + $tax_type + "_deduction_method_" + $row_index).selectpicker('setStyle', 'btn-select');
   }

   /* FUNCTION TO CALCULATE TOTAL DEDUCTION IN TAX POPUP */
   calculate_tax_deduction_total = function($tax_type) {
      $total_deduction = 0.0;
      $("." + $tax_type + "_deduction_row").each(function(index, val) {
         if ($(this).data('index') >= 0) {
            var row_total_amount = $(this).find('.row_total_amount').html();
            row_total_amount = parseFloat(row_total_amount.replace('$', ''));
            $total_deduction += row_total_amount;
         }
      });
      $("#" + $tax_type + "_deductions_total").html('$' + parseFloat($total_deduction).toFixed(2));
   }

   /* FUNCTION TO CALCULATE TAX ROW TOTAL IN TAX POPUP */
   calculate_tax_deduction_row = function($tax_type, $row_index, $is_from_arr) {
      if (typeof($is_from_arr) !== "undefined") {
         if ($tax_type == "pre_tax") {
            var $amount = pre_tax_deductions[$row_index]['deduction_amount'];
            var $deduction_method = pre_tax_deductions[$row_index]['deduction_method'];
         } else {
            var $amount = post_tax_deductions[$row_index]['deduction_amount'];
            var $deduction_method = post_tax_deductions[$row_index]['deduction_method'];
         }
      } else {
         var $amount = parseF($("#" + $tax_type + "_deduction_amount_" + $row_index).val());
         var $deduction_method = $("#" + $tax_type + "_deduction_method_" + $row_index).val();
      }

      var $gap_payroll_type = $("input[name='gap_payroll_type']:checked").val();
      var $gross_income = 0;
      var $row_total = 0;
      if ($gap_payroll_type == "Hourly") {
         $('input[id^="gap_payroll_type_hourly_wage"]').each(function(ind, ele) {
            var $tmp_ind = $(this).data('index');

            var $hourly_wage = parseFloat($("#gap_payroll_type_hourly_wage_" + $tmp_ind).val());
            var $hours = parseFloat($("#gap_payroll_type_hours_" + $tmp_ind).val());
            $gross_income += $hourly_wage * $hours;
         });
      } else if ($gap_payroll_type == "Salary") {
         $gross_income_with_comma = $("#gap_payroll_type_salary").val().replace('$', '');
         $gross_income = parseFloat($gross_income_with_comma.replace(',', ''));
         var pay_frequency = $('#gap_pay_frequency').val()
         if (pay_frequency == "DAILY") {
            $gross_income = $gross_income / 260;
         } else if (pay_frequency == "WEEKLY") {
            $gross_income = $gross_income / 52;
         } else if (pay_frequency == "BI_WEEKLY") {
            $gross_income = $gross_income / 26;
         } else if (pay_frequency == "SEMI_MONTHLY") {
            $gross_income = $gross_income / 24;
         } else if (pay_frequency == "MONTHLY") {
            $gross_income = $gross_income / 12;
         } else if (pay_frequency == "QUARTERLY") {
            $gross_income = $gross_income / 4;
         } else if (pay_frequency == "SEMI_ANNUAL") {
            $gross_income = $gross_income / 2;
         } else if (pay_frequency == "ANNUAL") {
            $gross_income = $gross_income / 1;
         }
         $gross_income = parseFloat($gross_income);
      }

      if ($deduction_method == "fixed_amount") {
         $row_total = $amount;
      } else if ($deduction_method == "gross_pay") {
         $row_total = parseFloat(($gross_income * $amount) / 100);
      }
      $("#" + $tax_type + "_deduction_row_total_" + $row_index).html('$' + parseF($row_total));
      if (typeof($is_from_arr) !== "undefined") {
         if ($tax_type == "pre_tax") {
            pre_tax_deductions[$row_index]['deduction_row_total'] = '$' + parseFloat($row_total);
         } else {
            post_tax_deductions[$row_index]['deduction_row_total'] = '$' + parseFloat($row_total);
         }
      }
      calculate_tax_deduction_total($tax_type);
   }

   /* FUNCTION FOR AFTER TAX POPUP SUBMIT DISPLAY VALUE IN FRONT FROM */
   display_tax_deduction = function($tax_type) {
      var deduction_data = [];
      var deduction_html = "";
      var cnt = 0;
      $("." + $tax_type + "_deduction_row").each(function(index, ele) {
         var $row_index = $(this).data('index');
         if ($row_index >= 0) {
            var deduction_name = $("#" + $tax_type + "_deduction_name_" + $row_index).val();
            var deduction_method = $("#" + $tax_type + "_deduction_method_" + $row_index).val();
            var deduction_amount = $("#" + $tax_type + "_deduction_amount_" + $row_index).val();
            var deduction_row_total = $("#" + $tax_type + "_deduction_row_total_" + $row_index).html();

            deduction_html += "<tr id='" + $tax_type + "_deduction_row2_" + cnt + "'>";
            deduction_html += "<td><strong>" + deduction_name + "</strong></td>";
            deduction_html += "<td>" + (deduction_method == "fixed_amount" ? "Fixed Amount" : "Gross Pay") + "</td>";
            deduction_html += "<td id='" + $tax_type + "_deduction_total_row_" + $row_index + "'>" + deduction_row_total + "</td>";
            deduction_html += "<td><a href='javascript:void(0);' class='text-action deduction_remove_row2'  data-tax_type='" + $tax_type + "' data-index='" + cnt + "'>X</a></td>";
            deduction_html += "</tr>";
            deduction_data[cnt] = {
               'deduction_name': deduction_name,
               'deduction_method': deduction_method,
               'deduction_amount': deduction_amount,
               'deduction_row_total': deduction_row_total,
            };
            cnt++;
         }
      });
      if ($tax_type == "pre_tax") {
         pre_tax_deductions['1'] = deduction_data;
      }
      if ($tax_type == "post_tax") {
         post_tax_deductions['2'] = deduction_data;
      }

      $("#" + $tax_type + "_deductions_tbody").html(deduction_html);
      if (deduction_data.length > 0) {
         $("#input_" + $tax_type + "_deductions").val(JSON.stringify(deduction_data));
      } else {
         $("#input_" + $tax_type + "_deductions").val('');
      }
   }
   /* TAX POPUP CODE END */

   /* FINAL FROM SUBMIT */
   $(document).off('click', '#calculate_coverage');
   $(document).on('click', '#calculate_coverage', function(e) {
      e.preventDefault();
      $('.gap_panel p.error').html("");
      var param = $('#home_pay_calculator').serialize();
      // var parentParam = parent.$('#frmGroupMemberEnrollment').serialize();
      // var param = param + '&' + parentParam + '&api_key=calculateTakeHomePay';
      var param = param + '&api_key=calculateTakeHomePay';
      $.ajax({
         // url: '<?= $HOST ?>/data.json',
         url: '<?= $HOST ?>/ajax_api_call.php',
         dataType: 'JSON',
         data: param,
         type: 'POST',
         success: function(res) {
            if (res.status == 'Success') {
               if (typeof(res.data) !== 'undefined') {
                  if (typeof(res.data['calculation_data']) !== 'undefined') {
                     $calculation_data = res.data['calculation_data'];
                     $.each($calculation_data, function(ind, val) {
                        if (ind == 'with_gap_take_home') {
                           parent.$('#with_gap_take_home').html(val);
                           $("#with_gap_take_home").html(val);
                        } else if (ind == 'pre_tax_deductions_line_items_names') {
                           $("#pre_tax_deductions_line_items_names").html(val);
                        } else if (ind == 'pre_tax_deductions_line_items_totals') {
                           $("#pre_tax_deductions_line_items_totals").html(val);
                        } else if (ind == 'post_tax_deductions_line_items_names') {
                           $("#post_tax_deductions_line_items_names").html(val);
                        } else if (ind == 'post_tax_deductions_line_items_totals') {
                           $("#post_tax_deductions_line_items_totals").html(val);
                        } else {
                           $("#" + ind + "").html(val);
                        }
                     });
                  }
                  if (typeof(res.data['curl_response']) !== 'undefined') {
                     if (typeof(res.data['curl_response']['errors']) !== 'undefined') {
                        $tmpErrors = "";
                        $.each(res.data['curl_response']['errors'], function(ind, val) {
                           $tmpErrors += val.message;
                        });
                        $('.gap_input_errors').html($tmpErrors);
                     }
                  }
               }
            } else {
               if (typeof(res.data) !== 'undefined') {
                  $.each(res.data, function(ind, val) {
                     $('#error_' + ind).html(val);
                  });
               }
            }
         }
      })
   });

   function parseF(amount) {
      if(typeof(amount) !== 'undefined'){
         amount = amount.toString().replace(/[^0-9.]/g,"");
         amount = parseFloat(amount);
         if(isNaN(amount)) {
             amount = 0;
         }
         return amount.toFixed(2);
      } else {
         return 0.00;
      }
   }
</script>