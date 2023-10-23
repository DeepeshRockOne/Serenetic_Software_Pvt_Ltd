<h2 class="m-t-0 m-b-15">Summary</h2>
<p class="m-b-25">Summary of your selected plans and waived categories.</p>
<div id="enrollment_summary_details_div"></div>
<div id="waived_coverage_section" style="display:none">
   <h4 class="m-t-0 m-b-25">Waived Coverage</h4>
   <div class="table-responsive m-b-30">
      <table class="<?=$table_class?> table-black">
         <thead>
            <tr>
               <th>Categories</th>
               <th>Reason</th>
               <th width="200px">Actions</th>
            </tr>
         </thead>
         <tbody id="waived_coverage_tbody"></tbody>
      </table>
   </div>
</div>
<div class="bundle-invoice m-b-30">
   <div class="row">
      <div class="col-sm-12" id="plan_breakdown_home_pay_div" style="display: none;">
         <div class="plan-breakdown">
            <h4 class="m-t-0 m-b-15 text-center">Take Home Pay Breakdown</h4>
            <p class="text-center">Breakdown of tax benefits.</p>
            <div class="table-responsive br-n">
               <table cellspacing="0" cellspacing="0" width="100%" border="0" >
                  <tbody>
                     <tr>
                        <td class="font-bold">Your Gross Income</td>
                        <td class="text-right" id="grp_take_home_gross_income">$0.00</td>
                     </tr>
                     <tr>
                        <td class="font-bold">
                           <a href="#estimated_taxes" data-toggle="collapse" class="collapsed">Estimated Payroll Taxes <span class="caret"></span>
                           </a>
                        </td>
                        <td class="text-right text-danger" id="grp_take_home_payroll_taxes">$0.00</td>
                     </tr>
                     <tr>
                        <td class="pn" colspan="2" style="border: none;">
                              <div id="estimated_taxes" class="collapse estimated_taxes_div">
                                 <table width="100%" border="0">
                                    <tbody>
                                       <tr>
                                          <td class="p-l-15"><p class="p-l-30">Federal Taxes</p></td>
                                          <td class="text-right text-danger" id="grp_with_gap_federal_taxes">$0.00</td>
                                       </tr>
                                       <tr>
                                          <td class="p-l-15"><p class="p-l-30">State Taxes</p></td>
                                          <td class="text-right text-danger" id="grp_with_gap_state_taxes">$0.00</td>
                                       </tr>
                                       <tr>
                                          <td class="p-l-15"><p class="p-l-30">FICA</p></td>
                                          <td class="text-right text-danger" id="grp_with_gap_fica">$0.00</td>
                                       </tr>
                                       <tr>
                                          <td class="p-l-15"><p class="p-l-30">Medicare</p></td>
                                          <td class="text-right text-danger" id="grp_with_gap_medicare">$0.00</td>
                                       </tr>
                                       <tr>
                                          <td class="p-l-15"><p class="p-l-30">Local Taxes</p></td>
                                          <td class="text-right text-danger" id="grp_with_gap_local_taxes">$0.00</td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </td>
                     </tr>
                     <tr>
                        <td class="font-bold" colspan="2">PreTax Deductions</td>
                     </tr>
                     <tr>
                        <td class="p-l-15" id="payment_pre_tax_deductions_line_items_names"></td>
                        <td class="text-right" id="payment_pre_tax_deductions_line_items_totals"></td>
                     </tr>
                     <tr>
                        <td class="font-bold" colspan="2">PostTax Deductions</td>
                     </tr>
                     <tr>
                        <td class="p-l-15" id="payment_post_tax_deductions_line_items_names"></td>
                        <td class="text-right" id="payment_post_tax_deductions_line_items_totals"></td>
                     </tr>
                     <tr>
                        <td class="text-success font-bold">Claim Payment</td>
                        <td class="text-success text-right" id="claim_payment"></td>
                     </tr>
                  </tbody>
                  <tfoot>
                     <tr>
                        <td >Your Take Home</td>
                        <td class="text-success text-right" id="payment_with_gap_take_home">$0.00</td>
                     </tr>
                  </tfoot>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>