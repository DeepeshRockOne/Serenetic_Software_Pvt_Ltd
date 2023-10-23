<div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="mn">Reprocess Order - <span class="fw300"><?=$orderRow['display_id']?></span></h4>
   </div>
   <form action="" id="reprocess_frm" name="reprocess_frm">
      <input type="hidden" name="order_id" value="<?=$orderRow['id']?>">
      <input type="hidden" name="customer_id" value="<?=$orderRow['customer_id']?>">
      <input type="hidden" name="location" id="location" value="<?=$location?>">
      <input type="hidden" name="location_from" id="location_from" value="<?=$location_from?>">
      <div class="panel-body">
         <h4 class="fs16 mn"><?=$orderRow['fname'].' '.$orderRow['lname']?></h4>
         <p class="m-b-20"><a href="javascript:void(0);" class="red-link pn fw500"><?=$orderRow['rep_id']?></a></p>
         <div id="single_coverage">
            <div class="table-responsive">
               <table class="<?=$table_class?> table-small m-b-20">
                  <!-- <caption class="bg_dark_blue text-white p-l-10">
                  P4: 04/01/2019 - 04/30/2019
                  </caption> -->
                  <thead >
                     <tr class="bg_light_primary">
                        <th >Product</th>
                        <th class="text-center" width="25%">Coverage Period</th>
                        <!-- <th class="bg_light_blue text-center">Cost</th> -->
                        <th class="text-center">Sale Type</th>
                        <th class="text-right">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php if(!empty($od_res)){
                     $is_renewal = $orderRow['is_renewal'];
                     $service_fee = 0;
                     $grand_total = 0;
                     $service_fee_id = 0;
                     $service_fee_app_id = 0;
                     
                     foreach($od_res as $key =>$val){
                        $grand_total += $val['unit_price'];
                        if($val['product_type'] == 'ServiceFee' && $val['type'] == 'Fees' ){
                           $service_fee += $val['unit_price'];
                           $service_fee_id = $val['product_id'];
                           // $service_fee_app_id = $val['fee_applied_for_product'];
                           continue;
                        }

                     ?>
                     <tr>
                        <td><?=$val['name']?></td>
                        <td class="text-center"><?= getCustomDate($val['start_coverage_period']).' - '.getCustomDate($val['end_coverage_period'])?></td>
                        <!-- <td class="text-center"><?=displayAmount($val['unit_price'])?></td> -->
                        <td class="text-center">
                           <label class=""><?=$is_renewal  == 'Y' ? 'Renewal' : 'New Business'?></label>
                        </td>
                        <td class="text-right"><?=displayAmount($val['unit_price'])?></td>
                     </tr>
                  <?php } } ?>
                  </tbody>
               </table>
               <table class="table table-small m-b-20 br-a">
                  <tbody>
                     <tr>
                        <td class="bg_light_gray">
                           <?php if($service_fee > 0) { ?>
                           <div class="m-t-5">
                              <!-- <input type="checkbox" class="js-switch mn selected_product" data-price="<?=$service_fee?>" id="service_fee_coverage" data-id="<?=$service_fee_id?>" name="selected_product[<?=$service_fee_id?>]" data-fee_applied_for="<?=$service_fee_app_id?>" value="<?=$service_fee_id?>"> -->
                              <label>Service Fee (<?=displayAmount($service_fee)?>)</label>
                              
                           </div>
                           <input type="hidden" name="product_type[<?=$service_fee_id?>]" value="Fees">
                           <?php } ?>
                        </td>
                        <td class="text-right bg_light_gray"><span class="fw700 m-t-5">Total :</span> &nbsp;&nbsp; <label id="grand_total" class="m-t-5" data-price="0"><?=displayAmount($grand_total)?></label>
                        <input type="hidden" name="grand_total_main" id="grand_total_main" value="<?=$grand_total?>">
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="row theme-form">
               <div class="col-sm-6">
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <div class="pr">
                           <input id="post_payment_date" type="text" class="form-control" name="post_payment_date">
                           <label>Payment Date (MM/DD/YYYY)</label>
                        </div>
                     </div>
                     <p class="error error_post_payment_date"></p>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-group" id="payment_method_div">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="panel-footer text-center">
         <a href="javascript:void(0);" class="btn btn-action" id="reprocess_order_btn">Reprocess</a>
         <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
      </div>
   </form>
</div>
<script type="text/javascript">
$(document).ready(function(e){
   ajax_get_payment_method();
   $("#post_payment_date").datepicker({
      startDate: '<?=$from_date?>',
      endDate: '<?=$to_date?>',
      orientation: "bottom",
      changeDay: true,
      changeMonth: true,
      changeYear: true,
      autoclose:true,
   });
});

$(document).off('change click','#payment_method');
$(document).on('change click','#payment_method',function(e){
   if($(this).val() === 'add_new_payment_method'){
      $.colorbox({
         href :'add_billing_profile.php?location=<?=$location;?>&id=<?=$customer_id?>&action=Add' ,
         iframe: true,
         width: '768px', 
         height: '675px',
         onClosed : function(e){
            ajax_get_payment_method();
         }
      });
   }
});

$(document).off('click','#reprocess_order_btn');
$(document).on('click','#reprocess_order_btn',function() {
   $.ajax({
      url:"ajax_reprocess_order.php",
      data: $("#reprocess_frm").serialize(),
      type: 'POST',
      dataType: 'JSON',
      beforeSend :function(){
         $("#reprocess_frm .error").html('');
         $('#ajax_loader').show();
      },
      success: function(res) {
      $('#ajax_loader').hide();
      var location_from = $("#location_from").val();
      if (res.status == 'success') {
         parent.$.colorbox.close();
         parent.setNotifySuccess("Order Reprocess Successfully!",true);
         if(location_from === undefined || location_from === ''){
            parent.window.location.reload();
         }else{
            parent.get_orders_history();
            parent.get_transactions_history();
         }
      } else if (res.status == 'fail') {
         $.each(res.errors, function(key, value) {
            $('.error_' + key).parent("p.error").show();
            $('.error_' + key).html(value).show();
         });
      }else if(res.status === 'order_not_found'){
         parent.$.colorbox.close();
         parent.setNotifyError("Order status outdated!",true);
         if(location_from === undefined || location_from === ''){
            parent.window.location.reload();
         }else{
            parent.get_orders_history();
            parent.get_transactions_history();
         }
      }else if(res.status == 'payment_fail'){
         parent.$.colorbox.close();
         if(res.msg !== undefined && res.msg!==''){
            parent.setNotifyError(res.msg,true);
         }else{
            parent.setNotifyError("Error in processing payment",true);
         }

         if(location_from === undefined || location_from === ''){
            parent.window.location.reload();
         }else{
            parent.get_orders_history();
            parent.get_transactions_history();
         }
         
      }
      return false;
      }
   });
});

function ajax_get_payment_method(){
   $.ajax({
      url : "regenerate_order.php",
      data : {get_payent_method:1,customer_id:'<?=$customer_id?>'},
      type:"POST",
      dataType : "json",
      beforeSend :function(){
         $("#ajax_loader").show();
         $("#payment_method_div").html("");
      },
      success : function(res){
         $("#ajax_loader").hide();
         $("#payment_method_div").html(res.bill_desc);
         common_select();
      }
   });
}
</script>