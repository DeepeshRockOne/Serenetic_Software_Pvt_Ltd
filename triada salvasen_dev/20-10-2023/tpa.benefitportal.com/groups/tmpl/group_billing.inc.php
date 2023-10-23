<style type="text/css">
   .popover-content{padding: 0px}
   .popover-set{padding: 5px 15px}
</style>
<div class="container">
   <div class="section-padding ">
      <div class="panel panel-default panel-block panel-title-block">
         <div class="panel-heading br-b">
            <div class="clearfix tbl_filter">
               <div class="pull-left">
                  <h4 class="fw600 m-t-7  m-b-15">Payments</h4>
               </div>
               <div class="pull-right">
                  <?php if(!empty($checkPaymentButtonDisplay)){ ?>
                     <a href="group_manage_payment_popup.php" class="btn btn-default group_manage_payment_popup">Manage Payment</a> 
                  <?php } ?>
               </div>
            </div>
         </div>
         <div class="panel-body">
            <div class="row ">
               <div class="col-sm-5">
                  <?php if($invoice_broken_locations == "Y"){ ?>
                  <div class="theme-form">
                     <table width="100%">
                        <tbody>
                           <tr>
                              <td class="fw500">
                                 <div class="form-group">
                                       <select class="form-control" name="company_id" id="company_id" onchange="load_payment_data();">
                                          <option data-hidden="true"></option>
                                          <?php if(!empty($group_company_res)) { ?>
                                             <?php foreach ($group_company_res as $key => $gc_row) { ?>
                                                   <option value="<?= $gc_row['id'] ?>" <?= $selected_company_id == $gc_row['id'] ? 'selected' : '' ?>><?= $gc_row['name'] ?></option>     
                                             <?php } ?>
                                          <?php } ?>
                                       </select>
                                       <label>Company/Location</label>
                                 </div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <?php } else { ?>
                     <input type="hidden" name="company_id" id="company_id" value="<?=$selected_company_id?>">
                  <?php } ?>

                  <div class="payment_panel bg_light_gray m-b-15">
                     <table width="100%">
                        <tbody>
                           <tr>
                              <td class="fw500">Previous Balance</td>
                              <td class="fw500 text-right">
                                 <h2 class="mn fw400 prev_balance">$0.00</h2>
                              </td>
                           </tr>
                           <tr>
                              <td class="fw500">Payment Recieved</td>
                              <td class="fw500 text-right"><span class="prev_payment_date"></span> <span class="text-action prev_received_amount">($0.00)</span></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="payment_panel bg_light_gray m-b-15">
                     <table width="100%">
                        <tbody>
                           <tr>
                              <td class="fw500">Balance Forward</td>
                              <td class="fw500 text-right balance_forward">$0.00</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="col-sm-4 col-sm-offset-3">
                  <div class="payment_panel bg-success m-b-15">
                     <table width="100%" class="text-white">
                        <tbody>
                           <tr>
                              <td class="fw500">
                                 Current Payment
                              </td>
                              <td class="fw500 text-right current_amount">$0.00</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="payment_panel bg_light_gray m-b-15">
                     <table width="100%">
                        <tbody>
                           <tr>
                              <td class="fw500">Due Date</td>
                              <td class="fw500 text-right due_date">-</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="table-responsive m-t-15 auto_draft_section" style="display: none;">
                     <table>
                        <tr>
                           <td>
                              <p>Payment Method: </p>
                           </td>
                           <td class="text-action">
                              <p class="fw500">&nbsp;&nbsp;<span class="auto_draft_payment_method"></span></p>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" class="fw500">
                              Next payment set for: <span class="auto_draft_date">-</span>
                           </td>
                        </tr>
                     </table>
                  </div>
                  <!-- pay now button removed task - EL8-1095 -->
                  <!-- <div class="text-right m-t-15 pay_bill_section" style="display: none;">  
                           <a href="<?=$HOST?>/pay_bill.php?location=group" class="btn btn-action pay_bill">Pay Now</a>
                  </div> -->
               </div>
            </div>
         </div>
      </div>
      <div class="panel panel-default panel-block">
         <div class="panel-body">
            <div class="clearfix m-b-15 tbl_filter">
               <div class="pull-left">
                  <h4 class="mn">List Bill Invoice</h4>
               </div>
            </div>
            <div class="table-responsive">
               <table class="<?=$table_class?>">
                  <thead>
                     <tr>
                        <th class="text-left">Added Date</th>
                        <th>List Bill #</th>
                        <th>Group Name</th>
                        <th class="text-center">Company Name</th>
                        <th class="text-center">Class Name</th>
                        <th>Status</th>
                        <th class="text-right">Balance</th>
                        <th class="text-center">Amendments</th>
                        <th width="90px">Actions</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if(!empty($invoiceRes)){?>
                        <?php foreach ($invoiceRes as $key => $invoice) { ?>
                           <tr>
                              <td><?= !empty($invoice['created_at']) ? date($DATE_FORMAT,strtotime($invoice['created_at'])) : '-' ?></td>
                              <td> 
                                 <a href="view_listbill_statement.php?list_bill=<?=$invoice['secured']?>" class="text-action"><strong><?=$invoice['list_bill_no']?></strong></a>
                                 
                              </td>
                              <td><?= $group_name ?></td>
                              <td class="text-center"><?= !empty($invoice['group_company_name']) ? $invoice['group_company_name']: $group_name ?></td>
                              <td class="text-center"><?= !empty($invoice['class_name']) ? $invoice['class_name']: '-' ?></td>
                              <td>
                                 <?php if($invoice['status'] == 'open'){?>
                                    <span class=""><?=ucwords(str_replace('_',' ',$invoice['status']));?></span>
                                 <?php }elseif($invoice['status'] == 'paid'){ ?>
                                    <span class="text-success"><?=ucwords(str_replace('_',' ',$invoice['status']));?></span>

                                 <?php }elseif($invoice['status'] == 'Past Due'){ ?>
                                    <span class="error"><?=ucwords(str_replace('_',' ',$invoice['status']));?></span>
                                 <?php }elseif(in_array($invoice['status'],array('write_off','void'))){ ?>
                                    <span class="text-danger"><?=ucwords(str_replace('_',' ',$invoice['status']));?></span>
                                 <?php }elseif(in_array($invoice['status'],array('Cancelled'))){ ?>
                                    <span class="error"><?=ucwords(str_replace('_',' ',$invoice['status']));?></span>
                                 <?php }else{ ?>
                                    <span class=""><?=ucwords(str_replace('_',' ',$invoice['status']));?></span>
                                 <?php } ?>
                              </td>
                              
                              
                              <?php if($invoice['grand_total'] >= $invoice['received_amount']){ ?>
                                 <td class="text-right"><?=displayAmount2($invoice['due_amount'],2);?></td>
                              <?php } else { ?>
                                 <td class="text-right text-danger">(-) <?=displayAmount($invoice['received_amount'] - $invoice['grand_total'],2);?></td>
                              <?php }  ?>

                              <td class="text-action text-center"><strong> 
                                 <?php if($invoice['amendment'] <= 0 ){ ?>
                                    <?= displayAmount(abs($invoice['amendment']),2)?>
                                 <?php }else { ?>
                                    (<?= displayAmount(abs($invoice['amendment']),2)?>)
                                 <?php } ?>
                                 
                                 </strong>
                              </td>
                              <td class="icons">
                                 <a href="view_listbill_statement.php?list_bill=<?=$invoice['secured']?>" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a>
                                 <a href="<?=$HOST?>/view_listbill_statement.php?list_bill=<?= $invoice['secured']?>&action_type=export_excel" data-toggle="tooltip" title="download csv" target="_BLANK"><i class="fa fa-download"></i></a>
                                 <a href="<?=$HOST?>/view_listbill_statement.php?list_bill=<?= $invoice['secured']?>&action_type=pdf" data-toggle="tooltip" title="download pdf" target="_BLANK"><i class="fa fa-download"></i></a>
                              </td>
                           </tr>
                        <?php } ?>
                     <?php }else{ ?>
                        <tr><td colspan="9" class="text-center">No Record(s) Found.</td></tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div class="panel panel-default panel-block">
         <div class="panel-body">
            <div class="clearfix m-b-15 tbl_filter">
               <div class="pull-left">
                  <h4 class="mn">Payments</h4>
               </div>
            </div>
            <div class="table-responsive">
               <table class="<?=$table_class?>">
                  <thead>
                     <tr>
                        <th>List Bill #</th>
                        <th>Payment Date</th>
                        <th>Transaction #</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th width="90px">Actions</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if(!empty($lb_order_res)){?>
                        <?php foreach ($lb_order_res as $key => $invoice) { ?>
                           <tr>
                              <td> 
                                 <a href="view_listbill_statement.php?list_bill=<?=md5($invoice['list_bill_id'])?>" class="text-action"><strong><?=$invoice['list_bill_no']?></strong></a>
                                 
                              </td>
                              <td><?= displayDate($invoice['created_at']) ?></td>
                              <td class="text-action"><strong><?= $invoice['transaction_id'] ?></strong></td>
                              <td>
                                 <?php
                                    if($invoice['payment_mode'] == "CC") {
                                       echo $invoice['card_type']." *".$invoice['last_cc_ach_no'];

                                    } else if($invoice['payment_mode'] == "ACH") {
                                       echo "ACH *".$invoice['last_cc_ach_no'];

                                    } else if($invoice['payment_mode'] == "Check") {
                                       echo "Check";
                                    }
                                 ?>
                              </td>
                              <td class="text-action"><strong>(<?= displayAmount($invoice['grand_total'],2)?>)</strong></td>
                              <td class="icons">
                                 <a href="payment_receipt.php?order_id=<?=md5($invoice['id'])?>" target="_blank" class="btn btn-action">Receipt</a>
                              </td>
                           </tr>
                        <?php } ?>
                     <?php }else{ ?>
                        <tr><td colspan="6" class="text-center">No Record(s) Found.</td></tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function(){
   
      $('.member_product_popover').popover({ 
         html : true,
         container: 'body',
         trigger: 'click',
         template: '<div class="popover"><div class="arrow"></div><div class="popover-content"></div></div>',
         placement: 'auto top',
         content: function() {
           return $('#popover_content_wrapper').html();
         }
      });
      $(".group_manage_payment_popup").colorbox({iframe:true, width:"768px",height:"350px"});
      $(".pay_bill").colorbox({iframe:true, width:"800px",height:"500px"});
      load_payment_data();
   });

   function load_payment_data() {
      var group_id = "<?=$group_id?>";
      var company_id = $("#company_id").val();
      $("#ajax_loader").show();
      $.ajax({
          url: 'ajax_payment_section_data.php?group_id='+group_id+"&company_id="+company_id,
          data: null,
          method: 'POST',
          dataType: 'json',
          success: function (res) {
              $("#ajax_loader").hide();
              $(".prev_balance").html(res.prev_balance);
              $(".prev_received_amount").html(res.prev_received_amount);
              $(".prev_payment_date").html(res.prev_payment_date);
              $(".balance_forward").html(res.balance_forward);

              $(".grand_total").html(res.grand_total);
              $(".amendment").html(res.amendment);
              $(".current_amount").html(res.current_amount);
              $(".due_date").html(res.due_date);

              $(".auto_draft_payment_method").html(res.auto_draft_payment_method);
              $(".auto_draft_date").html(res.auto_draft_date);
              $("a.pay_bill").attr('href',"<?=$HOST?>/pay_bill.php?location=group&list_bill_id="+res.list_bill_id);
              if(res.is_auto_draft_set == "Y") {
                  $(".auto_draft_section").show();
                  $(".pay_bill_section").hide();
              } else {
                  $(".auto_draft_section").hide();

                  if(res.has_open_list_bill == "Y") {
                     if(res.current_amount_org > 0) {
                        $(".pay_bill_section").show();
                     } else {
                        $(".pay_bill_section").hide();
                     }
                  } else {
                     $(".pay_bill_section").hide();
                  }
              }
          }
      });
   }
</script>